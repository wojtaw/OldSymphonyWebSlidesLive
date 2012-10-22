 $(document).ready(function() {
   modifyDownloadAccordingOSGeneral();
   $("#shareEmbed").click(showEmbedModalWindow); 
 });


function modifyDownloadAccordingOSGeneral(){
            $('#os').html("<b>" + $.client.os + "</b>");
            $('#browser').html("<b>" + $.client.browser + "</b>");
          
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Windows.zip");													
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Linux.zip");																				
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SlidesLive-Mac.dmg");																
            } else {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_AllPlatforms.zip");																		
            }
}

function modifyThankyouPageAccordingOS(){
			//First make them hidden all
            $('#stepsWindows').css("display", "none");
            $('#stepsMac').css("display", "none");		
	
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#stepsWindows').css("display", "block");			
                $('#backupLink').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Windows.zip");				
																	
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#stepsMac').css("display", "block");
                $('#backupLink').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Linux.zip");					
																								
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#stepsWindows').css("display", "block");
                $('#backupLink').attr("href", "http://slideslive.com/data/SL_Recorder/SlidesLive-Mac.dmg");					
																				
            } else {
                $('#stepsWindows').css("display", "block");	
                $('#backupLink').attr("href", "http://slideslive.com/data/SL_Recorder/SL_AllPlatforms.zip");																									
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