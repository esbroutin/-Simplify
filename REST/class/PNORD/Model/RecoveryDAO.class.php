<?php
 /**
 *  Class to handle DB access for recovery
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;
include("/var/www/webapp-simplify/config.inc.php");
include("/var/www/webapp-simplify/REST/lib/PHPMailer-master/PHPMailerAutoload.php");
global $gTblConfig;
    
use PHPMailer;

class RecoveryDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  function formatDates($str){
    $str = explode("-", substr($str, 0,10));
    $formated =  $str[2].'/'.$str[1].'/'.$str[0];
    return $formated;
  }
  
  /***************************************
  * List Recovery
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listRecovery($search){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $providerId ='';
    //if no search string, we dont filter the search 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (RECOVERY.ID LIKE '%$search%' 
            OR RECOVERY.LABEL LIKE '%".strtoupper($search)."%' 
            OR RECOVERY.LABEL LIKE '%".strtolower($search)."%')";
    }
      $user_id = $_SESSION['userid'];
      $sql = "SELECT recovery.id ,
                      recovery.label,
                      recovery.date,
                      recovery.recovery_stock,
                      recovery.recovery_used
              FROM recovery  WHERE USER_ID='$user_id' ".$conditionSql." ORDER BY DATE DESC";

      // $this->app->log->info('sql : '.$sql);

      $recoveryData = $this->db()->query($sql);
      $recoveriesData = $recoveryData->fetchAll(PDO::FETCH_ASSOC); 

      // $this->app->log->info('recoverysData : '. $this->dumpRet($recoverysData));
      return $recoveriesData ;          
  } 
      
  /***************************************
  * List Recovery
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listAdmin($userId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

      $sql = "SELECT recovery.id ,
                      recovery.label,
                      recovery.date,
                      recovery.recovery_stock,
                      recovery.recovery_used
              FROM recovery  WHERE USER_ID='$userId' ORDER BY DATE DESC";

      // $this->app->log->info('sql : '.$sql);

      $recoveryData = $this->db()->query($sql);
      $recoveriesData = $recoveryData->fetchAll(PDO::FETCH_ASSOC); 

      // $this->app->log->info('recoverysData : '. $this->dumpRet($recoverysData));
      return $recoveriesData ;          
  } 
     
  /***************************************
  * List Recovery Form
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listForm(){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

      $user_id = $_SESSION['userid'];
      $sql = "SELECT *
              FROM recovery_form  WHERE USER_ID='$user_id' ORDER BY CREATION_DATE DESC";

      // $this->app->log->info('sql : '.$sql);

      $form = $this->db()->query($sql);
      $forms = $form->fetchAll(PDO::FETCH_ASSOC); 

      // $this->app->log->info('recoverysData : '. $this->dumpRet($recoverysData));
      return $forms ;          
  } 
     
     
  /***************************************
  * List Recovery Form (all of then since we are administrator of recovery)
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listFormAdmin(){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

      $user_id = $_SESSION['userid'];
      $sql = "SELECT *
              FROM recovery_form ORDER BY CREATION_DATE DESC";

      // $this->app->log->info('sql : '.$sql);

      $form = $this->db()->query($sql);
      $forms['DATA'] = $form->fetchAll(PDO::FETCH_ASSOC); 

      $sql = "SELECT *
              FROM recovery_form WHERE status='ON_HOLD'";

      // $this->app->log->info('sql : '.$sql);

      $form = $this->db()->query($sql);
      $forms['COUNT_ON_HOLD'] = count($form->fetchAll(PDO::FETCH_ASSOC)); 

      // $this->app->log->info('recoverysData : '. $this->dumpRet($recoverysData));
      return $forms ;          
  } 
     
  /***************************************
  * Count On Hold Recovery Forms (all of then since we are administrator of recovery)
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function countForms(){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

      $user_id = $_SESSION['userid'];
      $sql = "SELECT *
              FROM recovery_form WHERE STATUS='ON_HOLD' ORDER BY CREATION_DATE DESC";

    	// $this->app->log->info('sql : '.$sql);

      $form = $this->db()->query($sql);
      $forms = count($form->fetchAll(PDO::FETCH_ASSOC)); 

      // $this->app->log->info('recoverysData : '. $this->dumpRet($recoverysData));
      return $forms ;          
  } 
     
  
  /**
  * Get Recovery
  * @return array[{object}]
  **/
  function getRecovery($recoveryId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "SELECT *          
              FROM recovery
              WHERE recovery.id = '$recoveryId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

    return $result;            
  } 
 
  /***************************************
  * Add Recovery
  * @return array[{object}]
  ***************************************/

  function addRecovery($recovery){

    $this->app->log->notice(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****recovery **** -> '.$this->dumpRet($recovery));

    //we check for undefined variables
    if (!isset($recovery->LABEL)) {
      $recovery->LABEL = '-';
    };
    //we convert values to local time.
    $day_duration = strtotime($recovery->END_TIME)-strtotime($recovery->START_TIME);
    $start_time = strtotime($recovery->START_TIME)+(39600);
    $end_time = strtotime($recovery->END_TIME)+(39600);
    $morning_night_end = 18000;
    $worktime_start = 27000;
    $rate_night_plus = 2.5;
    $rate_day_plus = 1.5;
    $rate_night = 2.25;
    $rate_day = 1.25;
    $worktime_end = 59400;
    $worktime_duration = $worktime_end - $worktime_start;
    $night_start = 72000;
    $current_overtime = 0;
    $launch_pause = $recovery->LUNCH_PAUSE;
    // $this->app->log->info('recovery->DATE : '. $this->dumpRet($recovery->DATE));
    $date_recovery = strtotime($recovery->DATE);
    $date_recovery = date(DATE_ATOM,$date_recovery);
    $this->app->log->info('date_recovery : '. $this->dumpRet($date_recovery));
    if(date('w', strtotime($date_recovery)) == 6) {
       $is_saturday = true;
    }else{
       $is_saturday = false;
    }
    $this->app->log->info('date("w", strtotime(date_recovery)) : '. $this->dumpRet(date('w', strtotime($date_recovery))));
    if(date('w', strtotime($date_recovery)) == 0) {
       $is_special_day = true;
    }else{
       $is_special_day = false;
    }
    $this->app->log->info('is_special_day : '. $this->dumpRet($is_special_day));
    if (isset($recovery->SPECIAL_DAY) && $is_special_day == false) {
      $is_special_day = $recovery->SPECIAL_DAY;
    }
    $this->app->log->info('is_saturday : '. $this->dumpRet($is_saturday));
    $this->app->log->info('is_special_day : '. $this->dumpRet($is_special_day));
    // $this->app->log->info('date_recovery : '. $this->dumpRet($date_recovery));
    $rule_version = 00001;
    $label = $recovery->LABEL;
    $user_id = $recovery->USER_ID;
    $max_tl = 8;
    $overtime_duration = $day_duration - $worktime_duration;
    $day_overrate_amount = 0;
    $day_rate_amount = 0;
    $night_overrate_amount = 0;
    $night_rate_amount = 0;

    //we get the current recovery stock for current user & current overtime since last monday (00:00:00).
    $sqlInfo = "SELECT RECOVERY_STOCK FROM TUSER WHERE LOGIN='$user_id'";

    $infos = $this->db()->query($sqlInfo)->fetch(PDO::FETCH_ASSOC);
    $current_recovery_stock = $infos['RECOVERY_STOCK'];

    $sqlInfo = "SELECT SUM(CAST(overtime AS numeric)) AS CURRENT_OVERIME
                FROM recovery where date_trunc('week', now()) <= recovery.date 
                AND recovery.date < date_trunc('week', now()) + '1 week'::interval";

    $infos = $this->db()->query($sqlInfo)->fetch(PDO::FETCH_ASSOC);
    $current_overtime = (double)$infos['CURRENT_OVERIME'];

    // if sunday or public holiday or sasturday, we dont have worktime duration
    if ($is_saturday == true || $is_special_day == true) {
      $overtime_duration = $day_duration;
      $day_overrate_amount = 0;
      $day_amount = 0;
      $night_overrate_amount = 0;
      $night_amount_morning = 0;
      $night_amount = 0;

      if ($start_time <= $morning_night_end) {
        $overtime_duration = $overtime_duration - ($morning_night_end - $start_time);
        $night_amount_morning = ($morning_night_end - $start_time);
        if ($end_time <= $night_start) {
          $day_amount = $overtime_duration;
        }
        else{
          $day_amount =($night_start - $morning_night_end) ;
          $overtime_duration =  $overtime_duration - $day_amount;
          $night_amount = $overtime_duration;
        }
      }
      else if ($start_time >=$morning_night_end && $end_time <= $night_start) { //is day
        $day_amount = $overtime_duration;
      }
      else if ($start_time > $night_start) {
        $night_amount = $overtime_duration;
      }
      else if ($start_time >=$morning_night_end && $start_time <=$night_start && $end_time >= $night_start) {
        $day_amount = $night_start - $start_time;
        $overtime_duration = $overtime_duration - $day_amount;
        $night_amount = $overtime_duration;
      }

      // if sunday or public holiday
      if ($is_special_day == true) {
        $rate_night_plus = 3.25;
        $rate_day_plus = 2.25;
        $rate_night = 3;
        $rate_day = 2;
      }
      $night_amount_morning = ceil($night_amount_morning/3600);
      $day_amount = ceil($day_amount/3600);
      $night_amount = ceil($night_amount/3600);
    // $this->app->log->info('current_overtime : '. $this->dumpRet($current_overtime));

      //we calculate the recovery hours (with/without overrate)
      if ($current_overtime > $max_tl) { // we already did more than 8 hours overtime this week
    // $this->app->log->info('current_overtime > max_tl');
        $recovery_amount = ($day_amount * $rate_day_plus) + ($night_amount * $rate_night_plus) + ($night_amount_morning * $rate_night_plus) ;
        $overtime = $day_amount + $night_amount  + $night_amount_morning ;
      }else{
    // $this->app->log->info('current_overtime < max_tl');
        $delta = $max_tl - $current_overtime;
        // $this->app->log->info('current_overtime < max_tl');
        if ($delta >= $night_amount_morning) {
          $night_rate_amount = $night_rate_amount + $night_amount_morning;
          $delta = $delta - $night_amount_morning;
          if ($delta >= $day_amount) {
            $day_rate_amount = $day_rate_amount + $day_amount;
            $delta = $delta - $day_amount;
            if ($delta >= $night_amount) {
              $day_rate_amount = $day_rate_amount +  $night_amount;
              $delta = $delta - $night_amount;
            }
            else{ // $delta < $night_amount
              $night_rate_amount = $night_rate_amount + $delta ;
              $night_overrate_amount = $night_overrate_amount + ($night_amount - $delta);
            }
          }
          else{ // $delta < $day_amount
            $day_rate_amount = $day_rate_amount + $delta ;
            $day_overrate_amount = $day_overrate_amount + ($day_amount - $delta);
            $night_overrate_amount = $night_overrate_amount + $night_amount;
          }
        }
        else{ //$delta < $night_amount_morning
          $night_rate_amount = $night_rate_amount + $delta ;
          $night_overrate_amount = $night_overrate_amount + ($night_amount_morning - $delta) + $night_amount;
          $day_overrate_amount = $day_overrate_amount + $day_amount;
        }
        //sum calculation
        $recovery_amount = ($day_overrate_amount * $rate_day_plus) + ($day_rate_amount * $rate_day) + ($night_overrate_amount * $rate_night_plus) + ($night_rate_amount * $rate_night) ;
        $overtime = $day_overrate_amount + $day_rate_amount  + $night_overrate_amount  + $night_rate_amount ;
      }

    // $this->app->log->info('night_rate_amount hours: '.$night_rate_amount);
    // $this->app->log->info('night_overrate_amount hours: '.$night_overrate_amount);
    // $this->app->log->info('day_rate_amount hours: '.$day_rate_amount);
    // $this->app->log->info('day_overrate_amount hours: '.$day_overrate_amount);

    // $this->app->log->info('night_amount_morning hours: '.$night_amount_morning);
    // $this->app->log->info('day_amount hours: '.$day_amount);
    // $this->app->log->info('night_amount hours: '.$night_amount);
    // $this->app->log->info('recovery_amount hours: '.$recovery_amount);


    //Adding new entry & updating user current recovery stock

    //we generate an custom id
    $recoveryId = $this->generateId('REC');
    
    // $this->app->log->info('****new id **** -> '.$this->dumpRet($recoveryId));

    $this->db()->beginTransaction();

    $sql = "INSERT INTO RECOVERY(
                                      ID,
                                      LABEL,
                                      START_TIME,                                      
                                      END_TIME,
                                      DATE,
                                      LUNCH_PAUSE,
                                      USER_ID,
                                      IS_SATURDAY,
                                      SPECIAL_DAY,
                                      RECOVERY_STOCK,
                                      OVERTIME,
                                      RULE_VERSION) 
                               VALUES(:id,
                                     :label,
                                     :start_time,
                                     :end_time,
                                     :date_recovery,
                                     :launch_pause,
                                     :user_id,
                                     :is_saturday,
                                     :is_special_day,
                                     :recovery_stock,
                                     :overtime,
                                     :rule_version)";

    $query = $this->db()->prepare($sql);

    $result = $query->execute(array(                            
                            ':id'=>$recoveryId,                            
                            ':label'=>$label,
                            ':start_time'=>$recovery->START_TIME,
                            ':end_time'=>$recovery->END_TIME,
                            ':date_recovery'=>$date_recovery,
                            ':launch_pause'=>$launch_pause,
                            ':user_id'=>$user_id,
                            ':is_saturday'=>$is_saturday,
                            ':is_special_day'=>$is_special_day,
                            ':recovery_stock'=>$recovery_amount,
                            ':overtime'=>$overtime,
                            ':rule_version'=>$rule_version
                            ));
    
    // $this->app->log->info('****result **** -> '.$this->dumpRet($result));
 
      $return = $query->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur
      return ($recoveryId);
    }
    else{
    // $this->app->log->info('****overtime_duration **** -> '.$this->dumpRet($overtime_duration));
    // $this->app->log->info('****worktime_duration **** -> '.$this->dumpRet($worktime_duration));
    // $this->app->log->info('****day_duration **** -> '.$this->dumpRet($day_duration));

    // we calculate all the time outside normal time.
    if ($start_time <= $morning_night_end) { // is morning night ?
      $BF_W_Time =  $morning_night_end - $start_time;
      // BF_W_N calculation
      if ($overtime_duration > $BF_W_Time) {
        $BF_W_N =$BF_W_Time;
        $overtime_duration = $overtime_duration - $BF_W_N;
        $start_time = $morning_night_end;
        $BF_W_Time =  $worktime_start - $start_time;
        // BF_W_D calculation
        if ($overtime_duration > $BF_W_Time) {
          $BF_W_D =$BF_W_Time;
          $overtime_duration = $overtime_duration - $BF_W_D;
          $start_time = $worktime_end;
          $AF_W_Time =  $night_start - $start_time;
          // AF_W_D calculation
          if ($overtime_duration > $AF_W_Time) {
            $AF_W_D =$AF_W_Time;
            $overtime_duration = $overtime_duration - $AF_W_D;
            $AF_W_N =$overtime_duration;
          }
          else if ($overtime_duration <= $AF_W_Time) {
            $AF_W_D = $overtime_duration;
            $overtime_duration = 0;
            $AF_W_N = 0;
          }
        }
        else if ($overtime_duration <= $BF_W_Time) {
          $BF_W_D = $overtime_duration;
          $overtime_duration = 0;
          $AF_W_D = 0;
          $AF_W_N = 0;
        }
      }
      else if ($overtime_duration < $BF_W_Time) {
        $BF_W_N = $overtime_duration;
        $overtime_duration = 0;
        $BF_W_D = 0;
        $AF_W_D = 0;
        $AF_W_N = 0;
      }
    }
    //If it's morning but after night time and before worktime
    else if ($start_time > $morning_night_end && $start_time <= $worktime_start) {
      // $this->app->log->info('BF_W_D start !  ');
      $BF_W_N = 0;
      $BF_W_Time =  $worktime_start - $start_time;
        // BF_W_D calculation
      if ($overtime_duration > $BF_W_Time) {
        $BF_W_D =$BF_W_Time;
        $overtime_duration = $overtime_duration - $BF_W_D;
        $start_time = $worktime_end;
        $AF_W_Time =  $night_start - $start_time;

        // AF_W_D calculation
        if ($overtime_duration > $AF_W_Time) {
          $AF_W_D =$AF_W_Time;
          $overtime_duration = $overtime_duration - $AF_W_D;
          $AF_W_N =$overtime_duration;
        }
        // AF_W_D calculation
        else if ($overtime_duration <= $AF_W_Time) {
          $AF_W_D = $overtime_duration;
          $overtime_duration = 0;
          $AF_W_N = 0;
        }
      }
      // BF_W_D calculation
      else if ($overtime_duration <= $BF_W_Time) {
        $BF_W_D = $overtime_duration;
        $overtime_duration = 0;
        $AF_W_D = 0;
        $AF_W_N = 0;
      }
    }
    else if ($start_time > $worktime_start && $start_time <= $worktime_end) {
      // $this->app->log->info('Workday start !  ');
      $BF_W_N = 0;
      $BF_W_D = 0;
      $AF_W_Time =  $night_start - $worktime_end;
      $overtime_duration = $end_time - $worktime_end;
      // AF_W_D calculation
      if ($overtime_duration > $AF_W_Time) {
        $AF_W_D =$AF_W_Time;
        $overtime_duration = $overtime_duration - $AF_W_D;
        $AF_W_N =$overtime_duration;
      }
      // AF_W_D calculation
      else if ($overtime_duration <= $AF_W_Time) {
        $AF_W_D = $overtime_duration;
        $overtime_duration = 0;
        $AF_W_N = 0;
      }
    }
    else if ($start_time > $worktime_end && $start_time <= $night_start) {
      // $this->app->log->info('AF_W_D start !  ');
      $BF_W_N = 0;
      $BF_W_D = 0;
      $AF_W_Time =  $night_start - $start_time;
      $overtime_duration = $end_time - $start_time;
      // AF_W_D calculation
      if ($overtime_duration > $AF_W_Time) {
        $AF_W_D =$AF_W_Time;
        $overtime_duration = $overtime_duration - $AF_W_D;
        $AF_W_N =$overtime_duration;
      }
      else if ($overtime_duration <= $AF_W_Time) {
        $AF_W_D = $overtime_duration;
        $overtime_duration = 0;
        $AF_W_N = 0;
      }
    }
    else if ($start_time > $night_start) {
      // $this->app->log->info('AF_W_N start !  ');
      $overtime_duration = $end_time - $night_start;
      $BF_W_N = 0;
      $BF_W_D = 0;
      $AF_W_D = 0;
      $AF_W_N = $overtime_duration;
    }

      // RECOVERY CALCULATION
      $BF_W_N = ceil($BF_W_N/3600);
      $BF_W_D = ceil($BF_W_D/3600);
      if ($launch_pause == false) {
        $BF_W_D = $BF_W_D +1;
      }
      $AF_W_D = ceil($AF_W_D/3600);
      $AF_W_N = ceil($AF_W_N/3600);
      // $this->app->log->info('BF_W_N : '.$this->dumpRet($BF_W_N));
      // $this->app->log->info('BF_W_D : '.$this->dumpRet($BF_W_D));
      // $this->app->log->info('AF_W_D : '.$this->dumpRet($AF_W_D));
      // $this->app->log->info('AF_W_N : '.$this->dumpRet($AF_W_N));
      // $this->app->log->info('current_overtime : '.$this->dumpRet($current_overtime));

    if ($current_overtime > $max_tl) { // we already did more than 8 hours overtime this week
      $day_overrate_amount = ($BF_W_D + $AF_W_D) ;
      $night_overrate_amount =  ($BF_W_N + $AF_W_N) ;
      $overtime = $day_overrate_amount + $night_overrate_amount;
      $recovery_amount = ($night_overrate_amount * $rate_night_plus) + ($day_overrate_amount * $rate_day_plus);
    }else{
      $delta = $max_tl - $current_overtime;
      if ($delta >= $BF_W_N) {
        $night_rate_amount = $night_rate_amount + $BF_W_N;
        $delta = $delta - $BF_W_N;
        if ($delta >= $BF_W_D) {
          $day_rate_amount = $day_rate_amount + $BF_W_D;
          $delta = $delta - $BF_W_D;
          if ($delta >= $AF_W_D) {
            $day_rate_amount = $day_rate_amount +  $AF_W_D;
            $delta = $delta - $AF_W_D;
            if ($delta >= $AF_W_N) {
              $night_rate_amount = $night_rate_amount +  $AF_W_N;
              $delta = $delta - $AF_W_N;
              }
              else{ // $delta < $AF_W_N
                $night_rate_amount = $night_rate_amount + $delta ;
                $day_overrate_amount = $day_overrate_amount + ($AF_W_N - $delta);
              }
          }
          else{ // $delta < $AF_W_D
            $day_rate_amount = $day_rate_amount + $delta ;
            $day_overrate_amount = $day_overrate_amount + ($AF_W_D - $delta);
            $night_overrate_amount = $night_overrate_amount + $AF_W_N;
          }
        }
        else{ // $delta < $BF_W_D
          $day_rate_amount = $day_rate_amount + $delta ;
          $day_overrate_amount = $day_overrate_amount + ($BF_W_D - $delta) + $AF_W_D;
          $night_overrate_amount = $night_overrate_amount + $AF_W_N;
        }
      }
      else{ //$delta < $BF_W_N
        $night_rate_amount = $night_rate_amount + $delta ;
        $night_overrate_amount = $night_overrate_amount + ($BF_W_N - $delta) + $AF_W_N;
        $day_overrate_amount = $day_overrate_amount + $BF_W_D+ $AF_W_D;
      }
    }

    //we calculate the total recovery time earned this day
    $recovery_amount = ($night_rate_amount * $rate_night) + ($night_overrate_amount * $rate_night_plus) + ($day_overrate_amount * $rate_day_plus) + ($day_rate_amount * $rate_day);

    $overtime = $night_rate_amount + $night_overrate_amount + $day_overrate_amount + $day_rate_amount;

    // $this->app->log->info('night_rate_amount: '.$night_rate_amount);
    // $this->app->log->info('rate_night: '.$rate_night);
    // $this->app->log->info('night_overrate_amount: '.$night_overrate_amount);
    // $this->app->log->info('day_overrate_amount: '.$day_overrate_amount);
    // $this->app->log->info('day_rate_amount: '.$day_rate_amount);
    // $this->app->log->info('recovery_amount: '.$recovery_amount);
    // $this->app->log->info('overtime: '.$overtime);

    //adding entry & updating user amount
    
    //we generate an custom id
    $recoveryId = $this->generateId('REC');
    
    // $this->app->log->info('****new brand **** -> '.$this->dumpRet($brand));

    $this->db()->beginTransaction();

    $sql = "INSERT INTO RECOVERY(
                                      ID,
                                      LABEL,
                                      START_TIME,                                      
                                      END_TIME,
                                      DATE,
                                      LUNCH_PAUSE,
                                      USER_ID,
                                      IS_SATURDAY,
                                      SPECIAL_DAY,
                                      RECOVERY_STOCK,
                                      OVERTIME,
                                      RULE_VERSION) 
                               VALUES(:id,
                                     :label,
                                     :start_time,
                                     :end_time,
                                     :date_recovery,
                                     :launch_pause,
                                     :user_id,
                                     :is_saturday,
                                     :is_special_day,
                                     :recovery_stock,
                                     :overtime,
                                     :rule_version)";

    $query = $this->db()->prepare($sql);

    $result = $query->execute(array(                            
                            ':id'=>$recoveryId,                            
                            ':label'=>$label,
                            ':start_time'=>$recovery->START_TIME,
                            ':end_time'=>$recovery->END_TIME,
                            ':date_recovery'=>$date_recovery,
                            ':launch_pause'=>$launch_pause,
                            ':user_id'=>$user_id,
                            ':is_saturday'=>$is_saturday,
                            ':is_special_day'=>$is_special_day,
                            ':recovery_stock'=>$recovery_amount,
                            ':overtime'=>$overtime,
                            ':rule_version'=>$rule_version
                            ));

    $return = $query->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur
      return ($recoveryId);
  }
  }

  /***************************************
  * UPDATE RECOVERY *UNUSED*
  *
  * @return licenceId
  ***************************************/

  function updateRecovery($recovery){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****recovery **** -> '.$this->dumpRet($recovery));


    $recoveryId =  $recovery->ID;
    $label = $recovery->LABEL;
    $warranty_start = $recovery->WARRANTY_START;
    $warranty_end = $recovery->WARRANTY_END;

    //we check for undefined variables
    if (!isset($recovery->DESCRIPTION)) {
      $recovery->DESCRIPTION = '-';
    };
    if (!isset($recovery->WEB_ADDRESS)) {
      $recovery->WEB_ADDRESS = '-';
    };
    if (!isset($recovery->BRAND_ID)) {
      $recovery->BRAND_ID = '-';
    };
    if (!isset($recovery->PROVIDER_ID)) {
      $recovery->BRAND_ID = '-';
    };
    if (!isset($recovery->SERIAL_NUMBER)) {
      $recovery->SERIAL_NUMBER = '-';
    };
    if (!isset($recovery->BARCODE)) {
      $recovery->BARCODE = '-';
    };
    if (!isset($recovery->SITE)) {
      $recovery->SITE = '-';
    };
    if (!isset($recovery->STATUS)) {
      $recovery->STATUS = '-';
    };
    if (!isset($recovery->TYPE)) {
      $recovery->TYPE = '-';
    };
    $description =  str_replace("'", "", $recovery->DESCRIPTION);
    $brand_Id = str_replace("'", "", $recovery->BRAND_ID);
    $serial_number = str_replace("'", "", $recovery->SERIAL_NUMBER);
    $barcode = str_replace("'", "", $recovery->BARCODE);
    $site = str_replace("'", "", $recovery->SITE);
    $status = str_replace("'", "", $recovery->STATUS);
    $type = str_replace("'", "", $recovery->TYPE);
    $provider_Id = str_replace("'", "", $recovery->PROVIDER_ID);

    $sqlRecovery = "UPDATE RECOVERY
                  SET LABEL='$label',
                      WARRANTY_START='$warranty_start',
                      WARRANTY_END='$warranty_end',
                      DESCRIPTION='$description',
                      BRAND_ID='$brand_Id',
                      BARCODE='$barcode',
                      SITE='$site',
                      STATUS='$status',
                      TYPE='$type',
                      SERIAL_NUMBER='$serial_number',
                      PROVIDER_ID='$provider_Id',
                      EDITION_DATE=CURRENT_TIMESTAMP
                  WHERE ID='$recoveryId';";
    // $this->app->log->info('****sqlRecovery **** -> '.$this->dumpRet($sqlRecovery));
    $queryRecovery = $this->db()->query($sqlRecovery);
    return($recoveryId);

  }

  /***************************************
  * REFUSE RECOVERY REQUEST
  *
  * @return 'validate'
  ***************************************/

  function validate($form){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****form **** -> '.$this->dumpRet($form));
    $formData = $this->getForm($form->ID);



    $formId =  $formData['ID'];
    $user_id =  $formData['USER_ID'];
    $to_use = $formData['TO_USE'];
    $used_recovery_ids = '[';

    $sqlListRecovery = "SELECT * FROM RECOVERY WHERE user_id = '$user_id' AND STATUS='valid' AND (CAST( RECOVERY_USED AS numeric) < CAST( RECOVERY_STOCK AS numeric)) ORDER BY DATE DESC;";
    $recoveriesData = $this->db()->query($sqlListRecovery)->fetchAll(PDO::FETCH_ASSOC);

    //we get the available amount to use
    $sqlSum = "SELECT SUM(CAST( RECOVERY_STOCK AS numeric) - CAST( RECOVERY_USED AS numeric)) 
                FROM RECOVERY 
                WHERE user_id = '$user_id' AND STATUS='valid';";
    $sum = $this->db()->query($sqlSum)->fetch(PDO::FETCH_ASSOC);
    $sum =  $sum['SUM'];
      // $this->app->log->info('****sum **** -> '.$this->dumpRet($sum));
    if ($to_use > $sum) {
      return ('cant_afford');
    }
      // $this->app->log->info('****recoveriesData **** -> '.$this->dumpRet($recoveriesData));

    for ($i=0; $i < count($recoveriesData); $i++) { 
      $this->app->log->info('****recoveriesData[i][RECOVERY_STOCK] **** -> '.$this->dumpRet($recoveriesData[$i]['RECOVERY_STOCK']));
      $available = $recoveriesData[$i]['RECOVERY_STOCK'] - $recoveriesData[$i]['RECOVERY_USED'];
      $recoveryId = $recoveriesData[$i]['ID'];

      if ($to_use > 0) {
        if ($available >= $to_use) {

          $new_recovery_used = $to_use + $recoveriesData[$i]['RECOVERY_USED'];
          $to_use = 0;

          // $this->app->log->info('**** IF new_recovery_used **** -> '.$this->dumpRet($new_recovery_used));

          $sqlRecovery = "UPDATE RECOVERY
                SET RECOVERY_USED='$new_recovery_used'
                WHERE ID='$recoveryId';";
          $result = $this->db()->query($sqlRecovery);
          if ($used_recovery_ids != "[") {$used_recovery_ids .= ",";};
          $used_recovery_ids .= '{"id":"'.$recoveriesData[$i]['ID'].'"}';

        }else{
            $to_use = $to_use - $available;
            $new_recovery_used = $recoveriesData[$i]['RECOVERY_STOCK'];

            // $this->app->log->info('****ELSE new_recovery_used **** -> '.$this->dumpRet($new_recovery_used));
            $sqlRecovery = "UPDATE RECOVERY
                  SET RECOVERY_USED='$new_recovery_used'
                  WHERE ID='$recoveryId';";
                  
            $result = $this->db()->query($sqlRecovery);
            if ($used_recovery_ids != "[") {$used_recovery_ids .= ",";};
            $used_recovery_ids .= '{"id":"'.$recoveriesData[$i]['ID'].'"}';


        }
      }

      // $this->app->log->info('****to_use **** -> '.$this->dumpRet($to_use));

    }

      $used_recovery_ids .= ']';
      // $this->app->log->info('****used_recovery_ids **** -> '.$this->dumpRet($used_recovery_ids));

    // we validate the recovery request
    $sqlRecovery = "UPDATE RECOVERY_FORM
          SET USED_RECOVERY_IDS='$used_recovery_ids', STATUS= 'VALIDATED'
          WHERE ID='$formId';";

    $result = $this->db()->query($sqlRecovery);
    $this->informUser($form,'validated');

    // $this->app->log->info('****result **** -> '.$this->dumpRet($result));
    return($formId);

  }

  /***************************************
  * SEND NOTIFICATION FUNCTION 
  *
  * @return 'sended'
  ***************************************/

  function sendNotification(){

    $mail = new PHPMailer;

    $mail->SMTPDebug = 3;                               // Enable verbose debug output

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'balade.pnord.nc';  // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;                               // Enable SMTP authentication
    $mail->Username = 'admin-administrator';                 // SMTP username
    $mail->Password = 'v6aHdJUw';                           // SMTP password
    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
    $mail->Port = 587;                                      // TCP port to connect to

    $mail->From = 'admin-administrator@province-nord.nc';
    $mail->FromName = 'Mailer';
    $mail->addAddress('e.broutin@province-nord.nc', 'Simplify account');     // Add a recipient
    $mail->addAddress('e.broutin@province-nord.nc');
    $mail->isHTML(true);                                  // Set email format to HTML

    $mail->Subject = 'Demande de recuperation !Simplify';
    $mail->Body    = 'Vous avez une nouvelle demande de recuperation a valider sur https://simplify.pnord.nc';
    $mail->AltBody = 'Vous avez une nouvelle demande de recuperation a valider sur https://simplify.pnord.nc';

    if(!$mail->send()) {
      echo 'Message could not be sent.';

      // $this->app->log->info('****mail->ErrorInfo **** -> '.$this->dumpRet($mail->ErrorInfo));
    } else {

      // $this->app->log->info('****Message has been sent **** -> ');
    }
  }

  /***************************************
  * SEND NOTIFICATION FUNCTION 
  *
  * @return 'sended'
  ***************************************/

  function informUser($form,$type){

      $mail = new PHPMailer;

        $this->app->log->info('****form **** -> '.$this->dumpRet($form));
        $this->app->log->info('****type **** -> '.$this->dumpRet($type));

      $mail->SMTPDebug = 3;                               // Enable verbose debug output
      if ($type == 'refused' || $type == 'validated') {
        $userMail = $form->USER_ID.'@province-nord.nc';
      }else{
        $userMail = $form['USER_ID'].'@province-nord.nc';
      }
        
        $this->app->log->info('****userMail **** -> '.$this->dumpRet($userMail));

      $mail->isSMTP();                                      // Set mailer to use SMTP
      $mail->Host = 'balade.pnord.nc';  // Specify main and backup SMTP servers
      $mail->SMTPAuth = true;                               // Enable SMTP authentication
      $mail->Username = 'admin-administrator';                 // SMTP username
      $mail->Password = 'v6aHdJUw';                           // SMTP password
      $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
      $mail->Port = 587;                                      // TCP port to connect to

      $mail->From = 'admin-administrator@province-nord.nc';
      $mail->FromName = 'Mailer';
      $mail->addAddress($userMail);     // Add a recipient
      $mail->isHTML(true);    

    if ($type == 'refused') {                                // Set email format to HTML

      $mail->Subject = 'Demande de recuperation Refusee - !Simplify';
      $mail->Body    = '<p> Votre demande '.$form->ID.' de recuperation pour le '.$this->formatDates($form->DATE).' a ete refusee -<br /> Raison : <strong>'.$form->REASON.'</strong></p>';
      $mail->AltBody = '<p> Votre demande '.$form->ID.' de recuperation pour le '.$this->formatDates($form->DATE).' a ete refusee -<br /> Raison : <strong>'.$form->REASON.'</strong></p>';

      if(!$mail->send()) {
        echo 'Message could not be sent.';

        $this->app->log->info('****mail->ErrorInfo **** -> '.$this->dumpRet($mail->ErrorInfo));
      } else {

        $this->app->log->info('****Message has been sent **** -> ');
      }
    }

    if ($type == 'validated') {                              // Set email format to HTML

      $mail->Subject = 'Demande de recuperation Validee - !Simplify';
      $mail->Body    = '<p> Votre demande '.$form->ID.' de recuperation pour le '.$this->formatDates($form->DATE).' a ete validee </p>';
      $mail->AltBody = '<p> Votre demande '.$form->ID.' de recuperation pour le '.$this->formatDates($form->DATE).' a ete validee </p>';

      if(!$mail->send()) {
        echo 'Message could not be sent.';

        // $this->app->log->info('****mail->ErrorInfo **** -> '.$this->dumpRet($mail->ErrorInfo));
      } else {

        // $this->app->log->info('****Message has been sent **** -> ');
      }
    }

    if ($type == 'recovery_deleted') {                              // Set email format to HTML

      $mail->Subject = 'Operation supprimee - !Simplify';
      $mail->Body    = '<p> Votre Operation '.$form['ID'].', "'.$form['LABEL'].'", du '.$this->formatDates($form['DATE']).' a ete supprimee par '.$_SESSION['userid'].' </p>';
      $mail->AltBody = '<p> Votre Operation '.$form['ID'].', "'.$form['LABEL'].'", du '.$this->formatDates($form['DATE']).' a ete supprimee par '.$_SESSION['userid'].' </p>';

      if(!$mail->send()) {
        echo 'Message could not be sent.';

        // $this->app->log->info('****mail->ErrorInfo **** -> '.$this->dumpRet($mail->ErrorInfo));
      } else {

        // $this->app->log->info('****Message has been sent **** -> ');
      }
    }
  }

  /***************************************
  * REFUSE RECOVERY REQUEST
  *
  * @return 'refused'
  ***************************************/

  function refuse($form){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****form **** -> '.$this->dumpRet($form));


    $recoveryId =  $form->ID;
    $refused_reason = str_replace('à', "a", str_replace('é', "e", str_replace('"', "", str_replace("'", "", $form->REASON))));
    $sqlRecovery = "UPDATE RECOVERY_FORM
                  SET STATUS='DENIED',
                      REASON='$refused_reason'
                  WHERE ID='$recoveryId';";
    // $this->app->log->info('****sqlRecovery **** -> '.$this->dumpRet($sqlRecovery));
    $queryRecovery = $this->db()->query($sqlRecovery);
    $this->informUser($form,'refused');
    return('refused');

  }
  /***************************************
  * GET FORM DATA & OSSOCIATED RECOVERY DATA
  *
  * @return 'refused'
  ***************************************/

  function getForm($formId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****getForm formId **** -> '.$this->dumpRet($formId));

    $sqlRecovery = "SELECT * FROM RECOVERY_FORM WHERE ID='$formId';";
    $formdata = $this->db()->query($sqlRecovery)->fetch(PDO::FETCH_ASSOC);
    // $this->app->log->info('****formdata **** -> '.$this->dumpRet($formdata));

    $recoveriesId = $formdata['USED_RECOVERY_IDS'];
    // $this->app->log->info('****recoveriesId **** -> '.$this->dumpRet($recoveriesId));
    $formdata['RECOVERIES_ID'] = json_decode($recoveriesId);

    // $this->app->log->info('****recoveriesId **** -> '.$this->dumpRet($recoveriesId));



    return($formdata);

  }

/******************************************************************************************
*
* FUNCTION addForm : [param : $recoveryId -> return ('form id')
*
*******************************************************************************************/

  function addForm($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info("data : ".$this->dumpRet($data));
  
    //we generate an custom id
    $recoveryId = $this->generateId('R_F');
    $date = strtotime($data->DATE);
    $date = date(DATE_ATOM,$date);
    
    // $this->app->log->info('****new brand **** -> '.$this->dumpRet($recoveryId));

    $this->db()->beginTransaction();
    $status = "ON_HOLD";
    $user_id = $_SESSION['userid'];

    $sql = "INSERT INTO RECOVERY_FORM(
                                      ID,
                                      STATUS,
                                      TO_USE,             
                                      DATE,
                                      CREATION_DATE,
                                      USER_ID) 
                               VALUES(:id,
                                     :status,
                                     :to_use,
                                     :date,
                                     CURRENT_TIMESTAMP,
                                     :user_id)";

    $query = $this->db()->prepare($sql);

    $result = $query->execute(array(                            
                            ':id'=>$recoveryId,                            
                            ':status'=>$status,
                            ':to_use'=>$data->TO_USE,
                            ':user_id'=>$user_id,
                            ':date'=>$date
                            ));

    $return = $query->fetch(PDO::FETCH_ASSOC);
    $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur
    $data->ID=$recoveryId;

      //we validate the form
      // $this->validate($data);
    $this->sendNotification();
      return ($recoveryId);

  }


/******************************************************************************************
*
* FUNCTION deleteRecovery : [param : $recoveryId -> return ('deleted')
*
*******************************************************************************************/

  function deleteRecovery($recoveryId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sqlRecovery = "SELECT USER_ID, ID, DATE, LABEL FROM RECOVERY WHERE ID='$recoveryId';";
    $resultInfo = $this->db()->query($sqlRecovery)->fetch(PDO::FETCH_ASSOC);
    $sql = "DELETE FROM recovery
                    WHERE id LIKE '$recoveryId'";
    $result = $this->db()->query($sql);
    $this->informUser($resultInfo,'recovery_deleted');
    return ('deleted');

  }

/******************************************************************************************
*
* FUNCTION generateId : [param : $type -> return Id = DAY[2]-MONTH[2]-YEAR[2]-Type[3]-NewOrderId[3]-Salt[2]
*
*******************************************************************************************/

  //FONCTION 

  function generateId($type) {
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';

//we count all existing recoveryId to predict the next id
    if ($type=='REC') {

      $sqlCount = "SELECT max(substring(id from 10 for 3)) as NEW_ID
                    FROM recovery
                    WHERE substring(id from 7 for 3) = '$type'
                    AND substring(id from 1 for 6) = '".date('ymd')."'";


      $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
      $resultCount = $resultCount['NEW_ID'];

      $recoveryId ="";
      $strCrypt ="";
      $newOrderInc = $resultCount + 1;
      if ($newOrderInc < 100){
        $newOrderInc = "0".$newOrderInc;
      }
      if ($newOrderInc < 10){
        $newOrderInc = "0".$newOrderInc;
      }

      $recoveryId = date('ymd').$type.$newOrderInc;
      $strCrypt = crypt($recoveryId,$salt);
      $strCrypt = substr($strCrypt,-2);
      $strCrypt = str_replace('/', 'A', $strCrypt);
      $strCrypt = str_replace('.', 'X', $strCrypt);
      $strCrypt = str_replace('\\', 'B', $strCrypt);
      $strCrypt = mb_strtoupper($strCrypt);

      $data = $recoveryId.$strCrypt;
      return $data;

    }else if ($type=='R_F') {

      $sqlCount = "SELECT max(substring(id from 10 for 3)) as NEW_ID
                    FROM recovery_form
                    WHERE substring(id from 7 for 3) = '$type'
                    AND substring(id from 1 for 6) = '".date('ymd')."'";


      $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
      $resultCount = $resultCount['NEW_ID'];

      $recoveryId ="";
      $strCrypt ="";
      $newOrderInc = $resultCount + 1;
      if ($newOrderInc < 100){
        $newOrderInc = "0".$newOrderInc;
      }
      if ($newOrderInc < 10){
        $newOrderInc = "0".$newOrderInc;
      }

      $recoveryId = date('ymd').$type.$newOrderInc;
      $strCrypt = crypt($recoveryId,$salt);
      $strCrypt = substr($strCrypt,-2);
      $strCrypt = str_replace('/', 'A', $strCrypt);
      $strCrypt = str_replace('.', 'X', $strCrypt);
      $strCrypt = str_replace('\\', 'B', $strCrypt);
      $strCrypt = mb_strtoupper($strCrypt);

      $data = $recoveryId.$strCrypt;
      return $data;
    }
  }
       
  function checkGoldenKey($orderId,$goldenKey) {

    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypted = crypt($orderId,$salt);
    $strCrypted = substr($strCrypted,-2);
    $strCrypted = str_replace('/', 'A', $strCrypted);
    $strCrypted = str_replace('.', 'X', $strCrypted);
    $strCrypted = str_replace('\\', 'B', $strCrypted);

      // $this->app->log->info(__CLASS__ . "::" . __METHOD__ . "$ strCrypted(".$strCrypted.")");

    if ($strCrypted == $goldenKey){
      
      $validRequest = true; // the given goldenKey is valid

      // $this->app->log->info(__CLASS__ . "::" . __METHOD__ . "$ Good !(");

    } elseif (mb_strtoupper($strCrypted) == $goldenKey){

      $validRequest = true; // the given goldenKey is valid

      // $this->app->log->info(__CLASS__ . "::" . __METHOD__ . "$mb_strtoupper Good !(");

    } elseif ($strCrypted != $goldenKey) {

      $validRequest = false; // the given goldenKey is not valid
      // $this->app->log->info(__CLASS__ . "::" . __METHOD__ . "$ Wrong !(");

    }

    return $validRequest;
  }
}
