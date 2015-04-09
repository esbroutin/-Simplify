<?php
 /**
 *  Class allowing to request thesaurus entries
 *  @creationdate 2015-03-27 
 **/ 
 
namespace PNORD\Model;

use PDO;
use Exception;
use PNORD\BaseSimplifyObject;

class ThesaurusDAO extends BaseSimplifyObject{
  
  function __construct($app){
    parent::__construct($app,true);
  }
  
  /**
  * Return CODE and DECODE Choices for a given thesaurus CAT
  * @param string $cat
  * @return array(CODE=>string, DECODE=>string)
  **/
  function getList($cat){   
        
    $query = "SELECT CODE,DECODE
              FROM THESAURUS
              WHERE CAT=".$this->db()->quote($cat)."
              ORDER BY DECODE";
    
    $result = $this->db()->query($query)->fetchAll(PDO::FETCH_ASSOC);
  
    // $this->app->log->info('thesaurus : '.$this->dumpRet($result));

    return $result;
  }

  /**
  * Return distinct list of CAT
  * @return array(CAT=>string, CODE_COUNT=>integer)
  **/
  function getDistinctCatList(){   
        
    $query = "SELECT DISTINCT CAT, COUNT(CODE) AS CODE_COUNT
              FROM THESAURUS
              GROUP BY CAT";
    // $this->app->log->info($query);
    $result = $this->db()->query($query);
    return $result->fetchAll(PDO::FETCH_ASSOC);
  }  
}