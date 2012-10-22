$(window).resize(resizePresentations); 
$(document).ready(resizePresentations);

//Objects and variables
var minimumTileWidth = 200;
var tileSpacing = 6;


//functions
function resizePresentations(){
		var containerWidth = $('#showcaseBar').width()-1;
		var numberOfPresentations = Math.floor(containerWidth / (tileSpacing + minimumTileWidth));
		var newTileWidth = Math.floor(containerWidth / numberOfPresentations - tileSpacing);
		console.log(newTileWidth);
		resizeTiles(newTileWidth);
}

function resizeTiles(newTileWidth){
	$('.tile_1_1').css("width", newTileWidth);
	$('.tile_1_1').css("height", newTileWidth);
	$('.tile_1_1').css("margin-right", tileSpacing);		
	$('.tile_1_1').css("margin-bottom", tileSpacing);			
}
 