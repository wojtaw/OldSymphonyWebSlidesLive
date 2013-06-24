package slideslive.gui
{
	import com.google.analytics.AnalyticsTracker;
	import com.google.analytics.GATracker;
	
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.FocusEvent;
	import flash.events.HTTPStatusEvent;
	import flash.events.IOErrorEvent;
	import flash.events.KeyboardEvent;
	import flash.events.MouseEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.filters.GlowFilter;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	import flash.net.URLRequestMethod;
	import flash.net.URLVariables;
	import flash.net.navigateToURL;
	import flash.text.AntiAliasType;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.text.TextFormatAlign;
	import flash.utils.Timer;
	
	import slideslive.event.GeneralEvents;
	

	public class BuyDialog extends MovieClip
	{
		private var buttonsWrapper:MovieClip;
		private var buyFree:BuyFree;
		private var buyMoney1:Buy2;
		private var buyMoney2:Buy5;
		private var buyMoney3:Buy15;
		private var mainTextField:TextField;
		private var dialogBackground:Sprite;
		private var gaTracker:AnalyticsTracker;		
		private var presentationID:int;
		private var emailWrapper:MovieClip;
		private var firstFocus = true;
		private var emailField:TextField;
		private var emailWarning:TextField;
		private var buyAmount:int;
		private var requestTimeout:Timer = new Timer(3000,1);		
		
		public function BuyDialog(gaTracker:AnalyticsTracker,presentationID:int)
		{
			this.presentationID = presentationID;
			this.gaTracker = gaTracker;
			createBasicLayout();
			createBuyButtons();		
			addListeners();
		}
		
		
		private function createBasicLayout():void
		{
			dialogBackground = new Sprite();
			dialogBackground.graphics.beginFill(0xFFFFFF);
			dialogBackground.graphics.drawRoundRect(0,0,555,275,15);
			dialogBackground.graphics.endFill();	
			dialogBackground.filters = [new GlowFilter(0x000000, .5, 25, 25, 2, 2, false, false)];
			
			var myFont = new RopaSans();						
			
			var myFormat:TextFormat = new TextFormat();
			myFormat.font = "Ropa Sans";
			myFormat.color = 0x027BF7;
			myFormat.size = 21;
			myFormat.align = TextFormatAlign.CENTER;
			
			mainTextField = new TextField();
			mainTextField.x = 0;
			mainTextField.y = 21;
			mainTextField.width = 555;
			mainTextField.multiline = true;
			mainTextField.wordWrap = true;
			mainTextField.embedFonts = true;
			mainTextField.defaultTextFormat = myFormat;
			mainTextField.antiAliasType = AntiAliasType.ADVANCED;			
			mainTextField.text = "This speaker also has to earn his daily bread.\n\nTo continue watching,\n please contribute to the bread fund. ";
			
			var myFormat2:TextFormat = mainTextField.getTextFormat(46, mainTextField.length);
			myFormat2.color = 0x777777;
			mainTextField.setTextFormat(myFormat2, 46, mainTextField.length);
			
			addChild(dialogBackground);
			addChild(mainTextField);
		}
		
		private function createBuyButtons():void {
			buttonsWrapper = new MovieClip();
			buyFree = new BuyFree();
			buyFree.x = 0;
			
			buyMoney1 = new Buy2();
			buyMoney1.x = 125;
			
			buyMoney2 = new Buy5();
			buyMoney2.x = 250;
			
			buyMoney3 = new Buy15();
			buyMoney3.x = 375;			
			
			buttonsWrapper.addChild(buyFree);
			buttonsWrapper.addChild(buyMoney1);
			buttonsWrapper.addChild(buyMoney2);
			buttonsWrapper.addChild(buyMoney3);
			
			buttonsWrapper.x = (this.width - buttonsWrapper.width) / 2;
			buttonsWrapper.y = this.height - buttonsWrapper.height - 15;
			
			addChild(buttonsWrapper);
		}
		
		private function addListeners():void {
			buyFree.addEventListener(MouseEvent.CLICK, clickedFree);
			buyMoney1.addEventListener(MouseEvent.CLICK, clickedBuy1);
			buyMoney2.addEventListener(MouseEvent.CLICK, clickedBuy2);
			buyMoney3.addEventListener(MouseEvent.CLICK, clickedBuy3);
		}
		
		private function clickedFree(e:MouseEvent):void {
			buyAmount = 0;
			finishBuyDialog(null);
		}
		
		private function clickedBuy1(e:MouseEvent):void {
			buyAmount = 2;
			showEnterEmail();
		}
		
		private function clickedBuy2(e:MouseEvent):void {
			buyAmount = 5;
			showEnterEmail();
		}
		
		private function clickedBuy3(e:MouseEvent):void {
			buyAmount = 15;
			showEnterEmail();
		}		
		
		private function showEnterEmail():void {
			fireAnalytics();
			
			var myFormat:TextFormat = new TextFormat();
			myFormat.font = "Ropa Sans";
			myFormat.color = 0x888888;
			myFormat.size = 21;
			myFormat.align = TextFormatAlign.LEFT;
			myFormat.blockIndent = 20;
			
			buttonsWrapper.visible = false;
			emailWrapper = new MovieClip();
			var continueButton:Continue = new Continue();
			continueButton.addEventListener(MouseEvent.CLICK, validateAndSendEmail);
			emailField = new TextField();
			emailField.border = false;
			emailField.width = 300;
			emailField.height = 30;
			emailField.y = 14;
			emailField.defaultTextFormat = myFormat;
			emailField.type = "input";
			emailField.multiline = false;
			emailField.text = "YOUR EMAIL ADDRESS";
			emailField.addEventListener(FocusEvent.FOCUS_IN, focusInEmail);
			emailField.addEventListener(KeyboardEvent.KEY_DOWN,keyHandler);
			
			
			myFormat.color = 0xCC0000;
			myFormat.size = 21;
			myFormat.blockIndent = 0;
			emailWarning = new TextField();
			emailWarning.y = 60;
			emailWarning.defaultTextFormat = myFormat;
			emailWarning.width = 300;
			emailWarning.height = 30;
			emailWarning.text = "* Please enter your email address.";
			emailWarning.visible = false;
			
			
			var textFieldBorder:Sprite = new Sprite();
			textFieldBorder.graphics.lineStyle(2,0x017BF6);
			textFieldBorder.graphics.drawRect(0,0,300,55);
			
			//stage.focus = inputField;			
			continueButton.x = textFieldBorder.width - 5;

			emailWrapper.addChild(emailField);
			emailWrapper.addChild(textFieldBorder);
			emailWrapper.addChild(emailWarning);
			emailWrapper.addChild(continueButton);
			emailWrapper.x = (this.width - emailWrapper.width) / 2;
			emailWrapper.y = 50;
			
			addChild(emailWrapper);

			mainTextField.text = "Thank you for your contribution. \nLet's continute watching.";
			mainTextField.y = this.height - mainTextField.height - 20;
			
			var myFormat2:TextFormat = mainTextField.getTextFormat(33, mainTextField.length);
			myFormat2.color = 0x777777;
			mainTextField.setTextFormat(myFormat2, 33, mainTextField.length);			
		}
		
		private function keyHandler(e:KeyboardEvent){
			if(e.charCode == 13){
				validateAndSendEmail(null);
			}
		}
		
		private function validateAndSendEmail(e:MouseEvent){
			if(isValidEmail(emailField.text)){
				emailWrapper.visible = false;
				emailWarning.visible = false;
				var preloaderAnim:PreLoaderAnimation = new PreLoaderAnimation();
				preloaderAnim.x = (this.width - preloaderAnim.width) / 2
				preloaderAnim.y = 60;
				addChild(preloaderAnim);
				
				requestTimeout.addEventListener(TimerEvent.TIMER, finishBuyDialog);
				requestTimeout.start();				
				sendRequestToServer(emailField.text);
			}else{
				emailWarning.visible = true;
			}
		}
		
		
		private function sendRequestToServer(emailAddress:String):void {
			var requestVars:URLVariables = new URLVariables();
			requestVars.to = emailAddress;
			requestVars.amount = buyAmount;
			requestVars.presentation = presentationID;
			
			var request:URLRequest = new URLRequest();
			//request.url = "http://virtual.slideslive.com/createEmail.php";
			request.url = "http://virtual.slideslive.com/sendmail.php";
			request.method = URLRequestMethod.GET;
			request.data = requestVars;
			
			var loader:URLLoader = new URLLoader();
			loader.addEventListener(Event.COMPLETE, loaderCompleteHandler);
			loader.addEventListener(HTTPStatusEvent.HTTP_STATUS, httpStatusHandler);
			loader.addEventListener(SecurityErrorEvent.SECURITY_ERROR, securityErrorHandler);
			loader.addEventListener(IOErrorEvent.IO_ERROR, ioErrorHandler);			
			
			try {
				loader.load(request);
			} catch (error:Error) {
				trace("Unable to load URL");
			}			
		}
	
		private function httpStatusHandler (e:Event):void {
			trace("httpStatusHandler:" + e);
			requestTimeout.stop();
			finishBuyDialog(null);	
		}
		
		private function loaderCompleteHandler(e:Event):void {
			trace(e.target.data);
		}		
		private function securityErrorHandler (e:Event):void{
			trace("securityErrorHandler:" + e);
		}
		private function ioErrorHandler(e:Event):void{
			trace("ioErrorHandler: " + e);
		}		
		
		private function isValidEmail(address:String):Boolean{
			var emailExpression:RegExp = /([a-z0-9._-]+?)@([a-z0-9.-]+)\.([a-z]{2,4})/;
			return emailExpression.test(address);			
		}
		
		private function focusInEmail(e:FocusEvent){
			if(firstFocus){
				emailField.text = "@";
				var tmpFormat:TextFormat = emailField.getTextFormat();
				tmpFormat.color = 0x017BF6;
				emailField.setTextFormat(tmpFormat);
				emailField.defaultTextFormat = tmpFormat;
				emailField.setSelection(0,0);
				firstFocus = false;
			}
		}
		
		private function fireAnalytics(){
			if(buyAmount == 0) gaTracker.trackPageview("buyButton/"+buyAmount);
			else gaTracker.trackPageview("buyButton/"+buyAmount+" ID: "+presentationID);
		}
		
		private function finishBuyDialog(e:*):void {
			this.visible = false;
			dispatchEvent(new GeneralEvents(GeneralEvents.BUYDONE));	
		}
		
	}
}