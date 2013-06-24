package slideslive.mediamodule
{	
	import flash.display.Loader;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.*;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.system.ApplicationDomain;
	import flash.system.LoaderContext;
	import flash.system.Security;
	import flash.system.SecurityDomain;
	
	import slideslive.controller.ErrorController;
	import slideslive.event.GeneralEvents;
	import slideslive.gui.PlayerClip;
	import slideslive.mediamodule.vimeo.VimeoPlayer;
	import slideslive.util.ErrorCodes;
	import slideslive.util.PlayerOutput;
	
	public class ModuleVimeo extends Module
	{
		public var playerVimeo:VimeoPlayer;
		private var videoParameter:String;
		private var videoHeight:int;
		private var videoWidth:int;
		private var error:ErrorController;
		
		public function ModuleVimeo(error:ErrorController, videoParameter:String, videoHeight:int, videoWidth:int){
			this.error = error;
			this.videoParameter = videoParameter;
			this.videoHeight = videoHeight;
			this.videoWidth = videoWidth;
			init();
			createVimeoPlayer();
		}
		
		public function init():void {
			Security.allowDomain("www.vimeo.com");  
			Security.allowDomain("vimeo.com");  
			Security.allowDomain('s.ytimg.com');  
			Security.allowDomain('i.ytimg.com');
		}
		
		override public function thcGetStreamClip():PlayerClip{
			var tmpMovieClipWrapper:PlayerClip = new PlayerClip();
			tmpMovieClipWrapper.setStreamClip(playerVimeo, this);
			return tmpMovieClipWrapper;
		}
		
		public function createVimeoPlayer():void {
			playerVimeo = new VimeoPlayer("f2a6e5fbbbda33f87c5dd75747fa7454401dbbf9", int(videoParameter), videoWidth, videoHeight);
			playerVimeo.addEventListener(GeneralEvents.VIMEO_SPECIFIC_READY, onPlayerReady);

			/*
			loaderYT = new Loader();
			loaderYT.contentLoaderInfo.addEventListener(Event.INIT, onLoaderInit);
			loaderYT.addEventListener(IOErrorEvent.IO_ERROR, YTLoaderError);
			
			var requestYT:URLRequest = new URLRequest("http://www.youtube.com/apiplayer?version=3");
			
			loaderYT.load(requestYT);
			*/
		}
		
		private function YTLoaderError(e:IOErrorEvent):void {
			PlayerOutput.printError("YOUTUBE Module failed to load YT API Player");
			error.handleError(ErrorCodes.YT_MODULE_APIFAIL);
		}
		
		private function onPlayerReady(event:Event):void {
			thcSetPlayerSize(videoWidth, videoHeight);
			
			PlayerOutput.printLog("Vimeo Player created");
			dispatchEvent(new GeneralEvents(GeneralEvents.VIDEO_MODULE_READY));
		}
		
		
		override public function thcPlayVideo():Boolean{
			PlayerOutput.printLog("Play video");
			playerVimeo.play();
			return true;
		}
		
		override public function thcPauseVideo():Boolean{
			PlayerOutput.printLog("Pause video");
			playerVimeo.pause();
			return true;
		}	
		
		override public function thcGetCurrTime():Number{
			return playerVimeo.getCurrentTime();
		}	
		
		override public function thcGetTotalTime():Number{
			//return playerVimeo.getDuration();
			return playerVimeo.getDuration();
		}	
		
		override public function thcSeekTime(tmpTime:int):Boolean{
			PlayerOutput.printLog("Rewind video to time");
			playerVimeo.seekTo(tmpTime);
			return true;
		}	
		
		override public function thcGetBytesLoaded():Number{
			return playerVimeo.getMooga().bytesLoaded;
		}			
		
		override public function thcGetBytesTotal():Number{
			return playerVimeo.getMooga().bytesTotal;
		}	
		
		override public function thcSetVolume(vol:Number):Boolean {
			playerVimeo.getMooga().setVolume(vol);
			return true;
		}
		
		override public function thcSetPlayerSize(width:Number, height:Number):Boolean {
			playerVimeo.width = width;
			playerVimeo.height = height;
			return true;
		}		
	}
}