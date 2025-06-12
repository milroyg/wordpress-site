<!-- Container -->
<div id="ptl-cont" class="container ptl-dashboard p-tl-cont ptl-cont">
   <div class="ptl-inner-cont">
      <div class="main-content" id="panel">
         <div class="container-fluid">
            <div class="row">
               <div class="col-12">
                  <div class="top-header text-center my-4">
                     <a href="<?php echo POST_TIMELINE_SITE; ?>">
                        <img src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/logo-new.png" alt="Image Logo">
                     </a>
                     <p class="font-weight-bold mt-2 mb-0 text-lgrey"><?php echo esc_attr__('Show Your Cool Stories To the World With Interactive &   Top-Rated Timelines.','post-timeline') ?></p>
                  </div>
               </div>
            </div>
         </div>
         <div class="container-fluid">
            <div class="row card-wrapper ptl-templates">
               <div class="col-lg-8 col-md-8">
                  <div class="ptl-card-img-box">
                     <a href="<?php echo POST_TIMELINE_PRO_LNK; ?>" target="_blank">
                        <img class="card-img-top" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/free-plugins-img1.png" alt="Image placeholder">
                     </a>
                     <a href="">
                        <img class="ptl-res-bg" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/free-plugins-res_bg.png" alt="Image placeholder">
                        <div class="card-body p-0">
                           <h3><?php echo esc_attr__('Enjoy Top-rated Post Timeline','post-timeline'); ?><br><?php echo esc_attr__(' Free Plugin','post-timeline'); ?></h3>
                           <ul class="list-unstyled">
                              <li>
                                 <i class="fa-regular fa-circle-check"></i>
                                 <span><?php echo esc_attr__('Only 1 Timeline Template','post-timeline'); ?></span>
                              </li>
                              <li>
                                 <i class="fa-regular fa-circle-check"></i>
                                 <span><?php echo esc_attr__('Non Customizable Timeline','post-timeline'); ?></span>
                              </li>
                           </ul>
                           <a href="https://posttimeline.com/timeline-templates/" target="_blank" class="btn btn-secondary"><?php echo esc_attr__('Update To Premium Plugin Now !!!','post-timeline'); ?></a>
                        </div>
                     </a>
                  </div>
                  <div class="row">
                     <div class="col-lg-12">
                        <div class="ptl-shortcod-btn">
                          <div class="ptl-shortcode-block"><div class="ptl-btn-empty"></div><button type="button" tagname="strong" data-toggle="ptl_modal" data-target="#insert-ptl-shortcode" id="ptl-shortcode-insert" class="components-button components-button is-secondary ptl-shortcode-button">Generate Shortcode</button>
                          </div>
                        </div>
                        <div class="ptl-title">
                           <h2><?php echo esc_attr__('My Timelines','post-timeline'); ?></h2>
                           <span></span>
                        </div>
                     </div>
                     <div class="col-lg-5 col-md-6 col-sm-6">
                        <div class="ptl-img-box-inner">
                           <img class="ptl-card-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/free-timeline-img.png" alt="Image placeholder">
                           <div class="card-body p-0">
                              <h4><?php echo esc_attr__('Vertical Timeline','post-timeline'); ?></h4>
                           </div>
                        </div>
                     </div>
                     <div class="col-lg-5 col-md-6 col-sm-6">
                        <div class="ptl-img-box-inner">
                           <a href="<?php echo POST_TIMELINE_PRO_LNK; ?>" target="__blank">
                              <img class="ptl-card-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/lock.png" alt="Image placeholder">
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-4">
                  <a href="<?php echo POST_TIMELINE_SITE ?>timeline-templates/" target="__blank">
                     <img class="card-img-top ptl-right-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/premium-side-image.png" alt="Image placeholder">
                  </a>
               </div>
            </div>
            <div class="row">
               <div class="col-lg-12">
                  <div class="ptl-title">
                     <h2><?php echo esc_attr__('How Timelines Work','post-timeline'); ?></h2>
                     <span></span>
                  </div>
               </div>
            </div>
            <div class="row no-gutters ptl-main-work">
               <div class="col-lg-4 col-md-6">
                  <div class="ptl-work-box">
                     <span class="ptl-count">01</span>
                     <span class="ptl-left-line"></span>
                     <span class="ptl-right-line"></span>
                     <div class="ptl-work-inner">
                       <a href="<?php echo admin_url(); ?>post-new.php?post_type=post-timeline">
                         <img class="work-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/work-icon1.png" alt="Image placeholder">
                      </a>
                        <a href="<?php echo admin_url(); ?>post-new.php?post_type=post-timeline">
                           <h5><?php echo esc_attr__('Create Your Post','post-timeline') ?></h5>
                        </a>
                        <p><?php echo esc_attr__('Instead of watching other timelines, create your own timeline post and leverage top-rated timeline features.','post-timeline') ?></p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-6">
                  <div class="ptl-work-box">
                     <span class="ptl-count">02</span>
                     <span class="ptl-left-line"></span>
                     <span class="ptl-right-line"></span>
                     <div class="ptl-work-inner">
                        <a data-toggle="ptl_modal" data-target="#insert-ptl-shortcode" href="javascript:void(0);">
                           <img class="work-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/work-icon2.png" alt="Image placeholder">
                        </a>
                        <a data-toggle="ptl_modal" data-target="#insert-ptl-shortcode" href="javascript:void(0);">
                           <h5><?php echo esc_attr__('Add Timeline Shortcode [post-timeline]','post-timeline'); ?></h5>
                        </a>
                        <p><?php echo esc_attr__('To add your specific timeline posts, you\'ll need to add a short code <b>[post-timeline]</b> on your page and use it based on your timeline goals.','post-timeline'); ?></p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-6">
                  <div class="ptl-work-box">
                     <span class="ptl-count">03</span>
                     <span class="ptl-left-line"></span>
                     <span class="ptl-right-line"></span>
                     <div class="ptl-work-inner">
                        <img class="work-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/work-icon3.png" alt="Image placeholder">
                        <h5><?php echo esc_attr__('Display Timeline','post-timeline'); ?></h5>
                        <p><?php echo esc_attr__('After adding the short code to the page, it is now its time to publish the page and get ready for the amazing timelines in action','post-timeline'); ?></p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row ptl-main-action-sec pb-4">
               <div class="col-12">
                  <div class="ptl-title">
                     <span></span>
                     <h2 class="mr-0 px-3"><?php echo esc_attr__('Want to Enhance Your Timeline Like a PRO?','post-timeline'); ?></h2>
                     <span></span>
                  </div>
               </div>
               <div class="col-lg-6 col-md-6">
                  <a href="https://www.youtube.com/playlist?list=PLD7u3Chrm7R9wj0tuvc8RsNQHekj1fHlG" class="ptl-action-box">
                     <img class="card-img-top" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/primium/video-tutorial-bg.png" alt="Image placeholder">
                     <div class="ptl-inner-box">
                        <img class="ptl-icon-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/primium/video_img.png" alt="Image placeholder">
                        <h5><?php echo esc_attr__('View Our Tutorials','post-timeline'); ?></h5>
                     </div>
                  </a>
               </div>
               <div class="col-lg-6 col-md-6">
                  <a href="<?php echo POST_TIMELINE_SITE; ?>contact-us/" class="ptl-action-box">
                     <img class="card-img-top" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/primium/get_support_bg.png" alt="Image placeholder">
                     <div class="ptl-inner-box">
                        <img class="ptl-icon-img" src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/dashboard/primium/get_support_img.png" alt="Image placeholder">
                        <h5><?php echo esc_attr__('Get Support!','post-timeline'); ?></h5>
                     </div>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>