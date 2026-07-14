jQuery( function( $ ) {

	$( '.uno_wp_form input[data-conv-half-alphanumeric="true"]' ).change( function() {
		var txt  = $( this ).val();
		var half = txt.replace( /[Ａ-Ｚａ-ｚ０-９]/g, function( s ) {
			return String.fromCharCode( s.charCodeAt( 0 ) - 0xFEE0 )
		} );
		$( this ).val( half );
	} );

	var file_delete = $( '.uno_wp_form .unoform-file-delete' );
	file_delete.each( function( i, e ) {
		var target = $( e ).data( 'unoform-file-delete' );
		var hidden_field = $( 'input[type="hidden"][name="' + target + '"]' );
		if ( hidden_field.val() ) {
			$( e ).css( 'visibility', 'visible' );
		}
		$( e ).click( function() {
			var file_field = $( 'input[type="file"][name="' + target + '"]' );
			var new_field = $( file_field[0].outerHTML );
			$( this ).css( 'visibility', 'hidden' );
			file_field.replaceWith( new_field );

			hidden_field.parent().fadeOut( 100, function() {
				$( this ).remove();
			} );
		} );
	} );
	$( document ).on( 'change', '.uno_wp_form input[type="file"]', function() {
		var name = $( this ).attr( 'name' );
		file_delete.closest( '[data-unoform-file-delete="' + name + '"]' ).css( 'visibility', 'visible' );
	} );

	var uno_wp_form_button_no_click = true;
	$( '.uno_wp_form input[type="submit"]' ).click( function() {
		var formElement = $( this ).closest( 'form' )[0];
		if ( formElement && formElement.checkValidity && !formElement.checkValidity() ) {
			return;
		}
		if ( uno_wp_form_button_no_click ) {
			uno_wp_form_button_no_click = false;
		} else {
			$( this ).prop( 'disabled', true );
		}
	} );
} );
