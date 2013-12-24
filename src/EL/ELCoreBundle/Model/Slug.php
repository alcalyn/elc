<?php

namespace EL\ELCoreBundle\Model;


class Slug
{
	
	/**
	 * Slug function.
	 * If returns always n-a-xxxxxxxx,
	 * the parameter use maybe not utf8 encoding,
	 * then preg_replace fail and returns null.
	 * 
	 * 
	 * @param string $s to slugify
	 * @param string $prefix to prepend after slugification
	 * @param string $sufix to append after slugification
	 */
    public static function slug($s, $prefix = '', $sufix = '')
    {
    	$r = $s;
    	
        // replace non letter or digits by -
		$r = preg_replace('~[^\\pL\d]+~u', '-', $r);
		
		// trim
		$r = trim($r, '-');
		
		// transliterate
		setlocale(LC_CTYPE, 'en_GB');
		$r = iconv('utf-8', 'ASCII//TRANSLIT', $r);
		
		// lowercase
		$r = strtolower($r);
		
		// remove unwanted characters
		$r = preg_replace('~[^-\w]+~', '', $r);
		
		if (empty($r)) {
			$r = 'n-a-'.substr(uniqid('', true), -8);
		}
        
        // return
        return $prefix.$r.$sufix;
    }
    
    
    
}
