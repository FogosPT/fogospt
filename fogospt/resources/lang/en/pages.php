<?php

return [
	'about'       => [
		'entries_from'    => 'Data retrieved from <a href="http://www.prociv.pt/">Protecção Civil Portuguesa webpage</a> (ANPC - Portuguese Civil Protection)',
		'update_interval' => 'Updates every 10 minutes',
		'near_location'   => 'Aproximate location.',
		'suggestion_bugs' => 'Suggestions / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a>',
		'made_by'         => 'Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a>'
	],
	'information' => [
		'statesOfOccurrences' => [
			'title' => 'Status of Occurrences',
			'items' => [
				'firstAlertdispatch'  => '1ST ALERT DISPATCH – Units in transit to the site.',
				'arrivalToOccurrence' => 'ARRIVAL TO SITE – Units have arrived on site.',
				'ongoing'             => 'ONGOING - Ongoing fire with no area limitation.',
				'inResolution'        => 'IN RESOLUTION – Fire with no danger of spreading beyond the current perimeter.',
				'inConclusion'        => 'IN CONCLUSION – Fire extinguished, with small combustion spots within the fire perimeter.',
				'surveillance'        => 'SURVEILLANCE – Units on site to act in case of need.',
				'closed'              => 'CLOSED – Return of all involved units to the base concluded.'
			]
		],
		'typeOfUnits'         => [
			'title' => 'Units',
			'items' => [
				'humans'      => 'HUMAN - Firefighters, Firefighters Special Force, PSP (police), Armed Forces, Emergency Medical Services, Sapadores Florestais (forest fires & rescue services), GNR (gendarmery), GIPS Grupo Intervenção de Proteção e Socorro (emergency rescue services)',
				'terrestrial' => 'TERRESTRIAL - Road vehicles',
				'air'         => 'AIR - Helicopters / Aircrafts'
			],

		],
		'numberDescription'   => 'The displayed numbers match the total number of dispatched units. This number may differ from the units on site, as some of the dispatched units may still be in transit.',
		'hoursDescription'    => 'The time displayed both on the units graph and on the fire status timeline are the ones in which our system detected a change of data on ANPC information and it may not match the exact time that change occurred.',
		'source'              => 'Fire risk data retrieved from IPMA (Portuguese Institute for Sea and Atmosphere).'
	]
];
