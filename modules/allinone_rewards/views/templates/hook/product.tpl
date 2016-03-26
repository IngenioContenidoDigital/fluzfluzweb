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
{if !isset($ajax_loyalty)}
<!-- MODULE allinone_rewards -->
<script type="text/javascript">
//<![CDATA[
	var url_allinone_loyalty="{$link->getModuleLink('allinone_rewards', 'loyalty', [], true)|escape:'javascript':'UTF-8'}";
//]]>
</script>
<p id="loyalty" class="align_justify"></p>
<br class="clear" />
<!-- END : MODULE allinone_rewards -->
{else}
	{if $display_credits}
		{l s='Buying this product you will collect ' mod='allinone_rewards'} <b><span id="loyalty_credits">{$credits|escape:'htmlall':'UTF-8'}</span></b> {l s=' with our loyalty program.' mod='allinone_rewards'}
		{l s='Your cart will total' mod='allinone_rewards'} <b><span id="total_loyalty_credits">{$total_credits|escape:'htmlall':'UTF-8'}</span></b> {l s='that can be converted into a voucher for a future purchase.' mod='allinone_rewards'}
	{else}
		{if isset($no_pts_discounted) && $no_pts_discounted == 1}
			{l s='No reward credits for this product because there\'s already a discount.' mod='allinone_rewards'}
		{else}
			{l s='Your basket must contain at least %s of products in order to get loyalty rewards.' sprintf={displayPrice price=$minimum|floatval} mod='allinone_rewards'}
		{/if}
	{/if}
{/if}