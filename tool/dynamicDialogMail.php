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

scriptLog('dynamicDialogMailt.php');
$isIE=false;
if (array_key_exists('isIE',$_REQUEST)) {
	$isIE=$_REQUEST['isIE'];
} 
$objectClass="";
//$obj=new SqlElement();
if (array_key_exists("objectClass", $_REQUEST)) {
	$objectClass=$_REQUEST['objectClass'];
	Security::checkValidClass($objectClass);

	$obj=new $objectClass();
}
?>
<input type="hidden" name="dialogMailObjectClass" id="dialogMailObjectClass" value="<?php echo htmlEncode($objectClass);?>" />
  <table>
    <tr>
      <td>
        <form dojoType="dijit.form.Form" id='mailForm' name='mailForm' onSubmit="return false;">
          <input id="mailRefType" name="mailRefType" type="hidden" value="" />
          <input id="mailRefId" name="mailRefId" type="hidden" value="" />
          <table>
          <?php if (property_exists($objectClass, 'idContact')) { ?>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToContact"><?php echo htmlEncode($obj->getColCaption("idContact"));?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToContact" name="dialogMailToContact" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
          <?php } ?>
          <?php if (property_exists($objectClass, 'idUser') and $objectClass!='Project') {?>   
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToUser"><?php echo htmlEncode($obj->getColCaption("idUser")); ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToUser" name="dialogMailToUser" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
          <?php } ?>
          <?php if (property_exists($objectClass, 'idResource') ) {?>   
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToResource"><?php echo htmlEncode($obj->getColCaption("idResource")); ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToResource" name="dialogMailToResource" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
          <?php } ?>
          <?php if (property_exists($objectClass, 'idSponsor')) { ?>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToSponsor"><?php echo htmlEncode($obj->getColCaption("idSponsor"));?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToSponsor" name="dialogMailToSponsor" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
          <?php } ?>
          <?php if (property_exists($objectClass, 'idProject')) { ?>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToProject"><?php echo i18n("colMailToProject") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToProject" name="dialogMailToProject" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToLeader"><?php echo i18n("colMailToLeader") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToLeader" name="dialogMailToLeader" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToManager"><?php echo i18n("colMailToManager") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToManager" name="dialogMailToManager" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
          <?php } ?>
            <?php if (property_exists($objectClass, '_Assignment') ) {?>  
             <tr>
              <td class="dialogLabel">
                <label for="dialogMailToAssigned"><?php echo i18n("colMailToAssigned") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToAssigned" name="dialogMailToAssigned" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
            <?php }?>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailToOther"><?php echo i18n("colMailToOther") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailToOther" name="dialogMailToOther" dojoType="dijit.form.CheckBox" 
                 type="checkbox" onChange="dialogMailToOtherChange();">
                </div> <?php echo i18n('helpOtherEmail');?>
              </td>
            </tr>
            <tr>
              <td class="dialogLabel">
              </td>
              <td>
                <textarea dojoType="dijit.form.Textarea" 
					          id="dialogOtherMail" name="dialogOtherMail"
					          style="width: 500px; display:none"
					          maxlength="4000"
					          class="input" ></textarea>
              </td>
            </tr>
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailMessage"><?php echo i18n("colMailMessage") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                 <textarea dojoType="dijit.form.Textarea" 
                    id="dialogMailMessage" name="dialogMailMessage"
                    style="width: 500px; "
                    maxlength="4000"
                    class="input" ></textarea>
              </td>
            </tr>
            <?php if (property_exists($objectClass, '_Note') ) {?>    
            <tr>
              <td class="dialogLabel">
                <label for="dialogMailSaveAsNote"><?php echo i18n("colSaveAsNote") ?>&nbsp;:&nbsp;</label>
              </td>
              <td>
                <div id="dialogMailSaveAsNote" name="dialogMailSaveAsNote" dojoType="dijit.form.CheckBox" type="checkbox" ></div>
              </td>
            </tr>
            <?php }?>
          </table>
       </form>
     </td>
   </tr>
    <tr>
      <td align="center">
        <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogMail').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button dojoType="dijit.form.Button" type="submit" id="dialogMailSubmit" onclick="protectDblClick(this);sendMail();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>    