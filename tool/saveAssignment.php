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

$assignmentId=null;
if (array_key_exists('assignmentId',$_REQUEST)) {
  $assignmentId=$_REQUEST['assignmentId']; // validated to be numeric in SqlElement base constructor
}
$assignmentId=trim($assignmentId);
if ($assignmentId=='') {
  $assignmentId=null;
}

// Get the assignment info
if (! array_key_exists('assignmentRefType',$_REQUEST)) {
  throwError('assignmentRefType parameter not found in REQUEST');
}
$refType=$_REQUEST['assignmentRefType'];
Security::checkValidClass($refType);

if (! array_key_exists('assignmentRefId',$_REQUEST)) {
  throwError('assignmentRefId parameter not found in REQUEST');
}
$refId=$_REQUEST['assignmentRefId'];
Security::checkValidId($refId);

$idResource=null;
if (array_key_exists('assignmentIdResource',$_REQUEST)) {
  $idResource=$_REQUEST['assignmentIdResource'];
	Security::checkValidId($idResource);
}

$idRole=null;
if (array_key_exists('assignmentIdRole',$_REQUEST)) {
  $idRole=$_REQUEST['assignmentIdRole'];
	Security::checkValidId($idRole);
}

$cost=null;
if (array_key_exists('assignmentDailyCost',$_REQUEST)) {
  $cost=$_REQUEST['assignmentDailyCost'];
  Security::checkValidNumeric($cost);
}

if (! array_key_exists('assignmentRate',$_REQUEST)) {
  throwError('assignmentRate parameter not found in REQUEST');
}
$rate=$_REQUEST['assignmentRate'];
Security::checkValidNumeric($rate);

if (! array_key_exists('assignmentAssignedWork',$_REQUEST)) {
  throwError('assignmentAssignedWork parameter not found in REQUEST');
}
$assignedWork=$_REQUEST['assignmentAssignedWork'];
Security::checkValidNumeric($assignedWork);

if (! array_key_exists('assignmentRealWork',$_REQUEST)) {
  throwError('assignmentRealWork parameter not found in REQUEST');
}
$realWork=$_REQUEST['assignmentRealWork'];
Security::checkValidNumeric($realWork);

if (! array_key_exists('assignmentLeftWork',$_REQUEST)) {
  throwError('assignmentLeftWork parameter not found in REQUEST');
}
$leftWork=$_REQUEST['assignmentLeftWork'];
Security::checkValidNumeric($leftWork);

if (! array_key_exists('assignmentPlannedWork',$_REQUEST)) {
  throwError('assignmentPlannedWork parameter not found in REQUEST');
}
$plannedWork=$_REQUEST['assignmentPlannedWork'];
Security::checkValidNumeric($plannedWork);


if (! array_key_exists('assignmentComment',$_REQUEST)) {
  throwError('assignmentComment parameter not found in REQUEST');
}
//$comment=htmlEncode($_REQUEST['assignmentComment']);
$comment=$_REQUEST['assignmentComment']; // Must not escape : will be done on display

Sql::beginTransaction();
// get the modifications (from request)
$assignment=new Assignment($assignmentId);
$oldCost=$assignment->dailyCost;

$assignment->refId=$refId;
$assignment->refType=$refType;
if (! $realWork && $idResource) {
  $assignment->idResource=$idResource;
}
$assignment->idRole=$idRole;
$assignment->dailyCost=$cost;
if (! $oldCost or $assignment->dailyCost!=$oldCost) {
  $assignment->newDailyCost=$cost;
}
$assignment->rate=$rate;
$assignment->assignedWork=Work::convertWork($assignedWork);
//$assignment->realWork=Work::convertWork($realWork); // Should not be changed here
$assignment->leftWork=Work::convertWork($leftWork);
$assignment->plannedWork=Work::convertWork($plannedWork);
$assignment->comment=$comment;

if (! $assignment->idProject) {
  $refObj=new $refType($refId);
  $assignment->idProject=$refObj->idProject;
}

if (! $oldCost and $cost and $assignment->realWork) {
	$wk=new Work();
	$where="idResource=" . Sql::fmtId($assignment->idResource);
	$where.=" and idAssignment=" . $assignment->id ;
	$where.=" and (cost=0 or cost is null) and work>0";
	$wkList=$wk->getSqlElementsFromCriteria(null, false, $where);
	foreach ($wkList as $wk) {
		$wk->dailyCost=$cost;
		$wk->dailyCost=$cost*$wk->work;
		$wk->save();
	}
	$assignment->realCost=$assignment->realWork*$assignment->dailyCost;
}

$result=$assignment->save();




$elt=new $assignment->refType($assignment->refId);
if ($assignmentId) {
  $elt->sendMailIfMailable(false,false,false,false,false,false,false,false,false,false,true,false);
} else {
  $elt->sendMailIfMailable(false,false,false,false,false,false,false,false,false,true,false,false);
}
if ($refType=='Meeting' or $refType=='PeriodicMeeting') {
	Meeting::removeDupplicateAttendees($refType, $refId);
}
  
// Message of correct saving
displayLastOperationStatus($result);
?>