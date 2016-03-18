<?php

namespace Hayate;

/**
 * 自定义数组辅助函数
 */
class ArrayHelpers {

	/**
	 * 性能更高的数组去重
	 * 解决 array_unique 使用快排算法处理大数组时的性能损耗问题
	 * @author gjy
	 *
	 * @param  array $arrData
	 * @return array
	 */
	public static function unique( $arrData = [] )
	{
		/**
		 * 去null后两次翻转
		 * merge用来修复数组index
		 */
		return array_merge( array_flip( array_flip( array_filter( $arrData ) ) ) );
	}

	/**
	 * 数据排序, 根据参数执行升序或降序
	 * 依赖 _quick_sort_desc函数
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  boolean $asc
	 * @return array
	 */
	public static function quick_sort($array = [], $key = '', $asc = FALSE)
	{
		return $asc ? array_reverse( self::_quick_sort_desc( $array, $key ) ) : self::_quick_sort_desc( $array, $key );
	}

	/**
	 * 根据键值降序排序
	 *
	 * @param  array $array
	 * @param  string $key
	 * @return array
	 */
	private static function _quick_sort_desc( $array = [], $key = '' )
	{
		if ( empty( $array ) || count( $array ) <= 1 )
		{
			return $array;
		}

		$end = count($array) - 1;
		$middle = intval($end / 2) + 1;

		$r1 = self::_quick_sort_desc(array_slice($array, 0, $middle), $key);
		$r2 = self::_quick_sort_desc(array_slice($array, $middle, $end - $middle + 1), $key);

		$r = array();
		$idx1 = $idx2 = 0;
		$len1 = count($r1);
		$len2 = count($r2);

		while (true) {
			if ($idx1 >= $len1) {
				break;
			}

			if ($idx2 >= $len2) {
				break;
			}

			if ($r1[$idx1][$key] >= $r2[$idx2][$key]) {
				array_push($r, $r1[$idx1]);
				$idx1++;
			} else {
				array_push($r, $r2[$idx2]);
				$idx2++;
			}
		}

		if ($idx1 < $len1) {
			$r = array_merge($r, array_slice($r1, $idx1, count($r1) - $idx1));
		}

		if ($idx2 < $len2) {
			$r = array_merge($r, array_slice($r2, $idx2, count($r2) - $idx2));
		}

		return $r;
	}

	/**
	 * array_map扩展, 实现多维数组处理
	 * @author gjy
	 *
	 * @param  string $filter # 处理数组值的函数名称, 如有自定义的函数务必写在 class{} 外面 !
	 * @param  array $data
	 * @return array
	 */
	public static function array_map_recursive( $filter, $data )
	{
		$result = array();
		foreach ( $data as $key => $val ) {
			$result[$key] = is_array($val) ? self::array_map_recursive($filter, $val) : $filter($val);
		}
		return $result;
	}

}
