<?php
error_reporting(0);

class shortcodesController extends FrontController {

    public function initContent() {
        parent::initContent();
        
        $this->setTemplate(_PS_THEME_DIR_.'short_codes.tpl');
    }
}
