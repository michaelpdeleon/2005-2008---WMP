<?php /* Smarty version 2.6.12, created on 2014-08-28 05:49:10
         compiled from buttons/button_yourinfo_continuebtn_image.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'regex_replace', 'buttons/button_yourinfo_continuebtn_image.tpl', 10, false),array('modifier', 'cat', 'buttons/button_yourinfo_continuebtn_image.tpl', 12, false),array('modifier', 'amp', 'buttons/button_yourinfo_continuebtn_image.tpl', 12, false),array('modifier', 'escape', 'buttons/button_yourinfo_continuebtn_image.tpl', 22, false),)), $this); ?>
<?php if ($this->_tpl_vars['config']['Adaptives']['platform'] == 'MacPPC' && $this->_tpl_vars['config']['Adaptives']['browser'] == 'NN'): ?>
  <?php $this->assign('js_to_href', 'Y'); ?>
<?php endif; ?>
<?php if ($this->_tpl_vars['type'] == 'input'): ?>
  <?php $this->assign('img_type', 'input type="image"'); ?>
<?php else: ?>
  <?php $this->assign('img_type', 'img'); ?>
<?php endif; ?>
<?php $this->assign('js_link', ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('regex_replace', true, $_tmp, "/^\s*javascript\s*:/Si", "") : smarty_modifier_regex_replace($_tmp, "/^\s*javascript\s*:/Si", ""))); ?>
<?php if ($this->_tpl_vars['js_link'] == $this->_tpl_vars['href']): ?>
  <?php $this->assign('js_link', ((is_array($_tmp=((is_array($_tmp=((is_array($_tmp="javascript: self.location='")) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_tpl_vars['href']) : smarty_modifier_cat($_tmp, $this->_tpl_vars['href'])))) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp)))) ? $this->_run_mod_handler('cat', true, $_tmp, "';") : smarty_modifier_cat($_tmp, "';"))); ?>
<?php else: ?>
  <?php $this->assign('js_link', $this->_tpl_vars['href']); ?>
  <?php if ($this->_tpl_vars['js_to_href'] != 'Y'): ?>
    <?php $this->assign('onclick', $this->_tpl_vars['href']); ?>
    <?php $this->assign('href', "javascript: void(0);"); ?>
  <?php endif; ?>
<?php endif; ?>

<?php if ($this->_tpl_vars['style'] == 'button' && ( $this->_tpl_vars['config']['Adaptives']['platform'] != 'MacPPC' || $this->_tpl_vars['config']['Adaptives']['browser'] != 'NN' )): ?>
<table cellspacing="0" cellpadding="0" onclick="<?php echo $this->_tpl_vars['js_link']; ?>
" class="ButtonTable"<?php if ($this->_tpl_vars['title'] != ''): ?> title="<?php echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"<?php endif; ?>>
<?php echo '<tr><td><';  echo $this->_tpl_vars['img_type'];  echo ' src="';  echo $this->_tpl_vars['ImagesDir'];  echo '/but1.gif" class="ButtonSide" alt="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '" /></td><td class="Button"';  echo $this->_tpl_vars['reading_direction_tag'];  echo '><font class="Button">';  echo $this->_tpl_vars['button_title'];  echo '</font></td><td><img src="';  echo $this->_tpl_vars['ImagesDir'];  echo '/but2.gif" class="ButtonSide" alt="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '" /></td></tr>'; ?>

</table>
<?php elseif ($this->_tpl_vars['image_menu']): ?>
<?php echo '<table cellspacing="0" class="SimpleButton"><tr>';  if ($this->_tpl_vars['button_title'] != ''):  echo '<td><a class="VertMenuItems" href="';  echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp));  echo '"';  if ($this->_tpl_vars['onclick'] != ''):  echo ' onclick="';  echo $this->_tpl_vars['onclick'];  echo '"';  endif;  echo '';  if ($this->_tpl_vars['title'] != ''):  echo ' title="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '"';  endif;  echo '';  if ($this->_tpl_vars['target'] != ''):  echo ' target="';  echo $this->_tpl_vars['target'];  echo '"';  endif;  echo '><font class="VertMenuItems">';  echo $this->_tpl_vars['button_title'];  echo '&nbsp;</font></a></td>';  endif;  echo '<td>';  if ($this->_tpl_vars['img_type'] == 'img'):  echo '<a class="VertMenuItems" href="';  echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp));  echo '"';  if ($this->_tpl_vars['onclick'] != ''):  echo ' onclick="';  echo $this->_tpl_vars['onclick'];  echo '"';  endif;  echo '';  if ($this->_tpl_vars['title'] != ''):  echo ' title="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '"';  endif;  echo '';  if ($this->_tpl_vars['target'] != ''):  echo ' target="';  echo $this->_tpl_vars['target'];  echo '"';  endif;  echo '>';  endif;  echo '<';  echo $this->_tpl_vars['img_type'];  echo ' ';  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/go_image_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  echo ' />';  if ($this->_tpl_vars['img_type'] == 'img'):  echo '</a>';  endif;  echo '</td></tr></table>'; ?>

<?php else: ?>
<?php echo '<table cellspacing="0"><tr>';  if ($this->_tpl_vars['button_title'] != ''):  echo '<td><a class="Button" href="';  echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp));  echo '"';  if ($this->_tpl_vars['onclick'] != ''):  echo ' onclick="';  echo $this->_tpl_vars['onclick'];  echo '"';  endif;  echo '';  if ($this->_tpl_vars['title'] != ''):  echo ' title="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '"';  endif;  echo '';  if ($this->_tpl_vars['target'] != ''):  echo ' target="';  echo $this->_tpl_vars['target'];  echo '"';  endif;  echo '>';  echo $this->_tpl_vars['button_title'];  echo '&nbsp;</a></td>';  endif;  echo '<td>';  if ($this->_tpl_vars['img_type'] == 'img'):  echo '<a href="';  echo ((is_array($_tmp=$this->_tpl_vars['href'])) ? $this->_run_mod_handler('amp', true, $_tmp) : smarty_modifier_amp($_tmp));  echo '"';  if ($this->_tpl_vars['onclick'] != ''):  echo ' onclick="';  echo $this->_tpl_vars['onclick'];  echo '"';  endif;  echo '';  if ($this->_tpl_vars['title'] != ''):  echo ' title="';  echo ((is_array($_tmp=$this->_tpl_vars['title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp));  echo '"';  endif;  echo '';  if ($this->_tpl_vars['target'] != ''):  echo ' target="';  echo $this->_tpl_vars['target'];  echo '"';  endif;  echo '>';  endif;  echo '<';  echo $this->_tpl_vars['img_type'];  echo ' ';  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/yourinfo_continuebtn_image.tpl", 'smarty_include_vars' => array('full_url' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  echo ' />';  if ($this->_tpl_vars['img_type'] == 'img'):  echo '</a>';  endif;  echo '</td></tr></table>'; ?>

<?php endif; ?>