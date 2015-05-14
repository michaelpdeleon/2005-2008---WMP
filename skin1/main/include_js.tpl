{* $Id: include_js.tpl,v 1.4 2006/03/01 07:20:13 max Exp $ *}
{if $config.UA.platform eq 'MacPPC'}
<script language="JavaScript" type="text/javascript">
<!--
{include_php file="`$template_dir`/`$src`"}
-->
</script>
{else}
<script src="{$SkinDir}/{$src}" language="JavaScript" type="text/javascript"></script>
{/if}
