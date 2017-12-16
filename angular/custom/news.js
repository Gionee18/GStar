
function newsCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	
	$scope.list = [];
	$scope.orderList = [];
	$scope.realArray = [];
	$scope.selectCategory = "";
	$scope.getSearchData = function(){
		var str='';
		if($scope.search){
		str = '?search_keyword='+$scope.search;
		}else{
		str = "";
		}
		dataService.getData($scope.data,baseUrl + "news"+ str ).then(function(dataResponse) {
			$scope.list = dataResponse.data.data;
			$scope.totalItems = $scope.list.length;
		});
	}
	
	 $scope.setProduct = function(orderList){
		var productArray = [];
		
		for(var i=0; i<orderList.length; i++){
			productArray[orderList[i].product_id] = orderList[i].product_name;
		}
		var productListArray = [];
		for(var i=0; i < productArray.length; i++){
			if(productArray[i])
			productListArray.push({'product_id':i,'product_name':productArray[i]});
		}
		$scope.productListArray = productListArray;
	}
	
	$scope.ddlProduct = function(){
		var realList = $scope.realArray;
		$scope.list = [];
		if($scope.selectProduct == ""){
			$scope.list = realList;
		}else{
			for(var i=0; i < realList.length; i++){
				if(realList[i].product_id == $scope.selectProduct){
					$scope.list.push(realList[i]);
				}
			}
		}
		$scope.orderList = $scope.list;
		$scope.totalItems = $scope.list.length;
	} 
	
	$scope.initData = function()
	{		
		$scope.data="";
		dataService.getData($scope.data, baseUrl + "news?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) {
			$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);
			$scope.realArray = arrayPushService.arrayPush(dataResponse.data.data, $scope.realArray);
			$scope.orderList = arrayPushService.arrayPush(dataResponse.data.data, $scope.orderList);
			$scope.setProduct($scope.realArray);
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
	
	$scope.deleteNews = function(id){
		
		if(confirm(msg["N1001"])){
			dataService.getData({'id':id}, baseUrl + "news/delete","POST").then(function(dataResponse) {
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
	
	
	
}

function addNewsCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.getContent = function() {
		////console.log('Editor content:', $scope.tinymceModel);
		};

	  $scope.setContent = function() {
		$scope.tinymceModel = 'Time: ' + (new Date());
	  };

	  $scope.tinymceOptions = {
		plugins: 'code table',
		toolbar: 'fontsizeselect | undo redo | bold italic | alignleft aligncenter alignright | code'
	  };
	$scope.addProductNews = function(){
		////console.log($scope.addNews);
		dataService.getData($scope.addNews, baseUrl + "news/add","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			////console.log(response);
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$location.path("/admin/edit-news/"+response.news_id);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
	$scope.getCategory = function(){
		dataService.getData($scope.data, baseUrl + "categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
			
			parentProducts = $scope.dataParentProducts;
			
		});
	}
	$scope.getCategory();
	var parentProducts =[];
	$scope.getProducts = function(id){
		$scope.getDataProducts = [];
			var getProductName={
				product_id : 0,
				product_name : '',
			};
		dataService.getData($scope.data, baseUrl + "products").then(function(dataResponse) {
			$scope.dataParentProducts = dataResponse.data.data;
			parentProducts = $scope.dataParentProducts;
			//////console.log(parentProducts);
			
			for(var i=0;i<parentProducts.length;i++)
			{
				
				if(id == parentProducts[i].category_id)
				{
					getProductName.product_id= parentProducts[i].id;
					getProductName.product_name= parentProducts[i].product_name;
				}
				
				
			}
			$scope.getDataProducts.push(getProductName);
		
		});
	}
	$scope.getProducts();
	
	
}


function editNewsCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage,arrayPushService) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.CatStatus = CatStatus;
	$scope.gallaryImgId = 0;
	$scope.sysUpload = {};
	$scope.baseUrl = baseUrl;
	
	$scope.editProductNews = function(){
		
		dataService.getData($scope.editNews, baseUrl + "news/update","POST").then(function(dataResponse) {
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
		plugins: 'code table',
		toolbar: 'fontsizeselect | undo redo | bold italic | alignleft aligncenter alignright | code'
	};
	$scope.getThisProduct = function(){
		dataService.getData($scope.data, baseUrl + "news/edit/"+$routeParams.id).then(function(dataResponse) {
			$scope.editNews = dataResponse.data.data;
			
			 $scope.editNews.category_id = Number(dataResponse.data.data.category_id);
			 $localStorage.id =  $scope.editNews.product_id;
			// ////console.log( $localStorage.id);
			$scope.editNews.status = Number(dataResponse.data.data.status);
			$scope.editNews.overview = dataResponse.data.data.desc1;
			$scope.editNews.specification = dataResponse.data.data.desc3;
			$scope.editNews.new_product = dataResponse.data.data.new_product_flag; 
			$scope.getCategory();
			$scope.getProducts($scope.editNews.category_id);
		});
	}
	
	var parentProducts =[];
	$scope.getCategory = function(){
		dataService.getData($scope.data, baseUrl + "categories").then(function(dataResponse) {
			$scope.dataParentCat = dataResponse.data.data;
			
			parentProducts = $scope.dataParentProducts;
			//$scope.getThisProduct();
		});
	}
	
	
	$scope.getProducts = function(id){
		$scope.getDataProducts = [];
			var getProductName={
				product_id : 0,
				product_name : '',
			};
		dataService.getData($scope.data, baseUrl + "products").then(function(dataResponse) {
			$scope.dataParentProducts = dataResponse.data.data;
			parentProducts = $scope.dataParentProducts;
			//////console.log(parentProducts);
			
			for(var i=0;i<parentProducts.length;i++)
			{
				
				if(id == parentProducts[i].category_id)
				{
					getProductName.product_id= parentProducts[i].id;
					getProductName.product_name= parentProducts[i].product_name;
				}
				
				
			}
			$scope.getDataProducts.push(getProductName);
			$scope.getDataProducts;
		});
	}
	
	$scope.getThisProduct();
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
		dataService.getData({'asset_library_id':$scope.gallaryImgId,'module':'news','module_id':$routeParams.id}, baseUrl + "assets/attachImage","POST").then(function(dataResponse) {
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
		
		if(confirm(msg['N1002'])){
			dataService.getData({'mapping_id':id}, baseUrl + "image/delete","POST").then(function(dataResponse) {
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
		dataService.getData({'module':'news','module_id':$routeParams.id}, baseUrl + "assets/moduleImages","POST").then(function(dataResponse) {
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
			/* var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if($.inArray( ext, documentsFormates  ) > -1)
			{
				
			}else{
				alert(msg['L1006']);
				return false;
			} */
        }else{
			alert(msg['L1008']);
            return false;
		}
		
		var fd = new FormData();
		 fd.append('file', $scope.sysUpload.docFile );
		 fd.append('title', $scope.sysUpload.docTitle );
		
		$http.post(baseUrl + "library/uploadNewsdoc",fd, {
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
		
		dataService.getData({'module':'news','module_id': $routeParams.id}, baseUrl + "assets/moduleDocuments","POST").then(function(dataResponse) {
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
		dataService.getData({'asset_library_id':$scope.docFileId,'module':'news','module_id':$routeParams.id}, baseUrl + "assets/attachDocument","POST").then(function(dataResponse) {
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
		
		if(confirm(msg['N1003'])){
			dataService.getData({'mapping_id':id}, baseUrl + "image/delete","POST").then(function(dataResponse) {
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
	.controller('newsCtrl', newsCtrl)
	.controller('addNewsCtrl', addNewsCtrl)
	.controller('editNewsCtrl', editNewsCtrl)
	
