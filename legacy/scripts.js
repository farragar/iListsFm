function getLovedTracks(username) {
  var user=username;
  var text="";
  var out="";
  var req = new XMLHttpRequest();

  /*limit to 400 tracks to ease computation*/
  req.open("GET", "http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&user="+user+"&api_key=d7113c54fb740ab1e97398776926fdc4&limit=300");

  req.onreadystatechange = function() {

       if (req.readyState == 4) {
          if (req.status == 200) {
            text=req.responseText;  
            parseXML(text);
          }
       }
    
  };

  req.send();
}

//TODO: Rewrite this. Use a real XML parser instead of this sketchy regex/substr hack.
function parseXML(text){

  var artists=new Array();
  var tracks=new Array();

  var splitted=text.split(/\r\n|\r|\n/);
  var pattern = new RegExp(/<name>.*<\/name>/);

  var artist=false;
  var counter=0

  for(var i=0; i<splitted.length; i++){
    if(splitted[i].match(pattern)){
      if(artist==true){
        var pos=splitted[i].indexOf(">")+1;
        var pos2=splitted[i].indexOf("</name>");
        var name=splitted[i].substring(pos, pos2);
        artists[counter]=name;
        artist=false;
        counter++;
      }
      else if(artist==false){ 
        var pos=splitted[i].indexOf(">")+1;
        var pos2=splitted[i].indexOf("</name>");
        var name=splitted[i].substring(pos, pos2);
        tracks[counter]=name;
        artist=true;
      }
    }
  }

  var tracksStr="";
  var artistsStr="";

  for(var i=0; i<tracks.length; i++){

    //Don't append delimiter to final elem
    if(i==tracks.length){
      tracksStr+=tracks[i];
      artistsStr+=artists[i]
    }

    //TODO: Make a decent delimiter - hash?
    else{
      tracksStr+=tracks[i]+"DELIMITER,";
      artistsStr+=artists[i]+"DELIMITER,";
    }
  }

  //Check if mac for file location processing
  var isMac=false;
  if (navigator.appVersion.indexOf("Mac")!=-1){
    isMac=true;
  }
  
  //Build form
  document.getElementById("content").innerHTML="<h2>Sucess!</h2><br />  Next, upload your itunes library .xml file, this is used to find the file locations for your playlist. It's usually located in 'C:\\Users\\YourUsernameHere\\Music\\iTunes' on Windows, or 'localhost/Users/YourUsernameHere/Music/iTunes' on Mac. No libraries are stored. <br /><p />Please note; if your file is large, you have many loved tracks, or a low upload speed, this may take some time. To reduce time, maximum file size is currently retracksicted to 20MB, and a maximum of 300 loved tracks will be used.";

  document.getElementById("content").innerHTML+="<form name=\"submittion\" action=\"processTracks.php\" id=\"submitForm\" method=\"post\" enctype=\"multipart/form-data\">";
  document.getElementById("submitForm").innerHTML+="<input type=\"file\" name=\"library\"></input>";
  document.getElementById("submitForm").innerHTML+="<input type=\"hidden\" name=\"tracksIn\"></input>";
  document.getElementById("submitForm").innerHTML+="<input type=\"hidden\" name=\"artistsIn\"></input>";
  document.getElementById("submitForm").innerHTML+="<input type=\"hidden\" name=\"OS\"></input>";
  document.getElementById("submitForm").innerHTML+="<input type=\"submit\" value=\"Lets go!\"></input>";

  document.getElementById("submitForm").tracksIn.value=tracksStr;

  alert(document.getElementById("submitForm").tracksIn.value);
  document.getElementById("submitForm").artistsIn.value=artistsStr;
  document.getElementById("submitForm").submitForm.OS.value=isMac;
}
