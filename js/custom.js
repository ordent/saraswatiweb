$(function() {

	$(".box-item").each(function(){
    	$(this).mouseover(function(){
		  	$(this).find('img').attr("src", $(this).data("img-hover"));
		  	$(this).addClass($(this).data("theme"));
		});
		$(this).mouseleave(function(){
		  	$(this).find('img').attr("src", $(this).data("img"));
		  	$(this).removeClass($(this).data("theme"));
		});
    });

    $(".link-what-we-do").mouseover(function(){
    	$(".dropdown-content").css("display","block");
    });

    $(".link-programs").mouseover(function(){
    	$(".dropdown2-content").css("display","block");
    });

    $(".dropdown-content").mouseover(function(){
    	$(this).css("display","block");
    });

    $(".dropdown2-content").mouseover(function(){
    	$(this).css("display","block");
    });

    $(".dropdown-content").mouseleave(function(){
    	$(this).css("display","none");
    });

     $(".dropdown2-content").mouseleave(function(){
    	$(this).css("display","none");
    });

     $(".link-what-we-do").mouseleave(function(){
     	$(".dropdown-content").css("display","none");
    });

    $(".link-programs").mouseleave(function(){
     	$(".dropdown2-content").css("display","none");
    });

	if($(window).width() <= 767){

		$('#teams-slider').slick({
	        asNavFor: '#team-desc-slider',
	        arrows: true,
	        autoplay: false,
	        autoplaySpeed: 2000,
	        centerMode: true,
	        centerPadding: '0px',
	        dots: false,
	        focusOnSelect: true,
	        infinite: true,
	        slidesToShow: 1,
	    });
	}else{
	    $('#teams-slider').slick({
	        asNavFor: '#team-desc-slider',
	        arrows: true,
	        autoplay: false,
	        autoplaySpeed: 2000,
	        centerMode: true,
	        centerPadding: '0px',
	        dots: false,
	        focusOnSelect: true,
	        infinite: true,
	        slidesToShow: 3,
	    });
	}

    $('#team-desc-slider').slick({
        asNavFor: '#teams-slider',
        arrows: false,
        autoplay: false,
        autoplaySpeed: 2000,
        centerMode: true,
        centerPadding: '0px',
        dots: false,
        focusOnSelect: true,
        infinite: true,
        slidesToShow: 1,
    });

    $('.owl-whatwedo').owlCarousel({
        loop: true,
        margin: 20,
        items: 2,
        nav: true,
        autoplay: false,
        center: true,
        navText: ["<i class='fa fa-chevron-left'></i>","<i class='fa fa-chevron-right'></i>"],
    });

});

var myFullpage = new fullpage('#fullpage', {
    sectionsColor: ['#fafafa', '#fff', '#fafafa', '#fff', '#fff', '#006598',  '#fafafa', '#fff', '#008ed2'],
    anchors: ['home', 'about', 'what-we-do', 'laksmi', 'devi', 'blog', 'team', 'partners', 'contact'],
    menu: '#menu',
    lazyLoad: false,
    responsiveWidth: 767,
});