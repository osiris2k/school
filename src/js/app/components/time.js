// get date form client computer
// new Date(year, month, day, hours, minutes, seconds, milliseconds);

// night
// var CURRENT_DATE = new Date(2017,3,15,23);
// day
// var CURRENT_DATE = new Date(2017,3,15,8);

// current time
// var CURRENT_DATE = new Date();

jQuery(document).ready(function($){

  var time = mode(CURRENT_DATE);
  time = checkTimeURL(time);
  updateTheme(time);
  updateLoading(time);
  checkFooter();
});

function checkFooter(){
  var url = window.location.href;
  if(url.search('/day') == -1 && url.search('/night') == -1){
    $('body').addClass('default');
  } else {
    $('body').addClass('home');
  }
}

function checkTimeURL(time){
  var url = window.location.href;
  if(url.search('/day') != -1){
    time = 'day';
  }

  if(url.search('/night') != -1){
    time = 'night';
  }

  return time;
}

function updateLoading(time){

  if( time == 'day' ) {
    $('body').removeClass('home-night-anim').addClass('home-day-anim');
  } else {
    $('body').removeClass('home-day-anim').addClass('home-night-anim');
  }
}

function updateTheme(time) {

  if( time == 'day' ) {
    $('body').removeClass('home-night').addClass('home-day');
    $('.menu-switch-left.menu-switch-desktop').attr('data-time', 'day').find('span').html('day');
    $('.menu-switch-right.menu-switch-desktop').attr('data-time', 'night').find('span').html('night');
  } else {
    $('body').removeClass('home-day').addClass('home-night');
    $('.menu-switch-left.menu-switch-desktop').attr('data-time', 'night').find('span').html('night');
    $('.menu-switch-right.menu-switch-desktop').attr('data-time', 'day').find('span').html('day');
  }
}

function mode(now) {

  if((typeof now) == "string"){
    return now;
  }

  // detect
  var day = now.getDay();
  var hours = now.getHours();

  var msg = "day";

  switch(day){
    // SUN - THU (18: 00)
    case 1:
    case 2:
    case 3:
    case 4:
    case 5:
    if( hours >= 18){
      msg = "night";
    }
    break;
    // FRI - SAT (2: 00)
    case 6:
    case 0:
    if( hours <= 2 || hours >= 18){
      msg = "night";
    }
    break;
  }

  return(msg);
}
