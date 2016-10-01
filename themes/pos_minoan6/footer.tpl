{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !isset($content_only) || !$content_only}
					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
					<div class="BrandSlider">
						{capture name='BrandSlider'}{hook h='BrandSlider'}{/capture}
						{if $smarty.capture.BrandSlider}
						{$smarty.capture.BrandSlider}
						{/if}
					</div>
				</div><!-- #columns -->
			</div><!-- .columns-container -->

			{if isset($HOOK_FOOTER)}
				<!-- Footer -->
				<div class="footer-container">
					<footer id="footer">
                                            <div class="footer-static-top" style="background:#fff;">
							<div class="container2">
								<div class="row footerPrueba" style="background:#f9f9f9; margin-left:0; min-height: 300px;">
									<div class="col-xs-12 col-md-2 col-sm-4">
									
										{capture name='blockFooter1'}{hook h='blockFooter1'}{/capture}
										{if $smarty.capture.blockFooter1}
										{$smarty.capture.blockFooter1}
										{/if}
										
									
									</div>
                                                                        <div class="top-right col-xs-12 col-md-5 col-sm-8" >
                                                                                    
                                                                                {capture name='blockFooter2'}{hook h='blockFooter2'}{/capture}
										{if $smarty.capture.blockFooter2}
										{$smarty.capture.blockFooter2}
										{/if}
									</div>
                                                                        <div class="blockFooterPay col-md-5 col-sm-12">
										{capture name='blockFooter3'}{hook h='blockFooter3'}{/capture}
										{if $smarty.capture.blockFooter3}
										{$smarty.capture.blockFooter3}
										{/if}
									</div>
								</div>	
							</div>
						
						</div>
                                                <div class="footer-bottom" style="background:#efefef;">
							<div class="container">
								<div class="row">
									<div class="col-xs-12 col-md-12 col-sm-12" style="padding-right: 0px;">
									{capture name='blockFooter4'}{hook h='blockFooter4'}{/capture}
									{if $smarty.capture.blockFooter4}
									{$smarty.capture.blockFooter4}
									{/if}
									
									{capture name='posscroll'}{hook h='posscroll'}{/capture}
									{if $smarty.capture.posscroll}
									{$smarty.capture.posscroll}
									{/if}
									
								</div>	
								</div>		
							</div>
                                                                   
						</div>
					{literal}
						<!-- Start of fluzfluz Zendesk Widget script -->
						<script>/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(e){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("//assets.zendesk.com/embeddable_framework/main.js","fluzfluz.zendesk.com");/*]]>*/</script>
						<!-- End of fluzfluz Zendesk Widget script -->
					{/literal}
					</footer>
				</div><!-- #footer -->
			{/if}
                            
		</div><!-- #page -->
                
                
{/if}
{include file="$tpl_dir./global.tpl"}
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
	</body>
</html>
