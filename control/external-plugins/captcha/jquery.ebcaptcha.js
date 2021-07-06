(function($){

	jQuery.fn.ebcaptcha = function(options){

		var element = this; 
		var button = $(this).find('input[type=button]');
		$('<label id="ebcaptchatext"></label>').insertBefore(button);
		$('<input type="text" class="textbox" id="ebcaptchainput"/><br/><br/>').insertBefore(button);
		var input = this.find('#ebcaptchainput'); 
		var label = this.find('#ebcaptchatext'); 
		
		$(element).find('input[type=button]').attr('disabled','disabled'); 

		
		var randomNr1 = 0; 
		var randomNr2 = 0;
		var totalNr = 0;


		randomNr1 = Math.floor(Math.random()*10);
		randomNr2 = Math.floor(Math.random()*10);
		totalNr = randomNr1 + randomNr2;
		var texti = randomNr1+" + "+randomNr2;
		$(label).text(texti);
		
	
		$(input).keyup(function(){

			var nr = $(this).val();
			if(nr==totalNr)
			{
				$(element).find('input[type=button]').removeAttr('disabled');				
			}
			else{
				$(element).find('input[type=button]').attr('disabled','disabled');
			}
			
		});

		$(document).keypress(function(e)
		{
			if(e.which==13)
			{
				if((element).find('input[type=button]').is(':disabled')==true)
				{
					e.preventDefault();
					return false;
				}
			}

		});

	};

})(jQuery);