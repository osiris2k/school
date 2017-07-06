var pc_nav = {
  selector: '.menu-toggle',
  navSelector: '.nav-wrapper',
  activeClass: 'burger-is-open',
  navMainOpened: false,
  navAnimationWait: false,
  init: function(){
    //pc_nav.close();
    $('body').on('click', pc_nav.selector, function() {
      if (pc_nav.navAnimationWait)
      return false;
      pc_nav.navAnimationWait = true;
      if(jQuery(this).hasClass(pc_nav.activeClass)){
        pc_nav.close();
        $('html').removeClass('noscroll');
      }else{
        pc_nav.open();
        $('html').addClass('noscroll');
        
      }
    });
  },
  open: function(){
    pc_reservation.close();
    pc_threezone_modal.closeCurrent();

    var nav = $(pc_nav.navSelector);
    var container = nav.find('.nav-block');
    var content = nav.find('.nav');
    var backgroundContainer = nav.find('.backgrounds');
    var background = nav.find('.background');
    var elements = nav.find('.main-menu > li');
    var letters = nav.find('.nav-letters span');


    nav.addClass('nav-is-open');
    $(pc_nav.selector).addClass('burger-is-open');


    pc_nav.navMainOpened = true;
    container.css({
      background: 'none'
    });
    var containerWidth = container.width();
    backgroundContainer.css({
      width: containerWidth
    });
    background.css({
      width: containerWidth
    });
    elements.css({
      opacity: 0
    });
    letters.css({
      opacity: 0
    });
    var tl = new TimelineLite();
    tl.pause();
    tl.fromTo(backgroundContainer, 0.6, {
      scaleX: 0,
      transformOrigin: 'left center'
    },{
      scaleX: 1,
      ease: Power2.easeIn
    }, 0);
    tl.fromTo(background, 0.6, {
      scaleX: 0,
      transformOrigin: 'left center'
    },{
      scaleX: 1,
      ease: Power2.easeOut
    }, 0.6);

    tl.call(function() {
      pc_nav.navAnimationWait = false;
    });
    tl.fromTo(letters, 0.7, {
      y: -50,
      alpha: 0
    }, {
      y: 0,
      alpha: 1,
      ease: Power3.easeOut
    }, 1);
    tl.staggerFromTo(elements, 0.8, {
      y: -50,
      alpha: 0
    }, {
      y: 0,
      alpha: 1,
      ease: Power3.easeOut
    }, 0.05, 1.1);
    tl.call(function() {
      container.css({
        background: ''
      });
      backgroundContainer.css({
        width: ''
      });
      background.css({
        width: ''
      });
      container.css({
        display: ''
      });
      elements.css({
        opacity: '',
        transform: ''
      });
      letters.css({
        opacity: '',
        transform: ''
      });
    });
    tl.play();


    // $(pc_nav.navSelector).show(0,function(){
    //   $(pc_nav.selector).addClass('burger-is-open');
    //   //$(pc_nav.navSelector).addClass('nav-is-open');
    // });


  },
  close: function(){

    var nav = $(pc_nav.navSelector);
    var container = nav.find('.nav-block');
    var content = nav.find('.nav');
    var backgroundContainer = nav.find('.backgrounds');
    var background = nav.find('.background');
    var letters = nav.find('.nav-letters span');
    var containerWidth = container.width();

    if (checkWindowWidth().width >= 992 && $('body').hasClass('home-night'))
    var bg = '#000000';
    else
    var bg = '#ffffff';


    background.css({
      display: 'none'
    });
    backgroundContainer.css({
      right: 0,
      left: 'auto',
      zIndex: 3,
      width: 0
    });
    container.css({
      right: 'auto',
      left: 0,
      background: bg
    });

    var tl = new TimelineLite();
    tl.pause();
    tl.to(backgroundContainer, 0.6, {
      width: containerWidth,
      ease: Power2.easeIn
    }, 0);
    tl.to(container, 0.6, {
      width: 0,
      ease: Power2.easeOut
    }, 0.6);

    tl.call(function() {
      background.css({
        display: ''
      });
      backgroundContainer.css({
        left: '',
        right: '',
        zIndex: '',
        width: ''
      });
      container.css({
        left: '',
        right: '',
        width: '',
        background: ''
      });

      nav.removeClass('nav-is-open');
      $(pc_nav.selector).removeClass('burger-is-open');

      pc_nav.navMainOpened = false;
      pc_nav.navAnimationWait = false;
    });
    tl.play();

    // $(pc_nav.selector).removeClass('burger-is-open');
    // $(pc_nav.navSelector).removeClass('nav-is-open');
    //$(pc_nav.navSelector).delay(600).hide(0);

  }
};

site.ready.push(pc_nav.init);
