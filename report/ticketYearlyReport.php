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

//echo "ticketYearlyReport.php";
include_once '../tool/projeqtor.php';

if (! isset($includedReport)) {
  include("../external/pChart/pData.class");  
  include("../external/pChart/pChart.class");  
  
  	$paramYear='';
	if (array_key_exists('yearSpinner',$_REQUEST)) {
		$paramYear=$_REQUEST['yearSpinner'];
	  $paramYear=Security::checkValidYear($paramYear);
	};
  
	$paramMonth='';
	if (array_key_exists('monthSpinner',$_REQUEST)) {
		$paramMonth=$_REQUEST['monthSpinner'];
    $paramMonth=Security::checkValidMonth($paramMonth);
	};

  
	$paramWeek='';
	if (array_key_exists('weekSpinner',$_REQUEST)) {
		$paramWeek=$_REQUEST['weekSpinner'];
	  $paramWeek=Security::checkValidWeek($paramWeek);
	};
  
  $paramProject='';
  if (array_key_exists('idProject',$_REQUEST)) {
    $paramProject=trim($_REQUEST['idProject']);
	Security::checkValidId($paramProject);
  }
    
  $paramTicketType='';
  if (array_key_exists('idTicketType',$_REQUEST)) {
    $paramTicketType=trim($_REQUEST['idTicketType']);
	  $paramTicketType = Security::checkValidId($paramTicketType); // only allow digits
  };
  
  $paramRequestor='';
  if (array_key_exists('requestor',$_REQUEST)) {
    $paramRequestor=trim($_REQUEST['requestor']);
	  $paramRequestor = Security::checkValidId($paramRequestor); // only allow digits
  }
    
  $paramIssuer='';
  if (array_key_exists('issuer',$_REQUEST)) {
    $paramIssuer=trim($_REQUEST['issuer']);
	  $paramIssuer = Security::checkValidId($paramIssuer); // only allow digits
  };
  
  $paramResponsible='';
  if (array_key_exists('responsible',$_REQUEST)) {
    $paramResponsible=trim($_REQUEST['responsible']);
	  $paramResponsible = Security::checkValidId($paramResponsible); // only allow digits
  };
  
  
  $user=getSessionUser();
  
  $periodType='year';
  //$periodValue=$_REQUEST['periodValue'];
  $periodValue=$paramYear;
  
  // Header
  $headerParameters="";
  if ($paramProject!="") {
    $headerParameters.= i18n("colIdProject") . ' : ' . htmlEncode(SqlList::getNameFromId('Project', $paramProject)) . '<br/>';
  }
  if ($periodType=='year' or $periodType=='month' or $periodType=='week') {
    $headerParameters.= i18n("year") . ' : ' . $paramYear . '<br/>';  
  }
  if ($periodType=='month') {
    $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
  }
  if ( $periodType=='week') {
    $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
  }
  if ($paramTicketType!="") {
    $headerParameters.= i18n("colIdTicketType") . ' : ' . SqlList::getNameFromId('TicketType', $paramTicketType) . '<br/>';
  }
  if ($paramRequestor!="") {
    $headerParameters.= i18n("colRequestor") . ' : ' . SqlList::getNameFromId('Contact', $paramRequestor) . '<br/>';
  }
  if ($paramIssuer!="") {
    $headerParameters.= i18n("colIssuer") . ' : ' . SqlList::getNameFromId('User', $paramIssuer) . '<br/>';
  }
  if ($paramResponsible!="") {
    $headerParameters.= i18n("colResponsible") . ' : ' . SqlList::getNameFromId('Resource', $paramResponsible) . '<br/>';
  }
  include "header.php";
}
$reportContext=false;
$where=getAccesRestrictionClause('Ticket',false);

$where.=" and ( (    creationDateTime>= '" . $paramYear . "-01-01'";
$where.="        and creationDateTime<='" . $paramYear . "-12-31' )";
$where.="    or (    doneDateTime>= '" . $paramYear . "-01-01'";
$where.="        and doneDateTime<='" . $paramYear . "-12-31' )";
$where.="    or (    idleDateTime>= '" . $paramYear . "-01-01'";
$where.="        and idleDateTime<='" . $paramYear . "-12-31' ) )";
if ($paramProject!="") {
  $where.=" and idProject in " .  getVisibleProjectsList(false, $paramProject);
}
if ($paramTicketType!="") {
  $where.=" and idTicketType='" . Sql::fmtId($paramTicketType) . "'";
}
if ($paramRequestor!="") {
  $where.=" and idContact='" . Sql::fmtId($paramRequestor) . "'";
}
if ($paramIssuer!="") {
  $where.=" and idUser='" . Sql::fmtId($paramIssuer) . "'";
}
if ($paramResponsible!="") {
  $where.=" and idResource='" . Sql::fmtId($paramResponsible) . "'";
}
$order="";
//echo $where;
$ticket=new Ticket();
$lstTicket=$ticket->getSqlElementsFromCriteria(null,false, $where, $order);
$created=array();
$done=array();
$closed=array();
for ($i=1; $i<=13; $i++) {
  $created[$i]=0;
  $done[$i]=0;
  $closed[$i]=0;
}
$sumProj=array();
foreach ($lstTicket as $t) {
  if (substr($t->creationDateTime,0,4)==$paramYear) {
    $month=intval(substr($t->creationDateTime,5,2));
    $created[$month]+=1;
    $created[13]+=1;
  }
  if (substr($t->doneDateTime,0,4)==$paramYear) {
    $month=intval(substr($t->doneDateTime,5,2));
    $done[$month]+=1;
    $done[13]+=1;
  }
  if (substr($t->idleDateTime,0,4)==$paramYear) {
    $month=intval(substr($t->idleDateTime,5,2));
    $closed[$month]+=1;
    $closed[13]+=1;
  }
}

if (checkNoData($lstTicket)) return;

// title
echo '<table width="95%" align="center">';
echo '<tr><td class="reportTableHeader" rowspan="2">' . i18n('Ticket') . '</td>';
echo '<td colspan="13" class="reportTableHeader">' . $periodValue . '</td>';
echo '</tr><tr>';
$arrMonth=getArrayMonth(4,true);
$arrMonth[13]=i18n('sum');
for ($i=1; $i<=12; $i++) {
  echo '<td class="reportTableColumnHeader">' . $arrMonth[$i-1] . '</td>';
}
echo '<td class="reportTableHeader" >' . i18n('sum') . '</td>';
echo '</tr>';

$sum=0;
for ($line=1; $line<=3; $line++) {
  if ($line==1) {
    $tab=$created;
    $caption=i18n('created');
    $serie="created";
  } else if ($line==2) {
    $tab=$done;
    $caption=i18n('done');
    $serie="done";
  } else if ($line==3) {
    $tab=$closed;
    $caption=i18n('closed');
    $serie="closed";
  }
  echo '<tr><td class="reportTableLineHeader" style="width:18%">' . $caption . '</td>';
  foreach ($tab as $id=>$val) {
    if ($id=='13') {
      echo '<td style="width:10%;" class="reportTableColumnHeader">';
    } else {
      echo '<td style="width:6%;" class="reportTableData">';
    }
    echo $val;
    echo '</td>';
  }
  
  echo '</tr>';
}
echo '</table>';
  
// Render graph
// pGrapg standard inclusions     
if (! testGraphEnabled()) { return;}

$dataSet=new pData;
$createdSum=array('','','','','','','','','','','','',$created[13]);
$created[13]="";
$doneSum=array('','','','','','','','','','','','',$done[13]);
$done[13]="";
$closedSum=array('','','','','','','','','','','','',$closed[13]);
$closed[13]="";
$rightScale=array('','','','','','','','','','','','',i18n('sum'));
$dataSet->AddPoint($created,"created");
$dataSet->SetSerieName(i18n("created"),"created");  
$dataSet->AddSerie("created");
$dataSet->AddPoint($done,"done");
$dataSet->SetSerieName(i18n("done"),"done");  
$dataSet->AddSerie("done");
$dataSet->AddPoint($closed,"closed");
$dataSet->SetSerieName(i18n("closed"),"closed");  
$dataSet->AddSerie("closed");
$arrMonth[13]="";
$dataSet->AddPoint($arrMonth,"months");  
$dataSet->SetAbsciseLabelSerie("months"); 
  
// Initialise the graph  
$width=700;
//if (array_key_exists('screenWidth',$_SESSION)) {
//  $width = round(($_SESSION['screenWidth'] * 0.8  ) - 15) ; // 80% of screen - split barr - padding (x2)
//}
$graph = new pChart($width,230);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
//$graph->drawFilledRoundedRectangle(7,7,$width-7,223,5,240,240,240);  
$graph->drawRoundedRectangle(5,5,$width-5,225,5,230,230,230);  

$graph->setColorPalette(0,200,100,100);
$graph->setColorPalette(1,100,200,100);
$graph->setColorPalette(2,100,100,200);
$graph->setColorPalette(3,200,100,100);
$graph->setColorPalette(4,100,200,100);
$graph->setColorPalette(5,100,100,200);
$graph->setGraphArea(40,30,$width-140,200);  
$graph->drawGraphArea(252,252,252);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",8);  
$graph->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0,0,0,0,TRUE,0,1, true);  
$graph->drawGrid(5,TRUE,230,230,230,255);  
  
// Draw the line graph  
$graph->drawFilledLineGraph($dataSet->GetData(),$dataSet->GetDataDescription(),30,true);
$graph->drawLineGraph($dataSet->GetData(),$dataSet->GetDataDescription());  
$graph->drawPlotGraph($dataSet->GetData(),$dataSet->GetDataDescription(),3,2,255,255,255);  
  
// Finish the graph  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",8);  
$graph->drawLegend($width-100,35,$dataSet->GetDataDescription(),240,240,240);  
//$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);  
//$graph->drawTitle(60,22,"graph",50,50,50,585);

$graph->clearScale();  
$dataSet->RemoveSerie("created");
$dataSet->RemoveSerie("done");
$dataSet->RemoveSerie("closed"); 
$dataSet->RemoveSerie("month"); 
$dataSet->AddPoint($createdSum,"createdSum");
$dataSet->SetSerieName(i18n("created"),"createdSum");  
$dataSet->AddSerie("createdSum");
$dataSet->AddPoint($doneSum,"doneSum");
$dataSet->SetSerieName(i18n("done"),"doneSum");  
$dataSet->AddSerie("doneSum");
$dataSet->AddPoint($closedSum,"closedSum");
$dataSet->SetSerieName(i18n("closed"),"closedSum");  
$dataSet->AddSerie("closedSum");
$dataSet->SetYAxisName(i18n("sum"));
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",8);
$dataSet->AddPoint($rightScale,"scale");  
$dataSet->SetAbsciseLabelSerie("scale");  
$graph->drawRightScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_START0,0,0,0,true,0,1, true);
$graph->drawBarGraph($dataSet->GetData(),$dataSet->GetDataDescription(),true);  

$imgName=getGraphImgName("ticketYearlyReport");
$graph->Render($imgName);
echo '<table width="95%" align="center"><tr><td align="center">';
echo '<img src="' . $imgName . '" />'; 
echo '</td></tr></table>';