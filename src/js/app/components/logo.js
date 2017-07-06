var pc_logo = {
  $logo: '',
  init: function(){
    pc_logo.$logo = jQuery('.logo');
  },
  update: function(){
    if(!pc_logo.$logo.length) return;

    var element = elementFromPoint(jQuery('.logo').offset().left,jQuery('.logo').position().top + jQuery('.logo').height() / 2);
    var container = $(element).closest('[data-logo]');

    if(container.length){
      var color = container.attr('data-logo');
      switch(color){
        case 'light':
          pc_logo.$logo.removeClass('is-dark').addClass('is-light');
          break;
        case 'dark':
          pc_logo.$logo.removeClass('is-light').addClass('is-dark');
          break;
        default:
          pc_logo.$logo.removeClass('is-light is-dark');
          break;
      }
    }
  }
};
/* Head background check
-------------------------------------------------------------------------- */
function headBackgroundCheck() {
  var elements = $('.logo');
  // if (nav.hasClass('is-opened'))
  // 	return;

  elements.each(function() {
    var element = $(this);
    var position = element.position();

    var behind = elementFromPoint(position.left+10, position.top+element.height());
    var container = $(behind).closest('[data-logo]');

    //console.log(behind);

    if (container.length == 1) {
      var color = container.attr('data-logo');
      if (color == 'dark') {
        element.removeClass('is-light');
        element.addClass('is-dark');
      } else {
        element.removeClass('is-light is-dark');
      }
    } else {
      element.removeClass('is-light is-dark');
    }
  });
}
site.ready.push(headBackgroundCheck);
site.resize.push(headBackgroundCheck);
site.scroll.push(headBackgroundCheck);
//
// site.ready.push(pc_logo.init);
// site.scroll.push(pc_logo.update);
