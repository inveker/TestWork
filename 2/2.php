<?php

function search($arr) {
    return array_search(2,array_count_values($arr));
}

//function test() {
//    $arr = [];
//    $len = rand(2,50);
////    $len = 100;
//    $tmp = [];
//    for($i = 0; $i < $len+1; $i++) {
//        $v = rand(0,1000000);
//        if(!isset($tmp[$v]))
//            $arr[] = $tmp[$v] = $v;
//    }
//    $dub = rand(0,$len);
//    do {
//        $rep = rand(0,$len);
////        $rep = $len;
//    } while ($dub == $rep);
//    $arr[$rep] = $arr[$dub];
//
//    $time = 0;
//    for ($i = 0; $i < 100000; $i++) {
//        $t = microtime(true);
//        $a = search($arr);
//        $time += microtime(true) - $t;
//        if($a != $arr[$dub])
//            exit('Fail');
//    }
//    var_dump($time);
//}
//test();

