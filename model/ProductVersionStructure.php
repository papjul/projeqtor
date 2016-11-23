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
class ProductVersionStructure extends SqlElement {

  // extends SqlElement, so has $id
  public $id;    // redefine $id to specify its visible place 
  public $idProductVersion;
  public $idComponentVersion;
  public $comment;
  public $creationDate;
  public $idUser;
  public $idle;
  
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
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /**
   * Save object (permuts objects ref if needed)
   * @see persistence/SqlElement#save()
   */
  public function save() {
    $old=$this->getOld();
    $result=parent::save();
    if ($old->idProductVersion!=$this->idProductVersion or $old->idComponentVersion!=$this->idComponentVersion) {
      if ($this->idComponentVersion) {
        $vers=new ComponentVersion($this->idComponentVersion);
        $comp=new Component($vers->idComponent);
        $comp->updateAllVersionProject();
        $list=$comp->getComposition(false,true);
        foreach ($list as $cptId) {
          $comp=new Component($cptId);
          $comp->updateAllVersionProject();
        }
      }
      if ($old->idComponentVersion and $old->idComponentVersion!=$this->idComponentVersion) {
        $vers=new ComponentVersion($old->idComponentVersion);
        $comp=new Component($vers->idComponent);
        $comp->updateAllVersionProject();
        $list=$comp->getComposition(false,true);
        foreach ($list as $cptId) {
          $comp=new Component($cptId);
          $comp->updateAllVersionProject();
        }
      }
      if ($this->idProductVersion) {
        $vers=new ComponentVersion($this->idProductVersion);
        if ($vers->id) {
          $comp=new Component($vers->idComponent); // V5.3.0 : idProduct can refer to Component
          if ($comp->id) {
            $comp->updateAllVersionProject();
            $list=$comp->getComposition(false,true);
            foreach ($list as $cptId) {
              $comp=new Component($cptId);
              $comp->updateAllVersionProject();
            }
          }
        }
      }
      if ($old->idProductVersion and $old->idProductVersion!=$this->idProductVersion) {
        $vers=new ComponentVersion($old->idProductVersion);
        if ($vers->id) {
          $comp=new Component($vers->idComponent); // V5.3.0 : idProduct can refer to Component
          if ($comp->id) {
            $comp->updateAllVersionProject();
            $list=$comp->getComposition(false,true);
            foreach ($list as $cptId) {
              $comp=new Component($cptId);
              $comp->updateAllVersionProject();
            }
          }
        }
      }
    }
    return $result;
  }
  
  public function delete() {	
  	$result=parent::delete();    
  	if ($this->idComponentVersion) {
  	  $vers=new ComponentVersion($this->idComponentVersion);
  	  $comp=new Component($vers->idComponent);
  	  $comp->updateAllVersionProject();
  	  $list=$comp->getComposition(false,true);
  	  foreach ($list as $cptId) {
  	    $comp=new Component($cptId);
  	    $comp->updateAllVersionProject();
  	  }
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
    $checkCrit=array('idProductVersion'=>$this->idProductVersion,
                     'idComponentVersion'=>$this->idComponentVersion);
    $comp=new ProductVersionStructure();
    $check=$comp->getSqlElementsFromCriteria($checkCrit);
    if (count($check)>0) {
      $result.='<br/>' . i18n('errorDuplicateLink');
    } 
    
    // Infinite loops
    if ($this->idProductVersion==$this->idComponentVersion) {
      $result='<br/>' . i18n('errorHierarchicLoop');
    }
    $productVersionStructure=self::getStructure($this->idProductVersion);
    foreach ($productVersionStructure as $prd=>$prdId) {
      if ($prdId==$this->idComponentVersion) {
        $result='<br/>' . i18n('errorHierarchicLoop');
        break;
      }
    }
    $componentVersionComposition=self::getComposition($this->idComponentVersion);
    foreach ($componentVersionComposition as $comp=>$compId) {
      if ($compId==$this->idProductVersion) {
        $result='<br/>' . i18n('errorHierarchicLoop');
        break;
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
  
  public static function getComposition($id,$level='all') {
    $result=array();
    $crit=array('idProductVersion'=>$id);
    $ps=new ProductVersionStructure();
    $psList=$ps->getSqlElementsFromCriteria($crit);
    if (is_numeric($level)) $level--;
    foreach ($psList as $ps) {
      $result['#'.$ps->idComponentVersion]=$ps->idComponentVersion;
      if ($level=='all' or $level>0) {
        $result=array_merge($result,self::getComposition($ps->idComponentVersion));
      }
    }
    return $result;
  }
  public static function getStructure($id, $level='all') {
    $result=array();
    $crit=array('idComponentVersion'=>$id);
    $ps=new ProductVersionStructure();
    $psList=$ps->getSqlElementsFromCriteria($crit);
    if (is_numeric($level)) $level--;
    foreach ($psList as $ps) {
      $result['#'.$ps->idProductVersion]=$ps->idProductVersion;
      if ($level=='all' or $level>0) {
        $result=array_merge($result,self::getStructure($ps->idProductVersion));
      }
    }
    return $result;
  }
}
?>