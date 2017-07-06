var pc_scroll_scrollmagic = {
  controller:'',
  triggerSelector: '[pc-block-animate]',
  activeClass: 'pc-out-animate',
  init:function(){
    pc_scroll_scrollmagic.initScroll();
  },
  initScroll: function(){
    if($(pc_scroll_scrollmagic.triggerSelector).length){
      pc_scroll_scrollmagic.controller = new ScrollMagic.Controller();

      $(pc_scroll_scrollmagic.triggerSelector).each(function(){
        var scene = new ScrollMagic.Scene({
    							triggerElement: this,
                  triggerHook: 1
    						})
                // .on("enter", function (e) {
                //   jQuery(e.currentTarget.triggerElement()).addClass(pc_scroll_scrollmagic.activeClass);
                //   e.currentTarget.remove();
      					// })
                .setClassToggle(this, pc_scroll_scrollmagic.activeClass)
    						// .addIndicators({name: "1 (duration: 0)"})
    						.addTo(pc_scroll_scrollmagic.controller);
      });
    }
  }
};

site.load.push(pc_scroll_scrollmagic.init);
