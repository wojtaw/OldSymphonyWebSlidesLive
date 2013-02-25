package slideslive.gui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.FocusEvent;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.KeyboardEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.text.TextField;
	import flash.ui.Keyboard;
	
	import slideslive.event.GeneralEvents;
	import slideslive.values.PlayerValues;

	public class NoteTaker extends MovieClip
	{
		private var notesBackground:Sprite;
		private var noteField:TextField = new TextField();
		private var isUserLogged:Boolean = false;
		private var playerValues:PlayerValues;
		
		public function NoteTaker(playerValues:PlayerValues) {
			this.playerValues = playerValues;
			createLayout();
			addListeners();
		}
		
		private function addListeners():void {
			noteField.addEventListener(FocusEvent.FOCUS_IN, focusInNoteTaking);
		}
		
		private function focusInNoteTaking(event:FocusEvent):void{
			checkUserLogin();
			trace("focus in");
			dispatchEvent(new GeneralEvents(GeneralEvents.NOTETAKING));
			noteField.addEventListener(KeyboardEvent.KEY_DOWN, checkEnterForSend);
			noteField.text = " ";
		}
				
		private function createLayout():void {
			notesBackground = new Sprite();
			notesBackground.graphics.beginFill(0xFFFFFF);
			notesBackground.graphics.drawRect(0,0,300,25);
			notesBackground.graphics.endFill();	

			noteField.border = true;
			noteField.width = 270;
			noteField.height = 20;
			noteField.y = 2;
			noteField.text = "Write comment and hit enter";
			noteField.type = "input";
			
			addChild(notesBackground);
			addChild(noteField);			
		}
		
		private function checkEnterForSend(e:KeyboardEvent):void{
			if(e.keyCode == Keyboard.ENTER || e.keyCode == 13){
				//noteField.removeEventListener(KeyboardEvent.KEY_DOWN, checkEnterForSend);
				if(isUserLogged) {
					sendUserNote();
					noteField.text = "";
				}
				else showLoginSignup();
			}
		}	
		
		private function showLoginSignup():void
		{
			noteField.text = "You are not logged in, Please login or register!";
		}
		
		private function checkUserLogin():void
		{
			var request:URLRequest = new URLRequest();
			//request.url = "http://virtual.slideslive.com/createEmail.php";
			request.url = playerValues.playerAPIUserAuth;
			request.method = URLRequestMethod.GET;
			
			var loader:URLLoader = new URLLoader();
			loader.addEventListener(Event.COMPLETE, userLoginCheckComplete);
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, httpStatusHandler);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			loader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);			
			
			try {
				loader.load(request);
			} catch (error:Error) {
				trace("Unable to load URL");
			}	
		}
		
		private function sendUserNote():void
		{
			dispatchEvent(new GeneralEvents(GeneralEvents.ADDNOTE,0,noteField.text));
		}		
		
		private function userLoginCheckComplete(e:Event):void {
			if(e.target.data == "LOGGED") isUserLogged = true;
			else isUserLogged = false;
		}	
		
		private function httpStatusHandler (e:HTTPStatusEvent):void {
			//trace("httpStatusHandler:" + e.status);
		}
				
		private function securityErrorHandler (e:Event):void{
			//trace("\n\nsecurityErrorHandler:" + e);
		}
		private function ioErrorHandler(e:Event):void{
			//trace("ioErrorHandler: " + e);
		}			
	}
}