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

include_once("../tool/projeqtor.php");
$proj='*'; 
if (array_key_exists('project',$_SESSION)) {
  $proj=$_SESSION['project'];
} else {
  $_SESSION['project']="*";
}
$prj=new Project();
$prj->id='*';
//$cpt=$prj->countMenuProjectsList();
$limitToActiveProjects=true;
if (isset($_SESSION['projectSelectorShowIdle']) and $_SESSION['projectSelectorShowIdle']==1) {
  $limitToActiveProjects=false;
}
$subProjectsToDraw=$prj->drawSubProjects('selectedProject', false, true, $limitToActiveProjects);     
$cpt=substr_count($subProjectsToDraw,'<tr>');
$displayMode="standard";
$paramDisplayMode=Parameter::getUserParameter('projectSelectorDisplayMode');
if ($paramDisplayMode) {
  $_SESSION['projectSelectorDisplayMode']=$paramDisplayMode;
}
if (isset($_SESSION['projectSelectorDisplayMode'])) {
  $displayMode=$_SESSION['projectSelectorDisplayMode'];
}
?>
<?php if ($displayMode=='standard') {?>
<span maxsize="160px" style="position: absolute; left:75px; top:22px; height: 20px; width: 165px; color:#202020;" 
  dojoType="dijit.form.DropDownButton" 
  id="selectedProject" jsId="selectedProject" name="selectedProject" showlabel="true" class="">
  <span style="width:140px; text-align: left;">
    <div style="width:140px; overflow: hidden; text-align: left;" >
    <?php
if ($proj=='*') {
  echo '<i>' . i18n('allProjects') . '</i>';
} else {
  $projObject=new Project($proj);
  echo htmlEncode($projObject->name);
};
    ?>
    </div>
  </span>
  <span dojoType="dijit.TooltipDialog" class="white" <?php echo ($cpt>25)?'style="max-width:900px;"':'';?>>   
    <div <?php echo ($cpt>25)?'style="height: 500px; overflow-x: hidden; overflow-y: scroll;"':'';?>>    
    <?php 
      echo $subProjectsToDraw;
    ?>
    </div>       
  </span>
</span>
<?php } else if ($displayMode=='select') {?>
<select dojoType="dijit.form.FilteringSelect" class="input" 
   style="position: absolute; left:75px; top:25px; width: 165px;" 
   <?php echo autoOpenFilteringSelect();?>
   name="projectSelectorFiletering" id="projectSelectorFiletering" >
   <script type="dojo/connect" event="onChange" args="evt">
    if (this.isValid()) {
      setSelectedProject(this.value, this.displayedValue, null);
    }
  </script>
   <option value="*"><i><?php echo i18n("allProjects");?></i></option>
   <?php htmlDrawOptionForReference("idProject", $proj, null, true,null, null, $limitToActiveProjects);?>
</select>
<?php } else if($displayMode=="search") {?>
<select id="projectSelectorFiletering" class="input" style="position: absolute; left:75px; top:25px; width: 165px;"  
<?php echo autoOpenFilteringSelect();?>
name="projectSelectorFiletering" data-dojo-type="dijit.form.FilteringSelect"
    data-dojo-props="
        queryExpr: '*${0}*',
        autoComplete:false">
  <script type="dojo/connect" event="onChange" args="evt">
    if (this.isValid()) {
      setSelectedProject(this.value, this.displayedValue, null);
    }
  </script>
   <option value="*"><i><?php echo i18n("allProjects");?></i></option>
   <?php htmlDrawOptionForReference("idProject", $proj, null, true,null, null, $limitToActiveProjects);?>
</select>

<?php } else  {
  ?>

ERROR : Unknown display mode
<?php }?>