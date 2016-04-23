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
<fieldset class="account_creation aior_sponsor">
	<h3 class="page-subheading">{l s='Sponsorship program' mod='allinone_rewards'}</h3>
	<p class="text form-group">
{if !isset($smarty.post.sponsorship_invisible)}
		<label for="sponsorship">{l s='Code or E-mail address of your sponsor' mod='allinone_rewards'}</label>
                <input type="text" size="52" maxlength="128" class="form-control text" id="sponsorship" name="sponsorship" placeholder="Ingrese Su sponsor" value="{if isset($smarty.post.sponsorship)}{$smarty.post.sponsorship|escape:'htmlall':'UTF-8'}{/if}" required/>
		<script type="text/javascript">
			/* cant be done in sponsorship.js, because that tpl is loaded in ajax (use live jquery function ?)*/
			// this variable is necesary because on 1.5.2 it crashs if directly in the code
			var error_sponsor = "{l s='This sponsor does not exist' mod='allinone_rewards' js=1}";
			$(document).ready(function(){
				$('#sponsorship').focus(function(){
					{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
						$('#sponsorship').parents('p').removeClass('form-error');
						$('#sponsorship').parents('p').removeClass('form-ok');
					{else}
						$('#sponsorship').removeClass('sponsorship_nok');
						$('#sponsorship_result').remove();
					{/if}
				});
				$('#sponsorship').blur(function(event){
					if (jQuery.trim($('#sponsorship').val()) != '') {
						$.ajax({
							type	: "POST",
							async	: false,
							cache	: false,
							url		: '{$link->getModuleLink('allinone_rewards', 'sponsorship', [], true)|escape:'javascript':'UTF-8'}',
							dataType: 'json',
							data 	: "popup=1&checksponsor=1&sponsorship="+$('#sponsorship').val()+"&customer_email="+$('#email').val(),
							success: function(obj)	{
								if (obj && obj.result == 1) {
									{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
										$('#sponsorship').parents('p').addClass('form-ok');
									{else}
										$('#sponsorship').after('&nbsp;<img id="sponsorship_result" src="{$module_template_dir|escape:'html':'UTF-8'}img/valid.png" align="absmiddle" class="icon" />');
									{/if}
								} else {
									{if version_compare($smarty.const._PS_VERSION_,'1.6','>=')}
										$('#sponsorship').parents('p').removeClass('form-ok');
										$('#sponsorship').parents('p').addClass('form-error');
									{else}
										$('#sponsorship').addClass('sponsorship_nok');
										$('#sponsorship').after('&nbsp;<img id="sponsorship_result" src="{$module_template_dir|escape:'html':'UTF-8'}img/invalid.png" title="'+error_sponsor+'" align="absmiddle" class="icon" />');
									{/if}
									event.stopPropagation();
								}
							}
						});
					}
				});
			});
		</script>
{else}
		<label style="width: 100%; text-align: center">{l s='You have been sponsored' mod='allinone_rewards'}</label>
{/if}
	</p>
</fieldset>
<!-- END : MODULE allinone_rewards -->