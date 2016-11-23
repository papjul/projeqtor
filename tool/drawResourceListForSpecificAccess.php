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
 * Acknowledge an operation
 */
if (!isset($user)) {
  $user=getSessionUser();
}
if ( ! isset($specific) or ! $specific) {
  errorLog("drawResourceListForSpecificAccess.php : specific variable not set");
  $specific="null"; // Avoid error
}
$table=array();
if (! $user->isResource) {
  $table[0]=' ';
}
if ($user->allSpecificRightsForProfilesOneOnlyValue($specific,'NO')) {
  $table[$user->id]=' ';
} else if ($user->allSpecificRightsForProfilesOneOnlyValue($specific,'ALL')) {
  $table=SqlList::getList('Resource');
} else if (($user->allSpecificRightsForProfilesOneOnlyValue($specific,'OWN')
    or $user->allSpecificRightsForProfilesOneOnlyValue($specific,'RES')) and $user->isResource ) {
  $table=array($user->id=>SqlList::getNameFromId('Resource', $user->id));
} else  {
  $table=array();
  $fullTable=SqlList::getList('Resource');
  foreach ($user->getAllSpecificRightsForProfiles($specific) as $right=>$profList) {
    if ( ($right=='OWN' or $right=='RES') and $user->isResource) {
      $table[$user->id]=SqlList::getNameFromId('Resource', $user->id);
    } else if ($right=='ALL' and in_array($user->idProfile, $profList)) {
      $table=$fullTable;
      break;
    } else if ($right=='ALL' or $right=='PRO') {
      $inClause='(0';
      foreach ($user->getSpecificAffectedProfiles() as $prj=>$prf) {
        if (in_array($prf, $profList)) {
          $inClause.=','.$prj;
        }
      }
      $inClause.=')';
      $crit='idProject in ' . $inClause;
      $aff=new Affectation();
      $lstAff=$aff->getSqlElementsFromCriteria(null, false, $crit, null, true);
      foreach ($lstAff as $id=>$aff) {
        if (array_key_exists($aff->idResource,$fullTable)) {
          $table[$aff->idResource]=$fullTable[$aff->idResource];
        }
      }
    }
  }
}
if (count($table)==0) {
  $table[$user->id]=' ';
}
asort($table);
foreach($table as $key => $val) {
  echo '<option value="' . $key . '"';
  if ( $key==$user->id and ! isset($specificDoNotInitialize)) { echo ' SELECTED '; }
  echo '>' . $val . '</option>';
}
?>
