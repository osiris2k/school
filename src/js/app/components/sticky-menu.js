var pc_sticky_menu = {
  selector: '[pc-sticky-menu]',
  ratioTrigger: 1,
  positionFixed: 0,
  positionTrigger: 0,
  stickyClass: 'menu-is-fixed',
  releaseAnimClass: 'menu-is-released',
  init: function(){
    if(!jQuery(pc_sticky_menu.selector).length) return;

    var ratioParam =jQuery(pc_sticky_menu.selector).attr('pc-sticky-menu');
    if(ratioParam){
      pc_sticky_menu.ratioTrigger = ratioParam;
    }

    pc_sticky_menu.onResize();
  },
  onResize: function(){
    if(!jQuery(pc_sticky_menu.selector).length) return;

    if(checkWindowWidth().width < 991) return;

    // reset class
    jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.stickyClass);
    jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.releaseAnimClass);

    pc_sticky_menu.positionFixed = jQuery(pc_sticky_menu.selector).offset().top;
    pc_sticky_menu.positionTrigger = jQuery('footer').offset().top - $(window).height() * pc_sticky_menu.ratioTrigger;

    pc_sticky_menu.onScroll();
  },
  onScroll: function(){
    if(!jQuery(pc_sticky_menu.selector).length) return;

    if(checkWindowWidth().width < 991) {
      jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.stickyClass);
      jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.releaseAnimClass);
      return;
    }

    var scrollTop = jQuery(window).scrollTop();
    if(scrollTop >= pc_sticky_menu.positionFixed){
      if(!jQuery(pc_sticky_menu.selector).hasClass(pc_sticky_menu.stickyClass)){
        jQuery(pc_sticky_menu.selector).addClass(pc_sticky_menu.stickyClass);
      }
    }else{
      if(jQuery(pc_sticky_menu.selector).hasClass(pc_sticky_menu.stickyClass)){
        jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.stickyClass);
      }
    }

    if(scrollTop >= pc_sticky_menu.positionTrigger){
      if(!jQuery(pc_sticky_menu.selector).hasClass(pc_sticky_menu.releaseAnimClass)){
        jQuery(pc_sticky_menu.selector).addClass(pc_sticky_menu.releaseAnimClass);
      }
    }else{
      if(jQuery(pc_sticky_menu.selector).hasClass(pc_sticky_menu.releaseAnimClass)){
        jQuery(pc_sticky_menu.selector).removeClass(pc_sticky_menu.releaseAnimClass);
      }
    }
  }
};

site.ready.push(pc_sticky_menu.init);
site.resize.push(pc_sticky_menu.onResize);
site.scroll.push(pc_sticky_menu.onScroll);
