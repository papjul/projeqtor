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
 * Menu defines list of items to present to users.
 */ 
require_once('_securityCheck.php');
class StatusMail extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $idMailable;
  public $idType;
  public $idStatus;
  public $idEvent;
  public $idle;
  public $_sec_SendMail;
  public $mailToContact;
  public $mailToUser;
  public $mailToResource;
  public $mailToSponsor;
  public $mailToProject;
  public $mailToLeader;
  public $mailToManager;
  public $mailToAssigned;
  public $mailToOther;
  public $otherMail;
  
  public $_noCopy;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="nameMailable" formatter="translateFormatter" width="12%" >${idMailable}</th>
    <th field="nameType" formatter="nameFormatter" width="9%" >${type}</th>
    <th field="colorNameStatus" width="10%" formatter="colorNameFormatter">${newStatus}</th>
    <th field="nameEvent" formatter="translateFormatter" width="10%" >${orOtherEvent}</th>
    <th field="mailToContact" width="7%" formatter="booleanFormatter" >${mailToContact}</th>    
    <th field="mailToUser" width="7%" formatter="booleanFormatter" >${mailToUser}</th>
    <th field="mailToResource" width="7%" formatter="booleanFormatter" >${mailToResource}</th>
    <th field="mailToProject" width="7%" formatter="booleanFormatter" >${mailToProject}</th>
    <th field="mailToLeader" width="7%" formatter="booleanFormatter" >${mailToLeader}</th>
    <th field="mailToManager" width="7%" formatter="booleanFormatter" >${mailToManager}</th>
    <th field="mailToAssigned" width="7%" formatter="booleanFormatter" >${mailToAssigned}</th>
    <th field="mailToOther" width="7%" formatter="booleanFormatter" >${mailToOther}</th>
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("idMailable"=>"", 
                                  "mailToOther"=>"nobr",
                                  "otherMail"=>"",
                                  "idType"=>"nocombo", 
  		                            "mailToSponsor"=>"hidden,calculated"
  );  
  
  private static $_colCaptionTransposition = array('idStatus'=>'newStatus',
  'otherMail'=>'email',
  'idEvent'=>'orOtherEvent',
  'idType'=>'type');
  
  //private static $_databaseColumnName = array('idResource'=>'idUser');
  private static $_databaseColumnName = array();
    
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    if ($this->id) {
      self::$_fieldsAttributes["idMailable"]='readonly';
    }
  }

   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

  public function control() {
    $result="";
    if (! trim($this->idMailable)) {
    	$result.='<br/>' . i18n('messageMandatory',array(i18n('colElement')));
    }
    $crit="idMailable='" . Sql::fmtId($this->idMailable) . "'";
    if (trim($this->idStatus)) {
    	$crit.=" and idStatus='" . Sql::fmtId($this->idStatus) . "'";
    }
    if (trim($this->idEvent)) {
      $crit.=" and idEvent='" . Sql::fmtId($this->idEvent) . "'";
    }
    if (trim($this->idType)) {
      $crit.=" and idType='" . Sql::fmtId($this->idType) . "'";
    } else {
      $crit.=" and idType is null";	
    }
    $crit.=" and id<>'" . Sql::fmtId($this->id) . "'";
    $list=$this->getSqlElementsFromCriteria(null, false, $crit);
    if (count($list)>0) {
      $result.="<br/>" . i18n('errorDuplicateStatusMail',null);
    }
    if (!trim($this->idStatus) and !trim($this->idEvent)) {
    	$result.="<br/>" . i18n('messageMandatory',array(i18n('colNewStatus')." ".i18n('colOrOtherEvent')));
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
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
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
    } else if ($colName=="idStatus") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value!=" ") { ';
      $colScript .= '    dijit.byId("idEvent").set("value"," ");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="idEvent") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value!=" ") { ';
      $colScript .= '    dijit.byId("idStatus").set("value"," ");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="mailToAssigned") {
    	$colScript .= '<script type="dojo/connect" event="onClick" >';
    	$colScript .= ' mailable=dijit.byId("idMailable");';
    	//$colScript .= ' alert(mailable.get("value")+" => "+mailable.get("displayedValue"));';
    	$colScript .= ' mVal=mailable.get("displayedValue");';
    	$colScript .= ' if (this.checked && mVal!=i18n("Activity") && mVal!=i18n("Meeting") && mVal!=i18n("TestSesion")) { ';
    	$colScript .= '   showAlert(i18n("msgIncorrectReceiver"));';
    	$colScript .= '   this.checked=false;';
    	$colScript .= ' }'; 
    	$colScript .= '</script>';
    } else if ($colName=='idMailable') { 
      $colScript .= '<script type="dojo/connect" event="onChange" args="evt">';
      $colScript .= '  dijit.byId("idType").set("value",null);';
      $colScript .= '  refreshList("idType","scope", mailableArray[this.value]);';
      $colScript .= '</script>';
    }
    return $colScript;
  }
}
?>