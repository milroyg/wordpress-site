<div class="ptl-p-cont ptl_setting_cont ptl-new-bg ptl-cont">
   <!-- New Html -->
   <div class="container-fluid">
      <div class="card mb-4">
         <div class="row">
            <div class="col-lg-4 col-md-4">
               <div class="nav-wrapper">
                  <ul class="nav nav-tabs container tabbing_list" id="tabs-icons-text" role="tablist">
                     <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-basic" data-toggle="ptl_tab" href="#tabs-basic-content" role="tab" aria-controls="tabs-basic-content" aria-selected="true">
                           <span class="tab_title"><?php echo esc_attr__('General', 'post-timeline'); ?></span>
                           <i class="fa-regular fa-chess-bishop"></i>
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-styles" data-toggle="ptl_tab" href="#tabs-styles-content" role="tab" aria-controls="tabs-styles-content" aria-selected="false">
                           <span class="tab_title"><?php echo esc_attr__('Style', 'post-timeline'); ?></span>
                           <i class="fa fa-clone"></i>
                           <!-- <i class="fa-solid fa-bring-forward"></i> -->
                        </a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-custom" data-toggle="ptl_tab" href="#tabs-custom-content" role="tab" aria-controls="tabs-custom-content" aria-selected="false">
                           <span class="tab_title"><?php echo esc_attr__('Custom', 'post-timeline'); ?></span>
                           <i class="fa-solid fa-object-group"></i>
                        </a>
                     </li>
                  </ul>
               </div>
            </div>
            <div class="col-lg-8 col-md-8">
               <div class="card-body">
                  <form id="frm-ptl-setting">
                     <div class="tab-content" id="myTabContent">
                        <div class="tab-pane active" id="tabs-basic-content" role="tabpanel" aria-labelledby="tabs-basic">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Settings', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Timeline Type', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="row">
                                             <div class="col-md-10">
                                                <div class="row">
                                                   <div class="col">
                                                      <div class="custom-control custom-radio">
                                                         <input type="radio" <?php echo ($options['ptl-type'] == 'tag') ? 'checked' : '' ?> class="custom-control-input" name="ptl-type" value="tag" id="ptl-type-tag" />
                                                         <label class="custom-control-label" for="ptl-type-tag"><?php echo esc_attr__('Tag Based', 'post-timeline') ?></label>
                                                      </div>
                                                   </div>
                                                   <div class="col">
                                                      <div class="custom-control custom-radio">
                                                         <input type="radio" <?php echo ($options['ptl-type'] == 'date') ? 'checked' : '' ?> class="custom-control-input" name="ptl-type" value="date" id="ptl-type-date" />
                                                         <label class="custom-control-label" for="ptl-type-date"><?php echo esc_attr__('Date Based', 'post-timeline') ?></label>
                                                      </div>
                                                   </div>
                                                   <div class="col">
                                                      <div class="custom-control custom-radio">
                                                         <input type="radio" <?php echo ($options['ptl-type'] == 'none') ? 'checked' : '' ?> class="custom-control-input" name="ptl-type" value="none" id="ptl-type-none" />
                                                         <label class="custom-control-label" for="ptl-type-none"><?php echo esc_attr__('None', 'post-timeline') ?></label>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose whether your timeline will be tag-based or date-based.', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Sorting Order', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select id="ptl-sort" name="ptl-sort" class="custom-good-select">
                                                <option <?php echo ($options['ptl-sort'] == 'ASC') ? 'selected' : '' ?> value="ASC"><?php echo esc_attr__('Ascending', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-sort'] == 'DESC') ? 'selected' : '' ?> value="DESC"><?php echo esc_attr__('Descending', 'post-timeline') ?></option>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose which direction your timeline will be displayed in', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Select Pagination', 'post-timeline') ?><span class="ptl-go-pro"><a href="https://posttimeline.com/" target="__blank"><?php echo esc_attr__('Go Pro', 'post-timeline') ?></a></span>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select id="ptl-pagination" name="ptl-pagination" class="custom-good-select">
                                                <option <?php echo ($options['ptl-pagination'] == 'carousel') ? 'selected' : '' ?> value="off"><?php echo esc_attr__('Disable', 'post-timeline'); ?></option>
                                                <option <?php echo ($options['ptl-pagination'] == 'button') ? 'selected' : '' ?> value="button"><?php echo esc_attr__('Load more Button', 'post-timeline'); ?></option>
                                                <option disabled value=""><?php echo esc_attr__('Load more on Scroll', 'post-timeline'); ?> </option>
                                             </select>
                                          </div>
                                          <div class="options-group"></div>
                                          <div class="description"><?php echo esc_attr__('Select how more posts will be loaded on the timeline.', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Social Share Links Settings', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                       <div class="row">
                                             <div class="col-lg-8 col-md-8">
                                                <div class="row align-items-center mb-2">
                                                   <div class="col-lg-2 col-md-3 col-sm-2 col-2">
                                                      <img src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/icons-facebook.png" alt="Social Icons" style="width: 40px;">
                                                   </div>
                                                   <div class="col-lg-10 col-md-9 col-sm-10 col-10">
                                                   <?php
                                                      $ptl_facebook = isset($options['social-shares']['ptl_facebook']) ? $options['social-shares']['ptl_facebook'] : '';
                                                      $ptl_twitter = isset($options['social-shares']['ptl_twitter']) ? $options['social-shares']['ptl_twitter'] : '';
                                                      $ptl_linkedin = isset($options['social-shares']['ptl_linkedin']) ? $options['social-shares']['ptl_linkedin'] : '';
                                                      $ptl_pinterest = isset($options['social-shares']['ptl_pinterest']) ? $options['social-shares']['ptl_pinterest'] : '';
                                                   ?>
                                                      <label class="custom-toggle" for="ptl_facebook"><input type="checkbox"  <?php echo ($ptl_facebook == 'on') ? 'checked' : ''; ?>  value="<?php echo esc_attr($ptl_facebook); ?>" name="social-shares[ptl_facebook]" id="ptl_facebook" class="ptl-social-shares"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                   </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                   <div class="col-lg-2 col-md-3 col-sm-2 col-2">
                                                      <img src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/icon-twitter.png" alt="Social Icons" style="width: 40px;">
                                                   </div>
                                                   <div class="col-lg-10 col-md-9 col-sm-10 col-10">
                                                      <label class="custom-toggle" for="ptl_twitter"><input type="checkbox" <?php echo ($ptl_twitter == 'on') ? 'checked' : ''; ?>  value="<?php echo esc_attr($ptl_twitter); ?>" name="social-shares[ptl_twitter]" id="ptl_twitter" class="ptl-social-shares"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                   </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                   <div class="col-lg-2 col-md-3 col-sm-2 col-2">
                                                      <img src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/icon-linkedin.png" alt="Social Icons" style="width: 40px;">
                                                   </div>
                                                   <div class="col-lg-10 col-md-9 col-sm-10 col-10">
                                                      <label class="custom-toggle" for="ptl_linkedin"><input type="checkbox" <?php echo ($ptl_linkedin == 'on') ? 'checked' : ''; ?>  value="<?php echo esc_attr($ptl_linkedin); ?>" name="social-shares[ptl_linkedin]" id="ptl_linkedin" class="ptl-social-shares"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                   </div>
                                                </div>
                                                <div class="row align-items-center mb-2">
                                                   <div class="col-lg-2 col-md-3 col-sm-2 col-2">
                                                      <img src="<?php echo POST_TIMELINE_URL_PATH ?>admin/images/icon-pinterest.png" alt="Social Icons" style="width: 40px;">
                                                   </div>
                                                   <div class="col-lg-10 col-md-9 col-sm-10 col-10">
                                                      <label class="custom-toggle" for="ptl_pinterest"><input type="checkbox" <?php echo ($ptl_pinterest == 'on') ? 'checked' : ''; ?>  value="<?php echo esc_attr($ptl_pinterest); ?>" name="social-shares[ptl_pinterest]" id="ptl_pinterest" class="ptl-social-shares"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose which social media links to display on your timeline', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Post Per Page', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <input type="number" class="ptl-text-fields" name="ptl-post-per-page" id="ptl-post-per-page" value="<?php echo esc_attr($options['ptl-post-per-page']) ?>" placeholder="<?php echo esc_attr__('Numberic Value (Example: 10)', 'post-timeline') ?>">
                                             <div class="description"><?php echo esc_attr__('Choose how many posts to appear per page.', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Visibility', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge <?php echo ($options['ptl-post-icon'] == 'off') ? 'ptl-off' : '' ?>">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-set">
                                                <div class="field-title">
                                                   <?php echo esc_attr__('Post Icon', 'post-timeline') ?>
                                                </div>
                                                <div class="field-content">
                                                   <div class="row align-items-center">
                                                      <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                         <label class="custom-toggle" for="ptl-post-icon"><input type="checkbox" <?php echo ($options['ptl-post-icon'] == 'on') ? 'checked' : '' ?> value="<?php echo esc_attr($options['ptl-post-icon']); ?>" name="ptl-post-icon" id="ptl-post-icon"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                      </div>
                                                      <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                         <div class="label"><?php echo esc_attr__('Enable this option to show post icons', 'post-timeline'); ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge <?php echo ($options['ptl-images'] == 'off') ? 'ptl-off' : '' ?>">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-title">
                                                <?php echo esc_attr__('Display Post Images', 'post-timeline') ?>
                                             </div>
                                             <div class="field-content">
                                                <div class="row align-items-center">
                                                   <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                      <label class="custom-toggle" for="ptl-images"><input <?php echo ($options['ptl-images'] == 'on') ? 'checked' : '' ?> type="checkbox" value="<?php echo esc_attr($options['ptl-images']) ?>" name="ptl-images" id="ptl-images"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                   </div>
                                                   <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                      <div class="label"><?php echo esc_attr__('Use this option to hide/display post images', 'post-timeline'); ?></div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-set">
                                                <div class="field-title">
                                                   <?php echo esc_attr__('Hide Content', 'post-timeline') ?>
                                                </div>
                                                <div class="field-content">
                                                   <div class="row align-items-center">
                                                      <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                         <label class="custom-toggle" for="ptl-content-hide"><input type="checkbox" <?php echo ($options['ptl-content-hide'] == 'on') ? 'checked' : '' ?> value="<?php echo esc_attr($options['ptl-content-hide']) ?>" name="ptl-content-hide" id="ptl-content-hide"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                      </div>
                                                      <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                         <div class="label"><?php echo esc_attr__('Enable this option to disable post content', 'post-timeline'); ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Navigation', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-set">
                                                <div class="field-title">
                                                   <?php echo esc_attr__('Status', 'post-timeline') ?>
                                                </div>
                                                <div class="field-content">
                                                   <div class="row align-items-center">
                                                      <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                         <label class="custom-toggle" for="ptl-nav-status">
                                                            <input type="checkbox" <?php echo ($options['ptl-nav-status'] == 'on') ? 'checked' : '' ?> value="<?php echo esc_attr($options['ptl-nav-status']) ?>" name="ptl-nav-status" id="ptl-nav-status"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                      </div>
                                                      <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                         <div class="label"><?php echo esc_attr__('Enable/disable timeline navigation', 'post-timeline'); ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Navigation Type', 'post-timeline') ?><span class="ptl-go-pro"><a href="https://posttimeline.com/" target="__blank"><?php echo esc_attr__('Go Pro', 'post-timeline') ?></a></span>
                                          </div>
                                          <div class="field-content disabled">
                                             <div class="row">
                                                <div class="col-md-3 col-sm-3 col-6">
                                                   <div class="radio-button radio-images <?php echo ($options['ptl-nav-type'] == '0') ? 'active' : '' ?>">
                                                      <label for="style-0">
                                                         <input disabled="disabled" type="radio" <?php echo ($options['ptl-nav-type'] == '0') ? 'checked' : '' ?> name="ptl-nav-type" value="0" id="style-0" />
                                                         <img src="<?php echo POST_TIMELINE_URL_PATH . '/admin/images/layout/nav-0.png' ?>" alt="Style 0" title="Style 0" class="radio-image">
                                                      </label>
                                                   </div>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-6">
                                                   <div class="radio-button radio-images ptl-lock-box <?php echo ($options['ptl-nav-type'] == '1') ? 'active' : '' ?>">
                                                      <label for="style-1">
                                                         <input disabled="disabled" type="radio" <?php echo ($options['ptl-nav-type'] == '1') ? 'checked' : '' ?> name="ptl-nav-type" value="0" id="style-1" />
                                                         <img src="<?php echo POST_TIMELINE_URL_PATH . '/admin/images/layout/nav-1.png' ?>" alt="Style 1" title="Style 1" class="radio-image">
                                                      </label>
                                                      <div class="ptl-lock-inner">
                                                         <svg width="30" height="60" viewBox="0 0 90 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M82.5 52.5H75V30C75 13.5 61.5 0 45 0C28.5 0 15 13.5 15 30V52.5H7.5C3.75 52.5 0 56.25 0 60V112.5C0 116.25 3.75 120 7.5 120H82.5C86.25 120 90 116.25 90 112.5V60C90 56.25 86.25 52.5 82.5 52.5ZM52.5 105H37.5L40.5 88.5C36.75 87 33.75 82.5 33.75 78.75C33.75 72.75 39 67.5 45 67.5C51 67.5 56.25 72.75 56.25 78.75C56.25 83.25 54 87 49.5 88.5L52.5 105ZM60 52.5H30V30C30 21.75 36.75 15 45 15C53.25 15 60 21.75 60 30V52.5Z" fill="white" />
                                                         </svg>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-6">
                                                   <div class="radio-button radio-images ptl-lock-box <?php echo ($options['ptl-nav-type'] == '2') ? 'active' : '' ?>">
                                                      <label for="style-2">
                                                         <input disabled="disabled" type="radio" <?php echo ($options['ptl-nav-type'] == '2') ? 'checked' : '' ?> name="ptl-nav-type" value="0" id="style-2" />
                                                         <img src="<?php echo POST_TIMELINE_URL_PATH . '/admin/images/layout/nav-2.png' ?>" alt="Style 2" title="Style 2" class="radio-image">
                                                      </label>
                                                      <div class="ptl-lock-inner">
                                                         <svg width="30" height="60" viewBox="0 0 90 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M82.5 52.5H75V30C75 13.5 61.5 0 45 0C28.5 0 15 13.5 15 30V52.5H7.5C3.75 52.5 0 56.25 0 60V112.5C0 116.25 3.75 120 7.5 120H82.5C86.25 120 90 116.25 90 112.5V60C90 56.25 86.25 52.5 82.5 52.5ZM52.5 105H37.5L40.5 88.5C36.75 87 33.75 82.5 33.75 78.75C33.75 72.75 39 67.5 45 67.5C51 67.5 56.25 72.75 56.25 78.75C56.25 83.25 54 87 49.5 88.5L52.5 105ZM60 52.5H30V30C30 21.75 36.75 15 45 15C53.25 15 60 21.75 60 30V52.5Z" fill="white" />
                                                         </svg>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="col-md-3 col-sm-3 col-6">
                                                   <div class="radio-button radio-images ptl-lock-box <?php echo ($options['ptl-nav-type'] == '3') ? 'active' : '' ?>">
                                                      <label for="style-3">
                                                         <input disabled="disabled" type="radio" <?php echo ($options['ptl-nav-type'] == '3') ? 'checked' : '' ?> name="ptl-nav-type" value="0" id="style-3" />
                                                         <img src="<?php echo POST_TIMELINE_URL_PATH . '/admin/images/layout/nav-3.png' ?>" alt="Style 3" title="Style 3" class="radio-image">
                                                      </label>
                                                      <div class="ptl-lock-inner">
                                                         <svg width="30" height="60" viewBox="0 0 90 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M82.5 52.5H75V30C75 13.5 61.5 0 45 0C28.5 0 15 13.5 15 30V52.5H7.5C3.75 52.5 0 56.25 0 60V112.5C0 116.25 3.75 120 7.5 120H82.5C86.25 120 90 116.25 90 112.5V60C90 56.25 86.25 52.5 82.5 52.5ZM52.5 105H37.5L40.5 88.5C36.75 87 33.75 82.5 33.75 78.75C33.75 72.75 39 67.5 45 67.5C51 67.5 56.25 72.75 56.25 78.75C56.25 83.25 54 87 49.5 88.5L52.5 105ZM60 52.5H30V30C30 21.75 36.75 15 45 15C53.25 15 60 21.75 60 30V52.5Z" fill="white" />
                                                         </svg>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Select a timeline navigation style.', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Post Configuration', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Default Icon', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="row align-items-center">
                                             <div class="col-md-4 col-sm-6">
                                                <div class="custom-control custom-radio sel-icon-type">
                                                   <input name="ptl-icon-type" <?php echo ($options['ptl-icon-type'] == 'font-awesome') ? 'checked' : '' ?> class="custom-control-input" id="plt-fontaweome" type="radio" value="font-awesome">
                                                   <label class="custom-control-label" for="plt-fontaweome"><?php echo esc_attr__('Font Awesome', 'post-timeline'); ?></label>
                                                </div>
                                             </div>
                                             <div class="col-md-8 col-sm-6">
                                                <div class="custom-control custom-radio sel-icon-type">
                                                   <input name="ptl-icon-type" <?php echo ($options['ptl-icon-type'] == 'upload-icon') ? 'checked' : '' ?> class="custom-control-input" id="ptl-upload-icon" value="upload-icon" type="radio">
                                                   <label class="custom-control-label" for="ptl-upload-icon"><?php echo esc_attr__('Upload Icon', 'post-timeline'); ?></label>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="ptl-fav-icon <?php echo ($options['ptl-icon-type'] != 'font-awesome') ? 'hide' : '' ?>">
                                             <div class="d-flex">
                                                <input type="text" name="ptl-fav-icon" id="ptl-fav-icon" value="<?php echo esc_attr($options['ptl-fav-icon']) ?>">
                                                <button class="ptl-icon-clear" <?php echo (empty($options['ptl-fav-icon'])) ? 'style="display: none"' : 'style="display: block"' ?> type="button"><svg width="10" height="10" viewBox="0 0 12 12" xmlns="https://www.w3.org/2000/svg">
                                                      <path d="M.566 1.698L0 1.13 1.132 0l.565.566L6 4.868 10.302.566 10.868 0 12 1.132l-.566.565L7.132 6l4.302 4.3.566.568L10.868 12l-.565-.566L6 7.132l-4.3 4.302L1.13 12 0 10.868l.566-.565L4.868 6 .566 1.698z"></path>
                                                   </svg></button>
                                                <span class="input-group-addon"><i class="fa-solid fa-font-awesome"></i></span>
                                             </div>
                                          </div>
                                          <div class="ptl-custom-icon <?php echo ($options['ptl-icon-type'] != 'upload-icon') ? 'hide' : '' ?>">
                                             <div class="p-icon d-flex align-items-center">
                                                <label for="ptl-custom-icon" class="col-form-label form-control-label"><?php echo esc_attr__('Upload Custom Icon', 'post-timeline'); ?></label>
                                                <?php
                                                $icon_meta = 'ptl-default-custom-icon';
                                                $icon_img = (isset($options['ptl-default-custom-icon'])) ? $options['ptl-default-custom-icon'] : '';
                                                echo $this->ptl_upload_icon($icon_meta, $icon_img); ?>
                                             </div>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Select a source from where to add an icon', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Date Format', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select name="ptl-date-format" id="ptl-date-format" class="ptl-select custom-good-select">
                                                <option <?php echo ($options['ptl-date-format'] == '') ? 'selected' : '' ?> value="" selected="selected"><?php echo esc_attr__('Hide', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'M') ? 'selected' : '' ?> value="M" selected="selected"><?php echo esc_attr__('Mar', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'j M') ? 'selected' : '' ?> value="j M"><?php echo esc_attr__('23 Mar', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'M j, Y') ? 'selected' : '' ?> value="M j, Y"><?php echo esc_attr__('Mar 10, 2001', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'F j, Y') ? 'selected' : '' ?> value="F j, Y"><?php echo esc_attr__('March 10, 2001', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'j F, Y') ? 'selected' : '' ?> value="j F, Y"><?php echo esc_attr__('10 March, 2010', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'm/d/Y') ? 'selected' : '' ?> value="m/d/Y"><?php echo esc_attr__('03/10/2017', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'm-j-y') ? 'selected' : '' ?> value="m-j-y"><?php echo esc_attr__('16-05-18', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'j-m-y') ? 'selected' : '' ?> value="j-m-y"><?php echo esc_attr__('05-16-18', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'D M j G:i:s') ? 'selected' : '' ?> value="D M j G:i:s"><?php echo esc_attr__('Sat Mar 10 17:16:18', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-date-format'] == 'Y-m-d') ? 'selected' : '' ?> value="Y-m-d"><?php echo esc_attr__('2001-03-10', 'post-timeline') ?></option>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose the date format for your posts.', 'post-timeline'); ?> | <a target="_blank" href="<?php echo POST_TIMELINE_SITE ?>docs/date-formats/"><?php echo esc_attr__('Help', 'post-timeline'); ?></a></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Read More Link', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select id="ptl-post-link-type" name="ptl-post-link-type" class="custom-good-select">
                                                <option <?php echo ($options['ptl-post-link-type'] == 'readmore') ? 'selected' : '' ?> value="readmore"><?php echo esc_attr__('Read More button', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-link-type'] == 'title') ? 'selected' : '' ?> value="title"><?php echo esc_attr__('Title link', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-link-type'] == '') ? 'selected' : '' ?> value=""><?php echo esc_attr__('Disabled', 'post-timeline') ?></option>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Select how links are utilized on your timeline', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Post Content Length', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <input type="number" class="ptl-text-fields" name="ptl-post-length" id="ptl-post-length" value="<?php echo esc_attr($options['ptl-post-length']) ?>" placeholder="<?php echo esc_attr__('Numberic Value (Example: 24)', 'post-timeline') ?>">
                                          <div class="description"><?php echo esc_attr__('Length of post content by the character count', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Link In New Tab', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <label class="custom-toggle" for="ptl-target-blank">
                                                <input type="checkbox" <?php echo ($options['ptl-target-blank'] == 'on') ? 'checked' : ''; ?> value="" name="ptl-target-blank" id="ptl-target-blank"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                             <div class="description"><?php echo esc_attr__('Enable/disable open post in new page.', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Select Slide Type', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select id="ptl-slides-setting" name="ptl-slides-setting" class="custom-good-select">
                                                <option <?php echo ($options['ptl-slides-setting'] == 'carousel') ? 'selected' : '' ?> value="carousel"><?php echo esc_attr__('Carousel', 'post-timeline'); ?></option>
                                                <option <?php echo ($options['ptl-slides-setting'] == 'popup') ? 'selected' : '' ?> value="popup"><?php echo esc_attr__('Popup Slider', 'post-timeline'); ?></option>
                                             </select>
                                          </div>
                                          <div class="options-group"></div>
                                          <div class="description"><?php echo esc_attr__('Choose how your timeline gallery will appear', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Advanced', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Slug', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <input type="text" class="ptl-text-fields" name="ptl-slug" id="ptl-slug" value="<?php echo esc_attr($options['ptl-slug']) ?>" placeholder="<?php echo esc_attr__('post-timeline-slug', 'post-timeline') ?>">
                                          <div class="description"><?php echo esc_attr__('Enter the slug (website identifier) of your timeline’s page', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Single Page Template', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <?php  ?>
                                             <select id="ptl-single-temp" name="ptl-single-temp" class="custom-good-select">
                                                <?php
                                                foreach ($single_temps as $name => $dir) {
                                                   $selected = ($options['ptl-single-temp'] == $name) ? 'selected' : '';
                                                   echo "<option " . esc_attr($selected) . " value=" . esc_attr($name) . ">" . esc_attr__($name, 'post-timeline') . "</option>";
                                                }
                                                ?>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose the post timeline blog page template.', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12">

                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12">
                              </div>
                           </div>
                        </div>
                        <div class="tab-pane" id="tabs-styles-content" role="tabpanel" aria-labelledby="tabs-styles">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Typography', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Font Family', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <div class="field-content-inner ptl-search-active">
                                                <select id="ptl-fonts-title" name="ptl-fonts-title" class="custom-good-select">
                                                   <option value=""><?php echo esc_attr__('Default Font', 'post-timeline'); ?></option>
                                                </select>
                                             </div>
                                             <div class="options-group">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Choose a font-family for the timeline', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Font Size', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <div class="ptl-unit">
                                                <input type="number" class="ptl-text-fields" name="ptl-size-content" id="ptl-size-content" value="<?php echo esc_attr($options['ptl-size-content']) ?>" min="0" max="30" placeholder="<?php echo esc_attr__('14', 'post-timeline') ?>">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Select the content area font size of the timeline.', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Theme Color', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Skin (Light or Dark)', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="row">
                                             <div class="col-md-10">
                                                <div class="row">
                                                   <div class="col">
                                                      <div class="custom-control custom-radio">
                                                         <input type="radio" <?php echo ($options['ptl-skin-type'] == 'ptl-light') ? 'checked' : '' ?> class="custom-control-input" name="ptl-skin-type" value="ptl-light" id="ptl-light" />
                                                         <label class="custom-control-label" for="ptl-light"><?php echo esc_attr__('Light', 'post-timeline') ?></label>
                                                      </div>
                                                   </div>
                                                   <div class="col">
                                                      <div class="custom-control custom-radio">
                                                         <input type="radio" <?php echo ($options['ptl-skin-type'] == 'ptl-dark') ? 'checked' : '' ?> class="custom-control-input" name="ptl-skin-type" value="ptl-dark" id="ptl-dark" />
                                                         <label class="custom-control-label" for="ptl-dark"><?php echo esc_attr__('Dark', 'post-timeline') ?></label>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Select a skin “tone” for the timeline.', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Enable Container Background', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <label class="custom-toggle" for="ptl-bg-status">
                                                <input type="checkbox" <?php echo ($options['ptl-bg-status'] == 'on') ? 'checked' : ''; ?> value="" name="ptl-bg-status" id="ptl-bg-status"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                             <div class="description"><?php echo esc_attr__('Enable/disable container background color', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Container Background', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content ptl-colorpicker-box post_field">
                                             <div style="position: relative;">
                                                <input type="text" class="colorpicker_value" id="ptl-bg-color-text" value="<?php echo esc_attr($options['ptl-bg-color']) ?>" class="hexcolor" name="ptl-bg-color-text">
                                                <input type="color" class="colorpicker" id="ptl-bg-color" name="ptl-bg-color" value="<?php echo esc_attr($options['ptl-bg-color']) ?>">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Add a hex color code to the container background', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Default Post Background', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content ptl-colorpicker-box post_field">
                                             <div style="position: relative;">
                                                <input type="text" class="colorpicker_value" id="ptl-post-bg-color-text" class="hexcolor" name="ptl-post-bg-color-text" value="<?php echo esc_attr($options['ptl-post-bg-color']) ?>">
                                                <input type="color" class="colorpicker" id="ptl-post-bg-color" name="ptl-post-bg-color" value="<?php echo esc_attr($options['ptl-post-bg-color']) ?>">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Add a hex color code to the post background', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Line Color', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content ptl-colorpicker-box post_field">
                                             <div style="position: relative;" ptl-colorpicker-box>
                                                <input type="text" class="hexcolor colorpicker_value" id="ptl-tagline-color-text" name="ptl-tagline-color-text" value="<?php echo esc_attr($options['ptl-tagline-color']) ?>">
                                                <input type="color" class="colorpicker" id="ptl-tagline-color" name="ptl-tagline-color" value="<?php echo esc_attr($options['ptl-tagline-color']) ?>">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Add a hex color code to the base line', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Navigation Color', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content ptl-colorpicker-box post_field">
                                             <div style="position: relative;" ptl-colorpicker-box>
                                                <input type="text" class="hexcolor colorpicker_value" id="ptl-nav-color-text" name="ptl-nav-color-text" value="<?php echo esc_attr($options['ptl-nav-color']) ?>">
                                                <input type="color" class="colorpicker" id="ptl-nav-color" name="ptl-nav-color" value="<?php echo esc_attr($options['ptl-nav-color']) ?>">
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Add a hex color code to the navigation style', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Animation', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-set">
                                                <div class="field-title">
                                                   <?php echo esc_attr__('Enabled?', 'post-timeline') ?>
                                                </div>
                                                <div class="field-content">
                                                   <div class="row align-items-center">
                                                      <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                         <label class="custom-toggle" for="ptl-anim-status"><input type="checkbox" <?php echo ($options['ptl-anim-status'] == 'on') ? 'checked' : '' ?> value="<?php echo esc_attr($options['ptl-anim-status']) ?>" name="ptl-anim-status" id="ptl-anim-status"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                      </div>
                                                      <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                         <div class="label"><?php echo esc_attr__('Enable this option to be able to add icons', 'post-timeline'); ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Animation Type', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <div class="field-content-inner">
                                                <select name="ptl-anim-type" id="ptl-anim-type" class="ptl-select custom-good-select">
                                                   <?php
                                                   $animations = array(
                                                      'backIn_left-right'     => esc_attr__('BackIn Left/Right', 'post-timeline'),
                                                      'bounceIn'              => esc_attr__('BounceIn', 'post-timeline'),
                                                      'bounceInUp'            => esc_attr__('BounceInUp', 'post-timeline'),
                                                      'fadeIn'                => esc_attr__('FadeIn', 'post-timeline'),
                                                      'fadeUpLeft-Right'      => esc_attr__('FadeUp Left/Right', 'post-timeline'),
                                                      'fadeInDown'            => esc_attr__('FadeInDown', 'post-timeline'),
                                                      'fadeDownLeft-Right'    => esc_attr__('FadeDown Left/Right', 'post-timeline'),
                                                      'fadeIn_left-right'     => esc_attr__('FadeIn Left/Right', 'post-timeline'),
                                                      'flipInY'               => esc_attr__('Flip Y', 'post-timeline'),
                                                      'flipInX'               => esc_attr__('Flip X', 'post-timeline'),
                                                      'flip-left-right'       => esc_attr__('Flip Left/Right', 'post-timeline'),
                                                      'lightSpeed_left-right' => esc_attr__('lightSpeed Left/Right', 'post-timeline'),
                                                      'rotateIn'              => esc_attr__('RotateIn', 'post-timeline'),
                                                      'fadeInUp'              => esc_attr__('Slide Up', 'post-timeline'),
                                                      'zoomInUp'              => esc_attr__('ZoomInUp', 'post-timeline'),
                                                      'zoomInDown'            => esc_attr__('ZoomInDown', 'post-timeline'),
                                                      'zoomInLeft-Right'      => esc_attr__('ZoomIn Left/Right', 'post-timeline'),
                                                      'zoomOutLeft-Right'     => esc_attr__('ZoomOut Left/Right', 'post-timeline')
                                                   );

                                                   foreach ($animations as $animKey => $animVal) {
                                                      $selected = '';

                                                      if ($options['ptl-anim-type'] == $animKey) $selected = 'selected="selected"';

                                                      echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($animKey) . '">' . esc_attr($animVal) . '</option>';
                                                   }
                                                   ?>
                                                </select>
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Select a style of animation for the timeline', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Speed', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <input type="number" class="ptl-text-fields" name="ptl-anim-speed" id="ptl-anim-speed" value="<?php echo esc_attr($options['ptl-anim-speed']) ?>" min="0.1" step="0.1" max="2" placeholder="<?php echo esc_attr__('0.5', 'post-timeline') ?>">
                                             <div class="description"><?php echo esc_attr__('Select how fast or slow the animation “moves”', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Easing', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content">
                                             <div class="field-content-inner">
                                                <?php $easingarr = ['easeInQuad', 'easeOutQuad', 'easeInOutQuad', 'easeInCubic', 'easeOutCubic', 'easeInOutCubic', 'easeInQuart', 'easeOutQuart', 'easeInOutQuart', 'easeInQuint', 'easeOutQuint', 'easeInOutQuint'];
                                                ?>
                                                <select name="ptl-easing" id="ptl-easing" class="ptl-select custom-good-select">
                                                   <?php
                                                   foreach ($easingarr as $key => $easing) {
                                                      $selected = '';

                                                      if ($options['ptl-easing'] == $easing)  $selected = 'selected="selected"';

                                                      echo '<option ' . esc_attr($selected) . ' value="' . esc_attr($easing) . '">' . esc_attr($easing) . '</option>';
                                                   }
                                                   ?>
                                                </select>
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Select the animation easing of vertical timeline', 'post-timeline'); ?> | <a target="_blank" href="<?php echo POST_TIMELINE_SITE ?>docs/scroll-animation-easing/"><?php echo esc_attr__('Help', 'post-timeline'); ?></a></div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Others', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Line Style', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select name="ptl-line-style" id="ptl-line-style" class="ptl-select custom-good-select">
                                                <option <?php echo ($options['ptl-line-style'] == 'solid') ? 'selected' : '' ?> value="solid"><?php echo esc_attr__('Solid', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-line-style'] == 'dotted') ? 'selected' : '' ?> value="dotted"><?php echo esc_attr__('Dotted', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-line-style'] == 'dashed') ? 'selected' : '' ?> value="dashed"><?php echo esc_attr__('Dashed', 'post-timeline') ?></option>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Select the style of the main “backbone” line.', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Post Border Radius', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <div class="field-content-inner">
                                             <select id="ptl-post-radius" name="ptl-post-radius" class="custom-good-select">
                                                <option <?php echo ($options['ptl-post-radius'] == '') ? 'selected' : '' ?> value=""><?php echo esc_attr__('Default', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-radius'] == '1') ? 'selected' : '' ?> value="1"><?php echo esc_attr__('1px', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-radius'] == '2') ? 'selected' : '' ?> value="2"><?php echo esc_attr__('2px', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-radius'] == '3') ? 'selected' : '' ?> value="3"><?php echo esc_attr__('3px', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-radius'] == '4') ? 'selected' : '' ?> value="4"><?php echo esc_attr__('4px', 'post-timeline') ?></option>
                                                <option <?php echo ($options['ptl-post-radius'] == '5') ? 'selected' : '' ?> value="5"><?php echo esc_attr__('5px', 'post-timeline') ?></option>
                                             </select>
                                          </div>
                                          <div class="description"><?php echo esc_attr__('Choose the radius of the post corners', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-set">
                                          <div class="field-title">
                                             <?php echo esc_attr__('Select Loader', 'post-timeline') ?>
                                          </div>
                                          <div class="field-content position-relative">
                                             <div class="field-content-inner">
                                                <select name="ptl-loader" id="ptl-loader" class="ptl-select custom-good-select">
                                                   <option <?php echo ($options['ptl-loader'] == 'ptl-spinner-glow') ? 'selected' : '' ?> value="ptl-spinner-glow"><?php echo esc_attr__('Spinner Glow', 'post-timeline') ?></option>
                                                   <option <?php echo ($options['ptl-loader'] == 'ptl-spinner') ? 'selected' : '' ?> value="ptl-spinner"><?php echo esc_attr__('Spinner', 'post-timeline') ?></option>
                                                   <option <?php echo ($options['ptl-loader'] == 'ptl-spinner-bubble') ? 'selected' : '' ?> value="ptl-spinner-bubble"><?php echo esc_attr__('Spinner Bubble', 'post-timeline') ?></option>
                                                   <option <?php echo ($options['ptl-loader'] == 'ptl-loader-bubble') ? 'selected' : '' ?> value="ptl-loader-bubble"><?php echo esc_attr__('Loader Bubble', 'post-timeline') ?></option>
                                                </select>
                                             </div>
                                             <div class="loader-sec">
                                                <?php
                                                if ($options['ptl-loader']) {
                                                   echo '<span class="' . esc_attr($options['ptl-loader']) . '"></span>';
                                                }
                                                ?>
                                             </div>
                                             <div class="description"><?php echo esc_attr__('Choose the style of loader for the animations', 'post-timeline'); ?></div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="input-group input-group-merge d-none <?php echo ($options['ptl-lazy-load'] == 'off') ? 'ptl-off' : '' ?>">
                                       <div class="row justify-content-between">
                                          <div class="col-md-8 col-sm-8 col-12">
                                             <div class="field-set">
                                                <div class="field-title">
                                                   <?php echo esc_attr__('Lazy Load', 'post-timeline') ?>
                                                </div>
                                                <div class="field-content">
                                                   <div class="row align-items-center">
                                                      <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                                                         <label class="custom-toggle" for="ptl-lazy-load"><input type="checkbox" <?php echo ($options['ptl-lazy-load'] == 'on') ? 'checked' : '' ?> value="<?php echo esc_attr($options['ptl-lazy-load']) ?>" name="ptl-lazy-load" id="ptl-lazy-load"><span class="custom-toggle-slider rounded-circle" data-label-off="No" data-label-on="Yes"></span></label>
                                                      </div>
                                                      <div class="col-lg-10 col-md-9 col-sm-9 col-9">
                                                         <div class="label"><?php echo esc_attr__('Enable or disable images lazy-load', 'post-timeline'); ?></div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-12">

                              </div>
                           </div>
                        </div>
                        <div class="tab-pane" id="tabs-custom-content" role="tabpanel" aria-labelledby="tabs-custom">
                           <div class="row">
                              <div class="col-md-12">
                                 <div class="ptl-setting-section">
                                    <div class="group-title">
                                       <h4><?php echo esc_attr__('Custom', 'post-timeline'); ?></h4>
                                    </div>
                                    <div class="input-group input-group-merge">
                                       <div class="field-title">
                                          <?php echo esc_attr__('Custom CSS', 'post-timeline') ?>
                                       </div>
                                       <div class="field-content">
                                          <textarea name="ptl-custom-css" id="ptl-custom-css"><?php echo esc_textarea($options['ptl-custom-css']) ?></textarea>
                                          <div class="description"><?php echo esc_attr__('Add your custom CSS to add on the timeline page', 'post-timeline'); ?></div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col-md-12">
                           <button type="button" class="btn btn-primary float-right" data-loading-text="<?php echo esc_attr__('Saving...', 'asl_wc') ?>" data-completed-text="Settings Updated" id="ptl_setting"><?php echo esc_attr__('Save Settings', 'asl_wc') ?></button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- ptl-cont -->