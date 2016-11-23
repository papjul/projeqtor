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

/**
 * ============================================================================
 * Project is the main object of the project managmement.
 * Almost all other objects are linked to a given project.
 */
require_once ('_securityCheck.php');
class ImputationLine {
  
  // List of fields that will be exposed in general user interface
  // public $id; // redefine $id to specify its visible place
  public $refType;
  public $refId;
  public $idProject;
  public $idAssignment;
  public $name;
  public $comment;
  public $wbs;
  public $wbsSortable;
  public $topId;
  public $validatedWork;
  public $assignedWork;
  public $plannedWork;
  public $realWork;
  public $leftWork;
  public $imputable;
  public $elementary;
  public $arrayWork;
  public $arrayPlannedWork;
  public $startDate;
  public $endDate;
  public $idle;
  public $locked;
  public $description;
  public $functionName;

  /**
   * ==========================================================================
   * Constructor
   * 
   * @param $id the
   *          id of the object in the database (null if not stored yet)
   * @return void
   */
  function __construct($id=NULL, $withoutDependentObjects=false) {
    $arrayWork=array();
  }

  /**
   * ==========================================================================
   * Return some lines for imputation purpose, including assignment and work
   * 
   * @return void
   */
  function __destruct() {
  }

  static function getLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned=true, $hideDone=false, $hideNotHandled=false, $displayOnlyCurrentWeekMeetings=false) {
    SqlElement::$_cachedQuery ['Assignment']=array();
    SqlElement::$_cachedQuery ['PlanningElement']=array();
    SqlElement::$_cachedQuery ['WorkElement']=array();
    SqlElement::$_cachedQuery ['Activity']=array();
    SqlElement::$_cachedQuery ['Project']=array();
    
    // Insert new lines for admin projects
    Assignment::insertAdministrativeLines($resourceId);
    
    // Initialize parameters
    if (Parameter::getGlobalParameter('displayOnlyHandled') == "YES") {
      $hideNotHandled=1;
    }
    $user=getSessionUser();
    // $user=new User($user->id);
    
    $result=array();
    if ($rangeType == 'week') {
      $nbDays=7;
    }
    $startDate=self::getFirstDay($rangeType, $rangeValue);
    $plus=$nbDays - 1;
    $endDate=date('Y-m-d', strtotime("+$plus days", strtotime($startDate)));
    
    // Get All assignments
    $crit=array('idResource' => $resourceId);
    if (!$showIdle) {
      $crit ['idle']='0';
    }
    $ass=new Assignment();
    $assList=$ass->getSqlElementsFromCriteria($crit, false, null, null, true, true);
    
    // Retrieve realwork and planned work entered for period
    $crit=array('idResource' => $resourceId);
    $crit [$rangeType]=$rangeValue;
    $work=new Work();
    $workList=$work->getSqlElementsFromCriteria($crit, false, null, null, false, true);
    $plannedWork=new PlannedWork();
    if ($showPlanned) {
      $plannedWorkList=$plannedWork->getSqlElementsFromCriteria($crit, false, null, null, false, true);
    }
    
    // Get acces restriction to hide projects dependong on access rights
    $profile=$user->getProfile(); // Default profile for user
    $listAccesRightsForImputation=$user->getAllSpecificRightsForProfiles('imputation');
    $listAllowedProfiles=array(); // List will contain all profiles with visibility to Others imputation
    if (isset($listAccesRightsForImputation ['PRO'])) {
      $listAllowedProfiles+=$listAccesRightsForImputation ['PRO'];
    }
    if (isset($listAccesRightsForImputation ['ALL'])) {
      $listAllowedProfiles+=$listAccesRightsForImputation ['ALL'];
    }
    $visibleProjects=array();
    foreach ( $user->getSpecificAffectedProfiles() as $prj => $prf ) {
      if (in_array($prf, $listAllowedProfiles)) {
        $visibleProjects [$prj]=$prj;
      }
    }
    // ... and remove assignments not to be shown
    $accessRightRead=securityGetAccessRight('menuActivity', 'read');
    if ($user->id != $resourceId and $accessRightRead != 'ALL') {
      foreach ( $assList as $id => $ass ) {
        if (!array_key_exists($ass->idProject, $visibleProjects)) {
          unset($assList [$id]);
        }
      }
    }
    
    // Hide some lines depending on user criteria selected on page
    if ($hideNotHandled or $hideDone or $displayOnlyCurrentWeekMeetings) {
      foreach ( $assList as $id => $ass ) {
        if ($ass->refType and SqlElement::class_exists($ass->refType))
          $refObj=new $ass->refType($ass->refId, true);
        if ($hideNotHandled and property_exists($refObj, 'handled') and !$refObj->handled) {
          unset($assList [$id]);
        }
        if ($hideDone and property_exists($refObj, 'done') and $refObj->done) {
          unset($assList [$id]);
        }
        if ($displayOnlyCurrentWeekMeetings and get_class($refObj) == 'Meeting') {
          if ($refObj->meetingDate < $startDate or $refObj->meetingDate > $endDate) {
            unset($assList [$id]);
          }
        }
      }
    }
    // Check if assignment exists for each work (may be closed or not assigned: so make it appear)
    foreach ( $workList as $work ) {
      if ($work->idAssignment) {
        $found=false;
        // Look into assList
        if (isset($assList ['#' . $work->idAssignment])) {
          $ass=$assList ['#' . $work->idAssignment];
          $found=true;
        }
        if (!$found) {
          $ass=new Assignment($work->idAssignment);
          if ($ass->id) { // Assignment exists, but not retrieve : display but readonly
            $ass->_locked=true;
            $assList [$ass->id]=$ass;
          } else { // Assignment does not exist : this is an error case as $wor->idAssignment is set !!! SHOULD NOT BE SEEN
            /*
             * $id=$work->refType.'#'.$work->refId; if (! isset($assList[$id])) { // neo-assignment do not exist : insert one $ass->id=null; $ass->name='<span style="color:red;"><i>' . i18n('notAssignedWork') . ' (1)</i></span>'; if ($work->refType and $work->refId) { $ass->comment=i18n($work->refType) . ' #' . $work->refId; } else { $ass->comment='unexpected case : assignment #' . htmlEncode($work->idAssignment) . ' not found'; } $ass->realWork=$work->work; $ass->refType=$work->refType; $ass->refId=$work->refId; } else { // neo-assignment exists : add work (once again ,at this step this should not be displayed, it is an error case $ass=$assList[$id]; $ass->realWork+=$work->work; } $ass->_locked=true; $assList[$id]=$ass;
             */
          }
        }
        if ($work->idWorkElement) { // Check idWorkElement : if set, add new line for ticket, locked
          $acticityAss=$ass; // Save reference to parent activity
          $ass=new Assignment();
          $we=new WorkElement($work->idWorkElement, true);
          $ass->id=$acticityAss->id;
          $ass->name=$we->refName;
          ;
          $ass->refType=$we->refType;
          $ass->refId=$we->refId;
          $ass->realWork=$we->realWork;
          $ass->leftWork=$we->leftWork;
          $ass->_locked=true;
          $ass->_topRefType=$acticityAss->refType;
          $ass->_topRefId=$acticityAss->refId;
          $ass->_idWorkElement=$work->idWorkElement;
          $id=$work->refType . '#' . $work->refId . '#' . $work->idWorkElement;
          $assList [$id]=$ass;
        }
      } else { // Work->idAssignment not set (for tickets not linked to Activities for instance)
        $id=$work->refType . '#' . $work->refId;
        if (isset($assList [$id])) {
          $ass=$assList [$id];
        } else {
          $ass=new Assignment();
        }
        if ($work->refType) { // refType exist (Ticket is best case)
          $obj=new $work->refType($work->refId, true);
          if ($obj->name) {
            $obj->name=htmlEncode($obj->name);
          }
        } else { // refType does not exist : is should not happen (name displayed in red), key ot to avoid errors
          $obj=new Ticket();
          $obj->name='<span style="color:red;"><i>' . i18n('notAssignedWork') . ' (2)</i></span>';
          if (!$ass->comment) {
            $ass->comment='unexpected case : no reference object';
          }
          $ass->_locked=true;
        }
        // $ass->name=$id . " " . $obj->name;
        $ass->name=$obj->name;
        if (isset($obj->WorkElement)) {
          $ass->realWork=$obj->WorkElement->realWork;
          $ass->leftWork=$obj->WorkElement->leftWork;
        }
        $ass->id=null;
        $ass->refType=$work->refType;
        $ass->refId=$work->refId;
        if ($work->refType) {
          // $ass->comment=i18n($work->refType) . ' #' . $work->refId;
        }
        $assList [$id]=$ass;
      }
    }
    
    $notElementary=array();
    $cptNotAssigned=0;
    foreach ( $assList as $idAss => $ass ) {
      $elt=new ImputationLine();
      $elt->idle=$ass->idle;
      $elt->refType=$ass->refType;
      $elt->refId=$ass->refId;
      $elt->comment=$ass->comment;
      $elt->idProject=$ass->idProject;
      $elt->idAssignment=$ass->id;
      $elt->assignedWork=$ass->assignedWork;
      $elt->plannedWork=$ass->plannedWork;
      $elt->realWork=$ass->realWork;
      $elt->leftWork=$ass->leftWork;
      $elt->arrayWork=array();
      if (isset($ass->_locked))
        $elt->locked=true;
      $elt->arrayPlannedWork=array();
      if (!$ass->idProject) {
        $elt->idProject=SqlList::getFieldFromId($ass->refType, $ass->refId, 'idProject');
      }
      if ($ass->idRole) {
        $elt->functionName=SqlList::getNameFromId('Role', $ass->idRole);
      }
      $crit=array('refType' => $elt->refType,'refId' => $elt->refId);
      if (isset($ass->_topRefType) and isset($ass->_topRefId)) {
        $crit=array('refType' => $ass->_topRefType,'refId' => $ass->_topRefId);
      }
      $plan=null;
      if ($ass->id) {
        $plan=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', $crit);
      }
      if ($plan and $plan->id and isset($ass->_topRefType) and isset($ass->_topRefId)) {
        $elt->wbs=$plan->wbs . '.' . htmlEncode($elt->refType) . '#' . $elt->refId;
        $elt->wbsSortable=$plan->wbsSortable . '.' . htmlEncode($elt->refType) . '#' . $elt->refId;
        $elt->topId=$plan->id;
        $elt->elementary=$plan->elementary;
        $elt->startDate=null;
        $elt->endDate=null;
        $elt->elementary=1;
        $elt->imputable=true;
        if (isset($ass->_idWorkElement)) {
          $elt->_idWorkElement=$ass->_idWorkElement;
        }
        $elt->name='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $ass->name;
        $key=$plan->wbsSortable . ' ' . $ass->_topRefType . '#' . $ass->_topRefId;
        if (isset($result [$key])) {
          $result [$key]->elementary=0;
        } else {
          $notElementary [$key]=$key;
        }
        $elt->locked=true;
      } else if ($plan and $plan->id) {
        $elt->name=htmlEncode($plan->refName);
        $elt->wbs=$plan->wbs;
        $elt->wbsSortable=$plan->wbsSortable;
        $elt->topId=$plan->topId;
        $elt->elementary=$plan->elementary;
        $elt->startDate=($plan->realStartDate)?$plan->realStartDate:$plan->plannedStartDate;
        $elt->endDate=($plan->realEndDate)?$plan->realEndDate:$plan->plannedEndDate;
        $elt->imputable=true;
      } else {
        $cptNotAssigned+=1;
        if (isset($ass->name)) {
          $elt->name=$ass->name;
        } else {
          $elt->name='<span style="color:red;"><i>' . i18n('notAssignedWork') . '</i></span>';
          if ($ass->refType and $ass->refId) {
            $elt->comment=i18n($ass->refType) . ' #' . $ass->refId;
          } else {
            $elt->comment='unexpected case : no assignment name';
          }
        }
        $elt->wbs='0.' . $cptNotAssigned;
        $elt->wbsSortable='000.' . str_pad($cptNotAssigned, 3, "0", STR_PAD_LEFT);
        $elt->elementary=1;
        $elt->topId=null;
        $elt->imputable=true;
        $elt->idAssignment=null;
        $elt->locked=true;
      }
      // if ( ! ($user->id = $resourceId or $scopeCode!='ALL' or ($scopeCode='PRO' and array_key_exists($ass->idProject, $visibleProjects) ) ) ) {
      // $elt->locked=true;
      // }
      $key=$elt->wbsSortable . ' ' . htmlEncode($ass->refType) . '#' . $ass->refId;
      if (array_key_exists($key, $result)) {
        $key.='/#' . $ass->id;
      }
      // fetch all work stored in database for this assignment
      foreach ( $workList as $work ) {
        if (($work->idAssignment and $work->idAssignment == $elt->idAssignment and !$work->idWorkElement and !isset($elt->_idWorkElement)) or (!$work->idAssignment and $work->refType == $elt->refType and $work->refId == $elt->refId) or
             ($work->idAssignment and $work->idAssignment == $elt->idAssignment and $work->idWorkElement and isset($elt->_idWorkElement) and $elt->_idWorkElement == $work->idWorkElement)) {
          $workDate=$work->workDate;
          $offset=dayDiffDates($startDate, $workDate) + 1;
          if (isset($elt->arrayWork [$offset])) {
            $elt->arrayWork [$offset]->work+=$work->work;
          } else {
            $elt->arrayWork [$offset]=$work;
          }
        }
      }
      // Fill arrayWork for days without an input
      for ($i=1; $i <= $nbDays; $i++) {
        if (!array_key_exists($i, $elt->arrayWork)) {
          $elt->arrayWork [$i]=new Work();
        }
      }
      if ($showPlanned) {
        foreach ( $plannedWorkList as $plannedWork ) {
          if ($plannedWork->idAssignment == $elt->idAssignment) {
            $workDate=$plannedWork->workDate;
            $offset=dayDiffDates($startDate, $workDate) + 1;
            $elt->arrayPlannedWork [$offset]=$plannedWork;
          }
        }
        // Fill arrayWork for days without an input
        for ($i=1; $i <= $nbDays; $i++) {
          if (!array_key_exists($i, $elt->arrayPlannedWork)) {
            $elt->arrayPlannedWork [$i]=new PlannedWork();
          }
        }
      }
      $result [$key]=$elt;
    }
    // If some not assigned work exists : add group line
    if ($cptNotAssigned > 0) {
      $elt=new ImputationLine();
      $elt->idle=0;
      $elt->arrayWork=array();
      $elt->arrayPlannedWork=array();
      $elt->name=i18n('notAssignedWork');
      $elt->wbs=0;
      $elt->wbsSortable='000';
      $elt->elementary=false;
      $elt->imputable=false;
      $elt->refType='Imputation';
      for ($i=1; $i <= $nbDays; $i++) {
        if (!array_key_exists($i, $elt->arrayWork)) {
          $elt->arrayWork [$i]=new Work();
        }
      }
      $result ['#']=$elt;
    }
    $act=new Activity();
    $accessRight=securityGetAccessRight($act->getMenuClass(), 'read');
    foreach ( $result as $key => $elt ) {
      $result=self::getParent($elt, $result, true, $accessRight);
    }
    ksort($result);
    return $result;
  }
  
  // Get the parent line for hierarchc display purpose
  private static function getParent($elt, $result, $direct=true, $accessRight) {
    // scriptLog(" => ImputationLine->getParent($elt->refType#$elt->refId, result[], $direct)");
    $plan=null;
    $user=getSessionUser();
    $visibleProjectList=$user->getVisibleProjects();
    
    // $visibleProjectList=explode(', ', getVisibleProjectsList());
    if ($elt->topId) {
      $plan=new PlanningElement($elt->topId, true);
    }
    if ($plan) {
      $key=$plan->wbsSortable . ' ' . htmlEncode($plan->refType) . '#' . $plan->refId;
      if (!array_key_exists($key, $result) and ($plan->refType != 'Project' or $direct or $accessRight == 'ALL' or array_key_exists($plan->refId, $visibleProjectList))) {
        $top=new ImputationLine();
        $top->idle=$plan->idle;
        $top->imputable=false;
        $top->name=htmlEncode($plan->refName);
        $top->wbs=$plan->wbs;
        $top->wbsSortable=$plan->wbsSortable;
        $top->topId=$plan->topId;
        $top->refType=$plan->refType;
        $top->refId=$plan->refId;
        // $top->assignedWork=$plan->assignedWork;
        // $top->plannedWork=$plan->plannedWork;
        // $top->realWork=$plan->realWork;
        // $top->leftWork=$plan->leftWork;
        $result [$key]=$top;
        $result=self::getParent($top, $result, $direct=false, $accessRight);
      }
    }
    scriptLog("      => ImputationLine->getParent()-exit");
    return $result;
  }

  private static function getFirstDay($rangeType, $rangeValue) {
    if ($rangeType == 'week') {
      $year=substr($rangeValue, 0, 4);
      $week=substr($rangeValue, 4, 2);
      $day=firstDayofWeek($week, $year);
      return date('Y-m-d', $day);
    }
  }

  static function drawLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned=true, $print=false, $hideDone=false, $hideNotHandled=false, $displayOnlyCurrentWeekMeetings=false, $currentWeek=0, $currentYear=0, $showId=false) {
    $outMode=(isset($_REQUEST ['outMode']))?$_REQUEST ['outMode']:'';
    $outMode=preg_replace('/.*(pdf|csv|html|mpp).*/', '$1', $outMode); // can only be [pdf|csv|html|mpp]
                                                                      // scriptLog(" => ImputationLine->drawLines(resourceId=$resourceId, rangeType=$rangeType, rangeValue=$rangeValue, showIdle=$showIdle, showPlanned=$showPlanned, print=$print, hideDone=$hideDone, hideNotHandled=$hideNotHandled, displayOnlyCurrentWeekMeetings=$displayOnlyCurrentWeekMeetings)");
    $keyDownEventScript=NumberFormatter52::getKeyDownEvent(); // Will add event $commaEvent
    $crit=array('periodRange' => $rangeType,'periodValue' => $rangeValue,'idResource' => $resourceId);
    $period=SqlElement::getSingleSqlElementFromCriteria('WorkPeriod', $crit);
    $user=getSessionUser();
    $canValidate=false;
    $crit=array('scope' => 'workValid','idProfile' => $user->idProfile);
    $habilitation=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
    $scope=new AccessScope($habilitation->rightAccess, true);
    if ($scope->accessCode == 'NO') {
      $canValidate=false;
    } else if ($scope->accessCode == 'ALL') {
      $canValidate=true;
    } else if (($scope->accessCode == 'OWN' or $scope->accessCode == 'RES') and $user->isResource and $resourceId == $user->id) {
      $canValidate=true;
    } else if ($scope->accessCode == 'PRO') {
      $crit='idProject in ' . transformListIntoInClause($user->getVisibleProjects());
      $aff=new Affectation();
      $lstAff=$aff->getSqlElementsFromCriteria(null, false, $crit, null, true, true);
      $fullTable=SqlList::getList('Resource');
      foreach ( $lstAff as $id => $aff ) {
        if ($aff->idResource == $resourceId) {
          $canValidate=true;
          continue;
        }
      }
    }
    $locked=false;
    $oldValues="";
    $nameWidth=220;
    $dateWidth=80;
    $workWidth=65;
    $inputWidth=55;
    $iconWidth=16;
    if ($outMode == 'pdf') {
      $dateWidth=40;
      $workWidth=40;
      $inputWidth=35;
    }
    $resource=new Resource($resourceId);
    $cal=$resource->idCalendarDefinition;
    if (!$cal)
      $cal=1;
    $capacity=work::getConvertedCapacity($resource->capacity);
    $weekendColor="cfcfcf";
    $currentdayColor="ffffaa";
    $today=date('Y-m-d');
    if ($rangeType == 'week') {
      $nbDays=7;
    }
    $startDate=self::getFirstDay($rangeType, $rangeValue);
    $plus=$nbDays - 1;
    $endDate=date('Y-m-d', strtotime("+$plus days", strtotime($startDate)));
    $rangeValueDisplay=substr($rangeValue, 0, 4) . '-' . substr($rangeValue, 4);
    $colSum=array();
    for ($i=1; $i <= $nbDays; $i++) {
      $colSum [$i]=0;
    }
    $width=600;
    if (isset($_REQUEST ['destinationWidth'])) {
      $width=$_REQUEST ['destinationWidth'];
      $width=preg_replace('/[^0-9]/', '', $width); // only allow digits
      $width=($width) - 155 - 30;
    }
    $tab=ImputationLine::getLines($resourceId, $rangeType, $rangeValue, $showIdle, $showPlanned, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings);
    
    if (!$print) {
      echo '<div dojoType="dijit.layout.BorderContainer">';
      echo '<div dojoType="dijit.layout.ContentPane" id="topRegionImputation" data-dojo-props="splitter: true" region="top" style="overflow-y: scroll;height: auto;">';
    }
    echo '<table class="imputationTable" style="width:100%">';
    echo '<TR class="ganttHeight">';
    echo '<td class="label" style="width:10%"><label for="imputationComment" >' . i18n("colComment") . '&nbsp;:&nbsp;</label></td>';
    if (!$print) {
      echo '<td style="width:90%"><textarea dojoType="dijit.form.Textarea" id="imputationComment" name="imputationComment"' . ' onChange="formChanged();"' . ' style="width: 99%;min-height:32px;" maxlength="4000" class="input">' . htmlEncode($period->comment) . '</textarea></td>';
    } else {
      echo htmlEncode($period->comment, 'print');
    }
    echo ' </TR>';
    echo '</table>';
    if (!$print) {
      echo '<input type="hidden" id="resourceCapacity" value="' . $capacity . '" />';
    }
    
    echo '<script type="dojo/connect" event="resize" args="evt">
            var valueHeight=parseInt(dojo.byId(\'topRegionImputation\').offsetHeight)-5;
            dojo.byId(\'imputationComment\').style.height=valueHeight+\'px\';
            dojo.byId(\'imputationComment\').style.maxHeight=valueHeight+\'px\';
            dojo.byId(\'imputationComment\').style.minHeight=valueHeight+\'px\';
            </script>';
    echo '</div>';
    
    echo '<div style="position:relative;overflow-y:scroll;" dojoType="dijit.layout.ContentPane" region="top">';
    echo '<table class="imputationTable" style="width:' . (($outMode == 'pdf')?'68':'100') . '%">';
    echo '<TR class="ganttHeight">';
    echo '  <TD class="ganttLeftTopLine" style="width:' . $iconWidth . 'px;"></TD>';
    echo '  <TD class="ganttLeftTopLine" colspan="5">';
    echo '<table style="width:98%"><tr><td style="width:99%">' . htmlEncode($resource->name) . ' - ' . i18n($rangeType) . ' ' . $rangeValueDisplay;
    echo '</td>';
    if ($period->submitted) {
      $msg='<div class="imputationSubmitted"><span class="nobr">' . i18n('submittedWorkPeriod', array(htmlFormatDateTime($period->submittedDate))) . '</span></div>';
      if (!$print and !$period->validated and ($resourceId == $user->id or $canValidate)) {
        echo '<td style="width:1%">' . $msg . '</td>';
        echo '<td style="width:1%">';
        echo '<button id="unsubmitButton" jsid="unsubmitButton" dojoType="dijit.form.Button" showlabel="true" >';
        echo '<script type="dojo/connect" event="onClick" args="evt">submitWorkPeriod("unsubmit");</script>';
        echo i18n('unSubmitWorkPeriod');
        echo '</button>';
        echo '</td>';
        $locked=true;
      } else {
        echo '<td style="width:1%">' . $msg . '</td>';
      }
    } else if (!$print and $resourceId == $user->id and !$period->validated) {
      echo '<td style="width:1%">';
      echo '<button id="submitButton" dojoType="dijit.form.Button" showlabel="true" >';
      echo '<script type="dojo/connect" event="onClick" args="evt">submitWorkPeriod("submit");</script>';
      echo i18n('submitWorkPeriod');
      echo '</button>';
      echo '</td>';
    }
    echo '<td style="width:10px">&nbsp;&nbsp;&nbsp;</td>';
    if ($period->validated) {
      $locked=true;
      $res=SqlList::getNameFromId('User', $period->idLocker);
      $msg='<div class="imputationValidated"><span class="nobr">' . i18n('validatedWorkPeriod', array(htmlFormatDateTime($period->validatedDate),$res)) . '</span></div>';
      if (!$print and $canValidate) {
        echo '<td style="width:1%">' . $msg . '</td>';
        // echo '<div xdojoType="dijit.Tooltip" xconnectId="unvalidateButton" xposition="above" >'.$msg.'</div>';
        echo '<td style="width:1%">';
        echo '<button id="unvalidateButton" jsid="unvalidateButton" dojoType="dijit.form.Button" showlabel="true" >';
        echo '<script type="dojo/connect" event="onClick" args="evt">submitWorkPeriod("unvalidate");</script>';
        echo i18n('unValidateWorkPeriod');
        echo '</button>';
        echo '</td>';
      } else {
        echo '<td style="width:1%">' . $msg . '</td>';
      }
    } else if (!$print and $canValidate) {
      echo '<td style="width:1%">';
      echo '<button id="validateButton" dojoType="dijit.form.Button" showlabel="true" >';
      echo '<script type="dojo/connect" event="onClick" args="evt">submitWorkPeriod("validate");</script>';
      echo i18n('validateWorkPeriod');
      echo '</button>';
      echo '</td>';
    }
    echo '</tr></table>';
    echo '</TD>';
    echo '  <TD class="ganttLeftTitle" colspan="' . $nbDays . '" ' . 'style="border-right: 1px solid #ffffff;border-bottom: 1px solid #DDDDDD;">' . htmlFormatDate($startDate) . ' - ' . htmlFormatDate($endDate) . '</TD>';
    echo '  <TD class="ganttLeftTopLine" colspan="2" style="text-align:center;color: #707070">' . htmlFormatDate($today) . '</TD>';
    echo '</TR>';
    echo '<TR class="ganttHeight">';
    echo '  <TD class="ganttLeftTitle" style="width:' . $iconWidth . 'px;"></TD>';
    echo '  <TD class="ganttLeftTitle" style="text-align: left; ' . 'border-left:0px; " nowrap>' . i18n('colTask') . '</TD>';
    echo '  <TD class="ganttLeftTitle" style="width: ' . $dateWidth . 'px;max-width:' . $dateWidth . 'px;overflow:hidden;">' . i18n('colStart') . '</TD>';
    echo '  <TD class="ganttLeftTitle" style="width: ' . $dateWidth . 'px;max-width:' . $dateWidth . 'px;overflow:hidden;">' . i18n('colEnd') . '</TD>';
    echo '  <TD class="ganttLeftTitle" style="width: ' . $workWidth . 'px;max-width:' . $workWidth . 'px;overflow:hidden;">' . i18n('colAssigned') . '</TD>';
    echo '  <TD class="ganttLeftTitle" style="width: ' . $workWidth . 'px;max-width:' . $workWidth . 'px;overflow:hidden;">' . i18n('colReal') . '</TD>';
    $curDate=$startDate;
    $businessDay=0;
    for ($i=1; $i <= $nbDays; $i++) {
      echo '  <TD class="ganttLeftTitle" style="width: ' . $inputWidth . 'px;max-width:' . $inputWidth . 'px;overflow:hidden;';
      if ($today == $curDate) {
        echo ' background-color:#' . $currentdayColor . '; color: #aaaaaa;';
      } else if (isOffDay($curDate, $cal)) {
        echo ' background-color:#' . $weekendColor . '; color: #aaaaaa;';
      }
      if (!isOffDay($curDate, $cal))
        $businessDay++;
      echo '">';
      if ($rangeType == 'week') {
        echo i18n('colWeekday' . $i) . " " . date('d', strtotime($curDate)) . '';
      }
      if (!$print) {
        echo ' <input type="hidden" id="day_' . $i . '" name="day_' . $i . '" value="' . $curDate . '" />';
      }
      echo '</TD>';
      $curDate=date('Y-m-d', strtotime("+1 days", strtotime($curDate)));
    }
    $businessDay=$businessDay*$capacity;
    echo '  <TD class="ganttLeftTitle" style="width: ' . $workWidth . 'px;max-width:' . $workWidth . 'px;overflow:hidden;">' . i18n('colLeft') . '</TD>';
    echo '  <TD class="ganttLeftTitle" style="width: ' . $workWidth . 'px;max-width:' . $workWidth . 'px;overflow:hidden;">' . i18n('colReassessed') . '</TD>';
    echo '</TR>';
    if (!$print) {
      echo '</table></div>';
      echo '<div style="position:relative;overflow-y:scroll;" dojoType="dijit.layout.ContentPane" data-dojo-props="splitter: true" region="center">';
      echo '<table class="imputationTable" style="width:' . (($outMode == 'pdf')?'68':'100') . '%">';
    }
    $nbLine=0;
    $collapsedList=Collapsed::getCollaspedList();
    $closedWbs='';
    $wbsLevelArray=array();
    $listLienProject=array();
    $listAllProject=array();
    foreach ( $tab as $key => $line ) {
      if ($line->refType == 'Project' && !isset($listAllProject [$line->refId])) {
        $listAllProject [$line->refId]=new Project($line->refId);
        $listLienProject [$line->refId]=array();
        $listLienProject [$line->refId] []=$line->refId;
        ;
      }
    }
    $listLienProject=ImputationLine::addProjectToListLienProject($listLienProject, $listAllProject);
    foreach ( $tab as $key => $line ) {
      if (($line->refType == 'Activity' && !SqlList::getFieldFromId("Project", SqlList::getFieldFromId("Activity", $line->refId, "idProject"), 'isUnderConstruction')) ||
           ($line->refType != 'Project' && $line->refType != 'Activity' && !SqlList::getFieldFromId("Project", $line->idProject, "isUnderConstruction")) || ($line->refType == 'Project' && !SqlList::getFieldFromId("Project", $line->refId, "isUnderConstruction")))
        if (($line->refType == 'Activity' && SqlList::getFieldFromId("ProjectType", SqlList::getFieldFromId("Project", SqlList::getFieldFromId("Activity", $line->refId, "idProject"), "idProjectType"), 'code') != 'TMP') ||
         ($line->refType != 'Project' && $line->refType != 'Activity' && SqlList::getFieldFromId("ProjectType", SqlList::getFieldFromId("Project", $line->idProject, "idProjectType"), 'code') != 'TMP') ||
         ($line->refType == 'Project' && SqlList::getFieldFromId("ProjectType", SqlList::getFieldFromId("Project", $line->refId, "idProjectType"), 'code') != 'TMP')) {
      if ($locked)
        $line->locked=true;
      $nbLine++;
      if ($line->elementary) {
        $rowType="row";
      } else {
        $rowType="group";
      }
      // if ($closedWbs and strlen($line->wbsSortable)<=strlen($closedWbs)) {
      if ($closedWbs and (strlen($line->wbsSortable) <= strlen($closedWbs) or $closedWbs != substr($line->wbsSortable, 0, strlen($closedWbs)))) {
        $closedWbs="";
      }
      $scope='Imputation_' . $resourceId . '_' . htmlEncode($line->refType) . '_' . $line->refId;
      $collapsed=false;
      if ($rowType == "group" and array_key_exists($scope, $collapsedList)) {
        $collapsed=true;
        if (!$closedWbs) {
          $closedWbs=$line->wbsSortable;
        }
      }
      $canRead=false;
      $canGoto=false;
      if ($line->refType and $line->refId) {
        $obj=new $line->refType($line->refId, true);
        $canRead=(securityGetAccessRightYesNo('menu' . $line->refType, 'read', $obj) == 'YES');
        $canGoto=($canRead and securityCheckDisplayMenu(null, $line->refType))?true:false;
      }
      
      echo '<tr id="line_' . $nbLine . '"class="ganttTask' . $rowType . '"';
      if ($closedWbs and $closedWbs != $line->wbsSortable) {
        echo ' style="display:none" ';
      }
      echo '>';
      echo '<td class="ganttName" style="width:' . ($iconWidth + 1) . 'px;">';
      if (!$print) {
        echo '<input type="hidden" id="wbs_' . $nbLine . '" ' . ' value="' . htmlEncode($line->wbsSortable) . '"/>';
        echo '<input type="hidden" id="status_' . $nbLine . '" ';
        if ($collapsed) {
          echo ' value="closed"';
        } else {
          echo ' value="opened"';
        }
        echo '/>';
        echo '<input type="hidden" id="idAssignment_' . $nbLine . '" name="idAssignment[]"' . ' value="' . htmlEncode($line->idAssignment) . '"/>';
        echo '<input type="hidden" id="imputable_' . $nbLine . '" name="imputable[]"' . ' value="' . (($line->imputable)?'1':'0') . '"/>';
        echo '<input type="hidden" id="locked_' . $nbLine . '" name="locked[]"' . ' value="' . (($line->locked)?'1':'0') . '"/>';
      }
      if (!$line->refType) {
        $line->refType='Imputation';
      }
      ;
      echo '<a ';
      if ($line->refType != 'Imputation' and !$print) {
        echo ' onmouseover="showBigImage(null,null,this,\'' . i18n($line->refType) . ' #' . htmlEncode($line->refId) . '<br/>';
        if ($canRead)
          echo '<i>' . i18n("clickToView") . '</i>';
        echo '\');" onmouseout="hideBigImage();"';
      }
      if (!$print and $canRead) {
        echo ' class="pointer" onClick="directDisplayDetail(\'' . htmlEncode($line->refType) . '\',\'' . htmlEncode($line->refId) . '\')"';
      }
      echo '>';
      echo formatIcon($line->refType, 16);
      echo '</a>';
      echo '</td>';
      echo '<td class="ganttName" >';
      // tab the name depending on level
      echo '<table width:"100%"><tr><td>';
      $wbs=$line->wbsSortable;
      $wbsTest=$wbs;
      $level=1;
      while ( strlen($wbsTest) > 3 ) {
        $wbsTest=substr($wbsTest, 0, strlen($wbsTest) - 4);
        if (array_key_exists($wbsTest, $wbsLevelArray)) {
          $level=$wbsLevelArray [$wbsTest] + 1;
          $wbsTest="";
        }
      }
      $wbsLevelArray [$wbs]=$level;
      // $level=(strlen($line->wbsSortable)+1)/4;
      $levelWidth=($level - 1) * 16;
      echo '<div style="float: left;width:' . $levelWidth . 'px;">&nbsp;</div>';
      echo '</td>';
      if (!$print) {
        if ($rowType == "group") {
          echo '<td width="16"><span id="group_' . $nbLine . '" ';
          if ($collapsed) {
            echo 'class="ganttExpandClosed"';
          } else {
            echo 'class="ganttExpandOpened"';
          }
          if (!$print) {
            echo 'onclick="workOpenCloseLine(' . $nbLine . ',\'' . $scope . '\')"';
          } else {
            echo ' style="cursor:default;"';
          }
          echo '>';
          echo '&nbsp;&nbsp;&nbsp;&nbsp;</span><span>&nbsp</span></td>';
        } else {
          echo '<td width="16"><div style="float: left;width:16px;">&nbsp;</div></td>';
        }
      }
      if ($line->refType == "Project") {
        $description=null;
        $crit=array();
        $crit ['id']=$line->refId;
        $description=SqlElement::getSingleSqlElementFromCriteria('Project', $crit);
        if ($description) {
          $line->description=$description->description;
        }
      } else if ($line->refType == "Activity") {
        $descriptionActivity=null;
        $crit2=array();
        $crit2 ['id']=$line->refId;
        $crit2 ['idProject']=$line->idProject;
        $descriptionActivity=SqlElement::getSingleSqlElementFromCriteria('Activity', $crit2);
        if ($descriptionActivity) {
          $line->description=$descriptionActivity->description;
        }
      }
      echo '<td width="100%" style="position:relative"';
      if (!$print and $canGoto) {
        echo ' class="pointer" onClick="gotoElement(\'' . htmlEncode($line->refType) . '\',\'' . htmlEncode($line->refId) . '\')"';
      }
      echo '>' . (($showId && $line->refId)?'#' . $line->refId . ' - ' . $line->name:$line->name);
      echo '<div id="extra_' . $nbLine . '" style="position:absolute; top:-2px; right:2px;" ></div>';
      
      if (isset($line->functionName) and $line->functionName and $outMode != "pdf") {
        echo '<div style="float:right; color:#8080DD; font-size:80%;font-weight:normal;">' . htmlEncode($line->functionName) . '</div>';
      }
      echo '</td>';
      if (!$print && $line->idAssignment && $line->refType != 'Ticket') {
        $explodeComment=array(" a");
        if ($line->comment)
          $explodeComment=explode("\n\n", $line->comment);
        echo '<td id="showBig' . $line->idAssignment . '" style="cursor:pointer;' . ($line->comment?"":"display:none;") . '" onclick="loadDialog(\'dialogCommentImputation\', function(){commentImputationTitlePopup(\'view\');}, true, \'&idAssignment=' . $line->idAssignment . '\', true);">' .
             formatCommentThumb($explodeComment [0]) . '</td>';
      }
      
      if ($line->idAssignment && $line->refType != 'Ticket'){
        //KEVIN

      

      echo '<td '; echo 'onclick="loadDialog(\'dialogCommentImputation\', function(){commentImputationTitlePopup(\'add\');}, true, \'&year=' . $currentYear . '&week=' . $currentWeek . '&idAssignment=' . $line->idAssignment . '&refIdComment=' . $line->refId .
      '&refTypeComment=' . $line->refType . '\', true);"title="' . i18n('commentImputationAdd') .'"'; echo '>';
      echo formatSmallButton('AddComment');
      echo '</td>';}

      
      echo '</tr></table>';
      echo '</td>';
      // echo '<td class="ganttDetail" align="center">' . htmlEncode($line->description) . '</td>';
      echo '<td class="ganttDetail" align="center" width="' . $dateWidth . 'px">' . htmlFormatDate($line->startDate) . '</td>';
      echo '<td class="ganttDetail" align="center" width="' . $dateWidth . 'px">' . htmlFormatDate($line->endDate) . '</td>';
      echo '<td class="ganttDetail" align="center" width="' . $workWidth . 'px">';
      if ($line->imputable) {
        if (!$print) {
          echo '<input type="text" xdojoType="dijit.form.NumberTextBox" ';
          // echo ' constraints="{pattern:\'###0.0#\'}"';
          echo ' style="width: 60px; text-align: center; " ';
          echo ' trim="true" class="input dijitTextBox dijitNumberTextBox dijitValidationTextBox displayTransparent" readOnly="true" tabindex="-1" ';
          echo ' id="assignedWork_' . $nbLine . '"';
          echo ' value="' . htmlDisplayNumericWithoutTrailingZeros(Work::displayImputation($line->assignedWork)) . '" ';
          echo ' />';
          // echo '</div>';
        } else {
          echo Work::displayImputation($line->assignedWork);
        }
      }
      echo '</td>';
      echo '<td class="ganttDetail" align="center" width="' . $workWidth . 'px">';
      if ($line->imputable) {
        if (!$print) {
          echo '<input type="text" xdojoType="dijit.form.NumberTextBox" ';
          // echo ' constraints="{pattern:\'###0.0#\'}"';
          echo ' style="width: 60px; text-align: center;" ';
          echo ' trim="true" class="input dijitTextBox dijitNumberTextBox dijitValidationTextBox displayTransparent" readOnly="true" tabindex="-1" ';
          echo ' id="realWork_' . $nbLine . '"';
          echo ' value="' . htmlDisplayNumericWithoutTrailingZeros(Work::displayImputation($line->realWork)) . '" ';
          echo ' />';
          // echo '</div>';
        } else {
          echo Work::displayImputation($line->realWork);
        }
      }
      echo '</td>';
      $curDate=$startDate;
      $listProject=Project::getAdminitrativeProjectList(true);
      for ($i=1; $i <= $nbDays; $i++) {
        echo '<td class="ganttDetail" align="center" width="' . $inputWidth . 'px;"';
        if ($today == $curDate) {
          echo ' style="background-color:#' . $currentdayColor . ';"';
        } else if (isOffDay($curDate, $cal)) {
          echo ' style="background-color:#' . $weekendColor . '; color: #aaaaaa;"';
        }
        echo '>';
        if ($line->imputable) {
          $isAdministrative=false;
          if (array_key_exists($line->idProject, $listProject))
            $isAdministrative=true;
          $valWork=$line->arrayWork [$i]->work;
          $idWork=$line->arrayWork [$i]->id;
          if (!$print) {
            echo '<div style="position: relative">';
            if ($showPlanned and $line->arrayPlannedWork [$i]->work) {
              echo '<div style="display: inline;';
              echo ' position: absolute; left: 7px; top: 1px; text-align: right;';
              echo ' color:#8080DD; font-size:90%;">';
              echo Work::displayImputation($line->arrayPlannedWork [$i]->work);
              echo '</div>';
            }
            echo '<div type="text" idProject="' . $line->idProject . '" dojoType="dijit.form.NumberTextBox" ';
            echo ' constraints="{min:0}"';
            echo '  style="width: 45px; text-align: center; ' . (($line->idle or $line->locked)?'color:#A0A0A0; xbackground: #EEEEEE;':'') . ' " ';
            echo ' trim="true" maxlength="4" class="input imputation" ';
            echo ' id="workValue_' . $nbLine . '_' . $i . '"';
            echo ' name="workValue_' . $i . '[]"';
            echo ' value="' . Work::displayImputation($valWork) . '" ';
            if ($line->idle or $line->locked) {
              echo ' readOnly="true" ';
            }
            echo ' >';
            // echo '<script type="dojo/method" event="onFocus" args="evt">';
            // echo ' oldImputationWorkValue=this.value;';
            // echo '</script>';
            echo $keyDownEventScript;
            echo '<script type="dojo/method" event="onChange" args="evt">';
            echo '  dispatchWorkValueChange("' . $nbLine . '","' . $i . '");';
            echo '</script>';
            echo '</div>';
            echo '</div>';
            if (!$print) {
              echo '<input type="hidden" id="workId_' . $nbLine . '_' . $i . '"' . ' name="workId_' . $i . '[]"' . ' value="' . $idWork . '"/>';
              echo '<input type="hidden" id="isAdministrative_' . $nbLine . '_' . $i . '"' . ' value="' . ($isAdministrative?1:0) . '"/>';
              echo '<input type="hidden" id="workOldValue_' . $nbLine . '_' . $i . '"' . ' value="' . Work::displayImputation($valWork) . '"/>';
              echo '<input type="hidden" id="idProject_' . $nbLine . '_' . $i . '"' . ' value="' . $line->idProject . '"/>';
            }
          } else {
            echo Work::displayImputation($valWork);
          }
          $colSum [$i]+=Work::displayImputation($valWork);
        } else {
          $sumWork=0;
          if ($line->refType == 'Project') {
            $sumWork=Work::displayImputation(ImputationLine::getAllWorkProjectDay($i, $listLienProject, $tab, $line->refId));
            echo '<div style="display:none" id="sumProject_' . $line->refId . '_' . $i . '">' . $sumWork . '</div>';
            echo '<input type="text" style="width: 45px; text-align: center;font-weight:bold;" ';
            echo ' class="input dijitTextBox dijitNumberTextBox dijitValidationTextBox displayTransparent imputation" readOnly="true" tabindex="-1" ';
            echo ' id="sumProjectDisplay_' . $line->refId . '_' . $i . '"';
            echo ' value="' . htmlDisplayNumericWithoutTrailingZeros($sumWork) . '" ';
            echo ' />';
            if ($listAllProject [$line->refId]->idProject && $listAllProject [$line->refId]->idProject != $line->refId)
              echo '<input type="hidden" id="projectParent_' . $line->refId . '_' . $i . '" value="' . $listAllProject [$line->refId]->idProject . '">';
          }
          /*
           * foreach ($line->arrayWork as $idW=>$lll){ if(isset($line->arrayWork[$idW]) && isset($line->arrayWork[$idW]->work))$sumWork+=$line->arrayWork[$idW]->work; }
           */
          echo '<input type="hidden" name="workId_' . $i . '[]" />';
          echo '<input type="hidden" name="workValue_' . $i . '[]" />';
        }
        echo '</td>';
        $curDate=date('Y-m-d', strtotime("+1 days", strtotime($curDate)));
      }
      echo '<td class="ganttDetail" ' . ($line->refType == 'Project'?'colspan="2"':'') . ' align="center" width="' . $workWidth . 'px;">';
      if ($line->imputable) {
        if (!$print) {
          echo '<div type="text" dojoType="dijit.form.NumberTextBox" ';
          echo ' constraints="{min:0}"';
          echo '  style="width: 60px; text-align: center;' . (($line->idle or $line->locked)?'color:#A0A0A0; xbackground: #EEEEEE;':'') . ' " ';
          echo ' trim="true" class="input imputation" ';
          echo ' id="leftWork_' . $nbLine . '"';
          echo ' name="leftWork[]"';
          echo ' value="' . Work::displayImputation($line->leftWork) . '" ';
          if ($line->idle or $line->locked) {
            echo ' readOnly="true" ';
          }
          echo ' >';
          echo $keyDownEventScript;
          echo '<script type="dojo/method" event="onChange" args="evt">';
          echo '  dispatchLeftWorkValueChange("' . $nbLine . '");';
          echo '</script>';
          echo '</div>';
        } else {
          echo Work::displayImputation($line->leftWork);
        }
      } else {
        if ($line->refType == 'Project') {
          echo '<div style="display:none" id="sumWeekProject_' . $line->refId . '">' . Work::displayImputation(ImputationLine::getAllWorkProjectWeek($listLienProject, $tab, $line->refId, $nbDays)) . '</div>';
          echo '<input type="text" style="width: 90px; text-align: center;font-weight:bold;" ';
          echo ' class="input dijitTextBox dijitNumberTextBox dijitValidationTextBox displayTransparent" readOnly="true" tabindex="-1" ';
          echo ' id="sumWeekProjectDisplay_' . $line->refId . '"';
          echo ' value="' . htmlDisplayNumericWithoutTrailingZeros(Work::displayImputation(ImputationLine::getAllWorkProjectWeek($listLienProject, $tab, $line->refId, $nbDays))) . '" ';
          echo ' />';
        }
        echo '<input type="hidden" id="leftWork_' . $nbLine . '" name="leftWork[]" />';
      }
      echo '</td>';
      if ($line->refType != 'Project') {
        echo '<td class="ganttDetail" align="center" width="' . $workWidth . 'px;">';
        if ($line->imputable) {
          if (!$print) {
            echo '<input type="text" xdojoType="dijit.form.NumberTextBox" ';
            // echo ' constraints="{pattern:\'###0.0#\'}"';
            echo '  style="width: 60px; text-align: center;" ';
            echo ' trim="true" class="input dijitTextBox dijitNumberTextBox dijitValidationTextBox displayTransparent" readOnly="true" tabindex="-1" ';
            echo ' id="plannedWork_' . $nbLine . '"';
            echo ' value="' . htmlDisplayNumericWithoutTrailingZeros(Work::displayImputation($line->plannedWork)) . '" ';
            echo ' />';
            // echo '</div>';
          } else {
            echo Work::displayImputation($line->plannedWork);
          }
        }
        echo '</td>';
        echo '</tr>';
      }
    }
}
if (!$print) {
  echo '<input type="hidden" id="nbLines" name="nbLines" value="' . $nbLine . '" />';
}
if (!$print and count($tab) > 20) {
  echo '</table>';
  echo '</div>';
  echo '<div dojoType="dijit.layout.ContentPane" region="bottom" style="overflow-y: scroll; height: auto;">';
  echo '<table class="imputationTable" style="width:100%">';
}
echo '<TR class="ganttDetail" >';
echo '  <TD class="ganttLeftTopLine" style="width:' . $iconWidth . 'px;"></TD>';
echo '  <TD class="ganttLeftTopLine" colspan="5" style="text-align: left; ' . 'border-left:0px;" nowrap><span class="nobr">';
echo Work::displayImputationUnit();
echo '</span></TD>';

$curDate=$startDate;
$nbFutureDays=Parameter::getGlobalParameter('maxDaysToBookWork');
if ($nbFutureDays == null || $nbFutureDays == '')
  $nbFutureDays=-1;
$nbFutureDaysBlocking=Parameter::getGlobalParameter('maxDaysToBookWorkBlocking');
if ($nbFutureDaysBlocking == null || $nbFutureDaysBlocking == '')
  $nbFutureDaysBlocking=-1;
$maxDateFuture=date('Y-m-d', strtotime("+" . $nbFutureDays . " days"));
$maxDateFutureBlocking=date('Y-m-d', strtotime("+" . $nbFutureDaysBlocking . " days"));
if (!$print)
  echo '<input type="hidden" id="nbFutureDays" value="' . $nbFutureDays . '" />';
if (!$print)
  echo '<input type="hidden" id="nbFutureDaysBlocking" value="' . $nbFutureDaysBlocking . '" />';
if (!$print)
  echo '<input type="hidden" value="' . $maxDateFuture . '" />';
if (!$print)
  echo '<input type="hidden" id="businessDay" value="' . ($businessDay) . '" />';
$totalWork=0;
for ($i=1; $i <= $nbDays; $i++) {
  echo '  <TD class="ganttLeftTitle" style="width: ' . $inputWidth . 'px;';
  if ($today == $curDate) {
    // echo ' background-color:#' . $currentdayColor . ';';
  }
  echo '"><span class="nobr">';
  if (!$print) {
    echo '<div type="text" dojoType="dijit.form.NumberTextBox" ';
    // echo ' constraints="{pattern:\'###0.0#\'}"';
    echo ' trim="true" disabled="true" ';
    if (round($colSum [$i], 2) > $capacity) {
      echo ' class="imputationInvalidCapacity imputation"';
    } else if (round($colSum [$i], 2) < $capacity) {
      echo ' class="displayTransparent imputation"';
    } else {
      echo ' class="imputationValidCapacity imputation"';
    }
    echo '  style="width: 45px; text-align: center; color: #000000 !important;" ';
    echo ' id="colSumWork_' . $i . '"';
    echo ' value="' . $colSum [$i] . '" ';
    echo ' >';
    echo '</div>';
    echo '<input type="hidden" id="colIsFuture_' . $i . '" value="' . (($curDate > $maxDateFuture && $nbFutureDays != -1)?1:0) . '" />';
    echo '<input type="hidden" id="colIsFutureBlocking_' . $i . '" value="' . (($curDate > $maxDateFutureBlocking && $nbFutureDaysBlocking != -1)?1:0) . '" />';
  } else {
    echo $colSum [$i];
  }
  $totalWork+=$colSum [$i];
  echo '</span></TD>';
  $curDate=date('Y-m-d', strtotime("+1 days", strtotime($curDate)));
}
$classTotalWork="imputationValidCapacity";
if (round($totalWork, 2) > $businessDay) {
  $classTotalWork='imputationInvalidCapacity';
} else if (round($totalWork, 2) < $businessDay) {
  $classTotalWork='displayTransparent';
}
$colSpanFooter='colspan="2"';
$inputWidthFooter=$inputWidth;
if (!$print and count($tab) > 20) {
  $colSpanFooter='';
  $inputWidthFooter=2 * $inputWidth;
}

echo '  <TD ' . $colSpanFooter . ' class="ganttLeftTitle" style="width: 132px;><span class="nobr" ><div id="totalWork" type="text" trim="true" disabled="true" dojoType="dijit.form.NumberTextBox" style="font-weight:bold;width: 95%; text-align: center; color: #000000 !important;" class="' .
     $classTotalWork . ' imputation" value="' . $totalWork . '"</div></span></TD>';

echo '</TR>';
echo '</table>';
if (!$print) {
  echo '</div>';
  echo '</div>';
}
}

static function getAllWorkProjectDay($day, $listLienProject, $imputationList, $idProject) {
$sumWork=0;
foreach ( $imputationList as $id => $line ) {
  foreach ( $listLienProject [$idProject] as $id2 => $line2 ) {
    if ($line->idProject == $line2) {
      $sumWork+=$line->arrayWork [$day]->work;
    }
  }
}
return $sumWork;
}

static function getAllWorkProjectWeek($listLienProject, $imputationList, $idProject, $nbDays) {
$sumWork=0;
foreach ( $imputationList as $id => $line ) {
  foreach ( $listLienProject [$idProject] as $id2 => $line2 ) {
    if ($line->idProject == $line2) {
      for ($i=1; $i <= $nbDays; $i++) {
        $sumWork+=$line->arrayWork [$i]->work;
      }
    }
  }
}
return $sumWork;
}

static function addProjectToListLienProject($listLienProject, $listAllProject, $idProject=-1, $idProjectOld=-1) {
if ($idProject == -1)
  foreach ( $listAllProject as $idP => $line ) {
    if ($listAllProject [$idP]->idProject && $listAllProject [$idP]->idProject != $listAllProject [$idP]->id) {
      $listLienProject [$listAllProject [$idP]->idProject] []=$listAllProject [$idP]->id;
      $listLienProject=ImputationLine::addProjectToListLienProject($listLienProject, $listAllProject, $listAllProject [$idP]->idProject, $listAllProject [$idP]->id);
    }
  }
if ($idProject != -1) {
  foreach ( $listLienProject [$idProjectOld] as $idLP => $line ) {
    $find=false;
    foreach ( $listLienProject [$idProject] as $idLP2 => $line2 ) {
      if ($listLienProject [$idProject] [$idLP2] == $listLienProject [$idProjectOld] [$idLP])
        $find=true;
    }
    if (!$find)
      $listLienProject [$idProject] []=$listLienProject [$idProjectOld] [$idLP];
  }
  if (isset($listAllProject [$idProject]) && $listAllProject [$idProject]->idProject && $listAllProject [$idProject]->idProject != $listAllProject [$idProject]->id) {
    $listLienProject=ImputationLine::addProjectToListLienProject($listLienProject, $listAllProject, $listAllProject [$idProject]->idProject, $listAllProject [$idProject]->id);
  }
}
return $listLienProject;
}

// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********

// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********

/**
 * ==========================================================================
 * Return the validation sript for some fields
 * 
 * @return the validation javascript (for dojo frameword)
 */
public function getValidationScript($colName) {
$colScript=parent::getValidationScript($colName);

if ($colName == "idle") {
  $colScript.='<script type="dojo/connect" event="onChange" >';
  $colScript.='  if (this.checked) { ';
  $colScript.='    if (dijit.byId("PlanningElement_realEndDate").get("value")==null) {';
  $colScript.='      dijit.byId("PlanningElement_realEndDate").set("value", new Date); ';
  $colScript.='    }';
  $colScript.='  } else {';
  $colScript.='    dijit.byId("PlanningElement_realEndDate").set("value", null); ';
  // $colScript .= ' dijit.byId("PlanningElement_realDuration").set("value", null); ';
  $colScript.='  } ';
  $colScript.='  formChanged();';
  $colScript.='</script>';
}
return $colScript;
}

// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
public function save() {
$finalResult="";
foreach ( $this->arrayWork as $work ) {
  $result="";
  if ($work->work) {
    // echo "save";
    $result=$work->save();
  } else {
    if ($work->id) {
      // echo "delete";
      $result=$work->delete();
    }
  }
  if (stripos($result, 'id="lastOperationStatus" value="ERROR"') > 0) {
    $status='ERROR';
    $finalResult=$result;
    break;
  } else if (stripos($result, 'id="lastOperationStatus" value="OK"') > 0) {
    $status='OK';
    $finalResult=$result;
  } else {
    if ($finalResult == "") {
      $finalResult=$result;
    }
  }
}
return $finalResult;
}
}
?>