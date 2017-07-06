var TABLET_SIZE = 1024;
var MOBILE_SIZE = 480;

var site = {
  isReady: false,
  ready: [],
  resize: [],
  scroll: [],
  load: [],
  onReady: function(){
    site.ready.forEach(function(item,index){
      item();
    });
    site.isReady = true;

    if(typeof b_loader != "undefined") b_loader.onShow();
  },
  onLoad: function(){ // call by loader
    site.load.forEach(function(item,index){
      item();
    });
  },
  onResize:function(){
    site.resize.forEach(function(item,index){
      item();
    });
  },
  onScroll:function(){
    site.scroll.forEach(function(item,index){
      item();
    });
  }
};

$(document).ready(site.onReady);

if(Modernizr.mobile){
  $(window).on('orientationchange', function() {
    requestAnimFrame(site.onResize);
  });
}else{
  $(window).on('resize',function(){
  	requestAnimFrame(site.onResize);
  });
}

$(window).on('scroll',function(){
	requestAnimFrame(site.onScroll);
});
