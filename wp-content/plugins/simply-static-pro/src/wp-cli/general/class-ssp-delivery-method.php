<?php

namespace simply_static_pro\commands\general;

use simply_static_pro\commands\deployment\Bunny_CDN;
use simply_static_pro\commands\deployment\Github;
use simply_static_pro\commands\deployment\Tiiny;
use simply_static_pro\commands\Update_Command;

class Delivery_Method extends Update_Command {

	protected $section = 'general';

	protected $name = 'delivery_method';

	protected $option_name = 'delivery_method';

	protected $description = 'Update Delivery Method.';

	public function get_synopsis() {

		$methods = $this->get_methods();

		$synopsis = [
			array(
				'type'        => 'positional',
				'name'        => 'value',
				'description' => "The value to save as Delivery Method.\nAvailable options: \n" . array_reduce( array_keys( $methods ), function ( $carry, $item ) use ( $methods ) {
						return $carry .= '- ' . $item . ' : ' . $methods[ $item ] . "\n";
					} ),
				'optional'    => false,
				'repeating'   => false,
			)
		];

		return array_merge( $synopsis, parent::get_synopsis() ); // TODO: Change the autogenerated stub
	}

	protected function get_methods() {
		return [
			'zip'        => 'ZIP Archive',
			'local'      => 'Local Directory',
			'simply-cdn' => 'Simply CDN',
			'github'     => 'Github',
			'tiiny'      => 'Tiiny.host',
			'cdn'        => 'BunnyCDN',
		];
	}

	/**
	 * Run
	 *
	 * @param $args
	 * @param $options
	 *
	 * @return void
	 */
	public function run( $args, $options ) {
		$update_value = $args[0];
		$methods      = $this->get_methods();

		if ( ! isset( $methods[ $update_value ] ) ) {
			\WP_CLI::error( "No such method available. Please choose one of the following: \n" . array_reduce( array_keys( $methods ), function ( $carry, $item ) use ( $methods ) {
					return $carry .= '- ' . $item . ' : ' . $methods[ $item ] . "\n";
				} ) );

			return;
		}

		$this->update( $update_value );
		\WP_CLI::success( 'Updated!' );

		$this->has_further_steps( $update_value );
	}

	public function has_further_steps( $type ) {
		$method_name = str_replace( '-', '_', $type );
		if ( method_exists( $this, $method_name ) ) {
			$this->{$method_name}();
		}
	}

	public function local() {
		$local_dir = new Local_Directory();
		\WP_CLI::line( \WP_CLI::colorize( "%YPlease Run Command \"" . $local_dir->get_name() . '" to set the local directory' ) );
	}

	public function simply_cdn() {
		$local_dir = new Simply_CDN();
		\WP_CLI::line( \WP_CLI::colorize( "%YPlease Run Command \"" . $local_dir->get_name() . '" to connect to Simply CDN if not connected already.' ) );
	}

	public function github() {
		/*
		$command = new Github();
		\WP_CLI::line(\WP_CLI::colorize( "%YPlease Run Command \"" . $command->get_name() . '" to setup GitHub deployment.' ));*/
		\WP_CLI::line( \WP_CLI::colorize( "%YPlease set the Github Deployment in the Simply Static > Settings to create, delete or set an existing repository." ) );
	}

	public function tiiny() {
		$command = new Tiiny();
		\WP_CLI::line( \WP_CLI::colorize( "%YPlease Run Command \"" . $command->get_name() . '" to setup Tiiny.Host deployment.' ) );
	}

	public function cdn() {
		$command = new Bunny_CDN();
		\WP_CLI::line( \WP_CLI::colorize( "%YPlease Run Command \"" . $command->get_name() . '" to setup BunnyCDN deployment.' ) );

		$response = $this->ask( 'Want to see info of it? [y/n]' );
		if ( $response === 'y' ) {
			\WP_CLI::runcommand( 'help ' . $command->get_name() );
		}
	}
}