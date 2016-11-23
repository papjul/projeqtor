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

/** ===========================================================================
 * Display the column selector div
 */

require_once "../tool/projeqtor.php";
scriptLog('   ->/tool/planningColumnSelector');

$columns=Parameter::getPlanningColumnOrder();
$columnsAll=Parameter::getPlanningColumnOrder(true);
$desc=Parameter::getPlanningColumnDescription();
  
foreach ($columnsAll as $order=>$col) {
	if ( (isset($resourcePlanning) and ($col=='ValidatedWork' or $col=='Resource' or substr($col,-4)=='Cost') )
	  or (isset($portfolioPlanning) and ($col=='Priority' or $col=='Resource' or $col=='IdPlanningMode') )	) {
	  // noting	
	} else if ( ! SqlElement::isVisibleField($col) ) {
		// noting 
	} else {
	  if ($col=='Name') {
		  echo '<div style="padding: 2px;" id="columnSelector'.$col.'" >';		
		  echo '<span style="display:inline-block;width:15px">&nbsp;</span>'; 
		} else {
		  echo '<div class="dojoDndItem" id="columnSelector'.$col.'" dndType="planningColumn">';
		  echo '<span class="dojoDndHandle handleCursor"><img style="width:6px" src="css/images/iconDrag.gif" />&nbsp;&nbsp;</span>';
		}
	  echo '<span dojoType="dijit.form.CheckBox" type="checkbox" id="checkColumnSelector'.$col.'" ' 
	    . ((substr($columns[$order],0,6)!='Hidden')?' checked="checked" ':'') 
	    . (($col=='Name')?' readonly':'')
	    . ' onChange="changePlanningColumn(\'' . $col . '\',this.checked,\'' . $order . '\')" '
	    . '></span><label for="checkColumnSelector'.$col.'" class="checkLabel" style="white-space:nowrap">';
	  echo '&nbsp;';
	  echo i18n('col' . $col) . "</label>";
	  echo '<div style="float: right; text-align:right">&nbsp;';
	  echo '<div dojoType="dijit.form.NumberSpinner" id="planningColumnSelectorWidthId'.$order.'" ';
	  echo (substr($columns[$order],0,6)=='Hidden')?'disabled="disabled" ':'';
	  echo ' onChange="changePlanningColumnWidth(\'' . $col . '\',this.value)"; ';
	  echo ' constraints="{ min:'.(($col=='Name')?'200':'20').', max:500, places:0 }"';
	  echo ' style="width:50px; text-align: center;" value="'.htmlEncode($desc[$col]['width']).'" >';
	  echo '</div>'; // NumberSpinner
	  echo '&nbsp;</div>'; // style="float: right
	  echo '</div>'; // id=columnSelector
	}
}

?>