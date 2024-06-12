<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;

class CountriesTableSeeder  extends Seeder
{
    public function run()
    {
        \DB::table('countries')->insert(array (
            0 =>
                array (
                    'id' => 1,
                    'name' => 'Cameroon',
                    'currencyCode' => 'XAF',
                ),
            1 =>
                array (
                    'id' => 2,
                    'name' => 'Benin',
                    'currencyCode' => 'XOF',
                ),
            2 =>
                array (
                    'id' => 3,
                    'name' => 'Madagascar',
                    'currencyCode' => 'MGA',
                ),
            3 =>
                array (
                    'id' => 4,
                    'name' => 'Rwanda',
                    'currencyCode' => 'RWF',
                ),
            4 =>
                array (
                    'id' => 5,
                    'name' => 'Seychelles',
                    'currencyCode' => 'SCR',
                ),
            5 =>
                array (
                    'id' => 6,
                    'name' => 'Cote d’Ivoire',
                    'currencyCode' => 'XOF',
                ),
            6 =>
                array (
                    'id' => 7,
                    'name' => 'Egypt',
                    'currencyCode' => 'EGP',
                ),
            7 =>
                array (
                    'id' => 8,
                    'name' => 'Mauritius',
                    'currencyCode' => 'MUR',
                ),
            8 =>
                array (
                    'id' => 9,
                    'name' => 'Burkina Faso',
                    'currencyCode' => 'XOF',
                ),
            9 =>
                array (
                    'id' => 10,
                    'name' => 'Eritrea',
                    'currencyCode' => 'ERN',
                ),
            10 =>
                array (
                    'id' => 11,
                    'name' => 'Sao Tome and Principe',
                    'currencyCode' => 'STD',
                ),
            11 =>
                array (
                    'id' => 12,
                    'name' => 'Angola',
                    'currencyCode' => 'AOA',
                ),
            12 =>
                array (
                    'id' => 13,
                    'name' => 'Libya',
                    'currencyCode' => 'LYD',
                ),
            13 =>
                array (
                    'id' => 14,
                    'name' => 'Zimbabwe',
                    'currencyCode' => 'ZWL',
                    ),
            14 =>
                array (
                    'id' => 15,
                    'name' => 'Guinea',
                     'currencyCode' => 'GNF',
                    ),
            15 =>
                array (
                    'id' => 16,
                    'name' => 'Sierra Leone',
                    'currencyCode' => 'SLL',
                    ),
            16 =>
                array (
                    'id' => 17,
                    'name' => 'Reunion',
                     'currencyCode' => 'EUR',
                      ),
            17 =>
                array (
                    'id' => 18,
                    'name' => 'Gabon',
                    'currencyCode' => 'XAF',
                     ),
            18 =>
                array (
                    'id' => 19,
                    'name' => 'Ghana',
                    'currencyCode' => 'GHS',
                   ),
            19 =>
                array (
                    'id' => 20,
                    'name' => 'Tanzania',
                    'currencyCode' => 'TZS',
                ),
            20 =>
                array (
                    'id' => 21,
                    'name' => 'Mali',
                    'currencyCode' => 'XOF',
                    ),
            21 =>
                array (
                    'id' => 22,
                    'name' => 'Somalia',
                    'currencyCode' => 'SOS',
                    ),
            22 =>
                array (
                    'id' => 23,
                    'name' => 'Mauritania',
                    'currencyCode' => 'MRO',
                    ),
            23 =>
                array (
                    'id' => 24,
                    'name' => 'Uganda',
                     'currencyCode' => 'UGX',
                    ),
            24 =>
                array (
                    'id' => 25,
                    'name' => 'Chad',
                    'currencyCode' => 'XAF',
                   ),
            25 =>
                array (
                    'id' => 26,
                    'name' => 'Mayotte',
                    'currencyCode' => 'EUR',
                    ),
            26 =>
                array (
                    'id' => 27,
                    'name' => 'Comoros',
                    'currencyCode' => 'KMF',
                ),
            27 =>
                array (
                    'id' => 28,
                    'name' => 'Botswana',
                   'currencyCode' => 'BWP',
                    ),
            28 =>
                array (
                    'id' => 29,
                    'name' => 'Senegal',
                    'currencyCode' => 'XOF',
                ),
            29 =>
                array (
                    'id' => 30,
                    'name' => 'Eswatini',
                    'currencyCode' => 'SZL',
                ),
            30 =>
                array (
                    'id' => 31,
                    'name' => 'Guinea-Bissau',
                    'currencyCode' => 'XOF',
                ),
            31 =>
                array (
                    'id' => 32,
                    'name' => 'DR Congo',
                    'currencyCode' => 'CDF',
                ),
            32 =>
                array (
                    'id' => 33,
                    'name' => 'Central African Republic',
                    'currencyCode' => 'XAF',
                ),
            33 =>
                array (
                    'id' => 34,
                    'name' => 'Lesotho',
                    'currencyCode' => 'LSL',
                ),
            34 =>
                array (
                    'id' => 35,
                    'name' => 'Congo',
                    'currencyCode' => 'XAF',
                ),
            35 =>
                array (
                    'id' => 36,
                    'name' => 'South Africa',
                    'currencyCode' => 'ZAR',
                ),
            36 =>
                array (
                    'id' => 37,
                    'name' => 'Liberia',
                    'currencyCode' => 'LRD',
                ),
            37 =>
                array (
                    'id' => 38,
                    'name' => 'Tunisia',
                    'currencyCode' => 'TND',
                ),
            38 =>
                array (
                    'id' => 39,
                    'name' => 'Zambia',
                    'currencyCode' => 'ZMW',
                ),
            39 =>
                array (
                    'id' => 40,
                    'name' => 'Niger',
                    'currencyCode' => 'XOF',
                ),
            40 =>
                array (
                    'id' => 41,
                    'name' => 'Western Sahara',
                    'currencyCode' => 'DZD',
                ),
            41 =>
                array (
                    'id' => 42,
                    'name' => 'Togo',
                    'currencyCode' => 'XOF',
                ),
            42 =>
                array (
                    'id' => 43,
                    'name' => 'Namibia',
                    'currencyCode' => 'NAD',
                ),
            43 =>
                array (
                    'id' => 44,
                    'name' => 'Mozambique',
                    'currencyCode' => 'MZN',
                ),
            44 =>
                array (
                    'id' => 45,
                    'name' => 'Ethiopia',
                    'currencyCode' => 'ETB',
                ),
            45 =>
                array (
                    'id' => 46,
                    'name' => 'Morocco',
                    'currencyCode' => 'MAD',
                ),
            46 =>
                array (
                    'id' => 47,
                    'name' => 'Malawi',
                    'currencyCode' => 'MWK',
                ),
            47 =>
                array (
                    'id' => 48,
                    'name' => 'Nigeria',
                    'currencyCode' => 'NGN',
                ),
            48 =>
                array (
                    'id' => 49,
                    'name' => 'Cabo Verde',
                    'currencyCode' => 'CVE',
                ),
            49 =>
                array (
                    'id' => 50,
                    'name' => 'Burundi',
                    'currencyCode' => 'BIF',
                ),
            50 =>
                array (
                    'id' => 51,
                    'name' => 'Algeria',
                    'currencyCode' => 'DZD',
                ),
            51 =>
                array (
                    'id' => 52,
                    'name' => 'Djibouti',
                    'currencyCode' => 'DJF',
                ),
            52 =>
                array (
                    'id' => 53,
                    'name' => 'Guadeloupe',
                    'currencyCode' => 'EUR',
                ),
            53 =>
                array (
                    'id' => 54,
                    'name' => 'Equatorial Guinea',
                    'currencyCode' => 'XAF',
                ),
            54 =>
                array (
                    'id' => 55,
                    'name' => 'Sudan',
                    'currencyCode' => 'SDG',
                ),
            55 =>
                array (
                    'id' => 56,
                    'name' => 'Kenya',
                     'currencyCode' => 'KES',
                ),
            56 =>
                array (
                    'id' => 57,
                    'name' => 'Singapore',
                    'currencyCode' => 'SGD',
                ),
            57 =>
                array (
                    'id' => 58,
                    'name' => 'South Korea',
                    'currencyCode' => 'KRW',
                ),
            58 =>
                array (
                    'id' => 59,
                    'name' => 'Syria',
                    'currencyCode' => 'SYP',
                ),
            59 =>
                array (
                    'id' => 60,
                    'name' => 'Uzbekistan',
                    'currencyCode' => 'UZS',
                ),
            60 =>
                array (
                    'id' => 61,
                    'name' => 'Bahrain',
                    'currencyCode' => 'BHD',
                ),
            61 =>
                array (
                    'id' => 62,
                    'name' => 'Japan',
                    'currencyCode' => 'JPY',
                ),
            62 =>
                array (
                    'id' => 63,
                    'name' => 'Jordan',
                    'currencyCode' => 'JOD',
                ),
            63 =>
                array (
                    'id' => 64,
                    'name' => 'Vietnam',
                    'currencyCode' => 'VND',
                ),
            64 =>
                array (
                    'id' => 65,
                    'name' => 'Kyrgyzstan',
                    'currencyCode' => 'KGS',
                ),
            65 =>
                array (
                    'id' => 66,
                    'name' => 'Thailand',
                    'currencyCode' => 'THB',
                ),
            66 =>
                array (
                    'id' => 67,
                    'name' => 'Sri Lanka',
                    'currencyCode' => 'LKR',
                ),
            67 =>
                array (
                    'id' => 68,
                    'name' => 'United Arab Emirates',
                    'currencyCode' => 'AED',
                ),
            68 =>
                array (
                    'id' => 69,
                    'name' => 'Laos',
                    'currencyCode' => 'LAK',
                ),
            69 =>
                array (
                    'id' => 70,
                    'name' => 'Afghanistan',
                    'currencyCode' => 'AFN',
                ),
            70 =>
                array (
                    'id' => 71,
                    'name' => 'Macau',
                    'currencyCode' => 'MOP',
                ),
            71 =>
                array (
                    'id' => 72,
                    'name' => 'Tajikistan',
                    'currencyCode' => 'TJS',
                ),
            72 =>
                array (
                    'id' => 73,
                    'name' => 'North Korea',
                    'currencyCode' => 'KPW',
                ),
            73 =>
                array (
                    'id' => 74,
                    'name' => 'Palestine',
                    'currencyCode' => 'ILS',
                ),
            74 =>
                array (
                    'id' => 75,
                    'name' => 'Hong Kong',
                    'currencyCode' => 'HKD',
                ),
            75 =>
                array (
                    'id' => 76,
                    'name' => 'Iraq',
                    'currencyCode' => 'IQD',
                ),
            76 =>
                array (
                    'id' => 77,
                    'name' => 'Lebanon',
                    'currencyCode' => 'LBP',
                ),
            77 =>
                array (
                    'id' => 78,
                    'name' => 'Kuwait',
                    'currencyCode' => 'KWD',
                ),
            78 =>
                array (
                    'id' => 79,
                    'name' => 'Brunei',
                    'currencyCode' => 'BND',
                ),
            79 =>
                array (
                    'id' => 80,
                    'name' => 'Maldives',
                    'currencyCode' => 'MVR',
                ),
            80 =>
                array (
                    'id' => 81,
                    'name' => 'Indonesia',
                    'currencyCode' => 'IDR',
                ),
            81 =>
                array (
                    'id' => 82,
                    'name' => 'Israel',
                    'currencyCode' => 'ILS',
                ),
            82 =>
                array (
                    'id' => 83,
                    'name' => 'Mongolia',
                    'currencyCode' => 'MNT',
                ),
            83 =>
                array (
                    'id' => 84,
                    'name' => 'Oman',
                    'currencyCode' => 'OMR',
                ),
            84 =>
                array (
                    'id' => 85,
                    'name' => 'India',
                    'currencyCode' => 'INR',
                ),
            85 =>
                array (
                    'id' => 86,
                    'name' => 'Myanmar',
                    'currencyCode' => 'MMK',
                ),
            86 =>
                array (
                    'id' => 87,
                    'name' => 'Malaysia',
                    'currencyCode' => 'MYR',
                ),
            87 =>
                array (
                    'id' => 88,
                    'name' => 'East Timor',
                    'currencyCode' => 'USD',
                ),
            88 =>
                array (
                    'id' => 89,
                    'name' => 'Yemen',
                    'currencyCode' => 'YER',
                ),
            89 =>
                array (
                    'id' => 90,
                    'name' => 'Bhutan',
                    'currencyCode' => 'BTN',
                ),
            90 =>
                array (
                    'id' => 91,
                    'name' => 'Cambodia',
                    'currencyCode' => 'KHR',
                ),
            91 =>
                array (
                    'id' => 92,
                    'name' => 'Pakistan',
                    'currencyCode' => 'PKR',
                ),
            92 =>
                array (
                    'id' => 93,
                    'name' => 'Bangladesh',
                    'currencyCode' => 'BDT',
                ),
            93 =>
                array (
                    'id' => 94,
                    'name' => 'Saudi Arabia',
                    'currencyCode' => 'SAR',
                ),
            94 =>
                array (
                    'id' => 95,
                    'name' => 'Turkmenistan',
                    'currencyCode' => 'TMT',
                ),
            95 =>
                array (
                    'id' => 96,
                    'name' => 'Qatar',
                    'currencyCode' => 'QAR',
                ),
            96 =>
                array (
                    'id' => 97,
                    'name' => 'Nepal',
                    'currencyCode' => 'NPR',
                ),
            97 =>
                array (
                    'id' => 98,
                    'name' => 'Kazakhstan',
                    'currencyCode' => 'KZT',
                ),
            98 =>
                array (
                    'id' => 99,
                    'name' => 'Philippines',
                    'currencyCode' => 'PHP',
                ),
            99 =>
                array (
                    'id' => 100,
                    'name' => 'Taiwan',
                    'currencyCode' => 'TWD',
                ),
            100 =>
                array (
                    'id' => 101,
                    'name' => 'China',
                    'currencyCode' => 'CNY',
                ),
            101 =>
                array (
                    'id' => 102,
                    'name' => 'Iran',
                    'currencyCode' => 'IRR',
                ),
            102 =>
                array (
                    'id' => 103,
                    'name' => 'Costa Rica',
                    'currencyCode' => 'CRC',
                ),
            103 =>
                array (
                    'id' => 104,
                    'name' => 'Cuba',
                    'currencyCode' => 'CUC',
                ),
            104 =>
                array (
                    'id' => 105,
                    'name' => 'Dominican',
                    'currencyCode' => 'DOP',
                ),
            105 =>
                array (
                    'id' => 106,
                    'name' => 'Mexico',
                    'currencyCode' => 'MXN',
                ),
            106 =>
                array (
                    'id' => 107,
                    'name' => 'Nicaragua',
                    'currencyCode' => 'NIO',
                ),
            107 =>
                array (
                    'id' => 108,
                    'name' => 'Panama',
                    'currencyCode' => 'PAB',
                ),
            108 =>
                array (
                    'id' => 109,
                    'name' => 'Netherlands Antilles',
                    'currencyCode' => NULL,
                ),
            109 =>
                array (
                    'id' => 110,
                    'name' => 'El Salvador',
                    'currencyCode' => 'SVC',
                ),
            110 =>
                array (
                    'id' => 111,
                    'name' => 'Puerto Rico',
                    'currencyCode' => 'USD',
                ),
            111 =>
                array (
                    'id' => 112,
                    'name' => 'Saint Vincent and the Grenadines',
                    'currencyCode' => 'XCD',
                ),
            112 =>
                array (
                    'id' => 113,
                    'name' => 'Honduras',
                    'currencyCode' => 'HNL',
                ),
            113 =>
                array (
                    'id' => 114,
                    'name' => 'Guatemala',
                    'currencyCode' => 'GTQ',
                ),
            114 =>
                array (
                    'id' => 115,
                    'name' => 'Georgia',
                    'currencyCode' => 'GEL',
                ),
            115 =>
                array (
                    'id' => 116,
                    'name' => 'Armenia',
                    'currencyCode' => 'AMD',
                ),
            116 =>
                array (
                    'id' => 117,
                    'name' => 'Azerbaijan',
                    'currencyCode' => 'AZN',
                ),
            117 =>
                array (
                    'id' => 118,
                    'name' => 'Belarus',
                    'currencyCode' => 'BYR',
                ),
            118 =>
                array (
                    'id' => 119,
                    'name' => 'Russia',
                    'currencyCode' => 'RUB',
                ),
            119 =>
                array (
                    'id' => 120,
                    'name' => 'Ukraine',
                    'currencyCode' => 'UAH',
                ),
            120 =>
                array (
                    'id' => 121,
                    'name' => 'Hungary',
                    'currencyCode' => 'HUF',
                ),
            121 =>
                array (
                    'id' => 122,
                    'name' => 'Iceland',
                    'currencyCode' => 'ISK',
                ),
            122 =>
                array (
                    'id' => 123,
                    'name' => 'Malta',
                    'currencyCode' => 'EUR',
                ),
            123 =>
                array (
                    'id' => 124,
                    'name' => 'Monaco',
                    'currencyCode' => 'EUR',
                ),
            124 =>
                array (
                    'id' => 125,
                    'name' => 'Norway',
                    'currencyCode' => 'NOK',
                ),
            125 =>
                array (
                    'id' => 126,
                    'name' => 'Romania',
                    'currencyCode' => 'RON',
                ),
            126 =>
                array (
                    'id' => 127,
                    'name' => 'San Marino',
                    'currencyCode' => 'EUR',
                ),
            127 =>
                array (
                    'id' => 128,
                    'name' => 'Sweden',
                    'currencyCode' => 'SEK',
                ),
            128 =>
                array (
                    'id' => 129,
                    'name' => 'Switzerland',
                    'currencyCode' => 'CHE',
                ),
            129 =>
                array (
                    'id' => 130,
                    'name' => 'Estonia',
                    'currencyCode' => 'EUR',
                ),
            130 =>
                array (
                    'id' => 131,
                    'name' => 'Latvia',
                    'currencyCode' => 'EUR',
                ),
            131 =>
                array (
                    'id' => 132,
                    'name' => 'Lithuania',
                    'currencyCode' => 'EUR',
                ),
            132 =>
                array (
                    'id' => 133,
                    'name' => 'Moldova',
                    'currencyCode' => 'MDL',
                ),
            133 =>
                array (
                    'id' => 134,
                    'name' => 'Turkey',
                    'currencyCode' => 'TRY',
                ),
            134 =>
                array (
                    'id' => 135,
                    'name' => 'Slovenia',
                    'currencyCode' => 'EUR',
                ),
            135 =>
                array (
                    'id' => 136,
                    'name' => 'Czech',
                    'currencyCode' => 'CZK',
                ),
            136 =>
                array (
                    'id' => 137,
                    'name' => 'Slovakia',
                    'currencyCode' => 'EUR',
                ),
            137 =>
                array (
                    'id' => 138,
                    'name' => 'North Macedonia',
                    'currencyCode' => 'MKD',
                ),
            138 =>
                array (
                    'id' => 139,
                    'name' => 'Bosnia Herzegovina',
                    'currencyCode' => 'BAM',
                ),
            139 =>
                array (
                    'id' => 140,
                    'name' => 'Vatican City State',
                    'currencyCode' => 'EUR',
                ),
            140 =>
                array (
                    'id' => 141,
                    'name' => 'Netherlands',
                    'currencyCode' => 'EUR',
                ),
            141 =>
                array (
                    'id' => 142,
                    'name' => 'Croatia',
                    'currencyCode' => 'HRK',
                ),
            142 =>
                array (
                    'id' => 143,
                    'name' => 'Greece',
                    'currencyCode' => 'EUR',
                ),
            143 =>
                array (
                    'id' => 144,
                    'name' => 'Ireland',
                    'currencyCode' => 'EUR',
                ),
            144 =>
                array (
                    'id' => 145,
                    'name' => 'Belgium',
                    'currencyCode' => 'EUR',
                ),
            145 =>
                array (
                    'id' => 146,
                    'name' => 'Cyprus',
                    'currencyCode' => 'EUR',
                ),
            146 =>
                array (
                    'id' => 147,
                    'name' => 'Denmark',
                    'currencyCode' => 'DKK',
                ),
            147 =>
                array (
                    'id' => 148,
                    'name' => 'United Kingdom',
                    'currencyCode' => 'GBP',
                ),
            148 =>
                array (
                    'id' => 149,
                    'name' => 'Germany',
                    'currencyCode' => 'EUR',
                ),
            149 =>
                array (
                    'id' => 150,
                    'name' => 'France',
                    'currencyCode' => 'EUR',
                ),
            150 =>
                array (
                    'id' => 151,
                    'name' => 'Italy',
                    'currencyCode' => 'EUR',
                ),
            151 =>
                array (
                    'id' => 152,
                    'name' => 'Luxembourg',
                    'currencyCode' => 'EUR',
                ),
            152 =>
                array (
                    'id' => 153,
                    'name' => 'Portugal',
                    'currencyCode' => 'EUR',
                ),
            153 =>
                array (
                    'id' => 154,
                    'name' => 'Poland',
                    'currencyCode' => 'PLN',
                ),
            154 =>
                array (
                    'id' => 155,
                    'name' => 'Spain',
                    'currencyCode' => 'EUR',
                ),
            155 =>
                array (
                    'id' => 156,
                    'name' => 'Albania',
                    'currencyCode' => 'ALL',
                ),
            156 =>
                array (
                    'id' => 157,
                    'name' => 'Andorra',
                    'currencyCode' => 'EUR',
                ),
            157 =>
                array (
                    'id' => 158,
                    'name' => 'Liechtenstein',
                    'currencyCode' => 'CHF',
                ),
            158 =>
                array (
                    'id' => 159,
                    'name' => 'Serbia',
                    'currencyCode' => 'RSD',
                ),
            159 =>
                array (
                    'id' => 160,
                    'name' => 'Austria',
                    'currencyCode' => 'EUR',
                ),
            160 =>
                array (
                    'id' => 161,
                    'name' => 'Bulgaria',
                    'currencyCode' => 'BGN',
                ),
            161 =>
                array (
                    'id' => 162,
                    'name' => 'Finland',
                    'currencyCode' => 'EUR',
                ),
            162 =>
                array (
                    'id' => 163,
                    'name' => 'Gibraltar',
                    'currencyCode' => 'GIP',
                ),
            163 =>
                array (
                    'id' => 164,
                    'name' => 'Dominica',
                    'currencyCode' => 'XCD',
                ),
            164 =>
                array (
                    'id' => 165,
                    'name' => 'Bermuda',
                    'currencyCode' => 'BMD',
                ),
            165 =>
                array (
                    'id' => 166,
                    'name' => 'Canada',
                    'currencyCode' => 'CAD',
                ),
            166 =>
                array (
                    'id' => 167,
                    'name' => 'United States',
                    'currencyCode' => 'USD',
                ),
            167 =>
                array (
                    'id' => 168,
                    'name' => 'Greenland',
                    'currencyCode' => 'DKK',
                ),
            168 =>
                array (
                    'id' => 169,
                    'name' => 'Tonga',
                    'currencyCode' => 'TOP',
                ),
            169 =>
                array (
                    'id' => 170,
                    'name' => 'Australia',
                    'currencyCode' => 'AUD',
                ),
            170 =>
                array (
                    'id' => 171,
                    'name' => 'Cook Islands',
                    'currencyCode' => 'NZD',
                ),
            171 =>
                array (
                    'id' => 172,
                    'name' => 'Nauru',
                    'currencyCode' => 'AUD',
                ),
            172 =>
                array (
                    'id' => 173,
                    'name' => 'New Caledonia',
                    'currencyCode' => 'XPF',
                ),
            173 =>
                array (
                    'id' => 174,
                    'name' => 'Vanuatu',
                    'currencyCode' => 'VUV',
                ),
            174 =>
                array (
                    'id' => 175,
                    'name' => 'Solomon Islands',
                    'currencyCode' => 'SBD',
                ),
            175 =>
                array (
                    'id' => 176,
                    'name' => 'Samoa',
                    'currencyCode' => 'WST',
                ),
            176 =>
                array (
                    'id' => 177,
                    'name' => 'Tuvalu',
                    'currencyCode' => 'AUD',
                ),
            177 =>
                array (
                    'id' => 178,
                    'name' => 'Micronesia',
                    'currencyCode' => 'USD',
                ),
            178 =>
                array (
                    'id' => 179,
                    'name' => 'Marshall Islands',
                    'currencyCode' => 'USD',
                ),
            179 =>
                array (
                    'id' => 180,
                    'name' => 'Kiribati',
                    'currencyCode' => 'AUD',
                ),
            180 =>
                array (
                    'id' => 181,
                    'name' => 'French Polynesia',
                    'currencyCode' => 'XPF',
                ),
            181 =>
                array (
                    'id' => 182,
                    'name' => 'New Zealand',
                    'currencyCode' => 'NZD',
                ),
            182 =>
                array (
                    'id' => 183,
                    'name' => 'Fiji',
                    'currencyCode' => 'FJD',
                ),
            183 =>
                array (
                    'id' => 184,
                    'name' => 'Papua New Guinea',
                    'currencyCode' => 'PGK',
                ),
            184 =>
                array (
                    'id' => 185,
                    'name' => 'Palau',
                    'currencyCode' => 'USD',
                ),
            185 =>
                array (
                    'id' => 186,
                    'name' => 'Chile',
                    'currencyCode' => 'CLP',
                ),
            186 =>
                array (
                    'id' => 187,
                    'name' => 'Colombia',
                    'currencyCode' => 'COP',
                ),
            187 =>
                array (
                    'id' => 188,
                    'name' => 'Guyana',
                    'currencyCode' => 'GYD',
                ),
            188 =>
                array (
                    'id' => 189,
                    'name' => 'Paraguay',
                    'currencyCode' => 'PYG',
                ),
            189 =>
                array (
                    'id' => 190,
                    'name' => 'Peru',
                    'currencyCode' => 'PEN',
                ),
            190 =>
                array (
                    'id' => 191,
                    'name' => 'Suriname',
                    'currencyCode' => 'SRD',
                ),
            191 =>
                array (
                    'id' => 192,
                    'name' => 'Venezuela',
                    'currencyCode' => 'VEF',
                ),
            192 =>
                array (
                    'id' => 193,
                    'name' => 'Uruguay',
                    'currencyCode' => 'UYU',
                ),
            193 =>
                array (
                    'id' => 194,
                    'name' => 'Ecuador',
                    'currencyCode' => 'USD',
                ),
            194 =>
                array (
                    'id' => 195,
                    'name' => 'Antigua and Barbuda',
                    'currencyCode' => 'XCD',
                ),
            195 =>
                array (
                    'id' => 196,
                    'name' => 'Aruba',
                    'currencyCode' => 'AWG',
                ),
            196 =>
                array (
                    'id' => 197,
                    'name' => 'Bahamas',
                    'currencyCode' => 'BSD',
                ),
            197 =>
                array (
                    'id' => 198,
                    'name' => 'Barbados',
                    'currencyCode' => 'BBD',
                ),
            198 =>
                array (
                    'id' => 199,
                    'name' => 'Cayman Islands',
                    'currencyCode' => 'KYD',
                ),
            199 =>
                array (
                    'id' => 200,
                    'name' => 'Grenada',
                    'currencyCode' => 'XCD',
                ),
            200 =>
                array (
                    'id' => 201,
                    'name' => 'Haiti',
                    'currencyCode' => 'HTG',
                ),
            201 =>
                array (
                    'id' => 202,
                    'name' => 'Jamaica',
                    'currencyCode' => 'JMD',
                ),
            202 =>
                array (
                    'id' => 203,
                    'name' => 'Martinique',
                    'currencyCode' => 'EUR',
                ),
            203 =>
                array (
                    'id' => 204,
                    'name' => 'Montserrat',
                    'currencyCode' => 'XCD',
                ),
            204 =>
                array (
                    'id' => 205,
                    'name' => 'Trinidad and Tobago',
                    'currencyCode' => 'TTD',
                ),
            205 =>
                array (
                    'id' => 206,
                    'name' => 'Saint Kitts and Nevis',
                    'currencyCode' => 'XCD',
                ),
            206 =>
                array (
                    'id' => 207,
                    'name' => 'Saint Pierre and Miquelon',
                    'currencyCode' => 'EUR',
                ),
            207 =>
                array (
                    'id' => 208,
                    'name' => 'Argentina',
                    'currencyCode' => 'ARS',
                ),
            208 =>
                array (
                    'id' => 209,
                    'name' => 'Belize',
                    'currencyCode' => 'BZD',
                ),
            209 =>
                array (
                    'id' => 210,
                    'name' => 'Bolivia',
                    'currencyCode' => 'BOB',
                ),
            210 =>
                array (
                    'id' => 211,
                    'name' => 'Brazil',
                    'currencyCode' => 'BRL',
                ),
            211 =>
                array (
                    'id' => 212,
                    'name' => 'American Samoa',
                    'currencyCode' => 'USD',
                ),
            212 =>
                array (
                    'id' => 213,
                    'name' => 'Aland Islands',
                    'currencyCode' => 'EUR',
                ),
            213 =>
                array (
                    'id' => 214,
                    'name' => 'Saint Barthélemy',
                    'currencyCode' => 'EUR',
                ),
            214 =>
                array (
                    'id' => 215,
                    'name' => 'Bonaire, Sint Eustatius and Saba',
                    'currencyCode' => 'USD',
                ),
            215 =>
                array (
                    'id' => 216,
                    'name' => 'Bouvet Island',
                    'currencyCode' => 'NOK',
                ),
            216 =>
                array (
                    'id' => 217,
                    'name' => 'Cocos (Keeling) Islands',
                    'currencyCode' => 'AUD',
                ),
            217 =>
                array (
                    'id' => 218,
                    'name' => 'Curaçao',
                    'currencyCode' => 'ANG',
                ),
            218 =>
                array (
                    'id' => 219,
                    'name' => 'Christmas Island',
                    'currencyCode' => 'AUD',
                ),
            219 =>
                array (
                    'id' => 220,
                    'name' => 'Falkland Islands (Malvinas)',
                    'currencyCode' => 'FKP',
                ),
            220 =>
                array (
                    'id' => 221,
                    'name' => 'Faroe Islands',
                    'currencyCode' => 'DKK',
                ),
            221 =>
                array (
                    'id' => 222,
                    'name' => 'French Guiana',
                    'currencyCode' => 'EUR',
                ),
            222 =>
                array (
                    'id' => 223,
                    'name' => 'Guernsey',
                    'currencyCode' => 'GBP',
                ),
            223 =>
                array (
                    'id' => 224,
                    'name' => 'South Georgia and the South Sandwich Islands',
                    'currencyCode' => 'GBP',
                ),
            224 =>
                array (
                    'id' => 225,
                    'name' => 'Guam',
                    'currencyCode' => 'USD',
                ),
            225 =>
                array (
                    'id' => 226,
                    'name' => 'Heard Island and McDonald Islands',
                    'currencyCode' => 'AUD',
                ),
            226 =>
                array (
                    'id' => 227,
                    'name' => 'Isle of Man',
                    'currencyCode' => 'GBP',
                ),
            227 =>
                array (
                    'id' => 228,
                    'name' => 'British Indian Ocean Territory',
                    'currencyCode' => 'GBP',
                ),
            228 =>
                array (
                    'id' => 229,
                    'name' => 'Jersey',
                    'currencyCode' => 'GBP',
                ),
            229 =>
                array (
                    'id' => 230,
                    'name' => 'Saint Lucia',
                    'currencyCode' => 'XCD',
                ),
            230 =>
                array (
                    'id' => 231,
                    'name' => 'Saint Martin',
                    'currencyCode' => 'EUR',
                ),
            231 =>
                array (
                    'id' => 232,
                    'name' => 'Northern Mariana Islands',
                    'currencyCode' => 'USD',
                ),
            232 =>
                array (
                    'id' => 233,
                    'name' => 'Norfolk Island',
                    'currencyCode' => 'AUD',
                ),
            233 =>
                array (
                    'id' => 234,
                    'name' => 'Niue',
                    'currencyCode' => 'NZD',
                ),
            234 =>
                array (
                    'id' => 236,
                    'name' => 'Pitcairn',
                    'currencyCode' => 'NZD',
                ),
            235 =>
                array (
                    'id' => 237,
                    'name' => 'Saint Helena',
                    'currencyCode' => 'SHP',
                ),
            236 =>
                array (
                    'id' => 238,
                    'name' => 'Svalbard and Jan Mayen Islands',
                    'currencyCode' => 'NOK',
                ),
            237 =>
                array (
                    'id' => 239,
                    'name' => 'Sint Maarten',
                    'currencyCode' => 'ANG',
                ),
            238 =>
                array (
                    'id' => 240,
                    'name' => 'Turks and Caicos Islands',
                    'currencyCode' => 'USD',
                ),
            239 =>
                array (
                    'id' => 241,
                    'name' => 'French Southern Territories',
                    'currencyCode' => 'EUR',
                ),
            240 =>
                array (
                    'id' => 242,
                    'name' => 'Tokelau',
                    'currencyCode' => 'NZD',
                ),
            241 =>
                array (
                    'id' => 243,
                    'name' => 'United States Minor Outlying Islands',
                    'currencyCode' => 'USD',
                ),
            242 =>
                array (
                    'id' => 244,
                    'name' => 'British Virgin Islands',
                    'currencyCode' => 'USD',
                ),
            243 =>
                array (
                    'id' => 245,
                    'name' => 'United States Virgin Islands',
                    'currencyCode' => 'USD',
                ),
            244 =>
                array (
                    'id' => 246,
                    'name' => 'Wallis and Futuna Islands',
                    'currencyCode' => 'XPF',
                ),
            245 =>
                array (
                    'id' => 247,
                    'name' => 'Kosovo',
                    'currencyCode' => 'EUR',
                ),
            246 =>
                array (
                    'id' => 248,
                    'name' => 'Montenegro',
                    'currencyCode' => 'EUR',
                ),
            247 =>
                array (
                    'id' => 249,
                    'name' => 'Anguilla',
                    'currencyCode' => 'XPF',
                ),
            248 =>
                array (
                    'id' => 250,
                    'name' => 'Gambia',
                    'currencyCode' => 'GMD',

                ),
            249 =>
                array (
                    'id' => 251,
                    'name' => 'South Sudan',
                    'currencyCode' => 'SSP',
                ),
        ));


    }
}
