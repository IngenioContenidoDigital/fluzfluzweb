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
{if $page_name!='module-allinone_rewards-sponsorship'}
<script>
	var url_allinone_sponsorship="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'javascript':'UTF-8'}";
</script>
<div id="sponsorship_popup" class="{if $scheduled}scheduled{/if}" style="display: none"></div>
{/if}
<!-- END : MODULE allinone_rewards -->