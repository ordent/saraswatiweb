$(function() {

	var baseURL = $('body').data('url');

	$(".form-forgot").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'email':{
            required: true,
            email: true,
            remote:{ 
              url: baseURL+'adminuser_auths/do_check_email_exists',
              type: "post",
            }
          },
        },
        messages: {
            'email': {
                required: "Email required",
                email: "Invalid Email Format",
                remote: "Your email not registered, please try another email"
            },
            'password':{
              required: "Password required"
            },
      },
      errorPlacement: function(error, element) {
          if (element.attr("name") == "email" ){
            error.insertAfter(".email");
          }
      },
      submitHandler: function(form) {

        var $form = $( '.form-forgot' ),
            url = $form.attr( 'action' );

            loading = "<div class='loading'></div>";
            $('.navigator-forgot').append(loading);
            $('.btn-forgot').hide();

            $.ajax({
              type: "POST",
              url:url,
              data: $(form).serialize(),
              dataType: 'json',
              success: function(response, text) {

                $('.loading').remove();
                $('.btn-close').show();

                if(response.status == 0){
                  $.get(baseURL+"forgot/success", function(data, status){
                      $(".popup .module-body").html(data);
                  });
                }else{
                  $('.loading').remove();
                  alert(response.message);
                }
              },
              error: function(){
                $('.loading').remove();
                $('.btn-forgot').show();
                alert("something when wrong, please try again")
              }
            });
      }

     });

});