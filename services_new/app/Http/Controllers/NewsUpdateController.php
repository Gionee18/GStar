<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\NewsUpdate;
use App\User;
use App\Asset;
use Validator;
use Illuminate\Support\Facades\Input;
use Redirect,Session;
use Auth,DB,Hash;
use App\Http\Controllers\GStarBaseController;
use LucaDegasperi\OAuth2Server\Authorizer;


class NewsUpdateController extends GStarBaseController
{
    
    public function AppNewsUpdateList(Request $request,Authorizer $auth)
    {
		
    	$user_id=$auth->getResourceOwnerId(); // the token user_id
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$Details = NewsUpdate::AppNewsUpdateList($search_keyword,$user_id,$pageNo);

		$result_array = array();

		$result_array['count'] = count($Details);
		$result_array['status'] = 'success';
		$result_array['data'] = $Details;
		$responseContent = $result_array;

		return $this->reponseBuilder($responseContent);
		
	 }
	 
	 public function AppNewsUpdateTopicsList(Request $request,Authorizer $auth)
    {
		
    	$user_id=$auth->getResourceOwnerId(); // the token user_id
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$category_id = isset($inputs['category_id']) ? trim($inputs['category_id']) : '';
		$subcategory_id = isset($inputs['subcategory_id']) ? trim($inputs['subcategory_id']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$Details = NewsUpdate::AppNewsUpdateTopicsList($search_keyword,$user_id,$category_id,$subcategory_id,$pageNo);

		$result_array = array();

		$result_array['count'] = count($Details);
		$result_array['status'] = 'success';
		$result_array['data'] = $Details;
		$responseContent = $result_array;

		return $this->reponseBuilder($responseContent);
		
	 }
	 
	 public function UpdateReadStatus(Request $request,Authorizer $auth)
    {
		$user_id=$auth->getResourceOwnerId();
		$userId = $request->input('user_id');
		$data = $request->input('data');

		$userId=$request->input('user_id');
		$data=$request->input('data');
		//print_r($data);die;
		if($userId  && $data)
		{
			if($userId == $user_id){
				$save = NewsUpdate::UpdateReadStatus($user_id,$data);
				if($save){
					$result_array['count'] = 1;
					$result_array['status'] = 'success';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC;
				}else{
					$result_array['count'] = null;
					$result_array['status'] = 'error';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_FAIL;
				}
			}else{
				$result_array['count'] = null;
				$result_array['status'] = 'error';
				$result_array['data'] = null;
				$result_array['msg'] = 'Mismatch UserId' ;
			}
				
		}else{
			$result_array['count'] = null;
			$result_array['status'] = 'error';
			$result_array['data'] = null;
			$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_PARAMETRE;	
		}
		return json_encode($result_array);
		
	 }

    public function listCategoryNews(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(NewsUpdateController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$CategoryDetails = NewsUpdate::getListOfNewsCategory($search_keyword,$pageNo);
			$result_array = array();
			$result_array['count'] = count($CategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $CategoryDetails;
			$responseContent = $result_array;
		}

		return $this->reponseBuilder($responseContent);
		
	 }


	  public function listSubCategoryNews(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(NewsUpdateController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SubCategoryDetails = NewsUpdate::getListOfNewsSubCategory($search_keyword,$pageNo);
			$result_array = array();
			$result_array['count'] = count($SubCategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SubCategoryDetails;
			$responseContent = $result_array;
		}

		return $this->reponseBuilder($responseContent);
		
	 }
	 
	  public function CategoryNewsById($id)
     {
		$result_array = array();
        $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$userDetail = NewsUpdate::CategoryNewsById($id);
			$result_array['count'] = count($userDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetail;				
		    $responseContent = $result_array;
        }
	  return $this->reponseBuilder($responseContent);
	}


	public function SubCategoryNewsById($id)
     {
		$result_array = array();
        $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$userDetail = NewsUpdate::SubCategoryNewsById($id);
			$result_array['count'] = count($userDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetail;				
		    $responseContent = $result_array;
        }
	  return $this->reponseBuilder($responseContent);
	}




    public function createCategoryNews(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $category = NewsUpdate::AddNewsCategory($request);
			if(!Session::has('msg'))
			{	
				$save = NewsUpdate::InsertNewsCategory($category); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = NewsUpdateController::MSG_ADDED_NEWS_UPDATE;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }


    public function createSubCategoryNews(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $subcategory = NewsUpdate::AddNewsSubCategory($request);
			if(!Session::has('msg'))
			{	
				$save = NewsUpdate::InsertNewsSubCategory($subcategory); 
				if($save){
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = NewsUpdateController::MSG_ADDED_NEWS_UPDATE_SUB;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }



    public function updateCategoryNews(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$category = NewsUpdate::editCategoryNews($request);
				if(!Session::has('msg'))
				{	
					$save = NewsUpdate::updateCategoryNews($category,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = NewsUpdateController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }


    public function updateSubCategoryNews(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$subcategory = NewsUpdate::editSubCategoryNews($request);
				if(!Session::has('msg'))
				{	
					$save = NewsUpdate::updateSubCategoryNews($subcategory,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = NewsUpdateController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }


    public function deleteCategoryNews(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$id = $request->input('id');
				if($id)
				{	
							$newstopic_count=NewsUpdate::getnewstopicBycategoryId($id);
							if($newstopic_count){
								$subcategory_count=NewsUpdate::getsubcategoryBycategoryId($id);
								if($subcategory_count){
									$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_SUBCAT_EXIST);
								}else{
									$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_TOPIC_EXIST);
								}
								
							}else{
								$save = NewsUpdate::deleteCategoryNews($id); 
								if($save){
									GStarBaseController:: deleteLog('Update category',$id);
									$result_array = array();
									$result_array['status'] = 'success';
									$result_array['msg'] = NewsUpdateController::MSG_RECORD_DELETED;
									$responseContent = $result_array;
								}else{
									$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_RECORD);
								}	
							}
							
				}else{
				     $responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }


   public function deleteSubCategoryNews(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$id = $request->input('id');
				if($id)
				{	
					$newstopic_count=NewsUpdate::getnewstopicBysubcategoryId($id);
					if($newstopic_count){
						$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_TOPIC_EXIST_SUBCAT);
					}else{
						$save = NewsUpdate::deleteSubcategoryNews($id); 
						if($save){
							GStarBaseController:: deleteLog('Update subcategory',$id);
							$result_array = array();
							$result_array['status'] = 'success';
							$result_array['msg'] = NewsUpdateController::MSG_RECORD_DELETED;
							$responseContent = $result_array;
						}else{
							$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_RECORD);
						}	
					}		
				}else{
				     $responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }



   public function listNewsTopic(Request $request)
    {
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

		$responseContent = $this->validateUser(NewsUpdateController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$NewsTopicDetails = NewsUpdate::getListOfNewsTopic($search_keyword,$pageNo);

			$result_array = array();

			$result_array['count'] = count($NewsTopicDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $NewsTopicDetails;
			$responseContent = $result_array;

		}

		return $this->reponseBuilder($responseContent);
		
	 }

	  public function newsTopicById($id)
     {
		   $result_array = array();
           $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,false);
		   if(empty($responseContent))
		   {
			$userDetail = NewsUpdate::newsTopicById($id);
			$result_array['count'] = count($userDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetail;				
		    $responseContent = $result_array;
          }
	  return $this->reponseBuilder($responseContent);
	}

   public function createNewsTopic(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $topic = NewsUpdate::AddNewsTopic($request);
		   //print_r($topic); die;
		   //$topic = NewsUpdate::SendPushAndroid($topic);
		   //die;
			if(!Session::has('msg'))
			{	
				$save = NewsUpdate::InsertNewsTopic($topic); 
				if($save){
					/* Send PUSH Notification */
					if($topic['status']== 1){
						$topic = NewsUpdate::SendPushAndroid($topic);
					}
					
					/* End */
					$result_array = array();
					$result_array['status'] = 'success';
					$result_array['msg'] = NewsUpdateController::MSG_ADDED_NEWS_TOPIC;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}	
			}else{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}
		return $this->reponseBuilder($responseContent);
    }


    public function updateNewsTopic(Request $request)
    {
		$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		if(empty($responseContent))
		{
		   $id=trim($request->input('id'));
		   if(!empty($id)){
		   		$topic = NewsUpdate::editNewsTopic($request);
		   		//print_r($topic);
		   		//die;
				if(!Session::has('msg'))
				{	
					$save = NewsUpdate::updateNewsTopic($topic,$id); 
					if($save){
						$result_array = array();
						$result_array['status'] = 'success';
						$result_array['msg'] = NewsUpdateController::MSG_RECORD_UPDATED;
						$responseContent = $result_array;
					}else{
						$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_UPDATE);
					}	
				}else{
					$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
		   }else{
		   		$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_ARGUMNETS_MISSING,NewsUpdateController::MSG_ARGUMNETS_MISSING);
		   }
		   
		}
		return $this->reponseBuilder($responseContent);
    }



    public function deleteNewsTopic(Request $request)
    {
			$result_array = array();		
			$responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
			if(empty($responseContent))
			{
				$id = $request->input('id');
				if($id)
				{	
							$save = NewsUpdate::deleteNewsTopic($id); 
							if($save){
								GStarBaseController:: deleteLog('Update topic',$id);
								$result_array = array();
								$result_array['status'] = 'success';
								$result_array['msg'] = NewsUpdateController::MSG_RECORD_DELETED;
								$responseContent = $result_array;
							}else{
								$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_NO_UPDATE,NewsUpdateController::MSG_NO_RECORD);
							}	
				}else{
				     $responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,NewsUpdateController::MSG_ARGUMNETS_MISSING);
				}
			}

			return $this->reponseBuilder($responseContent);	
   }




   // Get Subcategory under category
   
	public function CategorySubcategory(Request $request)
    {
		//echo 'hbjhbj'; die;
		$responseContent = $this->validateUser(NewsUpdateController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			$SubCategoryDetails = NewsUpdate::CategorySubcategoryList();
			$result_array = array();
			$result_array['count'] = count($SubCategoryDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $SubCategoryDetails;
			$responseContent = $result_array;
		}

		return $this->reponseBuilder($responseContent);
		
	 }


	  public function changeOrder(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = NewsUpdate::changeOrderofTopic($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(NewsUpdateController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }


	 public function changeOrderCategory(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = NewsUpdate::changeOrderofTopicCategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(NewsUpdateController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }



	 public function changeOrderSubcategory(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(NewsUpdateController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = NewsUpdate::changeOrderofTopicSubcategory($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(NewsUpdateController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(NewsUpdateController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }

    




}
