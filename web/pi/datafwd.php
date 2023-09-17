<?php

require('includes/opendb.php');

//url target
$url = "http://192.168.137.1/cat/datasink.php";

if(isset($_GET['uid']) && isset($_GET['temp']) && isset($_GET['voc']) && isset($_GET['co2']) && isset($_GET['fall']) && isset($_GET['hr']) && isset($_GET['talk']) === true)
{
    $uid = $_GET['uid'];
    $temp = $_GET['temp'];
    $voc = $_GET['voc'];
    $co2 = $_GET['co2'];
    $fall = $_GET['fall'];
    $hr = $_GET['hr'];
    $talk = $_GET['talk'];

    $time = time();
    
    //forward data to main server
    $payload = [
        'uid' => $uid,
        'temp' => $temp,
        'voc' => $voc,
        'co2' => $co2,
        'fall' => $fall,
        'hr' => $hr
    ];

    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($payload));
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
    $result = curl_exec($ch);

    //insert talk status into database
    $send = $conn->prepare("UPDATE talkstatus SET talk = ? WHERE id = 0");
    $send->bind_param("i", $talk);
    $send->execute();
    $send->close();

}

require('includes/closedb.php');

?>
