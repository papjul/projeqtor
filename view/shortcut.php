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

require_once "../tool/projeqtor.php";
$proj='';
if (array_key_exists('project',$_SESSION)) {
  $proj=$_SESSION['project'];
}
$prj=new Project($proj);
$lstProjSelect=$prj->getRecursiveSubProjectsFlatList(true,true);
$lstProjVisible=getSessionUser()->getVisibleProjects(); 
$lstProj=array_intersect_assoc($lstProjSelect,$lstProjVisible);
echo '<table style="width: 100%;">';
foreach ($lstProj as $prjId=>$prjName) {
  $att=new Attachment();
  $lstAtt=$att->getSqlElementsFromCriteria(array('refType'=>'Project','refId'=>$prjId, 'type'=>'link'));
  //* $lstAtt Attachment[]
  if (count($lstAtt)>0) {
    echo '<tr><th class="linkHeader">';
    echo htmlEncode($prjName);
    echo '</th></tr>';
    foreach ($lstAtt as $att) {
      echo '<tr><td class="linkData">';
        echo '<a href="' . htmlEncode($att->link) . '" target="#" class="hyperlink" title="' . htmlEncode($att->link) . '">';
        echo ($att->description)?htmlEncode($att->description):htmlEncode($att->link);
        echo '</a>';
      echo '</td></tr>';
    }
  }
}
echo "</table>";
