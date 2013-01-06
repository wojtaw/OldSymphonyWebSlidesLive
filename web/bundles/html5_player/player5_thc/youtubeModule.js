// JavaScript Document
var externalServiceID;
var playerYT;	

function createYoutubePlayer(externalServiceID){
	this.externalServiceID = externalServiceID;
	console.log("Creating youtube player"+externalServiceID);
     var tag = document.createElement('script');
      tag.src = "http://www.youtube.com/iframe_api";
      var firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
}

function onYouTubeIframeAPIReady(){
	playerYT = new YT.Player('player5_videoContainer', {
	  height: '300',
	  width: '544',
	  videoId: this.externalServiceID,
	  events: {
		'onReady': onPlayerReady,
		'onStateChange': onPlayerStateChange
	  }
	});	
	console.log("Player added");	
}

// 4. The API will call this function when the video player is ready.
function onPlayerReady(event) {
	console.log("Youtube Player ready");
	videoModuleReady();	
}


function onPlayerStateChange(event) {
	console.log("Youtube state change -------------------"+event.data);
	if(event.data == 1 || event.data == 2) videoSeekHandler();
}



function thcPlayVideo(){
	trace("Options: "+playerYT.getOptions());
	PlayerOutput.printLog("Play video");
	//playerYT.playVideo();
	playerYT.playVideo();
	return true;
}

function thcPauseVideo(){
	PlayerOutput.printLog("Pause video");
	playerYT.pauseVideo();
	return true;
}	

function thcGetCurrTime(){
	return playerYT.getCurrentTime();
}	

function thcGetTotalTime(){
	return playerYT.getDuration();
}	

function thcSeekTime(tmpTime){
	playerYT.seekTo(tmpTime)
	return true;
}	

function thcGetBytesLoaded(){
	return playerYT.getVideoBytesLoaded();
}			

function thcGetBytesTotal(){
	return playerYT.getVideoBytesTotal();
}	

function thcSetVolume(vol) {
	playerYT.setVolume(100*vol);
	return true;
}
		
function thcSetPlayerSize(width, height) {
	if(playerYT == null) return false;
	else playerYT.setSize(width,height);
	console.log("resizing player YOUTUBEEEEEEEEE");
	return true;
}		
