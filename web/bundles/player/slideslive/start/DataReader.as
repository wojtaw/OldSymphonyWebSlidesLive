package slideslive.start
{
	import flash.display.MovieClip;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.net.URLLoader;
	import flash.net.URLRequest;
	
	import slideslive.error.ErrorHandler;
	import slideslive.event.GeneralEvents;
	import slideslive.util.ErrorCodes;
	import slideslive.util.PlayerOutput;
	import slideslive.values.PlayerValues;
	import slideslive.values.SlideRecord;
	

	public class DataReader extends MovieClip
	{
		private var error:ErrorHandler;
		private var playerValues:PlayerValues;
		private var XMLData:XML;		
		
		private var flashVarObject:Object;
		
		public function DataReader(error:ErrorHandler, playerValues:PlayerValues, flashVarObject:Object)
		{
			this.error = error;
			this.playerValues = playerValues;
			this.flashVarObject = flashVarObject;			
			PlayerOutput.printLog("INIT Data reader");
		}	
		
		public function readInitialData():void{
			if(!loadHTMLParameters()) error.handleError(ErrorCodes.GENERAL_ERROR, "Loading HTML parameters failed");
			if(!loadPresentationXML()) error.handleError(ErrorCodes.GENERAL_ERROR, "Data init failure");
		}
		
		private function loadPresentationXML():Boolean{
			var presentationXMLPath:String;
			if(playerValues.isSlideAvailable()){
				presentationXMLPath = playerValues.getPathToXMLStorage()+playerValues.getPresentationID()+".xml";
			}else{
				PlayerOutput.printLog("Slides are not available");
				presentationXMLPath = playerValues.getPathToXMLStorage()+"noslide.xml";
			}
			
			PlayerOutput.printLog("Path to presentation XML: "+presentationXMLPath);
			var xmlLoad:URLLoader = new URLLoader();	
			xmlLoad.addEventListener(IOErrorEvent.IO_ERROR, onIOErrorSlidesBroken);
			xmlLoad.addEventListener(Event.COMPLETE, presentationXMLComplete, false, 0, true);	
			xmlLoad.load(new URLRequest(presentationXMLPath));	
			return true;
		}
		
		private function onIOErrorSlidesBroken(e:IOErrorEvent):void
		{
			error.handleError(ErrorCodes.XML_PRESENTATION_FAIL, "Path was - "+e.text);
		}
		
		public function presentationXMLComplete(e:Event):void{
			try {
				XMLData = new XML(e.target.data);				
			} catch (err:Error) {
				error.handleError(ErrorCodes.XML_PRESENTATION_DATA_FAIL);
			}
			mountData();
			dispatchEvent(new GeneralEvents("Data init successfull"));
		}
		
		public function mountData(){
			PlayerOutput.printLog("Now mounting data");
			for (var i:int=0; i<XMLData.slide.slideName.length();i++){ 
				var tmpSlideName:String = XMLData.slide.(orderId == (i+1)).slideName;			
				var tmpSlideTime:Number = XMLData.slide.(orderId == (i+1)).timeSec;		
				playerValues.addSlideRecord(new SlideRecord(tmpSlideName,tmpSlideTime));				
			}	
		}
		
		//1 - local HDD testing, 2 - local HDD anything, 3 - localhost, 4 - production
		private function loadHTMLParameters():Boolean
		{
			//playerValues.playerAPIUserAuth = "http://localhost/SlidesLive/web/app_dev.php/player_api/user_auth";
			if(PlayerValues.buildConfiguration == 1){
				playerValues.setPresentationID(38889130);
				playerValues.setPresentationModule("YOUTUBE");
				playerValues.setPresentationParameter("37P3zrajCy0");
				playerValues.setSlideAvailable(true);
				playerValues.setVideoAvailable(true);
				playerValues.setEmbed(true);
				playerValues.setPaid(false);
				
				playerValues.setPathToImages("SAMPLE_DATA/presentationImages/");
				playerValues.setPathToXMLStorage("SAMPLE_DATA/XMLstorage/");
				playerValues.setPathToWebsiteWatch("http://slideslive.com/w/");
				
			}else if(PlayerValues.buildConfiguration == 2){
				playerValues.setDebugMode(false);
				playerValues.setPresentationID(38889365);
				playerValues.setPresentationModule("YOUTUBE");
				//playerValues.setPresentationParameter("54191131");
				playerValues.setPresentationParameter("HdRPBs7IfAE");
				playerValues.setSlideAvailable(true);
				playerValues.setVideoAvailable(true);
				playerValues.setEmbed(true);
				playerValues.setPaid(false);
				//playerValues.scaleToWidth = 1400;
				playerValues.setVideoSlideRatio(-1);
				playerValues.setZooming(true);
				playerValues.setStartSlide(1);
				
				playerValues.setPathToImages("SAMPLE_DATA/presentationImages/");
				playerValues.setPathToXMLStorage("SAMPLE_DATA/XMLstorage/");
				playerValues.setPathToWebsiteWatch("http://slideslive.com/w/");
				
				playerValues.playerAPIUserAuth = "http://localhost/SlidesLive/web/app_dev.php/player_api/user_auth";
				playerValues.playerAPIAddNote = "http://localhost/SlidesLive/web/app_dev.php/player_api/add_note";				
				
			}else if(PlayerValues.buildConfiguration == 3){
				playerValues.setDebugMode(false);
				
				playerValues.setPresentationID(flashVarObject["presentationID"]);
				playerValues.setPresentationModule(flashVarObject["mediaType"]);
				playerValues.setPresentationParameter(flashVarObject["mediaID"]);
				if(flashVarObject["hasSlides"] == "true") playerValues.setSlideAvailable(true);
				else if(flashVarObject["hasSlides"] == "false") playerValues.setSlideAvailable(false);
				else error.handleError(ErrorCodes.WRONG_FLASH_VARS);
				
				if(flashVarObject["hasVideo"] == "true") playerValues.setVideoAvailable(true);
				else if(flashVarObject["hasVideo"] == "false") playerValues.setVideoAvailable(false);
				else error.handleError(ErrorCodes.WRONG_FLASH_VARS);
				
				//Embed is optional
				if(flashVarObject["isEmbed"] == "true") playerValues.setEmbed(true);
				else if(flashVarObject["isEmbed"] == "false") playerValues.setEmbed(false);
				
				//paid is optional
				if(flashVarObject["isPaid"] == "true") playerValues.setPaid(true);
				else if(flashVarObject["isPaid"] == "false") playerValues.setPaid(false);				
				
				//Scaling
				if(flashVarObject["widthScale"] == null) playerValues.scaleToWidth = -1;
				else playerValues.scaleToWidth = flashVarObject["widthScale"];	
				
				//zooming is optional
				if(flashVarObject["zoomingOn"] == "true") playerValues.setZooming(true);
				else if(flashVarObject["zoomingOn"] == "false") playerValues.setZooming(false);	
				
				//VideoSlide ratio
				if(flashVarObject["videoSlideRatio"] == null) playerValues.setVideoSlideRatio(-1);
				else playerValues.setVideoSlideRatio(flashVarObject["videoSlideRatio"]);	
				
				//StartSlide
				if(flashVarObject["startSlide"] == null) playerValues.setStartSlide(1);
				else playerValues.setStartSlide(flashVarObject["startSlide"]);					
				
				playerValues.setPathToImages("http://www.slideslive.com/data/PresentationSlides/");
				playerValues.setPathToXMLStorage("http://www.slideslive.com/data/PresentationXMLs/");	
				playerValues.setPathToWebsiteWatch("http://slideslive.com/w/");
				
				playerValues.playerAPIUserAuth = "http://slideslive.com/SlidesLive_dev/web/app_dev.php/player_api/user_auth";
				playerValues.playerAPIAddNote = "http://slideslive.com/SlidesLive_dev/web/app_dev.php/player_api/add_note";				
				
			}else if(PlayerValues.buildConfiguration == 4){
				
			}else{
				error.handleError(ErrorCodes.INCORRECT_BUILD_CONFIGURATION);
			}
			
			return true;
		}
	}
}