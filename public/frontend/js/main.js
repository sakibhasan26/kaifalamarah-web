(function ($) {
  "user strict";

  // preloader
  $(window).on('load', function() {
    $(".preloader").delay(800).animate({
      "opacity": "0"
    }, 800, function () {
        $(".preloader").css("display", "none");
    });
  });

// wow
if ($('.wow').length) {
  var wow = new WOW({
    boxClass: 'wow',
    // animated element css class (default is wow)
    animateClass: 'animated',
    // animation css class (default is animated)
    offset: 0,
    // distance to the element when triggering the animation (default is 0)
    mobile: false,
    // trigger animations on mobile devices (default is true)
    live: true // act on asynchronously loaded content (default is true)
  });
  wow.init();
}

//Create Background Image
(function background() {
  let img = $('.bg_img');
  img.css('background-image', function () {
    var bg = ('url(' + $(this).data('background') + ')');
    return bg;
  });
})();

// lightcase
 $(window).on('load', function () {
  $("a[data-rel^=lightcase]").lightcase({
    inline: {
        width: 8000,
        height: 8000
      },
  });
})


// header-fixed
var fixed_top = $(".header-section");
$(window).on("scroll", function(){
    if( $(window).scrollTop() > 100){
        fixed_top.addClass("animated fadeInDown header-fixed");
    }
    else{
        fixed_top.removeClass("animated fadeInDown header-fixed");
    }
});

// navbar-click
$(".navbar li a").on("click", function () {
  var element = $(this).parent("li");
  if (element.hasClass("show")) {
    element.removeClass("show");
    element.children("ul").slideUp(500);
  }
  else {
    element.siblings("li").removeClass('show');
    element.addClass("show");
    element.siblings("li").find("ul").slideUp(500);
    element.children('ul').slideDown(500);
  }
});

// Scroll To Top
if ($(".progress-wrap").length) {
  var progressPath = document.querySelector(".progress-wrap path");
  var pathLength = progressPath.getTotalLength();
  progressPath.style.transition = progressPath.style.WebkitTransition =
      "none";
  progressPath.style.strokeDasharray = pathLength + " " + pathLength;
  progressPath.style.strokeDashoffset = pathLength;
  progressPath.getBoundingClientRect();
  progressPath.style.transition = progressPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";
  var updateProgress = function() {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
  };
  updateProgress();
  $(window).scroll(updateProgress);
  var offset = 50;
  var duration = 550;
  jQuery(window).on("scroll", function() {
      if (jQuery(this).scrollTop() > offset) {
          jQuery(".progress-wrap").addClass("active-progress");
      } else {
          jQuery(".progress-wrap").removeClass("active-progress");
      }
  });
  jQuery(".progress-wrap").on("click", function(event) {
      event.preventDefault();
      jQuery("html, body").animate({
              scrollTop: 0,
          },
          duration
      );
      return false;
  });
}

//Odometer
if ($(".statistics-item,.icon-box-items,.counter-single-items").length) {
    $(".statistics-item,.icon-box-items,.counter-single-items").each(function () {
      $(this).isInViewport(function (status) {
        if (status === "entered") {
          for (var i = 0; i < document.querySelectorAll(".odometer").length; i++) {
            var el = document.querySelectorAll('.odometer')[i];
            el.innerHTML = el.getAttribute("data-odometer-final");
          }
        }
      });
    });
}

// faq
$('.faq-wrapper .faq-title').on('click', function (e) {
  var element = $(this).parent('.faq-item');
  if (element.hasClass('open')) {
    element.removeClass('open');
    element.find('.faq-content').removeClass('open');
    element.find('.faq-content').slideUp(300, "swing");
  } else {
    element.addClass('open');
    element.children('.faq-content').slideDown(300, "swing");
    element.siblings('.faq-item').children('.faq-content').slideUp(300, "swing");
    element.siblings('.faq-item').removeClass('open');
    element.siblings('.faq-item').find('.faq-title').removeClass('open');
    element.siblings('.taq-item').find('.faq-content').slideUp(300, "swing");
  }
});

// slider
var swiper = new Swiper(".testimonial-slider", {
  slidesPerView: 1,
  spaceBetween: 30,
  loop: true,
  autoplay: {
    speed: 1000,
    delay: 3000,
  },
  speed: 1000,
});

var swiper = new Swiper(".brand-slider", {
  slidesPerView: 6,
  spaceBetween: 30,
  loop: true,
  autoplay: {
    speed: 1000,
    delay: 3000,
  },
  speed: 1000,
  breakpoints: {
    1199: {
    slidesPerView: 5,
    },
    991: {
    slidesPerView: 4,
    },
    767: {
    slidesPerView: 3,
    },
    575: {
    slidesPerView: 2,
    },
  }
});

// progress bar
$(".progressbar").each(function(){
    $(this).find(".bar").animate({
      "width": $(this).attr("data-perc")
    },8000);
    if($('body').hasClass('rtl')) {
      $(this).find(".label").animate({
        "right": $(this).attr("data-perc")
      },8000);
    }else{
      $(this).find(".label").animate({
        "left": $(this).attr("data-perc")
      },8000);
    }
  });

// input toggle
$("#visa").click(function(){
  $(".card-form").addClass('active');
});
$("#paypal").click(function(){
  $(".card-form").removeClass('active');
});

//donation-tab-switcher
$('.donation-tab-switcher').on('click', function () {
  $(this).toggleClass('active');
  $('.donation-tab').toggleClass('change-color');
});

// custom cursor
var cursor = $(".cursor"),
  follower = $(".cursor-follower");

var posX = 0,
  posY = 0;

var mouseX = 0,
  mouseY = 0;

TweenMax.to({}, 0.016, {
  repeat: -1,
  onRepeat: function() {
    posX += (mouseX - posX) / 9;
    posY += (mouseY - posY) / 9;

    TweenMax.set(follower, {
        css: {
        left: posX - 12,
        top: posY - 12
        }
    });
    TweenMax.set(cursor, {
        css: {
        left: mouseX,
        top: mouseY
        }
    });
  }
});
$(document).on("mousemove", function(e) {
    mouseX = e.clientX;
    mouseY = e.clientY;
});
$("a").on("mouseenter", function() {
    cursor.addClass("active");
    follower.addClass("active");
});
$("a").on("mouseleave", function() {
    cursor.removeClass("active");
    follower.removeClass("active");
});
$('input').attr('autocomplete','off');

// sidebar
$(".has-sub > a").on("click", function () {
  var element = $(this).parent("li");
  if (element.hasClass("active")) {
    element.removeClass("active");
    element.children("ul").slideUp(500);
  }
  else {
    element.siblings("li").removeClass('active');
    element.addClass("active");
    element.siblings("li").find("ul").slideUp(500);
    element.children('ul').slideDown(500);
  }
});

//sidebar Menu
$(document).on('click', '.sidebar-collapse-icon', function () {
  $('.page-container').toggleClass('show');
});

window.addEventListener('resize', function () {
  if (screen.width > 991) {
    $('.sidebar-main-menu').show();
  }else{
    $('.sidebar-main-menu').hide();
  }
}, true);

// Mobile Menu
$('.sidebar-mobile-menu').on('click', function () {
  $('.sidebar-main-menu').slideToggle();
});

//upload
function proPicURL(input) {
  if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
          var preview = $(input).parents('.preview-thumb').find('.profilePicPreview');
          $(preview).css('background-image', 'url(' + e.target.result + ')');
          $(preview).addClass('has-image');
          $(preview).hide();
          $(preview).fadeIn(650);
      }
      reader.readAsDataURL(input.files[0]);
  }
}
$(".profilePicUpload").on('change', function () {
  proPicURL(this);
});

$(".remove-image").on('click', function () {
  $(".profilePicPreview").css('background-image', 'none');
  $(".profilePicPreview").removeClass('has-image');
})
$(".nice-select").niceSelect();

})(jQuery);






var allCountries = "";
function getAllCountries(hitUrl,targetElement = $(".country-select"),errorElement = $(".country-select").siblings(".select2")) {
  if(targetElement.length == 0) {
    return false;
  }
  var CSRF = $("meta[name=csrf-token]").attr("content");
  var data = {
    _token      : CSRF,
  };
  $.post(hitUrl,data,function() {
    // success
    $(errorElement).removeClass("is-invalid");
    $(targetElement).siblings(".invalid-feedback").remove();
  }).done(function(response){
    // Place States to States Field
    var options = "<option selected disabled>Select Country</option>";
    var selected_old_data = "";
    if($(targetElement).attr("data-old") != null) {
        selected_old_data = $(targetElement).attr("data-old");
    }
    $.each(response,function(index,item) {
        options += `<option value="${item.name}" data-id="${item.id}" data-mobile-code="${item.mobile_code}" ${selected_old_data == item.name ? "selected" : ""}>${item.name}</option>`;
    });

    allCountries = response;

    $(targetElement).html(options);
  }).fail(function(response) {
    var faildMessage = "Something went wrong Please try again.";
    var faildElement = `<span class="invalid-feedback" role="alert">
                            <strong>${faildMessage}</strong>
                        </span>`;
    $(errorElement).addClass("is-invalid");
    if($(targetElement).siblings(".invalid-feedback").length != 0) {
        $(targetElement).siblings(".invalid-feedback").text(faildMessage);
    }else {
      errorElement.after(faildElement);
    }
  });
}
// getAllCountries();
// select-2 init
$('.select2-basic').select2();
$('.select2-multi-select').select2();
$(".select2-auto-tokenize").select2({
tags: true,
tokenSeparators: [',']
});
function placePhoneCode(code) {
    if(code != undefined) {
        code = code.replace("+","");
        code = "+" + code;
        $("input.phone-code").val(code);
        $("div.phone-code").html(code);
    }
  }


$(document).on('click','.dashboard-list-item',function (e) {
    if(e.target.classList.contains("select-btn")) {
      $(".dashboard-list-item-wrapper .select-btn").text("Select");
      $(e.target).text("Selected");
      return false;
    }
    var element = $(this).parent('.dashboard-list-item-wrapper');
    if (element.hasClass('show')) {
      element.removeClass('show');
      element.find('.preview-list-wrapper').removeClass('show');
      element.find('.preview-list-wrapper').slideUp(300, "swing");
    } else {
      element.addClass('show');
      element.children('.preview-list-wrapper').slideDown(300, "swing");
      element.siblings('.dashboard-list-item-wrapper').children('.preview-list-wrapper').slideUp(300, "swing");
      element.siblings('.dashboard-list-item-wrapper').removeClass('show');
      element.siblings('.dashboard-list-item-wrapper').find('.dashboard-list-item').removeClass('show');
      element.siblings('.dashboard-list-item-wrapper').find('.preview-list-wrapper').slideUp(300, "swing");
    }
});

// f-dropdown
(function( $ ){
    $.fn.mySelectDropdown = function(options) {
      return this.each(function() {
        var $this = $(this);
        $this.each(function () {
          var dropdown = $("<div />").addClass("f-dropdown selectDropdown");
          if($(this).is(':disabled'))
            dropdown.addClass('disabled');
          $(this).wrap(dropdown);
          var label = $("<span />").append($("<span />")
            .text($(this).attr("placeholder"))).insertAfter($(this));
          var list = $("<ul />");
          $(this)
            .find("option")
            .each(function () {
              var image = $(this).data('image');
              if(image) {
                list.append($("<li />").append(
                  $("<a />").attr('data-val',$(this).val())
                  .html(
                    $("<span />").append($(this).text())
                  ).prepend('<img src="'+image+'">')
                ));
              } else if($(this).val() != '') {
                list.append($("<li />").append(
                  $("<a />").attr('data-val',$(this).val())
                  .html(
                    $("<span />").append($(this).text())
                  )
                ));
              }
            });
          list.insertAfter($(this));
          if ($(this).find("option:selected").length > 0 && $(this).find("option:selected").val() != '') {
            list.find('li a[data-val="' + $(this).find("option:selected").val() + '"]').parent().addClass("active");
            $(this).parent().addClass("filled");
            label.html(list.find("li.active a").html());
          }
        });
        if(!$(this).is(':disabled')) {
          $(this).parent().on("click", "ul li a", function (e) {
            e.preventDefault();
            var dropdown = $(this).parent().parent().parent();
            var active = $(this).parent().hasClass("active");
            var label = active
              ? $(this).html()
              : $(this).html();
            dropdown.find("option").prop("selected", false);
            dropdown.find("ul li").removeClass("active");
            dropdown.toggleClass("filled", !active);
            dropdown.children("span").html(label);
            if (!active) {
              dropdown
                .find('option[value="' + $(this).attr('data-val') + '"]')
                .prop("selected", true);
              $(this).parent().addClass("active");
            }
            dropdown.removeClass("open");
          });
          $this.parent().on("click", "> span", function (e) {
            var self = $(this).parent();
            self.toggleClass("open");
          });
          $(document).on("click touchstart", function (e) {
            var dropdown = $this.parent();
            if (dropdown !== e.target && !dropdown.has(e.target).length) {
              dropdown.removeClass("open");
            }
          });
        }
      });
    };


  })( jQuery );
  $('select.f-dropdown').mySelectDropdown();
