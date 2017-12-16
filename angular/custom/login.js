/**
 * Cube - Bootstrap Admin Theme
 * Copyright 2014 Phoonio
 */

 
function loginCtrl($scope,$http,dataService) {
	//alert();
	$('#page-wrapper').removeClass('nav-small');
	
	$scope.loginSubmit = function(){
		
		// dataService.getData({'email':'abc@demo.com','password':'ajdfslkjlakjdsflkaj'},"http://localhost/gpulse/services/login").then(function(dataResponse) {
			// //$scope.list = dataResponse.data.result.users;
		// });
		
	}
	
}

angular
	.module('cubeWebApp')
	.controller('loginCtrl', loginCtrl)
	