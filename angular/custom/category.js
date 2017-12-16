
function manageCategoryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService,$localStorage) {
	
	var realData = [];
	var filterData = [];
	if($localStorage.userData == 'null')
	{
		setTimeout(function(){ 
						window.location = siteUrl+"login.html";
					}, 100);
	}
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	
	$scope.reverse = true;
	$scope.sortKey = 'position';
	$scope.list = [];
	
	
	$scope.getUpdatedData=function(data){
	$scope.list=data;
	$scope.totalItems = $scope.list.length;
	}
	$scope.initData = function(){
		//dataService.getData($scope.data, baseUrl + "categories?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) {
		dataService.getData($scope.data, baseUrl + "categories"  ).then(function(dataResponse) {
			//$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);
			$scope.list = dataResponse.data.data;
			realData = dataResponse.data.data;
			$scope.totalItems = $scope.list.length;
			$scope.sort($scope.sortKey);
			if($scope.selectProduct != undefined)
			{
				$scope.productFilter($scope.selectProduct);
			}
		});
	}
	$scope.initData();
	$scope.productFilter = function()
	{	
		filterData = [];
		if($scope.selectProduct == 1)
		{
			for(var i=0;i<realData.length;i++)
			{
				if(realData[i].is_product == 1 && realData[i].is_tutorial != 1)
				{
					filterData.push(realData[i]);
				}
			}
			$scope.list = filterData;
		}
		else if($scope.selectProduct == 2)
		{
			for(var i=0;i<realData.length;i++)
			{
				if(realData[i].is_tutorial == 1 && realData[i].is_product != 1)
				{
					filterData.push(realData[i]);
				}
			}
			$scope.list = filterData;
		}
		else if($scope.selectProduct == 3)
		{
			for(var i=0;i<realData.length;i++)
			{
				if(realData[i].is_tutorial == 1 && realData[i].is_product == 1)
				{
					filterData.push(realData[i]);
				}
			}
			$scope.list = filterData;
		}
		else
		{
			$scope.list =realData;
		}
	}
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.gallaryDocs.length / $scope.itemsPerPage);
	};	
	
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.list.length / $scope.itemsPerPage))
		{
			if( (!$scope.search) || (!$scope.search && $scope.search == "") ){
				$scope.initData();$scope.sort($scope.sortKey);
			}
		}
	};
	
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		/* $scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		}) */;
    }
	
	
	$scope.deleteCat = function(id){
		if(confirm(msg["C1001"])){
			dataService.getData({'id':id}, baseUrl + "category/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
					var tmp = [];
					for(var i=0;i<$scope.list.length;i++){
						if($scope.list[i].id != id){
							tmp.push($scope.list[i]);
						}
					}
					$scope.list = tmp;
				}else if(response.status == 'fail'){
					toaster.pop('info',response.msg);
					var data = response.data;
					var tmp = [];
					for(var i=0;i<data.length;i++){
						tmp.push(data[i].productname);
					}
					$scope.messageHead = response.msg;
					$scope.message = tmp.join(",");
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
			
		}
	}
	
	
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.list.length;i++){
			var thisObj = {id:$scope.list[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "category/order","POST").then(function(dataResponse) {
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

function addCategoryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.categoryId = "";
	
	$scope.addCategory = function(){
	
	 
		dataService.getData($scope.addCat, baseUrl + "category/add","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$scope.categoryId = response.cat_id;
				$location.path("/edit-category/" + $scope.categoryId);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
	
	$scope.getCategory = function(){
		dataService.getData($scope.data, baseUrl + "categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
		});
	}
	$scope.getCategory();

}

function editCategoryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.sysUpload = {};
	$scope.baseUrl = baseUrl;
	$scope.clearData = function()
	{
	$scope.uploadImageName ="";
	document.getElementById("myFormImg").reset();
	}
	
	
	
	$scope.getThisCategory = function(){
		dataService.getData($scope.data, baseUrl + "category/edit/" + $routeParams.id).then(function(dataResponse) {
			$scope.editCat = dataResponse.data.data;
			$scope.editCat.parent_category = dataResponse.data.data.category_parent_id;
			$scope.editCat.status = Number( dataResponse.data.data.status );
			////console.log($scope.editCat.status);
			$("#parentCategory").val(dataResponse.data.data.category_parent_id);
		});
	}
	
	$scope.submitEditCategory = function(){
		$scope.editCat.cat_id = $routeParams.id;
		
		dataService.getData($scope.editCat, baseUrl + "category/update","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
		
	}
	/* $('#add_new_cat').on('hide.bs.modal', function (e) {
		$scope.gallaryImgId = 0;
	}); */
	$('#add_new_cat').on('hide.bs.modal', function (e) {
		$scope.gallaryImgId = 0;
		$scope.uploadImageName = "";
		$scope.sysUpload = {};$("#ig").val("");
	});
	
	$scope.getCategory = function(){
		dataService.getData($scope.data, baseUrl + "categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
			$scope.getThisCategory();
		});
	}
	$scope.getCategory();
	
	
	
	
	// Gallery
	$scope.getGallery = function(){
		dataService.getData($scope.data, baseUrl + "assets/images").then(function(dataResponse) {
			$scope.gallaryImages = dataResponse.data.data;
			$scope.totalItems = $scope.gallaryImages.length;
			
		});
	}
	$scope.getGallery();
	$scope.itemsPerPage = 8;
	$scope.currentPage = 1;
	$scope.maxSize = 8;
	$scope.totalItems = 0;
	$scope.setImage = function(id){
		$scope.gallaryImgId = id;
		$scope.selectedImage=id;
	}
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil( $scope.gallaryImages.length / $scope.itemsPerPage))
		{
			$scope.getGallery();
		}
	};
	$scope.submitGallaryImage = function(){
		
		if($scope.gallaryImgId==undefined || $scope.gallaryImgId==0)
		{
			alert('please select a image');
			return false;			
		}
		dataService.getData({'asset_library_id':$scope.gallaryImgId,'module':'category','module_id':$routeParams.id}, baseUrl + "assets/attachImage","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$(".cancel").click();
				$scope.DisplayImages();
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
		
	}
	
	$scope.submitSystemImage = function(){
		
		var input = document.getElementById("ig");
        if(input.files && input.files.length == 1)
        {           
            if (input.files[0].size > 5*1024*1024) 
            {
                alert(msg["L1004"]);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if($.inArray( ext, imagesFormates ) > -1)
			{
				
			}else{
				alert(msg["L1005"]);
				return false;
			}
        }else{
			alert(msg["L1007"]);
            return false;
		}
		
		var fd = new FormData();
		 fd.append('file', $scope.sysUpload.imageFile );
		 fd.append('title', $scope.sysUpload.title );
		 fd.append('module', 'category' );
		
		$http.post(baseUrl + "library/uploadFile",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
				$(".cancel").click();
				$scope.gallaryImgId = response.asset_id;
				$scope.submitGallaryImage();
			}else{
				toaster.pop('error',response.result.msg);
			}
			
		}).error(function(){
			toaster.pop('error', msg['0000']);
		});
	}
	
	$scope.deleteImage = function(id){
			
		if(confirm(msg['C1002'])){
			dataService.getData({'mapping_id':id, 'module':'category'}, baseUrl + "image/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
					$scope.DisplayImages();
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
			
		}
	}
	
	// DisplayImages
	
	$scope.DisplayImages = function(){
		dataService.getData({'module':'category','module_id':$routeParams.id}, baseUrl + "assets/moduleImages","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				$scope.thisCatImages = response.data;
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
		
	}
	$scope.DisplayImages();
	
	$scope.$watch('sysUpload.imageFile', function(newValue, oldValue){
		if(newValue){
			$scope.uploadImageName = newValue.name
		}
	});
}

angular
	.module('cubeWebApp')
	.controller('manageCategoryCtrl', manageCategoryCtrl)
	.controller('addCategoryCtrl', addCategoryCtrl)