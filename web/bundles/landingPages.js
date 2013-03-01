// JavaScript Document
function embedSchoolPresentation(){
	var embedSize = 0.8 * $(window).width();
	$('.embedWrapper').css("width", embedSize);
	
	slidesLive = createSlidesLiveBox();
    slidesLive.bgColor="transparent";
    //slidesLive.zoomingOn=true;
    //slidesLive.videoSlideRatio=20;			
	slidesLive.embedPresentation(38889017,embedSize);		
}