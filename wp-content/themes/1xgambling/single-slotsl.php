<?php get_header(); ?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div class="space-slotslaunch-single-page box-100 relative">

	<!-- Title Box -->
	<div class="space-title-box box-100 relative">
		<div class="space-title-box-ins space-page-wrapper relative">
			<div class="space-title-box-h1 relative">
				<h1><?php the_title(); ?></h1>
				<?php if ( has_excerpt() ) : ?>
					<div class="space-page-content-excerpt box-100 relative">
						<?php the_excerpt(); ?>
					</div>
				<?php endif; ?>
				<?php get_template_part( '/theme-parts/breadcrumbs' ); ?>
			</div>
		</div>
	</div>

	<!-- Page Section -->
	<div class="space-page-section box-100 relative">
		<div class="space-page-section-ins space-page-wrapper relative">
			<div class="space-content-section box-75 left relative">
				<div class="space-page-content-wrap relative">

					<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

					<?php
					// Сохраняем ID ДО вызова the_content(), т.к. [slotsl-game] shortcode может менять глобальный $post
					$current_game_id = get_the_ID();
					$game            = get_post( $current_game_id );
					?>

					<!-- 1. Game Attributes Table -->
					<?php
					$attrs          = get_post_meta( $current_game_id, 'slot_attrs', true );
					$provider_terms = get_the_terms( $current_game_id, 'sl-provider' );
					$provider_name  = ( ! empty( $provider_terms ) && ! is_wp_error( $provider_terms ) ) ? $provider_terms[0]->name : '';

					$rows = [
						'Provider'   => $provider_name,
						'RTP'        => ! empty( $attrs['rtp'] ) ? $attrs['rtp'] . '%' : '',
						'Volatility' => ! empty( $attrs['volatility'] ) ? $attrs['volatility'] : '',
						'Reels'      => ! empty( $attrs['reels'] ) ? $attrs['reels'] : '',
						'Paylines'   => ! empty( $attrs['payline'] ) ? $attrs['payline'] : '',
						'Min Bet'    => ! empty( $attrs['min_bet'] ) ? '$' . $attrs['min_bet'] : '',
						'Max Bet'    => ! empty( $attrs['max_bet'] ) ? '$' . $attrs['max_bet'] : '',
						'Max Win'    => ! empty( $attrs['max_win_per_spin'] ) ? $attrs['max_win_per_spin'] . 'x' : '',
					];

					$rows = array_filter( $rows );

					if ( ! empty( $rows ) ) : ?>
					<div class="slotsl-game-info-table" style="margin: 0 0 30px;">
						<h3 style="margin-bottom: 15px;">Game Information</h3>
						<table style="width:100%; border-collapse:collapse;">
							<?php foreach ( $rows as $label => $value ) : ?>
							<tr style="border-bottom: 1px solid #eee;">
								<td style="padding: 10px 12px; font-weight:600; width:40%; color:#555;"><?php echo esc_html( $label ); ?></td>
								<td style="padding: 10px 12px;"><?php echo esc_html( $value ); ?></td>
							</tr>
							<?php endforeach; ?>
						</table>
					</div>
					<?php endif; ?>

					<!-- 2. Контент поста: игра [slotsl-game] + уникальный текст из редактора -->
					<div class="space-page-content-box-wrap relative">
						<div class="space-page-content box-100 relative">
							<?php
							the_content();
							wp_link_pages( array(
								'before'      => '<div class="clear"></div><nav class="navigation pagination-post">' . esc_html__( 'Pages:', 'mercury' ),
								'after'       => '</nav>',
								'link_before' => '<span class="page-number">',
								'link_after'  => '</span>',
							) );
							?>
						</div>
					</div>

					<!-- 4. Лучшие казино -->
					<div class="slotsl-section-casinos" style="margin-top: 40px;">
						<h2 class="has-mercury-main-color has-text-color" style="font-size: 1.5em;">Best Online Casinos</h2>
						<p style="color:#7f8c8d; font-weight:100;">18+ New customers only. Deposit required. T&Cs apply</p>
						<?php echo do_shortcode( '[aces-casinos-4 items_number="5" external_link="1" category="" items_id="" exclude_id="" game_id="' . get_the_ID() . '" show_title="1" order="DESC" orderby="rating" title=""]' ); ?>
					</div>

					<!-- 5. Топ бонусы -->
					<div class="slotsl-section-bonuses" style="margin-top: 40px;">
						<h2 class="has-mercury-main-color has-text-color" style="font-size: 1.5em;">Featured Bonuses</h2>
						<?php echo do_shortcode( '[aces-bonuses-1 items_number="3" external_link="1" category="" items_id="" exclude_id="" parent_id="" columns="3" order="" orderby="DESC" title=""]' ); ?>
					</div>

					<!-- 6. Похожие игры (того же провайдера) -->
					<?php
					$provider_terms = get_the_terms( $current_game_id, 'sl-provider' );
					$provider_slug  = ( ! empty( $provider_terms ) && ! is_wp_error( $provider_terms ) ) ? $provider_terms[0]->slug : '';
					if ( $provider_slug ) : ?>
					<div class="slotsl-section-related" style="margin-top: 40px;">
						<h2 class="has-mercury-main-color has-text-color" style="font-size: 1.5em;">More <?php echo esc_html( $provider_terms[0]->name ); ?> Games</h2>
						<?php echo do_shortcode( '[slotsl-game-archives provider="' . esc_attr( $provider_slug ) . '" show_header="false" show_button="true" per_page="6" show_pagination="false"]' ); ?>
					</div>
					<?php endif; ?>

					<?php endwhile; endif; ?>

				</div>

				<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
				<?php endif; ?>

			</div>

			<!-- Sidebar -->
			<div class="space-sidebar-section box-25 right relative">
				<?php get_sidebar(); ?>
			</div>

		</div>
	</div>

</div>
</div>

<?php get_footer(); ?>
