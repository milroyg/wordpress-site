<?php
/* 
*      Robo Gallery     
*      Version: 5.0.2 - 91160
*      By Robosoft
*
*      Contact: https://robogallery.co/ 
*      Created: 2025
*      Licensed under the GPLv3 license - http://www.gnu.org/licenses/gpl-3.0.html
 */

if (!defined('WPINC')) {
    exit;
}

class roboGalleryModuleAssetsSimple extends roboGalleryModuleAssets
{

    protected function initJsFilesListAlt()
    {
        $this->initJsFilesList();
    }

    protected function initJsFilesList()
    {
        $this->files['js']['robo-gallery-simple-script'] = array(
            'url'    => $this->moduleUrl . 'assets/script.simple.js',
            'depend' => array(),
        );
    }

    protected function initCssFilesList()
    {
     //    $this->files['css']['robo-gallery-simple'] = array(
      //       'url'    => $this->moduleUrl . 'assets/simple.css',
       //      'depend' => array(),
       //  );
    }

}
