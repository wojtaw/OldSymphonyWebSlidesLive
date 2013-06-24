package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.ColorTransform;
	
	import slideslive.event.ControlsEvents;
	import slideslive.event.GeneralEvents;
	import slideslive.values.PlayerValues;

	public class SlidesControls extends MovieClip
	{
		private var slMaximize:SLMaximize;
		private var slMouth:SLMouth;
		private var slNext:SLNext;
		private var slPrev:SLPrev;
		private var slSync:SLSync;
		private var playerValues:PlayerValues;
		private var toolTipWindow:ToolTipWindow;		
		
		public function SlidesControls(playerValues:PlayerValues)
		{
			this.playerValues = playerValues;
			initControls();			
		}
		
		public function initControls():void{
			addButtons();
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
		
		private function addListeners(){
			slNext.addEventListener(MouseEvent.CLICK, nextSldClick);
			slNext.addEventListener(MouseEvent.MOUSE_OVER, nextSldOver);
			slNext.addEventListener(MouseEvent.MOUSE_OUT, nextSldOut);
			slPrev.addEventListener(MouseEvent.CLICK, prevSldClick);
			slPrev.addEventListener(MouseEvent.MOUSE_OVER, prevSldOver);
			slPrev.addEventListener(MouseEvent.MOUSE_OUT, prevSldOut);
			slMaximize.addEventListener(MouseEvent.CLICK, maximizeClick);
			slMaximize.addEventListener(MouseEvent.MOUSE_OVER, maximizeOver);
			slMaximize.addEventListener(MouseEvent.MOUSE_OUT, maximizeOut);
			slMouth.addEventListener(MouseEvent.CLICK, mouthClick);
			slMouth.addEventListener(MouseEvent.MOUSE_OVER, mouthOver);
			slMouth.addEventListener(MouseEvent.MOUSE_OUT, mouthOut);
			slSync.addEventListener(MouseEvent.CLICK, syncClick);
			slSync.addEventListener(MouseEvent.MOUSE_OVER, syncOver);
			slSync.addEventListener(MouseEvent.MOUSE_OUT, syncOut);
		}
		
		private function addButtons(){
			var spaceBetweenButtons:int = 1;
			slMouth = new SLMouth();
			slMaximize = new SLMaximize();
			slNext = new SLNext(); 
			slPrev = new SLPrev();
			slSync = new SLSync();
			
			slPrev.x = slMouth.x + slMouth.width + spaceBetweenButtons;
			slSync.x = slPrev.x + slPrev.width + spaceBetweenButtons;
			slNext.x = slSync.x + slSync.width + spaceBetweenButtons;
			slMaximize.x = slNext.x + slNext.width + spaceBetweenButtons;
			
			addChild(slMaximize);
			addChild(slSync);
			addChild(slNext);
			addChild(slPrev);
			addChild(slMouth);
		}
		
		private function transformColors(){
			var newColorTransform:ColorTransform = slMouth.symbolMouth.transform.colorTransform;
			newColorTransform.color = playerValues.colorOfControls;
			slMouth.symbolMouth.transform.colorTransform = newColorTransform;		
			slPrev.symbolTag.transform.colorTransform = newColorTransform;	
			slSync.symbolSync.transform.colorTransform = newColorTransform;
			slNext.symbolArrow2.transform.colorTransform = newColorTransform;	
			slMaximize.symbolMaximize.transform.colorTransform = newColorTransform;
			

			slPrev.symbolTag.visible = false;		
			slSync.symbolSync.visible = false;
			slNext.symbolArrow2.visible = false;
			slMaximize.symbolMaximize.visible = false;
			slMouth.symbolMouth.visible = false;	
			
			slMouth.mouthBg.alpha = 0.85;
			slSync.syncBg.alpha = 0.85;
			slPrev.prevBg.alpha = 0.85;
			slNext.nextBg.alpha = 0.85;
			slMaximize.maximizeBg.alpha = 0.85;
		}
		
		private function nextSldOver(e:MouseEvent){
			slNext.symbolArrow2.visible = true;
			slNext.nextBg.alpha = 1;
			placeToolTip("Next slide",slNext.x);
			toolTipWindow.visible = true;
		}
		private function nextSldOut(e:MouseEvent){
			slNext.symbolArrow2.visible = false;
			slNext.nextBg.alpha = 0.85;
			disableToolTip();
		}		
		private function mouthOver(e:MouseEvent){
			slMouth.symbolMouth.visible = true;
			slMouth.mouthBg.alpha = 1;
			placeToolTip("Sync to voice",slMouth.x);
			toolTipWindow.visible = true;
		}
		private function mouthOut(e:MouseEvent){
			slMouth.symbolMouth.visible = false;
			slMouth.mouthBg.alpha = 0.85;	
			disableToolTip();
		}
		private function prevSldOver(e:MouseEvent){
			slPrev.symbolTag.visible = true;
			slPrev.prevBg.alpha = 1;
			placeToolTip("Previous slide",slPrev.x);			
			toolTipWindow.visible = true;
		}
		private function prevSldOut(e:MouseEvent){
			slPrev.symbolTag.visible = false;
			slPrev.prevBg.alpha = 0.85;
			disableToolTip();
		}	
		private function syncOver(e:MouseEvent){
			slSync.symbolSync.visible = true;
			slSync.syncBg.alpha = 1;
			placeToolTip("Sync to slide",slSync.x);
			toolTipWindow.visible = true;
		}
		private function syncOut(e:MouseEvent){
			slSync.symbolSync.visible = false;
			slSync.syncBg.alpha = 0.85;
			disableToolTip();
		}			
		private function maximizeOver(e:MouseEvent){
			slMaximize.symbolMaximize.visible = true;
			slMaximize.maximizeBg.alpha = 1;
			placeToolTip("Fullscreen slide",slMaximize.x);
			toolTipWindow.visible = true;
		}
		private function maximizeOut(e:MouseEvent){
			slMaximize.symbolMaximize.visible = false;
			slMaximize.maximizeBg.alpha = 0.85;
			disableToolTip();
		}
		
		private function nextSldClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents("NextSlide"));
		}	
		private function mouthClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents("SynchronizeSlides"));			
		}	
		private function prevSldClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents("PreviousSlide"));			
		}	
		private function syncClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents("JumpToTime"));			
		}	
		private function maximizeClick(e:MouseEvent){
			dispatchEvent(new ControlsEvents("OpenBigSlide"));			
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
		
		public function colorRedSync():void {
			slSync.redFill.visible = true;
			slSync.greenFill.visible = false;
		}
		
		public function colorGreenSync():void {
			slSync.greenFill.visible = true;
			slSync.redFill.visible = false;
			
		}			
	}
}