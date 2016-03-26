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
<div class="panel">
	{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
	<h4>{l s='Loyalty rewards' mod='allinone_rewards'}</h4>
	{else}
	<h3>{l s='Loyalty rewards' mod='allinone_rewards'}</h3>
	{/if}
	<table class="table" id="reward_product_list" {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}style="width: 100%"{/if}>
		<thead>
			<tr>
				<th>{l s='Reward value' mod='allinone_rewards'}</th>
				<th>{l s='Reward date from' mod='allinone_rewards'}</th>
				<th>{l s='Reward date to' mod='allinone_rewards'}</th>
				<th>{l s='Action' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
{if $product_rewards|@count > 0}
	{foreach from=$product_rewards item=product_reward name=myLoop}
			<tr id="{$product_reward.id_reward_product|intval}">
				<td><span class="reward_value">{$product_reward.value|floatval}</span> <span class="reward_type">{if $product_reward.type==0}%{else}{$currency->sign|escape:'html':'UTF-8'}{/if}</span></td>
				<td class="reward_from">{if $product_reward.date_from != 0}{$product_reward.date_from|escape:'html':'UTF-8'}{/if}</td>
				<td class="reward_to">{if $product_reward.date_to != 0}{$product_reward.date_to|escape:'html':'UTF-8'}{/if}</td>
				<td>
					<img style="cursor: pointer" class="edit_reward" src="../img/admin/edit.gif">
					<img style="cursor: pointer" class="delete_reward" src="../img/admin/delete.gif">
				</td>
			</tr>
	{/foreach}
{/if}
			<tr id="0" style="display: none">
				<td colspan="4" align="center">{l s='No reward is defined for that product' mod='allinone_rewards'}</td>
			</tr>
		</tbody>
	</table>
	<div class="panel" style="margin-top: 50px">
		{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
		<h4 id="new_reward">{l s='New reward' mod='allinone_rewards'}</h4>
		<h4 id="update_reward" style="display: none">{l s='Update reward' mod='allinone_rewards'}</h4>
		{else}
		<h3 id="new_reward">{l s='New reward' mod='allinone_rewards'}</h3>
		<h3 id="update_reward" style="display: none">{l s='Update reward' mod='allinone_rewards'}</h3>
		{/if}
		<div class="form-group">
			<label class="control-label col-lg-2">{l s='Value' mod='allinone_rewards'}</label>
			<div class="input-group {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}margin-form{/if}">
				<input type="hidden" name="reward_product_id" id="reward_product_id">
				<input type="text" name="reward_product_value" id="reward_product_value" style="width: 80px; margin-right: 5px">
				<select id="reward_product_type" name="reward_product_type" style="width: 160px">
					<option value="0">% {l s='of its own price' mod='allinone_rewards'}</option>
					<option value="1">{$currency->sign|escape:'html':'UTF-8'}</option>
				</select>
				&nbsp;<span id="virtualvalue"></span>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-2">{l s='Dates' mod='allinone_rewards'}</label>
			<div class="input-group col-lg-10 {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}margin-form{/if}">
				<div {if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}class="row"{/if}>
					<div class="col-lg-5" {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}style="float: left; padding-right: 5px"{/if}>
						<div class="input-group">
							<span class="input-group-addon">{l s='from' mod='allinone_rewards'}</span>
							<input type="text" id="reward_product_from" style="text-align: center" name="reward_product_from" class="datetimepicker">
							<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
					<div class="col-lg-5">
						<div class="input-group">
							<span class="input-group-addon">{l s='to' mod='allinone_rewards'}</span>
							<input type="text" id="reward_product_to" style="text-align: center" name="reward_product_to" class="datetimepicker">
							<span class="input-group-addon"><i class="icon-calendar-empty"></i></span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer {if version_compare($smarty.const._PS_VERSION_,'1.6','<')}margin-form{/if}">
			<button class="button btn btn-default" id="cancelRewardProduct" type="button"><i class="process-icon-save"></i> {l s='Cancel' mod='allinone_rewards'}</button>
			<button class="button btn btn-default pull-right" id="submitRewardProduct" type="button"><i class="process-icon-save"></i> {l s='Save' mod='allinone_rewards'}</button>
		</div>
	</div>
	<script>
		var product_rewards_url = "{$product_rewards_url|escape:'javascript':'UTF-8'}";
		var delete_reward_label = "{l s='Do you really want to delete this reward ?' mod='allinone_rewards'}";
		var delete_reward_title = "{l s='Delete reward' mod='allinone_rewards'}";
		var currency_sign = '{$currency->sign|escape:'html':'UTF-8'}';

		var virtual_value = {$virtual_value|floatval};
		var virtual_name = "{$virtual_name|escape:'html':'UTF-8'}";

		manageEmptyRow();

		$('.datetimepicker').datetimepicker({
			prevText: '',
			nextText: '',
			dateFormat: 'yy-mm-dd',
			// Define a custom regional settings in order to use PrestaShop translation tools
			currentText: '{l s='Now' mod='allinone_rewards'}',
			closeText: '{l s='Done' mod='allinone_rewards'}',
			ampm: false,
			amNames: ['AM', 'A'],
			pmNames: ['PM', 'P'],
			timeFormat: 'hh:mm:ss tt',
			timeSuffix: '',
			timeOnlyTitle: "{l s='Choose Time' mod='allinone_rewards'}",
			timeText: '{l s='Time' mod='allinone_rewards'}',
			hourText: '{l s='Hour' mod='allinone_rewards'}',
			minuteText: '{l s='Minute' mod='allinone_rewards'}',
			secondText: '{l s='Second' mod='allinone_rewards'}',
			showSecond: true
		});
	</script>
</div>
<!-- END : MODULE allinone_rewards -->