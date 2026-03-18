<?php
/**
 * Single Team Member Template
 *
 * @package stout
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
	
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		
		<?php 
		// Get ACF fields
		$position = get_field('position_title');
		$subtitle_lines = get_field('subtitle_lines');
		$email = get_field('email_address');
		$social_links = get_field('social_links');
		$team_category = get_field('team_category');
		?>
		
		<header class="page-header">
            <div class="page-header-content">
			    <h1 class="page-header-title"><?php the_title(); ?></h1>
            </div>
        </header>

		<!-- Hero Image -->

			<div class="team-member-heroine">

                <?php if (has_post_thumbnail()) : ?>

                    <?php the_post_thumbnail('large', array(
                        'class' => 'team-member-heroine-image',
                        'alt'   => get_the_title()
                    )); ?>

				<?php endif; ?>

				<!-- Navigation -->
				<div class="team-member-navigation">

                    <?php
                    // Next/Previous team member navigation using menu_order
                    $current_post_id = get_the_ID();

                    // Get all team members in menu order
                    $all_team_members = get_posts(array(
                        'post_type' => 'team_member',
                        'posts_per_page' => -1,
                        'orderby' => 'menu_order title',
                        'order' => 'ASC'
                    ));

                    $prev_post = null;
                    $next_post = null;
                    $current_found = false;

                    foreach ($all_team_members as $index => $member) {
                        if ($member->ID == $current_post_id) {
                            $current_found = true;
                            // Get previous post (one before current)
                            if ($index > 0) {
                                $prev_post = $all_team_members[$index - 1];
                            }
                            // Get next post (one after current)
                            if ($index < count($all_team_members) - 1) {
                                $next_post = $all_team_members[$index + 1];
                            }
                            break;
                        }
                    }
                    ?>

                    <?php if ($prev_post || $next_post) : ?>
                        <div class="team-member-nav">
                            <?php if ($prev_post) : ?>
                                <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="nav-previous">
                                    <span>PREV</span>
                                    <svg class="nav-arrow" width="90" height="31" viewBox="0 0 90 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M-6.33813e-07 15.5001L88.5 15.5001M88.5 15.5001L73.8333 30M88.5 15.5001L73.8333 0.999997" stroke="white"/>
                                    </svg>
                                </a>
                            <?php endif; ?>

                            <?php if ($next_post) : ?>
                                <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="nav-next">
                                    <span>NEXT</span>
                                    <svg class="nav-arrow" width="90" height="31" viewBox="0 0 90 31" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M-6.33813e-07 15.5001L88.5 15.5001M88.5 15.5001L73.8333 30M88.5 15.5001L73.8333 0.999997" stroke="white"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
			</div>


		<div class="team-member-content">

            <!-- Member Info -->
            <div class="team-member-info">

                <?php if ($subtitle_lines) : ?>
                    <div class="team-member-subtitles">
                        <?php echo wp_kses_post( $subtitle_lines ); ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Member Content/Bio -->
            <?php if (get_the_content()) : ?>
                <div class="team-member-bio">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

            <!-- Contact/Social Links -->
            <?php 
            // Check if we have any social links
            $has_social_links = false;
            if ($social_links) {
                $has_social_links = !empty($social_links['team_social_link_facebook']) || 
                                  !empty($social_links['team_social_link_linkedin']) || 
                                  !empty($social_links['team_social_link_instagram']) || 
                                  !empty($social_links['team_social_link_youtube']) || 
                                  !empty($social_links['team_social_link_x']) || 
                                  !empty($social_links['team_social_link_bluesky']);
            }
            ?>
            <?php if ($email || $has_social_links) : ?>
                <div class="team-member-contact">
                    <?php if ($email) : ?>
                     <p class="team-member-contact-email">Email:
                        <a href="mailto:<?php echo esc_attr($email); ?>" class="team-contact-email">
                           <?php echo esc_html($email); ?>
                        </a>
                    </p>
                    <?php endif; ?>

                    <?php if ($has_social_links) : ?>
                        <div class="team-social-links">
                            <?php if (!empty($social_links['team_social_link_facebook'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_facebook']); ?>" target="_blank" rel="noopener" class="team-social-facebook">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                      <path d="M18 0H1.1C0.5 0 0 0.5 0 1.1V18C0 18.6 0.5 19 1.1 19H10.2V11.7H7.7V8.8H10.2V6.7C10.2 4.2 11.7 2.9 13.9 2.9C14.6 2.9 15.4 2.9 16.1 3V5.6H14.6C13.4 5.6 13.2 6.2 13.2 7V8.8H16L15.6 11.7H13.1V19H18C18.6 19 19 18.5 19 18V1.1C19 0.5 18.5 0 18 0Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">Facebook</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($social_links['team_social_link_linkedin'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_linkedin']); ?>" target="_blank" rel="noopener" class="team-social-linkedin">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                      <path d="M19 15.4221C19 17.3961 17.3961 19 15.4221 19H3.57792C1.6039 19 0 17.3961 0 15.4221V3.57792C0 1.6039 1.6039 0 3.57792 0H15.4221C17.3961 0 19 1.6039 19 3.57792V15.4221ZM4.44156 3.20779C3.45455 3.20779 2.83766 3.82468 2.83766 4.68831C2.71429 5.55195 3.33117 6.16883 4.31818 6.16883C5.3052 6.16883 5.92208 5.55195 5.92208 4.68831C5.92208 3.82468 5.30519 3.20779 4.44156 3.20779ZM5.7987 15.9156V7.27922H2.96104V15.9156H5.7987ZM16.039 15.9156V10.9805C16.039 8.38961 14.6818 7.15584 12.7078 7.15584C11.2273 7.15584 10.6104 8.01948 10.2403 8.63636V7.4026H7.4026C7.4026 7.4026 7.4026 8.26623 7.4026 16.039H10.2403V11.1039C10.2403 10.8571 10.2403 10.6104 10.3636 10.3636C10.487 9.87013 10.9805 9.37662 11.8442 9.37662C12.8312 9.37662 13.3247 10.1169 13.3247 11.3506V15.9156H16.039Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">LinkedIn</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($social_links['team_social_link_instagram'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_instagram']); ?>" target="_blank" rel="noopener" class="team-social-instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                      <path d="M9.5 0C6.9 0 6.6 0 5.6 0.1C4.6 0.1 3.9 0.3 3.3 0.5C2.7 0.7 2.1 1.1 1.6 1.6C1.1 2.1 0.7 2.7 0.5 3.3C0.3 3.9 0.1 4.6 0.1 5.6C0 6.6 0 6.9 0 9.5C0 12.1 0 12.4 0.1 13.4C0.1 14.4 0.3 15.1 0.5 15.7C0.7 16.3 1.1 16.9 1.6 17.4C2.1 17.9 2.7 18.3 3.3 18.5C3.9 18.7 4.6 18.9 5.6 18.9C6.6 18.9 6.9 19 9.5 19C12.1 19 12.4 19 13.4 18.9C14.4 18.9 15.1 18.7 15.7 18.5C16.3 18.3 16.9 17.9 17.4 17.4C17.9 16.9 18.3 16.3 18.5 15.7C18.7 15.1 18.9 14.4 18.9 13.4C18.9 12.4 19 12.1 19 9.5C19 6.9 19 6.6 18.9 5.6C18.9 4.6 18.7 3.9 18.5 3.3C18.3 2.7 17.9 2.1 17.4 1.6C16.9 1.1 16.3 0.7 15.7 0.5C15.1 0.3 14.4 0.1 13.4 0.1C12.4 0 12.1 0 9.5 0ZM9.5 1.7C12 1.7 12.3 1.7 13.3 1.8C14.2 1.8 14.7 2 15.1 2.1C15.5 2.3 15.9 2.5 16.2 2.8C16.5 3.1 16.7 3.5 16.9 3.9C17 4.2 17.2 4.7 17.2 5.7C17.2 6.7 17.3 7 17.3 9.6C17.3 12.1 17.3 12.4 17.2 13.4C17.2 14.3 17 14.8 16.9 15.2C16.7 15.6 16.5 16 16.2 16.3C15.9 16.6 15.6 16.8 15.1 17C14.8 17.1 14.3 17.3 13.3 17.3C12.3 17.3 12 17.4 9.5 17.4C7 17.4 6.7 17.4 5.7 17.3C4.8 17.3 4.3 17.1 3.9 17C3.5 16.8 3.1 16.6 2.8 16.3C2.5 16 2.3 15.6 2.1 15.2C2 14.9 1.8 14.4 1.8 13.4C1.8 12.4 1.7 12.1 1.7 9.6C1.7 7.1 1.7 6.8 1.8 5.7C1.8 4.8 2 4.3 2.1 3.9C2.3 3.5 2.5 3.1 2.8 2.8C3.1 2.5 3.4 2.3 3.9 2.1C4.2 2 4.7 1.8 5.7 1.8C6.7 1.7 7 1.7 9.5 1.7ZM9.5 12.7C7.8 12.7 6.3 11.3 6.3 9.5C6.3 7.7 7.7 6.3 9.5 6.3C11.3 6.3 12.7 7.7 12.7 9.5C12.7 11.3 11.2 12.7 9.5 12.7ZM9.5 4.6C6.8 4.6 4.6 6.8 4.6 9.5C4.6 12.2 6.8 14.4 9.5 14.4C12.2 14.4 14.4 12.2 14.4 9.5C14.4 6.8 12.2 4.6 9.5 4.6ZM15.7 4.4C15.7 5 15.2 5.5 14.6 5.5C14 5.5 13.5 5 13.5 4.4C13.5 3.8 14 3.3 14.6 3.3C15.2 3.3 15.7 3.8 15.7 4.4Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">Instagram</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($social_links['team_social_link_youtube'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_youtube']); ?>" target="_blank" rel="noopener" class="team-social-youtube">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="16" viewBox="0 0 22 16" fill="none">
                                      <path d="M21.7556 3.98143C21.7556 3.98143 21.5111 2.51476 20.8593 1.86291C20.0444 0.966612 19.0667 0.966612 18.6593 0.966612C15.6444 0.722168 11 0.722168 11 0.722168C11 0.722168 6.35556 0.722168 3.34074 0.966612C2.85185 0.966612 1.95556 0.966612 1.05926 1.86291C0.407407 2.51476 0.244444 3.98143 0.244444 3.98143C0.244444 3.98143 0 5.69254 0 7.48513V9.11476C0 10.8259 0.244444 12.6185 0.244444 12.6185C0.244444 12.6185 0.488889 14.0851 1.14074 14.737C1.95556 15.6333 3.0963 15.5518 3.58519 15.6333C5.37778 15.7962 11.0815 15.8777 11.0815 15.8777C11.0815 15.8777 15.7259 15.8777 18.8222 15.6333C19.2296 15.5518 20.2074 15.5518 21.0222 14.737C21.6741 14.0851 21.9185 12.6185 21.9185 12.6185C21.9185 12.6185 22.163 10.9074 22.163 9.11476V7.48513C22 5.69254 21.7556 3.98143 21.7556 3.98143ZM8.71852 11.3148V5.20365L14.5852 8.13698L8.71852 11.3148Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">YouTube</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($social_links['team_social_link_x'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_x']); ?>" target="_blank" rel="noopener" class="team-social-x">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                      <path d="M8.5 10.0761L7.8 9.09783L2.1 1.27174H4.5L9.1 7.63043L9.8 8.6087L15.7 16.8261H13.3L8.5 10.0761ZM10.7 7.63043L17.3 0H15.7L9.9 6.55435L5.4 0H0L7 9.97826L0 18H1.6L7.7 11.0543L12.6 18H18L10.7 7.63043Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">X</span>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($social_links['team_social_link_bluesky'])) : ?>
                                <a href="<?php echo esc_url($social_links['team_social_link_bluesky']); ?>" target="_blank" rel="noopener" class="team-social-bluesky">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                                      <path d="M3.84537 1.85744C5.72942 3.24518 7.75593 6.05894 8.49997 7.56897C9.24406 6.05905 11.2705 3.24515 13.1546 1.85744C14.514 0.856101 16.7166 0.0813225 16.7166 2.54671C16.7166 3.03908 16.4289 6.68288 16.2602 7.27444C15.6736 9.3311 13.5361 9.85567 11.6347 9.53818C14.9582 10.0932 15.8037 11.9314 13.9778 13.7697C10.5101 17.2609 8.9937 12.8937 8.60497 11.7747C8.53374 11.5696 8.50041 11.4736 8.49992 11.5552C8.49942 11.4736 8.4661 11.5696 8.39487 11.7747C8.0063 12.8937 6.48993 17.2611 3.02202 13.7697C1.1961 11.9314 2.04154 10.093 5.3651 9.53818C3.4637 9.85567 1.32618 9.3311 0.739681 7.27444C0.570922 6.68283 0.283203 3.03903 0.283203 2.54671C0.283203 0.0813225 2.48589 0.856101 3.84527 1.85744H3.84537Z" fill="white"/>
                                    </svg>
                                    <span class="sr-only">Bluesky</span>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        </div>

	</article>

<?php endwhile; ?>

<?php get_footer(); ?>