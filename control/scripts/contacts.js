   $(function() {

    if(window.location.href.indexOf("contacts") > -1) {

      /* submit */
    $(".form-contacts").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'address':{
            required: true,
          },
          'phone':{
            required: true,
          },
          'email':{
            required: true,
            email: true,
          },
          'ref2':{
            required:true
          }
        },
        messages: {
            'name': {
                required: "Required"
            },
            'address': {
                required: "Required"
            },
            'phone': {
                required: "Required"
            },
            'email': {
                required: "Required",
                email: "Invalid Email Format"
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            // $('.loading').remove();
            // loading = "<div class='loading'></div>";
            // $('.form-contacts .navigator').append(loading);
            // $('.form-contacts .btn-save').hide();
            // $('.form-contacts .btn-cancel').hide();
            // $('.form-contacts .btn-more').hide();
            // form.submit();
      }

     });

     $(".form-contacts").submit(function(e) {
         if($("#name").hasClass('error')){
          $('.form-contacts .loading').remove();
          $('.form-contacts .btn-save').show();
          $('.form-contacts .btn-cancel').show();
          $('.form-contacts .btn-more').show();
                    
          return false;
        }else{
              loading = "<div class='loading'></div>";
              $('.form-contacts .navigator').append(loading);
              $('.form-contacts .btn-save').hide();
              $('.form-contacts .btn-cancel').hide();
              $('.form-contacts .btn-more').hide();

              var formData = new FormData(this);
       
              formData.append('address', CKEDITOR.instances.address.getData());

              var url =$(this).attr("action");
              $.ajax({
                  url:url,
                  type: 'POST',
                  dataType: "json",
                  data: formData,
                  mimeType: "multipart/form-data",
                  success: function (data) {
                    $('.form-contacts .loading').remove();
                    $('.form-contacts .btn-save').show();
                    $('.form-contacts .btn-cancel').show();
                    $('.form-contacts .btn-more').show();
                    if(data.status > 0){
                        alert(data.message);
                    }else{
                       window.location=window.adminURL+window.titleLink+data.linkId;
                    }
                  },
                  error: function(){
                    $('.form-contacts .loading').remove();
                    $('.form-contacts .btn-save').show();
                    $('.form-contacts .btn-cancel').show();
                    $('.form-contacts .btn-more').show();
                  },
                  cache: false,
                  contentType: false,
                  processData: false
              });
        }
            
      });


        /* edit */
        if($('#id').val()){

          var ckeditorBody = {
                 skin : 'moono-dark',
                toolbar :
                [
                  ['Source','-','Preview'],
                  ['Bold','Italic','Underline','Strike'],
                ]
              };

          CKEDITOR.replace( 'address', ckeditorBody);

        }

    }

  });