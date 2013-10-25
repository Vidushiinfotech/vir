<?php
/**
 * Commmon Admin functions
 *
 */

//Include global functions
require_once EZ_BASE_PATH . 'includes/global-functions.php';

/**
 * Display the html of header
 * @param string $page_title Title of the page
 * @param string $header_name Pass the file name, leave blank for default header
 */
function admin_header($page_title = '', $header_name = 'admin-header') {

    $file_path = EZ_ADMIN_PATH . 'view/' . $header_name . '.php';
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        die('header file error!');
    }

}

/**
 * Display the html of admin footer
 * @param string $footer_name Footer file name
 */
function admin_footer( $footer_name = 'admin-footer' ) {

    $file_path = EZ_ADMIN_PATH . 'view/' . $footer_name . '.php';
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        die('footer file error!');
    }
}

/**
 * Get the templates name
 * @return array Templates slugs
 */
function get_admin_templates_names() {
    return array(
        'import-models',
        'download-emails',
        'calc-control',
        'parts',
        'cms'
    );
}

/**
 * Load the template into panel file
 */
function load_admin_template() {

    if (isset($_GET['page']) && !empty($_GET['page'])) {
        if ( in_array($_GET['page'], get_admin_templates_names()) ) {
            $file_path = EZ_ADMIN_PATH . 'view/' . $_GET['page'] . '.php';
            if (file_exists($file_path)) {
                include_once $file_path;
            } else {
                echo 'File not found.';
            }
        } else {
            echo 'Invalid Request!';
        }
    } else {
        include_once EZ_ADMIN_PATH . 'view/welcome.php';
    }
}

/**
 * Sanitize title
 */
function sanitize_title_with_dashes( $title, $raw_title = '', $context = 'display' ) {

	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

	if (seems_utf8($title)) {
		if (function_exists('mb_strtolower')) {
			$title = mb_strtolower($title, 'UTF-8');
		}
		$title = utf8_uri_encode($title, 200);
	}

	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = str_replace('.', '-', $title);

	if ( 'save' == $context ) {
		// Convert nbsp, ndash and mdash to hyphens
		$title = str_replace( array( '%c2%a0', '%e2%80%93', '%e2%80%94' ), '-', $title );

		// Strip these characters entirely
		$title = str_replace( array(
			// iexcl and iquest
			'%c2%a1', '%c2%bf',
			// angle quotes
			'%c2%ab', '%c2%bb', '%e2%80%b9', '%e2%80%ba',
			// curly quotes
			'%e2%80%98', '%e2%80%99', '%e2%80%9c', '%e2%80%9d',
			'%e2%80%9a', '%e2%80%9b', '%e2%80%9e', '%e2%80%9f',
			// copy, reg, deg, hellip and trade
			'%c2%a9', '%c2%ae', '%c2%b0', '%e2%80%a6', '%e2%84%a2',
			// grave accent, acute accent, macron, caron
			'%cc%80', '%cc%81', '%cc%84', '%cc%8c',
		), '', $title );

		// Convert times to x
		$title = str_replace( '%c3%97', 'x', $title );
	}

	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');

	return $title;
}

/**
 * Checks to see if a string is utf8 encoded.
 *
 * NOTE: This function checks for 5-Byte sequences, UTF8
 *       has Bytes Sequences with a maximum length of 4.
 */
function seems_utf8($str) {
	$length = strlen($str);
	for ($i=0; $i < $length; $i++) {
		$c = ord($str[$i]);
		if ($c < 0x80) $n = 0; # 0bbbbbbb
		elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
		elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
		elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
		elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
		elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
		else return false; # Does not match any model
		for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
			if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
				return false;
		}
	}
	return true;
}

/**
 * Encode the Unicode values to be used in the URI.
 */
function utf8_uri_encode( $utf8_string, $length = 0 ) {
	$unicode = '';
	$values = array();
	$num_octets = 1;
	$unicode_length = 0;

	$string_length = strlen( $utf8_string );
	for ($i = 0; $i < $string_length; $i++ ) {

		$value = ord( $utf8_string[ $i ] );

		if ( $value < 128 ) {
			if ( $length && ( $unicode_length >= $length ) )
				break;
			$unicode .= chr($value);
			$unicode_length++;
		} else {
			if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

			$values[] = $value;

			if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
				break;
			if ( count( $values ) == $num_octets ) {
				if ($num_octets == 3) {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
					$unicode_length += 9;
				} else {
					$unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
					$unicode_length += 6;
				}

				$values = array();
				$num_octets = 1;
			}
		}
	}

	return $unicode;
}

/**
 * Db function
 * @param type $data
 * @param type $stop
 */
function db ( $data, $stop=0 ){
    
    echo '<pre>';
    print_r( $data );
    echo '</pre>';
    
    if( $stop )
        die;
}
