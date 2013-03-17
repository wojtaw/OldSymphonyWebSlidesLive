package slideslive.controller
{
	import flash.display.MovieClip;
	import flash.events.Event;
	
	import slideslive.Player_THC_Streaming;
	import slideslive.error.ErrorHandler;
	import slideslive.gui.ErrorWindow;
	import slideslive.util.ErrorCodes;
	import slideslive.util.PlayerOutput;
	import slideslive.values.PlayerValues;

	public class ErrorController implements ErrorHandler
	{
		private var errorWindow:ErrorWindow;
		public var isDebugMode:Boolean = false;		

		
		public function ErrorController(errorWindow:ErrorWindow)
		{
			PlayerOutput.printLog("INIT Error controller");
			this.errorWindow = errorWindow;
		}
		
		
		public function handleError(error:String, aditionalMessage:String=""):void{
			PlayerOutput.printError(error+" \n\tMESSAGE: "+aditionalMessage+"\n");
			if(checkIfToDisplay(error))errorWindow.appendError(error+" \n\tMESSAGE: "+aditionalMessage+"\n");
		}
		
		private function checkIfToDisplay(error:String):Boolean
		{
			if(isDebugMode) return true;
			else {
				if(error == ErrorCodes.IMAGE_NOT_FOUND) return false;
				else return true;
			}
		}		
		
		
	}
}