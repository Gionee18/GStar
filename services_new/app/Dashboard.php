<?php

namespace App;
use Validator;
use Intervention\Image\Facades\Image as Image;
use File;
use Session,Auth,DB,Hash;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Dashboard extends Authenticatable
{
	  

	  public static function getProducts(){
	  	$final_array=array();

	  	$total_product=DB::table('product')->count();
	  	$total_astiveproduct=DB::table('product')
	  						->where('product.status','=','1')
	  						->count();
	  	$total_inactiveproduct=DB::table('product')
	  							->where('product.status','!=','1')
	  							->count();
	  	$total_isnewproduct=DB::table('product')
	  							->where('product.new_product_flag','=','1')
	  							->count();


	  	$last_month=DB::table('product')
	  				->whereBetween('product.created_at',[date("Y-m-d",strtotime("-30 days"))." 00:00:00",date("Y-m-d")." 23:59:59"])
	  				->count();						


	  	$final_array['total_product']=$total_product;
	  	$final_array['total_activeproduct']=$total_astiveproduct;
	  	$final_array['total_inactiveproduct']=$total_inactiveproduct;
	  	$final_array['total_isnewproduct']=$total_isnewproduct;
	  	$final_array['last_month']=$last_month;
	  	return $final_array;

	  }

	  public static function getUpdates(){
	  	$final_array=array();

	  	$total_update=DB::table('newstopic')->count();
	  	$total_astiveupdate=DB::table('newstopic')
	  							->where('newstopic.status','=','1')
	  							->count();
	  	$total_inactiveupdate=DB::table('newstopic')
	  							->where('newstopic.status','!=','1')
	  							->count();

	  	$today=date("Y-m-d");
	  	$total_archiveupdate=DB::table('newstopic')
	  							->where('newstopic.expired_on','<',$today)
	  							->count();

		$last_month=DB::table('newstopic')
	  							->whereBetween('newstopic.created_at',[date("Y-m-d",strtotime("-30 days"))." 00:00:00",date("Y-m-d")." 23:59:59"])
	  							->count();	  	

	  	$final_array['total_update']=$total_update;
	  	$final_array['total_activeupdate']=$total_astiveupdate;
	  	$final_array['total_inactiveupdate']=$total_inactiveupdate;
	  	$final_array['total_archiveupdate']=$total_archiveupdate;
	  	$final_array['last_month']=$last_month;

	  	return $final_array;

	  }


	  public static function getVideos(){
	  	$final_array=array();

	  	$total_video=DB::table('video_tutorials')->count();
	  	$total_astivevideo=DB::table('video_tutorials')
	  							->where('video_tutorials.status','=','1')
	  							->count();
	  	$total_inactivevideo=DB::table('video_tutorials')
	  							->where('video_tutorials.status','!=','1')
	  							->count();

	  	$last_month=DB::table('video_tutorials')
	  				->whereBetween('video_tutorials.created_at',[date("Y-m-d",strtotime("-30 days"))." 00:00:00",date("Y-m-d")." 23:59:59"])
	  				->count();


	  	$final_array['total_update']=$total_video;
	  	$final_array['total_activeupdate']=$total_astivevideo;
	  	$final_array['total_inactiveupdate']=$total_inactivevideo;
	  	$final_array['last_month']=$last_month;
	  	return $final_array;

	  }


	   public static function getRecommender(){
	  	$final_array=array();

	  	$total_model=DB::table('model')->count();
	  	$total_astivemodel=DB::table('model')
	  						->where('model.status','=','1')
	  						->count();
	  	$total_inactivemodel=DB::table('model')
	  							->where('model.status','!=','1')
	  							->count();
	  	

	  	$last_month=DB::table('model')
	  				->whereBetween('model.created_at',[date("Y-m-d",strtotime("-30 days"))." 00:00:00",date("Y-m-d")." 23:59:59"])
	  				->count();						


	  	$final_array['total_product']=$total_model;
	  	$final_array['total_activeproduct']=$total_astivemodel;
	  	$final_array['total_inactiveproduct']=$total_inactivemodel;
	  	
	  	$final_array['last_month']=$last_month;
	  	return $final_array;

	  }


public static function getUsers()
{
	$final_array=array();
	$GetDetails=DB::table('users')
	  			->where('users.role','>=',GStarBaseController::G_ADMIN_ROLE_ID)
	  			->get();

	if(!empty($GetDetails))
	{
		$loginUserRole=Auth::user()->role;

		$final_array['total_user']=0;
		$final_array['active_user']=0;
		$final_array['inactive_user']=0;
		$final_array['today_login_user']=0;
		$final_array['week_login_user']=0;

		foreach ($GetDetails as $key)
		{
			if((int)$loginUserRole< (int)$key->role)
			{
				$loginUserId=Auth::user()->id;
				if($loginUserRole == GStarBaseController::G_SUPERADMIN_ROLE_ID)
				{
					$final_array['total_user']+=1;
					if($key->status ==1){
						$final_array['active_user']+=1;
					}else{
						$final_array['inactive_user']+=1;
					}
					if($key->last_login != "")
					{
						$date=strtotime(date("Y-m-d")." 00:00:00");
						$date2=strtotime(date("Y-m-d")." 23:59:59");
						$date3=strtotime(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
							$final_array['week_login_user']+=1;
						}
						if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
							$final_array['today_login_user']+=1;
						}
					}
				}
				if((int)$loginUserRole <= GStarBaseController::G_ADMIN_ROLE_ID)
				{
					$final_array['total_user']+=1;
					if($key->status ==1){
						$final_array['active_user']+=1;
					}else{
						$final_array['inactive_user']+=1;
					}
					if($key->last_login != "")
					{
						$date=strtotime(date("Y-m-d")." 00:00:00");
						$date2=strtotime(date("Y-m-d")." 23:59:59");
						$date3=strtotime(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
							$final_array['week_login_user']+=1;
						}
						if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
							$final_array['today_login_user']+=1;
						}
					}
				}

				if($loginUserRole == GStarBaseController::G_TRAINER_ROLE_ID)
				{
					$sp_id = DB::table('user_mapping')
									->select('parent_id')
									->where('user_id',$key->id)
									->first();
					if($sp_id)
					{
						if($sp_id->parent_id ==$loginUserId)
						{
							$final_array['total_user']+=1;
							if($key->status ==1){
								$final_array['active_user']+=1;
							}else{
								$final_array['inactive_user']+=1;
							}
							if($key->last_login)
							{
								$date=strtotime(date("Y-m-d")." 00:00:00");
								$date2=strtotime(date("Y-m-d")." 23:59:59");
								$date3=strtotime(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
								if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
									$final_array['week_login_user']+=1;
								}
								if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
									$final_array['today_login_user']+=1;
								}
							}	
										
						} 
						else 
						{
							$sp_id2 = DB::table('user_mapping')->select('parent_id')->where('user_id',$sp_id->parent_id)->first();
							if($sp_id2){
								if($sp_id2->parent_id ==$loginUserId){
										$final_array['total_user']+=1;
										if($key->status ==1){
											$final_array['active_user']+=1;
										}else{
											$final_array['inactive_user']+=1;
										}
										if($key->last_login)
										{
											$date=strtotime(date("Y-m-d")." 00:00:00");
											$date2=strtotime(date("Y-m-d")." 23:59:59");
											$date3=strtotime(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
											if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
												$final_array['week_login_user']+=1;
											}
											if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
												$final_array['today_login_user']+=1;
											}
										}
								}
							}
						} 
					}
							
				}

				if($loginUserRole == GStarBaseController::G_SUPERVISOR_ROLE_ID)
				{
					$sp_id2 = DB::table('user_mapping')->select('parent_id')->where('user_id',$key->id)->first();
					if($sp_id2){
						if($sp_id2->parent_id ==$loginUserId){
								$final_array['total_user']+=1;
								if($key->status ==1){
									$final_array['active_user']+=1;
								}else{
									$final_array['inactive_user']+=1;
								}
								if($key->last_login)
								{
									$date=strtotime(date("Y-m-d")." 00:00:00");
									$date2=strtotime(date("Y-m-d")." 23:59:59");
									$date3=strtotime(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
									if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
										$final_array['week_login_user']+=1;
									}
									if(($date3 <= $key->last_login) && ( $key->last_login >= $date2)){
										$final_array['today_login_user']+=1;
									}
								}
						}
					}
							
				}	
			}
		}
	}	  	
	return $final_array;

}

public static function getUsersRequest($userRole)
{
	$final_array=array();
	$final_array['total_request']=0;
	$final_array['pending_request']=0;
	$final_array['request_approve']=0;
	$final_array['request_reject']=0;
	$final_array['weekly_request']=0;
	$requestUserId=array();
	switch ($userRole)
	{
        case 05:
         
            $user_dummy=DB::table('users_dummy')->where('is_deleted','0')->get();
            if($user_dummy){
            	foreach ($user_dummy as $key) {
            		$final_array['total_request']+=1;
					if($key->approved_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->approved_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->approved_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            $product_dummy=DB::table('product_dummy')->where('is_deleted','0')->get();
            if($product_dummy){
            	foreach ($product_dummy as $key) {
            		$final_array['total_request']+=1;
					if($key->approve_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->approve_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->approve_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            $user_activation_request=DB::table('user_activation_request')->where('is_deleted','0')->get();
            if($user_activation_request){
            	foreach ($user_activation_request as $key) {
            		$final_array['total_request']+=1;
					if($key->activation_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->activation_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->activation_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            break;
        case 10:

            $user_dummy=DB::table('users_dummy')->where('is_deleted','0')->get();
            if($user_dummy){
            	foreach ($user_dummy as $key) {
            		$final_array['total_request']+=1;
					if($key->approved_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->approved_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->approved_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            $product_dummy=DB::table('product_dummy')->where('is_deleted','0')->get();
            if($product_dummy){
            	foreach ($product_dummy as $key) {
            		$final_array['total_request']+=1;
					if($key->approve_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->approve_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->approve_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            $user_activation_request=DB::table('user_activation_request')->where('is_deleted','0')->get();
            if($user_activation_request){
            	foreach ($user_activation_request as $key) {
            		$final_array['total_request']+=1;
					if($key->activation_status ==0){
						$final_array['pending_request']+=1;
					}
					if($key->activation_status ==1){
						$final_array['request_approve']+=1;
					}
					if($key->activation_status ==2){
						$final_array['request_reject']+=1;
					}
					if($key->created_at != "")
					{
						$date=(date("Y-m-d")." 00:00:00");
						$date2=(date("Y-m-d")." 23:59:59");
						$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
						if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
							$final_array['weekly_request']+=1;
						}
					}
            	}
            }

            break;
        case 20:
            
        	$loginUserId=Auth::user()->id;
        	$sp_id = DB::table('user_mapping')
						->select('user_id')
						->where('parent_id',$loginUserId)
						->get();
			if($sp_id){
				$j=0;
				foreach ($sp_id as  $value) {
					$requestUserId[$j]=$value->user_id;
					$j++;
				}
				foreach ($sp_id as  $value) {
					$sp_id2 = DB::table('user_mapping')->select('user_id')->where('parent_id',$value->user_id)->get();
					if($sp_id2){
						$j=count($requestUserId)-1;
						foreach ($sp_id2 as  $value) {
							$requestUserId[$j]=$value->user_id;
							$j++;
						}
					}	
				}
			}
			if($requestUserId){
				$user_dummy=DB::table('users_dummy')->whereIn('user_id', $requestUserId)->where('is_deleted','0')->get();
	            if($user_dummy){
	            	foreach ($user_dummy as $key) {
	            		$final_array['total_request']+=1;
						if($key->approved_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->approved_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->approved_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }

	            $product_dummy=DB::table('product_dummy')->whereIn('request_userid', $requestUserId)->where('is_deleted','0')->get();
	            if($product_dummy){
	            	foreach ($product_dummy as $key) {
	            		$final_array['total_request']+=1;
						if($key->approve_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->approve_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->approve_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }

	            $user_activation_request=DB::table('user_activation_request')->whereIn('user_id', $requestUserId)->where('is_deleted','0')->get();
	            if($user_activation_request){
	            	foreach ($user_activation_request as $key) {
	            		$final_array['total_request']+=1;
						if($key->activation_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->activation_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->activation_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }
			}			
			
            break;
        case 30:
            $loginUserId=Auth::user()->id;
        	$sp_id = DB::table('user_mapping')
						->select('user_id')
						->where('parent_id',$loginUserId)
						->get();
			if($sp_id){
				$j=0;
				foreach ($sp_id as  $value) {
					$requestUserId[$j]=$value->user_id;
					$j++;
				}
			}
			if($requestUserId){
				$user_dummy=DB::table('users_dummy')->whereIn('user_id', $requestUserId)->where('is_deleted','0')->get();
	            if($user_dummy){
	            	foreach ($user_dummy as $key) {
	            		$final_array['total_request']+=1;
						if($key->approved_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->approved_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->approved_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }

	            $product_dummy=DB::table('product_dummy')->whereIn('request_userid', $requestUserId)->where('is_deleted','0')->get();
	            if($product_dummy){
	            	foreach ($product_dummy as $key) {
	            		$final_array['total_request']+=1;
						if($key->approve_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->approve_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->approve_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }

	            $user_activation_request=DB::table('user_activation_request')->whereIn('user_id', $requestUserId)->where('is_deleted','0')->get();
	            if($user_activation_request){
	            	foreach ($user_activation_request as $key) {
	            		$final_array['total_request']+=1;
						if($key->activation_status ==0){
							$final_array['pending_request']+=1;
						}
						if($key->activation_status ==1){
							$final_array['request_approve']+=1;
						}
						if($key->activation_status ==2){
							$final_array['request_reject']+=1;
						}
						if($key->created_at != "")
						{
							$date=(date("Y-m-d")." 00:00:00");
							$date2=(date("Y-m-d")." 23:59:59");
							$date3=(date(("Y-m-d")." 00:00:00",strtotime("-1 week")));
							if(($date3 <= $key->created_at) && ( $key->created_at >= $date2)){
								$final_array['weekly_request']+=1;
							}
						}
	            	}
	            }
			}
            break;            
    }
	  	
	return $final_array;

}







} // end of model
