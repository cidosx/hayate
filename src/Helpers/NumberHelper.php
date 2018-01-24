<?php

namespace Hayate\Helpers;

use InvalidArgumentException;

/**
 * 数字处理函数(并无卵用)
 * _(:3」∠x)_
 */
class NumberHelper
{
    /**
     * 除法等商拆分
     *
     * @author  gjy
     *
     * @param   numeric  $dividend
     * @param   numeric  $divisor
     * @param   integer  $n
     * @param   integer  $limit
     * @return  array
     */
    public static function division_split($dividend, $divisor, int $n, int $limit = 233)
    {
        if (1 > $n || 1 > $dividend || 1 > $divisor) {
            throw new InvalidArgumentException('搞事情啊?', 1);
        }

        if ($limit && $n > $limit) {
            throw new InvalidArgumentException('拆分太多了本函数不是很想算.', 1);
        }

        if (1 === $n) {
            return [
                'success' => true,
                'result' => [
                    [
                        'dividend' => $dividend,
                        'divisor' => $divisor,
                    ],
                ],
            ];
        }

        // 算出商
        $quotient = $dividend / $divisor;

        $data = [
            'success' => $divisor >= $n,
            'result' => [],
        ];

        while ($dividend > 0) {
            for($i = 0; $i < $n && $dividend > 0; ++$i) {
                if (! isset($data['result'][$i])) {
                    $data['result'][$i] = [
                        'dividend' => 0,
                        'divisor' => 0,
                    ];
                }

                // 如果最后一组不够分了, 这组除法的商就不等于其他组
                if ($dividend < $quotient) {
                    $data['result'][$i]['dividend'] += ($quotient - $dividend);
                    $data['success'] = false;
                }
                else {
                    $data['result'][$i]['dividend'] += $quotient;
                }

                $data['result'][$i]['divisor'] += 1;
                $dividend -= $quotient;
            }
        }

        return $data;
    }

    /**
     * 长整型相减
     * @author gjy
     *
     * @param  number string $num1
     * @param  number string $num2
     * @return number string
     */
    public static function long_int_minus($num1, $num2)
    {
        if (!self::_check_long_number($num1, $num2)) {
            return 0;
        }

        if ($num1 < $num2) {
            return '-' . self::long_int_minus($num2, $num1);
        }

        $arr1 = str_split(strrev($num1), 9);
        $arr2 = str_split(strrev($num2), 9);

        foreach ($arr1 as &$v) {
            $v = strrev($v);
        }
        unset($v);

        foreach ($arr2 as &$v) {
            $v = strrev($v);
        }
        unset($v);

        $len = count($arr1);
        foreach ($arr2 as $k => $v) {
            if ($arr1[$k] < $v) {
                for ($i = $k + 1; $i < $len; ++$i) {
                    if ($arr1[$i] < 1) {
                        $arr1[$i] = 999999999;
                        continue;
                    }
                    $arr1[$i] -= 1;
                    break;
                }
                $arr1[$k] = 1 . $arr1[$k] - $v;
            } else {
                $arr1[$k] = $arr1[$k] - $v;
            }
        }

        $hehe = '';
        foreach ($arr1 as $v) {
            $hehe = (strlen($v) === 9 ? $v : sprintf("%09d", $v)) . $hehe;
        }

        return ltrim($hehe, '0');
    }

    /**
     * 长整型相加
     * @author gjy
     *
     * @param  number string $num1
     * @param  number string $num2
     * @return number string
     */
    public static function long_int_sum($num1, $num2)
    {
        if (!self::_check_long_number($num1, $num2)) {
            return 0;
        }

        if ($num1 < $num2) {
            return self::long_int_sum($num2, $num1);
        }

        $arr1 = str_split(strrev($num1), 9);
        $arr2 = str_split(strrev($num2), 9);

        foreach ($arr1 as &$v) {
            $v = strrev($v);
        }
        unset($v);

        foreach ($arr2 as &$v) {
            $v = strrev($v);
        }
        unset($v);

        foreach ($arr2 as $k => $v) {
            $arr1[$k] = $arr1[$k] + $v;
            if ($arr1[$k] > 999999999) {
                $arr1[$k] -= 1000000000;
                $arr1[$k + 1] += 1;
            }
        }

        $hehe = '';
        foreach ($arr1 as $v) {
            $hehe = $v . $hehe;
        }

        return $hehe;
    }

    private static function _check_long_number($num1, $num2)
    {
        return is_numeric($num1) && is_numeric($num2) && $num1 >= 0 && $num2 >= 0 && strpos($num1, '.') === false && strpos($num1, '.') === false && ($num1 > 999999999 || $num2 > 999999999);
    }

    /**
     * 字节转换
     * @author gjy
     *
     * @param  integer | number string $num
     * @param  integer $precision
     * @return string
     */
    public static function byte_format($num, $precision = 1)
    {
        if ($num >= 1000000000000) {
            $num = round($num / 1099511627776, $precision);
            $unit = 'TB';
        } elseif ($num >= 1000000000) {
            $num = round($num / 1073741824, $precision);
            $unit = 'GB';
        } elseif ($num >= 1000000) {
            $num = round($num / 1048576, $precision);
            $unit = 'MB';
        } elseif ($num >= 1000) {
            $num = round($num / 1024, $precision);
            $unit = 'KB';
        } else {
            $unit = 'Bytes';
            return number_format($num) . ' ' . $unit;
        }

        return number_format($num, $precision) . ' ' . $unit;
    }
}
