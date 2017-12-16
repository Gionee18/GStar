<?php

namespace App;
use DB,Auth;
use Intervention\Image\Facades\Image as Image;
use File;
use Session,Validator,Hash;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Maatwebsite\Excel\Facades\Excel as Excel;
use Illuminate\Routing\UrlGenerator;
use App\NewsUpdate;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	// Function to Add user in to DB------
	 public static function AddUser($request) 
    { 
        $rules = array(
            'sp_id'    => 'required', // Supervisor ID is required
            'first_name'    => 'required', // first_name is required
            'last_name' => 'required', // last_name is required
            'email' => 'required', // email is required
            'password'=> 'required|alphaNum|min:5',//password is required
            'role'=> 'required',//role is required
            'contact'=> 'required',//contact is required
            'status' => 'required', // status is required
            'gender'=> 'required',//gender is required
            'dob'=> 'required'//dob is required
			//'city'=> 'required',//city is required
            //'state'=> 'required',//state is required
            //'zone'=> 'required'//zone is required
            //'beat_route_id'=> 'required',//beat_route_id is required
            //'rt_code'=> 'required',//rt_code is required
            //'nd_name'=> 'required',//nd_name is required
            //'rd_name'=> 'required'//rd_name is required
        );
        $validator = Validator::make($request->input(), $rules);
        $messages = $validator->errors();
        
        Session::forget('msg');
        $user = new User();
		$userDetail = array();
        
        if(!$messages->first('first_name')) {
            $user['first_name'] = trim($request->input('first_name'));
            } 
        else {
            Session::flash('msg', $messages->first('first_name'));
        } 
        if(!$messages->first('last_name')) {
            $user['last_name'] = trim($request->input('last_name'));
            } 
        else {
            Session::flash('msg', $messages->first('last_name'));
        } 
        if(!$messages->first('email')) {
				$email=$request->input('email');
				$alreadyemail=DB::table('users')->select('id')->where('email','=',$email)->get(); 
				if($alreadyemail!=NULL)
				 {
					Session::flash('msg', GStarBaseController::MSG_EMAIL_EXIST);
				 }
				   else
				   {
					$user['email'] = trim($request->input('email'));
				   } 
			} 
        else {
            Session::flash('msg', $messages->first('email'));
        } 
        if(!$messages->first('password')) {
            $user['password'] = Hash::make(trim($request->input('password')));
            } 
        else {
            Session::flash('msg', $messages->first('password'));
        } 
        if(!$messages->first('role')) {
            $user['role'] = trim($request->input('role'));
            } 
        else {
            Session::flash('msg', $messages->first('role'));
        } 
        if(!$messages->first('contact')) {
            $user['contact'] = trim($request->input('contact'));
            } 
        else {
            Session::flash('msg', $messages->first('contact'));
        } 
        if(!$messages->first('status')) {
            $user['status'] = trim($request->input('status'));
            } 
        else {
            Session::flash('msg', $messages->first('status'));
        } 
        if(!$messages->first('gender')) {
            $user['gender'] = trim($request->input('gender'));
            } 
        else {
            Session::flash('msg', $messages->first('gender'));
        } 
        if(!$messages->first('dob')) {
            $user['dob'] = date('y-m-d',strtotime(trim($request->input('dob'))));
            } 
        else {
            Session::flash('msg', $messages->first('dob'));
        } 
		
		if(!$messages->first('sp_id')) {
            $sp_id = trim($request->input('sp_id'));
            } 
        else {
            Session::flash('msg', $messages->first('sp_id'));
        }
      
		if(!$messages->first('city')) {
            $userDetail['city'] = trim($request->input('city'));
            } 
        else {
            Session::flash('msg', $messages->first('city'));
        }
		if(!$messages->first('state')) {
            $userDetail['state'] = trim($request->input('state'));
            } 
        else {
            Session::flash('msg', $messages->first('state'));
        }
		if(!$messages->first('zone')) {
            $userDetail['zone'] = trim($request->input('zone'));
            } 
        else {
            Session::flash('msg', $messages->first('zone'));
        }
		if(!$messages->first('beat_route_id')) {
            $userDetail['beat_route_id'] = trim($request->input('beat_route_id'));
            } 
        else {
            Session::flash('msg', $messages->first('beat_route_id'));
        }
		if(!$messages->first('rt_code')) {
            $userDetail['rt_code'] = trim($request->input('rt_code'));
            } 
        else {
            Session::flash('msg', $messages->first('rt_code'));
        }
		if(!$messages->first('nd_name')) {
            $userDetail['nd_name'] = trim($request->input('nd_name'));
            } 
        else {
            Session::flash('msg', $messages->first('nd_name'));
        }
		if(!$messages->first('rd_name')) {
            $userDetail['rd_name'] = trim($request->input('rd_name'));
            } 
        else {
            Session::flash('msg', $messages->first('rd_name'));
        }
		
		if(Input::hasFile('image')){ 
			
			$user['profile_picture'] = $user->uploadImage($request);
		}
		if(!Session::has('msg')){
			$newUser = $user->save();
			$newUserID = $user->id;
			if($newUserID){
			$UserDetails = DB::table('user_detail')->insert(
				['user_id' => $newUserID,
				  'city' => $userDetail['city'],
				 'state' => $userDetail['state'],
				 'zone' => $userDetail['zone'],
				 'beat_route_id' => $userDetail['beat_route_id'],
				 'rt_code' => $userDetail['rt_code'],
				 'nd_name' => $userDetail['nd_name'],
				 'rd_name' => $userDetail['rd_name']
				  ]
			   );
			  
			$User_mapping = DB::table('user_mapping')->insert(
				['user_id' => $newUserID,
				 'parent_id' => $sp_id
				  ]
			   );
			}
		}
		if(!empty($UserDetails)){
			$user['msg'] = GStarBaseController::MSG_ADDED_USER;
		}
        return $user;
    }
	
	
 // Function to Upload Image------	
	private function uploadImage($request)
	{
		$filename='';
		$fileSize = $request->file('image')->getSize();
		$extensionofFile = $request->file('image')->getClientOriginalExtension();
		$returnValue = GStarBaseController::verifyfile($extensionofFile,'image');
		if($returnValue)
		{
			if($fileSize <= GStarBaseController::FILE_SIZE)
			{ 
				$filename = time().".".$request->file('image')->getClientOriginalExtension(); 
				
				if (!is_dir(base_path('uploads'). '/profileImages')) 
				{
					mkdir(base_path('uploads'). '/profileImages', 0777, true); 
				}
				$targetPath = base_path('uploads'). '/profileImages';
				$targetfilePath = $targetPath.'/'.$filename; 				
				$isUploaded = $request->file('image')->move($targetPath,$filename);
				if($isUploaded)
				{
						$filename1 = self::createThumb($filename,$targetfilePath,$extensionofFile);
						$filename2 = self::createThumbMedium($filename,$targetfilePath,$extensionofFile);

				}
				return $filename;
			}
			else 
			{
				Session::flash('msg',GStarBaseController::MSG_FILE_SIZE);
			}
		}
		else
		{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
	}

 public static function createThumb($filename,$targetfilePath,$extensionofFile) 
 { 
 
					//--------------------------------
					if (!is_dir(base_path('uploads'). '/profileImages/thumbnail')) 
					{
						mkdir(base_path('uploads'). '/profileImages/thumbnail', 0777, true); 
					}

					$targetPath_thumb = base_path('uploads/profileImages'). '/thumbnail'; 
					$targetfilePath_thumb = $targetPath_thumb.'/'.$filename; 
					list($width,$height) = getimagesize($targetfilePath);
			        $thumb_width = 200;
					$thumb_height = 160;
					$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
					switch($extensionofFile)
					{
						case 'jpg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'jpeg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'png':
						$source = imagecreatefrompng($targetfilePath);
						break;
						case 'gif':
						$source = imagecreatefromgif($targetfilePath);
						break;
						default:
						$source = imagecreatefromjpeg($targetfilePath);
					}
					imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
					switch($extensionofFile)
					{
						case 'jpg' || 'jpeg':
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'png':
						imagepng($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'gif':
						imagegif($thumb_create,$targetfilePath_thumb,100);
						break;
						default:
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
					}
					return $filename;
 }

 public static function createThumbMedium($filename,$targetfilePath,$extensionofFile) 
 { 
 
					//--------------------------------
					if (!is_dir(base_path('uploads'). '/profileImages/thumbnail_medium')) 
					{
						mkdir(base_path('uploads'). '/profileImages/thumbnail_medium', 0777, true); 
					}

					$targetPath_thumb = base_path('uploads/profileImages'). '/thumbnail_medium'; 
					$targetfilePath_thumb = $targetPath_thumb.'/'.$filename; 
					list($width,$height) = getimagesize($targetfilePath);
			        $thumb_width = 400;
					$thumb_height = 320;
					$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
					switch($extensionofFile)
					{
						case 'jpg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'jpeg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'png':
						$source = imagecreatefrompng($targetfilePath);
						break;
						case 'gif':
						$source = imagecreatefromgif($targetfilePath);
						break;
						default:
						$source = imagecreatefromjpeg($targetfilePath);
					}
					imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
					switch($extensionofFile)
					{
						case 'jpg' || 'jpeg':
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'png':
						imagepng($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'gif':
						imagegif($thumb_create,$targetfilePath_thumb,100);
						break;
						default:
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
					}
					return $filename;
 }
 // Function to Upload Image------	
/*	private function uploadImage($request)
	{
		$filename='';
		$fileSize = $request->file('image')->getSize();
		$extensionofFile = $request->file('image')->getClientOriginalExtension();
		$returnValue = GStarBaseController::verifyfile($extensionofFile,'image');
		if($returnValue)
		{
			if($fileSize <= GStarBaseController::FILE_SIZE)
			{ 
				$filename = time().".".$request->file('image')->getClientOriginalExtension(); 
				
				if (!is_dir(base_path('uploads'). '/profileImages')) 
				{
					mkdir(base_path('uploads'). '/profileImages', 0777, true); 
				}
				$targetPath = base_path('uploads'). '/profileImages';
				$targetfilePath = $targetPath.'/'.$filename; 				
				$isUploaded = $request->file('image')->move($targetPath,$filename);
				if($isUploaded)
				{
					//--------------------------------
					if (!is_dir(base_path('uploads'). '/profileImages/thumbnail')) 
				{
					mkdir(base_path('uploads'). '/profileImages/thumbnail', 0777, true); 
				}

					$targetPath_thumb = base_path('uploads/profileImages'). '/thumbnail'; 
					$targetfilePath_thumb = $targetPath_thumb.'/'.$filename; 
					list($width,$height) = getimagesize($targetfilePath);
			        $thumb_width = 200;
					$thumb_height = 160;
					$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
					switch($extensionofFile)
					{
						case 'jpg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'jpeg':
						$source = imagecreatefromjpeg($targetfilePath);
						break;
						case 'png':
						$source = imagecreatefrompng($targetfilePath);
						break;
						case 'gif':
						$source = imagecreatefromgif($targetfilePath);
						break;
						default:
						$source = imagecreatefromjpeg($targetfilePath);
					}
					imagecopyresized($thumb_create,$source,0,0,0,0,$thumb_width,$thumb_height,$width,$height);
					switch($extensionofFile)
					{
						case 'jpg' || 'jpeg':
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'png':
						imagepng($thumb_create,$targetfilePath_thumb,100);
						break;
						case 'gif':
						imagegif($thumb_create,$targetfilePath_thumb,100);
						break;
						default:
						imagejpeg($thumb_create,$targetfilePath_thumb,100);
					}
					return $filename;
				}
			}
			else 
			{
				Session::flash('msg',GStarBaseController::MSG_FILE_SIZE);
			}
		}
		else
		{
			Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
		}
		
	}
*/
// Function to Edit User ------	
 public static function editUserFromInput($request,$userID) 
    { 
        $rules = array(
            'sp_id'    => 'required', // first_name is required
            'first_name'    => 'required', // first_name is required
            'last_name' => 'required', // last_name is required
			'role'=> 'required',//role is required
            'contact'=> 'required',//contact is required
            'status' => 'required', // status is required
            'gender'=> 'required',//gender is required
            'dob'=> 'required'//dob is required
            //'city'=> 'required',//city is required
            //'state'=> 'required',//state is required
            //'zone'=> 'required',//zone is required
            //'beat_route_id'=> 'required',//beat_route_id is required
           // 'rt_code'=> 'required',//rt_code is required
            //'nd_name'=> 'required',//nd_name is required
            //'rd_name'=> 'required'//rd_name is required
        );
        $validator = Validator::make($request->input(), $rules);
        $messages = $validator->errors();
        Session::forget('msg');
        $user = new User();
        $updateUser = array();
        $updates = array();
        $userDetail = array();
        $sp_id = array();
        if(!$messages->first('first_name')) {
            $updateUser['first_name'] = trim($request->input('first_name'));
            } 
        else {
            Session::flash('msg', $messages->first('first_name'));
        } 
        if(!$messages->first('last_name')) {
            $updateUser['last_name'] = trim($request->input('last_name'));
            } 
        else {
            Session::flash('msg', $messages->first('last_name'));
        } 
       
      if(!$messages->first('role')) {
            $updateUser['role'] = trim($request->input('role'));
            } 
        else {
            Session::flash('msg', $messages->first('role'));
        } 
        if(!$messages->first('contact')) {
            $updateUser['contact'] = trim($request->input('contact'));
            } 
        else {
            Session::flash('msg', $messages->first('contact'));
        } 
        if(!$messages->first('status')) {
            $updateUser['status'] = trim($request->input('status'));
            } 
        else {
            Session::flash('msg', $messages->first('status'));
        } 
        if(!$messages->first('gender')) {
            $updateUser['gender'] = trim($request->input('gender'));
            } 
        else {
            Session::flash('msg', $messages->first('gender'));
        } 
        if(!$messages->first('dob')) {
			 $updateUser['dob'] = date('y-m-d',strtotime(trim($request->input('dob'))));
            } 
        else {
            Session::flash('msg', $messages->first('dob'));
        } 
		
		  if(!$messages->first('sp_id')) {
            $sp_id['parent_id'] = trim($request->input('sp_id'));
            } 
        else {
            Session::flash('msg', $messages->first('sp_id'));
        }
		if(Input::hasFile('image')){ 
			
			$updateUser['profile_picture'] = $user->uploadImage($request);
		}
        //-----------------------------------------------------------------------------------------
		
		if(!$messages->first('city')) {
            $userDetail['city'] = trim($request->input('city'));
            } 
        else {
            Session::flash('msg', $messages->first('city'));
        }
		if(!$messages->first('state')) {
            $userDetail['state'] = trim($request->input('state'));
            } 
        else {
            Session::flash('msg', $messages->first('state'));
        }
		if(!$messages->first('zone')) {
            $userDetail['zone'] = trim($request->input('zone'));
            } 
        else {
            Session::flash('msg', $messages->first('zone'));
        }
		if(!$messages->first('beat_route_id')) {
            $userDetail['beat_route_id'] = trim($request->input('beat_route_id'));
            } 
        else {
            Session::flash('msg', $messages->first('beat_route_id'));
        }
		if(!$messages->first('rt_code')) {
            $userDetail['rt_code'] = trim($request->input('rt_code'));
            } 
        else {
            Session::flash('msg', $messages->first('rt_code'));
        }
		if(!$messages->first('nd_name')) {
            $userDetail['nd_name'] = trim($request->input('nd_name'));
            } 
        else {
            Session::flash('msg', $messages->first('nd_name'));
        }
		if(!$messages->first('rd_name')) {
            $userDetail['rd_name'] = trim($request->input('rd_name'));
            } 
        else {
            Session::flash('msg', $messages->first('rd_name'));
        }
		if(!Session::has('msg'))
		{
			$updateUserRecord = ''; $updateUserDetailRecord = ''; $updateUserMappingRecord = '';
			$updateUserRecord = DB::table('users')->where('id', $userID)->update($updateUser); 
			$updateUserDetailRecord = DB::table('user_detail')->where('user_id', $userID)->update($userDetail);
			$updateUserMappingRecord = DB::table('user_mapping')->where('user_id', $userID)->update($sp_id);
			   
			if($updateUserRecord == 1 || $updateUserDetailRecord == 1 || $updateUserMappingRecord == 1 )
			{
			    $updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED;
		    }
			else
			{
				Session::flash('msg', GStarBaseController::MSG_NO_UPDATE);
			}
		
		}
		
        return $updates;
        //-----------------------------------------------------------------------------------------
        
    }
	
	// Funtion to Edit App user---------------
 public static function editAppUserFromInput($request,$userID) 
 { 
        
        $rules = array(
            'first_name'    => 'required', // first_name is required
            'last_name' => 'required', // last_name is required
            'email'=> 'required',//email is required
            'contact'=>'required', //contact is required
        );
        $validator = Validator::make($request->input(), $rules);
        $messages = $validator->errors();
      //  Session::forget('msg');
        $user = new User();
        $updateUser = array();
        $userDetail = array();
        $updates = array();
        if(!$messages->first('first_name')) {
            $updateUser['first_name'] = trim($request->input('first_name'));
        }else {
            Session::flash('msg', $messages->first('first_name'));
            return;
        } 
        if(!$messages->first('last_name')) {
            $updateUser['last_name'] = trim($request->input('last_name'));
            } 
        else {
            Session::flash('msg', $messages->first('last_name'));
            return;
        } 

         if(!$messages->first('contact')) {
            $updateUser['contact'] = trim($request->input('contact'));
            } 
        else {
            Session::flash('msg', $messages->first('contact'));
            return;
        } 
       
        if(!$messages->first('email')) {
        	$email=trim($request->input('email'));
        	$getemail=DB::table('users')->where('email', $email)->where('id','!=',$userID)->first();
        	if($getemail){
        		Session::flash('msg', GStarBaseController::MSG_EMAIL_EXIST);
        		return;
        	}else{
        		$getemail=DB::table('users_dummy')->where('email', $email)->where('is_deleted',0)->where('user_id','!=',$userID)->first();
        		if($getemail){
        			Session::flash('msg',GStarBaseController::MSG_EMAIL_EXIST);
        			return;
        		}else{
        			$updateUser['email'] = $email;
        		}
        	}
        }else {
            Session::flash('msg', $messages->first('email'));
            return;
        } 

        if(trim($request->input('city'))){
        	$updateUser['city'] = trim($request->input('city'));
        }else{
        	$updateUser['city'] = '';
        }

         if(trim($request->input('state'))){
        	$updateUser['state'] = trim($request->input('state'));
        }else{
        	$updateUser['state'] = '';
        }

        if(trim($request->input('zone'))){
        	$updateUser['zone'] = trim($request->input('zone'));
        }else{
        	$updateUser['zone'] = '';
        }

        if(trim($request->input('nd_name'))){
        	$updateUser['nd_name'] = trim($request->input('nd_name'));
        }else{
        	$updateUser['nd_name'] = '';
        }

        if(trim($request->input('rd_name'))){
        	$updateUser['rd_name'] = trim($request->input('rd_name'));
        }else{
        	$updateUser['rd_name'] = '';
        }
		
		if(Input::hasFile('image')){ 
			$updateUser['profile_picture'] = $user->uploadImage($request);
		}
		// else{
		// 	$profile_pic=trim($request->input('profile_picture'));
		// 	if($profile_pic){
		// 		$updateUser['profile_picture'] = $profile_pic;
		// 	}else{
		// 		$updateUser['profile_picture'] = '';
		// 	}
			
		//}
        //-----------------------------------------------------------------------------------------
		
		//print_r($updateUser);die;

	if(!Session::has('msg')){
			$userRole=DB::table('users')->select('role')->where('id',$userID)->first();
			$updateUser['role']=$userRole->role;
			//print_r($updateUser);die;

			if($userRole->role<GStarBaseController::G_TRAINER_ROLE_ID){
				if(array_key_exists('profile_picture', $updateUser)){
					$updateAdmin=array('first_name'=>$updateUser['first_name'],'last_name'=>$updateUser['last_name'],'email'=>$updateUser['email'],'contact'=>$updateUser['contact'],'profile_picture'=>$updateUser['profile_picture']);
				}else{
					$updateAdmin=array('first_name'=>$updateUser['first_name'],'last_name'=>$updateUser['last_name'],'email'=>$updateUser['email'],'contact'=>$updateUser['contact']);
				}
				// $updateAdmin=array('first_name'=>$updateUser['first_name'],'last_name'=>$updateUser['last_name'],'email'=>$updateUser['email'],'contact'=>$updateUser['contact'],'profile_picture'=>$updateUser['profile_picture']);
				
				$updateUserAdmin=array('city'=>$updateUser['city'],'state'=>$updateUser['state'],'zone'=>$updateUser['zone'],'nd_name'=>$updateUser['nd_name'],'rd_name'=>$updateUser['rd_name']);

				$updateUserAdminRecord=DB::table('users')->where('id',$userID)->update($updateAdmin);

				$updateUserAdminDetail=DB::table('user_detail')->where('user_id',$userID)->update($updateUserAdmin);

				if($updateUserAdminRecord){
					$updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED;
					// if($updateUserAdminDetail){
						
					// }else{
					// 	$updates['msg'] = GStarBaseController::MSG_NO_UPDATE2;
					// }
				}else{
					if($updateUserAdminDetail){
						$updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED;
					}else{
						$updates['msg'] = GStarBaseController::MSG_NO_UPDATE2;
					}
				}
			}else{

				// print_r($updateUser);
				// die;
				$updateUserRecord = '';
				$user=DB::table('users_dummy')->where('user_id',$userID)->where('approved_status','=','0')->first();
				
				if(!empty($user)){

					if($user->approved_status=='0'){
					$updateUser['is_deleted']=0;
					$updateUserRecord=DB::table('users_dummy')->where('user_id',$userID)->where('approved_status','=','0')->update($updateUser);
					}else{
						$updateUser['user_id'] = $userID; 
				    	$updateUserRecord = DB::table('users_dummy')->insert($updateUser); 
					}
					if($updateUserRecord == 1){

					$updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE;
				     }
						else{
						Session::flash('msg', GStarBaseController::MSG_NO_UPDATE2);
					}
				}else{
						$updateUser['user_id'] = $userID; 
				    	$updateUserRecord = DB::table('users_dummy')->insert($updateUser); 
				    	if($updateUserRecord == 1){
						$updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE;
					     }else{
							Session::flash('msg', GStarBaseController::MSG_NO_UPDATE2);
						}
				}

				if($updates){
					// Send Push to Edit User Supervisor
					$editUserRole=$updateUser['role'];
					if($editUserRole == GStarBaseController::G_LEARNER_ROLE_ID){
						$Supervisor_Id=DB::table('user_mapping')->select('parent_id')->where('user_id',$userID)->first();
						if($Supervisor_Id){
							$Device_Token=DB::table('device')->where('user_id',$Supervisor_Id->parent_id)->where('login_status',1)->first();
							if($Device_Token){
								//Send Push
								$DeviceDetail['device_token']=array($Device_Token->device_token);
								$DeviceDetail['user'][0]['user_id']=$Device_Token->user_id;
								$DeviceDetail['user'][0]['user_role']=$Device_Token->user_role;
								$body=GStarBaseController::EditUserProfileUrl();
								// print_r($DeviceDetail);
								// print_r($body);die;
								$data=NewsUpdate::PUSH_nofity_Android($DeviceDetail,$body,1);
							}
						}
					}
				}

				
			}
			
		
		}		
        return $updates;
        //-----------------------------------------------------------------------------------------
        
    }
	// Function to get list of users----------------------
	public static function getListOfAllUsers($userType,$search_keyword,$pageNo,$getAll)
	{
		$userDetail_array = array();
		$nameArr = array();
		$inputs = Input::get();
		if($pageNo!= null) 
        {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		} else {
			$offset= 0;
		}
		
		if($userType == 'all')
		{
			if($search_keyword!="")
			{	
				if(strpos($search_keyword, " ") != false)
                {	
					$nameArr = explode(' ',$search_keyword);
					$sql=DB::table('users');
                    $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                    $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');
                    $sql->where(function ($query) use ($nameArr)
				   	{
					   	$query->where('users.first_name','like','%'.$nameArr[0].'%');
						$query->orWhere('users.last_name','like','%'.$nameArr[1].'%');
						$query->orWhere('users.email','like','%'.$nameArr[0].'%');
			        });
					
                    $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                    $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
                    $sql->orderBy('users.created_at', 'desc');
		            if(isset($inputs['role'])){
					  	$sql->where('users.role',trim($inputs['role']));
					}
					$archive_date=strtotime("-90 days");  	
					$sql->where(function ($query) use ($archive_date)
				   	{
				   		$query->orWhere('users.last_login','>=',$archive_date);
				   		$query->orWhere(function ($query2)
				   		{
					   		$created_date=date('Y-m-d',strtotime("-90 days"));
					   		$current_date=date('Y-m-d',strtotime("now"));
					   		$query2->whereBetween('created_at',array($created_date.' 00:00:00',$current_date.' 23:59:59'));
			   				$query2->whereNull('users.last_login');
			            });
				   			
			        });
			        if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			        }
                    
				 } else {
					$sql=DB::table('users');
                    $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                    $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');
                    $sql->where(function ($query) use ($search_keyword)
				   	{
					   	$query->where('users.first_name','like','%'.$search_keyword.'%');
					  	$query->orWhere('users.email','like','%'.$search_keyword.'%');
			        });
                    $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                    $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
                    $sql->orderBy('users.created_at', 'desc');
		            if(isset($inputs['role'])){
					  	$sql->where('users.role',trim($inputs['role']));
					}  	
					$archive_date=strtotime("-90 days");  	
					$sql->where(function ($query) use ($archive_date)
				   	{
				   		$query->orWhere('users.last_login','>=',$archive_date);
				   		$query->orWhere(function ($query2)
				   		{
					   		$created_date=date('Y-m-d',strtotime("-90 days"));
					   		$current_date=date('Y-m-d',strtotime("now"));
					   		$query2->whereBetween('created_at',array($created_date.' 00:00:00',$current_date.' 23:59:59'));
			   				$query2->whereNull('users.last_login');
			            });	
			        });
                    if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			        }
				 }
			} else {
			    $sql=DB::table('users');
                $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');
                $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
                $sql->orderBy('users.created_at', 'desc');
                if(isset($inputs['role'])){
			  				$sql->where('users.role',trim($inputs['role']));
			  	}
			  	
			  	$archive_date=strtotime("-90 days");
			  	
			  	$sql->where(function ($query) use ($archive_date)
		   		{
		   			$query->orWhere('users.last_login','>=',$archive_date);
		   			$query->orWhere(function ($query2)
		   			{
			   			$created_date=date('Y-m-d',strtotime("-90 days"));
			   			$current_date=date('Y-m-d',strtotime("now"));
			   			$query2->whereBetween('created_at',array($created_date.' 00:00:00',$current_date.' 23:59:59'));
			   			$query2->whereNull('users.last_login');
	            	});
		   			
	            });
                if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			    }
	       }
	       $userDetails=$sql->get();

            $j=0;
            
            foreach ($userDetails as $value)
            {
                $parentid[$j]=$value->parent_id;
                $sp_name[$j]= DB::table('users')->select('first_name','last_name')->where(array('users.id'=>$value->parent_id,'users.status'=>1))->first();
                $spname[$j]=$value->first_name." ".$value->last_name;
                $j++;
            }
            $j=0;
            $loginUserRole=Auth::user()->role;
            $loginUserId=Auth::user()->id;
            foreach ($userDetails as $value)
            {
                if(!empty($userDetails) && !empty($spname) && $value->role>$loginUserRole)
                { 
                    if($loginUserRole == GStarBaseController::G_TRAINER_ROLE_ID){
                    	$loginUserId=Auth::user()->id;
                    	$sp_id2=null;
						$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$value->id)->first();
						
						if($sp_id->parent_id ==$loginUserId)
						{
								$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
						} else {
							if($sp_id){
							$sp_id2=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
							}
							if($sp_id2)
							{
								if($sp_id2->parent_id ==$loginUserId)
								{
									$userDetail_array[$j]['id'] = $value->id;
				                    $userDetail_array[$j]['first_name'] = $value->first_name;
				                    $userDetail_array[$j]['last_name'] = $value->last_name;
				                    $userDetail_array[$j]['email'] = $value->email;
				                    $userDetail_array[$j]['role'] = $value->role;
				                    $userDetail_array[$j]['contact'] = $value->contact;
				                    $userDetail_array[$j]['status'] = $value->status;
				                    $userDetail_array[$j]['gender'] = $value->gender;
				                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
				                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
				                    $userDetail_array[$j]['city'] = $value->city;
				                    $userDetail_array[$j]['state'] = $value->state;
				                    $userDetail_array[$j]['zone'] = $value->zone;
				                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
				                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
				                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
				                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
				                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
				                    $userDetail_array[$j]['sp_name'] = $spname[$j];
				                    $j++;
								}
							}		

						}

                    }else if($loginUserRole == GStarBaseController::G_SUPERVISOR_ROLE_ID){
                    		$loginUserId=Auth::user()->id;
                    		$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$value->id)->first();
						
						if($sp_id->parent_id ==$loginUserId)
						{
								$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
						}
                    } else {
                    	$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
                    }
                    
                }
            }
		}


		if($userType == 'supervisors'){
		$userDetail_array = DB::table('users')->where('status',1)->where('role','!=',GStarBaseController::G_LEARNER_ROLE_ID)
		->select('id','first_name','last_name','role')->get();	
		}
		
		return $userDetail_array;
	}
	
	
	

    // Function to get list of archive users----------------------
	public static function ArchiveUserList($userType,$search_keyword,$pageNo,$getAll)
	{
		$userDetail_array = array();
		$nameArr = array();
		$inputs = Input::get();
		if($pageNo!= null) 
        {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		} else {
			$offset= 0;
		}
		
		if($userType == 'all')
		{
			if($search_keyword!="")
			{	
				if(strpos($search_keyword, " ") != false)
                {	
					$nameArr = explode(' ',$search_keyword);
					$sql=DB::table('users');
                    $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                    $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');

                    $sql->where(function ($query) use ($nameArr)
				   			{
					   			$query->where('users.first_name','like','%'.$nameArr[0].'%');
								$query->orWhere('users.last_name','like','%'.$nameArr[1].'%');
								$query->orWhere('users.email','like','%'.$nameArr[0].'%');
			            	});

					
                    $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                    $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
                    $sql->orderBy('users.created_at', 'desc');
		                if(isset($inputs['role'])){
					  				$sql->where('users.role',trim($inputs['role']));
					  			}
					  	
					  	
					  	$sql->where(function ($query)
				   		{
				   			$archive_date=strtotime("-90 days");
						  	$query->where(function ($query2) use ($archive_date)
					   		{
					   			$query2->where('users.last_login','<',$archive_date);
					   			$query2->whereNotNull('users.last_login');
					   			
				            });


				            $query->orWhere(function ($query3)
				   			{
					   			$created_date=date('Y-m-d',strtotime("-90 days"));
					   			$query3->where('created_at','<',$created_date.' 00:00:00');
					   			$query3->whereNull('users.last_login');
					   			
			            	});	

			            });		
                    if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			        }
				 } else {
					  $sql=DB::table('users');
                      $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                      $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');

                      $sql->where(function ($query) use ($search_keyword)
				   			{
					   			$query->where('users.first_name','like','%'.$search_keyword.'%');
					  			$query->orWhere('users.email','like','%'.$search_keyword.'%');
			            	});

					  
                      $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                       $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);

                       $sql->orderBy('users.created_at', 'desc');
		                if(isset($inputs['role'])){
					  				$sql->where('users.role',trim($inputs['role']));
					  			}
					  	
					  	$sql->where(function ($query)
				   		{
				   			$archive_date=strtotime("-90 days");
						  	$query->where(function ($query2) use ($archive_date)
					   		{
					   			$query2->where('users.last_login','<',$archive_date);
					   			$query2->whereNotNull('users.last_login');
					   			
				            });


				            $query->orWhere(function ($query3)
				   			{
					   			$created_date=date('Y-m-d',strtotime("-90 days"));
					   			$query3->where('created_at','<',$created_date.' 00:00:00');
					   			$query3->whereNull('users.last_login');
					   			
			            	});	

			            });	

                       if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			        }
				 }
			} else {
			    $sql=DB::table('users');
                $sql->join('user_detail', 'users.id','=','user_detail.user_id');
                $sql->join('user_mapping', 'user_mapping.user_id','=','users.id');
                $sql->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
                $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
                $sql->orderBy('users.created_at', 'desc');
                if(isset($inputs['role'])){
			  				$sql->where('users.role',trim($inputs['role']));
			  			}

			  	$sql->where(function ($query)
		   		{
		   			$archive_date=strtotime("-90 days");
				  	$query->where(function ($query2) use ($archive_date)
			   		{
			   			$query2->where('users.last_login','<',$archive_date);
			   			$query2->whereNotNull('users.last_login');
			   			
		            });


		            $query->orWhere(function ($query3)
		   			{
			   			$created_date=date('Y-m-d',strtotime("-90 days"));
			   			$query3->where('created_at','<',$created_date.' 00:00:00');
			   			$query3->whereNull('users.last_login');
			   			
	            	});	

	            });	

                if($getAll !=1){
			        	$sql->skip($offset);
                    	$sql->take(GStarBaseController::PAGINATION_LIMITS);
			        }
	       }
	       $userDetails=$sql->get();

            $j=0;
            
            foreach ($userDetails as $value)
            {
                $parentid[$j]=$value->parent_id;
                $sp_name[$j]= DB::table('users')->select('first_name','last_name')->where(array('users.id'=>$value->parent_id,'users.status'=>1))->first();
                $spname[$j]=$value->first_name." ".$value->last_name;
                $j++;
            }
            $j=0;
            $loginUserRole=Auth::user()->role;
            $loginUserId=Auth::user()->id;
            foreach ($userDetails as $value)
            {
                if(!empty($userDetails) && !empty($spname) && $value->role>$loginUserRole)
                { 
                    if($loginUserRole == GStarBaseController::G_TRAINER_ROLE_ID){
                    	$loginUserId=Auth::user()->id;
                    	$sp_id2=null;
						$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$value->id)->first();
						
						if($sp_id->parent_id ==$loginUserId)
						{
								$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
						} else {
							if($sp_id){
							$sp_id2=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
							}
							if($sp_id2)
							{
								if($sp_id2->parent_id ==$loginUserId)
								{
									$userDetail_array[$j]['id'] = $value->id;
				                    $userDetail_array[$j]['first_name'] = $value->first_name;
				                    $userDetail_array[$j]['last_name'] = $value->last_name;
				                    $userDetail_array[$j]['email'] = $value->email;
				                    $userDetail_array[$j]['role'] = $value->role;
				                    $userDetail_array[$j]['contact'] = $value->contact;
				                    $userDetail_array[$j]['status'] = $value->status;
				                    $userDetail_array[$j]['gender'] = $value->gender;
				                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
				                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
				                    $userDetail_array[$j]['city'] = $value->city;
				                    $userDetail_array[$j]['state'] = $value->state;
				                    $userDetail_array[$j]['zone'] = $value->zone;
				                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
				                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
				                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
				                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
				                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
				                    $userDetail_array[$j]['sp_name'] = $spname[$j];
				                    $j++;
								}
							}		

						}

                    }else if($loginUserRole == GStarBaseController::G_SUPERVISOR_ROLE_ID){
                    		$loginUserId=Auth::user()->id;
                    		$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$value->id)->first();
						
						if($sp_id->parent_id ==$loginUserId)
						{
								$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
						}
                    } else {
                    	$userDetail_array[$j]['id'] = $value->id;
	                    $userDetail_array[$j]['first_name'] = $value->first_name;
	                    $userDetail_array[$j]['last_name'] = $value->last_name;
	                    $userDetail_array[$j]['email'] = $value->email;
	                    $userDetail_array[$j]['role'] = $value->role;
	                    $userDetail_array[$j]['contact'] = $value->contact;
	                    $userDetail_array[$j]['status'] = $value->status;
	                    $userDetail_array[$j]['gender'] = $value->gender;
	                    $userDetail_array[$j]['dob'] = date("d-m-Y", strtotime( $value->dob ) );
	                    $userDetail_array[$j]['profile_picture'] = $value->profile_picture;
	                    $userDetail_array[$j]['city'] = $value->city;
	                    $userDetail_array[$j]['state'] = $value->state;
	                    $userDetail_array[$j]['zone'] = $value->zone;
	                    $userDetail_array[$j]['beat_route_id'] = $value->beat_route_id;
	                    $userDetail_array[$j]['rt_code'] = $value->rt_code;
	                    $userDetail_array[$j]['nd_name'] = $value->nd_name;
	                    $userDetail_array[$j]['rd_name'] = $value->rd_name;
	                    $userDetail_array[$j]['sp_id'] = $value->parent_id;
	                    $userDetail_array[$j]['sp_name'] = $spname[$j];
	                    $j++;
                    }
                    
                }
            }
		}


		if($userType == 'supervisors'){
		$userDetail_array = DB::table('users')->where('status',1)->where('role','!=',GStarBaseController::G_LEARNER_ROLE_ID)
		->select('id','first_name','last_name','role')->get();	
		}
		
		return $userDetail_array;
	}
	
	// Function to forgetPassword------------------
	 public static function forgetPassword($input){
        Session::forget('msg');
        $user = new User();
        if($input){
            $userEmail = trim($input['email']);
            $userDetails = DB::table('users')->select('id')->where(array('email'=>$userEmail,'status'=>'1'))->where('role','<',GStarBaseController::G_LEARNER_ROLE_ID)->first();
            if($userDetails){
                $user->emailID = $userEmail;
                $user->fp_id = $userDetails->id;
            }else{
                Session::flash('msg', GStarBaseController::MSG_EMAIL_NOT_REGIS);
                }
        }else{
              Session::flash('msg', GStarBaseController::MSG_EMAIL_ENTER_REGIS);
           }
        
        return $user;
    }
	
	// Function to changePassword------------------
	 public static function Changepassword($input){
        Session::forget('msg');
        $user = new User();
        if($input)
        {
           $oldpassword = trim($input['oldpassword']);
            $newpassword = trim($input['newpassword']);
            $confirmpassword = trim($input['confirmpassword']);
            $id = trim($input['id']);
			
            $userDetails = DB::table('users')->select('password')->where(array('id'=>$id,'status'=>'1'))->first();
            if(Hash::check($oldpassword,$userDetails->password))
            {
                if($newpassword===$confirmpassword)
                {
                    $user->password = $newpassword;
                     $user->password;
                }
                else
                {
                   Session::flash('msg', GStarBaseController::MSG_NEWPSWD_NOT_MATCH);
                } 
            }
                
            else{
                Session::flash('msg', GStarBaseController::MSG_PSWD_NOT_MATCH);
                }
        }
        else{
              Session::flash('msg', GStarBaseController::MSG_BAD_PARAMETERS);
           }
        
        return $user;
    }

// Function to reset Password by Admin------------------
	 public static function resetPasswordByAdmin($input){
        Session::forget('msg');
        $user = new User();
        if($input)
        {
           
            $newpassword = trim($input['newpassword']);
            $confirmpassword = trim($input['confirmpassword']);
            $id = trim($input['id']);
			
            $userDetails = DB::table('users')->select('password')->where(array('id'=>$id,'status'=>'1'))->first();
             if($newpassword===$confirmpassword)
                {
                    $user->password = $newpassword;
                     //$user->password;
                }
                else
                {
                   Session::flash('msg', GStarBaseController::MSG_NEWPSWD_NOT_MATCH);
                } 
           
        }
        else{
              Session::flash('msg', GStarBaseController::MSG_BAD_PARAMETERS);
           }
        
        return $user;
    }

// Function to Import User File------------------

	 public static function importUserFile($request) 
    { 
        $filename='';
        $supervisorsMsg=null;
        $type = array('xlsx','xls');
        $user = new User();
        $userFile = array();
        $userData =  array();   
        //$userDetail = new User();
        Session::forget('msg');
        if(Input::hasFile('user_file'))
        { 
            ini_set('max_execution_time','3600');
	    	ini_set('memory_limit','550M');
	    	$fileSize = $request->file('user_file')->getSize();
            $originalExtension = $request->file('user_file')->getClientOriginalExtension();
            if(in_array($originalExtension,$type))
            {   
                if($fileSize <= GStarBaseController::FILE_SIZE)
                {
                    $count=0;
                    $totalRecords = count($userFile);
                    $filename =  time().".".$originalExtension ;
					if (!is_dir(base_path('uploads'). '/importuser')) {
						mkdir(base_path('uploads'). '/importuser', 0777, true); 
					}
					$targetPath = base_path('uploads'). '/importuser'; 
                    $isUploaded = $request->file('user_file')->move($targetPath ,$filename);
                    if($isUploaded)
                    {
                    	$uploadedPath = $isUploaded->getpathName();
	                    $fileFullPath = $targetPath."/".$filename;
						if(Excel::load($fileFullPath))
						{
			                $userFile = Excel::load($fileFullPath)->toArray();
			                //print_r($userFile); die;
		                     $totalRecords = count($userFile);		

		                    foreach ($userFile as $key => $row) 
		                    { 
		                        if(!empty($row['email']) && !empty($row['fname']) && !empty($row['lname']) && !empty($row['contact']) && !empty($row['role'])&& !empty($row['gender'])&&  !empty($row['sp_id']))
		                        {
									$userData[$count]['email'] = trim($row['email']);                            
									$userData[$count]['fname'] = trim($row['fname']);$userData[$count]['lname'] = trim($row['lname']);
									$userData[$count]['contact'] = trim($row['contact']);
									if(empty($row['password'])){
										$pass = 12345;	
									}else{
										$pass = trim($row['password']);
									}
									$userData[$count]['password'] = $pass;
									$userData[$count]['role'] = trim($row['role']);$userData[$count]['gender'] = trim($row['gender']);
									$userData[$count]['dob'] = trim($row['dob']);$userData[$count]['city'] = trim($row['city']);
									$userData[$count]['state'] = trim($row['state']);$userData[$count]['zone'] = trim($row['zone']);
									$userData[$count]['beat_route_id'] = trim($row['beat_route_id']);
									$userData[$count]['rt_code'] = trim($row['rt_code']);$userData[$count]['nd_name'] = trim($row['nd_name']);
									$userData[$count]['rd_name'] = trim($row['rd_name']);$userData[$count]['sp_id'] = trim($row['sp_id']);
									if(!empty($row['status'])) {
										$userData[$count]['status'] = trim($row['status']);
									}else{
										$userData[$count]['status'] = 0;
									} 
		       
		                        }else{
		                        	if(empty($row['fname'])) {
		                                Session::flash('msg', 'First Name field is empty');
		                                return;
		                            }
		                            if(empty($row['lname'])) {
		                                Session::flash('msg', 'Last Name field is empty');
		                                return;
		                            }
		                            if(empty($row['contact'])) {
		                                Session::flash('msg', 'Contact field is empty');
		                                return;
		                            }
		                            if(empty($row['gender'])) {
		                                Session::flash('msg', 'Gender field is empty');
		                                return;
		                            }
									if(empty($row['email'])) {
		                                Session::flash('msg', 'Email field is empty');
		                                return;
		                            }
		                          	if(empty($row['role'])) {
		                                Session::flash('msg', 'Role field is empty');
		                                return;
		                            }
		                            if(empty($row['status'])) {
		                                Session::flash('msg', 'Status field is empty');
		                                return;
		                            }
		                            if(empty($row['sp_id'])) {
		                                Session::flash('msg', 'Supervisor ID is empty');
		                                return;
		                            }
								}// empty condition end
		                            $count++;
		                    } // Foreach Loop End
		                } //load excel file	
		            }//Is uploaded End
                    if($count != $totalRecords )
                    {
                        Session::flash('msg', GStarBaseController::MSG_EMPTY_ROW);
                    }else{
                         	return $userData;
                    }    
                }else{
                    Session::flash('msg', GStarBaseController::MSG_WRONG_FILE_SIZE);
                }
            }else{
                Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
            }
        }else{
            Session::flash('msg', GStarBaseController::MSG_NOT_VALID_FILE);
        }

		
     return $userData;       
    }


// public static function importUserFile($request) 
//     { 
//         $filename='';
//         $type = array('xlsx','xls');
//         $user = new User();
//         $userFile = array();
//         $userData =  array();   
//         //$userDetail = new User();
//         Session::forget('msg');

//          $fileSize = $request->file('user_file')->getSize();
               
//         if(Input::hasFile('user_file'))
//         { 
        	
//             //ini_set('max_execution_time','3600');
// 	    	//ini_set('memory_limit','550M');
// 	    $fileSize = $request->file('user_file')->getSize();
//             $originalExtension = $request->file('user_file')->getClientOriginalExtension();
//             //print_r($originalExtension); exit;
//             if(in_array($originalExtension,$type))
//             {   
//                 if($fileSize <= GStarBaseController::FILE_SIZE)
//                 { 
//                 	 //$userFile = Excel::load($request->file('user_file'))->toArray();
//               //   	  Excel::load(Input::file('user_file'), function ($reader) {

// 		            //     foreach ($reader->toArray() as $row) {
// 		            //         print_r($row);
// 		            //     }
// 		            // });
// 		   //          Excel::load($request->file('user_file'), function($reader) {

// 					// })->get();
// 					$results=array();
// 					Excel::load($request->file('user_file'), function($reader) {

// 					    // Getting all results
// 					    $results = $reader->get();
// 					    print_r($results);

// 					    // ->all() is a wrapper for ->get() and will work the same
// 					   // $results = $reader->all();

// 					});

//                      exit;
//                     $count=0;
//                     //$totalRecords = Excel::load($request->file('user_file'))->get()->count();
//                     $totalRecords = count($userFile);
//                     $filename =  time().".".$originalExtension ;
//                     if (!is_dir(base_path('uploads'). '/importuser')) 
// 					{
// 						mkdir(base_path('uploads'). '/importuser', 0777, true); 
// 					}
// 					$targetPath = base_path('uploads'). '/importuser'; 
//                     $isUploaded = $request->file('user_file')->move($targetPath ,$filename);
//                     if($isUploaded)
//                     {
//                     	$uploadedPath = $isUploaded->getpathName();
// 	                    $fileFullPath = $targetPath."/".$filename;
	
//                      foreach ($userFile as $key => $row) 
//                      { 


//                            if(!empty($row['email']) && !empty($row['fname']) && !empty($row['lname']) && !empty($row['contact']) && !empty($row['role'])&& !empty($row['gender'])&&  !empty($row['sp_id']))
//                                     {
// 										$userData[$count]['email'] = trim($row['email']);                            
// 										$userData[$count]['fname'] = trim($row['fname']);
// 										$userData[$count]['lname'] = trim($row['lname']);
// 										$userData[$count]['contact'] = trim($row['contact']);
// 										if(empty($row['password'])){
// 										$pass = 12345;	
// 										}
// 										else{
// 										$pass = trim($row['password']);
// 										}
// 										$userData[$count]['password'] = $pass ;
// 										$userData[$count]['role'] = trim($row['role']);
// 										$userData[$count]['gender'] = trim($row['gender']);
// 										$userData[$count]['dob'] = trim($row['dob']);
// 										$userData[$count]['city'] = trim($row['city']);
// 										$userData[$count]['state'] = trim($row['state']);
// 										$userData[$count]['zone'] = trim($row['zone']);
// 										$userData[$count]['beat_route_id'] = trim($row['beat_route_id']);
// 										$userData[$count]['rt_code'] = trim($row['rt_code']);
// 										$userData[$count]['nd_name'] = trim($row['nd_name']);
// 										$userData[$count]['rd_name'] = trim($row['rd_name']);
// 										$userData[$count]['sp_id'] = trim($row['sp_id']);
// 										if(!empty($row['status'])) {
// 											$userData[$count]['status'] = trim($row['status']);
// 										}
// 										else {
// 											$userData[$count]['status'] = 0;
// 										} 
       
//                                     }
//                                     else
//                                     {
// 										if(empty($row['email'])) {
//                                                 Session::flash('msg', 'Email field is empty');
//                                             }
//                                             if(empty($row['role'])) {
//                                                 Session::flash('msg', 'Role field is empty');
//                                             }
//                                             if(empty($row['status'])) {
//                                                 Session::flash('msg', 'Status field is empty');
//                                             }
//                                             if(empty($row['sp_id'])) {
//                                                 Session::flash('msg', 'Supervisor ID is empty');
//                                             }
// 											// if(empty($row['password'])) {
//                                                 // Session::flash('msg', 'Password is empty');
//                                             // }
//                                     }// empty condition end
//                             $count++;
//                         	} // Foreach Look End
//                            // } //load excel file	
//                        	}//Is uploaded End

//                         if($count != $totalRecords )
//                         {
//                             Session::flash('msg', GStarBaseController::MSG_EMPTY_ROW);
//                         }
//                         else
//                          {
//                          	return $userData;
//                          }    
//                      }
//                      else 
//                      {
//                             Session::flash('msg', GStarBaseController::MSG_WRONG_FILE_SIZE);
//                      }
//                 }
//                 else
//                 {
//                      Session::flash('msg', GStarBaseController::MSG_FILE_FORMAT);
//                 }
//              }
//              else
//              {
//                  Session::flash('msg', GStarBaseController::MSG_NOT_VALID_FILE);
//              }
//         return $userData;
           
//          }
// Function to App Update service-------

	
	public static function appUpdates($request){
	$Current_date = date('Y-m-d H:i:s');
	
		 $rules = array(
            'last_updated_date'    => 'required' // last updated date is required
        );
        $validator = Validator::make($request->input(), $rules);
        $messages = $validator->errors();

	$time = date("Y-m-d H:i:s", time());
        
         Session::forget('msg');
         $i = 0;
		 $lastUpdatedDate = '';
		 $finalArr = array();
        if(!$messages->first('last_updated_date')) { 
           			
			if($request->input('last_updated_date') > 0){ 
				$lastUpdatedDate = date("Y-m-d H:i:s", $request->input('last_updated_date'));  
			}else{
				$lastUpdatedDate  = date('Y-m-d H:i:s', strtotime("2016-01-01"));	
			}


			####$lastUpdatedDate = $request->input('last_updated_date'); //date('Y-m-d',strtotime(trim()));
        } 
        else {
            Session::flash('msg', $messages->first('last_updated_date'));
        } 



			$getListOfUpdatedCategory =	DB::table('category')
										->select('category.*')
										->leftJoin('asset_mapping', function($query){
											$query->on('category.id', '=', 'asset_mapping.module_id');
											$query->on('asset_mapping.module','=',DB::raw("'category'"));
										})
									->leftJoin('asset_library', function($query){
												$query->on('asset_library.id', '=', 'asset_mapping.asset_library_id');
									})
									->where(function($query) use ($lastUpdatedDate)
										{
											$query->where('category.created_at', '>=', $lastUpdatedDate)
											->orWhere('category.updated_at', '>=', $lastUpdatedDate)
											->orWhere('asset_mapping.updated_at', '>=', $lastUpdatedDate)
											->orWhere('asset_library.updated_at', '>=', $lastUpdatedDate);
										})
										->groupBy('category.id')
										->where('category.is_product','1')
										//->where('category.status','1')
										->orderBy('category.position','asc')
										->get();

				foreach($getListOfUpdatedCategory as $eachrow){
				
				$getListOfImages = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','category')
								  ->where('asset_mapping.approved_status','=','1')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.title','asset_library.name','asset_library.path','asset_library.type')
								  ->get(); 
								 
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['category_name'] = $eachrow->category_name;
				$finalArr[$i]['is_product'] = $eachrow->is_product;
				$finalArr[$i]['is_tutorial'] = $eachrow->is_tutorial;
				$finalArr[$i]['position'] = ($eachrow->position+1);
				$finalArr[$i]['category_parent_id'] = NULL;//$eachrow->category_parent_id;
				$finalArr[$i]['status'] = $eachrow->status;
				$finalArr[$i]['description'] = $eachrow->description;
				$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
				$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
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
						else
						{
							$finalArr[$i]['cat_doc'][$l]['doc_id'] = $eachImage->id;
							$finalArr[$i]['cat_doc'][$l]['name'] = $eachImage->name;
							$finalArr[$i]['cat_doc'][$l]['title'] = $eachImage->title;
							$finalArr[$i]['cat_doc'][$l]['path'] = $eachImage->path;
							 $l++;
						}	
					}
				}
				
				$i++;				
			}
			
			    $categoryDetailArr['categories'] = $finalArr;
			 
       
// updated products-----------------------
    $ProductArr = array();
    $k = 0;

    
									

    $getListOfUpdatedProducts =	  DB::table('product')
    								->select('product.*')
									->leftJoin('asset_mapping', function($query){
												$query->on('asset_mapping.module_id', '=', 'product.id');
												$query->on('asset_mapping.module','=',DB::raw("'product'"));
									})
									->leftJoin('asset_library', function($query){
												$query->on('asset_library.id', '=', 'asset_mapping.asset_library_id');
									})
    								->where(function($query) use ($lastUpdatedDate)
									{
										$query->where('product.created_at', '>=', $lastUpdatedDate)
										->orWhere('product.updated_at', '>=', $lastUpdatedDate)
										->orWhere('asset_mapping.updated_at', '>=', $lastUpdatedDate)
										->orWhere('asset_library.updated_at', '>=', $lastUpdatedDate);
									})
									->groupBy('product.id')
									//->where('product.status','1')
									->orderBy('product.launch_date','desc')
									->orderBy('product.position','asc')
									->orderBy('product.new_product_flag','desc')

									// ->orderBy('product.new_product_flag','product.desc','product.position','asc')
									->get();
				
				foreach($getListOfUpdatedProducts as $eachrow)
				{
				    $getListOfAssets = DB::table('asset_mapping')
								  ->join('asset_library', 'asset_mapping.asset_library_id', '=', 'asset_library.id')
								  ->where('asset_mapping.module','=','product')
								  ->where('asset_mapping.approved_status','=','1')
								  ->where('asset_mapping.module_id',$eachrow->id)
								  ->select('asset_library.id','asset_library.name','asset_library.title','asset_library.path','asset_library.type')
								  ->get(); 
								 
				$ProductArr[$k]['product_id'] = $eachrow->id;
				$ProductArr[$k]['category_id'] = $eachrow->category_id;
				$ProductArr[$k]['product_name'] = $eachrow->product_name;
				$ProductArr[$k]['product_desc'] = $eachrow->product_desc;
				$ProductArr[$k]['status'] = $eachrow->status;
				$ProductArr[$k]['desc1'] = $eachrow->desc1;
				$ProductArr[$k]['desc2'] = $eachrow->desc2;
				$ProductArr[$k]['desc3'] = $eachrow->desc3;
				$ProductArr[$k]['launch_date'] = $eachrow->launch_date;
				//date('d-m-Y',strtotime($eachrow->launch_date));
				$ProductArr[$k]['new_product_flag'] = $eachrow->new_product_flag;
				$ProductArr[$k]['position'] = $eachrow->position;
				$ProductArr[$k]['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
				$ProductArr[$k]['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
				if(!empty($getListOfAssets)){
					
					$m = 0;
					$n = 0;
					foreach($getListOfAssets as $eachAsset){
					if($eachAsset->type == 'image'){
					$ProductArr[$k]['pro_image'][$m]['image_id'] = $eachAsset->id;
				    $ProductArr[$k]['pro_image'][$m]['name'] = $eachAsset->name;
				    $ProductArr[$k]['pro_image'][$m]['title'] = $eachAsset->title;
				   $ProductArr[$k]['pro_image'][$m]['path'] = $eachAsset->path;
				   $m++;
					}
					else{
					$ProductArr[$k]['pro_doc'][$n]['doc_id'] = $eachAsset->id;
					$ProductArr[$k]['pro_doc'][$n]['name'] = $eachAsset->name;
					$ProductArr[$k]['pro_doc'][$n]['title'] = $eachAsset->title;
					$ProductArr[$k]['pro_doc'][$n]['path'] = $eachAsset->path;
					 $n++;
						}	
				
				  }
				}
				
				$k++;				
			}



			// function to App Delete Update---
			$deleteDetailLog = array();
			$deleteLog = array();
   			$deleteLog = self::deleteLog($lastUpdatedDate);
			if(!empty($deleteLog))
			{
				$deleteDetailLog['deleteLog'] = $deleteLog;
				
			}


			if(!empty($ProductArr))
			{
				$productDetailArr['products'] = $ProductArr;
				$masterArr = array_merge($categoryDetailArr, $productDetailArr,$deleteDetailLog);
				return $masterArr;
			}
			else if(!empty($finalArr))
			{
				return array_merge($categoryDetailArr,$deleteDetailLog);
			}
			else if(!empty($deleteLog))
			{
				
				$deleteDetailLog['deleteLog'] = $deleteLog;
				return $deleteDetailLog;
			}


						
}

public static function deleteLog($lastdate)
{

			$res = DB::table('delete_log')->where('delete_log.created_at', '>=', $lastdate)->get(); 
			
			$product = array();
			foreach($res as $key=>$val){
				$product[$val->type][] = $val->module_id;	
			}

			return  $product;
}

public static function deleteLogCount($lastdate)
{

			return DB::table('delete_log')->where('delete_log.created_at', '>=', $lastdate)->count(); 

}

// Function to App Update service-------

	
	public static function appUpdatesCount($request,$user_id){
	$Current_date = date('Y-m-d H:i:s');
	$rules = array(
        'last_updated_date'    => 'required' // last updated date is required
    );
    $validator = Validator::make($request->input(), $rules);
    $messages = $validator->errors();
    $updateCount = 0;
	$time = date("Y-m-d H:i:s", time());
	// echo strtotime("now");
	// exit;
    Session::forget('msg');
    $i = 0;
	$lastUpdatedDate = '';
	$finalArr = array();
    if(!$messages->first('last_updated_date')) { 
         			
		if($request->input('last_updated_date') > 0){ 
			$lastUpdatedDate = date("Y-m-d H:i:s", $request->input('last_updated_date'));  
		}else{
			$lastUpdatedDate  = date('Y-m-d H:i:s', strtotime("2016-01-01"));	
		}
    } else {
            Session::flash('msg', $messages->first('last_updated_date'));
    }	
		
			$getListOfUpdatedCategory =	DB::table('category')->select('category.id') 
											->leftJoin('asset_mapping', function($query){
												$query->on('category.id', '=', 'asset_mapping.module_id');
												$query->on('asset_mapping.module','=',DB::raw("'category'"));
											})
									->leftJoin('asset_library', function($query){
												$query->on('asset_library.id', '=', 'asset_mapping.asset_library_id');
									})
									->where(function($query) use ($lastUpdatedDate)
										{
											$query->where('category.created_at', '>=', $lastUpdatedDate)
											->orWhere('category.updated_at', '>=', $lastUpdatedDate)
											->orWhere('asset_mapping.updated_at', '>=', $lastUpdatedDate)
											->orWhere('asset_library.updated_at', '>=', $lastUpdatedDate);
										})
									//->where('category.status','1')
									->count(); 

						if($getListOfUpdatedCategory > 0){
								$updateCount ++;
						}
			    
			
			 
       
// updated products-----------------------
    $getListOfUpdatedProducts =	  DB::table('product')
    								->select('product.id')
									->leftJoin('asset_mapping', function($query){
												$query->on('asset_mapping.module_id', '=', 'product.id');
												$query->on('asset_mapping.module','=',DB::raw("'product'"));
									})
									->leftJoin('asset_library', function($query){
												$query->on('asset_library.id', '=', 'asset_mapping.asset_library_id');
									})
									->where(function($query) use ($lastUpdatedDate)
									{
										$query->where('product.created_at', '>=', $lastUpdatedDate)
										->orWhere('product.updated_at', '>=', $lastUpdatedDate)
										->orWhere('asset_mapping.updated_at', '>=', $lastUpdatedDate)
										->orWhere('asset_library.updated_at', '>=', $lastUpdatedDate);
									})
									//->where('product.status',1)
									->count();
				

						if($getListOfUpdatedProducts > 0){
								$updateCount ++;
						}

	$deleteLog = self::deleteLogCount($lastUpdatedDate);
						if($deleteLog > 0){
							$updateCount ++;
						}



	$sql =	DB::table('video_tutorials');
    		$sql->select('video_tutorials.video_id');
    		//$sql->where('video_tutorials.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('video_tutorials.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('video_tutorials.updated_at', '>=', $lastUpdatedDate);
            });

	$getListOfUpdatedTutorial=$sql->count('video_tutorials.video_id');
	
						if($getListOfUpdatedTutorial > 0){
								$updateCount ++;
						}

	$sql =	DB::table('tutorial_subcat');
    		$sql->select('tutorial_subcat.id');
    		//$sql->where('video_tutorials.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('tutorial_subcat.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('tutorial_subcat.updated_at', '>=', $lastUpdatedDate);
            });

	$getListOfUpdatedTutorialsubcat=$sql->count('tutorial_subcat.id');
	
						if($getListOfUpdatedTutorialsubcat > 0){
								$updateCount ++;
						}


	$sql =	DB::table('newscategory');
    		$sql->select('newscategory.id');
    		//$sql->where('newscategory.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('newscategory.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('newscategory.updated_at', '>=', $lastUpdatedDate);
            });

	$getListOfUpdatednewscategory=$sql->count();
	
						if($getListOfUpdatednewscategory > 0){
								$updateCount ++;
						}



	$sql =	DB::table('newssubcategory');
    		$sql->select('newssubcategory.id');
    		//$sql->where('newssubcategory.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('newssubcategory.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('newssubcategory.updated_at', '>=', $lastUpdatedDate);
            });

	$getListOfUpdatednewssubcategory=$sql->count();
	
						if($getListOfUpdatednewssubcategory > 0){
								$updateCount ++;
						}
		
		

		$sql =	DB::table('newstopic');
    		$sql->select('newstopic.id');
    		//$sql->where('newstopic.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('newstopic.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('newstopic.updated_at', '>=', $lastUpdatedDate);
            });

		$getListOfUpdatednewstopic=$sql->count();
	
						if($getListOfUpdatednewstopic > 0){
								$updateCount ++;
						}


		$sql =	DB::table('manufacture');
    		$sql->select('manufacture.id');
    		//$sql->where('manufacture.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('manufacture.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('manufacture.updated_at', '>=', $lastUpdatedDate);
            });

		$getListOfUpdatedManufacture=$sql->count();
	
						if($getListOfUpdatedManufacture > 0){
								$updateCount ++;
						}


		$sql =	DB::table('model');
    		$sql->select('model.id');
    		//$sql->where('model.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('model.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('model.updated_at', '>=', $lastUpdatedDate);
            });

		$getListOfUpdatedModel=$sql->count();
	
						if($getListOfUpdatedModel > 0){
								$updateCount ++;
						}	


		$sql =	DB::table('disclaimer');
    		$sql->select('disclaimer.id');
    		//$sql->where('model.status','=','1');
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('disclaimer.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('disclaimer.updated_at', '>=', $lastUpdatedDate);
            });

		$getListOfUpdateddisclaimer=$sql->count();
	
						if($getListOfUpdateddisclaimer > 0){
								$updateCount ++;
						}	


		
		$sql =	DB::table('users');
    		$sql->select('users.id');
    		$sql->where('users.id','=',$user_id);
    		$sql->where(function ($query)  use ($lastUpdatedDate){
    			$query->where('users.created_at', '>=', $lastUpdatedDate);
    			$query->orWhere('users.updated_at', '>=', $lastUpdatedDate);
            });

		$users=$sql->count();
	
						if($users > 0){
								$updateCount ++;
						}								
				
		return $updateCount;
			
						
}







// Function to Get User Detail -------
public static function userDetail($id) 
{
	$userDetail_array = array();
	$userDetails = DB::table('users')
			->join('user_detail', 'users.id', '=', 'user_detail.user_id')
			->join('user_mapping', 'users.id', '=', 'user_mapping.user_id')
			->leftjoin('NationalDistributor', 'NationalDistributor.id', '=', 'user_detail.nd_name')
			->leftjoin('RegionalDistributor', 'RegionalDistributor.id', '=', 'user_detail.rd_name')
			//->leftjoin('state', 'state.id', '=', 'user_detail.state')
			//->leftjoin('zone', 'zone.id', '=', 'user_detail.zone')
			//->leftjoin('city', 'city.id', '=', 'user_detail.city')
			->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.status','users.gender','users.dob','users.profile_picture', 'user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name as nd_id','user_detail.rd_name as rd_id','NationalDistributor.nd_name','RegionalDistributor.rd_name','user_mapping.parent_id')
			->where('users.id', $id)->first();
			
			if(!empty($userDetails))
			{
				
				
				if(!empty($userDetails))
				{
					
                    $sp_name = DB::table('users')->select('first_name','last_name','email','contact')->where('users.id', $userDetails->id)->first();
                    if(!empty($sp_name))
                    {
                    $spName = $sp_name->first_name.' '.$sp_name->last_name; 
                    $spEmail = $sp_name->email; 
                    $spContact = $sp_name->contact; 
                    }
                    else
                    {
                    $spName = '';   
                    $spEmail = '';  
                    $spContact = '';    
                    }

                    $userDetail_array['id'] = $userDetails->id;
					$userDetail_array['first_name'] = $userDetails->first_name;
					$userDetail_array['last_name'] = $userDetails->last_name;
					$userDetail_array['email'] = $userDetails->email;
					$userDetail_array['role'] = $userDetails->role;
					$userDetail_array['contact'] = $userDetails->contact;
					$userDetail_array['status'] = $userDetails->status;
					$userDetail_array['gender'] = $userDetails->gender;
					$userDetail_array['dob'] = date('d-m-Y',strtotime($userDetails->dob));
					$userDetail_array['profile_picture'] = $userDetails->profile_picture;
					$userDetail_array['city_name'] = $userDetails->city;
					//$userDetail_array['city_name'] = $userDetails->city_name;
					$userDetail_array['state_name'] = $userDetails->state;
					//$userDetail_array['state_name'] = $userDetails->state_name;
					$userDetail_array['zone_name'] = $userDetails->zone;
					//$userDetail_array['zone_name'] = $userDetails->zone_name;
					$userDetail_array['beat_route_id'] = $userDetails->beat_route_id;
					$userDetail_array['rt_code'] = $userDetails->rt_code;
					$userDetail_array['nd_id'] = $userDetails->nd_id;
					$userDetail_array['nd_name'] = $userDetails->nd_name;
					$userDetail_array['rd_id'] = $userDetails->rd_id;
					$userDetail_array['rd_name'] = $userDetails->rd_name;
					$userDetail_array['sp_id'] = $userDetails->parent_id;
					$userDetail_array['sp_name'] = $spName;
					$userDetail_array['sp_email'] = $spEmail;
					$userDetail_array['sp_contact'] = $spContact;
				}
				return $userDetail_array; 
			}
			else
			{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,UserController::MSG_NO_RECORD);
			}
				
}

// Function to Get Zone List -------
  public static function getZoneList() 
 {	
     $ZoneDetails = DB::table('zone')
                ->select('id','zone_name')
                ->get();
	return $ZoneDetails;
 }

// Function to Get State List -------
  public static function getStateList($zone_name) 
 {	

 	$stateDetails=array();
 	$Zone = DB::table('zone')
                ->select('id','zone_name')
                ->where('zone_name',$zone_name)
                ->first();
      if($Zone){
      	$stateDetails = DB::table('state')
								->where('state.zone_id',$Zone->id)
								->select('state.id as state_id','state.state_name')
								->get();
      }          
     
	return $stateDetails;
 }

// Function to Get City List -------
  public static function getCityList($state_name) 
 {	
 	$cityDetails=array();
 	$State = DB::table('state')
								->where('state.state_name',$state_name)
								->select('state.id as state_id','state.state_name')
								->first();
	if($State){
		$cityDetails = DB::table('city')
								->where('city.state_id',$State->state_id)
								->select('city.id as city_id','city.city_name')
								->get();
	}							
    
	return $cityDetails;
 } 
 
 // Function to Get ND List -------
 //  public static function getNDList() 
 // {	
 //    $ND_Details = DB::table('NationalDistributor')
 //                  ->select('id','nd_name')
 //                  ->get();
			
	// return $ND_Details;
 // } 
 
 // Function to Get RD List -------
 //  public static function getRDList() 
 // {	
 //    $RD_Details = DB::table('RegionalDistributor')
 //                  ->select('id','rd_name')
 //                  ->get();
			
	// return $RD_Details;
 // } 
 
// Function to Add Zone -------
  // public static function AddZone($request) 
  //       { 
  //           $rules = array(
  //               'zone_name'    => 'required' // zone_name is required
  //           );
  //           $validator = Validator::make($request->input(), $rules);
  //           $messages = $validator->errors();
  //           Session::forget('msg');
  //           if(!$messages->first('zone_name')) 
  //           {
  //                   $zone=$request->input('zone_name');
		// 			$alreadyzone = GStarBaseController::validateForExist('zone',$zone,'zone_name');
  //                   if($alreadyzone)
  //                   {
  //                       Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
		// 				return ;
  //                   }
  //                      $zone = trim($request->input('zone_name'));
  //            }
  //           else 
  //           {
  //               Session::flash('msg', $messages->first('zone_name'));
  //           } 
  //           if(!Session::has('msg'))
  //           {
  //                DB::table('zone')->insert(['zone_name' => $zone]);
  //                return $zone;
  //            }
             
  //       }

	// Function to App State -------
    //  public static function AddState($request) 
    // { 
    //     $rules = array(
    //         'zone_id'    => 'required', // zone_name is required
    //         'state_name'    => 'required'  // state_name is required
    //     );
    //     $validator = Validator::make($request->input(), $rules);
    //     $messages = $validator->errors();
    //     Session::forget('msg');
	   // if(!$messages->first('zone_id')) 
    //     {
    //             $zone=$request->input('zone_id');
    //            if(!$messages->first('state_name')) 
    //                 {
    //                             $state=$request->input('state_name');
				// 				$alreadystate = GStarBaseController::validateForExist('state',$state,'state_name');
    //                            if($alreadystate)
    //                             {
    //                                Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
				// 		           return ;
    //                             }
                              
    //                             $state = trim($request->input('state_name'));
    //                  }
				// 	else 
    //                 {
    //                     Session::flash('msg', $messages->first('state_name'));
    //                 }
    //      }
    //     else 
    //     {
    //         Session::flash('msg', $messages->first('zone_id'));
    //     } 
        
    //     if(!Session::has('msg'))
    //     {
    //          DB::table('state')->insert(['zone_id'=>$zone,'state_name' => $state]);
    //          return $state;
    //      }
         
    // }

// Function to App City -------
 // public static function AddCity($request) 
 //    { 
 //        $rules = array(
 //            'zone_id'    => 'required',   // zone_name is required
 //            'state_id'    => 'required',  // state_name is required
 //            'city_name'    => 'required' // city_name is required
 //        );
 //        $validator = Validator::make($request->input(), $rules);
 //        $messages = $validator->errors();
 //        Session::forget('msg');
 //        if(!$messages->first('zone_id')) 
 //        {
 //                $zone=$request->input('zone_id');
 //               if(!$messages->first('state_id')) 
 //                    {
 //                            $state=$request->input('state_id');
 //                            if(!$messages->first('city_name')) 
 //                                    {
 //                                            $city=$request->input('city_name');
	// 										$alreadycity = GStarBaseController::validateForExist('city',$city,'city_name');
                                           
 //                                            if($alreadycity)
 //                                            {
 //                                               Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
	// 					                        return ;
 //                                            }
 //                                              $city = trim($request->input('city_name'));
 //                                       }
 //                                        else 
 //                                        {
 //                                            Session::flash('msg', $messages->first('city_name'));
 //                                        }
 //                     }
 //                    else 
 //                    {
 //                        Session::flash('msg', $messages->first('state_id'));
 //                    }
                   
 //        }
 //        else 
 //        {
 //            Session::flash('msg', $messages->first('zone_id'));
 //        } 
        
 //        if(!Session::has('msg'))
 //        {
 //             DB::table('city')->insert(['zone_id'=>$zone,'state_id'=>$state,'city_name' => $city]);
 //             return $city;
 //        }
         
 //    }

	// Function to Insert Zone from CSV -------
// 	public static function insertZoneFromCSV() 
//     {
// 	$savedData =''; $count = 0;
// 	$demoTableData = DB::table('demotable')->groupBy('zone')->get(); 
// 	foreach($demoTableData as $eachrow)
// 	{
// 	$alreadyzone = GStarBaseController::validateForExist('zone',$eachrow->zone,'zone_name');
// 		if($alreadyzone!= 1)
// 		   {
// 			  $savedData =  DB::table('zone')->insert(['zone_name' => $eachrow->zone]);
// 			}
// 			else{
// 				$count = 1;
// 			}
// 	}
// 	if($savedData && ($count == 0))
// 	{
// 	echo "success";
// 	}
// 	else{
// 		echo "already exist";
// 	}
// }
	
	// Function to Insert State from CSV -------
// 	public static function insertStateFromCSV() 
//     {
// 	$savedData ='';
// 	$count = 0;
// 	$demoTableData = DB::table('demotable')->groupBy('State')->get(); 
	
// 	foreach($demoTableData as $eachrow)
// 	{
// 	$alreadyState = GStarBaseController::validateForExist('state',$eachrow->State,'state_name');
// 		if($alreadyState!= 1)
// 		   {
// 			   $zoneID =   DB::table('zone')->select('id')->where('zone_name',$eachrow->zone)->first(); 
// 			  $savedData =   DB::table('state')->insert(['zone_id'=>$zoneID->id,'state_name' => $eachrow->State]);
// 			}
// 			else{
// 				$count = 1;
// 				}
// 	}
// 	if($savedData && ($count == 0))
// 	{
// 	echo "success";
// 	}
// 	else{
// 		echo "already exist";
// 	}
// }

// Function to Insert City from CSV -------
// public static function insertCityFromCSV() 
//     {
// 	$savedData =''; $count = 0;
// 	$demoTableData = DB::table('demotable')->groupBy('City')->get(); 
	
// 	foreach($demoTableData as $eachrow)
// 	{
// 	$alreadyCity = GStarBaseController::validateForExist('city',$eachrow->City,'city_name');
// 		if($alreadyCity!= 1)
// 		   {
// 			   $zoneID =   DB::table('zone')->select('id')->where('zone_name',$eachrow->zone)->first(); 
// 			   $StateID =   DB::table('state')->select('id')->where('state_name',$eachrow->State)->first(); 
// 			  $savedData =   DB::table('city')->insert(['zone_id'=>$zoneID->id,'state_id'=>$StateID->id,'city_name' =>$eachrow->City]);
// 			}
// 			else{
// 				$count = 1;
// 			}
// 	}
// 	if($savedData && ($count == 0))
// 	{
// 	echo "success";
// 	}
// 	else{
// 		echo "already exist";
// 	}
// }



	// get list of users whos login date is n days older 
	public static function GetInactiveUser($num_days) {
		
		$InactiveUser = DB::table('users')
						->where('last_login', '!=', "")
						->where('last_login', '<', $num_days)
						->where('status', '1')
						->where('role','>',GStarBaseController::G_ADMIN_ROLE_ID)
						->select('id')
						->orderBy('id', 'ASC')->get();
				
		return $InactiveUser;
	}
	
	// logout device when session expired
	public static function existSession($userid, $timenow) {
		
		$user_exist = true;
		$userId = DB::table('oauth_sessions')			
			->join('oauth_access_tokens', 'session_id', '=', 'oauth_sessions.id')		
			->select('oauth_sessions.owner_id', 'oauth_access_tokens.expire_time')
			->where('oauth_access_tokens.expire_time', '>=', $timenow)	
			->where('oauth_sessions.owner_id', $userid)	
			->orderBy('oauth_access_tokens.expire_time','desc')->first();
		//print_r($userId); die;
		if(!empty($userId)) {
			$user_exist = false;
		}
		
		return $user_exist;
	}
	
	// logout device when session expired
	public static function expiredSession($timenow) {
		
		$user_array = array();
		$userIds = DB::table('oauth_sessions')			
			->join('oauth_access_tokens', 'session_id', '=', 'oauth_sessions.id')			
			->select('oauth_sessions.owner_id', 'oauth_access_tokens.expire_time', 'oauth_sessions.id')			
			->where('oauth_access_tokens.expire_time', '<', $timenow)			
			//->groupBy('oauth_sessions.owner_id')
			->orderBy('oauth_access_tokens.expire_time','desc')->get();
			
		if(!empty($userIds))
		{
			$e = 0;
			foreach($userIds as $row) {
				$user_array[$e]['user_id'] = $row->owner_id;
				$user_array[$e]['id'] = $row->id;
				$user_array[$e]['expire_time'] = date('d-m-Y h:i:s a',$row->expire_time);
				
				$e++;
			}
		}

		//print_r($user_array); die;
		
		return $user_array;
	}

	public static function Updatelogin($user_id,$login_count){
		
		$updateUser=array('last_login'=>strtotime('now'),'login_count'=>($login_count+1));
 		$saveLastLogin=DB::table('users')->where('id',$user_id)->update($updateUser);
	}
	
	// mark device logout
	public static function cleardevice($userid, $timenow, $id) {
		
		$userLogout=array();
		if(self::existSession($userid, $timenow)) {
			// save logout time
			DB::table('users')->where('id',$userid)->update(['last_logout'=>strtotime('now')]);
			// end save logout time
			$login_status = array('login_status'=>0);
			$update_login = DB::table('device')->where('user_id', $userid)->update($login_status);
			$userLogout=$userid;
		}
		
		// remove session entries
		DB::table('oauth_sessions')->where('id', $id)->delete();
		DB::table('oauth_access_tokens')->where('session_id', $id)->delete();
		
		return $userLogout;
	}
	
	// deactivate user with provided user id
	public static function deActivateUser($userid) {
		
		$updateUser = array('status'=>'0');
		$updateUserRecord = DB::table('users')->where('id', $userid)->update($updateUser);
		
		if($updateUserRecord) {
		    $updates['msg'] = GStarBaseController::MSG_RECORD_UPDATED;
		} else {
			$updates['msg'] = GStarBaseController::MSG_NO_UPDATE;
		}
		
		return $updates;
	}


	public static function updateprofileRequestList($request,$search_keyword,$pageNo)
    {
    	$finalArr = array();
    	$nameArr=array();
    	$inputs = Input::get();
		$i = 0;
		if($pageNo)
        {
           $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		}else{
			$offset= 0;
		}
		if($search_keyword!="")
		{
		 	if(strpos($search_keyword, " ") != false){
		 	 $nameArr = explode(' ',$search_keyword);
			 $sql = DB::table('users_dummy');
			 		$sql->where('first_name','like','%'.$nameArr[0].'%');
					$sql->orWhere('last_name','like','%'.$nameArr[1].'%');
					$sql->orWhere('email','like','%'.$nameArr[0].'%');
					if(isset($inputs['approve_status'])){
			  				$sql->where('approved_status',trim($inputs['approve_status']));
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
					$sql->where('is_deleted',0);
					$sql->orderBy('approved_status', 'asc');
			  		$sql->skip($offset);
			  		$sql->take(GStarBaseController::PAGINATION_LIMITS);
			  		
		 	 }else{
		 	 	$sql = DB::table('users_dummy');
			 			$sql->where('first_name','like',$search_keyword.'%');
			 			$sql->orWhere('email','like','%'.$search_keyword.'%');
			 			$sql->where('is_deleted',0);
			 			if(isset($inputs['approve_status'])){
			  				$sql->where('approved_status',trim($inputs['approve_status']));
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
						$sql->orderBy('approved_status', 'asc');
			  			$sql->skip($offset);
			  			$sql->take(GStarBaseController::PAGINATION_LIMITS);
		 	 }
         }
        else
		{
		     $sql = DB::table('users_dummy');
		     		$sql->where('is_deleted',0);
		     		$sql->orderBy('approved_status', 'asc');
		     		if(isset($inputs['approve_status'])){
			  				$sql->where('approved_status',trim($inputs['approve_status']));
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

         $users_dummyDetail=$sql->get();
	
	$user=new User();
	if(!empty($users_dummyDetail))
	{
		$LoginRole=Auth::user()->role;
		$LoginUserId=Auth::user()->id;
		foreach ($users_dummyDetail as $key ) 
		{
			if($LoginRole<$key->role)
			{

				if((int)$LoginRole <= (int)GStarBaseController::G_ADMIN_ROLE_ID)
				{
					$finalArr[$i]['id'] = $key->id;
					$user_id=$key->user_id;
					$finalArr[$i]['user_id'] =$user_id; 
					$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
					$finalArr[$i]['first_name'] = $key->first_name;
					$finalArr[$i]['last_name'] = $key->last_name;
					$finalArr[$i]['email'] = $key->email;
					$finalArr[$i]['contact'] = $key->contact;
					$finalArr[$i]['role'] = $key->role;
					$finalArr[$i]['profile_picture'] = $key->profile_picture;
					$finalArr[$i]['approved_status'] = $key->approved_status;
					$approved_date=$key->approved_date;
					if($approved_date){
						$finalArr[$i]['approved_date'] = date("d-m-Y", strtotime( $approved_date ) ) ;
					} else {
						$finalArr[$i]['approved_date'] = $approved_date;
					}
					$approved_userid=$key->approved_userid;
					$finalArr[$i]['approved_userid'] = $approved_userid;
					if($approved_userid!=NULL){
						$finalArr[$i]['approved_by'] = $user->getusername($approved_userid); 
					} else {
						$finalArr[$i]['approved_by'] = NULL;
					}
					$i++;

				}else if((int)$LoginRole == (int)GStarBaseController::G_TRAINER_ROLE_ID){
						$LoginRole=Auth::user()->role;
						$LoginUserId=Auth::user()->id; 
						$sp_id2=null;
						$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$key->user_id)->first();
						
						if($sp_id->parent_id ==$LoginUserId)
						{
								$finalArr[$i]['id'] = $key->id;
								$user_id=$key->user_id;
								$finalArr[$i]['user_id'] =$user_id; 
								$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
								$finalArr[$i]['first_name'] = $key->first_name;
								$finalArr[$i]['last_name'] = $key->last_name;
								$finalArr[$i]['email'] = $key->email;
								$finalArr[$i]['contact'] = $key->contact;
								$finalArr[$i]['role'] = $key->role;
								$finalArr[$i]['profile_picture'] = $key->profile_picture;
								$finalArr[$i]['approved_status'] = $key->approved_status;
								$approved_date=$key->approved_date;
								if($approved_date){
								$finalArr[$i]['approved_date'] = strtotime( $approved_date )."000";
								//date("d-m-Y", strtotime( $approved_date ) ) ;
								}else{
									$finalArr[$i]['approved_date'] = $approved_date;
								}
								$approved_userid=$key->approved_userid;
								$finalArr[$i]['approved_userid'] = $approved_userid;
								if($approved_userid!=NULL){
									$finalArr[$i]['approved_by'] = $user->getusername($approved_userid); 
								}else{
									$finalArr[$i]['approved_by'] = NULL;
								}
								$i++;
						} else {
							if($sp_id){
							$sp_id2=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
							}
							
							if($sp_id2)
							{
								if($sp_id2->parent_id ==$LoginUserId)
								{
									$finalArr[$i]['id'] = $key->id;
									$user_id=$key->user_id;
									$finalArr[$i]['user_id'] =$user_id; 
									$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
									$finalArr[$i]['first_name'] = $key->first_name;
									$finalArr[$i]['last_name'] = $key->last_name;
									$finalArr[$i]['email'] = $key->email;
									$finalArr[$i]['contact'] = $key->contact;
									$finalArr[$i]['role'] = $key->role;
									$finalArr[$i]['profile_picture'] = $key->profile_picture;
									$finalArr[$i]['approved_status'] = $key->approved_status;
									$approved_date=$key->approved_date;
									if($approved_date){
									$finalArr[$i]['approved_date'] = strtotime( $approved_date )."000";
									}else{
										$finalArr[$i]['approved_date'] = $approved_date;
									}
									$approved_userid=$key->approved_userid;
									$finalArr[$i]['approved_userid'] = $approved_userid;
									if($approved_userid!=NULL){
										$finalArr[$i]['approved_by'] = $user->getusername($approved_userid); 
									}else{
										$finalArr[$i]['approved_by'] = NULL;
									}
									$i++;
								}
							}		

						}
						

				} else {
					$sp_id=DB::table('user_mapping')
							->select('parent_id')->where('user_id',$key->user_id)->first();
					if($sp_id)
					{
						if($sp_id->parent_id ==$LoginUserId)
						{
							$finalArr[$i]['id'] = $key->id;
							$user_id=$key->user_id;
							$finalArr[$i]['user_id'] =$user_id; 
							$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
							$finalArr[$i]['first_name'] = $key->first_name;
							$finalArr[$i]['last_name'] = $key->last_name;
							$finalArr[$i]['email'] = $key->email;
							$finalArr[$i]['contact'] = $key->contact;
							$finalArr[$i]['role'] = $key->role;
							$finalArr[$i]['profile_picture'] = $key->profile_picture;
							$finalArr[$i]['approved_status'] = $key->approved_status;
							$approved_date=$key->approved_date;
							if($approved_date){
							$finalArr[$i]['approved_date'] = strtotime( $approved_date )."000";
							}else{
								$finalArr[$i]['approved_date'] = $approved_date;
							}
							$approved_userid=$key->approved_userid;
							$finalArr[$i]['approved_userid'] = $approved_userid;
							if($approved_userid!=NULL){
								$finalArr[$i]['approved_by'] = $user->getusername($approved_userid); 
							}else{
								$finalArr[$i]['approved_by'] = NULL;
							}
							$i++;
						}
					}		
				}

			}
		}
	}
	
	return  $finalArr;
	
	}

	public static function getusername($user_id) 
    {
    	$username = DB::table('users')->select('first_name','last_name')->where('id',$user_id)->first(); 
		if ($username) {
			return  $username->first_name.' '.$username->last_name;
		} else {
			return null;
		}
	}


	public static function updateprofileById($id,$user_id) 
    {

    	$finalArr = array();
			$result_array = array();
			$finalArr['olddata']=[];
			$finalArr['newdata']=[];
			$userDetails = DB::table('users')
			->join('user_detail', 'users.id', '=', 'user_detail.user_id')
			//->leftjoin('state', 'state.id', '=', 'user_detail.state')
			//->leftjoin('zone', 'zone.id', '=', 'user_detail.zone')
			//->leftjoin('city', 'city.id', '=', 'user_detail.city')
			->select('users.id','users.first_name','users.last_name', 'users.email','users.role','users.contact','users.profile_picture', 'user_detail.city','user_detail.state','user_detail.nd_name as nd_id','user_detail.rd_name as rd_id','user_detail.nd_name','user_detail.rd_name','user_detail.zone')
			->where('users.id', $user_id)->first();

			
			if(!empty($userDetails))
			{
					
                    $finalArr['olddata']['user_id'] = $userDetails->id;
					$finalArr['olddata']['first_name'] = $userDetails->first_name;
					$finalArr['olddata']['last_name'] = $userDetails->last_name;
					$finalArr['olddata']['email'] = $userDetails->email;
					$finalArr['olddata']['contact'] = $userDetails->contact;
					$finalArr['olddata']['role'] = $userDetails->role;
					if($userDetails->profile_picture){
						$finalArr['olddata']['profile_picture'] = $userDetails->profile_picture;
					}else{
						$finalArr['olddata']['profile_picture']='';
					}
					
					$finalArr['olddata']['city_name'] = $userDetails->city;
					//$finalArr['olddata']['city_name'] = $userDetails->city_name;
					$finalArr['olddata']['state_name'] = $userDetails->state;
					//$finalArr['olddata']['state_name'] = $userDetails->state_name;
					$finalArr['olddata']['zone_name'] = $userDetails->zone;
					//$finalArr['olddata']['zone_name'] = $userDetails->zone_name;
					$finalArr['olddata']['nd_id'] = $userDetails->nd_id;
					$finalArr['olddata']['nd_name'] = $userDetails->nd_name;
					$finalArr['olddata']['rd_id'] = $userDetails->rd_id;
					$finalArr['olddata']['rd_name'] = $userDetails->rd_name;
				}

		$userDetailsNew = DB::table('users_dummy')
			//->leftjoin('state', 'state.id', '=', 'users_dummy.state')
			//->leftjoin('zone', 'zone.id', '=', 'users_dummy.zone')
			//->leftjoin('city', 'city.id', '=', 'users_dummy.city')
			->select('users_dummy.user_id','users_dummy.first_name','users_dummy.last_name', 'users_dummy.email','users_dummy.role','users_dummy.contact','users_dummy.profile_picture', 'users_dummy.city','users_dummy.state','users_dummy.zone','users_dummy.nd_name as nd_id','users_dummy.rd_name as rd_id','users_dummy.nd_name','users_dummy.rd_name','users_dummy.approved_status')
			->where('users_dummy.user_id', $user_id)
			->where('users_dummy.is_deleted', 0)
			->where('users_dummy.id', $id)->first();
			if(!empty($userDetailsNew))
			{
                    $finalArr['newdata']['user_id'] = $userDetailsNew->user_id;
					$finalArr['newdata']['first_name'] = $userDetailsNew->first_name;
					$finalArr['newdata']['last_name'] = $userDetailsNew->last_name;
					$finalArr['newdata']['email'] = $userDetailsNew->email;
					$finalArr['newdata']['contact'] = $userDetailsNew->contact;
					$finalArr['newdata']['role'] = $userDetailsNew->role;
					$finalArr['newdata']['approved_status'] = $userDetailsNew->approved_status;

					if($userDetailsNew->profile_picture){
						$finalArr['newdata']['profile_picture'] = $userDetailsNew->profile_picture;
					}else{
						$finalArr['newdata']['profile_picture']='';
					}
					
					//$finalArr['newdata']['city_id'] = $userDetailsNew->city;
					$finalArr['newdata']['city_name'] = $userDetailsNew->city;
					//$finalArr['newdata']['state_id'] = $userDetailsNew->state;
					$finalArr['newdata']['state_name'] = $userDetailsNew->state;
					//$finalArr['newdata']['zone_id'] = $userDetailsNew->zone;
					$finalArr['newdata']['zone_name'] = $userDetailsNew->zone;
					$finalArr['newdata']['nd_id'] = $userDetailsNew->nd_id;
					$finalArr['newdata']['nd_name'] = $userDetailsNew->nd_name;
					$finalArr['newdata']['rd_id'] = $userDetailsNew->rd_id;
					$finalArr['newdata']['rd_name'] = $userDetailsNew->rd_name;
				}	
		return $finalArr;
		
	}


	public static function updateRequestProfile($id,$UserDetail,$approved_status) 
    {

    	
    	$userid=$UserDetail['user_id']; 	
    	$user=new User();
    	$result_array = array();
    	
    	if($approved_status == GStarBaseController::ADMIN_REJECT_REQUEST){

    		$updateDummyData=array('approved_status'=>$approved_status,'approved_date'=>date("Y-m-d h:m:s"),'approved_userid'=>Auth::user()->id);

    		$updateUserDummyRecord = DB::table('users_dummy')->where('id', $id)->update($updateDummyData);

			    $result_array['status'] = 'success';
				$result_array['msg'] = GStarBaseController::MSG_RECORD_ADMIN_REJECT_SUCCESS;
    	}else{
    			$approved_status = DB::table('users_dummy')->select('approved_status')->where('id',$id)->where('users_dummy.user_id', $UserDetail['user_id'])->first();

	    		if($approved_status->approved_status==0){

	    		
	    			if($UserDetail['profile_picture'] != "")
	    			{
	    				$updateUser = array('first_name'=>$UserDetail['first_name'],'last_name'=>$UserDetail['last_name'],'contact'=>$UserDetail['contact'],'email'=>$UserDetail['email'],'profile_picture'=>$UserDetail['profile_picture']);
	    			}else{
	    				$updateUser = array('first_name'=>$UserDetail['first_name'],'last_name'=>$UserDetail['last_name'],'contact'=>$UserDetail['contact'],'email'=>$UserDetail['email']);
	    			}
	    		

	    		$updateUserDetail = array('city'=>$UserDetail['city_name'],'state'=>$UserDetail['state_name'],'zone'=>$UserDetail['zone_name'],'nd_name'=>$UserDetail['nd_id'],'rd_name'=>$UserDetail['rd_id']);

				$updateUserRecord = DB::table('users')->where('id', $userid)->update($updateUser);

				$updateUserDetailRecord = DB::table('user_detail')->where('user_id', $userid)->update($updateUserDetail); 

				$updateDummyData=array('approved_status'=>'1','approved_date'=>date("Y-m-d h:m:s"),'approved_userid'=>Auth::user()->id);

				$updateUserDummyRecord = DB::table('users_dummy')->where('id', $id)->update($updateDummyData);

				$result_array['status'] = 'success';
				$result_array['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_SUCCESS;
	    	}
	    	else{
	    		
	    		$result_array['status'] = 'error';
				$result_array['msg'] = GStarBaseController::MSG_RECORD_UPDATED_ADMIN_APPROVE_FAIL;		
			
	    	}
    	}
    	
    	return $result_array;

	}


	public static function accountActivationList($search_keyword ,$pageNo) 
    {

	   $finalArr = array();
       $nameArr=array();
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
		 	 if(strpos($search_keyword, " ") != false){
		 	 	$nameArr = explode(' ',$search_keyword);
			 	$sql = DB::table('user_activation_request');
			 			$sql->join('users', 'user_activation_request.user_id','=','users.id');
			 			$sql->where('users.first_name','like','%'.$nameArr[0].'%');
						$sql->orWhere('users.last_name','like','%'.$nameArr[1].'%');
						$sql->orWhere('users.email','like','%'.$nameArr[0].'%');
						$sql->where('user_activation_request.is_deleted',0);
						if(isset($inputs['approve_status'])){
			  				$sql->where('user_activation_request.activation_status',trim($inputs['approve_status']));
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
					   				$sql->whereBetween('user_activation_request.created_at', [$date1, $date2]);	
					   			}
					   		}
				   		}
						$sql->select('users.id as userId','users.first_name','users.last_name', 'users.email','users.role','users.last_login','users.status','user_activation_request.id as activationId','user_activation_request.user_id','user_activation_request.requested_at','user_activation_request.activation_status','user_activation_request.created_at','user_activation_request.updated_at');

						$sql->orderBy('user_activation_request.activation_status', 'asc');
						$sql->orderBy('user_activation_request.requested_at', 'desc');
						
			  			$sql->skip($offset);
			  			$sql->take(GStarBaseController::PAGINATION_LIMITS);
			  			
		 	 }else{
		 	 	$sql = DB::table('user_activation_request');
		 	 		$sql->join('users', 'user_activation_request.user_id','=','users.id');
			 		$sql->where('users.first_name','like','%'.$search_keyword.'%');
			 		$sql->orWhere('users.email','like','%'.$search_keyword.'%');
			 		$sql->where('user_activation_request.is_deleted',0);
			 		if(isset($inputs['approve_status'])){
			  				$sql->where('user_activation_request.activation_status',trim($inputs['approve_status']));
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
					   				$sql->whereBetween('user_activation_request.created_at', [$date1, $date2]);	
					   			}
					   		}
				   		}
			 		$sql->select('users.id as userId','users.first_name','users.last_login','users.last_name', 'users.email','users.role','users.status','user_activation_request.id as activationId','user_activation_request.user_id','user_activation_request.requested_at','user_activation_request.activation_status','user_activation_request.created_at','user_activation_request.updated_at');
					$sql->orderBy('user_activation_request.activation_status', 'asc');
						$sql->orderBy('user_activation_request.requested_at', 'desc');
						
			  		$sql->skip($offset);
			  		$sql->take(GStarBaseController::PAGINATION_LIMITS);
			  						
		 	 }
		 	
         }
        else
		{
		     $sql = DB::table('user_activation_request');
		 	 		$sql->join('users', 'user_activation_request.user_id','=','users.id');
		 	 		$sql->where('user_activation_request.is_deleted',0);
		 	 		if(isset($inputs['approve_status'])){
			  				$sql->where('user_activation_request.activation_status',trim($inputs['approve_status']));
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
					   				$sql->whereBetween('user_activation_request.created_at', [$date1, $date2]);	
					   			}
					   		}
				   		}	
		 	 		$sql->select('users.id as userId','users.first_name','users.last_name', 'users.email','users.role','users.last_login','users.status','user_activation_request.id as activationId','user_activation_request.user_id','user_activation_request.requested_at','user_activation_request.activation_status','user_activation_request.created_at','user_activation_request.updated_at');
					$sql->orderBy('user_activation_request.activation_status', 'asc');

					$sql->orderBy('user_activation_request.requested_at', 'desc');
			  		$sql->skip($offset);
			  		$sql->take(GStarBaseController::PAGINATION_LIMITS);
			  		
         }
         $usersDetail=$sql->get();
         
	//$usersDetail = DB::table('user_activation_request')->orderBy('requested_at','ASC')->get(); 
	
	$user=new User();
	if(!empty($usersDetail)){

		foreach ($usersDetail as $key ) {
		$finalArr[$i]['id'] = $key->activationId;
		$user_id=$key->user_id;
		$finalArr[$i]['user_id'] =$user_id;
		//$userDetail=$user->getuserDetail($user_id); 
		$finalArr[$i]['user_name'] =$key->first_name.''.$key->last_name;
		$finalArr[$i]['role'] =$key->role;
		$finalArr[$i]['supervisor_name'] =$user->supervisor_name($user_id); 
		$finalArr[$i]['activation_status'] =$key->activation_status; 
		$finalArr[$i]['requested_at'] = ( $key->requested_at )."000";
		//date("d-m-Y h:m:sa", $key->requested_at ) ;
		if($key->last_login){
			$finalArr[$i]['last_login'] =  ( $key->last_login )."000";
			//date("d-m-Y h:m:sa", $key->last_login) ;
		}else{
			$finalArr[$i]['last_login'] = '';
		}
		
		$updated_at=$key->updated_at;
		if($updated_at!=NULL){
			$finalArr[$i]['updated_at'] = strtotime( $updated_at )."000";
			 //date("d-m-Y h:m:sa", strtotime( $updated_at ) ) ;
		}else{
			$finalArr[$i]['updated_at'] =  '' ;
		}
		
		$i++;
	}
	
		
	}

	 return  $finalArr;
	}

	// public static function getuserDetail($user_id) 
 //    {
 //    	$user = DB::table('users')->select('first_name','last_name','role','last_login')->where('id',$user_id)->first(); 
 //    	$userDetail=array();
	// 	$userDetail['user_name']=$user->first_name.' '.$user->last_name;
	// 	$userDetail['role']=$user->role;
	// 	$userDetail['last_login']=$user->last_login;
	 
	//  return  $userDetail;
	// }

	public static function supervisor_name($user_id) 
    {
    	$user = DB::table('user_mapping')->select('parent_id')->where('user_id',$user_id)->first(); 
    	$parent_name='';
    	if ($user) {
    		$parent_id=$user->parent_id;
	    	$userObj=new User();
	    	if($parent_id){
	    		$parent_name=$userObj->getusername($parent_id); 
	    	}
    	}
	 return  $parent_name;
	}


	public static function activateUser($user_id,$activation_status) 
    {
    	if($activation_status != GStarBaseController::ADMIN_REJECT_REQUEST){
				$user = DB::table('users')->where('id',$user_id)->where('status','0')->update(['status'=>'1']); 
			}
    	
    	$user = DB::table('user_activation_request')->where('user_id',$user_id)->where('activation_status','0')->update(['activation_status'=>$activation_status,'updated_at'=>date("Y-m-d h:m:s")]);
	 	return  $user;
	}


	public static function getUserDetailforActivation($id) 
    {
    	$user = DB::table('users')->select('email','first_name','last_name')->where('id',$id)->where('status', 1)->first();
		
		return $user;
	}


	public static function UserActivationRequest($user_id) 
    {
    	$msg=[];
    	$user = DB::table('user_activation_request')->where('user_id',$user_id)->where('activation_status','0')->first();
    	if($user){
    		$msg['msg']=GStarBaseController::MSG_PENDING_REQUEST;
    	}else{
    		$insertArray=array('user_id'=>$user_id,'activation_status'=>'0','requested_at'=>strtotime("now"));
    		$insert = DB::table('user_activation_request')->insert($insertArray);
    		if($insert){
    			
    			$msg['msg']=GStarBaseController::MSG_ACTIVATION_REQUEST;
    		}else{
    			
    			$msg['msg']=GStarBaseController::MSG_SOMETHING_WORNG;
    			
    		}
    	}
	 return  $msg;
	}
	
	public static function GetRole($id)
	{
		$save=DB::table('users')->where('id','=',$id)->first();
		return $save->role;
	}



	public static function UserAuditTrailList($request) 
    {
    	// ini_set('memory_limit', '512M');
    	// set_time_limit(0);
	   	$finalArr = array();
       	$nameArr=array();
       	$inputs = Input::get();
		//$search_keyword=isset($inputs['search_keyword'])?trim($inputs['search_keyword']):'';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		if($pageNo) 
        {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		} else {
			$offset= 0;
		}

	   	$i = 0;

	   	$sql = DB::table('users');
		$sql->join('user_detail', 'user_detail.user_id','=','users.id');
		$sql->join('user_mapping', 'user_mapping.user_id','=','users.id');

	   	if(isset($inputs['search_keyword'])){
	   		$search_keyword=trim($inputs['search_keyword']);
	   		if(strpos($search_keyword, " ") != false){
	   			$nameArr = explode(' ',$search_keyword);
	   			 $sql->where('users.first_name','like','%'.$nameArr[0].'%');
				$sql->orWhere('users.last_name','like','%'.$nameArr[1].'%');
				$sql->orWhere('users.email','like','%'.$nameArr[0].'%');
	   		}else{
	   			$sql->where('users.first_name','like','%'.$search_keyword.'%');
				$sql->orWhere('users.email','like','%'.$search_keyword.'%');
	   		}
	   		
	   	}

	   	if(isset($inputs['role'])){
	   		$role=($inputs['role']);
	   		$role=explode(',', $role);   
	   		$sql->whereIn('users.role', $role);
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
	   			if($s_date< $e_date){
	   				$sql->whereBetween('users.last_login', [$s_date, $e_date]);	
	   			}
	   			
	   		}
	   	}

	   	if(isset($inputs['status'])){
	   		$status=($inputs['status']);
	   		$status=explode(',', $status);
	   		if(count($status) == 1)
	   		$sql->where('users.status', $status[0]);
	   	}

	   	if(isset($inputs['supervisor_id'])){
	   		$supervisor_id=($inputs['supervisor_id']);
	   		$supervisor_id=explode(',', $supervisor_id);
	   		$sql->whereIn('user_mapping.parent_id', $supervisor_id);
	   	}

	   	if(isset($inputs['zone'])){
	   		$zone=trim($inputs['zone']);
	   		$sql->where("user_detail.zone", 'like',($zone));
	   		
	   	}

	   	if(isset($inputs['state'])){
	   		$state=($inputs['state']);
	   		$state=explode(',', $state);
	   		if(count($state) ==1){
	   			$sql->where("user_detail.state", 'like',($state[0]));
	   		}else{
		   			$sql->where(function ($query) use ($state)
		   			{
		   				for ($countStaate=0; $countStaate <count($state) ; $countStaate++) { 
		   				$query->orWhere("user_detail.state", 'like',($state[$countStaate]));
		   				}
	            	});
	   		}
	   	}


	   	if(isset($inputs['ND'])){
	   		$ND=($inputs['ND']);
	   		$ND=explode(',', $ND);
	   		if(count($ND) ==1){
	   			$sql->where("user_detail.nd_name", 'like', ($ND[0]));
	   		}else{
		   			$sql->where(function ($query) use ($ND)
		   			{
		   				for ($countND=0; $countND <count($ND) ; $countND++) { 
		   				$query->orWhere("user_detail.nd_name", 'like',($ND[$countND]));
		   				}
	            	});
	   		}
	   	}

	   	$sql->select('users.id as userId','users.first_name','users.last_name','users.email','users.role','users.last_login','users.status','users.contact','users.created_at','users.dob','users.last_logout','users.login_count','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
	   	if(!isset($inputs['getAll'])){
	   		$sql->skip($offset);
			$sql->take(GStarBaseController::PAGINATION_LIMITS);
	   	} 
		//$sql->get();

	   	$usersDetail =$sql->get();
		$user=new User();
	   	//dd(DB::getQueryLog());

		// print_r($usersDetail);die();
if(!empty($usersDetail))
{
	foreach(array_chunk($usersDetail, 1000) as $Detail){
	    foreach($Detail as $key){
	        $loginUserRole=Auth::user()->role;
	
		if((int)$loginUserRole< (int)$key->role){
			$loginUserId=Auth::user()->id;
			if($loginUserRole == GStarBaseController::G_TRAINER_ROLE_ID){
				$sp_id = DB::table('user_mapping')->select('parent_id')->where('user_id',$key->userId)->first();
				if($sp_id){
					if($sp_id->parent_id ==$loginUserId){
					$finalArr[$i]['user_id'] = $key->userId;
					$user_id=$key->userId;
					$finalArr[$i]['email'] =$key->email;
					$finalArr[$i]['first_name'] =$key->first_name;
					$finalArr[$i]['last_name'] =$key->last_name;
					$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
					$finalArr[$i]['role'] =$key->role;
					$finalArr[$i]['status']=$key->status;
					$finalArr[$i]['contact'] =$key->contact;
					$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
					$finalArr[$i]['zone'] =$key->zone;
					$finalArr[$i]['state'] =$key->state;
					$finalArr[$i]['city'] =$key->city;
					$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
					$finalArr[$i]['rt_code'] =$key->rt_code;
					$finalArr[$i]['nd_name'] =$key->nd_name;
					$finalArr[$i]['rd_name'] =$key->rd_name;
					$finalArr[$i]['supervisor_id'] =$key->parent_id;
					$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);
					if($key->created_at){
						$finalArr[$i]['created_at'] =  date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
					} else {
						$finalArr[$i]['created_at'] = '';
					}
					if($key->last_login){
						$finalArr[$i]['last_login'] = date("d-m-Y h:i:sa", $key->last_login);
					} else{
						$finalArr[$i]['last_login'] = '';
					}
					if($key->last_logout){
						$finalArr[$i]['last_logout'] =  date("d-m-Y h:i:sa", $key->last_logout);
					} else {
						$finalArr[$i]['last_logout'] =  '' ;
					}
					$finalArr[$i]['login_count'] =$key->login_count;

					// if(!isset($inputs['getAll'])){
					// 	if(isset($inputs['start_date']) && isset($inputs['end_date']))
					// {
					// 	$start_date=trim($inputs['start_date']);
					// 	$end_date=trim($inputs['end_date']);
					// 	if($start_date !="" && $end_date !="")
					// 	{
		   // 					$s_date=strtotime($start_date);
		   // 					$e_date=strtotime($end_date);
		   // 					$e_date=strtotime('+1 day',$e_date);
		   // 					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

		   // 					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

			  //  			}else{
			  //  				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

			  //  				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
			  //  			}
					// }else{
					// 	$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

					// 	$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
					// }
					// }
					
					
					$i++;
					} else {

						$sp_id2 = DB::table('user_mapping')->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
						if($sp_id2){
							if($sp_id2->parent_id ==$loginUserId){
								$finalArr[$i]['user_id'] = $key->userId;
								$user_id=$key->userId;
								$finalArr[$i]['email'] =$key->email;
								$finalArr[$i]['first_name'] =$key->first_name;
								$finalArr[$i]['last_name'] =$key->last_name;
								$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;

								$finalArr[$i]['role'] =$key->role;
								$finalArr[$i]['status'] =$key->status;
								$finalArr[$i]['contact'] =$key->contact;
								$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
								$finalArr[$i]['zone'] =$key->zone;
								$finalArr[$i]['state'] =$key->state;
								$finalArr[$i]['city'] =$key->city;
								$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
								$finalArr[$i]['rt_code'] =$key->rt_code;
								$finalArr[$i]['nd_name'] =$key->nd_name;
								$finalArr[$i]['rd_name'] =$key->rd_name;
								$finalArr[$i]['supervisor_id'] =$key->parent_id;
								$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);

								if($key->created_at){
									$finalArr[$i]['created_at'] = date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
								} else {
									$finalArr[$i]['created_at'] = '';
								}
								if($key->last_login){
								$finalArr[$i]['last_login'] =  date("d-m-Y h:i:sa", $key->last_login);
								} else {
									$finalArr[$i]['last_login'] = '';
								}
								if($key->last_logout){
									$finalArr[$i]['last_logout'] =  date("d-m-Y h:i:sa", $key->last_logout);
								} else {
									$finalArr[$i]['last_logout'] =  '' ;
								}
								$finalArr[$i]['login_count'] =$key->login_count;
								// if(!isset($inputs['getAll'])){
								// 	if(isset($inputs['start_date']) && isset($inputs['end_date']))
								// {
								// 	$start_date=trim($inputs['start_date']);
								// 	$end_date=trim($inputs['end_date']);
								// 	if($start_date !="" && $end_date !="")
								// 	{
					   // 					$s_date=strtotime($start_date);
					   // 					$e_date=strtotime($end_date);
					   // 					$e_date=strtotime('+1 day',$e_date);
					   // 					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

					   // 					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
								// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
								// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
								// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

								// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

						  //  			}else{
						  //  				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

						  //  				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
								// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
								// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
								// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

								// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
						  //  			}
								// }else{
								// 	$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

								// 	$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
								// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
								// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
								// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

								// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
								// }
								// }	
								
								$i++;
							}
						}
					} 
				}
					
			} else if((int)$loginUserRole <= GStarBaseController::G_ADMIN_ROLE_ID){
					$finalArr[$i]['user_id'] = $key->userId;
					$user_id=$key->userId;
					$finalArr[$i]['email'] =$key->email;
					$finalArr[$i]['first_name'] =$key->first_name;
					$finalArr[$i]['last_name'] =$key->last_name;
					$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;

					$finalArr[$i]['role'] =$key->role;
					$finalArr[$i]['status'] =$key->status;
					$finalArr[$i]['contact'] =$key->contact;
					$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
					$finalArr[$i]['zone'] =$key->zone;
					$finalArr[$i]['state'] =$key->state;
					$finalArr[$i]['city'] =$key->city;
					$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
					$finalArr[$i]['rt_code'] =$key->rt_code;
					$finalArr[$i]['nd_name'] =$key->nd_name;
					$finalArr[$i]['rd_name'] =$key->rd_name;
					$finalArr[$i]['supervisor_id'] =$key->parent_id;
					$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);

					if($key->created_at){

						$finalArr[$i]['created_at'] =  date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
					} else {
						$finalArr[$i]['created_at'] = '';
					}
					if($key->last_login){

							$finalArr[$i]['last_login'] = date("d-m-Y h:i:sa", $key->last_login);
					} else {
						$finalArr[$i]['last_login'] = '';
					}
					if($key->last_logout){
						$finalArr[$i]['last_logout'] =  date("d-m-Y h:i:sa", $key->last_logout);
					} else {
						$finalArr[$i]['last_logout'] =  '' ;
					}
					$finalArr[$i]['login_count'] =$key->login_count;
					// if (!isset($inputs['getAll'])) {
					// 	if(isset($inputs['start_date']) && isset($inputs['end_date']))
					// {
					// 	$start_date=trim($inputs['start_date']);
					// 	$end_date=trim($inputs['end_date']);
					// 	if($start_date !="" && $end_date !="")
					// 	{
		   // 					$s_date=strtotime($start_date);
		   // 					$e_date=strtotime($end_date);
		   // 					$e_date=strtotime('+1 day',$e_date);
		   // 					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

		   // 					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

			  //  			}else{
			  //  				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

			  //  				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
			  //  			}
					// }else{
					// 	$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

					// 	$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
					// 		$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
					// 		$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
					// 		$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

					// 		$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
					// }
					// }
					
					
					$i++;
			}
			
		}
		
	
	    }
	}
	
}
		
	 	return  $finalArr;
	}


	public static function ActiveInactiveUserList($request) 
    {
	   	$finalArr = array();
       	$nameArr=array();
       	$inputs = Input::get();
		//$search_keyword=isset($inputs['search_keyword'])?trim($inputs['search_keyword']):'';
		$pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		if($pageNo) 
        {
            $offset=($pageNo-1)*GStarBaseController::PAGINATION_LIMITS;
		} else {
			$offset= 0;
		}

	   	$i = 0;

	   	$sql = DB::table('users');
		$sql->join('user_detail', 'user_detail.user_id','=','users.id');
		$sql->join('user_mapping', 'user_mapping.user_id','=','users.id');

	   	if(isset($inputs['search_keyword'])){
	   		$search_keyword=trim($inputs['search_keyword']);
	   		if(strpos($search_keyword, " ") != false){
	   			$nameArr = explode(' ',$search_keyword);
	   			 $sql->where('users.first_name','like','%'.$nameArr[0].'%');
				$sql->orWhere('users.last_name','like','%'.$nameArr[1].'%');
				$sql->orWhere('users.email','like','%'.$nameArr[0].'%');
	   		}else{
	   			$sql->where('users.first_name','like','%'.$search_keyword.'%');
				$sql->orWhere('users.email','like','%'.$search_keyword.'%');
	   		}
	   		
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
	   			// echo $date1=date("d-m-Y h:i:sa", ($s_date));
	   			// echo $date2=date("d-m-Y h:i:sa", ($e_date));
	   			// die;
	   			if($s_date< $e_date){
	   				$sql->whereBetween('users.last_login', [$s_date, $e_date]);	
	   			}
	   			
	   		}
	   	}

	   	if(isset($inputs['status'])){
	   		$status=trim($inputs['status']);
	   		$sql->where('users.status', $status);
	   		//$sql->whereIn('users.status', $status);
	   	}
	   	
	   	if(isset($inputs['role']))
	   	{
	   		$role=($inputs['role']);
	   		$role=explode(',', $role);   
	   		$sql->whereIn('users.role', $role);
	   	}
	   	
	   	if(isset($inputs['supervisor_id'])){
	   		$supervisor_id=($inputs['supervisor_id']);
	   		$supervisor_id=explode(',', $supervisor_id);
	   		$sql->whereIn('user_mapping.parent_id', $supervisor_id);
	   	}

	   	$sql->select('users.id as userId','users.first_name','users.last_name','users.email','users.role','users.last_login','users.status','users.contact','users.created_at','users.dob','users.last_logout','users.login_count','user_detail.city','user_detail.state','user_detail.zone','user_detail.beat_route_id','user_detail.rt_code','user_detail.nd_name','user_detail.rd_name','user_mapping.parent_id');
		$sql->skip($offset);
		$sql->take(GStarBaseController::PAGINATION_LIMITS);
		//$sql->get();

	   	$usersDetail = $sql->get();

		$user=new User();

		if(!empty($usersDetail))
		{
			$loginUserRole=Auth::user()->role;

			foreach(array_chunk($usersDetail, 1000) as $Detail){
			    foreach($Detail as $key){
			    	if((int)$loginUserRole< (int)$key->role){
					$loginUserId=Auth::user()->id;
					if($loginUserRole == GStarBaseController::G_TRAINER_ROLE_ID){
						$sp_id = DB::table('user_mapping')->select('parent_id')->where('user_id',$key->userId)->first();
						if($sp_id){
							if($sp_id->parent_id ==$loginUserId){
								
									$finalArr[$i]['user_id'] = $key->userId;
									$user_id=$key->userId;
									$finalArr[$i]['email'] =$key->email;
									$finalArr[$i]['first_name'] =$key->first_name;
									$finalArr[$i]['last_name'] =$key->last_name;
									$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;
									$finalArr[$i]['role'] =$key->role;
									$finalArr[$i]['status'] =$key->status;
									$finalArr[$i]['contact'] =$key->contact;
									$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
									$finalArr[$i]['zone'] =$key->zone;
									$finalArr[$i]['state'] =$key->state;
									$finalArr[$i]['city'] =$key->city;
									$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
									$finalArr[$i]['rt_code'] =$key->rt_code;
									$finalArr[$i]['nd_name'] =$key->nd_name;
									$finalArr[$i]['rd_name'] =$key->rd_name;

									$finalArr[$i]['Last_login_date']=null;
									if($key->last_login)
									$finalArr[$i]['Last_login_date'] =  date("d-m-Y h:i:s a",$key->last_login);
								//date("d-m-Y h:i:sa",$key->last_login);


									$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);
									if($key->created_at){
										$finalArr[$i]['created_at'] =  date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
									} else {
										$finalArr[$i]['created_at'] = '';
									}	
									$i++;
							} else {

								$sp_id2 = DB::table('user_mapping')->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
								if($sp_id2){
									if($sp_id2->parent_id ==$loginUserId){
										$finalArr[$i]['user_id'] = $key->userId;
										$user_id=$key->userId;
										$finalArr[$i]['email'] =$key->email;
										$finalArr[$i]['first_name'] =$key->first_name;
										$finalArr[$i]['last_name'] =$key->last_name;
										$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;

										$finalArr[$i]['role'] =$key->role;
										$finalArr[$i]['status'] =$key->status;
										$finalArr[$i]['contact'] =$key->contact;
										$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
										$finalArr[$i]['zone'] =$key->zone;
										$finalArr[$i]['state'] =$key->state;
										$finalArr[$i]['city'] =$key->city;
										$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
										$finalArr[$i]['rt_code'] =$key->rt_code;
										$finalArr[$i]['nd_name'] =$key->nd_name;
										$finalArr[$i]['rd_name'] =$key->rd_name;
										$finalArr[$i]['Last_login_date']=null;
									if($key->last_login)
									// $finalArr[$i]['Last_login_date'] = $key->last_login."000";
										$finalArr[$i]['Last_login_date'] =  date("d-m-Y h:i:s a",$key->last_login);
									// date("d-m-Y h:i:sa",$key->last_login);
								
									$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);

									if($key->created_at){
										$finalArr[$i]['created_at'] =  date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
									} else {
										$finalArr[$i]['created_at'] = '';
									}
									
									$i++;
									}
								}
							} 
						}
							
					} else if((int)$loginUserRole <= GStarBaseController::G_ADMIN_ROLE_ID){
							$finalArr[$i]['user_id'] = $key->userId;
							$user_id=$key->userId;
							$finalArr[$i]['email'] =$key->email;
							$finalArr[$i]['first_name'] =$key->first_name;
							$finalArr[$i]['last_name'] =$key->last_name;
							$finalArr[$i]['user_name'] =$key->first_name.' '.$key->last_name;

							$finalArr[$i]['role'] =$key->role;
							$finalArr[$i]['status'] =$key->status;
							$finalArr[$i]['contact'] =$key->contact;
							$finalArr[$i]['dob'] =date("d-m-Y", strtotime($key->dob));
							$finalArr[$i]['zone'] =$key->zone;
							$finalArr[$i]['state'] =$key->state;
							$finalArr[$i]['city'] =$key->city;
							$finalArr[$i]['beat_route_id'] =$key->beat_route_id;
							$finalArr[$i]['rt_code'] =$key->rt_code;
							$finalArr[$i]['nd_name'] =$key->nd_name;
							$finalArr[$i]['rd_name'] =$key->rd_name;
							$finalArr[$i]['Last_login_date']=null;
							if($key->last_login)
								// $finalArr[$i]['Last_login_date'] = $key->last_login."000";
								$finalArr[$i]['Last_login_date'] =  date("d-m-Y h:i:s a",$key->last_login);
								// date("d-m-Y h:i:sa",$key->last_login);
							
							$finalArr[$i]['supervisor_name'] =$user->getusername($key->parent_id);

							if($key->created_at){
								$finalArr[$i]['created_at'] =  date("d-m-Y h:i:sa", strtotime($key->created_at)) ;
							} else {
								$finalArr[$i]['created_at'] = '';
							}
							
							$i++;
					}
					
				}
			   }
			}    	
			
		}

	 	return  $finalArr;
	}

	public static function SaveUserAuditTrail($userId,$data){

		$inserArr=array();
		for ($i=0; $i <count($data) ; $i++) { 
			$inserArr[$i]['module_access']=$data[$i]['module_name'];
			$inserArr[$i]['access_time']=$data[$i]['access_time'];
			$inserArr[$i]['login_time']=$data[$i]['login_time'];
			$inserArr[$i]['user_id']=$userId;
		}
		$save=DB::table('saveuser_trail')->insert($inserArr);
		return $save;
	}

	public static function GetUserModuleAccess($userId,$s_date,$e_date){

		if($s_date !=null && $e_date !=null){
			$ModuleAccessDetail=DB::table('saveuser_trail')
							->select('login_time')
							->where('user_id',$userId)
							->groupBy('login_time')
							->whereBetween('login_time', [$s_date, $e_date])
							->distinct('login_time')
							->orderBy('id','desc')
							->get();
		}else{
			$ModuleAccessDetail=DB::table('saveuser_trail')
							->select('login_time')
							->where('user_id',$userId)
							->groupBy('login_time')
							->distinct('login_time')
							->orderBy('id','desc')
							->get();	
		}
		
		if($ModuleAccessDetail){
			$i=0;
			$finalArr=array();
			//return $ModuleAccessDetail;
			foreach ($ModuleAccessDetail as $key ) {
				$finalArr[$i]['login_time']=date('d-m-Y h:i:s a',$key->login_time);
				$finalArr[$i]['module']='';
				$Products=DB::table('saveuser_trail')
						->where('login_time',$key->login_time)
						->where(DB::raw("LOWER(module_access)"), 'LIKE', '%'.strtolower('Products').'%')
						->count();

				$Recommender=DB::table('saveuser_trail')
						->where('login_time',$key->login_time)
						->where(DB::raw("LOWER(module_access)"), 'LIKE', '%'.strtolower('Recommender').'%')
						->count();	


				$Updates=DB::table('saveuser_trail')
						->where('login_time',$key->login_time)
						->where(DB::raw("LOWER(module_access)"), 'LIKE', '%'.strtolower('Updates').'%')
						->count();	

				$Tutorials=DB::table('saveuser_trail')
						->where('login_time',$key->login_time)
						->where(DB::raw("LOWER(module_access)"), 'LIKE', '%'.strtolower('Tutorials').'%')
						->count();						

				$finalArr[$i]['module']['product']='Products';
				$finalArr[$i]['module']['product_count']=0;
				if($Products >0){
					$finalArr[$i]['module']['product_count']=$Products;
				}

				$finalArr[$i]['module']['recommender']='Recommender';
				$finalArr[$i]['module']['recommender_count']=0;
				if($Recommender >0){
					$finalArr[$i]['module']['recommender_count']=$Recommender;
				}

				$finalArr[$i]['module']['update']='Updates';
				$finalArr[$i]['module']['update_count']=0;
				if($Updates >0){
					$finalArr[$i]['module']['update_count']=$Updates;
				}

				$finalArr[$i]['module']['tutorial']='Tutorials';
				$finalArr[$i]['module']['tutorial_count']=0;
				if($Tutorials >0){
					$finalArr[$i]['module']['tutorial_count']=$Tutorials;
				}

				$i++;					
			}
			return $finalArr;
		}else{
			return null;
		}	

	}

	public static function GetLastUsedDate($userId,$module,$s_date,$e_date){
		$date=null;
		if($s_date !=null && $e_date !=null){
			$get=DB::table('saveuser_trail')
				->select('access_time')
				->where('user_id',$userId)
				->where('module_access','=',$module)
				 ->whereBetween('access_time', [$s_date."000", $e_date."000"])
				->orderBy('id','desc')
				->first();
		}else{
			$get=DB::table('saveuser_trail')
				->select('access_time')
				->where('user_id',$userId)
				->where('module_access','=',$module)
				->orderBy('id','desc')
				->first();
		}
		
		if($get){

			$seconds = $get->access_time / 1000;
			$date = date("d-m-Y H:i:s a",$seconds);
			//$date=$date2.' '.$seconds;
		}
		return $date;		
	}

	public static function GetModuleVisitedCount($userId,$s_date,$e_date)
	{
		$count=null;
		if($s_date !=null && $e_date !=null){
			$Detail=DB::table('saveuser_trail')
							->where('user_id',$userId)
							->whereBetween('access_time', [$s_date."000", $e_date."000"])
							->count('module_access');
		}else{
			$Detail=DB::table('saveuser_trail')
							->where('user_id',$userId)
							->count('module_access');
		}
		
		if($Detail){
			$count=$Detail;
		}
		return $count;					
	}



	/* inactive users will be removed from database after an year */
	public static function ListofArchiveUserToBeDeleted()
	{
		$finalArr=array();
		$sql=DB::table('users');
        $sql->select('users.id');
        $sql->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID);
        $sql->where('users.status','=','0');
        $sql->orderBy('users.created_at', 'desc');
		$sql->where(function ($query)
		{
		   	$archive_date=strtotime("-1 year");
			$query->where(function ($query2) use ($archive_date)
			{
			   	$query2->where('users.last_login','<',$archive_date);
			   	$query2->where('users.last_login','!=',"");			
		    });     
		    $query->orWhere(function ($query3)
		   	{
			   	$created_date=date('Y-m-d',strtotime("-1 year"));
			   	$query3->where('created_at','<',$created_date.' 00:00:00');
			   	$query3->where('users.last_login','=',"");		
	        });	
	    });
	    $userDetails=$sql->get();

	    if($userDetails){
	    	foreach ($userDetails as $key ) {
	    		$finalArr[]=$key->id;
	    	}
	    }
	    return $finalArr;
	}

	public static function DeleteArchiveUser($id)
	{
		$result_array = array(); 
	    $delete1 = '';
		$delete2 = '';
		$delete3 = '';
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
        }
        return $result_array;
	}


	public static function GetDisclaimer()
	{
		$result_array = array(); 
		$result_array['disclaimer_text'] = ""; 
	    $data=DB::table('disclaimer')->where('id',1)->first();
	    if($data){
	    	$result_array['disclaimer_text']=$data->disclaimer_text;
	    }
        return $result_array;
	}

	public static function UpdateDisclaimer($disclaimer_text)
	{
		
		$data=DB::table('disclaimer')
            ->where('id', 1)
            ->update(['disclaimer_text' =>$disclaimer_text]);
        return $data;
	}

	

}///end of User model
