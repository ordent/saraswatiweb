$(function() {

    if(window.location.href.indexOf("adminuser_levels") > -1) {

      /* submit */
      $(".form-adminuser-levels").validate({
          onkeyup:false,
          onclick:false,
          rules: {
            'title':{
              required: true,
            },
            'ref2':{
              required:true
            },
          },
          messages: {
              'title': {
                  required: "Required",
              },
              'ref3':{
                required: "Required"
              },
        },
        submitHandler: function(form) {
              $('.loading').remove();
              loading = "<div class='loading'></div>";
              $('.form-adminuser-levels .navigator').append(loading);
              $('.form-adminuser-levels .btn-save').hide();
              $('.form-adminuser-levels .btn-cancel').hide();
              $('.form-adminuser-levels .btn-more').hide();
              form.submit();
        }

       });

    }
});