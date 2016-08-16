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
<table style="width: 100%;">
	<tr>
		<td style="text-align: center; font-size: 6pt; color: #444;  width:87%;">
			{if $available_in_your_account}
				{l s='An electronic version of this invoice is available in your account. To access it, log in to our website using your e-mail address and password (which you created when placing your first order).' pdf='true'}
				<br />
			{/if}
			{$shop_address|escape:'html':'UTF-8'}<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{l s='For more assistance, contact Support:' pdf='true'}<br />
				{if !empty($shop_phone)}
					{l s='Tel: %s' sprintf=[$shop_phone|escape:'html':'UTF-8'] pdf='true'}
				{/if}

				{if !empty($shop_fax)}
					{l s='Fax: %s' sprintf=[$shop_fax|escape:'html':'UTF-8'] pdf='true'}
				{/if}
				<br />
			{/if}
			
			{if isset($shop_details)}
				{$shop_details|escape:'html':'UTF-8'}<br />
			{/if}

			{if isset($free_text)}
				{$free_text|escape:'html':'UTF-8'}<br />
			{/if}
		</td>
		<td style="text-align: right; font-size: 8pt; color: #444;  width:13%;">
            {literal}{:pnp:} / {:ptp:}{/literal}
        </td>
	</tr>
</table>
*}

<table style="width: 200%; color: #949496; font-size: 10pt;">
    <tr>
        <td style="width: 60%; font-size: 25pt; color: #FDBB1D; letter-spacing: 1px;">Preguntas?</td>
    </tr>
    <tr>
        <td style="width: 60%; font-size: 10pt;">Escribenos a <span style="color: #E77569;">info@fluzfluz.com</span></td>
    </tr>
    <tr><td></td></tr>
    <tr>
        <td style="width: 60%; font-size: 9pt;">No somos grandes contribuyentes, somos intermediarios, Acogidos a la Ley 1429 de 2010 <span style="color: #FDBB1D; font-size: 12pt;">&curren;</span></td>
    </tr>
    <tr><td style="width: 60%; font-size: 8pt; line-height: 1.5pt;">Actividad Economica  CIIU 4791 4  Tarifa 11.04 x 1000</td></tr>
</table>
