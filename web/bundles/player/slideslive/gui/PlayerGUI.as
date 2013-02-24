package slideslive.gui
{
	import com.google.analytics.AnalyticsTracker;
	import com.google.analytics.GATracker;
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
		private var videoPreloader:VideoContainerBG;	
		private var slidePreloader:PreLoaderAnimation;		
		private var videoSlideWrapper:MovieClip = new MovieClip();
		private var videoStream:PlayerClip;
		private var tmpFill:TmpWrap = new TmpWrap();
		private var buyDialog:BuyDialog;
		
		private var slider:Slider;
		private var embedLogo:EmbedLogo;
		
		private var videoContainerWidth:Number;
		private var videoContainerHeight:Number;
		
		private var error:ErrorHandler;		
		private var gaTracker:AnalyticsTracker;
		
		public function PlayerGUI(playerValues:PlayerValues, error:ErrorHandler)
		{
			this.error = error;
			this.playerValues = playerValues;
		}
		
		public function initGUI():void{
			PlayerOutput.printLog("GUI is initializing");
			
			initGoogleAnalytics();
			if(!playerValues.isDebugMode()) tmpFill.visible = false;
			addChild(tmpFill);
			addChild(videoSlideWrapper);
			initVideoBgLoader();
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

			if(!playerValues.isZoomingOn()){
				videoSlideWrapper.y = 0;
				slider.visible = false;
			} else {
				videoSlideWrapper.y = slider.height;				
			}
			
			tmpFill.y = videoSlideWrapper.y;
			
			//Check if there are special cases like video or audio only, or different ratio
			if(playerValues.isSlideAvailable() && !playerValues.isVideoAvailable()) showSlidesOnly();
			if(!playerValues.isSlideAvailable() && playerValues.isVideoAvailable()) showVideoOnly();
			if(playerValues.getVideoSlideRatio() != -1) changeVideoSlideDefaultRatio(playerValues.getVideoSlideRatio());
			
			//Timers and listeners
			initTimerToHideControls();
			initListeners();
			
			scalePlayer();
			//Very last thing - fire evnet, that GUI is ready
			dispatchEvent(new GeneralEvents("GUI is ready"));
		}
		
		private function initGoogleAnalytics():void {
			gaTracker = new GATracker(this, "UA-33478098-1", "AS3",false);
		}		
		
		public function getTracker():AnalyticsTracker {
			return gaTracker;		
		}
		
		//Tmp scaling function
		public function scalePlayer(scaleWidth:Number=-1):void {
			if(scaleWidth == -1) scaleWidth = playerValues.scaleToWidth;
			if(playerValues.scaleToWidth != -1){
				trace("SCALING PLAYER TO "+scaleWidth / 960);
				var tmpScale:Number = scaleWidth / 960;
				slider.scaleX = tmpScale;
				slider.scaleY = tmpScale;
				trace(slider.width);
				slider.x = scaleWidth - slider.width - 30; 
				videoSlideWrapper.scaleX = tmpScale;
				videoSlideWrapper.scaleY = tmpScale;
			}
		}
		
		private function initVideoBgLoader():void{
			videoPreloader = new VideoContainerBG();
			slidePreloader = new PreLoaderAnimation();
			videoSlideWrapper.addChild(videoPreloader);
			videoSlideWrapper.addChild(slidePreloader);
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
				embedLogo.addEventListener(MouseEvent.CLICK, embedClick);
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
			slider.x = slidesContainer.x;
			slider.y = slider.height/2;			
		}
		
		private function sliderMoved(e:GeneralEvents){
			recalculateSlidesAndVideoContainers(e.data);
			recalculateControlsPosition();
			centerLoaders();
			recalculateBigPlayButton();
			dispatchEvent(new GeneralEvents(GeneralEvents.SLIDEQUALITY, slidesContainer.getWidth()));
			if(playerValues.isEmbedded()) recalculateEmbedParts();
			recalculateStageDimensions();		
			
			tmpFill.width = videoSlideWrapper.width;
			tmpFill.height = videoSlideWrapper.height;			
		}
	
		
		private function recalculateStageDimensions():void
		{
			playerValues.playerStageWidth = 960;
			playerValues.playerStageHeight = videoSlideWrapper.y + videoSlideWrapper.height;
			var isAvailable:Boolean = ExternalInterface.available;
			if(playerValues.isEmbedded()) ExternalInterface.call("resizeEmbedBridge", playerValues.playerStageHeight, playerValues.getPresentationID());
			else ExternalInterface.call("resizePlayerContainer", playerValues.playerStageHeight);
		}		
		
		private function recalculateSlidesAndVideoContainers(size:Number){
			var tmpSlidesWidth:Number;
			var tmpSlidesHeight:Number;
			//videoContainer.width = size;
			//videoContainer.height = (videoContainer.width / videoRatio);
			videoContainerWidth = size;
			videoContainerHeight = (size / videoRatio);
			
			if(videoContainerWidth < 50){
				videoContainer.visible = false;
				tmpSlidesWidth = playerValues.playerStageWidth;
				tmpSlidesHeight = (tmpSlidesWidth / slideRatio);
				slidesContainer.setSize(tmpSlidesWidth,tmpSlidesHeight);
				slidesContainer.x = 0;								
			}else{
				videoContainer.visible = true;
				if(videoStream != null) videoStream.getStreamModule().thcSetPlayerSize(videoContainerWidth,videoContainerHeight);				
				
				tmpSlidesWidth = (playerValues.playerStageWidth-videoContainerWidth)- universalSpace;
				tmpSlidesHeight = (tmpSlidesWidth / slideRatio);
				
				slidesContainer.setSize(tmpSlidesWidth,tmpSlidesHeight);
				slidesContainer.x = videoContainerWidth + universalSpace;			
			}
		}
		
		
		public function centerLoaders(){
			if(videoContainerWidth > 200){				
				videoPreloader.x = videoContainerWidth / 2;
				videoPreloader.y = videoContainerHeight / 2;
			}
			if(slidesContainer.getWidth() < 200) slidePreloader.visible = false;
			else slidePreloader.visible = true;
			slidePreloader.x = slidesContainer.x + (slidesContainer.getWidth() / 2) - (slidePreloader.width / 2);
			slidePreloader.y = (slidesContainer.getHeight() / 2) - (slidePreloader.height / 2);
		}		
		
		private function recalculateControlsPosition(){
			var controlsHeight:int = 45; //Because of tooltop, height property is inacurate
			if(videoContainerWidth < 350){
				videoControls.adaptToWidth(slidesContainer.getWidth() - (2*universalSpace));
				videoControls.y = slidesContainer.getHeight() - controlsHeight - universalSpace;
				videoControls.x = slidesContainer.x + universalSpace;	
				
				slidesControls.x = ((slidesContainer.getWidth() - slidesControls.width) / 2) + slidesContainer.x;
				slidesControls.y = slidesContainer.getHeight() - controlsHeight - universalSpace - controlsHeight - universalSpace;				
			} else {			
				
				videoControls.adaptToWidth(videoContainerWidth - (2*universalSpace));
				videoControls.y = videoContainerHeight - controlsHeight - universalSpace;
				videoControls.x = universalSpace;
				slidesControls.x = ((slidesContainer.getWidth() - slidesControls.width) / 2) + slidesContainer.x;
				slidesControls.y = slidesContainer.getHeight() - controlsHeight - universalSpace;
				//Avoid slidescontrols to go over wrapper
				if(slidesControls.y < 25) slidesControls.y = 25;
			}
			
			
			//Special case for slide control
			if(slidesContainer.getWidth() < (slidesControls.width + (2*universalSpace))) slidesControls.visible = false;
			else if(!slidesControls.visible) slidesControls.visible = true;			
		}
		
		private function recalculateEmbedParts(){
			if(videoContainerWidth >= playerValues.playerStageWidth - 150){
				embedLogo.y = videoControls.y - 65;
				embedLogo.x = videoContainerWidth - embedLogo.width;
			} else {
				embedLogo.x = playerValues.playerStageWidth - embedLogo.width;
				embedLogo.y = slidesControls.y; 
			}
							
		}
		
		private function recalculateBigPlayButton(){
			
			if(videoContainerWidth < 350){
				bigPlayButton.resize(slidesContainer.getWidth(),slidesContainer.getHeight());			
				bigPlayButton.x = slidesContainer.x;
			} else {
				bigPlayButton.resize(videoContainerWidth,videoContainerHeight);
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
			slidesContainer.x = videoContainerWidth + universalSpace;
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
			slidesContainer.hideSlideNumbers();
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
		
		private function changeVideoSlideDefaultRatio(newRatio:int){
			//Recalculate 0-100 to appropriate value
			var calculatedWidth = (newRatio / 100) * playerValues.playerStageWidth;
			sliderMoved(new GeneralEvents("SliderMoved",calculatedWidth));
		}
		
		private function embedOver(e:MouseEvent){
			embedLogo.symbolEmbed.visible = true;
		}
		private function embedOut(e:MouseEvent){
			embedLogo.symbolEmbed.visible = false;
		}	
		private function embedClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents(ControlsEvents.SLIDESLIVELOGO));
		}		
		
		//Some key functions served for other classes
		public function addVideoStream(videoStream:PlayerClip){
			this.videoStream = videoStream;
			videoContainer.addChild(this.videoStream);
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
		
		public function changeSyncState(synchronized:Boolean):void {
			if(synchronized){				
				videoControls.colorGreenDot();	
				slidesControls.colorGreenSync();
				slidesContainer.displayGreenToast();
			} else {
				videoControls.colorRedDot();
				slidesControls.colorRedSync();
				slidesContainer.displayRedToast();
			}
		}	
		
		//In case, that something was asynchronously updated, update also GUI dimensions and send to JavaScript
		public function recalculateGUI():void{
			recalculateControlsPosition();
			recalculateBigPlayButton();
			dispatchEvent(new GeneralEvents(GeneralEvents.SLIDEQUALITY, slidesContainer.getWidth()));
			if(playerValues.isEmbedded()) recalculateEmbedParts();
			recalculateStageDimensions();
		}	
		
		public function hideVideoLoader():void {
			videoPreloader.visible = false;
		}
		
		//Getters
		public function getVideoHeight():Number{
			return videoContainerHeight;
		}
		
		public function getVideoWidth():Number {
			return videoContainerWidth;
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
		
		public function setSlideNumbers(currentSlide:int, totalSlides:int):void
		{
			slidesContainer.changeSlideNumbers(currentSlide, totalSlides);
		}
		
		public function showFullscreenState():void
		{
			videoControls.showFullscreenState();
			
		}
		
		public function showNormalState():void
		{
			videoControls.showNormalState();
			
		}
		
		public function showBuyDialog(presentationID:int):void {
			buyDialog = new BuyDialog(gaTracker, presentationID);
			buyDialog.x = (playerValues.playerStageWidth - buyDialog.width) / 2;
			buyDialog.y = (playerValues.playerStageHeight - buyDialog.height) / 2;
			addChild(buyDialog);
		}
		
		public function disablePlayer():void {
			videoSlideWrapper.alpha = 0.2;
		}
		
		public function enablePlayer():void {
			videoSlideWrapper.alpha = 1;
		}		
	}
}