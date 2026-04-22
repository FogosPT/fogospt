<?php

return [
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
        'riskIndexes' => [
            'title' => 'Índices de Risco de Incêndio',
            'items' => [
                'fwi' => '(FWI) Índice Meteorológico de Risco de Incêndio - Este é o índice final do sistema Canadiano, sendo calculado em função dos seus sub-índices ISI e BUI.',
                'fmc' => '(FFMC) Índice de Humidade dos Combustíveis Finos - Este índice classifica os combustíveis finos mortos, de secagem rápida, quanto ao seu conteúdo em humidade. Corresponde, assim, ao grau de inflamabilidade destes combustíveis, que se encontram à superfície do solo. O conteúdo de humidade destes combustíveis às 12 UTC de um determinado dia depende do conteúdo de humidade à mesma hora, do dia anterior, da precipitação (mm) ocorrida em 24 horas (12-12 UTC) e da temperatura (ºC) e da humidade relativa do ar (%) às 12 UTC do próprio dia. A intensidade do vento influencia apenas na velocidade de secagem destes materiais.',
                'isi' => '(ISI) Índice de Propagação Inicial -  Este índice de propagação inicial do fogo, depende do sub-índice FFMC e da intensidade do vento (Km/h) às 12 UTC.',
                'bui' => '(BUI) Índice de Combustível Disponível - O índice de combustível disponível é um fator de avaliação dos vegetais que podem alimentar um fogo (combustíveis "pesados" que se encontram no solo) e é calculado a partir de dois dos sub-índices: DMC e DC.',
                'dc' => '(DC) Índice de Húmus - Este índice traduz o conteúdo de humidade do húmus e materiais lenhosos de tamanho médio que se encontram abaixo da superfície do solo até cerca de 8 cm. O índice de húmus é calculado a partir da precipitação ocorrida em 24 horas (12-12 UTC), da temperatura e humidade relativa do ar às 12 UTC e do índice de húmus da véspera.',
                'dmc' => '(DMC) Índice de Seca - Este índice é um bom indicador dos efeitos da seca sazonal nos combustíveis florestais (húmus e materiais lenhosos de maiores dimensões), que se encontram abaixo da superfície do solo, entre 8 e 20 cm de profundidade. O índice de seca é obtido a partir da precipitação ocorrida em 24 horas, da temperatura às 12 UTC e do índice de seca verificado na véspera.'


            ],
            'source' => 'Informação retirada do IPMA.'
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
