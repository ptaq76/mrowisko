<?php

return [

    'imap' => [
        'host'          => env('REKLAMACJE_IMAP_HOST', 'h56.seohost.pl'),
        'port'          => env('REKLAMACJE_IMAP_PORT', 993),
        'encryption'    => env('REKLAMACJE_IMAP_ENCRYPTION', 'ssl'),
        'validate_cert' => env('REKLAMACJE_IMAP_VALIDATE_CERT', false),
        'username'      => env('REKLAMACJE_IMAP_USERNAME', ''),
        'password'      => env('REKLAMACJE_IMAP_PASSWORD', ''),
        'folder'        => env('REKLAMACJE_IMAP_FOLDER', 'INBOX'),
    ],

    'gewichtsmeldung_imap' => [
        'host'          => env('GEWICHTSMELDUNG_IMAP_HOST', 'h56.seohost.pl'),
        'port'          => env('GEWICHTSMELDUNG_IMAP_PORT', 993),
        'encryption'    => env('GEWICHTSMELDUNG_IMAP_ENCRYPTION', 'ssl'),
        'validate_cert' => env('GEWICHTSMELDUNG_IMAP_VALIDATE_CERT', false),
        'username'      => env('GEWICHTSMELDUNG_IMAP_USERNAME', 'gewichtsmeldung@iantra.pl'),
        'password'      => env('GEWICHTSMELDUNG_IMAP_PASSWORD', ''),
        'folder'        => env('GEWICHTSMELDUNG_IMAP_FOLDER', 'INBOX'),
    ],

];
