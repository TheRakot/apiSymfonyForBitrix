<?php

declare(strict_types=1);

namespace Classes\Utility;

/**
 * Class TextHelper
 *
 * @package Classes\Utility
 */
final class TextHelper
{
	/**
	 * Очищает текст от спецсимволов и html сущностей
	 *
	 * @param string $text
	 * @param int    $length > 0 обрезает текст и добавляет ...
	 *
	 * @return string
	 */
	public static function truncate(string $text, int $length = 0): string
	{
		# убираем из текста &nbsp; и прочие символы
		$text = html_entity_decode($text);
		# чистим текст от тегов
		$result = $trim = trim(strip_tags($text));
		
		if ($length > 0) {
			#Уменьшим длину на размер троеточия
			if (mb_strlen($trim) > $length) {
				$length -= 3;
			}
			# добавляем троеточие
			$result = mb_substr($trim, 0, $length);
			
			if (mb_strlen((string)$result) == $length) {
				$result .= '...';
			}
		}
		
		return $result;
	}
	
	/**
	 * Возвращает текст между открывающим и закрывающим тэгом с именем $tagName (<$tagname>(returned TEXT)</$tagname>)
	 *
	 * @param mixed $tagName - имя тэга (не должно совпадать со стандартным именем HTML тэга)
	 * @param mixed $strFrom - строка
	 *
	 * @return string между двумя заданными тегами, иначе false
	 */
	public static function getTextBetweenTags($tagName, $strFrom): string
	{
		$arDenyTags = [
			'div',
			'span',
			'p',
			'table',
			'tr',
			'td',
			'tbody',
			'html',
			'head',
			'body',
			'acronym',
			'a',
			'input',
			'textarea',
		];
		
		if (!in_array($tagName, $arDenyTags, true)) {
			$startPos = stripos($strFrom, '<' . $tagName . '>') + strlen('<' . $tagName . '>');
			$endPos = stripos($strFrom, '</' . $tagName . '>') - stripos(
					$strFrom,
					'<' . $tagName . '>'
				) - strlen('<' . $tagName . '>');
			
			$strRes = substr($strFrom, $startPos, $endPos);
			//если между тэгами есть хотя бы 1 символ
			if ('' !== $strRes) {
				return $strRes;
			}
		}
		
		return '';
	}
	
	/**
	 * Возвращает одну из трех форм слова
	 * к числительному
	 *
	 * @param int   $number — числительное для которого нужно подобрать
	 * @param array $forms  = ('ед.число ед. сущ', 'мн числ. - ед число.' ,'мн. числительное мн число' )
	 *
	 * @return string
	 * @uses - getWordForm(3, array('Найден', 'Найдено', 'Найдено').' '.getWordForm(3, array('результат', 'результата',
	 *       'результатов'))
	 */
	public static function getWordForm(int $number, array $forms): string
	{
		$result = '';
		
		if ($number > 0) {
			$number = abs($number) % 100;
			
			$var1 = $number % 10;
			
			if ($number > 10 && $number < 20) {
				$result = $forms[2];
			} elseif ($var1 > 1 && $var1 < 5) {
				$result = $forms[1];
			} elseif (1 === $var1) {
				$result = $forms[0];
			} else {
				$result = $forms[2];
			}
		}
		
		return $result;
	}
	
	/**
	 * @param string $text
	 *
	 * @return string
	 */
	public static function noNumbers(string $text): string
	{
		return preg_replace('/\d/', '', $text);
	}
}