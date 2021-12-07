<?php
defined( 'ABSPATH' ) or exit;

/**
 * Plugin Name: WP List Table Copy
 * Plugin URI: https://github.com/csalzano/wp-list-table-copy
 * Description: Adds a "Copy Values" button to all WP List Tables that copies the visible contents to the clipboard so they can be pasted into a spreadsheet.
 * Version: 1.0.0
 * Author: Corey Salzano
 * Author URI: https://breakfastco.xyz
 * Text Domain: wp-list-table-copy
 * License: GPLv2
 */

add_action( 'admin_print_footer_scripts', function() {
	
	?><script type="text/javascript"><!--
	
	function copyStringToClipboard (str) {
		// Create new element
		var el = document.createElement('textarea');
		// Set value (string to be copied)
		el.value = str;
		// Set non-editable to avoid focus and move outside of view
		el.setAttribute('readonly', '');
		el.style = {position: 'absolute', left: '-9999px'};
		document.body.appendChild(el);
		// Select text inside element
		el.select();
		// Copy text to clipboard
		document.execCommand('copy');
		// Remove temporary element
		document.body.removeChild(el);
	}

	jQuery(document).ready(function(){
		jQuery('div.tablenav div.actions.bulkactions').append(
			'<button class="button" onclick="copyWPListTableValues( this ); return false;"><?php _e( 'Copy Values', 'wp-list-table-copy' ); ?></button>'
		);
	});

	function copyWPListTableValues( btn )
	{
		var rows = [];
		jQuery(btn).parent().parent().siblings( '.wp-list-table' ).find('tr').each( function( i )
		{
			var row = [];
			jQuery(this).children('td').each( function( j )
			{
				var content = jQuery(this)[0].innerText;
				var content_pieces = content.split( '\n' );

				for( var p=0; p<content_pieces.length; p++ )
				{
					if( '' == content_pieces[p].trim() 
						|| 'No description' == content_pieces[p].trim() )
					{
						continue;
					}

					if( 'Select ' == content_pieces[p].trim().substr( 0, 7 )
						|| 'Edit | ' == content_pieces[p].trim().substr( 0, 7 ) )
					{
						continue;
					}

					/**
					 * The — character is shown when a term has no description
					 * and at the beginning of child terms. Remove both.
					 */
					if( '—' == content_pieces[p].trim() )
					{
						content_pieces[p] = '';
					}
					else if( '— ' == content_pieces[p].trim().substr( 0, 2 ) )
					{
						content_pieces[p] = content_pieces[p].trim().substr( 2 );
					}

					row.push( content_pieces[p] );
				}
			});
			if( 0 < row.length )
			{
				rows.push( row );
			}
		});

		//Have 2d array rows with all of the data. Put it in a string
		var values_string = '';
		for( var r=0; r<rows.length; r++ )
		{
			values_string += rows[r].join( '\t' ) + '\n';
		}
		copyStringToClipboard( values_string );
	}
	
	--></script><?php
});
