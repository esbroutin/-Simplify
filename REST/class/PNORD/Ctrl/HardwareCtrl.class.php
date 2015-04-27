<?php
/**
 *  REST service to handle maintenance
 *  @creationdate 2015-02-06 
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject;
//we include libraries
include('../REST/lib/mpdf/mpdf.php');
include('../REST/lib/phpqrcode/qrlib.php');

use mPDF;
use QRcode;

class HardwareCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }

  /**
  * get the hardware details
  * @return array(array())
  **/
  function getHardware($hardwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->getHardware($hardwareId);   
     
  }

  /**
  * list
  * @return array(array())
  **/
  function listHardware($search){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->listHardware($search);   
     
  }

  /**
  * listTypes
  * @return array(array())
  **/
  function listTypes(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->listTypes();   
     
  }

  /**
  * delete the hardware list
  * @return array(array())
  **/
  function deleteHardware($hardwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->deleteHardware($hardwareId);   
     
  }

  /**
  * add a new hardware
  * @return array(array())
  **/
  function addHardware($type){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->addHardware($type);   
     
  }
  /**
  * addType
  * @return array(array())
  **/
  function addType($hardware){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->addType($hardware);   
     
  }

  /**
  * update an existing hardware
  * @return array(array())
  **/
  function updateHardware($hardware){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    return $dao->updateHardware($hardware);   
     
  }

  function formatDates($str){
    $str = explode("-", substr($str, 0,10));
    $formated =  $str[2].'/'.$str[1].'/'.$str[0];
    return $formated;
  }


  function generatePDF($hardwareId){
//on get informations for current hardware ID
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $hardwareId = str_replace('"', '', $hardwareId);
    $this->app->log->info('hardwareId : '.$this->dumpRet($hardwareId));
    $dao = new \PNORD\Model\HardwareDAO($this->app);  
    $hardwareInfo = $dao->getHardware($hardwareId);

    // We format the dates to french style nigga !
    $hardwareInfo['EDITION_DATE'] =  $this->formatDates($hardwareInfo['EDITION_DATE']);
    $hardwareInfo['WARRANTY_START'] =  $this->formatDates($hardwareInfo['WARRANTY_START']);
    $hardwareInfo['DEPLOYMENT_DATE'] =  $this->formatDates($hardwareInfo['DEPLOYMENT_DATE']);
    $hardwareInfo['WARRANTY_END'] =  $this->formatDates($hardwareInfo['WARRANTY_END']);
    $operator = $_SESSION['userid'];
    $this->app->log->info('hardwareInfo : '.$this->dumpRet($hardwareInfo));

/**************************************************************************************
*
* Préparation des données pour remplacement dans le template [DONNEES ADMINISTRATIVES]
*
***************************************************************************************/
    $tblSearch = array("{{HARDWARE_ID}}",
                        "{{TYPE}}",
                        "{{BRAND}}",
                        "{{MODEL}}",
                        "{{SITE}}",
                        "{{BARCODE}}",
                        "{{DIRECTION}}",
                        "{{SERIAL_NUMBER}}",
                        "{{WARRANTY_START}}",
                        "{{WARRANTY_END}}",
                        "{{DEPLOYMENT_DATE}}",
                        "{{DESCRIPTION}}",
                        "{{OPERATOR}}",
                        "{{EDITION_DATE}}");

    $tblReplace = array($hardwareId,
                        $hardwareInfo['TYPE'], 
                        $hardwareInfo['BRAND']['LABEL'],
                        $hardwareInfo['LABEL'],
                        $hardwareInfo['SITE'],
                        $hardwareInfo['BARCODE'],
                        $hardwareInfo['DIRECTION'],
                        $hardwareInfo['SERIAL_NUMBER'],
                        $hardwareInfo['WARRANTY_START'],
                        $hardwareInfo['WARRANTY_END'],
                        $hardwareInfo['DEPLOYMENT_DATE'],
                        $hardwareInfo['DESCRIPTION'],
                        $operator,
                        $hardwareInfo['EDITION_DATE']); 

// /**************************************************************************************
// *
// * Genération du PDF et remplacement des champs dans le template
// *
// ***************************************************************************************/

  $mpdf=new mPDF();
  $mpdf->SetTitle("Province Nord - Fiche de deploiement");
  $mpdf->SetAuthor("Province Nord DSI");
  $mpdf->SetFont('Arial','B',8);
  $mpdf->SetWatermarkText("Province Nord");
  $mpdf->showWatermarkText = true;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->SetDisplayMode('fullpage'); 

    $html = file_get_contents($this->app->environment['APP_CONFIG']['WWW_PATH'] . "templates/deployment.tmpl.html");
    $html = $html;
    $html = str_replace($tblSearch,$tblReplace,$html);
    $this->app->log->info('new html : '.$this->dumpRet($html));
    $mpdf->WriteHTML($html);
    $result=$mpdf->Output("/var/www/data/DEPLOYMENT/".md5($hardwareId).".pdf","F");
    return($hardwareId); 
    
}

  /**
  * get pdf for hardware
  * @return array(array())
  **/

  function readPDF($data){

      $hardwareId = md5($data);
      $filename = "/var/www/data/DEPLOYMENT/".$data.".pdf";
      $fileMD5 = "/var/www/data/DEPLOYMENT/".$hardwareId.".pdf";

      if (file_exists($fileMD5)) {

      // header('Content-Description: File Transfer');
      header('Content-Type: application/pdf');
      header('Content-Disposition: inline; filename='.basename($filename));
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($fileMD5));
      ob_clean();
      flush();
      readfile($fileMD5);
      exit;

      }  
    }

  }  
