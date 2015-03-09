<?php
 /**
 *  Class to handle DB access for license
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class LicenseDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /***************************************
  * List License
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listLicense($search){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    $providerId ='';
    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (LICENSE.ID LIKE '%$search%' 
            OR LICENSE.LABEL LIKE '%".strtoupper($search)."' 
            OR LICENSE.LABEL LIKE '%".strtolower($search)."%'
            OR PROVIDER.LABEL LIKE '%".strtoupper($search)."' 
            OR PROVIDER.LABEL LIKE '%".strtolower($search)."%' 
            OR BRAND.LABEL LIKE '%".strtoupper($search)."' 
            OR BRAND.LABEL LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT license.id ,
                      license.label,
                      license.date_start,
                      license.date_end,
                      license.date_creation
              FROM license,provider,brand WHERE license.provider_id=provider.id AND license.brand_id=brand.id ".$conditionSql." ORDER BY DATE_CREATION DESC";

    	// $this->app->log->info('sql : '.$sql);

      $licenseData = $this->db()->query($sql);
      $licensesData = $licenseData->fetchAll(PDO::FETCH_ASSOC); 

      //we get the associated brand & provider 
      for ($t = 0; $t <= count($licensesData) -1; $t++) {

        $licenseId = $licensesData[$t]['ID'];

        $Sqlprovider = "SELECT provider.id as ID,
                               provider.label as LABEL,
                               provider.contact as CONTACT,
                               provider.web_address as WEB_ADDRESS,
                               provider.description as DESCRIPTION
                                 FROM provider,license 
                              WHERE license.provider_id = provider.id AND license.id = '$licenseId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $provider = $this->db()->query($Sqlprovider);
        $licensesData[$t]['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

        $Sqlprovider = "SELECT brand.id as ID,
                               brand.label as LABEL,
                               brand.web_address as WEB_ADDRESS
                                 FROM brand,license 
                              WHERE license.brand_id = brand.id AND license.id = '$licenseId'";
        // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
        $brand = $this->db()->query($Sqlprovider);
        $licensesData[$t]['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    }

      // $this->app->log->info('licensesData : '. $this->dumpRet($licensesData));
      return $licensesData ;          
  } 
     
  
  /**
  * Get License
  * @return array[{object}]
  **/
  function getLicense($licenseId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('licenseId : '.$licenseId);

    $sql = "SELECT license.id AS LICENSE_ID,
                      license.label as LICENSE_LABEL,
                      license.date_start,
                      license.date_end,
                      license.date_creation,
                      license.service_level,
                      license.description as LICENSE_DESCRIPTION,
                      license.web_address as WEB_ADDRESS,
                      license.serial
          
              FROM license
              WHERE license.id = '$licenseId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

  //we get the associated provider data  
    $Sqlprovider = "SELECT provider.id as ID,
                           provider.label as LABEL,
                           provider.contact as CONTACT,
                           provider.web_address as WEB_ADDRESS,
                           provider.description as DESCRIPTION,
                           provider.status as STATUS
                             FROM provider,license 
                          WHERE license.provider_id = provider.id AND license.id = '$licenseId'";
    // $this->app->log->info('Sqlprovider : '. $this->dumpRet($Sqlprovider));
    $provider = $this->db()->query($Sqlprovider);
    $result['PROVIDER'] = $provider->fetch(PDO::FETCH_ASSOC);

  //we get the associated brand data  
    $SqlBrand = "SELECT brand.id as ID,
                           brand.label as LABEL,
                           brand.web_address as WEB_ADDRESS
                             FROM brand,license 
                          WHERE license.brand_id = brand.id AND license.id = '$licenseId'";
    // $this->app->log->info('SqlBrand : '. $this->dumpRet($SqlBrand));
    $brand = $this->db()->query($SqlBrand);
    $result['BRAND'] = $brand->fetch(PDO::FETCH_ASSOC);

    //we check the remaining license time to generate alerts or not

      $licenseEndTimestamp = strtotime($result['DATE_END']);
      $nowTimestamp = time();
      $delta = $licenseEndTimestamp - $nowTimestamp;
      if ($delta < 2629743){
        // $this->app->log->info($licenseId.' expire in less than 1 month: '.$delta);
        $result['ALERT'] = 1;
      }
    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 
 

  /*************************************** 
  * Function to generate the License' Gantt
  * @return array[{object}]
  ***************************************/

  function getGantt(){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

//we get all the license id, label, and dates (start & end)
    $sqlLicenses = "SELECT id AS LICENSE_ID,
                      label as LICENSE_LABEL,
                      date_start,
                      date_end
          
              FROM license
              ORDER BY date_start"; 
    $result = $this->db()->query($sqlLicenses);
    $listLicenses = $result->fetchAll(PDO::FETCH_ASSOC);

    // $this->app->log->info('---listLicenses :  '.$this->dumpRet($listLicenses));
 

//start of our gantt data 
$ganttData = '[';
//we convert the result to match the gantt api input 
    for ($i = 0; $i <= count($listLicenses) -1; $i++) {

      //we assign a different color for each row
      if ($i % 2 == 0) {
        $color = 'red';
      }else{
        $color = 'green';        
      }

    $ganttData .= '{"name":"row'.($i+1).'","tasks":[{"name":"'.$listLicenses[$i]["LICENSE_LABEL"].'","color":"'.$color.'","from":"'.$listLicenses[$i]["DATE_START"].'","to":"'.$listLicenses[$i]["DATE_END"].'"}]}';
      
    if ($i < count($listLicenses) -1) {
      $ganttData .= ','; 
    };
    }

  $ganttData .= ']';
  // $this->app->log->info('--- $ganttData:  '.$this->dumpRet($ganttData));

  return ($ganttData);

  } 
  
  /***************************************
  * Add License
  * @return array[{object}]
  ***************************************/

  function addLicense($license){

    $this->app->log->notice(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****license **** -> '.$this->dumpRet($license));

    $label = $license->LABEL;
    $date_start = $license->DATE_START;
    $date_end = $license->DATE_END;

    //we check for undefined variables
    if (!isset($license->DESCRIPTION)) {
      $license->DESCRIPTION = '-';
    };
    if (!isset($license->WEB_ADDRESS)) {
      $license->WEB_ADDRESS = '-';
    };
    if (!isset($license->SERVICE_LEVEL)) {
      $license->SERVICE_LEVEL = '-';
    };
    if (!isset($license->BRAND_ID)) {
      $license->BRAND_ID = '-';
    };
    if (!isset($license->SERIAL)) {
      $license->SERIAL = '-';
    };
    $description = $license->DESCRIPTION;
    $brand_Id = $license->BRAND_ID;    
    $serial = $license->SERIAL;
    $provider_Id = $license->PROVIDER_ID;
    $web_address = $license->WEB_ADDRESS;
    $service_level = $license->SERVICE_LEVEL;

    //we generate an custom id
    $licenseId = $this->generateId('LIC');

    //we use transaction to avoid sql injection level 1 & bad character escaping
    $this->db()->beginTransaction();

    $sqlLicense = "INSERT INTO LICENSE(
                                      ID,
                                      LABEL,
                                      DATE_START,
                                      DATE_END,
                                      DESCRIPTION,
                                      BRAND_ID,
                                      SERIAL,
                                      PROVIDER_ID,
                                      WEB_ADDRESS,
                                      SERVICE_LEVEL,
                                      DATE_CREATION) 
                               VALUES(:id,
                                     :label,
                                     :date_start,
                                     :date_end,
                                     :description,
                                     :brand_id,
                                     :serial,
                                     :provider_Id,
                                     :web_address,
                                     :service_level,
                                     CURRENT_TIMESTAMP)";

    $queryLicense = $this->db()->prepare($sqlLicense);

    $result = $queryLicense->execute(array(                            
                            ':id'=>$licenseId,                            
                            ':label'=>$label,
                            ':date_start'=>$date_start,
                            ':date_end'=>$date_end,
                            ':description'=>$description,
                            ':brand_id'=>$brand_Id,
                            ':serial'=>$serial,
                            ':provider_Id'=>$provider_Id,
                            ':web_address'=>$web_address,
                            ':service_level'=>$service_level
                            ));
    
    // $this->app->log->info('****licenseId **** -> '.$this->dumpRet($licenseId));
      $return = $queryLicense->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($licenseId);

  }

  /***************************************
  * UPDATE LICENSE
  *
  * @return licenceId
  ***************************************/

  function updateLicense($license){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****license **** -> '.$this->dumpRet($license));

    $label = $license->LICENSE_LABEL;
    $date_start = $license->DATE_START;
    $date_end = $license->DATE_END;

    //we check for undefined variables
    if (!isset($license->LICENSE_DESCRIPTION)) {
      $license->DESCRIPTION = '-';
    };
    if (!isset($license->WEB_ADDRESS)) {
      $license->WEB_ADDRESS = '-';
    };
    if (!isset($license->SERVICE_LEVEL)) {
      $license->SERVICE_LEVEL = '-';
    };
    if (!isset($license->BRAND_ID)) {
      $license->LICENSE_BRAND_ID = '-';
    };
    if (!isset($license->SERIAL)) {
      $license->SERIAL = '-';
    };
    if (!isset($license->WEB_ADDRESS)) {
      $license->WEB_ADDRESS = '-';
    };

    $licenseId =  $license->LICENSE_ID;
    $description = $license->LICENSE_DESCRIPTION;
    $brand_Id = $license->BRAND_ID;    
    $serial = $license->SERIAL;
    $provider_Id = $license->PROVIDER_ID;
    $web_address = $license->WEB_ADDRESS;
    $service_level = $license->SERVICE_LEVEL;

    $sqlLicense = "UPDATE LICENSE
                  SET LABEL='$label',
                      DATE_START='$date_start',
                      DATE_END='$date_end',
                      DESCRIPTION='$description',
                      BRAND_ID='$brand_Id',
                      SERVICE_LEVEL='$service_level',
                      SERIAL='$serial',
                      PROVIDER_ID='$provider_Id',
                      WEB_ADDRESS='$web_address'
                  WHERE ID='$licenseId';";
    // $this->app->log->info('****sqlLicense **** -> '.$this->dumpRet($sqlLicense));
    $queryLicense = $this->db()->query($sqlLicense);
    return($licenseId);

  }

/******************************************************************************************
*
* FUNCTION deleteLicense : [param : $licenseId -> return ('deleted')
*
*******************************************************************************************/

  function deleteLicense($licenseId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "DELETE FROM license
                    WHERE id LIKE '$licenseId'";
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

//we count all existing licenseId to predict the next id

    $sqlCount = "SELECT max(substring(license.id from 10 for 3)) as NEW_ID
                  FROM license
                  WHERE substring(license.id from 7 for 3) = '$type'
                  AND substring(license.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $licenseId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $licenseId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($licenseId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $licenseId.$strCrypt;
    return $data;
  }
       
}
