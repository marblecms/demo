<?php

namespace MarbleCms\Newsletter\Components;

use Illuminate\View\Component;

/**
 * Newsletter subscription form component.
 *
 * Usage:
 *   <x-newsletter::subscribe-form />
 *   <x-newsletter::subscribe-form :list-id="1" redirect="/thank-you" />
 *   <x-newsletter::subscribe-form show-name="true" button-label="Join us" />
 *
 * Override the view by publishing newsletter-views.
 */
class SubscribeForm extends Component
{
    public function __construct(
        public ?int    $listId      = null,
        public string  $redirect    = '/',
        public bool    $showName    = false,
        public string  $buttonLabel = 'Subscribe',
        public string  $placeholder = 'Your email address',
    ) {
    }

    public function render()
    {
        return view('newsletter::components.subscribe-form');
    }
}
