<?php
/**
 * Добавляет поле "SEO Intro" (над списком) к таксономиям casino-category, game-category, bonus-category.
 * Поле Description (стандартное) остаётся и выводится внизу как обычно.
 */

$xg_seo_taxonomies = [ 'casino-category', 'game-category', 'bonus-category' ];

foreach ( $xg_seo_taxonomies as $tax ) {
	add_action( "{$tax}_edit_form", 'xg_taxonomy_seo_fields', 10, 2 );
	add_action( "edited_{$tax}", 'xg_taxonomy_seo_save', 10, 2 );
}

function xg_taxonomy_seo_fields( $term, $taxonomy ) {
	$intro = get_term_meta( $term->term_id, '_xg_seo_intro', true );
	wp_nonce_field( 'xg_taxonomy_seo_save', 'xg_taxonomy_seo_nonce' );
	?>
	<tr class="form-field">
		<th scope="row">
			<label><?php esc_html_e( 'SEO Content (below list)', '1xgambling' ); ?></label>
		</th>
		<td>
			<p style="color:#666;margin-bottom:8px;font-size:13px;">
				SEO-текст — выводится <strong>под</strong> списком. Можно использовать шорткоды, H2, списки и т.д.<br>
				<em>Поле "Description" выше страницы выводится над списком.</em>
			</p>
			<?php
			wp_editor( $intro, 'xg_seo_intro', [
				'textarea_name' => 'xg_seo_intro',
				'media_buttons' => false,
				'teeny'         => false,
				'textarea_rows' => 6,
				'tinymce'       => true,
				'quicktags'     => true,
			] );
			?>
		</td>
	</tr>
	<?php
}

function xg_taxonomy_seo_save( $term_id, $tt_id ) {
	if ( ! isset( $_POST['xg_taxonomy_seo_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['xg_taxonomy_seo_nonce'], 'xg_taxonomy_seo_save' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	if ( isset( $_POST['xg_seo_intro'] ) ) {
		update_term_meta( $term_id, '_xg_seo_intro', wp_kses_post( $_POST['xg_seo_intro'] ) );
	}
}
