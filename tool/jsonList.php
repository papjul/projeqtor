<?PHP
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
 * Get the list of objects, in Json format, to display the grid list
 */

    require_once "../tool/projeqtor.php"; 
//scriptLog('   ->/tool/jsonList.php');  
    $type=$_REQUEST['listType']; // Note: checked against constant values.
    if (isset($_REQUEST['critField'])) {
      $field=$_REQUEST['critField'];
      Security::checkValidAlphanumeric($field);
      if (! isset($_REQUEST['critValue'])) {
        errorLog("incorrect query jonList : critValue is not set but critField set");
        return;
      }
      if (substr($field,0,2)=='id') {
        Security::checkValidId($_REQUEST['critValue']);
      } 
    } else if (isset($_REQUEST['critValue'])) {
      errorLog("incorrect query jonList : critValue is set but critField is not set");
      return;
    }
    
    echo '{"identifier":"id",' ;
    echo 'label: "name",';
    echo ' "items":[';
    // If type = 'list' and $dataType = idResource : execute the listResourceProject type
    $required=true; // when directly requesting 'listResourceProject', required is by default
    if ($type=='list'
    and array_key_exists('dataType', $_REQUEST) and $_REQUEST['dataType']=='idResource' 
    and array_key_exists('critField', $_REQUEST) and array_key_exists('critValue', $_REQUEST)
    and $_REQUEST['critField']=='idProject') {
    	$type='listResourceProject';
    	$_REQUEST['idProject']=$_REQUEST['critValue']; // This is valid : force idProject to critValue as criFiled=idProject (value has been tested as an id)
    	$required=array_key_exists('required', $_REQUEST);
    }
    
    if ($type=='ExpenseDetailType') {
      $type='list';
    }
    
    if ($type=='empty') {
          
    } else if ($type=='object') {    
      $objectClass=$_REQUEST['objectClass'];
      Security::checkValidClass($objectClass, 'objectClass');

      $obj=new $objectClass();
      $nbRows=listFieldsForFilter ($obj,0);
    } else if ($type=='operator') {    
      $dataType=$_REQUEST['dataType']; // Note: checked against constant values.
      if ($dataType=='int' or $dataType=='date' or $dataType=='datetime' or $dataType=='decimal') {
        echo ' {id:"=", name:"="}';
        echo ',{id:">=", name:">="}';
        echo ',{id:"<=", name:"<="}';
        echo ',{id:"<>", name:"<>"}';
        if ($dataType!='int' and $dataType!='decimal') {
          //echo ',{id:"xx", name:"xx"}';
          echo ',{id:"<=now+", name:"<= ' . i18n('today') . ' + "}';
          echo ',{id:">=now+", name:">= ' . i18n('today') . ' + "}';
          echo ',{id:"isEmpty", name:"' . i18n('isEmpty') . '"}';
          echo ',{id:"isNotEmpty", name:"' . i18n('isNotEmpty') . '"}';
        }
        echo ',{id:"SORT", name:"' . i18n('sortFilter') .'"}';
      } else if ($dataType=='varchar') {
        echo ' {id:"LIKE", name:"' . i18n("contains") . '"}';
        echo ',{id:"NOT LIKE", name:"' . i18n("notContains") . '"}';
        echo ',{id:"isEmpty", name:"' . i18n('isEmpty') . '"}';
        echo ',{id:"isNotEmpty", name:"' . i18n('isNotEmpty') . '"}';
        echo ',{id:"SORT", name:"' . i18n('sortFilter') .'"}';
      } else if ($dataType=='bool') {
        echo ' {id:"=", name:"="}';
        echo ',{id:"SORT", name:"' . i18n('sortFilter') .'"}';
      } else if ($dataType=='list') {
        echo ' {id:"IN", name:"' . i18n("amongst") . '"}';
        echo ',{id:"NOT IN", name:"' . i18n("notAmongst") . '"}';
        echo ',{id:"isEmpty", name:"' . i18n('isEmpty') . '"}';
        echo ',{id:"isNotEmpty", name:"' . i18n('isNotEmpty') . '"}';
        echo ',{id:"SORT", name:"' . i18n('sortFilter') .'"}';
      } else if ($dataType=='refObject') {
        echo ' {id:"LIKE", name:"' . i18n("contains") . '"},';
        echo ' {id:"hasSome", name:"' . i18n("isNotEmpty") . '"}';
        //echo ',{id:"NOT LIKE", name:"' . i18n("notContains") . '"}';
      } else  {
        echo ' {id:"UNK", name:"?"}';
        echo ',{id:"SORT", name:"' . i18n('sortFilter') .'"}';
      }
      
    } else if ($type=='list') {   
      $dataType=$_REQUEST['dataType']; // Note: checked against constant values.     
      $selected="";
      if ( array_key_exists('selected',$_REQUEST) ) {
        $selected=$_REQUEST['selected'];
      }
      if ($dataType=='planning') {
        $class='Project';
      } else {
        $class=substr($dataType,2);
      }
      if ($dataType=='idProject' and securityGetAccessRight('menuProject', 'read')!='ALL') {
      	$user=getSessionUser();
      	$list=$user->getVisibleProjects();
      } else if ($dataType=='planning') {
      	$user=getSessionUser();
      	$list=$user->getVisibleProjects();
      	$restrictArray=$user->getListOfPlannableProjects();
      	foreach ($list as $prj=>$prjname) {
      	  if (! isset($restrictArray[$prj])) {
      	    unset($list[$prj]);
      	  }
      	}
      } else if ($dataType=='idProfile' 
        and array_key_exists('critField', $_REQUEST) and array_key_exists('critValue', $_REQUEST) 
        and $_REQUEST['critField']=='idProject') {
        $idProj=$_REQUEST['critValue'];
        $user=new User();
        $prf=new Profile(getSessionUser()->getProfile($idProj));
        $lstPrf=$prf->getSqlElementsFromCriteria(null,false,"idle=0 and ".(($prf->sortOrder)?'sortOrder>='.$prf->sortOrder:'1=1'),"sortOrder asc");
        $list=array();
        foreach ($lstPrf as $profile) {
          $list[$profile->id]=i18n($profile->name);
        }
        if ($selected) {
          $aff=new Affectation($selected);
          $list[$aff->idProfile]=SqlList::getNameFromId('Profile', $aff->idProfile);
          $selected=null;
        }
      } else if (($dataType=='idProduct' or $dataType=='idComponent' or $dataType=='idProductOrComponent') 
        and array_key_exists('critField', $_REQUEST) and array_key_exists('critValue', $_REQUEST)) {
      	if (trim($_REQUEST['critValue']) and $_REQUEST['critField']=='idProject') {    	
	        $list=array();
	      	$listProd=SqlList::getList($class);
	      	$versProj=new VersionProject();
	      	$proj=new Project($_REQUEST['critValue']);
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
	      	foreach ($versProjList as $versProj) {	      	  
	      		$vers=new Version($versProj->idVersion);	      		
	      		if (isset($listProd[$vers->idProduct])) {
	      			$list[$vers->idProduct]=$listProd[$vers->idProduct];	      			
	      		}
	      	}
      	} else if (trim($_REQUEST['critValue']) and $_REQUEST['critField']=='idProduct') {
      	    $prod=new Product($_REQUEST['critValue']);
      	    $list=$prod->getComposition(true,true);
      	    if ($selected) {
      	      $list[$selected]=SqlList::getNameFromId('Component', $selected);
      	    }
      	} else {
      		$list=SqlList::getList($class);
      	}
      	
      } else if (substr($dataType,0,2)=='id' and substr($dataType,-4)=='Type' and $dataType!='idType' and $dataType!="idExpenseDetailType") {
        $list=SqlList::getList($class);
        if (array_key_exists('critField', $_REQUEST) and array_key_exists('critValue', $_REQUEST)) {
          $critField=$_REQUEST['critField'];
          $critVal=$_REQUEST['critValue'];
          if ($critField=='idProject') {
            $rtListProjectType=Type::listRestritedTypesForClass($class,$critVal,null,null);
            if (count($rtListProjectType)) {
              foreach($list as $id=>$val) {
                if ($id!=$selected and !in_array($id, $rtListProjectType)) {
                  unset($list[$id]);
                }
              }
            }
          }
          //$_REQUEST['required']='true';
        }
      } else if (array_key_exists('critField', $_REQUEST) and array_key_exists('critValue', $_REQUEST)) {
        $critField=$_REQUEST['critField'];
        if (($dataType=='idVersion' or $dataType=='idProductVersion' or $dataType=='idComponentVersion' 
          or $dataType=='idOriginalVersion' or $dataType=='idOriginalProductVersion' or $dataType=='idOriginalComponentVersion'
          or $dataType=='idTargetVersion' or $dataType=='idTargetProductVersion' or $dataType=='idTargetComponentVersion') 
        and ($critField=='idProductOrComponent' or $critField=='idComponent')) {
          $critField='idProduct';
        }
        if (property_exists($class,$critField)) {
          $crit=array( $critField => $_REQUEST['critValue']);
        } else {
          $crit=array();
        }
        if (substr($dataType,-16)=='ComponentVersion' and isset($_REQUEST['critField1']) and isset($_REQUEST['critValue1'])) {
          $crit[$_REQUEST['critField1']]=$_REQUEST['critValue1'];
        }
        $list=SqlList::getListWithCrit($class, $crit);
        
      } else {
        $list=SqlList::getList($class);        
      }
      if ($selected) {
      	$name=SqlList::getNameFromId($class, $selected);
      	if ($name==$selected and ($class=='Resource' or $class=='User' or $class=='Contact')) {
      		$name=SqlList::getNameFromId('Affectable', $selected);
      	}
      	if ($name==$selected and substr($class,-7)=='Version' and SqlElement::is_a($class, 'Version')) {
      	  $name=SqlList::getNameFromId('Version', $selected);
      	}
        $list[$selected]=$name;
      }
      if ($dataType=="idProject") { $wbsList=SqlList::getList('Project','sortOrder',$selected, true);} 
      $nbRows=0;
      // return result in json format
      if (! array_key_exists('required', $_REQUEST)) {
      	echo '{id:" ", name:""}';
        $nbRows+=1;
      }
      if ($dataType=="idProject") {
        $sepChar=Parameter::getUserParameter('projectIndentChar');
        if (!$sepChar) $sepChar='__';
        $wbsLevelArray=array();
      }
      if ($dataType=='idLinkable' or $dataType=='idCopyable' or $dataType=='idImportable' or $dataType=='idMailable'
          or $dataType=='idIndicatorable' or $dataType=='idChecklistable' or $dataType=='idDependable' 
          or $dataType=='idOriginable' or $dataType=='idReferencable') {
        asort($list);
      }
      $pluginObjectClass=substr($dataType,2);
      $table=$list;
      $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
      foreach ($lstPluginEvt as $script) {
        require $script; // execute code
      }
      foreach ($table as $id=>$name) {
        if ($dataType=="idProject" and $sepChar!='no') {
          if (isset($wbsList[$id])) {
        	  $wbs=$wbsList[$id];
          } else {
          	$wbsProj=new Project($id);
          	$wbs=$wbsProj->sortOrder;
          }
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
          //$levelWidth = ($level-1) * 2;
          //$sep=($levelWidth==0)?'':substr('_____________________________________________________',(-1)*($levelWidth));
          $name = $sep.$name;
        }
        if ($nbRows>0) echo ', ';
        echo '{id:"' . htmlEncodeJson($id) . '", name:"'. htmlEncodeJson($name) . '"}';
        $nbRows+=1;
      }
    } else if ($type=='listResourceProject') {
	      $idPrj=$_REQUEST['idProject'];
	      $prj=new Project($idPrj);
	      $lstTopPrj=$prj->getTopProjectList(true);
	      $in=transformValueListIntoInClause($lstTopPrj);
	      $where="idle=0 and idProject in " . $in; 
	      if (isset($_REQUEST['objectClass']) and $_REQUEST['objectClass']=='IndividualExpense') {
	        if (securityGetAccessRight('menuIndividualExpense', 'read', null, getSessionUser() )=='OWN') {
	          $where.=" and idResource=".Sql::fmtId(getSessionUser()->id);
	        }
	      }
	      $aff=new Affectation();
	      $list=$aff->getSqlElementsFromCriteria(null,null, $where);
	      $nbRows=0;
	      $lstRes=array();
	      if (array_key_exists('selected', $_REQUEST)) {
	        $lstRes[$_REQUEST['selected']]=SqlList::getNameFromId('Affectable', $_REQUEST['selected']);
	      }
	      $restrictArray=array();
	      $scope=Affectable::getVisibilityScope();
	      if ($scope!="all") {
	        $res=new Resource();
	        if ($scope=='orga') {
	          $crit="idOrganization in (". Organization::getUserOrganisationList().")";
	        } else if ($scope=='team') {
	          $aff=new Affectable(getSessionUser()->id,true);
	          $crit="idTeam='$aff->idTeam'";
	        } else {
	          traceLog("Error on htmlDrawOptionForReference() : Resource::getVisibilityScope returned something different from 'all', 'team', 'orga'");
	          $crit=array('id'=>'0');
	        }
	        $listRestrict=$res->getSqlElementsFromCriteria(null,false,$crit);
	        foreach ($listRestrict as $res) {
	          $restrictArray[$res->id]=$res->name;
	        }
	      }
	      foreach ($list as $aff) {
	        if (! array_key_exists($aff->idResource, $lstRes)) {
	        	$id=$aff->idResource;
	        	$name=SqlList::getNameFromId('Resource', $id);
	        	if ($name!=$id) {
	        	  if ($scope=="all" or isset($restrictArray[$id])) {
	              $lstRes[$id]=$name;
	        	  }
	        	}
	        }
	      }
	      $pluginObjectClass='Affectable';
	      $table=$lstRes;
	      $lstPluginEvt=Plugin::getEventScripts('list',$pluginObjectClass);
	      foreach ($lstPluginEvt as $script) {
	        require $script; // execute code
	      }
	      asort($table);
	      // return result in json format
        if (! $required) {
          echo '{id:" ", name:""}';
          $nbRows+=1;
        }
	      foreach ($table as $id=>$name) {
	        if ($nbRows>0) echo ', ';
		    echo '{id:"' . htmlEncodeJson($id) . '", name:"'. htmlEncodeJson($name) . '"}';
	        $nbRows+=1;
	      }
	    } else if ($type=='listTermProject') {
	    	if(!isset($_REQUEST['selected']))	{
	    	  /*if (isset($_REQUEST['directAccessIndex']) and isset($_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']])) {
            $obj=$_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']];
          } else {
          	$obj=$_SESSION['currentObject'];
          }*/
	    	  $obj=SqlElement::getCurrentObject(null,null,false,false); // V5.2
	        $idPrj=$_REQUEST['idProject'];
	        $prj=new Project($obj->idProject);
	        $lstTopPrj=$prj->getTopProjectList(true);
	        $in=transformValueListIntoInClause($lstTopPrj);
	        $where="idProject in " . $in." AND idBill is null";	       
	        $term=new Term();
	        $list=$term->getSqlElementsFromCriteria(null,null, $where);
	        $listFinal = array();
	        foreach ($list as $term) {
	      	  // on récupère les trigger
	      	  $dep = new Dependency();
	      	  $crit = array("successorRefType"=>"Term","successorRefId"=>$term->id);
	      	  $depList = $dep->getSqlElementsFromCriteria($crit,false);
	      	  $idle = 1;
	      	  foreach ($depList as $dep) {
	      		  switch ($dep->predecessorRefType) {
	      			  case "Activity":
	      				  //$act = new Activity($dep->predecessorRefId);
	      				  //if ($act->idle == 0) $idle = 0;
	      				  break;
	      			  case "Milestone":
	      				  $mil = new Milestone($dep->predecessorRefId);
	      				  if ($mil->idle == 0) $idle = 0;
	      				  break;
	      			  case "Project":
	      				  //$project = new Project($dep->predecessorRefId);
	      				  //if ($project->idle == 0) $idle = 0;
	      				  break;
	      		  }
	      	  }      	
	      	  // si tous les trigger sont clos alors on ajoute le term à la liste des term disponibles
	      	  if($idle==1) {
	      		  if($term->date!=null) {
  	      			$now = date('Y-m-d');
	        			$now = new DateTime($now);
	        			$now = $now->format('Y-m-d');
	        			if ($now >= $term->date) {
	        				$listFinal[$term->id]=$term;
	      	  		}
	      		  } else { 
	      			  $listFinal[$term->id]=$term;
	      		  }
	      	  }
	        }	
	        foreach ($listFinal as $term) {
	          if (! array_key_exists($term->id, $listFinal)) {
	          $listFinal[$term->id]=SqlList::getNameFromId('Term', $term->id);
	          }
	        }
	        
	        asort($listFinal);
	        // return result in json format	      
	        echo '{id:null, name:""}';
	        // $i=0;
	        foreach ($listFinal as $term) {
	      	  //if($i!=0) 
	      	  echo ', ';
	          echo '{id:"' . $term->id . '", name:"'. $term->name . '"}';
	         //$i++;
	        }
	      } else {
			echo '{id:"' . htmlEncodeJson($_REQUEST['selected']) . '", name:"'. htmlEncodeJson(SqlList::getNameFromId('Term', $_REQUEST['selected'])) . '"}';
	      }           
    } else if ($type=='listRoleResource') {
      $ctrl="";
      $idR=$_REQUEST['idResource'];
      $resource=new Resource($idR);
      $nbRows=0;
      if ($resource->idRole) {
        echo '{id:"' . $resource->idRole . '", name:"'. SqlList::getNameFromId('Role', $resource->idRole) . '"}';
        $nbRows+=1;
        $ctrl.='#' . $resource->idRole . '#';
      }

      $where="idResource='" . Sql::fmtId($idR) . "' and endDate is null";
      $where.=" and idRole <>'" . Sql::fmtId($resource->idRole) . "'";
      $rc=new ResourceCost();
      $lstRoles=$rc->getSqlElementsFromCriteria(null, false, $where);
      // return result in json format
      foreach ($lstRoles as $resourceCost) {
        $key='#' . $resource->idRole . '#';
        if (strpos($ctrl,$key)===false) {
          if ($nbRows>0) echo ', ';
          echo '{id:"' . $resourceCost->idRole . '", name:"'. SqlList::getNameFromId('Role', $resourceCost->idRole) . '"}';
          $nbRows+=1;
          $ctrl.=$key;
        }
      }
    } else if ($type=='listStatusDocumentVersion') {
      /*if (isset($_REQUEST['directAccessIndex']) and isset($_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']])) {
        $doc=$_SESSION['directAccessIndex'][$_REQUEST['directAccessIndex']];
      } else {
        $doc=$_SESSION['currentObject'];
      }*/
      $doc=SqlElement::getCurrentObject(null,null,false,false); // V5.2
    	$idDocumentVersion=$_REQUEST['idDocumentVersion'];
      $docVers=new documentVersion($idDocumentVersion);
    	$table=SqlList::getList('Status','name',$docVers->idStatus);
    	if ($doc and $docVers->idStatus) {      
	      $profile=getSessionUser()->getProfile($doc);
	      $type=new DocumentType($doc->idDocumentType);
	      $ws=new WorkflowStatus();
	      $crit=array('idWorkflow'=>$type->idWorkflow, 'allowed'=>1, 'idProfile'=>$profile, 'idStatusFrom'=>$docVers->idStatus);
	      $wsList=$ws->getSqlElementsFromCriteria($crit, false);
	      $compTable=array($docVers->idStatus=>'ok');
	      foreach ($wsList as $ws) {
	        $compTable[$ws->idStatusTo]="ok";
	      }
        $table=array_intersect_key($table,$compTable);
      } else {
        reset($table);
        $table=array(key($table)=>current($table));
      }  
      $nbRows=0;
      foreach ($table as $id=>$name) {    
        if ($nbRows>0) echo ', ';
        echo '{id:"' . $id . '", name:"'. $name . '"}';
        $nbRows+=1;
      }
    }
    echo ' ] }';

function listFieldsForFilter ($obj,$nbRows, $included=false) {
  // return result in json format
  foreach ($obj as $col=>$val) {
    if (substr($col, 0,1) <> "_" 
    and substr($col, 0,1) <> ucfirst(substr($col, 0,1))
    and ! $obj->isAttributeSetToField($col,'hidden')
    and ! $obj->isAttributeSetToField($col,'calculated')
    and (!$included or ($col!='id' and $col!='refType' and $col!='refId' and $col!='idle')  )) { 
      if ($nbRows>0) echo ', ';
      $dataType = $obj->getDataType($col);
      $dataLength = $obj->getDataLength($col);
      if ($dataType=='int' and $dataLength==1) { 
        $dataType='bool'; 
      } else if ($dataType=='datetime') { 
        $dataType='date'; 
      } else if ((substr($col,0,2)=='id' and $dataType=='int' and strlen($col)>2 
              and substr($col,2,1)==strtoupper(substr($col,2,1)))) { 
        $dataType='list'; 
      }
      $colName=$obj->getColCaption($col);
      if (substr($col,0,9)=='idContext') {
        $colName=SqlList::getNameFromId('ContextType',substr($col,9));
      }
      echo '{id:"' . ($included?get_class($obj).'_':'') . $col . '", name:"'. $colName .'", dataType:"' . $dataType . '"}';
      $nbRows++;
    } else if (substr($col, 0,1)<>"_" and substr($col, 0,1) == ucfirst(substr($col, 0,1)) ) {
    	$sub=new $col();
      $nbRows=listFieldsForFilter ($sub,$nbRows,true);
    }
  }
  if (isset($obj->_Note)) {
  	if ($nbRows>0) echo ', ';
  	echo '{id:"Note", name:"'. i18n('colNote') .'", dataType:"refObject"}';
  	$nbRows++;
  }  
  return $nbRows;
}
?>