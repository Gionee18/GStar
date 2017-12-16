

var app = angular.module('cubeWebApp', [
	'ngRoute',
	'angular-loading-bar',
	'ngAnimate',
	'easypiechart',
	'datatables',
	'toaster',
	'ngStorage',
	'ui.bootstrap',
	"dndLists",	
	'ui.tinymce',
	'ngSanitize'
	
	
]);

app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	cfpLoadingBarProvider.includeBar = true;
	cfpLoadingBarProvider.includeSpinner = true;
	cfpLoadingBarProvider.latencyThreshold = 100;
}]);

/**
 * Configure the Routes
 */
app.config(['$routeProvider', function ($routeProvider) {
	$routeProvider
		 .when("/", {
			redirectTo:'/dasboard'
		}) 
		.when("/dasboard", {
			templateUrl: "templates/dashboard.html", 
			controller: "dasboardCtrl", 
			title: 'Dashboard'
		})
		.when("/tables/simple", {
			templateUrl: "templates/tables.html", 
			controller: "mainCtrl", 
			title: 'Tables'
		})
		.when("/tables/tables-advanced", {
			templateUrl: "templates/tables-advanced.html", 
			controller: "mainCtrl", 
			title: 'Advanced tables'
		})
		
		.when("/admin/users", {
			templateUrl: "templates/admin/users.html", 
			controller: "usersCtrl",
			title: 'Users'
		})
		.when("/admin/settings", {
			templateUrl: "templates/admin/setting.html", 
			controller: "usersCtrl",
			title: 'Settings'
		})
		.when("/error-404-v2", {
			templateUrl: "templates/error-404-v2.html", 
			controller: "mainCtrl",
			title: 'Error 404'
		})
		.when("/error-404", {
			templateUrl: "templates/error-404-v2.html", 
			controller: "mainCtrl",
			title: 'Error 404'
		})
		//Approval
		.when("/approval/products", {
			templateUrl: "templates/approval/product-approval.html", 
			controller: "approvalProdCtrl",
			title: 'productapproval'
		})
		.when("/approval/product-compare/:pid/:request_userid/:id", {
			templateUrl: "templates/approval/product-compare.html", 
			controller: "compareProdCtrl",
			title: 'product-compare'
		})
		.when("/approval/edit-users-profile", {
			templateUrl: "templates/approval/user-approval.html", 
			controller: "editUserProfileCtrl",
			title: 'Edit-user-approval'
		})
		.when("/approval/user-compare/:id/:uid", {
			templateUrl: "templates/approval/user-profile-compare.html", 
			controller: "compareEditUserCtrl",
			title: 'userprofilecompare'
		})
		.when("/approval/users-activation", {
			templateUrl: "templates/approval/user-activation.html", 
			controller: "userActivationCtrl",
			title: 'user activation'
		})
		//Category Section
		.when("/manage-category", {
			templateUrl: "templates/category/manage-category.html", 
			controller: "manageCategoryCtrl", 
			title: 'Manage Category'
		})
		//Add Category
		.when("/add-category", {
			templateUrl: "templates/category/add-category.html", 
			controller: "addCategoryCtrl", 
			title: 'Add Category'
		})
		//Edit Category
		.when("/edit-category/:id", {
			templateUrl: "templates/category/edit-category.html", 
			controller: "editCategoryCtrl", 
			title: 'Edit Category'
		})
		
		//Product Section
		.when("/products/manage-products", {
			templateUrl: "templates/products/manage-products.html", 
			controller: "manageProductsCtrl", 
			title: 'Manage Products'
		})
		//Add Product
		.when("/products/add-product", {
			templateUrl: "templates/products/add-product.html", 
			controller: "addProductCtrl", 
			title: 'Add product'
		})
		//Edit Product
		.when("/products/edit-product/:id", {
			templateUrl: "templates/products/edit-product.html", 
			controller: "editProductCtrl", 
			title: 'Edit product'
		})
		
		//View Product News
		.when("/admin/news", {
			templateUrl: "templates/admin/news.html", 
			controller: "newsCtrl", 
			title: 'news'
		})
		//Add Product News
		.when("/admin/add-news", {
			templateUrl: "templates/admin/add-news.html", 
			controller: "addNewsCtrl", 
			title: 'Add news'
		})
		//edit Product News
		.when("/admin/edit-news/:id", {
			templateUrl: "templates/admin/edit-news.html", 
			controller: "editNewsCtrl", 
			title: 'Edit news'
		})
		//Add User
		.when("/admin/add-user", {
			templateUrl: "templates/admin/add-user.html", 
			controller: "addUserCtrl", 
			title: 'Add user'
		})
		//Edit User
		.when("/admin/edit-user/:uid", {
			templateUrl: "templates/admin/edit-user.html", 
			controller: "editUserCtrl", 
			title: 'Edit user'
		})
		// Library 
		.when("/admin/image-library", {
			templateUrl: "templates/admin/image-library.html", 
			controller: "imageLibraryCtrl", 
			title: 'Image Library'
		})
		
		.when("/admin/doc-library", {
			templateUrl: "templates/admin/doc-library.html", 
			controller: "docLibraryCtrl", 
			title: 'Document Library'
		})
		
		// Users 
		.when("/admin/view-profile", {
			templateUrl: "templates/admin/view-profile.html", 
			controller: "mainCtrl", 
			title: 'View Profile'
		})
		
		.when("/admin/settings", {
			templateUrl: "templates/admin/settings.html", 
			controller: "settingsCtrl", 
			title: 'Settings'
		})
		
		.when("/admin/change-password", {
			templateUrl: "templates/admin/change-password.html", 
			controller: "mainCtrl", 
			title: 'Change Password'
		})

		//tutorials Section
		.when("/tutorial/manage-tutorial", {
			templateUrl: "templates/tutorial/manage-tutorial.html", 
			controller: "manageTutorialCtrl", 
			title: 'Manage Tutorial'
		})
		.when("/tutorial/manage-subcategory", {
			templateUrl: "templates/tutorial/manage-subcategory.html", 
			controller: "managesubcategoryCtrl", 
			title: 'Manage Sub-Category'
		})
		//Add Product
		.when("/tutorial/add-tutorial", {
			templateUrl: "templates/tutorial/add-tutorial.html", 
			controller: "addTutorialCtrl", 
			title: 'Add Tutorial'
		})
		//Add Product
		.when("/tutorial/edit-tutorial/:id", {
			templateUrl: "templates/tutorial/edit-tutorial.html", 
			controller: "editTutorialCtrl", 
			title: 'Edit Tutorial'
		})
		
		//Updates
		.when("/updates/category-listing", {
			templateUrl: "templates/updates/category-listing.html", 
			controller: "categoryListingCtrl", 
			title: 'Category Listing'
		})
		.when("/updates/add-category", {
			templateUrl: "templates/updates/add-category.html", 
			controller: "categoryAddCtrl", 
			title: 'Add Category'
		})
		.when("/updates/subcategory-listing", {
			templateUrl: "templates/updates/subcategory-listing.html", 
			controller: "subcategoryListingCtrl", 
			title: 'Subcategory Listing'
		})
		.when("/updates/add-subcategory", {
			templateUrl: "templates/updates/add-subcategory.html", 
			controller: "subcategoryAddCtrl", 
			title: 'Add Subcategory'
		})
		.when("/updates/topic-listing", {
			templateUrl: "templates/updates/topic-listing.html", 
			controller: "topicListingCtrl", 
			title: 'Topic Listing'
		})
		.when("/updates/add-topic", {
			templateUrl: "templates/updates/add-topic.html", 
			controller: "topicAddCtrl", 
			title: 'Add Topic'
		})
		.when("/updates/edit-topic/:id", {
			templateUrl: "templates/updates/edit-topic.html", 
			controller: "topicEditCtrl", 
			title: 'Edit Topic'
		})
		//Recommender
		.when("/recommender/manufacturer-listing", {
			templateUrl: "templates/recommender/manufacturer-listing.html", 
			controller: "manufacturerListingCtrl", 
			title: 'Manufacturer Listing'
		})
		.when("/recommender/add-manufacturer", {
			templateUrl: "templates/recommender/add-manufacturer.html", 
			controller: "manufacturerAddCtrl", 
			title: 'Add Manufacturer'
		})
		.when("/recommender/model-listing", {
			templateUrl: "templates/recommender/model-listing.html", 
			controller: "modelListingCtrl", 
			title: 'Model Listing'
		})
		.when("/recommender/add-model", {
			templateUrl: "templates/recommender/add-model.html", 
			controller: "modelAddCtrl", 
			title: 'Add Model'
		})
		.when("/recommender/edit-model/:id", {
			templateUrl: "templates/recommender/edit-model.html", 
			controller: "modelEditCtrl", 
			title: 'Edit Model'
		})
		.when("/recommender/disclaimer", {
			templateUrl: "templates/recommender/disclaimer.html", 
			controller: "disclaimerCtrl", 
			title: 'Disclaimer'
		})
		.when("/specification/add-specification/:model_id/:brand", {
			templateUrl: "templates/specification/add-specification.html", 
			controller: "addSpecCtrl", 
			title: 'Add Specification'
		})
		.when("/specification/edit-specification/:model_id/:brand", {
			templateUrl: "templates/specification/edit-specification.html", 
			controller: "editSpecCtrl", 
			title: 'Edit Specification'
		})
		//specification
		.when("/specification/category-listing", {
			templateUrl: "templates/specification/category-listing.html", 
			controller: "catlistCtrl", 
			title: 'Category Listing'
		})
		.when("/specification/subcategory-listing", {
			templateUrl: "templates/specification/subcategory-listing.html", 
			controller: "subcatlistCtrl", 
			title: 'Subcategory Listing'
		})
		.when("/specification/attribute-listing/:id", {
			templateUrl: "templates/specification/attribute-listing.html", 
			controller: "attributelistCtrl", 
			title: 'Attribute Listing'
		})
		.when("/specification/view-specification/:id/:brand", {
			templateUrl: "templates/specification/view-specification.html", 
			controller: "viewspecificationCtrl", 
			title: 'View Specification'
		})
		.when("/user-reports/user-listing", {
			templateUrl: "templates/user-reports/user-listing.html", 
			controller: "userlistingCtrl", 
			title: 'User Listing'
		})
		.when("/user-reports/audit-trail", {
			templateUrl: "templates/user-reports/audit-trail.html", 
			controller: "auditTrailCtrl", 
			title: 'Audit Trail'
		})
		 .otherwise({
			redirectTo:'/error-404'
		}); 
		
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
        $rootScope.title = current.$$route.title;
	});
}]);

app.service('dataService',["$http","toaster",function($http,toaster) {
	delete $http.defaults.headers.common['X-Requested-With'];
	this.getData = function(param,url,method="GET") {
		if(method=="GET"){
			return $http({
				method: method,
				url: url,
				params: param,
				headers: { 'Content-Type' : 'application/json'}                  
			}).error(function(){
				toaster.pop('error', msg['0000']);
			});
		}else{
			return $http.post(url,param)
			.error(function(){
				toaster.pop('error', msg['0000']);
			});
		}
		
	}

}]);

