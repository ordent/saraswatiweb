   $(function() {

		window.baseURL = $('body').data('url');
	    window.adminURL = $('.breadcrumb').data('admin-url');
	    window.beforeLink = ""; 
	    window.titleLink = $('.breadcrumb').data('title-link');
	    window.schPath = $('.breadcrumb').data('sch-path');
	    window.perPage = $('.breadcrumb').data('perpage');
	    window.pg = $('.breadcrumb').data('pg');

	    $(".date").attr("readonly","readonly");
          $(".date").datepicker({
            dateFormat: 'yy-mm-dd',
            showOtherMonths: true,
            changeMonth: true,
            changeYear: true,
        });

	    pathname = window.location.pathname; 
		pathsplit = pathname.split("/");
		window.pathAliasName = pathsplit[2];

	    //section validate login
	    $.validator.addMethod("accept", function(value, element, param) {
	      return value.match(new RegExp("." + param + "$"));
	    });

	    $(".form-forgot").validate({
	        onkeyup:false,
	        onclick:false,
	        rules: {
	          'email':{
	            required: true,
	            email: true,
	            remote:{ 
	              url: window.baseURL+'adminuser_auths/do_check_email_exists',
	              type: "post",
	            }
	          },
	        },
	        messages: {
	            'email': {
	                required: "Email required",
	                email: "Invalid Email Format",
	                remote: "Your email not registered, please try another email"
	            },
	            'password':{
	              required: "Password required"
	            },
	      },
	      errorPlacement: function(error, element) {
	          if (element.attr("name") == "email" ){
	            error.insertAfter(".email");
	          }
	      },
	      submitHandler: function(form) {

	        var $form = $( '.form-forgot' ),
	            url = $form.attr( 'action' );

	            loading = "<div class='loading'></div>";
	            $('.navigator-forgot').append(loading);
	            $('.btn-forgot').hide();

	            $.ajax({
	              type: "POST",
	              url:url,
	              data: $(form).serialize(),
	              dataType: 'json',
	              success: function(response, text) {

	                $('.loading').remove();
	                $('.btn-close').show();

	                if(response.status == 0){
	                  $.get(window.baseURL+"forgot/success", function(data, status){
	                      $(".popup .module-body").html(data);
	                  });
	                }else{
	                  $('.loading').remove();
	                  alert(response.message);
	                }
	              },
	              error: function(){
	                $('.loading').remove();
	                $('.btn-forgot').show();
	                alert("something when wrong, please try again")
	              }
	            });
	      }

	     });


	    // form login
	    $(".form-login").validate({
	        onkeyup:false,
	        onclick:false,
	        rules: {
	          'email':{
	            required: true,
	            email: true,
	            remote:{ 
	              url: window.baseURL+'adminuser_auths/do_check_email_exists',
	              type: "post",
	            }
	          },
	          'password':{
	            required:true
	          },
	        },
	        messages: {
	            'email': {
	                 required: "Email required",
	                email: "Invalid Email Format",
	                remote: "Your email not registered, please try another email"
	            },
	            'password':{
	              required: "Password required"
	            },
	      },
	      errorPlacement: function(error, element) {
	          if (element.attr("name") == "email" ){
	            error.insertAfter(".reg-email");
	          }
	          if (element.attr("name") == "password" ){
	            error.insertAfter(".reg-password");
	          }
	      },
	      submitHandler: function(form) {

	        var $form = $( '.form-login' ),
	            url = $form.attr( 'action' );

	            $('.loading').remove();
	            loading = "<div class='loading'></div>";
	            $('.navigator-login').append(loading);
	            $('.btn-login').hide();

	            $.ajax({
	              type: "POST",
	              url:url,
	              data: $(form).serialize(),
	              dataType: 'json',
	              success: function(response, text) {

	                $('.loading').remove();
	                $('.btn-login').show();

	                if(response.status == 0){
	                  $('.loading').remove();
	                  window.location.href=window.baseURL+"index";
	                }else{
	                  $('.loading').remove();
	                  alert(response.message);
	                }
	              },
	              error: function(){
	                $('.loading').remove();
	                $('.btn-login').show();
	                // alert("Sistem gagal melakukan proses karena masalah server atau hal lain. Silahkan coba lagi")
	              }
	            });
	      }

	     });


	    $(".btn-close").click(function(){
	       jQuery("#facebox_overlay").click();
	    }); 

	    /* list */
	    if(!$('#id').val()){

	      $('#delete_btn_up').click(function(e) 
	      {
	        confirm("Are you sure ?","javascript:del()");
	      });
	      
	      $('#delete_btn_down').click(function(e) 
	      {
	        confirm("Are you sure ?","javascript:del()");
	      });
	      $('#publish_btn_up').click(function(e) 
	      {
	        confirm("Are you sure ?","javascript:pub()");
	      });
	      
	      $('#publish_btn_down').click(function(e) 
	      {
	        confirm("Are you sure ?","javascript:pub()");
	      });

	      $("[rel=tooltip]").tooltip();    
	    }

	    read_cookie_search();

	    if($('#id').val()){
          	$("select").addClass("span8");
         }else{
         	$("select").addClass("span3");
         }

         $("select").select2();

   });

function commafyValue(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

function checkAll()
{
  var choice_num = document.getElementsByName("choices").length;
  var choice_val = document.getElementsByName("choices");
  if(document.form_list.master.checked== true)
  {
    for(var i=0; i < choice_num; i++)
    {
      choice_val[i].checked=true;
    }
  }
  else
  {
    for(var i=0; i < choice_num; i++)
    {
      choice_val[i].checked=false;
    }
  }
}

function del()
{
      var queue = timer = 0;
      var checkedLength = 0;
      var choice_num = document.getElementsByName("choices").length; 
      var choice_val = document.getElementsByName("choices");
      for(var j=0; j < choice_num; j++)
      {
        
        if(choice_val[j].checked==true)
        {
          checkedLength++;
          var rec = choice_val[j].value;
          $.ajax({
            url: window.adminURL+window.titleLink+'/delete/'+choice_val[j].value,
            dataType: 'json',
            success: function(response) {
            queue++;
            
            /*cek status response balik*/
            if(response.status == 1){
              $("#record"+response.id).fadeOut();
              timer = 1000;
            }else{
              $("#record"+response.id+" td").addClass("zebra-error");
              alert("Can't remove record(s), you have set this record to unpublish first");
              timer = 3000;
            }
            /*end cek status */
            
            if(checkedLength == queue){
                setTimeout("goto()",timer);
            }
            
            }
          });
        }
      }
}


function pub()
{
  var queue = timer = 0;
      var checkedLength = 0;
      var choice_num = document.getElementsByName("choices").length; 
      var choice_val = document.getElementsByName("choices");
      for(var j=0; j < choice_num; j++)
      {
        
        if(choice_val[j].checked==true)
        {
          checkedLength++;
     
          if(window.titleLink == "sub_programs"){
          	window.beforeLink = window.titleLink;
          }

          window.titleLink = window.titleLink == "sub_programs" ? "programs" : window.titleLink;
          var rec = choice_val[j].value;
          $.ajax({
            url: window.adminURL+'widget/publish/'+window.titleLink+'/'+choice_val[j].value,
            dataType: 'json',
            success: function(response) {
            queue++;
            
            /*cek status response balik*/
            if(response.status == 1){
              $("#pub"+response.id).html(response.val);
              $("#record"+response.id+" td").css("background","#81dd87");
              timer = 1000;
            }else{
              $("#record"+response.id+" td").addClass("zebra-error");
              alert("Change record(s) failed!");
              timer = 3000;
            }
            /*end cek status*/
            
            if(checkedLength == queue){
                setTimeout("goto()",timer);
            }
            
            }
          });
        }
    }
}


function goto()
{
  if(window.beforeLink != ""){
  	window.titleLink = window.beforeLink;
  }

  window.location=window.adminURL+window.titleLink+'/pages/'+window.schPath+'/'+window.perPage+'/'+window.pg;
}

function set_search(){
  document.forms["search"].submit();

}

function openclose_search()
{
    if($("#search_box").css("display") == "none"){
      $("#close").html('<i class="icon-chevron-up pull-right"></i>');
      $("#search_box").css("display","");
      $.cookie('search_'+window.titleLink,1,{ path: '/' });
    }else{
      $("#close").html('<i class="icon-chevron-down pull-right"></i>');
      $("#search_box").css("display","none");;
      $.cookie('search_'+window.titleLink,null,{ path: '/' });
    }
}

function validateLimit(evt,val) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    var keyA = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode( key );
    var regex = /[0-9]/;
    if( !regex.test(key) && keyA != 8 && keyA != 46 && keyA != 37 && keyA != 39 && keyA != 13) {
      theEvent.returnValue = false;
      theEvent.preventDefault();
    }
}
  
function sendLimit(val)
{
  document.location=window.adminURL+window.titleLink+"/pages/"+window.schPath+"/"+val;
}


function read_cookie_search(){

  if($.cookie('search_'+window.titleLink) == 1){
    $("#close").html('<i class="icon-chevron-up pull-right"></i>');
    $("#search_box").css("display","");
  }else{
    $("#close").html('<i class="icon-chevron-down pull-right"></i>');
    $("#search_box").css("display","none");
  }
}