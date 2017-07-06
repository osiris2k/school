var pc_gallery = {
  gridSelector: '[pc-grid-container]',
  itemSelector: '.c-col-sm.pc-item-active',
  itemActiveClass: 'pc-item-active',
  shuffle: '',
  filterSelector: '[pc-filter-container] [data-group]',
  filterLabelSelector: '[pc-filter-container] .current-group a',
  filterContainerSelector: '[pc-filter-container] .gallery-filter ul',
  modalContentSelector: '#gallery-modal .slider-container',
  loadMoreSelector: '#gallery-loadmore',
  currentPage: 0,
  totalPage: 0,
  ITEM_PER_PAGE: 16,
  init: function(){
    if(!jQuery(pc_gallery.gridSelector).length) return;

    pc_gallery.initShuffle();
    pc_gallery.initFilter();
    pc_gallery.initLightbox();
    pc_gallery.initLoadMore();
  },
  initShuffle: function(){
    var Shuffle = window.shuffle;

    Shuffle.ShuffleItem.Scale.INITIAL = 0.9;
    Shuffle.ShuffleItem.Scale.HIDDEN = 0.9;
    Shuffle.ShuffleItem.Scale.VISIBLE = 1;

    Shuffle.options = {
      group: Shuffle.ALL_ITEMS, // Initial filter group.
      speed: 600, // Transition/animation speed (milliseconds).
      easing: 'cubic-bezier(0.645, 0.045, 0.355, 1)', // CSS easing function to use.
      itemSelector: '*', // e.g. '.picture-item'.
      sizer: null, // Element or selector string. Use an element to determine the size of columns and gutters.
      gutterWidth: 0, // A static number or function that tells the plugin how wide the gutters between columns are (in pixels).
      columnWidth: 0, // A static number or function that returns a number which tells the plugin how wide the columns are (in pixels).
      delimeter: null, // If your group is not json, and is comma delimeted, you could set delimeter to ','.
      buffer: 1, // Useful for percentage based heights when they might not always be exactly the same (in pixels).
      columnThreshold: 0.01, // Reading the width of elements isn't precise enough and can cause columns to jump between values.
      initialSort: null, // Shuffle can be initialized with a sort object. It is the same object given to the sort method.
      // throttle: throttle, // By default, shuffle will throttle resize events. This can be changed or removed.
      throttleTime: 300, // How often shuffle can be called on resize (in milliseconds).
      staggerAmount: 15, // Transition delay offset for each item in milliseconds.
      staggerAmountMax: 250, // Maximum stagger delay in milliseconds.
      useTransforms: true, // Whether to use transforms or absolute positioning.
    };


    var element = jQuery(pc_gallery.gridSelector)[0];
    var sizer = jQuery(pc_gallery.itemSelector)[0];

    pc_gallery.shuffle = new Shuffle(element, {
      itemSelector: pc_gallery.itemSelector,
      sizer: sizer
    });

    element.addEventListener(Shuffle.EventType.LAYOUT, function () {
      pc_sticky_menu.onResize();
    });
  },
  initFilter: function(){
    jQuery(pc_gallery.filterSelector).on('click',function(e){
      e.preventDefault();
      jQuery(pc_gallery.filterSelector).removeClass('active');
      jQuery(this).addClass('active');
      var group = jQuery(this).data('group');
      var html = jQuery(this).html();

      jQuery(pc_gallery.filterLabelSelector).html(html);
      pc_gallery.shuffle.filter(group);

      pc_gallery.closeSelect();

      // scrollto
      b_scroll_to_tweenmax.to(jQuery("#gallery").offset().top);
    });

    jQuery(pc_gallery.filterLabelSelector).on('click',function(e){
      e.preventDefault();
      if(checkWindowWidth().width >= 992) return;

      if(jQuery(pc_gallery.filterContainerSelector).is(':visible')){
        pc_gallery.closeSelect();
      }else{
        pc_gallery.openSelect();
      }

    });
  },
  initLoadMore: function(){
    pc_gallery.totalPage = Math.ceil(jQuery(pc_gallery.gridSelector).children().length / pc_gallery.ITEM_PER_PAGE);

    if(pc_gallery.totalPage > 1){
      jQuery(pc_gallery.loadMoreSelector).on('click',function(e){
        e.preventDefault();
        pc_gallery.loadMore();
      });
    }else{
      jQuery(pc_gallery.loadMoreSelector).parent().hide(0);
    }

    pc_gallery.loadMore();
  },
  loadMore: function(){
    pc_gallery.currentPage++;

    if(pc_gallery.currentPage >= pc_gallery.totalPage) jQuery(pc_gallery.loadMoreSelector).parent().hide(0);

    var list = jQuery(pc_gallery.gridSelector).children().not('.' + pc_gallery.itemActiveClass);
    var count = Math.min(pc_gallery.ITEM_PER_PAGE,list.length);

    var shuffleList = [];
    for( var i=0; i< count; i++){
      jQuery(list[i]).addClass(pc_gallery.itemActiveClass);
      shuffleList.push(list[i]);
    }


    pc_gallery.shuffle.options.speed = 0;
    pc_gallery.shuffle.add(shuffleList);
    pc_gallery.shuffle.options.speed = 600;
    // fixed for the loadmore item
    jQuery(pc_gallery.itemSelector).css('transition-duration','0.6s,0.6s');

  },
  openSelect: function(){
    if(checkWindowWidth().width >= 992) return;

    jQuery(pc_gallery.filterContainerSelector).slideDown(300);
    jQuery(pc_gallery.filterLabelSelector).addClass('open');
  },
  closeSelect: function(){
    if(checkWindowWidth().width >= 992) return;

    jQuery(pc_gallery.filterContainerSelector).slideUp(300);
    jQuery(pc_gallery.filterLabelSelector).removeClass('open');
  },
  initLightbox: function(){
    jQuery(document).on('click',pc_gallery.itemSelector,function(e){ console.log('click lightbox');
      e.preventDefault();
      var current = this;
      var currentIndex = 0;
      jQuery(pc_gallery.itemSelector + '.shuffle-item--visible').each(function(index,item){

        if(item === current){
          currentIndex = index;
        }
        var slideItem  = jQuery('<div></div>');
        slideItem.append(jQuery(item).find('figure').clone());
        jQuery(pc_gallery.modalContentSelector).append(slideItem);
      });
      jQuery(pc_gallery.modalContentSelector).slick({
        prevArrow:'<button class="slick-arrow slick-prev"><span></span></button>',
        nextArrow:'<button class="slick-arrow slick-next"><span></span></button>'
      });
      jQuery(pc_gallery.modalContentSelector).slick('slickGoTo',currentIndex,true);
      pc_gallery.updateGallerySize();
      pc_modal.showModal('#gallery-modal');
    });
  },
  updateGallerySize: function(){
    // update img size
    var imageGallery = jQuery(pc_gallery.modalContentSelector).find('.slick-slide img');
    var ratio = 0.787179487179487;
    var newWidth = checkWindowWidth().height * ratio;

    imageGallery.css({
      width: newWidth,
      height: checkWindowWidth().height
    });
  },
  onResize: function(){
    if(!jQuery(pc_gallery.gridSelector).length) return;

    // toggle select
    if(checkWindowWidth().width >= 992){
      pc_gallery.openSelect();
      jQuery(pc_gallery.filterContainerSelector).css('display','flex');
    }else{
      jQuery(pc_gallery.filterContainerSelector).css('display','none');
      pc_gallery.closeSelect();
    }

    // update modal size
    if(jQuery('#gallery-modal:visible').length){
      pc_gallery.updateGallerySize();
    }

    // fixed space between grid
    var ratio = (checkWindowWidth().width >= 992) ? 0.25 : 0.5;
    var gridSize = Math.ceil(jQuery(pc_gallery.gridSelector).width() * ratio);

    jQuery('.section-gallery .c-row .c-col-sm').width(gridSize);
    pc_gallery.shuffle.options.columnWidth = gridSize;
    pc_gallery.shuffle.update();
  }
};

site.ready.push(pc_gallery.init);
site.resize.push(pc_gallery.onResize);
