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
class Version extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $scope;
  public $idProduct;
  public $versionNumber;
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
  public $_Attachment=array();
  public $_Note=array();
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%" ># ${id}</th>
    <th field="name" width="20%" >${versionName}</th>
    <th field="nameProduct" width="25%" >${productName}</th>
    <th field="plannedEisDate" width="10%" formatter="dateFormatter">${plannedEis}</th>
    <th field="realEisDate" width="10%" formatter="dateFormatter">${realEis}</th>
    <th field="plannedEndDate" width="10%" formatter="dateFormatter">${plannedEnd}</th>
    <th field="realEndDate" width="10%" formatter="dateFormatter">${realEnd}</th>
    <th field="isEis" width="5%" formatter="booleanFormatter" >${isEis}</th>
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"required", "idProduct"=>"required"
  );   

  private static $_colCaptionTransposition = array('idContact'=>'contractor', 'idResource'=>'responsible'
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
    if ($item=='XXXVersionProjects') {
      $result .="<table><tr><td class='label' valign='top'><label>" . i18n('versions') . "&nbsp;:&nbsp;</label>";
      $result .="</td><td>";
      if ($this->id) {
        $result .= "xx";
      }
      $result .="</td></tr></table>";
      return $result;
    } 
  }
  
  public function drawVersionsList($critArray,$withProjects=false) {
    $result="<table>";
    $versList=$this->getSqlElementsFromCriteria($critArray,false,null,'name asc',false,true);
    foreach ($versList as $vers) {
      $result.= '<tr>';
      $result.= '<td style="padding-left:15px;">&nbsp;&nbsp;</td><td valign="top" width="20px" style="padding-left:5px;" class="icon'.$vers->scope.'Version16" height="16px" />&nbsp;</td>';
      $style="";
      if ($vers->idle) {$style='color#5555;text-decoration: line-through;';}
      else if ($vers->isEis) {$style='font-weight: bold;';}
      $result.= '<td style="vertical-align:top;'.$style.'">';   
      $result.="#$vers->id - ".htmlDrawLink($vers);
      if ($withProjects) {
        $result.='<td>';
        $vp=new VersionProject();
        $vpList=$vp->getSqlElementsFromCriteria(array('idVersion'=>$vers->id),false,null,null,false,true);
        $result.= '<table>';
        foreach ($vpList as $vp) {
          $result.= '<tr>';
          $result.= '<td style="padding-left:15px;"><td valign="top" width="20px" style="padding-left:5px;" class="iconProject16" height="16px" />&nbsp;</td>';
          $result.= '<td style="vertical-align:top;">'.SqlList::getNameFromId('Project', $vp->idProject).'</td>';
          $result.= '</tr>';
        }
        $result.= '</table>';
        $result.='</td>';
      }
      $result.= '</td></tr>';
    }
    $result .="</table>";
    return $result; 
  }
  
  public function save() {
  	$result=parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
  	if ($this->idle) {
  		VersionProject::updateIdle('Version', $this->id);
  	}
  	
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
  
    if (trim($this->versionNumber)) {
      $cpt=$this->countSqlElementsFromCriteria(null,"idProduct=".Sql::fmtId($this->idProduct)." and versionNumber='$this->versionNumber' and id!=".Sql::fmtId($this->id));
      if ($cpt>0) {
        $result.="<br/>" . i18n('errorDuplicate');
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
  static protected function drawFlatStructureButton($class,$id) {
    global $print;
    if ($print) return "";
    $result='<br/><table>';
    $result.='<tr><td>';
    $result.='<button id="showFlatStructureButton" dojoType="dijit.form.Button" showlabel="true"';
    $result.=' title="'.i18n('showFlatStructure').'" style="vertical-align: middle;">';
    $result.='<span>' . i18n('showFlatStructure') . '</span>';
    $result.='<script type="dojo/connect" event="onClick" args="evt">';
    $page="../report/productVersionFlatStructure.php?objectClass=$class&objectId=$id";
    $result.="var url='$page';";
    $result.='url+="&format=print";';
    $result.='showPrint(url, null, null, "html", "P");';
    $result.='</script>';
    $result.='</button>';
    $result.='<button id="showFlatStructureButtonCsv" dojoType="dijit.form.Button" showlabel="false" ';
    $result.=' title="'.i18n('showFlatStructure').'" iconClass="dijitButtonIcon dijitButtonIconCsv" class="roundedButtonSmall">';
    $result.='<script type="dojo/connect" event="onClick" args="evt">';
    $page="../report/productVersionFlatStructure.php?objectClass=$class&objectId=$id";
    $result.="var url='$page';";
    $result.='url+="&format=csv";';
    $result.='showPrint(url, null, null, "csv", "P");';
    $result.='</script>';
    $result.='</button>';
    $result.='</td>';
    $result.='</tr></table>';
    return $result;
  }

}
?>