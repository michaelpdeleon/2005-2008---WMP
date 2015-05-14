{* $Id: fedex.tpl,v 1.7.2.2 2006/07/11 08:39:32 svowl Exp $ *}
<form name="tracking" action="http://www.fedex.com/cgi-bin/tracking">
<input type="hidden" name="action" value="track" />
<input type="hidden" name="language" value="english" />
<input type="hidden" name="initial" value="x" />
<input type="hidden" name="tracknumbers" value="{$order.tracking}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_fedex_redirection}
</form>
