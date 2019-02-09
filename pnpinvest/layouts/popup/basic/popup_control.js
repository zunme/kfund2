// ��Ű �Է�
function set_cookie(name, value, expirehours, domain)
{
    var today = new Date();
    today.setTime(today.getTime() + (60*60*1000*expirehours));
    document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + today.toGMTString() + ";";
    if (domain) {
        document.cookie += "domain=" + domain + ";";
    }
}

// ��Ű ����
function get_cookie(name)
{
    var find_sw = false;
    var start, end;
    var i = 0;

    for (i=0; i<= document.cookie.length; i++)
    {
        start = i;
        end = start + name.length;

        if(document.cookie.substring(start, end) == name)
        {
            find_sw = true
            break
        }
    }

    if (find_sw == true)
    {
        start = end + 1;
        end = document.cookie.indexOf(";", start);

        if(end < start)
            end = document.cookie.length;

        return document.cookie.substring(start, end);
    }
    return "";
}

// ��Ű ����
function delete_cookie(name)
{
    var today = new Date();

    today.setTime(today.getTime() - 1);
    var value = get_cookie(name);
    if(value != "")
        document.cookie = name + "=" + value + "; path=/; expires=" + today.toGMTString();
}



if (typeof(ts_layer) == 'undefined') { // �ѹ��� ����
    var ts_layer = true;

    function selectbox_hidden(layer_id) 
    { 
        var ly = eval(layer_id); 

        // ���̾� ��ǥ 
        var ly_left  = ly.offsetLeft; 
        var ly_top    = ly.offsetTop; 
        var ly_right  = ly.offsetLeft + ly.offsetWidth; 
        var ly_bottom = ly.offsetTop + ly.offsetHeight; 

        // ����Ʈ�ڽ��� ��ǥ 
        var el; 

        for (i=0; i<document.forms.length; i++) { 

            for (k=0; k<document.forms[i].length; k++) { 
                el = document.forms[i].elements[k];    
                if (el.type == "select-one") { 
                    var el_left = el_top = 0; 
                    var obj = el; 
                    if (obj.offsetParent) { 
                        while (obj.offsetParent) { 
                            el_left += obj.offsetLeft; 
                            el_top  += obj.offsetTop; 
                            obj = obj.offsetParent; 
                        } 
                    } 
                    el_left  += el.clientLeft; 
                    el_top    += el.clientTop; 
                    el_right  = el_left + el.clientWidth; 
                    el_bottom = el_top + el.clientHeight; 

                    // ��ǥ�� ���� ���̾ ����Ʈ �ڽ��� ħ�������� ����Ʈ �ڽ��� hidden ��Ŵ 
                    if ( (el_left >= ly_left && el_top >= ly_top && el_left <= ly_right && el_top <= ly_bottom) || 
                        (el_right >= ly_left && el_right <= ly_right && el_top >= ly_top && el_top <= ly_bottom) || 
                        (el_left >= ly_left && el_bottom >= ly_top && el_right <= ly_right && el_bottom <= ly_bottom) || 
                        (el_left >= ly_left && el_left <= ly_right && el_bottom >= ly_top && el_bottom <= ly_bottom) ) 
                        el.style.visibility = 'hidden'; 
                } 
            } 
        } 
    } 

    // ���߾��� ����Ʈ �ڽ��� ��� ���̰� �� 
    function selectbox_visible() 
    { 
        for (i=0; i<document.forms.length; i++) { 
            for (k=0; k<document.forms[i].length; k++) { 
                el = document.forms[i].elements[k];    
                if (el.type == "select-one" && el.style.visibility == 'hidden') 
                    el.style.visibility = 'visible'; 
            } 
        } 
    }

	// �־��� ������Ʈ�� style ������Ƽ�� ��´�.
	function ts_getStyle(elem, name){

		// �迭 style[]�� ������Ƽ�� �����ϴ°�

		if(elem.style[name]){
			return elem.style[name];

		// �ƴϸ� IE�� �޼��带 ����Ϸ��� �õ��Ѵ�
		}else if(elem.currentStyle){
			return elem.currentStyle[name];

		// IE�� �޼��带 ����� �� ������, W3C �޼��带 ���
		}else if(document.defaultView && document.defaultView.getComputedStyle){
			// �޼��� textAlign ���,  text-align ������ ������ ����Ѵ�.
			name = name.replace(/([A-Z])/g,"-$1");
			name = name.toLowerCase();

			// style ��ü�� ������ , ������Ƽ�� ���� ��´�
			var s = document.defaultView.getComputedStyle(elem,"");
			return s && s.getPropertyValue(name);

		// �� ���� ���
		}else{
			return null;
		}

	}

	// ������Ʈ�� X(���� ���� ����) ��ǥ�� ã�´�.
	function ts_pageX(elem){
		var p = 0;

		// �θ��� �������� ���ؾ� �Ѵ�.
		while(elem.offsetParent){
			// ���� �θ��� �������� ���Ѵ�.
			p += elem.offsetLeft;

			// ���� �θ� ������Ʈ�� ���� ��, �۾��� ��� �����Ѵ�.
		elem = elem.offsetParent;
		}

		return p;
	}

	// ������Ʈ�� Y(������ ���� ����) ��ǥ�� ã�´�.
	function ts_pageY(elem){
		var p = 0;

		// �θ��� �������� ���ؾ� �Ѵ�.
		while(elem.offsetParent){
			// ���� �θ��� �������� ���Ѵ�.
			p += elem.offsetTop;

			// ���� �θ� ������Ʈ�� ���� ��, �۾��� ��� �����Ѵ�.
		elem = elem.offsetParent;
		}

		return p;
	}

	// ������Ʈ�� X(����) ��ġ�� �����ϴ� �Լ�
	function ts_setX(elem, pos){

		// CSS ������Ƽ �� 'left' �� ���� �����ϰ�, ���� ������ �ȼ��̴�.
		elem.style.left = pos + "px";
	}

	// ������Ʈ�� Y(����) ��ġ�� �����ϴ� �Լ�
	function ts_setY(elem, pos){

		// CSS ������Ƽ �� 'top' �� ���� �����ϰ�, ���� ������ �ȼ��̴�.
		elem.style.top = pos + "px";
	}

	// ������Ʈ�� ���� ����(CSS ��)�� ��´�.
	function ts_getHeight(elem){
		// CSS ���� ���ڷ� ��ȯ�Ͽ� ��´�.
		return parseInt(ts_getStyle(elem, 'height'));
	}

	// ������Ʈ�� ���� �ʺ�(CSS ��)�� ��´�.
	function ts_getWidth(elem){
		// CSS ���� ���ڷ� ��ȯ�Ͽ� ��´�.
		return parseInt(ts_getStyle(elem, 'width'));
	}

	// ������Ʈ�� �������� ����
	function ts_setOpacity(elem, level){
		// filtersr�� �����ϸ�, IE�̱� ������ Alpha ���͸� ���
		if(elem.filters){
			elem.filters.alpha.opacity = level;

		// �� ���� ���� W3C�� opacity ������Ƽ�� ���
		}else{
			elem.style.opacity = level / 100;
		}
	}

	// ������Ʈ�� ���̵��� ȿ�� ���� ����
	function ts_fadeIn(elem,delay){

		// �������� 0���� ����
		ts_setOpacity(elem, 0);

		setTimeout(function(){

			// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
			for (var i =0; i <= 100; i += 1){
				// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
				(function(){
					var pos = i;

					// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
					setTimeout(function(){
						ts_setOpacity(elem,pos);
					}, (pos + 1) * 10);
				})();
			}

		}, delay);

	}

	// ������Ʈ�� ���̵��� ȿ�� ���� �ݱ�
	function ts_fadeInBack(elem){

		// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
		for (var i =0; i <= 100; i += 1){
			// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
			(function(){
				var pos = i;

				// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
				setTimeout(function(){
					ts_setOpacity(elem,100 - pos);
					if(pos == 100)
						elem.style.display = "none";
				}, (pos + 1) * 10);
			})();
		}

	}

	// ������Ʈ�� �����̵� ȿ�� ���� ����
	function ts_slideLeftDown(elem,delay){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var h = ts_getHeight(elem);
		var w = ts_getWidth(elem);

		// 0 �ȼ����� �����̵� �ٿ��� �����Ѵ�.
		elem.style.height = "0px";
		elem.style.width = "0px";

		setTimeout(function(){

			// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
			for (var i =0; i <= 100; i += 1){
				// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
				(function(){
					var pos = i;

					// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
					setTimeout(function(){
						elem.style.height = ((pos / 100) * h) + "px";
					}, (pos + 1) * 20);

					// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
					setTimeout(function(){
						elem.style.width = ((pos / 100) * w) + "px";
					}, (pos + 1) * 20);

				})();
			}

		}, delay);

	}

	// ������Ʈ�� �����̵� ȿ�� ���� �ݱ�
	function ts_slideLeftDownBack(elem){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var h = ts_getHeight(elem);
		var w = ts_getWidth(elem);

		// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
		for (var i =0; i <= 100; i += 1){
			// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
			(function(){
				var pos = i;

				// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
				setTimeout(function(){
					elem.style.height = h - ((pos / 100) * h) + "px";
				}, (pos + 1) * 20);

				// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
				setTimeout(function(){
					elem.style.width = w - ((pos / 100) * w) + "px";
				}, (pos + 1) * 20);

			})();
		}

	}

	// ������Ʈ�� �����̵� ȿ�� ���� ����
	function ts_slideLeft(elem,delay){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var w = ts_getWidth(elem);

		// 0 �ȼ����� �����̵� �ٿ��� �����Ѵ�.
		elem.style.width = "0px";

		setTimeout(function(){

			// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
			for (var i =0; i <= 100; i += 1){
				// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
				(function(){
					var pos = i;

					// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
					setTimeout(function(){
						elem.style.width = ((pos / 100) * w) + "px";
					}, (pos + 1) * 20);

				})();
			}

		}, delay);

	}

	// ������Ʈ�� �����̵� ȿ�� ���� �ݱ�
	function ts_slideLeftBack(elem){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var w = ts_getWidth(elem);

		// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
		for (var i =0; i <= 100; i += 1){
			// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
			(function(){
				var pos = i;

				// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
				setTimeout(function(){
					elem.style.width = w - ((pos / 100) * w) + "px";
					if(pos == 100)
						elem.style.display = "none";
				}, (pos + 1) * 20);

			})();
		}

	}


	// ������Ʈ�� �����̵� ȿ�� ���� ����
	function ts_slideDown(elem,delay){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var h = ts_getHeight(elem);

		// 0 �ȼ����� �����̵� �ٿ��� �����Ѵ�.
		elem.style.height = "0px";
		elem.style.display = "none";

		setTimeout(function(){

			// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
			for (var i =0; i <= 100; i += 1){
				// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
				(function(){
					var pos = i;
				// �ͽ��÷η� height ���� ������ �߰�
				if(i == 0)
					elem.style.display = "block";


					// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
					setTimeout(function(){
						elem.style.height = ((pos / 100) * h) + "px";
					}, (pos + 1) * 20);

				})();
			}

		}, delay);

	}

	// ������Ʈ�� �����̵� ȿ�� ���� �ݱ�
	function ts_slideDownBack(elem){
		// ������Ʈ�� �ִ� ���̸� ���Ѵ�.
		var h = ts_getHeight(elem);

		// �� �ʸ��� ȭ���� �ٲ�� 20 ������¥�� ���ϸ��̼�
		for (var i =0; i <= 100; i += 1){
			// i ���� �ùٸ��� ���� �� �ְ� ũ������ �����
			(function(){
				var pos = i;

				// �־��� �ð��� Ÿ�Ӿƿ��� �߻���Ų��.
				setTimeout(function(){
					elem.style.height = h - ((pos / 100) * h) + "px";
					if(pos == 100)
						elem.style.display = "none";
				}, (pos + 1) * 20);

			})();
		}

	}

	// ������Ʈ�� �����̴� ȿ�� ����
	function ts_flickeringly(elem,delay){

		// ������Ʈ�� �����
		elem.style.display = "none";

		// delay �ð����� �����Ѵ�
		act_tmp = setInterval(function(){

			// ���콺�� �ö���� �����
			elem.onmouseover = function(){
				elem.style.display = "";
				clearInterval(act_tmp);
			}

			// ���� ������Ʈ�� �������� ��Ŵ���� üũ�� �����Ѵ�
			if(elem.style.display == "none"){
				elem.style.display = "";
			}else{
				elem.style.display = "none";
			}

		}, delay);

	}

	function layer_close(id,hiddenWay) {		
		var obj = document.getElementById("expirehours"+ id);
		var tmpid = document.getElementById("pop" + id);
		if (obj.checked == true) {
			set_cookie("it_ck_pop_"+id, "done", obj.value, window.location.host); 
		}
		if(hiddenWay == "ts_slideDownBack"){
			ts_slideDownBack(tmpid);
		}else if(hiddenWay == "ts_slideLeftBack"){
			ts_slideLeftBack(tmpid);
		}else if(hiddenWay == "ts_slideLeftDownBack"){
			ts_slideLeftDownBack(tmpid);
		}else if(hiddenWay == "ts_fadeInBack"){
			ts_fadeInBack(tmpid);
		}else{
			tmpid.style.display = "none";
		}
		selectbox_visible();
	}

}

