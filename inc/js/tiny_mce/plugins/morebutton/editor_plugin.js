/**
 * More Buttons Plugin - Adds a nifty button to show/hide toolbars
 *
 * @author Gabriel Harper (http://www.gabrielharper.com)
 */
 
(function() {
	var DOM = tinymce.DOM;

	// Load plugin specific language pack
	tinymce.PluginManager.requireLangPack('morebutton');

	tinymce.create('tinymce.plugins.moreButton', {
		init : function(ed, url) {
			var t = this, tbId = ed.getParam('morebutton_toolbar', 'toolbar2');
			var mbMode = ed.getParam('morebutton_mode', 1);

			// Hides the specified toolbar and resizes the iframe
			ed.onPostRender.add(function() {
				if (ed.getParam('morebutton_mode', 1)) {
					DOM.hide(ed.controlManager.get(tbId).id);
					t._resizeIframe(ed, tbId, 28);
				}
			});

			ed.addCommand('MB_More', function() {
				var id = ed.controlManager.get(tbId).id, cm = ed.controlManager;

                moreButtonSwitchImage();

				if (DOM.isHidden(id)) {
					cm.setActive('mb_more', 1);
					DOM.show(id);
					t._resizeIframe(ed, tbId, -28);
					ed.settings.morebutton_mode = 2;
				} else {
					cm.setActive('mb_more', 0);
					DOM.hide(id);
					t._resizeIframe(ed, tbId, 28);
					ed.settings.morebutton_mode = 1;
				}

			});
            
			ed.addButton('morebutton', {
				title : 'morebutton.mb_more_desc',
				image : url + '/img/morebutton.gif',
				cmd : 'MB_More'
			});

		},

		getInfo : function() {
			return {
				longname : 'More Button',
				author : 'Gabriel Harper (Intavant)', // add Moxiecode?
				authorurl : 'http://www.gabrielharper.com/',
				infourl : 'http://www.intavant.com/',
				version : '1.0'
			};
		},
        
		// Resizes the iframe by a relative height value
		_resizeIframe : function(ed, tb_id, dy) {
			var ifr = ed.getContentAreaContainer().firstChild;

			DOM.setStyle(ifr, 'height', ifr.clientHeight + dy); // Resize iframe
			ed.theme.deltaHeight += dy; // For resize cookie

            var ifrs = tinymce.DOM.get('frame_codemirror');
            if(ifrs)
    			DOM.setStyle(ifrs, 'height', ifrs.clientHeight + dy); // Resize iframe

		}

	});

	// Register plugin
	tinymce.PluginManager.add('morebutton', tinymce.plugins.moreButton);
})();
