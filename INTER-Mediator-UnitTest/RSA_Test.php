<?php
/**
 * Created by JetBrains PhpStorm.
 * User: msyk
 * Date: 12/03/26
 * Time: 13:27
 * To change this template use File | Settings | File Templates.
 */

if ((float)phpversion() >= 7.0) {
    require_once(dirname(__FILE__) . '/../lib/phpseclib_v2/Crypt/RSA.php');
    require_once(dirname(__FILE__) . '/../lib/phpseclib_v2/Math/BigInteger.php');
    require_once(dirname(__FILE__) . '/../lib/phpseclib_v2/Crypt/Random.php');
    if (!defined('CRYPT_RSA_PRIVATE_FORMAT_PKCS1')) {
        define('CRYPT_RSA_PRIVATE_FORMAT_PKCS1', constant('phpseclib\Crypt\RSA::PRIVATE_FORMAT_PKCS1'));
    }
    if (!defined('CRYPT_RSA_ENCRYPTION_PKCS1')) {
        define('CRYPT_RSA_ENCRYPTION_PKCS1', constant('phpseclib\Crypt\RSA::ENCRYPTION_PKCS1'));
    }
} else {
    require_once(dirname(__FILE__) . '/../lib/phpseclib_v1/Crypt/RSA.php');
    require_once(dirname(__FILE__) . '/../lib/phpseclib_v1/Math/BigInteger.php');    
}
require_once(dirname(__FILE__) . '/../lib/bi2php/biRSA.php');

class RSA_Test extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        mb_internal_encoding('UTF-8');
        date_default_timezone_set('Asia/Tokyo');
        $rsaClass = IMUtil::phpSecLibClass('phpseclib\Crypt\RSA');
        $this->rsa = new $rsaClass;
    }

    public function testGeneratedKey()
    {
        $publickey = null;
        $privatekey = null;
        $rsa = $this->rsa;
        extract($rsa->createKey(512)); /* 128, 256 didn't work, 512, 1024 work, 2048 didn't finish in 5 min. */
        $rsa->loadKey($publickey, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
//        echo "privatekey=",$privatekey,"\n";
//        echo "publickey=",$publickey,"\n";
        $str = "123";
        $enc = $rsa->encrypt($str);
//        echo "encoded=",$enc,"\n";
        $rsa->loadKey($privatekey, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $dec = $rsa->decrypt($enc);
        $this->assertEquals($str, $dec, "Basic Encrypt/Decrypt with Generated Key");
    }

    public function testSuppliedKey()
    {
        // $ openssl genrsa -out key.pem 512
        $generatedKey = <<<EOL
-----BEGIN RSA PRIVATE KEY-----
MIIBOwIBAAJBAKihibtt92M6A/z49CqNcWugBd3sPrW3HF8TtKANZd1EWQ/agZ65
H2/NdL8H6zCgmKpYFTqFGwlYrnWrsbD1UxcCAwEAAQJAWX5pl1Q0D7Axf6csBg1M
3V5u3qlLWqsUXo0ZtjuGDRgk5FsJOA9bkxfpJspbr2CFkodpBuBCBYpOTQhLUc2H
MQIhAN1stwI2BIiSBNbDx2YiW5IVTEh/gTEXxOCazRDNWPQJAiEAwvZvqIQLexer
TnKj7q+Zcv4G2XgbkhtaLH/ELiA/Fh8CIQDGIC3M86qwzP85cCrub5XCK/567GQc
GmmWk80j2KpciQIhAI/ybFa7x85Gl5EAS9F7jYy9ykjeyVyDHX0liK+V1355AiAG
jU6zr1wG9awuXj8j5x37eFXnfD/p92GpteyHuIDpog==
-----END RSA PRIVATE KEY-----
EOL;
        // $ openssl rsa -pubout -in key.pem
        $publickey = <<<EOL
-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAKihibtt92M6A/z49CqNcWugBd3sPrW3
HF8TtKANZd1EWQ/agZ65H2/NdL8H6zCgmKpYFTqFGwlYrnWrsbD1UxcCAwEAAQ==
-----END PUBLIC KEY-----
EOL;


        $rsa = $this->rsa;
        $rsa->loadKey($publickey);
        $str = "123";
        $enc = $rsa->encrypt($str);
//        echo "encoded=",bin2hex($enc),"\n";
        $rsa->loadKey($generatedKey);
        $dec = $rsa->decrypt($enc);
        $this->assertEquals($str, $dec, "Basic Encrypt/Decrypt with Supplied key.");
    }

    public function testSuppliedKeyAndData()
    {
        $str = "123";
        // generated by JavaScript RSA library on ondave.com.
        //   $data = "962e3b91a0b9815e0dca40a7595b5603211cee2992abf6054eae20b435d015ef3f54d4daf4cb4370db4c4c6f8432e49b9b81acfb6e1f5e7635bf72a74b29f272";

        // One of generated by openssl rsautl -encrypt -pubin -inkey pub.pem -in data -out enc
        $data = "1c4555cf53c88c1eedfc13db58ccf7f89c1a090fd159427658e5e5743e5c5e9129a716907efe4a76f25046598e92081e75d9217be4c56efd0df06e4507af4f04";
        $generatedKey = <<<EOL
-----BEGIN RSA PRIVATE KEY-----
MIIBOwIBAAJBAKihibtt92M6A/z49CqNcWugBd3sPrW3HF8TtKANZd1EWQ/agZ65
H2/NdL8H6zCgmKpYFTqFGwlYrnWrsbD1UxcCAwEAAQJAWX5pl1Q0D7Axf6csBg1M
3V5u3qlLWqsUXo0ZtjuGDRgk5FsJOA9bkxfpJspbr2CFkodpBuBCBYpOTQhLUc2H
MQIhAN1stwI2BIiSBNbDx2YiW5IVTEh/gTEXxOCazRDNWPQJAiEAwvZvqIQLexer
TnKj7q+Zcv4G2XgbkhtaLH/ELiA/Fh8CIQDGIC3M86qwzP85cCrub5XCK/567GQc
GmmWk80j2KpciQIhAI/ybFa7x85Gl5EAS9F7jYy9ykjeyVyDHX0liK+V1355AiAG
jU6zr1wG9awuXj8j5x37eFXnfD/p92GpteyHuIDpog==
-----END RSA PRIVATE KEY-----
EOL;
//        if ( strlen($data) %2 != 0 )    {
//            $data = '0' . $data;
//        }
//        $temp = '';
//        for ( $i = 0 ; $i < strlen($data) ; $i += 2 )   {
//            $temp .= substr( $data, strlen($data)-2-$i, 2 );
//        }
//        $data = $temp;

        $rsa = $this->rsa;
        $rsa->loadKey($generatedKey);
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $dec = $rsa->decrypt(pack("H*", $data));
        $this->assertEquals($str, $dec, "Decrypt with Supplied key.");
    }

    public function testBiRSA()
    {
        $generatedKey = <<<EOL
-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQEAxH7++yiHJUHEDU3wMw+FDfrlgOHNP+yiCFmYQPI0G7oj5uTy
tPMv3YVrEtb2Y62452C6WZcLSwOBqWlLUGfH0NJx35aaZG2CsUheNJEH+WEIFyel
mJmWDmwfZ6DnO+nsICylGWryDfgF7n4854mxa/SfI5bYAJD6x2D3o/NDwanlsbiU
B/ICKQmhZXvqNRRWdIEALasdLsDQ15MCfBTG1vKZqB9hiCFnZQEvrUKfWLdp6Uqa
j15QdEvTFopramsTkQHlOy/CnQDD7Qng8Qzqm7Ycq3Xz2R/nq5k/GeAnQdxKzW1j
QhktWfYrFQtxhyKcPXa/bchNkzctp3a/QRN2WwIDAQABAoIBAFvXoAZ0ovZfDuvJ
CgRTtLUcGDltUSoXyIRunCN/EawEDNPXHzpEkJLR0YI0x2U/xbUgGPnXB4hAU1KD
zJgAafzI4EDJe9CE/xkt4hpfz4JYQBfSiCwTXXfQQb2GD46Jf7xqIaEHw6uTyfH3
PzBZw3vaEqfn0X4yRYT7ZcRT58+UcAQJqQDU/6ZwNckewzhWzh/27LfstV+nJe5u
GSmRdb2H3x9ISKb+EMysM0n+YrNKC9giObCRm7EbIOE5iJvZnA0SiBP/y90anhsS
gHXaN4/cL5/U/ld9Nuk+MOH6R0qoVuGegDjdqHC+fCMUYbMvWKbnZiHU0/PnFfnu
SKxkmgECgYEA4kQDLLEq9ebk1nS402AWx0XNlJHmVR/PFUbVTOT1xMWFhZA4XMnX
KkMmeYIUMDJJcLbTBUYFeoygOM8TA5BSATxHGt9xrO2dl75HWVYP7Ncedc4iSj6P
dM4EsnFHKCgL2LqEuaQnMIDTZKo0WnlLphChJ3MzXwVzK5lXAiKHnaECgYEA3lF6
/o0mYjWTV0PDDYvUp6mtf2h5/V/wSDA7I4IJ9BpnsOIFCdShn8rDYN9qxJS0t3US
8kNNXgFrq2zjCDMibr1xALnyXnPlc86c2+kfgv+7Biu2foJ3MI4cL9umn/y76ENE
6VaO9bUs1HHKA0SFXNEr3ZFjm+kzx3qZZh1MensCgYBn99yFkrss1vXb3TJ4XjTZ
SCfY1tnBz6X2HuAwPxz3V9OstcJQUKa/0q9BMhZYtyKr2jZIvA4Ua73LnMsd3hjw
XGRH4th3H5BEg7iBQlx69bYXZ6q19t0wTOI3pHmP6CbZZYtLSjR/wxJftR3tXML4
AbgrSnIWfYiYRhOG9ZrfQQKBgFkxTWwUywJ5xhwrlnS31eBSRcYo71BFDkyX9RIA
2OdzNIiVlTnlcdZ+7bXOzLIDiyFTOf+yGrcNUNocvFUM1tKg9FY7Q867JqI4kVv1
Amx3FtyZ6wSEaTc0vIBC2m2zYtwDKQGIdaCESHEPGeIHuo2LadLhwpnJjLmKKUL7
nDRDAoGADIHzuzFTYYgG4heLd2yXGv5+MDX+NmVzTr1j5dVOfzpiBJBjCN8Q9x3v
v9nNeZFIhPbhCTjCdY/NlcIHOZqUQulTu1DpDZ7zFO1Fs4aEDnBvp9i8yJquxhOQ
dBazzmZ3S/t2b6NtqClmn/1BgjgnKYURBn888UzbX6lqCNG3/mI=
-----END RSA PRIVATE KEY-----
EOL;
        $rsa = $this->rsa;
        $rsa->loadKey($generatedKey);
        $privatekey = $rsa->getPrivateKey();
        $keyComp = $rsa->_parseKey($privatekey, CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $keyEncrypt = new biRSAKeyPair($keyComp['publicExponent']->toHex(), '0', $keyComp['modulus']->toHex());
        $keyDecrypt = new biRSAKeyPair('0', $keyComp['privateExponent']->toHex(), $keyComp['modulus']->toHex());
        $data = "happySAD200333#$#$#$#";
        $enc = $keyEncrypt->biEncryptedString($data);
        //var_dump($enc);
        $decrypted = $keyDecrypt->biDecryptedString($enc);
        $this->assertEquals($data, $decrypted, "Encrypt and decrypt with bi2RSA.");
    }

    public function testDecryptJSGenerated()
    {
        $generatedKey = <<<EOL
-----BEGIN RSA PRIVATE KEY-----
MIIEogIBAAKCAQEAxH7++yiHJUHEDU3wMw+FDfrlgOHNP+yiCFmYQPI0G7oj5uTy
tPMv3YVrEtb2Y62452C6WZcLSwOBqWlLUGfH0NJx35aaZG2CsUheNJEH+WEIFyel
mJmWDmwfZ6DnO+nsICylGWryDfgF7n4854mxa/SfI5bYAJD6x2D3o/NDwanlsbiU
B/ICKQmhZXvqNRRWdIEALasdLsDQ15MCfBTG1vKZqB9hiCFnZQEvrUKfWLdp6Uqa
j15QdEvTFopramsTkQHlOy/CnQDD7Qng8Qzqm7Ycq3Xz2R/nq5k/GeAnQdxKzW1j
QhktWfYrFQtxhyKcPXa/bchNkzctp3a/QRN2WwIDAQABAoIBAFvXoAZ0ovZfDuvJ
CgRTtLUcGDltUSoXyIRunCN/EawEDNPXHzpEkJLR0YI0x2U/xbUgGPnXB4hAU1KD
zJgAafzI4EDJe9CE/xkt4hpfz4JYQBfSiCwTXXfQQb2GD46Jf7xqIaEHw6uTyfH3
PzBZw3vaEqfn0X4yRYT7ZcRT58+UcAQJqQDU/6ZwNckewzhWzh/27LfstV+nJe5u
GSmRdb2H3x9ISKb+EMysM0n+YrNKC9giObCRm7EbIOE5iJvZnA0SiBP/y90anhsS
gHXaN4/cL5/U/ld9Nuk+MOH6R0qoVuGegDjdqHC+fCMUYbMvWKbnZiHU0/PnFfnu
SKxkmgECgYEA4kQDLLEq9ebk1nS402AWx0XNlJHmVR/PFUbVTOT1xMWFhZA4XMnX
KkMmeYIUMDJJcLbTBUYFeoygOM8TA5BSATxHGt9xrO2dl75HWVYP7Ncedc4iSj6P
dM4EsnFHKCgL2LqEuaQnMIDTZKo0WnlLphChJ3MzXwVzK5lXAiKHnaECgYEA3lF6
/o0mYjWTV0PDDYvUp6mtf2h5/V/wSDA7I4IJ9BpnsOIFCdShn8rDYN9qxJS0t3US
8kNNXgFrq2zjCDMibr1xALnyXnPlc86c2+kfgv+7Biu2foJ3MI4cL9umn/y76ENE
6VaO9bUs1HHKA0SFXNEr3ZFjm+kzx3qZZh1MensCgYBn99yFkrss1vXb3TJ4XjTZ
SCfY1tnBz6X2HuAwPxz3V9OstcJQUKa/0q9BMhZYtyKr2jZIvA4Ua73LnMsd3hjw
XGRH4th3H5BEg7iBQlx69bYXZ6q19t0wTOI3pHmP6CbZZYtLSjR/wxJftR3tXML4
AbgrSnIWfYiYRhOG9ZrfQQKBgFkxTWwUywJ5xhwrlnS31eBSRcYo71BFDkyX9RIA
2OdzNIiVlTnlcdZ+7bXOzLIDiyFTOf+yGrcNUNocvFUM1tKg9FY7Q867JqI4kVv1
Amx3FtyZ6wSEaTc0vIBC2m2zYtwDKQGIdaCESHEPGeIHuo2LadLhwpnJjLmKKUL7
nDRDAoGADIHzuzFTYYgG4heLd2yXGv5+MDX+NmVzTr1j5dVOfzpiBJBjCN8Q9x3v
v9nNeZFIhPbhCTjCdY/NlcIHOZqUQulTu1DpDZ7zFO1Fs4aEDnBvp9i8yJquxhOQ
dBazzmZ3S/t2b6NtqClmn/1BgjgnKYURBn888UzbX6lqCNG3/mI=
-----END RSA PRIVATE KEY-----
EOL;
        $enc =
            '8c87f3e5ef1021a764e80b92b3cf168130b8cb5c5b72016449bfb812da1718cc' .
            'ea125dec512a9c91bfc336f35ea1804aafb2ef6b55c715a2fca2c90491d270bd' .
            '9a857bee7734bfef3252afac67cb3a6c8dcc9168164a44a9c8f31001289077ef' .
            '3e493d4581cdb94c7812140d1ebca802636cf16cdc5fe48128f758094ebe64fe' .
            '4b7fb1fb814c8502e1c52fcd9cbc3431a7fc8f3f8dda146eef15b4d14192f444' .
            '6b9cff5bd8c3f2c8ba90b00ab93263182ad3ed7ad0d460cc02529826c6048091' .
            '1c712d6e212ced1a7f5fc18a1574fdceb101f28d13cd106e8d04a24de9ab3570' .
            '77fee33e168b584a1cbf6ea27de9e88a89e1616b18897cd7288d2a02c62434a7';
        $rsa = $this->rsa;
        $rsa->loadKey($generatedKey);
        $keyComp = $rsa->_parseKey($rsa->getPrivateKey(), CRYPT_RSA_PRIVATE_FORMAT_PKCS1);
        $keyDecrypt = new biRSAKeyPair('0', $keyComp['privateExponent']->toHex(), $keyComp['modulus']->toHex());
        $decrypted = $keyDecrypt->biDecryptedString($enc);
        $this->assertEquals("1234OhmyGOD#", $decrypted, "Decrypt from JavaScript encripted date.");
    }
}
