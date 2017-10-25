<?php

$canvas_html       = '<canvas id="' . $field_id . '_canvas" class="signature_canvas"></canvas>';
$clear_button_html = '<a href="#" id="' . $field_id . '_clear_button" class="signature_clear_button">Clear</a>';
$field_html        = str_replace(
	[
		' type="signature"',
		'>',
	],
	[
		' type="hidden"',
		'>' . $canvas_html . $clear_button_html,
	],
	Caldera_Forms_Field_Input::html( $field, $field_structure, $form )
);

?>
<?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<?php echo $field_html; ?>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>