var pc_video = {
  init: function(){
    $('body').on('click', '.video-block', function() {
      var element = $(this);




      if(element.hasClass('video-is-open')) {

        pc_video.close(element);
      } else {
        pc_video.open(element);
      }

      //videoWrapper.find('.fillcontainer').trigger('fill');
    });

    $('body').on('fill', '.fillcontainer', function() {
        var element = $(this);
        var container = element.parent();
        var ratio = element.attr('data-ratio');
        if (typeof (ratio) == 'undefined') {
            ratio = element.width() / element.height();
            element.attr('data-ratio', ratio);
        }
        var newWidth = container.width();
        var newHeight = newWidth / ratio;
        if (newHeight < container.height()) {
            newHeight = container.height();
            newWidth = newHeight * ratio;
        }
        element.css({
            width: newWidth + 1,
            height: newHeight
        });
        element.css({
            top: (container.height() - newHeight) / 2,
            left: (container.width() - newWidth) / 2
        });
    });
  },
  open: function($vdoContainer){
    if($vdoContainer.find('video').length) return;

    var cell = $vdoContainer.closest('.sec-video');
    var videoWrapper = $vdoContainer.find('.video-block-inner');
    var placeholder = $vdoContainer.find('.video-placeholder');
    var video = $('<video loop muted autoplay class="fillcontainer"></video>');
    video.html('<source src="' + placeholder.attr('data-mp4') + '" type="video/mp4" />');
    videoWrapper.append(video);
    $vdoContainer.delay(200).show(0,function(){
      $vdoContainer.addClass('video-is-open');
    });
  },
  close: function($vdoContainer){
    if(!$vdoContainer.hasClass('video-is-open')) return;

    $vdoContainer.removeClass('video-is-open');
    $vdoContainer.find('video').delay(400).hide(0,function(){
      $vdoContainer.find('video').remove();
    });
  }
};

//site.ready.push(pc_video.init);
