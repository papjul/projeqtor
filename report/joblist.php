<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : Julien PAPASIAN
 * 
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under 
 * the terms of the GNU Affero General Public License as published by the Free 
 * Software Foundation, either version 3 of the License, or (at your option) 
 * any later version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS 
 * FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public License for 
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org 
 *     
 *** DO NOT REMOVE THIS NOTICE ************************************************/

include_once '../tool/projeqtor.php';
$print = false;
if (array_key_exists('print', $_REQUEST)) {
    $print = true;
    include_once('../tool/formatter.php');
}
$paramActivity = '';
$paramProject = '';
if (array_key_exists('idActivity', $_REQUEST)) {
    $paramActivity = trim($_REQUEST['idActivity']);
}
if (array_key_exists('idProject', $_REQUEST)) {
    $paramProject = trim($_REQUEST['idProject']);
}

// Header
$headerParameters = "";
if ($paramProject != '') {
    $headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project', $paramProject)) . '<br/>';
}
if ($paramActivity != "") {
    $headerParameters.= i18n("colIdActivity") . ' : ' . htmlEncode(SqlList::getNameFromId('Activity', $paramActivity)) . '<br/>';
}
if ($paramActivity == '' && $paramProject == '') {
    exit('Project or activity parameter is missing');
}

if (array_key_exists('outMode', $_REQUEST) && $_REQUEST['outMode'] == 'csv') {
    $outMode = 'csv';
} else {
    $outMode = 'html';
}

if ($outMode == 'csv') {
    include_once "headerFunctions.php";
} else {
    include "header.php";
}

// All activities
$where = getAccesRestrictionClause('Activity', false);
if ($paramActivity != '') {
    $where .= " and idActivity = " . $paramActivity;
} elseif ($paramProject != '') {
    $where .= " and idProject = " . $paramProject;
    $where .= " and idActivity IS NOT NULL";
}

$lstActivity = (new Activity())->getSqlElementsFromCriteria(null, false, $where, null);

if (checkNoData($lstActivity))
    exit;

// Joblist definition
$where = getAccesRestrictionClause('JoblistDefinition', false);
$where .= "and nameChecklistable = 'Activity' ";
$where .= "and idType = " . $lstActivity[0]->idActivityType;
$joblist = (new JoblistDefinition())->getSqlElementsFromCriteria(null, false, $where, null);

if (checkNoData($joblist))
    exit;

// Job definition
$where = getAccesRestrictionClause('JobDefinition', false);
$where .= "and idJoblistDefinition = " . $joblist[0]->id;
$orderBy = " sortOrder ASC";
$jobDefinitions = (new JobDefinition())->getSqlElementsFromCriteria(null, false, $where, $orderBy);

if (checkNoData($jobDefinitions))
    exit;

$aDefLines = array();
foreach ($jobDefinitions as $jobDef) {
    if (!array_key_exists($jobDef->id, $aDefLines)) {
        $aDefLines[$jobDef->id] = array('name' => $jobDef->name,
            'title' => $jobDef->title,
            'daysBeforeWarning' => $jobDef->daysBeforeWarning,
            'nbcheck' => 0);
    }
}

// Get list of checkboxes of activities
$where = getAccesRestrictionClause('Job', false);
$where .= " and refType = 'Activity' ";
$where .= " and idJoblistDefinition = " . $joblist[0]->id;
$lstJobs = (new Job())->getSqlElementsFromCriteria(null, false, $where, null);

$result = array(); // Preparation of result lines
foreach ($lstJobs as $oJob) {
    $result[$oJob->refId][$oJob->idJobDefinition] = $oJob;
}

// title
if ($outMode == 'csv') {
    echo chr(239) . chr(187) . chr(191); // Microsoft Excel requirement
    echo i18n('colIdActivity') . ';';
    foreach ($aDefLines as $aLine) {
        echo htmlencode($aLine['name']) . ';';
    }
    echo i18n('colTotal') . ';';
    echo i18n('colGeneralComment');
    echo "\n";
} else {
    echo '<table align="center" style="width: 95%">';
    echo '<tr>';
    echo '<td class="reportTableHeader">' . i18n('colIdActivity') . '</td>';
    foreach ($aDefLines as $aLine) {
        echo '<th class="reportTableHeader" style="width: 60px" title="' . htmlencode($aLine['title']) . '"><span>' . htmlencode($aLine['name']) . '</span></th>';
    }
    echo '<td class="reportTableHeader">' . i18n('colTotal') . '</td>';
    echo '<td class="reportTableHeader">' . i18n('colGeneralComment') . '</td>';
    echo '</tr>';
}

function compareWbs($a, $b) {
    return version_compare($a->ActivityPlanningElement->wbs, $b->ActivityPlanningElement->wbs);
}

usort($lstActivity, 'compareWbs');

$status = array('done' => '#a5eda5',
    'warning' => '#edb584',
    'alert' => '#eda5a5',
    'blank' => '#FFFFFF');
$totalChecked = 0;
$nbActivitiesDone = 0;
$dToday = new DateTime();
foreach ($lstActivity as $activity) {
    if ($outMode == 'csv') {
        echo $activity->name . ';';
    } else {
        echo '<tr style="height: 30px">';
        echo '<td class="reportTableLineHeader pointer" onclick="gotoElement(\'Activity\',' . $activity->id . ');">' . htmlencode($activity->name) . '</td>';
    }
    // If there are checked boxes
    if (array_key_exists($activity->id, $result)) {
        $iCount = 0;
        foreach ($aDefLines as $sKey => $aLine) {
            if ($outMode == 'csv') {
                if (isset($result[$activity->id][$sKey])) {
                    if ($result[$activity->id][$sKey]->value) {
                        echo 'X';
                        ++$iCount;
                        ++$aDefLines[$sKey]['nbcheck'];
                    } elseif (!is_null($result[$activity->id][$sKey]->creationDate)) {
                        echo htmlFormatDate(substr($result[$activity->id][$sKey]->creationDate, 0, 10));
                    }
                    echo ';';
                } else {
                    echo ';';
                }
            } else {
                $color = $status['blank'];
                $title = '';
                if (isset($result[$activity->id][$sKey])) {
                    if ($result[$activity->id][$sKey]->value) {
                        $color = $status['done'];
                        ++$iCount;
                        ++$aDefLines[$sKey]['nbcheck'];
                    } else {
                        if ($result[$activity->id][$sKey]->value) {
                            $color = $status['done'];
                        } elseif (!is_null($result[$activity->id][$sKey]->creationDate) && $result[$activity->id][$sKey]->creationDate < $dToday->format('Y-m-d')) {
                            $color = $status['alert'];
                        } elseif (!is_null($result[$activity->id][$sKey]->creationDate) && $aLine['daysBeforeWarning'] > 0) {
                            $warningDate = new DateTime($result[$activity->id][$sKey]->creationDate);
                            $warningDate->modify('-' . $aLine['daysBeforeWarning'] . ' days');
                            if ($warningDate < $dToday) {
                                $color = $status['warning'];
                            }
                        }
                    }
                    if (!is_null($result[$activity->id][$sKey]->idUser)) {
                        $title .= 'By ' . SqlList::getNameFromId('User', $result[$activity->id][$sKey]->idUser) . "\n";
                    }
                    if (isset($result[$activity->id][$sKey]) && $result[$activity->id][$sKey]->value && !is_null($result[$activity->id][$sKey]->checkTime)) {
                        $title .= 'On ' . htmlFormatDate($result[$activity->id][$sKey]->checkTime) . "\n";
                    }
                    if (!is_null($result[$activity->id][$sKey]->creationDate)) {
                        $title .= 'Due for ' . htmlFormatDate(substr($result[$activity->id][$sKey]->creationDate, 0, 10)) . "\n";
                    }
                    if (!is_null($result[$activity->id][$sKey]->comment) && !empty($result[$activity->id][$sKey]->comment)) {
                        $title .= "————————\n" . $result[$activity->id][$sKey]->comment;
                    }
                }

                echo '<td class="reportTableData" style="background-color: ' . $color . '" title="' . $title . '">' . ((isset($result[$activity->id][$sKey]) && !is_null($result[$activity->id][$sKey]->comment) && !empty($result[$activity->id][$sKey]->comment)) ? '<big>*</big>' : '&nbsp;') . '</td>';
            }
        }
        if ($outMode == 'csv') {
            echo round((($iCount / count($aDefLines)) * 100), 2) . ';';
        } else {
            echo '<td class="reportTableLineHeader" style="text-align: right">' . round((($iCount / count($aDefLines)) * 100), 2) . ' %</td>';
        }
        $totalChecked += $iCount;
        if ($iCount == count($aDefLines)) {
            ++$nbActivitiesDone;
        }
    } else {
        for ($i = 0; $i < count($aDefLines); ++$i) {
            if ($outMode == 'csv') {
                echo ';';
            } else {
                echo '<td class="reportTableData">&nbsp;</td>';
            }
        }
        if ($outMode == 'csv') {
            echo '0;';
        } else {
            echo '<td class="reportTableLineHeader" style="text-align: right">0 %</td>';
        }
    }
    if ($outMode == 'csv') {
        echo mb_strtoupper(str_replace("\n", " / ", htmlTransformRichtextToPlaintext($activity->description, 'UTF-8')));
        echo "\n";
    } else {
        echo '<td class="reportTableLineHeader">' . mb_strtoupper($activity->description, 'UTF-8') . '</td>';
        echo '</tr>';
    }
}

if ($outMode == 'csv') {
    echo i18n('colTotal') . ';';
    foreach ($aDefLines as $aLine) {
        echo round((($aLine['nbcheck'] / count($lstActivity)) * 100), 2) . ';';
    }
    echo round((($totalChecked / (count($aDefLines) * count($lstActivity))) * 100), 2) . ';';
    echo $nbActivitiesDone . ' / ' . count($lstActivity) . ' done';
} else {
    echo '<tr><td class="reportTableHeader">' . i18n('colTotal') . '</td>';
    foreach ($aDefLines as $aLine) {
        echo '<td class="reportTableHeader">' . round((($aLine['nbcheck'] / count($lstActivity)) * 100), 2) . ' %</td>';
    }
    echo '<td class="reportTableHeader">' . round((($totalChecked / (count($aDefLines) * count($lstActivity))) * 100), 2) . ' %</td>';
    echo '<td class="reportTableHeader">' . $nbActivitiesDone . ' / ' . count($lstActivity) . ' done</td></tr>';
    echo '</table>';
}
