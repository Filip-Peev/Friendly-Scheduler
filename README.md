# 📅 Friendly Scheduler

A simple PHP + SQLite web application for scheduling meetings with friends and collecting RSVPs.

## Features

* Create meeting proposals
* Add meeting date, time, and details
* RSVP with **Yes** or **No**
* Update an existing RSVP
* Lock past meetings from editing
* Delete upcoming meetings
* Monthly meeting view
* Activity logging

## Built With

* PHP
* SQLite
* Bootstrap 5

## Getting Started

1. Clone the repository.
2. Place the project in your PHP web server directory.
3. Start your local server.
4. Open the project in your browser.

The SQLite database (`scheduler.db`) is created automatically on first run.

## Project Structure

```text
.
├── index.php
├── scheduler.db        # Created automatically
├── app_activity.log    # Created automatically
```

## License

This project is available under the MIT License.
