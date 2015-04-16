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
  * upload a file
  * @return array(array())
  **/
  function upload($certificate){
    $data = [];
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****certificate **** -> '.$this->dumpRet($certificate));
    $filename = $_FILES['file']['name'];
    $destination = '/var/www/data/certificates/' . $filename;
    move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );
    $temp = split('_', $filename);
    $data['ID'] = $temp[0];
    $data['LOCATION'] = $destination;
    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    return $dao->addFileLocation($data);   
     
  }

  /**
  * send file to user
  * @return array(array())
  **/

  function downloadFile($data){

    $dao = new \PNORD\Model\CertificateDAO($this->app);  
    $info = $dao->getCertificate($data); 
    $this->app->log->info('****info **** -> '.$this->dumpRet($info));


      if (file_exists($info['FILES'])) {

      // header('Content-Description: File Transfer');
      header('Content-Type: application/zip');
      $name = split('certificates/', $info['FILES']);
      // $this->app->log->info('****name **** -> '.$this->dumpRet($name));
      // $this->app->log->info('****info Files **** -> '.$this->dumpRet($info['FILES']));
      // $this->app->log->info('****filesize(info[FILES]) **** -> '.$this->dumpRet(filesize($info['FILES'])));
      header('Content-Disposition: inline; filename='.basename($name[1]));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($info['FILES']));
      ob_clean();
      flush();
      readfile($info['FILES']);
      exit;

      }  

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