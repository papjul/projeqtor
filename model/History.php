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

/* ============================================================================
 * History reflects all changes to any object.
 */ 
require_once('_securityCheck.php');
class History extends SqlElement {

  // extends SqlElement, so has $id
  public $id;    // redefine $id to specify its visible place 
  public $refType;
  public $refId;
  public $operation;
  public $colName; 
  public $oldValue;
  public $newValue;
  public $operationDate;
  public $idUser;
  public $isWorkHistory;
  
  public $_noHistory=true; // Will never save history for this object
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
  }

  
   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

  /** ===========================================================================
   * Store a new History trace (will call ->save)
   * @param $refType type of object updated
   * @param $refId id of object updated
   * @param $operation 
   * @param $colName name of column updated
   * @param $oldValue old value of column (before update)
   * @param $newValue new value of column (after update)
   * @return boolean true if save is OK, false either
   */
  public static function store ($obj, $refType, $refId, $operation, $colName=null, $oldValue=null, $newValue=null) {
  	$user=getSessionUser();
    $hist=new History();
    // Attention : History fields are not to be escaped by Sql::str because $olValue and $newValue have already been escaped
    // So other fiels (names) must be manually "quoted"
    if ($refType=='PlanningElement' and $obj and isset($obj->refType)) {
    	$refType=$obj->refType.'PlanningElement';
    }
    $hist->refType=$refType;
    if ($refType=='TicketSimple') {
      $hist->refType='Ticket';
    }
    $hist->refId=$refId;
    $hist->operation=$operation;
    $hist->colName=$colName;
    if ($colName and strtolower(substr($obj->getDataType($colName),-4))=='text') {
    	$hist->oldValue=mb_substr($oldValue,0,$hist->getDataLength('oldValue'),'UTF-8');
    	$hist->newValue=mb_substr($newValue,0,$hist->getDataLength('newValue'),'UTF-8');
    } else {
    	$hist->oldValue=$oldValue;
    	$hist->newValue=$newValue;
    }
    if ($obj and property_exists($obj, '_workHistory')) {
      $hist->isWorkHistory=1;
    }
    $hist->idUser=$user->id;
    $returnValue=$hist->save();
    // For TestCaseRun : store history for TestSession 
    if ($refType=='TestCaseRun') {
    	self::store ($obj, 'TestSession', $obj->idTestSession, $operation , $colName. '|' . 'TestCase' . '|' .$obj->idTestCase, $oldValue, $newValue);
    } else if ($refType=='Link') {       
    // For link : store History for both referenced items
      self::store ($obj, $obj->ref1Type, $obj->ref1Id, $operation , 'Link' . '|' . $colName. '|' . $obj->ref2Type . '|' . $obj->ref2Id, $oldValue, $newValue);
      if ($obj->ref1Type!=$obj->ref2Type or $obj->ref1Id!=$obj->ref2Id) {
        self::store ($obj, $obj->ref2Type, $obj->ref2Id, $operation , 'Link' . '|' . $colName. '|' . $obj->ref1Type . '|' . $obj->ref1Id, $oldValue, $newValue);
      }
    } else if ($refType=='Note') {
    	if ($operation=='insert') {
    		$newValue=$obj->note;
    	} else if ($operation=='delete') {
        $oldValue=$obj->note;
      }
    	if ($colName!="updateDate") {    
        self::store ($obj, $obj->refType, $obj->refId, $operation , $colName. '|' . $refType . '|' . $obj->id, $oldValue, $newValue);
    	}
    } else if ($refType=='Attachment') {
      if ($operation=='insert') {
        $newValue=$obj->fileName;
      } else if ($operation=='delete') {
        $oldValue=$obj->fileName;
      }
      if ($colName!="updateDate") {    
        self::store ($obj, $obj->refType, $obj->refId, $operation , $colName. '|' . $refType . '|' . $obj->id, $oldValue, $newValue);
      }
    } 
    if (strpos($returnValue,'<input type="hidden" id="lastOperationStatus" value="OK"')) {
      return true;
    } else {
      return false;
    }
  }
}
?>