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

/** ===========================================================================
 * Html specific functions
 */
require_once "../tool/projeqtor.php";
//DO NOT SCRIPT LOG : html.php is included in projeqtor.php, so for each script 
//scriptLog('   ->/tool/html.php');
/** ===========================================================================
 * Draw the options list for a select  
 * @param $col the name of the field, as idXxx. The table ref is then xxx.
 * @param $selection the value of the field, to be selected in the list
 * @param $obj optional - object for which list is generated
 * @param $required optional - indicates wether the list may present an empty value or not
 * @return void
 */
function htmlDrawOptionForReference($col, $selection, $obj=null, $required=false, $critFld=null, $critVal=null, $limitToActiveProjects=true) {
	//scriptLog("      =>htmlDrawOptionForReference(col=$col,selection=$selection,object=" . (($obj)?get_class($obj).'#'.$obj->id:'null' ).",required=$required,critFld=$critFld,critval=$critVal)");
  if (is_array($critFld)) {
	  foreach ($critFld as $tempId=>$tempCrt) {
	    $crtName='critFld'.$tempId;
	    $$crtName=$tempCrt;
	  }
	  $critFld=$critFld[0];
	}
	if (is_array($critVal)) {
	  foreach ($critVal as $tempId=>$tempVal) {
	    $valName='critVal'.$tempId;
	    $$valName=$tempVal;
	  }
	  $critVal=$critVal[0];
	}
  if ($col=='planning') {
    $listType='Project';
  } else {
    $listType=substr($col,2);
  }
  if ($obj and $col=='id'.get_class($obj).'Type') {
    if ($critFld and $critVal) {
      $$critFld=$critVal;
    }
    $critFld=null;$critVal=null;
  }
	$column='name';
	$user=getSessionUser();
	if ($listType=='DocumentDirectory') {
		$column='location';
	}	
  if (($col=='idVersion' or $col=='idProductVersion' or $col=='idComponentVersion') and ($critFld=='idProductOrComponent')) {
    $critFld='idProduct';
  }
  if ($col=='idResource' and $critFld=='idProject') {
  	$prj=new Project($critVal, true);
    $lstTopPrj=$prj->getTopProjectList(true);
    $in=transformValueListIntoInClause($lstTopPrj);
    $where="idProject in " . $in; 
    $aff=new Affectation();
    $list=$aff->getSqlElementsFromCriteria(null,null, $where);
    $nbRows=0;
    $table=array();
    if ($selection) {
       $table[$selection]=SqlList::getNameFromId('Affectable', $selection);
    }
    foreach ($list as $aff) {
      if (! array_key_exists($aff->idResource, $table)) {
        $id=$aff->idResource;
        $name=SqlList::getNameFromId('Resource', $id);
        //if ($name==$id and $col=='idResource') { // PBE V6.0 : this would insert users in Reosurce list (for instance responsible on Ticket)
        //	$name=SqlList::getNameFromId('User', $id);
        //}
        if ($name!=$id) {
          $table[$id]=$name;
        } 
      }
    }
    asort($table);
  } else if ($critFld and ($col=='idProductVersion' or $col=='idComponentVersion') and ($critFld=='idVersion' or $critFld=='idComponentVersion' or $critFld=='idProductVersion') ) {
    $critClass=substr($critFld,2);
    $versionField=str_replace('Version', '', $critFld);
    $version=new Version($critVal,true);
    $critArray=array($versionField=>$version->idProduct);
    $list=SqlList::getListWithCrit('ProductStructure',$critArray,str_replace('Version', '',$col),$selection);
    $table=array();
    foreach ($list as $id) {
      $crit=array('idProduct'=>$id);
      $list=SqlList::getListWithCrit('Version',$crit);
      $table=array_merge_preserve_keys($table,$list);
    }  
    if ($selection) {
      $table[$selection]=SqlList::getNameFromId('Version', $selection);
    }
  } else if ($critFld and ! (($col=='idProduct' or $col=='idProductOrComponent' or $col=='idComponent') and $critFld=='idProject') ) {
    $critArray=array($critFld=>$critVal);
    $table=SqlList::getListWithCrit($listType,$critArray,$column,$selection);
    if ($selection) {
      $refTable=substr($col,2);
      if (substr($listType,-7)=='Version' and SqlElement::is_a($refTable, 'Version')) $refTable='Version';
      $table[$selection]=SqlList::getNameFromId($refTable, $selection);
    }
    if ($col=="idProject" or $col=="planning") { 
    	$wbsList=SqlList::getListWithCrit($listType,$critArray,'sortOrder',$selection);
    }

  } else if ($col=='idBill') {
    $crit=array('paymentDone'=>'0','done'=>'1');
    $table=SqlList::getListWithCrit($listType, $crit,$column,$selection, (! $obj)?!$limitToActiveProjects:false);
  }else if ($col=='idLinkable' || $col=='idCopyable'){
    $typeRight='read';
    if($col=='idCopyable')$typeRight='update';
    $table=SqlList::getListNotTranslated($listType,$column,$selection, (! $obj)?!$limitToActiveProjects:false );
    $arrayToDel=array();
    foreach($table as $key => $val){
      $objTmp=new $val();
      if(property_exists($objTmp, "idProject") && $obj && property_exists($obj, "idProject")){
        $objTmp->idProject=$obj->idProject;
      }
      if(securityGetAccessRightYesNo('menu'.$val, $typeRight, $objTmp)=="NO" or !securityCheckDisplayMenu(null,$val))$arrayToDel[]=$key;
    }
    $table=SqlList::getList($listType,$column,$selection, (! $obj)?!$limitToActiveProjects:false );
    foreach($arrayToDel as $key)unset($table[$key]);
  } else {
    $table=SqlList::getList($listType,$column,$selection, (! $obj)?!$limitToActiveProjects:false );
    if ($col=="idProject" or $col=="planning") { 
    	$wbsList=SqlList::getList($listType,'sortOrder',$selection, (! $obj)?!$limitToActiveProjects:false );
    } 
    if ($selection) {
      $refTable=$listType;
      if (substr($listType,-7)=='Version' and SqlElement::is_a($refTable, 'Version')) $refTable='Version';
      $table[$selection]=SqlList::getNameFromId($refTable, $selection);
    } 
  }
  $restrictArray=array();
  $excludeArray=array();
  if ($obj) {
  	$class=get_class($obj);
    if ( $class=='Project' and $col=="idProject" and $obj->id!=null) { // on "is sub-project of", remove subproject and current project
      $excludeArray=$obj->getRecursiveSubProjectsFlatList();
      $excludeArray[$obj->id]=$obj->name;
    } 
    if ($col=="idProject") {
    	$menuClass=$obj->getMenuClass();
    	if ($class=='DocumentDirectory') {
    		$doc=new Document();
    		$menuClass=$doc->getMenuClass();
    	}
      $controlRightsTable=$user->getAccessControlRights($obj);
      if (! array_key_exists($menuClass,$controlRightsTable)) {
	      // If AccessRight notdefined for object and user profile => empty list + log error
	      traceLog('error in htmlDrawOptionForReference : no control rights for ' . $class);
        return;		
	    }
      $controlRights=$controlRightsTable[$menuClass];    
      if ($obj->id==null) {
        // creation mode
        if ($controlRights["create"]!="ALL") {         
          $restrictArray=$user->getVisibleProjects();
          if (count($restrictArray)==0) { // If user is affected to no project, only possible value is 0 (never users)
            $restrictArray[0]=0;
          }
        }
      } else {
        // read or update mode
        if (securityGetAccessRightYesNo($menuClass, 'update', $obj)=="YES") {
          // update
          if ($controlRights["update"]=="PRO" or $controlRights["update"]=="OWN" or $controlRights["update"]=="RES") {
            $restrictArray=$user->getVisibleProjects();
          }            
        }
      }
      if (count($restrictArray) and $controlRights["create"]!="ALL") {
        foreach ($restrictArray as $idP=>$nameP) {
            $tmpAccessRight="NO";
            $tmpAccessRightList = $user->getAccessControlRights($idP);
            if (array_key_exists ( $menuClass, $tmpAccessRightList )) {
              $tmpAccessRightObj = $tmpAccessRightList [$menuClass];
              if (array_key_exists ( 'create', $tmpAccessRightObj )) {
                $tmpAccessRight = $tmpAccessRightObj ['create'];
              }
            }
            if ($tmpAccessRight!='ALL' and $tmpAccessRight!='PRO') {
              unset($restrictArray[$idP]);
            }
        }
        if (count($restrictArray)==0) { 
          $restrictArray[0]=0;
        }
      }
    } else if ($col=='idStatus') {
    	if ($class=='TicketSimple') $class='Ticket';        
      $idType='id' . $class . 'Type';
      $typeClass=$class . 'Type';
      if (property_exists($obj,$idType) ) {
      	reset($table);
        $fisrtKey=key($table);
        $firstName=current($table);
        // look for workflow
        if ($obj->$idType and $obj->idStatus) {
          $profile="";
          if (sessionUserExists()) {
            $profile=getSessionUser()->getProfile($obj);
          } 
          $type=new $typeClass($obj->$idType,true);
          if (property_exists($type,'idWorkflow') ) {
            $ws=new WorkflowStatus();
            $crit=array('idWorkflow'=>$type->idWorkflow, 'allowed'=>1, 'idProfile'=>$profile, 'idStatusFrom'=>$obj->idStatus);
            $wsList=$ws->getSqlElementsFromCriteria($crit, false);
            $compTable=array($obj->idStatus=>'ok');
            foreach ($wsList as $ws) {
              $compTable[$ws->idStatusTo]="ok";
            }
            $table=array_intersect_key($table,$compTable);
          }
        } else {
           $table=array($fisrtKey=>$firstName);
        }
      }
      if ($selection) {
        $selStatus=new Status($selection,true);
        if ($selStatus->isCopyStatus) {
        	$table[$fisrtKey]=$firstName;
        }
      }
    } else if (($col=='idProduct' or $col=='idComponent' or  $col=='idProductOrComponent') and $critFld=='idProject' and $critVal) {
    	$restrictArray=array();
    	$versProj=new VersionProject();
    	$proj=new Project($critVal,true);
    	$lst=$proj->getTopProjectList(true);
    	$inClause='(0';
    	foreach ($lst as $prj) {
    	  if ($prj) {
    	    $inClause.=',';
    	    $inClause.=$prj;
    	  }
    	}
    	$inClause.=')';
    	$versProjList=$versProj->getSqlElementsFromCriteria(null, false, 'idProject in '.$inClause);
    	if (count($versProjList)==0) $table=array();
    	foreach ($versProjList as $versProj) {
    		$vers=new Version($versProj->idVersion,true);
    		$restrictArray[$vers->idProduct]="OK";
    	}
    	if ($selection) {
    	  $table[$selection]=SqlList::getNameFromId(substr($col,2), $selection);
    	}
    	if (isset($restrictArray[$selection])) unset($restrictArray[$selection]);
    } else if ($col=='idComponent' and $critFld=='idProduct' and $critVal) {
      $prod=new Product($critVal,true);
      $table=$prod->getComposition(true,true);
      if ($selection) {
        $table[$selection]=SqlList::getNameFromId(substr($col,2), $selection);
      }
    } else if (substr($col,-16)=='ComponentVersion' and $critFld=='idProductVersion' and $critVal) {
      $prodVers=new ProductVersion($critVal,true);
      $table=$prodVers->getComposition(true,true);
      if (isset($critFld1) and isset($critVal1) and $critFld1=='idComponent') {
        $listVers=SqlList::getListWithCrit('ComponentVersion', array('idComponent'=>$critVal1));
        $table=array_intersect_assoc($table,$listVers);
      }
      if ($selection) {
        $table[$selection]=SqlList::getNameFromId('ComponentVersion', $selection);
      }
    } else if ($col=='id'.$class.'Type' and property_exists($obj, 'idProject')) {
      if (! isset($idProject)) {
        if ($obj->idProject and $class!='Project' ) {
          $idProject=$obj->idProject;
        } else {
          $idProject=0;
        }
      }
      if ($class=='Project') {
        if ($obj and $obj->id) $idProject=$obj->id;
        else $idProject=null;
      }
      $critFld=null;$critVal=null;
      $rtListProjectType=Type::listRestritedTypesForClass($class.'Type',$idProject, null,null);
      if (count($rtListProjectType)) {
        foreach($rtListProjectType as $id=>$idType) {
          $restrictArray[$idType]="OK";
        }
        if ($selection) {$restrictArray[$selection]="OK";}
      }
    }
  } else { // (! $obj)
  	if ($col=="idProject") {
      $user=getSessionUser();
      if (! $user->_accessControlVisibility) {
        $user->getAccessControlRights(); // Force setup of accessControlVisibility
      }      
      if ($user->_accessControlVisibility != 'ALL') {
      	$restrictArray=$user->getVisibleProjects($limitToActiveProjects);
  	  }
    } else if ($col=="planning") {
      $user=getSessionUser();
      $restrictArray=$user->getListOfPlannableProjects();
    } else if (($col=="idProduct" or $col=="idProductOrComponent" or $col=="idComponent") and $critFld=='idProject') {
   		echo '<option value=" " ></option>';
    	return ;
    }
  }
  if ($col=='idResource' and Affectable::getVisibilityScope()!="all") {
    $restrictArray=array();
    $res=new Resource();
    $scope=Affectable::getVisibilityScope();
    if ($scope=='orga') {
      $crit="idOrganization in (". Organization::getUserOrganisationList().")";
    } else if ($scope=='team') {
      $aff=new Affectable(getSessionUser()->id,true);
      $crit="idTeam='$aff->idTeam'";
    } else {
      traceLog("Error on htmlDrawOptionForReference() : Resource::getVisibilityScope returned something different from 'all', 'team', 'orga'");
      $crit=array('id'=>'0');
    }
    $list=$res->getSqlElementsFromCriteria(null,false,$crit);
    foreach ($list as $res) {
      $restrictArray[$res->id]=$res->name;
    }
    if ($selection) $restrictArray[$selection]="OK";
  }
  if (! $required) {
    echo '<option value=" " ></option>';
  }
  if ($selection and $col=='idResource' and (! isset($table[$selection]) or $table[$selection]==$selection) ) {
    $table[$selection]=SqlList::getNameFromId('Affectable', $selection);
  }
  if ($listType=='Linkable' or $listType=='Copyable' or $listType=='Importable' or $listType=='Mailable'
   or $listType=='Indicatorable' or $listType=='Checklistable' or $listType=='Dependable' or $listType=='Originable'
   or $listType=='Referencable') {
    asort($table);
  }
  if ($col=="idProject") {
    $sepChar=Parameter::getUserParameter('projectIndentChar');
    if (!$sepChar) $sepChar='__';
    $wbsLevelArray=array();
  }
  $pluginObjectClass=substr($col,2);
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  if (! $obj) $sepChar='no';
  $selectedFound=false;
  $next="";
  if (isset($table['*'])) unset($table['*']);
  foreach($table as $key => $val) {
    if (! array_key_exists($key, $excludeArray) and ( count($restrictArray)==0 or array_key_exists($key, $restrictArray) or $key==$selection) ) {
      if ($col=="idProject" and $sepChar!='no') {   
        $wbs=$wbsList[$key];
        $wbsTest=$wbs;
        $level=1;
        while (strlen($wbsTest)>3) {
          $wbsTest=substr($wbsTest,0,strlen($wbsTest)-4);
          if (array_key_exists($wbsTest, $wbsLevelArray)) {
            $level=$wbsLevelArray[$wbsTest]+1;
            $wbsTest="";
          }
        }
        $wbsLevelArray[$wbs]=$level;
        $sep='';for ($i=1; $i<$level;$i++) {$sep.=$sepChar;}
        $val = $sep.$val;
      }
      if ($col=='idResource') {
      	if ($key==$user->id) {
      		$next=$key;
      	}
      } else if ($selectedFound) {
      	$selectedFound=false;
      	$next=$key;
      }
      echo '<option value="' . $key . '"';
      if ( $selection and $key==$selection ) { 
      	echo ' SELECTED ';
      	$selectedFound=true; 
      } 
      echo '><span >'. htmlEncode($val) . '</span></option>';
    }
  }
  // This function is not expected to return value, but is used to return next value (for status)
  return $next;
}

function htmlReturnOptionForWeekdays($selection, $required=false) {
	$arrayWeekDay=array('1'=>'Monday', '2'=>'Tuesday', '3'=>'Wednesday', '4'=>'Thursday',
	                    '5'=>'Friday', '6'=>'Saturday', '7'=>'Sunday');
  $result="";
	if (! $required) {
    $result.='<option value=" " ></option>';
  }
  for ($key=1; $key<=7; $key++) {
    $result.= '<option value="' . $key . '"';
    if ( $selection and $key==$selection ) { $result.= ' SELECTED '; } 
    $result.= '>'. i18n($arrayWeekDay[$key]) . '</option>';
  }
  return $result;
}

function htmlReturnOptionForMonths($selection, $required=false) {
  $arrayMonth=array('1'=>'January', '2'=>'February', '3'=>'March', '4'=>'April',
                      '5'=>'May', '6'=>'June', '7'=>'July','8'=>'August',
                      '9'=>'September', '10'=>'October', '11'=>'November','12'=>'December');
  $result="";
  if (! $required) {
    $result.='<option value=" " ></option>';
  }
  for ($key=1; $key<=12; $key++) {
    $result.= '<option value="' . $key . '"';
    if ( $selection and $key==$selection ) { $result.= ' SELECTED '; } 
    $result.= '>'. i18n($arrayMonth[$key]) . '</option>';
  }
  return $result;
}
/** ===========================================================================
 * Display the info of the aplication (name, version) with link to website
 * @return void
 */
function htmlDisplayInfos() {
  global $copyright, $version, $website, $aboutMessage;
  echo "<a class='statusBar' target='#' href='$website' >$copyright $version&nbsp;</a>";
}

/** ===========================================================================
 * Display the info of the aplication (name, version) with link to website
 * @return void
 */
function htmlDisplayDatabaseInfos() {
  $paramDbName=Parameter::getGlobalParameter('paramDbName');
  $paramDbDisplayName=Parameter::getGlobalParameter('paramDbDisplayName');
  if (! $paramDbDisplayName) {
    $paramDbDisplayName=$paramDbName;
  }
  echo "<div style='text-align:center;'><b>$paramDbDisplayName</b></div>";
}

/** ===========================================================================
 * Display the message No object selected for the corresponding object,
 * translate using i18n
 * @param $className the class of the object
 * @return void
 */
function htmlGetNoDataMessage($className) {
    return '<br/><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . i18n('messageNoData',array(i18n($className))) . '</i>';
}

function htmlGetNoAccessMessage($className) {
	return '<br/><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . i18n('messageNoAccess',array(i18n($className))) . '</i>';
}

function htmlGetDeletedMessage($className) {
  return '<br/><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . i18n('messageDeleted',array(i18n($className))) . '</i>';
}


/** ===========================================================================
 * Draw an html Table as cross reference
 * @param $lineObj the object class containing line data
 * @param $columnTable the table containing column data
 * @param $pivotTable the table containing pivot data (must contain id'ColumnTable' and id'LineTable'
 * @param $pivotValue the name of the field in pivot table containing pivot data
 * @param $format the format of data : check, text, label
 * @return void
 */
function htmlDrawCrossTable($lineObj, $lineProp, $columnObj, $colProp, $pivotObj, $pivotProp, $format='label', $formatList=null, $break=null) {
  global $collapsedList;
	if (is_array($lineObj)) {
    $lineList=$lineObj;
  } else {
    $lineList=SqlList::getList($lineObj);
  }
  // Filter on line (for instance will filter menu)
  if (is_array($lineObj)) {
    $pluginObjectClass='';
  } else {
    $pluginObjectClass=ucfirst($lineObj);
  }
  $table=$lineList;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $lineList=$table;
  // Filter on columns (for instance will filter profile)
  $columnList=SqlList::getList($columnObj);
  $pluginObjectClass=ucfirst($columnObj);
  $table=$columnList;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $columnList=$table;
  echo '<div style="width:98%; overflow-x:auto;  overflow-y:hidden;">';
  if ( ! ($break and ! is_array($lineObj)) ) {
	  echo '<table class="crossTable" >';
	  // Draw Header
	  echo '<tr><td>&nbsp;</td>';
	  foreach ($columnList as $col) {
	    echo '<td class="tabLabel">' . $col . '</td>';
	  }
	  echo '</tr>';
  }
  $breakVal='';
  $breakNum=0;
  foreach($lineList as $lineId => $lineName) {
  	if ($break and ! is_array($lineObj)) {
  		$class=ucfirst($lineObj);
  		$test=new $class($lineId,true);
  		if ($test->$break != $breakVal) {
  			$breakNum++;
  			$breakClass=substr($break,2);
  			$breakObj=new $breakClass($test->$break,true);
  			$breakName="";
  			if ($breakObj->name) {
  			  $breakName=(property_exists($breakObj,'_isNameTranslatable'))?i18n($breakObj->name):$breakObj->name;
  			} 
  			//echo '<tr><td class="tabLabel" style="text-align:left;border-top:2px solid #A0A0A0;">' . $breakName  . '</td>';
  			if ($test->$break) {
  			  $breakCode=$breakObj->name;
  			} else {
  				$breakCode=$breakNum;
  			} 
  			echo '</table></div><br/>';
        $divName='CrossTable_'.$lineObj.'_'.$breakCode;
        echo '<div id="' . $divName . '" dojoType="dijit.TitlePane"';
        echo ' open="' . (array_key_exists($divName, $collapsedList)?'false':'true') . '"';
        echo ' onHide="saveCollapsed(\'' . $divName . '\');"';
        echo ' onShow="saveExpanded(\'' . $divName . '\');"';
        echo ' title="' .$breakName . '"';
        echo ' style="width:98%; overflow-x:auto;  overflow-y:hidden;"';
        echo '>';
        echo '<table class="crossTable">';
        echo '<tr><td>&nbsp;</td>';
			  foreach ($columnList as $col) {
			    echo '<td class="tabLabel">' . $col . '</td>';
			  }
			  echo '</tr>';
        echo '<tr>';  			
  		}
  		$breakVal=$test->$break;
  	}
    echo '<tr><td class="crossTableLine"><label class="label largeLabel">' . $lineName . '</label></td>';
    foreach ($columnList as $colId => $colName) {
      $crit=array();
      $crit[$lineProp]=$lineId;
      $crit[$colProp]=$colId;
      $name=$pivotObj . "_" . $lineId . "_" . $colId;
      $class=ucfirst($pivotObj);
      $obj=SqlElement::getSingleSqlElementFromCriteria($class, $crit);
      $val=$obj->$pivotProp;
      echo '<td class="crossTablePivot">';
      switch ($format) {
        case 'check':
          $checked = ($val!='0' and ! $val==null) ? 'checked' : '';
          echo '<input dojoType="dijit.form.CheckBox" type="checkbox" ' . $checked . ' id="' . $name . '" name="' . $name . '" />'; 
          break;
        case 'text':
          echo '<input dojoType="dijit.form.TextBox id="' . $name . '" name="' . $name . '" type="text" class="input" style="width: 100px;" value="' . $val . '" />';
          break;
        case 'list':
          //echo '<input dojoType="dijit.form.TextBox id="' . $name . '" name="' . $name . '" type="text" class="input" style="width: 100px;" value="' . $val . '" />';
          echo '<select dojoType="dijit.form.FilteringSelect" class="input" '; 
          echo autoOpenFilteringSelect();
          echo ' style="width: 100px; font-size: 80%;"';
          echo ' id="' . $name . '" name="' . $name . '" ';
          echo ' >';
          htmlDrawOptionForReference('id' . $formatList, $val, null, true); 
          echo '</select>';
          break;  
        case 'label':
          echo $val;
          break;
      }
      echo '</td>';
    }
    echo '</tr>';
  }
  
  
  echo '</table></div>';
  
}

/** ===========================================================================
 * Get the data of a form table designed with htmlDrawCrossTable
 * @param $lineTable the table containing line data
 * @param $columnTable the table containing column data
 * @param $pivotTable the table containing pivot data (must contain id'ColumnTable' and id'LineTable'
 * @param $pivotValue the name of the field in pivot table containing pivot data
 * @param $format the format of data : check, text, label
 * @return an array containing 
 */
function htmlGetCrossTable($lineObj, $columnObj, $pivotObj) {
  if (is_array($lineObj)) {
    $lineList=$lineObj;
  } else {
    $lineList=SqlList::getList($lineObj);
  }
  // Filter on line (for menu)
  if (! is_array($lineObj)) {
    $pluginObjectClass=ucfirst($lineObj);
    $table=$lineList;
    $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
    foreach ($lstPluginEvt as $script) {
      require $script; // execute code
    }
    $lineList=$table;
  }
  $columnList=SqlList::getList($columnObj);
  // Filter on columns (for profile)
  $pluginObjectClass=ucfirst($columnObj);
  $table=$columnList;
  $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
  foreach ($lstPluginEvt as $script) {
    require $script; // execute code
  }
  $columnList=$table;
  $result=array();
  foreach($lineList as $lineId => $lineName) {
    foreach ($columnList as $colId => $colName) {
      $name=$pivotObj . "_" . $lineId . "_" . $colId;
      $val="";
      if (array_key_exists($name,$_REQUEST)) {
        $val=$_REQUEST[$name];
      }
      // Note: this needs an in-depth security review - seems to allow arbitrary manipulations of values (including access rights) by calls to saveParameter.php
      // TODO (SECURITY) : check validity of returned values
      $result[$lineId][$colId]=$val; 
    }
  }
  return $result;
}

/** ===========================================================================
 * Construct a Js table from a Php table (got from a database table)
 * @param $tableName name of database table containing data
 * @param $colName column name co,ntaining requested data in table
 * @return javascript creating an array 
 */
function htmlGetJsTable($tableName, $colName, $jsTableName=null) {
  $tab=SqlList::getList($tableName,$colName);
  $jsTableName=(! $jsTableName) ? 'tab'.ucfirst($tableName):$jsTableName;
  $script='var ' . $jsTableName . ' = [ ';
  $nb=0;
  foreach ($tab as $id=>$value) {
    $script .= (++$nb>1) ? ', ': '';
    $script .= ' { id: "' . $id . '", ' . $colName . ': "' . $value . '" } ';
  }
  $script.= ' ];';
  return $script;
}

/**
 * Format a date, depending on currentLocale
 * @param $val
 * @return unknown_type
 */
function htmlFormatDate($val,$trunc=false) {
  global $browserLocaleDateFormat;
  if (strlen($val)!=10) {
  	if (strlen($val)==19) {
  		if ($trunc) {
  			$val=substr($val,0,10);
  		} else {
  		  return htmlFormatDateTime($val);
  		}
  	} else {
      return $val;
  	}
  }
  $year=substr($val,0,4);
  $month=substr($val,5,2);
  $day=substr($val,8,2);
  $result=str_replace('YYYY', $year, $browserLocaleDateFormat);
  $result=str_replace('MM', $month, $result);
  $result=str_replace('DD', $day, $result);
  $result=str_replace('YY', substr($year,2,2), $result);
  return $result;
}

/**
 * Format a dateTime, depending on currentLocale
 * @param $val
 * @return unknown_type
 */
function htmlFormatDateTime($val, $withSecond=true, $hideZeroTime=false) {
  global $browserLocale;
  $locale=substr($browserLocale, 0,2);
  if (strlen($val)!=19 and strlen($val)!=16) {
    if (strlen($val)=="10") {
      return htmlFormatDate($val);
    } else {
      return $val;
    }
  }
  $result=htmlFormatDate(substr($val,0,10));
  if (! $hideZeroTime or substr($val,11,5)!='00:00') {
    $result.= " " . (($withSecond)?substr($val,11):substr($val,11,5));
  }
  return $result;
}
function htmlFormatTime($val, $withSecond=true) {
  global $browserLocale;
  $locale=substr($browserLocale, 0,2);
  $result= (($withSecond)?$val:substr($val,0,5));
  return $result;
}
/** ============================================================================
 * Transform string to be displays in html, pedending on context 
 * @param $context Printing context : 
 *   'print' : for printing purpose, also converts nl to <br> 
 *   'default' : default for conversion
 *   'none' : no convertion
 * @return string - the formated value 
 */
function htmlEncode($val,$context="default") {
  if ($context=='none') {
    return str_replace('"',"''",$val);
  } else if ($context=='print' or $context=='html') {
    return nl2br(htmlentities($val,ENT_COMPAT,'UTF-8'));
  } else if ($context=='withBR') {
    return nl2br(htmlspecialchars($val,ENT_QUOTES,'UTF-8'));
  } else if ($context=='mail') {
    $str=$val;
    if (get_magic_quotes_gpc()) {
      $str=str_replace('\"','"',$str);
      $str=str_replace("\'","'",$str);
      $str=str_replace('\\\\','\\',$str);
    }
    return nl2br(htmlentities($str,ENT_QUOTES,'UTF-8'));
  } else if ($context=='quotes') {
  	$str=str_replace("'"," ",$val);
  	$str=str_replace('"'," ",$str);
  	return $str;
  } else if ($context=='xml') {
  	$str=$val;
  	$str=str_replace("	"," ",$val);
  	return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
  } else if ($context=="parameter") {
  	$str=str_replace('"',"''",$val);
  	return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
  } else if ($context=="title") {
    $str=$val;
    $str=htmlspecialchars(htmlspecialchars($str,ENT_QUOTES,'UTF-8'),ENT_QUOTES,'UTF-8');
    $str=str_replace( array("\r\n","\n","\r"), array('<br/>','<br/>','<br/>'),$str);
    return $str;
  } else if ($context=="formatted") { // For long text, html format must be preserved but <script> must be removed (Mandatory for Editor fields)
    // Step one : remove <script> tags
    $str = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $val);
    // Step two : if some dangerous scripting capacitites still exist : replace text by warning image
    $test=strtolower($str);
    if (strpos($test,'<script')!==false or strpos($test,'onmouseover')!==false) {
      $str='<img src="../view/img/error.png"/><br/>'.i18n('textHiddenForSecurity');
    }
    return $str;
  } else if ($context=="pdf") {
    $str=str_replace(array('</div>','</p>'),array('</div><br/>','</p><br/>'), $val);
    $str=strip_tags($str,'<br><br/><font><b>');
    return $str;
  } else if ($context=="stipAllTags") {
    //$str=str_replace(array('</div>','</p>',"\n"),array('</div><br/>','</p><br/>',''), $val);
    $str=strip_tags($val);
    //$str=str_replace('<br/>',"\n",$str);
    return $str;
  } else if ($context=='protectQuotes') {
    $str=str_replace(array("'",'"'), array('\\'."'",'\\'.'"'), $val);
    return htmlspecialchars($str,ENT_QUOTES,'UTF-8');    
  }
  return htmlspecialchars($val,ENT_QUOTES,'UTF-8');
}

/**
 * Remove all caracters that may lead to error on Json file rendering
 * @param $val
 * @return unknown_type
 */
function htmlEncodeJson($val, $numericLength=0) {
  //$val=htmlspecialchars($val,ENT_QUOTES,'UTF-8');
	/*$val = str_replace('&quot;',"''",$val);
  $val = str_replace("&#039;","'",$val);
  $val = str_replace("&amp;","&",$val);
  $val = str_replace("&lt;","<",$val);
  $val = str_replace("&gt;",">",$val);*/
  $val = str_replace("\\","\\\\",$val);
  $val = str_replace("\"","\\\"",$val);
  $val = str_replace("\n"," ",$val);	     
  $val = preg_replace('/[ ]{2,}|[\t]/', ' ', trim($val));
  
  if ($numericLength>0) {
    $val=str_pad($val,$numericLength,'0', STR_PAD_LEFT);
  }
  return $val;
}

/** ============================================================================
 * Return an error message formated as a resultDiv result
 * @param $message the message to display
 * @return formated html message, with corresponding html input
 */
function htmlGetErrorMessage($message) {
  $returnValue = '<div class="messageERROR" >' . $message . '</div>';
  $returnValue .= '<input type="hidden" id="lastSaveId" value="" />';
  $returnValue .= '<input type="hidden" id="lastOperation" value="control" />';
  $returnValue .= '<input type="hidden" id="lastOperationStatus" value="ERROR" />';
  return $returnValue;
}

/** ============================================================================
 * Return a warning message formated as a resultDiv result
 * @param $message the message to display
 * @return formated html message, with corresponding html input
 */
function htmlGetWarningMessage($message) {
  $returnValue = '<div class="messageWARNING" >' . $message . '</div>';
  $returnValue .= '<input type="hidden" id="lastSaveId" value="" />';
  $returnValue .= '<input type="hidden" id="lastOperation" value="control" />';
  $returnValue .= '<input type="hidden" id="lastOperationStatus" value="WARNING" />';
  return $returnValue;
}

/** ============================================================================
 * Return an mime/Type formated as an image
 * @param $mimeType the textual mimeType
 * @return formated html mimeType, as an image
 */
function htmlGetMimeType($mimeType,$fileName, $id=null, $type='Attachment') {
  $ext = pathinfo($fileName, PATHINFO_EXTENSION);
  if (file_exists("../view/img/mime/$ext.png")) {
    $img="../view/img/mime/$ext.png";
  } else {
    $img= "../view/img/mime/unknown.png";
  }
  $image='<img src="' . $img . '" title="' . $mimeType . '" ';
  if ($id and ($ext=="htm" or $ext=="html" or $ext=="pdf" or $ext=="txt")) {
  	$image.=' style="cursor:pointer;float:left;" onClick="showHtml(\''.$id.'\',\''.htmlEncode($fileName,'quotes').'\',\''.$type.'\')" ';
  } else {
    $image.=' style="float:left;opacity: 0.4;filter: alpha(opacity=40);" ';
  }
  $image.='/>&nbsp;';
  return $image;
}

/** ============================================================================
 * Return an fileSize formated as GB, MB KB or B 
 * @param $mimeType the textual mimeType
 * @return formated html mimeType, as an image
 */
function htmlGetFileSize($fileSize) {
  $nbDecimals=1;
  $limit=1000;
  if ($fileSize==null) {
    return '';
  }
  if ($fileSize<$limit) {
    return $fileSize . ' ' . i18n('byteLetter');
  } else {
    $fileSize=round($fileSize/1024,$nbDecimals);
    if ($fileSize<$limit) {
      return $fileSize . ' K' . i18n('byteLetter');
    } else {
      $fileSize=round($fileSize/1024,$nbDecimals);
      if ($fileSize<$limit) {
        return $fileSize . ' M' . i18n('byteLetter');
      } else {
        $fileSize=round($fileSize/1024,$nbDecimals);
        if ($fileSize<$limit) {
          return $fileSize . ' G' . i18n('byteLetter');
        } else {
          $fileSize=round($fileSize/1024,$nbDecimals);
          return $fileSize . ' T' . i18n('byteLetter');
        }      
      }
    }
  }
}

/**
 * Extract argument from condition
 * @param $tag String to extract from
 * @param $arg 
 * @return String
 */
function htmlExtractArgument($tag, $arg) {
  $sp=explode($arg . '=', $tag);
  $fld="";
  if (isset($sp[1])) {
    $fld=$sp[1];
    if (strpos($fld,' ')>1) {
      $fld=substr($fld,0,strpos($fld,' '));
    }
    if (strpos($fld,'>')>1) {
      $fld=substr($fld,0,strpos($fld,'>'));
    }
    $fld=trim($fld,'"');
  }
  return $fld;
}

/**
 * Display a, Array of filter criteria
 * @param $filterArray Array
 * @return Void
 */
function htmlDisplayFilterCriteria($filterArray, $filterName="") {
  // Display Result
  echo "<table width='99.9%'>";
  echo "<tr><td class='dialogLabel'>";
  echo '<label for="filterNameDisplay" >' . i18n("filterName") . '&nbsp;:&nbsp;</label>';
  echo '<div type="text" dojoType="dijit.form.ValidationTextBox" ';
  echo ' name="filterNameDisplay" id="filterNameDisplay"';
  echo '  style="width: 564px;" ';
  echo ' trim="true" maxlength="100" class="input" ';
  echo ' value="' . htmlEncode($filterName) . '" ';
  echo ' >';
  echo '</td><td>';
  echo '<button title="' . i18n('saveFilter') . '" ';  
  echo ' dojoType="dijit.form.Button" '; 
  echo ' id="dialogFilterSave" name="dialogFilterSave" ';
  echo ' iconClass="dijitButtonIcon dijitButtonIconSave" showLabel="false"> ';
  echo ' <script type="dojo/connect" event="onClick" args="evt">saveFilter();</script>';
  echo '</button>';
  echo "</td></tr>";
  echo "<tr>";
  echo "<td class='filterHeader' style='width:525px;'>" . i18n("criteria") . "</td>";
  echo "<td class='filterHeader' style='width:25px;'>";
  echo ' <a src="css/images/smallButtonRemove.png" onClick="removefilterClause(\'all\');" title="' . i18n('removeAllFilters') . '" > ';
  echo formatSmallButton('Remove');
  echo ' </a>';
  echo "</td>";
  echo "</tr>";
  if (count($filterArray)>0) { 
    foreach ($filterArray as $id=>$filter) {
      echo "<tr>";
      echo "<td class='filterData'>" . 
           $filter['disp']['attribute'] . " " .
           $filter['disp']['operator'] . " " .
           $filter['disp']['value'] .
           "</td>";
      echo "<td class='filterData' style='text-align: center;'>";
      echo ' <a src="css/images/smallButtonRemove.png" onClick="removefilterClause(' . $id . ');" title="' . i18n('removeFilter') . '" > ';
      echo formatSmallButton('Remove');
      echo ' </a>';
      echo "</td>";
      echo "</tr>";
    }
  } else {
    echo "<tr><td class='filterData' colspan='2'><i>" . i18n("noFilterClause") . "</i></td></tr>";
  }
  echo "</table>";
  echo '<input id="nbFilterCriteria" name="nbFilterCriteria" type="hidden" value="' . count($filterArray) . '" />';
}

/**
 * Display a, Array of filter criteria
 * @param $filterArray Array
 * @return Void
 */
function htmlDisplayStoredFilter($filterArray,$filterObjectClass,$currentFilter="", $context="") {
  // Display Result
  $param=SqlElement::getSingleSqlElementFromCriteria('Parameter', 
       array('idUser'=>getSessionUser()->id, 'parameterCode'=>'Filter'.$filterObjectClass));
  $defaultFilter=($param)?$param->parameterValue:'';
  echo "<table width='100%'>";
  echo "<tr style='height:22px;'>";
  if ($context!='directFilterList') {
  	echo "<td class='filterHeader' style='width:699px;'>" . i18n("storedFilters") . "</td>";
    echo "<td class='filterHeader' style='width:25px;'>";
    echo "<td class='filterHeader' style='width:25px;'>";
  } else {
  	echo "<td class='filterHeader' style='font-size:8pt;width:300px;'>" . i18n("storedFilters") . "</td>";
  }
  echo "</td>";
  echo "</tr>";
  if ($context=='directFilterList') {
    echo "<tr>";
    echo '<td style="cursor:pointer;font-size:8pt;font-style:italic;' 
           . '"' 
           . ' class="filterData" '
           . 'onClick="selectStoredFilter(\'0\',\'directFilterList\''.(array_key_exists("contentLoad", $_REQUEST) && array_key_exists("container", $_REQUEST) ? ',\''.$_REQUEST['contentLoad'].'\',\''.$_REQUEST['container'].'\'' : '').');" ' 
           . ' title="' . i18n("selectStoredFilter") . '" >'
           . i18n("noFilterClause")
           . "</td>";
    echo "</tr>";
  }
  if (count($filterArray)>0) { 
    foreach ($filterArray as $filter) {
      echo "<tr>";
      echo '<td style="font-size:8pt;'. (($filter->name==$currentFilter and $context=='directFilterList')?'color:white; background-color: grey;':'cursor: pointer;') . '"' 
           . ' class="filterData" '
           //. ($filter->name==$currentFilter)?'':'onClick="selectStoredFilter('. "'" . htmlEncode($filter->id) . "'" . ');" ')
           . 'onClick="selectStoredFilter(\'' . htmlEncode($filter->id) . '\',\'' . htmlEncode($context) . '\''.(array_key_exists("contentLoad", $_REQUEST) && array_key_exists("container", $_REQUEST) ? ',\''.$_REQUEST['contentLoad'].'\',\''.$_REQUEST['container'].'\'' : '').');" ' 
           . ' title="' . i18n("selectStoredFilter") . '" >'
           . htmlEncode($filter->name)
           . ( ($defaultFilter==$filter->id and $context!='directFilterList')?' (' . i18n('defaultValue') . ')':'')
           . "</td>";
      if ($context!='directFilterList') {
        echo "<td class='filterData' style='text-align: center;'>";      
        echo ' <a src="css/images/smallButtonRemove.png" onClick="removeStoredFilter('. "'" . htmlEncode($filter->id) . "','" . htmlEncode(htmlEncode($filter->name)) . "'" . ');" title="' . i18n('removeStoredFilter') . '" > ';
        echo formatSmallButton('Remove');
        echo ' </a>';
        echo "</td>";
        echo "<td class='filterData' style='text-align: center;'>";
        if($filter->isShared==0)echo ' <img src="css/images/share.png" class="roundedButtonSmall" onClick="shareStoredFilter('. "'" . htmlEncode($filter->id) . "','" . htmlEncode(htmlEncode($filter->name)) . "'" . ');" title="' . i18n('shareStoredFilter') . '" class="smallButton"/> ';
        if($filter->isShared==1)echo ' <img src="css/images/shared.png" class="roundedButtonSmall" onClick="shareStoredFilter('. "'" . htmlEncode($filter->id) . "','" . htmlEncode(htmlEncode($filter->name)) . "'" . ');" title="' . i18n('unshareStoredFilter') . '" class="smallButton"/> ';
        echo "</td>";
      }
      
      echo "</tr>";
    }
  } else {
  	if ($context!='directFilterList') {
      echo "<tr><td class='filterData' colspan='3'><i>" . i18n("noStoredFilter") . "</i></td></tr>";
  	}
  }
  echo "</table>";

}

function htmlDisplaySharedFilter($filterArray,$filterObjectClass,$currentFilter="", $context="") {
  if (count($filterArray)>0) {  
    $nFilterArray=array();
    foreach ($filterArray as $filter) {
      $user=SqlElement::getSingleSqlElementFromCriteria("User", array("id"=>$filter->idUser));
      $cle=$user->name.'|'.$user->id;
      if(!isset($nFilterArray[$cle]))$nFilterArray[$cle]=array();
      $nFilterArray[$cle][$filter->name]=$filter;
      asort($nFilterArray[$cle]);
    }
    asort($nFilterArray);
    // Display Result
    $param=SqlElement::getSingleSqlElementFromCriteria('Parameter',
        array('idUser'=>getSessionUser()->id, 'parameterCode'=>'Filter'.$filterObjectClass));
    $defaultFilter=($param)?$param->parameterValue:'';
    echo '<div dojoType="dijit.form.DropDownButton"
                              style="width: 300px;margin:0 auto;"
                              id="filterSharedSelect" name="entity">';
    echo '<span>'.i18n("selectSharedFilter").'</span><div data-dojo-type="dijit/TooltipDialog">';
    $iterateur=0;
      foreach ($nFilterArray as $userName=>$filters) {
        $nameExplode=explode('|',$userName);
        echo '<span style="float:left;height:15px;font-weight:bold;" disabled="disabled" value="-2" '
            . ' title="' . i18n("selectStoredFilter") . '" >'.$nameExplode[0].'</span><br>';
        foreach ($filters as $filterName=>$filter) {
          echo '<span onclick="selectStoredFilter('.htmlEncode($filter->id).',\'' . htmlEncode($context) . '\');dijit.byId(\'filterSharedSelect\').closeDropDown();" class="menuTree" style="float:left;height:15px;" '
              . ' >&nbsp;&nbsp;&nbsp;&nbsp;'
                  . htmlEncode($filter->name)
                  . ( ($defaultFilter==$filter->id and $context!='directFilterList')?' (' . i18n('defaultValue') . ')':'')
                  . "</span><br>";
        }
        $iterateur++;
        if(sizeof($nFilterArray)>$iterateur)echo '<span style="float:left;height:15px;" value="-1" '
        . ' title="' . i18n("selectStoredFilter") . '" ></span><br>';
      }
    echo "</div></div>";
    echo "<span style='position:relative;left:20px;font-size:90%;color:#a0a0a0;'>".i18n("tipsSharedFilter").'</span>';
  }
}

function htmlDisplayCheckbox ($value) {
  $checkImg="checkedKO.png";
  if ($value!='0' and ! $value==null) { 
    $checkImg= 'checkedOK.png';
  } 
  return '<img src="img/' . $checkImg . '" />';
}

function htmlDisplayColored($value,$color) {
  global $print, $outMode;
  $result= "";
  $foreColor=htmlForeColorForBackgroundColor($color);
  //$result.= '<table><tr><td style="background-color:' . $color . '; color:' . $foreColor . ';">';
  //$result.= $value;
  //$result.= "</td></tr></table>";
  $result.='<div style="vertical-align:middle;padding: 2px 5px;border:1px solid #CCC;border-radius:10px;text-align: center;'
      .(($print and $outMode=='pdf')?'width:95%;min-height:18px;':'') 
      . 'background-color: ' . $color . '; color:' . $foreColor . ';">'
      .$value.'</div>';
  return $result;
}

function htmlForeColorForBackgroundColor($color) {
  $foreColor='#000000';
  if (strlen($color)==7) {
    $red=base_convert(substr($color,1,2),16,10);
    $green=base_convert(substr($color,3,2),16,10);
    $blue=base_convert(substr($color,5,2),16,10);
    $light=(0.3)*$red + (0.6)*$green + (0.1)*$blue;
    if ($light<128) { $foreColor='#FFFFFF'; }
  }
  return $foreColor;
}

function htmlDisplayCurrency($val,$noDecimal=false) {
  if (! $val and $val!='0') return '';
  global $browserLocale;
  $currency=Parameter::getGlobalParameter('currency');
  $currencyPosition=Parameter::getGlobalParameter('currencyPosition');
  if ($noDecimal) {
    $fmt = new NumberFormatter52( $browserLocale, NumberFormatter52::INTEGER );
  } else {
    $fmt = new NumberFormatter52( $browserLocale, NumberFormatter52::DECIMAL );
  }
  if (! isset($currencyPosition) or ! isset($currency) or $currencyPosition=='none') {
    return $fmt->format($val) ;
  } 
  if ($currencyPosition=='after') {
    return str_replace(' ','&nbsp;',$fmt->format($val)) . '&nbsp;' . $currency; 
  } else {
    return $currency . '&nbsp;' . str_replace(' ','&nbsp;',$fmt->format($val)) ;
  }
}

function htmlDisplayNumeric($val) {
  global $browserLocale;
  // old version : too restrictive
  $fmt = new NumberFormatter52( $browserLocale, NumberFormatter52::DECIMAL );
  return $fmt->format($val) ;
  // numflt_* functions unvailable in some PHP versions, so keep old version
  //$fmt = numfmt_create( $browserLocale, NumberFormatter::DECIMAL );
  //$data = numfmt_format($fmt, $val);
  //return $data;
}

function htmlDisplayNumericWithoutTrailingZeros($val) {
  global $browserLocale;
  if ($val==0) return 0;
  $fmt = new NumberFormatter52( $browserLocale, NumberFormatter52::DECIMAL );
  $res=$val;
  if (strpos($res, '.')!==false) {
    $res=trim($res,'0');
  }
  if (substr($res, -1)=='.') {
    $res=trim($res,'.');
  }
  if ($res<1 and substr($res,0,1)=='.') $res='0'.$res;
  if ($fmt->decimalSeparator!='.') {
    $res=str_replace('.', $fmt->decimalSeparator, $res);
  }
  return $res ;
}

function htmlDisplayPct($val) {
  return htmlDisplayNumericWithoutTrailingZeros($val) . '&nbsp;%';
}

function htmlRemoveDocumentTags($val) {
  $res=strstr($val, '<body>');
  $res=str_replace(array('<html>','</html>','<body>','</body>') , '', $res);
  return $res;
}

function htmlDrawLink($obj, $display=null) {
	$canRead=securityGetAccessRightYesNo('menu' . get_class($obj), 'read', $obj)=="YES";
	$disp=htmlencode(($display)?$display:$obj->name);
	if ($canRead) {
	  $result='<a class="link" onClick="gotoElement(\'' . get_class($obj) .'\',\''. htmlEncode($obj->id) .'\');">' . $disp . '</a>';
	} else {
		$result=$disp;
	}  
	 
	return $result;
}

function htmlFixLengthNumeric($val, $numericLength=0) {  
  if ($numericLength>0) {
    $val=str_pad($val,$numericLength,'0', STR_PAD_LEFT);
  }
  return $val;
}

function htmlTransformRichtextToPlaintext($string) {
  $string=str_replace(array('</div>  <div>'),
                      array('</div><div>'),
                      $string);
  $string=str_replace(array('&nbsp;','<br /> ','<br>','<br/>'  ,'</div>'  ,'</p>'  ,'</tr>'),
                      array(' '     ,"\n"    ,"\n"  ,"\n","</div>\n","</p>\n","</tr>\n"),
                      $string);
  $string=strip_tags(html_entity_decode($string));
  return $string;
}
?>