function loadSlide(path){
	console.log("loading slide "+path);
	modifiedUrl = "url("+path+")";
	$('#player5_slideLoader').css("background-image", modifiedUrl);
}