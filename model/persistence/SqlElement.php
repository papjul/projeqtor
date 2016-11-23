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

/** ===========================================================================**********
 * Abstract class defining all methods to interact with database,
 * using Sql class.
 * Give public visibility to elementary methods (save, delete, copy, ...)
 * and constructor.
 */
if (file_exists('../_securityCheck.php')) include_once('../_securityCheck.php');
abstract class SqlElement {
	// List of fields that will be exposed in general user interface
	public $id; // every SqlElement have an id !!!

	private static $staticCostVisibility=null;
	private static $staticWorkVisibility=null;
  private static $staticDeleteConfirmed=false;
  private static $staticSaveConfirmed=false;
  
	// Store the layout of the different object classes
	private static $_tablesFormatList=array();

	// Define the layout that will be used for lists
	private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%"># ${id}</th>
    <th field="name" width="85%">${name}</th> 
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';

	// Define the specific field attributes
	private static $_fieldsAttributes=array("name"=>"required");
	private static $_defaultValues=array();

	// Management of cache for queries : cache is only valid during current script
	public static $_cachedQuery=array('Habilitation'=>array(),'Menu'=>array(),'PluginTriggeredEvent'=>array(), 'Plugin'=>array());
	
	// Management of extraHiddenFileds per type
	private static $_extraHiddenFields=null;
	
	// All dependencies between objects :
	//    control => sub-object must not exist to allow deletion
	//    cascade => sub-objects are automaticaly deleted
	//    confirm => confirmation will be requested
  private static $_relationShip=array(
    "AccessProfile" =>      array("AccessRight"=>"cascade"),
    "AccessScopeRead" =>    array("AccessProfile"=>"controlStrict"),
    "AccessScopeCreate" =>  array("AccessProfile"=>"controlStrict"),
    "AccessScopeUpdate" =>  array("AccessProfile"=>"controlStrict"),
    "AccessScopeDelete" =>  array("AccessProfile"=>"controlStrict"),
    "Assignment" =>         array("PlannedWork"=>"cascade",
	     				                    "Work"=>"controlStrict"),
    "Action" =>             array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "ActionType" =>         array("Action"=>"controlStrict"),
    "Activity" =>           array("Activity"=>"confirm", 
                                  "Assignment"=>"confirm",
                                  "Attachment"=>"cascade",
                                  "Dependency"=>"cascade",
                                  "Link"=>"cascade",
                                  "Milestone"=>"confirm",
                                  "Note"=>"cascade",
                                  "PlannedWork"=>"cascade",
                                  "Ticket"=>"control",),
    "ActivityType" =>       array("Activity"=>"controlStrict"),
    "Bill" =>               array("BillLine"=>"confirm",
                                  "Note"=>"cascade", ),
    "BillType" =>           array("Bill"=>"controlStrict"),
    "CallForTender"=>       array("Tender"=>"controlStrict",
                                  "TenderEvaluationCriteria"=>"cascade"),
    "CallForTenderType"=>   array("CallForTender"=>"controlStrict"),
    "CalendarDefinition" => array("Calendar"=>"cascade",
                                  "Resource"=>"controlStrict"),
    "Checklist" =>	        array("ChecklistLine"=>"cascade"),
    "ChecklistDefinition" => array("Checklist"=>"control",
                                  "ChecklistDefinitionLine"=>"cascade"),
    "ClientType" =>         array("Client" => "controlStrict"),
    "Command" =>            array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "CommandType" =>        array("Command"=>"controlStrict"),
    "Component" =>          array("ProductStructure"=>"cascade",
                                  "ComponentVersion"=>"control"),   
    "ComponentVersion" =>   array("Requirement"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control",
                                  "Ticket"=>"control"),
    "Contact" =>            array("Activity"=>"controlStrict",
                                  "Affectation"=>"control",
                                  "Bill"=>"controlStrict",
                                  "Product"=>"controlStrict",
                                  "Project"=>"controlStrict",
                                  "Tender"=>"ControlStrict",
                                  "Ticket"=>"controlStrict",
                                  "Version"=>"controlStrict"),
    "ContextType" =>        array("Context" => "controlStrict"),
    "Client" =>             array("Project"=>"control"),
    "Criticality" =>        array("Issue"=> "controlStrict",
                                  "Opportunity"=> "controlStrict",
                                  "Requirement"=>"controlStrict",
                                  "Risk"=>"controlStrict",
                                  "Ticket"=>"controlStrict"),
    "Decision" =>           array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "DecisionType" =>       array("Decision"=>"controlStrict"),
    "Document" =>           array("Approver"=>"control",
                                  "DocumentVersion"=>"control",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "DocumentVersion" =>    array("Approver"=>"cascade"),
    "DocumentDirectory" =>  array("Document"=>"control",
                                  "DocumentDirectory"=>"control"),
    "DocumentType" =>       array("Document" => "controlStrict"),
    "Efficiency" =>         array("Action"=> "controlStrict"),
    "ExpenseDetailType" =>  array("ExpenseDetail" => "controlStrict"),
    "Feasibility" =>        array("Requirement"=>"controlStrict"),
    "Filter" =>             array("FilterCriteria"=>"cascade"),
    "Health" =>             array("Project"=> "controlStrict"),
    "IndividualExpenseType" => array("IndividualExpense" => "controlStrict"),
    //"InvoiceType" =>        array("Invoice" => "controlStrict"),
    "Issue" =>              array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "IssueType" =>          array("Issue"=>"controlStrict"),
    "Likelihood" =>         array("Opportunity"=> "controlStrict",
                                  "Risk"=>"controlStrict"),
    "Meeting" =>            array("Assignment"=>"cascade",
                                  "Attachment"=>"cascade",
                                  "Dependency"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade",
                                  "PlannedWork"=>"cascade"),
    "MeetingType" =>        array("Meeting"=>"controlStrict",
                                  "PeriodicMeeting"=>"controlStrict"),
    "Menu" =>               array("AccessRight"=>"cascade"),
    "MessageType" =>        array("Message"=>"controlStrict"),
    "Milestone" =>          array("Attachment"=>"cascade",
                                  "Dependency"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "MilestoneType" =>      array("Milestone"=>"controlStrict"),
    "OverallProgress" => 	  array("Project"=>"controlStrict"),
    "OpportunityType" =>    array("Opportunity" => "controlStrict"),
    //"PaymentType" => array("Payment" => "controlStrict"),
    "PeriodicMeeting" =>    array("Assignment"=>"cascade",
                                  "Meeting"=>"cascade",
                                  "Note"=>"cascade"),
    "Priority" =>           array("Action"=> "controlStrict",
                                  "Issue"=>"controlStrict", 
                                  "Opportunity"=> "controlStrict",
                                  "Risk"=> "controlStrict",
                                  "TestCase"=> "controlStrict",
                                  "Ticket"=>"controlStrict"),
    "Profile" =>            array("AccessRight"=>"cascade",
                                  "Habilitation"=>"cascade",
                                  "Message"=>"cascade",
                                  "Resource"=>"controlStrict",
                                  "User"=>"controlStrict"),
    "ProjectExpenseType" => array("ProjectExpense" => "controlStrict"),
    "ProjectType" =>        array("Project"=>"controlStrict"), 
    "Product" =>            array("Component"=>"cascade",
                                  "Requirement"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control",
                                  "ProductVersion"=>"control"),
    "ProductVersion" =>     array("Requirement"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control",
                                  "VersionProject"=>"cascade",
                                  "Ticket"=>"control",
                                  "Activity"=>"control"),
    "Project" =>            array("Action"=>"control",
                                  "Activity"=>"confirm",
                                  "Affectation"=>"confirm",
                                  "Assignment"=>"cascade",
                                  "Attachment"=>"cascade",
                                  "Bill"=>"control",
                                  "CallForTender"=>"control",
                                  "Command"=>"control",
                                  "Decision"=>"control",
                                  "Dependency"=>"cascade",
                                  "Document"=>"control",
                                  "Issue"=>"control",
                                  "IndividualExpense"=>"control",
                                  "Link"=>"cascade",
                                  "Meeting"=>"control",
                                  "Message"=>"cascade",
                                  "Milestone"=>"confirm",
                                  "Note"=>"cascade",
                                  "Opportunity"=>"control",
                                  "Parameter"=>"cascade",
                                  "PlannedWork"=>"cascade", 
                                  "Project"=>"confirm", 
                                  "ProjectExpense"=>"control",
                                  "Requirement"=>"control",
                                  "Risk"=>"control", 
                                  "Question"=>"control",
                                  "Quotation"=>"control",
                                  "Term"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control",
                                  "Ticket"=>"control",
                                  "VersionProject"=>"cascade",
                                  "Work"=>"control"),
    "Provider" =>           array("ProjectExpense"=>"controlStrict",
                                  "Tender"=>"ControlStrict"),
    "Quality" =>            array("Project"=> "controlStrict"),
    "Question" =>           array("Link"=>"cascade"),
    "QuestionType" =>       array("Question"=>"controlStrict"),
    "Quotation"=>           array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "QuotationType" =>      array("Quotation"=>"controlStrict"),    
    "Recipient" =>          array("Bill"=>"control",
                                  "Project"=>"controlStrict"),
    "RequirementType" =>    array("Requirement"=>"controlStrict"),
    "Requirement" =>        array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade",
                                  "Requirement"=>"control"),
    "Resolution" =>         array("Ticket"=>"controlStrict"),
    "Resource" =>           array("Action"=>"controlStrict", 
                                  "Activity"=>"controlStrict",
                                  "Affectation"=>"control",
                                  "Assignment"=>"control",
                                  "CallForTender"=>"controlStrict",
                                  "Decision"=>"controlStrict",
                                  "Issue"=>"controlStrict",
                                  "Meeting"=>"controlStrict",
                                  "Milestone"=>"controlStrict", 
                                  "Question"=>"controlStrict",
                                  "Requirement"=>"controlStrict",
                                  "ResourceCost"=>"cascade",
                                  "Risk"=>"controlStrict", 
                                  "Ticket"=>"controlStrict",
                                  "Tender"=>"controlStrict",
                                  "TestCase"=>"controlStrict",
                                  "TestSession"=>"controlStrict",
                                  "Work"=>"controlStrict"),
    "Risk" =>               array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade"),
    "RiskLevel" =>          array("Requirement"=>"controlStrict"),
    "RiskType" =>           array("Risk"=>"controlStrict"),
    "Role" =>               array("Affectation"=>"controlStrict", 
                                  "Assignment"=>"controlStrict",
                                  "Resource"=>"controlStrict",
                                  "ResourceCost"=>"controlStrict"),
    "Severity" =>           array("Opportunity"=>"controlStrict",
                                  "Risk"=>"controlStrict"),
    "Status" =>             array("Action"=>"controlStrict", 
                                  "Activity"=>"controlStrict",
                                  "Bill"=> "controlStrict",
                                  "Command"=>"controlStrict",
                                  "Decision"=>"controlStrict",
                                  "Document"=>"controlStrict",
                                  "DocumentVersion"=>"controlStrict",
                                  "Expense"=> "controlStrict",
                                  "IndividualExpense"=> "controlStrict",
                                  "Issue"=>"controlStrict",
                                  "Mail"=> "controlStrict",
                                  "Meeting"=>"controlStrict",
                                  "Milestone"=>"controlStrict", 
                                  "Opportunity"=>"controlStrict",
                                  "Project"=>"controlStrict",
                                  "ProjectExpense"=> "controlStrict",
                                  "Question"=>"controlStrict",
                                  "Quotation"=>"controlStrict",
                                  "Requirement"=>"controlStrict",
                                  "Risk"=>"controlStrict", 
                                  "StatusMail"=>"controlStrict",
                                  "TestCase"=>"controlStrict",
                                  "TestSession"=>"controlStrict",
                                  "Ticket"=>"controlStrict",
                                  "WorkflowStatus"=>"cascade"),
    "Team" =>               array("Resource"=>"control"),
    "Tender"=>              array("TenderEvaluation"=>"cascade"),
    "TenderStatus"=>        array("Tender"=>"control"),
    "TenderType"=>          array("Tender"=>"control"),
    "Term" =>               array("Dependency"=>"cascade"),
    "TestCase" =>           array("TestCase"=>"control",
                                  "TestCaseRun"=>"control" ),
    "TestCaseType" =>       array("TestCase"=>"controlStrict"),
    "TestSession" =>        array("Assignment"=>"confirm",
                                  "Attachment"=>"cascade",
                                  "Dependency"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade",
                                  "PlannedWork"=>"cascade",
                                  "TestCaseRun"=>"cascade",),
    "TestSessionType" =>    array("TestSession"=>"controlStrict"),
    "Ticket" =>             array("Attachment"=>"cascade",
                                  "Link"=>"cascade",
                                  "Note"=>"cascade",
                                  "Ticket"=>"control",
                                  "Work"=>"control"),
    "TicketType" =>         array("Ticket"=>"controlStrict"),
    "Trend" =>              array("Project"=> "controlStrict"),
    "Urgency" =>            array("Delay"=> "controlStrict",
                                  "Requirement"=>"controlStrict",
                                  "Ticket"=>"controlStrict",
                                  "TicketDelay"=> "controlStrict"),
    "User" =>               array("Action"=>"controlStrict", 
                                  "Activity"=>"controlStrict",
                                  "Affectation"=>"control", 
                                  "Attachment"=>"control",
                                  "Command"=>"controlStrict",
                                  "Decision"=>"controlStrict",
                                  "Issue"=>"controlStrict",
                                  "Meeting"=>"controlStrict",
                                  "Message"=>"control",
                                  "Milestone"=>"controlStrict",
                                  "Note"=>"control",
                                  "Opportunity"=>"controlStrict",
                                  "Parameter"=>"cascade", 
                                  "Project"=>"controlStrict", 
                                  "Question"=>"controlStrict",
                                  "Quotation"=>"controlStrict",
                                  "Requirement"=>"controlStrict",
                                  "Risk"=>"controlStrict", 
                                  "TestCase"=>"controlStrict",
                                  "TestSession"=>"controlStrict",
                                  "Ticket"=>"controlStrict"),
    "Version" =>            array("Requirement"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control",
                                  "VersionProject"=>"cascade"),
    //"VersioningType" =>     array("Versioning" => "controlStrict"),
    "WorkElement" =>        array("Work"=>"cascade"),
    "Workflow" =>           array("ActionType"=>"controlStrict", 
                                  "ActivityType"=>"controlStrict", 
                                  "BillType"=>"controlStrict",
                                  "ClientType"=>"controlStrict",
                                  "CommandType"=>"controlStrict",
                                  //"ContextType"=>"controlStrict",
                                  "DecisionType"=>"controlStrict",
                                  "DocumentType"=>"controlStrict",
                                  //"ExpenseDetailType"=>"controlStrict",
                                  "IndividualExpenseType"=>"controlStrict",
                                  "InvoiceType"=>"controlStrict",
                                  "IssueType"=>"controlStrict",
                                  "TicketType"=>"controlStrict", 
                                  "MeetingType"=>"controlStrict",
                                  "MessageType"=>"controlStrict",
                                  "OpportunityType"=>"controlStrict",
                                  "PaymentType"=>"controlStrict",
                                  "ProjectExpenseType"=>"controlStrict",
                                  "ProjectType"=>"controlStrict",
                                  "QuestionType"=>"controlStrict",
                                  "QuotationType"=>"controlStrict",
                                  "RequirementType"=>"controlStrict",
                                  "MilestoneType"=>"controlStrict", 
                                  "RiskType"=>"controlStrict", 
                                  "TestCaseType"=>"controlStrict",
                                  "TestSessionType"=>"controlStrict",
                                  "VersioningType"=>"controlStrict",
                                  "WorkflowStatus"=>"cascade")
	);
	private static $_closeRelationShip=array(
    "AccessScopeRead" =>    array("AccessProfile"=>"control"),
    "AccessScopeCreate" =>  array("AccessProfile"=>"control"),
    "AccessScopeUpdate" =>  array("AccessProfile"=>"control"),
    "AccessScopeDelete" =>  array("AccessProfile"=>"control"),
    "Activity" =>           array("Milestone"=>"control", 
                                  "Activity"=>"control", 
                                  "Ticket"=>"control",
                                  "Assignment"=>"cascade"),
    "Document" =>           array("DocumentVersion"=>"cascade"),
    "DocumentDirectory" =>  array("Document"=>"control",
                                  "DocumentDirectory"=>"control"),
    "Product" =>            array("Version"=>"control",
                                  "Requirement"=>"confirm",
                                  "TestCase"=>"confirm",
                                  "TestSession"=>"control"),
    "Project" =>            array("Action"=>"confirm",
                                  "Activity"=>"control",
                                  "Affectation"=>"cascade",
                                  "CallForTender"=>"control",
    		                          "Command"=>"control",
                                  "Document"=>"confirm",
                                  "Issue"=>"confirm",
                                  "IndividualExpense"=>"confirm",
                                  "ProjectExpense"=>"confirm",
                                  "Term"=>"confirm",
                                  "Bill"=>"confirm",
                                  "Milestone"=>"confirm",
                                  "Project"=>"control", 
                                  "Risk"=>"confirm", 
                                  "Ticket"=>"control",
                                  "Decision"=>"confirm",
                                  "Meeting"=>"confirm",
    		                          "Opportunity"=>"confirm",
    		                          "PeriodicMeeting"=>"confirm",
                                  "VersionProject"=>"cascade",
                                  "Question"=>"confirm",
    		                          "Quotation"=>"confirm",
                                  "Requirement"=>"confirm",
                                  "Tender"=>"control",
                                  "TestCase"=>"confirm",
                                  "TestSession"=>"confirm"),
    "Requirement" =>        array("Requirement"=>"control"),
    "Resource" =>           array("Action"=>"control", 
                                  "Activity"=>"control",
                                  "Affectation"=>"cascade",
                                  "Assignment"=>"cascade",
                                  "Issue"=>"control",
                                  "Milestone"=>"control", 
                                  "Risk"=>"control", 
                                  "Ticket"=>"control",
                                  "Decision"=>"control",
                                  "Meeting"=>"control",
                                  "Question"=>"control",
                                  "Requirement"=>"control",
                                  "TestCase"=>"control",
                                  "TestSession"=>"control"),
    "TestCase" =>           array("TestCase"=>"confirm",
                                  "TestCaseRun"=>"cascade" ),
    "TestSession" =>        array("TestCaseRun"=>"cascade" ),
    "User" =>               array("Affectation"=>"cascade"),
    "Version" =>            array("VersionProject"=>"cascade",
                                  "TestSession"=>"confirm")
  );

	/** =========================================================================
	 * Constructor. Protected because this class must be extended.
	 * @param $id the id of the object in the database (null if not stored yet)
	 * @return void
	 */
	protected function __construct($id = NULL, $withoutDependentObjects=false) {
		if (trim($id) and ! is_numeric($id)) {
			$class=get_class($this);
			traceHack("SqlElement->_construct : id '$id' is not numeric for class $class");
			return;
		} 
		$this->id=$id;
		if ($this->id=='') {
			$this->id=null;
		}
		$this->getSqlElement($withoutDependentObjects);
	}

	/** =========================================================================
	 * Destructor
	 * @return void
	 */
	protected function __destruct() {
	}

	// ============================================================================**********
	// UPDATE FUNCTIONS
	// ============================================================================**********

	/** =========================================================================
	 * Give public visibility to the saveSqlElement action
	 * @param force to avoid controls and force saving even if controls are false
	 * @return message including definition of html hiddenfields to be used
	 */
	public function save() {
		if (isset($this->_onlyCallSpecificSaveFunction) and $this->_onlyCallSpecificSaveFunction==true) return;
		if (!property_exists($this, '_onlyCallSpecificSaveFunction') or !$this->_onlyCallSpecificSaveFunction) {
  		// PlugIn Management 
  		$lstPluginEvt=Plugin::getEventScripts('beforeSave',get_class($this));
  		foreach ($lstPluginEvt as $script) {
  		  require $script; // execute code
  		}
		}
		$result=$this->saveSqlElement();
		if (!property_exists($this, '_onlyCallSpecificSaveFunction') or !$this->_onlyCallSpecificSaveFunction) {
		  // PlugIn Management
  		$lstPluginEvt=Plugin::getEventScripts('afterSave',get_class($this));
  		foreach ($lstPluginEvt as $script) {
  		  require $script; // execute code
  		}
		}
		return $result;
	}

	public function insert() { // Specific function to force insert with a defined id - Reserved to Import fonction
		$this->_onlyCallSpecificSaveFunction=true;
		// PlugIn Management
		$lstPluginEvt=Plugin::getEventScripts('beforeSave',get_class($this));
		foreach ($lstPluginEvt as $script) {
		  require $script; // execute code
		}
		$this->save(); // To force the update of fields calculated in the save function ...
		$this->_onlyCallSpecificSaveFunction=false;
		$result=$this->saveSqlElement(false, false, true);
		// PlugIn Management
		$lstPluginEvt=Plugin::getEventScripts('afterSave',get_class($this));
		foreach ($lstPluginEvt as $script) {
		  require $script; // execute code
		}
		return $result;
	}

	public function saveForced($withoutDependencies=false) {
	  // PlugIn Management
	  $lstPluginEvt=Plugin::getEventScripts('beforeSave',get_class($this));
	  foreach ($lstPluginEvt as $script) {
	    require $script; // execute code
	  }
		$result=$this->saveSqlElement(true,$withoutDependencies);
		// PlugIn Management
		$lstPluginEvt=Plugin::getEventScripts('afterSave',get_class($this));
		foreach ($lstPluginEvt as $script) {
		  require $script; // execute code
		}
		return $result;
	}

	/** =========================================================================
	 * Give public visibility to the purgeSqlElement action
	 * @return message including definition of html hiddenfields to be used
	 */
	public function purge($clause) {
		return $this->purgeSqlElement($clause);
	}

	/** =========================================================================
	 * Give public visibility to the closeSqlElement action
	 * @return message including definition of html hiddenfields to be used
	 */
	public function close($clause) {
    return $this->closeSqlElement($clause);
	}

	/** =========================================================================
	 * Give public visibility to the deleteSqlElement action
	 * @return message including definition of html hiddenfields to be used
	 */
	public function delete() {
		// PlugIn Management
	  $list=Plugin::getEventScripts('beforeDelete',get_class($this));
	  foreach ($list as $script) {
	    require $script; // execute code
	  }
		$result=$this->deleteSqlElement();
		// PlugIn Management
		$list=Plugin::getEventScripts('afterDelete',get_class($this));
		foreach ($list as $script) {
		  require $script; // execute code
		}
		return $result;
	}

	/** =========================================================================
	 * Give public visibility to the copySqlElement action
	 * @return the new object
	 */
	public function copy() {
		return $this->copySqlElement();
	}

	public function copyTo ($newClass, $newType, $newName, $setOrigin, $withNotes, $withAttachments,$withLinks, $withAssignments=false, $withAffectations=false, $toProject=null, $toActivity=null, $copyToWithResult=false) {
		return $this->copySqlElementTo($newClass, $newType, $newName, $setOrigin, $withNotes, $withAttachments,$withLinks, $withAssignments, $withAffectations, $toProject, $toActivity, $copyToWithResult);
	}
	/** =========================================================================
	 * Save an object to the database
	 * @return void
	 */
	private function saveSqlElement($force=false,$withoutDependencies=false,$forceInsert=false) {
		//traceLog("saveSqlElement(" . get_class($this) . "#$this->id)" 
		//  . ((SqlElement::is_subclass_of($this,'PlanningElement'))?"  => $this->refType #$this->refId":''));
		// if (get_class($this)=='History')  traceLog("    => $this->colName : '$this->oldValue'->'$this->newValue'");
		// #305
		$this->recalculateCheckboxes();
		// select operation to be executed
		if ($force) {
			$control="OK";
		} else {
			$control=$this->control();			
			$class=get_class($this);
			if ( ($control=='OK' or strpos($control,'id="confirmControl" value="save"')>0 )
			and property_exists($class, $class.'PlanningElement')) {
				$pe=$class.'PlanningElement';
				$controlPe=$this->$pe->control();
				if ($controlPe!='OK') {
					$control=$controlPe;
				}
			}
		}
		if ($control=="OK") {
			//$old=new Project();
			if (property_exists($this, 'idStatus') or property_exists($this,'reference') or property_exists($this,'idResource')
			or property_exists($this, 'description') or property_exists($this, 'result')) {
				$class=get_class($this);
				$old=new $class($this->id);
			}
			$statusChanged=false;
			$responsibleChanged=false;
			$descriptionChange=false;
			$resultChange=false;
			if (property_exists($this,'reference') and isset($old)) {
				$this->setReference(false, $old);
			}
			if (property_exists($this, 'idResource') and ! trim($this->idResource)) {
				$this->setDefaultResponsible();
			}
			if (! $this->id and $this instanceof PlanningElement) { // For planning element , check that not existing yet
				$critPe=array('refType'=>$this->refType, 'refId'=>$this->refId);
				$pe=SqlElement::getSingleSqlElementFromCriteria('PlanningElement', $critPe);
				if ($pe->id) {$this->id=$pe->id;}
			}
			if ($this->id != null  and !$forceInsert) {
				if (property_exists($this, 'idStatus')) {
					if ($this->idStatus and isset($old)) {
						if ($old->idStatus!=$this->idStatus) {
							$statusChanged=true;
						}
					}
				}
				$newItem=false;
				$returnValue=$this->updateSqlElement($force,$withoutDependencies);
			} else {
				if (property_exists($this, 'idStatus')) {
					$statusChanged=true;
				}
				$newItem=true;
				$returnValue=$this->insertSqlElement($forceInsert);
			}
			if (property_exists($this,'idResource') and ! $newItem and isset($old)) {
				if (trim($this->idResource) and trim($this->idResource)!=trim($old->idResource)) {
					$responsibleChanged=true;
				}
			}
			if (property_exists($this,'description') and ! $newItem and isset($old)) {
				if ($this->description!=$old->description) {
					$descriptionChange=true;
				}
			}
			if (property_exists($this,'result') and ! $newItem and isset($old)) {
				if ($this->result!=$old->result) {
					$resultChange=true;
				}
			}
			//if (($statusChanged or $responsibleChanged) and stripos($returnValue,'id="lastOperationStatus" value="OK"')>0 ) {
			if ( stripos($returnValue,'id="lastOperationStatus" value="OK"')>0 ) {
				$mailResult=$this->sendMailIfMailable($newItem, $statusChanged, false, $responsibleChanged,false,false,false,$descriptionChange, $resultChange,false,false,true);
				if ($mailResult) {
					$returnValue=str_replace('${mailMsg}',' - ' . i18n('mailSent'),$returnValue);
				} else {
					$returnValue=str_replace('${mailMsg}','',$returnValue);
				}
			} else {
				$returnValue=str_replace('${mailMsg}','',$returnValue);
			}
			// indicators
			if (SqlList::getIdFromTranslatableName('Indicatorable',get_class($this))) {
				$indDef=new IndicatorDefinition();
				$crit=array('nameIndicatorable'=>get_class($this),'idle'=>'0');
				$lstInd=$indDef->getSqlElementsFromCriteria($crit, false);
				foreach ($lstInd as $ind) {
					$fldType='id'.((get_class($this)=='TicketSimple')?'Ticket':get_class($this)).'Type';
					if (! $ind->idType or $ind->idType==$this->$fldType) {
						IndicatorValue::addIndicatorValue($ind,$this);
					}
				}
			}				
			if (property_exists($this, 'idle') and $this->idle) {
				$this->dispatchClose();
			}
			return $returnValue;
		} else {
			// errors on control => don't save, display error message
			if ( strpos($control,'id="confirmControl" value="save"')>0 ) {
				$returnValue='<b>' . i18n('messageConfirmationNeeded') . '</b><br/>' . $control;
				$returnValue .= '<input type="hidden" id="lastOperationStatus" value="CONFIRM" />';
			} else {
				$returnValue='<b>' . i18n('messageInvalidControls') . '</b><br/>' . $control;
				$returnValue .= '<input type="hidden" id="lastOperationStatus" value="INVALID" />';
			}
			$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
			$returnValue .= '<input type="hidden" id="lastOperation" value="control" />';
			return $returnValue;
		}
	}

	private function dispatchClose() {		
		if (property_exists($this,'idle') and $this->idle) {
			$relationShip=self::$_closeRelationShip;
			if (array_key_exists(get_class($this),$relationShip)) {
				$objects='';
				$error=false;
				foreach ( $relationShip[get_class($this)] as $object=>$mode) {
					if (($mode=='cascade' or $mode=='confirm') and property_exists($object,'idle')) {
						$where=null;
						$obj=new $object();
						$crit=array('id' . get_class($this) => $this->id, 'idle'=>'0');
						if (property_exists($obj, 'refType') and property_exists($obj,'refId')) {
						  if (property_exists($obj,'id' . get_class($this))) {
						    $crit=null;
						    $where="(id".get_class($this)."=".$this->id." or (refType='".get_class($this)."' and refId=".$this->id.")) and idle=0";
						  } else {
						    $crit=array("refType"=>get_class($this), "refId"=>$this->id, "idle"=>'0');
						  }
						}
						if ($object=="Dependency") {
							$crit=null;
							$where="idle=0 and ((predecessorRefType='" . get_class($this) . "' and predecessorRefId=" . $this->id .")"
									. " or (successorRefType='" . get_class($this) . "' and successorRefId=" . $this->id ."))";
						}
						if ($object=="Link") {
							$crit=null;
							$where="idle=0 and ((ref1Type='" . get_class($this) . "' and ref1Id=" . Sql::fmtId($this->id) .")"
									. " or (ref2Type='" . get_class($this) . "' and ref2Id=" . Sql::fmtId($this->id) ."))";
						}				
						$list=$obj->getSqlElementsFromCriteria($crit, false, $where);
						foreach ($list as $o) {					
							$o->idle=1;
							if (property_exists($o,'idleDate') and ! trim($o->idleDate)) {
								$o->idleDate=date('Y-m-d');
							}
							if (property_exists($o,'idleDateTime') and ! trim($o->idleDateTime)) {
								$o->idleDateTime=date('Y-m-d H:i:s');
							}
							$resO=$o->save();
						}
					}
				}
				if ($objects!="") {
					if ($error) {
						$result.="<br/>" . i18n("errorControlClose") . $objects;
					} else {
						$result.='<input type="hidden" id="confirmControl" value="save" /><br/>' . i18n("confirmControlSave") . $objects;
					}
				}
			}
		}
	}
	
	
	
	/** =========================================================================
	 * Save an object to the database : new object
	 * @return void
	 */
	private function insertSqlElement($forceInsert=false) {
		if (get_class($this)=='Origin') {
			if (! $this->originId or ! $this->originType) {
				return;
			}
		}
		$depedantObjects=array();
		$returnStatus="OK";
		$objectClass = get_class($this);
		$query="insert into " . $this->getDatabaseTableName();
		$queryColumns="";
		$queryValues="";
		// initialize object definition criteria
		$databaseCriteriaList=$this->getDatabaseCriteria();
		foreach ($databaseCriteriaList as $col_name => $col_value) {
			$dataType = $this->getDataType($col_name);
			$dataLength = $this->getDataLength($col_name);
			$attribute= $this->getFieldAttributes($col_name);
			if (strpos($attribute,'calculated')===false) {
				if ($dataType=='int' and $dataLength==1) {
					if ($col_value==NULL or $col_value=="") {
						$col_value='0';
					}
				}
				if ($col_value != NULL and $col_value != '' and $col_value != ' ' and ($col_name != 'id' or $forceInsert)) {
					if ($queryColumns != "") {
						$queryColumns.=", ";
						$queryValues.=", ";
					}
					$queryColumns .= $this->getDatabaseColumnName($col_name);
					$queryValues .= Sql::str($col_value, $objectClass);
				}
			}
		}
		if (Sql::isPgsql()) {$queryColumns=strtolower($queryColumns);}
		if (property_exists($this, 'lastUpdateDateTime')) { // Initialize lastUpdateDateTime (for tickets)
		  $this->lastUpdateDateTime=date('Y-m-d H:i:s');
		}
		// get all data
		foreach($this as $col_name => $col_value) {
			$attribute= $this->getFieldAttributes($col_name);
			if (strpos($attribute,'calculated')===false) {
				if (substr($col_name,0,1)=="_") {
					// not a fiels, just for presentation purpose
				} else if (ucfirst($col_name) == $col_name) {
					// if property is an object, store it to save it at the end of script
					$depedantObjects[$col_name]=($this->$col_name);
				} else if (array_key_exists($col_name, $databaseCriteriaList) ) {
					// Do not overwrite the default value from databaseCriteria, and do not double-set in insert clause
				} else {
					$dataType = $this->getDataType($col_name);
					$dataLength = $this->getDataLength($col_name);
					if ($dataType=='int' and $dataLength==1) {
						if ($col_value==NULL or $col_value=="") {
							$col_value='0';
						}
					}
					if ($dataLength>4000 and getEditorType()=='text') {
					  $col_value=nl2br($col_value);
					}
					if ($col_value != NULL and $col_value != '' and $col_value != ' '
					and ($col_name != 'id' or $forceInsert)
					and strpos($queryColumns, ' '. $this->getDatabaseColumnName($col_name) . ' ')===false ) {
						if ($queryColumns != "") {
							$queryColumns.=",";
							$queryValues.=", ";
						}
						$queryColumns .= ' ' . $this->getDatabaseColumnName($col_name) . ' ';
						$queryValues .=  Sql::str($col_value, $objectClass);
					}
				}
			}
		}
		$query.=" ($queryColumns) values ($queryValues)";
		// execute request
		$result = Sql::query($query);
		if (!$result) {
			$returnStatus="ERROR";
		}
		// save history
		$newId= Sql::$lastQueryNewid;
		$this->id=$newId;
		if ($returnStatus!="ERROR" and ! property_exists($this,'_noHistory') ) {
			$result = History::store($this, $objectClass,$newId,'insert');
			if (!$result) {$returnStatus="ERROR";}
		}
		// save depedant elements (properties that are objects)
		if ($returnStatus!="ERROR") {
			$returnStatus=$this->saveDependantObjects($depedantObjects,$returnStatus);
		}
		// Prepare return data
		if ($returnStatus!="ERROR") {
			$returnValue=i18n(get_class($this)) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultInserted');
		} else {
			$returnValue=Sql::$lastQueryErrorMessage;
		}
		if ($returnStatus=="OK") {
			$returnValue .= '${mailMsg}';
		}
		$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
		$returnValue .= '<input type="hidden" id="lastOperation" value="insert" />';
		$returnValue .= '<input type="hidden" id="lastOperationStatus" value="' . $returnStatus . '" />';
		return $returnValue;
	}

	/** 
	 * Get old values (stored in session) to : 
	 *  1) build the smallest query 
	 *  2) save change history
	 * @param string $objectClass
	 * @param string $force
	 * @return Ambigous <NULL, unknown>
	 */
	public static function getCurrentObject ($objectClass=null, $objectId=null, $throwError=false, $force=false, $isComboDetail=false) {
	  $oldObject = null;
	  if ($force) {
	    if ($objectClass) {
	      return new $objectClass($objectId);
	    } else {
	      return null;
	    }
	  }
	  if ( isset($_REQUEST['directAccessIndex'])) {
	    if (isset($_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex'].(($isComboDetail)?'_comboDetail':'')]) ) {
  	    $testObject=$_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex'].(($isComboDetail)?'_comboDetail':'')];
  	    if (!$objectClass or get_class($testObject)==$objectClass) {
  	      $oldObject=$testObject;
  	    } else if ($throwError) {
  	      throwError('currentObject ('.get_class($testObject).' #'.$obj->id.') is not of the expectec class ('.$objectClass.')');
  	      return null;
  	    }
	    } else if ($throwError) {
	      throwError('currentObject parameter not found in SESSION');
	      return null;
	    }
	  } else if (array_key_exists('currentObject'.(($isComboDetail)?'_comboDetail':''),$_SESSION)) {
	    $testObject = $_SESSION['currentObject'.(($isComboDetail)?'_comboDetail':'')];
	    if (!$objectClass or get_class($testObject)==$objectClass) {
	      $oldObject=$testObject;
	    } else if ($throwError) {
	      throwError('currentObject ('.get_class($testObject).' #'.$obj->id.') is not of the expectec class ('.$objectClass.')');
	      return null;
	    }
	  }
	  if (! $oldObject and $objectClass) {
	    $oldObject = new $objectClass($objectId);
	  }
	  return $oldObject;
	}
	public static function setCurrentObject ($obj, $isComboDetail=false) {
	  if (isset($_REQUEST ['directAccessIndex'])) {
	    if (!isset($_SESSION ['directAccessIndex'])) $_SESSION ['directAccessIndex']=array();
	    if ($isComboDetail) {
	      $_SESSION ['directAccessIndex'][$_REQUEST ['directAccessIndex'].'_comboDetail']=$obj;
	    } else {
	      $_SESSION ['directAccessIndex'][$_REQUEST ['directAccessIndex']]=$obj;
	    }
	  } else {
	    if ($isComboDetail) {
	      $_SESSION ['currentObject_comboDetail']=$obj;
	    } else {
	      $_SESSION ['currentObject']=$obj;
	    }
	  }
	}
	public static function unsetCurrentObject () {
	  if (isset($_REQUEST ['directAccessIndex']) and isset($_SESSION ['directAccessIndex'][$_REQUEST ['directAccessIndex']])) {
	    unset($_SESSION ['directAccessIndex'][$_REQUEST ['directAccessIndex']]);
	  } else if (isset($_SESSION ['currentObject'])){
	    unset($_SESSION ['currentObject']);
	  }
	}
	/** =========================================================================
	 * save an object to the database : existing object
	 * @return void
	 */
	private function updateSqlElement($force=false,$withoutDependencies=false) {
		//traceLog('updateSqlElement (for ' . get_class($this) . ' #' . $this->id . ')');
		$returnValue = i18n('messageNoChange') . ' ' . i18n(get_class($this)) . ' #' . $this->id;
		$returnStatus = 'NO_CHANGE';
		$depedantObjects=array();
		$objectClass = get_class($this);
		$arrayCols=array();
		if (Sql::isPgsql()) $arrayCols['lastupdatedatetime']='$lastUpdateDateTime';
		else $arrayCols['lastUpdateDateTime']='$lastUpdateDateTime';
		$idleChange=false;
		// Get old values (stored) to : 1) build the smallest query 2) save change history
		$oldObject = self::getCurrentObject (get_class($this),$this->id,false,$force);
		// Specific treatment for other versions
		$versionTypes=array('Version', 
		    'OriginalVersion', 'OriginalProductVersion', 'OriginalComponentVersion', 
		    'TargetVersion',   'TargetProductVersion',   'TargetComponentVersion');
		foreach ($versionTypes as $versType) {
			$otherFld='_Other'.$versType;
			$versFld='id'.$versType;
			if ( property_exists($this, $versFld) and property_exists($this, $otherFld)) {
				usort($oldObject->$otherFld,"OtherVersion::sort");
				foreach ($oldObject->$otherFld as $otherVers) {
					if (! trim($this->$versFld)) {
						$this->$versFld=$otherVers->idVersion;
					}
					if ($otherVers->idVersion==$this->$versFld) {
						$otherVers->delete();
					}
				}
			}
		}
		$nbChanged=0;
		$query="update " . $this->getDatabaseTableName();
		// get all data, and identify if changes
		foreach($this as $col_name => $col_new_value) {
			$attribute= $this->getFieldAttributes($col_name);
			if (strpos($attribute,'calculated')!==false) {
				// calculated field, not to be save
			} else if (substr($col_name,0,1)=="_") {
				// not a fiels, just for presentation purpose
			} else if (ucfirst($col_name) == $col_name) {
				$depedantObjects[$col_name]=($this->$col_name);
			} else {
				$dataType = $this->getDataType($col_name);
				$dataLength = $this->getDataLength($col_name);
				if ($dataType=='int' and $dataLength==1) {
					if ($col_new_value==NULL or $col_new_value=="") {
						$col_new_value='0';
					}
				}
				$col_old_value=$oldObject->$col_name;
				// special null treatment (new value)
				//$col_new_value=Sql::str(trim($col_new_value));
				if (get_class($this)!='Parameter') { // Do not trim parameters
				  $col_new_value=trim($col_new_value);
				}
				if ($dataType=='decimal') {
				  $col_new_value=str_replace(',', '.', $col_new_value);
				}
				if ($col_new_value=='') {$col_new_value=NULL;};
				// special null treatment (old value)
				//$col_old_value=SQL::str(trim($col_old_value));
				if ($col_old_value=='') {$col_old_value=NULL;};
				// if changed
				$isText=($dataType=='varchar' or substr($dataType,-4)=='text')?true:false;
				if ($isText and $dataLength>4000 and (getEditorType()=='text' or Importable::importInProgress() )) {
				  $textObj=new Html2Text($col_old_value);
				  $oldText=$textObj->getText();
				  if (Importable::importInProgress()) {
				    //$oldText=encodeCSV($oldText);
				    $oldText=str_replace("\n\n","\n",$oldText); // Remove double LF as they were removed during export
				    $oldText=str_replace("\r","",$oldText);
				    $col_new_value=str_replace("\r","",$col_new_value); // Replace CRLF with LF
				  }
				  if (trim($oldText)==trim($col_new_value)) {
				    $col_new_value=$col_old_value; // Was not changed : preserve formatting
				  } else {
				    if (substr($col_new_value,0,4)!='<div') $col_new_value=nl2br($col_new_value);
				  }
				}
				// !!! do not insert query for last update date time unless some change is detected
				if ( $col_new_value != $col_old_value or ($isText and ('x'.$col_new_value != 'x'.$col_old_value) ) ) { 
					if ($col_name=='idle') {$idleChange=true;}
					$insertableColName= $this->getDatabaseColumnName($col_name);
					if (Sql::isPgsql()) {$insertableColName=strtolower($insertableColName);}
					if (!array_key_exists($insertableColName, $arrayCols)) {
						$arrayCols[$insertableColName]=$col_name;
						$query .= ($nbChanged==0)?" set ":", ";
						if ($col_new_value==NULL or $col_new_value=='' or $col_new_value=="''") {
							$query .= $insertableColName . " = NULL";
						} else {
							$query .= $insertableColName . '=' . Sql::str($col_new_value) .' ';
						}
						$nbChanged+=1;
						// Save change history
						if ($objectClass!='History' and ! property_exists($this,'_noHistory') and $col_name!='id' and $col_name!='lastUpdateDateTime') {
							$result = History::store($this, $objectClass,$this->id,'update', $col_name, $col_old_value, $col_new_value);
							if (!$result) {
								$returnStatus="ERROR";
								$returnValue=Sql::$lastQueryErrorMessage;
							}
						}
					}
				}
			}
		}
		if (($force or $nbChanged>0) and property_exists($this, 'lastUpdateDateTime')) {
		  $insertableColName= $this->getDatabaseColumnName('lastUpdateDateTime');
		  if (Sql::isPgsql()) {$insertableColName=strtolower($insertableColName);}
		  $query .= (($nbChanged==0)?' SET ':', ').$insertableColName. '=' . Sql::str(date('Y-m-d H:i:s')) .' ';
		  $nbChanged+=1;
		}
		$query .= ' where id=' . $this->id;
		// If changed, execute the query
		if ($nbChanged > 0 and $returnStatus!="ERROR") {
			// Catch errors, and return error message
			$result = Sql::query($query);
			if ($result) {
				if (Sql::$lastQueryNbRows==0) {
					$test=new $objectClass($this->id);
					if ($this->id!=$test->id) {
						$returnValue = i18n('messageItemDelete', array(i18n(get_class($this)), $this->id));
						$returnStatus='ERROR';
					} else {
						$returnValue = i18n('messageNoChange') . ' ' . i18n(get_class($this)) . ' #' . $this->id;
						$returnStatus = 'NO_CHANGE';
					}
				} else {
					$returnValue=i18n(get_class($this)) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultUpdated');
					$returnStatus='OK';
				}
			} else {
				$returnValue=Sql::$lastQueryErrorMessage;
				$returnStatus="ERROR";
			}
		}

		// if object is Asignable, update assignments on idle change
		if ($idleChange and $returnStatus!="ERROR") {
			$ass=new Assignment();
			$query="update " . $ass->getDatabaseTableName();
			$query.=" set idle='" . $this->idle . "'";
			$query.=" where refType='" . get_class($this) . "' ";
			$query.=" and refId=" . $this->id;
			$result = Sql::query($query);
			if ($returnStatus=="ERROR") {
				$returnValue=Sql::$lastQueryErrorMessage;
				$returnStatus='ERROR';
			}
		}

		// save depedant elements (properties that are objects)
		if ($returnStatus!="ERROR" and ! $withoutDependencies) {
			$returnStatus=$this->saveDependantObjects($depedantObjects,$returnStatus);
			if ($returnStatus=="ERROR") {
				$returnValue=Sql::$lastQueryErrorMessage;
			}
			if ($returnStatus=="OK") {
				$returnValue=i18n(get_class($this)) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultUpdated');
			}
		}
		if ($returnStatus=="OK") {
			$returnValue .= '${mailMsg}';
		}
		// Prepare return data
		$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
		$returnValue .= '<input type="hidden" id="lastOperation" value="update" />';
		$returnValue .= '<input type="hidden" id="lastOperationStatus" value="' . $returnStatus . '" />';
		return $returnValue;
	}

	/** =========================================================================
	 * Save the dependant objects stored in a list (may be single objects or list
	 * @param $depedantObjects list (array) of objects to store
	 * @return void
	 */
	private function saveDependantObjects($depedantObjects,$returnStatus) {
		$returnStatusDep=$returnStatus;
		foreach ($depedantObjects as $class => $depObj) {
			if (is_array($depObj) and $returnStatusDep!="ERROR" ) {
				foreach ($depObj as $depClass => $depObjOccurence) {
					if ($depObjOccurence instanceof SqlElement and $returnStatusDep!="ERROR") {
						$depObjOccurence->refId=$this->id;
						$depObjOccurence->refType=get_class($this);
						$ret=$depObjOccurence->saveSqlElement();
						if (stripos($ret,'id="lastOperationStatus" value="ERROR"')) {
							$returnStatusDep="ERROR";
						} else if (stripos($ret,'id="lastOperationStatus" value="OK"')) {
							$returnStatusDep='OK';
						}
					}
				}
			} else if ($depObj instanceof SqlElement and $returnStatusDep!="ERROR") {
				$depObj->refId=$this->id;
				$depObj->refType=get_class($this);
				$ret=$depObj->save();
				if (stripos($ret,'id="lastOperationStatus" value="ERROR"')) {
					$returnStatusDep="ERROR";
				} else if (stripos($ret,'id="lastOperationStatus" value="OK"')) {
					$returnStatusDep='OK';
				}
			}
		}
		return $returnStatusDep;
	}
	/** =========================================================================
	 * Delete an object from the database
	 * @return void
	 */
	private function deleteSqlElement() {	  
		if (! $this->id or $this->id<0 ) {return;}
		$class = get_class($this);
		$control=$this->deleteControl();
		if ( ($control=='OK' or strpos($control,'id="confirmControl" value="delete"')>0 ) 
				and property_exists($class, $class.'PlanningElement')) {
			$pe=$class.'PlanningElement';
			$controlPe=$this->$pe->deleteControl();
			if ($controlPe!='OK') {
				$control=$controlPe;
			}
		}
		
		if ($control!="OK") {
			// errors on control => don't save, display error message
			if ( strpos($control,'id="confirmControl" value="delete"')>0 ) {
				$returnValue='<b>' . i18n('messageConfirmationNeeded') . '</b><br/>' . $control;
				$returnValue .= '<input type="hidden" id="lastOperationStatus" value="CONFIRM" />';
			} else {
			  $returnValue='<b>' . i18n('messageInvalidControls') . '</b><br/>' . $control;
			  $returnValue .= '<input type="hidden" id="lastOperationStatus" value="INVALID" />';
			}
			$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
			$returnValue .= '<input type="hidden" id="lastOperation" value="control" />';
			
			return $returnValue;
		}
		foreach($this as $col_name => $col_value) {
			// if property is an array containing objects, delete each
			if (is_array($this->$col_name)) {
				foreach ($this->$col_name as $obj) {
					if ($obj instanceof SqlElement) {
						if ($obj->id and $obj->id!='') { // object may be a "new" element, so try to delete only if id exists
							$obj->delete();
						}
					}
				}
			} else if (ucfirst($col_name) == $col_name) {
				// if property is an object, delete it
				if ($this->$col_name instanceof SqlElement) {
					if ($this->$col_name->id and $this->$col_name->id!='') { // object may be a "new" element, so try to delete only if id exists
					  $resSub=$this->$col_name->delete();
					}
				}
			}
		}
		// check relartionship : if "cascade", then auto delete
		$relationShip=self::$_relationShip;
		$canForceDelete=false;
	  if (getSessionUser()->id) {
		  $user=getSessionUser();
		  $crit=array('idProfile'=>$user->getProfile($this), 'scope'=>'canForceDelete');
		  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
		  if ($habil and $habil->id and $habil->rightAccess=='1') {
		    $canForceDelete=true;
		  }
		}
		$returnStatus="OK";
		$returnValue='';
		if ($class=='TicketSimple') {$class='Ticket';}
		if (array_key_exists($class,$relationShip)) {
			$relations=$relationShip[$class];
			$relations['Alert']='cascade';
			$relations['IndicatorValue']='cascade';
			foreach ($relations as $object=>$mode) {
			  if ($mode=="control" and $canForceDelete) {
			    $mode="confirm";
			  } else if ($mode=="controlStrict") {
			    $mode="control";
			  }
				if ($mode=="cascade" or ($mode=="confirm" and self::isDeleteConfirmed())) {
					$where=null;
					$obj=new $object();
					$crit=array($obj->getDatabaseColumnName('id' . $class) => $this->id);
					if (property_exists($obj, 'refType') and property_exists($obj,'refId')) {
					  if (property_exists($obj,'id' . $class)) {
					    $crit=null;
					    $where=$obj->getDatabaseColumnName("id".$class)."=".$this->id." or (refType='".$class."' and refId=".$this->id.")";
					  } else {
					    $crit=array("refType"=>$class, "refId"=>$this->id);
					  }
					}
					if ($object=='VersionProject' and ($class=='ProductVersion' or $class=='ComponentVersion')) {
					  $crit=array('idVersion' => $this->id);
					} 
					if ($object=="Dependency") {
						$crit=null;
						$where="(predecessorRefType='" . $class . "' and predecessorRefId=" . Sql::fmtId($this->id) .")"
						. " or (successorRefType='" . $class . "' and successorRefId=" . Sql::fmtId($this->id) .")";
					}
					if ($object=="Link") {
						$crit=null;
						$where="(ref1Type='" . $class . "' and ref1Id=" . Sql::fmtId($this->id) .")"
						. " or (ref2Type='" . $class . "' and ref2Id=" . Sql::fmtId($this->id) .")";
					}
					if ($object=="WorkflowStatus" and $class=='Status') {
					  $crit=null;
					  $where="idStatusFrom=" . Sql::fmtId($this->id) 
					     . " or idStatusTo=" . Sql::fmtId($this->id);
					}
					$list=$obj->getSqlElementsFromCriteria($crit,false,$where);
					foreach ($list as $subObj) {
						$subObjDel=new $object($subObj->id);
						$resSub=$subObjDel->delete();
						$statusSub = getLastOperationStatus ( $resSub );
						if ($statusSub=='INVALID' or $statusSub=='ERROR') {
						  $returnStatus=$statusSub;
						  $returnValue="$object #$subObj->id <br/><br/>".getLastOperationMessage($resSub);
						  break 2;
						}
					}
				}
			}
		}
		if ($returnStatus=="OK") { // May have errors deleting dependant elements 
  		$query="delete from " .  $this->getDatabaseTableName() . " where id=" . Sql::fmtId($this->id) . "";
  		// execute request
  		$result = Sql::query($query);
  		if (!$result) {
  			$returnStatus="ERROR";
  		} else {
  		  $peName=get_class($this).'PlanningElement';
  		  if (property_exists($this, $peName)) {
  		    $pe=new PlanningElement();
  		    $pe->purge(' refName is null');
  		  }
  		}
		}
		
		// save history
		if ($returnStatus!="ERROR" and ! property_exists($this,'_noHistory') ) {
			$result = History::store($this, $class,$this->id,'delete');
			if (!$result) {$returnStatus="ERROR";}
		}
		if ($returnValue=='') { // If $returnValue set from sub object, do not override with possibly empty.
  		if ($returnStatus!="ERROR") {
  			$returnValue=i18n($class) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultDeleted');
  		} else  { 
  			$returnValue=Sql::$lastQueryErrorMessage;
  		}
		}	
		$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
		$returnValue .= '<input type="hidden" id="lastOperation" value="delete" />';
		$returnValue .= '<input type="hidden" id="lastOperationStatus" value="' . $returnStatus .'" />';
		$returnValue .= '<input type="hidden" id="noDataMessage" value="' . htmlGetNoDataMessage(get_class($this)) . '" />';
		return $returnValue;
	}


	/** =========================================================================
	 * Purge objects from the database : delete all objects corresponding
	 * to clause $ clause
	 * Important :
	 *   => does not automatically purges included elements ...
	 *   => does not include history insertion
	 * @return void
	 */
	private function purgeSqlElement($clause) {
		$objectClass = get_class($this);
		// purge depending Planning Element if any
		if (property_exists($this, $objectClass.'PlanningElement')) {
			$query="select id from " .  $this->getDatabaseTableName() . " where " . $clause;
			$resultId = Sql::query($query);
			if (Sql::$lastQueryNbRows > 0) {
				$line = Sql::fetchLine($resultId);
				$peCrit='(0';
				while ($line) {
					$peCrit.=','.$line['id'];
					$line = Sql::fetchLine($resultId);
				}
				$peCrit.=')';
				$pe=new PlanningElement();
				$query="delete from " .  $pe->getDatabaseTableName() . " where refType='$objectClass' and refId in $peCrit";
				Sql::query($query);
			}
		}
		// get all data, and identify if changes
		$query="delete from " .  $this->getDatabaseTableName() . " where " . $clause;
		// execute request
		$returnStatus="OK";
		$result = Sql::query($query);
		if (!$result) {
			$returnStatus="ERROR";
		}
		if ($returnStatus!="ERROR") {
			$returnValue=Sql::$lastQueryNbRows . " " . i18n(get_class($this)) . '(s) ' . i18n('doneoperationdelete');
		} else {
			$returnValue=Sql::$lastQueryErrorMessage;
		}
		$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
		$returnValue .= '<input type="hidden" id="lastOperation" value="delete" />';
		$returnValue .= '<input type="hidden" id="lastOperationStatus" value="' . $returnStatus .'" />';
		$returnValue .= '<input type="hidden" id="noDataMessage" value="' . htmlGetNoDataMessage(get_class($this)) . '" />';
		return $returnValue;
	}

	/** =========================================================================
	 * Close objects from the database : delete all objects corresponding
	 * to clause $ clause
	 * Important :
	 *   => does not automatically purges included elements ...
	 *   => does not include history insertion
	 * @return void
	 */
	private function closeSqlElement($clause) {
		$objectClass = get_class($this);
		// get all data, and identify if changes
		$query="update " .  $this->getDatabaseTableName() . " set idle='1' where " . $clause;
		// execute request
		$returnStatus="OK";
		$result = Sql::query($query);
		if (!$result) {
			$returnStatus="ERROR";
		}
		if ($returnStatus!="ERROR") {
			$returnValue=Sql::$lastQueryNbRows . " " . i18n(get_class($this)) . '(s) ' . i18n('doneoperationclose');
		} else {
			$returnValue=Sql::$lastQueryErrorMessage;
		}
		$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($this->id) . '" />';
		$returnValue .= '<input type="hidden" id="lastOperation" value="update" />';
		$returnValue .= '<input type="hidden" id="lastOperationStatus" value="' . $returnStatus .'" />';
		$returnValue .= '<input type="hidden" id="noDataMessage" value="' . htmlGetNoDataMessage(get_class($this)) . '" />';
		return $returnValue;
	}


	/** =========================================================================
	 * Copy the curent object as a new one of the same class
	 * @return the new object
	 */
	private function copySqlElement() {
		$newObj=clone $this;
		$newObj->id=null;
		if (property_exists($newObj,"wbs")) {
			$newObj->wbs=null;
		}
		if (property_exists($newObj,"topId")) {
			$newObj->topId=null;
		}
		if (property_exists($newObj,"idStatus")) {
			if (get_class($newObj)=='TestSession') {
				$list=SqlList::getList('Status');
				$revert=array_keys($list);
				$newObj->idStatus=$revert[0];
			} else {
				$st=SqlElement::getSingleSqlElementFromCriteria('Status', array('isCopyStatus'=>'1'));
				if (! $st or ! $st->id) {
					errorLog("Error : several or no status exist with isCopyStatus=1 (expected is 1 only and only 1)");
				}
				$newObj->idStatus=$st->id;
			}

		}
		if (property_exists($newObj,"idUser") and get_class($newObj)!='Affectation' and get_class($newObj)!='Message') {
			$newObj->idUser=getSessionUser()->id;
		}
		if (property_exists($newObj,"creationDate")) {
			$newObj->creationDate=date('Y-m-d');
		}
		if (property_exists($newObj,"creationDateTime")) {
			$newObj->creationDateTime=date('Y-m-d H:i');
		}
		if (property_exists($newObj,"done")) {
			$newObj->done=0;
		}
		if (property_exists($newObj,"idle")) {
			$newObj->idle=0;
		}
		if (property_exists($newObj,"idleDate")) {
			$newObj->idleDate=null;
		}
		if (property_exists($newObj,"doneDate")) {
			$newObj->doneDate=null;
		}
		if (property_exists($newObj,"idleDateTime")) {
			$newObj->idleDateTime=null;
		}
		if (property_exists($newObj,"doneDateTime")) {
			$newObj->doneDateTime=null;
		}
		if (property_exists($newObj,"reference")) {
			$newObj->reference=null;
		}
		if (property_exists($newObj,"password")) {
		  $newObj->password=null;
		}
		if (property_exists($newObj,"apiKey")) {
		  $newObj->apiKey=md5($this->id.date('Ymdhis'));
		}
		if (property_exists($newObj,"idRunStatus")) {
		  $newObj->idRunStatus=5;
		}
		foreach($newObj as $col_name => $col_value) {
			if (ucfirst($col_name) == $col_name) {
				// if property is an object, delete it
				if ($newObj->$col_name instanceof SqlElement) {
					$newObj->$col_name->id=null;
					if (property_exists($newObj->$col_name,"wbs")) {
						$newObj->$col_name->wbs=null;
					}
					if (property_exists($newObj->$col_name,"topId")) {
						$newObj->$col_name->topId=null;
					}
					if ($newObj->$col_name instanceof PlanningElement) {
						$newObj->$col_name->plannedStartDate="";
						$newObj->$col_name->realStartDate="";
						$newObj->$col_name->plannedEndDate="";
						$newObj->$col_name->realEndDate="";
						$newObj->$col_name->plannedDuration="";
						$newObj->$col_name->realDuration="";
						$newObj->$col_name->assignedWork=0;
						$newObj->$col_name->plannedWork=0;
						$newObj->$col_name->leftWork=0;
						$newObj->$col_name->realWork=0;
						$newObj->$col_name->notPlannedWork=0;
						$newObj->$col_name->idle=0;
						$newObj->$col_name->done=0;
					}
				}
			}
		}
		if (get_class($this)=='User') {
			$newObj->name=i18n('copiedFrom') . ' ' . $newObj->name;
		}
		if (is_a($this, 'Version') and $newObj->versionNumber) {
		  $newObj->versionNumber=$newObj->versionNumber.' ('.i18n('copy').')';
		  $newObj->name=$newObj->name.' ('.i18n('copy').')';
		}
		if (property_exists($newObj,"isCopyStatus")) {
			$newObj->isCopyStatus=0;
		}
		$result=$newObj->saveSqlElement();
		Sql::$lastCopyId=$newObj->id;
		if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
			$returnValue=i18n(get_class($this)) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultCopied') . ' #' . $newObj->id;
			$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($newObj->id) . '" />';
			$returnValue .= '<input type="hidden" id="lastOperation" value="copy" />';
			$returnValue .= '<input type="hidden" id="lastOperationStatus" value="OK" />';
		} else {
			$returnValue=$result;
		}
		$newObj->_copyResult=$returnValue;
		return $newObj;
	}

	private function copySqlElementTo($newClass, $newType, $newName, $setOrigin, $withNotes, $withAttachments,$withLinks,$withAssignments=false,$withAffectations=false, $toProject=null, $toActivity=null, $copyToWithResult=false) {
		$newObj=new $newClass();
		$newObj->id=null;
		$typeName='id' . $newClass . 'Type';
		$newObj->$typeName=$newType;
		if ($setOrigin and property_exists($newObj, 'Origin')) {
			$newObj->Origin->originType=get_class($this);
			$newObj->Origin->originId=$this->id;
			$newObj->Origin->refType=$newClass;
		}
		foreach($newObj as $col_name => $col_value) {
			if (ucfirst($col_name) == $col_name) {
				if ($newObj->$col_name instanceof PlanningElement) {
					$sub=substr($col_name, 0,strlen($col_name)-15    );
					$plMode='id' . $sub . 'PlanningMode';
					if ($newClass=="Activity") {
						$newObj->$col_name->$plMode="1";
					} else if ($newClass=="Milestone") {
						$newObj->$col_name->$plMode="5";
					}
					if (get_class($this)==$newClass and $newClass!='Project') {
						$newObj->$col_name->$plMode=$this->$col_name->$plMode;
					}
					$newObj->$col_name->refName=$newName;
				}
			}
		}
		foreach($this as $col_name => $col_value) {
			if (ucfirst($col_name) == $col_name) {
				if ($this->$col_name instanceof SqlElement) {
					//$newObj->$col_name->id=null;
					if ($this->$col_name instanceof PlanningElement) {
						$pe=$newClass . 'PlanningElement';
						if (property_exists($newObj, $pe)) {
							if (get_class($this)==$newClass) {
								$plMode='id' . $newClass . 'PlanningMode';
								if (property_exists($this->$col_name,$plMode)) {
									$newObj->$col_name->$plMode=$this->$col_name->$plMode;
								}
							}
							$newObj->$pe->initialStartDate=$this->$col_name->initialStartDate;
							$newObj->$pe->initialEndDate=$this->$col_name->initialEndDate;
							$newObj->$pe->initialDuration=$this->$col_name->initialDuration;
							$newObj->$pe->validatedStartDate=$this->$col_name->validatedStartDate;
							$newObj->$pe->validatedEndDate=$this->$col_name->validatedEndDate;
							$newObj->$pe->validatedDuration=$this->$col_name->validatedDuration;
							$newObj->$pe->validatedWork=$this->$col_name->validatedWork;
							$newObj->$pe->validatedCost=$this->$col_name->validatedCost;
							$newObj->$pe->priority=$this->$col_name->priority;
							//$newObj->$pe->topId=$this->$col_name->topId;
							$newObj->$pe->topRefType=$this->$col_name->topRefType;
							$newObj->$pe->topRefId=$this->$col_name->topRefId;
						}
					}
				}
			} else if (property_exists($newObj,$col_name)) {
				if ($col_name!='id' and $col_name!="wbs" and $col_name!='name' and $col_name != $typeName
				and $col_name!="handled" and $col_name!="handledDate" and $col_name!="handledDateTime"
				and $col_name!="done" and $col_name!="doneDate" and $col_name!="doneDateTime"
				and $col_name!="idle" and $col_name!="idleDate" and $col_name!="idelDateTime"
				and $col_name!="idStatus" and $col_name!="reference" and $col_name!="billId"){ //topId ?
					$newObj->$col_name=$this->$col_name;
				}
			}
		}
		if (property_exists($newObj,"idStatus")) {
			$st=SqlElement::getSingleSqlElementFromCriteria('Status', array('isCopyStatus'=>'1'));
			if (! $st or ! $st->id) {
				errorLog("Error : several on no status exist with isCopyStatus=1");
			}
			$newObj->idStatus=$st->id;
		}
		if (property_exists($newObj,"idUser") and get_class($newObj)!='Affectation' and get_class($newObj)!='Message') {
			$newObj->idUser=getSessionUser()->id;
		}
		if (property_exists($newObj,"creationDate")) {
			$newObj->creationDate=date('Y-m-d');
		}
		if (property_exists($newObj,"creationDateTime")) {
			$newObj->creationDateTime=date('Y-m-d H:i');
		}
		if (property_exists($newObj,"meetingDate")) {
			$newObj->meetingDate=date('Y-m-d');
		}
		if (property_exists($newObj,"reference")) {
			$newObj->reference=null;
		}
		if (property_exists($newObj,"idProject") and $toProject) {
		  $newObj->idProject=$toProject;
		}
		if (property_exists($newObj,"idActivity") and $toActivity) {
		  $newObj->idActivity=$toActivity;
		}
		if (get_class($newObj)=='Bill') {
		  $newObj->paymentDate=null;
		  $newObj->paymentAmount=null;
		  $newObj->paymentDone=null;
		  $newObj->paymentsCount=null;
		  $newObj->date=date('Y-m-d');
		  $newObj->sendDate=null;
		  $newObj->idDeliveryMode=null;
		}
		$newObj->name=$newName;
		// check description
		if (property_exists($newObj,'description') and ! $newObj->description ) {
			$idType='id'.$newClass.'Type';
			if (property_exists($newObj, $idType)) {
				$type=$newClass.'Type';
				$objType=new $type($newObj->$idType);
				if (property_exists($objType, 'mandatoryDescription') and $objType->mandatoryDescription) {
					$newObj->description=$newObj->name;
				}
			}
		}
		if(!$copyToWithResult and property_exists($newObj,"result")){
		  $newObj->result=null;
		}
		$result=$newObj->save();
		if (stripos($result,'id="lastOperationStatus" value="OK"')>0 ) {
			$returnValue=i18n(get_class($this)) . ' #' . htmlEncode($this->id) . ' ' . i18n('resultCopied') . ' #' . $newObj->id;
			$returnValue .= '<input type="hidden" id="lastSaveId" value="' . htmlEncode($newObj->id) . '" />';
			$returnValue .= '<input type="hidden" id="lastOperation" value="copy" />';
			$returnValue .= '<input type="hidden" id="lastOperationStatus" value="OK" />';
		} else {
			$returnValue=$result;
		}
		if ($withNotes and property_exists($this,"_Note") and property_exists($newObj,"_Note")) {
			$crit=array('refType'=>get_class($this),'refId'=>$this->id);
			$note=new Note();
			$notes=$note->getSqlElementsFromCriteria($crit);
			foreach ($notes as $note) {
				$note->id=null;
				$note->refType=get_class($newObj);
				$note->refId=$newObj->id;
				$note->save();
			}
		}
		
		if ($withLinks) {
			$crit=array('ref1Type'=>get_class($this),'ref1Id'=>$this->id);
			$link=new Link();
			$links=$link->getSqlElementsFromCriteria($crit);
			foreach ($links as $link) {
				$link->id=null;
				$link->ref1Type=get_class($newObj);
				$link->ref1Id=$newObj->id;
				$link->save();
			}
			$crit=array('ref2Type'=>get_class($this),'ref2Id'=>$this->id);
			$link=new Link();
			$links=$link->getSqlElementsFromCriteria($crit);
			foreach ($links as $link) {
				$link->id=null;
				$link->ref2Type=get_class($newObj);
				$link->ref2Id=$newObj->id;
				$link->save();
			}
		}
		if ($withAttachments) {
			$crit=array('refType'=>get_class($this),'refId'=>$this->id);
			$attachment=new Attachment();
			$attachments=$attachment->getSqlElementsFromCriteria($crit);
			$pathSeparator=Parameter::getGlobalParameter('paramPathSeparator');
			$attachmentDirectory=Parameter::getGlobalParameter('paramAttachmentDirectory');
			foreach ($attachments as $attachment) {
				$fromdir = $attachmentDirectory . $pathSeparator . "attachment_" . $attachment->id . $pathSeparator;
				if (file_exists($fromdir.$attachment->fileName)) {
					$attachment->id=null;
					$attachment->refType=get_class($newObj);
					$attachment->refId=$newObj->id;
					$attachment->save();
					$todir = $attachmentDirectory . $pathSeparator . "attachment_" . $attachment->id . $pathSeparator;
					if (! file_exists($todir)) {
						mkdir($todir, 0777 , true);
					}
					copy($fromdir.$attachment->fileName, $todir.$attachment->fileName);
					$attachment->subDirectory=str_replace($attachmentDirectory,'${attachmentDirectory}',$todir);
					$attachment->save();
				}
			}
		}
		if ($withAssignments and property_exists($this,"_Assignment") and property_exists($newObj,"_Assignment")) {
		  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', 
		      array('idProfile' => getSessionUser()->getProfile($this),'scope' => 'assignmentEdit'));
		  if ($habil and $habil->rightAccess == 1) {
  			$ass=new Assignment();
  			// First delete existing Assignment (possibly created from Responsible)
  			if (property_exists($this, 'idResource') and $this->idResource) {
  			  $crit=array('idResource'=>$this->idResource,'refType'=>get_class($this), 'refId'=>$newObj->id);
  			  $assResp=SqlElement::getSingleSqlElementFromCriteria('Assignment', $crit);
  			  if ($assResp and $assResp->id) {
  			    $resDel=$assResp->delete();
  			  }
  			}
  	  	$crit=array('refType'=>get_class($this), 'refId'=>$this->id);
  	  	$lstAss=$ass->getSqlElementsFromCriteria($crit);
  	  	foreach ($lstAss as $ass) {
  	  		$ass->id=null;
  	  		$ass->idProject=$newObj->idProject;
  	  		$ass->refType=$newClass;
  	  		$ass->refId=$newObj->id;
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
		if (property_exists($this, '_BillLine') and property_exists($this, '_BillLine')) { // Copy BillLines
		  $crit=array('refType'=>get_class($this),'refId'=>$this->id);
		  $line=new BillLine();
		  $lines=$line->getSqlElementsFromCriteria($crit);
		  foreach ($lines as $line) {
		    $line->id=null;
		    $line->refType=get_class($newObj);
		    $line->refId=$newObj->id;
		    $line->save();
		  }
		}
		$newObj->_copyResult=$returnValue;
		return $newObj;
	}

	// ============================================================================**********
	// GET AND FETCH OBJECTS FUNCTIONS
	// ============================================================================**********

	/** =========================================================================
	 * Retrieve an object from the Request (modified Form) - Public method
	 * @return void (operate directly on the object)
	 */
	public function fillFromRequest($ext=null) {
		$this->fillSqlElementFromRequest(null,$ext);
	}

	/**  ========================================================================
	 * Retrieve a list of objects from the Database
	 * Called from an empty object of the expected class
	 * @param array $critArray the critera as an array
	 * @param boolean $initializeIfEmpty indicating if no result returns an
	 * initialised element or not
	 * @param string $clauseWhere Sql Where clause (alternative way to define criteria)
	 *        => $critArray must not be set
	 * @param string $clauseOrderBy Sql Order By clause
	 * @param boolean $getIdInKey
	 * @return SqlElement[] an array of objects
	 */
	public function getSqlElementsFromCriteria($critArray, $initializeIfEmpty=false,
	$clauseWhere=null, $clauseOrderBy=null, $getIdInKey=false, $withoutDependentObjects=false, $maxElements=null ) {
		//scriptLog("getSqlElementsFromCriteria(implode('|',$critArray), $initializeIfEmpty,$clauseWhere, $clauseOrderBy, $getIdInKey)");
		// Build where clause from criteria
		$whereClause='';
		$objects=array();
		$className=get_class($this);
		$defaultObj = new $className();
		if ($critArray) {
			foreach ($critArray as $colCrit => $valCrit) {
				$whereClause.=($whereClause=='')?' where ':' and ';
				if ($valCrit==null or $valCrit==' ') {
					$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . ' is null';
				} else {
					if ($this->getDataType($colCrit)=='int' and is_numeric($valCrit)) {
						$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . '='.$valCrit;
					} else {
						$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . '='.Sql::str($valCrit);
					}
				}
				$defaultObj->$colCrit=$valCrit;
			}
		} else if ($clauseWhere) {
			$whereClause = ' where ' . $clauseWhere;
		}
		$objectCrit=$this->getDatabaseCriteria();
		if (count($objectCrit)>0) {
			foreach ($objectCrit as $colCrit => $valCrit) {
				$whereClause.=($whereClause=='')?' where ':' and ';
				$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . " = " . Sql::str($valCrit) . " ";
			}
		}
		if (property_exists($this, 'isPrivate')) {
		  $whereClause.=($whereClause=='')?' where ':' and ';
		  $whereClause.=SqlElement::getPrivacyClause($this);
		}
		if (array_key_exists($className,self::$_cachedQuery)) {
			if (array_key_exists($whereClause,self::$_cachedQuery[$className])) {
				return self::$_cachedQuery[$className][$whereClause];
			}
		}
		// If $whereClause is set, get the element from Database
		$query = 'select * from ' . $this->getDatabaseTableName() . $whereClause;
		if ($clauseOrderBy) {
			$query .= ' order by ' . $clauseOrderBy;
		} else if (isset($this->sortOrder)) {
			$query .= ' order by ' . $this->getDatabaseTableName() . '.sortOrder';
		}
		if ($maxElements) {
			$query.=' LIMIT '.$maxElements;
		}
		$result = Sql::query($query);

		if (Sql::$lastQueryNbRows > 0) {
			$line = Sql::fetchLine($result);
			while ($line) {
				$obj=clone($this);
				// get all data fetched
				$keyId=null;
				foreach ($obj as $col_name => $col_value) {
					if (substr($col_name,0,1)=="_") {
						// not a field, just for presentation purpose
					} else if (strpos($this->getFieldAttributes($col_name),'calculated')!==false) {
						// calculated field : not to be fetched
					} else if (ucfirst($col_name) == $col_name) {
						if (! $withoutDependentObjects) {
							$obj->$col_name=$obj->getDependantSqlElement($col_name);
						}
					} else {
						$dbColName=$obj->getDatabaseColumnName($col_name);
						if (array_key_exists($dbColName,$line)) {
							$obj->{$col_name}=$line[$dbColName];
						} else if (array_key_exists(strtolower($dbColName),$line)) {
							$obj->{$col_name}=$line[strtolower($dbColName)];
						} else {
							errorLog("Error on SqlElement to get '" . $col_name . "' for Class '".get_class($obj) . "' "
							. " : field '" . $dbColName . "' not found in Database.");
						}
						if ($col_name=='id' and $getIdInKey) {$keyId='#' . $obj->{$col_name};}
					}
				}
				if ($getIdInKey) {
					$objects[$keyId]=$obj;
				} else {
					$objects[]=$obj;
				}

				$line = Sql::fetchLine($result);
			}
		} else {
			if ($initializeIfEmpty) {
				$objects[]=$defaultObj; // return at least 1 element, initialized with criteria
			}
		}
		if (array_key_exists($className,self::$_cachedQuery)) {
			self::$_cachedQuery[$className][$whereClause]=$objects;
		}
		return $objects;
	}

	/**  ========================================================================
	 * Retrieve the count of a list of objects from the Database
	 * Called from an empty object of the expected class
	 * @param $critArray the critera asd an array
	 * @param $clauseWhere Sql Where clause (alternative way to define criteria)
	 *        => $critArray must not be set
	 * @param $clauseOrderBy Sql Order By clause
	 * @return an array of objects
	 */
	public function countSqlElementsFromCriteria($critArray, $clauseWhere=null) {
		// Build where clause from criteria
		$whereClause='';
		$objects=array();
		$className=get_class($this);
		$defaultObj = new $className();
		if ($critArray) {
			foreach ($critArray as $colCrit => $valCrit) {
				$whereClause.=($whereClause=='')?' where ':' and ';
				if ($valCrit==null) {
					$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . ' is null';
				} else {
					$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . '= ' . Sql::str($valCrit);
				}
				$defaultObj->$colCrit=$valCrit;
			}
		} else if ($clauseWhere) {
			$whereClause = ' where ' . $clauseWhere;
		}
		// If $whereClause is set, get the element from Database
		$query = "select count(*) as cpt from " . $this->getDatabaseTableName() . $whereClause;
		$result = Sql::query($query);
		if (Sql::$lastQueryNbRows > 0) {
			$line = Sql::fetchLine($result);
			return $line['cpt'];
		}
		return 0;
	}
	public function sumSqlElementsFromCriteria($field, $critArray, $clauseWhere=null) {
	  // Build where clause from criteria
	  $fields=array();
	  if (is_array($field)) {
	    $fields=$field;
	  } else {
	    $fields=array($field);
	  }
	  $whereClause='';
	  $objects=array();
	  $className=get_class($this);
	  $defaultObj = new $className();
	  if ($critArray) {
	    foreach ($critArray as $colCrit => $valCrit) {
	      $whereClause.=($whereClause=='')?' where ':' and ';
	      if ($valCrit==null) {
	        $whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . ' is null';
	      } else {
	        $whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . '= ' . Sql::str($valCrit);
	      }
	      $defaultObj->$colCrit=$valCrit;
	    }
	  } else if ($clauseWhere) {
	    $whereClause = ' where ' . $clauseWhere;
	  }
	  // If $whereClause is set, get the element from Database
	  $selectFields="";
	  foreach ($fields as $fld) {
	    if ($selectFields) $selectFields.=', ';
	    $fldName=$defaultObj->getDatabaseColumnName($fld);
	    $selectFields.=" sum($fldName) as sum".strtolower($fld);
	  }
	  
	  $query = "select ". $selectFields . ' from ' . $this->getDatabaseTableName() . $whereClause;
	  $result = Sql::query($query);
	  if (Sql::$lastQueryNbRows > 0) {
	    $line = Sql::fetchLine($result);
	    if (is_array($field)) return $line;
	    else return $line["sum".strtolower($field)];
	  }
	  return null;
	}

	public function countGroupedSqlElementsFromCriteria($critArray, $critGroup, $critwhere) {
		// Build where clause from criteria
		$whereClause='';
		$className=get_class($this);
		if ($critArray) {
			foreach ($critArray as $colCrit => $valCrit) {
				$whereClause.=($whereClause=='')?' where ':' and ';
				if ($valCrit==null) {
					$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . ' is null';
				} else {
					$whereClause.=$this->getDatabaseTableName() . '.' . $this->getDatabaseColumnName($colCrit) . '= ' . Sql::str($valCrit);
				}
			}
		} else {
			$whereClause=$critwhere;
		}
		$groupList='';
		$critGroup=array_map('strtolower',$critGroup);
		foreach ($critGroup as $group) {
			$groupList.=($groupList=='')?'':', ';
			$groupList.=$group;
		}
		$query = "select $groupList, count(*) as cpt from " . $this->getDatabaseTableName() . ' where ' . $whereClause . " group by $groupList";
		$result = Sql::query($query);
		$groupRes=array();
		if (Sql::$lastQueryNbRows > 0) {
			while ($line = Sql::fetchLine($result)) {
				$grp='';
				foreach ($critGroup as $group) {
					$grp.=(($grp=='')?'':'|').$line[$group];
				}
				$groupRes[$grp]=$line['cpt'];
			}
		}
		return $groupRes;
	}

	/**  ==========================================================================
	 * Retrieve a single object from the Database
	 * Called from an empty object of the expected class
	 * @param $critArray the critera asd an array
	 * @param $initializeIfEmpty boolean indicating if no result returns en initialised element or not
	 * @return an array of objects
	 */
	public static function getSingleSqlElementFromCriteria($class, $critArray) {
		$obj=new $class();
		if ($class=='Attachment') {
			if (array_key_exists('refType',$critArray) ) {
				if ($critArray['refType']=='User' or $critArray['refType']=='Contact') {
					$critArray['refType']='Resource';
				}
			}
		}
		$objList=$obj->getSqlElementsFromCriteria($critArray, true);
		if (count($objList)==1) {
			return $objList[0];
		} else {
			$obj->_singleElementNotFound=true;
			if (count($objList)>1) {
				//traceLog("getSingleSqlElementFromCriteria for object '" . $class . "' returned more than 1 element");
				$obj->_tooManyRows=true;
			}
			return $obj;
		}
	}

	/**  ==========================================================================
	 * Retrieve an object from the Request (modified Form)
	 * @return void (operate directly on the object)
	 */
	private function fillSqlElementFromRequest($included=false,$ext=null) {
		foreach($this as $key => $value) {
			// If property is an object, recusively fill it
			if (ucfirst($key) == $key and substr($key,0,1)<> "_") {
				if (is_object($key)) {
					$subObjectClass = get_class($key);
					$subObject = $key;
				} else {
					$subObjectClass = $key;
					$subObject= new $subObjectClass;
				}
				$subObject->fillSqlElementFromRequest(true,$ext);
				$this->$key = $subObject;
			} else {
				if (substr($key,0,1)== "_") {
					// not a real field
				} else {
					$dataType = $this->getDataType($key);
					$dataLength = $this->getDataLength($key);
					$formField = $key . $ext;
					if ($included) { // if included, then object is called recursively, name is prefixed by className
						$formField = get_class($this) . '_' . $key . $ext;
					}
					if ($dataType=='int') {
					  if ($dataLength==1 and substr($key,0,11)!='periodicity') {
  						if (array_key_exists($formField,$_REQUEST)) {
  							//if field is hidden, must check value, otherwise just check existence
  							// if ($this->isAttributeSetToField($key, 'hidden')) {
  							// V5.4 : for action isPrivate can be dynamically hidden, was not detected with prior test
  							if ($_REQUEST[$formField]==='0' or $_REQUEST[$formField]==='1') {  
  							  $this->$key = $_REQUEST[$formField];
  							} else if ($_REQUEST[$formField]=='') {
  							  $this->$key = 0;
  							} else {
  								$this->$key = 1;
  							}
  						} else {
  							//echo "val=False<br/>";
  							$this->$key = 0;
  						}
					  } else if (array_key_exists($formField,$_REQUEST)) {
					    $this->$key = Security::checkValidInteger($_REQUEST[$formField]);
					  }				  
					} else if ($dataType=='datetime') {
						$formFieldBis = $key . "Bis" . $ext;
						if ($included) {
							$formFieldBis = get_class($this) . '_' . $key . "Bis" . $ext;
						}
						if (isset($_REQUEST[$formFieldBis])) {
							$test=Security::checkValidDateTime($_REQUEST[$formField]);
							$test=Security::checkValidDateTime($_REQUEST[$formFieldBis]);
							$this->$key = $_REQUEST[$formField] . " ";
              if (substr($_REQUEST[$formFieldBis],0,1)=='T') {
 							  $this->$key .= substr($_REQUEST[$formFieldBis],1);
              } else {
							  $this->$key .= $_REQUEST[$formFieldBis];
              }
						} else {
							//hidden field
							if (isset($_REQUEST[$formField])) {							
								$this->$key = $_REQUEST[$formField];
							}
						}
					} else if ($dataType=='decimal' and (substr($key, -4,4)=='Work')) {
						if (array_key_exists($formField,$_REQUEST)) {
						  if (get_class($this)=='WorkElement') {
						    $this->$key=Work::convertImputation($_REQUEST[$formField]);
						  } else {
							  $this->$key=Work::convertWork($_REQUEST[$formField]);
						  }
						}
					} else if ($dataType=='time') {
						if (array_key_exists($formField,$_REQUEST)) {
						  $test=Security::checkValidDateTime($_REQUEST[$formField]);
							$this->$key=substr($_REQUEST[$formField],1);
						}
					} else if ($dataType=='date') {
					  if (array_key_exists($formField,$_REQUEST)) {
					    $test=Security::checkValidDateTime($_REQUEST[$formField]);
					    $this->$key=$_REQUEST[$formField];
					  }
					} else {
						if (array_key_exists($formField,$_REQUEST)) {
							$this->$key = $_REQUEST[$formField];
						}
					}
				}
			}
		}
	}

	/**  ==========================================================================
	 * Retrieve an object from the Database
	 * @return void
	 */
	private function getSqlElement($withoutDependentObjects=false) {
		$curId=$this->id;
		if (! trim($curId)) {$curId=null;}
		
		// Cache management
		if ($curId and array_key_exists(get_class($this),self::$_cachedQuery)) {
			$whereClause='#id=' . $curId;
			$class=get_class($this);
			if (array_key_exists($whereClause,self::$_cachedQuery[$class])) {
				$obj=self::$_cachedQuery[$class][$whereClause];
				foreach($obj as $fld=>$val) {
					$this->$fld=$obj->$fld;
				}
				return;
			}
		}
		$empty=true;
		// If id is set, get the element from Database
		if ($curId != NULL) {
			$query = "select * from " . $this->getDatabaseTableName() . ' where id=' . $curId ;
			foreach ($this->getDatabaseCriteria() as $critFld=>$critVal) {
				$query .= ' and ' . $critFld . ' = ' . Sql::str($critVal);
			}
			$result = Sql::query($query);
			if (Sql::$lastQueryNbRows > 0) {
				$empty=false;
				$line = Sql::fetchLine($result);
				// get all data fetched
				foreach ($this as $col_name => $col_value) {
					if (substr($col_name,0,1)=="_") {
						$colName=substr($col_name,1);
						if (is_array($this->{$col_name}) and ucfirst($colName) == $colName and ! $withoutDependentObjects) {
							if (substr($colName,0,4)=="Link") {
								$linkClass=null;
								if (strlen($colName)>4) {
									$linkClass=substr($colName,5);
								}
								$this->{$col_name}=Link::getLinksForObject($this,$linkClass);
							} else if ($colName=="ResourceCost") {
								$this->{$col_name}=$this->getResourceCost();
							}  else if ($colName=="VersionProject") {
								if (get_class($this)!='OriginalVersion' and get_class($this)!='TargetVersion'
		              and get_class($this)!='OriginalProductVersion' and get_class($this)!='TargetProductVersion'
		              and get_class($this)!='OriginalComponentVersion' and get_class($this)!='TargetComponentVersion') {
									$vp=new VersionProject();
									$idCrit='id'.((get_class($this)=='Project')?'Project':'Version');
									$crit=array($idCrit=>$this->id);
									$this->{$col_name}=$vp->getSqlElementsFromCriteria($crit,false);
								}
							}  else if ($colName=="DocumentVersion") {
								$dv=new DocumentVersion();
								$crit=array('idDocument'=>$this->id);
								$this->{$col_name}=$dv->getSqlElementsFromCriteria($crit,false);
							} else if ($colName=="ExpenseDetail") {
								$this->{$col_name}=$this->getExpenseDetail();
							} else if (substr($colName,0,10)=="Dependency") {
								$depType=null;
								$crit=Array();
								if (strlen($colName)>10) {
									$depType=substr($colName,11);
									if ($depType=="Successor") {
										$crit=Array("PredecessorRefType"=>get_class($this),
                                "PredecessorRefId"=>$this->id );
									} else {
										$crit=Array("SuccessorRefType"=>get_class($this),
                                "SuccessorRefId"=>$this->id );
									}
								}
								$dep=new Dependency();
								$this->{$col_name}=$dep->getSqlElementsFromCriteria($crit, false);
							} else {
								$this->{$col_name}=$this->getDependantSqlElements($colName);
							}
						}
					} else if (ucfirst($col_name) == $col_name) {
					  if (! $withoutDependentObjects) {
						  $this->{$col_name}=$this->getDependantSqlElement($col_name);
					  }
					} else if (strpos($this->getFieldAttributes($col_name),'calculated')!==false) {
						 
					} else {
						//$test=$line[$this->getDatabaseColumnName($col_name)];
						$dbColName=$this->getDatabaseColumnName($col_name);
						if (array_key_exists($dbColName,$line)) {
							$this->{$col_name}=$line[$dbColName];
						} else if (array_key_exists(strtolower($dbColName),$line)) {
							$dbColName=strtolower($dbColName);
							$this->{$col_name}=$line[$dbColName];
						} else {
							errorLog("Error on SqlElement to get '" . $col_name . "' for Class '".get_class($this) . "' "
							. " : field '" . $dbColName . "' not found in Database.");
						}
					}
				}
			} else {
				$this->id=null;
			}
		}
		if ($empty and ! $withoutDependentObjects) {
			// Get all the elements that are objects (first letter is uppercase in object properties)
			foreach($this as $key => $value) {
				//echo substr($key,0,1) . "<br/>";
				if (ucfirst($key) == $key and substr($key,0,1)<> "_") {
					$this->{$key}=$this->getDependantSqlElement($key);
				}
			}
		}
		// set default idUser if exists
		if ($empty and property_exists($this, 'idUser') and get_class($this)!='Affectation' and get_class($this)!='Message') {
			if (sessionUserExists()) {
				$this->idUser=getSessionUser()->id;
			}
		}
		if ($curId and array_key_exists(get_class($this),self::$_cachedQuery)) {
			$whereClause='#id=' . $curId;
			$class=get_class($this);
			self::$_cachedQuery[get_class($this)][$whereClause]=clone($this);
		}
	}

	/** ==========================================================================
	 * retrieve single object included in an object from the Database
	 * @param $objClass the name of the class of the included object
	 * @return an object
	 */
	private function getDependantSqlElement($objClass) {		
		$curId=$this->id;
		if (! trim($curId)) {$curId=null;}
		$obj = new $objClass();
		$obj->refId=$this->id;
		$obj->refType=get_class($this);
		// If id is set, get the elements from Database
		if ( ($curId!=null) and ($obj instanceof SqlElement) ) {
			$obj->getSqlElement();
			// set the reference data
			// build query
			$query = "select id from " . $obj->getDatabaseTableName()
			. ' where refId =' . $curId.
       " and refType ='" . get_class($this) . "'" ;      
			foreach ($obj->getDatabaseCriteria() as $critFld=>$critVal) {
			  $query .= ' and ' . $critFld . ' = ' . Sql::str($critVal);
			}
			$result = Sql::query($query);
			// if no element in database, will return empty object
			//
			// IMPROVEMENT ON V4.2.0 : attention, this may return results when it did not previously...
			if (Sql::$lastQueryNbRows > 0) {
				$line = Sql::fetchLine($result);
				// get all data fetched for the dependant element
				$obj->id=$line['id'];
				$obj->getSqlElement();				
			}
		}
		// set the dependant element	
		return $obj;
	}

	/** ==========================================================================
	 * retrieve objects included in an object from the Database
	 * @param $objClass the name of the class of the included object
	 * @return an array ob objects
	 */
	private function getDependantSqlElements($objClass) {
		$curId=$this->id;
		$obj = new $objClass;
		$list=array();
		//$obj->refId=$this->id;
		//$obj->refType=get_class($this);
		// If id is set, get the elements from Database
		if ( ($curId != NULL) and ($obj instanceof SqlElement) ) {
			// set the reference data
			// build query
			$query = "select id from " . $obj->getDatabaseTableName();
			if (property_exists($objClass, 'id'.get_class($this))) {
				$query .= " where " . $obj->getDatabaseColumnName('id' . get_class($this)) . "= " . Sql::str($curId) . " ";
			} else {
				$refType=get_class($this);
				if ($refType=='TicketSimple') {
					$refType='Ticket';
				}
				$query .= " where refId =" . Sql::str($curId) . " "
				. " and refType ='" . $refType . "'";
			}
			$query .= " order by id desc ";
			$result = Sql::query($query);
			// if no element in database, will return empty array
			if (Sql::$lastQueryNbRows > 0) {
				while ($line = Sql::fetchLine($result)) {
					$newObj = new $objClass;
					$newObj->id=$line['id'];
					$newObj->getSqlElement();
					$list[]=$newObj;
				}
			}
		}
		// set the dependant element
		return $list;
	}

	// ============================================================================**********
	// GET STATIC DATA FUNCTIONS
	// ============================================================================**********

	/** ========================================================================
	 * return the type of a column depending on its name
	 * @param $colName the name of the column
	 * @return the type of the data
	 */
	public function getDataType($colName) {
		$colName=strtolower($colName);
		$formatList=self::getFormatList(get_class($this));
		if ( ! array_key_exists($colName, $formatList) ) {
			foreach ($this as $col=>$val) {
				if (is_object($val)) {
					$subObj=new $col();
					$subType=$subObj->getDataType($colName);
					if ($subType!='undefined') {
						return $subType;
					}
				}
			}
			return 'undefined';
		}	
		$fmt=$formatList[$colName];
		$split=preg_split('/[()\s]+/',$fmt,2);
		return $split[0];
	}

	/** ========================================================================
	 * return the length (max) of a column depending on its name
	 * @param $colName the name of the column
	 * @return the type of the data
	 */
	public function getDataLength($colName) {
		$colName=strtolower($colName);
		$formatList=self::getFormatList(get_class($this));
		if ( ! array_key_exists($colName, $formatList) ) {
			return '';
		}
		$fmt=$formatList[$colName];
		$split=preg_split('/[()\s]+/',$fmt,3);
		$type = $split[0];
		if ($type=='date') {
			return '10';
		} else if ($type=='time') {
			return '5';
		} else if ($type=='timestamp' or $type=='datetime') {
			return 19;
		} else if ($type=='double') {
			return 2;
		} else if ($type=='text') {
			return 65535;
		} else if ($type=='mediumtext') {
				return 16777215;
		} else if ($type=='longtext') {
				return 4294967295;
		} else {
			if (count($split)>=2) {
				return $split[1];
			} else {
				return 0;
			}
		}
	}

	/** ========================================================================
	 * return the generic layout for grit list
	 * @return the layout from static data
	 */
	public function getLayout() {
		$result="";
		$columns=ColumnSelector::getColumnsList(get_class($this));
		$totWidth=0;
		foreach ($columns as $col) {
			if ($col->hidden) {
				continue;
			}
			if ( ! self::isVisibleField($col->attribute) ) {
				continue;
			}
			$result.='<th';
			$result.=' field="'.htmlEncode($col->field).'"';
			$result.=' width="'.(($col->field=='name')?'auto':$col->widthPct.'%').'"';
			$result.=($col->formatter)?' formatter="'.htmlEncode($col->formatter).'"':'';
			$result.=($col->_from)?' from="'.$col->_from.'"':'';
			$result.=($col->hidden)?' hidden="true"':'';
			$result.='>'.$col->_displayName.'</th>'."\n";
			$totWidth+=($col->field=='name')?0:$col->widthPct;
		}
		if ($totWidth<90) {
			$autoWidth=100-$totWidth;
		} else {
			$autoWidth=10;
		}
		$result=str_replace('width="auto"', 'width="'.$autoWidth.'%"', $result);
		return $result;
	}

	/** ========================================================================
	 * return the generic attributes (required, disabled, ...) for a given field
	 * @return an array of fields  with specific attributes
	 */
	public function getFieldAttributes($fieldName) {
		$fieldsAttributes=$this->getStaticFieldsAttributes();
		if (array_key_exists($fieldName,$fieldsAttributes)) {
			return $fieldsAttributes[$fieldName];
		} else {
			return '';
		}
	}
	public function isAttributeSetToField($fieldName, $attribute) {
		if (strpos($this->getFieldAttributes($fieldName), $attribute)!==false) {
			return true;
		} else {
			return false;
		}
	}
	
	/** ========================================================================
	 * Return the default value for a given field
	 * @return string the name of the data table
	 */
	public function getDefaultValue($fieldName) {
	  $defaultValues=$this->getStaticDefaultValues();
	  if (array_key_exists($fieldName,$defaultValues)) {
	    if (substr($defaultValues[$fieldName],0,10)=='#$#EVAL#$#') {
	      $eval=substr($defaultValues[$fieldName],10);
        //if (strpos($eval,'return')===false) {
        //  $eval="return ".$eval;
        //}
	      $eval='$value='. str_replace("'",'"',$eval).";";
	      eval($eval);
	      return $value;
	    } else {
	      return $defaultValues[$fieldName];
	    }
	  } else {
	    return null;
	  }
	}
	/** ========================================================================
	 * Return the default value for a given field
	 * @return string the name of the data table
	 */
	public function setAllDefaultValues() {
	  $defaultValues=$this->getStaticDefaultValues();
	  foreach ($defaultValues as $field=>$value) {
	    if (! $this->id) {
	      $this->$field=$this->getDefaultValue($field);
	    } else if ($this->$field===null) {
	      if ($this->isAttributeSetToField($field, 'required')) {
	        $this->$field=$this->getDefaultValue($field);
	      }
	    }
	  } 
	}

	/** ========================================================================
	 * Return the name of the table in the database
	 * Default is the name of the class (lowercase)
	 * May be overloaded for some classes, who reference a table different
	 * from class name
	 * @return string the name of the data table
	 */
	public function getDatabaseTableName() {
		return $this->getStaticDatabaseTableName();
	}

	/** ========================================================================
	 * Return the name of the column name in the table in the database
	 * Default is the name of the field
	 * May be overloaded for some fields of some classes
	 * @return string the name of the data column
	 */
	public function getDatabaseColumnName($field) {
		$colName=$field;
		$databaseColumnName=$this->getStaticDatabaseColumnName();
		if (array_key_exists($field,$databaseColumnName)) {
			$colName=$databaseColumnName[$field];
		} //else {
		//return Sql::str($field); // Must not be quoted : would return 'name' (with quotes)
		//return $field;
		//}
		//if (Sql::isPgsql() ) {
		//	$colName=strtolower($colName);
		//}
		return $colName;
	}

	/** ========================================================================
	 * Return the name of the field in the object from the column name in the
	 * table in the database
	 * (it is the reversed method from getDatabaseColumnName()
	 * Default is the name of the field
	 * May be overloaded for some fields of some classes
	 * @return string the name of the data column
	 */
	public function getDatabaseColumnNameReversed($field) {
		$databaseColumnName=$this->getStaticDatabaseColumnName();
		$databaseColumnNameReversed=array_flip(array_map('strtolower',$databaseColumnName));
		$field=strtolower($field);
		if (array_key_exists(strtolower($field),$databaseColumnNameReversed)) {
			return $databaseColumnNameReversed[$field];
		} else {
			return $field;
		}
	}

	/** ========================================================================
	 * Return the additional criteria to select class elements in the database
	 * Default is empty string
	 * May be overloaded for some classes, which reference a table different
	 * from class name
	 * @return array listing criteria
	 */
	public function getDatabaseCriteria() {
		return $this->getStaticDatabaseCriteria();
	}

	/** ============================================================================
	 * Return the caption of a field using i18n translation
	 * @param $fld the name of the field
	 * @return the translated colXxxxxx value
	 */
	function getColCaption($fld) {
		if (! $fld or $fld=='') {
			return '';
		}
		$colCaptionTransposition=$this->getStaticColCaptionTransposition($fld);
		if (array_key_exists($fld,$colCaptionTransposition)) {
			$fldName=$colCaptionTransposition[$fld];
		} else {
			$fldName=$fld;
		}
		return i18n('col' . ucfirst($fldName));
	}

	public function getLowercaseFieldsArray() {
		$arrayFields=array();
		foreach ($this as $fld=>$fldVal) {
			if (is_object($this->$fld)) {
				$arrayFields=array_merge($arrayFields,$this->$fld->getLowercaseFieldsArray());
			} else {
				$arrayFields[strtolower($fld)]=$fld;
			}
		}
		return $arrayFields;
	}

	public function getFieldsArray($limitToExportableFields=false) {
		$arrayFields=array();
		foreach ($this as $fld=>$fldVal) {
			if (is_object($this->$fld)) {
				$arrayFields=array_merge($arrayFields,$this->$fld->getFieldsArray($limitToExportableFields));
			} else {
			  if ($limitToExportableFields) {
			    if ($this->isAttributeSetToField($fld,'hidden') and ! $this->isAttributeSetToField($fld,'forceExport')) {
			      continue;
			    }
			  }
				$arrayFields[$fld]=$fld;
			}
		}
		return $arrayFields;
	}
	/** =========================================================================
	 * Return the list of fields format and store it in static array of formats
	 * to be able to fetch it again without requesting it from database
	 * @param $class the class of the object
	 * @return the format list
	 */
	private static function getFormatList($class) {
		if (count(self::$_tablesFormatList)==0) { // if static value not initalized, try and retrieve from session
		  $fromSession=getSessionValue('_tablesFormatList');
		  if ($fromSession==null) {
		    setSessionValue('_tablesFormatList', self::$_tablesFormatList);
		  } else {
		    self::$_tablesFormatList=$fromSession;
		  }
		}
	  if (array_key_exists($class, self::$_tablesFormatList)) {
			return self::$_tablesFormatList[$class];
		}
		$obj=new $class();
		$formatList= array();
		$query="desc " . $obj->getDatabaseTableName();
		if (Sql::isPgsql()) {
			$query="SELECT a.attname as field, pg_catalog.format_type(a.atttypid, a.atttypmod) as type"
			. " FROM pg_catalog.pg_attribute a "
			. " WHERE a.attrelid = (SELECT oid FROM pg_catalog.pg_class WHERE relname='".$obj->getDatabaseTableName()."')"
			. " AND a.attnum > 0 AND NOT a.attisdropped"
			. " ORDER BY a.attnum";
		}
		$result=Sql::query($query);
		while ( $line = Sql::fetchLine($result) ) {
			$fieldName=(isset($line['Field']))?$line['Field']:$line['field'];
			$fieldName=$obj->getDatabaseColumnNameReversed($fieldName);
			$type=(isset($line['Type']))?$line['Type']:$line['type'];
			$from=array();                               $to=array();
			if (Sql::isPgsql()) {		  
				$from[]='integer';                           $to[]='int(12)';
				$from[]='numeric(12,0)';                     $to[]='int(12)';
				$from[]='numeric(5,0)';                      $to[]='int(5)';
				$from[]='numeric(3,0)';                      $to[]='int(3)';
				$from[]='numeric(1,0)';                      $to[]='int(1)';
				$from[]=' without time zone';                $to[]='';
				$from[]='character varying';                 $to[]='varchar';
				$from[]='numeric';                           $to[]='decimal';
				$from[]='timestamp';                         $to[]='datetime';
			} 
			$from[]='mediumtext';                          $to[]='varchar(16777215)';
			$from[]='longtext';                            $to[]='varchar(4294967295)';
			$from[]='text';                                $to[]='varchar(65535)';
			
			$type=str_ireplace($from, $to, $type);
			$formatList[strtolower($fieldName)] = $type;
		}
		self::$_tablesFormatList[$class]=$formatList;
		setSessionValue('_tablesFormatList', self::$_tablesFormatList); // store session value (as initalized)
		return $formatList;
	}

	/** ========================================================================
	 * return the generic layout
	 * @return the layout from static data
	 */
	protected function getStaticLayout() {
		return self::$_layout;
	}

	/** ==========================================================================
	 * Return the generic fieldsAttributes
	 * @return the layout
	 */
	protected function getStaticFieldsAttributes() {
		return self::$_fieldsAttributes;
	}
	
	/** ==========================================================================
	 * Return the generic defaultValues
	 * @return the layout
	 */
	protected function getStaticDefaultValues() {
	  return self::$_defaultValues;
	}

	/** ==========================================================================
	 * Return the generic databaseTableName
	 * @return the layout
	 */
	protected function getStaticDatabaseTableName() {
		$paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
		return strtolower($paramDbPrefix . get_class($this));
	}

	/** ========================================================================
	 * Return the generic databaseTableName
	 * @return the databaseTableName
	 */
	protected function getStaticDatabaseColumnName() {
		return array();
	}

	/** ========================================================================
	 * Return the generic database criteria
	 * @return the databaseTableName
	 */
	protected function getStaticDatabaseCriteria() {
		return array();
	}

	/** ============================================================================
	 * Return the specific colCaptionTransposition
	 * @return the colCaptionTransposition
	 */
	protected function getStaticColCaptionTransposition($fld=null) {
		return array();
	}

	// ============================================================================**********
	// GET VALIDATION SCRIPT
	// ============================================================================**********

	/** ========================================================================
	 * return generic javascript to be executed on validation of field
	 * @param $colName the name of the column
	 * @return the javascript code
	 */
	public function getValidationScript($colName) {
		$colScript = '';
		$posDate=strlen($colName)-4;
		if (substr($colName,0,2)=='id' and strlen($colName)>2 ) {  // SELECT => onChange
			$colScript .= '<script type="dojo/connect" event="onChange" args="evt">';
			$colScript .= '  if (this.value!=null && this.value!="") { ';
			$colScript .= '    formChanged();';
			$colScript .= '  }';
			//if ( get_class($this)=='Activity' or get_class($this)=='Ticket' or get_class($this)=='Milestone' ) {
			if ( get_class($this)!='Project' and get_class($this)!='Affectation' ) {
				if ($colName=='idProject' and property_exists($this,'idActivity')) {
					$colScript .= '   refreshList("idActivity","idProject", this.value);';
				}
				if ($colName=='idProject' and property_exists($this,'idResource')) {
				  $required='false';
				  if ($this->isAttributeSetToField('idResource', 'required')) $required='true';
					$colScript .= '   refreshList("idResource","idProject", this.value, "' . htmlEncode($this->idResource). '",null,'.$required.',null,null,"'.get_class($this).'");';
				}
				if ($colName=='idProject' and property_exists($this,'idProduct')) {
					$colScript .= '   refreshList("idProduct","idProject", this.value, dijit.byId("idProduct").get("value"));';
				}
				if ($colName=='idProject' and property_exists($this,'idComponent')) {
				  $colScript .= '   if (dijit.byId("idProduct") && trim(dijit.byId("idProduct").get("value"))) {';
				  //$colScript .= '     refreshList("idComponent","idProduct", dijit.byId("idProduct").get("value"), dijit.byId("idComponent").get("value"));';
				  $colScript .= '   } else {';
				  $colScript .= '     refreshList("idComponent","idProject", this.value, dijit.byId("idComponent").get("value"));';
				  $colScript .= '   }';
				}
				if ($colName=='idProject' and property_exists($this,'idProductOrComponent')) {
				  $colScript .= '   refreshList("idProductOrComponent","idProject", this.value, dijit.byId("idProductOrComponent").get("value"));';
				}
				if ($colName=='idProject' and property_exists($this,'id'.get_class($this).'Type')) {
				  $colScript .= '   refreshList("id'.get_class($this).'Type","idProject", this.value, dijit.byId("id'.get_class($this).'Type").get("value"),null,true);';
				}
				$arrVers=array('idVersion','idProductVersion',
				    'idOriginalVersion','idOriginalProductVersion','idOriginalComponentVersion',
				    'idTargetVersion','idTargetProductVersion','idTargetComponentVersion',
				    'idTestCase','idRequirement');
				$arrVersProd=array('idVersion','idProductVersion',
				    'idOriginalVersion','idOriginalProductVersion',
				    'idTargetVersion','idTargetProductVersion',
				    'idTestCase','idRequirement');
				$arrVersComp=array('idVersion','idComponentVersion',
				    'idOriginalComponentVersion',
				    'idTargetComponentVersion',
				    );
				$versionExists=false;
				foreach ($arrVers as $vers) {
					if (property_exists($this,$vers)) {
						$versionExists=true;
					}
				}
				if ($colName=='idProject' and $versionExists) {
				  foreach ($arrVersComp as $vers) {
				    if (property_exists($this,$vers)) {
				      $versProd=str_replace('Component', 'Product', $vers);
				      $colScript.="if (dijit.byId('$versProd') && trim(dijit.byId('$versProd').get('value')) ) {";
				      //$colScript.="refreshList('$vers','$versProd', dijit.byId('$versProd').get('value'));";
				      $colScript.=" } else if (dijit.byId('idComponent') && trim(dijit.byId('idComponent').get('value'))) {";
				      //$colScript.="refreshList('$vers','idComponent', trim(dijit.byId('idComponent').get('value')));";
				      $colScript.=" } else {";
				      $colScript.="refreshList('$vers','idProject', this.value);";
				      $colScript.=" }";
				    }
				  }
				  foreach ($arrVersProd as $vers) {
				    if (property_exists($this,$vers)) {
				      $colScript.=" if (dijit.byId('idProduct') && trim(dijit.byId('idProduct').get('value'))) {";
				      //$colScript.="refreshList('$vers','idProduct', trim(dijit.byId('idProduct').get('value')));";
				      $colScript.=" } else {";
				      $colScript.="refreshList('$vers','idProject', this.value);";
				      $colScript.=" }";
				    }
				  }
				}
				if ($colName=='idProduct' and property_exists($this,'idComponent') ) {
				  $colScript.="if (trim(this.value)) {";
				  $colScript.="refreshList('idComponent','idProduct', this.value);";
				  $colScript.="} else {";
				  if (property_exists($this,'idProject')) {
				    $colScript.="refreshList('idComponent','idProject', dijit.byId('idProject').get('value'));";
				  }
				  $colScript.="}";
				}
				if ($colName=='idProduct' and $versionExists) {
				  foreach ($arrVersProd as $vers) {
				    if (property_exists($this,$vers)) {
				      $colScript.="if (trim(dijit.byId('idProduct').get('value'))) {";
				      $colScript.="refreshList('$vers','idProduct', this.value);";
				      $colScript.="} else {";
				      if (property_exists($this,'idProject')) {
				      $colScript.="refreshList('$vers','idProject', dijit.byId('idProject').get('value'));";
				      }
				      $colScript.="}";
				    }
				  }
				}
				if ($colName=='idComponent' and $versionExists) {
				  foreach ($arrVersComp as $vers) {
				    if (property_exists($this,$vers)) {
				      $versProd=str_replace('Component', 'Product', $vers);
				      $colScript.="if (dijit.byId('$versProd') && trim(dijit.byId('$versProd').get('value'))) {";
				      $colScript.="  if (trim(this.value)) {";
				      $colScript.="  refreshList('$vers','idProductVersion', dijit.byId('$versProd').get('value'), null, null,null,'idComponent',this.value);";
				      $colScript.="  } else {";
				      $colScript.="  refreshList('$vers','idProductVersion', dijit.byId('$versProd').get('value'));";
				      $colScript.="  }";
				      $colScript.="} else if (trim(this.value)) {";
				      $colScript.="refreshList('$vers','idComponent', this.value);";
				      $colScript.="} else {";
				      if (property_exists($this,'idProject')) {
				        $colScript.="refreshList('$vers','idProject', dijit.byId('idProject').get('value'));";
				      }
				      $colScript.="}";
				    }
				  }
				}
				if (substr($colName,-14)=='ProductVersion') {
				  $versComp=str_replace('Product', 'Component', $colName);
				  if (property_exists($this,$versComp)) {
				    $colScript.="if (trim(this.value)) {";
				    if (property_exists($this,'idComponent')) {
				      $colScript.="  if (dijit.byId('idComponent') && trim(dijit.byId('idComponent').get('value')) ) {";
				      $colScript.="refreshList('$versComp','idProductVersion', this.value, null, null, null,'idComponent', dijit.byId('idComponent').get('value'));";
				      $colScript.=" } else {";
				      $colScript.="refreshList('$versComp','idProductVersion', this.value);";
				      $colScript.=" }";
				    }
				    if (property_exists($this,'idComponent')) {
				      $colScript.="} else if (dijit.byId('idComponent') && trim(dijit.byId('idComponent').get('value')) ) {";
				      $colScript.="refreshList('$versComp','idComponent', dijit.byId('idComponent').get('value'));";
				    }
				    $colScript.="} else {";
				    if (property_exists($this,'idProject')) {
				      $colScript.="refreshList('$versComp','idProject', dijit.byId('idProject').get('value'));";
				    }
				    $colScript.="}";
				  }				 
					$colScript .= 'if (! trim(dijit.byId("idProduct").get("value")) ) {';
					$colScript .= '   setProductValueFromVersion("idProduct",this.value);';
					$colScript .= '}';
				}
				if (substr($colName,-16)=='ComponentVersion') {
				  $colScript .= 'if (! trim(dijit.byId("idComponent").get("value")) ) {';
				  $colScript .= '   setProductValueFromVersion("idComponent",this.value);';
				  $colScript .= '}';
				}
				if ($colName=='idProject' and property_exists($this,'idContact')) {
					$colScript .= '   refreshList("idContact","idProject", this.value);';
				}
				if ($colName=='idProject' and property_exists($this,'idTicket')) {
					$colScript .= '   refreshList("idTicket","idProject", this.value);';
				}
				if ($colName=='idProject' and property_exists($this,'idUser')) {
					$colScript .= '   refreshList("idUser","idProject", this.value);';
				}
			}
			if ($colName=='idStatus' or $colName=='id'.get_class($this).'Type' or substr($colName,-12)=='PlanningMode') {
			  $colScript .= '   getExtraRequiredFields();';
			}	
		  if ($colName=='id'.get_class($this).'Type') {
		    $colScript .= '   getExtraHiddenFields(this.value);';
		  }
			$colScript .= '</script>';
		}
		if (substr($colName,$posDate,4)=='Date') {  // Date => onChange
			$colScript .= '<script type="dojo/connect" event="onChange">';
			$colScript .= '  if (this.value!=null && this.value!="") { ';
			$colScript .= '    formChanged();';
			$colScript .= '  }';
			$colScript .= '</script>';
		}
		if ( ! (substr($colName,0,2)=='id' and strlen($colName)>2 ) ) { // OTHER => onKeyPress
			$colScript .= '<script type="dojo/method" event="onKeyDown" args="event">'; // V4.2 Changed onKeyPress to onKeyDown because was not triggered
			$colScript .= '  if (isEditingKey(event)) {';
			$colScript .= '    formChanged();';
			$colScript .= '  }';
			$colScript .= '</script>';
		}
		if ($colName=="idStatus") {
			$colScript .= '<script type="dojo/connect" event="onChange" >';
			if (property_exists($this, 'idle') and get_class($this)!='StatusMail') {
				$colScript .= htmlGetJsTable('Status', 'setIdleStatus', 'tabStatusIdle');
				$colScript .= '  var setIdle=0;';
				$colScript .= '  var filterStatusIdle=dojo.filter(tabStatusIdle, function(item){return item.id==dijit.byId("idStatus").value;});';
				$colScript .= '  dojo.forEach(filterStatusIdle, function(item, i) {setIdle=item.setIdleStatus;});';
				$colScript .= '  if (setIdle==1) {';
				$colScript .= '    dijit.byId("idle").set("checked", true);';
				$colScript .= '  } else {';
				$colScript .= '    dijit.byId("idle").set("checked", false);';
				$colScript .= '  }';
			}
			if (property_exists($this, 'done')) {
				$colScript .= htmlGetJsTable('Status', 'setDoneStatus', 'tabStatusDone');
				$colScript .= '  var setDone=0;';
				$colScript .= '  var filterStatusDone=dojo.filter(tabStatusDone, function(item){return item.id==dijit.byId("idStatus").value;});';
				$colScript .= '  dojo.forEach(filterStatusDone, function(item, i) {setDone=item.setDoneStatus;});';
				$colScript .= '  if (setDone==1) {';
				$colScript .= '    dijit.byId("done").set("checked", true);';
				$colScript .= '  } else {';
				$colScript .= '    dijit.byId("done").set("checked", false);';
				$colScript .= '  }';
			}
			if (property_exists($this, 'handled')) {
				$colScript .= htmlGetJsTable('Status', 'setHandledStatus', 'tabStatusHandled');
				$colScript .= '  var setHandled=0;';
				$colScript .= '  var filterStatusHandled=dojo.filter(tabStatusHandled, function(item){return item.id==dijit.byId("idStatus").value;});';
				$colScript .= '  dojo.forEach(filterStatusHandled, function(item, i) {setHandled=item.setHandledStatus;});';
				$colScript .= '  if (setHandled==1) {';
				$colScript .= '    dijit.byId("handled").set("checked", true);';
				$colScript .= '  } else {';
				$colScript .= '    dijit.byId("handled").set("checked", false);';
				$colScript .= '  }';
			}
		  if (property_exists($this, 'cancelled')) {
        $colScript .= htmlGetJsTable('Status', 'setCancelledStatus', 'tabStatusCancelled');
        $colScript .= '  var setCancelled=0;';
        $colScript .= '  var filterStatusCancelled=dojo.filter(tabStatusCancelled, function(item){return item.id==dijit.byId("idStatus").value;});';
        $colScript .= '  dojo.forEach(filterStatusCancelled, function(item, i) {setCancelled=item.setCancelledStatus;});';
        $colScript .= '  if (setCancelled==1) {';
        $colScript .= '    dijit.byId("cancelled").set("checked", true);';
        $colScript .= '  } else {';
        $colScript .= '    dijit.byId("cancelled").set("checked", false);';
        $colScript .= '  }';
      }
			$colScript .= '  formChanged();';
			$colScript .= '</script>';
		} else if ($colName=="idResolution") {
		  $colScript .= '<script type="dojo/connect" event="onChange" >';
		  if (property_exists($this, 'solved')) {
  		  $colScript .= htmlGetJsTable('Resolution', 'solved', 'tabResolutionSolved');
  		  $colScript .= '  var solved=0;';
  		  $colScript .= '  var filterResolutionSolved=dojo.filter(tabResolutionSolved, function(item){return item.id==dijit.byId("idResolution").value;});';
  		  $colScript .= '  dojo.forEach(filterResolutionSolved, function(item, i) {solved=item.solved;});';
  		  $colScript .= '  if (solved==1) {';
  		  $colScript .= '    dijit.byId("solved").set("checked", true);';
  		  $colScript .= '  } else {';
  		  $colScript .= '    dijit.byId("solved").set("checked", false);';
  		  $colScript .= '  }';
  		}
  		$colScript .= '  formChanged();';
  		$colScript .= '</script>';
	  } else if ($colName=="idle") {
			$colScript .= '<script type="dojo/connect" event="onChange" >';
			$colScript .= '  if (this.checked) { ';
			if (property_exists($this, 'idleDateTime')) {
				$colScript .= '    if (! dijit.byId("idleDateTime").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("idleDateTime").set("value", curDate); ';
				$colScript .= '      dijit.byId("idleDateTimeBis").set("value", curDate); ';
				$colScript .= '    }';
			}
			if (property_exists($this, 'idleDate')) {
				$colScript .= '    if (! dijit.byId("idleDate").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("idleDate").set("value", curDate); ';
				$colScript .= '    }';
			}
			if (property_exists($this, 'done')) {
				$colScript .= '    if (! dijit.byId("done").get("checked")) {';
				$colScript .= '      dijit.byId("done").set("checked", true);';
				$colScript .= '    }';
			}
			if (property_exists($this, 'handled')) {
				$colScript .= '    if (! dijit.byId("handled").get("checked")) {';
				$colScript .= '      dijit.byId("handled").set("checked", true);';
				$colScript .= '    }';
			}
			$colScript .= '  } else {';
			if (property_exists($this, 'idleDateTime')) {
				$colScript .= '    dijit.byId("idleDateTime").set("value", null); ';
				$colScript .= '    dijit.byId("idleDateTimeBis").set("value", null); ';
			}
			if (property_exists($this, 'idleDate')) {
				$colScript .= '    dijit.byId("idleDate").set("value", null); ';
			}
			$colScript .= '  } ';
			$colScript .= '  formChanged();';
			$colScript .= '</script>';
		} else if ($colName=="done") {
			$colScript .= '<script type="dojo/connect" event="onChange" >';
			$colScript .= '  if (this.checked) { ';
			if (property_exists($this, 'doneDateTime')) {
				$colScript .= '    if (! dijit.byId("doneDateTime").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("doneDateTime").set("value", curDate); ';
				$colScript .= '      dijit.byId("doneDateTimeBis").set("value", curDate); ';
				$colScript .= '    }';
			}
			if (property_exists($this, 'doneDate')) {
				$colScript .= '    if (! dijit.byId("doneDate").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("doneDate").set("value", curDate); ';
				$colScript .= '    }';
			}
			if (property_exists($this, 'handled')) {
				$colScript .= '    if (! dijit.byId("handled").get("checked")) {';
				$colScript .= '      dijit.byId("handled").set("checked", true);';
				$colScript .= '    }';
			}
			$colScript .= '  } else {';
			if (property_exists($this, 'doneDateTime')) {
				$colScript .= '    dijit.byId("doneDateTime").set("value", null); ';
				$colScript .= '    dijit.byId("doneDateTimeBis").set("value", null); ';
			}
			if (property_exists($this, 'doneDate')) {
				$colScript .= '    dijit.byId("doneDate").set("value", null); ';
			}
			if (property_exists($this, 'idle')) {
				$colScript .= '    if (dijit.byId("idle").get("checked")) {';
				$colScript .= '      dijit.byId("idle").set("checked", false);';
				$colScript .= '    }';
			}
			$colScript .= '  } ';
			$colScript .= '  formChanged();';
			$colScript .= '</script>';
		} else if ($colName=="handled") {
			$colScript .= '<script type="dojo/connect" event="onChange" >';
			$colScript .= '  if (this.checked) { ';
			if (property_exists($this, 'handledDateTime')) {
				$colScript .= '    if ( ! dijit.byId("handledDateTime").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("handledDateTime").set("value", curDate); ';
				$colScript .= '      dijit.byId("handledDateTimeBis").set("value", curDate); ';
				$colScript .= '    }';
			}
			if (property_exists($this, 'handledDate')) {
				$colScript .= '    if (! dijit.byId("handledDate").get("value")) {';
				$colScript .= '      var curDate = new Date();';
				$colScript .= '      dijit.byId("handledDate").set("value", curDate); ';
				$colScript .= '    }';
			}
			$colScript .= '  } else {';
			if (property_exists($this, 'handledDateTime')) {
				$colScript .= '    dijit.byId("handledDateTime").set("value", null); ';
				$colScript .= '    dijit.byId("handledDateTimeBis").set("value", null); ';
			}
			if (property_exists($this, 'handledDate')) {
				$colScript .= '    dijit.byId("handledDate").set("value", null); ';
			}
			if (property_exists($this, 'done')) {
				$colScript .= '    if (dijit.byId("done").get("checked")) {';
				$colScript .= '      dijit.byId("done").set("checked", false);';
				$colScript .= '    }';
			}
			if (property_exists($this, 'idle')) {
				$colScript .= '    if (dijit.byId("idle").get("checked")) {';
				$colScript .= '      dijit.byId("idle").set("checked", false);';
				$colScript .= '    }';
			}
			$colScript .= '  } ';
			$colScript .= '  formChanged();';
			$colScript .= '</script>';
		}
		return $colScript;
	}

	// ============================================================================**********
	// MISCELLANOUS FUNCTIONS
	// ============================================================================**********

	/** =========================================================================
	 * Draw a specific item for a given class.
	 * Should always be implemented in the corresponding class.
	 * Here is alway an error.
	 * @param $item the item
	 * @return a message to draw (to echo) : always an error in this class,
	 *  must be redefined in the inherited class
	 */
	public function drawSpecificItem($item){
		return "No specific item " . $item . " for object " . get_class($this);
	}

	public function drawCalculatedItem($item){
		return "No calculated item " . $item . " for object " . get_class($this);
	}
	/** =========================================================================
	 * Indicate if a property of is translatable
	 * @param $col the nale of the property
	 * @return a boolean
	 */
	public function isFieldTranslatable($col) {
		$testField='_is' . ucfirst($col) . 'Translatable';
		if (isset($this->{$testField})) {
			if ($this->{$testField}) {
				return true;
			} else {
				return false;
			}
		}
	}

	/** =========================================================================
	 * control data corresponding to Model constraints, before saving an object
	 * @param void
	 * @return "OK" if controls are good or an error message
	 *  must be redefined in the inherited class
	 */
	public function control(){
		//traceLog('control (for ' . get_class($this) . ' #' . $this->id . ')');
		global $cronnedScript, $loginSave;
		$result="";
		$right="";
	  // Manage Exceptions
		if (get_class($this)=='Alert' or get_class($this)=='Mail' 
		 or get_class($this)=='Audit' or get_class($this)=='AuditSummary'
		 or get_class($this)=='ColumnSelector') {
			$right='YES';
		} else if (isset($cronnedScript) and $cronnedScript==true) { // Cronned script can do everything
			$right='YES';
	  } else if (isset($loginSave) and $loginSave==true) { // User->save during autenticate can do everything
        $right='YES';
		} else if (get_class($this)=='User') { // User can change his own data (to be able to change password)
			if (getSessionUser()->id or (getSessionUser()->name and getSessionUser()->isLdap and getSessionUser()->name=$this->name)) {
		    $usr=getSessionUser();
				if ($this->id==$usr->id) {
					$right='YES';
				}
			}
		} else if (get_class($this)=='Affectation' and property_exists($this, '_automaticCreation') and $this->_automaticCreation) {
		  $right='YES';
		}
		if ($right!='YES' and get_class($this)=='Project') {
		  if ($this->idProject) {
		    $proj=new Project($this->idProject,true);
		    $right=securityGetAccessRightYesNo('menuProject', 'update', $proj);
		  } else {
		    //  $right=securityGetAccessRightYesNo('menu' . get_class($this), (($this->id)?'update':'create'), $this); // This shoulod be applied to avoid creation at root
		    if ($this->id) {
		      $right=securityGetAccessRightYesNo('menu' . get_class($this), 'update', $this);
		    } else {
          $right=securityGetAccessRightYesNo('menu' . get_class($this), 'create');
		    }
		  }
		} else if ($right!='YES' and get_class($this)=='Affectation') {
		  $prj=new Project($this->idProject,true);
		  $right=securityGetAccessRightYesNo('menuProject', 'update', $prj);
		} else if ($right!='YES') {
		  $right=securityGetAccessRightYesNo('menu' . get_class($this), (($this->id)?'update':'create'), $this);
		}
		if ($right!='YES') {
			$result.='<br/>' . i18n('error'.(($this->id)?'Update':'Create').'Rights');
			return $result;
		}
		$isCopy=false;
		if (property_exists($this,'idStatus') and $this->idStatus) {
  		$status=new Status($this->idStatus);
  		if ($status->isCopyStatus) {
  		  $isCopy=true;
  		}
		}
		foreach ($this as $col => $val) {
			$dataType=$this->getDataType($col);
			$dataLength=$this->getDataLength($col);
			if (substr($col,0,1)!='_') {
				if (ucfirst($col) == $col and is_object($val)) {
					$subResult=$val->control();
					if ($subResult!='OK') {
						$result.= $subResult;
					}
				} else {
					// check if required
					if (strpos($this->getFieldAttributes($col), 'required')!==false and !$isCopy) {
						if (!$val) {
							$result.='<br/>' . i18n('messageMandatory',array($this->getColCaption($col)));
						} else if (trim($val)==''){
							$result.='<br/>' . i18n('messageMandatory',array($this->getColCaption($col)));
						}
					}
					if ($dataType=='datetime') {
						if (strlen($val)==9) {
							$result.='<br/>' . i18n('messageDateMandatoryWithTime',array(i18n('col' . ucfirst($col))));
						}
					}
					if ($dataType=='date' and $val!='') {
						if (strlen($val)!=10 or substr($val,4,1)!='-' or substr($val,7,1)!='-') {
							$result.='<br/>' . i18n('messageInvalidDateNamed',array(i18n('col' . ucfirst($col))));
						}
					}
				}
			}
			if ($val and $col!='colRefName') {
				if ($dataType=='varchar') {
					if (strlen($val)>$dataLength) {
						$result.='<br/>' . i18n('messageTextTooLong',array(i18n('col' . ucfirst($col)),$dataLength));
					}
				} else if ($dataType=="int" or $dataType=="decimal") {
					if (trim($val) and ! is_numeric($val)) {
						$result.='<br/>' . i18n('messageInvalidNumeric',array(i18n('col' . ucfirst($col))));
					}
				}
			}
			if ($dataLength>4000) {
			  // Remove "\n" that have no use here
			  //$this->$col=str_replace( array("\n",'<div></div>'),
			  //                         array(' ', ''           ),
			  //    $val );
			  if ($val=='<div></div>') $val=null;
			  try {
			    $test=strip_tags($val);
			  } catch (Exception $e) {
			    $result.='<br/>' . i18n('messageInvalidHTML',array(i18n('col' . ucfirst($col))));
			  }
			  $val=htmlEncode($val,'formatted'); // Erase <script tags and erase value if messy tags
			}
		}
		$idType='id'.((get_class($this)=='TicketSimple')?'Ticket':get_class($this)).'Type';
		if (property_exists($this, $idType)) {
			$type=((get_class($this)=='TicketSimple')?'Ticket':get_class($this)).'Type';
			$objType=new $type($this->$idType);
			if (property_exists($objType, 'mandatoryDescription') and $objType->mandatoryDescription
			and property_exists($this, 'description')) {
				if (! $this->description) {
					$result.='<br/>' . i18n('messageMandatory',array($this->getColCaption('description')));
				}
			}
			if (property_exists($objType, 'mandatoryResourceOnHandled') and $objType->mandatoryResourceOnHandled
			and property_exists($this, 'idResource')
			and property_exists($this, 'handled')) {
				if ($this->handled and ! trim($this->idResource)) {
					$user=getSessionUser();
					if ($user->isResource and Parameter::getGlobalParameter('setResponsibleIfNeeded')!='NO') {
						$this->idResource=$user->id;
					} else {
						$result.='<br/>' . i18n('messageMandatory',array($this->getColCaption('idResource')));
					}
				}
			}
			if (property_exists($objType, 'mandatoryResultOnDone') and $objType->mandatoryResultOnDone
			and property_exists($this, 'result')
			and property_exists($this, 'done')) {
				if ($this->done and ! $this->result) {
					$result.='<br/>' . i18n('messageMandatory',array($this->getColCaption('result')));
				}
			}
		}
		// Control for Closed item that all items are closed
		if (property_exists($this,'idle') and $this->idle and $this->id) { // #1690 : should be possible to import closed items
			$relationShip=self::$_closeRelationShip;
			if (array_key_exists(get_class($this),$relationShip)) {
				$objects='';
				$error=false;
				foreach ( $relationShip[get_class($this)] as $object=>$mode) {
					if (($mode=='control' or $mode=='confirm') and property_exists($object,'idle')) {
						$where=null;
						$obj=new $object();
						$crit=array('id' . get_class($this) => $this->id, 'idle'=>'0');
						if (property_exists($obj, 'refType') and property_exists($obj,'refId')) {
						  if (property_exists($obj,'id' . get_class($this))) {
						    $crit=null;
						    $where="(id".get_class($this)."=".$this->id." or (refType='".get_class($this)."' and refId=".$this->id.")) and idle=0";
						  } else {
						    $crit=array("refType"=>get_class($this), "refId"=>$this->id, "idle"=>'0');
						  }
						}						
						if ($object=="Dependency") {
							$crit=null;
							$where="idle=0 and ((predecessorRefType='" . get_class($this) . "' and predecessorRefId=" . $this->id .")"
							. " or (successorRefType='" . get_class($this) . "' and successorRefId=" . $this->id ."))";
						}
						if ($object=="Link") {
							$crit=null;
							$where="idle=0 and ((ref1Type='" . get_class($this) . "' and ref1Id=" . Sql::fmtId($this->id) .")"
							. " or (ref2Type='" . get_class($this) . "' and ref2Id=" . Sql::fmtId($this->id) ."))";
						}
						$nb=$obj->countSqlElementsFromCriteria($crit,$where);
						if ($nb>0) {
							if ($mode=="control") $error=true;
							if ($mode=="confirm" and self::isSaveConfirmed()) {
								// If mode confirm and message of confirmation occured : OK
							} else {
								$objects.="<br/>&nbsp;-&nbsp;" . i18n($object) . " (" . $nb . ")";
							}
						}
					}
				}
				if ($objects!="") {
					if ($error) {
						$result.="<br/>" . i18n("errorControlClose") . $objects;
					} else {
						$result.='<input type="hidden" id="confirmControl" value="save" /><br/>' . i18n("confirmControlSave") . $objects;
					}
				}
			}
		}
		// control Workflow
		$class=get_class($this);
		$old=new $class($this->id);
		$fldType='id'.$class.'Type';

		if ( property_exists($class, 'idStatus') and property_exists($class, $fldType)
		and trim($old->idStatus) and trim($old->$fldType)
		and (trim($old->idStatus)!=trim($this->idStatus) or trim($old->$fldType)!=trim($this->$fldType) )
		and $old->id and $class!='Document') {
			$oldStat=new Status($old->idStatus);
			$statList=SqlList::getList('Status');
			$firstStat=key($statList);
			if (! $oldStat->isCopyStatus and ($this->idStatus!=$old->idStatus or $this->idStatus!=$firstStat) ) {
				$type=new Type($this->$fldType);
				$crit=array('idWorkflow'=>$type->idWorkflow,
	    	            'idStatusTo'=>$this->idStatus,
	    	            'idProfile'=>getSessionUser()->getProfile($this));
				if (trim($old->idStatus)!=trim($this->idStatus)) {
					$crit['idStatusFrom']=$old->idStatus;
				}
				$ws=new WorkflowStatus();
				$wsList=$ws->getSqlElementsFromCriteria($crit);
				$allowed=false;
				foreach ($wsList as $ws) {
					if ($ws->allowed) {
						$allowed=true;
						break;
					}
				}
				if (! $allowed) {
					$result.="<br/>" . i18n("errorWorflow");
				}
			}
		}
		// PlugIn Management
		$list=Plugin::getEventScripts('control',get_class($this));
		foreach ($list as $script) {
		  require $script; // execute code
		}
		if ($result=="") {
			$result='OK';
		}
		return $result;
	}

	/** =========================================================================
	 * control data corresponding to Model constraints, before deleting an object
	 * @param void
	 * @return "OK" if controls are good or an error message
	 *  must be redefined in the inherited class
	 */
	public function deleteControl(){
		$result="";
		$objects="";
		$right=securityGetAccessRightYesNo('menu' . get_class($this), 'delete', $this);
		if (get_class($this)=='Alert' or get_class($this)=='Mail'
		    or get_class($this)=='Audit' or get_class($this)=='AuditSummary'
		    or get_class($this)=='ColumnSelector') {
		  $right='YES';
		}
		if ($right!='YES') {
			$result.='<br/>' . i18n('errorDeleteRights');
			return $result;
		}
		$relationShip=self::$_relationShip;
		$canForceDelete=false;
	    if (getSessionUser()->id) {
		  $user=getSessionUser();
  		  $crit=array('idProfile'=>$user->getProfile($this), 'scope'=>'canForceDelete');
  		  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
  		  if ($habil and $habil->id and $habil->rightAccess=='1') {
  		    $canForceDelete=true;
  		  }
		}
		if (array_key_exists(get_class($this),$relationShip)) {
			$relations=$relationShip[get_class($this)];
			$error=false;
			foreach ($relations as $object=>$mode) {
			  if ($mode=="control" and $canForceDelete) {
			    $mode="confirm";
			  } else if ($mode=="controlStrict") {
			    $mode="control";
			  }  
				if ($mode=="control" or $mode=="confirm") {
					$where=null;
					$obj=new $object();
					$crit=array('id' . get_class($this) => $this->id);
					if (self::is_a($this,'Version')) {
					  $crit=null;
					  $where="(1=0";
					  $arrayVersion=array('idVersion', 
					      'idTargetVersion','idTargetProductVersion','idTargetComponentVersion', 
					      'idOriginalVersion','idOriginalProductVersion','idOriginalComponentVersion');
					  foreach ($arrayVersion as $vers) {
  					  if (property_exists($obj, $vers)) {
  					    $where.=" or ".$obj->getDatabaseColumnName($vers)."=".$this->id;
  					  }
					  }
					  $where.=")";
					} else if (property_exists($obj, 'refType') and property_exists($obj,'refId')) {
						if (property_exists($obj,'id' . get_class($this))) {
						  $crit=null;
						  $where="id".get_class($this)."=".$this->id." or (refType='".get_class($this)."' and refId=".$this->id.")";
						} else {
					    $crit=array("refType"=>get_class($this), "refId"=>$this->id);
						}
					}
					if ($object=="Dependency") {
						$crit=null;
						$where="(predecessorRefType='" . get_class($this) . "' and predecessorRefId=" . $this->id .")"
						. " or (successorRefType='" . get_class($this) . "' and successorRefId=" . $this->id .")";
					} else if ($object=="Link") {
						$crit=null;
						$where="(ref1Type='" . get_class($this) . "' and ref1Id=" . Sql::fmtId($this->id) .")"
						. " or (ref2Type='" . get_class($this) . "' and ref2Id=" . Sql::fmtId($this->id) .")";
					} else if (substr($object,-4)=='Type' ) {
					  $scope=substr($object,0,strlen($object)-4);
					  $crit['scope']=$scope;			  
					}
					$nb=$obj->countSqlElementsFromCriteria($crit,$where);
					if ($nb>0) {
						if ($mode=="control") $error=true;
						if ($mode=="confirm" and self::isDeleteConfirmed()) {
							// If mode confirm and message of confirmation occured : OK
						} else {
						  $objects.="<br/>&nbsp;-&nbsp;" . i18n($object) . " (" . $nb . ")";
						}
					}
				}
			}
			if ($objects!="") {
				if ($error) {
					$result.="<br/>" . i18n("errorControlDelete") . $objects;
				} else {
					$result.='<input type="hidden" id="confirmControl" value="delete" /><br/>' . i18n("confirmControlDelete") . $objects;
				}
			}
		}
		// PlugIn Management
		$list=Plugin::getEventScripts('deleteControl',get_class($this));
		foreach ($list as $script) {
		  require $script; // execute code
		}
		if ($result=="") {
			$result='OK';
		}
		return $result;
	}

	/** =========================================================================
	 * Return the menu string for the object (from its class)
	 * @param void
	 * @return a string
	 */
	public function getMenuClass() {
		return "menu" . get_class($this);
	}

	/** =========================================================================
	 * Send a mail on status change (if object is "mailable")
	 * @param void
	 * @return status of mail, if sent
	 */
	public function sendMailIfMailable($newItem=false, $statusChange=false, $directStatusMail=null,
	$responsibleChange=false, $noteAdd=false, $attachmentAdd=false,
	$noteChange=false, $descriptionChange=false, $resultChange=false, $assignmentAdd=false, $assignmentChange=false,
	$anyChange=false) {
		$objectClass=get_class($this);
		$idProject=($objectClass=='Project')?$this->id:((property_exists($this,'idProject'))?$this->idProject:null);
		if ($objectClass=='TicketSimple') {$objectClass='Ticket';}
		if ($objectClass=='History' or $objectClass=='Audit') {
			return false; // exit : not for History
		}
		$canBeSend=true;
		if($idProject){
		  $canBeSend=!SqlList::getFieldFromId("Project", $idProject, "isUnderConstruction");
		}
		$statusMailList=array();
		if ($directStatusMail) { // Direct Send Mail
			$statusMailList=array($directStatusMail->id => $directStatusMail);
		} else if($canBeSend)  {
			
			$mailable=SqlElement::getSingleSqlElementFromCriteria('Mailable', array('name'=>$objectClass));
			if (! $mailable or ! $mailable->id) {
				return false; // exit if not mailable object
			}
			
			//if (! property_exists($this, 'idStatus')) { #1977 : item can be mailable even if no status
				//return false; // exit if object has not idStatus
			//}
			//if (! $this->idStatus) { Not valid any more : for instance note add available for document even without status
			//	return false; // exit if status not set
			//}
			//$crit=array();
			//$crit['idStatus']=$this->idStatus;
			$crit="idle='0' and idMailable='" . $mailable->id . "' and ( false ";
			if ($statusChange and property_exists($this,'idStatus') and trim($this->idStatus)) {
				$crit.="  or idStatus='" . $this->idStatus . "' ";
			}
			if ($responsibleChange) {
				$crit.=" or idEvent='1' ";
			}
			if ($noteAdd) {
				$crit.=" or idEvent='2' ";
			}
			if ($attachmentAdd) {
				$crit.=" or idEvent='3' ";
			}
			if ($noteChange) {
				$crit.=" or idEvent='4' ";
			}
			if ($descriptionChange) {
				$crit.=" or idEvent='5' ";
			}
			if ($resultChange) {
				$crit.=" or idEvent='6' ";
			}
			if ($assignmentAdd) {
				$crit.=" or idEvent='7' ";
			}
			if ($assignmentChange) {
				$crit.=" or idEvent='8' ";
			}
			if ($anyChange) {
				$crit.=" or idEvent='9' ";
			}
			$crit.=")";
			$statusMail=new StatusMail();
			$statusMailList=$statusMail->getSqlElementsFromCriteria(null,false, $crit);
		}
		if (count($statusMailList)==0 || (!$directStatusMail && !$canBeSend)) {
			return false; // exit not a status for mail sending (or disabled)
		}
		$dest="";
		foreach ($statusMailList as $statusMail) {
			if ($statusMail->idType){
				if (property_exists($this, 'idType') and $this->idType!=$statusMail->idType) {
					continue; // exist : not corresponding type
				}
				$typeName='id'.$objectClass.'Type';
				if (property_exists($this, $typeName) and $this->$typeName!=$statusMail->idType) {
					continue; // exist : not corresponding type
				}
			}
			if ($statusMail->mailToUser==0 and $statusMail->mailToResource==0 and $statusMail->mailToProject==0
			and $statusMail->mailToLeader==0  and $statusMail->mailToContact==0  and $statusMail->mailToOther==0
			and $statusMail->mailToManager==0 and $statusMail->mailToAssigned==0 and $statusMail->mailToSponsor==0) {
				continue; // exit not a status for mail sending (or disabled)
			}
			if ($statusMail->mailToUser) {
				if (property_exists($this,'idUser')) {
					$user=new User($this->idUser);
					$newDest = "###" . $user->email . "###";
					if ($user->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}
			if ($statusMail->mailToResource) {
				if (property_exists($this, 'idResource')) {
					$resource=new Resource($this->idResource);
					$newDest = "###" . $resource->email . "###";
					if ($resource->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}			
			if ($statusMail->mailToSponsor) {
				if (property_exists($this, 'idSponsor')) {
					$sponsor=new Sponsor($this->idSponsor);
					$newDest = "###" . $sponsor->email . "###";
					if ($sponsor->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}
			if ($statusMail->mailToProject or $statusMail->mailToLeader) {
				$aff=new Affectation();
				$crit=array('idProject'=>$idProject, 'idle'=>'0');
				$affList=$aff->getSqlElementsFromCriteria($crit, false);
				if ($affList and count($affList)>0) {
					foreach ($affList as $aff) {
						$resource=new Resource($aff->idResource);
						if ($statusMail->mailToProject) {
							// Change on V4.4.0 oly send mail if user has read access to item
							if ($aff->idResource==getSessionUser()->id) {
							  $usr=getSessionUser();
							} else {
							  $usr=new User($aff->idResource);
							}
							$canRead=false;
							if ($usr and $usr->id) {
								$canRead=(securityGetAccessRightYesNo('menu' . get_class($this), 'read', $this, $usr)=='YES');
							}
							if ($canRead and ! $resource->dontReceiveTeamMails) {
								$newDest = "###" . $resource->email . "###";
								if ($resource->email and strpos($dest,$newDest)===false) {
									$dest.=($dest)?', ':'';
									$dest.= $newDest;
								}
							}
						}
						if ($statusMail->mailToLeader and ($aff->idProfile or $resource->idProfile)) {
						  $profile=($aff->idProfile)?$aff->idProfile:$resource->idProfile;
							$prf=new Profile($profile);
							if ($prf->profileCode=='PL') {
								$newDest = "###" . $resource->email . "###";
								if ($resource->email and strpos($dest,$newDest)===false) {
									$dest.=($dest)?', ':'';
									$dest.= $newDest;
								}
							}
						}
					}
				}
			}
			if ($statusMail->mailToManager) {
				if (property_exists($this,'idProject')) {
					$project=new Project($idProject);
					$manager=new Affectable($project->idResource);
					$newDest = "###" . $manager->email . "###";
					if ($manager->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}
			if ($statusMail->mailToAssigned) {
				$ass=new Assignment();
				$crit=array('refType'=>$objectClass,'refId'=>$this->id);
				$assList=$ass->getSqlElementsFromCriteria($crit);
				foreach ($assList as $ass) {
					$res=new Resource($ass->idResource);
					$newDest = "###" . $res->email . "###";
					if ($res->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}
			if ($statusMail->mailToContact) {
				if (property_exists($this,'idContact')) {
					$contact=new Contact($this->idContact);
					$newDest = "###" . $contact->email . "###";
					if ($contact->email and strpos($dest,$newDest)===false) {
						$dest.=($dest)?', ':'';
						$dest.= $newDest;
					}
				}
			}
			if ($statusMail->mailToOther) {
				if ($statusMail->otherMail) {
					$otherMail=str_replace(';',',', $statusMail->otherMail);
					$otherMail=str_replace(' ',',', $otherMail);
					$split=explode(',',$otherMail);
					foreach ($split as $adr) {
						if ($adr and $adr!='') {
							$newDest = "###" . $adr . "###";
							if (strpos($dest,$newDest)===false) {
								$dest.=($dest)?', ':'';
								$dest.= $newDest;
							}
						}
					}
				}
			}
		}
		if ($dest=="") {
			return false; // exit no addressees
		}
		$dest=str_replace('###','',$dest);
		if ($newItem) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleNew');
		} else if ($noteAdd) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleNote');
		} else if ($noteChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleNoteChange');
		} else if ($assignmentAdd) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleAssignment');
		} else if ($assignmentChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleAssignmentChange');
		} else if ($attachmentAdd) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleAttachment');
		} else if ($statusChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleStatus');
		} else if ($responsibleChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleResponsible');
		} else if ($descriptionChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleDescription');
		} else if ($resultChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleResult');
		} else if ($directStatusMail) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleDirect');
		} else if ($anyChange) {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitleAnyChange');
		} else {
			$paramMailTitle=Parameter::getGlobalParameter('paramMailTitle'); // default
		}
    $title=$this->parseMailMessage($paramMailTitle);
		$message=$this->getMailDetail();
		if ($directStatusMail and isset($directStatusMail->message)) {
			$message=$this->parseMailMessage($directStatusMail->message)	.'<br/><br/>'.$message;
		}

		$message='<html>' .
      '<head>'  . 
      '<title>' . $title . '</title>' .
      '</head>' . 
      '<body style="font-family: Verdana, Arial, Helvetica, sans-serif;">' . 
		$message .
      '</body>' . 
      '</html>';
		$message = wordwrap($message, 70); // wrapt text so that line do not exceed 70 cars per line
		$resultMail=sendMail($dest, $title, $message, $this);
		if ($directStatusMail) {
			if ($resultMail) {
				return array('result'=>'OK', 'dest'=>$dest);
			} else {
				return array('result'=>'', 'dest'=>$dest);
			}
		}
		return $resultMail;
	}

	public static function getBaseUrl(){
	  		// FIX FOR IIS
		if (!isset($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'],1 );
			if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
		}
		$url=(((isset($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS'])=='on') or $_SERVER['SERVER_PORT']=='443')?'https://':'http://')
    .$_SERVER['SERVER_NAME']
    .(($_SERVER['SERVER_PORT']!='80' and $_SERVER['SERVER_PORT']!='443')?':'.$_SERVER['SERVER_PORT']:'')
    .$_SERVER['REQUEST_URI'];
	  $ref="";
	  if (strpos($url,'/tool/')) {
	    $ref.=substr($url,0,strpos($url,'/tool/'));
	  } else if (strpos($url,'/view/')) {
	    $ref.=substr($url,0,strpos($url,'/view/'));
	  } else if (strpos($url,'/report/')) {
	    $ref.=substr($url,0,strpos($url,'/report/'));
	  }
	  return $ref;
	}
	
	public function parseMailMessage($message) {
		$arrayFrom=array();
		$arrayTo=array();
		$objectClass=get_class($this);
		$item=i18n($objectClass);
		if ($objectClass=='Project') {
			$project=$this;
		} else if (property_exists($this, 'idProject')) {
			$project=new Project($this->idProject);
		} else {
			$project=new Project();
		}
		
		// db display name
		$arrayFrom[]='${dbName}';
		$arrayTo[]=Parameter::getGlobalParameter('paramDbDisplayName');
		
		// Class of item
		$arrayFrom[]='${item}';
		$arrayTo[]=$item;
		
		// id
		$arrayFrom[]='${id}';
		$arrayTo[]=$this->id;
		
		// name
		$arrayFrom[]='${name}';
		$arrayTo[]=(property_exists($this, 'name'))?$this->name:'';
		
		// status
		$arrayFrom[]='${status}';
		$arrayTo[]=(property_exists($this, 'idStatus'))?SqlList::getNameFromId('Status', $this->idStatus):'';
		
		// project
		$arrayFrom[]='${project}';
		$arrayTo[]=$project->name;
		
		// type
		$typeName='id' . $objectClass . 'Type';
		$arrayFrom[]='${type}';
		$arrayTo[]=(property_exists($this, $typeName))?SqlList::getNameFromId($objectClass . 'Type', $this->$typeName):'';
		
		// reference
		$arrayFrom[]='${reference}';
		$arrayTo[]=(property_exists($this, 'reference'))?$this->reference:'';
		
		// externalReference
		$arrayFrom[]='${externalReference}';
		$arrayTo[]=(property_exists($this, 'externalReference'))?$this->externalReference:'';
		
		// issuer
		$arrayFrom[]='${issuer}';
		$arrayTo[]=(property_exists($this, 'idUser'))?SqlList::getNameFromId('User', $this->idUser):'';
		
		// responsible
		$arrayFrom[]='${responsible}';
		$arrayTo[]=(property_exists($this, 'idResource'))?SqlList::getNameFromId('Resource', $this->idResource):'';
		
		// sender
		$arrayFrom[]='${sender}';
		$user=getSessionUser();
		$arrayTo[]=($user->resourceName)?$user->resourceName:$user->name;
		
		// context1 to context3
		$arrayFrom[]='${context1}';
		$arrayFrom[]='${context2}';
		$arrayFrom[]='${context3}';
		$arrayTo[]=(property_exists($this, 'idContext1'))?SqlList::getNameFromId('Context', $this->idContext1):'';
		$arrayTo[]=(property_exists($this, 'idContext2'))?SqlList::getNameFromId('Context', $this->idContext2):'';
		$arrayTo[]=(property_exists($this, 'idContext3'))?SqlList::getNameFromId('Context', $this->idContext3):'';
				
		// sponsor
		$arrayFrom[]='${sponsor}';
		$arrayTo[]=SqlList::getNameFromId('Sponsor', $project->idSponsor);
		
		// projectCode
		$arrayFrom[]='${projectCode}';
		$arrayTo[]=$project->projectCode;
		
		// ContractCode
		$arrayFrom[]='${contractCode}';
		$arrayTo[]=$project->contractCode;
		
		// Customer
		$arrayFrom[]='${customer}';
		$arrayTo[]=SqlList::getNameFromId('Client',$project->idClient);
		
		// url (direct access to item)
		$arrayFrom[]='${url}';
		if ($objectClass=='User') {
			// FIX FOR IIS
			$arrayTo[]=self::getBaseUrl();
		} else {
			$arrayTo[]=$this->getReferenceUrl();
		}
		
		// login
		$arrayFrom[]='${login}';
		$arrayTo[]=($objectClass=='User')?$this->name:getSessionUser()->name;
		
		// password
		$arrayFrom[]='${password}';
		$arrayTo[]=($objectClass=='User')?Parameter::getGlobalParameter('paramDefaultPassword'):'';
		
		// admin mail
		$arrayFrom[]='${adminMail}';
		$arrayTo[]=Parameter::getGlobalParameter('paramAdminMail');
		
		// Format title
		return str_replace($arrayFrom, $arrayTo, $message);
	}
	/**
	 *
	 * Get the detail of object, to be send by mail
	 * This is a simplified copy of objectDetail.php, in print mode
	 */
	public function getMailDetail () {
		$currencyPosition=Parameter::getGlobalParameter('currencyPosition');
		$currency=Parameter::getGlobalParameter('currency');
		$msg="";
		$rowStart='<tr>';
		$rowEnd='</tr>';
		$labelStart='<td style="background:#DDDDDD;font-weight:bold;text-align: right;width:25%;vertical-align: middle;">&nbsp;&nbsp;';
		$labelEnd='&nbsp;</td>';
		$fieldStart='<td style="width:2px;">&nbsp;</td><td style="background:#FFFFFF;text-align: left;">';
		$fieldEnd='</td>';
		$sectionStart='<td colspan="3" style="background:#555555;color: #FFFFFF; text-align: center;font-size:10pt;font-weight:bold;">';
		$sectionEnd='</td>';
		$tableStart='<table style="font-size:9pt; width: 95%;font-family: Verdana, Arial, Helvetica, sans-serif;">';
		$tableEnd='</table>';
		$msg=$tableStart;
		$ref=$this->getReferenceUrl();
		$msg.='<tr><td colspan="3" style="font-size:18pt;color:#AAAAAA"><a href="' . $ref . '" target="#">'.i18n(get_class($this)).' #'.htmlEncode($this->id).'</a></td></tr>';
		$nobr=false;
		foreach ($this as $col => $val) {
			$hide=false;
			$nobr_before=$nobr;
			$nobr=false;
			if (substr($col,0,4)=='_tab') {
				// Nothing
			} else if (substr($col,0,5)=='_sec_') {
				if (strlen($col)>8) {
					$section=substr($col,5);
					if ($section=='description' or $section=='treatment') {
						$msg.=$rowStart.$sectionStart.i18n('section' . ucfirst($section)).$sectionEnd.$rowEnd;
					}
				} else {
					$section='';
				}
			} else if (substr($col,0,5)=='_spe_') {
				// Nothing
			} else if (substr($col,0,6)=='_calc_') {
				$item=substr($col,6);
				$msg.= $this->drawCalculatedItem($item);
			} else if (substr($col,0,5)=='_lib_') {
				$item=substr($col,5);
				if (strpos($this->getFieldAttributes($col), 'nobr')!==false) {$nobr=true;}
				if ($this->getFieldAttributes($col)!='hidden') { $msg.= (($nobr)?'&nbsp;':'').i18n($item).'&nbsp;'; }
				if (!$nobr) { $msg.=$fieldEnd.$rowEnd; }
			} else if (substr($col,0,5)=='_Link') {
				// Nothing
			} else if (substr($col,0,11)=='_Assignment') {
				// Nothing
			} else if (substr($col,0,11)=='_Approver') {
				// Nothing
			} else if (substr($col,0,15)=='_VersionProject') {
				// Nothing
			} else if (substr($col,0,11)=='_Dependency') {
				// Nothing
			} else if ($col=='_ResourceCost') {
				// Nothing
			} else if ($col=='_DocumentVersion') {
				// Nothing
			} else if ($col=='_ExpenseDetail') {
				// Nothing
			} else if (substr($col,0,12)=='_TestCaseRun') {
				// Nothing
			} else if (substr($col,0,1)=='_' and substr($col,0,6)!='_void_' and substr($col,0,7)!='_label_') {
				// Nothing
			} else {
				$attributes=''; $isRequired=false; $readOnly=false;$specificStyle='';
				$dataType = $this->getDataType($col); $dataLength = $this->getDataLength($col);
				if ($dataType=='decimal' and substr($col, -4,4)=='Work') { $hide=true; }
				if (strpos($this->getFieldAttributes($col), 'hidden')!==false) { $hide=true; }
				if (strpos($this->getFieldAttributes($col), 'nobr')!==false) { $nobr=true; }
				if (strpos($this->getFieldAttributes($col), 'invisible')!==false) { $specificStyle.=' visibility:hidden'; }
				if (is_object($val)) {
					if (get_class($val)=='Origin') {
						if ($val->originType and $val->originId) {
							$val=i18n($val->originType) . ' #'.htmlEncode($val->originId).' : '. htmlEncode(SqlList::getNameFromId($val->originType, $val->originId));
						} else {
							$val="";
						}
						$dataType='varchar';$dataLength=4000;
					} else {
						$hide=true;
					}
				}
				if ($hide) { continue; }
				if (! $nobr_before) {
					$msg.=$rowStart.$labelStart.$this->getColCaption($col).$labelEnd.$fieldStart;
				} else {
					$msg.="&nbsp;&nbsp;&nbsp;";
				}
				if (is_array($val)) {
					// Nothing
				} else if (substr($col,0,6)=='_void_') {
					// Nothing
				} else if (substr($col,0,7)=='_label_') {
					//$captionName=substr($col,7);
					//$msg.='<label class="label shortlabel">' . i18n('col' . ucfirst($captionName)) . '&nbsp;:&nbsp;</label>';
				} else if ($hide) {
					// Nothing
				} else if ($dataLength>4000) {
				  $text=new Html2Text($val);
				  $plainText=$text->getText();
				  if (mb_strlen($plainText)>4000) {
				    $msg.=nl2br(mb_substr($plainText, 0,4000));
				  } else {
				    $msg.=  $val;
				  }
				} else  if (strpos($this->getFieldAttributes($col), 'displayHtml')!==false ) {
					$msg.=  $val;
				} else if ($col=='id') { // id
					$msg.= '<span style="color:grey;">#</span>' . $val;
				} else if ($col=='password') {
					$msg.=  "*****"; // nothing
				} else if ($dataType=='date' and $val!=null and $val != '') {
					$msg.= htmlFormatDate($val);
				} else if ($dataType=='datetime' and $val!=null and $val != '') {
					$msg.= htmlFormatDateTime($val,false);
				} else if ($dataType=='time' and $val!=null and $val != '') {
					$msg.= htmlFormatTime($val,false);
				} else if ($col=='color' and $dataLength == 7 ) { // color
					/*echo '<table><tr><td style="width: 100px;">';
					 echo '<div class="colorDisplay" readonly tabindex="-1" ';
					 echo '  value="' . htmlEncode($val) . '" ';
					 echo '  style="width: ' . $smallWidth / 2 . 'px; ';
					 echo ' color: ' . $val . '; ';
					 echo ' background-color: ' . $val . ';"';
					 echo ' >';
					 echo '</div>';
					 echo '</td>';
					 if ($val!=null and $val!='') {
					 //echo '<td  class="detail">&nbsp;(' . htmlEncode($val) . ')</td>';
					 }
					 echo '</tr></table>';*/
				} else if ($dataType=='int' and $dataLength==1) { // boolean
					$msg.='<input type="checkbox" disabled="disabled" ';
					if ($val!='0' and ! $val==null) {
						$msg.=' checked />';
					} else {
						$msg.=' />';
					}
				} else if (substr($col,0,2)=='id' and $dataType=='int' and strlen($col)>2
				and substr($col,2,1)==strtoupper(substr($col,2,1)) ) { // Idxxx
					$msg.= htmlEncode(SqlList::getNameFromId(substr($col,2),$val),'print');
				} else  if ($dataLength > 100) { // Text Area (must reproduce BR, spaces, ...
					$msg.= htmlEncode($val,'print');
				} else if ($dataType=='decimal' and (substr($col, -4,4)=='Cost' or substr($col,-6,6)=='Amount' or $col=='amount') ) {
					if ($currencyPosition=='after') {
						$msg.=  htmlEncode($val,'print') . ' ' . $currency;
					} else {
						$msg.=  $currency . ' ' . htmlEncode($val,'print');
					}
				} else if ($dataType=='decimal' and substr($col, -4,4)=='Work') {
					//$msg.=  Work::displayWork($val) . ' ' . Work::displayShortWorkUnit();
				} else {
					if ($this->isFieldTranslatable($col))  {
						$val=i18n($val);
					}
					if (strpos($this->getFieldAttributes($col), 'html')!==false) {
						$msg.=  $val;
					} else {
						$msg.=  htmlEncode($val,'print');
					}
				}
				if (! $nobr) {
					$msg.=$fieldEnd.$rowEnd;
				}
			}
		}
		if (isset($this->_Note) and is_array($this->_Note)) {
			$msg.=$rowStart.$sectionStart.i18n('sectionNote').$sectionEnd.$rowEnd;
			$note = new Note();
			$notes=$note->getSqlElementsFromCriteria(array('refType'=>get_class($this),'refId'=>$this->id),false,null,'id desc');
			foreach ($notes as $note) {
				if ($note->idPrivacy==1) {
					$userId=$note->idUser;
					$userName=SqlList::getNameFromId('User',$userId);
					$creationDate=$note->creationDate;
					$updateDate=$note->updateDate;
					if ($updateDate==null) {$updateDate='';}
					$msg.=$rowStart.$labelStart;
					$msg.=$userName;
					$msg.= '<br/>';
					if ($updateDate) {
						$msg.= '<i>' . htmlFormatDateTime($updateDate) . '</i>';
					} else {
						$msg.= htmlFormatDateTime($creationDate);
					}
					$msg.=$labelEnd.$fieldStart;
					//$msg.=htmlEncode($note->note,'print');
					$text=new Html2Text($note->note);
					$plainText=$text->getText();
					if (mb_strlen($plainText)>4000) { // Should not send too long email
					  $noteTruncated=nl2br(mb_substr($plainText, 0,4000));
					  $msg.=$noteTruncated;
					} else {
					  $msg.= $note->note; 
					}
					$msg.=$fieldEnd.$rowEnd;
				}
			}
		}
		$msg.=$tableEnd;
		return $msg;
	}
	
	public function getReferenceUrl() {
    $ref=self::getBaseUrl();
    $ref.='/view/main.php?directAccess=true&objectClass='.get_class($this).'&objectId='.$this->id;
    return $ref;
	}
	 
	/** =========================================================================
	 * Specific function added to setup a workaround for bug #305
	 * waiting for Dojo fixing (Dojo V1.6 ?)
	 * @todo : deactivate this function if Dojo fixed.
	 */
	public function recalculateCheckboxes($force=false) {
		// if no status => nothing to do
		if (! property_exists($this, 'idStatus')) {
			return;
		}
		$status=new Status($this->idStatus);
		// if no type => nothong to do
		$fldType = 'id' . get_class($this) . 'Type';
		$typeClass=get_class($this) . 'Type';
		if (! property_exists($this, $fldType)) {
			return;
		}
		$type=new $typeClass($this->$fldType);
		if ( ( (property_exists($type,'lockHandled') and $type->lockHandled) or $force)
		and property_exists($this,'handled')) {
			if ($status->setHandledStatus) {
				$this->handled=1;
				if (property_exists($this,'handledDate') and !$this->handledDate) $this->handledDate=date("Y-m-d");
				if (property_exists($this,'handledDateTime') and !$this->handledDateTime) $this->handledDateTime=date("Y-m-d H:i:s");
			} else {
				$this->handled=0;
			}
		}
		if ( ( (property_exists($type,'lockDone') and $type->lockDone) or $force)
		and property_exists($this,'done') ) {
			if ($status->setDoneStatus) {
				$this->done=1;
				if (property_exists($this,'doneDate') and !$this->doneDate) $this->doneDate=date("Y-m-d");
				if (property_exists($this,'doneDateTime') and !$this->doneDateTime) $this->doneDateTime=date("Y-m-d H:i:s");
			} else {
				$this->done=0;
			}
		}
		if ( ( (property_exists($type,'lockSolved') and $type->lockSolved) or $force)
    and property_exists($this,'solved') and property_exists($this,'idResolution') ) {
      $resolution=new Resolution($this->idResolution);
      if ($resolution->solved) {
        $this->solved=1;
      } else {
        $this->solved=0;
      }
    }
		if ( ( (property_exists($type,'lockIdle') and $type->lockIdle) or $force)
		and property_exists($this,'idle') ) {
			if (! self::isSaveConfirmed()) {
			// If save confirmed, must not override idle status that is cascaded
				if ($status->setIdleStatus) {
					$this->idle=1;
					if (property_exists($this,'idleDate') and !$this->idleDate) $this->idleDate=date("Y-m-d");
					if (property_exists($this,'idleDateTime') and !$this->idleDateTime) $this->idleDateTime=date("Y-m-d H:i:s");
				} else {
					$this->idle=0;
				}
			}
		}
		if ( ( (property_exists($type,'lockCancelled') and $type->lockCancelled) or $force)
			and property_exists($this,'cancelled') ) {
				$this->cancelled=($status->setCancelledStatus)?1:0;
		}
	}
	
	public function getAlertLevel($withIndicator=false) {
		$crit=array('refType'=>get_class($this),'refId'=>$this->id);
		$indVal=new IndicatorValue();
		$lst=$indVal->getSqlElementsFromCriteria($crit, false);
		$level="NONE";
		$desc='';
		foreach($lst as $indVal) {
			if ($indVal->warningSent and $level!="ALERT") {
				$level="WARNING"; // Over warning value
			}
			if ($indVal->alertSent) {
				$level="ALERT"; // Over alert value
			}
			if ($indVal->status=="KO") {
			  //$level="OVER"; // Over target value
			}
			if ($withIndicator and ($indVal->warningSent or $indVal->alertSent) ) {
			  if ($desc=='') $desc.='<div style="font-size:80%;color:#555555;">'.i18n('colIdIndicator').'&nbsp;:</div>';
				$color=($indVal->alertSent)?"#FFAAAA":"#FFDDAA";
				$desc.='<div style="font-size:80%;background-color:'.$color.';padding:2px 5px;margin:3px 0px 2px 0px;border:1px solid #aaaaaa">'.$indVal->getShortDescription().'</div>';
				//$indDesc=$indVal->getShortDescriptionArray();
				//$desc.=$indDesc['indicator'];
				//$desc.=$indDesc['target'];
			}
		}
		return array('level'=>$level,'description'=>$desc);
	}

	public function buildSelectClause($included=false,$hidden=array()){	
		$table=$this->getDatabaseTableName();
		$select="";
		$from="";
		if (self::is_subclass_of($this,'PlanningElement')) {
			$this->setVisibility();
		}
		foreach ($this as $col=>$val) {		
			$firstCar=substr($col,0,1);
			$threeCars=substr($col,0,3);
			if ( ($included and ($col=='id' or $threeCars=='ref' or $threeCars=='top' or $col=='idle') )
			or ($firstCar=='_')
			or ( strpos($this->getFieldAttributes($col), 'hidden')!==false and strpos($this->getFieldAttributes($col), 'forceExport')===false )
			or ($col=='password')
			or (isset($hidden[$col]))
			or (strpos($this->getFieldAttributes($col), 'noExport')!==false)
			or (strpos($this->getFieldAttributes($col), 'calculated')!==false)
			//or ($costVisibility!="ALL" and (substr($col, -4,4)=='Cost' or substr($col,-6,6)=='Amount') )
			//or ($workVisibility!="ALL" and (substr($col, -4,4)=='Work') )
			// or calculated field : not to be fetched
			) {
				// Here are all cases of not dispalyed fields
			} else if ($firstCar==ucfirst($firstCar)) {
				$ext=new $col();
				$from.=' left join ' . $ext->getDatabaseTableName() .
              ' on ' . $table . ".id" .  
              ' = ' . $ext->getDatabaseTableName() . '.refId' .
  				    ' and ' . $ext->getDatabaseTableName() . ".refType='" . get_class($this) . "'";
				$extClause=$ext->buildSelectClause(true,$hidden);
				if (trim($extClause['select'])) {
				  $select.=', '.$extClause['select'];
				}
			} else {
				$select .= ($select=='')?'':', ';
				$select .= $table . '.' . $this->getDatabaseColumnName($col) . ' as ' . $col;
			}
		}
		return array('select'=>$select,'from'=>$from);
	}

	public function setReference($force=false, $old=null) {
		scriptLog('SqlElement::setReference');
		$objectsWithFixedReference=array('Bill');
		if (! property_exists($this,'reference')) {
			return;
		}
		$class=get_class($this);
		if ($class=='TicketSimple') $class='Ticket';
		if ($class=='Bill' and !$this->billId) return; // Do not set Reference until billId is set
		
		$fmtPrefix=Parameter::getGlobalParameter('referenceFormatPrefix');
		$fmtSuffix='';
		$fmtNumber=Parameter::getGlobalParameter('referenceFormatNumber');
		if ($class=='Bill') {
		  $fmtPrefixBill=Parameter::getGlobalParameter('billReferenceFormat');
		  $fmtNumberBill=Parameter::getGlobalParameter('billNumSize');
		  if ($fmtPrefixBill) $fmtPrefix=$fmtPrefixBill;
		  if ($fmtNumberBill) $fmtNumber=$fmtNumberBill;
		}
		$posNume=strpos($fmtPrefix,'{NUME}');
		if ($posNume!==false) {
		  $fmtSuffix=substr($fmtPrefix,$posNume+6);
		  $fmtPrefix=substr($fmtPrefix,0,$posNume);
		}
		$change=Parameter::getGlobalParameter('changeReferenceOnTypeChange');
		$type='id' . $class . 'Type';
		if ($this->reference and ! $force) {
			if ($change!='YES') {
				return;
			}
			if (! property_exists($this,$type)) {
				return;
			}
			if (! property_exists($this,'idProject')) {
				return;
			}
			if (! $old) {
				$old=new $class($this->id);
			}
			if ($this->$type==$old->$type and $this->idProject==$old->idProject) {
				return;
			}
			if (in_array(get_class($this),$objectsWithFixedReference)) {
			  return;
			}
		}
		if (isset($this->idProject)) {
			$projObj=new Project($this->idProject);
		} else {
			$projObj=new Project();
		}
		if (isset($this->$type)) {
			$typeObj=new Type($this->$type);
		} else {
			$typeObj=new Type();
		}
		$year=date('Y');
		$month=date('m');
		if (get_class($this)=='Bill') {
		  $year=substr($this->date,0,4);
			$month=substr($this->date,5,2);
		} else if (property_exists($this,'creationDate')) {
			$year=substr($this->creationDate,0,4);
			$month=substr($this->creationDate,5,2);
		} else if (property_exists($this,'creationDateTime')) {
			$year=substr($this->creationDateTime,0,4);
			$month=substr($this->creationDateTime,5,2);
		}		
		$arrayFrom=array('{PROJ}', '{TYPE}','{YEAR}','{MONTH}');
		$arrayTo=array($projObj->projectCode,$typeObj->code, $year, $month);
		$prefix=str_replace($arrayFrom, $arrayTo, $fmtPrefix);
		$suffix=str_replace($arrayFrom, $arrayTo, $fmtSuffix);
		$query="select max(reference) as ref from " . $this->getDatabaseTableName();
		$query.=" where reference like '" . $prefix . "%'";
		$query.=" and length(reference)=( select max(length(reference)) from " . $this->getDatabaseTableName();
		$query.=" where reference like '" . $prefix . "%')";
		$ref=$prefix;
		$mutex = new Mutex($prefix);
		$mutex->reserve();
		$result=Sql::query($query);
		$numMax='0';
		if (count($result)>0) {
			$line=Sql::fetchLine($result);
			$refMax=$line['ref'];
			$numMax=substr($refMax,strlen($prefix));
		}
		$numMax+=1;
		if ($fmtNumber and  $fmtNumber-strlen($numMax)>0) {
			$num=substr('0000000000', 0, $fmtNumber-strlen($numMax)) . $numMax;
		} else {
			$num=$numMax;
		}
		$this->reference=$prefix.$num.$suffix;
		if (get_class($this)=='Document' and property_exists($this, 'documentReference')) {
			$fmtDocument=Parameter::getGlobalParameter('documentReferenceFormat');
			$docRef=str_replace(array('{PROJ}',              '{TYPE}',      '{NUM}', '{NAME}'),
			array($projObj->projectCode, $typeObj->code, $num,   $this->name),
			$fmtDocument);
			$this->documentReference=$docRef;
		}
		if ($force) {
			$this->updateSqlElement();
		}
		$mutex->release();

	}


	public function setDefaultResponsible() {
		if (get_class($this)!='Project' and property_exists($this,'idResource') and property_exists($this,'idProject')
		and ! trim($this->idResource) and trim($this->idProject)) {
			if (Parameter::getGlobalParameter('setResponsibleIfSingle')=="YES") {
				$aff=new Affectation();
				$crit=array('idProject'=>$this->idProject);
				$cpt=$aff->countSqlElementsFromCriteria($crit);
				if ($cpt==1) {
					$aff=SqlElement::getSingleSqlElementFromCriteria('Affectation', $crit);
					$res=new Resource($aff->idResource);
					if ($res and $res->id) {
						$this->idResource=$res->id;
					}
				}
			}
		}
	}

	public function getTitle($col) {
		return i18n('col'.ucfirst($col));
	}

	public static function unsetRelationShip($rel1, $rel2) {
		unset(self::$_relationShip[$rel1][$rel2]);
	}

	public function getOld() {
		$class=get_class($this);
		return new $class($this->id);
	}

	public function splitLongFields() {
		$maxLenth=500;
		foreach ($this as $fld=>$val) {
			if ($this->getDataLength($fld)>100 and strlen($val)>$maxLenth) {
				//$secFull="_sec_".$fld;
				//$this->$secFull=$val;
				$fldFull="_".$fld."_full";
				$this->$fldFull=$val;
				$this->$fld=substr($val,0,$maxLenth).' (...)';
			}

		}
	}

	public static function isVisibleField($col) {
		// Check if cost and work field is visible for profile
		$cost=(substr($col,-4)=='Cost' or substr($col,-6)=="Amount")?true:false;
		$work=(substr($col,-4)=='Work')?true:false;
		if (!$cost and !$work) {return true;}
		if (! self::$staticCostVisibility or ! self::$staticWorkVisibility) {
			$pe=new PlanningElement();
			$pe->setVisibility();
			self::$staticCostVisibility=$pe->_costVisibility;
			self::$staticWorkVisibility=$pe->_workVisibility;
		}
		$costVisibility=self::$staticCostVisibility ;
		$workVisibility=self::$staticWorkVisibility;
		$validated=(substr($col,0,9)=='validated')?true:false;
		if ($cost) {
			if ($costVisibility=='ALL') {
				return true;
			} else if ($costVisibility=='NO') {
				return false;
			} else if ($costVisibility=='VAL') {
				if ($validated) {
					return true;
				} else {
					return false;
				}
			} else {
				errorLog("ERROR : costVisibility='$costVisibility' is not 'ALL', 'NO' or 'VAL'");
			}
		} else if ($work) {
			if ($workVisibility=='ALL') {
				return true;
			} else if ($workVisibility=='NO') {
				return false;
			} else if ($workVisibility=='VAL') {
				if ($validated) {
					return true;
				} else {
					return false;
				}
			} else {
				errorLog("ERROR : workVisibility='$workVisibility' is not 'ALL', 'NO' or 'VAL'");
			}
		}
		return true;
	}
	
	public static function setDeleteConfirmed() {
		self::$staticDeleteConfirmed=true;
	}
	public static function isDeleteConfirmed() {
		return self::$staticDeleteConfirmed;
	}
	public static function setSaveConfirmed() {
		self::$staticSaveConfirmed=true;
	}
	public static function isSaveConfirmed() {
		return self::$staticSaveConfirmed;
	}
	public static function isThumbableField($col) {
	  return ($col=='idResource' or $col=='idUser' or $col=='idContact')?true:false;
	}
	public static function isColorableField($col) {
	  return ($col=='idProject' or $col=='idStatus' or $col=='idQuality' or $col=='idHealth' or $col=='idTrend'
				or $col=='idLikelihood' or $col=='idCriticality' or $col=='idSeverity' or $col=='idUrgency' or $col=='idPriority'
				or $col=='idRiskLevel' or $col=='idFeasibility' or $col=='idEfficiency' or $col=='idResolution'
				or $col=='idTenderStatus')?true:false;
	}
	public static function isIconableField($col) {
	  return ($col=='idQuality' or $col=='idHealth' or $col=='idTrend')?true:false;
	}
	
	public function getExtraRequiredFields($newType="", $newStatus="", $newPlanningMode="") {  
	  $result=array();
	  $type=$newType;
	  $status=$newStatus;
	  $planningMode=$newPlanningMode;
	  if ($this->id) {
	    $typeName='id'.str_replace('PlanningElement', '',get_class($this)).'Type';
	    $planningModeName='id'.str_replace('PlanningElement', '',get_class($this)).'PlanningMode';
	    if (!$type and property_exists($this,$typeName)) {
	      $type=$this->$typeName;
	    }
	    if (! $status and property_exists($this,'idStatus') ) {
	      $status=$this->idStatus;
	    }
	    if (! $planningMode and property_exists($this,$planningModeName) ) {
	      $planningMode=$this->$planningModeName;
	    }
	  } else {
	    $typeName='id'.str_replace('PlanningElement', '',get_class($this)).'Type';
	    $typeClassName=str_replace('PlanningElement', '',get_class($this)).'Type';
	    if (property_exists($this,$typeName) and self::class_exists($typeClassName)) {
  	    $table=SqlList::getList($typeClassName, 'name', null);
  	    if (count($table) > 0) {
  	      foreach ( $table as $idTable => $valTable ) {
  	        $type=$idTable;
  	        break;
  	      }
  	    }
	    }
	    $status=1; // first status always 1 (recorded)
	    $planningModeName='id'.str_replace('PlanningElement', '',get_class($this)).'PlanningMode';
	    $typeElt=null;
	    if (!$type and SqlElement::class_exists($typeClassName)) {
	      $typeList=SqlList::getList($typeClassName);
	      $typeElt=reset($typeList);
	      $type=($typeElt)?key($typeList):null;
	    }
	    if (! $planningMode and $type and property_exists($typeClassName,$planningModeName)) {
	      $typeObj=new $typeClassName($type);
	      $planningMode=$typeObj->$planningModeName;
	    }
	  } 
	  if ($planningMode) {
	    $planningModeObj=new PlanningMode($planningMode);
	    if ($planningModeObj->mandatoryStartDate and property_exists($this,'validatedStartDate')) {
  	    $result['validatedStartDate']='required';
  	  }
  	  if ($planningModeObj->mandatoryEndDate and property_exists($this,'validatedEndDate')) {
  	    $result['validatedEndDate']='required';
  	  }
  	  if ($planningModeObj->mandatoryDuration and property_exists($this,'validatedDuration')) {
  	  	$result['validatedDuration']='required';
  	  }
	  }
	  if ($type) {
	    $typeObj=new Type($type);
	    if ($typeObj->mandatoryResourceOnHandled) {
	      if ($newStatus) {
	        $statusObj=new Status($newStatus);
	        if ($statusObj->setHandledStatus) {
	          $result['idResource']='required';
	        }
	      } else {
	        if (property_exists($this,'handled') and $this->handled) {
	          $result['idResource']='required';
	        }
	      }
	    }
	    if ($typeObj->mandatoryDescription) {
	      $result['description']='required';
	    }
	    if ($typeObj->mandatoryResultOnDone) {
	      if ($newStatus) {
	        $statusObj=new Status($newStatus);
	        if ($statusObj->setDoneStatus) {
	          $result['result']='required';
	        }
	      } else {
	        if (property_exists($this,'done') and $this->done) {
	          $result['result']='required';
	        }
	      }
	    }
	    if (property_exists($typeObj, 'mandatoryResolutionOnDone') and $typeObj->mandatoryResolutionOnDone) {
	      if ($newStatus) {
	        $statusObj=new Status($newStatus);
	        if ($statusObj->setDoneStatus) {
	          $result['idResolution']='required';
	        }
	      } else {
	        if (property_exists($this,'done') and $this->done) {
	          $result['idResolution']='required';
	        }
	      }
	      
	    }
	  }
	  return $result;
	}
	public function getExtraHiddenFields($newType="") {
	  $class=get_class($this);
	  $typeFld='id'.$class."Type";
	  if (! property_exists($this,$typeFld) and !$newType) { return array(); } // No type for this item, so no extra field
	  $list=self::getExtraHiddenFieldsFullList();
	  $type=($newType)?$newType:$this->$typeFld;
	  if (!isset($list[$class])) { return array(); }
	  if (!isset($list[$class][$type])) { return array(); }
	  return $list[$class][$type];
	}
	private static function getExtraHiddenFieldsFullList() {
	  if (self::$_extraHiddenFields!=null) {
	    return self::$_extraHiddenFields;
	  }
	  $sessionList=getSessionValue('extraHiddenFieldsArray');
	  if ($sessionList) {
	    self::$_extraHiddenFields=$sessionList;
	    return self::$_extraHiddenFields;
	  }
	  $extra=new ExtraHiddenField();
	  $extraList=$extra->getSqlElementsFromCriteria(null); // Get all fields
	  $result=array();
	  foreach ($extraList as $extra) {
	    if (! isset($result[$extra->scope])) $result[$extra->scope]=array();
	    if (! isset($result[$extra->scope][$extra->idType])) $result[$extra->scope][$extra->idType]=array();
	    $result[$extra->scope][$extra->idType][]=$extra->field;
	  }
	  self::$_extraHiddenFields=$result;
	  setSessionValue('extraHiddenFieldsArray', $result);
	  return $result;
	}
	
	// ============================================================
	// Redefines standard class test function 
	// to avoid error logging when not necessary
	// ============================================================ 
	public static function is_a($object,$class) {
	   global $hideAutoloadError;
	   $hideAutoloadError=true; // Avoid error message in autoload
	   if (is_object($object)) {
	     $result=($object instanceof $class);
	   } else if (version_compare(PHP_VERSION, '5.3.9') >= 0) {
	     $result=@is_a($object,$class,true); // 3rd parameter "allow_string" is compatible only since V5.3.9
	   } else {
	     if (self::class_exists($object)) {
	       $obj=new $object();
	       $result=($obj instanceof $class);
	     } else {
	       $result=false;
	     }
	   }
	   $hideAutoloadError=true;
	   return $result;
  }
  public static function class_exists($item){
    global $hideAutoloadError;
    $hideAutoloadError=true; // Avoid error message in autoload
    $result=class_exists($item,true);
    $hideAutoloadError=false;
    return $result;
  }
  public static function is_subclass_of ( $className, $parentClass) {
    global $hideAutoloadError;
    $hideAutoloadError=true; // Avoid error message in autoload
    $result=is_subclass_of( $className, $parentClass);
    $hideAutoloadError=false;
    return $result;
  }
  public static function getPrivacyClause ($obj=null) {
    $isPrivate='isPrivate';
    $idUser='idUser';
    if ($obj) {
      if (!is_object($obj)) {
        $obj=new $obj();
      }
      $isPrivate=$obj->getDatabaseTableName() . '.' . $obj->getDatabaseColumnName('isPrivate');
      $idUser=$obj->getDatabaseTableName() . '.' . $obj->getDatabaseColumnName('idUser');
    }
    return "($isPrivate=0 or $idUser=".Sql::fmtId(getSessionUser()->id).")";
  }
  
}
?>