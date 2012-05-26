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
        <h1>MyLovedPlaylists</h1>
        <p>The following tool will generate an m3u playlist (compatible with all main media players) based on Last.FM data and iTunes library information. Your iTunes library file can be found in 'C:\Users\username\Music\iTunes\iTunes Music Library.xml' on Windows, and '/Users/username/Music/iTunes/iTunes Library.xml' on Mac.</p>

          <output id="list"></output>
          <div id="dropZone"> Drop library file here </div>
          <script>
          
          // Setup the dnd listeners.
          var dropZone = document.getElementById('dropZone');
          dropZone.addEventListener('dragover', handleDragOver, false);
          dropZone.addEventListener('drop', handleFileSelect, false);
  
          </script>            


      <form class="well form-inline" id="inputForm" name="inputForm" onsubmit="runForm(getJSLovedTracks); return false;" action="returnFile.php" method="post">
        <input type="text" id="username" name="username" placeholder="Last.FM Username"/>
        <select id="selectType" onChange="handleSelectChange();">
          <option value="lovedAndTop">Loved and top tracks</option>
          <option value="loved">Loved tracks only</option>
          <option value="top">Top tracks only</option>
        </select>
      <input id="numOfTop" type="number" placeholder="Use top ... tracks" />
      <select id="matchThreshold" onchange="handleThreshChange();" class="nullSelect">
        <option value="default" >Matching Threshold</option>
        <option value="perfect">Perfect</option>
        <option value="strong">Strong</option>
        <option value="intermediate">Intermediate (recommended)</option>
        <option value="weak">Weak</option>
        <option value="anything">Anything goes!</option>
      </select>
</select>
      <button id="submitBtn" class="btn btn-primary" type="submit" disabled>Submit</button>
<input type="hidden" id="trackObjStr" name="trackObjstr" />
<input type="hidden" id="perfectMatches" name="perfectMatches" />
<input type="hidden" id="semiMatches" name="semiMatches" />
<input type="hidden" id="failedMatches" name="failedMatches" /></li>
</form>
      </div>

      <footer>
        <p>&copy; farragar.com 2012</p>
      </footer>

    </div> <!-- /container -->
  
    
  </body>
</html>

