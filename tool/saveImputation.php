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

/** ============================================================================
 * Save some information to session (remotely).
 */

require_once "../tool/projeqtor.php";

$status="NO_CHANGE";
$errors="";
$finalResult="";

$rangeType=$_REQUEST['rangeType'];
$rangeValue=$_REQUEST['rangeValue'];
$userId=$_REQUEST['userId'];
$nbLines=$_REQUEST['nbLines'];
if ($rangeType=='week') {
  $nbDays=7;
}

if (isset($_REQUEST['imputationComment'])) {
	$comment=$_REQUEST['imputationComment'];
	$period=new WorkPeriod();
  $crit=array('idResource'=>$userId, 'periodRange'=>$rangeType,'periodValue'=>$rangeValue);
  $period=SqlElement::getSingleSqlElementFromCriteria('WorkPeriod', $crit);
  $period->comment=$comment;
  $result=$period->save();
  if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
    $status='ERROR';
    $finalResult=$result;
  } else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
    $status='OK';
  } else { 
    if ($finalResult=="") {
      $finalResult=$result;
    }
  }
}

ini_set('max_input_vars', 50*$nbLines+20);
ini_set('suhosin.post.max_vars', 50*$nbLines+20);
ini_set('suhosin.request.max_vars', 50*$nbLines+20);
Sql::beginTransaction();
for ($i=0; $i<$nbLines; $i++) {
  $imputable=$_REQUEST['imputable'][$i];
  $locked=$_REQUEST['locked'][$i];
  if ($imputable and ! $locked) {
    $line=new ImputationLine();
    $line->idAssignment=$_REQUEST['idAssignment'][$i];
    $ass=new Assignment($line->idAssignment);
    $line->refType=$ass->refType;
    $line->refId=$ass->refId;
    $line->idResource=$userId;
    if (isset($_REQUEST['leftWork'][$i])) {
      $line->leftWork=Work::convertImputation($_REQUEST['leftWork'][$i]);
    } else {
    	traceLog('WARNING - Left work not retrieved from screen');
    	traceLog('        - Maybe max_input_vars is too small in php.ini');
    	traceLog('        - Assignment #'.$ass->id.' on '.$ass->refType.' #'.$ass->refId.' for resource #'.$ass->idResource. ' - '.SqlList::getNameFromId('Resource',$ass->idResource));
    	trigger_error('Error - Maybe max_input_vars is too small in php.ini',E_USER_ERROR);
    }
    $line->imputable=$imputable;
    $arrayWork=array();
    for ($j=1; $j<=$nbDays; $j++) {
    	$workId=null;
    	if (array_key_exists('workId_' . $j, $_REQUEST)) {
        $workId=$_REQUEST['workId_' . $j][$i];
    	}
      $workValue=Work::convertImputation($_REQUEST['workValue_'.$j][$i]);
      $workDate=$_REQUEST['day_' . $j];
      if ($workId) {
        $work=new Work($workId);
      } else {
        $crit=array('idAssignment'=>$line->idAssignment,
                    'workDate'=>$workDate, 'idWorkElement'=>null);
        $work=SqlElement::getSingleSqlElementFromCriteria('Work', $crit);
      } 
      $arrayWork[$j]=$work;
      $arrayWork[$j]->work=$workValue;
      $arrayWork[$j]->idResource=$userId;
      $arrayWork[$j]->idProject=$ass->idProject;
      $arrayWork[$j]->refType=$line->refType;
      $arrayWork[$j]->refId=$line->refId;
      $arrayWork[$j]->idAssignment=$line->idAssignment;     
      $arrayWork[$j]->setDates($workDate);
    }
    $line->arrayWork=$arrayWork;
    $result=$line->save();
    if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ) {
      $status='ERROR';
      $finalResult=$result;
      break;
    } else if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
      $status='OK';
    } else { 
      if ($finalResult=="") {
        $finalResult=$result;
      }
    }
    $ass->leftWork=$line->leftWork;
    $resultAss=$ass->saveWithRefresh();
    if (stripos($resultAss,'id="lastOperationStatus" value="OK"')>0 ) {
      $status='OK';
    } else if (stripos($result,'id="lastOperationStatus" value="ERROR"')>0 ){
      $status='ERROR';
      $finalResult=$resultAss;
      break;
    }
  }
}

if ($status=='ERROR') {
	Sql::rollbackTransaction();
  echo '<div class="messageERROR" >' . $finalResult . '</div>';
} else if ($status=='OK'){ 
	Sql::commitTransaction();
  echo '<div class="messageOK" >' . i18n('messageImputationSaved') . '</div>';
  checkSendAlert($userId,$rangeValue);
} else {
	Sql::rollbackTransaction();
  echo '<div class="messageNO_CHANGE" >' . i18n('messageNoImputationChange') . '</div>';
}
echo '<input type="hidden" id="lastOperation" name="lastOperation" value="save">';
echo '<input type="hidden" id="lastOperationStatus" name="lastOperationStatus" value="' . $status .'">';

function checkSendAlert($userId,$periodValue) {
  $currentUser=getSessionUser();
  if ($userId==$currentUser->id) return;
  $param=Parameter::getGlobalParameter('imputationAlertInputByOther');
  if (!trim($param) or $param=='NO') return;
  $name=($currentUser->resourceName)?$currentUser->resourceName:$currentUser->name;
  $name='"'.$name.'"';
  $periodValue=substr($periodValue,0,4).'-'.substr($periodValue,4);
  $alertSendTitle=i18n('messageAlertImputationByOtherTitle');
  $alertSendMessage=i18n('messageAlertImputationByOtherBody',array($name,$periodValue));
  if ($param=='ALERT' or $param=='ALERT&MAIL') {
    $alertSendType='WARNING';
    $alert=new Alert();
    $alert->idUser=$userId;
    $alert->alertType=$alertSendType;
    $alert->alertInitialDateTime=date('Y-m-d H:i:s');
    $alert->alertDateTime=date('Y-m-d H:i:s');
    $alert->title=mb_substr($alertSendTitle,0,100);
    $alert->message=$alertSendMessage;
    $result=$alert->save();
  }
  if ($param=='MAIL' or $param=='ALERT&MAIL') {
    $to=SqlList::getFieldFromId('User', $userId, 'email');
    if (trim($to)) {
      $result=sendMail($to, '['.Parameter::getGlobalParameter('paramDbDisplayName').'] '.$alertSendTitle, $alertSendMessage);
    }
  }
}
?>