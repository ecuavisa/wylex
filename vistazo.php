<?php
//error_reporting(E_ERROR | E_PARSE);

include "Curl.php";
include "Helper.php";

$fechaInicio = "2021-08-01";
$fechaFin = date("Y-m-t");

$publicKey = "5ca314c6-fa18-496b-981e-4f631d544ee3";
$privateKey = '9sCD1E+Zi3Y35vKn8iHKpw==';

$api = Curl::GetPage([
    "url" => "https://suscripciones.vistazo.com/api/v1/users?status=active",
    "requestHeaders" => [
        "Authorization" => "Bearer wyleex+vistazo" 
    ]
]);
$apiData = json_decode($api);
$apiData = $apiData->data;

$emails = arrayData($fechaInicio, $fechaFin, "vistazo");

foreach ($apiData as $key => $data) {
  if(!in_array($data->email, $emails)){
    $username = explode("@", $data->email);
    $username = $username[0];

    $userEncription = [
        "usrname" => $username,
        "first_name"  => $data->first_name,
        "email" => $data->email,
        "pwdmd5" => (String) md5($data->email),
        'extid' => 'WYECU'.$data->id,
        "timestamp" => (String) strtotime($data->created_at)
    ];
    
    $text = json_encode($userEncription);
    $encrypted = encrypt($text, $publicKey, $privateKey);
    $url = urlencode("https:/www.vistazo.com");
    $codificado = "1,". $publicKey. "," .$encrypted;
    $urlProtect = "https://www.vistazo.com/user-portlet/refreshuserentitlements?redirect=".$url."&ssodata=".urlencode($codificado);
    
    try {
      $apiProtec = Curl::GetPage([
        "url" => $urlProtect,
      ]);
      fileData($data->email, $fechaInicio, $fechaFin, "vistazo");
    } catch (\Throwable $th) {}
  }
}