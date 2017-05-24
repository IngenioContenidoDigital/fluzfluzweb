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
{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
    {if $grupo == 4}
    <li><a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}"><img src="{$img_dir}icon/user-add.png" class="imgSponsor" /><span class="spanSponsor">{l s='Mis Fluzzers directos' mod='allinone_rewards'}</span></a></li>
    {/if}
{else}
    {if $grupo == 4}
<li><a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/sponsorship.gif" alt="{l s='Sponsorship program' mod='allinone_rewards'}" class="icon" /></a> <a href="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" title="{l s='Sponsorship program' mod='allinone_rewards'}">{l s='Sponsorship program' mod='allinone_rewards'}</a></li>
    {/if}
{/if}
<!-- END : MODULE allinone_rewards -->