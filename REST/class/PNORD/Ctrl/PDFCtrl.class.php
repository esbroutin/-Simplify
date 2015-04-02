<?php
/**
 *  REST service to handle pdf & ticket generation
 *  @creationdate 2014-03-31  
 **/ 
  
namespace PNORD\Ctrl;

use PNORD\BaseSimplifyObject; 
//on inclu l'api MPDF pour générer le bon
include('../REST/lib/mpdf/mpdf.php');
include('../REST/lib/phpqrcode/qrlib.php');

use mPDF;
use QRcode;

class PDFCtrl extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app);
  }
  /**
  * generate the pdf
  * @return array(array())
  **/
  function generatePDF($formId){
//on get informations for each recovery day associated with the form
      $formId = str_replace('"', '', $formId);
      $dao = new \PNORD\Model\RecoveryDAO($this->app);
      $formData = $dao->getForm($formId);
      $dao = new \PNORD\Model\AdminDAO($this->app);
      $userInfo = $dao->getUser($formData['USER_ID']);


      for ($i=0; $i < count($formData['RECOVERIES_ID']); $i++) { 
        $dao = new \PNORD\Model\RecoveryDAO($this->app);
        $recoveryInfo = $dao->getRecovery($formData['RECOVERIES_ID'][$i]->id);
        $formData['RECOVERIES_ID'][$i] = $recoveryInfo;

      }
    //on génère le QR Code  de la demande de récupération

// // *********** ! A MODIFIER SELON SERVEUR ******************

    $QRtext = "http://10.16.12.98/rest/pdf/read/".$formId;
    // $QRtext = "http://10.1.0.62/orloges/rest/pdf/read/".$orderId;
    $tempQRcode = tempnam(sys_get_temp_dir(),$formId);

    QRcode::png($QRtext,$tempQRcode);

    $recoveries=$formData['RECOVERIES_ID']; // object weightOrders contenant les propriétés BRIDGE_ID , WEIGHT, TEMP, DATE, LOADED, MATERIAL_ID, INOUT, ID
    $formData['DATE'] = substr($formData['DATE'], 0,10);
/**************************************************************************************
*
* Préparation des données pour remplacement dans le template [DONNEES ADMINISTRATIVES]
*
***************************************************************************************/
    $tblSearch = array("{{QRCODE}}",
                        "{{FORM_ID}}",
                        "{{TO_USE}}",
                        "{{DATE}}",
                        "{{USER_FAMILY_NAME}}",
                        "{{USER_NAME}}",
                        "{{ROLE}}",
                        "{{CREATION_DATE}}");

    $tblReplace = array($tempQRcode,
                        $formId,
                        $formData['TO_USE'], 
                        $formData['DATE'],
                        $userInfo['FAMILY_NAME'],
                        $userInfo['NAME'],
                        $userInfo['ROLE'],
                        substr($formData['CREATION_DATE'], 0,10)); 

/**************************************************************************************
*
* Préparation des données pour remplacement dans le template [DONNEES RECOVERIES]
*
***************************************************************************************/

    $countRecoveries = count($recoveries);
    $recoveryBloc = '';
    for ($i=0; $i < $countRecoveries; $i++) {
      // for each recovery, we create a new table with all the recovery data
      $recoveryBloc .= "<table class='infoTable' width ='100%'>
      <tr><th>Date</th><th>Motif</th><th>Récupération utilisée (h)</th></tr>
      <tr><td>".substr($recoveries[$i]['DATE'], 0,10)."</td><td>".$recoveries[$i]['LABEL']."</td><td class='text-danger'>".$recoveries[$i]['RECOVERY_USED']."</td></tr>
      </table>";

    }

    $blocSearch = "[[BLOC_RECOVERIES]]";
    $blocReplace = $recoveryBloc;

// /**************************************************************************************
// *
// * Genération du PDF et remplacement des champs dans le template
// *
// ***************************************************************************************/

  $mpdf=new mPDF();
  $mpdf->SetTitle("Province Nord - Demande de récupération n_".$formId);
  $mpdf->SetAuthor("Province Nord DSI");
  $mpdf->SetFont('Arial','B',8);
  $mpdf->SetWatermarkText("Province Nord");
  $mpdf->showWatermarkText = true;
  $mpdf->watermark_font = 'DejaVuSansCondensed';
  $mpdf->watermarkTextAlpha = 0.1;
  $mpdf->SetDisplayMode('fullpage'); 

    $html = file_get_contents($this->app->environment['APP_CONFIG']['WWW_PATH'] . "templates/recovery.tmpl.html");
    $html = $html;
    $html = str_replace($tblSearch,$tblReplace,$html);
        // $this->app->log->info("(tblReplace generatePDF -- html : ".$this->dumpRet($html).")");
    $html = str_replace($blocSearch,$blocReplace,$html);
    $mpdf->WriteHTML($html);
    $result=$mpdf->Output("/var/www/data/PDF/".md5($formId).".pdf","F");
    return($formId);  
  } 

  /**
  * send the pdf for specific order
  * @return array(array())
  **/

  function readPDF($data){

    $goldenKey = substr($data, -2);
    $formId = substr($data, 0,-2);
    $dao = new \PNORD\Model\RecoveryDAO($this->app);
    $validkey = $dao->checkGoldenKey($formId,$goldenKey);

    if ($validkey !=false){

      $formIdMD5 = md5($data);
      $filename = "/var/www/data/PDF/".$data.".pdf";
      $fileMD5 = "/var/www/data/PDF/".$formIdMD5.".pdf";

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

}