# Meeting Scheduler

A lightweight, single-file meeting scheduler with RSVP functionality. Propose meeting dates, let friends vote yes or no, and track responses — all backed by SQLite.

## Features

- **Create meetings** with title, date, time, and optional details
- **RSVP system** — friends can vote Yes or No, and update their response
- **Past meeting protection** — responses and deletes are blocked for past dates
- **Quick templates** — one-click scheduling for common hangouts (coffee, dinner, games, etc.)
- **Month-based navigation** — browse meetings by month
- **Activity logs** — full audit trail of all actions, color-coded by type
- **Responsive** — works on mobile and desktop via Bootstrap 5

## Tech Stack

- PHP 8+
- SQLite 3 (via PDO)
- Bootstrap 5.3 (CDN)

## Setup

1. Place files in your web server's document root (e.g., `htdocs/rsvp/`)
2. Make sure the web server has write permissions for the directory (SQLite creates `scheduler.db` automatically)
3. Navigate to `http://localhost/rsvp/` in your browser

No composer, no database setup, no config files — just drop and run.

## File Overview

| File | Purpose |
|------|---------|
| `index.php` | Main app — create meetings, RSVP, view by month |
| `templates.php` | Quick-add buttons for common meeting types |
| `logs.php` | Activity log viewer (last 500 entries) |

## License

MIT
