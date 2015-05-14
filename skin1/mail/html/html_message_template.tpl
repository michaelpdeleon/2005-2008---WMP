{* $Id: html_message_template.tpl,v 1.5 2005/11/28 14:19:29 max Exp $ *}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<style type="text/css">
{literal}
BODY {
	MARGIN-TOP: 10px; 
	MARGIN-BOTTOM: 10px;
	MARGIN-LEFT: 10px; 
	MARGIN-RIGHT: 10px;
	FONT-SIZE: 12px; 
	FONT-FAMILY: arial,helvetica,sans-serif
	PADDING: 0px;
}
TD {
	FONT-SIZE: 12px; 
	FONT-FAMILY: arial,helvetica,sans-serif
	COLOR: #000000;
}
TH {
	FONT-SIZE: 13px; 
	FONT-FAMILY: arial,helvetica,sans-serif
}
H1 {
    FONT-SIZE: 20px
}
TABLE,IMG,A {
	BORDER: 0px;
}
{/literal}
</style>
</head>
<body>
{include file=$mail_body_template}
</body>
</html>
