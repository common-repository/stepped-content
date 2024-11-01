<?php

if( !class_exists( 'STPBlock' ) ){
	class STPBlock{
		function __construct(){
			add_action( 'init', [ $this, 'onInit' ] );
		}

		function onInit() {
			wp_register_style( 'stp-content-style', STP_DIR_URL . 'dist/style.css', [], STP_VERSION ); // Style
			wp_register_style( 'stp-content-editor-style', STP_DIR_URL . 'dist/editor.css', [ 'stp-content-style' ], STP_VERSION ); // Backend Style
	
			register_block_type( __DIR__, [
				'editor_style'		=> 'stp-content-editor-style',
				'render_callback'	=> [$this, 'render']
			] ); // Register Block
	
			wp_set_script_translations( 'stp-content-editor-script', 'stepped-content', STP_DIR_PATH . 'languages' );
		}

		function render( $attributes, $content ){
			extract( $attributes );
	
			wp_enqueue_style( 'stp-content-style' );
			wp_enqueue_script( 'stp-content-script', STP_DIR_URL . 'dist/script.js', [ 'react', 'react-dom' ], STP_VERSION, true );
			wp_set_script_translations( 'stp-content-script', 'stepped-content', STP_DIR_PATH . 'languages' );
	
			$className = $className ?? '';
			$blockClassName = "wp-block-stp-content $className align$align";
	
			ob_start(); ?>
			<div class='<?php echo esc_attr( $blockClassName ); ?>' id='stpContent-<?php echo esc_attr( $cId ); ?>' data-props='<?php echo esc_attr( wp_json_encode( [ 'attributes' => $attributes, 'content' => $content ] ) ); ?>'></div>
	
			<?php return ob_get_clean();
		} // Render
	}
	new STPBlock();
}

require_once STP_DIR_PATH . 'inc/child/block.php';