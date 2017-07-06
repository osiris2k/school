var b_select_picker = {
  selector: '[b-select-picker]',
  selectPickerData: 'select-picker',
  defaultOption: {
    placeholder: 'Please Select..',
    search: false,
    limit: 0,
    tags: ''
  },
  searchSelector: '[b-hide-search]',
  tagSelector: '[b-tags]',
  maxResultData: 'max',
  init: function(){
    $(b_select_picker.selector).each(function(){
      $(this).attr('style','width:100%;');

      var dataAttr = $(this).data(b_select_picker.selectPickerData) || {};
      var data = $.extend({},b_select_picker.defaultOption,dataAttr);
      $(this).data(b_select_picker.selectPickerData,data);

      $(this).data('placeholder',data.placeholder);

      var isHideSearch = (data.search) ? 0 : -1;
      if(data.tags){
        // tags support
        var tagsData = data.tags.split(',');
        $(this).select2({
          tags: tagsData
        })
      }else{
        $(this).select2({
          allowClear: false,
          minimumResultsForSearch: isHideSearch,
          maximumSelectionSize: data.limit,
          templateResult: b_select_picker.formatState,
          templateSelection: b_select_picker.formatState
        });
      }
        
        // remove border
        if($(this).is('[name=lang]')){
            $(this).on("select2:open", function (e) { 
            $('.select2-container--open').addClass('remove-border');
         });
        }  
    });
  },
 formatState: function(state){
 if (!$(state.element).data('flag')) { return state.text; }
  var $state = $(
    '<div class="select-lang"><img src="' + $(state.element).data('flag') + '.png" class="img-flag" /><span>' + state.text + '</span></div>'
  );
  return $state;
 }
}

site.ready.push(b_select_picker.init);

