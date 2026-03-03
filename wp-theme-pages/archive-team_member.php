<?php
/**
 * Team Members Archive Template
 *
 * @package stout
 */

get_header(); ?>

<div class="team-archive-container">

	<?php if ( have_posts() ) : ?>
		
		<?php
		
		// Group team members by category
		$team_groups = array();
		
		// First, collect all team members and group them
		$team_query = new WP_Query(array(
			'post_type' => 'team_member',
			'posts_per_page' => -1,
			'orderby' => 'menu_order title',
			'order' => 'ASC'
		));
		
		if ($team_query->have_posts()) {
			while ($team_query->have_posts()) {
				$team_query->the_post();
				$categories = get_field('team_category');
				
				// Handle both checkbox array and legacy single values
				if (is_array($categories) && !empty($categories)) {
					// New checkbox format - extract values from ACF "Both" format
					$member_categories = array();
					foreach ($categories as $category) {
						if (is_array($category) && isset($category['value'])) {
							// ACF "Both (Array)" format with value/label pairs
							$member_categories[] = $category['value'];
						} else {
							// Simple array format
							$member_categories[] = $category;
						}
					}
				} elseif (is_string($categories) && !empty($categories)) {
					// Legacy single select format
					$member_categories = array($categories);
				} else {
					// No category assigned
					$member_categories = array('Other');
				}
				
				// Create member data once
				$member_data = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'position' => get_field('position_title'),
					'grid_image' => get_field('grid_photo'),
					'permalink' => get_permalink(),
					'excerpt' => get_the_excerpt()
				);
				
				// Add member to each of their categories
				foreach ($member_categories as $category) {
					if (!isset($team_groups[$category])) {
						$team_groups[$category] = array();
					}
					
					$team_groups[$category][] = $member_data;
				}
			}
			wp_reset_postdata();
		}
		
		// Define the order of team sections
		$section_order = array('Investment Team', 'Advisors', 'VC-in-Residence');
		?>
		
		<?php foreach ($section_order as $section_name) : ?>
			<?php if (isset($team_groups[$section_name]) && !empty($team_groups[$section_name])) : ?>
				<?php $section_id = sanitize_html_class(strtolower(str_replace(' ', '-', $section_name))); ?>
				
				<section class="team-section team-section-<?php echo $section_id; ?>" aria-labelledby="team-section-<?php echo $section_id; ?>">
					<h2 class="team-section-title" id="team-section-<?php echo $section_id; ?>"><?php echo esc_html($section_name); ?></h2>
					
					<div class="team-grid" role="list" aria-label="<?php echo esc_attr($section_name); ?> team members">
						<?php foreach ($team_groups[$section_name] as $member) : ?>
							
							<article class="team-member-card" role="listitem">
								<a href="<?php echo esc_url($member['permalink']); ?>" class="team-member-link" aria-describedby="member-<?php echo $member['id']; ?>-details">
									
									<?php if ($member['grid_image']) : ?>
										<div class="team-member-photo">
											<img src="<?php echo esc_url($member['grid_image']['sizes']['medium']); ?>" 
											     alt="<?php echo esc_attr($member['grid_image']['alt'] ?: 'Photo of ' . $member['title']); ?>" 
											     class="team-grid-image">
										</div>
									<?php endif; ?>
									
									<div class="team-member-details" id="member-<?php echo $member['id']; ?>-details">
										<h3 class="team-member-name"><?php echo esc_html($member['title']); ?></h3>
										
										<?php if ($member['position']) : ?>
											<p class="team-member-position"><?php echo esc_html($member['position']); ?></p>
										<?php endif; ?>
									</div>
									
								</a>
							</article>
							
						<?php endforeach; ?>
					</div>
					
				</section>
				
			<?php endif; ?>
		<?php endforeach; ?>
		
		<?php 
		// Display any remaining categories that weren't in the predefined order
		foreach ($team_groups as $category_name => $members) {
			if (!in_array($category_name, $section_order) && !empty($members)) : ?>
				<?php $section_id = sanitize_html_class(strtolower(str_replace(' ', '-', $category_name))); ?>
				
				<section class="team-section team-section-<?php echo $section_id; ?>" aria-labelledby="team-section-<?php echo $section_id; ?>">
					<h2 class="team-section-title" id="team-section-<?php echo $section_id; ?>"><?php echo esc_html($category_name); ?></h2>
					
					<div class="team-grid" role="list" aria-label="<?php echo esc_attr($category_name); ?> team members">
						<?php foreach ($members as $member) : ?>
							
							<article class="team-member-card" role="listitem">
								<a href="<?php echo esc_url($member['permalink']); ?>" class="team-member-link" aria-describedby="member-<?php echo $member['id']; ?>-details">
									
									<?php if ($member['grid_image']) : ?>
										<div class="team-member-photo">
											<img src="<?php echo esc_url($member['grid_image']['sizes']['medium']); ?>" 
											     alt="<?php echo esc_attr($member['grid_image']['alt'] ?: 'Photo of ' . $member['title']); ?>" 
											     class="team-grid-image">
										</div>
									<?php endif; ?>
									
									<div class="team-member-details" id="member-<?php echo $member['id']; ?>-details">
										<h3 class="team-member-name"><?php echo esc_html($member['title']); ?></h3>
										
										<?php if ($member['position']) : ?>
											<p class="team-member-position"><?php echo esc_html($member['position']); ?></p>
										<?php endif; ?>
										
										<?php if ($member['excerpt']) : ?>
											<p class="team-member-excerpt"><?php echo esc_html($member['excerpt']); ?></p>
										<?php endif; ?>
									</div>
									
								</a>
							</article>
							
						<?php endforeach; ?>
					</div>
					
				</section>
				
			<?php endif; ?>
		<?php } ?>
		
	<?php else : ?>
		
		<div class="no-team-members" role="status" aria-live="polite">
			<p>No team members found.</p>
		</div>
		
	<?php endif; ?>
	
</div>

<?php get_footer(); ?>