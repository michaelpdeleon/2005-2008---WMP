<?php /* Smarty version 2.6.12, created on 2014-10-13 01:03:04
         compiled from main/prnotice.tpl */ ?>
<?php func_load_lang($this, "main/prnotice.tpl","lbl_phone_1_title"); ?><!-- Deleted by Michael de Leon 09.14.06
Powered by <a href="http://www.x-cart.com" class="Bottom">X-Cart: shopping cart software</a>
-->
<!-- Start addition by Michael de Leon 09.14.06 -->
<div class="BottomText"><?php if ($this->_tpl_vars['config']['Company']['company_phone']):  echo $this->_tpl_vars['lng']['lbl_phone_1_title']; ?>
: <?php echo $this->_tpl_vars['config']['Company']['company_phone'];  endif; ?></div>
<!-- End addition by Michael de Leon 09.14.06 -->