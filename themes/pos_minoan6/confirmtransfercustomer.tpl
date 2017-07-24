{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
* @license   Commercial license see license.txt
* Support by mail  : contact@prestaplugins.com
* Support on forum : Patanock
* Support on Skype : Patanock13
*}
<!-- MODULE allinone_rewards -->

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="form-confirm-business">
    <div><img style="margin-left: -10px;" src="{$img_dir}checked.png" /></div>
    <p class="title-cancel">&Eacute;xito!</p>
    <div><div class="border-red"></div></div>
    <p class="texto-confirm-business">Transferencia de Usuarios Empresariales ha sido realizada con &eacute;xito.</p>
    <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
    <a class="btn btn-default btn-back-business" href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}"><span>{l s='regresar al panel principal'}</span></a>
</div>

{literal}
    <style>
        #left_column{display: none;}
        #footer, #launcher, #right_column, .breadcrumb { display: none!important; }
    </style>
{/literal}
{*if $popup}
    {literal}   
        <style>
            #header, #footer, #launcher, #right_column, .breadcrumb { display: none!important; }
            .button.button-small{display: none;}
            .form-cancel {padding-bottom: 0;min-height: auto;margin-top: 0px;}
        </style> 
        <script>
            setTimeout( function(){ 
                $.fancybox.close();
                window.top.location.reload();
            }  , 1000 );
        </script>
    {/literal}
{/if*}
