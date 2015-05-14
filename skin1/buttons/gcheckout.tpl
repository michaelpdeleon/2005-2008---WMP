{* $Id: gcheckout.tpl,v 1.1.2.1 2006/07/29 07:27:43 max Exp $ *}
<input type="image" name="{$lng.lbl_google_checkout}" alt="" src="http://checkout.google.com/buttons/checkout.gif?merchant_id={$payment_data.module_params.param01}&amp;w=160&amp;h=43&amp;style=white&amp;variant=text&amp;loc=en_US" height="43" width="160"{if $onclick} onclick="{$onclick}"{/if} />
