// JavaScript Document
var synchronizedSlides = true;
var currentSlideIndex = 0;
var slideQuality = "medium";

function initPresentationController(){
	console.log("Igniting player");	
	window.setInterval(runTimerTasks,200);
	reloadSlide();
}

function runTimerTasks(){
	checkSlideToUpdate();
}

function checkSlideToUpdate(){
	if(!synchronizedSlides) return false;
	if(currentSlideIndex < slideArray.length-1){
		if(thcGetCurrTime() > slideArray[currentSlideIndex+1].slideTime){
			reloadSlide("NEXT");
		}
	}	
	return true;
}

function reloadSlide(reloadType){
	//Default parameter value
	reloadType = typeof reloadType !== 'undefined' ? reloadType : "RELOAD";
	console.log("Reloading slide");	
	if(reloadType == "RELOAD"){

	}else if(reloadType == "NEXT"){
		currentSlideIndex++;
		checkCurrentIndex();
	}else if(reloadType == "PREV"){
		currentSlideIndex--;
		checkCurrentIndex();
	}else{
		return;
	}
	cacheSlides();
	var tmpPath = pathToSlides +slideQuality+"/"+ presentationID +"/"+ slideArray[currentSlideIndex].slideName;
	loadSlide(tmpPath);			
}

function cacheSlides(){
	console.log("caching slides");
	var tmpPath = "url("+pathToSlides +slideQuality+"/"+ presentationID +"/"+ slideArray[countIndex(currentSlideIndex+1)].slideName+")";	
	$('#player5_preloader1').css("background-image", tmpPath);	
	tmpPath = "url("+pathToSlides +slideQuality+"/"+ presentationID +"/"+ slideArray[countIndex(currentSlideIndex-1)].slideName+")";	
	$('#player5_preloader2').css("background-image", tmpPath);		
			
}

function videoSeekHandler(){
	synchronizeSlides();	
}

function checkCurrentIndex(){
	if(currentSlideIndex >= slideArray.length) currentSlideIndex = 0;
	else if(currentSlideIndex < 0) currentSlideIndex = slideArray.length-1;
}

function countIndex(searchedIndex){
	var valueToReturn = 0;
	if(searchedIndex >= slideArray.length) valueToReturn = searchedIndex - slideArray.length;
	else if(searchedIndex < 0) valueToReturn = slideArray.length + searchedIndex;
	else valueToReturn = searchedIndex;
	return valueToReturn;
}	

function nextSlideRequest(){
	synchronizedSlides = false;
	reloadSlide("NEXT");
}

function prevSlideRequest() {
	synchronizedSlides = false;
	reloadSlide("PREV");
}	

function rewindVideoRequest() {
	var tmpTime = slideArray[countIndex(currentSlideIndex)].slideTime;
	console.log("Now rewinding video to time "+tmpTime);
	thcSeekTime(tmpTime);
	synchronizedSlides = true;
}	

function syncSlidesRequest(){
	synchronizeSlides();
}

function bigSlideRequest(){
		//TODO
}


function synchronizeSlides(){
	console.log("Synchronizing slides");
	//Get current time
	synchronizedSlides = false;
	var searchedTime;
	searchedTime = thcGetCurrTime();
	
	var searchedIndex = 0;
	
	var i = 0;
	while(!synchronizedSlides && i< slideArray.length){
		i++;
		//When i haven't found slide with higher time and
		//I am at the end of the array, it must be last one
		if(i >= slideArray.length){
			synchronizedSlides = true;
			searchedIndex = i-1;
		} else if(searchedTime <= slideArray[i].slideTime){					
			if(i != 0) searchedIndex = i-1;
			else searchedIndex = i;
			synchronizedSlides = true;
		}				
	}
	console.log("Slide synchronization found slide: " + searchedIndex);
	currentSlideIndex = searchedIndex;
	reloadSlide();		
	return true;	
}

function decideSlideQuality(width){
	var tmpMode;
	if(width > 800) tmpMode = "original";
	else if(width > 450) tmpMode = "big";
	else if(width > 150) tmpMode = "medium";
	else tmpMode = "small";
	
	if(tmpMode != slideQuality){
		slideQuality = tmpMode;
		reloadSlide("RELOAD");
		console.log("Changed slide quality to   :    "+slideQuality);
	}
}

