<div class="pseudo-block pseudo-block__pagination has-light-background-color">
    <div class="row">
        <div class="col-12">
            <?php
            global $wp_query;
            $output = paginate_links(array(
                'current'   => max(1, get_query_var('paged')),
                'total'     => $wp_query->max_num_pages,
                'prev_text' => 'Prev',
                'next_text' => 'Next',
                'type'      => 'list',
                'end_size'  => 1,
                'mid_size'  => 1
            ));

            if ($output) {
                $output = str_replace('page-numbers dots', 'btn btn--dark--hollow btn--dots btn--disabled', $output);
                $output = str_replace('prev page-numbers', 'btn btn--dark--hollow btn--prev', $output);
                $output = str_replace('next page-numbers', 'btn btn--dark--hollow btn--next', $output);
                $output = str_replace('class="page-numbers current"', 'class="btn btn--dark--hollow btn--current btn--disabled"', $output);
                $output = str_replace('<a class="page-numbers', '<a class="btn btn--dark--hollow', $output);
                $output = str_replace('Prev', '<i class="fa-solid fa-arrow-left"></i> Prev', $output);
                $output = str_replace('Next', 'Next <i class="fa-solid fa-arrow-right"></i>', $output);
                $output = str_replace("<ul class='btn btn--dark--hollow'>", '<ul>', $output);
                $output = str_replace('outline current', 'current', $output);

                echo $output;
            }
            ?>
        </div>
    </div>
</div>
