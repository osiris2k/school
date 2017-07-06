var b_menu = {
  init: function(){
    setTimeout(function(){
      b_menu.animate();
    },500);
  },
  animate: function(){
    jQuery('.logo').addClass('logo-active');
    jQuery('.main-menu').addClass('main-menu-active');
    jQuery('.hamburger').addClass('hamburger-is-active');
  },
  reset: function(){
    jQuery('.logo').removeClass('logo-active');
    jQuery('.main-menu').removeClass('main-menu-active');
  },
  onScroll: function(){
    if(b_detect.isMobile()) return;

    if(jQuery(window).scrollTop() < 280){
      jQuery('.main-menu-container').addClass('active');
    }else{
      jQuery('.main-menu-container').removeClass('active');
    }
  }
}

site.load.push(b_menu.init);
site.scroll.push(b_menu.onScroll);
