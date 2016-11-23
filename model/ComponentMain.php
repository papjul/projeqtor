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
 * Component splits pruduct into elementary objects. A component car participate to several Components
 * Almost all other objects are linked to a given project.
 */ 
require_once('_securityCheck.php');
class ComponentMain extends ProductOrComponent {

  // List of fields that will be exposed in general user interface
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $scope;
  public $name;
  public $idComponentType;
  public $designation;
  public $idResource;
  public $idComponent;
  public $creationDate;
  public $idUser;
  public $idle;
  public $description;
  public $_sec_ComponentVersions;
  public $_spe_versions;
  public $_sec_ComponentStructure;
  public $_componentStructure=array();
  public $_sec_ComponentComposition;
  public $_componentComposition=array(); 
  public $_spe_tenders;
  public $_Attachment=array();
  public $_Note=array();
  public $_nbColMax=3;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%" ># ${id}</th>
    <th field="name" width="30%" >${componentName}</th>
    <th field="designation" width="25%" >${identifier}</th>  
    <th field="nameComponent" width="30%" >${isSubComponentOf}</th>
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';

   private static $_fieldsAttributes=array("name"=>"required",
      "scope"=>"hidden", 
       "idClient"=>"hidden", 
       "idContact"=>"hidden", 
       "idProduct"=>"hidden", 
       "idComponent"=>"hidden"
  );   

  private static $_colCaptionTransposition = array('idContact'=>'contractor',
      'idComponent'=>'isSubComponentOf',
      "designation"=>"identifier",
      'idResource'=>'responsible'
  );
  private static $_databaseColumnName = array('idComponent'=>'idProduct');
  
  private static $_databaseTableName = 'product';
  private static $_databaseCriteria = array('scope'=>'Component');
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
    if ($item=='versions' or $item=='versionsWithProjects') {
      $result .="<table><tr>";
      //echo "<td class='label' valign='top'><label>" . i18n('versions') . "&nbsp;:&nbsp;</label></td>";
      $result .="<td>";
      if ($this->id) {
        $vers=new ComponentVersion();
        $crit=array('idComponent'=>$this->id);
      	$result .= $vers->drawVersionsList($crit,($item=='versionsWithProjects')?true:false);
      }
      $result .="</td></tr></table>";
      return $result;
    } else {
      if ($item=='tenders') {
        Tender::drawListFromCriteria('id'.get_class($this),$this->id);
      }
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
    if ($this->id and $this->id==$this->idComponent) {
      $result.='<br/>' . i18n('errorHierarchicLoop');
    } else if ($this->idComponent){
    	$parent=new Component($this->idComponent);
    	while ($parent->id) {
    	  if ($parent->id==$this->id) {
          $result.='<br/>' . i18n('errorHierarchicLoop');
          break;
        }
        $parent=new Component($parent->idComponent);
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
  
  public function getSubComponents($limitToActiveComponents=false) {
    if ($this->id==null or $this->id=='') {
      return array();
    }
    $crit=array();
  	$crit['idComponent']=$this->id;
    if ($limitToActiveComponents) {$crit['idle']='0';}
    $sorted=SqlList::getListWithCrit('Component',$crit,'name');
  	$subComponents=array();
    foreach($sorted as $prodId=>$prodName) {
      $subComponents[$prodId]=new Component($prodId);
    }
    return $subComponents;
  }
  public function getSubComponentsList($limitToActiveComponents=false) {
    if ($this->id==null or $this->id=='') {
      return array();
    }
    $crit=array();
    $crit['idComponent']=$this->id;
    if ($limitToActiveComponents) {$crit['idle']='0';}
    $sorted=SqlList::getListWithCrit('Component',$crit,'name');
    return $sorted;
  }
  
  /** ==========================================================================
   * Recusively retrieves all the hierarchic sub-Components of the current Component
   * @return an array containing id, name, subComponents (recursive array)
   */
  public function getRecursiveSubComponents($limitToActiveComponents=false) {
    $crit=array('idComponent'=>$this->id);
    if ($limitToActiveComponents) {
      $crit['idle']='0';
    }
    $obj=new Component();
    $subComponents=$obj->getSqlElementsFromCriteria($crit, false) ;
    $subComponentList=null;
    foreach ($subComponents as $subProd) {
      $recursiveList=null;
      $recursiveList=$subProd->getRecursiveSubComponents($limitToActiveComponents);
      $arrayProd=array('id'=>$subProd->id, 'name'=>$subProd->name, 'subItems'=>$recursiveList);
      $subComponentList[]=$arrayProd;
    }
    return $subComponentList;
  }
  
  /** ==========================================================================
   * Recusively retrieves all the sub-Components of the current Component
   * and presents it as a flat array list of id=>name
   * @return an array containing the list of subComponents as id=>name 
   */
  public function getRecursiveSubComponentsFlatList($limitToActiveComponents=false, $includeSelf=false) {
  	$tab=$this->getSubComponentsList($limitToActiveComponents);
    $list=array();
    if ($includeSelf) {
      $list[$this->id]=$this->name;
    }
    if ($tab) {
      foreach($tab as $id=>$name) {
        $list[$id]=$name;
        $subobj=new Component();
        $subobj->id=$id;
        $sublist=$subobj->getRecursiveSubComponentsFlatList($limitToActiveComponents);
        if ($sublist) {
          $list=array_merge_preserve_keys($list,$sublist);
        }
      }
    }
    return $list;
  }
  
  public function updateAllVersionProject() {
    $vers=new ComponentVersion();
    $versList=$vers->getSqlElementsFromCriteria(array('idComponent'=>$this->id));
    foreach ($versList as $vers) {
      $existing=$vers->getLinkedProjects(false); // List of projects linked
      $target=array(); // List of project that should be linked
      $productVersions=$vers->getLinkedProductVersions(false);
      foreach ($productVersions as $pvId) {
        $pv=new ProductVersion($pvId);
        $arr=$pv->getLinkedProjects(false);
        $target=array_merge_preserve_keys($target,$arr);
      }
      foreach ($existing as $projId) {
        if (! in_array($projId,$target)) { // Existing not in target => delete VersionProject for all versions
          $vp=SqlElement::getSingleSqlElementFromCriteria('VersionProject', array('idProject'=>$projId,'idVersion'=>$vers->id));
          if ($vp->id) {
            $res=$vp->delete();
          }
        }
      }
      foreach ($target as $projId) {
        $vp=SqlElement::getSingleSqlElementFromCriteria('VersionProject', array('idProject'=>$projId,'idVersion'=>$vers->id));
        if (! $vp->id) {
          $res=$vp->save();
        }
      }
    }
  }
  
  public function getComposition($withName=true,$reculsively=false) {
    $ps=new ProductStructure();
    $psList=$ps->getSqlElementsFromCriteria(array('idProduct'=>$this->id));
    $result=array();
    foreach ($psList as $ps) {
      $result[$ps->idComponent]=($withName)?SqlList::getNameFromId('Component', $ps->idComponent):$ps->idComponent;
      if ($reculsively) {
        $comp=new Component($ps->idComponent);
        $result=array_merge_preserve_keys($comp->getComposition($withName,true),$result);
      }
    }
    return $result;
  }
  
  public static function canViewComponentList($obj=null) {
    //return securityGetAccessRightYesNo('menuComponent', 'read', null, null);
    $user=getSessionUser();
    $habil=SqlElement::getSingleSqlElementFromCriteria('habilitationOther', array('idProfile' => $user->getProfile($obj),'scope' => 'viewComponents'));
    if ($habil) {
      $list=new ListYesNo($habil->rightAccess);
      return $list->code;
    }
    return 'NO';
  }

}
?>