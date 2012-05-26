//TODO FOR EACH
//make function for DOM parser

function runForm(callback){
  switch(document.inputForm.selectType.value){
    case 'loved':
      getLastFmLoved(getJSLovedTracks);
      break;
    case 'top':
      getAndAppendTop([]);
      break;
    case 'lovedAndTop':
      getLastFmLoved(getAndAppendTop);
      break;
  }
}

function getLastFmLoved(callback){
  var username=document.inputForm.username.value;
  var req=$.get("http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&user="+username+"&api_key=d7113c54fb740ab1e97398776926fdc4&limit=400", function(data){
    var objs=parseAPIResponseXML(data);
    callback(objs);
  });
}

function getAndAppendTop(inputArr){
 var username=document.inputForm.username.value;
  var limit=document.inputForm.numOfTop.value;
  var req=$.get("http://ws.audioscrobbler.com/2.0/?method=user.gettoptracks&user="+username+"&api_key=d7113c54fb740ab1e97398776926fdc4&limit="+limit, function(data){
    var objs=parseAPIResponseXML(data);
    postProcess(objs, inputArr);
  });
}

function postProcess(topTracks, lovedTracks){
  var topLarger=topTracks>lovedTracks;
  var newArr=lovedTracks;

  for (var i in topTracks){
    if(($.inArray(topTracks[i], lovedTracks))==-1){
      newArr.push(topTracks[i]);
    }
  }

  console.log(newArr);
  getJSLovedTracks(newArr);
}


function parseAPIResponseXML(doc){

  var items=new Array();

  var artist=false;
  var counter=0;

  tracks=doc.getElementsByTagName("track");

  for (i=0; i<tracks.length; i++){
    thisItem={};
    names=tracks[i].getElementsByTagName("name");
    thisItem.trackName=names[0].childNodes[0].nodeValue;
    thisItem.artistName=names[1].childNodes[0].nodeValue;
    items[i]=thisItem;
  }

  return items;


}

function parseUserLibrary(libraryXML){
  if(window.DOMParser){
    parser=new DOMParser();
    xml=parser.parseFromString(libraryXML,"text/xml");
  }
  else{
    xml=new ActiveXObject("Microsoft.XMLDOM");
    xml.async=false;
    xml.loadXML(libraryXML);
  }
  if(!xml){
      document.getElementById("dropZone").setAttribute("class", "error");
      document.getElementById("dropZone").innerHTML="Fail! Couldn't parse your library! (╯°□°）╯︵ ┻━┻";
      return;
  }

  lib=xml.getElementsByTagName("dict")[1];

  if(!lib){
      document.getElementById("dropZone").setAttribute("class", "error");
      document.getElementById("dropZone").innerHTML="Fail! Couldn't parse your library! (╯°□°）╯︵ ┻━┻";
      return;
  }
  lib=(lib.getElementsByTagName("dict"));

  numTracks=(lib.length);
  var trackObjArr=[];

  //For each track
  for(i=0;i<numTracks;i++){
    thisTrack=lib[i];
    thisTrackObj={};
    thisTrackObj.id=i;

    //For each xml item
    for (j=0;j<thisTrack.childNodes.length; j++){
      if(thisTrack.childNodes[j].nodeName=='key'){
          item=thisTrack.childNodes[j].childNodes[0].nodeValue;
          switch(item){
            case 'Name':
                 thisTrackObj.trackName=thisTrack.childNodes[j+1].childNodes[0].nodeValue;
                 break;
            case 'Artist':
                 thisTrackObj.artistName=thisTrack.childNodes[j+1].childNodes[0].nodeValue;
                 break;
            case 'Location':
              if (navigator.appVersion.indexOf("Mac")==-1){
                 thisTrackObj.fileLocation=thisTrack.childNodes[j+1].childNodes[0].nodeValue;
              }
              else{
                 thisTrackObj.fileLocation=thisTrack.childNodes[j+1].childNodes[0].nodeValue.substr(16);
              }
                 break;
            default:
                 break;
          }
      }
    }
    if(!(thisTrackObj.artistName)){
      thisTrackObj.artistName="unknown";
    }
    else if(!(thisTrackObj.trackName)){
      thisTrackObj.trackName="unknown";
    }
    //tracks can't not have a location.
    
    trackObjArr[i]=thisTrackObj;
  }
trackObjStr=JSON.stringify(trackObjArr);

document.inputForm.trackObjStr.value=trackObjStr;
document.getElementById("submitBtn").removeAttribute("disabled");
document.getElementById("dropZone").setAttribute("class", "okay");
document.getElementById("dropZone").innerHTML="...File looks legit, good to go!";
}

function handleThreshChange(){
  if(document.inputForm.matchThreshold.value==("default")){
    document.inputForm.matchThreshold.className="nullSelect";
  }
  else{
    document.inputForm.matchThreshold.className=("legitSelect");
    }
}

function handleSelectChange(){
  if(document.inputForm.selectType.value==("loved")){
    document.inputForm.removeChild(document.inputForm.numOfTop);
    delete document.inputForm.numOfTop;
  }
  else if(document.inputForm.selectType.value!=("loved")&&(!document.inputForm.numOfTop)){
    var input=document.createElement("input");
    input.id="numOfTop";
    input.type="number";
    input.placeholder="Use top ... tracks";
    document.inputForm.insertBefore(input, document.inputForm.matchThreshold);
  }
}
  
 function handleDragOver(evt) {
              evt.stopPropagation();
              evt.preventDefault();
              evt.dataTransfer.dropEffect = 'copy'; 
          }

  function handleFileSelect(evt) {
    evt.stopPropagation();
    evt.preventDefault();

    var file = evt.dataTransfer.files[0]; 

    if(file.type !='text/xml'){
      document.getElementById("dropZone").setAttribute("class", "error");
      document.getElementById("dropZone").innerHTML="Fail! Wrong file type! (╯°□°）╯︵ ┻━┻";
    }
    else{
      var reader = new FileReader();
      reader.readAsText(file);

      reader.onerror=function(){
      document.getElementById("dropZone").setAttribute("class", "error");
      document.getElementById("dropZone").innerHTML="Invalid file type";
    }

      reader.onloadend=function(){
      var inputText=reader.result;
      parseUserLibrary(inputText);
    }
    }
  }

function getThresholdMultiplier(){
  switch(document.inputForm.matchThreshold.value){
    case 'perfect':
      return 1;
      break;
    case 'strong':
      return 0.8;
      break;
    case 'intermediate':
      return 0.6;
      break;
    case 'weak':
      return 0.35;
      break;
    case 'anything':
      return 0;
  }
}


function getJSLovedTracks(lovedItems){

  var tracksStr=document.inputForm.trackObjStr.value;
  var tracksObjArr=eval(tracksStr);
  var perfectMatch=[];
  var semiMatch=[]
  var noMatch=[];
   
  for(var i in lovedItems){
    var maxScore=0;
    var bestTrack;
    lovedArtistTokens=lovedItems[i].artistName.toLowerCase().split(" ");
    lovedTrackTokens=lovedItems[i].trackName.toLowerCase().split(" ");
    var perfectScore=lovedArtistTokens.length+(1.5*(lovedTrackTokens.length));

    for (var j in tracksObjArr){
      var score=0;
      currentTrack=tracksObjArr[j];
      artistTokens=currentTrack.artistName.toLowerCase().split(" ");
      trackTokens=currentTrack.trackName.toLowerCase().split(" ");
      for(var k in lovedArtistTokens){
        if(($.inArray(lovedArtistTokens[k], artistTokens))!=-1){
          score+=1;
        }
      }
      for(var k in lovedTrackTokens){
        if(($.inArray(lovedTrackTokens[k], trackTokens))!=-1){
          score+=1.5;
        }
      }
      if(score>maxScore){
        bestTrack=currentTrack;
        maxScore=score;
      }
    }

    targetScore=perfectScore*getThresholdMultiplier();
    

    if(maxScore==perfectScore){
      perfectMatch.push([lovedItems[i],bestTrack]);
    }
    else if(maxScore>=targetScore){
      semiMatch.push([lovedItems[i],bestTrack]);
    }
    else{
      noMatch.push(lovedItems[i]);
    }
    

  }
  document.inputForm.perfectMatches.value=JSON.stringify(perfectMatch);
  document.inputForm.semiMatches.value=JSON.stringify(semiMatch);
  document.inputForm.failedMatches.value=JSON.stringify(noMatch);
  document.inputForm.submit();
}
