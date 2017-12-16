<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Category;
use App\User;
use App\Asset;
use Validator;
use Illuminate\Support\Facades\Input;
use Redirect,Session;
use Auth,DB,Hash,Mail;
use App\Product;
use App\Http\Controllers\GStarBaseController;
use LucaDegasperi\OAuth2Server\Authorizer;


class CategoryController extends GStarBaseController
{
    /**
     * Display a listing of the resource.
     *
     */
	 
	 // function for Category List----
    public function index(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Category::getListOfAllCategory($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }


	 public function product_categories()
    {
		
		$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Category::getAllCategoryOfProduct();

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }

	 public function tutorial_categoriesProducts()
    {
		
		$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Category::getAllCategoryOfTutorialWithProduct();

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }



	  // function for change the order of Category----
    public function changeOrder(Request $request)
    {
		
		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Category::changeOrderofCategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	// function for Add Category----
    public function create(Request $request)
    {
		
			$responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$category = Category::createCategory($request);
				if(!Session::has('msg'))
				{	
					if($category->save())
					{ 
						$lastinsertID = $category->id; 
						$result_array = array();

						$result_array['count'] = count($lastinsertID);
						$result_array['status'] = 'success';
						$result_array['cat_id'] = $lastinsertID;
						$result_array['msg'] = CategoryController::MSG_ADDED_CATEGORY;
						$responseContent = $result_array;

					}
					else
					{
					   $responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
					}
				}
				else{
				     $responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
			}

			return $this->reponseBuilder($responseContent);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 
  // function for Edit Category(GET)----
       public function editCategory($id)
     {
		   $result_array = array();
           $responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,false);
		   if(empty($responseContent))
		   {
			$userDetail = Category::getCategoryDetail($id);
			$result_array['count'] = count($userDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetail;				
		    $responseContent = $result_array;
          }
	  return $this->reponseBuilder($responseContent);
	}
	
	 // function for Edit Category(POST)----
	public function updateCategory(Request $request)
    {
		   $categoryUpdate = 0;
			$responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
			$result_array = array();
		   if(empty($responseContent))
		   {
				$cat_id = $request->input('cat_id');
				$category = Category::editCategory($request);
				//print_r($category); exit;
				$categoryUpdate = DB::table('category')->where('id', $cat_id)->update($category);
				if(!Session::has('msg'))
				{ 
			      if($categoryUpdate){
					$result_array['status'] = 'success';
				    $result_array['msg'] = CategoryController::MSG_RECORD_UPDATED;
				    $responseContent = $result_array;
				  }
				   else
				   {
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,CategoryController::MSG_NO_UPDATE);
				   
			       }	
				}
				else
				{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }
		return   $this->reponseBuilder($responseContent);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 
	 // function for Delete Category----
    public function destroy(Request $request)
    {
		$result_array = array();
		$responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
		if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$id = $request->input('id');
		$module="category";
		$productdata=Product::getProductListByCategory($id);
		$productList= array(); 
		$imageList= array(); 
		//print_r($productdata);die;
		if(!empty($productdata['category_detail']['products']) )
		{
			$i=0;
			$prdArray = array();	
			foreach($productdata['category_detail']['products'] as $key=>$val)
			{
				$ppid = $val['pro_id'];
				$prdArray[$ppid]['name'] = $val['product_name'];
			}
			$result_array['msg'] = CategoryController::MSG_PRODUCT_EXIST;
			$result_array['data'] = $prdArray;
			$result_array['count'] = count($prdArray);
			$result_array['status'] = 'fail';
			return json_encode($result_array);
		} else {
			$GetTutorial = DB::table('video_tutorials')->where('category_id',$id)->get();
			if(!empty($GetTutorial)){
				$GetTutorialSubcat = DB::table('tutorial_subcat')->where('category_id',$id)->get();
				if(!empty($GetTutorialSubcat)){
					$result_array['msg'] = CategoryController::MSG_TUTORIAL_EXIST.' '.GStarBaseController::MSG_TUTORIAL_SUBCAT_EXIST;
							$result_array['data'] = null;
							$result_array['count'] = 1;
							$result_array['status'] = 'fail';
							return json_encode($result_array);
				}
				$result_array['msg'] = CategoryController::MSG_TUTORIAL_EXIST;
				$result_array['data'] = null;
				$result_array['count'] = 1;
				$result_array['status'] = 'fail';
				return json_encode($result_array);
			}else{
				$deleteImages = DB::table('asset_mapping')->where(array('module_id' =>$id,'module' =>'category'))->delete();
				$delete2=DB::table('product_dummy')->where('category_id',$id)->update(['is_deleted'=>1]);
				$disableCategory = DB::table('category')->where('id', $id)->delete();
				$disableCategory2 = DB::table('tutorial_subcat')->where('category_id', $id)->delete();
				if($disableCategory || $deleteImages)
				{
					GStarBaseController:: deleteLog('category',$id);
					$result_array['status'] = 'success';
					$result_array['msg'] = CategoryController::MSG_RECORD_DELETED;
					$responseContent = $result_array;
				} else {
					$responseContent  = $this->errorResponseBuilder(CategoryController::ERROR_CATEGORY_NOT_EXIST, CategoryController::MSG_CATEGORY_NOT_EXIST);
				}
			}			
		}
		return $this->reponseBuilder($responseContent);
    }

	// function for Upload file to library----
	 public function uploadFileToLibrary(Request $request)
    {
		
	         $result_array = array(); 
			$uploadFile = Category::uploadFileToLibrary($request);
			
			if(!$uploadFile)
			{
				if(Session::has('msg'))
				{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,
				   Session::get('msg'));
					return $this->reponseBuilder($responseContent);
				}
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_FILE_NOT_UPLOADED,CategoryController::MSG_FILE_NOT_UPLOADED);					
				return $this->reponseBuilder($responseContent);
			}
			$title = trim($request->input('title'));
			$type = $uploadFile['type'];
			$Imgtype = array('jpg','JPG','png','PNG','GIF','gif');
			if(in_array($type, $Imgtype)){
				$type = 'image';
			}
			$Videotype = array('mp4','MP4','3gp','3GP');
			if(in_array($type, $Videotype)){
				$type = 'video';
			}
			if(!Session::has('msg')){	
				
				$module = trim($request->input('module'));
				if($module =='product' && Auth::user()->role ==CategoryController::G_TRAINER_ROLE_ID)
				{
					$saveFile = DB::table('asset_library')->insert([
					['name' =>$uploadFile['name']['filename'],'path' =>$uploadFile['name']['targetpath'], 'type' => $type,'status' => '1','title'=>$title,'approved_status'=>'0','request_userid'=>Auth::user()->id]
				
               		]);
               		 if($saveFile){ 
			            $lastinsertID = DB::getPdo()->lastInsertId();
						$result_array['count'] = count($lastinsertID);
						$result_array['status'] = 'success';
						$result_array['asset_id'] = $lastinsertID;
						$result_array['msg'] = CategoryController::MSG_UPLOADED_FILE_ADMIN;
						
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
				
				}
				else{
				
					$saveFile = DB::table('asset_library')->insert([
					['name' =>$uploadFile['name']['filename'],'path' =>$uploadFile['name']['targetpath'], 'type' => $type,'status' => '1','title'=>$title,'approved_status'=>'1','request_userid'=>Auth::user()->id]
				
               		]);
               		 if($saveFile){ 
			            $lastinsertID = DB::getPdo()->lastInsertId();
						$result_array['count'] = count($lastinsertID);
						$result_array['status'] = 'success';
						$result_array['asset_id'] = $lastinsertID;
						$result_array['msg'] = CategoryController::MSG_UPLOADED_FILE;
						
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
				
				}

			}else{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
			
		return $this->reponseBuilder($responseContent);
      
	}
	
	// function for Attach Image to Module----
	 public function AttachImagesToModule(Request $request)
	 {		
		$result_array = array();
		$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,true);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
	   $addAsset = Category::AttachImagesToModule($request);
	   $ifExistImage=Category::ExistAttachImagesToModule($addAsset);
		//print_r($addAsset);die;
			if(!Session::has('msg'))
			{
			     if($addAsset['module']=='product' && Auth::user()->role==CategoryController::G_TRAINER_ROLE_ID){
			     	$saveAsset = DB::table('asset_mapping')->insert([
					['asset_library_id' =>$addAsset['asset_library_id'], 'module' => $addAsset['module'],'module_id' => $addAsset['module_id'],'approved_status'=>'0','request_userid'=>Auth::user()->id]
					]);
					if($saveAsset){ 
					    $result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_ATTACHED_IMAGE_ADMIN;
						
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
			     }else{
			     	$saveAsset = DB::table('asset_mapping')->insert([
					['asset_library_id' =>$addAsset['asset_library_id'], 'module' => $addAsset['module'],'module_id' => $addAsset['module_id'],'approved_status'=>'1','request_userid'=>Auth::user()->id]
					]);
					 if($saveAsset){ 
					    $result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_ATTACHED_IMAGE;
						
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
			     }
			     

			}
			else
			{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
    }
	
	// function for Attach Document to Module----
	 public function AttachDocumentToModule(Request $request)
	 {
		 
		 $result_array = array();
		 $responseContent = $this->validateUser(CategoryController::G_SUPERVISOR_ROLE_ID,true);
	     if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
	     $addAsset = Category::AttachImagesToModule($request);
	     //print_r($addAsset);
		 if(!Session::has('msg')){
		 	if($addAsset['module']=='product' && Auth::user()->role ==CategoryController::G_TRAINER_ROLE_ID){
		 		$saveAsset = DB::table('asset_mapping')->insert([
					['asset_library_id' =>$addAsset['asset_library_id'], 'module' => $addAsset['module'],'module_id' => $addAsset['module_id'],'approved_status'=>'0','request_userid'=>Auth::user()->id]
					]);
		 		 if($saveAsset){ 
			            $result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_ATTACHED_FILE_ADMIN;
						
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
		 	}
		 	else{
		 		$saveAsset = DB::table('asset_mapping')->insert([
					['asset_library_id' =>$addAsset['asset_library_id'], 'module' => $addAsset['module'],'module_id' => $addAsset['module_id'],'approved_status'=>'1','request_userid'=>Auth::user()->id]
					]);
		 		 if($saveAsset){ 
			            $result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_ATTACHED_FILE;
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
		 	}
			

			}
			else
			{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
    }
	
	// function for Edit Image to Library----
	public function EditImageToLibrary(Request $request)
    {
		
	         $result_array = array(); 
			$uploadFile = Category::EditImageToLibrary($request);
			$title = trim($request->input('title'));
			$id = trim($request->input('id'));
			$type = $uploadFile['type'];
			$typearr = array('jpg','JPG','png','PNG');
			if(in_array($type, $typearr)){
				$type = 'image';
			}
			if(!Session::has('msg'))
			{	

				if(Input::hasFile('file')!= 1){  
				$saveFile=DB::table('asset_library')->where('id', $id)->update(
									['status' => '1',
									'title'=>$title]
               		);
			}
			else{
					$saveFile=DB::table('asset_library')->where('id', $id)->update(
									['name' =>$uploadFile['name']['filename'],
									'path' =>$uploadFile['name']['targetpath'], 
									'type' => $type,
									'status' => '1',
									'title'=>$title]);
			} 
				
			   if($saveFile){ 
						
						$result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_RECORD_UPDATED;
						$result_array['data'] = $this->getAssetLibraryDetailById($id);
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
			}else{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
      
	}

// function for Edit Document to Library----
public function EditDocumentToLibrary(Request $request)
    {
		     $result_array = array(); 
			$uploadFile = Category::EditDocumentToLibrary($request);
			$title = trim($request->input('title'));
			$id = trim($request->input('id'));
			$type = $uploadFile['type'];
			$Videotype = array('mp4','MP4','3gp','3GP');
			if(in_array($type, $Videotype)){
				$type = 'video';
			}
			if(!Session::has('msg')){	

				if(Input::hasFile('file')!= 1){
					
					$saveFile=DB::table('asset_library')->where('id', $id)->update(
									['status' => '1',
									'title'=>$title]
               		);
				}
		          else
					{			
					$saveFile=DB::table('asset_library')->where('id', $id)->update(
									['name' =>$uploadFile['name']['filename'],
									'path' =>$uploadFile['name']['targetpath'], 
									'type' => $type,
									'status' => '1',
									'title'=>$title]
               		);
		         }
			 
			   if($saveFile){
						$result_array['status'] = 'success';
						$result_array['msg'] = CategoryController::MSG_EDIT_FILE;
						
						$result_array['data'] = $this->getAssetLibraryDetailById($id);
						$responseContent = $result_array;
				
				}else{
					$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_ARGUMNETS_MISSING,CategoryController::MSG_ARGUMNETS_MISSING);
				}
			}else{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
      
	}
	public function getAssetLibraryDetailById($id)
    {
		return $document_detail=DB::table('asset_library')->where('id',$id)->first();
	}

}
