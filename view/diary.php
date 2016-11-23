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
  require_once "../tool/formatter.php";
  scriptLog('   ->/view/diary.php');   

  if (! isset($destinationHeight)) {
    $destinationHeight=$_REQUEST['destinationHeight'];
  }
  
  $cpt=0;
  $arrayActivities=array(); // Array of activities to display
  $idRessource=getSessionUser()->id;
  $showDone=false;
  $showIdle=false;
  if (! isset($period)) {
  	$period=htmlentities($_REQUEST['diaryPeriod']);
    $year=htmlentities($_REQUEST['diaryYear']);
    $month=htmlentities($_REQUEST['diaryMonth']);
    $week=htmlentities($_REQUEST['diaryWeek']);
    $day=htmlentities($_REQUEST['diaryDay']);
    Parameter::storeUserParameter("diaryPeriod",$period);
    $idRessource=$_REQUEST['diaryResource'];
    $showIdle=(isset($_REQUEST['showIdle']))?true:false;
    $showDone=(isset($_REQUEST['showDone']))?true:false;
  }

  $ress=new Resource($idRessource);
  $calendar=$ress->idCalendarDefinition;
  $weekDaysCaption=array(
  		1=>i18n("Monday"),
  		2=>i18n("Tuesday"),
  		3=>i18n("Wednesday"),
  		4=>i18n("Thursday"),
  		5=>i18n("Friday"),
  		6=>i18n("Saturday"),
  		7=>i18n("Sunday"),
  );
  $projectColorArray=array();
  $projectNameArray=array();
  $totalHeight=$destinationHeight;
  $trHeight=$totalHeight;
  if ($period=="month") {
  	$week=weekNumber($year.'-'.$month.'-01');
  	$lastWeek=weekNumber($year.'-'.$month.'-'.intval(date("t",$month)));
	  if ($lastWeek>$week) {
		  $trHeight=floor(($totalHeight-10)/($lastWeek-$week+1))-1;
	  } else {
		  $trHeight=floor(($totalHeight-10)/($lastWeek+1))-1;
	  }
  } else if ($period=="week") {
    $trHeight=$totalHeight-10;
  } else if ($period=="day") {
    $trHeight=$totalHeight;
  }
  if ($period=="month") {
    if ($month=='01' and $week>50) {
      $currentDay=date('Y-m-d',firstDayofWeek($week,$year-1));
    } else {
  	  $currentDay=date('Y-m-d',firstDayofWeek($week,$year));
    }
  	$lastDayOfMonth=date('t',strtotime($year.'-'.$month.'-01'));
	$weekOfLastDayOfMonth=date('W',strtotime($year.'-'.$month.'-'.$lastDayOfMonth));
  	$firstDayOfLastWeek=date('Y-m-d',firstDayofWeek($weekOfLastDayOfMonth, $year ));
  	$endDay=addDaysToDate($firstDayOfLastWeek, 6);
  	$inScopeDay=false;	
  } else if ($period=="week") {
  	$currentDay=date('Y-m-d',firstDayofWeek($week,$year));
  	$endDay=addDaysToDate($currentDay, 6);
  	$inScopeDay=true;
  } else if ($period=="day") {
  	$currentDay=$day;
  	$endDay=$currentDay;
  	$inScopeDay=true;
  }
  
  echo '<TABLE style="width:100%;height:'.$totalHeight.'px">';
  
  if ($period!='day') {
    echo '<tr height="10px"><td></td>';
    for ($i=1; $i<=7;$i++) {
  	  echo '<td class="section" style="width: 14%;">'.$weekDaysCaption[$i].'</td>';
    }
  } else {
  	echo '<tr height="0px"><td></td>';
  	$trHeight=$totalHeight+10;
  }
  $arrayActivities=getAllActivities($currentDay, $endDay, $idRessource,$showDone,$showIdle);
  drawDiaryLineHeader($currentDay, $trHeight,$period); 
  while ($currentDay<=$endDay) {
  	if ($period=="month") {
  		if (substr($currentDay,5,2)==$month) {
  			$inScopeDay=true;
  		} else {
  			$inScopeDay=false;
  		}
  	}
  	echo '<td style="width: '.(($period=='day')?'100':'14').'%; border: 1px solid #AAAAAA;background-color:'.(($inScopeDay)?'white':'transparent').'">';
  	drawDay($currentDay,$idRessource,$inScopeDay,$period,$calendar); 
  	$currentDay=addDaysToDate($currentDay, 1);
  	if ($currentDay<=$endDay and date('N', strtotime($currentDay))==1) {
      drawDiaryLineHeader($currentDay, $trHeight,$period);
  	}
  }
  echo '</tr></TABLE>';
  
function drawDay($date,$ress,$inScopeDay,$period,$calendar=1) {
	global $cpt, $trHeight;
	$dayHeight=$trHeight;
	if ($period=='month') {
	  $dayHeight-=15;
	}
	echo '<table style="width:100%; height: 100%;'.(($date==date('Y-m-d'))?'border:0px solid #555555;':'').'">';
	if ($period!='day') {
		echo '<tr style="height:10px">';
		echo '<td class="report'.(($date==date('Y-m-d'))?'Table':'').'Header" style="padding:0;cursor: pointer;'.((!$inScopeDay)?'color:#AAAAAA':'').'"';
		echo ' onClick="diaryDay(\''.$date.'\');" >';
		//echo $date.'/';
		echo substr($date,8,2);
		echo '</td>';
		echo '</tr>';
	}
	echo '<tr >';
	$bgColor="#FFFFFF";
	if ($date==date('Y-m-d')) { 
		$bgColor="#ffffaa"; 
	} else if (isOffDay($date,$calendar)) {
		$bgColor="#dfdfdf";
	}
	
	echo '<td style="vertical-align:top;background-color:'.$bgColor.';">';
	echo '<div style="overflow-y: auto; overflow-x:hidden; height:'.$dayHeight.'px;max-height:">';
	echo '<table style="width:100%;">';
	$lst=getActivity($date);
	foreach ($lst as $item) {
		$cpt++;
		$hint=i18n($item['class']).' #'.$item['id']."\n"
				.$item['name']."\n"
				.i18n('colIdProject').": ".$item['projectName'];
		$hintHtml=i18n($item['class']).' #'.$item['id']."<br/>"
				.'<b>'.$item['name']."</b><br/>"
				.i18n('colIdProject').": <i>".$item['projectName'].'</i><br/>';
		if ($item['date']) { $hintHtml.=i18n('colDate').": <i>".$item['date']."</i>"; }
		if ($item['work'] and $item['real']) { $hintHtml.=i18n('colRealWork').": ".Work::displayWorkWithUnit($item['work']).""; }
		if ($item['work'] and ! $item['real']) { $hintHtml.=i18n('planned').": <i>".Work::displayWorkWithUnit($item['work'])."</i>"; }
		echo '<tr>';
		echo '<td style="padding: 3px 3px 0px 3px; width:100%;position:relative;">';
		echo '<div id="item_'.$cpt.'" style="border:1px solid: #EEEEEE; box-shadow: 2px 2px 4px #AAAAAA; width: 100%;background-color:'.$item['color'].'">';
		echo '<table style="width:100%"><tr><td style="vertical-align:top;width:20px">';		
		$attr=((! $item['real'])?'':' style="top:0px;background-color:#ffffff;opacity:0.5;filter:alpha(opacity=50);"');
		echo '<span '.$attr.'>'.formatIcon($item['class'], 16,null,true).'</span>';
		echo '</td><td style="color:#555555">';
		//Modification ici , typename ne marche pas...
		
		echo '<div style="cursor:pointer;color:'.getForeColor($item['color']).'" onClick="gotoElement(\''.$item['class'].'\', '.$item['id'].', false);" >';
		if ($item['real']) {
		  echo $item['name'];
		} else {
			echo '<i>'.$item['name'].'</i>';
		}
		if ($period=='week' or $period=='day') {
		  echo '<table style="vertical-align:top;width:100%">';
		  if ($item['projectName']) echo '<tr><td style="width:50px;text-align:right;font-weight:bold;vertical-align:top;">'.i18n('colIdProject').'&nbsp;:&nbsp;</td><td>'.$item['projectName'].'</td></tr>';
		  if ($item['typeName']) echo '<tr><td style="text-align:right;font-weight:bold;vertical-align:top;">'.i18n('colType').'&nbsp;:&nbsp;</td><td>'.$item['typeName'].'</td></tr>';
		  if ($item['priorityName'])echo '<tr><td style="text-align:right;font-weight:bold;vertical-align:top;">'.i18n('colIdPriority').'&nbsp;:&nbsp;</td><td>'.$item['priorityName'].'</td></tr>';
		  if ($item['responsibleName'])echo '<tr><td style="text-align:right;font-weight:bold;vertical-align:top;">'.i18n('colResponsible').'&nbsp;:&nbsp;</td><td>'.$item['responsibleName'].'</td></tr>';
		  echo '</table>';
		}
		if ($period=='day') {
		  echo '<div style="padding:5px;max-height:200px;width:100%;border:1px solid #A0A0A0;margin-top:5px;margin-bottom:5px;">'.$item['description'].'</div>';
		}
		echo '<div style="width:100%;height:24px;float:left;position:relative;left:-18px;padding-top:2px">';
		echo '   <div style="float:left;min-width:22px;height:22px;position:relative;margin-top:5px;">#'.$item['id'].'</div>';
		echo '   <div style="float:left;width:22px;height:22px;position:relative;padding-left:5px;">'.formatColorThumb("idPriority",$item['priorityId'], 22, 'left', i18n('colIdPriority').' : '.$item['priorityName']).'</div>';
		echo '   <div style="float:left;padding-left:5px;width:22px;height:22px;position:relative;" id="userThumbMilestone'.$item['id'].'">'.formatUserThumb($item['responsibleId'], i18n('colResponsible').' : '.$item['responsibleName'], "", 22, 'left', false).'</div>';
		echo '   <div style="float:left;max-width:100px;height:22px;position:relative;padding-left:5px;top:-1px">'.colorNameFormatter($item['statusName'].'#split#'.SqlList::getFieldFromId('Status', $item['statusId'], 'color')).'</div>';
		echo '</div>';
		echo '</div>';
		echo '</td></tr></table>';
		echo '</div>';
		// To display a tooltip in replacement of Hint
		echo '<div dojoType="dijit.Tooltip" connectId="item_'.$cpt.'" position="above">';
		echo $hintHtml;
		echo '</div>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';	
	echo '</div>';
	echo '</td>';		
	echo '</tr>';
	echo '</table>';
}

function getActivity($date) {
	global $arrayActivities;
	if (array_key_exists($date, $arrayActivities)) {
		return $arrayActivities[$date];
	} else {
		return array();
	}
}

function getAllActivities($startDate, $endDate, $ress, $showDone=false, $showIdle=false) {
	global $projectColorArray, $projectNameArray, $allActi;
	$result=array();
	// 
	$arrObj=array(new Action(), new Ticket(), new MilestonePlanningElement());
	foreach ($arrObj as $obj) {
		$critWhere="idResource=".Sql::fmtId($ress);
		if (!$showDone) {
		  $critWhere.=" and done=0 ";
		}
		if (!$showIdle) {
		  $critWhere.=" and idle=0 ";
		}
		if (property_exists($obj, 'actualDueDate') and property_exists($obj, 'initialDueDate')) {
		  $critWhere.=" and ( "
		   ." (actualDueDate>='$startDate' and actualDueDate<='$endDate') "
		   ." or ( actualDueDate is null and (initialDueDate>='$startDate' and initialDueDate<='$endDate') )"
		   ." )";
	  } else if (property_exists($obj, 'actualDueDateTime') and property_exists($obj, 'initialDueDateTime')) {
		  $critWhere.=" and ( "
		   ." (actualDueDateTime>='$startDate 00:00:00' and actualDueDateTime<='$endDate 23:59:59') "
		   ." or ( actualDueDateTime is null and (initialDueDateTime>='$startDate 00:00:00' and initialDueDateTime<='$endDate 23:59:59') )"
	     ." )";
		} else if (property_exists($obj, 'validatedEndDate') ) {
			if ($ress==getSessionUser()->id) {
				$critWhere=" refType='MileStone' and validatedEndDate>='$startDate' and validatedEndDate<='$endDate' ";
				$critWhere.=" and idProject in ".transformListIntoInClause(getSessionUser()->getVisibleProjects(true));
			} else {
				$critWhere="1=0";
			}
	  } else {
	  	$critWhere.=" and 1=0";
	  }
		$lst=$obj->getSqlElementsFromCriteria(null,false,$critWhere);
		foreach ($lst as $o) {	  
			if (array_key_exists($o->idProject,$projectColorArray)) {
				$color=$projectColorArray[$o->idProject];
				$projectId=$o->idProject;
				$projectName=$projectNameArray[$o->idProject];
			} else {
				$pro=new Project($o->idProject);
				$color=$pro->color;
				$projectId=$pro->id;
				$projectName=$pro->name;
				$projectColorArray[$o->idProject]=$color;
				$projectNameArray[$o->idProject]=$projectName;
			}
			$typeName=null;
			$typeId=null;
			$type='id'.get_class($o).'Type';
			if (property_exists($o,$type)) {
			  $typeId=$o->$type;
			  $typeName=SqlList::getNameFromId('Type', $o->$type);
			}
			$priorityName=null;
			$priorityId=null;
			if (property_exists($o,'idPriority')) {
			  $priorityId=$o->idPriority;
			  $priorityName=SqlList::getNameFromId('Priority', $o->idPriority);
			}
			$responsibleName=null;
			$responsibleId=null;
			if (property_exists($o,'idResource')) {
			  $responsibleId=$o->idResource;
			  $responsibleName=SqlList::getNameFromId('Affectable', $o->idResource);
			}
			$statusName=null;
			$statusId=null;
			if (property_exists($o,'idStatus')) {
			  $statusId=$o->idStatus;
			  $statusName=SqlList::getNameFromId('Status', $o->idStatus);
			}
			$description=null;
			if (property_exists($o,'description')) {
			  $description=$o->description;
			}
			$date=null;
			$dateField="";
			$name="";
			$id=$o->id;
			$class=get_class($o);
			if (property_exists($obj, 'actualDueDate') and property_exists($obj, 'initialDueDate')) {
				if ($o->actualDueDate) {
					$date=$o->actualDueDate;
					$dateField=i18n('colActualDueDate');
				} else {
					$date=$o->initialDueDate;
					$dateField=i18n('colInitialDueDate');
				}
				$name=$o->name;
			} else if (property_exists($obj, 'actualDueDateTime') and property_exists($obj, 'initialDueDateTime')) {
				if ($o->actualDueDateTime) {
					$date=substr($o->actualDueDateTime,0,10);
					$dateField=i18n('colActualDueDate');
				} else {
					$date=substr($o->initialDueDateTime,0,10);
					$dateField=i18n('colInitialDueDate');
				}
				$name=$o->name;
			} else if (property_exists($obj, 'validatedEndDate')) {
				$name=$o->refName;
				$id=$o->refId;
				$class=$o->refType;
				$date=$o->validatedEndDate;
				$dateField=i18n('colValidatedEndDate');
			}
			if ($date) {
				if (!array_key_exists($date, $result)) {
					$result[$date]=array();
				}				
				
				$result[$date]["$class#$id"]=array(
						'class'=>$class,
						'id'=>$o->id,
						'work'=>0,
				    'real'=>false,
						'name'=>$name,
						'color'=>$color,
						'date'=>$dateField,
				    'projectId'=>$projectId,
						'projectName'=>$projectName,
				    'typeId'=>$typeId,
				    'typeName'=>$typeName,
				    'priorityId'=>$priorityId,
				    'priorityName'=>$priorityName,
				    'responsibleId'=>$responsibleName,
				    'responsibleName'=>$responsibleName,
				    'statusId'=>$statusId,
				    'statusName'=>$statusName,
				    'description'=>$description
				);
			}
		}
	}
	// Planned Activities and real work
	$pw=new PlannedWork();
	$w=new Work();
	$critWhere="idResource=".Sql::fmtId($ress);
	$critWhere.=" and workDate>='$startDate' and workDate<='$endDate'";
	$pwList=$pw->getSqlElementsFromCriteria(null,false,$critWhere);
	$wList=$w->getSqlElementsFromCriteria(null,false,$critWhere);
	$workList=array_merge($pwList,$wList);
	//KEVIN
	foreach ($workList as $pw) {
		$item=new $pw->refType($pw->refId);
		if ($item->done and !$showDone) continue;
		if ($item->idle and !$showIdle) continue;
		if ($pw->refType=='Meeting') {
			$display=substr($item->meetingStartTime,0,5).' - '.htmlEncode($item->name);
		} else if (get_class($pw)=='Work') {
				$display='['.Work::displayWorkWithUnit($pw->work).'] '.htmlEncode($item->name);
		} else {
		  $display='<i>('.Work::displayWorkWithUnit($pw->work).')</i> '.htmlEncode($item->name);		  
		}
		if (array_key_exists($item->idProject,$projectColorArray)) {
			$color=$projectColorArray[$item->idProject];
			$projectId=$item->idProject;
			$projectName=$projectNameArray[$item->idProject];
		} else {
			$pro=new Project($item->idProject);
			$color=$pro->color;
			$projectId=$item->idProject;
			$projectName=$pro->name;
			$projectColorArray[$item->idProject]=$color;
			$projectNameArray[$item->idProject]=$projectName;
		}
		$date=$pw->workDate;
		if (!array_key_exists($date, $result)) {
			$result[$date]=array();
		}
		$typeId=null;
		$typeName=null;
		$type='id'.get_class($item).'Type';
		if (property_exists($item,$type)) {
		  $typeId=$item->$type;
		  $typeName=SqlList::getNameFromId('Type', $item->$type);
		}
		$priorityId=null;
		$priorityName=null;
		if (property_exists($item,'idPriority')) {
		  $priorityId=$item->idPriority;
		  $priorityName=SqlList::getNameFromId('Priority', $item->idPriority);
		}
		$responsibleId=null;
		$responsibleName=null;
		if (property_exists($item,'idResource')) {
		  $responsibleId=$item->idResource;
		  $responsibleName=SqlList::getNameFromId('Affectable', $item->idResource);
		}
		$statusName=null;
		$statusId=null;
		if (property_exists($item,'idStatus')) {
		  $statusId=$item->idStatus;
		  $statusName=SqlList::getNameFromId('Status', $item->idStatus);
		}
		$description=null;
		if (property_exists($obj,'description')) {
		  $description=$obj->description;
		}
		$result[$date][$pw->refType.'#'.$pw->refId]=array(
				'class'=>$pw->refType,
		    'id'=>$pw->refId,
				'work'=>$pw->work,
		    'real'=>((get_class($pw)=='Work')?true:false),
				'name'=>$item->name,
				'color'=>$color,
		    'date'=>"",
				'projectId'=>$projectId,
				'projectName'=>$projectName,
				'typeId'=>$typeId,
				'typeName'=>$typeName,
				'priorityId'=>$priorityId,
				'priorityName'=>$priorityName,
				'responsibleId'=>$responsibleName,
				'responsibleName'=>$responsibleName,
		    'statusId'=>$statusId,
		    'statusName'=>$statusName,
		    'description'=>$description
		);
	}
	return $result;
}

function drawDiaryLineHeader($currentDay, $trHeight,$period) {
	echo '</tr>';
	echo '<tr height="'.$trHeight.'px"><td class="buttonDiary" ';
	if ($period=="month") {
	  echo 'onClick="diaryWeek('.weekNumber($currentDay).','.substr($currentDay,0,4).');"';
	} else if ($period=="week") {
		echo 'onClick="diaryMonth('.substr($currentDay,5,2).','.substr($currentDay,0,4).');"';
	} else if ($period=="day") {
		echo 'onClick="diaryWeek('.weekNumber($currentDay).','.substr($currentDay,0,4).');"';
	}	
	echo '>';
	if ($period=='week') {
		$month=substr($currentDay,5,2);
		$monthArr=array(i18n("January"),i18n("February"),i18n("March"),
				i18n("April"), i18n("May"),i18n("June"),
				i18n("July"), i18n("August"), i18n("September"),
				i18n("October"),i18n("November"),i18n("December"));
		$dispMonth=(mb_strlen($monthArr[$month-1],'UTF-8')>4)?mb_substr($monthArr[$month-1],0,4,'UTF-8').'.':$monthArr[$month-1];
		echo '<div style="font-size:80%">'.$dispMonth.'</div>';
	} else {
	  echo '<div >'.weekNumber($currentDay).'</div>';
	}
	if ($period=="month") {
		echo '<img src="../view/css/images/right.png" /></td>';
	} else {
		echo '<img src="../view/css/images/left.png" /></td>';
	}
}
?>

