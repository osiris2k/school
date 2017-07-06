var pc_page = {
  pageSelector: '[pc-page]',
  init: function(){
    // forece refresh on back button (safari,firefox)
    jQuery(window).bind("pageshow", function(event) {
        if (event.originalEvent.persisted) {
            window.location.reload();
        }
    });

    jQuery(pc_page.pageSelector).on('click',function(e){
      e.preventDefault();

      var url = '';

      var time = jQuery(this).data('time') || '';

      if(time){
        url = '/' + time;

        if( time == 'day' ) {
          $('body').removeClass('home-night-anim').addClass('home-day-anim');
        } else {
          $('body').removeClass('home-day-anim').addClass('home-night-anim');
        }
      }else{
        var currentMode = mode(CURRENT_DATE);
        updateLoading(currentMode);

        url = jQuery(this).attr('pc-page');
      }

      b_loader.loadIn(function(){
        window.location.href = url;
      });

    });
  }
};

site.ready.push(pc_page.init);
