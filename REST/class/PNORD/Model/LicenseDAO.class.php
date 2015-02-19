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
  
  /**
  * List License
  *parameter -> search string
  * @return array[{object}]
  **/
  function listLicense($search){

    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (LICENSE.ID LIKE '%$search%' 
            OR LICENSE.LABEL LIKE '%".strtoupper($search)."' 
            OR LICENSE.LABEL LIKE '%".strtolower($search)."%' 
            OR PROVIDER.LABEL LIKE '%".strtoupper($search)."%' 
            OR PROVIDER.LABEL LIKE '%".strtolower($search)."%' 
            OR LICENSE.BRAND LIKE '%".strtoupper($search)."%' 
            OR LICENSE.BRAND LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT license.id ,
                      license.label,
                      license.date_start,
                      license.date_end,
                      license.brand,
                      license.date_creation,
                      provider.label as PROVIDER_LABEL
              FROM license, provider 
              WHERE license.provider_id = provider.id ".$conditionSql." ORDER BY DATE_CREATION DESC";
    	// $this->app->log->info('sql : '.$sql);

      $licenseData = $this->db()->query($sql);
      $licensesData = $licenseData->fetchAll(PDO::FETCH_ASSOC); 

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
                      license.brand as LICENSE_BRAND,
                      license.description as LICENSE_DESCRIPTION,
                      license.web_address as WEB_ADDRESS,
                      license.serial,
                      provider.contact as PROVIDER_CONTACT,
                      provider.description as PROVIDER_DESCRIPTION,
                      provider.label as PROVIDER_LABEL
          
              FROM license, provider 
              WHERE license.provider_id = provider.id
              AND license.id = '$licenseId'";
    $result = $this->db()->query($sql);
    return $result->fetch(PDO::FETCH_ASSOC);            
  } 


  /**
  * Function to generate the License' Gantt
  * @return array[{object}]
  **/
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
  
  /**
  * Add License
  * @return array[{object}]
  **/
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
    if (!isset($license->BRAND)) {
      $license->BRAND = '-';
    };
    if (!isset($license->SERIAL)) {
      $license->SERIAL = '-';
    };
    $description = $license->DESCRIPTION;
    $brand = $license->BRAND;    
    $serial = $license->SERIAL;
    $provider_Id = $license->PROVIDER_ID;
    $web_address = $license->WEB_ADDRESS;

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
                                      brand,
                                      SERIAL,
                                      PROVIDER_ID,
                                      WEB_ADDRESS,
                                      DATE_CREATION) 
                               VALUES(:id,
                                     :label,
                                     :date_start,
                                     :date_end,
                                     :description,
                                     :brand,
                                     :serial,
                                     :provider_Id,
                                     :web_address,
                                     CURRENT_TIMESTAMP)";

    $queryLicense = $this->db()->prepare($sqlLicense);

    $result = $queryLicense->execute(array(                            
                            ':id'=>$licenseId,                            
                            ':label'=>$label,
                            ':date_start'=>$date_start,
                            ':date_end'=>$date_end,
                            ':description'=>$description,
                            ':brand'=>$brand,
                            ':serial'=>$serial,
                            ':provider_Id'=>$provider_Id,
                            ':web_address'=>$web_address
                            ));
    
    $this->app->log->info('****licenseId **** -> '.$this->dumpRet($licenseId));
      $licenseId = str_replace('"', "", $licenseId);
      $return = $queryLicense->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

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
* FUNCTION generateId : [param : $type -> return Id = DAY-MONTH-YEAR-Type-NewOrderId.Salt
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
