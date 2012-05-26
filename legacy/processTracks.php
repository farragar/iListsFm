<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>MyLovedPlaylists</title>
  <link href="default.css" rel="stylesheet" type="text/css" media ="screen" />
</head>
<body id="body">
<div id="content">
<h1>...And we're done!</h1><br /><p />

<?php 
$extArr=explode(".", $_FILES['library']['name']);
//Check file upload & type before running matching script

if($_FILES['library']['error']){
  echo "Invalid file upload!";
}
else if(($extArr[1])!="xml"){
  echo "Invalid file type uploaded!";
}
else getLovedTracks();

function getLovedTracks(){ 
  $tracksIn=$_POST['tracksIn'];
  $artistsIn=$_POST['artistsIn'];
  $isMac=$_POST['OS'];
  $playlistStr="";

  //Normalise lowercase and tokenise, TODO: in JS, make a decent delimiter (hash?)
  $tracksIn=strtolower($tracksIn);
  $artistsIn=strtolower($artistsIn);
  $tracks=preg_split("/delimiter,/", $tracksIn);
  $artists=preg_split("/delimiter,/", $artistsIn);

  //cast to array
  $lastFmTracks=array();
  for ($i=0; $i<count($tracks); $i++){
    $lastFmTracks[]= (object) array('trackName'=>$tracks[$i], 'artistName'=>$artists[$i]);
  }

  $loc=$_FILES['library']['tmp_name'];
  $input = simplexml_load_file($loc);

  //Reduce xml to just names, artists and locations.
  $processedXML=preprocessXML($input);

  $uid=uniqid();
  $fh=fopen("./playlists/".$uid.".m3u", 'w');

  $notExist="";

  //For each loved track
  foreach ($lastFmTracks as $lastFmObj){
    $bestScore=0;
    $bestTrack="";
    $bestIndex=0;
    $lenBestTrack=0;
    $bestIndex=0;
    $arrIndex=0;

    //tokenise tracks, artists to bag of words
    $lastFmTrackNameArr=preg_split("/ /",$lastFmObj->trackName);
    $lastFmArtistNameArr=preg_split("/ /",$lastFmObj->artistName);

    //For each itunes track
    foreach($processedXML as $obj){
      $thisScore=0;
      $trackNameArr=preg_split("/ /",$obj->trackName);
      $artistNameArr=preg_split("/ /",$obj->artistName);

      //Score 1 for matching name tokens, 2 for matching artist tokens
      foreach($lastFmTrackNameArr as $token){
        if(in_array($token, $trackNameArr)){
          $thisScore++;
        }
      } 
      
      foreach($lastFmArtistNameArr as $token){
        if(in_array($token, $artistNameArr)){
          $thisScore+=2;
        }
      }

      if($thisScore>$bestScore){
        $bestTrack=$obj->trackName;
        $bestArtist=$obj->artistName;
        $bestScore=$thisScore;
        $bestIndex=$arrIndex;
        $lenBestTrack=count($trackNameArr);
        $lenBestArtist=count($artistNameArr);
      }
      $arrIndex++;
    }

    //Variance between recorded and max scores == partial match
    if($bestTrack==""&&$lastFmObj->trackName!=""){
      if (!($notExist)){
        $notExist="<br /> <p /><strong>We could not find any match for the following:</strong>";
      }
      $notExist.=("<br /><p />".htmlspecialchars($lastFmObj->trackName)."<strong> By </strong>".htmlspecialchars($lastFmObj->artistName));
    }
    else if(!($bestScore==($lenBestTrack+2*($lenBestArtist))&&$bestScore==(count($lastFmTrackNameArr))+2*(count($lastFmArtistNameArr)))&&$lastFmObj->trackName!=""){
      if(!$partialMatches){
        $partialMatches="<br /> <p /><strong>The following tracks didn't match perfectly, but we've guessed what they were supposed to be. These are usually caused by misspellings, slightly different track names, or double spaces. You may wish to check if some of these are correct: </strong>";
      }
      $partialMatches.="<br /><p /><strong>Track</strong>: ".htmlspecialchars($lastFmObj->trackName)." <strong> By: </strong>".htmlspecialchars($lastFmObj->artistName)."<br /><strong>Matched with:</strong>";
      $partialMatches.=htmlspecialchars($bestTrack);
      $partialMatches.=" <strong>By:</strong> ".htmlspecialchars($bestArtist);
    }

    //Write location of this best track to playlist; continue
    $obj=$processedXML[$bestIndex];
    $playlistStr=$obj->location."\r\n";  
    fwrite($fh,$playlistStr);
  }

  fclose($fh);

  echo('<a href="./playlists/'.$uid.'.m3u">Download Your file!</a><br /><p /> None of your library is stored. Playlists are deleted every 24 hours. Filenames correspond to iTunes playlist names, which can be changed. <a href="./details.php">Nerdy Details</a>');
  echo $notExist;
  echo $partialMatches;
}

function preprocessXML($file){
//Strips all xml tags != name, artist or location from an itunes library to ease processing

  $trackArr=array();
  $trackLib=$file->dict[0]->dict[0];
  $numTracks=count($trackLib)/2;
  $trackName=$file->dict[0]->dict[0]->dict[2]->string;
  for ($i=0; $i<$numTracks; $i++){
    $thisTrack=$trackLib->dict[$i];
    $trackName=strtolower($thisTrack->string);
    $artistName=strtolower($thisTrack->string[1]);

    foreach($thisTrack as $item){
      if($gotLocation){
        $location=$item;  
        $gotLocation=false;
        if($isMac){
          $location=substr($location,16);
        }
        $trackArr[]= (object) array('trackName'=>$trackName, 'artistName'=>$artistName, 'location'=>$location);
      }
      if(strcasecmp($item,"Location")==0){
        $gotLocation=true;
      }
    }
  }
  return $trackArr;
}
?>
</div>
</body>
</html>
