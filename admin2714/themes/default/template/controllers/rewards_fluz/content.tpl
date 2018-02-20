{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $content}
	{$content}
{else}
	<iframe class="clearfix" style="margin:0px;padding:0px;width:100%;height:920px;overflow:hidden;border:none" src="//addons.prestashop.com/iframe/search.php?isoLang={$iso_lang}&amp;isoCurrency={$iso_currency}&amp;isoCountry={$iso_country}&amp;parentUrl={$parent_domain}"></iframe>
{/if}
<div class="panel" style='height: 450px !important; overflow-y: auto !important;'>
        <div class="panel-heading">
                <i class="icon-group"></i>
                {l s='List Fluz Fluz Rewards'}
                <span class="badge">{count($reward_fluz)}</span>
        </div>
        {if count($reward_fluz)}
                    <table class="table">
                            <thead>
                            <tr>
                                    <th><span class="title_box">{l s='Id reward'}</span></th>
                                    <th><span class="title_box">{l s='credits (Fluz)'}</span></th>
                                    <th><span class="title_box">{l s='Estado'}</span></th>
                                    <th><span class="title_box">{l s='Id empleado'}</span></th>
                                    <th><span class="title_box">{l s='Nombre'}</span></th>
                                    <th><span class="title_box">{l s='Desde'}</span></th>
                                    <th><span class="title_box">{l s='Hasta'}</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $reward_fluz as $reward}
                                <tr class="table_reward">
                                    <input type="hidden" class="reward_fluz" id="reward_fluz-{$reward['id_rewards_distribute']}" value="{$reward['active']}">
                                    <td>{$reward['id_rewards_distribute']}</td>
                                    <td>{$reward['credits']}</td>
                                    {if $reward['active'] == 0}
                                    <td>{l s='Desactivado'}</td>
                                    {else}
                                    <td>{l s='Activado'}</td>    
                                    {/if}
                                    <td>{$reward['id_employee']}</td>
                                    <td>{$reward['name']}</td>
                                    <td>{{dateFormat date=$reward['date_from'] full=0}}</td>
                                    <td>{{dateFormat date=$reward['date_to'] full=0}}</td>
                                    <td>
                                        <select class="state_reward" id="state_reward-{$reward['id_rewards_distribute']}" name="state_reward" onchange="changeFunc({$reward['id_rewards_distribute']});">
                                            <option>Modificar Estado</option>
                                            <option value="0">Desactivar</option>
                                            <option value="1">Activar</option>
                                            <option value="2">Eliminar</option>
                                        </select>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                    </table>
        {/if}
</div>
{literal}
    <script>
        function changeFunc(id) {
            
            var state_value = $('#state_reward-'+id).val();
            
            $.ajax({
                    type : 'POST',
                    data:'action=modifyState&id_reward='+id+'&state_value='+state_value,
                    success: function(response){
                    if (response)
                    {
                        location.reload();
                    }
                }
            });
        }
    </script>
{/literal}