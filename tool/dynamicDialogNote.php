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
require_once "../tool/projeqtor.php";
if (! array_key_exists('objectClass',$_REQUEST)) {
  throwError('Parameter objectClass not found in REQUEST');
}
$objectClass=$_REQUEST['objectClass'];
Security::checkValidClass($objectClass);

if (! array_key_exists('objectId',$_REQUEST)) {
  throwError('Parameter objectId not found in REQUEST');
}
$objectId=$_REQUEST['objectId'];
Security::checkValidId($objectId);

$noteId=null;
if (array_key_exists('noteId',$_REQUEST)) {
  $noteId=$_REQUEST['noteId'];
  Security::checkValidId($noteId);
}
if ($noteId) {
  $note=new Note($noteId);
} else {
  $note=new Note();
  $note->refType=$objectClass;
  $note->refId=$objectId;
  $note->idPrivacy=1;
}
$detailHeight=600;
$detailWidth=1010;
if (array_key_exists('screenWidth',$_SESSION) and $_SESSION['screenWidth']) {
  $detailWidth = round($_SESSION['screenWidth'] * 0.60);
}
if (array_key_exists('screenHeight',$_SESSION)) {
  $detailHeight=round($_SESSION['screenHeight']*0.60);
}
?>
<div>
  <table style="width:100%;">
    <tr><td>
      <div <?php if (!$noteId) echo 'style="padding-bottom:7px"';?> id="dialogNotePredefinedDiv" dojoType="dijit.layout.ContentPane" region="center">
      <?php if (!$noteId) include "../tool/dynamicListPredefinedText.php";?>
      </div></td></tr>
    <tr>
      <td>
       <form id='noteForm' name='noteForm' onSubmit="return false;" >
         <input id="noteId" name="noteId" type="hidden" value="<?php echo $note->id;?>" />
         <input id="noteRefType" name="noteRefType" type="hidden" value="<?php echo $note->refType;?>" />
         <input id="noteRefId" name="noteRefId" type="hidden" value="<?php echo $note->refId;?>" />
         <input id="noteEditorType" name="noteEditorType" type="hidden" value="<?php echo getEditorType();?>" />
         <?php if (getEditorType()=="CK") {?> 
          <textarea style="width:<?php echo $detailWidth;?>px; height:<?php echo $detailHeight;?>px"
          name="noteNote" id="noteNote"><?php echo htmlspecialchars($note->note);?></textarea>
        <?php } else if (getEditorType()=="text"){
          $text=new Html2Text($note->note);
          $val=$text->getText();?>
          <textarea dojoType="dijit.form.Textarea" 
          id="noteNote" name="noteNote"
          style="width: 500px;"
          maxlength="4000"
          class="input"
          onClick="dijit.byId('noteNote').setAttribute('class','');"><?php echo $val;?></textarea>
        <?php } else {?>
          <textarea dojoType="dijit.form.Textarea" type="hidden"
           id="noteNote" name="noteNote"
           style="display:none;"><?php echo htmlspecialchars($note->note);?></textarea>    
           <div data-dojo-type="dijit.Editor" id="noteNoteEditor"
             data-dojo-props="onChange:function(){top.dojo.byId('noteNote').value=arguments[0];}
              ,plugins:['removeFormat','bold','italic','underline','|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 
                        'justifyRight', 'justifyFull','|','insertOrderedList','insertUnorderedList','|']
              ,onKeyDown:function(event){top.onKeyDownFunction(event,'noteNoteEditor',this);}
              ,onBlur:function(event){top.editorBlur('noteNoteEditor',this);}
              ,extraPlugins:['dijit._editor.plugins.AlwaysShowToolbar','foreColor','hiliteColor']"
              style="color:#606060 !important; background:none; 
                padding:3px 0px 3px 3px;margin-right:2px;height:<?php echo $detailHeight;?>px;width:<?php echo $detailWidth;?>px;min-height:16px;overflow:auto;"
              class="input"><?php echo $note->note;?></div>
        <?php }?>
          <table width="100%"><tr height="25px">
            <td width="33%" class="smallTabLabel" >
              <label class="smallTabLabelRight" for="notePrivacyPublic"><?php echo i18n('public');?>&nbsp;</label>
              <input type="radio" data-dojo-type="dijit/form/RadioButton" name="notePrivacy" id="notePrivacyPublic" value="1" <?php if ($note->idPrivacy==1) echo "checked";?> />
            </td>
            <td width="34%" class="smallTabLabel" >
              <label class="smallTabLabelRight" for="notePrivacyTeam"><?php echo i18n('team');?>&nbsp;</label>
              <?php $res=new Resource(getSessionUser()->id);
                    $hasTeam=($res->id and $res->idTeam)?true:false;
              ?>
              <input type="radio" data-dojo-type="dijit/form/RadioButton" name="notePrivacy" id="notePrivacyTeam" value="2" <?php if ($note->idPrivacy==2) echo "checked"; if (!$hasTeam) echo ' disabled ';?> />
            </td>
            <td width="33%" class="smallTabLabel" >
              <label class="smallTabLabelRight" for="notePrivacyPrivate"><?php echo i18n('private');?>&nbsp;</label>
              <input type="radio" data-dojo-type="dijit/form/RadioButton" name="notePrivacy" id="notePrivacyPrivate" value="3" <?php if ($note->idPrivacy==3) echo "checked";?> />
            </td>
          </tr></table>

       </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="dialogNoteAction">
        <button class="mediumTextButton"  dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogNote').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button class="mediumTextButton"  id="dialogNoteSubmit" dojoType="dijit.form.Button" type="submit" onclick="protectDblClick(this);saveNote();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>
</div>