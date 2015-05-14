<?php /* Smarty version 2.6.12, created on 2015-05-14 07:17:11
         compiled from buttons/go_image.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', 'buttons/go_image.tpl', 1, false),)), $this); ?>
src="<?php if ($this->_tpl_vars['full_url']):  echo $this->_tpl_vars['http_host'];  echo ((is_array($_tmp=$this->_tpl_vars['ImagesDir'])) ? $this->_run_mod_handler('replace', true, $_tmp, "..", "") : smarty_modifier_replace($_tmp, "..", ""));  else:  echo $this->_tpl_vars['ImagesDir'];  endif; ?>/go.gif" class="GoImage" alt=""