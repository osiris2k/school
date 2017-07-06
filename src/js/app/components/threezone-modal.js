var pc_threezone_modal = {
  modalSelector: '[pc-threezone-modal]',
  triggerData: 'threezone-modal-trigger',
  closeSelector: '[pc-threezone-modal-close]',
  activeClass: 'threezone-is-open',
  lastRect: '',
  init: function(){

    $(pc_threezone_modal.modalSelector).each(function() {
      var modal = $(this);
      $(this).hide(0);
      $(this).on('click', function(e) {
        // normal
        if (e.target == $(this)[0]) {
          pc_threezone_modal.close('#' + $(this).attr('id'));
        }
      });
    });

    // trigger
    $('[data-' + pc_threezone_modal.triggerData + ']').each(function() {
      $(this).on('click', function(e) {
        e.preventDefault();
        var id = $(this).data(pc_threezone_modal.triggerData);
        var background = $(this).find('figure img').attr("src");
        var container = $('<div class="threezone-image-container"></div>');
        var cover = $('<div class="threezone-modal-image" style="background-image: url(\'' + background + '\');"></div>');
        cover.appendTo('body');

        var coverTop = $(this).offset().top - $(window).scrollTop();
        var coverLeft = $(this).offset().left;
        var coverToTop = $(this).position().top;
        // var coverToLeft = $(this).offset().left;
        var coverToWidth = $(this).width();
        var coverToHeight = $(this).height();
        cover.css({
          left: coverLeft,
          top: coverTop,
          width: coverToWidth,
          height: coverToHeight
        });

        // NOTE: TRANSFORM
        // var targetX = coverLeft;
        // var targetY = coverTop;
        // var targetWidth = coverToWidth /  jQuery(window).width();
        // var targetHeight = coverToHeight / jQuery(window).height();
        // TweenMax.set(cover,{x:targetX,transformOrigin:"left top", y:targetY,scaleX:targetWidth,scaleY:targetHeight});

        // NOTE: CLIP
        // var hoverMargin = 40;
        // pc_threezone_modal.lastRect = 'rect(' + (coverTop+hoverMargin) + 'px ' + (coverLeft+coverToWidth-hoverMargin) + 'px ' + (coverTop + coverToHeight -hoverMargin) + 'px '+ (coverLeft+hoverMargin) +'px)';
        // TweenMax.set(cover, {clip:pc_threezone_modal.lastRect});

        pc_threezone_modal.open(id);
      });
    });

    // close
    $(pc_threezone_modal.closeSelector).each(function() {
      $(this).on('click', function(e) {
        e.preventDefault();
        var modalId = $(this).parents(pc_threezone_modal.modalSelector).attr('id');
        pc_threezone_modal.close('#' + modalId);
      });
    });


    // $(pc_threezone_modal.popupSelector).hide(0);

    // $('body').on('click', pc_threezone_modal.selector, function() {
    //   if($(pc_threezone_modal.popupSelector).hasClass(pc_threezone_modal.activeClass)){
    //     pc_threezone_modal.close();
    //   }else{
    //     pc_threezone_modal.open();
    //   }
    // });
  },
  open: function(id){
    if ($(id).length) {
      // clear animation
      $(id).removeClass(pc_threezone_modal.activeClass);
      $(id).show(0, function() {
        // $('body').addClass('overflow-hidden');
        $(this).addClass(pc_threezone_modal.activeClass).one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function() {
          $('html').addClass('noscroll');
        });





        // var content = $(this).find('.threezone-modal-content');
        var cover = $('.threezone-modal-image');

        //
        // content.css({
        //     right: '-50%'
        // });

        // var rect = 'rect(0px ' + jQuery(window).width() + 'px ' + jQuery(window).height() + 'px 0px)';
        // TweenMax.to(cover, 0.8, {clip:rect,ease:Circ.easeInOut});

        // NOTE:
        TweenMax.to(cover, 0.8, {left:0, top:0,width: jQuery(window).width(),height: jQuery(window).height(),ease:Circ.easeInOut});

        // NOTE: TRANSFORM
        // TweenMax.to(cover, 0.8, {x:0, y:0,scaleX: 1, scaleY: 1,ease:Circ.easeInOut});

        // var tl = new TimelineLite();
        // tl.pause();
        // tl.to(cover, 0.8, {
        //     //opacity:1,
        //     left: 0,
        //     right: 0,
        //     top:0,
        //     bottom:0,
        //     width: '100%',
        //     height: '100%',
        //     ease: Power3.easeInOut
        // }, 0);
        // tl.to(content, 0.8, {
        //     right: 0,
        //     ease: Power3.easeInOut
        // }, 0);
        // tl.call(function() {
        //   content.css({
        //       right: ''
        //   });
        // });
        // tl.play();
      });
    }
    // $(pc_threezone_modal.popupSelector).show(0,function(){
    //   $(pc_threezone_modal.popupSelector).addClass(pc_threezone_modal.activeClass);
    // });
  },
  close: function(id){
    if ($(id).length) {
      $(id).removeClass(pc_threezone_modal.activeClass);
      var cover = $('.threezone-modal-image');
      // TweenMax.to(cover, 0.8, {clip:pc_threezone_modal.lastRect,ease:Circ.easeInOut});
      TweenMax.to(cover, 0.8, {x: '100%',ease:Circ.easeInOut});

      $(id).delay(1000).hide(0,function(){
        cover.remove();
        $('html').removeClass('noscroll');
      });

      //$('body').removeClass('overflow-hidden');
    }

    // $(pc_threezone_modal.popupSelector).removeClass(pc_threezone_modal.activeClass);
    // $(pc_threezone_modal.popupSelector).delay(600).hide(0);
  },
  closeCurrent: function(){
    var id = '#' + jQuery('.threezone-modal:visible').attr('id');
    pc_threezone_modal.close(id);
  }
};

site.ready.push(pc_threezone_modal.init);
