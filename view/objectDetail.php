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

/*
 * ============================================================================ Presents the detail of an object, for viewing or editing purpose.
 */
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";

$reorg=(isset($paramReorg) and $paramReorg==true)?true:false; // For testing purpose until it works fine, set it to false for deployment
$leftPane="";
$rightPane="";
$extraPane="";
$bottomPane="";
scriptLog('   ->/view/objectDetail.php');
if (!isset($comboDetail)) {
  $comboDetail=false;
}
$collapsedList=Collapsed::getCollaspedList();
$readOnly=false;

if(false === function_exists('lcfirst')) {
  function lcfirst( $str ) {
    $str[0] = strtolower($str[0]);
    return (string)$str;
  }
}
$preseveHtmlFormatingForPDF=true;
// ********************************************************************************************************
// MAIN PAGE
// ********************************************************************************************************

// fetch information depending on, request
$objClass=$_REQUEST ['objectClass'];
Security::checkValidClass($objClass, 'objectClass');
if (isset($_REQUEST ['noselect'])) {
  $noselect=true;
}
if (!isset($noselect)) {
  $noselect=false;
}
if ($noselect) {
  $objId="";
  $obj=null;
  $profile=getSessionUser()->idProfile;
} else {
  $objId=$_REQUEST ['objectId'];
  $obj=new $objClass($objId);
  $profile=getSessionUser()->getProfile($obj);
  if (array_key_exists('refreshNotes', $_REQUEST)) {
    drawNotesFromObject($obj, true);
    exit();
  }
  if (array_key_exists('refreshBillLines', $_REQUEST)) {
    drawBillLinesFromObject($obj, true);
    exit();
  }
  if (array_key_exists('refreshJobDefinition', $_REQUEST)) {
    drawJobDefinitionFromObject($obj, true);
    exit();
  }
  if (array_key_exists('refreshChecklistDefinitionLines', $_REQUEST)) {
    drawChecklistDefinitionLinesFromObject($obj, true);
    exit();
  }
  if (array_key_exists('refreshAttachments', $_REQUEST)) {
    drawAttachmentsFromObject($obj, true);
    exit();
  }
  /*
   * On assignment change refresh all item if (array_key_exists ( 'refreshAssignment', $_REQUEST )) { drawAssignmentsFromObject($obj->_Assignment, $obj, true ); exit (); }
  */
  if (array_key_exists('refreshResourceCost', $_REQUEST)) {
    drawResourceCostFromObject($obj->$_ResourceCost, $obj, true);
    exit();
  }
  if (array_key_exists('refreshVersionProject', $_REQUEST)) {
    drawVersionProjectsFromObject($obj->$_VersionProject, $obj, true);
    exit();
  }
  if (array_key_exists('refreshProductProject', $_REQUEST)) {
    drawProductProjectsFromObject($obj->$_ProductProject, $obj, true);
    exit();
  }
  if (array_key_exists('refreshDocumentVersion', $_REQUEST)) {
    drawVersionFromObjectFromObject($obj->$_DocumentVersion, $obj, true);
    exit();
  }
  if (array_key_exists('refreshTestCaseRun', $_REQUEST)) {
    drawTestCaseRunFromObject($obj->_TestCaseRun, $obj, true);
    exit();
  }
  if (array_key_exists('refreshLinks', $_REQUEST)) {
    if (property_exists($obj, '_Link')) {
      drawLinksFromObject($obj->_Link, $obj, null, true);
    }
    exit();
  }
  if (array_key_exists('refreshHistory', $_REQUEST)) {
    $treatedObjects []=$obj;
    foreach ( $obj as $col => $val ) {
      if (is_object($val)) {
        $treatedObjects []=$val;
      }
    }
    drawHistoryFromObjects(true);
    if (isset($dynamicDialogHistory) and $dynamicDialogHistory and function_exists('showCloseButton')) {
      showCloseButton();
    }
    exit();
  }
}
// save the current object in session
$print=false;
if (array_key_exists('print', $_REQUEST) or isset($callFromMail)) {
  $print=true;
}
if (!$print and $obj) {
  if (!$comboDetail) {
    SqlElement::setCurrentObject ($obj);
  } else {
    SqlElement::setCurrentObject ($obj,true);
  }
}
$refresh=false;
if (array_key_exists('refresh', $_REQUEST)) {
  $refresh=true;
}

$treatedObjects=array();

$displayWidth='98%';
if ($print and isset($outMode) and $outMode == 'pdf') {
  if (isset($orientation) and $orientation=='L')
    $printWidth=1080;
  else
    $printWidth=760;
} else {
  $printWidth=980;
}
if (array_key_exists('destinationWidth', $_REQUEST)) {
  $width=$_REQUEST ['destinationWidth'];
  $width-=30;
  $displayWidth=$width . 'px';
} else {
  if (array_key_exists('screenWidth', $_SESSION)) {
    $detailWidth=round(($_SESSION ['screenWidth'] * 0.8) - 15); // 80% of screen - split barr - padding (x2)
  } else {
    $displayWidth='98%';
  }
}
if ($print) {
  $displayWidth=$printWidth . 'px'; // must match iFrame size (see main.php)
}

if ($print) {
  echo '<br/>';
  echo '<div class="reportTableHeader" style="width:' . ($printWidth - 10) . 'px;font-size:150%;">' . i18n($objClass) . ' #' . ($objId + 0) . '</div>';
  echo '<br/>';
}

// New refresh method
if (array_key_exists('refresh', $_REQUEST)) {
  if (!$print) {
    echo '<input type="hidden" id="objectClassName" name="objectClassName" value="' . $objClass . '" />' . $cr;
  }
  drawTableFromObject($obj);
  drawChecklistFromObject($obj);
  drawJoblistFromObject($obj);
  exit();
}
?>
<div <?php echo ($print)?'x':'';?>dojoType="dijit.layout.BorderContainer" class="background"><?php
  if (!$refresh and !$print) {
    ?>
  <div id="buttonDiv" dojoType="dijit.layout.ContentPane" region="top"
    style="z-index: 3; height: 35px; position: relative; overflow: visible !important;">
    <div id="resultDiv" dojoType="dijit.layout.ContentPane" region="top"
      style="display: none;z-index:99999;"></div>
		<?php  include 'objectButtons.php'; ?>
		<div id="detailBarShow" onMouseover="hideList('mouse');" onClick="hideList('click');">
      <div id="detailBarIcon" align="center"></div>
    </div>
	</div>
  <div id="formDiv" dojoType="dijit.layout.ContentPane" region="center">
	<?php
  }
  if (!$print) {
    ?>  
<form dojoType="dijit.form.Form" id="objectForm" jsId="objectForm"
      name="objectForm" encType="multipart/form-data" action=""
      method="">
      <script type="dojo/method" event="onShow">
        if (dijit.byId('name')) dijit.byId('name').focus();
      </script>
      <script type="dojo/method" event="onSubmit">
        // Don't do anything on submit, just cancel : no button is default => must click
		    //submitForm("../tool/saveObject.php","resultDiv", "objectForm", true);
		    return false;        
        </script>
      <div style="width: 100%; height: 100%;">
        <div id="detailFormDiv" dojoType="dijit.layout.ContentPane"
          region="top" style="width: 100%; height: 100%;"><?php
  }
  $noData=htmlGetNoDataMessage($objClass);
  $canRead=securityGetAccessRightYesNo('menu' . get_class($obj), 'read', $obj) == "YES";
  if (!$obj->id) {
    $canRead=securityGetAccessRightYesNo('menu' . get_class($obj), 'create', $obj) == "YES";
    $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj, $user) == "YES";  
    if (!$canRead or !$canUpdate) {
      $accessRightRead=securityGetAccessRight('menu' . get_class($obj), 'read', $obj, $user);
      $accessRightUpdate=securityGetAccessRight('menu' . get_class($obj), 'update', null, $user);
      if (($accessRightRead == 'OWN' or $accessRightUpdate == 'OWN') and property_exists($obj, 'idUser')) {
        $canRead=true;
        $obj->idUser=$user->id;
      } else if (($accessRightRead == 'RES' or $accessRightUpdate == 'RES') and property_exists($obj, 'idResource')) {
        $canRead=true;
        $obj->idResource=$user->id;
      }
    }
  }
  
  if (get_class($obj)=='Project' and isset($obj->codeType) and $obj->codeType=='TMP') {
    $canRead=true;
  }  
  if ($noselect) {
    echo $noData;
  } else if (!$canRead) {
    echo htmlGetNoAccessMessage($objClass);
    echo "</div></form>";
    exit();
  } else if ($objId and ! $obj->id) {
    echo htmlGetDeletedMessage($objClass);
    echo "</div></form>";
    exit;
  } else {
    if (!$print or $comboDetail) {
      echo '<input type="hidden" id="objectClassName" name="objectClassName" value="' . $objClass . '" />' . $cr;
    }
    drawTableFromObject($obj);
    drawChecklistFromObject($obj);
    drawJoblistFromObject($obj);
  }

  if (!$print) {
    ?> 
  </div>
      </div>
    </form>
  <?php
  }
  $widthPct=setWidthPct($displayWidth, $print, $printWidth,$obj,"2");
  if (!$noselect and isset($obj->_ChecklistDefinitionLine)) {
    ?> <br />
  <?php if ($print) {?>
<table width="<?php echo $printWidth;?>px;">
      <tr>
        <td class="section"><?php echo i18n('sectionChecklistLines');?></td>
      </tr>
      <tr>
        <td><?php drawChecklistDefinitionLinesFromObject($obj);?></td>
      </tr>
    </table>
  <?php
    } else {
      $titlePane=$objClass . "_checklistDefinitionLine";
      ?>
<div style="width: <?php echo $displayWidth;?>" dojoType="dijit.TitlePane" 
     title="<?php echo i18n('sectionChecklistLines');?>"
     open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
     id="<?php echo $titlePane;?>"       
     onHide="saveCollapsed('<?php echo $titlePane;?>');"
     onShow="saveExpanded('<?php echo $titlePane;?>');" >
     <?php  drawChecklistDefinitionLinesFromObject($obj); ?>
</div>
<?php }?> <?php
  }
  if (!$noselect and isset($obj->_JobDefinition)) {
    ?> <br />
  <?php if ($print) {?>
  <table width="<?php echo $printWidth;?>px;">
      <tr>
        <td class="section"><?php echo i18n('sectionJoblist');?></td>
      </tr>
      <tr>
        <td><?php drawJobDefinitionFromObject($obj);?></td>
      </tr>
    </table>
  <?php
    } else {
      $titlePane=$objClass . "_jobDefinition";
      ?>
  <div style="width: <?php echo $displayWidth;?>" dojoType="dijit.TitlePane"
     title="<?php echo i18n('sectionJoblist');?>"
     open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
     id="<?php echo $titlePane;?>"
     onHide="saveCollapsed('<?php echo $titlePane;?>');"
     onShow="saveExpanded('<?php echo $titlePane;?>');" >
     <?php  drawJobDefinitionFromObject($obj); ?>
  </div>
  <?php }?> <?php
  }
  $displayHistory='REQ';
  $paramDisplayHistory=Parameter::getUserParameter('displayHistory');
  if ($paramDisplayHistory) {
    $displayHistory=$paramDisplayHistory;
  }
  if ($obj and (property_exists($obj, '_noHistory') or property_exists($obj, '_noDisplayHistory'))) {
    $displayHistory='NO';
  }
  if ($print and Parameter::getUserParameter('printHistory') != 'YES') {
    $displayHistory='NO';
  }
  echo '<br/>';
  if ((!$noselect) and ($displayHistory == 'YES' or $displayHistory=='YESW') and !$comboDetail) {
    if ($print) {
      ?>
<table width="<?php echo $printWidth;?>px;">
      <tr>
        <td class="section"><?php echo i18n('elementHistoty');?></td>
      </tr>
    </table>
<?php drawHistoryFromObjects();?> <?php
    } else {
      $titlePane=$objClass . "_history";
      ?>
<div style="width: <?php echo $displayWidth;?>;" dojoType="dijit.TitlePane" 
       title="<?php echo i18n('elementHistoty');?>"
       open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
       id="<?php echo $titlePane;?>"         
       onHide="saveCollapsed('<?php echo $titlePane;?>');"
       onShow="saveExpanded('<?php echo $titlePane;?>');" ><?php drawHistoryFromObjects();?>
</div>
    <br />
<?php }?> <?php
  } else if (!$print){
    $titlePane=$objClass . "_history";
    ?>
<div style="display:none; width: <?php echo $displayWidth;?>;" dojoType="dijit.TitlePane" 
       title="<?php echo i18n('elementHistoty');?>"
       open="<?php echo ( array_key_exists($titlePane, $collapsedList)?'false':'true');?>"
       id="<?php echo $titlePane;?>"         
       onHide="saveCollapsed('<?php echo $titlePane;?>');"
       onShow="saveExpanded('<?php echo $titlePane;?>');" ></div>
	<?php
  }
  ?> <?php if ( ! $refresh and  ! $print) { ?></div>
<?php
}
?></div>
<?php

/**
 * ===========================================================================
 * Draw all the properties of object as html elements, depending on type of data
 *
 * @param $obj the
 *          object to present
 * @param $included boolean
 *          indicating wether the function is called recursively or not
 * @return void
 */
function drawTableFromObject($obj, $included=false, $parentReadOnly=false) {
scriptLog("drawTableFromObject(obj, included=$included, parentReadOnly=$parentReadOnly)");
  global $cr, $print, $treatedObjects, $displayWidth, $outMode, $comboDetail, $collapsedList, $printWidth, $profile, 
   $detailWidth, $readOnly, $largeWidth, $widthPct, $nbColMax, $preseveHtmlFormatingForPDF, $section;
  // if ($outMode == 'pdf') { V5.0 removed as field may content html tags...
  // $obj->splitLongFields ();
  // }
  $ckEditorNumber=0; // Will be used only if getEditor=="CK" for CKEditor
  $obj->setAllDefaultValues();
  if (property_exists($obj, '_sec_Assignment')) {
    $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile' => $profile,'scope' => 'assignmentView'));
    if ($habil and $habil->rightAccess != 1) {
      unset($obj->_sec_Assignment);
    }
  }
  if ($print) $obj->_nbColMax=1;
  $currency=Parameter::getGlobalParameter('currency');
  $currencyPosition=Parameter::getGlobalParameter('currencyPosition');
  $showThumb=Parameter::getUserParameter('paramShowThumb'); // show thumb between label and field ?
  if ($showThumb=='NO') {
    $showThumb=false;
  } else {
    $showThumb=true;
  }
  $treatedObjects []=$obj;
  $dateWidth='72';
  $verySmallWidth='44';
  $smallWidth='72';
  $mediumWidth='197';
  $largeWidth='300';
  $labelWidth=160; // To be changed if changes in css file (label and .label)
  $labelStyleWidth='145px';
  if ($outMode == 'pdf') {
    //$labelWidth=40;
    //$labelStyleWidth=$labelWidth . 'px;';
  }
  $fieldWidth=$smallWidth;
  $extName="";
  $user=getSessionUser();
  $displayComboButton=false;
  $habil=SqlElement::getSingleSqlElementFromCriteria('habilitationOther', array('idProfile' => $profile,'scope' => 'combo'));
  if ($habil) {
    $list=new ListYesNo($habil->rightAccess);
    if ($list->code == 'YES') {
      $displayComboButton=true;
    }
  }
  if ($comboDetail) {
    $extName="_detail";
  }
  $detailWidth=null; // Default detail div width
                       // Check screen resolution, to determine max field width (largeWidth)
  if (array_key_exists('destinationWidth', $_REQUEST)) {
    $detailWidth=$_REQUEST ['destinationWidth'];
  } else {
    if (array_key_exists('screenWidth', $_SESSION)) {
      $detailWidth=round(($_SESSION ['screenWidth'] * 0.8) - 15); // 80% of screen - split barr - padding (x2)
    }
  }
  // Set some king of responsive design : number of display columns depends on screen width
  $nbColMax=getNbColMax($displayWidth, $print, $printWidth, $obj);
  $currentCol=0;
  $nbCol=$nbColMax;
  
  // Define internalTable values, to present data as a table
  $internalTable=0;
  $internalTableCols=0;
  $internalTableRows=0;
  $internalTableCurrentRow=0;
  $internalTableSpecial='';
  $internalTableRowsCaptions=array();
  $classObj=get_class($obj);
  if ($obj->id == '0') {
    $obj->id=null;
  }
  $type=$classObj . 'Type';
  $idType='id' . $type;
  $objType=null;
  if (property_exists($obj, $idType)) {
    if (!$obj->id) {
      if (SqlElement::class_exists($type)) {
        $listType=SqlList::getList($type);
        $first_value = reset($listType);
        $first_key = key($listType);
        $objType=new $type($first_key);
      }
    } else {
      if (SqlElement::class_exists($type)) $objType=new $type($obj->$idType);
    }
  } else if ($included) {
    $type=$obj->refType . 'Type';
    $idType='id' . $type;
    if (!$obj->id) {
      if (SqlElement::class_exists($type)) {
        $listType=SqlList::getList($type);
        $first_value = reset($listType);
        $first_key = key($listType);
        $objType=new $type($first_key);
      }
    } else {
      if (SqlElement::class_exists($obj->refType)) {
        $orig=new $obj->refType($obj->refId);
        if (SqlElement::class_exists($type)) $objType=new $type($orig->$idType);
      }
    }
  }
  $extraHiddenFields=$obj->getExtraHiddenFields( ($objType)?$objType->id:null );
  $section='';
  $nbLineSection=0;
  
  if (SqlElement::is_subclass_of($obj, 'PlanningElement')) {
    $obj->setVisibility();
    $workVisibility=$obj->_workVisibility;
    $costVisibility=$obj->_costVisibility;
    if (get_class($obj) == "MeetingPlanningElement" or get_class($obj) == "PeriodicMeetingPlanningElement") {
      $obj->setAttributes($workVisibility, $costVisibility);
    }
  } else if (method_exists($obj, 'setAttributes')) {
    $obj->setAttributes();
  }
  $nobr=false;
  if (!$obj->id) {
    $canUpdate=(securityGetAccessRightYesNo('menu' . $classObj, 'create', $obj) == 'YES');
  } else {
    $canUpdate=(securityGetAccessRightYesNo('menu' . $classObj, 'update', $obj) == 'YES');
  }
  if ((isset($obj->locked) and $obj->locked and $classObj != 'User') or isset($obj->_readOnly)) {
    $canUpdate=false;
  }
  $arrayRequired=$obj->getExtraRequiredFields(); // will define extra required fields, depending on status, planning mode...
  // Loop on each property of the object
  foreach ( $obj as $col => $val ) {
    if ($detailWidth) {
      $colWidth=round(($displayWidth) / $nbCol); // 3 columns should be displayable
      $maxWidth=$colWidth - $labelWidth; // subtract label width
      if ($maxWidth >= $mediumWidth) {
        $largeWidth=$maxWidth;
      } else {
        $largeWidth=$mediumWidth;
      }
    }
    $hide=false;
    $nobr_before=$nobr;
    $nobr=false;
    if ($included and ($col == 'id' or $col == 'refId' or $col == 'refType' or $col == 'refName')) {
      $hide=true;
    }
    // If field is _tab_x_y, start a table presentation with x columns and y lines
    // the field _tab_x_y must be an array containing x + y values :
    // - the x column headers
    // - the y line headers    
    if (substr($col, 0, 4) == '_tab') {
      $decomp=explode("_", $col);
      $internalTableCols=$decomp [2];
      $internalTableRows=$decomp [3];
      $internalTableSpecial='';
      if (count($decomp) > 4) {
        $internalTableSpecial=$decomp [4];
      }
      // Determine how many items to be displayed per line and column
      $arrTab=array('rows'=>array(),'cols'=>array()); $arrStart=-99; $arrStop=$internalTableCols*$internalTableRows;
      for ($ii=0;$ii<$internalTableCols;$ii++) { $arrTab['cols'][$ii]=0; }
      for ($ii=0;$ii<$internalTableRows;$ii++) { $arrTab['rows'][$ii]=0; }
      foreach ($obj as $arrCol=>$arrVal) {
        if ($arrCol==$col) { $arrStart=-1; continue; }
        if ($arrStart<-1) continue;
        $arrStart++;
        if ($arrStart>=$arrStop) break;
        if (substr($arrCol,0,6)=='_void_' or substr($arrCol,0,7)=='_label_' or substr($arrCol,0,8)=='_button_') { continue; }
        if ($obj->isAttributeSetToField($arrCol, "hidden")) continue;
        $indCol=$arrStart%$internalTableCols;
        $indLin=floor($arrStart/$internalTableCols);
        $arrTab['rows'][$indLin]++;
        $arrTab['cols'][$indCol]++;
      }
      // 
      $internalTable=$internalTableCols * $internalTableRows;
      $internalTableRowsCaptions=array_slice($val, $internalTableCols);
      $internalTableCurrentRow=0;
      $colWidth=($detailWidth) / $nbCol;
      if (SqlElement::is_subclass_of($obj, 'PlanningElement') and $internalTableRows >= 3) {
        for ($i=0; $i < $internalTableRows; $i++) {
          $testRowCaption=strtolower($internalTableRowsCaptions [$i]);
          if ($workVisibility == 'NO' and substr($testRowCaption, -4) == 'work') {
            $internalTableRowsCaptions [$i]='';
          }
          if ($costVisibility == 'NO' and (substr($testRowCaption, -4) == 'cost' or substr($testRowCaption, -7) == 'expense')) {
            $internalTableRowsCaptions [$i]='';
          }
          if ($costVisibility != 'ALL' and substr($testRowCaption, 0,13) == 'reserveamount' ) {
            $internalTableRowsCaptions [$i]='';
          }
        }
        if ($workVisibility != 'ALL' and $costVisibility != 'ALL') {
          $val [2]='';
          $val [5]='';
        }
      }
      echo '</table><table id="' . $col . '" class="detail">';
      echo '<tr class="detail">';
      echo '<td class="detail"></td>'; // Empty label, to have column header in front of columns
      $internalTableBorderTitle=($print)?'border:1px solid #A0A0A0;':'';
      for ($i=0; $i < $internalTableCols; $i++) { // draw table headers
        echo '<td class="detail" style="min-width:75px;'.$internalTableBorderTitle.'">';
        if ($arrTab['cols'][$i]==0) {
          echo '<div class=""></div>';
        } else if ($val [$i]) {
          echo '<div class="tabLabel" style="text-align:left;white-space:nowrap;">' . htmlEncode($obj->getColCaption($val [$i])) . '</div>';
        } else {
          echo '<div class="tabLabel" style="text-align:left;white-space:nowrap;"></div>';
        }
        if ($i < $internalTableCols - 1) {
          echo '</td>';
        }
      }
      // echo '</tr>'; NOT TO DO HERE - WILL BE DONE AFTER
    } else if (substr($col, 0, 5) == '_sec_' and (! $comboDetail or $col!='_sec_Link') ) { // if field is _section, draw a new section bar column
      $prevSection=$section;
      $currentCol+=1;
      if (strlen($col) > 8) {
        $section=substr($col, 5);
      } else {
        $section='';
      }    
      // Determine number of items to be displayed in Header
      $sectionField='_'.$section;
      $sectionFieldDep='_Dependency_'.ucfirst($section);
      $sectionFieldDoc='_Document'.$section;
      $sectionFieldVP='_VersionProject';
      if ($section=='trigger') {
        $sectionFieldDep='_Dependency_Predecessor';
      }
      if (substr($section,0,14)=="Versionproject") {
        $sectionField='_VersionProject';
      }
      $cpt=null;
      if (property_exists($obj,$sectionField ) && isset($obj->$sectionField) && is_array($obj->$sectionField)) {
        $cpt=count($obj->$sectionField);
      } else if (property_exists($obj,$sectionFieldDep ) && is_array($obj->$sectionFieldDep)){
        $cpt=count($obj->$sectionFieldDep);
      } else if (property_exists($obj,$sectionFieldDoc ) && is_array($obj->$sectionFieldDoc)){
        $cpt=count($obj->$sectionFieldDoc);
      } else if (substr($section,0,14)=='Versionproject' and property_exists($obj,$sectionFieldVP ) and is_array($obj->$sectionFieldVP)){
        $cpt=count($obj->$sectionFieldVP);
      } else if ($section=='Affectations') {
        $crit=array('idProject=>'=>'0', 'idResource'=>'0');
        if ($classObj=='Project') {
          $crit=array('idProject'=>$obj->id);
        } else {
          $crit=array('idResource'=>$obj->id);
        }
        $aff=new Affectation();
        $cpt=$aff->countSqlElementsFromCriteria($crit);
      } else {
        // echo "***** $section *****<br/>";
      }
      // Determine colSpan
      $colSpan=null;
      $colSpanSection='_'.lcfirst($section).'_colSpan';
      if ( property_exists($obj,$colSpanSection) ) {
        $colSpan=$obj->$colSpanSection;
      }
      $widthPct=setWidthPct($displayWidth, $print, $printWidth,$obj,$colSpan);
      if ($col=='_sec_void') {
        if ($prevSection) {
          echo '</table>';
          if (!$print) {
            echo '</div>';
          } else {
            echo '<br/>';
          }
        }
        if (!$print) {
          echo '<div style="float:left;width:'.$widthPct.'" ><table><tr><td>&nbsp;</td></tr>';
        } else {
          echo '<table>';
        }
      } else {
        startTitlePane($classObj, $section, $collapsedList, $widthPct, $print, $outMode, $prevSection, $nbCol, $cpt);
      }
    } else if (substr($col, 0, 5) == '_spe_') { // if field is _spe_xxxx, draw the specific item xxx
      $item = substr($col, 5);
      if ($internalTable) {
        if ($internalTable % $internalTableCols == 0) {
          echo '</td><td>' . $cr;
          $internalTableCurrentRow++;
        } else {
          echo '</td><td>';
        }
      } else {
        echo '<tr><td colspan=2>';
      }
      echo $obj->drawSpecificItem($item); // the method must be implemented in the corresponidng class
      if ($internalTable) {
        // echo '<td>';
      } else {
        echo '</td></tr>';
      }
/*    } else if (substr($col, 0, 5) == '_spe_') { // if field is _spe_xxxx, draw the specific item xxx
      $item=substr($col, 5);
      if ($internalTable) echo '<td>';
      else   echo '<tr><td colspan=2>';
      echo $obj->drawSpecificItem($item); // the method must be implemented in the corresponidng class
      if ($internalTable) echo '<td>';
      else   echo '</td></tr>';*/
    } else if (substr($col, 0, 6) == '_calc_') { // if field is _calc_xxxx, draw calculated item
      $item=substr($col, 6);
      echo $obj->drawCalculatedItem($item); // the method must be implemented in the corresponidng class
    } else if (substr($col, 0, 5) == '_lib_') { // if field is just a caption
      $item=substr($col, 5);
      if (strpos($obj->getFieldAttributes($col), 'nobr') !== false) {
        $nobr=true;
      }
      if ($obj->getFieldAttributes($col) != 'hidden') {
        if ($nobr)
          echo '&nbsp;';
        echo '<span class="tabLabel" style="font-weight:normal">' . i18n($item) . '</span>';
        echo '&nbsp;';
      }
      
      if (!$nobr) {
        echo "</td></tr>";
      }
    } else if (substr($col, 0, 5) == '_Link' and ! $comboDetail) { // Display links to other objects
      $linkClass=null;
      if (strlen($col) > 5) {
        $linkClass=substr($col, 6);
      }
      drawLinksFromObject($val, $obj, $linkClass);
    } else if ($col == '_productComposition' and !$obj->isAttributeSetToField($col, "hidden")) { // Display Composition of Product (structure)
      drawStructureFromObject($obj, false,'composition', 'Product');
    } else if ($col == '_componentComposition' and !$obj->isAttributeSetToField($col, "hidden")) { // Display Composition of component (structure)
      drawStructureFromObject($obj, false,'composition', 'Component');
    } else if ($col == '_componentStructure' and !$obj->isAttributeSetToField($col, "hidden")) { // Display Structure of component (structure)
      drawStructureFromObject($obj, false,'structure', 'Component');
    } else if ($col == '_productVersionComposition' and !$obj->isAttributeSetToField($col, "hidden")) { // Display ProductVersionStructure (structure)
      drawVersionStructureFromObject($obj, false, 'composition', 'ProductVersion');
    } else if ($col == '_componentVersionStructure' and !$obj->isAttributeSetToField($col, "hidden")) { // Display ProductVersionStructure (structure)
      drawVersionStructureFromObject($obj, false, 'structure', 'ComponentVersion');
    } else if ($col == '_componentVersionComposition' and !$obj->isAttributeSetToField($col, "hidden")) { // Display ProductVersionStructure (structure)
      drawVersionStructureFromObject($obj, false, 'composition', 'ComponentVersion');
    } else if (substr($col, 0, 11) == '_Assignment') { // Display Assignments
      drawAssignmentsFromObject($val, $obj);
    } else if (substr($col, 0, 11) == '_Approver') { // Display Assignments
      drawApproverFromObject($val, $obj);
    } else if (substr($col, 0, 15) == '_VersionProject') { // Display Version Project
      drawVersionProjectsFromObject($val, $obj);
    } else if (substr($col, 0, 15) == '_ProductProject') { // Display Version Project
      drawProductProjectsFromObject($val, $obj);  
    } else if (substr($col, 0, 11) == '_Dependency') { // Display Dependencies
      $depType=(strlen($col) > 11)?substr($col, 12):"";
      drawDependenciesFromObject($val, $obj, $depType);
    } else if ($col == '_ResourceCost') { // Display ResourceCost
      drawResourceCostFromObject($val, $obj, false);
    } else if ($col == '_DocumentVersion') { // Display ResourceCost
      drawDocumentVersionFromObject($val, $obj, false);
    } else if ($col == '_ExpenseDetail') { // Display ExpenseDetail
      if ($obj->getFieldAttributes($col) != 'hidden') {
        drawExpenseDetailFromObject($val, $obj, false);
      }
    } else if (substr($col, 0, 12) == '_TestCaseRun') { // Display TestCaseRun
      drawTestCaseRunFromObject($val, $obj);
    } else if (substr($col, 0, 11) == '_Attachment' and ! $comboDetail) {
      if (!isset($isAttachmentEnabled)) {
        $isAttachmentEnabled=true; // allow attachment
        if (!Parameter::getGlobalParameter('paramAttachmentDirectory') or !Parameter::getGlobalParameter('paramAttachmentMaxSize')) {
          $isAttachmentEnabled=false;
        }
      }
      if ($isAttachmentEnabled and !$comboDetail) {
        $prevSection=$section;
        $section="Attachment";
        $ress=new Resource(getCurrentUserId());
        $cpt=0;
        foreach($obj->_Attachment as $cptObjTmp) {
          if ($user->id == $cptObjTmp->idUser or $cptObjTmp->idPrivacy == 1 or ($cptObjTmp->idPrivacy == 2 and $ress->idTeam == $cptObjTmp->idTeam)) {
            $cpt++;
          }
        }
        startTitlePane($classObj, $section, $collapsedList, $widthPct, $print, $outMode, $prevSection,$nbCol,$cpt);
        drawAttachmentsFromObject($obj, false);
      }
    } else if (substr($col, 0, 5) == '_Note' and ! $comboDetail) {
      $prevSection=$section;
      $section="Note";
      $ress=new Resource(getCurrentUserId());
      $cpt=0;
      foreach($obj->_Note as $cptObjTmp) {
        if ($user->id == $cptObjTmp->idUser or $cptObjTmp->idPrivacy == 1 or ($cptObjTmp->idPrivacy == 2 and $ress->idTeam == $cptObjTmp->idTeam)) {
          $cpt++;
        }
      }
      startTitlePane($classObj, $section, $collapsedList, $widthPct, $print, $outMode, $prevSection, $nbCol,$cpt);
      drawNotesFromObject($obj, false);
    } else if ($col== '_BillLine') {
      $prevSection=$section;
      $section="BillLine";
      $colSpanSection='_'.lcfirst($section).'_colSpan';
      if ( property_exists($obj,$colSpanSection) ) {
        $colSpan=$obj->$colSpanSection;
      }
      $widthPct=setWidthPct($displayWidth, $print, $printWidth,$obj,"2");
      startTitlePane($classObj, $section, $collapsedList, $widthPct, $print, $outMode, $prevSection, $nbCol,count($val));
      drawBillLinesFromObject($obj, false);
    } else if (substr($col, 0, 1) == '_' and 
    substr($col, 0, 6) != '_void_' and substr($col, 0, 7) != '_label_' and substr($col, 0, 8) != '_button_') { // field not to be displayed
                                                                                                                              //
    } else {
      $attributes='';
      $isRequired=false;
      $readOnly=false;
      $specificStyle='';
      if (($col == "idle" or $col == "done" or $col == "handled" or $col == "cancelled" or $col == "solved") and $objType) {
        $lock='lock' . ucfirst($col);
        if (!$obj->id or (property_exists($objType, $lock) and $objType->$lock)) {
          $attributes.=' readonly tabindex="-1"';
          $readOnly=true;
        }
      }
      if (strpos($obj->getFieldAttributes($col), 'required') !== false) {
        //$attributes.=' required="true" missingMessage="' . i18n('messageMandatory', array($obj->getColCaption($col))) . '" invalidMessage="' . i18n('messageMandatory', array($obj->getColCaption($col))) . '"';
        $isRequired=true;
      }
      if (array_key_exists($col, $arrayRequired)) {
        $attributes.=' required="true" missingMessage="' . i18n('messageMandatory', array($obj->getColCaption($col))) . '" invalidMessage="' . i18n('messageMandatory', array($obj->getColCaption($col))) . '"';
        $isRequired=true;
      }
      if (strpos($obj->getFieldAttributes($col), 'hidden') !== false) {
        $hide=true;
      } else if (in_array($col,$extraHiddenFields)) {
        $specificStyle.=' display:none';
        if ($print) $hide=true;
      }
      if (($col == 'idUser' or $col == 'creationDate' or $col == 'creationDateTime' || $col=='lastUpdateDateTime') and !$print) {
        $hide=true;
      }
      if (strpos($obj->getFieldAttributes($col), 'nobr') !== false) {
        $nobr=true;
      }
      if (strpos($obj->getFieldAttributes($col), 'invisible') !== false) {
        $specificStyle.=' visibility:hidden';
      }
      if (strpos($obj->getFieldAttributes($col), 'title') !== false) {
        $attributes.=' title="' . $obj->getTitle($col) . '"';
      }
      if ($col=='idComponent' or $col=='idComponentVersion' or $col=='idOriginalComponentVersion' or $col=='idTargetComponentVersion') {
        if (Component::canViewComponentList($obj)!='YES') {
          $hide=true;
        }
      }
      if (!$canUpdate or (strpos($obj->getFieldAttributes($col), 'readonly') !== false) or $parentReadOnly or ($obj->idle == 1 and $col != 'idle' and $col != 'idStatus')) {
        $attributes.=' readonly tabindex="-1"';
        $readOnly=true;
      }
	  if($col == 'progress') {
		// Determine if progress field is read only
		if ($obj->leftWork != 0 || $obj->realWork != 0) { // If work has been assigned, progress is calculated
		  $attributes .= ' readonly tabindex="-1"';
		  $readOnly = true;
		} else {
		  if (isset($orig) && $orig) {
			if (property_exists($orig, 'handled') && !$orig->handled) { // If object is not handled yet, progress is 0 %
			  $attributes .= ' readonly tabindex="-1"';
			  $readOnly = true;
			} elseif ($orig->done) { // If object is done, progress is 100 %
			  $attributes .= ' readonly tabindex="-1"';
			  $readOnly = true;
			}
		  } else {
			$attributes .= ' readonly tabindex="-1"';
			$readOnly = true;
		  }
		}
	  }
      $dataType=$obj->getDataType($col);
      $dataLength=$obj->getDataLength($col);
      if ( $obj->isAttributeSetToField($col,'calculated') and (substr($col, -4, 4) == 'Cost' or substr($col, -6, 6) == 'Amount' or $col == 'amount')) {
        $dataType='decimal';
      }
      if ($internalTable == 0) {
        if (!is_object($val) and !is_array($val) and !$hide and !$nobr_before) {
          echo '<tr class="detail'.((!$nobr)?' generalRowClass '.$col.'Class':'').'" style="'.((!$nobr)?$specificStyle:'').'">';
          if ($dataLength > 4000 and getEditorType()!='text') {
            // Will have to add label
            echo '<td colspan="2">';
          } else {
            echo '<td class="label" style="width:' . $labelStyleWidth . ';">';
            $thumbRes=SqlElement::isThumbableField($col);
            $thumbColor=SqlElement::isColorableField($col);
            $formatedThumb='';
            if ($thumbRes) {
              $formatedThumb=formatUserThumb($val, null, null, 22, 'right');
            } else if ($thumbColor) {
              $formatedThumb=formatColorThumb($col, $val, 20, 'right');              
            }
            // $thumbIcon=SqlElement::isIconableField($col);
            $thumb=(!$print && $val && ($thumbRes or $thumbColor) && $showThumb && $formatedThumb)?true:false;
            echo '<label for="' . $col . '" class="' . (($thumb)?'labelWithThumb ':'').'generalColClass '.$col.'Class" style="'.$specificStyle.'">';
            if ($outMode == 'pdf') { 
              echo str_replace(' ', '&nbsp;',htmlEncode($obj->getColCaption($col),'stipAllTags'));
            } else {
              echo htmlEncode($obj->getColCaption($col),'stipAllTags');
            }
            echo '&nbsp;' . (($thumb)?'':':&nbsp;') . '</label>' . $cr;
            if ($thumb) {
              echo $formatedThumb;
            }
            echo '</td>';
            if ($print and $outMode == "pdf") {
              echo '<td style="width:' . ($largeWidth + 10) . 'px">';
            } else {
              echo '<td style="width:' . ($largeWidth + 10) . 'px">';
            }
          }
        }
      } else {
        //$internalTableBorder=($print)?'border:1px dotted #A0A0A0;':'';
        $internalTableBorder='';
        if ($internalTable % $internalTableCols == 0) {
          echo '</td></tr>' . $cr;
          echo '<tr class="detail">';
          echo '<td class="' . $internalTableSpecial . '" style="text-align:right;width:' . $labelStyleWidth . ';">';
          if ($internalTableRowsCaptions [$internalTableCurrentRow] and $arrTab['rows'][$internalTableCurrentRow]>0) {
            echo '<label class="label ' . $internalTableSpecial . '">' . htmlEncode($obj->getColCaption($internalTableRowsCaptions [$internalTableCurrentRow])) . '&nbsp;:&nbsp;</label>';
          }
          echo '</td><td style="width:90%;white-space:nowrap;'.$internalTableBorder.'">';
          $internalTableCurrentRow++;
        } else {
          if ($obj->isAttributeSetToField($col, "colspan3")) {
            echo '</td><td class="detail" colspan="3">';
            $internalTable-=2;
          } else {            
            echo '</td><td class="detail" style="white-space:nowrap;'.$internalTableBorder.'">';
          }
        }
      }
      // echo $col . "/" . $dataType . "/" . $dataLength;
      if ($dataLength) {
        if ($dataLength <= 3) {
          $fieldWidth=$verySmallWidth;
        } else if ($dataLength <= 10) {
          $fieldWidth=$smallWidth;
        } else if ($dataLength <= 25) {
          $fieldWidth=$mediumWidth;
        } else {
          $fieldWidth=$largeWidth;
        }
      }
      if (substr($col, 0, 2) == 'id' and $dataType == 'int' and strlen($col) > 2 and substr($col, 2, 1) == strtoupper(substr($col, 2, 1))) {
        $fieldWidth=$largeWidth;
      }
      if (strpos($obj->getFieldAttributes($col), 'Width') !== false) {
        if (strpos($obj->getFieldAttributes($col), 'smallWidth') !== false) {
          $fieldWidth=$smallWidth;
        }
        if (strpos($obj->getFieldAttributes($col), 'mediumWidth') !== false) {
          $fieldWidth=$mediumWidth;
        }
        if (strpos($obj->getFieldAttributes($col), 'truncatedWidth') !== false) {
          $pos=strpos($obj->getFieldAttributes($col), 'truncatedWidth');
          $truncValue=substr($obj->getFieldAttributes($col), $pos + 14, 3);
          $fieldWidth-=$truncValue;
        }
      }
      // echo $dataType . '(' . $dataLength . ') ';
      if ($included) {
        $name=' id="' . $classObj . '_' . $col . '" name="' . $classObj . '_' . $col . $extName . '" ';
        $nameBis=' id="' . $classObj . '_' . $col . 'Bis" name="' . $classObj . '_' . $col . 'Bis' . $extName . '" ';
        $fieldId=$classObj . '_' . $col;
      } else {
        $name=' id="' . $col . '" name="' . $col . $extName . '" ';
        $nameBis=' id="' . $col . 'Bis" name="' . $col . 'Bis' . $extName . '" ';
        $fieldId=$col;
      }
      // prepare the javascript code to be executed
      $colScript=$obj->getValidationScript($col);
      $colScriptBis="";
      if ($dataType == 'datetime') {
        $colScriptBis=$obj->getValidationScript($col . "Bis");
      }
      // if ($comboDetail) {
      // $colScript=str_replace($col,$col . $extName,$colScript);
      // $colScriptBis=str_replace($col,$col . $extName,$colScriptBis);
      // }
      if (is_object($val)) {
        if (!$obj->isAttributeSetToField($col, 'hidden')) {
          if ($col == 'Origin') {
            drawOrigin($val->originType, $val->originId, $obj, $col, $print);
          } else {
            // Draw an included object (recursive call) =========================== Type Object
            $visibileSubObject=true;
            if (get_class($val) == 'WorkElement') {
              $hWork=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile' => $profile,'scope' => 'work'));
              if ($hWork and $hWork->id) {
                $visibility=SqlList::getFieldFromId('VisibilityScope', $hWork->rightAccess, 'accessCode', false);
                if ($visibility != 'ALL') {
                  $visibileSubObject=false;
                }
              }
            }
            if ($visibileSubObject) {
              drawTableFromObject($val, true, $readOnly);
              $hide=true; // to avoid display of an extra field for the object and an additional carriage return
            }
          }
        }
      } else if (is_array($val)) {
        // Draw an array ====================================================== Type Array
        traceLog("Error : array fileds management not implemented for fiels $col");
      } else if (substr($col, 0, 6) == '_void_') {
        // Empty field for tabular presentation
        // echo $col . ' is an array' . $cr;
        //
      } else if (substr($col, 0, 7) == '_label_') {
        $captionName=substr($col, 7);
        echo '<label class="label shortlabel">' . i18n('col' . ucfirst($captionName)) . '&nbsp;:&nbsp;</label>';
       
      } else if (substr($col, 0, 8) == '_button_') {
        if (! $print and !$comboDetail and !$obj->isAttributeSetToField($col,'hidden')) {
          $item=substr($col, 8);
          echo $obj->drawSpecificItem($item);
        }
      } else if ($print) { 
        // ============================================================================================================
        // ================================================
        // ================================================ PRINT
        // ================================================
        // ============================================================================================================
        if ($hide) { // hidden field
                       // nothing
        } else if (strpos($obj->getFieldAttributes($col), 'displayHtml') !== false) {
          // Display full HTML ================================================== Hidden field
          // echo '<div class="displayHtml">';
          if ($outMode == 'pdf') {
            echo htmlRemoveDocumentTags($val);
          } else {
            echo $val;
          }
        } else if ($col == 'id') { // id
          echo '<span style="color:grey;">#' . $val . "&nbsp;&nbsp;&nbsp;</span>";
        } else if ($col == 'password') {
          echo "..."; // nothing
        } else if ($dataType == 'date' and $val != null and $val != '') {
          echo htmlFormatDate($val);
        } else if ($dataType == 'datetime' and $val != null and $val != '') {
          //echo str_replace(' ','&nbsp;',htmlFormatDateTime($val, false));
          echo htmlFormatDateTime($val, false);
        } else if ($dataType == 'time' and $val != null and $val != '') {
          echo htmlFormatTime($val, false);
        } else if ($col == 'color' and $dataLength == 7) { // color
          echo '<table><tr><td style="width: 100px;">';
          echo '<div class="colorDisplay" readonly tabindex="-1" ';
          echo '  value="' . htmlEncode($val) . '" ';
          echo '  style="width: ' . $smallWidth / 2 . 'px; border-radius:10px;';
          echo ' color: ' . $val . '; ';
          echo ' background-color: ' . $val . ';"';
          echo ' >';
          echo '</div>';
          echo '</td>';
          if ($val != null and $val != '') {
            // echo '<td class="detail">&nbsp;(' . htmlEncode($val) . ')</td>';
          }
          echo '</tr></table>';
        } else if ($dataType == 'int' and $dataLength == 1) { // boolean
          $checkImg="checkedKO.png";
          if ($val != '0' and !$val == null) {
            $checkImg='checkedOK.png';
          }
          if ($col=='cancelled' or $col=='solved') echo "&nbsp;&nbsp;&nbsp;";
          echo '<img src="img/' . $checkImg . '" />';
        } else if (substr($col, 0, 2) == 'id' and $dataType == 'int' and strlen($col) > 2 and substr($col, 2, 1) == strtoupper(substr($col, 2, 1))) { // Idxxx
          echo htmlEncode(SqlList::getNameFromId(substr($col, 2), $val));
        } else if ($dataLength > 4000) {
          // echo '</td></tr><tr><td colspan="2">';
          echo '<div style="text-align:left;font-weight:normal" class="tabLabel">'.htmlEncode($obj->getColCaption($col),'stipAllTags').'&nbsp;:&nbsp;</div>';
          echo '<div style="border:1px dotted #AAAAAA;width:' . $colWidth . 'px;">';
          $val=htmlEncode($val,'formatted');
          if ($outMode=="pdf") { // Must purge data, otherwise will never be generated
            if ($preseveHtmlFormatingForPDF) {
              $val='<div>'.$val.'</div>';
            } else {
              $val=htmlEncode($val,'pdf'); // remove all tags but line breaks
            }            
          }
          echo $val.'&nbsp;';
          echo '</div>';
        } else if ($dataLength > 100) { // Text Area (must reproduce BR, spaces, ...
          echo htmlEncode($val, 'print');
          $fldFull='_' . $col . '_full';
          if ($outMode == 'pdf' and isset($obj->$fldFull)) {
            echo '<img src="../view/css/images/doubleArrowDown.png" />';
          }
        } else if ($dataType == 'decimal' and (substr($col, -4, 4) == 'Cost' or substr($col, -6, 6) == 'Amount' or $col == 'amount')) {
          if ($currencyPosition == 'after') {
            echo htmlEncode($val, 'print') . ' ' . $currency;
          } else {
            echo $currency . ' ' . htmlEncode($val, 'print');
          }
        } else if ($dataType == 'decimal' and substr($col, -4, 4) == 'Work') {
          echo Work::displayWork($val) . ' ' . Work::displayShortWorkUnit();
        } else if (strtolower(substr($col, -8, 8)) == 'progress' or substr($col, -3, 3) == 'Pct') {
            echo $val.'&nbsp;%';
        } else if ($col == 'icon') {
          if ($val) {
            echo '<img src="../view/icons/' . $val . '" />';
          }
        } else {
          if ($obj->isFieldTranslatable($col)) {
            $val=i18n($val);
          }
          if (0 and $internalTable == 0) {
            echo '<div style="width: 80%;"> ';
            if (strpos($obj->getFieldAttributes($col), 'html') !== false) {
              echo $val;
            } else {
              echo htmlEncode($val, 'print');
            }
            echo '</div>';
          } else {
            if (strpos($obj->getFieldAttributes($col), 'html') !== false) {
              echo $val;
            } else {
              echo htmlEncode($val, 'print');
            }
          }
        }
        // ============================================================================================================
        // ================================================
        // ================================================ END OF PRINT : Entering general case
        // ================================================
        // ============================================================================================================    
      } else if ($hide) {
        // Don't draw the field =============================================== Hidden field
        if (!$print) {
          if ($col == 'creationDate' and ($val == '' or $val == null) and !$obj->id) {
            $val=date('Y-m-d');
          }
          if ($col == 'creationDateTime' and ($val == '' or $val == null) and !$obj->id) {
            $val=date('Y-m-d H:i:s');
          }
          if ($col == 'idUser' and ($val == '' or $val == null) and !$obj->id) {
            $val=$user->id;
          }
          echo '<div dojoType="dijit.form.TextBox" type="hidden"  ';
          echo $name;
          if ($dataType == 'decimal' and (substr($col, -4, 4) == 'Work')) {
            $val=Work::displayWork($val);
          }
          echo ' value="' . htmlEncode($val) . '" ></div>';
        }
      } else if (strpos($obj->getFieldAttributes($col), 'displayHtml') !== false) {
        // Display full HTML ================================================== Simple Display html field
        echo '<div class="displayHtml generalColClass '.$col.'Class" style="'.$specificStyle.'">';
        echo $val;
        echo '</div>';
      } else if ($col == 'id') {
        // Draw Id (only visible) ============================================= ID
        // id is only visible
        $ref=$obj->getReferenceUrl();
        echo '<span class="roundedButton" style="padding:1px 5px 5px 5px;font-size:8pt; height: 50px; color:#AAAAAA;" >';
        echo '  <a  href="' . $ref . '" onClick="copyDirectLinkUrl();return false;"' . ' title="' . i18n("rightClickToCopy") . '" style="cursor: pointer;">';
        echo '    <span style="color:grey;vertical-align:middle;padding: 2px 0px 2px 0px !important;">#</span>';
        echo '    <span dojoType="dijit.form.TextBox" type="text"  ';
        echo $name;
        echo '     class="display pointer" ';
        echo '     readonly tabindex="-1" style="background: transparent; border: 0; cursor: pointer !important;width: ' . $smallWidth . 'px; padding: 2px 0px 2px 0px !important;" ';
        echo '     value="' . htmlEncode($val) . '" >';
        echo '    </span>';
        echo '  </a>';
        echo '</span>';
        echo '<input readOnly type="text" onClick="this.select();" id="directLinkUrlDiv" style="display:none;font-size:9px; color: #000000;position :absolute; top: 9px; left: 157px; border: 0;background: transparent;width:' . $largeWidth . 'px;" value="' . $ref . '" />';
        $alertLevelArray=$obj->getAlertLevel(true);
        $alertLevel=$alertLevelArray ['level'];
        $colorAlert="background-color:#FFFFFF";
        if ($alertLevel != 'NONE') {
          if ($alertLevel == 'ALERT') {
            $colorAlert='background-color:#FFAAAA;';
          } else if ($alertLevel == 'WARNING') {
            $colorAlert='background-color:#FFFFAA;';
          }
          echo '<span style="width:20px; position: absolute; left: 5px;" id="alertId" >';
          if ($alertLevel == 'ALERT') {
            echo '<image src="../view/css/images/indicatorAlert32.png" />';
          } else {
            echo '<image src="../view/css/images/indicatorWarning32.png" />';
          }
          echo '</span>';
          echo '<div dojoType="dijit.Tooltip" connectId="alertId" position="below">';
          echo $alertLevelArray ['description'];
          echo '</div>';
        }
      } else if ($col == 'reference') {
        // Draw reference (only visible) ============================================= ID
        // id is only visible
        echo '<span dojoType="dijit.form.TextBox" type="text"  ';
        echo $name;
        echo ' class="display generalColClass '.$col.'Class" ';
        echo ' readonly tabindex="-1" style="'.$specificStyle.';width: ' . ($largeWidth - $smallWidth - 40) . 'px;" ';
        echo ' value="' . htmlEncode($val) . '" ></span>';
      } else if ($col == 'password') {
        $paramDefaultPassword=Parameter::getGlobalParameter('paramDefaultPassword');
        // Password specificity ============================================= PASSWORD
        if ($canUpdate) {
          echo '<button id="resetPassword" dojoType="dijit.form.Button" showlabel="true"';
          echo ' class="generalColClass '.$col.'Class" style="'.$specificStyle.'"';
          echo $attributes;
          $salt=hash('sha256', "projeqtor" . date('YmdHis'));
          echo ' title="' . i18n('helpResetPassword') . '" >';
          echo '<span>' . i18n('resetPassword') . '</span>';
          echo '<script type="dojo/connect" event="onClick" args="evt">';
          echo '  dijit.byId("salt").set("value","' . $salt . '");';
          echo '  dijit.byId("crypto").set("value","sha256");';
          echo '  dojo.byId("password").value="' . hash('sha256', $paramDefaultPassword . $salt) . '";';
          echo '  formChanged();';
          echo '  showInfo("' . i18n('passwordReset', array($paramDefaultPassword)) . '");';
          echo '</script>';
          echo '</button>';
        }
        // password not visible
        echo '<input type="password"  ';
        echo $name;
        echo ' class="display generalColClass '.$col.'Class" style="width:150px;position:relative; left: 3px;'.$specificStyle.'"';
        echo ' readonly tabindex="-1" ';
        echo ' value="' . htmlEncode($val) . '" />';
      } else if ($col == 'color' and $dataLength == 7) {
        // Draw a color selector ============================================== COLOR
        echo '<table class="generalColClass '.$col.'Class" style="'.$specificStyle.'"><tr><td class="detail">';
        echo '<input xdojoType="dijit.form.TextBox" class="colorDisplay" type="text" readonly tabindex="-1" ';
        echo $name;
        echo $attributes;
        echo '  value="' . htmlEncode($val) . '" ';
        echo '  style="border-radius:10px; height:20px; border: 0;width: ' . $smallWidth . 'px; ';
        echo ' color: ' . $val . '; ';
        if ($val) {
          echo ' background-color: ' . $val . ';';
        } else {
          echo ' background-color: transparent;';
        }
        echo '" />';
        // echo $colScript;
        // echo '</div>';
        echo '</td><td class="detail">';
        if (!$readOnly) {
          echo '<div id="' . 'colorButton" dojoType="dijit.form.DropDownButton"  ';
          // echo ' style="width: 100px; background-color: ' . $val . ';"';
          echo ' showlabel="false" iconClass="colorSelector" style="position:relative;top:-2px;height:19px">';
          echo '  <span>' . i18n('selectColor') . '</span>';
          echo '  <div dojoType="dijit.ColorPalette" >';
          echo '    <script type="dojo/method" event="onChange" >';
          echo '      var fld=dojo.byId("color");';
          echo '      fld.style.color=this.value;';
          echo '      fld.style.backgroundColor=this.value;';
          echo '      fld.value=this.value;';
          echo '      formChanged();';
          echo '    </script>';
          echo '  </div>';
          echo '</div>';
        }
        echo '</td><td>';
        if (!$readOnly) {
          echo '<button id="resetColor" dojoType="dijit.form.Button" showlabel="true"';
          echo ' title="' . i18n('helpResetColor') . '" >';
          echo '<span>' . i18n('resetColor') . '</span>';
          echo '<script type="dojo/connect" event="onClick" args="evt">';
          echo '      var fld=dojo.byId("color");';
          echo '      fld.style.color="transparent";';
          echo '      fld.style.backgroundColor="transparent";';
          echo '      fld.value="";';
          echo '      formChanged();';
          echo '</script>';
          echo '</button>';
        }
        echo '</td></tr></table>';
      } else if ($col == 'durationSla') {
        // Draw a color selector ============================================== SLA as a duration
        echo '<div class="generalColClass '.$col.'Class" style="width: 30px;'.$specificStyle.'">';
        echo '<div dojoType="dijit.form.TextBox" class="colorDisplay generalColClass '.$col.'Class" type="text"  ';
        echo $name;
        echo $attributes;
        echo '  value="' . htmlEncode($val) . '" ';
        echo '  style="width: 30px;'.$specificStyle.'"';
        echo ' >';
        echo '</div>';
        echo i18n("shortDay") . "  ";
        echo '<div dojoType="dijit.form.TextBox" class="colorDisplay" type="text"  ';
        echo $attributes;
        echo '  value="' . htmlEncode($val) . '" ';
        echo '  style="width: 30px; "';
        echo ' >';
        echo '</div>';
        echo i18n("shortHour") . "  ";
        echo '<div dojoType="dijit.form.TextBox" class="colorDisplay" type="text"  ';
        echo $attributes;
        echo '  value="' . htmlEncode($val) . '" ';
        echo '  style="width: 30px; "';
        echo ' >';
        echo '</div>';
        echo i18n("shortMinute") . "  ";
        echo "</div>";
      } else if ($dataType == 'date') {
        // Draw a date ======================================================== DATE
        if ($col == 'creationDate' and ($val == '' or $val == null) and !$obj->id) {
          $val=date('Y-m-d');
        }
        $negative='';
        if (property_exists($obj, 'validatedEndDate')) {
          $negative=($col=="plannedEndDate" and $obj->plannedEndDate and $obj->validatedEndDate and $obj->plannedEndDate>$obj->validatedEndDate )?'background-color: #FFAAAA !important;':'';
        }        
        echo '<div dojoType="dijit.form.DateTextBox" ';
        echo $name;
        echo $attributes;
        echo ' invalidMessage="' . i18n('messageInvalidDate') . '"';
        echo ' type="text" maxlength="' . $dataLength . '" ';
        if (isset($_SESSION ['browserLocaleDateFormatJs'])) {
          echo ' constraints="{datePattern:\'' . $_SESSION ['browserLocaleDateFormatJs'] . '\'}" ';
        }
        echo ' style="'.$negative.'width:' . $dateWidth . 'px; text-align: center;' . $specificStyle . '" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        echo ' value="' . htmlEncode($val) . '" ';
        echo ' hasDownArrow="false" ';
        echo ' >';
        echo $colScript;
        echo '</div>';
      } else if ($dataType == 'datetime') {
        // Draw a date ======================================================== DATETIME
        if (strlen($val > 11)) {
          $valDate=substr($val, 0, 10);
          $valTime=substr($val, 11);
        } else {
          $valDate=$val;
          $valTime='';
        }
        if ($col == 'creationDateTime' and ($val == '' or $val == null) and !$obj->id) {
          $valDate=date('Y-m-d');
          $valTime=date("H:i");
        }
        echo '<div dojoType="dijit.form.DateTextBox" ';
        echo $name;
        echo $attributes;
        echo ' invalidMessage="' . i18n('messageInvalidDate') . '"';
        echo ' type="text" maxlength="10" ';
        if (isset($_SESSION ['browserLocaleDateFormatJs'])) {
          echo ' constraints="{datePattern:\'' . $_SESSION ['browserLocaleDateFormatJs'] . '\'}" ';
        }
        echo ' style="width:' . $dateWidth . 'px; text-align: center;' . $specificStyle . '" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        echo ' value="' . $valDate . '" ';
        echo ' hasDownArrow="false" ';
        echo ' >';
        echo $colScript;
        echo '</div>';
        $fmtDT=($classObj == "Audit" && strlen($valTime) > 5 && strpos($attributes, 'readonly') !== false)?'text':'time'; // valTime=substr($valTime,0,5);
        echo '<div dojoType="dijit.form.' . (($fmtDT == 'time')?'Time':'') . 'TextBox" ';
        echo $nameBis;
        echo $attributes;
        echo ' invalidMessage="' . i18n('messageInvalidTime') . '"';
        echo ' type="text" maxlength="8" ';
        // echo ' constraints="{datePattern:\'yy-MM-dd\'}" ';
        echo ' style="width:60px; text-align: center;' . $specificStyle . '" class="input '.(($isRequired)?'required':'').'" ';
        echo ' value="' . (($fmtDT == 'time')?'T':'') . $valTime . '" ';
        echo ' hasDownArrow="false" ';
        echo ' >';
        echo $colScriptBis;
        echo '</div>';
      } else if ($dataType == 'time') {
        // Draw a date ======================================================== TIME
        if ($col == 'creationTime' and ($val == '' or $val == null) and !$obj->id) {
          $val=date("H:i");
        }
        $fmtDT=($classObj == "Audit" && strlen($val) > 5 && strpos($attributes, 'readonly') !== false)?'text':'time'; // valTime=substr($valTime,0,5);
        echo '<div dojoType="dijit.form.' . (($fmtDT == 'time')?'Time':'') . 'TextBox" ';
        echo $name;
        echo $attributes;
        echo ' invalidMessage="' . i18n('messageInvalidTime') . '"';
        echo ' type="text" maxlength="' . $dataLength . '" ';
        // echo ' constraints="{datePattern:\'yy-MM-dd\'}" ';
        echo ' style="width:' . (($fmtDT == 'time')?'60':'65') . 'px; text-align: center;' . $specificStyle . '" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        echo ' value="' . (($fmtDT == 'time')?'T':'') . $val . '" ';
        echo ' hasDownArrow="false" ';
        echo ' >';
        echo $colScript;
        echo '</div>';
      } else if ($dataType == 'int' and $dataLength == 1) {
        if ($col=='cancelled' or $col=='solved') echo "&nbsp;&nbsp;&nbsp;";
        // Draw a boolean (as a checkbox ====================================== BOOLEAN
        echo '<div dojoType="dijit.form.CheckBox" type="checkbox" ';
        echo $name;
        echo ' class="greyCheck generalColClass '.$col.'Class"';
        echo $attributes;
        echo ' style="' . $specificStyle . '" ';
        // echo ' value="' . $col . '" ' ;
        if ($val != '0' and !$val == null) {
          echo 'checked';
        }
        echo ' >';
        echo $colScript;
        if (!strpos('formChanged()',$colScript)) {
          echo '<script type="dojo/connect" event="onChange" args="evt">';
  			  echo '    formChanged();';
  			  echo '</script>';
        }
        echo '</div>';
      } else if (substr($col, 0, 2) == 'id' and $dataType == 'int' and strlen($col) > 2 and substr($col, 2, 1) == strtoupper(substr($col, 2, 1))) {
        // Draw a reference to another object (as combo box) ================== IDxxxxx => ComboBox (as a FilteringSelect)
        $displayComboButtonCol=$displayComboButton;
        $displayDirectAccessButton=true;
        $canCreateCol=false;
        if ($comboDetail or strpos($attributes, 'readonly') !== false) {
          $displayComboButtonCol=false;
        }
        if (strpos($obj->getFieldAttributes($col), 'nocombo') !== false) {
          $displayComboButtonCol=false;
          $displayDirectAccessButton=false;
        }
        if ($displayComboButtonCol or $displayDirectAccessButton) {
          $idMenu='menu' . substr($col, 2);
          $comboClass=substr($col, 2);
          if ($col == "idResourceSelect") {
            $idMenu='menuResource';
            $comboClass='Resource';
          } else if (substr($col,-14)=="ProductVersion") {
            $idMenu='menuProductVersion';
            $comboClass='ProductVersion';
          } else if (substr($col,-16)=="ComponentVersion") {
            $idMenu='menuComponentVersion';
            $comboClass='ComponentVersion';
          } 
          $menu=SqlElement::getSingleSqlElementFromCriteria('Menu', array('name' => $idMenu));
          $crit=array();
          $crit ['idProfile']=$profile;
          $crit ['idMenu']=$menu->id;
          $habil=SqlElement::getSingleSqlElementFromCriteria('Habilitation', $crit);
          if ($habil and $habil->allowAccess) {
            $accessRight=SqlElement::getSingleSqlElementFromCriteria('AccessRight', array('idMenu' => $menu->id,'idProfile' => $profile));
            if ($accessRight) {
              $accessProfile=new AccessProfile($accessRight->idAccessProfile);
              if ($accessProfile) {
                $accessScope=new AccessScope($accessProfile->idAccessScopeCreate);
                if ($accessScope and $accessScope->accessCode != 'NO') {
                  $canCreateCol=true;
                }
              }
            }
          } else {
            $displayComboButtonCol=false;
            $displayDirectAccessButton=false;
          }
        }
        if ( $col=='idProfile' and !$obj->id and !$val and ($classObj=='Resource' or $classObj=='User')) { // set default 
          $val=Parameter::getGlobalParameter('defaultProfile');
        }
        if ($col == 'idProject') {
          if ($obj->id == null) {
            if (array_key_exists('project', $_SESSION) and !$obj->$col) {
              $val=$_SESSION ['project'];
            }
            $accessRight=securityGetAccessRight('menu' . $classObj, 'create'); // TODO : study use of this variable...
          } else {
            $accessRight=securityGetAccessRight('menu' . $classObj, 'update'); // TODO : study use of this variable...
          }
          if (securityGetAccessRight('menu' . $classObj, 'read') == 'PRO' and $classObj != 'Project') {
            $isRequired=true; // TODO : study condition above : why security for 'read'', why not for project, ...
          }
          $controlRightsTable=$user->getAccessControlRights();
          $controlRights=$controlRightsTable['menu'.$classObj];
          if ($classObj=='Project' and $controlRights["create"]!="ALL" and $controlRights["create"]!="PRO") {
            $isRequired=true;
          }
        }
        $critFld=null;
        $critVal=null;
        $valStore='';
        if ($col == 'idResource' or $col == 'idActivity' or $col == 'idProduct' 
            or $col == 'idComponent' or $col == 'idProductOrComponent' 
            or $col == 'idProductVersion' or $col == 'idComponentVersion'
            or $col == 'idVersion' or $col == 'idOriginalVersion' or $col == 'idTargetVersion' 
            or $col == 'idOriginalProductVersion' or $col == 'idTargetProductVersion'
            or $col == 'idOriginalComponentVersion' or $col == 'idTargetComponentVersion' 
            or $col == 'idTestCase' or $col == 'idRequirement' or $col == 'idContact' 
            or $col == 'idTicket' or $col == 'idUser' or $col=='id'.$classObj.'Type') {
          if ($col == 'idContact' and property_exists($obj, 'idClient') and $obj->idClient) {
            $critFld='idClient';
            $critVal=$obj->idClient;
          } else   if ($col == 'idContact' and property_exists($obj, 'idProvider') and $obj->idProvider) {
              $critFld='idProvider';
              $critVal=$obj->idProvider;
          } else if (property_exists($obj, 'idProject') and get_class($obj) != 'Project' and get_class($obj) != 'Affectation') {
            if ($obj->id) {
              $critFld='idProject';
              $critVal=$obj->idProject;
            } else if ($obj->isAttributeSetToField('idProject', 'required')) {
              if (array_key_exists('project', $_SESSION) and $_SESSION ['project'] != '*') {
                $critFld='idProject';
                $critVal=$_SESSION ['project'];
              } else {
                $table=SqlList::getList('Project', 'name', null);
                $restrictArray=array();
                if (! $user->_accessControlVisibility) {
                  $user->getAccessControlRights(); // Force setup of accessControlVisibility
                }
                if ($user->_accessControlVisibility != 'ALL') {
                  $restrictArray=$user->getVisibleProjects(true);
                }
                if (count($table) > 0) {
                  foreach ( $table as $idTable => $valTable ) {
                    if (count($restrictArray)==0 or isset($restrictArray[$idTable])) {
                      $firstId=$idTable;
                      break;
                    }
                  }
                  $critFld='idProject';
                  $critVal=$firstId;
                }
              }
            }
          }
        }     
        if ($col=='idComponent' and isset($obj->idProduct)) {
          $critFld='idProduct';
          $critVal=$obj->idProduct;
        }
        // if version and idProduct exists and is set : criteria is product
        if ((isset($obj->idProduct) or isset($obj->idComponent) or isset($obj->idProductOrComponent)) 
        and ($col=='idVersion' or $col=='idProductVersion' or $col=='idComponentVersion' 
            or $col=='idOriginalVersion' or $col=='idTargetVersion'
            or $col=='idOriginalProductVersion' or $col=='idTargetProductVersion'
            or $col=='idOriginalComponentVersion' or $col=='idTargetComponentVersion' 
            or $col=='idTestCase' or ($col=='idRequirement' and (isset($obj->idProductOrComponent) or isset($obj->idProduct) )))) {
          if (isset($obj->idProduct) and ($col=='idVersion' or $col=='idTargetVersion' or $col=='idProductVersion'
                                       or $col=='idOriginalProductVersion' or $col=='idTargetProductVersion' or $col=='idRequirement')) {
            $critFld='idProduct';
            $critVal=$obj->idProduct;
          } else if (isset($obj->idComponent) and ($col=='idComponentVersion'
                                       or $col=='idOriginalComponentVersion' or $col=='idTargetComponentVersion' )) { 
            $critFld='idProduct';
            $critVal=$obj->idComponent;
          } else if (isset($obj->idProductOrComponent)) {
            $critFld='idProduct';
            $critVal=$obj->idProductOrComponent;
          }
        }
        if (substr($col,-16)=='ComponentVersion') {
          $prodVers=str_replace('Component','Product',$col);
          if (property_exists($obj, $prodVers) and $obj->$prodVers) {
            $critFld='idProductVersion';
            $critVal=$obj->$prodVers;
          }
          if (property_exists($obj, 'idComponent') and $obj->idComponent) {
            $critFld=array($critFld,'idComponent');
            $critVal=array($critVal,$obj->idComponent);
          }
        }
        if (get_class($obj) == 'IndicatorDefinition') {
          if ($col == 'idIndicator') {
            $critFld='idIndicatorable';
            $critVal=$obj->idIndicatorable;
          }
          if ($col == 'idType') {
            $critFld='scope';
            $critVal=SqlList::getNameFromId('Indicatorable', $obj->idIndicatorable);
          }
          if ($col == 'idWarningDelayUnit' or $col == 'idAlertDelayUnit') {
            $critFld='idIndicator';
            $critVal=$obj->idIndicator;
          }
        }
        if (get_class($obj) == 'PredefinedNote') {
          if ($col == 'idType') {
            $critFld='scope';
            $critVal=SqlList::getNameFromId('Textable', $obj->idTextable, false);
          }
        }
        if (get_class($obj) == 'StatusMail') {
          if ($col == 'idType') {
            $critFld='scope';
            $critVal=SqlList::getNameFromId('Mailable', $obj->idMailable, false);
          }
        }
        if (get_class($obj) == 'ChecklistDefinition' || get_class($obj) == 'JoblistDefinition') { // Can be replaced by a specific table Joblistable if needed
          if ($col == 'idType') {
            $critFld='scope';
            $critVal=SqlList::getNameFromId('Checklistable', $obj->idChecklistable, false);
          }
        }
        if (SqlElement::is_a($obj,'PlanningElement')) {
          $planningModeName='id'.$obj->refType.'PlanningMode';    
          if ($col==$planningModeName and !$obj->id and $objType) {      
            if (property_exists($objType,$planningModeName)) {
              $obj->$planningModeName=$objType->$planningModeName;
              $val=$obj->$planningModeName;
            }
          }
        }
        if ($displayComboButtonCol) {
          $fieldWidth-=50;
        } else if ($displayDirectAccessButton) {
          $fieldWidth-=30;
        }
        if ($nobr_before or strpos($obj->getFieldAttributes($col), 'size1/3') !== false) {
          $fieldWidth=$fieldWidth / 3 - 3;
        }
        $hasOtherVersion=false;
        $versionType='';
        $otherVersion='';
        if ( (substr($col, 7) == 'Version' and SqlElement::is_a(substr($col,2), 'Version') )
            or ($col == 'idOriginalVersion' or $col == 'idOriginalProductVersion' or $col == 'idOriginalComponentVersion' ) 
            or ($col == 'idTargetVersion' or $col == 'idTargetProductVersion' or $col == 'idTargetComponentVersion' )  ) {
          $versionType=substr($col, 2);
          $otherVersion='_Other' . $versionType;
          if (isset($obj->$otherVersion) and !$obj->isAttributeSetToField($col, 'hidden') and !$obj->isAttributeSetToField($col, 'readonly') and $canUpdate and !$obj->idle) {
            $hasOtherVersion=true;
            $fieldWidth-=28;
          }
        }
        $showExtraButton=false;
        if ($col == 'idStatus' or $col == 'idResource') {
          if ( (($col == 'idStatus') or ($col == 'idResource' and $user->isResource and $user->id != $val and $obj->id and $classObj != 'Affectation'))
            and $classObj!='Document' and $classObj!='StatusMail'  and $classObj!="TicketSimple" and $canUpdate) {
            $showExtraButton=true;
            $fieldWidth=round($fieldWidth / 2) - 5;
          }
        }
        echo '<select dojoType="dijit.form.FilteringSelect" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" xlabelType="html" ';
        echo '  style="width: ' . ($fieldWidth) . 'px;' . $specificStyle . '"';
        echo $name;
        echo $attributes;
        echo $valStore;
        echo autoOpenFilteringSelect();
        echo ' >';
        if ($classObj=='IndividualExpense' and $col=='idResource' and securityGetAccessRight('menuIndividualExpense', 'read', $obj, $user )=='OWN') {
          $next=htmlDrawOptionForReference($col, $val, $obj, $isRequired, 'id', $user->id);
        } else {
          $next=htmlDrawOptionForReference($col, $val, $obj, $isRequired, $critFld, $critVal);
        }
        echo $colScript;
        echo '</select>';
        if ($displayDirectAccessButton or $displayComboButtonCol) {
          echo '<div id="' . $col . 'ButtonGoto" ';
          echo ' title="' . i18n('showDirectAccess') . '" style="float:right;margin-right:3px;'.$specificStyle.'"';
          echo ' class="roundedButton  generalColClass '.$col.'Class">';
          echo '<div class="iconGoto" ';
          $jsFunction="var sel=dijit.byId('$fieldId');" . "if (sel && trim(sel.get('value'))) {" . " gotoElement('" . $comboClass . "','$val');" . "} else {" . " showAlert(i18n('cannotGoto'));" . "}";
          echo ' onclick="' . $jsFunction . '"';
          echo '></div>';
          echo '</div>';
        }
        if ($displayComboButtonCol) {
          echo '<div id="' . $col . 'ButtonDetail" ';
          echo ' title="' . i18n('showDetail') . '" style="float:right;margin-right:3px;'.$specificStyle.'"';
          echo ' class="roundedButton generalColClass '.$col.'Class">';
          echo '<div class="iconView" ';
          echo ' onclick="showDetail(\'' . $col . '\',' . (($canCreateCol)?1:0) . ',\''.$comboClass.'\')"';
          echo '></div>';
          echo '</div>';
        }
        if ($hasOtherVersion) {
          if ($obj->id and $canUpdate) {
            echo '<a class="generalColClass '.$col.'Class" style="float:right;margin-right:5px;'.$specificStyle.'" ';
            echo ' onClick="addOtherVersion(' . "'" . $versionType . "'" . ');" ';
            echo ' title="' . i18n('otherVersionAdd') . '">';
            echo formatSmallButton('Add');
            echo '</a>';
          }
          if (count($obj->$otherVersion) > 0) {
            drawOtherVersionFromObject($obj->$otherVersion, $obj, $versionType);
          }
        }
        if ($col == 'idStatus' and $next and $showExtraButton) {
          echo '<div class="roundedVisibleButton roundedButton generalColClass '.$col.'Class"';
          echo ' title="' . i18n("moveStatusTo", array(SqlList::getNameFromId('Status', $next))) . '"';
          echo ' style="text-align:left;float:right;margin-right:10px; width:' . ($fieldWidth - 5) . 'px;'.$specificStyle.'"';
          $saveFunction=($comboDetail)?'top.saveDetailItem();':'saveObject()';
          echo ' onClick="dijit.byId(\'' . $fieldId . '\').set(\'value\',' . $next . ');setTimeout(\''.$saveFunction.'\',100);">';
          echo '<img src="css/images/iconMoveTo.png" style="position:relative;left:5px;top:2px;"/>';
          echo '<div style="position:relative;top:-16px;left:25px;width:' . ($fieldWidth - 30) . 'px">' . SqlList::getNameFromId('Status', $next) . '<div>';
          echo '</div>';
        }
        if ($col == 'idResource' and $next and $showExtraButton) {
          echo '<div class="roundedVisibleButton roundedButton generalColClass '.$col.'Class"';
          echo ' title="' . i18n("assignToMe") . '"';
          echo ' style="text-align:left;float:right;margin-right:10px; width:' . ($fieldWidth - 5) . 'px;'.$specificStyle.'"';
          $saveFunction=($comboDetail)?'top.saveDetailItem();':'saveObject()';
          echo ' onClick="dijit.byId(\'' . $fieldId . '\').set(\'value\',' . htmlEncode($user->id) . ');setTimeout(\''.$saveFunction.'\',100);"';
          echo '>';
          echo '<img src="css/images/iconMoveTo.png" style="position:relative;left:5px;top:2px;"/>';
          echo '<div style="position:relative;top:-16px;left:25px;width:' . ($fieldWidth - 30) . 'px">' . i18n('assignToMeShort') . '<div>';
          echo '</div>';
        }
      } else if (strpos($obj->getFieldAttributes($col), 'display') !== false) {
        echo '<div ';
        echo ' class="display generalColClass '.$col.'Class" style="'.$specificStyle.'"';
        echo ' >';
        if (strpos($obj->getFieldAttributes($col), 'html') !== false) {
          echo $val;
        } else {
          echo htmlEncode($val);
        }
        if (!$print) {
          echo '<input type="hidden" ' . $name . ' value="' . htmlEncode($val) . '" />';
        }
        if (strtolower(substr($col, -8, 8)) == 'progress' or substr($col, -3, 3) == 'Pct') {
          echo '&nbsp;%';
        }
        echo '</div>';
      } else if ($dataType == 'int' or $dataType == 'decimal' ) {
        // Draw a number field ================================================ NUMBER
        $colScript=NumberFormatter52::completeKeyDownEvent($colScript);
        $isCost=false;
        $isWork=false;
        $isDuration=false;
        $isPercent=false;
        if ($dataType == 'decimal' and (substr($col, -4, 4) == 'Cost' or substr($col, -6, 6) == 'Amount' or $col == 'amount')) {
          $isCost=true;
          $fieldWidth=$smallWidth;
        }
        if ($dataType == 'decimal' and (substr($col, -4, 4) == 'Work')) {
          $isWork=true;
          $fieldWidth=$smallWidth;
        }
        if ($dataType == 'int' and (substr($col, -8, 8) == 'Duration')) {
          $isDuration=true;
          $fieldWidth=$smallWidth;
        }
        if (($isCost or $isWork or $isDuration) and $internalTable != 0 and $displayWidth < 1600) {
          $fieldWidth-=12;
        }
        if (strtolower(substr($col, -8, 8)) == 'progress' or substr($col, -3, 3) == 'Pct') {
          $isPercent=true; 
        }
        $spl=explode(',', $dataLength);
        $dec=0;
        if (count($spl) > 1) {
          $dec=$spl [1];
        }
        $ent=$spl [0] - $dec;
        $max=substr('99999999999999999999', 0, $ent);
        if ($isCost and $currencyPosition == 'before') {
          echo '<span class="generalColClass '.$col.'Class" style="'.$specificStyle.'">'.$currency.'</span>';
        }
        $negative=(($isCost or $isWork) and $val<0)?'background-color: #FFAAAA !important;':''; 
        if ($col=='workElementEstimatedWork' and property_exists($obj, 'assignedWork')) {
          $negative=($obj->workElementEstimatedWork>$obj->assignedWork)?'background-color: #FFAAAA !important;':'';
        }
        if ($col=='workElementLeftWork' and property_exists($obj, 'leftWork')) {
          $negative=($obj->workElementLeftWork>$obj->leftWork)?'background-color: #FFAAAA !important;':'';
        }
        echo '<div dojoType="dijit.form.NumberTextBox" ';
        echo $name;
        echo $attributes;
        // echo ' style="text-align:right; width: ' . $fieldWidth . 'px;' . $specificStyle . '" ';
        echo ' style="'.$negative.'width: ' . $fieldWidth . 'px;' . $specificStyle . '" ';
        if ($max) {
          echo ' constraints="{min:-' . $max . ',max:' . $max . '}" ';
        }
        echo ' class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        // echo ' layoutAlign ="right" ';
        if ($isWork) {
          if ($classObj=='WorkElement') {
            $dispVal=Work::displayImputation($val);
          } else {
            $dispVal=Work::displayWork($val);
          }
        } else if ($dataLength>4000) {
          $dispVal=htmlEncode($val,'formatted');  
        } else {
          $dispVal=htmlEncode($val);
        }
        echo ' value="' . $dispVal . '" ';
        // echo ' value="' . htmlEncode($val) . '" ';
        echo ' >';
        echo $colScript;
        echo '</div>';
        if ($isCost and $currencyPosition == 'after') {
          echo '<span class="generalColClass '.$col.'Class" style="'.$specificStyle.'">'.$currency.'</span>';
        }
        if ($isWork or $isDuration or $isPercent) {
          echo '<span class="generalColClass '.$col.'Class" style="'.$specificStyle.'">';
        }
        if ($isWork) {
          if ($classObj=='WorkElement') {
            echo Work::displayShortImputationUnit();
          } else {
            echo Work::displayShortWorkUnit();
          }
        }
        if ($isDuration) {
          echo i18n("shortDay");
        }
        if ($isPercent) {
          echo '%';
        }
        if ($isWork or $isDuration or $isPercent) {
          echo '</span>';
        }
      } else if ($dataLength > 100 and ($dataLength <= 4000 or getEditorType()=='text')) {
        // Draw a long text (as a textarea) =================================== TEXTAREA
        echo '<textarea dojoType="dijit.form.Textarea" ';
        echo ' onKeyPress="if (dojo.isFF || isEditingKey(event)) {formChanged();}" '; // hard coding default event
        echo $name;
        echo $attributes;
        if (strpos($attributes, 'readonly') > 0) {
          $specificStyle.=' color:#606060 !important; background:none; background-color: #F0F0F0; ';
        }
        echo ' rows="2" style="max-height:150px;width: ' . $largeWidth . 'px;' . $specificStyle . '" ';
        echo ' maxlength="' . $dataLength . '" ';
        // echo ' maxSize="4" ';
        echo ' class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" >';
        $text=new Html2Text($val);
        $val=$text->getText();
        echo htmlEncode($val);
        // echo $colScript; // => this leads to the display of script in textarea
        echo '</textarea>';
      } else if ($dataLength > 4000) {
        // Draw a long text (as a textarea) =================================== TEXTAREA
        // No real need to hide and apply class : long fields will be hidden while hiding row
        //class="generalColClass '.$col.'Class" style="'.$specificStyle.'"
        if (getEditorType()=="CK") {
          //if (isIE() and ! $val) $val='<div></div>';
          echo '<div style="text-align:left;font-weight:normal; width:300px;" class="tabLabel">' . htmlEncode($obj->getColCaption($col),'stipAllTags') . '</div>';
          $ckEditorNumber++;
          echo '<textarea style="height:300px"'; // Important to set big height to retreive correct scroll position after save
          echo ' name="'.$col.$extName.'" ';
          echo ' id="'.$col.$extName.'" ';
          echo ' class="input '.(($isRequired)?'required':'').'" ';
          //echo $name.' '.$attributes;
          echo ' maxlength="' . $dataLength . '"';
          echo '>';
          echo htmlspecialchars($val);
          echo '</textarea>';
          //echo  str_replace( "\n", '<br/>', $val );
          echo '<input type="hidden" id="ckeditor'.$ckEditorNumber.'" value="'. $col . $extName.'" />';
          if ($readOnly) {
            echo '<input type="hidden" id="ckeditor'.$ckEditorNumber.'ReadOnly" value="true" />';
          }
        } else {
          $val=str_replace("\n","",$val);
          echo '<textarea style="display:none; visibility:hidden;" ';
          echo ' maxlength="' . $dataLength . '" ';
          echo $name;
          echo $attributes;
          echo '>';
          echo htmlspecialchars($val);
          echo '</textarea>';
          if (isIE() and ! $val) $val='<div></div>'; 
          echo '<div style="text-align:left;font-weight:normal; width:300px;" class="tabLabel">' . htmlEncode($obj->getColCaption($col),'stipAllTags') . '</div>';
          if (getEditorType()=="Dojo") {
            echo '<div data-dojo-type="dijit.Editor"'; // TEST
            echo ' id="' . $fieldId . 'Editor" ';
            echo ' title="' . i18n('clickToEditRichText') . '"';
            if ($readOnly) echo ' disabled=true';
            echo ' data-dojo-props="height:\'200px\'';
            if ($readOnly) echo ', disabled:true';
            echo ',onChange:function(){dojo.byId(\'' . $fieldId . '\').value=arguments[0];formChanged();}';
            echo ",plugins:['bold','italic','underline','removeFormat'";
            echo ",'|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'";
            echo ",'|','insertOrderedList','insertUnorderedList','|']";
            echo ',onKeyDown:function(event){onKeyDownFunction(event,\'' . $fieldId . '\',this);}'; // hard coding default event
            //echo ',onBlur:function(event){top.editorBlur(\'' . $fieldId . '\',this)}'; // hard coding default event
            echo ",extraPlugins:['dijit._editor.plugins.AlwaysShowToolbar','foreColor','hiliteColor'";
            // Full screen mode disabled : sets many issues on some keys : tab, esc or ctrl+S, ...
            echo ",'|','print'";
            echo ",'fullScreen'";
            // Font Choice ...
            if (0) echo ",'fontName','fontSize'";
            // Print option
            
            // echo ",{name: 'LocalImage', uploadable: true, uploadUrl: '../../form/tests/UploadFile.php', baseImageUrl: '../../form/tests/', fileMask: '*.jpg;*.jpeg;*.gif;*.png;*.bmp'}";
            echo "]";
            echo '" ';
            echo $attributes;
            if (strpos($attributes, 'readonly') > 0) {
              $specificStyle.=' color:#606060 !important; background:none; background-color: #F0F0F0; ';
            }
            echo ' rows="2" style="min-height:16px;width: ' . ($largeWidth + 145) . 'px;' . $specificStyle . '" ';
            echo ' maxlength="' . $dataLength . '" ';
            echo ' class="input '.(($isRequired)?'required':'').'" ';
            //echo ' style="background: none; background-color: #AAAAFF" ';
            echo '>';        
          } else { //  getEditorType()=="DojoInline"
            echo '<div data-dojo-type="dijit.InlineEditBox"'; // TEST
            // echo '<div data-dojo-type="dijit.Editor"'; // TEST
            echo ' id="' . $fieldId . 'Editor" ';
            echo ' height="50px" title="' . i18n('clickToEditRichText') . '"';
            echo ' data-dojo-props="editor:\'dijit/Editor\',renderAsHtml:true';
            if ($readOnly) echo ', disabled:true';
            echo ',onChange:function(){dojo.byId(\'' . $fieldId . '\').value=arguments[0];formChanged();}';
            echo ",editorParams:{height:'200px',plugins:['bold','italic','underline','removeFormat'";
            echo ",'|', 'indent', 'outdent', 'justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'";
            echo ",'|','insertOrderedList','insertUnorderedList','|']";
            echo ',onKeyDown:function(event){onKeyDownFunction(event,\'' . $fieldId . '\',this);}'; // hard coding default event
            echo ',onBlur:function(event){editorBlur(\'' . $fieldId . '\',this)}'; // hard coding default event
            echo ",extraPlugins:['dijit._editor.plugins.AlwaysShowToolbar','foreColor','hiliteColor'";
            echo ",'|','print'";
            echo ",'fullScreen'";
            // Font Choice ...
            if (0) echo ",'fontName','fontSize'";
            // echo ",{name: 'LocalImage', uploadable: true, uploadUrl: '../../form/tests/UploadFile.php', baseImageUrl: '../../form/tests/', fileMask: '*.jpg;*.jpeg;*.gif;*.png;*.bmp'}";
            echo "]}";
            echo '" ';
            echo $attributes;
            if (strpos($attributes, 'readonly') > 0) {
              $specificStyle.=' color:#606060 !important; background:none; background-color: #F0F0F0; ';
            }
            echo ' rows="2" style="padding:3px 0px 3px 3px;margin-right:2px;max-height:150px;min-height:16px;overflow:auto;width: ' . ($largeWidth + 145) . 'px;' . $specificStyle . '" ';
            echo ' maxlength="' . $dataLength . '" ';
            echo ' class="input '.(($isRequired)?'required':'').'" ';
            echo ' style="background: none; background-color: #AAAAFF" ';
            echo '>';
          }
          //echo '  <script type="dojo/connect" event="onKeyPress" args="evt">';
          //echo '   alert("OK");';
          //echo '  </script>';
          echo  str_replace( "\n", '<br/>', $val );
          //echo $val;
          echo '</div>';
        }
      } else if ($col == 'icon') {
        echo '<div dojoType="dijit.form.Select" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        echo '  style="width: ' . ($fieldWidth) . 'px;' . $specificStyle . '"';
        echo $name;
        echo $attributes;
        echo ' >';
        // htmlDrawOptionForReference($col, $val, $obj, $isRequired,$critFld, $critVal);
        echo '<span value=""> </span>';
        if ($handle=opendir(getcwd() . '/icons')) {
          while ( false !== ($entry=readdir($handle)) ) {
            if ($entry != "." && $entry != "..") {
              $ext=strtolower(pathinfo($entry, PATHINFO_EXTENSION));
              if ($ext == "png" or $ext == "gif" or $ext == "jpg" or $ext == "jpeg") {
                echo '<span value="' . $entry . '" ' . (($entry == $val)?'selected="selected"':'') . '><img src="../view/icons/' . $entry . '" /></span>';
              }
            }
          }
          closedir($handle);
        }
        echo $colScript;
        echo '</div>';
      } else {
        // Draw defaut data (text medium size) ================================ TEXT (default)
        if ($obj->isFieldTranslatable($col)) {
          $fieldWidth=$fieldWidth / 2;
        }
        echo '<div type="text" dojoType="dijit.form.ValidationTextBox" ';
        echo $name;
        echo $attributes;
        echo '  style="width: ' . $fieldWidth . 'px;' . $specificStyle . ';" ';
        echo ' trim="true" maxlength="' . $dataLength . '" class="input '.(($isRequired)?'required':'').' generalColClass '.$col.'Class" ';
        echo ' value="' . htmlEncode($val) . '" ';
        if ($obj->isFieldTranslatable($col)) {
          echo ' title="' . i18n("msgTranslatable") . '" ';
        }
        echo ' >';
        echo $colScript;
        echo '</div>';
        if ($obj->isFieldTranslatable($col)) {
          echo '<div dojoType="dijit.form.TextBox" type="text"  ';
          echo ' class="display" ';
          echo ' readonly tabindex="-1" style="width: ' . $fieldWidth . 'px;" ';
          echo ' title="' . i18n("msgTranslation") . '" ';
          echo ' value="' . htmlEncode(i18n($val)) . '" ></div>';
        }
      }
      if ($internalTable > 0) {
        $internalTable--;
        if ($internalTable == 0) {
          echo '</td></tr></table><table  style="width: 100%;">';
        }
      } else {
        if ($internalTable == 0 and !$hide and !$nobr) {
          echo '</td></tr>' . $cr;
        }
      }
    }
  }
  if (!$included) {
    if ($currentCol == 0) {
      if ($section and !$print) {
        echo '</div>';
      }
      echo '</table>';
    } else {
      echo '</table>';
      if ($section and !$print) {
        echo '</div>';
      }
      // echo '</td></tr></table>';
    }
  }
  endBuffering();
  finalizeBuffering();
  if ($outMode == 'pdf') {
    $cpt=0;
    foreach ( $obj as $col => $val ) {
      if (substr($col, 0, 1) == '_' and substr($col, -5) == '_full') {
        $cpt++;
        $section=substr($col, 1, strlen($col) - 6);
        // echo '</page><page>';
        if ($cpt == 1)
          echo '<page><br/>';
        echo '<table style="width:' . $printWidth . 'px;"><tr><td class="section">' . $obj->getColCaption($section) . '</td></tr></table>';
        echo htmlEncode($val, 'print');
        echo '<br/><br/>';
      }
    }
    if ($cpt > 0)
      echo '</page>';
  }
}

function startTitlePane($classObj, $section, $collapsedList, $widthPct, $print, $outMode, $prevSection, $nbCol, $nbBadge=null) {
  scriptLog("startTitlePane(classObbj=$classObj, section=$section, collapsedList=array, widthPct=$widthPct, print=$print, outMode=$outMode, prevSection=$prevSection, nbCol=$nbCol, nbBadge=$nbBadge)");
  global $currentColumn, $reorg, $leftPane, $rightPane, $extraPane, $bottomPane;
  if (!$currentColumn) $currentColumn=0;
  // echo '<tr><td colspan="2" style="width: 100%" class="halfLine">&nbsp;</td></tr>';
  
  //if ($prevSection and !$print) {
  if ($prevSection) { 
    echo '</table>';
    if (!$print) {
      echo '</div>';
    } else {
      echo '<br/>';
    }
  }
  endBuffering();
  if (!$print) {
    
    $arrayPosition=array(
         'treatment'=>     array('clear'=>(($nbCol==2)?'right':'none')),
         'progress'=>      array('float'=>(($nbCol==2)?'right':'left'), 'clear'=>(($nbCol==2)?'right':'none')),
         'predecessor'=>   array('clear'=>(($nbCol==2)?'both':(($classObj=='Activity')?'left':'none'))),
         'successor'=>     array('float'=>(($nbCol==2 or $classObj!='Project')?'left':'right'),  'clear'=>'none'),
        
         //'subprojects'=>   array('float'=>(($nbCol==2)?'left':'left'),   'clear'=>(($nbCol==2)?'left':'none')),
         'versionproject_versions'=>array('float'=>(($nbCol==2)?'right':'left'),  'clear'=>'right'),
         
         'version'=>       array('float'=>(($nbCol==2)?'right':'left'),  'clear'=>'none'),
         'approver'=>      array('float'=>'right','clear'=>'right'),
         'lock'=>          array('float'=>(($nbCol==2)?'left':'right'),  'clear'=>(($nbCol==2)?'none':'right')),
        
         'assignment'=>    array('float'=>'left','clear'=>(($nbCol==2)?'left':'none')),
         'attendees'=>     array('float'=>'left','clear'=>(($nbCol==2)?'left':'none')),
        
        //'periodicity'=>    array('float'=>'left','clear'=>'left'),
        
        'internalalert'=>  array('float'=>'right'),
        
        'expensedetail'=>      array('clear'=>'left'),
         'billline'=>      array('clear'=>'left'),
         'link'=>          array('clear'=>(($nbCol==3)?'left':'left')),
         'attachment'=>    array('float'=>'left',  'clear'=>'none'),
         'note'=>          array('float'=>'right',  'clear'=>'none')
        
    );
    $float='left';
    $clear='none';
    $lc=strtolower($section);
    if (isset($arrayPosition[$lc]['float'])) $float=$arrayPosition[$lc]['float'];
    if (isset($arrayPosition[$lc]['clear'])) $clear=$arrayPosition[$lc]['clear'];
    if ($nbCol==3 and ($lc=='note' or $lc=='link' or $lc=='attachment')) {
      $float='right';
      $clear='right';
    }
    $titlePane=$classObj . "_" . $section;
    startBuffering();
    echo '<div dojoType="dijit.TitlePane" title="' . i18n('section' . ucfirst($section)) . (($nbBadge!==null)?'<div id=\''.$section.'Badge\' class=\'sectionBadge\'>'.$nbBadge.'</div>':'').'"';
    echo ' open="' . (array_key_exists($titlePane, $collapsedList)?'false':'true') . '" ';
    echo ' id="' . $titlePane . '" ';
    echo ' style="display:inline-block;position:relative;width:' . $widthPct . ';float: '.$float.';clear:'.$clear.';margin: 0 0 4px 4px; padding: 0;top:0px;"';
    echo ' onHide="saveCollapsed(\'' . $titlePane . '\');"';
    echo ' onShow="saveExpanded(\'' . $titlePane . '\');">';
    echo '<table class="detail"  style="width: 100%;" >';
  } else {
    echo '<table class="detail" style="width:' . $widthPct . ';" >';
    echo '<tr><td class="section">' . i18n('section' . ucfirst($section)) . '</td></tr>';
    echo '<tr class="detail" style="height:2px;font-size:2px;">';
    echo '<td class="detail" >&nbsp;</td>';
    echo '</tr>';
    echo '</table><table class="detail" style="width:' . $widthPct . ';" >'; // For PDF
    //echo '</table><table class="detail" style="width:' . $widthPct . ';" >'; // For PDF
  }
}

function drawDocumentVersionFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->locked) {
    $canUpdate=false;
  }
  // if ($obj->idle==1) {$canUpdate=false;}
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  $typeEvo="EVO";
  $type=new VersioningType($obj->idVersioningType);
  $typeEvo=$type->code;
  $num="";
  $vers=new DocumentVersion($obj->idDocumentVersion);
  if ($typeEvo == 'SEQ') {
    $num=intVal($vers->name) + 1;
  }
  echo '<tr>';
  if (!$print) {
    $statusTable=SqlList::getList('Status', 'name', null);
    reset($statusTable);
    echo '<td class="assignHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle) {
      echo '<a onClick="addDocumentVersion(' . "'" . key($statusTable) . "'" . ",'" . $typeEvo . "'" . ",'" . $num . "'" . ",'" . htmlEncode($vers->name) . "'" . ",'" . htmlEncode($vers->name) . "'" . ');" ';
      echo ' title="' . i18n('addDocumentVersion') . '" > ';
      echo formatSmallButton('Add');
      echo '</a>';
    }
    echo '</td>';
  }
  echo '<td class="assignHeader" style="width:15%" >' . i18n('colIdVersion') . '</td>';
  echo '<td class="assignHeader" style="width:15%" >' . i18n('colDate') . '</td>';
  echo '<td class="assignHeader" style="width:15%">' . i18n('colIdStatus') . '</td>';
  echo '<td class="assignHeader" style="width:' . (($print)?'55':'45') . '%">' . i18n('colFile') . '</td>';
  echo '</tr>';
  $preserveFileName=Parameter::getGlobalParameter('preserveUploadedFileName');
  if (!$preserveFileName) {
    $preserveFileName="NO";
  }
  foreach ( $list as $version ) {
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignData" style="text-align:center; white-space: nowrap;vertical-align:top;">';
      if (!$print) {
        echo '<a href="../tool/download.php?class=DocumentVersion&id=' . htmlEncode($version->id) . '"';
        echo ' target="printFrame" title="' . i18n('helpDownload') . "\n" . (($preserveFileName == 'YES')?$version->fileName:$version->fullName) . '">'
             .formatSmallButton('Download') 
             .'</a>';
      }
      if ($canUpdate and !$print and (!$obj->idle or $obj->idDocumentVersion == $version->id)) {
        echo '  <a onClick="editDocumentVersion(' . "'" . htmlEncode($version->id) . "'" . ",'" . htmlEncode($version->version) . "'" . ",'" . htmlEncode($version->revision) . "'" . ",'" . htmlEncode($version->draft) . "'" . ",'" . htmlEncode($version->versionDate) . "'" . ",'" . htmlEncode($version->idStatus) . "'" . ",'" .
             $version->isRef . "'" . ",'" . $typeEvo . "'" . ",'" . htmlEncode($version->name) . "'" . ",'" . htmlEncode($version->name) . "'" . ",'" . htmlEncode($version->name) . "'" . ');" ' . 'title="' . i18n('editDocumentVersion') . '" >'
                .formatSmallButton('Edit') 
                .'</a> ';
      }
      if ($canUpdate and !$print and !$obj->idle) {
        echo '  <a onClick="removeDocumentVersion(' . "'" . htmlEncode($version->id) . "'" . ', \'' . htmlEncode($version->name) . '\');" ' . 'title="' . i18n('removeDocumentVersion') . '" >'
                .formatSmallButton('Remove') 
                .'</a> ';
      }
      echo '<input type="hidden" id="documentVersion_' . htmlEncode($version->id) . '" name="documentVersion_' . htmlEncode($version->id) . '" value="' . htmlEncode($version->description) . '"/>';
      echo '</td>';
    }
    echo '<td class="assignData">' . (($version->isRef)?'<b>':'') . htmlEncode($version->name) . (($version->isRef)?'</b>':'');
    if ($version->approved) {
      echo '&nbsp;&nbsp;<img src="../view/img/check.png" height="12px" title="' . i18n('approved') . '"/>';
    }
    echo '</td>';
    echo '<td class="assignData">' . htmlFormatDate($version->versionDate) . '</td>';
    $objStatus=new Status($version->idStatus);
    echo '<td class="assignData" style="width:15%">' . colorNameFormatter($objStatus->name . "#split#" . $objStatus->color) . '</td>';
    echo '<td class="assignData" title="' . htmlencode($version->description) . '">';
    echo '<table style="width:100%"><tr><td style="width:20px">';
    if ($version->isThumbable()) {
      $ext = pathinfo($version->fileName, PATHINFO_EXTENSION);
      if (file_exists("../view/img/mime/$ext.png")) {
        $img="../view/img/mime/$ext.png";
      } else {
        $img= "../view/img/mime/unknown.png";
      }
      echo '<img src="' . $img . '" ' . ' title="' . htmlEncode($version->fileName) . '" style="float:left;cursor:pointer"' . ' onClick="showImage(\'DocumentVersion\',\'' . htmlEncode($version->id) . '\',\'' . htmlEncode($version->fileName,'protectQuotes') . '\');" />';
    } else { 
      echo htmlGetMimeType($version->mimeType, $version->fileName , $version->id,'DocumentVersion');
    }
    echo '</td><td>';
    echo htmlEncode($version->fileName, 'print');
    if ($version->description and !$print) {
      echo formatCommentThumb($version->description);
    }
    echo '</td></tr></table>';
    echo '</td></tr>';
  }
  echo '</table></td></tr>';
}

function drawOrigin($refType, $refId, $obj, $col, $print) {
  echo '<tr class="detail"><td class="label" xstyle="width:10%;">';
  echo '<label for="' . $col . '" >' . htmlEncode($obj->getColCaption($col),'stipAllTags') . '&nbsp;:&nbsp;</label>';
  echo '</td>';
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if ($print) {
    echo '<td style="width: 120px">';
  } else {
    echo '<td>';
  }
  if ($refType and $refId) {
    echo '<table width="100%"><tr height="20px"><td xclass="noteData" width="1%" xvalign="top">';
    if (!$print and $canUpdate) {
      echo '<a onClick="removeOrigin(\'' . $obj->$col->id . '\',\'' . $refType . '\',\'' . $refId . '\');" title="' . i18n('removeOrigin') . '" > '.formatSmallButton('Remove').'</a>';
    }
    echo '</td><td width="5%" xclass="noteData" xvalign="top" style="white-space:nowrap">';
    echo '&nbsp;&nbsp;' . i18n($refType) . '&nbsp;#' . $refId . '&nbsp;:&nbsp;';
    echo '</td><td xclass="noteData" style="height: 15px">';
    $orig=new $refType($refId);
    echo htmlEncode($orig->name);
    echo '</td></tr></table>';
  } else {
    echo '<table><tr height="20px"><td>';
    if ($obj->id and !$print and $canUpdate) {
      echo '<a onClick="addOrigin();" title="' . i18n('addOrigin') . '" class="roundedButtonSmall"> '.formatSmallButton('Add').'</a>';
    }
    echo '</td></tr></table>';
  }
}

function drawHistoryFromObjects($refresh=false) {
  global $cr, $print, $treatedObjects, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $inList="( ('x',0)"; // initialize with non existing element, to avoid error if 1 only object involved
  foreach ( $treatedObjects as $obj ) {
    // $inList.=($inList=='')?'(':', ';
    if ($obj->id) {
      $inList.=", ('" . get_class($obj) . "', " . Sql::fmtId($obj->id) . ")";
    }
  }
  $showWorkHistory=false;
  $paramDisplayHistory=Parameter::getUserParameter('displayHistory');
  if ( ($paramDisplayHistory=='REQ' and getSessionValue('showWorkHistory')) or $paramDisplayHistory=='YESW') {
    $showWorkHistory=true;
  }
  $inList.=')';
  $where=' (refType, refId) in ' . $inList;
  $order=' operationDate desc, id asc';
  $hist=new History();
  $historyList=$hist->getSqlElementsFromCriteria(null, false, $where, $order);
  echo '<table style="width:100%;">';
  echo '<tr>';
  echo '<td class="historyHeader" style="width:10%">' . i18n('colOperation') . '</td>';
  echo '<td class="historyHeader" style="width:14%">' . i18n('colColumn') . '</td>';
  echo '<td class="historyHeader" style="width:23%">' . i18n('colValueBefore') . '</td>';
  echo '<td class="historyHeader" style="width:23%">' . i18n('colValueAfter') . '</td>';
  echo '<td class="historyHeader" style="width:15%">' . i18n('colDate') . '</td>';
  echo '<td class="historyHeader" style="width:15%">' . i18n('colUser') . '</td>';
  echo '</tr>';
  $stockDate=null;
  $stockUser=null;
  $stockOper=null;
  foreach ( $historyList as $hist ) {
    if (substr($hist->colName, 0, 24) == 'subDirectory|Attachment|' or substr($hist->colName, 0, 18) == 'idTeam|Attachment|' 
     or substr($hist->colName, 0, 25) == 'subDirectory|Attachement|' or substr($hist->colName, 0, 19) == 'idTeam|Attachement|') {
      continue;
    }
    $colName=($hist->colName == null)?'':$hist->colName;
    $split=explode('|', $colName);
    if (count($split) == 3) {
      $colName=$split [0];
      $refType=$split [1];
      $refId=$split [2];
      $refObject='';
    } else if (count($split) == 4) {
      $refObject=$split [0];
      $colName=$split [1];
      $refType=$split [2];
      $refId=$split [3];
    } else {
      $refType='';
      $refId='';
      $refObject='';
    }  
    if ($refType=='Attachement') {
      $refType='Attachment'; // New in V5 : change Class name, must preserve display for history
    }
    $curObj=null;
    $dataType="";
    $dataLength=0;
    $hide=false;
    $oper=i18n('operation' . ucfirst($hist->operation));
    $user=$hist->idUser;
    $user=SqlList::getNameFromId('User', $user);
    $date=htmlFormatDateTime($hist->operationDate);
    $class="NewOperation";
    if ($stockDate == $hist->operationDate and $stockUser == $hist->idUser and $stockOper == $hist->operation) {
      $oper="";
      $user="";
      $date="";
      $class="ContinueOperation";
    }
    if ($colName != '' or $refType != "") {
      if ($refType) {
        if ($refType == "TestCase") {
          $curObj=new TestCaseRun();
        } else {
          $curObj=new $refType();
        }
      } else {
        $curObj=new $hist->refType();
      }
      if ($curObj) {
        if ($refType) {
          $colCaption=i18n($refType) . ' #' . $refId . ' ' . $curObj->getColCaption($colName);
          if ($refObject) {
            $colCaption=i18n($refObject) . ' - ' . $colCaption;
          }
        } else {
          $colCaption=$curObj->getColCaption($colName);
        }
        $dataType=$curObj->getDataType($colName);
        $dataLength=$curObj->getDataLength($colName);
        if (strpos($curObj->getFieldAttributes($colName), 'hidden') !== false) {
          $hide=true;
        }
      }
    } else {
      $colCaption='';
    }
    if (substr($hist->refType, -15) == 'PlanningElement' and $hist->operation == 'insert') {
      $hide=true;
    }
    if ($hist->isWorkHistory and ! $showWorkHistory) {
      $hide=true;
    }
    if (!$hide) {
      echo '<tr>';
      echo '<td class="historyData' . $class . '" width="10%">' . $oper . '</td>';
      
      echo '<td class="historyData" width="14%">' . $colCaption . '</td>';
      $oldValue=$hist->oldValue;
      $newValue=$hist->newValue;
      if ($dataType == 'int' and $dataLength == 1) { // boolean
        $oldValue=htmlDisplayCheckbox($oldValue);
        $newValue=htmlDisplayCheckbox($newValue);
      } else if (substr($colName, 0, 2) == 'id' and strlen($colName) > 2 and strtoupper(substr($colName, 2, 1)) == substr($colName, 2, 1)) {
        if ($oldValue != null and $oldValue != '') {
          if ($oldValue == 0 and $colName == 'idStatus') {
            $oldValue='';
          } else {
            $oldValue=SqlList::getNameFromId(substr($colName, 2), $oldValue);
          }
        }
        if ($newValue != null and $newValue != '') {
          $newValue=SqlList::getNameFromId(substr($colName, 2), $newValue);
        }
      } else if ($colName == "color") {
        $oldValue=htmlDisplayColored("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $oldValue);
        $newValue=htmlDisplayColored("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $newValue);
      } else if ($dataType == 'date') {
        $oldValue=htmlFormatDate($oldValue);
        $newValue=htmlFormatDate($newValue);
      } else if ($dataType == 'datetime') {
        $oldValue=htmlFormatDateTime($oldValue);
        $newValue=htmlFormatDateTime($newValue);
      } else if ($dataType == 'decimal' and substr($colName, -4, 4) == 'Work') {
        $oldValue=Work::displayWork($oldValue) . ' ' . Work::displayShortWorkUnit();
        $newValue=Work::displayWork($newValue) . ' ' . Work::displayShortWorkUnit();
      } else if ($dataType == 'decimal' and (substr($colName, -4, 4) == 'Cost' or strtolower(substr($colName,-6,6))=='amount')) {
          $oldValue=htmlDisplayCurrency($oldValue);
          $newValue=htmlDisplayCurrency($newValue);
      } else if (substr($colName, -8, 8) == 'Duration') {
        $oldValue=$oldValue . ' ' . i18n('shortDay');
        $newValue=$newValue . ' ' . i18n('shortDay');
      } else if (substr($colName, -8, 8) == 'Progress') {
        $oldValue=$oldValue . ' ' . i18n('colPct');
        $newValue=$newValue . ' ' . i18n('colPct');
      } else if ($dataLength>4000 or $refType=='Note') {
        //$diff=diffValues($oldValue,$newValue);
        // Nothing, preserve html format 
        //$oldValue=$colName;
      } else if ($colName=='password' or $colName=='apiKey') {
        $allstars="**********";
        if ($oldValue) $oldValue=substr($oldValue,0,5).$allstars.substr($oldValue,-5);
        if ($newValue) $newValue=substr($newValue,0,5).$allstars.substr($newValue,-5);
      } else {
        //$diff=diffValues($oldValue,$newValue);
        $oldValue=htmlEncode($oldValue, 'print');
        $newValue=htmlEncode($newValue, 'print');
      }
      echo '<td class="historyData" width="23%">' . $oldValue . '</td>';
      echo '<td class="historyData" width="23%">' . $newValue . '</td>';
      echo '<td class="historyData' . $class . '" width="15%">';
      //echo formatDateThumb($creationDate, $updateDate);
      echo  $date . '</td>';
      echo '<td class="historyData' . $class . '" style="border-right: 1px solid #AAAAAA;" width="15%">';
      if ($user) {
        echo formatUserThumb($hist->idUser, $user, null,'16','left').'&nbsp;';
      }
      echo $user; 
      echo '</td>';
      echo '</tr>';
      $stockDate=$hist->operationDate;
      $stockUser=$hist->idUser;
      $stockOper=$hist->operation;
    }
  }
  echo '<tr>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '<td class="historyDataClosetable">&nbsp;</td>';
  echo '</tr>';
  echo '</table>';
}

function drawNotesFromObject($obj, $refresh=false) {
  global $cr, $print, $outMode, $user, $comboDetail, $displayWidth, $printWidth;
  $widthPct=setWidthPct($displayWidth, $print, $printWidth,$obj);
  $widthPctNote=((substr($widthPct,0,strlen($widthPct)-2)*0.85)-45).'px';
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (isset($obj->_Note)) {
    $notes=$obj->_Note;
  } else {
    $notes=array();
  }
  if (!$refresh and !$print) echo '<tr><td colspan="2">';
  echo '<input type="hidden" id="noteIdle" value="' . htmlEncode($obj->idle) . '" />';
  if (! $print) {
    echo '<table width="99.9%">';
  }  
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteHeader smallButtonsGroup" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addNote();" title="' . i18n('addNote') . '" >'.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="noteHeader" style="width:5%">' . i18n('colId') . '</td>';
  echo '<td class="noteHeader" style="width:' . (($print)?'95':'85') . '%">' . i18n('colNote') . '</td>';
  // echo '<td class="noteHeader" style="width:15%">' . i18n ( 'colDate' ) . '</td>';
  // echo '<td class="noteHeader" style="width:15%">' . i18n ( 'colUser' ) . '</td>';
  echo '</tr>';
  $nbNotes=0;
  $ress=new Resource($user->id);
  foreach ( $notes as $note ) {
    if ($user->id == $note->idUser or $note->idPrivacy == 1 or ($note->idPrivacy == 2 and $ress->idTeam == $note->idTeam)) {
      $nbNotes++;
      $userId=$note->idUser;
      $userName=SqlList::getNameFromId('User', $userId);
      $creationDate=$note->creationDate;
      $updateDate=$note->updateDate;
      if ($updateDate == null) {
        $updateDate='';
      }
      echo '<tr>';
      if (!$print) {
        echo '<td class="noteData smallButtonsGroup">';
        if ($note->idUser == $user->id and !$print and $canUpdate) {
          echo ' <a onClick="editNote(' . htmlEncode($note->id) . ',' . htmlEncode($note->idPrivacy) . ');" title="' . i18n('editNote') . '" > '.formatSmallButton('Edit').'</a>';
          echo ' <a onClick="removeNote(' . htmlEncode($note->id) . ');" title="' . i18n('removeNote') . '" > '.formatSmallButton('Remove').'</a>';
        }
        echo '</td>';
      }
      echo '<td class="noteData">#' . htmlEncode($note->id) . '</td>';
      echo '<td class="noteData">';
      if (!$print) {
        echo '<div style="display:none" type="hidden" id="note_' . htmlEncode($note->id) . '">';
        echo $note->note;
        echo '</div>';
      }
      echo formatUserThumb($userId, $userName, 'Creator');
      echo formatDateThumb($creationDate, $updateDate);
      echo formatPrivacyThumb($note->idPrivacy, $note->idTeam);
      // ADDED BRW
      //$strDataHTML=htmlEncode($note->note, ''); // context = '' => only htmlspecialchar, not htmlentities
      if (! $print) echo '<div style="max-width:'.$widthPctNote.';overflow-x:auto;" >';
      $strDataHTML=$note->note;
      //$strDataHTML=preg_replace('@(https?://([-\w\.]<+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@', '<a href="$1" target="_blank">$1</a>', $strDataHTML);
      //$strDataHTML=nl2br($strDataHTML); // then convert line breaks : must be after preg_replace of url
      if ($print and $outMode=='pdf') {
        $strDataHTML=str_replace(array('<div>','</div>'),array('<br/>',''), $strDataHTML);
        $strDataHTML=strip_tags($strDataHTML,'<br><br/><font><b>');
      }
      echo $strDataHTML;
      if (! $print) echo '</div>';
      // END ADDED BRW
      echo '</td>';
      /*
       * echo '<td class="noteData">' . htmlFormatDateTime ( $creationDate ) . '<br/>'; if ($note->fromEmail) { echo '<b>' . i18n ( 'noteFromEmail' ) . '</b>'; } echo '<i>' . htmlFormatDateTime ( $updateDate ) . '</i></td>'; echo '<td class="noteData">' . $userName . '</td>';
       */
      echo '</tr>';
    }
  }
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteDataClosetable">&nbsp;</td>';
  }
  echo '<td colspan="'.(($print)?'2':'3').'" class="noteDataClosetable">&nbsp;</td>';
  echo '</tr>';
  if (!$print) {
    echo '</table>';
  }
  if (!$refresh and !$print) echo '</td></tr>'; 
  if (!$print) {
    echo '<input id="NoteSectionCount" type="hidden" value="'.count($nbNotes++).'" />';
  }
}

function drawBillLinesFromObject($obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $widthPct;
  // $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj)=="YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  $lock=false;
  if ($obj->done or $obj->idle or (property_exists($obj, 'billingType') and $obj->billingType == "N") ) {
    $lock=true;
  }
  if (isset($obj->_BillLine)) {
    $lines=$obj->_BillLine;
  } else {
    $lines=array();
  }
  if (!$print) {
    echo '<input type="hidden" id="billLineIdle" value="' . htmlEncode($obj->idle) . '" />';
    echo '<table width="100%">'; 
  }
  echo '<tr>';
  $billingType='M';
  if (property_exists($obj,'billingType') and $obj->billingType) {
    $billingType=$obj->billingType;
  }
  if (!$print) {
    echo '<td class="noteHeader" style="width:5%">'; // changer le header
    if ($obj->id != null and !$print and !$lock) {
      echo '<a onClick="addBillLine(\'M\');" title="' . i18n('addLine') . '" > '.formatSmallButton('Add').'</a>';
      if ($billingType!='M') {
        echo '<a onClick="addBillLine(\''.$billingType.'\');" title="' . i18n('addLine') . '" style="cursor: pointer;display: inline-block;margin-left:5px;" class="roundedButtonSmall"> '.formatIcon('Bill',16).'</a>';
      }
    }
    echo '</td>';
  }
  echo '<td class="noteHeader" style="width:5%">' . i18n('colId') . '</td>';
  echo '<td class="noteHeader" style="width:5%">' . i18n('colLineNumber') . '</td>';
  echo '<td class="noteHeader" style="width:20%">' . i18n('colDescription') . '</td>';
  echo '<td class="noteHeader" style="width:30%">' . i18n('colDetail') . '</td>';
  echo '<td class="noteHeader" style="width:15%">' . i18n('colUnitPrice') . '</td>';
  echo '<td class="noteHeader" style="width:10%">' . i18n('colQuantity') . '</td>';
  echo '<td class="noteHeader" style="width:15%">' . strtolower(i18n('sum')) . '</td>';
  echo '</tr>';
  
  $fmt=new NumberFormatter52($browserLocale, NumberFormatter52::INTEGER);
  $fmtd=new NumberFormatter52($browserLocale, NumberFormatter52::DECIMAL);
  $lines=array_reverse($lines);
  foreach ( $lines as $line ) {
    $unit=new MeasureUnit($line->idMeasureUnit);
    echo '<tr>';
    if (!$print) {
      echo '<td class="noteData" style="text-align:center;white-space:nowrap">';
      if ($lock == 0) {
        echo ' <a onClick="editBillLine('.htmlEncode($line->id).',\''.htmlEncode(($line->billingType)?$line->billingType:$billingType).'\');" ';
        echo '  title="' . i18n('editLine') . '" > '.formatSmallButton('Edit').'</a>';
        echo ' <a onClick="removeBillLine(' . htmlEncode($line->id) . ');"' . ' ';
        echo '  title="' . i18n('removeLine') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="noteData" style="width:5%">#' . htmlEncode($line->id) . '</td>';
    echo '<td class="noteData" style="width:5%">' . htmlEncode($line->line) . '</td>';
    echo '<td class="noteData" style="width:20%">' . htmlEncode($line->description, 'withBR');
    if (!$print) {
      echo '<input type="hidden" id="billLineDescription_' . htmlEncode($line->id) . '" value="' . htmlEncode($line->description) . '" />';
    }
    echo '</td>';
    echo '<td class="noteData" style="width:30%">' . htmlEncode($line->detail, 'withBR');
    if (!$print) {
      echo '<input type="hidden" id="billLineDetail_' . htmlEncode($line->id) . '" value="' . htmlEncode($line->detail) . '" />';
    }
    echo '</td>';
    $unitPrice=($unit->name)?' / '.$unit->name:'';
    echo '<td class="noteData" style="width:15%">' . htmlDisplayCurrency($line->price) . $unitPrice.'</td>';
    $unitQuantity=($unit->name)?' '.(($line->quantity>1)?$unit->pluralName:$unit->name):'';
    echo '<td class="noteData" style="width:10%">' . htmlDisplayNumericWithoutTrailingZeros($line->quantity) . $unitQuantity. '</td>';
    echo '<td class="noteData" style="width:15%">' . htmlDisplayCurrency($line->amount) . '</td>';
    echo '</tr>';
  }
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteDataClosetable">&nbsp;</td>';
  }
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '</tr>';
  if (!$print) {
    echo '</table>';
  }
}

function drawChecklistDefinitionLinesFromObject($obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale;
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (isset($obj->_ChecklistDefinitionLine)) {
    $lines=$obj->_ChecklistDefinitionLine;
  } else {
    $lines=array();
  }
  echo '<input type="hidden" id="ChecklistDefinitionIdle" value="' . htmlEncode($obj->idle) . '" />';
  echo '<table width="100%">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteHeader" style="width:5%">'; // changer le header
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addChecklistDefinitionLine(' . htmlEncode($obj->id) . ');"' . ' title="' . i18n('addLine') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="noteHeader" style="width:30%">' . i18n('colName') . '</td>';
  echo '<td class="noteHeader" style="width:'.(($print)?'65':'60').'%">' . i18n('colChoices') . '</td>';
  echo '<td class="noteHeader" style="width:5%">' . i18n('colExclusiveShort') . '</td>';
  echo '</tr>';
  
  usort($lines, "ChecklistDefinitionLine::sort");
  foreach ( $lines as $line ) {
    echo '<tr>';
    if (!$print) {
      echo '<td class="noteData" style="text-align:center;">';
      if ($canUpdate) {
        echo ' <a onClick="editChecklistDefinitionLine(' . htmlEncode($obj->id) . ',' . htmlEncode($line->id) . ');"' 
            . ' title="' . i18n('editLine') . '" > '.formatSmallButton('Edit').'</a>';
        echo ' <a onClick="removeChecklistDefinitionLine(' . htmlEncode($line->id) . ');"' . ' title="' . i18n('removeLine') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    if ($line->check01) {
      echo '<td class="noteData" style="border-right:0; text-align:right" title="' . htmlEncode($line->title) . '">' 
        . '<div style="position: relative;">'  
        . htmlEncode($line->name) . '<div style="position:absolute;top:0px; left:0px; color: #AAAAAA;">' . htmlEncode($line->sortOrder) . '</div>' . ' : '
        . '</div></td>';
      echo '<td class="noteData" style="border-left:0;">';
      echo '<table witdh="100%"><tr>';
      for ($i=1; $i <= 5; $i++) {
        $check='check0' . $i;
        $title='title0' . $i;
        echo '<td style="min-width:100px; white-space:nowrap; vertical-align:top; " ' . (($line->$title)?'title="' . $line->$title . '"':'') . '>';
        if ($line->$check ) {
          echo "<table><tr><td>".htmlDisplayCheckbox(0) . "&nbsp;</td><td valign='top'>" . $line->$check . "&nbsp;&nbsp;</td></tr></table>";
        }
        echo '</td>';
      }
      echo '</tr></table>';
      echo '</td>';
      echo '<td class="noteData">' . htmlDisplayCheckbox($line->exclusive) . '</td>';
    } else {
      echo '<td class="reportTableHeader" colspan="3" style="text-align:center" title="' . htmlEncode($line->title) . '">' . htmlEncode($line->name) . '</td>';
    }
    echo '</tr>';
  }
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteDataClosetable">&nbsp;</td>';
  }
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '</tr>';
  echo '</table>';
}

function drawAttachmentsFromObject($obj, $refresh=false) {
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  echo '<input type="hidden" id="attachmentIdle" value="' . htmlEncode($obj->idle) . '" />';
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (isset($obj->_Attachment)) {
    $attachments=$obj->_Attachment;
  } else {
    $attachments=array();
  }
  if (!$refresh) echo '<tr><td colspan="2">';
  echo '<table width="100%">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="attachmentHeader smallButtonsGroup" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addAttachment(\'file\');" title="' . i18n('addAttachment') . '"> '.formatSmallButton('Add').'</a>';
      echo '<a onClick="addAttachment(\'link\');" title="' . i18n('addHyperlink') . '" > '.formatSmallButton('Link').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="attachmentHeader" style="width:5%">' . i18n('colId') . '</td>';
  echo '<td colspan="2" class="attachmentHeader" style="width:' . (($print)?'95':'85') . '%">' . i18n('colFile') . '</td>';
  echo '</tr>';
  foreach ( $attachments as $attachment ) {
    $userId=$attachment->idUser;
    $ress=new Resource($user->id);
    if ($user->id == $attachment->idUser or $attachment->idPrivacy == 1 or ($attachment->idPrivacy == 2 and $ress->idTeam == $attachment->idTeam)) {
      $userName=SqlList::getNameFromId('User', $userId);
      $creationDate=$attachment->creationDate;
      $updateDate=null;
      echo '<tr>';
      if (!$print) {
        echo '<td class="attachmentData smallButtonsGroup" style="width:10%"">';
        if ($attachment->fileName and $attachment->subDirectory and !$print) {
          echo '<a href="../tool/download.php?class=Attachment&id=' . htmlEncode($attachment->id) . '"';
          echo ' target="printFrame" title="' . i18n('helpDownload') . '">'.formatSmallButton('Download').'</a>';
        }
        if ($attachment->link and !$print) {
          echo '<a href="' . htmlEncode(urldecode($attachment->link)) . '"';
          echo ' target="#" title="' . urldecode($attachment->link) . '">'.formatSmallButton('Link').'</a>';
        }
        if ($attachment->idUser == $user->id and !$print and $canUpdate) {
          echo ' <a onClick="removeAttachment(' . htmlEncode($attachment->id) . ');" title="' . i18n('removeAttachment') . '" >'
              . formatSmallButton('Remove')
              . '</a>';
        }
        echo '</td>';
      }
      echo '<td class="attachmentData" style="width:5%;">#' . htmlEncode($attachment->id) . '</td>';
      echo '<td class="attachmentData" style="width:5%;border-right:none;text-align:center;">';
      if ($attachment->isThumbable()) {
        echo '<img src="' . getImageThumb($attachment->getFullPathFileName(), 32) . '" ' . ' title="' . htmlEncode($attachment->fileName) . '" style="float:left;cursor:pointer"' . ' onClick="showImage(\'Attachment\',\'' . htmlEncode($attachment->id) . '\',\'' . htmlEncode($attachment->fileName,'protectQuotes') . '\');" />';
      } else if ($attachment->link and !$print) {
        echo '<div style="float:left;cursor:pointer" onClick="showLink(\'' . htmlEncode(urldecode($attachment->link)) . '\');">';
        echo '<img src="../view/img/mime/html.png" title="' . htmlEncode($attachment->link) . '" />';
        echo '</div>';
      } else {
        echo htmlGetMimeType($attachment->mimeType, $attachment->fileName, $attachment->id);
      }
      echo '</td><td class="attachmentData" style="border-left:none;width:' . (($print)?'90':'80') . '%" >';
      echo formatUserThumb($userId, $userName, 'Creator');
      echo formatDateThumb($creationDate, $updateDate);
      echo formatPrivacyThumb($attachment->idPrivacy, $attachment->idTeam);
      if ($attachment->description and !$print) {
        echo formatCommentThumb($attachment->description);
      }
      if ($attachment->link) {
        echo htmlEncode(urldecode($attachment->link), 'print');
      } else {
        echo htmlEncode($attachment->fileName, 'print');
      }
      echo '</td>';
      echo '</tr>';
    }
  }
  echo '<tr>';
  if (!$print) {
    echo '<td class="attachmentDataClosetable">&nbsp;';
    echo '<input type="hidden" name="nbAttachments" id="nbAttachments" value="' . count($attachments) . '" />';
    echo '</td>';
  }
  echo '<td class="attachmentDataClosetable">&nbsp;</td>';
  echo '<td class="attachmentDataClosetable">&nbsp;</td>';
  echo '<td class="attachmentDataClosetable">&nbsp;</td>';
  echo '</tr>';
  echo '</table>';
  if (! $refresh) echo "</td></tr>";
  if (! $print) {
    echo '<input id="AttachmentSectionCount" type="hidden" value="'.count($attachments).'" />';
  }
}

function drawLinksFromObject($list, $obj, $classLink, $refresh=false) {
  if ($obj->isAttributeSetToField("_Link", "hidden")) {
    return;
  }
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  if (get_class($obj) == 'Document') {
    $dv=new DocumentVersion();
    $lstVers=$dv->getSqlElementsFromCriteria(array('idDocument' => $obj->id));
    foreach ( $lstVers as $dv ) {
      $crit="(ref1Type='DocumentVersion' and ref1Id=" . htmlEncode($dv->id) . ")";
      $crit.="or (ref2Type='DocumentVersion' and ref2Id=" . htmlEncode($dv->id) . ")";
      $lnk=new Link();
      $lstLnk=$lnk->getSqlElementsFromCriteria(null, null, $crit);
      foreach ( $lstLnk as $lnk ) {
        if ($lnk->ref1Type == 'DocumentVersion') {
          $lnk->ref1Type='Document';
          $lnk->ref1Id=$obj->id;
        } else {
          $lnk->ref2Type='Document';
          $lnk->ref2Id=$obj->id;
        }
        $list []=$lnk;
      }
    }
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (!$refresh) echo '<tr><td colspan="2">';
  echo '<table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="linkHeader" style="width:5%">';
    if ($obj->id != null and !$print and $canUpdate) {
      $linkable=SqlElement::getSingleSqlElementFromCriteria('Linkable', array('name' => get_class($obj)));
      $default=$linkable->idDefaultLinkable;
      echo '<a onClick="addLink(' . "'" . $classLink . "','" . $default . "'" . ');" title="' . i18n('addLink') . '" class="roundedButtonSmall">'.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  if (!$classLink) {
    echo '<td class="linkHeader" style="width:' . (($print)?'20':'15') . '%">' . i18n('colElement') . '</td>';
  } else {
    echo '<td class="linkHeader" style="width:' . (($print)?'10':'5') . '%">' . i18n('colId') . '</td>';
  }
  
  echo '<td class="linkHeader" style="width:' . (($classLink)?'75':'65') . '%">' . i18n('colName') . '</td>';
  // if ($classLink and property_exists($classLink, 'idStatus')) {
  echo '<td class="linkHeader" style="width:15%">' . i18n('colIdStatus') . '</td>';
  // }
  //echo '<td class="linkHeader" style="width:15%">' . i18n('colDate') . '</td>';
  //echo '<td class="linkHeader" style="width:15%">' . i18n('colUser') . '</td>';
  echo '</tr>';
  foreach ( $list as $link ) {
    $linkObj=null;
    if ($link->ref1Type == get_class($obj) and $link->ref1Id == $obj->id) {
      $linkObj=new $link->ref2Type($link->ref2Id);
    } else {
      $linkObj=new $link->ref1Type($link->ref1Id);
    }
    $userId=$link->idUser;
    $userName=SqlList::getNameFromId('User', $userId);
    $creationDate=$link->creationDate;
    $prop='_Link_' . get_class($linkObj);
    if ($classLink or !property_exists($obj, $prop)) {
      $gotoObj=(get_class($linkObj) == 'DocumentVersion')?new Document($linkObj->idDocument):$linkObj;
      $canGoto=(securityCheckDisplayMenu(null, get_class($gotoObj)) and securityGetAccessRightYesNo('menu' . get_class($gotoObj), 'read', $gotoObj) == "YES")?true:false;
      echo '<tr>';
      if (substr(get_class($linkObj), 0, 7) == 'Context') {
        $classLinkName=SqlList::getNameFromId('ContextType', substr(get_class($linkObj), 7, 1));
      } else {
        $classLinkName=i18n(get_class($linkObj));
      }
      if (!$print) {
        echo '<td class="linkData" style="text-align:center;width:5%;white-space:nowrap;">';
        if ($canGoto and (get_class($linkObj) == 'DocumentVersion' or get_class($linkObj) == 'Document') and isset($gotoObj->idDocumentVersion) and $gotoObj->idDocumentVersion) {
          echo '<a href="../tool/download.php?class=' . get_class($linkObj) . '&id=' . htmlEncode($linkObj->id) . '"';
          echo ' target="printFrame" title="' . i18n('helpDownload') . '">'
               .formatSmallButton('Download')
               .'</a>';
        }
        if ($canUpdate) {
          echo '  <a onClick="removeLink(' . "'" . htmlEncode($link->id) . "','" . get_class($linkObj) . "','" . htmlEncode($linkObj->id) . "','" . $classLinkName . "'" . ');" title="' . i18n('removeLink') . '" > '
                  .formatSmallButton('Remove')
               .'</a>';
        }
        echo '</td>';
      }
      if (!$classLink) {
        echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'20':'15') . '%"> <table><tr><td>';
        
        if (get_class($linkObj) == 'DocumentVersion' or get_class($linkObj) == 'Document') {
          if (get_class($linkObj) == 'DocumentVersion') $version=$linkObj;
          else $version=new DocumentVersion($linkObj->idDocumentVersion);
          if ($version->isThumbable()) {
            $ext = pathinfo($version->fileName, PATHINFO_EXTENSION);
            if (file_exists("../view/img/mime/$ext.png")) {
              $img="../view/img/mime/$ext.png";
            } else {
              $img= "../view/img/mime/unknown.png";
            }
            echo '<img src="' . $img . '" ' . ' title="' . htmlEncode($version->fileName) . '" style="float:left;cursor:pointer"' . ' onClick="showImage(\'DocumentVersion\',\'' . htmlEncode($version->id) . '\',\'' . htmlEncode($version->fileName,'protectQuotes') . '\');" />';
          } else {
            echo htmlGetMimeType($version->mimeType, $version->fileName , $version->id,'DocumentVersion');
          }
        } else {
          echo formatIcon(get_class($linkObj),16);
        }
        echo '</td><td style="vertical-align:top">&nbsp;'.$classLinkName .' #' . $linkObj->id.'</td></tr></table>';
      } else {
        echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'10':'5') . '%">#' . $linkObj->id;
      }
      echo '</td>';
      $goto="";
      if (!$print and $canGoto) {
        $goto=' onClick="gotoElement(' . "'" . get_class($gotoObj) . "','" . htmlEncode($gotoObj->id) . "'" . ');" style="cursor: pointer;" ';
      }
      echo '<td class="linkData" ' . $goto . ' style="position:relative;width:' . (($classLink)?'45':'35') . '%">';
     
      echo (get_class($linkObj) == 'DocumentVersion')?htmlEncode($linkObj->fullName):htmlEncode($linkObj->name);
      
      echo formatUserThumb($userId, $userName, 'Creator');
      echo formatDateThumb($creationDate, null);
      echo formatCommentThumb($link->comment);
      
      echo '</td>';
      if (property_exists($linkObj, 'idStatus')) {
        $objStatus=new Status($linkObj->idStatus);
        // $color=$objStatus->color;
        // $foreColor=getForeColor($color);
        // echo '<td class="linkData"><table width="100%"><tr><td style="background-color: ' . htmlEncode($objStatus->color) . '; color:' . $foreColor . ';width: 100%;">' . htmlEncode($objStatus->name) . '</td></tr></table></td>';
        echo '<td class="dependencyData"  style="width:15%">' . colorNameFormatter($objStatus->name . "#split#" . $objStatus->color) . '</td>';
      }
      //echo '<td class="dependencyData"  style="width:15%">' . htmlFormatDateTime($creationDate) . '<br/></td>';
      //echo '<td class="dependencyData"  style="width:15%">' . $userName . '</td>';
      echo '</tr>';
    }
  }
  echo '</table>';
  if (!$refresh) echo '</td></tr>';
  if (! $print) {
    echo '<input id="LinkSectionCount" type="hidden" value="'.count($list).'" />';
  }
}

function drawStructureFromObject($obj, $refresh=false,$way,$item) {
  $crit=array();
  if ($way=='composition') {
    $crit['idProduct']=$obj->id;
  } else if ($way=='structure') {
    $crit['idComponent']=$obj->id;
  } else {
    errorLog("unknown way=$way in drawStructureFromObject()");
  }
  $pcs=new ProductStructure();
  $list=$pcs->getSqlElementsFromCriteria($crit);
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (!$refresh) echo '<tr><td colspan="2">';
  echo '<table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="linkHeader" style="width:5%">';
    if ($obj->id != null and !$print and $canUpdate) {
     echo '<a onClick="addProductStructure(\''.$way.'\');" title="' . i18n('addProductStructure') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  $listClass=($item=='Product')?'Component':(($way=='structure')?'ProductOrComponent':'Component');
  echo '<td class="linkHeader" style="width:' . (($print)?'20':'15') . '%">' . i18n($listClass) . '</td>';
  echo '<td class="linkHeader" style="width:80%">' . i18n('colName') . '</td>';
  echo '</tr>';
  foreach ( $list as $comp ) {
    $compObj=null;
    if ($way=='structure') {
      $compObj=new ProductOrComponent($comp->idProduct);
    } else {
      $compObj=new ProductOrComponent($comp->idComponent);
    }
    if ($compObj->scope=='Product') $compObj=new Product($compObj->id);
    else $compObj=new Component($compObj->id);
    $userId=$comp->idUser;
    $userName=SqlList::getNameFromId('User', $userId);
    $creationDate=$comp->creationDate;
    $canGoto=(securityCheckDisplayMenu(null, get_class($compObj)) and securityGetAccessRightYesNo('menu' . get_class($compObj), 'read', $compObj) == "YES")?true:false;
    echo '<tr>';
    $classCompName=i18n(get_class($compObj));
    if (!$print) {
      echo '<td class="linkData" style="text-align:center;width:5%;white-space:nowrap;">';
      if ($canUpdate) {
        echo '  <a onClick="removeProductStructure(' . "'" . htmlEncode($comp->id) . "','" . get_class($compObj) . "','" . htmlEncode($compObj->id) . "','" . $classCompName . "'" . ');" '
              .'title="' . i18n('removeProductStructure') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    //echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'20':'15') . '%"><img src="css/images/icon'.get_class($compObj).'16.png" />&nbsp;'.$classCompName .' #' . $compObj->id;
    echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'20':'15') . '%"><table><tr>';
    echo '<td>'.formatIcon(get_class($compObj),16).'</td><td style="vertical-align:top">&nbsp;'.'#' . $compObj->id.'</td></tr></table>';
    echo '</td>';
    $goto="";
    if (!$print and $canGoto) {
      $goto=' onClick="gotoElement(' . "'" . get_class($compObj) . "','" . htmlEncode($compObj->id) . "'" . ');" style="cursor: pointer;" ';
    }
    echo '<td class="linkData" ' . $goto . ' style="position:relative;">';
    echo htmlEncode($compObj->name);
    echo formatUserThumb($userId, $userName, 'Creator');
    echo formatDateThumb($creationDate, null);
    echo formatCommentThumb($comp->comment);
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
  if (!$refresh) echo '</td></tr>';
  if (! $print) {
    echo '<input id="ProductStructureSectionCount" type="hidden" value="'.count($list).'" />';
  }
}

function drawVersionStructureFromObject($obj, $refresh=false,$way,$item) {
  $crit=array();
  if ($way=='composition') {
    $crit['idProductVersion']=$obj->id;
  } else if ($way=='structure') {
    $crit['idComponentVersion']=$obj->id;
  } else {
    errorLog("unknown way=$way in drawVersionStructureFromObject()");
  }
  $pcs=new ProductVersionStructure();
  $list=$pcs->getSqlElementsFromCriteria($crit);
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (!$refresh) echo '<tr><td colspan="2">';
  echo '<table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="linkHeader" style="width:5%">';
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addProductVersionStructure(\''.$way.'\');" title="' . i18n('addProductVersionStructure') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  $listClass=($item=='ProductVersion')?'ComponentVersion':(($way=='structure')?'Version':'ComponentVersion');
  echo '<td class="linkHeader" style="width:' . (($print)?'20':'15') . '%">' . i18n($listClass) . '</td>';
  echo '<td class="linkHeader" style="width:80%">' . i18n('colName') . '</td>';
  echo '</tr>';
  foreach ( $list as $comp ) {
    $compObj=null;
    if ($way=='structure') {
      $compObj=new Version($comp->idProductVersion);
    } else {
      $compObj=new Version($comp->idComponentVersion);
    }   
    if ($compObj->scope=='Product') $compObj=new ProductVersion($compObj->id);
    else $compObj=new ComponentVersion($compObj->id);
    $userId=$comp->idUser;
    $userName=SqlList::getNameFromId('User', $userId);
    $creationDate=$compObj->creationDate;
    $canGoto=(securityCheckDisplayMenu(null, get_class($compObj)) and securityGetAccessRightYesNo('menu' . get_class($compObj), 'read', $compObj) == "YES")?true:false;
    echo '<tr>';
    $classCompName=i18n(get_class($compObj));
    if (!$print) {
      echo '<td class="linkData" style="text-align:center;width:5%;white-space:nowrap;">';
      if ($canUpdate) {
        echo '  <a onClick="removeProductVersionStructure(' . "'" . htmlEncode($comp->id) . "','" . get_class($compObj) . "','" . htmlEncode($compObj->id) . "','" . $classCompName . "'" . ');" '
              .'title="' . i18n('removeProductStructure') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    //echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'20':'15') . '%"><img src="css/images/icon'.get_class($compObj).'16.png" />&nbsp;'.$classCompName .' #' . $compObj->id;
    echo '<td class="linkData" style="white-space:nowrap;width:' . (($print)?'20':'15') . '%"><table><tr><td>'.formatIcon(get_class($compObj),16).'</td><td style="vertical-align:top">&nbsp;'.'#' . $compObj->id.'</td></tr></table>';
    echo '</td>';
    $goto="";
    if (!$print and $canGoto) {
      $goto=' onClick="gotoElement(' . "'" . get_class($compObj) . "','" . htmlEncode($compObj->id) . "'" . ');" style="cursor: pointer;" ';
    }
    echo '<td class="linkData" ' . $goto . ' style="position:relative;">';
    echo htmlEncode($compObj->name);
    echo formatUserThumb($userId, $userName, 'Creator');
    echo formatDateThumb($creationDate, null);
    echo formatCommentThumb($comp->comment);
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
  if (!$refresh) echo '</td></tr>';
  if (! $print) {
    echo '<input id="ProductVersionStructureSectionCount" type="hidden" value="'.count($list).'" />';
  }
}

function drawApproverFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="dependencyHeader" style="width:5%">';
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addApprover();" title="' . i18n('addApprover') . '" class="roundedButtonSmall"> '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="dependencyHeader" style="width:' . (($print)?'10':'5') . '%">' . i18n('colId') . '</td>';
  echo '<td class="dependencyHeader" style="width:40%">' . i18n('colName') . '</td>';
  echo '<td class="dependencyHeader" style="width:50%">' . i18n('colIdStatus') . '</td>';
  echo '</tr>';
  if ($obj and get_class($obj) == 'Document') {
    $docVers=new DocumentVersion($obj->idDocumentVersion);
  }
  foreach ( $list as $app ) {
    $appName=SqlList::getNameFromId('Affectable', $app->idAffectable);
    echo '<tr>';
    if (!$print) {
      echo '<td class="dependencyData" style="text-align:center;">';
      if ($canUpdate) {
        echo '  <a onClick="removeApprover(' . "'" . htmlEncode($app->id) . "','" . $appName . "'" . ');" title="' . i18n('removeApprover') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="dependencyData">#' . htmlEncode($app->id) . '</td>';
    echo '<td class="dependencyData">' . htmlEncode($appName) . '</td>';
    echo '<td class="dependencyData">';
    $approved=0;
    $compMsg="";
    $date="";
    $approverId=null;
    if ($obj and get_class($obj) == 'Document') {
      $crit=array('refType' => 'DocumentVersion','refId' => $obj->idDocumentVersion,'idAffectable' => $app->idAffectable);
      $versApp=SqlElement::getSingleSqlElementFromCriteria('Approver', $crit);
      if ($versApp->id) {
        $approved=$versApp->approved;
        $compMsg=' ' . $docVers->name;
        $date=" (" . htmlFormatDateTime($versApp->approvedDate, false) . ")";
        $approverId=$versApp->id;
      }
    } else {
      $approved=$app->approved;
      $approverId=$app->id;
      $date=" (" . htmlFormatDateTime($app->approvedDate, false) . ")";
    }
    if ($approved) {
      echo '<img src="../view/img/check.png" height="12px"/>&nbsp;';
      echo i18n("approved") . $compMsg . $date;
    } else {
      echo i18n("notApproved") . $compMsg;
      if ($user->id == $app->idAffectable and !$print) {
        echo '&nbsp;&nbsp;<button dojoType="dijit.form.Button" showlabel="true" >';
        echo i18n('approveNow');
        echo '  <script type="dojo/connect" event="onClick" args="evt">';
        echo '   approveItem(' . $approverId . ');';
        echo '  </script>';
        echo '</button>';
      }
    }
    
    echo '</td>';
    echo '</tr>';
  }
  echo '</table></td></tr>';
}

function drawDependenciesFromObject($list, $obj, $depType, $refresh=false) {
  global $cr, $print, $user, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  $canEdit=$canUpdate;
  if (get_class($obj) == "Term" or get_class($obj) == "Requirement" or get_class($obj) == "TestCase") {
    $canEdit=false;
  }
  if (get_class($obj) == "Term") {
    if ($obj->idBill)
      $canUpdate=false;
  }
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (!$refresh) echo '<tr><td colspan=2 style="width:100%;">';
  echo '<table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="dependencyHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<a onClick="addDependency(' . "'" . $depType . "'" . ');" title="' . i18n('addDependency' . $depType) . '"> '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="dependencyHeader" style="width:' . (($print)?'30':'20') . '%">' . i18n('colElement') . '</td>';
  echo '<td class="dependencyHeader" style="width:55%">' . i18n('colName') . '</td>';
  echo '<td class="dependencyHeader" style="width:15%">' . i18n('colIdStatus') . '</td>';
  echo '</tr>';
  foreach ( $list as $dep ) {
    $depObj=null;
    if ($dep->predecessorRefType == get_class($obj) and $dep->predecessorRefId == $obj->id) {
      $depObj=new $dep->successorRefType($dep->successorRefId);
      // $depType="Successor";
    } else {
      $depObj=new $dep->predecessorRefType($dep->predecessorRefId);
      // $depType="Predecessor";
    }
    echo '<tr>';
    if (!$print) {
      echo '<td class="dependencyData" style="text-align:center;white-space:nowrap;">';
      if ($canEdit) {
        echo '  <a onClick="editDependency(' . "'" . $depType . "','" . htmlEncode($dep->id) . "','" . SqlList::getIdFromName('Dependable', i18n(get_class($depObj))) . "','" . get_class($depObj) . "','" . htmlEncode($depObj->id) . "','" . htmlEncode($dep->dependencyDelay) . "'" . ');" ' .
             ' title="' . i18n('editDependency' . $depType) . '" > '.formatSmallButton('Edit').'</a>';
      }
      if ($canUpdate) {
        echo '  <a onClick="removeDependency(' . "'" . htmlEncode($dep->id) . "','" . get_class($depObj) . "','" . htmlEncode($depObj->id) . "'" . ');" ' .
            'title="' . i18n('removeDependency' . $depType) . '"/> '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="dependencyData" style="white-space:nowrap"><table><tr><td>'.formatIcon(get_class($depObj),16).'</td><td>&nbsp;' . i18n(get_class($depObj)) . ' #' . htmlEncode($depObj->id) . '</td></tr></table></td>';
    echo '<td class="dependencyData"';
    $goto="";
    if (securityCheckDisplayMenu(null, get_class($depObj)) and securityGetAccessRightYesNo('menu' . get_class($depObj), 'read', $depObj) == "YES") {
      $goto=' onClick="gotoElement(' . "'" . get_class($depObj) . "','" . htmlEncode($depObj->id) . "'" . ');" style="cursor: pointer;" ';
    }
    if (!$print) {
      echo $goto;
    }
    echo '>' . htmlEncode($depObj->name);
    ////KEVIN TICKET #2038
    echo formatCommentThumb($dep->comment);
    if ($dep->dependencyDelay != 0 and $canEdit) {
      echo '&nbsp;<span style="float:right;background-color:#FFF8DC; color:#696969; border:1px solid #A9A9A9;" title="' . i18n("colDependencyDelay") . '">&nbsp;' . htmlEncode($dep->dependencyDelay) . '&nbsp;' . i18n('shortDay') . '&nbsp;</span>';
    }
    echo '</td>';
    if (property_exists($depObj, 'idStatus')) {
      $objStatus=new Status($depObj->idStatus);
    } else {
      $objStatus=new Status();
    }
    // $color=$objStatus->color;
    // $foreColor=getForeColor($color);
    // echo '<td class="dependencyData"><table><tr><td style="background-color: ' . htmlEncode($objStatus->color) . '; color:' . $foreColor . ';">' . htmlEncode($objStatus->name) . '</td></tr></table></td>';
    // echo '<td class="dependencyData" style="background-color: ' . htmlEncode($objStatus->color) . '; color:' . $foreColor . ';">' . htmlEncode($objStatus->name) . '</td>';
    echo '<td class="dependencyData" style="width:15%">' . colorNameFormatter($objStatus->name . "#split#" . $objStatus->color) . '</td>';
    echo '</tr>';
  }
  echo '</table>';
  if (!$refresh) echo '</td></tr>';
  if (! $print) {
    echo '<input id="'.$depType.'DependencySectionCount" type="hidden" value="'.count($list).'" />';
  }
}

function drawAssignmentsFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail, $section, $collapsedList, $widthPct, $outMode, $profile;
  if ($comboDetail) {
    return;
  }
  $pluginObjectClass='Assignment';
  $tableObject=$list;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $list=$tableObject;
  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile' => $profile,'scope' => 'assignmentView'));
  if ($habil and $habil->rightAccess != 1) {
    return;
  }
  $section='Assignment';
  // startTitlePane(get_class ( $obj ), $section, $collapsedList, $widthPct, $print, $outMode, "yes", $nbCol);
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile' => $profile,'scope' => 'assignmentEdit'));
  if ($habil and $habil->rightAccess != 1) {
    $canUpdate=false;
  }
  $pe=new PlanningElement();
  $pe->setVisibility();
  $workVisible=($pe->_workVisibility == 'ALL')?true:false;
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (!$print and $canUpdate) {
    echo '<td class="assignHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle and $workVisible) {
      echo '<a onClick="addAssignment(\'' . Work::displayShortWorkUnit() . '\',\'' . Work::getWorkUnit() . '\',\'' . Work::getHoursPerDay() . '\');" ';
      echo ' title="' . i18n('addAssignment') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="assignHeader" style="width:' . (($print)?'40':'30') . '%">' . i18n('colIdResource') . '</td>';
  echo '<td class="assignHeader" style="width:15%" >' . i18n('colRate') . '</td>';
  if ($workVisible) {
    echo '<td class="assignHeader" style="width:15%">' . i18n('colAssigned') . ' (' . Work::displayShortWorkUnit() . ')' . '</td>';
    echo '<td class="assignHeader"style="width:15%">' . i18n('colReal') . ' (' . Work::displayShortWorkUnit() . ')' . '</td>';
    echo '<td class="assignHeader" style="width:15%">' . i18n('colLeft') . ' (' . Work::displayShortWorkUnit() . ')' . '</td>';
  }
  echo '</tr>';
  $fmt=new NumberFormatter52($browserLocale, NumberFormatter52::DECIMAL);
  foreach ( $list as $assignment ) {
    echo '<tr>';
    $isResource=true;
    $resName=SqlList::getNameFromId('Resource', $assignment->idResource);
    if ($resName == $assignment->idResource) {
      $affName=SqlList::getNameFromId('Affectable', $assignment->idResource);
      if ($affName != $resName) {
        $isResource=false;
        $resName=$affName;
      }
    }
    if (!$print and $canUpdate) {
      echo '<td class="assignData" style="text-align:center;white-space:nowrap;">';
      if ($canUpdate and !$print and $workVisible) {
        echo '  <a onClick="editAssignment(' . "'" . htmlEncode($assignment->id) . "'" . ",'" . htmlEncode($assignment->idResource) . "'" . ",'" . htmlEncode($assignment->idRole) . "'" . ",'" . ($assignment->dailyCost * 100) . "'" . ",'" . htmlEncode($assignment->rate) . "'" . ",'" .
             Work::displayWork($assignment->assignedWork) * 100 . "'" . ",'" . Work::displayWork($assignment->realWork) * 100 . "'" . ",'" . Work::displayWork($assignment->leftWork) * 100 . "'" . ",'" . Work::displayShortWorkUnit() . "'" . ');" ' . 'title="' . i18n('editAssignment') .
             '" > '.formatSmallButton('Edit').'</a>';
        echo '<textarea style="display:none" id="comment_assignment_' . htmlEncode($assignment->id) . '" >'.htmlEncode($assignment->comment)."</textarea>";
      }
      if ($assignment->realWork == 0 and $canUpdate and !$print and $workVisible) {
        echo '  <a onClick="removeAssignment(' . "'" . htmlEncode($assignment->id) . "','" . Work::displayWork($assignment->realWork) * 100 . "','" . htmlEncode($resName, 'quotes') . "'" . ');" ' . 
        'title="' . i18n('removeAssignment') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="assignData">';
    echo '<table width="100%"><tr>';
    $goto="";
    if (!$print and $isResource and securityCheckDisplayMenu(null, 'Resource') and securityGetAccessRightYesNo('menuResource', 'read', '') == "YES") {
      $goto=' onClick="gotoElement(\'Resource\',\'' . htmlEncode($assignment->idResource) . '\');" style="cursor: pointer;" ';
    }
    echo '<td ' . $goto . '>' . $resName;
    echo ($assignment->idRole)?' (' . SqlList::getNameFromId('Role', $assignment->idRole) . ')':'';
    echo '</td>';
    if ($assignment->notPlannedWork> 0) {
      echo '<td>';
      echo '&nbsp;<span style="float:right;background-color:#FFAAAA; color:#696969; border:1px solid #A9A9A9;" title="' . i18n("colNotPlannedWork") . '">&nbsp;' . Work::displayWorkWithUnit($assignment->notPlannedWork). '&nbsp;</span>';
      echo '</td>';
    }
    if ($assignment->comment and !$print) {
      echo '<td>';
      echo formatCommentThumb($assignment->comment);
      echo '</td>';
    }
    echo '</tr></table>';
    


    echo '</td>';
    echo '<td class="assignData" align="center">' . htmlEncode($assignment->rate) . '</td>';
    if ($workVisible) {
      echo '<td class="assignData" align="right">' . $fmt->format(Work::displayWork($assignment->assignedWork)) . '</td>';
      echo '<td class="assignData" align="right">' . $fmt->format(Work::displayWork($assignment->realWork)) . '</td>';
      echo '<td class="assignData" align="right">' . $fmt->format(Work::displayWork($assignment->leftWork)) . '</td>';
    }
    echo '</tr>';
  }
  echo '</table></td></tr>';
}

function drawExpenseDetailFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  // $pe=new PlanningElement();
  // $pe->setVisibility();
  // $workVisible=($pe->_workVisibility=='ALL')?true:false;
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  $scope=str_replace('expense','',strtolower(get_class($obj)));
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (!$print) {
    echo '<td class="assignHeader" style="width:5%">';
    // if ($obj->id!=null and ! $print and $canUpdate and !$obj->idle and $workVisible) {
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle) {
      echo '<a onClick="addExpenseDetail(\''.$scope.'\');" title="' . i18n('addExpenseDetail') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="assignHeader" style="width:' . (($print)?'13':'8') . '%">' . i18n('colDate') . '</td>';
  echo '<td class="assignHeader" style="width:10%">' . i18n('colReference') . '</td>';
  echo '<td class="assignHeader" style="width:30%">' . i18n('colName') . '</td>';
  echo '<td class="assignHeader" style="width:12%" >' . i18n('colType') . '</td>';
  echo '<td class="assignHeader" style="width:25%">' . i18n('colDetail') . '</td>';
  // if ($workVisible) {
  echo '<td class="assignHeader" style="width:10%">' . i18n('colAmount') . '</td>';
  // }
  echo '</tr>';
  $fmt=new NumberFormatter52($browserLocale, NumberFormatter52::DECIMAL);
  foreach ( $list as $expenseDetail ) {
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignData" style="text-align:center;white-space:nowrap;width:5%">';
      // if ($canUpdate and ! $print and $workVisible) {
      if ($canUpdate and !$print) {
        echo '  <a onClick="editExpenseDetail(\''.$scope.'\',' . "'" . htmlEncode($expenseDetail->id) . "'" . ",'" . htmlEncode($expenseDetail->idExpense) . "'" . ",'" . htmlEncode($expenseDetail->idExpenseDetailType) . "'" . ",'" . htmlEncode($expenseDetail->expenseDate) . "'" . ",'" .
             $fmt->format($expenseDetail->amount) . "'" . ');" ' . 'title="' . i18n('editExpenseDetail') . '" > '.formatSmallButton('Edit').'</a>';
      }
      // if ($canUpdate and ! $print and $workVisible ) {
      if ($canUpdate and !$print) {
        echo '  <a onClick="removeExpenseDetail(' . "'" . htmlEncode($expenseDetail->id) . "'" . ');" ' . 'title="' . i18n('removeExpenseDetail') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="assignData" style="width:' . (($print)?'13':'8') . '%">' . htmlFormatDate($expenseDetail->expenseDate) . '</td>';
    echo '<td class="assignData" style="width:10%">' . $expenseDetail->externalReference. '</td>';
    echo '<td class="assignData" style="width:30%"';
    echo '>' . $expenseDetail->name;
    if ($expenseDetail->description and !$print) {
      echo formatCommentThumb($expenseDetail->description);
    }
    echo '<input type="hidden" id="expenseDetail_' . htmlEncode($expenseDetail->id) . '" value="' . htmlEncode($expenseDetail->name, 'none') . '"/>';
    echo '<input type="hidden" id="expenseDetailRef_' . htmlEncode($expenseDetail->id) . '" value="' . htmlEncode($expenseDetail->externalReference, 'none') . '"/>';
    
    echo '</td>';
    echo '<td class="assignData" style="width:12%">' . SqlList::getNameFromId('ExpenseDetailType', $expenseDetail->idExpenseDetailType) . '</td>';
    echo '<td class="assignData" style="width:25%">';
    echo $expenseDetail->getFormatedDetail();
    echo '</td>';
    echo '<td class="assignData" style="text-align:right;width:10%"">' . htmlDisplayCurrency($expenseDetail->amount) . '</td>';
    echo '</tr>';
  }
  echo '</table></td></tr>';
}

function drawResourceCostFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  $pe=new PlanningElement();
  $pe->setVisibility();
  $costVisible=($pe->_costVisibility == 'ALL')?true:false;
  if (!$costVisible)
    return;
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  $funcList=' ';
  foreach ( $list as $rcost ) {
    $key='#' . htmlEncode($rcost->idRole) . '#';
    if (strpos($funcList, $key) === false) {
      $funcList.=$key;
    }
  }
  if (!$print) {
    echo '<td class="assignHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle) {
      echo '<a onClick="addResourceCost(\'' . htmlEncode($obj->id) . '\', \'' . htmlEncode($obj->idRole) . '\',\'' . $funcList . '\');" title="' . i18n('addResourceCost') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="assignHeader" style="width:' . (($print)?'40':'30') . '%">' . i18n('colIdRole') . '</td>';
  echo '<td class="assignHeader" style="width:20%">' . i18n('colCost') . '</td>';
  echo '<td class="assignHeader" style="width:20%">' . i18n('colStartDate') . '</td>';
  echo '<td class="assignHeader" style="width:20%">' . i18n('colEndDate') . '</td>';
  
  echo '</tr>';
  $fmt=new NumberFormatter52($browserLocale, NumberFormatter52::DECIMAL);
  foreach ( $list as $rcost ) {
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignData" style="text-align:center;">';
      if (!$rcost->endDate and $canUpdate and !$print) {
        echo '  <a onClick="editResourceCost(' . "'" . htmlEncode($rcost->id) . "'" . ",'" . htmlEncode($rcost->idResource) . "'" . ",'" . htmlEncode($rcost->idRole) . "'" . ",'" . $rcost->cost * 100 . "'" . ",'" . htmlEncode($rcost->startDate) . "'" . ",'" . htmlEncode($rcost->endDate) . "'" . ');" ' . 'title="' .
             i18n('editResourceCost') . '" > '.formatSmallButton('Edit').'</a>';
      }
      if (!$rcost->endDate and $canUpdate and !$print) {
        echo '  <a onClick="removeResourceCost(' . "'" . htmlEncode($rcost->id) . "'" . ",'" . htmlEncode($rcost->idRole) . "'" . ",'" . SqlList::getNameFromId('Role', $rcost->idRole) . "'" . ",'" . htmlFormatDate($rcost->startDate) . "'" . ');" ' . 'title="' .
             i18n('removeResourceCost') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    echo '<td class="assignData" align="left">' . SqlList::getNameFromId('Role', $rcost->idRole) . '</td>';
    echo '<td class="assignData" align="right">' . htmlDisplayCurrency($rcost->cost);
    echo " / " . i18n('shortDay');
    echo '</td>';
    echo '<td class="assignData" align="center">' . htmlFormatDate($rcost->startDate) . '</td>';
    echo '<td class="assignData" align="center">' . htmlFormatDate($rcost->endDate) . '</td>';
    echo '</tr>';
  }
  echo '</table></td></tr>';
}

function drawVersionProjectsFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (get_class($obj) == 'Project') {
    $idProj=$obj->id;
    $idVers=null;
  } else if (SqlElement::is_a($obj,'Version') ) {
    $idProj=null;
    $idVers=$obj->id;
  }
  if (!$print) {
    echo '<td class="assignHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle) {
      echo '<a onClick="addVersionProject(\'' . $idVers . '\', \'' . $idProj . '\');" title="' . i18n('addVersionProject') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  if ($idProj) {
    echo '<td class="assignHeader" style="width:' . (($print)?'60':'50') . '%">' . i18n('colIdVersion') . '</td>';
  } else {
    echo '<td class="assignHeader" style="width:' . (($print)?'60':'50') . '%">' . i18n('colIdProject') . '</td>';
  }
  echo '<td class="assignHeader" style="width:15%">' . i18n('colStartDate') . '</td>';
  echo '<td class="assignHeader" style="width:15%">' . i18n('colEndDate') . '</td>';
  echo '<td class="assignHeader" style="width:10%">' . i18n('colIdle') . '</td>';
  
  echo '</tr>';
  foreach ( $list as $vp ) {
    $vers=new Version($vp->idVersion);
    if ($vers->scope!='Product') continue;
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignData" style="text-align:center;white-space:nowrap">';
      if ($canUpdate and !$print) {
        echo '  <a onClick="editVersionProject(' . "'" . htmlEncode($vp->id) . "'" . ",'" . htmlEncode($vp->idVersion) . "'" . ",'" . htmlEncode($vp->idProject) . "'" . ');" ' . 'title="' .
             i18n('editVersionProject') . '" > '.formatSmallButton('Edit').'</a>';
      }
      if ($canUpdate and !$print) {
        echo '  <a onClick="removeVersionProject(' . "'" . htmlEncode($vp->id) . "'" . ');" ' . 'title="' . i18n('removeVersionProject') . '" > '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    $goto="";
    if ($idProj) {
      if (!$print and securityCheckDisplayMenu(null, 'ProductVersion') and securityGetAccessRightYesNo('menuProductVersion', 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\'ProductVersion\',\'' . htmlEncode($vp->idVersion) . '\');" style="cursor: pointer;" ';
      }
      echo '<td class="assignData" align="left"' . $goto . '>' . htmlEncode(SqlList::getNameFromId('Version', $vp->idVersion)) . '</td>';
    } else {
      if (!$print and securityCheckDisplayMenu(null, 'Project') and securityGetAccessRightYesNo('menuProject', 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\'Project\',\'' . htmlEncode($vp->idProject) . '\');" style="cursor: pointer;" ';
      }
      echo '<td class="assignData" align="left"' . $goto . '>' . htmlEncode(SqlList::getNameFromId('Project', $vp->idProject)) . '</td>';
    }
    echo '<td class="assignData" align="center">' . htmlFormatDate($vp->startDate) . '</td>';
    echo '<td class="assignData" align="center">' . htmlFormatDate($vp->endDate) . '</td>';
    echo '<td class="assignData" align="center"><img src="../view/img/checked' . (($vp->idle)?'OK':'KO') . '.png" /></td>';
    
    echo '</tr>';
  }
  echo '</table></td></tr>';
}
function drawProductProjectsFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (get_class($obj) == 'Project') {
    $idProj=$obj->id;
    $idProd=null;
  } else if (get_class($obj) =='Product' ) {
    $idProj=null;
    $idProd=$obj->id;
  }
  if (!$print) {
    echo '<td class="assignHeader" style="width:10%">';
    if ($obj->id != null and !$print and $canUpdate and !$obj->idle) {
      echo '<a onClick="addProductProject(\'' . $idProd . '\', \'' . $idProj . '\');" title="' . i18n('addProductProject') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  if ($idProj) {
    echo '<td class="assignHeader" style="width:' . (($print)?'60':'50') . '%">' . i18n('colIdProduct') . '</td>';
  } else {
    echo '<td class="assignHeader" style="width:' . (($print)?'60':'50') . '%">' . i18n('colIdProject') . '</td>';
  }
  echo '<td class="assignHeader" style="width:15%">' . i18n('colStartDate') . '</td>';
  echo '<td class="assignHeader" style="width:15%">' . i18n('colEndDate') . '</td>';
  echo '<td class="assignHeader" style="width:10%">' . i18n('colIdle') . '</td>';

  echo '</tr>';
  foreach ( $list as $pp ) {
    //$prod=new Product($pp->idProduct);
    echo '<tr>';
    if (!$print) {
      echo '<td class="assignData" style="text-align:center;white-space:nowrap">';
      if ($canUpdate and !$print) {
        echo '  <a onClick="editProductProject(' . "'" . htmlEncode($pp->id) . "'" . ",'" . htmlEncode($pp->idProduct) . "'" . ",'" . htmlEncode($pp->idProject) . "'" . ');" ' . 
            'title="' . i18n('editProductProject') . '" > '.formatSmallButton('Edit').'</a>';
      }
      if ($canUpdate and !$print) {
        echo '  <a onClick="removeProductProject(' . "'" . htmlEncode($pp->id) . "'" . ');" ' . 
            'title="' . i18n('removeProductProject') . '"> '.formatSmallButton('Remove').'</a>';
      }
      echo '</td>';
    }
    $goto="";
    if ($idProj) {
      if (!$print and securityCheckDisplayMenu(null, 'Product') and securityGetAccessRightYesNo('menuProduct', 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\'Product\',\'' . htmlEncode($pp->idProduct) . '\');" style="cursor: pointer;" ';
      }
      echo '<td class="assignData" align="left"' . $goto . '>' . htmlEncode(SqlList::getNameFromId('Product', $pp->idProduct)) . '</td>';
    } else {
      if (!$print and securityCheckDisplayMenu(null, 'Project') and securityGetAccessRightYesNo('menuProject', 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\'Project\',\'' . htmlEncode($pp->idProject) . '\');" style="cursor: pointer;" ';
      }
      echo '<td class="assignData" align="left"' . $goto . '>' . htmlEncode(SqlList::getNameFromId('Project', $pp->idProject)) . '</td>';
    }
    echo '<td class="assignData" align="center">' . htmlFormatDate($pp->startDate) . '</td>';
    echo '<td class="assignData" align="center">' . htmlFormatDate($pp->endDate) . '</td>';
    echo '<td class="assignData" align="center"><img src="../view/img/checked' . (($pp->idle)?'OK':'KO') . '.png" /></td>';

    echo '</tr>';
  }
  echo '</table></td></tr>';
}

function drawAffectationsFromObject($list, $obj, $type, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  $pluginObjectClass='Affectation';
  $tableObject=$list;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $list=$tableObject;
  if ($comboDetail) {
    return;
  }
  $canCreate=securityGetAccessRightYesNo('menuAffectation', 'create') == "YES";
  if (! (securityGetAccessRightYesNo('menu'.get_class($obj), 'update', $obj) == "YES") ) {
    $canCreate=false;
    $canUpdate=false;
    $canDelete=false;
  }
  if ($obj->idle == 1) {
    $canUpdate=false;
    $canCreate=false;
    $canDelete=false;
  }
  
  echo '<table style="width:100%">';
  echo '<tr><td colspan=2 style="width:100%;"><table style="width:100%;">';
  echo '<tr>';
  if (get_class($obj) == 'Project') {
    $idProj=$obj->id;
    $idRess=null;
  } else if (get_class($obj) == 'Resource' or get_class($obj) == 'Contact' or get_class($obj) == 'User') {
    $idProj=null;
    $idRess=$obj->id;
  } else {
    $idProj=null;
    $idRess=null;
  }
  
  if (!$print) {
    echo '<td class="assignHeader" style="width:15%">';
    if ($obj->id != null and !$print and $canCreate and !$obj->idle) {
      echo '<a onClick="addAffectation(\'' . get_class($obj) . '\',\'' . $type . '\',\'' . $idRess . '\', \'' . $idProj . '\');" title="' . i18n('addAffectation') . '" /> '
        .formatSmallButton('Add').'</a>';
    }
    echo '</td>';
  }
  echo '<td class="assignHeader" style="width:8%">' . i18n('colId') . '</td>';
  echo '<td class="assignHeader" style="width:' . (($print)?'35':'20') . '%">' . i18n('colId' . $type) . '</td>';
  echo '<td class="assignHeader" style="width:18%">' . i18n('colIdProfile') . '</td>';
  echo '<td class="assignHeader" style="width:13%">' . i18n('colStartDate') . '</td>';
  echo '<td class="assignHeader" style="width:13%">' . i18n('colEndDate') . '</td>';
  echo '<td class="assignHeader" style="width:12%">' . i18n('colRate') . '</td>';
  // echo '<td class="assignHeader" style="width:10%">' . i18n('colIdle'). '</td>';
  
  echo '</tr>';
  foreach ( $list as $aff ) {
    $canUpdate=securityGetAccessRightYesNo('menuAffectation', 'update', $aff) == "YES";
    $canDelete=securityGetAccessRightYesNo('menuAffectation', 'delete', $aff) == "YES";
    if (! (securityGetAccessRightYesNo('menu'.get_class($obj), 'update', $obj) == "YES") ) {
      $canCreate=false;
      $canUpdate=false;
      $canDelete=false;
    }
    if ($obj->idle == 1) {
      $canUpdate=false;
      $canCreate=false;
      $canDelete=false;
    }
    $idleClass=($aff->idle or ($aff->endDate and $aff->endDate < $dateNow = date("Y-m-d")) )?' affectationIdleClass':'';
    $res=new Resource($aff->idResource);
    $isResource=($res->id)?true:false;
    $goto="";
    if ($type == 'Project') {
      $name=SqlList::getNameFromId($type, $aff->idProject);
      if (!$print and securityCheckDisplayMenu(null, 'Project') and securityGetAccessRightYesNo('menuProject', 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\'Project\',\'' . htmlEncode($aff->idProject) . '\');" style="cursor: pointer;" ';
      }
    } else {
      $name=SqlList::getNameFromId($type, $aff->idResource);
      $typeAffectable=$type;
      if ($aff->idResource == $name and $type == 'Resource') {
        $name=SqlList::getNameFromId('User', $aff->idResource);
        $typeAffectable='User';
        if ($aff->idResource != $name and trim($name)) {
          $name.=" (" . i18n('User') . ")";
        }
      }
      if (!$print and securityCheckDisplayMenu(null, $typeAffectable) and securityGetAccessRightYesNo('menu'.$typeAffectable, 'read', '') == "YES") {
        $goto=' onClick="gotoElement(\''.$typeAffectable.'\',\'' . htmlEncode($aff->idResource) . '\');" style="cursor: pointer;" ';
      }
    }
    if ($aff->idResource != $name and trim($name)) {
      echo '<tr>';
      if (!$print) {
        echo '<td class="assignData' . $idleClass . '" style="text-align:center;white-space: nowrap;">';
        if ($canUpdate and !$print) {
          echo '  <a onClick="editAffectation(' . "'" . htmlEncode($aff->id) . "'" . ",'" . get_class($obj) . "'" . ",'" . $type . "'" . ",'" . htmlEncode($aff->idResource) . "'" . ",'" . htmlEncode($aff->idProject) . "'" . ",'" . htmlEncode($aff->rate) . "'" . ",'" . htmlEncode($aff->idle) . "'" . ",'" .
               $aff->startDate . "'" . ",'" . htmlEncode($aff->endDate) . "'" . ',' . htmlEncode($aff->idProfile) . ');" ' . 
             'title="' . i18n('editAffectation') . '" > '.formatSmallButton('Edit').'</a>';
        }
        if ($canDelete and !$print) {
          echo '  <a onClick="removeAffectation(' . "'" . htmlEncode($aff->id) . "'" . ','.(($aff->idResource==getSessionUser()->id)?'1':'0').');" ' . 
              'title="' . i18n('removeAffectation') . '" > '.formatSmallButton('Remove').'</a>';
        }
        if ($canUpdate and !$print and $isResource and !$aff->idle) {
          echo '  <a onClick="replaceAffectation(' . "'" . htmlEncode($aff->id) . "'" . ",'" . get_class($obj) . "'" . ",'" . $type . "'" . ",'" . htmlEncode($aff->idResource) . "'" . ",'" . htmlEncode($aff->idProject) . "'" . ",'" . htmlEncode($aff->rate) . "'" . ",'" . htmlEncode($aff->idle) . "'" . ",'" .
              $aff->startDate . "'" . ",'" . htmlEncode($aff->endDate) . "'" . ',' . htmlEncode($aff->idProfile) . ');" ' . 
              'title="' . i18n('replaceAffectation') . '" > '.formatSmallButton('SwitchUser').'</a>';
        } else {
          if ($aff->idle) {
             echo '<a><div style="display:table-cell;width:20px;"><img style="position:relative;top:4px;left:2px" src="css/images/tabClose.gif" ' . 'title="' . i18n('colIdle') . '"/></div></a>';
          } else {
            echo '<a><div style="display:table-cell;width:20px;">&nbsp;</div></a>';
          }
        }
        
        echo '</td>';
      } 
      echo '<td class="assignData' . $idleClass . '" align="center">' . htmlEncode($aff->id) . '</td>';
      /*if ($idProj) {
        echo '<td class="assignData' . $idleClass . '" align="left"' . $goto . '>' . htmlEncode($name) . '</td>';
      } else {
        echo '<td class="assignData' . $idleClass . '" align="left"' . $goto . '>' . htmlEncode($name) . '</td>';
      }*/
      echo '<td class="assignData' . $idleClass . '" align="left"' . $goto . '>';
      if ($aff->description and !$print) {
        echo formatCommentThumb(htmlTransformRichtextToPlaintext($aff->description));
      }
      echo htmlEncode($name);
      echo '</td>';
      echo '<td class="assignData' . $idleClass . '" align="center" >' . SqlList::getNameFromId('Profile', $aff->idProfile, true) . '</td>';
      echo '<td class="assignData' . $idleClass . '" align="center" style="white-space: nowrap;">' . htmlFormatDate($aff->startDate) . '</td>';
      echo '<td class="assignData' . $idleClass . '" align="center" style="white-space: nowrap;">' . htmlFormatDate($aff->endDate) . '</td>';
      echo '<td class="assignData' . $idleClass . '" align="center" style="white-space: nowrap;">' . htmlEncode($aff->rate) . '</td>';
      // echo '<td class="assignData" align="center"><img src="../view/img/checked' . (($aff->idle)?'OK':'KO') . '.png" /></td>';
      echo '</tr>';
    }
  }
  echo '</table></td></tr>';
  echo '</table>';
}

function drawTestCaseRunFromObject($list, $obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale, $comboDetail;
  if ($comboDetail) {
    return;
  }
  $class=get_class($obj);
  $otherClass=($class == 'TestCase')?'TestSession':'TestCase';
  $nameWidth=67;
  $canCreate=securityGetAccessRightYesNo('menu' . $class, 'update', $obj) == "YES";
  $canUpdate=$canCreate;
  $canDelete=$canCreate;
  if ($obj->idle == 1) {
    $canUpdate=false;
    $canCreate=false;
    $canDelete=false;
  }
  usort($list, "TestCaseRun::sort");
  echo '<tr><td colspan="2" style="width:100%;">';
  echo '<table style="width:100%;">';
  echo '<tr>';
  if (!$print and $class == 'TestSession') {
    $nameWidth-=10;
    echo '<td class="assignHeader" style="width:10%;">';
    if ($obj->id != null and !$print and $canCreate and !$obj->idle) {
      echo '<a onClick="addTestCaseRun();" title="' . i18n('addTestCaseRun') . '" > '.formatSmallButton('Add').'</a>';
    }
    echo '</td>';
    // also count colDetail size
    $nameWidth-=10;
  }
  echo '<td class="assignHeader" colspan="4" style="width:' . ($nameWidth + 15) . '%">' . i18n('col' . $otherClass) . '</td>';
  if (!$print and $class == 'TestSession') {
    echo '<td class="assignHeader" style="width:10%">' . i18n('colDetail') . '</td>';
  }
  echo '<td class="assignHeader" colspan="2" style="width:15%">' . i18n('colIdStatus') . '</td>'; 
  echo '</tr>';
  foreach ( $list as $tcr ) {
    if ($otherClass == 'TestCase') {
      $tc=new TestCase($tcr->idTestCase);
    } else {
      $tc=new TestSession($tcr->idTestSession);
    }
    $st=new RunStatus($tcr->idRunStatus);
    echo '<tr>';
    if (!$print and $class == 'TestSession') {
      echo '<td class="assignData" style="width:10%;text-align:center;">';
      echo '<table style="width:100%"><tr><td style="width:30%;white-space:nowrap;">';
      if ($canUpdate and !$print) {
        echo '  <a onClick="editTestCaseRun(' . "'" . htmlEncode($tcr->id) . "'" . ",'" . htmlEncode($tcr->idTestCase) . "'" . ",'" . htmlEncode($tcr->idRunStatus) . "'" . ",'" . htmlEncode($tcr->idTicket) . "'" . ');" ' 
      . 'title="' . i18n('editTestCaseRun') . '" > '.formatSmallButton('Edit').'</a>';
      }
      if ($canDelete and !$print) {
        echo '  <a onClick="removeTestCaseRun(' . "'" . htmlEncode($tcr->id) . "'" . ",'" . htmlEncode($tcr->idTestCase) . "'" . ');" ' 
      . 'title="' . i18n('removeTestCaseRun') . '" > '.formatSmallButton('Remove').'</a>';
      }
      if (!$print) {
        echo '<input type="hidden" id="comment_' . htmlEncode($tcr->id) . '" value="' . htmlEncode($tcr->comment, 'none') . '"/>';
      }
      echo '</td><td>&nbsp;&nbsp;&nbsp;</td><td style="white-space:nowrap;">';
      if ($tcr->idRunStatus == 1 or $tcr->idRunStatus == 3 or $tcr->idRunStatus == 4) {
        echo '  <a onClick="passedTestCaseRun(' . "'" . htmlEncode($tcr->id) . "'" . ",'" . htmlEncode($tcr->idTestCase) . "'" . ",'" . htmlEncode($tcr->idRunStatus) . "'" . ",'" . htmlEncode($tcr->idTicket) . "'" . ');" ' 
        . 'title="' . i18n('passedTestCaseRun') . '" /> '.formatSmallButton('Passed').'</a>';
      }
      if ($tcr->idRunStatus == 1 or $tcr->idRunStatus == 4) {
        echo '  <a onClick="failedTestCaseRun(' . "'" . htmlEncode($tcr->id) . "'" . ",'" . htmlEncode($tcr->idTestCase) . "'" . ",'" . htmlEncode($tcr->idRunStatus) . "'" . ",'" . htmlEncode($tcr->idTicket) . "'" . ');" ' 
      . 'title="' . i18n('failedTestCaseRun') . '" > '.formatSmallButton('Failed').'</a>';
      }
      if ($tcr->idRunStatus == 1 or $tcr->idRunStatus == 3) {
        echo '  <a onClick="blockedTestCaseRun(' . "'" . htmlEncode($tcr->id) . "'" . ",'" . htmlEncode($tcr->idTestCase) . "'" . ",'" . htmlEncode($tcr->idRunStatus) . "'" . ",'" . htmlEncode($tcr->idTicket) . "'" . ');" ' 
            . 'title="' . i18n('blockedTestCaseRun') . '" > '.formatSmallButton('Blocked').'</a>';
      }
      echo '</td></tr></table>';
      echo '</td>';
    }
    $goto="";
    if (!$print and securityCheckDisplayMenu(null, 'TestCase') and securityGetAccessRightYesNo('menuTestCase', 'read', $tc) == "YES") {
      $goto=' onClick="gotoElement(\'' . $otherClass . '\',\'' . htmlEncode($tc->id) . '\');" style="cursor: pointer;" ';
    }
    $typeClass='id' . $otherClass . 'Type';
    echo '<td class="assignData" align="center" style="width:3%">' . htmlEncode($tcr->sortOrder) . '</td>';
    echo '<td class="assignData" align="center" style="width:10%">' . htmlEncode(SqlList::getNameFromId($otherClass . 'Type', $tc->$typeClass)) . '</td>';    
    echo '<td class="assignData" align="center" style="width:5%">#' . htmlEncode($tc->id) . '</td>';
    echo '<td class="assignData" align="left"' . $goto . ' style="width:' . $nameWidth . '%" >' . htmlEncode($tc->name);
    if ($tcr->comment and !$print) {
      echo formatCommentThumb($tcr->comment);
    }
    echo '</td>';
    if (!$print and $class == 'TestSession') {
      echo '<td class="assignData" style="width:10%" align="center">';
      if ($tc->description) {
        echo '<img src="../view/css/images/description.png" title="' . i18n('colDescription') . ":\n\n" . htmlEncode($tc->description) . '" alt="desc" />';
        echo '&nbsp;';
      }
      if ($tc->result) {
        echo '<img src="../view/css/images/result.png" title="' . i18n('colExpectedResult') . ":\n\n" . htmlEncode($tc->result) . '" alt="desc" />';
        echo '&nbsp;';
      }
      if (isset($tc->prerequisite) and $tc->prerequisite) {
        echo '<img src="../view/css/images/prerequisite.png" title="' . i18n('colPrerequisite') . ":\n\n" . htmlEncode($tc->prerequisite) . '" alt="desc" />';
      }
      echo '</td>';
    }
    echo '<td class="assignData" style="width:8%;text-align:left;border-right:0px;">';
    echo colorNameFormatter(i18n($st->name) . '#split#' . $st->color);
    echo '</td>';
    echo '<td class="assignData" style="width:7%;border-left:0px;font-size:' . (($tcr->idTicket and $tcr->idRunStatus == '3')?'100':'80') . '%; text-align: center;">';
    if ($tcr->idTicket and $tcr->idRunStatus == '3') {
      echo i18n('Ticket') . ' #' . $tcr->idTicket;
    } else if ($tcr->statusDateTime) {
      echo ' <i>(' . htmlFormatDateTime($tcr->statusDateTime, false) . ')</i> ';
    }
    echo '</td>';
    echo '</tr>';
  }
  echo '</table>';
  echo '</td></tr>';
}

function drawOtherVersionFromObject($otherVersion, $obj, $type) {
  global $print;
  usort($otherVersion, "OtherVersion::sort");
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (!$otherVersion or count($otherVersion) == 0)
    return;
  echo '<table>';
  foreach ( $otherVersion as $vers ) {
    if ($vers->id) {
      echo '<tr>';
      if ($obj->id and $canUpdate and !$print) {
        echo '<td style="width:20px">';
        echo '<a onClick="removeOtherVersion(' . "'" . htmlEncode($vers->id) . "'" . ', \'' . SqlList::getNameFromId('Version', $vers->idVersion) . '\'' . ', \'' . htmlEncode($vers->scope) . '\'' . ');" ' . 
        'title="' . i18n('otherVersionDelete') . '" > '.formatSmallButton('Remove').'</a>';
        echo '</td>';
        echo '<td style="width:20px">';
        echo '<a onClick="swicthOtherVersionToMain(' . "'" . htmlEncode($vers->id) . "'" . ', \'' . SqlList::getNameFromId('Version', $vers->idVersion) . '\'' . ', \'' . htmlEncode($vers->scope) . '\'' . ');" ' . 
        'title="' . i18n('otherVersionSetMain') . '" > '.formatSmallButton('Switch').'</a>';
        echo '</td>';
      }
      echo '<td>' . htmlEncode(SqlList::getNameFromId('Version', $vers->idVersion)) . '</td>';
      echo '</tr>';
    }
  }
  echo '</table>';
}

function drawChecklistFromObject($obj) {
  global $print, $noselect,$collapsedList,$displayWidth, $printWidth, $profile;
  if (!$obj or !$obj->id) return; // Don't try and display checklist for non existant objects
  $displayChecklist='NO';
  $crit="nameChecklistable='".get_class($obj)."'";
  $type='id'.get_class($obj).'Type';
  if (property_exists($obj,$type) ) {
    $crit.=' and (idType is null ';
    if ($obj->$type) {
      $crit.=" or idType='".$obj->$type."'";
    }
    $crit.=')';
  }
  $cd=new ChecklistDefinition();
  $cdList=$cd->getSqlElementsFromCriteria(null,false,$crit);
  if (count($cdList)==0) return; // Don't display checklist if non definition exist for it
  $user=getSessionUser();
  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile'=>$profile,'scope'=>'checklist'));
  $list=new ListYesNo($habil->rightAccess);
  $displayChecklist=Parameter::getUserParameter('displayChecklist');
  if (! $displayChecklist) $displayChecklist='YES';
  if (!$noselect and $obj->id and $list->code=='YES' and ($displayChecklist=='YES' or $print) ) {
      if ($print) {
        //echo '<table class="detail" width="'.$printWidth.'px;">';
        //echo '<tr><td>';
        include_once "../tool/dynamicDialogChecklist.php";
        //echo '</td></tr>';
        //echo '</table>';
      } else {
        $titlePane=get_class($obj) . "_checklist";
        echo '<div style="width:'.$displayWidth.'" dojoType="dijit.TitlePane"'; 
        echo ' title="'.i18n('sectionChecklist').'"';
        echo ' open="'.((array_key_exists($titlePane, $collapsedList))?'false':'true').'"';
        echo ' id="'.$titlePane.'"';       
        echo ' onHide="saveCollapsed(\''.$titlePane.'\');"';
        echo ' onShow="saveExpanded(\''.$titlePane.'\');"';
        echo '>';
        include_once "../tool/dynamicDialogChecklist.php";
        echo '</div>';
      }
  }
}


function setWidthPct($displayWidth, $print, $printWidth, $obj,$colSpan=null) {
  //scriptLog("setWidthPct(displayWidth=$displayWidth, print=$print, printWidth=$printWidth, obj,colSpan=$colSpan)");
  if (intval($displayWidth)<=0 and intval($printWidth)>0) {
    $displayWidth=(intval($printWidth)-50).'px';
  }
  $nbCol=getNbColMax($displayWidth, $print, $printWidth, $obj);
  if ($print) {
    $nbCol=1;
  }
  $widthPct=round(99 / $nbCol) . "%";
  if ($nbCol == '1') {
    $widthPct=$displayWidth;
  }
  if (substr($displayWidth, -2, 2) == "px") {
    $val=substr($displayWidth, 0, strlen($displayWidth) - 2);
    $widthPct=floor( ($val / $nbCol) - ($nbCol+1)) . "px";
  }
  if ($colSpan and $nbCol>=$colSpan) {
    $widthPct=$colSpan*substr($widthPct, 0, strlen($widthPct) - 2)."px";
  }
  if ($print) {
		$widthPct = round ( ($printWidth / $nbCol) - 2 * ($nbCol - 1) ) . "px";
	}
	return $widthPct;
}

function getNbColMax($displayWidth, $print, $printWidth, $obj) {
  global $nbColMax;
  if ($displayWidth > 1380) {
    $nbColMax=3;
  } else if ($displayWidth > 900) {
    $nbColMax=2;
  } else {
    $nbColMax=1;
  }
  if (property_exists($obj, '_nbColMax')) {
    if ($nbColMax > $obj->_nbColMax) {
      $nbColMax=$obj->_nbColMax;
    }
  } else {
    if ($nbColMax > 2) {
      $nbColMax=2;
    }
  }
  $paramMax=Parameter::getUserParameter('maxColumns');
  if ($paramMax and $paramMax<$nbColMax) $nbColMax=$paramMax;
  return $nbColMax;
}

function startBuffering() {
  global $reorg,$leftPane,$rightPane,$extraPane,$bottomPane, $nbColMax,$section;
  if (!$reorg) return;
  ob_start();
}

function endBuffering() {
  global $reorg,$leftPane,$rightPane,$extraPane,$bottomPane, $nbColMax,$section;
  if (!$reorg) return;
  $display=ob_get_clean();
  echo $display; // firt test !!!
}
function finalizeBuffering() {
  global $reorg,$leftPane,$rightPane,$extraPane,$bottomPane, $nbColMax,$section;
  if (!$reorg) return;
  
}

function drawJobDefinitionFromObject($obj, $refresh=false) {
  global $cr, $print, $user, $browserLocale;
  $canUpdate=securityGetAccessRightYesNo('menu' . get_class($obj), 'update', $obj) == "YES";
  if ($obj->idle == 1) {
    $canUpdate=false;
  }
  if (isset($obj->_JobDefinition)) {
    $lines=$obj->_JobDefinition;
  } else {
    $lines=array();
  }
  echo '<input type="hidden" id="JoblistDefinitionIdle" value="' . $obj->idle . '" />';
  echo '<table width="100%">';
  echo '<tr>';
  if (!$print) {
    echo '<th class="noteHeader" style="width:5%">'; // changer le header
    if ($obj->id != null and !$print and $canUpdate) {
      echo '<img src="css/images/smallButtonAdd.png"' . ' onClick="addJobDefinition(' . $obj->id . ');"' . ' title="' . i18n('addLine') . '" class="roundedButtonSmall"/> ';
    }
    echo '</th>';
  }
  echo '<th class="noteHeader" style="width: 20%">' . i18n('colSortOrder') . '</th>';
  echo '<th class="noteHeader" style="width:'.(($print)?'60':'55').'%">' . i18n('colName') . '</th>';
  echo '<th class="noteHeader" style="width: 20%">' . i18n('colDaysBeforeWarning') . '</th>';
  echo '</tr>';

  usort($lines, "JobDefinition::sort");
  foreach ( $lines as $line ) {
    echo '<tr>';
    if (!$print) {
      echo '<td class="noteData" style="text-align:center;">';
      if ($canUpdate) {
        echo ' <img src="css/images/smallButtonEdit.png"' . ' onClick="editJobDefinition(' . $obj->id . ',' . $line->id . ');"' . ' title="' . i18n('editLine') . '" class="roundedButtonSmall"/> ';
        echo ' <img src="css/images/smallButtonRemove.png"' . ' onClick="removeJobDefinition(' . $line->id . ');"' . ' title="' . i18n('removeLine') . '" class="roundedButtonSmall"/> ';
      }
      echo '</td>';
    }
    echo '<td class="noteData" title="' . $line->title . '">' . $line->sortOrder . '</td>';
    echo '<td class="noteData" title="' . $line->title . '">';
    echo "<table><tr><td>".htmlDisplayCheckbox(0) . "&nbsp;</td><td valign='top'>" . htmlEncode($line->name) . "</td></tr></table>";
    echo '</td>';
    echo '<td class="noteData">';
    echo $line->daysBeforeWarning . ' d';
    echo '</td>';
    echo '</tr>';
  }
  echo '<tr>';
  if (!$print) {
    echo '<td class="noteDataClosetable">&nbsp;</td>';
  }
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '<td class="noteDataClosetable">&nbsp;</td>';
  echo '</tr>';
  echo '</table>';
}

function drawJoblistFromObject($obj) {
  global $print, $noselect,$collapsedList,$displayWidth, $printWidth, $profile;
  if (!$obj or !$obj->id) return; // Don't try and display joblist for non existant objects
  $crit="nameChecklistable='".get_class($obj)."'";
  $type='id'.get_class($obj).'Type';
  if (property_exists($obj,$type) ) {
    $crit.=' and (idType is null ';
    if ($obj->$type) {
      $crit.=" or idType='".$obj->$type."'";
    }
    $crit.=')';
  }
  $cd=new JoblistDefinition();
  $cdList=$cd->getSqlElementsFromCriteria(null,false,$crit);
  if (count($cdList)==0) return; // Don't display joblist if non definition exist for it
  $user=getSessionUser();
  $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', array('idProfile'=>$profile,'scope'=>'joblist'));
  $list=new ListYesNo($habil->rightAccess);
  if (!$noselect and $obj->id and $list->code=='YES') {
      if ($print) {
        //echo '<table class="detail" width="'.$printWidth.'px;">';
        //echo '<tr><td>';
        include_once "../tool/dynamicDialogJoblist.php";
        //echo '</td></tr>';
        //echo '</table>';
      } else {
        $titlePane=get_class($obj) . "_joblist";
        echo '<div style="width:'.$displayWidth.'" dojoType="dijit.TitlePane"';
        echo ' title="'.i18n('sectionJoblist').'"';
        echo ' open="'.((array_key_exists($titlePane, $collapsedList))?'false':'true').'"';
        echo ' id="'.$titlePane.'"';
        echo ' onHide="saveCollapsed(\''.$titlePane.'\');"';
        echo ' onShow="saveExpanded(\''.$titlePane.'\');"';
        echo '>';
        include_once "../tool/dynamicDialogJoblist.php";
        echo '</div>';
      }
  }
}
?>