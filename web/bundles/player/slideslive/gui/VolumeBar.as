package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.MouseEvent;
	
	import slideslive.event.ControlsEvents;
	import slideslive.values.PlayerValues;

	public class VolumeBar extends MovieClip
	{
		private var viVolumeBar:VIVolumeBar;
		private var isVolumeDragging:Boolean = false;
		private var mouseDetector:MovieClip;
		private var volumeFill:MovieClip;
		private var volumeMask:MovieClip;
		private var volumeBarWidth:Number;
		
		private var playerValues:PlayerValues;
		
		public function VolumeBar(playerValues:PlayerValues)
		{
			this.playerValues = playerValues;
			initGraphic();
			initListeners();
		}
		
		private function initListeners():void
		{
			mouseDetector.addEventListener(MouseEvent.MOUSE_DOWN, initVolumeDrag);
			mouseDetector.addEventListener(MouseEvent.MOUSE_UP, terminateVolumeDrag);
			this.addEventListener(MouseEvent.MOUSE_UP, terminateVolumeDrag);
			
			mouseDetector.addEventListener(MouseEvent.MOUSE_MOVE, mouseMoveHandler);
		}
		
		private function calculateVolumeAndFill():void
		{
			var tmpNumber:Number = mouseDetector.mouseX / mouseDetector.width;
			tmpNumber = nearZeroRounding(tmpNumber);
			
			volumeFill.scaleX = tmpNumber; 
			dispatchEvent(new ControlsEvents(ControlsEvents.VOLUME, tmpNumber));
		}
		
		private function nearZeroRounding(tmpNumber:Number):Number {
			if(tmpNumber < 0.05) tmpNumber = 0;
			return tmpNumber;
		}
		
		private function mouseMoveHandler(e:MouseEvent):void{
			if(isVolumeDragging){
				calculateVolumeAndFill();
			}
		}				
		
		private function initVolumeDrag(e:MouseEvent):void{
			calculateVolumeAndFill();					
			isVolumeDragging = true;
		}
		
		private function terminateVolumeDrag(e:MouseEvent):void{
			isVolumeDragging = false
		}		
		
		private function initGraphic():void
		{
			viVolumeBar = new VIVolumeBar();
			volumeBarWidth = viVolumeBar.width - 5;
			
			volumeMask = drawVolumeMask(volumeBarWidth);
			volumeMask.y = 19;
			
			volumeFill = new MovieClip();
			volumeFill.graphics.beginFill(playerValues.colorOfControls);
			volumeFill.graphics.drawRect(0,0,volumeBarWidth,10);
			volumeFill.graphics.endFill();
			volumeFill.mask = volumeMask;
			volumeFill.y = 19;
			
			mouseDetector = new MovieClip();
			mouseDetector.graphics.beginFill(0x000000);
			mouseDetector.graphics.drawRect(0,0,this.volumeBarWidth,15);
			mouseDetector.graphics.endFill();	
			mouseDetector.y = 17;
			mouseDetector.alpha = 0;
			
			
			
			addChild(viVolumeBar);
			addChild(volumeFill);
			addChild(volumeMask);
			addChild(mouseDetector);
			
		}
		
		private function drawVolumeMask(totalWidth:Number):MovieClip {
			var tmpMask:MovieClip = new MovieClip();
			
			var blockWidth:Number = 2;
			var blockSpace:Number = 2;
			//Count number of blocks
			var numberOfBlocks:int = 1+(totalWidth-blockWidth) / (blockWidth + blockSpace);
			//count remaining space and adjust block space
			var remainingSpace:Number = totalWidth - (numberOfBlocks*(blockWidth+blockSpace)) + blockSpace;
			blockSpace += remainingSpace / (numberOfBlocks-1);

			tmpMask.graphics.beginFill(0x005500);
			//Generate blocks
			for (var i:int = 0; i < numberOfBlocks; i++) {
				tmpMask.graphics.drawRect(i*(blockWidth+blockSpace),0,blockWidth,15);
			}
			tmpMask.graphics.endFill();	
			return tmpMask;
		}
	}
}