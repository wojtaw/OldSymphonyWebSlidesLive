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
	import slideslive.util.ErrorCodes;
	import slideslive.util.PlayerOutput;
	
	
	public class ModuleYoutube extends Module
	{
		public var playerYT:Object;
		public var loaderYT:Loader;
		private var videoParameter:String;
		private var videoHeight:int;
		private var videoWidth:int;
		private var error:ErrorController;
		
		public function ModuleYoutube(error:ErrorController, videoParameter:String, videoHeight:int, videoWidth:int)
		{	
			this.error = error;
			this.videoParameter = videoParameter;
			this.videoHeight = videoHeight;
			this.videoWidth = videoWidth;
			init();
			createYTPlayer();
		}
		
		public function init():void {
			Security.allowDomain("www.youtube.com");  
			Security.allowDomain("youtube.com");  
			Security.allowDomain('s.ytimg.com');  
			Security.allowDomain('i.ytimg.com');
		}
		
		override public function thcGetStreamClip():PlayerClip{
			var tmpMovieClipWrapper:PlayerClip = new PlayerClip();
			tmpMovieClipWrapper.setStreamClip(loaderYT, this);
			return tmpMovieClipWrapper;
		}
		
		public function createYTPlayer():void {
			loaderYT = new Loader();
			loaderYT.contentLoaderInfo.addEventListener(Event.INIT, onLoaderInit);
			loaderYT.addEventListener(IOErrorEvent.IO_ERROR, YTLoaderError);
			
			var requestYT:URLRequest = new URLRequest("http://www.youtube.com/apiplayer?version=3");
			
			loaderYT.load(requestYT);
		}
		
		private function YTLoaderError(e:IOErrorEvent):void {
			PlayerOutput.printError("YOUTUBE Module failed to load YT API Player");
			error.handleError(ErrorCodes.YT_MODULE_APIFAIL);
		}
		
		private function onLoaderInit(e:Event):void {
			loaderYT.content.addEventListener("onReady", onPlayerReady);
			loaderYT.content.addEventListener("onError", onPlayerError);
			loaderYT.content.addEventListener("onStateChange", onPlayerStateChange);
			loaderYT.content.addEventListener("onPlaybackQualityChange", onVideoPlaybackQualityChange);
		}	
		
		
		private function onPlayerReady(event:Event):void {
			playerYT = loaderYT.content;
			playerYT.setSize(videoWidth, videoHeight);
			
			PlayerOutput.printLog("Available qualities: " + playerYT.getAvailableQualityLevels()[0]);
			playerYT.cueVideoById(videoParameter, 0, "large");
			PlayerOutput.printLog("YOUTUBE Player created");
			dispatchEvent(new GeneralEvents(GeneralEvents.VIDEO_MODULE_READY));
		}
		
		private function onPlayerError(event:Event):void {
			PlayerOutput.printLog("YOUTUBE Player error");			
		}
		
		private function updateQuality(quality:String):void {
			playerYT.setPlaybackQuality("large");			 
		}
		
		
		private function onPlayerStateChange(event:Event):void {
			PlayerOutput.printLog("STATE: "+playerYT.getPlayerState());
			PlayerOutput.printLog("Available qualities: "+playerYT.getAvailableQualityLevels()[0]);
			updateQuality("NORMAL");
			if(playerYT.getPlayerState() != -1 && playerYT.getPlayerState() != 5){
				//TO DO - CLEAN THIS SHIT
				/*
				if(videoController.videoDuration == 0){
					PlayerOutput.printLog("YOUTUBE MODULE - Duration changed to "+horiGetTotalTime());
					videoController.videoDuration = playerYT.getDuration();
				}
				*/
			}
		}
		
		private function onVideoPlaybackQualityChange(event:Event):void {
			
		}		
		
		override public function thcPlayVideo():Boolean{
			trace("Options: "+playerYT.getOptions());
			PlayerOutput.printLog("Play video");
			//playerYT.playVideo();
			playerYT.playVideo();
			return true;
		}
		
		override public function thcPauseVideo():Boolean{
			PlayerOutput.printLog("Pause video");
			playerYT.pauseVideo();
			return true;
		}	
		
		override public function thcGetCurrTime():Number{
			//playerMain.printLog(playerYT.getCurrentTime());
			return playerYT.getCurrentTime();
		}	
		
		override public function thcGetTotalTime():Number{
			return playerYT.getDuration();
		}	
		
		override public function thcSeekTime(tmpTime:int):Boolean{
			PlayerOutput.printLog("Rewind video to time");
			playerYT.seekTo(tmpTime)
			return true;
		}	
		
		override public function thcGetBytesLoaded():Number{
			return playerYT.getVideoBytesLoaded();
		}			
		
		override public function thcGetBytesTotal():Number{
			return playerYT.getVideoBytesTotal();
		}	
		
		override public function thcSetVolume(vol:Number):Boolean {
			playerYT.setVolume(100*vol);
			return true;
		}
		
		override public function thcSetPlayerSize(width:Number, height:Number):Boolean {
			if(playerYT == null) return false;
			else playerYT.setSize(width,height);
			return true;
		}		
		
		
		
		
		
	}
}