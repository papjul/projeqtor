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
 * Save the current object : call corresponding method in SqlElement Class
 * The new values are fetched in $_REQUEST
 * The old values are fetched in $currentObject of $_SESSION
 * Only changed values are saved. 
 * This way, 2 users updating the same object don't mess.
 */

require_once "../tool/projeqtor.php";

// Get the object class from request
if (! array_key_exists('objectClass',$_REQUEST)) {
  throwError('objectClass parameter not found in REQUEST');
}
$className=$_REQUEST['objectClass'];
Security::checkValidClass($className);

if (! array_key_exists('selection',$_REQUEST)) {
  throwError('selection parameter not found in REQUEST');
}
$selection=trim($_REQUEST['selection']);
$selectList=explode(';',$selection);

if (!$selection or count($selectList)==0) {
	 $summary='<div class=\'messageWARNING\' >'.i18n('messageNoData',array(i18n($className))).'</div >';
	 echo '<input type="hidden" id="summaryResult" value="'.$summary.'" />';
	 exit;
}

$description="";
if (array_key_exists('description',$_REQUEST)) {
	$description=trim($_REQUEST['description']);
}
$idActivity="";
if (array_key_exists('idActivity',$_REQUEST)) {
  $idActivity=trim($_REQUEST['idActivity']);
}
$idStatus="";
if (array_key_exists('idStatus',$_REQUEST)) {
  $idStatus=trim($_REQUEST['idStatus']);
}
$idResource="";
if (array_key_exists('idResource',$_REQUEST)) {
  $idResource=trim($_REQUEST['idResource']);
}
$idUser="";
if (array_key_exists('idUser',$_REQUEST)) {
	$idUser=trim($_REQUEST['idUser']);
}
$idContact="";
if (array_key_exists('idContact',$_REQUEST)) {
	$idContact=trim($_REQUEST['idContact']);
}
$result="";
if (array_key_exists('result',$_REQUEST)) {
  $result=trim($_REQUEST['result']);
}
$note="";
if (array_key_exists('note',$_REQUEST)) {
  $note=trim($_REQUEST['note']);
}
$idProject="";
if (array_key_exists('idProject',$_REQUEST)) {
  $idProject=trim($_REQUEST['idProject']);
}
$idTargetVersion="";
if (array_key_exists('idTargetVersion',$_REQUEST)) {
  $idTargetVersion=trim($_REQUEST['idTargetVersion']);
}
$idTargetProductVersion="";
if (array_key_exists('idTargetProductVersion',$_REQUEST)) {
  $idTargetProductVersion=trim($_REQUEST['idTargetProductVersion']);
}

$initialDueDate="";
if (array_key_exists('initialDueDate',$_REQUEST)) {
	$initialDueDate=trim($_REQUEST['initialDueDate']);
}
$actualDueDate="";
if (array_key_exists('actualDueDate',$_REQUEST)) {
	$actualDueDate=trim($_REQUEST['actualDueDate']);
}
$initialEndDate="";
if (array_key_exists('initialEndDate',$_REQUEST)) {
	$initialEndDate=trim($_REQUEST['initialEndDate']);
}
$actualEndDate="";
if (array_key_exists('actualEndDate',$_REQUEST)) {
	$actualEndDate=trim($_REQUEST['actualEndDate']);
}
$initialDueTime="";
if (array_key_exists('initialDueTime',$_REQUEST)) {
	$initialDueTime=trim($_REQUEST['initialDueTime']);
}
$actualDueTime="";
if (array_key_exists('actualDueTime',$_REQUEST)) {
	$actualDueTime=trim($_REQUEST['actualDueTime']);
}
$pe=$className.'PlanningElement';
$pe_validatedStartDate="";
if (array_key_exists($pe.'_validatedStartDate',$_REQUEST)) {
	$pe_validatedStartDate=trim($_REQUEST[$pe.'_validatedStartDate']);
}
$pe_validatedEndDate="";
if (array_key_exists($pe.'_validatedEndDate',$_REQUEST)) {
	$pe_validatedEndDate=trim($_REQUEST[$pe.'_validatedEndDate']);
}
$pm='id'.$className.'PlanningMode';
$pe_pm="";
if (array_key_exists($pe.'_'.$pm,$_REQUEST)) {
	$pe_pm=trim($_REQUEST[$pe.'_'.$pm]);
}
$type="";
if (array_key_exists('idType',$_REQUEST)) {
	$type=trim($_REQUEST['idType']);
}
$fixPlanning="";
if (array_key_exists('fixPlanning',$_REQUEST)) {
  $fixPlanning=trim($_REQUEST['fixPlanning']);
}
$isUnderConstruction="";
if (array_key_exists('isUnderConstruction',$_REQUEST)) {
  $isUnderConstruction=trim($_REQUEST['isUnderConstruction']);
}
$pe_priority="";
if (array_key_exists($pe.'_priority',$_REQUEST)) {
  $pe_priority=trim($_REQUEST[$pe.'_priority']);
}

//unset($_SESSION['currentObject']); // Clear last accessed item : otherwise history will get wrong
SqlElement::unsetCurrentObject();

$cptOk=0;
$cptError=0;
$cptWarning=0;
$cptNoChange=0;
echo "<table>";
foreach ($selectList as $id) {
	if (!trim($id)) { continue;}
	Sql::beginTransaction();
	echo '<tr>';
	echo '<td valign="top"><b>#'.$id.'&nbsp:&nbsp;</b></td>';
	$item=new $className($id);
	if (property_exists($item, 'locked') and $item->locked) {
		Sql::rollbackTransaction();
    $cptWarning++;
    echo '<td><span class="messageWARNING" >' . i18n($className) . " #" . htmlEncode($item->id) . ' '.i18n('colLocked'). '</span></td>';
		continue;
	}
	$typeField='id'.$className.'Type';
	if ($type and property_exists($item,$typeField)) {
		$item->$typeField=$type;
	}
	if ($description and property_exists($item,'description')) {
		$item->description.=(($item->description)?"\n":"").$description;
	}
	if ($idActivity and property_exists($item,'idActivity')) {
	  $item->idActivity=$idActivity;
	}
  if ($idStatus and property_exists($item,'idStatus')) {
  	//$oldStatus=new Status($item->idStatus);
    $item->idStatus=$idStatus;
    $item->recalculateCheckboxes(true);
  }
  if ($idResource and property_exists($item,'idResource')) {
    $item->idResource=$idResource;
  }
  if ($idUser and property_exists($item,'idUser')) {
  	$item->idUser=$idUser;
  }
  if ($idContact and property_exists($item,'idContact')) {
  	$item->idContact=$idContact;
  }
  if ($result and property_exists($item,'result')) {
    $item->result.=(($item->result)?"\n":"").$result;
  }
  if ($idProject and property_exists($item,'idProject')) {
    $item->idProject=$idProject;
  }
  if ($idTargetVersion and property_exists($item,'idTargetVersion')) {
    $item->idTargetVersion=$idTargetVersion;
  } 
  if ($idTargetProductVersion and property_exists($item,'idTargetProductVersion')) {
    $item->idTargetProductVersion=$idTargetProductVersion;
  }
  if ($initialDueDate and property_exists($item,'initialDueDate')) {
  	$item->initialDueDate=$initialDueDate;
  }
  if ($actualDueDate and property_exists($item,'actualDueDate')) {
  	$item->actualDueDate=$actualDueDate;
  }
  if ($initialEndDate and property_exists($item,'initialEndDate')) {
  	$item->initialEndDate=$initialEndDate;
  }
  if ($actualEndDate and property_exists($item,'actualEndDate')) {
  	$item->actualEndDate=$actualEndDate;
  }
  if ($initialDueDate and $initialDueTime and property_exists($item,'initialDueDateTime')) {
  	$item->initialDueDateTime=$initialDueDate.' '.substr($initialDueTime,1);
  }
  if ($actualDueDate and $actualDueTime and property_exists($item,'actualDueDateTime')) {
  	$item->actualDueDateTime=$actualDueDate.' '.substr($actualDueTime,1);
  }
  if ($fixPlanning and $fixPlanning!="" and property_exists($item,'fixPlanning')) {
    $item->fixPlanning=($fixPlanning=='ON')?1:0;
  }
  if ($isUnderConstruction and $isUnderConstruction!="" and property_exists($item,'isUnderConstruction')) {
    $item->isUnderConstruction=($isUnderConstruction=='ON')?1:0;
  }
  if (property_exists($item,$pe) and is_object($item->$pe)) {
  	if ($pe_validatedStartDate and property_exists($item->$pe,'validatedStartDate')) {
  	  $item->$pe->validatedStartDate=$pe_validatedStartDate;
  	}
  	if ($pe_validatedEndDate and property_exists($item->$pe,'validatedEndDate')) {
  		$item->$pe->validatedEndDate=$pe_validatedEndDate;
  	}
  	if ($pe_pm and property_exists($item->$pe,$pm)) {
  		$item->$pe->$pm=$pe_pm;
  	}
    if ($pe_priority and property_exists($item->$pe,'priority')) {
  		$item->$pe->priority=$pe_priority;
  	} 
  }
  $resultSave=$item->save();
  if ($note and property_exists($item,'_Note')) {
    $noteObj=new Note();
    $noteObj->refType=$className;
    $noteObj->refId=$id;
    $noteObj->creationDate=date('Y-m-d H:i:s');
    $noteObj->note=$note;
    $noteObj->idPrivacy=1;
    $res=new Resource(getSessionUser()->id);
    $noteObj->idTeam=$res->idTeam;
    $resultSaveNote=$noteObj->save();
    if (! stripos($resultSave,'id="lastOperationStatus" value="OK"')>0) {
    	$resultSave=$resultSaveNote;
    }   
  }
	$resultSave=str_replace('<br/><br/>','<br/>',$resultSave);
	$statusSave = getLastOperationStatus ( $resultSave );
	if ($statusSave=="ERROR" ) {
	  Sql::rollbackTransaction();
	  $cptError++;
	} else if ($statusSave=="OK") {
	  Sql::commitTransaction();
	  $cptOk++;
	} else if ($statusSave=="NO_CHANGE") {
	  Sql::commitTransaction();
	  $cptNoChange++;
	} else { 
	  Sql::rollbackTransaction();
	  $cptWarning++;
  }
  echo '<td><div style="padding: 0px 5px;" class="message'.$statusSave.'" >' . $resultSave . '</div></td>';
  echo '</tr>';
}
echo "</table>";
$summary="";
if ($cptError) {
  $summary.='<div class=\'messageERROR\' >' . $cptError." ".i18n('resultError') . '</div>';
}
if ($cptOk) {
  $summary.='<div class=\'messageOK\' >' . $cptOk." ".i18n('resultOk') . '</div>';
}
if ($cptWarning) {
  $summary.='<div class=\'messageWARNING\' >' . $cptWarning." ".i18n('resultWarning') . '</div>';
}
if ($cptNoChange) {
  $summary.='<div class=\'messageNO_CHANGE\' >' . $cptNoChange." ".i18n('resultNoChange') . '</div>';
}
echo '<input type="hidden" id="summaryResult" value="'.$summary.'" />';
?>