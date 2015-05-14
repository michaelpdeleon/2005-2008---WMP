{* $Id: button.tpl,v 1.11 2005/11/17 06:55:56 max Exp $ *}
{if $config.SnS_connector.sns_display_button eq 'Y' && $sns_collector_path_url ne ''}
{if $text_link ne "Y"}
<table cellpadding="0" cellspacing="0">
<tr><td align="center"><img style="display: none; cursor: pointer" onclick="javascript: window.open('{$sns_collector_path_url}/openChat.{$config.SnS_connector.sns_script_extension}', '_blank', 'status=yes,toolbar=no,menubar=no,location=no,width=500,height=400')" border="0" src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}" id="snsOperatorButton" alt="Powered by Sales-n-Stats" /><script type="text/javascript">
<!--
if (document.getElementById('snsOperatorButton')) document.getElementById('snsOperatorButton').style.display = '';
-->
</script>
<noscript><a href="{$sns_collector_path_url}/leaveMessage.{$config.SnS_connector.sns_script_extension}?noscript=true" target="_blank"><img src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}?script=no" style="cursor: pointer" alt="Powered by Sales-n-Stats" /></a></noscript></td></tr>
<tr><td height="15" align="center"><font size="1" face="Arial"><a href="http://www.sales-n-stats.com" style="text-decoration: none; color: #550000" target="_blank"><b>Powered by Sales-n-Stats</b></a></font></td></tr>
</table>
{else}
<script src="{$sns_collector_path_url}/operatorButton.js.{$config.SnS_connector.sns_script_extension}?mode=text"></script><noscript><a href="{$sns_collector_path_url}/leaveMessage.{$config.SnS_connector.sns_script_extension}?noscript=true" target="_blank">{$lng.lbl_sns_click_for_live_help}</a></noscript>
{/if}
{/if}
