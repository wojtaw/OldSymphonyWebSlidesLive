 $(document).ready(function() {
   $("#shareEmbed").click(showEmbedModalWindow); 
   runQuotesTimelapse();
 });
 
 var appDownloadWindows = "http://slideslive.com/data/SL_Recorder/SL_Windows.zip";
 var appDownloadLinux = "http://slideslive.com/data/SL_Recorder/SL_Linux.zip";
 var appDownloadMac = "http://slideslive.com/data/SL_Recorder/SlidesLive-Mac.dmg";
 var appDownloadAll = "http://slideslive.com/data/SL_Recorder/SL_AllPlatforms.zip";   

function redirectDownload(redirectUrl){
	window.location.replace(redirectUrl);
}

function modifyThankyouPageAccordingOS(){
			//First make them hidden all
            $('#stepsWindows').css("display", "none");
            $('#stepsMac').css("display", "none");				
	
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#stepsWindows').css("display", "block");			
                $('#backupLink').attr("href", appDownloadWindows);
				setTimeout(function(){redirectDownload(appDownloadWindows);},1500);
																	
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#stepsWindows').css("display", "block");
                $('#backupLink').attr("href", appDownloadLinux);	
				setTimeout(function(){redirectDownload(appDownloadLinux);},1500);																		
																								
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#stepsMac').css("display", "block");
                $('#backupLink').attr("href", appDownloadMac);	
				setTimeout(function(){redirectDownload(appDownloadMac);},1500);																				
																				
            } else {
                $('#stepsWindows').css("display", "block");	
                $('#backupLink').attr("href", appDownloadAll);	
				setTimeout(function(){redirectDownload(appDownloadAll);},1500);																																							
            }	
}


function showEmbedModalWindow(){
	$('#embedWindowContent').modal();
}



function resizePlayerContainer(playerHeight) { 
	//Add kind of padding
	playerHeight += 15;
	$('#playerPanel').css({ height: playerHeight });
	$('.gradientLeftPlayer').css({ height: (playerHeight + 144) });	
	$('.gradientRightPlayer').css({ height: (playerHeight + 144) });		
	$('.gradientRightPlayer').css({ top: -(playerHeight + 144) });			
}

function websiteOutput(outputMessage){
	console.log(outputMessage);	
}

function playerDimensionsAccordingScreen(playerType){
	var newWidth;
	var windowHeight = $(window).height();
	//check if window height ir reasonable
	if(windowHeight < 0 || windowHeight > 2000) return -1;
	if(playerType == "AUDIO") {	
		if (windowHeight < 480) {
			newWidth = 640;
		} else if(windowHeight >= 480 && windowHeight <= 820) {
			windowHeight -= 100;
			newWidth = (4*windowHeight) / 3;
		} else {
			newWidth = 960;
		}	
		return newWidth;
	} else if(playerType == "AUDIOVIDEO") {
		return 960;
	} else if(playerType == "VIDEO") {
		if (windowHeight < 480) {
			newWidth = 850;
		} else if(windowHeight >= 480 && windowHeight <= 640) {
			windowHeight -= 100;
			newWidth = (16*windowHeight) / 9;
		} else {
			newWidth = 960;
		}			
		return newWidth;	
	} else {
		return -1;			
	}
}


function runQuotesTimelapse(){
	console.log("quotes changing");	
	setInterval(function(){changeQuote()},3000);
}

function changeQuote(){
	console.log("quotes changing");
	$('#quoteBox').fadeOut(500,changeQuoteContent());	
}

function changeQuoteContent(){
	
}
