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
 * Presents an object. 
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/dashboardTicketMain.php'); 
  $user=getSessionUser();
  $nbDay=7;
  if(isset($_REQUEST['dashboardTicketMainNumberDay'])){
    $nbDay=$_REQUEST['dashboardTicketMainNumberDay'];
    if(!is_numeric($nbDay))$nbDay=7;
    Parameter::storeUserParameter("dashboardTicketMainNumberDay", $nbDay);
  }
  if(Parameter::getUserParameter("dashboardTicketMainNumberDay")!=null){
    $nbDay=Parameter::getUserParameter("dashboardTicketMainNumberDay");
  }else{
    Parameter::storeUserParameter("dashboardTicketMainNumberDay", $nbDay);
  }
  if(Parameter::getUserParameter("dashboardTicketMainTabPosition")!=null){
    $tabPosition=Parameter::getUserParameter("dashboardTicketMainTabPosition");
  }else{
    $tabPosition='
    {
    "orderListLeft":["TicketType","Priority","Product","Component"],
    "orderListRight":["OriginalProductVersion","TargetProductVersion","Contact","Resource","Status"],
    "TicketType":{"title":"dashboardTicketMainTitleType","withParam":true,"idle":true},
    "Priority":{"title":"dashboardTicketMainTitlePriority","withParam":true,"idle":true},
    "Product":{"title":"dashboardTicketMainTitleProduct","withParam":true,"idle":true},
    "Component":{"title":"dashboardTicketMainTitleCompoment","withParam":true,"idle":true},
    "OriginalProductVersion":{"title":"dashboardTicketMainTitleOriginVersion","withParam":true,"idle":true},
    "TargetProductVersion":{"title":"dashboardTicketMainTitleTargetVersion","withParam":true,"idle":true},
    "Contact":{"title":"dashboardTicketMainTitleUser","withParam":true,"idle":true},
    "Resource":{"title":"dashboardTicketMainTitleResponsible","withParam":true,"idle":true},
    "Status":{"title":"dashboardTicketMainTitleStatus","withParam":false,"idle":true}
    }
    ';
    Parameter::storeUserParameter("dashboardTicketMainTabPosition", $tabPosition);
  }

  $addParam=addParametersDashboardTicketMain();
  if($addParam!=""){
    $addParam=', "paramAdd":"'.$addParam.'"';
  }
  $tabPosition=json_decode($tabPosition,true);
  if(isset($_REQUEST['updatePosTab'])){
    $decodeRequest=json_decode($_REQUEST['updatePosTab'],true);
    $tabPosition['orderListLeft']=$decodeRequest['addLeft'];
    $tabPosition['orderListRight']=$decodeRequest['addRight'];
    for ($ite=0; $ite<sizeof($decodeRequest['iddleList']); $ite++){
      $tabPosition[$decodeRequest['iddleList'][$ite]["name"]]["idle"]=$decodeRequest['iddleList'][$ite]["idle"];
    }
    Parameter::storeUserParameter("dashboardTicketMainTabPosition", json_encode($tabPosition));
  }

  if(isset($_REQUEST['goToTicket'])){
    addParamToUser($user);
  }else{
?>
<div dojo-type="dijit.layout.BorderContainer" class="container">
<input type="hidden" name="objectClassManual" id="objectClassManual" value="DashboardTicket" />
	<div dojo-type="dijit.layout.ContentPane" id="parameterButtonDiv"
		class="listTitle" style="z-index: 3; overflow: visible" region="top">
		<div id="resultDiv" region="top"
			style="padding: 5px; padding-bottom: 20px; max-height: 100px; padding-left: 300px; z-index: 999"></div>

		<table width="100%">
			<tr height="32px" >
				<td width="50px" align="center"><?php echo formatIcon('TicketDashboard', 32, null, true);?></td>
				<td><span class="title"><?php echo i18n('dashboardTicketMainTitle');?>&nbsp;</span>
				</td>
			</tr>
		</table>

	</div>
	<div dojo-type="dijit.layout.ContentPane" region="center" style="height:100%;overflow:auto;">
		<div
			style="width: 97%; margin: 0 auto; height: 90px; padding-bottom: 15px; border-bottom: 1px solid #CCC;">
			<table width="100%" class="dashboardTicketMain">
				<tr>
					<td valign="top">
						<table>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainAllTicket=0')"
									href="#"><?php echo i18n("dashboardTicketMainAllIssues").addSelected("dashboardTicketMainAllTicket",0);?></a></td>
							</tr>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainAllTicket=2')"
									href="#"><?php echo i18n("dashboardTicketMainUnclosed").addSelected("dashboardTicketMainAllTicket",2);?></a></td>
							</tr>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainAllTicket=1')"
									href="#"><?php echo i18n("dashboardTicketMainUnresolved").addSelected("dashboardTicketMainAllTicket",1);?></a></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainRecent=1')"
									href="#"><?php echo i18n("dashboardTicketMainAddedRecently").addSelected("dashboardTicketMainRecent",1);?></a></td>
							</tr>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainRecent=2')"
									href="#"><?php echo i18n("dashboardTicketMainResolvedRecently").addSelected("dashboardTicketMainRecent",2);?></a></td>
							</tr>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainRecent=3')"
									href="#"><?php echo i18n("dashboardTicketMainUpdatedRecently").addSelected("dashboardTicketMainRecent",3);?></a></td>
							</tr>
							<tr>
								<td align="left"><?php echo i18n("dashboardTicketMainNumberDay");?>&nbsp;:&nbsp;<div
										dojoType="dijit.form.NumberTextBox"
										id="dashboardTicketMainNumberDay" style="width: 30px"
										onChange="if(isNaN(this.value))dijit.byId('dashboardTicketMainNumberDay').set('value',7);
          loadContent('dashboardTicketMain.php?dashboardTicketMainNumberDay='+dijit.byId('dashboardTicketMainNumberDay').get('value'), 'centerDiv', 'dashboardTicketMainForm');
          "
										value="<?php echo $nbDay;?>"></div></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainToMe=1')"
									href="#"><?php echo i18n("dashboardTicketMainAssignedToMe").addSelected("dashboardTicketMainToMe",1);?></a></td>
							</tr>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainToMe=2')"
									href="#"><?php echo i18n("dashboardTicketMainReportedByMe").addSelected("dashboardTicketMainToMe",2);?></a></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td align="left"><a
									onClick="changeParamDashboardTicket('dashboardTicketMainUnresolved=1')"
									href="#"><?php echo i18n("dashboardTicketMainUnscheduled").addSelected("dashboardTicketMainUnresolved",1);?></a></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<button id="updateTabDashboardTicketMain"
							dojoType="dijit.form.Button" showlabel="false"
							title="<?php echo i18n('menuParameter');?>"
							iconClass="iconParameter16">
							<script type="dojo/connect" event="onClick" args="evt">
          dijit.byId('popUpdatePositionTab').show();
        </script>
						</button>
					</td>
				</tr>
			</table>

		</div>
		<div style="width: 97%; margin: 0 auto; padding-bottom: 50px;">
			<div style="width: 50%; float: left; padding-bottom: 50px;">
      <?php 
      foreach ($tabPosition["orderListLeft"] as $key){
        $nAddP="";
        if($tabPosition[$key]['withParam'])$nAddP=$addParam;
        if($tabPosition[$key]['idle'])echo addTab('{"groupBy":"'.$key.'","withParam":"'.$tabPosition[$key]['withParam'].'","title":"'.$tabPosition[$key]['title'].'"'.$nAddP.'}');
      }
      ?>
    </div>
			<div style="width: 50%; float: left; padding-bottom: 50px;">
      <?php 
      foreach ($tabPosition["orderListRight"] as $key){
        $nAddP="";
        if($tabPosition[$key]['withParam'])$nAddP=$addParam;
        if($tabPosition[$key]['idle'])echo addTab('{"groupBy":"'.$key.'","withParam":"'.$tabPosition[$key]['withParam'].'","title":"'.$tabPosition[$key]['title'].'"'.$nAddP.'}');
      }
      ?>
      </div>
		</div>
	</div>
</div>
<div id="popUpdatePositionTab" dojoType="dijit.Dialog"
	onHide="loadContent('dashboardTicketMain.php', 'centerDiv');" title="<?php echo i18n("listTodayItems");?>">
  <?php createPopUpDnd($tabPosition);?>
</div>
<?php 
  }
  global $total1;
  global $total2;
  $total1=null;
  $total2=null;
function addTab($param){
  global $total1;
  global $total2;
  $param=json_decode($param,true);
  $ajoutGroupBy="t.id".$param["groupBy"];
  $ajoutWhere=" $ajoutGroupBy=a.id ";
  $paramAdd="";
  $total=0;
  $obT=new Ticket();
  $tableName=$obT->getDatabaseTableName();
  if(isset($param['paramAdd'])){
    $paramAdd=$param['paramAdd'];
    if($total1==null){
      $result=Sql::query("SELECT COUNT(*) as nbline FROM $tableName t WHERE t.idProject in ".getVisibleProjectsList(false)." $paramAdd ");
      if (Sql::$lastQueryNbRows > 0) {
        $line = Sql::fetchLine($result);
        $total1=$line['nbline'];
      }
    }
    $total=$total1;
  }else{
    if($total2==null){
      $result=Sql::query("SELECT COUNT(*) as nbline FROM $tableName t WHERE t.idProject in ".getVisibleProjectsList(false));
      if (Sql::$lastQueryNbRows > 0) {
        $line = Sql::fetchLine($result);
        $total2=$line['nbline'];
      }
    }
    $total=$total2;
  }
  
  $result=Sql::query("SELECT COUNT(*) as nbline, $ajoutGroupBy as idneed FROM $tableName t WHERE $ajoutGroupBy is not null AND t.idProject in ".getVisibleProjectsList(false)." $paramAdd GROUP BY $ajoutGroupBy ");
  if ($total > 0) {
    $res=array();
    $totT=0;
    while ($line = Sql::fetchLine($result)) {
      $object= new $param["groupBy"]($line['idneed'],true);
      $idU=$object->name;
      if(isset($object->sortOrder)){
        $idU=$object->sortOrder.'-'.$object->id;
      }
      $res[$idU]["name"]=$object->name;
      $res[$idU]["nb"]=$line['nbline'];
      $res[$idU]["id"]=$object->id;
      if(isset($object->color))$res[$idU]["color"]=$object->color;
      $totT+=$line['nbline'];
    }
    $addIfNoParam="";
    if(!$param['withParam'])$addIfNoParam='<span style="font-style:italic;color:#999999;">&nbsp;('.i18n('noFilterClause').')</span>';
    ksort($res);
    echo '<h2 style="color:#333333;font-size:16px;">'.trim(i18n("dashboardTicketMainTitleBase"))." ".(i18n($param["title"])).$addIfNoParam."</h2>";
    echo "<table width=\"95%\" class=\"tabDashboardTicketMain\">";
    echo '<tr><td class="titleTabTicket">'.i18n($param["title"]).'</td><td class="titleTabTicket">'.i18n("dashboardTicketMainColumnCount").'</td><td class="titleTabTicket">'.i18n("dashboardTicketMainColumnPourcent")."</td></tr>";
    foreach ($res as $idSort=>$nbline){
      $name='<a href="#" onclick="loadContent(\'dashboardTicketMain.php?goToTicket='.$param["groupBy"].'&val='.$nbline['id'].'\', \'centerDiv\', \'dashboardTicketMainForm\');">'.$nbline["name"].'</a>';
      $addColor=$name;
      if(isset($nbline["color"])){
        $addColor="<div style=\"background-color:".$nbline["color"].";border:1px solid #AAAAAA;border-radius:50%;width:20px;height:18px;float:left;\">&nbsp;</div><div style=\"color:".$nbline["color"].";radius:50%;width:10px;height:10px;float:left;\">&nbsp;</div>"
                 ."<div style=\"float:left;\">".$name."</div>";
      }
      echo "  <tr>";
      echo "    <td width=\"50%\">";
      echo $addColor;
      echo "    </td>";
      echo "    <td width=\"10%\">";
      echo $nbline["nb"];
      echo "    </td>";
      echo "    <td width=\"40%\">";
      echo '<div style="background-color:#3c78b5;margin-top: 3px;position:relative;height:13px;width:'.round(100*($nbline["nb"]/$total)).'px;float:left;">&nbsp;</div><div style="position:relative;margin-left:10px;width:50px; float: left;">'.round(100*($nbline["nb"]/$total))." %</div>";
      echo "    </td>";
      echo "  </tr>";
    }
    if($total-$totT>0){
      echo "  <tr>";
      echo "    <td width=\"50%\">";
      echo '<a class="styleUDashboard" href="#" onclick="loadContent(\'dashboardTicketMain.php?goToTicket='.$param["groupBy"].'&undefined=true\', \'centerDiv\', \'dashboardTicketMainForm\');">'.i18n("undefinedValue").'</a>';
      echo "    </td>";
      echo "    <td width=\"10%\">";
      echo '<span>'.($total-$totT).'</span>';
      echo "    </td>";
      echo "    <td width=\"40%\">&nbsp;";
      echo '<div style="background-color:#3c78b5;margin-top: 3px;position:relative;height:13px;width:'.round(100*(($total-$totT)/$total)).'px;float:left;">&nbsp;</div><div style="position:relative;margin-left:10px;width:50px; float: left;">'.round(100*(($total-$totT)/$total))." %</div>";
      echo "    </td>";
      echo "  </tr>";
    }
    echo "  <tr>";
    echo "    <td width=\"50%\">";
    echo '<a class="styleADashboard" href="#" onclick="loadContent(\'dashboardTicketMain.php?goToTicket='.$param["groupBy"].'\', \'centerDiv\', \'dashboardTicketMainForm\');">'.i18n("dashboardTicketMainAllIssues").'</a>';
    echo "    </td>";
    echo "    <td width=\"10%\">";
    echo '<span style="font-weight: bold;">'.$total.'</span>';
    echo "    </td>";
    echo "    <td width=\"40%\">&nbsp;";
    echo "    </td>";
    echo "  </tr>";
    echo "</table>";
    echo '<div style="width:95%;height:2px;margin-top:0px;margin-bottom:35px;background-color:#CCCCCC"></div>';
  }else{
    echo '<h2 style="color:#333333;font-size:16px;">'.(substr(i18n("dashboardTicketMainTitleBase"),-1)==" "?i18n("dashboardTicketMainTitleBase"):i18n("dashboardTicketMainTitleBase")." ").(i18n($param["title"]))."</h2>";
    echo '<span style="color:#333333;font-size:14px;font-style: italic;">'.i18n("noDataFound").'</span>';
    echo '<div style="width:95%;height:3px;margin-top:13px;margin-bottom:20px;background-color:#CCCCCC"></div>';
  }
}

function addSelected($param,$value){
  if(Parameter::getUserParameter($param)!=null){
    if(Parameter::getUserParameter($param)==$value){
      return "&nbsp;&nbsp;<img src=\"css/images/iconSelect.png\"/>";
    }
  }
}

function createPopUpDnd($tabPosition){
  echo '<table><tr><td valign="top"><table id="dndDashboardLeftParameters" jsId="dndDashboardLeftParameters" dojotype="dojo.dnd.Source" dndType="tableauBordLeft"
               withhandles="true" style="width:300px;cellspacing:0; cellpadding:0;" data-dojo-props="accept: [ \'tableauBordRight\',\'tableauBordLeft\' ]"> ';
echo '<tr><td colspan="3">&nbsp;</td></tr>';
foreach ($tabPosition['orderListLeft'] as $tableauBordLeftItem) {
  echo '<tr style="height:24px" id="dialogDashboardLeftParametersRow' .$tableauBordLeftItem. '"
              class="dojoDndItem" dndType="tableauBordLeft" style="height:10px;">';
  echo '<td valign="top" style="padding-right:10px;" class="dojoDndHandle handleCursor"><img style="width:6px" src="css/images/iconDrag.gif">&nbsp;</td>';
  echo '<td valign="top" style="padding-right:10px;"><div id="tableauBordTabIdle' .$tableauBordLeftItem. '" 
                 dojoType="dijit.form.CheckBox" type="checkbox" '.($tabPosition[$tableauBordLeftItem]['idle']?' checked="checked"':'').'>
                </div></td>';
  echo "<td valign=\"top\" style=\"padding-right:10px;\"><span class='nobr'>".(substr(i18n("dashboardTicketMainTitleBase"),-1)==" "?i18n("dashboardTicketMainTitleBase"):i18n("dashboardTicketMainTitleBase")." ").(i18n($tabPosition[$tableauBordLeftItem]['title']))."</span>";
  echo '</td>';
  echo '</tr>';
}
echo '</table></td><td width="20px;"></td>';
echo '<td valign="top"><table id="dndDashboardRightParameters" jsId="dndDashboardRightParameters" dojotype="dojo.dnd.Source" dndType="tableauBordRight"
               withhandles="true" style="width:300px;cellspacing:0; cellpadding:0;" data-dojo-props="accept: [ \'tableauBordRight\',\'tableauBordLeft\' ]">';
echo '<tr><td colspan="3">&nbsp;</td></tr>';
foreach ($tabPosition['orderListRight'] as $tableauBordRightItem) {
  echo '<tr style="height:24px" id="dialogDashboardRightParametersRow' .$tableauBordRightItem. '"
              class="dojoDndItem" dndType="tableauBordRight" style="height:10px;">';
  echo '<td valign="top" style="padding-right:10px;" class="dojoDndHandle handleCursor"><img style="width:6px" src="css/images/iconDrag.gif">&nbsp;</td>';
  echo '<td valign="top" style="padding-right:10px;"><div id="tableauBordTabIdle' .$tableauBordRightItem. '" 
               dojoType="dijit.form.CheckBox" type="checkbox" '.($tabPosition[$tableauBordRightItem]['idle']?' checked="checked"':'').'>
              </div></td>';
  echo "<td valign=\"top\" style=\"padding-right:10px;\"><span class='nobr'>".(substr(i18n("dashboardTicketMainTitleBase"),-1)==" "?i18n("dashboardTicketMainTitleBase"):i18n("dashboardTicketMainTitleBase")." ").(i18n($tabPosition[$tableauBordRightItem]['title']))."</span>";
  echo '</td>';
  echo '</tr>';
}
echo '</table></td></tr>
              <tr><td></td><td></td><td></td></tr>
              <tr><td align="right"></td><td></td><td align="left">
              <button style="margin-right:15px;" class="mediumTextButton" dojoType="dijit.form.Button" type="button" onclick="dijit.byId(\'popUpdatePositionTab\').hide();">
                '.i18n("buttonCancel").'
              </button>
              <button class="mediumTextButton" dojoType="dijit.form.Button" type="submit" id="confirmChangeTabBordTicketMain" onclick="protectDblClick(this);changeDashboardTicketMainTabPos();return false;">
                '.i18n("buttonOK").'
              </button>
              </td></tr>
              </table>';
}

function addParametersDashboardTicketMain($prefix="t"){
  $user=getSessionUser();
  $result="";
  $allTicket="0";
  if(isset($_REQUEST['dashboardTicketMainAllTicket'])){
    Parameter::storeUserParameter("dashboardTicketMainAllTicket", $_REQUEST['dashboardTicketMainAllTicket']);
  }
  if(Parameter::getUserParameter("dashboardTicketMainAllTicket")!=null){
    $allTicket=Parameter::getUserParameter("dashboardTicketMainAllTicket");
  }else{
    Parameter::storeUserParameter("dashboardTicketMainAllTicket", $allTicket);
  }
  if($allTicket=="1")$result.=" AND $prefix.done=0 ";
  if($allTicket=="2")$result.=" AND $prefix.idle=0 ";

  $recent="0";
  $nbDay=7;
  if(isset($_REQUEST['dashboardTicketMainRecent'])){
    if(Parameter::getUserParameter("dashboardTicketMainRecent")!=null){
      if($_REQUEST['dashboardTicketMainRecent']==Parameter::getUserParameter("dashboardTicketMainRecent"))$_REQUEST['dashboardTicketMainRecent']="0";
    }
    Parameter::storeUserParameter("dashboardTicketMainRecent", $_REQUEST['dashboardTicketMainRecent']);
  }

  if(Parameter::getUserParameter("dashboardTicketMainNumberDay")!=null){
    $nbDay=Parameter::getUserParameter("dashboardTicketMainNumberDay");
  }
  if(Parameter::getUserParameter("dashboardTicketMainRecent")!=null){
    $recent=Parameter::getUserParameter("dashboardTicketMainRecent");
  }
  if (Sql::isPgsql()) {
    if($recent=="1")$result.=" AND $prefix.creationDateTime>=NOW() - INTERVAL '" . intval($nbDay) . " day' ";
    if($recent=="2")$result.=" AND $prefix.doneDateTime>=NOW() - INTERVAL '" . intval($nbDay) . " day' ";
    if($recent=="3")$result.=" AND $prefix.id IN (SELECT t2.refId FROM history t2 WHERE t2.refId=$prefix.id AND t2.refType='Ticket' AND t2.operationDate>=NOW() - INTERVAL '" . intval($nbDay) . " day' ) ";
  } else {
    if($recent=="1")$result.=" AND $prefix.creationDateTime>=ADDDATE(NOW(), INTERVAL (-" . intval($nbDay) . ") DAY) ";
    if($recent=="2")$result.=" AND $prefix.doneDateTime>=ADDDATE(NOW(), INTERVAL (-" . intval($nbDay) . ") DAY) ";
    if($recent=="3")$result.=" AND $prefix.id IN (SELECT t2.refId FROM history t2 WHERE t2.refId=$prefix.id AND t2.refType='Ticket' AND t2.operationDate>=ADDDATE(NOW(), INTERVAL (-" . intval($nbDay) . ") DAY)) ";
  }

  if(isset($_REQUEST['dashboardTicketMainToMe'])){
    if(Parameter::getUserParameter("dashboardTicketMainToMe")!=null){
      if($_REQUEST['dashboardTicketMainToMe']==Parameter::getUserParameter("dashboardTicketMainToMe"))$_REQUEST['dashboardTicketMainToMe']="0";
    }
    Parameter::storeUserParameter("dashboardTicketMainToMe", $_REQUEST['dashboardTicketMainToMe']);
  }

  $toMe="";
  if(Parameter::getUserParameter("dashboardTicketMainToMe")!=null){
    $toMe=Parameter::getUserParameter("dashboardTicketMainToMe");
  }
  if($toMe=="1")$result.=" AND $prefix.idResource=".$user->id." ";
  if($toMe=="2")$result.=" AND $prefix.idUser=".$user->id." ";
  $unresolved="";

  if(isset($_REQUEST['dashboardTicketMainUnresolved'])){
    if(Parameter::getUserParameter("dashboardTicketMainUnresolved")!=null){
      if($_REQUEST['dashboardTicketMainUnresolved']==Parameter::getUserParameter("dashboardTicketMainUnresolved"))$_REQUEST['dashboardTicketMainUnresolved']="0";
    }
    Parameter::storeUserParameter("dashboardTicketMainUnresolved", $_REQUEST['dashboardTicketMainUnresolved']);
  }

  if(Parameter::getUserParameter("dashboardTicketMainUnresolved")!=null){
    $unresolved=Parameter::getUserParameter("dashboardTicketMainUnresolved");
  }
  if($unresolved=="1")$result.=" AND $prefix.idTargetProductVersion is null ";

  return $result;
}

function addParamToUser($user){
  $user->_arrayFilters['Ticket']=array();
  $objectClass=$_REQUEST['goToTicket'];
  $obRef=new Ticket();
  $iterateur=0;
  if(isset($_REQUEST['val'])){
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='id'.$objectClass;
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='=';
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=$_REQUEST['val'];
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('id'.$objectClass);
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']='=';
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=SqlList::getNameFromId($objectClass, $_REQUEST['val']);
  }else{
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='id'.$objectClass;
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='SORT';
    $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']='asc';
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('id'.$objectClass);
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=i18n('sortFilter');
    $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n('sortAsc');
    if(isset($_REQUEST['undefined'])){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='id'.$objectClass;
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='is null';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']='';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('id'.$objectClass);
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=i18n("isNotEmpty");
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']='';
      $iterateur++;
    }
  }
  $iterateur++;
  $tabPosition=Parameter::getUserParameter("dashboardTicketMainTabPosition");
  $tabPosition=json_decode($tabPosition,true);
  if($tabPosition[$objectClass]["withParam"]){
    $allTicket=Parameter::getUserParameter("dashboardTicketMainAllTicket");
    if($allTicket=="0"){
      $user->_arrayFilters['Ticket'][$iterateur]['disp']["attribute"]=i18n('labelShowIdle');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']["operator"]="";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']["value"]="";
      $user->_arrayFilters['Ticket'][$iterateur]['sql']["attribute"]='idle';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']["operator"]='>=';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']["value"]='0';
      $iterateur++;
    }
    if($allTicket=="1"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='done';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='=';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']='0';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('done');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']='=';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n('no');
      $iterateur++;
    }
    if($allTicket=="2"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='idle';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='=';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']='0';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('idle');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']="=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n("yes");
      $iterateur++;
    }
    
    $recent=Parameter::getUserParameter("dashboardTicketMainRecent");
    $nbDay=Parameter::getUserParameter("dashboardTicketMainNumberDay");
    if (preg_match('/[^\-0-9]/', $nbDay) == true) {
      $nbDay="";
    }
    if($recent=="1"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='creationDateTime';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='>=';
      //$user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=(-$nbDay)+'';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('creationDateTime');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=">=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n('today').' -'.$nbDay.' '.i18n('days');
      if (Sql::isPgsql()) {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']= "NOW() + INTERVAL '" . (intval($nbDay)*(-1)) . " day'";
      } else {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']= "ADDDATE(NOW(), INTERVAL (" . (intval($nbDay)*(-1)) . ") DAY)";
      }
      $iterateur++;
    }
    
    if($recent=="2"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='doneDateTime';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='>=';
      //$user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=(-$nbDay)+'';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('doneDateTime');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=">=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n('today').' -'.$nbDay.' '.i18n('days');
      if (Sql::isPgsql()) {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']= "NOW() + INTERVAL '" . (intval($nbDay)*(-1)) . " day'";
      } else {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']= "ADDDATE(NOW(), INTERVAL (" . (intval($nbDay)*(-1)) . ") DAY)";
      }
      $iterateur++;
    }
      //if($recent=="2")$result.=" AND $prefix.doneDateTime>=NOW() - INTERVAL '" . intval($nbDay) . " day' ";
      //if($recent=="2")$result.=" AND $prefix.doneDateTime>=ADDDATE(NOW(), INTERVAL (-" . intval($nbDay) . ") DAY) ";
    if($recent=="3"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='id';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='IN';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=i18n("dashboardTicketMainLastUpdate");
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=">=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=i18n('today').' -'.$nbDay.' '.i18n('days');
      if (Sql::isPgsql()) {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=" (SELECT t2.refId FROM history t2 WHERE t2.refId=ticket.id AND t2.refType='Ticket' AND t2.operationDate>=NOW() - INTERVAL '" . intval($nbDay) . " day' ) ";
      } else {
        $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=" (SELECT t2.refId FROM history t2 WHERE t2.refId=ticket.id AND t2.refType='Ticket' AND t2.operationDate>=ADDDATE(NOW(), INTERVAL (-" . intval($nbDay) . ") DAY)) ";
      }
      $iterateur++;
    }
    
    $toMe=Parameter::getUserParameter("dashboardTicketMainToMe");
    if($toMe=="1"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='idResource';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='=';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=$user->id;
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('idResource');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']="=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=$user->name;
      $iterateur++;
    }
    if($toMe=="2"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='idUser';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='=';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']=$user->id;
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('idUser');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']="=";
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']=$user->name;
      $iterateur++;
    }
    
    $unresolved=Parameter::getUserParameter("dashboardTicketMainUnresolved");
    if($unresolved=="1"){
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['attribute']='idTargetProductVersion';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['operator']='is null';
      $user->_arrayFilters['Ticket'][$iterateur]['sql']['value']='';
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['attribute']=$obRef->getColCaption('idTargetProductVersion');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['operator']=i18n('isEmpty');
      $user->_arrayFilters['Ticket'][$iterateur]['disp']['value']='';
      $iterateur++;
    }
  }else{
    $user->_arrayFilters['Ticket'][$iterateur]['disp']["attribute"]=i18n('labelShowIdle');
    $user->_arrayFilters['Ticket'][$iterateur]['disp']["operator"]="";
    $user->_arrayFilters['Ticket'][$iterateur]['disp']["value"]="";
    $user->_arrayFilters['Ticket'][$iterateur]['sql']["attribute"]='idle';
    $user->_arrayFilters['Ticket'][$iterateur]['sql']["operator"]='>=';
    $user->_arrayFilters['Ticket'][$iterateur]['sql']["value"]='0';
    $iterateur++;
  }
  setSessionUser($user);
  $_REQUEST['objectClass']='Ticket';
  include 'objectMain.php';
}

?>