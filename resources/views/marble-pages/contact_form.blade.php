@extends('layouts.frontend')

@section('title', $item->value('name') . ' — ' . config('app.name'))

@section('content')
<div class="content-card">
    <h1>{{ $item->value('form_title') ?: $item->value('name') }}</h1>

    @if($item->value('intro_text'))
        <p class="intro">{{ $item->value('intro_text') }}</p>
    @endif

    <x-marble::marble-form :item="$item" :hide-submit="true">
        <button type="submit" class="btn-submit">Send message</button>
    </x-marble::marble-form>
</div>
@endsection

@push('styles')
<style>
    .intro { color: #555; margin-bottom: 28px; font-size: 15px; }

    .marble-form-field { margin-bottom: 20px; }
    .marble-form-field label {
        display: block; font-weight: 600; font-size: 13px; color: #555;
        margin-bottom: 6px; text-transform: uppercase; letter-spacing: .4px;
    }
    .marble-form-field input[type=text],
    .marble-form-field input[type=email],
    .marble-form-field input[type=tel],
    .marble-form-field input[type=number],
    .marble-form-field select,
    .marble-form-field textarea {
        width: 100%; padding: 10px 12px; border: 1px solid #d0d8e4; border-radius: 4px;
        font-size: 14px; font-family: inherit; color: #333; background: #fafcff;
        transition: border-color .15s, box-shadow .15s; box-sizing: border-box;
    }
    .marble-form-field input:focus,
    .marble-form-field textarea:focus,
    .marble-form-field select:focus {
        outline: none; border-color: #2258A8; box-shadow: 0 0 0 3px rgba(34,88,168,.1); background: #fff;
    }
    .marble-form-field textarea { resize: vertical; min-height: 120px; }

    .btn-submit {
        background: linear-gradient(to bottom, #2258A8 0%, #163C80 100%);
        color: #fff; border: none; padding: 11px 28px; border-radius: 4px;
        font-size: 14px; font-weight: 600; cursor: pointer; letter-spacing: .3px;
        transition: opacity .15s;
    }
    .btn-submit:hover { opacity: .9; }

    .marble-form-success {
        background: #eafaf1; border: 1px solid #27ae60; color: #1a7a43;
        padding: 16px 20px; border-radius: 4px; margin-bottom: 24px; font-weight: 500;
    }
</style>
@endpush
