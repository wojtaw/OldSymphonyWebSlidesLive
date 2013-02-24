var allEmbeds = new Array();
var currentEmbedIndex = 0;
function slidesLiveEmbedBox(){
	//Public properties
	var thisInstance = this;
	this.xmlhttp = 5;
	this.presentationID = 38889556;
	this.width = 960;
	this.height = 100;		
	this.videoSlideRatio = -1; //-1-100 -1 is balanced
	this.forceLightbox = true;
	this.zoomingOn = true;
	this.startSlide = 1;
	this.bgColor = "#1E1E1E";	
	
	//Private vars
	this.playerContainer = document.createElement('div'),
	this.hasVideo = true,
	this.hasSlides = true,
	this.mediaType = "",
	this.mediaID = "",
	this.isPaird = false,
	this.playerURL = "http://slideslive.com/bundles/player/Player_5_THC.swf",
	//this.playerURL = "http://localhost/SlidesLive/web/bundles/player/Player_5_THC.swf",
	this.presentationJsonApiUrl = "http://slideslive.com/embedjsonapi/",
	
	this.embedPresentation = function(presentationID, width, videoSlideRatio, forceLightbox, zoomingOn, startSlide) {
		console.log("Embeding presentation"+presentationID);
		if(typeof(presentationID)!=='undefined') thisInstance.presentationID = presentationID;
		if(typeof(width)!=='undefined') thisInstance.width = width;		
		if(typeof(videoSlideRatio)!=='undefined') thisInstance.videoSlideRatio = videoSlideRatio;		
		if(typeof(forceLightbox)!=='undefined') thisInstance.forceLightbox = forceLightbox;						
		if(typeof(zoomingOn)!=='undefined') thisInstance.zoomingOn = zoomingOn;						
		if(typeof(startSlide)!=='undefined') thisInstance.startSlide = startSlide;										
		
		thisInstance.createPlayerContainer();
	},

	this.createPlayerContainer = function() {
		console.log("creating container");
		thisInstance.playerContainer.style.width = thisInstance.width+'px';	
		thisInstance.playerContainer.style.height = thisInstance.height+'px';		
		thisInstance.playerContainer.style.color = "white";
		thisInstance.playerContainer.setAttribute("id", thisInstance.presentationID);
		thisInstance.getPresentationData();
		
		//Find script tag that called creation
		var scripts = document.getElementsByTagName("script");
		var embedScript = document.getElementById('sle81767'); 

		//Try to find exact one
		for (var i=0; i<scripts.length; ++i ) {
		  if ( scripts[i].innerHTML.indexOf("embedPresentation("+thisInstance.presentationID) != -1 ) {
			embedScript = scripts[i];
		  }
		}
		
		embedScript.parentNode.insertBefore(thisInstance.playerContainer, embedScript);	
	},
	
	
	
	this.resizePlayerContainer = function(newHeight) {
		thisInstance.height = newHeight;
		console.log(thisInstance.height);		
		thisInstance.playerContainer.style.height = thisInstance.height+"px";		
	},
			
	
	this.createPlayerHtml = function(){
		var flashVarString = "hasVideo="+thisInstance.hasVideo+
							"&hasSlides="+thisInstance.hasSlides+
							"&presentationID="+thisInstance.presentationID+
							"&mediaType="+thisInstance.mediaType+
							"&mediaID="+thisInstance.mediaID+
							"&widthScale="+thisInstance.width+
							"&isEmbed=true"+																							
							"&isPaid=true"+thisInstance.isPaid+																											
							"&videoSlideRatio="+thisInstance.videoSlideRatio+
							"&zoomingOn="+thisInstance.zoomingOn+
							"&startSlide="+thisInstance.startSlide;																																																
	
		
		if(thisInstance.bgColor == "transparent"){
			var colorParameter = "<param name=\"wmode\" value=\"transparent\">";
			var colorEmbed = "wmode=\"transparent\"";			
		} else {
			var colorParameter = "<param name=\"bgcolor\" value=\""+thisInstance.bgColor+"\">";
			var colorEmbed = "bgcolor=\""+thisInstance.bgColor+"\"";			
		}
					
		var playerString = "<object type=\"application/x-shockwave-flash\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"100%\" height=\"100%\">"+
							"<param name=\"movie\" value=\""+thisInstance.playerURL+"\"></param>"+
							"<param name=\"allowFullScreen\" value=\"true\"></param>"+
							"<param name=\"allowscriptaccess\" value=\"always\"></param>"+														
							"<param name=\"flashvars\" value=\""+flashVarString+"\"></param>"+	
							colorParameter+																				
    						"<embed src=\""+thisInstance.playerURL+"\" "+colorEmbed+" flashvars=\""+flashVarString+"\" type=\"application/x-shockwave-flash\" width=\"100%\" height=\"100%\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"  allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>"+
							"</object>";
		return playerString;
	}
	
	this.getPresentationData = function(){
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			thisInstance.xmlhttp=new XMLHttpRequest();
		} else {// code for IE6, IE5
			thisInstance.xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}	
		
		thisInstance.xmlhttp.open("GET",thisInstance.presentationJsonApiUrl+thisInstance.presentationID,true);
		thisInstance.xmlhttp.onreadystatechange=thisInstance.serverResponseProcess;
		thisInstance.xmlhttp.send();	
	}
	
	this.serverResponseProcess = function(){
		if (thisInstance.xmlhttp.readyState==4 && thisInstance.xmlhttp.status==200)
		{
			var json = thisInstance.xmlhttp.responseText;
			var presentations = JSON.parse(json);
			var presentation = presentations[0];
			console.log(json);				
			thisInstance.hasVideo = presentation.hasVideo;
			thisInstance.hasSlides = presentation.hasSlides;
			thisInstance.mediaType = presentation.mediaType;
			thisInstance.mediaID = presentation.mediaID;									
			thisInstance.isPaid = presentation.isPaid;			
		}	
		thisInstance.playerContainer.innerHTML = thisInstance.createPlayerHtml();	  	
	}	
};

function createSlidesLiveBox(){
	var tmpSL = new slidesLiveEmbedBox();
	allEmbeds[currentEmbedIndex] = tmpSL;
	currentEmbedIndex++;
	return tmpSL;	
}


function resizeEmbedBridge(newHeight, presentationID) {
	for (var i = 0; i < allEmbeds.length; i++) {
		if(allEmbeds[i].presentationID == presentationID) allEmbeds[i].resizePlayerContainer(newHeight);
	}		
}
