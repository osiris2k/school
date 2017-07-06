var pc_slider_content = {
  selector: '[pc-slider-content]',
  releaseClass: 'slider-content-release',
  currentIndex: 0,
  windowHeight: 0,
  positionTrigger: 0,
  init: function(){
    if(!jQuery(pc_slider_content.selector).length) return;

    pc_slider_content.onResize();

  },
  onResize: function(){
    pc_slider_content.windowHeight = jQuery(window).height();
    pc_slider_content.positionTrigger = jQuery('footer').offset().top - pc_slider_content.windowHeight  ;
  },
  prevSlide: function(){
    pc_slider_content.slideShow('prev');
  },
  nextSlide: function(){
    pc_slider_content.slideShow('next');
  },
  onUpdate: function(){
    var scrollTop = jQuery(window).scrollTop();

    // set position
    if(scrollTop > pc_slider_content.positionTrigger){
      if(!jQuery(pc_slider_content.selector).hasClass(pc_slider_content.releaseClass)){
        jQuery(pc_slider_content.selector).addClass(pc_slider_content.releaseClass);
      }
    }else{
      if(jQuery(pc_slider_content.selector).hasClass(pc_slider_content.releaseClass)){
        jQuery(pc_slider_content.selector).removeClass(pc_slider_content.releaseClass);
      }

      // slide index
      var targetIndex = Math.floor(scrollTop / pc_slider_content.windowHeight);
      if(targetIndex != pc_slider_content.currentIndex){
        if(targetIndex > pc_slider_content.currentIndex){
          pc_slider_content.nextSlide();
        }else{
          pc_slider_content.prevSlide();
        }
        pc_slider_content.currentIndex = targetIndex;
      }
    }

  },
  slideShow: function(navigation) {
    var slideshow = $('.slideshow');
    var slides = slideshow.find('.slide');
    var activeSlide = slides.filter('.is-active');
    if (slideshow.data('animation-wait') === true)
        return;
    slideshow.data('animation-wait', true);
    if (navigation == 'prev') {
        var nextSlide = activeSlide.prev();
        if (nextSlide.length != 1)
            nextSlide = slides.last();
    } else if (navigation == 'next') {
        var nextSlide = activeSlide.next();
        if (nextSlide.length != 1)
            nextSlide = slides.first();
    }
    
    var activeSlideImage = activeSlide.find('.image');
    var nextSlideImage = nextSlide.find('.image');
    activeSlideImage.add(nextSlideImage).css({
        width: activeSlideImage.width(),
        height: activeSlideImage.height()
    });
    if (navigation == 'prev') {
        var activeSlideImageTo = 100;
        var nextSlideImageFrom = -100;
        nextSlide.css({
            display: 'block',
            height: 0
        });
        activeSlide.css({
            top: 'auto',
            bottom: 0
        });
        activeSlideImage.css({
            top: 'auto',
            bottom: 0
        });
    } else {
        var activeSlideImageTo = -100;
        var nextSlideImageFrom = 100;
        nextSlide.css({
            display: 'block',
            top: 'auto',
            bottom: 0,
            height: 0
        });
        nextSlideImage.css({
            top: 'auto',
            bottom: 0
        });
    }
    activeSlide.css({
        display: 'block'
    });
    activeSlide.removeClass('is-active');
    nextSlide.addClass('is-active');
    var tl = new TimelineLite();
    tl.pause();
    tl.to(activeSlide, 1, {
        height: 0,
        ease: Power3.easeInOut
    }, 0);
    tl.to(activeSlideImage, 1, {
        y: activeSlideImageTo,
        ease: Power3.easeInOut
    }, 0);
    tl.to(nextSlide, 1, {
        height: activeSlide.height(),
        ease: Power3.easeInOut
    }, 0);
    tl.fromTo(nextSlideImage, 1, {
        y: nextSlideImageFrom
    }, {
        y: 0,
        ease: Power3.easeInOut
    }, 0);
    tl.call(function() {
        activeSlideImage.add(nextSlideImage).css({
            width: '',
            height: '',
            top: '',
            bottom: '',
            transform: ''
        });
        activeSlide.add(nextSlide).css({
            display: '',
            height: '',
            top: '',
            bottom: ''
        });
        slideshow.data('animation-wait', false);
    });
    tl.play();
  }
};

site.ready.push(pc_slider_content.init);
site.resize.push(pc_slider_content.onResize);
site.scroll.push(pc_slider_content.onUpdate);
