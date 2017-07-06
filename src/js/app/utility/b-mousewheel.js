var b_mousewheel = {
  selector: '[b-vh]',
  sensitivity: 20,
  buffer: 5,
  isMove: false,
  delay_time: 810,
  lastY: 0,
  init: function(){

    jQuery('section').addClass('section-has-animate');

    if(b_detect.isMobile()) return;

    if(!jQuery(b_mousewheel.selector).length) return;

    // keyboard
    b_mousewheel.initKeyboard();

    // mousewheel
    b_mousewheel.addLockScreen();
  },
  initKeyboard: function(){
    jQuery(document).on('keydown',function(e){
      var event = e;
      if(b_mousewheel.isMove) return;

      b_mousewheel.isMove = true;
      setTimeout(function(){
        b_mousewheel.isMove = false;
      },b_mousewheel.delay_time);

      var section_index = 0;
      var next_index = 0;
      var minY = Number.MAX_VALUE;
      var scrollTop = jQuery(window).scrollTop();
      jQuery('section').each(function(index){
        var dif = scrollTop - jQuery(this).offset().top;
        if(Math.abs(dif) < minY){
          minY = Math.abs(dif);
          section_index = index;
        }
      });
      if(section_index > 6) return;
      switch(e.keyCode){
        // up
        case 38:
          next_index = section_index - 1;
          next_index = Math.max(0,next_index);
          break;
        // down
        case 40:
          next_index = section_index + 1;
          break;
        default:
          return;
          break;
      }

      if(jQuery('section').eq(next_index).length){
        event.preventDefault();
        var targetTop = jQuery('section').eq(next_index).offset().top;
        b_scroll_to_tweenmax.to(targetTop);

        b_mousewheel.updateSection(section_index,next_index);
      }
    });
  },
  addLockScreen: function(){
    // if(jQuery('.section-has-animate').length) return;

    jQuery('.main-menu').addClass('has-animate');
    jQuery('section').addClass('section-has-animate');

    // mousewheel
    jQuery(b_mousewheel.selector).each(function(){
      var section = jQuery(this);

      if(section.height() <= jQuery(window).height()){
        section.on( 'mousewheel', b_mousewheel.onMouseWheel);
      }
    });

    // touch
    if(Modernizr.touch){

      jQuery(b_mousewheel.selector).hammer({
        direction: Hammer.DIRECTION_VERTICAL
      }).bind("swipe", function(e){

        var section_index = 0;
        var next_index = 0;
        var minY = Number.MAX_VALUE;
        var scrollTop = jQuery(window).scrollTop();

        jQuery('section').each(function(index){
          var dif = scrollTop - jQuery(this).offset().top;
          if(Math.abs(dif) < minY){
            minY = Math.abs(dif);
            section_index = index;
          }
        });
        if(e.gesture.deltaY > 0){
          next_index = section_index - 1;
          next_index = Math.max(0,next_index);
        }else{
          next_index = section_index + 1;
        }

        if(jQuery('section').eq(next_index).length){
          event.preventDefault();
          var targetTop = jQuery('section').eq(next_index).offset().top;
          b_scroll_to_tweenmax.to(targetTop);

          b_mousewheel.updateSection(section_index,next_index);
        }

      });
      jQuery(b_mousewheel.selector).each(function(){
        jQuery(this).data('hammer').get('swipe').set({ direction: Hammer.DIRECTION_VERTICAL });
      })

    }
  },
  removeLockScreen: function(){
    if(!jQuery('.section-has-animate').length) return;

    jQuery('.main-menu').removeClass('has-animate');
    jQuery('section').removeClass('section-has-animate');
    jQuery(b_mousewheel.selector).off( 'mousewheel', b_mousewheel.onMouseWheel);
  },
  onMouseWheel: function(event){
    event.preventDefault();
    event.stopImmediatePropagation();

    if(b_mousewheel.isMove) return;

    if(Math.abs(event.deltaY) >= b_mousewheel.sensitivity || event.deltaFactor > 1){
      b_mousewheel.isMove = true;
      setTimeout(function(){
        b_mousewheel.isMove = false;
      },b_mousewheel.delay_time);

      var targetTop = 0;
      var $target = '';
      if(event.deltaY < 0){
        // down
        var abs = Math.abs(jQuery(window).scrollTop() - jQuery(this).offset().top);
        if( abs <= b_mousewheel.buffer ){
          $target = jQuery(this).next();
        }else{
          $target = jQuery(this);
        }
      }else{
        // up
        if(event.deltaFactor > 1){
          $target = jQuery(this);

        }else{
          if(jQuery(this).prev().length){
            $target = jQuery(this).prev();
          }else{
            $target = jQuery(this);
          }
        }
      }
      b_scroll_to_tweenmax.to($target.offset().top);
      b_mousewheel.updateSection(jQuery(this).index(),$target.index());
    }
  },
  updateSection: function(current,next){



    if(b_detect.isMobile()) return;

    // parallax
    jQuery(b_mousewheel.selector).removeClass('section-current');
    jQuery(b_mousewheel.selector).eq(next).addClass('section-current');
    var targetHeight = jQuery(window).height() * 0.8;

    if(next < current){
      targetHeight *= -1;
    }else if( next == current){
      targetHeight = 0;
    }
    TweenMax.to(jQuery('section').eq(current),0.8,{y:targetHeight, ease:Power3.easeInOut,clearProps:"transform"});

    setTimeout(function(){
      // menu
      if(next){
        jQuery('.main-menu-container').removeClass('active');
      }else{
        jQuery('.main-menu-container').addClass('active');
      }

      jQuery(b_mousewheel.selector).removeClass('section-animate');
      jQuery(b_mousewheel.selector).eq(next).addClass('section-animate');
    },b_mousewheel.delay_time);
  }
}
site.ready.push(b_mousewheel.init);
