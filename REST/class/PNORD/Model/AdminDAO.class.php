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

    for ($i=0;  $i < count($licensesData) ; $i++) { 
      $licenseId = $licensesData[$i]['ID'];
      $licenseEndTimestamp = strtotime($licensesData[$i]['DATE_END']);
      $nowTimestamp = time();
      $delta = $licenseEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($licenseId.' expire in less than 1 month: '.$delta);
        $alerts[$i] = $licensesData[$i];
        $alerts[$i]['TYPE'] = 'viewLicense';
        $alerts[$i]['TIME_LEFT'] = $delta;
      }
    }

    $sql = "SELECT hardware.id, hardware.label, hardware.warranty_end
            FROM hardware";
    // $this->app->log->info('sql : '.$sql);

    $hardwareData = $this->db()->query($sql);
    $hardwaresData = $hardwareData->fetchAll(PDO::FETCH_ASSOC); 
    //we check for each licenses the  remaining time, if < 1 month, we add it in the alerts array

    for ($i=0;  $i < count($hardwaresData) ; $i++) { 
      $hardwareId = $hardwaresData[$i]['ID'];
      $hardwareEndTimestamp = strtotime($hardwaresData[$i]['WARRANTY_END']);
      $nowTimestamp = time();
      $delta = $hardwareEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($hardwareId.'warranty expire in less than 1 month: '.$delta);
        $alerts[$i] = $hardwaresData[$i];
        $alerts[$i]['TYPE'] = 'viewHardware';
        $alerts[$i]['TIME_LEFT'] = $delta;
      }
    }
    // $this->app->log->info('*******(alerts) : '.$this->dumpRet($message));
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
    
    $this->app->log->info('****data **** -> '.$this->dumpRet($data));

    $password = md5($data->PASSWORD);
    $current_user = $_SESSION['userid'];

    $sql = "UPDATE TUSER
                    SET PASSWORD='$password'
                    WHERE LOGIN='$current_user';";

    $this->app->log->info('****sql **** -> '.$this->dumpRet($sql));
    $query = $this->db()->query($sql);
    return('changed');

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
    $this->app->log->info('userInfo'.$this->dumpRet($userInfo));
    $this->app->log->info('sql'.$this->dumpRet($sql));
    return($userInfo);

  }
}