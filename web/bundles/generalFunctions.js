 $(document).ready(function() {
   $("#shareEmbed").click(showEmbedModalWindow); 
   runQuotesTimelapse();
 });
 
 var appDownloadWindows = "http://slideslive.com/data/SL_Recorder/SL_Windows.zip";
 var appDownloadLinux = "http://slideslive.com/data/SL_Recorder/SL_Linux.zip";
 var appDownloadMac = "http://slideslive.com/data/SL_Recorder/SlidesLive-Mac.dmg";
 var appDownloadAll = "http://slideslive.com/data/SL_Recorder/SL_AllPlatforms.zip";
 
 var notesInterval;

function redirectDownload(redirectUrl){
	window.location.replace(redirectUrl);
}

function modifyThankyouPageAccordingOS(){
			//First make them hidden all
            $('#stepsWindows').css("display", "none");
            $('#stepsMac').css("display", "none");				
	
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#stepsWindows').css("display", "block");		
				modifiedUrl = "url(../bundles/static/images/steps-win.jpg)";
                $('#thankYouSteps').css("background-image", modifiedUrl);
                $('#thankYouSteps').css("padding-top", "190px");				
                $('#backupLink').attr("href", appDownloadWindows);
				setTimeout(function(){redirectDownload(appDownloadWindows);},1500);
																	
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#stepsWindows').css("display", "block");
                $('#backupLink').attr("href", appDownloadLinux);	
				setTimeout(function(){redirectDownload(appDownloadLinux);},1500);																		
																								
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#stepsMac').css("display", "block");
				modifiedUrl = "url(../bundles/static/images/steps-mac.jpg)";
                $('#thankYouSteps').css("background-image", modifiedUrl);
                $('#thankYouSteps').css("padding-top", "210px");											
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
	playerHeight += 0;
	console.log("setting height -------------------------------------------------------------------------");
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

function quoteContent(message,author,source){
this.message=message;
this.author=author;
this.source=source;
}

var allQuotes = new Object();
var currentQuoteIndex = 0;
var numberOfQuotes = 5;		

allQuotes[1] = new quoteContent("<img src=\"/SlidesLive/web/bundles/static/images/logo-pioneers.png\" width=\"360\" height=\"90\">",
								"SlidesLive at Pioneers festival<br />Vienna Oct 29-31, 2012",
								"http://slideslive.com/pioneersfestival");
								
allQuotes[2] = new quoteContent("This looks very cool! Can't wait to see more.",
								"Nathan Gold<br />The DEMOCOACH",
								"http://slideslive.com/DEMOCOACH");

allQuotes[3] = new quoteContent("LIVE Recording &amp; Automatic SYNC<br />It is so awsome! Thanks.",
								"Filip Blazek<br />DE.SIGN",
								"http://slideslive.com/DE.SIGN");
								
allQuotes[4] = new quoteContent("<img src=\"/SlidesLive/web/bundles/static/images/devel.png\" width=\"360\" height=\"109\">",
								"Devel.cz<br />IT Conference",
								"http://slideslive.com/Devel.cz");	
								
allQuotes[0] = new quoteContent("One button recording and a seriously easy editing tool.<br />Publishing a talk doesn't get any easier.",
								"Paul Winstanley<br />OpenInformatics Conference",
								"http://slideslive.com/OpenInformatics");															
								


function runQuotesTimelapse(){
	//Set at the end and run first content change
	currentQuoteIndex=numberOfQuotes;
	changeQuoteContent();
	//Set periodicall changing
	setInterval(function(){changeQuote()},7000);
}

function changeQuote(){
	$('#quoteAnimationWrapper').animate({opacity: 0},500,function() {changeQuoteContent()});	
}

function changeQuoteContent(){
	currentQuoteIndex++;
	if(currentQuoteIndex > numberOfQuotes-1) currentQuoteIndex = 0;
	$('.quotesText').html(allQuotes[currentQuoteIndex].message);	
	$('.quotesAuthor').html(allQuotes[currentQuoteIndex].author);	
	$('#quotesLink').attr("href", allQuotes[currentQuoteIndex].source);
	$('#quoteAnimationWrapper').animate({opacity: 1},500);		
}


function incorrectLoginAction(){
	$('#username').css("border-color", "#FF3300");	
	$('#password').css("border-color", "#FF3300");				
}

function startPresentationNotesRefresh(){
	notesInterval = setTimeout(refreshNotes,1000);    
}

function refreshNotes(){
	console.log("Notess jeje");
}




