<?php

return [
    'meta_title' => 'Termos de Utilização da API do Fogos.pt',

    'title' => 'Termos de Utilização da API do Fogos.pt',

    'intro_alert' => 'A utilização da API do Fogos.pt implica a leitura, compreensão e aceitação integral dos presentes termos de utilização.',

    'sections' => [
        [
            'title' => '1. Âmbito',
            'blocks' => [
                ['type' => 'p', 'html' => 'A API do Fogos.pt é disponibilizada no âmbito de um projeto de interesse público, com o objetivo de promover o acesso a informação sobre incêndios de forma transparente, aberta e acessível.'],
                ['type' => 'p', 'html' => 'O Fogos.pt reserva-se o direito de definir regras de acesso, autenticação, controlo técnico e limitação de utilização, sempre que tal seja necessário para proteger a estabilidade, a disponibilidade e a sustentabilidade da plataforma.'],
            ],
        ],
        [
            'title' => '2. Utilização Permitida',
            'blocks' => [
                ['type' => 'p', 'html' => 'É permitida a utilização da API nos seguintes contextos:'],
                ['type' => 'ul', 'items' => [
                    ['html' => '<strong>Utilização pessoal</strong>, incluindo projetos individuais, académicos, educativos, de investigação ou experimentais'],
                    ['html' => '<strong>Utilização por entidades, incluindo empresas, para fins não comerciais</strong>, nomeadamente:', 'subitems' => [
                        'Investigação',
                        'Análise de dados',
                        'Ferramentas internas de apoio operacional',
                        'Projetos cívicos ou de interesse público',
                        'Monitorização ou visualização interna sem exploração comercial',
                    ]],
                ]],
                ['type' => 'p', 'html' => 'A permissão de utilização depende, quando aplicável, da obtenção de autorização prévia e da atribuição de credenciais de acesso válidas.'],
            ],
        ],
        [
            'title' => '3. Utilização Não Permitida',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'Não é permitida a utilização da API em contextos de monetização, promoção comercial ou exploração económica direta ou indireta.'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Em plataformas, aplicações, websites ou serviços que incluam:', 'subitems' => [
                        '<strong>Publicidade</strong>, incluindo banners, anúncios, patrocínios, monetização programática ou conteúdos patrocinados',
                        '<strong>Recolha de donativos, contribuições, crowdfunding ou outras formas de financiamento</strong>',
                    ]],
                    ['html' => 'Em serviços pagos, por subscrição ou mediante qualquer contrapartida económica'],
                    ['html' => 'Em produtos ou serviços comercializados a terceiros'],
                    ['html' => 'Em soluções cujo valor ou modelo de negócio dependa, total ou parcialmente, da disponibilização dos dados da API'],
                    ['html' => 'Na revenda, redistribuição comercial ou sublicenciamento dos dados obtidos através da API'],
                    ['html' => 'Em qualquer utilização que possa comprometer a <strong>disponibilidade, desempenho, segurança ou estabilidade</strong> do Fogos.pt'],
                    ['html' => 'Em qualquer utilização que omita ou procure ocultar a origem dos dados'],
                    ['html' => 'Em qualquer utilização que contorne mecanismos de autenticação, limitação, autorização ou controlo definidos pelo Fogos.pt'],
                ]],
            ],
        ],
        [
            'title' => '4. Créditos e Atribuição',
            'blocks' => [
                ['type' => 'p', 'html' => 'Toda a utilização da API deve incluir <strong>referência clara, visível e inequívoca à origem dos dados</strong>.'],
                ['type' => 'alert_light', 'html' => '<strong>Fonte: Fogos.pt</strong>'],
                ['type' => 'p', 'html' => 'Sempre que possível, deverá igualmente ser incluído um link para o site oficial: <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a>'],
                ['type' => 'p', 'html' => 'A atribuição deve estar colocada de forma visível ao utilizador final, junto aos dados, mapas, gráficos, dashboards ou qualquer outro elemento que utilize informação proveniente da API.'],
                ['type' => 'alert_warning', 'html' => 'A ausência de atribuição, a atribuição insuficiente ou a ocultação da origem dos dados constitui incumprimento dos presentes termos.'],
            ],
        ],
        [
            'title' => '5. Regras Técnicas de Utilização',
            'blocks' => [
                ['type' => 'p', 'html' => 'Os utilizadores da API comprometem-se a:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Utilizar exclusivamente os mecanismos de autenticação fornecidos, incluindo token de acesso quando exigido'],
                    ['html' => 'Enviar os pedidos com o header de autenticação definido pelo Fogos.pt'],
                    ['html' => 'Incluir um <strong>User-Agent identificável</strong> e, sempre que possível, específico da aplicação ou serviço em causa'],
                    ['html' => 'Indicar, sempre que possível, o IP ou origem técnica dos pedidos'],
                    ['html' => 'Não efetuar pedidos excessivos, massivos, automatizados de forma abusiva ou desnecessária'],
                    ['html' => 'Respeitar limites de utilização, rate limits, quotas ou outras restrições técnicas que venham a ser definidas'],
                    ['html' => 'Não tentar contornar bloqueios, restrições, mecanismos de monitorização ou medidas de proteção'],
                    ['html' => 'Adotar boas práticas técnicas que reduzam o impacto da utilização da API na infraestrutura do Fogos.pt'],
                ]],
            ],
        ],
        [
            'title' => '6. Disponibilidade do Serviço',
            'blocks' => [
                ['type' => 'p', 'html' => 'O Fogos.pt não garante que a API esteja permanentemente disponível, ininterrupta, livre de falhas, completa ou atualizada em todos os momentos.'],
                ['type' => 'p', 'html' => 'O acesso à API poderá ser condicionado, limitado, suspenso ou interrompido, total ou parcialmente, por motivos técnicos, operacionais, de segurança, manutenção, proteção da infraestrutura, alteração de arquitetura ou gestão de recursos.'],
                ['type' => 'p', 'html' => 'O Fogos.pt poderá ainda alterar endpoints, estruturas de resposta, métodos de autenticação, limites de utilização ou outras condições técnicas sem necessidade de aviso prévio.'],
            ],
        ],
        [
            'title' => '7. Exatidão e Qualidade dos Dados',
            'blocks' => [
                ['type' => 'p', 'html' => 'Embora o Fogos.pt procure disponibilizar informação útil, atualizada e fiável, <strong>não é prestada qualquer garantia expressa ou implícita</strong> quanto à exatidão, integridade, atualidade, consistência, disponibilidade ou adequação dos dados a qualquer finalidade específica.'],
                ['type' => 'p', 'html' => 'Os dados disponibilizados através da API podem depender de fontes externas, processos automáticos, integrações técnicas e fluxos de atualização que estão sujeitos a falhas, atrasos, omissões, inconsistências ou interrupções.'],
                ['type' => 'p', 'html' => 'Cabe exclusivamente ao utilizador avaliar a adequação dos dados ao contexto em que os pretende utilizar.'],
            ],
        ],
        [
            'title' => '8. Limitação de Responsabilidade',
            'blocks' => [
                ['type' => 'p', 'html' => 'O Fogos.pt, os seus responsáveis, colaboradores ou entidades associadas não poderão ser responsabilizados por quaisquer danos diretos, indiretos, incidentais, consequenciais, especiais ou de qualquer outra natureza resultantes da utilização, impossibilidade de utilização ou confiança depositada na informação disponibilizada através da API.'],
                ['type' => 'p', 'html' => 'Isto inclui, sem limitar, perdas de dados, perdas operacionais, danos reputacionais, falhas de decisão, interrupções de atividade, prejuízos económicos ou quaisquer consequências decorrentes de erros, omissões, indisponibilidade, atrasos ou inexatidões dos dados.'],
            ],
        ],
        [
            'title' => '9. Utilizações Críticas ou Sensíveis',
            'blocks' => [
                ['type' => 'alert_danger', 'html' => 'A API do Fogos.pt não deve ser utilizada como fonte única ou determinante em contextos críticos, de emergência, segurança, proteção civil ou tomada de decisão operacional sensível.'],
                ['type' => 'p', 'html' => 'A utilização da API em sistemas de alerta, despacho, coordenação operacional, resposta de emergência, proteção de pessoas e bens, ou qualquer outro contexto em que erros, atrasos ou indisponibilidades possam causar danos, deve ser sempre acompanhada de validação independente e de fontes complementares.'],
                ['type' => 'p', 'html' => 'O utilizador assume integralmente a responsabilidade por qualquer utilização da API em contextos de risco elevado ou decisão crítica.'],
            ],
        ],
        [
            'title' => '10. Suspensão, Revogação e Bloqueio de Acesso',
            'blocks' => [
                ['type' => 'p', 'html' => 'O Fogos.pt reserva-se o direito de, a qualquer momento e sem necessidade de aviso prévio:'],
                ['type' => 'ul', 'items' => [
                    ['html' => 'Limitar, suspender ou revogar acessos autorizados'],
                    ['html' => 'Desativar tokens ou credenciais atribuídas'],
                    ['html' => 'Bloquear pedidos, origens, aplicações ou utilizadores'],
                    ['html' => 'Recusar novos pedidos de acesso'],
                    ['html' => 'Implementar medidas técnicas adicionais de controlo, filtragem ou proteção'],
                ]],
                ['type' => 'p', 'html' => 'Estas medidas poderão ser adotadas, nomeadamente, em caso de incumprimento dos presentes termos, utilização abusiva, risco técnico, risco jurídico, impacto negativo no serviço ou necessidade de proteger os utilizadores da plataforma.'],
            ],
        ],
        [
            'title' => '11. Alterações aos Termos',
            'blocks' => [
                ['type' => 'p', 'html' => 'O Fogos.pt poderá alterar os presentes termos de utilização a qualquer momento, sem aviso prévio.'],
                ['type' => 'p', 'html' => 'É da responsabilidade dos utilizadores consultar periodicamente esta página e assegurar que continuam a cumprir a versão mais atual dos termos aplicáveis.'],
            ],
        ],
        [
            'title' => '13. Considerações Finais',
            'blocks' => [
                ['type' => 'p', 'html' => 'A API do Fogos.pt existe para servir a comunidade e apoiar o acesso a informação de interesse público.'],
                ['type' => 'p', 'html' => 'A sua utilização deve respeitar o espírito do projeto: <strong>serviço público, responsabilidade, transparência e sustentabilidade</strong>.'],
                ['type' => 'p', 'html' => 'A continuidade deste serviço depende de uma utilização responsável, identificada e compatível com a missão principal da plataforma: manter o Fogos.pt online, atualizado e estável para todos os cidadãos.'],
            ],
        ],
    ],
];
