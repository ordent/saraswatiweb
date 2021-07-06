$(function() {

    var baseURL = $('body').data('url');

    // form login
    $(".form-login").validate({
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
          'password':{
            required:true
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
            error.insertAfter(".reg-email");
          }
          if (element.attr("name") == "password" ){
            error.insertAfter(".reg-password");
          }
      },
      submitHandler: function(form) {

        var $form = $( '.form-login' ),
            url = $form.attr( 'action' );

            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.navigator-login').append(loading);
            $('.btn-login').hide();

            $.ajax({
              type: "POST",
              url:url,
              data: $(form).serialize(),
              dataType: 'json',
              success: function(response, text) {

                $('.loading').remove();
                $('.btn-login').show();

                if(response.status == 0){
                  $('.loading').remove();
                  window.location.href=baseURL+"index";
                }else{
                  $('.loading').remove();
                  alert(response.message);
                }
              },
              error: function(){
                $('.loading').remove();
                $('.btn-login').show();
                // alert("Sistem gagal melakukan proses karena masalah server atau hal lain. Silahkan coba lagi")
              }
            });
      }

     });

});