
function manufacturerListingCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {
	
	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	/* $scope.getSearchData = function(){
		var str='';
		if($scope.search){
		str = '?search_keyword='+$scope.search;
		}else{
		str = "";
		}
		dataService.getData($scope.data, baseUrl + "list/manufacturer"+ str ).then(function(dataResponse) {
			$scope.manufactureList = dataResponse.data.data;
			$scope.totalItems = $scope.manufactureList.length;
			$scope.sort($scope.sortKey);
		});
	} */
	$scope.getUpdatedData=function(data){
		$scope.manufactureList = data;
		$scope.totalItems = $scope.manufactureList.length;
	}
	$scope.initData = function() {
		$http.get(baseUrl+"list/manufacturer").then(function(response) {
			$scope.manufactureList = response.data.data;
			$scope.totalItems = $scope.manufactureList.length;
			////console.log($scope.manufactureList);
		})
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

		$scope.manufactureList = $scope.manufactureList.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.manufactureList.length / $scope.itemsPerPage))
		{
			if($scope.selectCategory == ""){
				$scope.initData();
				$scope.sort($scope.sortKey);
			}
		}
	};

	$scope.editManufacturer = function(id){
		$http.get(baseUrl+'manufacturer/'+id).then(function(response){
			$scope.editManufacturerData = response.data.data;
			$scope.editManufacturerData.id = id;
			////console.log($scope.editManufacturerData);
		})
	}

	$scope.submitEditManufacturer = function() {
		$("#cancel").click();
		var input = document.getElementById("ig");
        if(input.files && input.files.length == 1)
        {           
            if (input.files[0].size > 5*1024*1024) 
            {
                alert(msg['3007']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if(ext =="GIF" || ext=="gif" || ext =="JPG" || ext =="jpg" || ext =="PNG" || ext =="png")
			{
				
			}else{
				alert('Only gif,jpg and png format allow.');
				return false;
			}
        }
	
		var fd = new FormData();
		if($scope.editManufacturerData.name == "Gionee" || $scope.editManufacturerData.name =="gionee")
		{
			alert("Please fill some other brand name");
		}
		else{
		fd.append('name', $scope.editManufacturerData.name);
		}
		
		fd.append('status', $scope.editManufacturerData.status);
		fd.append('file', $scope.editManufacturerData.file);
		fd.append('description', $scope.editManufacturerData.description);
		fd.append('id', $scope.editManufacturerData.id);
		////console.log($scope.editManufacturerData);

		//$http.post(baseUrl+"edit/manufacturer", $scope.editManufacturerData)
		$http.post(baseUrl+"edit/manufacturer", fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response){
			if (response.status == 'success') {
				////console.log(response);
				toaster.pop('success', response.msg);
				$scope.initData();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
	}

	$scope.deleteManufacturer = function(id) {
	if(confirm("Are you sure to delete this manfacturer?")){
		$http.post(baseUrl+"delete/manufacturer", {'id':id}).success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				
				$scope.initData();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
	}
}
function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#ig").change(function(){
		readURL(this);
	});

	
}

function manufacturerAddCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	var addManufacturer = {};
	$scope.CatStatus = CatStatus;

	$scope.addManufacturerData = function(){
		var input = document.getElementById("ig");
        if(input.files && input.files.length == 1)
        {           
            if (input.files[0].size > 5*1024*1024) 
            {
                alert(msg['3007']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if(ext =="GIF" || ext=="gif" || ext =="JPG" || ext =="JPEG"  ||  ext =="jpeg"  || ext =="jpg" || ext =="PNG" || ext =="png")
			{
				
			}else{
				alert('Only gif,jpg and png format allow.');
				return false;
			}
        }
	
	

		var fd = new FormData();
		if($scope.addManufacturer.name == "Gionee" || $scope.addManufacturer.name =="gionee")
		{
			alert("Please fill some other brand name");
		}
		else{
		fd.append('name', $scope.addManufacturer.name);
		fd.append('status', $scope.addManufacturer.status);
		fd.append('file', $scope.addManufacturer.file);
		fd.append('description', $scope.addManufacturer.description);
		////console.log($scope.addManufacturer);

		$http.post(baseUrl+"add/manufacturer", fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				$location.path('recommender/manufacturer-listing')
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
		}

	}
	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#ig").change(function(){
		readURL(this);
	});
	
}	

	


function modelListingCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,arrayPushService) {

	$scope.baseUrl = baseUrl;
	$scope.reverse = true;
	$scope.sortKey = "position";
	var realData= []; 
	var filterData=[];
	$scope.modelList = [];
	$scope.getUpdatedData=function(data){
		$scope.modelList = data;
		$scope.totalItems = $scope.modelList.length;
	}
	$http.get(baseUrl+"list/manufacturer").then(function(response) {
			$scope.manufactureList = response.data.data;
			
		})
	$scope.initData = function() {
	dataService.getData($scope.data, baseUrl + "list/model?page_no=" + Math.ceil(Number((($scope.currentPage*$scope.itemsPerPage)/100)+1)) ).then(function(dataResponse) { 
		
			$scope.modelList = arrayPushService.arrayPush(dataResponse.data.data, $scope.modelList);
			realData = $scope.modelList;
			$scope.totalItems = $scope.modelList.length;
			////console.log($scope.modelList);
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
				if(id== realData[i].mf_id)
				{
					filterData.push(realData[i]);
				}				
			
			}
			$scope.modelList = filterData;
			$scope.totalItems = $scope.modelList.length;
		}
		else
		{
			$scope.modelList = realData;
			$scope.totalItems = $scope.modelList.length;
			
		}
	}
	$scope.totalItems = 0;
	$scope.itemsPerPage = 10;
    $scope.currentPage = 1;
	$scope.maxSize = 10;
	$scope.pageCount = function () {
		return Math.ceil($scope.modelList.length / $scope.itemsPerPage);
	};		
	$scope.sort = function(keyname){
        $scope.sortKey = keyname; 
        $scope.reverse = !$scope.reverse;

		$scope.modelList = $scope.modelList.sort(function(a, b) {
			if (!$scope.reverse) return (a[keyname] > b[keyname]) ? 1 : ((a[keyname] < b[keyname]) ? -1 : 0);
			else return (b[keyname] > a[keyname]) ? 1 : ((b[keyname] < a[keyname]) ? -1 : 0);
		});
    }
	$scope.pageChanged = function() {
		if($scope.currentPage == Math.ceil($scope.modelList.length / $scope.itemsPerPage))
		{
			
				$scope.initData();
				$scope.sort($scope.sortKey);
			
		}
	};
	$scope.deleteModel = function(id) {
			
			if(confirm("Are you sure to delete this model?")){
		$http.post(baseUrl+"delete/model", {'id':id}).success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
					$("#close").click();
				$scope.initData();
			}else{
				toaster.pop('error', response.result.msg);
			}
		})
	}
	}
}

function modelAddCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location) {
	
	var addModel = {};
	$scope.CatStatus = CatStatus;
	$http.get(baseUrl+"list/manufacturer").then(function(response) {
			$scope.manufactureList = response.data.data;
			////console.log($scope.manufactureList);
		})
		
		
	$scope.addModelData = function(){
		
		var input = document.getElementById("ig");
        if(input.files && input.files.length == 1)
        {           
            if (input.files[0].size > 5*1024*1024) 
            {
                alert(msg['3007']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if(ext =="GIF" || ext=="gif" || ext =="JPG"  || ext =="JPEG"  ||  ext =="jpeg" || ext =="jpg" || ext =="PNG" || ext =="png")
			{
				
			}else{
				alert('Only gif,jpg and png format allow.');
				return false;
			}
        }
		
		var fd = new FormData();
		fd.append('model_name', $scope.addModel.model_name);
		fd.append('status', $scope.addModel.status);
		fd.append('file', $scope.addModel.file);
		fd.append('mf_id', $scope.addModel.mf_id);
		fd.append('description', $scope.addModel.description);
		////console.log($scope.addModel);

		$http.post(baseUrl+"add/model", fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response) {
			if(response.status == 'success'){
				toaster.pop('success', response.msg);
				$scope.model_id = response.data.model_id;
				////console.log($scope.model_id);
				$location.path("/recommender/edit-model/" + $scope.model_id);
				
			}else{
				toaster.pop('error', response.result.msg);
			}
		})

	}
	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#ig").change(function(){
		readURL(this);
	});
}	

function modelEditCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage) {
	$scope.baseUrl = baseUrl;
	$scope.getModelId = $routeParams.id;
	
		$http.get(baseUrl+"list/manufacturer").then(function(response) {
			$scope.manufactureList = response.data.data;
			////console.log($scope.manufactureList);
		})
		
		$http.get(baseUrl+'model/'+$scope.getModelId).then(function(response){
			$scope.editModel = response.data.data;
			$localStorage.model_name = response.data.data.model_name;
			$localStorage.mf_name = response.data.data.mf_name;
			////console.log($localStorage.model);
			$scope.editModel.id = $scope.getModelId;
			////console.log($scope.editModel);
		})
		
	$scope.editModelData = function(){
	var input = document.getElementById("ig");
        if(input.files && input.files.length == 1)
        {           
            if (input.files[0].size > 5*1024*1024) 
            {
                alert(msg['3007']);
                return false;
            }
			var ext = input.value.substring(input.value.lastIndexOf('.') + 1);
			if(ext =="GIF" || ext=="gif" || ext =="JPG" || ext =="jpg" || ext =="PNG" || ext =="png")
			{
				
			}else{
				alert('Only gif,jpg and png format allow.');
				return false;
			}
        }
	
		var fd = new FormData();
		fd.append('model_name', $scope.editModel.model_name);
		fd.append('status', $scope.editModel.status);
		fd.append('file', $scope.editModel.file);
		fd.append('mf_id', $scope.editModel.mf_id);
		fd.append('description', $scope.editModel.description);
		fd.append('id', $scope.editModel.id);
		////console.log($scope.editModel);

		$http.post(baseUrl+"edit/model", fd, {
			transformRequest: angular.identity,
			headers: {'Content-Type': undefined}
		})
		.success(function(response) {
			
			if(response.status == 'success'){
			toaster.pop('success',response.msg);
			
			}
			else
			{
				toaster.pop('error',response.result.msg);
			}
			
			})
	}
	function readURL(input) {

		if (input.files && input.files[0]) {
			var reader = new FileReader();

			reader.onload = function (e) {
				$('#blah').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}
	$("#ig").change(function(){
		readURL(this);
	});
	
}
function addSpecCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage) {
	$scope.specData = {};
	$scope.addSpec={};
	$scope.data = {};
	$scope.modelId = $routeParams.model_id;
	$scope.brand = $routeParams.brand;
	if($routeParams.brand == "gionee")
	{
		$scope.manufacturerName = $localStorage.category_name;
		$scope.modelName = $localStorage.product_name;
	}
	else
	{
		$scope.manufacturerName = $localStorage.mf_name;
	$scope.modelName = $localStorage.model_name;
	}
	
	$http.get(baseUrl+"list/specification")
		.success(function(response){
		if(response.status == 'success'){
			$scope.specData = response.data;
			////console.log($scope.specData);
			
		}
		})
		$scope.category={};
		//$scope.sendData={};
		var sendData=[];
			/* 'catid':0,
			'subcatid':0,
			'numaricvalue':'',
			'attribute':[] */
	
		//$scope.model_id = $localStorage.model.id;
	$scope.sendValue = function(cat,subcat,att)
	{	
		var realData = {};
		realData.catid = cat;
		realData.subcatid = subcat;
		realData.numaricvalue = null;
		realData.attribute = att;
		var arrlen=sendData.length;
		var i=0;
		for(i;i<arrlen;i++){
			if(sendData[i].catid==cat){
				if(sendData[i].subcatid ==subcat){
						//sendData[i].catid=cat;
						//sendData[i].subcatid=subcat;
						//sendData[i].numaricvalue='';
						sendData[i].attribute=att;
						break;
				}
			}
		}
		if(i==arrlen){
			sendData.push(realData);
		}
		
		////console.log(sendData);
			$scope.data = sendData;
	
	}	
	$scope.attribute = [];
	
	/* $scope.addsubcat = function(cat,subcat,numaric)
	{
		var realData = {};
		realData.catid = cat;
		realData.subcatid = subcat;
		realData.numaricvalue = numaric;
		realData.attribute = [];
		var arrlen=sendData.length;
		var i=0;
		for(i;i<arrlen;i++){
			if(sendData[i].catid==cat){
				if(sendData[i].subcatid ==subcat){
						//sendData[i].catid=cat;
						//sendData[i].subcatid=subcat;
						sendData[i].numaricvalue=numaric;
						//sendData[i].attribute=[];
						break;
				}
			}
		}
		if(i==arrlen){
			sendData.push(realData);
		}
		
		////console.log(sendData);
			$scope.data = sendData;
	} */
	$scope.addSpecData= function(status)
	{
		$http.post(baseUrl+"add/model/specification",{'model_id':$scope.modelId,'data':$scope.data,'view_status':status,'brand':$scope.brand})
		.success(function(response){
			if(response.status == 'success')
			{
				toaster.pop('success',response.msg);
				$location.path("/specification/edit-specification/" + $scope.modelId+"/"+$scope.brand);
				$localStorage.mf_name = ""; 
				$localStorage.model_name = ""; 
				$localStorage.category_name = "";
				$localStorage.product_name = "";
			}
			else
			{
				toaster.pop('error',response.result.msg);
			}
		})
		
		
	}

}
function editSpecCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage) {
	$scope.modelId = $routeParams.model_id;
	$scope.brand = $routeParams.brand;
	$scope.specData = [];
	$scope.addSpec={};
	$scope.data = {};
	$scope.arr=[];
	$scope.att=[];
	$scope.subcat1=[];
	var sendData=[];
	
	$scope.pushData=[];
	$scope.selectedSpecData = {};
	
	$scope.init = function(){
		$http.get(baseUrl+ "edit/model/specification/"+$scope.modelId+"?brand="+$scope.brand)
			.success(function(response){
				if(response.status == 'success'){
					$scope.selectedSpecData = response.data;
				
					$scope.publish_status = response.publish_status;
					$scope.manufacturerName = response.p_name.category;
					$scope.modelName = response.p_name.product;
					var selectedSpecData=$scope.selectedSpecData;
					//console.log(selectedSpecData);
					for(var i=0;i<selectedSpecData.length;i++)
					{
						var subcategory =selectedSpecData[i].subcategory;
						for(var j=0;j<subcategory.length;j++)
						{
							var realData = {};
							if(subcategory[j].selected_att_text.length > 0)
							{
								realData.catid = selectedSpecData[i].id;
								realData.subcatid = subcategory[j].subcatid;
								realData.attribute = subcategory[j].selected_att_text;
								if(subcategory[j].selected_att_numaric !==  'null')
								{
									realData.numaricvalue = subcategory[j].selected_att_numaric;
								}else{
									realData.numaricvalue = '';
								}								
								sendData.push(realData);	
								//console.log(sendData);
								
							}					    

						}
					}
					
				}
				else{
					toaster.pop("error",response.result.msg)
				}			
				
			})
	}
	$scope.init();	
	$scope.category={};	
	$scope.data = sendData;
		
	$scope.sendValue = function(cat,subcat,att)
	{	
		console.log("cat---"+ cat +"subcat --"+subcat+" att--"+att )
		var realData = {};
		realData.catid = cat;
		realData.subcatid = subcat;
		realData.numaricvalue = null;
		realData.attribute = att;
		var arrlen=sendData.length;
		var i=0;
		for(i;i<arrlen;i++){
			if(sendData[i].catid==cat){
				if(sendData[i].subcatid ==subcat){
						if(sendData[i].numaricvalue){
							realData.numaricvalue = sendData[i].numaricvalue;
						}else{
							realData.numaricvalue = '';
						}
						sendData[i].attribute=att;
						break;
				}
			}
		}
		if(i==arrlen){
			sendData.push(realData);
		}
			$scope.data = sendData;
			//console.log($scope.data);
	
	}	
	$scope.attribute = [];
	

	$scope.editSpecData= function(status)
	{
		$http.post(baseUrl+"edit/model/specification/"+$scope.modelId,{'data':$scope.data,'view_status':status,'brand':$scope.brand})
		.success(function(response){
			if(response.status == 'success'){
				toaster.pop('success',response.msg);
					//$scope.init();
				
					
			}
			else{
				toaster.pop('error',response.result.msg);
			}
		})
		
		
	}

}
function viewspecificationCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage) {
	
	$scope.modelId = $routeParams.id;
	$scope.brand = $routeParams.brand;
	
	$scope.init = function(){
		$http.get(baseUrl+ "edit/model/specification/"+$scope.modelId+"?brand="+$scope.brand)
			.success(function(response){
				if(response.status == 'success'){
					$scope.viewSpecData = response.data;
					$scope.manufacturerName = response.p_name.category;
					$scope.modelName = response.p_name.product;
				}
				else{
					toaster.pop("error",response.result.msg);
				}
			})
	}
	$scope.init();
} 
function disclaimerCtrl($scope,$http,DTOptionsBuilder,dataService,toaster,$location,$routeParams,$localStorage) {
	$scope.disclaimer_text;
	
	
	$scope.init = function()
	{
		$http.get(baseUrl+"disclaimer")
		.success(function(response){
			if(response.status == 'success')
			{
				$scope.disclaimer_text = response.data.disclaimer_text;
			}
			else{
				toaster.pop("error",response.result.msg);
			}
		})
	}
	$scope.init();
	$scope.update = function()
	{
		$http.post(baseUrl+"disclaimer",{'disclaimer_text':$scope.disclaimer_text})
		.success(function(response){
		if(response.status == 'success')
		{
			toaster.pop("success",response.msg);
			$scope.init();
		}
		else{
			toaster.pop("error",response.result.msg);
		}
	})
	}

}	


angular
	.module('cubeWebApp')
	.controller('manufacturerListingCtrl', manufacturerListingCtrl)
	.controller('manufacturerAddCtrl', manufacturerAddCtrl)
	.controller('modelListingCtrl', modelListingCtrl)
	.controller('modelAddCtrl', modelAddCtrl)
	.controller('modelEditCtrl', modelEditCtrl)
	.controller('addSpecCtrl', addSpecCtrl)
	.controller('editSpecCtrl', editSpecCtrl)
	.controller('viewspecificationCtrl', viewspecificationCtrl)
	.controller('disclaimerCtrl', disclaimerCtrl)
	
	
