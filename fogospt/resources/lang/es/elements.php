<?php
    return [
        'cards' => [
            'general' => [
                'place' => 'Local',
                'start_at' => 'Comienzo',
                'nature' => 'Naturaleza',
                'location' => 'Localización',
                'fireRisk' => 'Riesgo de Incendio',
                'updated' => 'Última actualización',
                'district' => 'Distrito',
                'concelho' => 'Condado',
                'freguesia' => 'Parroquia',
                'localidade' => 'Localidad'
            ],
            'resources' => [
                'units' => 'Medios',
            ],
            'status' => [
                'status' => 'Estado'
            ],
            'meteo' => [
                'title' => 'Meteo',
                'temp_atual' => 'Temperatura actual',
                'temp_min' => 'Temperatura mínima',
                'temp_max' => 'Temperatura máxima',
                'estado_atual' => 'Tiempo',
                'humidity' => 'Humedad',
                'pressure' => 'Presión atmosférica',
                'wind' => [
                    'speed' => 'Velocidad del Viento',
                    'deg' => 'Dirección del Viento'
                ],
                'precipitation' => 'Precipitación acumulada',
                'radiation'     => 'Radiación',
                'station'       => 'Estación',
                'data_from'     => 'Datos de',
                'source'        => 'Fuente',
            ],
            'extra' => [
                'title' => 'Información extra'
            ],
            'shares' => [
                'title' => 'Compartir'
            ],
            'photos' => [
                'title' => 'Fotos',
                'loadMore' => 'Cargar más',
            ],
            'ipmaCharts' => [
                'title'  => 'Previsión IPMA en este punto',
                'source' => 'Datos:',
                'error'  => 'No se pudieron obtener datos de IPMA para esta ubicación.',
                'learnMore' => '¿Qué significan estos gráficos?',
                'run' => 'Corrida del modelo: :time (hora local)',
            ],
            'satellite' => [
                'title'              => 'Perímetro y propagación (satélite)',
                'legend'             => 'Evidencia de satélite MTG (LSA SAF/EUMETSAT). Complementa — no sustituye — la información oficial de la ANEPC.',
                'empty'              => 'Sin datos satélite disponibles para esta incidencia.',
                'noPerimeter'        => 'Sin perímetro posible para esta incidencia: :message',
                'lastUpdate'         => 'Satélite',
                'stale'              => 'Datos de hace :mins min',
                'affectedConcelhos'  => 'Municipios afectados',
                'affectedFreguesias' => 'Parroquias afectadas',
                'none'               => '—',
            ]
        ],
        'riskLevels' => [
            'Máximo' => 'Máximo',
            'Muito Elevado' => 'Muy Elevado',
            'Elevado' => 'Elevado',
            'Moderado' => 'Moderado',
            'Reduzido' => 'Reducido'
        ]
    ];
