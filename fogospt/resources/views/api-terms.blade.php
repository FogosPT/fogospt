@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <h1 class="mb-4">Termos de Utilização da API do Fogos.pt</h1>

                    <div class="alert alert-warning">
                        A utilização da API do Fogos.pt implica a leitura, compreensão e aceitação integral dos presentes termos de utilização.
                    </div>

                    <div class="mb-4">
                        <h2>1. Âmbito</h2>
                        <p>
                            A API do Fogos.pt é disponibilizada no âmbito de um projeto de interesse público, com o objetivo de promover
                            o acesso a informação sobre incêndios de forma transparente, aberta e acessível.
                        </p>
                        <p>
                            O Fogos.pt reserva-se o direito de definir regras de acesso, autenticação, controlo técnico e limitação de utilização,
                            sempre que tal seja necessário para proteger a estabilidade, a disponibilidade e a sustentabilidade da plataforma.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>2. Utilização Permitida</h2>
                        <p>É permitida a utilização da API nos seguintes contextos:</p>

                        <ul>
                            <li>
                                <strong>Utilização pessoal</strong>, incluindo projetos individuais, académicos, educativos, de investigação ou experimentais
                            </li>
                            <li>
                                <strong>Utilização por entidades, incluindo empresas, para fins não comerciais</strong>, nomeadamente:
                                <ul>
                                    <li>Investigação</li>
                                    <li>Análise de dados</li>
                                    <li>Ferramentas internas de apoio operacional</li>
                                    <li>Projetos cívicos ou de interesse público</li>
                                    <li>Monitorização ou visualização interna sem exploração comercial</li>
                                </ul>
                            </li>
                        </ul>

                        <p>
                            A permissão de utilização depende, quando aplicável, da obtenção de autorização prévia e da atribuição de credenciais de acesso válidas.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>3. Utilização Não Permitida</h2>

                        <div class="alert alert-danger">
                            Não é permitida a utilização da API em contextos de monetização, promoção comercial ou exploração económica direta ou indireta.
                        </div>

                        <ul>
                            <li>
                                Em plataformas, aplicações, websites ou serviços que incluam:
                                <ul>
                                    <li><strong>Publicidade</strong>, incluindo banners, anúncios, patrocínios, monetização programática ou conteúdos patrocinados</li>
                                    <li><strong>Recolha de donativos, contribuições, crowdfunding ou outras formas de financiamento</strong></li>
                                </ul>
                            </li>
                            <li>Em serviços pagos, por subscrição ou mediante qualquer contrapartida económica</li>
                            <li>Em produtos ou serviços comercializados a terceiros</li>
                            <li>Em soluções cujo valor ou modelo de negócio dependa, total ou parcialmente, da disponibilização dos dados da API</li>
                            <li>Na revenda, redistribuição comercial ou sublicenciamento dos dados obtidos através da API</li>
                            <li>Em qualquer utilização que possa comprometer a <strong>disponibilidade, desempenho, segurança ou estabilidade</strong> do Fogos.pt</li>
                            <li>Em qualquer utilização que omita ou procure ocultar a origem dos dados</li>
                            <li>Em qualquer utilização que contorne mecanismos de autenticação, limitação, autorização ou controlo definidos pelo Fogos.pt</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>4. Créditos e Atribuição</h2>

                        <p>
                            Toda a utilização da API deve incluir <strong>referência clara, visível e inequívoca à origem dos dados</strong>.
                        </p>

                        <div class="alert alert-light border">
                            <strong>Fonte: Fogos.pt</strong>
                        </div>

                        <p>
                            Sempre que possível, deverá igualmente ser incluído um link para o site oficial:
                            <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a>
                        </p>

                        <p>
                            A atribuição deve estar colocada de forma visível ao utilizador final, junto aos dados, mapas, gráficos, dashboards ou qualquer outro
                            elemento que utilize informação proveniente da API.
                        </p>

                        <div class="alert alert-warning">
                            A ausência de atribuição, a atribuição insuficiente ou a ocultação da origem dos dados constitui incumprimento dos presentes termos.
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>5. Regras Técnicas de Utilização</h2>

                        <p>Os utilizadores da API comprometem-se a:</p>

                        <ul>
                            <li>Utilizar exclusivamente os mecanismos de autenticação fornecidos, incluindo token de acesso quando exigido</li>
                            <li>Enviar os pedidos com o header de autenticação definido pelo Fogos.pt</li>
                            <li>Incluir um <strong>User-Agent identificável</strong> e, sempre que possível, específico da aplicação ou serviço em causa</li>
                            <li>Indicar, sempre que possível, o IP ou origem técnica dos pedidos</li>
                            <li>Não efetuar pedidos excessivos, massivos, automatizados de forma abusiva ou desnecessária</li>
                            <li>Respeitar limites de utilização, rate limits, quotas ou outras restrições técnicas que venham a ser definidas</li>
                            <li>Não tentar contornar bloqueios, restrições, mecanismos de monitorização ou medidas de proteção</li>
                            <li>Adotar boas práticas técnicas que reduzam o impacto da utilização da API na infraestrutura do Fogos.pt</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>6. Disponibilidade do Serviço</h2>

                        <p>
                            O Fogos.pt não garante que a API esteja permanentemente disponível, ininterrupta, livre de falhas, completa ou atualizada em todos os momentos.
                        </p>

                        <p>
                            O acesso à API poderá ser condicionado, limitado, suspenso ou interrompido, total ou parcialmente, por motivos técnicos, operacionais,
                            de segurança, manutenção, proteção da infraestrutura, alteração de arquitetura ou gestão de recursos.
                        </p>

                        <p>
                            O Fogos.pt poderá ainda alterar endpoints, estruturas de resposta, métodos de autenticação, limites de utilização ou outras condições
                            técnicas sem necessidade de aviso prévio.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>7. Exatidão e Qualidade dos Dados</h2>

                        <p>
                            Embora o Fogos.pt procure disponibilizar informação útil, atualizada e fiável, <strong>não é prestada qualquer garantia expressa ou implícita</strong>
                            quanto à exatidão, integridade, atualidade, consistência, disponibilidade ou adequação dos dados a qualquer finalidade específica.
                        </p>

                        <p>
                            Os dados disponibilizados através da API podem depender de fontes externas, processos automáticos, integrações técnicas e fluxos de atualização
                            que estão sujeitos a falhas, atrasos, omissões, inconsistências ou interrupções.
                        </p>

                        <p>
                            Cabe exclusivamente ao utilizador avaliar a adequação dos dados ao contexto em que os pretende utilizar.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>8. Limitação de Responsabilidade</h2>

                        <p>
                            O Fogos.pt, os seus responsáveis, colaboradores ou entidades associadas não poderão ser responsabilizados por quaisquer danos diretos, indiretos,
                            incidentais, consequenciais, especiais ou de qualquer outra natureza resultantes da utilização, impossibilidade de utilização ou confiança depositada
                            na informação disponibilizada através da API.
                        </p>

                        <p>
                            Isto inclui, sem limitar, perdas de dados, perdas operacionais, danos reputacionais, falhas de decisão, interrupções de atividade, prejuízos económicos
                            ou quaisquer consequências decorrentes de erros, omissões, indisponibilidade, atrasos ou inexatidões dos dados.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>9. Utilizações Críticas ou Sensíveis</h2>

                        <div class="alert alert-danger">
                            A API do Fogos.pt não deve ser utilizada como fonte única ou determinante em contextos críticos, de emergência, segurança, proteção civil ou tomada de decisão operacional sensível.
                        </div>

                        <p>
                            A utilização da API em sistemas de alerta, despacho, coordenação operacional, resposta de emergência, proteção de pessoas e bens, ou qualquer outro
                            contexto em que erros, atrasos ou indisponibilidades possam causar danos, deve ser sempre acompanhada de validação independente e de fontes complementares.
                        </p>

                        <p>
                            O utilizador assume integralmente a responsabilidade por qualquer utilização da API em contextos de risco elevado ou decisão crítica.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>10. Suspensão, Revogação e Bloqueio de Acesso</h2>

                        <p>O Fogos.pt reserva-se o direito de, a qualquer momento e sem necessidade de aviso prévio:</p>

                        <ul>
                            <li>Limitar, suspender ou revogar acessos autorizados</li>
                            <li>Desativar tokens ou credenciais atribuídas</li>
                            <li>Bloquear pedidos, origens, aplicações ou utilizadores</li>
                            <li>Recusar novos pedidos de acesso</li>
                            <li>Implementar medidas técnicas adicionais de controlo, filtragem ou proteção</li>
                        </ul>

                        <p>
                            Estas medidas poderão ser adotadas, nomeadamente, em caso de incumprimento dos presentes termos, utilização abusiva, risco técnico,
                            risco jurídico, impacto negativo no serviço ou necessidade de proteger os utilizadores da plataforma.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>11. Alterações aos Termos</h2>

                        <p>
                            O Fogos.pt poderá alterar os presentes termos de utilização a qualquer momento, sem aviso prévio.
                        </p>

                        <p>
                            É da responsabilidade dos utilizadores consultar periodicamente esta página e assegurar que continuam a cumprir a versão mais atual dos termos aplicáveis.
                        </p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>13. Considerações Finais</h2>

                        <p>
                            A API do Fogos.pt existe para servir a comunidade e apoiar o acesso a informação de interesse público.
                        </p>

                        <p>
                            A sua utilização deve respeitar o espírito do projeto:
                            <strong>serviço público, responsabilidade, transparência e sustentabilidade</strong>.
                        </p>

                        <p>
                            A continuidade deste serviço depende de uma utilização responsável, identificada e compatível com a missão principal da plataforma:
                            manter o Fogos.pt online, atualizado e estável para todos os cidadãos.
                        </p>
                    </div>

                </div>
            </div>
    </main>
@endsection