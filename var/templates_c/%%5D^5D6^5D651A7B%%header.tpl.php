<?php /* Smarty version 2.6.12, created on 2014-10-11 21:15:03
         compiled from modules/SnS_connector/header.tpl */ ?>
<?php if ($this->_tpl_vars['sns_collector_path_url'] != ''): ?>
<script src="<?php echo $this->_tpl_vars['sns_collector_path_url']; ?>
/tracker.js.<?php echo $this->_tpl_vars['config']['SnS_connector']['sns_script_extension']; ?>
" type="text/javascript"></script>
<noscript><img style="display: none" src="<?php echo $this->_tpl_vars['sns_collector_path_url']; ?>
/static.<?php echo $this->_tpl_vars['config']['SnS_connector']['sns_script_extension']; ?>
" alt="" /></noscript>
<?php endif; ?>
