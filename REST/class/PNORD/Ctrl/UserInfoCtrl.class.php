<?php

namespace PNORD\Ctrl;

class UserInfoCtrl{
 
  function getUserInfo(){

    $userInfo = array(); 
    
    if(isset($_SESSION['userid'])){
      $userInfo['user'] = $_SESSION['userid'];
    }else{
      $userInfo = false;
    }

    return $userInfo;
    
  }
}