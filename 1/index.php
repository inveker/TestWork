<?php

preg_match('#^/files/[^\\/:*?"<>|+\s%!@]*?\.exe$#A', $_SERVER['REQUEST_URI'], $m);
if(!empty($m[0])) {
    $file = 'file.exe';

    if(!empty($_SERVER['HTTP_REFERER'])) {
        $pu = parse_url($_SERVER['HTTP_REFERER']);
        if(!empty($pu['host']))
            setcookie('referrer', $pu['host']);
    }

    if (file_exists($file)) {
        ob_get_level() && ob_get_clean();
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($m[0]).'"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
    }
}
