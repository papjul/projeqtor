<?php 
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
 * 
 * Most of properties are extracted from Dojo Framework.
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
//echo "work.php";

$paramProject='';
if (array_key_exists('idProject',$_REQUEST)) {
  $paramProject=trim($_REQUEST['idProject']);
  Security::checkValidId($paramProject);
}
$paramTeam='';
if (array_key_exists('idTeam',$_REQUEST)) {
  $paramTeam=trim($_REQUEST['idTeam']);
  Security::checkValidId($paramTeam);
}
$paramYear='';
if (array_key_exists('yearSpinner',$_REQUEST)) {
	$paramYear=$_REQUEST['yearSpinner'];
	$paramYear=Security::checkValidYear($paramYear);
};
$paramMonth='';
if (array_key_exists('monthSpinner',$_REQUEST)) {
	$paramMonth=$_REQUEST['monthSpinner'];
  $paramMonth=Security::checkValidMonth($paramMonth);
};
$paramWeek='';
if (array_key_exists('weekSpinner',$_REQUEST)) {
	$paramWeek=$_REQUEST['weekSpinner'];
	$paramWeek=Security::checkValidWeek($paramWeek);
};

$user=getSessionUser();

$paramResource='';
if (array_key_exists('idResource',$_REQUEST)) {
  $paramResource=trim($_REQUEST['idResource']);
  $paramResource = preg_replace('/[^0-9]/', '', $paramResource); // only allow digits
  $canChangeResource=false;
  $crit=array('idProfile'=>$user->idProfile, 'scope'=>'reportResourceAll');
  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
  if ($habil and $habil->id and $habil->rightAccess=='1') {
    $canChangeResource=true;
  }
  if (!$canChangeResource and $paramResource!=$user->id) {
    echo i18n('messageNoAccess',array(i18n('colReport')));
    exit;
  } 
}

$periodType=$_REQUEST['periodType']; // not filtering as data as data is only compared against fixed strings
$periodValue='';
if (array_key_exists('periodValue',$_REQUEST))
{
	$periodValue=$_REQUEST['periodValue'];
	$periodValue=Security::checkValidPeriod($periodValue);
}

// Header
$headerParameters="";
if ($paramProject!="") {
  $headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project', $paramProject)) . '<br/>';
}
if ($paramTeam!="") {
  $headerParameters.= i18n("colIdTeam") . ' : ' . htmlEncode(SqlList::getNameFromId('Team', $paramTeam)) . '<br/>';
}
if ($periodType=='year' or $periodType=='month' or $periodType=='week') {
  $headerParameters.= i18n("year") . ' : ' . $paramYear . '<br/>';
  
}
if ($periodType=='month') {
  $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
}
if ( $periodType=='week') {
  $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
}
if ( $paramResource=='') {
  $headerParameters.= i18n("colIdResource") . ' : ' . htmlEncode(SqlList::getNameFromId('Resource',$paramResource)) . '<br/>';
}
include "header.php";

$where=getAccesRestrictionClause('Activity',false,false,true,true);
//$where="1=1 ";
$where.=($periodType=='week')?" and week='" . $periodValue . "'":'';
$where.=($periodType=='month')?" and month='" . $periodValue . "'":'';
$where.=($periodType=='year')?" and year='" . $periodValue . "'":'';
if ($paramProject!='') {
  $where.=  "and idProject in " . getVisibleProjectsList(true, $paramProject) ;
}
$where.=($paramResource!='')?" and idResource='" . $paramResource . "'":'';
$order="";
//echo $where;
$work=new Work();
$lstWork=$work->getSqlElementsFromCriteria(null,false, $where, $order);
$result=array();
$projects=array();
$resources=array();
$sumProj=array();
foreach ($lstWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Resource', $work->idResource);
  }
  if (! array_key_exists($work->idProject,$projects)) {
    $projects[$work->idProject]=SqlList::getNameFromId('Project', $work->idProject);
  }
  if (! array_key_exists($work->idResource,$result)) {
    $result[$work->idResource]=array();
  }
  if (! array_key_exists($work->idProject,$result[$work->idResource])) {
    $result[$work->idResource][$work->idProject]=0;
  } 
  $result[$work->idResource][$work->idProject]+=$work->work;

}

if (checkNoData($result)) exit;
// title
$colWidth=round(80/count($projects));
echo '<table style="width:95%;" align="center">';
echo '<tr>';
echo '<td style="width:10%" class="reportTableHeader" rowspan="2">' . i18n('Resource') . '</td>';
echo '<td style="width:80%" colspan="' . count($projects) . '" class="reportTableHeader">' . i18n('Project') . '</td>';
echo '<td style="width:10%" class="reportTableHeader" rowspan="2">' . i18n('sum') . '</td>';
echo '</tr><tr>';
$newProject=array();
foreach ($projects as $id=>$name) {
  $newProject[SqlList::getFieldFromId('Project', $id, 'sortOrder').'-'.$id]=$name;
}
$projects=$newProject;
ksort($projects);
foreach ($projects as $id=>$name) {
  $idExplo=explode('-',$id);
  $id=$idExplo[1];
  echo '<td style="width:'.$colWidth.'%" class="reportTableColumnHeader">' . htmlEncode($name) . '</td>';
  $sumProj[$id]=0;  
}

echo '</tr>';

$sum=0;
asort($resources);
foreach ($resources as $idR=>$nameR) {
	if ($paramTeam) {
		$res=new Resource($idR);
	}
  if (!$paramTeam or $res->idTeam==$paramTeam) {
		$sumRes=0;
	  echo '<tr><td style="width:10%" class="reportTableLineHeader">' . htmlEncode($nameR) . '</td>';
	  foreach ($projects as $idP=>$nameP) {
      $idExplo=explode('-',$idP);
      $idP=$idExplo[1];
	    echo '<td style="width:' . $colWidth . '%" class="reportTableData">';
	    if (array_key_exists($idR, $result)) {
	      if (array_key_exists($idP, $result[$idR])) {
	        $val=$result[$idR][$idP];
	        echo Work::displayWorkWithUnit($val);
	        $sumProj[$idP]+=$val; 
	        $sumRes+=$val; 
	        $sum+=$val;
	      } 
	    }
	    echo '</td>';
	  }
	  echo '<td style="width:10%" class="reportTableColumnHeader">' . Work::displayWorkWithUnit($sumRes) . '</td>';
	  echo '</tr>';
  }
}
echo '<tr><td class="reportTableHeader">' . i18n('sum') . '</td>';
foreach ($projects as $id=>$name) {
  $idExplo=explode('-',$id);
  $id=$idExplo[1];
  echo '<td class="reportTableColumnHeader">' . Work::displayWorkWithUnit($sumProj[$id]) . '</td>';
}
echo '<td class="reportTableHeader">' . Work::displayWorkWithUnit($sum) . '</td></tr>';
echo '</table>';