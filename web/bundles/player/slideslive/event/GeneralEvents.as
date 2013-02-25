package slideslive.event
{	
	import flash.events.Event;
	
	public class GeneralEvents extends Event
	{
		
		public static const DATAREADERDONE:String = "Data init successfull";
		public static const GUIREADY:String = "GUI is ready";
		public static const SLIDERMOVE:String = "SliderMoved";
		public static const VIDEO_MODULE_READY:String = "Video module ready";
		public static const VIMEO_SPECIFIC_READY:String = "VimeoMoogaReady";
		public static const SLIDEQUALITY:String = "Slide quality";
		public static const BUYDONE:String = "Buy Dialog finished";
		public static const NOTETAKING:String = "User is writing note";
		public static const ADDNOTE:String = "Send request for note add in database";	
		public var data:Number;
		public var data2:String;
		
		public function GeneralEvents(videoEventString:String, data:Number=-1, data2:String="")
		{
			super(videoEventString, true, false);			
			this.data = data;
			this.data2 = data2;
		}
	}	
}