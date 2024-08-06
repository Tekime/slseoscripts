(function() {

//	var DOM = tinymce.DOM;

	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('codemirror');

	tinymce.create('tinymce.plugins.CodeMirror', {
		
		init : function(ed, url) {
			var t = this;
			t.editor = ed;
			this.cmurl = url;
            this.areaid = 'codemirror';
            this.areactl = false;
            this.baseurl = url + '/jscript/codemirror/';

			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', {src:url + '/jscript/codemirror/js/codemirror.js'});
			tinymce.DOM.loadCSS(url + '/css/codemirror.css');

			ed.addCommand('mceShowSource', t._showSourceEditor, t);
			ed.addCommand('mceHideSource', t._hideSourceEditor, t);
			ed.onNodeChange.add(t._nodeChange, t);

            ed.addCommand('CM_FontSize', function(ui, v) {
                
                var cmFrame = tinymce.DOM.get('frame_codemirror');
                
                if(document.frames) {
                    var cmBody = document.frames('frame_codemirror').document.body
                } else if (cmFrame.contentDocument) {
                    var cmDoc = cmFrame.contentDocument;
                    var cmBody = cmDoc.getElementsByTagName('body')[0]
                } else if (cmFrame.contentWindow) {
                    var cmDoc = cmFrame.contentWindow;
                    var cmBody = cmDoc.getElementsByTagName('body')[0]
                }

                var defFs = ed.getParam('codemirror_default_font_size', 11);
                var maxFs = ed.getParam('codemirror_max_font_size', 18);
                var minFs = ed.getParam('codemirror_min_font_size', 5);
                
                if(cmBody.style.fontSize == "") {
                    cSize = defFs;
                } else {
                    var cSize = Math.max(10, parseInt(cmBody.style.fontSize));
                }

                cSize = cSize + v;
                 
                if(cSize <= maxFs) {
                    var lSize = (cSize + 2) + "px";
                    var nSize = (cSize) + "px";

                    cmBody.style.fontSize = nSize;
                    cmBody.style.lineHeight = lSize;
                }
                
                t._updateEditRow();
            });
            
            ed.addCommand('CM_SelectAll', function() {
                var hFirstLine = t.CMEditor.firstLine();
                var hLastLine = t.CMEditor.lastLine();
                var hLastLineContents = t.CMEditor.lineContent(hLastLine);
                t.CMEditor.selectLines(hFirstLine,0,hLastLine,hLastLineContents.length);
                
            });
            
            ed.addCommand('CM_Cut', function() {
                // Get the selected text
                var cmSelection = t.CMEditor.selection();

                // Clear the selected text
                t.CMEditor.replaceSelection('');
                alert('Cut');
            });
            
            ed.addCommand('CM_Copy', function() {
                var ced = tinyMCE.activeEditor;

                if (ced.clipboardData)
                {
                    tx = ced.clipboardData.getData('text/plain');
                    alert('ced.clipboardData: '+tx);
                }
                else if (tinymce.isIE)
                {
                    tx = t.getWin().clipboardData.getData('Text');
                    alert('getWin().clipboardData: '+tx);
                }
                else
                {
                    alert('No clipboard data');
                }

            });
            ed.addCommand('CM_Paste', function() {
                cmStatusBar();
            });
            
            ed.addButton('srcfontup', {
            title : 'codemirror.cm_fontup',
            cmd : 'CM_FontSize',
            value : 1,
            image : url + '/img/ico_cm-fontup.gif'
            });
            
            ed.addButton('srcfontdown', {
            title : 'codemirror.cm_fontdown',
            cmd : 'CM_FontSize',
            value : -1,
            image : url + '/img/ico_cm-fontdown.gif'
            });

            ed.addButton('srcselectall', {
            title : 'codemirror.cm_selectall',
            'class' : 'mceIcon mce_selectall',
            cmd : 'CM_SelectAll'
            });

            /*
            ed.addButton('srccut', {
            title : 'codemirror.cm_cut',
            'class' : 'mceIcon mce_cut',
            cmd : 'CM_Cut'
            });

            ed.addButton('srccopy', {
            title : 'codemirror.cm_copy',
            'class' : 'mceIcon mce_copy',
            cmd : 'CM_Copy'
            });
            
            ed.addButton('srcpaste', {
            title : 'codemirror.cm_paste',
            'class' : 'mceIcon mce_paste',
            cmd : 'CM_Paste'
            });
            */
            
		},

		getInfo : function() {
			return {
				longname : 'CodeMirror for TinyMCE',
				author : 'Scriptalicious',
				authorurl : 'http://www.scriptalicious.com/',
				infourl : 'http://www.scriptalicious.com/',
				version : '1.0'
			};
		},

		_nodeChange : function(ed, cm, n) {
			var ed = this.editor;
			//not used for the moment
		},

        _updateEditRow : function()
        {
            var t = this, ed = t.editor;
            var cmEd = t.CMEditor;
            var cmCur = cmEd.cursorPosition().character+1;
            var cmLine = cmEd.lineNumber(cmEd.cursorPosition().line);                           
            var cmCurStr = "Line: "+cmLine+", Char: "+cmCur;
            
            var cmFrame = tinymce.DOM.get('frame_codemirror');
            var cmDoc = cmFrame.document || cmFrame.contentDocument || cmFrame.contentWindow;
            var cmBody = cmDoc.getElementsByTagName('body')[0];

            /*
            if(cmDoc.getElementById('editrowbg')) {   
                var cmRow = cmDoc.getElementById('editrowbg');
            } else {
                var cmRow = cmDoc.createElement('div');
                cmRow.setAttribute('class', 'editrowbg');
                cmRow.setAttribute('id', 'editrowbg');
                cmRow.innerHTML = "&nbsp;"; 
                cmBody.appendChild(cmRow);
            }
            */
            
            var cSize = (!cmBody.style.fontSize) ? ed.getParam('codemirror_default_font_size', 11) : Math.max(10, parseInt(cmBody.style.fontSize));
            var rSize = cSize + 2;
            var newPos = (cmLine-1) * rSize;

//            cmRow.style.top = newPos + "px";
//            cmRow.style.height = rSize + "px";

            cmSetStatusBar(cmCurStr);
        
        },
        
		_showSourceEditor : function()
		{
            var n, t = this, ed = t.editor, s = t.settings, r, mf, me, td;
            var cac = ed.getContentAreaContainer();
            
            var tbId = ed.getParam('morebutton_toolbar', 'toolbar2');
            
            tinymce.DOM.hide(ed.controlManager.get('toolbar1').id);
    		if (ed.getParam('morebutton_mode') == 2) {
                tinymce.DOM.hide(ed.controlManager.get(tbId).id);
            }

            var tbId = ed.getParam('codemirror_toolbar', 'toolbar3');
            tinymce.DOM.show(ed.controlManager.get(tbId).id);                

            var cmsource = ed.getContent();
            if(!t.areactl)
            {
                // Add source editor textarea control
                var cac = ed.getContentAreaContainer();
                
                mw = cac.firstChild.style.width;
                mh = cac.firstChild.style.height;
                t.areactl = tinymce.DOM.add(cac, 'textarea', {id : t.areaid});
                t.CMEditor = CodeMirror.fromTextArea(t.areaid, {
                    height: mh,
                    width: mw,
                    parserfile: ["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"],
                    stylesheet: [t.baseurl+"css/xmlcolors.css", t.baseurl+"css/jscolors.css", t.baseurl+"css/csscolors.css"],
                    path: t.baseurl + "js/",
                    lineNumbers : true,
                    indentUnit : 4,
                    tabMode : 'shift',
                    content : cmsource,
                    cursorActivity : function(c) {
                            t._updateEditRow();
                    },
                    textWrapping : false
                });
                t.CMEditor.frame.id = 'frame_'+t.areaid;
                t.CMEditor.frame.style.width = mw;
                t.CMEditor.frame.style.height = mh;
                t.CMEditor.frame.className = 'CodeMirrorFrame';            
            }

            tinymce.DOM.hide(cac.firstChild);
            t.CMEditor.setCode(cmsource);
            tinymce.DOM.show(t.CMEditor.frame);
		},
        
		_hideSourceEditor : function()
		{
            var n, t = this, ed = t.editor, s = t.settings, r, mf, me, td;
            var cac = ed.getContentAreaContainer();
            var tbId = ed.getParam('morebutton_toolbar', 'toolbar2');

            var tboffset = 0;
            
            tinymce.DOM.show(ed.controlManager.get('toolbar1').id);

    		if (ed.getParam('morebutton_mode') == 2) {
                tinymce.DOM.show(ed.controlManager.get(tbId).id);
            }
            
            var tbId = ed.getParam('codemirror_toolbar', 'toolbar3');
            tinymce.DOM.hide(ed.controlManager.get(tbId).id);                

        	ed.setContent(t.CMEditor.getCode(), {format : 'raw'});
            tinymce.DOM.hide(t.CMEditor.frame);
            tinymce.DOM.show(cac.firstChild);
		},
        
		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, dy) {
			var ifr = ed.getContentAreaContainer().firstChild;

			tinymce.DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
			ed.theme.deltaHeight += dy; // For resize cookie

            var ifrs = tinymce.DOM.get('frame_codemirror');
            if(ifrs)
    			tinymce.DOM.setStyle(ifrs, 'height', ifrs.clientHeight + dy); // Resize iframe

		},
        
        _resizeEditor : function()
        {
            mw = ed.getContentAreaContainer().firstChild.style.width;
            mh = ed.getContentAreaContainer().firstChild.style.height;
        }
	});

	// Register plugin
	tinymce.PluginManager.add('codemirror', tinymce.plugins.CodeMirror);
})();