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
 * Planning element is an object included in all objects that can be planned.
 */ 
require_once('_securityCheck.php');
class MilestonePlanningElementMain extends PlanningElement {

    public $id;
  public $idProject;
  public $refType;
  public $refId;
  public $refName;
  public $_tab_5_1_smallLabel = array('validated', 'planned', 'real','','requested', 'dueDate');
  public $validatedEndDate;
  public $plannedEndDate;
  public $realEndDate;
  public $_void_1_4;
  public $initialEndDate;
  public $_tab_3_1_smallLabel = array('', '', '','wbs');
  public $wbs;
  public $_label_planning;
  public $idMilestonePlanningMode;
  
  public $wbsSortable;
  public $topId;
  public $topRefType;
  public $topRefId;
  public $priority;
  public $idle;
  private static $_fieldsAttributes=array(
    "priority"=>"hidden,noImport",
    "initialStartDate"=>"hidden,noImport",
    "validatedStartDate"=>"hidden,noImport",
    "plannedStartDate"=>"hidden,noImport",
    "realStartDate"=>"hidden,noImport",
    "initialDuration"=>"hidden,noImport",
    "validatedDuration"=>"hidden,noImport",
    "plannedDuration"=>"hidden,noImport",
    "realDuration"=>"hidden,noImport",
    "initialWork"=>"hidden,noImport",
    "validatedWork"=>"hidden,noImport",
    "plannedWork"=>"hidden,noImport",
  	"notPlannedWork"=>"hidden",
    "realWork"=>"hidden,noImport",
    "plannedEndDate"=>"readonly",
    "assignedWork"=>"hidden,noImport",
    "leftWork"=>"hidden,noImport",
    "validatedCost"=>"hidden,noImport",
    "plannedCost"=>"hidden,noImport",
    "realCost"=>"hidden,noImport",
    "assignedCost"=>"hidden,noImport",
    "leftCost"=>"hidden,noImport",
    "realEndDate"=>"readonly,noImport",
    "idMilestonePlanningMode"=>"required,mediumWidth",
    "progress"=>"hidden,noImport",
    "expectedProgress"=>"hidden,noImport",
    "plannedStartFraction"=>"hidden",
    "plannedEndFraction"=>"hidden",
    "validatedStartFraction"=>"hidden",
    "validatedEndFraction"=>"hidden"
  );   
  
  private static $_databaseTableName = 'planningelement';
  
  private static $_databaseColumnName=array(
    "idMilestonePlanningMode"=>"idPlanningMode"
  );
    
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
    
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return array_merge(parent::getStaticFieldsAttributes(),self::$_fieldsAttributes);
  }

    /** ========================================================================
   * Return the generic databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
    $this->initialStartDate=$this->initialEndDate;
    $this->validatedStartDate=$this->validatedEndDate;
    $this->plannedStartDate=$this->plannedEndDate;
    $this->realStartDate=$this->realEndDate;
    $this->initialDuration=0;
    $this->validatedDuration=0;
    $this->plannedDuration=0;
    $this->realDuration=0;
    $this->initialWork=0;
    $this->validatedWork=0;
    $this->plannedWork=0;
    $this->notPlannedWork=0;
    $this->realWork=0;
    $this->elementary=1;
    return parent::save();
  }
  
}
?>