var b_parallax_bg_scrollmagic = {
  controller:'',
  parallaxAttr: 'b-parallax',
  TRIGGER_DEFUALT: 1,
  bgAttr: 'b-parallax-bg',
  RATIO_DEFAULT: 0.8,
  scenes: [],
  elementSelector: '[b-parallax-element]',
  durationData: 'duration',
  toData: 'to',
  fromData: 'from',
  defaultData:{
    "ease": "Linear.easeNone"
  },
  init:function(){

    var selector = '[' + b_parallax_bg_scrollmagic.parallaxAttr + ']';
    if($(selector).length){

      b_parallax_bg_scrollmagic.controller = new ScrollMagic.Controller();

      $(selector).each(function(){
        var $parallax = $(this);
        var triggerRatio = B_.GET_NUMBER($parallax.attr(b_parallax_bg_scrollmagic.parallaxAttr),b_parallax_bg_scrollmagic.TRIGGER_DEFUALT);

        // bg
        var heightRatio = ($parallax.height() / $(window).height());
        var duration = ((heightRatio * 100) + (triggerRatio * 100));
        var bgSelector = '[' + b_parallax_bg_scrollmagic.bgAttr + ']';
        $parallax.find(bgSelector).each(function(){

          var scene = new ScrollMagic.Scene({
          					triggerElement: $parallax[0],
                    triggerHook: triggerRatio,
                    duration: duration + '%'
          				})
          				// .addIndicators({name: "bottom: (duration: 0)"})
          				.addTo(b_parallax_bg_scrollmagic.controller);

          b_parallax_bg_scrollmagic.addParallaxBg(scene,$parallax,$(this),triggerRatio);
          b_parallax_bg_scrollmagic.scenes.push(scene);
        });

        // element
        $parallax.find(b_parallax_bg_scrollmagic.elementSelector).each(function(){
            var defaultDuration = (triggerRatio != 0) ? triggerRatio : heightRatio;
            var elementDuration = B_.GET_NUMBER($(this).data(b_parallax_bg_scrollmagic.durationData),defaultDuration);

            elementDuration *= 100;

            var tl = new TimelineMax();

            // From Data
            if($(this).data(b_parallax_bg_scrollmagic.fromData) != undefined){
              var fromData = $(this).data(b_parallax_bg_scrollmagic.fromData);
              fromData = $.extend({},b_parallax_bg_scrollmagic.defaultData,fromData);

              tl.add(TweenMax.from($(this),1,fromData));
            }

            // To Data
            if($(this).data(b_parallax_bg_scrollmagic.toData) != undefined){
              var toData = $(this).data(b_parallax_bg_scrollmagic.toData);
              toData = $.extend({},b_parallax_bg_scrollmagic.defaultData,toData);

              tl.add(TweenMax.to($(this),1,toData));
            }

            var scene = new ScrollMagic.Scene({
                      triggerElement: $parallax[0],
                      triggerHook: triggerRatio,
                      duration: elementDuration + '%'
                    })
                    .setTween(tl)
                    // .addIndicators({name: "parallax element: (duration: 0)"})
                    .addTo(b_parallax_bg_scrollmagic.controller);
        });

      });
    }
  },
  addParallaxBg: function(scene,$trigger,$element,triggerRatio){
    var parallaxRatio = B_.GET_NUMBER($element.attr(b_parallax_bg_scrollmagic.bgAttr),b_parallax_bg_scrollmagic.RATIO_DEFAULT);

    var heightRatio = $trigger.height() / $(window).height();

    var yData = parseInt($(window).height() * (1 - parallaxRatio));
    var targetHeight = $trigger.innerHeight();
    var heightFactor = parseInt(($(window).height()-targetHeight) * (1 - parallaxRatio));
    targetHeight += heightFactor;

    if(triggerRatio != 0){
      var targetTop = (-yData * 0.5) - (heightFactor * 0.5);

      $element.css({
        'top': targetTop,
        'height': targetHeight
      });
    }else{
      yData = parseInt($trigger.height() * (1 - parallaxRatio));
    }

    scene.setTween($element[0], {y: yData, ease: Linear.easeNone});

    if(!scene.element) scene.element = $element;
  },
  updateScene:function(){
    b_parallax_bg_scrollmagic.scenes.forEach(function(scene,index){
      scene.removeTween(true);
      b_parallax_bg_scrollmagic.addParallaxBg(scene,$(scene.triggerElement()),scene.element,scene.triggerHook());
    });
  }
}

site.ready.push(b_parallax_bg_scrollmagic.init);
site.resize.push(b_parallax_bg_scrollmagic.updateScene);
