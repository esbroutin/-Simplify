<?php
 /**
 *  Class to handle DB access for enterprises
 *  @creationdate 2015-02-20  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class EnterpriseDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Enterprise
  * @return array[{object}]
  **/
  function listEnterprise(){
    $sql = "SELECT * FROM ENTERPRISE";
    $result = $this->db()->query($sql);
    return $result->fetchAll(PDO::FETCH_ASSOC);            
  } 
     
}
