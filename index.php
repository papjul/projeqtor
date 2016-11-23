<?php
$indexPhp=true;
chdir(dirname(__FILE__).'/view');
$theme="ProjeQtOr";
if (is_file ( "../tool/parametersLocation.php" )) {
  include_once '../tool/projeqtor.php';
  $theme=getTheme();
} 
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
 * Default page. Redirects to view directory
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" 
  "http://www.w3.org/TR/html4/strict.dtd">
<html style="margin: 0px; padding: 0px;">
<head>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<?php if (! isset($debugIEcompatibility) or $debugIEcompatibility==false) {?>  
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
<?php }?>  
  <link rel="shortcut icon" href="view/img/logo.ico" type="image/x-icon" />
  <link rel="icon" href="view/img/logo.ico" type="image/x-icon" />
  <link rel="stylesheet" type="text/css" href="view/css/projeqtor.css" />
  <link rel="stylesheet" type="text/css" href="view/css/projeqtorFlat.css" />
  <title>ProjeQtOr</title>
  <script language="javascript">
    function autoRedirect() {
      window.setTimeout("document.getElementById('indexForm').submit()",10);
    }
  </script>
</head>

<body class="tundra <?php echo $theme;?>"  style='background-color: #C3C3EB' onload="autoRedirect();">
  <div id="wait">
  &nbsp;
  </div> 
  <table align="center" width="100%" height="100%" class="loginBackground">
    <tr height="100%">
      <td width="100%" align="center">
        <div class="background loginFrame" >
        <table  align="center" >
          <tr style="height:10px;" >
            <td align="left" style="height: 1%;" valign="top">
		        	<div style="position:relative;width: 400px; height: 54px;">
			          <div style="z-index:10;overflow:visible;position:absolute;width: 480px; height: 280px;top:15px;text-align: center">
				        <img style="max-height:60px" src="<?php 				        
				          if (file_exists("../logo.gif")) echo 'logo.gif';
				          else if (file_exists("../logo.jpg")) echo 'logo.jpg';
				          else if (file_exists("../logo.png")) echo 'logo.png';
				          else echo 'view/img/titleSmall.png';?>" />
			          </div>
			        </div>
            </td>
          </tr>
          <tr style="height:100%" height="100%">
            <td style="height:99%" align="left" valign="middle">
              <div  id="formDiv" dojoType="dijit.layout.ContentPane" region="center" style="width: 470px; height:210px;overflow:hidden">
  <form id="indexForm" name="indexForm" action="view/main.php" method="post">
    <input type="hidden" id="xcurrentLocale" name="xcurrentLocale" value="en" />
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