<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : -
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
//echo "workDetail.php";

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
include "header.php";

$where=getAccesRestrictionClause('Activity',false,false,true,true);
//$where="1=1 ";
$where.=($periodType=='week')?" and week='" . $periodValue . "'":'';
$where.=($periodType=='month')?" and month='" . $periodValue . "'":'';
$where.=($periodType=='year')?" and year='" . $periodValue . "'":'';
if ($paramProject!='') {
  $where.=  "and idProject in " . getVisibleProjectsList(true, $paramProject) ;
}
$order="";
//echo $where;
$work=new Work();
$lstWork=$work->getSqlElementsFromCriteria(null,false, $where, $order);
$result=array();
$activities=array();
$project=array();
$description=array();
$parent=array();
$resources=array();
$sumActi=array();
foreach ($lstWork as $work) {
  if (! array_key_exists($work->idResource,$resources)) {
    $resources[$work->idResource]=SqlList::getNameFromId('Resource', $work->idResource);
  }
  $refType=$work->refType;
  $refId=$work->refId;
  $key=$refType . "#" . $refId;
  if (! array_key_exists($key,$activities)) {
  	if ($refType) {
      $obj=new $refType($refId);
  	} else {
  		$obj=new Ticket();
  	}
    $key=SqlList::getFieldFromId('Project', $obj->idProject, 'sortOrder').'-'.$refType . "#" . $refId;
    $activities[$key]=$obj->name;
    $description[$key]=$obj->description;
    if ($refType=='Project') {
      $parent[$key]="[" . i18n('Project') . "]";
    } else {
      if (property_exists($obj,'idActivity') and $obj->idActivity) {
        $parent[$key]=SqlList::getNameFromId('Activity', $obj->idActivity);
      } else {
        $parent[$key]="";
      }
    }
    $project[$key]=SqlList::getNameFromId('Project', $obj->idProject);
  }
  if (! array_key_exists($work->idResource,$result)) {
    $result[$work->idResource]=array();
  }
  if (! array_key_exists($key,$result[$work->idResource])) {
    $result[$work->idResource][$key]=0;
  } 
  $result[$work->idResource][$key]+=$work->work;
}
ksort($activities);
if (checkNoData($result)) exit;

// title
echo '<table style="width:95%" align="center">';
echo '<tr>';
echo '<td class="reportTableHeader" rowspan="2" style="width:20%">' . i18n('Resource') . '</td>';
echo '<td class="reportTableHeader" rowspan="2" style="width:10%">' . i18n('colWork') . '</td>';
echo '<td class="reportTableHeader" colspan="3">' . i18n('Activity') . '</td>';
echo '</tr><tr>';
echo '<td class="reportTableColumnHeader" style="width:20%">' . i18n('colIdProject') . '</td>';
echo '<td class="reportTableColumnHeader" style="width:25%">' . i18n('colName') . '</td>';
//echo '<td class="reportTableColumnHeader" style="width:25%">' . i18n('colDescription') . '</td>';
echo '<td class="reportTableColumnHeader" style="width:25%">' . i18n('colParentActivity') . '</td>';
echo '</tr>';

$sum=0;
asort($resources);
foreach ($resources as $idR=>$nameR) {
	if ($paramTeam) {
    $res=new Resource($idR);
  }
  if (!$paramTeam or $res->idTeam==$paramTeam) {
	  $sumRes=0;
	  echo '<tr>';
	  echo '<td class="reportTableLineHeader" style="width:20%" rowspan="' . (count($result[$idR]) +1) . '">' . htmlEncode($nameR) . '</td>';
	  foreach ($activities as $key=>$nameA) {
	    if (array_key_exists($idR, $result)) {
	      if (array_key_exists($key, $result[$idR])) {
	        $val=$result[$idR][$key];
	        $sumRes+=$val; 
	        $sum+=$val;
	        echo '<td class="reportTableData" style="width:10%">' . Work::displayWorkWithUnit($val). '</td>';
	        echo '<td class="reportTableData" style="width:20%; text-align:left;">' . htmlEncode($project[$key]) . '</td>';
	        echo '<td class="reportTableData" style="width:25%; text-align:left;">' . htmlEncode($nameA) . '</td>'; 
	//        echo '<td class="reportTableData" style="width:25%; text-align:left;">' . htmlEncode($description[$key]) . '</td>'; 
	        echo '<td class="reportTableData" style="width:25%; text-align:left;" >' . htmlEncode($parent[$key]) . '</td>'; 
	        echo '</tr><tr>';
	      } 
	    }
	  }
    echo '<td class="reportTableColumnHeader">' . Work::displayWorkWithUnit($sumRes) . '</td>';
    echo '<td class="reportTableColumnHeader" style="text-align:left;" colspan="3">' . i18n('sum') . " " . $nameR . '</td>';
    echo '</tr>';
  }
}
echo '<tr>';
echo '<td class="reportTableHeader">' . i18n('sum') . '</td>';
echo '<td class="reportTableHeader">' . Work::displayWorkWithUnit($sum) . '</td>';
echo '<td colspan="3"></td>';
echo '</tr>';
echo '</table>';