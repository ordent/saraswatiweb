$(function() {

if(window.location.href.indexOf("menu_auths") > -1) {

    /* submit */
    $(".form-menu-auths").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'ref2':{
            required:true
          }
        },
        messages: {
            'ref2':{
              required: "Required"
            }
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-menu-auths .navigator').append(loading);
            $('.form-menu-auths .btn-save').hide();
            $('.form-menu-auths .btn-cancel').hide();
            $('.form-menu-auths .btn-more').hide();
            $form.submit();
      }

     });


    /* Edit */
    if($('#id').val()){

      /*ajax menu*/
      $('select#ref2').change(function() {

        var ajaxParm1 = $(this).val() == "" ? 0 : $(this).val();
        $.get(window.adminURL+'menu_auths/ajaxRequest1/'+ajaxParm1, function(data) {
            $('#ajax1').html(data);
            $("#ref1").addClass("span8");
            $("#ref1").select2();
          });
          return false;
      });

    }

     /* List */
    if(!$('#id').val()){
      $("select").addClass("span3");
      $("select").select2();
	  }
}
 
});