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

$lst=SqlList::getList('TicketType');

$includedReport=true;

$cptBoucle=0;
$cptBoucleMax=count($lst);
Foreach ($lst as $code=>$name) {
  echo '<table  width="95%" align="center"><tr><td style="width: 100%" class="section">';
  echo "$name" . '<br/>';
  echo '</td></tr>';
  echo '<tr><td>&nbsp;</td></tr>';
  echo '<tr><td></td></tr>';
  echo '</table>';
  $paramTicketType=$code;
  include "ticketYearlyReport.php";
  echo '<br/>';
  $cptBoucle++;
  if ($cptBoucle<$cptBoucleMax) {
    echo '</page><page pageset="old">';
  }
}