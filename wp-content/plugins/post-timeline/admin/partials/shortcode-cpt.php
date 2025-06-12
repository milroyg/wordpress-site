<div class="row">
   <div class="col-lg-6 col-md-12">
      <div class="form-group">
         <label class="form-control-label" for="ptl-post-type"><?php echo esc_attr__('Select Post Type', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select class="custom-select custom-good-select input" id="ptl-post-type" name="post-type">
               <?php
               foreach ($posttypes_array as $key => $posttypes) {
                  echo '<option value="' . esc_attr($posttypes) . '">' . esc_attr($posttypes) . '</option>';
               }
               ?>
            </select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Choose what type of post to include in the timeline.', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group">
         <label class="form-control-label" for="ptl-category"><?php echo esc_attr__('Select Category', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select class="custom-select custom-good-select input" id="ptl-category" name="category" multiple="multiple"></select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Choose the type of Category for the timeline.', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group">
         <label class="form-control-label" for="ptl-filter-ids"><?php echo esc_attr__('Filter By IDs', 'post-timeline'); ?></label>
         <input type="text" class="form-control input" placeholder="example: 10,13,2,4" name="filter-ids" id="ptl-filter-ids">
         <div class="description">
            <?php echo esc_attr__('Select which IDs to include', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group">
         <label class="form-control-label" for="ptl-sort"><?php echo esc_attr__('Sorting Order', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select name="sort" class="custom-select custom-good-select input" id="ptl-sort">
               <option value="DESC"><?php echo esc_attr__('Descending', 'post-timeline') ?></option>
               <option value="ASC"><?php echo esc_attr__('Ascending', 'post-timeline') ?></option>
            </select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Arrange your timeline based on sorting sequence.', 'post-timeline'); ?>
         </div>
      </div>
   </div>
   <div class="col-lg-6 col-md-12">
      <div class="form-group">
         <?php
         $taxonomies = get_object_taxonomies('post', 'objects');
         ?>
         <label class="form-control-label" for="ptl-taxonomy"><?php echo esc_attr__('Select Taxonomy', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select class="custom-select custom-good-select input" id="ptl-taxonomy" name="taxonomy">
               <option value=""><?php echo esc_attr__('Select Taxonomy', 'post-timeline'); ?></option>
               <?php
               foreach ($taxonomies as $taxonomy) {
                  if ($taxonomy->name == 'post_tag' || $taxonomy->name ==  'post_format') continue;

                  echo '<option value="' . esc_attr($taxonomy->name) . '">' . esc_attr($taxonomy->name) . '</option>';
               }
               ?>
            </select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Choose by which criteria the content is categorized.', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group">
         <label class="form-control-label" for="ptl-exclude-ids"><?php echo esc_attr__('Exclude IDs', 'post-timeline'); ?></label>
         <input type="text" class="form-control input" placeholder="example: 11,12,5,7" name="exclude-ids" id="ptl-exclude-ids">
         <div class="description">
            <?php echo esc_attr__('Choose which IDs to exclude', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group" style="padding-top: 17px;">
         <label class="form-control-label" for="ptl-type"><?php echo esc_attr__('Select Tag Type', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select name="type" class="custom-select custom-good-select input" id="ptl-type">
               <option value="date"><?php echo esc_attr__('Date Based', 'post-timeline') ?></option>
               <option value="tag"><?php echo esc_attr__('Tag Based', 'post-timeline') ?></option>
               <option value="none"><?php echo esc_attr__('None', 'post-timeline') ?></option>
            </select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Choose Your Tag Type From Date and Tag.', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group">
         <label class="form-control-label" for="ptl-sort-by"><?php echo esc_attr__('Sort By', 'post-timeline'); ?></label>
         <div class="field-group-inner">
            <select name="sort-by" class="custom-select custom-good-select input" id="ptl-sort-by">
               <option value="title"><?php echo esc_attr__('Title', 'post-timeline') ?></option>
               <option value="id"><?php echo esc_attr__('ID', 'post-timeline') ?></option>
               <option selected value="publish_date"><?php echo esc_attr__('Date', 'post-timeline') ?></option>
               <option value="rand"><?php echo esc_attr__('Random', 'post-timeline') ?></option>
               <option value="modified"><?php echo esc_attr__('Modified', 'post-timeline') ?></option>
               <option value="menu_order"><?php echo esc_attr__('Menu Order', 'post-timeline') ?></option>
               <option value="meta"><?php echo esc_attr__('Custom Meta', 'post-timeline') ?></option>
            </select>
         </div>
         <div class="description">
            <?php echo esc_attr__('Arrange your timeline based on sorting sequence.', 'post-timeline'); ?>
         </div>
      </div>
      <div class="form-group ptl-sorting-meta d-none">
         <label class="form-control-label" for="ptl-sort-meta"><?php echo esc_attr__('Sort meta', 'post-timeline'); ?></label>
         <input type="text" class="form-control input" placeholder="meta_key" name="sort-meta" id="ptl-sort-meta">
         <div class="description">
            <?php echo esc_attr__('Arrange your timeline based on sorting sequence.', 'post-timeline'); ?>
         </div>
      </div>
   </div>
</div>