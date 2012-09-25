package slideslive.gui.slider
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.geom.Rectangle;
	import flash.utils.setTimeout;	
	
	import slideslive.event.GeneralEvents;

	public class Slider extends MovieClip
	{
		
		private var trackWidth:Number;
		private var boundsRectangle:Rectangle;
		
		private var sliderSeeker:SLIDERSeeker;
		private var sliderBar:SLIDERBar;
		
		private var eventCounter:int; //Because we do not want to fire event hundereds times per second
		
		private var minValue:Number;
		private var maxValue:Number;
		private var balancedPosition:Number;
		
		public function Slider(minValue:Number, maxValue:Number, balancedPosition:Number=0) 
		{			
			this.balancedPosition = balancedPosition;
			this.minValue = minValue;
			this.maxValue = maxValue;
		}
		
		public function initSlider(){
			sliderSeeker = new SLIDERSeeker();
			sliderBar = new SLIDERBar();
			var sliderBalance:SLIDERBalance = new SLIDERBalance();			
			
			addChild(sliderBar);
			addChild(sliderSeeker);
			addChild(sliderBalance);

			//Init variables
			trackWidth = sliderBar.width;
			boundsRectangle = new Rectangle(0,0,trackWidth+1,0);		 //+1 is because of strange pixel behavior at the end
			
			//Calculate balanced position, where seeker sticks to
			balancedPosition = trackWidth*(balancedPosition/((maxValue-minValue)+minValue));
			sliderBalance.x = balancedPosition;			
			sliderSeeker.x = balancedPosition;

			//Init listeners
			sliderSeeker.addEventListener(MouseEvent.MOUSE_DOWN, initDrag);
			this.addEventListener(MouseEvent.MOUSE_UP, terminateDrag);
			stage.addEventListener(MouseEvent.MOUSE_UP, terminateDrag);
			stage.addEventListener(MouseEvent.ROLL_OUT, terminateDrag);
			
			var calculatedValue:Number = (Math.round((sliderSeeker.x / trackWidth)*1000)/1000)*((maxValue-minValue)+minValue);
			eventCounter = 0;
			dispatchEvent(new GeneralEvents("SliderMoved",calculatedValue));			
		}
		
		private function initDrag(e:MouseEvent):void {
			sliderSeeker.startDrag(false, boundsRectangle);	
			sliderSeeker.addEventListener(MouseEvent.MOUSE_MOVE, onSliderMove);
			stage.addEventListener(MouseEvent.MOUSE_MOVE, onSliderMove);			
		}
		
		private function onSliderMove(e:MouseEvent):void {
			eventCounter++;		
			holdToImportantPoints();
			if(eventCounter == 3){
				var calculatedValue:Number = (Math.round((sliderSeeker.x / trackWidth)*1000)/1000)*((maxValue-minValue)+minValue);
				dispatchEvent(new GeneralEvents("SliderMoved",calculatedValue));
				eventCounter = 0;
			}
		}
		
		private function holdToImportantPoints(){
			if(Math.abs(balancedPosition - sliderBar.mouseX) < 15) sliderSeeker.x = balancedPosition;
		}
		
		private function terminateDrag(e:MouseEvent):void {
			sliderSeeker.removeEventListener(MouseEvent.MOUSE_MOVE, onSliderMove);
			stage.removeEventListener(MouseEvent.MOUSE_MOVE, onSliderMove);	
			sliderSeeker.stopDrag();	
		}		
	}
}