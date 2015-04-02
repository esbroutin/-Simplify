<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class CertificateCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the certificate details
  * @return array(array())
  **/
  function getCertificate($certificateId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->getCertificate($certificateId);   
     
  }

  /**
  * get a certificate
  * @return array(array())
  **/
  function listCertificate($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->listCertificate($search);   
     
  }

  /**
  * delete the certificate list
  * @return array(array())
  **/
  function deleteCertificate($certificateId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->deleteCertificate($certificateId);   
     
  }

  /**
  * add a new certificate
  * @return array(array())
  **/
  function addCertificate($certificate){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->addCertificate($certificate);   
     
  }

  /**
  * update an existing certificate
  * @return array(array())
  **/
  function updateCertificate($certificate){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->updateCertificate($certificate);   
     
  }
    
}