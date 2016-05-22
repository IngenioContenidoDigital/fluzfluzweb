{capture name=path}{l s='cardsview'}{/capture}
{if !$cards}
    <h1>{l s='No hay resultados'}</h1>
{else}
    <div class='container'>
    {foreach from=$cards item=card}
        <div class="card"><img src="{$img_manu_dir}{$card.id_manufacturer}.jpg" width="40px" height="40px"/><a href="{$card.card_code}">{l s='Tarjeta: '}{$card.card_code}</a></div>
        {if $card@iteration mod 2 ==0}<br /><br/>{/if}
    {/foreach}
    </div>
{/if}
