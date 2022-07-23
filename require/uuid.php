<?php
    function gen_uuid($len=8) {
        $hex = md5("someFancySalt" . uniqid("", true));
        $pack = pack('H*', $hex);
        $tmp =  base64_encode($pack);
        $uid = preg_replace("#(*UTF8)[^A-Za-z0-9]#", "", $tmp);
        $len = max(4, min(128, $len));
        while (strlen($uid) < $len)
            $uid .= gen_uuid(22);
        return substr($uid, 0, $len);
    }

    function random_string($len=8) {
        $random = '';
        for ($i = 0; $i < $len; $i++) {
            $random .= chr(rand(ord('a'), ord('z')));
        }
        return $random;
    }
?>