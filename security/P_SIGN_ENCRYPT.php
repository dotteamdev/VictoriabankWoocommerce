<?php

function P_SIGN_ENCRYPT($OrderId, $Timestamp, $trtType, $Amount, $nounce, $encryptionType = 'MD5')
{
	$MAC  = '';
	$RSA_KeyPath = PLUGIN_DIR . get_key('Private key');
	$RSA_Key = file_get_contents ($RSA_KeyPath);
	$Data = array (
			'ORDER' => $OrderId,
			'NONCE' => $nounce,
			'TIMESTAMP' => $Timestamp,
			'TRTYPE' => $trtType,
			'AMOUNT' => $Amount
		);
		
	if (!$RSA_KeyResource = openssl_get_privatekey ($RSA_Key)) die ('Failed get private key');
	$RSA_KeyDetails = openssl_pkey_get_details ($RSA_KeyResource);
	$RSA_KeyLength = $RSA_KeyDetails['bits']/8;
	
	foreach ($Data as $Id => $Filed) $MAC .= strlen ($Filed).$Filed;

	$P_SIGN = '';
	 
	switch ($encryptionType) {
		case 'MD5':
			$P_SIGN = encrypt_by_md5($RSA_KeyLength, $MAC, $RSA_Key);
			break;
		
		case 'SHA-256':
			$P_SIGN = encrypt_by_sha256($MAC, $RSA_Key);
			break;
	}
	
	return strtoupper ($P_SIGN);
}

function encrypt_by_md5($RSA_KeyLength, $MAC, $RSA_Key)
{
	$First = '0001';
	$Prefix = '003020300C06082A864886F70D020505000410';

	$Hash = md5($MAC);

	$Data = $First;
	
	$paddingLength = $RSA_KeyLength - strlen ($Hash)/2 - strlen ($Prefix)/2 - strlen ($First)/2;
	for ($i = 0; $i < $paddingLength; $i++) $Data .= "FF";
	
	$Data .= $Prefix.$Hash;
	$BIN = pack("H*", $Data);
	
	if (!openssl_private_encrypt ($BIN, $EncryptedBIN, $RSA_Key, OPENSSL_NO_PADDING)) 
	{
		while ($msg = openssl_error_string()) echo $msg . "<br />\n";
		die ('Failed encrypt');
	}
	
	$P_SIGN = bin2hex ($EncryptedBIN);

	return $P_SIGN;
}

function encrypt_by_sha256($MAC, $RSA_Key)
{
	openssl_sign($MAC, $signature, $RSA_Key, OPENSSL_ALGO_SHA256);

    $P_SIGN = bin2hex ($signature);
    return $P_SIGN;
}

function get_key($key_name) {
	global $wpdb;

	$table = $wpdb->prefix . 'vb_payments_settings';

	$sql = "SELECT * FROM ". $table;

	$merchant_data = $wpdb->get_results($sql);

	foreach($merchant_data as $setting) {
		if($setting->name === $key_name) {
			return $setting->value;
		}   
	}
}


