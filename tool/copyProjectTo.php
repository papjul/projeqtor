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
 * Copy an object as a new one (of the same class) : call corresponding method in SqlElement Class
 */

require_once "../tool/projeqtor.php";
projeqtor_set_time_limit(300);

// Get the object from session(last status before change)
$proj=SqlElement::getCurrentObject(null,null,true,false);
if (! is_object($proj)) {
  throwError('last saved object is not a real object');
}
// Get the object class from request

if (! array_key_exists('copyProjectToName',$_REQUEST)) {
  throwError('copyProjectToName parameter not found in REQUEST');
}
$toName=$_REQUEST['copyProjectToName'];
if (! array_key_exists('copyProjectToType',$_REQUEST)) {
  throwError('copyProjectToName parameter not found in REQUEST');
}
$toType=$_REQUEST['copyProjectToType'];
$copyStructure=false;
if (array_key_exists('copyProjectStructure',$_REQUEST)) {
	$copyStructure=true;
}
$copySubProjects=false;
if (array_key_exists('copySubProjects',$_REQUEST)) {
  $copySubProjects=true;
}
$copyAffectations=false;
if (array_key_exists('copyProjectAffectations',$_REQUEST)) {
  $copyAffectations=true;
}
$copyAssignments=false;
if (array_key_exists('copyProjectAssignments',$_REQUEST)) {
	$copyAssignments=true;
}

$codeProject=null;
if (array_key_exists('copyProjectToProjectCode',$_REQUEST)) {
  $codeProject=$_REQUEST['copyProjectToProjectCode'];
}
// copy from existing object
Sql::beginTransaction();
$error=false;
//$newProj=copyProject($proj, $toName, $toType , $copyStructure, $copySubProjects, $copyAffectations, $copyAssignments, null);

Security::checkValidId($toType);
$newProj=$proj->copyTo('Project',$toType, $toName,  false, false, false, false, $copyAssignments);
$newProj->projectCode=$codeProject;
$result=$newProj->_copyResult;
if (! stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
  $error=true;
}
unset($newProj->_copyResult);
if(!$error)$newProj->save();
if (!$error and $copyStructure) {
  $res=PlanningElement::copyStructure($proj, $newProj, false, false, false,false, $copyAssignments, $copyAffectations,$newProj->id,$copySubProjects);
  if ($res!='OK') {
    $result=$res;
    $error=true;
  } else {
    PlanningElement::copyStructureFinalize();
  }
}
// copy affectations
if (!$error and $copyAffectations) {
  $aff=new Affectation();
  $crit=array('idProject'=>$proj->id);
  $lstAff=$aff->getSqlElementsFromCriteria($crit);
  foreach ($lstAff as $aff) {
    $critExists=array('idProject'=>$newProj->id, 'idResource'=>$aff->idResource);
    $affExists=SqlElement::getSingleSqlElementFromCriteria('Affectation', $critExists);
    if (!$affExists or !$affExists->id) {
  		$aff->id=null;
  		$aff->idProject=$newProj->id;
  		$aff->save();
    }
  }
}
// Message of correct saving
$status = displayLastOperationStatus($result);
if ($status == "OK") {
  if (! array_key_exists ( 'comboDetail', $_REQUEST )) {
    SqlElement::setCurrentObject (new Project( $newProj->id ));
  }
  User::resetAllVisibleProjects(null,getSessionUser()->id); // Will reteive visibiity for new project and sub-projects
}

/*
function copyProject($proj, $toName, $toType , $copyStructure, $copySubProjects, $copyAffectations, $copyAssignments, $newTop=null) {
  $newProj=$proj->copyTo('Project',$toType, $toName, false, false,false,false, $copyAssignments);
  $result=$newProj->_copyResult;
	$nbErrors=0;
	$errorFullMessage="";
	// Save Structure
  if (stripos($result,'id="lastOperationStatus" value="OK"')>0 and $copySubProjects) {
 	// copy subProjects
    $crit=array('idProject'=>$proj->id);
    $project=New Project();
    $projects=$project->getSqlElementsFromCriteria($crit, false, null, null, true);
    foreach ($projects as $project) {
      $newSubProject=copyProject($project, $project->name, $toType , $copyStructure, $copySubProjects, $copyAffectations, $copyAssignments, $proj->id);
      $subResult=$newSubProject->_copyResult;
      unset($newSubProject->_copyResult);
      if (stripos($subResult,'id="lastOperationStatus" value="OK"')>0 ) {
        $newSubProject->idProject=$newProj->id;
        $newSubProject->ProjectPlanningElement->wbs="";
        $newSubProject->save();        
      } else {
      	errorLog($subResult);  
      	$errorFullMessage.='<br/>'.i18n('Project').' #'.htmlEncode($project->id)." : ".$subResult;
        $nbErrors++;
      }
    }
  }
	if (stripos($result,'id="lastOperationStatus" value="OK"')>0 and $copyStructure and $nbErrors==0) {
      $milArray=array();
	  $milArrayObj=array();
	  $actArray=array();
	  $actArrayObj=array();
		$crit=array('idProject'=>$proj->id);
		$items=array();
		// Activities to be copied
	  $activity=New Activity();
	  $activities=$activity->getSqlElementsFromCriteria($crit, false, null, null, true);
	  foreach ($activities as $activity) {
	    $act=new Activity($activity->id);
	    $items['Activity_'.$activity->id]=$act;
	  }
	  $mile=New Milestone();
	  $miles=$mile->getSqlElementsFromCriteria($crit, false, null, null, true);
	  foreach ($miles as $mile) {
	    $mil=new Milestone($mile->id);
	    $items['Milestone_'.$mile->id]=$mil;
	  }
	  // Sort by wbsSortable
	  uasort($items, "customSortByWbsSortable");
	  $itemArrayObj=array();
	  $itemArray=array();
	  $itemArrayAssignment=array();
	  foreach ($items as $id=>$item) {
	  	$new=$item->copy();
	  	$tmpRes=$new->_copyResult;
	  	if (! stripos($tmpRes,'id="lastOperationStatus" value="OK"')>0 ) {
          errorLog($tmpRes);
          $errorFullMessage.='<br/>'.i18n(get_class($item)).' #'.htmlEncode($item->id)." : ".$tmpRes;
          $nbErrors++;
        } else {
  	  	  $itemArrayObj[get_class($new) . '_' . $new->id]=$new;
  	      $itemArray[$id]=get_class($new) . '_' . $new->id;
  	      if ($copyAssignments and property_exists($item, '_Assignment')) {
  	      	$itemArrayAssignment[]=array('class'=>get_class($item),'oldId'=>$item->id,'newId'=>$new->id);
  	      }
        }
	  }
	  foreach ($itemArrayObj as $new) {
			$new->idProject=$newProj->id;
			if ($new->idActivity) {
			 if (array_key_exists('Activity_' . $new->idActivity,$itemArray)) {
			 	$split=explode('_',$itemArray['Activity_' . $new->idActivity]);
			 	$new->idActivity=$split[1];
			 }
			}
			$pe=get_class($new).'PlanningElement';
			$new->$pe->wbs=null;
			$tmpRes=$new->save();
			if (! stripos($tmpRes,'id="lastOperationStatus" value="OK"')>0 ) {
				errorLog($tmpRes);
				$errorFullMessage.='<br/>'.i18n(get_class($new)).' #'.htmlEncode($new->id)." : ".$tmpRes;
				$nbErrors++;
			} 
		}
		if ($copyAssignments) {
			foreach ($itemArrayAssignment as $item) {
				$ass=new Assignment();
				$crit=array('refType'=>$item['class'], 'refId'=>$item['oldId']);
				$lstAss=$ass->getSqlElementsFromCriteria($crit);
				foreach ($lstAss as $ass) {
					$ass->id=null;
					$ass->idProject=$newProj->id;
					$ass->refId=$item['newId'];
					$ass->comment=null;
					$ass->realWork=0;
					$ass->leftWork=$ass->assignedWork;
					$ass->plannedWork=$ass->assignedWork;
					$ass->realStartDate=null;
					$ass->realEndDate=null;
					$ass->plannedStartDate=null;
					$ass->plannedEndDate=null;
					$ass->realCost=0;
					$ass->leftCost=$ass->assignedCost;
					$ass->plannedCost=$ass->assignedCost;
					$ass->billedWork=null;
					$ass->idle=0;
					$ass->save();
				}
			}
		}
	  // Copy dependencies
	  $critWhere="";
	  foreach ($itemArray as $id=>$new) {
	  	$split=explode('_',$id);
	  	$critWhere.=($critWhere)?', ':'';
	  	$critWhere.="('" . $split[0] . "','" . Sql::fmtId($split[1]) . "')";
	  }
	  if ($critWhere) {
	    $clauseWhere="(predecessorRefType,predecessorRefId) in (" . $critWhere . ")"
	         . " or (successorRefType,successorRefId) in (" . $critWhere . ")";
	  } else {
	  	$clauseWhere=" 1=0 ";
	  }
	  $dep=New dependency();
	  $deps=$dep->getSqlElementsFromCriteria(null, false, $clauseWhere);
	  foreach ($deps as $dep) {
	  	if (array_key_exists($dep->predecessorRefType . "_" . $dep->predecessorRefId, $itemArray) ) {
	  		$split=explode('_',$itemArray[$dep->predecessorRefType . "_" . $dep->predecessorRefId]);
	  		$dep->predecessorRefType=$split[0];
	  		$dep->predecessorRefId=$split[1];
	      $crit=array('refType'=>$split[0], 'refId'=>$split[1]);
	      $pe=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', $crit);
	      $dep->predecessorId=$pe->id;
	  	}
	    if (array_key_exists($dep->successorRefType . "_" . $dep->successorRefId, $itemArray) ) {
	      $split=explode('_',$itemArray[$dep->successorRefType . "_" . $dep->successorRefId]);
	      $dep->successorRefType=$split[0];
	      $dep->successorRefId=$split[1];
	      $crit=array('refType'=>$split[0], 'refId'=>$split[1]);
	      $pe=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', $crit);
	      $dep->successorId=$pe->id;
	    }
	  	$dep->id=null;
	    $tmpRes=$dep->save();
	    if (! stripos($tmpRes,'id="lastOperationStatus" value="OK"')>0 ) {
	      errorLog($tmpRes);
        $errorFullMessage.='<br/>'.i18n(get_class($dep)).' #'.htmlEncode($dep->id)." : ".$tmpRes;
	      $nbErrors++;
	    } 
	  }	
  }
  if (stripos($result,'id="lastOperationStatus" value="OK"')>0 and $copyAffectations and $nbErrors==0) {
  	$aff=new Affectation();
  	$crit=array('idProject'=>$proj->id);
  	$lstAff=$aff->getSqlElementsFromCriteria($crit);
  	foreach ($lstAff as $aff) {
  		$aff->id=null;
  		$aff->idProject=$newProj->id;
  		$aff->save();
  	}
  }
  
	if ($nbErrors>0) {
    $result='<div class="messageERROR" >' 
           . i18n('errorMessageCopy',array($nbErrors))
           . '</div><br/>'
           . str_replace('<br/><br/>','<br/>',$errorFullMessage);
    $newProj->_copyResult=str_replace('id="lastOperationStatus" value="OK"','id="lastOperationStatus" value="ERROR"',$result);
  }
  return $newProj;
}

function customSortByWbsSortable($a,$b) {
	$pe=get_class($a).'PlanningElement';
	$wbsA=$a->$pe->wbsSortable;
	$pe=get_class($b).'PlanningElement';
  $wbsB=$b->$pe->wbsSortable;
  return ($wbsA > $wbsB)?1:-1;
}*/
?>