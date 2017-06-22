<?php

// 应用公共文件
function getRandTime(){
    $time = time();
    $date = @date('Y-m-d');
    switch($time){
        case $time< @strtotime($date." 8:00:00") && $time > @strtotime($date." 00:00:00"):
            $t = rand(8000,8999);
            break;
        case $time < @strtotime($date." 10:00:00") && $time > @strtotime($date." 8:00:00"):
            $t = rand(1000,1999);
            break;
        case $time < @strtotime($date." 12:00:00") && $time > @strtotime($date." 10:00:00"):
            $t = rand(2000,2999);
            break;
        case $time < @strtotime($date." 14:00:00") && $time > @strtotime($date." 12:00:00"):
            $t = rand(3000,3999);
            break;
        case $time < @strtotime($date." 18:00:00") && $time > @strtotime($date." 14:00:00"):
            $t = rand(4000,4999);
            break;
        case $time < @strtotime($date." 20:00:00") && $time > @strtotime($date." 18:00:00"):
            $t = rand(5000,5999);
            break;
        case $time < @strtotime($date." 22:00:00") && $time > @strtotime($date." 20:00:00"):
            $t = rand(6000,6999);
            break;
        case $time < @strtotime($date." 24:00:00") && $time > @strtotime($date." 22:00:00"):
            $t = rand(7000,7999);
            break;
        default :
            $t = rand(500,999);
    }
    return $t;
}
