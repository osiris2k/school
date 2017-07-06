var ms_parallax = {
  controller: '',
  selector: '[ms-parallax]',
  init: function(){
    if($(ms_parallax.selector).length){
      ms_parallax.controller = new ScrollMagic.Controller();

      var scene = new ScrollMagic.Scene({
        triggerElement: jQuery('#page')[0],
        triggerHook: 0,
        duration: '200%'
      })
      // .addIndicators({name: "1 (duration: 0)"})
      .addTo(ms_parallax.controller);

      var yData = jQuery(window).height() * 0.95;
      // scene.setTween(jQuery(this)[0], {y: yData, ease: Linear.easeNone});
      scene.setTween(jQuery(ms_parallax.selector), {y: yData, ease: Linear.easeNone});
    }
  }
}

site.ready.push(ms_parallax.init);
