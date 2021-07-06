$(function() {


    if(window.location.href.indexOf("orders/inputs") > -1) {

        window.baseURL = $('body').data('url');
        window.adminURL = $('.breadcrumb').data('admin-url');
        window.titleLink = $('.breadcrumb').data('title-link');

        $(".form-input-customers").validate({
            onkeyup:false,
            onclick:false,
            rules: {
              'customers_id':{
                required: true,
              },
              'ref1':{
                required:true
              }
            },
            messages: {
                'customers_id': {
                    required: "Required",
                },
                'ref1': {
                    required: "Required",
                }
          },
          submitHandler: function(form) {
                $('.loading').remove();
                loading = "<div class='loading'></div>";
                $('.form-input-customers .navigator').append(loading);
                $('.form-input-customers .btn-check').hide();
                $('.form-input-customers .btn-cancel').hide();

                var $form = $( '.form-input-customers' ),
                    customer_id = $form.find( 'select[name="customers"]' ).val(),
                    store_id = $form.find( 'select[name="ref1"]' ).val(),
                    url = $form.attr( 'action' );
                    link = $form.data("redirect");

                    url_check = window.adminURL + window.titleLink + "/do_check_customers";

                    $.ajax({
                      type: "POST",
                      url:url_check,
                      data: {
                        store_id: store_id,
                        customer_id: customer_id
                      },
                      dataType: 'json',
                      success: function(response, text) {

                        $(".loading").remove();
                        $('.form-input-customers .btn-check').show();
                        $('.form-input-customers .btn-cancel').show();

                        if(response.count > 0){
                          window.location.href=url+"?str="+response.stores_id+"&r_cst="+response.ref_customers_id+"&cst="+response.customers_id;
                        }else{
                          alert("Customer with identifier "+customer_id+" is not found, please try another or create new customer");
                        }

                      },
                      error: function(){
                        $(".loading").remove();
                        $('.form-input-customers .btn-check').show();
                        $('.form-input-customers .btn-cancel').show();
                        alert("Something when wrong, please try again")
                      }
                    });
          }

         });

        $("select").removeClass("span3");
        $("select").addClass("span8");
        $("select").select2();

        $('.customers').select2({
          placeholder: 'Customer ID/ Name/ Dcard',
          allowClear: true,
          ajax: {
            url: window.adminURL+window.titleLink+'/do_get_customers',
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
              return {
                results: data
              };
            },
            cache: true
          }
        });
    }
});