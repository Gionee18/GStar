<?php

namespace App;
use Validator;
use URL;
//use App\Category;
use Intervention\Image\Facades\Image as Image;
use File;
use Session,Auth,DB,Hash;

use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Recommender extends Authenticatable
{

	 public static function getListOfManufacturer($search_keyword,$pageNo){
		
		$finalArr = array();
		$i = 0;
		 if($pageNo) 
         {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		 }else{
			 $offset= 0;
		 }
		 if($search_keyword!="")
		 {
			 $manufacture = DB::table('manufacture')
			  ->where('name','like','%'.$search_keyword.'%')->orderBy('id', 'desc')
			  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
        else
		{
		     $manufacture = DB::table('manufacture')->orderBy('id', 'desc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
		 
			foreach($manufacture as $eachrow)
			{

				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['name'] =$eachrow->name;
				//$finalArr[$i]['image'] = $eachrow->image;
				$finalArr[$i]['description'] = $eachrow->description;
				$finalArr[$i]['status'] =$eachrow->status;
				$finalArr[$i]['created_at'] = strtotime($eachrow->created_at)."000";
				//date('d-m-Y',strtotime($eachrow->created_at));
				if($eachrow->updated_at){
					$finalArr[$i]['updated_at'] = strtotime($eachrow->updated_at)."000";
					//date('d-m-Y',strtotime($eachrow->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$finalArr[$i]['asset_image']=[];
				$getListOfImages = DB::table('rec_asset_mapping')
								  ->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								  ->where('rec_asset_mapping.module','=','manufacturer')
								  ->where('rec_asset_mapping.module_id',$eachrow->id)
								  ->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								  ->get();
				if(!empty($getListOfImages)){
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						
						$finalArr[$i]['asset_image'][$l]['image_id'] = $eachImage->id;
						$finalArr[$i]['asset_image'][$l]['name'] = $eachImage->name;
						$finalArr[$i]['asset_image'][$l]['path'] = $eachImage->path;
						$l++;
					}	
				 }
				$i++;				
			}					  
			
			
  	return $finalArr;					
	}

	 

	public static function ManufacturerById($id) { 
		
		$finalArr = array();
		$i=0;
		$manufacture=DB::table('manufacture')->where('id',$id)->first(); 

		if($manufacture){

				$finalArr['id'] = $manufacture->id;
				$finalArr['name'] =$manufacture->name;
				//$finalArr['image'] = $manufacture->image;
				$finalArr['description'] = $manufacture->description;
				$finalArr['status'] =$manufacture->status;
				$finalArr['created_at'] = date('d-m-Y',strtotime($manufacture->created_at));
				if($manufacture->updated_at){
					$finalArr['updated_at'] = date('d-m-Y',strtotime($manufacture->updated_at));
				}else{
					$finalArr['updated_at'] = '';
				}
				$finalArr['asset_image']=[];
				$getListOfImages = DB::table('rec_asset_mapping')
								  ->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								  ->where('rec_asset_mapping.module','=','manufacturer')
								  ->where('rec_asset_mapping.module_id',$manufacture->id)
								  ->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								  ->get();
				if(!empty($getListOfImages)){
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						
						$finalArr['asset_image'][$l]['image_id'] = $eachImage->id;
						$finalArr['asset_image'][$l]['name'] = $eachImage->name;
						$finalArr['asset_image'][$l]['path'] = $eachImage->path;
						$l++;
					}	
				 }
		}
	    
		return $finalArr;
	}





	public static function createManufacturer($request) { 
		

		$rules = array(
			'name'    => 'required', 
		    'description' => 'required', 
			'status'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$manufacturer = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$alreadyExist = GStarBaseController::validateForExist('manufacture',$name,'name');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$manufacturer['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 

		if(!$messages->first('description')) {
			$manufacturer['description'] = trim($request->input('description'));
	     }
		else {
			//Session::forget('msg');
			Session::flash('msg', $messages->first('description'));
		  }
		
		if(!$messages->first('status')) {
			$manufacturer['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		return $manufacturer;
	}


	public static function editManufacturer($request) { 
		

		$rules = array(
			'name'    => 'required', 
		    'description' => 'required', 
			'status'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$manufacturer = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$manufacturer_id=trim($request->input('id'));
			$alreadyExist = $alreadyExist = DB::table('manufacture')->select('name')->where('id','!=',$manufacturer_id)->where('name','=',$name)->count();
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$manufacturer['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 

		if(!$messages->first('description')) {
			$manufacturer['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		if(!$messages->first('status')) {
			$manufacturer['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		return $manufacturer;
	}


	

	public static function addManufacture($Manufacturer)
	{
		
		$save=DB::table('manufacture')->insertGetId($Manufacturer);
		return $save;
		
		
	}

	public static function SaveuploadFile($saveId,$module,$image)
	{
		$saveImage=array('name'=>$image['filename'],'path'=>$image['targetpath']);
		$ImageId=DB::table('rec_asset_library')->insertGetId($saveImage);
		$attachModule=array('asset_library_id'=>$ImageId,'module'=>$module,'module_id'=>$saveId);
		$attach=DB::table('rec_asset_mapping')->insertGetId($attachModule);
		return $attach;
	}

	public static function UpdateploadFile($model_id,$module,$image)
	{
		
		$getListOfImages = DB::table('rec_asset_mapping')
							->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
							->where('rec_asset_mapping.module','=',$module)
							->where('rec_asset_mapping.module_id',$model_id)
							->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
							->get();
		if(!empty($getListOfImages)){
			foreach($getListOfImages as $eachImage)
			{			
				$imagepath=$eachImage->path.'/'.$eachImage->name;
				$imageid=$eachImage->id;
				$save=DB::table('rec_asset_library')->where('id','=',$imageid)->delete();
				$save=DB::table('rec_asset_mapping')->where('asset_library_id','=',$imageid)->where('module_id','=',$model_id)->delete();
				if($save){
					if(file_exists($imagepath)){
						unlink($imagepath);	
					}
				}
			}	
		}
		$saveImage=array('name'=>$image['filename'],'path'=>$image['targetpath']);
		$ImageId=DB::table('rec_asset_library')->insertGetId($saveImage);
		$attachModule=array('asset_library_id'=>$ImageId,'module'=>$module,'module_id'=>$model_id);
		$attach=DB::table('rec_asset_mapping')->insertGetId($attachModule);
		return $attach;
	}

	public static function updateManufacturer($manufacturer,$manufacturer_id)
	{
		
		$save=DB::table('manufacture')->where('id','=',$manufacturer_id)->update($manufacturer);
		return $save;
		
		
	}


	public static function ModelByManufacturerId($manufacturer_id)
	{

		$save=DB::table('model')->where('mf_id',$manufacturer_id)->get();
		return $save;
	}




	public static function deleteManufacturer($id)
	{
		$getListOfImages = DB::table('rec_asset_mapping')
							->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
							->where('rec_asset_mapping.module','=','manufacturer')
							->where('rec_asset_mapping.module_id',$id)
							->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
							->get();
		if(!empty($getListOfImages)){
			foreach($getListOfImages as $eachImage)
			{			
				$imagepath=$eachImage->path.'/'.$eachImage->name;
				$imageid=$eachImage->id;
				$save=DB::table('rec_asset_library')->where('id','=',$imageid)->delete();
				$save=DB::table('rec_asset_mapping')->where('asset_library_id','=',$imageid)->where('module_id','=',$id)->delete();
				if($save){
					if(file_exists($imagepath)){
						unlink($imagepath);	
					}
				}
			}	
		}
		$save=DB::table('manufacture')->where('id','=',$id)->delete();
		return $save;
		

		
	}



 public static function getListOfModel($search_keyword,$pageNo){
		
		$brand=strtolower(GStarBaseController::OTHER_BRAND);
		$finalArr = array();
		$i = 0;
		 if($pageNo) 
         {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		 }else{
			 $offset= 0;
		 }
		 if($search_keyword!="")
		 {
			 $model = DB::table('model')
			  ->where('model_name','like','%'.$search_keyword.'%')->orderBy('id', 'desc')
			  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
        else
		{
		     $model = DB::table('model')->orderBy('id', 'desc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
		 
			foreach($model as $eachrow)
			{

				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['model_name'] =$eachrow->model_name;
				$finalArr[$i]['mf_id'] = $eachrow->mf_id;
				$finalArr[$i]['mf_name'] = self::manufacturer_name($eachrow->mf_id);
				$finalArr[$i]['description'] = $eachrow->description;
				$finalArr[$i]['status'] =$eachrow->status;
				$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
				if($eachrow->updated_at){
					$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$finalArr[$i]['asset_image']=[];
				$getListOfImages = DB::table('rec_asset_mapping')
								  ->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								  ->where('rec_asset_mapping.module','=','model')
								  ->where('rec_asset_mapping.module_id',$eachrow->id)
								  ->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								  ->get();
				if(!empty($getListOfImages)){
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						
						$finalArr[$i]['asset_image'][$l]['image_id'] = $eachImage->id;
						$finalArr[$i]['asset_image'][$l]['name'] = $eachImage->name;
						$finalArr[$i]['asset_image'][$l]['path'] = $eachImage->path;
						$l++;
					}	
				 }
				 $finalArr[$i]['spec_status']=0;
				 $spec_status=DB::table('product_attribute_xref')
								  ->where('model_id',$eachrow->id)
								  ->where(DB::raw("LOWER(brand)"),$brand)
								  ->get();
				if(!empty($spec_status)){
					$finalArr[$i]['spec_status']=1;
				}	
				$i++;				
			}					  
			
			
  	return $finalArr;					
	}




	public static function ModelById($id){
		
		$brand=strtolower(GStarBaseController::OTHER_BRAND);
		$finalArr = array();
		$i = 0;
		$model=DB::table('model')->where('id',$id)->first(); 

		if($model){

				$finalArr['id'] = $model->id;
				$finalArr['model_name'] =$model->model_name;
				$finalArr['mf_id'] = $model->mf_id;
				$finalArr['mf_name'] = self::manufacturer_name($model->mf_id);
				$finalArr['description'] = $model->description;
				$finalArr['status'] =$model->status;
				$finalArr['created_at'] = date('d-m-Y',strtotime($model->created_at));
				if($model->updated_at){
					$finalArr['updated_at'] = date('d-m-Y',strtotime($model->updated_at));
				}else{
					$finalArr['updated_at'] = '';
				}
				$finalArr['asset_image']=[];
				$getListOfImages = DB::table('rec_asset_mapping')
								  ->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								  ->where('rec_asset_mapping.module','=','model')
								  ->where('rec_asset_mapping.module_id',$model->id)
								  ->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								  ->get();
				if(!empty($getListOfImages)){
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						$finalArr['asset_image'][$l]['image_id'] = $eachImage->id;
						$finalArr['asset_image'][$l]['name'] = $eachImage->name;
						$finalArr['asset_image'][$l]['path'] = $eachImage->path;
						$l++;
					}	
				 }
				 $finalArr['spec_status']=0;
				 // $spec_status=DB::table('product_attribute_xref')
					// 			  ->where('model_id',$model->id)
					// 			  ->get();
				 $spec_status=DB::table('product_attribute_xref')
								  ->where('model_id',$model->id)
								  ->where(DB::raw("LOWER(brand)"),$brand)
								  ->get();
				if(!empty($spec_status)){
					$finalArr['spec_status']=1;
				}				  
		}
			
  	return $finalArr;					
	}



	public static function manufacturer_name($id)
	{

		$mf=DB::table('manufacture')->where('id',$id)->first();
		return $mf->name;
	}

	public static function model_name($id)
	{

		$mf=DB::table('model')->where('id',$id)->first();
		return $mf->model_name;
	}



	public static function createModel($request) { 
		

		$rules = array(
			'model_name'    => 'required', 
		    'description' => 'required', 
			'status'=> 'required',
			'mf_id'=>'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$model = [];
		if(!$messages->first('model_name')) 
		{
			$model_name = trim($request->input('model_name'));
			$alreadyExist = GStarBaseController::validateForExist('model',$model_name,'model_name');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$model['model_name'] = $model_name;		   
		}else{
			Session::flash('msg', $messages->first('model_name'));
		} 

		if(!$messages->first('description')) {
			$model['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		if(!$messages->first('status')) {
			$model['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		if(!$messages->first('mf_id')) {
			$model['mf_id'] = trim($request->input('mf_id'));
	     }
		else {
			Session::flash('msg', $messages->first('mf_id'));
		  }  

		return $model;
	}


	public static function addModel($Model)
	{
		
		$save=DB::table('model')->insertGetId($Model);
		return $save;
		
		
	}


	// public static function saveHashtag($saveId,$HashtagArray)
	// {
	// 	$product_id=$saveId;
	// 	for ($i=0; $i <count($HashtagArray) ; $i++) { 
	// 		$savearray=array('product_id'=>$product_id,'hash_kay'=>$HashtagArray[$i]);
	// 		$save=DB::table('product_hashtags')->insertGetId($savearray);
	// 	}
	// 	return $save;
		
		
	// }

	public static function editModel($request) { 
		

		$rules = array(
			'model_name'    => 'required', 
		    'description' => 'required', 
			'status'=> 'required',
			'mf_id'=>'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$model = [];
		if(!$messages->first('model_name')) 
		{
			$model_name = trim($request->input('model_name'));
			$model_id=trim($request->input('id'));
			$alreadyExist = $alreadyExist = DB::table('model')->select('model_name')->where('id','!=',$model_id)->where('model_name','=',$model_name)->count();
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$model['model_name'] = $model_name;		   
		}else{
			Session::flash('msg', $messages->first('model_name'));
		} 

		if(!$messages->first('description')) {
			$model['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		if(!$messages->first('status')) {
			$model['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		if(!$messages->first('mf_id')) {
			$model['mf_id'] = trim($request->input('mf_id'));
	     }
		else {
			Session::flash('msg', $messages->first('mf_id'));
		  }  

		return $model;
	}



	public static function updateModel($model,$model_id)
	{
		
		$save=DB::table('model')->where('id','=',$model_id)->update($model);
		return $save;
		
		
	}


	public static function updateHashtag($saveId,$HashtagArray,$brand)
	{
		$product_id=$saveId;
		$gethashtags=DB::table('product_hashtags')->select('id','product_id','hash_kay')->where('product_id',$product_id)->where('brand',$brand)->get();
		if(!empty($gethashtags)){
			$delete=DB::table('product_hashtags')->where('product_id',$product_id)->where('brand',$brand)->delete();
		}
		for ($i=0; $i <count($HashtagArray) ; $i++) { 
			$savearray=array('product_id'=>$product_id,'hash_kay'=>$HashtagArray[$i],'brand',$brand);
			$save=DB::table('product_hashtags')->insertGetId($savearray);
		}
		return $save;
		
		
	}


	public static function deleteModel($id)
	{
		$brand=GStarBaseController::OTHER_BRAND;
		$getListOfImages = DB::table('rec_asset_mapping')
							->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
							->where('rec_asset_mapping.module','=','model')
							->where('rec_asset_mapping.module_id',$id)
							->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
							->get();
		if(!empty($getListOfImages)){
			foreach($getListOfImages as $eachImage)
			{			
				$imagepath=$eachImage->path.'/'.$eachImage->name;
				$imageid=$eachImage->id;
				$save=DB::table('rec_asset_library')->where('id','=',$imageid)->delete();
				$save=DB::table('rec_asset_mapping')->where('asset_library_id','=',$imageid)->where('module_id','=',$id)->delete();
				if($save){
					if(file_exists($imagepath)){
						unlink($imagepath);	
					}
				}
			}	
		}
		$delete=DB::table('product_hashtags')->where('product_id',$id)->where('brand',$brand)->delete();
		$delete=DB::table('product_attribute_xref')->where('model_id',$id)->where('brand',$brand)->delete();
		$delete=DB::table('product_spec_display')->where('model_id',$id)->where('brand',$brand)->delete();
		$save=DB::table('model')->where('id','=',$id)->delete();
		return $save;
		

		
	}



	public static function getlistSpecification(){

		//public static function getlistSpecification($search_keyword,$pageNo)
		
		$finalArr = array();
		$i = 0;
					//  if($pageNo) 
			  //        {
			  //           $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
					//  }else{
					// 	 $offset= 0;
					//  }
					//  if($search_keyword!="")
					//  {
					// 	 $manufacture = DB::table('manufacture')
					// 	  ->where('name','like',$search_keyword.'%')->orderBy('id', 'desc')
					// 	  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
			  //        }
			  //       else
					// {
					//      $manufacture = DB::table('manufacture')->orderBy('id', 'desc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
			  //        }


		$spec_category = DB::table('spec_category')->where('status','1')->orderBy('position', 'asc')->get();
		//$spec_category = DB::table('spec_category')->where('status','1')->orderBy('id', 'asc')->take(2)->get();
		//print_r($spec_category); exit;
		 
		foreach($spec_category as $eachrow)
		{
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['cat_name'] =$eachrow->name;
			$finalArr[$i]['status'] = $eachrow->status;
			$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
			if($eachrow->updated_at){
				$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
			}else{
				$finalArr[$i]['updated_at'] = '';
			}
			$finalArr[$i]['subcategory']=[];
			$subcategory = DB::table('spec_subcategory')
							->where('status','1')
							->where('spec_catid',$eachrow->id)
							->select('id as subcatid','spec_catid','name','status','created_at','updated_at','threshold')
							->orderBy('position', 'asc')
							->get();
			if(!empty($subcategory)){
			$l = 0;
			foreach($subcategory as $subcategory)
			{
				$finalArr[$i]['subcategory'][$l]['subcatid'] = $subcategory->subcatid;
				$finalArr[$i]['subcategory'][$l]['spec_catid'] = $subcategory->spec_catid;
				$finalArr[$i]['subcategory'][$l]['subcat_name'] = $subcategory->name;
				$finalArr[$i]['subcategory'][$l]['status'] = $subcategory->status;
				//$finalArr[$i]['subcategory'][$l]['priority'] = $subcategory->priority;
				$finalArr[$i]['subcategory'][$l]['threshold'] = $subcategory->threshold;
				$finalArr[$i]['subcategory'][$l]['created_at'] = date('d-m-Y',strtotime($subcategory->created_at));
				if($subcategory->updated_at){
					$finalArr[$i]['subcategory'][$l]['updated_at'] = date('d-m-Y',strtotime($subcategory->updated_at));
				}else{
					$finalArr[$i]['subcategory'][$l]['updated_at'] = '';
				}
				$finalArr[$i]['subcategory'][$l]['attribute']=[];
				$attribute_master = DB::table('attribute_master')
							->where('spec_subcatId',$subcategory->subcatid)
							->get();
				if(!empty($attribute_master)){
					$k = 0;
					foreach($attribute_master as $attribute)
					{
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_id']=$attribute->id;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_spec_subcatid']=$attribute->spec_subcatId;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_name']=$attribute->text_value;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['created_at'] = date('d-m-Y',strtotime($attribute->created_at));
						if($attribute->updated_at){
							$finalArr[$i]['subcategory'][$l]['attribute'][$k]['updated_at'] = date('d-m-Y',strtotime($attribute->updated_at));
						}else{
							$finalArr[$i]['subcategory'][$l]['attribute'][$k]['updated_at']= '';
						}
						$k++;
					}
				}			
				$l++;
			}	
		}
		$i++;				
	}					  
			
			
  	return $finalArr;					
	}




	public static function listSpecificationCategory($search_keyword,$pageNo){

		$finalArr = array();
		$i = 0;
		if($pageNo) 
		{
			$offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		}else{
			$offset= 0;
		}
			if($search_keyword!="")
		{
			$spec_category = DB::table('spec_category')->where('name','like','%'.$search_keyword.'%')->orderBy('id', 'desc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
		}
		else
		{
			$spec_category = DB::table('spec_category')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
		}
		 
		foreach($spec_category as $eachrow)
		{
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['cat_name'] =$eachrow->name;
			$finalArr[$i]['status'] = $eachrow->status;
			$finalArr[$i]['created_at'] = strtotime($eachrow->created_at)."000";
			//date('d-m-Y',strtotime($eachrow->created_at));
			if($eachrow->updated_at){
				$finalArr[$i]['updated_at'] = strtotime($eachrow->updated_at)."000";
				//date('d-m-Y',strtotime($eachrow->updated_at));
			}else{
				$finalArr[$i]['updated_at'] = '';
			}
		
		$i++;				
	}					  
			
			
  	return $finalArr;					
	}



	public static function listSpecificationSubcategory($search_keyword,$pageNo){

		$finalArr = array();
		$l = 0;
		if($pageNo) 
		{
			$offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		}else{
			$offset= 0;
		}
			if($search_keyword!="")
		{
			$subcategory = DB::table('spec_subcategory')->where('name','like','%'.$search_keyword.'%')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
		}
		else
		{
			$subcategory = DB::table('spec_subcategory')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
		}
		
			
			foreach($subcategory as $subcategory)
			{
				$finalArr[$l]['subcatid'] = $subcategory->id;
				$finalArr[$l]['spec_catid'] = $subcategory->spec_catid;
				$finalArr[$l]['spec_catname'] = self::specificationcatname($subcategory->spec_catid);
				$finalArr[$l]['subcat_name'] = $subcategory->name;
				$finalArr[$l]['status'] = $subcategory->status;
				//$finalArr[$l]['priority'] = $subcategory->priority;
				$finalArr[$l]['threshold'] = $subcategory->threshold;
				$finalArr[$l]['created_at'] = strtotime($subcategory->created_at)."000";
				//date('d-m-Y',strtotime($subcategory->created_at));
				if($subcategory->updated_at){
					$finalArr[$l]['updated_at'] = strtotime($subcategory->updated_at)."000";
					//date('d-m-Y',strtotime($subcategory->updated_at));
				}else{
					$finalArr[$l]['updated_at'] = '';
				}
				$finalArr[$l]['attribute']=[];
				$attribute_master = DB::table('attribute_master')
							->where('spec_subcatId',$subcategory->id)
							->get();
				if(!empty($attribute_master)){
					$k = 0;
					foreach($attribute_master as $attribute)
					{
						$finalArr[$l]['attribute'][$k]['attribute_id']=$attribute->id;
						$finalArr[$l]['attribute'][$k]['attribute_spec_subcatid']=$attribute->spec_subcatId;
						$finalArr[$l]['attribute'][$k]['attribute_name']=$attribute->text_value;
						$finalArr[$l]['attribute'][$k]['created_at'] = date('d-m-Y',strtotime($attribute->created_at));
						if($attribute->updated_at){
							$finalArr[$l]['attribute'][$k]['updated_at'] = date('d-m-Y',strtotime($attribute->updated_at));
						}else{
							$finalArr[$l]['attribute'][$k]['updated_at']= '';
						}
						$k++;
					}
				}			
				$l++;
			}	
		
  	return $finalArr;					
	}


	public static function specificationcatname($spec_catid){
		
	$spec_category = DB::table('spec_category')->where('id',$spec_catid)->first();
			
  	return $spec_category->name;					
	}


	public static function specificationSUBcatname($spec_subcatid){
		
	$spec_category = DB::table('spec_subcategory')->where('id',$spec_subcatid)->first();
			
  	return $spec_category->name;					
	}

	public static function addModelSpecification($model_id,$data,$view_status,$brand){
		
		//print_r($data);die;
		$product_xref = array();
		$product_display = array();
		$dataLen=count($data);
		$count_xref=0;
		$BatteryID=DB::table('spec_subcategory')->select('id')->where(DB::raw("LOWER(name)"), 'LIKE', '%'.strtolower('Battery').'%')->first();

for ($i=0; $i <$dataLen ; $i++) { 
	$attribute=$data[$i]['attribute'];
	if(!empty($attribute)){
		$product_display[$i]['model_id']=$model_id;
		$product_display[$i]['spec_catId']=$data[$i]['catid'];
		$product_display[$i]['spec_subcatId']=$data[$i]['subcatid'];
		$product_display[$i]['brand']=$brand;
		$spec_text='';
		if(is_array($attribute)){
			$att_len=count($attribute);
			for ($k=0; $k <$att_len ; $k++) 
			{
				/* product_xref array */
				$product_xref[$count_xref]['model_id']=$model_id;
				$product_xref[$count_xref]['brand']=$brand;
				$product_xref[$count_xref]['spec_catId']=$data[$i]['catid'];
				$product_xref[$count_xref]['spec_subcatId']=$data[$i]['subcatid'];
				$product_xref[$count_xref]['text_value']=$attribute[$k];
				if($BatteryID->id == $data[$i]['subcatid']){
					//$NumericValue=preg_replace("/[^0-9]/", "",$attribute[$k]);
					preg_match_all('!\d+!', $spec_text, $matches);
					$NumericValue="";
					for ($z=0; $z < count($matches); $z++) { 
						$NumericValue+=implode("",$matches[$z]);
						
					}
					if($NumericValue){
						$product_xref[$count_xref]['numeric_value']=$NumericValue[0];
					}else{
						$product_xref[$count_xref]['numeric_value']=null;
					}
				}else{
					//$product_xref[$count_xref]['numeric_value']=null;
					$numeric=DB::table('attribute_master')->select('numeric_value')->where('spec_subcatId',$data[$i]['subcatid'])->where('text_value','=',$attribute[$k])->first();
					if($numeric){
						$product_xref[$count_xref]['numeric_value']=$numeric->numeric_value;
					}else{
						$product_xref[$count_xref]['numeric_value']=null;
					}
				}
				$count_xref++;
				/* End */

				if($k==($att_len-1)){
					$spec_text=$spec_text.''.$attribute[$k];
				}else{
					$spec_text=$spec_text.''.$attribute[$k].'/';
				}
			} //End of Attribute for loop
		}else{
			$spec_text=$attribute;
			
			/* product_xref array */
				$product_xref[$count_xref]['model_id']=$model_id;
				$product_xref[$count_xref]['brand']=$brand;
				$product_xref[$count_xref]['spec_catId']=$data[$i]['catid'];
				$product_xref[$count_xref]['spec_subcatId']=$data[$i]['subcatid'];
				$product_xref[$count_xref]['text_value']=$attribute;
				

				if($BatteryID->id == $data[$i]['subcatid']){
					$NumericValue=intval($spec_text);//preg_replace("/[^0-9]/", "",$spec_text);
					//get numeric value
					preg_match_all('!\d+!', $spec_text, $matches);
					
					$NumericValue="";
					for ($z=0; $z < count($matches); $z++) { 
						$NumericValue+=implode("",$matches[$z]);
						
					}
					//end get numeric value

					if($NumericValue){
						$product_xref[$count_xref]['numeric_value']=$NumericValue;
					}else{
						$product_xref[$count_xref]['numeric_value']=null;
					}
				}else{
					$product_xref[$count_xref]['numeric_value']=null;	
				}
				
				$count_xref++;
				/* End */


		}// End of Attribute is_array IF ELSE
		$product_display[$i]['spec_text']=$spec_text;
		$product_display[$i]['view_status']=$view_status;
		

	} // End of Empty Value IF 

} // End of data for loop
		
		/* Insert Query */
		
		//print_r($product_xref);die;
		$save=DB::table('product_attribute_xref')->insert($product_xref);
		$save=DB::table('product_spec_display')->insert($product_display);
			
  		return $save;					
	}



	

	public static function getSpecificationBymodel($model_id,$brand){

		$finalArr = array();
		$i = 0;
		$spec_category = DB::table('spec_category')->where('status','1')->orderBy('position', 'asc')->get();
		foreach($spec_category as $eachrow)
		{
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['cat_name'] =$eachrow->name;
			$finalArr[$i]['status'] = $eachrow->status;
			$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
			if($eachrow->updated_at){
				$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
			}else{
				$finalArr[$i]['updated_at'] = '';
			}
			$finalArr[$i]['subcategory']=[];
			$subcategory = DB::table('spec_subcategory')
							->where('status','1')
							->where('spec_catid',$eachrow->id)
							->select('id as subcatid','spec_catid','name','status','created_at','updated_at','threshold')
							->orderBy('position','asc')
							->get();
			if(!empty($subcategory)){
			$l = 0;
			foreach($subcategory as $subcategory)
			{
				$finalArr[$i]['subcategory'][$l]['subcatid'] = $subcategory->subcatid;
				$finalArr[$i]['subcategory'][$l]['spec_catid'] = $subcategory->spec_catid;
				$finalArr[$i]['subcategory'][$l]['subcat_name'] = $subcategory->name;
				$finalArr[$i]['subcategory'][$l]['status'] = $subcategory->status;
				$finalArr[$i]['subcategory'][$l]['threshold'] = $subcategory->threshold;
				$finalArr[$i]['subcategory'][$l]['created_at'] = date('d-m-Y',strtotime($subcategory->created_at));
				if($subcategory->updated_at){
					$finalArr[$i]['subcategory'][$l]['updated_at'] = date('d-m-Y',strtotime($subcategory->updated_at));
				}else{
					$finalArr[$i]['subcategory'][$l]['updated_at'] = '';
				}

				/* Show selected value*/
				$finalArr[$i]['subcategory'][$l]['selected_att_text']=[];
				$finalArr[$i]['subcategory'][$l]['selected_att_numaric']=null;
				$spec = DB::table('product_attribute_xref')->where('model_id',$model_id)->where('spec_catid',$eachrow->id)->where('spec_subcatId',$subcategory->subcatid)->where('brand',$brand)->get();
				if(!empty($spec)){
					
					$specCount=0;
					foreach ($spec as $spec ) {
						$finalArr[$i]['subcategory'][$l]['selected_att_text'][$specCount] = $spec->text_value;
						//$finalArr[$i]['subcategory'][$l]['selected_att_numaric'][$specCount] = $spec->numeric_value;
						$specCount++;
					}
						
				}
				
				/* end  selected value*/
				$finalArr[$i]['subcategory'][$l]['attribute']=[];
				$attribute_master = DB::table('attribute_master')
							->where('spec_subcatId',$subcategory->subcatid)
							->get();
				if(!empty($attribute_master)){
					$k = 0;
					foreach($attribute_master as $attribute)
					{
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_id']=$attribute->id;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_spec_subcatid']=$attribute->spec_subcatId;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['attribute_name']=$attribute->text_value;
						$finalArr[$i]['subcategory'][$l]['attribute'][$k]['created_at'] = date('d-m-Y',strtotime($attribute->created_at));
						if($attribute->updated_at){
							$finalArr[$i]['subcategory'][$l]['attribute'][$k]['updated_at'] = date('d-m-Y',strtotime($attribute->updated_at));
						}else{
							$finalArr[$i]['subcategory'][$l]['attribute'][$k]['updated_at']= '';
						}
						$k++;
					}
				}			
				$l++;
			}	
		}
		$i++;				
	}
					  
			
			
  	return $finalArr;					
	}



	public static function updateModelSpecification($id,$data,$view_status,$brand){

		$save =null;
		$specDelete = DB::table('product_attribute_xref')
						->where('model_id',$id)
						->where(DB::raw("LOWER(brand)"),$brand)
						->delete();
		$specDeleteDisplay = DB::table('product_spec_display')
							->where('model_id',$id)
							->where(DB::raw("LOWER(brand)"),$brand)
							->delete();

		$save= self::addModelSpecification($id,$data,$view_status,$brand);	
  		return $save;					
	}


	public static function SpecificationCategoryById($id){
		
		$finalArr=array();
		$spec_category = DB::table('spec_category')->where('id',$id)->first();
		if(!empty($spec_category)){
			$finalArr['id'] = $spec_category->id;
			$finalArr['name'] =$spec_category->name;
			$finalArr['status'] =$spec_category->status;
			$finalArr['created_at'] = date('d-m-Y',strtotime($spec_category->created_at));
			if($spec_category->updated_at){
				$finalArr['updated_at'] = date('d-m-Y',strtotime($spec_category->updated_at));
			}else{
				$finalArr['updated_at'] = '';
			}
		}
				

		return $finalArr;
	}



	public static function AddSpecCategory($request) { 
		
		$rules = array(
			'name'    => 'required', 
			'status'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$alreadyExist = GStarBaseController::validateForExist('spec_category',$name,'name');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$category['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		if(!$messages->first('status')) {
			$category['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		return $category;
	}

	public static function InsertSpecCategory($category){
			$save =DB::table('spec_category')->insert($category);
			return $save;
	}



	public static function editSpecCategory($request) { 
		
		$rules = array(
			'name'    => 'required', 
			'status'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$id = trim($request->input('id'));
			$alreadyExist = DB::table('spec_category')->select('name')->where('name','=',$name)->where('id','!=',$id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$category['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		if(!$messages->first('status')) {
			$category['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		return $category;
	}


	public static function updateSpecCategory($category,$id){
			$save =DB::table('spec_category')->where('id','=',$id)->update($category);
			return $save;
	}

	public static function getSpecSubcatBycatId($id){
		
		$finalArr=array();
		$i=0;
		$spec_category = DB::table('spec_subcategory')
							->where('spec_catid',$id)
							->orderBy('position','asc')
							->get();
		if(!empty($spec_category)){
			foreach ($spec_category as $key ) {
				$finalArr[$i]['id'] = $key->id;
				$finalArr[$i]['spec_catid'] =$key->spec_catid;
				$finalArr[$i]['spec_catname'] = self::specificationcatname($key->spec_catid);
				$finalArr[$i]['name'] =$key->name;
				$finalArr[$i]['status'] =$key->status;
				//$finalArr[$i]['priority'] =$key->priority;
				$finalArr[$i]['threshold'] =$key->threshold;
				
				$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($key->created_at));
				if($key->updated_at){
					$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($key->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$i++;
			}
		}
		return $finalArr;
	}


	public static function deleteSpecCat($id){
			$save =DB::table('spec_category')->where('id',$id)->delete();
			return $save;
	}

	public static function SpecificationSubCategoryById($id){
		
		$finalArr=array();
		$spec_category = DB::table('spec_subcategory')->where('id',$id)->first();
		if(!empty($spec_category)){
			$finalArr['id'] = $spec_category->id;
			$finalArr['spec_catid'] =$spec_category->spec_catid;
			$finalArr['spec_catname'] = self::specificationcatname($spec_category->spec_catid);
			$finalArr['name'] =$spec_category->name;
			$finalArr['status'] =$spec_category->status;
			//$finalArr['priority'] =$spec_category->priority;
			$finalArr['threshold'] =$spec_category->threshold;	
			$finalArr['created_at'] = date('d-m-Y',strtotime($spec_category->created_at));
			if($spec_category->updated_at){
				$finalArr['updated_at'] = date('d-m-Y',strtotime($spec_category->updated_at));
			}else{
				$finalArr['updated_at'] = '';
			}
		}
				

		return $finalArr;
	}



	public static function AddSpecSUBCategory($request) { 
		
		$rules = array(
			'name'    => 'required', 
			'status'=> 'required',
			'spec_catid'=>'required',
			'threshold'=>'required',
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$subcategory = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$alreadyExist = GStarBaseController::validateForExist('spec_subcategory',$name,'name');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$subcategory['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		if(!$messages->first('status')) {
			$subcategory['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		 if(!$messages->first('spec_catid')) {
			$subcategory['spec_catid'] = trim($request->input('spec_catid'));
	     }
		else {
			Session::flash('msg', $messages->first('spec_catid'));
		  }
		  
		  if(!$messages->first('threshold')) {
			$subcategory['threshold'] = trim($request->input('threshold'));
	     }
		else {
			Session::flash('msg', $messages->first('threshold'));
		  } 

		return $subcategory;
	}

	public static function InsertSpecSUBCategory($subcategory){
			$save =DB::table('spec_subcategory')->insert($subcategory);
			return $save;
	}


	public static function editSpecSUBCategory($request) { 
		
		$rules = array(
			'name'    => 'required', 
			'status'=> 'required',
			'spec_catid'=>'required',
			'threshold'=>'required',
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$subcategory = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$id = trim($request->input('id'));
			$alreadyExist = DB::table('spec_subcategory')->select('name')->where('name','=',$name)->where('id','!=',$id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$subcategory['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		if(!$messages->first('status')) {
			$subcategory['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		 if(!$messages->first('spec_catid')) {
			$subcategory['spec_catid'] = trim($request->input('spec_catid'));
	     }
		else {
			Session::flash('msg', $messages->first('spec_catid'));
		  }
		  
		  if(!$messages->first('threshold')) {
			$subcategory['threshold'] = trim($request->input('threshold'));
	     }
		else {
			Session::flash('msg', $messages->first('threshold'));
		  } 

		return $subcategory;
	}


	public static function updateSpecSUBCategory($subcategory,$id){
		$save =DB::table('spec_subcategory')->where('id','=',$id)->update($subcategory);
		return $save;
	}


	public static function deleteSpecSUBCat($id){
		$save =DB::table('attribute_master')->where('spec_subcatId',$id)->delete();
		$save =DB::table('spec_subcategory')->where('id',$id)->delete();
		return $save;
	}

	public static function AttributeSubCategoryById($id){
		
		$finalArr=array();
		$i=0;
		$attribute = DB::table('attribute_master')->where('spec_subcatId',$id)->get();
		if(!empty($attribute)){
			foreach ($attribute as $key) {
				$finalArr[$i]['id'] = $key->id;
				$finalArr[$i]['spec_subcatId'] =$key->spec_subcatId;
				$finalArr[$i]['spec_subcatname'] = self::specificationSUBcatname($key->spec_subcatId);
				$finalArr[$i]['text_value'] =$key->text_value;
				$finalArr[$i]['numeric_value'] =$key->numeric_value;	
				$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($key->created_at));
				if($key->updated_at){
					$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($key->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$i++;
			}
			
		}
		
		return $finalArr;
	}


	public static function addAttributeSpec($request) { 
		
		$rules = array(
			'name'    => 'required', 
			'subcatid'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$attribute = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$attribute['text_value'] = $name;
			// $alreadyExist = GStarBaseController::validateForExist('attribute_master',$name,'text_value');
		 //    if($alreadyExist){
			// 		Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
			// 		return ;
		 //     }$attribute['text_value'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		if(!$messages->first('subcatid')) {
			$attribute['spec_subcatId'] = trim($request->input('subcatid'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
		  if($request->input('numeric_value')){
		  	$attribute['numeric_value']=trim($request->input('numeric_value'));
		  }else{
		  	$attribute['numeric_value']=null;
		  }
		  

		return $attribute;
	}

	public static function InsertAttributeSpec($attribute){
			$save =DB::table('attribute_master')->insert($attribute);
			return $save;
	}


	public static function editAttributeSpec($request) { 
		
		$rules = array(
			'name'    => 'required', 
			//'subcatid'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$attribute = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$attribute['text_value'] = $name;	
			// $id = trim($request->input('id'));
			// $alreadyExist = DB::table('attribute_master')->select('text_value')->where('text_value','=',$name)->where('id','!=',$id)->count(); 
		 //    if($alreadyExist){
			// 		Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
			// 		return ;
		 //     }$attribute['text_value'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
		} 
		
		// if(!$messages->first('subcatid')) {
		// 	$attribute['spec_subcatId'] = trim($request->input('subcatid'));
	 //     }
		// else {
		// 	Session::flash('msg', $messages->first('status'));
		//   }

		 if($request->input('numeric_value')){
		  	$attribute['numeric_value']=trim($request->input('numeric_value'));
		  }else{
		  	$attribute['numeric_value']=null;
		  } 

		return $attribute;
	}

	public static function UpdateAttributeSpec($attribute,$id){
			//print_r($attribute); echo $id; exit;
			$save =DB::table('attribute_master')->where('id',$id)->update($attribute);
			return $save;
	}


	public static function deleteAttributeSpec($id){
			$save =DB::table('attribute_master')->where('id',$id)->delete();
			return $save;
	}



	public static function AppMfModelList($request){
		$finalArr=array();
		$finalArr=self::MFModel();
		return $finalArr;

	}

	public static function MFModel(){

		$finalArr = array();
		$i=0;
		$gionee="GIONEE";
		
		$manufacture = DB::table('manufacture')->where(DB::raw("LOWER(name)"), 'not like', '%'.strtolower($gionee).'%')->where('status','1')->get();
        foreach($manufacture as $eachrow)
		{
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['name'] =$eachrow->name;
			$finalArr[$i]['asset_image']=[];
			$getListOfImages = DB::table('rec_asset_mapping')
								->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								->where('rec_asset_mapping.module','=','manufacturer')
								->where('rec_asset_mapping.module_id',$eachrow->id)
								->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								->get();
			if(!empty($getListOfImages)){
				$l = 0;
				foreach($getListOfImages as $eachImage)
				{		
					$finalArr[$i]['asset_image'][$l]['image_id'] = $eachImage->id;
					$finalArr[$i]['asset_image'][$l]['name'] = $eachImage->name;
					$finalArr[$i]['asset_image'][$l]['path'] = $eachImage->path;
					$l++;
				}	
			}
			$finalArr[$i]['model']=[];
			$model = DB::table('model')->where('mf_id','=',$eachrow->id)->where('status',1)->get();
			$j=0;
			foreach ($model as $modelkey) {
				$finalArr[$i]['model'][$j]['id']=$modelkey->id;
				$finalArr[$i]['model'][$j]['model_name']=$modelkey->model_name;
				
				$finalArr[$i]['model'][$j]['asset_image']=[];
				$getListOfImages = DB::table('rec_asset_mapping')
								->join('rec_asset_library', 'rec_asset_mapping.asset_library_id', '=', 'rec_asset_library.id')
								->where('rec_asset_mapping.module','=','model')
								->where('rec_asset_mapping.module_id',$modelkey->id)
								->select('rec_asset_library.id','rec_asset_library.name','rec_asset_library.path')
								->get();
				if(!empty($getListOfImages)){
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{		
						$finalArr[$i]['model'][$j]['asset_image'][$l]['image_id'] = $eachImage->id;
						$finalArr[$i]['model'][$j]['asset_image'][$l]['name'] = $eachImage->name;
						$finalArr[$i]['model'][$j]['asset_image'][$l]['path'] = $eachImage->path;
						$l++;
					}	
				}
				$j++;
			}
			$i++;				
		}

  	return $finalArr;	
	}



	public static function AppSearchAttributeList($request){
		$finalArr=array();
		$i=0;
		$att=['RAM','CPU','PRIMARY','BATTERY','PRICE','INTERNAL'];
		$sql = DB::table('spec_subcategory');

		$sql->where(function ($query2) use ($att) {
                foreach ($att as $select) {
		    		$query2->orWhere(DB::raw("LOWER(name)"), 'LIKE', '%'.strtolower($select).'%');
				}  
        });
		$sql->where(function ($query) {
                $query->where('spec_subcategory.status', '=', '1');
                
        });
   	    $attribute = $sql->get();

   	   foreach ($attribute as $key)
   	   {
   	   		$finalArr[$i]['id']=$key->id;

   	   		if($key->name=='Internal' || $key->name=='INTERNAL' || $key->name=='internal')
   	   		{
   	   			$finalArr[$i]['name']='Internal Memory';
   	   		
   	   		}else if($key->name=='Primary' || $key->name=='PRIMARY' || $key->name=='primary'){
   	   			$finalArr[$i]['name']='Primary Camera';
   	   		
   	   		}else{
   	   			$finalArr[$i]['name']=$key->name;	
   	   		}

   	   		$finalArr[$i]['search_attribute']=[];


   	   		if(strtolower($key->name)==strtolower('CPU'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.CpuArray');
   	   			$finalArr[$i]['id_key']='cpu_id';
   	   			$finalArr[$i]['value_key']='cpu';
   	   		}

   	   		if(strtolower($key->name)==strtolower('INTERNAL'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.InternamMMArray');
   	   			$finalArr[$i]['id_key']='intenal_memory_id';
   	   			$finalArr[$i]['value_key']='intenal_memory';

   	   		}

   	   		if(strtolower($key->name)==strtolower('RAM'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.RamArray');
   	   			$finalArr[$i]['id_key']='ram_id';
   	   			$finalArr[$i]['value_key']='ram';

   	   		}

   	   		if(strtolower($key->name)==strtolower('PRIMARY'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.PrimaryCameraArray');
   	   			$finalArr[$i]['id_key']='camera_id';
   	   			$finalArr[$i]['value_key']='camera';

   	   		}

   	   		if(strtolower($key->name)==strtolower('BATTERY'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.BatteryArray');
   	   			$finalArr[$i]['id_key']='battery_id';
   	   			$finalArr[$i]['value_key']='battery';

   	   		}	

   	   		if(strtolower($key->name)==strtolower('PRICE'))
   	   		{
   	   			$finalArr[$i]['search_attribute']=config('constants.PriceArray');
   	   			$finalArr[$i]['id_key']='price_id';
   	   			$finalArr[$i]['value_key']='price';

   	   		}	

   	   		$i++;
   	   }
   	   return $finalArr;


	}

	

	public static function GetAttributeMaster($subcatid){
		$i=0;
		$arr=array();
		$brand=GStarBaseController::GIONEE_BRAND;
		$attribute=DB::table('product_attribute_xref')
						->join('product_spec_display', 'product_attribute_xref.model_id', '=', 'product_spec_display.model_id')
							->select('product_attribute_xref.model_id','product_attribute_xref.spec_catId','product_attribute_xref.spec_subcatId','product_attribute_xref.text_value','product_attribute_xref.numeric_value','product_attribute_xref.brand')
							->where('product_attribute_xref.spec_subcatId',$subcatid)
							->where('product_spec_display.view_status','1')
							->where(DB::raw("LOWER(product_attribute_xref.brand)"), strtolower($brand))
							->orderBy('product_attribute_xref.created_at','DESC')
							->distinct()
							->get();
		foreach ($attribute as $key )
		 {

		 	$arr[$i]['model_id']=$key->model_id;
		 	$arr[$i]['spec_catId']=$key->spec_catId;
		 	$arr[$i]['spec_subcatId']=$key->spec_subcatId;
		 	//$arr[$i]['text_value']=$key->text_value;
		 	$arr[$i]['text_value']=null;
		 	if($float_value_of_var = floatval($key->text_value)){
				$arr[$i]['text_value']=(float)$float_value_of_var;
			}
			else if($int = intval($key->text_value)/*filter_var($key->text_value, FILTER_SANITIZE_NUMBER_INT)*/){
				$arr[$i]['text_value']=$int;
				
			}
		 	$arr[$i]['numeric_value']=$key->numeric_value;
		 	$i++;
		 }	
		 
		 return	$arr;			
	}

	public static function ModelDetails($modelIdArr,$count)
	{
		$brand=GStarBaseController::GIONEE_BRAND;
		$price='Price';
		$finalArr=array();
		$M_INNArr1=array();
		$i=0;
		if(count($modelIdArr)>=1)
		{
			for ($j=0; $j < count($modelIdArr); $j++) { 
				$M_INNArr1[$j]=$modelIdArr[$j]['model_id'];
			}
			$product=DB::table('product')
						->join('category', 'product.category_id', '=', 'category.id')
						->whereIn('product.id',$M_INNArr1)
						->where('product.status','1')
						->where('category.status','1')
						->orderBy('product.launch_date','DESC')
						->select('product.id as product_id','product.product_name','product.launch_date','product.new_product_flag','category.id as category_id','category.category_name')
						->get();
			if($product)
			{
				foreach ($product as $product)
				{
					$finalArr[$i]['id']=$product->product_id;
					$finalArr[$i]['product_name']=$product->product_name;
					$finalArr[$i]['new_product_flag']=$product->new_product_flag;
					$finalArr[$i]['category_id']=$product->category_id;
					$finalArr[$i]['category_name']=$product->category_name;
					$finalArr[$i]['launch_date']=$product->launch_date;
					//date('d-m-Y',strtotime($product->launch_date));

					$priceId=DB::table('spec_subcategory')
								->select('id')
								->where(DB::raw("LOWER(name)"), strtolower($price))
								->first();
					$finalArr[$i]['price']=null;
					if($priceId){
						$attribute=DB::table('product_attribute_xref')
							->join('product_spec_display', 'product_attribute_xref.model_id', '=', 'product_spec_display.model_id')
								->select('product_attribute_xref.text_value')
								->where('product_attribute_xref.spec_subcatId',$priceId->id)
								->where('product_spec_display.view_status','1')
								->where('product_spec_display.model_id',$product->product_id)
								->where(DB::raw("LOWER(product_attribute_xref.brand)"), strtolower($brand))
								->first();	
						if($attribute){
							$finalArr[$i]['price']=$attribute->text_value;
						}		
					}
					$finalArr[$i]['pro_asset']=null;
					$getListOfImages = DB::table('asset_mapping')
							->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
							->where('asset_mapping.module','=','product')
							->where('asset_library.type','=','image')
							->where('asset_mapping.module_id',$modelIdArr[$i]['model_id'])
							->select('asset_library.id','asset_library.name','asset_library.path')
							->get();
					if(!empty($getListOfImages)){
						$j=0;
						foreach($getListOfImages as $eachImage)
						{
							$finalArr[$i]['pro_asset'][$j]['asset_id'] = $eachImage->id;
							$finalArr[$i]['pro_asset'][$j]['name'] = $eachImage->name;
							$finalArr[$i]['pro_asset'][$j]['path'] = $eachImage->path;
							$j++;
						}
					}
				$i++;
				}
			}

		}
		$finalArr2=array();
		
		if(!empty($finalArr)){

			if($count >= count($finalArr)){
				$count=count($finalArr);
			}

			for ($k=0; $k < $count; $k++) { 
				$finalArr2[$k]=$finalArr[$k];
			}
		}
		return $finalArr2;
		
	}

	public static function SearchByMF($model_ids)
	{
		$GIONEE_BRAND=GStarBaseController::GIONEE_BRAND;
		$OTHER_BRAND=GStarBaseController::OTHER_BRAND;
		$finalDataArr=array();
		$ModelIDARRAY=array();
		// $CPU_ID=null; $RAM_ID=null; $PRIMARY_ID=null;
		// $BATTERY_ID=null; $PRICE_ID=null; $INTERNAL_ID=null;

		$CPU_ID=0; $RAM_ID=0; $PRIMARY_ID=0;
		$BATTERY_ID=0; $PRICE_ID=0; $INTERNAL_ID=0;

		$CPU_IDARR=Array(); $RAM_IDARR=Array(); $PRIMARY_IDARR=Array();
		$BATTERY_IDARR=Array(); $PRICE_IDARR=Array(); $INTERNAL_IDARR=Array();

		Session::forget('msg');
		$att=['RAM','CPU','PRIMARY','BATTERY','PRICE','INTERNAL'];
		// $sql = DB::table('spec_subcategory');
		// foreach ($att as $select) {
		//     $sql->orWhere(DB::raw("LOWER(name)"), 'LIKE', '%'.strtolower($select).'%');
		// }
  //  	    $attribute = $sql->get();

		$sql = DB::table('spec_subcategory');

		$sql->where(function ($query2) use ($att) {
                foreach ($att as $select) {
		    		$query2->orWhere(DB::raw("LOWER(name)"), '=',strtolower($select));
				}  
        });
		$sql->where(function ($query) {
                $query->where('spec_subcategory.status', '=', '1');
                
        });
        $sql->limit(6);
   	    $attribute = $sql->get();
   	    
		$k=0;
		if($model_ids)
		{
			/* Expload search Model and Brand*/
			$Search=explode(',', $model_ids);
			$ModelIDARRAY=$Search;

			//Find Search Key ID and VALUE
			foreach ($attribute as $key ) {
				if(strtolower($key->name) == strtolower('CPU')){
					$CPU_ID=$key->id;
				}else if(strtolower($key->name) == strtolower('RAM')){
					$RAM_ID=$key->id;
				}else if(strtolower($key->name) == strtolower('PRIMARY')){
					$PRIMARY_ID=$key->id;
				}else if(strtolower($key->name) == strtolower('BATTERY')){
					$BATTERY_ID=$key->id;
				}else if(strtolower($key->name) == strtolower('PRICE')){
					$PRICE_ID=$key->id;
				}else if(strtolower($key->name) == strtolower('INTERNAL')){
					$INTERNAL_ID=$key->id;
				}
			
			} // IDS

			$Get_Other_ModelAtt=self::GetOther_ModelAtt($OTHER_BRAND,$ModelIDARRAY,$attribute);
			
			$j=0;

			if(!empty($Get_Other_ModelAtt))
			{
				foreach ($Get_Other_ModelAtt as $key )
				{
					switch ($key->spec_subcatId)
					{
						case $CPU_ID:
								$CPU_IDARR['numeric_value'][]=$key->numeric_value;
								if($float_value_of_var = floatval($key->text_value)){
									$CPU_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$CPU_IDARR['text_value'][]=$int;	
								}else{
									$CPU_IDARR['text_value'][]=null;	
								}
							break;
						
						case $RAM_ID:
								$RAM_IDARR['numeric_value'][]=$key->numeric_value;
								if($float_value_of_var = floatval($key->text_value)){
									$RAM_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$RAM_IDARR['text_value'][]=$int;	
								} else {
									$RAM_IDARR['text_value'][]=null;	
								}
							break;

						case $BATTERY_ID:
								$BATTERY_IDARR['numeric_value'][]=$key->numeric_value;
							
								if($float_value_of_var = floatval($key->text_value)){
									$BATTERY_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$BATTERY_IDARR['text_value'][]=$int;	
								} else {
									$BATTERY_IDARR['text_value'][]=null;
								}
							break;
							
						case $PRICE_ID:
								$PRICE_IDARR['numeric_value'][]=$key->numeric_value;
								if($float_value_of_var = floatval($key->text_value)){
									$PRICE_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$PRICE_IDARR['text_value'][]=$int;	
								} else {
									$PRICE_IDARR['text_value'][]=null;
								}
							break;
										
						case $PRIMARY_ID:
								$PRIMARY_IDARR['numeric_value'][]=$key->numeric_value;
							
								if($float_value_of_var = floatval($key->text_value)){
									$PRIMARY_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$PRIMARY_IDARR['text_value'][]=$int;	
								} else {
									$PRIMARY_IDARR['text_value'][]=null;
								}
							break;

						case $INTERNAL_ID:
								$INTERNAL_IDARR['numeric_value'][]=$key->numeric_value;
							
								if($float_value_of_var = floatval($key->text_value)){
									$INTERNAL_IDARR['text_value'][]=(float)$float_value_of_var;
								} else if($int = intval($key->text_value)){
									$INTERNAL_IDARR['text_value'][]=$int;	
								} else {
									$INTERNAL_IDARR['text_value'][]=null;
								}
							break;	
					}
					
				}	
			}

			// if (!$PRICE_ID) { $PRICE_ID=0;}
			// if (!$RAM_ID) { $RAM_ID=0;}
			// if (!$PRIMARY_ID) { $PRIMARY_ID=0;}
			// if (!$BATTERY_ID) { $BATTERY_ID=0;}		
			// if (!$INTERNAL_ID) { $INTERNAL_ID=0;}
			// if (!$CPU_ID) { $CPU_ID=0;}

			if (empty($PRICE_IDARR)) { $PRICE_IDARR['text_value'][]=0;}
			if (empty($RAM_IDARR)) { $RAM_IDARR['numeric_value'][]=0;}
			if (empty($PRIMARY_IDARR)) { $PRIMARY_IDARR['numeric_value'][]=0;}
			if (empty($BATTERY_IDARR)) { $BATTERY_IDARR['numeric_value'][]=0;}
			if (empty($INTERNAL_IDARR)) { $INTERNAL_IDARR['numeric_value'][]=0;}
			if (empty($CPU_IDARR)) { $CPU_IDARR['numeric_value'][]=0;}

			
			// print_r($PRICE_IDARR);
			// print_r($RAM_IDARR);
			// print_r($PRIMARY_IDARR);
			// print_r($BATTERY_IDARR);
			// print_r($INTERNAL_IDARR);
			// print_r($CPU_IDARR);
			
			// die;

			$sql2="SELECT DISTINCT PAX.model_id FROM product_attribute_xref PAX,product_spec_display PSD  WHERE ";
		     //END Find Search Key ID and VALUE

				$sql2=$sql2." PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$PRICE_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($PRICE_IDARR['text_value']) == 1){
					$min=min($PRICE_IDARR['text_value'])-self::GetThreshold($attribute,$PRICE_ID);
					if($min <=0){
						$min=0;
					}
					if($min ==0){
						$max=50000;
					}else{
						$max=max($PRICE_IDARR['text_value'])+self::GetThreshold($attribute,$PRICE_ID);
					}

					$sql2=$sql2." (p.text_value >=".$min." AND p.text_value <= ".$max."))";
				}else {
					$min=min($PRICE_IDARR['text_value'])-self::GetThreshold($attribute,$PRICE_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($PRICE_IDARR['text_value'])+self::GetThreshold($attribute,$PRICE_ID);
					$sql2=$sql2." (p.text_value >=".$min." AND p.text_value <= ".$max."))";
				}
				
				$sql2=$sql2." AND PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$RAM_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($RAM_IDARR['numeric_value']) == 1){
					$min=min($RAM_IDARR['numeric_value'])-self::GetThreshold($attribute,$RAM_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($RAM_IDARR['numeric_value'])+self::GetThreshold($attribute,$RAM_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";	
				}else {
					$min=min($RAM_IDARR['numeric_value'])-self::GetThreshold($attribute,$RAM_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($RAM_IDARR['numeric_value'])+self::GetThreshold($attribute,$RAM_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}

				$sql2=$sql2." AND PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$PRIMARY_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($PRIMARY_IDARR['numeric_value']) == 1){
					//$min=min($PRIMARY_IDARR['numeric_value']);
					$min=min($PRIMARY_IDARR['numeric_value'])-self::GetThreshold($attribute,$PRIMARY_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($PRIMARY_IDARR['numeric_value'])+self::GetThreshold($attribute,$PRIMARY_ID);
				
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}else {
					//$min=min($PRIMARY_IDARR['numeric_value']);
					$min=min($PRIMARY_IDARR['numeric_value'])-self::GetThreshold($attribute,$PRIMARY_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($PRIMARY_IDARR['numeric_value'])+self::GetThreshold($attribute,$PRIMARY_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}

				$sql2=$sql2." AND PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$BATTERY_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($BATTERY_IDARR['numeric_value']) == 1){
					//$min=min($BATTERY_IDARR['numeric_value']);
					$min=min($BATTERY_IDARR['numeric_value'])-self::GetThreshold($attribute,$BATTERY_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($BATTERY_IDARR['numeric_value'])+self::GetThreshold($attribute,$BATTERY_ID);
				
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}else {
					//$min=min($BATTERY_IDARR['numeric_value']);
					$min=min($BATTERY_IDARR['numeric_value'])-self::GetThreshold($attribute,$BATTERY_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($BATTERY_IDARR['numeric_value'])+self::GetThreshold($attribute,$BATTERY_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}

				$sql2=$sql2." AND PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$INTERNAL_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($INTERNAL_IDARR['numeric_value']) == 1){
					//$min=min($INTERNAL_IDARR['numeric_value']);
					$min=min($INTERNAL_IDARR['numeric_value'])-self::GetThreshold($attribute,$INTERNAL_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($INTERNAL_IDARR['numeric_value'])+self::GetThreshold($attribute,$INTERNAL_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
			
				}else {
					//$min=min($INTERNAL_IDARR['numeric_value']);
					$min=min($INTERNAL_IDARR['numeric_value'])-self::GetThreshold($attribute,$INTERNAL_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($INTERNAL_IDARR['numeric_value'])+self::GetThreshold($attribute,$INTERNAL_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}

				$sql2=$sql2." AND PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$CPU_ID." and x.view_status='1' and p.brand = 'gionee' and ";
				if(count($CPU_IDARR['numeric_value']) == 1){
					//$min=min($CPU_IDARR['numeric_value']);
					$min=min($CPU_IDARR['numeric_value'])-self::GetThreshold($attribute,$CPU_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($CPU_IDARR['numeric_value'])+self::GetThreshold($attribute,$CPU_ID);
				
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}else {
					//$min=min($CPU_IDARR['numeric_value']);
					$min=min($CPU_IDARR['numeric_value'])-self::GetThreshold($attribute,$CPU_ID);
					if($min <=0){
						$min=0;
					}
					$max=max($CPU_IDARR['numeric_value'])+self::GetThreshold($attribute,$CPU_ID);
					$sql2=$sql2." (p.numeric_value >=".$min." AND p.numeric_value <= ".$max."))";
				}

			$sql2=$sql2."  AND PAX.spec_subcatid IN (".$PRICE_ID.",".$RAM_ID.",".$PRIMARY_ID.",".$INTERNAL_ID.",".$BATTERY_ID.",".$CPU_ID;
			$sql2=$sql2.") and PAX.brand = 'gionee' and PSD.view_status= '1'";
			
			// print_r($sql2);
			// die;
			return $sql2;

			
		}else{
			Session::flash('msg', 'model_ids required or worng');
			return;
		}
			
	} //end of Manufacture


	public static function GetThreshold($attribute,$subcatId)
	{
		$threshold=0;
		foreach ($attribute as $key ) {
			if($key->id ==$subcatId)
			$threshold=$key->threshold; 
		}
		return $threshold;
	}


	


	public static function GetOther_ModelAtt($OTHER_BRAND,$ModelIDARRAY,$attribute)
	{
		$i=0;
		$arr=array();
		$att_IDS=array();
		$j=0;
		foreach ($attribute as $key ) {
			$att_IDS[$j]=$key->id;
			$j++;
		}
		$sql=DB::table('product_attribute_xref')
						 ->join('product_spec_display', 'product_spec_display.brand', '=', 'product_attribute_xref.brand')
						->select('product_attribute_xref.model_id','product_attribute_xref.spec_catId','product_attribute_xref.spec_subcatId','product_attribute_xref.text_value','product_attribute_xref.numeric_value')
						->whereIn('product_attribute_xref.model_id',$ModelIDARRAY)
						->whereIn('product_attribute_xref.spec_subcatId', $att_IDS)
						->where(DB::raw("LOWER(product_attribute_xref.brand)"),strtolower($OTHER_BRAND))
						->where('product_spec_display.view_status','1')
						->orderBy('product_attribute_xref.spec_subcatId','DESC')
						->distinct()->get();
		
		return	$sql;			
	}




	public static function AppPhoneCompair($request){

		$gionee_brand=GStarBaseController::GIONEE_BRAND;
		$other_brand=GStarBaseController::OTHER_BRAND;
		Session::forget('msg');
		$finalArr=array();
		$i=0;
		$gionee_model=trim($request->input('gionee_model'));
		$other_model=trim($request->input('other_model'));
		if($gionee_model && $other_model){
			$spec_category = DB::table('spec_category')
							->where('status','1')
							->orderBy('position','asc')
							->get();
			foreach($spec_category as $eachrow)
			{
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['cat_name'] =$eachrow->name;
				$finalArr[$i]['subcategory']=[];
				$subcategory = DB::table('spec_subcategory')
							->where('status','1')
							->where('spec_catid',$eachrow->id)
							->orderBy('position','asc')
							->select('id as subcatid','spec_catid','name','status','created_at','updated_at','threshold')
							->get();
				if(!empty($subcategory)){
					$l = 0;
					foreach($subcategory as $subcategory)
					{
						$finalArr[$i]['subcategory'][$l]['subcatid'] = $subcategory->subcatid;
						$finalArr[$i]['subcategory'][$l]['subcat_name'] = $subcategory->name;
						$finalArr[$i]['subcategory'][$l]['gionee']=null;
						$finalArr[$i]['subcategory'][$l]['other']=null;
						$gionee_Model = DB::table('product_spec_display')
										->where('model_id',$gionee_model)
										->where('spec_catid',$eachrow->id)
										->where('spec_subcatId',$subcategory->subcatid)
										->where('brand',$gionee_brand)->first();
						if(!empty($gionee_Model)){
							$finalArr[$i]['subcategory'][$l]['gionee'] = $gionee_Model->spec_text;
						}

						$other_Model = DB::table('product_spec_display')
										->where('model_id',$other_model)
										->where('spec_catid',$eachrow->id)
										->where('spec_subcatId',$subcategory->subcatid)
										->where('brand',$other_brand)->first();
						if(!empty($other_Model)){
							$finalArr[$i]['subcategory'][$l]['other'] = $other_Model->spec_text;
						}
				
				
						$l++;
					}	
				}
				$i++;				
			}
			return $finalArr;
		}else{
			Session::flash('msg', 'gionee_model and other_model required');
			return;
		}
	}


	public static function uploadFile($request)
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
		     //$type = array('jpg','JPG','png','PNG');
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

	public static function getPublishStatus($id,$brand){
		$status = DB::table('product_spec_display')->select('view_status')->where('model_id',$id)->where('brand',$brand)->get();
		//print_r($status);
		if($status){
			return $status[0]->view_status;
		}else{
			return 0;
		}
		
	}


	public static function MF_ProductName($id,$brand){
		$finalArr=array();
		if(strtolower($brand) == strtolower(GStarBaseController::GIONEE_BRAND)){
			$getDetail=DB::table('product') 
								->join('category', 'category.id', '=', 'product.category_id')
								->select('product.id','product.category_id','product.product_name', 'category.category_name')
								->where('product.id',$id)
								->first();
			if($getDetail){
				$finalArr['category']=$getDetail->category_name;
				$finalArr['product']=$getDetail->product_name;
			}					


		}else if (strtolower($brand) == strtolower(GStarBaseController::OTHER_BRAND)) {
			
			 $model = DB::table('model') 
								->join('manufacture', 'manufacture.id', '=', 'model.mf_id')
								->select('model.id','model.mf_id','model.model_name', 'manufacture.name')
								->where('model.id',$id)
								->first();
			if($model){
				$finalArr['category']=$model->name;
				$finalArr['product']=$model->model_name;
			}	


		} else{
			$finalArr= null;
		}

		return $finalArr;
		
	}



public static function FindSearchCategorySubcategory($id,$spec)
{
	$arraycat=array();
	$att=['RAM','CPU','PRIMARY','BATTERY','PRICE','INTERNAL'];
	$sql = DB::table('spec_subcategory');
	$sql->where(function ($query2) use ($att) {
        foreach ($att as $select) {
		    $query2->orWhere(DB::raw("LOWER(name)"), 'LIKE', '%'.strtolower($select).'%');
		}  
    });
	$attribute = $sql->get();
	$i=0;
	$r_return=0;
	if($attribute){
			foreach ($attribute as $key ) {
				$arraycat['id'][$i]=$key->id;
				$arraycat['spec_catid'][$i]=$key->spec_catid;
				$i++;
			}
			if($spec == 'spec_subcategory')
			{
				$search=array_search($id,$arraycat['id']);
				$r_return = 0;
				if($search){$r_return = 1;}
				
			}else if($spec == 'spec_category'){
					$search=array_search($id,$arraycat['spec_catid']);
					$r_return = 1;
					if($search){$r_return = 1;}
			}else{
				$r_return = 0;
			}
			
	}else{
		$r_return = 0;
	}

	return $r_return;
	
}



public static function UndeleteId($table)
{
	$arraycat=array();
	$att=['RAM','CPU','PRIMARY','BATTERY','PRICE','INTERNAL'];
	$sql = DB::table('spec_subcategory');
	$sql->where(function ($query2) use ($att) {
        foreach ($att as $select) {
		    $query2->orWhere(DB::raw("LOWER(name)"), 'LIKE', '%'.strtolower($select).'%');
		}  
    });
	$attribute = $sql->get();
	$i=0;
	$r_return=0;
	$data=array();
	if($attribute){
		foreach ($attribute as $key ) {
			$arraycat['id'][$i]=$key->id;
			$arraycat['spec_catid'][$i]=$key->spec_catid;
			$i++;
		}

		if($table == 'spec_subcategory')
		{			
			$data= $arraycat['id'];//array_unique($arraycat['id']);		
		}else if($table == 'spec_category'){
			$data=$arraycat['spec_catid'];//array_unique($arraycat['spec_catid']);			
		}
			
	}
	return $data;
	
}


public static function changeOrderofSpecificationCategory($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('spec_category')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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



public static function changeOrderofSpecificationSubCategory($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('spec_subcategory')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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



}
