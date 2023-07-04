<?php
include($_SERVER['DOCUMENT_ROOT'].'/php/settings/websettings.php');
class ALPHP
{
    //checks if the url requesting page is the from current website
    public function CHECK_REQUEST_URL()
    {
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        if(isset($actual_link) && parse_url($actual_link, PHP_URL_HOST) == $webname)
        {
          return true;
        }
        else
        {
          return false;
        }
    }
    
    //check if there is an existing token
    public function CHECK_TOKEN($token)
    {
        if($_SESSION['A31axwebtokenLo0192s'] == $token) 
        {
          return true;
        }
        else 
        {
          return false;
        }
    }
    
    //creates a token
    public function CREATE_TOKEN()
    {
        session_start();
        $generatetoken = hash('sha256', rand(). $salt); //you can use any encryption 
        $_SESSION['A31axwebtokenLo0192s'] = $generatetoken; //store it as session variable
    }
    
    //checks if the file exists in the hosting
    public function CHECK_FILE_EXSITS($filepath)
    {
        if (file_exists($filepath)) 
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }
    
    //generate random string
    public function GENERATE_RANDOM_STRING($length = 10) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) 
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    //check request type
    public function CHECK_REQUEST_POST()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') 
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //upload file into server file from paramter is from input file
    public function UPLOAD_FILE($filefrom,$fileto)
    {
        if (!empty($fileto))
        {
            move_uploaded_file($filefrom, $fileto);
        }
    }
    
}

?>