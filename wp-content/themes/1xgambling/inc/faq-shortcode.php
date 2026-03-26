<?php
/**
 * FAQ Shortcode с FAQPage Schema (JSON-LD)
 *
 * Использование:
 * [faq]
 * [faq_item question="Вопрос?"]Ответ здесь[/faq_item]
 * [faq_item question="Второй вопрос?"]Второй ответ[/faq_item]
 * [/faq]
 */

// Глобальный массив для сбора FAQ items для schema
global $xg_faq_items;
$xg_faq_items = [];

// ─────────────────────────────────────────
// Shortcode: [faq_item question="..."]...[/faq_item]
// ─────────────────────────────────────────
function xg_faq_item_shortcode( $atts, $content = '' ) {
	global $xg_faq_items;

	$atts = shortcode_atts( [ 'question' => '' ], $atts );

	$question = sanitize_text_field( $atts['question'] );
	$answer   = wp_kses_post( do_shortcode( $content ) );

	// Сохраняем для schema
	$xg_faq_items[] = [
		'question' => $question,
		'answer'   => wp_strip_all_tags( $answer ),
	];

	// HTML аккордеон
	return sprintf(
		'<div class="xg-faq-item">
			<button class="xg-faq-question" aria-expanded="false">
				%1$s
				<span class="xg-faq-icon" aria-hidden="true">+</span>
			</button>
			<div class="xg-faq-answer" hidden>
				<div class="xg-faq-answer-inner">%2$s</div>
			</div>
		</div>',
		esc_html( $question ),
		$answer
	);
}
add_shortcode( 'faq_item', 'xg_faq_item_shortcode' );

// ─────────────────────────────────────────
// Shortcode: [faq]...[/faq]
// ─────────────────────────────────────────
function xg_faq_shortcode( $atts, $content = '' ) {
	global $xg_faq_items;

	// Сбрасываем items перед каждым FAQ блоком
	$xg_faq_items = [];

	// Рендерим faq_item shortcodes внутри
	$items_html = do_shortcode( $content );

	// Генерируем JSON-LD schema
	if ( ! empty( $xg_faq_items ) ) {
		$schema = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array_map( function( $item ) {
				return [
					'@type'          => 'Question',
					'name'           => $item['question'],
					'acceptedAnswer' => [
						'@type' => 'Answer',
						'text'  => $item['answer'],
					],
				];
			}, $xg_faq_items ),
		];

		$schema_json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );

		// Добавляем schema в footer
		add_action( 'wp_footer', function() use ( $schema_json ) {
			echo '<script type="application/ld+json">' . $schema_json . '</script>';
		} );
	}

	return sprintf(
		'<div class="xg-faq">%s</div>',
		$items_html
	);
}
add_shortcode( 'faq', 'xg_faq_shortcode' );

// ─────────────────────────────────────────
// CSS + JS стили аккордеона
// ─────────────────────────────────────────
function xg_faq_assets() {
	wp_enqueue_style( 'xg-faq', get_stylesheet_directory_uri() . '/css/faq.css', [], '1.0' );

	wp_register_script( 'xg-faq', false, [], false, true );
	wp_enqueue_script( 'xg-faq' );
	wp_add_inline_script( 'xg-faq', '
		document.addEventListener("DOMContentLoaded", function() {
			document.querySelectorAll(".xg-faq-question").forEach(function(btn) {
				btn.addEventListener("click", function() {
					var answer  = this.nextElementSibling;
					var isOpen  = this.getAttribute("aria-expanded") === "true";

					var faq = this.closest(".xg-faq");
					faq.querySelectorAll(".xg-faq-question").forEach(function(b) {
						b.setAttribute("aria-expanded", "false");
						b.nextElementSibling.hidden = true;
					});

					if ( ! isOpen ) {
						this.setAttribute("aria-expanded", "true");
						answer.hidden = false;
					}
				});
			});
		});
	' );
}
add_action( 'wp_enqueue_scripts', 'xg_faq_assets' );
