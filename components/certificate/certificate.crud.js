//certificate CRUD Controller
angular
  .module('simplify')
  .controller('CertificateCrudCtrl', ['$scope',
								'$state',
								'$stateParams',
								'$http',
								'certificateService',
								'$rootScope',
								'providerService',
								'$upload',
								'brandService',
								 function($scope, $state,$stateParams, $http, certificateService, $rootScope, providerService,$upload, brandService){

//we initialize our values
	$scope.formDone = 0;
	$scope.addButton = 0;
	$scope.uploading = 0;
	$scope.certificate = {};
	$scope.certificate.AUTO_SIGNED = true;
	$scope.fields = [];
	$scope.newCertificateId = 'XXXXX';
	//show list function
	$scope.showList = function(){
		$state.transitionTo('listCertificate', {reload: true});
	}

	//redirection to view created certificate
	$scope.view = function(){

		// console.log('$scope.newCertificateId : ' + $scope.newCertificateId);
		$state.transitionTo('viewCertificate',{recordId:$scope.newCertificateId});
	}

	// we load the provider list
	providerService.list(undefined).then(function(response){
		$scope.providers = response.data;
			});
	// we load the brand list
	brandService.list(undefined).then(function(response){
		$scope.brands = response.data;
			});

	$scope.addProvider = function () {

		//we add the new provider then reload the providers list
		providerService.add($scope.newProviderForm).then(function(response){
			//reloading & hide the provider form
			providerService.list(undefined).then(function(response){
				$scope.providers = response.data;
				$scope.newProvider = 0;
				$scope.newProviderForm = {};

			});
		});
	};

	$scope.addBrand = function () {

		//we add the new provider then reload the providers list
		brandService.add($scope.newBrandForm).then(function(response){
			//reloading & hide the provider form
			brandService.list(undefined).then(function(response){
				$scope.brands = response.data;
				$scope.newBrand = 0;
				$scope.newBrandForm = {};

			});
		});
	};
    $scope.upload = function (files) {
        if (files && files.length) {
				$scope.uploading = 1;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var id = $scope.newCertificateId+'_'+file.name;
                console.log('file', file);
                console.log('id', id);
                $upload.upload({
				    url: 'REST/certificate/upload', 
				    headers: {'Content-Type': file.type},
				    method: 'POST',
				    data: file,
				    file: file,
				    fileName: id
                }).progress(function (evt) {
                    $scope.progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
                    // console.log('progress: ' + $scope.progressPercentage + '% ' +
                    //             evt.config.file.name);
                }).success(function (data, status, headers, config) {
        		$scope.uploading = 0;
                    // console.log('file ' + config.file.name + 'uploaded. Response: ' +
                    //             JSON.stringify(data));
                });
            }
        }else{

        		$scope.uploading = 0;
        }
    };
	
/****************************
*
*WATCH FORM VALIDITY
*
****************************/

	$scope.$watchCollection('[certificate.DATE_START, certificate.DATE_END,certificate.AUTO_SIGNED, certificate.COMMON_NAME, certificate.PROVIDER, certificate.BRAND, certificate.ORGANIZATION, certificate.TOWN, certificate.COUNTRY, certificate.REGION, certificate.ORGANIZATION_UNIT]', function(newValues) {
		if ($scope.certificate.AUTO_SIGNED == true) {
			$scope.certificate.PROVIDER = '-';
			$scope.certificate.BRAND = '-';
		}
		if ($scope.certificate.COMMON_NAME == '') {
			$scope.certificate.COMMON_NAME = undefined;
		};

			var countDefined='';

			for (var i = newValues.length - 1; i >= 0; i--) {

				if(newValues[i] !=0 && newValues[i] !=undefined && newValues[i].length !=0 || newValues[i] ==false) {
					// console.log('newValues[i].length ',newValues[i].length);
					// console.log('newValues[i] ',newValues[i]);
					countDefined++;
				}
			};

			$scope.completeForm = ((countDefined) *100)/(newValues.length);
			$scope.completeForm = Math.round($scope.completeForm);
			$scope.remainingProgress = 100 - $scope.completeForm;

	});


	//redirection to addcertificate
	$scope.new = function(){   
		$scope.certificate = {};   
		$scope.formDone = 0;
	}

	//redirection to addcertificate
	$scope.changeSigned = function(){   
		if ($scope.certificate.AUTO_SIGNED == false) {
			$scope.certificate.PROVIDER = undefined;
			$scope.certificate.BRAND = undefined;

		}
	}

	//return to list when cancelling certificate creation
	$scope.confirmAdd = function(){
		if ($scope.certificate.AUTO_SIGNED != false) {
			$scope.certificate.PROVIDER_ID = '-'; 
			$scope.certificate.BRAND_ID = '-'; 
		}
			// console.log('certificate : ' , $scope.certificate);

		certificateService.add($scope.certificate).then(function(response){
			$scope.addButton = 0;
			$scope.formDone = 1;
			$scope.newCertificateId = response.data;

			//ugly temporary algorithm to remove double quotes because angularjs suddendly added stuff, bitch is crazy ...
			var str = '';
			str = $scope.newCertificateId;
			str = str.replace('"','');
			$scope.newCertificateId = str.replace('"','');
    		$scope.upload($scope.files);
			$rootScope.listAlerts ();
			// console.log('newCertificateId : ' + $scope.newCertificateId);
		});

	}
	
/**********************
* datePicker
**********************/

	$scope.today = function() {
    $scope.certificate.DATE_START = new Date();
  };

  $scope.clear = function () {
    $scope.certificate.DATE_START = null;
  };

  $scope.openStartDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedStartDate = true;
  };

  $scope.openEndDate = function($event) {
    $event.preventDefault();
    $event.stopPropagation();

    $scope.openedEndDate = true;
  };

  $scope.toggleMin = function() {
    $scope.minDateEnd = $scope.certificate.DATE_START;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.today();
  $scope.toggleMin();
}]);

