<?php

namespace App;
use DB,Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\GStarBaseController;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    
	
	public static function UserAuditTrailList($request) 
    {
    	// ini_set('memory_limit', '512M');
    	// set_time_limit(0);
	        $loginUserRole=Auth::user()->role;

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
	   	$sql->where('users.role','>',"'".$loginUserRole."'");

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
	  //  	if(isset($inputs['getAll'])){
	  //  		$sql->skip(($getAllCount*1000));
			// $sql->take(1000);
	  //  	} 
		
	   	$usersDetail =$sql->get();
		$user=new User();
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
					$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);
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

					if(!isset($inputs['getAll'])){
						if(isset($inputs['start_date']) && isset($inputs['end_date']))
					{
						$start_date=trim($inputs['start_date']);
						$end_date=trim($inputs['end_date']);
						if($start_date !="" && $end_date !="")
						{
		   					$s_date=strtotime($start_date);
		   					$e_date=strtotime($end_date);
		   					$e_date=strtotime('+1 day',$e_date);
		   					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

		   					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

			   			}else{
			   				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

			   				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
			   			}
					}else{
						$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

						$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
					}
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
								$finalArr[$i]['supervisor_id'] =$key->parent_id;
								$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);

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
								if(!isset($inputs['getAll'])){
									if(isset($inputs['start_date']) && isset($inputs['end_date']))
								{
									$start_date=trim($inputs['start_date']);
									$end_date=trim($inputs['end_date']);
									if($start_date !="" && $end_date !="")
									{
					   					$s_date=strtotime($start_date);
					   					$e_date=strtotime($end_date);
					   					$e_date=strtotime('+1 day',$e_date);
					   					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

					   					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
										$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
										$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
										$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

										$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

						   			}else{
						   				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

						   				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
										$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
										$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
										$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

										$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
						   			}
								}else{
									$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

									$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
										$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
										$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
										$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

										$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
								}
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
					$finalArr[$i]['supervisor_id'] =$key->parent_id;
					$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);

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
					if (!isset($inputs['getAll'])) {
						if(isset($inputs['start_date']) && isset($inputs['end_date']))
					{
						$start_date=trim($inputs['start_date']);
						$end_date=trim($inputs['end_date']);
						if($start_date !="" && $end_date !="")
						{
		   					$s_date=strtotime($start_date);
		   					$e_date=strtotime($end_date);
		   					$e_date=strtotime('+1 day',$e_date);
		   					$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,$s_date,$e_date);

		   					$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',$s_date,$e_date);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',$s_date,$e_date);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',$s_date,$e_date);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',$s_date,$e_date);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,$s_date,$e_date);

			   			}else{
			   				$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

			   				$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
			   			}
					}else{
						$finalArr[$i]['module_access'] =self::GetUserModuleAccess($key->userId,null,null);

						$finalArr[$i]['Products_Last_used_date']=self::GetLastUsedDate($key->userId,'Product',null,null);
							$finalArr[$i]['Recommender_Last_used_date']=self::GetLastUsedDate($key->userId,'Recommender',null,null);
							$finalArr[$i]['Tutorials_Last_used_date']=self::GetLastUsedDate($key->userId,'Tutorials',null,null);
							$finalArr[$i]['Updates_Last_used_date']=self::GetLastUsedDate($key->userId,'Updates',null,null);

							$finalArr[$i]['Count_of_modules_visited']=self::GetModuleVisitedCount($key->userId,null,null);
					}
					}
					
					
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
	   	if(!isset($inputs['getAll'])){
	   		$sql->skip($offset);
			$sql->take(GStarBaseController::PAGINATION_LIMITS);
	   	} 
		// $sql->skip($offset);
		// $sql->take(GStarBaseController::PAGINATION_LIMITS);
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


									$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);
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
								
									$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);

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
							
							$finalArr[$i]['supervisor_name'] =self::getusername($key->parent_id);

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
			$sql="SELECT COUNT(*) as `module_count`,`login_time`,`module_access` FROM `saveuser_trail` WHERE user_id=".$userId." AND `login_time` BETWEEN '".$s_date."' AND '".$e_date."' GROUP BY `login_time`,`module_access`";
		}else{
			$sql="SELECT COUNT(*) as `module_count`,`login_time`,`module_access` FROM `saveuser_trail` WHERE user_id=".$userId." GROUP BY `login_time`,`module_access`";
		}
		

		$data=DB::select( DB::raw($sql));
		
		if($data){
			$i=0;
			$finalArr=array();
			//return $ModuleAccessDetail;
			foreach ($data as $key) {

				$finalArr[$key->login_time]['login_time']=date('d-m-Y h:i:s a',$key->login_time);
				$keyname=strtolower($key->module_access);
				$finalArr[$key->login_time]['module'][$keyname]=$key->module_access;
				$finalArr[$key->login_time]['module'][$keyname.'_count']=$key->module_count;
		
				
				//$i++;					
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

	public static function getusername($user_id) 
    {
    	$username = DB::table('users')->select('first_name','last_name')->where('id',$user_id)->first(); 
		if ($username) {
			return  $username->first_name.' '.$username->last_name;
		} else {
			return null;
		}
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
