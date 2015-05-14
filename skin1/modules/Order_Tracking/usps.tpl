{* $Id: usps.tpl,v 1.6.2.3 2006/07/11 08:39:32 svowl Exp $ *}
<form action="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do" method="post" name="getTrackNum" id="getTrackNum">
<input type="hidden" id="strOrigTrackNum" name="strOrigTrackNum" value="{$order.tracking}" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_usps_redirection}
</form>
