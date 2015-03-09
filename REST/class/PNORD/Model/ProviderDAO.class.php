<?php
 /**
 *  Class to handle DB access for provider
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class ProviderDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Provider
  * @return array[{object}]
  **/
  function listProvider($search){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (PROVIDER.CONTACT LIKE '%".strtoupper($search)."' 
            OR PROVIDER.CONTACT LIKE '%".strtolower($search)."%' 
            OR PROVIDER.LABEL LIKE '%".strtoupper($search)."%' 
            OR PROVIDER.LABEL LIKE '%".strtolower($search)."%' 
            OR PROVIDER.DESCRIPTION LIKE '%".strtoupper($search)."%' 
            OR PROVIDER.DESCRIPTION LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT *
              FROM provider WHERE provider.status NOT IN ('DELETED') ".$conditionSql." ORDER BY EDITION_DATE DESC";
      // $this->app->log->info('sql : '.$sql);

      $providerData = $this->db()->query($sql);
      $providersData = $providerData->fetchAll(PDO::FETCH_ASSOC); 

      return $providersData ;           
  } 
  
  /**
  * Get provider
  * @return array[{object}]
  **/
  function getProvider($providerId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('provider ID : '.$providerId);

    $sql = "SELECT * FROM provider
              WHERE provider.id = '$providerId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 

  /**
  * Add Provider
  * @return array[{object}]
  **/
  function addProvider($provider){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('provider :' .$this->dumpRet($provider));

    $label = $provider->LABEL;

    //we check for undefined variables
    if (!isset($provider->CONTACT)) {
      $provider->CONTACT = '-';
    };
    if (!isset($provider->DESCRIPTION)) {
      $provider->DESCRIPTION = '-';
    };
    if (!isset($provider->WEB_ADDRESS)) {
      $provider->WEB_ADDRESS = '-';
    };
    $contact = $provider->CONTACT;
    $description = $provider->DESCRIPTION;
    $web_address = $provider->WEB_ADDRESS;

    //we generate an custom id
    $providerId = $this->generateId('PRO');
    
    // $this->app->log->info('****new provider **** -> '.$this->dumpRet($provider));

    $this->db()->beginTransaction();

    $sqlProvider = "INSERT INTO PROVIDER(
                                      ID,
                                      LABEL,
                                      CONTACT,
                                      DESCRIPTION,
                                      WEB_ADDRESS,
                                      EDITION_DATE) 
                               VALUES(:id,
                                     :label,
                                     :contact,
                                     :description,
                                     :web_address,
                                     CURRENT_TIMESTAMP)";

    $queryProvider = $this->db()->prepare($sqlProvider);

    $result = $queryProvider->execute(array(                            
                            ':id'=>$providerId,                            
                            ':label'=>$label,                            
                            ':contact'=>$contact,
                            ':description'=>$description,
                            ':web_address'=>$web_address
                            ));
    
    // $this->app->log->info('****result **** -> '.$this->dumpRet($result));
 
      $return = $queryProvider->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($providerId);

  }

  /***************************************
  * UPDATE PROVIDER
  *
  * @return providerId
  ***************************************/

  function updateProvider($provider){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****provider **** -> '.$this->dumpRet($provider));

    $label = $provider->LABEL;

    //we check for undefined variables
    if (!isset($provider->DESCRIPTION)) {
      $provider->DESCRIPTION = '-';
    };
    if (!isset($provider->WEB_ADDRESS)) {
      $provider->WEB_ADDRESS = '-';
    };
    if (!isset($provider->CONTACT)) {
      $provider->CONTACT = '-';
    };
    $providerId =  $provider->ID;
    $description = $provider->DESCRIPTION;
    $contact = $provider->CONTACT;    
    $web_address = $provider->WEB_ADDRESS;

    $sqlProvider = "UPDATE PROVIDER
                    SET LABEL='$label',
                        DESCRIPTION='$description',
                        CONTACT='$contact',
                        EDITION_DATE=CURRENT_TIMESTAMP,
                        WEB_ADDRESS='$web_address'
                    WHERE ID='$providerId';";

    // $this->app->log->info('****sqlProvider **** -> '.$this->dumpRet($sqlProvider));
    $queryProvider = $this->db()->query($sqlProvider);
    return($providerId);

  }

/******************************************************************************************
*
* FUNCTION deleteProvider : [param : $providerId -> return ('deleted')
*
*******************************************************************************************/

  function deleteProvider($providerId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "UPDATE PROVIDER
                    SET STATUS='DELETED'
                    WHERE ID='$providerId';";
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

//we count all existing providerId to predict the next id

    $sqlCount = "SELECT max(substring(provider.id from 10 for 3)) as NEW_ID
                  FROM provider
                  WHERE substring(provider.id from 7 for 3) = '$type'
                  AND substring(provider.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $providerId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $providerId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($providerId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $providerId.$strCrypt;
    return $data;
  }
}