# Marble Newsletter

Double opt-in newsletter management for [Marble CMS](https://github.com/marblecms/admin): subscriber lists, HTML campaigns, open/click tracking.

## Installation

```bash
composer require marblecms/marble-newsletter
php artisan marble:newsletter:install
```

Publish config:

```bash
php artisan vendor:publish --tag=newsletter-config
```

## Configuration (`config/newsletter.php`)

| Key | Default | Description |
|-----|---------|-------------|
| `from_name` | `APP_NAME` | Sender name |
| `from_email` | `MAIL_FROM_ADDRESS` | Sender email |
| `double_opt_in` | `true` | Require email confirmation |
| `confirmation_subject` | `'Please confirm…'` | Confirmation email subject |
| `confirmation_view` | `newsletter::emails.confirmation` | Confirmation email view |

## Frontend component

```blade
{{-- Basic --}}
<x-newsletter::subscribe-form />

{{-- With list and redirect --}}
<x-newsletter::subscribe-form :list-id="1" redirect="/thank-you" />

{{-- With name field and custom button --}}
<x-newsletter::subscribe-form :show-name="true" button-label="Join us" />
```

Props:
- `list-id` — attach subscriber to this list (optional)
- `redirect` — URL to redirect to after submit (default `/`)
- `show-name` — display a name input field (default `false`)
- `button-label` — submit button text (default `Subscribe`)
- `placeholder` — email input placeholder

## Double opt-in flow

1. Visitor submits the subscribe form
2. If `double_opt_in = true`, a `ConfirmationMail` is sent with a unique token link
3. Visitor clicks the link → `GET /newsletter/confirm/{token}` → status set to `confirmed`
4. `SubscriberConfirmed` event is fired

## Unsubscribe

Add `{{unsubscribe_url}}` anywhere in your campaign body. The mailing service replaces this with the subscriber's unique unsubscribe URL before sending.

## Tracking

- **Open tracking**: a 1×1 transparent GIF pixel is appended to every campaign email
- **Click tracking**: wrap links using `route('newsletter.track.click', [$token, base64_encode($url)])`

## Admin UI

Navigate to **Newsletter** in the top nav:

| Section | Description |
|---------|-------------|
| Overview | Stats: confirmed subscribers, lists, sent campaigns |
| Subscribers | Paginated list with status, lists, delete |
| Lists | Create/delete subscriber lists |
| Campaigns | Create, edit, view stats, send |

## Artisan Commands

| Command | Description |
|---------|-------------|
| `marble:newsletter:install` | Run migrations |
| `marble:newsletter:send {campaign}` | Send campaign by ID (for large lists / cron) |

## Events

| Event | Fired when |
|-------|-----------|
| `SubscriberConfirmed` | Subscriber confirms opt-in |
| `SubscriberUnsubscribed` | Subscriber unsubscribes |
| `CampaignSent` | Campaign finished sending |

## Database tables

| Table | Description |
|-------|-------------|
| `newsletter_subscribers` | Subscriber records |
| `newsletter_lists` | Named groups |
| `newsletter_subscriber_list` | Pivot: subscriber ↔ list |
| `newsletter_campaigns` | Campaign records |
| `newsletter_sends` | One row per subscriber per campaign |
| `newsletter_opens` | Open tracking events |
| `newsletter_clicks` | Click tracking events |
