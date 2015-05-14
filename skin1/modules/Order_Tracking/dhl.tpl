{* $Id: dhl.tpl,v 1.3.2.2 2006/07/11 08:39:32 svowl Exp $ *}
<form action="http://track.dhl-usa.com/TrackByNbr.asp?nav=TrackBynumber" method="post" name="getTrackNum" id="getTrackNum">
<input type="hidden" id="txtTrackNbrs" name="txtTrackNbrs" value="{$order.tracking}" />
<input type="hidden" name="hdnErrorMsg" value="" />
<input type="hidden" name="hdnTrackMode" value="nbr" />
<input type="hidden" name="hdnPostType" value="init" />
<input type="hidden" name="hdnRefPage" value="0" />
<input type="submit" value="{$lng.lbl_track_it|strip_tags:false|escape}" />
<br />
{$lng.txt_dhl_redirection}
</form>
