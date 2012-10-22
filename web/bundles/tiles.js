 $(document).ready(function() {
   console.log("jes");
 });
 
$(window).resize(resizePresentations); 
 
 

 
//Objects and variables
function presentationObject(presentationName,presentationImage)
{
	this.presentationName=presentationName;
	this.presentationImage=presentationImage;
}

var minimumTileWidth = 200;
var tileSpacing = 6;


var presentations = new Array();
var elementsString = "";


//functions
function resizePresentations(){
		var containerWidth = $('#boxWrapper').width()-1;
		var numberOfPresentations = Math.floor(containerWidth / (tileSpacing + minimumTileWidth));
		var newTileWidth = Math.floor(containerWidth / numberOfPresentations - tileSpacing);
		console.log(newTileWidth);
		resizeTiles(newTileWidth);
}

function resizeTiles(newTileWidth){
	$('.box_1_1').css("width", newTileWidth);
	$('.box_1_1').css("height", newTileWidth);
	$('.box_1_1').css("margin-right", tileSpacing);		
	$('.box_1_1').css("margin-bottom", tileSpacing);			
}


function createElements(){
	loadPresentationData();

	for (var i=0; i<presentations.length; i++){
		
		
		elementsString += "<div class=\"box_1_1\" id=\""+presentations[i].presentationName+"\" style=\"background-color:\#"+Math.floor(Math.random()*16777215).toString(16)+"\"></div>\n";
	}	
	
	console.log(elementsString);
		console.log($('#boxWrapper').width());
	$('#boxWrapper').html(elementsString);
}

function loadPresentationData(){
	createRandomPresentations();
	listPresentations();	
}

//BIG FAKE PART -----------------------------------------------------------------
//Debug functions
function createRandomPresentations(){
	for (var i=0; i<60; i++){
		tmpPresentation = presentationObject;
		presentations[i] = new presentationObject(makeName(), "prdel.jpg");
	}
}

function listPresentations(){
	for (var i=0; i<presentations.length; i++){
		console.log("Name: "+presentations[i].presentationName);
	}	
}

//Some stuff
function makeName() {
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}
 
 