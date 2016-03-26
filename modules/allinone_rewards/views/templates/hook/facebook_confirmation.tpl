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
<script type="text/javascript">
//<![CDATA[
	var url_allinone_facebook="{$link->getModuleLink('allinone_rewards', 'facebook', [], true)|escape:'javascript':'UTF-8'}";
//]]>
</script>
<div id="rewards_facebook_confirm">
	{$facebook_confirm_txt|escape:'string':'UTF-8'}
	{if $facebook_code}
	<center>{l s='Code :' mod='allinone_rewards'} <span id="rewards_facebook_code"></span></center>
	{/if}
</div>
<!-- END : MODULE allinone_rewards -->