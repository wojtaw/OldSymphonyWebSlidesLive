package slideslive.mediamodule
{
	import flash.display.MovieClip;
	
	public class Module extends MovieClip implements VideoModuleInterface
	{
		public function Module()
		{
		}
		
		public function thcGetStreamClip():MovieClip{
			return null;
		}		
		
		public function thcPlayVideo():Boolean{
			return true;
		}
		
		public function thcPauseVideo():Boolean{
			return true;
		}	
		
		public function thcGetCurrTime():Number{
			return 0;
		}	
		
		public function thcGetTotalTime():Number{
			return 0;
		}	
		
		public function thcSeekTime(tmpTime:int):Boolean{
			return true;
		}	
		
		public function thcGetBytesLoaded():Number{
			return 0;
		}			
		
		public function thcGetBytesTotal():Number{
			return 0;
		}	
		
		public function thcSetVolume(vol:Number):Boolean {
			return true;
		}
	
	}
}