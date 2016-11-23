/*******************************************************************************
 * COPYRIGHT NOTICE *
 * 
 * Copyright 2009-2016 ProjeQtOr - Pascal BERNARD - support@projeqtor.org Contributors : -
 * 
 * This file is part of ProjeQtOr.
 * 
 * ProjeQtOr is free software: you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * ProjeQtOr is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 * 
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org
 * 
 * DO NOT REMOVE THIS NOTICE **
 ******************************************************************************/

// ============================================================================
// All specific ProjeQtOr functions and variables for Dialog Purpose
// This file is included in the main.php page, to be reachable in every context
// ============================================================================
// =============================================================================
// = Variables (global)
// =============================================================================
var filterType="";
var closeFilterListTimeout;
var openFilterListTimeout;
var closeFavoriteReportsTimeout;
var openFavoriteReportsTimeout=null;
var popupOpenDelay=200;
var closeMenuListTimeout=null;
var openMenuListTimeout=null;
var menuListAutoshow=false;
// =============================================================================
// = Wait spinner
// =============================================================================

var waitingForReply=false;
/**
 * ============================================================================
 * Shows a wait spinner
 * 
 * @return void
 */
function showWait() {
  if (dojo.byId("wait")) {
    showField("wait");
    waitingForReply=true;
  } else {
    showField("waitLogin");
  }
}

/**
 * ============================================================================
 * Hides a wait spinner
 * 
 * @return void
 */
function hideWait() {
  waitingForReply=false;
  hideField("wait");
  hideField("waitLogin");
  if (top.dijit.byId("dialogInfo")) {
    top.dijit.byId("dialogInfo").hide();
  }
}

// =============================================================================
// = Generic field visibility properties
// =============================================================================

/**
 * ============================================================================
 * Setup the style properties of a field to set it visible (show it)
 * 
 * @param field
 *          the name of the field to be set
 * @return void
 */
function showField(field) {
  var dest=dojo.byId(field);
  if (dijit.byId(field)) {
    dest=dijit.byId(field).domNode;
  }
  if (dest) {
    dojo.style(dest, {
      visibility : 'visible'
    });
    dojo.style(dest, {
      display : 'inline'
    });
    // dest.style.visibility = 'visible';
    // dest.style.display = 'inline';
  }
}

/**
 * ============================================================================
 * Setup the style properties of a field to set it invisible (hide it)
 * 
 * @param field
 *          the name of the field to be set
 * @return void
 */
function hideField(field) {
  var dest=dojo.byId(field);
  if (dijit.byId(field)) {
    dest=dijit.byId(field).domNode;
  }
  if (dest) {
    dojo.style(dest, {
      visibility : 'hidden'
    });
    dojo.style(dest, {
      display : 'none'
    });
    // dest.style.visibility = 'hidden';
    // dest.style.display = 'none';
  }
}

function protectDblClick(widget){
  if (!widget.id) return;
  disableWidget(widget.id);
  setTimeout("enableWidget('"+widget.id+"');",300);
}
// =============================================================================
// = Message boxes
// =============================================================================

/**
 * ============================================================================
 * Display a Dialog Error Message Box
 * 
 * @param msg
 *          the message to display in the box
 * @return void
 */
function showError(msg) {
  top.hideWait();
  if (top.dojo.byId("dialogErrorMessage")) {
    top.dojo.byId("dialogErrorMessage").innerHTML=msg;
    top.dijit.byId("dialogError").show();
  } else if (dojo.byId('loginResultDiv')) {
    dojo.byId('loginResultDiv').innerHTML=
      '<input type="hidden" id="isLoginPage" name="isLoginPage" value="true" />'
      +'<div class="messageERROR" style="width:100%">'+msg+'</div>';
  } else {
    alert(msg);
  }
}

/**
 * ============================================================================
 * Display a Dialog Information Message Box
 * 
 * @param msg
 *          the message to display in the box
 * @return void
 */
function showInfo(msg,callback) {
  var callbackFunc=function() {};
  if (callback) { 
    callbackFunc=callback;
  }
  top.dojo.byId("dialogInfoMessage").innerHTML=msg;
  top.dijit.byId("dialogInfo").acceptCallback=callbackFunc;
  top.dijit.byId("dialogInfo").show();
}

/**
 * ============================================================================
 * Display a Dialog Alert Message Box
 * 
 * @param msg
 *          the message to display in the box
 * @return void
 */
function showAlert(msg,callback) {
  top.hideWait();
  var callbackFunc=function() {};
  if (callback) { 
    callbackFunc=callback;
  }
  top.dojo.byId("dialogAlertMessage").innerHTML=msg;
  top.dijit.byId("dialogAlert").acceptCallback=callbackFunc;
  top.dijit.byId("dialogAlert").show();
}

/**
 * ============================================================================
 * Display a Dialog Question Message Box, with Yes/No buttons
 * 
 * @param msg
 *          the message to display in the box
 * @param actionYes
 *          the function to be executed if click on Yes button
 * @param actionNo
 *          the function to be executed if click on No button
 * @return void
 */
function showQuestion(msg, actionYes, ActionNo) {
  dojo.byId("dialogQuestionMessage").innerHTML=msg;
  dijit.byId("dialogQuestion").acceptCallbackYes=actionYes;
  dijit.byId("dialogQuestion").acceptCallbackNo=actionNo;
  dijit.byId("dialogQuestion").show();
}

/**
 * ============================================================================
 * Display a Dialog Confirmation Message Box, with OK/Cancel buttons NB : no
 * action on Cancel click
 * 
 * @param msg
 *          the message to display in the box
 * @param actionOK
 *          the function to be executed if click on OK button
 * @return void
 */
function showConfirm(msg, actionOK) {
  dojo.byId("dialogConfirmMessage").innerHTML=msg;
  dijit.byId("dialogConfirm").acceptCallback=actionOK;
  dijit.byId("dialogConfirm").show();
}

/**
 * ============================================================================
 * Display a About Box
 * 
 * @param msg
 *          the message of the about box (must be passed here because built in
 *          php)
 * @return void
 */
function showAbout(msg) {
  showInfo(msg);
}

// =============================================================================
// = Print
// =============================================================================

/**
 * ============================================================================
 * Display a Dialog Print Preview Box
 * 
 * @param page
 *          the page to display
 * @param forms
 *          the form containing the data to send to the page
 * @return void
 */
function showPrint(page, context, comboName, outMode, orientation) {

  // dojo.byId('printFrame').style.width= 1000 + 'px';
  showWait();
  quitConfirmed=true;
  noDisconnect=true;
  if (!orientation)
    orientation='L';
  if (!outMode)
    outMode='html';
  var printInNewWin=printInNewWindow;
  if (outMode == "pdf") {
    printInNewWin=pdfInNewWindow;
  }
  if (outMode == "csv") {
    printInNewWin=true;
  }
  if (outMode == "mpp") {
    printInNewWin=true;
  }
  if (context=='favorite') {
    printInNewWin=false;
  }
  if (!printInNewWin) {
    dijit.byId("dialogPrint").show();
  }
  cl='';
  if (dojo.byId('objectClass')) {
    cl=dojo.byId('objectClass').value;
  }
  id='';
  if (dojo.byId('objectId')) {
    id=dojo.byId('objectId').value;
  }
  var params="&orientation=" + orientation;
  dojo.byId("sentToPrinterDiv").style.display='block';
  if (outMode) {
    params+="&outMode=" + outMode;
    if (outMode == 'pdf') {
      dojo.byId("sentToPrinterDiv").style.display='none';
    }
  }
  if (context == 'list') {
    if (dijit.byId("listShowIdle")) {
      if (dijit.byId("listShowIdle").get('checked')) {
        params+="&idle=true";
      }
    }
    if (dijit.byId("listIdFilter")) {
      if (dijit.byId("listIdFilter").get('value')) {
        params+="&listIdFilter="
            + encodeURIComponent(dijit.byId("listIdFilter").get('value'));
      }
    }
    if (dijit.byId("listNameFilter")) {
      if (dijit.byId("listNameFilter").get('value')) {
        params+="&listNameFilter="
            + encodeURIComponent(dijit.byId("listNameFilter").get('value'));
      }
    }
    if (dijit.byId("listTypeFilter")) {
      if (trim(dijit.byId("listTypeFilter").get('value'))) {
        params+="&objectType="
            + encodeURIComponent(dijit.byId("listTypeFilter").get('value'));
      }
    }
    if (dijit.byId("listClientFilter")) {
      if (trim(dijit.byId("listClientFilter").get('value'))) {
        params+="&objectClient="
            + encodeURIComponent(dijit.byId("listClientFilter").get('value'));
      }
    }
    if (dijit.byId("listElementableFilter")) {
      if (trim(dijit.byId("listElementableFilter").get('value'))) {
        params+="&objectElementable="
            + encodeURIComponent(dijit.byId("listElementableFilter").get('value'));
      }
    }
  } else if (context == 'planning') {
    if (dijit.byId("startDatePlanView").get('value')) {
      params+="&startDate="
          + encodeURIComponent(formatDate(dijit.byId("startDatePlanView").get(
              "value")));
      params+="&endDate="
          + encodeURIComponent(formatDate(dijit.byId("endDatePlanView").get(
              "value")));
      params+="&format=" + g.getFormat();
      if (dijit.byId('listShowIdle').get('checked')) {
        params+="&idle=true";
      }
      if (dijit.byId('showWBS').checked) {
        params+="&showWBS=true";
      }
      if (dijit.byId('listShowResource')) {
        if (dijit.byId('listShowResource').checked) {
          params+="&showResource=true";
        }
      }
      if (dijit.byId('listShowLeftWork')) {
        if (dijit.byId('listShowLeftWork').checked) {
          params+="&showWork=true";
        }
      }
      if (dijit.byId('listShowProject')) {
        if (dijit.byId('listShowProject').checked) {
          params+="&showProject=true";
        }
      }
    }
  } else if (context == 'report' || context=='favorite') {
    if (context == 'report' ) { 
      var frm=dojo.byId('reportForm'); 
    } else {
      var frm=dojo.byId('favoriteForm'); 
    }
    frm.action="../view/print.php";
    if (outMode) {
      frm.page.value=page;
      dojo.byId('outMode').value=outMode;
    } else {
      dojo.byId('outMode').value='';
    }
    if (printInNewWin) {
      frm.target='#';
    } else {
      frm.target='printFrame';
    }
    frm.submit();
    hideWait();
    quitConfirmed=false;
    noDisconnect=false;
    return;
  } else if (context == 'imputation') {
    var frm=dojo.byId('listForm');
    frm.action="../view/print.php?orientation=" + orientation;
    if (printInNewWin) {
      frm.target='#';
    } else {
      frm.target='printFrame';
    }
    if (outMode) {
      dojo.byId('outMode').value=outMode;
    } else {
      dojo.byId('outMode').value='';
    }
    frm.submit();
    hideWait();
    quitConfirmed=false;
    noDisconnect=false;
    return;
  }
  var grid=dijit.byId('objectGrid');
  if (grid) {
    var sortWay=(grid.getSortAsc()) ? 'asc' : 'desc';
    var sortIndex=grid.getSortIndex();
    if (sortIndex >= 0) {
      params+="&sortIndex=" + sortIndex;
      params+="&sortWay=" + sortWay;
    }
  }
  if (outMode == "csv") {
    dojo.byId("printFrame").src="print.php?print=true&page=" + page
        + "&objectClass=" + cl + "&objectId=" + id + params;
    hideWait();
  } else if (printInNewWin) {
    var newWin=window.open("print.php?print=true&page=" + page
        + "&objectClass=" + cl + "&objectId=" + id + params);
    hideWait();
  } else {
    dojo.byId("printFrame").src="print.php?print=true&page=" + page
        + "&objectClass=" + cl + "&objectId=" + id + params;
    if (outMode == 'pdf') {
      hideWait();
    } 
  }
  quitConfirmed=false;
  noDisconnect=false;
}

function sendFrameToPrinter() {
  dojo.byId("sendToPrinter").blur();
  window.frames['printFrame'].focus();
  window.frames['printFrame'].print();
  dijit.byId('dialogPrint').hide();
  return true;
}
// =============================================================================
// = Detail (from combo)
// =============================================================================

function showDetailDependency() {
  var depType=dijit.byId('dependencyRefTypeDep').get("value");
  if (depType) {
    var dependable=dependableArray[depType];
    var canCreate=0;
    if (canCreateArray[dependable] == "YES") {
      canCreate=1;
    }
    showDetail('dependencyRefIdDep', canCreate, dependable, true);

  } else {
    showInfo(i18n('messageMandatory', new Array(i18n('linkType'))));
  }
}

function showDetailLink() {
  var linkType=dijit.byId('linkRef2Type').get("value");
  if (linkType) {
    var linkable=linkableArray[linkType];
    var canCreate=0;
    if (canCreateArray[linkable] == "YES") {
      canCreate=1;
    }
    showDetail('linkRef2Id', canCreate, linkable, true);

  } else {
    showInfo(i18n('messageMandatory', new Array(i18n('linkType'))));
  }
}

function showDetailApprover() {
  var canCreate=0;
  if (canCreateArray['Resource'] == "YES") {
    canCreate=1;
  }
  showDetail('approverId', canCreate, 'Resource', true);
}

function showDetailOrigin() {
  var originType=dijit.byId('originOriginType').get("value");
  if (originType) {
    var originable=originableArray[originType];
    var canCreate=0;
    if (canCreateArray[originable] == "YES") {
      canCreate=1;
    }
    showDetail('originOriginId', canCreate, originable);

  } else {
    showInfo(i18n('messageMandatory', new Array(i18n('originType'))));
  }
}

function showDetail(comboName, canCreate, objectClass, multiSelect, objectId) {
  var contentWidget=dijit.byId("comboDetailResult");
  
  dojo.byId("canCreateDetail").value=canCreate;
  if (contentWidget) {
    contentWidget.set('content', '');
  }
  if (!objectClass) {
    objectClass=comboName.substring(2);
  }
  dojo.byId('comboName').value=comboName;
  dojo.byId('comboClass').value=objectClass;
  dojo.byId('comboMultipleSelect').value=(multiSelect) ? 'true' : 'false';
  dijit.byId('comboDetailResult').set('content',null);
  var val=null;
  if (dijit.byId(comboName)) {
    val=dijit.byId(comboName).get('value');
  } else if(dojo.byId(comboName)) {
    val=dojo.byId(comboName).value;
  }
  if (objectId) {
    if (objectId=='new') {
      cl=objectClass;
      id=null;
      window.frames['comboDetailFrame'].document.body.innerHTML='<i>'
          + i18n("messagePreview") + '</i>';
      dijit.byId("dialogDetail").show();
      frames['comboDetailFrame'].location.href="print.php?print=true&page=preparePreview.php";
      newDetailItem(objectClass);
    } else {
      cl=objectClass;
      id=objectId;
      window.frames['comboDetailFrame'].document.body.innerHTML='<i>'
          + i18n("messagePreview") + '</i>';
      dijit.byId("dialogDetail").show();
      frames['comboDetailFrame'].location.href="print.php?print=true&page=preparePreview.php";
      gotoDetailItem(objectClass,objectId);
    }
    
  } else if (!val || val == "" || val == " ") {
    cl=objectClass;
    window.frames['comboDetailFrame'].document.body.innerHTML='<i>'
        + i18n("messagePreview") + '</i>';
    dijit.byId("dialogDetail").show();
    displaySearch(cl);
  } else {
    cl=objectClass;
    id=val;
    window.frames['comboDetailFrame'].document.body.innerHTML='<i>'
        + i18n("messagePreview") + '</i>';
    dijit.byId("dialogDetail").show();
    displayDetail(cl, id);
  }
  dojo.connect(dijit.byId("dialogDetail"),"onhide", 
    function(){
      // nothing to do;
    });
}

function displayDetail(objClass, objId) {
  showWait();
  showField('comboSearchButton');
  hideField('comboSelectButton');
  hideField('comboNewButton');
  hideField('comboSaveButton');
  showField('comboCloseButton');
  dijit.byId('comboDetailResult').set('content',null);
  frames['comboDetailFrame'].location.href="print.php?print=true&page=objectDetail.php&objectClass="
      + objClass + "&objectId=" + objId + "&detail=true";
}

function directDisplayDetail(objClass, objId) {
  showWait();
  hideField('comboSearchButton');
  hideField('comboSelectButton');
  hideField('comboNewButton');
  hideField('comboSaveButton');
  showField('comboCloseButton');
  dijit.byId('comboDetailResult').set('content',null);
  window.frames['comboDetailFrame'].document.body.innerHTML='<i>'
    + i18n("messagePreview") + '</i>';
  dijit.byId("dialogDetail").show();
  frames['comboDetailFrame'].location.href="print.php?print=true&page=objectDetail.php&objectClass="
    + objClass + "&objectId=" + objId + "&detail=true";
}

function selectDetailItem(selectedValue, lastSavedName) {
  var idFldVal="";
  if (selectedValue) {
    idFldVal=selectedValue;
  } else {
    var idFld=frames['comboDetailFrame'].dojo.byId('comboDetailId');
    var comboGrid=frames['comboDetailFrame'].dijit.byId('objectGrid');
    if (comboGrid) {
      idFldVal="";
      var items=comboGrid.selection.getSelected();
      dojo.forEach(items, function(selectedItem) {
        if (selectedItem !== null) {
          idFldVal+=(idFldVal != "") ? '_' : '';
          idFldVal+=parseInt(selectedItem.id, 10) + '';
        }
      });
    } else {
      if (!idFld) {
        showError('error : comboDetailId not defined');
        return;
      }
      idFldVal=idFld.value;
    }
    if (!idFldVal) {
      showAlert(i18n('noItemSelected'));
      return;
    }
  }
  var comboName=dojo.byId('comboName').value;
  var combo=dijit.byId(comboName);
  var comboClass=dojo.byId('comboClass').value;
  crit=null;
  critVal=null;
  if (comboClass == 'Activity' || comboClass == 'Resource'
      || comboClass == 'Ticket') {
    prj=dijit.byId('idProject');
    if (prj) {
      crit='idProject';
      critVal=prj.get("value");
    }
  }
  if (comboName != 'idStatus' && comboName != 'idProject') {
    if (combo) {
      refreshList('id' + comboClass, crit, critVal, idFldVal, comboName);
    } else {
      if (comboName == 'dependencyRefIdDep') {
        refreshDependencyList(idFldVal);
        setTimeout("dojo.byId('dependencyRefIdDep').focus()", 1000);
        enableWidget('dialogDependencySubmit');
      } else if (comboName == 'linkRef2Id') {
        refreshLinkList(idFldVal);
        setTimeout("dojo.byId('linkRef2Id').focus()", 1000);
        enableWidget('dialogLinkSubmit');
      } else if (comboName == 'productStructureListId') {
        refreshProductStructureList(idFldVal,lastSavedName);
        setTimeout("dojo.byId('productStructureListId').focus()",500);
        enableWidget('dialogProductStructureSubmit');
      } else if (comboName == 'productVersionStructureListId') {
        refreshProductVersionStructureList(idFldVal,lastSavedName);
        setTimeout("dojo.byId('productVersionStructureListId').focus()",500);
        enableWidget('dialogProductVersionStructureSubmit');
      } else if (comboName == 'otherVersionIdVersion') {
        refreshOtherVersionList(idFldVal);
        setTimeout("dojo.byId('otherVersionIdVersion').focus()", 1000);
        enableWidget('dialogOtherVersionSubmit');
      } else if (comboName == 'approverId') {
        refreshApproverList(idFldVal);
        setTimeout("dojo.byId('approverId').focus()", 1000);
        enableWidget('dialogApproverSubmit');
      } else if (comboName == 'originOriginId') {
        refreshOriginList(idFldVal);
        setTimeout("dojo.byId('originOriginId').focus()", 1000);
        enableWidget('dialogOriginSubmit');
      } else if (comboName == 'testCaseRunTestCaseList') {
        refreshTestCaseRunList(idFldVal);
        setTimeout("dojo.byId('testCaseRunTestCaseList').focus()", 1000);
        enableWidget('dialogTestCaseRunSubmit');
      }
    }
  }
  if (combo) {
    combo.set("value", idFldVal);
  }
  hideDetail();
}

function displaySearch(objClass) {
  if (!objClass) {
    // comboName=dojo.byId('comboName').value;
    objClass=dojo.byId('comboClass').value;
  }
  showWait();
  hideField('comboSearchButton');
  showField('comboSelectButton');
  if (dojo.byId("canCreateDetail").value == "1") {
    showField('comboNewButton');
  } else {
    hideField('comboNewButton');
  }
  hideField('comboSaveButton');
  showField('comboCloseButton');
  var multipleSelect=(dojo.byId('comboMultipleSelect').value == 'true') ? '&multipleSelect=true'
      : '';
  top.frames['comboDetailFrame'].location.href="comboSearch.php?objectClass="
      + objClass + "&mode=search" + multipleSelect;
  setTimeout('dijit.byId("dialogDetail").show()', 10);
}

function newDetailItem(objectClass) {
  gotoDetailItem(objectClass);
}
function gotoDetailItem(objectClass,objectId) {
  // comboName=dojo.byId('comboName').value;
  hideField('comboSearchButton');
  var objClass=objectClass;
  if (!objectClass) {
    objClass=dojo.byId('comboClass').value;
    showField('comboSearchButton');
  }
  showWait();
  hideField('comboSelectButton');
  hideField('comboNewButton');
  if (dojo.byId("canCreateDetail").value == "1") {
    showField('comboSaveButton');
  } else {
    hideField('comboSaveButton');
  }
  showField('comboCloseButton');
  //contentNode=frames['comboDetailFrame'].dojo.byId('body');
  //destinationWidth=dojo.style(contentNode, "width");
  destinationWidth=frames['comboDetailFrame'].document.body.offsetWidth
  page="comboSearch.php";
  page+="?objectClass=" + objClass;
  if (objectId) {
    page+="&objectId="+objectId;
    page+="&mode=new";    
  } else {
    page+="&objectId=0";
    page+="&mode=new";
  }
  page+="&destinationWidth=" + destinationWidth;
  top.frames['comboDetailFrame'].location.href=page;
  setTimeout('dijit.byId("dialogDetail").show()', 10);
}

function saveDetailItem() {
  var comboName=dojo.byId('comboName').value;
  var formVar=frames['comboDetailFrame'].dijit.byId("objectForm");
  if (!formVar) {
    showError(i18n("errorSubmitForm", new Array(page, destination, formName)));
    return;
  }
  for(name in frames['comboDetailFrame'].CKEDITOR.instances) {
    frames['comboDetailFrame'].CKEDITOR.instances[name].updateElement();
  }
  // validate form Data
  if (formVar.validate()) {
    showWait();
    frames['comboDetailFrame'].dojo
        .xhrPost({
          url : "../tool/saveObject.php?comboDetail=true",
          form : "objectForm",
          handleAs : "text",
          load : function(data, args) {
            var contentWidget=dijit.byId("comboDetailResult");
            if (!contentWidget) {
              return;
            }
            contentWidget.set('content', data);
            checkDestination("comboDetailResult");
            var lastOperationStatus=top.dojo
                .byId('lastOperationStatusComboDetail');
            var lastOperation=top.dojo.byId('lastOperationComboDetail');
            var lastSaveId=top.dojo.byId('lastSaveIdComboDetail');
            if (lastOperationStatus.value == "OK") {
              var currentItemName="";
              if (frames['comboDetailFrame'].dijit.byId("name")) {
                currentItemName=frames['comboDetailFrame'].dijit.byId("name").get("value");
              }
              selectDetailItem(lastSaveId.value,currentItemName);
            }
            hideWait();
          },
          error : function() {
            hideWait();
          }
        });

  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function hideDetail() {
  hideField('comboSearchButton');
  hideField('comboSelectButton');
  hideField('comboNewButton');
  hideField('comboSaveButton');
  hideField('comboCloseButton');
  frames['comboDetailFrame'].location.href="preparePreview.php";
  dijit.byId("dialogDetail").hide();
  if (dijit.byId(dojo.byId('comboName').value)) {
    dijit.byId(dojo.byId('comboName').value).focus();
  }
}

//=============================================================================
//= Copy Object
//=============================================================================

/**
 * Display a copy object Box
 * 
 */
function copyObjectBox(copyType) {
  var callBack=function() {

  };
  if(copyType=="copyObjectTo"){
    callBack=function() {
      dojo.byId('copyClass').value=dojo.byId("objectClass").value;
      dojo.byId('copyId').value=dojo.byId("objectId").value;
      /*for ( var i in copyableArray) {
        if (copyableArray[i] == dojo.byId("objectClass").value) {
          dijit.byId('copyToClass').set('value', i);
        }
      }*/
      copyObjectToShowStructure();
    };
  }else if(copyType=="copyProject"){
    callBack=function() {
      dojo.byId('copyProjectId').value=dojo.byId("objectId").value;
      dijit.byId('copyProjectToName').set('value', dijit.byId('name').get('value'));
      // dijit.byId('copyToOrigin').set('checked','checked');
      dijit.byId('copyProjectToType').reset();
      if (dijit.byId('idProjectType') && dojo.byId('codeType')
          && dojo.byId('codeType').value != 'TMP') {
        var runModif="dijit.byId('copyProjectToType').set('value',dijit.byId('idProjectType').get('value'))";
        setTimeout(runModif, 1);
      }
    };
  }
  var params="&objectClass="+dojo.byId("objectClass").value;
  params+="&objectId="+dojo.byId("objectId").value;   
  params+="&copyType="+copyType;   
  loadDialog('dialogCopy', callBack, true, params, false);
}

//=============================================================================
//= Planning PDF
//=============================================================================

/**
* Display a planning PDF Box
* 
*/
function planningPDFBox(copyType) { 
  loadDialog('dialogPlanningPdf', null, true, "", false);
}

// =============================================================================
// = Notes
// =============================================================================

/**
 * Display a add note Box
 * 
 */
function addNote() {
  if (dijit.byId("noteToolTip")) {
    dijit.byId("noteToolTip").destroy();
    dijit.byId("noteNote").set("class", "");
  }
  var callBack=function() {
    var editorType=dojo.byId("noteEditorType").value;
    if (editorType=="CK") { // CKeditor type
      ckEditorReplaceEditor("noteNote",999);
    } else if (editorType=="text") {
      dijit.byId("noteNote").focus();
    } else if (dijit.byId("noteNoteEditor")) { // Dojo type editor
      dijit.byId("noteNoteEditor").set("class", "input");
      dijit.byId("noteNoteEditor").focus();
    }
  };
  var params="&objectClass="+dojo.byId("objectClass").value;
  params+="&objectId="+dojo.byId("objectId").value;
  params+="&noteId="; // Null    
  loadDialog('dialogNote', callBack, true, params, true);

}

function noteSelectPredefinedText(idPrefefinedText) {
  dojo.xhrGet({
    url : '../tool/getPredefinedText.php?id=' + idPrefefinedText,
    handleAs : "text",
    load : function(data) {
      var editorType=dojo.byId("noteEditorType").value;
      if (editorType=="CK") { // CKeditor type
        CKEDITOR.instances['noteNote'].setData(data);
      } else if (editorType=="text") { 
        dijit.byId('noteNote').set('value', data);
        dijit.byId('noteNote').focus();
      } else if (dijit.byId('noteNoteEditor')) {
        dijit.byId('noteNote').set('value', data);
        dijit.byId('noteNoteEditor').set('value', data);
        dijit.byId("noteNoteEditor").focus();
      } 
    }
  });
}
/**
 * Display a edit note Box
 * 
 */
function editNote(noteId, privacy) {
  if (dijit.byId("noteToolTip")) {
    dijit.byId("noteToolTip").destroy();
    dijit.byId("noteNote").set("class", "");
  }
  var callBack=function() {
    //dijit.byId('notePrivacyPublic').set('checked', 'true');
    var editorType=dojo.byId("noteEditorType").value;
    if (editorType=="CK") { // CKeditor type
      ckEditorReplaceEditor("noteNote",999);
    } else if (editorType=="text") { 
      dijit.byId("noteNote").focus();
    } else if (dijit.byId("noteNoteEditor")) { // Dojo type editor
      dijit.byId("noteNoteEditor").set("class", "input");
      dijit.byId("noteNoteEditor").focus();
    } 
  };
  var params="&objectClass="+dojo.byId("objectClass").value;
  params+="&objectId="+dojo.byId("objectId").value;
  params+="&noteId="+noteId;    
  loadDialog('dialogNote', callBack, true, params, true);
}

/**
 * save a note (after addNote or editNote)
 * 
 */
function saveNote() {
  var editorType=dojo.byId("noteEditorType").value;
  if (editorType=="CK") {
    noteEditor=CKEDITOR.instances['noteNote'];
    noteEditor.updateElement();
    var tmpCkEditor=noteEditor.document.getBody().getText();
    if (tmpCkEditor.trim()=="") {
      var msg=i18n('messageMandatory', new Array(i18n('Note')));
      noteEditor.focus();
      showAlert(msg);
      return;
    }
  } else if (editorType=="CK") {
    if (dijit.byId("noteNote").getValue() == '') {
      dijit.byId("noteNote").set("class", "input required");
      var msg=i18n('messageMandatory', new Array(i18n('Note')));
      dijit.byId("noteNote").focus();
      showAlert(msg);
      return;
    }
  } else if (dijit.byId("noteNoteEditor")) {
    if (dijit.byId("noteNote").getValue() == '') {
      dijit.byId("noteNoteEditor").set("class", "input required");
      var msg=i18n('messageMandatory', new Array(i18n('Note')));
      dijit.byId("noteNoteEditor").focus();
      dojo.byId("noteNoteEditor").focus();
      showAlert(msg);
      return;
    }
  } 
  loadContent("../tool/saveNote.php", "resultDiv", "noteForm", true, 'note');
  dijit.byId('dialogNote').hide();
}


/**
 * Display a delete note Box
 * 
 */
function removeNote(noteId) {
  var param="?noteId="+noteId;
  param+="&noteRefType="+dojo.byId("objectClass").value;
  param+="&noteRefId="+dojo.byId("objectId").value;
  actionOK=function() {
    loadContent("../tool/removeNote.php"+param, "resultDiv", "noteForm", true, 'note');
  };
  msg=i18n('confirmDelete', new Array(i18n('Note'), noteId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = Attachments
// =============================================================================

/**
 * Display an add attachment Box
 * 
 */
function addAttachment(attachmentType) {
  content=dijit.byId('dialogAttachment').get('content');
  if (content == "") {
    callBack=function() {
      dojo.connect(dijit.byId("attachmentFile"), "onComplete", function(dataArray) {
        saveAttachmentAck(dataArray);
      });
      dojo.connect(dijit.byId("attachmentFile"), "onProgress", function(data) {
        saveAttachmentProgress(data);
      });
      dojo.connect(dijit.byId("attachmentFile"), "onError", function(evt) {
        hideWait();
        showError(i18n("uploadUncomplete"));
      });
      addAttachment(attachmentType);
      if (isHtml5()) {
        dijit.byId('attachmentFile').addDropTarget(dojo.byId('attachmentFileDropArea'));
      }
    };
    loadDialog('dialogAttachment', callBack);
    return;
  }
  dojo.byId("attachmentId").value="";
  dojo.byId("attachmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("attachmentRefId").value=dojo.byId("objectId").value;
  dojo.byId("attachmentType").value=attachmentType;
  dojo.byId('attachmentFileName').innerHTML="";
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  if (attachmentType == 'file') {
    if (dijit.byId("attachmentFile")) {
      dijit.byId("attachmentFile").reset();
      if (!isHtml5()) {
        enableWidget('dialogAttachmentSubmit');
      } else {
        disableWidget('dialogAttachmentSubmit');
      }
    }
    dojo.style(dojo.byId('dialogAttachmentFileDiv'), {
      display : 'block'
    });
    dojo.style(dojo.byId('dialogAttachmentLinkDiv'), {
      display : 'none'
    });
  } else {
    dijit.byId("attachmentLink").set('value', null);
    dojo.style(dojo.byId('dialogAttachmentFileDiv'), {
      display : 'none'
    });
    dojo.style(dojo.byId('dialogAttachmentLinkDiv'), {
      display : 'block'
    });
    enableWidget('dialogAttachmentSubmit');
  }
  dijit.byId("attachmentDescription").set('value', null);
  dijit.byId("dialogAttachment").set('title', i18n("dialogAttachment"));
  dijit.byId('attachmentPrivacyPublic').set('checked', 'true');
  dijit.byId("dialogAttachment").show();
}

function changeAttachment(list) {
  if (list.length > 0) {
    htmlList="";
    for (var i=0; i < list.length; i++) {
      htmlList+=list[i]['name'] + '<br/>';
    }
    dojo.byId('attachmentFileName').innerHTML=htmlList;
    enableWidget('dialogAttachmentSubmit');
    dojo.byId('attachmentFile').height="200px";
  } else {
    dojo.byId('attachmentFileName').innerHTML="";
    disableWidget('dialogAttachmentSubmit');
    dojo.byId('attachmentFile').height="20px";
  }
}

/**
 * save an Attachment
 * 
 */
function saveAttachment(direct) {
  // disableWidget('dialogAttachmentSubmit');
  if (!isHtml5()) {
    if (dojo.isIE && dojo.isIE<=8) {
      dojo.byId('attachmentForm').submit();
    }
    showWait();
    dijit.byId('dialogAttachment').hide();
    return true;
  }
  if (dojo.byId("attachmentType")
      && dojo.byId("attachmentType").value == 'file'
      && dojo.byId('attachmentFileName')
      && dojo.byId('attachmentFileName').innerHTML == "") {
    return false;
  }
  if (direct) {
    if (dijit.byId("attachmentFileDirect")) {
      if (dijit.byId("attachmentFileDirect").getFileList().length > 20) {
        showAlert(i18n('uploadLimitNumberFiles'));
        return false;
      }
    }
  } else {
    if (dijit.byId("attachmentFile")) {
      if (dijit.byId("attachmentFile").getFileList().length > 20) {
        showAlert(i18n('uploadLimitNumberFiles'));
        return false;
      }
    }
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'block'
  });
  showWait();
  dijit.byId('dialogAttachment').hide();
  return true;
}

/**
 * Acknowledge the attachment save
 * 
 * @return void
 */
function saveAttachmentAck(dataArray) {
  if (!isHtml5()) {
    resultFrame=document.getElementById("resultPost");
    resultText=resultPost.document.body.innerHTML;
    dojo.byId('resultAck').value=resultText;
    loadContent("../tool/ack.php", "resultDiv", "attachmentAckForm", true,
        'attachment');
    return;
  }
  dijit.byId('dialogAttachment').hide();
  if (dojo.isArray(dataArray)) {
    result=dataArray[0];
  } else {
    result=dataArray;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  dojo.byId('resultAck').value=result.message;
  loadContent("../tool/ack.php", "resultDiv", "attachmentAckForm", true,
      'attachment');
}

function saveAttachmentProgress(data) {
  done=data.bytesLoaded;
  total=data.bytesTotal;
  if (total) {
    progress=done / total;
  }
  // dojo.style(dojo.byId('downloadProgress'), {display:'block'});
  dijit.byId('downloadProgress').set('value', progress);
}
/**
 * Display a delete Attachment Box
 * 
 */
function removeAttachment(attachmentId) {
  content=dijit.byId('dialogAttachment').get('content');
  if (content == "") {
    callBack=function() {
      dojo.connect(dijit.byId("attachmentFile"), "onComplete", function(
          dataArray) {
        saveAttachmentAck(dataArray);
      });
      dojo.connect(dijit.byId("attachmentFile"), "onProgress", function(data) {
        saveAttachmentProgress(data);
      });
      dijit.byId('dialogAttachment').hide();
      removeAttachment(attachmentId);
    };
    loadDialog('dialogAttachment', callBack);
    return;
  }
  dojo.byId("attachmentId").value=attachmentId;
  dojo.byId("attachmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("attachmentRefId").value=dojo.byId("objectId").value;
  actionOK=function() {
    loadContent("../tool/removeAttachment.php", "resultDiv", "attachmentForm",
        true, 'attachment');
  };
  msg=i18n('confirmDelete', new Array(i18n('Attachment'), attachmentId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = Links
// =============================================================================

/**
 * Display a add link Box
 * 
 */
var noRefreshLink=false;
function addLink(classLink, defaultLink) {
  loadDialog('dialogLink',function(){
    noRefreshLink=true;
    if (checkFormChangeInProgress()) {
      showAlert(i18n('alertOngoingChange'));
      return;
    }
    var objectClass=dojo.byId("objectClass").value;
    var objectId=dojo.byId("objectId").value;
    var message=i18n("dialogLink");
    dojo.byId("linkId").value="";
    dojo.byId("linkRef1Type").value=objectClass;
    dojo.byId("linkRef1Id").value=objectId;
    dojo.style(dojo.byId('linkDocumentVersionDiv'), {
      display : 'none'
    });
    dijit.byId("linkDocumentVersion").reset();
    if (classLink) {
      dojo.byId("linkFixedClass").value=classLink;
      message=i18n("dialogLinkRestricted", new Array(i18n(objectClass), objectId,
          i18n(classLink)));
      dijit.byId("linkRef2Type").setDisplayedValue(i18n(classLink));
      lockWidget("linkRef2Type");
      // var url="../tool/dynamicListLink.php"
      // + "?linkRef2Type="+dojo.byId("linkRef2Type").value
      // + "&linkRef1Type="+objectClass
      // + "&linkRef1Id="+objectId;
      // loadContent(url, "dialogLinkList", null, false);
      noRefreshLink=false;
      refreshLinkList();
    } else {
      dojo.byId("linkFixedClass").value="";
      if (defaultLink) {
        dijit.byId("linkRef2Type").set('value', defaultLink);
      } else {
        dijit.byId("linkRef2Type").reset();
      }
      message=i18n("dialogLinkExtended", new Array(i18n(objectClass), objectId));
      unlockWidget("linkRef2Type");
      noRefreshLink=false;
      refreshLinkList();
    }
  
    // dojo.byId("linkRef2Id").value='';
    dijit.byId("dialogLink").set('title', message);
    dijit.byId("linkComment").set('value', '');
    dijit.byId("dialogLink").show();
    disableWidget('dialogLinkSubmit');
  }, true, "", true);
}

function selectLinkItem() {
  var nbSelected=0;
  list=dojo.byId('linkRef2Id');
  if (dojo.byId("linkRef2Type").value == "Document") {
    if (list.options) {
      selected=new Array();
      for (var i=0; i < list.options.length; i++) {
        if (list.options[i].selected) {
          selected.push(list.options[i].value);
          nbSelected++;
        }
      }
      if (selected.length == 1) {
        dijit.byId("linkDocumentVersion").reset();
        refreshList('idDocumentVersion', 'idDocument', selected[0], null,'linkDocumentVersion', false);
        dojo.style(dojo.byId('linkDocumentVersionDiv'), {
          display : 'block'
        });
      } else {
        dojo.style(dojo.byId('linkDocumentVersionDiv'), {
          display : 'none'
        });
        dijit.byId("linkDocumentVersion").reset();
      }
    }
  } else {
    if (list.options) {
      for (var i=0; i < list.options.length; i++) {
        if (list.options[i].selected) {
          nbSelected++;
        }
      }
    }
    dojo.style(dojo.byId('linkDocumentVersionDiv'), {
      display : 'none'
    });
    dijit.byId("linkDocumentVersion").reset();
  }
  if (nbSelected > 0) {
    enableWidget('dialogLinkSubmit');
  } else {
    disableWidget('dialogLinkSubmit');
  }
}

/**
 * Refresh the link list (after update)
 */
function refreshLinkList(selected) {
  if (noRefreshLink)
    return;
  disableWidget('dialogLinkSubmit');
  var url='../tool/dynamicListLink.php';
  if (selected) {
    url+='?selected=' + selected;
  }
  if (!selected) {
    selectLinkItem();
  }
  loadContent(url, 'dialogLinkList', 'linkForm', false);
}

/**
 * save a link (after addLink)
 * 
 */
function saveLink() {
  if (dojo.byId("linkRef2Id").value == "")
    return;
  loadContent("../tool/saveLink.php", "resultDiv", "linkForm", true, 'link');
  dijit.byId('dialogLink').hide();
}

/**
 * Display a delete Link Box
 * 
 */
function removeLink(linkId, refType, refId, refTypeName) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeLink.php?linkId="+linkId+"&linkRef1Type="+dojo.byId("objectClass").value
        +"&linkRef1Id="+dojo.byId("objectId").value+"&linkRef2Type="+refType
        +"&linkRef2Id="+refId, "resultDiv", null, true, 'link');
  };
  if (!refTypeName) {
    refTypeName=i18n(refType);
  }
  msg=i18n('confirmDeleteLink', new Array(refTypeName, refId));
  showConfirm(msg, actionOK);
}

//=============================================================================
//= Product Composition
//=============================================================================

/**
* Display a add link Box
* 
*/
function addProductStructure(way) {
  if (checkFormChangeInProgress()) {
   showAlert(i18n('alertOngoingChange'));
   return;
  }
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  var param="&objectClass="+objectClass+"&objectId="+objectId+"&way="+way;
  loadDialog('dialogProductStructure',null, true, param, true);
}

function refreshProductStructureList(selected,newName) {
  var selectList=dojo.byId('productStructureListId');
  if (selected && selectList) {
    if (newName) {
      var option = document.createElement("option");
      option.text = newName;
      option.value=selected;
      selectList.add(option);
    }
    var ids=selected.split('_');
    for (j=0;j<selectList.options.length;j++) {
      var sel=selectList.options[j].value;
      if (ids.indexOf(sel)>=0) { // Found in selected items
        selectList.options[j].selected='selected';
      }
    }
    selectList.focus()
    enableWidget('dialogProductStructureSubmit');
  }
}
/**
* save a link (after addLink)
* 
*/
function saveProductStructure() {
  if (dojo.byId("productStructureListId").value == "") return;
  loadContent("../tool/saveProductStructure.php", "resultDiv", "productStructureForm", true, 'ProductStructure');
  dijit.byId('dialogProductStructure').hide();
}

/**
* Display a delete Link Box
* 
*/
function removeProductStructure(ProductStructureId, refType, refId, refTypeName) {
  if (checkFormChangeInProgress()) {
   showAlert(i18n('alertOngoingChange'));
   return;
  }
  actionOK=function() {
   loadContent("../tool/removeProductStructure.php?id="+ProductStructureId, "resultDiv", null, true, 'ProductStructure');
  };
  if (!refTypeName) {
   refTypeName=i18n(refType);
  }
  msg=i18n('confirmDeleteLink', new Array(refTypeName, refId));
  showConfirm(msg, actionOK);
}

//=============================================================================
//= Product Version Composition
//=============================================================================

/**
* Display a add link Box
* 
*/
function addProductVersionStructure(way) {
  if (checkFormChangeInProgress()) {
   showAlert(i18n('alertOngoingChange'));
   return;
  }
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  var param="&objectClass="+objectClass+"&objectId="+objectId+"&way="+way;
  loadDialog('dialogProductVersionStructure',null, true, param, true);
}

function refreshProductVersionStructureList(selected,newName) {
  var selectList=dojo.byId('productVersionStructureListId');
  if (selected && selectList) {
    if (newName) {
      var option = document.createElement("option");
      option.text = newName;
      option.value=selected;
      selectList.add(option);
    }
    var ids=selected.split('_');
    for (j=0;j<selectList.options.length;j++) {
      var sel=selectList.options[j].value;
      if (ids.indexOf(sel)>=0) { // Found in selected items
        selectList.options[j].selected='selected';
      }
    }
    selectList.focus()
    enableWidget('dialogProductVersionStructureSubmit');
  }
}
/**
* save a link (after addLink)
* 
*/
function saveProductVersionStructure() {
  if (dojo.byId("productVersionStructureListId").value == "") return;
  loadContent("../tool/saveProductVersionStructure.php", "resultDiv", "productVersionStructureForm", true, 'ProductVersionStructure');
  dijit.byId('dialogProductVersionStructure').hide();
}

/**
* Display a delete Link Box
* 
*/
function removeProductVersionStructure(ProductVersionStructureId, refType, refId, refTypeName) {
  if (checkFormChangeInProgress()) {
   showAlert(i18n('alertOngoingChange'));
   return;
  }
  actionOK=function() {
   loadContent("../tool/removeProductVersionStructure.php?id="+ProductVersionStructureId, "resultDiv", null, true, 'ProductVersionStructure');
  };
  if (!refTypeName) {
   refTypeName=i18n(refType);
  }
  msg=i18n('confirmDeleteLink', new Array(refTypeName, refId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = OtherVersions
// =============================================================================
function addOtherVersion(versionType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  dojo.byId("otherVersionRefType").value=objectClass;
  dojo.byId("otherVersionRefId").value=objectId;
  dojo.byId("otherVersionType").value=versionType;
  refreshOtherVersionList(null);
  dijit.byId("dialogOtherVersion").show();
  disableWidget('dialogOtherVersionSubmit');
}

/**
 * Refresh the link list (after update)
 */
function refreshOtherVersionList(selected) {
  disableWidget('dialogOtherVersionSubmit');
  var url='../tool/dynamicListOtherVersion.php';
  if (selected) {
    url+='?selected=' + selected;
  }
  if (!selected) {
    selectOtherVersionItem();
  }
  loadContent(url, 'dialogOtherVersionList', 'otherVersionForm', false);
}

function selectOtherVersionItem() {
  var nbSelected=0;
  list=dojo.byId('otherVersionIdVersion');
  if (list.options) {
    for (var i=0; i < list.options.length; i++) {
      if (list.options[i].selected) {
        nbSelected++;
      }
    }
  }
  if (nbSelected > 0) {
    enableWidget('dialogOtherVersionSubmit');
  } else {
    disableWidget('dialogOtherVersionSubmit');
  }
}

function saveOtherVersion() {
  if (dojo.byId("otherVersionIdVersion").value == "")
    return;
  loadContent("../tool/saveOtherVersion.php", "resultDiv", "otherVersionForm",
      true, 'otherVersion');
  dijit.byId('dialogOtherVersion').hide();
}

function removeOtherVersion(id, name, type) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("otherVersionId").value=id;
  actionOK=function() {
    loadContent("../tool/removeOtherVersion.php", "resultDiv",
        "otherVersionForm", true, 'otherVersion');
  };
  msg=i18n('confirmDeleteOtherVersion', new Array(name, i18n('col' + type)));
  showConfirm(msg, actionOK);
}

function swicthOtherVersionToMain(id, name, type) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("otherVersionId").value=id;
  // actionOK=function() {loadContent("../tool/switchOtherVersion.php",
  // "resultDiv", "otherVersionForm", true,'otherVersion');};
  // msg=i18n('confirmSwitchOtherVersion',new Array(name, i18n('col'+type)));
  // showConfirm (msg, actionOK);
  loadContent("../tool/switchOtherVersion.php", "resultDiv",
      "otherVersionForm", true, 'otherVersion');
}

function showDetailOtherVersion() {
  var canCreate=0;
  if (canCreateArray['Version'] == "YES") {
    canCreate=1;
  }
  var versionType='Version';
  if (dojo.byId("otherVersionType")) {
    var typeValue=dojo.byId("otherVersionType").value;
    if (typeValue.substr(-16)=='ComponentVersion') versionType='ComponentVersion';
    else if (typeValue.substr(-14)=='ProductVersion') versionType='ProductVersion';
  }
  showDetail('otherVersionIdVersion', canCreate, versionType, true);
}
// =============================================================================
// = Approvers
// =============================================================================

/**
 * Display a add link Box
 * 
 */
function addApprover() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  dojo.byId("approverRefType").value=objectClass;
  dojo.byId("approverRefId").value=objectId;
  refreshApproverList();
  dijit.byId("dialogApprover").show();
  disableWidget('dialogApproverSubmit');
}

function selectApproverItem() {
  var nbSelected=0;
  list=dojo.byId('approverId');
  if (list.options) {
    for (var i=0; i < list.options.length; i++) {
      if (list.options[i].selected) {
        nbSelected++;
      }
    }
  }
  if (nbSelected > 0) {
    enableWidget('dialogApproverSubmit');
  } else {
    disableWidget('dialogApproverSubmit');
  }
}

/**
 * Refresh the Approver list (after update)
 */
function refreshApproverList(selected) {
  disableWidget('dialogApproverSubmit');
  var url='../tool/dynamicListApprover.php';
  if (selected) {
    url+='?selected=' + selected;
  }
  selectApproverItem();
  loadContent(url, 'dialogApproverList', 'approverForm', false);
}

/**
 * save a link (after addLink)
 * 
 */
function saveApprover() {
  if (dojo.byId("approverId").value == "")
    return;
  loadContent("../tool/saveApprover.php", "resultDiv", "approverForm", true,
      'approver');
  dijit.byId('dialogApprover').hide();
}

/**
 * Display a delete Link Box
 * 
 */
function removeApprover(approverId, approverName) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("approverItemId").value=approverId;
  dojo.byId("approverRefType").value=dojo.byId("objectClass").value;
  dojo.byId("approverRefId").value=dojo.byId("objectId").value;
  actionOK=function() {
    loadContent("../tool/removeApprover.php", "resultDiv", "approverForm",
        true, 'approver');
  };
  msg=i18n('confirmDeleteApprover', new Array(approverName));
  showConfirm(msg, actionOK);
}

function approveItem(approverId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  loadContent("../tool/approveItem.php?approverId=" + approverId, "resultDiv",
      null, true, 'approver');
}
// =============================================================================
// = Origin
// =============================================================================

/**
 * Display a add origin Box
 * 
 */
function addOrigin() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  dijit.byId("originOriginType").reset();
  refreshOriginList();
  dojo.byId("originId").value="";
  dojo.byId("originRefType").value=objectClass;
  dojo.byId("originRefId").value=objectId;
  dijit.byId("dialogOrigin").show();
  disableWidget('dialogOriginSubmit');
}

/**
 * Refresh the origin list (after update)
 */
function refreshOriginList(selected) {
  disableWidget('dialogOriginSubmit');
  var url='../tool/dynamicListOrigin.php';
  if (selected) {
    url+='?selected=' + selected;
  }
  loadContent(url, 'dialogOriginList', 'originForm', false);
}

/**
 * save a link (after addLink)
 * 
 */
function saveOrigin() {
  if (dojo.byId("originOriginId").value == "")
    return;
  loadContent("../tool/saveOrigin.php", "resultDiv", "originForm", true,
      'origin');
  dijit.byId('dialogOrigin').hide();
}

/**
 * Display a delete Link Box
 * 
 */
function removeOrigin(id, origType, origId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("originId").value=id;
  dojo.byId("originRefType").value=dojo.byId("objectClass").value;
  dojo.byId("originRefId").value=dojo.byId("objectId").value;
  dijit.byId("originOriginType").set('value', origType);
  dojo.byId("originOriginId").value=origId;
  actionOK=function() {
    loadContent("../tool/removeOrigin.php", "resultDiv", "originForm", true,
        'origin');
  };
  msg=i18n('confirmDeleteOrigin', new Array(i18n(origType), origId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = Assignments
// =============================================================================

/**
 * Display a add Assignment Box
 * 
 */
function addAssignment(unit, rawUnit, hoursPerDay) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var prj=dijit.byId('idProject').get('value');

  /*
   * var datastore =new dojo.data.ItemFileReadStore({ query: {id:'*'}, url:
   * '../tool/jsonList.php?listType=listResourceProject&idProject='+prj,
   * clearOnClose: true }); var store = new dojo.store.DataStore({store:
   * datastore}); //store.query({id:"*"});
   * dijit.byId('assignmentIdResource').set('store',store);
   */
  refreshListSpecific('listResourceProject', 'assignmentIdResource','idProject', prj);
  dijit.byId("assignmentIdResource").reset();

  dojo.byId("assignmentId").value="";
  dojo.byId("assignmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("assignmentRefId").value=dojo.byId("objectId").value;
  dijit.byId("assignmentIdRole").reset();
  dijit.byId("assignmentDailyCost").reset();
  dijit.byId("assignmentRate").set('value', '100');
  dijit.byId("assignmentAssignedWork").set('value', '0');
  dojo.byId("assignmentAssignedWorkInit").value='0';
  dijit.byId("assignmentRealWork").set('value', '0');
  dijit.byId("assignmentLeftWork").set('value', '0');
  dojo.byId("assignmentLeftWorkInit").value='0';
  dijit.byId("assignmentPlannedWork").set('value', '0');
  dijit.byId("assignmentComment").set('value', '');
  dijit.byId("dialogAssignment").set('title', i18n("dialogAssignment"));
  dijit.byId("assignmentIdResource").set('readOnly', false);
  dijit.byId("assignmentIdRole").set('readOnly', false);
  dojo.byId("assignmentPlannedUnit").value=unit;
  dojo.byId("assignmentLeftUnit").value=unit;
  dojo.byId("assignmentRealUnit").value=unit;
  dojo.byId("assignmentAssignedUnit").value=unit;
  if (dojo.byId('objectClass').value == 'Meeting'
      || dojo.byId('objectClass').value == 'PeriodicMeeting') {
    if (dijit.byId('meetingEndTime')
        && dijit.byId('meetingEndTime').get('value')
        && dijit.byId('meetingStartTime')
        && dijit.byId('meetingStartTime').get('value')) {
      delay=(dijit.byId('meetingEndTime').get('value') - dijit.byId(
          'meetingStartTime').get('value')) / 1000 / 60 / 60;
      if (rawUnit == 'hours') {
        // OK
      } else {
        delay=Math.round(delay / hoursPerDay * 1000) / 1000;
      }
      dijit.byId("assignmentAssignedWork").set('value', delay);
      dijit.byId("assignmentPlannedWork").set('value', delay);
      dijit.byId("assignmentLeftWork").set('value', delay);
    }

  }
  dijit.byId("dialogAssignment").show();
}

/**
 * Display a edit Assignment Box
 * 
 */

var editAssignmentLoading=false;
function editAssignment(assignmentId, idResource, idRole, cost, rate,
    assignedWork, realWork, leftWork, unit) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  editAssignmentLoading=true;
  var prj=dijit.byId('idProject').get('value');
  /*
   * var datastore =new dojo.data.ItemFileReadStore({ query: {id:'*'}, url:
   * '../tool/jsonList.php?listType=listResourceProject&idProject='+prj
   * +'&selected=' + idResource, clearOnClose: true }); var store = new
   * dojo.store.DataStore({store: datastore}); //store.query({id:"*"});
   * dijit.byId('assignmentIdResource').set('store',store);
   */
  refreshListSpecific('listResourceProject', 'assignmentIdResource','idProject', prj, idResource);
  dijit.byId("assignmentIdResource").reset();
  dijit.byId("assignmentIdResource").set("value", idResource);
  dijit.byId("assignmentIdRole").set("value", idRole);
  dojo.byId("assignmentId").value=assignmentId;
  dojo.byId("assignmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("assignmentRefId").value=dojo.byId("id").value;
  dijit.byId("assignmentDailyCost")
      .set('value', dojo.number.format(cost / 100));
  dojo.byId("assignmentRate").value=rate;
  dijit.byId("assignmentAssignedWork").set('value',
      dojo.number.format(assignedWork / 100));
  dojo.byId("assignmentAssignedWorkInit").value=assignedWork / 100;
  dijit.byId("assignmentRealWork").set('value',
      dojo.number.format(realWork / 100));
  dijit.byId("assignmentLeftWork").set('value',
      dojo.number.format(leftWork / 100));
  var comment=dojo.byId('comment_assignment_' + assignmentId);
  if (comment) {
    dijit.byId("assignmentComment").set('value', comment.innerHTML);
  } else {
    dijit.byId("assignmentComment").set('value', '');
  }
  dojo.byId("assignmentPlannedUnit").value=unit;
  dojo.byId("assignmentLeftUnit").value=unit;
  dojo.byId("assignmentRealUnit").value=unit;
  dojo.byId("assignmentAssignedUnit").value=unit;
  dojo.byId("assignmentLeftWorkInit").value=leftWork / 100;
  assignmentUpdatePlannedWork('assignment');
  dijit.byId("dialogAssignment").set('title',
      i18n("dialogAssignment") + " #" + assignmentId);
  dijit.byId("dialogAssignment").show();
  if (dojo.number.parse(realWork) == 0) {
    dijit.byId("assignmentIdResource").set('readOnly', false);
    dijit.byId("assignmentIdRole").set('readOnly', false);
  } else {
    dijit.byId("assignmentIdResource").set('readOnly', true);
    if (!idRole) {
      dijit.byId("assignmentIdRole").set('readOnly', false);
    } else {
      dijit.byId("assignmentIdRole").set('readOnly', true);
    }
  }
  setTimeout("editAssignmentLoading=false", 1000);
}

/**
 * Update the left work on assignment update
 * 
 * @param prefix
 * @return
 */
function assignmentUpdateLeftWork(prefix) {
  var initAssigned=dojo.byId(prefix + "AssignedWorkInit");
  var initLeft=dojo.byId(prefix + "LeftWorkInit");
  var assigned=dojo.byId(prefix + "AssignedWork");
  var newAssigned=dojo.number.parse(assigned.value);
  if (newAssigned == null || isNaN(newAssigned)) {
    newAssigned=0;
    assigned.value=dojo.number.format(newAssigned);
  }
  var left=dojo.byId(prefix + "LeftWork");
  //// KEVIN #2338 ////
  var real = dojo.byId(prefix + "RealWork");
  // var planned = dojo.byId(prefix + "PlannedWork");
  diff=dojo.number.parse(assigned.value) - initAssigned.value;
  newLeft=parseFloat(initLeft.value) + diff;
  if (newLeft < 0 || isNaN(newLeft)) {
    newLeft=0;
  }
  if(assigned.value != initAssigned.value){
    diffe=dojo.number.parse(assigned.value) - real.value ;
    if (initAssigned.value==0 || isNaN(initAssigned.value)){
      newLeft= 0 + diffe;
    }
  }
  left.value=dojo.number.format(newLeft);
  assignmentUpdatePlannedWork(prefix);
}

/**
 * Update the planned work on assignment update
 * 
 * @param prefix
 * @return
 */
function assignmentUpdatePlannedWork(prefix) {
  var left=dojo.byId(prefix + "LeftWork");
  var newLeft=dojo.number.parse(left.value);
  if (newLeft == null || isNaN(newLeft)) {
    newLeft=0;
    left.value=dojo.number.format(newLeft);
  }
  var real=dojo.byId(prefix + "RealWork");
  var planned=dojo.byId(prefix + "PlannedWork");
  newPlanned=dojo.number.parse(real.value) + dojo.number.parse(left.value);
  planned.value=dojo.number.format(newPlanned);
}

/**
 * save an Assignment (after addAssignment or editAssignment)
 * 
 */
function saveAssignment() {
  /*
   * if (! dijit.byId('assignmentIdResource').get('value')) {
   * showAlert(i18n('messageMandatory',new Array(i18n('colIdResource'))));
   * return; } if (! dijit.byId('assignmentIdResource').get('value')) {
   * showAlert(i18n('messageMandatory',new Array(i18n('colIdResource'))));
   * return; }
   */
  var formVar=dijit.byId('assignmentForm');
  if (formVar.validate()) {
    dijit.byId("assignmentPlannedWork").focus();
    dijit.byId("assignmentLeftWork").focus();
    loadContent("../tool/saveAssignment.php", "resultDiv", "assignmentForm",
        true, 'assignment');
    dijit.byId('dialogAssignment').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

/**
 * Display a delete Assignment Box
 * 
 */
function removeAssignment(assignmentId, realWork, resource) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  if (parseFloat(realWork)) {
    msg=i18n('msgUnableToDeleteRealWork');
    showAlert(msg);
    return;
  }
  dojo.byId("assignmentId").value=assignmentId;
  dojo.byId("assignmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("assignmentRefId").value=dojo.byId("objectId").value;
  actionOK=function() {
    loadContent("../tool/removeAssignment.php", "resultDiv", "assignmentForm",
        true, 'assignment');
  };
  msg=i18n('confirmDeleteAssignment', new Array(resource));
  showConfirm(msg, actionOK);
}

function assignmentChangeResource() {
  if (editAssignmentLoading)
    return;
  var idResource=dijit.byId("assignmentIdResource").get("value");
  if (!idResource)
    return;
  dijit.byId('assignmentDailyCost').reset();
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=resourceRole&idResource='
        + idResource,
    handleAs : "text",
    load : function(data) {
      dijit.byId('assignmentIdRole').set('value', data);
    }
  });
}

function assignmentChangeRole() {
  if (editAssignmentLoading)
    return;
  var idResource=dijit.byId("assignmentIdResource").get("value");
  var idRole=dijit.byId("assignmentIdRole").get("value");
  if (!idResource || !idRole)
    return;
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=resourceCost&idResource='
        + idResource + '&idRole=' + idRole,
    handleAs : "text",
    load : function(data) {
      // #303
      // dijit.byId('assignmentDailyCost').set('value',data);
      dijit.byId('assignmentDailyCost').set('value', dojo.number.format(data));
    }
  });
}

// =============================================================================
// = ExpenseDetail
// =============================================================================

/**
 * Display a add Assignment Box
 * 
 */
function addExpenseDetail(expenseType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("expenseDetailId").value="";
  dojo.byId("idExpense").value=dojo.byId("objectId").value;
  dijit.byId("expenseDetailName").reset();
  dijit.byId("expenseDetailReference").reset();
  dijit.byId("expenseDetailDate").set('value', null);
  dijit.byId("expenseDetailType").reset();
  dojo.byId("expenseDetailDiv").innerHTML="";
  dijit.byId("expenseDetailAmount").reset();
  refreshList('idExpenseDetailType', expenseType, '1', null,'expenseDetailType', false);
  // dijit.byId("dialogExpenseDetail").set('title',i18n("dialogExpenseDetail"));
  dijit.byId("dialogExpenseDetail").show();
}

/**
 * Display a edit Assignment Box
 * 
 */
var expenseDetailLoad=false;
function editExpenseDetail(expenseType, id, idExpense, type, expenseDate,
    amount) {
  expenseDetailLoad=true;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  refreshList('idExpenseDetailType', expenseType, '1', null,'expenseDetailType', false);
  dojo.byId("expenseDetailId").value=id;
  dojo.byId("idExpense").value=idExpense;
  dijit.byId("expenseDetailName").set("value",
      dojo.byId('expenseDetail_' + id).value);
  dijit.byId("expenseDetailReference").set("value",
      dojo.byId('expenseDetailRef_' + id).value);
  dijit.byId("expenseDetailDate").set("value", getDate(expenseDate));
  dijit.byId("expenseDetailAmount").set("value", dojo.number.parse(amount));
  dijit.byId("dialogExpenseDetail").set('title',
      i18n("dialogExpenseDetail") + " #" + id);
  dijit.byId("expenseDetailType").set("value", type);
  expenseDetailLoad=false;
  expenseDetailTypeChange(id);
  expenseDetailLoad=true;
  setTimeout('expenseDetailLoad=false;', 500);
  dijit.byId("dialogExpenseDetail").show();
}

/**
 * save an Assignment (after addAssignment or editAssignment)
 * 
 */
function saveExpenseDetail() {
  expenseDetailRecalculate();
  if (!dijit.byId('expenseDetailName').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colName'))));
    return;
  }
  /*if (!dijit.byId('expenseDetailDate').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colDate'))));
    return;
  }*/
  /*if (!dijit.byId('expenseDetailType').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colType'))));
    return;
  }*/
  if (!dijit.byId('expenseDetailAmount').get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colAmount'))));
    return;
  }
  var formVar=dijit.byId('expenseDetailForm');
  if (formVar.validate()) {
    dijit.byId("expenseDetailName").focus();
    dijit.byId("expenseDetailAmount").focus();
    loadContent("../tool/saveExpenseDetail.php", "resultDiv",
        "expenseDetailForm", true, 'expenseDetail');
    dijit.byId('dialogExpenseDetail').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

/**
 * Display a delete Assignment Box
 * 
 */
function removeExpenseDetail(expenseDetailId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("expenseDetailId").value=expenseDetailId;
  actionOK=function() {
    loadContent("../tool/removeExpenseDetail.php", "resultDiv",
        "expenseDetailForm", true, 'expenseDetail');
  };
  msg=i18n('confirmDeleteExpenseDetail', new Array(dojo.byId('expenseDetail_'
      + expenseDetailId).value));
  showConfirm(msg, actionOK);
}

function expenseDetailTypeChange(expenseDetailId) {
  if (expenseDetailLoad)
    return;
  var idType=dijit.byId("expenseDetailType").get("value");
  var url='../tool/expenseDetailDiv.php?idType=' + idType;
  if (expenseDetailId) {
    url+='&expenseDetailId=' + expenseDetailId;
  }
  loadContent(url, 'expenseDetailDiv', null, false);
}

function expenseDetailRecalculate() {
  val=false;
  if (dijit.byId('expenseDetailValue01')) {
    val01=dijit.byId('expenseDetailValue01').get("value");
  } else {
    val01=dojo.byId('expenseDetailValue01').value;
  }
  if (dijit.byId('expenseDetailValue02')) {
    val02=dijit.byId('expenseDetailValue02').get("value");
  } else {
    val02=dojo.byId('expenseDetailValue02').value;
  }
  if (dijit.byId('expenseDetailValue03')) {
    val03=dijit.byId('expenseDetailValue03').get("value");
  } else {
    val03=dojo.byId('expenseDetailValue03').value;
  }
  total=1;
  if (dojo.byId('expenseDetailUnit01').value) {
    total=total * val01;
    val=true;
  }
  if (dojo.byId('expenseDetailUnit02').value) {
    total=total * val02;
    val=true;
  }
  if (dojo.byId('expenseDetailUnit03').value) {
    total=total * val03;
    val=true;
  }
  if (val) {
    dijit.byId("expenseDetailAmount").set('value', total);
    lockWidget("expenseDetailAmount");
  } else {
    unlockWidget("expenseDetailAmount");
  }
}

// =============================================================================
// = DocumentVersion
// =============================================================================

/**
 * Display a add Document Version Box
 * 
 */
function addDocumentVersion(defaultStatus, typeEvo, numVers, dateVers, nameVers) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  content=dijit.byId('dialogDocumentVersion').get('content');
  if (content == "") {
    callBack=function() {
      dojo.connect(dijit.byId("documentVersionFile"), "onComplete", function(
          dataArray) {
        saveDocumentVersionAck(dataArray);
      });
      dojo.connect(dijit.byId("documentVersionFile"), "onProgress", function(
          data) {
        saveDocumentVersionProgress(data);
      });
      addDocumentVersion(defaultStatus, typeEvo, numVers, dateVers, nameVers);
    };
    loadDialog('dialogDocumentVersion', callBack);
    return;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  if (dijit.byId("documentVersionFile")) {
    dijit.byId("documentVersionFile").reset();
    if (!isHtml5()) {
      enableWidget('dialogDocumentVersionSubmit');
    } else {
      disableWidget('dialogDocumentVersionSubmit');
    }
  }
  dojo.byId("documentVersionId").value="";
  dojo.byId('documentVersionFileName').innerHTML="";
  refreshListSpecific('listStatusDocumentVersion', 'documentVersionIdStatus','idDocumentVersion', '');
  dijit.byId('documentVersionIdStatus').set('value', defaultStatus);
  dojo.style(dojo.byId('inputFileDocumentVersion'), {
    display : 'block'
  });
  dojo.byId("documentId").value=dojo.byId("objectId").value;
  dojo.byId("documentVersionVersion").value=dojo.byId('version').value;
  dojo.byId("documentVersionRevision").value=dojo.byId('revision').value;
  dojo.byId("documentVersionDraft").value=dojo.byId('draft').value;
  dojo.byId("typeEvo").value=typeEvo;
  dijit.byId("documentVersionLink").set('value', '');
  dijit.byId("documentVersionFile").reset();
  dijit.byId("documentVersionDescription").set('value', '');
  dijit.byId("documentVersionUpdateMajor").set('checked', 'true');
  dijit.byId("documentVersionUpdateDraft").set('checked', false);
  dijit.byId("documentVersionDate").set('value', new Date());
  dijit.byId("documentVersionUpdateMajor").set('readOnly', false);
  dijit.byId("documentVersionUpdateMinor").set('readOnly', false);
  dijit.byId("documentVersionUpdateNo").set('readonly', false);
  dijit.byId("documentVersionUpdateDraft").set('readonly', false);
  dijit.byId("documentVersionIsRef").set('checked', false);
  dijit.byId('documentVersionVersionDisplay')
      .set(
          'value',
          getDisplayVersion(typeEvo, dojo.byId('documentVersionVersion').value,
              dojo.byId('documentVersionRevision').value, dojo
                  .byId('documentVersionDraft').value), numVers, dateVers,
          nameVers);
  dojo.byId('documentVersionMode').value="add";
  calculateNewVersion();
  setDisplayIsRefDocumentVersion();
  dijit.byId("dialogDocumentVersion").show();
}

/**
 * Display a edit Document Version Box
 * 
 */
// var documentVersionLoad=false;
function editDocumentVersion(id, version, revision, draft, versionDate, status,
    isRef, typeEvo, numVers, dateVers, nameVers) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  content=dijit.byId('dialogDocumentVersion').get('content');
  if (content == "") {
    callBack=function() {
      dojo.connect(dijit.byId("documentVersionFile"), "onComplete", function(
          dataArray) {
        saveDocumentVersionAck(dataArray);
      });
      dojo.connect(dijit.byId("documentVersionFile"), "onProgress", function(
          data) {
        saveDocumentVersionProgress(data);
      });
      editDocumentVersion(id, version, revision, draft, versionDate, status,
          isRef, typeEvo, numVers, dateVers, nameVers);
    };
    loadDialog('dialogDocumentVersion', callBack);
    return;
  }
  dijit.byId('documentVersionIdStatus').store;
  refreshListSpecific('listStatusDocumentVersion', 'documentVersionIdStatus','idDocumentVersion', id);
  dijit.byId('documentVersionIdStatus').set('value', status);
  dojo.style(dojo.byId('inputFileDocumentVersion'), {
    display : 'none'
  });
  dojo.byId("documentVersionId").value=id;
  dojo.byId("documentId").value=dojo.byId("objectId").value;
  dojo.byId("documentVersionVersion").value=version;
  dojo.byId("documentVersionRevision").value=revision;
  dojo.byId("documentVersionDraft").value=draft;
  dojo.byId("typeEvo").value=typeEvo;
  if (draft) {
    dijit.byId('documentVersionUpdateDraft').set('checked', true);
  } else {
    dijit.byId('documentVersionUpdateDraft').set('checked', false);
  }
  if (isRef == '1') {
    dijit.byId('documentVersionIsRef').set('checked', true);
  } else {
    dijit.byId('documentVersionIsRef').set('checked', false);
  }
  dijit.byId("documentVersionLink").set('value', '');
  dijit.byId("documentVersionFile").reset();
  dijit.byId("documentVersionDescription").set("value",
      dojo.byId("documentVersion_" + id).value);
  dijit.byId("documentVersionUpdateMajor").set('readOnly', 'readOnly');
  dijit.byId("documentVersionUpdateMinor").set('readOnly', 'readOnly');
  dijit.byId("documentVersionUpdateNo").set('readonly', 'readonly');
  dijit.byId("documentVersionUpdateNo").set('checked', true);
  dijit.byId("documentVersionUpdateDraft").set('readonly', 'readonly');
  dijit.byId("documentVersionDate").set('value', versionDate);
  dojo.byId('documentVersionMode').value="edit";
  dijit.byId('documentVersionVersionDisplay').set('value', nameVers);
  calculateNewVersion(false);
  setDisplayIsRefDocumentVersion();
  dijit.byId("dialogDocumentVersion").show();
}

function changeDocumentVersion(list) {
  if (list.length > 0) {
    dojo.byId('documentVersionFileName').innerHTML=list[0]['name'];
    enableWidget('dialogDocumentVersionSubmit');
  } else {
    dojo.byId('documentVersionFileName').innerHTML="";
    disableWidget('dialogDocumentVersionSubmit');
  }
}

/**
 * save an Assignment (after addAssignment or editAssignment)
 * 
 */
function saveDocumentVersion() {
  // dojo.byId('documentVersionForm').submit();
  if (!isHtml5()) {
    // dojo.byId('documentVersionForm').submit();
    showWait();
    dijit.byId('dialogDocumentVersion').hide();
    return true;
  }
  if (dojo.byId('documentVersionFileName').innerHTML == "") {
    return false;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'block'
  });
  showWait();
  dijit.byId('dialogDocumentVersion').hide();
  return true;
}

/**
 * Acknoledge the attachment save
 * 
 * @return void
 */
function saveDocumentVersionAck(dataArray) {
  if (!isHtml5()) {
    resultFrame=document.getElementById("documentVersionPost");
    resultText=documentVersionPost.document.body.innerHTML;
    dojo.byId('resultAckDocumentVersion').value=resultText;
    loadContent("../tool/ack.php", "resultDiv", "documentVersionAckForm", true,
        'documentVersion');
    return;
  }
  dijit.byId('dialogDocumentVersion').hide();
  if (dojo.isArray(dataArray)) {
    result=dataArray[0];
  } else {
    result=dataArray;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  dojo.byId('resultAckDocumentVersion').value=result.message;
  loadContent("../tool/ack.php", "resultDiv", "documentVersionAckForm", true,
      'documentVersion');
}

function saveDocumentVersionProgress(data) {
  done=data.bytesLoaded;
  total=data.bytesTotal;
  if (total) {
    progress=done / total;
  }
  dijit.byId('downloadProgress').set('value', progress);
}
/**
 * Display a delete Assignment Box
 * 
 */
function removeDocumentVersion(documentVersionId, documentVersionName) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  content=dijit.byId('dialogDocumentVersion').get('content');
  if (content == "") {
    callBack=function() {
      dojo.connect(dijit.byId("documentVersionFile"), "onComplete", function(
          dataArray) {
        saveDocumentVersionAck(dataArray);
      });
      dojo.connect(dijit.byId("documentVersionFile"), "onProgress", function(
          data) {
        saveDocumentVersionProgress(data);
      });
      removeDocumentVersion(documentVersionId, documentVersionName);
    };
    loadDialog('dialogDocumentVersion', callBack);
    return;
  }
  dojo.byId("documentVersionId").value=documentVersionId;
  actionOK=function() {
    loadContent("../tool/removeDocumentVersion.php", "resultDiv",
        "documentVersionForm", true, 'documentVersion');
  };
  msg=i18n('confirmDeleteDocumentVersion', new Array(documentVersionName));
  showConfirm(msg, actionOK);
}

function getDisplayVersion(typeEvo, version, revision, draft, numVers,
    dateVers, nameVers) {
  var res="";
  if (typeEvo == "EVO") {
    if (version != "" && revision != "") {
      res="V" + version + "." + revision;
    }
  } else if (typeEvo == "EVT") {
    res=dateVers;
  } else if (typeEvo == "SEQ") {
    res=numVers;
  } else if (typeEvo == "EXT") {
    res=nameVers;
  }
  if (typeEvo == "EVO" || typeEvo == "EVT" || typeEvo == "SEQ") {
    if (draft) {
      res+=draftSeparator + draft;
    }
  }
  return res;
}

function calculateNewVersion(update) {
  var typeEvo=dojo.byId("typeEvo").value;
  var numVers="";
  var dateVers="";
  var nameVers="";
  if (dijit.byId('documentVersionUpdateMajor').get('checked')) {
    type="major";
  } else if (dijit.byId('documentVersionUpdateMinor').get('checked')) {
    type="minor";
  } else if (dijit.byId('documentVersionUpdateNo').get('checked')) {
    type="none";
  }
  version=dojo.byId('documentVersionVersion').value;
  revision=dojo.byId('documentVersionRevision').value;
  draft=dojo.byId('documentVersionDraft').value;
  isDraft=dijit.byId('documentVersionUpdateDraft').get('checked');
  version=(version == '') ? 0 : parseInt(version, 10);
  revision=(revision == '') ? 0 : parseInt(revision, 10);
  draft=(draft == '') ? 0 : parseInt(draft, 10);
  if (type == "major") {
    dojo.byId('documentVersionNewVersion').value=version + 1;
    dojo.byId('documentVersionNewRevision').value=0;
    dojo.byId('documentVersionNewDraft').value=(isDraft) ? '1' : '';
  } else if (type == "minor") {
    dojo.byId('documentVersionNewVersion').value=version;
    dojo.byId('documentVersionNewRevision').value=revision + 1;
    dojo.byId('documentVersionNewDraft').value=(isDraft) ? '1' : '';
  } else { // 'none'
    dojo.byId('documentVersionNewVersion').value=version;
    dojo.byId('documentVersionNewRevision').value=revision;
    if (dojo.byId('documentVersionId').value) {
      dojo.byId('documentVersionNewDraft').value=(isDraft) ? ((draft) ? draft
          : 1) : '';
    } else {
      dojo.byId('documentVersionNewDraft').value=(isDraft) ? draft + 1 : '';
    }
  }
  dateVers=dojo.date.locale.format(dijit.byId("documentVersionDate").get(
      'value'), {
    datePattern : "yyyyMMdd",
    selector : "date"
  });
  nameVers=dijit.byId("documentVersionVersionDisplay").get('value');
  numVers=nameVers;
  if (typeEvo == "SEQ" && dojo.byId('documentVersionMode').value == "add") {
    if (!nameVers) {
      nameVers=0;
    }
    numVers=parseInt(nameVers, 10) + 1;
  }
  dijit.byId("documentVersionNewVersionDisplay").set('readOnly', 'readOnly');
  if (typeEvo == "EXT") {
    dijit.byId("documentVersionNewVersionDisplay").set('readOnly', false);
  }
  var newVers=getDisplayVersion(typeEvo,
      dojo.byId('documentVersionNewVersion').value, dojo
          .byId('documentVersionNewRevision').value, dojo
          .byId('documentVersionNewDraft').value, numVers, dateVers, nameVers);
  dijit.byId('documentVersionNewVersionDisplay').set('value', newVers);
  if (isDraft) {
    dijit.byId('documentVersionIsRef').set('checked', false);
    setDisplayIsRefDocumentVersion();
  }
}

function setDisplayIsRefDocumentVersion() {
  if (dijit.byId('documentVersionIsRef').get('checked')) {
    dojo.style(dojo.byId('documentVersionIsRefDisplay'), {
      display : 'block'
    });
    dijit.byId('documentVersionUpdateDraft').set('checked', false);
    calculateNewVersion();
  } else {
    dojo.style(dojo.byId('documentVersionIsRefDisplay'), {
      display : 'none'
    });
  }
}
// =============================================================================
// = Dependency
// =============================================================================

/**
 * Display a add Dependency Box
 * 
 */
function addDependency(depType) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  noRefreshDependencyList=false;
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  var message=i18n("dialogDependency");
  if (depType) {
    dojo.byId("dependencyType").value=depType;
    message=i18n("dialogDependencyRestricted", new Array(i18n(objectClass),
        objectId, i18n(depType)));
  } else {
    dojo.byId("dependencyType").value=null;
    message=i18n("dialogDependencyExtended", new Array(i18n(objectClass),
        objectId.value));
  }
  if (objectClass == 'Requirement') {
    refreshList('idDependable', 'scope', 'R', '4', 'dependencyRefTypeDep',true);
    dijit.byId("dependencyRefTypeDep").set('value', '4');
    dijit.byId("dependencyDelay").set('value', '0');
    dojo.byId("dependencyDelayDiv").style.display="none";
  } else if (objectClass == 'TestCase') {
    refreshList('idDependable', 'scope', 'TC', '5', 'dependencyRefTypeDep',true);
    dijit.byId("dependencyRefTypeDep").set('value', '5');
    dijit.byId("dependencyDelay").set('value', '0');
    dojo.byId("dependencyDelayDiv").style.display="none";
  } else {
    if (objectClass == 'Project') {
      dijit.byId("dependencyRefTypeDep").set('value', '3');
      refreshList('idDependable', 'scope', 'PE', '3', 'dependencyRefTypeDep',true);
    } else {
      dijit.byId("dependencyRefTypeDep").set('value', '1');
      refreshList('idDependable', 'scope', 'PE', '1', 'dependencyRefTypeDep',true);
    }
    if (objectClass == 'Term') {
      dojo.byId("dependencyDelayDiv").style.display="none";
    } else {
      dojo.byId("dependencyDelayDiv").style.display="block";
    }
  }
  refreshDependencyList();
  refreshList('idActivity', 'idProject', '0', null, 'dependencyRefIdDepEdit',false);
  dijit.byId('dependencyRefIdDepEdit').reset();
  dojo.byId("dependencyId").value="";
  dojo.byId("dependencyRefType").value=objectClass;
  dojo.byId("dependencyRefId").value=objectId;
  dijit.byId("dialogDependency").set('title', message);
  dijit.byId("dialogDependency").show();
  dojo.byId('dependencyAddDiv').style.display='block';
  dojo.byId('dependencyEditDiv').style.display='none';
  dijit.byId("dependencyRefTypeDep").set('readOnly', false);
  dijit.byId("dependencyComment").set('value',null);
  disableWidget('dialogDependencySubmit');
  
  
}

function editDependency(depType, id, refType, refTypeName, refId, delay) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  noRefreshDependencyList=true;
  var objectClass=dojo.byId("objectClass").value;
  var objectId=dojo.byId("objectId").value;
  var message=i18n("dialogDependencyEdit");
  if (objectClass == 'Requirement') {
    refreshList('idDependable', 'scope', 'R', refType, 'dependencyRefTypeDep',true);
    dijit.byId("dependencyRefTypeDep").set('value', refType);
    dijit.byId("dependencyDelay").set('value', '0');
    dojo.byId("dependencyDelayDiv").style.display="none";
  } else if (objectClass == 'TestCase') {
    refreshList('idDependable', 'scope', 'TC', refType, 'dependencyRefTypeDep',true);
    dijit.byId("dependencyRefTypeDep").set('value', refType);
    dijit.byId("dependencyDelay").set('value', '0');
    dojo.byId("dependencyDelayDiv").style.display="none";
  } else {
    refreshList('idDependable', 'scope', 'PE', refType, 'dependencyRefTypeDep',true);
    dijit.byId("dependencyRefTypeDep").set('value', refType);
    dijit.byId("dependencyDelay").set('value', delay);
    dojo.byId("dependencyDelayDiv").style.display="block";
  }
  // refreshDependencyList();
  refreshList('id' + refTypeName, 'idProject', '0', refId,'dependencyRefIdDepEdit', true);
  dijit.byId('dependencyRefIdDepEdit').set('value', refId);
  dojo.byId("dependencyId").value=id;
  dojo.byId("dependencyRefType").value=objectClass;
  dojo.byId("dependencyRefId").value=objectId;
  dijit.byId("dialogDependency").set('title', message);
  dijit.byId("dialogDependency").show();
  dojo.byId('dependencyAddDiv').style.display='none';
  dojo.byId('dependencyEditDiv').style.display='block';
  dijit.byId("dependencyRefTypeDep").set('readOnly', true);
  dijit.byId("dependencyRefIdDepEdit").set('readOnly', true);
  disableWidget('dialogDependencySubmit');
  //KEVIN TICKET #2038 
  disableWidget('dependencyComment');
  dijit.byId('dependencyComment').set('value',"");
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=dependencyComment&idDependency='+ id,
    handleAs : "text",
    load : function(data) {
      dijit.byId('dependencyComment').set('value', data);
      enableWidget('dialogDependencySubmit');
      enableWidget('dependencyComment');
    }
  });
}

/**
 * Refresh the Dependency list (after update)
 */
var noRefreshDependencyList=false;
function refreshDependencyList(selected) {
  if (noRefreshDependencyList)
    return;
  disableWidget('dialogDependencySubmit');
  var url='../tool/dynamicListDependency.php';
  if (selected) {
    url+='?selected=' + selected;
  }
  loadContent(url, 'dialogDependencyList', 'dependencyForm', false);
}
/**
 * save a Dependency (after addLink)
 * 
 */
function saveDependency() {
  var formVar=dijit.byId('dependencyForm');
  if (!formVar.validate()) {
    showAlert(i18n("alertInvalidForm"));
    return;
  }
  if (dojo.byId("dependencyRefIdDep").value == ""
      && !dojo.byId('dependencyId').value)
    return;
  loadContent("../tool/saveDependency.php", "resultDiv", "dependencyForm",
      true, 'dependency');
  dijit.byId('dialogDependency').hide();
}

function saveDependencyFromDndLink(ref1Type, ref1Id, ref2Type, ref2Id) {
  // alert("saveDependencyFromDndLink("+ref1Type+","+ref1Id+","+ref2Type+","+ref2Id+")");
  if (ref1Type == ref2Type && ref1Id == ref2Id)
    return;
  param="ref1Type=" + ref1Type;
  param+="&ref1Id=" + ref1Id;
  param+="&ref2Type=" + ref2Type;
  param+="&ref2Id=" + ref2Id;
  loadContent("../tool/saveDependencyDnd.php?" + param, "planResultDiv", null,
      true, 'dependency');
}
/**
 * Display a delete Dependency Box
 * 
 */
function removeDependency(dependencyId, refType, refId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("dependencyId").value=dependencyId;
  actionOK=function() {
    loadContent("../tool/removeDependency.php", "resultDiv", "dependencyForm",
        true, 'dependency');
  };
  msg=i18n('confirmDeleteLink', new Array(i18n(refType), refId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = BillLines
// =============================================================================

/**
 * Display a add line Box
 * 
 */
function addBillLine(billingType) {
  var postLoad=function() {  
    var prj=dijit.byId('idProject').get('value');
    refreshListSpecific('listTermProject', 'billLineIdTerm', 'idProject', prj);
    refreshListSpecific('listResourceProject', 'billLineIdResource', 'idProject',prj);
    refreshList('idActivityPrice', 'idProject', prj, null,'billLineIdActivityPrice');
    dijit.byId("dialogBillLine").set('title', i18n("dialogBillLine"));
  };
  var params="&id=";
  params+="&refType="+dojo.byId("objectClass").value;
  params+="&refId="+dojo.byId("objectId").value;
  if (billingType) params+="&billingType="+billingType;
  loadDialog('dialogBillLine', postLoad, true, params, true);
}

/**
 * Display a edit line Box
 * 
 */
function editBillLine(id,billingType) {
  var params="&id="+id;
  params+="&refType="+dojo.byId("objectClass").value;
  params+="&refId="+dojo.byId("objectId").value;
  if (billingType) params+="&billingType="+billingType;
  loadDialog('dialogBillLine', null, true, params, true)
}


/**
 * save a line (after addDetail or editDetail)
 * 
 */
function saveBillLine() {
  if (isNaN(dijit.byId("billLineLine").getValue())) {
    dijit.byId("billLineLine").set("class", "dijitError");
    // dijit.byId("noteNote").blur();
    var msg=i18n('messageMandatory', new Array(i18n('BillLine')));
    new dijit.Tooltip({
      id : "billLineToolTip",
      connectId : [ "billLineLine" ],
      label : msg,
      showDelay : 0
    });
    dijit.byId("billLineLine").focus();
  } else {
    loadContent("../tool/saveBillLine.php", "resultDiv", "billLineForm", true,
        'billLine');
    dijit.byId('dialogBillLine').hide();
  }
}

/**
 * Display a delete line Box
 * 
 */
function removeBillLine(lineId) {
  //dojo.byId("billLineId").value=lineId;
  actionOK=function() {
    loadContent("../tool/removeBillLine.php?billLineId="+lineId, "resultDiv", null,
        true, 'billLine');
  };
  msg=i18n('confirmDelete', new Array(i18n('BillLine'), lineId));
  showConfirm(msg, actionOK);
}

function billLineUpdateAmount() {
  var price=dijit.byId('billLinePrice').get('value');
  var quantity=dijit.byId('billLineQuantity').get('value');
  var amount=price*quantity;
  dijit.byId('billLineAmount').set('value',amount);
}

// =============================================================================
// = ChecklistDefinitionLine
// =============================================================================

/**
 * Display a add line Box
 * 
 */
function addChecklistDefinitionLine(checkId) {
  var params="&checkId=" + checkId;
  loadDialog('dialogChecklistDefinitionLine', null, true, params);
}

/**
 * Display a edit line Box
 * 
 */
function editChecklistDefinitionLine(checkId, lineId) {
  var params="&checkId=" + checkId + "&lineId=" + lineId;
  loadDialog('dialogChecklistDefinitionLine', null, true, params);
}

/**
 * save a line (after addDetail or editDetail)
 * 
 */
function saveChecklistDefinitionLine() {
  if (!dijit.byId("dialogChecklistDefinitionLineName").get('value')) {
    showAlert(i18n('messageMandatory', new Array(i18n('colName'))));
    return false;
  }
  loadContent("../tool/saveChecklistDefinitionLine.php", "resultDiv",
      "dialogChecklistDefinitionLineForm", true, 'checklistDefinitionLine');
  dijit.byId('dialogChecklistDefinitionLine').hide();

}

/**
 * Display a delete line Box
 * 
 */
function removeChecklistDefinitionLine(lineId) {
  var params="?lineId=" + lineId;
  // loadDialog('dialogChecklistDefinitionLine',null, true, params)
  // dojo.byId("checklistDefinitionLineId").value=lineId;
  actionOK=function() {
    loadContent("../tool/removeChecklistDefinitionLine.php" + params,
        "resultDiv", null, true, 'checklistDefinitionLine');
  };
  msg=i18n('confirmDelete', new Array(i18n('ChecklistDefinitionLine'), lineId));
  showConfirm(msg, actionOK);
}

// =============================================================================
// = Checklist
// =============================================================================

function showChecklist(objectClass) {
  if (!objectClass) {
    return;
  }
  if (dijit.byId('id')) {
    var objectId=dijit.byId('id').get('value');
  } else {
    return;
  }
  var params="&objectClass=" + objectClass + "&objectId=" + objectId;
  loadDialog('dialogChecklist', null, true, params, true);
}

function saveChecklist() {
  // var params="&objectClass="+objectClass+"&objectId="+objectId;
  // loadDialog('dialogChecklist',null, true, params);
  loadContent('../tool/saveChecklist.php', 'resultDiv', 'dialogChecklistForm',
      true, 'checklist');
  dijit.byId('dialogChecklist').hide();
  return false;
}

function checkClick(line, item) {
  checkName="check_" + line + "_" + item;
  if (dijit.byId(checkName).get('checked')) {
    for (var i=1; i <= 5; i++) {
      if (i != item && dijit.byId("check_" + line + "_" + i)) {
        dijit.byId("check_" + line + "_" + i).set('checked', false);
      }
    }
  }
}

// =============================================================================
// = History
// =============================================================================

function showHistory(objectClass) {
  if (!objectClass) {
    return;
  }
  if (dijit.byId('id')) {
    var objectId=dijit.byId('id').get('value');
  } else {
    return;
  }
  var params="&objectClass=" + objectClass + "&objectId=" + objectId;
  loadDialog('dialogHistory', null, true, params);
}
// =============================================================================
// = Import
// =============================================================================

/**
 * Display an import Data Box (Not used, for an eventual improvement)
 * 
 */
function importData() {
  var controls=controlImportData();
  if (controls) {
    showWait();
  }
  return controls;
}

function showHelpImportData() {
  var controls=controlImportData();
  if (controls) {
    showWait();
    var url='../tool/importHelp.php?elementType='
        + dijit.byId('elementType').get('value');
    url+='&fileType=' + dijit.byId('fileType').get('value');
    frames['resultImportData'].location.href=url;
  }
}

function controlImportData() {
  var elementType=dijit.byId('elementType').get('value');
  if (!elementType) {
    showAlert(i18n('messageMandatory', new Array(i18n('colImportElementType'))));
    return false;
  }
  var fileType=dijit.byId('fileType').get('value');
  if (!fileType) {
    showAlert(i18n('messageMandatory', new Array(i18n('colImportFileType'))));
    return false;
  }
  return true;
}
function importFinished() {
  if (dijit.byId('elementType') && dijit.byId('elementType').get('displayedValue')==i18n('Project') ) {
    refreshProjectSelectorList();
  }
}
// =============================================================================
// = Plan
// =============================================================================

/**
 * Display a planning Box
 * 
 */
function showPlanParam(selectedProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dijit.byId("dialogPlan").show();
}

/**
 * Run planning
 * 
 */
function plan() {
  var bt=dijit.byId('planButton');
  if (bt) {
    bt.set('iconClass', "iconPlan");
  }
  if (!dijit.byId('idProjectPlan').get('value')) {
    dijit.byId('idProjectPlan').set('value', ' ');
  }
  if (!dijit.byId('startDatePlan').get('value')) {
    showAlert(i18n('messageInvalidDate'));
    return;
  }
  loadContent("../tool/plan.php", "planResultDiv", "dialogPlanForm", true, null);
  dijit.byId("dialogPlan").hide();
}

function cancelPlan() {
  if (!dijit.byId('idProjectPlan').get('value')) {
    dijit.byId('idProjectPlan').set('value', ' ');
  }
  dijit.byId('dialogPlan').hide();
}

function showPlanSaveDates() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  callBack=function() {
    var proj=dijit.byId('idProjectPlan');
    if (proj) {
      dijit.byId('idProjectPlanSaveDates').set('value', proj.get('value'));
    }
  };
  if (dijit.byId("dialogPlanSaveDates")) {
    callBack();
    dijit.byId("dialogPlanSaveDates").show();
    return;
  }

  loadDialog('dialogPlanSaveDates', callBack, true);
}
function planSaveDates() {
  if (!dijit.byId('idProjectPlanSaveDates').get('value')) {
    dijit.byId('idProjectPlanSaveDates').set('value', ' ');
  }
  loadContent("../tool/planSaveDates.php", "planResultDiv",
      "dialogPlanSaveDatesForm", true, null);
  dijit.byId("dialogPlanSaveDates").hide();
}

//=============================================================================
//= Baseline
//=============================================================================

function showPlanningBaseline() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  callBack=function() {
    var proj=dijit.byId('idProjectPlan');
    if (proj) {
      dijit.byId('idProjectPlanBaseline').set('value', proj.get('value'));
    }
  };
  loadDialog('dialogPlanBaseline', callBack, true);
}
function savePlanningBaseline() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var callback=function(){
    dijit.byId('selectBaselineTop').reset();
    dijit.byId('selectBaselineBottom').reset();
    refreshList('idBaselineSelect',null,null,null,'selectBaselineTop');
    refreshList('idBaselineSelect',null,null,null,'selectBaselineBottom');
  };
  var formVar=dijit.byId('dialogPlanBaselineForm');
  if (formVar.validate()) {
    loadContent("../tool/savePlanningBaseline.php", "planResultDiv", "dialogPlanBaselineForm", true, null,null,null,callback);
    dijit.byId("dialogPlanBaseline").hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}
function editBaseline(baselineId) {
  var params="&editMode=true&baselineId="+baselineId;
  loadDialog('dialogPlanBaseline', null, true, params, true);
}

function removeBaseline(baselineId) {
  var param="?baselineId="+baselineId;
  actionOK=function() {
    loadContent("../tool/removePlanningBaseline.php"+param, "dialogPlanBaseline", null);
  };
  msg=i18n('confirmDelete', new Array(i18n('Baseline'), baselineId));
  showConfirm(msg, actionOK);
}
// =============================================================================
// = Filter
// =============================================================================

/**
 * Display a Filter Box
 * 
 */
var filterStartInput=false;
var filterFromDetail=false;
function showFilterDialog() {
  /*
   * if (checkFormChangeInProgress()) { showAlert(i18n('alertOngoingChange'));
   * return; }
   */
  function callBack(){
    filterStartInput=false;
    top.filterFromDetail=false;
    if (top.dijit.byId('dialogDetail').open) {
      top.filterFromDetail=true;
      dojo.byId('filterDefaultButtonDiv').style.display='none';
    } else {
      dojo.byId('filterDefaultButtonDiv').style.display='block';
    }
    dojo.style(dijit.byId('idFilterOperator').domNode, {
      visibility : 'hidden'
    });
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueList').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueCheckbox').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueDate').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterSortValueList').domNode, {
      display : 'none'
    });
    dijit.byId('idFilterAttribute').reset();
    dojo.byId('filterObjectClass').value=dojo.byId('objectClass').value;
    filterType="";
    var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
    dojo.xhrPost({
      url : "../tool/backupFilter.php?filterObjectClass="
          + dojo.byId('filterObjectClass').value + compUrl,
      handleAs : "text",
      load : function(data, args) {
      }
    });
    compUrl=(top.dijit.byId("dialogDetail").open) ? '?comboDetail=true' : '';
    loadContent("../tool/displayFilterClause.php" + compUrl,
        "listFilterClauses", "dialogFilterForm", false);
    loadContent("../tool/displayFilterList.php" + compUrl,
        "listStoredFilters", "dialogFilterForm", false);
    loadContent("../tool/displayFilterSharedList.php" + compUrl,
        "listSharedFilters", "dialogFilterForm", false);
    /*
     * var datastore = new dojo.data.ItemFileReadStore({url:
     * '../tool/jsonList.php?listType=object&objectClass=' +
     * dojo.byId("objectClass").value}); var store = new
     * dojo.store.DataStore({store: datastore}); store.query({id:"*"});
     * dijit.byId('idFilterAttribute').set('store',store);
     */
    refreshListSpecific('object', 'idFilterAttribute', 'objectClass', dojo.byId("objectClass").value);
    dijit.byId("dialogFilter").show();
  }
  
  loadDialog('dialogFilter', callBack, true, "", true);
}

/**
 * Select attribute : refresh dependant lists box
 * 
 */
function filterSelectAtribute(value) {
  if (value) {
    filterStartInput=true;
    dijit.byId('idFilterAttribute').store.store.fetchItemByIdentity({
      identity : value,
      onItem : function(item) {
        var dataType=dijit.byId('idFilterAttribute').store.store.getValue(
            item, "dataType", "inconnu");
        var datastoreOperator=new dojo.data.ItemFileReadStore({
          url : '../tool/jsonList.php?listType=operator&dataType=' + dataType
        });
        var storeOperator=new dojo.store.DataStore({
          store : datastoreOperator
        });
        storeOperator.query({
          id : "*"
        });
        dijit.byId('idFilterOperator').set('store', storeOperator);
        datastoreOperator.fetch({
          query : {
            id : "*"
          },
          count : 1,
          onItem : function(item) {
            dijit.byId('idFilterOperator').set("value", item.id);
          },
          onError : function(err) {
            console.info(err.message);
          }
        });
        dojo.style(dijit.byId('idFilterOperator').domNode, {
          visibility : 'visible'
        });
        dojo.byId('filterDataType').value=dataType;
        if (dataType == "bool") {
          filterType="bool";
          dojo.style(dijit.byId('filterValue').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueList').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueCheckbox').domNode, {
            display : 'block'
          });
          dijit.byId('filterValueCheckbox').set('checked', '');
          dojo.style(dijit.byId('filterValueDate').domNode, {
            display : 'none'
          });
        } else if (dataType == "list") {
          filterType="list";
          if (value == 'idTargetVersion' || value == 'idOriginalValue') {
            value='idVersion';
          }
          var urlListFilter='../tool/jsonList.php?required=true&listType=list&dataType='+value;
          if (currentSelectedProject && currentSelectedProject!='' && currentSelectedProject!='*') {
            urlListFilter+='&critField=idProject&critValue='+currentSelectedProject;
          }
          var tmpStore=new dojo.data.ItemFileReadStore({
            url : urlListFilter
          });
          var mySelect=dojo.byId("filterValueList");
          mySelect.options.length=0;
          var nbVal=0;
          tmpStore.fetch({
            query : {
              id : "*"
            },
            onItem : function(item) {
              mySelect.options[mySelect.length]=new Option(tmpStore.getValue(
                  item, "name", ""), tmpStore.getValue(item, "id", ""));
              nbVal++;
            },
            onError : function(err) {
              console.info(err.message);
            }
          });
          mySelect.size=(nbVal > 10) ? 10 : nbVal;
          dojo.style(dijit.byId('filterValue').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueList').domNode, {
            display : 'block'
          });
          dijit.byId('filterValueList').reset();
          dojo.style(dijit.byId('filterValueCheckbox').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueDate').domNode, {
            display : 'none'
          });
        } else if (dataType == "date") {
          filterType="date";
          dojo.style(dijit.byId('filterValue').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueList').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueCheckbox').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueDate').domNode, {
            display : 'block'
          });
          dijit.byId('filterValueDate').reset();
        } else {
          filterType="text";
          dojo.style(dijit.byId('filterValue').domNode, {
            display : 'block'
          });
          dijit.byId('filterValue').reset();
          dojo.style(dijit.byId('filterValueList').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueCheckbox').domNode, {
            display : 'none'
          });
          dojo.style(dijit.byId('filterValueDate').domNode, {
            display : 'none'
          });
        }
      },
      onError : function(err) {
        dojo.style(dijit.byId('idFilterOperator').domNode, {
          visibility : 'hidden'
        });
        dojo.style(dijit.byId('filterValue').domNode, {
          display : 'none'
        });
        dojo.style(dijit.byId('filterValueList').domNode, {
          display : 'none'
        });
        dojo.style(dijit.byId('filterValueCheckbox').domNode, {
          display : 'none'
        });
        dojo.style(dijit.byId('filterValueDate').domNode, {
          display : 'none'
        });
        // hideWait();
      }
    });
    dijit.byId('filterValue').reset();
    dijit.byId('filterValueList').reset();
    dijit.byId('filterValueCheckbox').reset();
    dijit.byId('filterValueDate').reset();
  } else {
    dojo.style(dijit.byId('idFilterOperator').domNode, {
      visibility : 'hidden'
    });
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueList').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueCheckbox').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueDate').domNode, {
      display : 'none'
    });
  }
}

function filterSelectOperator(operator) {
  filterStartInput=true;
  if (operator == "SORT") {
    filterType="SORT";
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueList').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueCheckbox').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueDate').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterSortValueList').domNode, {
      display : 'block'
    });
  } else if (operator == "<=now+" || operator == ">=now+") {
    filterType="text";
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'block'
    });
    dojo.style(dijit.byId('filterValueList').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueCheckbox').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueDate').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterSortValueList').domNode, {
      display : 'none'
    });
  } else if (operator == "isEmpty" || operator == "isNotEmpty"
      || operator == "hasSome") {
    filterType="null";
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueList').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueCheckbox').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterValueDate').domNode, {
      display : 'none'
    });
    dojo.style(dijit.byId('filterSortValueList').domNode, {
      display : 'none'
    });
  } else {
    dojo.style(dijit.byId('filterValue').domNode, {
      display : 'none'
    });
    dataType=dojo.byId('filterDataType').value;
    dojo.style(dijit.byId('filterSortValueList').domNode, {
      display : 'none'
    });
    if (dataType == "bool") {
      filterType="bool";
      dojo.style(dijit.byId('filterValueCheckbox').domNode, {
        display : 'block'
      });
    } else if (dataType == "list") {
      filterType="list";
      dojo.style(dijit.byId('filterValueList').domNode, {
        display : 'block'
      });
    } else if (dataType == "date") {
      filterType="date";
      dojo.style(dijit.byId('filterValueDate').domNode, {
        display : 'block'
      });
    } else {
      filterType="text";
      dojo.style(dijit.byId('filterValue').domNode, {
        display : 'block'
      });
    }
  }
}

/**
 * Save filter clause
 * 
 */
function addfilterClause(silent) {
  filterStartInput=false;
  if (dijit.byId('filterNameDisplay')) {
    dojo.byId('filterName').value=dijit.byId('filterNameDisplay').get('value');
  }
  if (filterType == "") {
    if (!silent)
      showAlert(i18n('attributeNotSelected'));
    return;
  }
  if (trim(dijit.byId('idFilterOperator').get('value')) == '') {
    if (!silent)
      showAlert(i18n('operatorNotSelected'));
    return;
  }
  if (filterType == "list"
      && trim(dijit.byId('filterValueList').get('value')) == '') {
    if (!silent)
      showAlert(i18n('valueNotSelected'));
    return;
  }
  if (filterType == "date" && !dijit.byId('filterValueDate').get('value')) {
    if (!silent)
      showAlert(i18n('valueNotSelected'));
    return;
  }
  if (filterType == "text" && !dijit.byId('filterValue').get('value')) {
    if (!silent)
      showAlert(i18n('valueNotSelected'));
    return;
  }
  if (dijit.byId('idFilterAttribute').get('value')=='idle' 
    && dijit.byId('idFilterOperator').get('value')=='='
    && dijit.byId('filterValueCheckbox').get('checked')) {
    dijit.byId('listShowIdle').set('checked',true);
  }
  // Add controls on operator and value
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '?comboDetail=true' : '';
  loadContent("../tool/addFilterClause.php" + compUrl, "listFilterClauses",
      "dialogFilterForm", false);
  // dijit.byId('filterNameDisplay').set('value',null);
  // dojo.byId('filterName').value=null;
}

/**
 * Remove a filter clause
 * 
 */
function removefilterClause(id) {
  if (dijit.byId('filterNameDisplay')) {
    dojo.byId('filterName').value=dijit.byId('filterNameDisplay').get(
        'value');
  }
  // Add controls on operator and value
  dojo.byId("filterClauseId").value=id;
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '?comboDetail=true' : '';
  loadContent("../tool/removeFilterClause.php" + compUrl,
      "listFilterClauses", "dialogFilterForm", false);
  // dijit.byId('filterNameDisplay').set('value',null);
  // dojo.byId('filterName').value=null;
}

/**
 * Action on OK for filter
 * 
 */
function selectFilter() {
  if (filterStartInput) {
    addfilterClause(true);
    setTimeout("selectFilterContinue();", 1000);
  } else {
    selectFilterContinue();
  }
}
function selectFilterContinue() {
  if (top.dijit.byId('dialogDetail').open) {
    var doc=top.window.frames['comboDetailFrame'];
  } else {
    var doc=top;
  }
  if (dijit.byId('filterNameDisplay')) {
    dojo.byId('filterName').value=dijit.byId('filterNameDisplay').get('value');
  }
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
  dojo.xhrPost({
    url : "../tool/backupFilter.php?valid=true" + compUrl,
    form : dojo.byId('dialogFilterForm'),
    handleAs : "text",
    load : function(data, args) {
    }
  });
  if (dojo.byId("nbFilterCriteria").value > 0) {
    doc.dijit.byId("listFilterFilter").set("iconClass", "iconActiveFilter");
  } else {
    doc.dijit.byId("listFilterFilter").set("iconClass", "iconFilter");
  }
  doc.loadContent(
      "../tool/displayFilterList.php?context=directFilterList&filterObjectClass="
          + dojo.byId('objectClass').value + compUrl, "directFilterList", null,
      false, 'returnFromFilter', false);
  if(dojo.byId('objectClassManual')!=null && (dojo.byId('objectClassManual').value=='Plugin_kanban' || dojo.byId('objectClassManual').value=='Plugin_liveMeeting')){
    loadContent("../plugin/kanban/kanbanView.php?idKanban="+dojo.byId('idKanban').value, "divKanbanContainer");
  }else{
    doc.refreshJsonList(dojo.byId('objectClass').value);
  }
  dijit.byId("dialogFilter").hide();
  filterStartInput=false;
}

/**
 * Action on Cancel for filter
 * 
 */
function cancelFilter() {
  filterStartInput=true;
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
  dojo.xhrPost({
    url : "../tool/backupFilter.php?cancel=true" + compUrl,
    form : dojo.byId('dialogFilterForm'),
    handleAs : "text",
    load : function(data, args) {
    }
  });
  dijit.byId('dialogFilter').hide();
}

/**
 * Action on Clear for filter
 * 
 */
function clearFilter() {
  if (dijit.byId('filterNameDisplay')) {
    dijit.byId('filterNameDisplay').reset();
  }
  dojo.byId('filterName').value="";
  removefilterClause('all');
  // setTimeout("selectFilter();dijit.byId('listFilterFilter').set('iconClass','iconFilter');",100);
  dijit.byId('listFilterFilter').set('iconClass', 'iconFilter');
  dijit.byId('filterNameDisplay').set('value', null);
  dojo.byId('filterName').value=null;
}

/**
 * Action on Default for filter
 * 
 */
function defaultFilter() {
  if (dijit.byId('filterNameDisplay')) {
    // if (dijit.byId('filterNameDisplay').get('value')=="") {
    // showAlert(i18n("messageMandatory", new Array(i18n("filterName")) ));
    // return;
    // }
    dojo.byId('filterName').value=dijit.byId('filterNameDisplay').get(
        'value');
  }
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '?comboDetail=true' : '';
  loadContent("../tool/defaultFilter.php" + compUrl, "listStoredFilters",
      "dialogFilterForm", false);
}

/**
 * Save a filter as a stored filter
 * 
 */
function saveFilter() {
  if (dijit.byId('filterNameDisplay')) {
    if (dijit.byId('filterNameDisplay').get('value') == "") {
      showAlert(i18n("messageMandatory", new Array(i18n("filterName"))));
      return;
    }
    dojo.byId('filterName').value=dijit.byId('filterNameDisplay').get(
        'value');
  }
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '?comboDetail=true' : '';
  loadContent("../tool/saveFilter.php" + compUrl, "listStoredFilters",
      "dialogFilterForm", false);
}

/**
 * Select a stored filter in the list and fetch criteria
 * 
 */
function selectStoredFilter(idFilter, context, contentLoad, container) {
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
  if (context == 'directFilterList') {
    if (dojo.byId('noFilterSelected')) {
      if (idFilter == '0') {
        dojo.byId('noFilterSelected').value='true';
      } else {
        dojo.byId('noFilterSelected').value='false';
      }
    }

    if(typeof contentLoad != 'undefined' && typeof container != 'undefined'){
      loadContent("../tool/selectStoredFilter.php?idFilter=" + idFilter
          + "&context=" + context + "&contentLoad="+contentLoad+"&container="+container+"&filterObjectClass="
          + dojo.byId('objectClass').value + compUrl, "directFilterList", null,
          false);
      loadContent(contentLoad, container);
    }else{
      loadContent("../tool/selectStoredFilter.php?idFilter=" + idFilter
          + "&context=" + context + "&filterObjectClass="
          + dojo.byId('objectClass').value + compUrl, "directFilterList", null,
          false);
    }
  } else {
    loadContent(
        "../tool/selectStoredFilter.php?idFilter=" + idFilter + compUrl,
        "listFilterClauses", "dialogFilterForm", false);
  }
}

/**
 * Removes a stored filter from the list
 * 
 */
function removeStoredFilter(idFilter, nameFilter) {
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
  var action=function() {
    loadContent("../tool/removeFilter.php?idFilter=" + idFilter + compUrl,
        "listStoredFilters", "dialogFilterForm", false);
  };
  top.showConfirm(i18n("confirmRemoveFilter", new Array(nameFilter)), action);
}

/**
 * Share a stored filter from the list
 * 
 */
function shareStoredFilter(idFilter, nameFilter) {
  var compUrl=(top.dijit.byId("dialogDetail").open) ? '&comboDetail=true' : '';
  loadContent("../tool/shareFilter.php?idFilter=" + idFilter + compUrl,
        "listStoredFilters", "dialogFilterForm", false);
}

// =============================================================================
// = Reports
// =============================================================================

function reportSelectCategory(idCateg) {
  if (isNaN(idCateg)) return;
  loadContent("../view/reportsParameters.php?idReport=", "reportParametersDiv",
      null, false);
  var tmpStore=new dojo.data.ItemFileReadStore(
      {
        url : '../tool/jsonList.php?required=true&listType=list&dataType=idReport&critField=idReportCategory&critValue='
            + idCateg
      });
  var mySelectWidget=dijit.byId("reportsList");
  mySelectWidget.reset();
  var mySelect=dojo.byId("reportsList");
  mySelect.options.length=0;
  var nbVal=0;
  tmpStore.fetch({
    query : {
      id : "*"
    },
    onItem : function(item) {
      mySelect.options[mySelect.length]=new Option(tmpStore.getValue(item,
          "name", ""), tmpStore.getValue(item, "id", ""));
      nbVal++;
    },
    onError : function(err) {
      console.info(err.message);
    }
  });
}

function reportSelectReport(idReport) {
  if (isNaN(idReport)) return;
  dojo.query(".section").removeClass("reportSelected");
  dojo.addClass(dojo.byId('report'+idReport),"reportSelected");
  loadContent("../view/reportsParameters.php?idReport=" + idReport,
      "reportParametersDiv", null, false);
}

// =============================================================================
// = Resource Cost
// =============================================================================

function addResourceCost(idResource, idRole, funcList) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("resourceCostId").value="";
  dojo.byId("resourceCostIdResource").value=idResource;
  dojo.byId("resourceCostFunctionList").value=funcList;
  dijit.byId("resourceCostIdRole").set('readOnly', false);
  if (idRole) {
    dijit.byId("resourceCostIdRole").set('value', idRole);
  } else {
    dijit.byId("resourceCostIdRole").reset();
  }
  dijit.byId("resourceCostValue").reset('value');
  dijit.byId("resourceCostStartDate").set('value', null);
  resourceCostUpdateRole();
  dijit.byId("dialogResourceCost").show();
}

function removeResourceCost(id, idRole, nameRole, startDate) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("resourceCostId").value=id;
  actionOK=function() {
    loadContent("../tool/removeResourceCost.php", "resultDiv",
        "resourceCostForm", true, 'resourceCost');
  };
  msg=i18n('confirmDeleteResourceCost', new Array(nameRole, startDate));
  showConfirm(msg, actionOK);
}

reourceCostLoad=false;
function editResourceCost(id, idResource, idRole, cost, startDate, endDate) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("resourceCostId").value=id;
  dojo.byId("resourceCostIdResource").value=idResource;
  dijit.byId("resourceCostIdRole").set('readOnly', true);
  dijit.byId("resourceCostValue").set('value', dojo.number.format(cost / 100));
  var dateStartDate=getDate(startDate);
  dijit.byId("resourceCostStartDate").set('value', dateStartDate);
  dijit.byId("resourceCostStartDate").set('disabled', true);
  dijit.byId("resourceCostStartDate").set('required', 'false');
  reourceCostLoad=true;
  dijit.byId("resourceCostIdRole").set('value', idRole);
  setTimeout('reourceCostLoad=false;', 300);
  dijit.byId("dialogResourceCost").show();
}

function saveResourceCost() {
  var formVar=dijit.byId('resourceCostForm');
  if (formVar.validate()) {
    loadContent("../tool/saveResourceCost.php", "resultDiv",
        "resourceCostForm", true, 'resourceCost');
    dijit.byId('dialogResourceCost').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function resourceCostUpdateRole() {
  if (reourceCostLoad) {
    return;
  }
  if (dijit.byId("resourceCostIdRole").get('value') ) {
    dojo.xhrGet({
      url : '../tool/getSingleData.php?dataType=resourceCostDefault&idRole=' + dijit.byId("resourceCostIdRole").get('value'),
      handleAs : "text",
      load : function(data) {
        dijit.byId('resourceCostValue').set('value', dojo.number.format(data));
      }
    });
  }
  var funcList=dojo.byId('resourceCostFunctionList').value;
  $key='#' + dijit.byId("resourceCostIdRole").get('value') + '#';
  if (funcList.indexOf($key) >= 0) {
    dijit.byId("resourceCostStartDate").set('disabled', false);
    dijit.byId("resourceCostStartDate").set('required', 'true');
  } else {
    dijit.byId("resourceCostStartDate").set('disabled', true);
    dijit.byId("resourceCostStartDate").set('value', null);
    dijit.byId("resourceCostStartDate").set('required', 'false');
  }
}

// =============================================================================
// = Version Project
// =============================================================================

function addVersionProject(idVersion, idProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params="&idVersionProject=&idVersion="+idVersion+"&idProject="+idProject;
  loadDialog('dialogVersionProject', null, true, params, true);
  }

function removeVersionProject(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeVersionProject.php?idVersionProject="+id, "resultDiv", null, true, 'versionProject');
  };
  msg=i18n('confirmDeleteVersionProject');
  showConfirm(msg, actionOK);
}
;
function editVersionProject(id, idVersion, idProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params="&idVersionProject="+id+"&idVersion="+idVersion+"&idProject="+idProject;
  loadDialog('dialogVersionProject', null, true, params, true);
}

function saveVersionProject() {
  var formVar=dijit.byId('versionProjectForm');
  if (formVar.validate()) {
    loadContent("../tool/saveVersionProject.php", "resultDiv",
        "versionProjectForm", true, 'versionProject');
    dijit.byId('dialogVersionProject').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

// =============================================================================
// = Product Project
// =============================================================================

function addProductProject(idProduct, idProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params="&idProductProject=&idProduct="+idProduct+"&idProject="+idProject;
  loadDialog('dialogProductProject', null, true, params, true);
}

function removeProductProject(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeProductProject.php?idProductProject="+id, "resultDiv", null, true, 'productProject');
  };
  msg=i18n('confirmDeleteProductProject');
  showConfirm(msg, actionOK);
}

function editProductProject(id, idProduct, idProject) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  params="&idProductProject="+id+"&idProduct="+idProduct+"&idProject="+idProject;
  loadDialog('dialogProductProject', null, true, params, true);
}

function saveProductProject() {
  var formVar=dijit.byId('productProjectForm');
  if (formVar.validate()) {
    loadContent("../tool/saveProductProject.php", "resultDiv",
        "productProjectForm", true, 'productProject');
    dijit.byId('dialogProductProject').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

//=============================================================================
//= Test Case Run
//=============================================================================

function addTestCaseRun() {
if (checkFormChangeInProgress()) {
 showAlert(i18n('alertOngoingChange'));
 return;
}
 //disableWidget('dialogTestCaseRunSubmit');  
var params="&testSessionId="+dijit.byId('id').get('value');
loadDialog('dialogTestCaseRun', null, true, params);

}
function refreshTestCaseRunList(selected) {
disableWidget('dialogTestCaseRunSubmit');
var url='../tool/dynamicListTestCase.php';
url+='?idProject='+dijit.byId('idProject').get('value');
if (dijit.byId('idProduct')) url+='&idProduct='+dijit.byId('idProduct').get('value');
else if (dijit.byId('idProductOrComponent')) url+='&idProduct='+dijit.byId('idProductOrComponent').get('value');
else if (dijit.byId('idComponent')) url+='&idComponent='+dijit.byId('idComponent').get('value');
if (selected) {
 url+='&selected=' + selected;
}
loadContent(url, 'testCaseRunListDiv', 'testCaseRunForm', false);
}

function editTestCaseRun(testCaseRunId) {
if (checkFormChangeInProgress()) {
 showAlert(i18n('alertOngoingChange'));
 return;
}
//var callBack=function() {
//idProject=dijit.byId('idProject').get('value');
//refreshList('idTestCase', 'idProject', '0', idTestCase,'testCaseRunTestCase', true);
//refreshList('idTicket', 'idProject', idProject, idTicket,'testCaseRunTicket', false);
//dijit.byId("testCaseRunTestCase").set('readOnly', true);
//dijit.byId('testCaseRunTestCase').set('value', idTestCase);
//dojo.byId("testCaseRunId").value=idTestCaseRun;
//dojo.byId("testCaseRunMode").value="edit";
var testSessionId = dijit.byId('id').get('value');
//dijit.byId('testCaseRunComment').set('value',
//   dojo.byId("comment_" + idTestCaseRun).value);
////dijit.byId('testCaseRunStatus').set('value', idRunStatus);
//
// testCaseRunChangeStatus();
//enableWidget('dialogTestCaseRunSubmit');
//if (!hide) {
// dijit.byId("dialogTestCaseRun").show();
//}
//};
var params="&testCaseRunId=" + testCaseRunId + "&testSessionId=" + testSessionId;
loadDialog('dialogTestCaseRun', null, true, params);
}

function passedTestCaseRun(idTestCaseRun, idTestCase, idRunStatus, idTicket) {
editTestCaseRun(idTestCaseRun, idTestCase, '2', idTicket, true);
showWait();
setTimeout("saveTestCaseRun()", 500);
}

function failedTestCaseRun(idTestCaseRun, idTestCase, idRunStatus, idTicket) {
editTestCaseRun(idTestCaseRun, idTestCase, '3', idTicket, false);
}

function blockedTestCaseRun(idTestCaseRun, idTestCase, idRunStatus, idTicket) {
editTestCaseRun(idTestCaseRun, idTestCase, '4', idTicket, true);
showWait();
setTimeout("saveTestCaseRun()", 500);
}

function testCaseRunChangeStatus() {
var status=dijit.byId('testCaseRunStatus').get('value');
if (status == '3') {
 dojo.byId('testCaseRunTicketDiv').style.display="block";
} else {
 if (!trim(dijit.byId('testCaseRunTicket').get('value'))) {
   dojo.byId('testCaseRunTicketDiv').style.display="none";
 } else {
   dojo.byId('testCaseRunTicketDiv').style.display="block";
 }
}
}

function removeTestCaseRun(id, idTestCase) {
if (checkFormChangeInProgress()) {
 showAlert(i18n('alertOngoingChange'));
 return;
}
dojo.byId("testCaseRunId").value=id;
actionOK=function() {
 loadContent("../tool/removeTestCaseRun.php", "resultDiv",
     "testCaseRunForm", true, 'testCaseRun');
};
msg=i18n('confirmDeleteTestCaseRun', new Array(idTestCase));
showConfirm(msg, actionOK);
}

function saveTestCaseRun() {
var formVar=dijit.byId('testCaseRunForm');
var mode=dojo.byId("testCaseRunMode").value;
if ( (mode == 'add'  && dojo.byId("testCaseRunTestCaseList").value == "") 
  || (mode == 'edit' && dojo.byId("testCaseRunTestCase").value == "" ) )
 return ;
if (mode == 'edit') {
 var status=dijit.byId('testCaseRunStatus').get('value');
 if (status == '3') {
   if (trim(dijit.byId('testCaseRunTicket').get('value')) == '') {
     dijit.byId("dialogTestCaseRun").show();
     showAlert(i18n('messageMandatory', new Array(i18n('colTicket'))));
     return;
   }
 }
}
if (mode == 'add' || formVar.validate()) {
 loadContent("../tool/saveTestCaseRun.php", "resultDiv", "testCaseRunForm",
     true, 'testCaseRun');
 dijit.byId('dialogTestCaseRun').hide();
} else {
 dijit.byId("dialogTestCaseRun").show();
 showAlert(i18n("alertInvalidForm"));
}
}

// =============================================================================
// = Affectation
// =============================================================================

function addAffectation(objectClass, type, idResource, idProject) {
  affectationLoad=true;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  
  if (dijit.byId('idProfile')) {
    dijit.byId("affectationProfile").set('value',
        dijit.byId('idProfile').get('value'));
  } else {
    dijit.byId("affectationProfile").reset();
  }
  refreshList('idProfile', 'idProject', idProject, null, 'affectationProfile', false  ); // Attention, selected is given as idAffectation => must seach its profile ...
  if (objectClass == 'Project') {
    refreshList('idProject', 'id', idProject, idProject, 'affectationProject', true);
    dijit.byId("affectationProject").set('value', idProject);
    refreshList('id' + type, null, null, null, 'affectationResource', false);
    dijit.byId("affectationResource").reset();
  } else {
    if (currentSelectedProject=='*') {
      refreshList('idProject', null, null, null, 'affectationProject', false);
      dijit.byId("affectationProject").reset();
    } else {
      refreshList('idProject', null, null, currentSelectedProject, 'affectationProject', true);
      dijit.byId("affectationProject").set('value', currentSelectedProject);
    }
    refreshList('id' + objectClass, null, null, idResource, 'affectationResource', true);
    dijit.byId("affectationResource").set('value', idResource);
  }
  dojo.byId("affectationId").value="";
  dojo.byId("affectationIdTeam").value="";
  if (objectClass == 'Project') {
    dijit.byId("affectationProject").set('readOnly', true);
    dijit.byId("affectationResource").set('readOnly', false);
  } else {
    dijit.byId("affectationResource").set('readOnly', true);
    dijit.byId("affectationProject").set('readOnly', false);
  }
  dijit.byId("affectationResource").set('required', true);
  dijit.byId("affectationProfile").set('required', true);
  dijit.byId("affectationRate").set('value', '100');
  dijit.byId("affectationIdle").reset();
  dijit.byId("affectationStartDate").reset();
  dijit.byId("affectationEndDate").reset();
  dijit.byId("affectationDescription").reset();
  dijit.byId("dialogAffectation").show();
  setTimeout("affectationLoad=false", 500);
}

function removeAffectation(id,own) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("affectationId").value=id;
  dojo.byId("affectationIdTeam").value="";
  actionOK=function() {
    loadContent("../tool/removeAffectation.php?confirmed=true", "resultDiv",
        "affectationForm", true, 'affectation');
  };
  if (own) {
    msg=i18n('confirmDeleteOwnAffectation', new Array(id));
  } else {
    msg=i18n('confirmDeleteAffectation', new Array(id));
  }
  showConfirm(msg, actionOK);
}

affectationLoad=false;
function editAffectation(id, objectClass, type, idResource, idProject, rate,
    idle, startDate, endDate, idProfile) {
  affectationLoad=true;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  refreshList('idProfile', 'idProject', idProject, id, 'affectationProfile', false  ); // Attention, selected is given as idAffectation => must seach its profile ...
  disableWidget("affectationDescription");
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=affectationDescription&idAffectation='+id,
    handleAs : "text",
    load : function(data) {
      dijit.byId('affectationDescription').set('value', data);
      enableWidget("affectationDescription");
    }
  });
  if (idProfile) {
    dijit.byId("affectationProfile").set('value', idProfile);
  } else {
    dijit.byId("affectationProfile").reset();
  }
  
  refreshList('idProject', null, null, idProject, 'affectationProject', true);
  if (objectClass == 'Project') {
    refreshList('id' + type, null, null, idResource, 'affectationResource',
        true);
  } else {
    refreshList('id' + objectClass, null, null, idResource,
        'affectationResource', true);
  }
  dijit.byId("affectationResource").set('required', true);
  dojo.byId("affectationId").value=id;
  dojo.byId("affectationIdTeam").value="";
  if (objectClass == 'Project') {
    dijit.byId("affectationProject").set('readOnly', true);
    dijit.byId("affectationProject").set('value', idProject);
    dijit.byId("affectationResource").set('readOnly', false);
    dijit.byId("affectationResource").set('value', idResource);
  } else {
    dijit.byId("affectationResource").set('readOnly', true);
    dijit.byId("affectationResource").set('value', idResource);
    dijit.byId("affectationProject").set('readOnly', false);
    dijit.byId("affectationProject").set('value', idProject);
  }
  if (rate) {
    dijit.byId("affectationRate").set('value', rate);
  } else {
    dijit.byId("affectationRate").reset();
  }
  if (startDate) {
    dijit.byId("affectationStartDate").set('value', startDate);
  } else {
    dijit.byId("affectationStartDate").reset();
  }
  if (endDate) {
    dijit.byId("affectationEndDate").set('value', endDate);
  } else {
    dijit.byId("affectationEndDate").reset();
  }
  if (idle == 1) {
    dijit.byId("affectationIdle").set('value', idle);
  } else {
    dijit.byId("affectationIdle").reset();
  }
  dijit.byId("dialogAffectation").show();
  setTimeout("affectationLoad=false", 500);
}

function saveAffectation() {
  var formVar=dijit.byId('affectationForm');
  if (dijit.byId('affectationStartDate') && dijit.byId('affectationEndDate')) {
    var start=dijit.byId('affectationStartDate').value;
    var end=dijit.byId('affectationEndDate').value;
    if (start && end && dayDiffDates(start, end) < 0) {
      showAlert(i18n("errorStartEndDates", new Array(i18n("colStartDate"),
          i18n("colEndDate"))));
      return;
    }
  }
  if (formVar.validate()) {
    loadContent("../tool/saveAffectation.php", "resultDiv", "affectationForm",
        true, 'affectation');
    dijit.byId('dialogAffectation').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}

function affectTeamMembers(idTeam) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  if (currentSelectedProject=='*') {
    refreshList('idProject', null, null, null, 'affectationProject', false);
    dijit.byId("affectationProject").reset();
  } else {
    refreshList('idProject', null, null, currentSelectedProject, 'affectationProject', false);
    dijit.byId("affectationProject").set('value', currentSelectedProject);
  }
  dojo.byId("affectationId").value="";
  dojo.byId("affectationIdTeam").value=idTeam;
  dijit.byId("affectationResource").set('readOnly', true);
  dijit.byId("affectationResource").set('required', false);
  dijit.byId("affectationResource").reset();
  dijit.byId("affectationProfile").set('readOnly', true);
  dijit.byId("affectationProfile").set('required', false);
  dijit.byId("affectationProfile").reset();
  dijit.byId("affectationProject").set('readOnly', false);
  dijit.byId("affectationRate").set('value', '100');
  dijit.byId("affectationIdle").reset();
  dijit.byId("affectationDescription").reset();
  dijit.byId("affectationIdle").set('readOnly', true);
  dijit.byId("dialogAffectation").show();
}

function affectationChangeResource() {
  var idResource=dijit.byId("affectationResource").get("value");
  if (!idResource)
    return;
  if (affectationLoad)
    return;
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=resourceProfile&idResource='
        + idResource,
    handleAs : "text",
    load : function(data) {
      dijit.byId('affectationProfile').set('value', data);
    }
  });
}

function replaceAffectation (id, objectClass, type, idResource, idProject, rate,
    idle, startDate, endDate, idProfile) {
  var callback=function() {
    refreshList('idProfile', 'idProject', idProject, null, 'replaceAffectationProfile', false  );
  };
  var param="&idAffectation="+id;
  loadDialog("dialogReplaceAffectation", callback, true, param);
}
function replaceAffectationSave() {
  var formVar=dijit.byId('replaceAffectationForm');
  if (dijit.byId('replaceAffectationStartDate') && dijit.byId('replaceAffectationEndDate')) {
    var start=dijit.byId('replaceAffectationStartDate').value;
    var end=dijit.byId('replaceAffectationEndDate').value;
    if (start && end && dayDiffDates(start, end) <= 0) {
      showAlert(i18n("errorStartEndDates", new Array(i18n("colStartDate"),i18n("colEndDate"))));
      return;
    }
  }
  if (dijit.byId('replaceAffectationResource').get("value")==dojo.byId("replaceAffectationExistingResource").value) {
    showAlert(i18n("errorReplaceResourceNotChanged"));
    return;
  }
  if (formVar.validate()) {
    loadContent("../tool/saveAffectationReplacement.php", "resultDiv", "replaceAffectationForm",
        true, 'affectation');
    dijit.byId('dialogReplaceAffectation').hide();
  } else {
    showAlert(i18n("alertInvalidForm"));
  }
}
function replaceAffectationChangeResource() {
  var idResource=dijit.byId("replaceAffectationResource").get("value");
  if (!idResource)
    return;
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=resourceProfile&idResource='
        + idResource,
    handleAs : "text",
    load : function(data) {
      dijit.byId('replaceAffectationProfile').set('value', data);
    }
  });
  dojo.xhrGet({
    url : '../tool/getSingleData.php?dataType=resourceCapacity&idResource='
        + idResource,
    handleAs : "text",
    load : function(data) {
      dijit.byId('replaceAffectationCapacity').set('value', parseFloat(data));
    }
  });
}
// =============================================================================
// = Misceallanous
// =============================================================================

// var manualWindow=null;
function showHelpOld() {
  var objectClass=dojo.byId('objectClass');
  var objectClassManual=dojo.byId('objectClassManual');
  var section='';
  if (objectClassManual) {
    section=objectClassManual.value;
  } else if (objectClass) {
    section=objectClass.value;
  }
  var url='../manual/manual.php?section=' + section;
  var name="Manual";
  var attributes='toolbar=no, titlebar=no, menubar=no, status=no, scrollbars=yes, directories=no, location=no, resizable=yes,'
      + 'height=650, width=1024, top=0, left=0';
  manualWindow=window.open(url, name, attributes);
  manualWindow.focus();
  // manualWindow.window.focus();

  return false;
}
var manualWindow=null;
var helpTimer=false;
function showHelp() {
  if (helpTimer) return; // avoid double open
  helpTimer=true;
  if (manualWindow) manualWindow.close();
  var objectClass=dojo.byId('objectClass');
  var objectClassManual=dojo.byId('objectClassManual');
  var section='';
  if (objectClassManual) {
    section=objectClassManual.value;
  } else if (objectClass) {
    section=objectClass.value;
  }
  dojo.xhrGet({
    url : "../tool/getManualUrl.php?section=" + section,
    handleAs : "text",
    load : function(data, args) {
      var url=data;
      var name="Manual";
      var attributes='toolbar=yes, titlebar=no, menubar=no, status=no, scrollbars=yes, directories=no, location=no, resizable=yes,'
          + 'height=650, width=1024, top=0, left=0';
      manualWindow=window.open(url, name, attributes);
      manualWindow.focus();
    },
    error : function() {
      consoleTraceLog("Error retrieving Manual URL for section '"+section+"'");
    }
  });
  setTimeout("helpTimer=false;",1000);
  return false;
}
/**
 * Refresh a list (after update)
 */
function refreshList(field, param, paramVal, selected, destination, required, param1, paramVal1,objectClass) {
  var urlList='../tool/jsonList.php?listType=list&dataType=' + field;
  if (param) {
    urlList+='&critField=' + param;
    urlList+='&critValue=' + paramVal;
  }
  if (param1) {
    urlList+='&critField1=' + param1;
    urlList+='&critValue1=' + paramVal1;
  }
  if (selected) {
    urlList+='&selected=' + selected;
  }
  if (required) {
    urlList+='&required=true';
  }
  if (objectClass) urlList+='&objectClass='+objectClass;
  var datastore=new dojo.data.ItemFileReadStore({
    url : urlList
  });
  var store=new dojo.store.DataStore({
    store : datastore
  });
  if (destination) {
    var mySelect=dijit.byId(destination);
  } else {
    var mySelect=dijit.byId(field);
  }
  mySelect.set('store', store);
  store.query({
    id : "*"
  }).then(function(items) {
    if (destination) {
      var mySelect=dijit.byId(destination);
    } else {
      var mySelect=dijit.byId(field);
    }
    if (required && ! selected && ! trim(mySelect.get('value')) ) { // required but no value set : select first
      mySelect.set("value", items[0].id);
    }
  });
  
}
function refreshListSpecific(listType, destination, param, paramVal, selected, required) {
  var urlList='../tool/jsonList.php?listType=' + listType;
  if (param) {
    urlList+='&' + param + '=' + paramVal;
  }
  if (selected) {
    urlList+='&selected=' + selected;
  }
  if (required) {
    urlList+='&required=true';
  }
  var datastore=new dojo.data.ItemFileReadStore({
    url : urlList
  });
  var store=new dojo.store.DataStore({
    store : datastore
  });
  store.query({
    id : "*"
  });
  var mySelect=dijit.byId(destination);
  mySelect.set('store', store);
}
function setProductValueFromVersion(field, versionId) {
  // alert("Call : "+field+"/"+versionId);
  dojo.xhrGet({
    url : "../tool/getProductValueFromVersion.php?idVersion=" + versionId,
    handleAs : "text",
    load : function(data, args) {
      prd=dijit.byId(field);
      if (prd) {
        prd.set("value", trim(data));
      }
    },
    error : function() {
    }
  });
}
function setClientValueFromProject(field, projectId) {
  dojo.xhrGet({
    url : "../tool/getClientValueFromProject.php?idProject=" + projectId,
    handleAs : "text",
    load : function(data, args) {
      client=dijit.byId(field);
      if (client && data) {
        client.set("value", data);
      }
    },
    error : function() {
    }
  });
}

var menuHidden=false;
var menuActualStatus='visible';
var menuDivSize=0;
var menuShowMode='CLICK';
var hideShowMenuInProgress=false;
var hideShowTries=0;
/**
 * Hide or show the Menu (left part of the screen
 */
function hideShowMenu(noRefresh) {
  if (!dijit.byId("leftDiv")) {
    return;
  }
  if (!dijit.byId("leftDiv") || !dijit.byId("centerDiv") || !dijit.byId("leftDiv_splitter")) {
    hideShowTries++;
    if (hideShowTries<10) setTimeout("hideShowMenu();",100);
    return;
  }
  hideShowTries=0;
  hideShowMenuInProgress=true;
  duration=300;
  if (menuActualStatus == 'visible' || !menuHidden) {
    menuDivSize=dojo.byId("leftDiv").offsetWidth;
    fullWidth=dojo.byId("mainDiv").offsetWidth;
    if (menuDivSize < 2) {
      menuDivSize=dojo.byId("mainDiv").offsetWidth * .2;
    }
    if (!isHtml5()) {
      duration=0;
      dijit.byId("leftDiv").resize({
        w : 20
      });
      setTimeout("dojo.byId('menuBarShow').style.display='block';", 10);
      // dojo.byId('menuBarShow').style.display='block';
      dojo.byId('leftDiv_splitter').style.display='none';
    } else {
      dojox.fx.combine([ dojox.fx.animateProperty({
        node : "leftDiv",
        properties : {
          width : 20
        },
        duration : duration
      }), dojox.fx.animateProperty({
        node : "centerDiv",
        properties : {
          left : 20,
          width : fullWidth
        },
        duration : duration
      }), dojox.fx.animateProperty({
        node : "leftDiv_splitter",
        properties : {
          left : 20
        },
        duration : duration
      }) ]).play();
      setTimeout("dojo.byId('menuBarShow').style.display='block'", duration);
      setTimeout("dojo.byId('leftDiv_splitter').style.display='none';",
          duration);
    }
    dojo.byId("buttonHideMenuLabel").innerHTML=i18n('buttonShowMenu');
    menuHidden=true;
    menuActualStatus='hidden';
  } else {
    dojo.byId('menuBarShow').style.display='none';
    dojo.byId('leftDiv_splitter').style.left='20px';
    dojo.byId('leftDiv_splitter').style.display='block';
    if (menuDivSize < 20) {
      menuDivSize=dojo.byId("mainDiv").offsetWidth * .2;
    }
    if (!isHtml5()) {
      duration=0;
      dijit.byId("leftDiv").resize({
        w : menuDivSize
      });
    } else {
      dojox.fx.combine([ dojox.fx.animateProperty({
        node : "leftDiv",
        properties : {
          width : menuDivSize
        },
        duration : duration
      }), dojox.fx.animateProperty({
        node : "centerDiv",
        properties : {
          left : menuDivSize + 5
        },
        duration : duration
      }), dojox.fx.animateProperty({
        node : "leftDiv_splitter",
        properties : {
          left : menuDivSize
        },
        duration : duration
      }) ]).play();
    }
    dojo.byId("buttonHideMenuLabel").innerHTML=i18n('buttonHideMenu');
    menuHidden=false;
    menuActualStatus='visible';
  }
  setTimeout('dijit.byId("globalContainer").resize();', duration + 10);
  if (!noRefresh && !formChangeInProgress && dojo.byId('id') && dojo.byId('id').value) {
    setTimeout('loadContent("objectDetail.php", "detailDiv", "listForm");',
        duration + 50);
  }
  setTimeout("hideShowMenuInProgress=false;",duration+50);
  // dojo.byId('menuBarShow').style.top='50px';
}
function tempShowMenu(mode) {
  if (mode == 'mouse' && menuShowMode == 'CLICK')
    return;
  hideShowMenu();
  menuHidden=true;
}
function menuClick() {
  if (menuHidden) {
    menuHidden=false;
    hideShowMenu(true);
    menuHidden=true;
  }
}

var switchedMode=false;
var listDivSize=0;
var switchedVisible='';
var switchListMode='CLICK';
function switchMode() {
  if (!switchedMode) {
    switchedMode=true;
    dojo.byId("buttonSwitchModeLabel").innerHTML=i18n('buttonStandardMode');
    if (!dojo.byId("listDiv")) {
      if (listDivSize == 0) {
        listDivSize=dojo.byId("centerDiv").offsetHeight * .4;
      }
      return;
    } else {
      listDivSize=dojo.byId("listDiv").offsetHeight;
    }
    if (dojo.byId('listDiv_splitter')) {
      dojo.byId('listDiv_splitter').style.display='none';
    }
    if (dijit.byId('id')) {
      hideList();
    } else {
      showList();
    }
  } else {
    switchedMode=false;
    dojo.byId("buttonSwitchModeLabel").innerHTML=i18n('buttonSwitchedMode');
    if (!dojo.byId("listDiv")) {
      return;
    }
    if (dojo.byId('listBarShow')) {
      dojo.byId('listBarShow').style.display='none';
    }
    if (dojo.byId('detailBarShow')) {
      dojo.byId('detailBarShow').style.display='none';
    }
    if (dojo.byId('listDiv_splitter')) {
      dojo.byId('listDiv_splitter').style.display='block';
    }
    if (listDivSize == 0) {
      listDivSize=dojo.byId("centerDiv").offsetHeight * .4;
    }
    dijit.byId("listDiv").resize({
      h : listDivSize
    });
    dijit.byId("mainDivContainer").resize();
  }
}

var switchModeSkipAnimation=true;
function showList(mode, skipAnimation) {
  duration=300;
  if (switchModeSkipAnimation) {
    skipAnimation=true;
    duration=0;
  }
  if (mode == 'mouse' && switchListMode == 'CLICK')
    return;
  if (!switchedMode) {
    return;
  }
  if (!dijit.byId("listDiv") || !dijit.byId("mainDivContainer")) {
    return;
  }
  if (dojo.byId('listDiv_splitter')) {
    setTimeout("dojo.byId('listDiv_splitter').style.display='none';",duration+50);
  }
  if (dojo.byId('listBarShow')) {
    setTimeout("dojo.byId('listBarShow').style.display='none';",duration+50);
  }
  correction=0;
  if (dojo.byId("listDiv").offsetHeight > 100)
    correction=5;
  fullSize=dojo.byId("listDiv").offsetHeight
      + dojo.byId("detailDiv").offsetHeight - 20 + correction;
  if (skipAnimation || !isHtml5()) {
    dijit.byId("listDiv").resize({
      h : fullSize
    });
    duration=0;
  } else {
    dojox.fx.animateProperty({
      node : "listDiv",
      properties : {
        height : fullSize
      },
      duration : duration
    }).play();
  }
  if (dojo.byId('detailBarShow')) {
    setTimeout("dojo.byId('detailBarShow').style.display='block';",
        duration + 50);
  }
  resizeContainer("mainDivContainer", duration);
  switchedVisible='list';
}

function hideList(mode, skipAnimation) {
  duration=300; 
  if (mode == 'mouse' && switchListMode == 'CLICK')
    return;
  if (!switchedMode) {
    return;
  }
  if (!dijit.byId("listDiv") || !dijit.byId("mainDivContainer")) {
    return;
  }
  if (skipAnimation && dijit.byId("detailDiv")) {
    dijit.byId("detailDiv").set('content', '');
  }
  if (switchModeSkipAnimation) {
    skipAnimation=true;
    duration=0;
  }
  if (dojo.byId('listDiv_splitter')) {
    dojo.byId('listDiv_splitter').style.display='none';
  }
  if (dojo.byId('listBarShow')) {
    setTimeout("dojo.byId('listBarShow').style.display='block';",duration+50);
  }
  if (dojo.byId('detailBarShow')) {
    setTimeout("dojo.byId('detailBarShow').style.display='none';",duration+50);
  }
  if (!isHtml5() || skipAnimation) {
    dijit.byId("listDiv").resize({
      h : 20
    });
    duration=0;
  } else {
    dojox.fx.combine([ dojox.fx.animateProperty({
      node : "listDiv",
      properties : {
        height : 20
      },
      duration : duration
    }) ]).play();
  }
  resizeContainer("mainDivContainer", duration);
  switchedVisible='detail';
}

function resizeContainer(container, duration) {
  sequ=10;
  if (duration) {
    for (i=0; i < sequ; i++) {
      setTimeout('dijit.byId("' + container + '").resize();', i * duration / sequ);
    }
  }
  setTimeout('dijit.byId("' + container + '").resize();', duration + 10);
}

function listClick() {
  stockHistory(dojo.byId('objectClass').value, dojo.byId('objectId').value);
  if (!switchedMode) {
    return;
  }
  setTimeout("hideList(null,true);", 1);
}

function stockHistory(curClass, curId) {
  currentScreen="object";
  if (dojo.byId("GanttChartDIV")) {
    currentScreen="planning";
  }
  if (historyPosition>=0) {
    current=historyTable[historyPosition];
    if (current[0]==curClass && current[1]==curId && current[2]==currentScreen) return; // do not re-stock current item
  }
  historyPosition+=1;
  historyTable[historyPosition]=new Array(curClass, curId,currentScreen);
  // Purge next history (not valid any more)
  for (var i=historyPosition+1;i<historyTable.length;i++) {
    historyTable.splice(i,1);
  }
  if (historyPosition > 0) {
    enableWidget('menuBarUndoButton');
  }
  if (historyPosition == historyTable.length - 1) {
    disableWidget('menuBarRedoButton');
  }
}

function undoItemButton() {
  var len=historyTable.length;
  if (len == 0) {
    return;
  }
  if (historyPosition == 0) {
    return;
  }
  historyPosition-=1;
  gotoElement(historyTable[historyPosition][0],
  historyTable[historyPosition][1], true, false, historyTable[historyPosition][2]);
  enableWidget('menuBarRedoButton');
  if (historyPosition == 0) {
    disableWidget('menuBarUndoButton');
  }
}

function redoItemButton() {
  var len=historyTable.length;
  if (len == 0) {
    return;
  }
  if (historyPosition == len - 1) {
    return;
  }
  historyPosition+=1;
  gotoElement(historyTable[historyPosition][0],
      historyTable[historyPosition][1], true, false, historyTable[historyPosition][2]);
  enableWidget('menuBarUndoButton');
  if (historyPosition == (len - 1)) {
    disableWidget('menuBarRedoButton');
  }
}

// Stock id and name, to
// => avoid filterJsonList to reduce visibility => clear this data on open
// => retrieve data before close to retrieve the previous visibility
var quickSearchStockId=null;
var quickSearchStockName=null;
var quickSearchIsOpen=false;

function quickSearchOpen() {
  dojo.style("quickSearchDiv", "display", "block");
  if (dijit.byId("listTypeFilter")) {
    dojo.style("listTypeFilter", "display", "none");
  }
  if (dijit.byId("listClientFilter")) {
    dojo.style("listClientFilter", "display", "none");
  }
  if (dijit.byId("listElementableFilter")) {
    dojo.style("listElementableFilter", "display", "none");
  }
  quickSearchStockId=dijit.byId('listIdFilter').get("value");
  if (dijit.byId('listNameFilter')) {
    quickSearchStockName=dijit.byId('listNameFilter').get("value");
    dojo.style("listNameFilter", "display", "none");
    dijit.byId('listNameFilter').reset();
  }
  dijit.byId('listIdFilter').reset();
  dojo.style("listIdFilter", "display", "none");
  dijit.byId("quickSearchValue").reset();
  dijit.byId("quickSearchValue").focus();
  quickSearchIsOpen=true;
}

function quickSearchClose() {
  quickSearchIsOpen=false;
  dojo.style("quickSearchDiv", "display", "none");
  if (dijit.byId("listTypeFilter")) {
    dojo.style("listTypeFilter", "display", "block");
  }
  if (dijit.byId("listClientFilter")) {
    dojo.style("listClientFilter", "display", "block");
  }
  if (dijit.byId("listElementableFilter")) {
    dojo.style("listElementableFilter", "display", "block");
  }
  dojo.style("listIdFilter", "display", "block");
  if (dijit.byId('listNameFilter')) {
    dojo.style("listNameFilter", "display", "block");
    dijit.byId('listNameFilter').set("value", quickSearchStockName);
  }
  dijit.byId("quickSearchValue").reset();
  dijit.byId('listIdFilter').set("value", quickSearchStockId);
  var objClass=dojo.byId('objectClass').value;
  refreshJsonList(objClass);
}

function quickSearchExecute() {
  if (!quickSearchIsOpen) {
    return;
  }
  if (!dijit.byId("quickSearchValue").get("value")) {
    showInfo(i18n('messageMandatory', new Array(i18n('quickSearch'))));
    return;
  }
  var objClass=dojo.byId('objectClass').value;
  refreshJsonList(objClass);
}

/*
 * ========================================== Copy functions
 */
function copyObject(objectClass) {
  dojo.byId("copyButton").blur();
  action=function() {
    unselectAllRows('objectGrid');
    loadContent("../tool/copyObject.php", "resultDiv", 'objectForm', true);
  };
  showConfirm(i18n("confirmCopy", new Array(i18n(objectClass),
      dojo.byId('id').value)), action);
}

function copyObjectToShowStructure() {
  if (dojo.byId('copyClass').value == 'Activity'
      && copyableArray[dijit.byId('copyToClass').get('value')] == 'Activity') {
    dojo.byId('copyWithStructureDiv').style.display='block';
  } else {
    dojo.byId('copyWithStructureDiv').style.display='none';
  }
}

function copyObjectToSubmit(objectClass) {
  var formVar=dijit.byId('copyForm');
  if (!formVar.validate()) {
    showAlert(i18n("alertInvalidForm"));
    return;
  }
  unselectAllRows('objectGrid');
  loadContent("../tool/copyObjectTo.php", "resultDiv", 'copyForm', true,
      'copyTo');
  dijit.byId('dialogCopy').hide();
}

function copyProjectToSubmit(objectClass) {
  var formVar=dijit.byId('copyProjectForm');
  if (!formVar.validate()) {
    showAlert(i18n("alertInvalidForm"));
    return;
  }
  unselectAllRows('objectGrid');
  loadContent("../tool/copyProjectTo.php", "resultDiv", 'copyProjectForm',
      true, 'copyProject');
  dijit.byId('dialogCopy').hide();
  // dojo.byId('objectClass').value='Project';
}

function copyProjectStructureChange() {
  var cpStr=dijit.byId('copyProjectStructure');
  if (cpStr) {
    if (!cpStr.get('checked')) {
      dijit.byId('copyProjectAssignments').set('checked', false);
      dijit.byId('copyProjectAssignments').set('readOnly', 'readOnly');
    } else {
      dijit.byId('copyProjectAssignments').set('readOnly', false);
    }
  }
}

function loadMenuBarObject(menuClass, itemName, from) {
  if (checkFormChangeInProgress()) {
    return false;
  }
  currentPluginPage=null;
  if (from == 'bar') {
    selectTreeNodeById(dijit.byId('menuTree'), menuClass);
  }
  cleanContent("detailDiv");
  formChangeInProgress=false;
  loadContent("objectMain.php?objectClass=" + menuClass, "centerDiv");
  return true;
}

function loadMenuBarItem(item, itemName, from) {
  if (checkFormChangeInProgress()) {
    return false;
  }
  currentPluginPage=null;
  if (from == 'bar') {
    selectTreeNodeById(dijit.byId('menuTree'), item);
  }
  cleanContent("detailDiv");
  formChangeInProgress=false;
  if (item == 'Today') {
    loadContent("today.php", "centerDiv");
  } else if (item == 'Planning') {
    vGanttCurrentLine=-1;
    cleanContent("centerDiv");
    loadContent("planningMain.php", "centerDiv");
  } else if (item == 'PortfolioPlanning') {
    vGanttCurrentLine=-1;
    cleanContent("centerDiv");
    loadContent("portfolioPlanningMain.php", "centerDiv");
  } else if (item == 'ResourcePlanning') {
    vGanttCurrentLine=-1;
    cleanContent("centerDiv");
    loadContent("resourcePlanningMain.php", "centerDiv");
  } else if (item == 'Imputation') {
    loadContent("imputationMain.php", "centerDiv");
  } else if (item == 'Diary') {
    loadContent("diaryMain.php", "centerDiv");
  } else if (item == 'ImportData') {
    loadContent("importData.php", "centerDiv");
  } else if (item == 'Reports') {
    loadContent("reportsMain.php", "centerDiv");
  } else if (item == 'UserParameter') {
    loadContent("parameter.php?type=userParameter", "centerDiv");
  } else if (item == 'ProjectParameter') {
    loadContent("parameter.php?type=projectParameter", "centerDiv");
  } else if (item == 'GlobalParameter') {
    loadContent("parameter.php?type=globalParameter", "centerDiv");
  } else if (item == 'Habilitation') {
    loadContent("parameter.php?type=habilitation", "centerDiv");
  } else if (item == 'HabilitationReport') {
    loadContent("parameter.php?type=habilitationReport", "centerDiv");
  } else if (item == 'HabilitationOther') {
    loadContent("parameter.php?type=habilitationOther", "centerDiv");
  } else if (item == 'AccessRight') {
    loadContent("parameter.php?type=accessRight", "centerDiv");
  } else if (item == 'AccessRightNoProject') {
    loadContent("parameter.php?type=accessRightNoProject", "centerDiv");
  } else if (item == 'Admin') {
    loadContent("admin.php", "centerDiv");
  } else if (item == 'Plugin' || item == 'PluginManagement') {
    loadContent("pluginManagement.php", "centerDiv");
  } else if (item == 'Calendar') {
    // loadContent("calendar.php","centerDiv");
    loadContent("objectMain.php?objectClass=CalendarDefinition", "centerDiv");
  } else if (item == 'Gallery') {
    loadContent("galleryMain.php", "centerDiv");
  } else if (item == 'DashboardTicket') {
    loadContent("dashboardTicketMain.php", "centerDiv");
  } else if (pluginMenuPage && pluginMenuPage['menu'+item]) {
    loadMenuBarPlugin(item, itemName, from);
  } else {  
    showInfo(i18n("messageSelectedNotAvailable", new Array(itemName)));
  }
  return true;
}

var currentPluginPage=null;
function loadMenuBarPlugin(item, itemName, from) {
  if (checkFormChangeInProgress()) {
    return false;
  }
  if (! pluginMenuPage || ! pluginMenuPage['menu'+item]) {
    showInfo(i18n("messageSelectedNotAvailable", new Array(item.name)));
    return;
  }
  currentPluginPage=pluginMenuPage['menu'+item];
  loadContent(pluginMenuPage['menu'+item], "centerDiv");
}

var customMenuAddRemoveTimeout=null;
var customMenuAddRemoveTimeoutDelay=3000;
var customMenuAddRemoveClass=null;
function customMenuManagement(menuClass) {
  var button=dojo.byId(menuClass);
  offsetbutton=button.offsetLeft+dojo.byId('menuBarVisibleDiv').offsetLeft+dojo.byId('menubarContainer').offsetLeft;
  if ( dojo.hasClass(button,'menuBarCustom') ) {
    clearTimeout(customMenuAddRemoveTimeout);
    dojo.byId('customMenuAdd').style.display='none';
    customMenuAddRemoveClass=menuClass;
    dojo.byId('customMenuRemove').style.left=offsetbutton+'px';
    dojo.byId('customMenuRemove').style.display='block';
    customMenuAddRemoveTimeout=setTimeout("dojo.byId('customMenuRemove').style.display='none';",customMenuAddRemoveTimeoutDelay);
  } else {
    clearTimeout(customMenuAddRemoveTimeout);
    dojo.byId('customMenuRemove').style.display='none';
    customMenuAddRemoveClass=menuClass;
    dojo.byId('customMenuAdd').style.left=offsetbutton+'px';
    dojo.byId('customMenuAdd').style.display='block';
    customMenuAddRemoveTimeout=setTimeout("dojo.byId('customMenuAdd').style.display='none';",customMenuAddRemoveTimeoutDelay);
  }
}
function customMenuAddItem() {
  var param="?operation=add&class="+customMenuAddRemoveClass;
  dojo.xhrGet({
    url : "../tool/saveCustomMenu.php"+param,
    handleAs : "text",
    load : function(data, args) {
    },
  });
  dojo.addClass(customMenuAddRemoveClass,'menuBarCustom');
  dojo.byId('customMenuAdd').style.display='none';
}
function customMenuRemoveItem() {
  var param="?operation=remove&class="+customMenuAddRemoveClass;
  dojo.xhrGet({
    url : "../tool/saveCustomMenu.php"+param,
    handleAs : "text",
    load : function(data, args) {
      if (data=='menuBarCustom') {
        dojo.byId(customMenuAddRemoveClass).style.display="none";
      }
    },
  });
  dojo.removeClass(customMenuAddRemoveClass,'menuBarCustom');
  dojo.byId('customMenuRemove').style.display='none';
}
// ====================================================================================
// ALERTS
// ====================================================================================
//
// var alertDisplayed=false;
function checkAlert() {
  // if (alertDisplayed) return;
  dojo.xhrGet({
    url : "../tool/checkAlertToDisplay.php",
    handleAs : "text",
    load : function(data, args) {
      checkAlertRetour(data);
    },
    error : function() {
      setTimeout('checkAlert();', alertCheckTime * 1000);
    }
  });
}
function checkAlertRetour(data) {
  if (data) {
    var reminderDiv=dojo.byId('reminderDiv');
    var dialogReminder=dojo.byId('dialogReminder');
    reminderDiv.innerHTML=data;
    if (dojo.byId("requestRefreshProject")
        && dojo.byId("requestRefreshProject").value == "true") {
      refreshProjectSelectorList();
      setTimeout('checkAlert();', alertCheckTime * 1000);
    } else if (dojo.byId('alertType')) {
      if (dojo.byId('alertCount') && dojo.byId('alertCount').value>1) {
        dijit.byId('markAllAsReadButton').set('label',i18n('markAllAsRead',new Array(dojo.byId('alertCount').value)));
        dojo.byId("markAllAsReadButtonDiv").style.display="inline";
      } else {
        dojo.byId("markAllAsReadButtonDiv").style.display="none";
      }
      dojo.style(dialogReminder, {
        visibility : 'visible',
        display : 'inline',
        bottom : '-200px'
      });
      var toColor='#FFCCCC';
      if (dojo.byId('alertType') && dojo.byId('alertType').value == 'WARNING') {
        toColor='#FFFFCC';
      }
      if (dojo.byId('alertType') && dojo.byId('alertType').value == 'INFO') {
        toColor='#CCCCFF';
      }
      dojo.animateProperty({
        node : dialogReminder,
        properties : {
          bottom : {
            start : -200,
            end : 0
          },
          right : 0,
          backgroundColor : {
            start : '#FFFFFF',
            end : toColor
          }
        },
        duration : 2000
      }).play();
    }
  } else {
    setTimeout('checkAlert();', alertCheckTime * 1000);
  }
}
function setAlertReadMessage() {
  // alertDisplayed=false;
  closeAlertBox();
  if (dojo.byId('idAlert') && dojo.byId('idAlert').value) {
    setAlertRead(dojo.byId('idAlert').value);
  }
}
function setAllAlertReadMessage() {
  // alertDisplayed=false;
  closeAlertBox();
  setAlertRead('*');
}
function setAlertReadMessageInForm() {
  dijit.byId('readFlag').set('checked', 'checked');
  submitForm("../tool/saveObject.php", "resultDiv", "objectForm", true);
}
function setAlertRemindMessage() {
  closeAlertBox();
  if (dojo.byId('idAlert') && dojo.byId('idAlert').value) {
    setAlertRead(dojo.byId('idAlert').value, dijit.byId('remindAlertTime').get(
        'value'));
  }
}

function setAlertRead(id, remind) {
  var url="../tool/setAlertRead.php?idAlert=" + id;
  if (remind) {
    url+='&remind=' + remind;
  }
  dojo.xhrGet({
    url : url,
    handleAs : "text",
    load : function(data, args) {
      setTimeout('checkAlert();', 1000);
    },
    error : function() {
      setTimeout('checkAlert();', 1000);
    }
  });
}

function closeAlertBox() {
  var dialogReminder=dojo.byId('dialogReminder');
  dojo.animateProperty({
    node : dialogReminder,
    properties : {
      bottom : {
        start : 0,
        end : -200
      }
    },
    duration : 900,
    onEnd : function() {
      dojo.style(dialogReminder, {
        visibility : 'hidden',
        display : 'none',
        bottom : '-200px'
      });
    }
  }).play();
}

// ===========================================================================================
// ADMIN functionalities
// ===========================================================================================
//
var cronCheckIteration=5; // Number of cronCheckTimeout to way max
function adminLaunchScript(scriptName,needRefresh) {
  if(typeof needRefresh == 'undefined')needRefresh=true;
  var url="../tool/" + scriptName + ".php";
  dojo.xhrGet({
    url : url,
    handleAs : "text",
    load : function(data, args) {
      
    },
    error : function() {
    }
  });
  if (scriptName == 'cronRun') {
    if(needRefresh)setTimeout('loadContent("admin.php","centerDiv");', 1000);
  } else if (scriptName == 'cronStop' && needRefresh) {
    i=120;
    cronCheckIteration=5;
    setTimeout('adminCronCheckStop();', 1000 * cronSleepTime);
  }
}

function adminCronCheckStop() {
  dojo.xhrGet({
    url : "../tool/cronCheck.php",
    handleAs : "text",
    load : function(data, args) {
      if (data != 'running') {
        loadContent("admin.php", "centerDiv");
      } else {
        cronCheckIteration--;
        if (cronCheckIteration > 0) {
          setTimeout('adminCronCheckStop();', 1000 * cronSleepTime);
        } else {
          loadContent("admin.php", "centerDiv");
        }
      }
    },
    error : function() {
      loadContent("admin.php", "centerDiv");
    }
  });
}

function adminCronRelaunch() {
  var url="../tool/cronRelaunch.php";
  dojo.xhrGet({
    url : url,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
}

function adminSendAlert() {
  formVar=dijit.byId("adminForm");
  if (formVar.validate()) {
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=sendAlert",
        "resultDiv", "adminForm", true, 'admin');
  }
}

function adminDisconnectAll() {
  actionOK=function() {
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=disconnectAll&element=Audit",
        "resultDiv", "adminForm", true, 'admin');
  };
  msg=i18n('confirmDisconnectAll');
  showConfirm(msg, actionOK);
}

function maintenance(operation, item) {
  if (operation == "updateReference") {
    loadContent("../tool/adminFunctionalities.php?adminFunctionality="
        + operation + "&element=" + item, "resultDiv", "adminForm", true,
        'admin');
  } else {
    var nb=0;
    if (operation!='read') {
      nb=dijit.byId(operation + item + "Days").get('value');
    }
    loadContent(
        "../tool/adminFunctionalities.php?adminFunctionality=maintenance&operation="
            + operation + "&item=" + item + "&nbDays=" + nb, "resultDiv",
        "adminForm", true, 'admin');
  }
}
function adminSetApplicationTo(newStatus) {
  var url="../tool/adminFunctionalities.php?adminFunctionality=setApplicationStatusTo&newStatus="
      + newStatus;
  showWait();
  dojo.xhrPost({
    url : url,
    form : "adminForm",
    handleAs : "text",
    load : function(data, args) {
      loadContent("../view/admin.php", "centerDiv")
    },
    error : function() {
    }
  });
}

function lockDocument() {
  if (checkFormChangeInProgress()) {
    return false;
  }
  dijit.byId('locked').set('checked', true);
  dijit.byId('idLocker').set('value', dojo.byId('idCurrentUser').value);
  var curDate=new Date();
  dijit.byId('lockedDate').set('value', curDate);
  dijit.byId('lockedDateBis').set('value', curDate);
  formChanged();
  submitForm("../tool/saveObject.php", "resultDiv", "objectForm", true);
  return true;
}

function unlockDocument() {
  if (checkFormChangeInProgress()) {
    return false;
  }
  dijit.byId('locked').set('checked', false);
  dijit.byId('idLocker').set('value', null);
  dijit.byId('lockedDate').set('value', null);
  dijit.byId('lockedDateBis').set('value', null);
  formChanged();
  submitForm("../tool/saveObject.php", "resultDiv", "objectForm", true);
  return true;
}

/*
 * ========================================================================
 * Planning columns management
 * ========================================================================
 */
function openPlanningColumnMgt() {
  // alert("openPlanningColumnMgt");
}

function changePlanningColumn(col, status, order) {
  if (status) {
    // order=planningColumnOrder.indexOf('Hidden'+col);
    order=dojo.indexOf(planningColumnOrder, 'Hidden' + col);
    planningColumnOrder[order]=col;
    movePlanningColumn(col, col);
  } else {
    // order=planningColumnOrder.indexOf(col);
    order=dojo.indexOf(planningColumnOrder, col);
    planningColumnOrder[order]='Hidden' + col;
  }
  if (col=='IdStatus' || col=='Type') {
    validatePlanningColumnNeedRefresh=true;
  }
  setPlanningFieldShow(col,status);
  dojo.xhrGet({
    url : '../tool/savePlanningColumn.php?action=status&status='
        + ((status) ? 'visible' : 'hidden') + '&item=' + col,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
}
function changePlanningColumnWidth(col, width) {
  setPlanningFieldWidth(col,width);
  showWait();
  JSGantt.changeFormat(g.getFormat(), g);
  dojo.xhrGet({
    url : '../tool/savePlanningColumn.php?action=width&width='+width+'&item=' + col,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
  hideWait();
}
var validatePlanningColumnNeedRefresh=false;
function validatePlanningColumn() {
  dijit.byId('planningColumnSelector').closeDropDown();
  showWait();
  setGanttVisibility(g);
  if (validatePlanningColumnNeedRefresh) { 
    refreshJsonPlanning();
    
  } else {
    JSGantt.changeFormat(g.getFormat(), g);
    hideWait();
  }
  validatePlanningColumnNeedRefresh=false;
}

function movePlanningColumn(source, destination) {
  var mode='';
  var list='';
  var nodeList=dndPlanningColumnSelector.getAllNodes();
  planningColumnOrder=new Array();
  for (i=0; i < nodeList.length; i++) {
    var itemSelected=nodeList[i].id.substr(14);
    check=(dijit.byId('checkColumnSelector' + itemSelected).get('checked')) ? ''
        : 'hidden';
    list+=itemSelected + "|";
    planningColumnOrder[i]=check + itemSelected;
  }
  var url='../tool/movePlanningColumn.php?orderedList=' + list;
  dojo.xhrPost({
    url : url,
    handleAs : "text",
    load : function(data, args) {
    }
  });
  // loadContent(url, "planResultDiv");
}

/*
 * ======================================================================== List
 * columns management
 * ========================================================================
 */

function changeListColumn(tableId, fieldId, status, order) {
  var spinner=dijit.byId('checkListColumnSelectorWidthId' + fieldId);
  spinner.set('disabled', !status);
  dojo.xhrGet({
    url : '../tool/saveSelectedColumn.php?action=status&status='
        + ((status) ? 'visible' : 'hidden') + '&item=' + tableId,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
  recalculateColumnSelectorName();
}

function changeListColumnWidth(tableId, fieldId, width) {
  if (width < 1) {
    width=1;
    dijit.byId('checkListColumnSelectorWidthId' + fieldId).set('value', width);
  } else if (width > 50) {
    width=50;
    dijit.byId('checkListColumnSelectorWidthId' + fieldId).set('value', width);
  }
  dojo.xhrGet({
    url : '../tool/saveSelectedColumn.php?action=width&item=' + tableId
        + '&width=' + width,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
  recalculateColumnSelectorName();
}

function validateListColumn() {
  showWait();
  dijit.byId('listColumnSelector').closeDropDown();
  loadContent("objectList.php?objectClass=" + dojo.byId('objectClass').value
      + "&objectId=" + dojo.byId('objectId').value, "listDiv");
}

function resetListColumn() {
  var actionOK=function() {
    showWait();
    dijit.byId('listColumnSelector').closeDropDown();
    dojo.xhrGet({
      url : '../tool/saveSelectedColumn.php?action=reset&objectClass='
          + dojo.byId('objectClass').value,
      handleAs : "text",
      load : function(data, args) {
        loadContent("objectList.php?objectClass="
            + dojo.byId('objectClass').value + "&objectId="
            + dojo.byId('objectId').value, "listDiv");
      },
      error : function() {
      }
    });
  };
  showConfirm(i18n('confirmResetList'), actionOK);
}

function moveListColumn(source, destination) {
  var mode='';
  var list='';
  var nodeList=dndListColumnSelector.getAllNodes();
  listColumnOrder=new Array();
  for (i=0; i < nodeList.length; i++) {
    var itemSelected=nodeList[i].id.substr(20);
    // check=(dijit.byId('checkListColumnSelector'+itemSelected).get('checked'))?'':'hidden';
    list+=itemSelected + "|";
    // listColumnOrder[i]=check+itemSelected;
  }
  // dijit.byId('listColumnSelector').closeDropDown();
  var url='../tool/moveListColumn.php?orderedList=' + list;
  dojo.xhrPost({
    url : url,
    handleAs : "text",
    load : function(data, args) {
    }
  });
  // loadContent(url, "planResultDiv");
  // setGanttVisibility(g);
  // JSGantt.changeFormat(g.getFormat(),g);
  // hideWait();
}

function recalculateColumnSelectorName() {
  cpt=0;
  tot=0;
  while (cpt < 999) {
    var itemSelected=dijit.byId('checkListColumnSelectorWidthId' + cpt);
    if (itemSelected) {
      if (!itemSelected.get('disabled')) {
        tot+=itemSelected.get('value');
      }
    } else {
      cpt=999;
    }
    cpt++;
  }
  name="checkListColumnSelectorWidthId"
      + dojo.byId('columnSelectorNameFieldId').value;
  nameWidth=100 - tot;
  color="";
  if (nameWidth < 10) {
    nameWidth=10;
    color="#FFAAAA";
  }
  dijit.byId(name).set('value', nameWidth);
  totWidth=tot + nameWidth;
  totWidthDisplay="";
  if (color) {
    totWidthDisplay='<div style="background-color:' + color + '">' + totWidth
        + '&nbsp;%</div>';
  }
  dojo.byId('columnSelectorTotWidthTop').innerHTML=totWidthDisplay;
  dojo.byId('columnSelectorTotWidthBottom').innerHTML=totWidthDisplay;
  dojo.xhrGet({
    url : '../tool/saveSelectedColumn.php?action=width&item='
        + dojo.byId('columnSelectorNameTableId').value + '&width=' + nameWidth,
    handleAs : "text",
    load : function(data, args) {
    },
    error : function() {
    }
  });
}

// =========================================================
// Other
// =========================================================
function showMailOptions() {
  var callback=function() {
    dojo.byId('mailRefType').value=dojo.byId('objectClass').value;
    dojo.byId('mailRefId').value=dojo.byId('objectId').value;
    title=i18n('buttonMail', new Array(i18n(dojo.byId('objectClass').value)));
    if (dijit.byId('attendees')) {
      dijit.byId('dialogMailToOther').set('checked', 'checked');
      dijit.byId('dialogOtherMail').set('value',
          extractEmails(dijit.byId('attendees').get('value')));
      dialogMailToOtherChange();
    }
    // if (dojo.byId('objectClass').value=='Activity') {
    // enableWidget('dialogMailToAssigned');
    // } else {
    // disableWidget('dialogMailToAssigned');
    // dijit.byId('dialogMailToAssigned').set('checked','');
    // }
    dijit.byId("dialogMail").set('title', title);
    dijit.byId("dialogMail").show();
  }
  if (dijit.byId("dialogMail")
      && dojo.byId('dialogMailObjectClass')
      && dojo.byId('dialogMailObjectClass').value == dojo.byId("objectClass").value) {
    dojo.byId('mailRefType').value=dojo.byId('objectClass').value;
    dojo.byId('mailRefId').value=dojo.byId('objectId').value;
    dijit.byId("dialogMail").show();
  } else {
    var param="&objectClass=" + dojo.byId("objectClass").value;
    loadDialog("dialogMail", callback, false, param);
  }

}

function dialogMailToOtherChange() {
  var show=dijit.byId('dialogMailToOther').get('checked');
  if (show) {
    showField('dialogOtherMail');
  } else {
    hideField('dialogOtherMail');
  }
}

function extractEmails(str) {
  var current='';
  var result='';
  var name=false;
  for (i=0; i < str.length; i++) {
    car=str.charAt(i);
    if (car == '"') {
      if (name == true) {
        name=false;
        current="";
      } else {
        if (current != '') {
          if ($result != '') {
            result+=', ';
          }
          result+=trimTag(current);
          current='';
        }
        name=true;
      }
    } else if (name == false) {
      if (car == ',' || car == ';' || car == ' ') {
        if (current != '') {
          if (result != '') {
            result+=', ';
          }
          result+=trimTag(current);
          current='';
        }
      } else {
        current+=car;
      }
    }
  }
  if (current != "") {
    if (result != '') {
      result+=', ';
    }
    result+=trimTag(current);
  }
  return result;
}

function sendMail() {
  loadContent("../tool/sendMail.php?className=Mailable", "resultDiv",
      "mailForm", true, 'mail');
  dijit.byId("dialogMail").hide();
}
//gautier ticket #2096
function assignTeamForMeeting() {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  dojo.byId("assignmentId").value=null;
  dojo.byId("assignmentRefType").value=dojo.byId("objectClass").value;
  dojo.byId("assignmentRefId").value=dojo.byId("objectId").value;
  actionOK=function() {
    loadContent("../tool/assignTeamForMeeting.php","resultDiv", "assignmentForm",
        true, 'assignment');
  };
  msg=i18n('confirmAssignWholeTeam');
  showConfirm(msg, actionOK);
  
}
function lockRequirement() {
  if (checkFormChangeInProgress()) {
    return false;
  }
  dijit.byId('locked').set('checked', true);
  dijit.byId('idLocker').set('value', dojo.byId('idCurrentUser').value);
  var curDate=new Date();
  dijit.byId('lockedDate').set('value', curDate);
  dijit.byId('lockedDateBis').set('value', curDate);
  formChanged();
  submitForm("../tool/saveObject.php", "resultDiv", "objectForm", true);
  return true;
}

function unlockRequirement() {
  if (checkFormChangeInProgress()) {
    return false;
  }
  dijit.byId('locked').set('checked', false);
  dijit.byId('idLocker').set('value', null);
  dijit.byId('lockedDate').set('value', null);
  dijit.byId('lockedDateBis').set('value', null);
  formChanged();
  submitForm("../tool/saveObject.php", "resultDiv", "objectForm", true);
  return true;
}

function loadDialog(dialogDiv, callBack, autoShow, params, clearOnHide, closable) {
  if(typeof closable =='undefined')closable=true;
  var hideCallback=function() {
  };
  if (clearOnHide) {
    hideCallback=function() {
      dijit.byId(dialogDiv).set('content', null);
    };
  }
  extraClass="projeqtorDialogClass";
  if (dialogDiv=="dialogLogfile") {
    extraClass="logFile";
  }
  if (!dijit.byId(dialogDiv)) {
    dialog=new dijit.Dialog({
      id : dialogDiv,
      title : i18n(dialogDiv),
      width : '500px',
      onHide : hideCallback,
      content : i18n("loading"),
      'class' : extraClass,
      closable : closable
    });
  } else {
    dialog=dijit.byId(dialogDiv);
  }
  if (!params) {
    params="";
  }
  showWait();
  dojo.xhrGet({
    url : '../tool/dynamicDialog.php?dialog=' + dialogDiv + '&isIE='
        + ((dojo.isIE) ? dojo.isIE : '') + params,
    handleAs : "text",
    load : function(data) {
      var contentWidget=dijit.byId(dialogDiv);
      contentWidget.set('content', data);
      if (autoShow) {
        setTimeout("dijit.byId('" + dialogDiv + "').show();", 100);
      }
      hideWait();
      if (callBack) {
        setTimeout(callBack, 10);
      }
    },
    error : function() {
      consoleTraceLog("error loading dialog " + dialogDiv);
      hideWait();
    }
  });
}
/*
 * ========================================================================
 * Today management
 * ========================================================================
 */
function saveTodayParameters() {
  loadContent('../tool/saveTodayParameters.php', 'centerDiv',
      'todayParametersForm');
  dijit.byId('dialogTodayParameters').hide();
}

function setTodayParameterDeleted(id) {
  dojo.byId('dialogTodayParametersDelete' + id).value=1;
  dojo.byId('dialogTodayParametersRow' + id).style.display='none';
}

function loadReport(url, dialogDiv) {
  var contentWidget=dijit.byId(dialogDiv);
  contentWidget.set('content',
      '<img src="../view/css/images/treeExpand_loading.gif" />');
  dojo.xhrGet({
    url : url,
    handleAs : "text",
    load : function(data) {
      var contentWidget=dijit.byId(dialogDiv);
      if (!contentWidget) {
        return;
      }
      contentWidget.set('content', data);
    },
    error : function() {
      consoleTraceLog("error loading report " + url + " into " + dialogDiv);
    }
  });
}

function reorderTodayItems() {
  var nodeList=dndTodayParameters.getAllNodes();
  for (i=0; i < nodeList.length; i++) {
    var item=nodeList[i].id.substr(24);
    var order=dojo.byId("dialogTodayParametersOrder" + item);
    if (order) {
      order.value=i + 1;
    }
  }
}

var multiSelection=false;
var switchedModeBeforeMultiSelection=false;
function startMultipleUpdateMode(objectClass) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  grid=dijit.byId("objectGrid"); // if the element is not a widget, exit.
  if (!grid) {
    return;
  }
  multiSelection=true;
  // dojo.xhrPost({url:
  // "../tool/saveDataToSession.php?idData=multipleMode&value=true"});
  formChangeInProgress=true;
  switchedModeBeforeMultiSelection=switchedMode;
  if (switchedModeBeforeMultiSelection) {
    switchMode();
  }
  unselectAllRows("objectGrid");
  dijit.byId('objectGrid').selection.setMode('multiple');
  loadContent('../view/objectMultipleUpdate.php?objectClass=' + objectClass,
      'detailDiv')
}

function saveMultipleUpdateMode(objectClass) {
  // submitForm("../tool/saveObject.php","resultDiv", "objectForm", true);
  grid=dijit.byId("objectGrid"); // if the element is not a widget, exit.
  if (!grid) {
    return;
  }
  dojo.byId("selection").value=""
  var items=grid.selection.getSelected();
  if (items.length) {
    dojo.forEach(items, function(selectedItem) {
      if (selectedItem !== null) {
        dojo.byId("selection").value+=parseInt(selectedItem.id) + ";";
      }
    });
  }
  loadContent('../tool/saveObjectMultiple.php?objectClass=' + objectClass,
      'resultDivMultiple', 'objectFormMultiple');
}

function endMultipleUpdateMode(objectClass) {
  if (dijit.byId('objectGrid')) {
    dijit.byId('objectGrid').selection.setMode('single');
    unselectAllRows("objectGrid");
  }
  multiSelection=false;
  // dojo.xhrPost({url:
  // "../tool/saveDataToSession.php?idData=multipleMode&value=false"});
  formChangeInProgress=false;
  if (switchedModeBeforeMultiSelection) {
    switchMode();
  }
  if (objectClass) {
    loadContent('../view/objectDetail.php?noselect=true&objectClass='
        + objectClass, 'detailDiv');
  }
}

function deleteMultipleUpdateMode(objectClass) {
  grid=dijit.byId("objectGrid"); // if the element is not a widget, exit.
  if (!grid) {
    return;
  }
  dojo.byId("selection").value=""
  var items=grid.selection.getSelected();
  if (items.length) {
    dojo.forEach(items, function(selectedItem) {
      if (selectedItem !== null) {
        dojo.byId("selection").value+=parseInt(selectedItem.id) + ";";
      }
    });
  }
  actionOK=function() {
    actionOK2=function() {
      if (dijit.byId('deleteMultipleResultDiv').get('content')!='') {
        showConfirm(dijit.byId('deleteMultipleResultDiv').get('content'), function(){loadContent('../tool/deleteObjectMultiple.php?objectClass=' + objectClass,
          'resultDivMultiple', 'objectFormMultiple');});
      } else {
        loadContent('../tool/deleteObjectMultiple.php?objectClass=' + objectClass,
            'resultDivMultiple', 'objectFormMultiple');
      } 
    };
    setTimeout(function(){
      loadContent('../tool/deleteObjectMultipleControl.php?objectClass=' + objectClass,
          'deleteMultipleResultDiv', 'objectFormMultiple',null,null,null,null,actionOK2);
    },200);
  };
  msg=i18n('confirmDeleteMultiple', new Array(i18n('menu' + objectClass),
      items.length));
  showConfirm(msg, actionOK);
}
function updateSelectedCountMultiple() {
  if (dojo.byId('selectedCount')) {
    dojo.byId('selectedCount').value=countSelectedItem('objectGrid');
  }
}

function showImage(objectClass, objectId, imageName) {
  if (objectClass == 'Affectable' || objectClass == 'Resource'
      || objectClass == 'User' || objectClass == 'Contact') {
    imageUrl="../files/thumbs/Affectable_" + objectId + "/thumb80.png";
  } else {
    imageUrl="../tool/download.php?class=" + objectClass + "&id=" + objectId;
  }
  var dialogShowImage=dijit.byId("dialogShowImage");
  if (!dialogShowImage) {
    dialogShowImage=new dojox.image.LightboxDialog({});
    dialogShowImage.startup();
  }
  if (dialogShowImage && dialogShowImage.show) {
    if (dojo.isFF) {
      dojo.xhrGet({
        url : imageUrl,
        handleAs : "text",
        load : function(data) {
          dialogShowImage.show({
            title : imageName,
            href : imageUrl
          });
          dijit.byId('formDiv').resize();
        }
      });
    } else {
      dialogShowImage.show({
        title : imageName,
        href : imageUrl
      });
      dijit.byId('formDiv').resize();
    }
    // dialogShowImage.show({ title:imageName, href:imageUrl });
  } else {
    showError("Error loading image " + imageName);
  }
  // dijit.byId('formDiv').resize();
}
function showBigImage(objectClass, objectId, node, title, hideImage, nocache) {
  var top=node.getBoundingClientRect().top;
  var left=node.getBoundingClientRect().left;
  var height=node.getBoundingClientRect().height;
  var width=node.getBoundingClientRect().width;
  if (!height)
    height=40;
  if (objectClass == 'Affectable' || objectClass == 'Resource'
      || objectClass == 'User' || objectClass == 'Contact') {
    imageUrl="../files/thumbs/Affectable_" + objectId + "/thumb80.png";
    if (nocache) {
      imageUrl+=nocache;
    }
  } else {
    imageUrl="../tool/download.php?class=" + objectClass + "&id=" + objectId;
  }
  var centerThumb80=dojo.byId("centerThumb80");
  if (centerThumb80) {
    var htmlPhoto='';
    var alone='';
    if (objectClass && objectId && !hideImage) {
      htmlPhoto='<img style="border-radius:40px;" src="' + imageUrl + '" />';
    } else {
      alone='Alone';
    }
    if (title) {
      htmlPhoto+='<div class="thumbBigImageTitle' + alone + '">' + title
          + '</div>';
    }
    var topPx=(top - 40 + (height / 2)) + "px";
    var leftPx=(left - 85) + "px";
    if (parseInt(leftPx)<3) {
      leftPx=(left+width+5)+"px";
    }
    centerThumb80.innerHTML=htmlPhoto;
    centerThumb80.style.top=topPx;
    centerThumb80.style.left=leftPx;
    centerThumb80.style.display="block";
  }
}
function hideBigImage(objectClass, objectId) {
  var centerThumb80=dojo.byId("centerThumb80");
  if (centerThumb80) {
    centerThumb80.innerHTML="";
    centerThumb80.style.display="none";
  }
}

function showLink(link) {
  // window.frames['showHtmlFrame'].location.href='../view/preparePreview.php';
  dijit.byId("dialogShowHtml").title=link;
  window.frames['showHtmlFrame'].location.href=link;
  dijit.byId("dialogShowHtml").show();
  window.frames['showHtmlFrame'].focus();
}
function showHtml(id, file, className) {
  dijit.byId("dialogShowHtml").title=file;
  window.frames['showHtmlFrame'].location.href='../tool/download.php?class='+className+'&id='
      + id + '&showHtml=true';
  dijit.byId("dialogShowHtml").show();
  window.frames['showHtmlFrame'].focus();
} 

// *******************************************************
// Dojo code to position into a tree
// *******************************************************
function recursiveHunt(lookfor, model, buildme, item) {
  var id=model.getIdentity(item);
  buildme.push(id);
  if (id == lookfor) {
    return buildme;
  }
  for ( var idx in item.children) {
    var buildmebranch=buildme.slice(0);
    var r=recursiveHunt(lookfor, model, buildmebranch, item.children[idx]);
    if (r) {
      return r;
    }
  }
  return undefined;
}

function selectTreeNodeById(tree, lookfor) {
  var buildme=[];
  var result=recursiveHunt(lookfor, tree.model, buildme, tree.model.root);
  if (result && result.length > 0) {
    tree.set('path', result);
  }
}

// ************************************************************
// Code to select columns to be exported
// ************************************************************
var ExportType='';
// open the dialog with checkboxes
function openExportDialog(Type) {
  ExportType=Type;
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  loadDialog("dialogExport", null, true, '&objectClass='
      + dojo.byId('objectClass').value);
}

// close the dialog with checkboxes
function closeExportDialog() {
  dijit.byId("dialogExport").hide();
}

// save current state of checkboxes
function saveCheckboxExport(obj, idUser) {
  var val=dojo.byId('column0').value;
  var toStore="";
  val=eval(val);
  for (i=1; i <= val; i++) {
    var checkbox=dijit.byId('column' + i);
    if (checkbox) {
      if (!checkbox.get('checked')) {
        var field=checkbox.value;
        toStore+=field + ";";
      }
    }
  }
  dojo.xhrPost({
    url : "../tool/saveCheckboxes.php?&objectClass=" + obj + "&toStore="
        + toStore,
    handleAs : "text",
    load : function() {
    }
  });
}

// Executes the report (shows the print/pdf/csv)
function executeExport(obj, idUser) {
  var verif=0;
  var val=dojo.byId('column0').value;
  var exportReferencesAs=dijit.byId('exportReferencesAs').get('value');
  var exportHtml=(dijit.byId('exportHtml').get('checked'))?'1':'0';
  val=eval(val);
  var toExport="";
  for (i=1; i <= val; i++) {
    var checkbox=dijit.byId('column' + i);
    if (checkbox) {
      if (checkbox.get('checked')) {
        verif=1;
      } else {
        var field=checkbox.value;
        toExport+=field + ";";
      }
    }
  }
  if (verif == 1) {
    if (ExportType == 'csv') {
      showPrint("../tool/jsonQuery.php?exportHtml="+exportHtml+
          "&exportReferencesAs="+ exportReferencesAs + "&hiddenFields=" + toExport, 'list', null,
          'csv');
    }
    saveCheckboxExport(obj, idUser);
    closeExportDialog(obj, idUser);
  } else {
    showAlert(i18n('alertChooseOneAtLeast'));
  }
}

// Check or uncheck all boxes
function checkExportColumns(scope) {
  if (scope == 'aslist') {
    showWait();
    dojo.xhrGet({
      url : "../tool/getColumnsList.php?objectClass="
          + dojo.byId('objectClass').value,
      load : function(data) {
        var list=";" + data;
        var val=dojo.byId('column0').value;
        val=eval(val);
        var allChecked=true;
        for (i=1; i <= val; i++) {
          var checkbox=dijit.byId('column' + i);
          if (checkbox) {
            var search=";" + checkbox.value + ";";
            if (list.indexOf(search) >= 0) {
              checkbox.set('checked', true);
            } else {
              checkbox.set('checked', false);
              allChecked=false;
            }
          }
        }
        dijit.byId('checkUncheck').set('checked', allChecked);
        hideWait();
      },
      error : function() {
        hideWait();
      }
    });
  } else {
    var check=dijit.byId('checkUncheck').get('checked');
    var val=dojo.byId('column0').value;
    val=eval(val);
    for (i=1; i <= val; i++) {
      var checkbox=dijit.byId('column' + i);
      if (checkbox) {
        checkbox.set('checked', check);
      }
    }
  }
}

// ==================================================================
// Project Selector Functions
// ==================================================================
function changeProjectSelectorType(displayMode) {
  dojo.xhrPost({
        url : "../tool/saveDataToSession.php?saveUserParam=true&idData=projectSelectorDisplayMode&value="
            + displayMode,
        load : function() {
          loadContent("../view/menuProjectSelector.php", 'projectSelectorDiv');
        }
      });
  if (dijit.byId('dialogProjectSelectorParameters')) {
    dijit.byId('dialogProjectSelectorParameters').hide();
  }
}

function refreshProjectSelectorList() {
  dojo.xhrPost({
    url : "../tool/refreshVisibleProjectsList.php",
    load : function() {
      loadContent('../view/menuProjectSelector.php', 'projectSelectorDiv');
      if (dijit.byId('idProjectPlan')) {
        refreshList('planning', null, null, dijit.byId('idProjectPlan').get('value'), 'idProjectPlan', false);
      }
    }
  });
  if (dijit.byId('dialogProjectSelectorParameters')) {
    dijit.byId('dialogProjectSelectorParameters').hide();
  }
}

// ********************************************************************************************
// Diary
// ********************************************************************************************
function diaryPrevious() {
  diaryPreviousNext(-1);
}
function diaryNext() {
  diaryPreviousNext(1);
}

var noRefreshDiaryPeriod=false;
function diarySelectDate(directDate) {
  if (!directDate)
    return;
  if (noRefreshDiaryPeriod) {
    return;
  }
  noRefreshDiaryPeriod=true;
  var period=dojo.byId("diaryPeriod").value;
  var year=directDate.getFullYear();
  var month=directDate.getMonth() + 1;
  if (period == "month") {
    dojo.byId("diaryYear").value=year;
    dojo.byId("diaryMonth").value=(month >= 10) ? month : "0" + month;
    diaryDisplayMonth(month, year);
  } else if (period == "week") {
    var week=getWeek(directDate.getDate(), month, year) + '';
    if (week == 1 && month > 10) {
      year+=1;
    }
    if (week > 50 && month == 1) {
      year-=1;
    }
    dojo.byId("diaryWeek").value=week;
    dojo.byId("diaryYear").value=year;
    dojo.byId("diaryMonth").value=month;
    diaryDisplayWeek(week, year);
  } else if (period == "day") {
    day=formatDate(directDate);
    dojo.byId("diaryDay").value=day;
    dojo.byId("diaryYear").value=year;
    diaryDisplayDay(day);
  }
  setTimeout("noRefreshDiaryPeriod=false;", 100);
  loadContent("../view/diary.php", "detailDiv", "diaryForm");
  return true;
}

function diaryPreviousNext(way) {
  if (waitingForReply)  {
    showInfo(i18n("alertOngoingQuery"));
    return;
  }
  period=dojo.byId("diaryPeriod").value;
  year=dojo.byId("diaryYear").value;
  month=dojo.byId("diaryMonth").value;
  week=dojo.byId("diaryWeek").value;
  day=dojo.byId("diaryDay").value;
  if (period == "month") {
    month=parseInt(month) + parseInt(way);
    if (month <= 0) {
      month=12;
      year=parseInt(year) - 1;
    } else if (month >= 13) {
      month=1;
      year=parseInt(year) + 1;
    }
    dojo.byId("diaryYear").value=year;
    dojo.byId("diaryMonth").value=(month >= 10) ? month : "0" + month;
    diaryDisplayMonth(month, year)
  } else if (period == "week") {
    week=parseInt(week) + parseInt(way);
    if (parseInt(week) == 0) {
      week=getWeek(31, 12, year - 1);
      if (week == 1) {
        var day=getFirstDayOfWeek(1, year);
        week=getWeek(day.getDate() - 1, day.getMonth() + 1, day.getFullYear());
      }
      year=parseInt(year) - 1;
    } else if (parseInt(week, 10) > 53) {
      week=1;
      year=parseInt(year) + 1;
    } else if (parseInt(week, 10) > 52) {
      lastWeek=getWeek(31, 12, year);
      if (lastWeek == 1) {
        var day=getFirstDayOfWeek(1, year + 1);
        lastWeek=getWeek(day.getDate() - 1, day.getMonth() + 1, day
            .getFullYear());
      }
      if (parseInt(week, 10) > parseInt(lastWeek, 10)) {
        week=01;
        year=parseInt(year) + 1;
      }
    }
    dojo.byId("diaryWeek").value=week;
    dojo.byId("diaryYear").value=year;
    diaryDisplayWeek(week, year);
  } else if (period == "day") {
    day=formatDate(addDaysToDate(getDate(day), way));
    year=day.substring(0, 4);
    dojo.byId("diaryDay").value=day;
    dojo.byId("diaryYear").value=year;
    diaryDisplayDay(day);
  }
  loadContent("../view/diary.php", "detailDiv", "diaryForm");
}

function diaryWeek(week, year) {
  dojo.byId("diaryPeriod").value="week";
  dojo.byId("diaryYear").value=year;
  dojo.byId("diaryWeek").value=week;
  diaryDisplayWeek(week, year);
  loadContent("../view/diary.php", "detailDiv", "diaryForm");
}

function diaryMonth(month, year) {
  dojo.byId("diaryPeriod").value="month";
  dojo.byId("diaryYear").value=year;
  dojo.byId("diaryMonth").value=month;
  diaryDisplayMonth(month, year);
  loadContent("../view/diary.php", "detailDiv", "diaryForm");
}
function diaryDay(day) {
  dojo.byId("diaryPeriod").value="day";
  dojo.byId("diaryYear").value=day.substring(day, 0, 4);
  dojo.byId("diaryMonth").value=day.substring(day, 5, 2);
  dojo.byId("diaryDay").value=day;
  diaryDisplayDay(day);
  loadContent("../view/diary.php", "detailDiv", "diaryForm");
}

function diaryDisplayMonth(month, year) {
  var vMonthArr=new Array(i18n("January"), i18n("February"), i18n("March"),
      i18n("April"), i18n("May"), i18n("June"), i18n("July"), i18n("August"),
      i18n("September"), i18n("October"), i18n("November"), i18n("December"));
  caption=vMonthArr[month - 1] + " " + year;
  dojo.byId("diaryCaption").innerHTML=caption;
  var firstday=new Date(year, month - 1, 1);
  dijit.byId('dateSelector').set('value', firstday);
}

function diaryDisplayWeek(week, year) {
  var firstday=getFirstDayOfWeek(week, year);
  var lastday=new Date(firstday);
  lastday.setDate(firstday.getDate() + 6);
  caption=year + ' #' + week + " (" + dateFormatter(formatDate(firstday))
      + " - " + dateFormatter(formatDate(lastday)) + ")";
  dojo.byId("diaryCaption").innerHTML=caption;
  dijit.byId('dateSelector').set('value', firstday);
}

function diaryDisplayDay(day) {
  var vDayArr=new Array(i18n("Sunday"), i18n("Monday"), i18n("Tuesday"),
      i18n("Wednesday"), i18n("Thursday"), i18n("Friday"), i18n("Saturday"));
  var d=getDate(day);
  caption=vDayArr[d.getDay()] + " " + dateFormatter(day);
  dojo.byId("diaryCaption").innerHTML=caption;
  dijit.byId('dateSelector').set('value', day);
}

// ********************************************************************************************
// WORKFLOW PARAMETERS (selection of status)
// ********************************************************************************************

function showWorkflowParameter(id) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  callBack=function() {
  };
  var params='&idWorkflow=' + id;
  loadDialog('dialogWorkflowParameter', callBack, true, params);
}

function saveWorkflowParameter() {
  loadContent("../tool/saveWorkflowParameter.php", "resultDiv",
      "dialogWorkflowParameterForm", true);
  dijit.byId('dialogWorkflowParameter').hide();
}

function changeCreationInfo() {
  toShow=false;
  if (dijit.byId('idUser')) {
    dijit.byId('dialogCreationInfoCreator').set('value',
        dijit.byId('idUser').get('value'));
    dojo.byId('dialogCreationInfoCreatorLine').style.display='inline';
    toShow=true;
  } else if (dojo.byId('idUser')) {
    dijit.byId('dialogCreationInfoCreator').set('value',
        dojo.byId('idUser').value);
    dojo.byId('dialogCreationInfoCreatorLine').style.display='inline';
    toShow=true;
  } else {
    dojo.byId('dialogCreationInfoCreatorLine').style.display='none';
  }

  if (dijit.byId('creationDate')) {
    dijit.byId('dialogCreationInfoDate').set('value',
        dijit.byId('creationDate').get('value'));
    dojo.byId('dialogCreationInfoDateLine').style.display='inline';
    dojo.byId('dialogCreationInfoTimeLine').style.display='none';
    toShow=true;
  } else if (dojo.byId('creationDate')) {
    dijit.byId('dialogCreationInfoDate').set('value',
        dojo.byId('creationDate').value);
    dojo.byId('dialogCreationInfoDateLine').style.display='inline';
    dojo.byId('dialogCreationInfoTimeLine').style.display='none';
    toShow=true;
  } else {
    if (dijit.byId('creationDateTime')) {
      val=dijit.byId('creationDateTime').get('value');
      valDate=val.substr(0, 10);
      valTime='T' + val.substr(11, 8);
      dijit.byId('dialogCreationInfoDate').set('value', valDate);
      dijit.byId('dialogCreationInfoTime').set('value', valTime);
      dojo.byId('dialogCreationInfoDateLine').style.display='inline';
      dojo.byId('dialogCreationInfoTimeLine').style.display='inline';
      toShow=true;
    } else if (dojo.byId('creationDateTime')) {
      val=dojo.byId('creationDateTime').value;
      valDate=val.substr(0, 10);
      valTime=val.substr(11, 8);
      dijit.byId('dialogCreationInfoDate').set('value', valDate);
      dijit.byId('dialogCreationInfoTime').set('value', valTime);
      dojo.byId('dialogCreationInfoDateLine').style.display='inline';
      dojo.byId('dialogCreationInfoTimeLine').style.display='inline';
      toShow=true;
    } else {
      dojo.byId('dialogCreationInfoDateLine').style.display='none';
      dojo.byId('dialogCreationInfoTimeLine').style.display='none';
    }
  }
  if (toShow) {
    dijit.byId('dialogCreationInfo').show();
  }

  if (toShow) {
    dijit.byId('dialogCreationInfo').show();
  }
}

function saveCreationInfo() {
  if (dijit.byId('idUser')) {
    dijit.byId('idUser').set('value',
        dijit.byId('dialogCreationInfoCreator').get('value'));
  } else if (dojo.byId('idUser')) {
    dojo.byId('idUser').value=dijit.byId('dialogCreationInfoCreator').get(
        'value');
  }

  if (dijit.byId('creationDate')) {
    dijit.byId('creationDate').set('value',
        formatDate(dijit.byId('dialogCreationInfoDate').get('value')));
  } else if (dojo.byId('creationDate')) {
    dojo.byId('creationDate').value=formatDate(dijit.byId(
        'dialogCreationInfoDate').get('value'));
  } else {
    if (dijit.byId('creationDateTime')) {
      valDate=formatDate(dijit.byId('dialogCreationInfoDate').get('value'));
      valTime=formatTime(dijit.byId('dialogCreationInfoTime').get('value'));
      val=valDate + ' ' + valTime;
      dijit.byId('creationDateTime').set('value', val);
    } else if (dojo.byId('creationDateTime')) {
      valDate=format(Datedijit.byId('dialogCreationInfoDate').get('value'));
      valTime=format(Datedijit.byId('dialogCreationInfoTime').get('value'));
      val=valDate + ' ' + valTime;
      dojo.byId('dialogCreationInfoDate').value=val;
    }
  }
  formChanged();
  //dojo.byId('buttonDivCreationInfo').innerHTML="";
  //forceRefreshCreationInfo=true;
  saveObject();
  dijit.byId('dialogCreationInfo').hide();
}

function showLogfile(name) {
  var atEnd=null;
  if (name=='last') {
    atEnd=function(name){
      var scroll=function() {
        dojo.query(".logFile .dijitDialogPaneContent").forEach(function(node, index, arr){
          node.scrollTop=parseInt(dojo.byId('logTableContainer').offsetHeight);
        });
      };
      setTimeout(scroll,500);
    };
  }
  
  loadDialog('dialogLogfile', atEnd, true, '&logname='+name, true);
}

function installPlugin(fileName,confirmed) {
  if (! confirmed) {
    actionOK=function() {
      installPlugin(fileName, true);
    };
    msg=i18n('confirmInstallPlugin', new Array(fileName));
    showConfirm(msg,actionOK);
  } else {
    showWait();
    dojo.xhrGet({
      url : "../plugin/loadPlugin.php?pluginFile="
          + encodeURIComponent(fileName),
      load : function(data) {
        if (data=="OK") {
          loadContent("pluginManagement.php", "centerDiv");
        } else if (data=="RELOAD") {
          showWait();
          noDisconnect=true;
          quitConfirmed=true;        
          dojo.byId("directAccessPage").value="pluginManagement.php";
          dojo.byId("menuActualStatus").value=menuActualStatus;
          dojo.byId("p1name").value="type";
          dojo.byId("p1value").value=forceRefreshMenu;
          forceRefreshMenu="";
          dojo.byId("directAccessForm").submit();     
        } else if (data.substr(0,8)=="CALLBACK") {
          var url=data.substring(9,data.indexOf('#'));
          window.open(url);
          var msg=data.substring(data.indexOf('#')+1,data.indexOf('##'));
          hideWait();
          callback=function() {loadContent("pluginManagement.php", "centerDiv");};
          showInfo(msg,callback);
          //setTimeout(callback,5000);
        } else {
          hideWait();
          showError(data+'<br/>');
        }
      },
      error : function(data) {
        hideWait();
        showError(data);
      }
    });
  }
}
function deletePlugin(fileName,confirmed) {
  if (! confirmed) {
    actionOK=function() {
      deletePlugin(fileName, true);
    };
    msg=i18n('confirmDeletePluginFile', new Array(fileName));
    showConfirm(msg,actionOK);
  } else {
    showWait();
    dojo.xhrGet({
      url : "../plugin/deletePlugin.php?pluginFile="
          + encodeURIComponent(fileName),
      load : function(data) {
        if (data=="OK") {
          loadContent("pluginManagement.php", "centerDiv");
        } else {
          hideWait();
          showError(data+'<br/>');
        }
      },
      error : function(data) {
        hideWait();
        showError(data);
      }
    });
  }
}
var historyShowHideWorkStatus=0;
function historyShowHideWork() {
  if (! dojo.byId('objectClass')) {return;}
  historyShowHideWorkStatus=((historyShowHideWorkStatus)?0:1);
  if (dijit.byId('dialogHistory')) {
    dijit.byId('dialogHistory').hide();
  } 
  dojo.xhrPost({
    url : "../tool/saveDataToSession.php?saveUserParam=false&idData=showWorkHistory&value="+historyShowHideWorkStatus,
    load : function() {
      showHistory(dojo.byId('objectClass').value);  
    }
  });
}

// ====================================================
// * UPLOAD PLUGIN * //
// ====================================================

function uploadPlugin() {
  if (!isHtml5()) {
    return true;
  }
  if (dojo.byId('pluginFileName').innerHTML == "") {
    return false;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'block'
  });
  showWait();
  return true;
}

function changePluginFile(list) {
  if (list.length > 0) {
    dojo.byId("pluginFileName").innerHTML=list[0]['name'];
    return true;
  }
}

function savePluginAck(dataArray) {
  if (!isHtml5()) {
    resultFrame=document.getElementById("resultPost");
    resultText=resultPost.document.body.innerHTML;
    dijit.byId('pluginResultDiv').set('content',resultText);
    savePluginFinalize();
    return;
  }
  if (dojo.isArray(dataArray)) {
    result=dataArray[0];
  } else {
    result=dataArray;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  if (dojo.isArray(dataArray)) {
    result=dataArray[0];
  } else {
    result=dataArray;
  }
  dojo.style(dojo.byId('downloadProgress'), {
    display : 'none'
  });
  contentNode = dojo.byId('pluginResultDiv');
  contentNode.innerHTML=result.message;
  contentNode.style.display="block"; 
  savePluginFinalize();
}
function savePluginFinalize() {
  contentNode = dojo.byId('pluginResultDiv');
  if (contentNode.innerHTML.indexOf('resultOK')>0) {
    setTimeout('loadContent("pluginManagement.php", "centerDiv");',1000);
  } else {
    hideWait();
  }
}

// ===================================================
// favorite reports management
// ===================================================

function refreshFavoriteReportList() {
  if (!dijit.byId('favoriteReports')) return;
  dijit.byId('favoriteReports').refresh();
  //var listContent=trim(dijit.byId('favoriteReports').get('content'));
}
function saveReportAsFavorite() {
  var fileName=dojo.byId('reportFile').value;
  var callback=function(){
    refreshFavoriteReportList();
    dijit.byId('listFavoriteReports').openDropDown();
    var delay=2000;
    var listContent=trim(dijit.byId('favoriteReports').get('content'));
    if (listContent=="") {delay=1;}
    hideReportFavoriteTooltip(delay);
  };
  loadContent("../tool/saveReportAsFavorite.php" , "resultDiv", "reportForm", true, 'report',false,false, callback);
}

function showReportFavoriteTooltip() {
  var listContent=trim(dijit.byId('favoriteReports').get('content'));
  if (listContent=="") {
    return;
  }
  clearTimeout(closeFavoriteReportsTimeout);
  clearTimeout(openFavoriteReportsTimeout);
  openFavoriteReportsTimeout=setTimeout("dijit.byId('listFavoriteReports').openDropDown();",popupOpenDelay);
}

function hideReportFavoriteTooltip(delay) {
  if (!dijit.byId("listFavoriteReports")) return;
  clearTimeout(closeFavoriteReportsTimeout);
  clearTimeout(openFavoriteReportsTimeout);
  closeFavoriteReportsTimeout=setTimeout('dijit.byId("listFavoriteReports").closeDropDown();',delay);
}

function removeFavoriteReport(id) {
  dojo.xhrGet({
    url: '../tool/removeFavoriteReport.php?idFavorite='+id,
    load: function(data,args) { 
      refreshFavoriteReportList(); 
    }
  });
}
function reorderFavoriteReportItems() {
  var nodeList=dndFavoriteReports.getAllNodes();
  var param="";
  for (i=0; i < nodeList.length; i++) {
    var domNode=nodeList[i];
    var item=nodeList[i].id.substr(11);
    var order=dojo.byId("favoriteReportOrder" + item);
    if (dojo.hasClass(domNode,'dojoDndItemAnchor')) {
      order.value=null;
      dojo.removeClass(domNode,'dojoDndItemAnchor');
      dojo.query('dojoDndItemAnchor').removeClass('dojoDndItemAnchor');
      continue;
    }
    if (order) {
      order.value=i + 1;
      param+=((param)?'&':'?')+"favoriteReportOrder"+item+"="+(i+1);
    }
  }
  dojo.xhrPost({
    url: '../tool/saveReportFavoriteOrder.php'+param,
    handleAs: "text",
    load: function(data,args) { 
      refreshFavoriteReportList(); 
    }
  });
}

function checkEmptyReportFavoriteTooltip() {
  var listContent=trim(dijit.byId('favoriteReports').get('content'));
  if (listContent=="") {
    dijit.byId("listFavoriteReports").closeDropDown();
  }
}

function showTickets(refType, refId) {
  loadDialog('dialogShowTickets', null, true, '&refType='+refType+'&refId='+refId, true);
}

function showMenuList() {
  clearTimeout(closeMenuListTimeout);
  menuListAutoshow=true;
  clearTimeout(openMenuListTimeout);
  openMenuListTimeout=setTimeout("dijit.byId('menuSelector').loadAndOpenDropDown();",popupOpenDelay);
  
}
function hideMenuList(delay, item) {
  if (! menuListAutoshow) return;
  clearTimeout(closeMenuListTimeout);
  clearTimeout(openMenuListTimeout);
  closeMenuListTimeout=setTimeout("dijit.byId('menuSelector').closeDropDown();",delay);
}

function saveRestrictTypes() {
  $callback=function() {
    dojo.xhrGet({
      url : '../tool/getSingleData.php?dataType=restrictedTypeClass'
        +'&idProject='+dojo.byId('idProjectParam').value
        +'&idProjectType='+dojo.byId('idProjectTypeParam').value
        +'&idProfile='+dojo.byId('idProfile').value,
      handleAs : "text",
      load : function(data) {
        dojo.byId('resctrictedTypeClassList').innerHTML=data;
      }
    });
  }
  loadContent("../tool/saveRestrictTypes.php" , "resultDiv", "restrictTypesForm", true, 'report',false,false, $callback);
  dijit.byId('dialogRestrictTypes').hide();
}

function getMaxWidth(document){
  return Math.max( document.scrollWidth, document.offsetWidth, 
      document.clientWidth);
}

function getMaxHeight(document){
  return Math.max( document.scrollHeight, document.offsetHeight, 
      document.clientHeight);
}

function planningToCanvasToPDF(){

  var iframe = document.createElement('iframe');
  
  //this onload is for firefox but also work on others browsers
  iframe.onload = function() {
  var orientation="landscape";  // "portrait" ou "landscape"
  if(!document.getElementById("printLandscape").checked)orientation="portrait";
  var ratio=parseInt(document.getElementById("printZoom").value)/100;
  var repeatIconTask=document.getElementById("printRepeat").checked; // If true this will repeat on each page the icon
  loadContent("../tool/submitPlanningPdf.php", "planResultDiv", 'planningPdfForm', false,null,null,null,function(){showWait();});
  var sizeElements=[];
  var marge=0;
  var widthIconTask=0; // the width that icon+task represent
  var heightColumn=parseInt(document.getElementById('leftsideTop').offsetHeight)*ratio;
  var heightRow=21*ratio;
  var widthRow=(parseInt(dojo.query('.ganttRightTitle')[0].offsetWidth)-1)*ratio;
  var nbRowTotal=0;
  var nbColTotal=0;
  // init max width/height by orientation
  var maxWidth=(540-marge)*1.25;
  var maxHeight=(737-marge)*1.25;
  if(orientation=="landscape"){
    maxWidth=(850-marge)*1.25;
    maxHeight=(450-marge)*1.25;
  }
  
  //We create an iframe will which contain the planning to transform it in image
  var frameContent=document.getElementById("iframeTmpPlanning");
  
  var cssLink2 = document.createElement("link");
  cssLink2.href = "css/projeqtor.css"; 
  cssLink2 .rel = "stylesheet"; 
  cssLink2 .type = "text/css"; 
  frameContent.contentWindow.document.head.appendChild(cssLink2);
  
  var cssLink = document.createElement("link");
  cssLink.href = "css/jsgantt.css"; 
  cssLink .rel = "stylesheet"; 
  cssLink .type = "text/css";
  frameContent.contentWindow.document.head.appendChild(cssLink);
  
  /*var css = document.createElement("style");
  css .type = "text/css";
  frameContent.contentWindow.document.head.appendChild(css);
  styles = '.rightTableLine{ height:22px; }';
  
  if (css.styleSheet) css.styleSheet.cssText = styles;
  else css.appendChild(document.createTextNode(styles));*/
  var heightV=(heightColumn+getMaxHeight(document.getElementById('leftside'))+(getMaxHeight(document.getElementById('leftside'))/21))+'px';
  
  frameContent.style.position='absolute';
  frameContent.style.width=(4+parseInt(document.getElementById('leftGanttChartDIV').style.width)+getMaxWidth(document.getElementById('rightTableContainer')))+'px';
  frameContent.style.height=heightV;
  frameContent.style.border='0';
  //frameContent.style.top='0';
  //frameContent.style.left='0';
  frameContent.contentWindow.document.body.innerHTML='<div style="float:left;width:'+document.getElementById('leftGanttChartDIV').style.width+';overflow:hidden;height:'+heightV+';">'+document.getElementById('leftGanttChartDIV').innerHTML+'</div><div style="float:left;width:'+getMaxWidth(document.getElementById('rightTableContainer'))+'px;height:'+heightV+';">'+document.getElementById('GanttChartDIV').innerHTML+"</div>";

  frameContent.contentWindow.document.getElementById('ganttScale').style.display='none';
  frameContent.contentWindow.document.getElementById('topGanttChartDIV').style.width=getMaxWidth(document.getElementById('rightTableContainer'))+'px';
  frameContent.contentWindow.document.getElementById('topGanttChartDIV').style.overflow='visible';
  frameContent.contentWindow.document.getElementById('mainRightPlanningDivContainer').style.overflow='visible';
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.overflow='visible';
  frameContent.contentWindow.document.getElementById('mainRightPlanningDivContainer').style.height=(getMaxHeight(document.getElementById('leftside')))+'px';
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.height=(getMaxHeight(document.getElementById('leftside')))+'px';
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.height=(getMaxHeight(document.getElementById('leftside')))+'px';
  frameContent.contentWindow.document.getElementById('dndSourceTable').style.height=(getMaxHeight(document.getElementById('leftside')))+'px';
  frameContent.contentWindow.document.getElementById('vScpecificDay_1').style.height=(getMaxHeight(document.getElementById('leftside')))+'px';
  frameContent.contentWindow.document.getElementById('leftside').style.top="0";
  frameContent.contentWindow.document.getElementById('leftsideTop').style.width=document.getElementById('leftGanttChartDIV').style.width;
  frameContent.contentWindow.document.getElementById('leftside').style.width=document.getElementById('leftGanttChartDIV').style.width;
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.overflowX="visible";
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.overflowY="visible";
  //Calculate each width column in left top side
  for(var i=0; i<dojo.query("[id^='topSourceTable'] tr")[1].childNodes.length;i++){
    sizeElements.push((dojo.query("[id^='topSourceTable'] tr")[1].childNodes[i].offsetWidth)*ratio);
  }
  for(var i=0; i<dojo.query("[class^='rightTableLine']").length;i++){
    dojo.query("[class^='rightTableLine']")[i].style.width=(parseInt(dojo.query("[class^='rightTableLine']")[i].style.width)-1)+"px";
  }
  for(var i=0; i<dojo.query("[class^='ganttDetail weekBackground']").length;i++){
    dojo.query("[class^='ganttDetail weekBackground']")[i].style.width=(parseInt(dojo.query("[class^='ganttDetail weekBackground']")[i].style.width)-1)+"px";
  }
  
  widthIconTask=sizeElements[0]+sizeElements[1];
  
  sizeColumn=parseInt(dojo.query(".ganttRightTitle")[0].style.width)*ratio;
  
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.width=getMaxWidth(frameContent.contentWindow.document.getElementById('rightGanttChartDIV'))+'px';
  frameContent.contentWindow.document.getElementById('topGanttChartDIV').style.width=getMaxWidth(frameContent.contentWindow.document.getElementById('rightGanttChartDIV'))+'px';
  frameContent.contentWindow.document.getElementById('mainRightPlanningDivContainer').style.width=getMaxWidth(frameContent.contentWindow.document.getElementById('rightGanttChartDIV'))+'px';
  //add border into final print
  frameContent.contentWindow.document.getElementById('leftsideTop').innerHTML ='<div id="separatorLeftGanttChartDIV2" style="position:absolute;height:100%;z-index:10000;width:4px;background-color:#C0C0C0;"></div>'+frameContent.contentWindow.document.getElementById('leftsideTop').innerHTML;
  frameContent.contentWindow.document.getElementById('leftside').innerHTML ='<div id="separatorLeftGanttChartDIV" style="position:absolute;height:100%;z-index:10000;width:4px;background-color:#C0C0C0;"></div>'+frameContent.contentWindow.document.getElementById('leftside').innerHTML;
  frameContent.contentWindow.document.getElementById('leftside').style.width=(parseInt(frameContent.contentWindow.document.getElementById('leftside').style.width)+parseInt(frameContent.contentWindow.document.getElementById('separatorLeftGanttChartDIV').style.width))+'px';
  frameContent.contentWindow.document.getElementById('leftsideTop').style.width=frameContent.contentWindow.document.getElementById('leftside').style.width;
  frameContent.contentWindow.document.getElementById('separatorLeftGanttChartDIV').style.left=(parseInt(frameContent.contentWindow.document.getElementById('leftside').style.width)-4)+'px';
  frameContent.contentWindow.document.getElementById('separatorLeftGanttChartDIV2').style.left=(parseInt(frameContent.contentWindow.document.getElementById('leftsideTop').style.width)-4)+'px';
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.width=frameContent.contentWindow.document.getElementById('rightTableContainer').style.width;
  frameContent.contentWindow.document.getElementById('rightGanttChartDIV').style.height=frameContent.contentWindow.document.getElementById('rightTableContainer').style.height;

  var tabImage=[]; //Contain pictures 
  var mapImage={}; //Contain pictures like key->value, cle=namePicture, value=base64(picture)
  
  //Start the 4 prints function
  //Print image activities and projects
  html2canvas(frameContent.contentWindow.document.getElementById('leftside')).then(function(leftElement) {
    
    //Print image column left side
    html2canvas(frameContent.contentWindow.document.getElementById('leftsideTop')).then(function(leftColumn) { 
      
      //Print right Line
      html2canvas(frameContent.contentWindow.document.getElementById('rightGanttChartDIV')).then(function(rightElement) {
        
        //Print right column
        html2canvas(frameContent.contentWindow.document.getElementById('rightside')).then(function(rightColumn) {
          if(ratio!=1){
            leftElement=cropCanvas(leftElement,0,0,leftElement.width,leftElement.height,ratio);
            leftColumn=cropCanvas(leftColumn,0,0,leftColumn.width,leftColumn.height,ratio);
            rightElement=cropCanvas(rightElement,0,0,rightElement.width,rightElement.height,ratio);
            rightColumn=cropCanvas(rightColumn,0,0,rightColumn.width,rightColumn.height,ratio);
          }
          //Init number of total rows
          nbRowTotal=Math.round(leftElement.height/heightRow); 
          //frameContent.parentNode.removeChild(frameContent);
          
          //Start pictures's calcul
          firstEnterHeight=true;
          var EHeightValue=0; //Height pointer cursor
          var EHeight=leftElement.height; //total height
          while((Math.ceil(EHeight/maxHeight)>=1 || firstEnterHeight) && EHeight>heightRow){
            var calculHeight=maxHeight;
            var ELeftWidth=leftElement.width; //total width
            var ERightWidth=rightElement.width; //total width
            var addHeighColumn=0;
            if(firstEnterHeight || (!firstEnterHeight && repeatIconTask)){
              addHeighColumn=heightColumn;
            }
            var heightElement=0;
            while(calculHeight-addHeighColumn>=heightRow && nbRowTotal!=0){
              calculHeight-=heightRow;
              heightElement+=heightRow;
              nbRowTotal--;
            }
            var iterateurColumnLeft=0;
            firstEnterWidth=true;
            var widthElement=0;
            var imageRepeat=null;
            if(repeatIconTask){
              imageRepeat=combineCanvasIntoOne(
                              cropCanvas(leftColumn,0,0,sizeElements[0]+sizeElements[1],heightColumn),
                              cropCanvas(leftElement,0,EHeightValue,sizeElements[0]+sizeElements[1],heightElement),
                              true);
            }
            var canvasList=[];
            while(ELeftWidth/maxWidth>=1 || (!firstEnterWidth && ELeftWidth>0)){
              firstEnterWidth2=true;
              oldWidthElement=widthElement;
              while(iterateurColumnLeft<sizeElements.length && ELeftWidth>=sizeElements[iterateurColumnLeft]){
                ELeftWidth-=sizeElements[iterateurColumnLeft];
                widthElement+=sizeElements[iterateurColumnLeft];
                if(repeatIconTask && !firstEnterWidth && firstEnterWidth2)ELeftWidth+=widthIconTask;
                iterateurColumnLeft++;
                firstEnterWidth2=false;
              }
              if(oldWidthElement==widthElement){
                widthElement+=ELeftWidth;
                ELeftWidth=0;
              }
              if(!firstEnterWidth){
                if(repeatIconTask){
                  canvasList.push(combineCanvasIntoOne(imageRepeat,
                                  combineCanvasIntoOne(
                                      cropCanvas(leftColumn,oldWidthElement,0,widthElement-oldWidthElement,heightColumn),
                                      cropCanvas(leftElement,oldWidthElement,EHeightValue,widthElement-oldWidthElement,heightElement),
                                      true),
                                      false));
                }else{
                  if(firstEnterHeight){
                    canvasList.push(combineCanvasIntoOne(
                                        cropCanvas(leftColumn,oldWidthElement,0,widthElement-oldWidthElement,heightColumn),
                                        cropCanvas(leftElement,oldWidthElement,EHeightValue,widthElement-oldWidthElement,heightElement),
                                        true));
                  }else{
                    canvasList.push(cropCanvas(leftElement,oldWidthElement,EHeightValue,widthElement-oldWidthElement,heightElement));
                  } 
                }
              }else{
                if(firstEnterHeight || repeatIconTask){
                  canvasList.push(combineCanvasIntoOne(
                                        cropCanvas(leftColumn,oldWidthElement,0,widthElement-oldWidthElement,heightColumn),
                                        cropCanvas(leftElement,oldWidthElement,EHeightValue,widthElement-oldWidthElement,heightElement),
                                        true));
                }else{
                  canvasList.push(cropCanvas(leftElement,oldWidthElement,EHeightValue,widthElement-oldWidthElement,heightElement));                  
                }
              }
              firstEnterWidth=false;
            }
            if(canvasList.length==0){
              if(firstEnterHeight || repeatIconTask){
                canvasList.push(combineCanvasIntoOne(
                                        cropCanvas(leftColumn,0,0,leftColumn.width,heightColumn),
                                        cropCanvas(leftElement,0,EHeightValue,leftElement.width,heightElement),
                                        true));
              }else{
                canvasList.push(cropCanvas(leftElement,0,EHeightValue,leftElement.width,heightElement));
              }
            }
            firstEnterWidth=true;
            if(repeatIconTask && leftColumn.width>widthIconTask){
              imageRepeat=combineCanvasIntoOne(combineCanvasIntoOne(
                                                    cropCanvas(leftColumn,0,0,sizeElements[0]+sizeElements[1],heightColumn),
                                                    cropCanvas(leftElement,0,EHeightValue,sizeElements[0]+sizeElements[1],heightElement),
                                                    true),
                                               combineCanvasIntoOne(
                                                    cropCanvas(leftColumn,leftColumn.width-4,0,4,heightColumn),
                                                    cropCanvas(leftElement,leftElement.width-4,EHeightValue,4,heightElement),
                                                    true),
                                               false);
            }
            widthElement=0;
            firstEnterWidth=true;
            var canvasList2=[];
            //Init number of total cols
            nbColTotal=Math.round(rightElement.width/widthRow); 
            while((Math.ceil(ERightWidth/maxWidth)>=1 || (!firstEnterWidth && ERightWidth>0)) && nbColTotal>0){
              firstEnterWidth2=true;
              oldWidthElement=widthElement;
              limit=0;
              if(firstEnterWidth)limit=canvasList[canvasList.length-1].width;
              if(!firstEnterWidth && repeatIconTask)limit=widthIconTask;
              var currentWidthElm=0;
              while(ERightWidth>widthRow && currentWidthElm+widthRow<maxWidth-limit && nbColTotal>0){
                ERightWidth-=widthRow;
                widthElement+=widthRow;
                currentWidthElm+=widthRow;
                firstEnterWidth2=false;
                nbColTotal--;
              }
              if(!firstEnterWidth){
                if(currentWidthElm!=0 && widthElement!=oldWidthElement)if(repeatIconTask){
                  canvasList2.push(combineCanvasIntoOne(imageRepeat,
                                       combineCanvasIntoOne(
                                           cropCanvas(rightColumn,oldWidthElement+1,0,currentWidthElm,heightColumn),
                                           cropCanvas(rightElement,oldWidthElement,EHeightValue,currentWidthElm,heightElement),
                                           true),
                                       false));
                }else{
                  if(firstEnterHeight){
                    canvasList2.push(combineCanvasIntoOne(
                                          cropCanvas(rightColumn,oldWidthElement+1,0,currentWidthElm,heightColumn),
                                          cropCanvas(rightElement,oldWidthElement,EHeightValue,currentWidthElm,heightElement),
                                          true));
                  }else{
                    canvasList2.push(cropCanvas(rightElement,oldWidthElement,EHeightValue,currentWidthElm,heightElement));
                  }
                }
              }else{
                if(widthElement==0){
                  canvasList2.push(canvasList[canvasList.length-1]);
                }else if(firstEnterHeight || repeatIconTask){
                  canvasList2.push(combineCanvasIntoOne(canvasList[canvasList.length-1],
                                        combineCanvasIntoOne(
                                            cropCanvas(rightColumn,oldWidthElement+1,0,currentWidthElm,heightColumn),
                                            cropCanvas(rightElement,oldWidthElement,EHeightValue,currentWidthElm,heightElement),
                                            true),
                                        false));
                }else{
                  canvasList2.push(combineCanvasIntoOne(canvasList[canvasList.length-1],
                                        cropCanvas(rightElement,oldWidthElement,EHeightValue,currentWidthElm,heightElement),
                                        false));
                }
              }
              if(nbColTotal==0){
                ERightWidth=0;
              }
              firstEnterWidth=false;
            }
            var baseIterateur=tabImage.length;
            for(var i=0;i<canvasList.length-1;i++){
              
              //Add image to mapImage in base64 format
              mapImage["image"+(i+baseIterateur)]=canvasList[i].toDataURL();
              
              //Add to tabImage an array wich contain parameters to put an image into a pdf page with a pagebreak if necessary
              ArrayToPut={image: "image"+(i+baseIterateur),width: canvasList[i].width*0.75,height:canvasList[i].height*0.75};
              if(!(canvasList2.length==0 && i==canvasList.length-1)){
                ArrayToPut['pageBreak']='after';
              }
              tabImage.push(ArrayToPut);
            }
            for(var i=0;i<canvasList2.length;i++){
              if(canvasList2[i].width-widthIconTask>4){
                //Add image to mapImage in base64 format
                mapImage["image"+(i+canvasList.length+baseIterateur)]=canvasList2[i].toDataURL();
                
                //Add to tabImage an array wich contain parameters to put an image into a pdf page with a pagebreak if necessary
                ArrayToPut={image: "image"+(i+canvasList.length+baseIterateur),width: canvasList2[i].width*0.75,height:canvasList2[i].height*0.75};
                if(i!=canvasList2.length-1){
                  ArrayToPut['pageBreak']='after';
                }
                tabImage.push(ArrayToPut);
              }
            }
            EHeight-=maxHeight-calculHeight;
            EHeightValue+=maxHeight-calculHeight;
            firstEnterHeight=false;
          }
          var dd = {
             pageOrientation: orientation,
             content: tabImage,
             images: mapImage
          };
          if( !dojo.isIE ) {

            var userAgent = navigator.userAgent.toLowerCase(); var IEReg = /(msie\s|trident.*rv:)([\w.]+)/; var match = IEReg.exec(userAgent); if( match )

            dojo.isIE = match[2] - 0;

            else

            dojo.isIE = undefined;

          }
          var pdfFileName='ProjeQtOr_Planning';
          var now = new Date();
          pdfFileName+='_'+formatDate(now).replace(/-/g,'')+'_'+formatTime(now).replace(/:/g,'');
          pdfFileName+='.pdf';
          if((dojo.isIE && dojo.isIE>0) || window.navigator.userAgent.indexOf("Edge") > -1) {
            pdfMake.createPdf(dd).download(pdfFileName);
          }else{
            pdfMake.createPdf(dd).download(pdfFileName);
          }
          // open the PDF in a new window
          //pdfMake.createPdf(dd).open();
          // print the PDF (temporarily Chrome-only)
         // pdfMake.createPdf(dd).print();
          // download the PDF (temporarily Chrome-only)
          dijit.byId('dialogPlanningPdf').hide();
          iframe.parentNode.removeChild(iframe);
          setTimeout('hideWait();',100);
        });
      });
    });
  });
  };
  iframe.id="iframeTmpPlanning";
  document.body.appendChild(iframe);
}
function cropCanvas(canvasToCrop,x,y,w,h,r){
  if(typeof r=='undefined')r=1;
    var tempCanvas = document.createElement("canvas"),
    tCtx = tempCanvas.getContext("2d");
    tempCanvas.width = w*r;
    tempCanvas.height = h*r;
    if(w!=0 && h!=0)tCtx.drawImage(canvasToCrop,x,y,w,h,0,0,w*r,h*r);
    return tempCanvas;
}

//addBottom=true : we add the canvas2 at the bottom of canvas1, addBottom=false : we add the canvas2 at the right of canvas1
function combineCanvasIntoOne(canvas1,canvas2,addBottom){
  var tempCanvas = document.createElement("canvas");
  var tCtx = tempCanvas.getContext("2d");
  var ajoutWidth=0;
  var ajoutHeight=0;
  var x=0;
  var y=0;
  if(addBottom){
    ajoutHeight=canvas2.height;
    y=canvas1.height;
  }else{
    ajoutWidth=canvas2.width;
    x=canvas1.width;
  }
  tempCanvas.width = canvas1.width+ajoutWidth;
  tempCanvas.height = canvas1.height+ajoutHeight;
  if(canvas1.width!=0 && canvas1.height!=0)tCtx.drawImage(canvas1,0,0,canvas1.width,canvas1.height);
  if(canvas1.width!=0 && canvas1.height!=0)if(canvas2.width!=0 && canvas2.height!=0)tCtx.drawImage(canvas2,0,0,canvas2.width,canvas2.height,x,y,canvas2.width,canvas2.height);
  return tempCanvas;
}

function changeParamDashboardTicket(paramToSend){
  loadContent('dashboardTicketMain.php?'+paramToSend, 'centerDiv', 'dashboardTicketMainForm');
}

function changeDashboardTicketMainTabPos(){
  var listChild=dojo.byId('dndDashboardLeftParameters').childNodes[1].childNodes;
  addLeft="";
  iddleList=',"iddleList":[';
  if(listChild.length>1){
    addLeft="[";
    for(var i=1;i<listChild.length;i++){
      getId="";
      if(listChild[i].id.includes('dialogDashboardLeftParametersRow')){
        getId=listChild[i].id.split('dialogDashboardLeftParametersRow')[1];
      }
      if(listChild[i].id.includes('dialogDashboardRightParametersRow')){
        getId=listChild[i].id.split('dialogDashboardRightParametersRow')[1];
      }
      //iddleList+='"'+dijit.byId('dialogTodayParametersIdle'+listChild[i].id.split('dialogDashboardLeftParametersRow')[1]).get('checked')+'"';
      if(getId!=""){
        addLeft+='"'+getId+'"';
        iddleList+='{"name":"'+getId+'","idle":'+dijit.byId('tableauBordTabIdle'+getId).get('checked')+'}';
        if(i+1!=listChild.length){
          addLeft+=',';
          iddleList+=',';
        } 
      }
    }
    addLeft+="]";
    if(dojo.byId('dndDashboardRightParameters').childNodes[0].childNodes.length>1){
      iddleList+=',';
    }
  }
  
  var listChild=dojo.byId('dndDashboardRightParameters').childNodes[0].childNodes;
  addRight="";
  if(listChild.length>1){
    addRight="[";
    for(var i=1;i<listChild.length;i++){
      getId="";
        if(listChild[i].id.includes('dialogDashboardLeftParametersRow')){
          getId=listChild[i].id.split('dialogDashboardLeftParametersRow')[1];
        }
        if(listChild[i].id.includes('dialogDashboardRightParametersRow')){
          getId=listChild[i].id.split('dialogDashboardRightParametersRow')[1];
        }
        //iddleList+='"'+dijit.byId('dialogTodayParametersIdle'+listChild[i].id.split('dialogDashboardLeftParametersRow')[1]).get('checked')+'"';
        if(getId!=""){
          addRight+='"'+getId+'"';
          iddleList+='{"name":"'+getId+'","idle":'+dijit.byId('tableauBordTabIdle'+getId).get('checked')+'}';
          if(i+1!=listChild.length){
            addRight+=',';
            iddleList+=',';
          }
        }
      }
    addRight+="]";
  }
  toSend='{"addLeft":';
  if(addLeft==""){
    addLeft="[]";
  }
  toSend+=addLeft;
  
  toSend+=',"addRight":';
  if(addRight==""){
    addRight="[]";
  }
  iddleList+="]";
  toSend+=addRight+iddleList+"}";
  loadContent('dashboardTicketMain.php?updatePosTab='+toSend, 'centerDiv', 'dashboardTicketMainForm');
}

function getLocalLocation(){
  if(dojo.locale.length==2){
    return dojo.locale+"_"+dojo.locale.toUpperCase();
  }else{
    return dojo.locale.split('-')[0]+"_"+dojo.locale.split('-')[1].toUpperCase();
  }
}

function commentImputationSubmit(year,week,idAssignment,refType,refId){
  var text=dijit.byId('commentImputation').get('value');
  if(text.trim()==''){
    showAlert(i18n('messageMandatory',[i18n('colComment')]));
    return;
  }
  showWait();
  dojo.xhrPost({
    url : "../tool/dynamicDialogCommentImputation.php?year="+year+"&week="+week+"&idAssignment="+idAssignment+"&refTypeComment="+refType+"&refIdComment="+refId,
    handleAs : "text",
    form : 'commentImputationForm',
    load : function(data, args) {
      formChangeInProgress=false;
      document.getElementById("showBig"+idAssignment).style.display='block'; 
      dojo.byId("showBig"+idAssignment).childNodes[0].onmouseover=function(){
        showBigImage(null,null,this,data);
      };
      dijit.byId('dialogCommentImputation').hide();
      hideWait();
    },
    error : function() {
      hideWait();
    }
  });
}

function commentImputationTitlePopup(type){
  title='';
  if(type=='add'){
    title= i18n('commentImputationAdd');
  }else if(type=='view'){
    title= i18n('commentImputationView');
  }
  dijit.byId('dialogCommentImputation').set('title',title);
}

// Evaluation criteria
function addTenderEvaluationCriteria(callForTenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=add&callForTenderId="+callForTenderId;
  loadDialog('dialogCallForTenderCriteria', null, true, params, false);
}
function editTenderEvaluationCriteria(criteriaId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&criteriaId="+criteriaId;
  loadDialog('dialogCallForTenderCriteria', null, true, params, false);
}
function saveTenderEvaluationCriteria() {
  var formVar=dijit.byId("dialogTenderCriteriaForm");
  if (!formVar) {
    showError(i18n("errorSubmitForm", new Array("n/a", "n/a", "dialogTenderCriteriaForm")));
    return;
  }
  if (formVar.validate()) {
    loadContent("../tool/saveTenderEvaluationCriteria.php", "resultDiv", "dialogTenderCriteriaForm", true,'tenderEvaluationCriteria');
    dijit.byId('dialogCallForTenderCriteria').hide();
  }  
}
function removeTenderEvaluationCriteria(criteriaId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeTenderEvaluationCriteria.php?criteriaId="+criteriaId, "resultDiv", null,true,'tenderEvaluationCriteria');
  };
  msg=i18n('confirmDelete', new Array(i18n('TenderEvaluationCriteria'), criteriaId));
  showConfirm(msg, actionOK);
}

//Tender submission
function addTenderSubmission(callForTenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=add&callForTenderId="+callForTenderId;
  loadDialog('dialogCallForTenderSubmission', null, true, params, false);
}
function editTenderSubmission(tenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  var params="&mode=edit&tenderId="+tenderId;
  loadDialog('dialogCallForTenderSubmission', null, true, params, false);
}
function saveTenderSubmission() {
  var formVar=dijit.byId("dialogTenderSubmissionForm");
  if (dijit.byId('dialogCallForTenderSubmissionProvider') && ! trim(dijit.byId('dialogCallForTenderSubmissionProvider').get("value"))) {
    showAlert(i18n('messageMandatory', new Array(i18n('colIdProvider'))));
    return;
  }
  if (!formVar) {
    showAlert(i18n("errorSubmitForm", new Array("n/a", "n/a", "dialogTenderSubmissionForm")));
    return;
  }
  if (formVar.validate()) {
    loadContent("../tool/saveTenderSubmission.php", "resultDiv", "dialogTenderSubmissionForm", true,'tenderSubmission');
    dijit.byId('dialogCallForTenderSubmission').hide();
  }  
}
function removeTenderSubmission(tenderId) {
  if (checkFormChangeInProgress()) {
    showAlert(i18n('alertOngoingChange'));
    return;
  }
  actionOK=function() {
    loadContent("../tool/removeTenderSubmission.php?tenderId="+tenderId, "resultDiv", null,true,'tenderSubmission');
  };
  msg=i18n('confirmDelete', new Array(i18n('Tender'), tenderId))+'<br/><b>'+i18n('messageAlerteDeleteTender')+'</b>';
  showConfirm(msg, actionOK);
}


function changeTenderEvaluationValue(index) {
  var value=dijit.byId("tenderEvaluation_"+index).get("value");
  var coef=dojo.byId("tenderCoef_"+index).value;
  var total=value*coef;
  dijit.byId("tenderTotal_"+index).set("value",total);
  var list=dojo.byId('idTenderCriteriaList').value.split(';');
  var sum=0;
  for (var i=0;i<list.length;i++) {
    sum+=dijit.byId('tenderTotal_'+list[i]).get('value');
  }
  dijit.byId("tenderTotal").set("value",sum);
  var newValue=Math.round(sum*dojo.byId('evaluationMaxCriteriaValue').value/dojo.byId('evaluationSumCriteriaValue').value*100)/100;
  dijit.byId("evaluationValue").set("value",newValue);
}

