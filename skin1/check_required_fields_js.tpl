{* $Id: check_required_fields_js.tpl,v 1.15 2006/03/24 11:14:49 max Exp $ *}
{*
Use service array:
	requiredFields
array structure:
	array(id, name, shadow_flag)
where:
	id 			- tag id
	name 		- element name
*}
<script type="text/javascript">
<!--
var lbl_required_field_is_empty = "{$lng.lbl_required_field_is_empty|strip_tags|escape:javascript}";
-->
</script>
{include file="main/include_js.tpl" src="check_required_fields_js.js"}

