<?php 
/* ============================================================================
 * Parameter is a global kind of object for parametring.
 * It may be on user level, on project level or on global level.
 */ 
require_once('_securityCheck.php');
class Parameter extends SqlElement {

  // extends SqlElement, so has $id
  public $id;    // redefine $id to specify its visiblez place 
  public $idUser;
  public $idProject;
  public $parameterCode;
  public $parameterValue;
  
  public $_noHistory=true; // Will never save history for this object
  
  private static $planningColumnOrder=array();
  private static $planningColumnOrderAll=array();
  private static $planningColumnDescription=array();
  
  /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
  }

  
  /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********

  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    //$colScript = parent::getValidationScript($colName);   
    $colScript="";
    if ($colName=="theme") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value!="random") changeTheme(this.value);';
      $colScript .= '</script>';
    } else if ($colName=="lang") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  changeLocale(this.value);';
      $colScript .= '</script>';
    } else if ($colName=="browserLocaleDateFormat") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  changeBrowserLocaleForDates(this.value);';
      $colScript .= '</script>';
    } else if ($colName=="paramConfirmQuit") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  paramConfirmQuit=this.value;';
      $colScript .= '</script>';
    } else if ($colName=="paramTopIconSize" or $colName=="paramIconSize") {
    	$colScript .= '<script type="dojo/connect" event="onChange" >';
    	$colScript .= '  newValue=this.value;';
    	$colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue,';
      $colScript .= '     load: function(data,args) { showWait(); noDisconnect=true; quitConfirmed=true;';
      $colScript .= '     dojo.byId("directAccessPage").value="parameter.php";';
      $colScript .= '     dojo.byId("menuActualStatus").value=menuActualStatus;';
      $colScript .= '     dojo.byId("p1name").value="type";';
      $colScript .= '     dojo.byId("p1value").value="userParameter";';
      $colScript .= '     dojo.byId("directAccessForm").submit();';
      //$colScript .= '     window.location=("../view/main.php?directAccessPage=parameter.php&menuActualStatus=" + menuActualStatus + "&p1name=type&p1value=userParameter");';
      $colScript .= '     }  });';
    	$colScript .= '</script>';
    } else if ($colName=="defaultProject") {
      //$colScript .= 'dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=defaultProject&value=" + this.value;});';
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';             
    } else if ($colName=="hideMenu") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value=="NO") {';
      $colScript .= '    if (menuActualStatus!="visible") {hideShowMenu()}';
      $colScript .= '    menuHidden=false;';
      $colScript .= '  } else {';
      $colScript .= '    menuHidden=true;';
      $colScript .= '    menuShowMode=this.value; hideShowMenu()';
      $colScript .= '  }';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';
    } else if ($colName=="switchedMode") {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.value=="NO") {';
      $colScript .= '    switchedMode=false;';
      $colScript .= '    dijit.byId("buttonSwitchMode").set("label",i18n("buttonSwitchedMode"));';
      $colScript .= '  } else {';
      $colScript .= '    switchedMode=true;';
      $colScript .= '    switchListMode=this.value;';
      $colScript .= '    dijit.byId("buttonSwitchMode").set("label",i18n("buttonStandardMode"));';
      $colScript .= '  }';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';    
    } else  if ($colName=="printInNewWindow"){
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  if (newValue=="YES") {'; 
      $colScript .= '    printInNewWindow=true;';
      $colScript .= '  } else {';
      $colScript .= '    printInNewWindow=false;';
      $colScript .= '  }';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';
    } else  if ($colName=="pdfInNewWindow"){
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  if (newValue=="YES") {'; 
      $colScript .= '    pdfInNewWindow=true;';
      $colScript .= '  } else {';
      $colScript .= '    pdfInNewWindow=false;';
      $colScript .= '  }';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';
    } else if ($colName=='versionNameAutoformat') {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  newValue=this.value;';
      $colScript .= '  var separator=dijit.byId("versionNameAutoformatSeparator");';
      $colScript .= '  if (newValue=="YES") {';
      $colScript .= '    if (! separator.get("value")) separator.set("value"," V");';
      $colScript .= '  } else {';
      $colScript .= '    separator.set("value",null);';
      $colScript .= '  }';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';
    } else {
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      //if (! $this->idUser and ! $this->idProject) {
      //  $colScript .= '  formChanged();';
      //}
      $colScript .= '  newValue=this.value;';
      $colScript .= '  dojo.xhrPost({url: "../tool/saveDataToSession.php?idData=' . $colName . '&value=" + newValue});';
      $colScript .= '</script>';
    }
    return $colScript;
  }
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /** ===========================================================================
   * Give the list of allows values for a parameter (to builmd a select)
   * @param $parameter the name of the parameter
   * @return array of allowed values as code=>value
   */
  static function getList($parameter) {
    global $isAttachmentEnabled;
    $list=array();
    switch ($parameter) {
      case 'theme': case 'defaultTheme':
        $list=array('ProjeQtOrFlatBlue'=>i18n('themeProjeQtOrFlatBlue'),
                    'ProjeQtOrFlatRed'=>i18n('themeProjeQtOrFlatRed'),
                    'ProjeQtOrFlatGreen'=>i18n('themeProjeQtOrFlatGreen'),
                    'ProjeQtOrFlatGrey'=>i18n('themeProjeQtOrFlatGrey'),
                    'ProjeQtOr'=>i18n('themeProjeQtOr'),
                    'ProjeQtOrFire'=>i18n('themeProjeQtOrFire'),
                    'ProjeQtOrForest'=>i18n('themeProjeQtOrForest'),
                    'ProjeQtOrEarth'=>i18n('themeProjeQtOrEarth'),
                    'ProjeQtOrWater'=>i18n('themeProjeQtOrWater'),
                    'ProjeQtOrWine'=>i18n('themeProjeQtOrWine'),
                    'ProjeQtOrDark'=>i18n('themeProjeQtOrDark'),
                    'ProjeQtOrLight'=>i18n('themeProjeQtOrLight'),
                    'Projectom'=>i18n('themeProjectom'),
                    'ProjectomLight'=>i18n('themeProjectomLight'),
                    'blueLight'=>i18n('themeBlueLight'), 
                    'blue'=>i18n('themeBlue'), 
                    'blueContrast'=>i18n('themeBlueContrast'),
                    'redLight'=>i18n('themeRedLight'),
                    'red'=>i18n('themeRed'),
                    'redContrast'=>i18n('themeRedContrast'),
                    'greenLight'=>i18n('themeGreenLight'),
                    'green'=>i18n('themeGreen'),
                    'greenContrast'=>i18n('themeGreenContrast'),
                    'orangeLight'=>i18n('themeOrangeLight'),
                    'orange'=>i18n('themeOrange'),
                    'orangeContrast'=>i18n('themeOrangeContrast'),
                    'greyLight'=>i18n('themeGreyLight'),
                    'grey'=>i18n('themeGrey'),
                    'greyContrast'=>i18n('themeGreyContrast'),
                    'white'=>i18n('themeWhite'),
                    'ProjectOrRia'=>i18n('themeProjectOrRIA'),
                    'ProjectOrRiaContrasted'=>i18n('themeProjectOrRIAContrasted'),
                    'ProjectOrRiaLight'=>i18n('themeProjectOrRIALight'),
                    'random'=>i18n('themeRandom')); // keep 'random' as last value to assure it is not selected via getTheme()
        break;
      case 'lang':case 'paramDefaultLocale':
        $list=array('en'=>i18n('langEn'), 
                    'fr'=>i18n('langFr'), 
                    'fr-ca'=>i18n('langFrCa'),
                    'de'=>i18n('langDe'),
                    'es'=>i18n('langEs'),
                    'pt'=>i18n('langPt'),
                    'pt-br'=>i18n('langPtBr'),
                    'ru'=>i18n('langRu'),
                    'zh'=>i18n('langZh'),
                    'nl'=>i18n('langNl'),
                    'fa'=>i18n('langFa'),
                    'ja'=>i18n('langJa'),
                    'el'=>i18n('langEl'),
                    'ua'=>i18n('langUa'),
                    'hr'=>i18n('langHr'));
        $list=self::getLangList();
        //sort($list);  // not a good idea : would push brazialian as defaut (first) language...   
        break;
      //gautier ticket timezone
      case 'paramDefaultTimezone':
        $list=self::getTimezoneList();
        break;
      case 'browserLocaleDateFormat':
      	$list=array('DD/MM/YYYY'=>'DD/MM/YYYY',
      			'MM/DD/YYYY'=>'MM/DD/YYYY',
      			'YYYY-MM-DD'=>'YYYY-MM-DD');
      	break;
      case 'defaultProject':
        if (sessionUserExists()) {
          $user=getSessionUser();
          $listVisible=$user->getVisibleProjects();
        } else {
          $listVisible=SqlList::getList('Project');
        }        
        $list['*']=i18n('allProjects');
        foreach ($listVisible as $key=>$val) {
          $list[$key]=$val;
        }
        break;
      case 'displayHistory':
        $list=array('NO'=>i18n('displayNo'),
                    'YES'=>i18n('displayYes'),
                    'YESW'=>i18n('displayYesWithWork'),
                    'REQ'=>i18n('displayOnRequest') );
        //if () {unset($list['YESW']);}
        break;
      case 'displayChecklist':
        $list=array('YES'=>i18n('displayYes'),
            'REQ'=>i18n('displayOnRequest'));
        break;
      case 'printHistory': case 'csvExportUTF8': case 'allowTypeRestrictionOnProject' :
      case 'versionNameAutoformat' :
        $list=array('NO'=>i18n('displayNo'),
            'YES'=>i18n('displayYes')); 
        break;
      case 'pdfInNewWindow': case "paramConfirmQuit": case "paramShowThumb" : case "paramShowThumbList":
      case 'dependencyStrictMode' : 
      	$list=array('YES'=>i18n('displayYes'),
      	            'NO'=>i18n('displayNo'));
      	break;
      case 'displayNote':
        $list=array('YES_OPENED'=>i18n('displayYesOpened'),
                    'YES_CLOSED'=>i18n('displayYesClosed'));
        break;
      case 'displayAttachment':
        if ($isAttachmentEnabled) {
          $list=array('YES_OPENED'=>i18n('displayYesOpened'),
                      'YES_CLOSED'=>i18n('displayYesClosed'));
        } else {
          $list=array('NO'=>i18n('displayNo'));          
        }
        break;
      /* case 'refreshUpdates':
        $list=array('YES'=>i18n('refreshUpdatesYes'),
                    'NO'=>i18n('refreshUpdatesNo'));
        break; */
      case 'hideMenu':
        $list=array('NO'=>i18n('displayNo'),
                    'AUTO'=>i18n('displayYesShowOnMouse'),
                    'CLICK'=>i18n('displayYesShowOnClick'));
        break;
      case 'switchedMode':
        $list=array('NO'=>i18n('displayNo'),
                    'AUTO'=>i18n('displayYesShowOnMouse'),
                    'CLICK'=>i18n('displayYesShowOnClick'));
        break;
      case 'printInNewWindow':
        $list=array('NO'=>i18n('displayNo'),
                    'YES'=>i18n('displayYes'));
        break;
  /////KEVIN////      
      case 'OpenDayMonday':
        $list=array('openDays'=>i18n('openDays'),
                    'offDays'=>i18n('offDays'));
        break;
        
       case 'OpenDayTuesday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
         
       case 'OpenDayWednesday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
         
       case 'OpenDayThursday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
         
       case 'OpenDayFriday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
         
       case 'OpenDaySaturday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
         
       case 'OpenDaySunday':
         $list=array('openDays'=>i18n('openDays'),
                     'offDays'=>i18n('offDays'));
         break;
                 
      case 'imputationUnit':
      	$list=array('days'=>i18n('days'),
      	            'hours'=>i18n('hours'));
      	break;
      case 'workUnit':
        $list=array('days'=>i18n('days'),
                    'hours'=>i18n('hours'));
        break;
      case 'getVersion':
      	$list=array('YES'=>i18n('displayYes'),
                    'NO'=>i18n('displayNo'));
      	break;
      case 'paramLdap_allow_login':case 'paramFadeLoadingMode';
        $list=array('false'=>i18n('displayNo'),
                    'true'=>i18n('displayYes'));
        break;
      case 'paramLdap_version':
        $list=array('2'=>'2',
                    '3'=>'3');
        break;
      case 'ldapDefaultProfile': case 'defaultProfile':
      	$list=SqlList::getList('Profile');
      	break;
      case 'ldapMsgOnUserCreation': case 'imputationAlertSendToResource': 
      case 'imputationAlertSendToProjectLeader': case 'imputationAlertSendToTeamManager':
      case 'imputationAlertInputByOther':
        $list=array('NO'=>i18n('displayNo'),
                    'ALERT'=>i18n('displayAlert'),
                    'MAIL'=>i18n('displayMail'),
                    'ALERT&MAIL'=>i18n('displayAlertAndMail'));
        break;
      case 'csvSeparator':
        $list=array(';'=>';',','=>',');
        break;
      case 'displayResourcePlan':
        $list=array('name'=>i18n('colName'),
                    'initials'=>i18n('colInitials'),
                    'NO'=>i18n('displayNo'));
        break;  
      case 'cronImportLogDestination':
        $list=array('file'=>i18n('cronLogAsFile'),
                    'mail'=>i18n('cronLogAsMail'),
                    'mail+log'=>i18n('cronLogAsMailWithFile'));
        break; 
      case 'currencyPosition':
        $list=array('before'=>i18n('before'), 
                    'after'=>i18n('after'));
        break; 
      case 'paramIconSize': case 'paramTopIconSize':
        $list=array('16'=>i18n('iconSizeSmall'), 
                    '22'=>i18n('iconSizeMedium'), 
                    '32'=>i18n('iconSizeBig'));
        break; 
      case 'paramMailEol':
      	 $list=array('CRLF'=>i18n('eolDefault'), 
                     'LF'=>i18n('eolPostfix'));
        break; 
      case 'logLevel':
         $list=array('0'=>i18n('debugLevel0'),
                     '1'=>i18n('debugLevel1'), 
                     '2'=>i18n('debugLevel2'),
                     '3'=>i18n('debugLevel3'),
                     '4'=>i18n('debugLevel4'));
        break; 
      case 'projectIndentChar':
         $list=array('_'=>'_','__'=>'__','___'=>'___',
                     '-'=>'-','--'=>'--','---'=>'---', 
                     '>'=>'>','>>'=>'>>','>>>'=>'>>>',
                     '|'=>'|', '|_'=>'|_','|__'=>'|__',
                     'no'=>i18n('paramNone'));
        break;
      case 'startPage':
      	$list=array();
      	if (securityCheckDisplayMenu(null,'Today')) {$list['today.php']=i18n('menuToday');}
      	if (securityCheckDisplayMenu(null,'DashboardTicket')) {$list['dashboardTicketMain.php']=i18n('menuDashboardTicket');}
      	if (securityCheckDisplayMenu(null,'Diary')) {$list['diaryMain.php']=i18n('menuDiary');}
      	if (securityCheckDisplayMenu(null,'Imputation')) {$list['imputationMain.php']=i18n('menuImputation');}
      	if (securityCheckDisplayMenu(null,'Planning')) {$list['planningMain.php']=i18n('menuPlanning');}
      	if (securityCheckDisplayMenu(null,'PortfolioPlanning')) {$list['portfolioPlanningMain.php']=i18n('menuPortfolioPlanning');}
      	if (securityCheckDisplayMenu(null,'ResourcePlanning')) {$list['resourcePlanningMain.php']=i18n('menuResourcePlanning');}
      	$arrayItem=array('Project','Document','Ticket','TicketSimple','Activity','Action');
      	foreach  ($arrayItem as $item) {
      		if (securityCheckDisplayMenu(null,$item)) {$list['objectMain.php?objectClass='.$item]=i18n('menu'.$item);}
      	}
      	$list['welcome.php']=i18n('paramNone');
      	$prf=new Profile(getSessionUser()->idProfile);
      	if ($prf->profileCode=='ADM') {
      	  $list['startGuide.php']=i18n('startGuideTitle');
      	}
      	break; 
      case 'changeReferenceOnTypeChange': case 'rememberMe':
        	$list=array('YES'=>i18n('displayYes'),
        	'NO'=>i18n('displayNo'));
        	break;
      case 'initializePassword': case 'setResponsibleIfNeeded': case 'setResponsibleIfSingle': 
      case 'realWorkOnlyForResponsible': case 'preserveUploadedFileName': case 'ganttPlanningPrintOldStyle':
      case 'displayOnlyHandled': case 'setHandledOnRealWork': case 'setDoneOnNoLeftWork':
        $list=array('NO'=>i18n('displayNo'),
                    'YES'=>i18n('displayYes'));
        break;
      case 'consolidateValidated' :
      	$list=array('NO'=>i18n('consolidateNever'),
      	             'ALWAYS'=>i18n('consolidateAlways'),
      	            'IFSET'=>i18n('consolidateIfSet'));
      	break;
      case 'editor' :
        $list=array('CK'=>i18n('CKEditor'),
                    'Dojo'=>i18n('DojoEditor'),
                    'DojoInline'=>i18n('DojoEditorInline'),
                    'text'=>i18n('plainTextEditor'));
        break;
      case 'maxColumns':
        $list=array('3'=>'3','2'=>'2','1'=>'1');
        break;
      case 'fontForPDF':
        $list=array('freesans'=>i18n('fontForPdfFreesans'),
            'helvetica'=>i18n('fontForPdfHelvetica'));
        break;
      case 'ldapCreationAction';
        $list=array('createNothing'=>i18n('createNothingFromLdapUser'),
          'createResource'=>i18n('createResourceFromLdapUser'),
          'createContact'=>i18n('createContactFromLdapUser'),
          'createResourceAndContact'=>i18n('createResourceAndContactFromLdapUser'));
        break;
      case 'responsibleFromProduct':
        $list=array('always'=>i18n('always'),
                    'ifempty'=>i18n('ifEmpty'),
                    'never'=>i18n('never'));
        break;
      case 'showTendersOnVersions':
        $list=array('NO'=>i18n('never'),
          '1#Product#'=>i18n("menuProduct"),
          '2#Product#ProductVersion#'=>i18n("menuProduct").', '.i18n("menuVersion"),
          '3#Product#Component#'=>i18n("menuProduct").', '.i18n("menuComponent"),
          '4#Product#Component#ProductVersion#ComponentVersion#'=>i18n("menuProduct").', '.i18n("menuComponent").', '.i18n("menuVersion")) ;
        break;
      case 'imputationAlertGenerationDay': 
        $list=array(
          'NEVER'=>i18n('never'),
          1=>i18n("Monday"), 
          2=>i18n('Tuesday'),
          3=>i18n('Wednesday'),
          4=>i18n('Thursday'),
          5=>i18n('Friday'),
          6=>i18n('Saturday'),
          7=>i18n('Sunday'),
          '*'=>i18n('periodicityDaily')
        );
        break;
      case 'imputationAlertControlDay' :
        $list=array(
            'current'=>i18n('imputationControlCurrentDay'),
            'previous'=>i18n('imputationControlPreviousDay'),
            'next'=>i18n('imputationControlNextDay')
        );
        break;
    } 
    return $list;
  }
  
  static function getParamtersList($typeParameter) {
    $parameterList=array();
    switch ($typeParameter) {
      case ('userParameter'):
        $parameterList=array(
                         'sectionDisplayParameter'=>"section",
                           "theme"=>"list", 
                           "lang"=>"list",
                           "browserLocaleDateFormat"=>"list",
                           'paramIconSize'=>'list',
                           "paramShowThumb"=>"list",
                           "paramShowThumbList"=>"list",
                           //'paramTopIconSize'=>'list',
                           //'sectionObjectDetail'=>'section', 
                           //"displayAttachment"=>"list",
                           //"displayNote"=>"list",
                         'sectionIHM'=>'section',
                           "displayHistory"=>"list",
                           "displayChecklist"=>"list",  
                           "hideMenu"=>"list",
                           "switchedMode"=>"list",
                           "paramConfirmQuit"=>"list",
                           "startPage"=>"list",
                           "editor"=>'list',
                           "maxColumns"=>'list',
                         'sectionPrintExport'=>'section',
                           'printHistory'=>'list',  
                           "printInNewWindow"=>"list",
                           "pdfInNewWindow"=>"list",
                         'newColumn'=>'newColumn',
                         'sectionMiscellaneous'=>'section',      
                           "defaultProject"=>"list",
                           'projectIndentChar'=>'list',
                           'markAlertsAsRead'=>'specific',
                         'sectionPhoto'=>'section',
                           'image'=>'photo'
        );
        $lockPassword=Parameter::getGlobalParameter('lockPassword');
        if (! getBooleanValue($lockPassword) and !getSessionUser()->isLdap) {
          $parameterList['sectionPassword']='section';
          $parameterList['password']='specific';
        }
        
        break;
      case ('globalParameter'):
      	$parameterList=array('sectionDailyHours'=>"section",
      	                       'startAM'=>'time',
      	                       'endAM'=>'time',
      	                       'startPM'=>'time',
      	                       'endPM'=>'time',
      	                     'sectionOpenDays'=>'section',
      	                       'OpenDayMonday'=>'list',
      	                       'OpenDayTuesday'=>'list',
      	                       'OpenDayWednesday'=>'list',
      	                       'OpenDayThursday'=>'list',
      	                       'OpenDayFriday'=>'list',
      	                       'OpenDaySaturday'=>'list',
      	                       'OpenDaySunday'=>'list',     	                     
      	                     'sectionWorkUnit'=>'section',      	                     
      	                       'imputationUnit'=>'list',
      	                       'workUnit'=>'list',
      	                       'dayTime'=>'number',       	                      
      	                     'sectionImputation'=>'section',
      	                       'displayOnlyHandled'=>'list',
      	                       'setHandledOnRealWork'=>'list',
      	                       'setDoneOnNoLeftWork'=>'list',
      	                       'maxDaysToBookWork'=>'number',
      	                       'maxDaysToBookWorkBlocking'=>'number',
      	                       'imputationAlertInputByOther'=>'list',
      	                     'sectionImputationAlert'=>'section',
      	                       'imputationAlertGenerationDay'=>'list',
      	                       'imputationAlertGenerationHour'=>'time',
      	                       'imputationAlertControlDay'=>'list',
      	                       'imputationAlertControlNumberOfDays'=>'number',
      	                       'imputationAlertSendToResource'=>'list',
      	                       'imputationAlertSendToProjectLeader'=>'list',
      	                       'imputationAlertSendToTeamManager'=>'list',
      	                     'sectionPlanning'=>'section',
                               'displayResourcePlan'=>'list',
      	                       'maxProjectsToDisplay'=>'number',
      	                       'ganttPlanningPrintOldStyle'=>'list',
      	                       'consolidateValidated'=>'list',
      	                       'dependencyStrictMode'=>'list',                     
      	                     'sectionResponsible'=>'section',
      	                       'setResponsibleIfSingle'=>'list',
      	                       'setResponsibleIfNeeded'=>'list',  
      	                       'realWorkOnlyForResponsible'=>'list',
      	                       'responsibleFromProduct'=>'list',
      	                     'sectionUserAndPassword'=>'section',
      	                       'defaultProfile'=>'list', 
      	                     //'sectionPassword'=>'section',
      	                       'paramDefaultPassword'=>'text',
      	                       'paramPasswordMinLength'=>'number', 
      	                       'paramLockAfterWrongTries'=>'number',
      	                       'passwordValidityDays'=>'number',
      	                       'rememberMe'=>'list',
      	                       'initializePassword'=>'list',
      	                     'sectionLdap'=>'section', 
      	                       'paramLdap_allow_login'=>'list',
											         'paramLdap_base_dn'=>'text',
											         'paramLdap_host'=>'text',
											         'paramLdap_port'=>'text',
											         'paramLdap_version'=>'list',
											         'paramLdap_search_user'=>'text',
											         'paramLdap_search_pass'=>'password',
											         'paramLdap_user_filter'=>'text',
      	                       'ldapDefaultProfile'=>'list',
      	                       'ldapMsgOnUserCreation'=>'list',
      	                       'ldapCreationAction'=>'list',
      	                     'sectionReferenceFormat'=>'section',
      	                       'referenceFormatPrefix'=>'text',
      	                       'referenceFormatNumber'=>'number',
                               'changeReferenceOnTypeChange'=>'list',
      	                       'documentReferenceFormat'=>'text',
      	                       'versionReferenceSuffix'=>'text',
      	                       'preserveUploadedFileName'=>'list',
      	                       'billReferenceFormat'=>'text',
      	                       'billNumSize'=>'number',
      	                     'sectionVersionNameFormat'=>'section',
      	                       'versionNameAutoformat'=>'list',
      	                       'versionNameAutoformatSeparator'=>'text',
      	                   'newColumn'=>'newColumn',
      	                     'sectionLocalization'=>'section',
      	                       'paramDefaultLocale'=>'list',
      	                       'paramDefaultTimezone'=>'list',
      	                       'currency'=>'text',
      	                       'currencyPosition'=>'list',
      	                       'filenameCharset'=>'text',
      	                     'sectionMiscellaneous'=>'section',
      	                       'getVersion'=>'list',
      	                       'csvSeparator'=>'list',
      	                       'csvExportUTF8'=>'list',
      	                       'paramMemoryLimitForPDF'=>'number',
      	                       'fontForPDF'=>'list',
      	                       "editor"=>'list',
      	                       'allowTypeRestrictionOnProject'=>'list',
      	                       'showTendersOnVersions'=>'list',
      	                   //'newColumn'=>'newColumn',
      	                     'sectionDisplay'=>'section',
      	                       'paramDbDisplayName'=>'text',  
      	                       'paramFadeLoadingMode'=>'list',
      	                       'paramIconSize'=>'list',
      	                       //'paramTopIconSize'=>'list',
      	                       'defaultTheme'=>'list',
      	                       'startPage'=>'list',
      	                       'maxItemsInTodayLists'=>'number',
      	                     'sectionFiles'=>'section',
      	                       'paramAttachmentDirectory'=>'text',
      	                       'paramAttachmentMaxSize'=>'longnumber',
      	                       'paramReportTempDirectory'=>'text',
      	                     'sectionDocument'=>'section',
      	                       'documentRoot'=>'text',
      	                       'draftSeparator'=>'text',
      	                    /* 'sectionBilling'=>'section',
      	                       'billPrefix'=>'text',
      	                       'billSuffix'=>'text',
      	                       'billNumSize'=>'number',
      	                       'billNumStart'=>'number', */
      	                     'sectionCron'=>'section',
                               'cronDirectory'=>'text',
                               'cronSleepTime'=>'number',                            
                               'cronCheckDates'=>'number',  
                               'alertCheckTime'=>'number',
                               'cronCheckImport'=>'number',
                               'cronImportDirectory'=>'text',
                               'cronImportLogDestination'=>'list',
                               'cronImportMailList'=>'text',
                               'cronCheckEmails'=>'number',
                               'cronCheckEmailsHost'=>'text',
                               'cronCheckEmailsUser'=>'text',
                               'cronCheckEmailsPassword'=>'password',
                             'sectionMail'=>'section',
      	                       'paramAdminMail'=>'text',
      	                       'paramMailSender'=>'text',
                               'paramMailReplyTo'=>'text',
      	                       'paramMailReplyToName'=>'text',
                               'paramMailSmtpServer'=>'text',
                               'paramMailSmtpPort'=>'number',
												       'paramMailSmtpUsername'=>'text',
												       'paramMailSmtpPassword'=>'password',
      	                       'paramMailEol'=>'list',
                               'paramMailSendmailPath'=> 'text',
                             'connectionSslToBd'=>'section',
                               'SslKey'=>'text',
                               'SslCert'=>'text',
                               'SslCa'=>'text',
      	                   'newColumnFull'=>'newColumnFull',
      	                     'sectionMailTitle'=>'section',  
                               'paramMailTitleNew'=>'longtext',
      	                       'paramMailTitleAnyChange'=>'longtext',
      	                       'paramMailTitleStatus'=>'longtext',
      	                       'paramMailTitleResponsible'=>'longtext',
      	                       'paramMailTitleDescription'=>'longtext',
      	                       'paramMailTitleResult'=>'longtext',
      	                       'paramMailTitleNote'=>'longtext',
      	                       'paramMailTitleNoteChange'=>'longtext',
      	                       'paramMailTitleAssignment'=>'longtext',
                               'paramMailTitleAssignmentChange'=>'longtext',
      	                       'paramMailTitleAttachment'=>'longtext',
      	      	               'paramMailTitleDirect'=>'longtext',
      	                       'paramMailTitleUser'=>'longtext',
      	                       'paramMailBodyUser'=>'longtext',
      	                       'paramMailTitleApprover'=>'longtext',
      	                       'paramMailBodyApprover'=>'longtext'
      	                       //'messageAlertImputationResource'=>'longtext',
      	                       //'messageAlertImputationProjectLeader'=>'longtext'
      	                     
      	);
    }
    global $hosted;
    if (isset($hosted) and $hosted) {
    	if ($typeParameter=='globalParameter') {
    	  unset($parameterList['documentRoot']);
    	  unset($parameterList['paramMailSender']);
    	  unset($parameterList['paramMailReplyTo']);
    	  unset($parameterList['paramMailSmtpServer']);
    	  unset($parameterList['paramMailSmtpPort']);
    	  unset($parameterList['paramMailSmtpPort']);
    	  unset($parameterList['paramMailSendmailPath']);
    	  unset($parameterList['cronImportDirectory']);
    	  unset($parameterList['paramMemoryLimitForPDF']);
    	  unset($parameterList['sectionFiles']);
    	  unset($parameterList['paramAttachmentDirectory']);
    	  unset($parameterList['paramAttachmentMaxSize']);    	  
    	  unset($parameterList['paramReportTempDirectory']);
    	  unset($parameterList['paramMailEol']);
    	  unset($parameterList['cronDirectory']);
    	}
    }
    $user=getSessionUser();
    $showChecklistAll=false;
    foreach ($user->getAllProfiles() as $prf) {
      $showChecklist=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile'=>$prf,'scope'=>'checklist'));
      if ($showChecklist and $showChecklist->id and $showChecklist->rightAccess=='1') {
        $showChecklistAll=true;
      }
    }
    if (! $showChecklistAll) {
      unset($parameterList['displayChecklist']);
    }
    return $parameterList;
  }
  
  static public function getGlobalParameter($code) {
  	global $$code;
  	if (isset($$code)) {
  	  if ($code=='paramDbPrefix') {
  	    $$code=strtolower($$code);
  	  }
  		return $$code;
  	}
  	if ($code=='paramDbHost' or $code=='paramDbPort' or $code=='paramDbType' or $code=='paramDbPrefix'
  	 or $code=='paramDbName' or $code=='paramDbUser' or $code=='paramDbPassword') {
  		return '';
  	}
  	if ($code=='paramPathSeparator') {
  		return DIRECTORY_SEPARATOR;
  	}
    if ($code=='mailEol') {
    	$nl=Parameter::getGlobalParameter('paramMailEol');
      if (isset($nl) and $nl) {
      	if ($nl=='LF') {
      		$nl="\n";
      	} else if ($nl=='CRLF') {
      		$nl="\r\n";
      	} else {
      		//$nl=$nl; 
      	}
      } else {
      	$nl="\r\n";
      }
      return $nl;
    }
  	if (!isset($_SESSION['globalParamatersArray'])) {
      $_SESSION['globalParamatersArray']=array();
      $p=new Parameter();
      $crit=" (idUser is null and idProject is null)";
      $lst=$p->getSqlElementsFromCriteria(null, false, $crit);
      foreach ($lst as $param) {
        $_SESSION['globalParamatersArray'][$param->parameterCode]=$param->parameterValue;
      }
  	}
  	if (isset($_SESSION['globalParamatersArray'][$code])) {
  		return $_SESSION['globalParamatersArray'][$code];
  	} else {
      	return '';
    }
  }

  static public function getUserParameter($code) {
  	if (!array_key_exists('userParamatersArray',$_SESSION)) {
      $_SESSION['userParamatersArray']=array();
    }
    if (array_key_exists($code,$_SESSION['userParamatersArray'])) {
      return $_SESSION['userParamatersArray'][$code];
    } 
    $p=new Parameter();
    $user=getSessionUser();
    if ($user->id) {
      $crit=" idUser =" . Sql::fmtId($user->id) . " and idProject is null and parameterCode='" . $code . "'";
    } else {
      $crit=" idUser is null and idProject is null and parameterCode='" . $code . "'";
    }  
    $lst=$p->getSqlElementsFromCriteria(null, false, $crit);
    $val='';
    if (count($lst)==1) {
      $val=$lst[0]->parameterValue;
    } else if ($user->id) {
      $val=self::getGlobalParameter($code);
    }
    if ($user->id) {
      $_SESSION['userParamatersArray'][$code]=$val;
    }
    return $val;
  }
  
  static function storeUserParameter($code,$value,$userId=null) {
    if (! $userId) {
  	  $userId=getSessionUser()->id;
    }
  	$param=SqlElement::getSingleSqlElementFromCriteria('Parameter', array('idUser'=>$userId,'parameterCode'=>$code));
  	if (! $param->id) {
  		$param->parameterCode=$code;
  		$param->idUser=$userId;
  		$param->idProject=null;
  	}
    $param->parameterValue=$value;
  	$param->save();
  	if (!array_key_exists('userParamatersArray',$_SESSION)) {
  		$_SESSION['userParamatersArray']=array();
  	}
  	$_SESSION['userParamatersArray'][$code]=$value;
  }
  static function storeGlobalParameter($code,$value) {
    $param=SqlElement::getSingleSqlElementFromCriteria('Parameter', array('idUser'=>null,'parameterCode'=>$code));
    if (! $param->id) {
    		$param->parameterCode=$code;
    		$param->idUser=null;
    		$param->idProject=null;
    }
    $param->parameterValue=$value;
    $param->save();
    if (!array_key_exists('globalParamatersArray',$_SESSION)) {
      $_SESSION['globalParamatersArray']=array();
    }
    $_SESSION['globalParamatersArray'][$code]=$value;
  }
  
  static public function getPlanningColumnOrder($all=false) {
    if (! count(self::$planningColumnOrderAll)) self::getPlanningColumnDescription();
  	if ($all) {
  		return self::$planningColumnOrderAll;
  	} else {
  		return self::$planningColumnOrder;
  	}
  }
  static public function getPlanningColumnDescription() {
    if (count(self::$planningColumnDescription)) return self::$planningColumnDescription;
    $arrayFields=array(
        'Name'=>300,
        'StartDate'=>80,
        'EndDate'=>80,
        'Progress'=>50,
        'ValidatedWork'=>70,
        'AssignedWork'=>70,
        'RealWork'=>70,
        'LeftWork'=>70,
        'PlannedWork'=>70,
        'Duration'=>60,
        'ValidatedCost'=>70, 
        'AssignedCost'=>70, 
        'RealCost'=>70, 
        'LeftCost'=>70, 
        'PlannedCost'=>70,
        'IdStatus'=>70, 
        'Type'=>120,
        'Resource'=>90,
        'Priority'=>50,
        'IdPlanningMode'=>150
        );
    $cpt=0;
    foreach($arrayFields as $col=>$width) {
      $cpt++;
      self::$planningColumnDescription[$col]=array('name'=>$col,'show'=>1,'order'=>$cpt,'width'=>$width);
    }
  	$pe=new ProjectPlanningElement();
    $pe->setVisibility();
    $workVisibility=$pe->_workVisibility;
    $costVisibility=$pe->_costVisibility;    
  	$res=array();
  	$resAll=array();
  	// Default Values
  	$user=getSessionUser();
  	$critHidden="idUser=" . $user->id . " and idProject is null and parameterCode like 'planningHideColumn%'";
  	$critOrder="idUser=" . $user->id . " and idProject is null and parameterCode  like 'planningColumnOrder%'";
  	$critWidth="idUser=" . $user->id . " and idProject is null and parameterCode  like 'planningColumnWidth%'";
  	$param=new Parameter();
  	$hiddenList=$param->getSqlElementsFromCriteria(null, false, $critHidden);
  	$orderList=$param->getSqlElementsFromCriteria(null, false, $critOrder);
  	$widthList=$param->getSqlElementsFromCriteria(null, false, $critWidth);
  	$hidden="|";
  	foreach($hiddenList as $param) {
  		if ($param->parameterValue=='1') {
  		  $hidden.=substr($param->parameterCode,18).'|';
  		  self::$planningColumnDescription[substr($param->parameterCode,18)]['show']=0;
  		}
  	}
  	if ($workVisibility!='ALL') {
  		if ($workVisibility!='VAL') {
  			$hidden.='ValidatedWork|';
  			self::$planningColumnDescription['ValidatedWork']['show']=0;
  		}
  		$hidden.='AssignedWork|RealWork|LeftWork|PlannedWork|';
  		self::$planningColumnDescription['AssignedWork']['show']=0;
  		self::$planningColumnDescription['RealWork']['show']=0;
  		self::$planningColumnDescription['LeftWork']['show']=0;
  		self::$planningColumnDescription['PlannedWork']['show']=0;
  	}
  	if ($costVisibility!='ALL') {
  	  if ($costVisibility!='VAL') {
  	    $hidden.='ValidatedCost|';
  	    self::$planningColumnDescription['ValidatedCost']['show']=0;
  	  }
  	  $hidden.='AssignedCost|RealCost|LeftCost|PlannedCost|';
  	  self::$planningColumnDescription['AssignedCost']['show']=0;
  	  self::$planningColumnDescription['RealCost']['show']=0;
  	  self::$planningColumnDescription['LeftCost']['show']=0;
  	  self::$planningColumnDescription['PlannedCost']['show']=0;
  	}
  	$arrayFieldsSorted=array();
  	$arrayFieldsSorted[0]='Name';
  	foreach ($orderList as $param) {
  	  $arrayFieldsSorted[intval($param->parameterValue)+1]=substr($param->parameterCode,19);	
  	  self::$planningColumnDescription[substr($param->parameterCode,19)]['order']=intval($param->parameterValue);
  	}
  	foreach ($widthList as $param) {
  	  self::$planningColumnDescription[substr($param->parameterCode,19)]['width']=intval($param->parameterValue);
  	}
  	ksort($arrayFieldsSorted);
  	foreach($arrayFields as $column=>$width) {
  	  if (! in_array($column,$arrayFieldsSorted)) {
  	  	$arrayFieldsSorted[]=$column;
  	  }
  	}
  	$i=1;  	
  	foreach($arrayFieldsSorted as $order=>$column) {
  	  $res[$i]=(!strpos($hidden,$column)>0)?$column:'Hidden'.$column;
  	  $resAll[$i]=$column;
  		$i++;
  	}
    self::$planningColumnOrderAll=$resAll;
    self::$planningColumnOrder=$res;
    return self::$planningColumnDescription;
  }
  
  /** 
   * Regenerate pamareter.php file depending on new param location : 
   *  if param exists in database : do not write param to file
   *  else : write param to file 
   */
  static public function regenerateParamFile($echoResult=false) {
  	global $parametersLocation, $currVersion;
  	// Security : copy file (except for first installation
  	if (!isset($currVersion) or $currVersion!='V0.0.0') {
  	  copy($parametersLocation, $parametersLocation.'.'.date('YmdHis'));
  	}
  	$fileHandler = fopen($parametersLocation,"r");
    if (!$fileHandler) {
    	throwError("Error opening file $parameterLocation");
    	return;
    }
    $cptLine=0;
    $cptVar=0;
    $cptVarDb=0;
    $cptVarFile=0;
    $var="";
    $arrayParams=array();
    while (!feof($fileHandler)) {
      $line = fgets($fileHandler);
      $cptLine++;
      if (substr($line,0,2)!='//' and strpos(strtolower($line),'<?php')===false) { // exclude comments
        $var.=$line;
        $posSemi=strrpos($var,';');
        if ($posSemi>0) {
        	$command=trim(substr($var,0,$posSemi));
        	$posEq=strpos($command,'=');
	        if ($posEq>0) {
	        	$paramCode=trim(substr($command,0,$posEq));
	        	$paramValue=trim(substr($command,$posEq+1));	          

	          $arrayParam[$paramCode]=$paramValue;
	          $cptVar+=1;
	        }
	        $var="";
        }
      }       
    }
    fclose($fileHandler);  
    $nl="\n";
    traceLog("=== REWRITE PARAMTERS.PHP FILE = START ====================");    
    $fileHandler = fopen($parametersLocation,"w");
    fwrite($fileHandler,'<?php'.$nl); 
    fwrite($fileHandler,'// ======================================================================================='.$nl);
    fwrite($fileHandler,'// Automatically generated parameter file'.$nl);
    fwrite($fileHandler,'// on '.date('Y-m-d H:i:s').$nl);
    fwrite($fileHandler,'// ======================================================================================='.$nl);
    if ($echoResult) echo "<table style=\"border: 1px solid black;\"><tr><th class=\"messageHeader\">Code</th><th class=\"messageHeader\">Value</th><th class=\"messageHeader\">Result</th></tr>";
    foreach ($arrayParam as $paramCode=>$paramValue) {
      $result='';
      $resultHtml='&nbsp;';
      $code=substr($paramCode,1);
      if (self::isGlobalParameterInDB($code)) {
        $result="moved to database";
        $resultHtml="<span style=\"color:red\">$result</span>";   
        $cptVarDb+=1;     
      } else if ($paramCode=='$enforceUTF8' and $paramValue) {
        global $maintenanceDisableEnforceUTF8;
        if (isset($maintenanceDisableEnforceUTF8) and $maintenanceDisableEnforceUTF8) {
          fwrite($fileHandler,$paramCode."='0';".$nl);
          $msg="For compatibility reason, \$enforceUTF8 parameter has been set to '0' in your parameters.php file<br/>";
          $msg.="Check your data through ProjeQtOr : if non ASCCII characters (like accentuated characters) are not displayed correctly, revert to \$enforceUTF8='1';";
          echo "<div class='messageWARNING'><i>" . $msg . "</i></div><br/>"; 
        }
      } else {
      	fwrite($fileHandler,$paramCode.'='.$paramValue.';'.$nl);
      	$result="kept in parameter file";
        $resultHtml="<span style=\"color:green\">$result</span>";
        $cptVarFile+=1;           
      }
      if ($echoResult) echo "<tr><td class=\"messageData\">$code</td><td class=\"messageData\">$paramValue</td><td class=\"messageData\">$resultHtml</td></tr>";
      traceLog("$paramCode $result");
    }
    if ($echoResult) echo "</table>";
    if ($echoResult) echo "<br/>lines read from file = $cptLine<br/>parameters found = $cptVar";
    if ($echoResult) echo "<br/>parameters moved to database = $cptVarDb<br/>parameters kept in parameter file = $cptVarFile";
    traceLog("---> lines read from file = $cptLine");
    traceLog("---> parameters found = $cptVar");
    traceLog("---> parameters moved to database = $cptVarDb");
    traceLog("---> parameters kept in parameter file = $cptVarFile");
    fwrite($fileHandler,'//======= END');
    fclose($fileHandler);
    traceLog("REWRITE PARAMTERS.PHP FILE = END ======================");
  }
  
  static public function isGlobalParameterInDB($code) {
    $p=new Parameter();
    $crit=" idUser is null and idProject is null and parameterCode='" . $code . "'";
    $lst=$p->getSqlElementsFromCriteria(null, false, $crit);
    if (count($lst)==1) {
      return true;
    } else {
    	return false;
    }
  }
  
  static public function clearGlobalParameters() {
  	// This function is call on most of admin functionalities or global parameters update, to force refresh of parameters
  	unset($_SESSION['globalParamatersArray']);
    $aut=new Audit();
    $table=$aut->getDatabaseTableName();
    $sessionId=session_id();
    $query="update $table set requestRefreshParam=1 where idle=0 and sessionid!='$sessionId'";
    Sql::query($query);
  }
  
  static public function refreshParameters() {
scriptLog('refreshParameters()');
  	// This function is call when refresh of parameters is requested
  	unset($_SESSION['globalParamatersArray']);
  }
  
  static public function getLangList() {
    $dir='../tool/i18n/nls';
    $handle = opendir($dir);
    $result=array();
    while ( ($file = readdir($handle)) !== false) {
      
      if ($file == '.' || $file == '..' || $file=='index.php' // exclude ., .. and index.php
      || ! is_dir($dir.'/'.$file) || substr($file,0,1)=='.' ) {        // non directories or directories starting with . (.svn)
        continue;
      }
      $nls=$file;
      $lang=str_replace('-',' ', $file);
      $lang=ucwords($lang);
      $lang=str_replace(' ','', $lang);
      $result[$nls]=i18n('lang'.$lang);
    }
    closedir($handle);
    asort($result);
    return $result;
  } 
  
  // gautier ticket #2290 list of timezone
static public function getTimezoneList() {
  $zones_array = array('Africa/Abidjan'=>'Africa/Abidjan',
'Africa/Accra'=>'Africa/Accra',
'Africa/Addis_Ababa'=>'Africa/Addis_Ababa',
'Africa/Algiers'=>'Africa/Algiers',
'Africa/Asmara'=>'Africa/Asmara',
'Africa/Asmera'=>'Africa/Asmera',
'Africa/Bamako'=>'Africa/Bamako',
'Africa/Bangui'=>'Africa/Bangui',
'Africa/Banjul'=>'Africa/Banjul',
'Africa/Bissau'=>'Africa/Bissau',
'Africa/Blantyre'=>'Africa/Blantyre',
'Africa/Brazzaville'=>'Africa/Brazzaville',
'Africa/Bujumbura'=>'Africa/Bujumbura',
'Africa/Cairo'=>'Africa/Cairo',
'Africa/Casablanca'=>'Africa/Casablanca',
'Africa/Ceuta'=>'Africa/Ceuta',
'Africa/Conakry'=>'Africa/Conakry',
'Africa/Dakar'=>'Africa/Dakar',
'Africa/Dar_es_Salaam'=>'Africa/Dar_es_Salaam',
'Africa/Djibouti'=>'Africa/Djibouti',
'Africa/Douala'=>'Africa/Douala',
'Africa/El_Aaiun'=>'Africa/El_Aaiun',
'Africa/Freetown'=>'Africa/Freetown',
'Africa/Gaborone'=>'Africa/Gaborone',
'Africa/Harare'=>'Africa/Harare',
'Africa/Johannesburg'=>'Africa/Johannesburg',
'Africa/Juba'=>'Africa/Juba',
'Africa/Kampala'=>'Africa/Kampala',
'Africa/Khartoum'=>'Africa/Khartoum',
'Africa/Kigali'=>'Africa/Kigali',
'Africa/Kinshasa'=>'Africa/Kinshasa',
'Africa/Lagos'=>'Africa/Lagos',
'Africa/Libreville'=>'Africa/Libreville',
'Africa/Lome'=>'Africa/Lome',
'Africa/Luanda'=>'Africa/Luanda',
'Africa/Lubumbashi'=>'Africa/Lubumbashi',
'Africa/Lusaka'=>'Africa/Lusaka',
'Africa/Malabo'=>'Africa/Malabo',
'Africa/Maputo'=>'Africa/Maputo',
'Africa/Maseru'=>'Africa/Maseru',
'Africa/Mbabane'=>'Africa/Mbabane',
'Africa/Mogadishu'=>'Africa/Mogadishu',
'Africa/Monrovia'=>'Africa/Monrovia',
'Africa/Nairobi'=>'Africa/Nairobi',
'Africa/Ndjamena'=>'Africa/Ndjamena',
'Africa/Niamey'=>'Africa/Niamey',
'Africa/Nouakchott'=>'Africa/Nouakchott',
'Africa/Ouagadougou'=>'Africa/Ouagadougou',
'Africa/Porto-Novo'=>'Africa/Porto-Novo',
'Africa/Sao_Tome'=>'Africa/Sao_Tome',
'Africa/Timbuktu'=>'Africa/Timbuktu',
'Africa/Tripoli'=>'Africa/Tripoli',
'Africa/Tunis'=>'Africa/Tunis',
'Africa/Windhoek'=>'Africa/Windhoek',
      'America/Adak'=>'America/Adak',
      'America/Anchorage'=>'America/Anchorage',
      'America/Anguilla'=>'America/Anguilla',
      'America/Antigua'=>'America/Antigua',
      'America/Araguaina'=>'America/Araguaina',
      'America/Argentina/Buenos_Aires'=>'America/Argentina/Buenos_Aires',
      'America/Argentina/Catamarca'=>'America/Argentina/Catamarca',
      'America/Argentina/ComodRivadavia'=>'America/Argentina/ComodRivadavia',
      'America/Argentina/Cordoba'=>'America/Argentina/Cordoba',
      'America/Argentina/Jujuy'=>'America/Argentina/Jujuy',
      'America/Argentina/La_Rioja'=>'America/Argentina/La_Rioja',
      'America/Argentina/Mendoza'=>'America/Argentina/Mendoza',
      'America/Argentina/Rio_Gallegos'=>'America/Argentina/Rio_Gallegos',
      'America/Argentina/Salta'=>'America/Argentina/Salta',
      'America/Argentina/San_Juan'=>'America/Argentina/San_Juan',
      'America/Argentina/San_Luis'=>'America/Argentina/San_Luis',
      'America/Argentina/Tucuman'=>'America/Argentina/Tucuman',
      'America/Argentina/Ushuaia'=>'America/Argentina/Ushuaia',
      'America/Aruba'=>'America/Aruba',
      'America/Asuncion'=>'America/Asuncion',
      'America/Atikokan'=>'America/Atikokan',
      'America/Atka'=>'America/Atka',
      'America/Bahia'=>'America/Bahia',
      'America/Bahia_Banderas'=>'America/Bahia_Banderas',
      'America/Barbados'=>'America/Barbados',
      'America/Belem'=>'America/Belem',
      'America/Belize'=>'America/Belize',
      'America/Blanc-Sablon'=>'America/Blanc-Sablon',
      'America/Boa_Vista'=>'America/Boa_Vista',
      'America/Bogota'=>'America/Bogota',
      'America/Boise'=>'America/Boise',
      'America/Buenos_Aires'=>'America/Buenos_Aires',
      'America/Cambridge_Bay'=>'America/Cambridge_Bay',
      'America/Campo_Grande'=>'America/Campo_Grande',
      'America/Cancun'=>'America/Cancun',
      'America/Caracas'=>'America/Caracas',
      'America/Catamarca'=>'America/Catamarca',
      'America/Cayenne'=>'America/Cayenne',
      'America/Cayman'=>'America/Cayman',
      'America/Chicago'=>'America/Chicago',
      'America/Chihuahua'=>'America/Chihuahua',
      'America/Coral_Harbour'=>'America/Coral_Harbour',
      'America/Cordoba'=>'America/Cordoba',
      'America/Costa_Rica'=>'America/Costa_Rica',
      'America/Creston'=>'America/Creston',
      'America/Cuiaba'=>'America/Cuiaba',
      'America/Curacao'=>'America/Curacao',
      'America/Danmarkshavn'=>'America/Danmarkshavn',
      'America/Dawson'=>'America/Dawson',
      'America/Dawson_Creek'=>'America/Dawson_Creek',
      'America/Denver'=>'America/Denver',
      'America/Detroit'=>'America/Detroit',
      'America/Dominica'=>'America/Dominica',
      'America/Edmonton'=>'America/Edmonton',
      'America/Eirunepe'=>'America/Eirunepe',
      'America/El_Salvador'=>'America/El_Salvador',
      'America/Ensenada'=>'America/Ensenada',
      'America/Fort_Wayne'=>'America/Fort_Wayne',
      'America/Fortaleza'=>'America/Fortaleza',
      'America/Glace_Bay'=>'America/Glace_Bay',
      'America/Godthab'=>'America/GodthabAmerica/Goose_Bay',
      'America/Grand_Turk'=>'America/Grand_Turk',
      'America/Grenada'=>'America/Grenada',
      'America/Guadeloupe'=>'America/Guadeloupe',
      'America/Guatemala'=>'America/Guatemala',
      'America/Guayaquil'=>'America/Guayaquil',
      'America/Guyana'=>'America/Guyana',
      'America/Halifax'=>'America/Halifax',
      'America/Havana'=>'America/Havana',
      'America/Hermosillo'=>'America/Hermosillo',
      'America/Indiana/Indianapolis'=>'America/Indiana/Indianapolis',
      'America/Indiana/Knox'=>'America/Indiana/Knox',
      'America/Indiana/Marengo'=>'America/Indiana/Marengo',
      'America/Indiana/Petersburg'=>'America/Indiana/Petersburg',
      'America/Indiana/Tell_City'=>'America/Indiana/Tell_City',
      'America/Indiana/Vevay'=>'America/Indiana/Vevay',
      'America/Indiana/Vincennes'=>'America/Indiana/Vincennes',
      'America/Indiana/Winamac'=>'America/Indiana/Winamac',
      'America/Indianapolis'=>'America/Indianapolis',
      'America/Inuvik'=>'America/Inuvik',
      'America/Iqaluit'=>'America/Iqaluit',
      'America/Jamaica'=>'America/Jamaica',
      'America/Jujuy'=>'America/Jujuy',
      'America/Juneau'=>'America/Juneau',
      'America/Kentucky/Louisville'=>'America/Kentucky/Louisville',
      'America/Kentucky/Monticello'=>'America/Kentucky/Monticello',
      'America/Knox_IN'=>'America/Knox_IN',
      'America/Kralendijk'=>'America/Kralendijk',
      'America/La_Paz'=>'America/La_Paz',
      'America/Lima'=>'America/Lima',
      'America/Los_Angeles'=>'America/Los_Angeles',
      'America/Louisville'=>'America/Louisville',
      'America/Lower_Princes'=>'America/Lower_Princes',
      'America/Maceio'=>'America/Maceio',
      'America/Managua'=>'America/Managua',
      'America/Manaus'=>'America/Manaus',
      'America/Marigot'=>'America/Marigot',
      'America/Martinique'=>'America/Martinique',
      'America/Matamoros'=>'America/Matamoros',
      'America/Mazatlan'=>'America/Mazatlan',
      'America/Mendoza'=>'America/Mendoza',
      'America/Menominee'=>'America/Menominee',
      'America/Merida'=>'America/Merida',
      'America/Metlakatla'=>'America/Metlakatla',
      'America/Mexico_City'=>'America/Mexico_City',
      'America/Miquelon'=>'America/Miquelon',
      'America/Moncton'=>'America/Moncton',
      'America/Monterrey'=>'America/Monterrey',
      'America/Montevideo'=>'America/Montevideo',
      'America/Montreal'=>'America/Montreal',
      'America/Montserrat'=>'America/Montserrat',
      'America/Nassau'=>'America/Nassau',
      'America/New_York'=>'America/New_York',
      'America/Nipigon'=>'America/Nipigon',
      'America/Nome'=>'America/Nome',
      'America/Noronha'=>'America/Noronha',
      'America/North_Dakota/Beulah'=>'America/North_Dakota/Beulah',
      'America/North_Dakota/Center'=>'America/North_Dakota/Center',
      'America/North_Dakota/New_Salem'=>'America/North_Dakota/New_Salem',
      'America/Ojinaga'=>'America/Ojinaga',
      'America/Panama'=>'America/Panama',
      'America/Pangnirtung'=>'America/Pangnirtung',
      'America/Paramaribo'=>'America/Paramaribo',
      'America/Phoenix'=>'America/Phoenix',
      'America/Port-au-Prince'=>'America/Port-au-Prince',
      'America/Port_of_Spain'=>'America/Port_of_Spain',
      'America/Porto_Acre'=>'America/Porto_Acre',
      'America/Porto_Velho'=>'America/Porto_Velho',
      'America/Puerto_Rico'=>'America/Puerto_Rico',
      'America/Rainy_River'=>'America/Rainy_River',
      'America/Rankin_Inlet'=>'America/Rankin_Inlet',
      'America/Recife'=>'America/Recife',
      'America/Regina'=>'America/Regina',
      'America/Resolute'=>'America/Resolute',
      'America/Rio_Branco'=>'America/Rio_Branco',
      'America/Rosario'=>'America/Rosario',
      'America/Santa_Isabel'=>'America/Santa_Isabel',
      'America/Santarem'=>'America/Santarem',
      'America/Santiago'=>'America/Santiago',
      'America/Santo_Domingo'=>'America/Santo_Domingo',
      'America/Sao_Paulo'=>'America/Sao_Paulo',
      'America/Scoresbysund'=>'America/Scoresbysund',
      'America/Shiprock'=>'America/Shiprock',
      'America/Sitka'=>'America/Sitka',
      'America/St_Barthelemy'=>'America/St_Barthelemy',
      'America/St_Johns'=>'America/St_Johns',
      'America/St_Kitts'=>'America/St_Kitts',
      'America/St_Lucia'=>'America/St_Lucia',
      'America/St_Thomas'=>'America/St_Thomas',
      'America/St_Vincent'=>'America/St_Vincent',
      'America/Swift_Current'=>'America/Swift_Current',
      'America/Tegucigalpa'=>'America/Tegucigalpa',
      'America/Thule'=>'America/Thule',
      'America/Thunder_Bay'=>'America/Thunder_Bay',
      'America/Tijuana'=>'America/Tijuana',
      'America/Toronto'=>'America/Toronto',
      'America/Tortola'=>'America/Tortola',
      'America/Vancouver'=>'America/Vancouver',
      'America/Virgin'=>'America/Virgin',
      'America/Whitehorse'=>'America/Whitehorse',
      'America/Winnipeg'=>'America/Winnipeg',
      'America/Yakutat'=>'America/Yakutat',
      'America/Yellowknife'=>'America/Yellowknife',
      'Antarctica/Casey'=>'Antarctica/Casey',
      'Antarctica/Davis'=>'Antarctica/Davis',
      'Antarctica/DumontDUrville'=>'Antarctica/DumontDUrville',
      'Antarctica/Macquarie'=>'Antarctica/Macquarie',
      'Antarctica/Mawson'=>'Antarctica/Mawson',
      'Antarctica/McMurdo'=>'Antarctica/McMurdo',
      'Antarctica/Palmer'=>'Antarctica/Palmer',
      'Antarctica/Rothera'=>'Antarctica/Rothera',
      'Antarctica/South_Pole'=>'Antarctica/South_Pole',
      'Antarctica/Syowa'=>'Antarctica/Syowa',
      'Antarctica/Troll'=>'Antarctica/Troll',
      'Antarctica/Vostok'=>'Antarctica/Vostok',
      'Arctic/Longyearbyen'=>'Arctic/Longyearbyen',
      'Asia/Aden'=>'Asia/Aden',
      'Asia/Almaty'=>'Asia/Almaty',
      'Asia/Amman'=>'Asia/Amman',
      'Asia/Anadyr'=>'Asia/Anadyr',
      'Asia/Aqtau'=>'Asia/Aqtau',
      'Asia/Aqtobe'=>'Asia/Aqtobe',
      'Asia/Ashgabat'=>'Asia/Ashgabat',
      'Asia/Ashkhabad'=>'Asia/Ashkhabad',
      'Asia/Baghdad'=>'Asia/Baghdad',
      'Asia/Bahrain'=>'Asia/Bahrain',
      'Asia/Baku'=>'Asia/Baku',
      'Asia/Bangkok'=>'Asia/Bangkok',
      'Asia/Beirut'=>'Asia/Beirut',
      'Asia/Bishkek'=>'Asia/Bishkek',
      'Asia/Brunei'=>'Asia/Brunei',
      'Asia/Calcutta'=>'Asia/Calcutta',
      'Asia/Chita'=>'Asia/Chita',
      'Asia/Choibalsan'=>'Asia/Choibalsan',
      'Asia/Chongqing'=>'Asia/Chongqing',
      'Asia/Chungking'=>'Asia/Chungking',
      'Asia/Colombo'=>'Asia/Colombo',
      'Asia/Dacca'=>'Asia/Dacca',
      'Asia/Damascus'=>'Asia/Damascus',
      'Asia/Dhaka'=>'Asia/Dhaka',
      'Asia/Dili'=>'Asia/Dili',
      'Asia/Dubai'=>'Asia/Dubai',
      'Asia/Dushanbe'=>'Asia/Dushanbe',
      'Asia/Gaza'=>'Asia/Gaza',
      'Asia/Harbin'=>'Asia/Harbin',
      'Asia/Hebron'=>'Asia/Hebron',
      'Asia/Ho_Chi_Minh'=>'Asia/Ho_Chi_Minh',
      'Asia/Hong_Kong'=>'Asia/Hong_Kong',
      'Asia/Hovd'=>'Asia/Hovd',
      'Asia/Irkutsk'=>'Asia/Irkutsk',
      'Asia/Istanbul'=>'Asia/Istanbul',
      'Asia/Jakarta'=>'Asia/Jakarta',
      'Asia/Jayapura'=>'Asia/Jayapura',
      'Asia/Jerusalem'=>'Asia/Jerusalem',
      'Asia/Kabul'=>'Asia/Kabul',
      'Asia/Kamchatka'=>'Asia/Kamchatka',
      'Asia/Karachi'=>'Asia/Karachi',
      'Asia/Kashgar'=>'Asia/Kashgar',
      'Asia/Kathmandu'=>'Asia/Kathmandu',
      'Asia/Katmandu'=>'Asia/Katmandu',
      'Asia/Khandyga'=>'Asia/Khandyga',
      'Asia/Kolkata'=>'Asia/Kolkata',
      'Asia/Krasnoyarsk'=>'Asia/Krasnoyarsk',
      'Asia/Kuala_Lumpur'=>'Asia/Kuala_Lumpur',
      'Asia/Kuching'=>'Asia/Kuching',
      'Asia/Kuwait'=>'Asia/Kuwait',
      'Asia/Macao'=>'Asia/Macao',
      'Asia/Macau'=>'Asia/Macau',
      'Asia/Magadan'=>'Asia/Magadan',
      'Asia/Makassar'=>'Asia/Makassar',
      'Asia/Manila'=>'Asia/Manila',
      'Asia/Muscat'=>'Asia/Muscat',
      'Asia/Nicosia'=>'Asia/Nicosia',
      'Asia/Novokuznetsk'=>'Asia/Novokuznetsk',
      'Asia/Novosibirsk'=>'Asia/Novosibirsk',
      'Asia/Omsk'=>'Asia/Omsk',
      'Asia/Oral'=>'Asia/Oral',
      'Asia/Phnom_Penh'=>'Asia/Phnom_Penh',
      'Asia/Pontianak'=>'Asia/Pontianak',
      'Asia/Pyongyang'=>'Asia/Pyongyang',
      'Asia/Qatar'=>'Asia/Qatar',
      'Asia/Qyzylorda'=>'Asia/Qyzylorda',
      'Asia/Rangoon'=>'Asia/Rangoon',
      'Asia/Riyadh'=>'Asia/Riyadh',
      'Asia/Saigon'=>'Asia/Saigon',
      'Asia/Sakhalin'=>'Asia/Sakhalin',
      'Asia/Samarkand'=>'Asia/Samarkand',
      'Asia/Seoul'=>'Asia/Seoul',
      'Asia/Shanghai'=>'Asia/Shanghai',
      'Asia/Singapore'=>'Asia/Singapore',
      'Asia/Srednekolymsk'=>'Asia/Srednekolymsk',
      'Asia/Taipei'=>'Asia/Taipei',
      'Asia/Tashkent'=>'Asia/Tashkent',
      'Asia/Tbilisi'=>'Asia/Tbilisi',
      'Asia/Tehran'=>'Asia/Tehran',
      'Asia/Tel_Aviv'=>'Asia/Tel_Aviv',
      'Asia/Thimbu'=>'Asia/Thimbu',
      'Asia/Thimphu'=>'Asia/Thimphu',
      'Asia/Tokyo'=>'Asia/Tokyo',
      'Asia/Ujung_Pandang'=>'Asia/Ujung_Pandang',
      'Asia/Ulaanbaatar'=>'Asia/Ulaanbaatar',
      'Asia/Ulan_Bator'=>'Asia/Ulan_Bator',
      'Asia/Urumqi'=>'Asia/Urumqi',
      'Asia/Ust-Nera'=>'Asia/Ust-Nera',
      'Asia/Vientiane'=>'Asia/Vientiane',
      'Asia/Vladivostok'=>'Asia/Vladivostok',
      'Asia/Yakutsk'=>'Asia/Yakutsk',
      'Asia/Yekaterinburg'=>'Asia/Yekaterinburg',
      'Asia/Yerevan'=>'Asia/Yerevan',
      'Atlantic/Azores'=>'Atlantic/Azores',
      'Atlantic/Bermuda'=>'Atlantic/Bermuda',
      'Atlantic/Canary'=>'Atlantic/Canary',
      'Atlantic/Cape_Verde'=>'Atlantic/Cape_Verde',
      'Atlantic/Faeroe'=>'Atlantic/Faeroe',
      'Atlantic/Faroe'=>'Atlantic/Faroe',
      'Atlantic/Jan_Mayen'=>'Atlantic/Jan_Mayen',
      'Atlantic/Madeira'=>'Atlantic/Madeira',
      'Atlantic/Reykjavik'=>'Atlantic/Reykjavik',
      'Atlantic/South_Georgia'=>'Atlantic/South_Georgia',
      'Atlantic/St_Helena'=>'Atlantic/St_Helena',
      'Atlantic/Stanley'=>'Atlantic/Stanley',
      'Australia/ACT'=>'Australia/ACT',
      'Australia/Adelaide'=>'Australia/Adelaide',
      'Australia/Brisbane'=>'Australia/Brisbane',
      'Australia/Broken_Hill'=>'Australia/Broken_Hill',
      'Australia/Canberra'=>'Australia/Canberra',
      'Australia/Currie'=>'Australia/Currie',
      'Australia/Darwin'=>'Australia/Darwin',
      'Australia/Eucla'=>'Australia/Eucla',
      'Australia/Hobart'=>'Australia/Hobart',
      'Australia/LHI'=>'Australia/LHI',
      'Australia/Lindeman'=>'Australia/Lindeman',
      'Australia/Lord_Howe'=>'Australia/Lord_Howe',
      'Australia/Melbourne'=>'Australia/Melbourne',
      'Australia/North'=>'Australia/North',
      'Australia/NSW'=>'Australia/NSW',
      'Australia/Perth'=>'Australia/Perth',
      'Australia/Queensland'=>'Australia/Queensland',
      'Australia/South'=>'Australia/South',
      'Australia/Sydney'=>'Australia/Sydney',
      'Australia/Tasmania'=>'Australia/Tasmania',
      'Australia/Victoria'=>'Australia/Victoria',
      'Australia/West'=>'Australia/West',
      'Australia/Yancowinna'=>'Australia/Yancowinna',
  'Europe/Amsterdam'=>'Europe/Amsterdam', 
  'Europe/Andorra'=>'Europe/Andorra',
  'Europe/Athens'=>'Europe/Athens',
  'Europe/Belfast'=>'Europe/Belfast',
  'Europe/Belgrade'=>'Europe/Belgrade',
  'Europe/Berlin'=>'Europe/Berlin',
  'Europe/Bratislava'=>'Europe/Bratislava',
  'Europe/Brussels'=>'Europe/Brussels',
  'Europe/Bucharest'=>'Europe/Bucharest',
  'Europe/Budapest'	=>'Europe/Budapest',
  'Europe/Busingen'	=>'Europe/Busingen',
  'Europe/Chisinau'	=>'Europe/Chisinau',
  'Europe/Copenhagen'	=>'Europe/Copenhagen',
  'Europe/Dublin'	 =>'Europe/Dublin',
  'Europe/Gibraltar'=>'Europe/Gibraltar',
  'Europe/Guernsey'	=>	'Europe/Guernsey',
  'Europe/Helsinki'	=>	'Europe/Helsinki',
  'Europe/Isle_of_Man'=> 'Europe/Isle_of_Man',
  'Europe/Istanbul'	=>	'Europe/Istanbul',
  'Europe/Jersey'	=>	'Europe/Jersey',
  'Europe/Kaliningrad'=>	'Europe/Kaliningrad',
  'Europe/Kiev'		=>	'Europe/Kiev',
  'Europe/Lisbon'	=>	'Europe/Lisbon',
  'Europe/Ljubljana'=>	'Europe/Ljubljana',
  'Europe/London'	=>	'Europe/London',
  'Europe/Luxembourg'=>	'Europe/Luxembourg',
  'Europe/Madrid'	=>	'Europe/Madrid',
  'Europe/Malta'	=>	'Europe/Malta',
  'Europe/Mariehamn'=>	'Europe/Mariehamn',
  'Europe/Minsk'	=>	'Europe/Minsk',
  'Europe/Monaco'	=>	'Europe/Monaco',
  'Europe/Moscow'	=>	'Europe/Moscow',
  'Europe/Nicosia'	=>	'Europe/Nicosia',
  'Europe/Oslo'		=>	'Europe/Oslo',
  'Europe/Paris'	=>	'Europe/Paris',
  'Europe/Podgorica'=>	'Europe/Podgorica',
  'Europe/Prague'	=>	'Europe/Prague',
  'Europe/Riga'		=>	'Europe/Riga',
  'Europe/Rome'		=>	'Europe/Rome',
  'Europe/Samara'	=>	'Europe/Samara',
  'Europe/San_Marino'	=>'Europe/San_Marino',
  'Europe/Sarajevo'	=>	'Europe/Sarajevo',
  'Europe/Simferopol'=>	'Europe/Simferopol',
  'Europe/Skopje'	=>	'Europe/Skopje',
  'Europe/Sofia'=>		'Europe/Sofia',
  'Europe/Stockholm'=>	'Europe/Stockholm',
  'Europe/Tallinn'	=>	'Europe/Tallinn',
  'Europe/Tirane'	=>	'Europe/Tirane',
  'Europe/Tiraspol'		=>'Europe/Tiraspol',
  'Europe/Uzhgorod'	=>	'Europe/Uzhgorod',
  'Europe/Vaduz'	=>	'Europe/Vaduz',
  'Europe/Vatican'=>		'Europe/Vatican',
  'Europe/Vienna'=>		'Europe/Vienna',
  'Europe/Vilnius'	=>	'Europe/Vilnius',
  'Europe/Volgograd'	=>'Europe/Volgograd',
  'Europe/Warsaw'	=>	'Europe/Warsaw',
  'Europe/Zagreb'	=>	'Europe/Zagreb',
  'Europe/Zaporozhye'=>'Europe/Zaporozhye',
  'Europe/Zurich'	=>	'Europe/Zurich',
  'Indian/Antananarivo'=>'Indian/Antananarivo',
'Indian/Chagos'=>'Indian/Chagos',
'Indian/Christmas'=>'Indian/Christmas',
'Indian/Cocos'=>'Indian/Cocos',
'Indian/Comoro'=>'Indian/Comoro',
'Indian/Kerguelen'=>'Indian/Kerguelen',
'Indian/Mahe'=>'Indian/Mahe',
'Indian/Maldives'=>'Indian/Maldives',
'Indian/Mauritius'=>'Indian/Mauritius',
'Indian/Mayotte'=>'Indian/Mayotte',
'Indian/Reunion'=>'Indian/Reunion',
'Pacific/Apia'=>'Pacific/Apia',
'Pacific/Auckland'=>'Pacific/Auckland',
'Pacific/Bougainville'=>'Pacific/Bougainville',
'Pacific/Chatham'=>'Pacific/Chatham',
'Pacific/Chuuk'=>'Pacific/Chuuk',
'Pacific/Easter'=>'Pacific/Easter',
'Pacific/Efate'=>'Pacific/Efate',
'Pacific/Enderbury'=>'Pacific/Enderbury',
'Pacific/Fakaofo'=>'Pacific/Fakaofo',
'Pacific/Fiji'=>'Pacific/Fiji',
'Pacific/Funafuti'=>'Pacific/Funafuti',
'Pacific/Galapagos'=>'Pacific/Galapagos',
'Pacific/Gambier'=>'Pacific/Gambier',
'Pacific/Guadalcanal'=>'Pacific/Guadalcanal',
'Pacific/Guam'=>'Pacific/Guam',
'Pacific/Honolulu'=>'Pacific/Honolulu',
'Pacific/Johnston'=>'Pacific/Johnston',
'Pacific/Kiritimati'=>'Pacific/Kiritimati',
'Pacific/Kosrae'=>'Pacific/Kosrae',
'Pacific/Kwajalein'=>'Pacific/Kwajalein',
'Pacific/Majuro'=>'Pacific/Majuro',
'Pacific/Marquesas'=>'Pacific/Marquesas',
'Pacific/Midway'=>'Pacific/Midway',
'Pacific/Nauru'=>'Pacific/Nauru',
'Pacific/Niue'=>'Pacific/Niue',
'Pacific/Norfolk'=>'Pacific/Norfolk',
'Pacific/Noumea'=>'Pacific/Noumea',
'Pacific/Pago_Pago'=>'Pacific/Pago_Pago',
'Pacific/Palau'=>'Pacific/Palau',
'Pacific/Pitcairn'=>'Pacific/Pitcairn',
'Pacific/Pohnpei'=>'Pacific/Pohnpei',
'Pacific/Ponape'=>'Pacific/Ponape',
'Pacific/Port_Moresby'=>'Pacific/Port_Moresby',
'Pacific/Rarotonga'=>'Pacific/Rarotonga',
'Pacific/Saipan'=>'Pacific/Saipan',
'Pacific/Samoa'=>'Pacific/Samoa',
'Pacific/Tahiti'=>'Pacific/Tahiti',
'Pacific/Tarawa'=>'Pacific/Tarawa',
'Pacific/Tongatapu'=>'Pacific/Tongatapu',
'Pacific/Truk'=>'Pacific/Truk',
'Pacific/Wake'=>'Pacific/Wake',
'Pacific/Wallis'=>'Pacific/Wallis',
'Pacific/Yap'=>'Pacific/Yap');
  return $zones_array;
 }
  
 public function control(){
   $result="";
   if ($this->parameterCode=="SslKey" and trim($this->parameterValue)!="" and !file_exists($this->parameterValue)) {
       $result.='<br/>' . i18n('msgNotaFile',array(i18n("paramSslKey"),$this->parameterValue));
   }
   if ($this->parameterCode=="SslCert" and trim($this->parameterValue)!="" and !file_exists($this->parameterValue)) {
       $result.='<br/>' . i18n('msgNotaFile',array(i18n("paramSslCert"),$this->parameterValue));
   }
   if ($this->parameterCode=="SslCa" and trim($this->parameterValue)!="" and !file_exists($this->parameterValue)) {
       $result.='<br/>' . i18n('msgNotaFile',array(i18n("paramSslCa"),$this->parameterValue));
   }
   $defaultControl=parent::control();
   if ($defaultControl!='OK') {
     $result.=$defaultControl;
   }
   if ($result=="") {
     $result='OK';
   }
   return $result;
 }
}
?>