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
 * Project is the main object of the project managmement.
 * Almost all other objects are linked to a given project.
 */ 
require_once('_securityCheck.php');
class OrganizationMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place
  public $name;
  public $idOrganizationType;
  public $idResource;
  public $idUser;
  public $creationDate;
  public $lastUpdateDateTime;
  public $idle;
  public $description;
  public $_sec_currentProjects;
  public $BudgetElementCurrent; // is an object

  public $_sec_Link;
  public $_Link=array();
  public $_Attachment=array();
  public $_Note=array();

  // hidden
  public $_nbColMax=3;
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="wbsSortable" from="OrganizationPlanningElement" formatter="sortableFormatter" width="10%" >${wbs}</th>
    <th field="name" width="65%" >${projectName}</th>
    <th field="nameOrganizationType" width="20%" >${type}</th>
    ';
// Removed in 1.2.0 
//     <th field="wbs" from="ProjectPlanningElement" width="5%" >${wbs}</th>
// Removed in 2.0.1
//  <th field="nameRecipient" width="10%" >${idRecipient}</th>
  

  private static $_fieldsAttributes=array(
      "name"=>"required",                                   
      "idOrganizationType"=>"required"
  );   
 
  private static $_colCaptionTransposition = array('idResource'=>'manager',
   'idUser'=>'issuer');
  
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

// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
  
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld=null) {
    return self::$_colCaptionTransposition;
  }  

    /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
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
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Recusively retrieves all the hierarchic sub-projects of the current project
   * @return an array containing id, name, subprojects (recursive array)
   */

  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    $result="";
     return $result;
  }
  


   /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {	
    $old=$this->getOld();
    $result = parent::save();
    return $result; 

  }
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
    //if ($this->id and $this->id==$this->idProject) {
    //  $result.='<br/>' . i18n('errorHierarchicLoop');
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
  
  public function updateSynthesis() {
    // Update current budgetElement
    $bec=$this->BudgetElementCurrent;
    $pe=new ProjectPlanningElement();
    
    $bec->validatedWork=0;
    $bec->assignedWork=0;
    $bec->realWork=0;
    $bec->leftWork=0;
    $bec->plannedWork=0;
    $bec->validatedCost=0;
    $bec->assignedCost=0;
    $bec->realCost=0;
    $bec->leftCost=0;
    $bec->plannedCost=0;
    $bec->expenseValidatedAmount=0;
    $bec->expenseAssignedAmount=0;
    $bec->expenseRealAmount=0;
    $bec->expenseLeftAmount=0;
    $bec->expensePlannedAmount=0;
    $bec->reserveAmount=0;
    $bec->totalValidatedCost=0;
    $bec->totalAssignedCost=0;
    $bec->totalRealCost=0;
    $bec->totalLeftCost=0;
    $bec->totalPlannedCost=0;
    
    // Add all Projects
    $crit=array('idOrganization'=>$this->id, 'idle'=>'0');
    $peList=$pe->getSqlElementsFromCriteria($crit);
    foreach ($peList as $pe) {
      $bec->validatedWork+=$pe->validatedWork;
      $bec->assignedWork+=$pe->assignedWork;
      $bec->realWork+=$pe->realWork;
      $bec->leftWork+=$pe->leftWork;
      $bec->plannedWork+=$pe->plannedWork;
      $bec->validatedCost+=$pe->validatedCost;
      $bec->assignedCost+=$pe->assignedCost;
      $bec->realCost+=$pe->realCost;
      $bec->leftCost+=$pe->leftCost;
      $bec->plannedCost+=$pe->plannedCost;
      $bec->expenseValidatedAmount+=$pe->expenseValidatedAmount;
      $bec->expenseAssignedAmount+=$pe->expenseAssignedAmount;
      $bec->expenseRealAmount+=$pe->expenseRealAmount;
      $bec->expenseLeftAmount+=$pe->expenseLeftAmount;
      $bec->expensePlannedAmount+=$pe->expensePlannedAmount;
      $bec->reserveAmount+=$pe->reserveAmount;
      $bec->totalValidatedCost+=$pe->totalValidatedCost;
      $bec->totalAssignedCost+=$pe->totalAssignedCost;
      $bec->totalRealCost+=$pe->totalRealCost;
      $bec->totalLeftCost+=$pe->totalLeftCost;
      $bec->totalPlannedCost+=$pe->totalPlannedCost;
      $crit=array('topId'=>$pe->id,'refType'=>'Project');
      // Remove sub-projects : will remove sub-projects of same Organization (already included) and of different Organization (must not be included)
      // This way, for projects with sub-projects we count only work on main project, sub-projects are added separately
      // It is importatn to di this way to remove sub-projects of different Organization 
      $subList=$pe->getSqlElementsFromCriteria($crit);
      foreach ($subList as $sub) {
        $bec->validatedWork-=$sub->validatedWork;
        $bec->assignedWork-=$sub->assignedWork;
        $bec->realWork-=$sub->realWork;
        $bec->leftWork-=$sub->leftWork;
        $bec->plannedWork-=$sub->plannedWork;
        $bec->validatedCost-=$sub->validatedCost;
        $bec->assignedCost-=$sub->assignedCost;
        $bec->realCost-=$sub->realCost;
        $bec->leftCost-=$sub->leftCost;
        $bec->plannedCost-=$sub->plannedCost;
        $bec->expenseValidatedAmount-=$sub->expenseValidatedAmount;
        $bec->expenseAssignedAmount-=$sub->expenseAssignedAmount;
        $bec->expenseRealAmount-=$sub->expenseRealAmount;
        $bec->expenseLeftAmount-=$sub->expenseLeftAmount;
        $bec->expensePlannedAmount-=$sub->expensePlannedAmount;
        $bec->reserveAmount-=$sub->reserveAmount;
        $bec->totalValidatedCost-=$sub->totalValidatedCost;
        $bec->totalAssignedCost-=$sub->totalAssignedCost;
        $bec->totalRealCost-=$sub->totalRealCost;
        $bec->totalLeftCost-=$sub->totalLeftCost;
        $bec->totalPlannedCost-=$sub->totalPlannedCost;
      }
    }

    $bec->save();
  }
  
  public static function getUserOrganization() {
    $res=new Affectable(getSessionUser()->id);
    return $res->idOrganization;
  }
  
  public static function getUserOrganisationList() {
    $userOrga = self::getUserOrganization(); // TODO : include sub-organizations
    return $userOrga;
  }
  
}
?>