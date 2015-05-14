<?php /* Smarty version 2.6.12, created on 2014-10-11 21:11:11
         compiled from menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'menu.tpl', 5, false),)), $this); ?>
<table cellspacing="1" width="100%" class="VertMenuBorder">
<tr>
<td class="VertMenuTitle">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td><?php echo $this->_tpl_vars['link_begin']; ?>
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/<?php if ($this->_tpl_vars['dingbats'] != ''):  echo $this->_tpl_vars['dingbats'];  else: ?>spacer.gif<?php endif; ?>" class="VertMenuTitleIcon" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['menu_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><?php echo $this->_tpl_vars['link_end']; ?>
</td>
<td width="100%" align="right"><?php if ($this->_tpl_vars['link_href']): ?><a href="<?php echo $this->_tpl_vars['link_href']; ?>
"><?php endif; ?><font class="VertMenuTitle"><?php echo $this->_tpl_vars['menu_title']; ?>
</font><?php if ($this->_tpl_vars['link_href']): ?></a><?php endif; ?></td>
</tr></table>
</td>
</tr>
<tr> 
<td class="VertMenuBox">
<table cellpadding="0" cellspacing="0" width="100%">
<tr><td class="VertMenuContent"><?php echo $this->_tpl_vars['menu_content']; ?>
<!-- Deleted by Michael de Leon 10.27.06 <br /> --></td></tr>
</table>
</td></tr>
</table>