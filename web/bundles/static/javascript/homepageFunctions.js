// JavaScript Document
 $(document).ready(function() {
   runQuotesTimelapse();
 });

function showEmbedPlayer(){
	console.log("Hello world");
	$('.embedPlayer').css("visibility", "visible");	
}

function runQuotesTimelapse(){
	console.log("quotes changing");	
	setInterval(function(){changeQuote()},3000);
}

function changeQuote(){
	console.log("quotes changing");
}

