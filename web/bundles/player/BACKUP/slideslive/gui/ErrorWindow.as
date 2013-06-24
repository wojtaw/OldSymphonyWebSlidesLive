package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.text.TextField;

	public class ErrorWindow extends MovieClip
	{
		var errorContent:TextField;
		
		public function ErrorWindow()
		{
			initWindow();
			this.visible = false;

		}
		
		private function initWindow():void
		{
			initBackground();			
			initTextField();
		}
		
		private function initTextField():void
		{
			errorContent = new TextField();			
			errorContent.textColor = 0xFFFFFF;
			errorContent.y = 50;
			errorContent.x = 50;
			errorContent.width = 960;
			errorContent.height = 300;
			addChild(errorContent);
		}
		
		private function initBackground():void
		{
			var square:Sprite = new Sprite();
			square.graphics.beginFill(0x000099);
			square.graphics.drawRect(0,0,500,300);
			square.graphics.endFill();
			square.alpha = 0.5;
			addChild(square);
		}
		
		public function appendError(textToAdd:String):void {
			this.visible = true;
			errorContent.appendText(textToAdd);
		}
	}
}