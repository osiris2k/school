var ms_scroll_scrollmagic = {
  controller:'',
  triggerSelector: '[ms-scroll-trigger]',
  activeClass: 'ms-animate',
  init:function(){
    if($(ms_scroll_scrollmagic.triggerSelector).length){
      ms_scroll_scrollmagic.controller = new ScrollMagic.Controller();

      $(ms_scroll_scrollmagic.triggerSelector).each(function(){
        var scene = new ScrollMagic.Scene({
    							triggerElement: this,
                  triggerHook: 0.8
    						})
                .setClassToggle(this, ms_scroll_scrollmagic.activeClass)
    						// .addIndicators({name: "1 (duration: 0)"})
    						.addTo(ms_scroll_scrollmagic.controller);
      });
    }
  }
}

site.ready.push(ms_scroll_scrollmagic.init);
