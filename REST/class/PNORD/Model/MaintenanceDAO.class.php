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
  
  /***************************************
  * List Maintenance
  *parameter -> search string
  * @return array[{object}]
  ***************************************/

  function listMaintenance($data){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

      $sql = "SELECT *
              FROM maintenance_entry WHERE LINKED_ID='$data' ORDER BY DATE DESC";

      // $this->app->log->info('sql : '.$sql);

      $maintenanceData = $this->db()->query($sql);
      $maintenancesData = $maintenanceData->fetchAll(PDO::FETCH_ASSOC); 
      // $this->app->log->info('maintenancesData : '. $this->dumpRet($maintenancesData));
      return $maintenancesData ;          
  } 
     
  
  /**
  * Get Maintenance
  * @return array[{object}]
  **/
  function getMaintenance($maintenanceId){
  
    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    // $this->app->log->info('maintenanceId : '.$maintenanceId);

    $sql = "SELECT *          
              FROM maintenance_entry
              WHERE maintenance_entry.id = '$maintenanceId'";
    $result = $this->db()->query($sql)->fetch(PDO::FETCH_ASSOC);
    // $this->app->log->info('result : '. $this->dumpRet($result));
    return $result;            
  } 
 
  /***************************************
  * Add Maintenance
  * @return array[{object}]
  ***************************************/

  function addMaintenance($maintenance){

    $this->app->log->notice(__CLASS__ . '::' . __METHOD__);
    
    // $this->app->log->info('****maintenance **** -> '.$this->dumpRet($maintenance));

    //we check for undefined variables
    if (!isset($maintenance->ISSUES)) {
      $maintenance->MAINTENANCE_AUTHORITY = '-';
    };
    if (!isset($maintenance->DESCRIPTION)) {
      $maintenance->COMMENTS = '-';
    }

    //we generate an custom id
    $maintenanceId = $this->generateId('MAI');

    //we use transaction to avoid sql injection level 1 & bad character escaping
    $this->db()->beginTransaction();

    $sqlMaintenance = "INSERT INTO MAINTENANCE(
                                      ID,
                                      DESCRIPTION,
                                      ISSUES,
                                      DATE,
                                      SOFTWARE_ID) 
                               VALUES(:id,
                                     :description,
                                     :issues,
                                     :date,
                                     :software_id)";

    $queryMaintenance = $this->db()->prepare($sqlMaintenance);

    $result = $queryMaintenance->execute(array(                            
                            ':id'=>$maintenanceId,                            
                            ':description'=>$maintenance->DESCRIPTION,
                            ':issues'=>$maintenance->ISSUES,
                            ':date'=>$maintenance->DATE,
                            ':software_id'=>$maintenance->SOFTWARE_ID
                            ));

      $return = $queryMaintenance->fetch(PDO::FETCH_ASSOC);
      $this->db()->commit(); // commit global pour éviter les Entrées orphelines ou incomplète en cas d'erreur

      return($maintenanceId);

  }

  /***************************************
  * UPDATE MAINTENANCE
  *
  * @return licenceId
  ***************************************/

  function updateMaintenance($maintenance){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);
    
    $maintenanceId =  $maintenance->ID;

    $sqlMaintenance = "UPDATE MAINTENANCE
                  SET DESCRIPTION='".str_replace("'","",$maintenance->DESCRIPTION)."',
                      DATE='$maintenance->DATE',
                      ISSUES='".str_replace("'","",$maintenance->ISSUES)."',
                      SOFTWARE_ID='".str_replace("'","",$maintenance->SOFTWARE_ID)."'
                  WHERE ID LIKE '$maintenanceId';";
    $this->app->log->info('****sqlMaintenance **** -> '.$this->dumpRet($sqlMaintenance));
    $queryMaintenance = $this->db()->query($sqlMaintenance);
    return($maintenanceId);

  }

/******************************************************************************************
*
* FUNCTION deleteMaintenance : [param : $maintenanceId -> return ('deleted')
*
*******************************************************************************************/

  function deleteMaintenance($maintenanceId){

    $this->app->log->info(__CLASS__ . '::' . __METHOD__);

    $sql = "DELETE FROM maintenance
                    WHERE id LIKE '$maintenanceId'";
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

//we count all existing maintenanceId to predict the next id

    $sqlCount = "SELECT max(substring(maintenance.id from 10 for 3)) as NEW_ID
                  FROM maintenance
                  WHERE substring(maintenance.id from 7 for 3) = '$type'
                  AND substring(maintenance.id from 1 for 6) = '".date('ymd')."'";


    $resultCount = $this->db()->query($sqlCount)->fetch(PDO::FETCH_ASSOC);
    $resultCount = $resultCount['NEW_ID'];

    $maintenanceId ="";
    $strCrypt ="";
    $newOrderInc = $resultCount + 1;
    if ($newOrderInc < 100){
      $newOrderInc = "0".$newOrderInc;
    }
    if ($newOrderInc < 10){
      $newOrderInc = "0".$newOrderInc;
    }

    $maintenanceId = date('ymd').$type.$newOrderInc;
    $salt = '$2a$07$MantaCaledoniavahinenoumeaTiare$';
    $strCrypt = crypt($maintenanceId,$salt);
    $strCrypt = substr($strCrypt,-2);
    $strCrypt = str_replace('/', 'A', $strCrypt);
    $strCrypt = str_replace('.', 'X', $strCrypt);
    $strCrypt = str_replace('\\', 'B', $strCrypt);
    $strCrypt = mb_strtoupper($strCrypt);

    $data = $maintenanceId.$strCrypt;
    return $data;
  }
       
}
