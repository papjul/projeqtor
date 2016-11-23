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
$projeqtor = 'loaded';
spl_autoload_register ( 'projeqtorAutoload', true );
//include_once ('../model/User.php');
global $targetDirImageUpload;
$targetDirImageUpload='../files/images/';
// Example
if ( is_session_started() === FALSE ) {
  session_start ();
} else {
  echo "ProjeQtOr is not compatible with session auto start.<br/>";
  echo "session.auto_start must be disabled (set to Off or 0). <br/>";
  echo "Update your php.ini file : session.auto_start = 0<br/>";
  echo "or create .htaccess at projeqtor root with : php_flag session.auto_start Off";
  exit;
}
// Setup session. Must be first command.
// === Application data : version, dependencies, about message, ...
$applicationName = "ProjeQtOr"; // Name of the application
$copyright = $applicationName; // Copyright to be displayed
$version = "V6.0.1"; // Version of application : Major / Minor / Release
$build = "0152"; // Build number. To be increased on each release
$website = "http://www.projeqtor.org"; // ProjeQtOr site url
if (!isset($aesKeyLength)) { // one can define key lenth to 256 in parameters.php with $aesKeyLength=256; // valid values are 128, 192 and 256
  $aesKeyLength=128;
}
/**
 * ============================================================================
 * Global tool script for the application.
 * Must be included (include once) on each script remotely called.
 * $Revision$
 * $Date$
 */
// some servers provide empty PHP_SELF, fill it
if (! isset($_SERVER ['PHP_SELF']) or ! $_SERVER ['PHP_SELF']) { // PHP_SELF do not exist or is empty
  $_SERVER ['PHP_SELF']=$_SERVER ['SCRIPT_NAME'];
} 
date_default_timezone_set ( 'Europe/Paris' );
$globalCatchErrors = false;
$globalSilentErrors = false;
set_exception_handler ( 'exceptionHandler' );
set_error_handler ( 'errorHandler' );
$browserLocale = "";
$reportCount = 0;
include_once ("../tool/file.php");
include_once "../tool/html.php"; // include html functions
if (!defined('PHP_VERSION_ID')) {
  $version = explode('.',PHP_VERSION);
  define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
/*
 * ============================================================================ global variables ============================================================================
 */
if (is_file ( "../tool/parametersLocation.php" )) {
  // Location of the parameters file should be changed.
  // For security reasons, you should move it to a non web accessed directory.
  // Just create parametersLocation.php file including just one line :
  // <?php $parametersLocation='location of your parameters file';
  include_once "../tool/parametersLocation.php";
  if (! is_file ( $parametersLocation )) {
    echo "*** ERROR ****<br/>";
    echo " parameter file not found at '" . $parametersLocation . "'<br/>";
    echo " Check file '/tool/parametersLocation.php' or remove it to use '/tool/parameters.php'.<br/>";
    echo " <br/>";
    echo " If problem persists, you may get some help at the forum at <a href='http://www.projeqtor.org/'>ProjeQtOr web site </a>.";
    exit ();
  }
  include_once $parametersLocation;
} else {
  setSessionValue('setup', true, true);
  if (is_file ( "../tool/config.php" )  and !(isset ( $indexPhp ) and $indexPhp)) {
    include_once "../tool/config.php";
    exit ();
  }
  include_once "../tool/parameters.php"; // New in 0.6.0 : No more need to change this line if you move this file. See above.
}

$tz = Parameter::getGlobalParameter ( 'paramDefaultTimezone' );
if ($tz)
  date_default_timezone_set ( $tz );
if (! isset ( $noScriptLog )) {
  scriptLog ( $_SERVER ["SCRIPT_NAME"] );
}
$testMode = false; // Setup a variable for testing purpose test.php changes this value to true
$i18nMessages = null; // Array containing messages depending on local (initialized at first need)

setupLocale (); // Set up the locale : must be called before any call to i18n()
securityCheckRequest ();

// About message (click on Logo)
$aboutMessage = ''; // About message to be displayed when clicking on application logo
$aboutMessage .= '<div>' . $applicationName . ' ' . $version . ' (' . ($build + 0) . ')</div><br/>';
$aboutMessage .= '<div>' . i18n ( "aboutMessageWebsite" ) . ' : <a target=\'#\' href=\'' . $website . '\'>' . $website . '</a></div><br/>';
if (isset($paramSupportEmail)) {
  $aboutMessage .= '<div>' . i18n ( "colEmail" ) . ' : <a target=\'#\' href=\'mailto:' . $paramSupportEmail . '\'>' . $paramSupportEmail . '</a></div><br/>';
}

// $paramIconSize=setupIconSize(); //Not used any more this way - user Parameter::getUserParameter("paramIconSize");
$cr = "\n"; // Line feed (just for html dynamic building, to ease debugging

$isAttachmentEnabled = true; // allow attachment
if (! Parameter::getGlobalParameter ( 'paramAttachmentDirectory' ) or ! Parameter::getGlobalParameter ( 'paramAttachmentMaxSize' )) {
  $isAttachmentEnabled = false;
}

if (isset($debugReport) and $debugReport) {
  $pos=strpos($_SERVER ["SCRIPT_NAME"], '/report/');
  if ($pos!==false) {
    echo substr($_SERVER ["SCRIPT_NAME"],$pos);
  }
}
if (false === function_exists('lcfirst')) {
  function lcfirst( $str ) {
    $str[0] = strtolower($str[0]);
    return (string)$str;
  }
}
/*
 * ============================================================================ main controls ============================================================================
 */

// Check 'magic_quotes' : must be disabled ====================================
if (get_magic_quotes_runtime ()) {
  @set_magic_quotes_runtime ( 0 );
}
$page = $_SERVER ['PHP_SELF'];
if (! (isset ( $maintenance ) and $maintenance) and ! (isset ( $batchMode ) and $batchMode) and ! (isset ( $indexPhp ) and $indexPhp)) {
  // Get the user from session. If not exists, request connection ===============
  if (getSessionUser() and getSessionUser()->id) {
    $user = getSessionUser();
    // user must be a User object. Otherwise, it may be hacking attempt.
    if (get_class ( $user ) != "User") {
      // Hacking detected
      traceLog ( "'user' is not an instance of User class. May be a hacking attempt from IP " . $_SERVER ['REMOTE_ADDR'] );
      envLog ();
      $user = null;
      throw new Exception ( i18n ( "invalidAccessAttempt" ) );
    }
    $oldRoot = "";
    if (array_key_exists ( 'appRoot', $_SESSION )) {
      $oldRoot = $_SESSION ['appRoot'];
    }
    if ($oldRoot != "" and $oldRoot != getAppRoot ()) {
      $appRoot = getAppRoot ();
      traceLog ( "Application root changed (from $oldRoot to $appRoot). New Login requested for user '" . $user->name . "' from IP " . $_SERVER ['REMOTE_ADDR'] );
      // session_destroy();
      Audit::finishSession ();
      $user = null;
    }
  } else {
    $user = null;
  }
  $pos = strrpos ( $page, "/" );
  if ($pos) {
    $page = substr ( $page, $pos + 1 );
  }
  scriptLog ( "Page=" . $page );
  if (! $user and $page != 'loginCheck.php' and $page != 'getHash.php' and $page != 'saveDataToSession.php') {
    $cookieHash = User::getRememberMeCookie ();
    if (! empty ( $cookieHash )) {
      $cookieUser = SqlElement::getSingleSqlElementFromCriteria ( 'User', array (
          'cookieHash' => $cookieHash 
      ) );
      if ($cookieUser and $cookieUser->id) {
        $user = $cookieUser;
        $loginSave = true;
        $user->setCookieHash ();
        $user->save ();
        $user->finalizeSuccessfullConnection(true);
        setSessionUser($user);
      }
    }
    if (! $user) {
      if (is_file ( "login.php" )) {
        include "login.php";
      } else {
        echo '<input type="hidden" id="lastOperation" name="lastOperation" value="testConnection">';
        echo '<input type="hidden" id="lastOperationStatus" name="lastOperationStatus" value="ERROR">';
        echo '<span class="messageERROR" >' . i18n ( 'errorConnection' ) . '</span>';
      }
      exit ();
    }
  }

  if (isset ( $user )) {
    if ($user->isLdap == 0) {
      if ($user and $page != 'loginCheck.php' and $page != "changePassword.php") {
        $changePassword = false;
        if (array_key_exists ( 'changePassword', $_REQUEST )) {
          $changePassword = true;
        }
        if (! $user->crypto) {
          $changePassword = true;
        } else {
          $defaultPwd = Parameter::getGlobalParameter ( 'paramDefaultPassword' );
          if ($user->crypto == "md5") {
            $defaultPwd = md5 ( $defaultPwd . $user->salt );
          } else if ($user->crypto == "sha256") {
            $defaultPwd = hash ( "sha256", $defaultPwd . $user->salt );
          }
          if ($user->password == $defaultPwd) {
            $changePassword = true;
          }
          $passwordValidityDays = Parameter::getGlobalParameter ( 'passwordValidityDays' );
          if ($passwordValidityDays and isset ( $user->passwordChangeDate )) {
            if (addDaysToDate ( $user->passwordChangeDate, $passwordValidityDays ) < date ( 'Y-m-d' )) {
              $changePassword = true;
              traceLog ( "password expired for user '$user->name'" );
            }
          }
        }
        if ($changePassword) {
          if (is_file ( "../view/passwordChange.php" )) {
            include "../view/passwordChange.php";
          } else {
            echo '<input type="hidden" id="lastOperation" name="lastOperation" value="testPassword">';
            echo '<input type="hidden" id="lastOperationStatus" name="lastOperationStatus" value="ERROR">';
            echo '<span class="messageERROR" >' . i18n ( 'invalidPasswordChange' ) . '</span>';
          }
          exit ();
        }
      }
    }
  }
  if (isset ( $user )) {
    Audit::updateAudit ();
  }
}
/*
 * ============================================================================ functions ============================================================================
 */

/**
 * ============================================================================
 * Set up the locale
 * May be found in request : transmitted from dojo (javascript)
 *
 * @return void
 */
function setupLocale() {
  global $currentLocale, $browserLocale, $browserLocaleDateFormat;
  $paramDefaultLocale = Parameter::getGlobalParameter ( 'paramDefaultLocale' );
  if (isset ( $_SESSION ['currentLocale'] )) {
    // First fetch in Session (filled in at login depending on user parameter)
    $currentLocale = $_SESSION ['currentLocale'];
  } else if (isset ( $_REQUEST ['currentLocale'] )) {
    // Second fetch from request (for screens before user id identified)
    $currentLocale = trim($_REQUEST ['currentLocale']);
    Security::checkValidLocale($currentLocale);
    $_SESSION ['currentLocale'] = $currentLocale;
    $i18nMessages = null; // Should be null at this moment, just to be sure
  } else {
    // none of the above methods worked : get the default one form parameter file
    $currentLocale = $paramDefaultLocale;
  }
  if (isset ( $_SESSION ['browserLocale'] )) {
    $browserLocale = $_SESSION ['browserLocale'];
  } else {
    $browserLocale = $currentLocale;
  }
  $_SESSION ['lang'] = $currentLocale; // Must be kept for user parameter screen initialization
  if (isset ( $_SESSION ['browserLocaleDateFormat'] )) {
    $browserLocaleDateFormat = $_SESSION ['browserLocaleDateFormat'];
  }
}

/**
 * ============================================================================
 * Set up the icon size, converting session text value (small, medium, big)
 * to int corresponding value (16, 22, 32)
 *
 * @return void
 */
// Not used any more this way - user Parameter::getUserParameter("paramIconSize");
/*
 * function setupIconSize() { global $iconSizeMode; $paramIconSize=Parameter::getGlobalParameter('paramIconSize');; //default // Search in Session, if found, convert from text to int corresponding value if (isset($_SESSION['iconSize'])) { $iconSizeMode = $_SESSION['iconSize']; switch ($iconSizeMode) { case 'small' : $paramIconSize='16'; break; case 'medium' : $paramIconSize='22'; break; case 'big' : $paramIconSize='32'; break; } } return $paramIconSize; }
 */

/**
 * ============================================================================
 * Internationalization / same function exists in js exploiting same resources
 *
 * @param $str the
 *          code of the message to search and translate
 * @return the translated message (or the input message if not found)
 */
function i18n($str, $vars = null) {
  // **********************************************************
  // IMPORTANT
  // ==========================================================
  // This procedure is called before any parameter is set
  // So don't use any database access (objects use db)
  // and don't use any log function (such as traceLog or other debug tracing function)
  global $i18nMessages, $currentLocale;
  $i18nSessionValue='i18nMessages'.((isset($currentLocale))?$currentLocale:'');
  // on first use, initialize $i18nMessages
  if (! $i18nMessages) { // Try and retrieve from session : not activated as not performance increased
    $i18nMessages=getSessionValue($i18nSessionValue,null,false); 
  }
  if (! $i18nMessages) {
    $filename = "../tool/i18n/nls/lang.js";
    $i18nMessages = array ();
    if (isset ( $currentLocale )) {
      $testFile = "../tool/i18n/nls/" . $currentLocale . "/lang.js";
      if (file_exists ( $testFile )) {
        $filename = $testFile;
      }
    }
    $file = fopen ( $filename, "r" );
    while ( $line = fgets ( $file ) ) {
      $split = explode ( ":", $line );
      if (isset ( $split [1] )) {
        $var = trim ( $split [0], ' ' );
        $valTab = explode ( ",", $split [1] );
        $val = trim ( $valTab [0], ' ' );
        $val = trim ( $val, '"' );
        $i18nMessages [$var] = $val;
      }
    }
    fclose ( $file );
    
    // Retrieve Plugin Translation files ==============================
    $langFileList=array();
    $pluginList=Plugin::getInstalledPluginNames();
    $locale=(isset($currentLocale))?$currentLocale:'';
    foreach ($pluginList as $plugin) {
      $testLocale=Plugin::getDir().'/'.$plugin.'/nls/'.$locale."/lang.js";
      $testDefault=Plugin::getDir().'/'.$plugin."/nls/lang.js";
      if ($locale and file_exists($testLocale)) {
        $langFileList[$plugin]=$testLocale;
      } else if (file_exists($testDefault)){
        $langFileList[$plugin]=$testDefault;
      }
    }
    
    // extra for personalizedTranslations plugin : old format (for plugin version < 1.0)
    $testLocale= "../plugin/personalizedTranslations/" . $currentLocale . "/lang.js";
    if (file_exists($testLocale)) {
      $langFileList['personalizedTranslationsLangOld']=$testLocale;
    }
    // extra for personalizedTranslations plugin : new format (for plugin version >= 1.0)
    $testLocale= "../plugin/nls/" . $currentLocale . "/lang.js";
    $testDefault="../plugin/nls/lang.js";
    if (file_exists($testLocale)) {
      $langFileList['personalizedTranslationsLang']=$testLocale;
    } else if (file_exists($testDefault)) {
      $langFileList['personalizedTranslationsLang']=$testDefault;
    }
    foreach ($langFileList as $testFile) {
      if (file_exists ( $testFile )) {
        $filename = $testFile;
        $file = fopen ( $filename, "r" );
        while ( $line = fgets ( $file ) ) {
          $split = explode ( ":", $line );
          if (isset ( $split [1] )) {
            $var = trim ( $split [0], ' ' );
            $valTab = explode ( ",", $split [1] );
            $val = trim ( $valTab [0], ' ' );
            $val = trim ( $val, '"' );
            $i18nMessages [$var] = $val;
          }
        }
        fclose ( $file );
      }
    }
    if (! isset($i18nNocache) or $i18nNocache==false) { // To help dev, do not cache captions
      //setSessionValue($i18nSessionValue,$i18nMessages,false); // does not improve unitary perfs, but may on high loaded server
    }
  }
  // fetch the message in the array
  if (array_key_exists ( $str, $i18nMessages )) {
    $ret = $i18nMessages [$str];
    if ($vars) {
      foreach ( $vars as $ind => $var ) {
        $rep = '${' . ($ind + 1) . '}';
        $ret = str_replace ( $rep, $var, $ret );
      }
    }
    return $ret;
  } else {
    return "[" . $str . "]"; // return a defaut value if message code not found
  }
}

/**
 * ============================================================================
 * Return the layout for a grid with the columns header translated (i18n)
 *
 * @param $layout the
 *          layout string
 * @return the translated layout
 */
/*
 * function layoutTranslation($layout) { $deb=strpos($layout,'${'); while ($deb) { $fin=strpos($layout,'}',$deb); if (! $fin) {exit;} $rep=substr($layout,$deb,$fin-$deb+1); $col=substr($rep,2, strlen($rep) - 3); $col=i18n('col' . ucfirst($col)); $layout=str_replace( $rep, $col, $layout); $deb=strpos($layout,'${'); } return $layout; }
 */

/**
 * ============================================================================
 * Exception management
 *
 * @param $exeption the
 *          exception
 * @return void
 */
function exceptionHandler($exception) {
  $logLevel = Parameter::getGlobalParameter ( 'logLevel' );
  errorLog ( "EXCEPTION *****" );
  errorLog ( "on file '" . $exception->getFile () . "' at line (" . $exception->getLine () . ")" );
  errorLog ( "cause = " . $exception->getMessage () );
  $trace = $exception->getTrace ();
  foreach ( $trace as $indTrc => $trc ) {
    if (isset($trc ['file']) and isset($trc ['line']) and isset($trc ['function'])) {
      errorLog ( "   => #" . $indTrc . " " . $trc ['file'] . " (" . $trc ['line'] . ")" . " -> " . $trc ['function'] . "()" );
    }
  }
  // echo "<span class='messageERROR'>" . i18n("messageError") . " : " . $exception->getMessage() . "</span> ";
  // echo "(" . i18n("contactAdministrator") . ")";
  if ($logLevel >= 3) {
    throwError ( $exception->getMessage () );
  } else {
    throwError ( i18n ( 'exceptionMessage', array (
        date ( 'Y-m-d' ),
        date ( 'H:i:s' ) 
    ) ) );
  }
}

/**
 * ============================================================================
 * Error management
 *
 * @param $exeption the
 *          exception
 * @return void
 */
function errorHandler($errorType, $errorMessage, $errorFile, $errorLine) {
  global $globalCatchErrors, $globalSilentErrors;
  $logLevel = Parameter::getGlobalParameter ( 'logLevel' );
  if ($globalSilentErrors) {
    return true;
  }
  if (! strpos ( $errorMessage, "getVersion.php" ) and ! strpos ( $errorMessage, "file-get-contents" ) and ! strpos ( $errorMessage, "function.session-destroy" )) {
    errorLog ( "ERROR *****" );
    errorLog ( "on file '" . $errorFile . "' at line (" . $errorLine . ")" );
    errorLog ( "cause = " . $errorMessage );
  }
  // echo "<span class='messageERROR'>" . i18n("messageError") . " : " . $exception->getMessage() . "</span> ";
  // echo "(" . i18n("contactAdministrator") . ")";
  if ($globalCatchErrors) {
    return true;
  }
  if ($logLevel >= 3) {
    throwError ( $errorMessage . "<br/>&nbsp;&nbsp;&nbsp;in " . basename ( $errorFile ) . "<br/>&nbsp;&nbsp;&nbsp;at line " . $errorLine, true );
  } else {
    throwError ( i18n ( 'errorMessage', array (
        date ( 'Y-m-d' ),
        date ( 'H:i:s' ) 
    ) ) );
  }
}

function enableCatchErrors() {
  global $globalCatchErrors;
  $globalCatchErrors = true;
}

function disableCatchErrors() {
  global $globalCatchErrors;
  $globalCatchErrors = false;
}
function enableSilentErrors() {
  global $globalSilentErrors;
  $globalSilentErrors = true;
}

function disableSilentErrors() {
  global $globalSilentErrors;
  $globalSilentErrors = false;
}

function traceHack($msg = "Unidentified source code") {
  errorLog ( "HACK ================================================================" );
  errorLog ( "Try to hack detected" );
  errorLog ( " Source Code = " . $msg );
  errorLog ( " QUERY_STRING = " . $_SERVER ['QUERY_STRING'] );
  errorLog ( " REMOTE_ADDR = " . $_SERVER ['REMOTE_ADDR'] );
  errorLog ( " SCRIPT_FILENAME = " . $_SERVER ['SCRIPT_FILENAME'] );
  // FIX FOR IIS
  if (! isset ( $_SERVER ['REQUEST_URI'] )) {
    $_SERVER ['REQUEST_URI'] = substr ( $_SERVER ['PHP_SELF'], 1 );
    if (isset ( $_SERVER ['QUERY_STRING'] )) {
      $_SERVER ['REQUEST_URI'] .= '?' . $_SERVER ['QUERY_STRING'];
    }
  }
  errorLog ( " REQUEST_URI = " . $_SERVER ['REQUEST_URI'] );
  require "../tool/hackMessage.php"; // Will call exit
  // exit; / exit is called in hackMessage
}

function securityCheckPage($page) {
  $path = $page;
  $pos = strpos($path, '?');
  if ($pos !== FALSE) {//there are parameters
    $path = substr($path, 0, $pos); // path up to parameters
  }
  if ((substr($path, -4) !== '.php') || // verify that path ends with '.php'
  (strpos($path, ":") !== FALSE) || // verify $path does not use a URL wrapper
  (file_exists($path) === FALSE)) { // verify $path is an actual file
    traceHack("securityCheckPage($page) - not .php or URL wrapper or not actual file");
    exit (); // Not required : traceHack already exits script
  }
  $allowed_folders = array(realpath("../tool/"),
      realpath("../view/"),
      realpath("../report/"),realpath("../report/object/"));
  if (! in_array(dirname(realpath($path)),$allowed_folders) ) {
    traceHack("securityCheckPage($page) - '".dirname(realpath($path))."' is not in allowed folders list");
    exit (); // Not required : traceHack already exits script
  }
}


/**
 * ============================================================================
 * Format error message, display it and exit script
 * NB : error messages are not using i18n (because it may be the origin of the error)
 * Error messages are always displayed in english (hard coded)
 *
 * @param $message string
 *          the message of the error to be returned
 * @param $code not
 *          used
 * @return void
 */
function throwError($message, $noEncode=false) {
  global $globalCatchErrors, $globalCronMode;
  if (isset ( $globalCronMode )) {
    traceLog ( "Cron error : " . $message );
    if ($globalCronMode == false) {
      traceLog ( "CRON IS STOPPED TO AVOID MULTIPLE-TREATMENT OF SAME FILES" );
      exit ();
    }
  } else {
    $msg=($noEncode)?$message:htmlspecialchars($message,ENT_QUOTES,'UTF-8'); // $noEncode used only on errorHandler : message is PHP error
    echo '<div class="messageERROR" >ERROR : ' . $msg . '</div>';
    echo '<input type="hidden" id="lastSaveId" value="" />';
    echo '<input type="hidden" id="lastOperation" value="ERROR" />';
    echo '<input type="hidden" id="lastOperationStatus" value="ERROR" />';
    if (! $globalCatchErrors) {
      exit ();
    }
  }
}

/**
 * ============================================================================
 * Autoload fonction, to automatically load classes
 * Class file is searched in :
 * 1 => current directory (same as current script) [DISABLED]
 * 2 => model directory => all object model classes should be here
 * 3 => model/persistence => all Sql classes, to interact with database
 * 4 => tool directory [DISABLED]
 *
 * @param $className string
 *          the name of the class
 * @return void
 */
$hideAutoloadError=false;
function projeqtorAutoload($className) {
  global $hideAutoloadError;
  if (preg_match('/\.\./', trim($className)) == true) {
	  traceHack("Directory traversal in className = $className");
	  exit;	
  }

  $localfile = ucfirst ( $className ) . '.php'; // locally
  $customfile = '../model/custom/' . $localfile; // Custom directory
  $modelfile = '../model/' . $localfile; // in the model directory
  $persistfile = '../model/persistence/' . $localfile; // in the model/persistence directory
  if (is_file ( $customfile )) {
    require_once $customfile;
  } elseif (is_file ( $modelfile )) {
    require_once $modelfile;
  } elseif (is_file ( $persistfile )) {
    require_once $persistfile;
  } else {
    if (! $hideAutoloadError) {
      errorLog ( "Impossible to load class $className<br/>" . "  => Not found in $customfile <br/>" . "  => Not found in $modelfile <br/>" . "  => Not found in $persistfile <br/>" );
      debugPrintTraceStack();
    }
    return false;
  }
}

/**
 * ============================================================================
 * Return the id of the current connected user (user stored in session)
 * If an weird data is detected (user not existing, user not of User class) an error is raised
 *
 * @return the current user id or raises an error
 */
function getCurrentUserId() {
  if (! sessionUserExists()) {
    throw new Exception ( "ERROR user does not exist" );
    exit ();
  }
  $user = getSessionUser();
  if (get_class ( $user ) != 'User') {
    throw new Exception ( "ERROR user is not a User object" );
    exit ();
  }
  return $user->id;
}

/**
 * ===========================================================================
 * New function that merges array, but preseves numeric keys (unlike array_merge)
 *
 * @param
 *          any number of arrays
 *          @retrun the arrays merged into one, preserving keys (even numeric ones)
 */
function array_merge_preserve_keys() {
  $params = func_get_args ();
  $result = array ();
  foreach ( $params as &$array ) {
    foreach ( $array as $key => &$value ) {
      $result [$key] = $value;
    }
  }
  return $result;
}

function array_sum_preserve_keys() {
  $params = func_get_args ();
  $result = array ();
  foreach ( $params as &$array ) {
    foreach ( $array as $key => &$value ) {
      if (isset ( $result [$key] )) {
        $result [$key] += $value;
      } else {
        $result [$key] = $value;
      }
    }
  }
  return $result;
}

/**
 * ===========================================================================
 * Check if menu can be displayed, depending of user profile
 *
 * @param $menu the
 *          name of the menu to check
 * @return boolean, true if displayable, false either
 */
function securityCheckDisplayMenu($idMenu, $class = null) {
  $user = null;
  $menu = $idMenu;
  if (! $idMenu and $class) {
    $menu = SqlList::getIdFromName ( 'MenuList', 'menu' . $class );
  }
  if (sessionUserExists()) {
    $user = getSessionUser();
  }
  if (! $user) {
    return false;
  }
  $result=false;
  $type=SqlList::getFieldFromId('Menu', $idMenu, 'type');
  if ($type=='Project' or $class=='Project') {
    $allProfiles=$user->getAllProfiles();
    foreach ($allProfiles as $profile) {
      $crit = array ();
      $crit ['idProfile'] = $profile;
      $crit ['idMenu'] = $menu;
      $obj = SqlElement::getSingleSqlElementFromCriteria ( 'Habilitation', $crit );
      if ($obj->id != null and $obj->allowAccess == 1) {
        $result=true;
        break;
      }
    }
  } else {
    $crit ['idProfile'] = $user->idProfile;
    $crit ['idMenu'] = $menu;
    $obj = SqlElement::getSingleSqlElementFromCriteria ( 'Habilitation', $crit );
    if ($obj->id != null and $obj->allowAccess == 1) {
      $result=true;
    }
  }
  return $result;
}

/**
 * ===========================================================================
 * Get the list of Project Id that are visible : the selected project and its
 * sub-projects
 * At the difference of User->getVisibleProjects(),
 * selected Project is taken into account
 *
 * @return the list of projects as a string of id
 */
function getVisibleProjectsList($limitToActiveProjects = true, $idProject = null) {
  if (! array_key_exists ( 'project', $_SESSION )) {
    return '( 0 )';
  }
  if ($idProject) {
    $project = $idProject;
  } else {
    $project = $_SESSION ['project'];
  }
  $keyVPL = (($limitToActiveProjects) ? 'TRUE' : 'FALSE') . '_' . (($project) ? $project : '*');
  if (! isset ( $_SESSION ['visibleProjectsList'] )) {
    $_SESSION ['visibleProjectsList'] = array ();
  }
  if (isset ( $_SESSION ['visibleProjectsList'] [$keyVPL] )) {
    return $_SESSION ['visibleProjectsList'] [$keyVPL];
  }
  if ($project == "*" or $project == '') {
    $user = getSessionUser();
    $_SESSION ['visibleProjectsList'] [$keyVPL] = transformListIntoInClause ( $user->getVisibleProjects ( $limitToActiveProjects ) );
    return $_SESSION ['visibleProjectsList'] [$keyVPL];
  }
  $prj = new Project ( $project );
  $subProjectsList = $prj->getRecursiveSubProjectsFlatList ( $limitToActiveProjects );
  $result = '(0';
  if ($project != '*') {
    $result .= ', ' . $project;
  }
  foreach ( $subProjectsList as $id => $name ) {
    $result .= ', ' . $id;
  }
  $result .= ')';
  $_SESSION ['visibleProjectsList'] [$keyVPL] = $result;
  return $result;
}

function getAccesRestrictionClause($objectClass, $alias = null, $showIdle = false, $excludeUserClause=false, $excludeResourceClause=false) {
  global $reportContext;
  if (! property_exists($objectClass,'idProject')) return '(1=1)'; // If not project depedant, no extra clause
  
  $obj = new $objectClass ();
  $user=getSessionUser();
  if ($alias) {
    $tableAlias = $alias.'.';
  } else if ($alias === false) {
    $tableAlias = ''; // No alias for table
  } else {
    $tableAlias = $obj->getDatabaseTableName () . '.';
  }
  // Retrieve acces right for default profile
  if (isset($reportContext) and $reportContext==true) {
    $accessRightRead = securityGetAccessRight ( $obj->getMenuClass (), 'report' );
  } else {
    $accessRightRead = securityGetAccessRight ( $obj->getMenuClass (), 'read' );
  }
  $listNO=transformListIntoInClause($user->getAccessRights($objectClass,'NO',$showIdle));
  $listOWN=(property_exists($obj,"idUser"))?transformListIntoInClause($user->getAccessRights($objectClass,'OWN',$showIdle)):null;
  $listRES=(property_exists($obj,"idResource"))?transformListIntoInClause($user->getAccessRights($objectClass,'RES',$showIdle)):null;
  $listPRO=transformListIntoInClause($user->getAccessRights($objectClass,'PRO',$showIdle));
  $listALL=transformListIntoInClause($user->getAccessRights($objectClass,'ALL',$showIdle));
  $listALLPRO=transformListIntoInClause($user->getAccessRights($objectClass,'ALL',$showIdle)+$user->getAccessRights($objectClass,'PRO',$showIdle));
  
  $clauseNO='(1=2)'; // Will dintinct the NO
  
  $clauseOWN='';
  if (! $excludeUserClause and property_exists ( $obj, "idUser" ) and substr($alias,-15)!='planningelement') {
    $clauseOWN="(".$tableAlias."idUser='".Sql::fmtId(getSessionUser()->id)."')";
  } else {
    $clauseOWN="(1=3)"; // Will distinct the OWN
  }
  
  $clauseRES='';
  if (! $excludeResourceClause and property_exists ( $obj, "idResource" ) and substr($alias,-15)!='planningelement') {
    $clauseRES="(".$tableAlias."idResource='".Sql::fmtId(getSessionUser()->id )."')";   
  } else {
    $clauseRES="(1=4)"; // Will distinct the RES
  }
    
  //$clausePRO='';
  $clauseAffPRO='';
  $fieldProj='idProject';
  $extraFieldCriteria='';
  $extraFieldCriteriaReverse='';
  if ($objectClass == 'Project') {
    if ($alias=='planningelement') {
      $fieldProj='refId';
      $extraFieldCriteria=" and refType='Project'";
      $extraFieldCriteriaReverse=" or refType!='Project'";
    } else { 
      $fieldProj='id';
    }
  }
  if ($objectClass == 'Document') {
    $v = new Version ();
    $vp = new VersionProject ();
    $clauseALLPRO="(".$tableAlias."idProject in ".$listALLPRO
    ." or (".$tableAlias."idProject is null and ".$tableAlias."idProduct in "
        ."(select idProduct from ".$v->getDatabaseTableName()." existV, ".$vp->getDatabaseTableName()." existVP "
            ."where existV.id=existVP.idVersion and existVP.idProject in ".$listALLPRO
            .")))";
  } else {
    //$clausePRO= "(".$tableAlias.$fieldProj." in ".transformListIntoInClause($user->getAffectedProjects(!$showIdle)).")";
    $clauseALLPRO= "(".$tableAlias.$fieldProj." in ".$listALLPRO.")";
  }
  
  $clauseALL='(1=1)'; // Will distinct the ALL
  
  // Build where clause depending 
  if ($accessRightRead=='NO') { // Default profile is No Access
    $queryWhere=$clauseNO;
    if ($listOWN) $queryWhere.=" or ($clauseOWN and $tableAlias$fieldProj in $listOWN $extraFieldCriteria)";
    if ($listRES) $queryWhere.=" or ($clauseRES and $tableAlias$fieldProj in $listRES $extraFieldCriteria)";
    $queryWhere.=" or ($clauseALLPRO)";
  } else if ($accessRightRead=='OWN') {
    $queryWhere="($clauseOWN";
    if ($listRES) $queryWhere.=" or ($clauseRES and $tableAlias$fieldProj in $listRES $extraFieldCriteria)";
    $queryWhere.=" or ($clauseALLPRO)";
    $queryWhere.=") and ($tableAlias$fieldProj not in $listNO or $tableAlias$fieldProj is null $extraFieldCriteriaReverse)";
  } else if ($accessRightRead=='RES') {
    $queryWhere="($clauseRES";
    if ($listOWN) $queryWhere.=" or ($clauseOWN and $tableAlias$fieldProj in $listOWN $extraFieldCriteria)";
    $queryWhere.=" or ($clauseALLPRO)";
    $queryWhere.=") and ($tableAlias$fieldProj not in $listNO or $tableAlias$fieldProj is null $extraFieldCriteriaReverse)";
  } else if ($accessRightRead=='PRO') {
    $queryWhere="($clauseALLPRO";
    if ($listRES) $queryWhere.=" or ($clauseRES and $tableAlias$fieldProj in $listRES $extraFieldCriteria)";
    if ($listOWN) $queryWhere.=" or ($clauseOWN and $tableAlias$fieldProj in $listOWN $extraFieldCriteria)";
    //$queryWhere.=" or (".$clauseALLPRO.")";
    $queryWhere.=") and ($tableAlias$fieldProj not in $listNO or $tableAlias$fieldProj is null $extraFieldCriteriaReverse)";
    } else if ($accessRightRead=='ALL') {
    $queryWhere="($tableAlias$fieldProj not in $listNO or $tableAlias$fieldProj is null $extraFieldCriteriaReverse)";
    if ($listRES) $queryWhere.=" and ($tableAlias$fieldProj not in $listRES or $tableAlias$fieldProj is null or $clauseRES $extraFieldCriteriaReverse)";
    if ($listOWN) $queryWhere.=" and ($tableAlias$fieldProj not in $listOWN or $tableAlias$fieldProj is null or $clauseOWN $extraFieldCriteriaReverse)";
  }
  return " " . $queryWhere . " ";
}

/**
 * ============================================================================
 * Return the name of the theme : defaut of selected by user
 */
function getTheme() {
  global  $indexPhp;
  if ( isset ( $indexPhp ) and $indexPhp and getSessionValue('setup', null, true)) return "ProjeQtOr"; // On first configuration, use default
  $defaultTheme = Parameter::getGlobalParameter ( 'defaultTheme' );
  if (substr ( $defaultTheme, 0, 12 ) == "ProjectOrRia") {
    $defaultTheme = "ProjeQtOr" . substr ( $defaultTheme, 12 );
  }
  $theme = 'ProjeQtOr'; // default if not always set
  if (isset ( $defaultTheme )) {
    $theme = $defaultTheme;
  }
  if (array_key_exists ( 'theme', $_SESSION ) and trim($_SESSION ['theme'])) {
    $theme = $_SESSION ['theme'];
  }
  if ($theme == "random") {
    $themes = array_keys ( Parameter::getList ( 'theme' ) );
    $rnd = rand ( 0, count ( $themes ) - 2 );
    $theme = $themes [$rnd];
    $_SESSION ['theme'] = $theme; // keep value in session to have same theme during all session...
  }
  return $theme;
}

/**
 * ===========================================================================
 * Send a mail
 *
 * @param $to the
 *          receiver of message
 * @param $title title
 *          of the message
 * @param $message the
 *          main body of the message
 * @return unknown_type
 */
function sendMail($to, $subject, $messageBody, $object = null, $headers = null, $sender = null, $attachmentsArray = null, $boundary = null) {
  // Code that caals sendMail :
  // + SqlElement::sendMailIfMailable() : sendMail($dest, $title, $message, $this)
  // + Cron::checkImport() : sendMail($to, $title, $message, null, null, null, $attachmentsArray, $boundary); !!! with attachments
  // + IndicatorValue::send() : sendMail($dest, $title, $messageMail, $obj)
  // + Meeting::sendMail() : sendMail($destList, $this->name, $vcal, $this, $headers,$sender) !!! VCAL Meeting Invite
  // + User::authenticate : sendMail($paramAdminMail, $title, $message)
  // + /tool/sendMail.php : sendMail($dest,$title,$msg)
  global $targetDirImageUpload;
  $messageBody=str_replace($targetDirImageUpload, SqlElement::getBaseUrl().substr(str_replace("..", "", $targetDirImageUpload), 0, strlen(str_replace("..", "", $targetDirImageUpload))-1), $messageBody);
  $paramMailSendmailPath = Parameter::getGlobalParameter ( 'paramMailSendmailPath' );
  $paramMailSmtpUsername = Parameter::getGlobalParameter ( 'paramMailSmtpUsername' );
  $paramMailSmtpPassword = Parameter::getGlobalParameter ( 'paramMailSmtpPassword' );
  $paramMailerType = strtolower ( Parameter::getGlobalParameter ( 'paramMailerType' ) );
  if (! isset ( $paramMailerType ) or $paramMailerType == '' or $paramMailerType == 'phpmailer') {
    // Cute method using PHPMailer : should work on all situations / First implementation on V4.0
    return sendMail_phpmailer ( $to, $subject, $messageBody, $object, $headers, $sender, $attachmentsArray );
  } else {
    $messageBody = wordwrap ( $messageBody, 70 );
    if ((isset ( $paramMailerType ) and $paramMailerType == 'mail') or ! $paramMailSmtpUsername or ! $paramMailSmtpPassword) {
      // Standard method using php mail function : do not take authentication into account
      return sendMail_mail ( $to, $subject, $messageBody, $object, $headers, $sender, $boundary );
    } else {
      // Authentified method using sockets : cannot send vCalendar or mails with attachments
      return sendMail_socket ( $to, $subject, $messageBody, $object, $headers, $sender, $boundary );
    }
  }
}

function sendMail_phpmailer($to, $title, $message, $object = null, $headers = null, $sender = null, $attachmentsArray = null) {
  scriptLog ( 'sendMail_phpmailer' );
  global $logLevel;
  $paramMailSender = Parameter::getGlobalParameter ( 'paramMailSender' );
  $paramMailReplyTo = Parameter::getGlobalParameter ( 'paramMailReplyTo' );
  $paramMailSmtpServer = Parameter::getGlobalParameter ( 'paramMailSmtpServer' );
  $paramMailSmtpPort = Parameter::getGlobalParameter ( 'paramMailSmtpPort' );
  $paramMailSendmailPath = Parameter::getGlobalParameter ( 'paramMailSendmailPath' );
  $paramMailSmtpUsername = Parameter::getGlobalParameter ( 'paramMailSmtpUsername' );
  $paramMailSmtpPassword = Parameter::getGlobalParameter ( 'paramMailSmtpPassword' );
  $paramMailSenderName = Parameter::getGlobalParameter ( 'paramMailReplyToName' );
  $eol = Parameter::getGlobalParameter ( 'mailEol' );
  if ($paramMailSmtpServer == null or strtolower ( $paramMailSmtpServer ) == 'null' or ! $paramMailSmtpServer) {
    return "";
  }
  // Save data of the mail ===========================================================
  $mail = new Mail ();
  if (sessionUserExists()) {
    $mail->idUser = getSessionUser()->id;
  }
  if ($object) {
    $mail->idProject = (property_exists ( $object, 'idProject' )) ? $object->idProject : null;
    $mail->idMailable = SqlList::getIdFromName ( 'Mailable', get_class ( $object ) );
    $mail->refId = $object->id;
    $mail->idStatus = (property_exists ( $object, 'idStatus' )) ? $object->idStatus : null;
  }
  $mail->mailDateTime = date ( 'Y-m-d H:i' );
  $mail->mailTo = $to;
  $mail->mailTitle = $title;
  //$mail->mailBody = $message;
  $mail->mailStatus = 'WAIT';
  $mail->idle = '0';
  $resMail = $mail->save ();
  if (stripos ( $resMail, 'id="lastOperationStatus" value="ERROR"' ) > 0) {
    errorLog ( "Error storing email in table : " . $resMail );
  }
  
  enableCatchErrors ();
  $resultMail = "NO";
  
  require_once '../external/PHPMailer/class.phpmailer.php';
  require_once '../external/PHPMailer/class.smtp.php';
  $phpmailer = new PHPMailer ();
  ob_start ();
  if ($logLevel>='3') $phpmailer->SMTPDebug=1;
  $phpmailer->isSMTP (); // Set mailer to use SMTP
  $phpmailer->Host = $paramMailSmtpServer; // Specify main smtp server
  $phpmailer->Port = $paramMailSmtpPort;
  if ($paramMailSmtpUsername and $paramMailSmtpPassword) {
    $phpmailer->SMTPAuth = true; // Enable SMTP authentication
    $phpmailer->Username = $paramMailSmtpUsername; // SMTP username
    $phpmailer->Password = $paramMailSmtpPassword; // SMTP password
    $phpmailer->SMTPSecure = 'tls'; // default (for ports 25 and 587
    if ($paramMailSmtpPort == '465')
      $phpmailer->SMTPSecure = 'ssl'; // 465 is default for ssl
    if (strpos ( $phpmailer->Host, '://' )!==false) {
      $phpmailer->SMTPSecure = substr ( $phpmailer->Host, 0, strpos ( $phpmailer->Host, '://' ) );
      if ($phpmailer->SMTPSecure=="smtp") $phpmailer->SMTPSecure="";
      $phpmailer->Host = substr ( $phpmailer->Host, strpos ( $phpmailer->Host, '://' ) + 3 );
    }
  }
  $phpmailer->From = ($sender) ? $sender : $paramMailSender; // Sender of email
  $phpmailer->FromName = $paramMailSenderName; // Name of sender
  $toList = explode ( ';', str_replace ( ',', ';', $to ) );
  foreach ( $toList as $addrMail ) {
    $addrName = null;
    if (strpos ( $addrMail, '<' )) {
      $addrName = substr ( $addrMail, 0, strpos ( $addrMail, '<' ) );
      $addrName = str_replace ( '"', '', $addrName );
      $addrMail = substr ( $addrMail, strpos ( $addrMail, '<' ) );
      $addrMail = str_replace ( array (
          '<',
          '>' 
      ), array (
          '',
          '' 
      ), $addrName );
    }
    $phpmailer->addAddress ( $addrMail, $addrName ); // Add a recipient with optional name
  }
  $phpmailer->addReplyTo ( $paramMailReplyTo, $paramMailSenderName ); //
  $phpmailer->WordWrap = 70; // Set word wrap to 70 characters
  $phpmailer->isHTML ( true ); // Set email format to HTML
  $phpmailer->Subject = $title; //
                               // $phpmailer->AltBody = 'Your email client does not support HTML format. The message body cannot be displayed';
  if ($headers) {
    $phpmailer->AddStringAttachment($message, "invite.ics", "7bit", "text/calendar; charset=utf-8; method=REQUEST");
    $phpmailer->Body = " "; //
    $heads = explode ( "\r\n", $headers );
  }else{
  $phpmailer->Body = $message; //
  }
  $phpmailer->CharSet = "UTF-8";
  if ($attachmentsArray) { // attachments
    if (! is_array ( $attachmentsArray )) {
      $attachmentsArray = array (
          $attachmentsArray 
      );
    }
    foreach ( $attachmentsArray as $attachment ) {
      $phpmailer->AddAttachment ( $attachment );
    }
  }
  if (trim ( $paramMailSendmailPath )) {
    ini_set ( 'sendmail_path', $paramMailSendmailPath );
    $phpmailer->IsSendmail ();
  }
  $resultMail = $phpmailer->send ();
  disableCatchErrors ();
  $debugMessages = ob_get_contents ();
  ob_end_clean ();
  if (! $resultMail) {
    errorLog ( "Error sending mail" );
    errorLog ( "   SMTP Server : " . $paramMailSmtpServer );
    errorLog ( "   SMTP Port : " . $paramMailSmtpPort );
    errorLog ( "   Mail stored in Database : #" . $mail->id );
    errorLog ( "   PHPMail error : " . $phpmailer->ErrorInfo );
    errorLog ( "   PHPMail debug : " . $debugMessages );
  }
  if ($resultMail === "NO") {
    $resultMail = "";
  }
  $mail->mailStatus = ($resultMail) ? 'OK' : 'ERROR';
  $mail->save ();
  return $resultMail;
}

function sendMail_socket($to, $subject, $messageBody, $object = null, $headers = null, $sender = null, $boundary = null) {
  scriptLog ( 'sendMail_socket' );
  $paramMailSender = Parameter::getGlobalParameter ( 'paramMailSender' );
  $paramMailReplyTo = Parameter::getGlobalParameter ( 'paramMailReplyTo' );
  error_reporting ( E_ERROR );
  $debug = false; // set to FALSE in production code
  $newLine = Parameter::getGlobalParameter ( 'mailEol' ); // "\r\n";
  $timeout = 30;
  // find location of script
  $path_info = pathinfo ( __FILE__ );
  $dir = $path_info ['dirname'];
  chdir ( $dir );
  $replyToEmailAddress = Parameter::getGlobalParameter ( 'paramMailReplyTo' );
  $replyToEmailName = Parameter::getGlobalParameter ( 'paramMailReplyToName' );
  if (! $replyToEmailName) {
    $replyToEmailName = $replyToEmailAddress;
  }
  $key = 'default';
  $smtpHost = Parameter::getGlobalParameter ( 'paramMailSmtpServer' );
  if (! $smtpHost) {
    $resultMail = 'NO';
    $to = "";
  }
  if (! strpos ( $smtpHost, '://' )) {
    $smtpHost = 'ssl://' . $smtpHost;
  }
  $smtpServers ['default'] ['server'] = $smtpHost;
  $smtpServers ['default'] ['userName'] = Parameter::getGlobalParameter ( 'paramMailSmtpUsername' );
  $smtpServers ['default'] ['passWord'] = Parameter::getGlobalParameter ( 'paramMailSmtpPassword' );
  $smtpServers ['default'] ['smtpPort'] = Parameter::getGlobalParameter ( 'paramMailSmtpPort' );
  // Save data of the mail
  $mail = new Mail ();
  if (sessionUserExists()) {
    $mail->idUser = getSessionUser()->id;
  }
  if ($object) {
    $mail->idProject = (property_exists ( $object, 'idProject' )) ? $object->idProject : null;
    $mail->idMailable = SqlList::getIdFromName ( 'Mailable', get_class ( $object ) );
    $mail->refId = $object->id;
    $mail->idStatus = (property_exists ( $object, 'idStatus' )) ? $object->idStatus : null;
  }
  $mail->mailDateTime = date ( 'Y-m-d H:i' );
  $mail->mailTo = $to;
  $mail->mailTitle = $subject;
  $mail->mailBody = $messageBody;
  $mail->mailStatus = 'WAIT';
  $mail->idle = '0';
  $mail->save ();
  //
  // start smtp
  enableCatchErrors ();
  // Fix $To Formatting for SMTP clients
  $toArray = explode ( ",", $to );
  forEach ( $toArray as &$to ) {
    $to = trim ( $to );
    $resultMail = false;
    $sock = fsockopen ( $smtpServers [$key] ['server'], $smtpServers [$key] ['smtpPort'], $errno, $errstr, $timeout );
    if (! $sock)
      break; // or loop over more smtp servers
    $res = fgets ( $sock, 515 );
    if ($debug)
      errorLog ( $res . "\n" );
    if (! empty ( $res )) {
      // send "HELO"
      $cmd = "HELO YOURSUBDOMAIN.YOURDOMAIN.com" . $newLine; // you can change this into more relevant uri
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "250" )) {
        quit ( $sock );
        break;
      }
      // send "AUTH LOGIN"
      $cmd = "AUTH LOGIN" . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "334 VXNlcm5hbWU6" )) {
        quit ( $sock );
        break;
      }
      // SEND USERNAME base64 encoded
      $cmd = base64_encode ( $smtpServers [$key] ['userName'] ) . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "334 UGFzc3dvcmQ6" )) {
        quit ( $sock );
        break;
      }
      // SEND PASSWORD base64 encoded
      $cmd = base64_encode ( $smtpServers [$key] ['passWord'] ) . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "235" )) {
        quit ( $sock );
        break;
      }
      // send SMTP command "MAIL FROM"
      $cmd = "MAIL FROM: <" . $paramMailSender . ">" . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "250" )) {
        quit ( $sock );
        break;
      }
      // tell the SMTP server who are the recipients
      $cmd = "RCPT TO: " . " <" . $to . ">" . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "250" )) {
        quit ( $sock );
        break;
      }
      // if more recipients add a line for each recipient
      // send SMTP command "DATA"
      $cmd = "DATA" . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "354" )) {
        quit ( $sock );
        break;
      }
      // send SMTP command containing whole message
      // comment out if not relevant
      $headers = "TO: " . $to . " <" . $to . ">" . $newLine;
      $headers .= "From: " . $replyToEmailName . " <" . $paramMailSender . ">" . $newLine;
      $headers .= "Reply-To: " . $replyToEmailName . " <" . $replyToEmailAddress . ">" . $newLine;
      $headers .= "Subject: " . $subject . $newLine;
      // Generate a mime boundary string
      $rnd_str = md5 ( time () );
      $mime_boundary = "==Multipart_Boundary_x{$rnd_str}x";
      $mime_alternative = "==Multipart_Boundary_x{$rnd_str}altx";
      $altcontent = "MIME-Version: 1.0" . $newLine . "Content-Type: multipart/alternative;" . " boundary=\"{$mime_alternative}\" " . $newLine . $newLine;
      $altcontent .= "This is a multi-part message in MIME format" . $newLine . $newLine . "--{$mime_alternative}" . $newLine;
      $altcontent .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $newLine . "Content-Disposition: inline" . $newLine . "Content-Transfer-Encoding: 7bit" . $newLine . $newLine . strip_tags ( preg_replace ( '#<[Bb][Rr]/?>#', PHP_EOL, $messageBody ) ) . $newLine . $newLine . "--{$mime_alternative}" . $newLine;
      $altcontent .= "Content-Type: text/html; charset=\"iso-8859-1\"" . $newLine . "Content-Disposition: inline" . $newLine . "Content-Transfer-Encoding: 7bit" . $newLine . $newLine . '<html><body style="font-family: Verdana, Arial, Helvetica, sans-serif;">' . $messageBody . '</body></html>' . $newLine . $newLine . "--{$mime_alternative}--" . $newLine;
      // Add headers for file attachment
      $headers .= $altcontent;
      $headers .= $newLine . "." . $newLine;
      $cmd = $headers;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
      if (! isValidReturn ( $res, "250" )) {
        quit ( $sock );
        break;
      }
      $resultMail = true; // ASSUME correct return for now
                          // tell SMTP we are done
      $cmd = "QUIT" . $newLine;
      fputs ( $sock, $cmd );
      $res = fgets ( $sock, 515 );
      if ($debug)
        errorLog ( "+ $cmd- $res\n" );
    }
  } // ENDING FOR EACH STATEMENT
  disableCatchErrors ();
  error_reporting ( E_ALL );
  if (! $resultMail) {
    errorLog ( "Error sending mail" );
    $smtp = $smtpServers ['default'] ['server'];
    errorLog ( "   SMTP Server : " . $smtp );
    $port = $smtpServers ['default'] ['smtpPort'];
    errorLog ( "   SMTP Port : " . $port );
    $path = $smtpServers ['default'] ['userName'];
    errorLog ( "   SMTP User : " . $path );
    errorLog ( "   Mail stored in Database : #" . $mail->id );
  }
  if ($resultMail === "NO") {
    $resultMail = "";
  }
  // save the status of the sending
  $mail->mailStatus = ($resultMail) ? 'OK' : 'ERROR';
  $mail->save ();
  return $resultMail;
}

//
// isValidReturn ()
//
// checks expected return over socket
//
function isValidReturn($ret, $expected) {
  $retLocal = trim ( $ret );
  $pos = strpos ( $retLocal, $expected );
  if ($pos === FALSE)
    return FALSE;
  if ($pos == 0)
    return TRUE;
  return FALSE;
}

//
// quit()
//
// quit if fails, probably overkill
//
function quit($sock) {
  if ($sock) {
    $cmd = "QUIT" . "\r\n";
    fputs ( $sock, $cmd );
  }
}

function sendMail_mail($to, $title, $message, $object = null, $headers = null, $sender = null, $boundary = null) {
  scriptLog ( 'sendMail_mail' );
  $paramMailSender = Parameter::getGlobalParameter ( 'paramMailSender' );
  $paramMailReplyTo = Parameter::getGlobalParameter ( 'paramMailReplyTo' );
  $paramMailSmtpServer = Parameter::getGlobalParameter ( 'paramMailSmtpServer' );
  $paramMailSmtpPort = Parameter::getGlobalParameter ( 'paramMailSmtpPort' );
  $paramMailSendmailPath = Parameter::getGlobalParameter ( 'paramMailSendmailPath' );
  $eol = Parameter::getGlobalParameter ( 'mailEol' );
  if ($paramMailSmtpServer == null or strtolower ( $paramMailSmtpServer ) == 'null') {
    return "";
  }
  // Save data of the mail
  $mail = new Mail ();
  if (sessionUserExists()) {
    $mail->idUser = getSessionUser()->id;
  }
  if ($object) {
    $mail->idProject = (property_exists ( $object, 'idProject' )) ? $object->idProject : null;
    $mail->idMailable = SqlList::getIdFromName ( 'Mailable', get_class ( $object ) );
    $mail->refId = $object->id;
    $mail->idStatus = (property_exists ( $object, 'idStatus' )) ? $object->idStatus : null;
  }
  $mail->mailDateTime = date ( 'Y-m-d H:i' );
  $mail->mailTo = $to;
  $mail->mailTitle = $title;
  $mail->mailBody = $message;
  $mail->mailStatus = 'WAIT';
  $mail->idle = '0';
  $resMail = $mail->save ();
  if (stripos ( $resMail, 'id="lastOperationStatus" value="ERROR"' ) > 0) {
    errorLog ( "Error storing email in table : " . $resMail );
  }
  // Send then mail
  if (! $headers) {
    $headers = 'MIME-Version: 1.0' . $eol;
    if ($boundary) {
      $headers .= 'Content-Type: multipart/mixed;boundary=' . $boundary . $eol;
      $headers .= $eol;
      $message = 'Your email client does not support MIME type.' . $eol . 'Your may have difficulties to read this mail or have access to linked files.' . $eol . '--' . $boundary . $eol . 'Content-Type: text/html; charset=utf-8' . $eol . $message;
    } else {
      $headers .= 'Content-Type: text/html; charset=utf-8' . $eol;
    }
    $headers .= 'From: ' . (($sender) ? $sender : $paramMailSender) . $eol;
    $headers .= 'Reply-To: ' . (($sender) ? $sender : $paramMailReplyTo) . $eol;
    $headers .= 'Content-Transfer-Encoding: 8bit' . $eol;
    $headers .= 'X-Mailer: PHP/' . phpversion ();
  }
  if (isset ( $paramMailSmtpServer ) and $paramMailSmtpServer) {
    ini_set ( 'SMTP', $paramMailSmtpServer );
  }
  if (isset ( $paramMailSmtpPort ) and $paramMailSmtpPort) {
    ini_set ( 'smtp_port', $paramMailSmtpPort );
  }
  if (isset ( $paramMailSendmailPath ) and $paramMailSendmailPath) {
    ini_set ( 'sendmail_path', $paramMailSendmailPath );
  }
  // error_reporting(E_ERROR);
  // restore_error_handler();
  enableCatchErrors ();
  $resultMail = "NO";
  if ($paramMailSmtpServer !== null) {
    $resultMail = mail ( $to, $title, $message, $headers );
  } else {
    errorLog ( "   SMTP Server not set. Not able to send mail." );
  }
  disableCatchErrors ();
  // error_reporting(E_ALL);
  // set_error_handler('errorHandler');
  if (! $resultMail) {
    errorLog ( "Error sending mail" );
    $smtp = ini_get ( 'SMTP' );
    errorLog ( "   SMTP Server : " . $smtp );
    $port = ini_get ( 'smtp_port' );
    errorLog ( "   SMTP Port : " . $port );
    $path = ini_get ( 'sendmail_path' );
    errorLog ( "   Sendmail path : " . $path );
    errorLog ( "   Mail stored in Database : #" . $mail->id );
  }
  if ($resultMail === "NO") {
    $resultMail = "";
  }
  // save the status of the sending
  $mail->mailStatus = ($resultMail) ? 'OK' : 'ERROR';
  $mail->save ();
  return $resultMail;
}

/**
 * ===========================================================================
 * Log tracing.
 * Not to be called directly. Use following functions instead.
 *
 * @param $message message
 *          to store on log
 * @param $level level
 *          of trace : 1=error, 2=trace, 3=debug, 4=script
 * @return void
 */
$previousTraceTimestamp=0;
function logTracing($message, $level = 9, $increment = 0) {
  global $debugPerf, $previousTraceTimestamp;
  $execTime="";
  if (isset($debugPerf) and $debugPerf==true) {
    if ($previousTraceTimestamp) {
      $execTime=(round(microtime(true)-$previousTraceTimestamp,3));
      $pos=strpos($execTime,'.');
      if ($pos==0) $execTime=$execTime.'.000';
      else $execTime=substr($execTime.'000',0,($pos+4));
      $execTime=" => ".$execTime;
    } else {
      $execTime=' => 0.000';
    }
    $previousTraceTimestamp=microtime(true);
  }
  $logLevel = Parameter::getGlobalParameter ( 'logLevel' );
  $tabcar = '                        ';
  if ($logLevel == 5) {
    if ($level <= 3)
      echo $message;
    return;
  }
  $logFile = Parameter::getGlobalParameter ( 'logFile' );
  if (! $logFile or $logFile == '' or $level == 9) {
    exit ();
  }
  if ($level <= $logLevel) {
    $file = str_replace ( '${date}', date ( 'Ymd' ), $logFile );
    if (is_array ( $message ) or is_object ( $message )) {
      $tab = ($increment == 0) ? '' : substr ( $tabcar, 0, ($increment * 3 - 1) );
      $txt = $tab . (is_array ( $message ) ? 'Array[' . count ( $message ) . ']' : 'Object[' . get_class ( $message ) . ']');
      logTracing ( $txt, $level, $increment );
      foreach ( $message as $ind => $val ) {
        $tab = substr ( $tabcar, 0, (($increment + 1) * 3 - 1) );
        if (is_array ( $val ) or is_object ( $val )) {
          $txt = $tab . $ind . ' => ';
          $txt .= is_array ( $val ) ? 'Array ' : 'Object ';
          logTracing ( $txt, $level, $increment + 1 );
          logTracing ( $val, $level, $increment + 1 );
        } else {
          $txt = $tab . $ind . ' => ' . $val;
          logTracing ( $txt, $level, $increment + 1 );
        }
      }
      $level = 999;
      $msg = '';
    } else {
      $msg = $message . "\n";
    }
    switch ($level) {
      case 1 :
        $msg = date ( 'Y-m-d H:i:s' ) . substr(microtime(), 1, 4) . $execTime . " ***** ERROR ***** " . $msg;
        break;
      case 2 :
        $msg = date ( 'Y-m-d H:i:s' ) . substr(microtime(), 1, 4) . $execTime . " ===== TRACE ===== " . $msg;
        break;
      case 3 :
        $msg = date ( 'Y-m-d H:i:s' ) . substr(microtime(), 1, 4) . $execTime . " ----- DEBUG ----- " . $msg;
        break;
      case 4 :
        $msg = date ( 'Y-m-d H:i:s' ) . substr(microtime(), 1, 4) . $execTime . " ..... SCRIPT .... " . $msg;
        break;
      default :
        break;
    }
    $dir = dirname ( $file );
    if (! file_exists ( $dir )) {
      echo '<br/><span class="messageERROR">' . i18n ( "invalidLogDir", array (
          $dir 
      ) ) . '</span>';
    } else if (! is_writable ( $dir )) {
      echo '<br/><span class="messageERROR">' . i18n ( "lockedLogDir", array (
          $dir 
      ) ) . '</span>';
    } else {
      writeFile ( $msg, $file );
    }
  }
}

/**
 * ===========================================================================
 * Log tracing for debug
 *
 * @param $message message
 *          to store on log 
 * @return void
 */
// debugLog to keep
function debugLog($message) {
  logTracing ( $message, 3 );
}
/**
 * ===========================================================================
 * Log tracing for debug to keep in the code
 * Will be used for debugQuery mode of for performance tracing
 * so can be considered as Trace log, but will generate a Debug message in log
 * Will be activated, depending on location, with :
 *  $debugTrace=true
 *  $debugQuery=true
 *  or directly calling traceExecutionTime() function
 * @param $message message
 *          to store on log
 * @return void
 */
function debugTraceLog($message) {
  logTracing ( $message, 3 );
}

/**
 * ===========================================================================
 * Log tracing for general trace
 *
 * @param $message message
 *          to store on log
 * @return void
 */
function traceLog($message) {
  logTracing ( $message, 2 );
}

/**
 * ===========================================================================
 * Log tracing for error
 *
 * @param $message message
 *          to store on log
 * @return void
 */
function errorLog($message) {
  if (getSessionValue('setup', null, true)) return;
  logTracing ( $message, 1 );
}

/**
 * ===========================================================================
 * Log tracing for entry into script
 *
 * @param $message message
 *          to store on log
 * @return void
 */
function scriptLog($script) {
  logTracing ( getIP () . " " . $script, 4 );
}

/**
 * ===========================================================================
 * Log a maximum of environment data (to trace hacking)
 *
 * @return void
 */
function envLog() {
  traceLog ( 'IP CLient=' . getIP () );
  if (isset ( $_REQUEST )) {
    foreach ( $_REQUEST as $ind => $val ) {
      traceLog ( '$_REQUEST[' . $ind . ']=' . $val );
    }
  }
}

/**
 * ===========================================================================
 * Get the IP of the Client
 *
 * @return the IP as a string
 */
function getIP() {
  if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
    $ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
  } else if (isset ( $_SERVER ['HTTP_CLIENT_IP'] )) {
    $ip = $_SERVER ['HTTP_CLIENT_IP'];
  } else if (isset ( $_SERVER ['REMOTE_ADDR'] )) {
    $ip = $_SERVER ['REMOTE_ADDR'];
  } else {
    $ip = 'batch';
  }
  return $ip;
}

/**
 * ===========================================================================
 * Get the access right for a menu and an access type
 *
 * @param $menuName The
 *          name of the menu; should be 'menuXXX'
 * @param $accessType requested
 *          access type : 'read', 'create', 'update', 'delete'
 * @return the access right
 *         'NO' => non access
 *         'PRO' => all elements of affected projects
 *         'OWN' => only own elements
 *         'ALL' => any element
 */
function securityGetAccessRight($menuName, $accessType, $obj = null, $user = null) {
//scriptLog("securityGetAccessRight($menuName, $accessType, ".(($obj)?get_class($obj).' #'.$obj->id:'').",". (($user)?'User #'.$user->id:'').")");  
  if (! $user) {
    $user = getSessionUser();
  }
  $accessRightList = $user->getAccessControlRights ($obj);
  $accessRight = 'ALL';
  if ($accessType == 'update' and $obj and $obj->id == null) {
    return securityGetAccessRight ( $menuName, 'create' );
  }
  if (array_key_exists ( $menuName, $accessRightList )) {
    $accessRightObj = $accessRightList [$menuName];
    if (array_key_exists ( $accessType, $accessRightObj )) {
      $accessRight = $accessRightObj [$accessType];
    }
  }
  return $accessRight;
}

/**
 * ===========================================================================
 * Get the access right for a menu and an access type, just returning 'YES' or 'NO'
 *
 * @param $menuName name  
 *   : name of the menu as 'menuXXX'
 * @param $accessType requested
 *          access type : 'read', 'create', 'update', 'delete'
 * @return the right as Yes or No (depending on object properties)
 */
function securityGetAccessRightYesNo($menuName, $accessType, $obj = null, $user = null) {
  if (substr ( $menuName, 4 )=='Admin') return 'YES';
  if (! SqlElement::class_exists ( substr ( $menuName, 4 ) )) {
    errorLog ( "securityGetAccessRightYesNo : " . substr ( $menuName, 4 ) . " is not an existing object class" );
  }
  if ($obj and property_exists($obj, 'isPrivate') and $obj->isPrivate==1 and $obj->idUser!=getSessionUser()->id) {
    return 'NO';
  }
  if (property_exists ( substr ( $menuName, 4 ), '_no' . ucfirst ( $accessType ) )) {
    return 'NO';
  }
  if (property_exists ( substr ( $menuName, 4 ), '_readOnly' ) and $accessType != 'read') {
    return 'NO';
  }
  if ($obj and $obj->id==0) {
    $obj->id=null;
  }
  if (! $user) {
    if (! sessionUserExists()) {
      global $maintenance;
      if ($maintenance) {
        return 'YES';
      } else {
        //traceLog("securityGetAccessRightYesNo : This is a case that should not exist unless hacking attempt. Exit.");
      	exit; //return 'NO'; // This is a case that should not exist unless hacking attempt or use of F5
      }
    } else {
      $user = getSessionUser();
    }
  } 
  $accessRight = securityGetAccessRight ( $menuName, $accessType, $obj, $user );
  if ($accessType == 'create') {  
    if ((!$obj or (property_exists(substr($menuName,4), 'name') and !$obj->name)) and property_exists(substr($menuName,4), 'idProject')) { // Case of project dependent screen, will allow if user has some create rights on one of his profiles
      foreach ($user->getAllProfiles() as $prf) {
        $tmpUser=new User();
        $tmpUser->idProfile=$prf;
        $accessRight = securityGetAccessRight ( $menuName, $accessType, $obj, $tmpUser );
        $accessRight = ($accessRight == 'NO' or $accessRight == 'OWN' or $accessRight == 'RES' or $accessRight=='READ') ? 'NO' : 'YES';
        if ($accessRight=='YES') break;
      }
    } else if ($accessRight == 'NO') {
      $accessRight="NO";// will return no
    } else if  ($accessRight=='READ') {
      $accessRight="NO";
    } else if ($accessRight == 'ALL' or $accessRight=='WRITE') {
      $accessRight = 'YES';
    } else if ($accessRight == 'PRO') {
      $accessRight = 'NO';
      if ($obj != null) {
        if (! $obj->id and (!property_exists(substr($menuName,4), 'name') or  !$obj->name)) {
          $accessRight = 'YES';      
        } else if (get_class ( $obj ) == 'Project') {
          if (array_key_exists ( $obj->id, $user->getAffectedProjects ( false ) )) {
            $accessRight = 'YES';
          }
        } else if (property_exists ( $obj, 'idProject' )) {
          $limitToActiveProjects = (get_class ( $obj ) == 'Affectation') ? false : true;
          if (isset ( $_SESSION ['projectSelectorShowIdle'] ) and $_SESSION ['projectSelectorShowIdle'] == 1)
            $limitToActiveProjects = false;
          if (array_key_exists ( $obj->idProject, $user->getAffectedProjects ( $limitToActiveProjects ) ) ) {
            $accessRight = 'YES';
          }
        }
      } else {
        $accessRight = 'YES';
      }
    } else if ($accessRight == 'OWN') {
      $accessRight = 'NO';
    } else if ($accessRight == 'RES') {
      $accessRight = 'NO';
    }
  } else if ($accessType == 'update' or $accessType == 'delete' or $accessType == 'read') {
    if ($accessRight == 'NO') {
      $accessRight="NO";// will return no
    } else if  ($accessRight=='READ') {
      if ($accessType == 'read') {
      	$accessRight="NO"; // TODO : why is it no here ?
      } else {
      	$accessRight="NO";
      }
    } else if ($accessRight == 'ALL' or $accessRight=='WRITE') {
      $accessRight = 'YES';
    } else if ($accessRight == 'PRO') {
      $accessRight = 'NO';
      if ($obj != null) {
        if (get_class ( $obj ) == 'Project') {
          if ( array_key_exists ( $obj->id, $user->getAffectedProjects(false))  or ! $obj->id) {
            $accessRight = 'YES';
          }
        } else if (property_exists ( $obj, 'idProject' )) {
          $limitToActiveProjects = (get_class ( $obj ) == 'Affectation') ? false : true;
          if (isset ( $_SESSION ['projectSelectorShowIdle'] ) and $_SESSION ['projectSelectorShowIdle'] == 1)
            $limitToActiveProjects = false;
          if (array_key_exists ( $obj->idProject, $user->getAffectedProjects ( $limitToActiveProjects ) ) or $obj->id == null) {
            $accessRight = 'YES';
          }
        }
      }
    } else if ($accessRight == 'OWN') {
      $accessRight = 'NO';
      if ($obj != null) {
        if (property_exists ( $obj, 'idUser' )) {
          $old = $obj->getOld ();
          if ($old->id and $user->id == $old->idUser) {
            $accessRight = 'YES';
          }
        }
      }
    } else if ($accessRight == 'RES') {
      $accessRight = 'NO';
      if ($obj != null) {
        if (property_exists ( $obj, 'idResource' )) {
          $old = $obj->getOld ();
          if ($old->id and $user->id == $old->idResource) {
            $accessRight = 'YES';
          }
        }
      }
    }
  }
  return $accessRight;
}

/**
 * ============================================================================
 * Transfor a list, as an array, into an 'IN' clause
 *
 * @param $list an
 *          array, with the id to select as index
 * @return the IN clause, as ('xx', 'yy', ... )
 */
function transformListIntoInClause($list) {
  if (count ( $list ) == 0)
    return '(0)';
  $result = '(';
  foreach ( $list as $id => $name ) {
    if (trim ( $id )) {
      $result .= ($result == '(') ? '' : ', ';
      $result .= $id;
    }
  }
  $result .= ')';
  return $result;
}

function transformValueListIntoInClause($list) {
  if (count ( $list ) == 0)
    return '(0)';
  $result = '(';
  foreach ( $list as $id => $name ) {
    if ($name) {
      $result .= ($result == '(') ? '' : ', ';
      if (is_numeric ( $name )) {
        $result .= $name;
      } else {
        $result .= "'" . $name . "'";
      }
    }
  }
  $result .= ')';
  if ($result == '()') {
    $result = '(0)';
  }
  return $result;
}

/**
 * ============================================================================
 * Calculate difference between 2 dates
 *
 * @param $start start
 *          date - format yyyy-mm-dd
 * @param $end end
 *          date - format yyyy-mm-dd
 * @return int number of work days (remove week-ends)
 */
function workDayDiffDates($start, $end) {
  if (! $start or ! $end) {
    return "";
  }
  $currentDate = $start;
  $endDate = $end;
  if ($end < $start) {
    return 0;
  }
  $duration = 0;
  while ( $currentDate <= $endDate ) {
    if (! isOffDay ( $currentDate )) {
      $duration ++;
    }
    $currentDate = addDaysToDate ( $currentDate, 1 );
  }
  return $duration;
}

/**
 * ============================================================================
 * Calculate difference between 2 dates
 *
 * @param $start start
 *          date - format yyyy-mm-dd
 * @param $end end
 *          date - format yyyy-mm-dd
 * @return int number of days
 */
function dayDiffDates($start, $end) {
  if (! trim ( $start ) or ! trim ( $end ))
    return 0;
  $tStart = explode ( "-", $start );
  $tEnd = explode ( "-", $end );
  $dStart = mktime ( 0, 0, 0, $tStart [1], $tStart [2], $tStart [0] );
  $dEnd = mktime ( 0, 0, 0, $tEnd [1], $tEnd [2], $tEnd [0] );
  $diff = $dEnd - $dStart;
  $diffDay = ($diff / 86400);
  return round ( $diffDay, 0 );
}

/**
 * ============================================================================
 * Calculate new date after adding some days
 *
 * @param $date start
 *          date - format yyyy-mm-dd
 * @param $days numbers
 *          of days to add (can be < 0 to subtract days)
 * @return new calculated date - format yyyy-mm-dd
 */
function addWorkDaysToDate_old($date, $days) {
  if ($days == 0) {
    return $date;
  }
  if ($days < 0) {
    return removeWorkDaysToDate ( $date, (- 1) * $days );
  }
  if (! $date) {
    return;
  }
  $days -= 1;
  $tDate = explode ( "-", $date );
  $dStart = mktime ( 0, 0, 0, $tDate [1], $tDate [2], $tDate [0] );
  if (date ( "N", $dStart ) >= 6) {
    $tDate [2] = $tDate [2] + 8 - date ( "N", $dStart );
    $dStart = mktime ( 0, 0, 0, $tDate [1], $tDate [2], $tDate [0] );
  }
  $weekEnds = floor ( $days / 5 );
  $additionalDays = $days - (5 * $weekEnds);
  if (date ( "N", $dStart ) + $additionalDays >= 6) {
    $weekEnds += 1;
  }
  $days += 2 * $weekEnds;
  $dEnd = mktime ( 0, 0, 0, $tDate [1], $tDate [2] + $days, $tDate [0] );
  return date ( "Y-m-d", $dEnd );
}

function addWorkDaysToDate($date, $days) {
  if (! $date) {
    return;
  }
  if ($days == 0) {
    return $date;
  }
  if ($days < 0) {
    return removeWorkDaysToDate ( $date, (- 1) * $days );
  }
  $endDate = $date;
  $left = $days;
  $left --;
  while ( $left > 0 ) {
    $endDate = addDaysToDate ( $endDate, 1 );
    if (! isOffDay ( $endDate )) {
      $left --;
    }
  }
  return $endDate;
}

function removeWorkDaysToDate($date, $days) {
  if ($days == 0) {
    return $date;
  }
  if ($days <= 0) {
    return addWorkDaysToDate ( $date, (- 1) * $days );
  }
  if (! $date) {
    return;
  }
  $endDate = $date;
  $left = $days;
  while ( $left > 0 ) {
    $endDate = addDaysToDate ( $endDate, - 1 );
    if (! isOffDay ( $endDate )) {
      $left --;
    }
  }
  return $endDate;
}

/**
 * ============================================================================
 * Calculate new date after adding some months
 *
 * @param $date start
 *          date - format yyyy-mm-dd
 * @param $months numbers
 *          of months to add (can be < 0 to subtract months)
 * @return new calculated date - format yyyy-mm-dd
 */
function addDaysToDate($date, $days) {
  // if (strlen($date)>10) $date=substr($date,0,10);
  if (! trim ( $date ))
    return null;
  $tDate = explode ( "-", $date );
  if (count ( $tDate ) < 3)
    return null;
  return date ( "Y-m-d", mktime ( 0, 0, 0, $tDate [1], $tDate [2] + $days, $tDate [0] ) );
}

/**
 * ============================================================================
 * Calculate new date after adding some months
 *
 * @param $date start
 *          date - format yyyy-mm-dd
 * @param $months numbers
 *          of months to add (can be < 0 to subtract months)
 * @return new calculated date - format yyyy-mm-dd
 */
function addMonthsToDate($date, $months) {
  $tDate = explode ( "-", $date );
  return date ( "Y-m-d", mktime ( 0, 0, 0, $tDate [1] + $months, $tDate [2], $tDate [0] ) );
}

/**
 * ============================================================================
 * Calculate new date after adding some weeks
 *
 * @param $date start
 *          date - format yyyy-mm-dd
 * @param $weeks numbers
 *          of weeks to add (can be < 0 to subtract weeks)
 * @return new calculated date - format yyyy-mm-dd
 */
function padto2($val) {
  return str_pad ( $val, 2, "0", STR_PAD_LEFT );
}

function addWeeksToDate($date, $weeks) {
  $tDate = explode ( "-", $date );
  return date ( "Y-m-d", mktime ( 0, 0, 0, $tDate [1], $tDate [2] + (7 * $weeks), $tDate [0] ) );
}

function workTimeDiffDateTime($start, $end) {
  $hoursPerDay=Parameter::getGlobalParameter ( 'dayTime' );
  $startDay=substr($start,0,10);
  $endDay=substr($end,0,10);
  $time = substr ( $start, 11, 5 );
  $hh = substr ( $time, 0, 2 );
  $mn = substr ( $time, 3, 2 );
  $mnStart = $hh * 60 + $mn;
  $time = substr ( $end, 11, 5 );
  $hh = substr ( $time, 0, 2 );
  $mn = substr ( $time, 3, 2 );
  $mnStop = $hh * 60 + $mn;
  $mnFullDay=60*24;
  if ($startDay==$endDay) {
    $days=0;
    $delay = ($mnStop - $mnStart) / (60 * $hoursPerDay);
  } else {
    $days = dayDiffDates ( $startDay, $endDay )-1;
    $delay=0;
    if ($days>0) {
      $delay=($days*$mnFullDay)/ (60 * $hoursPerDay);
    }
    $delay+=($mnFullDay - $mnStart) / (60 * $hoursPerDay);
    $delay+=($mnStop) / (60 * $hoursPerDay);
  }
  return $delay;
}

function addDelayToDatetime($dateTime, $delay, $unit) {
  $date = substr ( $dateTime, 0, 10 );
  $time = substr ( $dateTime, 11, 5 );
  if ($unit == 'DD') {
    $newDate = addDaysToDate ( $date, $delay );
    return $newDate . " " . $time;
  } else if ($unit == 'OD') {
    if ($delay < 0) {
      $newDate = removeWorkDaysToDate ( $date, (- 1) * $delay );
    } else {
      $newDate = addWorkDaysToDate ( $date, $delay + 1 );
    }
    return $newDate . " " . $time;
  } else if ($unit == 'HH') {
    $hh = substr ( $time, 0, 2 );
    $mn = substr ( $time, 3, 2 );
    $res = minutesToTime ( $hh * 60 + $mn + $delay * 60 );
    $newDate = addDaysToDate ( $date, $res ['d'] );
    return $newDate . " " . padto2 ( $res ['h'] ) . ":" . padto2 ( $res ['m'] ) . ':00';
  } else if ($unit == 'OH') {
    $startAM = Parameter::getGlobalParameter ( 'startAM' );
    $endAM = Parameter::getGlobalParameter ( 'endAM' );
    $startPM = Parameter::getGlobalParameter ( 'startPM' );
    $endPM = Parameter::getGlobalParameter ( 'endPM' );
    if (! $startAM or ! $endAM or ! $startPM or ! $endPM) {
      return $dateTime;
    }
    $mnEndAM = (substr ( $endAM, 0, 2 ) * 60 + substr ( $endAM, 3 ));
    $mnStartAM = (substr ( $startAM, 0, 2 ) * 60 + substr ( $startAM, 3 ));
    $mnEndPM = (substr ( $endPM, 0, 2 ) * 60 + substr ( $endPM, 3 ));
    $mnStartPM = (substr ( $startPM, 0, 2 ) * 60 + substr ( $startPM, 3 ));
    $mnDelay = $delay * 60;
    $hh = substr ( $time, 0, 2 );
    $mn = substr ( $time, 3, 2 );
    $mnTime = $hh * 60 + $mn;
    $AMPM = 'AM';
    if ($mnDelay >= 0) {
      if (isOffDay ( $date )) {
        $date = addWorkDaysToDate ( $date, 2 );
        $mnTime = $mnStartAM;
        $AMPM = 'AM';
      } else if ($mnTime >= $mnEndPM) {
        $date = addWorkDaysToDate ( $date, 2 );
        $mnTime = $mnStartAM;
        $AMPM = 'AM';
      } else if ($mnTime >= $mnStartPM) {
        $AMPM = 'PM';
      } else if ($mnTime >= $mnEndAM) {
        $mnTime = $mnStartPM;
        $AMPM = 'PM';
      } else if ($mnTime >= $mnStartAM) {
        $AMPM = 'AM';
      } else {
        $mnTime = $mnStartAM;
        $AMPM = 'AM';
      }
      while ( $mnDelay > 0 ) {
        if ($AMPM == 'AM') {
          $left = $mnEndAM - $mnTime;
          if ($left > $mnDelay) {
            $mnTime += $mnDelay;
            $mnDelay = 0;
          } else {
            $mnTime = $mnStartPM;
            $mnDelay -= $left;
            $AMPM = 'PM';
          }
        } else {
          $left = $mnEndPM - $mnTime;
          if ($left > $mnDelay) {
            $mnTime += $mnDelay;
            $mnDelay = 0;
          } else {
            $mnTime = $mnStartAM;
            $mnDelay -= $left;
            $date = addWorkDaysToDate ( $date, 2 );
            $AMPM = 'AM';
          }
        }
      }
    } else { // $mnDelay<0
      if (isOffDay ( $date )) {
        $date = removeWorkDaysToDate ( $date, 1 );
        $mnTime = $mnEndPM;
        $AMPM = 'PM';
      } else if ($mnTime >= $mnEndPM) {
        $mnTime = $mnEndPM;
        $AMPM = 'AP';
      } else if ($mnTime >= $mnStartPM) {
        $AMPM = 'PM';
      } else if ($mnTime >= $mnEndAM) {
        $mnTime = $mnEndAM;
        $AMPM = 'AM';
      } else if ($mnTime >= $mnStartAM) {
        $AMPM = 'AM';
      } else {
        $date = removeWorkDaysToDate ( $date, 1 );
        $mnTime = $mnEndPM;
        $AMPM = 'PM';
      }
      while ( $mnDelay < 0 ) {
        if ($AMPM == 'AM') {
          $left = $mnTime - $mnStartAM;
          if ($left > abs ( $mnDelay )) {
            $mnTime += $mnDelay;
            $mnDelay = 0;
          } else {
            $date = removeWorkDaysToDate ( $date, 1 );
            $mnTime = $mnEndPM;
            $mnDelay += $left;
            $AMPM = 'PM';
          }
        } else {
          $left = $mnTime - $mnStartPM;
          if ($left > abs ( $mnDelay )) {
            $mnTime += $mnDelay;
            $mnDelay = 0;
          } else {
            $mnTime = $mnEndAM;
            $mnDelay += $left;
            $AMPM = 'AM';
          }
        }
      }
    }
    $res = minutesToTime ( $mnTime );
    return $date . " " . padto2 ( $res ['h'] ) . ":" . padto2 ( $res ['m'] ) . ':00';
  } else {
    // return $dateTime;
  }
}

function minutesToTime($time) {
  if (is_numeric ( $time )) {
    $value = array (
        "d" => 0,
        "h" => 0,
        "m" => 0 
    );
    while ( $time < 0 ) {
      $value ["d"] -= 1;
      $time += 1440;
    }
    if ($time >= 1440) {
      $value ["d"] = floor ( $time / 1440 );
      $time = ($time % 1440);
    }
    if ($time >= 60) {
      $value ["h"] = floor ( $time / 60 );
      $time = ($time % 60);
    }
    $value ["m"] = floor ( $time );
    return ( array ) $value;
  } else {
    return ( bool ) FALSE;
  }
}

/**
 * Return wbs code as a sortable value string (pad number with zeros)
 *
 * @param $wbs wbs
 *          code
 * @return string the formated sortable wbs
 */
function formatSortableWbs($wbs) {
  $exp = explode ( '.', $wbs );
  $result = "";
  foreach ( $exp as $node ) {
    $result .= ($result != '') ? '.' : '';
    $result .= substr ( '000', 0, 3 - strlen ( $node ) ) . $node;
  }
  return $result;
}

/**
 * Calculate forecolor for a given background color
 * Return black for light backgroud color
 * Return white for dark backgroud color
 *
 * @param
 *          $color
 * @return string The fore color to fit the back ground color
 */
function getForeColor($color) {
  $foreColor = '#000000';
  if (strlen ( $color ) == 7) {
    $red = substr ( $color, 1, 2 );
    $green = substr ( $color, 3, 2 );
    $blue = substr ( $color, 5, 2 );
    $light = (0.3) * hexdec ( $red ) + (0.6) * hexdec ( $green ) + (0.1) * hexdec ( $blue );
    if ($light < 128) {
      $foreColor = '#FFFFFF';
    }
  }
  return $foreColor;
}

/*
 * calculate the first day of a given week
 */
function firstDayofWeek($week, $year) {
  $Jan1 = mktime ( 1, 1, 1, 1, 1, $year );
  $MondayOffset = (11 - date ( 'w', $Jan1 )) % 7 - 3;
  $desiredMonday = strtotime ( ($week - 1) . ' weeks ' . $MondayOffset . ' days', $Jan1 );
  return $desiredMonday;
}

/*
 * Calculate number of days between 2 dates
 */
/*  Not user anymore.  See dayDiffDates()
 function numberOfDays($startDate, $endDate) {
  $tabStart = explode("-", $startDate);
  $tabEnd = explode("-", $endDate);
  $diff = mktime(0, 0, 0, $tabEnd[1], $tabEnd[2], $tabEnd[0]) - 
          mktime(0, 0, 0, $tabStart[1], $tabStart[2], $tabStart[0]);
  return(($diff / 86400)+1);
}
*/

/* calculate the week number for a given date
 * 
 */
function weekNumber($dateValue) {
  return date ( 'W', strtotime ( $dateValue ) );
}

function weekFormat($dateValue) {
  // return date('Y-W', strtotime ($dateValue) );
  $w = (date ( 'W', strtotime ( $dateValue ) ));
  $m = (date ( 'm', strtotime ( $dateValue ) ));
  $y = (date ( 'Y', strtotime ( $dateValue ) ));
  if ($w == 1 && $m == 12) {
    return ($y+1).'-'.$w;
  } else if ($w>=52 && $m == 1) {
    return ($y-1).'-'.$w;
  } else {
    return date ( 'Y-W', strtotime ( $dateValue ) );
  }
}

/*
 * Checks if a date is a "off day" (weekend or else)
 */
function isOffDay($dateValue, $idCalendarDefinition = null) {
  if (isOpenDay ( $dateValue, $idCalendarDefinition )) {
    return false;
  } else {
    return true;
  }
}
/*
 * Checks if a date is a "off day" (weekend or else)
 */
$bankHolidays = array ();
$bankWorkdays = array ();

function isOpenDay($dateValue, $idCalendarDefinition = '1') {
  global $bankHolidays, $bankWorkdays;
  $paramDefaultLocale = Parameter::getGlobalParameter ( 'paramDefaultLocale' );
  $iDate = strtotime ( $dateValue );
  $year = date ( 'Y', $iDate );
  if (! $idCalendarDefinition)
    $idCalendarDefinition = 1;
  if (array_key_exists ( $year . '#' . $idCalendarDefinition, $bankWorkdays )) {
    $aBankWorkdays = $bankWorkdays [$year . '#' . $idCalendarDefinition];
  } else {
    $cal = new Calendar ();
    $crit = array (
        'year' => $year,
        'isOffDay' => '0',
        'idCalendarDefinition' => $idCalendarDefinition 
    );
    $aBankWorkdays = array ();
    $lstCal = $cal->getSqlElementsFromCriteria ( $crit );
    foreach ( $lstCal as $obj ) {
      $aBankWorkdays [] = $obj->day;
    }
    $bankWorkdays [$year . '#' . $idCalendarDefinition] = $aBankWorkdays;
  }
  if (array_key_exists ( $year . '#' . $idCalendarDefinition, $bankHolidays )) {
    $aBankHolidays = $bankHolidays [$year . '#' . $idCalendarDefinition];
  } else {
    $cal = new Calendar ();
    $crit = array (
        'year' => $year,
        'isOffDay' => '1',
        'idCalendarDefinition' => $idCalendarDefinition 
    );
    $aBankHolidays = array ();
    $lstCal = $cal->getSqlElementsFromCriteria ( $crit );
    foreach ( $lstCal as $obj ) {
      $aBankHolidays [] = $obj->day;
    }
    $bankHolidays [$year . '#' . $idCalendarDefinition] = $aBankHolidays;
  }
  $arrayDefaultOffDays=array();
  if (Parameter::getGlobalParameter('OpenDayMonday')=='offDays') $arrayDefaultOffDays[]=1;
  if (Parameter::getGlobalParameter('OpenDayTuesday')=='offDays') $arrayDefaultOffDays[]=2;
  if (Parameter::getGlobalParameter('OpenDayWednesday')=='offDays') $arrayDefaultOffDays[]=3;
  if (Parameter::getGlobalParameter('OpenDayThursday')=='offDays') $arrayDefaultOffDays[]=4;
  if (Parameter::getGlobalParameter('OpenDayFriday')=='offDays') $arrayDefaultOffDays[]=5;
  if (Parameter::getGlobalParameter('OpenDaySaturday')=='offDays') $arrayDefaultOffDays[]=6;
  if (Parameter::getGlobalParameter('OpenDaySunday')=='offDays') $arrayDefaultOffDays[]=0;
  if (in_array ( date ( 'w', $iDate ), $arrayDefaultOffDays)) {
    if (in_array ( date ( 'Ymd', $iDate ), $aBankWorkdays )) {
      return true;
    } else {
      return false;
    }
  } else {
    if (in_array ( date ( 'Ymd', $iDate ), $aBankHolidays )) {
      return false;
    } else {
      return true;
    }
  }
}

function getEaster($iYear = null) {
  if (is_null ( $iYear )) {
    $iYear = ( int ) date ( 'Y' );
  }
  $iN = $iYear - 1900;
  $iA = $iN % 19;
  $iB = floor ( ((7 * $iA) + 1) / 19 );
  $iC = ((11 * $iA) - $iB + 4) % 29;
  $iD = floor ( $iN / 4 );
  $iE = ($iN - $iC + $iD + 31) % 7;
  $iResult = 25 - $iC - $iE;
  if ($iResult > 0) {
    $iEaster = strtotime ( $iYear . '/04/' . $iResult );
  } else {
    $iEaster = strtotime ( $iYear . '/03/' . (31 + $iResult) );
  }
  return $iEaster;
}

function numberOfDaysOfMonth($dateValue) {
  return date ( 't', strtotime ( $dateValue ) );
}

function getBooleanValue($val) {
  if ($val === true) {
    return true;
  }
  if ($val === false) {
    return false;
  }
  if ($val == 'true') {
    return true;
  }
  if ($val == 'false') {
    return false;
  }
  return false;
}

function getBooleanValueAsString($val) {
  if (getBooleanValue ( $val )) {
    return 'true';
  } else {
    return 'false';
  }
}

function getArrayMonth($max, $addPoint = true) {
  $monthArr = array (
      i18n ( "January" ),
      i18n ( "February" ),
      i18n ( "March" ),
      i18n ( "April" ),
      i18n ( "May" ),
      i18n ( "June" ),
      i18n ( "July" ),
      i18n ( "August" ),
      i18n ( "September" ),
      i18n ( "October" ),
      i18n ( "November" ),
      i18n ( "December" ) 
  );
  if ($max) {
    foreach ( $monthArr as $num => $month ) {
      if (mb_strlen ( $month, 'UTF-8' ) > $max) {
        if ($addPoint) {
          $monthArr [$num] = mb_substr ( $month, 0, $max - 1, 'UTF-8' ) . '.';
        } else {
          $monthArr [$num] = mb_substr ( $month, 0, $max, 'UTF-8' );
        }
      }
    }
  }
  return $monthArr;
}

function getAppRoot() {
  $appRoot = "";
  $page = $_SERVER ['PHP_SELF'];
  if (strpos ( $page, '/', 1 )) {
    $appRoot = substr ( $page, 0, strpos ( $page, '/', 1 ) );
  }
  if ($appRoot == '/view' or $appRoot == '/tool' or $appRoot == '/report' or $appRoot == '/plugin') {
    $appRoot = '/';
  }
  return $appRoot;
}

function getPrintInNewWindow($mode = 'print') {
  $printInNewWindow = ($mode == 'pdf') ? true : false;
  if (array_key_exists ( $mode . 'InNewWindow', $_SESSION )) {
    if ($_SESSION [$mode . 'InNewWindow'] == 'YES') {
      $printInNewWindow = true;
    } else if ($_SESSION [$mode . 'InNewWindow'] == 'NO') {
      $printInNewWindow = false;
    }
  }
  return $printInNewWindow;
}

function checkVersion() {
  global $version, $website;
  $user = getSessionUser();
  $profile = new Profile ( $user->idProfile );
  if ($profile->profileCode != 'ADM') {
    return;
  }
  $getYesNo = Parameter::getGlobalParameter ( 'getVersion' );
  if ($getYesNo == 'NO') {
    return;
  }
  $checkUrl = 'http://projeqtor.org/admin/getVersion.php';
  $currentVersion = null;
  if (ini_get ( 'allow_url_fopen' )) {
    enableCatchErrors ();
    $currentVersion = file_get_contents ( $checkUrl );
    disableCatchErrors ();
  }
  if (! $currentVersion) {
    traceLog ( 'Cannot check Version at ' . $checkUrl );
    traceLog ( 'Maybe allow_url_fopen is Off in php.ini...' );
  }
  if (! $currentVersion) {
    return;
  }
  $crit = array (
      'title' => $currentVersion,
      'idUser' => $user->id 
  );
  $alert = new Alert ();
  $lst = $alert->getSqlElementsFromCriteria ( $crit, false );
  if (count ( $lst ) > 0) {
    return;
  }
  $current = explode ( ".", substr ( $currentVersion, 1 ) );
  $check = explode ( ".", substr ( $version, 1 ) );
  $newVersion = "";
  for($i = 0; $i < 3; $i ++) {
    // echo "'$check[$i]' - '$current[$i]'\n";
    if ($check [$i] < $current [$i]) {
      $newVersion = $currentVersion;
      break;
    }
    if ($check [$i] > $current [$i]) {
      traceLog ( "current version $version is higher than latest released $currentVersion" );
      break;
    }
  }
  if ($newVersion) {
    $alert = new Alert ();
    $alert->title = $currentVersion;
    $alert->message = i18n ( 'newVersion', array (
        $newVersion 
    ) ) . '<br/><a href="' . $website . '" target="#">' . $website . '</a>';
    $alert->alertDateTime = date ( "Y-m-d H:i:s" );
    $alert->alertInitialDateTime = $alert->alertDateTime;
    $alert->idUser = $user->id;
    $alert->alertType = 'INFO';
    $alert->save ();
  }
}

function wbsProjectSort($p1, $p2) {
  if ($p1->ProjectPlanningElement->wbsSortable < $p1->ProjectPlanningElement->wbsSortable) {
    return - 1;
  } else {
    return 1;
  }
}

function formatColor($type, $val) {
  $obj = new $type ( $val );
  $color = $obj->color;
  $foreColor = '#000000';
  if (strlen ( $color ) == 7) {
    $red = substr ( $color, 1, 2 );
    $green = substr ( $color, 3, 2 );
    $blue = substr ( $color, 5, 2 );
    $light = (0.3) * base_convert ( $red, 16, 10 ) + (0.6) * base_convert ( $green, 16, 10 ) + (0.1) * base_convert ( $blue, 16, 10 );
    if ($light < 128) {
      $foreColor = '#FFFFFF';
    }
  }
  
  $result = '<div align="center" style="text-align:center;  background:' . $color . ';color:' . $foreColor . ';">' . SqlList::getNameFromId ( $type, $val ) . '</div>';
  return $result;
}

function getPrintTitle() {
  $result = i18n ( "applicationTitle" );
  if (isset ( $_REQUEST ['objectClass'] ) and isset ( $_REQUEST ['page'] ))
  {
	$objectClass=$_REQUEST['objectClass'];
	Security::checkValidClass($objectClass, 'objectClass');

    if ($_REQUEST ['page'] == 'objectDetail.php') {
      $result .= ' - ' . i18n ( $objectClass ) . ' #' . ($_REQUEST ['objectId'] + 0);
    } else if ($_REQUEST ['page'] == '../tool/jsonQuery.php') {
      $result .= ' - ' . i18n ( 'menu' . $objectClass );
    }
  }
  return $result;
}

$startMicroTime = null;

function traceExecutionTime($step = '', $reset = false) {
  global $startMicroTime;
  if ($reset) {
    $startMicroTime = microtime ( true );
    return;
  }
  debugTraceLog( round ( (microtime ( true ) - $startMicroTime) * 1000 ) / 1000 . (($step) ? " s for step " . $step : '') );
  $startMicroTime = microtime ( true );
}

function isHtml5() {
  if (isset($_REQUEST['isIE'])) {
    $isIE=$_REQUEST['isIE'];
    if ($isIE and $isIE<=9) {
      return false;
    } else {
      return true;
    }
  }
  $browser = Audit::getBrowser ();
  if ($browser ['browser'] == 'Internet Explorer') {
    if ($browser ['version'] < '10') {
      return false;
    }
  }
  return true;
}

function isIE() {
  $browser = Audit::getBrowser ();
  if ($browser ['browser'] == 'Internet Explorer') {
    if ($browser ['version']) {
      return $browser ['version'];
    } else {
      return true;
    }
  }
  return false;
}

function formatBrowserDateToDate($dateTime) {
  global $browserLocaleDateFormat;
  if (substr ( $dateTime, 4, 1 ) == '-' and substr ( $dateTime, 7, 1 ) == '-') {
    return $dateTime;
  }
  if (substr_count ( $dateTime, ':' ) > 0 and substr_count ( $dateTime, ' ' ) > 0) {
    list ( $date, $time ) = explode ( ' ', $dateTime );
  } else {
    $date = $dateTime;
    $time = "";
  }
  if ($browserLocaleDateFormat == 'DD/MM/YYYY' and substr_count ( $date, '/' ) == 2 ) {
    list ( $day, $month, $year ) = explode ( '/', $date );
  } else if ($browserLocaleDateFormat == 'MM/DD/YYYY' and substr_count ( $date, '/' ) == 2 ) {
    list ( $month, $day, $year ) = explode ( '/', $date );
  } else {
    return $dateTime;
  }
  if (trim ( $time )) {
    if (substr_count ( $time, ':' ) == 2) {
      list ( $hour, $minute, $second ) = explode ( ':', $time );
    } else {
      list ( $hour, $minute ) = explode ( ':', $time );
      $second = 0;
    }
    return date ( 'Y-m-d H:i:s', mktime ( $hour, $minute, $second, $month, $day, $year ) );
  } else {
    return date ( 'Y-m-d', mktime ( 0, 0, 0, $month, $day, $year ) );
  }
}

function securityCheckRequest() {
  // parameters to check for non html
  $parameters = array (
      'objectClass',
      'objectId',
      'directAccess',
      'page',
      'directAccessPage' 
  );
  $pages = array (
      'page',
      'directAccessPage' 
  );
  foreach ( $parameters as $param ) {
    if (isset ( $_REQUEST [$param] )) {
      $paramVal = $_REQUEST [$param];
      if (in_array ( $param, $pages )) {
        securityCheckPage ( $paramVal );
        $pos = strpos ( $paramVal, '?' );
        if ($pos) {
          $paramVal = substr ( $paramVal, 0, $pos );
        }
      }
      if (trim ( $paramVal ) and htmlEntities ( $paramVal ) != $paramVal) {
        traceHack ( "projeqtor->securityCheckRequest, _REQUEST['$param']=$paramVal" );
        exit ();
      }
    }
  }
}

function projeqtor_set_time_limit($timeout) {
  if (ini_get ( 'safe_mode' )) {
    traceLog ( "WARNING : try to extend time limit to $timeout seconds forbidden by safe_mode. This may lead to unsuccessfull operation." );
  } else {
    $max = ini_get ( 'max_execution_time' );
    if ($max != 0 && ($timeout > $max or $timeout == 0)) { // Don't bother if unlimited or request max
      @set_time_limit ( $timeout );
    } else {
      @set_time_limit ( $max ); // Set time limit to max to reset current execution time to zero
    }
  }
}

function projeqtor_set_memory_limit($memory) {
  @ini_set ( 'memory_limit', $memory );
}

// Functions to set and retrieve data from SESSION : do not use direct $_SESSION
function setSessionValue($code, $value, $global=false) {
  global $paramDbName, $paramDbPrefix;
  if ($global) {
    $projeqtorSession = 'ProjeQtOr';
  } else {
    $projeqtorSession = 'ProjeQtOr_' . $paramDbName . (($paramDbPrefix) ? '_' . $paramDbPrefix : '');
  }
  if (! isset ( $_SESSION [$projeqtorSession] )) {
    $_SESSION [$projeqtorSession] = array ();
  }
  $_SESSION [$projeqtorSession] [$code] = $value;
}
function unsetSessionValue($code, $global=false) {
  global $paramDbName, $paramDbPrefix;
  if ($global) {
    $projeqtorSession = 'ProjeQtOr';
  } else {
    $projeqtorSession = 'ProjeQtOr_' . $paramDbName . (($paramDbPrefix) ? '_' . $paramDbPrefix : '');
  }
  if (isset ( $_SESSION [$projeqtorSession] [$code] )) {
    unset($_SESSION [$projeqtorSession] [$code]);
  }
}
function getSessionValue($code, $default = null, $global=false) {
  // Global parameter is forced when "whatever the databse" is required
  // it is mostly used to cases "also when database is not set yet" ;) 
  global $paramDbName, $paramDbPrefix;
  if ($global) {
    $projeqtorSession = 'ProjeQtOr';
  } else {
    $projeqtorSession = 'ProjeQtOr_' . $paramDbName . (($paramDbPrefix) ? '_' . $paramDbPrefix : '');
  }
  if (! isset ( $_SESSION [$projeqtorSession] )) {
    return $default;
  }
  if (! isset ( $_SESSION [$projeqtorSession] [$code] )) {
    return $default;
  }
  return $_SESSION [$projeqtorSession] [$code];
}
// Functions to get and set current user value from session
function getSessionUser() {
  $user=getSessionValue('user');
  if ($user===null) {
    return new User();
  } else {
    $user->_isRetreivedFromSession=true;
    return $user;  
  }
}
function setSessionUser($user) {
  if ($user and is_object($user)) {
    setSessionValue('user',$user);
  } else {
    unsetSessionValue('user');
  }
}
function sessionUserExists() {
  $user=getSessionValue('user');
  if ($user===null) {
    return false;
  } else {
    return true;
  }
}

function formatNumericOutput($val) {
  global $browserLocale;
  $fmt = new NumberFormatter52 ( $browserLocale, NumberFormatter52::DECIMAL );
  return $fmt->formatDecimalPoint ( $val );
}

function formatNumericInput($val) {
  global $browserLocale;
  $fmt = new NumberFormatter52 ( $browserLocale, NumberFormatter52::DECIMAL );
  if ($fmt->thouthandSeparator=='.' and substr_count($val,$fmt->decimalSeparator)!=1) { 
    // Thouthand separator is "." but locale decimal is not present : 
    // as we are dealing with decimals we expect it is generic format,
    // if not it will raise an error (expected behavior)
    $from = array ( ' ' );
    $to =   array ( ''  );
  } else {
    $from = array ( $fmt->thouthandSeparator, $fmt->decimalSeparator, ' ' ); // Take care to replace thouthand first
    $to   = array ( ''                      , '.'                   , ''  );
  }
  return str_replace ( $from, $to, $val );
}

function getLastOperationStatus($result) {
  if (!$result) return 'OK';
  $search = 'id="lastOperationStatus" value="';
  if (!stripos ( $result, $search )) {
    $search = 'id="lastPlanStatus" value="';
  }
  $start = stripos ( $result, $search ) + strlen ( $search );
  $end = stripos ( $result, '"', $start );
  $status = substr ( $result, $start, $end - $start );
  switch ($status) {
    case "OK" :
    case "INVALID" :
    case "ERROR" :
    case "NO_CHANGE" :
    case "INCOMPLETE" :
    case "WARNING" : 
    case "CONFIRM" : 
      break; // OK, valid status
    default :
      errorLog ( "'$status' is not an expected status in result \n$result" );
  }
  return $status;
}
function getLastOperationMessage($result) {
  return substr($result,0,strpos($result,'<input type="hidden" id="lastSaveId" value="'));
}

function displayLastOperationStatus($result) {
  $status = getLastOperationStatus ( $result );
  if ($status == "OK" or $status=="NO_CHANGE" or $status=="INCOMPLETE") {
    Sql::commitTransaction ();
  } else {
    Sql::rollbackTransaction ();
  }
  echo '<div class="message' . $status . '" >' . $result . '</div>';
  return $status;
}

function calculateFractionFromTime($time,$subtractMidDay=true) {
  $paramHoursPerDay=Parameter::getGlobalParameter('dayTime');
  $paramStartAm=Parameter::getGlobalParameter('startAM');
  $paramEndAm=Parameter::getGlobalParameter('endAM');
  $paramStartPm=Parameter::getGlobalParameter('startPM');
  $paramEndPm=Parameter::getGlobalParameter('endPM');
  $minutesPerDay=60*$paramHoursPerDay;
  if (! $minutesPerDay) return 0;
  $minutesTime = round(strtotime("1970-01-01 $time UTC")/60,0);
  $minutesStartAM=round(strtotime("1970-01-01 $paramStartAm UTC")/60,0);
  $minutesEndAM=round(strtotime("1970-01-01 $paramEndAm UTC")/60,0);
  $minutesStartPM=round(strtotime("1970-01-01 $paramStartPm UTC")/60,0);
  $minutes=$minutesTime-$minutesStartAM;
  if ($subtractMidDay and $minutesTime>$minutesStartPM) {
    $minutes-=$minutesStartPM-$minutesEndAM;
  }
  return round($minutes/$minutesPerDay,2);
}
function calculateFractionBeetweenTimes($startTime,$endTime) {
  $start=calculateFractionFromTime($startTime,false);
  $end=calculateFractionFromTime($endTime,false);
  return($end-$start);
}

function is_session_started() {
  if ( version_compare(phpversion(), '5.4.0', '>=') ) {
    return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
  } else {
    return session_id() === '' ? FALSE : TRUE;
  }
  return FALSE;
}
 
function getEditorType() {
  $editor=Parameter::getUserParameter('editor');
  if ($editor) {
    return $editor;
  } else {
    return "CK";
  }
}
function encodeCSV($val) {
  $csvExportUTF8=Parameter::getGlobalParameter('csvExportUTF8');
  //ini_set('mbstring.substitute_character', "none");
  //$val= mb_convert_encoding($val, 'UTF-8', 'UTF-8'); // This removes invalid UTF8 characters.
  if ($csvExportUTF8=='YES') {
    return $val;
  } else {
    return iconv("UTF-8", 'CP1252//TRANSLIT//IGNORE',$val);
  }
  // Was previous format, encoding to ISO-8859-1 : not including some characters (Euro)
  return utf8_decode($val);
}
function decodeCSV($val) {
  $csvExportUTF8=Parameter::getGlobalParameter('csvExportUTF8');
  //ini_set('mbstring.substitute_character', "none");
  //$val= mb_convert_encoding($val, 'UTF-8', 'UTF-8'); // This removes invalid UTF8 characters.
  if ($csvExportUTF8=='YES') {
    return $val;
  } else {
    return iconv('CP1252//TRANSLIT//IGNORE',"UTF-8",$val);
  }
  // Was previous format, encoding to ISO-8859-1 : not including some characters (Euro)
  return utf8_encode($val);
}
//

function autoOpenFilteringSelect() {
  return ' onMouseDown="dijit.byId(this.name).toggleDropDown();"  selectOnClick="true"';
}

function debugPrintTraceStack() {
  $stack=debug_backtrace();
  foreach ($stack as $stackLine) {
    $file=isset($stackLine['file'])?$stackLine['file']:'';
    $line=isset($stackLine['line'])?$stackLine['line']:'';
    $func=isset($stackLine['function'])?$stackLine['function']:'';
    $clas=isset($stackLine['class'])?$stackLine['class']:'';
    debugTraceLog(" =>"
        .(($file)?" $file":"")
        .(($line)?" at line $line":"")
        .(($func)?" called from $func":"")
        .(($clas)?" for class $clas":"")
    );
  }
}

function formatIcon ($class, $size, $title=null, $withHighlight=false) {
  //if ($size=="22") $size==24;
  $result='';
  if ($withHighlight) {
    if ($size==32) {
      $result.='<div style="position:absolute;left:0px;width:43px;top:0px;height:32px;" class="iconHighlight">&nbsp;</div>';
    } else if ($size==16) { // Tested only for $size=16
      $result.='<div style="position:absolute;left:3px;width:18px;top:3px;height:17px;z-index:20;opacity:0.7;alpha(opacity=70)" class="iconHighlight">&nbsp;</div>';
    }
  }
  $position=($withHighlight)?'position:absolute;'.(($size=='32')?'top:0;left:5px;':''):'';
  $result.="<div class='icon$class$size' style='z-index:500;width:".$size."px;height:".$size."px;$position;' title='$title'>&nbsp;</div>"; 
  return $result;
}
function formatSmallButton($class) {
  $size="16";
  $result='';
  $result.="<span class='roundedButtonSmall' style='top:0px;display:inline-block;width:".$size."px;height:".$size."px;'><div class='iconButton$class$size' style='' >&nbsp;</div></span>";
  return $result;
}
function formatBigButton($class) {
  $size="32";
  $result='';
  $result.="<span class='roundedButtonSmall' style='top:0px;display:inline-block;width:".$size."px;height:".$size."px;'><div class='iconButton$class$size' style='' >&nbsp;</div></span>";
  return $result;
}
?>
