//Config values	
var pathToXMLStorage;
var pathToSlides;
var pathToPlayerFiles;	
var buildConfig = 1;	 //1 - local testing, 2 - production WA1
//variables
var playerElementID;
var presentationID;
var externalServiceID;
var slideArray = new Array();
var videoModule;
var playerWidth;

function loadPlayer5_thc(playerElementID,presentationID,externalService,externalServiceID, playerWidth){
	this.playerElementID = playerElementID;
	this.playerWidth = typeof playerWidth !== 'undefined' ? playerWidth : 960;
	this.presentationID = presentationID;
	this.externalServiceID = externalServiceID;
	initVariables();
	bootstrap();
}

function initVariables(){
	if(buildConfig == 1){
		pathToXMLStorage = "http://localhost/data/PresentationXMLs/";
		pathToSlides = "http://www.slideslive.com/data/PresentationSlides/";
		pathToPlayerFiles = "/SlidesLive/web/bundles/html5_player/player5_thc/";			
	}else if(buildConfig == 2){
		pathToXMLStorage = "http://www.slideslive.com/data/PresentationXMLs/";
		pathToSlides = "http://www.slideslive.com/data/PresentationSlides/";
		pathToPlayerFiles = "/SlidesLive_WA1/web/bundles/html5_player/player5_thc/";			
	}else{
		
	}
}

function bootstrap(){
	//Load all files, when loading last one, after success continue player init
	console.log("loading js files");
	var scriptFiles = new Array("youtubeModule.js","slideContainer.js","presentationController.js","playerGUI.js","jquery-ui.js");
	
	for (var i=0;i<scriptFiles.length-1;i++){ 
		$.getScript(pathToPlayerFiles+scriptFiles[i], function(data, textStatus, jqxhr) {
		   console.log('Load was performed.'+jqxhr.status);
		});
	}		
	$.getScript(pathToPlayerFiles+scriptFiles[scriptFiles.length-1], createDOMPlayer);	
}


function createDOMPlayer(){
	console.log("Creating DOM element");
	playerString="<div id=\"player5_container\" class=\"clearfix\">"+
"	<div id=\"player5_sliderBar\" class=\"clearfix\">"+
"		<div id=\"player5_slider\"></div>"+
"	</div>"+
"	<div id=\"player5_videoContainer\">You do not have flash player or Javascript enabled. I am unable to play anything :(</div>"+
"	<div class=\"clearfix\" id=\"player5_slideContainer\">"+
"		<div id=\"player5_slideLoader\">"+
"			<div id=\"player5_slideControls\" class=\"clearfix\">"+
"				<div id=\"player5_syncSlide\">pusinka</div>"+     
"				<div id=\"player5_prevSlide\">&lt;</div>"+        
"				<div id=\"player5_syncVideo\">[]</div>"+                    
"				<div id=\"player5_nextSlide\">&gt;</div>"+ 
"				<div id=\"player5_bigSlide\">big</div>"+                        
"			</div>"+
"		</div>"+
"	</div>"+
"</div>";
	$("#"+playerElementID).html(playerString);

	loadPresentationXML();		
}

function videoModuleReady(){
	initPresentationController();
}

function loadPresentationXML() {	
		xmlPath = pathToXMLStorage+presentationID+".xml";
		var request = $.ajax({
            url:xmlPath,
            type:'GET',
            dataType:"xml",
			success: xmlRequestReady
        });
}

function xmlRequestReady(responseXml){
	console.log("xml ready ");
	var counter = 0;
    $(responseXml).find("slide").each(function(){
		var tmpSlide = new Object();
		tmpSlide.slideName = $(this).find("slideName").text();
		tmpSlide.slideTime = $(this).find("timeSec").text();
		slideArray[counter] = tmpSlide;
		counter++;
	});
	
	initGUI(playerWidth);
	createYoutubePlayer(externalServiceID);	
}

function testPrint(){
      for(var i=0; i<slideArray.length; i++) {
		  var tmpO = slideArray[i];
	      console.log("Slide ID "+i+" - "+tmpO.slideName+" - "+tmpO.slideTime);
      }	
}