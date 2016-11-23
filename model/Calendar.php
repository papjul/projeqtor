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
class Calendar extends SqlElement {

  // List of fields that will be exposed in general user interface
  public $_sec_description;
	public $id;    // redefine $id to specify its visible place 
  public $name;
  public $idCalendarDefinition;
  public $calendarDate;
  public $isOffDay;
  public $day;
  public $week;
  public $month;
  public $year;
  public $idle;
  public $_sec_void;  
  public $_spe_calendarView;
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%" ># ${id}</th>
    <th field="name" width="60%" >${name}</th>
    <th field="calendarDate" width="20%" formatter="dateFormatter" >${date}</th>
    <th field="isOffDay" width="10%" formatter="booleanFormatter">${isOffDay}</th>  
    ';

    private static $_fieldsAttributes=array("name"=>"x", 
                                  "calendarDate"=>"required",
                                  "day"=>"hidden",
                                  "week"=>"hidden",
                                  "month"=>"hidden",
                                  "year"=>"hidden",
                                  "idle"=>"hidden"
  );  
    private static $_colCaptionTransposition = array('calendarDate'=>'date');
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    if (! $id) {
    	$this->isOffDay='1';
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
// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="idle") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.checked) { ';
      $colScript .= '    if (dijit.byId("PlanningElement_realEndDate").get("value")==null) {';
      $colScript .= '      dijit.byId("PlanningElement_realEndDate").set("value", new Date); ';
      $colScript .= '    }';
      $colScript .= '  } else {';
      $colScript .= '    dijit.byId("PlanningElement_realEndDate").set("value", null); ';
      //$colScript .= '    dijit.byId("PlanningElement_realDuration").set("value", null); ';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    return $colScript;
  }
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Set all date values : workDate, 
   * @param $workDate
   * @return void
   */
  public function setDates($calendarDate) {
    $year=substr($calendarDate,0,4);
    $month=substr($calendarDate,5,2);
    $day=substr($calendarDate,8,2);
    $this->calendarDate=$calendarDate;
    $this->day=$year . $month . $day;
    $this->month=$year . $month; 
    $this->year=$year;
    if (weekNumber($calendarDate)=='01' and $month=='12') {$year+=1;}
    else if (weekNumber($calendarDate)>50 and $month=='01') {$year-=1;};
    $this->week=$year.weekNumber($calendarDate);
  }
  
  public function save() {
    $this->setDates($this->calendarDate);
    $this->idle=0;
  	return parent::save();
  }
  
  public function initialize($contry,$year) {
	  if ($contry=='fr') {  // Temporary desactivate France Holidays
	    $aBankHolidays = array (
	          $year.'0101',
	          $year.'0501',
	          $year.'0508',
	          $year.'0714',
	          $year.'0815',
	          $year.'1101',
	          $year.'1111',
	          $year.'1225'
	          );
	    $iEaster = getEaster ((int)$year);
	    $aBankHolidays[] = date ('Ymd', $iEaster);
	    $aBankHolidays[] = date ('Ymd', $iEaster + (86400*39));
	    $aBankHolidays[] = date ('Ymd', $iEaster + (86400*49));
	  }
  }
  
  public static function getOffDayList() {
  	$cal=New Calendar();
  	$crit=array('isOffDay'=>'1', 'idCalendarDefinition'=>'1');
  	$lst=$cal->getSqlElementsFromCriteria($crit);
  	$res='';
  	foreach ($lst as $obj) {
  		$res.='#' . htmlEncode($obj->day) . '#';
  	}
  	return $res; 
  }
  public static function getWorkDayList() {
    $cal=New Calendar();
    $crit=array('isOffDay'=>'0', 'idCalendarDefinition'=>'1');
    $lst=$cal->getSqlElementsFromCriteria($crit);
    $res='';
    foreach ($lst as $obj) {
      $res.='#' . htmlEncode($obj->day) . '#';
    }
    return $res;   	
  }
  
    /** =========================================================================
   * Draw a specific item for the current class.
   * @param $item the item. Correct values are : 
   *    - subprojects => presents sub-projects as a tree
   * @return an html string able to display a specific item
   *  must be redefined in the inherited class
   */
  public function drawSpecificItem($item){
  	if (! $this->id) {
  		//return;
  	}
    $today=date('Y-m-d');
  	global $bankHolidays,$bankWorkdays;
    //$result="<br/>";
    $result="";
    if ($item=='calendarView') {    	
      if ($this->year) {
        $y=$this->year;
      } else {
      	$y=date('Y');
      }
      //echo $y.'#'.$this->idCalendarDefinition;
      //if (! isset($bankWorkdays[$y.'#'.$this->idCalendarDefinition])) {return;	}
      if (! $this->idCalendarDefinition) { return;}
      $result .='<table >';
      $result .='<tr><td class="calendarHeader" colspan="32">' .$y . '</td></tr>';
      for ($m=1; $m<=12; $m++) {
      	$mx=($m<10)?'0'.$m:''.$m;
      	$time=mktime(0, 0, 0, $m, 1, $y);
        $libMonth=i18n(strftime("%B", $time));
      	$result .= '<tr style="height:30px">';
      	$result .= '<td class="calendar" style="background:#F0F0F0; width: 150px;">' . $libMonth . '</td>';
      	for ($d=1;$d<=date('t',strtotime($y.'-'.$mx.'-01'));$d++) {
      		$dx=($d<10)?'0'.$d:''.$d;
      		$day=$y.'-'.$mx.'-'.$dx;
      		$iDay=strtotime($day);
      		$isOff=isOffDay($day,$this->idCalendarDefinition);
      		$style='';
      		if ($day==$today) {
      			$style.='font-weight: bold; font-size: 9pt;';
      		}
      		if (in_array (date ('Ymd', $iDay), $bankWorkdays[$y.'#'.$this->idCalendarDefinition])) {
      			$style.='color: #FF0000; background: #FFF0F0;';
      		} else if (in_array (date ('Ymd', $iDay), $bankHolidays[$y.'#'.$this->idCalendarDefinition])) {
            $style.='color: #0000FF; background: #D0D0FF;';
          } else {
            $style.='background: ';
          	$style.=($isOff)?'#DDDDDD;':'#FFFFFF;';
          }
      		$result.= '<td class="calendar" style="'.$style.'">';
      		$result.= '<div style="cursor: pointer;" onClick="loadContent(\'../tool/saveCalendar.php?idCalendarDefinition='.htmlEncode($this->idCalendarDefinition).'&day='. $day . '\',\'CalendarDefinition_Calendar\');">';
      		$result.=  mb_substr(i18n(date('l',$iDay)),0,1,"UTF-8") . $d ;
      		$result.= '</div>';
      		$result.= '</td>';
      	}
      	$result .= '</tr>';
      }
      $result .='</table>';
      return $result;
    }
  }
  
}
?>