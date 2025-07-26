<?php

if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin( $str ) {
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}

function P_SIGN_DECRYPT($P_SIGN, $ACTION, $RC, $RRN, $ORDER, $AMOUNT, $ENCRYPTION_TYPE = 'MD5')
{
	$MAC = '';
	$RSA_KeyPath = PLUGIN_DIR . get_key('Bank public key');
	$RSA_Key = file_get_contents ($RSA_KeyPath);
	$InData = array (
				'ACTION' => $ACTION,
				'RC' => $RC,
				'RRN' => $RRN,
				'ORDER' => $ORDER ,
				'AMOUNT' => $AMOUNT
			);

	$RSA_KeyResource = openssl_get_publickey($RSA_Key);
	if (!$RSA_KeyResource) die ('Failed get public key');
	$RSA_KeyDetails = openssl_pkey_get_details ($RSA_KeyResource);
	$RSA_KeyLength = $RSA_KeyDetails['bits']/8;
		
	foreach($InData as $Id => $Filed) if ($Filed!= '-'  ) : $MAC .= strlen ($Filed).$Filed; else: $MAC .=$Filed; endif;
	
	$Hash_In = '';
	$First = '0001';
	$Prefix = '';

	switch ($ENCRYPTION_TYPE) {
		case 'MD5':
			$Hash_In = strtoupper(md5($MAC));
			$Prefix = '003020300C06082A864886F70D020505000410';
			$Data = $First;
	
			$paddingLength = $RSA_KeyLength - strlen ($Hash_In)/2 - strlen ($Prefix)/2 - strlen ($First)/2;
			for ($i = 0; $i < $paddingLength; $i++) $Data .= "FF";
			
			$Data .=  $Hash_In;
			break;
		
		case 'SHA-256':
			$Prefix = '3031300D060960864801650304020105000420';
			$Data = strtoupper(hash('sha256', $MAC));
			break;
	}
		
	$P_SIGNBIN = hex2bin ($P_SIGN);
	
	$OPENSSL_PUBLIC_DECRYPT = $ENCRYPTION_TYPE === 'MD5' ? openssl_public_decrypt($P_SIGNBIN, $DECRYPTED_BIN, $RSA_Key, OPENSSL_NO_PADDING) : openssl_public_decrypt($P_SIGNBIN,$DECRYPTED_BIN,$RSA_Key);

	if (!$OPENSSL_PUBLIC_DECRYPT)
	{
		while ($msg = openssl_error_string()) echo $msg . "<br />\n";
		die ('Failed decrypt');
	}

	$DECRYPTED = strtoupper(bin2hex($DECRYPTED_BIN));
	$DECRYPTED_HASH=str_replace($Prefix,'',$DECRYPTED);
	
	if ($DECRYPTED_HASH==$Data) {
		$RESULT="OK";
	} else {
		$RESULT="NOK";
	}
	
	return $RESULT;
}
