var b_loader = {
  containerSelector:'[b-loader]',
  percentSelector:'[b-percent]',
  $container: '',
  $percent: '',
  paths: '',
  imgs: '',
  imgsLoaded: '',
  isLoaded: false,
  animateDuration: 1500,
  init: function(){
    // reset page to top
    $('body').css('position','fixed');

    // assign variables
    b_loader.$container = $(b_loader.containerSelector);
    b_loader.$percent = b_loader.$container.find(b_loader.percentSelector);
    b_loader.imgs = jQuery();
    b_loader.imgsLoaded = 0;

    // collect img
    b_loader.paths = [];

    $('*').each(function(){
      // bg img
      var bgPath = $(this).css('background-image');
      if(bgPath != 'none' && bgPath.indexOf('linear-gradient') == -1){
        var path = $(this).css('background-image');
        path = path.replace('url(','').replace(')','').replace(/\"/gi, "");
        b_loader.addImg(path);
      }

      // img tag
      if($(this).is('img[src]')){
        b_loader.addImg($(this).attr('src'));
      }
    });

    // add loaded event
    b_loader.paths.forEach(function(item,i){
      b_loader.imgs = b_loader.imgs.add($('<img src="' + item + '"/>'));
    });

    // start load
    if (b_loader.imgs.length) {
      b_loader.onProgress();
      b_loader.imgs.on('load',b_loader.onImgLoaded);
      b_loader.imgs.on('error',b_loader.onError);

    }else{
      b_loader.onLoadComplete();
    }
  },
  addImg:function(path){
    if(path.indexOf('.svg') == -1 && path != ''){
      b_loader.paths.push(path);
    }
  },
  onError:function(e){
    b_loader.imgsLoaded++;
    b_loader.onProgress();
  },
  onImgLoaded:function(e){
    b_loader.imgsLoaded++;
    b_loader.onProgress();
  },
  onProgress:function(e){
    var currentPercent = parseInt((b_loader.imgsLoaded / b_loader.imgs.length) * 100);

    if (b_loader.$percent.length) b_loader.$percent.html(currentPercent);

    if(currentPercent >= 100){
      b_loader.onLoadComplete();
    }
  },
  onLoadComplete:function(){
    b_loader.isLoaded = true;
    b_loader.imgs.off('load error');
    b_loader.$percent.html(100);
    b_loader.onShow();
  },
  onShow:function(){
    if(b_loader.isLoaded && site.isReady){

      setTimeout(function(){
        // release page to normal style
        $('body').attr('style','');

        $('body').addClass('loaded');
        b_loader.loadOut();


        b_loader.loadOut();
        var delayTime = b_loader.animateDuration - 1000;
        setTimeout(site.onLoad,delayTime);
      },1000);
    }
  },
  loadOut: function(){
    jQuery('.loading').removeClass('load-in').addClass('load-out');

    b_loader.$container.delay(b_loader.animateDuration).hide(0,function(){
      $('.loading').removeClass('load-out');
    });
  },
  loadIn: function(callback){
    b_loader.$container.show(0,function(){
      jQuery('.loading').removeClass('load-out').addClass('load-in');
      setTimeout(function(){
        callback();
      },b_loader.animateDuration);
    });
  }
};

site.ready.push(b_loader.init);
