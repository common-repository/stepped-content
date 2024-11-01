<?php
class STPStep{
	public function __construct(){
		add_action( 'init', [$this, 'onInit'] );
	}

	function onInit(){
		register_block_type( __DIR__, [
			'render_callback' => [$this, 'render']
		] ); // Register Block
	}

	function getBackgroundCSS($bg, $isSolid = true, $isGradient = true, $isImage = true) {
		extract( $bg );
		$type = $type ?? 'solid';
		$color = $color ?? '#000000b3';
		$gradient = $gradient ?? 'linear-gradient(135deg, #4527a4, #8344c5)';
		$image = $image ?? [];
		$position = $position ?? 'center center';
		$attachment = $attachment ?? 'initial';
		$repeat = $repeat ?? 'no-repeat';
		$size = $size ?? 'cover';
		$overlayColor = $overlayColor ?? '#000000b3';

		$gradientCSS = $isGradient ? "background: $gradient;" : '';

		$imgUrl = $image['url'] ?? '';
		$imageCSS = $isImage ? "background: url($imgUrl); background-color: $overlayColor; background-position: $position; background-size: $size; background-repeat: $repeat; background-attachment: $attachment; background-blend-mode: overlay;" : '';

		$solidCSS = $isSolid ? "background: $color;" : '';
	
		$styles = 'gradient' === $type ? $gradientCSS : ( 'image' === $type ? $imageCSS : $solidCSS );
	
		return $styles;
	}

	function render( $attributes, $content ){
		extract( $attributes );

		$className = $className ?? '';
		$blockClassName = 'wp-block-stp-step stpStep' . $className;

		// Styles
		$bgStyle = $this->getBackgroundCSS($background);
		$styles = "
			#stpStep-$cId .instructions{
				$bgStyle
			}
			#stpStep-$cId .instructions .instructionTitle,
			#stpStep-$cId .instructions .instructionContent{
				color: $contentColor;
			}
		";

		ob_start(); ?>
		<div class='<?php echo esc_attr( $blockClassName ); ?>' id='stpStep-<?php echo esc_attr( $cId ); ?>'>
			<style><?php echo esc_html( $styles ); ?></style>

			<!-- <div class='stepInner'> -->
			<div class='instructions'>
				<?php if($isTitle) { ?>
					<h2 class='instructionTitle'>
						<?php echo esc_html( $step ); ?>. <?php echo esc_html( $title ); ?>
					</h2>
				<?php } else {} ?>

				<div class='instructionContent'>
					<?php echo wp_kses_post( $content ); ?>
				</div>
			</div>
			<!-- </div> -->
		</div>

		<?php return ob_get_clean();
	}
}
new STPStep();