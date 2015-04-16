<?php
 /**
 *  Class to handle DB access for hardware
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class HardwareDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /***************************************
  * List Hardware
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listHardware($search){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $providerId ='';
    //if no search string, we dont filter the search 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (HARDWARE.ID LIKE '%$search%' 
            OR HARDWARE.LABEL LIKE '%".strtoupper($search)."%' 
            OR HARDWARE.LABEL LIKE '%".strtolower($search)."%'
            OR HARDWARE.BARCODE LIKE '%$search'
            OR PROVIDER.LABEL LIKE '%".strtoupper($search)."%' 
            OR PROVIDER.LABEL LIKE '%".strtolower($search)."%' 
            OR PROVIDER.STATUS LIKE '%".strtoupper($search)."' 
            OR PROVIDER.STATUS LIKE '%".strtolower($search)."%' 
            OR BRAND.LABEL LIKE '%".strtoupper($search)."' 
            OR BRAND.LABEL LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT hardware.id ,
                      hardware.label,
                      hardware.warranty_start,
                      hardware.warranty_end,
                      hardware.edition_date,
                      hardware.deployment_date,
                      hardware.site,
                      hardware.status,
                      hardware.barcode
              FROM hardware,provider,brand WHERE hardware.provider_id=provider.id AND hardware.brand_id=brand.id ".$conditionSql." ORDER BY EDITION_DATE DESC";

    	// $this->app->log->info('sql : '.$sql);

      $hardwareData = $this->db()->query($sql);
      $hardwaresData = $hardwareData->fetchAll(PDO::FETCH_ASSOC); 

      //we get the associated brand & provider 
      for ($t = 0; $t <= count($hardwaresData) -1; $t++) {

        $hardwareId = $hardwaresData[$t]['ID'];

        $Sqlprovider = "SELECT provider.id as ID,
                               provider.label as LABEL,
                               provider.contact as CONTACT,
                               provider.web_address as WEB_ADDRESS,
                               provider.description as DESCRIPTION
                                 FROM provider,hardware 
                              WHERE hardware.provider_id = provider.id AND hardware.id = '$hardwareId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $provider = $this->db()->query($Sqlprovider);
        $hardwaresData[$t]['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

        $Sqlprovider = "SELECT brand.id as ID,
                               brand.label as LABEL,
                               brand.web_address as WEB_ADDRESS
                                 FROM brand,hardware 
                              WHERE hardware.brand_id = brand.id AND hardware.id = '$hardwareId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $brand = $this->db()->query($Sqlprovider);
        $hardwaresData[$t]['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    }

      // $this->app->log->info('hardwaresData : '. $this->dumpRet($hardwaresData));
      return $hardwaresData ;          
  } 
     
  
  /**
  * Get Hardware
  * @return array[{object}]
  **/
  function getHardware($hardwareId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('hardwareId : '.$hardwareId);

    $sql = "SELECT *          
              FROM hardware
              WHERE hardware.id = '$hardwareId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

  //we get the associated provider data  
    $Sqlprovider = "SELECT provider.id as ID,
                           provider.label as LABEL,
                           provider.contact as CONTACT,
                           provider.web_address as WEB_ADDRESS,
                           provider.description as DESCRIPTION,
                           provider.status as STATUS
                             FROM provider,hardware 
                          WHERE hardware.provider_id = provider.id AND hardware.id = '$hardwareId'";
    // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
    $provider = $this->db()->query($Sqlprovider);
    $result['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

  //we get the associated brand data  
    $SqlBrand = "SELECT brand.id as ID,
                           brand.label as LABEL,
                           brand.web_address as WEB_ADDRESS
                             FROM brand,hardware 
                          WHERE hardware.brand_id = brand.id AND hardware.id = '$hardwareId'";
    // $this->app->log->info('SqlBrand : '. $this->dumpRet($SqlBrand));
    $brand = $this->db()->query($SqlBrand);
    $result['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

      $hardwareEndTimestamp = strtotime($result['WARRANTY_END']);
      $nowTimestamp = time();
      $delta = $hardwareEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        $result['ALERT'] = 1;
      }
    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 
 
  /***************************************
  * Add Hardware
  * @return array[{object}]
  ***************************************/

  function addHardware($hardware){

    $this->app->log->notice(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****hardware **** -> '.$this->dumpRet($hardware));

    $label = $hardware->LABEL;
    $warranty_start = $hardware->WARRANTY_START;
    $warranty_end = $hardware->WARRANTY_END;

    //we check for undefined variables
    if (!isset($hardware->DESCRIPTION)) {
      $hardware->DESCRIPTION = '-';
    };
    if (!isset($hardware->TYPE)) {
      $hardware->TYPE = '-';
    };
    if (!isset($hardware->SITE)) {
      $hardware->SITE = '-';
    };
    if (!isset($hardware->DIRECTION)) {
      $hardware->DIRECTION = '-';
    };
    if (!isset($hardware->DEPLOYMENT_DATE)) {
      $hardware->DEPLOYMENT_DATE = '-';
    };
    if (!isset($hardware->BARCODE)) {
      $hardware->BARCODE = '-';
    };
    if (!isset($hardware->SERIAL_NUMBER)) {
      $hardware->SERIAL_NUMBER = '-';
    };
    if (!isset($hardware->BRAND_ID)) {
      $hardware->BRAND_ID = '-';
    };
    if (!isset($hardware->PROVIDER_ID)) {
      $hardware->PROVIDER_ID = '-';
    };
    if (!isset($hardware->SERIAL_NUMBER)) {
      $hardware->SERIAL_NUMBER = '-';
    };

    $description = $hardware->DESCRIPTION;
    $brand_Id = $hardware->BRAND_ID;      
    $type = $hardware->TYPE;    
    $site = $hardware->SITE;    
    $direction = $hardware->DIRECTION;    
    $deployment_date = $hardware->DEPLOYMENT_DATE;    
    $serial_number = $hardware->SERIAL_NUMBER;
    $provider_Id = $hardware->PROVIDER_ID;
    $barcode = $hardware->BARCODE;
    $serial_number = $hardware->SERIAL_NUMBER;

    //we generate an custom id
    $hardwareId = $this->generateId('HAR');

    //we use transaction to avoid sql injection level 1 & bad character escaping
    $this->db()->beginTransaction();

    $sqlHardware = "INSERT INTO HARDWARE(
                                      ID,
                                      LABEL,
                                      WARRANTY_START,
                                      WARRANTY_END,
                                      TYPE,
                                      SITE,
                                      DIRECTION,
                                      DESCRIPTION,
                                      DEPLOYMENT_DATE,
                                      BRAND_ID,
                                      SERIAL_NUMBER,
                                      PROVIDER_ID,
                                      BARCODE,
                                      EDITION_DATE) 
                               VALUES(:id,
                                     :label,
                                     :warranty_start,
                                     :warranty_end,
                                     :type,
                                     :site,
                                     :direction,
                                     :description,
                                     :deployment_date,
                                     :brand_id,
                                     :serial_number,
                                     :provider_Id,
                                     :barcode,
                                     CURRENT_TIMESTAMP)";

    $queryHardware = $this->db()->prepare($sqlHardware);

    $result = $queryHardware->execute(array(                            
                            ':id'=>$hardwareId,                            
                            ':label'=>$label,
                            ':warranty_start'=>$warranty_start,
                            ':warranty_end'=>$warranty_end,
                            ':type'=>$type,
                            ':site'=>$site,
                            ':direction'=>$direction,
                            ':description'=>$description,
                            ':deployment_date'=>$deployment_date,
                            ':brand_id'=>$brand_Id,
                            ':serial_number'=>$serial_number,
                            ':provider_Id'=>$provider_Id,
                            ':barcode'=>$barcode
                            ));

      $return = $queryHardware->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($hardwareId);

  }

  /***************************************
  * UPDATE HARDWARE
  *
  * @return licenceId
  ***************************************/

  function updateHardware($hardware){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****hardware **** -> '.$this->dumpRet($hardware));


    $hardwareId =  $hardware->ID;
    $label = $hardware->LABEL;
    $warranty_start = $hardware->WARRANTY_START;
    $warranty_end = $hardware->WARRANTY_END;

    //we check for undefined variables
    if (!isset($hardware->DESCRIPTION)) {
      $hardware->DESCRIPTION = '-';
    };
    if (!isset($hardware->WEB_ADDRESS)) {
      $hardware->WEB_ADDRESS = '-';
    };
    if (!isset($hardware->BRAND_ID)) {
      $hardware->BRAND_ID = '-';
    };
    if (!isset($hardware->PROVIDER_ID)) {
      $hardware->BRAND_ID = '-';
    };
    if (!isset($hardware->DEPLOYMENT_DATE)) {
      $hardware->DEPLOYMENT_DATE = '-';
    };
    if (!isset($hardware->SERIAL_NUMBER)) {
      $hardware->SERIAL_NUMBER = '-';
    };
    if (!isset($hardware->BARCODE)) {
      $hardware->BARCODE = '-';
    };
    if (!isset($hardware->SITE)) {
      $hardware->SITE = '-';
    };
    if (!isset($hardware->DIRECTION)) {
      $hardware->DIRECTION = '-';
    };
    if (!isset($hardware->STATUS)) {
      $hardware->STATUS = '-';
    };
    if (!isset($hardware->TYPE)) {
      $hardware->TYPE = '-';
    };
    $description =  str_replace("'", "", $hardware->DESCRIPTION);
    $brand_Id = str_replace("'", "", $hardware->BRAND_ID);
    $serial_number = str_replace("'", "", $hardware->SERIAL_NUMBER);
    $barcode = str_replace("'", "", $hardware->BARCODE);
    $site = str_replace("'", "", $hardware->SITE);
    $direction = str_replace("'", "", $hardware->DIRECTION);
    $status = str_replace("'", "", $hardware->STATUS);
    $type = str_replace("'", "", $hardware->TYPE);
    $provider_Id = str_replace("'", "", $hardware->PROVIDER_ID);
    $deployment_date = $hardware->DEPLOYMENT_DATE;

    $sqlHardware = "UPDATE HARDWARE
                  SET LABEL='$label',
                      WARRANTY_START='$warranty_start',
                      WARRANTY_END='$warranty_end',
                      DESCRIPTION='$description',
                      DEPLOYMENT_DATE='$deployment_date',
                      BRAND_ID='$brand_Id',
                      BARCODE='$barcode',
                      SITE='$site',
                      DIRECTION='$direction',
                      STATUS='$status',
                      TYPE='$type',
                      SERIAL_NUMBER='$serial_number',
                      PROVIDER_ID='$provider_Id',
                      EDITION_DATE=CURRENT_TIMESTAMP
                  WHERE ID='$hardwareId';";
    // $this->app->log->info('****sqlHardware **** -> '.$this->dumpRet($sqlHardware));
    $queryHardware = $this->db()->query($sqlHardware);
    return($hardwareId);

  }

/******************************************************************************************
*
* FUNCTION deleteHardware : [param : $hardwareId -> return ('deleted')
*
*******************************************************************************************/

  function deleteHardware($hardwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "DELETE FROM hardware
                    WHERE id LIKE '$hardwareId'";
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

//we count all existing hardwareId to predict the next id

    $sqlCount = "SELECT max(substring(hardware.id from 10 for 3)) as NEW_ID
                  FROM hardware
                  WHERE substring(hardware.id from 7 for 3) = '$type'
                  AND substring(hardware.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $hardwareId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $hardwareId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($hardwareId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $hardwareId.$strCrypt;
    return $data;
  }
       
}
