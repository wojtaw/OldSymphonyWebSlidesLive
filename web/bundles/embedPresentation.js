var xmlhttp;
var slidesLive = {
	//Public properties
	presentationID: 38889556, 
	width: 960,	
	videoSlideRatio: -1, //-1-100 -1 is balanced
	forceLightbox: true,
	zoomingOn: true,
	startSlide: 1,
	bgColor: "#1E1E1E",	
	
	//Private vars
	playerContainer: document.createElement('div'),
	hasVideo: true,
	hasSlides: true,
	mediaType: "",
	mediaID: "",
	isPaird: false,
	playerURL: "http://slideslive.com/bundles/player/Player_5_THC.swf",
	//playerURL: "http://localhost/SlidesLive/web/bundles/player/Player_5_THC.swf",
	presentationJsonApiUrl: "http://slideslive.com/embedjsonapi/",
	
	embedPresentation: function(presentationID, width, videoSlideRatio, forceLightbox, zoomingOn, startSlide) {
		console.log(presentationID);
		if(typeof(presentationID)!=='undefined') slidesLive.presentationID = presentationID;
		if(typeof(width)!=='undefined') slidesLive.width = width;		
		if(typeof(videoSlideRatio)!=='undefined') slidesLive.videoSlideRatio = videoSlideRatio;		
		if(typeof(forceLightbox)!=='undefined') slidesLive.forceLightbox = forceLightbox;						
		if(typeof(zoomingOn)!=='undefined') slidesLive.zoomingOn = zoomingOn;						
		if(typeof(startSlide)!=='undefined') slidesLive.startSlide = startSlide;										
		
		slidesLive.createPlayerContainer();
	},

	createPlayerContainer: function() {
		slidesLive.playerContainer.style.width = slidesLive.width+"px";
		slidesLive.playerContainer.style.height = slidesLive.height+"px";
		slidesLive.playerContainer.style.color = "white";
		getPresentationData();
				
		var embedScript = document.getElementById('sle81767'); embedScript.parentNode.insertBefore(slidesLive.playerContainer, embedScript);	
	},
	
	
	
	resizePlayerContainer: function(newHeight) {
		console.log("new function calleddddddddddddd");		
		slidesLive.height = newHeight;
		slidesLive.playerContainer.style.height = slidesLive.height+"px";		
	},
			
	
	createPlayerHtml: function(){
		var flashVarString = "hasVideo="+slidesLive.hasVideo+
							"&hasSlides="+slidesLive.hasSlides+
							"&presentationID="+slidesLive.presentationID+
							"&mediaType="+slidesLive.mediaType+
							"&mediaID="+slidesLive.mediaID+
							"&widthScale="+slidesLive.width+
							"&isEmbed=true"+																							
							"&isPaid=true"+slidesLive.isPaid+																											
							"&videoSlideRatio="+slidesLive.videoSlideRatio+
							"&zoomingOn="+slidesLive.zoomingOn+
							"&startSlide="+slidesLive.startSlide;																																																
	
		
		if(slidesLive.bgColor == "transparent"){
			console.log("transparent");			
			var colorParameter = "<param name=\"wmode\" value=\"transparent\">";
			var colorEmbed = "wmode=\"transparent\"";			
		} else {
			var colorParameter = "<param name=\"bgcolor\" value=\""+slidesLive.bgColor+"\">";
			var colorEmbed = "bgcolor=\""+slidesLive.bgColor+"\">";			
		}
					
		var playerString = "<object type=\"application/x-shockwave-flash\" width=\"100%\" height=\"100%\">"+
							"<param name=\"movie\" value=\""+slidesLive.playerURL+"\"></param>"+
							"<param name=\"allowFullScreen\" value=\"true\"></param>"+
							"<param name=\"allowscriptaccess\" value=\"always\"></param>"+														
							"<param name=\"flashvars\" value=\""+flashVarString+"\"></param>"+	
							colorParameter+																				
    						"<embed src=\""+slidesLive.playerURL+"\" "+colorEmbed+" flashvars=\""+flashVarString+"\"type=\"application/x-shockwave-flash\" width=\"100%\" height=\"100%\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>"+
							"</object>";
		return playerString;
	}
};


function resizeEmbedBridge(newHeight) {
	slidesLive.resizePlayerContainer(newHeight);		
}

function getPresentationData(){
	if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}	
	
	xmlhttp.open("GET",slidesLive.presentationJsonApiUrl+slidesLive.presentationID,true);
	xmlhttp.onreadystatechange=serverResponseProcess;
	xmlhttp.send();	
}

function serverResponseProcess(){
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
		var json = xmlhttp.responseText;
		var presentations = JSON.parse(json);
		var presentation = presentations[0];
		console.log(json);				
		slidesLive.hasVideo = presentation.hasVideo;
		slidesLive.hasSlides = presentation.hasSlides;
		slidesLive.mediaType = presentation.mediaType;
		slidesLive.mediaID = presentation.mediaID;									
		slidesLive.isPaid = presentation.isPaid;			
	}	
	slidesLive.playerContainer.innerHTML = slidesLive.createPlayerHtml();	  	
}
