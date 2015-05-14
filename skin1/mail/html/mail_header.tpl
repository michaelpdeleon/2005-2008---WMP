{* $Id: mail_header.tpl,v 1.6 2006/04/03 07:07:48 svowl Exp $ *}
<p /><font size="2">
{assign var="link" value="<a href=\"$http_location/\" target=\"_new\">`$config.Company.company_name`</a>"}
{$lng.eml_mail_header|substitute:"company":$link}
</font>

