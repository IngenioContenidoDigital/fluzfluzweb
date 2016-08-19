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
<!-- MODULE allinone_rewards -->

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div class="row">
    <h1 class="page-heading">{l s='Cash Out'}</h1>
    <div class="cashoutDiv col-lg-12">
        <span class="cashoutTitle col-lg-6">{l s="Your Point Total: "}</span>
        <span class="cashoutPoint col-lg-6"> {$totalAvailable}</span>
    </div>
    <div class="cashoutDiv col-lg-12">
        <span class="cashoutTitle col-lg-6">{l s="Cash Conversion Total: "}</span>
        <span class="cashoutPoint col-lg-6"> {displayPrice price=$pago currency=$payment_currency}</span>
    </div>
</div>
        
{if isset($payment_error)}
	{if $payment_error==1}
	<p class="error">{l s='Please fill all the required fields' mod='allinone_rewards'}</p>
	{elseif $payment_error==2}
	<p class="error">{l s='An error occured during the treatment of your request' mod='allinone_rewards'}</p>
	{/if}
{/if}

	<div id="general_txt" style="padding-bottom: 20px">{$general_txt|escape:'string':'UTF-8'}</div>

{if $return_days > 0}
	<p>{l s='Rewards will be available %s days after the validation of each order.'  sprintf={$return_days|intval} mod='allinone_rewards'}</p>
{/if}
	
    {if $rewards}    
       
	{if $voucher_minimum_allowed}
            <div id="min_transform" style="clear: both">{l s='The minimum required to be able to transform your rewards into vouchers is' mod='allinone_rewards'} <b>{$voucherMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}
	{if $payment_minimum_allowed}
            <div id="min_payment" style="clear: both">{l s='The minimum required to be able to ask for a payment is' mod='allinone_rewards'} <b>{$paymentMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}

	{*if $voucher_button_allowed}
                    
            <div class="row" style="margin-top:8%;">

                <h1 class="page-heading">{l s='Select Deposit Method' mod='allinone_rewards'}</h1>
                <div class="col-lg-12 containerCash">
                    <h4 class="directDep">{l s='Direct Deposit'}</h4>
                    <div class="contDirectDep">
                        <div class="row">    
                            <div class="required formCash form-group col-lg-6 col-md-6">
                                <div class="col-xs-12 col-sm-5 col-md-12 col-lg-12">
                                <label class="required textCard">
                                    {l s='Numero de Tarjeta'}
                                </label>
                                </div>
                                
                                    <div class="col-xs-12 col-sm-7 col-md-12 col-lg-8">
                                        <input type="text" pattern="[0-9]{literal}{13,16}{/literal}" class="imageCard formCardCash form-control" id="payment_details" name="payment_details" autocomplete="off" required/>
                                    </div>
                                
                            </div>   
                            <div class="required formCash form-group col-lg-6 col-md-6">
                                <div class="col-xs-12 col-sm-5 col-md-12 col-lg-12">
                                <label class="required textCard">
                                    {l s='Deposit Amount'}
                                </label>
                                </div>
                                {literal}
                                    <div class="col-xs-12 col-sm-7 col-md-12 col-lg-8">
                                        <input type="text" pattern="[0-9]" class="imageCard formCardCash form-control" id="numCard" name="numCard" autocomplete="off" required/>
                                    </div>
                                {/literal}
                            </div>     
                        </div>
                    </div>
                    <p class="{if $payment_invoice}required{/if} text">
                        <label for="payment_invoice">{l s='Invoice' mod='allinone_rewards'} ({displayPrice price=$totalForPaymentDefaultCurrency currency=$payment_currency}) {if $payment_invoice}<sup>*</sup>{/if}</label>
                        <input id="payment_invoice" name="payment_invoice" type="file" required>
                    </p>
                    <input class="button" type="submit" value="{l s='REQUEST DEPOSIT' mod='allinone_rewards'}" name="submitPayment" id="submitPayment">

                </div>
            </div>
            
	{/if*}
        
	{if $payment_button_allowed}
            <div id="payment" style="clear: both">
                    <a onClick="$('#payment_form').toggle()">{l s='Payment of your available rewards'} <span>{displayPrice price=$pago currency=$payment_currency}</span></a>
                    <form id="payment_form" class="std" method="post" action="{$pagination_link|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" style="display: {if isset($smarty.post.payment_details)}block{else}none{/if}">
                           
                            <fieldset>
                                <h4 class="directDep">{l s='Direct Deposit'}</h4>
                                 <div class="contDirectDep">
                                     
                                    <div id="payment_txt">{$payment_txt|escape:'string':'UTF-8'}</div>
                                    <div class="required formCash form-group col-lg-6 col-md-6">
                                        <p class="required">
                                        <label class="required textCard">
                                            {l s='Numero de Tarjeta'}
                                        </label>
                                        <div class="col-xs-12 col-sm-7 col-md-12 col-lg-8" style="padding-left:0px;">
                                            <input type="text" pattern="[0-9]{literal}{13,16}{/literal}" class="imageCard formCardCash form-control" id="payment_details" name="payment_details" autocomplete="off" required/>
                                        </div>
                                        </p>
                                    </div>
                                    <!--<div class="required formCash form-group col-lg-6 col-md-6">
                                        <div class="col-xs-12 col-sm-5 col-md-12 col-lg-12">
                                        <label class="required textCard">
                                            {l s='Deposit Amount'}
                                        </label>
                                        </div>
                                        {literal}
                                            <div class="col-xs-12 col-sm-7 col-md-12 col-lg-8">
                                                <input type="text" pattern="[0-9]" class="imageCard formCardCash form-control" id="numCard" name="numCard" autocomplete="off" required/>
                                            </div>
                                        {/literal}
                                    </div>-->
                                    <p class="{if $payment_invoice}required{/if} text">
                                            <label style="display:none;" for="payment_invoice">{l s='Invoice' mod='allinone_rewards'} ({displayPrice price=$totalForPaymentDefaultCurrency currency=$payment_currency}) {if $payment_invoice}<sup>*</sup>{/if}</label>
                                            <input id="payment_invoice" name="payment_invoice" type="file" accept="application/pdf" required>
                                    </p>
                                    <input class="button" type="submit" value="{l s='REQUEST DEPOSIT'}" name="submitPayment" id="submitPayment">
                                 </div>
                            </fieldset>
                           
                    </form>
            </div>
	{/if}
        {/if}
        </div>        
        
    </div>
        {if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
        <ul class="footer_links clearfix">
                <li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account' mod='allinone_rewards'}</span></a></li>
                <li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{else}{$base_dir|escape:'htmlall':'UTF-8'}{/if}"><span><i class="icon-chevron-left"></i> {l s='Home' mod='allinone_rewards'}</span></a></li>
        </ul>
        {else}
    <ul class="footer_links clearfix">
            <li><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'htmlall':'UTF-8'}icon/my-account.gif" alt="" class="icon" /> {l s='Back to your account' mod='allinone_rewards'}</a></li>
            <li class="f_right"><a href="{$base_dir|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'html':'UTF-8'}icon/home.gif" alt="" class="icon" /> {l s='Home' mod='allinone_rewards'}</a></li>
    </ul>
{/if}
{literal}
    <style>
        #left_column{display: none !important;}
        #center_column{min-width: 100% !important; margin: 0px;}
        #columns{margin-bottom: 0px !important; min-width: 100%;}
        .banner-home{margin: 0px;}
        .footer_links{display: none;}
        #min_payment{display: none;}
        .rewards{width: 80%; margin: 0 auto;}
        .rewards table.std td { font-size: 11px; line-height: 13px; padding: 10px !important; background:#f9f9f9; border: #fff 5px solid; border-right:none; border-left:none;}
        .page-heading{margin:0;}
        .breadcrumb{margin-left: 11%; font-size:12px;}
    </style>
{/literal}
{literal}
    <script>
            $('#numCard').on('keyup',function(){
                $(this).removeClass('visa');
                $(this).removeClass('mastercard');
                $(this).removeClass('amex');
                $(this).removeClass('discover');
                if($(this).val()===""){
                    $(this).removeClass('visa');
                }else{
                    $(this).validateCreditCard(function(result) {    
                        switch(result.card_type.name){
                            case 'visa':
                                $(this).addClass('visa');
                                break;
                            case 'mastercard':
                                $(this).addClass('mastercard');
                                break;
                             case 'amex':
                                $(this).addClass('amex');
                                break;
                             case 'discover':
                                $(this).addClass('discover');
                                break;
                            default:
                                $(this).removeClass('visa');
                                $(this).removeClass('mastercard');
                                $(this).removeClass('amex');
                                $(this).removeClass('discover');
                                break;
                        }
                    })
                }
            });
    </script>
    
{/literal}
{literal}
    <style>
    div.uploader span.filename{margin-left: 18px !important;}
    </style>
{/literal}

<!-- END : MODULE allinone_rewards -->