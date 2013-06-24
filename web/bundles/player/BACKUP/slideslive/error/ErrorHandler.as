package slideslive.error
{
	public interface ErrorHandler
	{
		function handleError(error:String, aditionalMessage:String=""):void;		
	}
}