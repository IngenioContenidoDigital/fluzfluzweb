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

<div class="tab-pane active" id="status">

<div>
    <h4>{l s='Estado de la Redencion'} <span class="badge">({$history|@count})</span></h4>
    
    {foreach from=$datos item=cashout}
            {$cashout.nombre}
            {$cashout.apellido}
            {$cashout.numero_tarjeta}
    {/foreach}
</div>    
<!-- Change status form -->
<form action="" method="post" class="form-horizontal well hidden-print">
        <div class="row">
                <div class="col-lg-9">
                        <select id="id_status" class="chosen form-control" name="id_status">
                        {foreach from=$state item=status}
                            <option value="{$status['id_status']|intval}"{if isset($currentState) && $status['id_status'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$status['name']|escape}</option>
                        {/foreach}
                        </select>
                        <input type="hidden" name="id_order" value="{$order->id}" />
                </div>
                <div class="col-lg-3">
                        <button type="submit" name="submitState" class="btn btn-primary">
                                {l s='Update status'}
                        </button>
                </div>
        </div>
</form>
</div>