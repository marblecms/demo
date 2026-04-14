@extends('layouts.frontend')

@section('title', $item->name())

@section('content')

@php
    $intro       = $item->value('intro');
    $screenshots = $item->value('screenshots') ?: [];
@endphp

<div class="screenshots-header">
    <div class="wrap">
        <x-breadcrumb :item="$item" />
        <h1>{{ $item->name() }}</h1>
        @if($intro)<p class="screenshots-intro">{{ $intro }}</p>@endif
    </div>
</div>

@if(!empty($screenshots))
<div class="screenshots-section">
    <div class="wrap">

        <div class="carousel" id="marble-carousel">
            <div class="carousel-track-wrap">
                <div class="carousel-track">
                    @foreach($screenshots as $i => $shot)
                        <div class="carousel-slide {{ $i === 0 ? 'is-active' : '' }}" data-index="{{ $i }}">
                            <img src="{{ $shot['url'] }}" alt="{{ $shot['original_filename'] ?? 'Screenshot ' . ($i + 1) }}" class="carousel-img">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-btn-fullscreen" aria-label="Fullscreen">&#x26F6;</button>
            </div>

            <button class="carousel-btn carousel-btn-prev" aria-label="Previous">&#8592;</button>
            <button class="carousel-btn carousel-btn-next" aria-label="Next">&#8594;</button>

            <div class="carousel-dots">
                @foreach($screenshots as $i => $shot)
                    <button class="carousel-dot {{ $i === 0 ? 'is-active' : '' }}" data-goto="{{ $i }}" aria-label="Go to screenshot {{ $i + 1 }}"></button>
                @endforeach
            </div>

            <div class="carousel-counter">
                <span class="carousel-counter-current">1</span> / <span class="carousel-counter-total">{{ count($screenshots) }}</span>
            </div>
        </div>

        <div class="carousel-thumbnails">
            @foreach($screenshots as $i => $shot)
                <button class="carousel-thumb {{ $i === 0 ? 'is-active' : '' }}" data-goto="{{ $i }}" aria-label="Screenshot {{ $i + 1 }}">
                    <img src="{{ $shot['thumbnail'] ?? $shot['url'] }}" alt="">
                </button>
            @endforeach
        </div>

    </div>
</div>
@endif

<div class="carousel-lightbox" id="carousel-lightbox" aria-hidden="true">
    <button class="lightbox-close" aria-label="Close">&#x2715;</button>
    <button class="lightbox-prev" aria-label="Previous">&#8592;</button>
    <button class="lightbox-next" aria-label="Next">&#8594;</button>
    <div class="lightbox-img-wrap">
        <img class="lightbox-img" src="{{ isset($screenshots[0]) ? $screenshots[0]['url'] : "" }}" alt="">
    </div>
    <div class="lightbox-counter">
        <span class="lightbox-counter-current">1</span> / <span class="lightbox-counter-total">{{ count($screenshots) }}</span>
    </div>
</div>

@push('scripts')
<script>
(function () {
    var carousel = document.getElementById('marble-carousel');
    if (!carousel) return;

    var slides     = carousel.querySelectorAll('.carousel-slide');
    var dots       = carousel.querySelectorAll('.carousel-dot');
    var thumbs     = document.querySelectorAll('.carousel-thumb');
    var counter    = carousel.querySelector('.carousel-counter-current');
    var total      = slides.length;
    var current    = 0;

    // Lightbox
    var lightbox  = document.getElementById('carousel-lightbox');
    var lbImg     = lightbox && lightbox.querySelector('.lightbox-img');
    var lbCurrent = lightbox && lightbox.querySelector('.lightbox-counter-current');

    function syncLightbox() {
        if (!lightbox || !lightbox.classList.contains('is-open')) return;
        var src = slides[current].querySelector('.carousel-img');
        if (lbImg)     { lbImg.src = src.src; lbImg.alt = src.alt; }
        if (lbCurrent) lbCurrent.textContent = current + 1;
    }

    function openLightbox() {
        if (!lightbox) return;
        syncLightbox();
        lightbox.classList.add('is-open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lightbox) return;
        lightbox.classList.remove('is-open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function goTo(n) {
        slides[current].classList.remove('is-active');
        dots[current].classList.remove('is-active');
        thumbs[current].classList.remove('is-active');

        current = (n + total) % total;

        slides[current].classList.add('is-active');
        dots[current].classList.add('is-active');
        thumbs[current].classList.add('is-active');
        if (counter) counter.textContent = current + 1;
        syncLightbox();
    }

    carousel.querySelector('.carousel-btn-prev').addEventListener('click', function () { goTo(current - 1); });
    carousel.querySelector('.carousel-btn-next').addEventListener('click', function () { goTo(current + 1); });

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () { goTo(parseInt(dot.dataset.goto)); });
    });
    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () { goTo(parseInt(thumb.dataset.goto)); });
    });

    var btnFs = carousel.querySelector('.carousel-btn-fullscreen');
    if (btnFs) btnFs.addEventListener('click', openLightbox);

    if (lightbox) {
        lightbox.querySelector('.lightbox-close').addEventListener('click', closeLightbox);
        lightbox.querySelector('.lightbox-prev').addEventListener('click', function () { goTo(current - 1); });
        lightbox.querySelector('.lightbox-next').addEventListener('click', function () { goTo(current + 1); });
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox || e.target === lbImg.parentElement) closeLightbox();
        });
    }

    // Keyboard
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowLeft')  goTo(current - 1);
        if (e.key === 'ArrowRight') goTo(current + 1);
        if (e.key === 'Escape')     closeLightbox();
    });
})();
</script>
@endpush

@endsection
