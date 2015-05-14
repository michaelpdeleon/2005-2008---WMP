<?php /* Smarty version 2.6.12, created on 2014-10-11 21:10:17
         compiled from dialog_message.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'dialog_message.tpl', 25, false),)), $this); ?>
<?php func_load_lang($this, "dialog_message.tpl","lbl_error,lbl_warning,lbl_information,lbl_close,lbl_go_to_last_edit_section"); ?><?php if ($this->_tpl_vars['top_message']['content'] != "" || $this->_tpl_vars['alt_content'] != ""):  if ($this->_tpl_vars['top_message']['type'] == 'E'):  $this->assign('log_icon', "log_type_Error.gif");  $this->assign('log_title', $this->_tpl_vars['lng']['lbl_error']);  elseif ($this->_tpl_vars['top_message']['type'] == 'W'):  $this->assign('log_icon', "log_type_Warning.gif");  $this->assign('log_title', $this->_tpl_vars['lng']['lbl_warning']);  else:  $this->assign('log_icon', "log_type_Information.gif");  $this->assign('log_title', $this->_tpl_vars['lng']['lbl_information']);  endif;  if ($this->_tpl_vars['alt_content'] != ""):  $this->assign('log_icon', "log_type_Warning.gif");  $this->assign('log_title', $this->_tpl_vars['title']);  endif; ?>
<div align="center" id="dialog_message">
<table cellspacing="0" class="DialogInfo">
<tr>
<td class="DialogInfoTitleBorder">
<table width="100%" cellspacing="2">
<tr> 
	<td class="DialogInfoTitle" width="16"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/<?php echo $this->_tpl_vars['log_icon']; ?>
" class="DialogInfoIcon" alt="" /></td>
	<td width="100%" class="DialogInfoTitle" align="left"><?php echo $this->_tpl_vars['log_title']; ?>
</td>
<?php if ($this->_tpl_vars['top_message']['no_close'] == ""): ?>
	<td align="right" class="DialogInfoTitle"><a href="javascript: void(0);" onclick="javascript: document.getElementById('dialog_message').style.display = 'none';"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/close.gif" class="DialogInfoClose" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['lng']['lbl_close'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /></a></td>
<?php endif; ?>
</tr>
</table></td>
</tr>
<tr>
<td class="DialogInfoBorder">
<table cellspacing="1" width="100%">
<tr> 
<td valign="top" class="DialogBox"><?php if ($this->_tpl_vars['alt_content'] != ""):  echo $this->_tpl_vars['alt_content'];  else:  echo $this->_tpl_vars['top_message']['content'];  endif;  if ($this->_tpl_vars['top_message']['anchor'] != ""): ?>
<br /><br />
<div align="right">
<table cellspacing="0" cellpadding="0">
<tr>
	<td><a href="#<?php echo $this->_tpl_vars['top_message']['anchor']; ?>
"><?php echo $this->_tpl_vars['lng']['lbl_go_to_last_edit_section']; ?>
</a></td>
	<td><a href="#<?php echo $this->_tpl_vars['top_message']['anchor']; ?>
"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/goto_arr.gif" width="12" height="10" alt="" /></a></td>
</tr>
</table>
</div><?php endif; ?>
</td>
</tr>
</table></td>
</tr></table>
<br />
</div>
<?php endif; ?>