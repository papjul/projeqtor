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
class TicketMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  // List of fields that will be exposed in general user interface
  public $_sec_description;
  public $id;    // redefine $id to specify its visible place 
  public $reference;
  public $name;
  public $idTicketType;
  public $idProject;
  public $externalReference;
  public $idUrgency;
  public $creationDateTime;
  public $lastUpdateDateTime;
  public $idUser;
  public $idContact;
  public $Origin;
  public $idTicket;
  public $idContext1;
  public $idContext2;
  public $idContext3;
  
  public $description;
  public $_sec_treatment;
  public $idActivity;
  public $idStatus;
  public $idResolution;
  public $idResource;
  public $idCriticality;
  public $idPriority;
  public $_tab_2_1 = array('initial', 'actual','dueDate');
  public $initialDueDateTime; // is an object
  public $actualDueDateTime;
  public $WorkElement;
  public $handled;
  public $handledDateTime;
  public $done;
  public $doneDateTime;
  public $solved;
  public $_lib_colSolved;
  public $idle;
  public $idleDateTime;
  public $cancelled;
  public $_lib_cancelled;
  public $result;
  public $_sec_productComponent;
  public $idProduct;
  public $idComponent;
  public $idOriginalProductVersion;
  public $_OtherOriginalProductVersion=array();
  public $idOriginalComponentVersion;
  public $_OtherOriginalComponentVersion=array();
  public $idTargetProductVersion;
  public $_OtherTargetProductVersion=array();
  public $idTargetComponentVersion;
  public $_OtherTargetComponentVersion=array();
  public $_sec_Link;
  public $_Link=array();
  public $_Attachment=array();
  public $_Note=array();
  public $_nbColMax=3;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="4%" ># ${id}</th>
    <th field="nameProject" width="7%">${idProject}</th>
    <th field="nameTicketType" width="7%" >${idTicketType}</th>
    <th field="name" width="17%">${name}</th>
    <th field="nameUser" formatter="thumbName22" width="10%">${issuer}</th>
    <th field="colorNameUrgency" width="7%" formatter="colorNameFormatter">${idUrgency}</th>
    <th field="colorNamePriority" width="7%" formatter="colorNameFormatter">${idPriority}</th>
    <th field="colorNameStatus" width="7%" formatter="colorNameFormatter">${idStatus}</th>
    <th field="actualDueDateTime" width="7%" formatter="dateTimeFormatter">${actualDueDateTime}</th>
    <th field="nameTargetProductVersion" width="7%" >${idTargetProductVersion}</th>
    <th field="nameResource" formatter="thumbName22" width="10%">${responsible}</th>
    <th field="handled" width="3%" formatter="booleanFormatter" >${handled}</th>
    <th field="done" width="3%" formatter="booleanFormatter" >${done}</th>
    <th field="idle" width="3%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("id"=>"nobr", "reference"=>"readonly",
                                  "name"=>"required", 
                                  "idProject"=>"required",
                                  "idTicketType"=>"required",
                                  "idStatus"=>"required",
                                  "creationDateTime"=>"required",
                                  "handled"=>"nobr",
                                  "done"=>"nobr",
                                  "idle"=>"nobr",
                                  "idleDateTime"=>"nobr",
                                  "cancelled"=>"nobr",
                                  "idContext1"=>"nobr,size1/3,title",
                                  "idContext2"=>"nobr,title", 
                                  "idContext3"=>"title",
                                  "doneDateTime"=>"nobr",
                                  "solved"=>"nobr"
  );  
  
  private static $_colCaptionTransposition = array('idUser'=>'issuer', 
                                                   'idResource'=> 'responsible',
                                                   'idActivity' => 'planningActivity',
                                                   'idContact' => 'requestor',
                                                   'idTargetVersion'=>'targetVersion',
                                                   'idOriginalVersion'=>'originalVersion',
                                                   'idTicket'=>'duplicateTicket',
                                                   'idContext1'=>'idContext');
  
  //private static $_databaseColumnName = array('idResource'=>'idUser');
  private static $_databaseColumnName = array('idTargetVersion'=>'idVersion');
  //private static $_databaseColumnName = array('idTargetVersion'=>'idVersion');
    
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    if ($this->idActivity and is_object($this->WorkElement) and $this->WorkElement->realWork>0) {
      self::$_fieldsAttributes['idActivity']='readonly';
    }
    if (Parameter::getGlobalParameter('realWorkOnlyForResponsible')=='YES') {
      if ($this->id and $this->idResource != getSessionUser()->id) {
        WorkElement::lockRealWork();
      }
    }
    if (!$this->id and getSessionUser()->isContact) {
      $this->idContact=getSessionUser()->id;
    }
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
    if ($colName=="idCriticality" or $colName=="idUrgency") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';  
      $colScript .= htmlGetJsTable('Urgency', 'value');
      $colScript .= htmlGetJsTable('Criticality', 'value');
      $colScript .= htmlGetJsTable('Priority', 'value');
      $colScript .= '  var urgencyValue=0; var criticalityValue=0; var priorityValue=0;';
      $colScript .= '  var filterUrgency=dojo.filter(tabUrgency, function(item){return item.id==dijit.byId("idUrgency").value;});';
      $colScript .= '  var filterCriticality=dojo.filter(tabCriticality, function(item){return item.id==dijit.byId("idCriticality").value;});';
      $colScript .= '  dojo.forEach(filterUrgency, function(item, i) {urgencyValue=item.value;});';
      $colScript .= '  dojo.forEach(filterCriticality, function(item, i) {criticalityValue=item.value;});';
      $colScript .= '  calculatedValue = Math.round(urgencyValue*criticalityValue/2);';
      $colScript .= '  var filterPriority=dojo.filter(tabPriority, function(item){return item.value==calculatedValue;});';
      $colScript .= '  if ( filterPriority.length==0) {';
      $colScript .= '    calculatedValue = Math.round(calculatedValue/2);';
      $colScript .= '    var filterPriority=dojo.filter(tabPriority, function(item){varChanged=true; return item.value==calculatedValue;});';
      $colScript .= '  }';
      $colScript .= '  var setVar="";';
      $colScript .= '  dojo.forEach(filterPriority, function(item, i) {if (setVar=="") dijit.byId("idPriority").set("value",item.id);});';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="actualDueDateTime") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  var upd=dijit.byId("initialDueDateTime");';
      $colScript .= '  if (upd && upd.get("value")==null) { ';
      $colScript .= '    upd.set("value", this.value); ';
      $colScript .= '  } ';
      $colScript .= '  formChanged();';      
      $colScript .= '</script>';     
    } else if ($colName=="actualDueDateTimeBis") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  var upd=dijit.byId("initialDueDateTimeBis");';
      $colScript .= '  if (upd && upd.get("value")==null) { ';
      $colScript .= '    upd.set("value", this.value); ';
      $colScript .= '  } ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';     
    } else if ($colName=="initialDueDateTime") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  var upd=dijit.byId("actualDueDateTime");';
      $colScript .= '  if (upd && upd.get("value")==null) { ';
      $colScript .= '    upd.set("value", this.value); ';
      $colScript .= '  } ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';     
    } else if ($colName=="initialDueDateTimeBis") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  var upd=dijit.byId("actualDueDateTimeBis");';
      $colScript .= '  if (upd && upd.get("value")==null) { ';
      $colScript .= '    upd.set("value", this.value); ';
      $colScript .= '  } ';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';     
    } else if ($colName=="idProduct" or $colName=="idComponent") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value) {';
      $colScript .= '    var idP=dijit.byId("idProduct").get("value");';
      $colScript .= '    var idC=dijit.byId("idComponent").get("value");';
      $colScript .= '    var idR=dijit.byId("idResource").get("value");';
      $colScript .= '    dojo.xhrGet({';
      $colScript .= '      url: "../tool/getSingleData.php?dataType=responsible&idProduct="+idP+"&idComponent="+idC+"&idResource="+idR,';
      $colScript .= '      handleAs: "text",';
      $colScript .= '      load: function (data) {';
      $colScript .= '        if (data) {';
      $colScript .= '          refreshList("idResource", "idProject", dijit.byId("idProject").get("value"), data, null, dijit.byId("idResource").get("required"));';
      $colScript .= '          dijit.byId("idResource").set("value",data);';
      $colScript .= '        }';
      $colScript .= '      }';
      $colScript .= '    });';
      $colScript .= '  };';
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
    if (trim($this->idActivity)) {    
      $parentActivity=new Activity($this->idActivity);
      if ($parentActivity->idProject!=$this->idProject) {
        $result.='<br/>' . i18n('msgParentActivityInSameProject');
      }
    }
    if ($this->idTicket) {
    	if ($this->idTicket==$this->id) {
    		$result.='<br/>' . i18n('duplicateIsSame');
    	} else {
    	  $duplicate=new Ticket($this->idTicket);
    	  if ($duplicate->idTicket and $duplicate->idTicket!=$this->id) {
    		  $result.='<br/>' . i18n('duplicateAlreadyLinked');
    	  }
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
  
  public function deleteControl() { 
    $result='';
    if ($this->WorkElement and $this->WorkElement->realWork>0) {
      $result.='<br/>' . i18n('msgUnableToDeleteRealWork');
    }
    if ($result=='') {
      $result .= parent::deleteControl();
    }
    return $result;
  }
  public function save() {
  	$old=$this->getOld();
  	if (! trim($this->creationDateTime)) {
  	  $this->creationDateTime=date('Y-m-d H:i');
  	}
  	if ($this->idTicketType != $old->idTicketType 
  	 or $this->idUrgency != $old->idUrgency
  	 or $this->creationDateTime != $old->creationDateTime) {
  	 	$crit=array('idTicketType'=>$this->idTicketType, 'idUrgency'=>$this->idUrgency, 'idle'=>'0');
  		$delay=SqlElement::getSingleSqlElementFromCriteria('TicketDelay', $crit);
  		if ($delay and $delay->id) {
  			$unit=new DelayUnit($delay->idDelayUnit);
  			$this->initialDueDateTime=addDelayToDatetime($this->creationDateTime,$delay->value, $unit->code);
  			if (! trim($this->actualDueDateTime) or ($old->actualDueDateTime==$old->initialDueDateTime 
  			                                     and $old->actualDueDateTime==$this->actualDueDateTime) ) {
  			  $this->actualDueDateTime=$this->initialDueDateTime;                                 	
  			}
  		}
  	}
  	if (isset($this->WorkElement)) {
  	  $this->WorkElement->done=$this->done;
  	  $this->WorkElement->idle=$this->idle;
  	}
  	if ($old->idActivity!=$this->idActivity and $this->idActivity) {
  		$act=new Activity($this->idActivity);
  		if ($act->idTargetProductVersion) {
  			$this->idTargetProductVersion=$act->idTargetProductVersion;
  			$vers=new Version($act->idTargetProductVersion);
  			if ($vers->idProduct) {
  				$this->idProduct=$vers->idProduct;
  			}
  		}
  	}
  	$responsibleFromProduct=Parameter::getGlobalParameter('responsibleFromProduct');
  	if (!$responsibleFromProduct) $responsibleFromProduct='always';
  	if ($responsibleFromProduct=='always' or ($responsibleFromProduct=='ifempty' and !trim($this->idResource))) { 
  	  $comp=new Component($this->idComponent,true);
  	  if ($comp->idResource) {
  	    $this->idResource=$comp->idResource;
  	  } else {
  	    $prod=new Product($this->idProduct,true);
  	    if ($prod->idResource) {
  	      $this->idResource=$prod->idResource;
  	    }
  	  }
  	}
  	$result=parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
  	if (trim($this->idTicket) and ! trim($old->idTicket)) {
      $duplicate=new Ticket($this->idTicket);
      if (! $duplicate->idTicket) {
      	$duplicate->idTicket=$this->id;
      	$duplicate->save();
      }
  	}
  	if ($old->idActivity and $old->idActivity!=$this->idActivity) {
  		// if top activity changed, must update corresponding Planning element for ticket work summary
  		$ape = SqlElement::getSingleSqlElementFromCriteria ( 'ActivityPlanningElement', array (
  				'refType' => 'Activity',
  				'refId' => $old->idActivity
  		) );
  		if ($ape and $ape->id) {
  			$ape->updateWorkElementSummary ();
  		} 
  	}
  	return $result;
  }

  public function getTitle($col) {
  	if (substr($col,0,9)=='idContext') {
  	  return SqlList::getNameFromId('ContextType', substr($col, 9));
  	} else {
  		return parent::getTitle($col);
  	} 
  	
  }
  
}
?>