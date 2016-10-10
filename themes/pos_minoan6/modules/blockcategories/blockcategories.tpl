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
<!-- Block categories module -->
<div id="categories_block_left" class="block blockCat">
	<h2 class="title_block">
            {l s='Categorias'}
	</h2>
	<div class="block_content">
		<ul class="tree {if $isDhtml}dhtml{/if}">
			{foreach from=$blockTreeCategories item=child name=blockTreeCategories}
                                {include file="$branche_tpl_path" node=$child}
			{/foreach}
		</ul>
	</div>
</div>
<!-- /Block categories module -->
<script>
    {if $currentCategory->id != "" && $currentCategory->id_parent != "" }
        var id_current = {$currentCategory->id};
        var id_parent = {$currentCategory->id_parent};
    {else}
        var id_current = 0;
        var id_parent = 0;
    {/if}
        
    if ( id_parent == 1 || id_parent == 2 ) {
        id_parent = id_current;
    }
</script>
{literal}
    <script>
        $(function() {
            if ( id_current != "" ) {
                $("#category-opt-"+id_current).css("color","#E1382C");
                downcategory(id_parent, false);
            }
        });
        function downcategory(id, colorfahter = true) {        
            if( $("#categorychildren-"+id).is(":visible") ) {
                $(".categoryfather").css("color","#6C6C6C");
                $(".categorychildren").css("display","none");
            } else {
                $(".categoryfather").css("color","#6C6C6C");
                $(".categorychildren").css("display","none");
                $("#categorychildren-"+id).css("display","block");
                if ( colorfahter ) {
                    $("#categoryfather-"+id).css("color","#E1382C");
                }
            }
        }
    </script>
{/literal}