<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Recommender;
use App\User;
use App\Asset;
use Validator;
use Illuminate\Support\Facades\Input;
use Redirect,Session;
use Auth,DB,Hash;
use App\Http\Controllers\GStarBaseController;
use LucaDegasperi\OAuth2Server\Authorizer;


class RecommenderController extends GStarBaseController
{

	public function listManufacturer(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$ManufacturerDetails = Recommender::getListOfManufacturer($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($ManufacturerDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $ManufacturerDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }

	
	   public function ManufacturerById($id = null)
    {
		$result_array = array();
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$RecommenderDetails = Recommender::ManufacturerById($id);
			$result_array['count'] = 1;//count($RecommenderDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $RecommenderDetails;
			$responseContent = $result_array;
		}	
			return $this->reponseBuilder($responseContent);
			
    }

	 
    public function createManufacturer(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);	
		if(empty($responseContent))
		{
			$Manufacturer = Recommender::createManufacturer($request);
				if(!Session::has('msg'))
				{	
					if($request->hasFile('file')){ 
					 $image = Recommender::uploadFile($request); 
						
						if(!Session::has('msg'))
						{ 
							$saveId = Recommender::addManufacture($Manufacturer);
							if($saveId){
								$module='manufacturer';
								$uploadImage = Recommender::SaveuploadFile($saveId,$module,$image);
								$result_array = array();
								$result_array['status'] = 'success';
								$result_array['msg'] = RecommenderController::MSG_ADDED_MANUFACTURER;
								$responseContent = $result_array;
							}else{
								$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
							}
						}else{
						  $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
						}
					}else {
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,'The image file is required.'); 
					}	
				}else{
				     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
			}

			return $this->reponseBuilder($responseContent);
    }



    public function updateManufacturer(Request $request)
    {
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$manufacturer_id=trim($request->input('id'));
				$uploadImage=null;
				if($manufacturer_id){
					$manufacturer = Recommender::editManufacturer($request);
					if(!Session::has('msg'))
					{	
						if($request->hasFile('file')){ 
					 		$image = Recommender::uploadFile($request); 
					 		if(Session::has('msg'))
							{
								$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
								return $this->reponseBuilder($responseContent);
							}else{
								$module='manufacturer';
								$uploadImage = Recommender::UpdateploadFile($manufacturer_id,$module,$image);
							}
					 	}
						$save = Recommender::updateManufacturer($manufacturer,$manufacturer_id); 
						if($save || $uploadImage){
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = RecommenderController::MSG_RECORD_UPDATED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
						}	
					}else{
					     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
					}
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
				
			}

			return $this->reponseBuilder($responseContent);
    }

    

	public function deleteManufacturer(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$id = $request->input('id');
				if($id)
				{	
					$manufacturer=Recommender::ModelByManufacturerId($id);
					//print_r($manufacturer); die;
					if($manufacturer){
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_MODEL_EXIST);
					}else{
						$save = Recommender::deleteManufacturer($id); 
						if($save){
							GStarBaseController:: deleteLog('manufacturer',$id);
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = RecommenderController::MSG_RECORD_DELETED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
						}	
					}		
				}else{
				     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }



   public function listModel(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$ModelDetails = Recommender::getListOfModel($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($ModelDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $ModelDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }



	  public function ModelById($id = null)
    {
		$result_array = array();
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$ModelDetails = Recommender::ModelById($id);
			$result_array['count'] = 1;//count($RecommenderDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $ModelDetails;
			$responseContent = $result_array;
		}	
			return $this->reponseBuilder($responseContent);
			
    }



    public function createModel(Request $request)
    {
		$brand=GStarBaseController::OTHER_BRAND;
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);	
		if(empty($responseContent))
		{
			$Model = Recommender::createModel($request);
				if(!Session::has('msg'))
				{	
					if($request->hasFile('file')){ 
					 $image = Recommender::uploadFile($request); 
						
						if(!Session::has('msg'))
						{ 
							$saveId = Recommender::addModel($Model);
							if($saveId){
								$module='model';
								$uploadImage = Recommender::SaveuploadFile($saveId,$module,$image);

								/* Add Hash tags values */
								$HashtagArray = GStarBaseController::get_hashtags($Model['description'], $str = 0);
								if(!empty($HashtagArray)){
									$saveHashtag = GStarBaseController::saveHashtag($saveId,$HashtagArray,$brand);
								}
								/* Add Hash tags values End */

								$result_array = array();
								$result_array['status'] = 'success';
								
								$result_array['data']['mf_id'] = $Model['mf_id'];
								$result_array['data']['mf_name'] = Recommender::manufacturer_name($Model['mf_id']);
								$result_array['data']['model_id'] = $saveId;
								$result_array['data']['model_name'] = Recommender::model_name($saveId);

								$result_array['msg'] = RecommenderController::MSG_ADDED_MODEL;
								$responseContent = $result_array;
							}else{
								$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
							}
						}else{
						  $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
						}
					}else {
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,'The image file is required.'); 
					}	
				}else{
				     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
			}

			return $this->reponseBuilder($responseContent);
    }





    public function updateModel(Request $request)
    {
			$brand=GStarBaseController::OTHER_BRAND;
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$model_id=trim($request->input('id'));
				$uploadImage=null;
				if($model_id){
					$model = Recommender::editModel($request);
					if(!Session::has('msg'))
					{	
						if($request->hasFile('file')){ 
					 		$image = Recommender::uploadFile($request); 
					 		if(Session::has('msg'))
							{
								$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
								return $this->reponseBuilder($responseContent);
							}else{
								$module='model';
								$uploadImage = Recommender::UpdateploadFile($model_id,$module,$image);
							}
					 	}
						$save = Recommender::updateModel($model,$model_id); 
						if($save || $uploadImage){

							/* Add Hash tags values */
								$HashtagArray = GStarBaseController::get_hashtags($model['description'], $str = 0);
								if(!empty($HashtagArray)){
									$saveHashtag = Recommender::updateHashtag($model_id,$HashtagArray,$brand);
								}
							/* Add Hash tags values End */

							$result_array = array();
							$result_array['status'] = 'success';


							$result_array['data']['mf_id'] = $model['mf_id'];
							$result_array['data']['mf_name'] = Recommender::manufacturer_name($model['mf_id']);
							$result_array['data']['model_id'] = $model_id;
							$result_array['data']['model_name'] = Recommender::model_name($model_id);

							$result_array['msg'] = RecommenderController::MSG_RECORD_UPDATED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
						}	
					}else{
					     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
					}
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
				
			}

			return $this->reponseBuilder($responseContent);
    }



    public function deleteModel(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$id = $request->input('id');
				if($id)
				{	
					$save = Recommender::deleteModel($id); 
						if($save){

							GStarBaseController:: deleteLog('model',$id);
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = RecommenderController::MSG_RECORD_DELETED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
						}	
				}else{
				     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }



   public function listSpecification()
    {
		// Request $request
		// $inputs = Input::get();
		// $search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		// $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			//$ModelDetails = Recommender::getlistSpecification($search_keyword,$pageNo);
			$SpecificationDetails = Recommender::getlistSpecification();

			$result_array = array();

			$result_array['count'] = count($SpecificationDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecificationDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }


	  public function listSpecificationCategory(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SpecificationDetails = Recommender::listSpecificationCategory($search_keyword,$pageNo);
			$result_array = array();
			$result_array['count'] = count($SpecificationDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecificationDetails;
			$result_array['undelete_id'] = Recommender::UndeleteId('spec_category');
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }


	 public function listSpecificationSubcategory(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SpecificationDetails = Recommender::listSpecificationSubcategory($search_keyword,$pageNo);
			$result_array = array();
			$result_array['count'] = count($SpecificationDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecificationDetails;
			$result_array['undelete_id'] = Recommender::UndeleteId('spec_subcategory');
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }



	 public function addModelSpecification(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
			if(empty($responseContent))
			{
				$model_id = $request->input('model_id');
				$brand = $request->input('brand');
				$data = $request->input('data');
				$view_status = $request->input('view_status');
				if($model_id && !empty($data) && ($view_status== 0 || $view_status==1) && ($brand== GStarBaseController::OTHER_BRAND || $brand==GStarBaseController::GIONEE_BRAND))
				{	
					$save = Recommender::addModelSpecification($model_id,$data,$view_status,$brand); 
						if($save){
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = RecommenderController::MSG_ADDED_MODEL_SPECIFICATION;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
						}	
				}else{
				     $responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }

 


	 public function updateModelSpecification(Request $request,$id)
    {
			
			$result_array = array();		
			$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
			if(empty($responseContent))
			{
				if($request->isMethod('post')){
					$brand = $request->input('brand');
					$data = $request->input('data');
					$view_status = $request->input('view_status');
					if(!empty($data)&& ($view_status== 0 || $view_status==1) && ($brand== GStarBaseController::OTHER_BRAND || $brand==GStarBaseController::GIONEE_BRAND))
					{	
						$save = Recommender::updateModelSpecification($id,$data,$view_status,$brand); 
						if($save){
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = RecommenderController::MSG_UPDATE_MODEL_SPECIFICATION;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
						}	
					}else{
				     	$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				    } 	//End of IF ELSE empty data
				}//End of IF $request->isMethod('post')
				else{
					
					if($id)
					{	
						$brand = $request->input('brand');
						if(($brand== GStarBaseController::OTHER_BRAND || $brand==GStarBaseController::GIONEE_BRAND)){

							$specdata = Recommender::getSpecificationBymodel($id,$brand); 
							if($specdata){
								$result_array = array();
								$result_array['count'] = 1;
								$result_array['status'] = 'success';
								$result_array['data'] = $specdata;
								$result_array['p_name'] =  Recommender::MF_ProductName($id,$brand);
								$result_array['publish_status'] = Recommender::getPublishStatus($id,$brand);
								$responseContent = $result_array;
							}else{
								$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_NO_RECORD);
							}	
						}else{
							$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
						}
						
						
					}else{
				     	$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
				}
			}
		}			
		return $this->reponseBuilder($responseContent);	
   }


    public function SpecificationCategoryById($id = null)
    {
		$result_array = array();
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SpecCategoryDetails = Recommender::SpecificationCategoryById($id);
			$result_array['count'] = 1;//count($RecommenderDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecCategoryDetails;
			$responseContent = $result_array;
		}	
			return $this->reponseBuilder($responseContent);
			
    }


    public function createSpecificationCategory(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $category = Recommender::AddSpecCategory($request);
			if(!Session::has('msg'))
			{	
				$save = Recommender::InsertSpecCategory($category); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = RecommenderController::MSG_RECORD_ADDED;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }


    public function updateSpecificationCategory(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$category = Recommender::editSpecCategory($request);
				if(!Session::has('msg'))
				{	
					$save = Recommender::updateSpecCategory($category,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = RecommenderController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }


    public function deleteSpecificationCategory(Request $request)
    {
		$result_array = array();		
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			$id = $request->input('id');
			if($id)
			{	
				$newstopic_count=Recommender::getSpecSubcatBycatId($id);
				//print_r($newstopic_count);
				if($newstopic_count){
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_SPEC_SUBCAT_EXIST);
				}else{
					$save = Recommender::deleteSpecCat($id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = RecommenderController::MSG_RECORD_DELETED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
					}	
				}			
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
			}
		}
		return $this->reponseBuilder($responseContent);	
   }



    public function SpecificationSubCategoryById($id = null)
    {
		$result_array = array();
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SpecCategoryDetails = Recommender::SpecificationSubCategoryById($id);
			$result_array['count'] = 1;//count($RecommenderDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecCategoryDetails;
			$responseContent = $result_array;
		}	
			return $this->reponseBuilder($responseContent);
			
    }


    public function createSpecificationSubCategory(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $subcategory = Recommender::AddSpecSUBCategory($request);
			if(!Session::has('msg'))
			{	
				$save = Recommender::InsertSpecSUBCategory($subcategory); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = RecommenderController::MSG_RECORD_ADDED;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }



     public function updateSpecificationSubCategory(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$subcategory = Recommender::editSpecSUBCategory($request);
				if(!Session::has('msg'))
				{	
					$save = Recommender::updateSpecSUBCategory($subcategory,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = RecommenderController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }


    public function deleteSpecificationSubCategory(Request $request)
    {
		$result_array = array();		
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			$id = $request->input('id');
			if($id)
			{	
				$find=Recommender::FindSearchCategorySubcategory($id,'spec_subcategory');
				
				if($find== 1){
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_SEARCH_ATTRIBUTE);
				}else{
					$save = Recommender::deleteSpecSUBCat($id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = RecommenderController::MSG_RECORD_DELETED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
					}
				}
								
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
			}
		}
		return $this->reponseBuilder($responseContent);	
   }


	public function AttributeSubCategoryById($id = null)
    {
		$result_array = array();
		$responseContent = $this->validateUser(RecommenderController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SpecCategoryDetails = Recommender::AttributeSubCategoryById($id);
			$result_array['count'] = 1;//count($RecommenderDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SpecCategoryDetails;
			$responseContent = $result_array;
		}	
			return $this->reponseBuilder($responseContent);
			
    }

	public function addAttributeSpec(Request $request)
    {
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $attribute = Recommender::addAttributeSpec($request);
			if(!Session::has('msg'))
			{	
				$save = Recommender::InsertAttributeSpec($attribute); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = RecommenderController::MSG_RECORD_ADDED;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }



    public function editAttributeSpec(Request $request)
    {
		
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$attribute = Recommender::editAttributeSpec($request);
				if(!Session::has('msg'))
				{	
					
					$save = Recommender::UpdateAttributeSpec($attribute,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = RecommenderController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_ARGUMNETS_MISSING,RecommenderController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }


    public function deleteAttributeSpec(Request $request)
    {
		$result_array = array();		
		$responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			$id = $request->input('id');
			if($id)
			{	
				$save = Recommender::deleteAttributeSpec($id); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = RecommenderController::MSG_RECORD_DELETED;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_NO_UPDATE,RecommenderController::MSG_NO_RECORD);
				}				
			}else{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,RecommenderController::MSG_ARGUMNETS_MISSING);
			}
		}
		return $this->reponseBuilder($responseContent);	
   }


   
    public function AppMfModelList(Request $request)
    {
		    $result_array = array();
			$MFDetails = Recommender::AppMfModelList($request);
			$result_array['count'] = count($MFDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $MFDetails;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
			
    }


    public function AppSearchAttributeList(Request $request)
    {
		    $result_array = array();
			$SearchAttList = Recommender::AppSearchAttributeList($request);
			//$SearchAttList = Recommender::SearchKeyList($SearchAttList);
			$result_array['count'] = count($SearchAttList);
			$result_array['status'] = 'success';
			$result_array['data'] = $SearchAttList;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
			
    }


    public function AppPhoneCompair(Request $request)
    {
		    $result_array = array();
			$ProductDetails = Recommender::AppPhoneCompair($request);
			if(Session::has('msg'))
			{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				return $this->reponseBuilder($responseContent);
			}
			if(!empty($ProductDetails)){
				$result_array['count'] = count($ProductDetails);
				$result_array['status'] = 'success';
				$result_array['data'] = $ProductDetails;
				$responseContent = $result_array;
			}else{

				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,'Specification not found!');
			}
			return $this->reponseBuilder($responseContent);
    }

    public function SearchRecommendor(Request $request)
    {
		$result_array = array();	
		$modelIdArr=array();
		$INArr=array();
		$brand_id=trim($request->input('brand_id'));
		$model_ids=trim($request->input('model_ids'));
		$cpu=trim($request->input('cpu'));
		$intenal_memory = trim($request->input('intenal_memory'));
		$ram = trim($request->input('ram'));
		$camera = trim($request->input('camera'));
		$battery = trim($request->input('battery'));
		$price = trim($request->input('price'));
		$cpu_id=trim($request->input('cpu_id'));
		$intenal_memory_id = trim($request->input('intenal_memory_id'));
		$ram_id = trim($request->input('ram_id'));
		$camera_id = trim($request->input('camera_id'));
		$battery_id = trim($request->input('battery_id'));
		$price_id = trim($request->input('price_id'));

		/******************* Search By Manufacture ********************/
		if($brand_id || $model_ids)
		{
			if($model_ids)
			{	
				
				$sql2=Recommender::SearchByMF($model_ids);
				if(Session::has('msg'))
				{
					$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
					return $this->reponseBuilder($responseContent);
				}else { // if not Session Has Message

					// print_r($sql2);
					// echo '------------------------------------------';
					// die;
					$res =  DB::select( DB::raw($sql2));
					// print_r($res);
					// die;
					if (!empty($res)) 
					{
						if(($cpu_id && $cpu) || ($ram_id && $ram) || ($intenal_memory_id && $intenal_memory) || ($camera_id && $camera) || ($battery_id && $battery)|| ($price_id && $price) )
						{	
							foreach ($res as $key ) {
								$result[]=$key->model_id;
							}

							$sql3=self::SEARCH_BY_ATTRIBUTE($request);
							
							//print_r($sql3);die;
							//echo '-------------------------------------';
							
							$sql4="SELECT DISTINCT a.model_id FROM ( ".$sql3." ) a   WHERE ";
							$sql4=$sql4."  a.model_id IN (".implode(",",$result).")";

							//echo $sql4; die;
							//echo '-------------------------------------';
							$res2 =  DB::select( DB::raw($sql4));
							if(!empty($res2)){
								$MArrayCount=0;
								foreach ($res2 as $key ) {
									$modelIdArr[$MArrayCount]['model_id']=$key->model_id;
									$MArrayCount++;
								}	
							}


						} else {  //// End of search Attribute in Manufacture

								$MArrayCount=0;
								foreach ($res as $key ) {
									$modelIdArr[$MArrayCount]['model_id']=$key->model_id;
									$MArrayCount++;
								}	
						}
							
					}

				}  //END of session not null else
			} //model_id select
			else
			{

				$brand_id=trim($request->input('brand_id'));

				if(($cpu_id && $cpu) || ($ram_id && $ram) || ($intenal_memory_id && $intenal_memory) || ($camera_id && $camera) || ($battery_id && $battery)|| ($price_id && $price) )
				{
						$sql=self::SEARCH_BY_ATTRIBUTE($request);
						if($sql !=null){
							//print_r($sql);
							$res =  DB::select( DB::raw($sql));
							$MArrayCount=0;
							foreach ($res as $key ) {
								$modelIdArr[$MArrayCount]['model_id']=$key->model_id;
								$MArrayCount++;
							}
						}
				}else{
						$sql5=self::SEARCH_BY_BRAND($request);
						if($sql5 !=null){
							
							$res =  DB::select( DB::raw($sql5));
							$MArrayCount=0;
							foreach ($res as $key ) {
								$modelIdArr[$MArrayCount]['model_id']=$key->model_id;
								$MArrayCount++;
							}
						}
				}

				

			} //model id not select only brand is selcted
			

		}  // End of Search By Manufacture

		
		/********************* Start Search By Attribute ************************/
		else
		{

			$sql=self::SEARCH_BY_ATTRIBUTE($request);
				if($sql !=null){
					// print_r($sql);
					// die;
					$res =  DB::select( DB::raw($sql));
					$MArrayCount=0;
					foreach ($res as $key ) {
						$modelIdArr[$MArrayCount]['model_id']=$key->model_id;
						$MArrayCount++;
					}
				}

		} //// End of Search By Attribute
		

		$count=3;
		if(count($modelIdArr)<=$count){
			$count=count($modelIdArr);
		}
		$Models=Recommender::ModelDetails($modelIdArr,$count);
		$result_array['count'] = count($Models);
		$result_array['status'] = 'success';
		$result_array['data'] = $Models;
		$responseContent = $result_array;

		// if($brand_id || $model_ids){ // return top 3 gionee product SearchByManufacture
		// 	$count=3;
		// 	if(count($modelIdArr)<=$count){
		// 		$count=count($modelIdArr);
		// 	}
			
		// 	$Models=Recommender::ModelDetails($modelIdArr,$count);
		// 	$result_array['count'] = count($Models);
		// 	$result_array['status'] = 'success';
		// 	$result_array['data'] = $Models;
		// 	$responseContent = $result_array;

		// } else {
		// 	$count=count($modelIdArr);
		// 	$Models=Recommender::ModelDetails($modelIdArr,$count);
		// 	$result_array['count'] = count($Models);
		// 	$result_array['status'] = 'success';
		// 	$result_array['data'] = $Models;
		// 	$responseContent = $result_array;
		// }
		
			
		return $this->reponseBuilder($responseContent);
		
			
    }




    public static function SEARCH_BY_ATTRIBUTE($request)
    {

    	$cpu=trim($request->input('cpu'));
		$intenal_memory = trim($request->input('intenal_memory'));
		$ram = trim($request->input('ram'));
		$camera = trim($request->input('camera'));
		$battery = trim($request->input('battery'));
		$price = trim($request->input('price'));
		$cpu_id=trim($request->input('cpu_id'));
		$intenal_memory_id = trim($request->input('intenal_memory_id'));
		$ram_id = trim($request->input('ram_id'));
		$camera_id = trim($request->input('camera_id'));
		$battery_id = trim($request->input('battery_id'));
		$price_id = trim($request->input('price_id'));

	 

    	$sql="SELECT DISTINCT PAX.model_id FROM product_attribute_xref PAX,product_spec_display PSD  WHERE ";

		if($price_id && $price){
			$select='Price';
			$Price_subcatID=DB::table('spec_subcategory')
							->where('id',$price_id)
							->where(DB::raw("LOWER(name)"), '=', strtolower($select))
							->first();
			if($Price_subcatID){
				/* Expload search Attribute*/
				$priceSearch=explode(',', $price);
				for ($i=0; $i < count($priceSearch); $i++) { 
					$Att=explode('-', $priceSearch[$i]);
					$priceSearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/
					
				$sql=$sql." PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$price_id." and x.view_status='1' and p.brand = 'gionee' and (";
					
				for ($i=0; $i <count($priceSearchAtt) ; $i++) { 
					$min=min($priceSearchAtt[$i]);
					$max=(max($priceSearchAtt[$i]))+(int)$Price_subcatID->threshold;
						
					if((count($priceSearchAtt)-1) ==$i){
						$sql=$sql."  (p.text_value <=".$max." AND p.text_value >= ".$min.")))";	
					}else{
						$sql=$sql."  (p.text_value <=".$max." AND p.text_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$price_id;		
			}
		}

		if($ram && $ram_id)
		{
			$select='RAM';
			$Ram_subcatID=DB::table('spec_subcategory')
							->where('id',$ram_id)
							->where(DB::raw("LOWER(name)"), '=', strtolower($select))
							->first();
			if($Ram_subcatID){
				/* Expload search Attribute*/
				$RamSearch=explode(',', $ram);
				for ($i=0; $i < count($RamSearch); $i++) { 
					$Att=explode('-', $RamSearch[$i]);
					$RamSearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/	

				if($price_id){
					$sql=$sql." and PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$ram_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}else{
					$sql=$sql."PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p,product_spec_display x where p.spec_subcatid =".$ram_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}	
				for ($i=0; $i <count($RamSearchAtt) ; $i++) { 
					if(count($RamSearchAtt[$i])==1){
						$min=min($RamSearchAtt[$i]);
						$max=$min+(int)$Ram_subcatID->threshold+GStarBaseController::RAM_CONSTANT;	
					}else{
						$min=min($RamSearchAtt[$i]);
						$max=(max($RamSearchAtt[$i]))+(int)$Ram_subcatID->threshold;
					}
					if((count($RamSearchAtt)-1) ==$i){
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.")))";	
					}else{
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$ram_id;		
			}
		}

		if($camera_id && $camera_id)
		{
			$select='Primary';
			$Camera_subcatID=DB::table('spec_subcategory')
							->where('id',$camera_id)
							->where(DB::raw("LOWER(name)"), '=', strtolower($select))
							->first();
			if($Camera_subcatID){
				/* Expload search Attribute*/
				$CameraSearch=explode(',', $camera);
				for ($i=0; $i < count($CameraSearch); $i++) { 
					$Att=explode('-', $CameraSearch[$i]);
					$CameraSearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/	
				if($price_id || $ram_id){
					$sql=$sql." and PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$camera_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}else{
					$sql=$sql."PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p,product_spec_display x where p.spec_subcatid =".$camera_id." and x.view_status='1' and p.brand = 'gionee' and (";

				}
					
				for ($i=0; $i <count($CameraSearchAtt) ; $i++) { 
					if(count($CameraSearchAtt[$i])==1){
						$min=min($CameraSearchAtt[$i]);
						$max=$min+(int)$Camera_subcatID->threshold+GStarBaseController::CAMERA_CONSTANT;	
					}else{
						$min=min($CameraSearchAtt[$i]);
						$max=(max($CameraSearchAtt[$i]))+(int)$Camera_subcatID->threshold;
					}
					if((count($CameraSearchAtt)-1) ==$i){
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.")))";	
					}else{
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$camera_id;		
			}
		}

		if($battery && $battery_id)
		{
			$select='Battery';
			$Battery_subcatID=DB::table('spec_subcategory')
							->where('id',$battery_id)
							->where(DB::raw("LOWER(name)"), '=',strtolower($select))
							->first();
			if($Battery_subcatID){
				/* Expload search Attribute*/
				$BatterySearch=explode(',', $battery);
				for ($i=0; $i < count($BatterySearch); $i++) { 
					$Att=explode('-', $BatterySearch[$i]);
					$BatterySearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/
					

				if($price_id || $ram_id || $camera_id){
					$sql=$sql." and PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$battery_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}else{
					$sql=$sql."PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$battery_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}	
				for ($i=0; $i <count($BatterySearchAtt) ; $i++) { 
					if(count($BatterySearchAtt[$i])==1){
						$min=min($BatterySearchAtt[$i]);
						$max=$min+(int)$Battery_subcatID->threshold+GStarBaseController::BATTERY_CONSTANT;	
					}else{
						$min=min($BatterySearchAtt[$i]);
						$max=(max($BatterySearchAtt[$i]))+(int)$Battery_subcatID->threshold;
					}

					if((count($BatterySearchAtt)-1) ==$i){
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.")))";	
					}else{
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$battery_id;		
			}
		}

		if($intenal_memory && $intenal_memory_id)
		{
			$select='Internal';
			$Internal_subcatID=DB::table('spec_subcategory')
						->where('id',$intenal_memory_id)
						->where(DB::raw("LOWER(name)"), '=', strtolower($select))
						->first();
			if($Internal_subcatID){
				/* Expload search Attribute*/
				$InternalSearch=explode(',', $intenal_memory);
				for ($i=0; $i < count($InternalSearch); $i++) { 
					$Att=explode('-', $InternalSearch[$i]);
					$InternalSearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/
				if($price_id || $ram_id || $camera_id|| $battery_id ){
					$sql=$sql." and PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$intenal_memory_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}else{
					$sql=$sql."PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p,product_spec_display x where p.spec_subcatid =".$intenal_memory_id." and x.view_status='1' and p.brand = 'gionee' and (";

				}	
				for ($i=0; $i <count($InternalSearchAtt) ; $i++) { 
					if(count($InternalSearchAtt[$i])==1){
						$min=min($InternalSearchAtt[$i]);
						$max=$min+(int)$Internal_subcatID->threshold+GStarBaseController::INTERNALMM_CONSTANT;	
					}else{
						$min=min($InternalSearchAtt[$i]);
						$max=(max($InternalSearchAtt[$i]))+(int)$Internal_subcatID->threshold;
					}
					if((count($InternalSearchAtt)-1) ==$i){
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.")))";	
					}else{
						$sql=$sql." (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$intenal_memory_id;		
			}
		}

		if($cpu_id && $cpu)
		{
			$select='CPU';
			$cpu_subcatID=DB::table('spec_subcategory')
							->where('id',$cpu_id)
							->where(DB::raw("LOWER(name)"), '=', strtolower($select))
							->first();
			if($cpu_subcatID){
				/* Expload search Attribute*/
				$CPUSearch=explode(',', $cpu);
				for ($i=0; $i < count($CPUSearch); $i++) { 
					$Att=explode('-', $CPUSearch[$i]);
					$SearchAtt[$i] = preg_replace("/[^0-9]/", "", $Att);
				}/* End Expload search Attribute*/

				if($price_id || $ram_id || $camera_id|| $battery_id|| $intenal_memory_id ){
					$sql=$sql." and PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$cpu_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}else{
					$sql=$sql."PAX.model_id IN ("."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$cpu_id." and x.view_status='1' and p.brand = 'gionee' and (";
				}
				for ($i=0; $i <count($SearchAtt) ; $i++) { 
					if(count($SearchAtt[$i])==1){
						$min=min($SearchAtt[$i]);
						$max=$min+(int)$cpu_subcatID->threshold+GStarBaseController::CPU_CONSTANT;	
					}else{
						$min=min($SearchAtt[$i]);
						$max=(max($SearchAtt[$i]))+(int)$cpu_subcatID->threshold;
					}
					if((count($SearchAtt)-1) ==$i){
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.")))";	
					}else{
						$sql=$sql."  (p.numeric_value <=".$max." AND p.numeric_value >= ".$min.") OR";	
					}
				}
				$INArr[]=$cpu_id;		
			}
		}

		if(empty($INArr)){
			return null;
		}
		$str = implode (", ", $INArr);
		$sql=$sql."  and PAX.spec_subcatid IN (".$str;
		$sql=$sql.") and PAX.brand = 'gionee' and PSD.view_status= '1'";		
		//print_r($sql);die;
		return $sql;



    } // END of Search By Attribute Sql




    public static function SEARCH_BY_BRAND($request)
    {

    	$brand_id=trim($request->input('brand_id'));
    	$INArr=array();
    	//$sql="SELECT DISTINCT PAX.model_id FROM product_attribute_xref PAX,product_spec_display PSD  WHERE ";
    	$sql="";

		if($brand_id)
		{
			$select='Price';
			$Price_subcatID=DB::table('spec_subcategory')
							//->where('id',$price_id)
							->where(DB::raw("LOWER(name)"), '=',strtolower($select))
							->first();
			if($Price_subcatID){
				$price_id=$Price_subcatID->id;
					
				$sql=$sql."select DISTINCT p.model_id from product_attribute_xref p, product_spec_display x where p.spec_subcatid =".$price_id." and x.view_status='1' and p.brand = 'gionee' and ";

				$pricesArray=config('constants.PriceArray');
				
				$Att=explode('-', $pricesArray[0]);
				$priceSearchAtt = preg_replace("/[^0-9]/", "", $Att);

				if($priceSearchAtt){
					$min=min($priceSearchAtt);
					$max=(max($priceSearchAtt))+(int)$Price_subcatID->threshold;
				}else{
					$min=0;
					$max=50000;
				}
				$sql=$sql."  (p.text_value <=".$max." AND p.text_value >= ".$min.")";	
				$INArr[]=$price_id;		
			}
		}

		

		if(empty($INArr)){
			return null;
		}
		// $str = implode (", ", $INArr);
		// $sql=$sql."  and PAX.spec_subcatid IN (".$str;
		// $sql=$sql.") and PAX.brand = 'gionee' and PSD.view_status= '1'";		

		return $sql;



    } // END of Search SEARCH_BY_BRAND



    public function changeOrder(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Recommender::changeOrderofSpecificationCategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(RecommenderController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }


	 public function changeOrderSubcategory(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(RecommenderController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Recommender::changeOrderofSpecificationSubCategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(RecommenderController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(RecommenderController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }





} // END of Controller
