   $(function() {

    if(window.location.href.indexOf("blogs") > -1) {

      /* submit */
    $(".form-blogs").validate({
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
          'author':{
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
            'preview': {
                required: "Required"
            },
            'description': {
                required: "Required"
            },
            'author': {
                required: "Required"
            },
            'ref3': {
                required: "Required",
            },
            'ref2': {
                required: "Required",
            }
      },
      submitHandler: function(form) {
            // $('.loading').remove();
            // loading = "<div class='loading'></div>";
            // $('.form-blogs .navigator').append(loading);
            // $('.form-blogs .btn-save').hide();
            // $('.form-blogs .btn-cancel').hide();
            // $('.form-blogs .btn-more').hide();
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

     $('#file_thumb').change(function () {

          //because this is single file upload I use only first index
          var f = this.files[0]
          var name = f.name;
          var ext = name.split('.').pop();
        if(ext == "jpg" || ext == "jpeg" || ext == "png"){
          //here I CHECK if the FILE SIZE is bigger than 500kb (numbers below are in bytes)
            if (f.size > 1000000 || f.fileSize > 1000000)
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

      $('#file_doc').change(function () {

          //because this is single file upload I use only first index
          var f = this.files[0]
          var name = f.name;
          var ext = name.split('.').pop();
        if(ext == "docx" || ext == "doc" || ext == "pdf"){
          //here I CHECK if the FILE SIZE is bigger than 500kb (numbers below are in bytes)
            if (f.size > 25000000 || f.fileSize > 25000000)
            {
               //show an alert to the user
               alert("Maximum file size only 25MB")

               //reset file upload control
               this.value = null;
            }
        }else{
          //show an alert to the user
             alert("File Allowed only doc, docx, pdf")
        }

      });

     $(".form-blogs").submit(function(e) {
         if($("#name").hasClass('error') || $("#file_image").hasClass('error') || $("#file_thumb").hasClass('error')){
          $('.form-blogs .loading').remove();
          $('.form-blogs .btn-save').show();
          $('.form-blogs .btn-cancel').show();
          $('.form-blogs .btn-more').show();
          return false;
        }else{
              loading = "<div class='loading'></div>";
              $('.form-blogs .navigator').append(loading);
              $('.form-blogs .btn-save').hide();
              $('.form-blogs .btn-cancel').hide();
              $('.form-blogs .btn-more').hide();

              var formData = new FormData(this);
              // formData.append('ref3', $('select[name="ref3"]').val());
              // formData.append('ref4', $('select[name="ref4"]').val());
              // formData.append('name', $('input[name="name"]').val());
              formData.append('description', CKEDITOR.instances.description.getData());
              // formData.append('price', $('input[name="price"]').val());
              // formData.append('expired_date', $('input[name="expired_date"]').val());
              // formData.append('ref2', $('select[name="ref2"]').val());
              // formData.append('id', $('input[name="id"]').val());
              // // Attach file
              // formData.append('file_image', $('input[name="file_image"]').prop('files'));

              var url =$(this).attr("action");
              $.ajax({
                  url:url,
                  type: 'POST',
                  dataType: "json",
                  data: formData,
                  mimeType: "multipart/form-data",
                  success: function (data) {
                    $('.form-blogs .loading').remove();
                    $('.form-blogs .btn-save').show();
                    $('.form-blogs .btn-cancel').show();
                    $('.form-blogs .btn-more').show();
                    if(data.status > 0){
                        alert(data.message);
                    }else{
                       window.location=window.adminURL+window.titleLink+data.linkId;
                    }
                  },
                  error: function(){
                    $('.form-blogs .loading').remove();
                    $('.form-blogs .btn-save').show();
                    $('.form-blogs .btn-cancel').show();
                    $('.form-blogs .btn-more').show();
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

        }

    }

  });