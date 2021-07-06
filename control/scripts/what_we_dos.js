   $(function() {

    if(window.location.href.indexOf("what_we_dos") > -1) {

      /* submit */
    $(".form-what-we-dos").validate({
        onkeyup:false,
        onclick:false,
        rules: {
          'name':{
            required: true,
          },
          'preview':{
            required: true,
          },
          'description':{
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
            'preview': {
                required: "Required"
            },
            'description': {
                required: "Required"
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            // $('.loading').remove();
            // loading = "<div class='loading'></div>";
            // $('.form-what-we-dos .navigator').append(loading);
            // $('.form-what-we-dos .btn-save').hide();
            // $('.form-what-we-dos .btn-cancel').hide();
            // $('.form-what-we-dos .btn-more').hide();
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

     // $('#icon_small').change(function () {

     //      //because this is single file upload I use only first index
     //      var f = this.files[0]
     //      var name = f.name;
     //      var ext = name.split('.').pop();
     //    if(ext == "jpg" || ext == "jpeg" || ext == "png"){
     //      //here I CHECK if the FILE SIZE is bigger than 500kb (numbers below are in bytes)
     //        if (f.size > 1000000 || f.fileSize > 1000000)
     //        {
     //           //show an alert to the user
     //           alert("Maximum file size only 1MB")

     //           //reset file upload control
     //           this.value = null;
     //        }
     //    }else{
     //      //show an alert to the user
     //         alert("File Allowed only png, jpg, jpeg")
     //    }

     //  });

     $('#icon_big').change(function () {

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

     $(".form-what-we-dos").submit(function(e) {
         if($("#name").hasClass('error') || $("#file_image").hasClass('error') || $("#icon_small").hasClass('error') || $("#icon_big").hasClass('error')){
          $('.form-what-we-dos .loading').remove();
          $('.form-what-we-dos .btn-save').show();
          $('.form-what-we-dos .btn-cancel').show();
          $('.form-what-we-dos .btn-more').show();
                    
          return false;
        }else{
              loading = "<div class='loading'></div>";
              $('.form-what-we-dos .navigator').append(loading);
              $('.form-what-we-dos .btn-save').hide();
              $('.form-what-we-dos .btn-cancel').hide();
              $('.form-what-we-dos .btn-more').hide();

              var formData = new FormData(this);
        
              formData.append('preview', CKEDITOR.instances.preview.getData());
              formData.append('description', CKEDITOR.instances.description.getData());

              var url =$(this).attr("action");
              $.ajax({
                  url:url,
                  type: 'POST',
                  dataType: "json",
                  data: formData,
                  mimeType: "multipart/form-data",
                  success: function (data) {
                    $('.form-what-we-dos .loading').remove();
                    $('.form-what-we-dos .btn-save').show();
                    $('.form-what-we-dos .btn-cancel').show();
                    $('.form-what-we-dos .btn-more').show();
                    if(data.status > 0){
                        alert(data.message);
                    }else{
                       window.location=window.adminURL+window.titleLink+data.linkId;
                    }
                  },
                  error: function(){
                    $('.form-what-we-dos .loading').remove();
                    $('.form-what-we-dos .btn-save').show();
                    $('.form-what-we-dos .btn-cancel').show();
                    $('.form-what-we-dos .btn-more').show();
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
                 filebrowserBrowseUrl : window.adminURL+'ckfinder/ckfinder.html',
                 filebrowserImageBrowseUrl : window.adminURL+'ckfinder/ckfinder.html?Type=Images',
                 filebrowserUploadUrl : window.adminURL+'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
                 filebrowserImageUploadUrl : window.adminURL+'ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
                 toolbar :
                  [
                    // ['Source','-','Preview'],
                    // ['Bold','Italic','Underline','Strike','NumberedList','BulletedList'],
                    // ['Link','Unlink','-','Image'],['TextColor','-','FontSize']
                    ['Source','-','Preview','Cut','Copy','Paste','PasteFromWord','PasteText'],
                    ['Bold','Italic','Underline','Strike'],['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
                    ['NumberedList','BulletedList','Subscript','Superscript','-','Outdent', 'Indent', '-', 'Blockquote'],
                    ['Link','Unlink','-','Movie','Image','Anchor','Table','Smiley','SpecialChar'],
                    [ 'Styles', 'Format','-','TextColor','BGColor','-','FontSize']
                  ]
              };

          CKEDITOR.replace( 'description', ckeditorBody);
          CKEDITOR.replace( 'preview', ckeditorBody);

        }

    }

  });