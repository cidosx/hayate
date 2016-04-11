<?php

namespace Hayate;

class StringHelpers {

	/**
	 * 返回传入的字母附近第n个字母
	 * @author gjy
	 *
	 * @param  string $letter
	 * @param  integer $num
	 * @return string
	 */
	public static function neighbor_letter($letter = '', $num = 1) {
		$num = (int) $num;
		if ($num === 0) {
			return $letter;
		}

		$ascllDec = ord($letter) + $num;
		if ($ascllDec < 65
			|| ($ascllDec > 90 && $ascllDec < 97)
			|| $ascllDec > 122) {
			return '';
		}

		return chr($ascllDec);
	}

	/**
	 * 全角字符转半角
	 * @author anonymous
	 *
	 * @param  string $str
	 * @param  string $coding
	 * @return string
	 */
	public static function str_full2half($str = '', $coding = 'UTF-8') {
		if (empty($str)) {
			return '';
		}

		if ($coding !== 'UTF-8') {
			$str = mb_convert_encoding($str, 'UTF-8', $coding);
		}

		$ret = '';

		for ($i = 0; $i < strlen($str); ++$i) {
			$s1 = $str[$i];
			if (($c = ord($s1)) & 0x80) {
				$s2 = $str[++$i];
				$s3 = $str[++$i];
				$c = (($c & 0xF) << 12) | ((ord($s2) & 0x3F) << 6) | (ord($s3) & 0x3F);
				if ($c == 12288) {
					$ret .= ' ';
				} elseif ($c > 65280 && $c < 65375 && $c != 65374) {
					$c -= 65248;
					$ret .= chr($c);
				} else {
					$ret .= $s1 . $s2 . $s3;
				}
			} else {
				$ret .= $str[$i];
			}
		}

		if ($coding === 'UTF-8') {
			return $ret;
		}

		return mb_convert_encoding($ret, $coding, 'UTF-8');
	}

	/**
	 * 去除制定的html标签
	 * @author gjy
	 *
	 * @param  string $str
	 * @param  string $type
	 * @return string
	 */
	public static function strip_html_tags($str = '', $type = 'script') {
		if (empty($str)) {
			return '';
		}

		$stripType = array(
			'all' => '@<[\/\!]*?[^<>]*?>@si', // 所有标签, 等同于 strip_tags()
			'script' => '@<script[^>]*?>.*?</script>@si', // 去除js
			'style' => '@<style[^>]*?>.*?</style>@siU', // 去除css
			'comments' => '@<![\s\S]*?--[ \t\n\r]*>@', // 去除注释
			'html' => '@<html[^>]*?>.*?</html>@si', // 去除html标签
			'body' => '@<body[^>]*?>.*?</body>@si', // 去除body标签
		);

		if (empty($stripType[$type])) {
			return $str;
		}

		return preg_replace($stripType[$type], '', $str);
	}

	/**
	 * 生成随机字符串
	 * @author CodeIgniter developer
	 * @revisor gjy
	 *
	 * @param  string $type
	 * @param  integer $len
	 * @return string
	 */
	public static function random_string($type = 'distinct_num', $len = 8) {
		switch ($type) {
		case 'numeric':
			return str_pad(mt_rand(0, str_repeat(9, $len)), $len, '0', STR_PAD_LEFT);
		case 'alnum':
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'nozero':
			$pool = '123456789';
			break;
		case 'alpha':
			$pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			break;
		case 'distinct':
			$pool = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
			break;
		case 'distinct_num':
			$pool = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY';
			break;
		case 'md5':
			return md5(uniqid(mt_rand()));
		case 'sha1':
			return sha1(uniqid(mt_rand(), TRUE));
		}

		return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
	}
}
