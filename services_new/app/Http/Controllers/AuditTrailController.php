<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\GStarBaseController;
use Validator;
use App\Audit;
use Maatwebsite\Excel\Facades\Excel as Excel;
use Illuminate\Support\Facades\Input;
use Auth,DB,Hash,Response;

class AuditTrailController extends GStarBaseController
{
     
     public function UserAuditTrail(Request $request) 
     {
     	ini_set('memory_limit', -1);
    	set_time_limit(0);
    	$inputs = Input::get();
	 	$result_array = array();
		$responseContent = $this->validateUser(UserController::G_SUPERVISOR_ROLE_ID,false);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		if (isset($inputs['download'])) {
			if ($inputs['download'] == 1) {
				$activationList = Audit::UserAuditTrailList($request);
				$e_download= Excel::create('audit report');
				$e_download->sheet('sheet', function($sheet) use (&$activationList) {
			        $sheet->fromArray($activationList);
				});
				$e_download->export('csv');
			}
		}else{
			$activationList = Audit::UserAuditTrailList($request);
			$result_array['count'] = count($activationList);
			$result_array['status'] = 'success';
			$result_array['data'] = $activationList;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
		}
		die();
	}

	public function ActiveInactiveUserList(Request $request) 
    {
     	ini_set('memory_limit', -1);
    	set_time_limit(0);
    	$inputs = Input::get();
	 	$result_array = array();
		$responseContent = $this->validateUser(UserController::G_SUPERVISOR_ROLE_ID,false);
	    if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$activationData = Audit::ActiveInactiveUserList($request);
		if (isset($inputs['download'])) {
			if ($inputs['download'] == 1) {
				$data = $activationData;
				$e_download= Excel::create('activeinactivereport');
				$e_download->sheet('sheet 1', function($sheet) use (&$data) {
			        $sheet->fromArray($data);
				});
				$e_download->export('csv');
			}
		}else{
			$result_array['count'] = count($activationData);
			$result_array['status'] = 'success';
			$result_array['data'] = $activationData;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
		}
		die();
	 	
	}


	
}
