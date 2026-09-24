<?php
return [
    'cards' => [
        'general' => [
            'place' => 'Location',
            'start_at' => 'Start',
            'nature' => 'Nature',
            'location' => 'Location',
            'fireRisk' => 'Fire Risk',
            'updated' => 'Last update',
            'district' => 'District',
            'concelho' => 'Municipality/County',
            'freguesia' => 'Parish',
            'localidade' => 'Locality',
            'cb' => 'Firefighters',
            'alertFrom' => 'Alert source'

        ],
        'resources' => [
            'units' => 'Means',
        ],
        'status' => [
            'status' => 'Status'
        ]
        ,
        'meteo' => [
            'title' => 'Meteo',
            'temp_atual' => 'Now',
            'temp_min' => 'Min',
            'temp_max' => 'Max',
            'estado_atual' => 'Now',
            'humidity' => 'Humidity',
            'pressure' => 'Atmospheric pressure',
            'wind' => [
                'speed' => 'Wind Speed',
                'deg' => 'Wind direction'
            ],
            'precipitation' => 'Accumulated precipitation',
            'radiation'     => 'Radiation',
            'station'       => 'Station',
            'data_from'     => 'Data from',
            'source'        => 'Source',
        ],
        'extra' => [
            'title' => 'More info'
        ],
        'weatherWarnings' => [
            'title' => 'Weather warnings',
            'source' => 'Source: IPMA',
        ],
        'shares' => [
            'title' => 'Share'
        ],
        'photos' => [
            'title' => 'Photos',
            'loadMore' => 'Load more',
        ],
        'ipmaCharts' => [
            'title'  => 'IPMA forecast at this point',
            'source' => 'Data:',
            'error'  => 'Could not load IPMA data for this location.',
            'learnMore' => 'What do these charts mean?',
            'run' => 'Model run: :time (local time)',
        ],
        'satellite' => [
            'title'              => 'Perimeter and spread (satellite)',
            'legend'             => 'MTG satellite evidence (LSA SAF/EUMETSAT). Complements — does not replace — official ANEPC data.',
            'empty'              => 'No satellite data available for this incident.',
            'noPerimeter'        => 'No perimeter available for this incident: :message',
            'lastUpdate'         => 'Satellite',
            'stale'              => 'Data from :mins min ago',
            'affectedConcelhos'  => 'Affected municipalities',
            'affectedFreguesias' => 'Affected parishes',
            'none'               => '—',
        ],
        'detail' => [
            'burn' => [
                'title' => 'Burned area'
            ],
            'cause' => [
                'title' => 'Causa'
            ]
        ]
    ],
    'riskLevels' => [
        'Máximo' => 'Max',
        'Muito Elevado' => 'Very high',
        'Elevado' => 'High',
        'Moderado' => 'Moderate',
        'Reduzido' => 'Low'
    ]
];
