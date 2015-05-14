{* $Id: popup_help_link.tpl,v 1.8 2005/12/07 14:07:27 max Exp $ *}
{if $config.UA.platform eq 'MacPPC' && $config.UA.browser eq 'MSIE'}
<a href="javascript: window.open('popup_info.php?action=CVV2','HELP_POPUP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" class="PopupHelpLink"><img src="{$ImagesDir}/question_button.gif" alt="{$lng.lbl_popup_help|escape}" /></a>
{else}
<a href="javascript: void(0);" onclick="javascript: window.open('popup_info.php?action=CVV2','HELP_POPUP','width=600,height=460,toolbar=no,status=no,scrollbars=yes,resizable=no,menubar=no,location=no,direction=no');" class="PopupHelpLink"><img src="{$ImagesDir}/question_button.gif" alt="{$lng.lbl_popup_help|escape}" /></a>
{/if}
