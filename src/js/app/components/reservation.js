var pc_reservation = {
  selector: '[p-reservations]',
  popupSelector: '.reservations',
  activeClass: 'reservations-is-open',
  navMainOpened: false,
  navAnimationWait: false,
  init: function(){

    $('body').on('click', pc_reservation.selector, function() {
      if (pc_reservation.navAnimationWait)
        return false;
      pc_reservation.navAnimationWait = true;
      if($(pc_reservation.popupSelector).hasClass(pc_reservation.activeClass)){
        pc_reservation.close();
        $('html').removeClass('noscroll');
      }else{
        pc_reservation.open();
        $('html').addClass('noscroll');
      }
    });
  },
  open: function(){
    pc_threezone_modal.closeCurrent();
    pc_nav.close();

    var reservation = $(pc_reservation.popupSelector);
    var container = reservation.find('.reservations-container');
    var content = reservation.find('.reservations-content');
    var backgroundContainer = reservation.find('.backgrounds');
    var background = reservation.find('.background');
    var elements = reservation.find('.inner > *');

    $(pc_reservation.popupSelector).addClass(pc_reservation.activeClass);
    pc_reservation.navMainOpened = true;
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

    var tl = new TimelineLite();
    tl.pause();
    tl.fromTo(backgroundContainer, 0.6, {
      scaleX: 0,
      transformOrigin: 'right center'
    },{
      scaleX: 1,
      ease: Power2.easeIn
    }, 0);
    tl.fromTo(background, 0.6, {
      scaleX: 0,
      transformOrigin: 'right center'
    },{
      scaleX: 1,
      ease: Power2.easeOut
    }, 0.6);

    tl.call(function() {
      pc_reservation.navAnimationWait = false;
    });
    tl.to(elements, 0.5, {
        alpha: 1
    }, 1.1);

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
          opacity: ''
      });
    });
    tl.play();

    // $(pc_reservation.popupSelector).show(0,function(){
    //   $(pc_reservation.popupSelector).addClass(pc_reservation.activeClass);
    // });
  },
  close: function(){


    var reservation = $(pc_reservation.popupSelector);
    var container = reservation.find('.reservations-container');
    var content = reservation.find('.reservations-content');
    var backgroundContainer = reservation.find('.backgrounds');
    var background = reservation.find('.background');
    var containerWidth = container.width();

    background.css({
      display: 'none'
    });
    backgroundContainer.css({
      left: 0,
      right: 'auto',
      zIndex: 3,
      width: 0
    });
    container.css({
      left: 'auto',
      right: 0,
      background: '#faf9f4'
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

      $(pc_reservation.popupSelector).removeClass(pc_reservation.activeClass);
      pc_reservation.navMainOpened = false;
      pc_reservation.navAnimationWait = false;
    });
    tl.play();
    // $(pc_reservation.popupSelector).removeClass(pc_reservation.activeClass);
    // $(pc_reservation.popupSelector).delay(600).hide(0);
  }
};

site.ready.push(pc_reservation.init);
