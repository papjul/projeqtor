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
 * Action is establised during meeting, to define an action to be followed.
 */  
require_once('_securityCheck.php');
class ActivityMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  // List of fields that will be exposed in general user interface
  public $_sec_description;
  public $id;    // redefine $id to specify its visible place 
  public $reference;
  public $name;
  public $idActivityType;
  public $idProject;
  public $externalReference;  
  public $creationDate;
  public $lastUpdateDateTime;
  public $idUser;
  public $idContact;
  public $Origin;
  public $description;  
  public $_sec_treatment;
  public $idActivity;
  public $idStatus;
  public $idResource;  
  public $handled;
  public $handledDate;
  public $done;
  public $doneDate;
  public $idle;
  public $idleDate;
  public $cancelled;
  public $_lib_cancelled;
  public $result;
  public $_sec_productComponent;
  public $idProduct;
  public $idComponent;
  public $idTargetProductVersion;
  public $idTargetComponentVersion;
  public $_sec_Assignment;
  public $_Assignment=array();
  public $_sec_Progress;
  public $ActivityPlanningElement; // is an object
  public $isPlanningActivity;
  public $_sec_predecessor;
  public $_Dependency_Predecessor=array();
  public $_sec_successor;
  public $_Dependency_Successor=array();
  public $_sec_Link;
  public $_Link=array();
  public $_Attachment=array();
  public $_Note=array();
  
  public $_nbColMax=3;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="4%" ># ${id}</th>
    <th field="nameProject" width="9%" >${idProject}</th>
    <th field="nameActivityType" width="7%" >${idActivityType}</th>
    <th field="wbsSortable" from="ActivityPlanningElement" formatter="sortableFormatter" width="5%" >${wbs}</th>
    <th field="name" width="12%" >${name}</th>
    <th field="validatedEndDate" from="ActivityPlanningElement" width="8%" formatter="dateFormatter">${validatedDueDate}</th>
    <th field="plannedEndDate" from="ActivityPlanningElement" width="8%" formatter="dateFormatter">${plannedDueDate}</th>
    <th field="colorNameStatus" width="9%" formatter="colorNameFormatter">${idStatus}</th>
    <th field="progress" from="ActivityPlanningElement" width="5%" formatter="percentFormatter">${progress}</th>
    <th field="nameTargetProductVersion" width="8%" >${targetVersion}</th>
    <th field="nameResource" formatter="thumbName22" width="8%" >${responsible}</th>
    <th field="handled" width="4%" formatter="booleanFormatter" >${handled}</th>
    <th field="done" width="4%" formatter="booleanFormatter" >${done}</th>
    <th field="idle" width="4%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("id"=>"nobr", "reference"=>"readonly",
                                  "name"=>"required", 
                                  "idProject"=>"required",
                                  "idActivityType"=>"required",
                                  "idStatus"=>"required",
                                  "creationDate"=>"required",
                                  "handled"=>"nobr",
                                  "done"=>"nobr",
                                  "idle"=>"nobr",
                                  "idleDate"=>"nobr",
                                  "cancelled"=>"nobr",
                                  "isPlanningActivity"=>"hidden"
  );  
  
  private static $_colCaptionTransposition = array('idUser'=>'issuer', 
                                                   'idResource'=> 'responsible',
                                                   'idActivity' => 'parentActivity',
                                                   'idContact' => 'requestor',                                    
                                                   'isPlanningActivity'=>'PlanningActivity');
  
  //private static $_databaseColumnName = array('idResource'=>'idUser');
  private static $_databaseColumnName = array('idTargetProductVersion'=>'idVersion', 
      'idTargetComponentVersion'=>'idComponentVersion');
    
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
  
  /** ==========================================================================
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }
  
  /** ============================================================================
   * Return the specific colCaptionTransposition
   * @return the colCaptionTransposition
   */
  protected function getStaticColCaptionTransposition($fld=null) {
    return self::$_colCaptionTransposition;
  }

  /** ========================================================================
   * Return the specific databaseColumnName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
  // ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="idProject") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  dojo.byId("ActivityPlanningElement_wbs").value=""; ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="idActivity") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  dojo.byId("ActivityPlanningElement_wbs").value=""; ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="idActivityType") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  setDefaultPlanningMode(this.value);';
      $colScript .= '</script>';
    }
    return $colScript;
  }

/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    if ($this->id and $this->id==$this->idActivity) {
      $result.='<br/>' . i18n('errorHierarchicLoop');
    } else if ($this->ActivityPlanningElement and $this->ActivityPlanningElement->id){
    	if (trim($this->idActivity)) {
    		$parentType='Activity';
    		$parentId=$this->idActivity;
    	} else {
    		$parentType='Project';
    		$parentId=$this->idProject;
    	}
    	$result.=$this->ActivityPlanningElement->controlHierarchicLoop($parentType, $parentId);
    }
    
    if (trim($this->idActivity)) {
      $parentActivity=new Activity($this->idActivity);
      if ($parentActivity->idProject!=$this->idProject) {
    	  $result.='<br/>' . i18n('msgParentActivityInSameProject');
      }
    }
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }
    if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
  
  /** =========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */  
  
  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
    
    $oldResource=null;
    $oldIdle=null;
    $oldIdProject=null;
    $oldIdActivity=null;
    $oldTargetProductVersion=null;
    if ($this->id) {
      $old=$this->getOld();
      $oldResource=$old->idResource;
      $oldIdle=$old->idle;
      $oldIdProject=$old->idProject;
      $oldIdActivity=$old->idActivity;
      $oldTargetProductVersion=$old->idTargetProductVersion;
    }
    // #305 : need to recalculate before dispatching to PE
    $this->recalculateCheckboxes();
    $this->ActivityPlanningElement->refName=$this->name;
    $this->ActivityPlanningElement->idProject=$this->idProject;
    $this->ActivityPlanningElement->idle=$this->idle;
    $this->ActivityPlanningElement->done=$this->done;
    $this->ActivityPlanningElement->cancelled=$this->cancelled;
    if ($this->idActivity and trim($this->idActivity)!='') {
      $this->ActivityPlanningElement->topRefType='Activity';
      $this->ActivityPlanningElement->topRefId=$this->idActivity;
      $this->ActivityPlanningElement->topId=null;
    } else {
      $this->ActivityPlanningElement->topRefType='Project';
      $this->ActivityPlanningElement->topRefId=$this->idProject;
      $this->ActivityPlanningElement->topId=null;
    } 
    if (trim($this->idProject)!=trim($oldIdProject) or trim($this->idActivity)!=trim($oldIdActivity)) {
    	$this->ActivityPlanningElement->wbs=null;
    	$this->ActivityPlanningElement->wbsSortable=null;
    }
    $result = parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;
    }
    if ( $this->idResource and trim($this->idResource) != ''
      and ! trim($oldResource)
      and stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
      	// Add assignment for responsible
      $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther',
          array('idProfile' => getSessionUser()->getProfile($this->idProject),'scope' => 'assignmentEdit'));
      if ($habil and $habil->rightAccess == 1) {      
        $ass=new Assignment();
        $crit=array('idResource'=>$this->idResource, 'refType'=>'Activity', 'refId'=>$this->id);
        $lst=$ass->getSqlElementsFromCriteria($crit, false);
        if (count($lst)==0) {
  	      $ass->idProject=$this->idProject;
  	      $ass->refType='Activity';
  	      $ass->refId=$this->id;
  	      $ass->idResource=$this->idResource;
  	      $ass->assignedWork=0;
  	      $ass->realWork=0;
  	      $ass->leftWork=0;
  	      $ass->plannedWork=0;
  	      $ass->notPlannedWork=0;
  	      $ass->rate='100';
  	      $ass->save();
        }   
      }
    }

    // Change idle or idProject value => update idle and idProject for assignments
     if ( ($this->idle !=  $oldIdle ) 
       or ($this->idProject != $oldIdProject ) ) {
      // Add assignment for responsible
      $ass=new Assignment();
      $crit=array("refType"=>"Activity", "refId"=>$this->id);
      $assList=$ass->getSqlElementsFromCriteria($crit,false);
      foreach($assList as $ass) {
        $ass->idle=$this->idle;
        $ass->idProject=$this->idProject;
        $ass->save();
        // Change idProject value => update idProject for work
        // update not done to PlannedWork : new planning must be calculated
        if ($this->idProject != $oldIdProject ) {
            $work=new Work();
            $crit=array("refType"=>"Activity", "refId"=>$this->id);
            $workList=$work->getSqlElementsFromCriteria($crit,false);
            foreach($workList as $work) {
              $work->idProject=$this->idProject;
              $work->save();
            }
            $work=new PlannedWork();
            $crit=array("refType"=>"Activity", "refId"=>$this->id);
            $workList=$work->getSqlElementsFromCriteria($crit,false);
            foreach($workList as $work) {
              $work->idProject=$this->idProject;
              $work->save();
            } 
        }   
      }      
    }   
    if ($this->idProject != $oldIdProject ) {
    	$lstElt=array('Activity','Ticket','Milestone','PeriodicMeeting','Meeting','TestSession');
    	foreach ($lstElt as $elt) {
    		$eltObj=new $elt();
    		$crit=array('idActivity'=>$this->id);
    		$lst=$eltObj->getSqlElementsFromCriteria($crit, false,null,null,true);
    		foreach($lst as $obj) {
          $objBis=new $elt($obj->id);   			
    			$objBis->idProject=$this->idProject;
    			$tmpRes=$objBis->save();
    		}
    	}
    }
    if ($oldTargetProductVersion!=$this->idTargetProductVersion) {
    	$vers=new Version($this->idTargetProductVersion);
    	$idProduct=($vers->idProduct)?$vers->idProduct:null;
    	$ticket=new Ticket();
    	$ticketList=$ticket->getSqlElementsFromCriteria(array('idActivity'=>$this->id));
    	foreach ($ticketList as $ticket) {
    		$ticket->idTargetProductVersion=$this->idTargetProductVersion;
    		if ($idProduct) {$ticket->idProduct=$idProduct;}
    		$ticket->save();
    	}
    }
    return $result;
  }

}
?>