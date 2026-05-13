<?php

return [
    'meta_title' => 'Acesso à API do Fogos.pt',

    'title' => 'Acesso à API do Fogos.pt — Novas Regras e Controlo de Utilização',

    'intro_lead' => 'O Fogos.pt tem, ao longo dos anos, disponibilizado uma API pública amplamente utilizada por diversas entidades e projetos.',

    'intro_p2_before' => 'Essa abertura foi sempre intencional e alinhada com os princípios do projeto:',
    'intro_p2_strong' => 'dados abertos, transparência e utilidade pública',
    'intro_p2_after' => '.',

    'intro_p3_before' => 'No entanto, o crescimento significativo da utilização da API — muitas vezes sem qualquer identificação ou controlo — tem vindo a criar desafios ao nível da',
    'intro_p3_strong' => 'performance, estabilidade e disponibilidade',
    'intro_p3_after' => '.',

    'intro_p4' => 'Foram também identificadas situações de utilização abusiva, que consomem recursos de forma desproporcional.',

    'why_title' => 'Porque é que isto muda agora?',

    'why_p1_before' => 'O objetivo principal do Fogos.pt mantém-se:',
    'why_p1_strong' => 'garantir informação fiável, atualizada e acessível a todos os cidadãos',
    'why_p1_after' => '.',

    'why_p2' => 'Para isso, é essencial assegurar que a infraestrutura não é comprometida por acessos descontrolados.',

    'why_p3' => 'Estas alterações pretendem garantir que todos conseguem continuar a usar o serviço de forma estável e sustentável.',

    'change_title' => 'O que vai mudar?',

    'change_alert_before' => '',
    'change_alert_strong' => 'Brevemente',
    'change_alert_after' => ', o acesso à API passará a estar restrito a utilizadores autorizados.',

    'change_intro' => 'Todos os pedidos à API deverão cumprir:',

    'req_auth_intro' => 'Autenticação obrigatória via header:',
    'req_auth_pre' => 'FOGOS-PT-AUTH: {token}',

    'req_token' => 'Utilização de <strong>token individual</strong>',
    'req_user_agent' => 'User-Agent <strong>identificável e personalizado</strong>',
    'req_ip' => 'Indicação do <strong>IP de origem</strong>, sempre que possível',

    'change_danger' => 'Pedidos que não cumpram estes requisitos poderão ser limitados ou bloqueados.',

    'access_title' => 'Como obter acesso?',

    'access_p1' => 'O acesso à API passará a depender de um pedido formal.',

    'access_p2' => 'Para solicitar autorização, deverá preencher o formulário:',

    'form_label' => 'Formulário',
    'form_url' => 'https://forms.gle/TH1REx1nEm9MnJVM9',

    'review_intro' => 'Cada pedido será analisado com base em:',

    'review_criteria' => [
        'Finalidade de utilização',
        'Volume de pedidos',
        'Impacto na infraestrutura',
        'Alinhamento com os objetivos do projeto',
    ],

    'principles_title' => 'Princípios de utilização',

    'principles' => [
        'Não comprometer a <strong>disponibilidade do serviço</strong>',
        'Evitar cargas excessivas ou desnecessárias',
        'Garantir identificação clara dos pedidos',
        'Utilização responsável e ética dos dados',
    ],

    'community_title' => 'Um compromisso com a comunidade',

    'community_p1_before' => 'O Fogos.pt continuará a ser um projeto',
    'community_p1_strong' => 'aberto, transparente e ao serviço da sociedade',
    'community_p1_after' => '.',

    'community_p2_before' => 'Estas medidas são necessárias para garantir que a plataforma continua a servir',
    'community_p2_strong' => 'milhões de utilizadores todos os anos',
    'community_p2_after' => ', especialmente em momentos críticos.',

    'community_p3_before' => 'Contamos com a colaboração de todos para manter este serviço',
    'community_p3_strong' => 'fiável, estável e sustentável',
    'community_p3_after' => '.',
];
