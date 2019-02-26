<?php

return [
    'about' => [
        'entries_from' => 'Registos retirados da <a href="http://www.prociv.pt/">Página da Protecção Civil Portuguesa</a>',
        'update_interval' => 'Actualizações de 2 em 2 minutos',
        'near_location' => 'Localização aproximada.',
        'suggestion_bugs' => 'Sugestões / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
        'made_by' => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>'
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
                'surveillance' => 'Vigilância – Meios no local para actuar em caso de necessidade',
                'closed' => 'Encerrada – Entrada, nas respectivas entidades, de todos os meios envolvidos​',
                'fake' => 'Falso alerta',
                'false' => 'Falso alarme'
            ]
        ],
        'typeOfUnits' => [
            'title' => 'Meios',
            'items' => [
                'humans' => 'HUMANOS - Bombeiros, Força Especial de Bombeiros, PSP, Forças Armadas, INEM, Equipas Sapadores
                    Florestais, GNR, GIPS Grupo Intervenção de Proteção e Socorro',
                'terrestrial' => 'TERRESTRES - Veículos rodoviários',
                'air' => 'AEREOS - Helicópteros / Aviões'
            ],

        ],
        'numberDescription' => 'Os números disponibilizados são os totais de meios accionados. O número pode diferir do que se encontra
                no terreno, uma vez que os meios accionados podem ainda estar em trânsito.',
        'hoursDescription' => 'As horas indicadas tanto no gráfico de meios como na linha do tempo dos estados do incêndios, são as
                horas que o nosso sistema detetou uma mudança de dados por parte da ANPC podendo não corresponder ao
                momento exato em que essa alteração ocorreu.',
        'source' => 'Risco de incêndio recolhido do IPMA.',
        'riskIndexes' => [
            'title' => 'Índices de Risco de Incêndio',
            'items' => [
                'fwi' => '(FWI) Índice Meteorológico de Risco de Incêndio - Este é o índice final do sistema Canadiano, sendo calculado em função dos seus sub-índices ISI e BUI.',
                'fmc' => '(FFMC) Índice de Humidade dos Combustíveis Finos - Este índice, classifica os combustíveis finos mortos, de secagem rápida, quanto ao seu conteúdo em humidade. Corresponde assim ao grau de inflamabilidade destes combustíveis, que se encontram à superfície do solo. O conteúdo de humidade destes combustíveis às 12 UTC de um determinado dia, depende do conteúdo de humidade à mesma hora, do dia anterior, da precipitação (mm) ocorrida em 24 horas (12-12 UTC) e da temperatura (ºC) e da humidade relativa do ar (%) às 12 UTC do próprio dia. A intensidade do vento influência apenas na velocidade de secagem destes materiais.',
                'isi' => '(ISI) Índice de Propagação Inicial -  Este índice de propagação inicial do fogo, depende do sub-índice FFMC e da intensidade do vento (Km/h) às 12 UTC.',
                'bui' => '(BUI) Índice de Combustível Disponível - O índice de combustível disponível, é um factor de avaliação dos vegetais que podem alimentar um fogo (combustíveis "pesados" que se encontram no solo) e é calculado a partir de dois dos sub-índices: DMC e DC.',
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
            'stats' => ':date - :total Incêndios em curso* combatidos por :man meios humanos, :cars meios terrestres e :aerial meios aereos.',
            'footer' => 'Incêndios no estado \'Despacho de 1º Alerta\' ou no estado \'Em Curso\''
        ],
        'now-text' => 'Agora',
        'today' => 'Hoje',
        'yesterday' => 'Ontem',
        'last-night' => 'Última noite',
        'last-days' => 'Últimos dias',
        'last-night-footer' => 'Incêndios entre as 21h e as 09h',
    ]

];
