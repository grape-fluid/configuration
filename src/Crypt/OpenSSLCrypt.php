<?php

namespace Grapesc\GrapeFluid\Configuration\Crypt;


/**
 * @author Mira Jakes <jakes@grapesc.cz>
 */
class OpenSSLCrypt implements ICrypt
{

	/** @var string */
	private $cryptKey;

	/** @var string */
	private $initializationVector = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";
	
	/** @var string */
	private $cipherMethod;


	/**
	 * OpenSSLCrypt constructor.
	 * @param string $cryptKey
	 * @param string $cipherMethod
	 */
	public function __construct($cryptKey, $cipherMethod = "AES-128-CFB8")
	{
		$this->cryptKey     = $cryptKey;
		$this->cipherMethod = $cipherMethod;
	}

	
	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function encrypt($data)
	{
		if (function_exists('openssl_encrypt') && in_array($this->cipherMethod, openssl_get_cipher_methods())) {
			$data = openssl_encrypt($data,$this->cipherMethod,	$this->cryptKey,OPENSSL_RAW_DATA, $this->initializationVector);
		}

		return base64_encode($data);
	}
	

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function decrypt($data)
	{
		$data = base64_decode($data);
		if (function_exists('openssl_decrypt') && in_array($this->cipherMethod, openssl_get_cipher_methods())) {
			return openssl_decrypt($data,$this->cipherMethod, $this->cryptKey,OPENSSL_RAW_DATA, $this->initializationVector);
		}

		return $data;
	}
	
}