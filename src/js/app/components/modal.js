var pc_modal = {
  activeClass: 'is-visible',
  modalSelector: '[pc-modal]',
  closeSelector: '[pc-modal-close]',
  triggerData: 'modal-trigger',
  overlaySelector: '.modal-overlay',
  init: function() {
    // modal
    $(pc_modal.modalSelector).each(function() {
      var modal = $(this);
      $(this).hide();
      $(this).on('click', function(e) {
        // normal
        if (e.target == $(this)[0]) {
          pc_modal.closeModal('#' + $(this).attr('id'));
        }
      });
    });

    // trigger
    $('[data-' + pc_modal.triggerData + ']').each(function() {
      $(this).on('click', function(e) {
        e.preventDefault();
        var id = $(this).data(pc_modal.triggerData);
        pc_modal.showModal(id);
      });
    });

    // close
    $(pc_modal.closeSelector).each(function() {
      $(this).on('click', function(e) {
        e.preventDefault();
        var modalId = $(this).parents(pc_modal.modalSelector).attr('id');
        pc_modal.closeModal('#' + modalId);
      });
    });

    $(pc_modal.overlaySelector).each(function() {
      $(this).on('click', function(e) {
        e.preventDefault();
        var modalId = $(this).parents(pc_modal.modalSelector).attr('id');
        pc_modal.closeModal('#' + modalId);
      });
    });
  },
  showModal: function(id) {
    if ($(id).length) {
      // clear animation
      $(id).removeClass(pc_modal.activeClass);

      $(id).show(0, function() {
        $(this).addClass(pc_modal.activeClass);

        // slide
        $(this).find('.slider-container').each(function(){
          var slick = $(this).slick('getSlick');
          slick.refresh();
        });
      });

    }
  },
  closeModal: function(id) {
    if ($(id).length) {

      $(id).removeClass(pc_modal.activeClass);
      $(id).hide();

      $(id).find('.slider-container').each(function(){
        var slick = $(this).slick('getSlick');
        slick.destroy();
        $(this).empty();
      });
    }
  },
  closeAllModal: function() {
    $('[data-' + pc_modal.triggerData + ']').each(function() {
      pc_modal.closeModal($(this).data(pc_modal.triggerData));
    });
  },
  setCloseModal: function(id) {
    $(id).hide().removeClass(pc_modal.activeClass);
    $('body').removeClass('overflow-hidden');
    $(id).find('.video-embed').each(function() {
      $(this).html("");
    });
  }
};

site.ready.push(pc_modal.init);
