<?php
 /**
 *  Class to handle DB access for software
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class SoftwareDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Software
  * @return array[{object}]
  **/
  function listSoftware($search){

    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (SOFTWARE.LABEL LIKE '%".strtoupper($search)."%' 
            OR SOFTWARE.LABEL LIKE '%".strtolower($search)."%' 
            OR BRAND.LABEL LIKE '%".strtoupper($search)."%' 
            OR BRAND.LABEL LIKE '%".strtolower($search)."%' 
            OR SOFTWARE.ID LIKE '%".strtoupper($search)."%' 
            OR SOFTWARE.ID LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT  software.id ,
                      software.label,
                      software.edition_date,
                      software.current_version,
                      software.next_version,
                      software.service_pack
              FROM software,brand WHERE software.status NOT IN ('DELETED') AND software.brand_id = brand.id ".$conditionSql." ORDER BY software.EDITION_DATE DESC";
      // $this->app->log->info('sql : '.$sql);

      $softwareData = $this->db()->query($sql);
      $softwaresData = $softwareData->fetchAll(PDO::FETCH_ASSOC); 

      //we get the associated brand & provider 
      for ($t = 0; $t <= count($softwaresData) -1; $t++) {

        $softwareId = $softwaresData[$t]['ID'];

        $Sqlprovider = "SELECT brand.id as ID,
                               brand.label as LABEL,
                               brand.web_address as WEB_ADDRESS
                                 FROM brand,software 
                              WHERE software.brand_id = brand.id AND software.id = '$softwareId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $brand = $this->db()->query($Sqlprovider);
        $softwaresData[$t]['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);
    }
      return $softwaresData ;           
  } 
  
  /**
  * Get software
  * @return array[{object}]
  **/
  function getSoftware($softwareId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software ID : '.$softwareId);

    $sql = "SELECT * FROM software
              WHERE software.id = '$softwareId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);
      //we get the associated brand 

        $Sqlprovider = "SELECT brand.id as ID,
                               brand.label as LABEL,
                               brand.web_address as WEB_ADDRESS
                                 FROM brand,software 
                              WHERE software.brand_id = brand.id AND software.id = '$softwareId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $brand = $this->db()->query($Sqlprovider);
        $result['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 

  /**
  * Add Software
  * @return array[{object}]
  **/
  function addSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('software :' .$this->dumpRet($software));

    //we check for undefined variables
    if (!isset($software->SERVICE_PACK)) {
      $software->SERVICE_PACK = '-';
    };
    if (!isset($software->CURRENT_VERSION)) {
      $software->CURRENT_VERSION = '-';
    };
    if (!isset($software->NEXT_VERSION)) {
      $software->NEXT_VERSION = '-';
    };
    if (!isset($software->TO_DO)) {
      $software->TO_DO = '-';
    };
    if (!isset($software->WEB_ADDRESS)) {
      $software->WEB_ADDRESS = '-';
    };
    $label = $software->LABEL;
    $web_address = $software->WEB_ADDRESS;
    $service_pack = $software->SERVICE_PACK;
    $current_version = $software->CURRENT_VERSION;
    $brand_id = $software->BRAND_ID;
    $next_version = $software->NEXT_VERSION;
    $to_do = $software->TO_DO;

    //we generate an custom id
    $softwareId = $this->generateId('SOF');
    
    // $this->app->log->info('****new software **** -> '.$this->dumpRet($software));

    $this->db()->beginTransaction();

    $sqlSoftware = "INSERT INTO SOFTWARE(
                                      ID,
                                      LABEL,
                                      SERVICE_PACK,
                                      CURRENT_VERSION,
                                      NEXT_VERSION,
                                      TO_DO,
                                      WEB_ADDRESS,
                                      BRAND_ID,
                                      EDITION_DATE) 
                               VALUES(:id,
                                     :label,
                                     :service_pack,
                                     :current_version,
                                     :next_version,
                                     :to_do,
                                     :web_address,
                                     :brandId,
                                     CURRENT_TIMESTAMP)";

    $querySoftware = $this->db()->prepare($sqlSoftware);

    $result = $querySoftware->execute(array(                            
                            ':id'=>$softwareId,                            
                            ':label'=>$label,                            
                            ':service_pack'=>$service_pack,
                            ':current_version'=>$current_version,
                            ':next_version'=>$next_version,
                            ':to_do'=>$to_do,
                            ':web_address'=>$web_address,
                            ':brandId'=>$brand_id
                            ));
    
    // $this->app->log->info('****result **** -> '.$this->dumpRet($result));
 
      $return = $querySoftware->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($softwareId);

  }

  /***************************************
  * UPDATE SOFTWARE
  *
  * @return softwareId
  ***************************************/

  function updateSoftware($software){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****software **** -> '.$this->dumpRet($software));

    $label = $software->LABEL;

    //we check for undefined variables
    if (!isset($software->SERVICE_PACK)) {
      $software->SERVICE_PACK = '-';
    };
    if (!isset($software->WEB_ADDRESS)) {
      $software->WEB_ADDRESS = '-';
    };
    if (!isset($software->TO_DO)) {
      $software->TO_DO = '-';
    };
    if (!isset($software->BRAND_ID)) {
      $software->BRAND_ID = '-';
    };
    if (!isset($software->CURRENT_VERSION)) {
      $software->CURRENT_VERSION = '-';
    };
    if (!isset($software->NEXT_VERSION)) {
      $software->WEB_ADDRESS = '-';
    };

    $softwareId =  $software->ID;
    $brand_id =  $software->BRAND_ID;
    $service_pack =  $software->SERVICE_PACK;
    $current_version = $software->CURRENT_VERSION;
    $next_version = $software->NEXT_VERSION;
    $to_do = $software->TO_DO;    
    $web_address = $software->WEB_ADDRESS;

    $sqlSoftware = "UPDATE SOFTWARE
                    SET LABEL='$label',
                        SERVICE_PACK='$service_pack',
                        BRAND_ID='$brand_id',
                        CURRENT_VERSION='$current_version',
                        NEXT_VERSION='$next_version',
                        TO_DO='$to_do',
                        EDITION_DATE=CURRENT_TIMESTAMP,
                        WEB_ADDRESS='$web_address'
                    WHERE ID='$softwareId';";

    // $this->app->log->info('****sqlSoftware **** -> '.$this->dumpRet($sqlSoftware));
    $querySoftware = $this->db()->query($sqlSoftware);
    return($softwareId);

  }

/******************************************************************************************
*
* FUNCTION deleteSoftware : [param : $softwareId -> return ('deleted')
*
*******************************************************************************************/

  function deleteSoftware($softwareId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "UPDATE SOFTWARE
                    SET STATUS='DELETED'
                    WHERE ID='$softwareId';";
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

//we count all existing softwareId to predict the next id

    $sqlCount = "SELECT max(substring(software.id from 10 for 3)) as NEW_ID
                  FROM software
                  WHERE substring(software.id from 7 for 3) = '$type'
                  AND substring(software.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $softwareId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $softwareId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($softwareId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $softwareId.$strCrypt;
    return $data;
  }
}