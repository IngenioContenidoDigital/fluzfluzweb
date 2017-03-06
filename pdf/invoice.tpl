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

{$style_tab}

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<!-- Invoicing -->
	<tr>
            <td colspan="12">
                {$addresses_tab}
            </td>
	</tr>

	<!-- Product -->
	<tr>
            <td colspan="12">
                {$product_tab}
            </td>
	</tr>

	<tr>
            <td colspan="12" height="10">&nbsp;</td>
	</tr>

        <!-- Payment - Total -->
	<tr>
            <td colspan="6" class="left">
                {$payment_tab}
            </td>
            <td colspan="6" rowspan="5" class="right">
                {$total_tab}
            </td>
	</tr>

        <tr>
            <td colspan="12" height="10">&nbsp;</td>
	</tr>

        <!-- Observations -->
	<tr>
            <td colspan="12">
                {$observations_tab}
            </td>
	</tr>
</table>
