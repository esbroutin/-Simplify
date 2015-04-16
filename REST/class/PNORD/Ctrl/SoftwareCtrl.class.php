<?php
/**
 *  REST service to handle software(s)
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;

class SoftwareCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the softwares list
  * @return array(array())
  **/

  function listSoftware($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->listSoftware($search);   

  }
  /**
  * list the maintenances
  * @return array(array())
  **/

  function listMaintenance($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\MaintenanceDAO($this->app);  
    return $dao->listMaintenance($data);   

  }

  /**
  * get the software data
  * @return array(array())
  **/

  function getSoftware($softwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->getSoftware($softwareId);   

  }

  /**
  * upload a file
  * @return array(array())
  **/
  function upload($maintenance){
    $data = [];
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $this->app->log->info('files : '.$this->dumpRet($_FILES));
    
    $this->app->log->info('****maintenance **** -> '.$this->dumpRet($maintenance));
    $filename = $_FILES['file']['name'];
    $destination = '/var/www/data/maintenances/' . $filename;
    move_uploaded_file( $_FILES['file']['tmp_name'] , $destination );
    $temp = split('_', $filename);
    $data['ID'] = $temp[0];
    $data['LOCATION'] = $destination;
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->addFileLocation($data);   
     
  }

  /**
  * send the pdf for specific order
  * @return array(array())
  **/

  function downloadFile($data){

    $dao = new \PNORD\Model\MaintenanceDAO($this->app);  
    $info = $dao->getMaintenance($data); 
    $this->app->log->info('****info **** -> '.$this->dumpRet($info));


      if (file_exists($info['FILES'])) {

      // header('Content-Description: File Transfer');
      header('Content-Type: application/zip');
      $name = split('maintenances/', $info['FILES']);
      $this->app->log->info('****name **** -> '.$this->dumpRet($name));
      $this->app->log->info('****info Files **** -> '.$this->dumpRet($info['FILES']));
      $this->app->log->info('****filesize(info[FILES]) **** -> '.$this->dumpRet(filesize($info['FILES'])));
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
  * delete the software
  * @return array(array())
  **/

  function deleteSoftware($softwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->deleteSoftware($softwareId);   

  }
  /**
  * add a new software (array)
  * @return success
  **/

  function addSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software : '.$this->dumpRet($software));
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->addSoftware($software);   

  }
  /**
  * add a new maintenance entry (array)
  * @return success
  **/

  function addMaintenance($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software : '.$this->dumpRet($software));
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->addMaintenance($data);   

  }
  /**
  * update a software (array)
  * @return success
  **/

  function updateSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software : '.$this->dumpRet($software));
    $dao = new \PNORD\Model\SoftwareDAO($this->app);  
    return $dao->updateSoftware($software);   

  }
    
}