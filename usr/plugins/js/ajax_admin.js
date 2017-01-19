function ajaxEngine(replaceID, type, js) {
	var xmlhttp = false;
	/*if(type != 2)
	{
		gid(replaceID).innerHTML = '<div align="center"><img src="media/loading.gif" /></div>';
	}*/
	
	try {
		xmlhttp = new XMLHttpRequest();
	} 
	catch (e) {
		try {
		xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
		}
		catch (e) {
		xmlhttp = new ActiveXObject('Msxml2.XMLHTTP');
		}
	}


	xmlhttp.onreadystatechange = function()	{
		if (xmlhttp.readyState == 4) {
			if (xmlhttp.status == 200) {
				if(type == 2)
				{
					document.getElementById(replaceID).value = xmlhttp.responseText;
					
					eval(js);
					
				}
				else
				{
					document.getElementById(replaceID).innerHTML = xmlhttp.responseText;
					
					eval(js);
					
				}
			} else {
				//alert('AJAX: ошибка выполнения!');
				return;
			}
		}
	}

	return xmlhttp;
}

function fixedEncodeURIComponent (str) {
	var newString = str.replace(/\//g,"|");
  return encodeURIComponent(newString).replace(/[!'()*]/g, escape);
}


function ajaxGet(uri, id) {
	var xmlhttp = ajaxEngine(id);
	var link = uri;

	xmlhttp.open('GET', link, true);
	xmlhttp.setRequestHeader('X_REQUESTED_WITH', 'XMLHttpRequest');
	xmlhttp.send(null);
}

function ajaxSuccess (js) {
  eval(js);
}

function ajaxGetJS(uri, js, id) {
	var xmlhttp = ajaxEngine(id, null, js);
	var link = uri;
	var code = js;

	xmlhttp.open('GET', link, true);
	xmlhttp.setRequestHeader('X_REQUESTED_WITH', 'XMLHttpRequest');	
	xmlhttp.send(null);
	// setTimeout(function() {
    //  xmlhttp.onload = demoHighCharts.init();
  //  }, 2000);
	
}

function ajaxQuery(uri, id, data, type) {
	var xmlhttp = ajaxEngine(id, type);
	var link = uri;

	xmlhttp.open('GET', link, true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(data);
}

function ajaxPost(uri, id, data, type) {
	var xmlhttp = ajaxEngine(id, type);
	var link = uri + '/rand-' + Math.random();

	xmlhttp.open('POST', link, true);
	xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	xmlhttp.send(data);
}

function genPreview(id_title, id_short, id_full)
{
	tinymce.triggerSave();

	var _title = gid(id_title).value;
	var _shortNews = tinyMCE.get(id_short).getContent();
	var _fullNews = tinyMCE.get(id_full).getContent();
	
	if(_title == '')
	{
		notif('warning', lang.info, lang.preview_title);
	}
	else
	{		
		$.fancybox( {href : '/ajax.php?do=getPreview&title='+encodeURIComponent(_title)+'&shortNews='+encodeURIComponent(_shortNews)+'&fullNews='+encodeURIComponent(_fullNews), title : lang.preview, type : 'ajax', maxWidth: 880, maxHeight: 600, fitToView: false,	width: '70%', height: '70%', autoSize: false, closeClick: false} );
	}
}


function inputTags(tags, id, input)
{
	arr = tags.split(',');
	if(arr[(arr.length-1)].length > 1) 
	{
		if(gid(id).style.display=="none")
		{
			showhide(id);
		}

		ajaxGet('ajax.php?do=inputTags&query='+tags+'&input='+input, id);
	}
}

function EditTitle(div, mod, id, subarr)
{
	var tempText = gid(div).innerHTML;
	
	if(subarr)
	{
		var text = gid(subarr).innerHTML;
	}
	else
	{
		var text = gid(div).innerHTML;
	}
	
	text = text.replace(/"/g, '&quot;');

	gid(div).innerHTML = '<input type="text" id="inputEdit_' + id + '" value="' + text + '" style="width:90%" class="EditTitle" />';
	gid('inputEdit_' + id).focus();
	gid('inputEdit_' + id).onblur = function()
	{
		gid(div).innerHTML = tempText;
		gid(div).onclick = function()
		{
			EditTitle(div, mod, id, subarr);
		}
	};
	
	gid('inputEdit_' + id).onkeypress = function(e)
	{
		e = e ? e : event;
		if (e.keyCode == 13 || e.keyCode == 10)
		{
			SaveTitle(mod, id, this.value);
			return false;
		}
	}

	gid(div).onclick = function(){}
}

function getTranslit(text, input)
{
	if(text.length > 0)
	{
		var data = "query=" + encodeURIComponent(text);
		ajaxPost('ajax.php?do=getTranslit', input, data, 2);
	}
	else
	{
		gid(input).value = '';
	}
}