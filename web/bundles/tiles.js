 $(document).ready(function() {
   console.log("jes");
 });
 

function presentationObject(presentationName,presentationImage)
{
	this.presentationName=presentationName;
	this.presentationImage=presentationImage;
}

var presentations = new Array();


var elementsString = "";

function createElements(){
	loadPresentationData();
	
	for (var i=0; i<presentations.length; i++){
		elementsString += "<div class=\"box_1_1\" id=\""+presentations[i].presentationName+"\" style=\"background-color:\#"+Math.floor(Math.random()*16777215).toString(16)+"\"></div>\n";
	}	
	
	console.log(elementsString);
	$('#boxWrapper').html(elementsString);
}

function loadPresentationData(){
	createRandomPresentations();
	listPresentations();	
}

//BIG FAKE PART -----------------------------------------------------------------
//Debug functions
function createRandomPresentations(){
	for (var i=0; i<6; i++){
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
 
 