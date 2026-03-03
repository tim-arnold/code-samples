<?php
/**
 * Portfolio Grid Block Template
 *
 * @param array $block The block settings and attributes.
 * @param string $content The block inner HTML (empty).
 * @param bool $is_preview True during AJAX preview.
 * @param int $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
$id = 'portfolio-grid-' . $block['id'];
if( !empty($block['anchor']) ) {
    $id = $block['anchor'];
}

// Create class attribute allowing for custom "className" and "align" values.
$className = 'glacier-card-wrapper';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Get ACF fields
$portfolio_title = get_field('portfolio_title') ?: 'Our <em>Portfolio</em>';
$arrow_image = get_field('arrow_image');
$portfolio_cards = get_field('portfolio_cards');

// Calculate rows needed - we'll handle this in JavaScript since the logic gets complex
$total_cards = $portfolio_cards ? count($portfolio_cards) : 0;
$initial_card_count = min(6, $total_cards);

// Use the original calculation that was working
// Each complete set of 6 cards uses 4 rows, plus 1 for title
$initial_sets = ceil($initial_card_count / 6);
$total_rows = 1 + ($initial_sets * 4); // 1 for title + 4 per card set
$grid_style = "grid-template-rows: repeat({$total_rows}, 1fr);";

?>

<section class="homepage-portfolio">
    <h2><span><?php echo wp_kses_post($portfolio_title); ?></span></h2>

    <div id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($className); ?>" style="<?php echo esc_attr($grid_style); ?>">
        <div class="top-row">

            <?php if($arrow_image): ?>
                <figure class="arrow-down-wrapper">
                    <?php echo wp_get_attachment_image($arrow_image['ID'], 'full', false, ['class' => 'arrow-down']); ?>
                </figure>
            <?php endif; ?>
        </div>

        <?php if($portfolio_cards): ?>
            <?php
            // Define the repeating pattern for each set of 6 cards
            $base_pattern = [
                ['class' => 'divpre', 'type' => 'grid-r', 'row_start' => 1, 'row_span' => 2, 'col_start' => 3, 'col_span' => 1],
                ['class' => 'div1', 'type' => 'grid-l', 'row_start' => 2, 'row_span' => 1, 'col_start' => 1, 'col_span' => 2],
                ['class' => 'div2', 'type' => 'grid-l', 'row_start' => 3, 'row_span' => 1, 'col_start' => 1, 'col_span' => 2],
                ['class' => 'div3', 'type' => 'grid-r', 'row_start' => 3, 'row_span' => 2, 'col_start' => 3, 'col_span' => 1],
                ['class' => 'div4', 'type' => 'grid-l', 'row_start' => 4, 'row_span' => 1, 'col_start' => 1, 'col_span' => 2],
                ['class' => 'div5', 'type' => 'grid-l', 'row_start' => 5, 'row_span' => 1, 'col_start' => 1, 'col_span' => 2]
            ];
            
            $card_index = 0;
            
            // Function to calculate dynamic grid positioning
            if (!function_exists('get_card_position')) {
                function get_card_position($index, $base_pattern) {
                $set_number = floor($index / 6); // Which set of 6 are we in (0, 1, 2...)
                $position_in_set = $index % 6; // Position within the current set (0-5)
                
                if ($position_in_set >= count($base_pattern)) {
                    return null; // Safety check
                }
                
                $pattern = $base_pattern[$position_in_set];
                
                // Calculate row offset for this set (each set adds 4 rows)
                $row_offset = $set_number * 4;
                
                // Adjust row positions for this set
                $adjusted_row_start = $pattern['row_start'] + $row_offset;
                
                // Generate dynamic class name
                $dynamic_class = $pattern['class'] . ($set_number > 0 ? '_' . $set_number : '');
                
                return [
                    'classes' => $dynamic_class . ' ' . $pattern['type'],
                    'grid_area' => $adjusted_row_start . ' / ' . $pattern['col_start'] . ' / ' . ($adjusted_row_start + $pattern['row_span']) . ' / ' . ($pattern['col_start'] + $pattern['col_span']),
                    'is_grid_l' => $pattern['type'] === 'grid-l',
                    'has_arrow_in_text' => in_array($position_in_set, [1, 4]) // div2 and div5 positions have arrows in text (repeats every 6)
                ];
                }
            }
            ?>

            <?php 
            // Only show first 6 cards initially
            $initial_cards = array_slice($portfolio_cards, 0, 6);
            ?>
            <?php foreach($initial_cards as $card): ?>
                <?php 
                $position_data = get_card_position($card_index, $base_pattern);
                if($position_data): ?>
                    <?php
                    $position_classes = $position_data['classes'];
                    $is_grid_l = $position_data['is_grid_l'];
                    $has_arrow_in_text = $position_data['has_arrow_in_text'];
                    $grid_area_style = 'grid-area: ' . $position_data['grid_area'] . ';';
                    ?>

                    <div class="glacier-card <?php echo esc_attr($position_classes); ?>" style="<?php echo esc_attr($grid_area_style); ?>">
                        <!-- Text Container First -->
                            <div class="text-container">
                                <div class="arrow"></div>

                                <?php if($card['location']): ?>
                                    <div class="location-label"><?php echo esc_html($card['location']); ?></div>
                                <?php endif; ?>

                                <?php if($card['logo']): ?>
                                    <div class="logo">
                                        <figure>
                                            <?php echo wp_get_attachment_image($card['logo']['ID'], 'full', false, ['alt' => $card['logo']['alt']]); ?>
                                        </figure>
                                    </div>
                                <?php endif; ?>

                                <div class="card-content">
                                    <?php if($card['ceo_name']): ?>
                                        <p class="ceo"><?php echo esc_html($card['ceo_name']); ?></p>
                                    <?php endif; ?>

                                    <?php if($card['description']): ?>
                                        <p class="description"><?php echo esc_html($card['description']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                        <!-- Image Container Second -->
                        <div class="image-container">

                            <?php if($card['product_image']): ?>
                                <figure class="product">
                                    <?php echo wp_get_attachment_image($card['product_image']['ID'], 'full', false, ['alt' => $card['product_image']['alt']]); ?>
                                </figure>
                            <?php endif; ?>

                            <?php if($card['hover_image']): ?>
                                <figure class="hovered">
                                    <?php echo wp_get_attachment_image($card['hover_image']['ID'], 'full', false, ['alt' => $card['hover_image']['alt']]); ?>
                                </figure>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php $card_index++; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php 
            if ($total_cards > 6): 
                // Show "View more" button if there are more cards to load
                $button_position = get_card_position($card_index, $base_pattern);
                if($button_position): 
                    $button_grid_style = 'grid-area: ' . $button_position['grid_area'] . ';';
                else:
                    // Fallback if we don't have a pattern position
                    $final_row = 1 + (ceil($card_index / 6) * 4);
                    $button_grid_style = "grid-area: {$final_row} / 1 / " . ($final_row + 1) . " / 3;";
                endif;
            elseif ($total_cards == 6):
                // Show empty placeholder if we have exactly 6 cards (complete set with no more)
                $button_position = get_card_position($card_index, $base_pattern);
                if($button_position): 
                    $button_grid_style = 'grid-area: ' . $button_position['grid_area'] . ';';
                else:
                    $final_row = 1 + (ceil($card_index / 6) * 4);
                    $button_grid_style = "grid-area: {$final_row} / 1 / " . ($final_row + 1) . " / 3;";
                endif;
            endif;
            
            if ($total_cards > 6): ?>
                
                <button class="glacier-card glacier-more" 
                        style="<?php echo esc_attr($button_grid_style); ?>"
                        data-total-cards="<?php echo esc_attr($total_cards); ?>"
                        data-loaded-cards="6"
                        data-post-id="<?php echo esc_attr($post_id ?: get_the_ID()); ?>"
                        data-nonce="<?php echo wp_create_nonce('load_more_portfolio_nonce'); ?>"
                        data-ajax-url="<?php echo esc_url(admin_url('admin-ajax.php')); ?>">
                    <p>View more</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="79" viewBox="0 0 31 79" fill="none">
                      <path d="M15.4999 0L15.4999 77.6667M15.4999 77.6667L1 63M15.4999 77.6667L30 63" stroke="white"/>
                    </svg>
                </button>
            <?php elseif ($total_cards == 6): ?>
                <!-- Empty placeholder for complete set of 6 cards -->
                <div class="glacier-card" style="<?php echo esc_attr($button_grid_style); ?>"></div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

