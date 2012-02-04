<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>MyLovedPlaylists</title>
	<link href="default.css" rel="stylesheet" type="text/css" media ="screen" />
</head>
<body id="body">
<div id="content">
<h1>...And we're done!</h1><br /><p />

<?php 

$extArr=explode(".", $_FILES['library']['name']);
if(($extArr[1])!="xml"){
	echo "Invalid file uploaded!";
}

else getLovedTracks();

function getLovedTracks(){ 

$val=$_POST['tracksIn'];
$val2=$_POST['artistsIn'];

$isMac=$_POST['OS'];

$val=strtolower($val);
$val2=strtolower($val2);

$tracks=preg_split("/delimiter,/", $val);
$artists=preg_split("/delimiter,/", $val2);

$items=array();
for ($i=0; $i<count($tracks); $i++){
	$items[]= (object) array('trackName'=>$tracks[$i], 'artistName'=>$artists[$i]);
}

$loc=$_FILES['library']['tmp_name'];
$str="";
$input = simplexml_load_file($loc);
$processedXML=preprocessXML($input);

$uid=uniqid();
$fh=fopen("./playlists/".$uid.".m3u", 'w');

$notExist="";
//For each loved track
foreach ($items as $itemObj){
	$bestScore=0;
	$bestTrack="";
	$bestIndex=0;
	$lenBestTrack=0;
	$bestIndex=0;
	$arrIndex=0;

	$lastFmTrackNameArr=preg_split("/ /",$itemObj->trackName);
	$lastFmArtistNameArr=preg_split("/ /",$itemObj->artistName);


	foreach($processedXML as $obj){
		$thisScore=0;
		$trackNameArr=preg_split("/ /",$obj->trackName);
		$artistNameArr=preg_split("/ /",$obj->artistName);

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
			$bestScore=$thisScore;
			$bestTrack=$obj->trackName;
			$bestIndex=$arrIndex;
			$lenBestTrack=count($trackNameArr);
			$bestArtist=$obj->artistName;
			$lenBestArtist=count($artistNameArr);
		}
		$arrIndex++;
	}

	if($bestTrack==""&&$itemObj->trackName!=""){
		if (!($notExist)){
			$notExist="<br /> <p /><strong>We could not find any match for the following:</strong>";
		}
		$notExist.=("<br><p>".$itemObj->trackName."<strong> By</strong> $itemObj->artistName");
	}
	else if(!($bestScore==($lenBestTrack+2*($lenBestArtist))&&$bestScore==(count($lastFmTrackNameArr))+2*(count($lastFmArtistNameArr)))&&$itemObj->trackName!=""){
		if(!$partialMatches){
			$partialMatches="<br /> <p /><strong>The following tracks didn't match perfectly, but we've guessed what they were supposed to be. These are usually caused by misspellings, slightly different track names, or double spaces. You may wish to check if some of these are correct: </strong>";
		}
		$partialMatches.="<br><p><strong>Track</strong>: ".$itemObj->trackName." <strong> By: </strong>".$itemObj->artistName."<br><strong>Matched with:</strong>";
		$partialMatches.=$bestTrack;
		$partialMatches.=" <strong>By:</strong> ".$bestArtist;
	}

	$obj=$processedXML[$bestIndex];
	$str=$obj->location."\r\n";	
	fwrite($fh,$str);
}

fclose($fh);
echo('<a href="./playlists/'.$uid.'.m3u">Download Your file!</a><br /><p /> None of your library is stored. Playlists are deleted every 24 hours. Filenames correspond to iTunes playlist names, which can be changed. <a href="./details.php">Nerdy Details</a>');

echo $notExist;
echo $partialMatches;

}

function preprocessXML($file){

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
