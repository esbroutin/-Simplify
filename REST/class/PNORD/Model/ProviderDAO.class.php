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
  * List Maintenances
  * @return array[{object}]
  **/
  function listProvider(){
    $sql = "SELECT * FROM PROVIDER";
    $result = $this->db()->query($sql);
    return $result->fetchAll(PDO::FETCH_ASSOC);            
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
    $contact = $provider->CONTACT;
    $description = $provider->DESCRIPTION;
    
    // $this->app->log->info('****new provider **** -> '.$this->dumpRet($provider));

    $this->db()->beginTransaction();

    $sqlProvider = "INSERT INTO PROVIDER(
                                      LABEL,
                                      CONTACT,
                                      DESCRIPTION) 
                               VALUES(:label,
                                     :contact,
                                     :description)";

    $queryProvider = $this->db()->prepare($sqlProvider);

    $result = $queryProvider->execute(array(                            
                            ':label'=>$label,                            
                            ':contact'=>$contact,
                            ':description'=>$description
                            ));
    
    $this->app->log->info('****result **** -> '.$this->dumpRet($result));
 
      $return = $queryProvider->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return('ok_add_provider');

  }

}