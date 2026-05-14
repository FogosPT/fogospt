<?php

return [
    'meta_title' => 'Fogos.pt API access',

    'title' => 'Fogos.pt API access — New rules and usage controls',

    'intro_lead' => 'Over the years, Fogos.pt has provided a public API widely used by many organisations and projects.',

    'intro_p2_before' => 'That openness has always been intentional and aligned with the project principles:',
    'intro_p2_strong' => 'open data, transparency and public utility',
    'intro_p2_after' => '.',

    'intro_p3_before' => 'However, significant growth in API usage — often without identification or controls — has created challenges for',
    'intro_p3_strong' => 'performance, stability and availability',
    'intro_p3_after' => '.',

    'intro_p4' => 'Cases of abusive use that consume resources disproportionately have also been identified.',

    'why_title' => 'Why is this changing now?',

    'why_p1_before' => 'Fogos.pt\'s main goal remains:',
    'why_p1_strong' => 'to provide reliable, up-to-date information that is accessible to all citizens',
    'why_p1_after' => '.',

    'why_p2' => 'To achieve that, it is essential to ensure infrastructure is not compromised by uncontrolled access.',

    'why_p3' => 'These changes aim to ensure everyone can keep using the service in a stable and sustainable way.',

    'change_title' => 'What will change?',

    'change_alert_before' => '',
    'change_alert_strong' => 'Soon',
    'change_alert_after' => ', API access will be restricted to authorised users.',

    'change_intro' => 'All API requests must meet the following:',

    'req_auth_intro' => 'Mandatory authentication via header:',
    'req_auth_pre' => 'FOGOS-PT-AUTH: {token}',

    'req_token' => 'Use of an <strong>individual token</strong>',
    'req_user_agent' => 'An identifiable and specific <strong>User-Agent</strong>',
    'req_ip' => 'Indication of the <strong>source IP</strong> whenever possible',

    'change_danger' => 'Requests that do not meet these requirements may be limited or blocked.',

    'access_title' => 'How do I get access?',

    'access_p1' => 'API access will depend on a formal request.',

    'access_p2' => 'To request authorisation, please complete the form:',

    'form_label' => 'Application form',
    'form_url' => 'https://forms.gle/TH1REx1nEm9MnJVM9',

    'review_intro' => 'Each request will be assessed based on:',

    'review_criteria' => [
        'Intended use',
        'Request volume',
        'Impact on infrastructure',
        'Alignment with the project\'s goals',
    ],

    'principles_title' => 'Principles of use',

    'principles' => [
        'Do not compromise <strong>service availability</strong>',
        'Avoid excessive or unnecessary load',
        'Ensure requests are clearly identifiable',
        'Responsible and ethical use of data',
    ],

    'community_title' => 'A commitment to the community',

    'community_p1_before' => 'Fogos.pt will remain a project that is',
    'community_p1_strong' => 'open, transparent and in service of society',
    'community_p1_after' => '.',

    'community_p2_before' => 'These measures are necessary so the platform can keep serving',
    'community_p2_strong' => 'millions of users every year',
    'community_p2_after' => ', especially at critical moments.',

    'community_p3_before' => 'We count on everyone\'s cooperation to keep this service',
    'community_p3_strong' => 'reliable, stable and sustainable',
    'community_p3_after' => '.',
];
