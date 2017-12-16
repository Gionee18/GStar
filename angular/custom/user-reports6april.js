
function userlistingCtrl($scope, $http, DTOptionsBuilder, dataService, toaster, arrayPushService) {

	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.search = "";
	$scope.list = [];
	$scope.orderList = [];
	$scope.realArray = [];
	$scope.filterStatus = [];
	$scope.selectCategory = "";
	$scope.startDate = "";
	$scope.endDate = "";
	var roleArr = [];
	var statusArr = [];
	var managerArr = [];
	$scope.reportManagerArr =[];
	var reportManager = {
		'name':'',
		'id':0

	}
	$scope.dataList = {
		'role' : roleArr,		
		'status' : statusArr,	
		'supervisor_id':managerArr,		
		'start_date' : $scope.startDate,
		'end_date' : $scope.endDate
	}
	$(document).ready(function () {
	$(function () {
		$("#from").datepicker({
			changeMonth: true,
		changeYear: true,
			maxDate : "0",
			dateFormat : 'dd-mm-yy',
			 onClose: function( selectedDate ) {
			$( "#to" ).datepicker( "option", "minDate", selectedDate );
		  }
		});

	});
	$(function () {
		$("#to").datepicker({
			changeMonth: true,
		changeYear: true,
			dateFormat : 'dd-mm-yy',
			 minDate: $scope.startDate,
		 maxDate: "0",
		  onClose: function( selectedDate ) {
			$( "#from" ).datepicker( "option", "maxDate", 'maxDate' );
		  }
		});

	});
	})
	$scope.status = {
		'1' : "Active",
		'0' : "Inactive",

	}
	$scope.role = {
		'10' : "Admin",
		'20' : "Trainer",
		'30' : "Supervisor",
		'40' : "Learner",
	}
	$http.get(baseUrl + "user/supervisorsList")
	.success(function (response) {
		if (response.status == 'success') {
			realData = response.data;
			$scope.reportManagerArr =[];
			for(var i=0;i<realData.length;i++)
			{	
				reportManager={};
				reportManager.name = realData[i].first_name+" "+realData[i].last_name;
				reportManager.id = realData[i].id;
				$scope.reportManagerArr.push(reportManager);	
			}
			console.log($scope.reportManagerArr);
		}
	}).error(function () {
		toaster.pop('error',response.result.msg);
	})
	$(document).ready(function () {
	setTimeout(function(){
	$('#roleAll').on('click',function () {
			$('.allroleList1').prop('checked', this.checked);
			roleArr=[];
			for(var i in $scope.role)
			{
				roleArr.push(i);
			}
			$scope.applyFunction();
		});

		$('.allroleList1').on('change',function () {
			var check = ($('.allroleList1').filter(":checked").length == $('.allroleList1').length);			
			$('#roleAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeRole = function () {
		
		roleArr = [];
		for (var i=0;i<Object.keys($scope.role).length;i++) {
		console.log($(".allroleList1").eq(i).prop('checked'))
		if($(".allroleList1").eq(i).prop('checked')) {		
				
				roleArr.push(Object.keys($scope.role)[i])
				
			}
		}	
		$scope.applyFunction();

	}
	$(document).ready(function () {
	setTimeout(function(){
	$('#statusAll').on('click',function () {
			$('.allstatusList').prop('checked', this.checked);
			statusArr=[];
			for(var i in $scope.status)
			{
				statusArr.push(i);
			}
			$scope.applyFunction();
		});

		$('.allstatusList').on('change',function () {
			var check = ($('.allstatusList').filter(":checked").length == $('.allstatusList').length);			
			$('#statusAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeStatus = function () {
		
		statusArr = [];
		for (var i=0;i<Object.keys($scope.status).length;i++) {
		console.log($(".allstatusList").eq(i).prop('checked'))
		if($(".allstatusList").eq(i).prop('checked')) {		
				statusArr.push(Object.keys($scope.status)[i])
				
			}
		}	

		$scope.applyFunction();
	}

	$(document).ready(function () {
	setTimeout(function(){
	$('#managerAll').on('click',function () {
			$('.allmanagerList').prop('checked', this.checked);
			managerArr=[];
			for(var i in $scope.reportManagerArr)
			{
				managerArr.push($scope.reportManagerArr[i].id);
			}
			$scope.applyFunction();
		});

		$('.allmanagerList').on('change',function () {
			var check = ($('.allmanagerList').filter(":checked").length == $('.allmanagerList').length);			
			$('#managerAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeManager = function () {
		
		managerArr = [];
		for (var i=0;i<$scope.reportManagerArr.length;i++) {
		console.log($(".allmanagerList").eq(i).prop('checked'))
		if($(".allmanagerList").eq(i).prop('checked')) {		
				managerArr.push($scope.reportManagerArr[i].id)
				
			}
		}	

		$scope.applyFunction();
	}

	$scope.applyFunction = function () {

		$scope.dataList = {
			'role' : roleArr,		
			'status' : statusArr,			
			'supervisor_id':managerArr,			
			'start_date' : $scope.startDate,
			'end_date' : $scope.endDate
		}
		console.log($scope.dataList);
	}

	$scope.resetAll = function () {
		
		
		roleArr = [];
		statusArr = [];	
		managerArr = [];
		$scope.dataList.role=[];
		$scope.dataList.supervisor_id=[];
		$scope.dataList.status =[];
		$scope.dataList.start_date=[];
		$scope.dataList.end_date=[];
	
		$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		$(".allroleList1").prop('checked', false);	
		$(".allstatusList").prop('checked', false);
		$(".allmanagerList").prop('checked', false);
		$("#managerAll").prop('checked', false);	
		$("#statusAll").prop('checked', false);
		$("#roleAll").prop('checked', false);
		$scope.startDate = "";
		$scope.endDate = "";
		$scope.initData();
	}
	


	$scope.initData = function () {
	var pagination ; 
		$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		
		
		var fd = new FormData();
		if ($scope.search.length > 0) {
			fd.append('search_keyword', $scope.search);
			 pagination = NaN;
		}
		if ($scope.dataList.role.length > 0) {
			fd.append('role', $scope.dataList.role);
			 pagination = NaN;
		}		
		if ($scope.dataList.status.length > 0) {
			fd.append('status', $scope.dataList.status);
			 pagination = NaN;
		}		
		if ($scope.dataList.supervisor_id.length > 0) {
			fd.append('supervisor_id', $scope.dataList.supervisor_id);
			 pagination = NaN;
		}
		if ($scope.dataList.start_date != undefined) {
			fd.append('start_date', $scope.dataList.start_date);
			 pagination = NaN;
		}
		if ($scope.dataList.end_date != undefined) {
			fd.append('end_date', $scope.dataList.end_date);
			 pagination = NaN;
		}
		if($scope.search.length == 0 && $scope.dataList.role.length == 0  &&  $scope.dataList.supervisor_id == 0 && $scope.dataList.status == 0 &&   $scope.dataList.start_date == undefined && $scope.dataList.end_date == undefined)
		{
		pagination = Math.ceil(Number((($scope.currentPage * $scope.itemsPerPage) / 100) + 1));
		}
		$http.post(baseUrl + "report/activeinactive/user/list?page_no=" +pagination,fd,  {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})
		.success(function (response) {
			//$scope.list = arrayPushService.arrayPush(response.data, $scope.list);
			if(isNaN(pagination))
			{
				$scope.list = response.data;
			}
			else
			{
			$scope.list = arrayPushService.arrayPush(response.data, $scope.list);
			}
			//$scope.list = response.data;
			realData = $scope.list;
			
			$scope.path = "";

			$scope.totalItems = $scope.list.length;
			$scope.sort($scope.sortKey);
		});
	}
	$scope.initData();

	$scope.exportData = function () {
		//alert();
		var blob = new Blob([document.getElementById('exportable').innerHTML], {
				type : "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
			});
		saveAs(blob, "Report.xls");
	};

	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
	$scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.list.length / $scope.itemsPerPage);
	};
	$scope.sort = function (keyname) {
		$scope.sortKey = keyname;
		$scope.reverse = !$scope.reverse;

	}
	$scope.pageChanged = function () {
		if ($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage)) {
			$scope.initData();
		}
	};
	$(document).ready(function () {
		$(".click_me").click(function () {
			$(".dis_filt").toggle();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();

		});

	});
	$(document).ready(function () {
		$(".click_me1").click(function () {

			$(".dis_filt1").toggle();
			$(".dis_filt").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".click_me2").click(function () {

			$(".dis_filt2").toggle();
			$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".dis1").click(function () {

			$(".dis_filt2").hide();
			$(".dis_filt1").hide();
			$(".dis_filt").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});

}

function auditTrailCtrl($scope, $http, DTOptionsBuilder, dataService, toaster, arrayPushService, $rootScope) {

	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	var ndArr = [];
	var roleArr = [];
	var statusArr = [];
	var stateArr = [];
	var managerArr = [];	
	$scope.ndlist = [];
	$scope.rolelist = [];
	$scope.statuslist = [];
	$scope.statelist = [];
	$scope.search = "";
	$scope.excelList = [];
	$scope.reportManagerArr =[];
	var reportManager = {
		'name':'',
		'id':0

	}
	$(document).ready(function () {
	$(function () {
		$("#auditFrom").datepicker({
			changeMonth: true,
		changeYear: true,
			maxDate : "0",
			dateFormat : 'dd-mm-yy',
			 onClose: function( selectedDate ) {
			$( "#auditTo" ).datepicker( "option", "minDate", selectedDate );
		  }
		});

	});
	$(function () {
		$("#auditTo").datepicker({
			changeMonth: true,
		changeYear: true,
			dateFormat : 'dd-mm-yy',
			 minDate: $scope.startDate,
		 maxDate: "0",
		  onClose: function( selectedDate ) {
			$( "#auditFrom" ).datepicker( "option", "maxDate", 'maxDate' );
		  }
		});

	});
	})
	$scope.list = [];
	var realData = [];
	$scope.ndList = [];
	$scope.realArray = [];
	$scope.dataList = {};

	$scope.role = {
		'10' : "Admin",
		'20' : "Trainer",
		'30' : "Supervisor",
		'40' : "Learner",
	}
	$scope.status = {
		'1' : "Active",
		'2' : "Inactive",

	}

	$scope.dataList = {
		'role' : roleArr,
		'state' : stateArr,
		'status' : statusArr,
		'ND' : ndArr,
		'supervisor_id':managerArr,
		'zone' : $scope.zone_name,
		'start_date' : $scope.startDate,
		'end_date' : $scope.endDate
	}

	$(document).ready(function () {
		$(".click_me").click(function () {
			$(".dis_filt").toggle();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();

		});

	});
	$(document).ready(function () {
		$(".click_me1").click(function () {

			$(".dis_filt1").toggle();
			$(".dis_filt").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".click_me2").click(function () {

			$(".dis_filt2").toggle();
			$(".dis_filt1").hide();
			$(".dis_filt").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".dis1").click(function () {

			$(".dis_filt2").hide();
			$(".dis_filt1").hide();
			$(".dis_filt").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".click_me3").click(function () {

			$(".dis_filt3").toggle();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		});

	});
	$(document).ready(function () {
		$(".click_me4").click(function () {

			$(".dis_filt4").toggle();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt").hide();
			$(".dis_filt5").hide();
		});

	});
		$(document).ready(function () {
		$(".click_me5").click(function () {

			$(".dis_filt5").toggle();
			$(".dis_filt4").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt").hide();
			$(".dis_filt3").hide();
			
		});

	});

	$http.get(baseUrl + "user/supervisorsList")
	.success(function (response) {
		if (response.status == 'success') {
			realData = response.data;
			$scope.reportManagerArr =[];
			for(var i=0;i<realData.length;i++)
			{	
				reportManager={};
				reportManager.name = realData[i].first_name+" "+realData[i].last_name;
				reportManager.id = realData[i].id;
				$scope.reportManagerArr.push(reportManager);	
			}
			console.log($scope.reportManagerArr);
		}
	}).error(function () {
		toaster.pop('error',response.result.msg);
	})
	
	$scope.getZone = function () {
		dataService.getData($scope.data, baseUrl + "address/zone").then(function (dataResponse) {
			$scope.zones = dataResponse.data.data;		
			$scope.states = [];
			});
	}
	$scope.getZone();

	$scope.getStates = function () {

		dataService.getData({
			'zone_name' : $scope.zone_name
		}, baseUrl + "address/state", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				$scope.states = response.data;
				$scope.cites = [];
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
		$scope.applyFunction();
	}

	$http.get(baseUrl + "user/nd")
	.success(function (response) {
		if (response.status == "success") {
			$scope.ndList = response.data;
		} else {
			toaster.pop("error", response.result.msg);
		}
	})

	$(document).ready(function () {
	setTimeout(function(){
	$('#ndAll').on('click',function () {
	        ndArr = [];
			$('.allndList').prop('checked', this.checked);
			ndArr = $scope.ndList;
			//console.log(ndArr);
			$scope.applyFunction();
		});

		$('.allndList').on('change',function () {
			var check = ($('.allndList').filter(":checked").length == $('.allndList').length);			
			$('#ndAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});

	$scope.changeNd = function () {
		ndArr = [];		
		for (var i in $scope.ndList) {		
			if ($(".allndList").eq(i).prop('checked')) {
				ndArr.push($scope.ndList[i]);				
			}
		}

		$scope.applyFunction();	
	}
	$(document).ready(function () {
	setTimeout(function(){
	$('#roleAll').on('click',function () {
			$('.allroleList1').prop('checked', this.checked);
			roleArr=[];
			for(var i in $scope.role)
			{
				roleArr.push(i);
			}
			$scope.applyFunction();
		});

		$('.allroleList1').on('change',function () {
			var check = ($('.allroleList1').filter(":checked").length == $('.allroleList1').length);			
			$('#roleAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeRole = function () {
		
		roleArr = [];
		for (var i=0;i<Object.keys($scope.role).length;i++) {
		console.log($(".allroleList1").eq(i).prop('checked'))
		if($(".allroleList1").eq(i).prop('checked')) {		
				
				roleArr.push(Object.keys($scope.role)[i])
				
			}
		}	
		$scope.applyFunction();

	}
	$(document).ready(function () {
	setTimeout(function(){
	$('#statusAll').on('click',function () {
			$('.allstatusList').prop('checked', this.checked);
			statusArr=[];
			for(var i in $scope.status)
			{
				statusArr.push(i);
			}
			$scope.applyFunction();
		});

		$('.allstatusList').on('change',function () {
			var check = ($('.allstatusList').filter(":checked").length == $('.allstatusList').length);			
			$('#statusAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeStatus = function () {
		
		statusArr = [];
		for (var i=0;i<Object.keys($scope.status).length;i++) {
		console.log($(".allstatusList").eq(i).prop('checked'))
		if($(".allstatusList").eq(i).prop('checked')) {		
				statusArr.push(Object.keys($scope.status)[i])
				
			}
		}	

		$scope.applyFunction();
	}

	$(document).ready(function () {
	setTimeout(function(){
	$('#managerAll').on('click',function () {
			$('.allmanagerList').prop('checked', this.checked);
			managerArr=[];
			for(var i in $scope.reportManagerArr)
			{
				managerArr.push($scope.reportManagerArr[i].id);
			}
			$scope.applyFunction();
		});

		$('.allmanagerList').on('change',function () {
			var check = ($('.allmanagerList').filter(":checked").length == $('.allmanagerList').length);			
			$('#managerAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeManager = function () {
		
		managerArr = [];
		for (var i=0;i<$scope.reportManagerArr.length;i++) {
		console.log($(".allmanagerList").eq(i).prop('checked'))
		if($(".allmanagerList").eq(i).prop('checked')) {		
				managerArr.push($scope.reportManagerArr[i].id)
				
			}
		}	

		$scope.applyFunction();
	}

	$(document).ready(function () {
	setTimeout(function(){
	$('#statesAll').on('click',function () {
			
			$('.allstatesList').prop('checked', this.checked);
			stateArr=[];
			for(var i=0;i<$scope.states.length;i++)
			{
				stateArr.push($scope.states[i].state_name);
			}		
			
			$scope.applyFunction();
		});
		
		$(document).on('change','.allstatesList',function () {
			var check = ($('.allstatesList').filter(":checked").length == $('.allstatesList').length);			
			$('#statesAll').prop("checked", check);
			$scope.$apply();
		});
	},1000)
		
	});
	$scope.changeStates = function () {
		
		stateArr = [];
		for (var i=0;i<$scope.states.length;i++) {		
			if ($(".allstatesList").eq(i).prop('checked')) {
				stateArr.push($scope.states[i].state_name);				
			}
		}
		$scope.applyFunction();
	}

	$scope.applyFunction = function () {

		$scope.dataList = {
			'role' : roleArr,
			'state' : stateArr,
			'status' : statusArr,
			'ND' : ndArr,
			'supervisor_id':managerArr,
			'zone' : $scope.zone_name,
			'start_date' : $scope.startDate,
			'end_date' : $scope.endDate
		}
		console.log($scope.dataList);
	}

	$scope.resetAll = function () {
		
		$scope.statelist = [];
		$scope.ndlist = [];
		$scope.statuslist = [];
		$scope.zone_name ='';
		$scope.rolelist = [];
		ndArr = [];
		roleArr = [];
		statusArr = [];
		stateArr = [];
		managerArr = [];
		$scope.dataList.role=[];
		$scope.dataList.state=[];
		$scope.dataList.status =[];
		$scope.dataList.supervisor_id =[];
		$scope.dataList.ND=[];
		$scope.dataList.zone=[];
		$scope.dataList.start_date=[];
		$scope.dataList.end_date=[];
	
		$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		$(".allroleList1").prop('checked', false);
		$(".allmanagerList").prop('checked', false);
		$("#managerAll").prop('checked', false);		
		$(".allstatesList").prop('checked', false);
		$(".allstatusList").prop('checked', false);
		$(".allndList").prop('checked', false);
		$("#statesAll").prop('checked', false);
		$("#statusAll").prop('checked', false);
		$("#roleAll").prop('checked', false);
		$("#ndAll").prop('checked', false);
		$scope.startDate = "";
		$scope.endDate = "";
		$scope.initData();
	}
	
	$scope.initData = function () {
	var pagination ;
		$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		var fd = new FormData();
		if ($scope.search.length > 0) {
			fd.append('search_keyword', $scope.search);
			pagination = NaN;
		}
		if ($scope.dataList.role.length > 0) {
			fd.append('role', $scope.dataList.role);
			pagination = NaN;
		}
		if ($scope.dataList.state.length > 0) {
			fd.append('state', $scope.dataList.state);
			pagination = NaN;
		}
		if ($scope.dataList.supervisor_id.length > 0) {
			fd.append('supervisor_id', $scope.dataList.supervisor_id);
			pagination = NaN;
		}
		if ($scope.dataList.status.length > 0) {
			fd.append('status', $scope.dataList.status);
			pagination = NaN;
		}
		if ($scope.dataList.ND.length > 0) {
			fd.append('ND', $scope.dataList.ND);
			pagination = NaN;
		}
		if ($scope.dataList.zone != undefined) {
			fd.append('zone', $scope.dataList.zone);
			pagination = NaN;
		}
		if ($scope.dataList.start_date != undefined) {
			fd.append('start_date', $scope.dataList.start_date);
			pagination = NaN;
		}
		if ($scope.dataList.end_date != undefined) {
			fd.append('end_date', $scope.dataList.end_date);
			pagination = NaN;
		}
		if($scope.search.length == 0 && $scope.dataList.role.length == 0  && $scope.dataList.state == 0 && $scope.dataList.supervisor_id == 0 && $scope.dataList.status == 0 &&  $scope.dataList.ND == 0 && $scope.dataList.zone == undefined && $scope.dataList.start_date == undefined && $scope.dataList.end_date == undefined)
		{
		pagination = Math.ceil(Number((($scope.currentPage * $scope.itemsPerPage) / 100) + 1));
		}
		$http.post(baseUrl + "report/user/audittrail?page_no=" +pagination,fd, {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})

		.success(function (response) {
			//$scope.list = response.data;
			//$scope.list = arrayPushService.arrayPush(response.data, $scope.list);
			if(isNaN(pagination))
			{
				$scope.list = response.data;
				//$scope.excelList = angular.copy(response.data);
			}
			else
			{
			$scope.list = arrayPushService.arrayPush(response.data, $scope.list);
			//$scope.excelList = angular.copy($scope.list);
			}
			realData =response.data;
			
			$scope.path = "";
			$scope.realArray = $scope.list;

			$scope.totalItems = $scope.list.length;
			$scope.sort($scope.sortKey);
		});
	}
	$scope.exportUserData = function(){
		$scope.getAll =1;
		
		$(".dis_filt").hide();
			$(".dis_filt1").hide();
			$(".dis_filt2").hide();
			$(".dis_filt3").hide();
			$(".dis_filt4").hide();
			$(".dis_filt5").hide();
		var fd = new FormData();
		fd.append('getAll', $scope.getAll);
		
		if ($scope.search.length > 0) {
			fd.append('search_keyword', $scope.search);
		
		}
		if ($scope.dataList.role.length > 0) {
			fd.append('role', $scope.dataList.role);
			
		}
		if ($scope.dataList.state.length > 0) {
			fd.append('state', $scope.dataList.state);
			
		}
		if ($scope.dataList.supervisor_id.length > 0) {
			fd.append('supervisor_id', $scope.dataList.supervisor_id);
		
		}
		if ($scope.dataList.status.length > 0) {
			fd.append('status', $scope.dataList.status);
		
		}
		if ($scope.dataList.ND.length > 0) {
			fd.append('ND', $scope.dataList.ND);
			
		}
		if ($scope.dataList.zone != undefined) {
			fd.append('zone', $scope.dataList.zone);
			
		}
		if ($scope.dataList.start_date != undefined) {
			fd.append('start_date', $scope.dataList.start_date);
			
		}
		if ($scope.dataList.end_date != undefined) {
			fd.append('end_date', $scope.dataList.end_date);
			
		}
		
		$http.post(baseUrl + "report/user/audittrail",fd, {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})

		.success(function (response) {
					
				$scope.excelList = response.data;
				$scope.exportData();
		})

	}
	$scope.initData();
	$scope.showDetail = function (id) {
		for (var i = 0; i < realData.length; i++) {
			if (id == realData[i].user_id) {
				$scope.moduleAccess = realData[i].module_access;
				break;
			}

		}
	}
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
	$scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.list.length / $scope.itemsPerPage);
	};
	$scope.sort = function (keyname) {
		$scope.sortKey = keyname;
		$scope.reverse = !$scope.reverse;

	}
	$scope.pageChanged = function () {
		if ($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage)) {
			$scope.initData();
		}
	};

	$scope.exportData = function () {
		//alert();
		var blob = new Blob([document.getElementById('exportable').innerHTML], {
				type : "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8"
			});
		saveAs(blob, "Report.xls");
	};

}

angular
.module('cubeWebApp')
.controller('userlistingCtrl', userlistingCtrl)
.controller('auditTrailCtrl', auditTrailCtrl)
