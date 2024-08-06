function checkAll(field)
{
    for (i = 0; i < field.length; i++)
	field[i].checked = true ;
}

function uncheckAll(field)
{
    for (i = 0; i < field.length; i++)
	field[i].checked = false ;
}

function myWindowOpen(url, title)
{
    var myScrollbars = 'yes';
    var myMenubar = 'yes';
    var myHeight = 380;
    var myWidth = 540;
    var myResizable = 'no';
    var myToolbar = 'no';
    var myLocation = 'no';
    var myStatus = 'no';
           
    var myWindow = window.open(url,title,'scrollbars='+myScrollbars+',menubar='+myMenubar+',height='+myHeight+',width='+myWidth+',resizable='+myResizable+',toolbar='+myToolbar+',location='+myLocation+',status='+myStatus);
}   
function breakout()
{
    if (top.location != location)
    {
        top.location.href = document.location.href;
    }
}
function switchVis(objId) {

//    var obj = (document.getElementById) ? document.getElementById(objId).style : ((document.layers) ? document.obj : document.all.obj.style);
    
    if (document.getElementById) { // DOM3 = IE5, NS6
        var obj = document.getElementById(objId).style;
    } else {
        if (document.layers) { // Netscape 4
            obj = document.obj;
        } else { // IE 4
            var obj = document.all.obj.style;
        }
    }
    obj.visibility = ((obj.visibility == 'hidden') || (obj.visibility == 'undefined') || (obj.visibility == '')) ? 'visible' : 'hidden';
    obj.display = ((obj.visibility == 'hidden') || (obj.visibility == 'undefined') || (obj.visibility == '')) ? 'none' : 'block';

}
function k_encode_string(strCode, provider)
{
    if((provider == 'undefined') || (provider == '')) provider = 'Scriptalicious - http://www.scriptalicious.com/'; 
    srcencrypted = escape(strCode);
    var encrypted = "<script language=\"JavaScript\" type=\"text/javascript\">\n";
    encrypted+="<!-- HTML Encryption provided by " + provider + " -->\n<!--\n";
    encrypted+="document.write(unescape('" + srcencrypted + "'));\n";
    encrypted+="//-->\n";
    encrypted+="</script\>";
    return encrypted;
}

function k_encode_convert(srcId, destId, provider)
{
    var code = document.getElementById(srcId).value;
    srcencrypted = k_encode_string(code, provider);
    document.getElementById(destId).value = srcencrypted;

}