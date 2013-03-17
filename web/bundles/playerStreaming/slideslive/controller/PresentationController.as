package slideslive.controller
{	
	import flash.display.Stage;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.KeyboardEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.geom.Rectangle;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.net.navigateToURL;
	import flash.ui.Keyboard;
	import flash.utils.Timer;
	
	import slideslive.event.ControlsEvents;
	import slideslive.event.GeneralEvents;
	import slideslive.gui.PlayerGUI;
	import slideslive.mediamodule.*;
	import slideslive.mediamodule.Module;
	import slideslive.util.PlayerOutput;
	import slideslive.util.SlideQuality;
	import slideslive.values.PlayerValues;

	public class PresentationController
	{
		private var playerValues:PlayerValues;
		
		private var videoModule:Module;	
		private var playerGUI:PlayerGUI
		private var error:ErrorController;
		private var isPlaying:Boolean = false;
		private var isFullscreen:Boolean = false;
		private var synchronizedSlides:Boolean = true;
		private var previousSyncState:Boolean = false;
		private var isSlideRequestComplete:Boolean = true;
		private var streamUnlocked:Boolean = false;
		private var firstIndexFound:Boolean = false;
		private var firstPlay = true;
		
		private var presentationTimer:Timer = new Timer(100);
		private var streamTimer:Timer = new Timer(1000);
		private var buyCountdown:Timer;
		
		private var slideLoadersArray:Array; //Array of loaders id, for slides caching
		private var currentSlideIndex:int = 0;
		private var slidesMode:String = SlideQuality.MEDIUM;
		//Useful variables, for code readibility
		private var totalSlides:int;
		private var presentationID:int;
		
		private var stage:Stage;
		
		
		public function PresentationController(playerValues:PlayerValues, playerGUI:PlayerGUI, error:ErrorController,stage:Stage)
		{
			this.playerValues = playerValues;
			this.playerGUI = playerGUI;
			this.stage = stage;
			
		}
		
		public function loadPresentation():Boolean {
			if(!igniteVideo()) return PlayerOutput.printError("VIDEO Ignition failed");
			else return true;
		}
		
		
		
		private function videoIgnited(e:GeneralEvents):Boolean{
			PlayerOutput.printLog("VIDEO STREAM READY");
			playerGUI.hideVideoLoader();
			if(!initListeners()) return PlayerOutput.printError("Listeners were not added");
			else if(!runMainTimer()) return PlayerOutput.printError("Main presentation timer failed");
			else if(!initSlides()) return PlayerOutput.printError("Slides addition failed");
			else {
				playerGUI.recalculateGUI();
				return true;
			}
		}		
		
		private function runMainTimer():Boolean
		{
			presentationTimer.addEventListener(TimerEvent.TIMER, runTimerTasks);
			presentationTimer.start();
			
			streamTimer.addEventListener(TimerEvent.TIMER, runStreamTasks);
			streamTimer.start();			
			
			return true;
		}
		
		//Each timer tick do following updates
		private function runTimerTasks(e:TimerEvent){
			playerGUI.updateTime(videoModule.thcGetCurrTime(), videoModule.thcGetTotalTime());
			GUISyncState();
		}	
		
		private function GUISyncState():void
		{
			if(previousSyncState != synchronizedSlides){
				if(synchronizedSlides) playerGUI.changeSyncState(true);
				else playerGUI.changeSyncState(false);
				previousSyncState = synchronizedSlides;
			}
		}
		
		private function runStreamTasks(e:TimerEvent):void {
			if(streamUnlocked) checkSlideToUpdate()	
			else {
				checkStreamLock();
				PlayerOutput.printLog("STREAM IS LOCKED FOR NOW");
			}
		}
		
		private function checkSlideToUpdate():Boolean
		{
			if(!isSlideRequestComplete) return false;
			
			isSlideRequestComplete = false;
			var request:URLRequest = new URLRequest();
			
			request.url = buildSlidePath(currentSlideIndex);
			request.method = URLRequestMethod.GET;
			
			var loader:URLLoader = new URLLoader();
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, slideRequestComplete);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			loader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);	
			
			try {
				loader.load(request);
			} catch (error:Error) {
				trace("Unable to load URL");
			}		
			return true;
		}		
		
		
		private function checkStreamLock():void {
			var request:URLRequest = new URLRequest();
			
			request.url = playerValues.getPathToStreamingSession()+playerValues.getSessionID()+"/stream.lock?cachekill="+Math.round(Math.random()*1000);
			request.method = URLRequestMethod.GET;
			
			var loader:URLLoader = new URLLoader();
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, lockRequestComplete);	
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			loader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);	
			
			try {
				loader.load(request);
			} catch (error:Error) {
				trace("Unable to load URL");
			}		
		}
		
		private function lockRequestComplete(e:HTTPStatusEvent):void {
			if(e.status == 200){
				streamUnlocked = false;
				playerGUI.showStreamLock();
			} else {
				streamUnlocked = true;
				streamTimer.delay = 10;
				playerGUI.hideStreamLock();
			}
		}			
		 
		
		private function slideRequestComplete(e:HTTPStatusEvent):void {
			if(e.status == 200){
				currentSlideIndex++;
				reloadStreamSlide();
			}else{
				if(!firstIndexFound){
					firstIndexFound = true;
					streamTimer.delay = 1000;
				}				
			}
			isSlideRequestComplete = true;
			
		}
		
		private function reloadStreamSlide():void {
			PlayerOutput.printLog("New slide in stream detected, load it!"+buildSlidePath(currentSlideIndex-1));
			
			playerGUI.loadSlideInContainer(buildSlidePath(currentSlideIndex-1),slideLoadersArray[2]);
			playerGUI.displaySlideLoader(slideLoadersArray[2]);
		}	
		
		
		private function buildSlidePath(slideIndex:int):String {
			var tmpSlideName:String;
			if(slideIndex < 9) tmpSlideName = "0-000"+(slideIndex+1)+".png";
			else if(slideIndex < 99) tmpSlideName = "0-00"+(slideIndex+1)+".png";
			else if(slideIndex < 999) tmpSlideName = "0-0"+(slideIndex+1)+".png";
			else tmpSlideName = "0-"+(slideIndex+1)+".png";
			
			var tmpRequestPath:String = playerValues.getPathToStreamingSession()+playerValues.getSessionID()+"/"+tmpSlideName+"?cachekill="+Math.round(Math.random()*1000);
			return tmpRequestPath;
		}
		
		private function moveSlideDot():void {
			var tmpResult:Number = playerValues.getSlidesRecords()[countIndex(currentSlideIndex)].getSlideTime();
			tmpResult = tmpResult/videoModule.thcGetTotalTime()
			if(isNaN(tmpResult)) tmpResult = 0;
			playerGUI.moveSlideDot(tmpResult);	
		}
		
		private function initListeners():Boolean
		{
			playerGUI.addEventListener(ControlsEvents.PLAYBTN, playHandler);
			playerGUI.addEventListener(ControlsEvents.OPENBIGSLIDE, openBigSldieHandler);
			playerGUI.addEventListener(ControlsEvents.FULLSCREEN, fullScreenHandler);
			playerGUI.addEventListener(ControlsEvents.VIDEOSEEK, videoSeekHandler);
			playerGUI.addEventListener(ControlsEvents.NEXTSLIDE, nextSlideHandler);
			playerGUI.addEventListener(ControlsEvents.PREVSLIDE, prevSlideHandler);
			playerGUI.addEventListener(ControlsEvents.JUMP, jumpToTimeHandler);
			playerGUI.addEventListener(ControlsEvents.SYNCHRONIZESLIDES, synchronizeEventHandler);			
			playerGUI.addEventListener(GeneralEvents.SLIDEQUALITY, decideSlideQuality);
			playerGUI.addEventListener(ControlsEvents.VOLUME,changeVolume);
			playerGUI.addEventListener(ControlsEvents.SLIDESLIVELOGO,openPresentationOnSlidesLive);
			playerGUI.addEventListener(GeneralEvents.BUYDONE, buyFinished);
			playerGUI.addEventListener(GeneralEvents.NOTETAKING, disableKeyboardListeners);
			playerGUI.addEventListener(GeneralEvents.ADDNOTE, sendNoteToServer);
			
			//Keyboard events
			enableKeyboardListeners();
			
			return true;			
		}
		
		private function enableKeyboardListeners(e:Event=null){
			stage.addEventListener(KeyboardEvent.KEY_DOWN, keyboardInteraction);
			stage.addEventListener(Event.FULLSCREEN, fullScreenEventFired);
		}
		
		private function disableKeyboardListeners(e:Event=null){
			stage.removeEventListener(KeyboardEvent.KEY_DOWN, keyboardInteraction);
			stage.removeEventListener(Event.FULLSCREEN, fullScreenEventFired);			
		}
		
		private function openBigSldieHandler(e:ControlsEvents):void {
			var path:String = playerValues.getPathToImages()+"original/"+playerValues.getSessionID()+"/"+playerValues.getSlidesRecords()[currentSlideIndex].getSlideName();
			var targetURL:URLRequest = new URLRequest(path);
			navigateToURL(targetURL, "_blank");
		}
		
		private function keyboardInteraction(e:KeyboardEvent):void{
			if (e.keyCode == 39){
				nextSlideHandler(new ControlsEvents(ControlsEvents.NEXTSLIDE));
			} else if(e.keyCode == 37){
				prevSlideHandler(new ControlsEvents(ControlsEvents.PREVSLIDE));
			} else if(e.keyCode == 32){
				playHandler(new ControlsEvents(ControlsEvents.PLAYBTN));
			} else if(e.keyCode == Keyboard.ENTER || e.keyCode == 83){
				jumpToTimeHandler(new ControlsEvents(ControlsEvents.JUMP));
			} else if(e.keyCode == Keyboard.SHIFT || e.keyCode == 86){
				synchronizeEventHandler(new ControlsEvents(ControlsEvents.SYNCHRONIZESLIDES));
			}						
		}		
		
		private function openPresentationOnSlidesLive(e:ControlsEvents):void{
			var path:String = playerValues.getPathToWebsiteWatch()+playerValues.getSessionID();
			var targetURL:URLRequest = new URLRequest(path);
			navigateToURL(targetURL, "_blank");
		}
		
		private function changeVolume(e:ControlsEvents):void {
			videoModule.thcSetVolume(e.numberData);
		}		
		
		private function synchronizeEventHandler(e:ControlsEvents):void{
			synchronizeSlides();
		}		
		
		protected function decideSlideQuality(e:GeneralEvents):void
		{
			var tmpMode:String;
			if(e.data > 800) tmpMode = SlideQuality.ORIGINAL;
			else if(e.data > 450) tmpMode = SlideQuality.BIG;
			else if(e.data > 150) tmpMode = SlideQuality.MEDIUM;
			else tmpMode = SlideQuality.SMALL;
			
			if(tmpMode != slidesMode){
				slidesMode = tmpMode;
				reloadSlide("RELOAD");
				PlayerOutput.printLog("Changed slide quality to   :    "+slidesMode);
			}
		}
		
		private function nextSlideHandler(e:ControlsEvents):void {
			synchronizedSlides = false;
			reloadSlide("NEXT");
		}
		
		private function prevSlideHandler(e:ControlsEvents):void {
			synchronizedSlides = false;
			reloadSlide("PREV");
		}	
		
		private function jumpToTimeHandler(e:ControlsEvents):void {
			var tmpTime:int = playerValues.getSlidesRecords()[countIndex(currentSlideIndex)].getSlideTime();
			PlayerOutput.printLog("Now rewinding video to time "+tmpTime);
			videoModule.thcSeekTime(tmpTime);
			synchronizedSlides = true;
			
			//Signalize that player will be in state of playing
			isPlaying = false;
			playHandler(new ControlsEvents(ControlsEvents.PLAYBTN));
		}			
		
		
		
		private function playHandler(e:ControlsEvents):void{
			PlayerOutput.printLog("PLAY FIRED");
			//If it's firsttime play, rund timer for buy dialog
			if(buyCountdown == null) runBuyContdown();
			if(firstPlay){
				firstPlay = false;
				checkStartSlide();
			}
			if(isPlaying){
				playerGUI.showPauseState();
				videoModule.thcPauseVideo();
				isPlaying = false;
			} else {
				playerGUI.showPlayState();
				videoModule.thcPlayVideo();
				isPlaying = true;
				playerGUI.recalculateGUI();
			}
			
		}
		
		private function checkStartSlide():void
		{
			if(playerValues.getStartSlide() != 1){
				currentSlideIndex = playerValues.getStartSlide()+1;
				checkCurrentIndex();
				reloadSlide("RELOAD");
				jumpToTimeHandler(null);
				playHandler(null);
			}
		}
		
		private function runBuyContdown():void {
			
			buyCountdown = new Timer(90000,1);
			if(playerValues.isPaid()) {
				buyCountdown.addEventListener(TimerEvent.TIMER_COMPLETE, showBuyDialog);
				buyCountdown.start();
			} else {
				//No listening, just do the timer and do not show anything
			}
		}
		
		private function showBuyDialog(e:TimerEvent):void {
			disableKeyboardListeners();
			playerGUI.showPauseState();
			videoModule.thcPauseVideo();
			isPlaying = false;
			playerGUI.disablePlayer();
			PlayerOutput.printLog("Showing buy dialog");
			playerGUI.showBuyDialog(presentationID);
		}
		
		private function buyFinished(e:GeneralEvents){
			enableKeyboardListeners();
			playerGUI.enablePlayer();
			playerGUI.showPlayState();
			videoModule.thcPlayVideo();
			isPlaying = true;
			playerGUI.recalculateGUI();			
		}
		
		private function fullScreenHandler(e:Event):void{
			PlayerOutput.printLog("FULL FIRED");
			if(isFullscreen){				
				stage.displayState = StageDisplayState.NORMAL;		
			}else{
				stage.displayState = StageDisplayState.FULL_SCREEN;
			}
		}
		
		private function fullScreenEventFired(e:Event){
			PlayerOutput.printLog("FULL ENTERED");	
			if(isFullscreen){
				isFullscreen = false;
				stage.scaleMode = StageScaleMode.NO_SCALE;					
				stage.invalidate();	
				playerGUI.showNormalState();
				if(!playerValues.isVideoAvailable()){
					playerGUI.scalePlayer();
					playerGUI.x = 0;
				}
			}else{
				isFullscreen = true;
				if(!playerValues.isVideoAvailable()) {
					stage.scaleMode = StageScaleMode.NO_SCALE;
					var calculatedWidth = ((4*stage.stageHeight)/3)
					playerGUI.scalePlayer(calculatedWidth);
					playerGUI.x = (stage.stageWidth - calculatedWidth) / 2;
				} else {
					stage.scaleMode = StageScaleMode.SHOW_ALL;				
				}
				stage.invalidate();
				playerGUI.showFullscreenState();
			}			
		}
		
		private function videoSeekHandler(e:ControlsEvents):void{
			PlayerOutput.printLog("Video seek");
			videoModule.thcSeekTime(e.numberData * videoModule.thcGetTotalTime());
			synchronizeSlides();
			
			//Signalize that player will be in state of playing
			isPlaying = false;
			playHandler(new ControlsEvents(ControlsEvents.PLAYBTN));			
		}			
		
		private function igniteVideo():Boolean {
			//this.videoContainer = videoContainer;
			//Choose video module according parameter
			if(playerValues.getLivestreamModule() == "YOUTUBE" || 
				playerValues.getLivestreamModule() == "audio" || 
				playerValues.getLivestreamModule() == "AUDIO"){
				PlayerOutput.printLog("Attempting to init YouTube module");

				videoModule = new ModuleYoutube(error, playerValues.getLivestreamParameter(), playerGUI.getVideoHeight()+2, playerGUI.getVideoWidth()+3);
				//TO DO - here it might fail if event would be dispatched before this row is executed
				videoModule.addEventListener(GeneralEvents.VIDEO_MODULE_READY, videoIgnited);
			} else if(playerValues.getLivestreamModule() == "VIMEO"){
				videoModule = new ModuleVimeo(error, playerValues.getLivestreamParameter(), playerGUI.getVideoHeight()+2, playerGUI.getVideoWidth()+3);
				//TO DO - here it might fail if event would be dispatched before this row is executed
				videoModule.addEventListener(GeneralEvents.VIDEO_MODULE_READY, videoIgnited);
			} else {
				PlayerOutput.printError("Incorrect or no module selected");
				return false;
			}	
			playerGUI.addVideoStream(videoModule.thcGetStreamClip());
			
			return true;			
			
		}
		
		private function initSlides():Boolean {
			totalSlides = playerValues.getSlidesRecords().length;
			presentationID = playerValues.getSessionID();
			initSlideArray();			
			return true;
		}
		
		private function initSlideArray():void {
			slideLoadersArray = new Array();
			slideLoadersArray.push(1);
			slideLoadersArray.push(2);
			slideLoadersArray.push(3);
			slideLoadersArray.push(4);
			slideLoadersArray.push(5);
			reloadSlide("RELOAD");
		}
		
		//Take loader that is going to be out off array,
		//load it with new image and put it to the end
		private function cacheNextSlideIntoArray():void {
			var loaderID:int = slideLoadersArray.shift();	
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex+2),loaderID);
			slideLoadersArray.push(loaderID);
		}
		
		//Take loader that is going to be out off array,
		//load it with new image and put it to the begining		
		private function cachePreviousSlideIntoArray():void {
			var loaderID:int = slideLoadersArray.pop();	
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex-2),loaderID);
			slideLoadersArray.unshift(loaderID);		
		}		
		
		//When we are at the point, nothing is cached
		//And we have to re-init the whole array
		private function reloadArray():void {	
			/*
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex-2),slideLoadersArray[0]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex-1),slideLoadersArray[1]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex),slideLoadersArray[2]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex+1),slideLoadersArray[3]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex+2),slideLoadersArray[4]);
			*/
		}	
		
		private function reloadSlide(eventSource:String="RELOAD"):void {
			if(!playerValues.isSlideAvailable()) eventSource="NONE";
			if(eventSource == "RELOAD"){
				reloadArray();				
			}else if(eventSource == "NEXT"){
				currentSlideIndex++;
				checkCurrentIndex();
				cacheNextSlideIntoArray();
			}else if(eventSource == "PREV"){
				currentSlideIndex--;
				checkCurrentIndex();
				cachePreviousSlideIntoArray();
			}else{
				return;
			}
			playerGUI.setSlideNumbers(currentSlideIndex, totalSlides);
			PlayerOutput.printLog("Slide reload - index "+currentSlideIndex);			
			//changeSlideNumbers();
			playerGUI.displaySlideLoader(slideLoadersArray[2]);
		}
		
		private function checkCurrentIndex(){
			if(currentSlideIndex >= totalSlides) currentSlideIndex = 0;
			else if(currentSlideIndex < 0) currentSlideIndex = totalSlides-1;
		}
		
		private function getPathForIndex(indexToLook:int):String {
			var slideName:String = playerValues.getSlidesRecords()[countIndex(indexToLook)].getSlideName();
			var path:String;
			//In case it is audioslideshow only
			if(!playerValues.isVideoAvailable()){
				slidesMode = SlideQuality.ORIGINAL;
				return playerValues.getPathToImages()+"original/"+presentationID+"/"+slideName;
			}
				
			if(slidesMode == SlideQuality.SMALL) path = playerValues.getPathToImages()+"small/"+presentationID+"/"+slideName;
			else if(slidesMode == SlideQuality.MEDIUM) path = playerValues.getPathToImages()+"medium/"+presentationID+"/"+slideName;
			else if(slidesMode == SlideQuality.BIG) path = playerValues.getPathToImages()+"big/"+presentationID+"/"+slideName;
			else if(slidesMode == SlideQuality.ORIGINAL) path = playerValues.getPathToImages()+"original/"+presentationID+"/"+slideName;			
			else PlayerOutput.printError("Incorrect slide size selected");
			return path;
		}	
		
		//If requested index is higher than limit or lower. 
		//this method will return equal value considering values looping
		private function countIndex(searchedIndex:int):int{
			var valueToReturn:int = 0;
			if(searchedIndex >= totalSlides) valueToReturn = searchedIndex - totalSlides;
			else if(searchedIndex < 0) valueToReturn = totalSlides + searchedIndex;
			else valueToReturn = searchedIndex;
			return valueToReturn;
		}	
		
		
		
		//Slides synchronization
		public function synchronizeSlides(timeInSeconds:int=-1):Boolean {
			//Get current time
			synchronizedSlides = false;
			var searchedTime:Number;
			if(timeInSeconds == -1){
				searchedTime = videoModule.thcGetCurrTime();
			} else {
				searchedTime = timeInSeconds;
			}
			
			var searchedIndex:int = 0;
			
			var i:int = 0;
			while(!synchronizedSlides && i< playerValues.getSlidesRecords().length){
				i++;
				//When i haven't found slide with higher time and
				//I am at the end of the array, it must be last one
				if(i >= playerValues.getSlidesRecords().length){
					synchronizedSlides = true;
					searchedIndex = i-1;
				} else if(searchedTime <= playerValues.getSlidesRecords()[i].getSlideTime()){					
					if(i != 0) searchedIndex = i-1;
					else searchedIndex = i;
					PlayerOutput.printLog("I found it " + i);
					synchronizedSlides = true;
				}				
			}
			PlayerOutput.printLog("Slide synchrnization found slide: " + searchedIndex);
			currentSlideIndex = searchedIndex;
			reloadSlide();		
			return true;
		}	
		
		private function sendNoteToServer(e:GeneralEvents){
			trace("note taken at"+Math.round(videoModule.thcGetCurrTime()));
			var requestVars:URLVariables = new URLVariables();
			requestVars.presentationID = playerValues.getSessionID();
			requestVars.timecode = Math.round(videoModule.thcGetCurrTime());
			requestVars.noteContent = e.data2;
			
			var request:URLRequest = new URLRequest();
			
			request.url = playerValues.playerAPIAddNote;
			request.method = URLRequestMethod.POST;
			request.data = requestVars;
			
			var loader:URLLoader = new URLLoader();
			loader.addEventListener(Event.COMPLETE, noteAddedHandler);
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, httpStatusHandler);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			loader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);			
			
			try {
				loader.load(request);
			} catch (error:Error) {
				trace("Unable to load URL");
			}	
		}
		
		private function noteAddedHandler(e:Event):void {
			ExternalInterface.call("refreshNotes");
		}
		
		private function httpStatusHandler (e:HTTPStatusEvent):void {
			//trace("httpStatusHandler:" + e.status);
		}
		
		private function securityErrorHandler (e:Event):void{
			//trace("\n\nsecurityErrorHandler:" + e);
		}
		private function ioErrorHandler(e:Event):void{
			//trace("ioErrorHandler: " + e);
		}		
		
	}
}