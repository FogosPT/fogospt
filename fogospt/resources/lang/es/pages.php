<?php

return [
    'about' => [
        'entries_from' => 'Datos de la <a href="http://www.prociv.pt/">ANEPC - Proteccion Civil Portuguesa/a>',
        'update_interval' => 'Actualizaciones de 2 em 2 minutos',
        'near_location' => 'Ubicacion aproximada.',
        'suggestion_bugs' => 'Sugerencias / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
        'made_by' => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>'
    ],
    'information' => [
        'statesOfOccurrences' => [
            'title' => 'Estados de Ocurrencia',
            'items' => [
                'firstAlertdispatch' => 'Envío de 1ª alerta - Medios en tránsito al teatro de operaciones.',
                'arrivalToOccurrence' => 'Llegada a TO - llegada al teatro de operaciones.',
                'ongoing' => 'En curso -  fuego en curso sin limitación de área',
                'inResolution' => 'En resolución - Fuego sin extender el peligro más allá del perímetro ya alcanzado',
                'inConclusion' => 'En conclusión - Incendio extinguido, con pequeños fuegos de combustión dentro del perímetro del incendio',
                'surveillance' => 'Vigilancia - Medios in situ para actuar en caso de necesidad',
                'closed' => 'Cerrado - Entrada, en las respectivas entidades, de todos los medios involucrados​',
                'fake' => 'Falso alerta',
                'false' => 'Falso alarma'
            ]
        ],
        'typeOfUnits' => [
            'title' => 'Medios',
            'items' => [
                'humans' => 'HUMANOS - Bomberos, Fuerza Especial de Bomberos, PSP, Fuerzas Armadas, INEM, Equipos de zapadores
                     Grupo de intervención de protección y socorro forestal, GNR, GIPS',
                'terrestrial' => 'TERRESTRES - Vehículos de combate en carretera',
                'air' => 'AAEREOS - Helicópteros / Aviones'
            ],

        ],
        'numberDescription' => 'Las cifras proporcionadas son el número total de recursos utilizados. El número puede diferir de los medios
                 en el teatro de operaciones (TO), ya que los medios activados pueden estar todavía en tránsito hacia el mismo',
        'hoursDescription' => 'Los tiempos indicados, tanto en el gráfico de medias como en el cronograma, de los estados de los incendios, indican la
                 momento en el que nuestro sistema detectó un cambio en los datos por parte de ANPEC y puede no corresponder al
                 el momento exacto en que se produjo este cambio.',
        'source' => 'Riesgo de incendio recopilado a través de IPMA.',
        'riskIndexes' => [
            'title' => 'Índices de riesgo de incendio',
            'items' => [
                'fwi' => '(FWI) Índice de riesgo de incendio meteorológico: este es el índice final del sistema canadiense, que se calcula de acuerdo con sus subíndices ISI y BUI.',
                'fmc' => '(FFMC) Índice de humedad de combustibles finos: este índice clasifica los combustibles finos y de secado rápido según su contenido de humedad. Esto corresponde al grado de inflamabilidad de estos combustibles, que se encuentran en la superficie del suelo. El contenido de humedad de estos combustibles a las 12 UTC de un día determinado, depende del contenido de humedad a la misma hora, el día anterior, precipitación (mm) en 24 horas (12-12 UTC) y temperatura (ºC) y temperatura relativa. humedad del aire (%) a las 12 UTC del mismo día. La intensidad del viento solo influye en la velocidad de secado de estos materiales.',
                'isi' => '(ISI) Índice de propagación inicial: este índice de propagación inicial del fuego depende del subíndice FFMC y de la intensidad del viento (Km / h) a las 12 UTC.',
                'bui' => '(BUI) Índice de combustible disponible: el índice de combustible disponible es un factor de evaluación para las verduras que pueden alimentar un incendio (combustibles "pesados" que se encuentran en el suelo) y se calcula a partir de dos de los subíndices: DMC y DC.',
                'dc' => '(DC) Índice de humus: este índice refleja el contenido de humedad del humus y los materiales leñosos de tamaño mediano que se encuentran por debajo de la superficie del suelo hasta unos 8 cm. El índice de humus se calcula a partir de la precipitación ocurrida en 24 horas (12-12 UTC), la temperatura y humedad relativa del aire a las 12 UTC y el índice de humus del día anterior.',
                'dmc' => '(DMC) Índice de sequía: este índice es un buen indicador de los efectos de la sequía estacional sobre los combustibles forestales (humus y materiales leñosos más grandes), que se encuentran por debajo de la superficie del suelo, entre 8 y 20 cm de profundidad. El índice de sequía se obtiene de la precipitación ocurrida en 24 horas, la temperatura a las 12 UTC y el índice de sequía verificado el día anterior.'


            ],
            'source' => 'Información extraída de IPMA.'
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

        'important' => 'Eventos importantes',
        'alerts' => 'Alertas',
    ],
    'stats' => [
        'now' => [
            'stats' => ':date - :total de incendios en curso * combatidos por :man medios humanos, :cars medios terrestres e :aerial medios aereos.',
            'footer' => 'Incencios en el estado \'Despacho de 1º Alerta\' ou en estado \'Em Curso\''
        ],
        'now-text' => 'Ahora',
        'today' => 'Hoy',
        'yesterday' => 'Ayer',
        'last-night' => 'Última noche',
        'last-days' => 'Últimos dias',
        'last-night-footer' => 'Incendios entre las 21h e las 09h',
    ],
    'table' => [
        'reload' => 'esta página se actualiza automáticamente.'
    ]

];
