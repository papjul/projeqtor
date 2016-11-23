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
 * User is a resource that can connect to the application.
 */ 
require_once('_securityCheck.php');
class ContactMain extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $_spe_image;
  public $name;
  public $userName;
  public $initials;
  public $email;
  public $idProfile;
  public $idClient;
  public $idProvider;
  public $function;
  public $phone;
  public $mobile;
  public $fax;
  public $isResource;
  public $isUser;
  public $idle;
  public $description;
  public $_sec_Address;
  public $designation;
  public $street;
  public $complement;
  public $zip;
  public $city;
  public $state;
  public $country;  
  public $_sec_Affectations;
  public $_spe_affectations;
  public $_sec_Miscellaneous;
  public $dontReceiveTeamMails;
  public $password;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%"># ${id}</th>
    <th field="name" width="30%">${realName}</th>
    <th field="photo" formatter="thumb32" width="5%">${photo}</th>
    <th field="initials" width="5%">${initials}</th>  
    <th field="nameClient" width="15%">${client}</th>
    <th field="nameProfile" width="10%" formatter="translateFormatter">${idProfile}</th>
    <th field="userName" width="15%">${userName}</th>
    <th field="isUser" width="5%" formatter="booleanFormatter">${isUser}</th>
    <th field="isResource" width="5%" formatter="booleanFormatter">${isResource}</th>    
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"required, truncatedWidth100",
                                          "userName"=>"truncatedWidth100",
                                          "email"=>"truncatedWidth100",
                                          "idProfile"=>"readonly",
                                          "isUser"=>"readonly",
                                          "isResource"=>"readonly",
                                          "password"=>"hidden" 
  );    
  
  private static $_databaseTableName = 'resource';

  private static $_databaseColumnName = array('name'=>'fullName',
                                              'userName'=>'name');

  private static $_databaseCriteria = array('isContact'=>'1');
  
  private static $_colCaptionTransposition = array('name'=>'realName');
  
  /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
        
    $crit=array("name"=>"menuUser");
    $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', $crit);
    if (! $menu) {
      return;
    }      
    if (securityCheckDisplayMenu($menu->id)) {
      $canUpdateUser=(securityGetAccessRightYesNo('menuUser', 'update', $this) == "YES");;
      if (! $canUpdateUser) {
        self::$_fieldsAttributes["idProfile"]="readonly";
      } else {
        self::$_fieldsAttributes["isUser"]="";
        self::$_fieldsAttributes["idProfile"]="";
        if ($this->isUser) {
          self::$_fieldsAttributes["idProfile"]="required";
          self::$_fieldsAttributes["userName"]="required,truncatedWidth100";
        }
      }
    }
    
    $crit=array("name"=>"menuResource");
    $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', $crit);
    if (! $menu) {
      return;
    }     
    if (securityCheckDisplayMenu($menu->id)) {
      self::$_fieldsAttributes["isResource"]="";
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
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="isUser") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.checked) { ';
      $colScript .= '    dijit.byId("userName").set("required", "true");';
      $colScript .= '    dojo.addClass(dijit.byId("userName").domNode,"required");';
      $colScript .= '    dijit.byId("idProfile").set("required", "true");';
      $colScript .= '    dojo.addClass(dijit.byId("idProfile").domNode,"required");';
      $colScript .= '  } else {';
      $colScript .= '    dijit.byId("userName").set("required", null);';
      $colScript .= '    dojo.removeClass(dijit.byId("userName").domNode,"required");';
      $colScript .= '    dijit.byId("idProfile").set("required", "true");';
      $colScript .= '    dojo.removeClass(dijit.byId("idProfile").domNode,"required");';
      $colScript .= '    dijit.byId("userName").set("value", "");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
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
    if ($this->isUser and (! $this->userName or $this->userName=="")) {
      $result.='<br/>' . i18n('messageMandatory',array(i18n('colUserName')));
    } 
    // Control that user is not duplicate
    $crit=array("name"=>$this->userName);
    $usr=new User();
    $lst=$usr->getSqlElementsFromCriteria($crit,false);
    if (count($lst)>0) {
      if (! $this->id or count($lst)>1 or $lst[0]->id!=$this->id) {
        $result.='<br/>' . i18n('errorDuplicateUser');
      }
    }
    $old=$this->getOld();
    // if uncheck isResource must check resource for deletion
    if ($old->isResource and ! $this->isResource and $this->id) {
        $obj=new Resource($this->id);
        $resultDelete=$obj->deleteControl(true);
        if ($resultDelete and $resultDelete!='OK') {
          $result.=$resultDelete;
        }
    }
    // if uncheck isUser must check user for deletion
    if ($old->isUser and ! $this->isUser and $this->id) {
        $obj=new User($this->id);
        $resultDelete=$obj->deleteControl(true);
        if ($resultDelete and $resultDelete!='OK') {
          $result.=$resultDelete;
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

  public function save() {
    if ($this->isUser and !$this->password and Parameter::getGlobalParameter('initializePassword')=="YES") {
      $paramDefaultPassword=Parameter::getGlobalParameter('paramDefaultPassword');
      $this->password=md5($paramDefaultPassword);
    }
  	$result=parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
    Affectation::updateAffectations($this->id);
    return $result;
  }
  
  public function deleteControl($nested=false) {
    
    $result="";
    if ($this->isUser) {    
      $crit=array("name"=>"menuUser");
      $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', $crit);
      if (! $menu) {
        return "KO";
      }     
      if (! securityCheckDisplayMenu($menu->id)) {
        $result="<br/>" . i18n("msgCannotDeleteContact");
        return $result;
      }             
    }
    /*$rec = new Recipient();
    $crit = array("id"=>$this->idRecipient);
    $recList = $rec->getSqlElementsFromCriteria($crit,false);
    if (count($recList)!=0) {
    	//$result = "Suppression impossible : contact li&eacute; a un contractant";
    	$result="<br/>" . i18n("msgCannotDeleteContact");
    }*/
    if (! $nested) {
	  // if uncheck isResource must check resource for deletion
	    if ($this->isResource) {
	        $obj=new Resource($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('Resource').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;
	        }
	    }
	  // if uncheck isUser must check user for deletion
	    if ($this->isUser) {
	        $obj=new User($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('User').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;
	        }
	    }
    }
    if ($nested) {
      SqlElement::unsetRelationShip('Contact','Affectation');
    }
    $resultDelete=parent::deleteControl();
    if ($result and $resultDelete) {
      $resultDelete='<b><br/>'.i18n('Contact').' #'.htmlEncode($this->id).' :</b>'.$resultDelete.'<br/>';
    } 
    $result=$resultDelete.$result;
    return $result;
  }
  
  public function drawContactsList($critArray) {
    $result="<table>";
    $conList=$this->getSqlElementsFromCriteria($critArray, false);
    foreach ($conList as $con) {
      $result.= '<tr><td valign="top" width="20px"><img src="css/images/iconList16.png" height="16px" /></td><td>';
      $result.=htmlDrawLink($con);
      $result.='</td></tr>';
    }
    $result .="</table>";
    return $result; 
  }
  
  public function drawSpecificItem($item){
  	global $print, $outMode, $largeWidth;
    $result="";
    if ($item=='affectations') {
      $aff=new Affectation();
      $critArray=array('idContact'=>(($this->id)?$this->id:'0'));
      $affList=$aff->getSqlElementsFromCriteria($critArray, false);
      drawAffectationsFromObject($affList, $this, 'Project', false);   
      return $result;
    }
    if ($item=='image' and $this->id){
      $result=Affectable::drawSpecificImage(get_class($this),$this->id, $print, $outMode, $largeWidth);
    	echo $result;
    }
  }
  
  public function getPhotoThumb($size) {
    $result="";
    $image=SqlElement::getSingleSqlElementFromCriteria('Attachment', array('refType'=>'Resource', 'refId'=>$this->id));
    if ($image->id and $image->isThumbable()) {
      $result.='<img src="'. getImageThumb($image->getFullPathFileName(),$size).'" '
             . ' title="'.htmlEncode($image->fileName).'" style="cursor:pointer"'
             . ' onClick="showImage(\'Attachment\',\''.htmlEncode($image->id).'\',\''.htmlEncode($image->fileName,'protectQuotes').'\');" />';
    } else {
      $result='<div style="width:'.$size.';height:'.$size.';border:1px solide grey;">&nbsp;</span>';
    }
    return $result;
  }
}
?>