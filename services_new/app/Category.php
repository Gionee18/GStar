<?php

namespace App;
use Validator;
use URL;
use Intervention\Image\Facades\Image as Image;
use File;
use Session,Auth,DB,Hash;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Category extends Authenticatable
{
	 protected $table = 'category';

// function to Add Category------------	
  public static function createCategory($request) { 
 
		$rules = array(
			'category_name'    => 'required', // category name is required
		    //'parent_category' => 'required', // parent category is required
			'description' => 'required', // description is required
			'status'=> 'required'//status is required
			
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = new Category();
		if(!$messages->first('category_name')) 
		{
			$categoryName = trim($request->input('category_name'));
			$alreadyExist = GStarBaseController::validateForExist('category',$categoryName,'category_name');
		   if($alreadyExist)
			{
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }
			$category['category_name'] = $categoryName;		   
		} 
		else 
		{
			Session::flash('msg', $messages->first('category_name'));
		} 

		// if(!$messages->first('parent_category')) {
		// 	$category['category_parent_id'] = trim($request->input('parent_category'));
	 //     }
		// else {
		// 	Session::flash('msg', $messages->first('parent_category'));
		//   }
		
	if(!$messages->first('description')) {
			$category['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
	
	if(!$messages->first('status')) {
			$category['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		  $is_product=trim($request->input('is_product')); 
		 $is_tutorial=trim($request->input('is_tutorial')); 

		 if($is_product){
		 	$category['is_product'] = 1;//trim($request->input('is_product'));
		 	if($is_tutorial){
			 		$category['is_tutorial'] = 1;//trim($request->input('is_tutorial'));
			 	}
		 }else{
			 	if($is_tutorial){
			 		$category['is_tutorial'] = 1;//trim($request->input('is_tutorial'));
			 	}
			 	else{
			 		Session::flash('msg', 'please select any one product or tutorial');
			 	}
		 }
		 
	
		return $category;
	}

	// function to Edit Category-----------
	public static function editCategory($request) { 
 
		$rules = array(
			'category_name'    => 'required', // category name is required
		    //'parent_category' => 'required', // parent category is required
			'description' => 'required', // description is required
			'status'=> 'required',//status is required
			'cat_id'=> 'required'//status is required
			
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = new Category();
		$updateCategory = array();
		if(!$messages->first('category_name')) {
			$updateCategory['category_name'] = trim($request->input('category_name'));
			} 
		else {
			Session::flash('msg', $messages->first('category_name'));
		} 
		// if(!$messages->first('parent_category')) {
		// 	$updateCategory['category_parent_id'] = trim($request->input('parent_category'));
	 //     }
		// else {
		// 	Session::flash('msg', $messages->first('parent_category'));
		//   }
		
	   if(!$messages->first('description')) {
			$updateCategory['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
	
	   if(!$messages->first('status')) {
			$updateCategory['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		  $is_product=trim($request->input('is_product')); 
		  $is_tutorial=trim($request->input('is_tutorial')); 

		  // print_r($is_product);
		  // print_r($is_tutorial); //exit;
		 if($is_product){
		 	$updateCategory['is_product'] = $is_product;
		 	
		 	if($is_tutorial){
			 		$updateCategory['is_tutorial'] = $is_tutorial;	
			 	}
			 	else{
			 		$updateCategory['is_tutorial'] = 0;
			 	}
		 }else{
			 	if($is_tutorial){
			 		$updateCategory['is_tutorial'] = $is_tutorial;	
			 		$updateCategory['is_product'] = 0;
			 	}
			 	else{
			 		Session::flash('msg', 'please select any one product or tutorial');
			 	}
		 }
		 
	
		return $updateCategory;
	}
	
	// function to get list of Categories--------
	public static function getListOfAllCategory($search_keyword,$pageNo){
		
		$finalArr = array();
		$i = 0;
		 if($pageNo) 
         {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		 }
		 else{
			 $offset= 0;
		 }
		 if($search_keyword!="")
		 {
			 $getListOfAllCategory = DB::table('category')
			  ->where('category_name','like','%'.$search_keyword.'%')->orderBy('position', 'asc')
			  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
        else
		{
		     $getListOfAllCategory = DB::table('category')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
		 
			foreach($getListOfAllCategory as $eachrow)
			{
				$j = 0;
				$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','category')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path')
								  ->get(); 
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['category_name'] = $eachrow->category_name;
				$finalArr[$i]['category_parent_id'] = NULL;//$eachrow->category_parent_id;
				$finalArr[$i]['status'] = $eachrow->status;
				$finalArr[$i]['description'] = $eachrow->description;
				$finalArr[$i]['is_product'] = $eachrow->is_product;
				$finalArr[$i]['is_tutorial'] = $eachrow->is_tutorial;
				$finalArr[$i]['position'] = $eachrow->position;
				$finalArr[$i]['created_at'] = date('d-m-y',strtotime($eachrow->created_at));
				$finalArr[$i]['updated_at'] = date('d-m-y',strtotime($eachrow->updated_at));
				if(!empty($getListOfImages)){
					foreach($getListOfImages as $eachImage){
						
				$finalArr[$i]['asset'][$j]['image_id'] = $eachImage->id;
				$finalArr[$i]['asset'][$j]['name'] = $eachImage->name;
				$finalArr[$i]['asset'][$j]['path'] = $eachImage->path;
					$j++;
				  }
				 }
				$i++;				
			}					

  return $finalArr;					
	}
	
	public static function getAllCategoryOfProduct(){
		
		     $finalArr = array();
				$i = 0;
		     $getListOfAllCategory = DB::table('category')->where('is_product',1)->orderBy('position', 'asc')->get();
         
		  return $getListOfAllCategory;
								
	}

	
	
	// function to upload file to Library--------
   public static function uploadFileToLibrary($request) { 
   
   $rules = array(
			'file'    => 'required',// file is required
			'title'    => 'required'// title is required
		 );
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$category = new Category();
		
		if(!$messages->first('title')) {
			$title = trim($request->input('title'));
			$alreadyExist = GStarBaseController::validateForExist('asset_library',$title,'title');
		   if($alreadyExist)
			{

					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		    }
		    
			$category['title'] = $title ;
	     }
		else {
			Session::flash('msg', $messages->first('title'));
		  }
		  
		if(Input::hasFile('file')){ 
			
			 $category['name'] = $category->uploadFile($request);
			 $category['type'] = $request->file('file')->getClientOriginalExtension(); 
			 $category['status'] = 1; 
		}
		else {
			Session::flash('msg', $messages->first('file'));
		  }
		return $category;
	}
	
// function to upload file--------
	private function uploadFile($request)
	{
		
		$Current_month = date('M');
		$Current_date = date('d-m-y');
		$filename='';
		$fileInfoArr= array();
		
		$fileSize = $request->file('file')->getSize();
		$extensionofFile = $request->file('file')->getClientOriginalExtension();
		$returnValue = GStarBaseController::verifyfile($extensionofFile,'file');
		if($returnValue)
		{
			if($fileSize <= GStarBaseController::FILE_SIZE)
			{ 
		     $type = array('jpg','JPG','png','PNG','GIF','gif','JPEG','jpeg');
				$filename = time().".".$request->file('file')->getClientOriginalExtension(); 
				$filename_thumb = time().".".$request->file('file')->getClientOriginalExtension(); 
				$fileInfoArr['filename'] = $filename;
				$fileInfoArr['targetpath'] = 'uploads/'. $Current_month . '/' . $Current_date;
				
				if (!is_dir(base_path('uploads'). '/'.$Current_month . '/' . $Current_date)) {
					mkdir(base_path('uploads'). '/'. $Current_month . '/' . $Current_date, 0777, true); 
				}
				
			   $targetPath = base_path('uploads'). '/'. $Current_month . '/' . $Current_date; 
				 $targetfilePath = $targetPath.'/'.$filename; 
				$isUploaded = $request->file('file')->move($targetPath,$filename);
				if($isUploaded){
					if(in_array($request->file('file')->getClientOriginalExtension(),$type))
					{
						$filename1 = GStarBaseController::createThumb($filename,$targetfilePath,$extensionofFile);
						$filename2 = GStarBaseController::createThumbMedium($filename,$targetfilePath,$extensionofFile);

					 }
						return $fileInfoArr;
				}
			}
			else {
				Session::flash('msg', GStarBaseController::MSG_FILE_SIZE);
			}
		}else{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
		
	}


	
	// function to Attach Image in to Module-------------
	public static function AttachImagesToModule($request) { 
	
    $rules = array(
			'asset_library_id'    => 'required', // asset_library_id is required
		    'module' => 'required', // module is required
			'module_id' => 'required' // module_id is required
			);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = new Category();
		$InputArr = array();
		if(!$messages->first('asset_library_id')) {
			$category['asset_library_id'] = trim($request->input('asset_library_id'));
			} 
		else {
			//Session::flash('msg', $messages->first('asset_library_id'));
			Session::flash('msg', GStarBaseController::MSG_NOT_SELECTED);
		} 
		
	   if(!$messages->first('module')) {
			$category['module'] = trim($request->input('module'));
	     }
		else {
			Session::flash('msg', $messages->first('module'));
		  }
		
	   if(!$messages->first('module_id')) {
			$category['module_id'] = trim($request->input('module_id'));
	     }
		else {
			Session::flash('msg', $messages->first('module_id'));
		  }
	
      return $category;
	}
	
	// function to Edit Image in to Library-------------
	public static function EditImageToLibrary($request) { 
   		$rules = array(
				'title'    => 'required',// title is required	
				'id'	=>	'required' 	//id is required
			);
   				$validator = Validator::make($request->input(), $rules);
				$messages = $validator->errors();
				Session::forget('msg');
				$category = new Category();
				
				
				 if(!$messages->first('title')) 
				 {
					$title = trim($request->input('title'));
					$alreadyExist = GStarBaseController::validateForExist('asset_library',$title,'title');
					if($alreadyExist)
					{
					   Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					   return ;
					}
					  $category['title'] = $title ;
					
			     }
				else {
					Session::flash('msg', $messages->first('title'));
				  }

				 if(!$messages->first('id')) {
					$category['id'] = trim($request->input('id'));
			     }
				else {
					Session::flash('msg', $messages->first('id'));
				  } 
				  
				if(Input::hasFile('file')){ 
					$category['name'] = $category->uploadImageFile($request);
					$category['type'] = $request->file('file')->getClientOriginalExtension(); 
					$category['status'] = 1; 
				}
				return $category;
	}
	
	// function to Upload Image -------------
	private function uploadImageFile($request)
	{
		
		$Current_month = date('M');
		$Current_date = date('d-m-y');
		$filename='';
		$fileInfoArr= array();
		
		$fileSize = $request->file('file')->getSize();
		$extensionofFile = $request->file('file')->getClientOriginalExtension();
		$returnValue = GStarBaseController::verifyfile($extensionofFile,'image');
		if($returnValue == 1)
		{
			if($fileSize <= GStarBaseController::FILE_SIZE)
			{ 
				$filename = time().".".$request->file('file')->getClientOriginalExtension(); 
				$fileInfoArr['filename'] = $filename;
		        $fileInfoArr['targetpath'] = 'uploads/'. $Current_month . '/' . $Current_date;
		
				if (!file_exists(base_path('uploads'). '/'.$Current_month . '/' . $Current_date)) 
				{
				  mkdir(base_path('uploads'). '/'. $Current_month . '/' . $Current_date, 0777, true); 
				}
				$targetPath = base_path('uploads'). '/'. $Current_month . '/' . $Current_date; 
				$targetfilePath = $targetPath.'/'.$filename; 
				$isUploaded = $request->file('file')->move($targetPath,$filename);
				if($isUploaded){
						$filename1 = GStarBaseController::createThumb($filename,$targetfilePath,$extensionofFile);
						$filename2 = GStarBaseController::createThumbMedium($filename,$targetfilePath,$extensionofFile);
				 }
				
					//------------------------------------------------------------------------
					return $fileInfoArr;
		//}
	}
	else 
	{
		Session::flash('msg', GStarBaseController::MSG_FILE_SIZE);
	}
		}
		else
		{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
		
	}
	
	//Function to Edit Document to Library------------ 
	public static function EditDocumentToLibrary($request) { 
   		$rules = array(
			    'title'    => 'required',// title is required	
				'id'	=>	'required' 	//id is required
			);
   				
				$validator = Validator::make($request->input(), $rules);
				$messages = $validator->errors();
				Session::forget('msg');
				$category = new Category();
				
				 if(!$messages->first('title')) {
					 $title = trim($request->input('title'));
					$alreadyExist = GStarBaseController::validateForExist('asset_library',$title,'title');
					if($alreadyExist)
					{
					   Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					   return ;
					}
					  $category['title'] = $title ;
					
			     }
				else {
					Session::flash('msg', $messages->first('title'));
				  }

				 if(!$messages->first('id')) {
					$category['id'] = trim($request->input('id'));
			     }
				else {
					Session::flash('msg', $messages->first('id'));
				  }
				  
				  if(Input::hasFile('file')){ 
					$category['name'] = $category->uploadDocumentFile($request);
					 $category['type'] = $request->file('file')->getClientOriginalExtension(); 
					 $category['status'] = 1; 
				}
				
				return $category;
   				
 }

 // function to upload Document-----------
	private function uploadDocumentFile($request)
	{
		
		$Current_month = date('M');
		$Current_date = date('d-m-y');
		$filename='';
		$fileInfoArr= array();
		
		$fileSize = $request->file('file')->getSize();
		$extensionofFile = $request->file('file')->getClientOriginalExtension();
		$returnValue = GStarBaseController::verifyfile($extensionofFile,'file');
		if($returnValue == 1)
		{
			if($fileSize <= GStarBaseController::FILE_SIZE)
			{ 
				$filename = time().".".$request->file('file')->getClientOriginalExtension(); 
				$fileInfoArr['filename'] = $filename;
				$fileInfoArr['targetpath'] = 'uploads/'. $Current_month . '/' . $Current_date;

				if (!file_exists(base_path('uploads'). '/'.$Current_month . '/' . $Current_date)) {
				mkdir(base_path('uploads'). '/'. $Current_month . '/' . $Current_date, 0777, true); 
				}
				$targetPath = base_path('uploads'). '/'. $Current_month . '/' . $Current_date; 
				$isUploaded = $request->file('file')->move($targetPath,$filename);
				if($isUploaded){
				return $fileInfoArr;	
				}
			}
			else {
				Session::flash('msg', GStarBaseController::MSG_WRONG_FILE_SIZE);
			}
		}else{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
		
		
	}

  //Function to get Category Detail----------- 
	public static function getCategoryDetail($id) 
	{ 
   		    $finalArr = array();
			$result_array = array();
			$CategoryDetails = DB::table('category')->where('id', $id)->first();
			$CategoryDetails = GStarBaseController::userdatesFormat($CategoryDetails);
			
			$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','category')
								  ->where('asset_mapping.module_id',$CategoryDetails->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path')
								  ->get(); 
								  
				$finalArr['id'] = $CategoryDetails->id;
				$finalArr['category_name'] = $CategoryDetails->category_name;
				$finalArr['category_parent_id'] = NULL;//$CategoryDetails->category_parent_id;
				$finalArr['status'] = $CategoryDetails->status;
				$finalArr['description'] = $CategoryDetails->description;
				$finalArr['is_product'] = $CategoryDetails->is_product;
				$finalArr['is_tutorial'] = $CategoryDetails->is_tutorial;
				$finalArr['created_at'] = $CategoryDetails->created_at;
				$finalArr['updated_at'] = $CategoryDetails->updated_at;
				if(!empty($getListOfImages)){
				$finalArr['image_id'] = $getListOfImages[0]->id;
				$finalArr['name'] = $getListOfImages[0]->name;
				$finalArr['path'] = $getListOfImages[0]->path;
				}else{
				$finalArr['name'] = '';
				$finalArr['path'] = '';
				}
				
		return $finalArr;
   				
 }
 
 //Function to change the order of category----------- 
	public static function changeOrderofCategory($request) 
	{
		$updated = 0;
		$rules = array(
				'orderDataArray' => 'required'// orderDataArray is required	
				
			);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$category = array();
		$updates = array();
		//$orderArr = $request->input('orderDataArray');
		
		
		 if(!$messages->first('orderDataArray')) {
					$category = $request->input('orderDataArray');
					
			     }
		else {
					Session::flash('msg', $messages->first('orderDataArray'));
				  }
				  
		if(!Session::has('msg'))
		{
			
		foreach($category as $each){
			//print_r($each);die;
			
			$updateCategoryOrder = $responseContent = DB::table('category')->where('id', $each['id'])->update(array('position'=>$each['pos']));
			if($updateCategoryOrder){
				$updated = 1;
			}
		}
		if($updated)
			{
			    $updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED;
		    }
			else
			{
				Session::flash('msg', GStarBaseController::MSG_NO_UPDATE);
			}
	 }
	 return $updates;
	}


	public static function ExistAttachImagesToModule($addAsset) { 
	
    
		Session::forget('msg');
		$InputArr = array();
		
		$exist=DB::table('asset_mapping')
					->where('asset_library_id',$addAsset['asset_library_id'])
					->where('module',$addAsset['module'])
					->where('module_id',$addAsset['module_id'])
					->count();
		if($exist){
			Session::flash('msg', GStarBaseController::ERROR_IMAGE_ATTACH_EXIST);
		}
		return ;			
		
	}



}
