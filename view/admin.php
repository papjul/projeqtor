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
 * List of parameter specific to a user.
 * Every user may change these parameters (for his own user only !).
 */
  require_once "../tool/projeqtor.php";
  scriptLog('   ->/view/admin.php');
  
  $user=getSessionUser();
  $collapsedList=Collapsed::getCollaspedList();      
?>
<input type="hidden" name="objectClassManual" id="objectClassManual" value="Administration" />
<div class="container" dojoType="dijit.layout.BorderContainer">
  <div id="adminButtonDiv" class="listTitle" dojoType="dijit.layout.ContentPane" region="top" style="z-index:3;overflow:visible">
    <div id="resultDiv" dojoType="dijit.layout.ContentPane"
      region="top" style="padding:5px;max-height:100px;padding-left:300px;z-index:999"></div>
    <table width="100%">
      <tr height="32px" style="vertical-align: middle;">
        <td width="50px" align="center">
         <?php echo formatIcon('Admin',32,null,true);?>
        </td>
        <td><span class="title">
          <?php echo i18n("menuAdmin");?>&nbsp;</span>      
        </td>
        <td width="10px" >&nbsp;
        </td>
        <td width="50px"> 
        </td>
        <td>      
        </td>
      </tr>
    </table>
  </div>
  <div id="formAdminDiv" dojoType="dijit.layout.ContentPane" region="center" style="overflow-y:auto;"> 
    <form dojoType="dijit.form.Form" id="adminForm" jsId="adminForm" name="adminForm" encType="multipart/form-data" action="" method="" >
      <table style="width:97%;margin:10px;padding: 10px;vertical-align:top;">
        <tr style="">
          <td style="width:49%;vertical-align:top;">
            <?php $titlePane="Admin_cronTasks"; ?> 
            <div dojoType="dijit.TitlePane" 
             open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
             id="<?php echo $titlePane;?>" 
             onHide="saveCollapsed('<?php echo $titlePane;?>');"
             onShow="saveExpanded('<?php echo $titlePane;?>');"       
             title="<?php echo i18n('cronTasks');?>">
            <table style="width:100%;">            
              <tr>
                <td class="label"><?php echo i18n("cronStatus"). "&nbsp;:&nbsp;";?></td>
                <td class="display">
                  <?php 
                    $cronStatus=Cron::check();
                    echo i18n($cronStatus);
                    if ($cronStatus=='running') {
                    	$arrayTimes=Cron::getActualTimes();
                    	if (isset($arrayTimes['SleepTime'])) {
                    	  echo "<i><br/>&nbsp;&nbsp;&nbsp;" . i18n('adminCronSleepTime', array($arrayTimes['SleepTime'])) . '</i>';
                    	}
                    	if (isset($arrayTimes['CheckDates'])) {
                    	  echo "<i><br/>&nbsp;&nbsp;&nbsp;" . i18n('adminCronCheckDates', array($arrayTimes['CheckDates'])) . '</i>';
                    	}
                    	if (isset($arrayTimes['CheckImport'])) {
                    	  echo "<i><br/>&nbsp;&nbsp;&nbsp;" . i18n('adminCronCheckImport', array($arrayTimes['CheckImport'])) . '</i>';
                    	}
                      if (isset($arrayTimes['CheckEmails'])) {
                        echo "<i><br/>&nbsp;&nbsp;&nbsp;" . i18n('adminCronCheckEmails', array($arrayTimes['CheckEmails'])) . '</i>';
                      }
                    }
                  ?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="alertRunStop" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo ($cronStatus=='stopped')?i18n('run'):i18n('stop'); ?>
                   <script type="dojo/connect" event="onClick" args="evt">                 
                  <?php if ($cronStatus=='stopped') {
                  	echo 'showWait();adminLaunchScript("cronRun");';
                  	echo 'disableWidget("alertRunStop");';
                  } else {
                  	echo 'showWait();adminLaunchScript("cronStop");';
                  	echo 'disableWidget("alertRunStop");';  
                  }
                    echo 'return false;';
                  ?> 
                   </script>
                 </button>
                </td>
              </tr>
            </table>
            </div><br/>
            <?php $titlePane="Admin_sendAlert"; ?> 
            <div dojoType="dijit.TitlePane"
             open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
             id="<?php echo $titlePane;?>" 
             onHide="saveCollapsed('<?php echo $titlePane;?>');"
             onShow="saveExpanded('<?php echo $titlePane;?>');"
             title="<?php echo i18n('sendAlert');?>">
            <table style="width:100%;">
              <tr>
                <td width="200px;" class="label"><?php echo i18n("colMailTo"). "&nbsp;:&nbsp;";?></td>
                <td width="90%">
                  <select dojoType="dijit.form.FilteringSelect" class="input" required="true"
                    <?php echo autoOpenFilteringSelect();?>
                    style="width: 98%;" name="alertSendTo" id="alertSendTo">
                    <option value="*"><?php echo i18n('allUsers')?></option>
                    <option value="connect"><?php echo i18n('allConnectedUsers')?></option>
                    <?php htmlDrawOptionForReference('idUser', null, null, true);?>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="label"><?php echo i18n("colSendDate"). "&nbsp;:&nbsp;";?></td>
                <td>
                  <div dojoType="dijit.form.DateTextBox" name="alertSendDate" id="alertSendDate"
	                  <?php if (isset($_SESSION['browserLocaleDateFormatJs'])) {
											echo ' constraints="{datePattern:\''.$_SESSION['browserLocaleDateFormatJs'].'\'}" ';
										}?>
                    invalidMessage="<?php echo i18n('messageInvalidDate')?>" 
                    type="text" maxlength="10"
                    style="width:75px; text-align: center;" class="input" required="true"
                    value="<?php echo date('Y-m-d');?>"
                    hasDownArrow="false">
                  </div>
                  <div dojoType="dijit.form.TimeTextBox" name="alertSendTime" id="alertSendTime"
                    invalidMessage="<?php echo i18n('messageInvalidTime')?>" 
                    type="text" maxlength="5" required="true"
                    style="width:50px; text-align: center;" class="input"
                    value="T<?php echo date('H:i');?>" 
                    hasDownArrow="false">
                  </div>      
                </td>
              </tr>
              <tr>
                <td class="label"><?php echo i18n("colType"). "&nbsp;:&nbsp;";?></td>
                <td>
                  <select dojoType="dijit.form.FilteringSelect" class="input" 
                    <?php echo autoOpenFilteringSelect();?>
                    style="width: 98%;" name="alertSendType" id="alertSendType" required="true">
                    <option value="INFO"><?php echo i18n('INFO')?></option>
                    <option value="WARNING"><?php echo i18n('WARNING')?></option>
                    <option value="ALERT"><?php echo i18n('ALERT')?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <td class="label"><?php echo i18n("colTitle"). "&nbsp;:&nbsp;";?></td>
                <td>
                  <div dojoType="dijit.form.TextBox"
                    style="width:98%;" required="true"
                    name="alertSendTitle" id="alertSendTitle">
                  </div>
                </td>
              </tr>
              <tr>
                <td class="label"><?php echo i18n("colMessage"). "&nbsp;:&nbsp;";?></td>
                <td>
                  <textarea dojoType="dijit.form.Textarea"
                    name="alertSendMessage" id="alertSendMessage"
                    style="width:99%;"
                    maxlength="4000"
                    class="input"></textarea>
                </td>
              </tr>
              <tr>
                <td class="label"></td>
                <td>
                  <button id="alertSend" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('send'); ?>
                   <script type="dojo/connect" event="onClick" args="evt">                 
                     adminSendAlert();
                     return false;
                   </script>
                 </button>
                </td>
              </tr>
            </table></div><br/>
            
            <?php $titlePane="Admin_manageConnections"; ?> 
            <div dojoType="dijit.TitlePane"
             open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
             id="<?php echo $titlePane;?>" 
             onHide="saveCollapsed('<?php echo $titlePane;?>');"
             onShow="saveExpanded('<?php echo $titlePane;?>');"
             title="<?php echo i18n('manageConnections');?>">
            <table style="width:100%;">
              <tr>
                <td width="200px;" class="label"><?php echo i18n("activeConnections"). "&nbsp;:&nbsp;";?></td>
                <td width="90%">
                  <?php $audit=New Audit();
                  $cpt=$audit->countSqlElementsFromCriteria(array('idle'=>'0'));
                  echo $cpt;?>
                </td>
              </tr>
              <tr>
                <td class="label"></td>
                <td>
                  <button id="disconnectAll" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('disconnectAll'); ?>
                   <script type="dojo/connect" event="onClick" args="evt">                 
                     adminDisconnectAll();
                     return false;
                   </script>
                 </button>
                </td>
              </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td width="200px;" class="label"><?php echo i18n("applicationStatus"). "&nbsp;:&nbsp;";?></td>
                <td width="90%">
                  <?php $statusApp=Parameter::getGlobalParameter('applicationStatus');
                  if (!trim($statusApp)) {$statusApp='Open';}
                  echo i18n('applicationStatus'.$statusApp);
                  ?>
                </td>
              </tr>
              <tr>
                <td class="label"></td>
                <td>
                  <button id="openCloseApp" dojoType="dijit.form.Button" showlabel="true">
                    <?php $operation="Closed";
                    if ($statusApp!='Open') {$operation='Open';}
                    echo i18n('setApplicationTo'.$operation); ?>
                   <script type="dojo/connect" event="onClick" args="evt">                 
                     adminSetApplicationTo('<?php echo $operation;?>');
                     return false;
                   </script>
                 </button>
                </td>
              </tr>
              <tr>
                <td class="label"><?php echo i18n("closedMessage"). "&nbsp;:&nbsp;";?></td>
                <td>
                  <textarea dojoType="dijit.form.Textarea"
                    name="msgClosedApplication" id="msgClosedApplication"
                    style="width:99%;"
                    maxlength="4000"
                    class="input"><?php echo Parameter::getGlobalParameter('msgClosedApplication');?></textarea>
                </td>
              </tr>  
              
            </table></div><br/>    
          </td>
          <td style="width:10px">&nbsp;</td>
          <td style="width:49%;vertical-align:top;">
            <?php $titlePane="Admin_dbMaintenance"; ?> 
            <div dojoType="dijit.TitlePane"
             open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
             id="<?php echo $titlePane;?>" 
             onHide="saveCollapsed('<?php echo $titlePane;?>');"
             onShow="saveExpanded('<?php echo $titlePane;?>');"
             title="<?php echo i18n('dbMaintenance');?>">
            <table style="width:100%;">
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("closeEmails"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display" width="90%">
                  <?php echo i18n('sentSinceMore');?>&nbsp;
                  <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="7"
                    name="closeMailDays" id="closeMailDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="closeEmails" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('close'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('close','Mail');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("deleteEmails"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display">
                  <?php echo i18n('sentSinceMore');?>&nbsp;
                   <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="30"
                    name="deleteMailDays" id="deleteMailDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="deleteEmails" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('deleteButton'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('delete','Mail');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
                       <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("closeAlerts"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display">
                  <?php echo i18n('sentSinceMore');?>&nbsp;
                   <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="7"
                    name="closeAlertDays" id="closeAlertDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="closeAlerts" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('close'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('close','Alert');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("deleteAlerts"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display">
                  <?php echo i18n('sentSinceMore');?>&nbsp;
                   <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="30"
                    name="deleteAlertDays" id="deleteAlertDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="deleteAlerts" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('deleteButton'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('delete','Alert');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
              <td class="label" style="width:200px">
                <?php echo i18n("deleteAudit"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display">
                  <?php echo i18n('closedSinceMore');?>&nbsp;
                   <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="30"
                    name="deleteAuditDays" id="deleteAuditDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="deleteAudit" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('deleteButton'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('delete','Audit');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("updateReference"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display">
                  <select dojoType="dijit.form.FilteringSelect" class="input" style="width:200px;"
                    <?php echo autoOpenFilteringSelect();?>
                    name="updateReferenceItem" id="updateReferenceItem" required="true">
                      <option value="*"><?php echo i18n('all')?></option>
                      <?php htmlDrawOptionForReference('idReferencable', null, null, true);?> 
                  </select>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="updateReference" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('operationUpdate'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       item=dijit.byId('updateReferenceItem').get('value');
                       maintenance('updateReference',item);
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
            </table></div>
            <br/>
            <?php $titlePane="Admin_logfileMaintenance"; ?> 
            <div dojoType="dijit.TitlePane"
             open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
             id="<?php echo $titlePane;?>" 
             onHide="saveCollapsed('<?php echo $titlePane;?>');"
             onShow="saveExpanded('<?php echo $titlePane;?>');"
             title="<?php echo i18n('logfileMaintenance');?>">
            <table style="width:100%;">
              <tr>
                <td class="label" style="width:200px">
                  <?php echo i18n("deleteLogfile"). "&nbsp;:&nbsp;";?>
                </td>
                <td class="display" width="90%">
                  <?php echo i18n('olderThan');?>&nbsp;
                  <div dojoType="dijit.form.NumberTextBox" constraints="{min:0,max:999}"
                    style="width:30px;"
                    value="7"
                    name="deleteLogfileDays" id="deleteLogfileDays">
                  </div>
                  &nbsp;<?php echo i18n('days');?>
                </td>
              </tr>
              <tr>
                <td></td>
                <td>
                  <button id="deleteLogfile" dojoType="dijit.form.Button" showlabel="true">
                    <?php echo i18n('deleteButton'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       maintenance('delete','Logfile');
                       return false;
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
              <tr>
                <td class="label" style="width:200px"><?php echo i18n('showLogfiles'). "&nbsp;:&nbsp;";?></td>
                <td>
                 <button id="showLogfile" dojoType="dijit.form.Button" showlabel="true" class="">
                    <?php echo i18n('showLogfiles'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       loadDialog('dialogLogfiles',null,true);
                       //loadContent("../view/logfiles.php","centerDiv");
                     </script>
                 </button>
                 <br/>
                 <button id="showLastLogfile" dojoType="dijit.form.Button" showlabel="true" class="">
                    <?php echo i18n('showLastLogfile'); ?>
                     <script type="dojo/connect" event="onClick" args="evt">
                       showLogfile('last');
                     </script>
                 </button>
                </td>
              </tr>
              <tr><td colspan="2">&nbsp;</td></tr>
            </table>
            </div>
          </td>
        </tr>
      </table>
    </form>
  </div>
</div>