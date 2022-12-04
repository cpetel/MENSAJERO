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
                try {
                    eval(ac);
                    
                } catch (error) {

                    //console.log(decodifica(getNodeText(id)));
                }

                    
				  break;

				default:
				  break;
				}

				
                
            }
        }
    }
}