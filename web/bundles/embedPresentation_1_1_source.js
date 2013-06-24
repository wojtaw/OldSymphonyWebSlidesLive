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
	this.lightboxShadow = document.createElement('div'),	
	this.lightboxClose = document.createElement('div'),		
	this.lightboxDisplayed = false,
	this.hasVideo = true,
	this.hasSlides = true,
	this.mediaType = "",
	this.mediaID = "",
	this.isPaid = false,
	this.playerURL = "http://slideslive.com/bundles/player/Player_5_THC.swf",
	//this.playerURL = "http://localhost/SlidesLive/web/bundles/player/Player_5_THC.swf",
	this.presentationJsonApiUrl = "http://slideslive.com/embedjsonapi/",
	
	this.embedPresentation = function(presentationID, width, videoSlideRatio, forceLightbox, zoomingOn, startSlide) {
		thisInstance.printLog("Embeding presentation"+presentationID);
		if(typeof(presentationID)!=='undefined') thisInstance.presentationID = presentationID;
		if(typeof(width)!=='undefined') thisInstance.width = width;		
		if(typeof(videoSlideRatio)!=='undefined') thisInstance.videoSlideRatio = videoSlideRatio;		
		if(typeof(forceLightbox)!=='undefined') thisInstance.forceLightbox = forceLightbox;						
		if(typeof(zoomingOn)!=='undefined') thisInstance.zoomingOn = zoomingOn;						
		if(typeof(startSlide)!=='undefined') thisInstance.startSlide = startSlide;										
		
		thisInstance.createPlayerContainer();
	},

	this.createPlayerContainer = function() {
		thisInstance.printLog("creating container");
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
		if(thisInstance.hasSlides && !thisInstance.hasVideo) newHeight = (parseInt(thisInstance.playerContainer.style.width) * 3) / 4;
		thisInstance.height = newHeight;
		thisInstance.printLog(thisInstance.height);		
		thisInstance.playerContainer.style.height = thisInstance.height+"px";		
	},
			
	
	this.createPlayerHtml = function(scaledWidth, lightboxCode){
		if(typeof(scaledWidth)=='undefined') scaledWidth = thisInstance.width;
		
		var flashVarString = "hasVideo="+thisInstance.hasVideo+
							"&hasSlides="+thisInstance.hasSlides+
							"&presentationID="+thisInstance.presentationID+
							"&mediaType="+thisInstance.mediaType+
							"&mediaID="+thisInstance.mediaID+
							"&widthScale="+scaledWidth+
							"&isEmbed=true"+																							
							"&isPaid="+thisInstance.isPaid+																											
							"&videoSlideRatio="+thisInstance.videoSlideRatio+
							"&zoomingOn="+thisInstance.zoomingOn+
							"&startSlide="+thisInstance.startSlide;	
		if(lightboxCode) flashVarString += "&autoplay=true";	
		
		if(thisInstance.bgColor == "transparent" || lightboxCode){
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
			thisInstance.printLog(json);				
			thisInstance.hasVideo = presentation.hasVideo;
			thisInstance.hasSlides = presentation.hasSlides;
			thisInstance.mediaType = presentation.mediaType;
			thisInstance.mediaID = presentation.mediaID;									
			thisInstance.isPaid = presentation.isPaid;			
		}	
		thisInstance.playerContainer.innerHTML = thisInstance.createPlayerHtml();	  	
	}
	
	this.hideLightBox = function() {
		thisInstance.lightboxDisplayed = false;
				
		thisInstance.lightboxShadow.style.display = "none";

		//Find script tag that called creation
		var scripts = document.getElementsByTagName("script");
		var embedScript = document.getElementById('sle81767'); 
		
		thisInstance.playerContainer.style.width = thisInstance.width+'px';	
		thisInstance.playerContainer.style.marginLeft = "0px";		
		thisInstance.playerContainer.style.marginRight = "0px";				
		thisInstance.playerContainer.innerHTML = thisInstance.createPlayerHtml();				

		//Try to find exact one
		for (var i=0; i<scripts.length; ++i ) {
		  if ( scripts[i].innerHTML.indexOf("embedPresentation("+thisInstance.presentationID) != -1 ) {
			embedScript = scripts[i];
		  }
		}
		
		embedScript.parentNode.insertBefore(thisInstance.playerContainer, embedScript);			
	}
	
	this.displayLightBox = function() {
		console.log("LIGHTBOX DISPLAYYY");
		if(thisInstance.width >= 800 || !thisInstance.forceLightbox || thisInstance.lightboxDisplayed) return false;

		thisInstance.lightboxDisplayed = true;
		
		document.onkeypress=function(e){
			var e=window.event || e
			if(e.keyCode == 27) thisInstance.hideLightBox();
		}		
		
		//Decide maximum player width according to type of content
		if(thisInstance.hasVideo){
			lbPlayerWidth = Math.round(0.8 * window.innerWidth);
			lbPlayerHeight = 500;
		} else {
			lbPlayerHeight = Math.round(0.9 * window.innerHeight);
			lbPlayerWidth = Math.round((4*lbPlayerHeight) / 3);		
			console.log("Audioslideshow, width: "+lbPlayerWidth+ "height: " + lbPlayerHeight );
		}
		
		thisInstance.lightboxShadow.style.width = "100%";	
		thisInstance.lightboxShadow.style.height = "100%";
		thisInstance.lightboxShadow.style.backgroundColor = "#000";
		thisInstance.lightboxShadow.style.backgroundColor = "rgba(0, 0, 0, 0.8)";		
		
		thisInstance.lightboxShadow.style.display = "block";		
		thisInstance.lightboxShadow.style.position = "fixed";				
		thisInstance.lightboxShadow.style.zIndex = "9999";						
		thisInstance.lightboxShadow.style.left = "0px";						
		thisInstance.lightboxShadow.style.top = "0px";

		thisInstance.lightboxClose.addEventListener("click",thisInstance.hideLightBox,true);
		thisInstance.lightboxClose.addEventListener("mouseover",thisInstance.lightUpCloseButton);		
		thisInstance.lightboxClose.addEventListener("mouseout",thisInstance.lightDownCloseButton);				
		thisInstance.lightboxClose.style.width = "50px";	
		thisInstance.lightboxClose.style.height = "50px";
		thisInstance.lightboxClose.style.backgroundImage = "url('http://slideslive.com/bundles/close-big.png')";	
		thisInstance.lightboxClose.style.backgroundRepeat = "no-repeat";
		thisInstance.lightboxClose.style.backgroundPosition = "50% 15px";		
		thisInstance.lightboxClose.style.cursor = "pointer";		
		thisInstance.lightboxClose.style.display = "block";		
		thisInstance.lightboxClose.style.position = "fixed";				
		thisInstance.lightboxClose.style.zIndex = "9999";						
		thisInstance.lightboxClose.style.right = "0px";						
		thisInstance.lightboxClose.style.top = "0px";		
		
		thisInstance.playerContainer.style.width = lbPlayerWidth+'px';	
		thisInstance.playerContainer.style.height = lbPlayerHeight+"px";
		thisInstance.playerContainer.style.marginTop = "50px";			
		thisInstance.playerContainer.style.marginLeft = "auto";		
		thisInstance.playerContainer.style.marginRight = "auto";				

		document.body.appendChild(thisInstance.lightboxShadow);
		thisInstance.lightboxShadow.appendChild(thisInstance.playerContainer);		
		thisInstance.lightboxShadow.appendChild(thisInstance.lightboxClose);				

		thisInstance.playerContainer.innerHTML = thisInstance.createPlayerHtml(lbPlayerWidth,true);		
		//document.body.insertBefore(thisInstance.lightboxShadow, document.body.firstChild);
	}
	
	this.lightUpCloseButton = function() {
		thisInstance.lightboxClose.style.backgroundPosition = "50% -28px";	
	}
	
	this.lightDownCloseButton = function() {
		thisInstance.lightboxClose.style.backgroundPosition = "50% 15px";	
	}	
	
	this.printLog = function(message){
		console.log(message);
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

function slFirstPlayBridge(presentationID) {
	console.log("test play fired - "+presentationID);
	
	for (var i = 0; i < allEmbeds.length; i++) {
		if(allEmbeds[i].presentationID == presentationID) allEmbeds[i].displayLightBox();
	}			
}
