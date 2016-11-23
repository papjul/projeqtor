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

$reportContext=true;
// Header
//echo "<page_header>";
if (Parameter::getGlobalParameter('logLevel')>='4') {
  echo $_SERVER['SCRIPT_FILENAME'];
}
projeqtor_set_time_limit(300);
projeqtor_set_memory_limit('512M');

// Security : check that no special car appears in the request
// Note: This check is not good enough, need to filter values based on context.
foreach ($_REQUEST as $reqParam=>$reqValue) {
	if ($reqParam=='reportName') {
    // Report name can have spec car. Will be escaped on display
	} else if ($reqParam=='refId') {
		if (! is_numeric($reqValue) ) {
			$refId='0';
		}
	} else {
		if ($reqValue!=Sql::fmtStr($reqValue)) {
			traceHack("improper value '$reqValue' for request parameter '$reqParam' while calling a report");
			exit;
		}
	}
}

echo "<table style='width:100%'><tr>";
echo "<td style='width:1%' class='reportHeader'>&nbsp;</td>";
echo "<td style='width:10%' class='reportHeader'>" . i18n('colParameters') . "</td>";
echo "<td style='width:1%' class='reportHeader'>&nbsp;</td>";
echo "<td style='width:1%' >&nbsp;</td>";
echo "<td style='width:30%'>"; 
echo $headerParameters;
echo "</td>";
echo "<td align='center' style='width:40%; font-size: 150%; font-weight: bold;'>"; 

if (array_key_exists('reportName', $_REQUEST)) {
  echo '<table><tr><td class="reportTableHeader" style="text-align: center; padding: 3px 10px 3px 10px;">';
  echo htmlEncode(ucfirst($_REQUEST['reportName']),'html');
  echo '</td></tr></table>';
}
echo "</td>";
echo "<td style='width:1%'>&nbsp;</td>";
echo "<td style='width:15%; text-align:right'>";
echo  htmlFormatDate(date('Y-m-d')) . " " . date('H:i');
echo "</td>";
echo "<td style='width:1%'>&nbsp;</td>";
echo "</tr></table>";
echo "<br/>";
//echo "</page_header>";

$graphEnabled=true;
if (! function_exists('ImagePng')) {
  $graphEnabled=false;
  errorLog("GD Library not enabled - impossible to draw charts");
}
if (! function_exists('imageftbbox')) {
  $graphEnabled=false;
  errorLog("GD Library or FreeType Librairy incorrect or not correctly installed - impossible to draw charts");
}

$rgbPalette=array(
6=>array('B'=>200, 'G'=>100, 'R'=>100),
7=>array('B'=>100, 'G'=>200, 'R'=>100),
8=>array('B'=>100, 'G'=>100, 'R'=>200),
9=>array('B'=>200, 'G'=>200, 'R'=>100),
10=>array('B'=>200, 'G'=>100, 'R'=>200),
11=>array('B'=>100, 'G'=>200, 'R'=>200),
0=>array('B'=>250, 'G'=> 50, 'R'=> 50),
1=>array('B'=> 50, 'G'=>250, 'R'=> 50),
2=>array('B'=> 50, 'G'=> 50, 'R'=>250),
3=>array('B'=>250, 'G'=>250, 'R'=> 50),
4=>array('B'=>250, 'G'=> 50, 'R'=>250),
5=>array('B'=> 50, 'G'=>250, 'R'=>250)
);

include_once('headerFunctions.php');
?>
