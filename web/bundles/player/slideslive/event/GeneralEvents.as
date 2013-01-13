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
		public var data:Number;
		
		public function GeneralEvents(videoEventString:String, data:Number=-1)
		{
			super(videoEventString, true, false);			
			this.data = data;
		}
	}	
}