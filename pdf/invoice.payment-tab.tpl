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
{*
<table id="payment-tab" width="100%">
	<tr>
		<td class="payment center small grey bold" width="44%">{l s='Payment Method' pdf='true'}</td>
		<td class="payment left white" width="56%">
			<table width="100%" border="0">
				{foreach from=$order_invoice->getOrderPaymentCollection() item=payment}
					<tr>
						<td class="right small">{$payment->payment_method}</td>
						<td class="right small">{displayPrice currency=$payment->id_currency price=$payment->amount}</td>
					</tr>
				{/foreach}
			</table>
		</td>
	</tr>
</table>
*}

<table style="width: 100%; color: #949496; font-size: 10pt;" cellpadding="4" cellspacing="4">
    <tr style="font-weight: bold;"><td colspan="2" style="font-size: 10pt;">INFORMACION DE PAGO</td></tr>
    {if $payment == "Tarjeta_credito"}
        <tr><td style="font-size: 11pt;">Debito</td><td style="font-size: 10pt;"><span style="color: #E15243;">X </span>Credito</td></tr>
    {else}
        <tr><td style="font-size: 11pt;"><span style="color: #E15243;">X </span>Debito</td><td style="font-size: 10pt;">Credito</td></tr>
    {/if}
    {*<tr><td colspan="2" style="font-size: 11pt;">Numero de Tarjeta *** *** *** <span style="background-color: #EFEFEF; font-size: 10pt; line-height: 1.5pt;"> 0000 </span></td></tr>*}
</table>