package slideslive
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.display.Stage;
	import flash.display.StageAlign;
	import flash.display.StageDisplayState;
	import flash.display.StageQuality;
	import flash.display.StageScaleMode;
	import flash.events.Event;
	import flash.system.Security;
	import flash.system.SecurityDomain;	
	import flash.display.Loader;
	import flash.display.LoaderInfo;	
	
	import slideslive.controller.ErrorController;
	import slideslive.controller.PresentationController;
	import slideslive.event.GeneralEvents;
	import slideslive.gui.ErrorWindow;
	import slideslive.gui.PlayerGUI;
	import slideslive.start.DataReader;
	import slideslive.test.TestMain;
	import slideslive.util.PlayerOutput;
	import slideslive.values.PlayerValues;
	
	
	public class Player_5_THC extends Sprite
	{
		private var playerContainer:MovieClip;
		private var errorWindow:ErrorWindow;
		private var testSuite:TestMain;
		private var playerGUI:PlayerGUI;
		private var presentationController:PresentationController;
		
		//Player values
		private var playerValues:PlayerValues;
		
		//Controllers
		private var errorController;
		
		public function Player_5_THC()
		{
			initTests();
			initErrorHandling();
			initSecurity();
			initStage();
			initData();
		}
		
		private function initSecurity():void {
			Security.allowDomain("www.youtube.com");  
			Security.allowDomain("youtube.com");  
			Security.allowDomain('s.ytimg.com');  
			Security.allowDomain('i.ytimg.com');
			Security.allowDomain("http://slideslive.com");
			Security.allowDomain("http://www.slideslive.com");  
			Security.allowDomain("www.slideslive.com");  
			Security.allowDomain("slideslive.com");
			Security.allowDomain("slideslive.com/data");
		}
		
		private function initTests(){
			if(PlayerValues.buildConfiguration==1){
				testSuite = new TestMain(this);
			}
		}
		
		private function initStage():void {
			stage.align = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;				
		}
		
		private function initErrorHandling():void
		{
			//First create empty player container
			//On top of it init error window			
			playerContainer = new MovieClip();
			addChild(playerContainer);
			errorWindow = new ErrorWindow();
			addChild(errorWindow);
			errorController = new ErrorController(errorWindow);	
		}	
		
		public function initData():void{
			//Init values and call datareader, than wait for event of finish
			playerValues = new PlayerValues();
			
			var flashVarsParameters:Object = LoaderInfo(this.root.loaderInfo).parameters;
			
			var dataReader:DataReader = new DataReader(errorController,playerValues,flashVarsParameters);;
			dataReader.addEventListener(GeneralEvents.DATAREADERDONE, initGUI);
			dataReader.readInitialData();	
		}
		
		public function initGUI(e:GeneralEvents):void{
			//Data were completed and error handler will take debug value
			errorController.isDebugMode = playerValues.isDebugMode();
			
			if(PlayerValues.buildConfiguration==1) testSuite.testReadedData(playerValues);		
			playerGUI = new PlayerGUI(playerValues,errorController);
			playerGUI.addEventListener(GeneralEvents.GUIREADY, initPresentation);
			addChild(playerGUI);
			//This will add error window on top
			this.setChildIndex(playerGUI, 0);
			playerGUI.initGUI();
		}
		
		public function initPresentation(e:GeneralEvents){
			PlayerOutput.printLog("ATTEMPT TO INIT PRESENTATION");
			presentationController = new PresentationController(playerValues,playerGUI,errorController, stage);
			presentationController.loadPresentation();
		}
		
		
		
	}
}