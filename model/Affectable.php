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
 * ============================================================================ User is a resource that can connect to the application.
 */
require_once ('_securityCheck.php');
class Affectable extends SqlElement {
  
  // extends SqlElement, so has $id
  public $_sec_Description;
  public $id; // redefine $id to specify its visible place
  public $name;
  public $userName;
  public $idProfile;
  public $isResource;
  public $isUser;
  public $isContact;
  public $email;
  public $idTeam;
  public $idOrganization;
  public $idle;
  public $_constructForName=true;
  public $_calculateForColumn=array("name" => "coalesce(fullName,concat(name,' #'))","userName" => "coalesce(name,concat(fullName,' *'))");
  private static $_fieldsAttributes=array("name" => "required","isContact" => "readonly","isUser" => "readonly","isResource" => "readonly","idle" => "hidden");
  private static $_databaseTableName='resource';
  private static $_databaseColumnName=array('name' => 'fullName','userName' => 'name');
  private static $_databaseCriteria=array();
  
  private static $_visibilityScope;

  /**
   * ==========================================================================
   * Constructor
   *
   * @param $id the
   *          id of the object in the database (null if not stored yet)
   * @return void
   */
  function __construct($id=NULL,$withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
    if ($this->id and !$this->name and $this->userName) {
      $this->name=$this->userName;
    }
  }

  /**
   * ==========================================================================
   * Destructor
   *
   * @return void
   */
  function __destruct() {
    parent::__destruct();
  }
  
  // ============================================================================**********
  // GET STATIC DATA FUNCTIONS
  // ============================================================================**********
  
  /**
   * ========================================================================
   * Return the specific databaseTableName
   *
   * @return the databaseTableName
   */
  protected function getStaticDatabaseTableName() {
    $paramDbPrefix=Parameter::getGlobalParameter('paramDbPrefix');
    return $paramDbPrefix . self::$_databaseTableName;
  }

  /**
   * ========================================================================
   * Return the specific databaseTableName
   *
   * @return the databaseTableName
   */
  protected function getStaticDatabaseColumnName() {
    return self::$_databaseColumnName;
  }

  /**
   * ========================================================================
   * Return the specific database criteria
   *
   * @return the databaseTableName
   */
  protected function getStaticDatabaseCriteria() {
    return self::$_databaseCriteria;
  }

  /**
   * ==========================================================================
   * Return the specific fieldsAttributes
   *
   * @return the fieldsAttributes
   */
  protected function getStaticFieldsAttributes() {
    return self::$_fieldsAttributes;
  }

  // ============================================================================**********
  // THUMBS & IMAGES
  // ============================================================================**********
  
  /**
   * 
   * @param unknown $classAffectable
   * @param unknown $idAffectable
   * @param string $fileFullName
   */
  public static function generateThumbs($classAffectable, $idAffectable, $fileFullName=null) {
    $sizes=array(16,22,32,48,80); // sizes to generate, may be used somewhere
    $thumbLocation='../files/thumbs';
    $attLoc=Parameter::getGlobalParameter('paramAttachmentDirectory');
    if (!$fileFullName) {
      $image=SqlElement::getSingleSqlElementFromCriteria('Attachment', array('refType' => 'Resource','refId' => $idAffectable));
      if ($image->id) {
        $fileFullName=$image->subDirectory . $image->fileName;
      }
    }
    $fileFullName=str_replace('${attachmentDirectory}', $attLoc, $fileFullName);
    $fileFullName=str_replace('\\', '/', $fileFullName);
    if ($fileFullName and isThumbable($fileFullName)) {
      foreach ( $sizes as $size ) {
        $thumbFile=$thumbLocation . "/Affectable_$idAffectable/thumb$size.png";
        createThumb($fileFullName, $size, $thumbFile, true);
      }
    }
  }

  public static function generateAllThumbs() {
    $affList=SqlList::getList('Affectable', 'name', null, true);
    foreach ( $affList as $id => $name ) {
      self::generateThumbs('Affectable', $id, null);
    } 
  }

  public static function deleteThumbs($classAffectable, $idAffectable, $fileFullName=null) {
    $thumbLocation='../files/thumbs/Affectable_' . $idAffectable;
    purgeFiles($thumbLocation, null);
  }

  public static function getThumbUrl($objectClass, $affId, $size, $nullIfEmpty=false, $withoutUrlExtra=false) {
    $thumbLocation='../files/thumbs';
    $file="$thumbLocation/Affectable_$affId/thumb$size.png";
    if (file_exists($file)) {
      if ($withoutUrlExtra) {
        return $file;
      } else {
        $cache=filemtime($file);
        return "$file?nocache=".$cache."#$affId#&nbsp;#Affectable";
      }
    } else {
      if ($nullIfEmpty) {
        return null;
      } else {
        if ($withoutUrlExtra) {
          return "../view/img/Affectable/thumb$size.png";
        } else {
          return "../view/img/Affectable/thumb$size.png#0#&nbsp;#Affectable";
        }
      }
    }
  }
  
  public static function showBigImageEmpty($extraStylePosition, $canAdd=true) {
    $result='<div style="position: absolute;'.$extraStylePosition.';'
      .'border-radius:40px;width:80px;height:80px;border: 1px solid grey;color: grey;font-size:80%;'
      .'text-align:center;'; 
    if ($canAdd) {
      $result.='cursor: pointer;"  onClick="addAttachment(\'file\');" title="'.i18n('addPhoto').'">';
      $result.='<br/><br/><br/>'.i18n('addPhoto').'</div>';
    } else {
      $result.='" ></div>';
    }
    return $result;
  }
  public static function showBigImage($extraStylePosition,$affId,$filename, $attachmentId) {
    $result='<div style="position: absolute;'.$extraStylePosition.'; border-radius:40px;width:80px;height:80px;border: 1px solid grey;">'
      . '<img style="border-radius:40px;" src="'. Affectable::getThumbUrl('Resource', $affId, 80).'" '
      . ' title="'.$filename.'" style="cursor:pointer"'
      . ' onClick="showImage(\'Attachment\',\''.$attachmentId.'\',\''.htmlEncode($filename,'protectQuotes').'\');" /></div>';
    return $result;
  }
  public static function drawSpecificImage($class,$id, $print, $outMode, $largeWidth) {
    $result="";
    $image=SqlElement::getSingleSqlElementFromCriteria('Attachment', array('refType'=>'Resource', 'refId'=>$id));
    if ($image->id and $image->isThumbable()) {
      if (!$print) {
        //$result.='<tr style="height:20px;">';
        //$result.='<td class="label">'.i18n('colPhoto').'&nbsp;:&nbsp;</td>';
        //$result.='<td>&nbsp;&nbsp;';
        $result.='<span class="label" style="position: absolute;top:28px;right:105px;">';
        $result.=i18n('colPhoto').'&nbsp;:&nbsp;';
        $canUpdate=securityGetAccessRightYesNo('menu'.$class, 'update') == "YES";
        if ($id==getSessionUser()->id) $canUpdate=true;
        if ($canUpdate) {
          //$result.='<img src="css/images/smallButtonRemove.png" class="roundedButtonSmall" style="height:12px" '
          //    .'onClick="removeAttachment('.htmlEncode($image->id).');" title="'.i18n('removePhoto').'" class="smallButton"/>';
          $result.= '<span onClick="removeAttachment('.htmlEncode($image->id).');" title="'.i18n('removePhoto').'" >';
          $result.= formatSmallButton('Remove');
          $result.= '</span>';
        }
        
        $horizontal='right:10px';
        $top='30px';
        $result.='</span>';
      } else {
        if ($outMode=='pdf') {
          $horizontal='left:450px';
          $top='100px';
        } else {
          $horizontal='left:400px';
          $top='70px';
        }
      }
      $extraStyle='top:30px;'.$horizontal;
      $result.=Affectable::showBigImage($extraStyle,$id,$image->fileName,$image->id);
      if (!$print) {
        //$result.='</td></tr>';
      }
    } else {
      if ($image->id) {
        $image->delete();
      }
      if (!$print) {
        $horizontal='right:10px';
        //$result.='<tr style="height:20px;">';
        //$result.='<td class="label">'.i18n('colPhoto').'&nbsp;:&nbsp;</td>';
        //$result.='<td>&nbsp;&nbsp;';
        $result.='<span class="label" style="position: absolute;top:28px;right:105px;">';
        $result.=i18n('colPhoto').'&nbsp;:&nbsp;';
        $canUpdate=securityGetAccessRightYesNo('menu'.$class, 'update') == "YES";
        if ($id==getSessionUser()->id) $canUpdate=true;
        if ($canUpdate) {
          //KEVIN
         $result.= '<span onClick="addAttachment(\'file\');"title="' . i18n('addPhoto') .'" >';
         $result.= formatSmallButton('Add');
         $result.= '</span>';
        }
        $result.='</span>';
        $extraStyle='top:30px;'.$horizontal;
        $result.=Affectable::showBigImageEmpty($extraStyle,$canUpdate);
        //$result.='</td>';
        //$result.='</tr>';
        
      }
    }
    return $result;
  }
  public static function isAffectable($objectClass=null) {
    if ($objectClass) {
      if ($objectClass=='Resource' or $objectClass=='User' or $objectClass=='Contact' 
       or $objectClass=='Affectable' or $objectClass=='ResourceSelect') {
        return true;
      }
    }
    return false;
  }
  
  public static function  getVisibilityScope($scope='List') {
    if (self::$_visibilityScope) return self::$_visibilityScope;
    $res='all';
    $crit=array('idProfile'=>getSessionUser()->idProfile, 'scope'=>'resVisibility'.$scope);
    $habil=SqlElement::getSingleSqlElementFromCriteria('HabilitationOther', $crit);
    if ($habil and $habil->id) {
      $res=SqlList::getFieldFromId('ListTeamOrga', $habil->rightAccess,'code',false);
    }
    self::$_visibilityScope==$res;
    return $res;
  }
}
?>