<?php

return [
    'meta_title' => 'Acceso a la API de Fogos.pt',

    'title' => 'Acceso a la API de Fogos.pt — Nuevas reglas y control de uso',

    'intro_lead' => 'Fogos.pt ha puesto durante años a disposición una API pública ampliamente utilizada por diversas entidades y proyectos.',

    'intro_p2_before' => 'Esa apertura ha sido siempre intencional y alineada con los principios del proyecto:',
    'intro_p2_strong' => 'datos abiertos, transparencia y utilidad pública',
    'intro_p2_after' => '.',

    'intro_p3_before' => 'Sin embargo, el crecimiento significativo del uso de la API — a menudo sin identificación ni control — está generando retos en términos de',
    'intro_p3_strong' => 'rendimiento, estabilidad y disponibilidad',
    'intro_p3_after' => '.',

    'intro_p4' => 'También se han identificado usos abusivos que consumen recursos de forma desproporcionada.',

    'why_title' => '¿Por qué cambia ahora?',

    'why_p1_before' => 'El objetivo principal de Fogos.pt se mantiene:',
    'why_p1_strong' => 'garantizar información fiable, actualizada y accesible para todos los ciudadanos',
    'why_p1_after' => '.',

    'why_p2' => 'Para ello es esencial asegurar que la infraestructura no se vea comprometida por accesos descontrolados.',

    'why_p3' => 'Estos cambios pretenden garantizar que todos puedan seguir usando el servicio de forma estable y sostenible.',

    'change_title' => '¿Qué va a cambiar?',

    'change_alert_before' => '',
    'change_alert_strong' => 'En breve',
    'change_alert_after' => ', el acceso a la API quedará restringido a usuarios autorizados.',

    'change_intro' => 'Todas las peticiones a la API deberán cumplir:',

    'req_auth_intro' => 'Autenticación obligatoria mediante cabecera:',
    'req_auth_pre' => 'FOGOS-PT-AUTH: {token}',

    'req_token' => 'Uso de un <strong>token individual</strong>',
    'req_user_agent' => '<strong>User-Agent</strong> identificable y específico',
    'req_ip' => 'Indicación de la <strong>IP de origen</strong>, siempre que sea posible',

    'change_danger' => 'Las peticiones que no cumplan estos requisitos podrán ser limitadas o bloqueadas.',

    'access_title' => '¿Cómo obtener acceso?',

    'access_p1' => 'El acceso a la API dependerá de una solicitud formal.',

    'access_p2' => 'Para solicitar autorización, debe cumplimentar el formulario:',

    'form_label' => 'Formulario',
    'form_url' => 'https://forms.gle/TH1REx1nEm9MnJVM9',

    'review_intro' => 'Cada solicitud se analizará en función de:',

    'review_criteria' => [
        'Finalidad del uso',
        'Volumen de peticiones',
        'Impacto en la infraestructura',
        'Alineación con los objetivos del proyecto',
    ],

    'principles_title' => 'Principios de uso',

    'principles' => [
        'No comprometer la <strong>disponibilidad del servicio</strong>',
        'Evitar cargas excesivas o innecesarias',
        'Garantizar una identificación clara de las peticiones',
        'Uso responsable y ético de los datos',
    ],

    'community_title' => 'Un compromiso con la comunidad',

    'community_p1_before' => 'Fogos.pt seguirá siendo un proyecto',
    'community_p1_strong' => 'abierto, transparente y al servicio de la sociedad',
    'community_p1_after' => '.',

    'community_p2_before' => 'Estas medidas son necesarias para garantizar que la plataforma siga sirviendo a',
    'community_p2_strong' => 'millones de usuarios cada año',
    'community_p2_after' => ', especialmente en momentos críticos.',

    'community_p3_before' => 'Contamos con la colaboración de todos para mantener este servicio',
    'community_p3_strong' => 'fiable, estable y sostenible',
    'community_p3_after' => '.',
];
