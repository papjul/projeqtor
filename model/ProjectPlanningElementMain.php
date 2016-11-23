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
class ProjectPlanningElementMain extends PlanningElement {

  public $id;
  public $idProject;
  public $refType;
  public $refId;
  public $refName;
  public $_tab_5_3_smallLabel = array('validated', 'planned', 'real', '', 'requested', 'startDate', 'endDate', 'duration' );
  //'real', 'left', '', '', '', '', , 'work', 'resourceCost', 'expense', 'totalCost');
  public $validatedStartDate;
  public $plannedStartDate;
  public $realStartDate;
  public $_void_14;
  public $initialStartDate;
  public $validatedEndDate;
  public $plannedEndDate;
  public $realEndDate;
  public $_void_24;
  public $initialEndDate;
  public $validatedDuration;
  public $plannedDuration;
  public $realDuration;
  public $_void_34;
  public $initialDuration;
  public $_tab_5_5_smallLabel = array('validated','assigned','real','left','reassessed',
      'work','cost','expense','reserveAmountShort','totalCost');
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
  public $expenseValidatedAmount;
  public $expenseAssignedAmount;
  public $expenseRealAmount;
  public $expenseLeftAmount;
  public $expensePlannedAmount;
  public $_void_res_11;
  public $_void_res_12;
  public $_void_res_13;
  public $reserveAmount;
  public $_void_res_15;
  public $totalValidatedCost;
  public $totalAssignedCost;
  public $totalRealCost;
  public $totalLeftCost;
  public $totalPlannedCost;
  public $_tab_5_1_smallLabel_1 = array('','','','','',
      'progress');
  public $progress;
  public $_label_expected;
  public $expectedProgress;
  public $_label_wbs;
  public $wbs;
  public $_tab_5_1_smallLabel_2 = array('','','','','',
      'margin');
  public $marginWork;
  public $marginWorkPct;
  public $marginCost;
  public $marginCostPct;
  public $_void_7_5;
  public $_tab_1_1_smallLabel_3 = array('',
      'priority');
  public $priority;
  public $wbsSortable;
  public $topId;
  public $topRefType;
  public $topRefId;
  public $idle;
  public $idOrganization;
  public $organizationInherited;
  public $organizationElementary;
  
  private static $_fieldsAttributes=array(
    "plannedStartDate"=>"readonly,noImport",
    "realStartDate"=>"readonly,noImport",
    "plannedEndDate"=>"readonly,noImport",
    "realEndDate"=>"readonly,noImport",
    "plannedDuration"=>"readonly,noImport",
    "realDuration"=>"readonly,noImport",
    "initialWork"=>"hidden,noImport",
    "plannedWork"=>"readonly,noImport",
  	"notPlannedWork"=>"hidden",
    "realWork"=>"readonly,noImport",
    "leftWork"=>"readonly,noImport",
    "assignedWork"=>"readonly,noImport",
    "idPlanningMode"=>"hidden,noImport",
  	"expenseAssignedAmount"=>"readonly,noImport",
  	"expensePlannedAmount"=>"readonly,noImport",
  	"expenseRealAmount"=>"readonly,noImport",
  	"expenseLeftAmount"=>"readonly,noImport",
  	"totalAssignedCost"=>"readonly,noImport",
  	"totalPlannedCost"=>"readonly,noImport",
  	"totalRealCost"=>"readonly,noImport",
  	"totalLeftCost"=>"readonly,noImport",
  	"totalValidatedCost"=>"readonly,noImport",
    "plannedStartFraction"=>"hidden",
    "plannedEndFraction"=>"hidden",
    "validatedStartFraction"=>"hidden",
    "validatedEndFraction"=>"hidden",
    "reserveAmount"=>"readonly",
    "idOrganization"=>"hidden",
    "organizationInherited"=>"hidden",
    "organizationElementary"=>"hidden"
  );   
  
  private static $_databaseTableName = 'planningelement';
  
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
    
  public function save() {
    $old=$this->getOld();
  	$this->updateTotal();
  	$result=parent::save();

  	// Save History (for burndown graph)
  	if ($this->realWork and	($this->realWork!=$old->realWork or $this->leftWork!=$old->leftWork
  	                      or $this->realCost!=$old->realCost or $this->leftCost!=$old->leftCost
  	                      or $this->totalRealCost!=$old->totalRealCost or $this->leftCost!=$old->leftCost) ) {
  	  $crit=array('idProject'=>$this->refId, 'day'=>date('Ymd'));
  	  $histo=SqlElement::getSingleSqlElementFromCriteria('ProjectHistory', $crit);
  	  $histo->idProject=$this->refId;
  	  $histo->day=date('Ymd');
  	  $histo->realWork=$this->realWork;
  	  $histo->leftWork=$this->leftWork;
  	  $histo->realCost=$this->realCost;
  	  $histo->leftCost=$this->leftCost;
  	  $histo->totalRealCost=$this->totalRealCost;
  	  $histo->totalLeftCost=$this->totalLeftCost;
  	  $histo->save();
  	}
  	
  	// Update BudgetElement (for organization summary)
  	if ($this->idOrganization) {
  	  $org=new Organization($this->idOrganization,false);
  	  $org->updateSynthesis();
  	} 
  	if ($old->idOrganization and $this->idOrganization!=$old->idOrganization) {
  	  $org=new Organization($old->idOrganization,false);
  	  $org->updateSynthesis();
  	}
  	
  	return $result;
  }
  
  public function updateTotal() {
  	$this->totalAssignedCost=$this->assignedCost+$this->expenseAssignedAmount;
  	$this->totalLeftCost=$this->leftCost+$this->expenseLeftAmount+$this->reserveAmount;
  	$this->totalPlannedCost=$this->plannedCost+$this->expensePlannedAmount+$this->reserveAmount;
  	$this->totalRealCost=$this->realCost+$this->expenseRealAmount;
  	$this->totalValidatedCost=$this->validatedCost+$this->expenseValidatedAmount;
  	if ($this->plannedWork!=0 and $this->validatedWork!=0) {
  	  $this->marginWork=$this->validatedWork-$this->plannedWork;
  	  $this->marginWorkPct=round($this->marginWork/$this->validatedWork*100,0);
  	} else {
  	  $this->marginWork=null;
  	  $this->marginWorkPct=null;
  	}
    if ($this->totalPlannedCost and $this->totalValidatedCost) {
  	  $this->marginCost=$this->totalValidatedCost-$this->totalPlannedCost;
  	  $this->marginCostPct=round($this->marginCost/$this->totalValidatedCost*100,0);
  	} else {
  	  $this->marginCost=null;
  	  $this->marginCostPct=null;
  	}
  	$this->plannedWork=$this->realWork+$this->leftWork; // Need to be done here to refrehed
  	$this->plannedCost=$this->realCost+$this->leftCost;
  }
  
  protected function updateSynthesisObj ($doNotSave=false) {
  	$this->updateSynthesisProject($doNotSave);
  }
  protected function updateSynthesisProject ($doNotSave=false) {
  	parent::updateSynthesisObj(true); // Will update work and resource cost, but not save yet ;)
  	$this->updateExpense(true); // Will retrieve expense directly on the project
  	$this->updateReserve(true); // Will retrieve reserve for risk directly on the project
  	$this->addTicketWork(true); // Will add ticket work that is not linked to Activity
  	$consolidateValidated=Parameter::getGlobalParameter('consolidateValidated');
  	$hasSubProjects=false;
  	$this->_noHistory=true;
  	// Add expense data from other planningElements
  	$validatedExpense=0;
  	$assignedExpense=0;
  	$plannedExpense=0;
  	$realExpense=0;
  	$leftExpense=0;
  	if (! $this->elementary) {
  		$critPla=array("topId"=>$this->id);
  		$planningElement=new ProjectPlanningElement();
  		$plaList=$planningElement->getSqlElementsFromCriteria($critPla, false);
  		// Add data from other planningElements dependant from this one
  		foreach ($plaList as $pla) { 
  		  if ($pla->refType=='Project') $hasSubProjects=true; 			
  			if (!$pla->cancelled and $pla->expenseValidatedAmount) $validatedExpense+=$pla->expenseValidatedAmount;
  			if (!$pla->cancelled and $pla->expenseAssignedAmount) $assignedExpense+=$pla->expenseAssignedAmount;
  			if (!$pla->cancelled and $pla->expensePlannedAmount) $plannedExpense+=$pla->expensePlannedAmount;
  		  $realExpense+=$pla->expenseRealAmount;
  			if (!$pla->cancelled and $pla->expenseLeftAmount) $leftExpense+=$pla->expenseLeftAmount;
  			if (isset($pla->reserveAmount) and $pla->reserveAmount) $this->reserveAmount+=$pla->reserveAmount;
  		}
  	}
  	// save cumulated data
  	$this->expenseAssignedAmount+=$assignedExpense;
  	$this->expensePlannedAmount+=$plannedExpense;
  	$this->expenseRealAmount+=$realExpense;
  	$this->expenseLeftAmount+=$leftExpense;
  	if ($consolidateValidated=="ALWAYS") {
  		$this->expenseValidatedAmount=$validatedExpense;
  		if ($hasSubProjects) $this->validatedExpenseCalculated=1;
  	} else if ($consolidateValidated=="IFSET") {
  		if ($validatedExpense) {
  			$this->expenseValidatedAmount=$validatedExpense;
  			if ($hasSubProjects) $this->validatedExpenseCalculated=1;
  		}
  	}
  	$resultSaveProj=$this->save();
  	// Dispath to top element
  	if ($this->topId) {
  		self::updateSynthesis($this->topRefType, $this->topRefId);
  	}
  }
  
  public function updateExpense($doNotSave=false) {
  	$exp=new Expense();
  	$lstExp=$exp->getSqlElementsFromCriteria(array('idProject'=>$this->refId,'cancelled'=>'0'));
  	$assigned=0;
  	$real=0;
  	$planned=0;
  	$left=0;
  	foreach ($lstExp as $exp) {
  		if ($exp->plannedAmount) {
  			$assigned+=$exp->plannedAmount;
  		}
  		if ($exp->realAmount) {
  			$real+=$exp->realAmount;
  		} else {
  			if ($exp->plannedAmount) {
  				$left+=$exp->plannedAmount;
  			}
  		}
  	}
  	$planned=$real+$left;
  	$this->expenseAssignedAmount=$assigned;
  	$this->expenseLeftAmount=$left;
  	$this->expensePlannedAmount=$planned;
  	$this->expenseRealAmount=$real;
  	$this->updateTotal();
  	if (! $doNotSave) {
  		$this->simpleSave();
  		if ($this->topId) {
  			self::updateSynthesis($this->topRefType, $this->topRefId);
  		}
  	}
  }
  public function updateReserve($doNotSave=false) {
    $reserve=0;
    $risk=new Risk();
    $lstRisk=$risk->getSqlElementsFromCriteria(array('idProject'=>$this->refId,'idle'=>'0'));
    foreach ($lstRisk as $risk) {
    	if ($risk->projectReserveAmount) {
    	  $reserve+=$risk->projectReserveAmount;
    	}
    }
    $opportunity=new Opportunity();
    $lstOpportunity=$opportunity->getSqlElementsFromCriteria(array('idProject'=>$this->refId,'idle'=>'0'));
    foreach ($lstOpportunity as $opportunity) {
      if ($opportunity->projectReserveAmount) {
        $reserve-=$opportunity->projectReserveAmount;
      }
    }
    $this->reserveAmount=$reserve;
    $this->updateTotal();
    if (! $doNotSave) {
    		$this->simpleSave();
    		if ($this->topId) {
    		  self::updateSynthesis($this->topRefType, $this->topRefId);
    		}
    }
  }
  public function addTicketWork($doNotSave=false) {
    //$crit=array('idProject'=>$this->refId,'idActivity'=>null);
    $where='idProject='.$this->refId.' and idActivity is null'; $crit=null;
    $tkt=new WorkElement();
    $sum=$tkt->sumSqlElementsFromCriteria(array('realWork', 'leftWork','realCost','leftCost'), $crit, $where);
    $this->realWork+=$sum['sumrealwork'];
    $this->leftWork+=$sum['sumleftwork'];
    $this->realCost+=$sum['sumrealcost'];
    $this->leftCost+=$sum['sumleftcost'];
    $this->plannedWork=$this->realWork+$this->leftWork; // Need to be done here to refrehed
    $this->plannedCost=$this->realCost+$this->leftCost;
    //$this->realCost+=$sumCost;
    if (! $doNotSave) {
      $this->simpleSave();
      if ($this->topId) {
        self::updateSynthesis($this->topRefType, $this->topRefId);
      }
    }
  }
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return array_merge(parent::getStaticFieldsAttributes(),self::$_fieldsAttributes);
  }
  
  public function getValidationScript($colName) {
  	$colScript = parent::getValidationScript($colName);
  	if ($colName=='validatedCost' or $colName=='expenseValidatedAmount') {
	  	$colScript .= '<script type="dojo/connect" event="onChange" >';
	  	$colScript .= '  if (dijit.byId("' . get_class($this) . '_totalValidatedCost")) {';
	  	$colScript .= '    var cost=dijit.byId("' . get_class($this) . '_validatedCost").get("value");';
	  	$colScript .= '    var expense=dijit.byId("' . get_class($this) . '_expenseValidatedAmount").get("value");';
	  	$colScript .= '    if (!cost) cost=0;';
	  	$colScript .= '    if (!expense) expense=0;';
	  	$colScript .= '    var total = cost+expense;';
	  	$colScript .= '    dijit.byId("' . get_class($this) . '_totalValidatedCost").set("value",total);';
	  	$colScript .= '    formChanged();';
	  	$colScript .= '  }';
	  	$colScript .= '</script>';
  	}
  	return $colScript;
  }
  
}
?>