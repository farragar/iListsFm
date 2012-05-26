<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>MyLovedPlaylists</title>
	<script src="./scripts.js"></script>
	<link href="./default.css" rel="stylesheet" type="text/css" media="screen"/>
</head>
<body id="body">
<div id="wrapper">
<div id="content">

This tool was made by <a href="http://www.farragar.com">Farragar</a>. It's provided <a href="https://github.com/farragar/MyLovedPlaylists">open-source</a>.<br /><p /> Currently written mostly in PHP, but I'm working on a full HTML5/javascript version.
<br /><p />

Library XML is preprocessed to leave just track names, artist names and local machine locations, then subject to the track matching algorithm:
<br /><p />
For each last.fm loved track, traverse the entire itunes library. 
<br />&nbsp;&nbsp;&nbsp; For each track in the itunes library, score how well it matches with the last.fm trackname: <br />
&nbsp;&nbsp;&nbsp; score++ for each matching word (bag of words) in the track name<br /> 
&nbsp;&nbsp;&nbsp; score+=2 for each match in the artist name (these are generally more unique).<br />
&nbsp;&nbsp;&nbsp;Record the index of the highest scoring track for this loved track.<br />
&nbsp;&nbsp;&nbsp;Write the highest scoring track location to the .m3u file <br />
When completed, save the playlist with a unique ID on the server and provide a link.
</div>
</div>
</body>
</html>
