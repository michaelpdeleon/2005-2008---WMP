{* $Id: conditions.tpl,v 1.14 2005/11/21 12:41:58 max Exp $ *}
<p />
{capture name=dialog}
{* Place terms and conditions here *}

{if $usertype eq "B" }
{include file="help/conditions_affiliates.tpl"}
{else}
{include file="help/conditions_customers.tpl"}
{/if}

{/capture}
{include file="dialog.tpl" title=$lng.lbl_terms_n_conditions content=$smarty.capture.dialog extra='width="100%"'}
