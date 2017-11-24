{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<button class="myfancybox btn btn-default btn-account" href="#validate_navigator" name="click_navigator" id="click_navigator" style="display:none;"></button>
<div style="display:none;">
    <div id="validate_navigator" class="myfancybox">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <br>
            <img class="logo img-responsive" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz" width="356" height="94" style="margin:0 auto;">
            <p class="p-advert-ie"> Estas Navegando en Internet Explorer. Para un mejor funcionamiento de la web te recomendamos cambiar de navegador.</p>
        </div>
    </div>
    <div class="col-lg-12 img-navigator">
        <a id="redirect_navigator_google">
            <img class="logo" src="{$img_dir}login/firefox.jpg" width="40" height="40" style="cursor:pointer;">
        </a>
        <a id="redirect_navigator_firefox">
            <img class="logo" src="{$img_dir}login/chrome.png" width="40" height="40" style="cursor:pointer;">
        </a>
    </div>
</div>    
{literal}
    <script>
        var es_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;
        var es_firefox = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;
        var es_safari = navigator.userAgent.toLowerCase().indexOf('safari') > -1;
        var es_opera = navigator.userAgent.toLowerCase().indexOf('opera');
        var es_ie = navigator.userAgent.indexOf("MSIE") > -1 ;

        $(document).ready(function() {
            
            if(!es_chrome && !es_firefox && !es_safari && !es_opera)
            {
                $('#click_navigator').get(0).click();
            } 
            
            if(es_ie)
            {
                $('#click_navigator').get(0).click();
            }    
            
        });
        
    </script>
{/literal}
{if isset($HOOK_HOME_TAB_CONTENT) && $HOOK_HOME_TAB_CONTENT|trim}
    {if isset($HOOK_HOME_TAB) && $HOOK_HOME_TAB|trim}
        <ul id="home-page-tabs" class="nav nav-tabs clearfix">
			{$HOOK_HOME_TAB}
		</ul>
	{/if}
	<div class="tab-content">{$HOOK_HOME_TAB_CONTENT}</div>
{/if}
{if isset($HOOK_HOME) && $HOOK_HOME|trim}
	<div class="clearfix">{$HOOK_HOME}</div>
{/if}