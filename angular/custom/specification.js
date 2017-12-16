
function catlistCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	$scope.addCatData ={};
	$scope.orderList = [];
	$scope.list=[];
	
	$scope.getUpdatedData=function(data){
		$scope.list = data;
		$scope.totalItems = $scope.list.length;
	}
	$scope.init = function()
	{
		$http.get(baseUrl+"list/specification/category")
			.success(function(response){
			if(response.status == 'success'){			
				$scope.list = response.data;
				$scope.orderList = angular.copy(response.data);
			
				$scope.totalItems = $scope.list.length;
			}
			else{
				toaster.pop('error',response.result.msg);
				
			}
			})
	}
	$scope.init();
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
			
				$scope.init();
				$scope.sort($scope.sortKey);
			} 
		}
	
	$scope.closeForm = function()
	{
		$scope.addCatData={};
		document.getElementById("catForm").reset();
	}
	
	$scope.submitAddCat = function()
	{	
		$http.post(baseUrl+"add/spec/category",{'name':$scope.addCatData.name,'status':$scope.addCatData.status})
			.success(function(response){
			if(response.status=="success"){
				toaster.pop('success',response.msg);
				$scope.addCatData={};
				$scope.init();
				
				$("#cancel").click();
				
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	
	}
	$scope.editData = function(id){

		for(var i=0;i<$scope.list.length;i++)
		{
			if($scope.list[i].id == id)
			{
				$scope.editCatData=angular.copy($scope.list[i]);
				//$scope.editData = angular.copy($scope.videoList[k]);
				$scope.id=id;
				break;
			}
		}
	}
	$scope.submitEditCat = function()
	{
		$http.post(baseUrl+"edit/spec/category",{'id':$scope.id,'name':$scope.editCatData.cat_name,'status':$scope.editCatData.status})
			.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				
				$("#close").click();
				$scope.init();
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	}
		$scope.deleteCat = function(id) {
		if(confirm("Are you sure to delete this category?")){
		$http.post(baseUrl+"delete/spec/category", {'id':id}).success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				
				$scope.init();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
		}
	}
	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].id, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "specification/category/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.init();$scope.sort($scope.sortKey);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}
}

function subcatlistCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$localStorage,arrayPushService) {
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	$scope.CatStatus = CatStatus;
	
	$scope.addsubCatData ={};
	$scope.list=[];
	var filerData= [] ;
	var realData= [];
	$scope.orderList = [];
	$http.get(baseUrl+"list/specification/category")
			.success(function(response){
			if(response.status == 'success'){			
				$scope.catlist = response.data;

			}
			else{
				toaster.pop('error',response.result.msg);
				
			}
			})
			
	$scope.getUpdatedData=function(data){
		$scope.list = data;
		$scope.totalItems = $scope.list.length;
	}
	
	$scope.init = function()
	{
		dataService.getData($scope.data, baseUrl + "list/specification/subcategory?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) { 
		
			if(dataResponse.data.status == 'success'){	
				$scope.list = arrayPushService.arrayPush(dataResponse.data.data, $scope.list);				
				$scope.orderList = angular.copy(dataResponse.data.data);
				$localStorage.subcatData = dataResponse.data.data;
				realData = $scope.list;
				$scope.totalItems = $scope.list.length;
				
				if($scope.category_id != undefined)
				{
					$scope.filterList($scope.category_id);
				}
			}
			else{
				toaster.pop('error',response.result.msg);
				
			}
			})
	}
	$scope.init();
	$scope.filterList = function(id)
	{
		if(id != "")
		{	
			filterData =[];
			for(var i=0;i<realData.length;i++)
			{	
				if(id== realData[i].spec_catid)
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
			if($scope.category_id == undefined){
				$scope.init();
				$scope.sort($scope.sortKey);
			}
		}
	};
	$scope.closeForm = function()
	{
		$scope.addsubCatData={};
		document.getElementById("subcatForm").reset();
	}
	$scope.submitAddsubCat = function()
	{	
		$http.post(baseUrl+"add/spec/subcategory",{'name':$scope.addsubCatData.name,'status':$scope.addsubCatData.status,'spec_catid':$scope.addsubCatData.spec_catid,'threshold':$scope.addsubCatData.threshold})
			.success(function(response){
			if(response.status=="success"){
				toaster.pop('success',response.msg);
				$("#cancel").click();
				$scope.addsubCatData = {};
				$scope.init();
				
				
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	
	}
	$scope.editData = function(id){
		$http.get(baseUrl+"spec/subcategory/"+id)
			.success(function(response){
			if(response.status == 'success'){
				$scope.editsubCatData=response.data;
				$scope.id =id;
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	}
	$scope.submitEditsubCat = function()
	{
		$http.post(baseUrl+"edit/spec/subcategory",{'id':$scope.id,'name':$scope.editsubCatData.name,'status':$scope.editsubCatData.status,'spec_catid':$scope.editsubCatData.spec_catid,'threshold':$scope.editsubCatData.threshold})
			.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				
				$("#cancel1").click();
				
				$scope.init();
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	}
	$scope.deleteSubCat = function(id) {
		if(confirm("Are you sure to delete this subcategory?")){
		$http.post(baseUrl+"delete/spec/subcategory", {'id':id}).success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				
				$scope.init();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
		}
	}


	$scope.setOrder = function(){
		var orderDataArray = [];
		
		for(var i=0;i<$scope.orderList.length;i++){
			var thisObj = {id:$scope.orderList[i].subcatid, pos:i};
			orderDataArray.push(thisObj);
		}
		dataService.getData({'orderDataArray':orderDataArray}, baseUrl + "specification/subcategory/order","POST").then(function(dataResponse) {
			var response = dataResponse.data;
			if(response.status == 'success'){
				toaster.pop('success',response.result.msg);
				$scope.init();$scope.sort($scope.sortKey);
			}else{
				toaster.pop('error',response.result.msg);
			}
		});
	}

}


function attributelistCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,arrayPushService,$localStorage) {
	
	$scope.subcatId = $routeParams.id;
	
	$scope.addattributeData ={};
	$scope.list=[];
	$scope.subcatname;
	$scope.subcatData = $localStorage.subcatData;
	
	
	for(var i=0;i<$scope.subcatData.length;i++)
	{
		if($scope.subcatId == $scope.subcatData[i].subcatid)
		{
			$scope.subcatname = $scope.subcatData[i].subcat_name;
			break;
		}
	}
	
	$scope.init = function()
	{
		$http.get(baseUrl+"attribute/spec/subcategory/"+$scope.subcatId)
			.success(function(response){
			if(response.status == 'success'){			
				$scope.list = response.data;
				$scope.totalItems = $scope.list.length;
				//$scope.subcatname = $scope.list[0].spec_subcatname;
				if($scope.list.length == 0)
				{
					$scope.message = "No attribute in this subcategory";
				}
				////console.log($scope.list);
			}
			else{
				toaster.pop('error',response.result.msg);
				
			}
			})
	}
	////console.log($scope.subcatname);
	$scope.init();
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
			/* if($scope.selectCategory == ""){
				$scope.init();
				$scope.sort($scope.sortKey);
			} */
		}
	};
	
	$scope.submitAddAttribute = function()
	{	
		$http.post(baseUrl+"add/attribute/spec/subcategory",{'name':$scope.name,'subcatid':$scope.subcatId,'numeric_value':$scope.number})
			.success(function(response){
			if(response.status=="success"){
				toaster.pop('success',response.msg);
				$scope.init();
				$("#cancel").click();
				$scope.name = "";
				$scope.number = "";
				
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	
	}
	$scope.editData = function(id){

		for(var i=0;i<$scope.list.length;i++)
		{
			if($scope.list[i].id == id)
			{
				$scope.editAttData=angular.copy($scope.list[i]);
				//////console.log($scope.editAttData)
				$scope.id=id;
				break;
			}
		}
	}
	$scope.submitEditAttribute = function()
	{
		$http.post(baseUrl+"edit/attribute/spec/subcategory",{'id':$scope.id,'name':$scope.editAttData.text_value,'subcatid':$scope.editAttData.spec_subcatname, 'numeric_value':$scope.editAttData.numeric_value})
			.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
				
				$("#close").click();
				$scope.init();
			}
			else{
				toaster.pop('error',response.result.msg);
			}
			})
	}
		$scope.deleteAtt = function(id) {
		if(confirm("Are you sure to delete this attribute?")){
		$http.post(baseUrl+"delete/attribute/spec/subcategory", {'id':id}).success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				
				$scope.init();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
		}
	}
	
}



angular
	.module('cubeWebApp')
	.controller('catlistCtrl', catlistCtrl)
	.controller('subcatlistCtrl', subcatlistCtrl)
	.controller('attributelistCtrl', attributelistCtrl)
	
	
