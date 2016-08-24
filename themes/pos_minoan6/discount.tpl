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
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Explore Network'}</span>{/capture}

<h1 class="page-heading">
    {l s='Explore Network'}
</h1>

<form action="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" method="post" id="formnetwork">
    <div class="blockcontainer">
        <div class="block-network">
            <h2>{l s='Explore Network'}</h2>
            <input type="text" name="searchnetwork" id="searchnetwork" class="textsearch" placeholder="{l s='Search member'}" value="{$searchnetwork}"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
            <table class="tablenetwork">
                {foreach from=$members item=member}
                    <tr>
                        <td>
                            <table class="tablecontent">
                                <tr>
                                    <td rowspan="2" class="img"><img src="/modules/blockmyaccountheader/avatar.png" height="30" width="30"></td>
                                    <td colspan="3" class="line"><span class="name">{$member.name}</span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td><span class="information">{l s='Points Contributed:'} </span><span class="data">0</span></td>
                                    <td><span class="information">{l s='Network Level:'} </span><span class="data">{$member.level}</span></td>
                                    <td><span class="information">{l s='Date Added:'} </span><span class="data">{$member.dateadd}</span></td>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                {/foreach}
            </table>
        </div>
        {*<div class="block-messages">
            <h2>{l s='My Messages'}</h2>
            <input type="text" name="searchmessage" id="searchmessage" class="textsearch" placeholder="Search member"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
        </div>*}
    </div>
</form>

<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to your account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Home'}
            </span>
        </a>
    </li>
</ul>
