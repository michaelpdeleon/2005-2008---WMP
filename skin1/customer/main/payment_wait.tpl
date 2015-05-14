{* $Id: payment_wait.tpl,v 1.8.2.1 2006/05/06 08:21:55 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>{$lng.msg_order_is_being_placed}</title>
<style type="text/css">
<!--
{literal}
BODY,DIV,TH,TD,P,INPUT,SELECT,TEXTAREA,TT {
	FONT-FAMILY: Verdana, Arial, Helvetica, Sans-serif; 
	COLOR: #550000;
	FONT-SIZE: 10px;
}
A:link {
	COLOR: #330000;
	TEXT-DECORATION: underline;
}
A:visited {
	COLOR: #330000;
	TEXT-DECORATION: underline;
}
A:hover {
	COLOR: #550000;
	TEXT-DECORATION: none;
}
A:active  {
	COLOR: #330000;
	TEXT-DECORATION: underline;
}
H1 {
	FONT-SIZE: 15px;
}
HTML,BODY { 
	MARGIN: 0px;
	PADDING: 0px;
	BACKGROUND-COLOR: #FFFBD3;
	HEIGHT: 100%;
}
TABLE, IMG {
	BORDER: 0px;
}
FORM {
	MARGIN: 0px;
}
.bground {
	BACKGROUND-COLOR: #FF8600;
}
TABLE.Container {
	HEIGHT: 100%;
	WIDTH: 100%;
}
TABLE.WebBasedPayment {
	HEIGHT: 100%;
	WIDTH: 100%;
}
TABLE.WebBasedPayment TR TD {
	TEXT-ALIGN: center;
	VERTICAL-ALIGN: middle;
	HEIGHT: 90%;
	PADDING: 0px;
}
{/literal}
-->
</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" align="center" class="Container">
<tr>
	<td class="bground" colspan="2">&nbsp;</td>
</tr>
<tr>
	<td height="1" colspan="2"><table height="1" cellspacing="0" cellpadding="0"><td></td></table></td>
</tr>
<tr>
	<td class="bground" height="1" colspan="2"><table height="1" cellspacing="0" cellpadding="0"><td></td></table></td>
</tr>
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr>
	<td style="padding-left: 30px; height: 90%;">
	<table cellspacing="1" cellpadding="2" width="100%" style="height: 100%;">
	<tr>
		<td valign="top"><h1>{$lng.msg_order_is_being_placed}</h1></td>
	</tr>
	<tr>
		<td valign="top" height="95%">
