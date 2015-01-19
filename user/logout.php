<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
unset($_SESSION['user']);
session_destroy();
redirect();
?>
