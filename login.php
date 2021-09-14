<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "Curl.php";
include "Helper.php";

$publicKey = "86f1d5db-6fdf-41f8-8206-267d88f8b57c";
$privateKey = 'uH9AwqsWKKYPUalWuShcQA==';

$data = [];
$data["msg"] = "Error login";
$data["status"] = "KO";

if (!empty($_POST))
{
    if(isset($_POST["user_id"])){
        $userId = $_POST["user_id"];
        $api = Curl::GetPage([
		    "url" => "https://suscripciones.ecuavisa.com/api/v1/users/".$userId,
		    "requestHeaders" => [
		        "Authorization" => "Bearer wyleex+vistazo" 
		    ]
		]);
		$apiData = json_decode($api);
		$apiData = $apiData->data;
		$pos = strpos($apiData->email, "@facebook.com");
		if($pos === false){
		    $username = explode("@", $apiData->email);
		    $username = $username[0];

		    $userEncription = [
		        "usrname" => $username,
		        "first_name"  => $apiData->first_name,
		        "email" => $apiData->email,
		        "pwdmd5" => (String) md5($apiData->email),
		        'extid' => 'WYECU'.$apiData->id,
		        "timestamp" => (String) strtotime($apiData->created_at)
		    ];
		    
		    $text = json_encode($userEncription);
		    $encrypted = encrypt($text, $publicKey, $privateKey);
		    $url = urlencode("https:/www.ecuavisa.com");
		    $codificado = "1,". $publicKey. "," .$encrypted;
		    $urlProtect = "https://www.ecuavisa.com/user-portlet/refreshuserentitlements?redirect=".$url."&ssodata=".urlencode($codificado);
		    $apiProtec = Curl::GetPage([
		        "url" => $urlProtect,
		    ]);
		    $data["msg"] = "User: ".$username." has accessed.";
			$data["status"] = "OK";
		}
    }
}
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);