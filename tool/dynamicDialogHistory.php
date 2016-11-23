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

include_once '../tool/formatter.php';

if (! isset ($print)) {
	$print=false;
}
if (! array_key_exists('objectClass',$_REQUEST)) {
	throwError('Parameter objectClass not found in REQUEST');
}
$objectClass=$_REQUEST['objectClass'];

if (! array_key_exists('objectId',$_REQUEST)) {
	throwError('Parameter objectId not found in REQUEST');
}
$objectId=$_REQUEST['objectId'];

$dynamicDialogHistory=true;
$_REQUEST['refreshHistory']=true;
$showWorkHistory=false;
if (getSessionValue('showWorkHistory')) {
  $showWorkHistory=true;
}
?>

<?php showCloseButton();?>

<div xstyle="width:300px">
<?php include '../view/objectDetail.php';?>
</div>

<?php // Centralise button, to be displayed on top and bottom 
function showCloseButton() {
  global $objectClass,$showWorkHistory;?> 
  
  <table style="width: 100%;">
   <tr>
     <td style="width: 50%;" align="left">
       <?php if (property_exists($objectClass, $objectClass.'PlanningElement')) {?>
       <button dojoType="dijit.form.Button" type="button" onclick="historyShowHideWork();">
         <?php if ($showWorkHistory) echo i18n("hideWork");
           else echo i18n("showWork");?>
       </button>
       <?php }?>
     </td>
     <td style="width: 50%;" align="right">
       <button dojoType="dijit.form.Button" type="button" onclick="dijit.byId('dialogHistory').hide();">
         <?php echo i18n("close");?>
       </button>
     </td>
   </tr>      
  </table>
<?php 
} ?>



