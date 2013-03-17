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
			dispatchEvent(new GeneralEvents("Data init successfull"));
		}
		
		//1 - local HDD testing, 2 - local HDD anything, 3 - localhost, 4 - production
		private function loadHTMLParameters():Boolean
		{
			//playerValues.playerAPIUserAuth = "http://localhost/SlidesLive/web/app_dev.php/player_api/user_auth";
			if(PlayerValues.buildConfiguration == 1){
				playerValues.setSessionID(38889130);
				playerValues.setLivestreamModule("YOUTUBE");
				playerValues.setLivestreamParameter("37P3zrajCy0");
				playerValues.setSlideAvailable(true);
				playerValues.setVideoAvailable(true);
				playerValues.setEmbed(true);
				playerValues.setPaid(false);
				
				playerValues.setPathToImages("SAMPLE_DATA/presentationImages/");
				playerValues.setPathToXMLStorage("SAMPLE_DATA/XMLstorage/");
				playerValues.setPathToWebsiteWatch("http://slideslive.com/w/");
				playerValues.setPathToStreamingSession("http://slideslive.com/w/");					
				
			}else if(PlayerValues.buildConfiguration == 2){
				playerValues.setDebugMode(false);
				playerValues.setSessionID(12345);
				playerValues.setLivestreamModule("YOUTUBE");
				//playerValues.setPresentationParameter("54191131");
				playerValues.setLivestreamParameter("tmO0o4p884w");
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
				//playerValues.setPathToStreamingSession("http://localhost/SlidesLive/data/Streaming/sessions/");
				playerValues.setPathToStreamingSession("http://slideslive.com/data/Streaming/sessions/");
				
				playerValues.playerAPIUserAuth = "http://localhost/SlidesLive/web/app_dev.php/player_api/user_auth";
				playerValues.playerAPIAddNote = "http://localhost/SlidesLive/web/app_dev.php/player_api/add_note";				
				
			}else if(PlayerValues.buildConfiguration == 3){
				playerValues.setDebugMode(false);
				
				playerValues.setSessionID(flashVarObject["sessionID"]);
				playerValues.setLivestreamModule(flashVarObject["streamType"]);
				playerValues.setLivestreamParameter(flashVarObject["streamID"]);
				
				//Embed is optional
				if(flashVarObject["isEmbed"] == "true") playerValues.setEmbed(true);
				else if(flashVarObject["isEmbed"] == "false") playerValues.setEmbed(false);				
				
				//This is legacy, set by default
				playerValues.setSlideAvailable(true);
				playerValues.setVideoAvailable(true);

			
				
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
				playerValues.setPathToStreamingSession("http://slideslive.com/data/Streaming/sessions/");				
				
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