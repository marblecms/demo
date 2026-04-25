@if(session('newsletter_subscribed'))
<div class="alert alert-success">{{ session('newsletter_subscribed') }}</div>
@endif

<form method="POST" action="{{ route('newsletter.subscribe') }}">
    @csrf
    <input type="hidden" name="redirect" value="{{ $redirect }}">
    @if($listId)
    <input type="hidden" name="list_id" value="{{ $listId }}">
    @endif

    @if($showName)
    <div class="form-group">
        <input type="text"
               name="name"
               class="form-control"
               placeholder="Your name"
               value="{{ old('name') }}">
    </div>
    @endif

    <div class="form-group">
        <input type="email"
               name="email"
               class="form-control"
               placeholder="{{ $placeholder }}"
               value="{{ old('email') }}"
               required>
        @error('email')
        <span class="help-block text-danger">{{ $message }}</span>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">{{ $buttonLabel }}</button>
</form>
