<?php
namespace Agrandesr;

class GlobalData {

    static function set(string $key, mixed $data) : void {
        if(!isset($GLOBALS['X-AGRANDESR-DATA'])) $GLOBALS['X-AGRANDESR-DATA']=[];
        $GLOBALS['X-AGRANDESR-DATA'][$key]=$data;
    }

    static function get(string $key) : mixed {
        return $GLOBALS['X-AGRANDESR-DATA'][$key];
    }

    static function getSafe(string $key) : mixed {
        return $GLOBALS['X-AGRANDESR-DATA'][$key] ?? null;
    }
}