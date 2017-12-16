<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Tutorial;
use App\User;
use App\Asset;
use Validator;
use Illuminate\Support\Facades\Input;
use Redirect,Session;
use Auth,DB,Hash;
use App\Http\Controllers\GStarBaseController;
use LucaDegasperi\OAuth2Server\Authorizer;


class TutorialController extends GStarBaseController
{


	public function listTutorial(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(TutorialController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Tutorial::getListOfTutorial($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }

	 public function ApplistTutorial()
    {
		
			$CategoryDetails = Tutorial::AppgetListOfTutorial();

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		return $this->reponseBuilder($responseContent);
		
	 }



	 public function tutorial_categoriesProducts()
    {
		
		$responseContent = $this->validateUser(TutorialController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Tutorial::getAllCategoryOfTutorialWithNewSubcat();

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }


	  //  public function getTutorialwithProductByCategory($cat_id = null)
   //  {
		 //    $result_array = array();
			// $TutorialDetails = Tutorial::getTutorialwithProductByCategory($cat_id);
			// $result_array['count'] = count($TutorialDetails);
			// $result_array['status'] = 'success';
			// $result_array['data'] = $TutorialDetails;
			// $responseContent = $result_array;
			// return $this->reponseBuilder($responseContent);
			
   //  }


	 // function for Add Video Tutorial----
    public function createTutorial(Request $request)
    {
    	$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			ini_set('upload_max_filesize', '250M');
			ini_set('post_max_size', '250M');
			$tutorial = Tutorial::createTutorial($request);

			if(!Session::has('msg'))
			{
				$save = Tutorial::addTutorial($tutorial); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = TutorialController::MSG_ADDED_TUTORIAL;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_ARGUMNETS_MISSING,TutorialController::MSG_ARGUMNETS_MISSING);
				}			
			}else{
				 $responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }



    public function updateTutorial(Request $request)
    {
			$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$video_id=trim($request->input('video_id'));
				if($video_id){
					$tutorial = Tutorial::editTutorial($request);
					if(!Session::has('msg'))
					{	
								$save = Tutorial::updateTutorial($tutorial,$video_id); 
								if($save){
									$result_array = array();
									$result_array['status'] = 'success';
									$result_array['msg'] = TutorialController::MSG_RECORD_UPDATED;
									$responseContent = $result_array;
								}else{
									$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_NO_UPDATE,CategoryController::MSG_NO_UPDATE);
								}	
					}else{
					     $responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
					}
				}else{
					$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,TutorialController::MSG_ARGUMNETS_MISSING);
				}
				
			}

			return $this->reponseBuilder($responseContent);
    }


    public function deleteTutorial(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$video_id = $request->input('video_id');
				if($video_id)
				{	
							$save = Tutorial::deleteTutorial($video_id); 
							if($save){
								$result_array = array();
								$result_array['status'] = 'success';
								$result_array['msg'] = TutorialController::MSG_RECORD_DELETED;
								$responseContent = $result_array;
							}else{
								$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_NO_UPDATE,CategoryController::MSG_NO_RECORD);
							}	
				}else{
				     $responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,TutorialController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);
			
   }





   public function VideoTutorialById($id)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,false);
			if(empty($responseContent))
			{

				$videoDetail = Tutorial::VideoTutorialById($id); 
				$result_array['count'] = 1;
				$result_array['status'] = 'success';
				$result_array['data'] = $videoDetail;
				$responseContent = $result_array;
			}
			return $this->reponseBuilder($responseContent);
			
   }





   public function listTutorialSubcategory(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(TutorialController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = Tutorial::getListOfTutorialSubcat($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }



	 public function createTutorialSubcategory(Request $request)
    {
    	$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			$tutorialsubcat = Tutorial::createTutorialSubcat($request);

			if(!Session::has('msg'))
			{
				$save = Tutorial::addTutorialSubcat($tutorialsubcat); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = TutorialController::MSG_ADDED_TUTORIALSUBCAT;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_ARGUMNETS_MISSING,TutorialController::MSG_ARGUMNETS_MISSING);
				}			
			}else{
				 $responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }


    public function updateTutorialSubcategory(Request $request,$id)
    {
    	$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			if($request->isMethod('post')){
				if($id){
					$tutorialsubcat = Tutorial::editTutorialSubcat($request,$id);
					if(!Session::has('msg'))
					{	
						$save = Tutorial::updateTutorialSubcat($tutorialsubcat,$id); 
						if($save){
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = TutorialController::MSG_RECORD_UPDATED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_NO_UPDATE,CategoryController::MSG_NO_UPDATE);
						}	
					}else{
						$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
					}
				}else{
					$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,TutorialController::MSG_ARGUMNETS_MISSING);
				}
			}else {
				$data=Tutorial::GettutorialSubcatBySubcatId($id);
				$result_array = array();
				$result_array['status'] = 'success';
				$result_array['count'] = count($data);
				$result_array['data'] = $data;
				$responseContent = $result_array;
			}
		}
    	
    	
		return $this->reponseBuilder($responseContent);
    }


     public function deleteTutorialSubcategory(Request $request)
    {
		$result_array = array();		
		$responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
			$id = $request->input('id');
			if($id)
			{	
				$data=Tutorial::GettutorialBySubcatId($id);
				if(!empty($data)){
					$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,TutorialController::MSG_TUTORIAL_EXIST_FOR_SUBCATEGORY);
				}else{
					$save = Tutorial::deleteTutorialSubcat($id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = TutorialController::MSG_RECORD_DELETED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_NO_UPDATE,CategoryController::MSG_NO_RECORD);
					}
				}
					
			}else{
				$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,TutorialController::MSG_ARGUMNETS_MISSING);
			}
		}
		return $this->reponseBuilder($responseContent);
			
   }



   public function changeOrder(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Tutorial::changeOrderofTutorial($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(TutorialController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }


	 public function changeOrderSubcategory(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(TutorialController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Tutorial::changeOrderofTutorialSubcategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(TutorialController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(TutorialController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }








}
