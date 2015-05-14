{* $Id: error_subscribe.tpl,v 1.12 2006/02/28 09:03:24 max Exp $ *}
{if $main eq "subscribe_exist_email"}
<font class="ErrorMessage">
{$lng.err_subscribed_already}
</font>
{elseif $main eq "subscribe_bad_email"}
<font class="ErrorMessage">
{$lng.err_subscribe_email_invalid}
</font>
{/if}
