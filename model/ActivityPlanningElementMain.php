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
class ActivityPlanningElementMain extends PlanningElement {

  public $id;
  public $idProject;
  public $refType;
  public $refId;
  public $refName;
  public $_tab_5_3_smallLabel = array('validated', 'planned', 'real', '', 'requested', 'startDate', 'endDate', 'duration');
  public $validatedStartDate;
  public $plannedStartDate;
  public $realStartDate;
  public $_void_1_4;
  public $initialStartDate;
  public $validatedEndDate;
  public $plannedEndDate;
  public $realEndDate;
  public $_void_2_4;
  public $initialEndDate;
  public $validatedDuration;
  public $plannedDuration;
  public $realDuration;
  public $_void_3_4;
  public $initialDuration;
  public $_tab_5_2_smallLabel_1 = array('validated', 'assigned', 'real', 'left', 'reassessed', 'work', 'cost');
  public $validatedWork;
  public $assignedWork;
  public $realWork;
  public $leftWork;
  public $plannedWork;
  public $validatedCost;
  public $assignedCost;
  public $realCost;
  public $leftCost;
  public $plannedCost;
  public $_tab_5_2_smallLabel_2 = array('', '', '', '', '', 'progress','priority');
  public $progress;
  public $_label_expected;
  public $expectedProgress;
  public $_label_wbs;
  public $wbs;
  public $priority;
  public $_label_idActivityPlanningMode;
  public $idActivityPlanningMode;
  public $_tab_5_1_smallLabel = array('workElementCount', 'estimated', 'real', 'left', '', 'ticket');
  public $workElementCount;
  public $workElementEstimatedWork;
  public $workElementRealWork;
  public $workElementLeftWork;
  public $_button_showTickets;
  
  
  //public $_label_wbs;
  
  //public $_label_progress;
  
  //public $_label_expected;
 
  public $wbsSortable;
  public $topId;
  public $topRefType;
  public $topRefId;
  public $idle;

  
  private static $_fieldsAttributes=array(
    "plannedStartDate"=>"readonly,noImport",
    "realStartDate"=>"readonly,noImport",
    "plannedEndDate"=>"readonly,noImport",
    "realEndDate"=>"readonly,noImport",
    "plannedDuration"=>"readonly,noImport",
    "realDuration"=>"readonly,noImport",
    "initialWork"=>"hidden",
    "plannedWork"=>"readonly,noImport",
  	"notPlannedWork"=>"hidden",
    "realWork"=>"readonly,noImport",
    "leftWork"=>"readonly,noImport",
    "assignedWork"=>"readonly,noImport",
    "idActivityPlanningMode"=>"required,mediumWidth,colspan3",
    "idPlanningMode"=>"hidden,noImport",
  	"workElementEstimatedWork"=>"readonly,noImport",
  	"workElementRealWork"=>"readonly,noImport",
  	"workElementLeftWork"=>"readonly,noImport",
  	"workElementCount"=>"display,noImport",
    "plannedStartFraction"=>"hidden",
    "plannedEndFraction"=>"hidden",
    "validatedStartFraction"=>"hidden",
    "validatedEndFraction"=>"hidden"
  );   
  
  private static $_databaseTableName = 'planningelement';
  
  private static $_databaseColumnName=array(
    "idActivityPlanningMode"=>"idPlanningMode"
  );
    
  /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    if ($this->isAttributeSetToField('workElementCount', 'hidden') 
        and $this->isAttributeSetToField('workElementEstimatedWork', 'hidden')
        and $this->isAttributeSetToField('workElementRealWork', 'hidden')
        and $this->isAttributeSetToField('workElementLeftWork', 'hidden')) {
      self::$_fieldsAttributes['_button_showTickets']='hidden';   
    }
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
    $this->updateWorkElementSummary(true);
    return parent::save();
  }
  
/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    $mode=null;
    if ($this->idActivityPlanningMode) {
      $mode=new ActivityPlanningMode($this->idActivityPlanningMode);
    }   
    if ($mode) {
      if ($mode->mandatoryStartDate and ! $this->validatedStartDate) {
        $result.='<br/>' . i18n('errorMandatoryValidatedStartDate');
      }
      if ($mode->mandatoryEndDate and ! $this->validatedEndDate) {
        $result.='<br/>' . i18n('errorMandatoryValidatedEndDate');
      }
      if ($mode->mandatoryDuration and ! $this->validatedDuration) {
        $result.='<br/>' . i18n('errorMandatoryValidatedDuration');
      }
   
    }
   
    
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }if ($result=="") {
      $result='OK';
    }
    return $result;
    
  }
  
  /** =========================================================================
   * Update the synthesis Data (work) from workElement (tipically Tickets)
   * Called by workElement
   * @return void
   */
  public function updateWorkElementSummary($noSave=false) {
    $we=new WorkElement();  	
  	$weList=$we->getSqlElementsFromCriteria(array('idActivity'=>$this->refId));
  	$this->workElementEstimatedWork=0;
  	$this->workElementRealWork=0;
  	$this->workElementLeftWork=0;
  	$this->workElementCount=0;
  	foreach ($weList as $we) {
  		$this->workElementEstimatedWork+=$we->plannedWork;
  		$this->workElementRealWork+=$we->realWork;
  		$this->workElementLeftWork+=$we->leftWork;
  		$this->workElementCount+=1;
  	}
  	if (! $noSave) {
  	  $this->simpleSave();
  	}
  	$top=new Activity($this->refId);
  	if ($this->workElementCount==0 and $top->isPlanningActivity) {
  	  $top->isPlanningActivity=0;
  	  $top->saveForced();
  	} else if ($this->workElementCount>0 and !$top->isPlanningActivity) {
  	  $top->isPlanningActivity=1;
  	  $top->saveForced();
  	}
  }
  
  public function drawSpecificItem($item) {
    if ($item='showTickets') {
      echo '<div id="' . $item . 'Button" ';
      echo ' title="' . i18n('showTickets') . '" style="float:right;margin-right:3px;"';
      echo ' class="roundedButton">';
      echo '<div class="iconView" ';
      $jsFunction="showTickets('Activity',$this->refId);";
      echo ' onclick="' . $jsFunction . '"';
      echo '></div>';
      echo '</div>';
    }
  }
}
?>