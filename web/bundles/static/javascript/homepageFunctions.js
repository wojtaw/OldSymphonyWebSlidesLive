// JavaScript Document
 $(document).ready(function() {
   modifyDownloadAccordingOS();
 });

function modifyDownloadAccordingOS(){
            $('#os').html("<b>" + $.client.os + "</b>");
            $('#browser').html("<b>" + $.client.browser + "</b>");
          
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#downloadButtonLink').attr("href", "http://slideslive.com/SlidesLiveData/SL_Recorder/SL_Windows.zip");												
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#downloadButtonLink').attr("href", "http://slideslive.com/SlidesLiveData/SL_Recorder/SL_Linux.zip");																				
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#downloadButtonLink').attr("href", "http://slideslive.com/SlidesLiveData/SL_Recorder/SlidesLive-Mac.dmg");
            } else {
                $('#downloadButtonLink').attr("href", "http://slideslive.com/SlidesLiveData/SL_Recorder/SL_AllPlatforms.zip");
            }
}

function showEmbedPlayer(){
	console.log("Hello world");
	$('.embedPlayer').css("visibility", "visible");	
	
}

