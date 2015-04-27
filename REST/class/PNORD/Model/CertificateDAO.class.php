<?php
 /**
 *  Class to handle DB access for certificate
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class CertificateDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /***************************************
  * List Certificate
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listCertificate($search){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $providerId ='';
    //if no search string, we dont filter the search 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "WHERE CERTIFICATE.ID LIKE '%$search%' 
            OR CERTIFICATE.COMMON_NAME LIKE '%".strtoupper($search)."%' 
            OR CERTIFICATE.COMMON_NAME LIKE '%".strtolower($search)."%'
            OR CERTIFICATE.ORGANIZATION LIKE '%".strtoupper($search)."%' 
            OR CERTIFICATE.ORGANIZATION LIKE '%".strtolower($search)."%'
            OR CERTIFICATE.ORGANIZATION_UNIT LIKE '%".strtoupper($search)."%' 
            OR CERTIFICATE.ORGANIZATION_UNIT LIKE '%".strtolower($search)."%'";
    }

      $sql = "SELECT *
              FROM certificate ".$conditionSql." ORDER BY CREATION_DATE DESC";

    	// $this->app->log->info('sql : '.$sql);

      $certificateData = $this->db()->query($sql);
      $certificatesData = $certificateData->fetchAll(PDO::FETCH_ASSOC); 

    //   //we get the associated brand & provider 
    //   for ($t = 0; $t <= count($certificatesData) -1; $t++) {

    //     $certificateId = $certificatesData[$t]['ID'];

    //     $Sqlprovider = "SELECT provider.id as ID,
    //                            provider.label as LABEL,
    //                            provider.contact as CONTACT,
    //                            provider.web_address as WEB_ADDRESS,
    //                            provider.description as DESCRIPTION
    //                              FROM provider,certificate 
    //                           WHERE certificate.provider_id = provider.id AND certificate.id = '$certificateId'";
    //     // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
    //     $provider = $this->db()->query($Sqlprovider);
    //     $certificatesData[$t]['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

    //     $Sqlprovider = "SELECT brand.id as ID,
    //                            brand.label as LABEL,
    //                            brand.web_address as WEB_ADDRESS
    //                              FROM brand,certificate 
    //                           WHERE certificate.brand_id = brand.id AND certificate.id = '$certificateId'";
    //     // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
    //     $brand = $this->db()->query($Sqlprovider);
    //     $certificatesData[$t]['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    // }

      // $this->app->log->info('certificatesData : '. $this->dumpRet($certificatesData));
      return $certificatesData ;          
  } 
     
  
  /**
  * Get Certificate
  * @return array[{object}]
  **/
  function getCertificate($certificateId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('certificateId : '.$certificateId);

    $sql = "SELECT *          
              FROM certificate
              WHERE certificate.id = '$certificateId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

    if ($result['AUTO_SIGNED'] == 'false') {
      //we get the associated provider data  
        $Sqlprovider = "SELECT provider.id as ID,
                               provider.label as LABEL,
                               provider.contact as CONTACT,
                               provider.web_address as WEB_ADDRESS,
                               provider.description as DESCRIPTION,
                               provider.status as STATUS
                                 FROM provider,certificate 
                              WHERE certificate.provider_id = provider.id AND certificate.id = '$certificateId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $provider = $this->db()->query($Sqlprovider);
        $result['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

      //we get the associated brand data  
        $SqlBrand = "SELECT brand.id as ID,
                               brand.label as LABEL,
                               brand.web_address as WEB_ADDRESS
                                 FROM brand,certificate 
                              WHERE certificate.brand_id = brand.id AND certificate.id = '$certificateId'";
        // $this->app->log->info('SqlBrand : '. $this->dumpRet($SqlBrand));
        $brand = $this->db()->query($SqlBrand);
        $result['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    }

    // we check if the certificate is still valid in a month (30 days)
    $certificateEndTimestamp = strtotime($result['DATE_END']);
    $nowTimestamp = time();
    $delta = $certificateEndTimestamp - $nowTimestamp;
    if ($delta < 2629743){
      $result['ALERT'] = 1;
    }
    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 
 
  /***************************************
  * Add Certificate
  * @return array[{object}]
  ***************************************/

  function addCertificate($certificate){

    $this->app->log->notice(__CLASS__ . '::' . __METHOD__);
    
    $this->app->log->info('****certificate **** -> '.$this->dumpRet($certificate));

    //we check for undefined variables
    if (!isset($certificate->CERTIFICATE_AUTHORITY)) {
      $certificate->CERTIFICATE_AUTHORITY = '-';
    };
    if (!isset($certificate->COMMENTS)) {
      $certificate->COMMENTS = '-';
    }
    if ($certificate->AUTO_SIGNED === true) {
      $certificate->AUTO_SIGNED = 'true';
      $brand_Id = '-';
      $provider_Id = '-';
    }else{
      $provider_Id = $certificate->PROVIDER->ID;
      $brand_Id = $certificate->BRAND->ID;
      $certificate->AUTO_SIGNED = 'false';
    };

    //we generate an custom id
    $certificateId = $this->generateId('CER');

    //we use transaction to avoid sql injection level 1 & bad character escaping
    $this->db()->beginTransaction();

    $sqlCertificate = "INSERT INTO CERTIFICATE(
                                      ID,
                                      COMMON_NAME,
                                      ORGANIZATION,
                                      ORGANIZATION_UNIT,
                                      TOWN,
                                      REGION,
                                      COUNTRY,
                                      DATE_START,
                                      DATE_END,
                                      AUTO_SIGNED,
                                      CERTIFICATE_AUTHORITY,
                                      PROVIDER_ID,
                                      COMMENTS,
                                      CREATION_DATE,
                                      BRAND_ID) 
                               VALUES(:id,
                                     :common_name,
                                     :organization,
                                     :organization_unit,
                                     :town,
                                     :region,
                                     :country,
                                     :date_start,
                                     :date_end,
                                     :auto_signed,
                                     :certificate_authority,
                                     :provider_Id,
                                     :comments,
                                     CURRENT_TIMESTAMP,
                                     :brand_id)";

    $queryCertificate = $this->db()->prepare($sqlCertificate);

    $result = $queryCertificate->execute(array(                            
                            ':id'=>$certificateId,                            
                            ':common_name'=>$certificate->COMMON_NAME,
                            ':organization'=>$certificate->ORGANIZATION,
                            ':organization_unit'=>$certificate->ORGANIZATION_UNIT,
                            ':town'=>$certificate->TOWN,
                            ':region'=>$certificate->REGION,
                            ':country'=>$certificate->COUNTRY,
                            ':date_start'=>$certificate->DATE_START,
                            ':date_end'=>$certificate->DATE_END,
                            ':auto_signed'=>$certificate->AUTO_SIGNED,
                            ':certificate_authority'=>$certificate->CERTIFICATE_AUTHORITY,
                            ':provider_Id'=>$provider_Id,
                            ':comments'=>$certificate->COMMENTS,
                            ':brand_id'=>$brand_Id
                            ));

      $return = $queryCertificate->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($certificateId);

  }

  /***************************************
  * UPDATE CERTIFICATE
  *
  * @return licenceId
  ***************************************/

  function updateCertificate($certificate){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****certificate **** -> '.$this->dumpRet($certificate));
    if ($certificate->AUTO_SIGNED == 'true') {
      $brand_Id = '-';
      $provider_Id = '-';
    }else{
      $provider_Id = $certificate->PROVIDER->ID;
      $brand_Id = $certificate->BRAND->ID;
      $certificate->AUTO_SIGNED = 'false';
    };
    $certificateId =  $certificate->ID;

    $sqlCertificate = "UPDATE CERTIFICATE
                  SET COMMON_NAME='".str_replace("'","",$certificate->COMMON_NAME)."',
                      DATE_START='$certificate->DATE_START',
                      DATE_END='$certificate->DATE_END',
                      ORGANIZATION='".str_replace("'","",$certificate->ORGANIZATION)."',
                      ORGANIZATION_UNIT='".str_replace("'","",$certificate->ORGANIZATION_UNIT)."',
                      BRAND_ID='".str_replace("'","",$brand_Id)."',
                      PROVIDER_ID='".str_replace("'","",$provider_Id)."',
                      TOWN='".str_replace("'","",$certificate->TOWN)."',
                      REGION='".str_replace("'","",$certificate->REGION)."',
                      COUNTRY='".str_replace("'","",$certificate->COUNTRY)."',
                      CERTIFICATE_AUTHORITY='".str_replace("'","",$certificate->CERTIFICATE_AUTHORITY)."',
                      COMMENTS='".str_replace("'","",$certificate->COMMENTS)."'
                  WHERE ID LIKE '$certificateId';";
    $this->app->log->info('****sqlCertificate **** -> '.$this->dumpRet($sqlCertificate));
    $queryCertificate = $this->db()->query($sqlCertificate);
    return($certificateId);

  }



  /***************************************
  * UPDATE FILE LOCATION
  *
  * @return licenceId
  ***************************************/

  function addFileLocation($data){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $certificateId =  $data['ID'];
    $certificateFileLocation =  $data['LOCATION'];

    $sqlCertificate = "UPDATE CERTIFICATE
                  SET FILES='$certificateFileLocation'
                  WHERE ID LIKE '$certificateId';";
    $queryCertificate = $this->db()->query($sqlCertificate);
    return($certificateId);

  }



/******************************************************************************************
*
* FUNCTION deleteCertificate : [param : $certificateId -> return ('deleted')
*
*******************************************************************************************/

  function deleteCertificate($certificateId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "DELETE FROM certificate
                    WHERE id LIKE '$certificateId'";
    $result = $this->db()->query($sql);
    return ('deleted');        

  }

/******************************************************************************************
*
* FUNCTION generateId : [param : $type -> return Id = DAY[2]-MONTH[2]-YEAR[2]-Type[3]-NewOrderId[3]-Salt[2]
*
*******************************************************************************************/

  //FONCTION 

  function generateId($type) {

//we count all existing certificateId to predict the next id

    $sqlCount = "SELECT max(substring(certificate.id from 10 for 3)) as NEW_ID
                  FROM certificate
                  WHERE substring(certificate.id from 7 for 3) = '$type'
                  AND substring(certificate.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $certificateId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $certificateId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($certificateId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $certificateId.$strCrypt;
    return $data;
  }
       
}
