package slideslive.controller
{
	import flash.events.Event;
	import flash.events.TimerEvent;
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
		
		private var presentationTimer:Timer = new Timer(100);
		
		private var slideLoadersArray:Array; //Array of loaders id, for slides caching
		private var currentSlideIndex:int = 0;
		private var slidesMode:String = SlideQuality.MEDIUM;
		//Useful variables, for code readibility
		private var totalSlides:int;
		private var presentationID:int;
		
		
		public function PresentationController(playerValues:PlayerValues, playerGUI:PlayerGUI, error:ErrorController)
		{
			this.playerValues = playerValues;
			this.playerGUI = playerGUI;
			
		}
		
		public function loadPresentation():Boolean {
			if(!igniteVideo()) return PlayerOutput.printError("VIDEO Ignition failed");
			else return true;
		}
		
		private function videoIgnited(e:GeneralEvents):Boolean{
			PlayerOutput.printLog("VIDEO STREAM READY");
			playerGUI.recalculateGUI();
			if(!initListeners()) return PlayerOutput.printError("Listeners were not added");
			else if(!runMainTimer()) return PlayerOutput.printError("Main presentation timer failed");
			else if(!initSlides()) return PlayerOutput.printError("Slides addition failed");
			else return true;
		}		
		
		private function runMainTimer():Boolean
		{
			presentationTimer.addEventListener(TimerEvent.TIMER, runTimerTasks);
			presentationTimer.start();
			return true;
		}
		
		//Each timer tick do following updates
		private function runTimerTasks(e:TimerEvent){
			playerGUI.updateTime(videoModule.thcGetCurrTime(), videoModule.thcGetTotalTime());
			moveSlideDot();
		}		
		
		private function moveSlideDot():void {
			var tmpResult:Number = playerValues.getSlidesRecords()[currentSlideIndex].getSlideTime();
			tmpResult = tmpResult/videoModule.thcGetTotalTime()
			if(isNaN(tmpResult)) tmpResult = 0;
			playerGUI.moveSlideDot(tmpResult);			
		}
		
		private function initListeners():Boolean
		{
			playerGUI.getBigControl().addEventListener(ControlsEvents.PLAYBTN, playHandler);
			playerGUI.getVideoControls().addEventListener(ControlsEvents.PLAYBTN, playHandler);
			playerGUI.getVideoControls().addEventListener(ControlsEvents.FULLSCREEN, fullScreenHandler);
			playerGUI.getVideoControls().addEventListener(ControlsEvents.VIDEOSEEK, videoSeekHandler);
			playerGUI.getSlideControls().addEventListener(ControlsEvents.NEXTSLIDE, nextSlideHandler);
			playerGUI.getSlideControls().addEventListener(ControlsEvents.PREVSLIDE, prevSlideHandler);
			playerGUI.getSlideControls().addEventListener(ControlsEvents.JUMP, jumpToTimeHandler);
			playerGUI.addEventListener(GeneralEvents.SLIDEQUALITY, decideSlideQuality);
			return true;			
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
				PlayerOutput.printLog("changed quality to -------------------- "+slidesMode);
			}
		}
		
		private function nextSlideHandler(e:ControlsEvents):void {
			reloadSlide("NEXT");
		}
		
		private function prevSlideHandler(e:ControlsEvents):void {
			reloadSlide("PREV");
		}	
		
		private function jumpToTimeHandler(e:ControlsEvents):void {
			var tmpTime:int = playerValues.getSlidesRecords()[currentSlideIndex].getSlideTime();
			PlayerOutput.printLog("Now rewinding video to time "+tmpTime);
			videoModule.thcSeekTime(tmpTime);
			
			//Signalize that player will be in state of playing
			isPlaying = false;
			playHandler(new ControlsEvents(ControlsEvents.PLAYBTN));
		}			
		
		
		
		private function playHandler(e:ControlsEvents):void{
			PlayerOutput.printLog("PLAY FIRED");
			if(isPlaying){
				playerGUI.showPauseState();
				videoModule.thcPauseVideo();
				isPlaying = false;
			} else {
				playerGUI.showPlayState();
				videoModule.thcPlayVideo();
				isPlaying = true;
			}
			
		}
		
		private function fullScreenHandler(e:ControlsEvents):void{
			PlayerOutput.printLog("FULL FIRED");
		}
		
		private function videoSeekHandler(e:ControlsEvents):void{
			PlayerOutput.printLog("Video seek");
			videoModule.thcSeekTime(e.numberData * videoModule.thcGetTotalTime());
			
			//Signalize that player will be in state of playing
			isPlaying = false;
			playHandler(new ControlsEvents(ControlsEvents.PLAYBTN));			
		}			
		
		private function igniteVideo():Boolean {
			//this.videoContainer = videoContainer;
			//Choose video module according parameter
			if(playerValues.getPresentationModule() == "YOUTUBE" || 
				playerValues.getPresentationModule() == "audio" || 
				playerValues.getPresentationModule() == "AUDIO"){
				PlayerOutput.printLog("Attempting to init YouTube module");
				//TO DO - investigate bug why video width and height do not fit by few pixels
				videoModule = new ModuleYoutube(error, playerValues.getPresentationParameter(), playerGUI.getVideoHeight()+2, playerGUI.getVideoWidth()+3);
				//TO DO - here it might fail if event would be dispatched before this row is executed
				videoModule.addEventListener(GeneralEvents.YT_MODULE_READY, videoIgnited);
			} else {
				PlayerOutput.printError("Incorrect or no module selected");
				return false;
			}	
			playerGUI.addVideoStream(videoModule.thcGetStreamClip());
			
			return true;			
			
		}
		
		private function initSlides():Boolean {
			totalSlides = playerValues.getSlidesRecords().length;
			presentationID = playerValues.getPresentationID();
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
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex-2),slideLoadersArray[0]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex-1),slideLoadersArray[1]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex),slideLoadersArray[2]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex+1),slideLoadersArray[3]);
			playerGUI.loadSlideInContainer(getPathForIndex(currentSlideIndex+2),slideLoadersArray[4]);
		}	
		
		private function reloadSlide(eventSource:String="RELOAD"):void {
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
			PlayerOutput.printLog("Slide reload - index "+currentSlideIndex);			
			//changeSlideNumbers();
			playerGUI.displaySlideLoader(slideLoadersArray[2]);
		}
		
		private function checkCurrentIndex(){
			if(currentSlideIndex > totalSlides) currentSlideIndex = 0;
			else if(currentSlideIndex < 0) currentSlideIndex = totalSlides-1;
		}
		
		private function getPathForIndex(indexToLook:int):String {
			var slideName:String = playerValues.getSlidesRecords()[countIndex(indexToLook)].getSlideName();
			var path:String;
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
		
	}
}