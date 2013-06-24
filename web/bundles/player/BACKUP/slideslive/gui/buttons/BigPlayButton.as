package slideslive.gui.buttons
{
	import flash.display.MovieClip;
	import flash.events.MouseEvent;
	
	import slideslive.event.ControlsEvents;

	public class BigPlayButton extends MovieClip
	{
		private var buttonFill:HiddenButton;
		private var bigPlay:BigPlay;
		
		public function BigPlayButton()
		{
			bigPlay = new BigPlay();
			bigPlay.overState.visible = false;
			this.addChild(bigPlay);
			
			buttonFill = new HiddenButton();
			this.addChild(buttonFill);	
			this.addEventListener(MouseEvent.CLICK, fireClickEvent);
			this.addEventListener(MouseEvent.MOUSE_OVER, overEvent);
			this.addEventListener(MouseEvent.MOUSE_OUT, outEvent);
		}
		
		public function resize(newWidth:Number, newHeight:Number){
			buttonFill.width = newWidth;
			buttonFill.height = newHeight;
			bigPlay.x = buttonFill.width/2;
			bigPlay.y = buttonFill.height/2;			
		}
		
		private function overEvent(e:MouseEvent) {
			bigPlay.overState.visible = true;
		}
		
		private function outEvent(e:MouseEvent) {
			bigPlay.overState.visible = false;
		}		
		
		private function fireClickEvent(e:MouseEvent) {
			dispatchEvent(new ControlsEvents("PlayBtn"));
		}		
	}
}