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
    scriptLog('   ->/tool/jsonQuery.php'); 
    $objectClass=$_REQUEST['objectClass'];
	  Security::checkValidClass($objectClass);
	
    $showThumb=Parameter::getUserParameter('paramShowThumbList');
    if ($showThumb=='NO') {
      $showThumb=false;
    } else {
      $showThumb=true;
    }
    
    $hiddenFields=array();
    if (isset($_REQUEST['hiddenFields'])) {
    	$hiddens=explode(';',$_REQUEST['hiddenFields']);
    	foreach ($hiddens as $hidden) {
    		if (trim($hidden)) {
    			$hiddenFields[$hidden]=$hidden;
    		}
    	}
    }
    $print=false;
    if ( array_key_exists('print',$_REQUEST) ) {
      $print=true;
      include_once('../tool/formatter.php');
    }
    $comboDetail=false;
    if ( array_key_exists('comboDetail',$_REQUEST) ) {
      $comboDetail=true;
    }
    $quickSearch=false;
    if ( array_key_exists('quickSearch',$_REQUEST) ) {
      $quickSearch=Sql::fmtStr($_REQUEST['quickSearch']);
    }
    if (! isset($outMode)) { $outMode=""; } 
       
    $obj=new $objectClass();
    $table=$obj->getDatabaseTableName();
    $accessRightRead=securityGetAccessRight($obj->getMenuClass(), 'read');  
    $querySelect = '';
    $queryFrom=$table;
    $queryWhere='';
    $queryOrderBy='';
    $idTab=0;
    
    $res=array();
    $layout=$obj->getLayout();
    $array=explode('</th>',$layout);

    // ====================== Build restriction clauses ================================================
    
    // --- Quick search criteria (textual search in any text field, including notes)
    if ($quickSearch) {
    	$queryWhere.= ($queryWhere=='')?'':' and ';
    	$queryWhere.="( 1=2 ";
    	$note=new Note();
    	$noteTable=$note->getDatabaseTableName();
    	foreach($obj as $fld=>$val) {
    	  if ($obj->getDataType($fld)=='varchar') {    				
            $queryWhere.=' or '.$table.".".$fld." ".((Sql::isMysql())?'LIKE':'ILIKE')." '%".$quickSearch."%'";
    	  }
    	}
    	if (is_numeric($quickSearch)) {
    		$queryWhere.= ' or ' . $table . ".id=" . $quickSearch . "";
    	}
    	$queryWhere.=" or exists ( select 'x' from $noteTable " 
    	                           . " where $noteTable.refType=".Sql::str($objectClass)
    	                           . " and $noteTable.refId=$table.id " 
    	                           . " and $noteTable.note ".((Sql::isMysql())?'LIKE':'ILIKE')." '%" . $quickSearch . "%' ) ";
    	$queryWhere.=" )";
    }
    
    // --- Should idle projects be shown ?
    $showIdleProjects=(! $comboDetail and isset($_SESSION['projectSelectorShowIdle']) and $_SESSION['projectSelectorShowIdle']==1)?1:0;
    // --- "show idle checkbox is checked ?
    if (! isset($showIdle)) $showIdle=false;
    if (!$showIdle and ! array_key_exists('idle',$_REQUEST) and ! $quickSearch) {
      $queryWhere.= ($queryWhere=='')?'':' and ';
      $queryWhere.= $table . "." . $obj->getDatabaseColumnName('idle') . "=0";
    } else {
      $showIdle=true;
    }
    
    // --- Direct filter on id (only used for printing, as direct filter is done on client side)
    if (array_key_exists('listIdFilter',$_REQUEST)  and ! $quickSearch) {
      $param=$_REQUEST['listIdFilter'];
      $param=strtr($param,"*?","%_");
      $param=Sql::fmtStr($param);
      $queryWhere.= ($queryWhere=='')?'':' and ';
      $queryWhere.=$table.".".$obj->getDatabaseColumnName('id')." like '%".$param."%'";
    }
    // --- Direct filter on name (only used for printing, as direct filter is done on client side)
    if (array_key_exists('listNameFilter',$_REQUEST)  and ! $quickSearch) {
      $param=$_REQUEST['listNameFilter'];
      $param=strtr($param,"*?","%_");
      $param=Sql::fmtStr($param);
      $queryWhere.= ($queryWhere=='')?'':' and ';
      $queryWhere.=$table.".".$obj->getDatabaseColumnName('name')." ".((Sql::isMysql())?'LIKE':'ILIKE')." '%".$param."%'";
    }
    // --- Direct filter on type 
    if ( array_key_exists('objectType',$_REQUEST)  and ! $quickSearch) {
      if (trim($_REQUEST['objectType'])!='') {
        $queryWhere.= ($queryWhere=='')?'':' and ';
        $queryWhere.= $table . "." . $obj->getDatabaseColumnName('id' . $objectClass . 'Type') . "=" . Sql::str($_REQUEST['objectType']);
      }
    }
    // --- Direct filter on client
    if ( array_key_exists('objectClient',$_REQUEST)  and ! $quickSearch) {
      if (trim($_REQUEST['objectClient'])!='' and property_exists($obj, 'idClient')) {
        $queryWhere.= ($queryWhere=='')?'':' and ';
        $queryWhere.= $table . "." . $obj->getDatabaseColumnName('idClient') . "=" . Sql::str($_REQUEST['objectClient']);
      }
    }
    // --- Direct filter on elementable
    if ( array_key_exists('objectElementable',$_REQUEST)  and ! $quickSearch) {
      if (trim($_REQUEST['objectElementable'])!='') {
        $elementable=null;
        if ( property_exists($obj,'idMailable') ) $elementable='idMailable';
        else if (property_exists($obj,'idIndicatorable')) $elementable='idIndicatorable';
        else if (property_exists($obj,'idTextable')) $elementable='idTextable';
        else if ( property_exists($obj,'idChecklistable')) $elementable='idChecklistable';
        if ($elementable) {
          $queryWhere.= ($queryWhere=='')?'':' and ';
          $queryWhere.= $table . "." . $obj->getDatabaseColumnName($elementable) . "=" . Sql::str($_REQUEST['objectElementable']);
        }
      }
    }
    
    // --- Restrict to allowed projects : for Projects list
    if ($objectClass=='Project' and $accessRightRead!='ALL') {
        $accessRightRead='ALL';
        $queryWhere.= ($queryWhere=='')?'':' and ';
        $queryWhere.=  '(' . $table . ".id in " . transformListIntoInClause(getSessionUser()->getVisibleProjects(! $showIdle)) ;
        //if ($objectClass=='Project') {
          $queryWhere.= " or $table.codeType='TMP' "; // Templates projects are always visible in projects list
        //}
        $queryWhere.= ')';
    }  
    // --- Restrict to allowed project taking into account selected project : for all list that are project dependant
    if (property_exists($obj, 'idProject') and array_key_exists('project',$_SESSION)) {
        if ($_SESSION['project']!='*') {
          $queryWhere.= ($queryWhere=='')?'':' and ';
          if ($objectClass=='Project') {
            $queryWhere.=  $table . '.id in ' . getVisibleProjectsList(! $showIdleProjects) ;
          } else if ($objectClass=='Document') {
          	$queryWhere.= "(" . $table . ".idProject in " . getVisibleProjectsList(! $showIdleProjects) . " or " . $table . ".idProject is null)";
          } else {
            $queryWhere.= $table . ".idProject in " . getVisibleProjectsList(! $showIdleProjects) ;
          }
        }
    }

    // --- Take into account restriction visibility clause depending on profile
    if ( ($objectClass=='Version' or $objectClass=='Resource') and $comboDetail) {
    	// No limit, although idProject exists
    } else {
      $clause=getAccesRestrictionClause($objectClass,$table, $showIdleProjects);
      if (trim($clause)) {
        $queryWhere.= ($queryWhere=='')?'(':' and (';
        $queryWhere.= $clause;
        if ($objectClass=='Project') {
          $queryWhere.= " or $table.codeType='TMP' "; // Templates projects are always visible in projects list
        }
        $queryWhere.= ')';
      }
    }
    if ($objectClass=='Resource') {
      $scope=Affectable::getVisibilityScope('Screen');
      if ($scope!="all") {
        $queryWhere.= ($queryWhere=='')?'':' and ';
        if ($scope=='orga') {
          $queryWhere.=" $table.idOrganization in (". Organization::getUserOrganisationList().")";
        } else if ($scope=='team') {
          $aff=new Affectable(getSessionUser()->id,true);
          $queryWhere.=" $table.idTeam='$aff->idTeam'";
        }
      }
    }
    
    // --- Apply systematic restriction  criteria defined for the object class (for instance, for types, limit to corresponding type)
    $crit=$obj->getDatabaseCriteria();
    foreach ($crit as $col => $val) {
      $queryWhere.= ($queryWhere=='')?'':' and ';
      $queryWhere.= $obj->getDatabaseTableName() . '.' . $obj->getDatabaseColumnName($col) . "=" . Sql::str($val) . " ";
    }

    // --- If isPrivate existe, take into account privacy 
    if (property_exists($obj,'isPrivate')) {
      $queryWhere.= ($queryWhere=='')?'':' and ';
      $queryWhere.= SqlElement::getPrivacyClause($obj);
    }
    // --- When browsing Docments throught directory view, limit list of Documents to currently selected Directory
    if ($objectClass=='Document') {
    	if (array_key_exists('Directory',$_SESSION) and ! $quickSearch) {
    		$queryWhere.= ($queryWhere=='')?'':' and ';
        $queryWhere.= $obj->getDatabaseTableName() . '.' . $obj->getDatabaseColumnName('idDocumentDirectory') . "='" . $_SESSION['Directory'] . "'";
    	}
    }
    
    // --- Apply sorting filers --------------------------------------------------------------
    // --- 1) retrieve corresponding filter clauses depending on context
    $arrayFilter=array();
    if (! $quickSearch) {
      if (! $comboDetail and is_array( getSessionUser()->_arrayFilters)) {
        if (array_key_exists($objectClass, getSessionUser()->_arrayFilters)) {
        	$arrayFilter=getSessionUser()->_arrayFilters[$objectClass];
        }
      } else if ($comboDetail and is_array( getSessionUser()->_arrayFiltersDetail)) {
        if (array_key_exists($objectClass, getSessionUser()->_arrayFiltersDetail)) {
          $arrayFilter=getSessionUser()->_arrayFiltersDetail[$objectClass];
        }
      }
    }
    // --- 2) sort from index checked in List Header (only used for printing, as direct filter is done on client side)
    $sortIndex=null;   
    if ($print) {
      if (array_key_exists('sortIndex', $_REQUEST)) {
        $sortIndex=$_REQUEST['sortIndex']+1;
        $sortWay=(array_key_exists('sortWay', $_REQUEST))?$_REQUEST['sortWay']:'asc';
        $nb=0;
        $numField=0;
        foreach ($array as $val) {
          $fld=htmlExtractArgument($val, 'field');      
          if ($fld and $fld!="photo") {            
            $numField+=1;
            if ($sortIndex and $sortIndex==$numField) {
              $queryOrderBy .= ($queryOrderBy=='')?'':', ';
              //if (Sql::isPgsql()) $fld='"'.$fld.'"';
              $queryOrderBy .= " " . $fld . " " . $sortWay;
            }
          }
        }
      }
    }
    // 3) sort from Filter Criteria
    if (! $quickSearch) {
	    foreach ($arrayFilter as $crit) {
	      if ($crit['sql']['operator']=='SORT') {
	        $doneSort=false;
          $split=explode('_', $crit['sql']['attribute']);
	        if (count($split)>1 ) {
	          $externalClass=$split[0];
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();          
	          $idTab+=1;
	          $externalTableAlias = 'T' . $idTab;
	          $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
	           ' on ( ' . $externalTableAlias . ".refType='" . get_class($obj) . "' and " .  $externalTableAlias . '.refId = ' . $table . '.id )';
	          $queryOrderBy .= ($queryOrderBy=='')?'':', ';
            $queryOrderBy .= " " . $externalTableAlias . '.' . $split[1] 
            . " " . $crit['sql']['value'];
	          $doneSort=true;
          }
	        if (substr($crit['sql']['attribute'],0,2)=='id' and strlen($crit['sql']['attribute'])>2 ) {
	          $externalClass = substr($crit['sql']['attribute'],2);
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();
	          $sortColumn='id';          
	          if (property_exists($externalObj,'sortOrder')) {
	          	$sortColumn=$externalObj->getDatabaseColumnName('sortOrder');
	          } else {
	          	$sortColumn=$externalObj->getDatabaseColumnName('name');
	          }
            $idTab+=1;
            $externalTableAlias = 'T' . $idTab;
            $queryOrderBy .= ($queryOrderBy=='')?'':', ';
            $queryOrderBy .= " " . $externalTableAlias . '.' . $sortColumn
               . " " . str_replace("'","",$crit['sql']['value']);
            $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
            ' on ' . $table . "." . $obj->getDatabaseColumnName('id' . $externalClass) . 
            ' = ' . $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('id');
            $doneSort=true;
	        }
	        if (! $doneSort) {
	          $queryOrderBy .= ($queryOrderBy=='')?'':', ';
	          $queryOrderBy .= " " . $table . "." . $obj->getDatabaseColumnName($crit['sql']['attribute']) 
	                             . " " . $crit['sql']['value'];
	        }
	      }
	    }
    }
    // --- Rest of filter selection will be done later, after building select clause
    
    // ====================== Build restriction clauses ================================================
    // --- Build select clause, and eventualy extended From clause and Where clause
    $numField=0;
    $formatter=array();
    $arrayWidth=array();
    if ($outMode=='csv') {
    	$obj=new $objectClass();
    	$clause=$obj->buildSelectClause(false,$hiddenFields);
    	$querySelect .= ($querySelect=='')?'':', ';
    	$querySelect .= $clause['select'];
    	//$queryFrom .= ($queryFrom=='')?'':', ';
    	$queryFrom .= $clause['from'];
    } else {
	    foreach ($array as $val) {
	      //$sp=preg_split('field=', $val);
	      //$sp=explode('field=', $val);
	      $fld=htmlExtractArgument($val, 'field');      
	      if ($fld) {
	        $numField+=1;    
	        $formatter[$numField]=htmlExtractArgument($val, 'formatter');
	        $from=htmlExtractArgument($val, 'from');
	        $arrayWidth[$numField]=htmlExtractArgument($val, 'width');
	        $querySelect .= ($querySelect=='')?'':', ';
	        if (substr($formatter[$numField],0,5)=='thumb' and substr($formatter[$numField],0,9)!='thumbName') {
            $querySelect.=substr($formatter[$numField],5).' as ' . $fld;;
            continue;
          }    
	        if (strlen($fld)>9 and substr($fld,0,9)=="colorName") {
	          $idTab+=1;
	          // requested field are colorXXX and nameXXX => must fetch the from external table, using idXXX
	          $externalClass = substr($fld,9);
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();
	          $externalTableAlias = 'T' . $idTab;
	          if (Sql::isPgsql()) {
	          	//$querySelect .= 'concat(';
		          if (property_exists($externalObj,'sortOrder')) {
	              $querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('sortOrder');
	              $querySelect .=  " || '#split#' ||";
	            }
	            $querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('name');
	            $querySelect .=  " || '#split#' ||";
	            $querySelect .= "COALESCE(".$externalTableAlias . '.' . $externalObj->getDatabaseColumnName('color').",'')";
	            //$querySelect .= ') as "' . $fld .'"';
	            $querySelect .= ' as "' . $fld .'"'; 
	          } else {
	            $querySelect .= 'convert(concat(';
	            if (property_exists($externalObj,'sortOrder')) {
                $querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('sortOrder');
                $querySelect .=  ",'#split#',";
	            }
	            $querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('name');
	            $querySelect .=  ",'#split#',";
	            $querySelect .= "COALESCE(".$externalTableAlias . '.' . $externalObj->getDatabaseColumnName('color').",'')";
	            $querySelect .= ') using utf8) as ' . $fld;
	          }	          
	          $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
	            ' on ' . $table . "." . $obj->getDatabaseColumnName('id' . $externalClass) . 
	            ' = ' . $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('id');
	        } else if (strlen($fld)>4 and substr($fld,0,4)=="name" and !$from) {
	          $idTab+=1;
	          // requested field is nameXXX => must fetch it from external table, using idXXX
	          $externalClass = substr($fld,4);
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();
	          $externalTableAlias = 'T' . $idTab;
	          if (property_exists($externalObj, '_calculateForColumn') and isset($externalObj->_calculateForColumn['name'])) {
	          	$fieldCalc=$externalObj->_calculateForColumn["name"];
	          	$fieldCalc=str_replace("(","($externalTableAlias.",$fieldCalc);
	          	//$calculated=true;
	          	$querySelect .= $fieldCalc . ' as ' . ((Sql::isPgsql())?'"'.$fld.'"':$fld);
	          } else {
	          	$querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('name') . ' as ' . ((Sql::isPgsql())?'"'.$fld.'"':$fld);
	          }
	          if (substr($formatter[$numField],0,9)=='thumbName') {
	            $numField+=1;
	            $formatter[$numField]='';
	            $arrayWidth[$numField]='';
	            $querySelect .= ', '.$table . "." . $obj->getDatabaseColumnName('id' . $externalClass) . ' as id' . $externalClass;
	          }
	          //if (! stripos($queryFrom,$externalTable)) {
	            $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
	              ' on ' . $table . "." . $obj->getDatabaseColumnName('id' . $externalClass) . 
	              ' = ' . $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('id');
	          //}   
	        } else if (strlen($fld)>5 and substr($fld,0,5)=="color") {
	          $idTab+=1;
	          // requested field is colorXXX => must fetch it from external table, using idXXX
	          $externalClass = substr($fld,5);
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();
	          $externalTableAlias = 'T' . $idTab;
	          $querySelect .= $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('color') . ' as ' . ((Sql::isPgsql())?'"'.$fld.'"':$fld);
	          //if (! stripos($queryFrom,$externalTable)) {
	            $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias . 
	              ' on ' . $table . "." . $obj->getDatabaseColumnName('id' . $externalClass) . 
	              ' = ' . $externalTableAlias . '.' . $externalObj->getDatabaseColumnName('id');
	          //}
	        } else if ($from) {
	          // Link to external table
	          $externalClass = $from;
	          $externalObj=new $externalClass();
	          $externalTable = $externalObj->getDatabaseTableName();          
	          $externalTableAlias = strtolower($externalClass);
	          if (! stripos($queryFrom,'left join ' . $externalTable . ' as ' . $externalTableAlias)) {
	            $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
	              ' on (' . $externalTableAlias . '.refId=' . $table . ".id" . 
	              ' and ' . $externalTableAlias . ".refType='" . $objectClass . "')";
	          }
	          if (strlen($fld)>4 and substr($fld,0,4)=="name") {
              $idTab+=1;
              // requested field is nameXXX => must fetch it from external table, using idXXX
              $externalClassName = substr($fld,4);
              $externalObjName=new $externalClassName();
              $externalTableName = $externalObjName->getDatabaseTableName();
              $externalTableAliasName = 'T' . $idTab;
              $querySelect .= $externalTableAliasName . '.' . $externalObjName->getDatabaseColumnName('name') . ' as ' . ((Sql::isPgsql())?'"'.$fld.'"':$fld);
              $queryFrom .= ' left join ' . $externalTableName . ' as ' . $externalTableAliasName .
                  ' on ' . $externalTableAlias . "." . $externalObj->getDatabaseColumnName('id' . $externalClassName) . 
                  ' = ' . $externalTableAliasName . '.' . $externalObjName->getDatabaseColumnName('id');   
            } else {
            	$querySelect .=  $externalTableAlias . '.' . $externalObj->getDatabaseColumnName($fld) . ' as ' . ((Sql::isPgsql())?'"'.$fld.'"':$fld);
            } 	
            
	          if ( property_exists($externalObj,'wbsSortable') 
	            and strpos($queryOrderBy,$externalTableAlias . "." . $externalObj->getDatabaseColumnName('wbsSortable'))===false) {
	            $queryOrderBy .= ($queryOrderBy=='')?'':', ';
	            $queryOrderBy .= " " . $externalTableAlias . "." . $externalObj->getDatabaseColumnName('wbsSortable') . " ";
	          } 
	        } else {      
	          // Simple field to add to request 
	          $querySelect .= $table . '.' . $obj->getDatabaseColumnName($fld) . ' as ' . ((Sql::isPgsql())?'"'.strtr($fld,'.','_').'"':strtr($fld,'.','_'));
	        }
	      }
	    }
	    if (property_exists($obj,'idProject')) {
	      $querySelect.=','.$table.'.idProject as idproject';
	    }
    }
    // --- build order by clause
    if ($objectClass=='DocumentDirectory') {
    	$queryOrderBy .= ($queryOrderBy=='')?'':', ';
    	$queryOrderBy .= " " . $table . "." . $obj->getDatabaseColumnName('location');
    } else if ( property_exists($objectClass,'wbsSortable')) {
      $queryOrderBy .= ($queryOrderBy=='')?'':', ';
      $queryOrderBy .= " " . $table . "." . $obj->getDatabaseColumnName('wbsSortable');
    } else if (property_exists($objectClass,'sortOrder')) {
      $queryOrderBy .= ($queryOrderBy=='')?'':', ';
      $queryOrderBy .= " " . $table . "." . $obj->getDatabaseColumnName('sortOrder');
    } else {
      $queryOrderBy .= ($queryOrderBy=='')?'':', ';
      $queryOrderBy .= " " . $table . "." . $obj->getDatabaseColumnName('id') . " desc";
    }
    
    
    // --- Check for an advanced filter (stored in User)
    foreach ($arrayFilter as $crit) {
      if ($crit['sql']['operator']!='SORT') { // Sorting already applied above
      	$split=explode('_', $crit['sql']['attribute']);
      	$critSqlValue=$crit['sql']['value'];
      	if (substr($crit['sql']['attribute'], -4, 4) == 'Work') {
      	  if ($objectClass=='Ticket') {
      	    $critSqlValue=Work::convertImputation(trim($critSqlValue,"'"));
      	  } else {
      	    $critSqlValue=Work::convertWork(trim($critSqlValue,"'"));
      	  }
      	}
      	if ($crit['sql']['operator']=='IN' 
      	and ($crit['sql']['attribute']=='idProduct' or $crit['sql']['attribute']=='idProductOrComponent' or $crit['sql']['attribute']=='idComponent')) {
          $critSqlValue=str_replace(array(' ','(',')'), '', $critSqlValue);
      		$splitVal=explode(',',$critSqlValue);
      		$critSqlValue='(0';
      		foreach ($splitVal as $idP) {
      			$prod=new Product($idP);
      			$critSqlValue.=', '.$idP;
      	    $list=$prod->getRecursiveSubProductsFlatList(false, false); // Will work only if selected is Product, not for Component 
      	    foreach ($list as $idPrd=>$namePrd) {
      	    	$critSqlValue.=', '.$idPrd;
      	    }
      		}      		
      		$critSqlValue.=')';
      	}
        if (count($split)>1 ) {
          $externalClass=$split[0];
          $externalObj=new $externalClass();
          $externalTable = $externalObj->getDatabaseTableName();          
          $idTab+=1;
          $externalTableAlias = 'T' . $idTab;
          $queryFrom .= ' left join ' . $externalTable . ' as ' . $externalTableAlias .
           ' on ( ' . $externalTableAlias . ".refType='" . get_class($obj) . "' and " .  $externalTableAlias . '.refId = ' . $table . '.id )';
          $queryWhere.=($queryWhere=='')?'':' and ';
          $queryWhere.=$externalTableAlias . "." . $split[1] . ' ' 
                 . $crit['sql']['operator'] . ' '
                 . $critSqlValue;
        } else {
          $queryWhere.=($queryWhere=='')?'':' and ';
          if ($crit['sql']['operator']!=' exists ') {
            $queryWhere.="(".$table . "." . $crit['sql']['attribute'] . ' ';
          } 
		      $queryWhere.= $crit['sql']['operator'] . ' ' . $critSqlValue;
		      if (strlen($crit['sql']['attribute'])>=9 
		      and substr($crit['sql']['attribute'],0,2)=='id'
		      and ( substr($crit['sql']['attribute'],-7)=='Version' and SqlElement::is_a(substr($crit['sql']['attribute'],2), 'Version') )
		      and $crit['sql']['operator']=='IN') {
		      	$scope=substr($crit['sql']['attribute'],2);
		      	$vers=new OtherVersion();
		      	$queryWhere.=" or exists (select 'x' from ".$vers->getDatabaseTableName()." VERS "
		      	  ." where VERS.refType=".Sql::str($objectClass)." and VERS.refId=".$table.".id and scope=".Sql::str($scope)
		      	  ." and VERS.idVersion IN ".$critSqlValue
		      	  .")";
		      }
		      if ($crit['sql']['operator']=='NOT IN') {
		        $queryWhere.=" or ".$table . "." . $crit['sql']['attribute']. " IS NULL ";
		      }
		      if ($crit['sql']['operator']!=' exists ') {
		        $queryWhere.=")";
		      }
        }
      }
    }
    
    $list=Plugin::getEventScripts('query',$objectClass);
    foreach ($list as $script) {
      require $script; // execute code
    }
    
    // ==================== Constitute query and execute ============================================================
    // --- Buimd where from "Select", "From", "Where" and "Order by" clauses built above
    $queryWhere=($queryWhere=='')?' 1=1':$queryWhere;
    $query='select ' . $querySelect 
         . ' from ' . $queryFrom
         . ' where ' . $queryWhere 
         . ' order by' . $queryOrderBy;   
    // --- Execute query
    $result=Sql::query($query);
    if (isset($debugJsonQuery) and $debugJsonQuery) { // Trace in configured to
       debugTraceLog("jsonQuery: ".$query); // Trace query
       debugTraceLog("  => error (if any) = ".Sql::$lastQueryErrorCode.' - '.Sql::$lastQueryErrorMessage);
       debugTraceLog("  => number of lines returned = ".Sql::$lastQueryNbRows);
    }
    $nbRows=0;
    $dataType=array();
    
    // --- Format for "printing" 
    if ($print) {
    	if ($outMode=='csv') { // CSV mode
    		$exportReferencesAs='name';
    		if (isset($_REQUEST['exportReferencesAs'])) {
    		  $exportReferencesAs=$_REQUEST['exportReferencesAs'];
    		}
    		$exportHtml=false;
    		if (isset($_REQUEST['exportHtml']) and $_REQUEST['exportHtml']=='1') {
    		  $exportHtml=true;
    		}
    		$csvSep=Parameter::getGlobalParameter('csvSeparator');
    		$csvQuotedText=true;
    		$obj=new $objectClass();
    		$first=true;
    		$arrayFields=array();
    	  //if (Sql::isPgsql()) {
    	  	$arrayFields=$obj->getLowercaseFieldsArray();
    	  	//$arrayFieldsWithCase=$obj->getFieldsArray();        
        //}
    		while ($line = Sql::fetchLine($result)) {
    			if ($first) {
	    			foreach ($line as $id => $val) {
	    				$colId=$id;
	    				if (Sql::isPgsql() and isset($arrayFields[$id])) {
	    					$colId=$arrayFields[$id];
	    				}
	    				$val=encodeCSV($obj->getColCaption($colId));
	    				if (substr($id,0,9)=='idContext' and strlen($id)==10) {
                $ctx=new ContextType(substr($id,-1));
                $val=encodeCSV($ctx->name);
              } 
	    				//$val=encodeCSV($id);
	    				$val=str_replace($csvSep,' ',$val);
	            //if ($id!='id') { echo $csvSep ;}
	    				echo $val.$csvSep;
	            $dataType[$id]=$obj->getDataType($id);
	            $dataLength[$id]=$obj->getDataLength($id);
	          }
	          echo "\r\n";
    			}
    			foreach ($line as $id => $val) {
    				$foreign=false;
    				if (substr($id, 0,2)=='id' and strlen($id)>2) {
    					$class=substr($arrayFields[strtolower($id)], 2);
    					if (ucfirst($class)==$class) {
    						$foreign=true;
    						if ($class=="TargetVersion" or $class=="TargetProductVersion" or $class=="TargetComponentVersion"
    						 or $class=="OriginalVersion" or $class=="OriginalProductVersion" or $class=="OriginalComponentVersion") $class='Version';
    						if ($exportReferencesAs=='name') {
    					    $val=SqlList::getNameFromId($class, $val);
    						}
    					}
    				}
    				if ($dataLength[$id]>4000 and !$exportHtml) {
    				  $text=new Html2Text($val);
    				  $val=$text->getText();
    				}
    				$val=encodeCSV($val);
    				if ($csvQuotedText) {
    				  $val=str_replace('"','""',$val);	
    				}
            //if ($id!='id') { echo $csvSep ;}
            if ( ($dataType[$id]=='varchar' or $foreign) and $csvQuotedText) { 
              echo '"' . $val . '"'.$csvSep;
            } else if ( ($dataType[$id]=='decimal')) {
            	echo formatNumericOutput($val).$csvSep;
            } else {
              $val=str_replace($csvSep,' ',$val);
            	echo $val.$csvSep;
            }
    			}
    			$first=false;
    			echo "\r\n";
    		}
    		if ($first) {
    			echo encodeCSV(i18n("reportNoData")); 
    		}
    	} else { // NON CSV mode : includes pure print and 'pdf' ($outMode=='pdf') mode
        echo '<br/>';
        echo '<div class="reportTableHeader" style="width:99%; font-size:150%;border: 0px solid #000000;">' . i18n('menu'.$objectClass) . '</div>';
        echo '<br/>';
	      echo '<table style="width:'.(($outMode=='pdf')?'950px':'100%').'">';
	      echo '<tr>';
	      $layout=str_ireplace('width="','style="border:1px solid black;width:',$layout);
	      $layout=str_ireplace('<th ','<th class="reportHeader" ',$layout);
	      echo $layout;
	      echo '</tr>';
	      if (Sql::$lastQueryNbRows > 0) {
	        $hiddenField='<span style="color:#AAAAAA">(...)</span>';
	        while ($line = Sql::fetchLine($result)) {
	          echo '<tr>';
	          $numField=0;
	          $idProject=($objectClass=='Project')?$line['id']:((isset($line['idproject']))?$line['idproject']:null);
	          foreach ($line as $id => $val) {
	            $numField+=1;
	            $disp="";
	            if (!isset($arrayWidth[$numField]) or $arrayWidth[$numField]=='') continue;
	            if ($formatter[$numField]=="colorNameFormatter") {
	              $disp=colorNameFormatter($val);
	            } else if ($formatter[$numField]=="booleanFormatter") {
	              $disp=booleanFormatter($val);
	            } else if ($formatter[$numField]=="colorFormatter") {
	              $disp=colorFormatter($val);
	            } else if ($formatter[$numField]=="dateTimeFormatter") {
	              $disp=dateTimeFormatter($val);
	            } else if ($formatter[$numField]=="dateFormatter") {
	              $disp=dateFormatter($val);
	            } else if ($formatter[$numField]=="timeFormatter") {
                $disp=timeFormatter($val);
	            } else if ($formatter[$numField]=="translateFormatter") {
	              $disp=translateFormatter($val);
	            } else if ($formatter[$numField]=="percentFormatter") {
	              $disp=percentFormatter($val,($outMode=='pdf')?false:true);
	            } else if ($formatter[$numField]=="numericFormatter") {
	              $disp=numericFormatter($val);
	            } else if ($formatter[$numField]=="sortableFormatter") {
	              $disp=sortableFormatter($val);
	            } else if ($formatter[$numField]=="workFormatter") {
	              if ($idProject and ! $user->getWorkVisibility($idProject,$id)) {
	                $disp=$hiddenField;
	              } else {
                  $disp=workFormatter($val);
	              }
              } else if ($formatter[$numField]=="costFormatter") {
                if ($idProject and ! $user->getCostVisibility($idProject,$id)) {
                  $disp=$hiddenField;
                } else {
                  $disp=costFormatter($val);
                }
              } else if ($formatter[$numField]=="iconFormatter") {
                $disp=iconFormatter($val);
              } else if (substr($formatter[$numField],0,9)=='thumbName') {
                //$disp=thumbFormatter($objectClass,$line['id'],substr($formatter[$numField],5));
                $nameClass=substr($id,4);
                if (Sql::isPgsql()) $nameClass=strtolower($nameClass);
                if ($val and $showThumb) {
                  $size=substr($formatter[$numField],9);
                  $radius=round($size/2,0);
                  $thumbUrl=Affectable::getThumbUrl('Affectable',$line['id'.$nameClass], substr($formatter[$numField],9),false, ($outMode=='pdf')?true:false);
                  $disp='<div style="text-align:left;">';
                  $disp.='<img style="border-radius:'.$radius.'px;height:'.$size.'px;float:left" src="'.$thumbUrl.'"';
                  $disp.='/>';
                  $disp.='<div style="margin-left:'.($size+2).'px;">'.$val.'</div>';
                  $disp.='</div>';
                } else {
                  $disp="";
                }
              } else if (substr($formatter[$numField],0,5)=='thumb') {
	            	$disp=thumbFormatter($objectClass,$line['id'],substr($formatter[$numField],5));
	            } else if ($formatter[$numField]=="privateFormatter") {
	              $disp=privateFormatter($val);
	            } else {
	              $disp=htmlEncode($val);
	            }
	            
	            echo '<td class="tdListPrint" style="width:' . $arrayWidth[$numField] . ';">' . $disp . '</td>';
	          }
	          echo '</tr>';       
	        }
	      }
	      echo "</table>";
	      //echo "</div>";
    	}
    } else {
      // return result in json format
      echo '{"identifier":"id",' ;
      echo ' "items":[';
      if (Sql::$lastQueryNbRows > 0) {        
        while ($line = Sql::fetchLine($result)) {
          echo (++$nbRows>1)?',':'';
          echo  '{';
          $nbFields=0;
          $idProject=($objectClass=='Project')?$line['id']:((isset($line['idproject']))?$line['idproject']:null);
          foreach ($line as $id => $val) {
            if ($id=='idproject') continue;
            echo (++$nbFields>1)?',':'';
            $numericLength=0;
            if ($id=='id') {
            	$numericLength=6;
            } else if ($formatter[$nbFields]=='percentFormatter') {
            	$numericLength=3;
            	if ($val<0) $numericLenght=0;
            } else if ($formatter[$nbFields]=='workFormatter') {
              $numericLength=9;
              if ($val<0) $numericLength=0;
              if ($idProject and ! $user->getWorkVisibility($idProject,$id)) {
                $val='-';
                $numericLength=0;
              }
            } else if ($formatter[$nbFields]=='costFormatter') {
            	$numericLength=9;
            	if ($val<0) $numericLength=0;
            	if ($idProject and ! $user->getCostVisibility($idProject,$id)) {
            	  $val='-';
            	  $numericLength=0;
            	}
            } else if ($formatter[$nbFields]=='numericFormatter') {
            	$numericLength=9;
            	if ($val<0) $numericLength=0;
            }
            if ($id=='colorNameRunStatus') {
            	$split=explode('#',$val);
            	foreach ($split as $ix=>$sp) {
            	  if ($ix==0) {
            	  	$val=$sp;
            	  } else if ($ix==2) {
            		  $val.='#'.i18n($sp);	
            	  } else {
            	  	$val.='#'.$sp;
            	  }
            	} 
            }
            if (substr($formatter[$nbFields],0,5)=='thumb') {             
            	if (substr($formatter[$nbFields],0,9)=='thumbName') {
            	  $nameClass=substr($id,4);
            	  if (Sql::isPgsql()) $nameClass=strtolower($nameClass);
            	  if ($val and $showThumb) {
            	    $val=Affectable::getThumbUrl('Affectable',$line['id'.$nameClass], substr($formatter[$nbFields],9)).'#'.$val;
            	  } else {
            	    $val="####$val";
            	  }  	  
            	} else if (Affectable::isAffectable($objectClass)) {
            		$val=Affectable::getThumbUrl($objectClass,$line['id'], $val);
            	} else {          	
	            	$image=SqlElement::getSingleSqlElementFromCriteria('Attachment', array('refType'=>$objectClass, 'refId'=>$line['id']));
	              if ($image->id and $image->isThumbable()) {
	            	  $val=getImageThumb($image->getFullPathFileName(),$val).'#'.htmlEncodeJson($image->id, 6).'#'.htmlEncodeJson($image->fileName); 
	              } else {
	              	$val="##";
	              }
            	}
            	
            }            
            echo '"' . htmlEncode($id) . '":"' . htmlEncodeJson($val, $numericLength) . '"';
          }
          echo '}';       
        }
      }
       echo ']';
      //echo ', "numberOfRow":"' . $nbRows . '"' ;
      echo ' }';
    }
    
?>
