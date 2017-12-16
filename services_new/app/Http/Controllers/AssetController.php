<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Asset;
use App\User;
use Validator;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use Auth, DB, Hash, Mail;
use App\Http\Controllers\GStarBaseController;

class AssetController extends GStarBaseController {
    
    // function for Asset List----
    public function index(Request $request) {
        $inputs          = Input::get();
        $responseContent = $this->validateUser(AssetController::G_LEARNER_ROLE_ID, false);
        if (!empty($responseContent)) {
            return $this->reponseBuilder($responseContent);
        }
        $search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
        $pageNo         = isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
        $result_array   = array();
        $ImageDetail    = Asset::AssetList('image', $pageNo, $search_keyword);
        $ImageDetail    = $this->userdatesFormat($ImageDetail);
        
        foreach ($ImageDetail as $eachrow) {
            if ($eachrow->updated_at == '01-01-1970') {
                $eachrow->updated_at = $eachrow->created_at;
            }
        }
        
        $result_array['count']  = count($ImageDetail);
        $result_array['status'] = 'success';
        $result_array['data']   = $ImageDetail;
        $responseContent        = $result_array;
        return $this->reponseBuilder($responseContent);
        
        
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        
        
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }
    
    /*
     *
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
    //
    }*/
    
    // function to Deattach asset from a module-----
    public function destroy(Request $request) {
        $result_array    = array();
        $responseContent = $this->validateUser(AssetController::G_SUPERVISOR_ROLE_ID, true);
        if (!empty($responseContent)) 
		{
            return $this->reponseBuilder($responseContent);
        }
        $mapping_id = $request->input('mapping_id');
        $module = $request->input('module');
        if($module=='product' && Auth::User()->role ==AssetController::G_TRAINER_ROLE_ID)
        {
            $deleteImage = DB::table('asset_mapping')->where('id', $mapping_id)->delete();
        }
        else{

            /// Log Maintain
            $GetModuleId = DB::table('asset_mapping')->where('id', $mapping_id)->first();
            if($module=='product'){
                if($GetModuleId){
                    $product = DB::table('product')
                                ->where('id', $GetModuleId->module_id)
                                ->where('status', '1')
                                ->first();
                    if($product){
                        DB::table('product')
                            ->where('id', $GetModuleId->module_id)
                            ->update(['updated_at' => date('Y-m-d H:i:s', strtotime("now"))]);
                        GStarBaseController::deleteLog('product Assets',$mapping_id);
                    }            
                }
                
            }
            if($module=='category'){

                if($GetModuleId){
                    $category = DB::table('category')
                                ->where('id', $GetModuleId->module_id)
                                ->where('status', '1')
                                ->first();
                    if($category){
                        DB::table('category')
                            ->where('id', $GetModuleId->module_id)
                            ->update(['updated_at' => date('Y-m-d H:i:s', strtotime("now"))]);
                        GStarBaseController::deleteLog('category Assets',$mapping_id);
                    }            
                }
                
            }
            /// End Log Maintain
            $deleteImage = DB::table('asset_mapping')->where('id', $mapping_id)->delete();   
        }
        
          if ($deleteImage) 
		  {
            $result_array['status'] = 'success';
            $result_array['msg']    = AssetController::MSG_RECORD_DELETED;
            $responseContent        = $result_array;
          } 
		  else 
		  {
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_BAD_PARAMETERS, CategoryController::MSG_BAD_PARAMETERS);
          }
        
        return $this->reponseBuilder($responseContent);
    }
    
    // function to Delete asset from a library-----
    public function libraryAssetDelete(Request $request) {
        $result_array    = array();
        $responseContent = $this->validateUser(AssetController::G_SUPERVISOR_ROLE_ID, true);
        if (!empty($responseContent)) 
		{
            return $this->reponseBuilder($responseContent);
        }
        $asset_id = $request->input('asset_id');
        $assetName   = DB::table('asset_library')/*->select('name', DB::raw('DATE(`created_at`) as created_at'))*/->where('id', $asset_id)->first();
        // $month       = date('M', strtotime($assetName->created_at));
        // $date        = date('d-m-y', strtotime($assetName->created_at));
        $deleteAsset = DB::table('asset_library')->where('id', $asset_id)->delete();
        if ($deleteAsset) {
            $path       = base_path('uploads') . '/' . $assetName->path . '/' . $assetName->name;

            if(file_exists($path)){
                unlink($path);   
            }
            // $dirHandle = opendir($dir);
            // while ($file = readdir($dirHandle)) {
            //     if ($file == $assetName->name) {
            //         unlink($dir . '/' . $file);
            //     }
            // }
            $result_array['status'] = 'success';
            $result_array['msg']    = AssetController::MSG_RECORD_DELETED;
            $responseContent        = $result_array;
            
        } 
		else
		{
           $responseContent = $this->errorResponseBuilder(AssetController::ERROR_BAD_PARAMETERS, CategoryController::MSG_BAD_PARAMETERS);
        }
        
        return $this->reponseBuilder($responseContent);
    }
    
    // function to List Home Banner Images-----
    public function listImagesofHomeBannerImages() {
        $result_array = array();
        $ImageDetail  = Asset::listImagesofHomeBanner();
        
        $result_array['count']  = count($ImageDetail);
        $result_array['status'] = 'success';
        $result_array['data']   = $ImageDetail;
        $responseContent        = $result_array;
        
        return $this->reponseBuilder($responseContent);
        
    }
    
    
    
    // function to display Images by Module
    public function DisplayImages(Request $request) {
        $result_array    = array();
        $responseContent = $this->validateUser(AssetController::G_LEARNER_ROLE_ID, true);
        if (!empty($responseContent)) {
            return $this->reponseBuilder($responseContent);
        }
        
        if (($request->input('module_id') != '') && ($request->input('module') != '')) {
            $ImageDetail = Asset::listImages($request, 'image');
            if ($ImageDetail != NULL) {
                
                $result_array['count']  = count($ImageDetail);
                $result_array['status'] = 'success';
                $result_array['data']   = $ImageDetail;
                $responseContent        = $result_array;
            } else {
                
                $result_array['status'] = 'success';
                $result_array['msg']    = AssetController::MSG_NO_RECORD;
                $responseContent        = $result_array;
            }
            
        } else {
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_ARGUMNETS_MISSING, AssetController::MSG_ARGUMNETS_MISSING);
        }
        return $this->reponseBuilder($responseContent);
    }
    
    // function to display Documents by Module
    public function DisplayDocuments(Request $request) {
        $result_array    = array();
        $responseContent = $this->validateUser(AssetController::G_LEARNER_ROLE_ID, true);
        if (!empty($responseContent)) {
            return $this->reponseBuilder($responseContent);
        }
        
        if (($request->input('module_id') != '') && ($request->input('module') != '')) {
            $DocumentDetail = Asset::listImages($request, 'document');
            if ($DocumentDetail != NULL) {
                
                $result_array['count']  = count($DocumentDetail);
                $result_array['status'] = 'success';
                $result_array['data']   = $DocumentDetail;
                $responseContent        = $result_array;
            } else {
                
                $result_array['status'] = 'success';
                $result_array['msg']    = AssetController::MSG_NO_RECORD;
                $responseContent        = $result_array;
            }
        } else {
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_ARGUMNETS_MISSING, AssetController::MSG_ARGUMNETS_MISSING);
        }
        return $this->reponseBuilder($responseContent);
        
    }
    
    // function to list of documents
    
    public function documents(Request $request) {
        $inputs         = Input::get();
        $result_array   = array();
        $search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
        $pageNo         = isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
        
        $ImageDetail = Asset::AssetList('documents', $pageNo, $search_keyword);
        $ImageDetail = $this->userdatesFormat($ImageDetail);
        
        foreach ($ImageDetail as $eachrow) {
            if ($eachrow->updated_at == '01-01-1970') {
                $eachrow->updated_at = $eachrow->created_at;
            }
        }
        
        
        $result_array['count']  = count($ImageDetail);
        $result_array['status'] = 'success';
        $result_array['data']   = $ImageDetail;
        $responseContent        = $result_array;
        return $this->reponseBuilder($responseContent);
        
        
    }
    
    //function upload file for HomeBanner
    public function UploadFileToHomeBanner(Request $request) {
        
        $result_array = array();
        
        $uploadFile = Asset::UploadFileToHomeBanner($request);
        
        if (!$uploadFile) {
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_FILE_NOT_UPLOADED, AssetController::MSG_FILE_NOT_UPLOADED);
            return $this->reponseBuilder($responseContent);
        }
        
        if (!Session::has('msg')) {
            
            $saveFile = DB::table('settings')->insert([
            ['key' =>$uploadFile['key'],'value' =>$uploadFile['value']['filename'],
			'path' =>$uploadFile['value']['targetpath'], 'type' => $uploadFile['type'],'status' => '1']]);
            
            if ($saveFile) {
                $result_array['status'] = 'success';
                $result_array['msg'] = AssetController::MSG_UPLOADED_FILE;
                $responseContent     = $result_array;
                } else {
                $responseContent = $this->errorResponseBuilder(AssetController::ERROR_ARGUMNETS_MISSING, AssetController::MSG_ARGUMNETS_MISSING);
            }
            
        } else {
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_BAD_PARAMETERS, Session::get('msg'));
        }
        
        
        return $this->reponseBuilder($responseContent);
    }
    
    // function to delete HomeBanner Image
	
    public function deleteHomeBannerImage(Request $request) {
        $responseContent = $this->validateUser(AssetController::G_TRAINER_ROLE_ID, true);
        if (!empty($responseContent)) {
            return $this->reponseBuilder($responseContent);
        }
        $result_array = array();
		
        $ImageID      = $request->input('id');
		$ImageName   = DB::table('settings')->select('value')->where('id', $ImageID)->first();
		$disableImage = DB::table('settings')->where('id', $ImageID)->delete();
        GStarBaseController:: deleteLog('Banner',$ImageID);
        if ($disableImage) 
		{
			$dir       = base_path('uploads') . '/homeBanner';
            $dirHandle = opendir($dir);
            while ($file = readdir($dirHandle)) 
			{
				if ($file == $ImageName->value) 
				{
                    unlink($dir . '/' . $file);
                }
            }
				$result_array['status'] = 'success';
				$result_array['msg']    = AssetController::MSG_RECORD_DELETED;
				$responseContent        = $result_array;
        } 
		else 
		{
            $responseContent = $this->errorResponseBuilder(AssetController::ERROR_BAD_PARAMETERS, AssetController::MSG_BAD_PARAMETERS);
        }
        
        return $this->reponseBuilder($responseContent);
    }
    
    // function to List Home Banner Images-----
    public function listImagesofHomeBanner() {
        $result_array    = array();
        $responseContent = $this->validateUser(AssetController::G_TRAINER_ROLE_ID, false);
        if (!empty($responseContent)) {
            return $this->reponseBuilder($responseContent);
        }
        $ImageDetail = Asset::listImagesofHomeBanner();
        
        $result_array['count']  = count($ImageDetail);
        $result_array['status'] = 'success';
        $result_array['data']   = $ImageDetail;
        $responseContent        = $result_array;
        
        return $this->reponseBuilder($responseContent);
        
    }
	
	// function for delet All Assets from Library------ 
  public function deleteAllAssets(Request $request)
    {
		$result_array    = array();
		$assetIDArr    = array();
        $responseContent = $this->validateUser(AssetController::G_TRAINER_ROLE_ID, true);
        if (!empty($responseContent)) 
		{
            return $this->reponseBuilder($responseContent);
        }
        $asset_ids = $request->input('asset_id');
        $assetIDArr = explode(',',$asset_ids);
		//print_r($assetIDArr);die;
		$delete = 0;
		foreach ($assetIDArr as $asset_id){
		//echo $assetid; die;
        $assetName   = DB::table('asset_library')->where('id', $asset_id)->first();
        //print_r($assetName); die;
        // $month       = date('M', strtotime($assetName->created_at));
        // $date        = date('d-m-y', strtotime($assetName->created_at));
        $deleteAsset = DB::table('asset_library')->where('id', $asset_id)->delete();
        if ($deleteAsset) {
            $path       = base_path('uploads') . '/' . $assetName->path . '/' . $assetName->name;

            if(file_exists($path)){
                unlink($path);   
            }
            // $dirHandle = opendir($dir);
            // while ($file = readdir($dirHandle)) {
            //     if ($file == $assetName->name) {
            //         unlink($dir . '/' . $file);
            //     }
            // }
			$delete = 1;
          }
		}
		 if($delete){
            $result_array['status'] = 'success';
            $result_array['msg']    = AssetController::MSG_RECORD_DELETED;
            $responseContent        = $result_array;
		 }
            		
		else
		{
           $responseContent = $this->errorResponseBuilder(AssetController::ERROR_BAD_PARAMETERS, CategoryController::MSG_BAD_PARAMETERS);
        }
        
        return $this->reponseBuilder($responseContent);
	}

    
}
