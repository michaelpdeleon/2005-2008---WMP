<?php /* Smarty version 2.6.12, created on 2015-05-14 06:52:27
         compiled from news.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('insert', 'gate', 'news.tpl', 3, false),)), $this); ?>
<?php func_load_lang($this, "news.tpl","lbl_your_email,lbl_news"); ?><?php if ($this->_tpl_vars['active_modules']['News_Management']):  require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'gate', 'func' => 'news_exist', 'assign' => 'is_news_exist', 'lngcode' => $this->_tpl_vars['shop_language'])), $this); ?>

<?php endif;  if ($this->_tpl_vars['active_modules']['News_Management'] && $this->_tpl_vars['is_news_exist']): ?>
<br />
<?php ob_start(); ?>
<div class="VertMenuItems">
<div style="font-size: 9px">
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "today_news.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php require_once(SMARTY_CORE_DIR . 'core.run_insert_handler.php');
echo smarty_core_run_insert_handler(array('args' => array('name' => 'gate', 'func' => 'news_subscription_allowed', 'assign' => 'is_subscription_allowed', 'lngcode' => $this->_tpl_vars['shop_language'])), $this); ?>

<?php if ($this->_tpl_vars['is_subscription_allowed']): ?>
<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" width="1" height="8" alt="" /><br />

<form action="news.php" name="subscribeform" method="post">
<input type="hidden" name="subscribe_lng" value="<?php echo $this->_tpl_vars['store_language']; ?>
" />

<table>
<tr>
	<td>
<?php echo $this->_tpl_vars['lng']['lbl_your_email']; ?>

<br />
<input type="text" name="newsemail" size="16" />
<br />
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "buttons/subscribe_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	</td>
</tr>
</table>
</form>
<?php endif; ?></div>
<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean();  $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.tpl", 'smarty_include_vars' => array('dingbats' => "dingbats_news.gif",'menu_title' => $this->_tpl_vars['lng']['lbl_news'],'menu_content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
  endif; ?>