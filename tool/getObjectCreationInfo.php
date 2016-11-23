<?PHP
/**
 * * COPYRIGHT NOTICE *********************************************************
 *
 * Copyright 2014-2015 ProjeQtOr - Pascal BERNARD - support@projeqtor.org
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
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * ProjeQtOr. If not, see <http://www.gnu.org/licenses/>.
 *
 * You can get complete code of ProjeQtOr, other resource, help and information
 * about contributors at http://www.projeqtor.org
 *
 * ** DO NOT REMOVE THIS NOTICE ***********************************************
 */

/**
 * ===========================================================================
 * Get creation information for given object
 */
require_once "../tool/projeqtor.php";
require_once "../tool/formatter.php";
scriptLog ( '   ->/tool/getObjectCreationInfo.php' );
if (! isset($obj)) {
	$objectClass = $_REQUEST ['objectClass'];
	Security::checkValidClass($objectClass);

	$objectId = $_REQUEST ['objectId'];
	$obj = new $objectClass ( $objectId ); // validated to be numeric value in SqlElement base constructor
} else {
  $objectClass=get_class($obj);
  $objectId=$obj->id;
}
if (! isset($comboDetail)) {
  $comboDetail=false;
}
$updateRight=securityGetAccessRightYesNo('menu' . $objectClass, 'update', $obj);
$canUpdateCreationInfo=false;
if ($obj->id and $updateRight) {
  $user=getSessionUser();
  $habil=SqlElement::getSingleSqlElementFromCriteria('habilitationOther', array('idProfile' => $user->getProfile($obj),'scope' => 'canUpdateCreation'));
  if ($habil) {
    $list=new ListYesNo($habil->rightAccess);
    if ($list->code == 'YES') {
      $canUpdateCreationInfo=true;
    }
  }
}
?>
<div style="float:left;">
<?php 
if (property_exists ( $obj, 'lastUpdateDateTime' ) && $obj->lastUpdateDateTime) {
  echo formatDateThumb(null,$obj->lastUpdateDateTime,'left',32,'Update');
}
?>
</div>
<div style="padding-right:16px;" <?php echo ($canUpdateCreationInfo)?'class="buttonDivCreationInfoEdit" onClick="changeCreationInfo();"':'';?>>
<?php 
if (!$comboDetail and $obj->id and property_exists ( $obj, 'idUser' )) {
  echo formatUserThumb($obj->idUser,SqlList::getNameFromId('Affectable', $obj->idUser),'Creator',32,'right',true);
  $creationDate='';
	if (property_exists ( $obj, 'creationDateTime' )) {
		$creationDate=$obj->creationDateTime;
	} else if (property_exists ( $obj, 'creationDate' )) {
		$creationDate=$obj->creationDate;
	}
	if ($creationDate) {
    echo formatDateThumb($creationDate,null,'right',32);
  }

}
if (property_exists ( $obj, 'isPrivate' )) {
  echo '<div style="position:absolute;top:0px;">';
  if ($obj->isPrivate) {
    echo '<img style="position:relative;left:60px;height:16px" src="../view/img/private.png" />';
  }
  echo '</div>';
}
?>
</div>