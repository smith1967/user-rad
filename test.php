<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author IT
 */
class test {
    //put your code here
    function __construct() {
        echo 'Before<br />';
    }
    function __destruct() {
        print 'After<br />';
    }
            function hello(){
        echo 'Hello world<br />';
    }
}

$o = new test();
$o->hello();
?>
