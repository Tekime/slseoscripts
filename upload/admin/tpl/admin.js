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
function k_frmSetCheckbox(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;

	if(!countCheckBoxes) {
        if(CheckValue == undefined) {
            CheckValue = (objCheckBoxes.checked) ? false : true; 
        }
		objCheckBoxes.checked = CheckValue;
	} else {
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++) {
            if(CheckValue == undefined) {
                CheckValue = (objCheckBoxes[i].checked) ? false : true; 
            }
			objCheckBoxes[i].checked = CheckValue;
        }
    }
    // Refresh rich checks
    Custom.clear();
}

function chHtml(oId, oHtml)
{
    if (document.getElementById || document.all)
    {
        var obj = document.getElementById? document.getElementById(oId): document.all[oId];
        if (obj && typeof obj.innerHTML != "undefined") obj.innerHTML = oHtml;
    }
}

function chForm(oId, oHtml)
{
    if (document.getElementById || document.all)
    {
        var obj = document.getElementById? document.getElementById(oId): document.all[oId];
        if (obj && typeof obj.value != "undefined") obj.value = oHtml;
    }
}

function fCounter(fldId, ctId, fMax)
{
    if (document.getElementById || document.all)
    {
        var fldObj = document.getElementById? document.getElementById(fldId): document.all[fldId];
        var ctObj = document.getElementById? document.getElementById(ctId): document.all[ctId];
        
        ctObj.value = fldObj.value.length
        
        if(fldObj.value.length >= fMax)
        {
            fldObj.value = fldObj.value.substring(0,fMax-1);
        }

        if (ctObj && typeof ctObj.value == "undefined") ctObj.value = fldObj.value.length;

    }    
}
function getMouseX(e)
{
    var posx = 0;
    if (!e) var e = window.event;
    if (e.pageX) {
        posx = e.pageX;
    }
    else if (e.clientX) {
        posx = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
    }
    return posx;
}
function getMouseY()
{
    var posy = 0;
    if (!e) var e = window.event;
    if (e.pageY) {
        posy = e.pageY;
    }
    else if (e.clientY) {
        posy = e.clientY + document.body.scrollTop  + document.documentElement.scrollTop;
    }
    return posy;
}
function myWindowOpen(url, title, width, height)
{
    var myScrollbars = 'yes';
    var myMenubar = 'yes';
    var myHeight = height;
    var myWidth = width;
    var myResizable = 'no';
    var myToolbar = 'no';
    var myLocation = 'no';
    var myStatus = 'no';
           
    var myWindow = window.open(url,title,'scrollbars='+myScrollbars+',menubar='+myMenubar+',height='+myHeight+',width='+myWidth+',resizable='+myResizable+',toolbar='+myToolbar+',location='+myLocation+',status='+myStatus);
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
    obj.visibility = (obj.visibility == 'hidden') ? 'visible' : 'hidden';
    obj.display = (obj.visibility == 'hidden') ? 'block' : 'none';
}
function setVis(my,obj,vis)
{
    if (document.getElementById) { // DOM3 = IE5, NS6
        el = document.getElementById(obj);

    } else {
        if (document.layers) { // Netscape 4
            el = document.obj;
        } else { // IE 4
            el = document.all.obj;
        }
    }

    if(vis == true)
    {
        el.style.visibility = 'visible';
        el.style.display = 'block';
    }
    else
    {
        el.style.visibility = 'hidden';
        el.style.display = 'none';                       
    }
}

function openWin(url, name, w, h) {

  l = (screen.availWidth-10 - w) / 2;
  t = (screen.availHeight-20 - h) / 2;

  features = "width="+w+",height="+h+",left="+l+",top="+t;
  features += ",screenX="+l+",screenY="+t;
  features += ",scrollbars=1,resizable=1,location=0";
  features += ",menubar=0,toolbar=0,status=0";

  window.open(url, name, features);
}

function confirmSubmit(string)
{
    var agree=confirm(string);
    if (agree)
    	return true ;
    else
    	return false ;
}

function sbFocus(obj) {
    if (document.getElementById) { // DOM3 = IE5, NS6
        document.getElementById(obj).focus()
    } else {
        if (document.layers) { // Netscape 4
            document.obj.focus()
        } else { // IE 4
            document.document.all.obj.focus()
        }
    }
}

function format_safeurl(usource,udest,lc,md) {
    if (!lc) lc = true;
    if (!md) md = false;
    
    var oSource = document.getElementById(usource);
    var oDest = document.getElementById(udest);

    if(oDest.disabled == true) return false;
    var s = oSource.value;
    var word_list = new Array();
    var word_list = s.split(/[^a-zA-Z0-9]/);
    var t = '';
    var re = new RegExp ('"', 'gi') ;
        
    for (i=0;i<word_list.length;i++)
    {
        var newstr = word_list[i];
        newstr = newstr.replace(re, '');
        if(t) {
            t = t + '-' + newstr;
        } else {
            t = newstr;        
        }
    }

    if(lc) t = t.toLowerCase();
    if(md) t = t.replace(/-+/g,"-");

    oDest.value = t;
}

function findPosX(obj)
{
	var curleft = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curleft += obj.offsetLeft
			obj = obj.offsetParent;
		}
	}
	else if (obj.x)
		curleft += obj.x;
	return curleft;
}

function findPosY(obj)
{
	var curtop = 0;
	if (obj.offsetParent)
	{
		while (obj.offsetParent)
		{
			curtop += obj.offsetTop
			obj = obj.offsetParent;
		}
	}
	else if (obj.y)
		curtop += obj.y;
	return curtop;
}

function show(oDest, oSrc) {
    document.getElementById(oDest).innerHTML=opener.document.getElementById(oSrc).value;
}




/*
bool switchSubCheckbox(string parent, string child)
 - Disables and checks the child checkbox if the parent is unchecked.
 - Extend this to handle generic parameters
*/
function switchSubCheckbox(parent, child) {
    if (document.getElementById(parent).checked == false) {
        document.getElementById(child).checked = true;
        document.getElementById(child).disabled = true;        
    } else if (document.getElementById(parent).checked == true) {
        document.getElementById(child).checked = false;
        document.getElementById(child).disabled = false;    
    } else {
        return false;
    }
    return true;
}

function openWindow(url, name) {
popupWin = window.open(url,name,'_blank,resizable=1,location=0,scrollbars=1,toolbar=0,personalbar=0,status=1,width=800,height=640,left=15,top=5');
window.name = 'popupWin';
popupWin.focus();
}


var kDateMonths = new Array();
kDateMonths[0] = 'January';
kDateMonths[1] = 'February';
kDateMonths[2] = 'March';
kDateMonths[3] = 'April';
kDateMonths[4] = 'May';
kDateMonths[5] = 'June';
kDateMonths[6] = 'July';
kDateMonths[7] = 'August';
kDateMonths[8] = 'September';
kDateMonths[9] = 'October';
kDateMonths[10] = 'November';
kDateMonths[11] = 'December';

var kDateSuffix = new Array();
kDateSuffix[0] = 'th';
kDateSuffix[1] = 'st';
kDateSuffix[2] = 'nd';
kDateSuffix[3] = 'rd';
kDateSuffix[4] = 'th';
kDateSuffix[5] = 'th';
kDateSuffix[6] = 'th';
kDateSuffix[7] = 'th';
kDateSuffix[8] = 'th';
kDateSuffix[9] = 'th';



function runMiniClock()
{
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    var month = time.getMonth();
    var day = time.getDate();
    var year = time.getUTCFullYear();
    
    minutes=((minutes < 10) ? "0" : "") + minutes;
    ampm = (hours >= 12) ? "PM" : "AM";
    hours=(hours > 12) ? hours-12 : hours;
    hours=(hours == 0) ? 12 : hours;
    daystr = ""+day;
    lastday = daystr.charAt(daystr.length-1);
    var datestr = kDateMonths[month] + " " + day + kDateSuffix[lastday];
    var clock = hours + ":" + minutes + " " + ampm;
    if(clock != document.getElementById('miniclock').innerHTML) document.getElementById('miniclock').innerHTML = clock;
    timer = setTimeout("runMiniClock()",1000);
}

function identifyBrowser()
{
  var agent = navigator.userAgent.toLowerCase();

  if (typeof navigator.vendor != "undefined" &&
      navigator.vendor == "KDE" &&
      typeof window.sidebar != "undefined")
  {
    return "kde";
  }
  else if (typeof window.opera != "undefined")
  {
    var version = parseFloat(
        agent.replace(/.*opera[\/ ]([^ $]+).*/, "$1"));

    if (version >= 7)
    {
      return "opera7";
    }
    else if (version >= 5)
    {
      return "opera5";
    }

    return false;
  }
  else if (typeof document.all != "undefined")
  {
    if (typeof document.getElementById != "undefined")
    {
      var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, "$1").
          replace(/ /, "");

      if (typeof document.uniqueID != "undefined")
      {
        if (browser.indexOf("5.5") != -1)
        {
          return browser.replace(/(.*5\.5).*/, "$1");
        }
        else
        {
          return browser.replace(/(.*)\..*/, "$1");
        }
      }
      else
      {
        return "ie5mac";
      }
    }

    return false;
  }
  else if (typeof document.getElementById != "undefined")
  {
    if (navigator.vendor.indexOf("Apple Computer, Inc.") != -1)
    {
      if (typeof window.XMLHttpRequest != "undefined")
      {
        return "safari1.2";
      }

      return "safari1";
    }
    else if (agent.indexOf("gecko") != -1)
    {
      return "mozilla";
    }
  }
  return false;
}

function identifyOS()
{
  var agent = navigator.userAgent.toLowerCase();

  if (agent.indexOf("win") != -1)
  {
    return "win";
  }
  else if (agent.indexOf("mac") != -1)
  {
    return "mac";
  }
  else
  {
    return "unix";
  }

  return false;
}

function submitRichForm(obj) {
    var ed = tinyMCE.activeEditor;
    var pg = document.getElementById(ed.id);
    ed.setProgressState(true);

    ifr = tinymce.DOM.get(ed.id + '_ifr');
    if(ifr.style.display == 'none')
    {
        ed.setContent(ed.plugins.codemirror.CMEditor.getCode(), {format : 'raw'});
    }
    pg.value = ed.getContent();
    submitkForm();
 //   t=setTimeout("submitkForm()",1500);
}

function submitkForm() {
    document.getElementById('kForm').submit();
}

/* Cross-browser event registration from: http://www.scottandrew.com/weblog/articles/cbs-events */
function addEvent(obj, evType, fn, useCapture){
 obj = document.getElementById(obj);
  if (obj.addEventListener){
    obj.addEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.attachEvent){
    var r = obj.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be attached");
  }
}

/* From: http://www.scottandrew.com/weblog/articles/cbs-events */
function removeEvent(obj, evType, fn, useCapture){
 obj = document.getElementById(obj);
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
} 

function kFxFoldUp(objid)
{
    obj = document.getElementById(objid);
    obj.style.height = '100px';
    for(kx=100;kx>0;kx=kx-1)
    {
        obj.style.height = kx+'px';
    }
}