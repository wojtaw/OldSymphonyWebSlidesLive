package slideslive.gui
{
	import com.greensock.*;
	
	import flash.display.*;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.Security;
	import flash.system.SecurityDomain;
	import flash.text.TextField;
	import flash.utils.Timer;
	
	import slideslive.error.ErrorHandler;
	import slideslive.event.ControlsEvents;
	import slideslive.event.GeneralEvents;
	import slideslive.gui.buttons.BigPlayButton;
	import slideslive.gui.slider.Slider;
	import slideslive.util.PlayerOutput;
	import slideslive.values.PlayerValues;	

	public class PlayerGUI extends MovieClip
	{
		//var AudioOnly, VideoOnly, Both
		//var Fullscreen or no
		//var Embed or not
		
		//Numbers for zooming etc...
		private var videoRatio:Number = (16/9);
		private var slideRatio:Number = (4/3);
		private var universalSpace:int = 15; //some kind of margin
		
		private var playerValues:PlayerValues;
		
		private var controlsHideTimer:Timer;
		
		private var slidesContainer:SlidesContainer;
		private var slidesControls:SlidesControls;
		private var videoContainer:VideoContainer;
		private var videoControls:VideoControls;
		private var bigPlayButton:BigPlayButton;
		private var videoSlideWrapper:MovieClip = new MovieClip();
		private var slider:Slider;
		private var embedLogo:EmbedLogo;
		
		private var error:ErrorHandler;		
		
		public function PlayerGUI(playerValues:PlayerValues, error:ErrorHandler)
		{
			this.error = error;
			this.playerValues = playerValues;
		}
		
		public function initGUI():void{
			PlayerOutput.printLog("GUI is initializing");
			
			addChild(videoSlideWrapper);
			initVideoContainer();
			initSlideContainer();
			//Controls at the top
			initBigPlayButton();
			initVideoControls();
			initSlideControls();
			//Embed components
			if(playerValues.isEmbedded()) displayEmbedComponents();
			//Above all slider
			initSlider();
			videoSlideWrapper.y = slider.height;
			
			//Check if there are special cases like video or audio only
			if(playerValues.isSlideAvailable() && !playerValues.isVideoAvailable()) showSlidesOnly();
			if(!playerValues.isSlideAvailable() && playerValues.isVideoAvailable()) showVideoOnly();
			
			//Timers and listeners
			initTimerToHideControls();
			initListeners();
			
			//Very last thing - fire evnet, that GUI is ready
			dispatchEvent(new GeneralEvents("GUI is ready"));
		}
		
		private function initTimerToHideControls():void {
			controlsHideTimer = new Timer(3000,1);
			controlsHideTimer.addEventListener(TimerEvent.TIMER, hideControls);
			controlsHideTimer.start();
		}
		
		private function initListeners():void {
			stage.addEventListener(MouseEvent.MOUSE_MOVE, interruptHideControlsTimer);
			this.addEventListener(MouseEvent.MOUSE_MOVE, showControls);
			this.addEventListener(MouseEvent.ROLL_OUT, hideControls);
			this.addEventListener(MouseEvent.ROLL_OVER, showControls);
			slidesControls.addEventListener(MouseEvent.MOUSE_OVER, interruptHideControlsTimer);
			videoControls.addEventListener(MouseEvent.MOUSE_OVER, interruptHideControlsTimer);
			
			if(playerValues.isEmbedded()){
				embedLogo.addEventListener(MouseEvent.MOUSE_OVER, embedOver);
				embedLogo.addEventListener(MouseEvent.MOUSE_OUT, embedOut);			
			}
		}
		
		private function interruptHideControlsTimer(e:*){
			controlsHideTimer.reset();
			controlsHideTimer.start();
		}
		
		private function hideControls(e:*){
			var isMouseOverControls:Boolean = videoControls.hitTestPoint(mouseX,mouseY,true) || slidesControls.hitTestPoint(mouseX,mouseY,true);
			if(!isMouseOverControls){				
				TweenLite.to(videoControls, playerValues.controlsAnimationLenght, {alpha:0});
				TweenLite.to(slidesControls, playerValues.controlsAnimationLenght, {alpha:0});
			}
		}
		
		private function showControls(e:*){
			TweenLite.to(videoControls, playerValues.controlsAnimationLenght, {alpha:1});
			TweenLite.to(slidesControls, playerValues.controlsAnimationLenght, {alpha:1});			
		}
		
		private function initSlider():void {
			slider = new Slider(0,playerValues.playerStageWidth,playerValues.playerStageWidth/1.7777);
			addChild(slider);
			slider.addEventListener(GeneralEvents.SLIDERMOVE, sliderMoved);
			slider.initSlider();
			slider.x = playerValues.playerStageWidth - slider.width - 15;
			slider.y = slider.height/2;			
		}
		
		private function sliderMoved(e:GeneralEvents){
			recalculateSlidesAndVideoContainers(e.data);
			recalculateControlsPosition();
			recalculateBigPlayButton();
			recalculateStageDimensions();
			dispatchEvent(new GeneralEvents(GeneralEvents.SLIDEQUALITY, slidesContainer.width));
			if(playerValues.isEmbedded()) recalculateEmbedParts();
		}
		
		private function recalculateStageDimensions():void
		{
			playerValues.playerStageWidth = 960;
			playerValues.playerStageHeight = videoSlideWrapper.y + videoSlideWrapper.height;
			var isAvailable:Boolean = ExternalInterface.available;
			PlayerOutput.printLog("External interface available: "+isAvailable);
			ExternalInterface.call("resizePlayerContainer", playerValues.playerStageHeight);	
		}		
		
		private function recalculateSlidesAndVideoContainers(size:Number){
			videoContainer.width = size;
			videoContainer.height = (videoContainer.width / videoRatio);
			if(videoContainer.width < 10){
				slidesContainer.width = playerValues.playerStageWidth;
				slidesContainer.height = (slidesContainer.width / slideRatio);
				slidesContainer.x = 0;								
			}else{				
				slidesContainer.width = (playerValues.playerStageWidth-videoContainer.width)- universalSpace;
				slidesContainer.height = (slidesContainer.width / slideRatio);
				slidesContainer.x = videoContainer.width + universalSpace;			
			}
		}
		
		private function recalculateControlsPosition(){
			var controlsHeight:int = 45; //Because of tooltop, height property is inacurate
			if(videoContainer.width < 350){
				videoControls.adaptToWidth(slidesContainer.width - (2*universalSpace));
				videoControls.y = slidesContainer.height - controlsHeight - universalSpace;
				videoControls.x = slidesContainer.x + universalSpace;	
				
				slidesControls.x = ((slidesContainer.width - slidesControls.width) / 2) + slidesContainer.x;
				slidesControls.y = slidesContainer.height - controlsHeight - universalSpace - controlsHeight - universalSpace;				
			} else {			
				
				videoControls.adaptToWidth(videoContainer.width - (2*universalSpace));
				videoControls.y = videoContainer.height - controlsHeight - universalSpace;
				videoControls.x = universalSpace;
				slidesControls.x = ((slidesContainer.width - slidesControls.width) / 2) + slidesContainer.x;
				slidesControls.y = slidesContainer.height - controlsHeight - universalSpace;
			}
			
			
			//Special case for slide control
			if(slidesContainer.width < (slidesControls.width + (2*universalSpace))) slidesControls.visible = false;
			else if(!slidesControls.visible) slidesControls.visible = true;			
		}
		
		private function recalculateEmbedParts(){
			if(videoContainer.width >= playerValues.playerStageWidth - 150){
				embedLogo.y = videoControls.y - 65;
				embedLogo.x = videoContainer.width - embedLogo.width;
			} else {
				embedLogo.x = playerValues.playerStageWidth - embedLogo.width;
				embedLogo.y = slidesControls.y; 
			}
							
		}
		
		private function recalculateBigPlayButton(){
			
			if(videoContainer.width < 350){
				bigPlayButton.resize(slidesContainer.width,slidesContainer.height);			
				bigPlayButton.x = slidesContainer.x;
			} else {
				bigPlayButton.resize(videoContainer.width,videoContainer.height);
				bigPlayButton.x = videoContainer.x;
			}
		}
		
		
		private function initVideoContainer():void {			
			videoContainer = new VideoContainer();
			videoSlideWrapper.addChild(videoContainer);
		}
		
		private function initBigPlayButton():void {
			bigPlayButton = new BigPlayButton();
			
			videoSlideWrapper.addChild(bigPlayButton);
		}
		
		private function initVideoControls():void {
			videoControls = new VideoControls(playerValues);
			videoSlideWrapper.addChild(videoControls);
		}
		
		private function initSlideContainer():void {
			slidesContainer = new SlidesContainer(error);
			slidesContainer.x = videoContainer.width + universalSpace;
			videoSlideWrapper.addChild(slidesContainer);
			
		}

		private function initSlideControls():void {
			slidesControls = new SlidesControls(playerValues);
			slidesControls.x = 500;
			slidesControls.y = 100;
			videoSlideWrapper.addChild(slidesControls);			
		}
		
		private function displayEmbedComponents():void {
			embedLogo = new EmbedLogo();
			embedLogo.x = playerValues.playerStageWidth - embedLogo.width;
			embedLogo.y = slidesControls.y + 56;
			embedLogo.symbolEmbed.visible = false;
			videoSlideWrapper.addChild(embedLogo);
		}
		
		private function showVideoOnly():void {
			slider.visible = false;
			videoSlideWrapper.y = 0;
			//Fire fictional event to simulate slides only slider position
			sliderMoved(new GeneralEvents("SliderMoved",playerValues.playerStageWidth));			
		}
		
		private function showSlidesOnly():void{
			slider.visible = false;
			videoSlideWrapper.y = 0;
			//Fire fictional event to simulate slides only slider position
			sliderMoved(new GeneralEvents("SliderMoved",0));
		}
		
		private function embedOver(e:MouseEvent){
			embedLogo.symbolEmbed.visible = true;
			embedLogo.embedLogoBG.alpha = 1;
		}
		private function embedOut(e:MouseEvent){
			embedLogo.symbolEmbed.visible = false;
			embedLogo.embedLogoBG.alpha = 0.85;
		}	
		
		//Some key functions served for other classes
		public function addVideoStream(videoStream:MovieClip){
			videoContainer.addChild(videoStream);
		}
		
		public function showPauseState():void {
			videoControls.showPauseState();
			bigPlayButton.alpha = 1;
		}
		
		public function showPlayState():void {
			videoControls.showPlayState();
			bigPlayButton.alpha = 0;
		}		
		
		
		public function updateTime(timeInSeconds:Number, timeTotalInSeconds:Number):void
		{
			if(timeTotalInSeconds == 0) videoControls.updateBarPlayed(0);
			else videoControls.updateBarPlayed(timeInSeconds / timeTotalInSeconds);
			videoControls.updateTime(timeInSeconds,timeTotalInSeconds);
		}
		
		public function loadSlideInContainer(pathToFile:String, loaderNumber:int){
			slidesContainer.loadSlide(pathToFile, loaderNumber);
		}
		
		public function displaySlideLoader(loaderID:int):void {
			slidesContainer.displayLoader(loaderID);	
		}	
		
		public function moveSlideDot(position:Number):void
		{
			videoControls.moveSlideDot(position);
		}		
		
		//Getters
		public function getVideoHeight():Number{
			return videoContainer.height;
		}
		
		public function getVideoWidth():Number {
			return videoContainer.width;
		}
		
		public function getSlideControls():SlidesControls {
			return slidesControls;
		}
		
		public function getVideoControls():VideoControls {
			return videoControls;
		}
		
		public function getBigControl():BigPlayButton {
			return bigPlayButton;
		}
	}
}