var nav=document.querySelector('nav');
	window.addEventListener('scroll',function() {
	if(window.pageYOffset >100){
		nav.classList.add('bg-dark','shadow');
	}else{
		nav.classList.remove('bg-dark');
	}
	})
	
	
	$(window).scroll(function(){
		$(".edit-a").css("opacity", 1-$(window).scrollTop() / 250);
	});


//FOR EFFECT
	
	window.onload = function() {
		const EFFECT = document.querySelector("#effect");

		window.addEventListener('scroll', scrollEffect);

		function scrollEffect() {
			if(window.scroll>=500) {
				EFFECT.getElementsByClassName.opacity = '1';
				EFFECT.style.transform = 'translateX(0px)';
				EFFECT.style.transition = '1s ease-in-out';
			}
			else {
				EFFECT.style.opacity = '0';
				EFFECT.style.transform = 'translateX(-50px)';
			}
		}
		scrollEffect();
	}

//SCROLL UP BUTTON

  document.addEventListener("DOMContentLoaded", function() {
    var scrollUpBtn = document.getElementById("scrollUpBtn");

    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
      if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollUpBtn.style.display = "block";
      } else {
        scrollUpBtn.style.display = "none";
      }
    }

    scrollUpBtn.addEventListener("click", function() {
      document.body.scrollTop = 0;
      document.documentElement.scrollTop = 0;
    });
  });


