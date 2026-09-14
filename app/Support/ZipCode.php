<?php

namespace App\Support;

class ZipCode
{
    /**
     * 入力チェック用の正規表現(ハイフンあり/なしの7桁数字のみ許容)
     */
    public const PATTERN = '/^\d{3}-?\d{4}$/';

    /**
     * 郵便番号を表示用にハイフン付き(XXX-XXXX)へフォーマットする。
     * ハイフンなしの7桁数字のみ変換し、それ以外の値はそのまま返す。
     */
    public static function format(?string $zip): ?string
    {
        if ($zip === null || $zip === '') {
            return $zip;
        }

        if (preg_match('/^\d{7}$/', $zip)) {
            return substr($zip, 0, 3).'-'.substr($zip, 3);
        }

        return $zip;
    }
}
