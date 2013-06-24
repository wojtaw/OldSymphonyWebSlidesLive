package slideslive.values
{
	public class SlideRecord
	{
		private var slideName:String;
		private var slideTime:Number;
		
		public function SlideRecord(slideName:String,sldTime:Number)
		{
			this.slideName = slideName;
			this.slideTime = sldTime;			
		}
		
		public function getSlideName():String {
			return slideName;
		}
		
		public function getSlideTime():Number {
			return slideTime;
		}			
	}
}