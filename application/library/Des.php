<?php
class Des {
	private $key; //私钥，允许的最大字节长度56位
	private $iv; //偏移量，固定字节长度为8位
	private $cipher;
	public function __construct($key, $iv = 0) {
		if (!$key) {
			Utility::errorlog('php des ' . json_encode(debug_backtrace()));
			exit;
		}

		$this->key = $key;
		if ($iv == 0) {
			$this->iv = $key;
		} else {
			$this->iv = $iv;
		}
		$this->cipher = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_CBC, '');
		//var_dump(mcrypt_enc_get_iv_size($this->cipher),mcrypt_enc_get_key_size($this->cipher),mcrypt_enc_get_block_size($this->cipher));exit;
	}

	public function encrypt($str) {
		$size = mcrypt_enc_get_block_size($this->cipher);
		$str = $this->pkcs5Pad($str, $size);
		mcrypt_generic_init($this->cipher, $this->key, $this->iv);
		$data = mcrypt_generic($this->cipher, $str);
		mcrypt_generic_deinit($this->cipher);
		return base64_encode($data);
	}

	public function decrypt($str) {
		$str = base64_decode($str);
		mcrypt_generic_init($this->cipher, $this->key, $this->iv);
		$data = mdecrypt_generic($this->cipher, $str);
		$data = rtrim($data, "\0");
		mcrypt_generic_deinit($this->cipher);
		$data = $this->pkcs5Unpad($data);
		return $data;
	}

	public function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen($text) % $blocksize);
		return $text . str_repeat(chr($pad), $pad);
	}

	public function pkcs5Unpad($text) {
		$pad = ord($text{strlen($text) - 1});
		if ($pad > strlen($text)) {
			return false;
		}

		if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
			return false;
		}

		return substr($text, 0, -1 * $pad);
	}

	public function __destruct() {
		mcrypt_module_close($this->cipher);
	}

}
