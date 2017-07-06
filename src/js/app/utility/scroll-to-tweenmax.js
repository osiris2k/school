var b_scroll_to_tweenmax = {
  attr: 'b-scroll-to',
  offsetSelector: '[b-scroll-to-offset]',
  offset: 0,
  init:function(){
    b_scroll_to_tweenmax.offset = jQuery(b_scroll_to_tweenmax.offsetSelector).innerHeight() || 0;

    // if(jQuery(window).width() < 992){
    //   b_scroll_to_tweenmax.offset = 60;
    // }else{
    //   b_scroll_to_tweenmax.offset = 0;
    // }

    var selector = '[' + b_scroll_to_tweenmax.attr + ']';
    $(selector).each(function(){
      $(this).off('click');
      $(this).on('click', function(e){
        var value = $(this).attr(b_scroll_to_tweenmax.attr);

        if(!jQuery(value).length && 'top' != value) return;

        e.preventDefault();
        var targetTop = 0;

        if(value.indexOf('#') != -1 && $(value).length){
          targetTop = $(value).offset().top;
        }else{
          switch(value){
            case 'bottom':
              targetTop = $(document).height() - $(window).height();
            break;
            default:
              targetTop = $(this).attr(b_scroll_to_tweenmax.attr) | 0;
            break;
          }
        }

        var id = jQuery(this).attr('b-scroll-to');
        switch(id){
          case '#ristorante':
          case '#marcello':
            if(checkWindowWidth().width >= 992){
              targetTop -= jQuery('[pc-sticky-menu]').innerHeight();
            }

            // if(jQuery(id).is('.no-anim')){
            //   targetTop -= 75;
            // }

            break;
          case '#private-events':
          case '#private-parties':
            if(checkWindowWidth().width >= 992){
              targetTop -= jQuery('[pc-sticky-menu]').innerHeight() * 0.5;
            }

            break;
        }


        b_scroll_to_tweenmax.to(targetTop);
      });
    });
  },
  to:function(scrollTop){
    //  if(scrollTop > jQuery('section').eq(1).offset().top){
    //
    //  }

    scrollTop -= b_scroll_to_tweenmax.offset;

    TweenMax.killTweensOf($(window));
    TweenMax.to($("html, body"),0.8,{scrollTop:scrollTop, ease:Power3.easeInOut});
  },
  onScroll:function(){
    var currentTop = $(window).scrollTop();

    var selector = '[' + b_scroll_to_tweenmax.attr + ']';
    var lastTop = Number.MAX_VALUE;
    var targetIndex = 0;
    $(selector).each(function(i){
      var value = $(this).attr(b_scroll_to_tweenmax.attr);

      if(value.indexOf('#') != -1 && $(value).length){
        var targetTop = $(value).offset().top;
        targetTop -= b_scroll_to_tweenmax.offset;
        var diffTop = Math.abs(targetTop - currentTop);
        if(diffTop < lastTop){
          lastTop = diffTop;
          targetIndex = i;
        }
      }
    });

    $(selector).removeClass('active');
    $(selector).eq(targetIndex).addClass('active');
  },
  updateHash:function(){
    var $hash = $(window.location.hash);
    if($hash.length) {
      b_scroll_to_tweenmax.to($hash.offset().top);
    }else{
      switch(window.location.hash){
        case '#country-club':
          jQuery('.gallery-tab li a').eq(1).trigger('click');
        case '#luxury-villas':
          $gallery = $('#ms-gallery-container');
          if($gallery.length){
            b_scroll_to_tweenmax.to($gallery.offset().top);
          }
        break;
      }
    }
  },
  onLoad: function(){
    b_scroll_to_tweenmax.updateHash();
    b_scroll_to_tweenmax.onScroll();
  }
}

site.ready.push(b_scroll_to_tweenmax.init);
site.resize.push(b_scroll_to_tweenmax.init);
site.scroll.push(b_scroll_to_tweenmax.onScroll);
site.load.push(b_scroll_to_tweenmax.onLoad);
