
function kConfirm(str) {
    var dlg = document.createElement('div');
    if(dlg) {
        dlg.className = 'k_ui-dialog';
        var width = 300;
        var height = 200;
        
        dlg.style.width = width;
        dlg.style.height = height;

        dlg.style.top = ((document.body.offsetHeight/2) - (height+50))+'px';
        dlg.style.left = ((document.body.offsetWidth/2) - (width/2))+'px';

        var txt = document.createTextNode('Screen width is '+document.body.offsetHeight+'');
        dlg.appendChild(txt);
        document.body.appendChild(dlg);
        
    }
    else
    {
        var agree = confirm(string);
        return agree;
    }
    
    return (agree) ? true : false;
}

