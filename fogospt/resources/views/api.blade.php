@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="container my-5">

                <div class="row justify-content-center">
                    <div class="col-lg-10">

                        <div class="mb-4">
                            <h1 class="mb-3">Acesso à API do Fogos.pt — Novas Regras e Controlo de Utilização</h1>
                            <p class="lead">
                                O Fogos.pt tem, ao longo dos anos, disponibilizado uma API pública amplamente utilizada por diversas entidades e projetos.
                            </p>
                            <p>
                                Essa abertura foi sempre intencional e alinhada com os princípios do projeto:
                                <strong>dados abertos, transparência e utilidade pública</strong>.
                            </p>
                            <p>
                                No entanto, o crescimento significativo da utilização da API — muitas vezes sem qualquer identificação ou controlo —
                                tem vindo a criar desafios ao nível da <strong>performance, estabilidade e disponibilidade</strong>.
                            </p>
                            <p>
                                Foram também identificadas situações de utilização abusiva, que consomem recursos de forma desproporcional.
                            </p>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>Porque é que isto muda agora?</h2>
                            <p>
                                O objetivo principal do Fogos.pt mantém-se:
                                <strong>garantir informação fiável, atualizada e acessível a todos os cidadãos</strong>.
                            </p>
                            <p>
                                Para isso, é essencial assegurar que a infraestrutura não é comprometida por acessos descontrolados.
                            </p>
                            <p>
                                Estas alterações pretendem garantir que todos conseguem continuar a usar o serviço de forma estável e sustentável.
                            </p>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>O que vai mudar?</h2>

                            <div class="alert alert-warning">
                                <strong>Brevemente</strong>, o acesso à API passará a estar restrito a utilizadores autorizados.
                            </div>

                            <p>Todos os pedidos à API deverão cumprir:</p>

                            <ul>
                                <li>
                                    Autenticação obrigatória via header:
                                    <pre class="bg-light p-2">FOGOS-PT-AUTH: {token}</pre>
                                </li>
                                <li>Utilização de <strong>token individual</strong></li>
                                <li>User-Agent <strong>identificável e personalizado</strong></li>
                                <li>Indicação do <strong>IP de origem</strong>, sempre que possível</li>
                            </ul>

                            <div class="alert alert-danger mt-3">
                                Pedidos que não cumpram estes requisitos poderão ser limitados ou bloqueados.
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>Como obter acesso?</h2>

                            <p>
                                O acesso à API passará a depender de um pedido formal.
                            </p>

                            <p>
                                Para solicitar autorização, deverá preencher o formulário:
                            </p>

                            <div class="alert alert-info">
                                👉 <strong><a href="https://forms.gle/TH1REx1nEm9MnJVM9">Formulário</a></strong>
                            </div>

                            <p>Cada pedido será analisado com base em:</p>

                            <ul>
                                <li>Finalidade de utilização</li>
                                <li>Volume de pedidos</li>
                                <li>Impacto na infraestrutura</li>
                                <li>Alinhamento com os objetivos do projeto</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>Princípios de utilização</h2>

                            <ul>
                                <li>Não comprometer a <strong>disponibilidade do serviço</strong></li>
                                <li>Evitar cargas excessivas ou desnecessárias</li>
                                <li>Garantir identificação clara dos pedidos</li>
                                <li>Utilização responsável e ética dos dados</li>
                            </ul>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>Um compromisso com a comunidade</h2>

                            <p>
                                O Fogos.pt continuará a ser um projeto
                                <strong>aberto, transparente e ao serviço da sociedade</strong>.
                            </p>

                            <p>
                                Estas medidas são necessárias para garantir que a plataforma continua a servir
                                <strong>milhões de utilizadores todos os anos</strong>, especialmente em momentos críticos.
                            </p>

                            <p>
                                Contamos com a colaboração de todos para manter este serviço
                                <strong>fiável, estável e sustentável</strong>.
                            </p>
                        </div>

                    </div>
                </div>

            </div>
    </main>
@endsection