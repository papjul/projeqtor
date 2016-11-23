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
class ProductProject extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $idProduct;
  public $idProject;
  public $startDate;
  public $endDate;
  public $idle;

  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%" ># ${id}</th>
    <th field="nameProduct" width="40%" >${idProduct}</th>
    <th field="nameProject" width="40%" >${idProject}</th>
    <th field="idle" width="10%" formatter="booleanFormatter" >${idle}</th>
    '; 
  
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
  
  public function save() {
    $new=($this->id)?false:true;
    $old=$this->getOld();
    $result=parent::save();

    // Create or update VersionProject for each Version of the Product 
    $vers=new Version();
    $versList=$vers->getSqlElementsFromCriteria(array('idProduct'=>$this->idProduct));
    foreach ($versList as $vers) {
      $vp=SqlElement::getSingleSqlElementFromCriteria('VersionProject', array('idProject'=>$this->idProject, 'idVersion'=>$vers->id));
      if (! $vp->id) {
        $vp->idProject=$this->idProject;
        $vp->idVersion=$vers->id;
      }
      $vp->startDate=$this->startDate;
      $vp->endDate=$this->endDate;
      $vp->idle=$this->idle;
      $vp->save();
    }

    return $result;
  }
  public function delete() {
    $result=parent::delete();
    
    // Delete all VersionProject for all versions of Product
    $vers=new Version();
    $versList=$vers->getSqlElementsFromCriteria(array('idProduct'=>$this->idProduct));
    foreach ($versList as $vers) {
      $vp=SqlElement::getSingleSqlElementFromCriteria('VersionProject', array('idProject'=>$this->idProject, 'idVersion'=>$vers->id));
      if ($vp->id) {
        $vp->delete();
      }
    }
    
    return $result;
  }

  public function control() {
  	$result="";
  	if (! $this->id) {
  	  $crit=array('idProject'=>$this->idProject, 'idProduct'=>$this->idProduct);
  	  $list=$this->getSqlElementsFromCriteria($crit, false);
  	  if (count($list)>0) {
        $result.='<br/>' . i18n('errorDuplicateProductProject');
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
  
}
?>