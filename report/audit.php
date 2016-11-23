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

include_once '../tool/projeqtor.php';

AuditSummary::updateAuditSummary(date('Ymd'));
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
//$paramWeek='';
//if (array_key_exists('weekSpinner',$_REQUEST)) {
//  $paramWeek=$_REQUEST['weekSpinner'];
//};

$user=getSessionUser();

$periodType='month';
$periodValue='';
if (array_key_exists('periodValue',$_REQUEST))
{
	$periodValue=$_REQUEST['periodValue'];
	$periodValue=Security::checkValidPeriod($periodValue);
}


// Header
$headerParameters="";
if ($periodType=='year' or $periodType=='month' or $periodType=='week') {
  $headerParameters.= i18n("year") . ' : ' . $paramYear . '<br/>';
}
if ($periodType=='month') {
  $headerParameters.= i18n("month") . ' : ' . $paramMonth . '<br/>';
}
//if ( $periodType=='week') {
//  $headerParameters.= i18n("week") . ' : ' . $paramWeek . '<br/>';
//}

include "header.php";

$crit="auditDay like '" . $periodValue . "%'";

$as=new AuditSummary();
$result=$as->getSqlElementsFromCriteria(null, false, $crit);

if (checkNoData($result)) exit;

$monthDays = date('t',mktime(0, 0, 0, $paramMonth, 1, $paramYear)); 
$days=array();
$nb=array();
$min=array();
$max=array();
$mean=array();
for ($i=1;$i<=$monthDays;$i++) {
  $nb[$i]=0;
  $days[$i]=$i;
  $min[$i]=0;
  $max[$i]=0;
  $mean[$i]=0;
}
//$day=array();
foreach ($result as $as) {
	$d=intval(substr($as->auditDay,6));
	$nb[$d]=$as->numberSessions;
	$mean[$d]=formatDateRpt($as->meanDuration);
	$min[$d]=formatDateRpt($as->minDuration);
	$max[$d]=formatDateRpt($as->maxDuration);	  
}

// Graph
if (! testGraphEnabled()) { echo "pChart not enabled. See log file."; return;}
include("../external/pChart/pData.class");  
include("../external/pChart/pChart.class");  

// Graph 1 : connections per day
$DataSet = new pData;  
$DataSet->AddPoint($nb,'Serie1');
$DataSet->AddSerie('Serie1');  
$DataSet->SetSerieName("Connexions","Serie1");  
$DataSet->AddPoint($days,'Serie2');
$DataSet->SetAbsciseLabelSerie("Serie2");  

// Initialise the graph  
$width=700;
$graph = new pChart($width,260);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
$graph->drawRoundedRectangle(5,5,$width-5,258,5,230,230,230);  
$graph->setColorPalette(0,100,100,250);
$graph->setGraphArea(80,30,$width-30,230);  
$graph->drawGraphArea(252,252,252);  
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);  
$graph->drawGrid(4,TRUE,230,230,230,255);    
// Draw the line graph  
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);  
// Finish the graph  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10); 
//$graph->drawLegend(45,35,$DataSet->GetDataDescription(),255,255,255);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
$graph->drawTitle(60,22,i18n('connectionsNumberPerDay'),50,50,50,585);   
$imgName=getGraphImgName("auditNb");
$graph->Render($imgName);
echo '<table width="95%" align="center"><tr><td align="center">';
echo '<img src="' . $imgName . '" />'; 
echo '</td></tr></table>';
echo '<br/>';

// Graph 2: connection duration per day
$DataSet = new pData;  
$DataSet->AddPoint($max,'Serie3');
$DataSet->AddSerie('Serie3');  
$DataSet->SetSerieName(i18n("max"),"Serie3");     
$DataSet->AddPoint($mean,'Serie1');
$DataSet->AddSerie('Serie1');  
$DataSet->SetSerieName(i18n("mean"),"Serie1");  
$DataSet->AddPoint($min,'Serie2');
$DataSet->AddSerie('Serie2');  
$DataSet->SetSerieName(i18n("min"),"Serie2");

$DataSet->AddPoint($days,'SerieX');
$DataSet->SetAbsciseLabelSerie("SerieX");
$DataSet->SetYAxisFormat("time");  
// Initialise the graph  
$width=700;
$graph = new pChart($width,260);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
$graph->drawRoundedRectangle(5,5,$width-5,258,5,230,230,230);  
$graph->setColorPalette(0,255,100,100); 
$graph->setColorPalette(1,100,100,250);
$graph->setColorPalette(2,100,190,100);
$graph->setGraphArea(80,30,$width-30,230);  
$graph->drawGraphArea(252,252,252);  
$graph->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);  
$graph->drawGrid(4,TRUE,230,230,230,255);    
// Draw the line graph  
$graph->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());  
$graph->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);  
// Finish the graph  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10); 
$graph->drawLegend(620,10,$DataSet->GetDataDescription(),255,255,255);  
$graph->setFontProperties("../external/pChart/Fonts/tahoma.ttf",10);
$graph->drawTitle(60,22,i18n('connectionsDurationPerDay'),50,50,50,585);   
$imgName=getGraphImgName("auditNb");
$graph->Render($imgName);
echo '<table width="95%" align="center"><tr><td align="center">';
echo '<img src="' . $imgName . '" />'; 
echo '</td></tr></table>';
echo '<br/>';

function formatDateRpt($dateRpt) {
	$baseDay=date('Y-m-d');
	if ($dateRpt>'24:00:00') {
		$split=explode(':',$dateRpt);
		$hours=$split[0];
		while ($hours>=24) {
			$hours-=24;
			$baseDay=addDaysToDate($baseDay, 1);
		}
		$dateRpt=$hours.':00:00';
	}
	
  return strtotime($baseDay.' '.$dateRpt)-strtotime(date('Y-m-d 00:00:00'));
}  
   
?>

