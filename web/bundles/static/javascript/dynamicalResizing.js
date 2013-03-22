// JavaScript Document
window.setInterval(dummyFce, 200);
var currentSize = 10;

function yourfunction() { 
	currentSize += 10;
	printDebug(currentSize);
	$('#blockForResize').css({ width: currentSize, height: '300px' });
}


function dummyFce() {
	
}

function sendData(val){
	$('#blockForResize').css({ height: val });
	printDebug(val);	
}