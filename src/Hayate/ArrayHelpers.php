<?php

namespace Hayate;

/**
 * 自定义数组辅助函数
 */
class ArrayHelpers
{

    /**
     * 性能更高的数组去重
     * 解决 array_unique 使用快排算法处理大数组时的性能损耗问题
     * @author gjy
     *
     * @param  array $arrData
     * @return array
     */
    public static function unique(array $arrData = array(), $filtered = false)
    {
        if (!$filtered) {
            $arrData = array_filter($arrData);
        }

        return array_keys(array_flip($arrData));
    }

    /**
     * 数据排序, 根据参数执行升序或降序
     * 只适用于自然序列的数组
     *
     * 依赖 _quick_sort_desc函数
     *
     * @param  array $array
     * @param  string $key
     * @param  boolean $asc
     * @return array
     */
    public static function quick_sort(array $array = array(), $key = '', $asc = false)
    {
        return $asc ? array_reverse(self::_quick_sort_desc($array, $key)) : self::_quick_sort_desc($array, $key);
    }

    /**
     * 根据键值降序排序
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    private static function _quick_sort_desc(array $array = array(), $key = '')
    {
        if (empty($array) || count($array) <= 1) {
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
    public static function array_map_recursive($filter, $data)
    {
        $return = array();
        foreach ($data as $key => $val) {
            $return[$key] = is_array($val) ? self::array_map_recursive($filter, $val) : $filter($val);
        }
        return $return;
    }

    /**
     * 随机取数组中一个值
     * @author gjy
     *
     * @param  array $array
     * @return array | mixed
     */
    public static function random_value(array $array)
    {
        return is_array($array) ? $array[array_rand($array)] : $array;
    }

    /**
     * fetch some item
     * @author gjy
     *
     * @param  mixed $items
     * @param  array $array
     * @param  mixed $default
     * @return array
     */
    public static function fetch_items(array $array, $items, $default = null)
    {
        if (is_numeric($items) || is_string($items)) {
            return isset($array[$items]) ? $array[$items] : $default;
        }

        if (!is_array($items)) {
            return $default;
        }

        $return = array();
        foreach ($items as $item) {
            $return[$item] = isset($array[$item]) ? $array[$item] : $default;
        }

        return $return;
    }

    /**
     * 二维数组里fetch, 保留键名
     * @author gjy
     *
     * @param  array $array
     * @param  array $items
     * @param  mixed $default
     * @return array
     */
    public static function fetch_more(array $array, $items, $default = null)
    {
        $return = array();

        foreach ($array as $key => $val) {
            $return[$key] = array();
            foreach ($items as $v) {
                $return[$key][$v] = isset($val[$v]) ? $val[$v] : $default;
            }
        }

        return $return;
    }

    /**
     * 可深度fetch, 结果是一维数组 (laravel里偷来的2333...
     *
     * @param  array $array
     * @param  string $key
     * @return array
     */
    public static function fetch(array $array, $key)
    {
        foreach (explode('.', $key) as $segment) {
            $results = array();

            foreach ($array as $value) {
                if (array_key_exists($segment, $value = (array) $value)) {
                    $results[] = $value[$segment];
                }
            }

            $array = array_values($results);
        }

        return array_values($results);
    }

    /**
     * 使用传入的key当作外层array的index
     * @author gjy
     *
     * @param  array $array
     * @param  string | integer $key
     * @return array
     */
    public static function change_index(array $array, $key)
    {
        $check = reset($array);
        if (!isset($check[$key])) {
            return array();
        }

        $results = array();
        foreach ($array as $v) {
            if (isset($results[$v[$key]])) {
                continue;
            }

            $results[$v[$key]] = $v;
        }

        return $results;
    }

    /**
     * fetch array return map
     * @author gjy
     *
     * @param  array $array
     * @param  string $keyValue # ex: item_id.title
     * @return array
     */
    public static function fetch_map(array $array, $keyValue)
    {
        $check = reset($array);
        $kv = explode('.', $keyValue);
        if (count($kv) !== 2 || !isset($check[$kv[0]], $check[$kv[1]])) {
            return array();
        }

        $indexKey = $kv[0];
        $valueKey = $kv[1];

        $results = array();
        foreach ($array as $v) {
            if (isset($results[$v[$indexKey]])) {
                continue;
            }

            $results[$v[$indexKey]] = $v[$valueKey];
        }

        return $results;
    }

}
