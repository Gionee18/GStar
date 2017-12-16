<?php

namespace App;
use Validator;
use Intervention\Image\Facades\Image as Image;
use File;
use Session,Auth,DB,Hash;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Asset extends Authenticatable
{
	  protected $table = 'asset_library';

	// function to get Asset list------------
	public static function AssetList($type,$pageNo,$search_keyword)
    {
			if($pageNo) 
			{
				$offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
			}
			else
			{
			   $offset= 0;
			}
			if($type == 'image')
			{
			  if($search_keyword!="")
			   {
				$listofAssets = DB::table('asset_library')->where(array('status'=>1,'type'=>'image'))->where('type','!=','')
				->where('title','like',$search_keyword.'%')->orderBy('id', 'desc')
				->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
		    	}
			 else
			   {
				$listofAssets = DB::table('asset_library')->where(array('status'=>1,'type'=>'image'))->where('type','!=','')
				->orderBy('id', 'desc')
				->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
			  }
			}
			if($type == 'documents')
			{
				if($search_keyword!="")
			{
				$listofAssets = DB::table('asset_library')->where('status',1)->where('type','!=','image')->where('type','!=','')->where('title','like',$search_keyword.'%')
				->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();
			}
			else
			{
				 $listofAssets = DB::table('asset_library')->where('status',1)->where('type','!=','image')->where('type','!=','')
				->skip($offset)->take(GStarBaseController::PAGINATION_LIMITS)->get();  
			}
		}
				
				return $listofAssets;
    }
	
	// function to get Image list of HomeBanner------------
	public static function listImagesofHomeBanner()
	{
		$getlistImagesofHomeBanner = DB::table('settings')->get();
        return $getlistImagesofHomeBanner;
    }

	// function to get Image list------------
    public static function listImages($request,$type)
	{
		$module = $request->input('module');
		$module_id = $request->input('module_id');
		if ($module=='product') {
				if($type == 'image'){
			$listImagesbyModule = DB::table('asset_library')
									    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
									    ->join($module , 'asset_mapping.module_id', '=', $module.'.id')
									    ->where(array('asset_library.status'=>'1','asset_library.type'=>'image'))
									    ->where('asset_mapping.module',$module)
									    ->where('asset_mapping.module_id',$module_id)
									    ->select('asset_mapping.id','asset_library.id as AssetId','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
									    ->get();
			 }
			 else{
				$listImagesbyModule = DB::table('asset_library')
									    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
									    ->join($module , 'asset_mapping.module_id', '=', $module.'.id')
									    ->where('asset_library.status',1)
									    ->where('asset_library.type','!=','image')
									    ->where('asset_mapping.module',$module)
									    ->where('asset_mapping.module_id',$module_id)
									    ->select('asset_mapping.id','asset_library.id as AssetId','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
									    ->get(); 
			 }
		}else{
			if($type == 'image'){
		$listImagesbyModule = DB::table('asset_library')
								    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								    ->join($module , 'asset_mapping.module_id', '=', $module.'.id')
								    ->where(array('asset_library.status'=>'1','asset_library.type'=>'image'))
								    ->where('asset_mapping.module',$module)
								    ->where('asset_mapping.module_id',$module_id)
								    ->select('asset_mapping.id','asset_library.id as AssetId','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
								    ->get();
		 }
		 else{
			$listImagesbyModule = DB::table('asset_library')
								    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								    ->join($module , 'asset_mapping.module_id', '=', $module.'.id')
								    ->where('asset_library.status',1)
								    ->where('asset_library.type','!=','image')
								    ->where('asset_mapping.module',$module)
								    ->where('asset_mapping.module_id',$module_id)
								    ->select('asset_mapping.id','asset_library.id as AssetId','asset_library.name','asset_library.path','asset_library.type','asset_library.title')
								    ->get(); 
		 }
		}
		

        
        return $listImagesbyModule;
    }
	// function to Delete Image------------
    public static function DeleteImages($request)
	{
		$id = $request->input('id');
		$msg=DB::table('asset_mapping')->where('id', $id)->delete();
								    
        return $msg;
    }

	// function to Upload File to HomeBanner------------
    public static function UploadFileToHomeBanner($request) { 
   $rules = array(
			'key'    => 'required',// file key is required
			'value'    => 'required',// file is required
			'type'    => 'required'// file type is required
		 
			
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$asset = new Asset();
		
		  if(!$messages->first('key')) {
			 $asset['key'] = trim($request->input('key'));
	     }
		else {
			Session::flash('msg', $messages->first('key'));
		  }
		  
		  if(!$messages->first('type')) {
			 $asset['type'] = trim($request->input('type'));
	     }
		else {
			Session::flash('msg', $messages->first('type'));
		  }
		if(Input::hasFile('value')){ 
			
			 $asset['key'] = 'home_banner';
			 $asset['value'] = $asset->uploadFile($request);
			$asset['type'] = $request->input('type'); 
			 $asset['status'] = 1; 
		}
		else {
			Session::flash('msg', $messages->first('key'));
		  }
		  
	return $asset;
	}
	
	
	// function to Upload File------------
  private function uploadFile($request)
 {
  
    $filename='';
    $fileInfoArr= array();
    $extensionofFile = $request->file('value')->getClientOriginalExtension();
    $returnValue = GStarBaseController::verifyfile($extensionofFile,'homebanner');
       if($returnValue)
     {
    $filename = time().".".$request->file('value')->getClientOriginalExtension(); 

    $filename_thumb = time().".".$request->file('value')->getClientOriginalExtension(); 

    $fileInfoArr['filename'] = $filename;
    $fileInfoArr['targetpath'] = 'uploads/homeBanner';
    
    if (!is_dir(base_path('uploads'). '/homeBanner')) {
     mkdir(base_path('uploads'). '/homeBanner', 0777, true); 
    }
    
      $targetPath = base_path('uploads'). '/homeBanner'; 
     $targetfilePath = $targetPath.'/'.$filename; 

      $isUploaded = $request->file('value')->move($targetPath,$filename);
	if($isUploaded){
		//thumbnail creation
		$filename1 = GStarBaseController::createThumbBanner($filename,$targetfilePath,$extensionofFile);
		$filename2 = GStarBaseController::createThumbMediumBanner($filename,$targetfilePath,$extensionofFile);

		return $fileInfoArr;
	
    }
   
  }
  else
  {
   Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
  }
  
  
 }


// function to get Image list by Category------------
 public static function listImagesbyCategory($id,$module)
	{
		$listImagesbyCategory = DB::table('asset_library')
								    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								    ->join('category' , 'asset_mapping.module_id', '=','category.id')
								    ->where(array('asset_library.status'=>1,'asset_mapping.module'=>$module,'asset_mapping.module_id'=>$id))
								   
								    ->select('asset_mapping.id','asset_library.id as ImageId','asset_library.name','asset_library.path','asset_library.type')
								    ->get();
        
        return $listImagesbyCategory;
    }

// function to Delete Image list by Category------------	
public static function listDeletingImagesbyCategory($id,$module)
	{
		$listImagesbyCategory = DB::table('asset_library')
								    ->join('asset_mapping', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								    ->join('category' , 'asset_mapping.module_id', '=','category.id')
								    ->where(array('asset_mapping.module'=>$module,'asset_mapping.module_id'=>$id))
								    ->select('asset_mapping.id','asset_library.id as ImageId','asset_library.name','asset_library.path','asset_library.type')
								    ->get();
        
        return $listImagesbyCategory;
    }



}
