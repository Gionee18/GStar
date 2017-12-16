<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware;
use LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;
use Auth, DB ; 

class GStarBaseController extends Controller
{
   
	
	public function __construct()
    {
       date_default_timezone_set("Asia/Calcutta");
    }
   
   //File Upload Path
	const FILE_PATH = "C:\wamp\www\gstar\services\uploads";

	/* FCM ACCESS Key */
	//const FCM_API_ACCESS_KEY='AIzaSyALyLF3fI2oRczX0kf_DUXj94xPK16es8A';
	const FCM_API_ACCESS_KEY='AIzaSyCQISJi8YX0y6qS0JNbTCggMLGj9gdJ2q0';

	

	const NEW_TOPIC_CREATE='New Topic Created!';

	const EDIT_USER_PUSH='Edit User Request!';
	
	
	const PAGE_NUMBER =0;
	const PAGINATION_LIMITS = 100;
	const TOPICPAGINATION_LIMITS = 20;
	const FILE_SIZE = 20000000;
	const VIDEO_FILE_SIZE = 100000000;
	
	//Application Role ID
	const G_SUPERADMIN_ROLE_ID = 05;   // Gionee Super Admin
	const G_ADMIN_ROLE_ID = 10;   // Gionee Admin
	const G_TRAINER_ROLE_ID = 20; // Gionee Trainer
	const G_SUPERVISOR_ROLE_ID = 30; // Gionee Supervisor
	const G_LEARNER_ROLE_ID = 40; // Gionee Learner


	const ADMIN_REJECT_REQUEST = '2'; 


	const CAMERA_CONSTANT = 20; 
	const BATTERY_CONSTANT = 500;
	const RAM_CONSTANT = 4;
	const INTERNALMM_CONSTANT=12;
	const CPU_CONSTANT=4;

	const OTHER_BRAND = 'others';
	const GIONEE_BRAND = 'gionee'; 
	
	
	//Application Constant key
	const RESULT_TEXT = "result";
	const ERROR_TEXT = "error";
	const STATUS_TEXT = "status";
	const ERROR_CODE = "error_code";
	const APPLICATION_TYPE = "application/json";
	const SUCCESS_TEXT = "success";
	const MSG_TEXT = "msg";
	const USERS = 'users';
	const USER_DETAILS = 'user_details';
	const MSG_EDIT_FILE = "File Edit successfully!";
	
	//Application Error Codes
	const ERROR_NOT_AUTHORIZED = 1001;
	const ERROR_SESSION_TIMEOUT = 1002;
	const ERROR_ARGUMNETS_MISSING = 1003;
	const ERROR_BAD_PARAMETERS = 1004;
	const ERROR_EMAIL_NOT_EXIST = 1005;
	const ERROR_LOGOUT = 1006;
	const ERROR_FORGET_PASSWORD = 1007;
	const ERROR_RESET_PSWD = 1009;
	const ERROR_ADDED_USER = 1010;
	const ERROR_RECORD_UPDATED = 1011;
	const ERROR_RECORD_DELETED = 1012;
	const ERROR_NO_UPDATE = 1013;
	const ERROR_NO_RECORD = 1014;
	const ERROR_NO_AUTHENTICATE = 1015;
	const ERROR_ADDED_ND = 1016;
	const ERROR_FILE_NOT_UPLOADED = 1017;
	const ERROR_USER_NOT_EXIST = 1018;
	const ERROR_USER_ALREADY_ACTIVE = 1019;
	const ERROR_CATEGORY_NOT_EXIST = 1020;
	
	
	 
	
	//Application Message Text
	const MSG_NOT_AUTHORIZED = "Not Authorized!";
	const MSG_TUTORIAL_SUBCAT_EXIST = "and tutorial subcategory exist!";
	const MSG_USER_ALREADY_ACTIVE = "User already active!";
	const MSG_PENDING_REQUEST = "You already requested for account activation. Waiting for admin approval!";
	const MSG_ACTIVATION_REQUEST = "Your account activation request successfully. Waiting for admin approval!";
	const MSG_SOMETHING_WORNG = 'Something want worng!';
	const MSG_SESSION_TIMEOUT = "Session Timeout!";
	const MSG_ARGUMNETS_MISSING = "Argument Missing!";
	const MSG_BAD_PARAMETERS = "Bad Parameteres!";
	const MSG_LOGOUT = "You have been successfully logged out!";
	const MSG_USER_ACTIVATE = "User activated successfully!";
	const MSG_USER_ACTIVATE_REJECT = "User activated request rejected successfully!";
	const MSG_FORGET_PASSWORD = "Password has been send to your registered Email-id!";
	const MSG_EMAIL_NOT_EXIST = "Email not exist!";
	const MSG_EMAIL_EXIST = "E-mail already registered!";
	const MSG_USER_DELETED = " users Deleted!";
	const MSG_EMAIL_NOT_REGIS = "Please provide your registered E-mail ID!";
	const MSG_EMAIL_ENTER_REGIS = "Enter your registered E-mail ID!";
	const MSG_RESET_PSWD = "Password reset successfully!";
	const MSG_PSWD_NOT_MATCH = "Old Password does not match!";
	const MSG_NEWPSWD_NOT_MATCH = "New Password and Confirm Password does not match!";
	const MSG_ADDED_USER = "User registered successfully!";
	const MSG_RECORD_UPDATED = "Record updated successfully!";
	const MSG_RECORD_UPDATED_ADMIN_APPROVE = "Record updated successfully. Waiting for admin approval!";
	const MSG_RECORD_UPDATED_ADMIN_APPROVE_SUCCESS = "Record approved successfully!";
	const MSG_RECORD_UPDATED_ADMIN_APPROVE_FAIL = "Record already approved or no update found!";
	const MSG_RECORD_ADMIN_REJECT_FAIL = "Record already rejected!";
	const MSG_RECORD_ADMIN_REJECT_SUCCESS = "Record rejected successfully!";
	const MSG_RECORD_DELETED = "Record Deleted Successfully!";

	const MSG_RECORD_ADDED = "Record added successfully!";
	const MSG_NO_UPDATE = "Please provide some changes!";
	const MSG_NO_UPDATE2 = "Please provide some changes. No update found!";
	const MSG_NO_RECORD = "No records found!";
	const MSG_ADDED_CATEGORY = "Category Added successfully!";
	const MSG_ADDED_TUTORIAL = "Tutorial Added successfully!";
	const MSG_ADDED_TUTORIALSUBCAT = "Tutorial Subcategory Added successfully!";
	const MSG_ADDED_NEWS_UPDATE = "Category added successfully in News/Updates!";
	const MSG_ADDED_NEWS_UPDATE_SUB = "Subcategory added successfully in News/Updates!";
	const MSG_ADDED_NEWS_TOPIC = "Topic added successfully in News/Updates!";

	const MSG_READ_TOPIC = "Read Topic By User inserted successfully!";
	const MSG_READ_TOPIC_FAIL = "Error in read topic By User!";
	const MSG_READ_TOPIC_USER = "Topic already read by User!";
	const MSG_READ_TOPIC_PARAMETRE = "Missing Parameter for read topic!";
	
	const MSG_SAVE_DEVICE_ERROR = "Missing Parameter device_id or device_token!";
	const MSG_SAVE_DEVICE = "Device added successfully!";

	const MSG_SAVE_AUDIT_ERROR = "Missing Parameter user_id,login_time or data !";
	const MSG_SAVE_AUDIT_ERROR_USER = "Mismatch user_id and access_token !";
	const MSG_SAVE_AUDIT = "User audit added successfully!";


	const MSG_AC_DEACTIVATE = "Your Account has been deactivate. Please contact Admin!";
	const MSG_ADDED_PRODUCT = "Product Added successfully!";
	const MSG_UPLOADED_FILE = "File Uploaded successfully!";
	const MSG_UPLOADED_FILE_ADMIN = "File Uploaded successfully. Waiting for approval!";
	const MSG_ATTACHED_FILE = "File Attached successfully!";
	const MSG_ATTACHED_FILE_ADMIN = "File Attached successfully. Waiting for approval!";
	const MSG_UPLOADED_IMAGE = "Image Uploaded successfully!";
	const MSG_ATTACHED_IMAGE = "Image Attached successfully!";
	const MSG_ATTACHED_IMAGE_ADMIN = "Image Attached successfully. Waiting for approval!";
	const MSG_FILE_NOT_UPLOADED = "File not Uploaded!";
	const MSG_CAT_ALREADY_EXIST = "Category already registered!";
	const MSG_ALREADY_EXIST = "Duplicate entry!";
	const MSG_NO_AUTHENTICATE = "Either Email Or Password Incorrect!";
	const MSG_EXCEL_IMPORT = "Excel Imported Successfully!";	
	const MSG_IMAGE_EXIST = "Image exists";
	const MSG_USER_NOT_EXIST = "User does not exist!";
	const MSG_FILE_SIZE = "Max file size should be less than 1 MB!";
	const MSG_VIDEO_SIZE = "Max file size should be less than or equal to 100 MB!";
	const MSG_FILE_FORMAT = "Invalid file format!";
	const MSG_EMPTY_ROW = "Please provide valid file which does not contain empty rows.";
	const MSG_WRONG_FILE_SIZE = "Max file size should be less than 5MB!";
	const MSG_NOT_VALID_FILE = "select valid file";
	const MSG_NOT_SELECTED = "Please select at least one.";

	const MSG_ADDED_MANUFACTURER = "Manufacturer Added successfully!";
	const MSG_ADDED_MODEL = "Model Added successfully!";
	const MSG_ADDED_MODEL_SPECIFICATION = "Specification added successfully inside model!";
	const MSG_UPDATE_MODEL_SPECIFICATION = "Specification update successfully inside model!";

	const MSG_PRODUCT_EXIST = "First delete Product inside this Category";
	const MSG_TUTORIAL_EXIST = "First delete Tutorial inside this Category";
	const MSG_TOPIC_EXIST = "First delete Topics inside this Category!";
	const MSG_TOPIC_EXIST_SUBCAT = "First delete Topics inside this Subcategory!";

	const MSG_SUBCAT_EXIST = "First delete Subcategory inside this Category!";
	const MSG_MODEL_EXIST = "First delete Model inside this Manufacturer!";

	const MSG_SPEC_SUBCAT_EXIST = "First delete subcategory inside this Category!";
	const MSG_TUTORIAL_EXIST_FOR_PRODUCT = "First delete tutorials inside this Product!";
	const MSG_TUTORIAL_EXIST_FOR_CATEGORY = "First delete tutorials inside this Category!";
	const MSG_CATEGORY_NOT_EXIST = "Category not exist!";
	const MSG_TUTORIAL_EXIST_FOR_SUBCATEGORY = "First delete tutorials inside this Subcategory!";

	const MSG_SEARCH_ATTRIBUTE = "Searching category and subcategory cannot be deleted!";
	
	const ERROR_IMAGE_ATTACH_EXIST="Image already exists in this module";


	const MSG_DISCLAIMER = "Disclaimer updated successfully!";

	const SERVICE_VERSION = "1";






	
	public function validateUser($rolePerm, $checkInput){ 
		//echo $rolePerm; echo Auth::user()->role;
		//print_r(Input::get());
		$content = null;
		//print_r();
		if(!Auth::check()){
			$content = [GStarBaseController::RESULT_TEXT => array(GStarBaseController::ERROR_CODE => GStarBaseController::ERROR_SESSION_TIMEOUT,GStarBaseController::MSG_TEXT => GStarBaseController::MSG_SESSION_TIMEOUT), GStarBaseController::STATUS_TEXT => GStarBaseController::ERROR_TEXT];			
		}
		else if(Auth::user()->role >= $rolePerm){
			$content = [GStarBaseController::RESULT_TEXT => array(GStarBaseController::ERROR_CODE => GStarBaseController::ERROR_NOT_AUTHORIZED,GStarBaseController::MSG_TEXT => GStarBaseController::MSG_NOT_AUTHORIZED), GStarBaseController::STATUS_TEXT => GStarBaseController::ERROR_TEXT];
		}
		else if($checkInput && !Input::get()){ 
			$content = [GStarBaseController::RESULT_TEXT => array(GStarBaseController::ERROR_CODE => GStarBaseController::ERROR_ARGUMNETS_MISSING,GStarBaseController::MSG_TEXT => GStarBaseController::MSG_ARGUMNETS_MISSING), GStarBaseController::STATUS_TEXT => GStarBaseController::ERROR_TEXT];
		}
		return $content;	
	}
	
	
	public function verify($username, $password)
	{
		$credentials = [
		'email'    => $username,
		'password' => $password,
		];

		if (Auth::once($credentials)) {
		return Auth::user()->id;
		}else{
		return false;
		}
	}
	
	public function queryResponseBuilder($key, $val){
		$content = null;
		$content = [GStarBaseController::RESULT_TEXT => array($key => $val), GStarBaseController::STATUS_TEXT => GStarBaseController::SUCCESS_TEXT];
		return $content;
	}
	
	public function errorResponseBuilder($error,$msg, $data =null){
		$content = null;
		if($data != null)
		{
			
			$response  = array(GStarBaseController::ERROR_CODE => $error,GStarBaseController::MSG_TEXT => $msg ) ;
			$response = array_merge( $response ,  $data );
			$content = [GStarBaseController::RESULT_TEXT => $response , GStarBaseController::STATUS_TEXT => GStarBaseController::ERROR_TEXT];
		}
		else		
		{	
			$content = [GStarBaseController::RESULT_TEXT => array(GStarBaseController::ERROR_CODE => $error,GStarBaseController::MSG_TEXT => $msg), GStarBaseController::STATUS_TEXT => GStarBaseController::ERROR_TEXT];
	
		}
	
		return $content;
	}
	
	
	
	public function reponseBuilder($content){
		return Response::json($content);
	}
	
	
	
	//Function to convert DB Format Date
	public function dbDateFormat( $inputs ){ 
		$dateArray = array();
		$dateArray['start_date']  = $inputs['start_date'] ;
		$dateArray['end_date']  = $inputs['end_date'] ;
		if($dateArray){
			
			date_default_timezone_set("Asia/Kolkata");
			foreach( $dateArray as $key => $val )
			{
				if(!empty($val)){
					$input[$key] = date("Y-m-d", strtotime( $val ) ) ;
				}else{
					$input[$key] = $val;
				}
			}
			$inputs = array_merge( $inputs , $input);
		}
		
		return $inputs ;
	
	}
	
	//Function to convert Date to DD-MM-YYYY
	public function userDateFormat( $inputs ){ 

		if(is_array($inputs)){ 
 
			foreach($inputs as $key => $value){ 
				$dateArray = array();
				$dateArray['start_date']  = $value->start_date ;
				$dateArray['end_date']  = $value->end_date ;
				if($dateArray){
					
					date_default_timezone_set("Asia/Kolkata");
					
					foreach( $dateArray as $key1 => $val )
					{
						if(!empty($val)){
							$input[$key1] = date("d-m-Y", strtotime( $val ) ) ;
						}else{
							$input[$key] = $val;
						}
						
						
					}
					$inputs[$key]->start_date = $input['start_date'];
					$inputs[$key]->end_date = $input['end_date'];
				}
				
			}
		}else if($inputs){ 
			$dateArray = array();
			$dateArray['start_date']  = $inputs->start_date ;
			$dateArray['end_date']  = $inputs->end_date ;
			if($dateArray){
				
				date_default_timezone_set("Asia/Kolkata");
				foreach( $dateArray as $key => $val )
				{
					$input[$key] = date("d-m-Y", strtotime( $val ) ) ;
				}
				$inputs->start_date = $input['start_date'];
				$inputs->end_date = $input['end_date'];
			}
		}
		return $inputs ;
	
	}
	
		public static function userdatesFormat( $inputs ){ 
		if(is_array($inputs)){ 
			foreach($inputs as $key => $value){ 
				$dateArray = array();
				$dateArray['created_at']  = $value->created_at ;
				$dateArray['updated_at']  = $value->updated_at ;
				if($dateArray){
					
					date_default_timezone_set("Asia/Kolkata");
					foreach( $dateArray as $key1 => $val )
					{
						if(!empty($val)){
							$input[$key1] = date("d-m-Y", strtotime( $val ) ) ;
						}else{
							$input[$key] = $val;
						}
					}
					$inputs[$key]->created_at = $input['created_at'];
					if(!empty($input['updated_at']))
     					$inputs[$key]->updated_at = $input['updated_at'];
					}
				
			}
		}else if($inputs){ 
			$dateArray = array();
			$dateArray['created_at']  = $inputs->created_at ;
			$dateArray['updated_at']  = $inputs->updated_at ;
			if($dateArray){
				
				date_default_timezone_set("Asia/Kolkata");
				foreach( $dateArray as $key => $val )
				{
					$input[$key] = date("d-m-Y", strtotime( $val ) ) ;
					
				}
				$inputs->created_at = $input['created_at'];
				$inputs->updated_at = $input['updated_at'];
			}
		}
		return $inputs ;
	
	}
	
	
	//Function to convert DB Format Date
	public static function dbDateFormatModel( $inputs ){ 
		$dateArray = array();
		$dateArray['start_date']  = $inputs['start_date'] ;
		$dateArray['end_date']  = $inputs['end_date'] ;
		if($dateArray){
			
			date_default_timezone_set("Asia/Kolkata");
			foreach( $dateArray as $key => $val )
			{
				if(!empty($val)){
					$input[$key] = date("Y-m-d", strtotime( $val ) ) ;
				}else{
					$input[$key] = $val;
				}
			}
			$inputs = array_merge( $inputs , $input);
		}
		
		return $inputs ;
	
	}
	
     public static function validateForExist($tableName,$value,$coloumnName) 
     {

		$alreadyExist = DB::table($tableName)->select($coloumnName)->where($coloumnName,'=',$value)->count(); 
		if($alreadyExist){
			return $alreadyExist;
		}else {
		return false ;
		}				
    }
	
     public static function deleteLog($type,$id) 
     {

		$res = DB::table('delete_log')->insert(
		    array('type' => $type,
		          'module_id' => $id)
		);		
		return $res;		
    }

    	public static function verifyfile($file_extension,$file_type)
  {
	  if($file_type == 'document')
	  {
		$type = array('pdf','PDF','xls','XLS','xlsx','XLSX','doc','DOC','txt','TXT','docx','DOCX','PPT','ppt','PPTX','pptx','CSV','csv');  
		$value = in_array($file_extension,$type);
		
	  }
	  else if($file_type == 'image')
	  {
		//$type = array('jpg','JPG','png','PNG','JPEG','jpeg');  
		$type = array('jpg','JPG','png','PNG','JPEG','jpeg','gif','GIF');  
		$value = in_array($file_extension,$type);
		
	  }
	  else if($file_type == 'file')
	  {
		$type = array('jpg','JPG','png','PNG','pdf','PDF','xls','XLS','doc','DOC','txt','TXT','xlsx','XLSX','docx','DOCX','mp4','MP4','3gp','3GP','PPT','ppt','PPTX','pptx','JPEG','jpeg','gif','GIF','CSV','csv');
		$value = in_array($file_extension,$type);
		 
	  }
	  
	   else if($file_type == 'homebanner')
	  {
		$type = array('jpg','JPG','png','PNG','gif','GIF','JPEG','jpeg');
		$value = in_array($file_extension,$type);
		 
	  }
	  return $value;
  }



  public static function get_hashtags($string, $str = 1)
	 {
		 preg_match_all('/#(\w+)/',$string,$matches);
		  $i = 0;
		  //print_r($matches);
		  if(!empty($matches[1])){
			  	if ($str) {
					   foreach ($matches[1] as $match) 
					   {
						   $count = count($matches[1]);
						   $keywords .= "$match";
						    $i++;
						    if ($count > $i) $keywords .= ", ";
					   }
				  }else{

					  foreach ($matches[1] as $match) 
					  {
					  	$keyword[] = $match;
					  }
					  $keywords = $keyword;
				 }
				return $keywords;
		}else{
			return null;
		}

	}


	public static function saveHashtag($saveId,$HashtagArray,$brand)
	{
		$product_id=$saveId;
		for ($i=0; $i <count($HashtagArray) ; $i++) { 
			$savearray=array('product_id'=>$product_id,'hash_kay'=>$HashtagArray[$i],'brand'=>$brand);
			$save=DB::table('product_hashtags')->insertGetId($savearray);
		}
		return $save;
		
		
	}


	public static function super_unique($array,$key)
	{
		$temp_array = array();
		foreach ($array as &$v) {
		    if (!isset($temp_array[$v[$key]]))
		     $temp_array[$v[$key]] =& $v;
		}
		$array = array_values($temp_array);
		return $array;
	}


	public static function EditUserProfileUrl(){
		$url="/#/approval/edit-users-profile";
		$base=url('/');
		$baseSplit=explode('/services', $base);
		$return_Url=$baseSplit[0].$url;
		return $return_Url;
	}
	//http://localhost/gstar/#/approval/edit-users-profile




public static function createThumb($filename,$targetfilePath,$extensionofFile) 
 { 
		$Current_month = date('M');
		$Current_date = date('d-m-y');
		if (!is_dir(base_path('uploads'). '/'.$Current_month . '/' . $Current_date.'/thumbnail')) 
		{
		 mkdir(base_path('uploads'). '/'. $Current_month . '/' . $Current_date.'/thumbnail', 0777, true); 
		}

		$targetPath_thumb = base_path('uploads'). '/'.$Current_month . '/' . $Current_date.'/thumbnail'; 
		$targetfilePath_thumb = $targetPath_thumb.'/'.$filename;
		list($width,$height) = getimagesize($targetfilePath);
		$thumb_width = 200;
		$thumb_height = 160;
		
		if($height< $thumb_height){ $thumb_height=$height; }
		if($width< $thumb_width){ $thumb_width=$width; }
		
		$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);

		list($originalWidth, $originalHeight) = getimagesize($targetfilePath);
		$ratio = $originalWidth / $originalHeight;

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
		$Current_month = date('M');
		$Current_date = date('d-m-y');
		if (!is_dir(base_path('uploads'). '/'.$Current_month . '/' . $Current_date.'/thumbnail_medium')) 
		{
		 mkdir(base_path('uploads'). '/'. $Current_month . '/' . $Current_date.'/thumbnail_medium', 0777, true); 
		}

		$targetPath_thumb = base_path('uploads'). '/'.$Current_month . '/' . $Current_date.'/thumbnail_medium'; 
		$targetfilePath_thumb = $targetPath_thumb.'/'.$filename;
		list($width,$height) = getimagesize($targetfilePath);
		$thumb_width = 400;
		$thumb_height = 320;
		if($height< $thumb_height){ $thumb_height=$height; }
		if($width< $thumb_width){ $thumb_width=$width; }

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

public static function createThumbBanner($filename,$targetfilePath,$extensionofFile) 
 { 

 	if (!is_dir(base_path('uploads/homeBanner'). '/thumbnail')) {
        mkdir(base_path('uploads/homeBanner'). '/thumbnail', 0777, true); 
    }

    $targetPath_thumb = base_path('uploads/homeBanner'). '/thumbnail'; 
    //$targetfilePath_thumb = $targetPath_thumb.'/'.$filename_thumb;
    $targetfilePath_thumb = $targetPath_thumb.'/'.$filename; 

	list($width,$height) = getimagesize($targetfilePath);
	$thumb_width = 200;
	$thumb_height = 100;
	$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
	switch($extensionofFile){
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
	switch($extensionofFile){
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



 public static function createThumbMediumBanner($filename,$targetfilePath,$extensionofFile) 
 { 

 	if (!is_dir(base_path('uploads/homeBanner'). '/thumbnail_medium')) {
        mkdir(base_path('uploads/homeBanner'). '/thumbnail_medium', 0777, true); 
    }

    $targetPath_thumb = base_path('uploads/homeBanner'). '/thumbnail_medium'; 
    //$targetfilePath_thumb = $targetPath_thumb.'/'.$filename_thumb;
    $targetfilePath_thumb = $targetPath_thumb.'/'.$filename; 

	list($width,$height) = getimagesize($targetfilePath);
	$thumb_width = 400;
	$thumb_height = 320;
	$thumb_create = imagecreatetruecolor($thumb_width,$thumb_height);
	switch($extensionofFile){
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
	switch($extensionofFile){
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
