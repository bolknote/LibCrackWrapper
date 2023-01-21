<?php
declare(strict_types=1);

namespace LibCrackWrapper\Classes;

final class Constants
{
    public const STR_RESULTS = [
        null,
        'it is based on your username',
        'it is based upon your password entry',
        'it is derived from your password entry',
        'it is derivable from your password entry',
        "it's derivable from your password entry",
        'you are not registered in the password file',
        'it is WAY too short',
        'it is too short',
        'it does not contain enough DIFFERENT characters',
        'it is all whitespace',
        'it is too simplistic/systematic',
        'it looks like a National Insurance number.',
        'it is based on a dictionary word',
        'it is based on a (reversed) dictionary word',
        'error loading dictionary',
        "it's derived from your password entry",
    ];
}