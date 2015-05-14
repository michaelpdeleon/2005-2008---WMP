// $Id: func.js,v 1.16.2.5 2006/08/02 05:39:12 max Exp $

var current_taxes = [];
var product_thumbnail = document.getElementById('product_thumbnail');
var availObj = document.getElementById('product_avail');

/*
	Rebuild page if some options is changed
*/
function check_options() {
	var local_taxes = [];
	var is_rebuild_wholesale = false;
	var variantid = false;

	for (var t in taxes)
		local_taxes[t] = taxes[t][0];
	price = default_price;

	/* Find variant */
	for (var x in variants) {
		if (variants[x][1].length == 0)
			continue;

		variantid = x;
		for (var c in variants[x][1]) {
			if (getPOValue(c) != variants[x][1][c]) {
				variantid = false;
				break;
			}
		}

		if (variantid)
			break;
	}

	/* If variant found ... */
	if (variantid) {
		price = variants[variantid][0][0];
		orig_price = variants[variantid][0][4];
		avail = variants[variantid][0][1];

		/* Get variant wholesale prices */
		if (variants[variantid][3]) {
			product_wholesale = [];
			for (var t in variants[variantid][3]) {
				var _tmp = modi_price(variants[variantid][3][t][2], cloneObject(variants[variantid][3][t][3]), variants[variantid][3][t][4]);
				product_wholesale[t] = [variants[variantid][3][t][0], variants[variantid][3][t][1], _tmp[0], []];

				/* Get variant wholesale taxes */
				for (var c in _tmp[1]) {
					product_wholesale[t][3][c] = _tmp[1][c];
				}
			}
			is_rebuild_wholesale = true;
		}

		/* Get variant taxes */
		for (var t in local_taxes) {
			if (variants[variantid][2][t])
				local_taxes[t] = parseFloat(variants[variantid][2][t]);
		}

		if (!product_thumbnail)
			product_thumbnail = document.getElementById('product_thumbnail');

		/* Change product thumbnail */
		if (product_thumbnail) {
			if (variants[variantid][0][2].src && variants[variantid][0][2].width > 0 && variants[variantid][0][2].height > 0) {
				if (product_thumbnail.src != variants[variantid][0][2].src) {
					product_thumbnail.src = variants[variantid][0][2].src;
					product_thumbnail.width = variants[variantid][0][2].width;
					product_thumbnail.height = variants[variantid][0][2].height;
				}
			} else if (document.getElementById('product_thumbnail').src != product_image.src) {
				product_thumbnail.src = product_image.src;
				if (product_image.width > 0 && product_image.height > 0) {
					product_thumbnail.width = product_image.width;
					product_thumbnail.height = product_image.height;
				}
			}
		}

		/* Change product weight */
		if (document.getElementById('product_weight'))
			document.getElementById('product_weight').innerHTML = variants[variantid][0][3];
		if (document.getElementById('product_weight_box'))
			document.getElementById('product_weight_box').style.display = parseInt(variants[variantid][0][3]) > 0 ? "" : "none";

		/* Change product code */
		if (document.getElementById('product_code'))
			document.getElementById('product_code').innerHTML = variants[variantid][0][5];

	}

	if (pconf_price > 0)
		price = pconf_price;

	/* Find modifiers */
	var _tmp = modi_price(price, local_taxes, orig_price);
	price = _tmp[0];
	local_taxes = _tmp[1];
	if (!variantid) {
		product_wholesale = [];
		for (var t in _product_wholesale) {
			_tmp = modi_price(_product_wholesale[t][2], _product_wholesale[t][3], _product_wholesale[t][4]);
			product_wholesale[t] = [_product_wholesale[t][0], _product_wholesale[t][1], _tmp[0],_tmp[1]];
		}
		is_rebuild_wholesale = true;
	}

	/* Update taxes */
	for (var t in local_taxes) {
		if (document.getElementById('tax_'+t)) {
			document.getElementById('tax_'+t).innerHTML = currency_symbol+price_format(local_taxes[t]);
		}
		current_taxes[t] = local_taxes[t];
	}

	if (is_rebuild_wholesale)
		rebuild_wholesale();

	/* Update form elements */
	/* Update price */
	if (document.getElementById('product_price'))
		document.getElementById('product_price').innerHTML = currency_symbol+price_format(price);

	/* Update alt. price */
	if (alter_currency_rate > 0 && alter_currency_symbol != "" && document.getElementById('product_alt_price'))
		document.getElementById('product_alt_price').innerHTML = "("+alter_currency_symbol+" "+price_format(price*alter_currency_rate)+")";

	/* Update Save % */
	if (document.getElementById('save_percent') && document.getElementById('save_percent_box') && list_price > 0 && dynamic_save_money_enabled) {
		var save_percent = Math.round(100-(price/list_price)*100);
		if (save_percent > 0) {
			document.getElementById('save_percent_box').style.display = '';
			document.getElementById('save_percent').innerHTML = save_percent;
		} else {
			document.getElementById('save_percent_box').style.display = 'none';
			document.getElementById('save_percent').innerHTML = '0';
		}
	}

	/* Update product quantity */
	if (document.getElementById('product_avail_txt')) {
		if (avail > 0) {
			document.getElementById('product_avail_txt').innerHTML = substitute(txt_items_available, "items", (variantid ? avail : product_avail));
		} else {
			document.getElementById('product_avail_txt').innerHTML = lbl_no_items_available;
		}
	}

	if ((mq > 0 && avail > mq+min_avail) || is_unlimit)
		avail = mq+min_avail-1;

	var select_avail = min_avail;
	/* Update product quantity selector */
	if (!availObj)
		availObj = document.getElementById('product_avail');

	if (availObj && availObj.tagName.toUpperCase() == 'SELECT') {
		if (!isNaN(min_avail) && !isNaN(avail)) {
			var first_value = -1;
			if (availObj.options[0])
				first_value = availObj.options[0].value;

			if (first_value == min_avail) {

				/* New and old first value in quantities list is equal */
				if ((avail-min_avail) != availObj.options.length) {
					if (availObj.options.length > avail-min_avail) {
						var cnt = availObj.options.length;
						for (var x = avail-min_avail; x < cnt-1; x++)
							availObj.options[availObj.options.length-1] = null;
					} else {
						var cnt = availObj.options.length;
						for (var x = cnt+min_avail; x <= avail; x++)
							availObj.options[cnt++] = new Option(x, x);
					}
				}
			} else {

				/* New and old first value in quantities list is differ */
				while (availObj.options.length > 0)
					availObj.options[0] = null;
				var cnt = 0;
				for (var x = min_avail; x <= avail; x++)
					availObj.options[cnt++] = new Option(x, x);
			}
			if (availObj.options.length == 0)
				availObj.options[0] = new Option(txt_out_of_stock, 0);
		}
		select_avail = availObj.options[availObj.selectedIndex].value;
	}

	check_wholesale(select_avail);

	if ((alert_msg == 'Y') && (min_avail > avail))
		alert(txt_out_of_stock);
	
	/* Check exceptions */
	var ex_flag = check_exceptions();
	if (!ex_flag && (alert_msg == 'Y'))
		alert(exception_msg);
			
	if (document.getElementById('exception_msg'))
		document.getElementById('exception_msg').innerHTML = (ex_flag ? '' : exception_msg_html+"<br /><br />");

	return true;
}

/*
	Calculate product price with price modificators 
*/
function modi_price(_price, _taxes, _orig_price) {
var return_price = round(_price, 2);

	/* List modificators */
	for (var x2 in modifiers) {
		var value = getPOValue(x2);
		if (!value || !modifiers[x2][value])
			continue;

		/* Get selected option */
		var elm = modifiers[x2][value];
		return_price += parseFloat(elm[1] == '$' ? elm[0] : (_price*elm[0]/100));

		/* Get tax extra charge */
		for (var t2 in _taxes) {
			if (elm[2][t2]) {
				_taxes[t2] += (elm[1] == '$' ? elm[2][t2] : (_orig_price*elm[2][t2]/100));
			}
		}
	}

	return [return_price, _taxes];
}

/*
	Check product options exceptions
*/
function check_exceptions() {
	if (!exceptions)
		return true;

	/* List exceptions */
	for (var x in exceptions) {
		if (isNaN(x))
			continue;

		var found = true;
        for (var c in exceptions[x]) {
			var value = getPOValue(c);
			if (!value)
				return true;

            if (value != exceptions[x][c]) {
				found = false;
				break;
			}
		}
		if (found)
			return false;
	}

	return true;
}

/*
	Rebuild wholesale tables
*/
function rebuild_wholesale() {

	var obj = document.getElementById('wl_table');
	if (!obj)
		return false;

	/* Clear wholesale span object if product wholesale prices service array is empty */
	if (!product_wholesale || product_wholesale.length == 0) {
		obj.innerHTML = "";
		return false;
	}

	/* Display headline */
	var str = '<br /><table cellpadding="2" cellspacing="2"><tr class="TableHead"><td align="right"><b>'+lbl_quantity+':&nbsp;</b></td>';
	for (var x in product_wholesale) {
		if (product_wholesale[x][0] == 0)
			continue;
		str += '<td>'+product_wholesale[x][0];
		if (x == product_wholesale.length-1)
			str +='+';
		else if (product_wholesale[x][0] != product_wholesale[x][1])
			str += '-'+product_wholesale[x][1];
		str += '&nbsp;'+(product_wholesale[x][0] == 1 ? lbl_item : lbl_items)+'</td>';
	}

    /* Display wholesale prices taxes */
	var tax_str = '';
    if (taxes.length > 0) {
        for (var x in taxes) {
            if (current_taxes[x] > 0)
                tax_str += substitute(lbl_including_tax, 'tax', taxes[x][1])+'<br />';
        }
    }

	/* Display wholesale prices */
	str += '</tr><tr bgcolor="#EEEEEE"><td align="right"><b>'+lbl_price+(tax_str.length > 0 ? '*' : '')+':&nbsp;</b></td>';
	for (var x in product_wholesale) {
		if (product_wholesale[x][0] == 0)
			continue;
		str += '<td>'+price_format(product_wholesale[x][2])+'</td>';
	}

	str += '</tr></table>';

	if (tax_str.length > 0)
		str += '<br /><table><tr><td class="FormButton" valign="top"><b>*'+txt_note+':</b>&nbsp;</td><td nowrap="nowrap" valign="top">'+tax_str+'</td></tr></table>';

	str += '<br />';
	obj.innerHTML = str;

	return true;
}

/*
	Display current wholesale price as product price
*/
function check_wholesale(qty) {
	if (product_wholesale.length == 0)
		return true;

	var wl_taxes = current_taxes.slice(0);
	var wl_price = price;
	var found = false;
	for (var x = 0; x < product_wholesale.length; x++) {
		if (product_wholesale[x][0] <= qty && (product_wholesale[x][1] >= qty || product_wholesale[x][1] == 0)) {
			wl_price = product_wholesale[x][2];
			wl_taxes = product_wholesale[x][3].slice(0);
			found = true;
		}
		if (document.getElementById('wp'+x))
			document.getElementById('wp'+x).innerHTML = currency_symbol+price_format(price-default_price+product_wholesale[x][2]);
	}

	if (document.getElementById('product_price'))
		document.getElementById('product_price').innerHTML = currency_symbol+price_format(wl_price);
	if (alter_currency_rate > 0 && alter_currency_symbol != "" && document.getElementById('product_alt_price'))
		document.getElementById('product_alt_price').innerHTML = "("+alter_currency_symbol+" "+price_format((wl_price)*alter_currency_rate)+")";

	for (var x in taxes) {
		if (document.getElementById('tax_'+x) && wl_taxes[x] && current_taxes[x]) {
			document.getElementById('tax_'+x).innerHTML = currency_symbol+price_format(wl_taxes[x]);
		}
	}

	return true;
}

/*
	Get product option value
*/
function getPOValue(c) {
	if (!document.getElementById('po'+c) || document.getElementById('po'+c).tagName.toUpperCase() != 'SELECT')
		return false;
	return document.getElementById('po'+c).options[document.getElementById('po'+c).selectedIndex].value;
}

/*
    Get product option object by class name / class id
*/
function product_option(classid) {
	if (!isNaN(classid))
		 return document.getElementById("po"+classid);

	if (!names)
		return false;

	for (var x in names) {
		if (names[x]['class_name'] != classid)
			continue;
		return document.getElementById('po'+x);
    }

	return false;
}

/*
	Get product option value by class name / or class id
*/
function product_option_value(classid) {
	var obj = product_option(classid);
	if (!obj)
		return false;

	if (obj.type != 'select-one')
		return obj.value;

	var classid = parseInt(obj.id.substr(2));
	var optionid = parseInt(obj.options[obj.selectedIndex].value);
	if (names[classid] && names[classid]['options'][optionid])
		return names[classid]['options'][optionid];

	return false;
}

