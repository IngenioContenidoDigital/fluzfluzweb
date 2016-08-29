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


{* ORIGINAL
<table style="width: 100%">
        <tr>
                <td style="width: 50%">
                        {if $logo_path}
                                <img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
                        {/if}
                </td>
                <td style="width: 50%; text-align: right;">
                        <table style="width: 100%">
                                <tr>
                                        <td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%;">{if isset($header)}{$header|escape:'html':'UTF-8'|upper}{/if}</td>
                                </tr>
                                <tr>
                                        <td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'html':'UTF-8'}</td>
                                </tr>
                                <tr>
                                        <td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'html':'UTF-8'}</td>
                                </tr>
                        </table>
                </td>
        </tr>
</table>
*}

<table style="width: 100%; color: #949496; font-size: 10pt;" border="0">
    <tr>
        <td style="width: 80%; color: #FAA621; font-weight: bold; font-size: 35pt; letter-spacing: 6px;">FLUZ FLUZ<sub style="font-size: 13pt;">&reg;</sub></td>
        <td style="line-height: 2pt;">
            Factura de venta:<br>
            <span style="background-color: #EFEFEF; line-height: 1.5pt;">&nbsp;{$title|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;</span>
        </td>
    </tr>
    <tr>
        <td style="font-size: 7.5pt;">
            <span>Fluz Fluz Colombia SAS, NIT.900.961.325-6<br>
                IVA R&eacute;gimen Com&uacute;n<br>
                Actividad Econ&oacute;mica CIIU 4791 4 Tarifa 11.04 X 1000<br>
                Ley 1429 de 2010, No efectuar retenci&oacute;n en la fuente
            </span>
        </td>
        <td style="line-height: 1.6pt;">
            {assign var=dateexp value="/"|explode:$date}
            Fecha:<br>
            <span style="background-color: #EFEFEF; line-height: 1.5pt;">&nbsp;{$dateexp.0}&nbsp;&nbsp;/&nbsp;&nbsp;{$dateexp.1}&nbsp;&nbsp;/&nbsp;&nbsp;{$dateexp.2}&nbsp;&nbsp;</span>
        </td>
    </tr>
</table>