@extends('layouts.bakoelkembang')

@section('content')

    {{-- PROFIL --}}
    <section id="profil">
        @include('landingpage.partials.profil')
    </section>

    {{-- FAQ --}}
    <section id="faq">
        @include('landingpage.partials.faq')
    </section>

    {{-- KONTAK --}}
    <section id="kontak">
        @include('landingpage.partials.kontak')
    </section>

@endsection