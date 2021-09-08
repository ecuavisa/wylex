<?php
error_reporting(E_ERROR | E_PARSE);

include "Curl.php";
include "Helper.php";

$fechaInicio = "2021-08-01";
$fechaFin = date("Y-m-t");

$publicKey = "86f1d5db-6fdf-41f8-8206-267d88f8b57c";
$privateKey = 'uH9AwqsWKKYPUalWuShcQA==';

$api = Curl::GetPage([
    "url" => "https://suscripciones.ecuavisa.com/api/v1/users",
    "requestHeaders" => [
        "Authorization" => "Bearer wyleex+vistazo" 
    ]
]);
$apiData = json_decode($api);
$apiData = $apiData->data;

$emails = arrayData($fechaInicio, $fechaFin);

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
    $url = urlencode("https:/www.ecuavisa.com");
    $codificado = "1,". $publicKey. "," .$encrypted;
    $urlProtect = "https://www.ecuavisa.com/user-portlet/refreshuserentitlements?redirect=".$url."&ssodata=".urlencode($codificado);
    
    try {
      $apiProtec = Curl::GetPage([
        "url" => $urlProtect,
      ]);
      fileData($data->email, $fechaInicio, $fechaFin);
    } catch (\Throwable $th) {}
  }
}