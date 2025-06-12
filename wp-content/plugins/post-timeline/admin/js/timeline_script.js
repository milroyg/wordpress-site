/*Our main script*/
(function($){

	/**
   * [toastIt toast it based on the error or message]
   * @param  {[type]} _response [description]
   * @return {[type]}           [description]
   */
  var toastIt = function(_response) {

    if(_response.success) {
      atoastr.success(_response.msg || _response.message);
    }
    else {
      atoastr.error(_response.error || _response.message || _response.msg);
    }
  };


	$(document).ready(function(e){

		//  Create Css Editor
		var textareaeditor = document.getElementById('tabs-custom');
		var editor = '';

		if (textareaeditor) {

			textareaeditor.addEventListener("click", function() {
				setTimeout(function() { editor = wp.codeEditor.initialize($('#ptl-custom-css'), ptl_editor_settings.ce_css); }, 200);
			}, { once: true });
		}

		var ptl_cont = $('.ptl-cont');

		// Generate Shortcode
		$('#ptl-add-shortcode').on('click',function(){

			var $form 	 = $('.ptl_modal-body').find('#shortcode-popup');
			var formData = $form.PTLSerializeObject();


			//  loading
			$('.ptl-popup-loader').removeClass('d-none');

			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=generate_shorcode',{formData:formData }, function(_response) {
				
				if (_response.success) {
					
					if ($('.ptl_shortcode_area')[0]) {
						window.ptl_gutenberg_attrs.setAttributes({shortcode:  _response.shortcode});
					}
					else {
						var prev_content = tmce_getContent('content');
						tmce_setContent(prev_content+_response.shortcode);
						tmce_focus('content');
					}
					
					$("[data-dismiss=ptl_modal]").trigger({ type: "click" });
				}
				
				//  loading
				$('.ptl-popup-loader').addClass('d-none');

			}, 'json'); 

		});

		// Ptl shortcode btn Dashboard
		// Generate Shortcode
		$('#ptl-add-dashboard-shortcode').on('click', function() {

			var $form = $('.ptl_modal-body').find('#shortcode-popup');
			var formData = $form.PTLSerializeObject();

			//  loading
			$('.ptl-popup-loader').removeClass('d-none');

			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=generate_shorcode', { formData: formData }, function(_response) {

				if (_response.success) {

					aswal({

				      html:"<div class='ptl-text-aswal'>"+
				            "<div class='ptl-logo-aswal'>"+
				            "<img src='"+PTL_REMOTE.logo+"'>"+
				            "<div>"+
				            "<h2>Post Timeline Shortcode</h2>"+
				            "<div class='dash-shortcode-clip'><input type='text' value='"+_response.shortcode+"' readonly></div>"+
				            "<div>",
				      showCancelButton: true,
				      cancelButtonText: "Close",
				      allowOutsideClick: true,
				      allowEscapeKey: true,
				      confirmButtonColor: "#e05300",
				      confirmButtonText: "Copy",
				      preConfirm: () => {
				          // Copy link to clipboard
				          const input = $('.dash-shortcode-clip input');
				          input.select();
				          document.execCommand('copy');	


				      }

				    });


					$("[data-dismiss=ptl_modal]").trigger({ type: "click" });
				}	

				//  loading
				$('.ptl-popup-loader').addClass('d-none');

			}, 'json');
		});

		// admin page mdeia event
		$('.radio-event-m input').on('click',function(){
		    var url = $('.p-vid-url'),
		        gallery = $('.p-gallery');
		    if($(this).parent('.media-url')[0]){
		        gallery.addClass('hide');
		        url.removeClass('hide');
		    }else if($(this).parent('.media-gallery')[0]){
		        url.addClass('hide');
		        gallery.removeClass('hide');
		    }else{
		        url.addClass('hide');
		        gallery.addClass('hide');
		    }
		});

		
		// Create Date Time Picker 
		var date_picker_field = ptl_cont.find('.ptl-date-picker');

		if(date_picker_field[0]) {

			date_picker_field.ptl_datetimepicker({
			  icons: {
			    time: "fa fa-clock",
			    date: "fa fa-calendar-day",
			    up: "fa fa-chevron-up",
			    down: "fa fa-chevron-down",
			    previous: 'fa fa-chevron-left',
			    next: 'fa fa-chevron-right',
			    today: 'fa fa-screenshot',
			    clear: 'fa fa-trash',
			    close: 'fa fa-remove'
			  },
			  format: 'MM/DD/YYYY'
			});
		}
		
		$('#ptl-post-date').on('click',function() {
		    $(this).parent('.ptl-date-picker').find('#button-addon2').trigger('click');
		})

		/*  Clear Button for fontawesome icon input  */
		$(document).on('click','.ptl-icon-clear', function() {
			$(this).parents('.ptl_ico_picker-container').find('.ptl_ico_picker-input').val('');

			$(this).css('display', 'none');
			$(this).parents('.ptl_ico_picker-container').find('.ptl_ico_picker-input').focus();
		});

		$(document).on('click', '.ptl_ico_picker-item', function() {
			$(this).parents('.ptl_ico_picker-container').find('.ptl-icon-clear').css('display', 'block');
		});

		$(document).on('keyup', '.ptl_ico_picker-container .ptl_ico_picker-input', function() {
			if ($(this).val() != '' && $(this).val() != null) {
				$(this).parents('.ptl_ico_picker-container').find('.ptl-icon-clear').css('display', 'block');
			} else {
				$(this).parents('.ptl_ico_picker-container').find('.ptl-icon-clear').css('display', 'none');
			}
		});


		// Date Picker Filter in manage Page 
		var dateOptions = {
					viewMode: 'years',
					format: 'MM/YYYY'
				};

		if($('.ptl-filter-date-picker')[0]) {

			$('.ptl-filter-date-picker').ptl_datetimepicker(dateOptions).on("dp.change", function (e) {
				
				if (e.date._i != "" && e.date._i != null) {
					$(".ptl-date-clear").css("display", "block");
				}
				else{
					$(".ptl-date-clear").css("display", "none");
				}

			});
	
		}
	  
		/*  Clear Button for DatePicker on manage page Date filter  */
		$('.ptl-date-clear').on('click', function() {
		  $(this).parents('.ptl-filter-date-picker').find('.ptl-event-date-picker').val('');
		  $(this).css('display', 'none');
		  $(this).parents('.ptl-filter-date-picker').find('.ptl-event-date-picker').focus();
		});

		$('#ptl_event').on('keyup',function(){

			if ($(this).val() != '' && $(this).val() != null) {
				$('.ptl-date-clear').css('display', 'block');
			}else{
				$('.ptl-date-clear').css('display', 'none');
			}
		});

		// Post timeline page checkbox (Select ALL)
		$(".post-type-post-timeline #cb-select-all-1").click(function() {
		   $('.post-type-post-timeline #the-list input[name="post[]"]').prop('checked', this.checked);
		 });

		 $('.post-type-post-timeline #the-list input[name="post[]"]').change(function() {
		   $(".post-type-post-timeline #cb-select-all-1").prop("checked", $('.post-type-post-timeline #the-list input[name="post[]"]').length === $('#the-list input[name="post[]"]:checked').length);
		 });

		// admin Popup open on click
		$('.ptl-cont').on('click', '#add-page-url', function(e) {

		    // This can be any value. Its sole purpose is to let us know if the modal that's open
		    // is the default or a custom one.
		    window.mySpecialModalVariable = e.target.dataset.target;

		    // This must have a value because the link dialogue expects it to 
		    // (a wp_editor instance, actually, but that part doesn't matter here...as long as it has a value).
		    wpActiveEditor = true;	


		    // Open the link popup.
		    wpLink.open();

		    // So no other action is triggered.
		    return false;
		});

		// When the dialogue box's submit button is clicked...
		$('body').on('click', '#wp-link-submit', function(e) {

		    var selectedItem = wpLink.getAttrs();
		    $('#ptl-post-link').val(selectedItem.href);
		    if (!selectedItem.href) {
		        close_admin_popup(e);
		    }

		    // Do something with the selectedItem, then close the dialogue box.

		   close_admin_popup(e);
		});

		// When the user clicks the cancel button, close button, or overlay...
		$('body').on('click', '#wp-link-cancel, #wp-link-close, #wp-link-backdrop', function(e) {

		    // Do whatever's necessary, then...
		    close_admin_popup(e);
		});

		ptl_cont.find('.sel-icon-type input').on('click',function(){
			
		    var 
		    	$this 		= $(this);
		    	type 		= $(this).attr('id'),
		    	upload 		= $('.ptl-custom-icon'),
		    	fawesome 	= $('.ptl-fav-icon');

		    if(type == 'ptl-upload-icon'){
		        upload.removeClass('hide');
		        fawesome.addClass('hide');
		    }else{
		        upload.addClass('hide');
		        fawesome.removeClass('hide');
		    }
		});

		ptl_cont.find('.sel-social-type input').on('click', function() {

			var
				$this = $(this);
				type = $(this).attr('id'),
				custom = $('.ptl-social-custom');


			if(type == 'ptl-social-fix') {
				custom.addClass('hide');
				// fixed.addClass('hide');
			} else {
				custom.removeClass('hide');
				// fixed.removeClass('hide');
			}
		});
		ptl_cont.find('#ptl-loader').on('change',function(){
			var loader_class = $(this).val();
			$('.loader-sec').html('<span class="'+loader_class+'"></span>');
		});
		// preview image
		ptl_cont.find('.custom-file-input').on('change',function(){

			var url = window.URL.createObjectURL(this.files[0]);
		    $(this).parent('div').find('.image-preview').attr('src',url);

		});

		// Admin popup close
		function close_admin_popup(e) {

		    // Again, an editor instance is expected, as upon closing .focus() is called on
		    // wpLink.textarea, so we need to provide a jQuery object to .focus() on.
		    wpLink.textarea = $('body');
		    wpLink.close();

		    window.mySpecialModalVariable = false;
		    e.preventDefault ? e.preventDefault() : e.returnValue = false;
		    e.stopPropagation();
		};


		// Admin Field Gallery Uploader
		// Start
		/*
		 * A custom function that checks if element is in array, we'll need it later
		 */
		function in_array(el, arr) {
			for(var i in arr) {
				if(arr[i] == el) return true;
			}
			return false;
		}


		/*
		 * Sortable images
		 */
		if ($('.ptl_gallery_mtb')[0]) {

			$('ul.ptl_gallery_mtb').sortable({
				items:'li',
				cursor:'-webkit-grabbing', /* mouse cursor */
				scrollSensitivity:40,
				/*
				You can set your custom CSS styles while this element is dragging
				start:function(event,ui){
					ui.item.css({'background-color':'grey'});
				},
				*/
				stop:function(event,ui){
					ui.item.removeAttr('style');

					var sort = new Array(), /* array of image IDs */
					    gallery = $(this); /* ul.ptl_gallery_mtb */

					/* each time after dragging we resort our array */
					gallery.find('li').each(function(index){
						sort.push( $(this).attr('data-id') );
					});
					/* add the array value to the hidden input field */
					gallery.parent().next().val( sort.join() );
					/* console.log(sort); */
				}
			});
		}

		/*
		 * Multiple images uploader
		 */
		$('.ptl_upload_gallery_button , .ptl_upload_icon_button').click( function(e){ /* on button click*/
			e.preventDefault();
			var multiple 			= true,
				$class 				= 'gallery',
				is_class_upload 	= $(this).hasClass('ptl_upload_icon_button');

			if (is_class_upload) {
				multiple 	= false;
				$class 		= 'icon';
			}
			var button = $(this),
			    hiddenfield = button.prev(),
			    hiddenfieldvalue = hiddenfield.val().split(","), /* the array of added image IDs */
		    	    custom_uploader = wp.media({
				title: 'Insert images', /* popup title */
				library : {type : 'image'},
				button: {text: 'Use these images'}, /* "Insert" button text */
				multiple: multiple
			    }).on('select', function() {

				var attachments = custom_uploader.state().get('selection').map(function( a ) {
					a.toJSON();
	            			return a;
				}),
				thesamepicture = false,
				i;

				/* loop through all the images */
	      		for (i = 0; i < attachments.length; ++i) {
	      			
					/* if you don't want the same images to be added multiple time */
					if( !in_array( attachments[i].id, hiddenfieldvalue ) ) {
						if (is_class_upload) {
							$('ul.ptl_'+$class+'_mtb').html('<li data-id="' + attachments[i].id + '"><span style="background-image:url(' + attachments[i].attributes.url + ')"></span><a href="#" class="ptl_'+$class+'_remove">&times;</a></li>');
						}else{
							/* add HTML element with an image */
							$('ul.ptl_'+$class+'_mtb').append('<li data-id="' + attachments[i].id + '"><span style="background-image:url(' + attachments[i].attributes.url + ')"></span><a href="#" class="ptl_'+$class+'_remove">&times;</a></li>');
						}
						
						if (is_class_upload) {
							/* add an image ID to the array of all images */
							hiddenfieldvalue = attachments[i].id ;
						}else{
							/* add an image ID to the array of all images */
							hiddenfieldvalue.push( attachments[i].id );
						}
						
					} else {
						thesamepicture = true;
					}
	      		}
	      		if (!is_class_upload) {
					/* refresh sortable */
					$( "ul.ptl_"+$class+"_mtb" ).sortable( "refresh" );
	      		}

	      		if (is_class_upload) {
	      			/* add an image ID to the array of all images */
	      			hiddenfield.val( hiddenfieldvalue );
	      		}else{
	      			/* add the IDs to the hidden field value */
					hiddenfield.val( hiddenfieldvalue.join() );
	      		}
				
				/* you can print a message for users if you want to let you know about the same images */
				if( thesamepicture == true ) alert('The same images are not allowed.');
			}).open();
		});

		/*
		 * Remove certain images
		 */
		$('body').on('click', '.ptl_gallery_remove,.ptl_icon_remove', function(){
			var id = $(this).parent().attr('data-id'),
			    gallery = $(this).parent().parent(),
			    hiddenfield = gallery.parent().next(),
			    hiddenfieldvalue = hiddenfield.val().split(","),
			    i = hiddenfieldvalue.indexOf(id);

			$(this).parent().remove();

			/* remove certain array element */
			if(i != -1) {
				hiddenfieldvalue.splice(i, 1);
			}

			/* add the IDs to the hidden field value */
			hiddenfield.val( hiddenfieldvalue.join() );

			/* refresh sortable */
			gallery.sortable( "refresh" );

			return false;
		});
		/*
		 * Selected item
		 */
		$('body').on('mousedown', 'ul.ptl_gallery_mtb li', function(){
			var el = $(this);
			el.parent().find('li').removeClass('ptl-active');
			el.addClass('ptl-active');
		});

		// Create Nice Select 
		$('.ptl-cont .custom-good-select').goodSelect();


		// Get Google Fonts on page Ready
		$.getJSON(PTL_REMOTE.plugin_url+'includes/gfonts.json', function(fonts){
			$.each(fonts, function( key, font ) {
			   $('#ptl-fonts-title')
			       .append($("<option></option>")
			       .attr("value", font.family)
			       .text(font.family));		   
			       $('#ptl-fonts-content')
			       .append($("<option></option>")
			       .attr("value", font.family)
			       .text(font.family));
			});
				PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=get_fonts_options',{ }, function(_response) {
			  
			  if (_response.success) {

			  	$('#ptl-fonts-title option[value="'+_response.font_title+'"]').prop('selected', true).change();
			  	
			  	setTimeout(function(){ 
			  		if ( _response.subsets_title != null) {
			  			for (var i = 0; i < _response.subsets_title.length; ++i) {
			  			    // console.log(_response.subsets[i]);
			  			    $('.subsets-title input[value="'+_response.subsets_title[i]+'"]').prop('checked', true);
			  			}	 
			  		}
	 				if ( _response.variants_title != null) {
	 					for (var i = 0; i < _response.variants_title.length; ++i) {
	 					    // console.log(_response.variants[i]);
	 					    $('.variants-title input[value="'+_response.variants_title[i]+'"]').prop('checked', true);
	 					}

	 				}
	 				if ( _response.subsets_content != null) {
	 					for (var i = 0; i < _response.subsets_content.length; ++i) {
	 					    // console.log(_response.subsets[i]);
	 					    $('.subsets-content input[value="'+_response.subsets_content[i]+'"]').prop('checked', true);
	 					}
	 				}
	 				if ( _response.variants_content != null) {
	 					for (var i = 0; i < _response.variants_content.length; ++i) {
	 					    // console.log(_response.variants[i]);
	 					    $('.variants-content input[value="'+_response.variants_content[i]+'"]').prop('checked', true);
	 					}
	 				}
		  	
			  	}, 300);


			  	setTimeout(function(){ 
			  		$('#ptl-fonts-content option[value="'+_response.font_content+'"]').prop('selected', true).change();

			  		if ( _response.subsets_content != null) {
			  			for (var i = 0; i < _response.subsets_content.length; ++i) {
			  			    // console.log(_response.subsets[i]);
			  			    $('.subsets-content input[value="'+_response.subsets_content[i]+'"]').prop('checked', true);
			  			}
			  		}
		  			if ( _response.variants_content != null) {
		  				for (var i = 0; i < _response.variants_content.length; ++i) {
		  				    // console.log(_response.variants[i]);
		  				    $('.variants-content input[value="'+_response.variants_content[i]+'"]').prop('checked', true);
		  				}
		  			}

			  	}, 300);

			  	setTimeout(function(){ 
			  		if ( _response.subsets_content != null) {
			  			for (var i = 0; i < _response.subsets_content.length; ++i) {
			  			    // console.log(_response.subsets[i]);
			  			    $('.subsets-content input[value="'+_response.subsets_content[i]+'"]').prop('checked', true);
			  			}
			  		}

		  			if ( _response.variants_content != null) {
		  				for (var i = 0; i < _response.variants_content.length; ++i) {
		  				    // console.log(_response.variants[i]);
		  				    $('.variants-content input[value="'+_response.variants_content[i]+'"]').prop('checked', true);
		  				}
		  			}

		  			$(document).find('.ptl-cont .custom-good-select').goodSelect('destroy').goodSelect();

			  	}, 500);

			    return;
			  }

			}, 'json'); 
			

		});

		// Social share meta box repeater
		$(document).on('click', '.ptl-remove-item', function() {
			$(this).parents('tr.ptl-sub-row').remove();
		}); 				
		$(document).on('click', '.ptl-add-item', function() {
			var row_no = $('.ptl-item-table tr.ptl-sub-row').length;    
			var p_this = $(this);
			row_no = parseFloat(row_no);
			var row_html = $('.ptl-item-table .ptl-hide-tr').html().replace(/rand_no/g, row_no).replace(/hide_ptl-social-rep/g, 'ptl-social-rep');
			$('.ptl-item-table tbody').append('<tr class="ptl-sub-row">' + row_html + '</div>'); 

			$(".ptl-social-icon").ptl_ico_picker();
		});


		// Get Google Fonts Attr onChange event
		$('#ptl-fonts-title,#ptl-fonts-content').on('change',function(){
			var font = $(this).val(),
				atts = {},
				parent = $(this).parents('.field-content');
				param = 'title';
				if ($(this).attr('id') == 'ptl-fonts-content') {
					param = 'content';
				}

			$.getJSON(PTL_REMOTE.plugin_url+'includes/gfonts.json', function(fonts){
				$.each(fonts, function( key, fonts ) {
					if (fonts.family == font) {
						atts.subsets = fonts.subsets; 
						atts.variants = fonts.variants; 
					
					}
				});
				parent.find('.row-subsets-'+param).html('');
				parent.find('.row-variants-'+param).html('');

				$.each(atts.subsets, function(i)
				    {	
				    	if (parent.find('.row-subsets-'+param)[0]) {
				    		// $this.parents('.select-box').find('.options-group').empty();
				    		parent.find('.options-group').find('.row-subsets-'+param).append('<div class="col-lg-4 col-md-6 col-sm-6"><div class="row mb-2"><div class="col-lg-5 col-md-5 col-sm-5 col-4"><label class="custom-toggle" for="set-'+i+'-'+param+'"><input type="checkbox" name="subsets-'+param+'[]" id="set-'+i+'-'+param+'" value="'+atts.subsets[i]+'"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label></div><div class="col-lg-6 col-md-6 col-sm-7 col-8 pl-0"><div class="label">'+atts.subsets[i]+'</div></div><div>');
				    	}else{
				    		parent.find('.options-group').append('<div class="subsets-head mt-2"><h3>Subsets</h3></div><div class="subsets-'+param+'"><div class="row row-subsets-'+param+'"><div class="col-lg-4 col-md-6 col-sm-6"><div class="row mb-2"><div class="col-lg-5 col-md-5 col-sm-5 col-4"><label class="custom-toggle" for="set-'+i+'-'+param+'"><input type="checkbox" name="subsets-'+param+'[]" id="set-'+i+'-'+param+'" value="'+atts.subsets[i]+'"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label></div><div class="col-lg-6 col-md-6 col-sm-7 col-8 pl-0"><div class="label">'+atts.subsets[i]+'</div></div></div></div></div></div></div>')
				    	}

				    });
					$.each(atts.variants, function(i)
				    {	
				    	if (parent.find('.variants-'+param)[0]) {
				    		// $this.parents('.select-box').find('.options-group').empty();
				    		parent.find('.options-group').find('.row-variants-'+param).append('<div class="col-lg-4 col-md-6 col-sm-6"><div class="row mb-2"><div class="col-lg-5 col-md-5 col-sm-5 col-4"><label class="custom-toggle" for="var-'+i+'-'+param+'"><input type="checkbox" name="variants-'+param+'[]" id="var-'+i+'-'+param+'" value="'+atts.variants[i]+'"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label></div><div class="col-lg-6 col-md-6 col-sm-7 col-8 pl-0"><div class="label">'+atts.variants[i]+'</div></div></div>');
				    	}else{
				    		parent.find('.options-group').append('<div class="variants-head"><h3>Variants</h3></div><div class="variants-'+param+'"><div class="row row-variants-'+param+'"><div class="col-lg-4 col-md-6 col-sm-6"><div class="row mb-2"><div class="col-lg-5 col-md-5 col-sm-5 col-4"><label class="custom-toggle" for="var-'+i+'-'+param+'"><input type="checkbox" name="variants-'+param+'[]" id="var-'+i+'-'+param+'" value="'+atts.variants[i]+'"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label></div><div class="col-lg-6 col-md-6 col-sm-7 col-8 pl-0"><div class="label">'+atts.variants[i]+'</div></div></div></div></div></div></div>')
				    	}

				    });
			});

		});

		$('#frm-ptl-setting .input-group-merge input[type=checkbox]').on('click',function(){
			if($(this).prop("checked") == true){
				$(this).parents('.input-group-merge').removeClass('ptl-off');
			}else{
				$(this).parents('.input-group-merge').addClass('ptl-off');
			}
		});

		// Add And remove Classes From Radio images in settings
		$('#frm-ptl-setting .radio-images').on('click',function(){
		    $('#frm-ptl-setting .radio-images').removeClass('active');
		    $(this).addClass('active');
		});


		// Change Values For color Picker
		$(document).on('change','.colorpicker',function(){
		    var value = $(this).val();
		    $(this).parents('.post_field').find('.colorpicker_value').val(value);
		}); 

		$(document).on('keyup','.colorpicker_value',function(){
		    var value = $(this).val();
		    $(this).parents('.post_field').find('.colorpicker').val(value);
		});

		// Create IconPicker
	 	$("#ptl-fav-icon").ptl_ico_picker();
	 	$(".ptl-social-icon").ptl_ico_picker();

	 	// click event on ptl_ico_picker icon
	 	$('.ptl_ico_picker-container .ptl_ico_picker-icon').on('click',function(){
	 	   $('.ptl_ico_picker-input').focus();
	 	});

		//  Save Event
		$(document).on('click','#ptl_setting', function(e) {

			var sets_title 	= [],
				vars_title 	= [],
				attr_title 	= {},
				family_title 	= $('#ptl-fonts-title').val();
		    $('.options-group').find("input[name='subsets-title[]']:checked").each( function (i,e) {
				sets_title[i] = e.value;
			});
			$('.options-group').find("input[name='variants-title[]']:checked").each( function (i,e) {
				vars_title[i] = e.value;
			});

		    attr_title['family-title'] = family_title;
		    attr_title['subsets-title'] = sets_title;
		    attr_title['variants-title'] = vars_title;		

		    var sets_content 	= [],
				vars_content 	= [],
				attr_content 	= {},
				family_content 	= $('#ptl-fonts-content').val();
		    $('.options-group').find("input[name='subsets-content[]']:checked").each( function (i,e) {
				sets_content[i] = e.value;
			});
			$('.options-group').find("input[name='variants-content[]']:checked").each( function (i,e) {
				vars_content[i] = e.value;
			});

		    attr_content['family-content'] = family_content;
		    attr_content['subsets-content'] = sets_content;
		    attr_content['variants-content'] = vars_content;


		  var $btn = $(this);

		  $btn.bootButton('loading');
		  var $form = $('.card-body').find('#frm-ptl-setting');

		  var bg_status = 'off',
		  	  ptl_images = 'off',
		  	  post_icon = 'off',
		  	  nav_status = 'off',
			  rm_status = 'off',
			  ptl_targed_blank = 'off',
			  anim_status = 'off';
			  social_share = 'off';
			  ptl_content_hide = 'off';
			  ptl_lazy_load = 'off';
			  ptl_scroll_anim = 'off';
			  

		  	if($('#ptl-bg-status').prop("checked") == true){
		  	  bg_status = 'on';
		  		$('#ptl-bg-status').val(bg_status);
		  	}
		  	if($('#ptl-images').prop("checked") == true){
		  	  ptl_images = 'on';
		  		$('#ptl-images').val(ptl_images);
		  	}
		  	if($('#ptl-post-icon').prop("checked") == true){
		  	  post_icon = 'on';
		  		$('#ptl-post-icon').val(post_icon);
		  	}
		  	if($('#ptl-nav-status').prop("checked") == true){
		  	  nav_status = 'on';
		  		$('#ptl-nav-status').val(nav_status);
		  	}
		  	if($('#ptl-rm-status').prop("checked") == true){
		  	  rm_status = 'on';
		  		$('#ptl-rm-status').val(rm_status);
		  	}
		  	if($('#ptl-anim-status').prop("checked") == true){
		  	  anim_status = 'on';
		  		$('#ptl-anim-status').val(anim_status);
		  	}
		  	if($('#ptl-content-hide').prop("checked") == true){
		  	  ptl_content_hide = 'on';
		  		$('#ptl-content-hide').val(ptl_content_hide);
		  	}	  

		  	// if($('#ptl-lazy-load').prop("checked") == true){
		  	//   ptl_lazy_load = 'on';
		  	// 	$('#ptl-lazy-load').val(ptl_lazy_load);
		  	// }	
		  	if($('#ptl-scroll-anim').prop("checked") == true){
		  	  ptl_scroll_anim = 'on';
		  		$('#ptl-scroll-anim').val(ptl_scroll_anim);
		  	}	  
		  	if($('#ptl-target-blank').prop("checked") == true){
		  	  ptl_targed_blank = 'on';
		  		$('#ptl-target-blank').val(ptl_targed_blank);
		  	}	  

		  	// Social Share 
		  	if ($('#ptl-social-share').prop("checked") == true) {
		  		social_share = 'on';
		  		$('#ptl-social-share').val(social_share);
		  	}

		  	var socials = {};
		  	$('.ptl-social-shares').each(function() {
		  		var id = $(this).attr('id'),
		  			status = 'off';
		  		if ($('#' + id).prop("checked") == true) {
		  			status = 'on';
		  			$('#' + id).val(status);
		  		}

		  		socials[id] = status;

		  	});
		  	
		   var all_data = {
	            'ptl-bg-status': bg_status,
	            'ptl-images': ptl_images,
	            'ptl-post-icon': post_icon,
	            'ptl-nav-status': nav_status,
	            'ptl-rm-status': rm_status,
	            'ptl-anim-status': anim_status,
	            'ptl-social-share': social_share,
	            'ptl-content-hide' : ptl_content_hide,
	            'ptl-lazy-load' : ptl_lazy_load,
	            'ptl-scroll-anim' : ptl_scroll_anim,
	            'ptl-target-blank' : ptl_targed_blank,
	            'social-shares' : socials,
	        };


		  var formData = $form.PTLSerializeObject();

		  formData['ptl-fonts-title'] = { font: attr_title  };
		  formData['ptl-fonts-content'] = { font: attr_content  };

		  formData = $.extend(all_data, formData);
		  
		  if (editor != '') {
		  	formData['ptl-custom-css'] = editor.codemirror.getValue();
		  }


		  PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=save_setting',{ formData:formData}, function(_response) {

		    $btn.bootButton('reset');

		    toastIt(_response);

		  }, 'json');
		});

		$(document).on('change','form#shortcode-popup #ptl_select_posttype',function(){
			var type = $(this).val();

			//  loading
			$('.ptl-popup-loader').removeClass('d-none');
			
			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=select_modal_posttype',{ type:type}, function(_response) {

			    if(_response.modal_html) {
		             $('#insert-ptl-shortcode .set_ptl_post_type #tabs-source-text .ptl-inner-settings').html(_response.modal_html);


		             $(document).find('.ptl-cont .custom-good-select').goodSelect('destroy').goodSelect();

		             //  loading
		             $('.ptl-popup-loader').addClass('d-none');
		             
		           }

			}, 'json');
		});

		$('.ptl_modal-body').on('change','form#shortcode-popup  #ptl-layout',function(){
			var layout = $(this).val();	

			//  loading
			$('.ptl-popup-loader').removeClass('d-none');

			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=select_layout',{ layout:layout}, function(_response) {

			    if(_response.options) {
			    	$('.ptl-tmpl-btns').html('');
		            $('#ptl-template').html(_response.options);
		            if (_response.position) {
		             	$('.ptl-tmpl-btns').html(_response.position);
		            }
		            $('#ptl-template').change();
		            $(document).find('.ptl-cont .custom-good-select').goodSelect('destroy').goodSelect();
		        }

		        //  loading
		        $('.ptl-popup-loader').addClass('d-none');

			}, 'json');
		});


		$(document).on('change','form#shortcode-popup #ptl-post-type',function(){
		    var post_types = jQuery(this).val();

		    //  loading
		    $('.ptl-popup-loader').removeClass('d-none');

			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=selected_post_types',{ post_types:post_types}, function(_response) {

		    if(_response.taxonomies) {

	             $('#ptl-taxonomy').html(_response.taxonomies);
	             $('#ptl-tag-taxonomy').html(_response.taxonomies);
	             $('#ptl-category').html('');

	             $(document).find('.ptl-cont .custom-good-select').goodSelect('destroy').goodSelect();
	           }


	           //  loading
	           $('.ptl-popup-loader').addClass('d-none');
			}, 'json');
		});

		$(document).on('change', 'form#shortcode-popup #ptl-type', function() {
			var type = jQuery(this).val(),
				selected_post_type = jQuery('#ptl_select_posttype').val();
			if (type == 'tag' && selected_post_type == 'cpt' ) {
				$('#ptl-tag-taxonomy').parents('.form-group').show();
			}else{
				$('#ptl-tag-taxonomy').parents('.form-group').hide();
			}

			if (type == 'date') {

				$("#ptl-sort-by").val("publish_date");
				$("#ptl-sort-by").goodSelect("update");
			}

		});

		$(document).on('change','form#shortcode-popup #ptl-taxonomy',function(){
		    var taxonomy = jQuery(this).val();

		    //  loading
		    $('.ptl-popup-loader').removeClass('d-none');

			PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=selected_taxonomy',{ taxonomy:taxonomy}, function(_response) {

		    if(_response.categories) {

	             $('#ptl-category').html(_response.categories);
	             $(document).find('.ptl-cont .custom-good-select').goodSelect('destroy').goodSelect();
	           }
       	//  loading
       	$('.ptl-popup-loader').addClass('d-none');
			}, 'json');
		});

		$(".ptl_modal-body .card-body.set_ptl_post_type").on('change','#ptl-template',function(){
		   var src = $('option:selected', this).attr('src'),
		   	   name = $('option:selected', this).text();	
		    $('.temp-img').attr('src',src);
		    $('.temp-img').attr('alt',name);
		});

		// 
		$(".ptl_modal-body .card-body.set_ptl_post_type").on('change','#ptl-nav-type',function(){
		   var src = $('option:selected', this).attr('src'),
		   	   name = $('option:selected', this).text();	
		    $('.nav-img').attr('src',src);
		    $('.nav-img').attr('alt',name);
		});

		// ptl tag term_order sorting
		if($('.taxonomy-ptl_tag table.tags #the-list')[0]) {
			
			$('.taxonomy-ptl_tag table.tags #the-list').sortable({
				'items': 'tr',
				'axis': 'y',
				'helper': ptlfixHelper,
				'update' : function(e, ui) {

					var formData = {
						order: $('#the-list').sortable('serialize')
					};

					//	Send a request to update the menu ordering
					PTL_AJAX(PTL_REMOTE.ajax_url + '?action=ptl_ajax_handler&ptl-action=update_tags_order', formData, function(_response) {

						toastIt(_response);

					}, 'json');
				}
			});
		}


		$(document).on('change', 'form#shortcode-popup #ptl-sort-by', function() {
			var sort_type = jQuery(this).val();

			if (sort_type == 'meta') {
				$('.ptl-sorting-meta').removeClass('d-none');
			}else{
				$('.ptl-sorting-meta').addClass('d-none');
			}

		});

		var ptlfixHelper = function(e, ui) {
			ui.children().children().each(function() {
				$(this).width($(this).width());
			});
			return ui;
		};

		//Add Timeline Post
		function add_timeline_post() {

			$('#post').validationEngine({});

			$('#publish').bind('click',function(e) {

				if(!$('#post').validationEngine('validate')) {
					e.preventDefault();
					return false;
				}
			});

			//Filter Init
			$('#setting_ptl-post-gallery').addClass('hide');
			$('#setting_ptl-post-vidlink').addClass('hide');

			//Add Filters
			$('#ptl-post-type').bind('change',function(e){

				var _val = this.value;

				switch(_val) {

					case '0':
						$('#setting_ptl-post-vidlink').addClass('hide');
						$('#setting_ptl-post-gallery').addClass('hide');
					break;

					case '1':
						$('#setting_ptl-post-vidlink').removeClass('hide');
						$('#setting_ptl-post-gallery').addClass('hide');
					break;

					case '2':
						$('#setting_ptl-post-vidlink').addClass('hide');
						$('#setting_ptl-post-gallery').removeClass('hide');
					break;
				}
			});
		};

		// get tmce content 
		function tmce_getContent(editor_id, textarea_id) {
		  if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
		  if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
		  
		  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		    return tinyMCE.get(editor_id).getContent();
		  }else{
		    return jQuery('#'+textarea_id).val();
		  }
		}

		// set tmce content
		function tmce_setContent(content, editor_id, textarea_id) {
		  if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
		  if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
		  
		  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		    return tinyMCE.get(editor_id).setContent(content);
		  }else{
		    return jQuery('#'+textarea_id).val(content);
		  }
		}

		// Focus on tmce
		function tmce_focus(editor_id, textarea_id) {
		  if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
		  if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;
		  
		  if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		    return tinyMCE.get(editor_id).focus();
		  }else{
		    return jQuery('#'+textarea_id).focus();
		  }
		}

		//Settings Timeline
		function timeline_settings() {
		}

		//Only for Post Timeline :: Custom TYPE
		if(document.getElementById('post_timeline_timeline_metabox')) {

			add_timeline_post();
		};

		//Settings Page
		if(document.getElementById('page-post-timeline-settings')) {

			timeline_settings();
		};

	});

})(jQuery);


/*Validation Script*/
/*
 * Inline Form Validation Engine 2.6.2, jQuery plugin
 *
 * Copyright(c) 2010, Cedric Dugas
 * http://www.position-absolute.com
 *
 * 2.0 Rewrite by Olivier Refalo
 * http://www.crionics.com
 *
 * Form validation engine allowing custom regex rules to be added.
 * Licensed under the MIT License
 */
 (function($) {

	"use strict";

	var methods = {

		/**
		* Kind of the constructor, called before any action
		* @param {Map} user options
		*/
		init: function(options) {
			var form = this;
			if (!form.data('jqv') || form.data('jqv') == null ) {
				options = methods._saveOptions(form, options);
				// bind all formError elements to close on click
				$(document).on("click", ".formError", function() {
					$(this).fadeOut(150, function() {
						// remove prompt once invisible
						$(this).closest('.formError').remove();
					});
				});
			}
			return this;
		 },
		/**
		* Attachs jQuery.validationEngine to form.submit and field.blur events
		* Takes an optional params: a list of options
		* ie. jQuery("#formID1").validationEngine('attach', {promptPosition : "centerRight"});
		*/
		attach: function(userOptions) {

			var form = this;
			var options;

			console.log('===> timeline_script.js ===> 71');

			if(userOptions)
				options = methods._saveOptions(form, userOptions);
			else
				options = form.data('jqv');

			options.validateAttribute = (form.find("input[data-validation-engine*=validate],select[data-validation-engine*=validate]").length) ? "data-validation-engine" : "class";
			if (options.binded) {

				// delegate fields
				form.on(options.validationEventTrigger, "input["+options.validateAttribute+"*=validate]:not([type=checkbox]):not([type=radio]):not(.datepicker)", methods._onFieldEvent);
				form.on("click", "input["+options.validateAttribute+"*=validate][type=checkbox],input["+options.validateAttribute+"*=validate][type=radio]", methods._onFieldEvent);
				form.on(options.validationEventTrigger,"input["+options.validateAttribute+"*=validate][class*=datepicker]", {"delay": 300}, methods._onFieldEvent);
			}
			if (options.autoPositionUpdate) {
				$(window).bind("resize", {
					"noAnimation": true,
					"formElem": form
				}, methods.updatePromptsPosition);
			}
			form.on("click","a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']", methods._submitButtonClick);
			form.removeData('jqv_submitButton');

			// bind form.submit
			form.on("submit", methods._onSubmitEvent);
			return this;
		},
		/**
		* Unregisters any bindings that may point to jQuery.validaitonEngine
		*/
		detach: function() {

			var form = this;
			var options = form.data('jqv');

			// unbind fields
			form.off(options.validationEventTrigger, "["+options.validateAttribute+"*=validate]:not([type=checkbox]):not([type=radio]):not(.datepicker)", methods._onFieldEvent);
			form.off("click", "["+options.validateAttribute+"*=validate][type=checkbox],["+options.validateAttribute+"*=validate][type=radio]", methods._onFieldEvent);
			form.off(options.validationEventTrigger,"["+options.validateAttribute+"*=validate][class*=datepicker]", methods._onFieldEvent);

			// unbind form.submit
			form.off("submit", methods._onSubmitEvent);
			form.removeData('jqv');

			form.off("click", "a[data-validation-engine-skip], a[class*='validate-skip'], button[data-validation-engine-skip], button[class*='validate-skip'], input[data-validation-engine-skip], input[class*='validate-skip']", methods._submitButtonClick);
			form.removeData('jqv_submitButton');

			if (options.autoPositionUpdate)
				$(window).off("resize", methods.updatePromptsPosition);

			return this;
		},
		/**
		* Validates either a form or a list of fields, shows prompts accordingly.
		* Note: There is no ajax form validation with this method, only field ajax validation are evaluated
		*
		* @return true if the form validates, false if it fails
		*/
		validate: function(userOptions) {
			var element = $(this);
			var valid = null;
			var options;

			if (element.is("form") || element.hasClass("validationEngineContainer")) {
				if (element.hasClass('validating')) {
					// form is already validating.
					// Should abort old validation and start new one. I don't know how to implement it.
					return false;
				} else {
					element.addClass('validating');
					if(userOptions)
						options = methods._saveOptions(element, userOptions);
					else
						options = element.data('jqv');
					var valid = methods._validateFields(this);

					// If the form doesn't validate, clear the 'validating' class before the user has a chance to submit again
					setTimeout(function(){
						element.removeClass('validating');
					}, 100);
					if (valid && options.onSuccess) {
						options.onSuccess();
					} else if (!valid && options.onFailure) {
						options.onFailure();
					}
				}
			} else if (element.is('form') || element.hasClass('validationEngineContainer')) {
				element.removeClass('validating');
			} else {
				// field validation
		                var form = element.closest('form, .validationEngineContainer');
		                options = (form.data('jqv')) ? form.data('jqv') : $.validationEngine.defaults;
		                valid = methods._validateField(element, options);

		                if (valid && options.onFieldSuccess)
		                    options.onFieldSuccess();
		                else if (options.onFieldFailure && options.InvalidFields.length > 0) {
		                    options.onFieldFailure();
		                }

		                return !valid;
			}
			if(options.onValidationComplete) {
				// !! ensures that an undefined return is interpreted as return false but allows a onValidationComplete() to possibly return true and have form continue processing
				return !!options.onValidationComplete(form, valid);
			}
			return valid;
		},
		/**
		*  Redraw prompts position, useful when you change the DOM state when validating
		*/
		updatePromptsPosition: function(event) {

			if (event && this == window) {
				var form = event.data.formElem;
				var noAnimation = event.data.noAnimation;
			}
			else
				var form = $(this.closest('form, .validationEngineContainer'));

			var options = form.data('jqv');
			// No option, take default one
			if (!options)
				options = methods._saveOptions(form, options);

			console.log('===> timeline_script.js ===> 195');
			var input_clause = 'input['+options.validateAttribute+'*=validate],select['+options.validateAttribute+'*=validate]';
			form.find(input_clause).not(":disabled").each(function(){
				var field = $(this);
				if (options.prettySelect && field.is(":hidden"))
				  field = form.find("#" + options.usePrefix + field.attr('id') + options.useSuffix);
				var prompt = methods._getPrompt(field);
				var promptText = $(prompt).find(".formErrorContent").html();

				if(prompt)
					methods._updatePrompt(field, $(prompt), promptText, undefined, false, options, noAnimation);
			});
			return this;
		},
		/**
		* Displays a prompt on a element.
		* Note that the element needs an id!
		*
		* @param {String} promptText html text to display type
		* @param {String} type the type of bubble: 'pass' (green), 'load' (black) anything else (red)
		* @param {String} possible values topLeft, topRight, bottomLeft, centerRight, bottomRight
		*/
		showPrompt: function(promptText, type, promptPosition, showArrow) {
			var form = this.closest('form, .validationEngineContainer');
			var options = form.data('jqv');
			// No option, take default one
			if(!options)
				options = methods._saveOptions(this, options);
			if(promptPosition)
				options.promptPosition=promptPosition;
			options.showArrow = showArrow==true;

			methods._showPrompt(this, promptText, type, false, options);
			return this;
		},
		/**
		* Closes form error prompts, CAN be invidual
		*/
		hide: function() {
			 var form = $(this).closest('form, .validationEngineContainer');
			 var options = form.data('jqv');
			 // No option, take default one
			 if (!options)
				options = methods._saveOptions(form, options);
			 var fadeDuration = (options && options.fadeDuration) ? options.fadeDuration : 0.3;
			 var closingtag;

			 if(form.is("form") || form.hasClass("validationEngineContainer")) {
				 closingtag = "parentForm"+methods._getClassName($(form).attr("id"));
			 } else {
				 closingtag = methods._getClassName($(form).attr("id")) +"formError";
			 }
			 $('.'+closingtag).fadeTo(fadeDuration, 0, function() {
				 $(this).closest('.formError').remove();
			 });
			 return this;
		 },
		 /**
		 * Closes all error prompts on the page
		 */
		 hideAll: function() {
			 var form = this;
			 var options = form.data('jqv');
			 var duration = options ? options.fadeDuration:300;
			 $('.formError').fadeTo(duration, 0, function() {
				 $(this).closest('.formError').remove();
			 });
			 return this;
		 },
		/**
		* Typically called when user exists a field using tab or a mouse click, triggers a field
		* validation
		*/
		_onFieldEvent: function(event) {
			var field = $(this);
			var form = field.closest('form, .validationEngineContainer');
			var options = form.data('jqv');
			// No option, take default one
			if (!options)
				options = methods._saveOptions(form, options);
			options.eventTrigger = "field";

            if (options.notEmpty == true){

                if(field.val().length > 0){
                    // validate the current field
                    window.setTimeout(function() {
                        methods._validateField(field, options);
                    }, (event.data) ? event.data.delay : 0);

                }

            }else{

                // validate the current field
                window.setTimeout(function() {
                    methods._validateField(field, options);
                }, (event.data) ? event.data.delay : 0);

            }




		},
		/**
		* Called when the form is submited, shows prompts accordingly
		*
		* @param {jqObject}
		*            form
		* @return false if form submission needs to be cancelled
		*/
		_onSubmitEvent: function() {
			var form = $(this);
			var options = form.data('jqv');

			//check if it is trigger from skipped button
			if (form.data("jqv_submitButton")){
				var submitButton = $("#" + form.data("jqv_submitButton"));
				if (submitButton){
					if (submitButton.length > 0){
						if (submitButton.hasClass("validate-skip") || submitButton.attr("data-validation-engine-skip") == "true")
							return true;
					}
				}
			}

			options.eventTrigger = "submit";

			// validate each field
			// (- skip field ajax validation, not necessary IF we will perform an ajax form validation)
			var r=methods._validateFields(form);

			if (r && options.ajaxFormValidation) {
				methods._validateFormWithAjax(form, options);
				// cancel form auto-submission - process with async call onAjaxFormComplete
				return false;
			}

			if(options.onValidationComplete) {
				// !! ensures that an undefined return is interpreted as return false but allows a onValidationComplete() to possibly return true and have form continue processing
				return !!options.onValidationComplete(form, r);
			}
			return r;
		},
		/**
		* Return true if the ajax field validations passed so far
		* @param {Object} options
		* @return true, is all ajax validation passed so far (remember ajax is async)
		*/
		_checkAjaxStatus: function(options) {
			var status = true;
			$.each(options.ajaxValidCache, function(key, value) {
				if (!value) {
					status = false;
					// break the each
					return false;
				}
			});
			return status;
		},

		/**
		* Return true if the ajax field is validated
		* @param {String} fieldid
		* @param {Object} options
		* @return true, if validation passed, false if false or doesn't exist
		*/
		_checkAjaxFieldStatus: function(fieldid, options) {
			return options.ajaxValidCache[fieldid] == true;
		},
		/**
		* Validates form fields, shows prompts accordingly
		*
		* @param {jqObject}
		*            form
		* @param {skipAjaxFieldValidation}
		*            boolean - when set to true, ajax field validation is skipped, typically used when the submit button is clicked
		*
		* @return true if form is valid, false if not, undefined if ajax form validation is done
		*/
		_validateFields: function(form) {
			var options = form.data('jqv');

			// this variable is set to true if an error is found
			var errorFound = false;

			// Trigger hook, start validation
			form.trigger("jqv.form.validating");
			// first, evaluate status of non ajax fields
			var first_err=null;
			var input_clause = 'input['+options.validateAttribute+'*=validate],select['+options.validateAttribute+'*=validate]';
			form.find(input_clause).not(":disabled").each( function() {
				var field = $(this);
				var names = [];
				if ($.inArray(field.attr('name'), names) < 0) {
					errorFound |= methods._validateField(field, options);
					if (errorFound && first_err==null)
						if (field.is(":hidden") && options.prettySelect)
							first_err = field = form.find("#" + options.usePrefix + methods._jqSelector(field.attr('id')) + options.useSuffix);
						else {

							//Check if we need to adjust what element to show the prompt on
							//and and such scroll to instead
							if(field.data('jqv-prompt-at') instanceof jQuery ){
								field = field.data('jqv-prompt-at');
							} else if(field.data('jqv-prompt-at')) {
								field = $(field.data('jqv-prompt-at'));
							}
							first_err=field;
						}
					if (options.doNotShowAllErrosOnSubmit)
						return false;
					names.push(field.attr('name'));

					//if option set, stop checking validation rules after one error is found
					if(options.showOneMessage == true && errorFound){
						return false;
					}
				}
			});

			// second, check to see if all ajax calls completed ok
			// errorFound |= !methods._checkAjaxStatus(options);

			// third, check status and scroll the container accordingly
			form.trigger("jqv.form.result", [errorFound]);

			if (errorFound) {
				if (options.scroll) {
					var destination=first_err.offset().top;
					var fixleft = first_err.offset().left;

					//prompt positioning adjustment support. Usage: positionType:Xshift,Yshift (for ex.: bottomLeft:+20 or bottomLeft:-20,+10)
					var positionType=options.promptPosition;
					if (typeof(positionType)=='string' && positionType.indexOf(":")!=-1)
						positionType=positionType.substring(0,positionType.indexOf(":"));

					if (positionType!="bottomRight" && positionType!="bottomLeft") {
						var prompt_err= methods._getPrompt(first_err);
						if (prompt_err) {
							destination=prompt_err.offset().top;
						}
					}

					// Offset the amount the page scrolls by an amount in px to accomodate fixed elements at top of page
					if (options.scrollOffset) {
						destination -= options.scrollOffset;
					}

					// get the position of the first error, there should be at least one, no need to check this
					//var destination = form.find(".formError:not('.greenPopup'):first").offset().top;
					if (options.isOverflown) {
						var overflowDIV = $(options.overflownDIV);
						if(!overflowDIV.length) return false;
						var scrollContainerScroll = overflowDIV.scrollTop();
						var scrollContainerPos = -parseInt(overflowDIV.offset().top);

						destination += scrollContainerScroll + scrollContainerPos - 5;
						var scrollContainer = $(options.overflownDIV).filter(":not(:animated)");

						scrollContainer.animate({ scrollTop: destination }, 1100, function(){
							if(options.focusFirstField) first_err.focus();
						});

					} else {
						$("html, body").animate({
							scrollTop: destination
						}, 1100, function(){
							if(options.focusFirstField) first_err.focus();
						});
						$("html, body").animate({scrollLeft: fixleft},1100)
					}

				} else if(options.focusFirstField)
					first_err.focus();
				return false;
			}
			return true;
		},
		/**
		* This method is called to perform an ajax form validation.
		* During this process all the (field, value) pairs are sent to the server which returns a list of invalid fields or true
		*
		* @param {jqObject} form
		* @param {Map} options
		*/
		_validateFormWithAjax: function(form, options) {

			var data = form.serialize();
									var type = (options.ajaxFormValidationMethod) ? options.ajaxFormValidationMethod : "GET";
			var url = (options.ajaxFormValidationURL) ? options.ajaxFormValidationURL : form.attr("action");
									var dataType = (options.dataType) ? options.dataType : "json";
			$.ajax({
				type: type,
				url: url,
				cache: false,
				dataType: dataType,
				data: data,
				form: form,
				methods: methods,
				options: options,
				beforeSend: function() {
					return options.onBeforeAjaxFormValidation(form, options);
				},
				error: function(data, transport) {
					if (options.onFailure) {
						options.onFailure(data, transport);
					} else {
						methods._ajaxError(data, transport);
					}
				},
				success: function(json) {
					if ((dataType == "json") && (json !== true)) {
						// getting to this case doesn't necessary means that the form is invalid
						// the server may return green or closing prompt actions
						// this flag helps figuring it out
						var errorInForm=false;
						for (var i = 0; i < json.length; i++) {
							var value = json[i];

							var errorFieldId = value[0];
							var errorField = $($("#" + errorFieldId)[0]);

							// make sure we found the element
							if (errorField.length == 1) {

								// promptText or selector
								var msg = value[2];
								// if the field is valid
								if (value[1] == true) {

									if (msg == ""  || !msg){
										// if for some reason, status==true and error="", just close the prompt
										methods._closePrompt(errorField);
									} else {
										// the field is valid, but we are displaying a green prompt
										if (options.allrules[msg]) {
											var txt = options.allrules[msg].alertTextOk;
											if (txt)
												msg = txt;
										}
										if (options.showPrompts) methods._showPrompt(errorField, msg, "pass", false, options, true);
									}
								} else {
									// the field is invalid, show the red error prompt
									errorInForm|=true;
									if (options.allrules[msg]) {
										var txt = options.allrules[msg].alertText;
										if (txt)
											msg = txt;
									}
									if(options.showPrompts) methods._showPrompt(errorField, msg, "", false, options, true);
								}
							}
						}
						options.onAjaxFormComplete(!errorInForm, form, json, options);
					} else
						options.onAjaxFormComplete(true, form, json, options);

				}
			});

		},
		/**
		* Validates field, shows prompts accordingly
		*
		* @param {jqObject}
		*            field
		* @param {Array[String]}
		*            field's validation rules
		* @param {Map}
		*            user options
		* @return false if field is valid (It is inversed for *fields*, it return false on validate and true on errors.)
		*/
		_validateField: function(field, options, skipAjaxValidation) {
			if (!field.attr("id")) {
				field.attr("id", "form-validation-field-" + $.validationEngine.fieldIdCounter);
				++$.validationEngine.fieldIdCounter;
			}

			if(field.hasClass(options.ignoreFieldsWithClass))
				return false;

           if (!options.validateNonVisibleFields && (field.is(":hidden") && !options.prettySelect || field.parent().is(":hidden")))
				return false;

			var rulesParsing = field.attr(options.validateAttribute);
			var getRules = /validate\[(.*)\]/.exec(rulesParsing);

			if (!getRules)
				return false;
			var str = getRules[1];
			var rules = str.split(/\[|,|\]/);

			// true if we ran the ajax validation, tells the logic to stop messing with prompts
			var isAjaxValidator = false;
			var fieldName = field.attr("name");
			var promptText = "";
			var promptType = "";
			var required = false;
			var limitErrors = false;
			options.isError = false;
			options.showArrow = options.showArrow ==true;

			// If the programmer wants to limit the amount of error messages per field,
			if (options.maxErrorsPerField > 0) {
				limitErrors = true;
			}

			var form = $(field.closest("form, .validationEngineContainer"));
			// Fix for adding spaces in the rules
			for (var i = 0; i < rules.length; i++) {
				rules[i] = rules[i].toString().replace(" ", "");//.toString to worked on IE8
				// Remove any parsing errors
				if (rules[i] === '') {
					delete rules[i];
				}
			}

			for (var i = 0, field_errors = 0; i < rules.length; i++) {

				// If we are limiting errors, and have hit the max, break
				if (limitErrors && field_errors >= options.maxErrorsPerField) {
					// If we haven't hit a required yet, check to see if there is one in the validation rules for this
					// field and that it's index is greater or equal to our current index
					if (!required) {
						var have_required = $.inArray('required', rules);
						required = (have_required != -1 &&  have_required >= i);
					}
					break;
				}


				var errorMsg = undefined;
				switch (rules[i]) {

					case "required":
						required = true;
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._required);
						break;
					case "custom":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._custom);
						break;
					case "groupRequired":
						// Check is its the first of group, if not, reload validation with first field
						// AND continue normal validation on present field
						var classGroup = "["+options.validateAttribute+"*=" +rules[i + 1] +"]";
						var firstOfGroup = form.find(classGroup).eq(0);
						if(firstOfGroup[0] != field[0]){

							methods._validateField(firstOfGroup, options, skipAjaxValidation);
							options.showArrow = true;

						}
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._groupRequired);
						if(errorMsg)  required = true;
						options.showArrow = false;
						break;
					case "ajax":
						// AJAX defaults to returning it's loading message
						errorMsg = methods._ajax(field, rules, i, options);
						if (errorMsg) {
							promptType = "load";
						}
						break;
					case "minSize":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._minSize);
						break;
					case "maxSize":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._maxSize);
						break;
					case "min":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._min);
						break;
					case "max":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._max);
						break;
					case "past":
						errorMsg = methods._getErrorMessage(form, field,rules[i], rules, i, options, methods._past);
						break;
					case "future":
						errorMsg = methods._getErrorMessage(form, field,rules[i], rules, i, options, methods._future);
						break;
					case "dateRange":
						var classGroup = "["+options.validateAttribute+"*=" + rules[i + 1] + "]";
						options.firstOfGroup = form.find(classGroup).eq(0);
						options.secondOfGroup = form.find(classGroup).eq(1);

						//if one entry out of the pair has value then proceed to run through validation
						if (options.firstOfGroup[0].value || options.secondOfGroup[0].value) {
							errorMsg = methods._getErrorMessage(form, field,rules[i], rules, i, options, methods._dateRange);
						}
						if (errorMsg) required = true;
						options.showArrow = false;
						break;

					case "dateTimeRange":
						var classGroup = "["+options.validateAttribute+"*=" + rules[i + 1] + "]";
						options.firstOfGroup = form.find(classGroup).eq(0);
						options.secondOfGroup = form.find(classGroup).eq(1);

						//if one entry out of the pair has value then proceed to run through validation
						if (options.firstOfGroup[0].value || options.secondOfGroup[0].value) {
							errorMsg = methods._getErrorMessage(form, field,rules[i], rules, i, options, methods._dateTimeRange);
						}
						if (errorMsg) required = true;
						options.showArrow = false;
						break;
					case "maxCheckbox":
						field = $(form.find("input[name='" + fieldName + "']"));
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._maxCheckbox);
						break;
					case "minCheckbox":
						field = $(form.find("input[name='" + fieldName + "']"));
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._minCheckbox);
						break;
					case "equals":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._equals);
						break;
					case "funcCall":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._funcCall);
						break;
					case "creditCard":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._creditCard);
						break;
					case "condRequired":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._condRequired);
						if (errorMsg !== undefined) {
							required = true;
						}
						break;
					case "funcCallRequired":
						errorMsg = methods._getErrorMessage(form, field, rules[i], rules, i, options, methods._funcCallRequired);
						if (errorMsg !== undefined) {
							required = true;
						}
						break;

					default:
				}

				var end_validation = false;

				// If we were passed back an message object, check what the status was to determine what to do
				if (typeof errorMsg == "object") {
					switch (errorMsg.status) {
						case "_break":
							end_validation = true;
							break;
						// If we have an error message, set errorMsg to the error message
						case "_error":
							errorMsg = errorMsg.message;
							break;
						// If we want to throw an error, but not show a prompt, return early with true
						case "_error_no_prompt":
							return true;
							break;
						// Anything else we continue on
						default:
							break;
					}
				}

				//funcCallRequired, first in rules, and has error, skip anything else
				if( i==0 && str.indexOf('funcCallRequired')==0 && errorMsg !== undefined ){
					if(promptText != '') {
						promptText += "<br/>";
					}
					promptText += errorMsg;
					options.isError=true;
					field_errors++;
					end_validation=true;
				}

				// If it has been specified that validation should end now, break
				if (end_validation) {
					break;
				}

				// If we have a string, that means that we have an error, so add it to the error message.
				if (typeof errorMsg == 'string') {
					if(promptText != '') {
						promptText += "<br/>";
					}
					promptText += errorMsg;
					options.isError = true;
					field_errors++;
				}
			}
			// If the rules required is not added, an empty field is not validated
			//the 3rd condition is added so that even empty password fields should be equal
			//otherwise if one is filled and another left empty, the "equal" condition would fail
			//which does not make any sense
			if(!required && !(field.val()) && field.val().length < 1 && $.inArray('equals', rules) < 0) options.isError = false;

			// Hack for radio/checkbox group button, the validation go into the
			// first radio/checkbox of the group
			var fieldType = field.prop("type");
			var positionType=field.data("promptPosition") || options.promptPosition;

			if ((fieldType == "radio" || fieldType == "checkbox") && form.find("input[name='" + fieldName + "']").length > 1) {
				if(positionType === 'inline') {
					field = $(form.find("input[name='" + fieldName + "'][type!=hidden]:last"));
				} else {
				field = $(form.find("input[name='" + fieldName + "'][type!=hidden]:first"));
				}
				options.showArrow = options.showArrowOnRadioAndCheckbox;
			}

			if(field.is(":hidden") && options.prettySelect) {
				field = form.find("#" + options.usePrefix + methods._jqSelector(field.attr('id')) + options.useSuffix);
			}

			if (options.isError && options.showPrompts){
				methods._showPrompt(field, promptText, promptType, false, options);
			}else{
				if (!isAjaxValidator) methods._closePrompt(field);
			}

			if (!isAjaxValidator) {
				field.trigger("jqv.field.result", [field, options.isError, promptText]);
			}

			/* Record error */
			var errindex = $.inArray(field[0], options.InvalidFields);
			if (errindex == -1) {
				if (options.isError)
				options.InvalidFields.push(field[0]);
			} else if (!options.isError) {
				options.InvalidFields.splice(errindex, 1);
			}

			methods._handleStatusCssClasses(field, options);

			/* run callback function for each field */
			if (options.isError && options.onFieldFailure)
				options.onFieldFailure(field);

			if (!options.isError && options.onFieldSuccess)
				options.onFieldSuccess(field);

			return options.isError;
		},
		/**
		* Handling css classes of fields indicating result of validation
		*
		* @param {jqObject}
		*            field
		* @param {Array[String]}
		*            field's validation rules
		* @private
		*/
		_handleStatusCssClasses: function(field, options) {
			/* remove all classes */
			if(options.addSuccessCssClassToField)
				field.removeClass(options.addSuccessCssClassToField);

			if(options.addFailureCssClassToField)
				field.removeClass(options.addFailureCssClassToField);

			/* Add classes */
			if (options.addSuccessCssClassToField && !options.isError)
				field.addClass(options.addSuccessCssClassToField);

			if (options.addFailureCssClassToField && options.isError)
				field.addClass(options.addFailureCssClassToField);
		},

		 /********************
		  * _getErrorMessage
		  *
		  * @param form
		  * @param field
		  * @param rule
		  * @param rules
		  * @param i
		  * @param options
		  * @param originalValidationMethod
		  * @return {*}
		  * @private
		  */
		 _getErrorMessage:function (form, field, rule, rules, i, options, originalValidationMethod) {
			 // If we are using the custon validation type, build the index for the rule.
			 // Otherwise if we are doing a function call, make the call and return the object
			 // that is passed back.
	 		 var rule_index = jQuery.inArray(rule, rules);
			 if (rule === "custom" || rule === "funcCall" || rule === "funcCallRequired") {
				 var custom_validation_type = rules[rule_index + 1];
				 rule = rule + "[" + custom_validation_type + "]";
				 // Delete the rule from the rules array so that it doesn't try to call the
			    // same rule over again
			    delete(rules[rule_index]);
			 }
			 // Change the rule to the composite rule, if it was different from the original
			 var alteredRule = rule;


			 var element_classes = (field.attr("data-validation-engine")) ? field.attr("data-validation-engine") : field.attr("class");
			 var element_classes_array = element_classes.split(" ");

			 // Call the original validation method. If we are dealing with dates or checkboxes, also pass the form
			 var errorMsg;
			 if (rule == "future" || rule == "past"  || rule == "maxCheckbox" || rule == "minCheckbox") {
				 errorMsg = originalValidationMethod(form, field, rules, i, options);
			 } else {
				 errorMsg = originalValidationMethod(field, rules, i, options);
			 }

			 // If the original validation method returned an error and we have a custom error message,
			 // return the custom message instead. Otherwise return the original error message.
			 if (errorMsg != undefined) {
				 var custom_message = methods._getCustomErrorMessage($(field), element_classes_array, alteredRule, options);
				 if (custom_message) errorMsg = custom_message;
			 }
			 return errorMsg;

		 },
		 _getCustomErrorMessage:function (field, classes, rule, options) {
			var custom_message = false;
			var validityProp = /^custom\[.*\]$/.test(rule) ? methods._validityProp["custom"] : methods._validityProp[rule];
			 // If there is a validityProp for this rule, check to see if the field has an attribute for it
			if (validityProp != undefined) {
				custom_message = field.attr("data-errormessage-"+validityProp);
				// If there was an error message for it, return the message
				if (custom_message != undefined)
					return custom_message;
			}
			custom_message = field.attr("data-errormessage");
			 // If there is an inline custom error message, return it
			if (custom_message != undefined)
				return custom_message;
			var id = '#' + field.attr("id");
			// If we have custom messages for the element's id, get the message for the rule from the id.
			// Otherwise, if we have custom messages for the element's classes, use the first class message we find instead.
			if (typeof options.custom_error_messages[id] != "undefined" &&
				typeof options.custom_error_messages[id][rule] != "undefined" ) {
						  custom_message = options.custom_error_messages[id][rule]['message'];
			} else if (classes.length > 0) {
				for (var i = 0; i < classes.length && classes.length > 0; i++) {
					 var element_class = "." + classes[i];
					if (typeof options.custom_error_messages[element_class] != "undefined" &&
						typeof options.custom_error_messages[element_class][rule] != "undefined") {
							custom_message = options.custom_error_messages[element_class][rule]['message'];
							break;
					}
				}
			}
			if (!custom_message &&
				typeof options.custom_error_messages[rule] != "undefined" &&
				typeof options.custom_error_messages[rule]['message'] != "undefined"){
					 custom_message = options.custom_error_messages[rule]['message'];
			 }
			 return custom_message;
		 },
		 _validityProp: {
			 "required": "value-missing",
			 "custom": "custom-error",
			 "groupRequired": "value-missing",
			 "ajax": "custom-error",
			 "minSize": "range-underflow",
			 "maxSize": "range-overflow",
			 "min": "range-underflow",
			 "max": "range-overflow",
			 "past": "type-mismatch",
			 "future": "type-mismatch",
			 "dateRange": "type-mismatch",
			 "dateTimeRange": "type-mismatch",
			 "maxCheckbox": "range-overflow",
			 "minCheckbox": "range-underflow",
			 "equals": "pattern-mismatch",
			 "funcCall": "custom-error",
			 "funcCallRequired": "custom-error",
			 "creditCard": "pattern-mismatch",
			 "condRequired": "value-missing"
		 },
		/**
		* Required validation
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @param {bool} condRequired flag when method is used for internal purpose in condRequired check
		* @return an error string if validation failed
		*/
		_required: function(field, rules, i, options, condRequired) {
			switch (field.prop("type")) {
				case "radio":
				case "checkbox":
					// new validation style to only check dependent field
					if (condRequired) {
						if (!field.prop('checked')) {
							return options.allrules[rules[i]].alertTextCheckboxMultiple;
						}
						break;
					}
					// old validation style
					var form = field.closest("form, .validationEngineContainer");
					var name = field.attr("name");
					if (form.find("input[name='" + name + "']:checked").length == 0) {
						if (form.find("input[name='" + name + "']:visible").length == 1)
							return options.allrules[rules[i]].alertTextCheckboxe;
						else
							return options.allrules[rules[i]].alertTextCheckboxMultiple;
					}
					break;
				case "text":
				case "password":
				case "textarea":
				case "file":
				case "select-one":
				case "select-multiple":
				default:
					var field_val      = $.trim( field.val()                               );
					var dv_placeholder = $.trim( field.attr("data-validation-placeholder") );
					var placeholder    = $.trim( field.attr("placeholder")                 );
					if (
						   ( !field_val                                    )
						|| ( dv_placeholder && field_val == dv_placeholder )
						|| ( placeholder    && field_val == placeholder    )
					) {
						return options.allrules[rules[i]].alertText;
					}
					break;
			}
		},
		/**
		* Validate that 1 from the group field is required
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_groupRequired: function(field, rules, i, options) {
			var classGroup = "["+options.validateAttribute+"*=" +rules[i + 1] +"]";
			var isValid = false;
			// Check all fields from the group
			field.closest("form, .validationEngineContainer").find(classGroup).each(function(){
				if(!methods._required($(this), rules, i, options)){
					isValid = true;
					return false;
				}
			});

			if(!isValid) {
		  return options.allrules[rules[i]].alertText;
		}
		},
		/**
		* Validate rules
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_custom: function(field, rules, i, options) {
			var customRule = rules[i + 1];
			var rule = options.allrules[customRule];
			var fn;
			if(!rule) {
				alert("jqv:custom rule not found - "+customRule);
				return;
			}

			if(rule["regex"]) {
				 var ex=rule.regex;
					if(!ex) {
						alert("jqv:custom regex not found - "+customRule);
						return;
					}
					var pattern = new RegExp(ex);

					if (!pattern.test(field.val())) return options.allrules[customRule].alertText;

			} else if(rule["func"]) {
				fn = rule["func"];

				if (typeof(fn) !== "function") {
					alert("jqv:custom parameter 'function' is no function - "+customRule);
						return;
				}

				if (!fn(field, rules, i, options))
					return options.allrules[customRule].alertText;
			} else {
				alert("jqv:custom type not allowed "+customRule);
					return;
			}
		},
		/**
		* Validate custom function outside of the engine scope
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_funcCall: function(field, rules, i, options) {
			var functionName = rules[i + 1];
			var fn;
			if(functionName.indexOf('.') >-1)
			{
				var namespaces = functionName.split('.');
				var scope = window;
				while(namespaces.length)
				{
					scope = scope[namespaces.shift()];
				}
				fn = scope;
			}
			else
				fn = window[functionName] || options.customFunctions[functionName];
			if (typeof(fn) == 'function')
				return fn(field, rules, i, options);

		},
		_funcCallRequired: function(field, rules, i, options) {
			return methods._funcCall(field,rules,i,options);
		},
		/**
		* Field match
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_equals: function(field, rules, i, options) {
			var equalsField = rules[i + 1];

			if (field.val() != $("#" + equalsField).val())
				return options.allrules.equals.alertText;
		},
		/**
		* Check the maximum size (in characters)
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_maxSize: function(field, rules, i, options) {
			var max = rules[i + 1];
			var len = field.val().length;

			if (len > max) {
				var rule = options.allrules.maxSize;
				return rule.alertText + max + rule.alertText2;
			}
		},
		/**
		* Check the minimum size (in characters)
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_minSize: function(field, rules, i, options) {
			var min = rules[i + 1];
			var len = field.val().length;

			if (len < min) {
				var rule = options.allrules.minSize;
				return rule.alertText + min + rule.alertText2;
			}
		},
		/**
		* Check number minimum value
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_min: function(field, rules, i, options) {
			var min = parseFloat(rules[i + 1]);
			var len = parseFloat(field.val());

			if (len < min) {
				var rule = options.allrules.min;
				if (rule.alertText2) return rule.alertText + min + rule.alertText2;
				return rule.alertText + min;
			}
		},
		/**
		* Check number maximum value
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_max: function(field, rules, i, options) {
			var max = parseFloat(rules[i + 1]);
			var len = parseFloat(field.val());

			if (len >max ) {
				var rule = options.allrules.max;
				if (rule.alertText2) return rule.alertText + max + rule.alertText2;
				//orefalo: to review, also do the translations
				return rule.alertText + max;
			}
		},
		/**
		* Checks date is in the past
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_past: function(form, field, rules, i, options) {

			var p=rules[i + 1];
			var fieldAlt = $(form.find("*[name='" + p.replace(/^#+/, '') + "']"));
			var pdate;

			if (p.toLowerCase() == "now") {
				pdate = new Date();
			} else if (undefined != fieldAlt.val()) {
				if (fieldAlt.is(":disabled"))
					return;
				pdate = methods._parseDate(fieldAlt.val());
			} else {
				pdate = methods._parseDate(p);
			}
			var vdate = methods._parseDate(field.val());

			if (vdate > pdate ) {
				var rule = options.allrules.past;
				if (rule.alertText2) return rule.alertText + methods._dateToString(pdate) + rule.alertText2;
				return rule.alertText + methods._dateToString(pdate);
			}
		},
		/**
		* Checks date is in the future
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_future: function(form, field, rules, i, options) {

			var p=rules[i + 1];
			var fieldAlt = $(form.find("*[name='" + p.replace(/^#+/, '') + "']"));
			var pdate;

			if (p.toLowerCase() == "now") {
				pdate = new Date();
			} else if (undefined != fieldAlt.val()) {
				if (fieldAlt.is(":disabled"))
					return;
				pdate = methods._parseDate(fieldAlt.val());
			} else {
				pdate = methods._parseDate(p);
			}
			var vdate = methods._parseDate(field.val());

			if (vdate < pdate ) {
				var rule = options.allrules.future;
				if (rule.alertText2)
					return rule.alertText + methods._dateToString(pdate) + rule.alertText2;
				return rule.alertText + methods._dateToString(pdate);
			}
		},
		/**
		* Checks if valid date
		*
		* @param {string} date string
		* @return a bool based on determination of valid date
		*/
		_isDate: function (value) {
			var dateRegEx = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/);
			return dateRegEx.test(value);
		},
		/**
		* Checks if valid date time
		*
		* @param {string} date string
		* @return a bool based on determination of valid date time
		*/
		_isDateTime: function (value){
			var dateTimeRegEx = new RegExp(/^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/);
			return dateTimeRegEx.test(value);
		},
		//Checks if the start date is before the end date
		//returns true if end is later than start
		_dateCompare: function (start, end) {
			return (new Date(start.toString()) < new Date(end.toString()));
		},
		/**
		* Checks date range
		*
		* @param {jqObject} first field name
		* @param {jqObject} second field name
		* @return an error string if validation failed
		*/
		_dateRange: function (field, rules, i, options) {
			//are not both populated
			if ((!options.firstOfGroup[0].value && options.secondOfGroup[0].value) || (options.firstOfGroup[0].value && !options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}

			//are not both dates
			if (!methods._isDate(options.firstOfGroup[0].value) || !methods._isDate(options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}

			//are both dates but range is off
			if (!methods._dateCompare(options.firstOfGroup[0].value, options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}
		},
		/**
		* Checks date time range
		*
		* @param {jqObject} first field name
		* @param {jqObject} second field name
		* @return an error string if validation failed
		*/
		_dateTimeRange: function (field, rules, i, options) {
			//are not both populated
			if ((!options.firstOfGroup[0].value && options.secondOfGroup[0].value) || (options.firstOfGroup[0].value && !options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}
			//are not both dates
			if (!methods._isDateTime(options.firstOfGroup[0].value) || !methods._isDateTime(options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}
			//are both dates but range is off
			if (!methods._dateCompare(options.firstOfGroup[0].value, options.secondOfGroup[0].value)) {
				return options.allrules[rules[i]].alertText + options.allrules[rules[i]].alertText2;
			}
		},
		/**
		* Max number of checkbox selected
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_maxCheckbox: function(form, field, rules, i, options) {

			var nbCheck = rules[i + 1];
			var groupname = field.attr("name");
			var groupSize = form.find("input[name='" + groupname + "']:checked").length;
			if (groupSize > nbCheck) {
				options.showArrow = false;
				if (options.allrules.maxCheckbox.alertText2)
					 return options.allrules.maxCheckbox.alertText + " " + nbCheck + " " + options.allrules.maxCheckbox.alertText2;
				return options.allrules.maxCheckbox.alertText;
			}
		},
		/**
		* Min number of checkbox selected
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_minCheckbox: function(form, field, rules, i, options) {

			var nbCheck = rules[i + 1];
			var groupname = field.attr("name");
			var groupSize = form.find("input[name='" + groupname + "']:checked").length;
			if (groupSize < nbCheck) {
				options.showArrow = false;
				return options.allrules.minCheckbox.alertText + " " + nbCheck + " " + options.allrules.minCheckbox.alertText2;
			}
		},
		/**
		* Checks that it is a valid credit card number according to the
		* Luhn checksum algorithm.
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return an error string if validation failed
		*/
		_creditCard: function(field, rules, i, options) {
			//spaces and dashes may be valid characters, but must be stripped to calculate the checksum.
			var valid = false, cardNumber = field.val().replace(/ +/g, '').replace(/-+/g, '');

			var numDigits = cardNumber.length;
			if (numDigits >= 14 && numDigits <= 16 && parseInt(cardNumber) > 0) {

				var sum = 0, i = numDigits - 1, pos = 1, digit, luhn = new String();
				do {
					digit = parseInt(cardNumber.charAt(i));
					luhn += (pos++ % 2 == 0) ? digit * 2 : digit;
				} while (--i >= 0)

				for (i = 0; i < luhn.length; i++) {
					sum += parseInt(luhn.charAt(i));
				}
				valid = sum % 10 == 0;
			}
			if (!valid) return options.allrules.creditCard.alertText;
		},
		/**
		* Ajax field validation
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		*            user options
		* @return nothing! the ajax validator handles the prompts itself
		*/
		 _ajax: function(field, rules, i, options) {

			 var errorSelector = rules[i + 1];
			 var rule = options.allrules[errorSelector];
			 var extraData = rule.extraData;
			 var extraDataDynamic = rule.extraDataDynamic;
			 var data = {
				"fieldId" : field.attr("id"),
				"fieldValue" : field.val()
			 };

			 if (typeof extraData === "object") {
				$.extend(data, extraData);
			 } else if (typeof extraData === "string") {
				var tempData = extraData.split("&");
				for(var i = 0; i < tempData.length; i++) {
					var values = tempData[i].split("=");
					if (values[0] && values[0]) {
						data[values[0]] = values[1];
					}
				}
			 }

			 if (extraDataDynamic) {
				 var tmpData = [];
				 var domIds = String(extraDataDynamic).split(",");
				 for (var i = 0; i < domIds.length; i++) {
					 var id = domIds[i];
					 if ($(id).length) {
						 var inputValue = field.closest("form, .validationEngineContainer").find(id).val();
						 var keyValue = id.replace('#', '') + '=' + escape(inputValue);
						 data[id.replace('#', '')] = inputValue;
					 }
				 }
			 }

			 // If a field change event triggered this we want to clear the cache for this ID
			 if (options.eventTrigger == "field") {
				delete(options.ajaxValidCache[field.attr("id")]);
			 }

			 // If there is an error or if the the field is already validated, do not re-execute AJAX
			 if (!options.isError && !methods._checkAjaxFieldStatus(field.attr("id"), options)) {
				 $.ajax({
					 type: options.ajaxFormValidationMethod,
					 url: rule.url,
					 cache: false,
					 dataType: "json",
					 data: data,
					 field: field,
					 rule: rule,
					 methods: methods,
					 options: options,
					 beforeSend: function() {},
					 error: function(data, transport) {
						if (options.onFailure) {
							options.onFailure(data, transport);
						} else {
							methods._ajaxError(data, transport);
						}
					 },
					 success: function(json) {

						 // asynchronously called on success, data is the json answer from the server
						 var errorFieldId = json[0];
						 //var errorField = $($("#" + errorFieldId)[0]);
						 var errorField = $("#"+ errorFieldId).eq(0);

						 // make sure we found the element
						 if (errorField.length == 1) {
							 var status = json[1];
							 // read the optional msg from the server
							 var msg = json[2];
							 if (!status) {
								 // Houston we got a problem - display an red prompt
								 options.ajaxValidCache[errorFieldId] = false;
								 options.isError = true;

								 // resolve the msg prompt
								 if(msg) {
									 if (options.allrules[msg]) {
										 var txt = options.allrules[msg].alertText;
										 if (txt) {
											msg = txt;
							}
									 }
								 }
								 else
									msg = rule.alertText;

								 if (options.showPrompts) methods._showPrompt(errorField, msg, "", true, options);
							 } else {
								 options.ajaxValidCache[errorFieldId] = true;

								 // resolves the msg prompt
								 if(msg) {
									 if (options.allrules[msg]) {
										 var txt = options.allrules[msg].alertTextOk;
										 if (txt) {
											msg = txt;
							}
									 }
								 }
								 else
								 msg = rule.alertTextOk;

								 if (options.showPrompts) {
									 // see if we should display a green prompt
									 if (msg)
										methods._showPrompt(errorField, msg, "pass", true, options);
									 else
										methods._closePrompt(errorField);
								}

								 // If a submit form triggered this, we want to re-submit the form
								 if (options.eventTrigger == "submit")
									field.closest("form").submit();
							 }
						 }
						 errorField.trigger("jqv.field.result", [errorField, options.isError, msg]);
					 }
				 });

				 return rule.alertTextLoad;
			 }
		 },
		/**
		* Common method to handle ajax errors
		*
		* @param {Object} data
		* @param {Object} transport
		*/
		_ajaxError: function(data, transport) {
			if(data.status == 0 && transport == null)
				alert("The page is not served from a server! ajax call failed");
			else if(typeof console != "undefined")
				console.log("Ajax error: " + data.status + " " + transport);
		},
		/**
		* date -> string
		*
		* @param {Object} date
		*/
		_dateToString: function(date) {
			return date.getFullYear()+"-"+(date.getMonth()+1)+"-"+date.getDate();
		},
		/**
		* Parses an ISO date
		* @param {String} d
		*/
		_parseDate: function(d) {

			var dateParts = d.split("-");
			if(dateParts==d)
				dateParts = d.split("/");
			if(dateParts==d) {
				dateParts = d.split(".");
				return new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);
			}
			return new Date(dateParts[0], (dateParts[1] - 1) ,dateParts[2]);
		},
		/**
		* Builds or updates a prompt with the given information
		*
		* @param {jqObject} field
		* @param {String} promptText html text to display type
		* @param {String} type the type of bubble: 'pass' (green), 'load' (black) anything else (red)
		* @param {boolean} ajaxed - use to mark fields than being validated with ajax
		* @param {Map} options user options
		*/
		 _showPrompt: function(field, promptText, type, ajaxed, options, ajaxform) {
		 	//Check if we need to adjust what element to show the prompt on
			if(field.data('jqv-prompt-at') instanceof jQuery ){
				field = field.data('jqv-prompt-at');
			} else if(field.data('jqv-prompt-at')) {
				field = $(field.data('jqv-prompt-at'));
			}

			 var prompt = methods._getPrompt(field);
			 // The ajax submit errors are not see has an error in the form,
			 // When the form errors are returned, the engine see 2 bubbles, but those are ebing closed by the engine at the same time
			 // Because no error was found befor submitting
			 if(ajaxform) prompt = false;
			 // Check that there is indded text
			 if($.trim(promptText)){
				 if (prompt)
					methods._updatePrompt(field, prompt, promptText, type, ajaxed, options);
				 else
					methods._buildPrompt(field, promptText, type, ajaxed, options);
			}
		 },
		/**
		* Builds and shades a prompt for the given field.
		*
		* @param {jqObject} field
		* @param {String} promptText html text to display type
		* @param {String} type the type of bubble: 'pass' (green), 'load' (black) anything else (red)
		* @param {boolean} ajaxed - use to mark fields than being validated with ajax
		* @param {Map} options user options
		*/
		_buildPrompt: function(field, promptText, type, ajaxed, options) {

			// create the prompt
			var prompt = $('<div>');
			prompt.addClass(methods._getClassName(field.attr("id")) + "formError");
			// add a class name to identify the parent form of the prompt
			prompt.addClass("parentForm"+methods._getClassName(field.closest('form, .validationEngineContainer').attr("id")));
			prompt.addClass("formError");

			switch (type) {
				case "pass":
					prompt.addClass("greenPopup");
					break;
				case "load":
					prompt.addClass("blackPopup");
					break;
				default:
					/* it has error  */
					//alert("unknown popup type:"+type);
			}
			if (ajaxed)
				prompt.addClass("ajaxed");



			// create the prompt content
			var promptContent = $('<div>').addClass("formErrorContent").html(promptText).appendTo(prompt);

			// determine position type
			var positionType=field.data("promptPosition") || options.promptPosition;

			// create the css arrow pointing at the field
			// note that there is no triangle on max-checkbox and radio
			if (options.showArrow) {
				var arrow = $('<div>').addClass("formErrorArrow");

				//prompt positioning adjustment support. Usage: positionType:Xshift,Yshift (for ex.: bottomLeft:+20 or bottomLeft:-20,+10)
				if (typeof(positionType)=='string')
				{
					var pos=positionType.indexOf(":");
					if(pos!=-1)
						positionType=positionType.substring(0,pos);
				}

				switch (positionType) {
					case "bottomLeft":
					case "bottomRight":
						prompt.find(".formErrorContent").before(arrow);
						arrow.addClass("formErrorArrowBottom").html('<div class="line1"><!-- --></div><div class="line2"><!-- --></div><div class="line3"><!-- --></div><div class="line4"><!-- --></div><div class="line5"><!-- --></div><div class="line6"><!-- --></div><div class="line7"><!-- --></div><div class="line8"><!-- --></div><div class="line9"><!-- --></div><div class="line10"><!-- --></div>');
						break;
					case "topLeft":
					case "topRight":
						arrow.html('<div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div>');
						prompt.append(arrow);
						break;
				}
			}
			// Add custom prompt class
			if (options.addPromptClass)
				prompt.addClass(options.addPromptClass);

            // Add custom prompt class defined in element
            var requiredOverride = field.attr('data-required-class');
            if(requiredOverride !== undefined) {
                prompt.addClass(requiredOverride);
            } else {
                if(options.prettySelect) {
                    if($('#' + field.attr('id')).next().is('select')) {
                        var prettyOverrideClass = $('#' + field.attr('id').substr(options.usePrefix.length).substring(options.useSuffix.length)).attr('data-required-class');
                        if(prettyOverrideClass !== undefined) {
                            prompt.addClass(prettyOverrideClass);
                        }
                    }
                }
            }

			prompt.css({
				"opacity": 0
			});
			if(positionType === 'inline') {
				prompt.addClass("inline");
				if(typeof field.attr('data-prompt-target') !== 'undefined' && $('#'+field.attr('data-prompt-target')).length > 0) {
					prompt.appendTo($('#'+field.attr('data-prompt-target')));
				} else {
					field.after(prompt);
				}
			} else {
				field.before(prompt);
			}

			var pos = methods._calculatePosition(field, prompt, options);
			// Support RTL layouts by @yasser_lotfy ( Yasser Lotfy )
			if ($('body').hasClass('rtl')) {
				prompt.css({
					'position': positionType === 'inline' ? 'relative' : 'absolute',
					"top": pos.callerTopPosition,
					"left": "initial",
					"right": pos.callerleftPosition,
					"marginTop": pos.marginTopSize,
					"opacity": 0
				}).data("callerField", field);
		    	} else {
				prompt.css({
					'position': positionType === 'inline' ? 'relative' : 'absolute',
					"top": pos.callerTopPosition,
					"left": pos.callerleftPosition,
					"right": "initial",
					"marginTop": pos.marginTopSize,
					"opacity": 0
				}).data("callerField", field);
		    	}


			if (options.autoHidePrompt) {
				setTimeout(function(){
					prompt.animate({
						"opacity": 0
					},function(){
						prompt.closest('.formError').remove();
					});
				}, options.autoHideDelay);
			}
			return prompt.animate({
				"opacity": 0.87
			});
		},
		/**
		* Updates the prompt text field - the field for which the prompt
		* @param {jqObject} field
		* @param {String} promptText html text to display type
		* @param {String} type the type of bubble: 'pass' (green), 'load' (black) anything else (red)
		* @param {boolean} ajaxed - use to mark fields than being validated with ajax
		* @param {Map} options user options
		*/
		_updatePrompt: function(field, prompt, promptText, type, ajaxed, options, noAnimation) {

			if (prompt) {
				if (typeof type !== "undefined") {
					if (type == "pass")
						prompt.addClass("greenPopup");
					else
						prompt.removeClass("greenPopup");

					if (type == "load")
						prompt.addClass("blackPopup");
					else
						prompt.removeClass("blackPopup");
				}
				if (ajaxed)
					prompt.addClass("ajaxed");
				else
					prompt.removeClass("ajaxed");

				prompt.find(".formErrorContent").html(promptText);

				var pos = methods._calculatePosition(field, prompt, options);
				// Support RTL layouts by @yasser_lotfy ( Yasser Lotfy )
				if ($('body').hasClass('rtl')) {
					var css = {"top": pos.callerTopPosition,
					"left": "initial",
					"right": pos.callerleftPosition,
					"marginTop": pos.marginTopSize,
					"opacity": 0.87};
				} else {
					var css = {"top": pos.callerTopPosition,
					"left": pos.callerleftPosition,
					"right": "initial",
					"marginTop": pos.marginTopSize,
					"opacity": 0.87};
				}

                prompt.css({
                    "opacity": 0,
                    "display": "block"
                });

				if (noAnimation)
					prompt.css(css);
				else
					prompt.animate(css);
			}
		},
		/**
		* Closes the prompt associated with the given field
		*
		* @param {jqObject}
		*            field
		*/
		 _closePrompt: function(field) {
			 var prompt = methods._getPrompt(field);
			 if (prompt)
				 prompt.fadeTo("fast", 0, function() {
					 prompt.closest('.formError').remove();
				 });
		 },
		 closePrompt: function(field) {
			 return methods._closePrompt(field);
		 },
		/**
		* Returns the error prompt matching the field if any
		*
		* @param {jqObject}
		*            field
		* @return undefined or the error prompt (jqObject)
		*/
		_getPrompt: function(field) {
				var formId = $(field).closest('form, .validationEngineContainer').attr('id');
			var className = methods._getClassName(field.attr("id")) + "formError";
				var match = $("." + methods._escapeExpression(className) + '.parentForm' + methods._getClassName(formId))[0];
			if (match)
			return $(match);
		},
		/**
		  * Returns the escapade classname
		  *
		  * @param {selector}
		  *            className
		  */
		  _escapeExpression: function (selector) {
			  return selector.replace(/([#;&,\.\+\*\~':"\!\^$\[\]\(\)=>\|])/g, "\\$1");
		  },
		/**
		 * returns true if we are in a RTLed document
		 *
		 * @param {jqObject} field
		 */
		isRTL: function(field)
		{
			var $document = $(document);
			var $body = $('body');
			var rtl =
				(field && field.hasClass('rtl')) ||
				(field && (field.attr('dir') || '').toLowerCase()==='rtl') ||
				$document.hasClass('rtl') ||
				($document.attr('dir') || '').toLowerCase()==='rtl' ||
				$body.hasClass('rtl') ||
				($body.attr('dir') || '').toLowerCase()==='rtl';
			return Boolean(rtl);
		},
		/**
		* Calculates prompt position
		*
		* @param {jqObject}
		*            field
		* @param {jqObject}
		*            the prompt
		* @param {Map}
		*            options
		* @return positions
		*/
		_calculatePosition: function (field, promptElmt, options) {

			var promptTopPosition, promptleftPosition, marginTopSize;
			var fieldWidth 	= field.width();
			var fieldLeft 	= field.position().left;
			var fieldTop 	=  field.position().top;
			var fieldHeight 	=  field.height();
			var promptHeight = promptElmt.height();


			// is the form contained in an overflown container?
			promptTopPosition = promptleftPosition = 0;
			// compensation for the arrow
			marginTopSize = -promptHeight;


			//prompt positioning adjustment support
			//now you can adjust prompt position
			//usage: positionType:Xshift,Yshift
			//for example:
			//   bottomLeft:+20 means bottomLeft position shifted by 20 pixels right horizontally
			//   topRight:20, -15 means topRight position shifted by 20 pixels to right and 15 pixels to top
			//You can use +pixels, - pixels. If no sign is provided than + is default.
			var positionType=field.data("promptPosition") || options.promptPosition;
			var shift1="";
			var shift2="";
			var shiftX=0;
			var shiftY=0;
			if (typeof(positionType)=='string') {
				//do we have any position adjustments ?
				if (positionType.indexOf(":")!=-1) {
					shift1=positionType.substring(positionType.indexOf(":")+1);
					positionType=positionType.substring(0,positionType.indexOf(":"));

					//if any advanced positioning will be needed (percents or something else) - parser should be added here
					//for now we use simple parseInt()

					//do we have second parameter?
					if (shift1.indexOf(",") !=-1) {
						shift2=shift1.substring(shift1.indexOf(",") +1);
						shift1=shift1.substring(0,shift1.indexOf(","));
						shiftY=parseInt(shift2);
						if (isNaN(shiftY)) shiftY=0;
					};

					shiftX=parseInt(shift1);
					if (isNaN(shift1)) shift1=0;

				};
			};


			switch (positionType) {
				default:
				case "topRight":
					promptleftPosition +=  fieldLeft + fieldWidth - 27;
					promptTopPosition +=  fieldTop;
					break;

				case "topLeft":
					promptTopPosition +=  fieldTop;
					promptleftPosition += fieldLeft;
					break;

				case "centerRight":
					promptTopPosition = fieldTop+4;
					marginTopSize = 0;
					promptleftPosition= fieldLeft + field.outerWidth(true)+5;
					break;
				case "centerLeft":
					promptleftPosition = fieldLeft - (promptElmt.width() + 2);
					promptTopPosition = fieldTop+4;
					marginTopSize = 0;

					break;

				case "bottomLeft":
					promptTopPosition = fieldTop + field.height() + 5;
					marginTopSize = 0;
					promptleftPosition = fieldLeft;
					break;
				case "bottomRight":
					promptleftPosition = fieldLeft + fieldWidth - 27;
					promptTopPosition =  fieldTop +  field.height() + 5;
					marginTopSize = 0;
					break;
				case "inline":
					promptleftPosition = 0;
					promptTopPosition = 0;
					marginTopSize = 0;
			};



			//apply adjusments if any
			promptleftPosition += shiftX;
			promptTopPosition  += shiftY;

			return {
				"callerTopPosition": promptTopPosition + "px",
				"callerleftPosition": promptleftPosition + "px",
				"marginTopSize": marginTopSize + "px"
			};
		},
		/**
		* Saves the user options and variables in the form.data
		*
		* @param {jqObject}
		*            form - the form where the user option should be saved
		* @param {Map}
		*            options - the user options
		* @return the user options (extended from the defaults)
		*/
		 _saveOptions: function(form, options) {

			 // is there a language localisation ?
			 if ($.validationEngineLanguage)
			 var allRules = $.validationEngineLanguage.allRules;
			 else
			 $.error("jQuery.validationEngine rules are not loaded, plz add localization files to the page");
			 // --- Internals DO NOT TOUCH or OVERLOAD ---
			 // validation rules and i18
			 $.validationEngine.defaults.allrules = allRules;

			 var userOptions = $.extend(true,{},$.validationEngine.defaults,options);

			 form.data('jqv', userOptions);
			 return userOptions;
		 },

		 /**
		 * Removes forbidden characters from class name
		 * @param {String} className
		 */
		 _getClassName: function(className) {
			 if(className)
				 return className.replace(/:/g, "_").replace(/\./g, "_");
					  },
		/**
		 * Escape special character for jQuery selector
		 * http://totaldev.com/content/escaping-characters-get-valid-jquery-id
		 * @param {String} selector
		 */
		 _jqSelector: function(str){
			return str.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
		},
		/**
		* Conditionally required field
		*
		* @param {jqObject} field
		* @param {Array[String]} rules
		* @param {int} i rules index
		* @param {Map}
		* user options
		* @return an error string if validation failed
		*/
		_condRequired: function(field, rules, i, options) {
			var idx, dependingField;

			for(idx = (i + 1); idx < rules.length; idx++) {
				dependingField = jQuery("#" + rules[idx]).first();

				/* Use _required for determining wether dependingField has a value.
				 * There is logic there for handling all field types, and default value; so we won't replicate that here
				 * Indicate this special use by setting the last parameter to true so we only validate the dependingField on chackboxes and radio buttons (#462)
				 */
				if (dependingField.length && methods._required(dependingField, ["required"], 0, options, true) == undefined) {
					/* We now know any of the depending fields has a value,
					 * so we can validate this field as per normal required code
					 */
					return methods._required(field, ["required"], 0, options);
				}
			}
		},

	    _submitButtonClick: function(event) {
	        var button = $(this);
	        var form = button.closest('form, .validationEngineContainer');
	        form.data("jqv_submitButton", button.attr("id"));
	    }
		  };

	 /**
	 * Plugin entry point.
	 * You may pass an action as a parameter or a list of options.
	 * if none, the init and attach methods are being called.
	 * Remember: if you pass options, the attached method is NOT called automatically
	 *
	 * @param {String}
	 *            method (optional) action
	 */
	 $.fn.validationEngine = function(method) {

		 var form = $(this);
		 if(!form[0]) return form;  // stop here if the form does not exist

		 if (typeof(method) == 'string' && method.charAt(0) != '_' && methods[method]) {

			 // make sure init is called once
			 if(method != "showPrompt" && method != "hide" && method != "hideAll")
			 methods.init.apply(form);

			 return methods[method].apply(form, Array.prototype.slice.call(arguments, 1));
		 } else if (typeof method == 'object' || !method) {

			 // default constructor with or without arguments
			 methods.init.apply(form, arguments);
			 return methods.attach.apply(form);
		 } else {
			 $.error('Method ' + method + ' does not exist in jQuery.validationEngine');
		 }
	};



	// LEAK GLOBAL OPTIONS
	$.validationEngine= {fieldIdCounter: 0,defaults:{

		// Name of the event triggering field validation
		validationEventTrigger: "blur",
		// Automatically scroll viewport to the first error
		scroll: true,
		// Focus on the first input
		focusFirstField:true,
		// Show prompts, set to false to disable prompts
		showPrompts: true,
		// Should we attempt to validate non-visible input fields contained in the form? (Useful in cases of tabbed containers, e.g. jQuery-UI tabs)
		validateNonVisibleFields: false,
		// ignore the validation for fields with this specific class (Useful in cases of tabbed containers AND hidden fields we don't want to validate)
		ignoreFieldsWithClass: 'ignoreMe',
		// Opening box position, possible locations are: topLeft,
		// topRight, bottomLeft, centerRight, bottomRight, inline
		// inline gets inserted after the validated field or into an element specified in data-prompt-target
		promptPosition: "topRight",
		bindMethod:"bind",
		// internal, automatically set to true when it parse a _ajax rule
		inlineAjax: false,
		// if set to true, the form data is sent asynchronously via ajax to the form.action url (get)
		ajaxFormValidation: false,
		// The url to send the submit ajax validation (default to action)
		ajaxFormValidationURL: false,
		// HTTP method used for ajax validation
		ajaxFormValidationMethod: 'get',
		// Ajax form validation callback method: boolean onComplete(form, status, errors, options)
		// retuns false if the form.submit event needs to be canceled.
		onAjaxFormComplete: $.noop,
		// called right before the ajax call, may return false to cancel
		onBeforeAjaxFormValidation: $.noop,
		// Stops form from submitting and execute function assiciated with it
		onValidationComplete: false,

		// Used when you have a form fields too close and the errors messages are on top of other disturbing viewing messages
		doNotShowAllErrosOnSubmit: false,
		// Object where you store custom messages to override the default error messages
		custom_error_messages:{},
		// true if you want to validate the input fields on blur event
		binded: true,
		// set to true if you want to validate the input fields on blur only if the field it's not empty
		notEmpty: false,
		// set to true, when the prompt arrow needs to be displayed
		showArrow: true,
		// set to false, determines if the prompt arrow should be displayed when validating
		// checkboxes and radio buttons
		showArrowOnRadioAndCheckbox: false,
		// did one of the validation fail ? kept global to stop further ajax validations
		isError: false,
		// Limit how many displayed errors a field can have
		maxErrorsPerField: false,

		// Caches field validation status, typically only bad status are created.
		// the array is used during ajax form validation to detect issues early and prevent an expensive submit
		ajaxValidCache: {},
		// Auto update prompt position after window resize
		autoPositionUpdate: false,

		InvalidFields: [],
		onFieldSuccess: false,
		onFieldFailure: false,
		onSuccess: false,
		onFailure: false,
		validateAttribute: "class",
		addSuccessCssClassToField: "",
		addFailureCssClassToField: "",

		// Auto-hide prompt
		autoHidePrompt: false,
		// Delay before auto-hide
		autoHideDelay: 10000,
		// Fade out duration while hiding the validations
		fadeDuration: 300,
	 // Use Prettify select library
	 prettySelect: false,
	 // Add css class on prompt
	 addPromptClass : "",
	 // Custom ID uses prefix
	 usePrefix: "",
	 // Custom ID uses suffix
	 useSuffix: "",
	 // Only show one message per error prompt
	 showOneMessage: false
	}};
	$(function(){$.validationEngine.defaults.promptPosition = methods.isRTL()?'topLeft':"topRight"});
})(jQuery);


(function($){
    $.fn.validationEngineLanguage = function(){
    };
    $.validationEngineLanguage = {
        newLang: function(){
            $.validationEngineLanguage.allRules = {
                "required": { // Add your regex rules here, you can take telephone as an example
                    "regex": "none",
                    "alertText": "* This field is required",
                    "alertTextCheckboxMultiple": "* Please select an option",
                    "alertTextCheckboxe": "* This checkbox is required",
                    "alertTextDateRange": "* Both date range fields are required"
                },
                "requiredInFunction": { 
                    "func": function(field, rules, i, options){
                        return (field.val() == "test") ? true : false;
                    },
                    "alertText": "* Field must equal test"
                },
                "dateRange": {
                    "regex": "none",
                    "alertText": "* Invalid ",
                    "alertText2": "Date Range"
                },
                "dateTimeRange": {
                    "regex": "none",
                    "alertText": "* Invalid ",
                    "alertText2": "Date Time Range"
                },
                "minSize": {
                    "regex": "none",
                    "alertText": "* Minimum ",
                    "alertText2": " characters required"
                },
                "maxSize": {
                    "regex": "none",
                    "alertText": "* Maximum ",
                    "alertText2": " characters allowed"
                },
		"groupRequired": {
                    "regex": "none",
                    "alertText": "* You must fill one of the following fields",
                    "alertTextCheckboxMultiple": "* Please select an option",
                    "alertTextCheckboxe": "* This checkbox is required"
                },
                "min": {
                    "regex": "none",
                    "alertText": "* Minimum value is "
                },
                "max": {
                    "regex": "none",
                    "alertText": "* Maximum value is "
                },
                "past": {
                    "regex": "none",
                    "alertText": "* Date prior to "
                },
                "future": {
                    "regex": "none",
                    "alertText": "* Date past "
                },	
                "maxCheckbox": {
                    "regex": "none",
                    "alertText": "* Maximum ",
                    "alertText2": " options allowed"
                },
                "minCheckbox": {
                    "regex": "none",
                    "alertText": "* Please select ",
                    "alertText2": " options"
                },
                "equals": {
                    "regex": "none",
                    "alertText": "* Fields do not match"
                },
                "creditCard": {
                    "regex": "none",
                    "alertText": "* Invalid credit card number"
                },
                "phone": {
                    // credit: jquery.h5validate.js / orefalo
                    "regex": /^([\+][0-9]{1,3}([ \.\-])?)?([\(][0-9]{1,6}[\)])?([0-9 \.\-]{1,32})(([A-Za-z \:]{1,11})?[0-9]{1,4}?)$/,
                    "alertText": "* Invalid phone number"
                },
                "email": {
                    // HTML5 compatible email regex ( http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#    e-mail-state-%28type=email%29 )
                    "regex": /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
                    "alertText": "* Invalid email address"
                },
                "fullname": {
                    "regex":/^([a-zA-Z]+[\'\,\.\-]?[a-zA-Z ]*)+[ ]([a-zA-Z]+[\'\,\.\-]?[a-zA-Z ]+)+$/,
                    "alertText":"* Must be first and last name"
                },
                "zip": {
                    "regex":/^\d{5}$|^\d{5}-\d{4}$/,
                    "alertText":"* Invalid zip format"
                },
                "integer": {
                    "regex": /^[\-\+]?\d+$/,
                    "alertText": "* Not a valid integer"
                },
                "number": {
                    // Number, including positive, negative, and floating decimal. credit: orefalo
                    "regex": /^[\-\+]?((([0-9]{1,3})([,][0-9]{3})*)|([0-9]+))?([\.]([0-9]+))?$/,
                    "alertText": "* Invalid floating decimal number"
                },
                "date": {                    
                    //	Check if date is valid by leap year
			"func": function (field) {
					var pattern = new RegExp(/^(\d{4})[\/\-\.](0?[1-9]|1[012])[\/\-\.](0?[1-9]|[12][0-9]|3[01])$/);
					var match = pattern.exec(field.val());
					if (match == null)
					   return false;
	
					var year = match[1];
					var month = match[2]*1;
					var day = match[3]*1;					
					var date = new Date(year, month - 1, day); // because months starts from 0.
	
					return (date.getFullYear() == year && date.getMonth() == (month - 1) && date.getDate() == day);
				},                		
			 "alertText": "* Invalid date, must be in YYYY-MM-DD format"
                },
                "ipv4": {
                    "regex": /^((([01]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))[.]){3}(([0-1]?[0-9]{1,2})|(2[0-4][0-9])|(25[0-5]))$/,
                    "alertText": "* Invalid IP address"
                },
                "url": {
                    "regex": /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i,
                    "alertText": "* Invalid URL"
                },
                "onlyNumberSp": {
                    "regex": /^[0-9\ ]+$/,
                    "alertText": "* Numbers only"
                },
                "onlyLetterSp": {
                    "regex": /^[a-zA-Z\ \']+$/,
                    "alertText": "* Letters only"
                },
				"onlyLetterAccentSp":{
                    "regex": /^[a-z\u00C0-\u017F\ ]+$/i,
                    "alertText": "* Letters only (accents allowed)"
                },
                "onlyLetterNumber": {
                    "regex": /^[0-9a-zA-Z]+$/,
                    "alertText": "* No special characters allowed"
                },
                // --- CUSTOM RULES -- Those are specific to the demos, they can be removed or changed to your likings
                "ajaxUserCall": {
                    "url": "ajaxValidateFieldUser",
                    // you may want to pass extra data on the ajax call
                    "extraData": "name=eric",
                    "alertText": "* This user is already taken",
                    "alertTextLoad": "* Validating, please wait"
                },
				"ajaxUserCallPhp": {
                    "url": "phpajax/ajaxValidateFieldUser.php",
                    // you may want to pass extra data on the ajax call
                    "extraData": "name=eric",
                    // if you provide an "alertTextOk", it will show as a green prompt when the field validates
                    "alertTextOk": "* This username is available",
                    "alertText": "* This user is already taken",
                    "alertTextLoad": "* Validating, please wait"
                },
                "ajaxNameCall": {
                    // remote json service location
                    "url": "ajaxValidateFieldName",
                    // error
                    "alertText": "* This name is already taken",
                    // if you provide an "alertTextOk", it will show as a green prompt when the field validates
                    "alertTextOk": "* This name is available",
                    // speaks by itself
                    "alertTextLoad": "* Validating, please wait"
                },
				 "ajaxNameCallPhp": {
	                    // remote json service location
	                    "url": "phpajax/ajaxValidateFieldName.php",
	                    // error
	                    "alertText": "* This name is already taken",
	                    // speaks by itself
	                    "alertTextLoad": "* Validating, please wait"
	                },
                "validate2fields": {
                    "alertText": "* Please input HELLO"
                },
	            //tls warning:homegrown not fielded 
                "dateFormat":{
                    "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(?:(?:0?[1-9]|1[0-2])(\/|-)(?:0?[1-9]|1\d|2[0-8]))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^(0?2(\/|-)29)(\/|-)(?:(?:0[48]00|[13579][26]00|[2468][048]00)|(?:\d\d)?(?:0[48]|[2468][048]|[13579][26]))$/,
                    "alertText": "* Invalid Date"
                },
                //tls warning:homegrown not fielded 
				"dateTimeFormat": {
	                "regex": /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1}$|^(?:(?:(?:0?[13578]|1[02])(\/|-)31)|(?:(?:0?[1,3-9]|1[0-2])(\/|-)(?:29|30)))(\/|-)(?:[1-9]\d\d\d|\d[1-9]\d\d|\d\d[1-9]\d|\d\d\d[1-9])$|^((1[012]|0?[1-9]){1}\/(0?[1-9]|[12][0-9]|3[01]){1}\/\d{2,4}\s+(1[012]|0?[1-9]){1}:(0?[1-5]|[0-6][0-9]){1}:(0?[0-6]|[0-6][0-9]){1}\s+(am|pm|AM|PM){1})$/,
                    "alertText": "* Invalid Date or Date Format",
                    "alertText2": "Expected Format: ",
                    "alertText3": "mm/dd/yyyy hh:mm:ss AM|PM or ", 
                    "alertText4": "yyyy-mm-dd hh:mm:ss AM|PM"
	            }
            };
            
        }
    };

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

          this.addClass('disabled').attr('disabled','disabled').html('<i class="fa fa-circle-o-notch fa-spin"></i> ' + this[0].dataset.loadingText);
        }
        else if(_state == 'reset')
          this.removeClass('disabled').removeAttr('disabled').html(this[0].dataset.resetText);
        else if(_state == 'update')
          this.removeClass('disabled').removeAttr('disabled').html(this[0].dataset.updateText);
        else
          this.addClass('disabled').attr('disabled','disabled').html(this[0].dataset[_state+'Text']);
    };


    $.validationEngineLanguage.newLang(); 
})(jQuery);

