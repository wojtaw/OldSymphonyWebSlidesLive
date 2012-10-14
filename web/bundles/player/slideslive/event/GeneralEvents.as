package slideslive.event
{	
	import flash.events.Event;
	
	public class GeneralEvents extends Event
	{
		
		public static const DATAREADERDONE:String = "Data init successfull";
		public static const GUIREADY:String = "GUI is ready";
		public static const SLIDERMOVE:String = "SliderMoved";
		public static const YT_MODULE_READY:String = "Youtube module ready";
		public static const SLIDEQUALITY:String = "Slide quality";
		public var data:Number;
		
		public function GeneralEvents(videoEventString:String, data:Number=-1)
		{
			super(videoEventString, true, false);			
			this.data = data;
		}
	}	
}