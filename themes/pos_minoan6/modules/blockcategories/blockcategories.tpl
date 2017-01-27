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
    <h2 class="title_block" after-content="+">{l s='Categorias'}</h2>
    <div class="block_content">
        <div class="line-bottom"></div>
        <ul class="tree {if $isDhtml}dhtml{/if}">
            {foreach from=$blockTreeCategories item=child name=blockTreeCategories}
                {include file="$branche_tpl_path" node=$child}
            {/foreach}
        </ul>
    </div>
</div>
<!-- /Block categories module -->

<!-- Block filter brands -->
<div id="categories_block_left" class="block blockCat">
    <h2 class="title_block" after-content="+">{l s='Comercios'}</h2>
    <div class="block_content">
        <div class="line-bottom"></div>
        <ul class="tree {if $isDhtml}dhtml{/if}">
            <li>
                <a class="manufacturer_filter" id="" href="" style="font-weight: bold; font-style: italic;">
                    {l s='Todos'}
                </a>
            </li>
            {foreach from=$manufacturers_filter item=manufacturer_filter}
                <li>
                    <a class="manufacturer_filter" id="{$manufacturer_filter.id}" href="">
                        {$manufacturer_filter.name|lower}
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
<!-- /Block filter brands -->

<!-- Block filter cities -->
<div id="categories_block_left" class="block blockCat">
    <h2 class="title_block" after-content="+">{l s='Ciudades'}</h2>
    <div class="block_content">
        <div class="line-bottom"></div>
        <ul class="tree {if $isDhtml}dhtml{/if}">
            <li>
                <a class="city_manufacturer_filter" id="" href="" style="font-weight: bold; font-style: italic;">
                    {l s='Todas'}
                </a>
            </li>
            {foreach from=$cities_manufacturer_filter item=city_manufacturer_filter}
                <li>
                    <a class="city_manufacturer_filter" id="{$city_manufacturer_filter.city|lower|replace:" ":""|replace:"(":""|replace:")":""|replace:".":""|replace:",":""|replace:"á":"a"|replace:"é":"e"|replace:"í":"i"|replace:"ó":"o"|replace:"ú":"u"|replace:"Á":"a"|replace:"É":"e"|replace:"Í":"i"|replace:"Ó":"o"|replace:"Ú":"u"}" href="">
                        {$city_manufacturer_filter.city|lower}
                    </a>
                </li>
            {/foreach}
        </ul>
    </div>
</div>
<!-- /Block filter cities -->



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
        
        $(".manufacturer_filter").click(function(e) {
            var manufacturer = $(this).attr("id");
            manufacturer = manufacturer.replace("m", "");
            var days = 15;

            date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));

            document.cookie = "citymanufacturerfilter=; expires="+date.toGMTString()+"; path=/";
            document.cookie = "manufacturerfilter="+manufacturer+"; expires="+date.toGMTString()+"; path=/";
            var categorypage = window.location.protocol+"//"+window.location.hostname+"/es/content/6-categorias";
            e.preventDefault();
            window.location.href = categorypage;
        });
        
        $(".city_manufacturer_filter").click(function(e) {
            var city = $(this).attr("id");
            var days = 15;

            date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            
            document.cookie = "manufacturerfilter=; expires="+date.toGMTString()+"; path=/";
            document.cookie = "citymanufacturerfilter="+city+"; expires="+date.toGMTString()+"; path=/";
            var categorypage = window.location.protocol+"//"+window.location.hostname+"/es/content/6-categorias";
            e.preventDefault();
            window.location.href = categorypage;
        });

        $(".title_block").click(function() {
            if ( $(this).hasClass('active') ) {
                $(this).css("border-bottom","1px dotted #D2D1D1");
                $(this).attr('after-content','+');
            } else {
                $(this).css("border-bottom","none");
                $(this).attr('after-content','-');
            }
        });
    </script>
{/literal}
{literal}
<style>
    .categorychildren{padding-left: 15px;}
    @media (max-width: 1024px) {
        .title_block { font-size: 11px!important; }
        .title_block::after { font-size: 20px!important; }
    }
    @media (max-width: 1000px) {
        .title_block { font-size: 11px!important; }
        .title_block::after { font-size: 12px!important; }
    }
</style>
{/literal}