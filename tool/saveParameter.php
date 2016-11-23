<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
 * Contributors : 
 *  => g.miraillet : Fix #1502
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

// TODO (SECURITY) : enforce security (habilitation to change parameters, lock fixed params, ...)
$status="NO_CHANGE";
$errors="";
$type=$_REQUEST['parameterType'];
Sql::beginTransaction();
$forceRefreshMenu='';
if ($type=='habilitation') {
  $crosTable=htmlGetCrossTable('menu', 'profile', 'habilitation') ;
  $hab=new Habilitation();
  $allHab=$hab->getSqlElementsFromCriteria(array());
  foreach ($allHab as $hab) {
    $allHab[$hab->idMenu.'#'.$hab->idProfile]=$hab;
    unset($allHab[$hab->id]);
  }
  $forceRefreshMenu=false;
  foreach($crosTable as $lineId => $line) {
    foreach($line as $colId => $val) {
      //$crit['idMenu']=$lineId;
      //$crit['idProfile']=$colId;
      //$obj=SqlElement::getSingleSqlElementFromCriteria('Habilitation', $crit);
      $key=$lineId.'#'.$colId;
      if (isset($allHab[$key])) {
        $obj=$allHab[$key];
      } else {
        $obj=new Habilitation();
        $obj->idMenu=$lineId;
        $obj->idProfile=$colId;
      }
      $newVal=($val)?1:0;
      if ($obj->allowAccess!=$newVal) {
        $obj->allowAccess=$newVal;
        $result=$obj->save();
        $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
        $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
        if ($isSaveNO_CHANGE===false) {
          if ($isSaveOK===false) {
            $status="ERROR";
            $errors=$result;
          } else if ($status=="NO_CHANGE") {
            $status="OK";
            if ($obj->idProfile==getSessionUser()->idProfile) {
              $forceRefreshMenu='habilitation';
            }
          }
        }
      }
    }
    resetUser();
  }
  Habilitation::correctUpdates(); // Call correct updates 3 times, to assure all level updates
  Habilitation::correctUpdates();
  Habilitation::correctUpdates();
} else if ($type=='habilitationReport') {
  $crosTable=htmlGetCrossTable('report', 'profile', 'habilitationReport') ;
  foreach($crosTable as $lineId => $line) {
    foreach($line as $colId => $val) {
      $crit['idReport']=$lineId;
      $crit['idProfile']=$colId;
      $obj=SqlElement::getSingleSqlElementFromCriteria('HabilitationReport', $crit);
      $obj->allowAccess=($val)?1:0;
      $result=$obj->save();
      $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
      $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
      if ($isSaveNO_CHANGE===false) {
        if ($isSaveOK===false) {
          $status="ERROR";
          $errors=$result;
        } else if ($status=="NO_CHANGE") {
          $status="OK";
          //if ($obj->idProfile==getSessionUser()->idProfile) {
          //  $forceRefreshMenu='habilitationReport';
          //}
        }
      }
    }
  }
} else if ($type=='habilitationOther') {
  $crosTable=htmlGetCrossTable(array('imputation'=>i18n('imputationAccess'),
                                     'workValid'=>i18n('workValidate'),
  		                               'diary'=>i18n('diaryAccess'),
                                     //'expense'=>i18n('resourceExpenseAccess'),
                                     'work'=>i18n('workAccess'),
                                     'cost'=>i18n('costAccess'),
  		                               'assignmentView'=>i18n('assignmentViewRight'),
  		                               'assignmentEdit'=>i18n('assignmentEditRight'),
                                     'combo'=>i18n('comboDetailAccess'),
  		                               'checklist'=>i18n('checklistAccess'),
                                     'planning'=>i18n('planningRight'),
  																	 'resourcePlanning'=>i18n('resourcePlanningRight'),
                                     'document'=>i18n('documentUnlockRight'),
                                     'requirement'=>i18n('requirementUnlockRight'),
                                     'reportResourceAll'=>i18n('reportResourceAll'),
                                     'canForceDelete'=>i18n('canForceDelete'),
                                     'canUpdateCreation'=>i18n('canUpdateCreationInfo'), 
                                     'viewComponents'=>i18n('viewComponents'),
                                     'resVisibilityList'=>i18n('resourceVisibilityList'),
                                     'resVisibilityScreen'=>i18n('resourceVisibilityScreen')),
                               'profile', 
                               'habilitationOther') ;
  foreach($crosTable as $lineId => $line) {
    foreach($line as $colId => $val) {
      $crit['scope']=$lineId;
      $crit['idProfile']=$colId;
      $obj=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
      $obj->rightAccess=($val)?$val:0;
      $result=$obj->save();
      $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
      $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
      if ($isSaveNO_CHANGE===false) {
        if ($isSaveOK===false) {
          $status="ERROR";
          $errors=$result;
        } else if ($status=="NO_CHANGE") {
          $status="OK";
          //if ($obj->idProfile==getSessionUser()->idProfile) {
          //  $forceRefreshMenu='habilitationOther';
          //}
        }
      }
    }
  }
} else if ($type=='accessRight') {
  $crosTable=htmlGetCrossTable('menuProject', 'profile', 'accessRight') ;
  foreach($crosTable as $lineId => $line) {
    foreach($line as $colId => $val) {
      $crit['idMenu']=$lineId;
      $crit['idProfile']=$colId;
      $obj=SqlElement::getSingleSqlElementFromCriteria('AccessRight', $crit);
      $obj->idAccessProfile=$val;
      $result=$obj->save();
      $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
      $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
      if ($isSaveNO_CHANGE===false) {
        if ($isSaveOK===false) {
          $status="ERROR";
          $errors=$result;
        } else if ($status=="NO_CHANGE") {
          $status="OK";
          //if ($obj->idProfile==getSessionUser()->idProfile) {
          //  $forceRefreshMenu='accessRight';
          //}
        }
      }
    }
    resetUser();
  }
} else if ($type=='accessRightNoProject') {
  $tableCrossRef=array('menuReadWriteEnvironment','menuReadWriteList','menuReadWriteType');
  foreach ($tableCrossRef as $crossRef) {
    $crosTable=htmlGetCrossTable($crossRef, 'profile', 'accessRight') ;
    foreach($crosTable as $lineId => $line) {
      foreach($line as $colId => $val) {
        $crit['idMenu']=$lineId;
        $crit['idProfile']=$colId;
        $obj=SqlElement::getSingleSqlElementFromCriteria('AccessRight', $crit);
        $obj->idAccessProfile=$val;
        $result=$obj->save();
        $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
        $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
        if ($isSaveNO_CHANGE===false) {
          if ($isSaveOK===false) {
            $status="ERROR";
            $errors=$result;
          } else if ($status=="NO_CHANGE") {
            $status="OK";
            //if ($obj->idProfile==getSessionUser()->idProfile) {
            //  $forceRefreshMenu='accessRightNoProject';
            //}
          }
        }
      }
    }
    resetUser();
  }
} else if ($type=='userParameter') {
  $parameterList=Parameter::getParamtersList($type);
  foreach($_REQUEST as $fld => $val) {
    if (array_key_exists($fld, $parameterList)) {
      $user=getSessionUser();
      $crit['idUser']=$user->id;
      $crit['idProject']=null;
      $crit['parameterCode']=$fld;
      $obj=SqlElement::getSingleSqlElementFromCriteria('Parameter', $crit);
      $obj->parameterValue=$val;
      $result=$obj->save();
      $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
      $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
      if ($isSaveNO_CHANGE===false) {
        if ($isSaveOK===false) {
          $status="ERROR";
          $errors=$result;
        } else if ($status=="NO_CHANGE") {
          $status="OK";
        }
      }
    }
  }
} else if ($type=='globalParameter') {
  $parameterList=Parameter::getParamtersList($type);
  $changeImputationAlerts=false;
  foreach($_REQUEST as $fld => $val) { // TODO (SECURITY) : forbit writting of db and prefix params
    if (array_key_exists($fld, $parameterList)) {
      $crit['parameterCode']=$fld;
      $crit['idUser']=null;
      $crit['idProject']=null;
      $obj=SqlElement::getSingleSqlElementFromCriteria('Parameter', $crit);
      if ($parameterList[$fld]=='time') {
        $val=substr($val,1,5);
      }
      $val=str_replace('#comma#',',',$val);
      if ($fld=='imputationAlertGenerationDay'  or $fld=='imputationAlertGenerationHour'
       or $fld=='imputationAlertControlDay'     or $fld=='imputationAlertControlNumberOfDays'
       or $fld=='imputationAlertSendToResource' or $fld=='imputationAlertSendToProjectLeader'
       or $fld=='imputationAlertSendToTeamManager') {
        $$fld=$val;
        if ($obj->parameterValue!=$val) {
          $changeImputationAlerts=true;
        }
      }
      $obj->parameterValue=$val;
      $obj->idUser=null;
      $obj->idProject=null;
      $result=$obj->save();
      $paramCode='globalParameter_'.$fld;
      $_SESSION[$paramCode]=$val;
      $isSaveOK=strpos($result, 'id="lastOperationStatus" value="OK"');
      $isSaveNO_CHANGE=strpos($result, 'id="lastOperationStatus" value="NO_CHANGE"');
      if ($isSaveNO_CHANGE===false) {
        if ($isSaveOK===false) {
          $status="ERROR";
          $errors=$result;
        } else if ($status=="NO_CHANGE") {
          $status="OK";
        }
      }
    } else if  ($fld=='imputationAlertGenerationDay'  or $fld=='imputationAlertGenerationHour'
       or $fld=='imputationAlertControlDay'     or $fld=='imputationAlertControlNumberOfDays'
       or $fld=='imputationAlertSendToResource' or $fld=='imputationAlertSendToProjectLeader'
       or $fld=='imputationAlertSendToTeamManager') {
        $$fld=$val;
        $changeImputationAlerts=true;
    }
  }
  if ($changeImputationAlerts) {
    $cronExec=SqlElement::getSingleSqlElementFromCriteria('CronExecution',array('fonctionName'=>'generateImputationAlert'));
    if (isset($imputationAlertControlDay) and $imputationAlertControlDay=='NEVER'
    or (    isset($imputationAlertSendToResource) and $imputationAlertSendToResource=='NO' 
        and isset($imputationAlertSendToProjectLeader) and $imputationAlertSendToProjectLeader=='NO'
        and isset($imputationAlertSendToTeamManager) and $imputationAlertSendToTeamManager=='NO')) {
      if ($cronExec->id) {
        $cronExec->delete();
      } else {
        // No cron, nothing to do
      }
    } else {
      $hours=substr($imputationAlertGenerationHour,0,2);
      $minutes=substr($imputationAlertGenerationHour,3,2);;
      $dayOfMonth='*';
      $month='*';
      $dayOfWeek=$imputationAlertGenerationDay;
      $cronStr=$minutes.' '.$hours.' '.$dayOfMonth.' '.$month.' '.$dayOfWeek;
      $cronExec->cron=$cronStr;
      $cronExec->fileExecuted='../tool/generateImputationAlert.php';
      $cronExec->idle=false;
      $cronExec->fonctionName='generateImputationAlert';
      $cronExec->nextTime=null;
      $cronExec->save();
    }
    //Cron::restart();
    $errors=i18n("cronRestartRequired");
    $status='WARNING';
  }
  Parameter::clearGlobalParameters();// force refresh 
} else {
   $errors="Save not implemented";
   $status='WARNING';
}
if ($status=='ERROR') {
	Sql::rollbackTransaction();
  echo '<div class="messageERROR" >' . $errors . '</div>';
} else if ($status=='WARNING'){ 
	Sql::commitTransaction();
  echo '<div class="messageWARNING" >' . i18n('messageParametersSaved') . ' - ' .$errors .'</div>';
  $status='INVALID';
} else if ($status=='OK'){ 
	Sql::commitTransaction();
  echo '<div class="messageOK" >' . i18n('messageParametersSaved') . '</div>';
} else {
	Sql::rollbackTransaction();
  echo '<div class="messageNO_CHANGE" >' . i18n('messageParametersNoChangeSaved') . '</div>';
}
echo '<input type="hidden" id="forceRefreshMenu" value="'.$forceRefreshMenu.'" />';
echo '<input type="hidden" id="lastOperation" name="lastOperation" value="save">';
echo '<input type="hidden" id="lastOperationStatus" name="lastOperationStatus" value="' . $status .'">';

function resetUser() {
	$user=getSessionUser();
  $user->reset();
	setSessionUser($user);
}
?>