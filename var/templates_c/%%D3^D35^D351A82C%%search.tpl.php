<?php /* Smarty version 2.6.12, created on 2015-05-14 06:52:27
         compiled from customer/search.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'customer/search.tpl', 16, false),)), $this); ?>
<form method="post" action="search.php" name="productsearchform">
<input type="hidden" name="simple_search" value="Y" />
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="posted_data[by_title]" value="Y" />
<input type="hidden" name="posted_data[by_shortdescr]" value="Y" />
<input type="hidden" name="posted_data[by_fulldescr]" value="Y" />
<!-- Start addition by Michael de Leon 11.21.06 -->
<INPUT type="hidden" name="posted_data[by_productcode]" value="Y" />
<!-- End addition by Michael de Leon 11.21.06 -->
<table cellpadding="0" cellspacing="0">  
<tr> 
	<!-- Deleted by Michael de Leon 10.25.06
	<td class="TopLabel" style="padding-left: 20px; padding-right: 5px;">:</td>
	-->
	<td valign="middle" style="padding-left: 5px; padding-top: 1px;"><input type="text" name="posted_data[substring]" size="20" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['search_prefilled']['substring'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></td>
	<td valign="middle" style="padding-left: 5px; padding-right: 10px; padding-top: 1px;"><a href="javascript: document.productsearchform.submit();"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/search_head.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></a></td>
	<!-- Deleted by Michael de Leon 09.14.06
	<td><a href="search.php"><u></u></a></td>
	-->
</tr>
</table>
</form>