$(function() {

  if(window.location.href.indexOf("ref_socials") > -1) {

    /* submit */
    $(".form-ref-socials").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'link':{
            required: true,
          },
          'class':{
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
            'link': {
                required: "Required",
            },
            'class': {
                required: "Required",
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-ref-socials .navigator').append(loading);
            $('.form-ref-socials .btn-save').hide();
            $('.form-ref-socials .btn-cancel').hide();
            $('.form-ref-socials .btn-more').hide();
            $form.submit();
      }

     });

  }
 
});