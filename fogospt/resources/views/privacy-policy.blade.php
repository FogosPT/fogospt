@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    @if(app()->getLocale() === 'pt')

                    <h1 class="mb-1">Política de Privacidade – Fogos.pt</h1>
                    <p class="text-muted mb-4"><small>Última atualização: 24 de abril de 2026</small></p>

                    <p>A Fogos.pt ("nós", "nosso" ou "Aplicação") respeita a sua privacidade e compromete-se a proteger os seus dados pessoais.</p>
                    <p>Esta Política de Privacidade explica como a aplicação Fogos.pt acede, utiliza, trata e protege informação quando utiliza a aplicação móvel.</p>

                    <hr>

                    <div class="mb-4">
                        <h2>1. Quem Somos</h2>
                        <p>A Fogos.pt é uma aplicação independente que disponibiliza informação sobre incêndios rurais, ocorrências, avisos meteorológicos e informação de segurança pública em Portugal.</p>
                        <p>Contacto: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a><br>
                        Website: <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>2. Informação que Podemos Utilizar</h2>
                        <h5>a) Dados de Localização</h5>
                        <p>Se ativar permissões de localização, a aplicação pode utilizar a localização do seu dispositivo para:</p>
                        <ul>
                            <li>Alertas de ocorrências por proximidade</li>
                            <li>Apresentar ocorrências perto de si</li>
                            <li>Funcionalidades de radar / realidade aumentada</li>
                        </ul>
                        <p><strong>A localização é processada localmente no seu dispositivo para estas funcionalidades e não é enviada para os nossos servidores, salvo quando tal resulte de uma funcionalidade escolhida por si.</strong></p>
                        <p>Pode revogar as permissões de localização a qualquer momento nas definições do seu dispositivo.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>3. Fotografias e Conteúdos Submetidos pelo Utilizador</h2>
                        <p>Se optar por utilizar funcionalidades de fotografia ou reporte de ocorrências, os conteúdos submetidos podem incluir:</p>
                        <ul>
                            <li>Fotografia captada por si</li>
                            <li>Coordenadas GPS</li>
                            <li>Direção da bússola</li>
                            <li>Data e hora</li>
                        </ul>
                        <p>Estes dados apenas são enviados se optar expressamente por os submeter.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>4. Notificações</h2>
                        <p>Se ativadas, a aplicação pode enviar notificações relacionadas com:</p>
                        <ul>
                            <li>Alertas por concelho subscrito</li>
                            <li>Ocorrências próximas da sua localização</li>
                            <li>Outros alertas que tenha escolhido receber</li>
                        </ul>
                        <p>Pode desativar notificações na aplicação ou nas definições do seu dispositivo.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>5. Dados que Não Vendemos</h2>
                        <p>Não vendemos dados pessoais.</p>
                        <p>Não utilizamos dados pessoais para perfilagem publicitária.</p>
                        <p>Não partilhamos dados pessoais com terceiros para fins de marketing.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>6. Fontes dos Dados</h2>
                        <p>A aplicação apresenta informação obtida a partir de fontes públicas oficiais, incluindo:</p>
                        <ul>
                            <li>ANEPC</li>
                            <li>IPMA</li>
                            <li>Fontes públicas de deteção satélite (incluindo MODIS/VIIRS, quando aplicável)</li>
                        </ul>
                        <p>A informação é disponibilizada para fins informativos.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>7. Dados Técnicos e Analíticos</h2>
                        <p>A aplicação pode tratar informação técnica limitada necessária para desempenho, segurança e diagnóstico, como:</p>
                        <ul>
                            <li>Versão da aplicação</li>
                            <li>Tipo de dispositivo</li>
                            <li>Registos de falhas (crash diagnostics)</li>
                            <li>Métricas anónimas de utilização (caso serviços analíticos estejam ativos)</li>
                        </ul>
                        <p>Sempre que existam prestadores terceiros (por exemplo serviços de analytics ou crash reporting), estes tratam dados nos termos das respetivas políticas de privacidade.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>8. Conservação dos Dados</h2>
                        <p>Os dados são conservados apenas durante o período necessário para disponibilizar a funcionalidade relevante ou cumprir obrigações legais aplicáveis.</p>
                        <p>Conteúdos submetidos pelos utilizadores podem ser conservados para fins operacionais ou de segurança.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>9. Segurança</h2>
                        <p>Adotamos medidas técnicas e organizativas razoáveis para proteger a informação contra acesso não autorizado, alteração, divulgação ou destruição.</p>
                        <p>No entanto, nenhum sistema é totalmente imune a riscos.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>10. Crianças</h2>
                        <p>A aplicação não é dirigida a menores de 13 anos e não recolhe intencionalmente dados pessoais de crianças.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>11. Os Seus Direitos</h2>
                        <p>Nos termos da legislação aplicável, incluindo o RGPD, poderá ter direito a:</p>
                        <ul>
                            <li>Aceder aos seus dados</li>
                            <li>Solicitar retificação</li>
                            <li>Solicitar apagamento</li>
                            <li>Opor-se a certos tratamentos</li>
                            <li>Retirar consentimento quando aplicável</li>
                        </ul>
                        <p>Pedidos podem ser enviados para: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>12. Alterações a Esta Política</h2>
                        <p>Esta Política de Privacidade pode ser atualizada periodicamente.</p>
                        <p>Quaisquer alterações serão publicadas em: <a href="https://fogos.pt/privacy-policy" target="_blank" rel="noopener noreferrer">https://fogos.pt/privacy-policy</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>13. Contacto</h2>
                        <p>Para questões relacionadas com privacidade:</p>
                        <p><a href="mailto:mail@fogos.pt">mail@fogos.pt</a><br>
                        <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a></p>
                    </div>

                    <hr>

                    <p class="text-muted"><em>A Fogos.pt é uma aplicação independente e não representa nem está afiliada com qualquer entidade governamental.</em></p>

                    @else

                    <h1 class="mb-1">Privacy Policy – Fogos.pt</h1>
                    <p class="text-muted mb-4"><small>Last updated: April 24, 2026</small></p>

                    <p>Fogos.pt ("we", "our", or "the App") respects your privacy and is committed to protecting your personal data.</p>
                    <p>This Privacy Policy explains how Fogos.pt accesses, uses, stores and protects information when you use the mobile application.</p>

                    <hr>

                    <div class="mb-4">
                        <h2>1. Who We Are</h2>
                        <p>Fogos.pt is an independent application providing information about wildfires, incidents, weather alerts and related public safety information in Portugal.</p>
                        <p>Contact: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a><br>
                        Website: <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>2. Information We Collect</h2>
                        <h5>a) Location Data</h5>
                        <p>If you enable location permissions, the app may use your device location for:</p>
                        <ul>
                            <li>Proximity incident alerts</li>
                            <li>Showing incidents near you</li>
                            <li>Augmented reality/radar functionality</li>
                        </ul>
                        <p><strong>Location is processed on your device for these features and is not transmitted to our servers unless explicitly required by a feature you choose to use.</strong></p>
                        <p>You may revoke location permissions at any time in your device settings.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>3. Incident Photos Submitted by Users</h2>
                        <p>If you choose to use the incident camera/reporting features, photographs you submit may include:</p>
                        <ul>
                            <li>Photo content you capture</li>
                            <li>GPS coordinates</li>
                            <li>Compass direction</li>
                            <li>Timestamp</li>
                        </ul>
                        <p>This data is only submitted when you actively choose to send it.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>4. Notifications</h2>
                        <p>If enabled, we may send push notifications related to:</p>
                        <ul>
                            <li>Subscribed municipality alerts</li>
                            <li>Nearby incidents</li>
                            <li>Other incident notifications you choose to receive</li>
                        </ul>
                        <p>You can disable notifications in the app or in your device settings.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>5. Information We Do Not Sell</h2>
                        <p>We do not sell personal data.</p>
                        <p>We do not use personal information for advertising profiling.</p>
                        <p>We do not share personal data with third parties for marketing purposes.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>6. Data Sources</h2>
                        <p>The app displays information obtained from public official sources, including:</p>
                        <ul>
                            <li>ANEPC</li>
                            <li>IPMA</li>
                            <li>Public satellite fire detection sources (including MODIS/VIIRS where applicable)</li>
                        </ul>
                        <p>This information is displayed for informational purposes.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>7. Analytics and Technical Data</h2>
                        <p>The app may process limited technical information necessary for performance, security and troubleshooting, such as:</p>
                        <ul>
                            <li>App version</li>
                            <li>Device type</li>
                            <li>Crash diagnostics</li>
                            <li>Anonymous usage metrics (if analytics services are enabled)</li>
                        </ul>
                        <p>Where third-party processors are used (e.g. crash reporting or analytics providers), they process data under their own privacy obligations.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>8. Data Retention</h2>
                        <p>We retain personal data only for as long as necessary to provide the relevant feature or comply with legal obligations.</p>
                        <p>User-submitted content may be retained as necessary for operational or safety purposes.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>9. Security</h2>
                        <p>We implement reasonable technical and organizational measures to protect information against unauthorized access, alteration, disclosure or destruction.</p>
                        <p>However, no system is completely secure.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>10. Children</h2>
                        <p>The app is not directed to children under 13 and does not knowingly collect personal data from children.</p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>11. Your Rights</h2>
                        <p>Depending on applicable law (including GDPR), you may have rights to:</p>
                        <ul>
                            <li>Access your data</li>
                            <li>Request correction</li>
                            <li>Request deletion</li>
                            <li>Object to certain processing</li>
                            <li>Withdraw consent where applicable</li>
                        </ul>
                        <p>Requests may be sent to: <a href="mailto:mail@fogos.pt">mail@fogos.pt</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>12. Changes to This Policy</h2>
                        <p>We may update this Privacy Policy from time to time.</p>
                        <p>Changes will be posted at: <a href="https://fogos.pt/privacy-policy" target="_blank" rel="noopener noreferrer">https://fogos.pt/privacy-policy</a></p>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h2>13. Contact</h2>
                        <p>Questions regarding privacy may be sent to:</p>
                        <p><a href="mailto:mail@fogos.pt">mail@fogos.pt</a><br>
                        <a href="https://fogos.pt" target="_blank" rel="noopener noreferrer">https://fogos.pt</a></p>
                    </div>

                    <hr>

                    <p class="text-muted"><em>Fogos.pt is an independent application and is not affiliated with or representing any government entity.</em></p>

                    @endif

                </div>
            </div>
        </div>
    </main>
@endsection
