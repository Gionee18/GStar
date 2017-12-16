
function approvalProdCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	
	
	//$scope.searchKeyword = '';
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";	
	$scope.list = [];	
	$scope.statuslist = "";
	$scope.search="";
	$scope.status =
	{
	0:"Pending",
	1:"Approved",
	2:"Rejected",
	
	}
	
	$(document).ready(function () {
	$(function () {
		$("#productFrom").datepicker({
			changeMonth: true,
		changeYear: true,
			maxDate : "0",
			dateFormat : 'dd-mm-yy',
			 onClose: function( selectedDate ) {
			$( "#productTo" ).datepicker( "option", "minDate", selectedDate );
		  }
		});

	});
	$(function () {
		$("#productTo").datepicker({
			changeMonth: true,
		changeYear: true,
			dateFormat : 'dd-mm-yy',
			 minDate: $scope.startDate,
		 maxDate: "0",
		  onClose: function( selectedDate ) {
			$( "#productFrom" ).datepicker( "option", "maxDate", 'maxDate' );
		  }
		});

	});
	})
	$scope.getUpdatedData=function(data){
		$scope.list = data;
		$scope.totalItems = $scope.list.length;
	}
	$scope.productList = function(){
			var fd = new FormData();
		
		if( $scope.statuslist.length > 0)
		{
		 fd.append('approve_status', $scope.statuslist );
		}
		if( $scope.startDate != undefined)
		{
		 fd.append('start_date', $scope.startDate );
		}
		if( $scope.endDate != undefined)
		{
		 fd.append('end_date', $scope.endDate );
		}
		
		if($scope.search.length>0)
		{
		 fd.append('search_keyword', $scope.search );
		}
		
		$http.post(baseUrl+"product/approved/list",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function(response) {
			$scope.list = response.data;
			////console.log($scope.list);
			
			$scope.totalItems = $scope.list.length;
		});
	}
	$scope.productList();
	
	
	
	
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.list.length / $scope.itemsPerPage);
	};		
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};
	$scope.exportData = function () {
			//alert();
			var blob = new Blob([document.getElementById('exportable').innerHTML], {
				type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
			});
			saveAs(blob, "Report.xls");
		};
	
	
}

function compareProdCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$routeParams, $location) {
	$scope.getId = $routeParams.pid;
	$scope.orgId = $routeParams.id;
	$scope.requestUserId = $routeParams.request_userid;
	$scope.compareData = function(){
		
		
		$http.post(baseUrl +"product/approved/admin",{'id':$scope.orgId,'product_id':$scope.getId,'request_id':$scope.requestUserId})
			.success(function(response){
			if(response.status == 'success')
			{
			$scope.updatedProduct = response.data.newdata;
			$scope.currentProduct = response.data.olddata;
			}
			})
			

	
	}
	$scope.compareData();

	$scope.approvedProdFunc = function($approve_status){
		$http.post(baseUrl + "product/approved/byAdmin",{"product_id":$scope.getId, "request_id":$scope.requestUserId,"id":$scope.orgId,'approve_status':$approve_status})
		.success(function(response) {
			if (response.status == "success") {
				toaster.pop('success',response.msg);
				$location.path("/approval/products");
			}else{
				toaster.pop('error',response.result.msg);

			}
		
		});
	}

}

function editUserProfileCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$routeParams, $location) {
	
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";	
	$scope.list = [];	
	$scope.statuslist="";
	$scope.search="";
	$scope.getUpdatedData=function(data){
		$scope.list = data;
		$scope.totalItems = $scope.list.length;
	}
	$scope.status =
	{
	0:"Pending",
	1:"Approved",
	2:"Rejected",
	
	}
	$(document).ready(function () {
	$(function () {
		$("#profileFrom").datepicker({
			changeMonth: true,
		changeYear: true,
			maxDate : "0",
			dateFormat : 'dd-mm-yy',
			 onClose: function( selectedDate ) {
			$( "#profileTo" ).datepicker( "option", "minDate", selectedDate );
		  }
		});

	});
	$(function () {
		$("#profileTo").datepicker({
			changeMonth: true,
		changeYear: true,
			dateFormat : 'dd-mm-yy',
			 minDate: $scope.startDate,
		 maxDate: "0",
		  onClose: function( selectedDate ) {
			$( "#profileFrom" ).datepicker( "option", "maxDate", 'maxDate' );
		  }
		});

	});
	})
	$scope.userProfileList = function(){
		
		var fd = new FormData();
		
		if( $scope.statuslist.length > 0)
		{
		 fd.append('approve_status', $scope.statuslist );
		}
		if($scope.search.length>0)
		{
		 fd.append('search_keyword', $scope.search );
		}
		if( $scope.startDate != undefined)
		{
		 fd.append('start_date', $scope.startDate );
		}
		if( $scope.endDate != undefined)
		{
		 fd.append('end_date', $scope.endDate );
		}
		$http.post(baseUrl+"user/updateprofile/list",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function(response) {
			$scope.list = response.data;
			////console.log($scope.list);
			
			$scope.totalItems = $scope.list.length;
		});
	}
	$scope.userProfileList();
	
	

	
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.list.length / $scope.itemsPerPage);
	};		
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};
	$scope.exportData = function () {
			//alert();
			var blob = new Blob([document.getElementById('exportable').innerHTML], {
				type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
			});
			saveAs(blob, "Report.xls");
		};
	
}
function compareEditUserCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$routeParams, $location) {
	
	$scope.userProfileList = function(){
		$scope.id=$routeParams.id;
		$scope.uid=$routeParams.uid;
		// dataService.getData($scope.data,baseUrl + "user/updateprofile/"+$scope.id).then(function(dataResponse) {
		$http.get(baseUrl+ "user/updateprofile/"+$scope.id+"/"+$scope.uid)

		.success(function(response){
			if(response.status == 'success'){

			$scope.list = response.data;
			$scope.newData = response.data.newdata;
			$scope.oldData = response.data.olddata;
						
			////console.log($scope.list);
			////console.log($scope.newData);
			////console.log($scope.oldData);
			}else{
				toaster.pop('error',response.result.msg);
			}

		});
	}
	$scope.userProfileList();
	$scope.approveUser = function(aid){
		$http.post(baseUrl+ "user/updateprofile/approved",{'user_id':$scope.uid,'approved_status':aid,'id':$scope.id})
		.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop('success',response.msg);
				$location.path('/approval/edit-users-profile');
				
			}
			else{
			
				toaster.pop('error',response.msg);
			}
		
		})
	}
}

function userActivationCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$routeParams, $location) {
	$scope.statuslist="";
	$scope.search="";
	$scope.status =
	{
	0:"Pending",
	1:"Approved",
	2:"Rejected",
	
	}
	$(document).ready(function () {
	$(function () {
		$("#userFrom").datepicker({
			changeMonth: true,
		changeYear: true,
			maxDate : "0",
			dateFormat : 'dd-mm-yy',
			 onClose: function( selectedDate ) {
			$( "#userTo" ).datepicker( "option", "minDate", selectedDate );
		  }
		});

	});
	$(function () {
		$("#userTo").datepicker({
			changeMonth: true,
		changeYear: true,
			dateFormat : 'dd-mm-yy',
			 minDate: $scope.startDate,
		 maxDate: "0",
		  onClose: function( selectedDate ) {
			$( "#userFrom" ).datepicker( "option", "maxDate", 'maxDate' );
		  }
		});

	});
	})
		$scope.userActivationFunct = function()
		{
		
			var fd = new FormData();
		
		if( $scope.statuslist.length > 0)
		{
		 fd.append('approve_status', $scope.statuslist );
		}
		if($scope.search.length>0)
		{
		 fd.append('search_keyword', $scope.search );
		}
		if( $scope.startDate != undefined)
		{
		 fd.append('start_date', $scope.startDate );
		}
		if( $scope.endDate != undefined)
		{
		 fd.append('end_date', $scope.endDate );
		}
		
		$http.post(baseUrl+"user/activation/request/list",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
			
			.success(function(response)
			{
				if(response.status == 'success')
				{
					$scope.list = response.data;
					//////console.log($scope.list);
					$scope.totalItems = $scope.list.length;
				}
			
			})
		}
		$scope.userActivationFunct();
		
		$scope.activationApproved = function(id,st){
			$http.post(baseUrl+"user/activate",{'user_id':id,'activation_status':st})
				.success(function(response){
					if(response.status == 'success')
					{
						$scope.userActivationFunct();
						toaster.pop('success',response.result.msg);
					}
				})
		}
		$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.list.length / $scope.itemsPerPage);
	};	
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.exportData = function () {
			//alert();
			var blob = new Blob([document.getElementById('exportable').innerHTML], {
				type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
			});
			saveAs(blob, "Report.xls");
		};
}

angular
	.module('cubeWebApp')
	.controller('approvalProdCtrl', approvalProdCtrl)
	.controller('compareProdCtrl', compareProdCtrl)
	.controller('editUserProfileCtrl', editUserProfileCtrl)
	.controller('compareEditUserCtrl', compareEditUserCtrl)
	.controller('userActivationCtrl', userActivationCtrl)

