(function ($) {
  'use strict';

  //  Helper methods
  var tab_loading = {show: function(){jQuery('.asl-p-cont > .loading').removeClass('hide');},hide: function(){jQuery('.asl-p-cont > .loading').addClass('hide');}};
  var PTL_AJAX = function (_url, _data, _callback, _option, _error_cb) {_data   = _data == null ? {}: _data; if(window['PTL_REMOTE'] && PTL_REMOTE.nounce) { _data['ptl-nounce'] = PTL_REMOTE.nounce; } _option = _option == null ? {}: _option;  var i   = _option.dataType ? _option.dataType : "json";if(_option.submit) {_option.submit.button('loading');}tab_loading.show();var s = {type : _option.type ? _option.type : "POST",url : _url,data : _data,dataType : i,error: function (_d) {tab_loading.hide();if(_error_cb)_error_cb(_d);} ,success: function (_d) {tab_loading.hide();_callback(_d);}};var o = jQuery.ajax(s);};

  var ptl_feedback_popup = {
    elements: {},
    /**
     * [bindEvents description]
     * @return {[type]} [description]
     */
    bindEvents: function() {
      
      var self = this;

      self.elements.$deactivator_link.on('click', function (event) {
        event.preventDefault();
        self.showModal();
      });      

      $(document).on('click','#ptl-feedback-submit',function(){
        self.sendFeedback();
      });      

      $(document).on('click','#ptl-feedback-skip',function(){
        self.deactivate();
      });

    },
    /**
     * [deactivate description]
     * @return {[type]} [description]
     */
    deactivate: function() {
      location.href = this.elements.$deactivator_link.attr('href');
    },
    /**
     * [ptl_escape_listener description]
     * @param  {[type]} e [description]
     * @return {[type]}   [description]
     */
    ptl_escape_listener: function(e){

      var self = this;

      if(e.key === "Escape") {
        this.ptl_close_modal(self.elements.ptl_modal);
      }
    },
    /**
     * [ptl_close_modal description]
     * @return {[type]} [description]
     */
    ptl_close_modal: function(){

      var self = this;

      window.setTimeout(function() {
        $(self.elements.ptl_modal).ptl_modal('hide');
      }, 300);

      //  Clear the listener for Escape
      document.removeEventListener('keyup', self.ptl_escape_listener);
    },
    /**
     * [showModal description]
     * @return {[type]} [description]
     */
    showModal: function() {
      
      var self = this;

      $(self.elements.ptl_modal).ptl_modal('show');
    },
    /**
     * [sendFeedback description]
     * @return {[type]} [description]
     */
    sendFeedback: function() {
      
      var self = this,
          formData = $(self.elements.ptl_modal.ptl_form).PTLSerializeObject();
        var data = {
          'action': 'ajax_post_timeline_deactivate_feedback',
          'formData': formData,
        };
      $.post(ajaxurl,data,function(_response){
        self.deactivate();
      });
    },
    /**
     * [init description]
     * @return {[type]} [description]
     */
    init: function() {

      this.elements.$deactivator_link  = $('#the-list').find('[data-slug="post-timeline"] span.deactivate a');
      this.elements.ptl_modal          = document.querySelector('#ptl-feedback-popup');
      this.elements.ptl_modal.ptl_form = document.querySelector('#ptl-feedback-form');
      
      this.bindEvents();
    }
  };

  $(function() {
    
    ptl_feedback_popup.init();
  });

})(jQuery);

