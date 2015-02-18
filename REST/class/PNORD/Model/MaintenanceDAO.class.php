<?php
 /**
 *  Class to handle DB access for maintenance
 *  @creationdate 2015-02-06  
 **/ 
 
namespace PNORD\Model;

use PDO;

use PNORD\BaseSimplifyObject;

class MaintenanceDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true); 
  }
  
  /**
  * List Maintenances
  * @return array[{object}]
  **/
  function listMaintenance(){
    $sql = "SELECT * FROM MAINTENANCE";
    $result = $this->db()->query($sql);
    return $result->fetchAll(PDO::FETCH_ASSOC);            
  } 
     
}
