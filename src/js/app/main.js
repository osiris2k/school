/* requestAnimationFrame polyfill
-------------------------------------------------------------------------- */
(function() {
  var lastTime = 0;
  var vendors = ['webkit', 'moz'];
  for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
    window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
    window.cancelAnimationFrame =
    window[vendors[x]+'CancelAnimationFrame'] || window[vendors[x]+'CancelRequestAnimationFrame'];
  }

  if (!window.requestAnimationFrame)
  window.requestAnimationFrame = function(callback, element) {
    var currTime = new Date().getTime();
    var timeToCall = Math.max(0, 16 - (currTime - lastTime));
    var id = window.setTimeout(function() { callback(currTime + timeToCall); },
    timeToCall);
    lastTime = currTime + timeToCall;
    return id;
  };

  if (!window.cancelAnimationFrame)
  window.cancelAnimationFrame = function(id) {
    clearTimeout(id);
  };
}());

/* elementFromPoint
-------------------------------------------------------------------------- */
function elementFromPoint(x, y) {
  if (!isRelativeToViewport()) x += window.pageXOffset, y += window.pageYOffset
  return document.elementFromPoint(x, y)
}

var relativeToViewport;
function isRelativeToViewport() {
  if (relativeToViewport != null) return relativeToViewport

  var x = window.pageXOffset ? window.pageXOffset + window.innerWidth - 1 : 0
  var y = window.pageYOffset ? window.pageYOffset + window.innerHeight - 1 : 0
  if (!x && !y) return true

  // Test with a point larger than the viewport. If it returns an element,
  // then that means elementFromPoint takes page coordinates.
  return relativeToViewport = !document.elementFromPoint(x, y)
}

/* check window width (scrollbar included)
-------------------------------------------------------------------------- */
function checkWindowWidth() {
  var e = window,
  a = 'inner';
  if (!('innerWidth' in window )) {
    a = 'client';
    e = document.documentElement || document.body;
  }
  return {
    width: e[a + 'Width'],
    height: e[a + 'Height']
  };
}

/* =============================================================================
GLOBAL
========================================================================== */

var pageIsLoading = true;
var previousUrl = null;
var pageInitWait = false;
var pageOpeningWait = false;
var siteOpeningWait = true;
var withTransition = false;
var isDev = false;
var $window;
var $toLoad = $('.to-load');
$('html,body').stop().scrollTop(0);
$(function() {

  /* Resize
  -------------------------------------------------------------------------- */
  var windowWidth = $(window).width();
  var windowHeight = $(window).height();
  function checkWindowSize(args) {
    var e = window, a = 'inner';
    if (!('innerWidth' in window )) {
      a = 'client';
      e = document.documentElement || document.body;
    }
    windowWidth = $(window).width();
    windowHeight = e[ a+'Height' ];
  }

  $(window).on('resize orientationchange', function(e) {
    checkWindowSize();
    checkDeviceType();
    globalResize();

    $(window).trigger('scroll');
  });

  /* Scroll
  -------------------------------------------------------------------------- */
  var isScrolling = false;
  var scrollCheckTimeout = null;
  var windowScrollTop = 0;

  function scrollDispatcher(e) {
    //headBackgroundCheck();
    isScrolling = true;
    clearTimeout(scrollCheckTimeout);
    scrollCheckTimeout = setTimeout(function() {
      isScrolling = false;
    }, 100);
    windowScrollTop = $(window).scrollTop();
  }
  $(window).on('scroll', scrollDispatcher);

  /* Check device type
  -------------------------------------------------------------------------- */
  var isHandheld = null;
  var isTablet = null;
  var isSmartphone = null;
  var isDesktop = null;

  function checkDeviceType () {
    if (windowWidth >= 992) {
      isHandheld = isSmartphone = isTablet = false;
      isDesktop = true;
    } else if (windowWidth >= 768) {
      isHandheld = isTablet = true;
      isDesktop = isSmartphone = false;
    } else {
      isHandheld = isSmartphone = true;
      isDesktop = isTablet = false;
    }
  }
  checkDeviceType();

  (function(a){(jQuery.browser=jQuery.browser||{}).mobile=/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);


  /* Detect Safari
  -------------------------------------------------------------------------- */
  var isSafari = false;

  function checkSafari() {
    if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1)
    isSafari = true;
  }
  checkSafari();

  /* Detect IE
  -------------------------------------------------------------------------- */
  var isIE = false;

  function detectIE() {
    var rv = -1;
    var ua = window.navigator.userAgent;

    // Test values; Uncomment to check result …

    // IE 10
    // ua = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)';

    // IE 11
    // ua = 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko';

    // IE 12 / Spartan
    // ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36 Edge/12.0';

    // Edge (IE 12+)
    // ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10586';

    var msie = ua.indexOf('MSIE ');
    if (msie > 0) {
      // IE 10 or older => return version number
      rv = parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    var trident = ua.indexOf('Trident/');
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf('rv:');
      rv = parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    var edge = ua.indexOf('Edge/');
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      rv = parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
    }

    if (rv != -1)
    isIE = true;

    // other browser
    return rv;
  }

  var IEVersion = detectIE();


  /* Simplified version
  -------------------------------------------------------------------------- */
  function simplifiedVersion() {
    if (!isDesktop || jQuery.browser.mobile /*|| isSafari*/ || (isIE && IEVersion < 10))
    return true;
    else
    return false;
  }

  /* Prevent image dragging
  -------------------------------------------------------------------------- */
  $('body').on('mousedown', 'img', function() {
    return false;
  });

  /* Scroll to link
  -------------------------------------------------------------------------- */
  $('body').on('click', '.link-scrollto', function(e) {
    // Selectors
    var link = $(this);
    var target = $(link.attr('href'));
    if (target.length == 0)
    return;

    // Scroll
    $('html,body').animate(
      {
        scrollTop: target.offset().top
      },
      {
        duration: 1000,
        easing: 'easeInOutCubic'
      }
    );

    e.preventDefault();
  });

  /* Sound
  -------------------------------------------------------------------------- */
  var siteMusic = null;
  var siteMusicVolume = 0.15;
  function siteAudioInit() {
    if (simplifiedVersion())
    return;
    var mainTheme = $('#sound-music');
    siteMusic = new Howl({
      src: [mainTheme.attr('src')],
      loop: true,
      volume: siteMusicVolume
    });
    siteMusic.play();

    $('.sound').addClass('is-visible');
  }

  $('body').on('click', '.sound', function() {
    if (siteMusic == null)
    return;
    var button = $(this);
    if (button.hasClass('is-playing')) {
      siteMusicVolume = 0;
      siteMusic.fade(siteMusicVolume, 0, 1000);
      button.removeClass('is-playing');
    } else {
      siteMusicVolume = 0.15;
      siteMusic.fade(0, siteMusicVolume, 1000);
      button.addClass('is-playing');
    }
  })


  /* Videos
  -------------------------------------------------------------------------- */
  $('.sec-box .video-placeholder').each(function() {
    var placeholder = $(this);
    if (simplifiedVersion()) {
      var image = $('<div class="image" style="background-image: url(\'' + placeholder.attr('data-image') + '\');"></div>');
      image.replaceAll(placeholder);
    } else {
      var video = $('<video loop muted autoplay></video>');
      video.html('<source src="' + placeholder.attr('data-mp4') + '" type="video/mp4" />');
      video.replaceAll(placeholder);
    }
  });



  if (Modernizr.touchevents) {
    if(!$('.sec-video .video-placeholder').length) return;
    $('.sec-video .video-placeholder').each(function() {
      var placeholder = $(this);
      var video = $('<video poster="' + placeholder.attr('data-image') + '"></video>');
      video.html('<source src="' + placeholder.attr('data-mp4') + '" type="video/mp4" />');
      video.replaceAll(placeholder);
      plyr.setup('.sec-video video');
    });
  } else {
    $('body').on('click', '.sec-video .video-placeholder', function() {
      var placeholder = $(this);
      var layout = $('.sec-video');
      var video = $('<video autoplay></video>');
      video.html('<source src="' + placeholder.attr('data-mp4') + '" type="video/mp4" />');
      video.replaceAll(placeholder);
      plyr.setup('.sec-video video');

      layout.find('.video-container').each(function() {
        var player = $(this);
        var media = player.find('video').get(0);
        media.addEventListener('play', function() {
          if (siteMusic != null)
          siteMusic.fade(siteMusicVolume, 0, 1000);
        });
        media.addEventListener('pause', function() {
          if (siteMusic != null)
          siteMusic.fade(0, siteMusicVolume, 1000);
        });
      });
    });
  }


  /* =============================================================================
  GLOBAL
  ========================================================================== */

  /* Resize
  -------------------------------------------------------------------------- */
  function globalResize() {

    var hero = $('.section-hero');
    if(checkWindowWidth().width >= 992) {
      hero.css({
        height:checkWindowWidth().height
      })
    } else if (checkWindowWidth().width > 360) {
      hero.css({
        height:checkWindowWidth().height - 60
      })
    } else {
      hero.css({
        height:checkWindowWidth().height - 50
      })
    }

    // Fullscreen elements
    var fullscreen = $('.fullscreen');
    var newWidth = $(window).width();

    if (newWidth % 2 == 1)
    newWidth = newWidth + 1

    var containerWidht = newWidth - 148;

    if(checkWindowWidth().width >= 992) {
      fullscreen.css({
        width: containerWidht
      });
    } else {
      fullscreen.css({
        width: newWidth
      });
    }

    $('.box').trigger('box');
    $('.content-right').trigger('slidebg');
    $('.content-nav').trigger('stickynav');
    $('.page-container').trigger('footerFixed');

  }
  $('body').on('stickynav', '.content-nav', function() {
    var element = $(this);
    var newWidth = ($(window).width() - 148) * 0.75;
    newWidth = parseInt(newWidth);
    if(element.hasClass('gallery-nav')) {
      if (checkWindowWidth().width >= 992) {
        element.css({
          width: $(window).width() - 148
        });
      } else {
        element.css({
          width: $(window).width()
        });
      }
    } else {
      if (checkWindowWidth().width >= 1280) {
        element.css({
          width: newWidth - 1
        });
      } else if (checkWindowWidth().width >= 992) {
        element.css({
          width: $(window).width() - 148
        });
      } else {
        element.css({
          width: $(window).width()
        });
      }
    }

  });

  $('body').on('box', '.box', function() {
    var element = $(this);
    var container = element.parent();

    var newWidth = $(window).width();
    if (newWidth % 2 == 1)
    newWidth = newWidth + 1

    var containerWidht = newWidth - 148;

    if(checkWindowWidth().width >= 992) {
      element.css({
        width: containerWidht / 2,
        height: containerWidht / 2
      });
    } else if(checkWindowWidth().width >= 576) {
      element.css({
        width: newWidth,
        height: 'auto'
      });
    } else {
      element.css({
        width: $(window).width(),
        height: 'auto'
      });
    }
  });

  $('body').on('footerFixed', '.page-container', function() {
    var element = $(this);
    var footerBottom = element.find('.footer-bottom').height();

    if(checkWindowWidth().width >= 992) {
      element.css({
        paddingBottom: footerBottom
      });
    } else if(checkWindowWidth().width > 360) {
      element.css({
        paddingBottom: '60px'
      });
    } else {
      element.css({
        paddingBottom: '50px'
      });
    }
  });

  $('body').on('slidebg', '.content-right', function() {
    var element = $(this);
    var elementInner = element.find('.slider-container-right');
    var newWidth = ($(window).width() - 148) * 0.25;
    newWidth = parseInt(newWidth) + 2;

    if (newWidth % 2 == 1)
    newWidth = newWidth + 1

    if(checkWindowWidth().width >= 1280) {
      element.css({
        width: newWidth
      });
      elementInner.css({
        width: newWidth,
        height: $(window).height()
      });
    } else {
      element.css({
        width: 'auto'
      });
      elementInner.css({
        width: 'auto',
        height: 'auto'
      });
    }
  });


  /* Clock
  -------------------------------------------------------------------------- */
  function checkTime(i) {
    return (i < 10) ? "0" + i : i;
  }

  function startTime() {
    var today = new Date(),
    h = checkTime(today.getHours()),
    m = checkTime(today.getMinutes());
    //s = checkTime(today.getSeconds());
    //document.getElementById('time').innerHTML = h + ":" + m + ":" + s;
    document.getElementById('clock').setAttribute('data-hour',h);
    document.getElementById('clock').setAttribute('data-minute',m);
    //document.getElementsByTagName('svg')[0].setAttribute('data-second',s);
    //document.getElementById('clock').classList.toggle('dots-on');
  }

  if(checkWindowWidth().width >= 992){
    var t = setInterval(function () {
      startTime();
    }, 500);
  }

  function aboutHeroHide() {
    var container = $('.section-hero .hero-container');
    if (!simplifiedVersion()) {
      var scrollTop = windowScrollTop;
      var scale = scrollTop / windowHeight;
      if (scale > 1) {
        container.css({
          opacity: '',
          transform: ''
        })
      } else {
        container.css({
          opacity: 1 - (scale * 0.5),
          transform: 'translate3d(0, ' + (scrollTop / 2) + 'px, 0)'
        });
      }
    } else {
      container.css({
        opacity: '',
        transform: ''
      })
    }
  }
  if(checkWindowWidth().width >= 992){
    $(window).on('scroll', aboutHeroHide);
  }



  /* =============================================================================
  ENDING STUFF
  ========================================================================== */

  /* Trigger resize event to position everything after window load
  -------------------------------------------------------------------------- */
  $(window).on('load', function() {
    $(window).trigger('resize');
    siteAudioInit();
    $('select').selectric();
  });

});

function scrollContent() {

  var newScroll = jQuery(window).scrollTop();

  // To Load
  $toLoad.each(function(){
    if($('body').hasClass('loaded') && !$(this).hasClass('disabled')){
      var object = $(this);
      if(newScroll + $(window).height() * 0.9 > $(this).offset().top) {
        object.removeClass('no-anim');
        object.addClass('loaded');
      } else if(newScroll + $(window).height() < $(this).offset().top + 1) {
        object.addClass('no-anim');
        object.removeClass('loaded');
      }
    }
  });
}
site.load.push(scrollContent);
site.scroll.push(scrollContent);
