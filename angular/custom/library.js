function imageLibraryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,arrayPushService) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.sysUpload = {};
	$scope.submitBtn = true;
	$scope.gallaryImages = [];
	$scope.hideButton = false;
	
	
	$scope.baseUrl = baseUrl;
	$scope.getGallery = function(){
		dataService.getData($scope.data, baseUrl + "assets/images?page_no=" + Math.ceil(Number(($scope.currentPage/$scope.itemsPerPage)+1)) ).then(function(dataResponse) {
			$scope.gallaryImages = arrayPushService.arrayPush(dataResponse.data.data, $scope.gallaryImages);
			$scope.totalItems = $scope.gallaryImages.length;
			$scope.hideButton = false;
			if($scope.gallaryImages.length > 0)
			{
			$scope.hideButton =true;
			}
		});
	}
	$scope.getGallery();
	
	$scope.searchGallery = function(){
		var str='';
		if($scope.search){
		str = '?search_keyword='+$scope.search;
		}else{
		str = "";
		}
		dataService.getData($scope.data, baseUrl + "assets/images"+str).then(function(dataResponse) {
			$scope.gallaryImages = dataResponse.data.data;
			$scope.totalItems = $scope.gallaryImages.length;
		});
	}
	
	
	$scope.itemsPerPage = 12;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.totalItems = 0;
	
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.gallaryImages.length / $scope.itemsPerPage))
		{
			$scope.getGallery();
		}
	};
	$scope.deleteImage = function(id){
		 if(confirm(msg['L1002'])){
			dataService.getData({'asset_id':id}, baseUrl + "libraryDocument/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
						
						
					var tmpList = $scope.gallaryImages;
					var tmp = [];
					for(var i=0;i<tmpList.length;i++){
						if(tmpList[i].id != id){
							tmp.push(tmpList[i]);
						}
					}
					$scope.gallaryImages = tmp;
					
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
	}
	
	$scope.setEditImage = function(id){
		$scope.sysUpload.file = "";
		$("input[type='file']").val("");
		$scope.submitBtn = true;
		for(var i=0;i<$scope.gallaryImages.length;i++){
			if($scope.gallaryImages[i].id == id){
				$scope.sysUpload.id = id;
				$scope.sysUpload.name = $scope.gallaryImages[i].name;
				$scope.sysUpload.path = $scope.gallaryImages[i].path;
				$scope.sysUpload.title = $scope.gallaryImages[i].title;
			}
		}
	}
	
	function readURL2(input){
		if (input) {
		var reader = new FileReader();

		reader.onload = function (e) {
		$('#blah').attr('src', e.target.result);
		}

		reader.readAsDataURL(input);
		$scope.submitBtn = false;
		}
	}

	$scope.$watch('sysUpload.file', function(newValue, oldValue){
		if(newValue){
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
			
			readURL2(newValue);
		}
	});
	
	$scope.changeTitle = function(){
		$scope.submitBtn = false;
	}
	
	$scope.submitSystemImage = function(){
		
		var fd = new FormData();
		 fd.append('id', $scope.sysUpload.id );
		 fd.append('title', $scope.sysUpload.title );
		 fd.append('file', $scope.sysUpload.file );
		
		$http.post(baseUrl + "library/editImage",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				var tmpList = $scope.gallaryImages;
				var tmp = [];
				for(var i=0;i<tmpList.length;i++){
					if(tmpList[i].id != $scope.sysUpload.id){
						tmp.push(tmpList[i]);
					}else{
						thsArr = {
							created_at:response.data.created_at,
							id:response.data.id,
							name:response.data.name,
							path:response.data.path,
							status:response.data.status,
							title:response.data.title,
							type:response.data.type,
							updated_at:response.data.updated_at
						}
						tmp.push(thsArr);
					}
				}
				$scope.gallaryImages = tmp;
			
				$("body").removeClass("modal-open");	
				$("#img_popup").hide();
								
			}else{
				toaster.pop('error',response.result.msg);
			}
		}).error(function(){
			toaster.pop('error', msg['2002']);
		});
	}
	
	$scope.imageArray = [];
	$scope.funPushPop = function(id){ 
		if($.inArray( id, $scope.imageArray ) == -1){
			$scope.imageArray.push(id);
		}else{
			var imgArr = $scope.imageArray;
			var tmp = [];
			for(var i=0;i<imgArr.length;i++){
				if(imgArr[i]!=id){
					tmp.push(imgArr[i]);
				}
			}
			$scope.imageArray = tmp;
			$scope.selectAllImages = false;
		}
	}
	
	$scope.checkValue = function(id){
		if($.inArray( id, $scope.imageArray ) > -1){
			return true;
		}else{
			return false;
		}
	}
	
	$scope.deleteSelected = function(){
		if($scope.imageArray.length != 0)
		{
		 	if(confirm(msg['L1012'])){
			var id = $scope.imageArray.join(",");
			dataService.getData({'asset_id':id}, baseUrl + "asset/deleteAll","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
						$scope.getGallery();
					var tmpList = $scope.gallaryImages;
					var tmp = [];
					for(var i=0;i<tmpList.length;i++){
						if($.inArray( tmpList[i].id, $scope.imageArray ) == -1){
							tmp.push(tmpList[i]);
						}
					}
					$scope.imageArray = [];
					$scope.gallaryImages = tmp;
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
		
		}
		else
		{
			alert("Please select any image to delete")
		}
		
	}
	
	$scope.funcSelectAll = function(){
		$scope.imageArray = [];
		if($scope.selectAllImages != true){
			for(var i=0;i < $scope.gallaryImages.length;i++){
				$scope.imageArray.push($scope.gallaryImages[i].id);
			}
		}
	}
}

function docLibraryCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,arrayPushService) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.sysUpload = {};
	$scope.submitBtn = true;
	$scope.filterDocType = 'all';
	$scope.realGallaryDocs = [];
	$scope.hideButton = false;
	$scope.$watch('filterDocType', function(newValue, oldValue){
		if(newValue){
			var tmp = [];
			for(var i=0; i<$scope.realGallaryDocs.length; i++){
				/* if($scope.realGallaryDocs[i].type == newValue || newValue == 'all'){
					tmp.push($scope.realGallaryDocs[i]);
				} */
				if(newValue == "all"){
					tmp.push($scope.realGallaryDocs[i]);
				}else if(newValue == "pdf"){
					if($scope.realGallaryDocs[i].type == "pdf"){
						tmp.push($scope.realGallaryDocs[i]);
					}
				}else if(newValue == "doc"){
					if($scope.realGallaryDocs[i].type == "doc" || $scope.realGallaryDocs[i].type == "docx"){
						tmp.push($scope.realGallaryDocs[i]);
					}
				}else if(newValue == "xls"){
					if($scope.realGallaryDocs[i].type == "xls" || $scope.realGallaryDocs[i].type == "xlsx"){
						tmp.push($scope.realGallaryDocs[i]);
					}
				}else if(newValue == "txt"){
					if($scope.realGallaryDocs[i].type == "txt"){
						tmp.push($scope.realGallaryDocs[i]);
					}
				}
				else if(newValue == "video"){
					if($scope.realGallaryDocs[i].type == "video"){
						tmp.push($scope.realGallaryDocs[i]);
					}
				}
			}
			$scope.gallaryDocs = tmp;
			$scope.totalItems = $scope.gallaryDocs.length;
		}
	});
	
	$scope.changeTitle = function(){
		$scope.submitBtn = false;
	}
	
	$scope.getSearchData = function(){
		var str='';
		if($scope.search){
		str = '?search_keyword='+$scope.search;
		}else{
		str = "";
		}
		dataService.getData($scope.data, baseUrl + "assets/documents"+str).then(function(dataResponse) {
			$scope.gallaryDocs = dataResponse.data.data;
			$scope.totalItems = $scope.gallaryDocs.length;
			
		});
	}
	
	$scope.getGallery = function(){
		dataService.getData($scope.data, baseUrl + "assets/documents?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1))).then(function(dataResponse) {
			$scope.realGallaryDocs = arrayPushService.arrayPush(dataResponse.data.data, $scope.realGallaryDocs);
			$scope.gallaryDocs = arrayPushService.arrayPush(dataResponse.data.data, $scope.gallaryDocs);
			$scope.totalItems = $scope.gallaryDocs.length;
			$scope.hideButton = false;
			if($scope.gallaryDocs.length > 0)
			{
			$scope.hideButton = true;
			}
		});
	}
	$scope.getGallery();
	$scope.itemsPerPage = 12;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.totalItems = 0;
	$scope.pageCount = function () {
     return Math.ceil($scope.gallaryDocs.length / $scope.itemsPerPage);
   };
	
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.gallaryDocs.length / $scope.itemsPerPage))
		{
			$scope.getGallery();
		}
	};
	$scope.deleteDoc = function(id){
		if(confirm(msg['L1001'])){
			dataService.getData({'asset_id':id}, baseUrl + "libraryDocument/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
						
					var tmpList = $scope.gallaryDocs;
					var tmp = [];
					for(var i=0;i<tmpList.length;i++){
						if(tmpList[i].id != id){
							tmp.push(tmpList[i]);
						}
					}
					$scope.gallaryDocs = tmp;
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
	}
	
	$scope.setEditDoc = function(id){
		$scope.sysUpload.file = "";
		$("input[type='file']").val("");
		$scope.submitBtn = true;
		for(var i=0;i<$scope.gallaryDocs.length;i++){
			if($scope.gallaryDocs[i].id == id){
				$scope.sysUpload.id = id;
				$scope.sysUpload.name = $scope.gallaryDocs[i].name;
				$scope.sysUpload.type = $scope.gallaryDocs[i].type;
				$scope.sysUpload.title = $scope.gallaryDocs[i].title;
			}
		}
	}
	
	function readURL2(input){
		if (input.files && input.files[0]) {
			var str = input.files[0].name;
			var arr = str.split(".");
			$scope.sysUpload.type = arr[arr.length - 1];
			$scope.submitBtn = false;
		}
	}
	
	
	$scope.$watch('sysUpload.file', function(newValue, oldValue){
		if(newValue){
			
			var input = document.getElementById("ig");
			if(input.files && input.files.length == 1)
			{           
				if (input.files[0].size > 20*1024*1024) 
				{
					alert(msg['L1014']);
					return false;
				}
				var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
				if($.inArray( ext, documentsFormates ) > -1)
				{
					
				}else{
					alert(msg["L1006"]);
					return false;
				}
			}else{
				alert(msg["L1008"]);
				return false;
			}
			
			var str = newValue.name;
			var arr = str.split(".");
			$scope.sysUpload.type = arr[arr.length - 1];
			$scope.submitBtn = false;
		}
	});
	
	$scope.submitSystemDoc = function(){
		
		var fd = new FormData();
		 fd.append('id', $scope.sysUpload.id );
		 fd.append('title', $scope.sysUpload.title );
		 fd.append('file', $scope.sysUpload.file );
		
		$http.post(baseUrl + "asset/editDocumentToLibrary",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				$(".cancel").click();
				// $scope.getGallery();
				var tmpList = $scope.gallaryDocs;
				var tmp = [];
				for(var i=0;i<tmpList.length;i++){
					if(tmpList[i].id != $scope.sysUpload.id){
						tmp.push(tmpList[i]);
					}else{
						thsArr = {
							created_at:response.data.created_at,
							id:response.data.id,
							name:response.data.name,
							path:response.data.path,
							status:response.data.status,
							title:response.data.title,
							type:response.data.type,
							updated_at:response.data.updated_at
						}
						tmp.push(thsArr);
					}
				}
				$scope.gallaryDocs = tmp;
			}else{
				toaster.pop('error',response.result.msg);
			}
			
		}).error(function(){
			toaster.pop('error', msg['0000']);
		});
	}
	
	$scope.imageArray = [];
	$scope.funPushPop = function(id){ 
		if($.inArray( id, $scope.imageArray ) == -1){
			$scope.imageArray.push(id);
		}else{
			var imgArr = $scope.imageArray;
			var tmp = [];
			for(var i=0;i<imgArr.length;i++){
				if(imgArr[i]!=id){
					tmp.push(imgArr[i]);
				}
			}
			$scope.imageArray = tmp;
		}
	}
	
	$scope.checkValue = function(id){
		if($.inArray( id, $scope.imageArray ) > -1){
			return true;
		}else{
			return false;
		}
	}
	
	$scope.deleteSelected = function(){
		if($scope.imageArray.length != 0)
		{
				if(confirm(msg['L1011'])){
				var id = $scope.imageArray.join(",");
				dataService.getData({'asset_id':id}, baseUrl + "asset/deleteAll","POST").then(function(dataResponse) {
					var response = dataResponse.data;
					if(response.status == 'success'){
						toaster.pop('success',response.msg);
						$scope.getGallery();	
						var tmpList = $scope.gallaryDocs;
						var tmp = [];
						for(var i=0;i<tmpList.length;i++){
							if($.inArray( tmpList[i].id, $scope.imageArray ) == -1){
								tmp.push(tmpList[i]);
							}
						}
						$scope.imageArray = [];
						$scope.gallaryDocs = tmp;
					}else{
						toaster.pop('error',response.result.msg);
					}
				});
			}
		}
		else
		{
			alert("Please select any document to delete");	
		}
	}
	
	$scope.funcSelectAll = function(){
		$scope.imageArray = [];
		if($scope.selectAllImages != true){
			for(var i=0;i < $scope.gallaryDocs.length;i++){
				$scope.imageArray.push($scope.gallaryDocs[i].id);
			}
		}
	}
	
}



function settingsCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	$('#page-wrapper').removeClass('nav-small');
	$scope.baseUrl = baseUrl;
	$scope.sysUpload = {};
	
	$scope.getHomeBanner = function(){
		dataService.getData($scope.data, baseUrl + "assets/homeBanner").then(function(dataResponse) {
			$scope.realHomeBanner = dataResponse.data.data;
			$scope.homeBanners = dataResponse.data.data;
		});
	}
	$scope.getHomeBanner();
	
	$scope.deleteHomeBanner = function(id){
		if(confirm(msg['L1003'])){
			dataService.getData({'id':id}, baseUrl + "assets/homeBanner/delete","POST").then(function(dataResponse) {
				var response = dataResponse.data;
				if(response.status == 'success'){
					toaster.pop('success',response.msg);
						$("#close").click();
					$scope.getHomeBanner();
				}else{
					toaster.pop('error',response.result.msg);
				}
			});
		}
	}
	$scope.closeModal = function()
	{
		$scope.uploadImageName="";
	}
	$scope.uploadHomeBanner = function(){
		
		var input = document.getElementById("doc");
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
		 fd.append('key', $scope.sysUpload.key );
		 fd.append('value', $scope.sysUpload.value );
		 fd.append('type', $scope.sysUpload.type );
		
		$http.post(baseUrl + "assets/homeBanner/upload",fd, {
            transformRequest: angular.identity,
            headers: {'Content-Type': undefined}
        })
		.success(function (response) {
			if(response.status == 'success'){
			$scope.uploadImageName="";
				toaster.pop('success',response.msg);
				$(".cancel").click();
				$scope.getHomeBanner();
				$("#doc").val("");
			}else{
				toaster.pop('error',response.result.msg);
			}
			
		}).error(function(){
			toaster.pop('error', msg['0000']);
		});
	}
		
	$scope.$watch('sysUpload.value', function(newValue, oldValue){
		if(newValue){
			
			var input = document.getElementById("doc");
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
		
			$scope.uploadImageName = newValue.name;
		}
	});
	
}
	


angular
	.module('cubeWebApp')
	.controller('imageLibraryCtrl', imageLibraryCtrl)
	.controller('docLibraryCtrl', docLibraryCtrl)
	