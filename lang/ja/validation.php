<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | バリデーションエラーメッセージの日本語訳。属性名は各FormRequestの
    | attributes()で日本語化しているため、ここではメッセージ本文のみを訳す。
    |
    */

    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは有効なURLではありません。',
    'after' => ':attributeには:dateより後の日付を指定してください。',
    'after_or_equal' => ':attributeには:date以降の日付を指定してください。',
    'alpha' => ':attributeはアルファベットのみ使用できます。',
    'alpha_dash' => ':attributeはアルファベット、数字、ダッシュ(-)、アンダースコア(_)が使用できます。',
    'alpha_num' => ':attributeはアルファベットと数字が使用できます。',
    'array' => ':attributeは配列を指定してください。',
    'ascii' => ':attributeは半角の英数字と記号のみ使用できます。',
    'before' => ':attributeには:dateより前の日付を指定してください。',
    'before_or_equal' => ':attributeには:date以前の日付を指定してください。',
    'between' => [
        'array' => ':attributeは:min個から:max個までの範囲で指定してください。',
        'file' => ':attributeは:min KBから:max KBまでの範囲で指定してください。',
        'numeric' => ':attributeは:minから:maxまでの範囲で指定してください。',
        'string' => ':attributeは:min文字から:max文字までの範囲で指定してください。',
    ],
    'boolean' => ':attributeにはtrueかfalseを指定してください。',
    'can' => ':attributeに不正な値が含まれています。',
    'confirmed' => ':attributeと確認用の値が一致しません。',
    'contains' => ':attributeに必要な値が含まれていません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attributeには有効な日付を指定してください。',
    'date_equals' => ':attributeには:dateと同じ日付を指定してください。',
    'date_format' => ':attributeの形式が:formatと一致しません。',
    'decimal' => ':attributeは小数点以下:decimal桁で指定してください。',
    'declined' => ':attributeを拒否してください。',
    'declined_if' => ':otherが:valueの場合、:attributeを拒否してください。',
    'different' => ':attributeと:otherには異なる値を指定してください。',
    'digits' => ':attributeは:digits桁で指定してください。',
    'digits_between' => ':attributeは:min桁から:max桁までの範囲で指定してください。',
    'dimensions' => ':attributeの画像サイズが無効です。',
    'distinct' => ':attributeに重複した値があります。',
    'doesnt_end_with' => ':attributeは:valuesのいずれでも終わらない値を指定してください。',
    'doesnt_start_with' => ':attributeは:valuesのいずれでも始まらない値を指定してください。',
    'email' => ':attributeには有効なメールアドレスを指定してください。',
    'ends_with' => ':attributeには:valuesのいずれかで終わる値を指定してください。',
    'enum' => '選択された:attributeは無効です。',
    'exists' => '選択された:attributeは無効です。',
    'extensions' => ':attributeは:valuesのいずれかの拡張子を指定してください。',
    'file' => ':attributeにはファイルを指定してください。',
    'filled' => ':attributeに値を指定してください。',
    'gt' => [
        'array' => ':attributeは:value個より多くしてください。',
        'file' => ':attributeは:value KBより大きくしてください。',
        'numeric' => ':attributeは:valueより大きい値にしてください。',
        'string' => ':attributeは:value文字より多くしてください。',
    ],
    'gte' => [
        'array' => ':attributeは:value個以上にしてください。',
        'file' => ':attributeは:value KB以上にしてください。',
        'numeric' => ':attributeは:value以上の値にしてください。',
        'string' => ':attributeは:value文字以上にしてください。',
    ],
    'hex_color' => ':attributeには有効な16進数カラーコードを指定してください。',
    'image' => ':attributeには画像を指定してください。',
    'in' => '選択された:attributeは無効です。',
    'in_array' => ':attributeが:otherに存在しません。',
    'integer' => ':attributeは整数で指定してください。',
    'ip' => ':attributeには有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeには有効なIPv4アドレスを指定してください。',
    'ipv6' => ':attributeには有効なIPv6アドレスを指定してください。',
    'json' => ':attributeには有効なJSON文字列を指定してください。',
    'list' => ':attributeはリスト形式で指定してください。',
    'lowercase' => ':attributeは小文字で指定してください。',
    'lt' => [
        'array' => ':attributeは:value個より少なくしてください。',
        'file' => ':attributeは:value KBより小さくしてください。',
        'numeric' => ':attributeは:valueより小さい値にしてください。',
        'string' => ':attributeは:value文字より少なくしてください。',
    ],
    'lte' => [
        'array' => ':attributeは:value個以下にしてください。',
        'file' => ':attributeは:value KB以下にしてください。',
        'numeric' => ':attributeは:value以下の値にしてください。',
        'string' => ':attributeは:value文字以下にしてください。',
    ],
    'mac_address' => ':attributeには有効なMACアドレスを指定してください。',
    'max' => [
        'array' => ':attributeは:max個以下指定してください。',
        'file' => ':attributeは:max KB以下のファイルを指定してください。',
        'numeric' => ':attributeは:max以下の数字を指定してください。',
        'string' => ':attributeは:max文字以下で指定してください。',
    ],
    'max_digits' => ':attributeは:max桁以下で指定してください。',
    'mimes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには:valuesタイプのファイルを指定してください。',
    'min' => [
        'array' => ':attributeは:min個以上指定してください。',
        'file' => ':attributeは:min KB以上のファイルを指定してください。',
        'numeric' => ':attributeは:min以上の数字を指定してください。',
        'string' => ':attributeは:min文字以上で指定してください。',
    ],
    'min_digits' => ':attributeは:min桁以上で指定してください。',
    'missing' => ':attributeは存在してはいけません。',
    'missing_if' => ':otherが:valueの場合、:attributeは存在してはいけません。',
    'missing_unless' => ':otherが:valueでない限り、:attributeは存在してはいけません。',
    'missing_with' => ':valuesが存在する場合、:attributeは存在してはいけません。',
    'missing_with_all' => ':valuesが全て存在する場合、:attributeは存在してはいけません。',
    'multiple_of' => ':attributeは:valueの倍数で指定してください。',
    'not_in' => '選択された:attributeは無効です。',
    'not_regex' => ':attributeの形式が無効です。',
    'numeric' => ':attributeは数字で指定してください。',
    'password' => [
        'letters' => ':attributeは1文字以上の英字を含めてください。',
        'mixed' => ':attributeは1文字以上の大文字と小文字を含めてください。',
        'numbers' => ':attributeは1文字以上の数字を含めてください。',
        'symbols' => ':attributeは1文字以上の記号を含めてください。',
        'uncompromised' => '指定した:attributeは漏洩したことがあるパスワードです。別の:attributeを指定してください。',
    ],
    'present' => ':attributeが存在していません。',
    'present_if' => ':otherが:valueの場合、:attributeが存在している必要があります。',
    'present_unless' => ':otherが:valueでない限り、:attributeが存在している必要があります。',
    'present_with' => ':valuesが存在する場合、:attributeが存在している必要があります。',
    'present_with_all' => ':valuesが全て存在する場合、:attributeが存在している必要があります。',
    'prohibited' => ':attributeは許可されていません。',
    'prohibited_if' => ':otherが:valueの場合、:attributeは許可されていません。',
    'prohibited_unless' => ':otherが:valuesに含まれていない限り、:attributeは許可されていません。',
    'prohibits' => ':attributeは:otherの存在を禁止しています。',
    'regex' => ':attributeの形式が無効です。',
    'required' => ':attributeを入力してください。',
    'required_array_keys' => ':attributeに:valuesを含めてください。',
    'required_if' => ':otherが:valueの場合、:attributeを入力してください。',
    'required_if_accepted' => ':otherを承認する場合、:attributeを入力してください。',
    'required_if_declined' => ':otherを拒否する場合、:attributeを入力してください。',
    'required_unless' => ':otherが:valuesでない場合、:attributeを入力してください。',
    'required_with' => ':valuesが指定されている場合、:attributeを入力してください。',
    'required_with_all' => ':valuesが全て指定されている場合、:attributeを入力してください。',
    'required_without' => ':valuesが指定されていない場合、:attributeを入力してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを入力してください。',
    'same' => ':attributeと:otherには同じ値を指定してください。',
    'size' => [
        'array' => ':attributeは:size個指定してください。',
        'file' => ':attributeのサイズは:size KBにしてください。',
        'numeric' => ':attributeは:sizeにしてください。',
        'string' => ':attributeは:size文字にしてください。',
    ],
    'starts_with' => ':attributeには:valuesのいずれかで始まる値を指定してください。',
    'string' => ':attributeは文字列を指定してください。',
    'timezone' => ':attributeには有効なタイムゾーンを指定してください。',
    'unique' => ':attributeはすでに使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'uppercase' => ':attributeは大文字で指定してください。',
    'url' => ':attributeには有効なURLを指定してください。',
    'ulid' => ':attributeには有効なULIDを指定してください。',
    'uuid' => ':attributeには有効なUUIDを指定してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | 各FormRequestのattributes()側で個別に日本語化しているため、
    | ここでは空のままとする。
    |
    */

    'attributes' => [],

];
