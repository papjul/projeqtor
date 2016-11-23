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
 * Stauts defines list stauts an activity or action can get in (lifecylce).
 */  
require_once('_securityCheck.php'); 
class CalendarDefinition extends SqlElement {

  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id;    // redefine $id to specify its visible place 
  public $name;
  //public $sortOrder=0;
  public $idle;
  public $_sec_Year;
  public $_spe_year;
  public $_spe_copyFromDefault;
  public $_sec_Calendar;
  public $_spe_calendar;
  public $_calendar_colSpan="2";
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%"># ${id}</th>
    <th field="name" width="60%">${name}</th>
    <th field="idle" width="5%" formatter="booleanFormatter">${idle}</th>
    ';
  private static $_fieldsAttributes=array("sortOrder"=>"hidden");
  
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
   * Return the specific fieldsAttributes
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
  	return self::$_fieldsAttributes;
  }
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }
  
  public function drawSpecificItem($item){
  	//scriptLog("Project($this->id)->drawSpecificItem($item)");
  	$result="";
  	$cal=new Calendar;
  	$currentYear=date('Y');
  	if ($item=='calendar') {
  		//$result.='<div id="viewCalendarDiv" dojoType="dijit.layout.ContentPane" region="top">';  		
      $cal->setDates($currentYear.'-01-01');
      $cal->idCalendarDefinition=$this->id;
      $result= $cal->drawSpecificItem('calendarView');
      //$result.='</div>';
  		return $result;
  	} else if ($item=='year') {
  		$result.='<div style="width:70px; text-align: center; color: #000000;" dojoType="dijit.form.NumberSpinner"'
  		 . ' constraints="{min:2000,max:2100,places:0,pattern:\'###0\'}" intermediateChanges="true" maxlength="4" '
       . ' value="'. $currentYear.'" smallDelta="1" id="calendartYearSpinner" name="calendarYearSpinner" >'
  		 . ' <script type="dojo/method" event="onChange" >'
  		 . ' 	loadContent("../tool/saveCalendar.php?idCalendarDefinition='.htmlEncode($this->id).'&year="+this.value,"CalendarDefinition_Calendar");'
  		 . ' </script>'
  		 . '</div>';
  		 return $result;
  	} else if ($item=='copyFromDefault') {
  		if ($this->id!=1) {
  		  $result.='<div type="button" dojoType="dijit.form.Button" showlabel="true">'
  			. i18n('copyFromCalendar')	
  		  . ' <script type="dojo/method" event="onClick" >'
  			. ' 	loadContent("../tool/saveCalendar.php?copyYearFrom="+dijit.byId("calendarCopyFrom").get("value")+"&idCalendarDefinition='.htmlEncode($this->id).'&year="+dijit.byId("calendartYearSpinner").get("value"),"CalendarDefinition_Calendar");'
  			. ' </script>'
  			. '</div>&nbsp;&nbsp;';
  		  $result.='<select dojoType="dijit.form.FilteringSelect" class="input" xlabelType="html" '
				. '  style="width:150px;" name="calendarCopyFrom" id="calendarCopyFrom" '.autoOpenFilteringSelect().'>';
  		  ob_start();
				htmlDrawOptionForReference('idCalendarDefinition', 1, null, true);
				$result.=ob_get_clean();
				$result.= '</select>';
  		}		
  	}
  	
  	return $result;
  }
  
  public function deleteControl() {
  	$result="";
  	if ($this->id==1)	{
  		$result .= "<br/>" . i18n("errorDeleteDefaultCalendar");
  	}
  	if (! $result) {
  		$result=parent::deleteControl();
  	}
  	return $result;
  }
}
?>