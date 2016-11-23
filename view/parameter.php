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
 * List of parameter specific to a user.
 * Every user may change these parameters (for his own user only !).
 */
require_once "../tool/projeqtor.php";
scriptLog('   ->/view/parameter.php');

$type=$_REQUEST['type'];
$criteriaRoot=array();
$user=getSessionUser();
$manual=ucfirst($type);

$collapsedList=Collapsed::getCollaspedList();

$parameterList=Parameter::getParamtersList($type);
switch ($type) {
	case ('userParameter'):
		$criteriaRoot['idUser']=$user->id;
		$criteriaRoot['idProject']=null;
		break;
	case ('projectParameter'):
		$criteriaRoot['idUser']=null;
		$criteriaRoot['idProject']=null;
		break;
	case ('globalParameter'):
		$criteriaRoot['idUser']=null;
		$criteriaRoot['idProject']=null;
		break;
	case ('habilitation'):
	case ('habilitationReport'):
	case ('accessRight'):
	case ('accessRightNoProject'):
	case ('habilitationOther'):
		break;
	default:
		traceHack('parameter : unknown parameter type '.$type);
		exit;		 
}

/** =========================================================================
 * Design the html tags for parameter page depending on list of paramters
 * defined in $parameterList
 * @param $objectList array of parameters with format
 * @return void
 */
function drawTableFromObjectList($objectList) { 
	global $criteriaRoot, $type, $collapsedList;
	$displayWidth='98%';
	$longTextWidth="500px";
	$arrayReadOnly=array();
	if ($type=='globalParameter' and (Parameter::getGlobalParameter('imputationUnit')=='hours' or Parameter::getGlobalParameter('workUnit')=='hours') ) {
	  $work=new Work();
	  $cpt=$work->countSqlElementsFromCriteria(array());
	  if ($cpt>0) {
	    $arrayReadOnly['dayTime']=true;
	  }
	}
	if (array_key_exists('destinationWidth',$_REQUEST)) {
	  $width=$_REQUEST['destinationWidth'];
	  $width-=30;
	  $displayWidth=$width . 'px';
	  $longTextWidth=($displayWidth-30-300).'px';
	} else {
	  if (array_key_exists('screenWidth',$_SESSION)) {
	    $detailWidth = round(($_SESSION['screenWidth'] * 0.8) - 15) ; // 80% of screen - split barr - padding (x2)
	  } else {
	    $displayWidth='98%';
	  }
	}
	echo '<table style="width:99%"><tr><td style="width:50%;vertical-align:top;">';
	echo '<div>';
	echo '<table>';
	foreach($objectList as $code => $format) {
		$criteria=$criteriaRoot;
		$criteria['parameterCode']=$code;
		// fetch the parameter saved in Database
		if ($type=='userParameter') {
			$obj=new Parameter();
			$obj->parameterCode=$code;
			$obj->parameterValue=Parameter::getUserParameter($code);
		} else if ($type=='globalParameter') {
			$obj=new Parameter();
			$obj->parameterCode=$code;
			$obj->parameterValue=Parameter::getGlobalParameter($code);
		} else {
		  $obj=SqlElement::getSingleSqlElementFromCriteria('Parameter', $criteria);
		}
		if ($type=='userParameter') { // user parameters may be stored in session
			if (array_key_exists($code,$_SESSION) ) {
				$obj->parameterValue=$_SESSION[$code];
			}
		}
		if ($format=='newColumn') {
			echo '</table></div></td><td style="width:50%;vertical-align:top;"><div><table>';
		} else if ($format=='newColumnFull') {
      echo '</table></div></td></tr><tr><td colspan="2" style="width:50%;vertical-align:top;"><div><table>';
    } else {
			if ($format!="section") {
			  if ($format!='photo') {
				  echo '<tr>';
				  echo '<td class="crossTableLine"><label class="label largeLabel" for="' . $code . '" title="' . i18n('help' . ucfirst($code)) . '">' . i18n('param' . ucfirst($code) ) . ' :&nbsp;</label></td><td>';
			  }
			} else {
				echo '</table></div><br/>';
				$divName=$type.'_'.$code;
				echo '<div id="' . $divName . '" dojoType="dijit.TitlePane"';
				echo ' open="' . (array_key_exists($divName, $collapsedList)?'false':'true') . '"';
				echo ' onHide="saveCollapsed(\'' . $divName . '\');"';
				echo ' onShow="saveExpanded(\'' . $divName . '\');"';
				echo ' title="' . i18n($code) . '" style="width:98%; position:relative;"';
				echo '>';
				echo '<table>';
				echo '<tr>';
			}
			if ($format=='list') {
				$listValues=Parameter::getList($code);
				echo '<select dojoType="dijit.form.FilteringSelect" class="input" name="' . $code . '" id="' . $code . '" ';
				echo autoOpenFilteringSelect();
				echo ' title="' . i18n('help' . ucfirst($code)) . '" style="width:200px">';
				if ($type=='userParameter' or $code=='versionNameAutoformat') {
					echo $obj->getValidationScript($code);
				}
				foreach ($listValues as $value => $valueLabel ) {
					$selected = ($obj->parameterValue==$value)?'selected':'';
					$value=str_replace(',','#comma#',$value); // Comma sets an isse (not selected) when in value
					echo '<option value="' . $value . '" ' . $selected . '>' . $valueLabel . '</option>';
				}
				echo '</select>';
			} else if ($format=='time') {
				echo '<div dojoType="dijit.form.TimeTextBox" ';
				echo ' name="' . $code . '" id="' . $code . '"';
				echo ' title="' . i18n('help' . ucfirst($code)) . '"';
				echo ' type="text" maxlength="5" ';
				echo ' style="width:50px; text-align: center;" class="input" ';
				echo ' value="T' . htmlEncode($obj->parameterValue) . '" ';
				echo ' hasDownArrow="false" ';
				echo ' >';
				echo $obj->getValidationScript($code);
				echo '</div>';
			} else if ($format=='number' or $format=='longnumber') {
				echo '<div dojoType="dijit.form.NumberTextBox" ';
				echo ' name="' . $code . '" id="' . $code . '"';
				echo ' title="' . i18n('help' . ucfirst($code)) . '"';
				echo ($format=='longnumber')?' style="width: 100px;" ':' style="width: 50px;" ';
				//echo ' constraints="{places:\'0\'}" ';
				echo ' class="input" ';
				if (isset($arrayReadOnly[$code])) echo " readonly ";
				echo ' value="' .  htmlEncode($obj->parameterValue)  . '" ';
				echo ' >';
				echo NumberFormatter52::completeKeyDownEvent($obj->getValidationScript($code));
				echo '</div>';
			} else if ($format=='text' or $format=='password') {
				echo '<div dojoType="dijit.form.TextBox" ';
				echo ' name="' . $code . '" id="' . $code . '"';
				echo ' title="' . i18n('help' . ucfirst($code)) . '"';
				echo ' style="width: 200px;" ';
				echo ' class="input" ';
				if ($format=='password') echo ' type="password" ';
				echo ' value="' .  htmlEncode($obj->parameterValue)  . '" ';
				echo ' >';
				echo $obj->getValidationScript($code);
				echo '</div>';
			} else if ($format=='longtext') {
				echo '<textarea dojoType="dijit.form.Textarea" ';
				echo ' name="' . $code . '" id="' . $code . '"';
				echo ' title="' . i18n('help' . ucfirst($code)) . '"';
				echo ' style="width: '.$longTextWidth.';" ';
				echo ' class="input" ';
				echo ' >';
				echo $obj->parameterValue;
				//echo $obj->getValidationScript($code);
				echo '</textarea>';
			} else if ($format=='photo') { // for user photo 
			  echo "</td></tr>";
			  $user=getSessionUser();
			  $user->drawSpecificItem('image');
			  echo '<input type="hidden" id="objectId" value="'.htmlEncode($user->id).'"/>';
			  echo '<input type="hidden" id="objectClass" value="User"/>';
			  echo '<input type="hidden" id="parameter" value="true"/>';
			  echo "<tr><td></td><td>";
			  echo '<div style="position:relative;top:0px;left:0px;height:85px;">&nbsp;</div>';
			} else if ($format=='specific') {
			  if ($code=='password') {
			    $title=i18n('changePassword');
			    echo '<button id="changePassword" dojoType="dijit.form.Button" showlabel="true"';
			    if (0) {
			      $result .= ' disabled="disabled" ';
			    }
			    echo ' title="' . $title . '" style="vertical-align: middle;">';
			    echo '<span>' . $title . '</span>';
			    echo '<script type="dojo/connect" event="onClick" args="evt">';
			    echo ' requestPasswordChange();';
			    echo '</script>';
			    echo '</button>';
			  } else if ($code=='markAlertsAsRead') {
			    $title=i18n('helpMarkAlertsAsRead');
			    echo '<button id="markAlertsAsRead" dojoType="dijit.form.Button" showlabel="true"';
			    echo ' title="' . $title . '" style="vertical-align: middle;">';
			    echo '<span>' . i18n('paramMarkAlertsAsRead') . '</span>';
			    echo '<script type="dojo/connect" event="onClick" args="evt">';
			    echo ' maintenance("read","Alert");';
			    echo '</script>';
			    echo '</button>';
			  }
			}
			echo '</td></tr>';
		}
	}
	echo '</table>';
	echo '</td></tr></table>';
}
?>
<input
  type="hidden" name="objectClassManual" id="objectClassManual"
  value="<?php echo $manual;?>" />
<div class="container" dojoType="dijit.layout.BorderContainer">
<div id="parameterButtonDiv" class="listTitle" style="z-index:3;overflow:visible"
  dojoType="dijit.layout.ContentPane" region="top">
  <div id="resultDiv" dojoType="dijit.layout.ContentPane"
      region="top" style="padding:5px;padding-bottom:20px;max-height:100px;padding-left:300px;z-index:999"></div>
<table width="100%">
  <tr height="100%" style="vertical-align: middle;">
    <td width="50px" align="center"><?php echo formatIcon(ucfirst($type), 32, null, true);?></td>
    <td><span class="title"><?php echo str_replace(" ","&nbsp;",i18n('menu'.ucfirst($type)))?>&nbsp;</span>
    </td>
    <td width="10px">&nbsp;</td>
    <td width="50px">
    <button id="saveParameterButton" dojoType="dijit.form.Button"
      showlabel="false"
      title="<?php echo i18n('buttonSaveParameters');?>"
      iconClass="dijitButtonIcon dijitButtonIconSave" class="detailButton"><script
      type="dojo/connect" event="onClick" args="evt">
        	submitForm("../tool/saveParameter.php","resultDiv", "parameterForm", true);
          </script></button>
    <div dojoType="dijit.Tooltip" connectId="saveButton"><?php echo i18n("buttonSaveParameter")?></div>
    </td>
    <td style="position:relative;">
    
    </td>
  </tr>
</table>
</div>
<div id="formDiv" dojoType="dijit.layout.ContentPane" region="center"
  style="overflow-y: auto; overflow-x: hidden;">
<form dojoType="dijit.form.Form" id="parameterForm" jsId="parameterForm"
  name="parameterForm" encType="multipart/form-data" action="" method=""><input
  type="hidden" name="parameterType" value="<?php echo $type;?>" /> <?php 
  if ($type=='habilitation') {
  	htmlDrawCrossTable('menu', 'idMenu', 'profile', 'idProfile', 'habilitation', 'allowAccess', 'check', null,'idMenu') ;
  } else if ($type=='accessRight') {
  	htmlDrawCrossTable('menuProject', 'idMenu', 'profile', 'idProfile', 'accessRight', 'idAccessProfile', 'list', 'accessProfile', 'idMenu');
  } else if ($type=='accessRightNoProject') {
  	$titlePane="habilitation_ReadWriteEnvironment";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('menuEnvironmentalParameter') . '">';
  	htmlDrawCrossTable('menuReadWriteEnvironment', 'idMenu', 'profile', 'idProfile', 'accessRight', 'idAccessProfile', 'list', 'listReadWrite') ;
  	echo '</div><br/>';
  	$titlePane="habilitation_ReadWriteList";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('menuListOfValues') . '">';
  	htmlDrawCrossTable('menuReadWriteList', 'idMenu', 'profile', 'idProfile', 'accessRight', 'idAccessProfile', 'list', 'listReadWrite') ;
  	echo '</div><br/>';
  	$titlePane="habilitation_ReadWriteType";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('menuType') . '">';
  	htmlDrawCrossTable('menuReadWriteType', 'idMenu', 'profile', 'idProfile', 'accessRight', 'idAccessProfile', 'list', 'listReadWrite') ;
  	echo '</div><br/>';
  } else if ($type=='habilitationReport') {
  	htmlDrawCrossTable('report', 'idReport', 'profile', 'idProfile', 'habilitationReport', 'allowAccess', 'check', null, 'idReportCategory') ;
  } else if ($type=='habilitationOther') {
  	$titlePane="habilitationOther_Imputation";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionImputationDiary') . '">';
  	htmlDrawCrossTable(array('imputation'=>i18n('imputationAccess'), 
  	                         'workValid'=>i18n('workValidate'),
  	                         'diary'=>i18n('diaryAccess'),
  	                         //'expense'=>i18n('resourceExpenseAccess')
  	                         ), 
  	    'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'accessScope') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_WorkCost";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionWorkCost') . '">';
  	htmlDrawCrossTable(array('work'=>i18n('workAccess'),'cost'=>i18n('costAccess')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'visibilityScope') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_AssignmentManagement";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionAssignmentManagement') . '">';
  	htmlDrawCrossTable(array('assignmentView'=>i18n('assignmentViewRight'),'assignmentEdit'=>i18n('assignmentEditRight')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_Buttons";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionButtons') . '">';
  	htmlDrawCrossTable(array('combo'=>i18n('comboDetailAccess'),'checklist'=>i18n('checklistAccess')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_PlanningRight";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionPlanningRight') . '">';
  	htmlDrawCrossTable(array('planning'=>i18n('planningRight'),'resourcePlanning'=>i18n('resourcePlanningRight')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_Unlock";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionUnlock') . '">';
  	htmlDrawCrossTable(array('document'=>i18n('documentUnlockRight'),'requirement'=>i18n('requirementUnlockRight')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_Report";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionReport') . '">';
  	htmlDrawCrossTable(array('reportResourceAll'=>i18n('reportResourceAll')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_Delete";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('sectionDelete') . '">';
  	htmlDrawCrossTable(array('canForceDelete'=>i18n('canForceDelete'),'canUpdateCreation'=>i18n('canUpdateCreationInfo'),'viewComponents'=>i18n('viewComponents')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listYesNo') ;
  	echo '</div><br/>';
  	$titlePane="habilitationOther_ResourceVisibility";
  	echo '<div dojoType="dijit.TitlePane"';
  	echo ' open="' . ( array_key_exists($titlePane, $collapsedList)?'false':'true') . '"';
  	echo ' id="' . $titlePane . '" ';
  	echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
  	echo ' onShow="saveExpanded(\'' . $titlePane . '\');"';
  	echo ' title="' . i18n('resourceVisibility') . '">';
  	htmlDrawCrossTable(array('resVisibilityList'=>i18n('resourceVisibilityList'),'resVisibilityScreen'=>i18n('resourceVisibilityScreen')), 'scope', 'profile', 'idProfile', 'habilitationOther', 'rightAccess', 'list', 'listTeamOrga') ;
  	echo '</div><br/>';
  } else {
  	drawTableFromObjectList($parameterList);
  }
  ?></form>
</div>
</div>
