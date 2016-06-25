
{capture name=path}{l s='cardsview'}{/capture}
{if !$cards}
    <h1>{l s='No hay resultados'}</h1>
{else}
    <div class='container c'>
    {foreach from=$cards item=card}
        <div class="card"><img src="{$img_manu_dir}{$card.id_manufacturer}.jpg" width="40px" height="40px"/><a class="myfancybox" href="#myspecialcontent"><div><span style="color: #000;">{l s='Tarjeta: '}</span><span class="codeImg">{$card.card_code}</span></div>
        <div class="oculto">{$link->getImageLink($card.link_rewrite, $card.id_image, 'home_default')}</div></a>
        </div>
        {if $card@iteration mod 2 ==0}<br /><br/>{/if}
    {/foreach}
    </div>
    <div style="display: none;">
            <div id="myspecialcontent" class="infoPopUp">
                
                <img id="img-prod" src="" height="" width="" alt="" /><br/>
                <p class="col-lg-6">{l s="Your Gift Card ID is: "}</p><span  class="micode"> </span>
                <p>{l s="Value: "}</p>
                <img id="bar-code" src="" /><br/>
                <span class="popText" class="micode"></span>
            </div>
        </div>
{/if}
{literal}
    <script>

        $('.myfancybox').click(function(){
            var codeImg2 = $(this).find(".codeImg").html();
            var ruta = $(this).before().find(".oculto").html();
            $("#img-prod").attr("src",ruta)
	
            $.ajax({
                    method:"POST",
                    data: {'codeImg2': codeImg2},
                    url: '/raizBarcode.php', 
                    success:function(response){
                        $('#bar-code').attr('src','.'+response);
                        $('.micode').html(codeImg2);
                    }
              });
        })
        
    </script>
{/literal}
