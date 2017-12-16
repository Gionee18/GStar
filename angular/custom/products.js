
function manageProductsCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	
	$scope.list = [];
	$scope.orderList = [];
	$scope.realArray = [];
	$scope.selectCategory = "";
	
	$scope.getUpdatedData=function(data){
		$scope.list=data;
		$scope.totalItems = $scope.list.length;
	}
	$scope.setCategory = function(orderList){
		var categoryArray = [];
		
		for(var i=0; i<orderList.length; i++){
			categoryArray[orderList[i].category_id] = orderList[i].category_name;
		}
		var categoryListArray = [];
		for(var i=0; i < categoryArray.length; i++){
			if(categoryArray[i])
			categoryListArray.push({'category_id':i,'category_name':categoryArray[i]});
		}
		$scope.categoryListArray = categoryListArray;
	}
	
	$scope.ddlCategory = function(){
		var realList = $scope.realArray;
		$scope.list = [];
		if($scope.selectCategory == ""){
			$scope.list = realList;
		}else{
			for(var i=0; i < realList.length; i++){
				if(realList[i].category_id == $scope.selectCategory){
					$scope.list.push(realList[i]);
				}
			}
		}
		$scope.orderList = $scope.list;
		$scope.totalItems = $scope.list.length;
	}
	
	$scope.initData = function()
	{		
		//$scope.data="";
		 dataService.getData($scope.data, baseUrl + "products?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) { 
		//dataService.getData($scope.data, baseUrl + "products"  ).then(function(dataResponse) {
			$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);
			$scope.realArray = dataResponse.data.data;
			$scope.orderList = dataResponse.data.data;
			$scope.setCategory($scope.realArray);
			$scope.totalItems = $scope.list.length;
			$scope.sort($scope.sortKey);
		});
	}
	$scope.initData();
	
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

		/* $scope.list = $scope.list.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		}); */
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
	
	$scope.deleteProd = function(id){
		
		if(confirm(msg["P1001"])){
			dataService.getData({'id':id}, baseUrl + "product/delete","POST").then(function(dataResponse) {
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
					
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
	}
	
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "product/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.initData();$scope.sort($scope.sortKey);$scope.ddlCategory();
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
}

function addProductCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$(function () {
		$("#from").datepicker({
			changeMonth: true,
			changeYear: true,
			
			dateFormat : 'dd-mm-yy',
		
		});

	});
	$scope.getContent = function() {
		////console.log('Editor content:', $scope.tinymceModel);
		};

	  $scope.setContent = function() {
		$scope.tinymceModel = 'Time: ' + (new Date());
	  };

	  $scope.tinymceOptions = {
		selector: "textarea", 
		plugins: 'code table image advlist',
		advlist_bullet_styles: "square",
				
	  };
	$scope.addProduct = function(){
		dataService.getData($scope.addProd, baseUrl + "product/add","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$location.path("/products/edit-product/"+response.product_id);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
	$scope.getCategory = function(){
	
		dataService.getData($scope.data, baseUrl + "product/categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
		});
	}
	$scope.getCategory();
	
}


function editProductCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,arrayPushService,$localStorage) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.baseUrl = baseUrl;
	$scope.id=$routeParams.id;
	$(function () {
		$("#from").datepicker({
			changeMonth: true,
			changeYear: true,
		
			dateFormat : 'dd-mm-yy',
		
		});

	});
	
	$scope.clearData = function()
	{
	$scope.uploadImageName ="";
	document.getElementById("productimg").reset();
	}
	$scope.productdocFunct= function()
	{
	$scope.uploadDocName ="";
	document.getElementById("productdoc").reset();	
	}
	
	
	
	$scope.editProduct = function(){
		$scope.editProd.product_id = $routeParams.id;
		dataService.getData($scope.editProd, baseUrl + "product/update","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	  $scope.getContent = function() {
		////console.log('Editor content:', $scope.tinymceModel);
		};

	  $scope.setContent = function() {
		$scope.tinymceModel = 'Time: ' + (new Date());
	  };

	  $scope.tinymceOptions = {
		selector: "textarea", 
		plugins: 'code table image advlist',
		advlist_bullet_styles: "square",
				
	  };
	$scope.getThisProduct = function(){
		dataService.getData($scope.data, baseUrl + "product/edit/"+$routeParams.id).then(function(dataResponse) {
			$scope.editProd = dataResponse.data.data;
			$scope.editProd.category_id = Number(dataResponse.data.data.category_id);
			$scope.editProd.status = Number(dataResponse.data.data.status);
			$scope.editProd.overview = dataResponse.data.data.desc1;
			$scope.editProd.specification = dataResponse.data.data.desc3;
			$scope.editProd.new_product = dataResponse.data.data.new_product_flag;
			$localStorage.product_name = dataResponse.data.data.product_name;
			$localStorage.category_name = dataResponse.data.data.category_name;
			////console.log($localStorage.product_name + "defv"+$localStorage.category_name)
		});
	}
	
	
	$scope.getCategory = function(){
		dataService.getData($scope.data, baseUrl + "product/categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
			$scope.getThisProduct();
		});
	}
	$scope.getCategory();
	
	
	// Gallery
	$scope.list = [];
	$scope.getGallery = function(){
		dataService.getData($scope.data, baseUrl + "assets/images").then(function(dataResponse) {
			$scope.gallaryImages = dataResponse.data.data;
			$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);
			$scope.totalItems = $scope.list.length;
			
			$scope.DisplayImages();
		});
	}
	$scope.getGallery();
	
	$scope.getDocs = function(){
		dataService.getData($scope.data, baseUrl + "assets/documents").then(function(dataResponse) {
			$scope.gallaryDocs = dataResponse.data.data;
			$scope.totalItems1 = $scope.gallaryDocs.length;
			$scope.DisplayDocs();
		});
	}
	$scope.getDocs();
	$scope.itemsPerPage = 8;
	$scope.currentPage = 1;
	$scope.maxSize = 8;
	$scope.totalItems = 0;
	$scope.totalItems1 = 0;

	$scope.setImage = function(id){
		$scope.gallaryImgId = id;
	}
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil( $scope.list.length / $scope.itemsPerPage))
		{
			$scope.getGallery();
		}
	};
	$scope.pageChanged1 = function() {
		if($scope.currentPage == Math.ceil( $scope.gallaryDocs.length / $scope.itemsPerPage))
		{
			$scope.getDocs();
		}
	};
	$('#add_new_docmnt').on('hide.bs.modal', function (e) {
		$scope.docFileId = 0;
		$scope.uploadDocName = "";
		$scope.sysUpload = {};$("#doc").val("");
	});
	$('#add_new_cat').on('hide.bs.modal', function (e) {
		$scope.gallaryImgId = 0;
		$scope.uploadImageName = "";
		$scope.sysUpload = {};$("#ig").val("");
	});
	$scope.submitGallaryImage = function(){
		if($scope.gallaryImgId==undefined || $scope.gallaryImgId==0)
		{
			alert('please select a image');
			return false;			
		}
		dataService.getData({'asset_library_id':$scope.gallaryImgId,'module':'product','module_id':$routeParams.id}, baseUrl + "assets/attachImage","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$(".cancel").click();
				$scope.DisplayImages();
				$scope.gallaryImgId = 0;
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
                alert(msg['L1004']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if($.inArray( ext, imagesFormates ) > -1)
			{
				
			}else{
				alert(msg['L1005']);
				return false;
			}
        }else{
			alert(msg['L1007']);
            return false;
		}
		
		var fd = new FormData();
		 fd.append('file', $scope.sysUpload.imageFile );
		 fd.append('title', $scope.sysUpload.title );
		 fd.append('module', 'product' );
		
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
			toaster.pop('error', msg['2002']);
		});
	}
	
	$scope.deleteImage = function(id){
		
		if(confirm(msg['P1002'])){
			dataService.getData({'mapping_id':id, 'module':'product'}, baseUrl + "image/delete","POST").then(function(dataResponse) {
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
	
	$scope.DisplayImages = function(){
		dataService.getData({'module':'product','module_id':$routeParams.id}, baseUrl + "assets/moduleImages","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				$scope.thisCatImages = response.data;
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	$scope.DisplayImages();
	
	$scope.submitSystemDoc = function(){
		
		
		var input = document.getElementById("doc");
        if(input.files && input.files.length == 1){           
            if (input.files[0].size > 20*1024*1024){
                alert(msg['L1014']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if($.inArray( ext, documentsFormates ) > -1)
			{
				
			}else{
				alert(msg['L1006']);
				return false;
			}
        }else{
			alert(msg['L1008']);
            return false;
		}
		
		var fd = new FormData();
		 fd.append('file', $scope.sysUpload.docFile );
		 fd.append('title', $scope.sysUpload.docTitle );
		 fd.append('module', 'product' );
		
		$http.post(baseUrl + "library/uploadFile",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
				$(".cancel").click();
				$scope.docFileId = response.asset_id;
				$scope.submitLibraryDoc();
			}else{
				toaster.pop('error',response.result.msg);
			}
		}).error(function(){
			toaster.pop('error', msg['2002']);
		});
	}
	
	$scope.DisplayDocs = function(){
		dataService.getData({'module':'product','module_id':$routeParams.id}, baseUrl + "assets/moduleDocuments","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				$scope.thisCatDocs = response.data;
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	$scope.DisplayDocs();
	
	$scope.setDoc = function(id){
		$scope.docFileId = id;
	}
	
	$scope.submitLibraryDoc = function(){
		if($scope.docFileId==undefined || $scope.docFileId==0)
		{
			alert('please select a file');
			return false;			
		}
		dataService.getData({'asset_library_id':$scope.docFileId,'module':'product','module_id':$routeParams.id}, baseUrl + "assets/attachDocument","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$(".cancel").click();
				$scope.DisplayDocs();
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	
	$scope.deleteDocs = function(id){
		
		if(confirm(msg['P1003'])){
			dataService.getData({'mapping_id':id, 'module':'product'}, baseUrl + "image/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
					$scope.DisplayDocs();
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
	}
	
	$scope.$watch('sysUpload.docFile', function(newValue, oldValue){
		if(newValue){
			$scope.uploadDocName = newValue.name
		}
	});
	
	$scope.$watch('sysUpload.imageFile', function(newValue, oldValue){
		if(newValue){
			$scope.uploadImageName = newValue.name
		}
	});
	
	
	
	
	
}



angular
	.module('cubeWebApp')
	.controller('manageProductsCtrl', manageProductsCtrl)
	.controller('addProductCtrl', addProductCtrl)
	.controller('editProductCtrl', editProductCtrl)
	
