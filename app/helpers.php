<?php

use App\Tag;

	function createOrderLink($class, $order, $dir_request, $page)
    {
    	
    	$dir = 'ASC';
    	if ($class == $order && $dir_request == 'ASC')
    		$dir = 'DESC';
    	
    	return "?order=" . $class . "&dir=" . $dir . "&page=" . $page;
    	
    }
    
    function createOrderLinkImage($class, $order, $dir_request)
    {
    	if ($class != $order)
    		return "";
    	
    	if ($dir_request == 'ASC')
    		return "glyphicon glyphicon-sort-by-alphabet";
    	elseif ($dir_request == "DESC")
    		return "glyphicon glyphicon-sort-by-alphabet-alt";
    	else 
    		return "";
    
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
    
    function getColorBirthdate($date) {

    	$date = date('Y', time()) . "-" . $date;
    	
    	$datetime1 = new DateTime($date);
    	$datetime2 = new DateTime('now');
    	$difference = $datetime2->diff($datetime1);
    	$difference = $difference->format('%r%a');
    
    	$color = '';
    	 
    	if ($difference <= 30)
    		$color = "btn-info";
    	if ($difference <= 6)
    		$color = 'btn-warning';
    	if ($difference === '-0')
    		$color = 'btn-danger';
    	if ($difference < 0)
    		$color = '';
    	 
    	return " btn " . $color;
    	 
    }
    
    function getTags($tag_ids) {

    	$tags_sel = Tag::find(explode(",", $tag_ids));
    	return $tags_sel;
    	
    }
    
    
?>