<?php /* Smarty version 2.6.12, created on 2014-10-11 21:14:32
         compiled from customer/menu_cart.tpl */ ?>
<?php func_load_lang($this, "customer/menu_cart.tpl","lbl_view_cart,lbl_checkout,lbl_friends_wish_list,lbl_wish_list,lbl_gift_registry,lbl_your_cart"); ?><?php ob_start(); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/main/minicart.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<a href="cart.php" class="wwmp_vertmenulink"><?php echo $this->_tpl_vars['lng']['lbl_view_cart']; ?>
</a><br />
<!-- Deleted by Michael de Leon 09.14.06
<A href="home.php" class="VertMenuItems"></A><BR>
-->
<a href="cart.php?mode=checkout" class="wwmp_vertmenulink"><?php echo $this->_tpl_vars['lng']['lbl_checkout']; ?>
</a><br />
<?php if ($this->_tpl_vars['active_modules']['Wishlist'] != "" && $this->_tpl_vars['wlid'] != ""): ?>
<a href="cart.php?mode=friend_wl&amp;wlid=<?php echo $this->_tpl_vars['wlid']; ?>
" class="wwmp_vertmenulink"><?php echo $this->_tpl_vars['lng']['lbl_friends_wish_list']; ?>
</a><br />
<?php endif; ?>

<!-- Start addition by Michael de Leon 09.14.06 -->
<?php if ($this->_tpl_vars['anonymous_login'] == "" && $this->_tpl_vars['login'] != ""): ?>
<!-- End addition by Michael de Leon 09.14.06 -->
<?php if ($this->_tpl_vars['active_modules']['Wishlist'] != ""): ?>
<a href="cart.php?mode=wishlist" class="wwmp_vertmenulink"><?php echo $this->_tpl_vars['lng']['lbl_wish_list']; ?>
</a><br />
<?php if ($this->_tpl_vars['active_modules']['Gift_Registry'] != ""): ?>
<a href="giftreg_manage.php" class="wwmp_vertmenulink"><?php echo $this->_tpl_vars['lng']['lbl_gift_registry']; ?>
</a><br />
<?php endif; ?>
<?php endif; ?>
<!-- Deleted by Michael de Leon 09.14.06
<a href="register.php?mode=update" class="wwmp_vertmenulink"></a><br />
<a href="register.php?mode=delete" class="wwmp_vertmenulink"></a><br />
</a><br />
-->
<?php if ($this->_tpl_vars['user_subscription'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/Subscriptions/subscriptions_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br />
<?php endif; ?>
<?php if ($this->_tpl_vars['active_modules']['RMA'] != ""): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/RMA/customer_menu.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><br />
<?php endif; ?>
<!-- Start addition by Michael de Leon 07.06.07
<br />
-->
<!-- Start addition by Michael de Leon 09.14.06 -->
<?php endif; ?>
<!-- End addition by Michael de Leon 09.14.06 -->
<?php $this->_smarty_vars['capture']['menu'] = ob_get_contents(); ob_end_clean(); ?>
<!-- Start addition by Michael de Leon 10.26.06 -->
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "menu.tpl", 'smarty_include_vars' => array('dingbats' => "wwmp_yc_icon10.26.06.jpg",'menu_title' => $this->_tpl_vars['lng']['lbl_your_cart'],'menu_content' => $this->_smarty_vars['capture']['menu'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<!-- End addition by Michael de Leon 10.26.06 -->
<!-- Deleted by Michael de Leon 10.26.06
-->