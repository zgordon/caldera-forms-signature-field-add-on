jQuery( document ).ready( function( $ ) {

	$('input[data-type=signature]').each( function () {
		var $this = $( this );
		var $canvas      = $this.siblings('.signature_canvas').first();
		var $clear       = $this.siblings('.signature_clear_button').first();
		var signaturePad = new SignaturePad( $canvas[ 0 ] );

		/**
		 * The clear button.
		 */
		$clear.click( function(e) {
			e.preventDefault();
			signaturePad.clear();
			$this.val('');
		} );

		/**
		 * Update image on mouseout
		 */
		$canvas.mouseout( function() {
			if ( ! signaturePad.isEmpty() ) {
				$this.val(signaturePad.toDataURL());
			}
		} );
	} );
} );

