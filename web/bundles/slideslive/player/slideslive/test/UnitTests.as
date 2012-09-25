package slideslive.test
{
	import slideslive.util.PlayerOutput;

	public class UnitTests
	{
		public function UnitTests()
		{
			
			
		}
		
		public function assertEquals(var1:*, var2:*):Boolean{
			if (var1 is Number && var2 is Number){
				return compareValues(var1,var2);
			} else if(var1 is int && var2 is int){
				return compareValues(var1,var2);
			} else if(var1 is String && var2 is String){
				return compareStrings(var1,var2);	
			} else if(var1 is Boolean && var2 is Boolean){
				return compareBool(var1,var2);					
			} else {
				trace("AssertEquals failed, unknown types");
				return false;
			}
		}
		
		public function compareValues(var1:*, var2:*):Boolean{
			if(var1==var2) return true;
			else {
				PlayerOutput.printError("Equals failed - expected: "+var1+" actual: "+var2);
				return false;			
			}
		}
		
		public function compareStrings(var1:String, var2:String):Boolean{
			if(var1==var2) return true;
			else {
				PlayerOutput.printError("Equals failed - expected: "+var1+" actual: "+var2);
				return false;			
			}
		}
		
		public function compareBool(var1:Boolean, var2:Boolean):Boolean{
			if(var1 && var2) return true;
			else {
				PlayerOutput.printError("Equals failed - expected: "+var1+" actual: "+var2);
				return false;			
			}
		}
		
		
	}
}