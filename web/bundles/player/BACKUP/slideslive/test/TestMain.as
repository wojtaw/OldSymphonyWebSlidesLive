package slideslive.test
{
	import slideslive.Player_5_THC;
	import slideslive.util.PlayerOutput;
	import slideslive.values.PlayerValues;
	import slideslive.values.SlideRecord;

	public class TestMain extends UnitTests
	{
		private var playerMain:Player_5_THC;
		
		public function TestMain(playerMain:Player_5_THC)
		{
			this.playerMain = playerMain;
		}
		
		public function testReadedData(playerValues:PlayerValues):void{
			var success:int;
			var fails:int;
			
			PlayerOutput.printLog("-----------Test case----------");
			
			if(assertEquals(playerValues.getPresentationID(),38889130)) success++;
			else fails++;
			
			if(assertEquals(playerValues.getPathToXMLStorage(),"SAMPLE_DATA/XMLstorage/")) success++;
			else fails++;
			
			if(assertEquals(playerValues.getPresentationParameter(),"37P3zrajCy0")) success++;
			else fails++;
			
			if(assertEquals(playerValues.isSlideAvailable(),true)) success++;
			else fails++;
			
			if(assertEquals(playerValues.getSlidesRecords().length,79)) success++;
			else fails++;
			
			if(assertEquals(playerValues.getSlidesRecords()[78].getslideName(),"1-0100.png")) success++;
			else fails++;			
			PlayerOutput.printLog("-----------Test done - success: "+success+" fails: "+fails);			
		}
	}
}