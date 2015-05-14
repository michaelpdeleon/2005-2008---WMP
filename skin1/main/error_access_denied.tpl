{* $Id: error_access_denied.tpl,v 1.9 2005/11/17 06:55:39 max Exp $ *}
<!-- Start addition by Michael de Leon 12.04.06 -->
{php}
	func_header_location("page_not_found.php");
{/php}
<!-- End addition by Michael de Leon 12.04.06 -->

<h3>{$lng.err_access_denied}</h3>
{$lng.err_access_denied_msg}
{if $id ne ''}
<br /><br />
<b>{$lng.lbl_error_id}:</b> {$id}
{/if}
