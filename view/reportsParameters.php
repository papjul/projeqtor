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
scriptLog('   ->/view/reportsList.php');
?>
<form id='reportForm' name='reportForm' onSubmit="return false;">
<table><tr><td>
<table style="width:100%;">
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
<?php 
$user=getSessionUser();
$currentWeek=weekNumber(date('Y-m-d'));
if (strlen($currentWeek)==1) {
  $currentWeek='0' . $currentWeek;
}
$currentYear=strftime("%Y") ;
$currentMonth=strftime("%m") ;
$idReport=$_REQUEST['idReport'];
if (!$idReport) {
  exit;
}
$report=new Report($idReport);
echo "<input type='hidden' id='reportFile' name='reportFile' value='" . htmlEncode($report->file) . "' />";
echo "<input type='hidden' id='reportId' name='reportId' value='" . htmlEncode($report->id) . "' />";
$param=new ReportParameter();
$crit=array('idReport'=>$idReport);
$listParam=$param->getSqlElementsFromCriteria($crit,false,null,'sortOrder');
foreach ($listParam as $param) {
  if ($param->paramType=='week') {
    $defaultWeek='';
    $defaultYear='';
    if ($param->defaultValue=='currentWeek') {
      $defaultWeek=$currentWeek;
      $defaultYear=$currentYear;
    } else if ($param->defaultValue=='currentYear') {
      $defaultYear=$currentYear;
    }
    ?>
    <input type="hidden" id='periodValue' name='periodValue' value='<?php echo $currentYear . $currentWeek;?>' />
    <input type="hidden" id='periodType' name='periodType' value='week'/>
    <tr>
    <td class="label"><label><?php echo i18n("year");?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:70px; text-align: center; color: #000000;" 
      dojoType="dijit.form.NumberSpinner" 
      constraints="{min:2000,max:2100,places:0,pattern:'###0'}"
      intermediateChanges="true"
      maxlength="4"
      value="<?php echo $defaultYear;?>" smallDelta="1"
      id="yearSpinner" name="yearSpinner" >
      <script type="dojo/method" event="onChange">
        var year=dijit.byId('yearSpinner').get('value');
        var week=dijit.byId('weekSpinner').get('value') + '';
        week=(week.length==1)?'0'+week:week;
        dojo.byId('periodValue').value='' + year + week;
      </script>
    </div></td>
    </tr>
    <tr>
    <td class="label"><label><?php echo i18n("week");?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:55px; text-align: center; color: #000000;" 
       dojoType="dijit.form.NumberSpinner" 
       constraints="{min:0,max:55,places:0,pattern:'00'}"
       intermediateChanges="true"
       maxlength="2"
       value="<?php echo $defaultWeek;?>" smallDelta="1"
       id="weekSpinner" name="weekSpinner" >
       <script type="dojo/method" event="onChange" >
         var year=dijit.byId('yearSpinner').get('value');
         if (this.value>getWeek(31, 12, year)) {
          dijit.byId('weekSpinner').set('value',1);
          year=parseInt(year)+1;
          dijit.byId('yearSpinner').set('value',year);
         } else if (this.value==0) {
          year=parseInt(year)-1;          
          dijit.byId('weekSpinner').set('value',getWeek(31, 12, year));
          dijit.byId('yearSpinner').set('value',year);
         }
         var week=dijit.byId('weekSpinner').get('value') + '';
         week=(week.length==1)?'0'+week:week;
         dojo.byId('periodValue').value='' + year + week;
       </script>
     </div></td>
     </tr>
<?php 
  } else if ($param->paramType=='month') {
    $defaultMonth='';
    $defaultYear='';
    if ($param->defaultValue=='currentMonth') {
      $defaultMonth=$currentMonth;
      $defaultYear=$currentYear;
    } else if ($param->defaultValue=='currentYear') {
    	$defaultYear=$currentYear;
    }
?>
    <input type="hidden" id='periodValue' name='periodValue' value='<?php echo $currentYear . $currentMonth;?>' />
    <input type="hidden" id='periodType' name='periodType' value='month'/>
    <tr>
    <td class="label"><label><?php echo i18n("year");?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:70px; text-align: center; color: #000000;" 
      dojoType="dijit.form.NumberSpinner" 
      constraints="{min:2000,max:2100,places:0,pattern:'###0'}"
      intermediateChanges="true"
      maxlength="4"
      value="<?php echo $defaultYear;?>" smallDelta="1"
      id="yearSpinner" name="yearSpinner" >
      <script type="dojo/method" event="onChange">
        var year=dijit.byId('yearSpinner').get('value');
        var month=dijit.byId('monthSpinner').get('value') + '';
        month=(month.length==1)?'0'+month:month;
        dojo.byId('periodValue').value='' + year + month;
      </script>
    </div></td>
    </tr>
    <tr>
    <td class="label"><label><?php echo i18n("month");?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:55px; text-align: center; color: #000000;" 
       dojoType="dijit.form.NumberSpinner" 
       constraints="{min:0,max:13,places:0,pattern:'00'}"
       intermediateChanges="true"
       maxlength="2"
       value="<?php echo $defaultMonth;?>" smallDelta="1"
       id="monthSpinner" name="monthSpinner" >
       <script type="dojo/method" event="onChange" >
        var year=dijit.byId('yearSpinner').get('value');
        if (this.value==13) {
          dijit.byId('monthSpinner').set('value','01');
          year=parseInt(year)+1;
          dijit.byId('yearSpinner').set('value',year);
        } else if (this.value==0) {
          dijit.byId('monthSpinner').set('value','12');
          year=parseInt(year)-1;
          dijit.byId('yearSpinner').set('value',year);
        }
        var month=dijit.byId('monthSpinner').get('value')+'';
        month=(month.length==1)?'0'+month:month;
        //dijit.byId('monthSpinner').set('value',month);
        dojo.byId('periodValue').value='' + year + month;
       </script>
     </div></td>
     </tr> 
<?php    
  } else if ($param->paramType=='year') {
    $defaultYear='';
    if ($param->defaultValue=='currentYear') {
      $defaultYear=$currentYear;
    }
?>
    <input type="hidden" id='periodValue' name='periodValue' value='<?php echo $currentYear;?>' />
    <input type="hidden" id='periodType' name='periodType' value='year'/>
    <tr>
    <td class="label"><label><?php echo i18n("year");?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:70px; text-align: center; color: #000000;" 
      dojoType="dijit.form.NumberSpinner" 
      constraints="{min:2000,max:2100,places:0,pattern:'###0'}"
      intermediateChanges="true"
      maxlength="4"
      value="<?php echo $defaultYear;?>" smallDelta="1"
      id="yearSpinner" name="yearSpinner" >
      <script type="dojo/method" event="onChange">
        var year=dijit.byId('yearSpinner').get('value');
        dojo.byId('periodValue').value='' + year;
      </script>
    </div></td>
    </tr>
<?php    
  } else if ($param->paramType=='date') {
    $defaultDate='';
    if ($param->defaultValue=='today') {
      $defaultDate=date('Y-m-d');
    } else if ($param->defaultValue) {
      $defaultDate=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td><div style="width:100px; text-align: center; color: #000000;" 
      dojoType="dijit.form.DateTextBox" 
      <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
				echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
      }?>
      invalidMessage="<?php echo i18n('messageInvalidDate');?>" 
      value="<?php echo $defaultDate;?>"
      hasDownArrow="true"
      id="<?php echo $param->name;?>" name="<?php echo $param->name;?>" >
    </div></td>
    </tr>
<?php    
  } else if ($param->paramType=='periodScale') {
    $defaultValue=$param->defaultValue;
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <option value="day" <?php echo ($defaultValue=='day')?'SELECTED':'';?> ><?php echo i18n('day'); ?> </option>
       <option value="week" <?php echo ($defaultValue=='week')?'SELECTED':'';?> ><?php echo i18n('week'); ?> </option>
       <option value="month" <?php echo ($defaultValue=='month')?'SELECTED':'';?> ><?php echo i18n('month'); ?> </option>
       <option value="quarter" <?php echo ($defaultValue=='quarter')?'SELECTED':'';?> ><?php echo i18n('quarter'); ?> </option>
     </select>
    </td>
    </tr>
<?php    
  } else if ($param->paramType=='boolean') {
    $defaultValue=($param->defaultValue=='true' or $param->defaultValue=='1')?true:false;
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <div dojoType="dijit.form.CheckBox" type="checkbox" 
      id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
      style=""
      <?php echo ($defaultValue)?' checked ':'';?> >
    </div>
    </td>
    </tr><?php    
  } else if ($param->paramType=='projectList') {
    $defaultValue='';
    if ($param->defaultValue=='currentProject') {
      if (array_key_exists('project',$_SESSION)) {
        if ($_SESSION['project']!='*') {
          $defaultValue=$_SESSION['project'];
        }
      }
    } else if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idProject', $defaultValue, null, false); ?>
       <script type="dojo/connect" event="onChange" args="evt">
          if (dijit.byId('idVersion')) {
            if (dijit.byId('idProduct')) {
              var idProduct=trim(dijit.byId('idProduct').get('value'));
              if (idProduct) {
                refreshList("idVersion","idProduct", idPoduct);
              } else {
                if (trim(this.value)) {
                  refreshList("idVersion","idProject", this.value);
                } else {
                  refreshList("idVersion");
                }
              }
            } else {
              if (trim(this.value)) {
                refreshList("idVersion","idProject", this.value);
              } else {
                refreshList("idVersion");
              }
            }
          } 
          if (dijit.byId('idProduct')) {
            refreshList("idProduct","idProject", this.value);
          }
          if (dijit.byId('idBaselineSelect')) {
            dijit.byId('idBaselineSelect').set("value",null);
            refreshList("idBaselineSelect","idProject", this.value, null, null, true);
          }

       </script>
     </select>    
    </td>
    </tr>
<?php    
  } else if ($param->paramType=='productList') {
    $defaultValue='';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idProduct', $defaultValue, null, false); ?>
       <script type="dojo/connect" event="onChange" args="evt">
          if (dijit.byId('idVersion')) {
            if (dijit.byId('idProject')) {
              if (trim(this.value)) {
                refreshList("idVersion","idProduct", this.value);
              } else {
                if (trim( dijit.byId("idProject").get("value")) ) {
                  refreshList("idVersion","idProject", dijit.byId("idProject").get("value"));
                } else {
                  refreshList("idVersion");
                }
              }
            } else {
              if (trim(this.value)) {
                refreshList("idVersion","idProduct", this.value);
              } else {
                refreshList("idVersion");
              }
            }
          } 
       </script>
     </select>    
    </td>
    </tr>
<?php 
  } else if ($param->paramType=='userList') {
    $defaultValue='';
    if ($param->defaultValue=='currentUser') {
      if (sessionUserExists()) {
        $defaultValue=$user->id;
      }
    } else if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idUser', $defaultValue, null, false); ?>
     </select>    
    </td>
    </tr>
<?php
  } else if ($param->paramType=='versionList') {
    $defaultValue=$param->defaultValue;
    ?>
  <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
      <select dojoType="dijit.form.FilteringSelect" class="input"
      <?php echo autoOpenFilteringSelect();?>
              style="width: 200px;"
              id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
        >
        <?php htmlDrawOptionForReference('idVersion', $defaultValue, null, false); ?>
      </select>
    </td>
  </tr>
<?php
  } else if ($param->paramType=='testSessionList') {
    $defaultValue=$param->defaultValue;
    ?>
  <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
      <select dojoType="dijit.form.FilteringSelect" class="input"
      <?php echo autoOpenFilteringSelect();?>
              style="width: 200px;"
              id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
        >
        <?php htmlDrawOptionForReference('idTestSession', $defaultValue, null, false); ?>
      </select>
    </td>
  </tr>  
<?php
  } else if ($param->paramType=='resourceList') {
    $canChangeResource=false;
    if ($param->name=='resource' or $param->name=='idResource') {
      $crit=array('idProfile'=>$user->idProfile, 'scope'=>'reportResourceAll');
      $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
      if ($habil and $habil->id and $habil->rightAccess=='1') {
        $canChangeResource=true;
      }
    } else {
      $canChangeResource=true;
    }
    $defaultValue=null;
    if ($param->defaultValue=='currentResource' or (!$param->defaultValue and !$canChangeResource) ) {
      if ($user->isResource) {
        $defaultValue=$user->id;
      }
    } else if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       <?php if (! $canChangeResource) echo ' readonly ';?>
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idResource', $defaultValue, null, false); ?>
     </select>    
    </td>
    </tr>
<?php 
  } else if ($param->paramType=='requestorList') {
    $defaultValue='';
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idContact', $defaultValue, null, false); ?>
     </select>    
    </td>
    </tr>    
<?php 
  } else if ($param->paramType=='milestoneTypeList') {
    $defaultValue='';
    $saveShowMilestoneObj=SqlElement::getSingleSqlElementFromCriteria('Parameter',array('idUser'=>$user->id,'idProject'=>null,'parameterCode'=>'planningShowMilestone'));
    $defaultValue=$saveShowMilestoneObj->parameterValue;
    if (! is_numeric($defaultValue)) $defaultValue=null;
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
      style="width: 150px;"
      name="<?php echo $param->name;?>" id="<?php echo $param->name;?>">
      <option value=" " <?php echo (! $defaultValue)?'SELECTED':'';?>><?php echo i18n("paramNone");?></option>                            
      <?php htmlDrawOptionForReference('idMilestoneType', $defaultValue,null, true);?>
      <option value="all" <?php echo ($defaultValue=='all')?'SELECTED':'';?>><?php echo i18n("all");?></option>
    </select>
    </td></tr>
<?php 
  } else if ($param->paramType=='showDetail') {
    $defaultValue='';
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
      <input dojoType="dijit.form.CheckBox" id="<?php echo $param->name;?>" name="<?php echo $param->name;?>" />
    </td>
    </tr>       
<?php 
  } else if ($param->paramType=='ticketType') {
    $defaultValue='';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
       <?php htmlDrawOptionForReference('idTicketType', $defaultValue, null, false); ?>
     </select>    
    </td>
    </tr>
<?php 
  } else if ($param->paramType=='objectList') {
    $defaultValue='';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
    $arr=SqlList::getListNotTranslated('Importable');
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.FilteringSelect" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name;?>"
     >
     <?php 
       foreach ($arr as $val) {
         echo '<option value="' . $val . '" ';
         if ($val==$defaultValue) {
           echo ' SELECTED '; 
         }  
         echo '>' . i18n($val) . '</option>';
       }
     ?>    
     </select>    
    </td>
    </tr>
<?php 
  } else if ($param->paramType=='id') {
    $defaultValue='';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>#
    <div style="width:60px; text-align: left; color: #000000;" 
      dojoType="dijit.form.TextBox" 
      value="<?php echo $defaultValue;?>"
      id="<?php echo $param->name;?>" name="<?php echo $param->name;?>" >
    </div> 
    </td>
    </tr>
<?php 
  } else if ($param->paramType=='nextPeriod') {
    $defaultValue='10/month';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
    $defList=explode('/',$defaultValue);
    $defaultPeriodValue=$defList[0];
    $defaultPeriodScale=$defList[1];
?>
    <tr style="height:10px;">
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td >
	    <div style="width:20px; text-align: left; color: #000000;" 
	      dojoType="dijit.form.NumberTextBox"
	      constraints="{min:1,max:99}"  
	      value="<?php echo $defaultPeriodValue;?>"
	      id="<?php echo $param->name;?>Value" name="<?php echo $param->name;?>Value" >
	    </div>
	    <div style="border: 1px solid #eeeeee; position: relative; top: -18px; left: 35px;">
		    <input type="radio" data-dojo-type="dijit/form/RadioButton"
		      <?php if ($defaultPeriodScale=="month") { echo 'checked';} ?>
		      name="<?php echo $param->name;?>Scale" id="scaleMonth" value="month"/> 
		    <label for="scaleMonth" class="notLabel"><?php echo i18n('month');?></label> <br />
		    <input type="radio" data-dojo-type="dijit/form/RadioButton"
		      <?php if ($defaultPeriodScale=="week") { echo 'checked';} ?>
		      name="<?php echo $param->name;?>Scale" id="scaleWeek" value="week"/> 
		    <label for="scaleWeek" class="notLabel"><?php echo i18n('week');?></label>
	    </div>
    </td>
    </tr>
<?php 
  } else {
    $defaultValue='';
    if ($param->defaultValue) {
      $defaultValue=$param->defaultValue; 
    }
    $class=(substr($param->paramType,-4,4)=='List')?substr($param->paramType,0,strlen($param->paramType)-4):$param->paramType;
    $class=ucfirst($class);
?>
    <tr>
    <td class="label"><label><?php echo i18n('col' . ucfirst($param->name));?>&nbsp;:&nbsp;</label></td>
    <td>
    <select dojoType="dijit.form.<?php echo (($param->multiple == 1) ? 'MultiSelect' : 'FilteringSelect') ?>" class="input" 
    <?php echo autoOpenFilteringSelect();?>
       style="width: 200px;"
       id="<?php echo $param->name;?>" name="<?php echo $param->name . (($param->multiple == 1) ? '[]' : '');?>"
     >
       <?php htmlDrawOptionForReference($param->name, $defaultValue, null, ($class=='Baseline' || $param->multiple)); ?>
     </select>     
    </td>
    </tr>

<?php 
  }
}
?>
  <tr>
    <td></td>
    <td><div style="position:absolute;top:5px" class="nobr">
      <button title="<?php echo i18n('reportShow')?>"   
         dojoType="dijit.form.Button" type="submit" 
         id="reportSubmit" name="reportSubmit" 
         iconClass="dijitButtonIcon dijitButtonIconDisplay" class="detailButton whiteBackground" showLabel="false"
         onclick="dojo.byId('outMode').value='';runReport();return false;">
      </button>
      <button title="<?php echo i18n('reportPrint')?>"  
         dojoType="dijit.form.Button" type="button"
         id="reportPrint" name="reportPrint"
         iconClass="dijitButtonIcon dijitButtonIconPrint" class="detailButton whiteBackground" showLabel="false">
          <script type="dojo/connect" event="onClick" args="evt">
            dojo.byId('outMode').value='';            
            var fileName=dojo.byId('reportFile').value;
            showPrint("../report/"+ fileName, 'report',null,null,'<?php echo $report->orientation;?>');
          </script>
      </button>
      <button title="<?php echo i18n('reportPrintPdf')?>"  
         dojoType="dijit.form.Button" 
         id="reportPrintPdf" name="reportPrintPdf"
         iconClass="dijitButtonIcon dijitButtonIconPdf" class="detailButton whiteBackground" showLabel="false">
          <script type="dojo/connect" event="onClick" args="evt">
            dojo.byId('outMode').value='pdf';
            var fileName=dojo.byId('reportFile').value;
            //showPrint("../report/"+ fileName, 'report', null, 'pdf');
            if(fileName.lastIndexOf("jsonPlanning.php") != -1){
              showPrint("../report/"+ fileName.substring(0,fileName.indexOf("php")-1) +"_pdf" + fileName.substring(fileName.indexOf("php")-1,fileName.length), 'report', null, 'pdf','<?php echo $report->orientation;?>');
            }else if(fileName.lastIndexOf("jsonResourcePlanning.php") != -1){
              showPrint("../report/"+ fileName.substring(0,fileName.indexOf("php")-1) +"_pdf"+ fileName.substring(fileName.indexOf("php")-1,fileName.length), 'report', null, 'pdf','<?php echo $report->orientation;?>');
            }else{
              showPrint("../report/"+ fileName, 'report', null, 'pdf','<?php echo $report->orientation;?>');
            }
          </script>
      </button>
      <input type="hidden" name="orientation" value="<?php echo $report->orientation;?>" />
      <button title="<?php echo i18n('showInToday')?>"   
         dojoType="dijit.form.Button" type="button" 
         id="reportShowInToday" name="reportShowInToday" 
         iconClass="dijitButtonIcon dijitButtonIconToday" class="detailButton whiteBackground" showLabel="false"
         onclick="saveReportInToday();">
      </button>
      <?php if (isHtml5()) {?>
      <button title="<?php echo i18n('defineAsFavorite')?>"   
         dojoType="dijit.form.Button" type="button" 
         id="reportDefineAsFavorite" name="reportDefineAsFavorite" 
         iconClass="iconReportsFavorite" class="detailButton whiteBackground" showLabel="false"
         onclick="saveReportAsFavorite();">
      </button>
      <?php }?>
        <input type="hidden" id="page" name="page" value="<?php echo ((substr($report->file,0,3)=='../')?'':'../report/') . $report->file;?>"/>
        <input type="hidden" id="print" name="print" value=true />
        <input type="hidden" id="report" name="report" value=true />
        <input type="hidden" id="outMode" name="outMode" value='' />
        <input type="hidden" id="reportName" name="reportName" value="<?php echo i18n($report->name);?>" />
      </dib></td>
  </tr>
</table>
</td><td>&nbsp;
<div id="resultDiv" dojoType="dijit.layout.ContentPane" region="top" style="display:none"></div>
</td></tr></table>
</form>