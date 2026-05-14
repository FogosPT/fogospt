<?php

return [
    'meta_title' => 'Términos de uso de la API de Fogos.pt',

    'title' => 'Términos de uso de la API de Fogos.pt',

    'intro_alert' => 'El uso de la API de Fogos.pt implica la lectura, comprensión y aceptación íntegra de los presentes términos de uso.',

    'sections' => [
        [
            'title' => '1. Ámbito',
            'blocks' => [
                ['type' => 'p', 'html' => 'La API de Fogos.pt se ofrece en el marco de un proyecto de interés público, con el objetivo de promover el acceso a información sobre incendios de forma transparente, abierta y accesible.'],
                ['type' => 'p', 'html' => 'Fogos.pt se reserva el derecho de definir normas de acceso, autenticación, control técnico y limitación de uso cuando sea necesario para proteger la estabilidad, la disponibilidad y la sostenibilidad de la plataforma.'],
            ],
        ],
        [
            'title' => '2. Uso permitido',
            'blocks' => [
                ['type' => 'p', 'html' => 'Está permitido el uso de la API en los siguientes contextos:'],
                ['type' => 'ul', 'items' => [
                    ['html' => '<strong>Uso personal</strong>, incluidos proyectos individuales, académicos, educativos, de investigación o experimentales'],
                    ['html' => '<strong>Uso por entidades, incluidas empresas, con fines no comerciales</strong>, en particular:', 'subitems' => [
                        'Investigación',
                        'Análisis de datos',
                        'Herramientas internas de apoyo operativo',
                        'Proyectos cívicos o de interés público',
                        'Monitorización o visualización interna sin explotación comercial',
                    ]],
                ]],
                ['type' => 'p', 'html' => 'El permiso de uso depende, cuando corresponda, de la obtención de autorización previa y de la asignación de credenciales de acceso válidas.'],
            ],
        ],
        [
            'title' => '3. Uso no permitido',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'No está permitido el uso de la API en contextos de monetización, promoción comercial o explotación económica directa o indirecta.'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'En plataformas, aplicaciones, sitios web o servicios que incluyan:', 'subitems' => [
                        '<strong>Publicidad</strong>, incluidos banners, anuncios, patrocinios, monetización programática o contenidos patrocinados',
                        '<strong>Recaudación de donaciones, contribuciones, crowdfunding u otras formas de financiación</strong>',
                    ]],
                    ['html' => 'En servicios de pago, por suscripción o con cualquier contraprestación económica'],
                    ['html' => 'En productos o servicios comercializados a terceros'],
                    ['html' => 'En soluciones cuyo valor o modelo de negocio dependa, total o parcialmente, de la puesta a disposición de los datos de la API'],
                    ['html' => 'En la reventa, redistribución comercial o sublicencia de los datos obtenidos a través de la API'],
                    ['html' => 'En cualquier uso que pueda comprometer la <strong>disponibilidad, el rendimiento, la seguridad o la estabilidad</strong> de Fogos.pt'],
                    ['html' => 'En cualquier uso que omita u oculte el origen de los datos'],
                    ['html' => 'En cualquier uso que eluda mecanismos de autenticación, limitación, autorización o control definidos por Fogos.pt'],
                ]],
            ],
        ],
        [
            'title' => '4. Créditos y atribución',
            'blocks' => [
                ['type' => 'p', 'html' => 'Todo uso de la API debe incluir <strong>una referencia clara, visible e inequívoca al origen de los datos</strong>.'],
                ['type' => 'alert_light', 'html' => '<strong>Fuente: Fogos.pt</strong>'],
                ['type' => 'p', 'html' => 'Siempre que sea posible, también debe incluirse un enlace al sitio oficial: <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a>'],
                ['type' => 'p', 'html' => 'La atribución debe mostrarse de forma visible al usuario final junto a los datos, mapas, gráficos, paneles o cualquier otro elemento que utilice información procedente de la API.'],
                ['type' => 'alert_warning', 'html' => 'La ausencia de atribución, una atribución insuficiente u ocultar el origen de los datos constituye incumplimiento de los presentes términos.'],
            ],
        ],
        [
            'title' => '5. Normas técnicas de uso',
            'blocks' => [
                ['type' => 'p', 'html' => 'Los usuarios de la API se comprometen a:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Utilizar exclusivamente los mecanismos de autenticación proporcionados, incluido el token de acceso cuando sea obligatorio'],
                    ['html' => 'Enviar las peticiones con la cabecera de autenticación definida por Fogos.pt'],
                    ['html' => 'Incluir un <strong>User-Agent identificable</strong> y, siempre que sea posible, específico de la aplicación o servicio'],
                    ['html' => 'Indicar, siempre que sea posible, la IP u origen técnico de las peticiones'],
                    ['html' => 'No realizar peticiones excesivas, masivas, automatizadas de forma abusiva o innecesaria'],
                    ['html' => 'Respetar límites de uso, rate limits, cuotas u otras restricciones técnicas que puedan definirse'],
                    ['html' => 'No intentar eludir bloqueos, restricciones, mecanismos de monitorización o medidas de protección'],
                    ['html' => 'Adoptar buenas prácticas técnicas que reduzcan el impacto del uso de la API en la infraestructura de Fogos.pt'],
                ]],
            ],
        ],
        [
            'title' => '6. Disponibilidad del servicio',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt no garantiza que la API esté permanentemente disponible, ininterrumpida, libre de fallos, completa o actualizada en todo momento.'],
                ['type' => 'p', 'html' => 'El acceso a la API puede condicionarse, limitarse, suspenderse o interrumpirse, total o parcialmente, por motivos técnicos u operativos, de seguridad, mantenimiento, protección de la infraestructura, cambios de arquitectura o gestión de recursos.'],
                ['type' => 'p', 'html' => 'Fogos.pt también puede modificar endpoints, estructuras de respuesta, métodos de autenticación, límites de uso u otras condiciones técnicas sin necesidad de aviso previo.'],
            ],
        ],
        [
            'title' => '7. Exactitud y calidad de los datos',
            'blocks' => [
                ['type' => 'p', 'html' => 'Aunque Fogos.pt procura ofrecer información útil, actualizada y fiable, <strong>no se ofrece garantía expresa ni implícita</strong> en cuanto a exactitud, integridad, vigencia, coherencia, disponibilidad o idoneidad de los datos para un fin determinado.'],
                ['type' => 'p', 'html' => 'Los datos facilitados a través de la API pueden depender de fuentes externas, procesos automáticos, integraciones técnicas y flujos de actualización sujetos a fallos, retrasos, omisiones, incoherencias o interrupciones.'],
                ['type' => 'p', 'html' => 'Corresponde exclusivamente al usuario evaluar la idoneidad de los datos en el contexto en que pretenda utilizarlos.'],
            ],
        ],
        [
            'title' => '8. Limitación de responsabilidad',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt, sus responsables, colaboradores o entidades asociadas no podrán ser responsabilizados por daños directos, indirectos, incidentales, consecuentes, especiales o de cualquier otra naturaleza derivados del uso, la imposibilidad de uso o la confianza depositada en la información facilitada a través de la API.'],
                ['type' => 'p', 'html' => 'Esto incluye, sin limitación, pérdidas de datos, pérdidas operativas, daños reputacionales, errores de decisión, interrupciones de actividad, perjuicios económicos o cualquier consecuencia derivada de errores, omisiones, indisponibilidad, retrasos o inexactitudes en los datos.'],
            ],
        ],
        [
            'title' => '9. Usos críticos o sensibles',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'La API de Fogos.pt no debe utilizarse como fuente única o determinante en contextos críticos, de emergencia, seguridad, protección civil o toma de decisiones operativas sensibles.'],
                ['type' => 'p', 'html' => 'El uso de la API en sistemas de alerta, despacho, coordinación operativa, respuesta de emergencia, protección de personas y bienes, o cualquier otro contexto en el que errores, retrasos o indisponibilidades puedan causar daños debe ir siempre acompañado de validación independiente y fuentes complementarias.'],
                ['type' => 'p', 'html' => 'El usuario asume íntegramente la responsabilidad por cualquier uso de la API en contextos de alto riesgo o decisión crítica.'],
            ],
        ],
        [
            'title' => '10. Suspensión, revocación y bloqueo de acceso',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt se reserva el derecho de, en cualquier momento y sin necesidad de aviso previo:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Limitar, suspender o revocar accesos autorizados'],
                    ['html' => 'Desactivar tokens o credenciales asignadas'],
                    ['html' => 'Bloquear peticiones, orígenes, aplicaciones o usuarios'],
                    ['html' => 'Rechazar nuevas solicitudes de acceso'],
                    ['html' => 'Implementar medidas técnicas adicionales de control, filtrado o protección'],
                ]],
                ['type' => 'p', 'html' => 'Estas medidas podrán adoptarse, entre otros supuestos, en caso de incumplimiento de los presentes términos, uso abusivo, riesgo técnico, riesgo jurídico, impacto negativo en el servicio o necesidad de proteger a los usuarios de la plataforma.'],
            ],
        ],
        [
            'title' => '11. Cambios en los términos',
            'blocks' => [
                ['type' => 'p', 'html' => 'Fogos.pt puede modificar los presentes términos de uso en cualquier momento sin aviso previo.'],
                ['type' => 'p', 'html' => 'Es responsabilidad de los usuarios consultar periódicamente esta página y asegurarse de cumplir la versión más reciente de los términos aplicables.'],
            ],
        ],
        [
            'title' => '13. Consideraciones finales',
            'blocks' => [
                ['type' => 'p', 'html' => 'La API de Fogos.pt existe para servir a la comunidad y apoyar el acceso a información de interés público.'],
                ['type' => 'p', 'html' => 'Su uso debe respetar el espíritu del proyecto: <strong>servicio público, responsabilidad, transparencia y sostenibilidad</strong>.'],
                ['type' => 'p', 'html' => 'La continuidad de este servicio depende de un uso responsable, identificado y compatible con la misión principal de la plataforma: mantener Fogos.pt en línea, actualizado y estable para todos los ciudadanos.'],
            ],
        ],
    ],
];
