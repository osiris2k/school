var b_pikaday = {
  selector: '[b-calendar]',
  init:function(){

    $(b_pikaday.selector).each(function(){

      if(jQuery(this).is("input[name='r-date']")){
        pikadayResponsive(jQuery(this), {
          placeholder: "",
          format: 'D MMM YYYY',
          outputFormat: "YYYYMMDD"
        });
      }
    });
  }
}

site.ready.push(b_pikaday.init);
