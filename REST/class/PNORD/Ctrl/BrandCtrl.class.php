<?php
/**
 *  REST service to handle Brand(s)
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class BrandCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the Brands list
  * @return array(array())
  **/

  function listBrand($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\BrandDAO($this->app);  
    return $dao->listBrand($search);   

  }

  /**
  * get the Brand data
  * @return array(array())
  **/

  function getBrand($brandId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\BrandDAO($this->app);  
    return $dao->getBrand($brandId);   

  }

  /**
  * delete the Brand
  * @return array(array())
  **/

  function deleteBrand($brandId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\BrandDAO($this->app);  
    return $dao->deleteBrand($brandId);   

  }
  /**
  * add a new Brand (array)
  * @return success
  **/

  function addBrand($Brand){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $this->app->log->info('Brand : '.$this->dumpRet($Brand));
    $dao = new \PNORD\Model\BrandDAO($this->app);  
    return $dao->addBrand($Brand);   

  }
  /**
  * update a Brand (array)
  * @return success
  **/

  function updateBrand($Brand){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('Brand : '.$this->dumpRet($Brand));
    $dao = new \PNORD\Model\BrandDAO($this->app);  
    return $dao->updateBrand($Brand);   

  }
    
}