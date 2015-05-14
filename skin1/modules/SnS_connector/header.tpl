{* $Id: header.tpl,v 1.6 2005/11/17 06:55:56 max Exp $ *}
{if $sns_collector_path_url ne ''}
<script src="{$sns_collector_path_url}/tracker.js.{$config.SnS_connector.sns_script_extension}" type="text/javascript"></script>
<noscript><img style="display: none" src="{$sns_collector_path_url}/static.{$config.SnS_connector.sns_script_extension}" alt="" /></noscript>
{/if}

