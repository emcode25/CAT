<?php

//api to take in data from sensor/pi and store in main database

require('includes/opendb.php');

if(isset($_POST['uid']) && isset($_POST['temp']) && isset($_POST['voc']) && isset($_POST['co2']) && isset($_POST['fall']) && isset($_POST['hr']) === true)
{
    $uid = $_POST['uid'];
    $temp = $_POST['temp'];
    $voc = $_POST['voc'];
    $co2 = $_POST['co2'];
    $fall = $_POST['fall'];
    $hr = $_POST['hr'];

    $time = time();
	
    //insert into database
    $send = $conn->prepare("INSERT INTO sensordata (uid, temp, voc, co2, fall, hr, time) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $send->bind_param("sdiiiii", $uid, $temp, $voc, $co2, $fall, $hr, $time);
    $send->execute();
    $send->close();
}

require('includes/closedb.php');
?>