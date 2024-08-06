<!-- Rich Editor Template -->
<script type="text/javascript">
var kBaseVarNames = new Array();
var kBaseVarVals = new Array();
<!-- BEGIN BLOCK: select_cfg_vars -->
kBaseVarNames.push('${cfg_name}');
kBaseVarVals.push('${cfg_name}');
<!-- END BLOCK: select_cfg_vars -->
tinyMCE.init({
	// General options
	mode : "specific_textareas",
	editor_selector : "mceEditor",
	theme : "scriptalicious",
    dialog_type:"modal",
	plugins : "codemirror,morebutton,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
    theme_scriptalicious_layout_manager : "customlayout",
    theme_scriptalicious_custom_layout : "scriptaliciousLayout",

	// Theme options
	theme_scriptalicious_buttons1 : "formatselect,|,bold,italic,underline,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,|,link,unlink,image,media,|,forecolor,|,morebutton,codemirror",
	theme_scriptalicious_buttons2 : "fontselect,fontsizeselect,kbasevars|,strikethrough,justifyfull,|,pastetext,pasteword,|,table,|,removeformat,|,charmap,outdent,indent,|,styleprops,image|,help",
    theme_scriptalicious_buttons3: "srcfontdown,srcfontup,|,srcselectall,|,srccut,srccopy,srcpaste",
	theme_scriptalicious_toolbar_location : "top",
	theme_scriptalicious_toolbar_align : "left",
	theme_scriptalicious_statusbar_location : "bottom",
	theme_scriptalicious_resizing : true,
    theme_scriptalicious_resizing_use_cookie : false,
	theme_scriptalicious_resize_horizontal : false,
	theme_scriptalicious_resizing_min_height : 250,
	theme_scriptalicious_resizing_max_height : 800,
    theme_scriptalicious_blockformats : "p,address,h1,h2,h3,h4,h5,h6,blockquote,dt,dd,code,samp",
    theme_scriptalicious_blockformats : "p,address,pre,h1,h2,h3,h4,h5,h6",
    theme_scriptalicious_fonts : "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
    theme_scriptalicious_more_colors : 1,
    theme_scriptalicious_row_height : 23,
	theme_scriptalicious_tabs_location : "top",
    theme_scriptalicious_preview_file : "{richeditor_preview_file}",

    morebutton_mode : 1,
    morebutton_toolbar : 'toolbar2',
    
    apply_source_formatting : false,
    remove_linebreaks: false,
    preformatted : true,
   
	content_css : "{site_url}inc/js/tiny_mce/scriptalicious.css",
});

function moreButtonSwitchImage()
{
    var mb_urlbase = '{site_url}inc/js/tiny_mce/plugins/morebutton/img/';
    var mb_button = 'morebutton.gif';
    var mb_button2 = 'morebutton2.gif';

    var obj = document.getElementById(tinyMCE.activeEditor.id+'_morebutton');
    var imgNode = getFirstChildNodeByTagName(obj,"IMG");

    if(imgNode.src == mb_urlbase+mb_button)
    {
        imgNode.src = mb_urlbase+mb_button2;
    }
    else
    {
        imgNode.src = mb_urlbase+mb_button;
    }
}

function getFirstChildNodeByTagName(parent,tagName)
{
    for (var i=0;i<parent.childNodes.length;i++) {

        if (parent.childNodes[i].tagName == tagName) return parent.childNodes[i];
    }  
    return false;
}
function mceToggleEditor(editor,state)
{

    if(typeof state == "undefined")
    {
        state = (tinyMCE.get(editor)) ? (false) : (true);
    }
    else
    {
    
    }
    
    var ed = tinyMCE.activeEditor;
    var tbId = ed.getParam('morebutton_toolbar', 'toolbar2');
    var tboffset = 0;
    
    ifr = tinymce.DOM.get(ed.id + '_ifr');
    ifrs = tinymce.DOM.get('frame_codemirror');

    if(state == false) {
        
        if(ifrs) {
    		if (ed.getParam('morebutton_mode') == 2) {
                tinymce.DOM.show(ed.controlManager.get(tbId).id);
                tboffset = 28;
            }
            var heights = Math.max(10, parseInt(ifr.style.height));

            ifrs.style.height = (heights+tboffset) + 'px';
        }
        tinyMCE.settings.theme_scriptalicious_layout_mode = 'source';
        tinyMCE.execCommand('mceSwitchTabs', false, editor);
        tinyMCE.execCommand('mceShowSource', false, editor);
        
        var cmEd = ed.plugins.codemirror.CMEditor;

        cmEd.focus();

        cmStatusBar();

    } else {

        if(ifrs) {
    		if (ed.getParam('morebutton_mode') == 2) {
                tinymce.DOM.show(ed.controlManager.get(tbId).id);
                tboffset = -28;
            }
            var heights = Math.max(10, parseInt(ifrs.style.height)); 
            ifr.style.height = (heights+tboffset) + 'px';
        }

        tinyMCE.settings.theme_scriptalicious_layout_mode = 'editor';
        tinyMCE.execCommand('mceSwitchTabs', false, editor);
        tinyMCE.execCommand('mceHideSource', false, editor);

        var pVal = ed.translate('scriptalicious.path') + ': ';
        var oSb = tinymce.DOM.get(ed.id + '_path_row');
        tinymce.DOM.setHTML(oSb, pVal);
    
    }

}
function cmStatusBar(c) {
    var ed = tinyMCE.activeEditor;
    var cmEd = ed.plugins.codemirror.CMEditor;
    
    var cmCur = cmEd.cursorPosition().character;
    var cmLine = cmEd.lineNumber();
    var cmCurStr = ed.translate('codemirror.cm_curpos_line')+cmLine+", "+ed.translate('codemirror.cm_curpos_char')+cmCur;

    cmSetStatusBar(cmCurStr);
}
function cmSetStatusBar(str) {
    var ed = tinyMCE.activeEditor;
    var oSb = tinymce.DOM.get(ed.id + '_path_row');
    tinymce.DOM.setHTML(oSb, str);
}
function mceUpdateContents() {
    var ed = tinyMCE.activeEditor;
    var pg = document.getElementById(tinyMCE.activeEditor.id);

    ifr = tinymce.DOM.get(ed.id + '_ifr');

    if(ifr.style.display == 'none')
    {
        pg.value = ed.plugins.codemirror.CMEditor.getCode();
    }
    else
    {
        pg.value = ed.getContent();        
    }
    return true;
}
</script>

