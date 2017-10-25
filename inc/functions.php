<?php

class CA62926 {

	static function saveSignature( $data ) {

		$content_path = '/uploads/sig/' . self::randomFileName() . '.png';

		/**
		 * Maybe create folder
		 */
		wp_mkdir_p( dirname( WP_CONTENT_DIR . $content_path ) );

		/**
		 * Prepare data.
		 */
		list( $type, $data ) = explode( ';', $data );
		list( , $data )      = explode( ',', $data );
		$data                = base64_decode( $data );

		/**
		 * Create file.
		 */
		file_put_contents( WP_CONTENT_DIR . $content_path, $data );
		return [
			'url'          => content_url( $content_path ),
		];
	}

	static function randomFileName() {
		return strftime( '%Y%m%d-%H%M%S-' ) . substr( md5( mt_rand() ), 0, 18 );
	}
}
