var pc_threezone = {
  selector: '[pc-threezone]',
  settings: '',
  $zone1: '',
  $zoneImage1: '',
  $zon2: '',
  $zoneImage2: '',
  $zone3: '',
  $zoneImage3: '',
  top1: '',
  top2: '',
  top3: '',
  lastScroll: '',
  init: function(){
    if(!jQuery(pc_threezone.selector).length) return;

    pc_threezone.settings = {
      slidesToShow: 1,
      centerMode: true,
      centerPadding: '15%',
      dots: false,
      arrows: false,
      variableWidth: false,
      infinite: false,
      responsive: [
        {
          breakpoint: 99999,
          settings: "unslick"
        },
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 1,
            centerMode: true,
            centerPadding: '15%',
            dots: false,
            arrows: false,
            variableWidth: true,
            infinite: false
          }
        },
        {
          breakpoint: 567,
          settings: {
            slidesToShow: 1,
            centerMode: true,
            centerPadding: '20%',
            dots: false,
            arrows: false,
            variableWidth: false,
            infinite: false
          }
        }
      ]
    };

    $(pc_threezone.selector).slick(pc_threezone.settings);


  },
  onLoad: function(){
    pc_threezone.updateSelector();
  },
  resize: function() {
    if(!jQuery(pc_threezone.selector).length) return;
    if (checkWindowWidth().width >= 992) {
      var sectionFirst = $('.sec-box:first-child');
      $('.sec-three-zone').insertAfter(sectionFirst);
      if ($(pc_threezone.selector).hasClass('slick-initialized')) {
        $(pc_threezone.selector).slick('unslick');
      }
      //return
    } else {
      var boxHero = $('.sec-box:first-child .boxs .box:first-child');
      $('.sec-three-zone').insertAfter(boxHero);
    }

    if (!$(pc_threezone.selector).hasClass('slick-initialized')) {
      return $(pc_threezone.selector).slick(pc_threezone.settings);
    }
    //pc_threezone.updateSelector();
  },
  updateSelector: function(){
    pc_threezone.$zone1 = $(pc_threezone.selector).find('.ratio-zone-1');
    pc_threezone.$zoneImage1 = pc_threezone.$zone1.find('img');
    pc_threezone.$zone2 = $(pc_threezone.selector).find('.ratio-zone-2');
    pc_threezone.$zoneImage2 = pc_threezone.$zone2.find('img');
    pc_threezone.$zone3 = $(pc_threezone.selector).find('.ratio-zone-3');
    pc_threezone.$zoneImage3 = pc_threezone.$zone3.find('img');

    pc_threezone.lastScroll = $(window).scrollTop();

    pc_threezone.top1 = pc_threezone.$zone1.offset().top ;
    pc_threezone.top2 = pc_threezone.$zone2.offset().top ;
    pc_threezone.top3 = pc_threezone.$zone3.offset().top ;

    pc_threezone.$zoneImage1.css({
      left: -(pc_threezone.$zone1.offset().left),
      // top: -(pc_threezone.top1 - pc_threezone.lastScroll)
    });

    pc_threezone.$zoneImage2.css({
      left: -(pc_threezone.$zone2.offset().left),
      // top: -(pc_threezone.top2 - pc_threezone.lastScroll)
    });
    pc_threezone.$zoneImage3.css({
      left: -(pc_threezone.$zone3.offset().left),
      // top: -(pc_threezone.top3 - pc_threezone.lastScroll)
    });
  },
  update: function() {
    if(!jQuery(pc_threezone.selector).length) return;

    if(Math.abs(pc_threezone.lastScroll-$(window).scrollTop()) < 2) return;

    pc_threezone.lastScroll = $(window).scrollTop();
    TweenMax.set(pc_threezone.$zoneImage1,{y:pc_threezone.lastScroll - pc_threezone.top1});
    TweenMax.set(pc_threezone.$zoneImage2,{y:pc_threezone.lastScroll - pc_threezone.top2});
    TweenMax.set(pc_threezone.$zoneImage3,{y:pc_threezone.lastScroll - pc_threezone.top3});

    // pc_threezone.$zoneImage1.css({
    //   top: -(pc_threezone.top1 - pc_threezone.lastScroll)
    // });
    // pc_threezone.$zoneImage2.css({
    //   top: -(pc_threezone.top2 - pc_threezone.lastScroll)
    // });
    // pc_threezone.$zoneImage3.css({
    //   top: -(pc_threezone.top3 - pc_threezone.lastScroll)
    // });




    // var zone1 = $(pc_threezone.selector).find('.ratio-zone-1');
    // var zoneImage1 = zone1.find('img');
    // var zone2 = $(pc_threezone.selector).find('.ratio-zone-2');
    // var zoneImage2 = zone2.find('img');
    // var zone3 = $(pc_threezone.selector).find('.ratio-zone-3');
    // var zoneImage3 = zone3.find('img');

    // zoneImage1.css({
    //   left: -(zone1.offset().left),
    //   top: -(zone1.offset().top - $(window).scrollTop())
    // });
    // zoneImage2.css({
    //   left: -(zone2.offset().left),
    //   top: -(zone2.offset().top - $(window).scrollTop())
    // });
    // zoneImage3.css({
    //   left: -(zone3.offset().left),
    //   top: -(zone3.offset().top - $(window).scrollTop())
    // });
  }
};

// site.ready.push(pc_threezone.init,pc_threezone.update);
site.ready.push(pc_threezone.init);
site.resize.push(pc_threezone.resize);
// site.load.push(pc_threezone.onLoad);
// site.scroll.push(pc_threezone.update);
