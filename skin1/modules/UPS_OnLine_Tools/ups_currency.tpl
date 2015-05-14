{* $Id: ups_currency.tpl,v 1.3 2005/11/17 06:55:58 max Exp $ *}
<option value="AUD"{if $selected eq "AUD"} selected="selected"{/if}>Australia, AUD</option>
<option value="CAD"{if $selected eq "CAD"} selected="selected"{/if}>Canada, CAD</option>
<option value="GBP"{if $selected eq "GBP"} selected="selected"{/if}>United Kingdom, GBP</option>
<option value="USD"{if $selected eq "USD" or $selected eq ""} selected="selected"{/if}>United States, USD</option>
