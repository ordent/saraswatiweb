$(function() {

  if(window.location.href.indexOf("ref_blogs") > -1) {

    /* submit */
    $(".form-ref-blogs").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'ref2':{
            required:true
          },
        },
        messages: {
            'name': {
                required: "Required",
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-ref-blogs .navigator').append(loading);
            $('.form-ref-blogs .btn-save').hide();
            $('.form-ref-blogs .btn-cancel').hide();
            $('.form-ref-blogs .btn-more').hide();
            $form.submit();
      }

     });

  }
 
});