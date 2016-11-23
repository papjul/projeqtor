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
class UserMain extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $_spe_image;
  public $name;
  public $resourceName;
  public $initials;
  public $email;
  public $idProfile;
  public $locked;
  public $loginTry;
  
  public $isContact;
  public $isResource=0;
  
  public $idle;
  public $description;
  public $_sec_Affectations;
  public $_spe_affectations;
  public $_sec_Miscellaneous;
  public $password;
  public $_spe_buttonSendMail;
  public $dontReceiveTeamMails;
  public $isLdap;
  public $apiKey;

  public $_arrayFilters=array();
  //public $_arrayFiltersId=array();
  public $_arrayFiltersDetail=array();
  //public $_arrayFiltersDetailId=array();
  public $salt;
  public $crypto;
  public $cookieHash;
  public $passwordChangeDate;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%"># ${id}</th>
    <th field="name" width="20%">${userName}</th>
    <th field="photo" formatter="thumb32" width="5%">${photo}</th>
    <th field="nameProfile" width="15%" formatter="translateFormatter">${idProfile}</th>
    <th field="resourceName" width="25%">${realName}</th>
    <th field="initials" width="10%">${initials}</th> 
    <th field="isResource" width="5%" formatter="booleanFormatter">${isResource}</th>
    <th field="isContact" width="5%" formatter="booleanFormatter">${isContact}</th>
    <th field="isLdap" width="5%" formatter="booleanFormatter">${isLdap}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';
  
  private static $_fieldsAttributes=array("id"=>"nobr",
                                          "name"=>"required, truncatedWidth100",
                                          "resourceName"=>"truncatedWidth100",
  		                                    "email"=>"truncatedWidth100",
  										                    "isLdap"=>"",
                                          "idProfile"=>"required",
                                          "loginTry"=>"hidden",
                                          "salt"=>'hidden', 
                                          "crypto"=>'hidden',
  		                                    "cookieHash"=>'hidden',
  		                                    "passwordChangeDate"=>'hidden',
  		                                    "apiKey"=>"readonly"
  );  
  
  public $_calculateForColumn=array("name"=>"coalesce(fullName,concat(name,' #'))");
  
  private static $_databaseCriteria = array('isUser'=>'1');
  
  private static $_databaseColumnName = array('resourceName'=>'fullName');
  
  private static $_colCaptionTransposition = array('resourceName'=>'realName',
   'name'=> 'userName');
  
  private static $_databaseTableName = 'resource';
  
  private $_accessControlRights;
  
  public $_accessControlVisibility; // ALL if user should have all projects listed

  private $_affectedProjects;  // Array listing all affected projects
  private $_affectedProjectsIncludingClosed;  // Array listing all affected projects
  private $_specificAffectedProfiles; // Array listing all projects affected with profile different from default
  private $_specificAffectedProfilesIncludingClosed; // Array listing all projects affected with profile different from default
  private $_allProfiles;
  private $_allAccessRights;
  
  public $_visibleProjects;   // Array listing all visible projects (affected and their subProjects)
  private $_visibleProjectsIncludingClosed;
  private $_hierarchicalViewOfVisibleProjects;
  private $_hierarchicalViewOfVisibleProjectsNotClosed;
  
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    global $objClass;
    $paramDefaultPassword=Parameter::getGlobalParameter('paramDefaultPassword');
  	parent::__construct($id,$withoutDependentObjects);
    
  	if (! $this->id and Parameter::getGlobalParameter('initializePassword')=="YES") {
  		$tmpSalt=hash('sha256',"projeqtor".date('YmdHis'));
  		$this->password=hash('sha256',$paramDefaultPassword.$tmpSalt);
  	}
    if (! $this->id) {
      $this->idProfile=Parameter::getGlobalParameter('defaultProfile');
    }
  	// Fetch data to set attributes only to display user. Other access to User (for History) don't need these attributes.
  	if (isset($objClass) and $objClass and $objClass=='User') {
	    $crit=array("name"=>"menuContact");
	    $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', $crit);
	    if (! $menu) {
	      return;
	    }     
	    if (securityCheckDisplayMenu($menu->id)) {
	      self::$_fieldsAttributes["isContact"]="";
	    }
	    if ($this->isLdap!=0) {
	    	self::$_fieldsAttributes["name"]="readonly, truncatedWidth100";
	    	//self::$_fieldsAttributes["resourceName"]="readonly";
	    	self::$_fieldsAttributes["email"]="readonly, truncatedWidth100";
	    	self::$_fieldsAttributes["password"]="hidden";
	    }
	    if ($this->isResource or $this->isContact) {
	      self::$_fieldsAttributes["resourceName"]="required,truncatedWidth100";
	    }
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
  
// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo framework)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="isResource") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.checked || dijit.byId("isContact").get("checked")) { ';
      $colScript .= '    dijit.byId("resourceName").set("required", "true");';
      $colScript .= '    dojo.addClass(dijit.byId("resourceName").domNode,"required");';
      $colScript .= '  } else {';
      $colScript .= '    dijit.byId("resourceName").set("required", null);';
      $colScript .= '    dojo.removeClass(dijit.byId("resourceName").domNode,"required");';
      //$colScript .= '    dijit.byId("resourceName").set("value", "");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    if ($colName=="isContact") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.checked || dijit.byId("isResource").get("checked")) { ';
      $colScript .= '    dijit.byId("resourceName").set("required", "true");';
      $colScript .= '    dojo.addClass(dijit.byId("resourceName").domNode,"required");';
      $colScript .= '  } else {';
      $colScript .= '    dijit.byId("resourceName").set("required", null);';
      $colScript .= '    dojo.removeClass(dijit.byId("resourceName").domNode,"required");';
      //$colScript .= '    dijit.byId("resourceName").set("value", "");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    return $colScript;

  }
  
  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    global $print, $outMode, $largeWidth;
    $result="";
    if ($item=='buttonSendMail') {
      $canUpdate=(securityGetAccessRightYesNo('menuUser', 'update', $this) == "YES");
      if ($print or !$canUpdate) {
        return "";
      } 
      $result .= '<tr><td valign="top" class="label"><label></label></td><td>';
      $result .= '<button id="sendInfoToUser" dojoType="dijit.form.Button" showlabel="true"'; 
      $result .= ' title="' . i18n('sendInfoToUser') . '" >';
      $result .= '<span>' . i18n('sendInfoToUser') . '</span>';
      $result .=  '<script type="dojo/connect" event="onClick" args="evt">';
      $result .= '   if (checkFormChangeInProgress()) {return false;}';
	    $result .=  '  var email="";';
	    $result .=  '  if (dojo.byId("email")) {email = dojo.byId("email").value;}';
      $result .=  '  if (email==null || email=="") { ';
      $result .=  '    showAlert("' . i18n('emailMandatory') . '");';
	    $result .=  '  } else {';
      $result .=  '    loadContent("../tool/sendMail.php","resultDiv","objectForm",true);';
	    $result .=  '  }';	
      $result .= '</script>';
      $result .= '</button>';
      $result .= '</td></tr>';
      return $result;
    } 
    if ($item=='affectations') {
      $aff=new Affectation();
      $critArray=array('idUser'=>(($this->id)?$this->id:'0'));
      $affList=$aff->getSqlElementsFromCriteria($critArray, false);
      drawAffectationsFromObject($affList, $this, 'Project', false);   
      return $result;
    }
    if ($item=='image' and $this->id){
      $result=Affectable::drawSpecificImage(get_class($this),$this->id, $print, $outMode, $largeWidth);
    	echo $result;
    }
  }

  /** =========================================================================
   * Get the access rights for all the screens
   * For more information, refer to AccessControl.ofp diagram 
   * @return an array containing rights for every screen
   *  must be redefined in the inherited class
   */
  public function getAccessControlRights($obj=null) {
    // _accessControlRights fetched yet, just return it
    SqlElement::$_cachedQuery['AccessProfile']=array();
    
    $profile=$this->idProfile;
    if ($obj) {
      $profile=$this->getProfile($obj);
    }
    if ($this->_accessControlRights and isset($this->_accessControlRights[$profile])) {       
      return $this->_accessControlRights[$profile];
    }        
    $menuList=SqlList::getListNotTranslated('Menu');
    $noAccessArray=array( 'read' => 'NO', 'create' => 'NO', 'update' => 'NO', 'delete' => 'NO','report'=>'NO');
    $allAccessArray=array( 'read' => 'ALL', 'create' => 'ALL', 'update' => 'ALL', 'delete' => 'ALL', 'report'=>'ALL');
    $readAccessArray=array( 'read' => 'ALL', 'create' => 'NO', 'update' => 'NO', 'delete' => 'NO', 'report'=>'ALL');
    // first time function is called for object, so go and fetch data
    $this->_accessControlVisibility='PRO';
    $accessControlRights=array();
    $accessScopeList=SqlList::getList('AccessScope', 'accessCode');
    $accessScopeRW=SqlList::getList('ListReadWrite', 'code');
    $accessRight=new AccessRight();
    $noAccessAllowed=array();
    $crit=array('idProfile'=>$profile);
    $accessRightList=$accessRight->getSqlElementsFromCriteria( $crit, false);
    $habilitation=new Habilitation();
    $crit=array('idProfile'=>$profile);
    $habilitationList=$habilitation->getSqlElementsFromCriteria( $crit, false);
    foreach ($habilitationList as $hab) { // if allowAcces = 1 in habilitation (access to screen), default access is all
    	if (array_key_exists($hab->idMenu,$menuList)) {
    	  $menuName=$menuList[$hab->idMenu];
    	  if ($hab->allowAccess==1) {
    	    $accessControlRights[$menuName]=$allAccessArray;
    	  } else {
    	    $accessControlRights[$menuName]=$noAccessArray;
    	    $accessControlRights[$menuName]['report']='ALL';
    	    $noAccessAllowed[$menuName]=true;
    	  }
    	}
    }
    foreach ($accessRightList as $arObj) {
      $menuName=(array_key_exists($arObj->idMenu,$menuList))?$menuList[$arObj->idMenu]:'';
      if (! $menuName or ! array_key_exists($menuName, $accessControlRights)) {
        $accessControlRights[$menuName]=$noAccessArray;	
      } else {
        $scopeArray=$noAccessArray;
        $accessProfile=new AccessProfile($arObj->idAccessProfile);
        if ($arObj->idAccessProfile<1000000) {
          $scopeArray=array( 'read' =>  $accessScopeList[$accessProfile->idAccessScopeRead],
                             'create' => $accessScopeList[$accessProfile->idAccessScopeCreate],
                             'update' => $accessScopeList[$accessProfile->idAccessScopeUpdate],
                             'delete' => $accessScopeList[$accessProfile->idAccessScopeDelete],
                             'report' =>  $accessScopeList[$accessProfile->idAccessScopeRead], );
          if ($accessScopeList[$accessProfile->idAccessScopeRead]=='ALL') {
            $this->_accessControlVisibility='ALL';
          }
        } else {     
          if (isset($noAccessAllowed[$menuName]) and $noAccessAllowed[$menuName]) {
          	// Nothing
          } else {
            $RW=$accessScopeRW[$arObj->idAccessProfile];
            if ($RW=='WRITE') {
              $scopeArray=$allAccessArray;
            } else {
              $scopeArray=$readAccessArray;
            }
          }
        }
        $accessControlRights[$menuName]=$scopeArray;
      }
    }
    foreach ($menuList as $menuId=>$menuName) {
      if (! array_key_exists($menuName, $accessControlRights)) {
        $accessControlRights[$menuName]=$noAccessArray; 
      }     	
    }
    // override with habilitation 
    if (! $this->_accessControlRights) {
      $this->_accessControlRights=array();
    }
    $this->_accessControlRights[$profile]=$accessControlRights;
    if ($this->id==getSessionUser()->id and isset($this->_isRetreivedFromSession) and $this->_isRetreivedFromSession) {
      setSessionUser($this); // Store user to cache Data
    }
    return $this->_accessControlRights[$profile];
  }

  /** =========================================================================
   * Get the list of all projects the resource corresponding to the user is affected to
   * @return a list of projects (id=>name)
   */
  public function getAffectedProjects($limitToActiveProjects=true) {
    if ($this->_affectedProjects and $limitToActiveProjects) {
      return $this->_affectedProjects;
    } else if ($this->_affectedProjectsIncludingClosed and ! $limitToActiveProjects) {
      return $this->_affectedProjectsIncludingClosed;  	
    }
    $result=array();
    $aff=new Affectation();
    $crit = array("idResource"=>$this->id);
    if ($limitToActiveProjects) {
    	$crit["idle"]='0';
    }
    $affList=$aff->getSqlElementsFromCriteria($crit,false);
    foreach ($affList as $aff) {
      $prj=new Project($aff->idProject,true); 
    	if (! isset($result[$aff->idProject])) {
	      $result[$aff->idProject]=$prj->name;
	      $lstSubPrj=$prj->getRecursiveSubProjectsFlatList($limitToActiveProjects);
	      foreach ($lstSubPrj as $idSubPrj=>$nameSubPrj) {
	      	$result[$idSubPrj]=$nameSubPrj;
	      }
    	}
    	
    }
    if ($limitToActiveProjects) {
      $this->_affectedProjects=$result;
    } else {
      $this->_affectedProjectsIncludingClosed=$result;
    }
    return $result;
  }

  /** =========================================================================
   * Get the list of all projects where affected profile is different from main profile
   * @return a list of projects (idProject=>idProfile)
   */
  public function getSpecificAffectedProfiles($limitToActiveProjects=true) {
    if ($this->_specificAffectedProfiles and $limitToActiveProjects) {
      return $this->_specificAffectedProfiles;
    } else if ($this->_specificAffectedProfilesIncludingClosed and ! $limitToActiveProjects) {
      return $this->_specificAffectedProfilesIncludingClosed;
    } else {
      $this->getVisibleProjects($limitToActiveProjects); // Will update_specificAffectedProfiles or _specificAffectedProfilesIncludingClosed
      if ($limitToActiveProjects) {
        return $this->_specificAffectedProfiles;
      } else {
        return $this->_specificAffectedProfilesIncludingClosed;
      }
    }
  }
  
  public function getAllProfiles() {
    if ($this->_allProfiles) {
      return $this->_allProfiles;
    } else {
      $this->getVisibleProjects(); // Will update_specificAffectedProfiles or _specificAffectedProfilesIncludingClosed
      return $this->_allProfiles;
    }
  }  
  /** =========================================================================
   * Get the list of all projects the user can have readable access to, 
   * this means the projects the resource corresponding to the user is affected to
   * and their sub projects
   * @return a list of projects id
   */
  public function getVisibleProjects($limitToActiveProjects=true) {
//scriptLog("getVisibleProjects()");
    if ($limitToActiveProjects and $this->_visibleProjects) {
      return $this->_visibleProjects;
    }
    if (! $limitToActiveProjects and $this->_visibleProjectsIncludingClosed) {
      return $this->_visibleProjectsIncludingClosed;
    }
    $result=array();
    // Retrieve current affectation profile for each project
    $resultAff=array();
    $resultProf=array();
    $resultProf[$this->idProfile]=$this->idProfile; // The default profile, even if used on no project
    if ($this->idProfile) {
      $resultProf[$this->idProfile]=$this->idProfile;
    }
    $affProfile=array();
    $aff=new Affectation();
    $crit = array("idResource"=>$this->id);
    if ($limitToActiveProjects) {
      $crit["idle"]='0';
    }
    $affList=$aff->getSqlElementsFromCriteria($crit,false, null,'idProject asc, startDate asc');
    $today=date('Y-m-d');
    foreach ($affList as $aff) {
      if ( (! $aff->startDate or $aff->startDate<=$today) and (! $aff->endDate or $aff->endDate>=$today)) {
        $affProfile[$aff->idProject]=$aff->idProfile;
        $resultProf[$aff->idProfile]=$aff->idProfile;
      }
    }
    $accessRightRead=securityGetAccessRight('menuProject', 'read');
    // For ALL, by default can have access to all projects
    if ($accessRightRead=="ALL") {
    	$listAllProjects=SqlList::getList('Project');
    	foreach($listAllProjects as $idPrj=>$namePrj) {
    		$result[$idPrj]=$namePrj;
    	}
    } 
    // Scpecific rights for projects affected to user : may change rights for ALL (admin)
    $affPrjList=$this->getAffectedProjects($limitToActiveProjects);
    $profile=$this->idProfile;
    foreach($affPrjList as $idPrj=>$namePrj) {
      if (isset($affProfile[$idPrj])) {	        
        $profile=$affProfile[$idPrj];
        $resultAff[$idPrj]=$profile;
        $prj=new Project($idPrj,true);
        $lstSubPrj=$prj->getRecursiveSubProjectsFlatList($limitToActiveProjects);
        foreach ($lstSubPrj as $idSubPrj=>$nameSubPrj) {
          $result[$idSubPrj]=$nameSubPrj;
          $resultAff[$idSubPrj]=$profile;
        }
      } 
    	$result[$idPrj]=$namePrj;
    }
    
    $this->_allProfiles=$resultProf;
    if ($limitToActiveProjects) {
      $this->_visibleProjects=$result;
      $this->_specificAffectedProfiles=$resultAff;
    } else {
      $this->_visibleProjectsIncludingClosed=$result;
      $this->_specificAffectedProfilesIncludingClosed=$resultAff;
    }
    if (getSessionUser()->id==$this->id) {
      setSessionUser($this); // Store user to cache Data
    }  
    return $result;
  }
  
  /** =========================================================================
   * Get the list of all projects the user can have readable access to, 
   * this means the projects the resource corresponding to the user is affected to
   * and their sub projects
   * @return a list of projects id
   */

  public function getHierarchicalViewOfVisibleProjects($hideClosed=false) {
//scriptLog("getHierarchicalViewOfVisibleProjects()");
    if (!$hideClosed and is_array($this->_hierarchicalViewOfVisibleProjects)) {
      return $this->_hierarchicalViewOfVisibleProjects;
    } 
    if ($hideClosed and is_array($this->_hierarchicalViewOfVisibleProjectsNotClosed)) {
      return $this->_hierarchicalViewOfVisibleProjectsNotClosed;
    } 
    $result=array();
    $wbsArray=array();
    $currentTop='0';
    $visibleProjectsList=$this->getVisibleProjects($hideClosed);
    $critList="refType='Project' and refId in (0";
    foreach ($visibleProjectsList as $idPrj=>$namePrj) {
    	$critList.=','.$idPrj;
    }
    $critList.=')';  
    if ($hideClosed) {
    	$critList.=' and idle=0';  
    }
    $ppe=new ProjectPlanningElement();
    $projList=$ppe->getSqlElementsFromCriteria(null, false, $critList, 'wbsSortable', false);
    foreach ($projList as $projPe) {
    	$wbsTest=$projPe->wbsSortable;
    	$wbsParent='';
    	$wbsArray[$projPe->wbsSortable]=array();
    	$wbsArray[$projPe->wbsSortable]['cpt']=0;
    	while (strlen($wbsTest)>3) {
    		$wbsTest=substr($wbsTest,0,strlen($wbsTest)-4);
    		if (array_key_exists($wbsTest,$wbsArray)) {
    			$wbsParent=$wbsTest;
    			$wbsTest="";
    		}
    	}
    	if (! $wbsParent) {
    		$currentTop+=1;
    		$wbsArray[$projPe->wbsSortable]['wbs']=$currentTop;    		
    	} else {
    		$wbsArray[$wbsParent]['cpt']+=1;
    		$wbsArray[$projPe->wbsSortable]['wbs']=$wbsArray[$wbsParent]['wbs'].'.'.$wbsArray[$wbsParent]['cpt'];
    	}
    	$result['#'.$projPe->refId]=$wbsArray[$projPe->wbsSortable]['wbs'].'#'.str_replace('#','&sharp;',$projPe->refName);
    }
    if (! $hideClosed) {
      $this->_hierarchicalViewOfVisibleProjects=$result;
    } else {
    	$this->_hierarchicalViewOfVisibleProjectsNotClosed=$result;
    }
    return $result;
  }
  public function getHierarchicalViewOfVisibleProjectsWithTop() {
    if (is_array($this->_hierarchicalViewOfVisibleProjects)) {
      return $this->_hierarchicalViewOfVisibleProjects;
    } 
    $result=array();
    $visibleProjectsList=$this->getVisibleProjects();
    foreach ($visibleProjectsList as $idPrj=>$namePrj) {
      if (! array_key_exists("#".$idPrj, $result)) {
        $result["#".$idPrj]=$namePrj; 
        $prj=new Project($idPrj);
        while ($prj->idProject) {
          if (array_key_exists("#".$prj->idProject, $result)) {
            $prj->idProject=null;
          } else {
            $prj=new Project($prj->idProject);
            $result["#".$prj->id]=$prj->name;
          }
        }
      }
    }
    $this->_hierarchicalViewOfVisibleProjects=$result;
    return $result;
  }
  
  public function getProfile($objectOrIdProject=null) {
    if (is_object($objectOrIdProject)) {
      if (get_class($objectOrIdProject)=='Project') {
        $idProject=$objectOrIdProject->id;
      } else if (property_exists($objectOrIdProject, 'idProject')) {
        $idProject=$objectOrIdProject->idProject;
      } else {
        return ($this->idProfile)?$this->idProfile:0;
      }
    } else {
      $idProject=$objectOrIdProject;
    }
    if (! $idProject) {
      return ($this->idProfile)?$this->idProfile:0;
    }
    $specificProfiles=$this->getSpecificAffectedProfiles();
    if (isset($specificProfiles[$idProject])) {
      return $specificProfiles[$idProject];
    } else {
      return ($this->idProfile)?$this->idProfile:0;
    }
  }
  
  // Return a list of project with specific access rights depending on profile (only read access taken into account) for a given class
  public function getAccessRights($class,$right=null,$showIdle=false) {
    if ($this->_allAccessRights and isset($this->_allAccessRights[$class])) {
      if ($right) { // Retrieve only for specific right (NO, OWN, RES, PRO, ALL)
        if (isset($this->_allAccessRights[$class][$right])) {
          return $this->_allAccessRights[$class][$right];
        } else {
          return array();
        }
      } else {      // Retrive all rights (one sub-table per right)
        return $this->_allAccessRights[$class];
      }
    }
    $result=array();
    $accessProfile=array();    
    $listAffectedProfiles=$this->getSpecificAffectedProfiles(!$showIdle);
    $obj=new $class();
    $menu=$obj->getMenuClass ();
    foreach($listAffectedProfiles as $prj=>$prf) {
      if (isset($accessProfile[$prf])) {
        $access=$accessProfile[$prf];
      } else {
        $accessList=$this->getAccessControlRights($prj);
        if (isset($accessList[$menu])) {
          $access=$accessList[$menu]['read'];
          $accessProfile[$prf]=$access;
        } else {
          $access="NO"; // Should not be reached because access list should always be set
        }
      }
      if (! isset($result[$access])) $result[$access]=array();
      $result[$access][$prj]=$prj;
    }
    if (! $this->_allAccessRights) $this->_allAccessRights=array();
    $this->_allAccessRights[$class]=$result;
    if ($this->id==getSessionUser()->id and isset($this->_isRetreivedFromSession) and $this->_isRetreivedFromSession) {
      setSessionUser($this); // Store user to cache Data
    }
    // Same code as beginning, but now _allAccessRights[$class] is set
    if ($right) { // Retrieve only for specific right (NO, OWN, RES, PRO, ALL)
      if (isset($this->_allAccessRights[$class][$right])) {
        return $this->_allAccessRights[$class][$right];
      } else {
        return array();
      }
    } else {      // Retrive all rights (one sub-table per right)
      return $this->_allAccessRights[$class];
    }
  }
  /** =========================================================================
   * Reinitalise Visible Projects list to force recalculate
   * @return void
   */  
  public function resetVisibleProjects() {
    $this->_visibleProjects=null;
    $this->_visibleProjectsIncludingClosed=null;
    $this->_affectedProjects=null;
    $this->_affectedProjectsIncludingClosed=null;
    $this->_hierarchicalViewOfVisibleProjects=null;
    $this->_hierarchicalViewOfVisibleProjectsNotClosed=null;
    $this->_specificAffectedProfiles=null;
    $this->_specificAffectedProfilesIncludingClosed=null;
    $this->_allProfiles=null;
    $this->_allAccessRights=null;
  }
  
  public static function resetAllVisibleProjects($idProject=null, $idUser=null) {
  	if (! getSessionUser()->id) return;
  	$user=getSessionUser();
    if ($idUser) {
      if ($idUser==$user->id) {
         self::resetAllVisibleProjects(null, null);
      } else {
    	  $audit=new Audit();
    	  $auditList=$audit->getSqlElementsFromCriteria(array("idUser"=>$idUser, 'idle'=>'0'));
    	  foreach ($auditList as $audit) {
    		  $audit->requestRefreshProject=1;
    		  $res=$audit->save();
    	  }
      }
    } else if ($idProject) {
      $aff=new Affectation();
      $affList=$aff->getSqlElementsFromCriteria(array('idProject'=>$idProject));
      foreach ($affList as $aff) {
        if ($aff->idUser==$user->id) {
          self::resetAllVisibleProjects(null, null);
        } else {
      	  $audit=new Audit();
	        $auditList=$audit->getSqlElementsFromCriteria(array("idUser"=>$aff->idUser, 'idle'=>'0'));
	        foreach ($auditList as $audit) {
	         $audit->requestRefreshProject=1;
	         $res=$audit->save();
	        }
        }
      }
    } else {
    	$user->resetVisibleProjects();
      setSessionUser($user);
      unset($_SESSION['visibleProjectsList']);
    }
  }

/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";
    if ($this->isResource and (! $this->resourceName or $this->resourceName=="")) {
      $result.='<br/>' . i18n('messageMandatory',array(i18n('colresourceName')));
    } 
    $crit=array("name"=>$this->name);
    $lst=$this->getSqlElementsFromCriteria($crit,false);
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
    // if uncheck isContact must check contact for deletion
    if ($old->isContact and ! $this->isContact and $this->id) {
        $obj=new Contact($this->id);
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
  
  public function deleteControl($nested=false)
  {
    $result="";
    
    if (! $nested) {
	    // if uncheck isResource must check resource for deletion
	    if ($this->isResource) {
	        $obj=new Resource($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('Resource').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;
	        }
	    }
	    // if uncheck isContact must check contact for deletion
	    if ($this->isContact) {
	        $obj=new Contact($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('Contact').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;
	        }
      }
    }
    if ($nested) {
      SqlElement::unsetRelationShip('User','Affectation');
    }
    $resultDelete=parent::deleteControl();
    if ($result and $resultDelete) {
      $resultDelete='<b><br/>'.i18n('User').' #'.htmlEncode($this->id).' :</b>'.$resultDelete.'<br/>';
    } 
    $result=$resultDelete.$result;
    return $result;
  }
  
  public function save() {
  	if (!$this->apiKey)  {
  		$this->apiKey=md5($this->id.date('Ymdhis'));
  	}
  	$old=$this->getOld();
  	if ($old->locked and ! $this->locked) {
  		$this->loginTry=0;
  	}
  	$paramDefaultPassword=Parameter::getGlobalParameter('paramDefaultPassword');
    if (! $this->id and Parameter::getGlobalParameter('initializePassword')=="YES") {
      $this->salt=hash('sha256',"projeqtor".date('YmdHis'));
      $this->password=hash('sha256',$paramDefaultPassword.$this->salt);
      $this->crypto='sha256';
    }
    $result=parent::save();
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
    Affectation::updateAffectations($this->id);
    return $result;
  }
  
  public function reset() {
    $this->_accessControlRights=null;
    $this->_accessControlVisibility=null;
    $this->_visibleProjects=null;
    $this->_visibleProjectsIncludingClosed=null;
    $this->_hierarchicalViewOfVisibleProjects=null;
  }
  
  
  /** =========================================================================
   * fonction for authentificate user with user/password
   * @param $Username $Password
   * can create user directly from Ldap
   * @return -1 or Id of authentified user
   */
	public function authenticate( $paramlogin, $parampassword) {
debugTraceLog("User->authenticate('$paramlogin', '$parampassword')" );	
	  $paramLdap_allow_login=Parameter::getGlobalParameter('paramLdap_allow_login');
	  $paramLdap_base_dn=Parameter::getGlobalParameter('paramLdap_base_dn');
	  $paramLdap_host=Parameter::getGlobalParameter('paramLdap_host');
	  $paramLdap_port=Parameter::getGlobalParameter('paramLdap_port');
	  $paramLdap_version=Parameter::getGlobalParameter('paramLdap_version');
	  $paramLdap_search_user=Parameter::getGlobalParameter('paramLdap_search_user');
	  $paramLdap_search_pass=Parameter::getGlobalParameter('paramLdap_search_pass');
	  $paramLdap_user_filter=Parameter::getGlobalParameter('paramLdap_user_filter');
	  $paramLdap_defaultprofile=Parameter::getGlobalParameter('paramLdap_defaultprofile');
	  $rememberMe=false;
	  if (isset($_REQUEST['rememberMe']) and Parameter::getGlobalParameter('rememberMe')!='NO') {
	  	$rememberMe=true;
	  }
	 	if ( ! $this->id ) {
			if (isset($paramLdap_allow_login) and strtolower($paramLdap_allow_login)=='true') {
		  	$this->name=strtolower($paramlogin);
		  	$this->isLdap = 1;
        debugTraceLog("User->authenticate : access through LDAP");		  	
			} else {
        debugTraceLog("User->authenticate : no user id (exit)");			  
				return "login";
		  }	
	 	}	
 	
	 	$lstPluginEvt=Plugin::getEventScripts('connect','User');
	 	foreach ($lstPluginEvt as $script) {
	 	  require $script; // execute code
	 	  if (isset($plgErrorLogin)) {
	 	    break;
	 	  }
	 	}
	 	if (isset($plgErrorLogin)) {
	 	  debugTraceLog("User->authenticate : some plugin error (exit)");	 	  
	 	  return $plgErrorLogin;
	 	}
		if ($this->isLdap == 0) {
			if ($this->crypto=='sha256') {
			  debugTraceLog("User->authenticate : sha256 encryption");
        $expected=$this->password.$_SESSION['sessionSalt'];
        $expected=hash("sha256", $expected);
      } else if ($this->crypto=='md5') {
        debugTraceLog("User->authenticate : md5 encryption");
				$expected=$this->password.$_SESSION['sessionSalt'];
				$expected=md5($expected);				
			} else if ($this->crypto=='old') {
			  debugTraceLog("User->authenticate : migration, no encryption");
        // Migrating to V4.0.0 : $parampassword is not MD5 unencrypted, but User->password is
        $expected=$this->password; // is MD5 encrypted
        $parampassword=md5(AesCtr::decrypt($parampassword, $_SESSION['sessionSalt'], Parameter::getGlobalParameter('aesKeyLength')));
      } else { // no crypto
        debugTraceLog("User->authenticate : no encryption");
				$expected=$this->password;
				$parampassword=AesCtr::decrypt($parampassword, $_SESSION['sessionSalt'], Parameter::getGlobalParameter('aesKeyLength'));
			}
			if ( $expected <> $parampassword) {
				$this->unsuccessfullLogin();
				debugTraceLog("User->authenticate : wrong password $expected!=$parampassword (exit)");
	      return "password";
			} else {
			  debugTraceLog("User->authenticate : Successfull login");
				$this->successfullLogin($rememberMe);
	  	  return "OK";
	  	}
	  } else {
	    debugTraceLog("User->authenticate : LDAP authenticate");
	  	disableCatchErrors();
	  	// Decode password
	  	$parampassword=AesCtr::decrypt($parampassword, $_SESSION['sessionSalt'], Parameter::getGlobalParameter('aesKeyLength'));
	  	// check password on LDAP
	    if (! function_exists('ldap_connect')) {
	    	errorLog('Ldap not installed on your PHP server. Check php_ldap extension or you should not set $paramLdap_allow_login to "true"');        
        return "ldap";
	    }
			try { 
	    	$ldapCnx=ldap_connect($paramLdap_host, $paramLdap_port);
			} catch (Exception $e) {
        traceLog("authenticate - LDAP connection error : " . $e->getMessage() );
        return "ldap";
	    }
	    if (! $ldapCnx) {
        traceLog("authenticate - LDAP connection error : not identified error");        
        return "ldap";
      }
			@ldap_set_option($ldapCnx, LDAP_OPT_PROTOCOL_VERSION, $paramLdap_version);
			@ldap_set_option($ldapCnx, LDAP_OPT_REFERRALS, 0);
	
			//$ldap_bind_dn = 'cn='.$this->ldap_search_user.','.$this->base_dn;
			$ldap_bind_dn = empty($paramLdap_search_user) ? null : $paramLdap_search_user;
			$ldap_bind_pw = empty($paramLdap_search_pass) ? null : $paramLdap_search_pass;
	
  		try {
		   $bind=ldap_bind($ldapCnx, $ldap_bind_dn, $ldap_bind_pw);
  		} catch (Exception $e) {
        traceLog("authenticate - LDAP Bind Error : " . $e->getMessage() );
        return "ldap";
      }  
			if (! $bind) {
        traceLog("authenticate - LDAP Bind Error : not identified error" );
			  return "ldap";
			}
			if (strpos($this->name,'*')!==false or strpos($this->name,'*')!==false 
			or strpos($this->name,'[')!==false or strpos($this->name,']')!==false
      or strpos($this->name,'\\')!==false) {
			  // Control : must not contain * or %
			  traceLog("authenticate - LDAP conection using for user '".$this->name."' : * or % or [ or ] or \ " );
			  return "login";
			}
			$filter_r = html_entity_decode(str_replace(array('%USERNAME%','%username%'), array($this->name,$this->name), $paramLdap_user_filter), ENT_COMPAT, 'UTF-8');
			$result = @ldap_search($ldapCnx, $paramLdap_base_dn, $filter_r);
			if (!$result) {
			  traceLog("authenticate - Filter error : ldap_search failed for filter $filter_r)" );			  
			  $this->unsuccessfullLogin();
				return "login";
			}
			$result_user = ldap_get_entries($ldapCnx, $result);
			if ($result_user['count'] == 0) {
			  traceLog("authenticate - Filter error : ldap_search returned no result for filter $filter_r)" );
			  $this->unsuccessfullLogin();
				return "login";
			}
		  if ($result_user['count'] > 1) {
		    traceLog("authenticate - Filter error : ldap_search returned more than one result for filter $filter_r)" );
		    $this->unsuccessfullLogin();
        return "login";
      }
			$first_user = $result_user[0];
			$ldap_user_dn = $first_user['dn'];
      if (strtolower($ldap_user_dn)==strtolower($paramLdap_search_user)) {
      	traceLog("authenticate - Filter error : filter retrieved admin user (LDAP user in global parameters)" );
      	$this->unsuccessfullLogin();
      	return "login";
      } 
			
			// Bind with the dn of the user that matched our filter (only one user should match filter ..)
      enableCatchErrors();
			try {
				$bind_user = @ldap_bind($ldapCnx, $ldap_user_dn, $parampassword);
			} catch (Exception $e) {
        traceLog("authenticate - LdapBind Error : " . $e->getMessage() );
        $this->unsuccessfullLogin();
        return "login";
      }
			if (! $bind_user or !$parampassword) {
			  $this->unsuccessfullLogin();
				return "login";
			}
			disableCatchErrors();
			if (! $this->id and $this->isLdap) {
				if (!count($first_user) == 0) {
					Sql::beginTransaction();
					// Contact information based on the inetOrgPerson class schema
					if (isset( $first_user['mail'][0] )) {
				  		$this->email=$first_user['mail'][0];						
					}
					if (isset( $first_user['cn'][0] )) {
						$this->resourceName=$first_user['cn'][0];    
					} 
				  $this->isLdap=1;
				  $this->name=strtolower($paramlogin);
				  $this->idProfile=Parameter::getGlobalParameter('ldapDefaultProfile');
				  $createAction=Parameter::getGlobalParameter('ldapCreationAction');
				  if ($createAction=='createResource' or $createAction=='createResourceAndContact') {
				    $this->isResource=1;
				  }
				  if ($createAction=='createContact' or $createAction=='createResourceAndContact') {
				    $this->isContact=1;
				  }
  				if (! $this->resourceName and ($this->isResource or $this->isContact)) {
  				  $this->resourceName=$this->name;
				  }
				  setSessionUser($this);
				  $resultSaveUser=$this->save();
					$sendAlert=Parameter::getGlobalParameter('ldapMsgOnUserCreation');
					if ($sendAlert!='NO') {
						$title="ProjeQtOr - " . i18n('newUser');
						$message=i18n("newUserMessage",array($paramlogin));
						if ($sendAlert=='MAIL' or $sendAlert=='ALERT&MAIL') {
							$paramAdminMail=Parameter::getGlobalParameter('paramAdminMail');
						  sendMail($paramAdminMail, $title, $message);
						}
						if ($sendAlert=='ALERT' or $sendAlert=='ALERT&MAIL') {
							$prof=new Profile();
							$crit=array('profileCode'=>'ADM');
							$lstProf=$prof->getSqlElementsFromCriteria($crit,false);
							foreach ($lstProf as $prof) {
								$crit=array('idProfile'=>$prof->id);
								$lstUsr=$this->getSqlElementsFromCriteria($crit,false);
								foreach($lstUsr as $usr) {
									$alert=new Alert();
									$alert->idUser=$usr->id;
									$alert->alertType='INFO';
									$alert->alertInitialDateTime=date('Y-m-d H:i:s');
									$alert->message=$message;
									$alert->title=$title;
									$alert->alertDateTime=date('Y-m-d H:i:s');
									$alert->save();
								}
							}
						}
					}
					if (stripos($resultSaveUser,'id="lastOperationStatus" value="OK"')>0 ) {
            Sql::commitTransaction();
					} else {
						Sql::rollbackTransaction();
					}									
				}					
			}
	  }
	  $this->successfullLogin($rememberMe);
	  setSessionUser($this);
	  return "OK";     
  }

  private function unsuccessfullLogin() {
  	global $loginSave;
  	$maxTry=Parameter::getGlobalParameter('paramLockAfterWrongTries');
  	if ($maxTry) {
  		$this->loginTry+=1;
  		if ($this->loginTry>=$maxTry) {
  			$this->locked=1;
  			traceLog("user '$this->name' locked - too many tries");
  		}
  		$loginSave=true;
  		$this->save();
  	}
  }
  
  private function successfullLogin($rememberMe) {
  	global $loginSave;
    $maxTry=Parameter::getGlobalParameter('paramLockAfterWrongTries');
  	if ($maxTry) {
      $this->loginTry=0;
      $loginSave=true;
      if ($rememberMe) {
      	$this->setCookieHash();
      }
      $this->save();
  	} else if ($rememberMe) {
  		$loginSave=true;
      $this->setCookieHash();
      $this->save();
  	}
  }
  
  /** ========================================================================
   * Valid login
   * @param $user the user object containing login information
   * @return void
   */
  public function finalizeSuccessfullConnection($rememberMe) {
    setSessionUser($this);
    $_SESSION['appRoot']=getAppRoot();
    $crit=array();
    $crit['idUser']=$this->id;
    $crit['idProject']=null;
    $obj=new Parameter();
    $objList=$obj->getSqlElementsFromCriteria($crit,false);
    //$this->_arrayFilters[$filterObjectClass . "FilterName"]=$filter->name;
    foreach($objList as $obj) {
      if ($obj->parameterCode=='lang' and $obj->parameterValue) {
        $_SESSION['currentLocale']=$obj->parameterValue;
        $i18nMessages=null;
      } else if ($obj->parameterCode=='defaultProject') {
        $prj=new Project($obj->parameterValue);
        if ($prj->name!=null and $prj->name!='') {
          $_SESSION['project']=$obj->parameterValue;
        } else {
          $_SESSION['project']='*';
        }
      } else if (substr($obj->parameterCode,0,6)=='Filter') {
        if (! $this->_arrayFilters) {
          $this->_arrayFilters=array();
        }
        $idFilter=$obj->parameterValue;
        $filterObjectClass=substr($obj->parameterCode,6);
        $filterArray=array();
        $filter=new Filter($idFilter);
        $arrayDisp=array();
        $arraySql=array();
        if (is_array($filter->_FilterCriteriaArray)) {
          foreach ($filter->_FilterCriteriaArray as $filterCriteria) {
            $arrayDisp["attribute"]=$filterCriteria->dispAttribute;
            $arrayDisp["operator"]=$filterCriteria->dispOperator;
            $arrayDisp["value"]=$filterCriteria->dispValue;
            $arraySql["attribute"]=$filterCriteria->sqlAttribute;
            $arraySql["operator"]=$filterCriteria->sqlOperator;
            $arraySql["value"]=$filterCriteria->sqlValue;
            $filterArray[]=array("disp"=>$arrayDisp,"sql"=>$arraySql);
          }
        }
        $this->_arrayFilters[$filterObjectClass]=$filterArray;
        $this->_arrayFilters[$filterObjectClass . "FilterName"]=$filter->name;
      } else {
        $_SESSION[$obj->parameterCode]=$obj->parameterValue;
      }
    }
    traceLog("NEW CONNECTED USER '" . $this->name . "'".(($rememberMe)?' (using remember me feature)':''));
    Audit::updateAudit();
  }
  
  
  public function disconnect() {
    purgeFiles(Parameter::getGlobalParameter('paramReportTempDirectory'),"user" . $this->id . "_");
    $this->stopAllWork();
    traceLog("DISCONNECTED USER '" . $this->name . "'");
    Parameter::clearGlobalParameters();
    setSessionUser(null);
  }

  public function stopAllWork() {
    $we=new WorkElement();
    $weList=$we->getSqlElementsFromCriteria(array('idUser'=>$this->id, 'ongoing'=>'1'));
    foreach ($weList as $we) {
      $we->stop();
    }
  }
  
  public static function setOldUserStyle() {
    self::$_databaseTableName = 'user';
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
  
  public function setCookieHash() {
  	$cookieHash = md5(sha1($this->name . microtime().rand(10000000,99999999))); // not secure - at least use an unknown value such as password...
	  /* to be checked later on : openssl_random_pseudo_bytes is compatible with PHP >= 5.3
       Compatibility with PHP 5.2 must be preserved
    $cookieHash = openssl_random_pseudo_bytes(32, $crypto_strong); // but this is better...
	  if (!$crypto_strong){
		  errorLog("DEBUG: openssl_random_pseudo_bytes() uses not cryptographiclly secure algorithm for login cookie");
	  }*/
  	$this->cookieHash=$cookieHash;
  	$domain=$_SERVER['SERVER_NAME'];
  	if ($domain=='localhost') {$domain="";}
  	$result=setcookie("projeqtor",$cookieHash,time()+3600*24*7,'/',$domain);
  }
  public function cleanCookieHash() {
  	$cookieHash=$this->cookieHash;
  	setcookie('projeqtor', $cookieHash, 1);
  	$this->cookieHash=null;
  	$this->save();
  }
  public static function getRememberMeCookie() {
  	$cookieHash=null;
  	if (isset($_COOKIE['projeqtor']) and Parameter::getGlobalParameter('rememberMe')!='NO') {
  		$cookieHash = $_COOKIE['projeqtor'];
  	}
  	return $cookieHash;
  }
  
  public function getWorkVisibility($idProject,$col) {
    return $this->getVisibility($idProject,$col,'work');
  }
  public function getCostVisibility($idProject,$col) {
    return $this->getVisibility($idProject,$col,'cost');
  }
  public function getVisibility($idProject,$col,$type) {
    $profile=$this->getProfile($idProject);
    if ($type=='cost') {
      $visibility=PlanningElement::getCostVisibiliy($profile);
    } else {
      $visibility=PlanningElement::getWorkVisibiliy($profile);
    }
    if ($visibility=='ALL') {
      return true;
    } else if ($visibility=='NO') {
      return false;
    } else if ($visibility=='VAL') {
      if (strpos(strtolower($col),'validated')!==false) {
        return true;
      } else {
        return false;
      }
    }
  }
  
  private $_allSpecificRightsForProfiles=array();
  
  public function getAllSpecificRightsForProfiles($specific) {
    SqlElement::$_cachedQuery['AccessScope']=array();
    SqlElement::$_cachedQuery['ListYesNo']=array();
    if (isset($this->_allSpecificRightsForProfiles[$specific])) {
      return $this->_allSpecificRightsForProfiles[$specific];
    }
    $result=array();
    foreach ($this->getAllProfiles() as $prof) {
      $crit=array('scope'=>$specific, 'idProfile'=>$prof);
      $habilitation=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
      if ($specific=='planning') {
        $scope=new ListYesNo($habilitation->rightAccess);
        $code=$scope->code;
      } else {
        $scope=new AccessScope($habilitation->rightAccess);
        $code=$scope->accessCode;
      }
      if (!isset($result[$code])) $result[$code]=array();
      $result[$code][$prof]=$prof;
    }
    $this->_allSpecificRightsForProfiles[$specific]=$result;
    if ($this->id==getSessionUser()->id) {
      setSessionUser($this); // Store user to cache Data
    }
    return $result;
  }
  
  public function allSpecificRightsForProfilesOneOnlyValue($specific,$value) {
    $list=$this->getAllSpecificRightsForProfiles($specific);
    foreach ($list as $val=>$lstProf) {
      if ($val!=$value) return false;
    }
    return true;
  }
  public function allSpecificRightsForProfilesContainsValue($specific,$value) {
    $list=$this->getAllSpecificRightsForProfiles($specific);
    foreach ($list as $val=>$lstProf) {
      if ($val==$value) return true;
    }
    return false;
  }
  
  public function getListOfPlannableProjects() {
    $rightsList=$this->getAllSpecificRightsForProfiles('planning'); // Get planning rights for all user profiles
    $affProjects=$this->getSpecificAffectedProfiles();              // Affected projects, with profile
    $result=array();
    $defProfile=$this->idProfile;
    $access="NO";
    $accessList=$this->getAccessControlRights();                    // Get acces rights
    $canPlan=false;
    $right=SqlElement::getSingleSqlElementFromCriteria('habilitationOther', array('idProfile'=>$defProfile, 'scope'=>'planning'));
    if ($right) {
      $list=new ListYesNo($right->rightAccess);
      if ($list->code=='YES') {
        $canPlan=true;
      }
    }
    if (isset($accessList['menuProject'])) {                        // Retrieve acces rights for projects
      $access=$accessList['menuProject']['update'];                 // Retrieve update acces right for projects
    }
    if ($access=='ALL' and $canPlan) {        // Update rights for project = "ALL" (admin type) and Can Plan for defaut project
      // List of plannable project is list of all project minus list of affected with no plan right
      $result=$this->getVisibleProjects();
      foreach ($affProjects as $prj=>$prf) {
        if (isset($rightsList['NO'][$prf])) {
          unset($result[$prj]);
        }
      }
    } else {
      // List of plannable project is list of all project minus list of affected with no plan right
      if (! isset ($rightsList['YES'])) return $result; // Return empty array
      foreach ($affProjects as $prj=>$prf) {
        if (isset($rightsList['YES'][$prf])) {
          $result[$prj]=$prj;
        }
      }
    }
    return $result;
  }
  
}