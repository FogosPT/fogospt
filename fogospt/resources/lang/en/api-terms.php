<?php

return [
    'meta_title' => 'Fogos.pt API terms of use',

    'title' => 'Fogos.pt API terms of use',

    'intro_alert' => 'Use of the Fogos.pt API implies full reading, understanding and acceptance of these terms of use.',

    'sections' => [
        [
            'title' => '1. Scope',
            'blocks' => [
                ['type' => 'p', 'html' => 'The Fogos.pt API is provided as part of a public-interest project aimed at promoting access to wildfire-related information in a transparent, open and accessible way.'],
                ['type' => 'p', 'html' => 'Fogos.pt reserves the right to define rules on access, authentication, technical controls and usage limits whenever necessary to protect the stability, availability and sustainability of the platform.'],
            ],
        ],
        [
            'title' => '2. Permitted use',
            'blocks' => [
                ['type' => 'p', 'html' => 'Use of the API is permitted in the following contexts:'],
                ['type' => 'ul', 'items' => [
                    ['html' => '<strong>Personal use</strong>, including individual, academic, educational, research or experimental projects'],
                    ['html' => '<strong>Use by organisations, including companies, for non-commercial purposes</strong>, including:', 'subitems' => [
                        'Research',
                        'Data analysis',
                        'Internal operational support tools',
                        'Civic or public-interest projects',
                        'Internal monitoring or visualisation without commercial exploitation',
                    ]],
                ]],
                ['type' => 'p', 'html' => 'Permission to use the API depends, where applicable, on prior authorisation and the allocation of valid access credentials.'],
            ],
        ],
        [
            'title' => '3. Prohibited use',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'Use of the API is not permitted in contexts involving monetisation, commercial promotion or direct or indirect economic exploitation.'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'On platforms, applications, websites or services that include:', 'subitems' => [
                        '<strong>Advertising</strong>, including banners, ads, sponsorships, programmatic monetisation or sponsored content',
                        '<strong>Collection of donations, contributions, crowdfunding or other forms of fundraising</strong>',
                    ]],
                    ['html' => 'In paid services, subscription services or any paid arrangement'],
                    ['html' => 'In products or services marketed to third parties'],
                    ['html' => 'In solutions whose value or business model depends, wholly or partly, on making API data available'],
                    ['html' => 'Resale, commercial redistribution or sublicensing of data obtained through the API'],
                    ['html' => 'Any use that could compromise Fogos.pt’s <strong>availability, performance, security or stability</strong>'],
                    ['html' => 'Any use that omits or seeks to conceal the origin of the data'],
                    ['html' => 'Any use that circumvents authentication, limits, authorisation or control mechanisms defined by Fogos.pt'],
                ]],
            ],
        ],
        [
            'title' => '4. Credit and attribution',
            'blocks' => [
                ['type' => 'p', 'html' => 'All use of the API must include <strong>a clear, visible and unambiguous reference to the source of the data</strong>.'],
                ['type' => 'alert_light', 'html' => '<strong>Source: Fogos.pt</strong>'],
                ['type' => 'p', 'html' => 'Whenever possible, a link to the official website should also be included: <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a>'],
                ['type' => 'p', 'html' => 'Attribution must be clearly visible to end users alongside data, maps, charts, dashboards or any other element that uses information from the API.'],
                ['type' => 'alert_warning', 'html' => 'Missing attribution, insufficient attribution or concealing the origin of the data constitutes a breach of these terms.'],
            ],
        ],
        [
            'title' => '5. Technical rules of use',
            'blocks' => [
                ['type' => 'p', 'html' => 'API users agree to:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Use only the authentication mechanisms provided, including access tokens when required'],
                    ['html' => 'Send requests with the authentication header defined by Fogos.pt'],
                    ['html' => 'Include an identifiable <strong>User-Agent</strong> and, whenever possible, one specific to the application or service'],
                    ['html' => 'Indicate, whenever possible, the IP address or technical origin of requests'],
                    ['html' => 'Avoid excessive, bulk or unnecessarily abusive automated requests'],
                    ['html' => 'Respect usage limits, rate limits, quotas or other technical restrictions that may be defined'],
                    ['html' => 'Not attempt to circumvent blocks, restrictions, monitoring mechanisms or protective measures'],
                    ['html' => 'Follow good technical practices that reduce the impact of API usage on Fogos.pt infrastructure'],
                ]],
            ],
        ],
        [
            'title' => '6. Service availability',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt does not guarantee that the API will be permanently available, uninterrupted, fault-free, complete or up to date at all times.'],
                ['type' => 'p', 'html' => 'API access may be conditioned, limited, suspended or interrupted, in whole or in part, for technical or operational reasons, security, maintenance, infrastructure protection, architecture changes or resource management.'],
                ['type' => 'p', 'html' => 'Fogos.pt may also change endpoints, response structures, authentication methods, usage limits or other technical conditions without prior notice.'],
            ],
        ],
        [
            'title' => '7. Accuracy and quality of data',
            'blocks' => [
                ['type' => 'p', 'html' => 'Although Fogos.pt strives to provide useful, up-to-date and reliable information, <strong>no express or implied warranty is given</strong> as to accuracy, completeness, timeliness, consistency, availability or fitness of the data for any particular purpose.'],
                ['type' => 'p', 'html' => 'Data provided through the API may depend on external sources, automated processes, technical integrations and update pipelines subject to failures, delays, omissions, inconsistencies or interruptions.'],
                ['type' => 'p', 'html' => 'It is solely the user’s responsibility to assess whether the data is suitable for the intended context.'],
            ],
        ],
        [
            'title' => '8. Limitation of liability',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt, its representatives, collaborators or associated entities shall not be liable for any direct, indirect, incidental, consequential, special or other damages arising from use, inability to use or reliance on information provided through the API.'],
                ['type' => 'p', 'html' => 'This includes, without limitation, data loss, operational loss, reputational harm, decision failures, business interruption, economic loss or any consequences arising from errors, omissions, unavailability, delays or inaccuracies in the data.'],
            ],
        ],
        [
            'title' => '9. Critical or sensitive uses',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'The Fogos.pt API must not be used as the sole or decisive source in critical contexts, emergencies, security, civil protection or sensitive operational decision-making.'],
                ['type' => 'p', 'html' => 'Use of the API in alerting systems, dispatch, operational coordination, emergency response, protection of people and property, or any other context where errors, delays or unavailability could cause harm must always be accompanied by independent validation and complementary sources.'],
                ['type' => 'p', 'html' => 'The user accepts full responsibility for any use of the API in high-risk or critical decision-making contexts.'],
            ],
        ],
        [
            'title' => '10. Suspension, revocation and blocking of access',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt reserves the right, at any time and without prior notice, to:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Limit, suspend or revoke authorised access'],
                    ['html' => 'Deactivate assigned tokens or credentials'],
                    ['html' => 'Block requests, origins, applications or users'],
                    ['html' => 'Refuse new access requests'],
                    ['html' => 'Implement additional technical control, filtering or protection measures'],
                ]],
                ['type' => 'p', 'html' => 'Such measures may be taken, among other reasons, in case of breach of these terms, abusive use, technical risk, legal risk, negative impact on the service or need to protect platform users.'],
            ],
        ],
        [
            'title' => '11. Changes to the terms',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt may change these terms of use at any time without prior notice.'],
                ['type' => 'p', 'html' => 'Users are responsible for reviewing this page periodically and ensuring they comply with the latest applicable terms.'],
            ],
        ],
        [
            'title' => '13. Final remarks',
            'blocks' => [
                ['type' => 'p', 'html' => 'The Fogos.pt API exists to serve the community and support access to public-interest information.'],
                ['type' => 'p', 'html' => 'Its use must respect the spirit of the project: <strong>public service, responsibility, transparency and sustainability</strong>.'],
                ['type' => 'p', 'html' => 'Continuity of this service depends on responsible, identifiable use aligned with the platform’s core mission: keeping Fogos.pt online, updated and stable for all citizens.'],
            ],
        ],
    ],
];
