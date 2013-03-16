$(document).ready(documentReadyHandler)


function embedSchoolPresentation(){
	var embedSize = 0.8 * $(window).width();
	$('.embedWrapper').css("width", embedSize);
	
	slidesLive = createSlidesLiveBox();
    slidesLive.bgColor="transparent";
    //slidesLive.zoomingOn=true;
    slidesLive.videoSlideRatio=45;			
	slidesLive.embedPresentation(38889038,embedSize);		
}

function embedConferencePresentation(){
	var embedSize = 0.8 * $(window).width();
	$('.embedWrapper').css("width", embedSize);
	
	slidesLive = createSlidesLiveBox();
    slidesLive.bgColor="transparent";
    //slidesLive.zoomingOn=true;
    slidesLive.videoSlideRatio=45;			
	slidesLive.embedPresentation(38889565,embedSize);		
}

function documentReadyHandler(){
	setTimeout(animateBlueBar, 3000);
}

function animateBlueBar(){
	$('#blueTopBar').animate({top:'0px',opacity:'0.85'},2000);
}