$(function() {

if(window.location.href.indexOf("menus") > -1) {

    /* submit */
    $(".form-menus").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'title':{
            required: true,
          },
          'uri':{
            required: true,
          },
          'ref3':{
            required:true
          }
        },
        messages: {
            'title': {
                required: "Required",
            },
            'uri': {
                required: "Required",
            },
            'ref3':{
              required: "Required"
            }
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-menus .navigator').append(loading);
            $('.form-menus .btn-save').hide();
            $('.form-menus .btn-cancel').hide();
            $('.form-menus .btn-more').hide();
            $form.submit();
      }

     });

	/* sortable */    
	$('.list').sortable({

		start: function(event, ui) {
			ui.item.startPos = ui.item.index();
		},
		update: function(event, ui) {
			
			var i = window.pg;
			
			$.post(window.adminURL+"menus/ajaxsort", {
						data : $(this).sortable('toArray'),
						index_order : i
			}, function(data){
				timer = 1000;
				setTimeout("goto()",timer);
			});
			
			$(".list li" ).each(function() {
				i++;
				$(this).find("table tr td#numb").text(i);
			});
			
			
		}

	});

}
 
});