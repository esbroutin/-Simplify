<?php
/**
 *  REST service to handle provider(s)
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class ProviderCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the providers list
  * @return array(array())
  **/

  function listProvider($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\ProviderDAO($this->app);  
    return $dao->listProvider($search);   

  }

  /**
  * get the provider data
  * @return array(array())
  **/

  function getProvider($providerId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\ProviderDAO($this->app);  
    return $dao->getProvider($providerId);   

  }

  /**
  * delete the provider
  * @return array(array())
  **/

  function deleteProvider($providerId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\ProviderDAO($this->app);  
    return $dao->deleteProvider($providerId);   

  }
  /**
  * add a new provider (array)
  * @return success
  **/

  function addProvider($provider){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('provider : '.$this->dumpRet($provider));
    $dao = new \PNORD\Model\ProviderDAO($this->app);  
    return $dao->addProvider($provider);   

  }
  /**
  * update a provider (array)
  * @return success
  **/

  function updateProvider($provider){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('provider : '.$this->dumpRet($provider));
    $dao = new \PNORD\Model\ProviderDAO($this->app);  
    return $dao->updateProvider($provider);   

  }
    
}