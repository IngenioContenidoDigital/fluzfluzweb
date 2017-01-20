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
    <label style="margin-top: 10px;">Ubicaci&oacute;n:</label>
    <select class="form-control" name="city_manufacturer_filter" id="city_manufacturer_filter">
        <option id="option_" value="">- Ciudad -</option>
        {foreach from=$cities_manufacturer_filter item=city_manufacturer_filter}
            <option id="option_{$city_manufacturer_filter.city|lower|replace:" ":""|replace:"(":""|replace:")":""|replace:".":""|replace:",":""|replace:"á":"a"|replace:"é":"e"|replace:"í":"i"|replace:"ó":"o"|replace:"ú":"u"|replace:"Á":"a"|replace:"É":"e"|replace:"Í":"i"|replace:"Ó":"o"|replace:"Ú":"u"}" value="{$city_manufacturer_filter.city}">{$city_manufacturer_filter.city}</option>
        {/foreach}
    </select>
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
        
        var cityselected = getCookie("citymanufacturerfilter");
        if ( cityselected != null && cityselected != "" ) {
            cityselected = cityselected.toLowerCase();
            cityselected = cityselected.replace(" ", "").replace("(", "").replace(")", "").replace(".", "").replace(",", "").replace("á", "a").replace("é", "e").replace("´i", "i").replace("ó", "o").replace("ú", "u");
            $("#option_"+cityselected).attr("selected","selected");
        }

        $("#city_manufacturer_filter").change(function() {
            var city = $("#city_manufacturer_filter").val();
            var days = 15;

            date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));

            document.cookie = "citymanufacturerfilter="+city+"; expires="+date.toGMTString()+"; path=/";
            location.reload();
        });
        
        function getCookie(name) {
            var value = "; " + document.cookie;
            var parts = value.split("; " + name + "=");
            if (parts.length == 2) return parts.pop().split(";").shift();
        }
    </script>
{/literal}