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

class NewsUpdate extends Authenticatable
{
	 

	public static function AppNewsUpdateList($search_keyword,$user_id,$pageNo)
	{	
		 if($pageNo!= null) {
            $offset=($pageNo-1)*GStarBaseController::TOPICPAGINATION_LIMITS;
		 } else {
			 $offset= 0;
		 }
		 
		$finalArr = array();
		
		$i = 0;

		$getList = DB::table('newscategory')
					->orderBy('position', 'asc')
					->where('status', '1')
					->get();	

		foreach($getList as $eachrow)
		{
			$category_id = $eachrow->id;
			
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['category_name'] = $eachrow->category_name;
								
			$getSubcategory = DB::table('newssubcategory')
								->where('category_id',$category_id)
								->orderBy('position', 'asc')
								->where('status', '1')
								->get();	
				
			if(!empty($getSubcategory)) {
				$t = 0;
				
				$finalArr[$i]['subcategory'] = [];
				
				foreach($getSubcategory as $row) {
					$subcategory_id = $row->id;
					$today = date('Y-m-d');				
						
					$finalArr[$i]['subcategory'][$t]['id'] = $row->id;
					$finalArr[$i]['subcategory'][$t]['subcategory_name'] = $row->subcategory_name;
					
					if($search_keyword != "")
					{
						$getTopic = DB::table('newstopic')
									->where('topic_name','like','%'.$search_keyword.'%')
									->orWhere('topic_desc','like','%'.$search_keyword.'%')
									->where('category_id',$category_id)
									->where('subcategory_id',$subcategory_id)
									->orderBy('position', 'asc')
									->where('status', '1')									
									->get();	 
					 } else {
						 $getTopic = DB::table('newstopic')
										->where('category_id',$category_id)
										->where('subcategory_id',$subcategory_id)
										->orderBy('position', 'asc')
										->where('status', '1')
										->where('expired_on', '>=', $today)
										->skip($offset)->take(GStarBaseController::TOPICPAGINATION_LIMITS)
										->get();	
					}
										
					$finalArr[$i]['subcategory'][$t]['topic']=[];
					
					if(!empty($getTopic)){		
						$j=0;
						foreach ($getTopic as $keyTopic) {
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['id'] = $keyTopic->id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['category_id'] = $keyTopic->category_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['subcategory_id'] = $keyTopic->subcategory_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_name'] = $keyTopic->topic_name;
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_desc'] = $keyTopic->topic_desc;
							
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['is_read'] = 0;
							
							$req_array = array();
							$req_array['category_id'] = $keyTopic->category_id;
							$req_array['subcategory_id'] = $keyTopic->subcategory_id;
							$req_array['topic_id'] = $keyTopic->id;
							
							 
							$readstatus=self::TopicReadStatus($user_id, $req_array);
							
							if($readstatus){
								$finalArr[$i]['subcategory'][$t]['topic'][$j]['is_read'] = 1;
							}
								
							$j++;
						}
					}
					
					/*$getExpiredTopics = DB::table('newstopic')
										->where('category_id',$category_id)
										//->where('subcategory_id',$subcategory_id)
										->orderBy('id', 'desc')
										->where('status', '1')
										->where('expired_on', '<', $today)
										->skip($offset)->take(GStarBaseController::TOPICPAGINATION_LIMITS)
										->get();	
					
					if(!empty($getExpiredTopics)){	
						
						$t++;
						
						$finalArr[$i]['subcategory'][$t]['id'] = 0;
						$finalArr[$i]['subcategory'][$t]['subcategory_name'] = 'Archived';
						
						$j=0;
						foreach ($getExpiredTopics as $keyTopic) {
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['id'] = $keyTopic->id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['category_id'] = $keyTopic->category_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['subcategory_id'] = $keyTopic->subcategory_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_name'] = $keyTopic->topic_name;
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_desc'] = $keyTopic->topic_desc;
							
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['is_read'] = 1;
															
							$j++;
						}
					}*/
					
					$t++;
				}		
				
				$getExpiredTopics = DB::table('newstopic')
										->where('category_id',$category_id)
										//->where('subcategory_id',$subcategory_id)
										->orderBy('position', 'asc')
										->where('status', '1')
										->where('expired_on', '<', $today)
										->skip($offset)->take(GStarBaseController::TOPICPAGINATION_LIMITS)
										->get();	
					
					if(!empty($getExpiredTopics)){	
						
						//$t++;
						
						$finalArr[$i]['subcategory'][$t]['id'] = 0;
						$finalArr[$i]['subcategory'][$t]['subcategory_name'] = 'Archived';
						
						$j=0;
						foreach ($getExpiredTopics as $keyTopic) {
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['id'] = $keyTopic->id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['category_id'] = $keyTopic->category_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['subcategory_id'] = $keyTopic->subcategory_id;
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_name'] = $keyTopic->topic_name;
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['topic_desc'] = $keyTopic->topic_desc;
							
							
							$finalArr[$i]['subcategory'][$t]['topic'][$j]['is_read'] = 1;
															
							$j++;
						}
					}			
			}
						
			$i++;			
		}		
				
		return $finalArr;					
	}
	
	public static function AppNewsUpdateTopicsList($search_keyword,$user_id,$category_id,$subcategory_id,$pageNo)
	{	
		$offset = 0;
		
		if($pageNo!= null) {
			$limit = $pageNo*GStarBaseController::TOPICPAGINATION_LIMITS;
		} else {
			$limit = 0;
		}
		
		$today = date('Y-m-d');
		$finalArr = array();
		
		$i = 0;

		if($search_keyword != "")
		{
			if($subcategory_id > 0) {
				$getTopic = DB::table('newstopic')
					->where('topic_name','like','%'.$search_keyword.'%')
					->orWhere('topic_desc','like','%'.$search_keyword.'%')
					->where('category_id',$category_id)
					->where('subcategory_id',$subcategory_id)
					->orderBy('position', 'asc')
					->where('status', '1')									
					->get();	 
			} else {
				$getTopic = DB::table('newstopic')
					->where('topic_name','like','%'.$search_keyword.'%')
					->orWhere('topic_desc','like','%'.$search_keyword.'%')
					->where('category_id',$category_id)
					->orderBy('position', 'asc')
					->where('status', '1')	
					->where('expired_on', '<=', $today)								
					->get();
			}
		} else {
			
			if($subcategory_id > 0) {
				$getTopic = DB::table('newstopic')
					->where('category_id',$category_id)
					->where('subcategory_id',$subcategory_id)
					->orderBy('position', 'asc')
					->where('status', '1')
					->where('expired_on', '>=', $today)
					->skip($offset)->take($limit)
					->get();
			} else {
				$getTopic = DB::table('newstopic')
					->where('category_id',$category_id)
					->orderBy('position', 'asc')
					->where('status', '1')
					->where('expired_on', '<=', $today)
					->skip($offset)->take($limit)
					->get();
					
			}	
		}
		
		if(!empty($getTopic)){		
			$j=0;
			foreach ($getTopic as $keyTopic) {
				$finalArr['topics'][$j]['id'] = $keyTopic->id;
				$finalArr['topics'][$j]['category_id'] = $keyTopic->category_id;
				$finalArr['topics'][$j]['subcategory_id'] = $keyTopic->subcategory_id;
				$finalArr['topics'][$j]['topic_name'] = $keyTopic->topic_name;
							
				$finalArr['topics'][$j]['topic_desc'] = $keyTopic->topic_desc;
				$finalArr['topics'][$j]['expired_on'] = $keyTopic->expired_on;
							
							
				$finalArr['topics'][$j]['is_read'] = 0;
				
				$req_array = array();
				$req_array['category_id'] = $keyTopic->category_id;
				$req_array['subcategory_id'] = $keyTopic->subcategory_id;
				$req_array['topic_id'] = $keyTopic->id;
				
				$readstatus=self::TopicReadStatus($user_id, $req_array);
				
				if($readstatus){
					$finalArr['topics'][$j]['is_read'] = 1;
				}
				
				if( $subcategory_id == 0 || $keyTopic->expired_on < date('Y-m-d') ) {
					$finalArr['topics'][$j]['is_read'] = 1;
				}
												
				$j++;
			}
		}
					
		return $finalArr;					
	}
	
	public static function TopicReadStatus($user_id,$data){
		$status=0;
		$read=DB::table('read_newstopic')
				->where('user_id',$user_id)
				->where('newscategory_id',$data['category_id'])
				->where('newssubcategory_id',$data['subcategory_id'])
				->where('newstopic_id',$data['topic_id'])
				->first();
		if($read){
			$status=1;
		}		
		return $status;
	}

	public static function SendPushAndroid($topic){
		
		$DeviceDetail=array();

		$notification_admin=0;
		$notification_trainer=0;
		$notification_supervisor=0;
		$notification_learner=0;

		if(array_key_exists('notification_admin', $topic)){
			$notification_admin= $topic['notification_admin'];	
		}

		if(array_key_exists('notification_trainer', $topic)){
			$notification_trainer= $topic['notification_trainer'];	
		}

		if(array_key_exists('notification_learner', $topic)){
			$notification_learner= $topic['notification_learner'];	
		}

		if(array_key_exists('notification_supervisor', $topic)){
			$notification_supervisor= $topic['notification_supervisor'];	
		}
		
		
    	if($notification_admin == 1){
    		$role=GStarBaseController::G_ADMIN_ROLE_ID;
    		$DeviceDetail=self::GetDeviceTokenBYRole($DeviceDetail,$role);
    	}
    	if($notification_trainer == 1){
    		$role=GStarBaseController::G_TRAINER_ROLE_ID;
    		$DeviceDetail=self::GetDeviceTokenBYRole($DeviceDetail,$role);
    	}
    	if($notification_supervisor == 1){
    		$role=GStarBaseController::G_SUPERVISOR_ROLE_ID;
    		$DeviceDetail=self::GetDeviceTokenBYRole($DeviceDetail,$role);
    	}
    	if($notification_learner == 1){
    		$role=GStarBaseController::G_LEARNER_ROLE_ID;
    		$DeviceDetail=self::GetDeviceTokenBYRole($DeviceDetail,$role);
    	}

    	if(!empty($DeviceDetail)){
    		$send=self::PUSH_nofity_Android($DeviceDetail,$topic,0);
    	}

    	
    	

    	return ;
	}

	public static function GetDeviceTokenBYRole($DeviceDetail,$role){

		$count=0;
		if(count($DeviceDetail) != 0){
			$count=count($DeviceDetail['device_token']);
		}
		
		$Detail=DB::table('device')->where('user_role',$role)->where('login_status','1')->get();
		
		if(!empty($Detail)){
			
			foreach ($Detail as $key ) {
			$DeviceDetail['device_token'][$count]=$key->device_token;
			$DeviceDetail['user'][$count]['user_id']=$key->user_id;
			$DeviceDetail['user'][$count]['user_role']=$key->user_role;
			$count++;
			}
			
		}
	return	$DeviceDetail;

	}

	public static function PUSH_nofity_Android($DeviceDetail,$topic,$user){

		
		$headers = array
		(
			'Authorization: key=' . GStarBaseController::FCM_API_ACCESS_KEY,
			'Content-Type: application/json'
		);
		
		
		
		if($user ==1){
			$msg = array
			(
				'title'		=> GStarBaseController::EDIT_USER_PUSH,
				'body'	=> $topic,
				'click_action'	=> 'UserActivity',
				'category'=>'',
				'subcategory'=>''
			);
		} else {

			$category=DB::table('newscategory')->where('id',$topic['category_id'])->first();
			$subcategory=DB::table('newssubcategory')->where('id',$topic['subcategory_id'])->first();

			$msg = array
			(
				'title'		=> GStarBaseController::NEW_TOPIC_CREATE,
				'body'	=> $topic['topic_name'],
				'click_action'		=> 'UpdateActivity',
				'category'=>$category->category_name,
				'subcategory'=>$subcategory->subcategory_name
			);
		}
		

		
		
		$fields = array
		(
			'registration_ids' 	=> $DeviceDetail['device_token'],
			'data'=> ($msg),
		);

		

		$url="https://fcm.googleapis.com/fcm/send";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,0); 
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode( $fields));
		$result = curl_exec($ch);
		curl_close($ch);

		$result=json_decode($result, true);

		if(!empty($result)){
			$notifyMsgId=self::InsertNotificationMessage($msg);
			//$notifyMsgId=1;
			if($notifyMsgId !=0)
				$SaveDetail=self::SaveNotificationDetail($notifyMsgId,$DeviceDetail,$result);
		}
		
		return;
				
	}


	public static function InsertNotificationMessage($msg){

		$id = DB::table('notification')->insertGetId(
    				['notify_msg' => $msg['body'], 'notify_title' => $msg['title']]
		);
		return $id;
	}


	public static function SaveNotificationDetail($notifyMsgId,$DeviceDetail,$result){
		
		 
		$insertarr=array();
		
		for ($i=0; $i <count($result['results']) ; $i++) { 
			$insertarr[$i]['user_id']=$DeviceDetail['user'][$i]['user_id'];
			$insertarr[$i]['user_role']=$DeviceDetail['user'][$i]['user_role'];
			$insertarr[$i]['notifymsg_id']=$notifyMsgId;
			if(array_key_exists('message_id', $result['results'][$i])){
				$insertarr[$i]['message_id']=$result['results'][$i]['message_id'];
			}else{
				$insertarr[$i]['error']=$result['results'][$i]['error'];
			}
			$id = DB::table('notify_user')->insert($insertarr[$i]);
		}
		//print_r($insertarr);
		//die;

		
		return; //$id;
	}


	public static function UpdateReadStatus($user_id,$data)
	{
		$insert=null;
		for ($i=0; $i <count($data) ; $i++)
		{ 
			$userexist=DB::table('users')->where('id',$user_id)->first();
			$newstopicexist=DB::table('newstopic')
							->where('id',$data[$i]['topic_id'])
							->where('category_id',$data[$i]['category_id'])
							->where('subcategory_id',$data[$i]['subcategory_id'])
							->where('status','1')
							->first();
			if($userexist && $newstopicexist)
			{
				$isexist=self::TopicReadStatus($user_id,$data[$i]);
				if(!$isexist){
					$insertarr=array('user_id'=>$user_id,'newscategory_id'=>$data[$i]['category_id'],'newssubcategory_id'=>$data[$i]['subcategory_id'],'newstopic_id'=>$data[$i]['topic_id']);
							$insert=DB::table('read_newstopic')->insert($insertarr);
				}
			}	
		}
		return $insert;
	}



	// public static function UpdateReadStatus($user_id,$category_id,$subcategory_id,$topic_id){

	// 	$result_array = array();
	// 	if($user_id && $category_id && $subcategory_id && $topic_id){
	// 			$userexist=DB::table('users')->where('id',$user_id)->first();
	// 			$newscategoryexist=DB::table('newscategory')->where('id',$category_id)->first();
	// 			$newssubcategoryexist=DB::table('newssubcategory')->where('id',$subcategory_id)->first();
	// 			$newstopicexist=DB::table('newstopic')->where('id',$topic_id)->first();
	// 			if($userexist && $newscategoryexist && $newstopicexist && $newssubcategoryexist){
	// 				$isexist=self::TopicReadStatus($user_id,$category_id,$subcategory_id,$topic_id);
	// 			if($isexist){
	// 				$result_array['count'] = 0;
	// 				$result_array['status'] = 'error';
	// 				$result_array['data'] = null;
	// 				$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_USER;
	// 			}else{
	// 				$insertarr=array('user_id'=>$user_id,'newscategory_id'=>$category_id,'newssubcategory_id'=>$subcategory_id,'newstopic_id'=>$topic_id);
	// 				$insert=DB::table('read_newstopic')->insert($insertarr);
	// 				if($insert){
	// 					$result_array['count'] = count($insert);
	// 					$result_array['status'] = 'success';
	// 					$result_array['data'] = null;
	// 					$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC;
	// 				}else{
	// 					$result_array['count'] = 0;
	// 					$result_array['status'] = 'error';
	// 					$result_array['data'] = null;
	// 					$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_FAIL;
	// 				}
	// 			}
	// 		}else{
	// 			$result_array['count'] = 0;
	// 			$result_array['status'] = 'error';
	// 			$result_array['data'] = null;
	// 			$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_PARAMETRE.' or user, category, topic not exist.';
	// 		}
			
	// 	}else{
	// 		$result_array['count'] = 0;
	// 		$result_array['status'] = 'error';
	// 		$result_array['data'] = null;
	// 		$result_array['msg'] = GStarBaseController::MSG_READ_TOPIC_PARAMETRE;
	// 	}
		
	// 	return $result_array;
	// }


	// public static function AppNewsUpdateList($search_keyword){
		
	// 	$finalArr = array();
	// 	$i = 0;

	// 	$getList = DB::table('newscategory')
	// 				->orderBy('id', 'desc')
	// 				->where('status', '1')
	// 				->get();	

	// 		foreach($getList as $eachrow)
	// 		{
	// 			$category_id=$eachrow->id;
	// 			 if($search_keyword!="")
	// 			 {
	// 				$getTopic = DB::table('newstopic')
	// 						    ->where('topic_name','like',$search_keyword.'%')
	// 						    ->where('category_id',$category_id)
	// 						    ->orderBy('id', 'desc')
	// 							->where('status', '1')
	// 							->get();	 
	// 	         }
	// 	        else
	// 			{
	// 			     $getTopic = DB::table('newstopic')
	// 								->where('category_id',$category_id)
	// 								->orderBy('id', 'desc')
	// 								->where('status', '1')
	// 								->get();	
	// 	        }
				
	// 				if(!empty($getTopic)){
	// 					$finalArr[$i]['category']['id'] = $eachrow->id;
	// 					$finalArr[$i]['category']['category_name'] = $eachrow->category_name;
	// 					$finalArr[$i]['category']['status'] = $eachrow->status;
	// 					$finalArr[$i]['category']['description'] = $eachrow->description;
	// 					$finalArr[$i]['category']['created_at'] = date('d-m-Y',strtotime($eachrow->created_at));
	// 					if($eachrow->updated_at){
	// 						$finalArr[$i]['category']['updated_at'] = date('d-m-Y',strtotime($eachrow->updated_at));
	// 					}else{
	// 						$finalArr[$i]['category']['updated_at'] = '';
	// 					}
	// 					$j=0;
	// 					foreach ($getTopic as $keyTopic) {
	// 						$finalArr[$i]['category']['topic'][$j]['id'] = $keyTopic->id;
	// 						$finalArr[$i]['category']['topic'][$j]['category_id'] = $keyTopic->category_id;
	// 						$finalArr[$i]['category']['topic'][$j]['topic_name'] = $keyTopic->topic_name;
	// 						$finalArr[$i]['category']['topic'][$j]['status'] = $keyTopic->status;
	// 						$finalArr[$i]['category']['topic'][$j]['topic_desc'] = $keyTopic->topic_desc;
	// 						$finalArr[$i]['category']['topic'][$j]['notification_admin'] = $keyTopic->notification_admin;
	// 						$finalArr[$i]['category']['topic'][$j]['notification_trainer'] = $keyTopic->notification_trainer;
	// 						$finalArr[$i]['category']['topic'][$j]['notification_supervisor'] = $keyTopic->notification_supervisor;
	// 						$finalArr[$i]['category']['topic'][$j]['notification_learner'] = $keyTopic->notification_learner;
	// 						$finalArr[$i]['category']['topic'][$j]['created_at'] = date('d-m-Y',strtotime($keyTopic->created_at));
	// 						if($eachrow->updated_at){
	// 						$finalArr[$i]['category']['topic'][$j]['updated_at'] = date('d-m-Y',strtotime($keyTopic->updated_at));
	// 					}else{
	// 						$finalArr[$i]['category']['topic'][$j]['id'] = '';
	// 					}

	// 					}
	// 					$j++;
	// 					$i++;
	// 				}
				
						
								
	// 		}			

 //  	return $finalArr;					
	// }


	public static function getListOfNewsCategory($search_keyword,$pageNo){
		
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
			 $getList = DB::table('newscategory')
							->where('category_name','like','%'.$search_keyword.'%')
							->orderBy('position', 'asc')
							//->orderBy('status', 'asc')
							->skip($offset)
							->take(GStarBaseController::PAGINATION_LIMITS)
							->get();
         }
        else
		{
		     $getList = DB::table('newscategory')
							->orderBy('position', 'asc')
							//->orderBy('status', 'asc')
							->skip($offset)
							->take(GStarBaseController::PAGINATION_LIMITS)
							->get();
         }
		 
			foreach($getList as $eachrow)
			{
				$finalArr[$i]['id'] = $eachrow->id;
				$finalArr[$i]['category_name'] = $eachrow->category_name;
				$finalArr[$i]['status'] = $eachrow->status;
				$finalArr[$i]['description'] = $eachrow->description;
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


	public static function getListOfNewsSubCategory($search_keyword,$pageNo)
	{	
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
			$getList = DB::table('newssubcategory')
							->where('subcategory_name','like','%'.$search_keyword.'%')
							->orderBy('position', 'asc')
							//->orderBy('status', 'asc')
							->skip($offset)
							->take(GStarBaseController::PAGINATION_LIMITS)
							->get();
         }else{
		     $getList = DB::table('newssubcategory')
							->orderBy('position', 'asc')
							//->orderBy('status', 'asc')
							->skip($offset)
							->take(GStarBaseController::PAGINATION_LIMITS)
							->get();
         }
		 foreach($getList as $eachrow)
		{
			$finalArr[$i]['id'] = $eachrow->id;
			$finalArr[$i]['category_id'] = $eachrow->category_id;
			$finalArr[$i]['category_name'] = null;
			$category=DB::table('newscategory')->select('category_name')->where('id',$eachrow->category_id)->first();
			if($category){
			$finalArr[$i]['category_name'] = $category->category_name;	
			}
			$finalArr[$i]['subcategory_name'] = $eachrow->subcategory_name;
			$finalArr[$i]['status'] = $eachrow->status;
			$finalArr[$i]['description'] = $eachrow->description;
			$finalArr[$i]['created_at'] = strtotime($eachrow->created_at)."000";
			//date('d-m-Y',strtotime($eachrow->created_at));
			$finalArr[$i]['updated_at'] = '';
			if($eachrow->updated_at){
				$finalArr[$i]['updated_at'] = strtotime($eachrow->updated_at)."000";
				//date('d-m-Y',strtotime($eachrow->updated_at));
			}			
			$i++;				
		}					
  	return $finalArr;					
	}

	public static function AddNewsCategory($request) { 
		

		$rules = array(
			'category_name'    => 'required', 
			'status'    => 'required', 
		    'description' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = array();
		if(!$messages->first('category_name')) 
		{
			$category_name = trim($request->input('category_name'));
			$alreadyExist = GStarBaseController::validateForExist('newscategory',$category_name,'category_name'); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$category['category_name'] = $category_name;		   
		}else {
			Session::flash('msg', $messages->first('category_name'));
		} 

		if(!$messages->first('status')) {
			$category['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
		
		if(!$messages->first('description')) {
			$category['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		return $category;
	}


	public static function AddNewsSubCategory($request) { 
		

		$rules = array(
			'category_id' =>'required',
			'subcategory_name'    => 'required', 
			'status'    => 'required', 
		    'description' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$subcategory = array();

		if(!$messages->first('category_id')) {
			$subcategory['category_id'] = trim($request->input('category_id'));
	    }else{
			Session::flash('msg', $messages->first('category_id'));
		}

		if(!$messages->first('subcategory_name')) 
		{
			$subcategory_name = trim($request->input('subcategory_name'));
			$alreadyExist = GStarBaseController::validateForExist('newssubcategory',$subcategory_name,'subcategory_name'); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$subcategory['subcategory_name'] = $subcategory_name;		   
		}else {
			Session::flash('msg', $messages->first('subcategory_name'));
		} 

		if(!$messages->first('status')) {
			$subcategory['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
		
		if(!$messages->first('description')) {
			$subcategory['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		return $subcategory;
	}

	
	public static function editCategoryNews($request) { 
		

		$rules = array(
			'category_name'    => 'required', 
			'status'    => 'required', 
		    'description' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$category = array();
		if(!$messages->first('category_name')) 
		{
			$category_name = trim($request->input('category_name'));
			$id = trim($request->input('id'));
			$alreadyExist = DB::table('newscategory')->select('category_name')->where('category_name','=',$category_name)->where('id','!=',$id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$category['category_name'] = $category_name;		   
		}else {
			Session::flash('msg', $messages->first('category_name'));
		} 

		if(!$messages->first('status')) {
			$category['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
		
		if(!$messages->first('description')) {
			$category['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		return $category;
	}


	public static function editSubCategoryNews($request) { 
		

		$rules = array(
			'category_id' =>'required',
			'subcategory_name'    => 'required', 
			'status'    => 'required', 
		    'description' => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		
		Session::forget('msg');
		$subcategory = array();

		if(!$messages->first('category_id')) {
			$subcategory['category_id'] = trim($request->input('category_id'));
	    }else{
			Session::flash('msg', $messages->first('category_id'));
		}

		if(!$messages->first('subcategory_name')) 
		{
			$subcategory_name = trim($request->input('subcategory_name'));
			$id = trim($request->input('id'));
			$alreadyExist = DB::table('newssubcategory')->select('subcategory_name')->where('subcategory_name','=',$subcategory_name)->where('id','!=',$id)->count(); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$subcategory['subcategory_name'] = $subcategory_name;		   
		}else {
			Session::flash('msg', $messages->first('subcategory_name'));
		} 

		if(!$messages->first('status')) {
			$subcategory['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }
		
		if(!$messages->first('description')) {
			$subcategory['description'] = trim($request->input('description'));
	     }
		else {
			Session::flash('msg', $messages->first('description'));
		  }
		
		return $subcategory;
	}

	public static function InsertNewsCategory($category)
	{
		
		$save=DB::table('newscategory')->insert($category);
		return $save;
	}


	public static function InsertNewsSubCategory($subcategory)
	{
		
		$save=DB::table('newssubcategory')->insert($subcategory);
		return $save;
	}


	public static function updateCategoryNews($category,$id)
	{
		
		$save=DB::table('newscategory')->where('id',$id)->update($category);
		return $save;
	}


	public static function updateSubCategoryNews($subcategory,$id)
	{
		
		$save=DB::table('newssubcategory')->where('id',$id)->update($subcategory);
		return $save;
	}


	public static function deleteCategoryNews($id)
	{
		$save=DB::table('newscategory')->where('id','=',$id)->delete();
		return $save;
		
	}


	public static function deleteSubcategoryNews($id)
	{
		$save=DB::table('newssubcategory')->where('id','=',$id)->delete();
		return $save;
		
	}

	public static function CategoryNewsById($id)
	{
		$save=DB::table('newscategory')->where('id','=',$id)->first();
		return $save;
	}


	public static function SubCategoryNewsById($id)
	{
		$finalArr=array();
		$i=0;
		$data=DB::table('newssubcategory')->where('id','=',$id)->first();
		if($data){
			$finalArr[$i]['id'] = $data->id;
			$finalArr[$i]['category_id'] = $data->category_id;
			$finalArr[$i]['category_name'] = null;
			$category=DB::table('newscategory')->select('category_name')->where('id',$data->category_id)->first();
			if($category){
			$finalArr[$i]['category_name'] = $category->category_name;	
			}
			$finalArr[$i]['subcategory_name'] = $data->subcategory_name;
			$finalArr[$i]['status'] = $data->status;
			$finalArr[$i]['description'] = $data->description;
			$finalArr[$i]['created_at'] = date('d-m-Y',strtotime($data->created_at));
			$finalArr[$i]['updated_at'] = '';
			if($data->updated_at){
				$finalArr[$i]['updated_at'] = date('d-m-Y',strtotime($data->updated_at));
			}		
		}
		return $finalArr;
	}

	public static function getnewstopicBycategoryId($category_id)
	{
		$save=DB::table('newstopic')->where('category_id','=',$category_id)->count();
		return $save;
	}

	public static function getnewstopicBysubcategoryId($subcategory_id)
	{
		$save=DB::table('newstopic')->where('subcategory_id','=',$subcategory_id)->count();
		return $save;
	}


	public static function getsubcategoryBycategoryId($id)
	{
		$save=DB::table('newssubcategory')->where('category_id','=',$id)->count();
		return $save;
	}



	public static function getListOfNewsTopic($search_keyword,$pageNo){
		
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
			 $getList = DB::table('newstopic')
									  ->where('topic_name','like','%'.$search_keyword.'%')
									  ->orderBy('position', 'asc')
									  //->orderBy('status', 'asc')
									  ->skip($offset)
									  ->take(GStarBaseController::PAGINATION_LIMITS)
									  ->get();
         }
        else
		{
		     $getList = DB::table('newstopic')
								     ->orderBy('position', 'asc')
								     //->orderBy('status', 'asc')
								     ->skip($offset)
								     ->take(GStarBaseController::PAGINATION_LIMITS)
								     ->get();
         }
		 
			foreach($getList as $eachrow)
			{
				$finalArr[$i]['id'] = $eachrow->id;
				$category_id=$eachrow->category_id;
				$subcategory_id=$eachrow->subcategory_id;
				$finalArr[$i]['category_id'] = $category_id;
				$finalArr[$i]['subcategory_id'] = $subcategory_id;
				$finalArr[$i]['category_name'] = NewsUpdate::topicCategory($category_id);
				$finalArr[$i]['subcategory_name'] = NewsUpdate::topicSubcategory($subcategory_id);
				$finalArr[$i]['topic_name'] = $eachrow->topic_name;
				$finalArr[$i]['topic_desc'] = $eachrow->topic_desc;
				$finalArr[$i]['status'] = $eachrow->status;
				$finalArr[$i]['notification_admin'] = $eachrow->notification_admin;
				$finalArr[$i]['notification_trainer'] = $eachrow->notification_trainer;
				$finalArr[$i]['notification_supervisor'] = $eachrow->notification_supervisor;
				$finalArr[$i]['notification_learner'] = $eachrow->notification_learner;
				$finalArr[$i]['expired_on'] =NULL;//$eachrow->expired_on;
				if($eachrow->expired_on>0){
					$finalArr[$i]['expired_on'] = strtotime($eachrow->expired_on)."000";
					//date('d-m-Y',strtotime($eachrow->expired_on));
				}
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

	public static function AddNewsTopic($request) { 
		

		$rules = array(
			'category_id'    => 'required', 
			'subcategory_id'=>'required',
			'topic_name'    => 'required', 
		    'status' => 'required',
			'topic_desc'    => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$topic = array();
		if(!$messages->first('category_id')) {
			$topic['category_id'] = trim($request->input('category_id'));
	     }
		else {
			Session::flash('msg', $messages->first('category_id'));
		  }

		  if(!$messages->first('subcategory_id')) {
			$topic['subcategory_id'] = trim($request->input('subcategory_id'));
	     }
		else {
			Session::flash('msg', $messages->first('subcategory_id'));
		  }

		if(!$messages->first('topic_name')) 
		{
			$topic_name = trim($request->input('topic_name'));
			$alreadyExist = GStarBaseController::validateForExist('newstopic',$topic_name,'topic_name'); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$topic['topic_name'] = $topic_name;		   
		}else {
			Session::flash('msg', $messages->first('topic_name'));
		} 

		if(!$messages->first('status')) {
			$topic['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		if(!$messages->first('topic_desc')) {
			$topic['topic_desc'] = trim($request->input('topic_desc'));
	     }
		else {
			Session::flash('msg', $messages->first('topic_desc'));
		  }

		  $notification_admin=trim($request->input('notification_admin')); 
		  $notification_trainer=trim($request->input('notification_trainer')); 
		  $notification_supervisor=trim($request->input('notification_supervisor')); 
		  $notification_learner=trim($request->input('notification_learner'));

		  if($notification_admin){
			 $topic['notification_admin'] = 1;//trim($request->input('notification_admin'));
		 }
		 if($notification_trainer){
			 $topic['notification_trainer'] = 1;//trim($request->input('notification_admin'));
		 }
		 if($notification_supervisor){
			 $topic['notification_supervisor'] = 1;//trim($request->input('notification_admin'));
		 }
		 if($notification_learner){
			 $topic['notification_learner'] = 1;//trim($request->input('notification_admin'));
		 }

		$expired_on=trim($request->input('expired_on')); 
		 if($expired_on){
			$topic['expired_on'] = date('Y-m-d',strtotime($expired_on));
		 }else{
		 	$topic['expired_on'] = date('Y-m-d',strtotime("180 days"));
		 }

		
		return $topic;
	}


	public static function editNewsTopic($request) { 
		

		$rules = array(
			'category_id'    => 'required',
			'subcategory_id'=>'required', 
			'topic_name'    => 'required', 
		    'status' => 'required',
			'topic_desc'    => 'required'
		);
		$validator = Validator::make($request->input(), $rules);
		$messages = $validator->errors();
		Session::forget('msg');
		$topic = array();
		if(!$messages->first('category_id')) {
			$topic['category_id'] = trim($request->input('category_id'));
	     }
		else {
			Session::flash('msg', $messages->first('category_id'));
		  }

		 if(!$messages->first('subcategory_id')) {
			$topic['subcategory_id'] = trim($request->input('subcategory_id'));
	     }
		else {
			Session::flash('msg', $messages->first('subcategory_id'));
		  } 

		if(!$messages->first('topic_name')) 
		{
			$topic_name = trim($request->input('topic_name'));
			$id = trim($request->input('id'));
			$alreadyExist = DB::table('newstopic')->select('topic_name')->where('topic_name','=',$topic_name)->where('id','!=',$id)->count(); 
			//$alreadyExist = GStarBaseController::validateForExist('newstopic',$topic_name,'topic_name'); 
		    if($alreadyExist){
					Session::flash('msg', GStarBaseController::MSG_ALREADY_EXIST);
					return ;
		     }$topic['topic_name'] = $topic_name;		   
		}else {
			Session::flash('msg', $messages->first('topic_name'));
		} 

		if(!$messages->first('status')) {
			$topic['status'] = trim($request->input('status'));
	     }
		else {
			Session::flash('msg', $messages->first('status'));
		  }

		if(!$messages->first('topic_desc')) {
			$topic['topic_desc'] = trim($request->input('topic_desc'));
	     }
		else {
			Session::flash('msg', $messages->first('topic_desc'));
		  }


		  $notification_admin=trim($request->input('notification_admin')); 
		  $notification_trainer=trim($request->input('notification_trainer')); 
		  $notification_supervisor=trim($request->input('notification_supervisor')); 
		  $notification_learner=trim($request->input('notification_learner'));

		  if($notification_admin){
			 $topic['notification_admin'] = 1;//trim($request->input('notification_admin'));
		 }else{
		 	$topic['notification_admin'] = 0;
		 }

		 if($notification_trainer){
			 $topic['notification_trainer'] = 1;//trim($request->input('notification_admin'));
		 }else{
		 	$topic['notification_trainer'] = 0;
		 }

		 if($notification_supervisor){
			 $topic['notification_supervisor'] = 1;//trim($request->input('notification_admin'));
		 }else{
		 	$topic['notification_supervisor'] = 0;
		 }

		 if($notification_learner){
			 $topic['notification_learner'] = 1;//trim($request->input('notification_admin'));
		 }else{
		 	$topic['notification_learner'] = 0;
		 }

		$expired_on=trim($request->input('expired_on')); 
		 if($expired_on){
			$topic['expired_on'] = date('Y-m-d',strtotime($expired_on));
		 }else{
		 	$topic['expired_on'] = date('Y-m-d',strtotime("180 days"));
		 }

		return $topic;
	}
	

	public static function InsertNewsTopic($topic)
	{
		
		$save=DB::table('newstopic')->insert($topic);
		return $save;
	}

	public static function topicCategory($id)
	{
		
		$save=DB::table('newscategory')->where('id','=',$id)->first();
		return $save->category_name;
	}

	public static function topicSubcategory($id)
	{
		
		$save=DB::table('newssubcategory')->where('id','=',$id)->first();
		return $save->subcategory_name;
	}

	public static function updateNewsTopic($topic,$id)
	{
		$updatereadstatus=self::updateTopicReadStatus($id);
		$save=DB::table('newstopic')->where('id',$id)->update($topic);
		return $save;
	}

	public static function deleteNewsTopic($id)
	{
		// $arr=array('status'=>'0');
		// $save=DB::table('newscategory')->where('id','=',$id)->update($arr);
		$updatereadstatus=self::updateTopicReadStatus($id);
		$save=DB::table('newstopic')->where('id','=',$id)->delete();
		return $save;

	}

	public static function updateTopicReadStatus($id)
	{
		$save=DB::table('read_newstopic')->where('newstopic_id','=',$id)->delete();
		return $save;

	}

	public static function newsTopicById($id)
	{
		$finalArr=array();
		$save=DB::table('newstopic')->where('id','=',$id)->first();
		if($save){
			$finalArr['id'] = $save->id;
			$category_id=$save->category_id;
			$subcategory_id=$save->subcategory_id;
			$finalArr['category_id'] = $category_id;
			$finalArr['subcategory_id'] = $subcategory_id;
			$finalArr['category_name'] = NewsUpdate::topicCategory($category_id);
			$finalArr['subcategory_name'] = NewsUpdate::topicSubcategory($subcategory_id);
			$finalArr['topic_name'] = $save->topic_name;
			$finalArr['topic_desc'] = $save->topic_desc;
			$finalArr['status'] = $save->status;
			$finalArr['notification_admin'] = $save->notification_admin;
			$finalArr['notification_trainer'] = $save->notification_trainer;
			$finalArr['notification_supervisor'] = $save->notification_supervisor;
			$finalArr['notification_learner'] = $save->notification_learner;
			$finalArr['expired_on'] =NULL;//$save->expired_on;
			if($save->expired_on>0){
				$finalArr['expired_on'] = date('d-m-Y',strtotime($save->expired_on));
			}
			$finalArr['created_at'] = date('d-m-Y',strtotime($save->created_at));
			if($save->updated_at){
				$finalArr['updated_at'] = date('d-m-Y',strtotime($save->updated_at));
			}else{
				$finalArr['updated_at'] = '';
			}
		}
		return $finalArr;
	}




	public static function CategorySubcategoryList()
	{
		$finalArr=array();
		$i=0;
		$category=DB::table('newscategory')->get();

		if($category){
			foreach ($category as $key ) {
				$finalArr[$i]['id']=$key->id;
				$finalArr[$i]['category_name']=$key->category_name;
				$finalArr[$i]['status']=$key->status;
				$finalArr[$i]['description']=$key->description;
				$finalArr[$i]['subcat']=[];
				$subcategory=DB::table('newssubcategory')->where('category_id','=',$key->id)->get();
				if($subcategory){
					$j=0;
					foreach ($subcategory as  $value) {
						$finalArr[$i]['subcat'][$j]['id']=$value->id;
						$finalArr[$i]['subcat'][$j]['category_id']=$value->category_id;
						$finalArr[$i]['subcat'][$j]['subcategory_name']=$value->subcategory_name;
						$finalArr[$i]['subcat'][$j]['status']=$value->status;
						$finalArr[$i]['subcat'][$j]['description']=$value->description;
						$j++;
					}
				}
				$i++;
			}

		}

		return $finalArr;
	}

	public static function changeOrderofTopic($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('newstopic')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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


	public static function changeOrderofTopicCategory($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('newscategory')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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


	public static function changeOrderofTopicSubcategory($request) 
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
			
			$updateProductOrder = $responseContent = DB::table('newssubcategory')->where('id', $each['id'])->update(array('position'=>$each['pos']));
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
