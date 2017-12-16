
function manageTutorialCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$localStorage) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.videoList = {};
	$scope.list = [];
	var RealData = [];
	$scope.searchBox= "";
	$scope.showModel = true;
	$scope.catList = [];
	var localData = [];
	var filteredData = [];
	$scope.editData = {};
	$scope.showModelEdit = false;
	$scope.orderList = [];	
	$scope.getCategory = function(){
	
		dataService.getData($scope.data, baseUrl + "tutorial/categories/products").then(function(dataResponse) {
			$scope.catList = dataResponse.data.data;
			////console.log($scope.catList);
		});
	}
	$scope.getCategory();

	
	$scope.selectProduct = function(id)
	{
		$scope.list=RealData;
	    $scope.product_id = "";
		localData=[];

		if(id!= ""){
			$scope.showModel = false;
			for (var j = 0; j < $scope.list.length; j++) {
					if($scope.list[j].category_id == id){
						localData.push($scope.list[j]);
					}
			}
			$scope.list=localData;
			$scope.totalItems = $scope.list.length;
				$scope.orderList = $scope.list;
			for(var i=0;i<$scope.catList.length;i++)
			{
				if(id == $scope.catList[i].id)
				{
					$scope.productList = $scope.catList[i].product;
					////console.log($scope.productList);
					if($scope.productList.length == 0){
					$scope.showModel = true;
					//toaster.pop('error','No product in this category. Please add product in this category first');
					} 
				}
			}
		}else{
			$scope.showModel = true;
			$scope.product_id = "";
			$scope.list=RealData;
			$scope.totalItems = $scope.list.length;
		}
	}

	
	$scope.selectModel = function (mid) {
	filteredData = [];
	////console.log(localData);
	if(mid!= ""){
		for (var k = 0; k <localData.length; k++) {
			if(localData[k].product_id !== 'undefined')
			if (localData[k].product_id == mid) {
				filteredData.push(localData[k])
			}
		}
		////console.log(filteredData);
		$scope.list=filteredData;
		$scope.orderList = $scope.list;
		$scope.totalItems = $scope.list.length;
	}else{
		$scope.list=localData;	
		$scope.totalItems = $scope.list.length;
	}
		
	}
	$scope.getUpdatedData=function(data){
	$scope.list=data;
	$scope.totalItems = $scope.list.length;
	}	

	$scope.initData = function()
	{		
		$scope.data="";
		 dataService.getData($scope.data, baseUrl + "list/video/tutorial?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) { 
		
			$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);
			$scope.orderList = angular.copy($scope.list);
			
			RealData=$scope.list;
			////console.log($scope.list);
			if($scope.category_id != undefined || $scope.product_id != undefined )
				{
				$scope.selectProduct($scope.category_id);
				$scope.selectModel($scope.product_id);
				}
			$scope.totalItems = $scope.list.length;
			$localStorage.listData = $scope.list;
		})
	}
	$scope.initData();



	$scope.deleteVideo = function(id)
	{
		if(confirm(msg["P1004"])){
			dataService.getData({'video_id':id}, baseUrl + "delete/video/tutorial","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
					
					$scope.initData();
					}else{
					toaster.pop('error',response.result.msg);
				}
			});
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

		$scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage))
		{
			if($scope.category_id == undefined && $scope.product_id == undefined){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].video_id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "tutorial/order","POST").then(function(dataResponse) {
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

function addTutorialCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.catList = [];
	$scope.addProd = {};
	var getlink ;
	var separtedkey;
	$scope.urlData =[];
	$scope.showModel = true;
	$scope.showAdd =false;
	
	
	
	$scope.getCategory = function(){
	
		dataService.getData($scope.data, baseUrl + "tutorial/categories/products").then(function(dataResponse) {
			$scope.catList = dataResponse.data.data;
			
			////console.log($scope.catList);
		});
	}

	$scope.getCategory();
	
	$scope.selectProduct = function(id){
	$scope.showModel = false;
	
		if(id != undefined)
		{
			
			for(var i=0;i<$scope.catList.length;i++)
			{
				if(id == $scope.catList[i].id)
				{
					$scope.productList = $scope.catList[i].product;
					if($scope.productList.length == 0){
					$scope.showModel = true;
					toaster.pop('error','No sub-category in this category. Please add sub-catgeory in this category first');
					} 
				}
			
			}
		}
		else
		{	$scope.showModel = true;
			$scope.addProd.subcat_id = "";
		}
	
	}
	$scope.showDiv = function()
	{
		if($("#chck").prop('checked'))
		{	
			
			$scope.showAdd =true;
		}
		else
		{
			$scope.showAdd =false;
		}
	
	}
	function getParameterByName(name, url) {
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}
	$('#link').on('paste', function () {
		var that = this;
		setTimeout(function () {


			getlink = that.value;
			var name = 'v';

		var separtedkey = getParameterByName(name, getlink);
		
			//separtedkey = getlink.split("=")[1];
			$scope.addProd.youtube_url =that.value;
			
			$.ajax({
				url:"https://www.googleapis.com/youtube/v3/videos?id="+separtedkey+"&key=AIzaSyCcK-OvNb_D59P5dYkC1-q6GkCvnsbI5vE&part=snippet,contentDetails",
				method: "GET",
				crossDomain : true,
				dataType: "json",
				success: function(response)
				{
					$scope.urlData = response;					
					$scope.addProd.thumbnail = $scope.urlData.items[0].snippet.thumbnails.default.url;
					$scope.addProd.channel_name = $scope.urlData.items[0].snippet.channelTitle;
					$scope.imageUrl = response.items[0].snippet.thumbnails.default.url;
					////console.log($scope.imageUrl);
					$("#imgURL").attr("src",response.items[0].snippet.thumbnails.default.url)
					duration = $scope.urlData.items[0].contentDetails.duration;

					////console.log(duration);
					var string = "";

					var splitPT = duration.split("PT")[1];
					if(splitPT.search("H")>0)
					{
						var splitH = splitPT.split('H');
						string= splitH[0];
					}
					if(splitPT.search("M")>0)
					{
						var splitM = splitPT.split("M")[0];
						if(splitM.search("H"))
						{
							var getM=splitPT.substring(splitPT.lastIndexOf("H")+1,splitPT.lastIndexOf("M"));
						}
						else
						{
							getM = splitM;
						}
						if(string == "")
						string= getM;
						else
						string = string+":"+getM;

					}
					if(splitPT.search("S")>0  )
					{
						var splitSec = splitPT.split("S")[0];
						if(splitSec.search("M"))
						{
							var getS=splitPT.substring(splitPT.lastIndexOf("M")+1,splitPT.lastIndexOf("S"));
						}
						else
						{
							getS = splitSec;
						}
						if(string == "")
						string= getS;
						else
						string = string+":"+getS;
					}

					$scope.addProd.duration =string;

				}

			})

		}, 0);
		});
	$scope.addProduct = function(){
		
		var fd = new FormData();
		
		if($scope.addProd.file != undefined)
		{
		 fd.append('file', $scope.addProd.file );
		}
		
		 fd.append('title', $scope.addProd.title );
		 if($scope.addProd.short_description != undefined)
		{
		 fd.append('short_description', $scope.addProd.short_description );
		}
		 
		 fd.append('category_id', $scope.addProd.category_id );
		 if($scope.addProd.subcat_id != undefined)
		 {
		 fd.append('subcat_id', $scope.addProd.subcat_id );
		 }
		 if($scope.addProd.subcat_name != undefined)
		 {
		 fd.append('subcat_name', $scope.addProd.subcat_name );
		 }
		 if($scope.addProd.youtube_url != undefined)
		{
		 fd.append('youtube_url', $scope.addProd.youtube_url );
		}
		if($scope.addProd.thumbnail != undefined)
		{
		 fd.append('thumbnail', $scope.addProd.thumbnail );
		}
		if($scope.addProd.channel_name != undefined)
		{
		 fd.append('channel_name', $scope.addProd.channel_name );
		}		
		if($scope.addProd.duration != undefined)
		{
		 fd.append('duration', $scope.addProd.duration );
		}
		
		
		
	
		
		 fd.append('status', $scope.addProd.status );
		 ////console.log($scope.addProd)
		
		$http.post(baseUrl+"add/video/tutorial",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
			.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$location.path('/tutorial/manage-tutorial');
			}else{
				toaster.pop('error',response.result.msg);
			}
		}) 
	}
	
	
	
}


function editTutorialCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.catList = [];
	$scope.editProd = [];
	var getlink ;
	var separtedkey;
	$scope.urlData =[];
	$scope.id = $routeParams.id
	$scope.showModel = true;
	$scope.showAdd =false;
	$scope.productList=[];
	
	
		dataService.getData($scope.data, baseUrl + "tutorial/categories/products").then(function(dataResponse) {
			$scope.catList = dataResponse.data.data;
			
			$http.get(baseUrl+"video/tutorial/"+$scope.id)
			.success(function(response){
				if(response.status=="success")
				{
					$scope.editProd = response.data[0];
					//console.log($scope.editProd.subcat_id);
					$scope.imageUrl = $scope.editProd.thumbnail;
					//console.log($scope.editProd.category_id)
					$scope.selectProductCat($scope.editProd.category_id);
					//console.log($scope.productList);
				}else{
				toaster.pop('error',response.result.msg);
			}
			})
			
		});

	 
	$scope.showDiv = function()
	{
		if($("#chck").prop('checked'))
		{	
			
			$scope.showAdd =true;
		}
		else
		{
			$scope.showAdd =false;
		}
	
	}
	/* $scope.editvideoFunct= function()
	{ */
	$scope.selectProductCat = function(id) {
	
		$scope.showModelEdit = false;
		
		for(var i=0;i<$scope.catList.length;i++)
			{	
				//console.log($scope.catList[i].id)
				if(id == $scope.catList[i].id)
				{	
					
					//console.log(id == $scope.catList[i].id)
					$scope.productList = $scope.catList[i].product;
					////console.log($scope.productList)
					////console.log($scope.editProd.subcat_id)
					 
					if($scope.productList.length == 0){
					$scope.showModelEdit = true;
					break;
					//toaster.pop('error','No product in this category. Please add product in this category first');
					} 
				/* 	$scope.form = {type : $scope.productList[0].id}; */ 
				}
			
			}

	}
	
			
	function getParameterByName(name, url) {
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}
	
	
	
	$('#link').on('paste', function () {
		var that = this;
		setTimeout(function () {


			getlink = that.value
			
		var name = 'v';

		var separtedkey = getParameterByName(name, getlink);
		
			$scope.editProd.youtube_url =that.value;
			
			$.ajax({
				url:"https://www.googleapis.com/youtube/v3/videos?id="+separtedkey+"&key=AIzaSyCcK-OvNb_D59P5dYkC1-q6GkCvnsbI5vE&part=snippet,contentDetails",
				method: "GET",
				crossDomain : true,
				dataType: "json",
				success: function(response)
				{
					$scope.urlData = response;					
					$scope.editProd.thumbnail = $scope.urlData.items[0].snippet.thumbnails.default.url;
					$scope.editProd.channel_name = $scope.urlData.items[0].snippet.channelTitle;
					$scope.imageUrl = response.items[0].snippet.thumbnails.default.url;
					////console.log($scope.imageUrl);
					$("#imgURL").attr("src",response.items[0].snippet.thumbnails.default.url)
					duration = $scope.urlData.items[0].contentDetails.duration;

					////console.log(duration);
					var string = "";

					var splitPT = duration.split("PT")[1];
					if(splitPT.search("H")>0)
					{
						var splitH = splitPT.split('H');
						string= splitH[0];
					}
					if(splitPT.search("M")>0)
					{
						var splitM = splitPT.split("M")[0];
						if(splitM.search("H"))
						{
							var getM=splitPT.substring(splitPT.lastIndexOf("H")+1,splitPT.lastIndexOf("M"));
						}
						else
						{
							getM = splitM;
						}
						if(string == "")
						string= getM;
						else
						string = string+":"+getM;

					}
					if(splitPT.search("S")>0  )
					{
						var splitSec = splitPT.split("S")[0];
						if(splitSec.search("M"))
						{
							var getS=splitPT.substring(splitPT.lastIndexOf("M")+1,splitPT.lastIndexOf("S"));
						}
						else
						{
							getS = splitSec;
						}
						if(string == "")
						string= getS;
						else
						string = string+":"+getS;
					}

					$scope.editProd.duration =string;

				}

			})

		}, 0);
		});
	$scope.editTutorialData = function(){
		
		var fd = new FormData();

		 
		if($scope.editProd.file != undefined)
		{
		 fd.append('file', $scope.editProd.file );
		}
		fd.append('video_id',$scope.id);
		 fd.append('title', $scope.editProd.title );
		 if($scope.editProd.short_description != undefined || $scope.editProd.short_description == "null")
		 {
		 fd.append('short_description', $scope.editProd.short_description );
		 }
		 fd.append('category_id', $scope.editProd.category_id );
		 if($scope.editProd.subcat_id != undefined  || $scope.editProd.subcat_id == "null")
		 {
		 fd.append('subcat_id', $scope.editProd.subcat_id );
		 }
		 if($scope.editProd.subcat_name != undefined || $scope.editProd.subcat_name == "null")
		 {
		 fd.append('subcat_name', $scope.editProd.subcat_name );
		 }
		 
		
		if($scope.editProd.youtube_url != undefined || $scope.editProd.youtube_url == "null")
		{
		 fd.append('youtube_url', $scope.editProd.youtube_url );
		}
		
		
		if($scope.editProd.thumbnail != undefined || $scope.editProd.thumbnail == "null")
		{
		 fd.append('thumbnail', $scope.editProd.thumbnail );
		}
		if($scope.editProd.channel_name != undefined || $scope.editProd.channel_name == "null")
		{
		 fd.append('channel_name', $scope.editProd.channel_name );
		}
		
		if($scope.editProd.duration != undefined || $scope.editProd.duration == "null")
		{
		 fd.append('duration', $scope.editProd.duration );
		}
	
		
		 fd.append('status', $scope.editProd.status );
		 ////console.log($scope.editProd)
		
		$http.post(baseUrl+"edit/video/tutorial",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
			.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$location.path('/tutorial/manage-tutorial');
			}else{
				toaster.pop('error',response.result.msg);
			}
		}) 
	}
	
	
	
}
function managesubcategoryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams) {

	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	var subcatId;
	var realData= []; 
	var filterData = [];
	$scope.orderList = [];	
	
		dataService.getData($scope.data, baseUrl + "tutorial/categories/products").then(function(dataResponse) {
			$scope.catList = dataResponse.data.data;
			//console.log($scope.catList);
		});
	$scope.getUpdatedData=function(data){
		$scope.list=data;
		$scope.totalItems = $scope.list.length;
	}
	
	$scope.subCategory =  function()
	{
		$http.get(baseUrl+"list/tutorial/subcat")
		.success(function(response){
			if(response.status == 'success')
			{	
				$scope.list = response.data;
				$scope.orderList = angular.copy(response.data);
				realData = $scope.list;
				$scope.totalItems = $scope.list.length;
				if($scope.category_id != undefined)
				{
					$scope.filterList($scope.category_id);
				}
			}
			else
			{
				toaster.pop("error",response.result.msg);
			}
		})
	
	}
	$scope.subCategory();
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
			$scope.list = filterData;
				$scope.orderList = angular.copy($scope.list);
			$scope.totalItems = $scope.list.length;
		}
		else
		{
			$scope.list = realData;
			$scope.totalItems = $scope.list.length;
			
		}
	}
	$scope.submitAddsubCat = function()
	{
		$http.post(baseUrl+"add/tutorial/subcat",$scope.addsubCat)
		.success(function(response){
			if(response.status == 'success')
			{	
				$("#cancel").click();
				toaster.pop('success',response.msg);
				$scope.addsubCat = {};
				$scope.subCategory();
				
			}
			else
			{
				toaster.pop("error",response.result.msg);
			}
		})
	}
	$scope.editSubcat= function(id)
	{	
	subcatId = id;
		$http.get(baseUrl+"edit/tutorial/subcat/"+id)
		.success(function(response){
			if(response.status == 'success')
			{	
				$scope.editsubData = response.data;
				
			}
			else
			{
				toaster.pop("error",response.result.msg);
			}
		})
	
	}
	$scope.submitEditData  = function()
	{
		$http.post(baseUrl+"edit/tutorial/subcat/"+subcatId,$scope.editsubData)
		.success(function(response){
			if(response.status == 'success')
			{	
				toaster.pop("success",response.msg);
				$("#close").click();
				$scope.subCategory();
			}
			else
			{
				toaster.pop("error",response.result.msg);
			}
		})
	}
	$scope.deleteSubcat = function(id)
	{	
		if(confirm("Are you sure to delete this Sub-Category ?"))
		$http.post(baseUrl+"delete/tutorial/subcat",{'id':id})
		.success(function(response){
			if(response.status == 'success')
			{	
				toaster.pop("success",response.msg)
				$scope.subCategory();
			}
			else
			{
				toaster.pop("error",response.result.msg);
			}
		})
	
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

		$scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.subCategory();
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
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "tutorial/subcat/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.sort($scope.sortKey);
				
				$scope.subCategory();
				
				
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
}
angular
	.module('cubeWebApp')
	.controller('manageTutorialCtrl', manageTutorialCtrl)
	.controller('addTutorialCtrl', addTutorialCtrl)
	.controller('editTutorialCtrl', editTutorialCtrl)
	.controller('managesubcategoryCtrl', managesubcategoryCtrl)
	
	
