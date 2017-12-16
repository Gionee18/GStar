<?php

namespace App;
use Validator;
use Session,Auth,DB,Hash;
//use Input;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Product extends Authenticatable
{
	 protected $table = 'product';

	
 // Function to get list of Products---------------------
	public static function getListOfAllProducts($search_keyword,$pageNo){
		$brand=strtolower(GStarBaseController::GIONEE_BRAND);
	  if($pageNo) 
         {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		 }
		 else
		 {
			 $offset= 0;
		 }
		
		$finalArr = array();
		$i = 0;
		 if($search_keyword!=""){
		 $getListOfAllProducts = DB::table('product') 
								->join('category', 'category.id', '=', 'product.category_id')
								->where('product.product_name','like','%'.$search_keyword.'%')
								->orWhere('category.category_name','like','%'.$search_keyword.'%')
								->select('product.id','product.category_id','product.product_name',
								'product.product_desc','product.status','product.desc1','product.desc2','product.desc3',
								'product.created_at','product.launch_date','product.updated_at','product.position','product.new_product_flag','category.category_name')
								->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->orderBy('product.position','asc')->orderBy('product.new_product_flag','desc')
								->get();
       }
	   else{
		$getListOfAllProducts = DB::table('product') 
								->join('category', 'category.id', '=', 'product.category_id')
								->select('product.id','product.category_id','product.product_name',
								'product.product_desc','product.status','product.desc1','product.desc2','product.desc3',
								'product.created_at','product.launch_date','product.updated_at','product.position','product.new_product_flag','category.category_name')
								->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->orderBy('product.position','asc')->orderBy('product.new_product_flag','desc')->get();
	   }
	   foreach($getListOfAllProducts as $eachrow){
				$j = 0;
			
				$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path','asset_library.type')
								  ->get();
          						  
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['product_name'] = $eachrow->product_name;
				$finalArr[$i]['category_id'] = $eachrow->category_id;
				$finalArr[$i]['category_name'] = $eachrow->category_name;
				$finalArr[$i]['product_desc'] = $eachrow->product_desc;
				$finalArr[$i]['desc1'] = $eachrow->desc1;
				$finalArr[$i]['desc2'] = $eachrow->desc2;
				$finalArr[$i]['desc3'] = $eachrow->desc3;
				$finalArr[$i]['new_product_flag'] = $eachrow->new_product_flag;
				$finalArr[$i]['position'] = $eachrow->position;
				$finalArr[$i]['status'] = $eachrow->status;

				$finalArr[$i]['created_at'] = date('d-m-y',strtotime($eachrow->created_at));
				$finalArr[$i]['launch_date'] = strtotime($eachrow->launch_date)."000";
				//date('d-m-Y',strtotime($eachrow->launch_date));

				$finalArr[$i]['updated_at'] = date('d-m-y',strtotime($eachrow->updated_at));
				 $finalArr[$i]['spec_status']=0;
				 $spec_status=DB::table('product_attribute_xref')
								  ->where('model_id',$eachrow->id)
								  ->where(DB::raw("LOWER(brand)"),$brand)
								  ->get();
				if(!empty($spec_status)){
					$finalArr[$i]['spec_status']=1;
				}
				if(!empty($getListOfImages)){
				
				    $j = 0;
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						if($eachImage->type == 'image'){
						$finalArr[$i]['asset_image'][$j]['image_id'] = $eachImage->id;
						$finalArr[$i]['asset_image'][$j]['name'] = $eachImage->name;
						$finalArr[$i]['asset_image'][$j]['path'] = $eachImage->path;
						$j++;
						}
						else{
						$finalArr[$i]['asset_doc'][$l]['doc_id'] = $eachImage->id;
						$finalArr[$i]['asset_doc'][$l]['name'] = $eachImage->name;
						$finalArr[$i]['asset_doc'][$l]['path'] = $eachImage->path;
						$l++;
						}	
				   }
				}
				
				$i++;				
			}					

     return $finalArr;		
						
	}
	
	//function to get product by Category---------------------
	public static function getProductListByCategory($cat_id){
		$finalArr = array();
		$i = 0;

		$listofAllCategories = self::ListOfAllCategory();
		if(empty($cat_id)){
		
			 foreach($listofAllCategories as $eachrow){
				 $k =0 ;
				$getListOfAllProducts = DB::table('product')
								->where('product.category_id',$eachrow['id'])
								->where('product.status','1')
								->orderBy('product.position','asc')
								->orderBy('product.launch_date','desc')
								->get();
				$finalArr[$i]['category_detail'] = $eachrow;
				$finalArr[$i]['category_detail']['products']=[];
				if(!empty($getListOfAllProducts)){
					foreach($getListOfAllProducts as $eachrow)
					{
						$j =0 ;
						$getListOfImages = DB::table('asset_mapping')
						->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
						->where('asset_mapping.module','=','product')
						->where('asset_mapping.approved_status','=','1')
						->where('asset_mapping.module_id',$eachrow->id)
						->select('asset_library.id','asset_library.name','asset_library.path')
						->get();


						$finalArr[$i]['category_detail']['products'][$k]['pro_id'] = $eachrow->id;
						$finalArr[$i]['category_detail']['products'][$k]['product_name'] = $eachrow->product_name;
						$finalArr[$i]['category_detail']['products'][$k]['category_id'] = $eachrow->category_id;
						$finalArr[$i]['category_detail']['products'][$k]['product_desc'] = $eachrow->product_desc;
						$finalArr[$i]['category_detail']['products'][$k]['overview'] = $eachrow->desc1;
						$finalArr[$i]['category_detail']['products'][$k]['description'] = $eachrow->desc2;
						$finalArr[$i]['category_detail']['products'][$k]['specification'] = $eachrow->desc3;
						$finalArr[$i]['category_detail']['products'][$k]['new_product_flag'] = $eachrow->new_product_flag;
						$finalArr[$i]['launch_date'] = $eachrow->launch_date;
						//date('d-m-Y',strtotime($eachrow->launch_date));
						$finalArr[$i]['category_detail']['products'][$k]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
						$finalArr[$i]['category_detail']['products'][$k]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
						if(!empty($getListOfImages)){
						foreach($getListOfImages as $eachImage)
						{
							$finalArr[$i]['category_detail']['products'][$k]['pro_asset'][$j]['asset_id'] = $eachImage->id;
							$finalArr[$i]['category_detail']['products'][$k]['pro_asset'][$j]['name'] = $eachImage->name;
							$finalArr[$i]['category_detail']['products'][$k]['pro_asset'][$j]['path'] = $eachImage->path;
							$j++;
						}
						}
						$k++;			
			       }					

				}
				$i++;	
				
			 }
			
			}
		else{
			
			 foreach($listofAllCategories as $eachrow){
				 
				 $k =0 ;
				
				$getListOfAllProducts = DB::table('product')
										->where('product.category_id',$cat_id)
										->orderBy('product.position','asc')
										->orderBy('product.launch_date','desc')
										->get();
					if($eachrow['id'] == $cat_id){
					$finalArr['category_detail'] = $eachrow;}
					$finalArr['category_detail']['products']=[];
				if(!empty($getListOfAllProducts)){
					foreach($getListOfAllProducts as $eachrow){
			    $j =0 ;
				$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path')
								  ->get();
				$finalArr['category_detail']['products'][$k]['pro_id'] = $eachrow->id;
				$finalArr['category_detail']['products'][$k]['product_name'] = $eachrow->product_name;
				$finalArr['category_detail']['products'][$k]['category_id'] = $eachrow->category_id;
				$finalArr['category_detail']['products'][$k]['product_desc'] = $eachrow->product_desc;
				$finalArr['category_detail']['products'][$k]['overview'] = $eachrow->desc1;
				$finalArr['category_detail']['products'][$k]['description'] = $eachrow->desc2;
				$finalArr['category_detail']['products'][$k]['specification'] = $eachrow->desc3;
				$finalArr['category_detail']['products'][$k]['new_product_flag'] = $eachrow->new_product_flag;

				$finalArr['category_detail']['products'][$k]['launch_date'] = $eachrow->launch_date;
				//date('d-m-Y',strtotime($eachrow->launch_date));

				$finalArr['category_detail']['products'][$k]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
				$finalArr['category_detail']['products'][$k]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
				if(!empty($getListOfImages)){
					foreach($getListOfImages as $eachImage){
						
				$finalArr['category_detail']['products'][$k]['pro_asset'][$j]['image_id'] = $eachImage->id;
				$finalArr['category_detail']['products'][$k]['pro_asset'][$j]['name'] = $eachImage->name;
				$finalArr['category_detail']['products'][$k]['pro_asset'][$j]['path'] = $eachImage->path;
					$j++;
				  }
				}
				$k++;			
			}					
		}
	 }
  }
return $finalArr;
						
}
	//function to get list of all Categories---------------------
	public static function ListOfAllCategory(){
		
		$finalArr = array();
		$i = 0;
		
		$getListOfAllCategory = DB::table('category')->orderBy('id', 'desc')->get();
		 
			foreach($getListOfAllCategory as $eachrow){
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
				$finalArr[$i]['created_at'] = date('d-m-y',strtotime($eachrow->created_at));
				$finalArr[$i]['updated_at'] = date('d-m-y',strtotime($eachrow->updated_at));
				if(!empty($getListOfImages)){
					foreach($getListOfImages as $eachImage){
						
				$finalArr[$i]['cat_asset'][$j]['image_id'] = $eachImage->id;
				$finalArr[$i]['cat_asset'][$j]['name'] = $eachImage->name;
				$finalArr[$i]['cat_asset'][$j]['path'] = $eachImage->path;
					$j++;
				  }
				}
				
				$i++;				
			}					

  return $finalArr;					
	}
	
	
	//function to get product details---------------------
		
	public static function getProductDetails($request){
		
		$finalArr = array();
		$productID = '';
		$i = 0;
		$j = 0;
		$product_id = $request->input('product_id');
		$getProductDetails = DB::table('product')
							->where(array('product.status'=>1,'product.id'=>$product_id))
							->orderBy('product.position','asc')
							->orderBy('product.launch_date','desc')
							->get();
		
			foreach($getProductDetails as $eachrow)
			{
			   $getListOfAssets = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.name','asset_library.title','asset_library.path','asset_library.type')
								  ->get(); 
								 
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['product_name'] = $eachrow->product_name;
				$finalArr[$i]['category_id'] = $eachrow->category_id;
				$finalArr[$i]['product_desc'] = $eachrow->product_desc;
				$finalArr[$i]['desc1'] = $eachrow->desc1;
				$finalArr[$i]['desc2'] = $eachrow->desc2;
				$finalArr[$i]['desc3'] = $eachrow->desc3;
				$finalArr[$i]['new_product_flag'] = $eachrow->new_product_flag;
				$finalArr[$i]['launch_date'] = $eachrow->launch_date;
				//date('d-m-y',strtotime($eachrow->launch_date));

				$finalArr[$i]['created_at'] = date('d-m-y',strtotime($eachrow->created_at));
				$finalArr[$i]['updated_at'] = date('d-m-y',strtotime($eachrow->updated_at));
				
				if(!empty($getListOfAssets)){
					$j = 0;
					$l = 0;
					foreach($getListOfAssets as $eachAsset){
					if($eachAsset->type == 'image'){
					$finalArr[$i]['pro_image'][$j]['image_id'] = $eachAsset->id;
					$finalArr[$i]['pro_image'][$j]['image_title'] = $eachAsset->title;
				    $finalArr[$i]['pro_image'][$j]['image_name'] = $eachAsset->name;
				   $finalArr[$i]['pro_image'][$j]['image_path'] = $eachAsset->path;
				   $j++;
				}
				else{
					$finalArr[$i]['pro_doc'][$l]['doc_id'] = $eachAsset->id;
					$finalArr[$i]['pro_doc'][$l]['doc_title'] = $eachAsset->title;
					$finalArr[$i]['pro_doc'][$l]['doc_name'] = $eachAsset->name;
					$finalArr[$i]['pro_doc'][$l]['doc_path'] = $eachAsset->path;
					 $l++;
				}	
				
			 }
		}
							
	}					

  return $finalArr;		
}

//function to create Product---------------------
	public static function createProduct($request) { 
 
		$rules = array(
			'category_id'    => 'required', // category id is required
		    'product_name' => 'required', // product name is required
			'product_desc' => 'required', // product description is required
			'status' => 'required', // status is required
			'launch_date' => 'required' // launch_date is required
			);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$product = new Product();
		
		if(!$messages->first('category_id')) {
			$product['category_id'] = trim($request->input('category_id'));
			} 
		else {
			Session::flash('msg', $messages->first('category_id'));
		} 
		

	   if(!$messages->first('product_name')) {
		    $ProductName = trim($request->input('product_name'));
			$alreadyExist = GStarBaseController::validateForExist('product',$ProductName,'product_name');
			if($alreadyExist)
			{
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		   }
			$product['product_name'] = $ProductName;	
	     }
		else {
			Session::flash('msg', $messages->first('product_name'));
		  }
		if(!$messages->first('product_desc')) {
			$product['product_desc'] = trim($request->input('product_desc'));
	     }
		else {
			Session::flash('msg', $messages->first('product_desc'));
		  }
	  if(!$messages->first('status')) {
			$product['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		  if(!$messages->first('launch_date')) {
			$product['launch_date'] = date('Y-m-d',strtotime(trim($request->input('launch_date'))));
	     }
		else {
			Session::flash('msg', $messages->first('launch_date'));
		  } 
	 /* if(!$messages->first('new_product')) {
			$product['new_product_flag'] = trim($request->input('new_product'));
	     }
		else {
			Session::flash('msg', $messages->first('new_product'));
		  } */
			$product['new_product_flag'] = trim($request->input('new_product'));
			$product['desc1'] = trim($request->input('overview'));
			//$product['desc2'] = trim($request->input('description'));
			$product['desc3'] = trim($request->input('specification'));
			
			// if($request->input('launch_date')){
			// 	$product['launch_date'] = date('Y-m-d',strtotime(trim($request->input('launch_date'))));
             
			// }
	   

	  
		return $product;
	}

	//function to Edit Product---------------------
	public static function editProduct($request) { 
 
		$rules = array(
			'category_id'    => 'required', // category id is required
		    'product_name' => 'required', // product name is required
			'product_desc' => 'required', // product description is required
			'status' => 'required', // status is required
			'product_id' => 'required', // product id is required
			'launch_date' => 'required' // launch_date is required
			);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$product = new Product();
		$updateProduct = array();
		if(!$messages->first('category_id')) {
			$updateProduct['category_id'] = trim($request->input('category_id'));
			} 
		else {
			Session::flash('msg', $messages->first('category_id'));
		} 
		
	  if(!$messages->first('product_name')) {
	  		$product_id = $request->input('product_id');
	  		$product_name=trim($request->input('product_name'));
	  		$alreadyExist=DB::table('product')->select('product_name')->where('product_name','=',$product_name)->where('id','!=',$product_id)->count(); 
		if($alreadyExist){
			Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
		}else{
			$updateProduct['product_name'] = $product_name;
		}
			
	     }
		else {
			Session::flash('msg', $messages->first('product_name'));
		  }
		
	 if(!$messages->first('product_desc')) {
			$updateProduct['product_desc'] = trim($request->input('product_desc'));
	     }
		else {
			Session::flash('msg', $messages->first('product_desc'));
		  }
	if(!$messages->first('status')) {
			$updateProduct['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
	 if(!$messages->first('product_id')) {
			$product_id = trim($request->input('product_id'));
	     }
		else {
			Session::flash('msg', $messages->first('product_id'));
		  }


		if(!$messages->first('launch_date')) {
			$updateProduct['launch_date'] = date('Y-m-d',strtotime(trim($request->input('launch_date'))));
	     }
		else {
			Session::flash('msg', $messages->first('launch_date'));
		  }  
	        $updateProduct['new_product_flag'] = trim($request->input('new_product'));
			$updateProduct['desc1'] = trim($request->input('overview'));
			//$updateProduct['desc2'] = trim($request->input('description'));
			$updateProduct['desc3'] = trim($request->input('specification'));

			
	    
	
		return $updateProduct;
	}

	
	//Function to get Product Detail----------- 
	public static function getProductDetail($id) 
	{ 
   		    $brand=strtolower(GStarBaseController::GIONEE_BRAND);
   		    $finalArr = array();
			$result_array = array();
			$ProductDetails = DB::table('product')->where('id', $id)->first();
			$ProductDetails = GStarBaseController::userdatesFormat($ProductDetails);
			
			$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','category')
								  ->where('asset_mapping.module_id',$ProductDetails->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path')
								  ->get(); 
								  
				$finalArr['id'] = $ProductDetails->id;
				$finalArr['product_name'] = $ProductDetails->product_name;
				$finalArr['category_id'] = $ProductDetails->category_id;
				$finalArr['category_name'] = null;
				$CatName = DB::table('category')->select('category_name')->where('id',$ProductDetails->category_id)->first();

				if(!empty($CatName)){
				$finalArr['category_name'] = $CatName->category_name;	
				}
				$finalArr['product_desc'] = $ProductDetails->product_desc;
				$finalArr['status'] = $ProductDetails->status;
				$finalArr['desc1'] = $ProductDetails->desc1;
				//$finalArr['desc2'] = $ProductDetails->desc2;
				$finalArr['desc3'] = $ProductDetails->desc3;
				$finalArr['new_product_flag'] = $ProductDetails->new_product_flag;
				$finalArr['created_at'] = $ProductDetails->created_at;
				$finalArr['updated_at'] = $ProductDetails->updated_at;
				$finalArr['launch_date'] = date('d-m-Y',strtotime($ProductDetails->launch_date));
				$finalArr['spec_status']=0;
				 // $spec_status=DB::table('product_attribute_xref')
					// 			  ->where('model_id',$model->id)
					// 			  ->get();
				 $spec_status=DB::table('product_attribute_xref')
								  ->where('model_id',$id)
								  ->where(DB::raw("LOWER(brand)"),$brand)
								  ->get();
				if(!empty($spec_status)){
					$finalArr['spec_status']=1;
				}	

				if(!empty($getListOfImages))
				{
					$finalArr['image_id'] = $getListOfImages[0]->id;
					$finalArr['name'] = $getListOfImages[0]->name;
					$finalArr['path'] = $getListOfImages[0]->path;
				}
				else
				{
					$finalArr['name'] = '';
					$finalArr['path'] = '';
				}
				
		return $finalArr;
   				
 }

 	//Function to get Product Detail----------- 
	public static function getapprovedProductDetailId($id,$request_id,$product_id) 
	{ 
   		    $finalArr = array();
			$result_array = array();
			$finalArr['olddata']=[];
			$finalArr['newdata']=[];
			$finalArr['olddata']['assets'] = '';
			$finalArr['newdata']['assets'] = '';
			$ProductDetails = DB::table('product')->where('id', $product_id)->first();
			$ProductDetails = GStarBaseController::userdatesFormat($ProductDetails);
			//print_r($ProductDetails); exit;
			$getListOfImages = DB::table('asset_library')
								  ->join('asset_mapping', 'asset_library.id', '=', 'asset_mapping.asset_library_id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$ProductDetails->id)
								  ->where('asset_mapping.approved_status','=',1)
								 // ->where('asset_mapping.request_userid',$request_id)
								  ->select('asset_library.id','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
								  ->get(); 
				$finalArr['olddata']['id'] = $ProductDetails->id;
				$finalArr['olddata']['product_name'] = $ProductDetails->product_name;
				$category_id=$ProductDetails->category_id;
				$category_name=DB::table('category')->where('id', $category_id)->first();
				$finalArr['olddata']['category_name'] = $category_name->category_name;
				$finalArr['olddata']['category_id'] = $ProductDetails->category_id;
				$finalArr['olddata']['product_desc'] = $ProductDetails->product_desc;
				$finalArr['olddata']['status'] = $ProductDetails->status;
				$finalArr['olddata']['desc1'] = $ProductDetails->desc1;
				$finalArr['olddata']['desc3'] = $ProductDetails->desc3;
				$finalArr['olddata']['desc2'] = $ProductDetails->desc2;
				$finalArr['olddata']['position'] = $ProductDetails->position;
				$finalArr['olddata']['new_product_flag'] = $ProductDetails->new_product_flag;
				$finalArr['olddata']['created_at'] = $ProductDetails->created_at;
				$finalArr['olddata']['updated_at'] = $ProductDetails->updated_at;
				if(!empty($getListOfImages))
				{
					$finalArr['olddata']['assets'] = $getListOfImages;
					
				}
				else
				{
					$finalArr['olddata']['assets'] = '';
				}

				//$newProductDetails = DB::table('product_dummy')->where('product_id', $id)->where('request_userid', $request_id)->where('approve_status','0')->first();
				$newProductDetails = DB::table('product_dummy')->where('id', $id)->where('is_deleted', 0)->where('product_id', $product_id)->where('request_userid', $request_id)->first();
			$newProductDetails = GStarBaseController::userdatesFormat($newProductDetails);
			
			
				$finalArr['newdata']['product_id'] = $newProductDetails->product_id;
				$finalArr['newdata']['product_name'] = $newProductDetails->product_name;
				$finalArr['newdata']['category_id'] = $newProductDetails->category_id;
				$category_id=$newProductDetails->category_id;
				$category_name=DB::table('category')->where('id', $category_id)->first();
				$finalArr['newdata']['category_name'] = $category_name->category_name;
				$finalArr['newdata']['product_desc'] = $newProductDetails->product_desc;
				$finalArr['newdata']['status'] = $newProductDetails->status;
				$finalArr['newdata']['desc1'] = $newProductDetails->desc1;
				$finalArr['newdata']['desc3'] = $newProductDetails->desc3;
				$finalArr['newdata']['desc2'] = $newProductDetails->desc2;
				$finalArr['newdata']['position'] = $newProductDetails->position;
				$finalArr['newdata']['approve_status'] = $newProductDetails->approve_status;
				$finalArr['newdata']['request_userid'] = $newProductDetails->request_userid;
				$finalArr['newdata']['new_product_flag'] = $newProductDetails->new_product_flag;
				$finalArr['newdata']['created_at'] = $newProductDetails->created_at;
				$finalArr['newdata']['updated_at'] = $newProductDetails->updated_at;

				$getListOfImagesnew = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$ProductDetails->id)
								  ->where('asset_mapping.approved_status','0')
								  ->where('asset_mapping.request_userid',$request_id)
								  ->select('asset_library.id','asset_library.name','asset_library.path','asset_library.type','asset_library.title','asset_mapping.module','asset_mapping.module_id','asset_mapping.id as asset_mappingId')
								  ->get(); 
						
				if(!empty($getListOfImagesnew))
				{
					   
					$finalArr['newdata']['assets'] = $getListOfImagesnew;
					
				}
				else
				{
					$finalArr['newdata']['assets'] = '';
				}
			
				
		return $finalArr;
   				
 }

 public static function ProductApproveAdmin($productupdate,$request)
	{
		$id=$request->input('id');	
		$product_id = trim($request->input('product_id'));
		$request_id = trim($request->input('request_id'));
		$status = trim($request->input('approve_status'));
		$result_array=array();
		$approve_status=$productupdate['approve_status'];

		
        
		if($status==GStarBaseController::ADMIN_REJECT_REQUEST)
		{
			if($approve_status==$status){
				$result_array = GStarBaseController::errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,GStarBaseController::MSG_RECORD_ADMIN_REJECT_FAIL);
			}else
			{
				$product_dummy_update = DB::table('product_dummy')
										->where('id',$id)
										->where('product_id',$product_id)
										->where('request_userid',$request_id)
										->update(['updated_at'=>date("Y-m-d h:m:s"),'approve_status'=>'2','approved_date'=>date("Y-m-d h:m:s")]);
				if($productupdate['assets'])
				{
					$updateAsset=array('updated_at'=>date("Y-m-d h:m:s"),'approved_status'=>'2');
					foreach ($productupdate['assets'] as $key ) {
					$asset_library_update = DB::table('asset_library')->where('request_userid', $request_id)->where('id', $key->id)->update($updateAsset);
							$asset_mapping_update = DB::table('asset_mapping')->where('request_userid', $request_id)->where('id', $key->asset_mappingId)->update($updateAsset);
					 }		
				
				}	
				if($product_dummy_update){
					if($productupdate['assets'])
					{
						$result_array['status'] = 'success';
						$result_array['msg'] = GStarBaseController::MSG_RECORD_ADMIN_REJECT_SUCCESS;
					}else{
						$result_array['status'] = 'success';
						$result_array['msg'] = GStarBaseController::MSG_RECORD_ADMIN_REJECT_SUCCESS;
					}
				}else{
					if($productupdate['assets'])
					{
						$result_array['status'] = 'success';
						$result_array['msg'] = GStarBaseController::MSG_RECORD_ADMIN_REJECT_SUCCESS;
					}else{
						$result_array['status'] = 'error';
						$result_array['msg'] = GStarBaseController::MSG_RECORD_ADMIN_REJECT_FAIL;
					}
				}

			}
		}else{
			$update=array('product_name'=>$productupdate['product_name'],'category_id'=>$productupdate['category_id'],'product_desc'=>$productupdate['product_desc'],'status'=>$productupdate['status'],'desc1'=>$productupdate['desc1'],'desc2'=>$productupdate['desc2'],'desc3'=>$productupdate['desc3'],'position'=>$productupdate['position'],'new_product_flag'=>$productupdate['new_product_flag'],'updated_at'=>date("Y-m-d h:m:s"));
			if($approve_status=='0')
			{

			  $product_update = DB::table('product')->where('id', $product_id)->update($update);
			  if($product_update){
			  	$updateAsset=array('updated_at'=>date("Y-m-d h:m:s"),'approved_status'=>'1');
				if($productupdate['assets'])
				 {
					foreach ($productupdate['assets'] as $key ) {
					$asset_library_update = DB::table('asset_library')->where('request_userid', $request_id)->where('id', $key->id)->update($updateAsset);
					$asset_mapping_update = DB::table('asset_mapping')->where('request_userid', $request_id)->where('id', $key->asset_mappingId)->update($updateAsset);
					}
				}
				$product_dummy_update = DB::table('product_dummy')->where('id',$id)->where('product_id',$product_id)->where('request_userid',$request_id)->update(['updated_at'=>date("Y-m-d h:m:s"),'approve_status'=>'1','approved_date'=>date("Y-m-d h:m:s")]);
			  
			 }
			 if($product_dummy_update){
				$result_array['status'] = 'success';
				$result_array['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_SUCCESS;
				}
			}
			else{
				if($productupdate['assets'])
				{
					foreach ($productupdate['assets'] as $key ) {
					$asset_library_update = DB::table('asset_library')->where('request_userid', $request_id)->where('id', $key->id)->update(['updated_at'=>date("Y-m-d h:m:s"),'approved_status'=>'1']);
							$asset_mapping_update = DB::table('asset_mapping')->where('request_userid', $request_id)->where('id', $key->asset_mappingId)->update(['updated_at'=>date("Y-m-d h:m:s"),'approved_status'=>'1']);
							
					 }
					 $product_dummy_update = DB::table('product_dummy')->where('id',$id)->where('product_id',$product_id)->where('request_userid',$request_id)->update(['updated_at'=>date("Y-m-d h:m:s"),'approve_status'=>'1','approved_date'=>date("Y-m-d h:m:s")]);
				 if($product_dummy_update){
					$result_array['status'] = 'success';
					$result_array['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_SUCCESS;
					}
			    }
				else{

					$result_array['status'] = 'error';
					$result_array['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_FAIL;
					//$result_array = GStarBaseController::errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_FAIL);
				}
					
			}
		}
		
			
		return $result_array;
	}

 public static function getapprovedProductDetail($request,$search_keyword,$pageNo) 
	{ 
   		$finalArr = array();
   		$inputs = Input::get();
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
			 $sql = DB::table('product_dummy');
			 			$sql->where('is_deleted',0);
			  			$sql->where('product_name','like','%'.$search_keyword.'%');
			  			$sql->orderBy('approve_status', 'asc');
			  			$sql->orderBy('approved_date', 'desc');
			  			if(isset($inputs['approve_status'])){
			  				$sql->where('approve_status',trim($inputs['approve_status']));
			  			}
			  			if(isset($inputs['start_date']) && isset($inputs['end_date'])){
					   		$start_date=trim($inputs['start_date']);
					   		$end_date=trim($inputs['end_date']);
					   		if($start_date !="" && $end_date !=""){
					   			$s_date=strtotime($start_date);
					   			$e_date=strtotime($end_date);
					   			$e_date=strtotime('+1 day',$e_date);
					   			if($s_date == $e_date){
					   				$e_date=strtotime('+1 day', $s_date);

					   			}
					   			$date1=date("d-m-Y h:i:s", ($s_date));
					   			$date2=date("d-m-Y h:i:s", ($e_date));
					   			if($s_date< $e_date){
					   				$sql->whereBetween('created_at', [$date1, $date2]);	
					   			}
					   		}
				   		}
			  			$sql->skip($offset);
			  			$sql->take(GStarBaseController::PAGINATION_LIMITS);
			  						
         }
        else
		{
		    $sql = DB::table('product_dummy');
			 			$sql->where('is_deleted',0);
			  			$sql->orderBy('approve_status', 'asc');
			  			$sql->orderBy('approved_date', 'desc');
			  			if(isset($inputs['approve_status'])){
			  				$sql->where('approve_status',trim($inputs['approve_status']));
			  			}
			  			if(isset($inputs['start_date']) && isset($inputs['end_date'])){
					   		$start_date=trim($inputs['start_date']);
					   		$end_date=trim($inputs['end_date']);
					   		if($start_date !="" && $end_date !=""){
					   			$s_date=strtotime($start_date);
					   			$e_date=strtotime($end_date);
					   			$e_date=strtotime('+1 day',$e_date);
					   			if($s_date == $e_date){
					   				$e_date=strtotime('+1 day', $s_date);

					   			}
					   			$date1=date("d-m-Y h:i:s", ($s_date));
					   			$date2=date("d-m-Y h:i:s", ($e_date));
					   			if($s_date< $e_date){
					   				$sql->whereBetween('created_at', [$date1, $date2]);	
					   			}
					   		}
				   		}
			  			$sql->skip($offset);
			  			$sql->take(GStarBaseController::PAGINATION_LIMITS);
         }

         $newProductDetails=$sql->get();



         if(!empty($newProductDetails)){

         	foreach ($newProductDetails as $key ) {
				$product_id=$key->product_id;
				$request_userid=$key->request_userid;
				$getListOfImagesnew = DB::table('asset_mapping')
								  		->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  		->where('asset_mapping.module','=','product')
								  		->where('asset_mapping.module_id',$product_id)
								  		->where('asset_mapping.approved_status','0')
								  		->where('asset_mapping.request_userid',$request_userid)
								  		->select('asset_library.id','asset_library.name','asset_library.path','asset_library.type','asset_library.title','asset_mapping.module','asset_mapping.module_id','asset_mapping.id as asset_mappingId')
								  		->get(); 
				if($getListOfImagesnew){
					$finalArr[$i]['approve_status'] = 0;
				}
				else{
					$finalArr[$i]['approve_status'] = $key->approve_status;
				}		  		
				$finalArr[$i]['id'] = $key->id;
				$finalArr[$i]['product_id'] = $key->product_id;
				$finalArr[$i]['product_name'] = $key->product_name;
				$finalArr[$i]['category_id'] = $key->category_id;
				$category_id=$key->category_id;
				$category=DB::table('category')->where('id',$category_id)->first();
				$finalArr[$i]['category_name'] = $category->category_name;
				$finalArr[$i]['product_desc'] = $key->product_desc;
				$finalArr[$i]['status'] = $key->status;
				$finalArr[$i]['desc1'] = $key->desc1;
				$finalArr[$i]['desc3'] = $key->desc3;
				$finalArr[$i]['new_product_flag'] = $key->new_product_flag;
				$finalArr[$i]['created_at'] = $key->created_at;
				$finalArr[$i]['updated_at'] = $key->updated_at;
				$approved_date=$key->approved_date;
				if($approved_date){
					$finalArr[$i]['approved_date'] = strtotime($approved_date)."000";
					//date("d-m-Y", strtotime( $approved_date ) ) ;
				}else{
					$finalArr[$i]['approved_date'] = "";//$approved_date;
				}
				//$finalArr[$i]['approved_date'] = $key->approved_date;
				$user=DB::table('users')->where('id',$request_userid)->first();
				$finalArr[$i]['request_userid'] = $request_userid;
				$finalArr[$i]['request_username'] = $user->first_name.' '.$user->last_name;
				$i++;
			}

        }
		
		return $finalArr;
   				
 }


 //Function to change the order of Product----------- 
	public static function changeOrderofProduct($request) 
	{
		$updated = 0;
		$rules = array(
				'orderDataArray' => 'required'// orderDataArray is required	
				
			);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$product = array();
		$updates = array();
		//$orderArr = $request->input('orderDataArray');
		
		
		 if(!$messages->first('orderDataArray')) {
					$product = $request->input('orderDataArray');
					
			     }
		else {
					Session::flash('msg', $messages->first('orderDataArray'));
				  }
				  
		if(!Session::has('msg'))
		{
			
		foreach($product as $each){
			//print_r($each);die;
			
			$updateProductOrder = $responseContent = DB::table('product')->where('id', $each['id'])->update(array('position'=>$each['pos']));
			if($updateProductOrder){
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

 
 //function for Monthly Product Details -----------    
	public static function MonthlyProductDetails(){
		$Current_date = date('Y-m-d');
		$thirtydaysolddate  = date('Y-m-d', strtotime(date("Y-m-d", strtotime($Current_date)) . " -30 day"));
		$k = 0;

		  $finalArr = array();
			$result_array = array();
			$ProductDetails = DB::table('product')
			 ->where('product.new_product_flag',1)
			 ->where('product.status','1')
			->whereBetween('product.created_at',array($thirtydaysolddate.' 00:00:00',$Current_date.' 23:59:00'))
			->orderBy('product.position','asc')
			->orderBy('product.launch_date','desc')
			/* ->orWhere(function($query) use ($thirtydaysolddate,$Current_date)
            {
                $query->where('updated_at', '>',$thirtydaysolddate)
                      ->where('updated_at', '<', $Current_date);
            }) */
			
			->get();



			$ProductDetails = GStarBaseController::userdatesFormat($ProductDetails);
			if(!empty($ProductDetails)){
				


			$finalArr['category_detail']['id'] = 59;
			$finalArr['category_detail']['category_name'] = "Legacy Series";
			$finalArr['category_detail']['category_parent_id'] = "Legacy Series";
			$finalArr['category_detail']['status'] = "Legacy Series";
			$finalArr['category_detail']['description'] = "Legacy Series";
			$finalArr['category_detail']['created_at'] = "Legacy Series";
			$finalArr['category_detail']['updated_at'] = "Legacy Series";
			
			

			
			foreach($ProductDetails as $product){
				$j =0 ;
			$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.module_id',$product->id)
								  ->select('asset_library.id','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
								  ->get(); 
						  
				$finalArr['category_detail']['products'][$k]['pro_id'] = $product->id;
				$finalArr['category_detail']['products'][$k]['product_name'] = $product->product_name;
				$finalArr['category_detail']['products'][$k]['category_id'] = $product->category_id;
				$finalArr['category_detail']['products'][$k]['product_desc'] = $product->product_desc;
				$finalArr['category_detail']['products'][$k]['status'] = $product->status;
				$finalArr['category_detail']['products'][$k]['desc1'] = $product->desc1;
				
				$finalArr['category_detail']['products'][$k]['desc3'] = $product->desc3;
				$finalArr['category_detail']['products'][$k]['new_product_flag'] = $product->new_product_flag;
				$finalArr['category_detail']['products'][$k]['launch_date'] =$product->launch_date;
				// date('d-m-Y',strtotime($product->launch_date));

				$finalArr['category_detail']['products'][$k]['created_at'] = $product->created_at;
				$finalArr['category_detail']['products'][$k]['updated_at'] = $product->updated_at;
				if(!empty($getListOfImages))
				{
					$m = 0;
					$n = 0;
					foreach($getListOfImages as $eachImage){

					if($eachImage->type == 'image'){
						$finalArr['category_detail']['products'][$k]['pro_image'][$m]['asset_id'] = $eachImage->id;
						$finalArr['category_detail']['products'][$k]['pro_image'][$m]['name'] = $eachImage->name;
						$finalArr['category_detail']['products'][$k]['pro_image'][$m]['path'] = $eachImage->path;
						$finalArr['category_detail']['products'][$k]['pro_image'][$m]['title'] = $eachImage->title;
						$finalArr['category_detail']['cat_image']['asset_id'] = $eachImage->id;

						$finalArr['category_detail']['cat_image']['name'] = $eachImage->name;
						$finalArr['category_detail']['cat_image']['path'] = $eachImage->path;
						$finalArr['category_detail']['cat_image']['title'] = $eachImage->title;
						$m++;
					}else{
						$finalArr['category_detail']['products'][$k]['pro_doc'][$n]['asset_id'] = $eachImage->id;
						$finalArr['category_detail']['products'][$k]['pro_doc'][$n]['name'] = $eachImage->name;
						$finalArr['category_detail']['products'][$k]['pro_doc'][$n]['path'] = $eachImage->path;
						$finalArr['category_detail']['products'][$k]['pro_doc'][$n]['title'] = $eachImage->title;
						$n++;

					}
					$j++;
				 	}
				}
				
				$k++;
			}
					
		
		return $finalArr;
   }
}


}
