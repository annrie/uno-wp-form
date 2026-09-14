jQuery( function( $ ) {
	$( '#unomoon-form_chart' ).unomoon_form_repeatable( {
		add_position: 'last'
	} );

	$( '#unomoon-form_chart .repeatable-boxes' ).sortable( {
		items : '> .repeatable-box',
		handle: '.sortable-icon-handle'
	} );
} );