var videoRatio = (16/9);
var slideRatio = (4/3);
var videoSlideRatio = (4/3);
var stageWidth = 960;
var stageHeight = 300;
var universalSpace = 15;

function initGUI(stageWidth){
	this.stageWidth = stageWidth;
	initStageWidth();
	initSlider();
	initListeners();	
}

function initStageWidth(){
	//Check if browser window isn't smaller then stage width
	if($(window).width() < stageWidth) stageWidth = $(window).width();
	
	$('#player5_container').width(stageWidth);	
	var ui = new Object();
	ui.value = (100 / 1.777777);
	recalculateGUI(null,ui);
}

function initSlider(){
	$( "#player5_slider" ).slider({
    slide: recalculateGUI,
	value: 50,
	animate: "fast"
	});
}

function recalculateGUI(event, ui){
	console.log("recalculating GUI - "+ui.value);	
	videoWidth = ((ui.value / 100) * stageWidth);
	recalculateSlidesAndVideoContainers(videoWidth);
	decideSlideQuality($('#player5_slideContainer').width());	
	recalculateControlsPosition();
	//External method for adapting the website
	resizePlayerContainer(stageHeight+65);
}

function recalculateControlsPosition(){
	//Is slide container wide enough for controls?
	var difference = $('#player5_slideContainer').width() - $('#player5_slideControls').width();
	if(difference < 20){
		$('#player5_slideControls').css('display','none');
	}else{
		$('#player5_slideControls').css('display','block');		
	}
}

function recalculateSlidesAndVideoContainers(videoWidth){
	var tmpSlidesWidth;
	var tmpSlidesHeight;

	videoContainerWidth = videoWidth;
	videoContainerHeight = (videoWidth / videoRatio);
	
	if(videoContainerWidth < 50){
		$('#player5_videoContainer').css('display','none');
		
		tmpSlidesWidth = stageWidth;
		tmpSlidesHeight = (tmpSlidesWidth / slideRatio);
		$('#player5_slideContainer').width(Math.round(tmpSlidesWidth));
		$('#player5_slideContainer').height(Math.round(tmpSlidesHeight));				
	}else{
		$('#player5_videoContainer').css('display','block');		
		$('#player5_videoContainer').width(Math.round(videoContainerWidth));
		$('#player5_videoContainer').height(Math.round(videoContainerHeight));	
		thcSetPlayerSize(Math.round(videoContainerWidth),Math.round(videoContainerHeight));		
		
		tmpSlidesWidth = (stageWidth-videoContainerWidth)- universalSpace;
		tmpSlidesHeight = (tmpSlidesWidth / slideRatio);
		
		$('#player5_slideContainer').width(Math.round(tmpSlidesWidth));
		$('#player5_slideContainer').height(Math.round(tmpSlidesHeight));				
	}
	stageHeight = Math.max(tmpSlidesHeight,videoContainerHeight);	
}
	
	

function initListeners(){
	$("#player5_prevSlide").click(prevSlideClicked); 
	$("#player5_nextSlide").click(nextSlideClicked); 
	$("#player5_syncVideo").click(syncVideoClicked); 
	$("#player5_syncSlide").click(syncSlideClicked); 
	$("#player5_bigSlide").click(bigSlideClicked); 				
}

function prevSlideClicked(){
	console.log("Prev slide");
	prevSlideRequest();
}

function nextSlideClicked(){
	console.log("Next slide");
	nextSlideRequest();		
}

function syncVideoClicked(){
	console.log("Video sync click");
	rewindVideoRequest();		
}

function syncSlideClicked(){
	console.log("Slides sync click");
	syncSlidesRequest();		
}

function bigSlideClicked(){
	console.log("Slides sync click");
	bigSlideRequest();		
}