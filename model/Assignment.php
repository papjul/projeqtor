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
 * Assignment defines link of resources to an Activity (or else)
 */  
require_once('_securityCheck.php');
class Assignment extends SqlElement {

  // extends SqlElement, so has $id
  public $id;    // redefine $id to specify its visible place 
  public $idProject;
  public $refType;
  public $refId;
  public $idResource;
  public $idRole;
  public $comment;
  public $assignedWork;
  public $realWork;
  public $leftWork;
  public $plannedWork;
  public $notPlannedWork;
  public $rate;
  public $realStartDate;
  public $realEndDate;
  public $plannedStartDate;
  public $plannedStartFraction;
  public $plannedEndDate;
  public $plannedEndFraction;
  public $dailyCost;
  public $newDailyCost;
  public $assignedCost;
  public $realCost;
  public $leftCost;
  public $plannedCost;
  public $idle;
  public $billedWork;
  
  private static $_fieldsAttributes=array("idProject"=>"required", 
    "idResource"=>"required", 
    "refType"=>"required", 
  	"notPlannedWork"=>"hidden",
    "refId"=>"required",
      "realWork"=>"noImport",
      "plannedWork"=>"readonly,noImport",
      "notPlannedWork"=>"readonly,noImport",
      "plannedStartDate"=>"readonly,noImport",
      "plannedStartFraction"=>"hidden,noImport",
      "plannedEndDate"=>"readonly,noImport",
      "plannedEndFraction"=>"hidden,noImport",
      "realStartDate"=>"readonly,noImport",
      "realEndDate"=>"readonly,noImport",
      "assignedCost"=>"readonly,noImport",
      "realCost"=>"readonly,noImport",
      "leftCost"=>"readonly,noImport",
      "plannedCost"=>"readonly,noImport",
      "billedWork"=>"readonly,noImport",
      "dailyCost"=>"readonly,noImport",
      "newDailyCost"=>"readonly,noImport"
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

// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
  /**
   * Save object 
   * @see persistence/SqlElement#save()
   */
  public function save() {
    
  	$creation=($this->id)?false:true;
  	
    if (! $this->realWork) { $this->realWork=0; }
    // if cost has changed, update work 
    
    $this->plannedWork = $this->realWork + $this->leftWork;
    
    $r=new Resource($this->idResource);
    // If idRole not set, set to default for resource
    if (! $this->idRole) {
      $this->idRole=$r->idRole;
    }
    $newCost=$r->getActualResourceCost($this->idRole);
    $this->newDailyCost=$newCost;
    $this->leftCost=$this->leftWork*$newCost;
    $this->plannedCost = $this->realCost + $this->leftCost;
    if ($this->dailyCost==null) {
      $this->dailyCost=$newCost;
      if (! $this->idRole) {
        // search idRole found for newDailyCost
        $where="idResource=" . Sql::fmtId($this->idResource);
        $where.= " and endDate is null";
        $where.= " and cost=" . (($newCost)?$newCost:'0');
        $rc=new ResourceCost();
        $lst = $rc->getSqlElementsFromCriteria(null, false, $where, "startDate desc");
        if (count($lst)>0) {
          $this->idRole=$lst[0]->idRole;
        }
      }      
    }
    $this->assignedCost=$this->assignedWork*$this->dailyCost;
    
    if ($this->refType=='PeriodicMeeting') {
    	$this->idle=1;
    	$this->leftWork=0;
    }
    
    if (! $this->idProject) {
      if (!SqlElement::class_exists($this->refType)) return "ERROR '$this->refType' is not a valid class";
    	$refObj=new $this->refType($this->refId);
    	$this->idProject=$refObj->idProject;
    }
    // Dispatch value
    $result = parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
    
    if ($this->refType=='PeriodicMeeting') {
      $meet=new Meeting();
      $lstMeet=$meet->getSqlElementsFromCriteria(array('idPeriodicMeeting'=>$this->refId));
      foreach ($lstMeet as $meet) {
        $critArray=array('refType'=>'Meeting', 'refId'=>$meet->id, 'idResource'=>$this->idResource, 'idRole'=>$this->idRole);
        $ass=SqlElement::getSingleSqlElementFromCriteria('Assignment', $critArray);
        if (!$ass or !$ass->id) {
        	$ass->realWork=0;
            $ass->realCost=0;
        }
      	$ass->refType='Meeting';
      	$ass->refId=$meet->id;
      	$ass->idResource=$this->idResource;
      	$ass->idRole=$this->idRole;
      	$ass->idProject=$this->idProject;
        $ass->comment=$this->comment;
        $ass->assignedWork=$this->assignedWork;
        $ass->leftWork=$ass->assignedWork-$ass->realWork;
        $ass->plannedWork=$ass->assignedWork;
        $ass->rate=$this->rate;
        $ass->dailyCost=$this->dailyCost;
        $ass->assignedCost=$this->assignedCost;
        $ass->leftCost=$ass->assignedCost-$ass->realCost;
        $ass->plannedCost=$ass->assignedCost;
        $ass->idle=0;      	
        $resAss=$ass->save();
      }
    }
    
    PlanningElement::updateSynthesis($this->refType, $this->refId);
    // Recalculate indicators
    if (SqlList::getIdFromTranslatableName('Indicatorable',$this->refType)) {
        $indDef=new IndicatorDefinition();
        $crit=array('nameIndicatorable'=>$this->refType);
        $lstInd=$indDef->getSqlElementsFromCriteria($crit, false);
        if (count($lstInd)>0) {
        	$item=new $this->refType($this->refId);
	        foreach ($lstInd as $ind) {
	          $fldType='id'. $this->refType .'Type';
	          if (! $ind->idType or $ind->idType==$item->$fldType) {
	            IndicatorValue::addIndicatorValue($ind,$item);
	          }
	        }
        }
      }
    
    /*if ($limitedRate==true) {
      $result = i18n("limitedRate", array($affectation->rate)) . $result;      
    }*/
    
    // Dispatch value
    return $result;
  }
  
  public function simpleSave() {
  	$result = parent::save();
  }
  /**
   * Delete object and dispatch updates to top 
   * @see persistence/SqlElement#save()
   */
  public function delete() {    
    if ($this->refType=='PeriodicMeeting') {
      $meet=new Meeting();
      $lstMeet=$meet->getSqlElementsFromCriteria(array('idPeriodicMeeting'=>$this->refId));
      foreach ($lstMeet as $meet) {
        $critArray=array('refType'=>'Meeting', 'refId'=>$meet->id, 'idResource'=>$this->idResource, 'idRole'=>$this->idRole);
        $ass=SqlElement::getSingleSqlElementFromCriteria('Assignment', $critArray);
        if ($ass and $ass->id and ! $ass->realWork) {
        	$ass->delete();
        }
      }
    }
    $result = parent::delete();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
    // Delete planned work for the assignment
    $pw=new PlannedWork();
    $pwList=$pw->purge('idAssignment='.Sql::fmtId($this->id));
    
    // Update planning elements
    PlanningElement::updateSynthesis($this->refType, $this->refId);
    
    // Dispatch value
    return $result;
  }
  
  public function refresh() {
    $work=new Work();
    $crit=array('idAssignment'=>$this->id);
    $workList=$work->getSqlElementsFromCriteria($crit,false);
    $realWork=0;
    $realCost=0;
    $this->realStartDate=null;
    $this->realEndDate=null;
    foreach ($workList as $work) {
      $realWork+=$work->work;
      $realCost+=$work->cost;
      if ( !$this->realStartDate or $work->workDate<$this->realStartDate ) {
        $this->realStartDate=$work->workDate;
      }
      if ( !$this->realEndDate or $work->workDate>$this->realEndDate ) {
        $this->realEndDate=$work->workDate;
      }     
    }
    $this->realWork=$realWork;
    $this->realCost=$realCost;
  }
  
  public function saveWithRefresh() {
    $this->refresh();
    return $this->save();
  }

/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    if (! $this->idResource) {
      $result.='<br/>' . i18n('messageMandatory', array(i18n('colIdResource')));
    } 
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }else if($this->refType=="Meeting"){
      $elm=SqlElement::getSingleSqlElementFromCriteria("Assignment", array('refType'=>$this->refType,'refId'=>$this->refId,'idResource'=>$this->idResource));
      if($elm && $elm->id!=$this->id){
        $result.='<br/>' . i18n('messageResourceDouble');
      }
    }
    if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
  public static function insertAdministrativeLines($resourceId) {
    // Insert new assignment for all administrative activities
    $type=new ProjectType();
    $critType=array('code'=>'ADM', 'idle'=>'0');
    $lstType=$type->getSqlElementsFromCriteria($critType,false,null,null,false,true);
    foreach ($lstType as $type) {
    	$proj=new Project();
    	$critProj=array('idProjectType'=>$type->id, 'idle'=>'0');
    	$lstProj=$proj->getSqlElementsFromCriteria($critProj,false,null,null,false,true);
    	foreach ($lstProj as $proj) {
    		$acti=new Activity();
    	  $critActi=array('idProject'=>$proj->id, 'idle'=>'0');
    	  $lstActi=$acti->getSqlElementsFromCriteria($critActi,false,null,null,false,true);
    	  foreach ($lstActi as $acti) {
          $assi=new Assignment();
          $critAssi=array('refType'=>'Activity', 'refId'=>$acti->id, 'idResource'=>$resourceId);
          $lstAssi=$assi->getSqlElementsFromCriteria($critAssi,false,null,null,false,true);
          if (count($lstAssi)==0) {
          	$assi->idProject=$proj->id;
          	$assi->refType='Activity';
          	$assi->refId=$acti->id;
          	$assi->idResource=$resourceId;          	
            $assi->assignedWork=0;
            $assi->realWork=0;
            $assi->leftWork=0;
            $assi->plannedWork=0;
            $assi->notPlannedWork=0;
            $assi->rate=0;
            $assi->idle=0;
            $assi->save();
          }
    	  }
    	}
    }
  }
}
?>