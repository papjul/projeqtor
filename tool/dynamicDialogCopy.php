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
if (! array_key_exists('copyType',$_REQUEST)) {
  throwError('Parameter copyType not found in REQUEST');
}
$copyType=$_REQUEST['copyType'];
if($copyType!='copyObjectTo' && $copyType!='copyProject'){
  traceHack('dynamicDialogCopy: $copyType contains an unexpected valid value');
} 
$idClass=SqlList::getIdFromTranslatableName('Copyable', $objectClass);
$toCopy=new $objectClass($objectId);
if($copyType=="copyObjectTo"){
  
?>
  <table>
    <tr>
      <td>
       <form dojoType="dijit.form.Form" id='copyForm' name='copyForm' onSubmit="return false;">
         <input id="copyClass" name="copyClass" type="hidden" value="" />
         <input id="copyId" name="copyId" type="hidden" value="" />
         <table>
           <tr>
             <td class="dialogLabel"  >
               <label for="copyToClass" ><?php echo i18n("copyToClass") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="copyToClass" name="copyToClass" required
                class="input" >
                 <?php htmlDrawOptionForReference('idCopyable', $idClass, null, true);?>
                 <script type="dojo/connect" event="onChange" args="evt" >
                   var objclass=copyableArray[this.value];
                   dijit.byId('copyToType').set('value',null);
                   //dijit.byId('copyToType').reset();
                   var idProject=(dijit.byId('idProject'))?dijit.byId('idProject').get('value'):null;
                   refreshList("id"+objclass+"Type","idProject", idProject, null,'copyToType',true);
                   /*if (dojo.byId('copyClass').value==objclass) {
                     var runModif="dijit.byId('copyToType').set('value',dijit.byId('id"+objclass+"Type').get('value'))";
                     setTimeout(runModif,1);
                   }*/
                   copyObjectToShowStructure();
                 </script> 
               </select>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel"  >
               <label for="copyToType" ><?php echo i18n("copyToType") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="copyToType" name="copyToType" required
                class="input">
                <?php $colName='id'.$objectClass.'Type';
                      htmlDrawOptionForReference($colName, $toCopy->$colName, null, true);?>
               </select>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel" >
               <label for="copyToName" ><?php echo i18n("copyToName") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <div id="copyToName" name="copyToName" dojoType="dijit.form.ValidationTextBox"
                required="required"
                style="width: 400px;"
                trim="true" maxlength="100" class="input"
                value="<?php echo $toCopy->name;?>">
               </div>     
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <div id="copyWithStructureDiv" style="display:none;">
	               <label for="copyWithStructure" style="width:90%;text-align: right;"><?php echo i18n("copyWithStructure") ?>&nbsp;:&nbsp;</label>
	               <div id="copyWithStructure" name="copyWithStructure" dojoType="dijit.form.CheckBox" type="checkbox" 
	                checked >
	               </div>
	               <br />
                 <label for="copyWithAssignments" style="width:90%;text-align: right;"><?php echo i18n("copyAssignments") ?>&nbsp;:&nbsp;</label>
                 <div id="copyWithAssignments" name="copyWithAssignments" dojoType="dijit.form.CheckBox" type="checkbox" 
                   >
                 </div>
              </div>
             </td>
           </tr>
           
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToOrigin" style="width:90%;text-align: right;"><?php echo i18n("copyToOrigin") ?>&nbsp;:&nbsp;</label>
               <div id="copyToOrigin" name="copyToOrigin" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToLinkOrigin" style="width:90%;text-align: right;"><?php echo i18n("copyToLinkOrigin") ?>&nbsp;:&nbsp;</label>
               <div id="copyToLinkOrigin" name="copyToLinkOrigin" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToWithLinks" style="width:90%;text-align: right;"><?php echo i18n("copyToWithLinks") ?>&nbsp;:&nbsp;</label>
               <div id="copyToWithLinks" name="copyToWithLinks" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToWithAttachments" style="width:90%;text-align: right;"><?php echo i18n("copyToWithAttachments") ?>&nbsp;:&nbsp;</label>
               <div id="copyToWithAttachments" name="copyToWithAttachments" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToWithNotes" style="width:90%;text-align: right;"><?php echo i18n("copyToWithNotes") ?>&nbsp;:&nbsp;</label>
               <div id="copyToWithNotes" name="copyToWithNotes" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>     
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyToWithResult" style="width:90%;text-align: right;"><?php echo i18n("copyToWithResult") ?>&nbsp;:&nbsp;</label>
               <div id="copyToWithResult" name="copyToWithResult" dojoType="dijit.form.CheckBox" type="checkbox">
               </div>
             </td>
           </tr>    
           <tr><td>&nbsp;</td><td >&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="copyAction">
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogCopy').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogCopySubmit" onclick="protectDblClick(this);copyObjectToSubmit();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>
<?php 
}else if($copyType=="copyProject"){
?>
<table>
    <tr>
      <td>
       <form dojoType="dijit.form.Form" id='copyProjectForm' name='copyProjectForm' onSubmit="return false;">
         <input id="copyProjectId" name="copyProjectId" type="hidden" value="" />
         <table>
           <tr>
             <td class="dialogLabel"  >
               <label for="copyProjectToType" ><?php echo i18n("colProjectType") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="copyProjectToType" name="copyProjectToType" required
                class="input" value="" >
                <?php htmlDrawOptionForReference('idProjectType', null, null, true);?>
               </select>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel" >
               <label for="copyProjectToName" ><?php echo i18n("copyToName") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <div id="copyProjectToName" name="copyProjectToName" dojoType="dijit.form.ValidationTextBox"
                required="required"
                style="width: 400px;"
                trim="true" maxlength="100" class="input"
                value="">
               </div>     
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel" >
               <label for="copyProjectToName" ><?php echo i18n("colProjectCode") ?>&nbsp;:&nbsp;</label>
             </td>
             <td>
               <div id="copyProjectToProjectCode" name="copyProjectToProjectCode" dojoType="dijit.form.ValidationTextBox"
                style="width: 400px;"
                trim="true" maxlength="100" class="input"
                value="">
               </div>     
             </td>
           </tr>
           <tr><td>&nbsp;</td><td>&nbsp;</td></tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyProjectStructure" style="width:90%;text-align: right;"><?php echo i18n("copyProjectStructure") ?>&nbsp;:&nbsp;</label>
               <div id="copyProjectStructure" name="copyProjectStructure" dojoType="dijit.form.CheckBox" type="checkbox" 
                onChange="copyProjectStructureChange()" checked >
               </div>
             </td>
           </tr>
           <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copySubProjects" style="width:90%;text-align: right;"><?php echo i18n("copySubProjects") ?>&nbsp;:&nbsp;</label>
               <div id="copySubProjects" name="copySubProjects" dojoType="dijit.form.CheckBox" type="checkbox" 
                checked >
               </div>
             </td>
           </tr>
            <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyProjectAffectations" style="width:90%;text-align: right;"><?php echo i18n("copyProjectAffectations") ?>&nbsp;:&nbsp;</label>
               <div id="copyProjectAffectations" name="copyProjectAffectations" dojoType="dijit.form.CheckBox" type="checkbox" 
                 >
               </div>
             </td>
           </tr>
            </tr>
            <tr>
             <td class="dialogLabel" colspan="2" style="width:100%; text-align: left;">
               <label for="copyProjectAssignments" style="width:90%;text-align: right;"><?php echo i18n("copyAssignments") ?>&nbsp;:&nbsp;</label>
               <div id="copyProjectAssignments" name="copyProjectAssignments" dojoType="dijit.form.CheckBox" type="checkbox" 
                 >
               </div>
             </td>
           </tr>
           <tr><td>&nbsp;</td><td >&nbsp;</td></tr>
         </table>
        </form>
      </td>
    </tr>
    <tr>
      <td align="center">
        <input type="hidden" id="copyProjectAction">
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogCopy').hide();">
          <?php echo i18n("buttonCancel");?>
        </button>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogProjectCopySubmit" onclick="protectDblClick(this);copyProjectToSubmit();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
      </td>
    </tr>
  </table>
<?php 
}
?>