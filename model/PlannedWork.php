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
 * Project is the main object of the project managmement.
 * Almost all other objects are linked to a given project.
 */ 
require_once('_securityCheck.php');
class PlannedWork extends GeneralWork {

  public $_noHistory;
    
  // List of fields that will be exposed in general user interface
  
  // Define the layout that will be used for lists
  private static $_layout='
    <th field="id" formatter="numericFormatter" width="10%" ># ${id}</th>
    <th field="nameResource" formatter="thumbName22" width="35%" >${resourceName}</th>
    <th field="nameProject" width="35%" >${projectName}</th>
    <th field="rate" width="15%" formatter="percentFormatter">${rate}</th>  
    <th field="idle" width="5%" formatter="booleanFormatter" >${idle}</th>
    ';
  
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

// ============================================================================**********
// GET STATIC DATA FUNCTIONS
// ============================================================================**********
  
  /** ==========================================================================
   * Return the specific layout
   * @return the layout
   */
  protected function getStaticLayout() {
    return self::$_layout;
  }


// ============================================================================**********
// GET VALIDATION SCRIPT
// ============================================================================**********
  
  /** ==========================================================================
   * Return the validation sript for some fields
   * @return the validation javascript (for dojo frameword)
   */
  public function getValidationScript($colName) {
    $colScript = parent::getValidationScript($colName);

    if ($colName=="idle") {   
      $colScript .= '<script type="dojo/connect" event="onChange" >';
      $colScript .= '  if (this.checked) { ';
      $colScript .= '    if (dijit.byId("PlanningElement_realEndDate").get("value")==null) {';
      $colScript .= '      dijit.byId("PlanningElement_realEndDate").set("value", new Date); ';
      $colScript .= '    }';
      $colScript .= '  } else {';
      $colScript .= '    dijit.byId("PlanningElement_realEndDate").set("value", null); ';
      //$colScript .= '    dijit.byId("PlanningElement_realDuration").set("value", null); ';
      $colScript .= '  } '; 
      $colScript .= '  formChanged();';
      $colScript .= '</script>';
    }
    return $colScript;
  }
  
// ============================================================================**********
// MISCELLANOUS FUNCTIONS
// ============================================================================**********
  
  /**
   * Run planning calculation for project, starting at start date
   * @static
   * @param string $projectId id of project to plan
   * @param string $startDate start date for planning
   * @return string result
   */

// ================================================================================================================================
// PLAN
// ================================================================================================================================

  public static function plan($projectId, $startDate) {
  	projeqtor_set_time_limit(300);
  	projeqtor_set_memory_limit('512M');
  	
  	// Strict dependency means when B follows A (A->B), B cannot start same date as A ends, but only day after
  	$strictDependency=(Parameter::getGlobalParameter('dependencyStrictMode')=='NO')?false:true;
  	
  	//-- Manage cache
  	SqlElement::$_cachedQuery['Resource']=array();
  	SqlElement::$_cachedQuery['Project']=array();
  	SqlElement::$_cachedQuery['Affectation']=array();
  	SqlElement::$_cachedQuery['PlanningMode']=array();
  	
  	$workUnit=Work::getWorkUnit();
  	$hoursPerDay=Work::getHoursPerDay();
  	$hour=round(1/$hoursPerDay,10);
  	$halfHour=round(1/$hoursPerDay/2,10);
  	
    $withProjectRepartition=true;
    $result="";
    $startTime=time();
    $startMicroTime=microtime(true);
    $globalMaxDate=date('Y')+3 . "-12-31"; // Don't try to plan after Dec-31 of current year + 3
    $globalMinDate=date('Y')-1 . "-01-01"; // Don't try to plan before Jan-01 of current year -1
    
    $arrayPlannedWork=array();
    $arrayRealWork=array();
    $arrayAssignment=array();
    $arrayPlanningElement=array();

    //-- Controls (check that current user can run planning)
    $accessRightRead=securityGetAccessRight('menuActivity', 'read');
    if ($accessRightRead=='ALL' and ! trim($projectId)) {
      $listProj=explode(',',getVisibleProjectsList());
      if (count($listProj)-1 > Parameter::getGlobalParameter('maxProjectsToDisplay')) {
        $result=i18n('selectProjectToPlan');
        $result .= '<input type="hidden" id="lastPlanStatus" value="CONTROL" />';
        return $result;
      }
    }
    
    $daysPerWeek=7;
    if (Parameter::getGlobalParameter('OpenDaySunday')=='offDays') $daysPerWeek--;
    if (Parameter::getGlobalParameter('OpenDayMonday')=='offDays') $daysPerWeek--;
    if (Parameter::getGlobalParameter('OpenDayTuesday')=='offDays') $daysPerWeek;
    if (Parameter::getGlobalParameter('OpenDayWednesday')=='offDays') $daysPerWeek--;
    if (Parameter::getGlobalParameter('OpenDayThursday')=='offDays') $daysPerWeek--;
    if (Parameter::getGlobalParameter('OpenDayFriday')=='offDays') $daysPerWeek--;
    if (Parameter::getGlobalParameter('OpenDaySaturday')=='offDays') $daysPerWeek--;
    
    //-- Build in list to get a where clause : "idProject in ( ... )"
    $proj=new Project($projectId,true);
    $inClause="idProject in " . transformListIntoInClause($proj->getRecursiveSubProjectsFlatList(true, true));
    //$inClause.=" and " . getAccesRestrictionClause('Activity',false);
    //-- Remove Projects with Fixed Planning flag
    $inClause.=" and idProject not in " . Project::getFixedProjectList() ;
    $user=getSessionUser();
    $inClause.=" and idProject in ". transformListIntoInClause($user->getListOfPlannableProjects());
    //-- Purge existing planned work
    $plan=new PlannedWork();
    $plan->purge($inClause);
    //-- #697 : moved the administrative project clause after the purge
    //-- Remove administrative projects
    $inClause.=" and idProject not in " . Project::getAdminitrativeProjectList() ;
    //-- Get the list of all PlanningElements to plan (includes Activity, Projects, Meetings, Test Sessions)
    $pe=new PlanningElement();
    $clause=$inClause;
    $order="wbsSortable asc";
    $list=$pe->getSqlElementsFromCriteria(null,false,$clause,$order,true);
    if (count($list)==0) {
      $result=i18n('planDone', array('0'));
      $result.= '<input type="hidden" id="lastPlanStatus" value="INCOMPLETE" />';
      return $result;
    }
    $fullListPlan=PlanningElement::initializeFullList($list);
    $listProjectsPriority=$fullListPlan['_listProjectsPriority'];
    unset($fullListPlan['_listProjectsPriority']);
    $listPlan=self::sortPlanningElements($fullListPlan, $listProjectsPriority);
    $resources=array();
    $a=new Assignment();
    $topList=array();
    $arrayNotPlanned=array();
//-- Treat each PlanningElement ---------------------------------------------------------------------------------------------------
    foreach ($listPlan as $plan) {
      if (! $plan->id) {
        continue;
      }
    	$plan=$fullListPlan['#'.$plan->id];
      //-- Determine planning profile
      if ($plan->idle) {
      	$plan->_noPlan=true;
      	$fullListPlan=self::storeListPlan($fullListPlan,$plan);
      	continue;
      }
      if (isset($plan->_noPlan) and $plan->_noPlan) {
      	continue;
      } 
      $profile="ASAP"; // Default planning mode
      $startPlan=$startDate;
      $startFraction=0;
      $endPlan=null;
      $step=1;
      if (! $plan->idPlanningMode) {
        $profile="ASAP";
      } else {
        $pm=new PlanningMode($plan->idPlanningMode,true);
        $profile=$pm->code;  
      }
      if ($profile=="ASAP" and $plan->assignedWork==0 and $plan->leftWork==0 and $plan->validatedDuration>0) {
        $profile="FDUR";
      }
      if ($profile=="REGUL" or $profile=="FULL" 
       or $profile=="HALF" or $profile=="QUART") { // Regular planning
        $startPlan=$plan->validatedStartDate;
        $endPlan=$plan->validatedEndDate;
        $step=1;
      } else if ($profile=="FDUR") { // Fixed duration
        // #V5.1.0 : removed this option
        // This leads to issue when saving validate dates : it fixed start, which may not be expected
        // If one want Fixed duration with fixed start, use regular beetween dates, or use milestone to define start
      	//if ($plan->validatedStartDate) {   
      	//  $startPlan=$plan->validatedStartDate;
      	//}
        $step=1;
      } else if ($profile=="ASAP" or $profile=="GROUP") { // As soon as possible
        //$startPlan=$plan->validatedStartDate;
      	$startPlan=$startDate; // V4.5.0 : if validated is fixed, must not be concidered as "Must not start before"
      	$endPlan=null;
        $step=1;
      } else if ($profile=="ALAP") { // As late as possible (before end date)
          $startPlan=$plan->validatedEndDate;
          $endPlan=$startDate;
          $step=-1;         
      } else if ($profile=="FLOAT") { // Floating milestone
        $startPlan=$startDate;
        $endPlan=null;
        $step=1;
      } else if ($profile=="FIXED") { // Fixed milestone
        if ($plan->refType=='Milestone') {
          $startPlan=$plan->validatedEndDate;
          $plan->plannedStartDate=$plan->validatedEndDate;
          $plan->plannedEndDate=$plan->validatedEndDate;
          $fullListPlan=self::storeListPlan($fullListPlan,$plan);          
        } else {
          $startPlan=$plan->validatedStartDate;
          $startFraction=$plan->validatedStartFraction;
        }
        $endPlan=$plan->validatedEndDate;
        $step=1;
      } else if ($profile=="START") { // Start not before validated date
        $startPlan=$plan->validatedStartDate;
      	$endPlan=null;
        $step=1;
        $profile='ASAP'; // Once start is set, treat as ASAP mode (as soon as possible)
      } else {
        $profile=="ASAP"; // Default is ASAP
        $startPlan=$startDate;
        $endPlan=null;
        $step=1;
      }
      //-- Take into accound predecessors
      $precList=$plan->_predecessorListWithParent;
      foreach ($precList as $precId=>$precVal) { // $precVal = dependency delay
      	$prec=$fullListPlan[$precId];
        $precEnd=$prec->plannedEndDate;
        $precFraction=$prec->plannedEndFraction;       
        if ($prec->realEndDate) {
        	$precEnd=$prec->realEndDate;
        	$precFraction=1;
        }
        if ($strictDependency or $precVal!=0 or $precFraction==1) {
          if ( ( $prec->refType!='Milestone' and $plan->refType!='Milestone') or $precFraction==1) {
          //if ($prec->refType!='Milestone') {
            $startPossible=addWorkDaysToDate($precEnd,($precVal>=0)?2+$precVal:1+$precVal); // #77
          } else {
            $startPossible=addWorkDaysToDate($precEnd,1+$precVal);
          }
          $startPossibleFraction=0;
        } else {
          $startPossible=$precEnd;
          $startPossibleFraction=$precFraction;
        }
        if ($startPossible>=$startPlan or ($startPossible==$startPlan and $startPossibleFraction>$startFraction)) { // #77       
          $startPlan=$startPossible;
          $startFraction=$startPossibleFraction;
        }
      }
      if ($plan->refType=='Milestone') {
        if ($profile!="FIXED") {
          if ($strictDependency) {
            $plan->plannedStartDate=addWorkDaysToDate($startPlan,1);
          } else if ($startFraction==1) {
          	if (count($precList)>0) {
              $plan->plannedStartDate=addWorkDaysToDate($startPlan,2);
          	} else {
          		$plan->plannedStartDate=addWorkDaysToDate($startPlan,1);
          	}
          	$plan->plannedStartFraction=0;
          } else {
            $plan->plannedStartDate=$startPlan;
            $plan->plannedStartFraction=$startFraction;
          }
          $plan->plannedEndDate=$plan->plannedStartDate;
          $plan->plannedEndFraction=$plan->plannedStartFraction;
          $plan->plannedDuration=0;
          //$plan->save();
          $fullListPlan=self::storeListPlan($fullListPlan,$plan);
        }
        if ($profile=="FIXED") { // We are on Milestone ;)
        	$plan->plannedEndDate=$plan->validatedEndDate;
        	$plan->plannedDuration=0;
          //$plan->save();
          $fullListPlan=self::storeListPlan($fullListPlan,$plan);
        }
      } else {        
        if (! $plan->realStartDate) {
          //$plan->plannedStartDate=($plan->leftWork>0)?$plan->plannedStartDate:$startPlan;
        	if ($plan->plannedWork==0 and $plan->elementary==1) {
	        	if ($plan->validatedStartDate and $plan->validatedStartDate>$startPlan) {
	            $plan->plannedStartDate=$plan->validatedStartDate;
	          } else if ($plan->initialStartDate and $plan->initialStartDate>$startPlan) {
	            $plan->plannedStartDate=$plan->initialStartDate;
	          } else {
	            // V5.1.0 : should never start before startplan
	            //$plan->plannedStartDate=date('Y-m-d');
	            $plan->plannedStartDate=$startPlan;
	          }
        	}
        }
        if (! $plan->realEndDate) {
          //$plan->plannedEndDate=($plan->plannedWork==0)?$plan->validatedEndDate:$plan->plannedEndDate;
        	if ($plan->plannedWork==0 and $plan->elementary==1) {
	          if ($plan->validatedEndDate and $plan->validatedEndDate>$startPlan) {
	            $plan->plannedEndDate=$plan->validatedEndDate;
	          } else if ($plan->initialEndDate and $plan->initialEndDate>$startPlan) {
	            $plan->plannedEndDate=$plan->initialEndDate;
	          } else {
	            // V5.1.0 : should never start before startplan
	            //$plan->plannedEndDate=date('Y-m-d');
	            $plan->plannedEndDate=$startPlan;
	          }
          }        	
        }
        if ($profile=="FDUR") {
          if (! $plan->realStartDate) {
            if ($plan->elementary) {
              $plan->plannedStartDate=$startPlan;
              $endPlan=addWorkDaysToDate($startPlan,$plan->validatedDuration);
            }
          } else {
            $endPlan=addWorkDaysToDate($plan->realStartDate,$plan->validatedDuration);
          }
          if (! $plan->realEndDate) {
            $plan->plannedEndDate=$endPlan;
          }
          $fullListPlan=self::storeListPlan($fullListPlan,$plan);
          //$plan->save();
        }
        // get list of top project to chek limit on each project
        if ($withProjectRepartition) {
          $proj = new Project($plan->idProject,true);
          $listTopProjects=$proj->getTopProjectList(true);
        }
        $crit=array("refType"=>$plan->refType, "refId"=>$plan->refId);
        $listAss=$a->getSqlElementsFromCriteria($crit,false);
        $groupAss=array();
        //$groupMaxLeft=0;
        //$groupMinLeft=99999;           
        if ($profile=='GROUP' and count($listAss<2)) {
        	$profile=='ASAP';
        }
        if ($profile=='GROUP') {
        	foreach ($listAss as $ass) {
	        	$r=new Resource($ass->idResource,true);
	          $capacity=($r->capacity)?$r->capacity:1;
	          if (array_key_exists($ass->idResource,$resources)) {
	            $ress=$resources[$ass->idResource];
	          } else {
	            $ress=$r->getWork($startDate, $withProjectRepartition);      
	            $resources[$ass->idResource]=$ress;
	          }
	        	$assRate=1;
	          if ($ass->rate) {
	            $assRate=$ass->rate / 100;
	          }
	          //if ($ass->leftWork>$groupMaxLeft) $groupMaxLeft=$ass->leftWork;
	          //if ($ass->leftWork<$groupMinLeft) $groupMinLeft=$ass->leftWork;
	          if (! isset($groupAss[$ass->idResource]) ) {
		          $groupAss[$ass->idResource]=array();
	            $groupAss[$ass->idResource]['leftWork']=$ass->leftWork;
	            //$groupAss[$ass->idResource]['TogetherWork']=array();
		          $groupAss[$ass->idResource]['capacity']=$capacity;
		          $groupAss[$ass->idResource]['ResourceWork']=$ress;
	            $groupAss[$ass->idResource]['assRate']=$assRate;	
	            $groupAss[$ass->idResource]['calendar']=$r->idCalendarDefinition;
	          } else {
	          	$groupAss[$ass->idResource]['leftWork']+=$ass->leftWork;
	          	$assRate=$groupAss[$ass->idResource]['assRate']+$assRate;
	          	if ($assRate>1) $assRate=1;
	          	$groupAss[$ass->idResource]['assRate']=$assRate;
	          	$groupAss[$ass->idResource]['calendar']=$r->idCalendarDefinition;
	          }
        	  if ($withProjectRepartition) {
              foreach ($listTopProjects as $idProject) {
	              $projKey='Project#' . $idProject;
	              if (! array_key_exists($projKey,$groupAss[$ass->idResource]['ResourceWork'])) {
	                $groupAss[$ass->idResource]['ResourceWork'][$projKey]=array();
	              }
	              if (! array_key_exists('rate',$groupAss[$ass->idResource]['ResourceWork'][$projKey])) {
	                $groupAss[$ass->idResource]['ResourceWork'][$projKey]['rate']=$r->getAffectationRate($idProject);
	              }
	              $groupAss[$ass->idResource]['ResourceWork']['init'.$projKey]=$groupAss[$ass->idResource]['ResourceWork'][$projKey];
	            }
	          }
        	}
        }
        $plan->notPlannedWork=0;
        foreach ($listAss as $ass) {
          if ($ass->notPlannedWork>0) {
            $ass->notPlannedWork=0;
            $changedAss=true;
          }
          if ($profile=='GROUP' and $withProjectRepartition) {
          	foreach ($listAss as $asstmp) {
	            foreach ($listTopProjects as $idProject) {
	              $projKey='Project#' . $idProject;
	              $groupAss[$asstmp->idResource]['ResourceWork'][$projKey]=$groupAss[$asstmp->idResource]['ResourceWork']['init'.$projKey];
	            }
          	}
          }
          $changedAss=true;
          $ass->plannedStartDate=null;
          $ass->plannedEndDate=null;
          $r=new Resource($ass->idResource,true);
          $capacity=($r->capacity)?$r->capacity:1;
          if (array_key_exists($ass->idResource,$resources)) {
            $ress=$resources[$ass->idResource];
          } else {
            $ress=$r->getWork($startDate, $withProjectRepartition);        
          }
          if ($startPlan>$startDate) {
            $currentDate=$startPlan;
          } else {
            $currentDate=$startDate;
            if ($step==-1) {
              $step=1;
            }
          }
          if ($profile=='GROUP') {
            foreach($groupAss as $id=>$grp) {
              $groupAss[$id]['leftWorkTmp']=$groupAss[$id]['leftWork'];	
            }
          }  
          $assRate=1;
          if ($ass->rate) {
            $assRate=$ass->rate / 100;
          }
          // Get data to limit to affectation on each project           
          if ($withProjectRepartition) {
            foreach ($listTopProjects as $idProject) {
              $projKey='Project#' . $idProject;
              if (! array_key_exists($projKey,$ress)) {
                $ress[$projKey]=array();
              }
              if (! array_key_exists('rate',$ress[$projKey])) {
                $ress[$projKey]['rate']=$r->getAffectationRate($idProject);
              }
            }
          }
          //$projRate=$ress['Project#' . $ass->idProject]['rate'];
          $capacityRate=round($assRate*$capacity,2);
          $keyElt=$ass->refType.'#'.$ass->refId;
          $left=$ass->leftWork;
          $regul=false;
          if ($profile=="REGUL" or $profile=="FULL" or $profile=="HALF" or $profile=="QUART" or $profile=="FDUR") {
          	$delaiTh=workDayDiffDates($currentDate,$endPlan);
          	if ($delaiTh and $delaiTh>0) { 
              $regulTh=round($ass->leftWork/$delaiTh,10);
          	}
          	$delai=0;          	
          	for($tmpDate=$currentDate; $tmpDate<=$endPlan;$tmpDate=addDaysToDate($tmpDate, 1)) {
          		if (isOffDay($tmpDate,$r->idCalendarDefinition)) continue;
          		if (isset($ress['real'][$keyElt][$tmpDate])) continue;
          		$tempCapacity=$capacityRate;
          		if (isset($ress[$tmpDate])) {
          			$tempCapacity-=$ress[$tmpDate];
          		}
          		if ($tempCapacity<0) $tempCapacity=0;
          		if ($tempCapacity>=$regulTh or $regulTh==0) {
          			$delai+=1;
          		} else {
          			$delai+=round($tempCapacity/$regulTh,2);
          		}
          	}            
            if ($delai and $delai>0) { 
              $regul=round(($ass->leftWork/$delai)+0.000005,5);                            
              $regulDone=0;
              $interval=0;
              $regulTarget=0;
            }
          }
          while (1) {           
            if ($left<0.01) {
              break;
            }
            if ($profile=='FIXED' and $currentDate>$plan->validatedEndDate) {
              $changedAss=true;
              $ass->notPlannedWork=$left;
              $plan->notPlannedWork+=$left;
              $arrayNotPlanned[$ass->id]=$left;
              $left=0;
              break;
            }
            // Set limits to avoid eternal loop
            if ($currentDate==$globalMaxDate) { break; }         
            if ($currentDate==$globalMinDate) { break; } 
            if ($ress['Project#' . $plan->idProject]['rate']==0) { break ; }
            if (isOpenDay($currentDate, $r->idCalendarDefinition)) {
              $planned=0;
              $week=weekFormat($currentDate);
              if (array_key_exists($currentDate, $ress)) {
                $planned=$ress[$currentDate];
              }
              if ($regul) {
              	if (! isset($ress['real'][$keyElt][$currentDate])) {
                  $interval+=$step;
              	}
              }
              if ($planned < $capacity)  {
                $value=$capacity-$planned; 
                if ($value>$capacityRate) {
               	  $value=$capacityRate;
                }
                if (isset($ress['real'][$keyElt][$currentDate])) {
                  //$value-=$ress['real'][$keyElt][$currentDate]; // Case 1 remove existing
                  //if ($value<0) $value=0;
                  $value=0; // Case 2 : if real is defined, no more work to plan
                }
                if ($withProjectRepartition) {
                  foreach ($listTopProjects as $idProject) {
                    $projectKey='Project#' . $idProject;
                    $plannedProj=0;
                    $rateProj=1;
                    if (array_key_exists($week,$ress[$projectKey])) {
                      $plannedProj=$ress[$projectKey][$week];
                    }
                    $rateProj=Resource::findAffectationRate($ress[$projectKey]['rate'],$currentDate) / 100;
                    // ATTENTION, if $rateProj < 0, this means there is no affectation left ...
                    if ($rateProj<0) {
                    	$changedAss=true;
                    	$ass->notPlannedWork=$left;
                    	$plan->notPlannedWork+=$left;
                    	if (!$ass->plannedStartDate) $ass->plannedStartDate=$currentDate;
                    	if (!$ass->plannedEndDate) $ass->plannedEndDate=$currentDate;
                    	if (!$plan->plannedStartDate) $plan->plannedStartDate=$currentDate;
                    	if (!$plan->plannedEndDate) $plan->plannedEndDate=$currentDate;
                    	$arrayNotPlanned[$ass->id]=$left;
                    	$left=0;
                    }
                    if ($rateProj==1) {
                    	$leftProj=round(7*$capacity*$rateProj,2)-$plannedProj; // capacity for a full week
                    	// => to be able to plan weekends
                    } else {
                      $leftProj=round($daysPerWeek*$capacity*$rateProj,2)-$plannedProj; // capacity for a week
                    }
                    if ($value>$leftProj) {
                      $value=$leftProj;
                    }
                  }
                }
                $value=($value>$left)?$left:$value;
                if ($currentDate==$startPlan and $value>((1-$startFraction)*$capacity)) {
                  $value=((1-$startFraction)*$capacity);
                }
                if ($regul) {
                	$tmpTarget=$regul;
                	if (isset($ress['real'][$keyElt][$currentDate])) {
                	  $tmpTarget=0;
                	}
                  $tempCapacity=$capacityRate;
                  if (isset($ress[$currentDate])) {
                    $tempCapacity-=$ress[$currentDate];
                  }
                  if ($tempCapacity<0) $tempCapacity=0;
                  if ($tempCapacity<$regulTh and $regulTh!=0) {
                    $tmpTarget=round($tmpTarget*$tempCapacity/$regulTh,10);
                  }                                    
                	$regulTarget=round($regulTarget+$tmpTarget,10);              
                  $toPlan=$regulTarget-$regulDone;
                  if ($value>$toPlan) {
                    $value=$toPlan;
                  }
                  if ($workUnit=='days') {
                    $value=round($value,1);
                  } else {
                  	$value=round($value/$halfHour,0)*$halfHour;
                  }
                  if ($profile=="FULL" and $toPlan<1 and $interval<$delaiTh) {
                    $value=0;
                  }
                  if ($profile=="HALF" and $interval<$delaiTh) {
                    if ($toPlan<0.5) {
                      $value=0;
                    } else {
                      $value=0.5;
                    }
                  }
                  if ($profile=="QUART" and $interval<$delaiTh) {
                    if ($toPlan<0.25) {
                      $value=0;
                    } else {
                      $value=0.25;
                    }
                  }
                  if ($value>($capacity-$planned)) {
                    $value=$capacity-$planned;
                    if ($value<0.1) $value=0;
                  }
                  $regulDone+=$value;
                }
                if ($profile=='GROUP') {
                	foreach($groupAss as $id=>$grp) {
                		$grpCapacity=1;
                		if ($grp['leftWorkTmp']>0) {
	                		$grpCapacity=$grp['capacity']*$grp['assRate'];
	                		if (isOffDay($currentDate,$grp['calendar'])) {
	                		  $grpCapacity=0;
	                		} else if (isset($grp['ResourceWork'][$currentDate])) {
	                			$grpCapacity-=$grp['ResourceWork'][$currentDate];
	                		}
                		}
                		if ($value>$grpCapacity) {
                			$value=$grpCapacity;
                		}
                	}
                	// Check Project Affectation Rate
                	foreach($groupAss as $id=>$grp) {
	                  foreach ($listTopProjects as $idProject) {
	                    $projectKey='Project#' . $idProject;
	                    $plannedProj=0;
	                    $rateProj=1;
	                    if (isset($grp['ResourceWork'][$projectKey][$week])) {
	                      $plannedProj=$grp['ResourceWork'][$projectKey][$week];
	                    }
	                    $rateProj=Resource::findAffectationRate($grp['ResourceWork'][$projectKey]['rate'],$currentDate) / 100;
	                    if ($rateProj==1) {
	                      $leftProj=round(7*$grp['capacity']*$rateProj,2)-$plannedProj; // capacity for a full week
	                      // => to be able to plan weekends
	                    } else {
	                      $leftProj=round($daysPerWeek*$grp['capacity']*$rateProj,2)-$plannedProj; // capacity for a week
	                    }
	                    if ($value>$leftProj) {
	                      $value=$leftProj;
	                    }
	                  }
                	}
                	foreach($groupAss as $id=>$grp) {
                		$groupAss[$id]['leftWorkTmp']-=$value;
                		//$groupAss[$id]['weekWorkTmp'][$week]+=$value;
	                	if ($withProjectRepartition and $value >= 0.01) {
	                    foreach ($listTopProjects as $idProject) {
	                      $projectKey='Project#' . $idProject;
	                      $plannedProj=0;
	                      if (array_key_exists($week,$grp['ResourceWork'][$projectKey])) {
	                        $plannedProj=$grp['ResourceWork'][$projectKey][$week];
	                      }
	                      $groupAss[$id]['ResourceWork'][$projectKey][$week]=$value+$plannedProj;
	                    }
	                  }
                	}
                }
                if ($value>=0.01) {
                  if ($profile=='FIXED' and $currentDate==$plan->validatedStartDate) {
                    $fractionStart=$plan->validatedStartFraction;
                  } else {
                    $fractionStart=($capacity!=0)?round($planned/$capacity,2):'0';
                  }
                  $fraction=($capacity!=0)?round($value/$capacity,2):'1';;             
                  $plannedWork=new PlannedWork();
                  $plannedWork->idResource=$ass->idResource;
                  $plannedWork->idProject=$ass->idProject;
                  $plannedWork->refType=$ass->refType;
                  $plannedWork->refId=$ass->refId;
                  $plannedWork->idAssignment=$ass->id;
                  $plannedWork->work=$value;
                  $plannedWork->setDates($currentDate);
                  $arrayPlannedWork[]=$plannedWork;
                  if (! $ass->plannedStartDate or $ass->plannedStartDate>$currentDate) {
                    $ass->plannedStartDate=$currentDate;
                    $ass->plannedStartFraction=$fractionStart;
                  }
                  if (! $ass->plannedEndDate or $ass->plannedEndDate<$currentDate) {
                    $ass->plannedEndDate=$currentDate;
                    $ass->plannedEndFraction=min(($fractionStart+$fraction),1);
                  }
                  if (! $plan->plannedStartDate or $plan->plannedStartDate>$currentDate) {
                    $plan->plannedStartDate=$currentDate;
                    $plan->plannedStartFraction=$fractionStart;
                  } else if ($plan->plannedStartDate==$currentDate and $plan->plannedStartFraction<$fractionStart) {
                    $plan->plannedStartFraction=$fractionStart;
                  }
                  if (! $plan->plannedEndDate or $plan->plannedEndDate<$currentDate) {
                    $plan->plannedEndDate=$currentDate;
                    $plan->plannedEndFraction=min(($fractionStart+$fraction),1);
                  } else if ($plan->plannedEndDate==$currentDate and $plan->plannedEndFraction<$fraction) {
                    $plan->plannedEndFraction=min(($fractionStart+$fraction),1);
                  }
                  $changedAss=true;
                  $left-=$value;
                  $ress[$currentDate]=$value+$planned;
                  // Set value on each project (from current to top)
                  if ($withProjectRepartition and $value >= 0.01) {
                    foreach ($listTopProjects as $idProject) {
                      $projectKey='Project#' . $idProject;
                      $plannedProj=0;
                      if (array_key_exists($week,$ress[$projectKey])) {
                        $plannedProj=$ress[$projectKey][$week];
                      }
                      $ress[$projectKey][$week]=$value+$plannedProj;               
                    }
                  }
                }
              }            
            }
            $currentDate=addDaysToDate($currentDate,$step);
            if ($currentDate<$startDate and $step=-1) {
              $currentDate=$startPlan;
              $step=1;
            }
          }
          if ($changedAss) {
            $ass->_noHistory=true; // Will only save planning data, so no history required  
            $arrayAssignment[]=$ass;
          }
          $resources[$ass->idResource]=$ress;
        } 
      }
      $fullListPlan=self::storeListPlan($fullListPlan,$plan);
    }
    $cpt=0;
    $query='';
    foreach ($arrayPlannedWork as $pw) {
      if ($cpt==0) {
        $query='INSERT into ' . $pw->getDatabaseTableName() 
          . ' (idResource,idProject,refType,refId,idAssignment,work,workDate,day,week,month,year)'
          . ' VALUES ';
      } else {
        $query.=', ';
      }
      $query.='(' 
        . "'" . Sql::fmtId($pw->idResource) . "',"
        . "'" . Sql::fmtId($pw->idProject) . "',"
        . "'" . $pw->refType . "',"
        . "'" . Sql::fmtId($pw->refId) . "',"
        . "'" . Sql::fmtId($pw->idAssignment) . "',"
        . "'" . $pw->work . "',"
        . "'" . $pw->workDate . "',"
        . "'" . $pw->day . "',"
        . "'" . $pw->week . "',"
        . "'" . $pw->month . "',"
        . "'" . $pw->year . "')";
      $cpt++; 
      if ($cpt>=100) {
        $query.=';';
        SqlDirectElement::execute($query);
        $cpt=0;
        $query='';
      }
    }
    if ($query!='') {
      $query.=';';
      SqlDirectElement::execute($query);
    }
    // save Assignment
    foreach ($arrayAssignment as $ass) {
      $ass->simpleSave();
    }
    
    foreach ($fullListPlan as $pe) {
   	  $pe->simpleSave();
    }
    
    
    $endTime=time();
    $endMicroTime=microtime(true);
    $duration = round(($endMicroTime - $startMicroTime)*1000)/1000;
    if (count($arrayNotPlanned)>0) {
    	//$result='<div class="messageWARNING">'
    	$result=i18n('planDoneWithLimits', array($duration));
    	foreach ($arrayNotPlanned as $assId=>$left) {
    		$ass=new Assignment($assId,true);
    		$rName=SqlList::getNameFromId('Resource', $ass->idResource);
    		$oName=SqlList::getNameFromId($ass->refType, $ass->refId);
    		$result .='<br/>&nbsp;&nbsp;&nbsp;'.Work::displayWorkWithUnit($left). ' - '.$rName.' - '.i18n($ass->refType).' #'.htmlEncode($ass->refId).' : '.$oName; 
    	}	
    	//$result.='</div>';
    	$result .= '<input type="hidden" id="lastPlanStatus" value="INCOMPLETE" />';
    } else {
    	$result=i18n('planDone', array($duration));
    	$result .= '<input type="hidden" id="lastPlanStatus" value="OK" />';
    }
    return $result;
  }
// End of PLAN
// ================================================================================================================================
  
  
  private static function storeListPlan($listPlan,$plan) {
scriptLog("storeListPlan(listPlan,$plan->id)");
    $listPlan['#'.$plan->id]=$plan;
    // Update planned dates of parents
    if (($plan->plannedStartDate or $plan->realStartDate) and ($plan->plannedEndDate or $plan->realEndDate) ) {
      foreach ($plan->_parentList as $topId=>$topVal) {
        $top=$listPlan[$topId];
        $startDate=($plan->realStartDate)?$plan->realStartDate:$plan->plannedStartDate;
        if (!$top->plannedStartDate or $top->plannedStartDate>$startDate) {
          $top->plannedStartDate=$startDate;
        }
        $endDate=($plan->realEndDate)?$plan->realEndDate:$plan->plannedEndDate;
        if (!$top->plannedEndDate or $top->plannedEndDate<$endDate) {
          $top->plannedEndDate=$endDate;
        }
        $listPlan[$topId]=$top;
      }
    }
    return $listPlan;
  }
  
  private static function sortPlanningElements($list,$listProjectsPriority) {
  	// first sort on simple criterias
    foreach ($list as $id=>$elt) {
    	if ($elt->idPlanningMode=='16' or $elt->idPlanningMode=='6') { // FIXED
    		$crit='1';
    	} else if ($elt->idPlanningMode=='2' or  $elt->idPlanningMode=='3' or  $elt->idPlanningMode=='7' or  $elt->idPlanningMode=='20'
    	  or $elt->idPlanningMode=='10' or $elt->idPlanningMode=='11' or $elt->idPlanningMode=='13') { // REGUL or FULL or HALF or QUART)
    	  $crit='2';
    	} else if ($elt->idPlanningMode=='8' or  $elt->idPlanningMode=='14') { // FDUR  
    	  $crit='3';
    	} else { // Others (includes GROUP, with is not a priority but a constraint)
        $crit='4';
    	}
      $crit.='.';
      $prio=$elt->priority;
      if (isset($listProjectsPriority[$elt->idProject])) {
        $projPrio=$listProjectsPriority[$elt->idProject];
      } else { 
      	$projPrio=500;
      }
      if (! $elt->leftWork or $elt->leftWork==0) {$prio=0;}
      $crit.=str_pad($projPrio,5,'0',STR_PAD_LEFT).'.'.str_pad($prio,5,'0',STR_PAD_LEFT).'.'.$elt->wbsSortable;
      $elt->_sortCriteria=$crit;
      $list[$id]=$elt;
    }
    //self::traceArray($list);
    $bool = uasort($list,array(new PlanningElement(), "comparePlanningElementSimple"));
    //self::traceArray($list);
    // then sort on predecessors
    $result=self::specificSort($list);
    //self::traceArray($result);
    return $result;
  }
  
  private static function specificSort($list) {
  	// Sort to teake dependencies into account
  	$wait=array(); // array to store elements that has predecessors not sorted yet
  	$result=array(); // target array for sorted elements
  	foreach($list as $id=>$pe) {
  		$canInsert=false;
  		if ($pe->_predecessorListWithParent) {
  			$pe->_tmpPrec=array();
  			// retrieve prédecessors not sorted yet
  			foreach($pe->_predecessorListWithParent as $precId=>$precPe) {
  				if (! array_key_exists($precId, $result)) {
  					 $pe->_tmpPrec[$precId]=$precPe;
  				}
  			} 			
  			if (count($pe->_tmpPrec)>0) {
  				// if has some not written predecessor => wait (until no more predecessor)
  				$wait[$id]=$pe;
  				$canInsert=false;
  			} else {
  				// all predecessors are sorted yet => can insert it in sort list
  				$canInsert=true;
  			}
  		} else {
  			// no predecessor, so can insert
  			$canInsert=true;
  		}
  		if ($canInsert) {
  			$result[$id]=$pe;
  			// now, must check if can insert waiting ones
  			self::insertWaiting($result,$wait,$id);
  		}
  	}
  	// in the end, empty wait stack (should be empty !!!!)
  	foreach($wait as $wId=>$wPe) {
  		unset($wPe->_tmpPrec); // no used elsewhere
      $result[$wId]=$wPe;
  	}
  	return $result;
  }
  
  private static function insertWaiting(&$result,&$wait,$id) {
    foreach($wait as $wId=>$wPe) {
      if (isset($wPe->_tmpPrec) and array_key_exists($id, $wPe->_tmpPrec)) {
        // ok, prec has been inserted, not waiting for it anymore
        unset($wPe->_tmpPrec[$id]);
        if (count($wPe->_tmpPrec)==0) {
          // Waiting for no more prec => store it
          unset($wPe->_tmpPrec);
          $result[$wId]=$wPe;
          // and remove it from wait list
          unset ($wait[$wId]);
          // and check if this new insertion can release others
          self::insertWaiting($result,$wait,$wId); 
        } else {
          // Store wait stack with new prec list (with less items...)
          $wait[$wId]=$wPe;
        }
      }
    }
  }
  private static function traceArray($list) {
  	debugTraceLog('*****traceArray()*****');
  	foreach($list as $id=>$pe) {
  		debugTraceLog($id . ' - ' . $pe->wbs . ' - ' . $pe->refType . '#' . $pe->refId . ' - ' . $pe->refName . ' - Prio=' . $pe->priority . ' - Left='.$pe->leftWork.' - '.$pe->_sortCriteria);
  		if (count($pe->_predecessorListWithParent)>0) {
  			foreach($pe->_predecessorListWithParent as $idPrec=>$prec) {
  				debugTraceLog('   ' . $idPrec.'=>'.$prec);
  			}
  		}
  	}
  }
  
  public static function planSaveDates($projectId, $initial, $validated) {
  	if ($initial=='NEVER' and $validated=='NEVER') {
  		$result=i18n('planDatesNotSaved');
  		$result .= '<input type="hidden" id="lastPlanStatus" value="WARNING" />';
  		return $result;
  	}
  	$cpt=0;
  	$proj=new Project($projectId,true);
  	$inClause="idProject in " . transformListIntoInClause($proj->getRecursiveSubProjectsFlatList(true, true));
  	$obj=new PlanningElement();
  	$tablePE=$obj->getDatabaseTableName();
  	$inClause.=" and " . getAccesRestrictionClause('Activity',$tablePE);
  	// Remove administrative projects :
  	$inClause.=" and idProject not in " . Project::getAdminitrativeProjectList() ;
  	// Remove Projects with Fixed Planning flag
  	$inClause.=" and idProject not in " . Project::getFixedProjectList() ;
  	// Get the list of all PlanningElements to plan (includes Activity and/or Projects)
  	$pe=new PlanningElement();
  	$order="wbsSortable asc";
  	$list=$pe->getSqlElementsFromCriteria(null,false,$inClause,$order,true);
  	foreach ($list as $pe) {
  		// initial
  		if (($initial=='ALWAYS' or ($initial=='IFEMPTY' and ! $pe->initialStartDate)) and trim($pe->plannedStartDate)) {
  			$pe->initialStartDate=$pe->plannedStartDate;
  			$cpt++;
  		}
  		if (($initial=='ALWAYS' or ($initial=='IFEMPTY' and ! $pe->initialEndDate)) and trim($pe->plannedEndDate)) {
  			$pe->initialEndDate=$pe->plannedEndDate;
  			$cpt++;
  		}
  		// validated
  		if (($validated=='ALWAYS' or ($validated=='IFEMPTY' and ! $pe->validatedStartDate)) and trim($pe->plannedStartDate)) {
  			$pe->validatedStartDate=$pe->plannedStartDate;
  			$cpt++;
  		}
  		if (($validated=='ALWAYS' or ($validated=='IFEMPTY' and ! $pe->validatedEndDate)) and trim($pe->plannedEndDate)) {
  			$pe->validatedEndDate=$pe->plannedEndDate;
  			$cpt++;
  		}
  		$pe->simpleSave();
  	}
  	if ($cpt>0) {
  		$result=i18n('planDatesSaved');
  		$result .= '<input type="hidden" id="lastPlanStatus" value="OK" />';
  	} else {
  		$result=i18n('planDatesNotSaved');
  		$result .= '<input type="hidden" id="lastPlanStatus" value="WARNING" />';
  	}
  	return $result;
  }
  
}
?>