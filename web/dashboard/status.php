<?php

//api to provide status report summary to clients

require('includes/opendb.php');

if(isset($_GET['uid']) === true)
{
    $uid = $_GET['uid'];

    //get recent event data
    $dataArr = $conn->prepare("SELECT temp, voc, co2, fall, hr FROM sensordata WHERE uid = ? ORDER BY eventid DESC LIMIT 10");
    $dataArr->bind_param("s", $uid);
    $dataArr->execute();
    $result = $dataArr->get_result();
    $dataArr->close(); 

    $temp = 0.0;
    $voc = 0;
    $co2 = 0;
    $fall = 0;
    $hr = 0;

    //add event data and take average
    foreach($result->fetch_all() as $dbReq)
    {
        $temp += $dbReq[0];
        $voc += $dbReq[1];
        $co2 += $dbReq[2];
        $fall += $dbReq[3];
        $hr += $dbReq[4];
    }

    $avgTemp = $temp / 10.0;
    $avgVoc = $voc / 10;
    $avgCo2 = $co2 / 10;
    $avgHr = $hr / 10; 

    //determine if danger thresholds reached
    $tempStatus = ($avgTemp > 82) ? 1 : 0;
    $fallStatus = ($fall > 0) ? 1 : 0;
    $vocStatus = ($avgVoc > 600) ? 1 : 0;
    $co2Status = ($avgCo2 > 5000) ? 1 : 0;
    $hrStatus = ($avgHr > 120) ? 1 : 0;

    //create json response for client
    $response = array(
       'temp' => $tempStatus,
       'voc' => $vocStatus,
       'co2' => $co2Status,
       'fall' => $fallStatus,
       'hrStatus' => $hrStatus
    );

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response);
}

require('includes/closedb.php');
?>