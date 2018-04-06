// closure to avoid namespace collision
(function(){
	// creates the plugin
	tinymce.create('tinymce.plugins.uvasomdeptrfd', {
		// creates control instances based on the control's id.
		// our button's id is "uvasomdeptrfd_button"
		createControl : function(id, controlManager) {
			if (id == 'uvasomdeptrfd_button') {
				// creates the button
				var button = controlManager.createButton('uvasomdeptrfd_button', {
					title : 'uvasomdeptrfd Shortcode', // title of the button
					image : '../wp-content/plugins/uvasomdeptrfd/uvasomfaculty.jpg',  // path to the button's image
					onclick : function() {
						// triggers the thickbox
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						tb_show( 'UVA SOM Faculty', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=uvasomdeptrfd-form' );
					}
				});
				return button;
			}
			return null;
		}
	});
	
	// registers the plugin. DON'T MISS THIS STEP!!!
	tinymce.PluginManager.add('uvasomdeptrfd', tinymce.plugins.uvasomdeptrfd);
	
	// executes this when the DOM is ready
	jQuery(function(){
		// creates a form to be displayed everytime the button is clicked
		// you should achieve this using AJAX instead of direct html code like this
		var form = jQuery('<div id="uvasomdeptrfd-form"><table id="uvasomdeptrfd-table" class="form-table">\
			<tr>\
				<th><label for="uvasomdeptrfd-listing">List By</label></th>\
				<td><select id="uvasomdeptrfd-listing" name="listing">\
					<option value="primary" selected>Primary Department Members</option>\
					<option value="training-grant">Training Grant Participants</option>\
					</select><br />\
				<small>Select if you want a primary department members or training grant participants. Default is by primary department.</small>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="uvasomdeptrfd-submit" class="button-primary" value="Insert Faculty List" name="submit" />\
		</p>\
		</div>');
		
		var table = form.find('table');
		form.appendTo('body').hide();
		
		// handles the click event of the submit button
		form.find('#uvasomdeptrfd-submit').click(function(){
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = { 
				'listing'    : 'primary'
				};
			var shortcode = '[uvasomfaculty';
			
			for( var index in options) {
				var value = table.find('#uvasomdeptrfd-' + index).val();
				
				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] )
					shortcode += ' ' + index + '="' + value + '"';
			}
			
			shortcode += ']';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			tb_remove();
		});
	});
})()