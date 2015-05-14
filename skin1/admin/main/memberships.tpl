{include file="page_title.tpl" title=$lng.lbl_edit_membership_levels}

{$lng.txt_edit_membership_levels_top_text}

<br /><br />

{if $active_modules.Simple_Mode}

{$lng.txt_edit_membership_levels_simple_mode_text}

{else}

{$lng.txt_edit_membership_levels_non_simple_mode_text}

{/if}

<br /><br />
{include file="main/language_selector.tpl" script="memberships.php?"}
<br />
{foreach from=$memberships key=type item=v}
{include file="admin/main/edit_memberships.tpl" type=$type levels=$v title=$memberships_lbls.$type}
<br />
{/foreach}
