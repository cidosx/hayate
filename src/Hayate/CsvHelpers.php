<?php

namespace Hayate;

class CsvHelpers {

	/** end of line */
	CONST EOL = "\r\n";

	/** 字符定界 */
	CONST ENCLOSURE = '"';

	/** 默认逗号分隔符 */
	CONST DELIMITER = ',';

	/** tab分隔符 */
	CONST DELIMITER_TAB = '	';


	/**
	 * 保存为csv文件
	 * @author gjy
	 *
	 * @param  string $filePathName
	 * @param  array $data
	 * @return boolean
	 */
	public static function storage($filePathName, array $data, $overwrite = FALSE) {

		if (is_file($filePathName) && !$overwrite) {
			// TODO throw exception
			return FALSE;
		}

		if (empty($data['rows'])) {
			return FALSE;
		}

		touch($filePathName);
		$filePathName = realpath($filePathName);

		$fp = fopen($filePathName, 'w');

		// 添加bom头 EFBBBF 为utf8 bom
		fwrite($fp, pack('H*','EFBBBF'));

		// 自定义一个数组header, 与cell分开处理
		if (! empty($data['header'])) {
			fwrite($fp, self::ENCLOSURE
				. join(self::ENCLOSURE . self::DELIMITER . self::ENCLOSURE, $data['header'])
				. self::ENCLOSURE
				. self::EOL);
		}

		// cell中需要判断是否为长数字
		foreach ($data['rows'] as $row) {
			if (is_array($row)) {
				$contents = '';

				foreach ($row as $cell) {
					if (is_numeric($cell)) {
						// 增加一个tab防止数字被转换成科学计数
						$contents .= self::ENCLOSURE . $cell . '	' . self::ENCLOSURE . self::DELIMITER;
					}
					else {
						// 转义内容中的引号
						$contents .= self::ENCLOSURE . str_replace('"', '""', $cell) . self::ENCLOSURE . self::DELIMITER;
					}
				}

				fwrite($fp, rtrim($contents, self::DELIMITER) . self::EOL);
			}
			else {
				fwrite($fp, $row . self::EOL);
			}
		}

		fclose($fp);
		return $filePathName;
	}


	/**
	 * 下载文件
	 * @author gjy
	 *
	 * @param  string $filename
	 * @param  array $data
	 * @return void
	 */
	public static function download($filename, array $data) {
		// 类似的逻辑, 但结果变为输出到浏览器
	}
}
