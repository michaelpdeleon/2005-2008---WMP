{* $Id: language_selector.tpl,v 1.5 2006/03/29 13:55:19 max Exp $ *}
{if $all_languages_cnt > 1}
<table cellspacing="0" cellpadding="0" border="0" width="100%">
<tr>
    <td colspan="3" align="right">
    <table cellspacing="1" cellpadding="2" border="0">
    <tr>
        <td>{$lng.lbl_language}:</td>
        <td>{include file="main/language_selector_short.tpl"}</td>
    </tr>
    </table>
    </td>
</tr>
</table>
{/if}
