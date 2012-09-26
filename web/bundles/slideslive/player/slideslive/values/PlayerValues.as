package slideslive.values
{
	public class PlayerValues
	{
		//Configuration
		public static const buildConfiguration:int = 3; //1 - local HDD testing, 2 - local HDD anything, 3 - localhost, 4 - production
		private var isDebug:Boolean = false;
		
		//Paths
		private var pathToXMLStorage:String;
		private var pathToImages:String;
		
		//Slides records
		private var slidesRecords:Array = new Array();
		
		//Important values
		public var playerStageWidth:int = 960;
		public var playerStageHeight:int = 400;
		
		//FlashVars parameters
		private var videoAvailable:Boolean;
		private var slideAvailable:Boolean;
		private var presentationID:int;
		private var presentationModule:String;
		private var presentationParameter:String;
		private var isEmbed:Boolean=false;
		
		//GUI values
		public var colorOfControls:Number = 0x552288;
		public var colorOfControlsDisabled:Number = 0xCCCCCC;
		public var colorOfBarUnder:Number = 0x0033CC;
		public var colorBgSlides:Number = 0x1E1E1E;	
		public var colorOfStage:Number = 0x009900;
		public var colorOfSeeker:Number = 0x000000;
		public var colorOfBarPlayed:Number = 0x333333;
		public var controlsAnimationLenght:Number = 1.0;
		
		
		
		
		public function PlayerValues()
		{
			
		}
		
		public function isVideoAvailable():Boolean
		{
			return videoAvailable;
		}
		
		public function setVideoAvailable(value:Boolean):void
		{
			videoAvailable = value;
		}			
		
		public function isSlideAvailable():Boolean
		{
			return slideAvailable;
		}
		
		public function setEmbed(value:Boolean):void
		{
			isEmbed = value;
		}			
		
		public function isEmbedded():Boolean
		{
			return isEmbed;
		}		
		
		public function setSlideAvailable(value:Boolean):void
		{
			slideAvailable = value;
		}			
		
		public function getPresentationID():int
		{
			return presentationID;
		}
		
		public function setPresentationID(value:int):void
		{
			presentationID = value;
		}			
		
		public function getPresentationModule():String
		{
			return presentationModule;
		}
		
		public function setPresentationModule(value:String):void
		{
			presentationModule = value;
		}		
		
		
		public function getPresentationParameter():String
		{
			return presentationParameter;
		}

		public function setPresentationParameter(value:String):void
		{
			presentationParameter = value;
		}
		
		public function getPathToImages():String
		{
			return pathToImages;
		}
		
		public function setPathToImages(value:String):void
		{
			pathToImages = value;
		}
		
		public function getPathToXMLStorage():String
		{
			return pathToXMLStorage;
		}
		
		public function setPathToXMLStorage(value:String):void
		{
			pathToXMLStorage = value;
		}
		
		public function isDebugMode():Boolean
		{
			return isDebug;
		}
		
		public function setDebugMode(value:Boolean):void
		{
			isDebug = value;
		}		
		
		public function addSlideRecord(slideRecord:SlideRecord):Boolean{
			slidesRecords.push(slideRecord);
			return true;
		}
		
		public function getSlidesRecords():Array{
			return slidesRecords;
		}
				

	}
}