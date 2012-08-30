 $(document).ready(function() {
   modifyDownloadAccordingOSGeneral();
 });

function modifyDownloadAccordingOSGeneral(){
            $('#os').html("<b>" + $.client.os + "</b>");
            $('#browser').html("<b>" + $.client.browser + "</b>");
          
            if ($.client.os.toLowerCase().substring(0,3) == "win") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Windows.zip");													
            } else if ($.client.os.toLowerCase().substring(0,3) == "lin") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_Linux.zip");																				
            } else if ($.client.os.toLowerCase().substring(0,3) == "mac") {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SlidesLive-Mac.dmg");																
            } else {
                $('#downloadButtonLink2').attr("href", "http://slideslive.com/data/SL_Recorder/SL_AllPlatforms.zip");																		
            }
}