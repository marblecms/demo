@extends('layouts.frontend')

@section('title', $item->value('subject') ?: $item->name())

@section('content')

@php
    $subject  = $item->value('subject') ?: $item->name();
    $message  = $item->value('message');

@endphp

{{-- Breadcrumb --}}
<x-breadcrumb :item="$item" />

<div class="contact-layout">
    <div class="contact-main">
        <div class="content-card">
            <h1>{{ $subject }}</h1>
            @if($message)
                <p class="contact-intro">{{ $message }}</p>
            @endif

            <x-marble::marble-form :item="$item" :hide-submit="true">
                <button type="submit" class="btn-submit">
                    &#9993; Send message
                </button>
            </x-marble::marble-form>
        </div>
    </div>

    <aside class="contact-sidebar">
        <div class="contact-info-card">
            <h3>Get in touch</h3>
            <div class="contact-info-item">
                <div class="contact-info-icon">&#128205;</div>
                <div>
                    <div class="contact-info-label">Address</div>
                    <div class="contact-info-value">Marble Street 1<br>Vienna, Austria</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-info-icon">&#128222;</div>
                <div>
                    <div class="contact-info-label">Phone</div>
                    <div class="contact-info-value">+43 1 234 5678</div>
                </div>
            </div>
            <div class="contact-info-item">
                <div class="contact-info-icon">&#9993;</div>
                <div>
                    <div class="contact-info-label">Email</div>
                    <div class="contact-info-value">hello@marble-cms.io</div>
                </div>
            </div>
        </div>
        <div class="contact-response-card">
            <div class="contact-response-icon">&#9201;</div>
            <div class="contact-response-text">We typically respond within <strong>1 business day</strong>.</div>
        </div>
    </aside>
</div>

@endsection
