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
  throwError('objectClass parameter not found in REQUEST');
}
$objectClass=$_REQUEST['objectClass'];
Security::checkValidClass($objectClass);

$obj=new $objectClass();

$idUser = getSessionUser()->id;
$cs=new ColumnSelector();
$crit=array('scope'=>'export','objectClass'=>$objectClass, 'idUser'=>$user->id);
$csList=$cs->getSqlElementsFromCriteria($crit);
$hiddenFields=array();
foreach ($csList as $cs) {
	if ($cs->hidden) {
		$hiddenFields[$cs->field]=true;
	}
}
$htmlresult='<td valign="top">';
$FieldsArray=$obj->getFieldsArray(true);
foreach($FieldsArray as $key => $val) {
	if ( ! SqlElement::isVisibleField($val) ) {
		unset($FieldsArray[$key]);
    continue;
	}
	if (substr($val,0,5)=='_sec_') {
		if (strlen($val)>6) {
			$section=substr($val,5);
			if ($section=='Assignment' or $section=='Affectations' or substr($section,0,14)=='Versionproject'
       or $section=='Subprojects' or $section=='Approver' or $section=='ExpenseDetail' 
       or $section=='predecessor' or $section=='successor' or $section =='TestCaseRun'
       or $section=='Projects') {
			  unset($FieldsArray[$key]);
			  continue;
			}
			$FieldsArray[$key]=i18n('section' . ucfirst($section));
		}
	} else {
	  $FieldsArray[$key]=$obj->getColCaption($val);
	}
	if(substr($FieldsArray[$key],0,1)=="["){
		unset($FieldsArray[$key]);
		continue;
	}
}
$countFields=count($FieldsArray);
$htmlresult.='<input type="hidden" dojoType="dijit.form.TextBox" id="column0" name="column0" value="'.$countFields.'">';
$index=1;
$last_key = end($FieldsArray);
$allChecked="checked";
foreach($FieldsArray as $key => $val){
	if(substr($key,0,5)=="_sec_"){
		if($val!=$last_key) {
			$htmlresult.='</td><td style="vertical-align:top;width: 200px;" valign="top">'
			.'<div class="section" style="width:90%"><b>'.$val.'</b></div><br/>';
		}
	} else if(substr($key,0,5)=="input"){
	}else {
		$checked='checked';
		if (array_key_exists($key, $hiddenFields)) {
			$checked='';
			$allChecked='';
		}
    if (substr($key,0,9)=='idContext' and strlen($key)==10) {
      $ctx=new ContextType(substr($key,-1));
      $val=$ctx->name;
    } 
		$htmlresult.='<input type="checkbox" dojoType="dijit.form.CheckBox" id="column'.$index.'" name="column'.$index.'" value="'.$key.'" '.$checked.'>';
		$htmlresult.='<label for="column'.$index.'" class="checkLabel">'.$val.'</label><br/>';
		$index++;
	}
}
$htmlresult.='</td>';
$htmlresult.="<br/>";
?>
<form id="dialogExportForm" name="dialogExportForm">
<table style="width: 100%;">
  <tr>
    <td colspan="2" class="reportTableHeader"><?php echo i18n("chooseColumnExport");?></td>
  </tr>
  <tr><td colspan="2" >&nbsp;</td></tr>
  <tr>
    <td>
      <input type="checkbox" dojoType="dijit.form.CheckBox" id="checkUncheck" name="checkUncheck" value="Check" onclick="checkExportColumns();" <?php echo $allChecked?> />
      <label for="checkUncheck" class="checkLabel"><b><?php echo i18n("checkUncheckAll")?></b></label>&nbsp;&nbsp;&nbsp;
    </td>
    <td>
      <input type="checkbox" dojoType="dijit.form.Button" id="checkAsList" name="checkAsList" onclick="checkExportColumns('aslist');" 
       showLabel="true" label="<?php echo i18n("checkAsList")?>" />
    </td>
  </tr>
  <tr>
    <td style="width:300px;text-align:right" class="dialogLabel"><?php echo i18n("exportReferencesAs")?> :&nbsp;</td>
    <td > <select dojoType="dijit.form.FilteringSelect" class="input" 
           <?php echo autoOpenFilteringSelect();?>
				   style="width: 150px;" name="exportReferencesAs" id="exportReferencesAs">         
           <option value="name"><?php echo i18n("colName");?></option>                            
           <option value="id"><?php echo i18n("colId");?></option>
			   </select></td>
  </tr>
  <tr>
    <td style="width:300px;text-align:right" class="dialogLabel"><?php echo i18n("exportHtml")?> :&nbsp;</td>
    <td > <div type="checkbox" dojoType="dijit.form.CheckBox" id="exportHtml" name="exportHtml" ></div></td>
  </tr>
  <tr><td colspan="2" >&nbsp;</td></tr>
</table>
<table style="width: 100%;">
  <tr>
  <?php  echo $htmlresult; ?>
  </tr>
</table>
<div style="height:10px;"></div>    
<div style="height:5px;border-top:1px solid #AAAAAA"></div>    
<table style="width: 100%">
  <tr>
    <td style="width: 50%; text-align: right;">
    <button align="right" dojoType="dijit.form.Button"
      onclick="closeExportDialog();">
      <?php echo i18n("buttonCancel");?></button>&nbsp;
    </td>
    <td style="width: 50%; text-align: left;">&nbsp;
    <button align="left" dojoType="dijit.form.Button"
      id="dialogPrintSubmit"
      onclick="executeExport('<?php echo $objectClass;?>','<?php echo $idUser;?>');">
      <?php echo i18n("buttonOK");?></button>
    </td>
  </tr>
</table>
</form>
