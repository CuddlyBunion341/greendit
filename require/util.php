<?php
    function linkHTML($url, $display) {
        return '<a href="'.$url.'">'.$display.'</a>';
    }
    function plural($word,$count) {
        if ($count == 1) return $word;
        return $word.'s';
    }
    function formatDate($date) {
        $datediff = time() - strtotime($date);
        if ($datediff > (60 * 60 * 24)) {
            $date = round($datediff / (60 * 60 * 24));
            return $date.' '.plural('day',$date).' ago';
        }
        if ($datediff > (60 * 60)) {
            $date = round($datediff / (60 * 60));
            return $date.' '.plural('hour',$date).' ago';
        }
        if ($datediff > 60) {
            $date = round($datediff / 60);
            return $date.' '.plural('minute',$date).' ago';
        }
        return 'moments ago';
    }
?>