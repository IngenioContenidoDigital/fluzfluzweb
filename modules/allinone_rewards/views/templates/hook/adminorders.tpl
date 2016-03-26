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
	<div class="{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}col-lg-5{else}clear{/if}" id="adminorders">
{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
		<div class="panel" style="overflow: auto">
			<div class="panel-heading">{l s='Loyalty reward for this order' mod='allinone_rewards'}</div>
{else}
			<br>
			<fieldset>
				<legend>{l s='Loyalty reward for this order' mod='allinone_rewards'}</legend>
{/if}
				<div style="width: 50%; float: left"><span style="font-weight: bold">{l s='Reward :' mod='allinone_rewards'}</span> {displayPrice price=$reward->credits}</div>
				<div style="width: 50%; float: left"><span style="font-weight: bold">{l s='Status :' mod='allinone_rewards'}</span> {$reward_state|escape:'html':'UTF-8'}</div>
{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
		</div>
{else}
			</fieldset>
{/if}
	</div>
<!-- END : MODULE allinone_rewards -->