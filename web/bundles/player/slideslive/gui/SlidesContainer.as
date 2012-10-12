package slideslive.gui
{
	import com.greensock.*;
	
	import fl.containers.UILoader;
	
	import flash.display.Bitmap;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.events.SecurityErrorEvent;
	import flash.events.TimerEvent;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	import flash.utils.Timer;
	
	import slideslive.error.ErrorHandler;
	import slideslive.util.ErrorCodes;
	import slideslive.values.PlayerValues;	

	public class SlidesContainer extends MovieClip
	{
		public var slideLoader1:UILoader = new UILoader();
		public var slideLoader2:UILoader = new UILoader();
		public var slideLoader3:UILoader = new UILoader();
		public var slideLoader4:UILoader = new UILoader();
		public var slideLoader5:UILoader = new UILoader();
		private var toastWindow:ToastWindow;
		private var toastTimer:Timer = new Timer(2000,1);
		private var slideNumbers:SlideNumbers;	
		
		private var error:ErrorHandler;			
		private var currentWidth:Number;
		private var currentHeight:Number;
		private var resizableWrapper:MovieClip;
		
		
		public function SlidesContainer(error:ErrorHandler)
		{
			this.error = error;
			initSlideContainer()
		}
		
		public function setSize(newWidth:int, newHeight:int){
			currentWidth = newWidth;
			currentHeight = newHeight;
			resizableWrapper.width = currentWidth;
			resizableWrapper.height = currentHeight;
			centerToast();
			redrawSlidesNumberPosition();
		}
		
		public function getWidth():Number {
			return currentWidth;
		}
		
		public function getHeight():Number {
			return currentHeight;
		}		
		
		public function initSlideContainer():void{
			initLoaders();
			initToastWindow();
			initSlideNumbers();
			initListeners();			
		}
		
		private function initSlideNumbers():void
		{
			slideNumbers = new SlideNumbers();	
			slideNumbers.y = 15;
			addChild(slideNumbers);
			redrawSlidesNumberPosition();			
		}
		
		private function redrawSlidesNumberPosition():void{
			slideNumbers.slideNumberMove.CurrNumber.x = 2;
			slideNumbers.slideNumberMove.numbersSlash.x = slideNumbers.slideNumberMove.CurrNumber.width - 2;
			slideNumbers.slideNumberMove.TotalNumber.x = slideNumbers.slideNumberMove.CurrNumber.width + 5;
			slideNumbers.x = currentWidth - slideNumbers.slideNumberMove.width - 3;
		}
		
		public function changeSlideNumbers(currentSlide:int, totalSlides:int):void {
			//Decide hov to move with text fields
			if(currentSlide < 9) slideNumbers.slideNumberMove.CurrNumber.width = 15;
			else if(currentSlide < 99) slideNumbers.slideNumberMove.CurrNumber.width = 23;
			else slideNumbers.slideNumberMove.CurrNumber.width = 30;
			
			if(totalSlides < 9) slideNumbers.slideNumberMove.TotalNumber.width = 15;
			else if(totalSlides < 99) slideNumbers.slideNumberMove.TotalNumber.width = 23;
			else slideNumbers.slideNumberMove.TotalNumber.width = 32;			
			
			redrawSlidesNumberPosition();
			slideNumbers.slideNumberMove.TotalNumber.text = totalSlides;
			slideNumbers.slideNumberMove.CurrNumber.text = (currentSlide + 1);
		}
		
		
		private function centerToast():void {
			if(currentWidth < toastWindow.width){
				toastWindow.visible = false;
			} else {
				toastWindow.visible = true;
			}
			toastWindow.x = 15;
			toastWindow.y = 15;
		}
		
		private function initToastWindow():void
		{
			toastWindow = new ToastWindow();
			toastWindow.alpha = 0;
			addChild(toastWindow);
		}		
		
		
		private function initListeners():void{
			slideLoader1.addEventListener(Event.COMPLETE, onLoaderComplete);
			slideLoader2.addEventListener(Event.COMPLETE, onLoaderComplete);
			slideLoader3.addEventListener(Event.COMPLETE, onLoaderComplete);
			slideLoader4.addEventListener(Event.COMPLETE, onLoaderComplete);
			slideLoader5.addEventListener(Event.COMPLETE, onLoaderComplete);
			
			//Error handling
			slideLoader1.addEventListener(IOErrorEvent.IO_ERROR, onLoaderError);
			slideLoader2.addEventListener(IOErrorEvent.IO_ERROR, onLoaderError);
			slideLoader3.addEventListener(IOErrorEvent.IO_ERROR, onLoaderError);
			slideLoader4.addEventListener(IOErrorEvent.IO_ERROR, onLoaderError);
			slideLoader5.addEventListener(IOErrorEvent.IO_ERROR, onLoaderError);		
		}
		
		private function onLoaderError(error_Event:IOErrorEvent):void {
			error.handleError(ErrorCodes.IMAGE_NOT_FOUND, "Slides images were not found");
		}
			
		//This will smooth loaded image
		private function onLoaderComplete(e:Event):void 
		{
			if(e.currentTarget.content is Bitmap)
			{
				Bitmap(e.currentTarget.content).smoothing = true;
			}
		}	
		
		private function initLoaders():void {
			slideLoader1.setSize(this.width, this.height);
			slideLoader1.scaleContent = true;
			slideLoader1.maintainAspectRatio = true;
			slideLoader1.move(0, 0);
			slideLoader1.width = 40;
			slideLoader1.height = 30;
			
			slideLoader2.setSize(this.width, this.height);
			slideLoader2.scaleContent = true;
			slideLoader2.maintainAspectRatio = true;
			slideLoader2.move(0, 0);
			slideLoader2.width = 40;
			slideLoader2.height = 30;			
			
			slideLoader3.setSize(this.width, this.height);
			slideLoader3.scaleContent = true;
			slideLoader3.maintainAspectRatio = true;
			slideLoader3.move(0, 0);
			slideLoader3.width = 40;
			slideLoader3.height = 30;			
			
			slideLoader4.setSize(this.width, this.height);
			slideLoader4.scaleContent = true;
			slideLoader4.maintainAspectRatio = true;
			slideLoader4.move(0, 0);
			slideLoader4.width = 40;
			slideLoader4.height = 30;			
			
			slideLoader5.setSize(this.width, this.height);
			slideLoader5.scaleContent = true;
			slideLoader5.maintainAspectRatio = true;
			slideLoader5.move(0, 0);
			slideLoader5.width = 40;
			slideLoader5.height = 30;			

			resizableWrapper = new MovieClip();
			//This is because UILoader only without graphic is causing strange stretching		
			var tmpGraphic:Sprite = new Sprite();
			tmpGraphic.graphics.beginFill(0x009999);
			tmpGraphic.graphics.drawRect(0,0,40,30);
			tmpGraphic.graphics.endFill();	
			tmpGraphic.alpha = 0;
			resizableWrapper.addChild(tmpGraphic);
			
			resizableWrapper.addChild(slideLoader1);
			resizableWrapper.addChild(slideLoader2);
			resizableWrapper.addChild(slideLoader3);
			resizableWrapper.addChild(slideLoader4);
			resizableWrapper.addChild(slideLoader5);
					
			this.addChild(resizableWrapper);
			
		}
		
		public function loadSlide(pathToFile:String,loaderNumber:int):void
		{
			if(loaderNumber == 1){
				slideLoader1.source = pathToFile;
			}else if(loaderNumber == 2){
				slideLoader2.source = pathToFile;				
			}else if(loaderNumber == 3){
				slideLoader3.source = pathToFile;				
			}else if(loaderNumber == 4){
				slideLoader4.source = pathToFile;				
			}else if(loaderNumber == 5){
				slideLoader5.source = pathToFile;				
			}	
		}
		
		public function displayLoader(loaderNumber:int):void
		{
			slideLoader1.visible = false;
			slideLoader2.visible = false;
			slideLoader3.visible = false;
			slideLoader4.visible = false;
			slideLoader5.visible = false;
			
			if(loaderNumber == 1){
				slideLoader1.visible = true;
			}else if(loaderNumber == 2){
				slideLoader2.visible = true;				
			}else if(loaderNumber == 3){
				slideLoader3.visible = true;				
			}else if(loaderNumber == 4){
				slideLoader4.visible = true;				
			}else if(loaderNumber == 5){
				slideLoader5.visible = true;				
			}	
			
		}
		
		private function showToastWindow():void {
			toastWindow.alpha = 1;
			if(toastTimer != null) toastTimer.stop();
			toastTimer = new Timer(3000,1);
			toastTimer.addEventListener(TimerEvent.TIMER, toastFinish);
			toastTimer.start();			
		}
		
		private function toastFinish(e:TimerEvent):void{
			toastTimer.stop();
			//toastWindow.alpha = 0;
			TweenLite.to(toastWindow, 1, {alpha:0});
		}			
		
		public function displayRedToast():void{
			toastWindow.toastField.text = "Slides not synced";	
			toastWindow.toastField.textColor = 0xFF2027;
			showToastWindow();
		}
		
		
		public function displayGreenToast():void{
			toastWindow.toastField.text = "Slides in sync";
			toastWindow.toastField.textColor = 0x43D73A;
			showToastWindow();
		}
		
		public function hideSlideNumbers():void {
			slideNumbers.visible = false;
		}
		
		
	}
}