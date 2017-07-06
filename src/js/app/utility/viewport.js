var b_viewport = {
  heightAttr: 'b-vh',
  widthAttr: 'b-vw',
  update:function(){
    $('[' + b_viewport.heightAttr + ']').each(function(){
      var percent = B_.GET_NUMBER($(this).attr(b_viewport.heightAttr),1);
      var wh = window.innerHeight || $(window).height();
      var targetHeight =  wh * percent;

        $(this).height(targetHeight);


    });

    $('[' + b_viewport.widthAttr + ']').each(function(){
      var percent = B_.GET_NUMBER($(this).attr(b_viewport.widthAttr),1);
      var targetWidth = $(window).width() * percent;
      $(this).width(targetWidth);
    });
  },
  inview:function($element){
    var top = $(window).scrollTop();
    var bottom = top + $(window).height();
    var trigger = $element.offset().top
    if($element.height() < $(window).height() * 0.8){
      trigger += $element.height();
    }
    return (trigger >= top && trigger <= bottom) ? true : false;
  }
}

site.ready.push(b_viewport.update);
site.resize.push(b_viewport.update);
