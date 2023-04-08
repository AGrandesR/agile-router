<?php

namespace Agrandesr\examples;



class SecurityClass {
    function checkToken($token='', $extradata=[]) {
        if($token=='token') return true;
        else return false;
    }

    function createToken() {

    }
}