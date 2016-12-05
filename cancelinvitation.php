<?php

require(dirname(__FILE__).'/config/config.inc.php');
    Tools::displayFileAsDeprecated();
    Tools::redirect('index.php?controller=cancelinvitation', __PS_BASE_URI__, null, 'HTTP/1.1 301 Moved Permanently');