package slideslive.util
{
	public class PlayerOutput
	{
		public function PlayerOutput()
		{			
			
		}
		
		//Player console output
		public static function printLog(message:String):void {
			trace("LOG: "+message);
		}
		
		public static function printTest(message:String):void {
			trace("TEST: "+message);
		}
		
		public static function printError(message:String):Boolean {
			trace("ERROR: "+message);
			return false;
			//playerGUI.showErrorWindow("ERROR: "+message);
		}
		
		public static function printDebug(message:String):void {
			trace("DEBUG: "+message);
			//playerGUI.debugWin.debugField.appendText(message);
		}			
	}
}