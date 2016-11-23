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
 * Presents the list of objects of a given class.
 *
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/objectList.php');

if (! isset($comboDetail)) {
  $comboDetail=false;
}
$objectClass=$_REQUEST['objectClass'];
Security::checkValidClass($objectClass);
$objectType='';
if (array_key_exists('objectType',$_REQUEST)) {
  $objectType=$_REQUEST['objectType'];
}
$objectClient='';
if (array_key_exists('objectClient',$_REQUEST)) {
  $objectClient=$_REQUEST['objectClient'];
}
$objectElementable='';
if (array_key_exists('objectElementable',$_REQUEST)) {
  $objectElementable=$_REQUEST['objectElementable'];
}
$obj=new $objectClass;

if (array_key_exists('Directory', $_REQUEST)) {
	$_SESSION['Directory']=$_REQUEST['Directory'];
} else {
	unset($_SESSION['Directory']);
}
$multipleSelect=false;
if (array_key_exists('multipleSelect', $_REQUEST)) {
	if ($_REQUEST['multipleSelect']) {
		$multipleSelect=true;
	}
}
$showIdle=(! $comboDetail and isset($_SESSION['projectSelectorShowIdle']) and $_SESSION['projectSelectorShowIdle']==1)?1:0;
if (! $comboDetail and is_array( getSessionUser()->_arrayFilters)) {
  if (array_key_exists($objectClass, getSessionUser()->_arrayFilters)) {
    $arrayFilter=getSessionUser()->_arrayFilters[$objectClass];
    foreach ($arrayFilter as $filter) {
      if ($filter['sql']['attribute']=='idle' and $filter['sql']['operator']=='>=' and $filter['sql']['value']=='0') {
        $showIdle=1;
      }
    }
  }
} 
?>
<div dojoType="dojo.data.ItemFileReadStore" id="objectStore" jsId="objectStore" clearOnClose="true"
  url="../tool/jsonQuery.php?objectClass=<?php echo $objectClass;?><?php echo ($comboDetail)?'&comboDetail=true':'';?><?php echo ($showIdle)?'&idle=true':'';?>" >
</div>
<div dojoType="dijit.layout.BorderContainer">
<div dojoType="dijit.layout.ContentPane" region="top" id="listHeaderDiv">
  <form dojoType="dijit.form.Form" id="quickSearchListForm" action="" method="" >
  <script type="dojo/method" event="onSubmit" >
    quickSearchExecute();
    return false;        
  </script>
  <div class="listTitle" id="quickSearchDiv" 
     style="display:none; height:100%; width: 100%; position: absolute;">
    <table >
      <tr height="100%" style="vertical-align: middle;">
        <td width="50px" align="center">
         <div style="position:absolute; top:0px;left:5px ;" class="icon<?php echo ((SqlElement::is_subclass_of($objectClass, 'PlgCustomList'))?'ListOfValues':$objectClass);?>32" style="margin-left:9px;width:32px;height:32px" /></div>    
        </td>
        <td><span class="title" ><?php echo i18n("menu" . $objectClass);?></span></td>
        <td style="text-align:right;" width="200px">
                <span class="nobr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?php echo i18n("quickSearch");?>
                &nbsp;</span> 
        </td>
        <td style="vertical-align: middle;">
          <div title="<?php echo i18n('quickSearch')?>" type="text" class="filterField rounded" dojoType="dijit.form.TextBox" 
             id="quickSearchValue" name="quickSearchValue"
             style="width:200px;">
          </div>
        </td>
	      <td style="width:36px">            
	        <button title="<?php echo i18n('quickSearch')?>"  
	          dojoType="dijit.form.Button" 
	          id="listQuickSearchExecute" name="listQuickSearchExecute"
	          iconClass="dijitButtonIcon dijitButtonIconSearch" class="detailButton" showLabel="false">
	          <script type="dojo/connect" event="onClick" args="evt">
              //dijit.byId('quickSearchListForm').submit();
              quickSearchExecute();
          </script>
	        </button>
	      </td>      
        <td style="width:36px">
          <button title="<?php echo i18n('comboCloseButton')?>"  
            dojoType="dijit.form.Button" 
            id="listQuickSearchClose" name="listQuickSearchClose"
            iconClass="dijitButtonIcon dijitButtonIconUndo" class="detailButton" showLabel="false">
            <script type="dojo/connect" event="onClick" args="evt">
              quickSearchClose();
            </script>
          </button>
        </td>    
      </tr>
    </table>
  </div>
  </form>
<table width="100%" class="listTitle" >
  <tr >
    <td width="50px" align="center">
       <div style="position:absolute;left:0px;width:43px;top:0px;height:36px;" class="iconHighlight">&nbsp;</div>
       <div style="position:absolute; top:0px;left:5px ;" class="icon<?php echo ((SqlElement::is_subclass_of($objectClass, 'PlgCustomList'))?'ListOfValues':$objectClass);?>32" style="margin-left:9px;width:32px;height:32px" /></div>
    
    </td>
    <td><span class="title"><?php echo i18n("menu" . $objectClass);?></span></td>
    <td>   
      <form dojoType="dijit.form.Form" id="listForm" action="" method="" >
        <script type="dojo/method" event="onSubmit" >
          return false;        
        </script>
        <table style="width: 100%; height: 27px;">
          <tr>
            <td style="text-align:right;" width="5px">
              <input type="hidden" id="objectClass" name="objectClass" value="<?php echo $objectClass;?>" /> 
              <input type="hidden" id="objectId" name="objectId" value="<?php if (isset($_REQUEST['objectId']))  { echo htmlEncode($_REQUEST['objectId']);}?>" />
              <span class="nobr">&nbsp;&nbsp;&nbsp;&nbsp;
              <?php echo i18n("colId");?>
              &nbsp;</span> 
            </td>
            <td width="5px">
              <div title="<?php echo i18n('filterOnId')?>" style="width:40px" class="filterField rounded" dojoType="dijit.form.TextBox" 
               type="text" id="listIdFilter" name="listIdFilter">
                <script type="dojo/method" event="onKeyUp" >
				          setTimeout("filterJsonList()",10);
                </script>
              </div>
            </td>
              <?php if ( property_exists($obj,'name')) { ?>
              <td style="text-align:right;" width="5px">
                <span class="nobr">&nbsp;&nbsp;&nbsp;
                <?php echo i18n("colName");?>
                &nbsp;</span> 
              </td>
              <td width="5px">
                <div title="<?php echo i18n('filterOnName')?>" type="text" class="filterField rounded" dojoType="dijit.form.TextBox" 
                id="listNameFilter" name="listNameFilter" style="width:120px">
                  <script type="dojo/method" event="onKeyUp" >
                  setTimeout("filterJsonList()",10);
                </script>
                </div>
              </td>
              <?php }?>              
              <?php if ( property_exists($obj,'id' . $objectClass . 'Type') ) { ?>
              <td style="vertical-align: middle; text-align:right;" width="5px">
                 <span class="nobr">&nbsp;&nbsp;&nbsp;
                <?php echo i18n("colType");?>
                &nbsp;</span>
              </td>
              <td width="5px">
                <select title="<?php echo i18n('filterOnType')?>" type="text" class="filterField roundedLeft" dojoType="dijit.form.FilteringSelect"
                <?php echo autoOpenFilteringSelect();?> 
                id="listTypeFilter" name="listTypeFilter" style="width:140px">
                  <?php htmlDrawOptionForReference('id' . $objectClass . 'Type', $objectType, $obj, false); ?>
                  <script type="dojo/method" event="onChange" >
                    refreshJsonList('<?php echo $objectClass;?>');
                  </script>
                </select>
              </td>
              <?php }?>   
              <?php if ( property_exists($obj,'idClient') ) { ?>
              <td style="vertical-align: middle; text-align:right;" width="5px">
                 <span class="nobr">&nbsp;&nbsp;&nbsp;
                <?php echo i18n("colClient");?>
                &nbsp;</span>
              </td>
              <td width="5px">
                <select title="<?php echo i18n('filterOnClient')?>" type="text" class="filterField roundedLeft" dojoType="dijit.form.FilteringSelect"
                <?php echo autoOpenFilteringSelect();?> 
                id="listClientFilter" name="listClientFilter" style="width:140px">
                  <?php htmlDrawOptionForReference('idClient', $objectClient, $obj, false); ?>
                  <script type="dojo/method" event="onChange" >
                    refreshJsonList('<?php echo $objectClass;?>');
                  </script>
                </select>
              </td>
              <?php }?> 
              <?php 
                 $elementable=null;
                 if ( property_exists($obj,'idMailable') ) $elementable='idMailable';
                 else if (property_exists($obj,'idIndicatorable')) $elementable='idIndicatorable';
                 else if (property_exists($obj,'idTextable')) $elementable='idTextable';
                 else if ( property_exists($obj,'idChecklistable')) $elementable='idChecklistable';
                 //$elementable=null;
                 if ($elementable) { ?>
              <td style="vertical-align: middle; text-align:right;" width="5px">
                 <span class="nobr">&nbsp;&nbsp;&nbsp;
                <?php echo i18n("colElement");?>
                &nbsp;</span>
              </td>
              <td width="5px">
                <select title="<?php echo i18n('filterOnElement')?>" type="text" class="filterField roundedLeft" dojoType="dijit.form.FilteringSelect"
                <?php echo autoOpenFilteringSelect();?> 
                id="listElementableFilter" name="listElementableFilter" style="width:140px">
                  <?php htmlDrawOptionForReference($elementable, $objectElementable, $obj, false); ?>
                  <script type="dojo/method" event="onChange" >
                    refreshJsonList('<?php echo $objectClass;?>');
                  </script>
                </select>
              </td>
              <?php }?>                     
              <?php $activeFilter=false;
                 if (! $comboDetail and is_array(getSessionUser()->_arrayFilters)) {
                   if (array_key_exists($objectClass, getSessionUser()->_arrayFilters)) {
                     if (count(getSessionUser()->_arrayFilters[$objectClass])>0) {
                       $activeFilter=true;
                     }
                   }
                 } else if ($comboDetail and is_array(getSessionUser()->_arrayFiltersDetail)) {
                   if (array_key_exists($objectClass, getSessionUser()->_arrayFiltersDetail)) {
                     if (count(getSessionUser()->_arrayFiltersDetail[$objectClass])>0) {
                       $activeFilter=true;
                     }
                   }
                 }
                 ?>
            <td >&nbsp;</td>
            <td width="5px"><span class="nobr">&nbsp;</span></td>
<?php if (! $comboDetail or 1) {?>            
            <td width="36px">
              <button title="<?php echo i18n('quickSearch')?>"  
               dojoType="dijit.form.Button" 
               id="iconSearchOpenButton" name="iconSearchOpenButton"
               iconClass="dijitButtonIcon dijitButtonIconSearch" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  quickSearchOpen();
                </script>
              </button>
              <span id="gridRowCountShadow1" class="gridRowCountShadow1"></span>
              <span id="gridRowCountShadow2" class="gridRowCountShadow2"></span>              
              <span id="gridRowCount" class="gridRowCount"></span>             
              <input type="hidden" id="listFilterClause" name="listFilterClause" value="" style="width: 50px;" />
            </td>
<?php }
      if (! $comboDetail or 1) {?>            
            <td width="51px">
              <button 
              title="<?php echo i18n('advancedFilter')?>"  
               class="comboButton"
               dojoType="dijit.form.DropDownButton" 
               id="listFilterFilter" name="listFilterFilter"
               iconClass="icon<?php echo($activeFilter)?'Active':'';?>Filter" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  showFilterDialog();
                </script>
                <script type="dojo/method" event="onMouseEnter" args="evt">
                  clearTimeout(closeFilterListTimeout);
                  clearTimeout(openFilterListTimeout);
                  openFilterListTimeout=setTimeout("dijit.byId('listFilterFilter').openDropDown();",popupOpenDelay);
                </script>
                <script type="dojo/method" event="onMouseLeave" args="evt">
                  clearTimeout(openFilterListTimeout);
                  closeFilterListTimeout=setTimeout("dijit.byId('listFilterFilter').closeDropDown();",2000);
                </script>
                <div dojoType="dijit.TooltipDialog" id="directFilterList" style="z-index: 999999;display:none; position: absolute;">
                  <?php 
                     //$_REQUEST['filterObjectClass']=$objectClass;
                     //$_REQUEST['context']="directFilterList";
                     if ($comboDetail) $_REQUEST['comboDetail']=true;
                     include "../tool/displayFilterList.php";?>
                 <script type="dojo/method" event="onMouseEnter" args="evt">
                    clearTimeout(closeFilterListTimeout);
                    clearTimeout(openFilterListTimeout);
                </script>
                <script type="dojo/method" event="onMouseLeave" args="evt">
                  dijit.byId('listFilterFilter').closeDropDown();
                </script>
                </div> 
              </button>
            </td>
<?php }?>   
<?php if (! $comboDetail) {?>  
            <td width="51px">           
							<div dojoType="dijit.form.DropDownButton"							    
							  id="listColumnSelector" jsId="listColumnSelector" name="listColumnSelector" 
							  showlabel="false" class="comboButton" iconClass="dijitButtonIcon dijitButtonIconColumn" 
							  title="<?php echo i18n('columnSelector');?>">
                <span>title</span>
							  <div dojoType="dijit.TooltipDialog" class="white" id="listColumnSelectorDialog"
							    style="position: absolute; top: 50px; right: 40%">   
                  <script type="dojo/connect" event="onHide" args="evt">
                    if (dndMoveInProgress) { this.show(); }
                  </script>
                  <script type="dojo/connect" event="onShow" args="evt">
                    recalculateColumnSelectorName();
                  </script>                 
                  <div style="text-align: center;position: relative;"> 
                    <button dojoType="dijit.form.Button" title="<?php echo i18n('titleResetList');?>"
                        class="mediumTextButton" id="" name="" showLabel="true"><?php echo i18n('buttonReset');?>
                        <script type="dojo/connect" event="onClick" args="evt">
                        resetListColumn();
                      </script>
                      </button>
                    <button title="" dojoType="dijit.form.Button" 
                      class="mediumTextButton" id="" name="" showLabel="true"><?php echo i18n('buttonOK');?>
                      <script type="dojo/connect" event="onClick" args="evt">
                        validateListColumn();
                      </script>
                    </button>
                    <div style="position: absolute;top: 34px; right:42px;" id="columnSelectorTotWidthTop"></div>
                  </div>   
                  <div style="height:5px;border-bottom:1px solid #AAAAAA"></div>    
							    <div id="dndListColumnSelector" jsId="dndListColumnSelector" dojotype="dojo.dnd.Source"  
							      dndType="column"
							      withhandles="true" class="container">                       
							      <?php include('../tool/listColumnSelector.php')?>
							    </div>
                  <div style="height:5px;border-top:1px solid #AAAAAA"></div>    
                  <div style="text-align: center;position: relative;">
	                  <button dojoType="dijit.form.Button" title="<?php echo i18n('titleResetList');?>"
	                      class="mediumTextButton" id="" name="" showLabel="true"><?php echo i18n('buttonReset');?>
	                      <script type="dojo/connect" event="onClick" args="evt">
                        resetListColumn();
                      </script>
	                    </button>
                    <button title="" dojoType="dijit.form.Button" 
                       class="mediumTextButton" id="" name="" showLabel="true"><?php echo i18n('buttonOK');?>
                      <script type="dojo/connect" event="onClick" args="evt">
                        validateListColumn();
                      </script>
                    </button>
                    <div style="position: absolute;bottom: 33px; right:42px;" id="columnSelectorTotWidthBottom"></div>
                  </div>   
							  </div>
							</div>   
             </td>
<?php }?>                 
<?php if (! $comboDetail) {?>                
             <td width="36px">
              <button title="<?php echo i18n('printList')?>"  
               dojoType="dijit.form.Button" 
               id="listPrint" name="listPrint"
               iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  showPrint("../tool/jsonQuery.php", 'list');
                </script>
              </button>
              </td>
<?php }?>            
<?php if (! $comboDetail) {?>        
             <td width="36px">
              <button title="<?php echo i18n('reportPrintPdf')?>"  
               dojoType="dijit.form.Button" 
               id="listPrintPdf" name="listPrintPdf"
               iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  showPrint("../tool/jsonQuery.php", 'list', null, 'pdf');
                </script>
              </button>              
            </td>
             <td width="36px">
              <button title="<?php echo i18n('reportPrintCsv')?>"  
               dojoType="dijit.form.Button" 
               id="listPrintCsv" name="listPrintCsv"
               iconClass="dijitButtonIcon dijitButtonIconCsv" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  openExportDialog('csv');
                  //showPrint("../tool/jsonQuery.php", 'list', null, 'csv');
                </script>
              </button>              
            </td>
            <td width="36px">
              <button id="newButtonList" dojoType="dijit.form.Button" showlabel="false"
                title="<?php echo i18n('buttonNew', array(i18n($_REQUEST['objectClass'])));?>"
                iconClass="dijitButtonIcon dijitButtonIconNew" class="detailButton">
                <script type="dojo/connect" event="onClick" args="evt">
		              dojo.byId("newButton").blur();
                  id=dojo.byId('objectId');
	                if (id) { 	
		                id.value="";
		                unselectAllRows("objectGrid");
                    if (switchedMode) {
                      setTimeout("hideList(null,true);", 1);
                    }
                    loadContent("objectDetail.php", "detailDiv", dojo.byId('listForm'));
                  } else { 
                    showError(i18n("errorObjectId"));
	                }
                </script>
              </button>
            </td>
<?php }?>       
<?php if (! $comboDetail) {?> 
            <td style="text-align: right; width:10%; min-width:80px;white-space:normal;">
              <?php echo i18n("labelShowIdle");?>
            </td>
            <td style="width: 10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
              <div title="<?php echo i18n('showIdleElements')?>" dojoType="dijit.form.CheckBox" 
                class="whiteCheck" <?php if ($showIdle) echo " checked ";?>
                type="checkbox" id="listShowIdle" name="listShowIdle">
                <script type="dojo/method" event="onChange" >
                  refreshJsonList('<?php echo $objectClass;?>');
                </script>
              </div>&nbsp;
            </td>
<?php }?>           
          </tr>
        </table>    
      </form>
    </td>
  </tr>
</table>
<div id="listBarShow" onMouseover="showList('mouse')" onClick="showList('click');">
  <div id="listBarIcon" align="center"></div>
</div>
</div>
<div dojoType="dijit.layout.ContentPane" region="center" id="gridContainerDiv">
<table id="objectGrid" jsId="objectGrid" dojoType="dojox.grid.DataGrid"
  query="{ id: '*' }" store="objectStore"
  queryOptions="{ignoreCase:true}"
  rowPerPage="<?php echo Parameter::getGlobalParameter('paramRowPerPage');?>"
  columnReordering="false"
  rowSelector="false"
  loadingMessage="loading..."
  fastScroll="false"
  onHeaderClick="unselectAllRows('objectGrid');selectGridRow();"
  onHeaderCellContextMenu="dijit.byId('listColumnSelector').toggleDropDown();"
  selectionMode="<?php echo ($multipleSelect)?'extended':'single';?>" >
  <thead>
    <tr>
      <?php echo $obj->getLayout();?>
    </tr>
  </thead>
  <script type="dojo/connect" event="onSelected" args="evt">
    if (gridReposition) {return;}
    if (multiSelection) {updateSelectedCountMultiple();return;}
	  if ( dojo.byId('comboDetail') ) {
      rows=objectGrid.selection.getSelected();
      row=rows[0]; 
      dojo.byId('comboDetailId').value=row.id;
      dojo.byId('comboDetailId').value=dojo.byId('comboDetailId').value.replace(/^[0]+/g,"");
      dojo.byId('comboDetailName').value=row.name;
      return true;
    }
    actionYes = function () {
      rows=objectGrid.selection.getSelected();
      row=rows[0]; 
      var id = row.id;
	  dojo.byId('objectId').value=id;
	  //cleanContent("detailDiv");
      formChangeInProgress=false; 
      listClick();
      loadContent("objectDetail.php", "detailDiv", 'listForm');
   	}
    actionNo = function () {
	    //unselectAllRows("objectGrid");
      selectRowById('objectGrid', parseInt(dojo.byId('objectId').value));
    }
    if (checkFormChangeInProgress(actionYes, actionNo)) {
      return true;
    }
  </script>
  <script type="dojo/connect" event="onDeselected" args="evt">
    if (multiSelection) {updateSelectedCountMultiple();return;}
  </script>
  <script type="dojo/method" event="onRowDblClick" args="row">
    if ( dojo.byId('comboDetail') ) {
      rows=objectGrid.selection.getSelected();
      row=rows[0]; 
      dojo.byId('comboDetailId').value=row.id;
      dojo.byId('comboDetailId').value=dojo.byId('comboDetailId').value.replace(/^[0]+/g,"");
      dojo.byId('comboDetailName').value=row.name;
      top.selectDetailItem();
      return;
    }
  </script>
  <script type="dojo/connect" event="_onFetchComplete" args="items, req">
     refreshGridCount();
  </script>
</table>
</div>
</div>