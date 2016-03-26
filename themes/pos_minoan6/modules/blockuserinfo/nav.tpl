<!-- Block user information module NAV  -->

<!--  	<div class="poswelcome">
			{if $is_logged}
				<p class="welcome-msg">{l s='welcome' mod='blockuserinfo'} {$cookie->customer_firstname} {$cookie->customer_lastname}  </p>
			{else}
					<p class="welcome-msg">{l s='Welcome Greentech store!' mod='blockuserinfo'} </p>
			{/if}
	</div>  -->
	
	<div class="header_userinfo">
		<div class="mobile-userinfo">
			<div class=" top-menu current">
				<span>My Account</span>
			</div>
			<ul id="header_links_vertical" class=" vertical-link header_links_ul toogle_content">
				{$context = Context::getContext()}
				<li class="first"><a class="link-myaccount" href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='My account' mod='blockuserinfo'}">
					{l s='My account' mod='blockuserinfo'}
				</a></li>
				<li><a class="link-wishlist wishlist_block" href="{$context->link->getModuleLink('blockwishlist', 'mywishlist')}" title="{l s='My wishlist' mod='blockuserinfo'}">
				{l s='My wishlist' mod='blockuserinfo'}</a></li>
				<li><a class="link-checkout" href="{$link->getPageLink('order', true)|escape:'html'}" title="{l s='checkout' mod='blockcontact'}">{l s='Checkout' mod='blockuserinfo'}</a></li>
				<li>
				{if $is_logged}
					<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
						{l s='Log out' mod='blockuserinfo'}
					</a>
				{else}
					<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">
						{l s='Log in' mod='blockuserinfo'}
					</a>
				{/if}
				</li>
			</ul>
		</div>	
	</div>

	

<!-- /Block usmodule NAV -->
