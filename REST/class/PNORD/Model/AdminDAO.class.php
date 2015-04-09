<?php
 /**
 *  Class to handle DB access for brand
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class AdminDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Admin
  * @return array[{object}]
  **/
  function listAlert(){

    $sql = "SELECT license.id, license.label, license.date_end
            FROM license";
    // $this->app->log->info('sql : '.$sql);

    $licenseData = $this->db()->query($sql);
    $licensesData = $licenseData->fetchAll(PDO::FETCH_ASSOC); 
    // $this->app->log->info('*******count(licensesData) : '.count($licensesData));
    $alerts = [];

    //we check for each licenses the  remaining time, if < 1 month, we add it in the alerts array
    $alerts['LICENSE'] = [];

    for ($i=0;  $i < count($licensesData) ; $i++) { 
      $licenseId = $licensesData[$i]['ID'];
      $licenseEndTimestamp = strtotime($licensesData[$i]['DATE_END']);
      $nowTimestamp = time();
      $delta = $licenseEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($licenseId.' expire in less than 1 month: '.$delta);
        $alerts['LICENSE'][$i] = $licensesData[$i];
        $alerts['LICENSE'][$i]['TYPE'] = 'viewLicense';
        $alerts['LICENSE'][$i]['TIME_LEFT'] = $delta;
      }
    }

    $sql = "SELECT hardware.id, hardware.label, hardware.warranty_end
            FROM hardware";
    // $this->app->log->info('sql : '.$sql);

    $hardwareData = $this->db()->query($sql);
    $hardwaresData = $hardwareData->fetchAll(PDO::FETCH_ASSOC);
    $alertCount = count($alerts);
    //we check for each hardware the  remaining time, if < 1 month, we add it in the alerts array
    $alerts['HARDWARE'] = [];
    for ($i=0;  $i < count($hardwaresData) ; $i++) { 
      $hardwareId = $hardwaresData[$i]['ID'];
      $hardwareEndTimestamp = strtotime($hardwaresData[$i]['WARRANTY_END']);
      $nowTimestamp = time();
      $delta = $hardwareEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($hardwareId.'warranty expire in less than 1 month: '.$delta);
        $alerts['HARDWARE'][$i] = $hardwaresData[$i];
        $alerts['HARDWARE'][$i]['TYPE'] = 'viewHardware';
        $alerts['HARDWARE'][$i]['TIME_LEFT'] = $delta;
      }
    }

    $sql = "SELECT id, date_end
            FROM certificate";
    // $this->app->log->info('sql : '.$sql);

    $certificateData = $this->db()->query($sql);
    $certificatesData = $certificateData->fetchAll(PDO::FETCH_ASSOC);
    $alertCount = count($alerts);
    //we check for each hardware the  remaining time, if < 1 month, we add it in the alerts array
    $alerts['CERTIFICATE'] = [];

    for ($i=0;  $i < count($certificatesData) ; $i++) { 
      $certificateId = $certificatesData[$i]['ID'];
      $certificateEndTimestamp = strtotime($certificatesData[$i]['DATE_END']);
      $nowTimestamp = time();
      $delta = $certificateEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($hardwareId.'warranty expire in less than 1 month: '.$delta);
        $alerts['CERTIFICATE'][$i] = $certificatesData[$i];
        $alerts['CERTIFICATE'][$i]['TYPE'] = 'viewCertificate';
        $alerts['CERTIFICATE'][$i]['TIME_LEFT'] = $delta;
      }
    }
      if (isset($alerts['LICENSE'])) {
        if (isset($alerts['HARDWARE'])) {
          if (isset($alerts['CERTIFICATE'])) {
            $alerts = array_merge($alerts['LICENSE'],$alerts['HARDWARE'],$alerts['CERTIFICATE']);
          }else{
            $alerts = array_merge($alerts['LICENSE'],$alerts['HARDWARE']);
          }
        }else if (isset($alerts['CERTIFICATE'])) {
          $alerts = array_merge($alerts['LICENSE'],$alerts['CERTIFICATE']);
        }else{
          $alerts = $alerts['LICENSE'];
        }
      }else if (isset($alerts['HARDWARE'])) {
        if (isset($alerts['CERTIFICATE'])) {
          $alerts = array_merge($alerts['HARDWARE'],$alerts['CERTIFICATE']);
        }else{
          $alerts = $alerts['HARDWARE'];
        }
      }else if (isset($alerts['CERTIFICATE'])) {
          $alerts = $alerts['CERTIFICATE'];
      }
      
    // $this->app->log->info('*******(alerts) : '.$this->dumpRet($alerts));
      $alerts['COUNT_ALERTS'] = count($alerts);

    return ($alerts) ;           
  }

  
  /***************************************
  * UPDATE ADMIN DATA
  *
  * @return brandId
  ***************************************/

  function update($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****data **** -> '.$this->dumpRet($data));

    $password = md5($data->PASSWORD);
    $current_user = $_SESSION['userid'];

    $sql = "UPDATE TUSER
                    SET PASSWORD='$password'
                    WHERE LOGIN='$current_user';";

    // $this->app->log->info('****sql **** -> '.$this->dumpRet($sql));
    $query = $this->db()->query($sql);
    return('changed');

  } 

  /***************************************
  * LIST USERS
  *
  * @return brandId
  ***************************************/

  function listUsers($search){
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (LOGIN LIKE '%$search%'
            OR NAME LIKE '%".strtoupper($search)."%' 
            OR NAME LIKE '%".strtolower($search)."%'
            OR FAMILY_NAME LIKE '%".strtoupper($search)."%' 
            OR FAMILY_NAME LIKE '%".strtolower($search)."%')";
    }



    $current_user = $_SESSION['userid'];

    $sql = "SELECT * FROM TUSER
                    WHERE BOSS='$current_user' ".$conditionSql.";";

    $result = $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    // $this->app->log->info('result : '.$this->dumpRet($result));
    // $this->app->log->info('sql : '.$this->dumpRet($sql));
    return($result);

  }
  /***************************************
  * GET USER INFO
  *
  * @return brandId
  ***************************************/

  function getUser($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "SELECT name, family_name, role FROM TUSER WHERE login='$data'" ;

    $user = $this->db()->query($sql);
    $userInfo = $user->fetch(PDO::FETCH_ASSOC); 
    // $this->app->log->info('userInfo'.$this->dumpRet($userInfo));
    // $this->app->log->info('sql'.$this->dumpRet($sql));
    return($userInfo);

  }
}