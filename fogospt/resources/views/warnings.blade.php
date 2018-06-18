@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                @foreach($data as $warning)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">{{ $warning['text'] }}</h4>
                                <p class="f-local">
                                    {{ $warning['label'] }}
                                </p>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection

@push('scripts')
@endpush





