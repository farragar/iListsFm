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
<form name="sending" onsubmit="getLovedTracks(this.usernameField.value); return false;" >
<h2>Last.fm Username:</h2>
<input type="text" id="usernameField" name="username"></input>
<input type="submit" value="Go"></input>
</form>
<form name ="submittion" action="sent.php" id="submitForm" method="post" enctype="multipart/form-data">
</form>
</div>
</div>
</script>

</body>
</html>
