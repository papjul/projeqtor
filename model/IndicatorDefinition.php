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
 * Habilitation defines right to the application for a menu and a profile.
 */ 
require_once('_securityCheck.php');
class IndicatorDefinition extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_description;
  public $id;    // redefine $id to specify its visible place 
  public $idIndicatorable;
  public $name;
  public $nameIndicatorable;
  public $idType;
  public $idIndicator;
  public $codeIndicator;
  public $typeIndicator;
  public $warningValue;
  public $idWarningDelayUnit;
  public $codeWarningDelayUnit;
  public $alertValue; 
  public $idAlertDelayUnit;
  public $codeAlertDelayUnit;
  public $idle;
  public $_sec_SendMail;
  
  public $mailToContact;
  public $mailToUser;
  public $mailToResource;
  public $mailToProject;
  public $mailToLeader;
  public $mailToManager;
  public $mailToAssigned;
  public $mailToOther;
  public $otherMail;
  
  public $_sec_InternalAlert;
  public $alertToUser;
  public $alertToResource;
  public $alertToProject;
  public $alertToContact;
  public $alertToLeader;
  public $alertToManager;
  public $alertToAssigned;
  
  public $_isNameTranslatable = true;

  public $_noCopy;
  
  public $_nbColMax=3;
    
    private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="nameIndicatorable" formatter="translateFormatter" width="15%" >${element}</th>
    <th field="nameIndicator" width="20%" formatter="translateFormatter">${idIndicator}</th>
    <th field="nameType" width="10%" >${type}</th>
    <th field="warningValue" width="8%" formatter="numericFormatter">${warning}</th>
    <th field="nameWarningDelayUnit" width="12%" formatter="translateFormatter">${unit}</th>
    <th field="alertValue" width="8%" formatter="numericFormatter">${alert}</th>
    <th field="nameAlertDelayUnit" width="12%" formatter="translateFormatter">${unit}</th> 
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"hidden",
                                  "idType"=>"nocombo",
                                  "warningValue"=>"nobr",
                                  "alertValue"=>"nobr",
                                  "nameIndicatorable"=>"hidden",
                                  "codeIndicator"=>"hidden",
                                  "typeIndicator"=>"hidden",
                                  "codeWarningDelayUnit"=>"hidden",
                                  "codeAlertDelayUnit"=>"hidden",
                                  "mailToOther"=>"nobr",
                                  "otherMail"=>""
  );  
  
    private static $_colCaptionTransposition = array('idIndicatorable'=>'element',
                                                     'idType'=>'type',
                                                     'warningValue'=>'warning',
                                                     'alertValue'=>'alert',
                                                     'alertToUser'=>'mailToUser',
                                                     'alertToResource'=>'mailToResource',
                                                     'alertToProject'=>'mailToProject',
                                                     'alertToContact'=>'mailToContact',
                                                     'alertToLeader'=>'mailToLeader',
                                                     'alertToManager'=>'mailToManager',
                                                     'alertToAssigned'=>'mailToAssigned',
                                                     'otherMail'=>'email');
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    
    if ($this->id) {
      self::$_fieldsAttributes["idIndicatorable"]='readonly'; 
      self::$_fieldsAttributes["idIndicator"]='readonly';
    }
  }

  
   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

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
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  public function save() {
    $old=$this->getOld();
  	$indicatorable=new Indicatorable($this->idIndicatorable);
  	$this->nameIndicatorable=$indicatorable->name;
  	$delayUnit=new DelayUnit($this->idWarningDelayUnit);
  	$this->codeWarningDelayUnit=$delayUnit->code;
  	$delayUnit=new DelayUnit($this->idAlertDelayUnit);
    $this->codeAlertDelayUnit=$delayUnit->code;
  	$indicator=new Indicator($this->idIndicator);
    $this->codeIndicator=$indicator->code;
  	$this->typeIndicator=$indicator->type;
  	$this->name=$indicator->name;
  	$result=parent::save();
  	if (!$old->idle and $this->idle) {
  	  $this->purgeExistingIndicatorValue();
  	} else if ($this->warningValue!=$old->warningValue or $this->idWarningDelayUnit!=$old->idWarningDelayUnit
       or $this->alertValue!=$old->alertValue or $this->idAlertDelayUnit!=$old->idAlertDelayUnit){
  	  $this->updateExistingIndicatorValue();
  	}
  	return $result;
  }
  
  public function delete() {
    $result=parent::delete();
    $this->purgeExistingIndicatorValue();
    return $result;
  }
  
  private function purgeExistingIndicatorValue() {
    $iv=new IndicatorValue();
    $iv->purge("idIndicatorDefinition='".$this->id."'");
  }
  private function updateExistingIndicatorValue() {
    $iv=new IndicatorValue();
    $ivList=$iv->getSqlElementsFromCriteria(array('idIndicatorDefinition'=>$this->id));
    foreach ($ivList as $iv) {
      $obj=new $iv->refType($iv->refId);
      if ($obj->id) {
        IndicatorValue::addIndicatorValue($this,$obj);
      } else {
        $iv->delete();
      }
    }
  }
  
    /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);
        if ($this->mailToOther=='1') {
      self::$_fieldsAttributes['otherMail']='';
    } else {
      self::$_fieldsAttributes['otherMail']='invisible';
    } 
    
    $colScript = parent::getValidationScript($colName);

    if ($colName=="mailToOther") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= ' var fld = dijit.byId("otherMail").domNode;';
      $colScript .= '  if (this.checked) { ';
      $colScript .= '    dojo.style(fld, {visibility:"visible"});';
      $colScript .= '  } else {';
      $colScript .= '    dojo.style(fld, {visibility:"hidden"});';
      $colScript .= '    fld.set("value","");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=='idIndicatorable') { 
      $colScript .= '<script type="dojo/connect" event="onChange" args="evt">';
      $colScript .= '  dijit.byId("idIndicator").set("value",null);';
      $colScript .= '  dijit.byId("idType").set("value",null);';
      $colScript .= '  refreshList("idIndicator","idIndicatorable", this.value, null, null, true);';
      $colScript .= '  refreshList("idType","scope", indicatorableArray[this.value]);';
      $colScript .= '</script>';
    }
    if ($colName=='idIndicator') { 
      $colScript .= '<script type="dojo/connect" event="onChange" args="evt">';
      $colScript .= '  dijit.byId("idWarningDelayUnit").set("value",null);';
      $colScript .= '  dijit.byId("idAlertDelayUnit").set("value",null);';
      $colScript .= '  dijit.byId("warningValue").set("value",null);';
      $colScript .= '  dijit.byId("alertValue").set("value",null);';
      $colScript .= '  refreshList("idWarningDelayUnit","idIndicator", this.value, null, null, false);';
      $colScript .= '  refreshList("idAlertDelayUnit","idIndicator", this.value, null, null, false);';
      $colScript .= '</script>';
    }  
    return $colScript;
  }
  
  public function control(){
    $result="";
    if (! trim($this->idIndicatorable)) {
    	$result.='<br/>' . i18n('messageMandatory',array(i18n('colElement')));
    }
    if (! trim($this->idIndicator)) {
      $result.='<br/>' . i18n('messageMandatory',array(i18n('colIdIndicator')));
    }
    if ($this->alertValue!="" and ! trim($this->idAlertDelayUnit) ) {
      $result.='<br/>' . i18n('messageMandatory',array(i18n('colUnit')));
    }
    if ($this->warningValue!="" and ! trim($this->idWarningDelayUnit) ) {
      $result.='<br/>' . i18n('messageMandatory',array(i18n('colUnit')));
    }    
    //if (! trim($this->idType)) {
    //  $result.='<br/>' . i18n('messageMandatory',array(i18n('colType')));
    //}
    $crit=array('idIndicatorable'=>trim($this->idIndicatorable),
                'idIndicator'=>trim($this->idIndicator),
                'idType'=>trim($this->idType));
    $elt=SqlElement::getSingleSqlElementFromCriteria('IndicatorDefinition', $crit);
    if ($elt and $elt->id and $elt->id!=$this->id) {
      $result.='<br/>' . i18n('errorDuplicateIndicator');
    }
    $defaultControl=parent::control();
    if ($defaultControl!='OK') {
      $result.=$defaultControl;
    }if ($result=="") {
      $result='OK';
    }
    return $result;
  }
  
}
?>