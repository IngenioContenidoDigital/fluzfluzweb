{capture name=path}{l s='cardsview'}{/capture}
{if !$cards}
    <h1>{l s='No hay resultados'}</h1>
{else}
    <div class='container c'>
    {foreach from=$cards item=card}
        <a class="myfanc" href="#myspecialcontent">
            <div class="card"><img class="col-lg-3 col-md-3 col-sm-3 col-xs-3" src="{$img_manu_dir}{$card.id_manufacturer}.jpg" width="40px" height="40px"/>
                <div class="col-lg-7 col-md-7 col-sm-5 col-xs-8 codigoCard"><span style="color: #000;">{l s='Tarjeta: '}</span><span class="codeImg">{$card.card_code}</span></div>
                <div class="oculto">{$img_manu_dir}{$card.id_manufacturer}.jpg</div>
            </div>
        </a>
        
        <div id="pOculto">{displayPrice price=$card.price no_utf8=false convert=false}</div>
        <div id="desc_oculto">{$card.description}</div>
        <div id="prodid_oculto">{$card.id_product}</div>
        <div id="nameOculto">{$card.product_name}</div>
        {if $card@iteration mod 2 ==0}<br /><br/>{/if}
    {/foreach}
    </div>
    <div id="pagination" class="pagination">
    {if $nbpagination < $cards|@count || $cards|@count > 10}
            <div id="pagination" class="pagination">
                            {if true || $nbpagination < $cards|@count}
                    <ul class="pagination">
                                    {if $page != 1}
                                    {assign var='p_previous' value=$page-1}
                            <li id="pagination_previous"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_previous|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">
                                    <img src="{$img_dir}icon/left-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></a></li>
                                    {else}
                            <li id="pagination_previous" class="disabled"><span><img src="{$img_dir}icon/left-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></span></li>
                                    {/if}
                                    {if $page > 2}
                            <li><a href="{$pagination_link|escape:'html':'UTF-8'}p=1&n={$nbpagination|escape:'html':'UTF-8'}">1</a></li>
                                            {if $page > 3}
                            <!--<li class="truncate">...</li>-->
                                            {/if}
                                    {/if}
                                    {section name=pagination start=$page-1 loop=$page+2 step=1}
                                            {if $page == $smarty.section.pagination.index}
                            <li class="current"><span>{$page|escape:'html':'UTF-8'}</span></li>
                                            {elseif $smarty.section.pagination.index > 0 && $cards|@count+$nbpagination > ($smarty.section.pagination.index)*($nbpagination)}
                            <li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$smarty.section.pagination.index|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$smarty.section.pagination.index|escape:'html':'UTF-8'}</a></li>
                                            {/if}
                                    {/section}
                                    {if $max_page-$page > 1}
                                            {if $max_page-$page > 2}
                            <!--<li class="truncate">...</li>-->
                                            {/if}
                            <li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$max_page|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$max_page|escape:'html':'UTF-8'}</a></li>
                                    {/if}
                                    {if $cards|@count > $page * $nbpagination}
                                            {assign var='p_next' value=$page+1}
                            <li id="pagination_next"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_next|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}"><img src="{$img_dir}icon/right-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></a></li>
                                    {else}
                            <li id="pagination_next" class="disabled"><img src="{$img_dir}icon/right-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></li>
                                    {/if}
                    </ul>
                            {/if}
                            {*if $cards|@count > 10}
                    <form action="{$pagination_link|escape:'html':'UTF-8'}" method="get" class="pagination">
                            <p>
                                    <input type="submit" class="button_mini" value="{l s='OK'  mod='allinone_rewards'}" />
                                    <label for="nb_item">{l s='items:' mod='allinone_rewards'}</label>
                                    <select name="n" id="nb_item">
                                    {foreach from=$nArray item=nValue}
                                            {if $nValue <= $cards|@count}
                                            <option value="{$nValue|escape:'htmlall':'UTF-8'}" {if $nbpagination == $nValue}selected="selected"{/if}>{$nValue|escape:'htmlall':'UTF-8'}</option>
                                            {/if}
                                    {/foreach}
                                    </select>
                                    <input type="hidden" name="p" value="1" />
                            </p>
                    </form>
                            {/if*}
            </div>
	{/if}
    </div>
    <div class="col-lg-6 card-view">
        <div>
            
        </div>
        <div class="title-card">
            <img id="img-prod" src="" height="" width="" alt="" class="imgCardView"/><span id="nameViewCard"></span><br/>
        </div>
        <div class="pointPrice">
                <p class="col-lg-7 col-xs-8 col-md-8 pCode">{l s="Your Gift Card ID is: "}<br><span class="micode" style="font-size:20px; color: #ef4136;"> </span></p>
                <p class="col-lg-5 col-xs-4 col-md-4 pPrice">{l s="Value: "}<br><span id="priceCard" style="font-size:20px; color: #ef4136;"></span></p>
        </div>
        <div>
            <img id="bar-code" src=""/><br/>
            <span class="micode popText" id="code-img"></span>
        </div>
    </div>
    <div class="CardInstru" data-toggle="collapse" data-target="#demo">
        <div><h4 class="insTitle">{l s='Gift Card Instructions'}</h4></div>
        <div class="pViewcard collapse" id="demo"></div>
    </div>
    <div class="CardInstru" data-toggle="collapse" data-target="#terms">
        <div><h4 class="insTitle">{l s='Terms'}</h4></div>
        <div class="terms-card collapse" id="terms"></div>
    </div>
    <div class="containerCard">
        <ul>
            <li>
                <input type="radio" id="f-option" name="selector" value="1">
              <div class="check" id="used"></div>
              <label id="labelCard" for="f-option">{l s='MARK AS USED'}</label>
            </li>

            <li>
                <input type="radio" id="s-option" name="selector" value="0">
              <div id="not-used" class="check"></div>
              <label id="labelCard2" for="s-option">{l s='MARK AS FINISHED'}</label>
            </li>
        </ul>
    </div>
   
    <!--<div style="display: none;">
            <div id="myspecialcontent" class="infoPopUp">
                <div class="cardDesign">
                    <div class="tCardView">
                        <img id="img-prod" src="" height="" width="" alt="" class="imgCardView"/><span id="nameViewCard"></span><br/>
                    </div>
                    <div class="pointPrice">
                            <p class="col-lg-7 col-xs-8 col-md-8 pCode">{l s="Your Gift Card ID is: "}<br><span class="micode" style="font-size:20px;"> </span></p>
                            <p class="col-lg-5 col-xs-4 col-md-4 pPrice">{l s="Value: "}<br><span id="priceCard" style="font-size:20px;"></span></p>
                    </div>
                    <div>
                        <img id="bar-code" src=""/><br/>
                        <span class="micode popText" id="code-img"></span>
                    </div>
                </div>
                <div class="containerCard">
                    <ul>
                        <li>
                            <input type="radio" id="f-option" name="selector" value="1">
                          <div class="check" id="used"></div>
                          <label id="labelCard" for="f-option">{l s='MARK AS USED'}</label>
                        </li>
                       
                        <li>
                            <input type="radio" id="s-option" name="selector" value="0">
                          <div id="not-used" class="check"></div>
                          <label id="labelCard2" for="s-option">{l s='MARK AS FINISHED'}</label>
                        </li>
                    </ul>
                </div>        
                <div class="CardInstru">
                    <h4 class="insTitle">{l s='Gift Card Instructions'}</h4>
                    <div class="pViewcard"></div>
                </div> 
            </div>
    </div>-->
{/if}
{literal}
    <script>

        $('.myfanc').click(function(){
            var codeImg2 = $(this).find(".codeImg").html();
            var price = document.getElementById("pOculto").innerHTML;
            var name = document.getElementById("nameOculto").innerHTML;
            var description = document.getElementById("desc_oculto").innerHTML;
            var idproduct = document.getElementById("prodid_oculto").innerHTML;
            var ruta = $(this).before().find(".oculto").html();
            $("#img-prod").attr("src",ruta)
            $.ajax({
                    method:"POST",
                    data: {'action': 'consultcodebar', 'codeImg2': codeImg2,'price':price,'idproduct':idproduct},
                    url: '/raizBarcode.php', 
                    success:function(response){
                        var response = jQuery.parseJSON(response);
                        if (response.used == 1) {
                           $('#labelCard').addClass('labelcard');
                           $('#used').addClass('checkConfirm');
                           $('#not-used').removeClass('checkConfirm');
                           $('#labelCard2').removeClass('labelcard');
                        } else {
                           $('#labelCard2').addClass('labelcard');
                           $('#labelCard').removeClass('labelcard');
                           $('#not-used').addClass('checkConfirm');
                           $('#used').removeClass('checkConfirm')
                        }
                        
                        if ( response.codetype == 0 ) {
                            $('#bar-code').attr('src','.'+response.code);
                            $('.pointPrice').css("float","left").css("width","50%").css("padding","10px 0 0 10px");
                            $('.pCode').addClass("col-lg-12").addClass("col-xs-12").addClass("col-md-12");
                            $('.pPrice').addClass("col-lg-12").addClass("col-xs-12").addClass("col-md-12");
                            $('#bar-code').parent().css("float","right");
                            $('#bar-code').parent().css("margin-right","10%");
                            $('.popText').css("font-size","14px");
                        }
                        if ( response.codetype == 1 ) {
                            $('#bar-code').attr('src','.'+response.code);
                        }
                        if ( response.codetype == 2 ) {
                            $('.popText').parent().css("margin-top","50px");
                            $('.popText').css("background","none");
                            $('.popText').css("color","#fff");
                        }
                        
                        $('.micode').html(codeImg2);
                        $('#priceCard').html(price);
                        $('#nameViewCard').html(name);
                        $('.pViewcard').html(description);
                    }
              });
        });
        
        
        $('#used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard').addClass('labelcard');
            $('#labelCard2').removeClass('labelcard');
            $('#not-used').removeClass('checkConfirm');
        });
        
        $('#labelCard').click(function(){
            $(this).addClass('labelcard');
            $('#used').addClass('checkConfirm');
            $('#not-used').removeClass('checkConfirm');
            $('#labelCard2').removeClass('labelcard');
        
        });
        
        $('#labelCard2').click(function(){
            $(this).addClass('labelcard');
            $('#labelCard').removeClass('labelcard');
            $('#not-used').addClass('checkConfirm');
            $('#used').removeClass('checkConfirm')
            
        });
        
        $('#not-used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard2').addClass('labelcard');
            $('#labelCard').removeClass('labelcard');
            $('#used').removeClass('checkConfirm');
        });
        
    </script>
{/literal}
{literal}
    <script>
        $('input:radio[name=selector]').click(function() {
            var val = $('input:radio[name=selector]:checked').val();
            var idproduct = document.getElementById("prodid_oculto").innerHTML;
            var codeImg2 = document.getElementById("code-img").innerHTML;
            $.ajax({
                    method:"POST",
                    data: {'action': 'updateUsed','val': val, 'codeImg2': codeImg2,'idproduct':idproduct},
                    url: '/raizBarcode.php'
              });
        });
    </script>
{/literal}
{*literal}
    <style>
        .fancybox-lock .fancybox-overlay{display: none !important;}
    </style>    
{/literal*}
