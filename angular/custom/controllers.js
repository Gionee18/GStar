/**
 * Cube - Bootstrap Admin Theme
 * Copyright 2014 Phoonio
 */

function mainCtrl($scope,$http,DTOptionsBuilder,$localStorage,toaster,dataService,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.pageTitle = "View Profile";
	$scope.detailUser={};
	$scope.initData = function()
	{
		 $scope.dtOptions = DTOptionsBuilder.newOptions()
       // .withDisplayLength(7)
        .withOption('bLengthChange', true)
		.withOption('bsearching', false)
		.withOption('bPaginate', true)
		.withOption('bFilter', true)
		.withOption('bInfo', true)
		.withOption('sPaginationType', "full_numbers")
		.withOption('iDisplayLength', 10);
		$scope.data="";
		// dataService.getData($scope.data,baseUrl+"/listUser").then(function(dataResponse) {
			// //////console.log(dataResponse)
			// $scope.list = dataResponse.data.result.users;
		// });
	}
	$scope.initData();
	//profile
	var usr_id=$localStorage.userData.id;
$http.get(baseUrl + "/user/edit/"+usr_id)
		.success(function (response) {
			if(response.status == 'success'){
				$scope.detailUser = response.data;
				if($scope.detailUser.role== 05)
				{
					$scope.detailUser.role='Super Admin';
				}
				else if($scope.detailUser.role==10)
				{
						$scope.detailUser.role='Admin';
				}
				else if($scope.detailUser.role==20)
				{
						$scope.detailUser.role='Trainer';
				}
				else if($scope.detailUser.role==30)
				{
						$scope.detailUser.role='Supervisor';
				}
				else if($scope.detailUser.role==40)
				{
						$scope.detailUser.role='Learner';
				}
				$scope.imgSrc=baseUrl+"uploads//profileImages//"+$scope.detailUser.profile_picture;
				spID= $scope.detailUser.sp_id;
				//console.log(spID)
				$scope.showData(spID);
			}
		}).error(function(){
			toaster.pop('error', msg['2002']);
		});
		
		
		$scope.showData = function(spID)
		{
		$http.get(baseUrl+"/user/edit/"+spID)
			.success(function(response){
				if(response.status == "success")
				{
					$scope.supervisorDetailedUser  = response.data;
					//console.log(response);
				}
			})
		}
		//change password
	$scope.changePassword=function(){
		if($scope.changePass.newpassword!=$scope.changePass.confirmpassword)
		{
		alert('new password and confirm password does not match');
	    return false;
		}
		 var fd = new FormData();
		 fd.append('oldpassword',$scope.changePass.oldpassword );
		 fd.append('newpassword', $scope.changePass.newpassword );
		 fd.append('confirmpassword',$scope.changePass.confirmpassword );
		 fd.append('id',$localStorage.userData.id );
		 $http.post(baseUrl + "user/Changepassword", fd , {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);	
       $location.path('/');				
			}
			else{
				toaster.pop('error',response.result.msg);
			}
		}).error(function(){
			toaster.pop('error', msg['2002']);
		}); 
		}
}

function emailCtrl($scope) {
	
	if (!$('#page-wrapper').hasClass('nav-small')) {
		$('#page-wrapper').addClass('nav-small');
	}
}

function dasboardCtrl($scope,toaster,$localStorage ) {
	if($localStorage.userData){
		var usr_profile=$localStorage.userData.profile_picture;
		$scope.imgSrc=baseUrl+"uploads//profileImages//"+usr_profile;
	}
	
}


function easyChartCtrl($scope) {
	
	$scope.percent = 65;
	$scope.options = {
		barColor: '#03a9f4',
		trackColor: '#f2f2f2',
		scaleColor: false,
		lineWidth: 8,
		size: 130,
		animate: 1500,
		onStep: function(from, to, percent) {
			$(this.el).find('.percent').text(Math.round(percent));
		},
	};

	$scope.optionsGreen = angular.copy($scope.options);
	$scope.optionsGreen.barColor = '#8bc34a';
	
	$scope.optionsRed = angular.copy($scope.options);
	$scope.optionsRed.barColor = '#e84e40';
	
	$scope.optionsYellow = angular.copy($scope.options);
	$scope.optionsYellow.barColor = '#ffc107';
	
	$scope.optionsPurple = angular.copy($scope.options);
	$scope.optionsPurple.barColor = '#9c27b0';
	
	$scope.optionsGray = angular.copy($scope.options);
	$scope.optionsGray.barColor = '#90a4ae';
};

function dashboardFlotCtrl($scope) {
	var data1 = [
	    [gd(2015, 1, 1), 838], [gd(2015, 1, 2), 749], [gd(2015, 1, 3), 634], [gd(2015, 1, 4), 1080], [gd(2015, 1, 5), 850], [gd(2015, 1, 6), 465], [gd(2015, 1, 7), 453], [gd(2015, 1, 8), 646], [gd(2015, 1, 9), 738], [gd(2015, 1, 10), 899], [gd(2015, 1, 11), 830], [gd(2015, 1, 12), 789]
	];
	
	var data2 = [
	    [gd(2015, 1, 1), 342], [gd(2015, 1, 2), 721], [gd(2015, 1, 3), 493], [gd(2015, 1, 4), 403], [gd(2015, 1, 5), 657], [gd(2015, 1, 6), 782], [gd(2015, 1, 7), 609], [gd(2015, 1, 8), 543], [gd(2015, 1, 9), 599], [gd(2015, 1, 10), 359], [gd(2015, 1, 11), 783], [gd(2015, 1, 12), 680]
	];
	
	var series = new Array();

	series.push({
		data: data1,
		bars: {
			show : true,
			barWidth: 24 * 60 * 60 * 12000,
			lineWidth: 1,
			fill: 1,
			align: 'center'
		},
		label: 'Revenues'
	});
	series.push({
		data: data2,
		color: '#e84e40',
		lines: {
			show : true,
			lineWidth: 3,
		},
		points: { 
			fillColor: "#e84e40", 
			fillColor: '#ffffff', 
			pointWidth: 1,
			show: true 
		},
		label: 'Orders'
	});

	$.plot("#graph-bar", series, {
		colors: ['#03a9f4', '#f1c40f', '#2ecc71', '#3498db', '#9b59b6', '#95a5a6'],
		grid: {
			tickColor: "#f2f2f2",
			borderWidth: 0,
			hoverable: true,
			clickable: true
		},
		legend: {
			noColumns: 1,
			labelBoxBorderColor: "#000000",
			position: "ne"       
		},
		shadowSize: 0,
		xaxis: {
			mode: "time",
			tickSize: [1, "month"],
			tickLength: 0,
			// axisLabel: "Date",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 12,
			axisLabelFontFamily: 'Open Sans, sans-serif',
			axisLabelPadding: 10
		}
	});

	var previousPoint = null;
	$("#graph-bar").bind("plothover", function (event, pos, item) {
		if (item) {
			if (previousPoint != item.dataIndex) {

				previousPoint = item.dataIndex;

				$("#flot-tooltip").remove();
				var x = item.datapoint[0],
				y = item.datapoint[1];

				showTooltip(item.pageX, item.pageY, item.series.label, y );
			}
		}
		else {
			$("#flot-tooltip").remove();
			previousPoint = [0,0,0];
		}
	});
}

angular
	.module('cubeWebApp')
	.controller('mainCtrl', mainCtrl)
	.controller('emailCtrl', emailCtrl)
	.controller('easyChartCtrl', easyChartCtrl)
	.controller('dashboardFlotCtrl', dashboardFlotCtrl)
	.controller('dasboardCtrl', dasboardCtrl)
	