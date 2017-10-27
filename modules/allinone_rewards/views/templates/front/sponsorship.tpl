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
<script type="text/javascript">
//<![CDATA[
	var msg = "{l s='You must agree to the terms of service before continuing.' mod='allinone_rewards'}";
	var url_allinone_sponsorship="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}";
//]]>
</script>

{assign var="sback" value="0"}
{if isset($popup)}
	{assign var="sback" value="1"}
{/if}

<div id="rewards_sponsorship" class="rewards">
	{if !isset($popup)}
		{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='Sponsorship program' mod='allinone_rewards'}{/capture}

		{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
	<h1 class="page-heading">{l s='Sponsorship program' mod='allinone_rewards'}</h1>
		{else}
                    {include file="$tpl_dir./breadcrumb.tpl"}
                    <h2>{l s='Sponsorship program' mod='allinone_rewards'}</h2>
		{/if}
	{/if}

	{if $error}
	<p class="error">
		{if $error == 'email invalid'}
			{l s='At least one email address is invalid!' mod='allinone_rewards'}
		{elseif $error == 'name invalid'}
			{l s='At least one first name or last name is invalid!' mod='allinone_rewards'}
		{elseif $error == 'email exists'}
			{l s='Someone with this email address has already been sponsored' mod='allinone_rewards'}: {foreach from=$mails_exists item=mail}{$mail|escape:'html':'UTF-8'} {/foreach}<br>
		{elseif $error == 'no revive checked'}
			{l s='Please mark at least one checkbox' mod='allinone_rewards'}
		{elseif $error == 'bad phone'}
			{l s='The mobile phone is invalid' mod='allinone_rewards'}
		{elseif $error == 'sms already sent'}
			{l s='This mobile phone has already been invited during last 10 days, please retry later.' mod='allinone_rewards'}
		{elseif $error == 'sms impossible'}
			{l s='An error occured, the SMS has not been sent' mod='allinone_rewards'}
		{elseif $error == 'purchase incomplete'}
			{l s='Por favor verifica el estado de tu afiliacion, tu proceso de registro esta incompleto. Si tienes una invitacion por favor realiza el proceso de registro nuevamente.'}
		{elseif $error == 'no sponsor'}
			{l s='No hay espacios disponibles en la red.'}
		{/if}
	</p>
	{/if}
        
        {if $invitation_sent}
            <input type="hidden" value="{$urlWhatsapp}" id="urlWhatsapp"/>
            <script>
                var urlWhatsapp = $("#urlWhatsapp").val();
                if ( urlWhatsapp != "" ) {
                    window.open(urlWhatsapp, "_blank");
                }
            </script>
        {/if}

	{if ($invitation_sent||$sms_sent) && isset($popup)}
	<p class="popup">
		{if $sms_sent}
		{l s='A SMS has been sent to your friend!' mod='allinone_rewards'}
		{else if $nbInvitation > 1}
		{l s='Emails have been sent to your friends!' mod='allinone_rewards'}
		{else}
		{l s='An email has been sent to your friend!' mod='allinone_rewards'}
		{/if}
	</p>
	{else}
		{if $invitation_sent||$sms_sent}
	<p class="success">
			{if $sms_sent}
		{l s='A SMS has been sent to your friend!' mod='allinone_rewards'}
			{else if $nbInvitation > 1}
		{l s='Emails have been sent to your friends!' mod='allinone_rewards'}
			{else}
		{l s='An email has been sent to your friend!' mod='allinone_rewards'}
			{/if}
	</p>
		{/if}

		{if !isset($popup) && $revive_sent}
	<p class="success">
			{if $nbRevive > 1}
		{l s='Reminder emails have been sent to your friends!' mod='allinone_rewards'}
			{else}
		{l s='A reminder email has been sent to your friend!' mod='allinone_rewards'}
			{/if}
	</p>
		{/if}

		{if !isset($popup)}
	<ul class="idTabs">
		<li><a href="#idTab1" {if $activeTab eq 'sponsor'}class="selected"{/if}>{l s='Sponsor my friends' mod='allinone_rewards'}</a></li>
		<!--<li><a href="#idTab2" {if $activeTab eq 'pending'}class="selected"{/if}>{l s='Pending friends' mod='allinone_rewards'}</a></li>-->
		<!--<li><a href="#idTab3" {if $activeTab eq 'subscribed'}class="selected"{/if}>{l s='Friends I sponsored' mod='allinone_rewards'}</a></li>-->
			{if $reward_order_allowed || $reward_registration_allowed}
                <!--<li><a href="#idTab4" {if $activeTab eq 'statistics'}class="selected"{/if}>{l s='Statistics' mod='allinone_rewards'}</a></li>-->
			{/if}
	</ul>
	<div class="sheets">
		<div id="idTab1" class="sponsorshipBlock">
		{else}
                    <div class="sponsorshipBlock sponsorshipPopup">
		{/if}

		{*if isset($text)}
                    <div id="sponsorship_text" {if isset($popup) && $afterSubmit}style="display: none"{/if}>
                        {$text|escape:'string':'UTF-8'}
                        {if isset($popup)}
                        <div align="center">
                            <input id="invite" type="button" class="button" value="{l s='Invite my friends' mod='allinone_rewards'}" />
                            <input id="noinvite" type="button" class="button" value="{l s='No, thanks' mod='allinone_rewards'}" />
                        </div>
                        {/if}
                    </div>
		{/if*}

		{if $canSendInvitations || isset($popup)}
			<div id="sponsorship_form"  {if isset($popup) && !$afterSubmit}style="display: none"{/if}>
				<!--<div>
				{*l s='Sponsorship is quick and easy. You can invite your friends in different ways :' mod='allinone_rewards'*}
				<ul>
					<li>{l s='Propose your sponsorship on the social networks, by clicking the following links' mod='allinone_rewards'}<br>
						&nbsp;<a href="http://www.facebook.com/sharer.php?u={$link_sponsorship_fb|escape:'html':'UTF-8'}" target="_blank" title="{l s='Facebook' mod='allinone_rewards'}"><img src='{$rewards_path|escape:'html':'UTF-8'}img/facebook.png' height='20'></a>
						&nbsp;<a href="http://twitter.com/share?url={$link_sponsorship_twitter|escape:'html':'UTF-8'}" target="_blank" title="{l s='Twitter' mod='allinone_rewards'}"><img src='{$rewards_path|escape:'html':'UTF-8'}img/twitter.png' height='20'></a>
						&nbsp;<a href="https://plus.google.com/share?url={$link_sponsorship_google|escape:'html':'UTF-8'}" target="_blank" title="{l s='Google+' mod='allinone_rewards'}"><img src="{$rewards_path|escape:'html':'UTF-8'}img/google.png"></a>
					</li>
					<li>{l s='Give this sponsorship link to your friends, or post it on internet (forums, blog...)' mod='allinone_rewards'}<br>{$link_sponsorship|escape:'htmlall':'UTF-8'}</li>
					
                                        <li>{l s='Give them your mail' mod='allinone_rewards'} <b>{$email|escape:'html':'UTF-8'}</b> {l s='or your sponsor code' mod='allinone_rewards'} <b>{$code|escape:'html':'UTF-8'}</b> {l s='to enter in the registration form.' mod='allinone_rewards'}</li>
			{if $sms}
					<li>
						<form id="sms_form" method="post" action="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" style="display: inline">{l s='Enter their mobile phone (international format) and send them a SMS' mod='allinone_rewards'} <input id="phone" name="phone" maxlength="16" type="text" placeholder="{l s='e.g. +33612345678' mod='allinone_rewards'}" />
							<input type="image" src="{$base_dir_ssl|escape:'html':'UTF-8'}modules/allinone_rewards/img/sendsms.gif" id="submitSponsorSMS" name="submitSponsorSMS" alt="{l s='Send SMS' mod='allinone_rewards'}" title="{l s='Send SMS' mod='allinone_rewards'}" align="absmiddle" />
						</form>
					</li>
			{/if}
					<li>{l s='Fill in the following form and they will receive an mail.' mod='allinone_rewards'}</li>
				</ul>
				</div>-->
				<div>
					<form id="list_contacts_form" method="post" action="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}">
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <p class="title-sponsor">{l s='Add a friend' mod='allinone_rewards'}<span>{l s='(maximum of two invites per sponsor)' mod='allinone_rewards'}</span></p>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                                    <input type="checkbox" name="autoaddnetwork" id="autoaddnetwork" value="1" {if isset($autoaddnetwork) && $autoaddnetwork == 1} checked="checked"{/if}/>
                                                    <label for="autoaddnetwork" style="vertical-align: sub;">
                                                        Impedir que nuevos usuarios se agreguen autom&aacute;ticamente a mi network
                                                    </label>
                                                </div>
                                            </div>
                                                <br/><br/>
						<!--<textarea name="message" class="text">{if isset($message)}{$message|escape:'html':'UTF-8'}{/if}</textarea>-->
                                                        
                                                        {if $subscribeFriends|@count == 0 AND $pendingFriends|@count == 0}
                                                                
                                                            {section name=friends start=0 loop=$nbFriends step=1}
                                                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">           
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsFirstName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsFirstName[$smarty.section.friends.index])}{$friendsFirstName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                                </div>
                                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsLastName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsLastName[$smarty.section.friends.index])}{$friendsLastName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                                </div>    
                                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsEmail[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsEmail[$smarty.section.friends.index])}{$friendsEmail[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                                </div>
                                                            {/section}
                                                        {elseif $subscribeFriends|@count == 1 AND $pendingFriends|@count == 1}
                                                            <div style="color:#ef4136;">{l s="Seleccione a su amigo en estado pendiente para reenviar la invitacion."}</div><br>
                                                            <div class="row">
                                                                 <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                 <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 item t-sponsor second-item">{l s='Last name' mod='allinone_rewards'}</div>
                                                                 <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                             </div>
                                                            {foreach from=$pendingFriends item=pendingFriend name=myLoop}
                                                                <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email" style="line-height: 25px;">
                                                                        <input type="checkbox" name="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" id="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" value="1" />
                                                                        {$pendingFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$pendingFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$pendingFriend.email|escape:'html':'UTF-8'}
                                                                    </div>
                                                                    <div style="color:#eabf1e; line-height: 25px; text-align: center;" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email">{l s="Estado: Pendiente"}</div>
                                                                </div>
                                                            {/foreach}
                                                            {foreach from=$subscribeFriends item=subscribeFriend name=myLoop}
                                                                <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email"><img src="{$img_dir}icon/points.png" style="width: 30px; margin-right: 3%;"/>{$subscribeFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$subscribeFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$subscribeFriend.email|escape:'html':'UTF-8'}</div>
                                                                    <div style="color:#22b573; line-height: 25px;height: 53px;text-align:center;" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email">{l s="Estado: confirmado"}</div>
                                                                </div>
                                                            {/foreach}    
                                                        {elseif $pendingFriends|@count == 2}
                                                            <div style="color:#ef4136;">{l s="Seleccione a sus amigos en estado pendiente para reenviar la invitacion."}</div>
                                                            <div class="row">
                                                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 item second-item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                            </div>
                                                            {foreach from=$pendingFriends item=pendingFriend name=myLoop}
                                                                <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email" style="line-height: 25px;">
                                                                        <input type="checkbox" name="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" id="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" value="1" />
                                                                        {$pendingFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$pendingFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$pendingFriend.email|escape:'html':'UTF-8'}
                                                                    </div>
                                                                    <div style="color:#eabf1e; line-height: 25px; text-align: center;" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email">{l s="Estado: Pendiente"}</div>
                                                                </div>
                                                            {/foreach}
                                                        {elseif $pendingFriends|@count == 1} 
                                                                <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">           
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsFirstName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsFirstName[$smarty.section.friends.index])}{$friendsFirstName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" onfocus="focusFunction()" onblur="blurFunction()"/></div>
                                                                </div>
								<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsLastName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsLastName[$smarty.section.friends.index])}{$friendsLastName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" onfocus="focusFunction()" onblur="blurFunction()"/></div>
                                                                </div>    
                                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsEmail[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsEmail[$smarty.section.friends.index])}{$friendsEmail[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" onfocus="focusFunction()" onblur="blurFunction()"/></div>
                                                                </div>
                                                                {foreach from=$pendingFriends item=pendingFriend name=myLoop}
                                                                    <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 status-email" style="line-height: 25px;">
                                                                            <input type="checkbox" class="abc" name="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" id="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" value="1" />
                                                                            {$pendingFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$pendingFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$pendingFriend.email|escape:'html':'UTF-8'}
                                                                        </div>
                                                                        <div style="color:#eabf1e; line-height: 25px; text-align: center;" class="col-lg-6 col-md-6 col-sm-6 status-email">{l s="Estado: Pendiente"}</div>
                                                                    </div>
                                                                {/foreach}
                                                            {elseif $subscribeFriends|@count == 1}
                                                            <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">           
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsFirstName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsFirstName[$smarty.section.friends.index])}{$friendsFirstName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                            </div>
                                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsLastName[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsLastName[$smarty.section.friends.index])}{$friendsLastName[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                            </div>    
                                                            <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
								<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsEmail[{$smarty.section.friends.index|escape:'html':'UTF-8'}]" size="20" value="{if isset($friendsEmail[$smarty.section.friends.index])}{$friendsEmail[$smarty.section.friends.index]|escape:'html':'UTF-8'}{/if}" /></div>
                                                            </div>    
                                                            {foreach from=$subscribeFriends item=subscribeFriend name=myLoop}
                                                            <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                <div class="col-lg-6 col-md-6 col-sm-6 status-email"><img src="{$img_dir}icon/points.png" style="width: 30px; margin-right: 3%;"/>{$subscribeFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$subscribeFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$subscribeFriend.email|escape:'html':'UTF-8'}</div>
                                                                <div style="color:#22b573; line-height: 25px;height: 53px;text-align:center;" class="col-lg-6 col-md-6 col-sm-6 status-email">{l s="Estado: confirmado"}</div>
                                                            </div>
                                                            {/foreach}    
                                                            
                                                            {elseif $subscribeFriends|@count == 2}
                                                                    <div style="color:#ef4136;">{l s="Tus Amigos Confirmados."}</div>
                                                                    <div class="row">
                                                                        <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 item second-item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                                                                        <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                                                                    </div>
                                                                    {foreach from=$subscribeFriends item=subscribeFriend name=myLoop}
                                                                        <div class="row {if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
                                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email"><img src="{$img_dir}icon/points.png" style="width: 30px; margin-right: 3%;"/>{$subscribeFriend.firstname|escape:'html':'UTF-8'}&nbsp;&nbsp;&nbsp;{$subscribeFriend.lastname|escape:'html':'UTF-8'}&nbsp;&nbsp;-&nbsp;&nbsp;{$subscribeFriend.email|escape:'html':'UTF-8'}</div>
                                                                            <div style="color:#22b573; line-height: 25px;height: 53px;text-align:center;" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email">{l s="Estado: confirmado"}</div>
                                                                        </div>
                                                                    {/foreach}
                                                                   {literal}
                                                                        <style>
                                                                            #submitSponsorFriends{display: none;}
                                                                            .checkbox{display: none;}
                                                                        </style>
                                                                   {/literal}
                                                            {/if}
                                                    <div class="row">
                                                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" title="Funcion habilitada para realizar una invitacion a la vez">
                                                            <input class="cgv" type="checkbox" name="inviteWhatsapp" id="inviteWhatsapp" />&nbsp;
                                                            <label style="color: #777777;line-height: 30px;font-weight: normal;" for="inviteWhatsapp">Enviar invitaci&oacute;n tambi&eacute;n v&iacute;a Whatsapp</label>&nbsp;
                                                            <i class="icon icon-whatsapp" style="color: #189D0E;"></i>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 blockPhoneInviteWhatsapp">
                                                            <select name="countryPhoneInviteWhatsapp" id="countryPhoneInviteWhatsapp" style="background: #f9f9f9; height: 25px!important;">
                                                                <option value="57">COL (+57)</option>
                                                                <option disabled>──────────</option>
                                                            </select>
                                                            <input type="number" class="text" placeholder="Ej: 3001234567" name="phoneInviteWhatsapp" id="phoneInviteWhatsapp" size="20" value="" style="padding-left: 10px; height: 25px!important; background-color: #f9f9f9; border: 1px solid lightgray;" />
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
                                                        <p class="bold"><span style="color:#ef4136;">{l s="Important: " mod='allinone_rewards'}</span>{l s='Data provided for any action outside the intended shall not be used.' mod='allinone_rewards'}</p>
                                                        <p class="checkbox">
                                                            <input class="cgv" type="checkbox" name="conditionsValided" id="conditionsValided" value="1" {if isset($smarty.post.conditionsValided) AND $smarty.post.conditionsValided eq 1}checked="checked"{/if} />&nbsp;
                                                            <label for="conditionsValided">{l s='I agree to the terms of service and adhere to them unconditionally.' mod='allinone_rewards'}</label>
                                                            <a href="http://reglas.fluzfluz.co/terminos-y-condiciones/{*$link->getModuleLink('allinone_rewards', 'rules', ['sback' => $sback], true)|escape:'html':'UTF-8'*}" title="{l s='Conditions of the sponsorship program' mod='allinone_rewards'}" target="_blank">{l s='Read conditions' mod='allinone_rewards'}</a>
                                                        </p>
                                                        <p>{l s='Preview' mod='allinone_rewards'} <a href="#emailcontent" style="color:#ef4136; text-decoration: none;" class="mail-invited myfancybox" title="{l s='Invitation email' mod='allinone_rewards'}">{l s='the default email' mod='allinone_rewards'}</a> {l s='that will be sent to your friends.' mod='allinone_rewards'}</p>
                                                        <!--<p>{l s='Preview' mod='allinone_rewards'} <a href="{$link->getModuleLink('allinone_rewards', 'email', ['sback' => $sback], true)|escape:'html':'UTF-8'}" style="color:#ef4136; text-decoration: none;" class="fancybox mail" title="{l s='Invitation email' mod='allinone_rewards'}">{l s='the default email' mod='allinone_rewards'}</a> {l s='that will be sent to your friends.' mod='allinone_rewards'}</p>-->
                                                    </div>
                                                    {if $subscribeFriends|@count == 0 AND $pendingFriends|@count == 0}
                                                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                        <p class="submit" align="right"><input type="submit" id="submitSponsorFriends" name="submitSponsorFriends" class="button_large" value="{l s='ADD FRIENDS' mod='allinone_rewards'}" /></p>
                                                    </div>
                                                    {elseif $pendingFriends|@count == 2}
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-btn" style="text-align:right;">
                                                           <input type="submit" value="{l s='Remind my friends' mod='allinone_rewards'}" name="revive" id="revive" class="button_large" />
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-btn" style="text-align:right;">
                                                           <input type="submit" value="{l s='Cancelar Invitacion' mod='allinone_rewards'}" name="reviveCancel" id="reviveCancel" class="button_large" />
                                                        </div>
                                                    {elseif $subscribeFriends|@count == 1 AND $pendingFriends|@count == 1}
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-btn" style="text-align:right;">
                                                           <input type="submit" value="{l s='Remind my friends' mod='allinone_rewards'}" name="revive" id="revive" class="button_large" />
                                                        </div>
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-3 text-btn" style="text-align:right;">
                                                           <input type="submit" value="{l s='Cancelar Invitacion' mod='allinone_rewards'}" name="reviveCancel" id="reviveCancel" class="button_large" />
                                                        </div>
                                                    {elseif $pendingFriends|@count == 1}
                                                        <diV class="col-xs-12 col-sm-12 col-md-4 col-lg-6">
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4">
                                                                <p class="submit" align="right"><input style="width: 180px; text-align: center;" type="submit" id="submitSponsorFriends" name="submitSponsorFriends" class="button_large" value="{l s='Invitar Fluzzers' mod='allinone_rewards'}"/></p>
                                                            </div>
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 text-btn" style="text-align:right;">
                                                               <input style="width: 180px; text-align: center;" type="submit" value="{l s='Remind my friends' mod='allinone_rewards'}" name="revive" id="revive" class="button_large" />
                                                            </div>
                                                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-4 text-btn" style="text-align:right;">
                                                               <input style="width: 180px; text-align: center;" type="submit" value="{l s='Cancelar Invitacion' mod='allinone_rewards'}" name="reviveCancel" id="reviveCancel" class="button_large" />
                                                            </div>
                                                        </div>
                                                    {elseif $subscribeFriends|@count == 1}
                                                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                                                            <p class="submit" align="right"><input type="submit" id="submitSponsorFriends" name="submitSponsorFriends" class="button_large" value="{l s='ADD FRIENDS' mod='allinone_rewards'}" /></p>
                                                        </div>    
                                                    {/if}
					</form>
				</div>
			</div>
		{else}
			<div>
				{l s='To become a sponsor, you need to have completed at least' mod='allinone_rewards'} {$orderQuantityS|escape:'html':'UTF-8'} {if $orderQuantityS > 1}{l s='orders' mod='allinone_rewards'}{else}{l s='order' mod='allinone_rewards'}{/if}.
			</div>
		{/if}
		</div>
                
                <div id="mail-invitation" style="display:none;">
                    {literal}
                        <style> 
                            .fancybox-inner{height: auto !important;}
                            
                            @media (max-width:425px){
                             .btn-invitation{width: 100% !important;}
                             .terminos{font-size: 9px;line-height: 1;}
                            }
                            
                            @media (max-width:375px){
                             .p-benef{padding-left: 0px; text-align: left;}
                             .p-title{padding-left: 30px !important; font-size: 16px !important; padding-bottom: 0px !important;}
                            }
                        </style>
                    {/literal}
                    <div id="emailcontent" class="infomail">
                        <div>
                            <div style="width:100%; background:#c6ae93; padding:7px 0;">
                                <img class="logo" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/7d5e7fc3-1279-4cbd-90e6-fcd68d88925c.png" alt="fluz fluz" style="margin:10px 0 5px 15px;"/>
                            </div>
                            <div>
                                <td>
                                    <img src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/519cd2a8-870f-46e8-8fdb-6942bf2dd5d9.gif" style="margin-top:-3px; width:100%;">
                                </td>
                             </div>
                        </div>
                        
                        <!--Welcome Text-->
                        <div>
                            <div align="center" class="titleblock" style="padding:10% 0 0 0">
                                <span class="title" style="font-weight:400;font-size:28px; line-height:40px; color:#ea4136; ">Hola <span id="invited">  </span>,</span><br/><br/>
                                    <span class="subtitle" style="font-weight:400;font-size:16px; line-height:25px; padding-top:0px;">Felicitaciones, has sido invitado <br> por {$sponsor} a unirte a Fluz Fluz! {$Expiration}</span>
                            </div>
                        </div>
                        <!--Login Info-->
                            <div>
                                <div class="table" style="width:100%">
                                    <div>
                                        <div style="padding:5% 0 5%; margin:auto; text-align:center;">
                                            <div>
                                                <div class="btn-invitation" style="width:50%; background:#ea4136; color:#fff; padding: 13px 50px; letter-spacing:2px; margin:15px auto 0 auto; text-decoration: none;">ACEPTAR INVITACI&Oacute;N</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!--Why Sign-Up-->
                        <div style="margin-bottom: 20px;">
                            <div>
                                <div style="text-align: center;">
                                    <a href="https://youtu.be/bVmfZ-Iu-UY" target="_blank"><img src="http://fluzfluz.co/img/video.png" width="80%"/></a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <p style="font-size:18px; text-align:left; padding-top:25px; padding-bottom:5px;padding-left: 75px;">
                                Beneficios de Fluz Fluz:
                                </p>
                            </div>
                        </div>
                        <table style="background:#f9f9f9; padding:5% 5%; width:85%; margin:auto;">
                            <tr >
                            <td align="center" ><img style="margin:auto;" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/f9c9369a-3ef5-4832-a306-639926d56c71.png"></td>
                            <td>
                            <h2 style="font-weight:400; padding-left:10%; font-size: 1.5em; margin-bottom: 0px;">Compra!</h2><p style="padding-left:10%; margin-top: 0px;">Encuentra los bonos de tus marcas favoritas en Fluz Fluz</p></td>
                            </tr>

                            <tr>
                            <td align="center" ><img style="margin:auto;" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/d0067167-2fe3-41cf-890b-74fe8f5b4e4c.png"></td>
                            <td>
                                <h2 style="font-weight:400; padding-left:10%; font-size: 1.5em; margin-bottom: 0px;">Ahorra!</h2><p style="padding-left:10%; margin-top: 0px;">Entre m&aacute;s compras, m&aacute;s ahorras</p></td>
                            </tr>


                            <tr>
                            <td align="center" ><img style="margin:auto;" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/4fa08124-f1c3-42f7-829b-c0f066b42121.png"></td>
                            <td>
                            <h2 style="font-weight:400; padding-left:10%; font-size: 1.5em; margin-bottom: 0px;">Invita Amigos!</h2><p style="padding-left:10%; margin-top: 0px;">Mientras m&aacute;s amigos invitas, m&aacute;s Fluz recibes por las compras de ellos.</p></td>
                            </tr>

                            <tr>
                            <td align="center" ><img style="margin:auto;" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/1f6bd74f-cdef-4c2c-92a1-d949a4162a98.png"></td>
                            <td>
                            <h2 style="font-weight:400; padding-left:10%; font-size: 1.5em; margin-bottom: 0px;">Redime!</h2><p style="padding-left:10%; margin-top: 0px;">Convierte tus Fluz acumulados en nuevos bonos o dinero en efectivo!</p></td>
                            </tr>

                            <tr>
                            <td align="center" ><img style="margin:auto;" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/31d775c2-e322-463b-8de9-0350cc386158.png"></td>
                            <td>
                            <h2 style="font-weight:400; padding-left:10%; font-size: 1.5em; margin-bottom: 0px;">Sin riesgo!</h2><p style="padding-left:10%; margin-top: 0px;">Carga tu cover consumible inicial y mantente activo con m&iacute;nimo 2 compras mensuales de cualquier marca y valor!</p></td>
                            </tr>
                            </table>
                        <div>
                            <div class="table" style="width:100%">
                                <div>
                                    <div style="padding:5% 0 5%; margin:auto; text-align:center;">
                                        <div>
                                            <a style="background:#ea4136; color:#fff; padding: 13px 50px; letter-spacing:2px; margin:15px auto 0 auto; text-decoration: none;" href="https://fluzfluz.co/es/content/6-categorias">
                                            COMPRA AHORA</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div>
                                <img src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/fcf1fce7-d477-4637-93b8-e7a52c04ed72.gif" style="margin-bottom:-7px; width:100%;">
                            </div>
                        </div>
                        <table style="width:100%; background:#c6ae93;">
                            <tr class="terminos">
                                <td style="width:25%; text-align:center; padding-top:15px; ">
                                <a href="https://fluzfluz.co/es/content/6-categorias" style="color:#fff; text-decoration: none;">
                                Comprar</a>
                                </td>
                                <td style="width:25%; text-align:center; padding-top:15px;">
                                <a href="https://fluzfluz.co/es/content/3-terminos-y-condiciones-de-uso" style="color:#fff; text-decoration: none;">
                                    Pol&iacute;tica de privacidad</a>
                                </td>
                                <td style="width:25%; text-align:center; padding-top:15px;">
                                <a href="https://fluzfluz.co/es/contactanos" style="color:#fff; text-decoration: none;">
                                Ayuda</a>
                                </td>
                                <td style="width:25%; text-align:center; padding-top:15px;">
                                <a href="https://fluzfluz.co" style="color:#fff; text-decoration: none;">
                                    Cancelar suscripci&oacute;n</a>
                                </td>
                            </tr>
                        </table>
                        <!--Social Footer-->

                        <table style="width:100%; background:#c6ae93;">
                            <tr>
                            <td class="space_footer" style="padding:10px!important">&nbsp;</td>
                            </tr>


                            <tr>
                            <td style="text-align:center; width:35%">
                            </td>

                            <td style="text-align:center;">
                            <a href="https://fluzfluz.co"><img width="75%" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/730a0207-ec61-41ea-a870-efe876b67e98.png"></a>
                            </td>
                            <td style="text-align:center;">
                            <a href="https://fluzfluz.co"><img width="75%" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/62c49072-2d47-4943-be64-178cb0531fe2.png"></a>
                            </td>
                            <td style="text-align:center;">
                            <a href="https://fluzfluz.co"><img width="75%" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/22aec251-65b7-491a-a6cc-2d03d5ee8181.png"></a>
                            </td>
                            <td style="text-align:center;">
                            <a href="https://fluzfluz.co"><img width="75%" src="https://gallery.mailchimp.com/e63d3ec47059b6abdf6a36c8f/images/24400c0c-6958-4bb2-a218-b287271b66f5.png"></a>
                            </td>

                            <td style="text-align:center; width:35%">
                            </td>
                            </tr>

                            <tr>
                            <td class="space_footer" style="padding:0px!important">&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>

		{if !isset($popup)}
		<!--<div id="idTab2" class="sponsorshipBlock">
			{if $pendingFriends AND $pendingFriends|@count > 0}
			<!--<div>
				{l s='These friends have not yet registered on this website since you sponsored them, but you can try again! To do so, mark the checkboxes of the friend(s) you want to remind, then click on the button "Remind my friends".' mod='allinone_rewards'}
			</div>
			<div>
				<form method="post" action="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}" class="std">
					<table class="std">
					<thead>
						<tr>
							<th class="first_item">&nbsp;</th>
							<th class="item">{l s='Last name' mod='allinone_rewards'}</th>
							<th class="item">{l s='First name' mod='allinone_rewards'}</th>
							<th class="item">{l s='Email' mod='allinone_rewards'}</th>
							<th class="last_item">{l s='Last invitation' mod='allinone_rewards'}</th>
						</tr>
					</thead>
					<tbody>
					{foreach from=$pendingFriends item=pendingFriend name=myLoop}
						<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
							<td>
								<input type="checkbox" name="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" id="friendChecked[{$pendingFriend.id_sponsorship|escape:'html':'UTF-8'}]" value="1" />
							</td>
							<td>{$pendingFriend.lastname|escape:'html':'UTF-8'}</td>
							<td>{$pendingFriend.firstname|escape:'html':'UTF-8'}</td>
							<td>{$pendingFriend.email|escape:'html':'UTF-8'}</td>
							<td>{dateFormat date=$pendingFriend.date_upd full=0}</td>
						</tr>
					{/foreach}
					</tbody>
					</table>
					<p class="submit" align="center">
						<input type="submit" value="{l s='Remind my friends' mod='allinone_rewards'}" name="revive" id="revive" class="button_large" />
					</p>
				</form>
			</div>
			{else}
			<!--<div>
				{l s='You have not sponsored any friends.' mod='allinone_rewards'}
			</div>
			{/if}
		</div>-->

		<div id="idTab3" class="sponsorshipBlock">
			{if $subscribeFriends AND $subscribeFriends|@count > 0}
			<div>
				{l s='Here are sponsored friends who have accepted your invitation:' mod='allinone_rewards'}
			</div>
			<div>
				<table class="std">
				<thead>
					<tr>
						<th class="first_item">&nbsp;</th>
						<th class="item">{l s='Last name' mod='allinone_rewards'}</th>
						<th class="item">{l s='First name' mod='allinone_rewards'}</th>
						<th class="item">{l s='Email' mod='allinone_rewards'}</th>
						<th class="item">{l s='Channel' mod='allinone_rewards'}</th>
						<th class="last_item">{l s='Inscription date' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$subscribeFriends item=subscribeFriend name=myLoop}
					<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
						<td>{$smarty.foreach.myLoop.iteration|escape:'html':'UTF-8'}.</td>
						<td>{$subscribeFriend.lastname|escape:'html':'UTF-8'}</td>
						<td>{$subscribeFriend.firstname|escape:'html':'UTF-8'}</td>
						<td>{$subscribeFriend.email|escape:'html':'UTF-8'}</td>
						<td>{if $subscribeFriend.channel==1}{l s='Email invitation' mod='allinone_rewards'}{elseif $subscribeFriend.channel==2}{l s='Sponsorship link' mod='allinone_rewards'}{elseif $subscribeFriend.channel==3}{l s='Facebook' mod='allinone_rewards'}{elseif $subscribeFriend.channel==4}{l s='Twitter' mod='allinone_rewards'}{elseif $subscribeFriend.channel==5}{l s='Google +1' mod='allinone_rewards'}{/if}</td>
						<td>{dateFormat date=$subscribeFriend.date_upd full=0}</td>
					</tr>
					{/foreach}
				</tbody>
				</table>
			</div>
			{else}
			<div>
				{l s='No sponsored friends have accepted your invitation yet.' mod='allinone_rewards'}
			</div>
			{/if}
		</div>
			{if $reward_order_allowed || $reward_registration_allowed}
		<!--<div id="idTab4" class="sponsorshipBlock">
			<div class="title">{l s='Details by registration channel' mod='allinone_rewards'}</div>
			<div>
				<table class="std">
					<thead>
						<tr>
							<th colspan="2" class="first_item left">{l s='Channels' mod='allinone_rewards'}</th>
							<th class="item center">{l s='Friends' mod='allinone_rewards'}</th>
							<th class="item center">{l s='Orders' mod='allinone_rewards'}</th>
							{if $reward_order_allowed}<th class="item center">{l s='Rewards for orders' mod='allinone_rewards'}</th>{/if}
							{if $reward_registration_allowed}<th class="item center">{l s='Rewards for registrations' mod='allinone_rewards'}</th>{/if}
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="left" rowspan="5">{l s='My direct friends' mod='allinone_rewards'}</td>
							<td class="left">{l s='Email invitation' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb1|intval}</td>
							<td class="center">{$statistics.nb_orders_channel1|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.direct_rewards_orders1|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.direct_rewards_registrations1|escape:'html':'UTF-8'}</td>{/if}
						</tr>
						<tr>
							<td class="left">{l s='Sponsorship link' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb2|intval}</td>
							<td class="center">{$statistics.nb_orders_channel2|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.direct_rewards_orders2|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.direct_rewards_registrations2|escape:'html':'UTF-8'}</td>{/if}
						</tr>
						<tr>
							<td class="left">{l s='Facebook' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb3|intval}</td>
							<td class="center">{$statistics.nb_orders_channel3|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.direct_rewards_orders3|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.direct_rewards_registrations3|escape:'html':'UTF-8'}</td>{/if}
						</tr>
						<tr>
							<td class="left">{l s='Twitter' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb4|intval}</td>
							<td class="center">{$statistics.nb_orders_channel4|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.direct_rewards_orders4|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.direct_rewards_registrations4|escape:'html':'UTF-8'}</td>{/if}
						</tr>
						<tr>
							<td class="left">{l s='Google +1' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb5|intval}</td>
							<td class="center">{$statistics.nb_orders_channel5|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.direct_rewards_orders5|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.direct_rewards_registrations5|escape:'html':'UTF-8'}</td>{/if}
						</tr>
				{if $multilevel}
						<tr>
							<td class="left" colspan="2">{l s='Indirect friends' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.indirect_nb|intval}</td>
							<td class="center">{$statistics.indirect_nb_orders|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.indirect_rewards|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">-</td>{/if}
						</tr>
				{/if}
						<tr class="total">
							<td class="left" colspan="2">{l s='Total' mod='allinone_rewards'}</td>
							<td class="center">{$statistics.direct_nb1+$statistics.direct_nb2+$statistics.direct_nb3+$statistics.direct_nb4+$statistics.direct_nb5+$statistics.indirect_nb|intval}</td>
							<td class="center">{$statistics.nb_orders_channel1+$statistics.nb_orders_channel2+$statistics.nb_orders_channel3+$statistics.nb_orders_channel4+$statistics.nb_orders_channel5+$statistics.indirect_nb_orders|intval}</td>
							{if $reward_order_allowed}<td class="right">{$statistics.total_orders|escape:'html':'UTF-8'}</td>{/if}
							{if $reward_registration_allowed}<td class="right">{$statistics.total_registrations|escape:'html':'UTF-8'}</td>{/if}
						</tr>
					</tbody>
				</table>
			</div>

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
					<tr class="total">
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
					<tr class="total">
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
		</div>-->
			{/if}
	</div>
		{/if}
	{/if}
</div>
<hr>
<div class="sponsorshipBlock">
    <div id="sponsorship_form"  {if isset($popup) && !$afterSubmit}style="display: none"{/if}>
        <form id="list_contacts_form_Third" method="post" action="{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'html':'UTF-8'}">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <p class="title-sponsor">Agregar m&aacute;s fluzzer</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <p class="">&iquest;Tiene m&aacute;s miembros que desea ver en su red?</p>
                    <br>
                    <p class="">Nuestra herramienta s&oacute;lo le permite invitar a dos miembros directamente a su red. Pero en el caso de que usted tiene un tercer amigo que quiere unirse, puede ayudar a obtener en su red.</p>
                    <br>
                    <p class="">Ingrese el correo electr&oacute;nico de su tercer amigo y buscaremos una invitaci&oacute;n abierta en su red. Si hay una invitaci&oacute;n abierta en su red, se insertar&aacute;n a unas pocas capas de distancia.</p>
                </div>
            </div>
            <div class="row">
                {if empty($sponsorshipThird)}
                    <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">           
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsFirstNameThird" size="20" value="" /></div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsLastNameThird" size="20" value="" /></div>
                    </div>    
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><input type="text" class="text" name="friendsEmailThird" size="20" value="" /></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4" title="Funcion habilitada para realizar una invitacion a la vez">
                            <input class="cgv" type="checkbox" name="inviteWhatsappThird" id="inviteWhatsappThird" />&nbsp;
                            <label style="color: #777777;line-height: 30px;font-weight: normal;" for="inviteWhatsappThird">Enviar invitaci&oacute;n tambi&eacute;n v&iacute;a Whatsapp</label>&nbsp;
                            <i class="icon icon-whatsapp" style="color: #189D0E;"></i>
                        </div>
                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 blockPhoneInviteWhatsappThird">
                            <select name="countryPhoneInviteWhatsappThird" id="countryPhoneInviteWhatsappThird" style="background: #f9f9f9; height: 25px!important;">
                                <option value="57">COL (+57)</option>
                                <option disabled>──────────</option>
                            </select>
                            <input type="number" class="text" placeholder="Ej: 3001234567" name="phoneInviteWhatsappThird" id="phoneInviteWhatsappThird" size="20" value="" style="padding-left: 10px; height: 25px!important; background-color: #f9f9f9; border: 1px solid lightgray;" />
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-6">
                        <p class="bold"><span style="color:#ef4136;">{l s="Important: " mod='allinone_rewards'}</span>{l s='Data provided for any action outside the intended shall not be used.' mod='allinone_rewards'}</p>
                        <p style="margin: 0px;padding: 0 !important;border: none;color: #666666;font-family: 'Open sans';line-height: 30px;font-size: 13px;">
                            <input class="cgv" type="checkbox" name="conditionsValidedThird" id="conditionsValidedThird" value="1" />&nbsp;
                            <label style="color: #777777;line-height: 30px;font-weight: normal;" for="conditionsValided">{l s='I agree to the terms of service and adhere to them unconditionally.' mod='allinone_rewards'}</label>
                            <a href="http://reglas.fluzfluz.co/terminos-y-condiciones/{*$link->getModuleLink('allinone_rewards', 'rules', ['sback' => $sback], true)|escape:'html':'UTF-8'*}" title="{l s='Conditions of the sponsorship program' mod='allinone_rewards'}" target="_blank">{l s='Read conditions' mod='allinone_rewards'}</a>
                        </p>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                        <p class="submit" align="right"><input type="submit" id="submitSponsorFriendsThird" name="submitSponsorFriendsThird" class="button_large" value="{l s='ADD FRIENDS' mod='allinone_rewards'}" /></p>
                    </div>
                {else}
                    <div class="col-lg-6 col-md-6 col-sm-4 col-xs-12">           
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='First name' mod='allinone_rewards'}</div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 item t-sponsor">{l s='Last name' mod='allinone_rewards'}</div>
                    </div>    
                    <div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 last_item t-sponsor">{l s='Email' mod='allinone_rewards'}</div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-6 status-email">
                        <img src="http://fluzfluzweb.localhost/themes/pos_minoan6/img/icon/points.png" style="width: 30px; margin-right: 3%;">{$sponsorshipThird['firstname']}&nbsp;&nbsp;&nbsp;{$sponsorshipThird['lastname']}&nbsp;&nbsp;-&nbsp;&nbsp;{$sponsorshipThird['email']}
                    </div>                    
                    {if $sponsorshipThird['id_customer'] != ""}
                        <div style="color:#22b573; line-height: 25px;height: 53px;text-align:center;" class="col-lg-6 col-md-6 col-sm-6 status-email">Estado: confirmado</div>
                    {else}
                        <div style="color:#eabf1e; line-height: 25px; text-align: center;" class="col-lg-6 col-md-6 col-sm-6 col-xs-12 status-email">{l s="Estado: Pendiente"}</div>
                    {/if}
                {/if}
            </div>
        </form>
    </div>
</div>
<br>
<br>

	{if !isset($popup)}
		{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
<ul class="footer_links clearfix">
	<li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account' mod='allinone_rewards'}</span></a></li>
	<li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl|escape:'html':'UTF-8'}{else}{$base_dir|escape:'html':'UTF-8'}{/if}"><span><i class="icon-chevron-left"></i> {l s='Home' mod='allinone_rewards'}</span></a></li>
</ul>
		{else}
<ul class="footer_links clearfix">
	<li><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'html':'UTF-8'}icon/my-account.gif" alt="" class="icon" /> {l s='Back to your account' mod='allinone_rewards'}</a></li>
	<li class="f_right"><a href="{$base_dir|escape:'html':'UTF-8'}"><img src="{$img_dir|escape:'html':'UTF-8'}icon/home.gif" alt="" class="icon" /> {l s='Home' mod='allinone_rewards'}</a></li>
</ul>
		{/if}
	{/if}
{literal}
    <style>
        .btnCash{display: none;}
        .blockPhoneInviteWhatsapp, .blockPhoneInviteWhatsappThird { display: none; }
    </style>
{/literal}
 <div id="direction" style="display:none;">{$base_dir_ssl}</div>
{literal}
    <script>
        function focusFunction() {
            $('input[type="submit"]').removeAttr('disabled','disabled');
            $('#revive').attr('disabled','disabled');
            $('#revive').addClass('deshabilitar');
            $('#reviveCancel').attr('disabled','disabled');
            $('#reviveCancel').addClass('deshabilitar');
        }
    </script>
    <script>
        $(document).ready(function(){
            $('.abc').change(function() {
                if($(this).is(":checked")) {
                    $('#submitSponsorFriends').attr('disabled','disabled');
                    $('#submitSponsorFriends').addClass('deshabilitar');
                    $('#revive').removeAttr('disabled','disabled');
                    $('#revive').removeClass('deshabilitar');
                    $('#reviveCancel').removeAttr('disabled','disabled');
                    $('#reviveCancel').removeClass('deshabilitar');
                } else {
                    $('#submitSponsorFriends').removeAttr('disabled','disabled');
                    $('#submitSponsorFriends').removeClass('deshabilitar');
                }
            });
    });
    </script>
{/literal}    
{literal}
    <script>
         $('.mail-invited').click(function(){
            var name = $("input[name='friendsFirstName[0]']").val();
            if (!name){
                name = $("input[name='friendsFirstName[]']").val();
            }
            $('#invited').html(name);
          });
    </script>
{/literal}

<script>
    var id_customer = {$id_customer};
</script>

{literal}
    <script>
        $('#autoaddnetwork').change(function() {
            var value = 0;
            if( $(this).is(":checked") ) {
                value = 1;
            }
            $.ajax({
                method:"POST",
                data: {'action': 'updateautoaddnetwork', 'id': id_customer, 'value': value},
                url: '/autoaddnetwork.php'
            });
        });

        $("#inviteWhatsapp").change(function() {
            $("#phoneInviteWhatsapp").val("");
            if( $('#inviteWhatsapp').attr('checked') ) {
                $(".blockPhoneInviteWhatsapp").css("display","block");
            } else {
                $(".blockPhoneInviteWhatsapp").css("display","none");
            }
        });
        
        $("#inviteWhatsappThird").change(function() {
            $("#phoneInviteWhatsappThird").val("");
            if( $('#inviteWhatsappThird').attr('checked') ) {
                $(".blockPhoneInviteWhatsappThird").css("display","block");
            } else {
                $(".blockPhoneInviteWhatsappThird").css("display","none");
            }
        });

        $(function() {
            $.ajax({
                method: "GET",
                data: {},
                url: 'https://restcountries.eu/rest/v2/all', 
                success:function(countries) {
                    $.each(countries, function(i, item) {
                        $("#countryPhoneInviteWhatsapp").append($('<option>', {
                            value: item.callingCodes[0],
                            text: item.alpha3Code+" (+"+item.callingCodes[0]+")"
                        }));
                        $("#countryPhoneInviteWhatsappThird").append($('<option>', {
                            value: item.callingCodes[0],
                            text: item.alpha3Code+" (+"+item.callingCodes[0]+")"
                        }));
                    });
                }
            });
        });
    </script>
{/literal}
