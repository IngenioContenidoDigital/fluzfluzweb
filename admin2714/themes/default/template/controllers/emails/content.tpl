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

{if isset($content)}
	{$content}
        <div id="formAddPaymentPanel" class="panel">
            <div class="panel-heading">
                    <i class="icon-envelope"></i>
                    {l s="Email Enviado (muestra la informacion del email. Solo texto plano no editable)"}
            </div>
            <div class="t-email">
                {l s='Plantilla Email: '}{$template}
            </div>
            {foreach from=$vars item=var}
                <div class="row">
                    {$var}
                </div>
            {/foreach}
        </div>
{/if}
{literal}

    <style>
        .t-email{font-weight: bold; color: #000; text-transform: uppercase; margin-bottom: 20px;}
    </style>
    
{/literal}