{if isset($titlefont) && $titlefont} {$titlefont|html_entity_decode} {/if}
{if isset($linkfont) && $linkfont} {$linkfont|html_entity_decode} {/if}
<style type="text/css">
	{* FONT STYLE *}
		{if isset($titlefont) && $titlefont}
			h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, a{ldelim}
			font-family:{$fontnametitle};
			{rdelim} 
			.titleFont{ldelim}
			font-family:{$fontnametitle} !important;
			{rdelim}
		{/if}
		{if isset($linkfont) && $linkfont}
			body, .footer-container #footer a, .footer-container #footer ul li{ldelim}
			font-family:{$fontnamelink};
			{rdelim}
			.linkFont{ldelim}
			font-family:{$fontnamelink} !important;
			{rdelim}
		{/if}
	{* END FONT STYLE *}
	{* COLOR OPTION 
		{if isset($textcolor) && $textcolor}
			body{ldelim} color:{$textcolor}; {rdelim}
		{/if}
		{if isset($linkcolor) && $linkcolor}
			a{ldelim} color:{$linkcolor}; {rdelim}
		{/if}
		{if isset($linkcolorhover) && $linkcolorhover}
			a:hover, a:focus{ldelim} color:{$linkcolorhover}; {rdelim}
		{/if}
	 END COLOR OPTION *}
</style>