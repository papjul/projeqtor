<?php
/*** COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
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
 * Budget Element is an object included in all objects that can be budgeted.
 */ 
require_once('_securityCheck.php');
class BudgetElementMain extends SqlElement {

  public $id;
  public $refType;
  public $refId;
  public $year;
  public $refName;
  public $budgetWork;
  public $validatedWork;
  public $assignedWork;
  public $realWork;
  public $leftWork;
  public $plannedWork;
  public $budgetCost;
  public $validatedCost;
  public $assignedCost;
  public $realCost;
  public $leftCost;
  public $plannedCost;
  public $topId;
  public $topRefType;
  public $topRefId;
  public $elementary;
  public $expenseBudgetAmount;
  public $expenseAssignedAmount;
  public $expensePlannedAmount;
  public $expenseRealAmount;
  public $expenseLeftAmount;
  public $expenseValidatedAmount;
  public $totalBudgetCost;
  public $totalAssignedCost;
  public $totalPlannedCost;
  public $totalRealCost;
  public $totalLeftCost;
  public $totalValidatedCost;
  public $reserveAmount;
  public $idle;
  
  private static $_fieldsAttributes=array(
                                  "id"=>"hidden",
                                  "refType"=>"hidden",
                                  "refId"=>"hidden",
                                  "refName"=>"hidden",
                                  "progress"=>"display,noImport",
                                  "topId"=>"hidden",
                                  "topRefType"=>"hidden",
                                  "topRefId"=>"hidden",
                                  "idle"=>"hidden",
                                  "validatedWork"=>"readonly,noImport",
                                  "assignedWork"=>"readonly,noImport",
                                  "realWork"=>"readonly,noImport",
                                  "leftWork"=>"readonly,noImport",
                                  "plannedWork"=>"readonly,noImport",
                                  "validatedCost"=>"readonly,noImport",
                                  "assignedCost"=>"readonly,noImport",
                                  "realCost"=>"readonly,noImport",
                                  "leftCost"=>"readonly,noImport",
                                  "plannedCost"=>"readonly,noImport",
                                  "elementary"=>"hidden"
                                  
  );   
  
  private static $_databaseTableName = 'budgetelement';
  public static $_noDispatch=false;
  public static $_noDispatchArray=array();
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

  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
  }
// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
 
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);
   
    
    return $colScript;
  }
  
  /** ==========================================================================
   * Extends save functionality to implement wbs calculation
   * Triggers parent::save() to run defaut functionality in the end.
   * @return the result of parent::save() function
   */
  public function save() {  	
  	// Get old element (stored in database) : must be fetched before saving
    $old=new BudgetElement($this->id);
    $result=parent::save();
    return $result;
  }

  
  public function simpleSave() {
    // Avoir save actions
    $result = parent::save();
    return $result;
  }
  
    /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
    /**
   * Delete object 
   * @see persistence/SqlElement#save()
   */
  public function delete() { 
    $result = parent::delete();
    return $result;
   
  }
  
 /** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    //if ($this->idle and $this->leftWork>0) {
    //  $result.='<br/>' . i18n('errorIdleWithLeftWork');
    //}
   
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }
    if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
  public function deleteControl()
  {
  	$result="";
  	 
  	// Cannot delete item with real work
  	//if ($this->id and $this->realWork and $this->realWork>0)	{
  	//	$result .= "<br/>" . i18n("msgUnableToDeleteRealWork");
  	//}
  	 
  	if (! $result) {
  		$result=parent::deleteControl();
  	}
  	return $result;
  }
  
  public function getFieldAttributes($fieldName) {
    return parent::getFieldAttributes($fieldName);
  }  

}
?>