@extends('layouts.frontend')

@section('title', $item->value('subject') ?: $item->name())

@section('content')

@php
    $subject  = $item->value('subject') ?: $item->name();
    $message  = $item->value('message');

    // Breadcrumb
    $parent   = $item->parent_id ? \Marble\Admin\Models\Item::find($item->parent_id) : null;
@endphp

{{-- Breadcrumb --}}
<nav class="breadcrumb">
    <a href="/">Home</a>
    @if($parent)
        <span class="breadcrumb-sep">/</span>
        <a href="{{ \Marble\Admin\Facades\Marble::url($parent) }}">{{ $parent->name() }}</a>
    @endif
    <span class="breadcrumb-sep">/</span>
    <span>{{ $subject }}</span>
</nav>

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

@push('styles')
<style>
    .contact-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 24px;
        align-items: start;
    }

    .contact-intro {
        font-size: 15px;
        color: #555;
        margin-bottom: 28px;
        line-height: 1.7;
    }

    /* Form fields */
    .marble-form-field { margin-bottom: 20px; }
    .marble-form-field label {
        display: block;
        font-weight: 700;
        font-size: 12px;
        color: #666;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .marble-form-field input[type=text],
    .marble-form-field input[type=email],
    .marble-form-field input[type=tel],
    .marble-form-field input[type=number],
    .marble-form-field select,
    .marble-form-field textarea {
        width: 100%;
        padding: 11px 14px;
        border: 2px solid #e0e8f0;
        border-radius: 7px;
        font-size: 14px;
        font-family: inherit;
        color: #333;
        background: #fafcff;
        transition: border-color .15s, box-shadow .15s;
    }
    .marble-form-field input:focus,
    .marble-form-field textarea:focus,
    .marble-form-field select:focus {
        outline: none;
        border-color: #2258A8;
        box-shadow: 0 0 0 3px rgba(34,88,168,.1);
        background: #fff;
    }
    .marble-form-field textarea { resize: vertical; min-height: 130px; }

    .btn-submit {
        background: linear-gradient(135deg, #2258A8 0%, #3370cc 100%);
        color: #fff;
        border: none;
        padding: 13px 32px;
        border-radius: 7px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        letter-spacing: .2px;
        transition: opacity .15s, transform .1s;
    }
    .btn-submit:hover { opacity: .9; transform: translateY(-1px); }

    .marble-form-success {
        background: #eafaf1;
        border: 1px solid #27ae60;
        color: #1a7a43;
        padding: 16px 20px;
        border-radius: 7px;
        margin-bottom: 24px;
        font-weight: 600;
        font-size: 15px;
    }
    .marble-form-error {
        background: #fef0f0;
        border: 1px solid #e74c3c;
        color: #c0392b;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 13px;
        margin-top: 4px;
    }

    /* Sidebar */
    .contact-info-card {
        background: #fff;
        border-radius: 10px;
        padding: 24px;
        box-shadow: 0 1px 6px rgba(0,0,0,.07);
        border: 1px solid #e8edf3;
        margin-bottom: 14px;
    }
    .contact-info-card h3 {
        font-size: 14px;
        font-weight: 800;
        color: #0f1a2e;
        margin: 0 0 18px;
        text-transform: uppercase;
        letter-spacing: .6px;
    }
    .contact-info-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 18px;
    }
    .contact-info-item:last-child { margin-bottom: 0; }
    .contact-info-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
    .contact-info-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: #aaa; margin-bottom: 3px; }
    .contact-info-value { font-size: 13px; color: #444; line-height: 1.5; }

    .contact-response-card {
        background: #f0f7ff;
        border: 1px solid #c8dff5;
        border-radius: 8px;
        padding: 14px 16px;
        display: flex;
        gap: 10px;
        align-items: center;
        font-size: 13px;
        color: #445;
    }
    .contact-response-icon { font-size: 20px; flex-shrink: 0; }
    .contact-response-text { line-height: 1.5; }

    @media (max-width: 768px) {
        .contact-layout { grid-template-columns: 1fr; }
    }
</style>
@endpush
