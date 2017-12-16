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

class Tutorial extends Authenticatable
{
	 

	
	public static function AppgetListOfTutorial(){
		
		$finalArr = array();
		$i=0;
		$getListOfAllTutorial = DB::table('video_tutorials')
								->join('category', 'video_tutorials.category_id', '=', 'category.id')
								->select('video_tutorials.category_id')
								->where('video_tutorials.status','1')
								->where('category.status','1')
								->groupBy('video_tutorials.category_id')
								->orderBy('category.position', 'asc')
								->orderBy('video_tutorials.position', 'asc')
								->get();
		foreach($getListOfAllTutorial as $eachrow)
		{
			$category_id=$eachrow->category_id;
			$category=DB::table('category')->where('id',$category_id)->first();
			$getListOfImages = DB::table('asset_mapping')
								->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								->where('asset_mapping.module','=','category')
								->where('asset_mapping.module_id',$category_id)
								->select('asset_library.id','asset_library.title','asset_library.name','asset_library.path','asset_library.type')
								->get(); 
								 
				$finalArr[$i]['id'] = $category->id;
				$finalArr[$i]['category_name'] = $category->category_name;
				$finalArr[$i]['position'] = ($category->position+1);
				$finalArr[$i]['category_parent_id'] = NULL;
				$finalArr[$i]['status'] = $category->status;
				$finalArr[$i]['description'] = $category->description;
				$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($category->created_at));
				$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($category->updated_at));
				$finalArr[$i]['cat_image']=[];
				if(!empty($getListOfImages)){
					$j = 0;
					$l = 0;
					foreach($getListOfImages as $eachImage)
					{
						if($eachImage->type == 'image')
						{
							$finalArr[$i]['cat_image'][$j]['image_id'] = $eachImage->id;
							$finalArr[$i]['cat_image'][$j]['name'] = $eachImage->name;
							$finalArr[$i]['cat_image'][$j]['title'] = $eachImage->title;
							$finalArr[$i]['cat_image'][$j]['path'] = $eachImage->path;
							$j++;
						}	
					}
				}
				$GetProduct = DB::table('video_tutorials')
				 			->join('tutorial_subcat', 'tutorial_subcat.id', '=', 'video_tutorials.subcat_id')
								  ->select('video_tutorials.subcat_id')
								  ->where('video_tutorials.category_id',$eachrow->category_id)
								  ->where('video_tutorials.status','1')
								  ->orderBy('tutorial_subcat.position', 'asc')
								  ->distinct()
								  ->get(); 
				$p=0;
				foreach ($GetProduct as $GetProduct){
					$product_id=$GetProduct->subcat_id;
					$Product=DB::table('tutorial_subcat')->where('id',$product_id)->first();
					$finalArr[$i]['product'][$p]['id']=$Product->id;
					$finalArr[$i]['product'][$p]['category_id']=$Product->category_id;
					$finalArr[$i]['product'][$p]['product_name']=$Product->name;
					$finalArr[$i]['product'][$p]['product_desc']=null;
					$finalArr[$i]['product'][$p]['is_new']=null;
					$finalArr[$i]['product'][$p]['status']=null;
					$finalArr[$i]['product'][$p]['desc1']=null;
					$finalArr[$i]['product'][$p]['desc2']=null;
					$finalArr[$i]['product'][$p]['desc3']=null;
					$finalArr[$i]['product'][$p]['launch_date']=null;
					$finalArr[$i]['product'][$p]['position']=null;
					$finalArr[$i]['product'][$p]['created_at']=null;
					$finalArr[$i]['product'][$p]['pro_image']=[];
					if($Product->created_at){
							$finalArr[$i]['product'][$p]['updated_at'] = date('d-m-Y',strtotime($Product->created_at));
						}else{
						$finalArr[$i]['product'][$p]['updated_at'] = '';
						}
					$finalArr[$i]['product'][$p]['updated_at']=$Product->updated_at;
					
				$getDetail = DB::table('video_tutorials')
								  ->where('category_id',$category_id)
								  ->where('subcat_id',$product_id)
								  ->where('status','=','1')
								   // ->Where(function ($query) {
						     //            $query ->whereNotNull('video_path')
						     //                  ->whereNotNull('youtube_url');
						     //        })
								  //->whereNotNull('video_path')
								  //->whereNotNull('youtube_url')
								  ->orderBy('position', 'asc')
								  ->get(); 
				$j=0;
				if(!empty($getDetail)){
					$finalArr[$i]['product'][$p]['tutorials']['video_count'] = count($getDetail);
					foreach($getDetail as $key){
						//if(($key->video_path!=null) || ($key->youtube_url != null)){
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['video_id'] = $key->video_id;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['title'] = $key->title;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['short_description'] = $key->short_description;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['video_path'] = $key->video_path;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['category_id'] = $key->category_id;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['channel_name'] = $key->channel_name;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['product_id'] = $key->subcat_id;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['youtube_url'] = $key->youtube_url;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['thumbnail'] = $key->thumbnail;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['duration'] = Tutorial::DurationFormate($key->duration);
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['status'] = $key->status;
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['created_at'] = date('d-m-Y',strtotime($key->created_at));
							if($key->updated_at){
								$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['updated_at'] = date('d-m-Y',strtotime($key->updated_at));
							}else{
							$finalArr[$i]['product'][$p]['tutorials']['video'][$j]['updated_at'] = '';
							}
							$j++;
						//}
						
				  	}
				 }else{
				 	$finalArr[$i]['product'][$p]['tutorials']['video_count']=0;
				 	$finalArr[$i]['product'][$p]['tutorials']['video']=null;
				 }
				$p++;
			}	
				$i++;				
			}					

  	return $finalArr;					
	}



	 public static function getListOfTutorial($search_keyword,$pageNo){
		
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
			 $getListOfAllTutorial = DB::table('video_tutorials')
			  ->where('title','like','%'.$search_keyword.'%')->orderBy('position', 'asc')
			  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }
        else
		{
		     $getListOfAllTutorial = DB::table('video_tutorials')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
         }

         //print_r($getListOfAllTutorial); exit;
		 
			foreach($getListOfAllTutorial as $eachrow)
			{
				$finalArr[$i]['video_id'] = $eachrow->video_id;
				$finalArr[$i]['title'] = $eachrow->title;
				$finalArr[$i]['short_description'] = $eachrow->short_description;
				$finalArr[$i]['video_path'] = $eachrow->video_path;
				$finalArr[$i]['category_id'] = $eachrow->category_id;
				$finalArr[$i]['product_id'] = $eachrow->subcat_id;
				$finalArr[$i]['youtube_url'] = $eachrow->youtube_url;
				$finalArr[$i]['thumbnail'] = $eachrow->thumbnail;
				$finalArr[$i]['duration'] = Tutorial::DurationFormate($eachrow->duration);
				$finalArr[$i]['status'] = $eachrow->status;
				$finalArr[$i]['created_at'] = strtotime($eachrow->created_at)."000";
				//date('d-m-Y',strtotime($eachrow->created_at));
				if($eachrow->updated_at){
					$finalArr[$i]['updated_at'] = strtotime($eachrow->updated_at)."000";
					//date('d-m-Y',strtotime($eachrow->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$category=DB::table('category')->select('category_name')->where('id',$eachrow->category_id)->first();
				$product=DB::table('tutorial_subcat')->select('name')->where('id',$eachrow->subcat_id)->first();
				$finalArr[$i]['category_name']=$category->category_name;
				$finalArr[$i]['product_name']=$product->name;
						
				$i++;				
			}					

  	return $finalArr;					
	}

	 public static function DurationFormate($duration){
	 	if($duration>=3600){
	 		return gmdate("H:i:s", $duration);
	 	}else{
	 		return gmdate("i:s", $duration);
	 	}
	 	
	 }
	


	//  public static function getAllCategoryOfTutorialWithProduct(){
		
	// 	     $finalArr = array();
	// 			$i = 0;
	// 	     $getCategory = DB::table('category')->select('id','category_name')->where('is_tutorial',1)->where('status',1)->orderBy('position', 'asc')->get();
	// 	     //print_r($getCategory); exit;
	// 	     foreach ($getCategory as $key ) {
	// 	     	$category_id=$key->id;
	// 	     	$finalArr[$i]['id']=$category_id;
	// 	     	$finalArr[$i]['category_name']=$key->category_name;
	// 	     	$getProduct = DB::table('product')->select('id','product_name')->where('category_id',$category_id)->where('status',1)->orderBy('position', 'asc')->get();
		     	
	// 	     	$j=0;
	// 	     	$finalArr[$i]['product']=[];
	// 	     	foreach ($getProduct as $key2) {
	// 	     		$finalArr[$i]['product'][$j]['id']=$key2->id;
	// 	     		$finalArr[$i]['product'][$j]['product_name']=$key2->product_name;
	// 	     		$j++;
	// 	     	}
	// 	     	$i++;
	// 	     }
	// 	  return $finalArr;
								
	// }


	 public static function getAllCategoryOfTutorialWithNewSubcat()
	 {
		$finalArr = array();
		$i = 0;
		$getCategory = DB::table('category')->select('id','category_name')->where('is_tutorial',1)->orderBy('position', 'asc')->get();
		     foreach ($getCategory as $key ) {
		     	$category_id=$key->id;
		     	$finalArr[$i]['id']=$category_id;
		     	$finalArr[$i]['category_name']=$key->category_name;
		     	$getProduct = DB::table('tutorial_subcat')->select('id','name')->where('category_id',$category_id)->get();
		     	
		     	$j=0;
		     	$finalArr[$i]['product']=[];
		     	foreach ($getProduct as $key2) {
		     		$finalArr[$i]['product'][$j]['id']=$key2->id;
		     		$finalArr[$i]['product'][$j]['name']=$key2->name;
		     		$j++;
		     	}
		     	$i++;
		     }
		  return $finalArr;
								
	}

	// public static function getTutorialwithProductByCategory($cat_id) { 
		
	// 	$finalArr = array();
	// 	$i=0;

	// 	if(!empty($cat_id)){
	// 		$tutorialList=DB::table('video_tutorials')->select('product_id')->where('category_id',$cat_id)->groupBy('product_id')->get(); 
	// 	}else{
	// 		$tutorialList=DB::table('video_tutorials')->select('product_id')->groupBy('category_id','product_id')->get(); 
	// 	}
	// 	foreach ($tutorialList as $key) {
	// 		$getListOfAllProducts = DB::table('product')->where('product.id',$key->product_id)->first();
	// 		$getListOfImages = DB::table('asset_mapping')
	// 					->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
	// 					->where('asset_mapping.module','=','product')
	// 					->where('asset_mapping.module_id',$key->product_id)
	// 					->select('asset_library.id','asset_library.name','asset_library.path')
	// 					->get();

	// 		$finalArr[$i]['product_detail']['product']['id']=$getListOfAllProducts->id;
	// 		$finalArr[$i]['product_detail']['product']['category_id']=$getListOfAllProducts->category_id;
	// 		$finalArr[$i]['product_detail']['product']['product_name']=$getListOfAllProducts->product_name;
	// 		$finalArr[$i]['product_detail']['product']['product_desc']=$getListOfAllProducts->id;
	// 		$finalArr[$i]['product_detail']['product']['id']=$getListOfAllProducts->product_desc;	
	// 		$finalArr[$i]['product_detail']['product']['status']=$getListOfAllProducts->status;
	// 		$finalArr[$i]['product_detail']['product']['desc1']=$getListOfAllProducts->desc1;
	// 		$finalArr[$i]['product_detail']['product']['desc2']=$getListOfAllProducts->desc2;
	// 		$finalArr[$i]['product_detail']['product']['desc3']=$getListOfAllProducts->desc3;
	// 		$finalArr[$i]['product_detail']['product']['new_product_flag']=$getListOfAllProducts->new_product_flag;
	// 		$finalArr[$i]['product_detail']['product']['created_at']=$getListOfAllProducts->created_at;
	// 		$finalArr[$i]['product_detail']['product']['updated_at']=$getListOfAllProducts->updated_at;
	// 				$j=0;
	// 				$finalArr[$i]['product_detail']['product']['pro_asset']=[];
	// 		foreach($getListOfImages as $eachImage){	
	// 		 $finalArr[$i]['product_detail']['product']['pro_asset'][$j]['id'] =$eachImage->id;
	// 		$finalArr[$i]['product_detail']['product']['pro_asset'][$j]['name'] = $eachImage->name;
	// 		$finalArr[$i]['product_detail']['product']['pro_asset'][$j]['path'] = $eachImage->path;
	// 				$j++;
	// 		}
	// 		$getDetail = DB::table('video_tutorials')
	// 							  ->where('product_id',$key->product_id)
	// 							  ->where('category_id',$cat_id)
	// 							  ->where('status','1')
	// 							  ->get(); 
	// 		$k=0;
	// 		$finalArr[$i]['product_detail']['product']['video']=[];
	// 		foreach($getDetail as $Videokey){
	// 		 $finalArr[$i]['product_detail']['product']['video'][$k]['video_id'] = $Videokey->video_id;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['title'] = $Videokey->title;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['short_description'] = $Videokey->short_description;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['video_path'] = $Videokey->video_path;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['category_id'] = $Videokey->category_id;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['product_id'] = $Videokey->product_id;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['youtube_url'] = $Videokey->youtube_url;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['thumbnail'] = $Videokey->thumbnail;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['duration'] = Tutorial::DurationFormate($Videokey->duration);
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['status'] = $Videokey->status;
	// 		$finalArr[$i]['product_detail']['product']['video'][$k]['created_at'] = date('d-m-Y',strtotime($Videokey->created_at));
	// 		if($Videokey->updated_at){
	// 		 $finalArr[$i]['product_detail']['product']['video'][$k]['updated_at'] = date('d-m-Y',strtotime($Videokey->updated_at));
	// 		}else{
	// 			$finalArr[$i]['product_detail']['product']['video'][$k]['updated_at'] = '';
	// 		}
	// 		$k++;   
	// 	 }
				  	
	// 		$i++;
	// 	}
		
	    
	// 	return $finalArr;
	// }





	public static function createTutorial($request) { 
		

		$rules = array(
			'title'    => 'required', 
			'category_id' => 'required', 
			'status'=> 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();

		Session::forget('msg');
		$tutorial = [];
		if(!$messages->first('title')) 
		{
			$title = trim($request->input('title'));
			$alreadyExist = GStarBaseController::validateForExist('video_tutorials',$title,'title');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$tutorial['title'] = $title;		   
		}else{
			Session::flash('msg', $messages->first('title'));
			return;
		} 
		
		if(!$messages->first('category_id')) {
			$tutorial['category_id'] = trim($request->input('category_id'));
	    }else{
			Session::flash('msg', $messages->first('category_id'));
			return;
		}

		if(!$messages->first('status')) {
			$tutorial['status'] = trim($request->input('status'));
	     }else{
			Session::flash('msg', $messages->first('status'));
			return;
		  }

		  if($request->input('short_description')){
		  	$tutorial['short_description'] = trim($request->input('short_description'));	
		  }
		  

		  if($request->input('thumbnail')){
		  	$tutorial['thumbnail'] = trim($request->input('thumbnail'));
		  }

		  if($request->input('channel_name')){
		  	$tutorial['channel_name'] = trim($request->input('channel_name'));
		  }

		   if($request->input('duration')){
		  	$duration = trim($request->input('duration'));
		  	$duration =explode(":",$duration);
		  	
			if(count($duration)==3){
				$tutorial['duration'] =($duration[0]*60*60)+($duration[1]*60)+$duration[2] ;
			}else{
				if(count($duration)==2){
					$tutorial['duration'] =($duration[0]*60)+$duration[1] ;
				}else{
					$tutorial['duration'] =$duration[0] ;
				}
				
			}
		  }

		  if($request->input('youtube_url')){
			$tutorial['youtube_url'] = trim($request->input('youtube_url'));
	     }

	     // if(($request->hasFile('file') == FALSE) && ($request->input('youtube_url')=="")){
	     // 	Session::flash('msg', 'Video field or youtube url atleast one is required.');
	     // 	return;
	     // }

	     if(($request->input('subcat_id') == FALSE) && ($request->input('subcat_name') == "")){
	     	Session::flash('msg', 'Subcategory is required.');
	     	return;
	     }

	     if($request->input('subcat_id')){
	     	$tutorial['subcat_id']=$request->input('subcat_id');
	     }else if($request->input('subcat_name')){

	     	$alreadyExist=DB::table('tutorial_subcat')
	      						->where('name',$request->input('subcat_name'))
	      						->count();
	      	if($alreadyExist){
	      		Session::flash('msg', 'Subcategory already exist.');
	      		return;
	      	}else{
	      		$category_id=$request->input('category_id');
	      		if($category_id){
	      			$insertArr=array('name'=>$request->input('subcat_name'),'category_id'=>$category_id);
	      			$insert=DB::table('tutorial_subcat')->insertGetId($insertArr);
	      			if($insert){
	      				$tutorial['subcat_id']=$insert;
	      			}else{
	      				Session::flash('msg', 'Error in creating Subcategory.');
	      				return;
	      			}
	      		}else{
	      			Session::flash('msg', 'category is required.');
	      			return;
	      		}			
	      	}
	     }else{
	     	Session::flash('msg', 'Subcategory is required.');
	      	return;	
	     }

	 //    if($request->input('short_description')) {  	
		//  	$tutorial['short_description'] = trim($request->input('short_description'));	
		// }

		if($request->hasFile('file')){ 
		  	$tutorial['video_path'] = Tutorial::uploadFile($request,$tutorial['category_id'],$tutorial['subcat_id']); 
		  }
		 
		//print_r($tutorial); die;
		return $tutorial;
	}


	public static function editTutorial($request) { 
		

		$rules = array(
			'title'    => 'required', 
			'category_id' => 'required',
			'status'=> 'required'
			
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$tutorial = [];
		if(!$messages->first('title')) 
		{
			$title = trim($request->input('title'));
			$video_id = trim($request->input('video_id'));
			$alreadyExist = DB::table('video_tutorials')->select('title')->where('title','=',$title)->where('video_id','!=',$video_id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$tutorial['title'] = $title;		   
		}else {
			Session::flash('msg', $messages->first('title'));
		}

		if(!$messages->first('category_id')) {
			$tutorial['category_id'] = trim($request->input('category_id'));
	     }
		else {
			Session::flash('msg', $messages->first('category_id'));
		  } 


		if(!$messages->first('status')) {
			$tutorial['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		 // if($request->input('short_description')){
		 //  	$tutorial['short_description'] = trim($request->input('short_description'));	
		 //  }
		  

		  if($request->input('thumbnail')){
		  	$tutorial['thumbnail'] = trim($request->input('thumbnail'));
		  }

		  if($request->input('channel_name')){
		  	$tutorial['channel_name'] = trim($request->input('channel_name'));
		  }

		   if($request->input('duration')){
		  	$duration = trim($request->input('duration'));
		  	$duration =explode(":",$duration);
		  	
			if(count($duration)==3){
				$tutorial['duration'] =($duration[0]*60*60)+($duration[1]*60)+$duration[2] ;
			}else{
				if(count($duration)==2){
					$tutorial['duration'] =($duration[0]*60)+$duration[1] ;
				}else{
					$tutorial['duration'] =$duration[0] ;
				}
				
			}
		  }

		  if($request->input('youtube_url')){
			$tutorial['youtube_url'] = trim($request->input('youtube_url'));
	     }

	     if(($request->input('subcat_id') == FALSE) && ($request->input('subcat_name') == "")){
	     	Session::flash('msg', 'Subcategory is required.');
	     	return;
	     }

	     if($request->input('subcat_id')){
	     	$tutorial['subcat_id']=$request->input('subcat_id');
	     }else if($request->input('subcat_name')){

	     	$alreadyExist=DB::table('tutorial_subcat')
	      						->where('name',$request->input('subcat_name'))
	      						->count();
	      	if($alreadyExist){
	      		Session::flash('msg', 'Subcategory already exist.');
	      		return;
	      	}else{
	      		$category_id=$request->input('category_id');
	      		if($category_id){
	      			$insertArr=array('name'=>$request->input('subcat_name'),'category_id'=>$category_id);
	      			$insert=DB::table('tutorial_subcat')->insertGetId($insertArr);
	      			if($insert){
	      				$tutorial['subcat_id']=$insert;
	      			}else{
	      				Session::flash('msg', 'Error in creating Subcategory.');
	      				return;
	      			}
	      		}else{
	      			Session::flash('msg', 'category is required.');
	      			return;
	      		}			
	      	}
	     }else{
	     	Session::flash('msg', 'Subcategory is required.');
	      	return;	
	     }

	    if($request->input('short_description')) {  	
		 	$tutorial['short_description'] = trim($request->input('short_description'));	
		}

		if($request->hasFile('file')){ 
		  	$tutorial['video_path'] = Tutorial::uploadFile($request,$tutorial['category_id'],$tutorial['subcat_id']);
		  	$video_id = trim($request->input('video_id'));
		  	$arr=DB::table('video_tutorials')->select('video_path')->where('video_id','=',$video_id)->first();
				if($arr){
					if(file_exists($arr->video_path)){
						unlink($arr->video_path);	
					}
				} 
		  }
		 
		//print_r($tutorial); die;
		return $tutorial; 

		
		  
		
		return $tutorial;
	}

	public static function addTutorial($tutorial)
	{
		
		$save=DB::table('video_tutorials')->insert($tutorial);
		return $save;
		
		
	}

	public static function updateTutorial($tutorial,$video_id)
	{
		
		$save=DB::table('video_tutorials')->where('video_id','=',$video_id)->update($tutorial);
		return $save;
		
		
	}

	public static function deleteTutorial($video_id)
	{
		$arr=DB::table('video_tutorials')->select('video_path')->where('video_id','=',$video_id)->first();
		$save=DB::table('video_tutorials')->where('video_id','=',$video_id)->delete();
		GStarBaseController:: deleteLog('tutorial',$video_id);
		if($save){
			if(file_exists($arr->video_path)){
				unlink($arr->video_path);	
			}
		}
		return $save;
		

		
	}

	public static function VideoTutorialById($video_id){
		$finalArr=array();
		$i=0;
		$videoDetail=DB::table('video_tutorials')->where('video_id','=',$video_id)->first();
		//print_r($videoDetail); die;
		if($videoDetail){
			$finalArr[$i]['video_id'] = $videoDetail->video_id;
			$finalArr[$i]['title'] = $videoDetail->title;
			$finalArr[$i]['short_description'] = $videoDetail->short_description;
			$finalArr[$i]['video_path'] = $videoDetail->video_path;
			$finalArr[$i]['category_id'] = $videoDetail->category_id;
			$finalArr[$i]['subcat_id'] = $videoDetail->subcat_id;
			$finalArr[$i]['youtube_url'] = $videoDetail->youtube_url;
			$finalArr[$i]['thumbnail'] = $videoDetail->thumbnail;
			$finalArr[$i]['duration'] = Tutorial::DurationFormate($videoDetail->duration);
			$finalArr[$i]['status'] = $videoDetail->status;
			$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($videoDetail->created_at));
			if($videoDetail->updated_at){
			 $finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($videoDetail->updated_at));
			}else{
				$finalArr[$i]['updated_at'] = '';
			}
		}
		
		return $finalArr;
	}




	public static function getListOfTutorialSubcat($search_keyword,$pageNo)
	{
		
		$finalArr = array();
		$i = 0;
		if($pageNo) 
        {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		} else {
			 $offset= 0;
		}
		if($search_keyword!="")
		{
			 $getListOfAllTutorial = DB::table('tutorial_subcat')
			  ->where('name','like','%'.$search_keyword.'%')->orderBy('position', 'asc')
			  ->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
        }else{
		     $getListOfAllTutorial = DB::table('tutorial_subcat')->orderBy('position', 'asc')->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
        }
		foreach($getListOfAllTutorial as $eachrow)
		{
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['name'] = $eachrow->name;
				$finalArr[$i]['category_id'] = $eachrow->category_id;
				
				$finalArr[$i]['created_at'] = strtotime($eachrow->created_at)."000";
				//date('d-m-Y',strtotime($eachrow->created_at));
				if($eachrow->updated_at){
					$finalArr[$i]['updated_at'] = strtotime($eachrow->updated_at)."000";
					//date('d-m-Y',strtotime($eachrow->updated_at));
				}else{
					$finalArr[$i]['updated_at'] = '';
				}
				$category=DB::table('category')->select('category_name')->where('id',$eachrow->category_id)->first();
				$finalArr[$i]['category_name']=$category->category_name;
						
				$i++;				
		}					

  		return $finalArr;					
	}


	public static function createTutorialSubcat($request) { 

		$rules = array(
			'name'    => 'required', 
			'category_id' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$tutorialsubcat = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$alreadyExist = GStarBaseController::validateForExist('tutorial_subcat',$name,'name');
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$tutorialsubcat['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
			return;
		} 
		
		if(!$messages->first('category_id')) {
			$tutorialsubcat['category_id'] = trim($request->input('category_id'));
	    }else{
			Session::flash('msg', $messages->first('category_id'));
			return;
		}

		
		return $tutorialsubcat;
	}


	public static function addTutorialSubcat($tutorialsubcat) { 

		$save=DB::table('tutorial_subcat')->insert($tutorialsubcat);
		
		return $save;
	}

	public static function editTutorialSubcat($request,$id) { 

		$rules = array(
			'name'    => 'required', 
			'category_id' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$tutorialsubcat = [];
		if(!$messages->first('name')) 
		{
			$name = trim($request->input('name'));
			$alreadyExist = DB::table('tutorial_subcat')->select('name')->where('name','=',$name)->where('id','!=',$id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$tutorialsubcat['name'] = $name;		   
		}else{
			Session::flash('msg', $messages->first('name'));
			return;
		} 
		
		if(!$messages->first('category_id')) {
			$tutorialsubcat['category_id'] = trim($request->input('category_id'));
	    }else{
			Session::flash('msg', $messages->first('category_id'));
			return;
		}

		
		return $tutorialsubcat;
	}

	public static function updateTutorialSubcat($tutorialsubcat,$id) { 

		$save=DB::table('tutorial_subcat')->where('id',$id)->update($tutorialsubcat);
		
		return $save;
	}

	public static function GettutorialSubcatBySubcatId($id)
	 {
	 	$finalArr=array();
	 	$list = DB::table('tutorial_subcat')->where('id',$id)->first();
		if($list)
		{
			$finalArr['id'] = $list->id;
			$finalArr['name'] = $list->name;
			$finalArr['category_id'] = $list->category_id;
			$finalArr['created_at'] = date('d-m-Y',strtotime($list->created_at));
			if($list->updated_at){
				$finalArr['updated_at'] = date('d-m-Y',strtotime($list->updated_at));
			}else{
				$finalArr['updated_at'] = '';
			}
			$category=DB::table('category')->select('category_name')->where('id',$list->category_id)->first();
			$finalArr['category_name']=$category->category_name;							
		}					

  		return $finalArr;
	 } 


	 public static function deleteTutorialSubcat($id)
	{
		
		$save=DB::table('tutorial_subcat')->where('id','=',$id)->delete();
		
		return $save;
		

		
	}

	public static function GettutorialBySubcatId($id)
	{
		
		$save=DB::table('video_tutorials')->where('subcat_id','=',$id)->get();
		
		return $save;
		

		
	}


	 



	public static function uploadFile($request,$category_id,$product_id)
	{
		
		$category_id = $category_id;
		$product_id = $product_id;
		$filename='';
		//$fileInfoArr= array();
		
		$fileSize = $request->file('file')->getSize();
		$extensionofFile = $request->file('file')->getClientOriginalExtension();
		$type = array('mp4','MP4');
		$returnValue = in_array($extensionofFile,$type);
		//print_r($returnValue);
		if($returnValue)
		{
			if($fileSize <= GStarBaseController::VIDEO_FILE_SIZE)
			{ 
				$filename = time().".".$request->file('file')->getClientOriginalExtension(); 
				$filename_thumb = time().".".$request->file('file')->getClientOriginalExtension(); 
				
				if (!is_dir(base_path('uploads').'/'.'tutorial'.'/'.$category_id . '/' . $product_id)) {
					mkdir(base_path('uploads'). '/'.'tutorial'.'/'.$category_id . '/' . $product_id, 0777, true); 
				}
				
			   $targetPath = base_path('uploads').'/'.'tutorial'.'/'. $category_id . '/' . $product_id; 
				 $targetfilePath = $targetPath.'/'.$filename; 
				$isUploaded = $request->file('file')->move($targetPath,$filename);
				return 'uploads/tutorial/'. $category_id . '/' . $product_id.'/'.$filename; 
			}else {
				Session::flash('msg', GStarBaseController::MSG_VIDEO_SIZE);
			}
		}else{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
		
	}


	public static function changeOrderofTutorial($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('video_tutorials')->where('video_id', $each['id'])->update(array('position'=>$each['pos']));
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


	public static function changeOrderofTutorialSubcategory($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('tutorial_subcat')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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
