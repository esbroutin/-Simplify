<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class RecoveryCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the recovery details
  * @return array(array())
  **/
  function getRecovery($recoveryId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->getRecovery($recoveryId);   
     
  }

  /**
  * get a recovery
  * @return array(array())
  **/
  function listRecovery($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->listRecovery($search);   
     
  }

  /**
  * get a recovery
  * @return array(array())
  **/
  function listAdmin($userId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->listAdmin($userId);   
     
  }


  /**
  * countForms
  * @return array(array())
  **/
  function countForms(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->countForms();   
     
  }

  /**
  * get a recovery
  * @return array(array())
  **/
  function listForm(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->listForm();   
     
  }
  /**
  * get a recovery
  * @return array(array())
  **/
  function listFormAdmin(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->listFormAdmin();   
     
  }

  /**
  * delete the recovery list
  * @return array(array())
  **/
  function deleteRecovery($recoveryId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->deleteRecovery($recoveryId);   
     
  }

  /**
  * refuse a recovery request
  * @return array(array())
  **/
  function refuse($form){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->refuse($form);   
     
  }

  /**
  * validate a recovery request
  * @return array(array())
  **/
  function validate($form){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->validate($form);   
     
  }

  /**
  * add a new recovery form request
  * @return array(array())
  **/
  function addForm($recovery){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->addForm($recovery);   
     
  }

  /**
  * add a new recovery
  * @return array(array())
  **/
  function addRecovery($recovery){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->addRecovery($recovery);   
     
  }

  /**
  * update an existing recovery
  * @return array(array())
  **/
  function updateRecovery($recovery){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);  
    return $dao->updateRecovery($recovery);   
     
  }
    
}