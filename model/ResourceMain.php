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
class ResourceMain extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;
  public $_spe_image;
  public $name;
  public $userName;
  public $initials;
  public $email;
  public $idProfile;
  public $capacity;
  public $idCalendarDefinition;
  public $idOrganization;
  public $idTeam;
  public $phone;
  public $mobile;
  public $fax;
  public $isContact;
  public $isUser;
  public $idle;
  public $description;
  public $_sec_ResourceCost;
  public $idRole;
  public $_ResourceCost=array();
  public $_sec_Affectations;
  public $_spe_affectations;
  public $_spe_affectationGraph;
  public $_sec_Miscellaneous;
  public $dontReceiveTeamMails;
  public $password;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="5%"># ${id}</th>
    <th field="name" width="20%">${realName}</th>
    <th field="photo" formatter="thumb32" width="5%">${photo}</th>
    <th field="initials" width="10%">${initials}</th>  
    <th field="nameTeam" width="15%">${team}</th>
    <th field="capacity" width="10%" >${capacity}</th>
    <th field="userName" width="20%">${userName}</th> 
    <th field="isUser" width="5%" formatter="booleanFormatter">${isUser}</th>
    <th field="isContact" width="5%" formatter="booleanFormatter">${isContact}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';

  private static $_fieldsAttributes=array("name"=>"required, truncatedWidth100",
                                          "userName"=>"truncatedWidth100",
                                          "email"=>"truncatedWidth100",
                                          "idProfile"=>"readonly",
                                          "isUser"=>"readonly",
                                          "isContact"=>"readonly",
                                          "password"=>"hidden" ,
                                          "idRole"=>"required",
                                          "idCalendarDefinition"=>"required"
  );    
  
  private static $_databaseTableName = 'resource';

  private static $_databaseColumnName = array('name'=>'fullName',
                                              'userName'=>'name');

  private static $_databaseCriteria = array('isResource'=>'1');
  
  private static $_colCaptionTransposition = array('idRole'=>'mainRole', 'name'=>'realName'
  );
  
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
    
    $crit=array("name"=>"menuContact");
    $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', $crit);
    if (! $menu) {
      return;
    }     
    if (securityCheckDisplayMenu($menu->id)) {
      self::$_fieldsAttributes["isContact"]="";
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
      $colScript .= '    dijit.byId("userName").set("value", "");';
      $colScript .= '    dojo.removeClass(dijit.byId("userName").domNode,"required");';
      $colScript .= '    dijit.byId("idProfile").set("required", null);';
      $colScript .= '    dojo.removeClass(dijit.byId("idProfile").domNode,"required");';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    return $colScript;

  } 

  public function getWork($startDate, $withProjectRepartition=false) {
    $result=array();
    $real=array();
    $startDay=str_replace('-','',$startDate);
    $where="day >= '" . $startDay . "'";
    $where.=" and idResource='" . Sql::fmtId($this->id) . "'"; 
    $pw=new PlannedWork();
    $pwList=$pw->getSqlElementsFromCriteria(null,false,$where);
    $listTopProjectsArray=array();
    foreach ($pwList as $work) {
      $date=$work->workDate;
      if (array_key_exists($date,$result)) {
        $val=$result[$date];
      } else {
        $val=0;
      }
      $val+=$work->work;
      $result[$date]=$val;
      if ($withProjectRepartition) {
        $projectKey='Project#'. $work->idProject;
        if (array_key_exists($projectKey,$listTopProjectsArray)) {
          $listTopProjects=$listTopProjectsArray[$projectKey];
        } else {
          $proj = new Project($work->idProject);
          $listTopProjects=$proj->getTopProjectList(true);
          $listTopProjectsArray[$projectKey]=$listTopProjects;
        }
      // store Data on a project level view
        foreach ($listTopProjects as $idProject) {
          $projectKey='Project#'. $idProject;
          $week=weekFormat($date);
          if (array_key_exists($projectKey,$result)) {
            if (array_key_exists($week,$result[$projectKey])) {
              $valProj=$result[$projectKey][$week];
            } else {
              $valProj=0;
            }
          } else {
            $result[$projectKey]=array();
            $result[$projectKey]['rate']=$this->getAffectationRate($idProject);
            $valProj=0;
          }
          $valProj+=$work->work; 
          $result[$projectKey][$week]=$valProj;
        }
      }
    }
    $w=new Work();
    $wList=$w->getSqlElementsFromCriteria(null,false,$where);
    foreach ($wList as $work) {
      $date=$work->workDate;
      if (array_key_exists($date,$result)) {
        $val=$result[$date];
      } else {
        $val=0;
      }
      $val+=$work->work;
      $result[$date]=$val;
// ProjectRepartition - start
      if ($withProjectRepartition) {
        $projectKey='Project#'. $work->idProject;
        if (array_key_exists($projectKey,$listTopProjectsArray)) {
          $listTopProjects=$listTopProjectsArray[$projectKey];
        } else {
          $proj = new Project($work->idProject);
          $listTopProjects=$proj->getTopProjectList(true);
          $listTopProjectsArray[$projectKey]=$listTopProjects;
        }
        // store Data on a project level view
        foreach ($listTopProjects as $idProject) {
          $projectKey='Project#' . $idProject;
          $week=weekFormat($date);
          if (array_key_exists($projectKey,$result)) {
            if (array_key_exists($week,$result[$projectKey])) {
              $valProj=$result[$projectKey][$week];
            } else {
              $valProj=0;
            }
          } else {
            $result[$projectKey]=array();
            $result[$projectKey]['rate']=$this->getAffectationRate($idProject);
            $valProj=0;
          }
          $valProj+=$work->work; 
          $result[$projectKey][$week]=$valProj;
        }
      } // ProjectRepartition - end
      $key=$work->refType.'#'.$work->refId;
      if (! isset($real[$key])) {
        $real[$key]=array();
      }
      $real[$key][$date]=$work->work;
    }
    $result['real']=$real;
    return $result;
  }
  
  private static $affectationRates=array();
  public function getAffectationRate($idProject) {
  	if (isset(self::$affectationRates[$this->id.'#'.$idProject])) {
  		return self::$affectationRates[$this->id.'#'.$idProject];
  	}
    $result="";
    /*$crit=array('idResource'=>$this->id, 'idProject'=>$idProject);
    $aff=SqlElement::getSingleSqlElementFromCriteria('Affectation',$crit);
    if ($aff->rate) {
      $result=$aff->rate;
    } else {
      $prj=new Project($idProject);
      if ($prj->idProject) {
        $result=$this->getAffectationRate($prj->idProject);
      } else {
        $result='100';
      }
    }*/
    $periods=Affectation::buildResourcePeriodsPerProject($this->id);
    if (isset($periods[$idProject])) {
    	$result=$periods[$idProject]['periods'];
    } else {
		  $result=array(array('start'=>Affectation::$minAffectationDate, 'end'=>Affectation::$maxAffectationDate, 'rate'=>100));
    }
    self::$affectationRates[$this->id.'#'.$idProject]=$result;
    return $result;
  }
  // Find a rate amongst list of project affectation periods
  public static function findAffectationRate($arrayPeriods,$date) {
  	foreach ($arrayPeriods as $period) {
  		if ($period['start']<=$date and $date<=$period['end']) {
  			return $period['rate']; 
  		} else if ($date<$period['start']) {
  			return 0;
  		}
  	}
  	return -1; // not found => -1;
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
    // if uncheck isUser must check user for deletion
    if ($old->isUser and ! $this->isUser and $this->id) {
        $obj=new User($this->id);
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
    self::$_fieldsAttributes["idProfile"]="";
    self::$_fieldsAttributes["userName"]="";
    
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
  
  public function getResourceCost() {
    $result=array();
    $rc=new ResourceCost();
    $crit=array('idResource'=>$this->id);
    $rcList=$rc->getSqlElementsFromCriteria($crit, false, null, 'idRole, startDate');
    return $rcList;
  }
  public function getActualResourceCost($idRole=null) {
    if (! $this->id) return null;
    if (! $idRole) $idRole=$this->idRole;
    $where="idResource='" . Sql::fmtId($this->id) . "'";
    if ($idRole) {
      $where.= " and idRole='" . Sql::fmtId($idRole) . "'";
    }
    $where.= " and endDate is null";
    $rc=new ResourceCost();
    $rcL = $rc->getSqlElementsFromCriteria(null, false, $where, "startDate desc");
    if (count($rcL)>=1) {
      return $rcL[0]->cost;
    }
    return null;
  }  

  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
  	global $comboDetail, $print, $outMode, $largeWidth;
    $result="";
    if ($item=='affectations') {
      $aff=new Affectation();
      $critArray=array('idResource'=>(($this->id)?$this->id:'0'));
      $affList=$aff->getSqlElementsFromCriteria($critArray, false);
      drawAffectationsFromObject($affList, $this, 'Project', false);   
      return $result;
    } else if ($item=='affectationGraph') {
    	//$result.='<tr style="height:100%">';
    	//$result.='<td colspan="2" style="width:100%">';
    	$result.=Affectation::drawResourceAffectation($this->id);
    	//$result.='</td></tr>';
    	echo $result;
    } else if ($item=='image' and $this->id){
    	$result=Affectable::drawSpecificImage(get_class($this),$this->id, $print, $outMode, $largeWidth);
    	echo $result;
    }
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
	      $result="<br/>" . i18n("msgCannotDeleteResource");
	      return $result;
	    }     	    	
    }
    if (! $nested) {
	    // if uncheck isContact must check contact for deletion
	    if ($this->isContact) {
	        $obj=new Contact($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('Contact').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;
	        }
	    }
	  // if uncheck isUser must check user for deletion
	    if ($this->isUser) {
	        $obj=new User($this->id);
	        $resultDelete=$obj->deleteControl(true);
	        if ($resultDelete and $resultDelete!='OK') {
	          $result.='<b><br/>'.i18n('User').' #'.htmlEncode($this->id).' :</b>'.$resultDelete;;
	        }
	    }
    }
    if ($nested) {
    	SqlElement::unsetRelationShip('Resource','Affectation');
    }
    $resultDelete=parent::deleteControl();
    if ($result and $resultDelete) {
      $resultDelete='<b><br/>'.i18n('Resource').' #'.htmlEncode($this->id).' :</b>'.$resultDelete.'<br/>';
    } 
    $result=$resultDelete.$result;
    return $result;
  }
  
  public function drawMemberList($team) {
    $result="<table>";
    $crit=array('idTeam'=>$team);
    $resList=$this->getSqlElementsFromCriteria($crit, false);
    foreach ($resList as $res) {
      $result.= '<tr><td valign="middle" width="20px"><img src="css/images/iconList16.png" height="16px" /></td><td>';
      $result.=''.$res->getPhotoThumb(32).'&nbsp;</td><td>';
      $result.=htmlDrawLink($res);
      $result.='</td></tr>';
    }
    $result .="</table>";
    return $result; 
  }
  
  public function getPhotoThumb($size) {
  	$result="";
  	$radius=round($size/2,0);
  	$image=SqlElement::getSingleSqlElementFromCriteria('Attachment', array('refType'=>'Resource', 'refId'=>$this->id));
    if ($image->id and $image->isThumbable()) {
  	  $result.='<img src="'. getImageThumb($image->getFullPathFileName(),$size).'" '
             . ' style="cursor:pointer;border-radius:'.$radius.'px;height:'.$size.'px;width:'.$size.'px"'
             . ' onClick="showImage(\'Attachment\',\''.htmlEncode($image->id).'\',\''.htmlEncode($image->fileName,'protectQuotes').'\');" />';
    } else {
    	//$result='<div style="width:'.$size.';height:'.$size.';border:1px solide grey;">&nbsp;</span>';
      $result.='<img src="../view/img/Affectable/thumb'.$size.'.png" '
             . ' style="border-radius:'.$radius.'px;height:'.$size.'px;width:'.$size.'px"'
             . '  />';
    }
    return $result;
  }
  
}
?>