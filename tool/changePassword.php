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
 * Chek login/password entered in connection screen
 */
  require_once "../tool/projeqtor.php"; 
scriptLog("changePassword.php");  
  $password="";
  if (array_key_exists('password',$_POST)) {
    $password=$_POST['password'];
  }    
  $userSalt=$_POST['userSalt'];
  if ($password=="") {
    passwordError();
  }
  if ($password==hash('sha256',Parameter::getGlobalParameter('paramDefaultPassword').$userSalt)) {
    passwordError();
  }
  $user=getSessionUser();
  if ( ! $user ) {
   passwordError();
  } 
  if ( ! $user->id) {
    passwordError();
  } 
  if ( $user->idle!=0) {
    passwordError();
  } 
  if ($user->isLdap<>0) {
    passwordError();
  } 
  $passwordLength=$_POST['passwordLength'];
  if ($passwordLength<Parameter::getGlobalParameter('paramPasswordMinLength')) {
    passwordError();
  }
  
  changePassword($user, $password, $userSalt, 'sha256');
  
  /** ========================================================================
   * Display an error message because of invalid login
   * @return void
   */
  function passwordError() {
    echo '<div class="messageERROR">';
    echo i18n('invalidPasswordChange', array(Parameter::getGlobalParameter('paramPasswordMinLength')));
    echo '</div>';
    exit;
  }
  
   /** ========================================================================
   * Valid login
   * @param $user the user object containing login information
   * @return void
   */
  function changePassword ($user, $newPassword, $salt, $crypto) {
  	Sql::beginTransaction();
    //$user->password=md5($newPassword); password is encryted in JS
    $user->password=$newPassword;
    $user->salt=$salt;
    $user->crypto=$crypto;
    $user->passwordChangeDate=date('Y-m-d');
    $result=$user->save();
    if (getLastOperationStatus($result)=='OK') {
      $result=i18n('passwordChanged');
	    $result.='<div id="validated" name="validated" type="hidden"  dojoType="dijit.form.TextBox">OK';
	    $result.='<input type="hidden" id="lastOperationStatus" value="OK" />';
    }
    displayLastOperationStatus($result);
  }
  
?>