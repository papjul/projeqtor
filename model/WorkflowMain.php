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
class WorkflowMain extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  public $sortOrder;
  public $idle;
  public $workflowUpdate;
  public $description;
  public $_sec_void;
  public $_spe_hideStatus;
  public $_sec_WorkflowDiagram;
  public $_workflowDiagram_colSpan="2";
  public $_spe_workflowDiagram;
  public $_sec_WorkflowStatus;
  public $_workflowStatus_colSpan="2";
  public $_spe_workflowStatus;
  public $_workflowStatus;
  
  public $_statusList;
  
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%"># ${id}</th>
    <th field="name" width="75%">${name}</th>
    <th field="sortOrder" width="10%">${sortOrderShort}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';
  
  private static $_fieldsAttributes=array(
    "workflowUpdate"=>"hidden"
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
    return array_merge(parent::getStaticFieldsAttributes(),self::$_fieldsAttributes);
  }
 
  /** ==========================================================================
   * Return list of workflow status for a workflow (id)
   * @return an array of WorkflowStatus
   */
  public function getWorkflowstatus() {
    if ($this->id==null or $this->id=='') {
      return array();
    }
    if ($this->_workflowStatus) {
      return $this->_workflowStatus;
    }
    $ws=new WorkflowStatus();
    $crit=array('idWorkflow'=>$this->id);
    $wsList=$ws->getSqlElementsFromCriteria($crit, false);
    return $wsList;
  }
  
   /** ==========================================================================
   * Return check value of workflow status for a workflow
   * @return a 3 level array [idStatusFrom] [idStatusTo] [idProfile] => check value
   */
  public function getWorkflowstatusArray() {
    $wsList=$this->getWorkflowstatus();
    $result=array();
    // Initialize
    $statusList=$this->getStatusList();
    $profileList=SqlList::getList('Profile');
    foreach($statusList as $idFrom => $valFrom) {
      $result[$idFrom]=array();
      foreach($statusList as $idTo => $valTo) {
        if ($idFrom!=$idTo) {
          $result[$idFrom][$idTo]=array();
          foreach($profileList as $idProf => $valProf) {
            $result[$idFrom][$idTo][$idProf]=0;
          }
        }
      }
    }
    // Get Data
    foreach ($wsList as $ws) {
      $result[$ws->idStatusFrom][$ws->idStatusTo][$ws->idProfile]=$ws->allowed;
    }
    return $result;
  }
  
  private function getStatusList() {
  	if ($this->_statusList) {
  		return $this->_statusList;
  	}
  	$statusList=SqlList::getList('Status');
  	foreach ($statusList as $idStatus=>$status) {
  		$critArray=array('scope'=>'workflow', 'objectClass'=>'workflow#'.$this->id, 'idUser'=>$idStatus);
  		$cs=SqlElement::getSingleSqlElementFromCriteria("ColumnSelector", $critArray);
  		if ($cs and $cs->id and $cs->hidden) {
  			unset ($statusList[$idStatus]);
  		}
  	}
  	$this->_statusList=$statusList;
  	return $statusList;
  }
  /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
    global $_REQUEST, $print;
    if (array_key_exists('destinationWidth', $_REQUEST)) {
      $detailWidth=$_REQUEST['destinationWidth'];
	    $detailWidth=preg_replace('/[^0-9]/','', $detailWidth); // only allow digits
      $detailWidth-=40;
      $detailWidth.='px';
    } else {
      $detailWidth="100%";
    }
    $result="";
    if ($item=='workflowStatus') {
      $width="100px";
      $statusList=$this->getStatusList();
      $profileList=SqlList::getList('Profile');
      $profileIdList="";
      foreach ($profileList as $profileCode => $profileValue) {
        $profileIdList.=$profileCode . " ";
      }     
      $nbProfiles=count($profileList);
      $result .= '<div style="border: 1px solid #A0A0A0;overflow: auto; width: ' . $detailWidth . '">';
      $result .= '<table style="zoom:100%;">';

      $wsListArray=$this->getWorkflowstatusArray();
      foreach ($statusList as $statLineCode => $statLineValue) {
        $result .= ' <tr>';
        $result .= '  <th class="workflowHeader">' . i18n('from') . '&nbsp;\\&nbsp;' . i18n('to') . '</th>';
        foreach ($statusList as $statCode => $statValue) {
          $result .= '  <th class="workflowHeader">' . $statValue . '</th>';
        }
        $result .= '  <th class="workflowHeader"></th>';
        $result .='</tr>'; 
        $result .= '<tr>';
        $result .= '  <td class="workflowHeader">' . $statLineValue . '</td>';
        foreach ($statusList as $statColumnCode => $statColumnValue) {
          $result .= '  <td class="workflowData">';
          if ($statColumnCode!=$statLineCode) {
            $allChecked=true;
            foreach ($profileList as $profileCode => $profileValue) {  
              if ($wsListArray[$statLineCode][$statColumnCode][$profileCode]==0) {
                $allChecked=false;
              }
            }
            $title=$statLineValue . ' => ' . $statColumnValue;
            $result .='<table>' ;
            $result .= '  <tr title="' . $title . '"><td>';
            // dojotype not set to improve perfs
            $result .= '  <input xdojoType="dijit.form.CheckBox" type="checkbox" ';
            $result .= ' onclick="workflowSelectAll('. $statLineCode . ',' . $statColumnCode . ',\'' . $profileIdList .'\');"';
            $name = 'val_' . $statLineCode . '_' . $statColumnCode;
            $result .= ' name="' . $name . '" id="' . $name . '" ';
            $result .= ($allChecked)?' checked ':'';
            $result .= '/>';
            $result .= ' </td>';
            $result .= '  <td><b>' . i18n('all') . '</b></td></tr>';  
            //$profileIdx=0;
            foreach ($profileList as $profileCode => $profileValue) {  
              $titleProfile=$title . "\n" . $profileValue;                          
              $result .= '  <tr title="' . $titleProfile . '" class="workflowDetail" ><td valign="top" style="vertical-align: top;" >';
              // dojotype not set to improve perfs
              $result .= '  <input xdojoType="dijit.form.CheckBox" type="checkbox" ';
              $result .= ' onclick="workflowChange('. $statLineCode . ',' . $statColumnCode . ',\'' . $profileIdList .'\');"';
              $name = 'val_' . $statLineCode . '_' . $statColumnCode . '_' . $profileCode;
              $result .= ' name="' . $name . '" id="' . $name . '" ';
              if ($wsListArray[$statLineCode][$statColumnCode][$profileCode]==1) { $result .=  'checked'; }
              $result .= ' />';
              $result .= ' </td> ';
              $result .= '  <td><div style="width:60px;overflow: hidden; white-space: nowrap; overflow: hidden; "><span class="nobr">' . $profileValue . '</span></div></td></tr>';  
            }
            $result .= '</table>';
          }
          $result .='</td>';
        }
        $result .= '  <td class="workflowHeader">' . $statLineValue . '</td>';
        $result .= '</tr>';
      } 
      $result .= ' <tr>';
      $result .= '  <th class="workflowHeader">' . i18n('from') . '&nbsp;\\&nbsp;' . i18n('to') . '</th>';
      foreach ($statusList as $statCode => $statValue) {
        $result .= '  <th class="workflowHeader">' . $statValue . '</th>';
      }
      $result .='</tr>'; 
      $result .= '</table>';
      $result .= '</div>';
      
    // WORKFLOW DIAGRAM  
    } else if ($item=='workflowDiagram') {
      $statusList=$this->getStatusList();
      $statusColorList=SqlList::getList('Status', 'color');
      foreach ($statusColorList as $key=>$val) {
        if (strtolower($val)=='#ffffff') {
          $statusColorList[$key]='#eeeeee';
        }
      }
      $profileList=SqlList::getList('Profile');
      $width="75";
      $height="15";
      $sepWidth="10";
      $sepHeight="10";
      $dottedStyle='solid';
      $arrowDownImg='<div class="wfDownArrow"></div>';
      $arrowUpImg='<div class="wfUpArrow"></div>';
      $wsListArray=$this->getWorkflowstatusArray();
      $crossArray=array();
      foreach ($statusList as $statLineCode => $statLineValue) {
        $crossArray[$statLineCode]=array();
        foreach ($statusList as $statColumnCode => $statColumnValue) {
          $allChecked=true;
          $oneChecked=false;
          if ($statColumnCode!=$statLineCode) {           
            foreach ($profileList as $profileCode => $profileValue) {  
              if ($wsListArray[$statLineCode][$statColumnCode][$profileCode]==0) {
                $allChecked=false;
              } else {
                $oneChecked=true;
              }
            }            
          }
          if ($allChecked) {
            $val="ALL";
          } else if ($oneChecked) {
            $val="ONE";
          } else {
            $val="NO";
          }
          $crossArray[$statLineCode][$statColumnCode]=$val;
        }
      }
      $i=0;
      $max=array();
      $maxAll=array();
      $maxOne=array();
      $min=array();
      $minAll=array();
      $minOne=array();
      $borderLeft=array();
      $sepLeft=array();
      $borderRight=array();
      $sepRight=array();
      foreach ($statusList as $statLineCode => $statLineValue) {
        $j=0;
        $i++;
        $max[$i]=$i;
        $maxAll[$i]=$i;
        $maxOne[$i]=$i;
        $min[$i]='';
        $minAll[$i]='';
        $minOne[$i]='';
        foreach ($statusList as $statColumnCode => $statColumnValue) {
          $j++;
          //$min[$j]=$j;
          if ($crossArray[$statLineCode][$statColumnCode]!="NO") {
            if ($crossArray[$statLineCode][$statColumnCode]=="ALL") {
              $styleLine='solid';
            } else {
              $styleLine=$dottedStyle;
            }      
            if ($i<$j) {
              $max[$i]=$j;
              if ($crossArray[$statLineCode][$statColumnCode]=="ALL") {
                $maxAll[$i]=$j; 
              } else {
                $maxOne[$i]=$j; 
              }
              for ($t=$i+1;$t<$j;$t++) {
                $borderLeft[$t][$j]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
                $sepLeft[$t][$j]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
              }
              $sepLeft[$i][$j]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
            } else if ($i>$j) {
              if ($min[$i]=='') {
                $min[$i]=$j;
                $minOne[$i]=$j;
              }
              if ($crossArray[$statLineCode][$statColumnCode]=="ALL") {
                if ($minAll[$i]=='') {
                  $minAll[$i]=$j;
                }
              } 
              for ($t=($j+1);$t<=$i;$t++) {
                if (! isset($borderRight[$t][$j])) {
                  $borderRight[$t][$j]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
                }
                if (! isset($sepRight[$t-1][$j])) {
                  $sepRight[$t-1][$j]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
                }
              }
              //$sepRight[$j-1][$i-1]='2px ' . $styleLine . ' ' . $statusColorList[$statLineCode];
            }
          }
        } 
      }
      if (!$print) {
        $result.='<div style="width:'.$detailWidth.'; height:auto; overflow-x: auto; overflow-y: hidden; border: 1px solid #A0A0A0;">';
        //$result.='<div>';
      }
      $result.='<table style="zoom:90%;margin:0; spacing:0; padding:0; background-color:#FFFFFF;">';
      $result.='<tr><td colspan="' . (count($statusList)*2+1) .'"><div style="height: ' . $sepHeight . 'px;"></div></td></tr>';
      $i=0;
      foreach($statusList as $idL=>$nameL) {
        $i++;
        $result.='<tr>';
        $result.='<td><div style="width:' . $sepWidth . 'px">' . '</div></td>'; 
        $j=0;
        foreach($statusList as $idC=>$nameC) {
          $j++;
          $colorI=$statusColorList[$idL];
          if (! $colorI or $colorI=='#FFFFFF') {
            $colorI="#000000";
          }
          if ($idL==$idC) {
            $result.='<td style="border:2px solid ' . $colorI . ';">';
            $result.='<div style="text-align:center; width:' . $width . 'px;height: ' . $height . 'px;">' . $nameL . '</div>';
          } else if ($i<$j) {
            $border='';
            $arrow="";
            if ($max[$i]>$j) {
              $form=$dottedStyle;
              if ($maxAll[$i]>$j) {
                $form='solid';
              }
              $border.='border-bottom:2px ' . $form . ' ' . $colorI . ';';           
            }
            if (isset($borderLeft[$i][$j])) {
              $border.='border-left:' . $borderLeft[$i][$j] . ';';
              //$arrow=$arrowImg;
            }
            $result.='<td style="' . $border . '">';
            $result.='<div style="width:' . $width . 'px;height: ' . $height . 'px;">' . $arrow . '</div>';
          } else {
            $border='';
            $arrow="";
            if ($min[$i] and $min[$i]<=$j) {
              $form=$dottedStyle;
              if ($minAll[$i] and $minAll[$i]<=$j) {
                $form='solid';
              }
              $border.='border-bottom:2px ' . $form . ' ' . $colorI . ';';            
            }
            if (isset($borderRight[$i][$j])) {
              $border.='border-left:' . $borderRight[$i][$j] . ';';
              //$arrow=$arrowImg;
            }
            $result.='<td style="' . $border . '">';
            $result.='<div style="width:' . $width . 'px;height: ' . $height . 'px;">' . $arrow . '</div>';
          }
          $result.='</td>';
          if ($i<=$j and $max[$i]>$j) {
            $border='border-bottom:2px ' . $dottedStyle . ' ' . $colorI . ';';
            if ($maxAll[$i]>$j) {
              $border='border-bottom:2px solid ' . $colorI . ';';
            }
            $result.='<td  style="' . $border . '"><div style="width:' . $sepWidth . 'px;height: ' . $height . 'px;"></div></td>';
          } else if ($j<$i and $min[$i] and $min[$i]<=$j) {
            $border='border-bottom:2px ' . $dottedStyle . ' ' . $colorI . ';';
            if ($minAll[$i] and $minAll[$i]<=$j) {
              $border='border-bottom:2px solid ' . $colorI . ';';
            }
            $result.='<td  style="' . $border . '"><div style="width:' . $sepWidth . 'px;height: ' . $height . 'px;"></div></td>';
          } else {
            $result.='<td><div style="width:' . $sepWidth . 'px;height: ' . $height . 'px;"></div></td>';
          } 
        }
        $result.='</tr>';
        $j=0;
        $result.='<tr>';
        $result.='<td><div style="width:' . $sepWidth . 'px; height: ' . $sepHeight . 'px;">' . '</div></td>'; 
        foreach($statusList as $idC=>$nameC) {
          $j++;
          $border='';
          $arrow="";
          if (isset($sepLeft[$i][$j])){
            $border.='border-left:' . $sepLeft[$i][$j] . ';';
            if ($i==$j-1) {
              $arrow=$arrowDownImg;
            }
          }
          if (isset($sepRight[$i][$j])){
            $border.='border-left:' . $sepRight[$i][$j] . ';';
            if ($i==$j) {
              $arrow=$arrowUpImg;
            }
          }
          $result.='<td style="' . $border . '"><div style="height: ' . $sepHeight . 'px;width:' . $sepWidth . 'px;position: relative;">' . $arrow . '</div></td>';
          $result.='<td><div style="height: ' . $sepHeight . 'px;width:' . $sepWidth . 'px;"></div></td>';
        }
        $result.='</tr>';
        
      }

      $result.='</table>';
      if (! $print) {
      	//$result.='</div>';
        $result.='</div>';
      }
    } else if ($item=='hideStatus') {
    	if (!$print and $this->id) {
        $result.='<button id="workflowParameterButton" dojoType="dijit.form.Button" showlabel="false"';
        $result.='title="'.i18n('workflowParameters').'"'; 
        $result.='iconClass="iconParameter16" style="position:relative;left:-31px;top:12px;">';
        $result.=' <script type="dojo/connect" event="onClick" args="evt">';
		    $result.='  showWorkflowParameter('.htmlEncode($this->id).');';
        $result.=' </script>';
        $result.='</button>';
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
    $statusList=SqlList::getList('Status');
    $firstStatus=key($statusList);
    $firstStatusName=current($statusList);
    $firstStatusFound=false;
    $search = 'val_' . $firstStatus . '_';
    foreach ($_REQUEST as $field=>$value) { // The first status (default=recorded) is seached in the workflow definition 
      if (substr($field,0,strlen($search))==$search) {
        $firstStatusFound=true;
        break;
      }
    }    
    if (!$firstStatusFound) { // First status is not found : so it will never be possible to quit this workflow ...
        $result.='<br/>' . i18n('msgFirstStatusMandatoryInWorkflow',array($firstStatusName)); 
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
  
  /** =========================================================================
   * save data
   * @param void
   */
  public function save() {
    global $_REQUEST;
    
    projeqtor_set_time_limit(300);
    
    if ($this->workflowUpdate and $this->workflowUpdate!="[     ]" and $this->workflowUpdate!="[      ]") {
      $old=$this->getOld();
      if (! $old->workflowUpdate or $old->workflowUpdate=="[      ]") {
        $this->workflowUpdate="[     ]";
      } else {
        $this->workflowUpdate="[      ]";
      }
    }
    $result = parent::save();   
    if (! strpos($result,'id="lastOperationStatus" value="OK"')) {
      return $result;     
    }
    // save detail (workflowstatus)
    $statusList=$this->getStatusList();
    $profileList=SqlList::getList('Profile');
    $ws=new WorkflowStatus();
    //$ws->purge("idWorkFlow='" . $this->id . "'");
    $oldArray=$this->getWorkflowstatusArray();
    foreach ($statusList as $statLineCode => $statLineValue) {
      foreach ($statusList as $statColumnCode => $statColumnValue) {
        if ($statLineCode!=$statColumnCode) {
          foreach ($profileList as $profileCode => $profileValue) {
            $oldVal=$oldArray[$statLineCode][$statColumnCode][$profileCode];
            $valName = 'val_' . $statLineCode . '_' . $statColumnCode . '_' . $profileCode;
            if (array_key_exists($valName,$_REQUEST)) {            
              if ($oldVal!=1) {
                $ws=new WorkflowStatus();
                $ws->idWorkflow=$this->id;
                $ws->idProfile=$profileCode;
                $ws->idStatusFrom=$statLineCode;
                $ws->idStatusTo=$statColumnCode;
                $ws->allowed=1;
                $ws->save();    
              }
            } else {
              if ($oldVal==1) {
                $crit=array('idWorkflow'=>$this->id,
                            'idProfile'=>$profileCode,
                            'idStatusFrom'=>$statLineCode,
                            'idStatusTo'=>$statColumnCode);
                $ws=SqlElement::getSingleSqlElementFromCriteria('WorkflowStatus', $crit);
                $ws->delete();
              }  
            }
          }   
        }     
      }
    }
    return $result;
  }
   
  public function copy() {
     $result=parent::copy();
     $new=$result->id;
     $ws=new WorkflowStatus();
     $crit=array('idWorkflow'=>$this->id);
     $lst=$ws->getSqlElementsFromCriteria($crit);
     foreach ($lst as $ws) {
       $ws->idWorkflow=$new;
       $ws->id=null;
       $ws->save();
     }
     
     Sql::$lastQueryNewid=$new;
     return $result;
  }
}
?>