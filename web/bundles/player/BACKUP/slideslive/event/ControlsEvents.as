package slideslive.event
{
	import flash.events.Event;
	
	public class ControlsEvents extends Event
	{
		public static const NEXTSLIDE:String = "NextSlide";
		public static const PREVSLIDE:String = "PreviousSlide";
		public static const OPENBIGSLIDE:String = "OpenBigSlide";
		public static const JUMP:String = "JumpToTime";
		public static const SYNCHRONIZESLIDES:String = "SynchronizeSlides";
		public static const PLAYBTN:String = "PlayBtn";
		public static const FULLSCREEN:String = "FullScreen";
		public static const VOLUME:String = "ChangeVolume";
		public static const VIDEOSEEK:String = "VideoSeek";
		public static const SLIDESLIVELOGO:String = "Slides live logo link";
		public var numberData:Number;
		
		public function ControlsEvents(controlsEventString:String, numberData:Number=-1) {
			super(controlsEventString, true, false);
			this.numberData = numberData;
		}
	}
}