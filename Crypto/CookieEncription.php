<?php declare( strict_types=1 );

//$key = random_bytes(SODIUM_CRYPTO_AUTH_KEYBYTES);
//
//// Using your key to encrypt information
//$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
//$ciphertext = sodium_crypto_secretbox('This is the encripted text', $nonce, $key);
////echo "nonce: " . $nonce . "\n";
////echo "ciphertext: ". $ciphertext . "\n";
//
//
//
//$plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
////echo "plaintext: ". $plaintext  . "\n";
//
////echo "-------------------------\n";
//
//$key = random_bytes(SODIUM_CRYPTO_AUTH_KEYBYTES);
//$message = json_encode(['username'=> 'pass']);
//$mac = sodium_crypto_auth($message, $key);
//$outbound = $mac . $message;
//
//if (sodium_crypto_auth_verify($mac, $message, $key)) {
//	$data = json_decode($message, true);
////	var_dump( $data );
//} else {
//	sodium_memzero($key);
//	throw new Exception("Malformed message or invalid MAC");
//}

class SodiumCookie {
	private $key;

	/**
	 * Sets the encryption key
	 *
	 * @param string $key
	 */
	public function __construct( $key ) {
		$this->key = $key;
	}

	/**
	 * Reads an encrypted cookie
	 *
	 * @param string $index
	 *
	 * @return string
	 * @throws Exception
	 */
	public function read( $index ) {
		if ( ! array_key_exists( $index, $_COOKIE ) ) {
			return null;
		}
		$cookie = sodium_hex2bin( $_COOKIE[ $index ] );
		list ( $encKey, $authKey ) = $this->splitKeys( $index );

		$mac        = mb_substr(
			$cookie,
			0,
			SODIUM_CRYPTO_AUTH_BYTES,
			'8bit'
		);
		$nonce      = mb_substr(
			$cookie,
			SODIUM_CRYPTO_AUTH_BYTES,
			SODIUM_CRYPTO_STREAM_NONCEBYTES,
			'8bit'
		);
		$ciphertext = mb_substr(
			$cookie,
			SODIUM_CRYPTO_AUTH_BYTES + SODIUM_CRYPTO_STREAM_NONCEBYTES,
			null,
			'8bit'
		);

		if ( sodium_crypto_auth_verify( $mac, $nonce . $ciphertext, $authKey ) ) {
			sodium_memzero( $authKey );
			$plaintext = sodium_crypto_stream_xor( $ciphertext, $nonce, $encKey );
			sodium_memzero( $encKey );
			if ( $plaintext !== false ) {
				return $plaintext;
			}
		} else {
			sodium_memzero( $authKey );
			sodium_memzero( $encKey );
		}
		throw new Exception( 'Decryption failed.' );
	}

	/**
	 * Writes an encrypted cookie
	 *
	 * @param string $index
	 * @param string $value
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function write( $index, $value ) {
		$nonce = random_bytes( SODIUM_CRYPTO_STREAM_NONCEBYTES );
		[ $encKey, $authKey ] = $this->splitKeys( $index );
		$ciphertext = sodium_crypto_stream_xor( $value, $nonce, $encKey );
		sodium_memzero( $value );
		$mac = sodium_crypto_auth( $nonce . $ciphertext, $authKey );
		sodium_memzero( $encKey );
		sodium_memzero( $authKey );

		return setcookie( $index, sodium_bin2hex( $mac . $nonce . $ciphertext ) );
	}

	/**
	 * Just an example. In a real system, you want to use HKDF for
	 * key-splitting instead of just a keyed BLAKE2b hash.
	 *
	 * @param string $cookieName Cookie Name
	 *
	 * @return array(2) [encryption key, authentication key]
	 */
	private function splitKeys( $cookieName ) {
		$encKey  = sodium_crypto_generichash( sodium_crypto_generichash( 'encryption', $cookieName ), $this->key, SODIUM_CRYPTO_STREAM_KEYBYTES );
		$authKey = sodium_crypto_generichash( sodium_crypto_generichash( 'authentication', $cookieName ), $this->key, SODIUM_CRYPTO_AUTH_KEYBYTES );

		return [ $encKey, $authKey ];
	}
}

$key = random_bytes( SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
$sc  = new SodiumCookie( $key );
$sc->write( 'sensitive', $value );

//Reading Cookie
try {
	$value = $sc->read( 'sensitive' );
} catch ( Exception $ex ) {
	// Handle the exception here
}


/// OR enother alternative diffrent implementation
///
class SodiumCookieB {

	/*
	// At some point, we run this command:
	$key = \Sodium\randombytes_buf(\Sodium\CRYPTO_SECRETBOX_KEYBYTES);
	*/

	/**
	 * Store ciphertext in a cookie
	 *
	 * @param string $name - cookie name
	 * @param mixed $cookieData - cookie data
	 * @param string $key - crypto key
	 *
	 * @return bool
	 */
	function setSafeCookie( $name, $cookieData, $key ) {
		$nonce = \Sodium\randombytes_buf( \Sodium\CRYPTO_SECRETBOX_NONCEBYTES );

		return setcookie(
			$name,
			base64_encode(
				$nonce .
				\Sodium\crypto_secretbox(
					json_encode( $cookieData ),
					$nonce,
					$key
				)
			)
		);
	}

	/**
	 * Decrypt a cookie, expand to array
	 *
	 * @param string $name - cookie name
	 * @param string $key - crypto key
	 *
	 * @return array|mixed
	 */
	function getSafeCookie( $name, $key ) {
		if ( ! isset( $_COOKIE[ $name ] ) ) {
			return array();
		}

		$decoded    = base64_decode( $_COOKIE[ $name ] );
		$nonce      = mb_substr( $decoded, 0, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
		$ciphertext = mb_substr( $decoded, \Sodium\CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );
		$decrypted  = sodium_crypto_secretbox_open( $ciphertext, $nonce, $key );

		if ( empty( $decrypted ) ) {
			return array();
		}

		return json_decode( $decrypted, true );
	}
}