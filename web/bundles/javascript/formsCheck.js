function initLoginFormCheck(){
	console.log("Runtime form checking init");
	$("#username").blur(function(e) {
		if(!isValidEmail(e.target.value)){
			$('#username').css("border-color", "#CC9900");		
			$('label[for=username]').html('You should enter your email ;)\<br \/\>');			
			$('label[for=username]').css('color','#CC9900');						
		} else {
			$('#username').css("border-color", "#000");		
			$('label[for=username]').html('Username<br \/\>');						
			$('label[for=username]').css('color','#FFF');			
		}
	});
}

function isValidEmail(emailString){
	var regExpression = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return regExpression.test(emailString);	
}