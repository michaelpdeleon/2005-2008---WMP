<?php /* Smarty version 2.6.12, created on 2014-10-11 21:15:06
         compiled from rectangle_bottom.tpl */ ?>
	</td>
</tr>
<!-- Deleted by Michael de Leon 09.14.06
<?php if ($this->_tpl_vars['active_modules']['Users_online'] != ""): ?>
<tr>
	<td><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Users_online/menu_users_online.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
</tr>
<?php else: ?>
<tr>
	<td><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /></td>
</tr>
<?php endif; ?>
-->
<tr>
	<td><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr>
	<td class="BottomRow">
<?php if ($this->_tpl_vars['printable'] != ''): ?>
<hr size="1" noshade="noshade" />
<?php endif; ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "bottom.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
</table>