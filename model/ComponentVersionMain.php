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
class ComponentVersionMain extends Version {

  // List of fields that will be exposed in general user interface
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $scope;
  public $idComponent;
  public $versionNumber;
  public $idComponentVersionType;
  public $name;
  public $idContact;
  public $idResource;
  public $creationDate;
  public $idUser;
  public $_tab_4_2 = array('initial', 'planned', 'real', 'done', 'eisDate', 'endDate');
  public $initialEisDate;
  public $plannedEisDate;
  public $realEisDate;
  public $isEis;
  public $initialEndDate;
  public $plannedEndDate;
  public $realEndDate;
  public $idle;
  public $description;
  public $_sec_ComponentVersionStructure;
  public $_componentVersionStructure=array();
  public $_sec_ComponentVersionComposition;
  public $_componentVersionComposition=array();
  public $_spe_tenders;
  public $_Attachment=array();
  public $_Note=array();
  public $_nbColMax=3;
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="name" width="20%" >${versionName}</th>
    <th field="nameComponent" width="25%" >${componentName}</th>
    <th field="plannedEisDate" width="10%" formatter="dateFormatter">${plannedEis}</th>
    <th field="realEisDate" width="10%" formatter="dateFormatter">${realEis}</th>
    <th field="plannedEndDate" width="10%" formatter="dateFormatter">${plannedEnd}</th>
    <th field="realEndDate" width="10%" formatter="dateFormatter">${realEnd}</th>
    <th field="isEis" width="5%" formatter="booleanFormatter" >${isEis}</th>
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"required", 
      "idComponent"=>"required",
      "idContact"=>"hidden",
      "idResource"=>"hidden",
      "scope"=>"hidden",
      "idProduct"=>"hidden"
  );   

  private static $_colCaptionTransposition = array('idContact'=>'contractor', 'idResource'=>'responsible'
  );
  private static $_databaseColumnName = array('idComponent'=>'idProduct','idComponentVersionType'=>'idVersionType');
  private static $_databaseTableName = 'version';
  private static $_databaseCriteria = array('scope'=>'Component');
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    $paramNameAutoformat=Parameter::getGlobalParameter('versionNameAutoformat');
    if ($paramNameAutoformat=='YES') {
      self::$_fieldsAttributes['name']='readonly';
      self::$_fieldsAttributes['versionNumber']='required';
    } else {
      self::$_fieldsAttributes['versionNumber']='hidden';
    }
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
  
  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
  }
  
  /** ========================================================================
   * Return the specific databaseTableName
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }
  
  /** ========================================================================
   * Return the specific database criteria
   * @return the databaseTableName
   */
  protected function getStaticDatabaseCriteria() {
    return self::$_databaseCriteria;
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
    if ($colName=="initialEisDate") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (! dijit.byId("plannedEisDate").get("value")) {'; 
      $colScript .= '  dijit.byId("plannedEisDate").set("value",this.value);'; 
      $colScript .= '};';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if ($colName=="initialEndDate") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (! dijit.byId("plannedEndDate").get("value")) {'; 
      $colScript .= '  dijit.byId("plannedEndDate").set("value",this.value);'; 
      $colScript .= '};';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if ($colName=="realEisDate") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (this.value) {'; 
      $colScript .= '  dijit.byId("isEis").set("checked",true);';
      $colScript .= '} else {;';
      $colScript .= '  dijit.byId("isEis").set("checked",false);';
      $colScript .= '};'; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if ($colName=="isEis") { 
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (this.checked) { ';
      $colScript .= '  if (! dijit.byId("realEisDate").get("value")) {';
      $colScript .= '    var curDate = new Date();';
      $colScript .= '    dijit.byId("realEisDate").set("value", curDate); ';
      $colScript .= '  }';
      $colScript .= '} else {;';    
      $colScript .= '  dijit.byId("realEisDate").set("value", null); ';
      $colScript .= '};';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';  
    }
    if ($colName=="realEndDate") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (this.value) {'; 
      $colScript .= '  dijit.byId("idle").set("checked",true);'; 
      $colScript .= '} else {;';
      $colScript .= '  dijit.byId("idle").set("checked",false);';
      $colScript .= '};'; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if ($colName=="idle") { 
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= 'if (this.checked) { ';
      $colScript .= '  if (! dijit.byId("realEndDate").get("value")) {';
      $colScript .= '    var curDate = new Date();';
      $colScript .= '    dijit.byId("realEndDate").set("value", curDate); ';
      $colScript .= '  }';   
      $colScript .= '} else {;';    
      $colScript .= '  dijit.byId("realEndDate").set("value", null); '; 
      $colScript .= '};';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';  
    }
    $paramNameAutoformat=Parameter::getGlobalParameter('versionNameAutoformat');
    if ($paramNameAutoformat=='YES') {
      if ($colName=="versionNumber") {
        $colScript .= '<script type="dojo/method" event="onKeyPress" >';
        $colScript .= '  setTimeout(\'updateVersionName("'.Parameter::getGlobalParameter("versionNameAutoformatSeparator").'");\',100);';
        $colScript .= '  formChanged();';
        $colScript .= '</script>';
      }
      if ($colName=="versionNumber" or $colName=="idComponent") {
        $colScript .= '<script type="dojo/connect" event="onChange" >';
        $colScript .= '  updateVersionName("'.Parameter::getGlobalParameter("versionNameAutoformatSeparator").'");';
        $colScript .= '  formChanged();';
        $colScript .= '</script>';
      }
    }
    return $colScript;
  }
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  

  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    $result="";
    if ($item=='tenders') {
       Tender::drawListFromCriteria('id'.get_class($this),$this->id);
    } 
  }
  
  
  public function save() {
    $old=$this->getOld();
    $this->scope='Component';
    $paramNameAutoformat=Parameter::getGlobalParameter('versionNameAutoformat');
    if ($paramNameAutoformat=='YES') {
      $separator=Parameter::getGlobalParameter('versionNameAutoformatSeparator');
      $this->name=SqlList::getNameFromId('Component', $this->idComponent).$separator.$this->versionNumber;
    }
  	$result=parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
  	if ($this->idle) {
  		VersionProject::updateIdle('Version', $this->id);
  	}
  	if ($this->idComponent!=$old->idComponent) {
  	  $comp=new Component($this->idComponent);
  	  $comp->updateAllVersionProject();
  	  if (trim($old->idComponent)) {
  	    $comp=new Component($old->idComponent);
  	    $comp->updateAllVersionProject();
  	  }
  	}
  	return $result;
  }
  public function delete() {
    $result=parent::delete();
    $vp=new VersionProject();
    $vpList=$vp->getSqlElementsFromCriteria(array('idVersion'=>$this->id));
    foreach ($vpList as $vp) {
      $vp->delete();
    }
    return $result;
  }
  public function getLinkedProjects($withName=true) {
    $vp=new VersionProject();
    $result=array();
    $vpList=$vp->getSqlElementsFromCriteria(array('idVersion'=>$this->id));
    foreach ($vpList as $vp) {
      $result[$vp->idProject]=($withName)?SqlList::getNameFromId('Project', $vp->idProject):$vp->idProject;
    }
    return $result;
  }
  public function getLinkedProductVersions($withName=true) {
    $result=array();
    $vpList=ProductVersionStructure::getStructure($this->id);
    foreach ($vpList as $idVers) {
      $vers=new Version($idVers);
      if ($vers->scope=='Product') {
        $result[$idVers]=($withName)?$vers->name:$vers->id;
      }
    }
    return $result;
  }
}
?>