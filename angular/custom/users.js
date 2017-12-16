function usersCtrl($scope, $http, DTOptionsBuilder, dataService, toaster, $location, arrayPushService,$timeout) {
	$('#page-wrapper').removeClass('nav-small');

	var r = [];
	$scope.dataUsers = [];
	$scope.filterselectUser = [];
	$scope.search="";
	$scope.statuslist ="";
	$scope.archive ="";
	$scope.archivechck =false;
	$scope.userRoleFilter= {
		'10':"Admin",
		'20':"Trainer",
		'30':"Supervisor",
		'40':"Learner"
	}
	$scope.archivelist= {
		"1":"Archive",
		"2":"Non-Archive"
	}
	$scope.selectAll = function () {
		$scope.filterselectUser = [];
		if ($("#selectUser").is(":checked")) {
			for (var i = 0; i < $scope.dataUsers.length; i++) {
				$scope.filterselectUser.push($scope.dataUsers[i].id);
				$("#selectUser" + $scope.dataUsers[i].id).prop("checked", true);

			}
		} else {
			$scope.filterselectUser = [];
			for (var i = 0; i < $scope.dataUsers.length; i++) {
				$("#selectUser" + $scope.dataUsers[i].id).prop("checked", false);
			}
		}
	}

	$scope.selectUser = function (id) {
		if ($("#selectUser" + id).is(":checked")) {
			$scope.filterselectUser.push(id);
		} else {
			$("#selectUser").prop("checked", false);
			$scope.filterselectUser = [];
			for (var i = 0; i < $scope.dataUsers.length; i++) {
				if (id != $scope.dataUsers[i].id) {
					$scope.filterselectUser.push($scope.dataUsers[i].id);
				}
			}
		}
		
	}


	$scope.getUsers = function () {	
	
		var fd = new FormData();
		var pagination = NaN;
		var url ='?page_no='+pagination;	
		
		if($scope.statuslist.length > 0)
		{			
			url =url+'&role='+$scope.statuslist;
			pagination = NaN;	
		}
		if($scope.search.length > 0)
		{
			
				url =url+"&"+'search_keyword='+$scope.search;
			
			pagination = NaN;
	
		}
		if($scope.statuslist.length == 0 && $scope.search.length == 0)
		{
		pagination = Math.ceil(Number((($scope.currentPage * $scope.itemsPerPage) / 100) + 1));
		}
		
		
		
		
		
		
		
		
		
		
	/* 	if( $scope.statuslist.length > 0)
		{
		 fd.append('role', $scope.statuslist );
		 pagination = NaN
		}
		if($scope.search.length>0)
		{
		 fd.append('search_keyword', $scope.search );
		  pagination = NaN
		}
		if($scope.statuslist.length == 0 && $scope.search.length == 0)
		{
		pagination = Math.ceil(Number((($scope.currentPage * $scope.itemsPerPage) / 100) + 1));
		}
		$http.post(baseUrl+"users?page_no=" +pagination,fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
	 */
	 $http.get( baseUrl + "users"+url)
		.success(function (response) {
			
		if($scope.archivechck == true)
		{
			
			$scope.dataUsers = response.archive_user;
		}
		else
		{	
			if(isNaN(pagination))
			{
				$scope.dataUsers = response.data;
			}
			else
			{
			$scope.dataUsers = arrayPushService.arrayPush(response.data, $scope.dataUsers);
			}
			//$scope.dataUsers = response.data;				
		}
		//$scope.dataUsers = dataResponse.data.data;
			$scope.totalItems = $scope.dataUsers.length;
		});
	}

	$scope.getUsers();
	
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
	$scope.currentPage = 1;
	$scope.maxSize = 10;

	$scope.pageChanged = function () {
		if ($scope.currentPage == Math.ceil($scope.dataUsers.length / $scope.itemsPerPage)) {
			$scope.getUsers();
		}
	};
	$scope.sort = function (keyname) {
		$scope.sortKey = keyname;
		$scope.reverse = !$scope.reverse;

		$scope.list = $scope.list.sort(function (a, b) {
				if (!$scope.reverse)
					return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
				else
					return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
			});
	};
	$scope.deleteUser = function (id) {
		if (confirm(msg['U1001'])) {

			dataService.getData({
				'id' : id
			}, baseUrl + "user/delete", "POST").then(function (dataResponse) {
				var response = dataResponse.data;
				if (response.status == 'success') {
					toaster.pop('success', response.msg);

					$scope.getUsers();
					var tmp = [];
					for (var i = 0; i < $scope.dataUsers.length; i++) {
						if (id != $scope.dataUsers[i].id)
							tmp.push($scope.dataUsers[i]);
					}
					$scope.dataUsers = tmp;
				} else {
					toaster.pop('error', response.result.msg);
				}
			});
		}
	}

	$scope.deleteAllUser = function () {
		if ($(".checkuser").is(':checked')) {
			if (confirm(msg['U1001'])) {
				dataService.getData({
					'id' : $scope.filterselectUser.join()
				}, baseUrl + "user/deleteAll", "POST").then(function (dataResponse) {
					var response = dataResponse.data;
					if (response.status == 'success') {
						toaster.pop('success', response.msg);
						//console.log($scope.filterselectUser)
						//$scope.getUsers();
						var tmp = [];

						for (var i = 0; i < $scope.dataUsers.length; i++) {
							//if(id != $scope.dataUsers[i].id)
							if ($.inArray($scope.dataUsers[i].id, ($scope.filterselectUser)) == -1)
								tmp.push($scope.dataUsers[i]);
						}
						$scope.dataUsers = tmp;
					} else {
						toaster.pop('error', response.result.msg);
					}
				});
			}
		} else {
			alert("please select to delete")
		}
	}

	$scope.exportData = function () {
		//var fd = new FormData();
		//var getAll =1;
		//fd.append('getAll', getAll);
		//var download =1;
		//var archive =1;
		//fd.append('download',download);
		/* if($scope.archive == true)
		{ */
		 //fd.append('archive', archive );
		//}
		//if( $scope.statuslist.length > 0)
		//{
		 //fd.append('role', $scope.statuslist );
		//}
		//if($scope.search.length>0)
		//{ 
		 //fd.append('search_keyword', $scope.search );
		//}
		var getAll =1;
		var download =1;
		var archive =1;
		url="?getAll="+1+"&download="+1;
		if($scope.archive == true)
		{
			url = url+'archive='+1;
		}
		if($scope.statuslist.length > 0)
		{
			
				url =url+"&"+'role='+$scope.statuslist;
			
		}
		if($scope.search.length > 0)
		{
				url =url+"&"+'search_keyword='+$scope.search;
		
		}
		window.open(baseUrl+"users"+url, '_blank');
		// $http.get(baseUrl+"users"+url)
		// .then(function(result) {  
			
			
	 //  //      	if($scope.archivechck == true){	
		// 	// 	$scope.dataUsersExport = response.archive_user;
		// 	// } else {
		// 	// 	$scope.dataUsersExport = response.data;				
		// 	// }
		// 	if($scope.archivechck == true){	
		// 		$scope.dataUsersExport = result.data.archive_user;
		// 	} else {
		// 		$scope.dataUsersExport = result.data.data;				
		// 	}
			
		// });
	}
	
	$scope.$watch('sysUpload.user_file', function (newValue, oldValue) {
		if (newValue) {
			$scope.uploadDocName = newValue.name
		}
	});

	$scope.importUsers = function () {

		var input = document.getElementById("user_file");
		if (input.files && input.files.length == 1) {
			if (input.files[0].size > 5 * 1024 * 1024) {
				alert(msg["U1002"]);
				return false;
			}
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if ($.inArray(ext, spreadSheetFormates) > -1) {}
			else {
				alert(msg["U1003"]);
				return false;
			}
		} else {
			alert(msg["U1004"]);
			return false;
		}
		var fd = new FormData();
		fd.append('user_file', $scope.sysUpload.user_file);

		$http.post(baseUrl + "user/import", fd, {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})
		.success(function (response) {
			if (response.status == 'success') {
				toaster.pop('success', response.result.msg);
				$("#import").click();
				$scope.getUsers();
			} else {
				toaster.pop('error', response.result.msg);
				$("#import").click();
			}

		}).error(function () {
			toaster.pop('error', msg['0000']);
		});
	}

	$scope.funSetUserForResetPassword = function (id) {
		$scope.setUserForResetPassword = id;
	}

	$scope.resetPassword = function () {
		$scope.resetPass.id = $scope.setUserForResetPassword;
		dataService.getData($scope.resetPass, baseUrl + "user/resetPasswordByAdmin", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				toaster.pop('success', response.result.msg);
				$(".cancel").click();
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
	}

	$('#resetPassword').on('hide.bs.modal', function (e) {
		$scope.resetPass = {};
	});
}

function addUserCtrl($scope, $http, DTOptionsBuilder, dataService, toaster, $location) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.show = false;
	$(function () {
		$("#maskedDate").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1970:2010',
			dateFormat : 'dd-mm-yy'
		});
	});

	// image upload
	$scope.stepsModel = [];
	$scope.imageUpload = function (element) {
		var reader = new FileReader();
		reader.onload = $scope.imageIsLoaded;
		reader.readAsDataURL(element.files[0]);
	}

	$scope.imageIsLoaded = function (e) {
		$scope.$apply(function () {
			$scope.stepsModel = [];
			$scope.stepsModel.push(e.target.result);
		});
	}

	$http.get(baseUrl + "/user/supervisorsList")
	.success(function (response) {
		if (response.status == 'success') {
			$scope.supervisorsList = response.data;
			$scope.realSupervisorsList = response.data;
		}
	}).error(function () {
		toaster.pop('error', msg['0000']);
	});

	$scope.addUser = function () {

		if ($scope.addUser.password != $scope.addUser.confirm_password) {
			alert(msg["U1005"]);
			return false;
		}
		var input = document.getElementById("profilePic");

		if (input.files && input.files.length == 1) {
			if (input.files[0].size > 5 * 1024 * 1024) /* or maybe .size */
			{
				alert(msg['L1004']);
				return false;
			}
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if ($.inArray(ext, imagesFormates) > -1) {}
			else {
				alert(msg["L1005"]);
				return false;
			}
		}
		if ($scope.addUser.gender == '' || $scope.addUser.gender == undefined) {
			$scope.addUser.gender = 'male';
		}
		if ($scope.addUser.beat_route_id == '' || $scope.addUser.beat_route_id == undefined) {
			$scope.addUser.beat_route_id = '';
		}
		if ($scope.addUser.rt_code == '' || $scope.addUser.rt_code == undefined) {
			$scope.addUser.rt_code = '';
		}
		if ($scope.addUser.nd_name == '' || $scope.addUser.nd_name == undefined) {
			$scope.addUser.nd_name = '';
		}
		if ($scope.addUser.profile_picture == '' || $scope.addUser.profile_picture == undefined) {
			$scope.addUser.profile_picture = '';
		}
		if ($scope.addUser.city_name == '' || $scope.addUser.city_name == undefined) {
			$scope.addUser.city_name = '';
		}
		if ($scope.addUser.state == '' || $scope.addUser.state == undefined) {
			$scope.addUser.state = '';
		}
		if ($scope.addUser.zone == '' || $scope.addUser.zone == undefined) {
			$scope.addUser.zone = '';
		}
		var fd = new FormData();
		fd.append('status', $scope.addUser.status);
		fd.append('email', $scope.addUser.email);
		fd.append('first_name', $scope.addUser.first_name);
		fd.append('last_name', $scope.addUser.last_name);
		fd.append('contact', $scope.addUser.contact);
		fd.append('password', $scope.addUser.password);
		fd.append('role', $scope.addUser.role);
		fd.append('gender', $scope.addUser.gender);
		fd.append('dob', $scope.addUser.dob);
		fd.append('sp_id', $scope.addUser.sp_id);
		fd.append('city', $scope.addUser.city_name);
		fd.append('state', $scope.addUser.state_name);
		fd.append('zone', $scope.addUser.zone_name);
		fd.append('beat_route_id', $scope.addUser.beat_route_id);
		fd.append('rt_code', $scope.addUser.rt_code);
		fd.append('nd_name', $scope.addUser.nd_name);
		fd.append('rd_name', $scope.addUser.rd_name);
		fd.append('image', $scope.addUser.profile_picture);

		$http.post(baseUrl + "/user/add", fd, {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})
		.success(function (response) {
			if (response.status == 'success') {
				toaster.pop('success', response.result.msg);
				$location.path('/admin/users');
			} else {
				toaster.pop('error', response.result.msg);
			}
		}).error(function () {
			toaster.pop('error', msg['0000']);
		});
	};

	$scope.getZone = function () {
		dataService.getData($scope.data, baseUrl + "address/zone").then(function (dataResponse) {
			$scope.zones = dataResponse.data.data;
			$scope.totalItems = $scope.zones.length;
			$scope.states = [];
			$scope.cites = [];

		});
	}
	$scope.getZone();

	$scope.getStates = function () {
		dataService.getData({
			'zone_name' : $scope.addUser.zone_name
		}, baseUrl + "address/state", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				$scope.states = response.data;
				$scope.cites = [];
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
	}

	$scope.getCites = function () {
		dataService.getData({
			'state_name' : $scope.addUser.state_name
		}, baseUrl + "address/city", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				$scope.cites = response.data;
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
	}

	$scope.getNDs = function () {
		dataService.getData($scope.data, baseUrl + "user/nd").then(function (dataResponse) {
			$scope.NDs = dataResponse.data.data;
		});
	}
	$scope.getNDs();

	$scope.getRDs = function () {
		dataService.getData($scope.data, baseUrl + "user/rd?nd_name=" + $scope.addUser.nd_name).then(function (dataResponse) {
			$scope.RDs = dataResponse.data.data;
		});
	}

	$scope.getRTs = function () {
		dataService.getData($scope.data, baseUrl + "user/rt?rd_name=" + $scope.addUser.rd_name).then(function (dataResponse) {
			$scope.RTs = dataResponse.data.data;
		});
	}
	$scope.showrole = "Super Admin";
	$scope.changeRole = function () {
		$scope.show = true;
		if ($scope.addUser.role == "10") {
			$scope.showrole = "Super Admin";
			$scope.supervisorsList = [];
			for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
				if ($scope.realSupervisorsList[i].role == 5) {
					$scope.supervisorsList.push($scope.realSupervisorsList[i]);
				}
			}
		} else if ($scope.addUser.role == "20") {
			$scope.showrole = "Admin";
			$scope.supervisorsList = [];
			for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
				if ($scope.realSupervisorsList[i].role == 10) {
					$scope.supervisorsList.push($scope.realSupervisorsList[i]);
				}
			}
		} else if ($scope.addUser.role == "30") {
			$scope.showrole = "Trainer";
			$scope.supervisorsList = [];
			for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
				if ($scope.realSupervisorsList[i].role == 20) {
					$scope.supervisorsList.push($scope.realSupervisorsList[i]);
				}
			}
		} else if ($scope.addUser.role == "40") {
			$scope.showrole = "Supervisor";
			$scope.supervisorsList = [];
			for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
				if ($scope.realSupervisorsList[i].role == 30) {
					$scope.supervisorsList.push($scope.realSupervisorsList[i]);
				}
			}
		} else {
			$scope.showrole = "Super Admin";
		}

		$scope.addUser.sp_id = "";
	}
}

function editUserCtrl($scope, $http, $routeParams, $localStorage, DTOptionsBuilder, dataService, toaster, $location,$rootScope) {
	$scope.supervisorsList = [];
	$scope.editUser = {};
	$scope.profileUrl = baseUrl + 'uploads//profileImages//';

	$(function () {
		$("#maskedDate").datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1970:2010',
			dateFormat : 'dd-mm-yy',
			onClose : function (selectedDate) {
				$("#to").datepicker("option", "minDate", selectedDate);
			}
		});
	});

	$localStorage.usr_id = $routeParams.uid;
	$scope.usr_id = $routeParams.uid;
	// alert($scope.usr_id);
	$scope.stepsModel = [];
	$scope.imageUpload = function (element) {
		var reader = new FileReader();
		reader.onload = $scope.imageIsLoaded;
		reader.readAsDataURL(element.files[0]);
	}

	$scope.imageIsLoaded = function (e) {
		$scope.$apply(function () {
			$scope.stepsModel = [];
			$scope.stepsModel.push(e.target.result);
		});
	}
	$http.get(baseUrl + "/user/supervisorsList")
	.success(function (response) {
		if (response.status == 'success') {
			$scope.supervisorsList = response.data;
			$scope.realSupervisorsList = response.data;

			$http.get(baseUrl + "/user/edit/" + $routeParams.uid)
			.success(function (response) {
				if (response.status == 'success') {
					$scope.editUser = response.data;
					$scope.editUser.contact = parseInt($scope.editUser.contact);
					for (var i = 0; i < $scope.supervisorsList.length; i++) {
						if ($scope.supervisorsList[i].id == $scope.editUser.sp_id) {
							$scope.editUser.sp_id = $scope.supervisorsList[i];
						}
					}
					$scope.imgSrc = baseUrl + "uploads//profileImages//" + $scope.editUser.profile_picture;
					$scope.stepsModel = [];
					$scope.stepsModel.push($scope.imgSrc);

					$scope.getZone();
					$scope.getStates();
					$scope.getCites();

					$scope.getNDs();
				}
			}).error(function () {
				toaster.pop('error', msg['2002']);
			});

		}
	}).error(function () {
		toaster.pop('error', msg['0000']);
	});

	//edit user save
	$scope.EditUser1 = function () {
		var input = document.getElementById("profilePic");
		if (input.files && input.files.length == 1) {
			if (input.files[0].size > 5 * 1024 * 1024) /* or maybe .size */
			{
				alert(msg['L1004']);
				return false;
			}
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if ($.inArray(ext, imagesFormates) > -1) {}
			else {
				alert(msg["L1005"]);
				return false;
			}
		}

		if ($scope.editUser.beat_route_id == '' || $scope.editUser.beat_route_id == undefined) {
			$scope.editUser.beat_route_id = '';
		}
		if ($scope.editUser.rt_code == '' || $scope.editUser.rt_code == undefined) {
			$scope.editUser.rt_code = '';
		}
		if ($scope.editUser.nd_id == '' || $scope.editUser.nd_id == undefined) {
			$scope.editUser.nd_id = '';
		}
		if ($scope.editUser.rd_id == '' || $scope.editUser.rd_id == undefined) {
			$scope.editUser.rd_id = '';
		}
		if ($scope.editUser.profile_picture == '' || $scope.editUser.profile_picture == undefined) {
			$scope.editUser.profile_picture = '';
		}
		if ($scope.editUser.city_name == '' || $scope.editUser.city_name == undefined) {
			$scope.editUser.city_name = '';
		}
		if ($scope.editUser.state_name == '' || $scope.editUser.state_name == undefined) {
			$scope.editUser.state_name = '';
		}
		if ($scope.editUser.zone_name == '' || $scope.editUser.zone_name == undefined) {
			$scope.editUser.zone_name = '';
		}
		var fd = new FormData();
		fd.append('id', $localStorage.usr_id);
		fd.append('status', $scope.editUser.status);
		fd.append('email', $scope.editUser.email);
		fd.append('first_name', $scope.editUser.first_name);
		fd.append('last_name', $scope.editUser.last_name);
		fd.append('contact', $scope.editUser.contact);
		fd.append('role', $scope.editUser.role);
		fd.append('gender', $scope.editUser.gender);
		fd.append('dob', $scope.editUser.dob);
		fd.append('sp_id', $scope.editUser.sp_id.id);
		fd.append('city', $scope.editUser.city_name);
		fd.append('state', $scope.editUser.state_name);
		fd.append('zone', $scope.editUser.zone_name);
		fd.append('beat_route_id', $scope.editUser.beat_route_id);
		fd.append('rt_code', $scope.editUser.rt_code);
		fd.append('nd_name', $scope.editUser.nd_id);
		fd.append('rd_name', $scope.editUser.rd_id);
		fd.append('image', $scope.editUser.profile_picture);
		$http.post(baseUrl + "user/edit/" + $localStorage.usr_id, fd, {
			transformRequest : angular.identity,
			headers : {
				'Content-Type' : undefined
			}
		})
		.success(function (response) {
			if (response.status == 'success') {
				toaster.pop('success', response.result.msg);
				$location.path('/admin/users');
				/*  if($scope.userData.role == 10){
				$location.path('/admin/users');
				}
				else if($scope.userData.role > 10){
				$location.path('/admin/view-profile');
				} */
			} else {
				toaster.pop('error', response.result.msg);
			}
		}).error(function () {
			toaster.pop('error', msg['2002']);
		});
	};

	$scope.zones = [];
	$scope.states = [];
	$scope.cites = [];

	$scope.getZone = function () {
		dataService.getData($scope.data, baseUrl + "address/zone").then(function (dataResponse) {
			$scope.zones = dataResponse.data.data;
		});
	}

	$scope.getStates = function () {
		dataService.getData({
			'zone_name' : $scope.editUser.zone_name
		}, baseUrl + "address/state", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				$scope.states = response.data;
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
	}

	$scope.getCites = function () {
		dataService.getData({
			'state_name' : $scope.editUser.state_name
		}, baseUrl + "address/city", "POST").then(function (dataResponse) {
			var response = dataResponse.data;
			if (response.status == 'success') {
				$scope.cites = response.data;
			} else {
				toaster.pop('error', response.result.msg);
			}
		});
	}

	$scope.getNDs = function () {
		dataService.getData($scope.data, baseUrl + "user/nd").then(function (dataResponse) {
			$scope.NDs = dataResponse.data.data;
			$scope.getRDs();
		});
	}

	$scope.getRDs = function () {
		dataService.getData($scope.data, baseUrl + "user/rd?nd_name=" + $scope.editUser.nd_id).then(function (dataResponse) {
			$scope.RDs = dataResponse.data.data;
			$scope.getRTs();
		});
	}

	$scope.getRTs = function () {
		dataService.getData($scope.data, baseUrl + "user/rt?rd_name=" + $scope.editUser.rd_id).then(function (dataResponse) {
			$scope.RTs = dataResponse.data.data;
		});
	}
	$scope.showrole = "Super Admin";
	$scope.$watch('editUser.role', function (newValue, oldValue) {
		if (newValue) {
			/* if($scope.editUser.role == "20"){
			$scope.supervisorsList = [{'id':1,'first_name':'Admin'}];
			}else{
			$scope.supervisorsList = $scope.realSupervisorsList;
			} */
			if ($scope.editUser.role == "10") {
				$scope.showrole = "Super Admin";
				$scope.supervisorsList = [];
				for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
					if ($scope.realSupervisorsList[i].role == 5) {
						$scope.supervisorsList.push($scope.realSupervisorsList[i]);
					}
				}
			} else if ($scope.editUser.role == "20") {
				$scope.showrole = "Admin";
				$scope.supervisorsList = [];
				for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
					if ($scope.realSupervisorsList[i].role == 10) {
						$scope.supervisorsList.push($scope.realSupervisorsList[i]);
					}
				}
			} else if ($scope.editUser.role == "30") {
				$scope.showrole = "Trainer";
				$scope.supervisorsList = [];
				for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
					if ($scope.realSupervisorsList[i].role == 20) {
						$scope.supervisorsList.push($scope.realSupervisorsList[i]);
					}
				}
			} else if ($scope.editUser.role == "40") {
				$scope.showrole = "Supervisor";
				$scope.supervisorsList = [];
				for (var i = 0; i < $scope.realSupervisorsList.length; i++) {
					if ($scope.realSupervisorsList[i].role == 30) {
						$scope.supervisorsList.push($scope.realSupervisorsList[i]);
					}
				}
			} else {
				$scope.showrole = "Super Admin";
			}
			if (oldValue) {
				$scope.editUser.sp_id.id = "";
			}

		}
	});

	// $rootScope.$watch(function () {
		// return localStorage.getItem('ngStorage-userData');
	// }, function (newVal, oldVal) {
		// if (oldVal != newVal && newVal === undefined) {
			// //console.log(newVal);
			// $localStorage.userData = newVal;
		// }
	// })
}

angular
.module('cubeWebApp')
.controller('usersCtrl', usersCtrl)
