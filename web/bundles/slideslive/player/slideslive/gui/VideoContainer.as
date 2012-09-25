package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;

	public class VideoContainer extends MovieClip
	{
		public function VideoContainer()
		{
			var square:Sprite = new Sprite();
			square.graphics.beginFill(0x0088FF);
			square.graphics.drawRect(0,0,542,305);
			square.graphics.endFill();	
			addChild(square);			
		}
	}
}