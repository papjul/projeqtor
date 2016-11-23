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
 * Connnexion page of application.
 */
   require_once "../tool/projeqtor.php";
   if (isset($locked) and $locked) {
     include_once "../view/locked.php";
     exit;
   }
   header ('Content-Type: text/html; charset=UTF-8');
   scriptLog('   ->/view/login.php');
   $_SESSION['application']="PROJEQTOR";
   if (getSessionValue('setup', null, true) or version_compare(ltrim(Sql::getDbVersion(),'V'), '5.0.0',"<") ) {
     $msgList=array();
   } else {
     $msg=new Message();
     $msgList=$msg->getSqlElementsFromCriteria(array('showOnLogin'=>'1', 'idle'=>'0'));
     $msgTypeList=SqlList::getList('MessageType','color');
   }
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" 
  "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="keywork" content="projeqtor, project management" />
  <meta name="author" content="projeqtor" />
  <meta name="Copyright" content="Pascal BERNARD" />
<?php if (! isset($debugIEcompatibility) or $debugIEcompatibility==false) {?>  
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php }?>  
  <title><?php echo (Parameter::getGlobalParameter('paramDbDisplayName'))?Parameter::getGlobalParameter('paramDbDisplayName'):i18n("applicationTitle");?></title>
  <link rel="shortcut icon" href="img/logo.ico" type="image/x-icon" />
  <link rel="icon" href="img/logo.ico" type="image/x-icon" />
  <link rel="stylesheet" type="text/css" href="css/projeqtor.css" />
  <link rel="stylesheet" type="text/css" href="css/projeqtorFlat.css" />
  <script type="text/javascript" src="../external/CryptoJS/rollups/md5.js?version=<?php echo $version.'.'.$build;?>" ></script>
  <script type="text/javascript" src="../external/CryptoJS/rollups/sha256.js?version=<?php echo $version.'.'.$build;?>" ></script>
  <script type="text/javascript" src="../external/phpAES/aes.js?version=<?php echo $version.'.'.$build;?>" ></script>
  <script type="text/javascript" src="js/projeqtor.js?version=<?php echo $version.'.'.$build;?>" ></script>
  <script type="text/javascript" src="js/projeqtorDialog.js?version=<?php echo $version.'.'.$build;?>" ></script>
  <script type="text/javascript" src="../external/dojo/dojo.js?version=<?php echo $version.'.'.$build;?>"
    djConfig='modulePaths: {"i18n":"../../tool/i18n",
                            "i18nCustom":"../../plugin"},
              parseOnLoad: true, 
              isDebug: <?php echo getBooleanValueAsString(Parameter::getGlobalParameter('paramDebugMode'));?>'></script>
  <script type="text/javascript" src="../external/dojo/projeqtorDojo.js?version=<?php echo $version.'.'.$build;?>"></script>
  <?php Plugin::includeAllFiles();?>
  <script type="text/javascript"> 
    var customMessageExists=<?php echo(file_exists(Plugin::getDir()."/nls/$currentLocale/lang.js"))?'true':'false';?>;
    dojo.require("dojo.parser");
    dojo.require("dojo.date");
    dojo.require("dojo.date.locale");
    dojo.require("dojo.number");
    dojo.require("dijit.focus");
    dojo.require("dojo.i18n");
    dojo.require("dijit.Dialog"); 
    dojo.require("dijit.form.ValidationTextBox");
    dojo.require("dijit.form.TextBox");
    dojo.require("dijit.form.CheckBox");
    dojo.require("dijit.form.Button");
    dojo.require("dijit.form.Form");
    dojo.require("dijit.form.FilteringSelect");
    var fadeLoading=<?php echo getBooleanValueAsString(Parameter::getGlobalParameter('paramFadeLoadingMode'));?>;
    var aesLoginHash="<?php echo md5(session_id());?>";
    var browserLocaleDateFormat="";
    var browserLocaleDateFormatJs="";
    var aesKeyLength=<?php echo Parameter::getGlobalParameter('aesKeyLength');?>;
    dojo.addOnLoad(function(){
      currentLocale="<?php echo $currentLocale?>";
      saveResolutionToSession();
      saveBrowserLocaleToSession();
      dijit.Tooltip.defaultPosition=["below","right"];
      dijit.byId('login').focus(); 
      // For IE, focus to login is delayed
      dijit.byId('password').focus(); 
      setTimeout("dijit.byId('login').focus();",10);
      //dijit.byId('login').focus(); 
      var changePassword=false;
      hideWait();
      showMessage(1, <?php echo count($msgList);?>);
      if (dojo.isIE && dojo.isIE<=8) {
        $varsParam=new Array();
        $varsParam[0]=dojo.isIE;
        dojo.byId('loginResultDiv').innerHTML=
          '<input type="hidden" id="isLoginPage" name="isLoginPage" value="true" />'
          +'<div class="messageERROR" style="width:100%">'+i18n("warningIE", $varsParam )+'</div>';
        //dojo.byId('loginResultDiv').style.position="fixed";
        //dojo.byId('loginResultDiv').style.top="0px";
        //dojo.byId('loginResultDiv').style.width="100%";
        var hideMessage=function() {
          dojo.byId('loginResultDiv').innerHTML=
          '<input type="hidden" id="isLoginPage" name="isLoginPage" value="true" />'
        };
        disableWidget('password');
        disableWidget('login');
        disableWidget('loginButton');
        disableWidget('passwordButton');
        disableWidget('passwordButton');
        disableWidget('rememberMe');
      } else if (dojo.isIE && dojo.isIE<=10) {
        $varsParam=new Array();
        $varsParam[0]=dojo.isIE;
        dojo.byId('loginResultDiv').innerHTML=
          '<input type="hidden" id="isLoginPage" name="isLoginPage" value="true" />'
          +'<div class="messageWARNING" style="width:100%">'+i18n("warningIE", $varsParam )+'</div>';
      }
    });

    function showMessage(id, idMax) {
      contentNode=dojo.byId('loginMessage_'+id);
      if (! contentNode) return;
      dojo.fadeIn({ 
		    node: contentNode ,
		    duration: 800, 
		    onEnd: function() {
		      id++;
			    if (id<=idMax) { showMessage(id, idMax);}
				}
  		}).play();
    } 
  </script>
</head>

<body class="<?php echo getTheme();?>" onLoad="hideWait();" style="overflow: auto;" onBeforeUnload="">
<?php if (array_key_exists('objectClass', $_REQUEST) and array_key_exists('objectId', $_REQUEST)  ) {
	Security::checkValidClass($_REQUEST['objectClass']);
echo '<input type="hidden" id="objectClass" value="' . $_REQUEST['objectClass'] . '" />';
echo '<input type="hidden" id="objectId" value="' . htmlEncode($_REQUEST['objectId']) . '" />';
}
?>
  <div id="waitLogin" style="display:none" >
  </div>
  <div class="loginMessageContainer">
  	<?php 
  	$cpt=0;
  	foreach ($msgList as $msg) { 
      $cpt++;?>  
    <div class="loginMessage" id="loginMessage_<?php echo $cpt;?>">
    <div class="loginMessageTitle" style="color:<?php echo $msgTypeList[$msg->idMessageType];?>;"><?php echo htmlEncode($msg->name);?></div>
    <br/><?php echo $msg->description;?>
    </div>
    <?php }?>
  </div>
  <table align="center" width="100%" height="100%" class="loginBackground">
    <tr height="100%">
	    <td width="100%" align="center">
	      <div class="background loginFrame" >
	          <!--  <div style="position:fixed; top:0px; right:0px; height:128px;width:128px;box-shadow:0px 0px 50px #FFFFFF; background: #FFFFFF; border-radius:64px;"> 
	          <img style="position:absolute; top:2px;right:-2px;" src="img/logoMedium.png"  />
	          </div>  -->
			  <table  align="center">
			    <tr style="height:10px;" >
			      <td align="left" style="position:relative;height: 1%;" valign="top">
			        <div style="position:relative;width: 400px; height: 54px;">
			          <div style="z-index:10;overflow:visible;position:absolute;width: 480px; height: 50px;top:15px;text-align: center">
				        <img style="max-height:60px" src="<?php 
				          if (file_exists("../logo.gif")) echo '../logo.gif';
				          else if (file_exists("../logo.jpg")) echo '../logo.jpg';
				          else if (file_exists("../logo.png")) echo '../logo.png';
				          else echo 'img/titleSmall.png';?>" />
			          </div>
			        </div>
			      </td>
			    </tr>
			    <tr style="height:100%" height="100%">
			      <td style="height:99%" align="left" valign="middle">
			        <div  id="formDiv" dojoType="dijit.layout.ContentPane" region="center" style="background:transparent !important;width: 470px; overflow:hidden;position: relative;">
			          <form  dojoType="dijit.form.Form" id="loginForm" jsId="loginForm" name="loginForm" encType="multipart/form-data" action="" method="" >
			            <script type="dojo/method" event="onSubmit" >             
                    connect(false);
    		            return false;        
                  </script>
                  <br/><br/>
			            <table width="100%">
			              <tr>     
			                <td title="<?php echo i18n("login");?>" style="background:transparent !important;">
			                  <div class="inputLoginIcon iconLoginUser">&nbsp;</div>
			                </td>
			                <td title="<?php echo i18n("login");?>">
			                  <input tabindex="1" id="login" type="text"  class="inputLogin"
			                   dojoType="dijit.form.TextBox" />
                        <input type="hidden" id="hashStringLogin" name="login" style="width:200px" value=""/>  
			                </td>
			                <td width="110px">&nbsp;</td>
			              </tr>
			              <tr style="font-size:50%"><td colspan="3">&nbsp;</td></tr>
			              <tr>
			                <td title="<?php echo i18n("password");?>" style="background:transparent !important;">
			                  <div  class="inputLoginIcon iconLoginPassword">&nbsp;</div>
			                </td>  
			                <td title="<?php echo i18n("password");?>">
			                  <input  tabindex="2" id="password" type="password" class="inputLogin"
			                   dojoType="dijit.form.TextBox" />
                        <input type="hidden" id="hashStringPassword" name="password" style="width:200px" value=""/>
			                </td>
			                <td></td>
			              </tr>
			              <?php if (Parameter::getGlobalParameter('rememberMe')!='NO') {?>
			              <tr style="font-size:50%"><td colspan="2">&nbsp;</td></tr>
			              <tr>
			                <td></td>
			                <td><div style="width:200px;text-align:center;"><div class="greyCheck" dojoType="dijit.form.CheckBox" type="checkbox" name="rememberMe"></div> <?php echo i18n('rememberMe');?></div></td>
			                <td></td>
			              </tr>
			              <?php }?>
			              <tr style="font-size:50%"><td colspan="3">&nbsp;</td></tr>
			              <tr>
			                <td class="label" style="background:transparent !important;"><label>&nbsp;</label></td>
			                <td>
			                  <button tabindex="3" type="submit" id="loginButton" class="largeTextButton"
			                   dojoType="dijit.form.Button" showlabel="true">OK
			                    <script type="dojo/connect" event="onClick" args="evt">
                            return true;
                          </script>
			                  </button>
			                </td>
			                <td></td>
			              </tr>
	<?php 
	$showPassword=true;
	$lockPassword=Parameter::getGlobalParameter('lockPassword');
	if (isset($lockPassword)) {
	  if (getBooleanValue($lockPassword)) {
	    $showPassword=false;
	  }
	}
	if ($showPassword) { 
	?>               <tr style="height:5px"><td colspan="3" ></td></tr>
			              <tr>
			                <td class="label" style="background:transparent !important;"><label>&nbsp;</label></td>
			                <td style="width:200px">  
			                  <button tabindex="4" id="passwordButton" class="largeTextButton" type="button" dojoType="dijit.form.Button" showlabel="true">
			                    <?php echo i18n('buttonChangePassword') ?>
			                    <script type="dojo/connect" event="onClick" args="evt">
                            connect(true);
                            return false;
                          </script>
			                  </button>  
			                </td>
			                <td ></td>
			              </tr>
  <?php }?>
			              <tr><td colspan="3">&nbsp;</td></tr>
			              <tr>
			                <td class="label" style="background:transparent !important;"><label>&nbsp;</label></td>
			                <td colspan="3" style="position:fixed;width:100%; height:100%">
			                  <div id="loginResultDiv" dojoType="dijit.layout.ContentPane" region="none" >
			                    <input type="hidden" id="isLoginPage" name="isLoginPage" value="true" />
			                    <?php if (Parameter::getGlobalParameter('applicationStatus')=='Closed'
			                          or Sql::getDbVersion()!=$version) {
			                    	      echo '<div style="position:absolute;float: left;left:-110px;top : -50px;">';
			                    	      echo '<img src="../view/img/closedApplication.gif" width="60px"/>';
			                    	      echo '</div>';
			                    	      echo '<div class="messageERROR" >';
			                    	      if (Parameter::getGlobalParameter('applicationStatus')=='Closed') {
			                    	        echo htmlEncode(Parameter::getGlobalParameter('msgClosedApplication'),'withBR');
			                    	      } else {
			                    	      	echo i18n('wrongMaintenanceUser');
			                    	      }
			                    	      echo '</div>';
			                          } else if (array_key_exists('lostConnection',$_REQUEST)) {
			                            //echo '<div class="messageWARNING">'.i18n("disconnectMessage");
			                            echo '<div class="messageWARNING">';
			                            //echo '<br/>';
			                            echo i18n("errorConnection").'</div>';
			                          } 
			                     ?>
			                  </div>
			                </td>
			              </tr>
			            </table>
			          </form>
		          </div>
		        </td>
		      </tr>
	      </table>
	      </div>
      </td>
    </tr>
  </table>
</body>
</html>