<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Dashboard;

use Validator;
use Illuminate\Support\Facades\Input;
use Redirect, Session;
use Auth, DB, Hash, Mail;
use App\Http\Controllers\GStarBaseController;

class DashboardController extends GStarBaseController 
{
    
    
    public function index(Request $request)
    {
        
        $responseContent = $this->validateUser(DashboardController::G_LEARNER_ROLE_ID,false);
        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}

        $Detail['users']=array();
        $Detail['products']=array();
        $Detail['updates']=array();
        $Detail['videos']=array();
        $Detail['recommender']=array();
        $Detail['request']=array();

        $Detail['products']=Dashboard::getProducts();
        $Detail['updates']=Dashboard::getUpdates();
        $Detail['videos']=Dashboard::getVideos();
        $Detail['recommender']=Dashboard::getRecommender();
        $Detail['users']=Dashboard::getUsers();

        $userId=Auth::user()->id;
        $userRole=Auth::user()->role;
        switch ($userRole) {
            case 05:
                $Detail['request']=Dashboard::getUsersRequest($userRole);
                break;
            case 10:
                $Detail['request']=Dashboard::getUsersRequest($userRole);
                break;
            case 20:
                $Detail['request']=Dashboard::getUsersRequest($userRole);
                break;
            case 30:
                $Detail['request']=Dashboard::getUsersRequest($userRole);
                break;            
        }

        $result_array['count']  = count($Detail);
        $result_array['status'] = 'success';
        $result_array['data']   = $Detail;
        $responseContent        = $result_array;
        return $this->reponseBuilder($responseContent);
        
        
    }
    
    
    
}
