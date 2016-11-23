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
 * CommandMain
 */  
require_once('_securityCheck.php'); 
class CommandMain extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_description;
  public $id;    // redefine $id to specify its visible place 
  public $reference;
  public $name;
  public $idCommandType;
  public $idProject;
  public $idClient;
  public $idContact;
  public $externalReference;  
  public $receptionDate;
  public $idDeliveryMode;
  public $idUser;
  public $creationDate;
  public $Origin;
  public $description;
  public $additionalInfo;
  public $_sec_treatment;
  public $idStatus;
  public $idResource;  
  public $handled;
  public $handledDate;
  public $done;
  public $doneDate;
  public $idle;
  public $idleDate;
  public $cancelled;
  public $_lib_cancelled;
  public $idActivityType;
  public $idActivity;
  public $idPaymentDelay;
  public $_tab_5_3_smallLabel = array('untaxedAmountShort', 'tax', '', 'fullAmountShort','work', 'initial', 'add', 'countTotal');
  public $untaxedAmount;
  public $taxPct;
  public $taxAmount;
  public $fullAmount;
  public $initialWork;
  public $addUntaxedAmount;
  public $_void_1;
  public $addTaxAmount;
  public $addFullAmount;
  public $addWork;
  public $totalUntaxedAmount;
  public $_void_2;
  public $totalTaxAmount;
  public $totalFullAmount;
  public $validatedWork;
  public $initialPricePerDayAmount;
  public $addPricePerDayAmount;
  public $validatedPricePerDayAmount;
  public $_tab_2_2_smallLabel = array('initial', 'validated', 'startDate', 'endDate');
  public $initialStartDate;
  public $validatedStartDate;
  public $initialEndDate;
  public $validatedEndDate;
  public $comment;
  //public $_sec_BillLine;
  public $_BillLine=array();
  public $_BillLine_colSpan="2";
  public $_sec_Link;
  public $_Link=array();
  public $_Attachment=array();
  public $_Note=array();
  
  public $_nbColMax=3;
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="4%" ># ${id}</th>
    <th field="nameProject" width="9%" >${idProject}</th>
    <th field="nameCommandType" width="7%" >${idCommandType}</th>
    <th field="name" width="12%" >${name}</th>
    <th field="colorNameStatus" width="9%" formatter="colorNameFormatter">${idStatus}</th>
    <th field="nameResource" formatter="thumbName22" width="8%" >${responsible}</th>
    <th field="validatedEndDate" width="8%" formatter="dateFormatter" >${validatedEndDate}</th>
  	<th field="untaxedAmount" formatter="costFormatter" width="7%" >${untaxedAmount}</th>
  	<th field="addUntaxedAmount" formatter="costFormatter" width="7%" >${addUntaxedAmount}</th>
  	<th field="totalUntaxedAmount" formatter="costFormatter" width="7%" >${totalUntaxedAmount}</th>
  	<th field="handled" width="4%" formatter="booleanFormatter" >${handled}</th>
    <th field="done" width="4%" formatter="booleanFormatter" >${done}</th>
    <th field="idle" width="4%" formatter="booleanFormatter" >${idle}</th>
    ';

  private static $_fieldsAttributes=array("id"=>"nobr", "reference"=>"readonly",
                                  "name"=>"required", 
                                  "idCommandType"=>"required",
                                  "idStatus"=>"required",
                                  "handled"=>"nobr",
                                  "done"=>"nobr",
                                  "idle"=>"nobr",
                                  "idPaymentDelay"=>"hidden",
						  							      "taxAmount"=>"calculated,readonly",
                                  "fullAmount"=>"readonly",
                                  "addTaxAmount"=>"calculated,readonly",
                                  "addFullAmount"=>"readonly",
                                  "totalTaxAmount"=>"calculated,readonly",
                                  "totalFullAmount"=>"readonly",
                                  "totalUntaxedAmount"=>"readonly",
						  							      "externalReference"=>"required",
                                  "idleDate"=>"nobr",
                                  "cancelled"=>"nobr", 
                                  "validatedWork"=>"readonly",
                                  "initialPricePerDayAmount"=>"hidden",
                                  "addPricePerDayAmount"=>"hidden",
                                  "validatedPricePerDayAmount"=>"hidden"
  );  
  
  private static $_colCaptionTransposition = array('idUser'=>'issuer', 
                                                   'idResource'=> 'responsible',
  													'idActivity'=>'linkActivity',
  'idDeliveryMode'=>'receiveMode');
  private static $_databaseColumnName = array('taxPct'=>'tax');
//  private static $_databaseColumnName = array('idResource'=>'idUser');
    
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    
    if ($this->id) {
    	self::$_fieldsAttributes["creationDate"]='readonly';
    } else {
      $this->receptionDate=date('Y-m-d');
    }
    /*$status=new Status($this->idStatus);
    if ($status->isCopyStatus) {
    	self::$_fieldsAttributes["externalReference"]="";
    }*/
    if (count($this->_BillLine)) {
      self::$_fieldsAttributes['untaxedAmount']='readonly';
      self::$_fieldsAttributes['addUntaxedAmount']='readonly';
      self::$_fieldsAttributes['totalUntaxedAmount']='readonly';
      self::$_fieldsAttributes['initialWork']='readonly';
      self::$_fieldsAttributes['addWork']='readonly';
    }
    if ($this->fullAmount) {
      $this->taxAmount=$this->fullAmount-$this->untaxedAmount;
      $this->addTaxAmount=$this->addFullAmount-$this->addUntaxedAmount;
      $this->totalTaxAmount=$this->totalFullAmount-$this->totalUntaxedAmount;
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
    
/** =========================================================================
   * control data corresponding to Model constraints
   * @param void
   * @return "OK" if controls are good or an error message 
   *  must be redefined in the inherited class
   */
  public function control(){
    $result="";

   	$defaultControl=parent::control();
   	
   	if ($defaultControl!='OK') {
   		$result.=$defaultControl;
   	}
   	
   	if ($result=="") $result='OK';
	
    return $result;
  }

  
  /** =========================================================================
   * Overrides SqlElement::deleteControl() function to add specific treatments
   * @see persistence/SqlElement#deleteControl()
   * @return the return message of persistence/SqlElement#deleteControl() method
   */  
  
  /**=========================================================================
   * Overrides SqlElement::save() function to add specific treatments
   * @see persistence/SqlElement#save()
   * @return the return message of persistence/SqlElement#save() method
   */
  public function save() {
  	$result='';
  	
  	$oldIdProject=0;
  	$oldTotalUntaxedAmount=0;
  	$oldValidatedWork=0;
  	
  	//Check if we are in CREATION
    if (trim($this->id)=='') {
    	// fill the creatin date if it's empty - creationDate is not empty for import ! 
    	if ($this->creationDate=='') $this->creationDate=date('Y-m-d H:i');
  	} else {
  		$old=$this->getOld();
  		$oldIdProject=$old->idProject;
  		$oldTotalUntaxedAmount=$old->totalUntaxedAmount;
  		$oldValidatedWork=$old->validatedWork;
  	}
  
  	if (trim($this->idClient)) {
  	  $client=new Client($this->idClient);
  	  if ($client->taxPct!='' and !$this->taxPct) {
  	    $this->taxPct=$client->taxPct;
  	  }
  	}
  	
  	$billLine=new BillLine();
  	$crit = array("refType"=> "Command", "refId"=>$this->id);
  	$billLineList = $billLine->getSqlElementsFromCriteria($crit,false);
  	if (count($billLineList)>0) {
  	  $amount=0;
  	  $addAmount=0;
  	  $work=0;
  	  $addWork=0;
  	  foreach ($billLineList as $line) {
  	    if ($line->extra) {
  	      $addAmount+=$line->amount;
  	      if ($line->idMeasureUnit==3) $addWork+=$line->quantity; // Only if unit is Days
  	    } else {
  	      $amount+=$line->amount;
  	      if ($line->idMeasureUnit==3) $work+=$line->quantity;
  	    }
  	  }
  	  $this->untaxedAmount=$amount;
  	  $this->addUntaxedAmount=$addAmount;
  	  $this->initialWork=$work;
  	  $this->addWork=$addWork;
  	}
  	if (!$this->untaxedAmount) $this->untaxedAmount=0;
  	if (!$this->addUntaxedAmount) $this->addUntaxedAmount=0;
  	$this->totalUntaxedAmount=$this->untaxedAmount+$this->addUntaxedAmount;
  	$this->validatedWork=$this->initialWork+$this->addWork;
  	
  	$this->fullAmount=$this->untaxedAmount*(1+$this->taxPct/100);
  	$this->addFullAmount=$this->addUntaxedAmount*(1+$this->taxPct/100);
  	$this->totalFullAmount=$this->totalUntaxedAmount*(1+$this->taxPct/100);
  	
  	$this->validatedPricePerDayAmount=($this->validatedWork!=0)?($this->totalUntaxedAmount/$this->validatedWork):0;
  	$this->initialPricePerDayAmount=($this->initialWork!=0)?($this->untaxedAmount/$this->initialWork):0;
  	  	
  	$this->name=trim($this->name);
      
    $resultClass = parent::save();
      
    if (! strpos($resultClass,'id="lastOperationStatus" value="OK"')) {
    	return $resultClass;
    }
    
    /* dispatch of command to validated may be weird */
    if ($oldTotalUntaxedAmount!=$this->totalUntaxedAmount || 
    	$oldIdProject!=$this->idProject || $this->validatedWork!=$oldValidatedWork) {
    	if (trim($oldIdProject)!='') {
	    	$prj=new Project($oldIdProject);
	    	$prj->updateValidatedWork();
    	}
      if (trim($this->idProject)!='') {
	    	$prj=new Project($this->idProject);
	    	$prj->updateValidatedWork();
    	}
    }
    return $resultClass;
  }
  public function simpleSave() {
    return $this->save();
  }
    /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    
    $colScript = parent::getValidationScript($colName);
    if ($colName=="untaxedAmount" || $colName=="taxPct" || $colName=="initialWork"
  	 || $colName=="addUntaxedAmount" || $colName=="addWork") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  updateCommandTotal();';
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    } else if ($colName=="idProject") {
    	$colScript .= '<script type="dojo/connect" event="onChange" >';
    	$colScript .= '  setClientValueFromProject("idClient",this.value);';
    	$colScript .= '  formChanged();';
    	$colScript .= '</script>';
    } else if ($colName=="idClient") {
    	$colScript .= '<script type="dojo/connect" event="onChange" >';
    	$colScript .= '  refreshList("idContact", "idClient", this.value, null, null, false);';
    	$colScript .= '  formChanged();';
    	$colScript .= '</script>';
    }    
    return $colScript;
  }
    
  private function zeroIfNull($value) {
  	$val = $value;
  	if (!$val || $val=='' || !is_numeric($val)) {
  		$val=0;
  	} else { 
  		$val=$val*1;
  	}
  	
  	return $val;
  	
  }
  
}
?>