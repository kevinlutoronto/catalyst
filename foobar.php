<?php
	// A variable containing the output.
	$output = "";
	
	// Four scenarios for output.
	// 1. "foo" if $i is divisible by 3.
	// 2. "bar" if $i is divisible by 5.
	// 3. "foobar" if $i is divisible by 15.
	// 4. Otherwise, $i itself.
	for($i = 1; $i < 101; $i++){
		if($i % 3 == 0){
			$output .= "foo, ";
		}else if ($i % 5 == 0){
			$output .= "bar, ";
		}else if($i % 15 == 0){
			$output .= "foobar, ";
		}else{
			$output .= "$i, ";
		}
	}
	
	
	// Eliminate the ", " in the end of the output.
	$output = substr($output, 0, -2);
	
	// Output the result.
	print $output;
	
?>
