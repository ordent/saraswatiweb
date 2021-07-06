$(document).ready(function() {

      var token = '2802323113.1677ed0.f2e8259e71b2493f8b58cd169cf57ffb', // learn how to obtain it below
        userid = 8775672796, // User ID - get it in source HTML of your Instagram profile or look at the next example :)
        num_photos = 4; // how much photos do you want to get
     
    $.ajax({
      url: 'https://api.instagram.com/v1/users/' + userid + '/media/recent', // or /users/self/media/recent for Sandbox
      dataType: 'jsonp',
      type: 'GET',
      data: {access_token: token, count: num_photos},
      success: function(data){
        for( x in data.data ){
          // $('#images').append('<li><img src="'+data.data[x].images.low_resolution.url+'"></li>'); // data.data[x].images.low_resolution.url - URL of image, 306х306
          // data.data[x].images.thumbnail.url - URL of image 150х150
          // data.data[x].images.standard_resolution.url - URL of image 612х612
          // data.data[x].link - Instagram post URL
          // $('.instagram').append('<div class="col-4 col-md-4 padding"><a href="'+data.data[x].link+'" target="_blank"><img src="'+data.data[x].images.low_resolution.url+'"/></a></div>'); 
          $('.instagram').append('<div class="col-xs-12 col-sm-6 col-md-3"><a href="'+data.data[x].link+'" target="_blank"><img alt="insta-img" src="'+data.data[x].images.low_resolution.url+'"></a></div>');
        }
      },
      error: function(data){
        console.log(data); // send the error notifications to console
      }
    });
});
