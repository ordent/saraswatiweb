$(function() {

  if(window.location.href.indexOf("what_we_do_quotes") > -1) {

    /* submit */
    $(".form-what-we-do-quotes").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'description':{
            required:true
          },
          'ref3':{
            required:true
          },
          'ref2':{
            required:true
          },
        },
        messages: {
            'name': {
                required: "Required",
            },
            'description': {
                required: "Required",
            },
            'ref3':{
              required: "Required",
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-what-we-do-quotes .navigator').append(loading);
            $('.form-what-we-do-quotes .btn-save').hide();
            $('.form-what-we-do-quotes .btn-cancel').hide();
            $('.form-what-we-do-quotes .btn-more').hide();
            $form.submit();
      }

     });

  }
 
});