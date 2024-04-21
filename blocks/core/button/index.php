<?php
// Note: For core blocks, this file controls front-end output only.
if (isset($args['block'])) {
	$block = $args['block'];
	$buttonColor = 'primary';

	if (isset($args['block']['attrs']['style']['color']['button'])) {
		$buttonColor = array_reverse(explode('|', $args['block']['attrs']['style']['color']['button']));
	}

	// Add colour classes
	if (isset($args['block']['attrs']['className']) && str_contains($args['block']['attrs']['className'], 'is-style-outline')) {
		$updatedHtml = str_replace('<a class="', '<a class="btn btn--' . $buttonColor[0] . '--hollow ', $args['block']['innerHTML']);
	}
	else {
		$updatedHtml = str_replace('<a class="', '<a class="btn btn--' . $buttonColor[0] . ' ', $args['block']['innerHTML']);

	}

	// Add icon to external links
	if (str_contains($args['block']['innerHTML'], 'target="_blank"')) {
		$updatedHtml = str_replace('</a>', ' <i class="fa-sharp fa-solid fa-up-right-from-square"></i> </a>', $updatedHtml);
	}

	$args['block']['innerHTML'] = $updatedHtml;
	$args['block']['innerContent'] = array($updatedHtml);
	
	echo render_block($args['block']);
}
