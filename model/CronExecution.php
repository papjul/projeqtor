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

/** ============================================================================
 * Activity is main planned element
 */  
require_once('_securityCheck.php');
class CronExecution extends SqlElement {
  
  public $id;    // redefine $id to specify its visible place
  public $cron;
  public $fileExecuted;
  public $idle;
  public $fonctionName;
  public $nextTime;
  
   /** ==========================================================================
   * Constructor
   * @param $id the id of the object in the database (null if not stored yet)
   * @return void
   */ 
  function __construct($id = NULL, $withoutDependentObjects=false) {
    parent::__construct($id,$withoutDependentObjects);
  }

   /** ==========================================================================
   * Destructor
   * @return void
   */ 
  function __destruct() {
    parent::__destruct();
  }

  public function save($withRelaunch=true) {
    parent::save();
  }
  
  public function calculNextTime(){
    $UTC=new DateTimeZone(Parameter::getGlobalParameter ( 'paramDefaultTimezone' ));
    $date=new DateTime('now');
    $date->modify('+1 minute');
    $splitCron=explode(" ",$this->cron);
    $count=0;
    if(count($splitCron)==5){
      $find=false;
      while(!$find){ //cron minute/hour/day of month/month/day of week
        if(($splitCron[0]=='*' || $date->format("i")==$splitCron[0])
        && ($splitCron[1]=='*' || $date->format("H")==$splitCron[1])
        && ($splitCron[2]=='*' || $date->format("d")==$splitCron[2])
        && ($splitCron[3]=='*' || $date->format("m")==$splitCron[3])
        && ($splitCron[4]=='*' || $date->format("N")==$splitCron[4])){
          $find=true;
          $this->nextTime=$date->format("U");
          $this->save(false);
        }else{
          $date->modify('+1 minute');
        }
        $count++;
        if($count>=2150000){
          $this->idle=1;
          $this->save(false);
          $find=true;
          errorLog("Can't find next time for cronexecution because too many execution #".$this->id);
        }
      }
    }else{
      errorLog("Can't find next time for cronexecution because too many execution #".$this->id);
    }
  }
}
?>