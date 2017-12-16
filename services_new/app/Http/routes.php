<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::get('version',array('uses' => 'UserController@AppVersion'));

// Route::get('smtpmail',array('uses' => 'sendmail@mail'));
// Route::get('checkport',array('uses' => 'sendmail@checkport'));




//Route for login in App

Route::post('oauth/access_token', function() {

 return Response::json(Authorizer::issueAccessToken());
});

//Route for forgot password in App
Route::post('user/appForgetPassword', array('uses' => 'UserController@forgetPasswordApp'));


 //App API 
Route::group(['prefix' => 'v1', 'middleware' => 'oauth' , 'before' => 'oauth|auth'], function()
{





// Update App Data
Route::get('app/update',array('uses' => 'UserController@appUpdate'));
Route::get('app/updateCount',array('uses' => 'UserController@appUpdateCount'));


//route to Get UserData
Route::get('user/data',array('uses' => 'UserController@userSession'));


Route::post('save/user/trail',array('uses' => 'UserController@SaveUserTrail'));

Route::get('user/activation/request',array('uses' => 'UserController@UserActivationRequest'));
	

// route to List Home Banner Images
Route::get('assets/homeBannerImages',array('uses' => 'AssetController@listImagesofHomeBannerImages'));



// route to getProductListByCategory
Route::get('category/product/{cat_id?}',array('uses' => 'ProductController@getProductListByCategory'));

//Route::get('category/tutorial/{cat_id?}',array('uses' => 'TutorialController@getTutorialwithProductByCategory'));


Route::get('productDetails',array('uses' => 'ProductController@MonthlyProductDetails'));

//route to ProductDetails
Route::post('product/details',array('uses'=>'ProductController@ProductDetails'));
	
// route to changePassword
Route::post('user/changeAppPassword',array('uses' => 'UserController@ChangeAppPassword'));


//Route::get('userSession', "UserController@sessionData");

//Edit User Details
Route::match(['get', 'post'],'appUser/edit/{id?}',array('uses' => 'UserController@editApp'));


//route to Zone list
Route::post('address/zone',array('uses' => 'UserController@AppZoneList'));

// route to State List
Route::post('address/state',array('uses' => 'UserController@AppStateList'));

// route to City List
Route::post('address/city',array('uses' => 'UserController@AppCityList'));


// route to National Distributor(ND) List
Route::get('user/nd/list',array('uses' => 'UserController@NdList'));

// route to Regional Distributor(RD) List
Route::get('user/rd/list',array('uses' => 'UserController@RdList'));


Route::post('app/list/tutorial',array('uses' => 'TutorialController@ApplistTutorial'));

Route::post('news/update/list',array('uses' => 'NewsUpdateController@AppNewsUpdateList'));

Route::post('news/update/topics',array('uses' => 'NewsUpdateController@AppNewsUpdateTopicsList'));

Route::post('news/update/readstatus',array('uses' => 'NewsUpdateController@UpdateReadStatus'));


Route::post('savedevice',array('uses' => 'UserController@SaveDevice'));


Route::post('app/logout', "UserController@appLogout");



//// Recommendor

Route::post('app/search/mf/model/list',array('uses' => 'RecommenderController@AppMfModelList'));

Route::post('app/search/attribute/list',array('uses' => 'RecommenderController@AppSearchAttributeList'));

Route::post('app/search/recommendor',array('uses' => 'RecommenderController@SearchRecommendor'));

//Route::post('app/search/bymanufacturer',array('uses' => 'RecommenderController@SearchByManufacturer'));



Route::post('app/phone/compair',array('uses' => 'RecommenderController@AppPhoneCompair'));

//end

});  // end oAuth Route


Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/', 'HomeController@index');



//Route::get('productDetails',array('uses' => 'ProductController@MonthlyProductDetails'));
	
// route to process the login
Route::post('login', array('uses' => 'UserController@login'));

// route to process the logout
Route::get('logout', array('uses' => 'UserController@Logout'));

// route to List Categories
Route::get('categories',array('uses' => 'CategoryController@index'));

Route::get('product/categories',array('uses' => 'CategoryController@product_categories'));



// route to create new Category
Route::post('category/add',array('uses' => 'CategoryController@create'));

//route to Edit Category
Route::get('category/edit/{id}',array('uses' => 'CategoryController@editCategory'));

//route to update Category
Route::post('category/update',array('uses' => 'CategoryController@updateCategory'));

//route to Delete Category
Route::post('category/delete',array('uses'=>'CategoryController@destroy'));


// route to List Product
Route::get('products',array('uses' => 'ProductController@index'));

// route to create new Product
Route::post('product/add',array('uses' => 'ProductController@create'));

//route to Edit Product
Route::get('product/edit/{id}',array('uses' => 'ProductController@editProduct'));



//route to Update Product
Route::post('product/update',array('uses' => 'ProductController@updateProduct'));

//route to Edit Image Library
Route::post('library/editImage',array('uses'=>'CategoryController@EditImageToLibrary'));

//route to Delete Product
Route::post('product/delete',array('uses'=>'ProductController@destroy'));

//route to Upload Files to library
Route::post('library/uploadFile',array('uses'=>'CategoryController@uploadFileToLibrary'));

//Check User is Login or not with the help of 
Route::get('isLoggedIn',array('uses' => 'UserController@isLoggedIn'));

//route to Edit Document Library
Route::post('asset/editDocumentToLibrary',array('uses'=>'CategoryController@EditDocumentToLibrary'));

//route to Add Images in to Module
Route::post('assets/attachImage',array('uses'=>'CategoryController@AttachImagesToModule'));

//route to Add Document in to Module
Route::post('assets/attachDocument',array('uses'=>'CategoryController@AttachDocumentToModule'));

// route to List Home Banner Images
Route::get('assets/homeBanner',array('uses' => 'AssetController@listImagesofHomeBanner'));

// route to Upload Home Banner Images
Route::post('assets/homeBanner/upload',array('uses' => 'AssetController@UploadFileToHomeBanner'));

// route to Delete Home Banner Images
Route::post('assets/homeBanner/delete',array('uses' => 'AssetController@deleteHomeBannerImage'));

// route to list of Images
Route::get('assets/images',array('uses' => 'AssetController@index'));

// route to list of documents
Route::get('assets/documents',array('uses' => 'AssetController@documents'));

//Deattach Image from Module
Route::post('image/delete',array('uses'=>'AssetController@destroy')); 

//Deattach Document from Module
Route::post('document/delete',array('uses'=>'AssetController@destroy')); 

//Delete asset from library
Route::post('libraryImage/delete',array('uses'=>'AssetController@libraryAssetDelete')); 

//Delete document from library
Route::post('libraryDocument/delete',array('uses'=>'AssetController@libraryAssetDelete')); 

// route to display Images by Module
Route::post('assets/moduleImages',array('uses' => 'AssetController@DisplayImages'));

// route to display Documents by Module
Route::post('assets/moduleDocuments',array('uses' => 'AssetController@DisplayDocuments'));

//route to Users List 
Route::get('users',array('uses' => 'UserController@index'));

//route to Users  Archive List List 
//Route::post('users/archive',array('uses' => 'UserController@ArchiveUserList'));

// route to  forget password
Route::post('user/forgetPassword', array('uses' => 'UserController@forgetPassword'));

// route to changePassword
Route::post('user/Changepassword',array('uses' => 'UserController@Changepassword'));

// route to resetPassword by Admin
Route::post('user/resetPasswordByAdmin',array('uses' => 'UserController@resetPasswordByAdmin'));

// route to create new User
Route::post('user/add',array('uses' => 'UserController@create')); 

//route to Edit User 
Route::match(['get', 'post'],'user/edit/{id?}',array('uses' => 'UserController@edit'));

// route to Delete User
Route::post('user/delete',array('uses' => 'UserController@destroy'));

// route to Delete AllUser
Route::post('user/deleteAll',array('uses' => 'UserController@deleteAll'));

// route to ListUser dropdown for Supervisor
Route::get('user/supervisorsList',array('uses' => 'UserController@supervisorsList'));

//route to Import excel file (USER)
Route::post('user/import',array('uses'=>'UserController@importUser'));




//route to Zone list
Route::get('address/zone',array('uses' => 'UserController@ZoneList'));

// route to State List
Route::post('address/state',array('uses' => 'UserController@StateList'));

// route to City List
Route::post('address/city',array('uses' => 'UserController@CityList'));

// route to  Add Zone List
//Route::post('address/addzone',array('uses' => 'UserController@AddZoneList'));

// route to Add State List
//Route::post('address/addstate',array('uses' => 'UserController@AddStateList'));

// route to Add City List
//Route::post('address/addcity',array('uses' => 'UserController@AddCityList'));

// route to National Distributor(ND) List
Route::get('user/nd',array('uses' => 'UserController@NdList'));

// route to Regional Distributor(RD) List
Route::get('user/rd',array('uses' => 'UserController@RdList'));

// route to Regional Distributor(RD) List
Route::get('user/rt',array('uses' => 'UserController@RtList'));

// route to Delete All Asset from library
Route::post('asset/deleteAll',array('uses' => 'AssetController@deleteAllAssets'));



// route to Ordering Category
Route::post('category/order',array('uses' => 'CategoryController@changeOrder'));

// route to Ordering Category
Route::post('product/order',array('uses' => 'ProductController@changeOrder'));


Route::post('topic/order',array('uses' => 'NewsUpdateController@changeOrder'));

Route::post('topic/category/order',array('uses' => 'NewsUpdateController@changeOrderCategory'));

Route::post('topic/subcategory/order',array('uses' => 'NewsUpdateController@changeOrderSubcategory'));


Route::post('tutorial/order',array('uses' => 'TutorialController@changeOrder'));

Route::post('tutorial/subcat/order',array('uses' => 'TutorialController@changeOrderSubcategory'));


Route::post('specification/category/order',array('uses' => 'RecommenderController@changeOrder'));

Route::post('specification/subcategory/order',array('uses' => 'RecommenderController@changeOrderSubcategory'));




// route to import CSV for internal use
//Route::post('insert/zone',array('uses' => 'UserController@insertZoneFromCSV'));

// route to import CSV for internal use
//Route::post('insert/state',array('uses' => 'UserController@insertStateFromCSV'));

// route to import CSV for internal use
//Route::post('insert/city',array('uses' => 'UserController@insertCityFromCSV'));



Route::post('product/approved/list',array('uses' => 'ProductController@approvedproductlist'));
//Route::get('product/approved/admin/{id}/{request_id}',array('uses' => 'ProductController@approvedproductById'));
Route::post('product/approved/admin',array('uses' => 'ProductController@approvedproductById'));
Route::post('product/approved/byAdmin',array('uses' => 'ProductController@ApprovedRejectProductByAdmin'));


Route::post('user/updateprofile/list',array('uses' => 'UserController@updateprofileRequestList'));
Route::get('user/updateprofile/{id}/{userid}',array('uses' => 'UserController@updateprofileById'));
Route::post('user/updateprofile/approved',array('uses' => 'UserController@updateprofileApproved'));



// route to account activation request list
Route::post('user/activation/request/list',array('uses' => 'UserController@accountActivationList'));
Route::post('user/activate',array('uses' => 'UserController@activateUser'));






Route::get('tutorial/categories/products',array('uses' => 'TutorialController@tutorial_categoriesProducts'));

Route::get('list/video/tutorial',array('uses' => 'TutorialController@listTutorial'));
Route::post('add/video/tutorial',array('uses' => 'TutorialController@createTutorial'));
Route::post('edit/video/tutorial',array('uses' => 'TutorialController@updateTutorial'));
Route::get('video/tutorial/{id}',array('uses' => 'TutorialController@VideoTutorialById'));
Route::post('delete/video/tutorial',array('uses' => 'TutorialController@deleteTutorial'));


Route::get('list/tutorial/subcat',array('uses' => 'TutorialController@listTutorialSubcategory'));
Route::post('add/tutorial/subcat',array('uses' => 'TutorialController@createTutorialSubcategory'));
Route::match(['get', 'post'],'edit/tutorial/subcat/{id}',array('uses' => 'TutorialController@updateTutorialSubcategory'));
Route::post('delete/tutorial/subcat',array('uses' => 'TutorialController@deleteTutorialSubcategory'));


Route::get('list/category/news',array('uses' => 'NewsUpdateController@listCategoryNews'));
Route::get('category/news/{id}',array('uses' => 'NewsUpdateController@CategoryNewsById'));
Route::post('add/category/news',array('uses' => 'NewsUpdateController@createCategoryNews'));
Route::post('edit/category/news',array('uses' => 'NewsUpdateController@updateCategoryNews'));
Route::post('delete/category/news',array('uses'=>'NewsUpdateController@deleteCategoryNews'));



Route::get('list/subcategory/news',array('uses' => 'NewsUpdateController@listSubCategoryNews'));
Route::get('subcategory/news/{id}',array('uses' => 'NewsUpdateController@SubCategoryNewsById'));
Route::post('add/subcategory/news',array('uses' => 'NewsUpdateController@createSubCategoryNews'));
Route::post('edit/subcategory/news',array('uses' => 'NewsUpdateController@updateSubCategoryNews'));
Route::post('delete/subcategory/news',array('uses'=>'NewsUpdateController@deleteSubCategoryNews'));



Route::get('list/news/topic',array('uses' => 'NewsUpdateController@listNewsTopic'));
Route::get('news/topic/{id}',array('uses' => 'NewsUpdateController@newsTopicById'));
Route::post('add/news/topic',array('uses' => 'NewsUpdateController@createNewsTopic'));
Route::post('edit/news/topic',array('uses' => 'NewsUpdateController@updateNewsTopic'));
Route::post('delete/news/topic',array('uses' => 'NewsUpdateController@deleteNewsTopic'));


Route::get('catsubcat/news/topic',array('uses' => 'NewsUpdateController@CategorySubcategory'));




Route::get('list/manufacturer',array('uses' => 'RecommenderController@listManufacturer'));
Route::get('manufacturer/{id}',array('uses' => 'RecommenderController@ManufacturerById'));
Route::post('add/manufacturer',array('uses' => 'RecommenderController@createManufacturer'));
Route::post('edit/manufacturer',array('uses' => 'RecommenderController@updateManufacturer'));
Route::post('delete/manufacturer',array('uses' => 'RecommenderController@deleteManufacturer'));


Route::get('list/model',array('uses' => 'RecommenderController@listModel'));
Route::get('model/{id}',array('uses' => 'RecommenderController@ModelById'));
Route::post('add/model',array('uses' => 'RecommenderController@createModel'));
Route::post('edit/model',array('uses' => 'RecommenderController@updateModel'));
Route::post('delete/model',array('uses' => 'RecommenderController@deleteModel'));



Route::get('list/specification',array('uses' => 'RecommenderController@listSpecification'));


Route::get('list/specification/category',array('uses' => 'RecommenderController@listSpecificationCategory'));

Route::get('list/specification/subcategory',array('uses' => 'RecommenderController@listSpecificationSubcategory'));


Route::post('add/model/specification',array('uses' => 'RecommenderController@addModelSpecification'));

Route::match(['get', 'post'],'edit/model/specification/{id}',array('uses' => 'RecommenderController@updateModelSpecification'));


Route::get('spec/category/{id}',array('uses' => 'RecommenderController@SpecificationCategoryById'));
Route::post('add/spec/category',array('uses' => 'RecommenderController@createSpecificationCategory'));
Route::post('edit/spec/category',array('uses' => 'RecommenderController@updateSpecificationCategory'));
Route::post('delete/spec/category',array('uses' => 'RecommenderController@deleteSpecificationCategory'));


Route::get('spec/subcategory/{id}',array('uses' => 'RecommenderController@SpecificationSubCategoryById'));
Route::post('add/spec/subcategory',array('uses' => 'RecommenderController@createSpecificationSubCategory'));
Route::post('edit/spec/subcategory',array('uses' => 'RecommenderController@updateSpecificationSubCategory'));
Route::post('delete/spec/subcategory',array('uses' => 'RecommenderController@deleteSpecificationSubCategory'));

//Route::post('changepriority/spec/subcategory',array('uses' => 'RecommenderController@changePrioritySpec'));

Route::get('attribute/spec/subcategory/{id}',array('uses' => 'RecommenderController@AttributeSubCategoryById'));
Route::post('add/attribute/spec/subcategory',array('uses' => 'RecommenderController@addAttributeSpec'));
Route::post('edit/attribute/spec/subcategory',array('uses' => 'RecommenderController@editAttributeSpec'));
Route::post('delete/attribute/spec/subcategory',array('uses' => 'RecommenderController@deleteAttributeSpec'));





Route::get('report/activeinactive/user/list',array('uses' => 'AuditTrailController@ActiveInactiveUserList'));

Route::get('report/user/audittrail',array('uses' => 'AuditTrailController@UserAuditTrail'));


Route::match(['get', 'post'],'disclaimer',array('uses' => 'UserController@Disclaimer'));


Route::get('dashboard/data',array('uses' => 'DashboardController@index'));



// CORN
// route to deactivate users whos login is 30 day older
Route::get('user/deactivate',array('uses' => 'UserController@deactivateUsers'));

// deactivate device if user's session expired
Route::get('user/logoutdevice',array('uses' => 'UserController@logoutDevice'));


// Cron to inactive users will be removed from database after an year.
Route::get('delete/archive/user',array('uses' => 'UserController@DeleteArchiveUsers'));

// CORN




});

