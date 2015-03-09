<?php
 /**
 *  Class to handle DB access for brand
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class BrandDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Brand
  * @return array[{object}]
  **/
  function listBrand($search){

    //if no search string, we get all 
    if ($search == 'undefined' || $search== ''){
      $conditionSql = "";
    }else{
          $conditionSql = "AND (BRAND.LABEL LIKE '%".strtoupper($search)."%' 
            OR BRAND.LABEL LIKE '%".strtolower($search)."%')";
    }

      $sql = "SELECT *
              FROM brand WHERE brand.status NOT IN ('DELETED') ".$conditionSql." ORDER BY EDITION_DATE DESC";
      // $this->app->log->info('sql : '.$sql);

      $brandData = $this->db()->query($sql);
      $brandsData = $brandData->fetchAll(PDO::FETCH_ASSOC); 

      return $brandsData ;           
  } 
  
  /**
  * Get brand
  * @return array[{object}]
  **/
  function getBrand($brandId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('brand ID : '.$brandId);

    $sql = "SELECT * FROM brand
              WHERE brand.id = '$brandId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);

    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 

  /**
  * Add Brand
  * @return array[{object}]
  **/
  function addBrand($brand){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('brand :' .$this->dumpRet($brand));

    $label = $brand->LABEL;

    //we check for undefined variables
    if (!isset($brand->WEB_ADDRESS)) {
      $brand->WEB_ADDRESS = '-';
    };
    $web_address = $brand->WEB_ADDRESS;

    //we generate an custom id
    $brandId = $this->generateId('BRA');
    
    // $this->app->log->info('****new brand **** -> '.$this->dumpRet($brand));

    $this->db()->beginTransaction();

    $sqlBrand = "INSERT INTO BRAND(
                                      ID,
                                      LABEL,
                                      WEB_ADDRESS,
                                      EDITION_DATE) 
                               VALUES(:id,
                                     :label,
                                     :web_address,
                                     CURRENT_TIMESTAMP)";

    $queryBrand = $this->db()->prepare($sqlBrand);

    $result = $queryBrand->execute(array(                            
                            ':id'=>$brandId,                            
                            ':label'=>$label,
                            ':web_address'=>$web_address
                            ));
    
    $this->app->log->info('****result **** -> '.$this->dumpRet($result));
 
      $return = $queryBrand->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($brandId);

  }

  /***************************************
  * UPDATE PROVIDER
  *
  * @return brandId
  ***************************************/

  function updateBrand($brand){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****brand **** -> '.$this->dumpRet($brand));

    $label = $brand->LABEL;

    //we check for undefined variables
    if (!isset($brand->DESCRIPTION)) {
      $brand->DESCRIPTION = '-';
    };
    if (!isset($brand->WEB_ADDRESS)) {
      $brand->WEB_ADDRESS = '-';
    };
    if (!isset($brand->CONTACT)) {
      $brand->CONTACT = '-';
    };
    if (!isset($brand->BRAND)) {
      $brand->BRAND = '-';
    };
    if (!isset($brand->SERIAL)) {
      $brand->SERIAL = '-';
    };
    if (!isset($brand->WEB_ADDRESS)) {
      $brand->WEB_ADDRESS = '-';
    };

    $brandId =  $brand->ID;
    $description = $brand->DESCRIPTION;
    $contact = $brand->CONTACT;    
    $web_address = $brand->WEB_ADDRESS;

    $sqlBrand = "UPDATE BRAND
                    SET LABEL='$label',
                        EDITION_DATE=CURRENT_TIMESTAMP,
                        WEB_ADDRESS='$web_address'
                    WHERE ID='$brandId';";

    // $this->app->log->info('****sqlBrand **** -> '.$this->dumpRet($sqlBrand));
    $queryBrand = $this->db()->query($sqlBrand);
    return($brandId);

  }

/******************************************************************************************
*
* FUNCTION deleteBrand : [param : $brandId -> return ('deleted')
*
*We dont really delete the brand but we assign a new status to it, so we avoid orphan entries (like licenses without brand ...)
*Changing status makes it unavailable to selection during crud or edit process.
*
*******************************************************************************************/

  function deleteBrand($brandId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "UPDATE BRAND
                    SET STATUS='DELETED'
                    WHERE ID='$brandId';";
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

//we count all existing brandId to predict the next id

    $sqlCount = "SELECT max(substring(brand.id from 10 for 3)) as NEW_ID
                  FROM brand
                  WHERE substring(brand.id from 7 for 3) = '$type'
                  AND substring(brand.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $brandId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $brandId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($brandId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $brandId.$strCrypt;
    return $data;
  }
}