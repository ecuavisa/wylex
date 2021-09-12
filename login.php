<?php

$code = "KO";

if (!empty($_POST))
{
    if(isset($_POST["accesstoken"])){
        $code = "OK";
        $msg = '<field name="output">
        <string>'.$_POST["accesstoken"].'</string>
        </field>';
    }
}else{
    $msg =  '<field name="msg">
    <string>User identity could not be verified. Please, try it again</string>
    </field>';
}
header ("Content-Type:text/xml");
echo '<?xml version="1.0"?>
<itwresponse version="1.0">
<field name="code">
<string>'.$code.'</string>
</field>
'.$msg.'
</itwresponse>';