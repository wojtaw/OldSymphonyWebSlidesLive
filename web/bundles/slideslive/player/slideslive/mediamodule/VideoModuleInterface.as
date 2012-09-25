package slideslive.mediamodule
{
	import flash.display.MovieClip;
	
	public interface VideoModuleInterface
	{
		function thcGetStreamClip():MovieClip;
		function thcPlayVideo():Boolean;
		function thcPauseVideo():Boolean;
		function thcGetCurrTime():Number;
		function thcGetTotalTime():Number;
		function thcSeekTime(tmpTime:int):Boolean;
		function thcGetBytesLoaded():Number;
		function thcGetBytesTotal():Number;
		function thcSetVolume(vol:Number):Boolean;
		
	}
}