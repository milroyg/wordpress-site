<?php

if (!class_exists('ACHBAdminMenu')) {
  class TLGBAdminMenu
  {
    function __construct()
    {
      add_action('admin_menu', [$this, 'adminMenu']);
      add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts']);
    }

    function adminMenu()
    {
      add_submenu_page(
        'tools.php',
        'Timeline Block',
        'Timeline Block',
        'manage_options',
        'timeline-block',
        [$this, 'renderPage'],
        100
      );
    }

    function renderTemplate($content)
    {
      $parseBlocks = parse_blocks($content);
      return render_block($parseBlocks[0]);
    }

    function renderPage()
    {

      ?>
      <div id="bplAdminHelpPage" data-version="<?php echo esc_attr(TLGB_VERSION); ?>"  data-is-premium='<?php echo esc_attr(tlgbIsPremium()); ?>'>
       
      </div>
      <?php
    }

    function adminEnqueueScripts($hook)
    {
      if ('tools_page_timeline-block' === $hook) {
        wp_enqueue_style('tlgb-admin-help', TLGB_DIR_URL . 'build/admin-help.css', [], TLGB_VERSION);
        wp_enqueue_script('tlgb-admin-help', TLGB_DIR_URL . 'build/admin-help.js', ['react', 'react-dom'], TLGB_VERSION, true);
        wp_enqueue_script('tlgb-fs-file', TLGB_DIR_URL . 'assets/js/fs.js');
        wp_set_script_translations('tlgb-admin-help', 'timeline-block', TLGB_DIR_PATH . 'languages');
      }
    }
  }
  new TLGBAdminMenu();
}