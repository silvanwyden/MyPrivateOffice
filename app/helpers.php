<?php


	function createOrderLink($class, $order, $dir_request)
    {
    	
    	$dir = 'DESC';
    	if ($class == $order && $dir_request == 'DESC')
    		$dir = 'ASC';
    	
    	return "?order=" . $class . "&dir=" . $dir;
    	
    }
    
    function getColorDate($date) {
    	
    	if ($date == '0000-00-00')
    		return '';
    	
    	$datetime1 = new DateTime($date);
    	$datetime2 = new DateTime('now');
    	$difference = $datetime2->diff($datetime1);
    	$difference = $difference->format('%r%a');
   	
    	$color = '';
    	
    	if ($difference <= 7)
    		$color = "btn-info";
    	if ($difference <= 3)
    		$color = 'btn-warning';
    	if ($difference < 0)
    		$color = 'btn-danger';
    	if ($difference === '-0')
    		$color = 'btn-danger';
    	
    	return " btn " . $color;
    	
    }
    
    
?>