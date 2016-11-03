<?php

$dir = '/mnt/data/down/mp3/';
switch ($_REQUEST['act']) {
    case 'palysel':
        stop();
        if ($_POST['dir'] != '/') {
            $dir .= $_POST['dir'];
        }
//        var_export($_POST);        exit;
        $mp3 = (array) json_decode($_POST['mp3'], 1);
        if (!$mp3) {
            exit(json_encode(array('status' => 0, 'info' => '您没有选择文件')));
        }
        $sh = "nohup sudo mplayer ";
        foreach ($mp3 as $key => $value) {
            $sh .= "'" . $dir . $value . "' ";
        }
        $sh .= " 2>/dev/null &";
//        echo $sh;
        exec($sh);
        echo json_encode(array('status' => 1));
        break;
    case 'palyall':
        stop();
        if ($_GET['dir'] != '/') {
            $dir .= $_GET['dir'];
        }
        $sh = "nohup sudo mplayer $dir* 2>/dev/null &";
//        echo $sh;
        $ok = exec($sh);
//        var_export($ok);
        echo json_encode(array('status' => 1));
        break;
    case 'palyalls':
        stop();
        if ($_GET['dir'] != '/') {
            $dir .= $_GET['dir'];
        }
        $sh = "nohup sudo mplayer -shuffle $dir* 2>/dev/null &";
//        echo $sh;
        exec($sh);
        echo json_encode(array('status' => 1));
        break;
    case 'stop':
        stop();
        echo json_encode(array('status' => 1));
        break;
    case 'play':
        stop();
        $mp3 = $_GET['mp3'];
        if ($_GET['dir'] != '/') {
            $dir .= $_GET['dir'];
        }
        $sh = "nohup sudo mplayer '$dir$mp3' 2>/dev/null &";
//        echo $sh;
        exec($sh);
        echo json_encode(array('status' => 1));
        break;

    default:
        if ($_GET['dir']) {
            $dir .= $_GET['dir'] . '/';
        }
        $folder = opendir($dir);
        $i = 0;
        while ($f = readdir($folder)) {
            if ($f <> "." && $f <> ".." && $f <> ".htaccess") {
                $f = $f;
                if (is_dir($dir . $f)) {
                    $r['dir'][] = $f;
//                    $fo = opendir($dir . $f);
//                    while ($ff = readdir($fo)) {
//                        if ($ff <> "." && $ff <> ".." && $ff <> ".htaccess") {
//                            $ff = $f . '/' . $ff;
//                            $r[$i] = $ff;
//                            $i++;
//                        }
//                    }
                } else {
                    $r['file'][$i] = $f;
                    $i++;
                }
            }
        }
        sort($r['dir']);
        sort($r['file']);
        //var_export($r);
        echo json_encode($r);
        break;
}

function stop() {
    $sh = "nohup sudo pkill mplayer >/dev/null &";
    exec($sh);
}
