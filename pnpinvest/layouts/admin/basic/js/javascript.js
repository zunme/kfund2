// �� â
function popup_window(url, winname, opt)
{
    window.open(url, winname, opt);
}

/*** window.popup.open ***/
/*** sms ���� �߰� �� �Լ� ***/
function popup(src,width,height) {
	var scrollbars = "1";
	var resizable = "no";
	if (typeof(arguments[3])!="undefined") scrollbars = arguments[3];
	if (arguments[4]) resizable = "yes";
	window.open(src,'popup','width='+width+',height='+height+',scrollbars='+scrollbars+',toolbar=no,status=no,resizable='+resizable+',menubar=no');
}
/*** sms ���� �߰� �� �Լ� End ***/
