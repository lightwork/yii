<?php

/**
 *
 * http://www.php.net/manual/en/function.mcrypt-encrypt.php#78531
 *
 */
class Cipher extends CApplicationComponent
{

	/**
	 * Get a random Initialization Vector.
	 * The reason this is provided, instead of relying on mcrypt_create_iv, is
	 * because mcrypt_create_iv can be *really* slow.
	 * @param int $size
	 * @return string
	 */
	public function getNewIv($size = 32)
	{
		return $this->randomCharacters($size);
	}

	/**
	 * Get a random salt.
	 * @param int $size
	 * @return string
	 */
	public function getNewSalt($size = 45)
	{
		return $this->randomCharacters($size * 2);
	}

	/**
	 * Encrypt a given input.
	 *
	 * @param string $input
	 * @param string $iv
	 * @param string $salt
	 * @return string A base-64 encoded string.
	 */
	public function encrypt($input, $iv, $salt = null)
	{
		if(!isset($salt)) { $salt = $this->salt; }
		$securekey = hash('sha256',$salt,TRUE);
		return $this->_encrypt($input, $securekey, $iv);
	}

	/**
	 * Decrypt a given input.
	 *
	 * @param string $input The previously encrypted input.
	 * @param string $iv The init vector used to create the encrypted value.
	 * @param string $salt The secret salt used to create the encrypted value.
	 *
	 * @return string The decoded value.
	 */
	public function decrypt($input, $iv, $salt = null)
	{
		if(!isset($salt)) { $salt = $this->salt; }
		$securekey = hash('sha256',$salt,TRUE);
		return $this->_decrypt($input, $securekey, $iv);
	}

    private function _encrypt($input, $securekey, $iv) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $securekey, $input, MCRYPT_MODE_ECB, $iv));
    }

    private function _decrypt($input, $securekey, $iv) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $securekey, base64_decode($input), MCRYPT_MODE_ECB, $iv));
    }

	private function randomCharacters($size = 32)
	{
		$iv = '';
		for($i = 0; $i < $size; $i++) {
			$iv .= chr(rand(0,255));
		}
		return $iv;
	}
}