<?php

return [
    'seo' => [
        'brand_suffix' => ' | Fogos.pt',
        'home' => [
            'title'       => 'Mapa de Fogos e Incêndios em Portugal',
            'h1'          => 'Mapa de Incêndios e Fogos em Portugal',
            'description' => 'Acompanha em tempo real os incêndios em Portugal. Mapa com fogos ativos, meios mobilizados, avisos meteorológicos e estatísticas da ANEPC, ICNF e IPMA.',
            'intro'       => 'Acompanha em tempo real os incêndios florestais em Portugal continental e Madeira. Vê fogos ativos, meios mobilizados pelos bombeiros e proteção civil, avisos meteorológicos do IPMA e estatísticas oficiais da ANEPC e ICNF.',
            'menu_label'  => 'Início',
        ],
        'madeira' => [
            'title'       => 'Incêndios na Madeira — Mapa em Tempo Real',
            'h1'          => 'Mapa de Incêndios na Madeira',
            'description' => 'Acompanha em tempo real os incêndios na Região Autónoma da Madeira. Mapa com fogos ativos, meios mobilizados e avisos meteorológicos.',
            'intro'       => 'Acompanha em tempo real os incêndios florestais na Região Autónoma da Madeira. Fogos ativos, meios mobilizados e avisos oficiais.',
            'menu_label'  => 'Madeira',
        ],
        'list'        => [ 'title' => 'Lista de Incêndios em Portugal',         'description' => 'Lista atualizada de todos os incêndios registados em Portugal continental.' ],
        'table'       => [ 'title' => 'Tabela de Incêndios em Portugal',        'description' => 'Tabela detalhada com estado, meios e localização dos incêndios em Portugal.' ],
        'stats'       => [ 'title' => 'Estatísticas de Incêndios em Portugal',  'description' => 'Estatísticas atualizadas: número de incêndios, área ardida, causas e meios mobilizados em Portugal.' ],
        'warnings'    => [ 'title' => 'Avisos Meteorológicos — Portugal',       'description' => 'Avisos meteorológicos do IPMA e alertas de proteção civil em Portugal continental.' ],
        'warningsMadeira' => [ 'title' => 'Avisos Meteorológicos — Madeira',    'description' => 'Avisos meteorológicos do IPMA e alertas de proteção civil para a Madeira.' ],
        'otherFires'  => [ 'title' => 'Outras Ocorrências — Portugal',          'description' => 'Outras ocorrências registadas pela ANEPC: queimadas, queimas, gestão de combustível.' ],
        'about'       => [ 'title' => 'Sobre o Fogos.pt',                       'description' => 'Sobre o projeto Fogos.pt, operado pela VOST Portugal. Fontes de dados, parceiros e missão.' ],
        'information' => [ 'title' => 'Informação sobre Incêndios e Meios',     'description' => 'Significado dos estados de incêndio, tipos de meios, índices de risco e camadas do mapa.' ],
        'partnerships'=> [ 'title' => 'Parcerias do Fogos.pt',                  'description' => 'Parceiros institucionais e tecnológicos do Fogos.pt: ANEPC, AGIF, PTServidor, Cloudflare, Mapbox.' ],
        'api'         => [ 'title' => 'API Pública do Fogos.pt',                'description' => 'Documentação da API pública do Fogos.pt para dados de incêndios em Portugal.' ],
        'apiTerms'    => [ 'title' => 'Termos da API',                          'description' => 'Termos de utilização da API pública do Fogos.pt.' ],
        'notifications'=> [ 'title' => 'Notificações de Incêndios',             'description' => 'Subscreve notificações de incêndios em Portugal por distrito ou ocorrência específica.' ],
        'privacyPolicy'=> [ 'title' => 'Política de Privacidade',               'description' => 'Política de privacidade do Fogos.pt.' ],
        'fire' => [
            'title'       => 'Incêndio em :location (:concelho) — :status',
            'title_no_concelho' => 'Incêndio em :location — :status',
            'description' => 'Incêndio em :location, :concelho. Estado: :status. :man operacionais, :terrain meios terrestres, :aerial meios aéreos.',
        ],
    ],

    'about' => [
        'entries_from' => 'Registos com base em dados da ANEPC – Autoridade Nacional de Emergência e Proteção Civil.',
        'update_interval' => 'Atualizações frequentes.',
        'near_location' => 'Localização aproximada.',
        'suggestion_bugs' => 'Sugestões: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
        'made_by' => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>',

        'about_title' => 'Sobre o Fogos.pt',
        'about_text' => 'O Fogos.pt é uma das principais fontes de informação sobre incêndios rurais em Portugal, disponibilizando dados em tempo quase real.',
        'about_text_2' => 'A plataforma foi desenvolvida originalmente por João Pina, e hoje é operada pela VOST Portugal, que assegura a integração, tratamento e disponibilização da informação ao público e a entidades operacionais, com a liderança técnica de João Pina, também ele um fundador da VOST Portugal.',
        'about_text_3' => 'Mais do que um agregador, o Fogos.pt funciona como uma camada de integração que transforma dados complexos em informação clara, útil e acionável.',

        'data_title' => 'Origem e tratamento dos dados',
        'data_intro' => 'A informação resulta da articulação de múltiplas fontes oficiais e tecnológicas, incluindo:',
        'data_authorities_title' => 'Autoridades',
        'data_authorities_anepc' => 'ANEPC – Autoridade Nacional de Emergência e Proteção Civil',
        'data_authorities_icnf' => 'ICNF – Instituto da Conservação da Natureza e das Florestas',
        'data_authorities_agif' => 'AGIF – Agência para a Gestão Integrada de Fogos Rurais',
        'data_satellites_title' => 'Satélites e tecnologia',
        'data_satellites_text' => 'Copernicus, NASA, Meteosat e Mapbox',
        'data_other_title' => 'Outras fontes complementares',
        'data_other_text' => 'Waze e contributos OSINT por voluntários da VOST Portugal',
        'data_footer' => 'Todos os dados são integrados, processados e validados pela VOST Portugal, incluindo processos de verificação, normalização e contextualização operacional.',

        'partners_title' => 'Parceiros e apoios',
        'partners_intro' => 'A continuidade do serviço é suportada por:',
        'partners_anepc' => 'ANEPC — através do protocolo de cooperação com a VOST Portugal',
        'partners_pt_servidor' => 'PTServidor — infraestrutura técnica (pro bono)',
        'partners_cloudflare' => 'Cloudflare — segurança e resiliência (Project Galileo)',
        'partners_mapbox' => 'Mapbox — visualização cartográfica',
        'partners_agif' => 'AGIF — enquadramento institucional',

        'commitment_title' => 'Compromisso',
        'commitment_text' => 'O Fogos.pt e a VOST Portugal asseguram a operação contínua do Fogos.pt, mantendo elevados padrões de transparência, fiabilidade e utilidade pública, especialmente em contextos críticos.',
    ],
    'information' => [
        'statesOfOccurrences' => [
            'title' => 'Estados das Ocorrências',
            'items' => [
                'firstAlertdispatch' => 'Despacho de 1º alerta – Meios em trânsito para o teatro de operações.',
                'arrivalToOccurrence' => 'Chegada ao TO – chegada ao teatro de operações.',
                'ongoing' => 'Em curso - Incêndio em evolução sem limitação de área',
                'inResolution' => 'Em resolução – Incêndio sem perigo de propagação para além do perímetro já atingido',
                'inConclusion' => 'Em conclusão – Incêndio extinto, com pequenos focos de combustão dentro do perímetro do incêndio',
                'surveillance' => 'Vigilância – Meios no local para atuar em caso de necessidade',
                'closed' => 'Encerrada – Entrada, nas respetivas entidades, de todos os meios envolvidos​',
                'fake' => 'Falso alerta',
                'false' => 'Falso alarme'
            ]
        ],
        'typeOfUnits' => [
            'title' => 'Meios',
            'items' => [
                'humans' => 'Operacionais - Bombeiros, Força Especial de Bombeiros, PSP, Forças Armadas, INEM, Equipas Sapadores
                    Florestais, GNR, GIPS Grupo Intervenção de Proteção e Socorro',
                'terrestrial' => 'TERRESTRES - Veículos rodoviários',
                'air' => 'AÉREOS - Helicópteros / Aviões'
            ],

        ],
        'numberDescription' => 'Os números disponibilizados são os totais de meios acionados. O número pode diferir dos meios que se encontram
            no teatro de operações (TO), uma vez que os meios acionados podem ainda estar em trânsito para o mesmo',
        'hoursDescription' => 'As horas indicadas, tanto no gráfico de meios como na linha de tempo, dos estados dos incêndios, indicam a
                hora em que o nosso sistema detetou uma mudança de dados por parte da ANEPC, podendo não corresponder ao
                momento exato em que essa alteração ocorreu.',
        'source' => 'Risco de incêndio recolhido via IPMA.',
        'opacityOccurrences' => [
            'title' => 'Ícones com opacidade reduzida',
            'description' => 'Os ícones apresentados com opacidade reduzida correspondem a ocorrências cuja natureza é:',
            'items' => [
                'fuelManagement' => 'Gestão de Combustível',
                'burning' => 'Queima',
                'burnPrevention' => 'Prevenção a Queimadas',
            ]
        ],
        'riskIndexes' => [
            'title' => 'Índices de Perigo de Incêndio',
            'items' => [
                'fwi' => '(FWI) Índice Meteorológico de Perigo de Incêndio - Este é o índice final do sistema Canadiano, sendo calculado em função dos seus sub-índices ISI e BUI.',
                'fmc' => '(FFMC) Índice de Humidade dos Combustíveis Finos - Este índice classifica os combustíveis finos mortos, de secagem rápida, quanto ao seu conteúdo em humidade. Corresponde, assim, ao grau de inflamabilidade destes combustíveis, que se encontram à superfície do solo. O conteúdo de humidade destes combustíveis às 12 UTC de um determinado dia depende do conteúdo de humidade à mesma hora, do dia anterior, da precipitação (mm) ocorrida em 24 horas (12-12 UTC) e da temperatura (ºC) e da humidade relativa do ar (%) às 12 UTC do próprio dia. A intensidade do vento influencia apenas na velocidade de secagem destes materiais.',
                'isi' => '(ISI) Índice de Propagação Inicial -  Este índice de propagação inicial do fogo, depende do sub-índice FFMC e da intensidade do vento (km/h) às 12 UTC.',
                'bui' => '(BUI) Índice de Combustível Disponível - O índice de combustível disponível é um fator de avaliação dos vegetais que podem alimentar um fogo (combustíveis "pesados" que se encontram no solo) e é calculado a partir de dois dos sub-índices: DMC e DC.',
                'dc' => '(DC) Índice de Húmus - Este índice traduz o conteúdo de humidade do húmus e materiais lenhosos de tamanho médio que se encontram abaixo da superfície do solo até cerca de 8 cm. O índice de húmus é calculado a partir da precipitação ocorrida em 24 horas (12-12 UTC), da temperatura e humidade relativa do ar às 12 UTC e do índice de húmus da véspera.',
                'dmc' => '(DMC) Índice de Seca - Este índice é um bom indicador dos efeitos da seca sazonal nos combustíveis florestais (húmus e materiais lenhosos de maiores dimensões), que se encontram abaixo da superfície do solo, entre 8 e 20 cm de profundidade. O índice de seca é obtido a partir da precipitação ocorrida em 24 horas, da temperatura às 12 UTC e do índice de seca verificado na véspera.'


            ],
            'source' => 'Informação retirada do IPMA.'
        ],
        'ruralFireRisk' => [
            'title' => 'Perigo de Incêndio Rural (RCM)',
            'intro' => 'O Risco Conjuntural e Meteorológico (RCM) resulta da combinação do FWI com a carta de perigosidade do território. <strong>Não é o FWI</strong> — é um índice integrado, calculado por concelho, e é o indicador oficial usado em alertas públicos.',
            'classesTitle' => 'O RCM é classificado em cinco classes:',
            'classes' => [
                'reduced' => 'Reduzido',
                'moderate' => 'Moderado',
                'high' => 'Elevado',
                'veryHigh' => 'Muito Elevado',
                'maximum' => 'Máximo',
            ],
            'legendNote' => 'Sempre que esta camada esteja activa no mapa, a respectiva legenda de cores aparece no canto inferior esquerdo.',
            'source' => 'Mais informação no <a href="https://www.ipma.pt/pt/riscoincendio/rcm.pt/" target="_blank" rel="noopener">IPMA — Perigo de Incêndio Rural</a>.',
        ],
        'mapLayers' => [
            'title' => 'Camadas adicionais do mapa',
            'intro' => 'Para além dos ícones de ocorrências, o mapa principal pode mostrar várias camadas opcionais que podem ser ligadas no painel "Mapa" (topo direito). O significado dos ícones e o que cada camada mostra:',
            'hotspots' => [
                'title' => 'Hotspots de satélite (MODIS, VIIRS, IPMA FRP)',
                'description' => 'Pontos quentes detectados por satélite nas últimas horas. MODIS (Aqua/Terra) e VIIRS (Suomi NPP / NOAA-20) são feeds da NASA FIRMS; "IPMA FRP" é o produto Fire Radiative Power da LSA-SAF, em refresh 15 min. Não é uma confirmação de incêndio — qualquer fonte de calor (fogo, vulcão, fábrica, queimadas) pode aparecer. Útil para confirmar fogos activos visíveis do espaço, especialmente em regiões com poucas ocorrências reportadas.',
            ],
            'lightning' => [
                'title' => 'Descargas elétricas (trovoadas)',
                'description' => 'Descargas detectadas pela rede do IPMA nas últimas 24 horas. A cor indica a polaridade da amplitude; o tamanho indica o tipo de descarga.',
                'items' => [
                    'negative' => 'Descarga negativa (nuvem-solo) — tipicamente mais energética e mais perigosa para ignição de fogos.',
                    'positive' => 'Descarga positiva (nuvem-solo).',
                    'intracloud' => 'Descarga entre nuvens — desenhada com círculo mais pequeno e mais transparente.',
                    'ageFade' => 'Quanto mais antiga a descarga, mais transparente o ícone (≤1h opaco, 1–6h, 6–12h, 12–24h cada vez mais ténue).',
                ],
            ],
            'gaia' => [
                'title' => 'Eventos satélite (Gaia)',
                'description' => 'Disponível na vista <a href="/pt/gaia">/gaia</a>. Pontos vermelhos = eventos activos detectados por satélite; cinzentos = inactivos. Clicar num evento carrega o polígono actual (footprint); o botão "Ver evolução" abre uma timeline animada com a evolução histórica do perímetro.',
            ],
            'windAnimated' => [
                'title' => 'Vento animado',
                'description' => 'Partículas que se deslocam segundo o campo de vento previsto pelo modelo AROME do IPMA (componentes u/v a 10 m, hora actual). A intensidade local aparece no canto inferior direito quando o cursor está sobre o mapa, em km/h.',
            ],
            'ipmaValue' => [
                'title' => 'Valor pontual das camadas IPMA',
                'description' => 'Com qualquer camada de previsão IPMA ligada (temperatura, vento, humidade, precipitação, direcção do vento), clicar (ou tocar) num ponto do mapa mostra o valor da camada nesse local no canto inferior direito. A consulta é feita ao mesmo modelo AROME usado para gerar a camada.',
            ],
        ],
        'sources' => [
            'title' => 'Fontes de dados',
            'intro' => 'A informação apresentada no fogos.pt é agregada de várias fontes oficiais, todas com créditos públicos:',
            'items' => [
                'anepc' => '<strong>ANEPC</strong> — Estado das ocorrências (despacho, em curso, em resolução, etc.), meios despachados (operacionais, terrestres, aéreos), localização e natureza do incidente. Recolhido em tempo real através do nosso backend interno.',
                'ipma' => '<strong>IPMA</strong> — Previsão meteorológica horária (modelo AROME para temperatura, vento, humidade, precipitação), Perigo de Incêndio Rural (RCM), índices Canadianos (FWI/ISI/BUI/DC/DMC/FFMC, modelo ECMWF), Fire Radiative Power (LSA-SAF) e descargas elétricas. <a href="https://www.ipma.pt" target="_blank" rel="noopener">ipma.pt</a>.',
                'nasa' => '<strong>NASA FIRMS</strong> — Hotspots de satélite MODIS (Aqua/Terra) e VIIRS (S-NPP, NOAA-20). <a href="https://firms.modaps.eosdis.nasa.gov" target="_blank" rel="noopener">firms.modaps.eosdis.nasa.gov</a>.',
                'gaia' => '<strong>Plataforma Gaia</strong> — Detecção e delineação de eventos de fogo a partir de satélite, com polígonos actuais e timeline histórica do perímetro.',
                'basemaps' => '<strong>Mapas base</strong> — OpenStreetMap (CC-BY-SA), Esri World Imagery / Transportation / Boundaries (vista satélite), CARTO Positron (modo IPMA).',
            ],
        ],
        'ipmaCharts' => [
            'title' => 'Gráficos de Previsão IPMA (página de detalhe)',
            'intro' => 'Cada incidente tem uma secção com gráficos de previsão do IPMA para a localização exacta do fogo. As variáveis meteorológicas horárias (próximas 48 h) são alimentadas pelo modelo AROME. O FWI e os sub-índices Canadianos são calculados para as 12 UTC com base no modelo ECMWF — e não no AROME. Os produtos LSA-SAF (diários, 7 dias) combinam observação por satélite com previsões do ECMWF. A linha vertical a tracejado vermelho marca a hora actual.',
            'items' => [
                'tempHum' => 'Temperatura e humidade — temperatura do ar a 2 m (°C, eixo esquerdo) e humidade relativa do ar (%, eixo direito). Indicador-chave da secura: humidade abaixo de 30% combinada com temperatura elevada acelera muito a propagação.',
                'wind' => 'Vento e rajada — intensidade média do vento a 10 m e rajada máxima (km/h). As setas no topo indicam a direcção: a ponta da seta indica para onde o vento sopra.',
                'pressure' => 'Pressão atmosférica — pressão reduzida ao nível do mar (hPa). Variações bruscas podem indicar aproximação e passagem de superfícies frontais.',
                'precip' => 'Precipitação acumulada — precipitação prevista acumulada numa hora (mm). Útil para perceber alívio ou agravamento das condições no terreno.',
                'fwiIsiBui' => 'FWI / ISI / BUI — índices Canadianos de risco meteorológico, calculados para as 12 UTC a partir do modelo ECMWF. FWI é o índice final; ISI representa a propagação inicial; BUI o combustível disponível. Valores mais altos = condições mais perigosas.',
                'dcDmcFfmc' => 'DC / DMC / FFMC — índices de humidade dos combustíveis. DC mede combustíveis profundos (seca prolongada), DMC os intermédios, FFMC os finos à superfície (resposta rápida ao tempo recente).',
                'frm' => 'FRM — probabilidade de extremos (%) e anomalia (%) face à climatologia. Sinaliza condições atípicas para a época do ano.',
            ],
            'rcmNote' => 'O RCM (Índice Conjuntural e Meteorológico) — que corresponde ao <strong>Perigo de Incêndio Rural</strong> — não tem gráfico próprio nesta página; é apresentado como camada do mapa principal, com legenda dedicada.',
            'source' => 'Fonte: IPMA (modelo AROME para as variáveis horárias, modelo ECMWF para os índices Canadianos, e produtos LSA-SAF para os indicadores diários). Dados atualizados a cada corrida do modelo (00 e 12 UTC).',
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

        'important' => 'Ocorrências importantes',
        'alerts' => 'Alertas',
        'warnings' => 'Avisos (ANEPC / IPMA)',
        'planes' => 'Movimentos de meios aéreos',
    ],
    'stats' => [
        'now' => [
            'stats' => ':date - :total Incêndios em curso* combatidos por :man operacionais, :cars meios terrestres e :aerial meios aéreos.',
            'footer' => 'Incêndios no estado \'Despacho de 1º Alerta\' ou no estado \'Em Curso\''
        ],
        'now-text' => 'Agora',
        'today' => 'Hoje',
        'yesterday' => 'Ontem',
        'last-night' => 'Última noite',
        'last-days' => 'Últimos dias',
        'last-night-footer' => 'Incêndios entre as 21h e as 09h',
        'burn-area-last-days' => 'Área ardida nos últimos dias',
        'burn-area-last-days-footer' => 'Dados registados no ICNF, nem todos os incêndios têm estes dados. Em hectares.',
        'motives' => [
            'title' => 'Causas de incêndio no mês corrente',
            'footer' => 'Dados registados no ICNF, nem todos os incêndios têm estes dados.'
        ]
    ],
    'table' => [
        'reload' => 'Esta página é atualizada automaticamente.'
    ],
    'list' => [
        'no-data' => 'Sem registo de incêndios'
    ]

];
