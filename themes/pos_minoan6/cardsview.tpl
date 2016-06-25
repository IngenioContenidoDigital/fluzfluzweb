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
                    <p class="col-lg-6 pPrice">{l s="Value: "}<br/><span id="priceCard"></span></p>
                    <img id="bar-code" src=""/><br/>
                    <span class="popText" class="micode"></span>
                </div>
                <div class="containerCard">
                    <ul>
                        <li>
                          <input type="radio" id="f-option" name="selector">
                          <div class="check" id="used"></div>
                          <label id="labelCard" for="f-option">{l s='MARK AS USED'}</label>
                        </li>
                        <li>
                          <input type="radio" id="s-option" name="selector">
                          <div id="not-used" class="check"><div class="inside"></div></div>
                          <label id="labelCard2" for="s-option">{l s='MARK AS FINISHED'}</label>
                        </li>
                    </ul>
                </div>        
                <div class="CardInstru">
                    <h4 class="insTitle">{l s='Gift Card Instructions'}</h4>
                    <div class="pViewcard">
                        {l s="Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod"}
                    </div>
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
            var ruta = $(this).before().find(".oculto").html();
            $("#img-prod").attr("src",ruta)
	
            $.ajax({
                    method:"POST",
                    data: {'codeImg2': codeImg2,'price':price},
                    url: '/raizBarcode.php', 
                    success:function(response){
                        $('#bar-code').attr('src','.'+response);
                        $('.micode').html(codeImg2);
                        $('#priceCard').html(price);
                        $('#nameViewCard').html(name);
                    }
              });
        });
        
        $('#used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard').addClass('labelcard');
            $('#labelCard2').removeClass('labelcard');
            $('#not-used').removeClass('checkConfirm');
        });
        
        $('#not-used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard2').addClass('labelcard');
            $('#labelCard').removeClass('labelcard');
            $('#used').removeClass('checkConfirm');
        });
        
    </script>
{/literal}
