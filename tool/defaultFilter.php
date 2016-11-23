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
 * Save filter from User to Session to be able to restore it
 * Retores it if cancel is set
 * Cleans it if clean is set
 * The new values are fetched in $_REQUEST
 */

require_once "../tool/projeqtor.php";

$user=getSessionUser();

if (! array_key_exists('filterObjectClass',$_REQUEST)) {
  throwError('filterObjectClass parameter not found in REQUEST');
}
$filterObjectClass=$_REQUEST['filterObjectClass'];
$name="";
if (array_key_exists('filterName',$_REQUEST)) {
  $name=$_REQUEST['filterName'];
}

Sql::beginTransaction();
echo '<table width="100%"><tr><td align="center">';
$crit=array();
$crit['idUser']=$user->id;
$crit['idProject']=null;
$crit['parameterCode']="Filter" . $filterObjectClass;
$param=SqlElement::getSingleSqlElementFromCriteria('Parameter',$crit);
if ($name) {
  $critFilter=array("refType"=>$filterObjectClass, "name"=>$name, "idUser"=>$user->id);
  $filter=SqlElement::getSingleSqlElementFromCriteria("Filter", $critFilter);
  if (! $filter->id) {
    echo '<span class="messageERROR" style="z-index:999;position:relative;top:7px">' . i18n('defaultFilterError', array($name)) . '</span>';
  } else {
    $param->parameterValue=$filter->id;
    $param->save();
    echo '<span class="messageOK" style="z-index:999;position:relative;top:7px">' . i18n('defaultFilterSet', array($name)) . '</span>';
  }
} else {
  $param->delete();
  echo '<span class="messageOK" style="z-index:999;position:relative;top:7px">' . i18n('defaultFilterCleared') . '</span>';
}
echo '</td></tr></table>';
Sql::commitTransaction();
$flt=new Filter();
$crit=array('idUser'=> $user->id, 'refType'=>$filterObjectClass );
$filterList=$flt->getSqlElementsFromCriteria($crit, false);
htmlDisplayStoredFilter($filterList,$filterObjectClass);
?>