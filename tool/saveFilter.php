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

/** ===========================================================================
 * Save a filter : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 */

require_once "../tool/projeqtor.php";

$user=getSessionUser();

$comboDetail=false;
if (array_key_exists('comboDetail',$_REQUEST)) {
  $comboDetail=true;
}

if (! $comboDetail and ! $user->_arrayFilters) {
  $user->_arrayFilters=array();
} else if ($comboDetail and ! $user->_arrayFiltersDetail) {
  $user->_arrayFiltersDetail=array();
}

// Get the filter info
if (! array_key_exists('filterObjectClass',$_REQUEST)) {
  throwError('filterObjectClass parameter not found in REQUEST');
}
$filterObjectClass=$_REQUEST['filterObjectClass'];

// Get existing filter info
if (!$comboDetail and array_key_exists($filterObjectClass,$user->_arrayFilters)) {
  $filterArray=$user->_arrayFilters[$filterObjectClass];
} else if ($comboDetail and array_key_exists($filterObjectClass,$user->_arrayFiltersDetail)) {
  $filterArray=$user->_arrayFiltersDetail[$filterObjectClass];
} else {
  $filterArray=array();
}

$name="";
if (array_key_exists('filterName',$_REQUEST)) {
  $name=$_REQUEST['filterName'];
}
Sql::beginTransaction();
trim($name);
if (! $name) {
  echo htmlGetErrorMessage((i18n("messageMandatory", array(i18n("filterName")))));
  return;
} else {
  $crit=array("refType"=>$filterObjectClass, "name"=>$name, "idUser"=>$user->id);
  $filter=SqlElement::getSingleSqlElementFromCriteria("Filter", $crit);
  if (! $filter->id) {
    $filter->refType=$filterObjectClass;
    $filter->name=$name;
    $filter->idUser=$user->id;
    $filter->isShared=0;
  }
  $filter->save();
  $criteria=new FilterCriteria();
  $criteria->purge("idFilter='" . $filter->id . "'");
  foreach ($filterArray as $filterCriteria) {
    $criteria=new FilterCriteria();
    $criteria->idFilter=$filter->id;
    $criteria->dispAttribute=$filterCriteria["disp"]["attribute"];
    $criteria->dispOperator=$filterCriteria["disp"]["operator"];
    $criteria->dispValue=$filterCriteria["disp"]["value"];
    $criteria->sqlAttribute=$filterCriteria["sql"]["attribute"];
    $criteria->sqlOperator=$filterCriteria["sql"]["operator"];
    $criteria->sqlValue=$filterCriteria["sql"]["value"];
    if ($criteria->sqlValue==null) {
    	if ($criteria->sqlOperator=='is null' or $criteria->sqlOperator=='is not null') {
    		$criteria->sqlValue=null;
    	} else {
    	  $criteria->sqlValue='0';
    	}
    }
    $criteria->save();
  }
}
echo '<table width="100%"><tr><td align="center" >';
echo '<span class="messageOK" style="z-index:999;position:relative;top:7px" >' . i18n('colFilter') . " '" . htmlEncode($name) . "' " . i18n('resultUpdated') . ' (#'.htmlEncode($filter->id).')</span>';
echo '</td></tr></table>';

$flt=new Filter();
$crit=array('idUser'=> $user->id, 'refType'=>$filterObjectClass );
$filterList=$flt->getSqlElementsFromCriteria($crit, false);
htmlDisplayStoredFilter($filterList,$filterObjectClass);
Sql::commitTransaction();
?>