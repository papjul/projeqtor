<?php
include_once "../tool/projeqtor.php";

$class=null;
if (isset($_REQUEST['class'])) {
  $class=$_REQUEST['class'];
}
Security::checkValidClass($class);

$id=null;
if (isset($_REQUEST['id'])) {
  $id=$_REQUEST['id'];
}
Security::checkValidId($id);
$scale='day';
if (isset($_REQUEST['scale'])) {
  $scale=$_REQUEST['scale'];
}
if ($scale!='day' and $scale!='week') {
  echo '<div style="background-color:#FFF0F0;padding:3px;border:1px solid #E0E0E0;">'.i18n('ganttDetailScaleError')."</div>";
  return;
}

$dates=array();
$work=array();
$start=null;
$end=null;

$crit=array('refType'=>$class,'refId'=>$id);

$pe=SqlElement::getSingleSqlElementFromCriteria($class.'PlanningElement', $crit);
if ($pe->assignedWork==0 and $pe->leftWork==0 and $pe->realWork==0) {
  echo '<div style="background-color:#FFF0F0;padding:3px;border:1px solid #E0E0E0;">'.i18n('noDataToDisplay')."</div>";
  return;
}

$wk=new Work();
$wkLst=$wk->getSqlElementsFromCriteria($crit);
foreach($wkLst as $wk) {
  $dates[$wk->workDate]=$wk->workDate;
  if (!$start or $start>$wk->workDate) $start=$wk->workDate;
  if (!$end or $end<$wk->workDate) $end=$wk->workDate;
  if (! isset($work[$wk->idAssignment])) $work[$wk->idAssignment]=array();
  if (! isset($work[$wk->idAssignment]['resource'])) {
    $work[$wk->idAssignment]['resource']=SqlList::getNameFromId('Resource', $wk->idResource);
    $work[$wk->idAssignment]['capacity']=SqlList::getFieldFromId('Resource', $wk->idResource,'capacity');
  }
  $work[$wk->idAssignment][$wk->workDate]=array('work'=>$wk->work,'type'=>'real');
}

$wk=new PlannedWork();
$wkLst=$wk->getSqlElementsFromCriteria($crit);
foreach($wkLst as $wk) {
  $dates[$wk->workDate]=$wk->workDate;
  if (!$start or $start>$wk->workDate) $start=$wk->workDate;
  if (!$end or $end<$wk->workDate) $end=$wk->workDate;
  if (! isset($work[$wk->idAssignment])) $work[$wk->idAssignment]=array();
  if (! isset($work[$wk->idAssignment]['resource'])) {
    $work[$wk->idAssignment]['resource']=SqlList::getNameFromId('Resource', $wk->idResource);
    $work[$wk->idAssignment]['capacity']=SqlList::getFieldFromId('Resource', $wk->idResource,'capacity');
  }
  if (! isset($work[$wk->idAssignment][$wk->workDate]) ) {
    $work[$wk->idAssignment][$wk->workDate]=array('work'=>$wk->work,'type'=>'planned');
  }
}
$dt=$start;
while ($dt<=$end) {
  if (!isset($dates[$dt])) {
    $dates[$dt]=$dt;
  }
  $dt=addDaysToDate($dt, 1);
}
ksort($dates);

$width=20;
echo '<table id="planningBarDetailTable" style="background-color:#FFFFFF;border-collapse: collapse;marin:0;padding:0">';
foreach ($work as $res) {
  echo '<tr style="height:20px;border:1px solid #505050;">';
  foreach ($dates as $dt) {
    $color="#ffffff";
    $height=0; $w=0;
    $capacity=$res['capacity'];
    if ($capacity==0) $capacity=1;
    if (isset($res[$dt])) {
      $w=$res[$dt]['work'];       
      if (!$pe->validatedEndDate or $dt<=$pe->validatedEndDate) {
        $color=($res[$dt]['type']=='real')?"#507050":"#50BB50";  
      } else {
        $color=($res[$dt]['type']=='real')?"#705050":"#BB5050";
      }
      
      $height=round($w*20/$capacity,0);
    }
    echo '<td style="padding:0;width:'.$width.'px;border-right:1px solid #eeeeee;position:relative;">'
        .'<div style="display:block;background-color:'.$color.';position:absolute;bottom:0px;left:0px;width:100%;height:'.$height.'px;"></div>'
        .'</td>';
  }
  echo '<td style="border-left:1px solid #505050;"><div style="width:200px; max-width:200px;overflow:hidden; text-align:left">&nbsp;'.$res['resource'].'&nbsp;</div></td>';
  echo '</tr>';
}
echo '</table>';