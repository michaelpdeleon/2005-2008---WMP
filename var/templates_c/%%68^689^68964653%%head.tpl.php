<?php /* Smarty version 2.6.12, created on 2015-05-14 06:37:37
         compiled from head.tpl */ ?>
<!--
include_once $xcart_dir."/home/wwmpon2/public_html/xcart413/include/func/func.debug.php";
func_print_r($this->_tpl_vars);
-->
<!-- Start of edit by Michael de Leon 09.14.06-->
<table cellpadding="0" cellspacing="0" width="100%" align="center">
<tr> 
	<td class="HeadLogo" align="center"><a href="javascript:"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_header11.17.06.jpg" width="980" height="100" alt="" /></a></td>
</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" align="center">
<?php if ($this->_tpl_vars['main'] != 'fast_lane_checkout'): ?>
<tr> 
	<td colspan="3" class="HeadThinLine"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /></td>
</tr>
<!-- End of edit by Michael de Leon 09.14.06-->
<tr> 
	<td class="HeadLine" height="22" width="20%">
<?php if ($this->_tpl_vars['usertype'] == 'C'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
	</td>
<!-- Deleted by Michael de Leon 10.25.06
	<td class="HeadLine" align="right">
<form action="home.php" method="get" name="sl_form">
<input type="hidden" name="redirect" value="?" />
<table cellpadding="0" cellspacing="0">
<tr>
	<td style="padding-right: 5px;"><b>:</b></td>
	<td><select name="sl" onchange="javascript: this.form.submit();">
<option value="" selected="selected"></option>
	</select></td>
</tr>
</table>
</form>
&nbsp;
	</td>
-->
	<!-- Begin addition by Michael de Leon 09.14.06-->
	<td class="HeadLine" align="center">
<?php unset($this->_sections['sb']);
$this->_sections['sb']['name'] = 'sb';
$this->_sections['sb']['loop'] = is_array($_loop=$this->_tpl_vars['speed_bar']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sb']['show'] = true;
$this->_sections['sb']['max'] = $this->_sections['sb']['loop'];
$this->_sections['sb']['step'] = 1;
$this->_sections['sb']['start'] = $this->_sections['sb']['step'] > 0 ? 0 : $this->_sections['sb']['loop']-1;
if ($this->_sections['sb']['show']) {
    $this->_sections['sb']['total'] = $this->_sections['sb']['loop'];
    if ($this->_sections['sb']['total'] == 0)
        $this->_sections['sb']['show'] = false;
} else
    $this->_sections['sb']['total'] = 0;
if ($this->_sections['sb']['show']):

            for ($this->_sections['sb']['index'] = $this->_sections['sb']['start'], $this->_sections['sb']['iteration'] = 1;
                 $this->_sections['sb']['iteration'] <= $this->_sections['sb']['total'];
                 $this->_sections['sb']['index'] += $this->_sections['sb']['step'], $this->_sections['sb']['iteration']++):
$this->_sections['sb']['rownum'] = $this->_sections['sb']['iteration'];
$this->_sections['sb']['index_prev'] = $this->_sections['sb']['index'] - $this->_sections['sb']['step'];
$this->_sections['sb']['index_next'] = $this->_sections['sb']['index'] + $this->_sections['sb']['step'];
$this->_sections['sb']['first']      = ($this->_sections['sb']['iteration'] == 1);
$this->_sections['sb']['last']       = ($this->_sections['sb']['iteration'] == $this->_sections['sb']['total']);
?>
	<?php if ($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['active'] == 'Y'): ?>
		<?php if ($this->_sections['sb']['last']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/tab.tpl", 'smarty_include_vars' => array('tab_title' => "<A href=\"".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['link'])."\" class=\"speedmenu\">".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['title'])."</A>")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/tab.tpl", 'smarty_include_vars' => array('tab_title' => "<A href=\"".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['link'])."\" class=\"speedmenu\">".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['title'])."</A>")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <font class="speedmenu_bar">|</font>
		<?php endif; ?>
	<?php endif; ?>
<?php endfor; endif; ?></td>
	<td class="HeadLine" width="20%">&nbsp;</td>
	<!-- End addition by Michael de Leon 09.14.06-->
</tr>
<?php else: ?>
<!-- Start addition by Michael de Leon 11.09.06 -->
<tr> 
	<td colspan="5" class="HeadThinLine"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /></td>
</tr>
<tr> 
	<td class="HeadLine" height="22" width="20%">
<?php if ($this->_tpl_vars['usertype'] == 'C'): ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/search.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<?php endif; ?>
	</td>
	<td class="HeadLine" align="center">
<?php unset($this->_sections['sb']);
$this->_sections['sb']['name'] = 'sb';
$this->_sections['sb']['loop'] = is_array($_loop=$this->_tpl_vars['speed_bar']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['sb']['show'] = true;
$this->_sections['sb']['max'] = $this->_sections['sb']['loop'];
$this->_sections['sb']['step'] = 1;
$this->_sections['sb']['start'] = $this->_sections['sb']['step'] > 0 ? 0 : $this->_sections['sb']['loop']-1;
if ($this->_sections['sb']['show']) {
    $this->_sections['sb']['total'] = $this->_sections['sb']['loop'];
    if ($this->_sections['sb']['total'] == 0)
        $this->_sections['sb']['show'] = false;
} else
    $this->_sections['sb']['total'] = 0;
if ($this->_sections['sb']['show']):

            for ($this->_sections['sb']['index'] = $this->_sections['sb']['start'], $this->_sections['sb']['iteration'] = 1;
                 $this->_sections['sb']['iteration'] <= $this->_sections['sb']['total'];
                 $this->_sections['sb']['index'] += $this->_sections['sb']['step'], $this->_sections['sb']['iteration']++):
$this->_sections['sb']['rownum'] = $this->_sections['sb']['iteration'];
$this->_sections['sb']['index_prev'] = $this->_sections['sb']['index'] - $this->_sections['sb']['step'];
$this->_sections['sb']['index_next'] = $this->_sections['sb']['index'] + $this->_sections['sb']['step'];
$this->_sections['sb']['first']      = ($this->_sections['sb']['iteration'] == 1);
$this->_sections['sb']['last']       = ($this->_sections['sb']['iteration'] == $this->_sections['sb']['total']);
?>
	<?php if ($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['active'] == 'Y'): ?>
		<?php if ($this->_sections['sb']['last']): ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/tab.tpl", 'smarty_include_vars' => array('tab_title' => "<A href=\"".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['link'])."\" class=\"speedmenu\">".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['title'])."</A>")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php else: ?>
			<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "customer/tab.tpl", 'smarty_include_vars' => array('tab_title' => "<A href=\"".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['link'])."\" class=\"speedmenu\">".($this->_tpl_vars['speed_bar'][$this->_sections['sb']['index']]['title'])."</A>")));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <font class="speedmenu_bar">|</font>
		<?php endif; ?>
	<?php endif; ?>
<?php endfor; endif; ?></td>
<!-- End addition by Michael de Leon 11.09.06 -->
	<td colspan="3" class="HeadLine" width="20%">
	<form action="<?php echo $this->_tpl_vars['xcart_web_dir']; ?>
/include/login.php" method="post" name="toploginform">
	<input type="hidden" name="mode" value="logout" />
	<input type="hidden" name="redirect" value="<?php echo $this->_tpl_vars['redirect']; ?>
" />
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td class="FLCAuthPreBox">
<?php if ($this->_tpl_vars['active_modules']['SnS_connector'] && $this->_tpl_vars['sns_collector_path_url'] != '' && $this->_tpl_vars['config']['SnS_connector']['sns_display_button'] == 'Y'): ?>
	<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/rarrow.gif" alt="" valign="middle" /><b><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "modules/SnS_connector/button.tpl", 'smarty_include_vars' => array('text_link' => 'Y')));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></b>
<?php else: ?>
	<img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" />
<?php endif; ?></td>
<?php if ($this->_tpl_vars['login'] != ""): ?>
<!-- Start addition by Michael de Leon 11.02.06 -->
			<td class="wwmp_loginnoticehead" nowrap="nowrap">Hello <?php echo $this->_tpl_vars['login']; ?>
!</td>
			<td><a href="javascript: document.toploginform.submit();"><img class="wwmp_logoutbtn_head" src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/wwmp_logoutbtn11.01.06.jpg" border="0"></a></td>
<!-- End addition by Michael de Leon 11.02.06 -->
<?php endif; ?>
		</tr>
		</table>
	</form>
	</td>
</tr>
<?php endif; ?>
<tr> 
	<td colspan="5" class="HeadThinLine"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" class="Spc" alt="" /></td>
</tr>
<?php if ($this->_tpl_vars['main'] != 'fast_lane_checkout'): ?>
<tr>
<!-- Start edit by Michael de Leon 12.06.06 -->
	<td colspan="3" valign="middle" height="30" width="100%" align="center">
<table cellspacing="0" cellpadding="0" width="100%">
<tr>
<?php if (( ( $this->_tpl_vars['main'] == 'catalog' && $this->_tpl_vars['cat'] != '' ) || $this->_tpl_vars['main'] == 'product' || ( $this->_tpl_vars['main'] == 'comparison' && $this->_tpl_vars['mode'] == 'compare_table' ) || ( $this->_tpl_vars['main'] == 'choosing' && $GLOBALS['HTTP_GET_VARS']['mode'] == 'choose' ) ) && $this->_tpl_vars['config']['Appearance']['enabled_printable_version'] == 'Y'): ?>
	<td align="right"><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "printable.tpl", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?></td>
<?php else: ?>
	<td align="left">&nbsp;</td>
<?php endif; ?>
</tr>
</table>
	</td>
<!-- End edit by Michael de Leon 12.06.06 -->
</tr>
<?php else: ?>
<tr>
	<td colspan="3" class="FLCTopPad"><img src="<?php echo $this->_tpl_vars['ImagesDir']; ?>
/spacer.gif" alt="" /></td>
</tr>
<?php endif; ?>
</table>