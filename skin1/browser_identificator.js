// $Id: browser_identificator.js,v 1.3 2005/11/17 06:55:36 max Exp $
if (document.getElementById('adaptives_script')) {
/*
var plugins = '';
var x;
	for(x = 0; x < navigator.plugins.length; x++)
		plugins += (plugins == ''?"":"|")+urlEncode(navigator.plugins[x].name);
*/
	document.getElementById('adaptives_script').src = xcart_web_dir+"/adaptive.php?send_browser="+(localIsDOM?"Y":"N")+(localIsStrict?"Y":"N")+(localIsJava?"Y":"N")+"|"+localBrowser+"|"+localVersion+"|"+localPlatform+"|"+(localIsCookie?"Y":"N")+"|"+screen.width+"|"+screen.height+"|"+usertype;
}
