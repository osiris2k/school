var b_slide_slick = {
  selector: '[b-slider]',
  responsiveData: 'responsive',
  activeClass:'active',
  currentPageSelector: '[b-slider-current]',
  totalPageSelector: '[b-slider-total]',
  progressSelector: '[b-slider-progress]',
  init:function(){

    $(b_slide_slick.selector).on('init',function(event,slick){
      // prevent default autoplay
      slick.options.isAutoplay = slick.options.autoplay;
      slick.options.autoplay = false;
    });
    $(b_slide_slick.selector).each(function(){
      if(jQuery(this).is('.slick-initialized')) return;

      var rData = $(this).data(b_slide_slick.responsiveData) || {};
      var slickData = $(this).data('slick') || {};
      var defaultSlide = slickData.slidesToShow || 1;

      var $arrowDOM = $(this).siblings('.slick-arrows').find('.arrows-inner');

      $(this).slick({
        arrows: true,
        dots: false,
        centerPadding: '0px',
        focusOnSelect: true,
        appendArrows: $arrowDOM,
        prevArrow:'<button class="slick-arrow slick-prev"><span><i class="icon icon-left-arrow"></i></span></button>',
        nextArrow:'<button class="slick-arrow slick-next"><span><i class="icon icon-right-arrow"></i></span></button>',
        responsive: [
          {
            breakpoint: 1366,
            settings: {
              slidesToShow: rData.xl || defaultSlide
            }
          },{
            breakpoint: 1280,
            settings: {
              slidesToShow: rData.lg || defaultSlide
            }
          },
          {
            breakpoint: 992,
            settings: {
              slidesToShow: rData.md || defaultSlide
            }
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: rData.sm || defaultSlide
            }
          },
          {
            breakpoint: 576,
            settings: {
              slidesToShow: rData.xs || defaultSlide
            }
          }
        ]
      });

      if($(slickData.pageSelector).length){
        var s = $(this).slick('getSlick');
        s.$currentPage = $(slickData.pageSelector).find(b_slide_slick.currentPageSelector);
        s.$totalPage = $(slickData.pageSelector).find(b_slide_slick.totalPageSelector);
      }

      if($(slickData.progressSelector).length){
        var s = $(this).slick('getSlick');
        s.$progressBar = $(slickData.progressSelector).find(b_slide_slick.progressSelector);

        if(s.$progressBar.length){
          var duration = s.options.autoplaySpeed / 1000;
          s.progressTween = TweenMax.fromTo(s.$progressBar,duration,{x:'-100%'},{x:'0%',ease:Power0.easeNone});
          s.progressTween.pause();
          if(s.options.pauseOnFocus && s.options.pauseOnHover){
            $(this).on('mouseenter',function(){
                s.progressTween.pause();
            });
            $(this).on('mouseleave',function(){
                s.progressTween.resume(0);
            });
          }

        }
      }
    });

    $(b_slide_slick.selector).on('afterChange',function(event,slick,currentSlide){
      b_slide_slick.updateCurrent(slick);
    });

    $(b_slide_slick.selector).on('beforeChange',function(event,slick,currentSlide,nextSlide){
      b_slide_slick.stopCurrent(slick);

    });
  },
  initSlick: function($dom){
    if($dom.is('.slick-initialized')) return;

    $dom.slick({
      arrows: true,
      dots: false,
      centerPadding: '0px',
      focusOnSelect: true,
      prevArrow:'<button class="slick-arrow slick-prev"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="37px" height="72px" viewBox="19.2 -11.7 37 72" style="enable-background:new 19.2 -11.7 37 72;" xml:space="preserve"><polygon class="arrow-item" points="55.2,60.3 19.2,24.3 55.2,-11.7 56.1,-10.7 21.1,24.3 56.1,59.3 "/></svg></button>',
      nextArrow:'<button class="slick-arrow slick-next"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="37px" height="72px" viewBox="19.2 -11.7 37 72" style="enable-background:new 19.2 -11.7 37 72;" xml:space="preserve"><polygon class="arrow-item" points="20.1,-11.7 56.1,24.3 20.1,60.3 19.2,59.3 54.2,24.3 19.2,-10.7 "/></svg></button>',
    });
  },
  destorySlick: function($dom){
    if(!$dom.is('.slick-initialized')) return;

    $dom.slick('destroy');
    $dom.empty();
  },
  onLoadComplete:function(){
    setTimeout(function(){
      $(b_slide_slick.selector).each(function(){
        var slick = $(this).slick('getSlick');
        // resume autoplay
        if(slick.options.isAutoplay){
          slick.options.autoplay = true;
          slick.play();
        }

        b_slide_slick.updateCurrent(slick);
      });
    },500);
  },
  updateCurrent:function(slick){
    slick.$slideTrack.children().each(function(){
      if($(this).is('.slick-active')){

        // animation
        $(this).find('[b-animation]').addClass(b_slide_slick.activeClass);

        // plane animation
        $(this).find('.animate-plane').addClass('animate');

        // progress
        if(typeof b_progress_progressbar != 'undefined'){
          var progressSelector = b_progress_progressbar.lineSelector + ',' + b_progress_progressbar.circleSelector + ',' + b_progress_progressbar.semiSelector ;
          $(this).find(progressSelector).each(function(){
            b_progress_progressbar.update($(this));
          });
        }

        if($(this).find('video').length){
          $(this).find('video').get(0).play();
          if(slick.options.autoplay){
            slick.pause();
            $(this).find('video').get(0).addEventListener('ended',function(e){
              e.target.removeEventListener(e.type, arguments.callee);
              slick.next();
              slick.play();
            },false);
          }
        }
      }else{
        // animation
        $(this).find('[b-animation]').removeClass(b_slide_slick.activeClass);

        // plane animation
        $(this).find('.animate-plane').removeClass('animate');
      }
    });

    b_slide_slick.updatePage(slick);
    b_slide_slick.updateProgress(slick);
  },
  stopCurrent:function(slick){
    slick.$slides.each(function(){
      if($(this).is('.slick-current') && $(this).find('video').length){
        $(this).find('video').get(0).currentTime = 0;
        $(this).find('video').get(0).pause();
      }
    });
  },
  updatePage: function(slick){
    // update page
    if(slick.$currentPage){
      slick.$currentPage.html(slick.currentSlide + 1);
    }
    if(slick.$totalPage){
      slick.$totalPage.html(slick.$slides.length);
    }
  },
  updateProgress: function(slick){
    if(slick.$progressBar){
      slick.progressTween.resume(0);
    }
  }
}

site.ready.push(b_slide_slick.init);
site.load.push(b_slide_slick.onLoadComplete);
