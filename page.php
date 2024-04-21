<?php
the_post();
get_header();
$blocks = parse_blocks(get_the_content());
Doublee_Block_Utils::output_custom_blocks($blocks, array(
	'args'   => [],
	'parent' => 'page'
));
get_footer();
