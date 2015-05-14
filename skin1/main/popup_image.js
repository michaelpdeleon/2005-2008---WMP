// $Id: popup_image.js,v 1.4 2005/12/21 13:01:22 max Exp $
function popup_image(type, id, max_x, max_y, title) {

	max_x = parseInt(max_x);
	max_y = parseInt(max_y);

	if (!max_x)
		max_x = 160;
	else
		max_x += 25;
	if (!max_y)
		max_y = 120;
	else
		max_y += 25;

	return window.open(xcart_web_dir+'/popup_image.php?type='+type+'&id='+id+'&title='+title,'images','width='+max_x+',height='+max_y+',toolbar=no,status=no,scrollbars=yes,resizable=yes,menubar=no,location=no,direction=no');
}
