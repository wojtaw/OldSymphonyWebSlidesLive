package slideslive.gui
{
	import fl.containers.UILoader;
	
	import flash.display.Bitmap;
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.Event;
	import flash.events.IOErrorEvent;
	import flash.events.ProgressEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.URLRequest;
	import flash.net.navigateToURL;
	
	import slideslive.error.ErrorHandler;
	import slideslive.util.ErrorCodes;

	public class SlidesContainer extends MovieClip
	{
		public var slideLoader1:UILoader = new UILoader();
		public var slideLoader2:UILoader = new UILoader();
		public var slideLoader3:UILoader = new UILoader();
		public var slideLoader4:UILoader = new UILoader();
		public var slideLoader5:UILoader = new UILoader();
		
		private var error:ErrorHandler;			
		
		
		public function SlidesContainer(error:ErrorHandler)
		{
			this.error = error;
			initSlideContainer()
		}
		
		public function initSlideContainer():void{
			var square:Sprite = new Sprite();
			square.graphics.beginFill(0x0099FF);
			square.graphics.drawRect(0,0,408,305);
			square.graphics.endFill();	
			addChild(square);
			initLoaders();
			initListeners();			
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
			
			slideLoader2.setSize(this.width, this.height);
			slideLoader2.scaleContent = true;
			slideLoader2.maintainAspectRatio = true;
			slideLoader2.move(0, 0);
			
			slideLoader3.setSize(this.width, this.height);
			slideLoader3.scaleContent = true;
			slideLoader3.maintainAspectRatio = true;
			slideLoader3.move(0, 0);
			
			slideLoader4.setSize(this.width, this.height);
			slideLoader4.scaleContent = true;
			slideLoader4.maintainAspectRatio = true;
			slideLoader4.move(0, 0);
			
			slideLoader5.setSize(this.width, this.height);
			slideLoader5.scaleContent = true;
			slideLoader5.maintainAspectRatio = true;
			slideLoader5.move(0, 0);
			
			addChild(slideLoader1);
			addChild(slideLoader2);
			addChild(slideLoader3);
			addChild(slideLoader4);
			addChild(slideLoader5);
		}
		
		public function loadSlide(pathToFile:String,loaderNumber:int):void
		{
			trace("loading slide");
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
	}
}