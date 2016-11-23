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
 * Welcom screen (replacing Today if no access right)
 */
  require_once "../tool/projeqtor.php";
?>  
<table style="width:100%;height:100%;">
    <tr style="height:100%; vertical_align: middle;">
      <td style="width:100%;text-align: center;">
        <div style="position:relative;width:100%;height:100%;left:0px;">        
          <div style="position:absolute;width:100%;height:100%; top:25%;">
            <img style="height:50%;top:25%;left:25%;opacity:0.10;filter:alpha(opacity=10);" src="img/logoBig.png" />
          </div>
          <div id="welcomeTitle" style="position:absolute;width:100%;height:100%;top:5%;left:-30%" >
            <?php $logo="img/title.png"; 
                  if (file_exists("../logo.gif")) $logo="../logo.gif"; 
                  if (file_exists("../logo.png")) $logo="../logo.png"; ?> 
            <img style="max-height:60px" src="<?php echo $logo;?>" style="width: 300px; height:54px"/>
          </div>
        </div>
      </td>
    </tr>
</table>