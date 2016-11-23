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
 * Presents an object. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/diaryMain.php');  
  $user=getSessionUser();
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="Diary" />
<div class="container" dojoType="dijit.layout.BorderContainer">
  <div id="listDiv" dojoType="dijit.layout.ContentPane" region="top" class="listTitle" splitter="false" style="height:58px;">
  <table width="100%" height="27px" class="listTitle" >
    <tr height="17px">
      <td width="50px" align="center">
        <?php echo formatIcon('Diary',32,null,true);?>
      </td>
      <td width="100px" ><span class="title"><?php echo i18n('menuDiary');?></span></td>
      <td style="text-align: center"> 
		   <?php 
		   $period=Parameter::getUserParameter("diaryPeriod");
		   if (!$period) {$period="month";}
		   $year=date('Y');
		   $month=date('m');
		   $week=date('W');
		   $day=date('Y-m-d');
		   echo '<div style="font-size:20px" id="diaryCaption">';
		   if ($period=='month') {
		     echo i18n(date("F",mktime(0,0,0,$month,1,$year))).' '.$year;
		   } else if ($period=='week') {
         $firstday=date('Y-m-d',firstDayofWeek($week, $year));
         $lastday=addDaysToDate($firstday, 6);
         echo $year.' #'.$week."<span style='font-size:70%'> (".htmlFormatDate($firstday)." - ".htmlFormatDate($lastday).")</span>";
       } else if ($period=='day') {
         $vDayArr = array('', i18n("Monday"),i18n("Tuesday"),i18n("Wednesday"),
		                i18n("Thursday"), i18n("Friday"),i18n("Saturday"),i18n("Sunday"));
         echo $vDayArr[date("N",mktime(0,0,0,$month,date('d'),$year))]." ".htmlFormatDate($day);
       }
       echo "</div>";
		   ?>
		   </td>
		    <td style="width: 200px;text-align: left; align: left;"nowrap="nowrap">
                <?php echo i18n("colFirstDay");
                $currentWeek=weekNumber(date('Y-m-d')) ;
                $currentYear=strftime("%Y") ;
                $currentDay=date('Y-m-d',firstDayofWeek($currentWeek,$currentYear));?> 
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
                    return diarySelectDate(this.value);
                  </script>
                </div>
              </td>
		   <td nowrap="nowrap" width="400px" ><form id="diaryForm" name="diaryForm">
		   <input type="hidden" name="diaryPeriod" id="diaryPeriod" value="<?php echo $period;?>" />
		   <input type="hidden" name="diaryYear" id="diaryYear" value="<?php echo $year;?>" />
		   <input type="hidden" name="diaryMonth" id="diaryMonth" value="<?php echo $month;?>" />
		   <input type="hidden" name="diaryWeek" id="diaryWeek" value="<?php echo $week;?>" />
		   <input type="hidden" name="diaryDay" id="diaryDay" value="<?php echo $day;?>" />
		   <table style="width:100%"><tr><td>
		   <?php echo i18n("colIdResource");?> 
		   <select dojoType="dijit.form.FilteringSelect" class="input roundedLeft" style="width: 150px;"
        name="diaryResource" id="diaryResource"
        <?php echo autoOpenFilteringSelect();?>
        value="<?php echo ($user->isResource)?$user->id:'0';?>" >
         <script type="dojo/method" event="onChange" >
           loadContent("../view/diary.php","detailDiv","diaryForm");
         </script>
         <?php 
           $specific='diary';
           include '../tool/drawResourceListForSpecificAccess.php'?>  
       </select>
       </td><td style="text-align:right">
         <table style="width:99%"><tr>
           <td><?php echo i18n("labelShowDone")?>&nbsp;</td>
           <td>
             <div title="<?php echo i18n('labelShowDone')?>" dojoType="dijit.form.CheckBox" 
                class="whiteCheck" type="checkbox" id="showDone" name="showDone">
                <script type="dojo/method" event="onChange" >
                  loadContent("../view/diary.php","detailDiv","diaryForm");
                </script>
              </div>
            </td>
          </tr><tr>
            <td><?php echo i18n("labelShowIdle")?>&nbsp;</td>
            <td>
              <div title="<?php echo i18n('showIdleElements')?>" dojoType="dijit.form.CheckBox" 
                class="whiteCheck" type="checkbox" id="showIdle" name="showIdle">
                <script type="dojo/method" event="onChange" >
                  loadContent("../view/diary.php","detailDiv","diaryForm");
                </script>
              </div>
            </td>
          </tr></table>
       </td></tr></table>
		   </form> </td>
   	</tr>
   	<tr height="18px" vertical-align="middle">
   	  <td colspan="5">
   	    <table width="100%"><tr><td width="50%;">
   	    <div class="buttonDiary" onClick="diaryPrevious();"><img src="../view/css/images/left.png" /></div>
   	    </td><td style="width:1px"></td><td width="50%">
   	    <div class="buttonDiary" onClick="diaryNext();"><img src="../view/css/images/right.png" /></div>
   	    </td></tr>
   	    </table>
   	  </td>
   	</tr>
   </table>
  </div>
  <?php $destinationHeight=$_REQUEST['destinationHeight']-58;?>
  <div id="detailDiv" dojoType="dijit.layout.ContentPane" region="center" style="height:<?php echo $destinationHeight;?>px">
   <?php include 'diary.php'; ?>
  </div>
</div>