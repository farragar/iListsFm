<!DOCTYPE html>
<html lang="en">
  <head>
  <script src="./scripts.js"></script>
  <script src="http://code.jquery.com/jquery-latest.js" ></script>
    <meta charset="utf-8">
    <title>MyLovedPlaylists</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="./assets/css/bootstrap-responsive.css" rel="stylesheet">

    <link rel="shortcut icon" href="./favicon.ico">
  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="#">Project name</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li><a href="#contact">Source</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <!-- Main hero unit for a primary marketing message or call to action -->
      <div class="hero-unit">
        <h2>Perfectly Matched:</h2><p />
        <table class="table-striped">
          <thead>
            <th>Last.FM Track</th>
            <th>Last.FM Artist</th>
          </thead>
          <?php 
            $playlist="";
            $perfectMatches=json_decode($_POST['perfectMatches']);
            $semiMatches=json_decode($_POST['semiMatches']);
            $failedTracks=json_decode($_POST['failedMatches']);

            foreach($perfectMatches as $loveTrackPair){
              echo '<tr><td>'.$loveTrackPair[0]->trackName.'</td>'.
                '<td>'.$loveTrackPair[0]->artistName.'</td></tr>';
              $playlist=$playlist.$loveTrackPair[1]->fileLocation;
            }
          ?>

      </table>
      <h2>Partially Matched:</h2><p />
        <table class="table-striped">
          <thead>
            <th>Last.FM Track</th>
            <th>Last.FM Artist</th>
            <th>Match Track</th>
            <th>Match Artist</th>
          </thead>
          <?php 
            foreach($semiMatches as $loveTrackPair){
              echo '<tr><td>'.$loveTrackPair[0]->trackName.'</td>'.
                '<td>'.$loveTrackPair[0]->artistName.'</td>'.
                '<td>'.$loveTrackPair[1]->trackName.'</td>'.
                '<td>'.$loveTrackPair[1]->artistName.'</td></tr>';
            }
          ?>
       </table>
<h2> Failed tracks:</h2><p />
        <table class="table-striped">
          <thead>
            <th>Last.FM Track</th>
            <th>Last.FM Artist</th>
</thead>
          <?php 
            foreach($failedTracks as $track){
              echo '<tr><td>'.$track->trackName.'</td>'.
              '<td>'.$track->artistName.'</td></tr>';
            }
          ?>
</table>
      <footer>
        <p>&copy; farragar.com 2012</p>
      </footer>

    </div> <!-- /container -->

  </body>
</html>


