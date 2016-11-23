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
require_once("../tool/projeqtor.php");

function generateImputationAlert() {
  $endDate=date('Y-m-d');
  // imputationAlertGenerationDay => managed by CRON
  // imputationAlertGenerationHour => managed by CRON
  $controlDay=Parameter::getGlobalParameter('imputationAlertControlDay');
  if (!$controlDay) {
    traceLog("generationImputationAlert() - No control day defined - Exiting");
    return;
  }
  if ($controlDay=='next') {
    $endDate=addDaysToDate($endDate, 1);
  } else if ($controlDay=='previous') {
    $endDate=addDaysToDate($endDate, -1);
  } // else = current => nothing to do
    
  $numberOfDays=Parameter::getGlobalParameter('imputationAlertControlNumberOfDays');
  if ($numberOfDays=="" or $numberOfDays==null) {
    traceLog("generationImputationAlert() - No number of days defined - Exiting");
    return;
  }
  $startDate=addDaysToDate($endDate, (-1)*($numberOfDays-1));
    
  $sendToResource=Parameter::getGlobalParameter('imputationAlertSendToResource');
  $sendToProjectLeader=Parameter::getGlobalParameter('imputationAlertSendToProjectLeader');
  $sendToTeamManager=Parameter::getGlobalParameter('imputationAlertSendToTeamManager');

  $lstResource=SqlList::getList('Resource');
  $lstProject=SqlList::getList('Project');
  
  // Initialize list of resources
  $lstRes=array();
  foreach ($lstResource as $id=>$name) {
    $lstRes[$id]=array(
        'name'=>$name, 
        'full'=>false, 
        'days'=>array(), 
        'capacity'=>SqlList::getFieldFromId('Resource', $id, 'capacity'),
        'projects'=>array()
    );
    // Initialize list of days for the period
    $tmpDate=$startDate;
    while ($tmpDate<=$endDate) {
      $lstRes[$id]['days'][$tmpDate]=array(
          'open'=>isOpenDay($tmpDate,SqlList::getFieldFromId('Resource', $id, 'idCalendarDefinition')),
          'work'=>0
      );
      $tmpDate=addDaysToDate($tmpDate, 1);
    }
    // Store projects the resource is affected to
    $aff=new Affectation();
    $lstAff=$aff->getSqlElementsFromCriteria(array('idResource'=>$id,'idle'=>'0'));
    foreach ($lstAff as $aff) {
      if ( (! $aff->startDate or $aff->startDate<=$endDate) and (! $aff->endDate or $aff->endDate>=$startDate) ) {
        $lstRes[$id]['projects'][$aff->idProject]=$aff->idProject;
      }
    }
  }
  
  $where="workDate>='$startDate' and workDate<='$endDate'";
  $wk=new Work();
  $workList=$wk->getSqlElementsFromCriteria(null,false,$where);
  foreach ($workList as $wk) {
    $lstRes[$wk->idResource]['days'][$wk->workDate]['work']+=$wk->work;
  }
  
  foreach ($lstRes as $idRes=>$res) {
    $tmpDate=$startDate;
    $full=true;
    while ($tmpDate<=$endDate) {
      if ($res['days'][$tmpDate]['open']=='1' and $res['days'][$tmpDate]['work']<$res['capacity']) {
        $full=false;
      }
      $tmpDate=addDaysToDate($tmpDate, 1);
    }
    $lstRes[$idRes]['full']=$full;
    if (!$full) {
      $lstRes[$idRes]['workDetail']=getImputationSummary($res);
    }
  }
    
  $dest=array();
  foreach ($lstRes as $id=>$res) {
    if (!$full) {
      if ($sendToResource and $sendToResource!='NO') {
        $dest[$id]=array(
            'ress'=>array($id=>$res['workDetail']),
            'send'=>$sendToResource
        );
      }
      if ($sendToTeamManager and $sendToTeamManager!='NO') {
        $team=SqlList::getFieldFromId('Resource', $id, 'idTeam');
        $manager=(trim($team))?SqlList::getFieldFromId('Team', $team, 'idResource'):'';
        if (trim($manager) and isset($dest[$manager])) {
          $dest[$manager]['ress'][$id]=$res['workDetail'];
          if ($dest[$manager]['send']!=$sendToTeamManager) {
            $dest[$manager]['send']='ALERT&MAIL';
          }
        } else if (trim($manager)) {
          $dest[$manager]=array(
              'ress'=>array($id=>$res['workDetail']),
              'send'=>$sendToTeamManager
          );
        }
      }
      if ($sendToProjectLeader and $sendToProjectLeader!='NO') {
        foreach ($lstRes[$id]['projects'] as $proj) {
          $plList=Affectation::getProjectLeaderList($proj);
          foreach ($plList as $idPL=>$namePL) {
            if (isset($dest[$idPL])) {
              $dest[$idPL]['ress'][$id]=$res['workDetail'];
              if ($dest[$idPL]['send']!=$sendToProjectLeader) {
                $dest[$idPL]['send']='ALERT&MAIL';
              }
            } else {
              $dest[$idPL]=array(
                'ress'=>array($id=>$res['workDetail']),
                'send'=>$sendToProjectLeader
              );
            }
          }
        }
      }
    }
  }
  foreach ($dest as $id=>$dst) {
    $send=$dst['send'];
    $list=$dst['ress'];
    $title=i18n("messageAlertImputationProjectLeader",array(htmlFormatDateTime($endDate)));
    if (count($list)==1 and isset($list[$id])) {
      $title=i18n("messageAlertImputationResource",array(htmlFormatDateTime($endDate)));
    }
    if ($send=='ALERT' or $send=='ALERT&MAIL') {
      $msg="";
      foreach ($list as $idRes=>$detRes) {
        $msg.=(($msg=="")?'':', ').SqlList::getNameFromId('Resource',$idRes);
      }
      sendAlertForImputationAlert($id,$title,$msg);
    }
    if ($send=='MAIL' or $send=='ALERT&MAIL') {
      $msg="";
      foreach ($list as $idRes=>$detRes) {
        $msg.=$detRes;
      }
      sendMailForImputationAlert($id,$title,$msg);
    }
  }
}
function getImputationSummary($resTab) {
  $workHeader="";
  $workData="";
  foreach ($resTab['days'] as $day=>$dayData) {
    $colorDay=($dayData['open']=='1')?'#eeeeee':'#aaaaaa';
    $workHeader.='<td style="text-align:center;border:1px solid #555555;width:80px;background-color:'.$colorDay.'">'.htmlFormatDate($day).'</td>';
    $colorData=$colorDay;
    if ($dayData['work']>$resTab['capacity']) {
      $colorData='#ffaaaa';
    } else if ($dayData['open']=='1') {
      if ($dayData['work']==$resTab['capacity']) {
        $colorData='#aaffaa';
      } else if ($dayData['work']<$resTab['capacity']) {
        $colorData='#ffffaa';
      }
    } else if ($dayData['work']>0) {
      $colorData='#ffaaaa';
    }
    $workData.='<td style="text-align:center;border:1px solid #555555;background-color:'.$colorData.'">'.Work::displayWorkWithUnit($dayData['work']).'</td>';
  }
  $result='<table style="font-family:Verdana, Arial;font-size:8pt;border:1px solid #555555;border-collapse: collapse;">';
  $result.='<tr><td style="font-weight:bold;color:#ffffff;text-align:center;border:1px solid #555555;border-right:1px solid #eeeeee;background-color:#555555;width:150px">'.i18n('colIdResource').'</td>'
      .'<td colspan="'.count($resTab['days']).'" style="font-weight:bold;color:#ffffff;text-align:center;background-color:#555555">'.i18n('colWork').'</td></tr>';
  $result.='<tr><td rowspan="2" style="text-align:left;border:1px solid #555555;">'.$resTab['name'].'</td>'.$workHeader.'</tr>';
  $result.='<tr>'.$workData.'</tr>';
  $result.='</table>';    
  return $result;
}

function sendAlertForImputationAlert($alertSendTo,$alertSendTitle,$alertSendMessage){
  $alertSendType='WARNING';
  $alert=new Alert();
  $alert->idUser=$alertSendTo;
  $alert->alertType=$alertSendType;
  $alert->alertInitialDateTime=date('Y-m-d H:i:s');
  $alert->alertDateTime=date('Y-m-d H:i:s');
  $alert->title=mb_substr($alertSendTitle,0,100);
  $alert->message=htmlspecialchars($alertSendMessage,ENT_QUOTES,'UTF-8');
  $result=$alert->save();
}
function sendMailForImputationAlert($alertSendTo,$alertSendTitle,$alertSendMessage) {
  $to=SqlList::getFieldFromId('Resource', $alertSendTo, 'email');
  if (trim($to)) {
    $result=sendMail($to, '['.Parameter::getGlobalParameter('paramDbDisplayName').'] '.$alertSendTitle, $alertSendMessage);
  }
}