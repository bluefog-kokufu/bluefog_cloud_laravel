<?php

namespace App\Support;

class ContentDisposition
{
    /**
     * 日本語ファイル名を含むダウンロードのContent-Dispositionヘッダー値を
     * RFC 5987/6266形式(filename*=UTF-8''...)で生成する。
     * この形式に対応していない古いブラウザ向けに、ASCIIのフォールバック名も併記する。
     */
    public static function attachment(string $filename): string
    {
        $ascii = preg_replace('/[^\x20-\x7E]/', '_', $filename) ?: 'download.csv';

        return sprintf(
            'attachment; filename="%s"; filename*=UTF-8\'\'%s',
            $ascii,
            rawurlencode($filename)
        );
    }
}
