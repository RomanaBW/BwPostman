/**
 * BwPostman Newsletter Component
 *
 * BwPostman JImageManager behavior for media, based on joomla com_media.
 *
 * @version			2.0.0 bwpm
 * @package			BwPostman-Admin
 * @author			Romana Boldt
 * @copyright		(C) 2012-2017 Boldt Webservice <forum@boldt-webservice.de>
 * @support			https://www.boldt-webservice.de/en/forum-en/bwpostman.html
 * @license			GNU/GPL v3, see LICENSE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

(function($) {
var ImageManager = this.ImageManager = {
	initialize: function()
	{
		o = this._getUriObject(window.self.location.href);
		q = this._getQueryObject(o.query);
		this.editor = decodeURIComponent(q['e_name']);

		// Setup image manager fields object
		this.fields			= new Object();
		this.fields.url		= document.getElementById("f_url");
		this.fields.alt		= document.getElementById("f_alt");
		this.fields.align	= document.getElementById("f_align");
		this.fields.title	= document.getElementById("f_title");
		this.fields.caption	= document.getElementById("f_caption");
		this.fields.c_class	= document.getElementById("f_caption_class");

		// Setup image listing objects
		this.folderlist = document.getElementById('folderlist');

		this.frame		= window.frames['imageframe'];
		this.frameurl	= this.frame.location.href;

		// Setup image listing frame
		this.imageframe = document.getElementById('imageframe');
		this.imageframe.manager = this;
		$(this.imageframe).on('load', function(){ ImageManager.onloadimageview(); });

		// Setup folder up button
		this.upbutton = document.getElementById('upbutton');
		$(this.upbutton).off('click');
		$(this.upbutton).on('click', function(){ ImageManager.upFolder(); });
	},

	onloadimageview: function()
	{
		// Update the frame url
		this.frameurl = this.frame.location.href;

		var folder = this.getImageFolder();
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if (folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				if (this.folderlist.className.test(/\bchzn-done\b/)) {
					$(this.folderlist).trigger('liszt:updated');
				}
				break;
			}
		}

		a = this._getUriObject($('#uploadForm').attr('action'));
		q = this._getQueryObject(a.query);
		q['folder'] = folder;
		var query = [];
		for (var k in q) {
			var v = q[k];
			if (q.hasOwnProperty(k) && v !== null) {
				query.push(k+'='+v);
			}
		}
		a.query = query.join('&');
		var portString = '';
		if (typeof(a.port) !== 'undefined' && a.port != 80) {
			portString = ':'+a.port;
		}
		$('#uploadForm').attr('action', a.scheme+'://'+a.domain+portString+a.path+'?'+a.query);
	},

	getImageFolder: function()
	{
		var url 	= this.frame.location.search.substring(1);
		var args	= this.parseQuery(url);

		return args['folder'];
	},

	onok: function()
	{
		var tag		= '';
		var extra	= '';

		// Get the image tag field information
		var url		= this.fields.url.value;
		var alt		= this.fields.alt.value;
		var align	= this.fields.align.value;
		var title	= this.fields.title.value;
		var caption	= this.fields.caption.value;
		var c_class	= this.fields.c_class.value;

		if (url != '') {
			// Set alt attribute
			if (alt != '') {
				extra = extra + 'alt="'+alt+'" ';
			} else {
				extra = extra + 'alt="" ';
			}
			// Set align attribute
			if (align != '' && caption == '') {
				extra = extra + 'class="pull-'+align+'" ';
			}
			// Set title attribute
			if (title != '') {
				extra = extra + 'title="'+title+'" ';
			}

			tag = '<img src="'+url+'" '+extra+'/>';

			// Process caption
			if (caption != '') {
				var figclass = '';
				var captionclass = '';
				if (align != '') {
					figclass = ' class="pull-'+align+'"';
				}
				if (c_class != '') {
					captionclass = ' class="'+c_class+'"';
				}
				tag = '<figure'+figclass+'>'+tag+'<figcaption'+captionclass+'>'+caption+'</figcaption></figure>';
			}
		}

		window.parent.jInsertEditorText(tag, this.editor);
		return false;
	},

	setFolder: function(folder,asset,author)
	{
		for(var i = 0; i < this.folderlist.length; i++)
		{
			if (folder == this.folderlist.options[i].value) {
				this.folderlist.selectedIndex = i;
				if (this.folderlist.className.test(/\bchzn-done\b/)) {
					$(this.folderlist).trigger('liszt:updated');
				}
				break;
			}
		}
		this.frame.location.href='index.php?option=com_bwpostman&view=mediaList&tmpl=component&folder=' + folder + '&asset=' + asset + '&author=' + author;
	},

	getFolder: function() {
		return this.folderlist.value;
	},

	upFolder: function()
	{
		var currentFolder = this.getFolder();

		if (currentFolder.length < 2) {
			return false;
		}

		var folders = currentFolder.split('/');
		var search = '';

		for(var i = 0; i < folders.length - 1; i++) {
			search += folders[i];
			search += '/';
		}

		// remove the trailing slash
		search = search.substring(0, search.length - 1);

		for(var i = 0; i < this.folderlist.length; i++)
		{
			var thisFolder = this.folderlist.options[i].value;

			if (thisFolder == search)
			{
				this.folderlist.selectedIndex = i;
				var newFolder = this.folderlist.options[i].value;
				this.setFolder(newFolder);
				break;
			}
		}
	},

	populateFields: function(file)
	{
		$("#f_url").val(image_base_path+file);
	},

	showMessage: function(text)
	{
		var message  = document.id('message');
		var messages = document.id('messages');

		if (message.firstChild)
			message.removeChild(message.firstChild);

		message.appendChild(document.createTextNode(text));
		messages.style.display = "block";
	},

	parseQuery: function(query)
	{
		var params = new Object();
		if (!query) {
			return params;
		}
		var pairs = query.split(/[;&]/);
		for ( var i = 0; i < pairs.length; i++ )
		{
			var KeyVal = pairs[i].split('=');
			if ( ! KeyVal || KeyVal.length != 2 ) {
				continue;
			}
			var key = unescape( KeyVal[0] );
			var val = unescape( KeyVal[1] ).replace(/\+ /g, ' ');
			params[key] = val;
	   }
	   return params;
	},

	refreshFrame: function()
	{
		this._setFrameUrl();
	},

	_setFrameUrl: function(url)
	{
		if (url != null) {
			this.frameurl = url;
		}
		this.frame.location.href = this.frameurl;
	},

	_getQueryObject: function(q) {
		var vars = q.split(/[&;]/);
		var rs = {};
		if (vars.length) vars.forEach(function(val) {
			var keys = val.split('=');
			if (keys.length && keys.length == 2) rs[encodeURIComponent(keys[0])] = encodeURIComponent(keys[1]);
		});
		return rs;
	},

	_getUriObject: function(u){
		var bitsAssociate = {}, bits = u.match(/^(?:([^:\/?#.]+):)?(?:\/\/)?(([^:\/?#]*)(?::(\d*))?)((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[\?#]|$)))*\/?)?([^?#\/]*))?(?:\?([^#]*))?(?:#(.*))?/);
		['uri', 'scheme', 'authority', 'domain', 'port', 'path', 'directory', 'file', 'query', 'fragment'].forEach(function(key, index) {
			bitsAssociate[key] = bits[index];
		});

		return (bits)
			? bitsAssociate
			: null;
	}
};
})(jQuery);

jQuery(function(){
	ImageManager.initialize();
});
