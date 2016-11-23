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
scriptLog('   ->/tool/listColumnSelector');

$listColumns=ColumnSelector::getColumnsList($objectClass);

$cpt=0;

foreach ($listColumns as $col) {
	if ( ! SqlElement::isVisibleField($col->attribute) ) {
		// nothing
	} else {
		echo '<div style="width:100%;" class="dojoDndItem" id="listColumnSelectorId'.htmlEncode($col->id).'" dndType="planningColumn">';
		if ($col->attribute=='id') {
		  echo '<span style="float:left;width:14px;">&nbsp;</span>';
		} else {
		  echo '<span class="dojoDndHandle handleCursor"><img style="width:6px" src="css/images/iconDrag.gif" />&nbsp;&nbsp;</span>';
		}
		echo '<span dojoType="dijit.form.CheckBox" type="checkbox" id="checkListColumnSelectorId'.$cpt.'" '
		. ((! $col->hidden)?' checked="checked" ':'')
		. (( $col->field=='id' or $col->field=='name')?' disabled="disabled" ':'')
		. ' onChange="changeListColumn(\'' . htmlEncode($col->id) . '\','.$cpt.',this.checked,\'' . htmlEncode($col->sortOrder) . '\')" '
		. '></span><label for="checkListColumnSelectorId'.$cpt.'" class="checkLabel">';
		echo '&nbsp;';
		echo $col->_displayName . "</label>&nbsp;&nbsp;";
		echo '<div style="float: right; text-align:right">';
		if ($col->attribute=='name') {
      echo '<div class="input" dojoType="dijit.form.NumberTextBox" id="checkListColumnSelectorWidthId'.$cpt.'" ';
      echo 'disabled="disabled" ';     
      echo ' style="width:22px;position:absolute;right:24px;background: #F0F0F0; text-align: center;" value="'.htmlEncode($col->widthPct).'" ></div>';
      echo '<input type="hidden" id="columnSelectorNameFieldId" value="'.$cpt.'" />';
      echo '<input type="hidden" id="columnSelectorNameTableId" value="'.htmlEncode($col->id).'" />';
		} else {
			echo '<div dojoType="dijit.form.NumberSpinner" id="checkListColumnSelectorWidthId'.$cpt.'" ';
			echo ($col->hidden or $col->attribute=='name')?'disabled="disabled" ':'';
			if ($col->attribute!='name') {	
			  echo ' onChange="changeListColumnWidth(\'' . htmlEncode($col->id) . '\','.$cpt.',this.value)" ';
			  echo ' onClick="recalculateColumnSelectorName()" ';
			}	 
			echo ' constraints="{ min:1, max:50, places:0 }"';
			echo ' style="width:'.(($col->hidden or $col->attribute=='name')?'37':'35').'px; text-align: center;" value="'.htmlEncode($col->widthPct).'" >';
			echo '</div>';
		}
		echo '&nbsp;</div>';
		echo '</div>';
		$cpt++;

	}
}
?>