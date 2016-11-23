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
scriptLog('   ->/view/portfolioPlanningList.php');

$canPlan=false;
$right=SqlElement::getSingleSqlElementFromCriteria('habilitationOther', array('idProfile'=>$user->idProfile, 'scope'=>'planning'));
if ($right) {
  $list=new ListYesNo($right->rightAccess);
  if ($list->code=='YES') {
    $canPlan=true;
  }
}
$startDate=date('Y-m-d');
$endDate=null;
$user=getSessionUser();
$saveDates=false;
$paramStart=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningStartDate'));
if ($paramStart->id) {
  $startDate=$paramStart->parameterValue;
  $saveDates=true;
}
$paramEnd=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningEndDate'));
if ($paramEnd->id) {
  $endDate=$paramEnd->parameterValue;
  $saveDates=true;
}
$saveShowWbsObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowWbs'));
$saveShowWbs=$saveShowWbsObj->parameterValue;
$saveShowResourceObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowResource'));
$saveShowResource=$saveShowResourceObj->parameterValue;
$saveShowWorkObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowWork'));
$saveShowWork=$saveShowWorkObj->parameterValue;
$saveShowClosedObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowClosed'));
$saveShowClosed=$saveShowClosedObj->parameterValue;
$saveShowMilestoneObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowMilestone'));
$saveShowMilestone=$saveShowMilestoneObj->parameterValue;

if ($saveShowClosed) {
	$_REQUEST['idle']=true;
}

$proj=null;
if (array_key_exists('project',$_SESSION)) {
  $proj=$_SESSION['project'];
}
if ($proj=='*' or !$proj) {
  $proj=null;
}
//$objectClass='Task';
//$obj=new $objectClass;
?>
  
<div id="mainPlanningDivContainer" dojoType="dijit.layout.BorderContainer">
	<div dojoType="dijit.layout.ContentPane" region="top" id="listHeaderDiv" height="27px"
	style="z-index: 3; position: relative; overflow: visible !important;">
		<table width="100%" height="27px" class="listTitle" >
		  <tr height="27px">
		  	<td style="vertical-align:top; width:250px;">
		      <table >
    		    <tr height="32px">
      		    <td width="50px" align="center">
                <?php echo formatIcon('PortfolioPlanning', 32, null, true);?>
              </td>
              <td width="200px" ><span class="title" style="max-width:200px;white-space:normal"><?php echo i18n('menuPortfolioPlanning');?></span></td>
      		  </tr>
    		  </table>
		    </td>
		    <td>   
		      <form dojoType="dijit.form.Form" id="listForm" action="" method="" >
		        <table style="width: 100%;">
		          <tr>
		            <td style="width:70px">
		              <input type="hidden" id="objectClass" name="objectClass" value="" /> 
		              <input type="hidden" id="objectId" name="objectId" value="" />
                  <input type="hidden" id="portfolio" name="portfolio" value="true" />
		              &nbsp;&nbsp;&nbsp;
<?php if ($canPlan) { ?>
		              <button id="planButton" dojoType="dijit.form.Button" showlabel="false"
		                title="<?php echo i18n('buttonPlan');?>"
		                iconClass="iconPlanStopped" >
		                <script type="dojo/connect" event="onClick" args="evt">
                     showPlanParam();
                     return false;
                    </script>
		              </button>
<?php }?>             
		            </td>
		            <td style="white-space:nowrap;width:240px">
		              <table>
                    <tr>
                      <td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayStartDate");?>&nbsp;&nbsp;</td><td>
                        <div dojoType="dijit.form.DateTextBox"
	                        <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
														echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
													}?>
                           id="startDatePlanView" name="startDatePlanView"
                           invalidMessage="<?php echo i18n('messageInvalidDate')?>"
                           type="text" maxlength="10"
                           style="width:100px; text-align: center;" class="input roundedLeft"
                           hasDownArrow="true"
                           value="<?php echo $startDate;?>" >
                           <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                           </script>
                         </div>
                      </td>
                    </tr>
                    <tr>
                      <td align="right">&nbsp;&nbsp;&nbsp;<?php echo i18n("displayEndDate");?>&nbsp;&nbsp;</td>
                      <td>
                        <div dojoType="dijit.form.DateTextBox"
	                        <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
														echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
													}?>
                           id="endDatePlanView" name="endDatePlanView"
                           invalidMessage="<?php echo i18n('messageInvalidDate')?>"
                           type="text" maxlength="10"
                           style="width:100px; text-align: center;" class="input roundedLeft"
                           hasDownArrow="true"
                           value="<?php echo $endDate;?>" >
                           <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                           </script>
                        </div>
                      </td>
                    </tr>
                  </table>
		            </td>
                <td>
                  <table >
                    <tr>
                      <td width="32px">
                        <button title="<?php echo i18n('printPlanning')?>"
                         dojoType="dijit.form.Button"
                         id="listPrint" name="listPrint"
                         iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton" 
                         showLabel="false">
                          <script type="dojo/connect" event="onClick" args="evt">
<?php $ganttPlanningPrintOldStyle=Parameter::getGlobalParameter('ganttPlanningPrintOldStyle');
      if (!$ganttPlanningPrintOldStyle) {$ganttPlanningPrintOldStyle="NO";}
      if ($ganttPlanningPrintOldStyle=='YES') {?>
                          showPrint("../tool/jsonPlanning.php?portfolio=true", 'planning');
<?php } else { ?>
                          showPrint("planningPrint.php", 'planning');
<?php }?>   
                          </script>
                        </button>
                      </td>
                      <td width="32px">
                        <button title="<?php echo i18n('reportPrintPdf')?>"
                         dojoType="dijit.form.Button"
                         id="listPrintPdf" name="listPrintPdf"
                         iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton"  showLabel="false">
                          <script type="dojo/connect" event="onClick" args="evt">
                          var paramPdf='<?php echo Parameter::getGlobalParameter("pdfPlanningBeta");?>';
                          if(paramPdf!='false' && (dojo.isChrome || paramPdf=='true') ) planningPDFBox();
                          else showPrint("../tool/jsonPlanning_pdf.php?portfolio=true", 'planning', null, 'pdf');
                          </script>
                        </button>
                      </td>
                      <td>
                       <div dojoType="dijit.form.DropDownButton"
                             id="planningColumnSelector" jsId="planningColumnSelector" name="planningColumnSelector" 
                             showlabel="false" class="comboButton" iconClass="dijitButtonIcon dijitButtonIconColumn"
                             title="<?php echo i18n('columnSelector');?>">
                          <span>title</span>
                          <div dojoType="dijit.TooltipDialog" class="white" style="width:250px;">
                            <script type="dojo/connect" event="onHide" args="evt">
                              if (dndMoveInProgress) { this.show(); }
                            </script>   
                            <div id="dndPlanningColumnSelector" jsId="dndPlanningColumnSelector" 
                             dojotype="dojo.dnd.Source"  
                             dndType="column"
                             withhandles="true" class="container">    
                               <?php 
                                 $portfolioPlanning=true; 
                                 include('../tool/planningColumnSelector.php')?>
                            </div> 
                            <div style="height:5px;"></div>    
                            <div style="text-align: center;"> 
                              <button title="" dojoType="dijit.form.Button" 
                                id="" name="" showLabel="true"><?php echo i18n('buttonOK');?>
                                <script type="dojo/connect" event="onClick" args="evt">
                                  validatePlanningColumn();
                                </script>
                              </button>
                            </div>                
                          </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="4" style="white-space:nowrap;">
                        <span title="<?php echo i18n('saveDates')?>" dojoType="dijit.form.CheckBox"
                           type="checkbox" id="listSaveDates" name="listSaveDates" class="whiteCheck"
                           <?php if ( $saveDates) {echo 'checked="checked"'; } ?>  >

                          <script type="dojo/method" event="onChange" >
                            refreshJsonPlanning();
                          </script>
                        </span>
                        <span for="listSaveDates"><?php echo i18n("saveDates");?></span>
                      </td>
                    </tr>
                  </table>
                </td>
		            <td>
                  <div id="planResultDiv" style="display:none" 
                    dojoType="dijit.layout.ContentPane" region="center" >
                  </div>
                  <table>
                  <tr><td style="font-weight:bold;text-align:center;"><?php echo i18n('displayBaseline');?></td></tr>
                  <tr><td style="text-align:right"><?php echo i18n('baselineTop').'&nbsp;:&nbsp;';?>
                  <select dojoType="dijit.form.FilteringSelect" class="input roundedLeft" 
                        style="width: 150px;"
                        name="selectBaselineTop" id="selectBaselineTop"
                        <?php echo autoOpenFilteringSelect();?>
                        >
                        <script type="dojo/method" event="onChange" >
                           saveDataToSession("planningBaselineTop",this.value,false);
                           refreshJsonPlanning();
                        </script>
                        <?php htmlDrawOptionForReference('idBaseline', getSessionValue("planningBaselineTop"), null,false,($proj)?'idProject':null,($proj)?$proj:null);?>
                      </select>
                  </td></tr>
                  <tr><td style="text-align:right"><?php echo i18n('baselineBottom').'&nbsp;:&nbsp';?>
                  
                   <select dojoType="dijit.form.FilteringSelect" class="input roundedLeft" 
                        style="width: 150px;"
                        name="selectBaselineBottom" id="selectBaselineBottom"
                        <?php echo autoOpenFilteringSelect();?>
                        >
                        <script type="dojo/method" event="onChange" >
                           saveDataToSession("planningBaselineBottom",this.value,false);
                           refreshJsonPlanning();
                        </script>
                        <?php htmlDrawOptionForReference('idBaseline', getSessionValue("planningBaselineBottom"), null,false,($proj)?'idProject':null,($proj)?$proj:null);?>
                      </select>
                  </td></tr>
                  </table>
                </td>
		            <td style="text-align: right; align: right;">
		              <table width="100%">
                    <tr style="height:10px">
                      <td><?php echo i18n("labelShowWbs");?></td>
                      <td style="width:35px">
					              <div title="<?php echo i18n('showWbs')?>" dojoType="dijit.form.CheckBox" 
			                    type="checkbox" id="showWBS" name="showWBS" class="whiteCheck"
			                    <?php if ($saveShowWbs=='1') { echo ' checked="checked" '; }?> >
					                <script type="dojo/method" event="onChange" >
                            saveUserParameter('planningShowWbs',((this.checked)?'1':'0'));
                            refreshJsonPlanning();
                          </script>
					              </div>&nbsp;
		                  </td>
                    </tr>
                    <tr>
                      <td><?php echo i18n("labelShowIdle");?></td>
                      <td>
					              <div title="<?php echo i18n('showIdleElements')?>" dojoType="dijit.form.CheckBox" 
			                    type="checkbox" id="listShowIdle" name="listShowIdle" class="whiteCheck"
			                    <?php if ($saveShowClosed=='1') { echo ' checked="checked" '; }?> >
					                <script type="dojo/method" event="onChange" >
                            saveUserParameter('planningShowClosed',((this.checked)?'1':'0'));
                            refreshJsonPlanning();
                          </script>
					              </div>&nbsp;
                      </td>
                    </tr>                 
                    <tr>
                    <td colspan="2">
                      <?php echo i18n("colListShowMilestone");?>                  
				                <select dojoType="dijit.form.FilteringSelect" class="input roundedLeft" 
				                  style="width: 150px;"
				                  <?php echo autoOpenFilteringSelect();?>
				                  name="listShowMilestone" id="listShowMilestone">
				                  <script type="dojo/method" event="onChange" >
                            saveUserParameter('planningShowMilestone',this.value);
                            refreshJsonPlanning();
                          </script>
                            <option value=" " <?php echo (! $saveShowMilestone)?'SELECTED':'';?>><?php echo i18n("paramNone");?></option>                            
                            <?php htmlDrawOptionForReference('idMilestoneType', $saveShowMilestone,null, true);?>
                            <?php if ($saveShowMilestone!='all') {?>
                            <option value="all"><?php echo i18n("all");?></option>
                            <?php }?>
			                  </select>
                      </td>
                    </tr>
                  </table>
		            </td>
		          </tr>
		        </table>    
		      </form>
		    </td>
		  </tr>
		</table>
		<div id="listBarShow" onMouseover="showList('mouse')" onClick="showList('click');">
		  <div id="listBarIcon" align="center"></div>
		</div>
	
		<div dojoType="dijit.layout.ContentPane" id="planningJsonData" jsId="planningJsonData" 
     style="display: none">
		  <?php
		        $portfolio=true;
            include '../tool/jsonPlanning.php';
          ?>
		</div>
	</div>
	<div dojoType="dijit.layout.ContentPane" region="center" id="gridContainerDiv">
   <div id="submainPlanningDivContainer" dojoType="dijit.layout.BorderContainer"
    style="border-top:1px solid #ffffff;">
        <?php $leftPartSize=Parameter::getUserParameter('planningLeftSize');
          if (! $leftPartSize) {$leftPartSize='325px';} ?>
	   <div dojoType="dijit.layout.ContentPane" region="left" splitter="true" 
      style="width:<?php echo $leftPartSize;?>; height:100%; overflow-x:scroll; overflow-y:hidden;" class="ganttDiv" 
      id="leftGanttChartDIV" name="leftGanttChartDIV"
      onScroll="dojo.byId('ganttScale').style.left=(this.scrollLeft)+'px'; this.scrollTop=0;" 
      onmousewheel="leftMouseWheel(event);">
      <script type="dojo/method" event="onUnload" >
         var width=this.domNode.style.width;
         setTimeout("saveUserParameter('planningLeftSize','"+width+"');",1);
         return true;
      </script>
     </div>
     <div dojoType="dijit.layout.ContentPane" region="center" 
      style="height:100%; overflow:hidden;" class="ganttDiv" 
      id="GanttChartDIV" name="GanttChartDIV" >
       <div id="mainRightPlanningDivContainer" dojoType="dijit.layout.BorderContainer">
         <div dojoType="dijit.layout.ContentPane" region="top" 
          style="width:100%; height:45px; overflow:hidden;" class="ganttDiv"
          id="topGanttChartDIV" name="topGanttChartDIV">
         </div>
         <div dojoType="dijit.layout.ContentPane" region="center" 
          style="width:100%; overflow-x:scroll; overflow-y:scroll; position: relative; top:-10px;" class="ganttDiv"
          id="rightGanttChartDIV" name="rightGanttChartDIV"
          onScroll="dojo.byId('rightside').style.left='-'+(this.scrollLeft+1)+'px';
                    dojo.byId('leftside').style.top='-'+(this.scrollTop)+'px';"
         >
         </div>
       </div>
     </div>
   </div>
	</div>
</div>
