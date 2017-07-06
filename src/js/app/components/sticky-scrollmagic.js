var b_sticky_scrollmagic = {
  controller:'',
  selector: '[b-sticky]',
  elementSelector: '[b-sticky-element]',
  containerSelector: '[b-sticky-container]',
  scenes: [],
  stickyData: 'sticky',
  defaultOption: {
    offset: 0
  },
  init:function(){
    if($(b_sticky_scrollmagic.selector).length){

      b_sticky_scrollmagic.controller = new ScrollMagic.Controller();

      $(b_sticky_scrollmagic.selector).each(function(){
        var trigger = $(this)
        var offset = 0;
        var duration = 0;

        var element = trigger.find(b_sticky_scrollmagic.elementSelector).add(trigger.filter(b_sticky_scrollmagic.elementSelector));

        // header offset
        var dataAttr = $(this).data(b_sticky_scrollmagic.stickyData) || {};
        var data = $.extend({},b_sticky_scrollmagic.defaultOption,dataAttr);
        $(this).data(b_sticky_scrollmagic.stickyData,data);

        offset = data.offset * -1;

        var container = trigger.find(b_sticky_scrollmagic.containerSelector).add(trigger.filter(b_sticky_scrollmagic.containerSelector));
        if(container.length){
          duration = container.height();
          duration -= element.height();
        }

        var scene = new ScrollMagic.Scene({
    							triggerElement: element[0],
                  triggerHook: 0,
                  offset: offset,
                  duration: duration
    						})
                .setPin(element[0])
    						// .addIndicators({name: "1 (duration: 0)"})
    						.addTo(b_sticky_scrollmagic.controller);

        b_sticky_scrollmagic.scenes.push(scene);
      });

      //b_sticky_scrollmagic.check();
    }
  },
  check:function(){
    if($(window).width() <= TABLET_SIZE){
      b_sticky_scrollmagic.removePin();
    }else{
      b_sticky_scrollmagic.addPin();
    }
  },
  addPin: function(){
    b_sticky_scrollmagic.scenes.forEach(function(item,index){
      if(!item.enabled()){
        item.setPin(item.triggerElement());
        item.enabled(true);
      }
    });
  },
  removePin:function(){
    b_sticky_scrollmagic.scenes.forEach(function(item,index){
      if(item.enabled()){
        item.removePin(true);
        item.enabled(false);
      }
    });
  }
}

site.ready.push(b_sticky_scrollmagic.init);
//site.resize.push(b_sticky_scrollmagic.check);
