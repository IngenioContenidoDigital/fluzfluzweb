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
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
{include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div class="banner-home">
    <div class="banner-box banner1" style="text-align: right; margin-top: -35px; background: url('/img/cms/FluzFluz/network/bannerNetwork.png') center center / 100% no-repeat transparent;">
            <div class='col-lg-12 col-xs-12 col-md-12 col-sm-12 bannerNetwork'>
            <div class="divNetwork">
                <h1 class="col-lg-6 col-md-6 col-sm-6 col-xs-6 titleNetwork">+{$totalAvailable/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}
                    <br/><p class="pNetwork">{l s='Your Total Points' mod='allinone_rewards'}</p>
                </h1>
            </div>
            <div class="divNetwork">
                <h1 class="col-lg-6 col-md-6 col-sm-6 col-xs-6 titleNetwork">+{$totalpointNetwork|number_format:0}
                    <br/><p class="pNetwork">{l s="Total Network Points" mod='allinone_rewards'}</p>
                </h1>
            </div>
            </div>
    </div>
</div>
<div id="rewards_account" class="rewards">	
<h1 class="page-heading">{l s='My rewards account' mod='allinone_rewards'}</h1>

<div id="container" class="col-lg-6 graphicStat"></div>
<div id="container2" class="col-lg-6 graphicStat">
    <h4 class="titleStats">{l s="Performance Summary" mod='allinone_rewards'}</h4>
    <div class="yourPointnet">
        <div id="yourPoint" class="puntoGrap">
            <span>{$totalGlobal/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|number_format:0}{l s=" pts."}</span>
        </div>
        <p class="pGrap">{l s="YOUR POINT GENERATION" mod='allinone_rewards'}</p><br/>
        {foreach from=$topPoint item=top}
        <div id="topPoint" class="puntoGrap">
            <span>{$top.points|number_format:0}{l s=" pts."}</span>
        </div>
        <p class="pGrap">{l s="TOP POINT GENERATION: " mod='allinone_rewards'}{$top.name}</p><br/>
        {/foreach}
        {foreach from=$worstPoint item=worst}
        <div id="worstPoint" class="puntoGrap">
            <span>{$worst.points|number_format:0}{l s=" pts."}</span>
        </div>
        <p class="pGrap">{l s="WORST POINT GENERATION: " mod='allinone_rewards'}{$worst.name}</p>
        {/foreach}
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
	<!--<table class="std">
		<thead>
			<tr>
				<th style="text-align: center" class="first_item">{l s='Total rewards' mod='allinone_rewards'}</th>
				{if $convertColumns}
				<th style="text-align: center" class="item">{l s='Already converted' mod='allinone_rewards'}</th>
				{/if}
				{if $paymentColumns}
				<th style="text-align: center" class="item">{l s='Paid' mod='allinone_rewards'}</th>
				{/if}
				<th style="text-align: center" class="item">{l s='Available' mod='allinone_rewards'}</th>
				<th style="text-align: center" class="last_item">{l s='Awaiting validation' mod='allinone_rewards'}</th>
				{if $paymentColumns}
				<th style="text-align: center" class="last_item">{l s='Awaiting payment' mod='allinone_rewards'}</th>
				{/if}
			</tr>
		</thead>
		<tr class="alternate_item">
			<td style="text-align: center">{$totalGlobal/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</td>
			{if $convertColumns}
			<td style="text-align: center">{$totalConverted/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</td>
			{/if}
			{if $paymentColumns}
			<td style="text-align: center">{$totalPaid|escape:'html':'UTF-8'}</td>
			{/if}
			<td style="text-align: center">{$totalAvailable/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</td>
			<td style="text-align: center">{$totalPending/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</td>
			{if $paymentColumns}
			<td style="text-align: center">{$totalWaitingPayment|escape:'html':'UTF-8'}</td>
			{/if}
		</tr>
	</table>-->

	<!--<table class="std">
		<thead>
			<tr>
				<th class="first_item">{l s='Event' mod='allinone_rewards'}</th>
				<th class="item">{l s='Date' mod='allinone_rewards'}</th>
				<th class="item">{l s='Reward' mod='allinone_rewards'}</th>
	{*if $rewards_duration > 0}
				<th class="item">{l s='Status' mod='allinone_rewards'}</th>
				<th class="last_item">{l s='Validity' mod='allinone_rewards'}</th>
	{else}
				<th class="last_item">{l s='Status' mod='allinone_rewards'}</th>
	{/if}
			</tr>
		</thead>
		<tbody>
	{foreach from=$displayrewards item=reward name=myLoop}
			<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
				<td>{$reward.detail|escape:'htmlall':'UTF-8'}</td>
				<td>{dateFormat date=$reward.date full=1}</td>
				<td align="right">{($reward.credits)/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</td>
				<td>{$reward.state|escape:'htmlall':'UTF-8'}</td>
		{if $rewards_duration > 0}
				<td>{if $reward.id_reward_state==RewardsStateModel::getValidationId()}{dateFormat date=$reward.validity full=1}{else}&nbsp;{/if}</td>
		{/if}
			</tr>
	{/foreach*}
		</tbody>
	</table>-->
    {if $rewards}    
       <table class="std">
            <h2 class="tituloNet">{l s="Recent Network Activity" mod='allinone_rewards'}</h2>
                <thead>
			<tr>
				<th class="first_item">{l s='NAME' mod='allinone_rewards'}</th>
				<th class="item">{l s='PURCHASE' mod='allinone_rewards'}</th>
                                <th class="first_item">{l s='POINTS' mod='allinone_rewards'}</th>
                                <th class="item">{l s='TIME' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
                    {foreach from=$activityRecent item=activity name=myLoop}
                            <tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                <td align="right"><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$activity.name|escape:'html':'UTF-8'}</td>
                                    <td><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$activity.purchase|escape:'htmlall':'UTF-8'}</td>
                                    <td align="right" style="padding-top:17px !important;">{$activity.points|number_format:0}</td>
                                    <td style="padding-top:17px !important;">{dateFormat date=$activity.time full=1}</td>

                            </tr>
                    {/foreach}
		</tbody>
	</table>

	{if $nbpagination < $rewards|@count || $rewards|@count > 10}
<div id="pagination" class="pagination">
		{if true || $nbpagination < $rewards|@count}
	<ul class="pagination">
			{if $page != 1}
			{assign var='p_previous' value=$page-1}
		<li id="pagination_previous"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_previous|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">
			<img src="{$img_dir}icon/Left.png" style="height:auto; width: 48%; padding: 0;"/></a></li>
			{else}
		<li id="pagination_previous" class="disabled"><span><img src="{$img_dir}icon/Left.png" style="height:auto; width: 48%; padding: 0;"/></span></li>
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
				{elseif $smarty.section.pagination.index > 0 && $rewards|@count+$nbpagination > ($smarty.section.pagination.index)*($nbpagination)}
		<li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$smarty.section.pagination.index|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$smarty.section.pagination.index|escape:'html':'UTF-8'}</a></li>
				{/if}
			{/section}
			{if $max_page-$page > 1}
				{if $max_page-$page > 2}
		<!--<li class="truncate">...</li>-->
				{/if}
		<li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$max_page|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$max_page|escape:'html':'UTF-8'}</a></li>
			{/if}
			{if $rewards|@count > $page * $nbpagination}
				{assign var='p_next' value=$page+1}
		<li id="pagination_next"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_next|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}"><img src="{$img_dir}icon/Right.png" style="height:auto; width: 48%; padding: 0;"/></a></li>
			{else}
		<li id="pagination_next" class="disabled"><img src="{$img_dir}icon/Right.png" style="height:auto; width: 48%; padding: 0;"/></li>
			{/if}
	</ul>
		{/if}
		{if $rewards|@count > 10}
	<form action="{$pagination_link|escape:'html':'UTF-8'}" method="get" class="pagination">
		<p>
			<input type="submit" class="button_mini" value="{l s='OK'  mod='allinone_rewards'}" />
			<label for="nb_item">{l s='items:' mod='allinone_rewards'}</label>
			<select name="n" id="nb_item">
			{foreach from=$nArray item=nValue}
				{if $nValue <= $rewards|@count}
				<option value="{$nValue|escape:'htmlall':'UTF-8'}" {if $nbpagination == $nValue}selected="selected"{/if}>{$nValue|escape:'htmlall':'UTF-8'}</option>
				{/if}
			{/foreach}
			</select>
			<input type="hidden" name="p" value="1" />
		</p>
	</form>
		{/if}
</div>
	{/if}

	{if $voucher_minimum_allowed}
<div id="min_transform" style="clear: both">{l s='The minimum required to be able to transform your rewards into vouchers is' mod='allinone_rewards'} <b>{$voucherMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}
	{if $payment_minimum_allowed}
<div id="min_payment" style="clear: both">{l s='The minimum required to be able to ask for a payment is' mod='allinone_rewards'} <b>{$paymentMinimum|escape:'html':'UTF-8'}</b></div>
	{/if}

	{if $voucher_button_allowed}
<div id="transform" style="clear: both">
	<a href="{$pagination_link|escape:'htmlall':'UTF-8'}transform-credits=true" onclick="return confirm('{l s='Are you sure you want to transform your rewards into vouchers ?' mod='allinone_rewards' js=1}');">{l s='Transform my rewards into a voucher worth' mod='allinone_rewards'} <span>{$totalAvailableCurrency}</span></a>
</div>
	{/if}
	{if $payment_button_allowed}
<div id="payment" style="clear: both">
	<a onClick="$('#payment_form').toggle()">{l s='Ask for the payment of your available rewards :' mod='allinone_rewards'} <span>{displayPrice price=$totalForPaymentDefaultCurrency currency=$payment_currency}</span></a>
	<form id="payment_form" class="std" method="post" action="{$pagination_link|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data" style="display: {if isset($smarty.post.payment_details)}block{else}none{/if}">
		<fieldset>
			<div id="payment_txt">{$payment_txt|escape:'string':'UTF-8'}</div>
			<p class="required textarea">
				<label for="payment_details">{l s='Bank account, paypal address, address, details...' mod='allinone_rewards'} <sup>*</sup></label>
				<textarea id="payment_details" name="payment_details" rows="3" cols="40">{if isset($payment_details)}{$payment_details|escape:'html':'UTF-8'}{/if}</textarea>
			</p>
			<p class="{if $payment_invoice}required{/if} text">
				<label for="payment_invoice">{l s='Invoice' mod='allinone_rewards'} ({displayPrice price=$totalForPaymentDefaultCurrency currency=$payment_currency}) {if $payment_invoice}<sup>*</sup>{/if}</label>
				<input id="payment_invoice" name="payment_invoice" type="file">
			</p>
			<input class="button" type="submit" value="{l s='Save' mod='allinone_rewards'}" name="submitPayment" id="submitPayment">
			<p class="required"><sup>*</sup>{l s='Required field' mod='allinone_rewards'}</p>
		</fieldset>
	</form>
</div>
	{/if}
{/if}
        <table class="std">
            <h2 class="tituloNet">{l s="Top Network Performers" mod='allinone_rewards'}</h2>
		<thead>
			<tr>
				<th class="first_item">{l s='NAME' mod='allinone_rewards'}</th>
				<th class="item">{l s='PURCHASE' mod='allinone_rewards'}</th>
                                <th class="first_item">{l s='POINTS' mod='allinone_rewards'}</th>
                                <th class="item">{l s='TIME' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
	{foreach from=$topNetwork item=topNet name=myLoop}
			<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                            <td align="right"><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$topNet.name|escape:'html':'UTF-8'}&nbsp;&nbsp;{$topNet.lastname|escape:'html':'UTF-8'}</td>
                                <td><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$topNet.purchase|escape:'htmlall':'UTF-8'}</td>
                                <td align="right" style="padding-top:17px !important;">{$topNet.points|number_format:0}</td>
                                <td style="padding-top:17px !important;">{dateFormat date=$topNet.time full=1}</td>
				
			</tr>
	{/foreach}
		</tbody>
	</table>
        <table class="std">
            <h2 class="tituloNet">{l s="Worst Network Performers" mod='allinone_rewards'}</h2>
		<thead>
			<tr>
				<th class="first_item">{l s='NAME' mod='allinone_rewards'}</th>
				<th class="item">{l s='PURCHASE' mod='allinone_rewards'}</th>
                                <th class="first_item">{l s='POINTS' mod='allinone_rewards'}</th>
                                <th class="item">{l s='TIME' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
	{foreach from=$topWorst item=worst name=myLoop}
			<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
				<td align="right"><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$worst.name|escape:'html':'UTF-8'}&nbsp;&nbsp;{$topNet.lastname|escape:'html':'UTF-8'}</td>
                                <td><img src="{$img_dir}icon/points.png" style="height:50%; width: auto; margin-right: 3%;"/>{$worst.purchase|escape:'htmlall':'UTF-8'}</td>
                                <td align="right" style="padding-top:17px !important;">{$worst.points|number_format:0}</td>
                                <td style="padding-top:17px !important;">{dateFormat date=$worst.time full=1}</td>
			</tr>
	{/foreach}
		</tbody>
	</table>
        <div id="idTab4" class="sponsorshipBlock">
            {if $multilevel && $statistics.sponsored1}
            <div class="title">{l s='Details by sponsorship level' mod='allinone_rewards'}</div>
            <table class="std">
                    <thead>
                            <tr>
                                    <th class="first_item left">{l s='Level' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Friends' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Orders' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Rewards' mod='allinone_rewards'}</th>
                            </tr>
                    </thead>
                    <tbody>
                            {section name=levels start=0 loop=$statistics.maxlevel step=1}
                                    {assign var="indiceFriends" value="nb`$smarty.section.levels.iteration`"}
                                    {assign var="indiceOrders" value="nb_orders`$smarty.section.levels.iteration`"}
                                    {assign var="indiceRewards" value="rewards`$smarty.section.levels.iteration`"}
                            <tr>
                                    <td class="left">{l s='Level' mod='allinone_rewards'} {$smarty.section.levels.iteration|escape:'html':'UTF-8'}</td>
                                    <td class="center">{if isset($statistics[$indiceFriends])}{$statistics[$indiceFriends]|intval}{else}0{/if}</td>
                                    <td class="center">{if isset($statistics[$indiceOrders])}{$statistics[$indiceOrders]|intval}{else}0{/if}</td>
                                    <td class="right">{$statistics[$indiceRewards]|escape:'html':'UTF-8'}</td>
                            </tr>
                            {/section}
                            <tr class="total" style="color:#ef4136;">
                                    <td class="left">{l s='Total' mod='allinone_rewards'}</td>
                                    <td class="center">{$statistics.direct_nb1+$statistics.direct_nb2+$statistics.direct_nb3+$statistics.direct_nb4+$statistics.direct_nb5+$statistics.indirect_nb|intval}</td>
                                    <td class="center">{$statistics.nb_orders_channel1+$statistics.nb_orders_channel2+$statistics.nb_orders_channel3+$statistics.nb_orders_channel4+$statistics.nb_orders_channel5+$statistics.indirect_nb_orders|intval}</td>
                                    <td class="right">{$statistics.total_global|escape:'html':'UTF-8'}</td>
                            </tr>
                    </tbody>
            </table>
                    {/if}

                    {if $statistics.sponsored1}
            <div class="title">{l s='Details for my direct friends' mod='allinone_rewards'}</div>
            <table class="std">
                    <thead>
                            <tr>
                                    <th class="first_item left">{l s='Name' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Orders' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Rewards' mod='allinone_rewards'}</th>
                            {if $multilevel}
                                    <th class="item center">{l s='Friends' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Friends\' orders' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Rewards' mod='allinone_rewards'}</th>
                                    <th class="item center">{l s='Total' mod='allinone_rewards'}</th>
                            {/if}
                            </tr>
                    </thead>
                    <tbody>
                            {foreach from=$statistics.sponsored1 item=sponsored name=myLoop}
                                    {assign var="indiceDirect" value="direct_customer`$sponsored.id_customer`"}
                                    {assign var="indiceIndirect" value="indirect_customer`$sponsored.id_customer`"}
                                    {if isset($statistics[$indiceDirect])}
                                            {assign var="valueDirect" value=$statistics[$indiceDirect]}
                                    {else}
                                            {assign var="valueDirect" value=0}
                                    {/if}
                                    {if isset($statistics[$indiceIndirect])}
                                            {assign var="valueIndirect" value=$statistics[$indiceIndirect]}
                                    {else}
                                            {assign var="valueIndirect" value=0}
                                    {/if}
                            <tr>
                                    <td class="left">{$sponsored.lastname|escape:'html':'UTF-8'} {$sponsored.firstname|escape:'html':'UTF-8'}</td>
                                    <td class="center">{$sponsored.direct_orders|intval}</td>
                                    <td class="right">{$sponsored.direct|escape:'html':'UTF-8'}</td>
                                    {if $multilevel}
                                    <td class="center">{$valueDirect+$valueIndirect|intval}</td>
                                    <td class="center">{$sponsored.indirect_orders|intval}</td>
                                    <td class="right">{$sponsored.indirect|escape:'html':'UTF-8'}</td>
                                    <td class="total right">{$sponsored.total|escape:'html':'UTF-8'}</td>
                                    {/if}
                            </tr>
                            {/foreach}
                            <tr class="total" style="color:#ef4136;">
                                    <td class="left">{l s='Total' mod='allinone_rewards'}</td>
                                    <td class="center">{$statistics.total_direct_orders|intval}</td>
                                    <td class="right">{$statistics.total_direct_rewards|escape:'html':'UTF-8'}</td>
                                    {if $multilevel}
                                    <td class="center">{$statistics.indirect_nb|intval}</td>
                                    <td class="center">{$statistics.total_indirect_orders|intval}</td>
                                    <td class="right">{$statistics.total_indirect_rewards|escape:'html':'UTF-8'}</td>
                                    <td class="right">{$statistics.total_global|escape:'html':'UTF-8'}</td>
                                    {/if}
                            </tr>
                    </tbody>
            </table>
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
        .breadcrumb{display: none !important;}
        #center_column{min-width: 100% !important; margin: 0px;}
        #columns{margin-bottom: 0px !important; min-width: 100%;}
        .banner-home{margin: 0px;}
        .footer_links{display: none;}
        #transform {display: none;}
        #min_payment{display: none;}
        .rewards{width: 80%; margin: 0 auto;}
        .page-heading{display: none;}
        #payment{display:none;}
        .rewards table.std td { font-size: 11px; line-height: 13px; padding: 10px !important; background:#f9f9f9; border: #fff 5px solid; border-right:none; border-left:none;}
    </style>
{/literal}

{literal}
    <script>
    $(function () {
        series = {/literal}[{foreach from=$arraySeries item=foo}'{$foo}',{/foreach}]
        {literal}
        columns = {/literal}[{foreach from=$arrayGraph item=foo}{$foo},{/foreach}]
            {literal}

        $('#container').highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
                text: '{/literal}{l s='Network trend' mod='allinone_rewards'}{literal}'
            },
            subtitle: {
                text: 'Fluz Fluz',
            },
            xAxis: {
                categories: series,
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 1,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                        
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 2
                        }
                    },
                    threshold: null
                }
            },

            series: [{
                type: 'area',
                name: 'Points',
                data: columns
            }]
        });
    //});
});
    </script>
{/literal}
<!-- END : MODULE allinone_rewards -->