@extends('app')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        .map-configurator {
            padding: 96px 16px 32px;
            max-width: 1400px;
            margin: 0 auto;
        }
        .map-configurator h1 {
            font-size: 1.75rem;
            margin: 0 0 4px;
        }
        .map-configurator .intro {
            color: #555;
            margin: 0 0 20px;
        }
        .map-configurator .grid {
            display: grid;
            gap: 20px;
            grid-template-columns: 1fr;
        }
        @media (min-width: 992px) {
            .map-configurator .grid {
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }
        }
        .mc-card {
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            padding: 14px 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,.04);
        }
        .mc-card + .mc-card {
            margin-top: 16px;
        }
        .mc-card h2 {
            font-size: 1.05rem;
            margin: 0 0 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .mc-card h2 .mc-count {
            font-size: .8rem;
            color: #777;
            font-weight: normal;
        }
        .mc-card .mc-hint {
            font-size: .82rem;
            color: #666;
            margin: 0 0 10px;
        }
        .mc-search {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: .9rem;
        }
        .mc-districts {
            max-height: 460px;
            overflow-y: auto;
            border-top: 1px solid #eee;
        }
        .mc-district {
            border-bottom: 1px solid #eee;
        }
        .mc-district__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 4px;
            cursor: pointer;
            user-select: none;
        }
        .mc-district__head:hover { background: #fafafa; }
        .mc-district__title {
            text-transform: capitalize;
            font-weight: 600;
            font-size: .92rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .mc-district__title .mc-district__count {
            font-weight: normal;
            color: #888;
            font-size: .8rem;
        }
        .mc-district__actions {
            display: flex;
            gap: 6px;
        }
        .mc-district__actions button {
            border: 1px solid #ccc;
            background: #fff;
            font-size: .75rem;
            padding: 2px 8px;
            border-radius: 4px;
            cursor: pointer;
        }
        .mc-district__actions button:hover { background: #f4f4f4; }
        .mc-district__body {
            display: none;
            padding: 4px 8px 10px;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 4px 12px;
        }
        .mc-district.is-open .mc-district__body {
            display: grid;
        }
        .mc-district.is-open .mc-district__chevron {
            transform: rotate(90deg);
        }
        .mc-district__chevron {
            transition: transform .15s;
        }
        .mc-concelho {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .85rem;
            text-transform: capitalize;
            padding: 2px 0;
            cursor: pointer;
        }
        .mc-concelho.is-hidden { display: none; }
        .mc-concelho input { margin: 0; }
        .mc-inline {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 16px;
        }
        .mc-inline label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .9rem;
            cursor: pointer;
            margin: 0;
        }
        .mc-inline input { margin: 0; }
        #mc-map {
            width: 100%;
            height: 340px;
            border-radius: 6px;
            background: #f0f0f0;
        }
        .mc-url-block {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .mc-url-block input {
            flex: 1 1 240px;
            min-width: 0;
            padding: 6px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: monospace;
            font-size: .85rem;
        }
        .mc-url-block button, .mc-url-block a {
            border: 1px solid #ccc;
            background: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: .85rem;
            cursor: pointer;
            text-decoration: none;
            color: #222;
        }
        .mc-url-block .btn-primary {
            background: #b81e1f;
            color: #fff;
            border-color: #b81e1f;
        }
        .mc-copied {
            font-size: .8rem;
            color: #1b7f2b;
            margin-top: 4px;
            display: none;
        }
        .mc-copied.is-visible { display: inline-block; }
        .mc-reset {
            border: 1px solid #ccc;
            background: #fff;
            font-size: .8rem;
            padding: 4px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: auto;
        }
    </style>
@endpush

@section('content')
    <main role="main" class="mb-auto">
        <div class="map-configurator">
            <h1>{{ __('pages.seo.mapConfigurator.title') }}</h1>
            <p class="intro">{{ __('pages.seo.mapConfigurator.description') }}</p>

            <div class="grid">
                <div>
                    <div class="mc-card">
                        <h2>
                            <span>{{ __('js.mapConfig.municipalities') }}</span>
                            <span class="mc-count" data-mc-count>0</span>
                        </h2>
                        <p class="mc-hint">{{ __('js.mapConfig.municipalitiesHint') }}</p>
                        <input type="search" class="mc-search" data-mc-search
                               placeholder="{{ __('js.mapConfig.searchMunicipality') }}">
                        <div class="mc-districts" data-mc-districts></div>
                        <div style="display:flex;gap:8px;margin-top:8px;">
                            <button type="button" class="mc-reset" data-mc-clear-all>{{ __('js.mapConfig.clearAll') }}</button>
                        </div>
                    </div>

                    <div class="mc-card">
                        <h2>{{ __('js.mapConfig.incidentKind') }}</h2>
                        <div class="mc-inline" data-mc-kind></div>
                    </div>

                    <div class="mc-card">
                        <h2>{{ __('js.mapConfig.status') }}</h2>
                        <p class="mc-hint">{{ __('js.mapConfig.statusHint') }}</p>
                        <div class="mc-inline" data-mc-status></div>
                    </div>

                    <div class="mc-card">
                        <h2>{{ __('js.mapConfig.baseLayer') }}</h2>
                        <div class="mc-inline" data-mc-base></div>
                    </div>

                    <div class="mc-card">
                        <h2>{{ __('js.mapConfig.extraLayers') }}</h2>
                        <p class="mc-hint">{{ __('js.mapConfig.extraLayersHint') }}</p>
                        <div class="mc-inline" data-mc-layers></div>
                    </div>
                </div>

                <div>
                    <div class="mc-card" style="position: sticky; top: 84px;">
                        <h2>{{ __('js.mapConfig.preview') }}</h2>
                        <div id="mc-map"></div>
                        <div style="margin-top:14px;">
                            <label style="display:block;font-size:.85rem;font-weight:600;margin-bottom:6px;">
                                {{ __('js.mapConfig.generatedUrl') }}
                            </label>
                            <div class="mc-url-block">
                                <input type="text" readonly data-mc-url>
                                <button type="button" data-mc-copy>{{ __('js.mapConfig.copy') }}</button>
                                <a href="#" target="_blank" rel="noopener" data-mc-open class="btn-primary">
                                    {{ __('js.mapConfig.openMap') }}
                                </a>
                            </div>
                            <span class="mc-copied" data-mc-copied>{{ __('js.mapConfig.copied') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/js/concelhos.js"></script>
    <script>
        window.fogosMapConfigMapCustomUrl = @json(route('mapCustom', ['locale' => \App::getLocale()]));
    </script>
    <script src="/js/mapa-config-wizard.js?v={{ filemtime(public_path('js/mapa-config-wizard.js')) }}"></script>
@endpush
