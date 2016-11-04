<?php

$dir = '/mnt/data/down/mp3/';
switch ($_REQUEST['act']) {
    //播放选择
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
    //播放目录
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
    //随机播放目录
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
    //停止
    case 'stop':
        stop();
        echo json_encode(array('status' => 1));
        break;
    //播放单曲
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
    //设置,读取音量
    case 'amixer':
        if (isset($_GET['yl'])) {
            $yl = (int) $_GET['yl'];
            if ($yl > 0 && $yl <= 100) {
                $sh = "amixer set Master $yl% on 2>/dev/null";
//            echo $sh;
                $ok = exec($sh);
            } else {
                $sh = "amixer set Master 0% off 2>/dev/null";
//            echo $sh;
                $ok = exec($sh);
            }
            if ($ok) {
                echo json_encode(array('status' => 1));
            } else {
                echo json_encode(array('status' => 0, 'info' => '设置失败，请重试'));
            }
        } else {
            $sh = "amixer get Master |grep '%'|awk 'BEGIN{FS=\"[\\[\\%]\"}{print $2}' 2>/dev/null";
//            echo $sh;
            $ok = exec($sh);
            echo json_encode(array('status' => 1, 'yl' => $ok));
        }
        break;
    //取播放状态
    case 'playing':
        $sh = "pgrep mplayer 2>/dev/null";
//            echo $sh;
        $ok = exec($sh);
        if ($ok) {
            echo json_encode(array('status' => 1));
        } else {
            echo json_encode(array('status' => 0, 'info' => '没在播放'));
        }
        break;
    //取目录
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
