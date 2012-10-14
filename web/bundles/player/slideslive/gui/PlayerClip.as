package slideslive.gui
{
	import flash.display.DisplayObjectContainer;
	import flash.display.MovieClip;
	
	import slideslive.mediamodule.Module;

	public class PlayerClip extends MovieClip
	{
		private var streamClip:DisplayObjectContainer;
		private var streamModule:Module;
		
		public function PlayerClip()
		{
			
		}
		
		public function setStreamClip(streamClip:DisplayObjectContainer, streamModule:Module=null){
			this.streamClip = streamClip;
			this.streamModule = streamModule;
			this.addChild(this.streamClip);
		}
		
		public function getStreamClip():Object{
			return streamClip;
		}	
		
		public function getStreamModule():Object{
			return streamModule;
		}		
		
	}
}