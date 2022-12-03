var req;
var isIE;
function daleSVG(evt,t) {
	   //alert("diste=" + evt);
	   //var x;
	   //var SVGDocument = evt.target.ownerDocument;
       //var SVGRoot     = SVGDocument.documentElement;
	   //x=evt.target.getAttribute("opacity");
	   //if (x===".75"){x="0.25"} else {x=".75"};
	   //evt.target.setAttribute("opacity",x);
	   llamaSVG(t.id);
}

function init() {
	llamaBarrios();
	inicia_timer();
}

function inicia_timer() {
	
	setInterval(llama_timer,1000);
//	llama_timer();
}

function llama_timer() {
        var url = "php/getbarrios.php?action=timer";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
	
}


function dale(t) {
	llamaBarrio(t.id);
}

function llamaSVG(id) {
        var url = "php/getbarrios.php?action=toco&tipo=svg&id=" + id +"";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}

function llamaBoton(t) {
        var url = "php/getbarrios.php?action=toco&tipo=boton&id=" + t.id +"";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}

function llamaMotivo(t) {
        var url = "php/getbarrios.php?action=toco&tipo=motivo&id=" + escape(t.value) +"&index="+ t.selectedIndex;
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}


function llamahora(t) {
        var url = "php/getbarrios.php?action=toco&tipo=hora&id=" + t.value +"";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}


function llamaminutos(t) {
        var url = "php/getbarrios.php?action=toco&tipo=minutos&id=" + t.value +"";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}


function llamaOpcion(t) {
        var url = "php/getbarrios.php?action=toco&tipo=opcion&id=" + t.id +"";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}


function llamaBarrio(id) {
        var url = "php/getbarrios.php?action=toco&tipo=barrio&id=" + id;
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}

function llamaBarrios() {
        var url = "php/getbarrios.php?action=init";
        req = initRequest();
        req.open("GET", url, true);
        req.onreadystatechange = callback;
        req.send(null);
}

function initRequest() {
    if (window.XMLHttpRequest) {
        if (navigator.userAgent.indexOf('MSIE') != -1) {
            isIE = true;
        }
        return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        isIE = true;
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
}

function callback() {

    if (req.readyState == 4) {
        if (req.status == 200) {
            parseMessages(req.responseXML);
            
        }
    }
}

// Firefox splits large text regions into multiple Text objects (4096 chars in
// each). Glue it back together.
function getNodeText(node) {
    var r = "";
    for (var x = 0;x < node.childNodes.length; x++) {
        r = r + node.childNodes[x].nodeValue;
    }
    return r;
}
function decodifica (data) {
	return hex2bin (data);
}

function hex2bin(hex)
{
    var bytes = [], str;

    for(var i=0; i< hex.length-1; i+=2)
        bytes.push(parseInt(hex.substr(i, 2), 16));

    return String.fromCharCode.apply(String, bytes);    
}


function json_decode (str_json) {
    // Decodes the JSON representation into a PHP value  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/json_decode    // +      original by: Public Domain (http://www.json.org/json2.js)
    // + reimplemented by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      improved by: T.J. Leahy
    // +      improved by: Michael White
    // *        example 1: json_decode('[\n    "e",\n    {\n    "pluribus": "unum"\n}\n]');    // *        returns 1: ['e', {pluribus: 'unum'}]
/*
        http://www.JSON.org/json2.js
        2008-11-19
        Public Domain.        NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
        See http://www.JSON.org/js.html
    */
 
    var json = this.window.JSON;    if (typeof json === 'object' && typeof json.parse === 'function') {
        try {
            return json.parse(str_json);
        } catch (err) {
            if (!(err instanceof SyntaxError)) {                throw new Error('Unexpected error type in json_decode()');
            }
            this.php_js = this.php_js || {};
            this.php_js.last_error_json = 4; // usable by json_last_error()
            return null;        }
    }
 
    var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
    var j;    var text = str_json;
 
    // Parsing happens in four stages. In the first stage, we replace certain
    // Unicode characters with escape sequences. JavaScript handles many characters
    // incorrectly, either silently deleting them, or treating them as line endings.    cx.lastIndex = 0;
    if (cx.test(text)) {
        text = text.replace(cx, function (a) {
            return '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
        });    }
 
    // In the second stage, we run the text against regular expressions that look
    // for non-JSON patterns. We are especially concerned with '()' and 'new'
    // because they can cause invocation, and '=' because it can cause mutation.    // But just to be safe, we want to reject all unexpected forms.
    // We split the second stage into 4 regexp operations in order to work around
    // crippling inefficiencies in IE's and Safari's regexp engines. First we
    // replace the JSON backslash pairs with '@' (a non-JSON character). Second, we
    // replace all simple value tokens with ']' characters. Third, we delete all    // open brackets that follow a colon or comma or that begin the text. Finally,
    // we look to see that the remaining characters are only whitespace or ']' or
    // ',' or ':' or '{' or '}'. If that is so, then the text is safe for eval.
    if ((/^[\],:{}\s]*$/).
    test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').    replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
    replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
 
        // In the third stage we use the eval function to compile the text into a
        // JavaScript structure. The '{' operator is subject to a syntactic ambiguity        // in JavaScript: it can begin a block or an object literal. We wrap the text
        // in parens to eliminate the ambiguity.
        j = eval('(' + text + ')');
 
        return j;    }
 
    this.php_js = this.php_js || {};
    this.php_js.last_error_json = 4; // usable by json_last_error()
return null;
}

function base64_decode (data) {
    // Decodes string using MIME base64 algorithm  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/base64_decode    // +   original by: Tyler Akins (http://rumkin.com)
    // +   improved by: Thunder.m
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman    // +   bugfixed by: Pellentesque Malesuada
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // -    depends on: utf8_decode    // *     example 1: base64_decode('S2V2aW4gdmFuIFpvbm5ldmVsZA==');
    // *     returns 1: 'Kevin van Zonneveld'
    // mozilla has this native
    // - but breaks in 2.0.0.12!
    //if (typeof this.window['btoa'] == 'function') {    //    return btoa(data);
    //}
    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
        ac = 0,        dec = "",
        tmp_arr = [];
 
    if (!data) {
        return data;    }
 
    data += '';
 
    do { // unpack four hexets into three octets using index points in b64        h1 = b64.indexOf(data.charAt(i++));
        h2 = b64.indexOf(data.charAt(i++));
        h3 = b64.indexOf(data.charAt(i++));
        h4 = b64.indexOf(data.charAt(i++));
         bits = h1 << 18 | h2 << 12 | h3 << 6 | h4;
 
        o1 = bits >> 16 & 0xff;
        o2 = bits >> 8 & 0xff;
        o3 = bits & 0xff; 
        if (h3 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1);
        } else if (h4 == 64) {
            tmp_arr[ac++] = String.fromCharCode(o1, o2);        } else {
            tmp_arr[ac++] = String.fromCharCode(o1, o2, o3);
        }
    } while (i < data.length);
     dec = tmp_arr.join('');
    dec = this.utf8_decode(dec);
 
    return dec;
}

function utf8_decode (str_data) {
    // Converts a UTF-8 encoded string to ISO-8859-1  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/utf8_decode    // +   original by: Webtoolkit.info (http://www.webtoolkit.info/)
    // +      input by: Aman Gupta
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Norman "zEh" Fuchs
    // +   bugfixed by: hitwork    // +   bugfixed by: Onno Marsman
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: utf8_decode('Kevin van Zonneveld');
    // *     returns 1: 'Kevin van Zonneveld'    
	var tmp_arr = [],
        i = 0,
        ac = 0,
        c1 = 0,
        c2 = 0,        c3 = 0;
 
    str_data += '';
 
    while (i < str_data.length) {        c1 = str_data.charCodeAt(i);
        if (c1 < 128) {
            tmp_arr[ac++] = String.fromCharCode(c1);
            i++;
        } else if (c1 > 191 && c1 < 224) {            c2 = str_data.charCodeAt(i + 1);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 31) << 6) | (c2 & 63));
            i += 2;
        } else {
            c2 = str_data.charCodeAt(i + 1);            c3 = str_data.charCodeAt(i + 2);
            tmp_arr[ac++] = String.fromCharCode(((c1 & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
            i += 3;
        }
    } 
    return tmp_arr.join('');
}

function parseMessages(responseXML) {

    // no matches returned
    if (responseXML == null) {
        return false;
    } else {

        var ids = responseXML.getElementsByTagName("ids")[0];

        if (ids.childNodes.length > 0) {

            for (loop = 0; loop < ids.childNodes.length; loop++) {
                var id = ids.childNodes[loop];
				switch(id.tagName)
				{
				case "objeto":
					var idhtml = id.getElementsByTagName("html")[0];
					var oi = decodifica(getNodeText(idhtml));
					document.getElementById(id.getElementsByTagName("id")[0].textContent).innerHTML=oi;
				  break;

				case "accion":
                    
					var ac=decodifica(getNodeText(id));
                    console.log(ac);
                    eval(ac);
                    console.log(eval(ac));
				  break;

				default:
				  break;
				}

				
                
            }
        }
    }
}