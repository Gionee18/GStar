<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\GStarBaseController;
use Validator;
use App\User;
use \Cache;

use Maatwebsite\Excel\Facades\Excel as Excel;
use Illuminate\Support\Facades\Input;
use Redirect,Session;
use Auth,DB,Hash,Mail;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;


class UserController extends GStarBaseController
{
     /**
     * Display a listing of the resource.
     *
     * @return Response
     */
	
     public function AppVersion(Request $request)
	{
		 $result_array = array();          
		 $result_array['count'] = 1;          
		 $result_array['status'] = 'success';          
		 $result_array['version'] = GStarBaseController::SERVICE_VERSION;         
		 return $this->reponseBuilder($result_array);
	}
	 
	  // function for user  List----
    public function index(Request $request)
    {
    	ini_set('memory_limit', -1);
    	set_time_limit(0);
		$inputs = Input::get();
		$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
	    $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';

	    $getAll =  isset($inputs['getAll']) ? trim($inputs['getAll']) : '';

		//List All Users
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$userDetails = User::getListOfAllUsers('all',$search_keyword ,$pageNo,$getAll);
		// function for user  Archive List----
		$ArchiveuserDetails = User::ArchiveUserList('all',$search_keyword ,$pageNo,$getAll);

		if ( (isset($inputs['download']) && $inputs['download'] == 1)) {
				if ( isset($inputs['archive']) && $inputs['archive'] == 1) {
					$data = $ArchiveuserDetails;
					$e_download= Excel::create('report');
					$e_download->sheet('sheet', function($sheet) use (&$data) {
				        $sheet->fromArray($data);
					});
					$e_download->export('csv');
				} else {
					$data = $userDetails;
					$e_download= Excel::create('report');
					$e_download->sheet('sheet', function($sheet) use (&$data) {
				        $sheet->fromArray($data);
					});
					$e_download->export('csv');
				}
		} else {
			$result_array = array();
			$result_array['count'] = count($userDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetails;
			$result_array['archive_user'] = $ArchiveuserDetails;
			$result_array['archive_user_count'] = count($ArchiveuserDetails);
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
		}
		die();
   }


   // function for user  Archive List----
  //   public function ArchiveUserList(Request $request)
  //   {
		// $inputs = Input::get();
		// $search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
	 //    $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		
		// //List All Users
		// $responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	 //    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		// $userDetails = User::ArchiveUserList('all',$search_keyword ,$pageNo);
			
		// $result_array = array();
		// $result_array['count'] = count($userDetails);
		// $result_array['status'] = 'success';
		// $result_array['data'] = $userDetails;
		// $responseContent = $result_array;
			
		// return $this->reponseBuilder($responseContent);
		
  //  }
    

	public function sessionData(Authorizer $auth)
	{
		$user_id  =Authorizer::getResourceOwnerId(); // the token user_id
 		$user=\App\User::find($user_id);

 		//update login_count and last_login
 		$save=User::Updatelogin($user_id,$user['login_count']);

		return json_encode($user);

	}
	
	// function for retrieve user session data------
	 public function userSession(Authorizer $auth)
	 {
			$user_id=Authorizer::getResourceOwnerId(); // the token user_id
			$user=\App\User::find($user_id);

			//update login_count and last_login
 			$save=User::Updatelogin($user_id,$user['login_count']);

 			$user=\App\User::find($user_id);
			$result_array['count'] = count($user);
			$result_array['status'] = 'success';
			$result_array['userImagePath'] = 'uploads/profileImages';
			$result_array['disclaimer_text']="";
			$disclaimer=User::GetDisclaimer();
			if(!empty($disclaimer)){
				$result_array['disclaimer_text']=$disclaimer['disclaimer_text'];
			}
			$result_array['data'] = $user;
			return json_encode($result_array);

	}


	public function SaveDevice(Request $request,Authorizer $auth)
	 {
			$result_array=array();
			$user_id=Authorizer::getResourceOwnerId();
			$user=\App\User::find($user_id);
			
			$user_role=$user['role'];
			$device_id=$request->input('device_id');
			$device_token=$request->input('device_token');

			// echo $user_id.' / '.$user_role.' / '.$device_id.' / '.$device_token;
			// die;
			if($user_id && $user_role && $device_id && $device_token){

				$getDevice=self::GetExistDevice($device_id);
				$save=null;
				if($getDevice){
					$save=self::UpdateDeviceDetail($user_id,$user_role,$device_id,$device_token);
				}else{
					$save=self::InsertDeviceDetail($user_id,$user_role,$device_id,$device_token);
				}
				if($save){
					$result_array['count'] = 1;
					$result_array['status'] = 'success';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_SAVE_DEVICE;
				}else{
					$result_array['count'] = 1;
					$result_array['status'] = 'error';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_SOMETHING_WORNG;
				}
				
			}else{
				$result_array['count'] = 1;
				$result_array['status'] = 'error';
				$result_array['data'] = null;
				$result_array['msg'] = GStarBaseController::MSG_SAVE_DEVICE_ERROR;
				
			}
			return json_encode($result_array);


	}


	public function appLogout(Request $request,Authorizer $auth)
	{      

	    $responseContent=array();
	    $token = $request->input('access_token'); 
	    $device_id = $request->input('device_id');
	    $login_time = $request->input('login_time');
	    $user_id=Authorizer::getResourceOwnerId();
	    if($device_id && $token && $login_time){
	    	$time=strtotime('now');
	        DB::table('saveuser_trail')->where('login_time',$login_time)->update(['logout_time'=>$time]);
	        DB::table('device')->where('device_id',$device_id)->update(['login_status'=>0]);
	        DB::table('users')->where('id',$user_id)->update(['last_logout'=>$time]);

	        $sqlDel =  " Delete from oauth_access_tokens WHERE id='".$token."' ";         
	        $res =  DB::delete( DB::raw($sqlDel) );  

	        $responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_LOGOUT);       
	    }else{
	    			$result_array['count'] = 1;
					$result_array['status'] = 'error';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_SOMETHING_WORNG.'or invalid access token, login_time and device id';
					$responseContent=$result_array;
	    }         
	    return $this->reponseBuilder($responseContent);     
	}


	public function UpdateDeviceDetail($user_id,$user_role,$device_id,$device_token)
	 {
	 	$arr=array('user_id'=>$user_id,'user_role'=>$user_role,'device_token'=>$device_token,'login_status'=>'1');
	 	$update=DB::table('device')->where('device_id',$device_id)->update($arr);
	 	return $update;
	 }

	 public function InsertDeviceDetail($user_id,$user_role,$device_id,$device_token)
	 {
	 	$arr=array('user_id'=>$user_id,'user_role'=>$user_role,'device_token'=>$device_token,'device_id'=>$device_id,'login_status'=>'1');
	 	$insert=DB::table('device')->insert($arr);
	 	return $insert;
	 }


	 public function GetExistDevice($device_id)
	 {
	 	$isexist=DB::table('device')->where('device_id',$device_id)->first();
	 	return $isexist;
	 }


	// function for retrieve user session data------
	 public function UserActivationRequest(Authorizer $auth)
	 {
			$user_id=Authorizer::getResourceOwnerId(); // the token user_id
			$user=\App\User::find($user_id);
			$status=$user['status'];
			if($status=='0'){
				 $request = User::UserActivationRequest($user_id);
				 $responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $request['msg']);
			}else{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_USER_ALREADY_ACTIVE,UserController::MSG_USER_ALREADY_ACTIVE);
			}
			return $this->reponseBuilder($responseContent);
	}

		
	
	// function for Add user------
	 public function create(Request $request)
    {  
			$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,true);
	         if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		  $user = User::AddUser($request);
			if(!Session::has('msg')){	
				$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $user['msg']);
			}else{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
    }
	
    //Function for Check Looged user or not------
    public function isLoggedIn(){
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent)){ 
			
			$responseContent  = $this->queryResponseBuilder(UserController::USER_DETAILS, Auth::user());
		}
		return $this->reponseBuilder($responseContent);
	}



    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show()
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
	 
	// function for Edit user------ 
   public function edit(Request $request,$id)
    {	
		$result_array = array();
		$userDetail_array = array();
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
		if(empty($responseContent))
		{
			if($request->isMethod('post')){
				$role = Auth::User()->role; $userID = Auth::User()->id;
			 	$userRole = User::GetRole($id);
				if($userID != $id)
				{
					if ($userRole >= $role){
				    	$user = User::editUserFromInput($request,$id);
			    	}else{
				   		$responseContent = $this->errorResponseBuilder(UserController::ERROR_NOT_AUTHORIZED,UserController::MSG_NOT_AUTHORIZED);
				   		return $this->reponseBuilder($responseContent);
					}
				}else{
					if($role==UserController::G_ADMIN_ROLE_ID){
						$user = User::editUserFromInput($request,$id);
					}else{
						//Other then Admin in web
						// Request Approve By Admin
						$user = User::editAppUserFromInput($request,$id);

					}
			    	
				}
				if(!Session::has('msg'))
				{
					$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $user['msg']);
				}else{
					$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
				}
			}else{
				$userDetail = User::userDetail($id);
				$result_array['count'] = count($userDetail);
				$result_array['status'] = 'success';
				$result_array['data'] = $userDetail;
				$responseContent = $result_array;
			}
		}
		
		return $this->reponseBuilder($responseContent); 
    }


    // function for Edit user old------ 
  //  public function edit(Request $request,$id)
  //   {	
		
		// $result_array = array();
		// $userDetail_array = array();
		// $responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,true);
		// print_r($responseContent);
		// die;
  //       if(empty($responseContent))
		// { 
		// 	print_r(Auth::User());
		// 	exit;
		// 	$role = Auth::User()->role; $userID = Auth::User()->id;
		// 	 $userRole = User::GetRole($id);
		// 	 echo $userRole; exit;
		// 	if($userID != $id)
		// 	{
		// 		if ($userRole >= $role)
		// 		{
		// 		    $user = User::editUserFromInput($request,$id);
		// 	    }else{
		// 		   $responseContent = $this->errorResponseBuilder(UserController::ERROR_NOT_AUTHORIZED,UserController::MSG_NOT_AUTHORIZED);
		// 		   return $this->reponseBuilder($responseContent);
		// 		}
		// 	}else{
		// 	    $user = User::editUserFromInput($request,$id);
		// 	}
		// 	if(!Session::has('msg'))
		// 	{
		// 		$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $user['msg']);
		// 	}else{
		// 		$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		// 	}
		// }else if(Auth::User()){
		// 	    $userDetail = User::userDetail($id);
		// 		$result_array['count'] = count($userDetail);
		// 		$result_array['status'] = 'success';
		// 		$result_array['data'] = $userDetail;
		// 		$responseContent = $result_array;
		// }else{
		// 	$responseContent = $this->errorResponseBuilder(UserController::ERROR_SESSION_TIMEOUT,UserController::MSG_SESSION_TIMEOUT);
		// }

		// return $this->reponseBuilder($responseContent); 
 	
  //   }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
	 
	 // function for Edit App user------ 
   public function editApp(Request $request,$id)
    {
				
		$result_array = array();
		$userDetail_array = array();
		if($request->isMethod('post')){
			$user = User::editAppUserFromInput($request,$id);
			if(!Session::has('msg'))
			{
			    $responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $user['msg']);
			}else{
			   $responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		}else{
			$userDetails = DB::table('users')
		->join('user_detail', 'users.id', '=', 'user_detail.user_id')
		->join('user_mapping', 'users.id', '=', 'user_mapping.user_id')
		->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id')
		->where('users.id', $id)->where('users.status',1)->first();
		if($userDetails){

				if(!empty($userDetails) )
				{
				
				$sp_name = DB::table('users')->select('first_name','last_name')->where('users.id', $userDetails->parent_id)->first();
				
				if($sp_name){
					$spName = $sp_name->first_name.' '.$sp_name->last_name;
				}else{
					$spName = "";  
				}

					$userDetail_array['id'] = $userDetails->id;
					$userDetail_array['first_name'] = $userDetails->first_name;
					$userDetail_array['last_name'] = $userDetails->last_name;
					$userDetail_array['email'] = $userDetails->email;
					$userDetail_array['contact'] = $userDetails->contact;
					$userDetail_array['status'] = $userDetails->status;
					$userDetail_array['gender'] = $userDetails->gender;
					$userDetail_array['role'] = $userDetails->role;
					$userDetail_array['dob'] = $userDetails->dob;
					$userDetail_array['profile_picture'] = $userDetails->profile_picture;
					$userDetail_array['city'] = $userDetails->city;
					$userDetail_array['state'] = $userDetails->state;
					$userDetail_array['zone'] = $userDetails->zone;
					$userDetail_array['beat_route_id'] = $userDetails->beat_route_id;
					$userDetail_array['rt_code'] = $userDetails->rt_code;
					$userDetail_array['nd_name'] = $userDetails->nd_name;
					$userDetail_array['rd_name'] = $userDetails->rd_name;
					$userDetail_array['sp_name'] = $spName;
					$userDetail_array['sp_id'] = $userDetails->parent_id;
				}
				$result_array['count'] = count($userDetail_array);
				$result_array['status'] = 'success';
				$result_array['data'] = $userDetail_array;
				$responseContent = $result_array;
			}
			else
			{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,'No Active User Found');
			}	
		
		}
		

			 
		return $this->reponseBuilder($responseContent); 
 	
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
	 
  // function for Delete user------ 
	public function destroy(Request $request)
    {
		$responseContent = $this->validateUser(UserController::G_TRAINER_ROLE_ID,true);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
	    $result_array = array(); 
	    $delete1 = '';
		$delete2 = '';
		$delete3 = '';
		    $id = $request->input('id'); 
		    $is_deleted1=DB::table('user_activation_request')
		    			->where('user_id', $id)
		    			->update(['is_deleted'=>1]);

		     $is_deleted2=DB::table('users_dummy')
		    			->where('user_id', $id)
		    			->update(['is_deleted'=>1]);

		     $is_deleted3=DB::table('product_dummy')
		    			->where('request_userid', $id)
		    			->update(['is_deleted'=>1]);

			$delete3 = DB::table('users')->where('id', $id)->where('role','>=',UserController::G_ADMIN_ROLE_ID)->delete();
			if($delete3)
			{
				$delete1 = DB::table('user_detail')->where('user_id', $id)->delete();
				$delete2 = DB::table('user_mapping')->where('user_id', $id)->delete();
			}	
			if($delete1 || $delete2 || $delete3)
			{
				$result_array['status'] = 'success';
				$result_array['msg'] = UserController::MSG_RECORD_DELETED;
				$responseContent = $result_array;
               
            }else
			{
                $responseContent = $this->errorResponseBuilder(UserController::ERROR_USER_NOT_EXIST,UserController::MSG_USER_NOT_EXIST);
            }

      return $this->reponseBuilder($responseContent);
		
   } 


// function for delet All user------ 
 public function deleteAll(Request $request)
    {
		$responseContent = $this->validateUser(UserController::G_TRAINER_ROLE_ID,true);
		if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$result_array = array(); 
		$delete1 = '';
		$delete2 = '';
		$delete3 = '';	

		$userIDs = $request->input('id');
		$userIDArr = array();
		$userIDArr = explode(",",$userIDs);

			$is_deleted1=DB::table('user_activation_request')
		    			->whereIn('user_id', $userIDArr)
		    			->update(['is_deleted'=>1]);

		     $is_deleted2=DB::table('users_dummy')
		    			->whereIn('user_id', $userIDArr)
		    			->update(['is_deleted'=>1]);

		     $is_deleted3=DB::table('product_dummy')
		     			->whereIn('request_userid', $userIDArr)
		    			->update(['is_deleted'=>1]);
		
		    $delete3 = DB::table('users')->whereIn('id', $userIDArr)->where('role','>=',UserController::G_ADMIN_ROLE_ID)->delete();
			if($delete3){
				$delete1 = DB::table('user_detail')->whereIn('user_id', $userIDArr)->delete();
			    $delete2 = DB::table('user_mapping')->whereIn('user_id', $userIDArr)->delete();
			}	
			if(($delete1 || $delete2) && $delete3){
				        $result_array['status'] = 'success';
						$result_array['msg'] = UserController::MSG_RECORD_DELETED;
						$responseContent = $result_array;
				}else{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_USER_NOT_EXIST,UserController::MSG_USER_NOT_EXIST);
			}
		
		return $this->reponseBuilder($responseContent);
	}






	

	 
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    

	 //Function to Validate User for login
	 public function login(Request $request)
	 {  
		$result_array = array();
		$rules = array(
			'email'    => 'required|email', // make sure the email is an actual email
			'password' => 'required|alphaNum|min:5' // password can be alphanumeric and has to be greater than 3 characters
		);

		// run the validation rules on the inputs from the form
		$validator = Validator::make(Input::all(), $rules);
		
		// if the validator fails, redirect back to the form
		if ($validator->fails()) {
			$result_array = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,UserController::MSG_BAD_PARAMETERS);
		} else {
			$UserStatus=DB::table('users')->where('email',trim(Input::get('email')))->where('status','0')->first();
			if(!empty($UserStatus)){
				if($UserStatus->role !=GStarBaseController::G_LEARNER_ROLE_ID){
					$result_array = $this->errorResponseBuilder(UserController::ERROR_NOT_AUTHORIZED,UserController::MSG_AC_DEACTIVATE);
					return json_encode($result_array);
				}
			}
			$ROLE_LOGIN='30';
			$invalidUser=DB::table('users')->where('email',trim(Input::get('email')))->first();	
			if(!empty($invalidUser)){
				if((int)$invalidUser->role > (int)$ROLE_LOGIN){
					$result_array = $this->errorResponseBuilder(UserController::ERROR_NOT_AUTHORIZED,UserController::MSG_NOT_AUTHORIZED);
					return json_encode($result_array);
				}	
			}
			$userdata = array(
			'email'     => trim(Input::get('email')),
			'password'  => trim(Input::get('password'))
			);
			if (Auth::attempt($userdata)) {
				$result_array['count'] = count(Auth::user());
			    $result_array['status'] = 'success';
				$result_array['data'] = Auth::user();
			} else {        
				// validation not successful, send back to form 
				$result_array = $this->errorResponseBuilder(UserController::ERROR_NOT_AUTHORIZED,UserController::MSG_NO_AUTHENTICATE);
			}
		}
		
		return json_encode($result_array);
	 }
	 
	 //Function for Logout currect User
	 public function Logout()
	 {
		  Auth::logout();
		 $responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_LOGOUT);
		 return $this->reponseBuilder($responseContent);
	 }
	 
	 
	 //Function Redirect After Login
	 public function dashboard()
	 {
		  $responseContent = $this->validateUser(UserController::G_ADMIN_ROLE_ID,true);
		if(empty($responseContent)){
			// Return Auth user details
			$responseContent  = $this->queryResponseBuilder(UserController::USER_DETAILS, Auth::user());
		}
		return $this->reponseBuilder($responseContent);
		
		
	 }
   //Function to generate random password
	 private function randomPassword() {
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}
	
	 //Function for send mail-----------
	 public function sendEmail($id,$newPassword,$email,$fname, $subject=null)
    {
       
		$user = array();
		////$userEmail =  DB::table('users')->select('email','first_name')->where('id',$id)->where('status', 1)->orderBy('id', 'ASC')->first();
		////if($userEmail){

			$user['email'] =  $email;
			$user['name'] =  $fname;
			$user['password'] =  $newPassword;
			$user['subject'] =  $subject;
			
	        Mail::send('forgetPassword', ['user' => $user], function ($m) use ($user) {
	        	//$m->from('support@gionee.co.in', $name = 'Gionee Gstar Admin');
	            $m->to($user['email'], $user['name'])->subject($user['subject']);
	        });
    	////}
    }


	 //Function for send mail-----------
	 public function sendEmailForgot($id,$newPassword,$subject=null)
    {
       
		$user = array();
		$userEmail =  DB::table('users')->select('email','first_name')->where('id',$id)->where('status', 1)->orderBy('id', 'ASC')->first();
		if($userEmail){

			$user['email'] =  $userEmail->email;
			$user['name'] =  $userEmail->first_name;
			$user['password'] =  $newPassword;
			$user['subject'] =  $subject;
			
	        Mail::send('forgetPassword', ['user' => $user], function ($m) use ($user) {
	        	//$m->from('support@gionee.co.in', $name = 'Gionee Gstar Admin');
	            $m->to($user['email'], $user['name'])->subject($user['subject']);
	        });
    	}
    }

   //Function to forget password for App-----------
   public function forgetPasswordApp()
	 {
		 $user = User::forgetPassword(Input::get());
		 if(!Session::has('msg')){
			$newPass = $this->randomPassword();
			$update_password = DB::table('users')->where('id', $user->fp_id)
												 ->where('status', '1')
												 ->update(array('password' => Hash::make($newPass)));
											 
			$subject = 'Forget Password for GSTAR';
			$this->sendEmailForgot($user->fp_id,$newPass,$subject);
			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_FORGET_PASSWORD);
		 }else{
			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		 }						
		return $this->reponseBuilder($responseContent);
	 }

	  //Function to forget password for web-----------
    public function forgetPassword()
	 {
		 $user = User::forgetPassword(Input::get());
		 if(!Session::has('msg')){
			$newPass = $this->randomPassword();
		    $update_password = DB::table('users')->where('id', $user->fp_id)
												 ->where('status', '1')
												 ->update(array('password' => Hash::make($newPass)));
											 
			$subject = 'Forget Password for GSTAR';
			$this->sendEmailForgot($user->fp_id,$newPass,$subject);
			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_FORGET_PASSWORD);
		 }else{
			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		 }						
		return $this->reponseBuilder($responseContent);
	 }

	  //Function to change password for App-----------
	  public function ChangeAppPassword()
	 {
		 $user = User::Changepassword(Input::get());
		 $id = Input::get('id'); 
		 if(!Session::has('msg')){
			$update_password = DB::table('users')->where('id', $id)
												 ->where('status', '1')
												 ->update(array('password' => Hash::make($user->password)));
			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_RESET_PSWD);
		 }else{
			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		 }						
		return $this->reponseBuilder($responseContent);
	 }
	 
	 //Function to change password for Web----------- 
	  public function Changepassword()
	 {
		 $user = User::Changepassword(Input::get());
	     $id = Input::get('id'); 
		 if(!Session::has('msg')){
			$update_password = DB::table('users')->where('id', $id)
												 ->where('status', '1')
												 ->update(array('password' => Hash::make($user->password)));
			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_RESET_PSWD);
		 }else{
			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		 }						
		return $this->reponseBuilder($responseContent);
	 }
	 
	 
	  //Function to reset password by admin for Web----------- 
	  public function resetPasswordByAdmin()
	 {
		 $user = User::resetPasswordByAdmin(Input::get());
	     $id = Input::get('id'); 
	     $is_mail_sent = Input::get('is_mail'); 
		 if(!Session::has('msg')){
			$update_password = DB::table('users')->where('id', $id)
												 ->where('status', '1')
												 ->update(array('password' => Hash::make($user->password)));
			$update_Detail = DB::table('users')->select('first_name','last_name','email')->where('id', $id)->first();
			$email=$update_Detail->email;
			$name=$update_Detail->first_name.' '.$update_Detail->last_name;
			if($is_mail_sent)
			{
				$subject = 'Reset Password for GSTAR';
			    $this->sendEmail($id,$user->password,$email,$name,$subject);	
			}
			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_RESET_PSWD);
		 }else{
			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		 }						
		return $this->reponseBuilder($responseContent);
	 }
	 
	  //Function to supervisors List-----------
	   public function supervisorsList()
    {
			$search_keyword = '';
			$pageNo = '';
	    	$getAll =  isset($inputs['getAll']) ? trim($inputs['getAll']) : '';

			//List All Supervisors 
			$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	         if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			$userDetails = User::getListOfAllUsers('supervisors',$search_keyword,$pageNo,$getAll);
			$result_array = array();

			$result_array['count'] = count($userDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $userDetails;
			$responseContent = $result_array;
			
		return $this->reponseBuilder($responseContent);
		
		  
    }
	   /*function to  Import  (USER) excel file to database */
	public function importUser(Request $request)
	{
		$input = Input::get();
		$userData =  array();
		$userPassword =  array();
		$useremail = array();
		$userfName = array();
		$supervisorNotExist="";
		$pass ='';
		$responseContent = $this->validateUser(UserController::G_TRAINER_ROLE_ID,false);
	    if(!empty($responseContent))
	    { return $this->reponseBuilder($responseContent); }
		
		$user = User::importUserFile($request);
		//print_r($user); exit;
		$count=0;
		$deletingIds = array();
		if(!Session::has('msg'))
		{ 
			foreach ($user as $key => $row)
			{
				$ExistingSupervisor = array(DB::table('users')->select('id')->where(array('id'=>$row['sp_id']))->first());
				if($ExistingSupervisor[0]==NULL){
					$supervisorNotExist=$supervisorNotExist.' '.$row['email'].' ';
				}
			}

			if($supervisorNotExist !=NULL){
				$supervisorNotExist='Supervisor not exist for User '.$supervisorNotExist;
				$responseContent = $this->errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,$supervisorNotExist);
				return $this->reponseBuilder($responseContent);
			}
			
			foreach ($user as $key => $row)
			{ 	
				
				$ExistingUser=DB::table('users')->where('email',$row['email'])->first();

				if ($ExistingUser) {

					if($row['status']==2){
						if($row['role'] >= UserController::G_ADMIN_ROLE_ID){
							$deletingIds[] = $ExistingUser->id;
							break;
						}
					}
					//update
					if($row['role'] >= UserController::G_ADMIN_ROLE_ID){
						if (Hash::check($row['password'], $ExistingUser->password)) {
							$userPassword[$ExistingUser->id] = $row['password'];
							$useremail[$ExistingUser->id] = $row['email'];
							$userfName[$ExistingUser->id] = $row['fname'];	
						}
						
					$updateArr=array('email'=>$row['email'],'password'=> Hash::make($row['password']),'first_name'=>$row['fname'],'last_name' => $row['lname'],'contact' =>$row['contact'],'status' => $row['status'],'role' => $row['role'],'dob' => date('y-m-d',strtotime($row['dob'])),'email_sent' => 1,'gender' => $row['gender']);
					
					$data =DB::table('users')->where('id', $ExistingUser->id)->update($updateArr);
					$updateUserDetailArr=array('city' => $row['city'],'state' => $row['state'],'zone' => $row['zone'],'beat_route_id' => $row['beat_route_id'],'rt_code' => $row['rt_code'],'nd_name' => $row['nd_name'],'rd_name' => $row['rd_name']);
					$UserDetails = DB::table('user_detail')->where('user_id', $ExistingUser->id)->update($updateUserDetailArr);
				    $User_mapping = DB::table('user_mapping')->where('user_id', $ExistingUser->id)->update(['parent_id' => $row['sp_id']]);
					  
					}
				} else {
					//Insert
					if($row['role'] >= UserController::G_ADMIN_ROLE_ID && $row['status']!=2){
					$insertArr=array('email'=>$row['email'],'password'=> Hash::make($row['password']),'first_name'=>$row['fname'],'last_name' => $row['lname'],'contact' =>$row['contact'],'status' => $row['status'],'role' => $row['role'],'dob' => date('y-m-d',strtotime($row['dob'])),'email_sent' => 1,'gender' => $row['gender']);
					
					$data =	DB::table('users')->insert($insertArr);
					$lastinsertID = DB::getPdo()->lastInsertId();	
					$inserUserDetailArr=array('user_id' => $lastinsertID,'city' => $row['city'],'state' => $row['state'],'zone' => $row['zone'],'beat_route_id' => $row['beat_route_id'],'rt_code' => $row['rt_code'],'nd_name' => $row['nd_name'],'rd_name' => $row['rd_name']);
					$UserDetails = DB::table('user_detail')->insert($inserUserDetailArr);				   
				    $User_mapping = DB::table('user_mapping')->insert(['user_id' => $lastinsertID,'parent_id' => $row['sp_id']]);
					$userPassword[$lastinsertID] = $row['password'];
					$useremail[$lastinsertID] = $row['email'];
					$userfName[$lastinsertID] = $row['fname'];	  
					}
				}		
			}

			// print_r($deletingIds);
			// die();
			foreach($userPassword as $userID =>$password){
				$subject = 'Password for GSTAR Login';		   
				//$this->sendEmail($userID,$password,$useremail[$userID],$userfName[$userID], $subject);
			}

			if(count($deletingIds)>0){
		 		$delete3 = DB::table('users')->whereIn('id', $deletingIds)->where('role','>=',UserController::G_ADMIN_ROLE_ID)->delete();
				if($delete3){
					$delete1 = DB::table('user_detail')->whereIn('user_id', $deletingIds)->delete();
					$delete2 = DB::table('user_mapping')->whereIn('user_id', $deletingIds)->delete();
				}	
				// if(($delete1 || $delete2) && $delete3){
				// 	$result_array['status'] = 'success';
				// 	$result_array['msg'] = UserController::MSG_RECORD_DELETED;
				// 	$responseContent = $result_array;
				// 	$str .= " and ".count($deletingIds).' '.UserController::MSG_USER_DELETED;
				// }
			}	

			$responseContent  = $this->queryResponseBuilder(GStarBaseController::MSG_TEXT, GStarBaseController::MSG_EXCEL_IMPORT);
		}else{ 
			$responseContent = $this->errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		}
		

		return $this->reponseBuilder($responseContent);
	}

// function to App Update---

   public function appUpdate(Request $request)
   {
			$update = User::appUpdates($request);
			$result_array = array();
			if(!Session::has('msg'))
			{ 
			$ttcount = 0;
			if($update){
				if(isset($update['products']) && count($update['products'])>0){
					$ttcount++;
				}
				if(isset($update['categories']) && count($update['categories'])>0){
					$ttcount++;
				}
				if(isset($update['deleteLog']) && count($update['deleteLog'])>0){
					$ttcount++;
				}
			}
			

			$result_array['count'] = $ttcount;
			$result_array['status'] = 'success';
			$result_array['time_flag'] = time();
			$result_array['data'] = $update;
			
			$responseContent = $result_array;
			}
			else
			{
			$responseContent = $this->errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,Session::get('msg'));	
			}
		return $this->reponseBuilder($responseContent);
	}


// function to App Update Count---

   public function appUpdateCount(Request $request,Authorizer $auth)
   {
			$user_id=Authorizer::getResourceOwnerId(); // the token user_id
			$update = User::appUpdatesCount($request,$user_id);
			$result_array = array();
			if(!Session::has('msg'))
			{ 
			$result_array['count'] = $update;
			$result_array['status'] = 'success';
			$result_array['time_flag'] = time();
			
			
			$responseContent = $result_array;
			}
			else
			{
			$responseContent = $this->errorResponseBuilder(GStarBaseController::ERROR_BAD_PARAMETERS,Session::get('msg'));	
			}
		return $this->reponseBuilder($responseContent);
	}

// function to Zone List-----
    public function ZoneList()
    {
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$ZoneDetails = User::getZoneList();
		$result_array = array();

		$result_array['count'] = count($ZoneDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $ZoneDetails;
		return $this->reponseBuilder($result_array);	  
    }


// function to State List-----
    public function StateList(Request $request)
    {
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
		if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		//$zone_id = $request->input('zone_id');
		$zone_name = $request->input('zone_name');
		$stateDetails = User::getStateList($zone_name);
		$result_array = array();

		$result_array['count'] = count($stateDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $stateDetails;
		return $this->reponseBuilder($result_array);
    }

// function to City List-----
     public function CityList(Request $request)
    {
		$responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		//$state_id = $request->input('state_id');
		$state_name = $request->input('state_name');
		$cityDetails = User::getCityList($state_name);
		$result_array = array();

		$result_array['count'] = count($cityDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $cityDetails;
		return $this->reponseBuilder($result_array);
    }




    // function to App Zone List-----
    public function AppZoneList()
    {
		
		$ZoneDetails = User::getZoneList();
		$result_array = array();

		$result_array['count'] = count($ZoneDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $ZoneDetails;
		return $this->reponseBuilder($result_array);	  
    }


// function to State List-----
    public function AppStateList(Request $request)
    {
		
		//$zone_id = $request->input('zone_id');
		$zone_name = $request->input('zone_name');
		$stateDetails = User::getStateList($zone_name);
		$result_array = array();

		$result_array['count'] = count($stateDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $stateDetails;
		return $this->reponseBuilder($result_array);
    }

// function to City List-----
     public function AppCityList(Request $request)
    {
		//$state_id = $request->input('state_id');
		$state_name = $request->input('state_name');
		$cityDetails = User::getCityList($state_name);
		$result_array = array();

		$result_array['count'] = count($cityDetails);
		$result_array['status'] = 'success';
		$result_array['data'] = $cityDetails;
		return $this->reponseBuilder($result_array);
    }

	
	// function to Add Zone -----
	// public function AddZoneList(Request $request)
 //    {  
	//     $zone = User::AddZone($request);
	// 	$responseContent = array();
	// 		if(!Session::has('msg')){	
	// 			if($zone){ 
	// 				$responseContent['status'] = 'success';
	// 				$responseContent['msg'] = UserController::MSG_RECORD_ADDED;
	// 			}else{
	// 				$responseContent = $this->errorResponseBuilder(UserController::ERROR_ARGUMNETS_MISSING,UserController::MSG_ARGUMNETS_MISSING);
	// 			}
	// 		}else{
	// 			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
	// 		}
	// 	return $this->reponseBuilder($responseContent);
 //    }

	// function to Add State -----
  //   public function AddStateList(Request $request)
  //   {  
		// $state = User::AddState($request);
		// 	if(!Session::has('msg')){	
		// 		if($state){ 
		// 			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_RECORD_ADDED);
		// 		}else{
		// 			$responseContent = $this->errorResponseBuilder(UserController::ERROR_ARGUMNETS_MISSING,UserController::MSG_ARGUMNETS_MISSING);
		// 		}
		// 	}else{
		// 		$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		// 	}
		// return $this->reponseBuilder($responseContent);
  //   }

  // function to Add City -----
  //   public function AddCityList(Request $request)
  //   {  
		// $city = User::AddCity($request);
		// 	if(!Session::has('msg'))
		// 	{	
		// 		if($city)
		// 		{ 
		// 			$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT,UserController::MSG_RECORD_ADDED);
		// 		}
		// 		else
		// 		{
		// 			$responseContent = $this->errorResponseBuilder(UserController::ERROR_ARGUMNETS_MISSING,UserController::MSG_ARGUMNETS_MISSING);
		// 		}
		// 	}
		// 	else
		// 	{
		// 		$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
		// 	}
		// return $this->reponseBuilder($responseContent);
  //   }
	
	
  // function to ND List -----
	public function NdList()
    {
		$url="http://requesterapp.gionee.co.in/api/gstar/shops/ndList?auth_token=3c221fcb7c17ca86e91faa975cbfc8f6";
			 //  Initiate curl
		   $ch = curl_init();
		   // Disable SSL verification
		   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		   // Will return the response, if false it print the response
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		   // Set the url
		   curl_setopt($ch, CURLOPT_URL,$url);
		   // Execute
		   $result=curl_exec($ch);
		   // Closing
		   curl_close($ch);

		   $result_array = array();
		   $realData = json_decode($result);
		   
		   if($realData->object){
			$result_array['count'] = count($realData->object);
			$result_array['status'] = 'success';
			$result_array['data'] = $realData->object;
		   }else{
			$result_array['count'] = count($result);
			$result_array['status'] = 'success';
			$result_array['data'] = $result;
		   }
		  return $this->reponseBuilder($result_array);   

	  
    }

  // function to RD List -----
    public function RdList(Request $request)
    {

   
    $ndID = $request->input('nd_name');

   $url="http://requesterapp.gionee.co.in/api/gstar/shops/rdList?auth_token=3c221fcb7c17ca86e91faa975cbfc8f6&nd=".urlencode($ndID);
     //  Initiate curl "U.T. Electronics Pvt. Ltd."
 


   $ch = curl_init($url);
   // Disable SSL verification
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   // Will return the response, if false it print the response
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   // Set the url
   curl_setopt($ch, CURLOPT_URL,$url);
   // Execute
   $result=curl_exec($ch);
   // Closing
   curl_close($ch);
   $result_array = array();
   $realData = json_decode($result);
   
   if($realData->object){
    $result_array['count'] = count($realData->object);
    $result_array['status'] = 'success';
    $result_array['data'] = $realData->object;
   }else{
    $result_array['count'] = count($result);
    $result_array['status'] = 'success';
    $result_array['data'] = $result;
   }
  return $this->reponseBuilder($result_array); 


  
    }
 



 // function to RD List -----
    public function RtList(Request $request)
    {

   $rdID = $request->input('rd_name');
   $url="http://requesterapp.gionee.co.in/api/gstar/shops/shopList?auth_token=3c221fcb7c17ca86e91faa975cbfc8f6&rd=".urlencode($rdID);
     //  Initiate curl "Asian Traders"
   $ch = curl_init();
   // Disable SSL verification
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   // Will return the response, if false it print the response
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   // Set the url
   curl_setopt($ch, CURLOPT_URL,$url);
   // Execute
   $result=curl_exec($ch);
   // Closing
   curl_close($ch);

   $result_array = array();
   $realData = json_decode($result);
   
   if($realData->object){
    $result_array['count'] = count($realData->object);
    $result_array['status'] = 'success';
    $result_array['data'] = $realData->object;
   }else{
    $result_array['count'] = count($result);
    $result_array['status'] = 'success';
    $result_array['data'] = $result;
   }
  return $this->reponseBuilder($result_array); 


  
    }
	
	
	

	
 // function to Insert Zone in to DB -----	
	// public function insertZoneFromCSV()
 //    {  
		
	// 	$data = User::insertZoneFromCSV();
	//  }
	
// function to Insert State in to DB -----	
	// public function insertStateFromCSV()
 //    {  
	// 	$data = User::insertStateFromCSV();
	// 	}
	
// function to Insert City in to DB -----		
	// public function insertCityFromCSV()
 //    {  
	// 	$data = User::insertCityFromCSV();
	// 	}


	
	

	public function updateprofileRequestList(Request $request)
    {
       
	        $inputs = Input::get();
			$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		    $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		    $responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}

	        $result_array = array();
			$users_dummyDetail = User::updateprofileRequestList($request,$search_keyword ,$pageNo);
			$result_array['count'] = count($users_dummyDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $users_dummyDetail;
			$responseContent = $result_array; 
			return $this->reponseBuilder($responseContent);

    }



    public function updateprofileById($id,$user_id)
    {
       		
	        $result_array = array();
			$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			$users_dummyDetail = User::updateprofileById($id,$user_id);
			$result_array['count'] = count($users_dummyDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $users_dummyDetail;
			$responseContent = $result_array; 
			return $this->reponseBuilder($responseContent);
    }


    public function updateprofileApproved(Request $request)
	 {  

		$result_array = array();
		$responseContent = $this->validateUser(ProductController::G_LEARNER_ROLE_ID,true);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			
		$user_id = trim($request->input('user_id'));
		$id = trim($request->input('id'));
		$approved_status = trim($request->input('approved_status'));
		
		if(!$user_id && !$approved_status && !$id) {
			$result_array = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,UserController::MSG_BAD_PARAMETERS);
		}
		else {

			$UserDetail = User::updateprofileById($id,$user_id);
			$UserDetail=$UserDetail['newdata'];
			
			$updateprofile=User::updateRequestProfile($id,$UserDetail,$approved_status);
			$result_array=$updateprofile;

		}
		
		return json_encode($result_array);
	 }


	 public function accountActivationList() {

	 	    $inputs = Input::get();
			$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		    $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		    $responseContent = $this->validateUser(UserController::G_LEARNER_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		
		 	$result_array = array();
			$activationList = User::accountActivationList($search_keyword ,$pageNo);
			$result_array['count'] = count($activationList);
			$result_array['status'] = 'success';
			$result_array['data'] = $activationList;
			$responseContent = $result_array; 
			return $this->reponseBuilder($responseContent);
	}


	public function activateUser(Request $request) {
		
		
		$responseContent = array();
		$responseContent = $this->validateUser(UserController::G_TRAINER_ROLE_ID,true);
		if(!empty($responseContent)){
			  return $this->reponseBuilder($responseContent);
		  }
		 $user_id = trim($request->input('user_id'));
		 $activation_status = trim($request->input('activation_status'));
		 
		if($user_id && $activation_status){

			if($activation_status != UserController::ADMIN_REJECT_REQUEST){
				$users = User::activateUser($user_id,$activation_status);
				$this->sendEmailUserActivation($user_id);
				$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_USER_ACTIVATE);
			}else{
				$users = User::activateUser($user_id,$activation_status);
				$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_USER_ACTIVATE_REJECT);
			}
			// $users = User::activateUser($user_id,$activation_status);
			// if($activation_status != UserController::ADMIN_REJECT_REQUEST){
			// 	$this->sendEmailUserActivation($user_id);
			// }
			// $responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, UserController::MSG_USER_ACTIVATE);
		}else
		{
		$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,UserController::MSG_ARGUMNETS_MISSING);
		}
		  
		 
		  return $this->reponseBuilder($responseContent);
	}

	 public function sendEmailUserActivation($id)
    {
       
// 		Gionee Gstar: account activation approved

// your request for account activation has been approved.
// Kindly login to account to avail the services.
		$user = array();
		$userEmail=User::getUserDetailforActivation($id);
		if($userEmail){

			$user['email'] =  $userEmail->email;
			$user['name'] =  $userEmail->first_name.' '.$userEmail->last_name;
			$user['subject'] =  'Gionee Gstar: account activation approved';
			
	        Mail::send('accountActivation', ['user' => $user], function ($m) use ($user) {
	        	//$m->from('support@gionee.co.in', $name = 'Gionee Gstar Admin');
	            $m->to($user['email'], $user['name'])->subject($user['subject']);
	        });
    	}
    }



    public function SaveUserTrail(Request $request,Authorizer $auth)
	 {
		$result_array=array();
		$user_id=Authorizer::getResourceOwnerId();
		$user=\App\User::find($user_id);	
		$userId=$request->input('user_id');
		$data=$request->input('data');
		
		if($userId  && $data)
		{
			if($userId == $user_id){
				$SaveAudit=User::SaveUserAuditTrail($userId,$data);
				if($SaveAudit){
					$result_array['count'] = 1;
					$result_array['status'] = 'success';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_SAVE_AUDIT;
				}else{
					$result_array['count'] = null;
					$result_array['status'] = 'error';
					$result_array['data'] = null;
					$result_array['msg'] = GStarBaseController::MSG_SOMETHING_WORNG;
				}
			}else{
				$result_array['count'] = null;
				$result_array['status'] = 'error';
				$result_array['data'] = null;
				$result_array['msg'] = GStarBaseController::MSG_SAVE_AUDIT_ERROR ;
			}
				
		}else{
			$result_array['count'] = null;
			$result_array['status'] = 'error';
			$result_array['data'] = null;
			$result_array['msg'] = GStarBaseController::MSG_SAVE_AUDIT_ERROR;	
		}
		return json_encode($result_array);

	}



	/* Cron to deactivate user whos login is 30 days older*/
	public function deactivateUsers(Request $request) {
		
		$data = array();
		$last_login = strtotime('-30 days');
		$users = User::GetInactiveUser($last_login);
		
		if(count($users)) {
			foreach($users as $user) {
				$data[$user->id] = User::deActivateUser($user->id);
			} 
		}
		
		return $this->reponseBuilder($data); 
	}
	
	/* Cron to logout devices which session has been expired */
	public function logoutDevice(Request $request) {
		
		$data = array();
		$userLogout=array();
		$last_login = strtotime('now');
		$users = User::expiredSession($last_login);

		if(count($users)) {
			foreach($users as $user) {
				$userLogout=User::cleardevice($user['user_id'], $last_login, $user['id']);
			} 
		}

		return $this->reponseBuilder($userLogout); 
	}


	/* Cron to inactive users will be removed from database after an year. */
	public function DeleteArchiveUsers(Request $request) {
		
		$data = array();
		$users = User::ListofArchiveUserToBeDeleted();

		if(count($users)) {
			for ($i=0; $i <count($users) ; $i++) { 
				$data[$users[$i]] = User::DeleteArchiveUser($users[$i]);
			}
		}
		
		return $this->reponseBuilder($data); 
	}
	


	public function Disclaimer(Request $request)
    {	
		$result_array = array();
		$userDetail_array = array();
		$responseContent = $this->validateUser(UserController::G_TRAINER_ROLE_ID,false);

		if(empty($responseContent))
		{
			if($request->isMethod('post'))
			{
				$disclaimer_text=$request->input('disclaimer_text');
				$disclaimer = User::UpdateDisclaimer($disclaimer_text);
				if($disclaimer){			
					$result_array['status'] = 'success';
					$result_array['msg'] = UserController::MSG_DISCLAIMER;
					$responseContent = $result_array;
				}else{
					$responseContent = $this->errorResponseBuilder(UserController::ERROR_ARGUMNETS_MISSING,UserController::MSG_NO_UPDATE);
				}
			}else{
				$disclaimer = User::GetDisclaimer();
				$result_array['count'] = count($disclaimer);
				$result_array['status'] = 'success';
				$result_array['data'] = $disclaimer;
				$responseContent = $result_array;
			}
		}
		
		return $this->reponseBuilder($responseContent); 
    }
	
	
	
}
