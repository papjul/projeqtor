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

?>
<table width="500px">
    <tr><td style="width:100%;background-color:#F0F0F0;font-weight:bold;text-align:center;padding:10px;"><?php echo i18n("savePlannedDates");?></td></tr>
    <tr><td >&nbsp;</td></tr>
    <tr>
      <td width="100%">
       <form id='dialogPlanSaveDatesForm' name='dialogPlanSaveDatesForm' onSubmit="return false;">
         <table width="100%" >
           <tr>
             <td class="dialogLabel"  >
               <label for="idProjectPlanSaveDates" ><?php echo i18n("colIdProject") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="idProjectPlanSaveDates" name="idProjectPlanSaveDates" 
                class="input" value="" >
                 <?php 
                    $proj=null; 
                    if (array_key_exists('project',$_SESSION)) {
                        $proj=$_SESSION['project'];
                    }
                    if ($proj=="*" or ! $proj) $proj=null;
                    htmlDrawOptionForReference('idProject', $proj, null, false);
                 ?>
               </select>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr><td colspan="2" style="width:100%; text-align: center;">
             
             <table width="100%">
               <tr><td colspan="5"><b><?php echo i18n("reportPlannedDates");?><br/></b></td></tr>
               <tr><td colspan="5">&nbsp;</td>
               <tr>
                 <td style="width:35%;text-align: right;"><b><?php echo i18n('updateInitialDates');?></b></td>
                 <td style="width:5%">&nbsp;</td> 
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesAlways" 
	                    value="ALWAYS" /><?php echo i18n('always');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesIfEmpty" 
	                    checked value="IFEMPTY" /><?php echo i18n('ifEmpty');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateInitialDates" id="updateInitialDatesNever"  
	                    value="NEVER" /><?php echo i18n('never');?></td>
               </tr>
               <tr><td colspan="5">&nbsp;</td>	
               <tr>
                 <td style="width:35%;text-align: right;"><b><?php echo i18n('updateValidatedDates');?></b></td>
                 <td style="width:5%">&nbsp;</td> 
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesAlways" 
	                    checked value="ALWAYS" /><?php echo i18n('always');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesIfEmpty" 
	                     value="IFEMPTY" /><?php echo i18n('ifEmpty');?></td>
                 <td style="width:20%">
                    <input type="radio" dojoType="dijit.form.RadioButton" name="updateValidatedDates" id="updateValidatedDatesNever"  
	                    value="NEVER" /><?php echo i18n('never');?></td>
               </tr> 
             </table>
           </td></tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="dialogPlanSaveDatesCancel">
        <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogPlanSaveDates').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button dojoType="dijit.form.Button" type="submit" id="dialogPlanSaveDatesSubmit" onclick="protectDblClick(this);planSaveDates();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>