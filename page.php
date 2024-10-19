<?php
the_post();
get_header();
$blocks = parse_blocks(get_the_content());
Starterkit_Block_Utils::output_custom_blocks($blocks, array(
	'args'   => [],
	'parent' => 'page'
));
get_footer();
