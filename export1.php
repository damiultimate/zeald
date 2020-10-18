<?php
/**
 * Refactor this code to be the tidiest, 'best practice' design you can come up with
 * The point of this exercise is not to find minor bugs in the code, but to focus on the architecture of 
 * this piece of software and ensure it is very well designed - easy to maintain, extend, refactor over 
 * time as required.
 * 
 * This code uses a standalone implementation of Laravel Collections which provides the global 'collect'
 * method and various methods which operate on the resulting Collection object. 
 * https://laravel.com/docs/5.8/collections
 */

 use Illuminate\Support;

// prepare the request & process the arguments
$database = 'nba2019';
include('include/utils.php');
require_once('vendor/autoload.php');
require_once('classes/Controller1.php');
require_once('classes/FormatHelper.php');

// process the args
$args = collect($_REQUEST);
$format = $args->pull('format') ?: 'html';
$type = $args->pull('type');
if (!$type) {
    exit('Please specify a type');
}

//Here, the controller class is instanciated and first, the setArgs method is called. The setArgs was put there to avoid hardcoding the $args variable into the method call and allow flexibility. Also, anybody can create any helper class they want to, so far as it meets the requirements. In this example, the FormatHelper class is used.

$controller = new Controller();

$controller->setArgs($args);

$controller->setHelper(new FormatHelper());

echo $controller->export($type, $format);