var pc_widget = {
  widgetSelector: '#widget-33b024f4c57f2d50daa299a13f7427a222b3cb99f851ac32fc90c7c42bb3',
  EXTECTED_REQUEST: 2,
  requestCount: 0,
  init: function(){
    //console.log('init widget');
    var $widget = jQuery(pc_widget.widgetSelector);

    if(!$widget.length) return;

    var observer = new MutationObserver(function (mutations) {
      pc_widget.requestCount++;

      if(pc_widget.requestCount >= pc_widget.EXTECTED_REQUEST){
        observer.disconnect();

        socialSlider();
      }
    });


    if($widget.length){
      observer.observe($widget[0], {
          childList: true,
          characterData: true,
          subtree: true
      });
    }
  }
};

site.ready.push(pc_widget.init);



function socialSlider() {
  if(!$('.home .lm-widget').length)
    return;
    var settings = {
      slidesToShow: 1,
      dots: true,
      arrows: false,
      infinite: false,
      responsive: [
        {
          breakpoint: 99999,
          settings: "unslick"
        },
        {
          breakpoint: 576,
          settings: {
            slidesToShow: 1,
            dots: true,
            arrows: false,
            infinite: false
          }
        }
      ]
    };


    if (checkWindowWidth().width >= 576) {
      if ($('.lm-postlist').hasClass('slick-initialized')) {
        $('.lm-postlist').slick('unslick');
      }
    } else {
      if (!$('.lm-postlist').hasClass('slick-initialized')) {
        //return
        $('.lm-postlist').slick(settings);
        // $('.lm-postlist').slick('slickFilter','.lm-postlist-item');
        $('.lm-postlist-item:nth-child(-n+3)').addClass('lm-filter');
        $('.lm-postlist').slick('slickFilter','.lm-filter');

      }
    }
}

site.resize.push(socialSlider);
