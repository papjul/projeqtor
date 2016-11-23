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
  scriptLog('   ->/view/objectButton.php'); 

  if (! isset($comboDetail)) {
    $comboDetail=false;
  }
  $id=null;
  $class=$_REQUEST['objectClass'];
  Security::checkValidClass($class);
  if (array_key_exists('objectId',$_REQUEST)) {
  	$id=$_REQUEST['objectId'];
  }	
  $obj=new $class($id);
  if (isset($_REQUEST['noselect'])) {
  	$noselect=true;
  }
  if (! isset($noselect)) {
  	$noselect=false;
  }
  $printPage="objectDetail.php";
  if (file_exists('../report/object/'.$class.'.php')) {
  	$printPage='../report/object/'.$class.'.php';
  }
  $createRight=securityGetAccessRightYesNo('menu' . $class, 'create');
  if (!$obj->id) {
    $updateRight=$createRight;
  } else {
    $updateRight=securityGetAccessRightYesNo('menu' . $class, 'update', $obj);
  }
  $updateRight='YES';
  $deleteRight=securityGetAccessRightYesNo('menu' . $class, 'delete', $obj);
?>
<table style="width:100%;height:100%;">
 <tr style="height:100%";>
  <td style="z-index:-1;width:40%;white-space:nowrap;">  
    <div style="width:100%;height:100%;">
      <table style="width:100%;height:100%;">
        <tr>
          <td style="width:43px;height:100%;">&nbsp;
            <div style="position:absolute;left:0px;width:43px;top:0px;height:36px;" class="iconHighlight">&nbsp;</div>
            <div style="position:absolute; top:0px;left:5px ;" class="icon<?php echo ((SqlElement::is_subclass_of($class, 'PlgCustomList'))?'ListOfValues':$class);?>32" style="margin-left:9px;width:32px;height:32px" /></div>
          </td>
          <td class="title" style="width:10%;">
            &nbsp;<?php echo i18n($_REQUEST['objectClass']);?><span id="buttonDivObjectId"><?php echo ($obj->id)?'&nbsp;#'.$obj->id:'';?>&nbsp;</span>
          </td>
          <td class="title" style="height:100%;">
          
            <div style="width:100%;height:100%;position:relative;">
            <div id="buttonDivObjectName" style="width:100%;position:absolute;top:8px;text-overflow:ellipsis;overflow:hidden;">
                 <?php  if (property_exists($obj,'name')){ echo ($obj->name)?'&nbsp;-&nbsp;'.$obj->name:''; }?>
            </div>
          </td>
        </tr>
      </table>  
    </div> 
  </td>     
  <td style="width:8%; text-align:right;"  >
      <div style="width:120px;margin-right:16px;" id="buttonDivCreationInfo"><?php include_once '../tool/getObjectCreationInfo.php';?></div>
  </td>
  <td style="width:2%;">
    &nbsp;
  </td>
  <td  style="white-space:nowrap;">
    <div style="float:left;position:50%;width:45%;white-space:nowrap"> 
    <?php if (! $comboDetail ) {?>
      <button id="newButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonNew', array(i18n($_REQUEST['objectClass'])));?>"
       iconClass="dijitButtonIcon dijitButtonIconNew" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
		  dojo.byId("newButton").blur();
          id=dojo.byId('objectId');
	      if (id) { 	
		    id.value="";
		    unselectAllRows("objectGrid");
            loadContent("objectDetail.php", "detailDiv", dojo.byId('listForm'));
          } else { 
            showError(i18n("errorObjectId"));
	      }
        </script>
      </button>
      <button id="saveButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonSave', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect) {echo "disabled";} ?>
       iconClass="dijitButtonIcon dijitButtonIconSave" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
		      saveObject();
        </script>
      </button>
      <button id="printButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonPrint', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect) {echo "disabled";} ?> 
       iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
		    dojo.byId("printButton").blur();
        if (dojo.byId("printPdfButton")) {dojo.byId("printPdfButton").blur();}
        showPrint("<?php echo $printPage;?>", null, null, null, 'P');
        </script>
      </button>  
<?php if ($_REQUEST['objectClass']!='Workflow' and $_REQUEST['objectClass']!='Mail') {?>    
     <button id="printButtonPdf" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('reportPrintPdf');?>"
       <?php if ($noselect) {echo "disabled";} ?> 
       iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
        dojo.byId("printButton").blur();
        if (dojo.byId("printPdfButton")) {dojo.byId("printPdfButton").blur();}
        showPrint("<?php echo $printPage;?>", null, null, 'pdf', 'P');
        </script>
      </button>   
<?php } 
      if (! (property_exists($_REQUEST['objectClass'], '_noCopy')) ) { ?>
      <button id="copyButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonCopy', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect) {echo "disabled";} ?>
       iconClass="dijitButtonIcon dijitButtonIconCopy" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          <?php 
          $crit=array('name'=> $_REQUEST['objectClass']);
          $paramCopy="copyProject";
          if($_REQUEST['objectClass'] != "Project"){
            $copyable=SqlElement::getSingleSqlElementFromCriteria('Copyable', $crit);
            if ($copyable->id) {
              $paramCopy="copyObjectTo";
              echo "copyObjectBox('$paramCopy');";
            }else{
              echo "copyObject('" .$_REQUEST['objectClass'] . "');";
            }
          }else{
            echo "copyObjectBox('$paramCopy');";
          }
          /*if ( $_REQUEST['objectClass'] == "Project") {
            echo "copyProject();";
          } else {
            $copyable=SqlElement::getSingleSqlElementFromCriteria('Copyable', $crit);
	          if ($copyable->id) {
	            echo "copyObjectTo('" . $_REQUEST['objectClass'] . "');";
	          } else {
	            echo "copyObject('" .$_REQUEST['objectClass'] . "');";
	          }
          }*/
          ?>
        </script>
      </button>    
<?php }?>
      <button id="undoButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonUndo', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect or 1) {echo "disabled";} ?>
       iconClass="dijitButtonIcon dijitButtonIconUndo" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          dojo.byId("undoButton").blur();
          loadContent("objectDetail.php", "detailDiv", 'listForm');
          formChangeInProgress=false;
        </script>
      </button>    
      <button id="deleteButton" dojoType="dijit.form.Button" showlabel="false" 
       title="<?php echo i18n('buttonDelete', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect) {echo "disabled";} ?> 
       iconClass="dijitButtonIcon dijitButtonIconDelete" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          dojo.byId("deleteButton").blur();
		      action=function(){
            //unselectAllRows('objectGrid');
		        loadContent("../tool/deleteObject.php", "resultDiv", 'objectForm', true);
          };
          var alsoDelete="";
		      //if (dojo.byId('nbAttachments')) {
          //  if (dojo.byId('nbAttachments').value>0) {
          //    alsoDelete+="<br/><br/>" + i18n('alsoDeleteAttachment', new Array(dojo.byId('nbAttachments').value) );
          //  }
          //}
          showConfirm(i18n("confirmDelete", new Array("<?php echo i18n($_REQUEST['objectClass']);?>",dojo.byId('id').value))+alsoDelete ,action);
        </script>
      </button>    
     <button id="refreshButton" dojoType="dijit.form.Button" showlabel="false" 
       title="<?php echo i18n('buttonRefresh', array(i18n($_REQUEST['objectClass'])));?>"
       <?php if ($noselect) {echo "disabled";} ?> 
       iconClass="dijitButtonIcon dijitButtonIconRefresh" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          dojo.byId("refreshButton").blur();
          loadContent("objectDetail.php", "detailDiv", 'listForm');
        </script>
      </button>    
    <?php 
    $clsObj=get_class($obj);
    if ($clsObj=='TicketSimple') {$clsObj='Ticket';}
    $mailable=SqlElement::getSingleSqlElementFromCriteria('Mailable', array('name'=>$clsObj));
    if ($mailable and $mailable->id) {
    ?>
     <button id="mailButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonMail', array(i18n($clsObj)));?>"
       <?php if ($noselect) {echo "disabled";} ?>
       iconClass="dijitButtonIcon dijitButtonIconEmail" class="detailButton" >
        <script type="dojo/connect" event="onClick" args="evt">
          showMailOptions();  
        </script>
      </button>
    <?php 
    if (! array_key_exists('planning',$_REQUEST)) {?> 
    <span id="multiUpdateButtonDiv" >
    <button id="multiUpdateButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonMultiUpdate');?>"
       iconClass="dijitButtonIcon dijitButtonIconMultipleUpdate" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          startMultipleUpdateMode('<?php echo get_class($obj);?>');  
        </script>
    </button>
    </span>
    <?php }
    //if (array_key_exists('planning',$_REQUEST) and array_key_exists('planningType',$_REQUEST) and $_REQUEST['planningType']=='Planning') {
    ?> 
    <span id="indentButtonDiv">
     <button id="indentDecreaseButton" dojoType="dijit.form.Button" showlabel="false"
        title="<?php echo i18n('indentDecreaseButton');?>"
        iconClass="dijitButtonIcon dijitButtonIconDecrease" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          indentTask("decrease");  
        </script>
      </button>
      <button id="indentIncreaseButton" dojoType="dijit.form.Button" showlabel="false"
        title="<?php echo i18n('indentIncreaseButton');?>"
        iconClass="dijitButtonIcon dijitButtonIconIncrease" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          indentTask("increase");  
        </script>
      </button>
    </span>
    <?php }?> 
    <?php 
      $crit="nameChecklistable='".get_class($obj)."'";
      $type='id'.get_class($obj).'Type';
      if (property_exists($obj,$type) ) {
        $crit.=' and (idType is null ';
        if ($obj->$type) {
          $crit.=" or idType='".$obj->$type."'";
        }
        $crit.=')';
  		}
  		$cd=new ChecklistDefinition();
  		$cdList=$cd->getSqlElementsFromCriteria(null,false,$crit);
  		$user=getSessionUser();
  		$habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile'=>$user->getProfile($obj),'scope'=>'checklist'));
  		$list=new ListYesNo($habil->rightAccess);
  		$displayChecklist=Parameter::getUserParameter('displayChecklist');
  		if ($list->code!='YES' or $displayChecklist!='REQ') {
  		  $buttonCheckListVisible="never";
  		} else if (count($cdList)>0 and $obj->id) {
        $buttonCheckListVisible="visible";
      } else {
        $buttonCheckListVisible="hidden";
      }
      //$displayButton=( $buttonCheckListVisible=="visible")?'void':'none';?>
      
    <span id="checkListButtonDiv" style="display:<?php echo ($buttonCheckListVisible=='visible')?'inline':'none';?>;">
      <?php if ($buttonCheckListVisible!="never") {?>
      <button id="checkListButton" dojoType="dijit.form.Button" showlabel="false"
        title="<?php echo i18n('Checklist');?>"
        iconClass="dijitButtonIcon dijitButtonIconChecklist" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          showChecklist('<?php echo get_class($obj);?>');  
        </script>
      </button>
      <?php }?>
      <input type="hidden" id="buttonCheckListVisible" value="<?php echo $buttonCheckListVisible;?>" />
    </span>
    
    <?php $buttonHistoryVisible=true; 
      $paramHistoryVisible=Parameter::getUserParameter('displayHistory');
      if ($paramHistoryVisible and $paramHistoryVisible!='REQ') {
        $buttonHistoryVisible=false;
      }
      if (!$obj->id) $buttonHistoryVisible=false;
    ?>
    <span id="historyButtonDiv" style="display:<?php echo ($buttonHistoryVisible)?'inline':'none';?>;">
      <?php if ($paramHistoryVisible=='REQ') {?>
      <button id="historyButton" dojoType="dijit.form.Button" showlabel="false"
        title="<?php echo i18n('showHistory');?>"
        iconClass="dijitButtonIcon dijitButtonIconHistory" class="detailButton">
        <script type="dojo/connect" event="onClick" args="evt">
          showHistory('<?php echo get_class($obj);?>');  
        </script>
      </button>
      <?php }?>
      <input type="hidden" id="buttonHistoryVisible" value="<?php echo $paramHistoryVisible;?>" />
    </span>
    
    <?php
        }
      ?>
      <input type="hidden" id="createRight" name="createRight" value="<?php echo $createRight;?>" />
      <input type="hidden" id="updateRight" name="updateRight" value="<?php echo (!$obj->id)?$createRight:$updateRight;?>" />
      <input type="hidden" id="deleteRight" name="deleteRight" value="<?php echo $deleteRight;?>" />
       <?php $isAttachmentEnabled = true; // allow attachment
    		if (! Parameter::getGlobalParameter ( 'paramAttachmentDirectory' ) or ! Parameter::getGlobalParameter ( 'paramAttachmentMaxSize' )) {
    			$isAttachmentEnabled = false;
    		} 
       if ($isAttachmentEnabled and property_exists($obj,'_Attachment') and $updateRight=='YES' and isHtml5() and ! $readOnly ) {?>
			<span id="attachmentFileDirectDiv" style="position:relative;<?php echo (!$obj->id or $comboDetail)?'visibility:hidden;':'';?>">
		  <?php if (isHtml5()) {?>	
			<div dojoType="dojox.form.Uploader" type="file" id="attachmentFileDirect" name="attachmentFile" 
			MAX_FILE_SIZE="<?php echo Parameter::getGlobalParameter('paramAttachmentMaxSize');?>"
			url="../tool/saveAttachment.php?attachmentRefType=<?php echo get_class($obj);?>&attachmentRefId=<?php echo $obj->id;?>"
			multiple="true" class="directAttachment" 			
			uploadOnSelect="true"
			target="resultPost"
			onBegin="saveAttachment(true);"
			onError="dojo.style(dojo.byId('downloadProgress'), {display:'none'});"
			style="font-size:60%;height:21px; width:100px; border-radius: 5px; border: 1px dashed #EEEEEE; padding:1px 7px 5px 1px; color: #000000;
			 text-align: center; vertical-align:middle;font-size: 7pt; background-color: #FFFFFF; opacity: 0.8;z-index:9999"
			label="<?php echo i18n("Attachment");?><br/><i>(<?php echo i18n("dragAndDrop");?>)</i>">		 
			  <script type="dojo/connect" event="onComplete" args="dataArray">
          saveAttachmentAck(dataArray);
	      </script>
				<script type="dojo/connect" event="onProgress" args="data">
          saveAttachmentProgress(data);
	      </script>
			</div>
			<?php }?>
			</span>
  </div>
     
<?php }?>
  </td>
  </tr>
</table>