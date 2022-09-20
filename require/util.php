<?php
function linkHTML($url, $display) {
    return '<a href="' . $url . '">' . $display . '</a>';
}

function plural($word, $count) {
    if ($count == 1) return $word;
    return $word . 's';
}

function formatDate($date) {
    $datediff = time() - strtotime($date);
    if ($datediff > (60 * 60 * 24)) {
        $date = round($datediff / (60 * 60 * 24));
        return $date . ' ' . plural('day', $date) . ' ago';
    }
    if ($datediff > (60 * 60)) {
        $date = round($datediff / (60 * 60));
        return $date . ' ' . plural('hour', $date) . ' ago';
    }
    if ($datediff > 60) {
        $date = round($datediff / 60);
        return $date . ' ' . plural('minute', $date) . ' ago';
    }
    return 'moments ago';
}

function replace(&$subject, $pattern, $replacement) {
    $subject = preg_replace($pattern, $replacement, $subject);
}

function active($bool, $class = 'active', $whitespace = true) {
    if ($bool) {
        return $whitespace ? ' ' . $class : $class;
    }
}

function icon($name, $class = '') {
    return '<svg class="icon icon-' . $name . ' ' . $class . '"><use href="#icon-' . $name . '"></use></svg>';
}

function markdownify($text) {
    replace($text, '/\*\*(.*?)\*\*/', '<b>$1</b>'); // bold
    replace($text, '/\*(.*?)\*/', '<i>$1</i>'); // italic
    replace($text, '/`(.*?)`/s', '<pre>$1</pre>'); // mono
    replace($text, '/==(.*?)==/', '<mark>$1</mark>'); // highlight
    replace($text, '/\[\]\((.*?)\)/', '<a href="$1">$1</a>'); // link
    replace($text, '/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>'); // link
    replace($text, '/\n/', '<br>'); // newline
    return $text;
}