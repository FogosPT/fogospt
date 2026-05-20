<?php

return [
    'availableLocales' => [
        'pt',
        'en',
        'es',
    ],

    /*
     * Tópicos FCM unificados disponíveis para subscrição no site.
     * Os valores aqui correspondem ao topic name (sem prefixo de ambiente —
     * o backend acrescenta `dev-` fora de produção).
     */
    'notifications' => [
        [
            'name'  => 'pages.notifications.important',
            'value' => 'incident-important',
        ],
        [
            'name'  => 'pages.notifications.warnings',
            'value' => 'warnings',
        ],
        [
            'name'  => 'pages.notifications.planes',
            'value' => 'planes',
        ],
        [
            'name'  => 'pages.notifications.madeira',
            'value' => 'madeira',
        ],
    ],

    /*
     * Distritos com o respetivo código DICO. O tópico FCM resultante é
     * `district-<dico>00` (apenas incêndios). Madeira é gerida via tópico
     * dedicado `madeira` na secção `notifications`.
     */
    'districts' => [
        ['name' => 'pages.notifications.aveiro',         'value' => 'district-0100', 'dico' => '01'],
        ['name' => 'pages.notifications.beja',           'value' => 'district-0200', 'dico' => '02'],
        ['name' => 'pages.notifications.braga',          'value' => 'district-0300', 'dico' => '03'],
        ['name' => 'pages.notifications.braganca',       'value' => 'district-0400', 'dico' => '04'],
        ['name' => 'pages.notifications.casteloBranco',  'value' => 'district-0500', 'dico' => '05'],
        ['name' => 'pages.notifications.coimbra',        'value' => 'district-0600', 'dico' => '06'],
        ['name' => 'pages.notifications.evora',          'value' => 'district-0700', 'dico' => '07'],
        ['name' => 'pages.notifications.faro',           'value' => 'district-0800', 'dico' => '08'],
        ['name' => 'pages.notifications.guarda',         'value' => 'district-0900', 'dico' => '09'],
        ['name' => 'pages.notifications.leiria',         'value' => 'district-1000', 'dico' => '10'],
        ['name' => 'pages.notifications.lisboa',         'value' => 'district-1100', 'dico' => '11'],
        ['name' => 'pages.notifications.portalegre',     'value' => 'district-1200', 'dico' => '12'],
        ['name' => 'pages.notifications.porto',          'value' => 'district-1300', 'dico' => '13'],
        ['name' => 'pages.notifications.santarem',       'value' => 'district-1400', 'dico' => '14'],
        ['name' => 'pages.notifications.setubal',        'value' => 'district-1500', 'dico' => '15'],
        ['name' => 'pages.notifications.vianadoCastelo', 'value' => 'district-1600', 'dico' => '16'],
        ['name' => 'pages.notifications.vilaReal',       'value' => 'district-1700', 'dico' => '17'],
        ['name' => 'pages.notifications.viseu',          'value' => 'district-1800', 'dico' => '18'],
    ],
];
