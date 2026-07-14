jQuery( function( $ ) {
	$( '#uno-wp-form_chart' ).uno_wp_form_repeatable( {
		add_position: 'last'
	} );

	$( '#uno-wp-form_chart .repeatable-boxes' ).sortable( {
		items : '> .repeatable-box',
		handle: '.sortable-icon-handle'
	} );
} );