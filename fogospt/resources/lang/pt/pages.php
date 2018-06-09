<?php

return [
	'about'       => [
		'entries_from'    => 'Registos retirados da <a href="http://www.prociv.pt/">Página da Protecção Civil Portuguesa</a>',
		'update_interval' => 'Actualizações de 10 em 10 minutos',
		'near_location'   => 'Localização aproximada.',
		'suggestion_bugs' => 'Sugestões / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
		'made_by'         => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>'
	],
	'information' => [
		'statesOfOccurrences' => [
			'title' => 'Estados das Ocorrências',
			'items' => [
				'firstAlertdispatch'  => 'DESPACHO 1º ALERTA – Meios em trânsito para o teatro de operações.',
				'arrivalToOccurrence' => 'CHEGADA AO TO – chega ao teatro de operações.',
				'ongoing'             => 'EM CURSO - Incêndio em evolução sem limitação de área',
				'inResolution'        => 'EM RESOLUÇÃO – Incêndio sem perigo de propagação para além do perímetro já atingido',
				'inConclusion'        => 'EM CONCLUSÃO – Incêndio extinto, com pequenos focos de combustão dentro do perímetro do incêndio',
				'surveillance'        => 'VIGILÂNCIA – Meios no local para actuar em caso de necessidade',
				'closed'              => 'ENCERRADA – Entrada, nas respectivas entidades, de todos os meios envolvidos'
			]
		],
		'typeOfUnits'         => [
			'title' => 'Meios',
			'items' => [
				'humans'      => 'HUMANOS - Bombeiros, Força Especial de Bombeiros, PSP, Forças Armadas, INEM, Equipas Sapadores
                    Florestais, GNR, GIPS Grupo Intervenção de Proteção e Socorro',
				'terrestrial' => 'TERRESTRES - Veículos rodoviários',
				'air'         => 'AEREOS - Helicópteros / Aviões'
			],

		],
		'numberDescription'   => 'Os números disponibilizados são os totais de meios accionados. O número pode diferir do que se encontra
                no terreno, uma vez que os meios accionados podem ainda estar em trânsito.',
		'hoursDescription'    => 'As horas indicadas tanto no gráfico de meios como na linha do tempo dos estados do incêndios, são as
                horas que o nosso sistema detetou uma mudança de dados por parte da ANPC podendo não corresponder ao
                momento exato em que essa alteração ocorreu.',
		'source'              => 'Risco de incêndio recolhido do IPMA.'
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

        'important' => 'Ocorrências importantes',
        'alerts' => 'Alertas',
	]

];
