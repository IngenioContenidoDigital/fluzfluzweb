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

<li>
        {if $node.father == "true"}
                <a id="categoryfather-{$node.id_category}" class="categoryfather" onclick="downcategory({$node.id_category});">
                        {$node.name|escape:'html':'UTF-8'}
                </a>
        {else}
                <a id="category-opt-{$node.id_category}" href="{$node.link|escape:'html':'UTF-8'}">
                        {$node.name|escape:'html':'UTF-8'}
                </a>
        {/if}

	{if $node.children|@count > 0}
		<ul style="display: none;" id="categorychildren-{$node.id_category}" class="categorychildren">
                        <li>
                                <a id="category-opt-0" href="{$node.link|escape:'html':'UTF-8'}" style="font-weight: bold; font-style: italic;">
                                        {l s='Todas'}
                                </a>
                        </li>
			{foreach from=$node.children item=child2 name=categoryTreeBranch}
                                {include file="$branche_tpl_path" node=$child2}
			{/foreach}
		</ul>
	{/if}
</li>