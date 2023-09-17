<?php 
require('includes/opendb.php');

$uid = "kyle";

//verify if requested uid exists
if(isset($_GET['uid']))
{

  $uidCheck = $conn->prepare("SELECT * FROM sensordata WHERE uid = ?");
  $uidCheck->bind_param("s", $uid);
  $uidCheck->execute();
  $uidCheck->store_result();
  $uidCount = $uidCheck->num_rows;
  $uidCheck->close();

  if($uidCount != 1)
  {
    $uid = "User not found!";
  }

  $uid = $_GET['uid'];
}

require('includes/closedb.php');

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="favicon.jpg">
    <link rel="canonical" href="https://getbootstrap.com/docs/3.4/examples/dashboard/">

    <title>CAT Dash</title>

    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/3.4/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="https://getbootstrap.com/docs/3.4/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/3.4/examples/dashboard/dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->x

  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="http://127.0.0.1/cat/index.php"> <img style="height: 30px;" src="catword.png"/> </a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a class="navbar-light-text" href="#">Dashboard</a></li>
          </ul>
          <form class="navbar-form navbar-right">
          </form>
        </div>
      </div>
    </nav>
  
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <img class="img-responsive img-fluid" src="happycatcentered.png"/>
          <ul class="nav nav-sidebar">
            <li><a href="http://127.0.0.1/cat/index.php?uid=kyle"><b>Kyle</b></a></li>
            <li><a href="http://127.0.0.1/cat/index.php?uid=emily"><b>Emily</b></a></li>
            <li><a href="http://127.0.0.1/cat/index.php?uid=luke"><b>Luke</b></a></li>
            <li><a href="http://127.0.0.1/cat/index.php?uid=lucus"><b>Lucus</b></a></li>
          </ul>

        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Your Litter</h1>

          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="kyle.png" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Kyle</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="emily.png" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Emily</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="luke.png" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Luke</h4>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
              <img src="lucus.png" width="200" height="200" class="img-responsive" alt="Generic placeholder thumbnail">
              <h4>Lucus</h4>
            </div>
          </div>

          <h2 class="sub-header"><?php echo($uid); ?></h2>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Heartrate</th>
                  <th>Fall</th>
                  <th>Temperature</th>
                  <th>CO2</th>
                  <th>VOC</th>
                </tr>
              </thead>
              <tbody>
              <?php
                  require('includes/opendb.php');

                  //display last 30 events per uid
                  $dataArr = $conn->prepare("SELECT time, temp, voc, co2, fall, hr FROM sensordata WHERE uid = ? ORDER BY eventid DESC LIMIT 30");
                  $dataArr->bind_param("s", $uid);
                  $dataArr->execute();
                  $result = $dataArr->get_result();
                  $dataArr->close(); 
                      
                  //loop through events and print. Highlight data reaching thresholds
                  foreach($result->fetch_all() as $dbReq)
                  {
                    $time = $dbReq[0];
                    $temp = $dbReq[1];
                    $voc = $dbReq[2];
                    $co2 = $dbReq[3];
                    $fall = $dbReq[4];
                    $hr = $dbReq[5];
                  ?>
                    <tr> 
                        <td><?php echo(date("Y-m-d h:i:sa",$time)); ?></td>
                        <td <?php if($hr > 120) {echo("class=\"bg-danger\"");} ?>><?php echo($hr); ?></td>
                        <td <?php if($fall == 1) {echo("class=\"bg-danger\"");} ?>><?php echo($fall); ?></td>
                        <td <?php if($temp > 83) {echo("class=\"bg-danger\"");} ?>><?php echo($temp); ?></td>
                        <td <?php if($co2 > 5000) {echo("class=\"bg-danger\"");} ?>><?php echo($co2); ?></td>
                        <td <?php if($voc > 600) {echo("class=\"bg-danger\"");} ?>><?php echo($voc); ?></td>
                      </tr>
                <?php
                  } 
                  require('includes/closedb.php');
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha384-nvAa0+6Qg9clwYCGGPpDQLVpLNn0fRaROjHqs13t4Ggj3Ez50XnGQqc/r8MhnRDZ" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/3.4/assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="https://getbootstrap.com/docs/3.4/dist/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="https://getbootstrap.com/docs/3.4/assets/js/vendor/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="https://getbootstrap.com/docs/3.4/assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
