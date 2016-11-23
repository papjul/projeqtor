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
?>
<table>
    <tr>
     <td class="section"><?php echo i18n("sectionStoredFilters");?></td>
    </tr>
    <tr>
      <td>
        <div id='listStoredFilters' dojoType="dijit.layout.ContentPane" region="center"></div>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
      <td>
        <div id='listSharedFilters' dojoType="dijit.layout.ContentPane" region="center"></div>
      </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
</table>
<table xstyle="border: 1px solid grey;">
    <tr>
     <td class="section"><?php echo i18n("sectionActiveFilter");?></td>
    </tr>
    <tr>
      <td style="margin: 2px;"> 
        <div id='listFilterClauses' dojoType="dijit.layout.ContentPane" region="center" style="overflow: hidden"></div>
         
        <form id='dialogFilterForm' name='dialogFilterForm' onSubmit="return false;">
         <input type="hidden" id="filterObjectClass" name="filterObjectClass" />
         <input type="hidden" id="filterClauseId" name="filterClauseId" />
         <input type="hidden" id="filterDataType" name="filterDataType" />
         <input type="hidden" id="filterName" name="filterName" />
         <table width="100%" style="border: 1px solid grey;">
           <tr><td colspan="4" class="filterHeader"><?php echo i18n("addFilterClauseTitle");?></td></tr>
           <tr style="vertical-align: top;">
             <td style="width: 210px;" >
               <div dojoType="dojo.data.ItemFileReadStore" jsId="attributeStore" url="../tool/jsonList.php?listType=empty" searchAttr="name" >
               </div>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="idFilterAttribute" name="idFilterAttribute" 
                missingMessage="<?php echo i18n('attributeNotSelected');?>"
                class="input" value="" style="width: 200px;" store="attributeStore">
                  <script type="dojo/method" event="onChange" >
                    filterSelectAtribute(this.value);
                  </script>              
               </select>
             </td>
             <td style="width: 110px;">
               <div dojoType="dojo.data.ItemFileReadStore" jsId="operatorStore" url="../tool/jsonList.php?listType=empty" searchAttr="name" >
               </div>
               <select dojoType="dijit.form.FilteringSelect" 
               <?php echo autoOpenFilteringSelect();?>
                id="idFilterOperator" name="idFilterOperator" 
                missingMessage="<?php echo i18n('valueNotSelected');?>"
                class="input" value="" style="width: 100px;" store="operatorStore">
                  <script type="dojo/method" event="onChange" >
                    filterSelectOperator(this.value);
                  </script>        
               </select>
             </td>
             <td style="width:410px;vertical-align:middle;">
               <input id="filterValue" name="filterValue" value=""  
                 dojoType="dijit.form.TextBox" 
                 style="width:400px" />
               <select id="filterValueList" name="filterValueList[]" value=""  
                 dojoType="dijit.form.MultiSelect" multiple
                 style="width:400px" size="10" class="selectList"></select>
               <input type="checkbox" id="filterValueCheckbox" name="filterValueCheckbox" value=""  
                 dojoType="dijit.form.CheckBox" style="padding-top:7px";/> 
               <input id="filterValueDate" name="filterValueDate" value=""  
                 dojoType="dijit.form.DateTextBox" 
                 constraints="{datePattern:browserLocaleDateFormatJs}"
                 style="width:100px" />
               <select id="filterSortValueList" name="filterSortValueList" value="asc"  
                 dojoType="dijit.form.FilteringSelect"
                 <?php echo autoOpenFilteringSelect();?>
                 missingMessage="<?php echo i18n('valueNotSelected');?>" 
                 style="width:400px" size="10" class="input">
                  <option value="asc" SELECTED><?php echo i18n('sortAsc');?></option>
                  <option value="desc"><?php echo i18n('sortDesc');?></option>
               </select> 
             </td>
             <td style="width:25px; text-align: center;vertical-align:middle;" align="center">
               <a src="css/images/smallButtonAdd.png" style="margin-top:3px" onClick="addfilterClause();" title="<?php echo i18n('addFilterClause');?>" class="smallButton">
               <?php echo formatSmallButton('Add');?>
               </a> 
             </td>
           </tr>
         </table>
        </form>
      </td>
    </tr>
    <tr style="height:32px">
      <td align="center">
        <table><tr><td>
        <span id="filterDefaultButtonDiv">
          <button class="mediumTextButton" dojoType="dijit.form.Button" onclick="defaultFilter();">
            <?php echo i18n("buttonDefault");?>
          </button>
        </span>
        </td><td>
        <button class="mediumTextButton" dojoType="dijit.form.Button" onclick="clearFilter();">
          <?php echo i18n("buttonClear");?>
        </button>
        </td><td>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="cancelFilter();">
          <?php echo i18n("buttonCancel");?>
        </button>
        </td><td>
        <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="dialogFilterSubmit" onclick="protectDblClick(this);selectFilter();return false;">
          <?php echo i18n("buttonOK");?>
        </button>
        </td></tr></table>
      </td>
    </tr>
  </table>