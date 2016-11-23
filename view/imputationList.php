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
require_once "../tool/formatter.php";
scriptLog('   ->/view/imputationList.php');

$user=getSessionUser();
$rangeType='week';
$currentWeek=weekNumber(date('Y-m-d')) ;
$currentYear=strftime("%Y") ;
if ($currentWeek==1 and strftime("%m")>10 ) {
	$currentYear+=1;
}
$currentDay=date('Y-m-d',firstDayofWeek($currentWeek,$currentYear));
$rangeValue=$currentYear . $currentWeek;

$showPlanned=Parameter::getUserParameter('imputationShowPlannedWork');
$hideDone=Parameter::getUserParameter('imputationHideDone');
$displayHandledGlobal=Parameter::getGlobalParameter('displayOnlyHandled');
$hideNotHandled=Parameter::getUserParameter('imputationHideNotHandled');

if ($displayHandledGlobal=="YES") {
	$hideNotHandled=1;
}
$displayOnlyCurrentWeekMeetings=Parameter::getUserParameter('imputationDisplayOnlyCurrentWeekMeetings');

$showId=false;
if(Parameter::getUserParameter("showId")!=null && Parameter::getUserParameter("showId")==1)$showId=true;
?>

<div dojoType="dijit.layout.BorderContainer">
  <div style="top:30px !important; left: 200px !important; width: 500px; margin: 0px 8px 4px 8px; padding: 5px;display:none;" 
       id="resultDiv" dojoType="dijit.layout.ContentPane" region="none" >
  </div>   
  <div dojoType="dijit.layout.ContentPane" region="top" id="imputationButtonDiv" class="listTitle" >
  <table width="100%" height="32px" class="listTitle">
    <tr height="32px">
      <td width="50px" align="center">
        <?php echo formatIcon('Imputation', 32, null, true);?>
      </td>
      <td width="200px" ><span class="title"><?php echo i18n('menuImputation');?></span></td>
      <td rowspan="2" width="500px" style="vertical-align:top">   
          <table style="width: 100%;" >
            <tr height="32px">
              <td nowrap="nowrap" style="text-align: right;">
                <?php echo i18n("colIdResource");?> 
                <select dojoType="dijit.form.FilteringSelect" class="input roundedLeft" 
                  style="width: 150px;"
                  name="userName" id="userName"
                  <?php echo autoOpenFilteringSelect();?>
                  value="<?php echo ($user->isResource)?$user->id:'0';?>" 
                  >
                  <script type="dojo/method" event="onChange" >
                    refreshImputationList();
                  </script>
                  <?php 
                   $specific='imputation';
                   include '../tool/drawResourceListForSpecificAccess.php';?>  
                </select>
             </td>
             <td nowrap="nowrap" style="text-align: right;">   
                &nbsp;&nbsp;<?php echo i18n("year");?>
                <div style="width:70px; text-align: center; color: #000000;" 
                  dojoType="dijit.form.NumberSpinner" 
                  constraints="{min:2000,max:2100,places:0,pattern:'###0'}"
                  intermediateChanges="true"
                  maxlength="4" class="roundedLeft"
                  value="<?php echo $currentYear;?>" smallDelta="1"
                  id="yearSpinner" name="yearSpinner" >
                  <script type="dojo/method" event="onChange" >
                   return refreshImputationPeriod();
                  </script>
                </div>
             </td>
             <td nowrap="nowrap" style="text-align: right;">  
                &nbsp;&nbsp;<?php echo i18n("week");?>
                <div style="width:55px; text-align: center; color: #000000;" 
                  dojoType="dijit.form.NumberSpinner" 
                  constraints="{min:0,max:55,places:0,pattern:'00'}"
                  intermediateChanges="true"
                  maxlength="2" class="roundedLeft"
                  value="<?php echo $currentWeek;?>" smallDelta="1"
                  id="weekSpinner" name="weekSpinner" >
                  <script type="dojo/method" event="onChange" >
                   return refreshImputationPeriod();
                  </script>
                </div>
              </td>
            </tr>
            <tr height="32px">
              <td style=""></td>
              <td style="width: 200px;text-align: right; align: left;" nowrap="nowrap" colspan="2">
                <?php echo i18n("colFirstDay");?> 
                <div dojoType="dijit.form.DateTextBox"
                	<?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
										echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
									}?>
                  id="dateSelector" name=""dateSelector""
                  invalidMessage="<?php echo i18n('messageInvalidDate')?>"
                  type="text" maxlength="10" 
                  style="width:100px; text-align: center;" class="input roundedLeft"
                  hasDownArrow="true"
                  value="<?php echo $currentDay;?>" >
                  <script type="dojo/method" event="onChange">
                    return refreshImputationPeriod(this.value);
                  </script>
                </div>
              </td>
            </tr>
          </table>
       </td>
       <td rowspan="2">   
          <table style="width: 100%;" >
            <tr>
              <td style="text-align: right; align: right;min-width:150px" >
            	  &nbsp;&nbsp;<?php echo i18n("labelDisplayOnlyCurrentWeekMeetings");?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
                <div title="<?php echo i18n('labelDisplayOnlyCurrentWeekMeetings')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                  id="listDisplayOnlyCurrentWeekMeetings" name="listDisplayOnlyCurrentWeekMeetings" <?php if ($displayOnlyCurrentWeekMeetings) echo 'checked';?>>
                  <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
                </div>&nbsp;
              </td>
              <td style="width:200px;text-align: right; align: right;min-width:150px" >
              &nbsp;&nbsp;<?php echo i18n("labelShowIdle");?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
                <div title="<?php echo i18n('showIdleElements')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                  id="listShowIdle" name="listShowIdle">
                  <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
                </div>&nbsp;
              </td>
            </tr>
				    <tr>
              <td style="text-align: right; align: right;min-width:150px" >
            	  &nbsp;&nbsp;<?php echo i18n("labelHideDone");?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
                <div title="<?php echo i18n('labelHideDone')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                  id="listHideDone" name="listHideDone" <?php if ($hideDone) echo 'checked';?>>
                  <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
                </div>&nbsp;
              </td>
              <td style="width:200px;text-align: right; align: right;min-width:150px" >
      	        &nbsp;&nbsp;<?php echo i18n("labelShowPlannedWork");?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
				        <div title="<?php echo i18n('showPlannedWork')?>" dojoType="dijit.form.CheckBox" type="checkbox" 
				        class="whiteCheck"
				         id="listShowPlannedWork" name="listShowPlannedWork" <?php if ($showPlanned) echo 'checked';?>>
				          <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
				        </div>&nbsp;
				      </td>
            </tr>
            <tr>
              <td style="text-align: right; align: right;min-width:150px" >
            	  <?php if ( $displayHandledGlobal!="YES") { echo '&nbsp;&nbsp;'.i18n("labelHideNotHandled");}?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
                <?php if ( $displayHandledGlobal!="YES") { ?>
                <div title="<?php echo i18n('labelHideNotHandled')?>" dojoType="dijit.form.CheckBox" type="checkbox" class="whiteCheck"
                id="listHideNotHandled" name="listHideNotHandled" <?php if ($hideNotHandled) echo 'checked';?>>
                  <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
                </div>&nbsp;
                <?php }?>
              </td>
              <td style="width:200px;text-align: right; align: right;min-width:150px" >
      	        &nbsp;&nbsp;<?php echo i18n("labelShowId");?>
              </td>
              <td style="width:10px;text-align: center; align: center;white-space:nowrap;">&nbsp;
				        <div title="<?php echo i18n('labelShowId')?>" dojoType="dijit.form.CheckBox" type="checkbox" 
				        class="whiteCheck"
				         id="showId" name="showId" <?php if ($showId) echo 'checked';?>>
				          <script type="dojo/method" event="onChange" >
                    return refreshImputationList();
                  </script>
				        </div>&nbsp;
				      </td>
				    </tr>
          </table>    
      </td>
    </tr>
    
    <tr>
      <td colspan="2">
        <table width="100%"  >
          <tr height="27px">
            
            <td style="min-width:200px"> 
              <button id="saveParameterButton" dojoType="dijit.form.Button" showlabel="false"
                title="<?php echo i18n('buttonSaveImputation');?>"
                iconClass="dijitButtonIcon dijitButtonIconSave" class="detailButton" >
                  <script type="dojo/connect" event="onClick" args="evt">
                    this.focus();
                    setTimeout('saveImputation()',10);;
                 </script>
              </button>
              <button title="<?php echo i18n('print')?>"  
               dojoType="dijit.form.Button" 
               id="printButton" name="printButton"
               iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  showPrint('../report/imputation.php', 'imputation');
                </script>
              </button>
              <button title="<?php echo i18n('reportPrintPdf')?>"  
               dojoType="dijit.form.Button" 
               id="printButtonPdf" name="printButtonPdf"
               iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton" showLabel="false">
                <script type="dojo/connect" event="onClick" args="evt">
                  showPrint('../report/imputation.php', 'imputation', null, 'pdf');
                </script>
              </button>
              <button id="undoButton" dojoType="dijit.form.Button" showlabel="false"
               title="<?php echo i18n('buttonUndoImputation');?>"
               "disabled"
               iconClass="dijitButtonIcon dijitButtonIconUndo"  class="detailButton">
                <script type="dojo/connect" event="onClick" args="evt">
                  formChangeInProgress=false;
                  refreshImputationList();
                </script>
              </button>    
              <div dojoType="dijit.Tooltip" connectId="saveButton"><?php echo i18n("buttonSaveImputation")?></div>
            </td>
          </tr>
        </table>
      <td>
    <tr>
  </table>
  </div>
  <div style="position:relative;" dojoType="dijit.layout.ContentPane" region="center" id="workDiv" name="workDiv">
     <form dojoType="dijit.form.Form" id="listForm" action="" method="post" >
       <input type="hidden" name="userId" id="userId" value="<?php echo $user->id;?>"/>
       <input type="hidden" name="rangeType" id="rangeType" value="<?php echo $rangeType;?>"/>
       <input type="hidden" name="rangeValue" id="rangeValue" value="<?php echo $rangeValue;?>"/>
       <input type="checkbox" name="idle" id="idle" style="display: none;" />     
       <input type="checkbox" name="showPlannedWork" id="showPlannedWork" style="display: none;" />
       <input type="checkbox" name="showIdT" id="showIdT" style="display: none;" />
       <input type="checkbox" name="hideDone" id="hideDone" style="display: none;" />
       <input type="checkbox" name="hideNotHandled" id="hideNotHandled" style="display: none;" />
       <input type="checkbox" name="displayOnlyCurrentWeekMeetings" id="displayOnlyCurrentWeekMeetings" style="display: none;" />
       <input type="hidden" id="page" name="page" value="../report/imputation.php"/>
       <input type="hidden" id="outMode" name="outMode" value="" />
       <input type="hidden" name="yearSpinnerT" id="yearSpinnerT" value=""/>
       <input type="hidden" name="weekSpinnerT" id="weekSpinnerT" value=""/>
       <input type="hidden" noname="daysWorkFuture" id="daysWorkFuture" value="0"/>
       <input type="hidden" noname="daysWorkFutureBlocking" id="daysWorkFutureBlocking" value="0"/>
      <?php if (! isset($print) ) {$print=false;}
      ImputationLine::drawLines($user->id, $rangeType, $rangeValue, false, $showPlanned, $print, $hideDone, $hideNotHandled, $displayOnlyCurrentWeekMeetings,$currentWeek,$currentYear, $showId);?>
     </form>
  </div>
</div>