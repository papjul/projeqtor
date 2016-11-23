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
 * Presents the action buttons of an object.
 * 
 */ 
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/objectMultipleUpdate.php');

  $displayWidth='98%';
  if (array_key_exists('destinationWidth',$_REQUEST)) {
    $width=$_REQUEST['destinationWidth'];
    $displayWidth=floor($width*0.6);
    $labelWidth=250;
    $fieldWidth=$displayWidth-$labelWidth-15-15;
  } 
  $objectClass=$_REQUEST['objectClass'];
  Security::checkValidClass($objectClass);
  $obj=new $objectClass();
?>
<div dojoType="dijit.layout.BorderContainer" class="background">
  <div id="buttonDiv" dojoType="dijit.layout.ContentPane" region="top">
    <div dojoType="dijit.layout.BorderContainer" >
      <div id="buttonDivContainer" dojoType="dijit.layout.ContentPane" region="left">
        <table width="100%" class="listTitle" >
          <tr valign="middle" height="32px"> 
            <td width="50px" align="center" >
              <img style="position: absolute; top: 0px; left: 0px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
              <img style="position: absolute; top: 5px; left: 5px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
              <img style="position: absolute; top: 10px; left: 10px" src="css/images/icon<?php echo $objectClass;?>22.png" width="22" height="22" />
            </td>
            <td valign="middle"><span class="title"><?php echo i18n('labelMultipleMode');?></span></td>
            <td width="15px">&nbsp;</td>
            <td><span class="nobr">
             <button id="selectAllButton" dojoType="dijit.form.Button" showlabel="false" 
               title="<?php echo i18n('buttonSelectAll');?>"
               iconClass="iconSelectAll" class="detailButton" >
                <script type="dojo/connect" event="onClick" args="evt">
                   selectAllRows('objectGrid');
                   updateSelectedCountMultiple();
                </script>
              </button>    
              <button id="unselectAllButton" dojoType="dijit.form.Button" showlabel="false" 
               title="<?php echo i18n('buttonUnselectAll');?>"
               iconClass="iconUnselectAll" class="detailButton" >
                <script type="dojo/connect" event="onClick" args="evt">
                   unselectAllRows('objectGrid');
                   updateSelectedCountMultiple();
                </script>
              </button>    
              <button id="saveButtonMultiple" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonSaveMultiple');?>"
               iconClass="dijitButtonIcon dijitButtonIconSave" class="detailButton" >
                <script type="dojo/connect" event="onClick" args="evt">
                  saveMultipleUpdateMode("<?php echo $objectClass;?>");  
                </script>
              </button>
              <button id="deleteButtonMultiple" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonDeleteMultiple');?>"
               iconClass="dijitButtonIcon dijitButtonIconDelete" class="detailButton" >
                <script type="dojo/connect" event="onClick" args="evt">
                  deleteMultipleUpdateMode("<?php echo $objectClass;?>");  
                </script>
              </button>
              <button id="undoButtonMultiple" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonQuitMultiple');?>"
               iconClass="dijitButtonIcon dijitButtonIconExit" class="detailButton" >
                <script type="dojo/connect" event="onClick" args="evt">
                  dojo.byId("undoButtonMultiple").blur();
                  endMultipleUpdateMode("<?php echo $objectClass;?>");
                </script>
              </button>    
            </span></td>
            <td>&nbsp;&nbsp;&nbsp;</td>
            <td>
              <?php echo i18n("selectedItemsCount");?> :
              <input type="text" id="selectedCount"
                style="font-weight: bold;background: transparent;border: 0px;color: white;" 
                value="0" readOnly />
            </td>
          </tr>
        </table>
      </div>
      <div dojoType="dijit.layout.ContentPane" region="center" 
       style="z-index: 3; height: 35px; position: absolute !important; overflow: visible !important;">
      </div>
    </div>
  </div>
  <div id="resultDiv" style="left: 60% ! important; width: 40%;">
  </div>
  <div dojoType="dijit.layout.ContentPane" region="center">
    <div dojoType="dijit.layout.BorderContainer" class="background">
      <div dojoType="dijit.layout.ContentPane" region="center" style="overflow-y: auto">
        <form dojoType="dijit.form.Form" id="objectFormMultiple" jsId="objectFormMultiple" 
          name="objectFormMultiple" encType="multipart/form-data" action="" method="">
          <script type="dojo/method" event="onSubmit">
            return false;        
          </script>
          <input type="hidden" id="selection" name="selection" value=""/>
     <?php $displayWidth=($labelWidth+$fieldWidth+15)."px"; 
     $collapsedList=Collapsed::getCollaspedList();
     $titlePane=get_class($obj)."_MultipleDescription";?>
     <br/>
     <div style="width: <?php echo $displayWidth;?>" dojoType="dijit.TitlePane" 
     title="<?php echo i18n('sectionDescription');?>"
     open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
     id="<?php echo $titlePane;?>" 
     onHide="saveCollapsed('<?php echo $titlePane;?>');"
     onShow="saveExpanded('<?php echo $titlePane;?>');" >
          <table>
            <?php
      // Project
             if (isDisplayable($obj,'idProject')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeProject',array($obj->getColCaption('idProject')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idProject" name="idProject">
                 <?php htmlDrawOptionForReference('idProject', null, null, false);?>
                </select>
                <button id="projectButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idProject",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
      // Type
            $type='id'.get_class($obj).'Type';
            if (isDisplayable($obj,$type)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeType');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idType" name="idType">
                 <?php htmlDrawOptionForReference($type, null, null, false);?>
                </select>
                <button id="typeButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail($type,0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
      // Issuer
            if (isDisplayable($obj,'idUser')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeIssuer',array($obj->getColCaption('idUser')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idUser" name="idUser">
                 <?php htmlDrawOptionForReference('idUser', null, null, false);?>
                </select>
                <button id="userButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idUser",0); 
                  </script>
                </button>
              </td>
            </tr>
             <?php }
      // Requestor
             if (isDisplayable($obj,'idContact')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeRequestor',array($obj->getColCaption('idContact')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idContact" name="idContact">
                 <?php htmlDrawOptionForReference('idContact', null, null, false);?>
                </select>
                <button id="contactButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idContact",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
       // fix planning, under construction
             $arrayCheckbox=array("fixPlanning","isUnderConstruction");
             foreach($arrayCheckbox as $checkField) {
             if (isDisplayable($obj,$checkField)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeRequestor',array($obj->getColCaption($checkField)));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="<?php echo $checkField;?>" name="<?php echo $checkField;?>">
                 <option value=""> </option>
                 <option value="ON"><?php echo i18n("checked");?></option>
                 <option value="OFF"><?php echo i18n("unchecked");?></option>
                </select>
              </td>
            </tr>
            <?php }
                }
      // Description 
            if (isDisplayable($obj, 'description') ) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddToDescription');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="description" id="description"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }?>
        </table>
     </div>
     <?php $titlePane=get_class($obj)."_MultipleResult";?>
     <br/>
     <div style="width: <?php echo $displayWidth;?>" dojoType="dijit.TitlePane" 
     title="<?php echo i18n('sectionTreatment');?>"
     open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
     id="<?php echo $titlePane;?>" 
     onHide="saveCollapsed('<?php echo $titlePane;?>');"
     onShow="saveExpanded('<?php echo $titlePane;?>');" >
          <table>
            <?php 
      // Status      
            if (isDisplayable($obj,'idActivity')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo (SqlElement::is_a($obj,'Ticket'))?i18n('colChangePlanningActivity'):i18n('colChangeParentActivity');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idActivity" name="idActivity">
                 <?php htmlDrawOptionForReference('idActivity', null, null, false);?>
                </select>
                <button id="activityButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                                showDetail("idActivity",0); 
                              </script>
                </button>
              </td>
            </tr>
            <?php } 
            if (isDisplayable($obj,'idStatus')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeStatus');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idStatus" name="idStatus">
                 <?php htmlDrawOptionForReference('idStatus', null, null, false);?>
                </select>
                <button id="statusButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idStatus",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
      // Responsable
            if (isDisplayable($obj,'idResource')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeResponsible',array($obj->getColCaption('idResource')));?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idResource" name="idResource">
                 <?php htmlDrawOptionForReference('idResource', null, null, false);?>
                </select>
                <button id="responsibleButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idResource",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
      // Target Version
             if (isDisplayable($obj,'idTargetVersion')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeTargetVersion');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idTargetVersion" name="idTargetVersion">
                 <?php htmlDrawOptionForReference('idTargetVersion', null, null, false);?>
                </select>
                <button id="targetVersionButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idTargetVersion",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
         // Product Target Version
             if (isDisplayable($obj,'idTargetProductVersion')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeTargetVersion');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth-25;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="idTargetProductVersion" name="idTargetProductVersion">
                 <?php htmlDrawOptionForReference('idTargetProductVersion', null, null, false);?>
                </select>
                <button id="targetProductVersionButton" dojoType="dijit.form.Button" showlabel="false"
                  title="<?php echo i18n('showDetail');?>" iconClass="iconView">
                  <script type="dojo/connect" event="onClick" args="evt">
                    showDetail("idTargetProductVersion",0); 
                  </script>
                </button>
              </td>
            </tr>
            <?php }
       // Initial Due Date
            if (isDisplayable($obj,'initialDueDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialDueDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialDueDate" id="initialDueDate"
                	<?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Actual due date
            if (isDisplayable($obj,'actualDueDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualDueDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualDueDate" id="actualDueDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php } 
      // Initial End Date
						if (isDisplayable($obj,'initialEndDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialEndDate" id="initialEndDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Actual End Date
            if (isDisplayable($obj,'actualEndDate')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualEndDate" id="actualEndDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Initial Due DateTime 
            if (isDisplayable($obj,'initialDueDateTime')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeInitialDueDateTime');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="initialDueDate" id="initialDueDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
                <div dojoType="dijit.form.TimeTextBox" name="initialDueTime" id="initialDueTime"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Actual Due Datetime
            if (isDisplayable($obj,'actualDueDateTime')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeActualDueDateTime');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="actualDueDate" id="actualDueDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
                <div dojoType="dijit.form.TimeTextBox" name="actualDueTime" id="actualDueTime"
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Validate Start Date
            $pe=get_class($obj).'PlanningElement';
            if (isDisplayable($obj,'validatedStartDate', true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeValidatedStartDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="<?php echo $pe;?>_validatedStartDate" id="<?php echo $pe;?>_validatedStartDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Validated End Date
            $pe=get_class($obj).'PlanningElement';
            if (isDisplayable($obj,'validatedEndDate', true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changeValidatedEndDate');?>&nbsp;:&nbsp;</td>
              <td>
                <div dojoType="dijit.form.DateTextBox" name="<?php echo $pe;?>_validatedEndDate" id="<?php echo $pe;?>_validatedEndDate"
                <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                 style="width:100px;" class="input" value="" ></div>
              </td>
            </tr>
            <?php }
      // Planning Mode
            $pe=get_class($obj).'PlanningElement';
            $pm='id'.get_class($obj).'PlanningMode';
            if (isDisplayable($obj,$pm, true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('changePlanningMode');?>&nbsp;:&nbsp;</td>
              <td>
                <select dojoType="dijit.form.FilteringSelect" class="input" style="width:<?php echo $fieldWidth;?>px;" 
                <?php echo autoOpenFilteringSelect();?>
                 id="<?php echo $pe.'_'.$pm;?>" name="<?php echo $pe.'_'.$pm;?>">
                 <?php htmlDrawOptionForReference($pm, null, null, false);?>
                </select>
              </td>
            </tr>
            <?php }
      // Priority
            $pe=get_class($obj).'PlanningElement';
            $pm='id'.get_class($obj).'PlanningMode';
            if (isDisplayable($obj,'priority', true)) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colChangeRequestor',array(i18n('colPriority')));?>&nbsp;:&nbsp;</td>
              <td>
                <input dojoType="dijit.form.TextBox" class="input" style="width:<?php echo $fieldWidth;?>px;" 
                 id="<?php echo $pe.'_priority';?>" name="<?php echo $pe.'_priority';?>" value="">
                </select>
              </td>
            </tr>
            <?php }
       // result
            if (isDisplayable($obj,'result')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddToResult');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="result" id="result"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }?>
          </table>
      </div>
      <?php $titlePane=get_class($obj)."_MultipleOthers";?>
      <br/>
      <div style="width: <?php echo $displayWidth;?>" dojoType="dijit.TitlePane" 
     title="<?php echo i18n('sectionMiscellaneous');?>"
     open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
     id="<?php echo $titlePane;?>" 
     onHide="saveCollapsed('<?php echo $titlePane;?>');"
     onShow="saveExpanded('<?php echo $titlePane;?>');" >
          <table>
            <?php
      // Notes
            if (isDisplayable($obj,'_Note')) {?>
            <tr class="detail">
              <td class="label" style="width:<?php echo $labelWidth;?>px;"><?php echo i18n('colAddNote');?>&nbsp;:&nbsp;</td>
              <td>
                <textarea dojoType="dijit.form.Textarea" name="note" id="note"
                 rows="2" style="width:<?php echo $fieldWidth;?>px;" maxlength="4000" maxSize="4" class="input" ></textarea>
              </td>
            </tr>
            <?php }?>
          </table>
          </div>
        </form>
      </div>
      <div dojoType="dijit.layout.ContentPane" id="resultDivMultiple" region="right" class="listTitle" style="width:38%"></div>
    </div>
  </div> 
</div>

<?php 
function isDisplayable($obj, $field, $fromPlanningElement=false) {
  if ( property_exists($obj,$field) 
  and ! $obj->isAttributeSetToField($field,'readonly') 
  and ! $obj->isAttributeSetToField($field,'hidden') ) {
    return true;
  } else {
    $pe=get_class($obj).'PlanningElement';
    if ($fromPlanningElement and property_exists($obj,$pe) and is_object($obj->$pe) and property_exists($obj->$pe,$field)) {
      $peObj=$obj->$pe;
      if (! $peObj->isAttributeSetToField($field,'readonly')
      and ! $peObj->isAttributeSetToField($field,'hidden') ) {
        return true;
      } else {
        return false;
      }      
    } else {
      return false;
    }
  }         
}
?>