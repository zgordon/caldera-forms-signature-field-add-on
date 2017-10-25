<?php
/**
 * Add signature field type.
 */
add_filter('caldera_forms_get_field_types', function($types){
	$types['signature'] = [
		"field"       => "Signature",
		'category'    => 'Special',
		"file"        => starter()->plugin_dir_path . 'inc/caldera-forms/signature-input.inc.php',
		"description" => 'Signature Drawing Pad',
		"setup"       => [
			"template"  => starter()->plugin_dir_path . 'inc/caldera-forms/signature-config.inc.php',
			"preview"  => starter()->plugin_dir_path . 'inc/caldera-forms/signature-preview.inc.php',
		],
		'scripts'     => [
			starter()->plugin_dir_url . 'public/js/signature_pad.min.js',
			starter()->plugin_dir_url . 'public/caldera-forms/script.js',
		],
		'styles'     => [
			starter()->plugin_dir_url . 'public/caldera-forms/style.css',
		],
	];
	return $types;
});

/**
 * Save signature as an attachement.
 */
add_filter( "caldera_forms_save_field_signature", function( $entry, $field ) {
	return CA62926::saveSignature( $entry )[ 'url' ];
}, 10, 2 );

/**
 * Show signature in submission backend preview.
 */
add_filter( 'caldera_forms_view_field_signature', function( $value ) {
	return '<img src="' . $value . '">';
} );

/**
 * Show signature in submission summary.
 */
add_filter( 'caldera_forms_magic_summary_field_value', function( $value, $field, $form ) {
	$type = Caldera_Forms_Field_Util::get_type( $field, $form );
	return ( 'signature' === $type )
		? '<img src="' . $value . '">'
		: $value
	;
}, 10, 3 );
