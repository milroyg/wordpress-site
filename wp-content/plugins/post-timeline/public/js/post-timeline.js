/*!
 * imagesLoaded PACKAGED v4.1.4
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

!function(e,t){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",t):"object"==typeof module&&module.exports?module.exports=t():e.EvEmitter=t()}("undefined"!=typeof window?window:this,function(){function e(){}var t=e.prototype;return t.on=function(e,t){if(e&&t){var i=this._events=this._events||{},n=i[e]=i[e]||[];return n.indexOf(t)==-1&&n.push(t),this}},t.once=function(e,t){if(e&&t){this.on(e,t);var i=this._onceEvents=this._onceEvents||{},n=i[e]=i[e]||{};return n[t]=!0,this}},t.off=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){var n=i.indexOf(t);return n!=-1&&i.splice(n,1),this}},t.emitEvent=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){i=i.slice(0),t=t||[];for(var n=this._onceEvents&&this._onceEvents[e],o=0;o<i.length;o++){var r=i[o],s=n&&n[r];s&&(this.off(e,r),delete n[r]),r.apply(this,t)}return this}},t.allOff=function(){delete this._events,delete this._onceEvents},e}),function(e,t){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(i){return t(e,i)}):"object"==typeof module&&module.exports?module.exports=t(e,require("ev-emitter")):e.imagesLoaded=t(e,e.EvEmitter)}("undefined"!=typeof window?window:this,function(e,t){function i(e,t){for(var i in t)e[i]=t[i];return e}function n(e){if(Array.isArray(e))return e;var t="object"==typeof e&&"number"==typeof e.length;return t?d.call(e):[e]}function o(e,t,r){if(!(this instanceof o))return new o(e,t,r);var s=e;return"string"==typeof e&&(s=document.querySelectorAll(e)),s?(this.elements=n(s),this.options=i({},this.options),"function"==typeof t?r=t:i(this.options,t),r&&this.on("always",r),this.getImages(),h&&(this.jqDeferred=new h.Deferred),void setTimeout(this.check.bind(this))):void a.error("Bad element for imagesLoaded "+(s||e))}function r(e){this.img=e}function s(e,t){this.url=e,this.element=t,this.img=new Image}var h=e.jQuery,a=e.console,d=Array.prototype.slice;o.prototype=Object.create(t.prototype),o.prototype.options={},o.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},o.prototype.addElementImages=function(e){"IMG"==e.nodeName&&this.addImage(e),this.options.background===!0&&this.addElementBackgroundImages(e);var t=e.nodeType;if(t&&u[t]){for(var i=e.querySelectorAll("img"),n=0;n<i.length;n++){var o=i[n];this.addImage(o)}if("string"==typeof this.options.background){var r=e.querySelectorAll(this.options.background);for(n=0;n<r.length;n++){var s=r[n];this.addElementBackgroundImages(s)}}}};var u={1:!0,9:!0,11:!0};return o.prototype.addElementBackgroundImages=function(e){var t=getComputedStyle(e);if(t)for(var i=/url\((['"])?(.*?)\1\)/gi,n=i.exec(t.backgroundImage);null!==n;){var o=n&&n[2];o&&this.addBackground(o,e),n=i.exec(t.backgroundImage)}},o.prototype.addImage=function(e){var t=new r(e);this.images.push(t)},o.prototype.addBackground=function(e,t){var i=new s(e,t);this.images.push(i)},o.prototype.check=function(){function e(e,i,n){setTimeout(function(){t.progress(e,i,n)})}var t=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(t){t.once("progress",e),t.check()}):void this.complete()},o.prototype.progress=function(e,t,i){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded,this.emitEvent("progress",[this,e,t]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,e),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&a&&a.log("progress: "+i,e,t)},o.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(e,[this]),this.emitEvent("always",[this]),this.jqDeferred){var t=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[t](this)}},r.prototype=Object.create(t.prototype),r.prototype.check=function(){var e=this.getIsImageComplete();return e?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},r.prototype.getIsImageComplete=function(){return this.img.complete&&this.img.naturalWidth},r.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.img,t])},r.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},r.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},r.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},r.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype=Object.create(r.prototype),s.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var e=this.getIsImageComplete();e&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},s.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.element,t])},o.makeJQueryPlugin=function(t){t=t||e.jQuery,t&&(h=t,h.fn.imagesLoaded=function(e,t){var i=new o(this,e,t);return i.jqDeferred.promise(h(this))})},o.makeJQueryPlugin(),o});


/* Popout for Gallery */
(function ($) {
  $.fn.popout = function (options) {
    const settings = $.extend({
      keyboard: true,
      backdrop: true,
      focus: true
    }, options);

    return this.each(function () {
      const $popout = $(this);
      const $close = $popout.find('.ptl-popout-close');

      // Skip if already shown
      if ($popout.hasClass('show')) return;

      // Fade support
      const useFade = $popout.hasClass('fade');

      // Show the popout
      $popout.show(0, function () {
        if (useFade) {
          setTimeout(() => $popout.addClass('show'), 10); // trigger fade transition
        } else {
          $popout.addClass('show');
        }

        $popout.trigger('shown.bs.popout');
      });

      // Auto-focus if needed
      if (settings.focus) {
        $popout.find('iframe, button, input, [tabindex]').first().focus();
      }

      // Close logic
      function hidePopout() {
        $popout.removeClass('show');

        const remove = () => {
          $popout.hide();
          $popout.trigger('hidden.bs.popout');
        };

        if (useFade) {
          setTimeout(remove, 300); // matches Bootstrap's fade timing
        } else {
          remove();
        }

        // Clean up
        $(document).off('keydown.popout');
        $popout.off('click.popout');
        $close.off('click.popout');
      }

      // Click on close button
      $close.on('click.popout', hidePopout);

      // Click on backdrop
      if (settings.backdrop) {
        $popout.on('click.popout', function (e) {
          if ($(e.target).is($popout)) {
            hidePopout();
          }
        });
      }

      // ESC key to close
      if (settings.keyboard) {
        $(document).on('keydown.popout', function (e) {
          if (e.key === "Escape") {
            hidePopout();
          }
        });
      }
    });
  };
})(jQuery);

(function($) {
    var items = new Array(), current = 0;

    /* Callbacks */
    var OnStep     = function(Percent) { };
    var OnComplete = function()        { };

    // Get all images from css and <img> tag
    var getImages = function(element) {
        $(element).find('img').each(function() {
            var url = "";
            
            if ($(this).get(0).nodeName.toLowerCase() == 'img' && typeof($(this).attr('data-src')) != 'undefined' && $(this).attr('data-src') != 'null') {
                url = $(this).attr('data-src');

                items.push(this);
            }
        });
    };

    var loadComplete = function() {
        current++;
   
        OnStep(Math.round((current / items.length) * 100));
   
        if (current == items.length) {
            OnComplete();
        }
    };

    var loadImg = function(_image) {

        var img = new Image;

        if (_image.dataset.src != 'null') {
          
          img.src = _image.dataset.src;

          img.onload = function() {
            
            _image.src         = img.src;
            _image.dataset.src = null;
            loadComplete();
          };
        }

    };

    $.fn.ptl_prefetch = function(options) {

        return this.each(function() {
            
            /* Set Callbacks */
            if (typeof(options.OnStep) !== "undefined") OnStep = options.OnStep;
            if (typeof(options.OnComplete) !== "undefined") OnComplete = options.OnComplete;

            getImages(this);

            for (var i = 0; i < items.length; i++) {
                loadImg(items[i]);
            }
        });
    };
})(jQuery);

(function( $ ) {

  'use strict';

  /* PTL_AJAX Helper Function */
  var tab_loading = {show: function(){jQuery('.asl-p-cont > .loading').removeClass('hide');},hide: function(){jQuery('.asl-p-cont > .loading').addClass('hide');}};
  var PTL_AJAX = function (_url, _data, _callback, _option) {_data   = _data == null ? {}: _data;_option = _option == null ? {}: _option; var i   = _option.dataType ? _option.dataType : "json";if(_option.submit) {_option.submit.button('loading');}tab_loading.show();var s = {type : _option.type ? _option.type : "POST",url : _url,data : _data,dataType : i,success : function (_d) {tab_loading.hide();_callback(_d);}};var o = jQuery.ajax(s);};
  function displayMessage(message,_form,_class,_no_animation){if(!_class) _class = 'alert alert-danger';_form.empty();var message = _form.message(message, {append: true,arrow: 'bottom',classes: [_class],animate: true});if(!_no_animation)jQuery('html, body').animate({scrollTop: _form.offset().top}, 'slow');};

  /**
   * [getKeyByIndex description]
   * @param  {[type]} obj   [description]
   * @param  {[type]} index [description]
   * @return {[type]}       [description]
   */
  function getKeyByIndex(obj, index) {
    var i = 0;

    if(obj)
      for (var attr in obj) {

        if(obj.hasOwnProperty(atfhtr)) {

            if (index === i){
              return attr;
            }
            i++;
        }
      }
    
    return null;
  };


  /**
   * [post_timeline description]
   * @param  {[type]} _options [description]
   * @return {[type]}          [description]
   */
  $.fn.post_timeline = function(_options) {

    var options = $.extend({},_options);
    var panim = null;

    /////////////////////////
    ///// Vertical Methods //
    /////////////////////////
    var ptl_navigation_vertical = {
        current_iterate: 0,
        initialize: function($_container) {

            this.$cont = $_container;

            var max_li = (_options.nav_max && !isNaN(_options.nav_max))?parseInt(_options.nav_max):4,
                $cont  = $_container.find('.yr_list');

            //set limit
            if(max_li <= 2 || max_li > 15) {
              max_li = 6;
            }


            var $cont_inner = $cont.find('.yr_list-inner'),
                $cont_view  = $cont.find('.yr_list-view');

            this.$cont_inner = $cont_inner;
            var cin_height   = $cont_inner.height();
            var $c_li        = $cont.find('.yr_list-inner li');
            this.li_count    = $c_li.length; 

            this.li_width        = cin_height / this.li_count; //pad
            this.iterate_width   = this.li_width * max_li;
            this.total_iterate   = Math.ceil(cin_height / this.iterate_width) - 1;

            //the iteration wrapper
            var c_iterate = 0,
                n_iterate = 0;
            for(var i = 0; i <= this.total_iterate; i++) {

              c_iterate  = i * max_li;
              n_iterate += max_li;
              var $temp_div = $c_li.slice(c_iterate, n_iterate).wrapAll('<div class="ptl-yr-list"/>');
              
              if(i == this.current_iterate) {
                $temp_div.parent().addClass('ptl-active');
              }
            }

            this.in_wrap_height = $cont.find('.ptl-yr-list').eq(0).outerHeight();
            this.iterate_width  = this.in_wrap_height;


            this.btn_top     = $cont.find('.btn-top'),
            this.btn_bottom  = $cont.find('.btn-bottom');
            
            if(this.li_count <= max_li) {

                this.btn_top.hide();
                this.btn_bottom.hide();
            }
            else{

              this.btn_top.show();
              this.btn_bottom.show();
              $(this.btn_top).addClass('ptl-disabled');
            }


            var padding = 0;
            $cont_view.height(((this.in_wrap_height) + padding)+ 'px');
            //$cont_view.height(((max_li * this.li_width) + padding)+ 'px');
            this.btn_top.unbind().bind('click',$.proxy(this.topArrow,this));
            this.btn_bottom.unbind().bind('click',$.proxy(this.bottomArrow,this));
        },
        topArrow: function(e) {

            var that = this;
            if(this.current_iterate > 0) {

                this.current_iterate--;

                this.$cont_inner.find('.ptl-yr-list').eq(this.current_iterate).addClass('ptl-active');

                //add disable
                if(this.current_iterate == 0) {
                    $(this.btn_top).addClass('ptl-disabled');
                }
                $(this.btn_bottom).removeClass('ptl-disabled');

                var to_top =  -(this.current_iterate * this.iterate_width);

                //console.log(this.current_iterate,'   ',to_left);
                that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate + 1).removeClass('ptl-active');
                this.$cont_inner.animate({'top':to_top+'px'},500,'swing',function(e) {

                    //that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate + 1).removeClass('ptl-active');
                    //console.log('===> post-timeline-public-display-12-h.php ===> 165 complete');
                });
            }
        },
        bottomArrow: function(e) {

            var that = this;
            if(this.current_iterate < this.total_iterate) {

                this.current_iterate++;

                this.$cont_inner.find('.ptl-yr-list').eq(this.current_iterate).addClass('ptl-active');

                if(this.current_iterate == this.total_iterate) {
                    $(this.btn_bottom).addClass('ptl-disabled');
                }
            
                $(this.btn_top).removeClass('ptl-disabled');
                  
                var to_top =  -(this.current_iterate * this.iterate_width);

                //console.log(this.current_iterate,'   ',to_left);
                that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate - 1).removeClass('ptl-active');
                this.$cont_inner.animate({'top':to_top+'px'},500,'swing',function(e) {
                    //console.log('===> post-timeline-public-display-12-h.php ===> 165 complete');
                    //that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate - 1).removeClass('ptl-active');
                    //that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate - 1).removeClass('ptl-active');
                });
            }
        },
        //vertical
        goTo: function(_iterate) {

            var that = this;

            var prev_iterate     = that.current_iterate;
            that.current_iterate = _iterate;

            //same iteration return
            if(prev_iterate == that.current_iterate)return;



            that.$cont_inner.find('.ptl-yr-list').eq(that.current_iterate).addClass('ptl-active');
            that.$cont_inner.find('.ptl-yr-list').eq(prev_iterate).removeClass('ptl-active');
            //that.$cont_inner.find('.ptl-yr-list').eq(prev_iterate).addClass('ptl-rem');

            //add Disable
            $(this.btn_top).removeClass('ptl-disabled');
            $(this.btn_bottom).removeClass('ptl-disabled');

            if(this.current_iterate == 0) {
                $(this.btn_top).addClass('ptl-disabled');
            }

            if(this.current_iterate == this.total_iterate) {
                $(this.btn_bottom).addClass('ptl-disabled');
            }

            var to_top = -(this.current_iterate * this.iterate_width);

            // console.log('Goto Index: ',_iterate,' prev_iterate: '+prev_iterate,'current_iterate: '+this.current_iterate);


            this.$cont_inner.animate({'top':to_top+'px'},500,'swing',function(e) {

                //that.$cont_inner.find('.ptl-yr-list').eq(prev_iterate).removeClass('ptl-active');
            });
        },
        //Update tags List
        update_tags: function(_tag_list) {

            var that  = this,
              count = 0;

            for(var y in _tag_list) {

              var tag_index = _tag_list[y][0],
                  tag_value = _tag_list[y][1];

              if(_tag_list.hasOwnProperty(y)) {
                
                if(that.$cont_inner.find('li[data-tag="'+tag_index+'"]').length == 0) {
                  count++;
                  that.$cont_inner.append('<li data-tag="'+tag_index+'"><a data-scroll data-options=\'{ "easing": "Quart" }\'  data-href="#ptl-tag-'+tag_index+'"><span>'+tag_value+'</span></a></li>');
                }
              }
            
            }

            //fix the tag list
            if(count > 0) {

              var _iterate = ptl_navigation_vertical.current_iterate;


              that.$cont_inner.find('.ptl-yr-list li').unwrap();
              
              //make current iteration
              ptl_navigation_vertical.current_iterate = _iterate;

              ptl_navigation_vertical.initialize(ptl_navigation_vertical.$cont);

            }
        }
    };

    /**
     * [debounce description]
     * @param  {[type]} func      [description]
     * @param  {[type]} wait      [description]
     * @param  {[type]} immediate [description]
     * @return {[type]}           [description]
     */
    function debounce(func, wait, immediate) {
      
      var timeout;
      return function() {
        var context = this, args = arguments;
        var later = function() {
          timeout = null;
          if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
      };
    };

    /**
     * [add_animation Add Animation]
     * @param {[type]} _options [description]
     * @param {[type]} $cont    [description]
     */
    var add_animation  = function(_options, $cont) {

      _options.offset = _options.offset || 0;

      //activate first li 
      $cont.find('.yr_list li:first-child a').addClass('active');

      var active_node = null;
      
      _options.scroller = _options.scroller || null;


      var first_load = true;
      /**
       * [description]
       */
      var anim_options = {
          boxClass:     'panim', 
          animateClass: 'animated',
          scrollContainer: (window['ptl_admin_pan'])?'.modaal-wrapper':_options.scroller,
          //scrollContainer: null,
          offset: 300,
          callbackloop: debounce(function(box) {

            //  Set the Opacity
            if(first_load) {
              first_load = false;
              $cont.find('.ptl-content-loaded').removeClass('ptl-content-loaded');
            }

            var _tag = $(box).data('tag');
            
            if(_options.scrolling)return;

            if(!active_node || _tag != active_node.data('tag')) {

              var $node = $cont.find('.yr_list li[data-tag="'+_tag+'"]');
              active_node = $node;
              $cont.find('.yr_list li a.active').removeClass('active');
              $node.children(0).addClass('active');

              //goto navigation
              var _nav_index = $cont.find('.ptl-yr-list.ptl-active').index();
              var _index = $node.parent().index();

              // console.log('===> post-timeline.js ===> Current:', _nav_index, ' and Go to:', _index );

              if(_nav_index != _index) {

                ptl_navigation_vertical.goTo(_index);
              }
            }
          })
      };
      
      panim = new PTLAnim(anim_options);
        
      panim.init();

      // if (_options['ptl-scroll-anim'] == 'on') {

      //   PTLAnim.prototype.addBox = function(element) {
      //     this.boxes.push(element);
      //   };
      //   // Attach scrollSpy to .panim elements for detect view exit events,
      //   // then reset elements and add again for animation
      //   $cont.find('.panim').on('scrollSpy:exit', function() {
      //     $(this).css({
      //       'visibility': 'hidden',
      //       'animation-name': 'none'
      //     }).addClass('animate__animated').removeClass('animate__').removeClass('animated');
      //     panim.addBox(this);
      //   }).scrollSpy();

      // }
    };


    /**
     * [add_like_event Add the like event]
     * @param {[type]} $cont   [description]
     * @param {[type]} options [description]
     */
    var add_like_event = function($cont, options) {

      $cont.on('click','.ptl-post-like',function(){
        
        var $this   = $(this),
            post_id = $this.attr('id').replace("post-", "");

        PTL_AJAX(PTL_REMOTE.ajax_url + "?action=ptl_save_post_like", { post_id: post_id }, function(_response) {

          if (_response.success) {

            $this.parent('li').find('.ptl-like-count').text(_response.count);
            $this.find('.heart').addClass('active')
          } 
        }, 'json');
      });
    }


    //  Default Carousel
    var carousel_params = {
        margin:10, 
        nav:true, 
        responsive:{ 0:{ items:1}, 600:{items:1}, 1000:{items:1}}
    };

     // Function to convert hyphenated string to camelCase
    function toCamelCase(str) {
        return str.replace(/-([a-z])/g, function (g) { return g[1].toUpperCase(); });
    }


    // Loop through the options to find attributes with the 'gallery-' prefix
    for (var key in options) {

        if (options.hasOwnProperty(key) && key.startsWith('ptl-gallery-')) {
            
            // Extract the Owl Carousel option name
            var owlOption = key.replace('ptl-gallery-', '');

            // Convert the option name to camelCase
            var owlOption = toCamelCase(owlOption);
            
            // Parse the value if necessary
            var value = options[key];

            if (value === '1') {
                value = true;
            } 
            else if (value === '0') {
                value = false;
            }
            else if (!isNaN(value)) {
                value = parseFloat(value);
            }
            else {
                try {value = JSON.parse(value);} catch (e) {}
            }

            // Add the option to the carousel_params object
            carousel_params[owlOption] = value;
        }
    }



    /**
     * [add_carousel description]
     * @param {[type]} $cont   [description]
     * @param {[type]} options [description]
     */
    var add_carousel = function($cont, options) {

        var $gallery_items = $cont.find(".ptl-media-post-gallery");
        
        if($gallery_items.length > 0 && $.fn.owlCarousel) {
          $gallery_items.owlCarousel(carousel_params);
        }
    };


    /**
     * [images_popup Post Popup Gallery Event]
     * @param  {[type]} $cont   [description]
     * @param  {[type]} options [description]
     * @return {[type]}         [description]
     */
    var images_popup = function($cont, options) {

      $cont.on('click','.ptl-post-like',function(){
        
        var $this   = $(this),
            post_id = $this.attr('id').replace("post-", "");

        PTL_AJAX(PTL_REMOTE.ajax_url + "?action=ptl_save_post_like", { post_id: post_id }, function(_response) {

          if (_response.success) {

            $this.parent('li').find('.ptl-like-count').text(_response.count);

          } 
        
        }, 'json');
      });
    };


    /**
     * [loadmore_functionality Add Load More]
     * @param {[type]} $cont    [description]
     * @param {[type]} _options [description]
     */
    var loadmore_functionality = function($cont, _options) {
      
        var page = 2,
          allow_scroll = true;
      
        //Load More Button
        if (_options['ptl-pagination'] == 'button') {
            
            $cont.find('.plt-load-more .ptl-more-btn').bind('click', function (e) {

                e.preventDefault();
                ajax_post_Data($cont, options);
            });
        }
        // Infinity Scroll
        else if (_options['ptl-pagination'] == 'scroll') {
        
            var currentscrollHeight = 0;

            $(window).scroll(function () {

                const scrollHeight = $(document).height();
                const scrollPos = Math.floor($(window).height() + $(window).scrollTop());
                const isBottom = scrollHeight - 100 < scrollPos;

                if (isBottom && currentscrollHeight < scrollHeight && allow_scroll == true) {
                    //alert('calling...');

                    ajax_post_Data($cont,options);

                    currentscrollHeight = scrollHeight;
                    allow_scroll = false;
                }
            });
        
        }

        ////////////////////////////
        // Ajax Retrive Post Data //
        ////////////////////////////
        function ajax_post_Data($cont,_options) {

            var $this = $cont.find('.plt-load-more .ptl-more-btn');
            var step = $cont.find('.plt-load-more').attr('data-steps');
            var first_load = true;

            var data = {
            'action': 'timeline_ajax_load_posts',
            'page': page,
            'step': step,
            'config': _options,
            'security': PTL_REMOTE.security
            };

            //  Show the loader
            $this.bootButton('loading');

            var scroll_loader = $cont.find('.ptl-scroll-loader');

            if (scroll_loader[0]) {
                scroll_loader.html('<span class="'+PTL_REMOTE.loader_style+'"></span> ');
            }

            //  Send an AJAX request for load more
            $.post( PTL_REMOTE.ajax_url, data, function( response ) {

                response = JSON.parse(response);

                if( $.trim(response.template) != '' && response.success ) {

                    // append template
                    $cont.find('.ptl-tmpl-main-inner' ).append( response.template );

                    add_carousel($cont, _options);       // Add Carousel
                    add_video_posts($cont, _options);    // Add Video
                       
                    if (response.navigation) {
                        //Update tag list
                        ptl_navigation_vertical.update_tags(response.navigation);
                    }

                    if (response.step) {
                      $cont.find('.plt-load-more').attr('data-steps',response.step);
                    }

                    // Remove duplicate Years
                    $cont.find('.ptl-sec-year[data-id]').each(function(){
                      
                        var ids = $(this).attr('data-id');

                        if(ids.length > 1 && ids == $(this).attr('data-id')){
                            
                            $cont.find('.ptl-sec-year[data-id="'+$(this).attr('data-id')+'"]').eq(1).parent('div').parent('.ptl-row').remove();
                        }
                    });

                    page++;

                    allow_scroll = true;  

                    if (_options['ptl-scroll-anim'] == 'on') {

                        // reset animation
                        $cont.find('.panim').on('scrollSpy:exit', function() {
                            
                            $(this).css({
                                'visibility': 'hidden',
                            'animation-name': 'none'
                            })
                            .removeClass('animate__').addClass('animate__animated');
                            panim.addBox(this);
                        })
                        .scrollSpy();

                    }

                    //  Set the Opacity
                    if(first_load) {
                      first_load = false;
                      $cont.find('.ptl-content-loaded').removeClass('ptl-content-loaded');
                    }

                    // Add the video and gallery

                }
                else {
                    $cont.find('.ptl-no-more-posts span').text(PTL_REMOTE.LANG.no_more_posts);
                    $this.hide();
                }

                //  Hide the loader
                $this.bootButton('reset');

                if (scroll_loader[0]) {
                    scroll_loader.html('');
                }

                // if (_options['ptl-lazy-load'] == 'on') {
                //   // lazyload images
                //   prefetcher($cont);
                // }

                //  Add Carousel
                //add_carousel($cont, _options);

            });
        }
    };


    /**
     * [add_video_posts description]
     * @param {[type]} $cont    [description]
     * @param {[type]} _options [description]
     */
    var add_video_posts = function($cont, _options) {

        var ptl_video_link;  

        $cont.find('.ptl-video-btn').unbind('click').bind('click', function() {
        
        //  Get the proper video src
        ptl_video_link = $(this).attr( "data-src" );
        
        $cont.find("#ptl-video").attr('src', "" ); 

        if (ptl_video_link != '' && ptl_video_link != null) {

          ptl_video_link = ptl_video_link.replace('watch?v=', 'embed/');
          ptl_video_link = ptl_video_link.split('&')[0];

          //  Change the attribute
          $cont.find("#ptl-video").attr('src', ptl_video_link + "?autoplay=1&amp;modestbranding=1&amp;showinfo=0" ); 

          $cont.find('.ptl-video-popup').popout({
            keyboard: true,
            backdrop: true,
            focus: true
          });
        }
        });
    };


    /**
     * [prefetcher Prefetch all the images of the container]
     * @param  {[type]} $cont [description]
     * @return {[type]}       [description]
     */
    var prefetcher = function($cont) {

       $cont.ptl_prefetch({
          OnStep: function(percent) {

            // console.log('===> post-timeline.js ===> 1155',percent);
          },
          OnComplete: function() {
            // console.log('===> post-timeline.js ===> OnComplete');
          }
      });

    };

    /**
     * [post_gallery_popup description]
     * @param {[type]} $cont    [description]
     * @param {[type]} _options [description]
     */
    var post_gallery_popup = function($cont, _options) {

        $cont.on('click','.ptl-gallery-popup-btn',function() {
            
            $cont.find('.ptl-gallery-popup').popout({
              keyboard: true,
              backdrop: true,
              focus: true
            });

            var $this = $(this);

            var post_id = $this.attr('data-post');

            $cont.find('.ptl-gallery-popup .ptl-popup-carousel').html('');

            PTL_AJAX(PTL_REMOTE.ajax_url + "?action=ptl_popup_gallery", { post_id: post_id }, function(_response) {

              if (_response.success) {

                $cont.find('.ptl-gallery-popup .ptl-popup-carousel').html(_response.gallery);

                $cont.find(".ptl-media-post-gallery-popup").owlCarousel(carousel_params);
              } 
            }, 'json');
        });
    };


    /**
     * [post_readmore_btn Add the readmore button to each ptl-short-desc that exceeds container height]
     * @param  {[type]} $cont    [jQuery object] The content container
     * @param  {[type]} _options [object] Additional options (e.g., max-visible-lines for truncation)
     * @return {[type]}          [void]
     */
    var post_readmore_btn = function($cont, _options) {

        // Get the max-visible-lines from _options, default to null if not provided
        var max_visible_lines = _options['ptl-max-visible-lines'] || null;

        // Check if max_visible_lines is valid (a positive number)
        if (max_visible_lines === null || isNaN(max_visible_lines) || max_visible_lines <= 0) {
            return; // Exit the function if max_visible_lines is invalid
        }

        // Loop through each '.ptl-short-desc' element within the container
        $cont.find('.ptl-short-desc').each(function() {
            
            var $content = $(this);

            // Get the line-height of the content (assumed to be the first element in the container)
            var lineHeight = parseInt($content.css('line-height'), 10);

            // Check if lineHeight is a valid number
            if (isNaN(lineHeight) || lineHeight <= 0) {
                return; // Exit if line height is invalid
            }

            // Temporarily set 'max-height' to 'none' to get the full height of the content
            $content.css('max-height', 'none');

            // Get the full scroll height of the content (this includes all hidden parts)
            var fullContentHeight = $content[0].scrollHeight;

            // Calculate the max height in pixels based on the number of lines (max_visible_lines)
            var maxHeightInPixels = max_visible_lines * lineHeight;

            // Now apply the max-height based on max-visible-lines
            $content.css({
                'overflow': 'hidden',
                'max-height': maxHeightInPixels + 'px', // Set max-height dynamically
                'transition': 'max-height 0.5s ease' // Smooth transition for expansion/collapse
            });

            // If the full content height exceeds the max-height, show the "Read More" link
            if (fullContentHeight > maxHeightInPixels) {
                // Dynamically create the "Read More" link
                var $readMoreLink = $('<a/>', {
                    class: 'ptl-read-more-link',
                    text: PTL_REMOTE.LANG.read_more
                });

                // Append the "Read More" link after the content
                $content.after($readMoreLink);

                // Add click event to toggle the expanded/collapsed state
                $readMoreLink.on('click', function(event) {
                    event.preventDefault(); // Prevent the default anchor behavior

                    $content.toggleClass('ptl-expanded');

                    if ($content.hasClass('ptl-expanded')) {
                        $content.css('max-height', fullContentHeight + 'px'); // Expand to full height
                        $(this).text(PTL_REMOTE.LANG.read_less); // Use "Read Less" from PTL_REMOTE
                    } else {
                        $content.css('max-height', maxHeightInPixels + 'px'); // Set back to initial height
                        $(this).text(PTL_REMOTE.LANG.read_more); // Use "Read More" from PTL_REMOTE
                    }
                });
            }
        });
    };




    
    //////////////////////////
    //// Horizontal Methods //
    //////////////////////////
    
    // Navigation
    var ptl_navigation = {
        /**
         * [initialize description]
         * @param  {[type]} $container [description]
         * @return {[type]}            [description]
         */
        initialize: function($container) {
            
            var max_li = (_options.nav_max && !isNaN(_options.nav_max)) ? parseInt(_options.nav_max) : 6;
            var $cont = $container.find('.ptl-nav-wrapper');

            // Set limit for max_li
            if (max_li <= 2 || max_li > 15) {
                max_li = 6;
            }


            // ref to max li
            this.max_li_limit = max_li;

            this.$cont = $cont;
            var $cont_inner  = $cont.find('.ptl-h-nav-inner'),
                $cont_view   = $cont.find('.ptl-h-nav-view');

            // Ensure the width is calculated after the DOM is fully loaded
            
            
            $(window).on('load', function() {
        
            }
            .bind(this));
            
            this.setup($container, this.max_li_limit, $cont_inner, $cont_view, false);

            // Bind resize event
            $(window).resize($.proxy(this.resize, this));
        },
        /**
         * [setup Create the navigations]
         * @param  {[type]} $container  [description]
         * @param  {[type]} max_li      [description]
         * @param  {[type]} $cont_inner [description]
         * @param  {[type]} $cont_view  [description]
         * @return {[type]}             [description]
         */
        setup: function($container, max_li, $cont_inner, $cont_view, resizing) {
            
            this.max_li      = max_li;
            this.$cont_inner = $cont_inner;
            var $c_li        = $cont_inner.find('li');
            this.li_count    = $c_li.length;

            var padding     = 20;
            this.li_width   = $cont_view.find('li').eq(0).outerWidth(true) + padding;


            //  Only do the resize if the li has changed too much!
            var new_max_li = this.max_li;

            // Adjust max_li based on the container width
            while ((new_max_li * this.li_width) > $cont_view.width() && new_max_li > 2) {
                new_max_li--;
            }

            if (resizing) {
                if (new_max_li == this.max_li) {
                    return; // No change in max_li, no need to update
                }
            }

            this.max_li = new_max_li;
            
            // Calculate the number of groups and adjust the width of each group
            var num_groups       = Math.ceil($c_li.length / this.max_li);
            this.total_iterate   = num_groups - 1;
            this.current_iterate = 0;

            // Clear existing groups and recreate them
            $cont_inner.find('.ptl-yr-list').contents().unwrap();
            
            for (var i = 0; i < num_groups; i++) {
                var start = i * this.max_li;
                var end = start + this.max_li;
                $c_li.slice(start, end).wrapAll('<ul class="' + (i === 0 ? 'ptl-yr-list ptl-active' : 'ptl-yr-list') + '"></ul>');
            }

            // Container width of iteration
            this.iterate_width   = $cont_inner.find('.ptl-yr-list').eq(0).outerWidth(true);


            // Reset the Zero
            $cont_inner.css('transform', 'translateX(' + -(this.current_iterate * this.iterate_width) + 'px)');


            this.btn_left  = $container.find('.ptl-btn-left');
            this.btn_right = $container.find('.ptl-btn-right');


            //   remove the disable class
            this.btn_left.removeClass('ptl-disabled');
            this.btn_right.removeClass('ptl-disabled');


            // Unbind previous events to avoid multiple bindings
            this.btn_left.off('click');
            this.btn_right.off('click');


            // Hide arrows if all items fit in the view
            if (this.li_count <= this.max_li) {
                this.btn_left.hide();
                this.btn_right.hide();
            }
            else {
                this.btn_left.show();
                this.btn_right.show();
                $(this.btn_left).addClass('ptl-disabled');
            }

            // Bind arrow click events
            this.btn_left.bind('click', $.proxy(this.leftArrow, this));
            this.btn_right.bind('click', $.proxy(this.rightArrow, this));
        },
        // Function to handle the left arrow click
        leftArrow: function(e) {
            e.preventDefault(); // Prevent default anchor behavior

            var that = this;

            if (this.current_iterate > 0) {
                this.current_iterate--;

                // Move to the previous group
                that.$cont_inner.css('transform', 'translateX(' + -(this.current_iterate * this.iterate_width) + 'px)');

                // Update button states
                $(that.btn_right).removeClass('ptl-disabled');
                if (that.current_iterate === 0) {
                    $(that.btn_left).addClass('ptl-disabled');
                }

                // Update active class
                that.$cont_inner.find('.ptl-yr-list').removeClass('ptl-active').eq(that.current_iterate).addClass('ptl-active');
            }
        },

        // Function to handle the right arrow click
        rightArrow: function(e) {
            e.preventDefault(); // Prevent default anchor behavior

            var that = this;

            if (this.current_iterate < this.total_iterate) {
                this.current_iterate++;

                // Move to the next group
                that.$cont_inner.css('transform', 'translateX(' + -(this.current_iterate * this.iterate_width) + 'px)');

                // Update button states
                $(that.btn_left).removeClass('ptl-disabled');
                if (that.current_iterate === that.total_iterate) {
                    $(that.btn_right).addClass('ptl-disabled');
                }

                // Update active class
                that.$cont_inner.find('.ptl-yr-list').removeClass('ptl-active').eq(that.current_iterate).addClass('ptl-active');
            }
        },

        // Resize function to handle window resize event
        resize: function() {
            this.setup(this.$cont, this.max_li_limit, this.$cont_inner, this.$cont.find('.ptl-h-nav-view'), true);
        },

        // Get the first item in the horizontal navigation
        get_first: function(_no_trigger) {
            var that = this;
            var p_index = (that.max_li * that.current_iterate);

            // Show the current item
            if (!_no_trigger) {
                this.$cont_inner.find('li').eq(p_index).trigger('click');
            }
        },

        // Load a specific tag in the horizontal navigation
        load_tag: function(_tag, $cont) {
            
            var that = this;

            $cont.find('li.active').removeClass('active');

            var $tag_li     = $cont.find('li[data-tag="' + _tag + '"]'),
                $p_tag      = $tag_li.parent(),
                tag_index   = ($tag_li.index() + (this.max_li * $p_tag.index()));

            $tag_li.addClass('active');

            var iterate = Math.floor(tag_index / this.max_li);

            // If iterate is not the same
            if (this.current_iterate != iterate) {
                this.current_iterate = iterate;

                $(this.btn_left).removeClass('ptl-disabled');
                $(this.btn_right).removeClass('ptl-disabled');

                // If max iterate
                if (iterate == this.total_iterate) {
                    $(this.btn_right).addClass('ptl-disabled');
                }

                // If at the beginning
                if (this.current_iterate == 0) {
                    $(this.btn_left).addClass('ptl-disabled');
                }


                var to_left = -(iterate * this.iterate_width);

                this.$cont_inner.css({
                    'transform': 'translateX(' + to_left + 'px)'
                });

                setTimeout(function() {
                    that.get_first(true); // No trigger
                }, 500); // Duration of the animation

                // Even if the iterate is the same, update the active class
                this.$cont_inner.find('.ptl-yr-list').removeClass('ptl-active').eq(iterate).addClass('ptl-active');
            }
        }
    };


    /**
     * [make_horz_navigation Make Horizontal Navigation]
     * @param  {[type]} $cont      [description]
     * @param  {[type]} slick_inst [description]
     * @return {[type]}            [description]
     */
    function make_horz_navigation($cont, slick_inst) {

      $cont.find('.ptl-h-nav li').eq(0).addClass('active');

      var after_click = '';
      
      $cont.find('.ptl-h-nav li').bind('click',function(e) {

          var _tag     = $(this).data('tag'); 

          after_click = _tag;
          slick_inst[0].agile_slick.the_tag = _tag;

          //var $node   = $cont.find('.timeline-box[data-tag="'+_tag+'"]').parent();
          var $node        = $cont.find('.timeline-box[data-tag="'+_tag+'"]');


          var slide_index  = $node.not('.agile_slick-cloned').data('agile_slick-index'),
              total_slides = slick_inst[0].agile_slick.slideCount,
              showing_up   = slick_inst[0].agile_slick.options.slidesToShow,
              total_moves  = total_slides - showing_up;

          //reduce of next sibling is null,, $node[0].nextSibling && 
          if(slick_inst[0].agile_slick.options.slidesToShow > 1) {

            if(slide_index >= total_moves) {

              slide_index = total_moves;
            }
          }
        
          slick_inst[0].agile_slick.goTo(slide_index);
      });


      //initialize horizontal navigation
      ptl_navigation.initialize($cont);

      /**
       * [On Slide Change]
       * @param  {[type]} _e  [description]
       * @param  {[type]} _s) {var $_slide [description]
       * @return {[type]}     [description]
       */
      slick_inst.on('afterChange', function(_e,_s) {

          var $_slide = $(_s.$slides[_s.getCurrent()]),
              _tag    = $_slide.data('tag');

              _tag    = $_slide.data('tag') || slick_inst[0].agile_slick.the_tag;

              if(typeof(after_click) != "undefined" && after_click !== '') {
                _tag    = after_click || _tag;
                after_click = '';
              }
              

          /*var $_slide = $(_s.$slides[_s.getCurrent()]),
          _tag    = $_slide.find('.timeline-box').data('tag');

          _tag    = slick_inst[0].agile_slick.the_tag || $_slide.find('.timeline-box').data('tag');*/
          ptl_navigation.load_tag(_tag, $cont);
      });


      //  Add Carousel
      add_carousel($cont, _options);

      //  Add the video
      add_video_posts($cont, options);

      // Create gallery popup
      post_gallery_popup($cont, options);

      //  Close the video after hide event on modal
      $cont.find('.ptl-video-popup').on('hidden.bs.popout', function (e) {
        $cont.find("#ptl-video").attr('src', '' ); 
      });
    };


    /**
     * [ptl_main Post Timeline Main method]
     * @return {[type]} [description]
     */
    function ptl_main() {

      //  Main This
      var $this      = $(this);
      var $section   = $this.find('.timeline-section');

      // Add the read-more
      post_readmore_btn($section, options);

      //  To handle horizontal timelines
      if(options['ptl-layout'] == 'horizontal') {

        var slides_to_show   = (options['ptl-slides'])? parseInt(options['ptl-slides']): 2,
            slides_to_scroll = (options['ptl-slides_scroll'])? parseInt(options['ptl-slides_scroll']): 1;

        if($section.find('.timeline-box').length <= 1) {
          slides_to_show = 1;
        }
        
        var slider_params = {
          slidesToShow: slides_to_show,
          slidesToScroll: slides_to_scroll,
          infinite: false
        };

        //set slick count
        var _w = $section.width();

        if(_w <= 768) {

          slider_params.slidesToShow = 1;
          $section.attr('data-agile_slick','{"slidesToShow": 1, "slidesToScroll": 1}');
        }
        
        //  Slider
        var slick_inst = $section.agile_slick(slider_params);  


        //Make Navigation
        if(options['ptl-nav-status']) {

          make_horz_navigation($this, slick_inst);
        }


        //  Pre-scroll, unused code
        if(false && window.location.hash) {

          //  For Year Node
          var ptl_hash  = window.location.hash;

          var node_li   = $this.find('.yr_list li a[href="'+ptl_hash+'"]');

          if(node_li.length) {

            window.setTimeout(function(e) {
              node_li.parent().trigger('click');
            
            }, 500);
          }
          /*
          //  For Posts
          var ptl_hash  = window.location.hash.replace('#','');
          
          var hash_node = $section.find('[data-href="'+ptl_hash+'"]');

          if(hash_node && hash_node[0]) {

            var hash_post = hash_node.parents('.ptl-post-div');

            if(hash_post && hash_post[0]) {

              var post_list  = Array.prototype.slice.call( hash_post[0].parentNode.children );

              var post_index = post_list.indexOf( hash_post[0] );

              if(post_index) {

                slider_params.initialSlide = post_index;
              }
            }
          }
          */
        }

        /**
         * [Resize Event]
         * @param  {[type]} e)
         * @return {[type]}    [description]
         */
        $(window).on('resize',function(e) {
          
          // set slick count
          var _w = $section.width();

          if(_w <= 768) {
            
            //$section.attr('data-agile_slick','{"slidesToShow": 1, "slidesToScroll": 1}');
            slick_inst[0].agile_slick.options.slidesToShow = 1;
          }
          else {


            var slides = (options['ptl-slides'])? parseInt(options['ptl-slides']): ((options.template == "3-h" || options.template == "7-h")?1:2);

            //$section.attr('data-agile_slick','{"slidesToShow": 2, "slidesToScroll": 1}');
            slick_inst[0].agile_slick.options.slidesToShow = slides;
          }

          slick_inst.agile_slick('resize');
        });
        
        $this.find('.ptl-content-loaded').removeClass('ptl-content-loaded');
        $this.find('.ptl-preloder').addClass('hide');
      }
      //  Vertical Timelines
      else {

          options.scroller = _options.scroller || null;

          //  Add ptl scroll :: once
          $.fn.ptlScroller({
            nav_offset: (options.nav_offset)?parseInt(options.nav_offset):null,
            selector: '[data-scroll]',
            easing: _options['ptl-easing'],//easeInQuad,easeOutQuad,easeInOutQuad,easeInCubic,easeOutCubic,easeInOutCubic,easeInQuart,easeOutQuart,easeInOutQuart,easeInQuint,easeOutQuint,easeInOutQuint
            doc: options.scroller,
            before: function(e){

              options.scrolling = true;
              
            },
            after: function(e){

              options.scrolling = false; 
            }
          });

          
          //Add Animation :: Once
          if(options['ptl-anim-status']) {

            add_animation(options, $this);
          }
          //  Set the opacity to 1 when animation is disabled
          else {
            $this.find('.ptl-content-loaded').removeClass('ptl-content-loaded');
          }


          //  Add the load more
          loadmore_functionality($this, options);

          //  Add the video
          add_video_posts($this, options);

          //  Close the video after hide event on modal
          $this.find('.ptl-video-popup').on('hidden.bs.popout', function (e) {
            $this.find("#ptl-video").attr('src', '' ); 
          });

          // Create gallery popup
          post_gallery_popup($this, options);

          //  Add the video
          add_like_event($this, options);

          //  Add Popups for Images
          images_popup($this, options);

          //  Add Carousel
          add_carousel($this, options);

          // if (options['ptl-lazy-load'] == 'on') {
          //   // pre fetch images
          //   prefetcher($this);
          // }
          


          //  Navigation enable?
          if(options['ptl-nav-status']) {

            ptl_navigation_vertical.initialize($this);

            var $nav        = $this.find('.yr_list');
            
            // Add 'sticky-nav' class to make it sticky
            $nav.addClass('sticky-nav');

            function isElementMoreThanHalfScreen($el) {
                
                var rect = $el[0].getBoundingClientRect();
                var windowHeight = (window.innerHeight || document.documentElement.clientHeight);

                // Check if the element makes up more than 50% of the screen height
                var elementInViewHeight = Math.min(rect.bottom, windowHeight) - Math.max(rect.top, 0);

                return elementInViewHeight >= windowHeight / 2;
            }

            function toggleNavVisibility() {
                if (isElementMoreThanHalfScreen($this)) {
                    $nav.css({ position: 'fixed' });
                } else {
                    $nav.css({ position: 'absolute' });
                }
            }

            $(window).on('scroll resize', toggleNavVisibility);

            // Initial check
            toggleNavVisibility();
        }     
      }

      $this.find('.ptl-loader-overlay').addClass('d-none');
    };

    /*loop for each*/
    this.each(ptl_main);

    return this;
  };

  //   Main Loop
  $('.ptl-cont').each(function(e){

      var ptl_config_id = $(this).attr('data-id'),
          ptl_config    = window['ptl_config_' + ptl_config_id];

      if(ptl_config) {
       $(this).post_timeline(ptl_config);
      }
  });

  /**
   * [bState Change Button State]
   * @param  {[type]} _state [description]
   * @return {[type]}        [description]
   */
  jQuery.fn.bootButton = function(_state) {

      //  Empty
      if(!this[0])return;

      if(_state == 'loading')
        this.attr('data-reset-text', this.html());

      if(_state == 'loading') {

        if(!this[0].dataset.resetText) {
          this[0].dataset.resetText = this.html();
        }

        this.addClass('disabled').attr('disabled','disabled').html('<span class="'+PTL_REMOTE.loader_style+'"></span> ' + this[0].dataset.loadingText);
      }
      else if(_state == 'reset')
        this.removeClass('disabled').removeAttr('disabled').html(this[0].dataset.resetText);
      else if(_state == 'update')
        this.removeClass('disabled').removeAttr('disabled').html(this[0].dataset.updateText);
      else
        this.addClass('disabled').attr('disabled','disabled').html(this[0].dataset[_state+'Text']);
  };

}( jQuery ));


