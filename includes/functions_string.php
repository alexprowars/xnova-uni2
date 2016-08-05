<?php

function colorNumber($n, $s = '')
{
	if ($n > 0) {
		if ($s != '') {
			$s = colorGreen($s);
		} else {
			$s = colorGreen($n);
		}
	} elseif ($n < 0) {
		if ($s != '') {
			$s = colorRed($s);
		} else {
			$s = colorRed($n);
		}
	} else {
		if ($s == '') {
			$s = $n;
		}
	}
	return $s;
}

function colorRed($n)
{
	return '<font color="#ff0000">' . $n . '</font>';
}

function colorGreen($n)
{
	return '<font color="#00ff00">' . $n . '</font>';
}

function pretty_number($n, $floor = true)
{
	if ($floor) {
		$n = floor($n);
	}
	return number_format($n, 0, ",", ".");
}

function pretty_time ($seconds, $separator = '')
{
	$day    = floor($seconds / (24 * 3600));
	$hs     = floor($seconds / 3600 % 24);
	$ms     = floor($seconds / 60 % 60);
	$sr     = floor($seconds / 1 % 60);

	if ($hs < 10) { $hh = "0" . $hs; } else { $hh = $hs; }
	if ($ms < 10) { $mm = "0" . $ms; } else { $mm = $ms; }
	if ($sr < 10) { $ss = "0" . $sr; } else { $ss = $sr; }

	$time = '';
	if ($day != 0) { $time .= $day . 	(($separator != '') ? $separator : 'д '); }
	if ($hs  != 0) { $time .= $hh . 	(($separator != '') ? $separator : 'ч '); }
	if ($ms  != 0) { $time .= $mm . 	(($separator != '') ? $separator : 'м '); }
	$time .= $ss . (($separator != '') ? '' : 'с');

	return $time;
}

function pretty_time_hour ($seconds)
{
	$min = floor($seconds / 60 % 60);

	$time = '';
	if ($min != 0) { $time .= $min . 'min '; }

	return $time;
}
 
?>