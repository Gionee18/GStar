<?php

namespace App\Http\Controllers;

use Redirect,Mail;

class sendmail extends Controller
{
    public function __construct()
    {
        
    }

    

    public function mail() 
    {
        //ssl://
        //echo "string";
        $user=array('email'=>'kapilkumar.v@vvdntech.in');
        Mail::send('email.emailtest', ['user' => $user], function ($m) use ($user) {
                //$m->from('gstar@gionee.co.in', 'Gstar Admin');
                $m->to('kapilkumar.v@vvdntech.in', 'Kapil Kumar')->subject('Welcome!');
        });
        //sleep(50);
        // try {


        //     Mail::send('email.emailtest', ['user' => $user], function ($m) use ($user) {
        //         $m->from('support@gionee.co.in', 'GioneeMail');
        //         $m->to('kapilkumar.v@vvdntech.in', 'Kapil Kumar')->subject('Welcome!');
        // });

        //     // Mail::send('email.emailtest', $data, function ($m) use ($data) {
        //     //     $m->from('support@gionee.co.in', $name = 'Gionee Gstar Admin');
        //     //     $m->to('GioneeMail', 'GioneeMail')->subject('Welcome!');
        //     // });

            
        //     print_r('success');
            
        // } catch (Exception $e) {
        //    print_r($e); 
        // }
        die();
         
    }

    public function checkport() 
     { 
        $host="eapi.mgage.solutions";
        $port=25;
        $hostip = @gethostbyname($host); 
        if ($hostip == $host) 
        { 
            echo "Server is down or does not exist"; 
        } else { 
            if (!$x = @fsockopen($hostip, $port, $errno, $errstr, 5))
            { 
                echo "Port $port is closed."; 
            } else { 
                echo "Port $port is open."; 
                if ($x) { 
                    @fclose($x); 
                } 
            } 
        } 
        die();
     }
}