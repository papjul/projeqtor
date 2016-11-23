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
 * Presents left menu of application. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/menuBar.php');
  //$iconSize=Parameter::getUserParameter('paramTopIconSize');
  $iconSize=32;
  $showMenuBar=Parameter::getUserParameter('paramShowMenuBar');
  $showMenuBar='YES';
  //$showMenuBar='NO';
  if (! $iconSize or $showMenuBar=='NO') $iconSize=16;
  $allMenuClass=array('menuBarItem'=>'all','menuBarCustom'=>'custom');
  
  $customMenuArray=SqlList::getListWithCrit("MenuCustom",array('idUser'=>getSessionUser()->id));
  
  $cptAllMenu=0;
  $obj=new Menu();
  $menuList=$obj->getSqlElementsFromCriteria(null, false);
  $pluginObjectClass='Menu';
  $tableObject=$menuList;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $menuList=$tableObject;
  $defaultMenu=Parameter::getUserParameter('defaultMenu');
  if (! $defaultMenu) $defaultMenu='menuBarItem';
  foreach ($menuList as $menu) {
    if (securityCheckDisplayMenu($menu->id,substr($menu->name,4))) {
      $menuClass=$menu->menuClass;
      if (in_array($menu->name,$customMenuArray)) $menuClass.=" menuBarCustom";
      if ($menu->type!='menu' and (strpos(' menuBarItem '.$menuClass, $defaultMenu)>0)) {
        $cptAllMenu+=1;
      }
      if ($menu->type=='menu' or $menu->name=='menuAlert' or $menu->name=='menuToday' or $menu->name=='menuReports' or $menu->name=='menuParameter' or $menu->name=='menuUserParameter') {
        continue;
      }
      $sp=explode(" ", $menu->menuClass);
      foreach ($sp as $cl) {
        if (trim($cl)) {
          $allMenuClass[$cl]=$cl;
        }
      }
    }
  }
  
  function drawMenu($menu) {
  	global $iconSize, $defaultMenu,$customMenuArray;
  	$menuName=$menu->name;
  	$menuClass=' menuBarItem '.$menu->menuClass;
  	if (in_array($menu->name,$customMenuArray)) $menuClass.=' menuBarCustom';
  	$idMenu=$menu->id;
    $style=(strpos($menuClass, $defaultMenu)===false)?'display: none;':'display: block; opacity: 1;';
  	if ($menu->type=='menu') {
    	if ($menu->idMenu==0) {
    		//echo '<td class="menuBarSeparator" style="width:5px;"></td>';
    	}
    } else if ($menu->type=='item') {
    	  $class=substr($menuName,4); 
        //echo '<td  title="' .(($menuName=='menuReports')?'':i18n($menu->name)) . '" >';
    	  echo '<td  title="' .i18n($menu->name) . '" >';
        echo '<div class="'.$menuClass.'" style="position:relative;'.$style.'" id="'.$class.'" ';
        echo 'onClick="hideReportFavoriteTooltip(0);loadMenuBarItem(\'' . $class .  '\',\'' . htmlEncode(i18n($menu->name),'quotes') . '\',\'bar\');" ';
        echo 'oncontextmenu="event.preventDefault();customMenuManagement(\''.$class.'\');" ';
        if ($menuName=='menuReports' and isHtml5() ) {
          echo ' onMouseEnter="showReportFavoriteTooltip();"';
          echo ' onMouseLeave="hideReportFavoriteTooltip(2000);"';
        }
        echo '>';
        //echo '<img src="../view/css/images/icon' . $class . $iconSize.'.png" />';
        echo '<div class="icon' . $class . $iconSize.'" style="margin-left:9px;width:'.$iconSize.'px;height:'.$iconSize.'px" /></div>';
        echo '<div class="menuBarItemCaption">'.i18n($menu->name).'</div>';
        if ($menuName=='menuReports' and isHtml5() ) {?>
          <button class="comboButtonInvisible" dojoType="dijit.form.DropDownButton" 
           id="listFavoriteReports" name="listFavoriteReports" style="position:relative;top:-10px;left:-10px;">
            <div dojoType="dijit.TooltipDialog" id="favoriteReports" style="display:none;"
              href="../tool/refreshFavoriteReportList.php"
              onMouseEnter="clearTimeout(closeFavoriteReportsTimeout);"
              onMouseLeave="hideReportFavoriteTooltip(200)"
              onDownloadEnd="checkEmptyReportFavoriteTooltip()">
              <?php Favorite::drawReportList();?>
            </div>
          </button>
        <?php }
        echo '</div>';
        echo '</td>'; 
    } else if ($menu->type=='plugin') {
      $class=substr($menuName,4);
      echo '<td  title="' .i18n($menu->name) . '" >';
      echo '<div class="'.$menuClass.'" style="'.$style.'" id="'.$class.'"';
      echo 'oncontextmenu="event.preventDefault();customMenuManagement(\''.$class.'\');" ';
      echo 'onClick="loadMenuBarPlugin(\'' . $class .  '\',\'' . htmlEncode(i18n($menu->name),'quotes') . '\',\'bar\');">';
      echo '<img src="../view/css/images/icon' . $class . $iconSize.'.png" />';
      echo '<div class="menuBarItemCaption">'.i18n($menu->name).'</div>';
      echo '</div>';
      echo '</td>';
    } else if ($menu->type=='object') { 
      $class=substr($menuName,4);
      if (securityCheckDisplayMenu($idMenu, $class)) {
      	echo '<td title="' .i18n('menu'.$class) . '" >';
      	echo '<div class="'.$menuClass.'" style="'.$style.'" id="'.$class.'" ';
      	echo 'oncontextmenu="event.preventDefault();customMenuManagement(\''.$class.'\');" ';
      	echo 'onClick="loadMenuBarObject(\'' . $class .  '\',\'' . htmlEncode(i18n($menu->name),'quotes') . '\',\'bar\');" >';
      	echo '<div class="icon' . $class . $iconSize.'" style="margin-left:9px;width:'.$iconSize.'px;height:'.$iconSize.'px" /></div>';
      	//echo '<img src="../view/css/images/icon' . $class . $iconSize. '.png" />';
      	echo '<div class="menuBarItemCaption">'.i18n('menu'.$class).'</div>';
      	echo '</div>';
      	echo '</td>';
      }
    }
  }  
  
  function drawAllMenus($menuList) {
    //echo '<td>&nbsp;</td>';
    $obj=new Menu();
    $menuList=$obj->getSqlElementsFromCriteria(null, false);
    $pluginObjectClass='Menu';
    $tableObject=$menuList;
    $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
    foreach ($lstPluginEvt as $script) {
      require $script; // execute code
    }
    $menuList=$tableObject;
    $lastType='';
    foreach ($menuList as $menu) { 
      if (securityCheckDisplayMenu($menu->id,substr($menu->name,4)) ) {
    		drawMenu($menu);
    		$lastType=$menu->type;
    	}
    }
    //echo '<td>&nbsp;</td>';
  }
?>
  <table width="100%"><tr height="<?php echo $iconSize+18; ?>px">  
    <td width="287px">
      <div class="titleProject" style="position: absolute; left:0px; top: -1px;width:75px; text-align:right;">
        &nbsp;<?php echo (i18n("menu"));?>&nbsp;:&nbsp;</div>
      <div style="position: absolute; left:75px; top: 1px;width:205px; background: transparent; color: #FFFFFF !important; border:1px solid #FFF;vertical-align:middle;" 
        onChange="menuFilter(this.value);" id="menuSelector" id="menuSelector"
        onMouseEnter="showMenuList();" onMouseLeave="hideMenuList(300);"
        dojoType="dijit.form.Select" class="input filterField rounded menuSelect" 
        ><?php foreach ($allMenuClass as $cl=>$clVal) {
          $selected=($defaultMenu==$cl)?' selected=selected ':'';
          echo '<option value="'.$cl.'" '.$selected.' style="color:#fff !important;">';
          echo '<div style="z-index:9999;height:14px;vertical-align:middle;top:-1px;width:180px;" value="'.$cl.'" '.$selected.' class="menuSelectList" onMouseOver="clearTimeout(closeMenuListTimeout);" onMouseLeave="hideMenuList(200,\''.$cl.'\');">';
          echo '  <div style="z-index:9;position:absolute;height:16px;width:18px;left:9px;background-color:#ffffff;border-radius:5px;opacity: 0.5;">&nbsp;</div>';
          echo '  <span style="z-index:10;position:absolute;height:16px;left:10px;" class="icon'.ucfirst($cl).'16">&nbsp;</span>';
          echo '  <span style="z-index:11;position:absolute;left:35px;top:5px;">'. i18n('menu'.ucfirst($clVal)).'</span>';
          echo '</div>';
          echo '</option>';
      }?></div>
      <div class="titleProject" style="position: absolute; left:0px; top: 22px;width:75px; text-align:right;">
        &nbsp;<?php echo (i18n("projectSelector"));?>&nbsp;:&nbsp;</div>
      <div style="height:100%" dojoType="dijit.layout.ContentPane" region="center" id="projectSelectorDiv" >
        <?php include "menuProjectSelector.php"?>
      </div>
      <span style="position: absolute; left:250px; top:22px; height: 20px">
        <button id="projectSelectorParametersButton" dojoType="dijit.form.Button" showlabel="false"
         title="<?php echo i18n('menuParameter');?>" style="height:20px;"
         iconClass="iconParameter16" xclass="detailButton">
          <script type="dojo/connect" event="onClick" args="evt">
           loadDialog('dialogProjectSelectorParameters', null, true);
          </script>
        </button>
      </span>
    </td>
<?php if ($showMenuBar!='NO') {?>    
    <td width="8px" id="menuBarLeft" >
      <button id="menuBarMoveLeft" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('menuBarMoveLeft');?>" class="buttonMove"
       iconClass="leftBarIcon" style="position:relative; left:-4px;width: 14px;top:-2px;height:48px;margin:0;vertical-align:middle">
         <script type="dojo/method" event="onMouseDown">         
           menuBarMove=true;
           moveMenuBar('left');
         </script>
         <script type="dojo/method" event="onMouseUp">
           moveMenuBarStop();
         </script>
         <script type="dojo/method" event="onClick">
           moveMenuBarStop();
         </script>
      </button>    
    </td>
    <td>
    <div id="menuBarVisibleDiv" style="height:<?php echo $iconSize+9;?>px;width:<?php echo ($cptAllMenu*56);?>px; position: absolute; top: 0px; left: 300px; z-index:0">
      <div style="width: 100%; height:50px; position: absolute; left: 0px; top:0px; overflow:hidden; z-index:0">
	    <div name="menubarContainer" id="menubarContainer" style="width:<?php echo ($cptAllMenu*56);?>px; position: absolute; left:0px; overflow:hidden;z-index:0">
	      <table><tr>
	    <?php drawAllMenus($menuList);?>
	    </tr></table>
	    </div>
      </div>
    </div>
    </td> 
<?php } else {?>
    <td style="width:80%"><div id="menuBarVisibleDiv"></div></td>
<?php }?>
    <td width="100px" align="center" id="menuBarRight" class="statusBar" style="position:relative;z-index:30;">
      <table><tr><td rowspan="2">
<?php if ($showMenuBar!='NO') {?>       
      <button id="menuBarMoveRight" dojoType="dijit.form.Button" showlabel="false" 
       title="<?php echo i18n('menuBarMoveRight');?>"
       iconClass="rightBarIcon" class="buttonMove" 
       style="position:absolute; left:4px;width: 14px;margin:0;top:0px;height:48px; z-index:35;">
         <script type="dojo/method" event="onMouseDown">         
           menuBarMove=true;
           moveMenuBar('right');
         </script>
         <script type="dojo/method" event="onMouseUp">
           moveMenuBarStop();
         </script>
         <script type="dojo/method" event="onClick">
           moveMenuBarStop();
         </script>
      </button>   
<?php }?>
      </td><td>
      <button id="menuBarUndoButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonUndoItem');?>"
       disabled="disabled"
       style="position:relative;left: 10px; top:-5px; z-index:30;height:18px"
       iconClass="dijitButtonIcon dijitButtonIconPrevious" class="detailButton" >
        <script type="dojo/connect" event="onClick" args="evt">
          undoItemButton();
        </script>
      </button>  
      </td><td>  
      <button id="menuBarRedoButton" dojoType="dijit.form.Button" showlabel="false"
       title="<?php echo i18n('buttonRedoItem');?>"
       disabled="disabled"
       style="position:relative;left: 10px; top:-5px; z-index:30;height:18px"
       iconClass="dijitButtonIcon dijitButtonIconNext" class="detailButton" >
        <script type="dojo/connect" event="onClick" args="evt">
          redoItemButton();
        </script>
      </button>
      </td></tr>
      <tr style="height:10px;"><td colspan="2">     
       <a id="menuBarNewtabButton" title="<?php echo i18n('buttonNewtabItem');?>"
       style="height:18px; position:relative; top:-1px;left:10px;width:60px;" 
       href="" target="_blank">
       <button dojoType="dijit.form.Button" iconClass="dijitButtonIcon iconNewtab" class="detailButton"
       style="height:16px;width:60px;">
         <script type="dojo/connect" event="onClick" args="evt">
           var url="main.php?directAccess=true";
           if (dojo.byId('objectClass') && dojo.byId('objectClass').value) { 
             url+="&objectClass="+dojo.byId('objectClass').value;
           } else {
             url+="&objectClass=Today";
           }
           if (dojo.byId('objectId') && dojo.byId('objectId').value) {
             url+="&objectId="+dojo.byId('objectId').value;
           } else {
             url+="&objectId=";
           }
           dojo.byId("menuBarNewtabButton").href=url;
         </script>
       </button>
       </a>
      </td></tr>
      </table>    
    </td>
  </tr></table>
  <div class="customMenuAddRemove"  id="customMenuAdd" onClick="customMenuAddItem();"><?php echo i18n('customMenuAdd');?></div>
  <div class="customMenuAddRemove"  id="customMenuRemove" onClick="customMenuRemoveItem();"><?php echo i18n('customMenuRemove');?></div>
      