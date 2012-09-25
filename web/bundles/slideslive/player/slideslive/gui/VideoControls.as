package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.events.TimerEvent;
	import flash.geom.ColorTransform;
	import flash.utils.Timer;
	
	import slideslive.event.ControlsEvents;
	import slideslive.event.GeneralEvents;
	import slideslive.values.PlayerValues;

	public class VideoControls extends MovieClip
	{
		private var playerValues:PlayerValues;
		
		//Buttons MoviceClips
		private var viFull:VIFull;
		private var viPlay:VIPlay;
		private var viVolumeBar:VIVolumeBar;
		private var viTimeBar:VITimeBar;
		private var viControlsAdaptable:VIControlsAdaptable;
		private var slidesDot:SlidesDot;
		
		private var timelineBarSeek:MovieClip;
		private var timelineBarPlayed:MovieClip;
		private var timelineBarLoaded:MovieClip;
		private var timelineTotalLength:Number;
		
		private var symbolSeekCursor:SymbolSeekCursor;
		private var seekTimer:Timer = new Timer(25); 		
		
		private var toolTipWindow:ToolTipWindow;	
		
		public function VideoControls(playerValues:PlayerValues)
		{
			this.playerValues = playerValues;
			initControls();				
		}
		
		public function adaptToWidth(newWidth:int):void{
			viFull.x = newWidth - viFull.width;
			viPlay.x = 0;
			viVolumeBar.x = viFull.x - viVolumeBar.width - 1;
			viTimeBar.x = viVolumeBar.x - viTimeBar.width;
			viControlsAdaptable.x = viPlay.width + 1;
			viControlsAdaptable.width = viTimeBar.x - viControlsAdaptable.x;
			
			timelineTotalLength = viControlsAdaptable.width - 19;
			
			timelineBarSeek.barFill.width = timelineTotalLength;
			timelineBarSeek.x = viControlsAdaptable.x + 9;
			timelineBarSeek.y = viControlsAdaptable.y + 18;
			
			timelineBarPlayed.x = viControlsAdaptable.x + 9;
			timelineBarPlayed.y = viControlsAdaptable.y + 18;			
			
		}
		
		public function initControls():void{
			addComponents();
			transformColors();
			addListeners();		
			initTooltip();
		}
		
		private function initTooltip(){
			toolTipWindow = new ToolTipWindow();
			toolTipWindow.y = -10;
			toolTipWindow.x = 10;
			toolTipWindow.tooltipField.text = "Hello world tooltip";
			toolTipWindow.visible = false;	
			addChild(toolTipWindow);
		}		
		
		private function addComponents():void{
			viFull = new VIFull();
			viPlay = new VIPlay();
			viControlsAdaptable = new VIControlsAdaptable();
			viVolumeBar = new VIVolumeBar();
			viTimeBar = new VITimeBar();
			timelineBarSeek = createBarSeek();
			timelineBarPlayed = createBarPlayed();
			//timelineBarLoaded = createBarLoaded();
			symbolSeekCursor = new SymbolSeekCursor();
			slidesDot = new SlidesDot();
			slidesDot.y = 20;
			
			timelineBarSeek.addChild(symbolSeekCursor);
			
			addChild(timelineBarPlayed);
			addChild(timelineBarSeek);
			
			viControlsAdaptable.addChild(slidesDot);
			
			addChild(viFull);
			addChild(viPlay);
			addChild(viControlsAdaptable);
			addChild(viTimeBar);
			addChild(viVolumeBar);			
		}
		
		private function createBarSeek():MovieClip
		{
			var timelineBar = new TimelineBar();
			timelineBar.barFill.alpha = 0;
			timelineBar.barFill.height = 9;			
			return timelineBar;
		}
		
		
		private function createBarPlayed():MovieClip
		{
			var timelineBar = new TimelineBar();
			
			var newColorTransform:ColorTransform = timelineBar.barFill.transform.colorTransform;
			newColorTransform.color = playerValues.colorOfControls;
			timelineBar.barFill.transform.colorTransform = newColorTransform;
			
			timelineBar.barFill.height = 9;	
			timelineBar.barFill.width = 1;
			return timelineBar;
		}		
		
		private function transformColors(){
			var newColorTransform:ColorTransform = viFull.symbolFull.transform.colorTransform;
			newColorTransform.color = playerValues.colorOfControls;
			viFull.symbolFull.transform.colorTransform = newColorTransform;
			viPlay.symbolPlay.transform.colorTransform = newColorTransform;
			viPlay.symbolPause.transform.colorTransform = newColorTransform;			

			viFull.symbolFull.visible = false;	
			viPlay.symbolPlay.alpha = 0;
			viPlay.symbolPause.visible = false;
			viPlay.symbolPauseUnder.visible = false;
			
			
			viPlay.playBg.alpha = 0.85;
			viFull.fullBg.alpha = 0.85;
		}	
		
		private function addListeners():void {
			viFull.addEventListener(MouseEvent.MOUSE_OVER, fullOver);
			viFull.addEventListener(MouseEvent.MOUSE_OUT, fullOut);
			viFull.addEventListener(MouseEvent.CLICK, fullClicked);
			viPlay.addEventListener(MouseEvent.MOUSE_OVER, playOver);
			viPlay.addEventListener(MouseEvent.MOUSE_OUT, playOut);
			viPlay.addEventListener(MouseEvent.CLICK, playClicked);	
			timelineBarSeek.addEventListener(MouseEvent.MOUSE_MOVE, moveSeekCursor);
			timelineBarSeek.addEventListener(MouseEvent.MOUSE_OUT, moveSeekStop);
			timelineBarSeek.addEventListener(MouseEvent.CLICK, seekerClick);	
		}
		
		private function fullOver(e:MouseEvent){
			viFull.symbolFull.visible = true;
			viFull.fullBg.alpha = 1;
			placeToolTip("FullScreen",viFull.x);
			toolTipWindow.visible = true;			
		}
		private function fullOut(e:MouseEvent){
			viFull.symbolFull.visible = false;
			viFull.fullBg.alpha = 0.85;
			disableToolTip();
		}	
		
		private function fullClicked(e:MouseEvent){
			dispatchEvent(new ControlsEvents("FullScreen"));
		}
		
		private function playOver(e:MouseEvent){
			viPlay.symbolPlay.alpha = 1;
			viPlay.symbolPause.alpha = 1;
			viPlay.playBg.alpha = 1;
			placeToolTip(" Play ",viPlay.x);
			toolTipWindow.visible = true;					
		}
		private function playOut(e:MouseEvent){
			viPlay.symbolPlay.alpha = 0;
			viPlay.symbolPause.alpha = 0;
			viPlay.playBg.alpha = 0.85;
			disableToolTip();
		}	
		
		private function playClicked(e:MouseEvent){
			dispatchEvent(new ControlsEvents("PlayBtn"));
		}		
		
		private function disableToolTip(){
			toolTipWindow.visible = false;
			toolTipWindow.x = 100; //Center tooltip because of zooming controls
		}		
		
		private function placeToolTip(tooltipText:String, peakPosition:int):void {
			toolTipWindow.tooltip.width = tooltipText.length * 6;
			toolTipWindow.tooltipField.width = tooltipText.length * 6;
			toolTipWindow.tooltipTriangle.x = (toolTipWindow.tooltip.width / 2);
			
			toolTipWindow.tooltipField.x = 0;
			toolTipWindow.x = peakPosition-(toolTipWindow.width/2)+25;
			toolTipWindow.tooltipField.text = tooltipText;			
		}	
		
		public function showPlayState(){
			viPlay.symbolPlay.visible = false;
			viPlay.symbolPlayUnder.visible = false;
			viPlay.symbolPause.visible = true;
			viPlay.symbolPauseUnder.visible = true;
		}
		
		public function showPauseState(){
			viPlay.symbolPlay.visible = true;
			viPlay.symbolPlayUnder.visible = true;
			viPlay.symbolPause.visible = false;
			viPlay.symbolPauseUnder.visible = false;
		}		
		
		
		public function updateBarPlayed(ratio:Number):void
		{
			timelineBarPlayed.barFill.width = ratio * timelineTotalLength; 
		}
		
		public function updateTime(currentTime:Number, totalSeconds:Number):void
		{
			var currentMinutes:int = Math.floor(currentTime / 60);
			var currentSeconds:int = currentTime - (currentMinutes*60);
			var durationMinutes:int = Math.floor(totalSeconds / 60);
			var durationSeconds:int = totalSeconds - (durationMinutes*60);
			var timeStringBuilder:String = "";
			if(currentMinutes < 10) timeStringBuilder += "0"+currentMinutes;
			else timeStringBuilder += currentMinutes;
			if(currentSeconds < 10) timeStringBuilder += ":0"+currentSeconds;
			else timeStringBuilder += ":"+currentSeconds;
			if(totalSeconds==0) timeStringBuilder += "/00:00";
			else timeStringBuilder += "/"+durationMinutes+":"+durationSeconds;
			viTimeBar.timeCodeField.text = timeStringBuilder;			
			
		}
	
		//Move seek cursor to proper postiion, make it visible and start the time
		private function moveSeekCursor(e:MouseEvent){
			symbolSeekCursor.x = timelineBarSeek.mouseX;
			symbolSeekCursor.visible = true;
			seekTimer.start();
		}		
		
		//When mouse is out of the bar, stop timer and make seeker invisible
		private function moveSeekStop(e:MouseEvent){
			symbolSeekCursor.visible = false;
			seekTimer.stop();
		}			
		
		//Timer will ensure, thath seeker will move with the mouse when over
		private function moveSeeker(e:TimerEvent):void {
			symbolSeekCursor.x = timelineBarSeek.mouseX;
		}	
		
		private function seekerClick(e:MouseEvent):void {
			dispatchEvent(new ControlsEvents("VideoSeek",(symbolSeekCursor.x / timelineTotalLength)));			
		}
		
		//Recieves number 0 - 1 signalizing position on the bar 
		public function moveSlideDot(position:Number):void
		{
			slidesDot.x = 10+((viControlsAdaptable.width - 20)*position);
		}
	}
}