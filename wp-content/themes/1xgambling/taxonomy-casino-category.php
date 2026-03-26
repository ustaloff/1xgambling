<?php
/**
 * Child theme override: taxonomy-casino-category.php
 * Adds SEO intro (above list) and SEO content (below list) from term meta.
 */
get_header(); ?>

<!-- Title Box Start -->

<div class="space-archive-title-box box-100 relative">
	<div class="space-archive-title-box-ins space-page-wrapper relative">
		<div class="space-archive-title-box-h1 relative">
			<h1><?php echo esc_html( get_queried_object()->name ); ?></h1>
			<?php get_template_part( '/theme-parts/breadcrumbs' ); ?>
		</div>
	</div>
</div>

<!-- Title Box End -->

<!-- Archive Section Start -->

<div class="space-archive-section box-100 relative space-organization-archive">
	<div class="space-archive-section-ins space-page-wrapper relative">
		<div class="space-organization-archive-ins box-100 relative">

			<?php
			// ── Навигация по категориям ──────────────────────────────────
			if ( get_theme_mod( 'mercury_category_navigation_casinos' ) ) {
				$args       = [
					'hide_empty' => 1,
					'type'       => 'casino',
					'orderby'    => 'name',
					'taxonomy'   => 'casino-category',
					'order'      => 'ASC',
				];
				$categories = get_categories( $args );
				if ( $categories ) { ?>
				<div class="space-categories-list-box relative">
					<ul class="space-categories-title">
						<?php if ( get_theme_mod( 'mercury_casinos_list_page_id' ) ) { ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( get_theme_mod( 'mercury_casinos_list_page_id' ) ) ); ?>"><?php esc_html_e( 'All', 'mercury' ); ?></a>
							</li>
						<?php } ?>
						<?php
						$current_tax = get_queried_object();
						foreach ( $categories as $category ) {
							if ( $current_tax->slug === $category->slug ) { ?>
								<li class="active"><?php echo esc_html( $category->name ); ?></li>
							<?php } else { ?>
								<li>
									<a href="<?php echo esc_url( get_term_link( $category->slug, 'casino-category' ) ); ?>" title="<?php echo esc_attr( $category->name ); ?>"><?php echo esc_html( $category->name ); ?></a>
								</li>
							<?php }
						} ?>
					</ul>
				</div>
				<?php }
			} ?>

			<?php $term_id = get_queried_object()->term_id; ?>

			<!-- Casino List Start -->
			<div class="space-companies-archive-items box-100 relative">
				<?php
				$casinos_archive_style = get_theme_mod( 'mercury_casino_archive_style' );

				if ( in_array( $casinos_archive_style, [ 4, 7, 8, 9 ] ) ) {
					$paged    = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
					$wp_query = new WP_Query( [
						'post_type' => 'casino',
						'paged'     => $paged,
						'meta_key'  => 'casino_overall_rating',
						'orderby'   => 'meta_value_num',
						'order'     => 'DESC',
						'tax_query' => [ [
							'taxonomy' => 'casino-category',
							'field'    => 'id',
							'terms'    => $term_id,
						] ],
					] );
				}

				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						if ( $casinos_archive_style == 2 ) {
							get_template_part( '/aces/casino-item-style-2' );
						} elseif ( $casinos_archive_style == 3 ) {
							get_template_part( '/aces/casino-item-style-3' );
						} elseif ( $casinos_archive_style == 4 ) {
							get_template_part( '/aces/casino-item-style-4' );
						} elseif ( $casinos_archive_style == 5 ) {
							get_template_part( '/aces/casino-item-style-5' );
						} elseif ( $casinos_archive_style == 6 ) {
							get_template_part( '/aces/casino-item-style-6' );
						} elseif ( $casinos_archive_style == 7 ) {
							get_template_part( '/aces/casino-item-style-7' );
						} elseif ( $casinos_archive_style == 8 ) {
							get_template_part( '/aces/casino-item-style-8' );
						} elseif ( $casinos_archive_style == 9 ) {
							get_template_part( '/aces/casino-item-style-9' );
						} else {
							get_template_part( '/aces/casino-item-style-1' );
						}
					endwhile;

					the_posts_pagination( [
						'end_size'  => 2,
						'prev_text' => esc_html__( '&laquo;', 'mercury' ),
						'next_text' => esc_html__( '&raquo;', 'mercury' ),
					] );

				else : ?>
					<div class="space-page-content-wrap relative">
						<div class="space-page-content page-template box-100 relative">
							<h2><?php esc_html_e( 'Posts not found', 'mercury' ); ?></h2>
							<p><?php esc_html_e( 'No posts has been found. Please return to the homepage.', 'mercury' ); ?></p>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<!-- Casino List End -->

			<?php if ( ! is_paged() ) {
				// ── Description (стандартное поле) ───────────────────────
				if ( term_description() ) { ?>
					<div class="space-taxonomy-description box-100 relative">
						<div class="space-page-content case-15 relative">
							<?php echo term_description(); ?>
						</div>
					</div>
				<?php }
				// ── SEO Content (наше поле, шорткоды работают) ──────────
				$seo_intro = get_term_meta( $term_id, '_xg_seo_intro', true );
				if ( $seo_intro ) { ?>
					<div class="space-taxonomy-description box-100 relative">
						<div class="space-page-content case-15 relative">
							<?php echo do_shortcode( wpautop( wp_kses_post( $seo_intro ) ) ); ?>
						</div>
					</div>
				<?php }
			} ?>

		</div>
	</div>
</div>

<!-- Archive Section End -->

<?php get_footer(); ?>
