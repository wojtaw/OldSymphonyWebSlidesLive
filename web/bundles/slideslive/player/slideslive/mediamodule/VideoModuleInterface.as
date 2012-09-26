package slideslive.mediamodule
{
	import flash.display.MovieClip;
	
	import slideslive.gui.PlayerClip;
	
	public interface VideoModuleInterface
	{
		function thcGetStreamClip():PlayerClip;
		function thcPlayVideo():Boolean;
		function thcPauseVideo():Boolean;
		function thcGetCurrTime():Number;
		function thcGetTotalTime():Number;
		function thcSeekTime(tmpTime:int):Boolean;
		function thcGetBytesLoaded():Number;
		function thcGetBytesTotal():Number;
		function thcSetVolume(vol:Number):Boolean;
		function thcSetPlayerSize(width:Number, height:Number):Boolean;
		
	}
}