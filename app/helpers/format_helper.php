<?php
/**
Helper format
https://jagowebdev.com
*/

function format_ribuan($value) {
	return number_format($value, 0, ',' , '.');
}