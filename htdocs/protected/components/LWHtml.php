<?php

class LWHtml extends CHtml {

	const EMPTY_IMAGE = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

	static function EmptyImage($htmlOptions) {
		return self::image(EMPTY_IMAGE, '', $htmlOptions);
	}

	static function ordinal($n) {
		$n_last = $n % 100;
		if (($n_last > 10 && $n_last < 14) || $n == 0){
			return "{$n}th";
		}
		switch(substr($n, -1)) {
			case '1':    return "{$n}st";
			case '2':    return "{$n}nd";
			case '3':    return "{$n}rd";
			default:     return "{$n}th";
		}
	}

	static function clean($value, $echo = true) {
		$clean = strip_tags($value);
		if ($echo) { echo $clean; }
		else return $clean;
	}

	static function rand_string( $length ) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		$str = '';
		$size = strlen( $chars );
		for( $i = 0; $i < $length; $i++ ) {
			$str .= $chars[ rand( 0, $size - 1 ) ];
		}

		return $str;
	}

	static function money($number)
	{
		return '$ ' . number_format($number, 2);
	}

	/**
	 * Render an address element for an address model.
	 * @param UserAddress $addressModel An address model, or any class supporting getters for similar attributes.
	 * @return string
	 */
	static function address($addressModel) {
		$address = '';
		$address .= $addressModel->user->FullName . '<br />';
		$address .= $addressModel->street1 . '<br />';
		if(isset($addressModel->street2) && !empty($addressModel->street2)) {
			$address .= $addressModel->street2 . '<br />';
		}
		$address .= $addressModel->city . ', ' . $addressModel->state->abbreviation . ' ' . $addressModel->zip;
		return CHtml::tag('address', array(), $address);
	}
}