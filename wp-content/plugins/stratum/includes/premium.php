<?php

namespace Stratum;

class Premium {

	private static $instance = null;

	/**
	 * Premium constructor.
	*/
	public function __construct() {

		add_action( 'admin_menu', array($this, 'admin_menu'), 99 );
	}

	public static function get_instance()
	{
		if (self::$instance == null)
		{
			self::$instance = new Premium();
		}
		return self::$instance;
	}

    public function admin_menu() {

        add_submenu_page(
	        'stratum',
	        esc_html__( 'Premium', 'stratum' ),
	        esc_html__( 'Premium', 'stratum' ),
	        'manage_options',
	        'stratum-premium',
	        array( $this, 'premium_page' )
	    );
    }

	public function premium_page() {

		$settings = \Stratum\Settings::get_instance();
		$pluginData = $settings->getPluginData();

		$storeUrl = isset( $pluginData['PluginURI'] ) ? $pluginData['PluginURI'] : '#';

		$storeUrl = add_query_arg(
			array(
				'utm_source'   => 'dashboard',
				'utm_medium'   => 'go-premium-button',
				'utm_campaign' => 'stratum-premium',
			),
			$storeUrl
		)

		?>
		<div class="wrap">
			<h1><?php esc_html_e('Go Premium', 'stratum'); ?></h1>
			<div class="card">
				<p><?php esc_html_e('Stratum Pro unlocks access to numerous customization settings in many widgets. Work faster by employing your custom templates, achieve fancier motion effects and shapes, design better.', 'stratum'); ?></p>
				<a href="<?php echo esc_url($storeUrl); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e('Go Premium', 'stratum'); ?></a>
			</div>
		</div>
		<?php
	}

}

new Premium();