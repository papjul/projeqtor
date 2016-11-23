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

require_once('../model/_securityCheck.php');
require_once('maintenanceFunctions.php');
require_once('../tool/configCheckPrerequisites.php');
$maintenance=true;
Sql::$maintenanceMode=true;
setSessionValue('setup', false, true);
// Version History : starts at 0.3.0 with clean database (before scripts are empty)
$versionHistory = array(
  "V0.3.0", "V0.4.0",  "V0.5.0",  "V0.6.0",  "V0.7.0",  "V0.8.0",  "V0.9.0",  
	"V1.0.0", "V1.1.0",  "V1.2.0",  "V1.3.0",  "V1.4.0",  "V1.5.0",  "V1.6.0",  "V1.7.0",  "V1.8.0",  "V1.9.0",
  "V2.0.0", "V2.0.1",  "V2.1.0",  "V2.1.1",  "V2.2.0",  "V2.3.0",  "V2.4.0",  "V2.4.1",  "V2.4.2",  "V2.5.0",  "V2.6.0",
  "V3.0.0", "V3.0.1",  "V3.1.0",  "V3.2.0",  "V3.3.0",  "V3.3.1",  "V3.4.0",  "V3.4.1",
  "V4.0.0", "V4.0.1",  "V4.1.-",  "V4.1.0",  "V4.2.0",  "V4.2.1",  "V4.3.0.a","V4.3.0",  "V4.3.2",  "V4.4.0",  "V4.5.0", "V4.5.3", "V4.5.6",
  "V5.0.0", "V5.1.0.a","V5.1.0",  "V5.1.1",  "V5.1.4",  "V5.1.5",  "V5.2.0",  "V5.2.2.a","V5.2.2",  "V5.2.3",  "V5.2.4", "V5.2.5", "V5.3.0.a", "V5.3.0", "V5.3.2", "V5.3.3", 
  "V5.4.0", "V5.4.2", "V5.4.3", "V5.4.4", "V5.4.5", "V5.5.0", "V5.5.2", "V5.5.3", 
  "V6.0.0");
$versionParameters =array(
  'V1.2.0'=>array('paramMailSmtpServer'=>'localhost',
                 'paramMailSmtpPort'=>'25',
                 'paramMailSendmailPath'=>null,
                 'paramMailTitle'=>'[Project\'Or RIA] ${item} #${id} moved to status ${status}',
                 'paramMailMessage'=>'The status of ${item} #${id} [${name}] has changed to ${status}',
                 'paramMailShowDetail'=>'true' ),
  'V1.3.0'=>array('defaultTheme'=>'blue'),
  'V1.4.0'=>array('paramReportTempDirectory'=>'../files/report/'),
  'V1.5.0'=>array('currency'=>'€', 
                  'currencyPosition'=>'after'),
  'V1.8.0'=>array('paramLdap_allow_login'=>'false',
					'paramLdap_base_dn'=>'dc=mydomain,dc=com',
					'paramLdap_host'=>'localhost',
					'paramLdap_port'=>'389',
					'paramLdap_version'=>'3',
					'paramLdap_search_user'=>'cn=Manager,dc=mydomain,dc=com',
					'paramLdap_search_pass'=>'secret',
					'paramLdap_user_filter'=>'uid=%USERNAME%')
);
$SqlEndOfCommand=";";
$SqlComment="--";
   
require_once (dirname(__FILE__) . '/../tool/projeqtor.php');
// New in V5.1 => check again prerequisites (may have been changed on new version, but only displays errors
if (checkPrerequisites()!="OK") {
  exit;
} 

$nbErrors=0;
$currVersion=Sql::getDbVersion();
traceLog("");
traceLog("=====================================");
traceLog("");
traceLog("DataBase actual Version = " . $currVersion );
traceLog("ProjeQtOr actual Version = " . $version );
traceLog("");
if ($currVersion=="") {
  $currVersion='V0.0.0';
  // if no current version, parameters are set through config.php
  //$versionParameters=array(); // Clear $versionParameter to avoid dupplication of parameters
  $versionParameters=array("V4.4.0"=>array('enforceUTF8'=>true)); // V4.4.0 set enforceUTF8 only for new fresh install
}
/*$arrVers=explode('.',substr($currVersion,1));
$currVer=$arrVers[0];
$currMaj=$arrVers[1];
$currRel=$arrVers[2];*/

if ($currVersion!='V0.0.0' and beforeVersion($currVersion,'V3.0.0') ) {
	$nbErrors+=runScript('V3.0.-');
}

foreach ($versionHistory as $vers) {
  /*$arrVers=explode('.',substr($vers,1));
  $histVer=$arrVers[0];
  $histMaj=$arrVers[1];
  $histRel=$arrVers[2];*/
  if ( beforeVersion($currVersion, $vers) ) {
    $nbErrors+=runScript($vers);
  }
}

if ($currVersion=='V0.0.0') {
  traceLog ("create default project");
  $type=new ProjectType();
  $lst=$type->getSqlElementsFromCriteria(array('name'=>'Fixed Price'));
  $type=(count($lst)>0)?$lst[0]:null;
  $proj=new Project();
  $proj->color='#0000FF';
  $proj->description='Default project' . "\n" .
                     'For example use only.' . "\n" .
                     'Remove or rename this project when initializing your own data.';
  $proj->name='Default project';
  if ($type) {
    $proj->idProjectType=$type->id;
  }
  $result=$proj->save();
  $split=explode("<", $result);
  traceLog($split[0]);
  // For V4.4.0 initialize consolidateValidated for new installations (for others, keep previous behavior as defaut)
  $prm=new Parameter();
  $prm->parameterCode='consolidateValidated';
  $prm->parameterValue='IFSET';
  $prm->save();
  // New in V5 : Start Guide Page
  Parameter::storeUserParameter('startPage', 'startGuide.php',1);
}

//echo "for V1.6.1<br/>";
// For V1.6.1
$tst=new ExpenseDetailType('1');
if (! $tst->id and beforeVersion($currVersion,"V1.6.1")) {
	$nbErrors+=runScript('V1.6.1');
}

$memoryLimitForPDF=Parameter::getGlobalParameter('paramMemoryLimitForPDF');
// For V1.7.0
if (! isset($memoryLimitForPDF) and beforeVersion($currVersion,"V3.0.0")) {
	writeFile('$paramMemoryLimitForPDF = \'512\';',$parametersLocation);
  writeFile("\n",$parametersLocation);
  traceLog('Parameter $paramMemoryLimitForPDF added');
}

// For V1.9.0
if (beforeVersion($currVersion,"V1.9.0") and $currVersion!='V0.0.0') {
  traceLog("update Reference [V1.9.0]");
	$adminFunctionality='updateReference';
	include('../tool/adminFunctionalities.php');
	echo "<br/>";
}

// For V1.9.1
if (beforeVersion($currVersion,"V1.9.1")) {
  traceLog("update affectations [V1.9.1]");
  // update affectations
  $aff=new Affectation();
  $affList=$aff->getSqlElementsFromCriteria(null, false);
  foreach ($affList as $aff) {
    $aff->save();
  }
}

// For V2.1.0
if (beforeVersion($currVersion,"V2.1.0")) {
  traceLog("update planning elements [2.1.0]");
  // update PlanningElements (progress)
  $pe=new PlanningElement();
  $peList=$pe->getSqlElementsFromCriteria(null, false);
  foreach ($peList as $pe) {
    $pe->save();
  }
}
// For V2.1.1
if (beforeVersion($currVersion,"V2.1.1")) {
  traceLog("update assignments [V2.1.1]");
  // update PlanningElements (progress)
  $ass=new Assignment();
  $assList=$ass->getSqlElementsFromCriteria(null, false);
  foreach ($assList as $ass) {
    $ass->saveWithRefresh();
  }
}

// For V2.4.1 & V2.4.2
if (beforeVersion($currVersion,"V2.4.2")) {
  traceLog("update dependencies for requirements [V2.4.2]");
  $req=new Requirement();
  $reqList=$req->getSqlElementsFromCriteria(null, false);
  foreach ($reqList as $req) {
  	$rq=new Requirement($req->id);
    $rq->updateDependencies();
  }
  $ses=new TestSession();
  $sesList=$ses->getSqlElementsFromCriteria(null, false);
  foreach ($sesList as $ses) {
  	$ss=new TestSession($ses->id);
    $ss->updateDependencies();
  }
  $tst=new TestCase();
  $tstList=$tst->getSqlElementsFromCriteria(null, false);
  foreach ($tstList as $tst) {
    $tc=new TestCase($tst->id);
    $tc->updateDependencies();
  }
}

// For V2.6.0 : migration of parameters to database
if (beforeVersion($currVersion,"V2.6.0")) {
  $arrayParamsToMigrate=array('paramDbDisplayName',
                              'paramMailTitle','paramMailMessage','paramMailSender','paramMailReplyTo','paramAdminMail',
                              'paramMailSmtpServer','paramMailSmtpPort','paramMailSendmailPath','paramMailShowDetail');
  migrateParameters($arrayParamsToMigrate); 
}
if (beforeVersion($currVersion,"V3.0.0")) {
  $arrayParamsToMigrate=array('paramLdap_allow_login', 'paramLdap_base_dn', 'paramLdap_host', 'paramLdap_port',
    'paramLdap_version', 'paramLdap_search_user', 'paramLdap_search_pass', 'paramLdap_user_filter',
    'paramDefaultPassword','paramPasswordMinLength', 'lockPassword',
    'paramDefaultLocale', 'paramDefaultTimezone', 'currency', 'currencyPosition',
    'paramFadeLoadingMode', 'paramRowPerPage', 'paramIconSize',
    'defaultTheme', 'paramPathSeparator', 'paramAttachmentDirectory', 'paramAttachmentMaxSize',
    'paramReportTempDirectory', 'paramMemoryLimitForPDF',
    'defaultBillCode','paramMailEol' 
    //'logFile', 'logLevel', 'paramDebugMode',
    );
  migrateParameters($arrayParamsToMigrate); 
}
if (afterVersion($currVersion,"V3.0.0") and beforeVersion($version,"V3.1.3") 
and ! strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' and $paramDbType=='mysql') { 
  traceLog("rename table workPeriod to workperiod [V3.1.3");
	$paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
	$query="RENAME TABLE `".$paramDbPrefix."workPeriod` TO `".$paramDbPrefix."workperiod`;";
	$query=trim(formatForDbType($query));
  //Sql::beginTransaction();
  $result=Sql::query($query);
  //Sql::commitTransaction();
}

if (beforeVersion($currVersion,"V3.3.0") and $currVersion!='V0.0.0') {
  traceLog("update test sessions dates [V3.3.0]");
  $ses=new TestSession();
  $sesList=$ses->getSqlElementsFromCriteria(null, false);
  foreach ($sesList as $ses) {
    $ss=new TestSession($ses->id);
    $ss->TestSessionPlanningElement->validatedStartDate=$ss->startDate;
    $ss->TestSessionPlanningElement->validatedEndDate=$ss->endDate;
    $ss->save();
  }
}

$tstTable=new TodayParameter();
$tst=Sql::query("select count(*) from ". $tstTable->getDatabaseTableName()) ;
if (! $tst or count($tst)==0) {
  $nbErrors+=runScript('V3.3.1.linux');
}

if (beforeVersion($currVersion,"V3.4.0")) {
  traceLog("set default profile [V3.4.0]");
	$defProf=Parameter::getGlobalParameter('defaultProfile');
	if (! $defProf) {
		$prf=new Profile('5');
		if ($prf->profileCode=='G') {
			$param=New Parameter();
			$param->parameterCode='defaultProfile';
			$param->parameterValue=5;
			$param->idUser=null;
			$param->idProject=null;
			$param->save();
		}
	}
}

if (beforeVersion($currVersion,"V4.0.0")) {
  traceLog("delete old references to projectorria [V4.0.0]");
	// Deleting old files referencing projector or projectorria : these files have been renamed
  $root=$_SERVER['SCRIPT_FILENAME'];
	$root=substr($root,0,strpos($root, '/tool/'));
  if (! $root) { // On IIS, previous method does not return correct method 
	  $root=__FILE__;
	  $root=substr($root,0,strpos($root, '/db/'));
	}
	if (! $root) { // On Windows, previous method should fail
	  $root=__FILE__;
	  $root=substr($root,0,strpos($root, '\\db\\'));
	}	
	$files = glob($root.'/db/Projector_*.sql'); // get all file names
  error_reporting(0);
  disableCatchErrors();
  if ($files) {
	  foreach($files as $file){ // iterate files
	    if(is_file($file))
	      $perms = fileperms($file);
	      if ($perms & 0x0080) {
	        $do=@unlink($file); // delete file
	      } else {
	      	errorLog("Cannot delete file : ".$file);
	      } 
	  }
  }  
  $arrayFiles=array('/tool/projector.php',
    '/view/js/projector.js',
    '/view/js/projectorDialog.js',
    '/view/js/projectorFormatter.js',
    '/view/js/projectorWork.js',
    '/view/css/projector.css',
    '/view/css/projectorIcons.css',
    '/view/css/projectorPrint.css');
  foreach ($arrayFiles as $file) {
  	if (file_exists($root.$file)) {
  		$perms = fileperms($root.$file);
  		if ($perms & 0x0080) {
  		  $do=@unlink($root.$file);
  		} else {
        errorLog("Cannot delete file : ".$root.$file);
      } 
  	}
  }
  error_reporting(E_ALL);
  enableCatchErrors();
}
$tstTable=new OverallProgress();
$tst=Sql::query("select count(*) from ". $tstTable->getDatabaseTableName()) ;
if (! $tst or count($tst)==0) {
  $nbErrors+=runScript('V4.0.1.linux');
}

if (beforeVersion($currVersion,"V4.1.-")) {
	if (isset($flashReport) and ($flashReport==true or $flashReport=='true')) {
		$nbErrors+=runScript('V4.1.-.flash');
	}
}

if (beforeVersion($currVersion,"V4.2.0")) {
  traceLog("update user password changed date [4.2.0]");
	$user=new User();
	$userList=$user->getSqlElementsFromCriteria(null);
	foreach ($userList as $user) {
		if (! $user->passwordChangeDate) {
	    $user->passwordChangeDate=date('Y-m-d');
	    $user->save();
		}
	}
	
}
if (beforeVersion($currVersion,"V5.0.1") and $currVersion!='V0.0.0') {
  traceLog("update attachment on drive [5.0.1]");
  // Attachments : directory name changed from attachement_x to attachment_x
  $error=false;
  $attDir=Parameter::getGlobalParameter('paramAttachmentDirectory');
  if (file_exists($attDir)) {
    $handle = opendir($attDir);
    if (! $handle) $error=true;
    $globalCatchErrors=true;
    while (!$error and ($file = readdir($handle)) !== false) {
      if ($file == '.' || $file == '..' || $file=='index.php') {
        continue;
      }
      $filepath = ($attDir == '.') ? $file : $attDir . '/' . $file;
      if (is_link($filepath)) {
        continue;
      }
      
      if (is_dir($filepath) and substr($file,0,12)=='attachement_') { 
        $newfilepath=str_replace('attachement_', 'attachment_', $filepath);
        $res=rename($filepath,$newfilepath);
        if (!$res) {
          traceLog("Error rename $filepath into $newfilepath");
          //$error=true;
        }
      }
    }
  } else {
    traceLog("WARNING : attachment directory '$attDir' not found");
  }
  traceLog("update attachment in table [5.0.1]");
  $globalCatchErrors=false;
  $att=new Attachment();
  $lstAtt=$att->getSqlElementsFromCriteria(array()); // All attachments stored in DB
  $cpt=0;
  $cptCommit=1000;
  Sql::beginTransaction();
  traceLog("   => ".count($lstAtt)." attachments to read (may not all be updated)");
  foreach ($lstAtt as $att) {
    if ($att->subDirectory) {
      $arrayFrom=array('${attachementDirectory}','attachement_');
      $arrayTo=array('${attachmentDirectory}','attachment_');
      $att->subDirectory=str_replace($arrayFrom, $arrayTo, $att->subDirectory);
      $att->save();
      $cpt++;
      if ( ($cpt % $cptCommit) == 0) {
        Sql::commitTransaction();
        traceLog("   => $cpt attachments done...");
        projeqtor_set_time_limit(1500);
        Sql::beginTransaction();
      }
    }
  } 
  Sql::commitTransaction();
  traceLog("   => $cpt attachments updated");
}
if (beforeVersion($currVersion,"V5.0.2") and $currVersion!='V0.0.0') {
  traceLog("generate thumbs for resources [5.0.2]");
  Affectable::generateAllThumbs();
}
if (beforeVersion($currVersion,"V5.1.0.a")) {
  traceLog("update bill reference [5.1.0.a]");
  include_once("../tool/formatter.php");
  // Take into account of BillId and prefix/suffix to define new Reference format
  $prefix=Parameter::getGlobalParameter('billPrefix');
  $suffix=Parameter::getGlobalParameter('billSuffix');
  $length=Parameter::getGlobalParameter('billNumSize');
  $ref="$prefix{NUME}$suffix";
  Parameter::storeGlobalParameter('billReferenceFormat', $ref);
  $bill=new Bill();
  $bills=$bill->getSqlElementsFromCriteria(null,null, 'billId is not null', 'billId asc');
  foreach($bills as $bill) {
    $bill->reference=str_replace('{NUME}', numericFixLengthFormatter( $bill->billId,$length), $ref);
    $bill->save();
  }
}  

if (beforeVersion($currVersion,"V5.1.0.a") and $currVersion!='V0.0.0' and Sql::isMysql()) {
  // Must remove default enforceUTF8
  $maintenanceDisableEnforceUTF8=true;
  Parameter::regenerateParamFile();
}
if (beforeVersion($currVersion,"V5.1.5") and afterVersion($currVersion, "V5.1.0")) {
  // Fresh installs from 5.1.0 to 5.1.4 left many parameters in file, that were moved to database
  // must clean parameter file to enforce db value
  Parameter::regenerateParamFile();
}
if (beforeVersion($currVersion,"V5.2.0") and $currVersion!='V0.0.0') {
  traceLog("update work elements [5.2.0]");
  setSessionUser(new User());
  $we=new WorkElement();
  $weList=$we->getSqlElementsFromCriteria(null,false, "realWork>0");
  $cpt=0;
  $cptCommit=100;
  Sql::beginTransaction();
  traceLog("   => ".count($weList)." to update");
  if (count($weList)<1000) {
    projeqtor_set_time_limit(1500);
  } else {
    traceLog("   => setting unlimited execution time for script (more than 1000 work elements to update)");
    projeqtor_set_time_limit(0);
  }
  foreach($weList as $we) {
    $res=$we->save();
    $cpt++;
    if ( ($cpt % $cptCommit) == 0) {
      Sql::commitTransaction();
      traceLog("   => $cpt work elements done...");      
      Sql::beginTransaction();
    } 
  }
  Sql::commitTransaction();
  traceLog("   => $cpt work elements updated");
}

if (beforeVersion($currVersion,"V5.3.0") and $currVersion!='V0.0.0') {
  traceLog("update version project for versions of all components [5.3.0]");
  $comp=new Component();
  $compList=$comp->getSqlElementsFromCriteria(null,false,null,null,false,true); // List all components
  $cpt=0;
  $cptCommit=100;
  Sql::beginTransaction();
  traceLog("   => ".count($compList)." components to update");
  if (count($compList)<1000) {
    projeqtor_set_time_limit(1500);
  } else {
    traceLog("   => setting unlimited execution time for script (more than 1000 work elements to update)");
    projeqtor_set_time_limit(0);
  }
  foreach($compList as $comp) {
    $comp->updateAllVersionProject();
    $cpt++;
    if ( ($cpt % $cptCommit) == 0) {
      Sql::commitTransaction();
      traceLog("   => $cpt components done...");
      Sql::beginTransaction();
    }
  }
  Sql::commitTransaction();
  traceLog("   => $cpt components updated");
}

if ($currVersion=='V5.5.0' and Sql::isPgsql()) {
  traceLog("   => Fix issues on tenderstatus for PostgreSql database");
  traceLog("   => If issue has already been fixed, don't care about errors");
  $nbErrorsPg=runScript('V5.5.1.pg');
}
if (beforeVersion($currVersion,"V5.5.4") and $currVersion!='V0.0.0' and file_exists('../api/.htpasswd')) {
  traceLog("   => Removing default .htpassword file in API to avoid security leak");
  $pwd=file_get_contents('../api/.htpasswd');
  if (strpos($pwd,'admin:$apr1$31cb5jwm$Ae3XumMQ1ckxUerDZoi290')!==null) {
    if (! rename('../api/.htpasswd','../api/.htpasswd.sav') ) {
      traceLog("   => Could not rename ../api/.htpasswd - this can be a security leak");
      echo "Could not rename file '../api/.htpasswd' - this can be a security leak<br/>";
      echo "Try and rename or remove this file to secure your data<br/><br/>";
      $nbErrors++;
    }
  }
}
// To be sure, after habilitations updates ...
Habilitation::correctUpdates();
Habilitation::correctUpdates();
Habilitation::correctUpdates();
deleteDuplicate();
Sql::saveDbVersion($version);
traceLog('=====================================');
traceLog("");
echo '<div class="message'.(($nbErrors==0)?'OK':'WARNING').'">';
echo "__________________________________";
echo "<br/><br/>";
if ($nbErrors==0) {
  traceLog("DATABASE UPDATE COMPLETED TO VERSION " . $version);
  echo "DATABASE UPDATE COMPLETED <br/>TO VERSION " . $version;
} else {
  traceLog($nbErrors . " ERRORS DURING UPDATE TO VERSION " . $version );
  echo $nbErrors . " ERRORS DURING UPDATE <BR/>TO VERSION " . $version . "<br/>";
  echo "(details of errors in log file)";
}
traceLog("");
traceLog("=====================================");
traceLog("");
echo "<br/>__________________________________<br/><br/>";
echo '</div>';