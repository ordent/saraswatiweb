   $(function() {

    if(window.location.href.indexOf("what_we_do_photos") > -1) {

      /* submit */
    $(".form-what-we-do-photos").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'ref3':{
            required: true,
          },
          'ref2':{
            required:true
          }
        },
        messages: {
            'name': {
                required: "Required"
            },
            'ref3': {
                required: "Required"
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            // $('.loading').remove();
            // loading = "<div class='loading'></div>";
            // $('.form-what-we-do-photos .navigator').append(loading);
            // $('.form-what-we-do-photos .btn-save').hide();
            // $('.form-what-we-do-photos .btn-cancel').hide();
            // $('.form-what-we-do-photos .btn-more').hide();
            // form.submit();
      }

     });

     $('#file_image').change(function () {

          //because this is single file upload I use only first index
          var f = this.files[0]
          var name = f.name;
          var ext = name.split('.').pop();
        if(ext == "jpg" || ext == "jpeg" || ext == "png"){
          //here I CHECK if the FILE SIZE is bigger than 500kb (numbers below are in bytes)
            if (f.size > 5000000 || f.fileSize > 5000000)
            {
               //show an alert to the user
               alert("Maximum file size only 1MB")

               //reset file upload control
               this.value = null;
            }
        }else{
          //show an alert to the user
             alert("File Allowed only png, jpg, jpeg")
        }

      });

     $(".form-what-we-do-photos").submit(function(e) {
         if($("#name").hasClass('error') || $("#file_image").hasClass('error')){
          $('.form-what-we-do-photos .loading').remove();
          $('.form-what-we-do-photos .btn-save').show();
          $('.form-what-we-do-photos .btn-cancel').show();
          $('.form-what-we-do-photos .btn-more').show();
                    
          return false;
        }else{
              loading = "<div class='loading'></div>";
              $('.form-what-we-do-photos .navigator').append(loading);
              $('.form-what-we-do-photos .btn-save').hide();
              $('.form-what-we-do-photos .btn-cancel').hide();
              $('.form-what-we-do-photos .btn-more').hide();

              var formData = new FormData(this);

              var url =$(this).attr("action");
              $.ajax({
                  url:url,
                  type: 'POST',
                  dataType: "json",
                  data: formData,
                  mimeType: "multipart/form-data",
                  success: function (data) {
                    $('.form-what-we-do-photos .loading').remove();
                    $('.form-what-we-do-photos .btn-save').show();
                    $('.form-what-we-do-photos .btn-cancel').show();
                    $('.form-what-we-do-photos .btn-more').show();
                    if(data.status > 0){
                        alert(data.message);
                    }else{
                       window.location=window.adminURL+window.titleLink+data.linkId;
                    }
                  },
                  error: function(){
                    $('.form-what-we-do-photos .loading').remove();
                    $('.form-what-we-do-photos .btn-save').show();
                    $('.form-what-we-do-photos .btn-cancel').show();
                    $('.form-what-we-do-photos .btn-more').show();
                  },
                  cache: false,
                  contentType: false,
                  processData: false
              });
        }
            
      });


        /* edit */
        if($('#id').val()){

        }

    }

  });