<?
include("config.inc.php");
global $gTblConfig;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" ng-app="simplify">
<head> 
    <title>Province Nord - !Simplify</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">


<!--  css & design-->
  <!-- <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css"> -->
  <link rel="stylesheet" href="lib/font-awesome-4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="css/simplify.css">
  <!-- <link rel="stylesheet" href="css/custom_darkly_bootstrap.css"> -->
  <link rel="stylesheet" href="css/custom_slate_bootstrap.css">
  <!-- <link rel="stylesheet" href="css/custom_spacelab_bootstrap.min.css"> -->
  <!-- <link rel="stylesheet" href="css/custom_cerulean_bootstrap.css"> -->
  <link rel="stylesheet" href="bower_components/angular-motion/dist/angular-motion.min.css">
  <link rel="stylesheet" href="bower_components/angular-gantt/dist/angular-gantt.css">
  <link rel="stylesheet" href="bower_components/angular-gantt/dist/angular-gantt-plugins.css">
  <link rel="stylesheet" href="bower_components/angular-ui-select/dist/select.css">
  <link rel="stylesheet" href="bower_components/angular-loading-bar/build/loading-bar.css">
  <link rel="stylesheet" href="css/selectize.default.css">

<!--  scripts-->
  <script type="text/javascript" src="bower_components/angularjs/angular.js"></script>
  <script type="text/javascript" src="bower_components/jquery/dist/jquery.js"></script>
  <script type="text/javascript" src="bower_components/angular-resource/angular-resource.min.js"></script>
  <script type="text/javascript" src="bower_components/angular-ui-router/release/angular-ui-router.js"></script>
  <script type="text/javascript" src="lib/node_modules/angular-bootstrap/dist/ui-bootstrap-tpls.js"></script> 
  <script type="text/javascript" src="bower_components/angular-sanitize/angular-sanitize.js"></script>
  <script type="text/javascript" src="bower_components/angular-ui-select/dist/select.js"></script>
  <script type="text/javascript" src="bower_components/angular-animate/angular-animate.min.js"></script>
  <script type="text/javascript" src="bower_components/angular-loading-bar/build/loading-bar.js"></script>
  <script type="text/javascript" src="bower_components/moment/moment.js"></script>
  <script type="text/javascript" src="bower_components/angular-moment/angular-moment.js"></script>
  <script type="text/javascript" src="bower_components/angular-gantt/dist/angular-gantt.js"></script>
  <script type="text/javascript" src="bower_components/angular-gantt/dist/angular-gantt-plugins.js"></script>
  
<!-- Application -->
  <script src="js/simplify.js"></script>
<?
  //Inclusion automatique des fichiers Javascript

  //Services, Directives, Filtres
  $directory = $gTblConfig['WWW_PATH'] . "js/*/";
  $jsFiles = glob($directory . "*.js");
  foreach($jsFiles as $jsFile){
    if(!strpos($jsFile,"_")){ //exclude _mock and _tests files
      echo "<script src='".substr($jsFile,strlen($gTblConfig['WWW_PATH']))."'></script>\n";
    }
  }

  //Components
  $directory = $gTblConfig['WWW_PATH'] . "components/*/";
  $jsFiles = glob($directory . "*.js");
  foreach($jsFiles as $jsFile){
    if(!strpos($jsFile,"_")){ //exclude _mock and _tests files
      echo "<script src='".substr($jsFile,strlen($gTblConfig['WWW_PATH']))."'></script>\n";
    }
  }

  //templates
  $directory = $gTblConfig['WWW_PATH'] . "templates/";
  $jsFiles = glob($directory . "*.js");
  foreach($jsFiles as $jsFile){
    if(!strpos($jsFile,"_")){ //exclude _mock and _tests files
      echo "<script src='".substr($jsFile,strlen($gTblConfig['WWW_PATH']))."'></script>\n";
    }
  }

?>
  
<!--route et configuration-->
  <script src="js/routes.js"></script>
  <script src="js/config.js"></script>

</head> 
<body> 
  <div ng-app="simplify" >
  <!-- NAVBAR -->
      <div class=" col-md-12" ng-include="'templates/header.html'"></div>
      <div class=" col-md-2" ng-include="'templates/nav.html'"></div>
  <!-- UI-VIEW -->
      <div class=" col-md-9 reveal-animation" ui-view></div>
      <!-- <footer class="col-xs-12 col-md-12 sticky-footer" ng-include="'templates/footer.html'"></footer> -->
  </div>
</body>
</html>   
   