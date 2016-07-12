{capture name=path}{l s='cardsview'}{/capture}
{if !$cards}
    <h1>{l s='No hay resultados'}</h1>
{else}
    <div class='container c'>
    {foreach from=$cards item=card}
        <div class="card"><img src="{$img_manu_dir}{$card.id_manufacturer}.jpg" width="40px" height="40px"/><a class="myfancybox" href="#myspecialcontent"><div><span style="color: #000;">{l s='Tarjeta: '}</span><span class="codeImg">{$card.card_code}</span></div>
        <div class="oculto">{$img_manu_dir}{$card.id_manufacturer}.jpg</div></a>
        </div>
        <div id="pOculto">{displayPrice price=$card.price no_utf8=false convert=false}</div>
        <div id="desc_oculto">{$card.description}</div>
        <div id="prodid_oculto">{$card.id_product}</div>
        <div id="nameOculto">{$card.product_name}</div>
        {if $card@iteration mod 2 ==0}<br /><br/>{/if}
    {/foreach}
    </div>
    
    <div style="display: none;">
            <div id="myspecialcontent" class="infoPopUp">
                <div class="cardDesign">
                    <div class="tCardView">
                        <img id="img-prod" src="" height="" width="" alt="" class="imgCardView"/><span id="nameViewCard"></span><br/>
                    </div>
                    <p class="col-lg-6 pCode">{l s="Your Gift Card ID is: "}<br/><span class="micode" style="font-size:16px;"> </span></p>
                    <p class="col-lg-6 pPrice">{l s="Value: "}<br/><span id="priceCard" style="font-size:16px;"></span></p>
                    <img id="bar-code" src=""/><br/>
                    <span class="micode popText" id="code-img"></span>
                </div>
                <div class="containerCard">
                    <ul>
                        <li>
                          <input type="radio" id="f-option" name="selector" value="1">
                          <div class="check" id="used" {if $usedQ == 1}checked="checked"{/if}></div>
                          <label id="labelCard" for="f-option" {if $usedQ == 0}checked="checked"{/if}>{l s='MARK AS USED'}</label>
                        </li>
                        {debug}
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
    </div>
{/if}
{literal}
    <script>

        $('.myfancybox').click(function(){
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
                        $('#bar-code').attr('src','.'+response);
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