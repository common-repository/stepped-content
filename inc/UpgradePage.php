<?php
if( !class_exists( 'SPTUpgradePage' ) ){
	class SPTUpgradePage{
		public function __construct(){
			add_action( 'admin_menu', [$this, 'adminMenu'] );
			add_action( 'admin_enqueue_scripts', [$this, 'adminEnqueueScripts'] );
		}

		function adminMenu(){
			add_submenu_page(
				'tools.php',
				__( 'Stepped Content', 'stepped-content' ),
				__( 'Stepped Content- Upgrade', 'stepped-content' ),
				'manage_options',
				'stp-upgrade',
				[ $this, 'upgradePage' ]
			);
		}

		function upgradePage(){ ?>
			<div id='bplUpgradePage'></div>
		<?php }

		function adminEnqueueScripts( $hook ) {
			if( strpos( $hook, 'stp-upgrade' ) ){
				wp_enqueue_script( 'stp-admin-upgrade', STP_DIR_URL . 'dist/admin-upgrade.js', [ 'react', 'react-dom' ], STP_VERSION );
				wp_set_script_translations( 'stp-admin-upgrade', 'stepped-content', STP_DIR_PATH . 'languages' );
			}
		}
	}
	new SPTUpgradePage;
}