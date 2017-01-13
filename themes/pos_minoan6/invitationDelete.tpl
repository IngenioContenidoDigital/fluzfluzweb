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

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="form-cancel">
    <div><img style="margin-left: -10px;" src="{$img_dir}alert_icon.png" /></div>
    <p class="title-cancel">Este enlace ya no est&aacute; disponible</p>
    <div><div class="border-red"></div></div>
    <p class="texto-cancel">Lo sentimos, pero tu invitaci&oacute;n ha expirado o ha sido cancelada. Nos encantar&iacute;a que te unieras a nuestra red, sin embargo, necesitaras ser invitado de nuevo para unirte.</p>
</div>

{literal}
    <style>
        #left_column{display: none;}
        .col-sm-3{display:none;}
    </style>
{/literal}