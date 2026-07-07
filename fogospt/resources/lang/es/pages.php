<?php

return [
    'seo' => [
        'brand_suffix' => ' | Fogos.pt',
        'home' => [
            'title'       => 'Mapa de Incendios en Portugal en Tiempo Real',
            'h1'          => 'Mapa de Incendios Forestales en Portugal',
            'description' => 'Sigue en tiempo real los incendios en Portugal. Mapa con fuegos activos, medios desplegados, avisos meteorológicos y estadísticas oficiales de ANEPC, ICNF e IPMA.',
            'intro'       => 'Sigue en tiempo real los incendios forestales en Portugal continental y Madeira. Consulta fuegos activos, medios desplegados por bomberos y protección civil, avisos meteorológicos del IPMA y estadísticas oficiales de ANEPC e ICNF.',
            'menu_label'  => 'Inicio',
        ],
        'madeira' => [
            'title'       => 'Incendios en Madeira – Mapa en Vivo',
            'h1'          => 'Mapa de Incendios en Madeira',
            'description' => 'Sigue en tiempo real los incendios en la Región Autónoma de Madeira. Mapa con fuegos activos, medios desplegados y avisos meteorológicos.',
            'intro'       => 'Sigue en tiempo real los incendios forestales en la Región Autónoma de Madeira. Fuegos activos, medios desplegados y avisos oficiales.',
            'menu_label'  => 'Madeira',
        ],
        'list'        => [ 'title' => 'Lista de Incendios en Portugal',          'description' => 'Lista actualizada de todos los incendios registrados en Portugal continental.' ],
        'table'       => [ 'title' => 'Tabla de Incendios en Portugal',          'description' => 'Tabla detallada con estado, medios y ubicación de los incendios en Portugal.' ],
        'stats'       => [ 'title' => 'Estadísticas de Incendios en Portugal',   'description' => 'Estadísticas actualizadas: número de incendios, área quemada, causas y medios desplegados en Portugal.' ],
        'warnings'    => [ 'title' => 'Avisos Meteorológicos – Portugal',        'description' => 'Avisos meteorológicos del IPMA y alertas de protección civil en Portugal continental.' ],
        'warningsMadeira' => [ 'title' => 'Avisos Meteorológicos – Madeira',     'description' => 'Avisos meteorológicos del IPMA y alertas de protección civil para Madeira.' ],
        'otherFires'  => [ 'title' => 'Otros Incidentes – Portugal',             'description' => 'Otros incidentes registrados por la ANEPC: quemas controladas, gestión de combustible.' ],
        'about'       => [ 'title' => 'Sobre Fogos.pt',                          'description' => 'Sobre el proyecto Fogos.pt, operado por VOST Portugal. Fuentes de datos, socios y misión.' ],
        'information' => [ 'title' => 'Información sobre Incendios y Medios',    'description' => 'Significado de los estados de incendio, tipos de medios, índices de riesgo y capas del mapa.' ],
        'partnerships'=> [ 'title' => 'Socios de Fogos.pt',                      'description' => 'Socios institucionales y tecnológicos de Fogos.pt: ANEPC, AGIF, PTServidor, Cloudflare, Mapbox.' ],
        'api'         => [ 'title' => 'API Pública de Fogos.pt',                 'description' => 'Documentación de la API pública de Fogos.pt para datos de incendios en Portugal.' ],
        'apiTerms'    => [ 'title' => 'Términos de la API',                      'description' => 'Términos de uso de la API pública de Fogos.pt.' ],
        'notifications'=> [ 'title' => 'Notificaciones de Incendios',            'description' => 'Suscríbete a notificaciones de incendios en Portugal por distrito o incidente concreto.' ],
        'privacyPolicy'=> [ 'title' => 'Política de Privacidad',                 'description' => 'Política de privacidad de Fogos.pt.' ],
        'fire' => [
            'title'             => '[:date :hour] Incendio en :location (:concelho) — :status',
            'title_no_concelho' => '[:date :hour] Incendio en :location — :status',
            'description'       => '[:date :hour] Incendio en :location (:concelho). Estado: :status. :man efectivos, :terrain medios terrestres, :aerial medios aéreos.',
        ],
    ],

    'about' => [
        'entries_from' => 'Datos basados en registros de la ANEPC – Autoridad Nacional de Emergencia y Protección Civil.',
        'update_interval' => 'Actualizaciones frecuentes.',
        'near_location' => 'Ubicación aproximada.',
        'suggestion_bugs' => 'Sugerencias: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
        'made_by' => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>',

        'about_title' => 'Sobre Fogos.pt',
        'about_text' => 'Fogos.pt es una de las principales fuentes de información sobre incendios rurales en Portugal, ofreciendo datos casi en tiempo real.',
        'about_text_2' => 'La plataforma fue desarrollada originalmente por João Pina, y hoy es operada por VOST Portugal, que garantiza la integración, el tratamiento y la difusión de información al público y a entidades operacionales, bajo el liderazgo técnico de João Pina, también fundador de VOST Portugal.',
        'about_text_3' => 'Más que un agregador, Fogos.pt funciona como una capa de integración que transforma datos complejos en información clara, útil y accionable.',

        'data_title' => 'Origen y tratamiento de los datos',
        'data_intro' => 'La información resulta de la articulación de múltiples fuentes oficiales y tecnológicas, incluyendo:',
        'data_authorities_title' => 'Autoridades',
        'data_authorities_anepc' => 'ANEPC – Autoridad Nacional de Emergencia y Protección Civil',
        'data_authorities_icnf' => 'ICNF – Instituto para la Conservación de la Naturaleza y los Bosques',
        'data_authorities_agif' => 'AGIF – Agencia para la Gestión Integrada de Incendios Rurales',
        'data_satellites_title' => 'Satélites y tecnología',
        'data_satellites_text' => 'Copernicus, NASA, Meteosat y Mapbox',
        'data_other_title' => 'Otras fuentes complementarias',
        'data_other_text' => 'Waze y contribuciones OSINT de voluntarios de VOST Portugal',
        'data_aerial_title' => 'Medios aéreos (tracking en tiempo real)',
        'data_aerial_intro' => 'El tracking de los medios aéreos del DECIR usa tres fuentes complementarias de datos ADS-B:',
        'data_aerial_fr24' => '<strong><a href="https://www.flightradar24.com" target="_blank" rel="noopener">Flightradar24</a></strong> — agregador comercial líder del mercado, accedido vía suscripción de la API.',
        'data_aerial_alive' => '<strong><a href="https://airplanes.live" target="_blank" rel="noopener">airplanes.live</a></strong> — agregador comunitario de datos ADS-B.',
        'data_aerial_adsbfi' => '<strong><a href="https://adsb.fi" target="_blank" rel="noopener">adsb.fi</a></strong> — open data de receptores ADS-B voluntarios.',
        'data_aerial_disclaimer' => 'Las posiciones pueden tener un retraso de segundos a minutos. La cobertura depende de la red de receptores ADS-B — aeronaves a baja altitud o en zonas montañosas pueden quedar temporalmente sin señal.',
        'data_footer' => 'Todos los datos son integrados, procesados y validados por VOST Portugal, incluyendo procesos de verificación, normalización y contextualización operacional.',

        'partners_title' => 'Socios y apoyos',
        'partners_intro' => 'La continuidad del servicio está respaldada por:',
        'partners_anepc' => 'ANEPC — a través del protocolo de cooperación con VOST Portugal',
        'partners_pt_servidor' => 'PTServidor — infraestructura técnica (pro bono)',
        'partners_cloudflare' => 'Cloudflare — seguridad y resiliencia (Project Galileo)',
        'partners_mapbox' => 'Mapbox — visualización cartográfica',
        'partners_agif' => 'AGIF — marco institucional',

        'commitment_title' => 'Compromiso',
        'commitment_text' => 'Fogos.pt y VOST Portugal garantizan la operación continua de Fogos.pt, manteniendo altos estándares de transparencia, fiabilidad y utilidad pública, especialmente en contextos críticos.',
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
        'opacityOccurrences' => [
            'title' => 'Iconos con opacidad reducida',
            'description' => 'Los iconos mostrados con opacidad reducida corresponden a ocurrencias cuya naturaleza es:',
            'items' => [
                'fuelManagement' => 'Gestión de Combustible',
                'burning' => 'Quema',
                'burnPrevention' => 'Prevención de Quemas',
            ]
        ],
        'riskIndexes' => [
            'title' => 'Índices de riesgo de incendio',
            'items' => [
                'fwi' => '(FWI) Índice de riesgo de incendio meteorológico: este es el índice final del sistema canadiense, que se calcula de acuerdo con sus subíndices ISI y BUI.',
                'fmc' => '(FFMC) Índice de humedad de combustibles finos: este índice clasifica los combustibles finos y de secado rápido según su contenido de humedad. Esto corresponde al grado de inflamabilidad de estos combustibles, que se encuentran en la superficie del suelo. El contenido de humedad de estos combustibles a las 12 UTC de un día determinado, depende del contenido de humedad a la misma hora, el día anterior, precipitación (mm) en 24 horas (12-12 UTC) y temperatura (ºC) y temperatura relativa. humedad del aire (%) a las 12 UTC del mismo día. La intensidad del viento solo influye en la velocidad de secado de estos materiales.',
                'isi' => '(ISI) Índice de propagación inicial: este índice de propagación inicial del fuego depende del subíndice FFMC y de la intensidad del viento (km/h) a las 12 UTC.',
                'bui' => '(BUI) Índice de combustible disponible: el índice de combustible disponible es un factor de evaluación para las verduras que pueden alimentar un incendio (combustibles "pesados" que se encuentran en el suelo) y se calcula a partir de dos de los subíndices: DMC y DC.',
                'dc' => '(DC) Índice de humus: este índice refleja el contenido de humedad del humus y los materiales leñosos de tamaño mediano que se encuentran por debajo de la superficie del suelo hasta unos 8 cm. El índice de humus se calcula a partir de la precipitación ocurrida en 24 horas (12-12 UTC), la temperatura y humedad relativa del aire a las 12 UTC y el índice de humus del día anterior.',
                'dmc' => '(DMC) Índice de sequía: este índice es un buen indicador de los efectos de la sequía estacional sobre los combustibles forestales (humus y materiales leñosos más grandes), que se encuentran por debajo de la superficie del suelo, entre 8 y 20 cm de profundidad. El índice de sequía se obtiene de la precipitación ocurrida en 24 horas, la temperatura a las 12 UTC y el índice de sequía verificado el día anterior.'


            ],
            'source' => 'Información extraída de IPMA.'
        ],
        'ruralFireRisk' => [
            'title' => 'Peligro de Incendio Rural (RCM)',
            'intro' => 'El RCM (Riesgo Coyuntural y Meteorológico) combina el FWI con la carta de peligrosidad del territorio. <strong>No es el FWI</strong> — es un índice integrado, calculado por municipio, y es el indicador oficial usado en las alertas públicas.',
            'classesTitle' => 'El RCM se clasifica en cinco clases:',
            'classes' => [
                'reduced' => 'Reducido',
                'moderate' => 'Moderado',
                'high' => 'Elevado',
                'veryHigh' => 'Muy Elevado',
                'maximum' => 'Máximo',
            ],
            'legendNote' => 'Cuando esta capa está activa en el mapa, su leyenda de colores aparece en la esquina inferior izquierda.',
            'source' => 'Más información en <a href="https://www.ipma.pt/pt/riscoincendio/rcm.pt/" target="_blank" rel="noopener">IPMA — Peligro de Incendio Rural</a>.',
        ],
        'mapLayers' => [
            'title' => 'Capas adicionales del mapa',
            'intro' => 'Además de los iconos de incidencias, el mapa principal puede mostrar varias capas opcionales, activables desde el panel "Mapa" (arriba a la derecha). Significado de los iconos y de cada capa:',
            'hotspots' => [
                'title' => 'Hotspots de satélite (MODIS, VIIRS, IPMA FRP)',
                'description' => 'Puntos calientes detectados desde el espacio en las últimas horas. MODIS (Aqua/Terra) y VIIRS (Suomi NPP / NOAA-20) provienen de NASA FIRMS; "IPMA FRP" es el producto Fire Radiative Power de LSA-SAF, con refresco de 15 min. Un hotspot no confirma un incendio — cualquier anomalía térmica (incendio, volcán, fábrica, quema controlada) puede aparecer.',
            ],
            'lightning' => [
                'title' => 'Descargas eléctricas (tormentas)',
                'description' => 'Descargas detectadas por la red del IPMA en las últimas 24 horas. El color codifica la polaridad de la amplitud; el tamaño, el tipo de descarga.',
                'items' => [
                    'negative' => 'Descarga negativa (nube-tierra) — normalmente más energética y con mayor probabilidad de iniciar incendios.',
                    'positive' => 'Descarga positiva (nube-tierra).',
                    'intracloud' => 'Descarga entre nubes — dibujada más pequeña y con menor opacidad.',
                    'ageFade' => 'Las descargas más antiguas se atenúan (≤1h opaco, luego 1–6h, 6–12h, 12–24h progresivamente más transparentes).',
                ],
            ],
            'gaia' => [
                'title' => 'Eventos satélite (Gaia)',
                'description' => 'Disponible en la vista <a href="/es/gaia">/gaia</a>. Marcadores rojos = eventos activos detectados por satélite; grises = inactivos. Al hacer clic en un evento se carga su polígono actual; el botón "Ver evolução" abre una línea temporal animada con el perímetro histórico.',
            ],
            'windAnimated' => [
                'title' => 'Viento animado',
                'description' => 'Partículas que se desplazan por el campo de viento previsto por el modelo AROME del IPMA (componentes u/v a 10 m, hora actual). La intensidad local en km/h aparece en la esquina inferior derecha mientras el cursor está sobre el mapa.',
            ],
            'ipmaValue' => [
                'title' => 'Valor puntual de las capas IPMA',
                'description' => 'Con cualquier capa de previsión IPMA activa (temperatura, viento, humedad, precipitación, dirección del viento), al hacer clic o tocar el mapa se muestra el valor de la capa en ese punto en la esquina inferior derecha. La consulta usa el mismo modelo AROME que genera la capa.',
            ],
        ],
        'sources' => [
            'title' => 'Fuentes de datos',
            'intro' => 'La información de fogos.pt se agrega de varias fuentes oficiales, todas con créditos públicos:',
            'items' => [
                'anepc' => '<strong>ANEPC</strong> — Estado de las incidencias (despacho, en curso, en resolución, etc.), medios desplegados (operativos, terrestres, aéreos), ubicación y naturaleza del incidente. Recogido en tiempo real a través de nuestro backend interno.',
                'ipma' => '<strong>IPMA</strong> — Previsión meteorológica horaria (modelo AROME para temperatura, viento, humedad, precipitación), Peligro de Incendio Rural (RCM), índices canadienses (FWI/ISI/BUI/DC/DMC/FFMC, modelo ECMWF), Fire Radiative Power (LSA-SAF) y descargas eléctricas. <a href="https://www.ipma.pt" target="_blank" rel="noopener">ipma.pt</a>.',
                'nasa' => '<strong>NASA FIRMS</strong> — Hotspots de satélite MODIS (Aqua/Terra) y VIIRS (S-NPP, NOAA-20). <a href="https://firms.modaps.eosdis.nasa.gov" target="_blank" rel="noopener">firms.modaps.eosdis.nasa.gov</a>.',
                'gaia' => '<strong>Plataforma Gaia</strong> — Detección y delimitación de eventos de fuego por satélite, con polígonos actuales y línea temporal histórica del perímetro.',
                'basemaps' => '<strong>Mapas base</strong> — OpenStreetMap (CC-BY-SA), Esri World Imagery / Transportation / Boundaries (vista satélite), CARTO Positron (modo IPMA).',
            ],
        ],
        'ipmaCharts' => [
            'title' => 'Gráficos de previsión IPMA (página de detalle)',
            'intro' => 'Cada incidente tiene un panel con gráficos de previsión del IPMA para la ubicación exacta del fuego. Las variables meteorológicas horarias (próximas 48 h) se alimentan del modelo AROME. El FWI y los subíndices canadienses se calculan a las 12 UTC a partir del modelo ECMWF — no del AROME. Los productos LSA-SAF (diarios, 7 días) combinan observación por satélite con previsiones del ECMWF. La línea vertical roja a trazos marca la hora actual.',
            'items' => [
                'tempHum' => 'Temperatura y humedad — temperatura del aire a 2 m (°C, eje izquierdo) y humedad relativa del aire (%, eje derecho). Indicador clave de sequedad: humedad por debajo del 30% combinada con temperatura elevada acelera la propagación.',
                'wind' => 'Viento y ráfaga — intensidad media del viento a 10 m y ráfaga máxima (km/h). Las flechas en la parte superior indican la dirección: la punta de la flecha indica hacia dónde sopla el viento.',
                'pressure' => 'Presión atmosférica — presión al nivel del mar (hPa). Variaciones bruscas pueden indicar la aproximación y el paso de superficies frontales.',
                'precip' => 'Precipitación acumulada — precipitación prevista acumulada en una hora (mm). Útil para anticipar alivio o agravamiento sobre el terreno.',
                'fwiIsiBui' => 'FWI / ISI / BUI — índices canadienses de riesgo meteorológico, calculados a las 12 UTC a partir del modelo ECMWF. FWI es el índice final; ISI representa la propagación inicial; BUI el combustible disponible. Valores más altos = condiciones más peligrosas.',
                'dcDmcFfmc' => 'DC / DMC / FFMC — índices de humedad de los combustibles. DC mide combustibles profundos (sequía prolongada), DMC los intermedios, FFMC los finos en superficie (respuesta rápida al tiempo reciente).',
                'frm' => 'FRM — probabilidad de extremos (%) y anomalía (%) frente a la climatología. Señala condiciones atípicas para la época del año.',
            ],
            'rcmNote' => 'El RCM (Riesgo Coyuntural y Meteorológico) — equivalente al <strong>Peligro de Incendio Rural</strong> — no tiene gráfico propio en esta página; se muestra como capa del mapa principal, con su leyenda dedicada.',
            'source' => 'Fuente: IPMA (modelo AROME para las variables horarias, modelo ECMWF para los índices canadienses y productos LSA-SAF para los indicadores diarios). Datos actualizados en cada corrida del modelo (00 y 12 UTC).',
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
        'warnings' => 'Avisos (ANEPC / IPMA)',
        'planes' => 'Movimientos de medios aéreos',
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
    ],
    'list' => [
        'no-data'       => 'Sin registro de incendios',
        'search'        => 'Buscar:',
        'info'          => 'Mostrando _TOTAL_ incendios',
        'info-empty'    => 'Mostrando 0 incendios',
        'info-filtered' => '(filtrado de un total de _MAX_ incendios)',
        'zero-records'  => 'Ningún incendio encontrado',
    ]

];
