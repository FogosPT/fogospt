<?php

return [
    'about' => [
        'entries_from' => 'Data retrieved from <a href="http://www.prociv.pt/">Portuguese Civil Protection webpage</a> (ANEPC - Portuguese Civil Protection)',
        'update_interval' => 'Updates every 2 minutes',
        'near_location' => 'Aproximate location.',
        'suggestion_bugs' => 'Suggestions / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
        'made_by' => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>'
    ],
    'information' => [
        'statesOfOccurrences' => [
            'title' => 'Status of Occurrences',
            'items' => [
                'firstAlertdispatch' => '1ST ALERT DISPATCH – Units in transit to the site.',
                'arrivalToOccurrence' => 'ARRIVAL TO SITE – Units have arrived on site.',
                'ongoing' => 'ONGOING - Ongoing fire with no area delimitation.',
                'inResolution' => 'IN RESOLUTION – Fire with no danger of spreading beyond the current perimeter.',
                'inConclusion' => 'IN CONCLUSION – Fire extinguished, with small combustion spots within the fire perimeter.',
                'surveillance' => 'SURVEILLANCE – Units on site to act in case of need.',
                'closed' => 'CLOSED – Return of all involved units to the base concluded.'
            ]
        ],
        'typeOfUnits' => [
            'title' => 'Units',
            'items' => [
                'humans' => 'HUMAN - Firefighters, FEPC, PSP (police), Armed Forces, Emergency Medical Services, Sapadores Florestais (forest fires & rescue services), GNR (gendarmery), GIPS Grupo Intervenção de Proteção e Socorro (emergency rescue services)',
                'terrestrial' => 'TERRESTRIAL - Road vehicles',
                'air' => 'AIR - Helicopters / Aircrafts'
            ],

        ],
        'numberDescription' => 'The number displayed match the total number of dispatched units. This number may differ from the units on site, as some of the dispatched units may still be in transit to the operational theater',
        'hoursDescription' => 'The time displayed, both on the units graph and on the fire status timeline, are the ones in which our system detected a change of data in the ANPEC website and it may not match the exact time that change occurred.',
        'source' => 'Fire risk data retrieved from IPMA (Portuguese Institute for Sea and Atmosphere).',
        'riskIndexes' => [
            'title' => 'Fire Risk Index',
            'items' => [
                'fwi' => '(FWI) Fire Weather Index - This is the final Canadian index system, calculated based on the sub-indexes ISI and BUI.',
                'fmc' => '(FFMC) Fine Fuel Moisture Code - This index classifies the fine fuel moisture regarding moisture percentage. It is related to the degree of flammability of the fuel located at surface level. The moisture percentage at 12 UTC for a given day, depends on the moisture percentage at the same time the precious day, 24 hours (12-12 UTC) precipitation (mm), temperature (ºC) and relative humidity (%) at 12 UTC that day. Wind speed affects only the drying speed of the material.',
                'isi' => '(ISI) Initial Spread Index -  This index classifies the inicial propagation of the fire. It depends on the sub-index FFMC and wind speed (Km/h) at 12 UTC.',
                'bui' => '(BUI) Build Up Index - The build up index, is a index of evaluation of the vegatation that is able to feed a fire ("heavy" fuel on the ground). It is calculated from two sub-indexes: DMC e DC.',
                'dc' => '(DC) Drought Code - This index translates de moisture percentage of the húmus and medium sized firewood materials located below surface up to 8 cm. It is calculated from the precipitation for 24 hours (12-12 UTC), temperature e relative humidity at 12 UTC and DC the previous day.',
                'dmc' => '(DMC) Duff Moisture Code - This index is a good index of the effects of seasonal drought on forest fuel (húmus and large sized firewood materials), located below surface, from 8 to 20 cm deep. The index is calculated from precipitation from the previous 24 hours, temperature at 12 UTC and drought code the previous day.'


            ],
            'source' => 'Information retrieved from IPMA. (Portuguese Institute for Sea and Atmosphere).'
        ]
    ],
    'notifications' => [
        'aveiro' => 'Aveiro',
        'beja' => 'Beja',
        'braga' => 'Braga',
        'braganca' => 'Bragança',
        'casteloBranco' => 'Castelo Branco',
        'coimbra' => 'Coimbra',
        'evora' => 'Évora',
        'faro' => 'Faro',
        'guarda' => 'Guarda',
        'leiria' => 'Leiria',
        'lisboa' => 'Lisboa',
        'portalegre' => 'Portalegre',
        'porto' => 'Porto',
        'santarem' => 'Santarém',
        'setubal' => 'Setúbal',
        'vianadoCastelo' => 'Viana do Castelo',
        'vilaReal' => 'Vila Real',
        'viseu' => 'Viseu',
        'madeira' => 'Madeira',

        'important' => 'Important events',
        'alerts' => 'Alerts',
    ],
    'stats' => [
        'now' => [
            'stats' => ':date - :total Fires ongoing* - :man humans, :cars cars e :aerial air.',
            'footer' => 'Fires with the status \'1ST ALERT DISPATCH\' or  \'ONGOING\''
        ],
        'now-text' => 'Now',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last-night' => 'Last Night',
        'last-night-footer' => 'Wildfires between 21pm and 09am',
        'last-days' => 'Last Days',
    ],
    'table' => [
        'reload' => 'This page auto refreshes.'
    ],
    'list' => [
        'no-data' => 'Sem registo de incêndios'
    ]
];
