<?php /* Smarty version 2.6.12, created on 2014-10-11 21:14:10
         compiled from v4menu/v4menu.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'v4menu/v4menu.tpl', 22, false),)), $this); ?>
<?php $this->assign('showicons', 'n'); ?>
<?php $this->assign('iconsymbol', '<font color="#F4F88E">&#8226;</font>&nbsp;'); ?>
<?php $this->assign('showarrows', 'n'); ?>
<?php $this->assign('arrowsymbol', '<font color="#8FD2A5">&raquo;</font>'); ?>
<?php if ($this->_tpl_vars['config']['General']['root_categories'] == 'Y'): ?>
<!-- Start addition by Michael de Leon 10.30.06 -->
<table cellspacing="1" width="100%" class="VertMenuBorder">
<tr>
<td class="VertMenuTitle">
<table cellspacing="0" cellpadding="0" width="100%"><tr>
<td><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_lb_icon10.26.06.jpg" class="VertMenuTitleIcon" alt="<?php echo ((is_array($_tmp=$this->_tpl_vars['menu_title'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" /><?php echo $this->_tpl_vars['link_end']; ?>
</td>
<td width="100%" align="right"><font class="VertMenuTitle">Lab Products</font></td>
</tr></table>
</td>
</tr>
</table>
<div id="v4menuwrapper">
<div class="v4menu">
<!-- End addition by Michael de Leon 10.30.06 -->
<ul>
<?php $_from = $this->_tpl_vars['categories']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['c']):
?>
<!-- Start addition by Michael de Leon 10.30.06 -->
	<?php if ($this->_tpl_vars['c']['categoryid'] != '25' && $this->_tpl_vars['c']['categoryid'] != '26' && $this->_tpl_vars['c']['categoryid'] != '27' && $this->_tpl_vars['c']['categoryid'] != '28' && $this->_tpl_vars['c']['categoryid'] != '29'): ?>
<!-- End addition by Michael de Leon 10.30.06 -->
		<li><a href="home.php?cat=<?php echo $this->_tpl_vars['c']['categoryid']; ?>
" title="<?php echo $this->_tpl_vars['c']['category']; ?>
"><?php if ($this->_tpl_vars['showicons'] == 'y'):  echo $this->_tpl_vars['iconsymbol'];  endif;  echo $this->_tpl_vars['c']['category'];  if ($this->_tpl_vars['c']['subcats'] && $this->_tpl_vars['showarrows'] == 'y'): ?>&nbsp;<?php echo $this->_tpl_vars['arrowsymbol'];  endif; ?><!--[if IE 7]><!--></a><!--<![endif]-->
		<?php if ($this->_tpl_vars['c']['subcats'] != ""): ?>
			<table class="v4menutable"><tr><td>
			<!-- Deleted by Michael de Leon 10.27.06 A BIG SECTION HERE.  SEE THE ORIGINAL FILE. -->
			</td></tr></table><!--[if lte IE 6]></a><![endif]-->
		<?php endif; ?>
		</li>
<!-- Start addition by Michael de Leon 10.30.06 -->
	<?php endif; ?>
<!-- End addition by Michael de Leon 10.30.06 -->
<?php endforeach; endif; unset($_from); ?>
</ul>
</div>
</div>
<?php endif; ?>