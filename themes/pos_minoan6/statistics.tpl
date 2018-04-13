{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
* @license   Commercial license see license.txt
* Support by mail  : contact@prestaplugins.com
* Support on forum : Patanock
* Support on Skype : Patanock13
*}
<!-- PAGE Historial Transferencias -->

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<form method="post">
    <div class="row">
        {assign var="borders" value=1}
        {foreach key=key item=item from=$statistics}
            {if $borders == 1}
                <div class="row">
            {/if}
                    <div class="panel_month col-lg-4" id="pannel-{$key}">
                        <div class="title col-lg-12">{$item.year} {$item.month}</div>
                        <br>
                        <div class="col-lg-12"><span>Cantidad Empleados: </span>{$item.customers}</div>
                        <div class="col-lg-12"><span>Total Fluz Network: </span>{$item.fluz|round:0}</div>
                        <div class="col-lg-12"><span>Total Network: </span>COP $ {$item.fluzcop|round:0}</div>
                        <div class="col-lg-12"><span>Total Transacciones: </span>{$item.orders}</div>
                        {if  $item.manufacturers|@count > 0} 
                            <div class="col-lg-12 title-manufacturers">Comercios Destacados</div>
                            <div class="row manufacturers">
                                {foreach item=manufacturer from=$item.manufacturers}
                                    <a class="" href="{$link->getCategoryLink($manufacturer.id_category, $manufacturer.link_rewrite)|escape:'html':'UTF-8'}" title="{$manufacturer.manufacturer|escape:'html':'UTF-8'}">
                                        <img src="{$s3}m/{$manufacturer.id_manufacturer}.jpg" alt="{$manufacturer.manufacturer|lower|escape:'htmlall':'UTF-8'}" title="{$manufacturer.manufacturer|lower|escape:'htmlall':'UTF-8'}" />
                                    </a>
                                {/foreach}
                            </div>
                        {else}
                            <div class="col-lg-12" style="margin-top: 50px;">&nbsp;</div>
                        {/if}
                        {if  $item.categories|@count > 0} 
                            <div class="col-lg-12 title-categories" id="{$key}">Categorias Destacadas</div>
                            <div class="row categories" id="list-{$key}">
                                <div class="row title-table">
                                    <div class="col-lg-4">Categoria</div>
                                    <div class="col-lg-4"># Ordenes</div>
                                    <div class="col-lg-4">Total</div>
                                </div>
                                {foreach item=category from=$item.categories}
                                    <div class="row">
                                        <div class="col-lg-4">{$category.category}</div>
                                        <div class="col-lg-4">{$category.orders}</div>
                                        <div class="col-lg-4">COP $ {$category.total|round:0}</div>
                                    </div>
                                {/foreach}
                            </div>
                        {else}
                            <div class="col-lg-12" style="margin-top: 25px;">&nbsp;</div>
                        {/if}
                    </div>
            {$borders = $borders + 1}
            {if $borders == 4}
                </div>
                {$borders = 1}
            {/if}
        {/foreach}
        {if $borders != 4}
            </div>
        {/if}
    </div>
</form>

{literal}
    <style>
        .panel_month { border-bottom: 1px solid #EF4136; font-family: 'Open Sans'; padding: 15px 10px; }
        .panel_month > .title { color: #EF4136; font-size: 20px; font-weight: bold; }
        .panel_month > div > span { color: #4E4E4E; }
        .panel_month > div { color: #EF4136; }
        .panel_month > .manufacturers { text-align: center; height: 25px; }
        .panel_month > .manufacturers > a > img { width: 40px; }
        .panel_month > .title-categories, .panel_month > .title-manufacturers { text-align: center; cursor: pointer; margin-top: 5px; color: #777777; font-weight: bold; padding: 10px 0; }
        .panel_month > .categories { text-align: center; display: none; }
        .panel_month > .categories > div { color: #4E4E4E; border-bottom: 1px solid #C9B197; font-size: 11px; line-height: 12px; padding: 5px 0; }
        .panel_month > .categories > .title-table { font-weight: bold; }
        .panel_month > .categories > div > div { vertical-align: middle; }
    </style>
    
    <script>
        $(".title-categories").on('mouseover mouseout click', function(e) {
            var cat = $(this).attr("id");
            $(".panel_month").css("border","none");
            $(".panel_month").css("border-bottom","1px solid #EF4136");
            if( !$("#list-"+cat).is(":visible") ){
                $(".categories").slideUp();
                $("#list-"+cat).slideToggle();
                $("#pannel-"+cat).css("border","1px solid #EF4136");
            } else {
                $("#list-"+cat).slideUp();
            }
        });
    </script>
{/literal}