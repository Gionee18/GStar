
function categoryListingCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	$scope.categoryList = [];
	$scope.editId;
	$scope.orderList= [];
	$scope.getUpdatedData=function(data){
		$scope.categoryList = data;
		$scope.totalItems = $scope.categoryList.length;
	}
	
	$scope.initData = function()
	{		
		 $scope.data="";
		dataService.getData($scope.data, baseUrl + "list/category/news"  ).then(function(dataResponse) {
			$scope.categoryList = dataResponse.data.data;
			$scope.orderList = angular.copy(dataResponse.data.data);
			$scope.totalItems = $scope.categoryList.length;
		}) 
	}
	
	$scope.initData();
	
	$scope.editCategory = function(id){
		//$scope.editId = id;
		$scope.data="";
		dataService.getData($scope.data, baseUrl + "category/news/"+id  ).then(function(dataResponse) {
			$scope.editCatData = dataResponse.data.data;
			$scope.editCatData.id = id;
			////console.log($scope.editCatData);
			
		}) 
	}
	$scope.submitEditCat = function(){
		$("#cancel").click();
		$http.post(baseUrl+"edit/category/news",$scope.editCatData)
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop('success',response.msg);
				$scope.initData();
			}
			else
			{
				toaster.pop('error',response.result.msg);
			}
			
			})
	
	}
	
	$scope.deleteCategory = function(id)
	{	
		   
	if(confirm(msg["C1001"])){
		
		$http.post(baseUrl+"delete/category/news",{'id':id})
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop("success",response.msg);
				$scope.initData();
				$("#close").click();
			}else
			{
				toaster.pop("error",response.result.msg);
			}
			
				
			})
		}
			
	
	
	}
	
	
	
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.gallaryDocs.length / $scope.itemsPerPage);
	};		
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.categoryList = $scope.categoryList.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.categoryList.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};
	
		$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "topic/category/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.sort($scope.sortKey);
				
				$scope.initData();
				
				
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
	
}

function categoryAddCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.addData = function()
	{		
		
		//dataService.getData($scope.data, baseUrl + "list/video/tutorial"  ).then(function(dataResponse) {
		$http.post(baseUrl+"add/category/news",$scope.addCategory)
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop('success',response.msg);
				$location.path('/updates/category-listing');
			}
			else
			{
				toaster.pop('error',response.result.msg);
			}
		}) 
	}
}	
	
function subcategoryListingCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,arrayPushService,$localStorage) 
{
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	$scope.categoryList = [];
	$scope.editId;
	$scope.subcategoryList =[];
	var filterData = [];
	var realData = [];
	$scope.orderList = [];	
	
	$scope.getUpdatedData=function(data){
		$scope.subcategoryList = data;
		$scope.totalItems = $scope.subcategoryList.length;
	}
	
	
	$scope.categoryData = function()
	{		
		 $scope.data="";
		dataService.getData($scope.data, baseUrl + "list/category/news"  ).then(function(dataResponse) {
			$scope.categoryList = dataResponse.data.data;
		
		}) 
	}
	$scope.categoryData();
	$scope.initData = function()
	{		
		 $scope.data="";
		dataService.getData($scope.data, baseUrl + "list/subcategory/news"  ).then(function(dataResponse) {
			$scope.subcategoryList = dataResponse.data.data;
			$scope.orderList = angular.copy(dataResponse.data.data);
			realData = $scope.subcategoryList;
			if($scope.category_id != undefined )
			{
				$scope.filterList($scope.category_id)
			}
		}) 
	}
	
	$scope.initData();
	
	$scope.filterList = function(id)
	{	
	
	
		
		if(id != "")
		{
			filterData =[];
			for(var i=0;i<realData.length;i++)
			{	
				if(id== realData[i].category_id)
				{
					filterData.push(realData[i]);
				}
				
			
			}
			$scope.subcategoryList = filterData;
			$scope.orderList = angular.copy($scope.subcategoryList);
			$scope.totalItems = $scope.subcategoryList.length;
		}
		else
		{
			$scope.subcategoryList = realData;
			$scope.totalItems = $scope.subcategoryList.length;
			
		}
	}
	
	$scope.editSubCategory = function(id){
		//$scope.editId = id;
		$scope.data="";
		dataService.getData($scope.data, baseUrl + "subcategory/news/"+id  ).then(function(dataResponse) {
			$scope.editSubCatData = dataResponse.data.data[0];
			$scope.editSubCatData.id = id;
			////console.log($scope.editSubCatData);
			
		}) 
	}
	$scope.submitEditSubCat = function(){
		$("#cancel").click();
		$http.post(baseUrl+"edit/subcategory/news",$scope.editSubCatData)
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop('success',response.msg);
				$scope.initData();
			}
			else
			{
				toaster.pop('error',response.result.msg);
			}
			
			})
	
	}
	
	$scope.deleteCategory = function(id)
	{	
		   
	if(confirm("Are you sure to delete this subcategory?")){
		
		$http.post(baseUrl+"delete/subcategory/news",{'id':id})
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop("success",response.msg);
				$scope.initData();
				$("#close").click();
			}else
			{
				toaster.pop("error",response.result.msg);
			}
			
				
			})
		}
	
	}
	
	
	
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.gallaryDocs.length / $scope.itemsPerPage);
	};		
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.categoryList = $scope.categoryList.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.categoryList.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "topic/subcategory/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.sort($scope.sortKey);
				
				$scope.initData();
				
				
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
	


}

function subcategoryAddCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,arrayPushService,$localStorage) 
{	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	$scope.categoryData = function()
	{		
		 $scope.data="";
		dataService.getData($scope.data, baseUrl + "list/category/news"  ).then(function(dataResponse) {
			$scope.categoryList = dataResponse.data.data;
			////console.log($scope.categoryList);
			
		}) 
	}
	$scope.categoryData();
	$scope.addSubcat = function()
	{
		$http.post(baseUrl+"add/subcategory/news",$scope.addData)
			.success(function(response){
			if(response.status == "success"){
				$location.path("updates/subcategory-listing");
				toaster.pop("success",response.msg);
			}
			else{
				toaster.pop("error",response.result.msg)
			}
			
			})
	}
	
	
}


function topicListingCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,arrayPushService) {
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	$scope.orderList = [];
	$scope.topicList = [];
	$scope.nowtime = (new Date).getTime();
	
	$scope.getUpdatedData=function(data){
		$scope.topicList = data;
		$scope.totalItems = $scope.topicList.length;
	}
	$scope.initData = function(){
		 $scope.data="";
		 dataService.getData($scope.data, baseUrl + "list/news/topic?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) { 
		
			$scope.topicList = arrayPushService.arrayPush(dataResponse.data.data, $scope.topicList);
		
			$scope.orderList = angular.copy(dataResponse.data.data);
			
			$scope.totalItems = $scope.topicList.length;
		})
	}
	$scope.initData();

	$scope.deleteTopic = function(id)
	{
		if(confirm(msg["C1003"])){
		$http.post(baseUrl+"delete/news/topic",{'id':id})
			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop("success",response.msg);
				
				$scope.initData();
			}else
			{
				toaster.pop("error",response.result.msg);
			}
			
				
			})
		}
	
	}
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.topicList.length / $scope.itemsPerPage);
	};	
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.topicList.length / $scope.itemsPerPage))
		{
			
				$scope.initData();
				$scope.sort($scope.sortKey);
			
		}
	};	
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		
    }
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "topic/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.initData();$scope.sort($scope.sortKey);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
}

function topicAddCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	$(function() {
		$( "#maskedDate" ).datepicker({  minDate: "0",changeMonth: true,
			changeYear: true, dateFormat: 'dd-mm-yy' });
	});
	$scope.categoryList = {};
	$scope.CatStatus = CatStatus;
	 $scope.data="";
		dataService.getData($scope.data, baseUrl + "catsubcat/news/topic"  ).then(function(dataResponse) {
			$scope.categoryList = dataResponse.data.data;
			////console.log($scope.categoryList);
			
		})
	$scope.changeSubcat  = function(id)
	{
		if(id != undefined )
		{	
			for(var i=0;i<$scope.categoryList.length;i++)
			{
				if(id== $scope.categoryList[i].id)
				{
					$scope.subcatList= $scope.categoryList[i].subcat;
				}
			}
		}
		else
		{
			$scope.addTopic.subcategory_id = "";
		}
	}
	$scope.tinymceOptions = {
		selector: "textarea", 
		plugins: 'code table image advlist',
		advlist_bullet_styles: "square",
				
	  };
	 

	$scope.addTopicData = function(){
		/* if($scope.addTopic.expired_on != undefined)
		{ */
		 var fd = new FormData();
		
		if($scope.addTopic.expired_on != undefined)
		{
		 fd.append('expired_on', $scope.addTopic.expired_on );
		}
		fd.append('topic_name', $scope.addTopic.topic_name );
		fd.append('status', $scope.addTopic.status );
		if($scope.addTopic.notification_admin != undefined)
		{
		fd.append('notification_admin', $scope.addTopic.notification_admin );
		}
		if($scope.addTopic.notification_trainer != undefined)
		{
		fd.append('notification_trainer', $scope.addTopic.notification_trainer );
		}
		if($scope.addTopic.notification_supervisor != undefined)
		{
		fd.append('notification_supervisor', $scope.addTopic.notification_supervisor );
		}
		if($scope.addTopic.notification_learner != undefined)
		{
		fd.append('notification_learner', $scope.addTopic.notification_learner );
		}
		
		fd.append('category_id', $scope.addTopic.category_id );
		fd.append('subcategory_id', $scope.addTopic.subcategory_id );
		fd.append('topic_desc', $scope.addTopic.topic_desc );
		
			$http.post(baseUrl+"add/news/topic",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })

			.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop("success",response.msg);
				$location.path('/updates/topic-listing');
			}
			else{
				toaster.pop("error",response.result.msg);
			}
			
			})
	}
	
	
	
}

//topic edit controller

function topicEditCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,arrayPushService,$localStorage) {
	$(function() {
		$( "#maskedDate" ).datepicker({  minDate: "0", changeMonth: true,
			changeYear: true, dateFormat: 'dd-mm-yy' });
	});
	$scope.CatStatus = CatStatus;
	$scope.baseUrl = baseUrl;
	$scope.topicID = $routeParams.id;
	$scope.showModel = true;
	
	
	$scope.tinymceOptions = {
		selector: "textarea", 
		plugins: 'code table image advlist',
		advlist_bullet_styles: "square",
				
	  };
	/* $scope.editTopicData = function(){
		$http.get(baseUrl+"news/topic/"+$scope.topicID)
			.success(function(response){
				$scope.editTopic = response.data;
				$scope.editTopic.status = Number( response.data.status );
				$scope.changeSubcat($scope.editTopic.category_id);
		})
	}Helvetica neue
	$scope.editTopicData(); */

	$scope.initData = function()
	{		
		
		dataService.getData($scope.data, baseUrl + "catsubcat/news/topic"  ).then(function(dataResponse) {
			$scope.categoryList = dataResponse.data.data;
			$http.get(baseUrl+"news/topic/"+$scope.topicID)
			.success(function(response){
				$scope.editTopic = response.data;
				$scope.editTopic.status = Number( response.data.status );
				$scope.changeSubcat($scope.editTopic.category_id);
		})
			
		}) 
	}
	$scope.initData();
	$scope.changeSubcat  = function(id)
	{
		if(id != undefined )
		{	
			for(var i=0;i<$scope.categoryList.length;i++)
			{
				if(id== $scope.categoryList[i].id)
				{
					$scope.subcatList= $scope.categoryList[i].subcat;
				}
			}
		}
		else
		{
			$scope.addTopic.subcategory_id = "";
		}
	}


	$scope.submitTopicData = function(){
	 var fd = new FormData();
		
		if($scope.editTopic.expired_on != undefined)
		{
		 fd.append('expired_on', $scope.editTopic.expired_on );
		}
		fd.append('topic_name', $scope.editTopic.topic_name );
		fd.append('status', $scope.editTopic.status );
		if($scope.editTopic.notification_admin != undefined)
		{
		fd.append('notification_admin', $scope.editTopic.notification_admin );
		}
		if($scope.editTopic.notification_trainer != undefined)
		{
		fd.append('notification_trainer', $scope.editTopic.notification_trainer );
		}
		if($scope.editTopic.notification_supervisor != undefined)
		{
		fd.append('notification_supervisor', $scope.editTopic.notification_supervisor );
		}
		if($scope.editTopic.notification_learner != undefined)
		{
		fd.append('notification_learner', $scope.editTopic.notification_learner );
		}
		
		fd.append('category_id', $scope.editTopic.category_id );
		fd.append('subcategory_id', $scope.editTopic.subcategory_id );
		fd.append('topic_desc', $scope.editTopic.topic_desc );
		fd.append('id', $scope.topicID );
		
			$http.post(baseUrl+"edit/news/topic",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })

		.success(function(response){
			if(response.status == 'success')
			{	
				toaster.pop('success',response.msg);
				$scope.initData();
				$location.path('/updates/topic-listing');
			}
			else
			{
				toaster.pop('error',response.result.msg);

			}
		})
	}

	
}



angular
	.module('cubeWebApp')
	.controller('categoryListingCtrl', categoryListingCtrl)
	.controller('categoryAddCtrl', categoryAddCtrl)
	.controller('topicListingCtrl', topicListingCtrl)
	.controller('topicAddCtrl', topicAddCtrl)
	.controller('topicEditCtrl', topicEditCtrl)
	.controller('subcategoryListingCtrl', subcategoryListingCtrl)
	.controller('subcategoryAddCtrl', subcategoryAddCtrl)
	
	
