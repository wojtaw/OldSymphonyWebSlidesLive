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
	animateTryBox();
	animateArrow();
	initListeners();
}

function initListeners() {
	$("#circleArrow").click(function(event) {
  		event.preventDefault();
		$("html, body").animate({ scrollTop: $('#embedPoint').offset().top }, 2000);
		console.log("scrollliing");
	});	
}

function animateTryBox(e){
	$('#tryThisBox').animate({left:'+=275px',opacity:'0.85'},4000,"swing",animateTryBoxBack);	
}

function animateTryBoxBack(e){
	$('#tryThisBox').animate({left:'-=275px',opacity:'1'},4000,"swing",animateTryBox);	
}

function animateArrow(e){
	$('.circleArrow').animate({top:'+=30px',opacity:'0.3'},2000,"swing",animateArrowBack);	
}

function animateArrowBack(e){
	$('.circleArrow').animate({top:'-=30px',opacity:'0.15'},2000,"swing",animateArrow);	
}

function animateBlueBar(){
	$('#blueTopBar').animate({top:'0px',opacity:'0.85'},2000);
}