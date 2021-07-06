
$(function() {

  if(window.location.href.indexOf("adminuser_auths") > -1) {

    /* Submit */
    $(".form-adminuser").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'email':{
            required: true,
            email:true,
            remote:{ 
                      url: window.baseURL+'general/do_check_email',
                      data: { 
                              id: $('#id').val(),
                              table: $('#table').val()
                            },
                      type: "post"
                    },
          },
          'name':{
            required: true,
          },
          'ref2':{
            required:true
          },
        },
        messages: {
            'email': {
                required: "Required",
                email: "Not vaild format email",
                remote: "Email already esixts"
            },
            'name': {
                required: "Required",
            },
            'ref2': {
                required: "Required",
            },
            'ref3':{
              required: "Required"
            },
      },
      submitHandler: function(form) {
            $('.loading').remove();
            loading = "<div class='loading'></div>";
            $('.form-adminuser .navigator').append(loading);
            $('.form-adminuser .btn-save').hide();
            $('.form-adminuser .btn-cancel').hide();
            $('.form-adminuser .btn-more').hide();
            form.submit();
      }

     });

     /* edit */
    if($('#id').val()){
      var ref3Val = $("select[name='ref2']").val();
      if(ref3Val != 10){
        $(".brands").addClass("hide");
        $(".form-list #ref4").prop("disabled",true);
      }

      $('.form-adminuser #ref2').change(function(){
        if($(this).val() == 10){
            $('#ref4').prop("disabled",false);
            $(".brands").removeClass("hide");
          }else{
            $('#ref4').prop("disabled",true);
            $(".brands").addClass("hide");
          }
          $("select").select2();
          return false; 
      });
    }else{

      var ref4Val = $("select[name='ref4']").val();
      if(ref4Val == undefined || ref4Val == ""){
        $(".form-list #ref4").prop("disabled",true);
      }else{
        $(".form-list #ref4").prop("disabled",false);
      }

      $('.form-list #ref3').change(function(){
        if($(this).val() == 3){
            $('#ref4').prop("disabled",false);
          }else{
            $('#ref4').prop("disabled",true);
          }
          return false; 
      });

    }

  }
   
});
