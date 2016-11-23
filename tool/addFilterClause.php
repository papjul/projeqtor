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
 * Save a note : call corresponding method in SqlElement Class
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
if (! array_key_exists('idFilterAttribute',$_REQUEST)) {
  throwError('idFilterAttribute parameter not found in REQUEST');
}
$idFilterAttribute=$_REQUEST['idFilterAttribute'];
$idFilterAttribute=preg_replace('/[^a-zA-Z0-9_]/','', $idFilterAttribute); // Note: may need to be more permissive.

if (! array_key_exists('idFilterOperator',$_REQUEST)) {
  throwError('idFilterOperator parameter not found in REQUEST');
}
$idFilterOperator=$_REQUEST['idFilterOperator'];
// TODO (SECURITY) : test completness of test
if (preg_match('/^(([<>]|<>)?=|(NOT )?LIKE|hasSome|(NOT )?IN|is(Not)?Empty|<>|SORT|[<>]=now\+)$/', $idFilterOperator) != true) {
	traceHack("bad value for idFilterOperator ($idFilterOperator)");
	exit;
}

if (! array_key_exists('filterDataType',$_REQUEST)){
  throwError('filterDataType parameter not found in REQUEST');
}
$filterDataType=$_REQUEST['filterDataType'];
// TODO (SECURITY) : test completness of test
if (preg_match('/^(list|decimal|int|date|bool|refObject|varchar)$/', $filterDataType) != true){
	traceHack("bad value for filterDataType ($filterDataType)");
	exit;
}


if (! array_key_exists('filterValue',$_REQUEST)) {
  throwError('filterValue parameter not found in REQUEST');
}
$filterValue=$_REQUEST['filterValue']; // Note: value is checked before use depending on context.

if (array_key_exists('filterValueList',$_REQUEST)) {
  $filterValueList=$_REQUEST['filterValueList']; // key => value pairs  - are escaped before use.
} else {
  $filterValueList=array();
}

if (! array_key_exists('filterValueDate',$_REQUEST)) {
  throwError('filterValueDate parameter not found in REQUEST');
}
$filterValueDate=$_REQUEST['filterValueDate'];
Security::checkValidDateTime($filterValueDate);

if (! array_key_exists('filterValueCheckbox',$_REQUEST)) {
  $filterValueCheckbox=false;
} else {
  $filterValueCheckbox=true;
}

if (! array_key_exists('filterSortValueList',$_REQUEST)) {
  throwError('filterSortValueList parameter not found in REQUEST');
}
$filterSortValue=$_REQUEST['filterSortValueList']; 
Security::checkValidAlphanumeric($filterSortValue);

if (! array_key_exists('filterObjectClass',$_REQUEST)) {
  throwError('filterObjectClass parameter not found in REQUEST');
}
$filterObjectClass=$_REQUEST['filterObjectClass'];
Security::checkValidClass($filterObjectClass);

$name="";
if (array_key_exists('filterName',$_REQUEST)) {
  $name=$_REQUEST['filterName'];
}
trim($name); // Note: filtered before use using htmlEncode()

// Get existing filter info
if (!$comboDetail and array_key_exists($filterObjectClass,$user->_arrayFilters)) {
  $filterArray=$user->_arrayFilters[$filterObjectClass];
} else if ($comboDetail and array_key_exists($filterObjectClass,$user->_arrayFiltersDetail)) {
  $filterArray=$user->_arrayFiltersDetail[$filterObjectClass];
} else {
  $filterArray=array();
}

$obj=new $filterObjectClass();
// Add new filter
if ($idFilterAttribute and $idFilterOperator) {
  $arrayDisp=array();
  $arraySql=array();
  $dataType=$obj->getDataType($idFilterAttribute);
  $dataLength=$obj->getDataLength($idFilterAttribute);
  $split=explode('_',$idFilterAttribute);
  if (count($split)>1 ) {
  	$externalClass=$split[0];
    $externalObj=new $externalClass();
    $arrayDisp["attribute"]=$externalObj->getColCaption($split[1]);
  } else {
  	//echo  $idFilterAttribute . "=>" . $obj->getColCaption($idFilterAttribute);
    if (substr($idFilterAttribute,0,9)=='idContext') {
      $arrayDisp["attribute"]=SqlList::getNameFromId('ContextType',substr($idFilterAttribute,9));
    } else {
      $arrayDisp["attribute"]=$obj->getColCaption($idFilterAttribute);
    }
  }
  $arraySql["attribute"]=$obj->getDatabaseColumnName($idFilterAttribute);
  if ($idFilterOperator=="=" or $idFilterOperator==">=" or $idFilterOperator=="<="  or $idFilterOperator=="<>") {
    $arrayDisp["operator"]=$idFilterOperator;
    $arraySql["operator"]=$idFilterOperator;
    if ($filterDataType=='date') {
      $arrayDisp["value"]="'" . htmlFormatDate($filterValueDate) . "'";
      $arraySql["value"]="'" . $filterValueDate . "'";
    } else if ($filterDataType=='bool') {
        $arrayDisp["value"]=($filterValueCheckbox)?i18n("displayYes"):i18n("displayNo");
        $arraySql["value"]=($filterValueCheckbox)?1:0;
    } else {
      $arrayDisp["value"]="'" . htmlEncode($filterValue) . "'";
      $arraySql["value"]="'" . trim(Sql::str(htmlEncode($filterValue)),"'") . "'";
    }
  } else if ($idFilterOperator=="LIKE" or $idFilterOperator=="hasSome") {
  	if ($filterDataType=='refObject' or $idFilterOperator=="hasSome") {
  		$arraySql["operator"]=' exists ';
  		if ($idFilterOperator=="hasSome") { 
  			$filterValue="";
  			$arrayDisp["value"]="";
  			$arrayDisp["operator"]=i18n("isNotEmpty");
  		} else {
  			$arrayDisp["operator"]=i18n("contains");
  			$arrayDisp["value"]="'" . trim(Sql::str(htmlEncode($filterValue)),"'") . "'";
  		}
		  Security::checkValidClass($idFilterAttribute);
  		$refObj=new $idFilterAttribute();
  		$refObjTable=$refObj->getDatabaseTableName();
  		$table=$obj->getDatabaseTableName();
  		$arraySql["value"]=" ( select 'x' from $refObjTable "
  		. " where $refObjTable.refType=".Sql::str($filterObjectClass)." "
  		. " and $refObjTable.refId=$table.id "
  		. " and $refObjTable.note ".((Sql::isMysql())?'LIKE':'ILIKE')." '%" . trim(Sql::str(htmlEncode($filterValue)),"'") . "%' ) ";
  	} else {
      $arrayDisp["operator"]=i18n("contains");
      $arraySql["operator"]=(Sql::isMysql())?'LIKE':'ILIKE';
      $arrayDisp["value"]="'" . htmlEncode($filterValue) . "'";
      $arraySql["value"]="'%" . trim(Sql::str(htmlEncode($filterValue)),"'") . "%'";
  	}
  } else if ($idFilterOperator=="NOT LIKE") {
    $arrayDisp["operator"]=i18n("notContains");
    $arraySql["operator"]=(Sql::isMysql())?'NOT LIKE':'NOT ILIKE';
    $arrayDisp["value"]="'" . htmlEncode($filterValue) . "'";
    $arraySql["value"]="'%" . trim(Sql::str(htmlEncode($filterValue)),"'") . "%'";
  } else if ($idFilterOperator=="IN" or $idFilterOperator=="NOT IN") {
    $arrayDisp["operator"]=($idFilterOperator=="IN")?i18n("amongst"):i18n("notAmongst");
    $arraySql["operator"]=$idFilterOperator;
    $arrayDisp["value"]="";
    $arraySql["value"]="(";
    foreach ($filterValueList as $key=>$val) {
      $arrayDisp["value"].=($key==0)?"":", ";
      $arraySql["value"].=($key==0)?"":", ";
      $arrayDisp["value"].="'" . Sql::fmtStr(SqlList::getNameFromId(Sql::fmtStr(substr($idFilterAttribute,2)),$val)) . "'";
      $arraySql["value"].=Security::checkValidId($val);
    }
    //$arrayDisp["value"].=")";
    $arraySql["value"].=")";
  } else if ($idFilterOperator=="isEmpty") {
      $arrayDisp["operator"]=i18n("isEmpty");
      $arraySql["operator"]="is null";
      $arrayDisp["value"]="";
      $arraySql["value"]="";
  } else if ($idFilterOperator=="isNotEmpty") {
      $arrayDisp["operator"]=i18n("isNotEmpty");
      $arraySql["operator"]="is not null";
      $arrayDisp["value"]="";
      $arraySql["value"]="";
  } else if ($idFilterOperator=="SORT") {  
    $arrayDisp["operator"]=i18n("sortFilter");
    $arraySql["operator"]=$idFilterOperator;
    Security::checkValidAlphanumeric($filterSortValue);
    $arrayDisp["value"]=htmlEncode(i18n('sort' . ucfirst($filterSortValue) ));
    $arraySql["value"]=$filterSortValue;
  } else if ($idFilterOperator=="<=now+") {  
    $arrayDisp["operator"]="<= " . i18n('today') . (($filterValue>0)?' +':' ');
    $arraySql["operator"]="<=";
    $arrayDisp["value"]=htmlEncode(intval($filterValue)) . ' ' . i18n('days');
    if (preg_match('/[^\-0-9]/', $filterValue) == true) {
      $filterValue="";
    }
    if (Sql::isPgsql()) {
      $arraySql["value"]= "NOW() + INTERVAL '" . intval($filterValue) . " day'";
    } else {
      $arraySql["value"]= "ADDDATE(NOW(), INTERVAL (" . intval($filterValue) . ") DAY)";
    }
  } else if ($idFilterOperator==">=now+") {  
    $arrayDisp["operator"]=">= " . i18n('today') . (($filterValue>0)?' +':' ');
    $arraySql["operator"]=">=";
    $arrayDisp["value"]=htmlEncode(intval($filterValue)) . ' ' . i18n('days');
    if (preg_match('/[^\-0-9]/', $filterValue) == true) {
      $filterValue="";
    }
    if (Sql::isPgsql()) {
      $arraySql["value"]= "NOW() + INTERVAL '" . intval($filterValue) . " day'";
    } else {
      $arraySql["value"]= "ADDDATE(NOW(), INTERVAL (" . intval($filterValue) . ") DAY)";
    }
  } else {
     echo htmlGetErrorMessage(i18n('incorrectOperator'));
     exit;
  } 
  $filterArray[]=array("disp"=>$arrayDisp,"sql"=>$arraySql);
  if ($idFilterAttribute=='idle' and $filterValueCheckbox) {
    $arrayDisp["attribute"]=i18n('labelShowIdle');
    $arrayDisp["operator"]="";
    $arrayDisp["value"]="";
    $arraySql["attribute"]='idle';
    $arraySql["operator"]='>=';
    $arraySql["value"]='0';
    $filterArray[]=array("disp"=>$arrayDisp,"sql"=>$arraySql);
  }
  if (! $comboDetail) {
    $user->_arrayFilters[$filterObjectClass]=$filterArray;
  } else {
  	$user->_arrayFiltersDetail[$filterObjectClass]=$filterArray;
  }
}

//$user->_arrayFilters[$filterObjectClass . "FilterName"]=$name;
if (! $comboDetail) {
  $user->_arrayFilters[$filterObjectClass . "FilterName"]="";
} else {
  $user->_arrayFiltersDetail[$filterObjectClass . "FilterName"]="";	
}
htmlDisplayFilterCriteria($filterArray,$name); 

// save user (for filter saving)
setSessionUser($user);


?>