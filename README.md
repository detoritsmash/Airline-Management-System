# System Architecture - Airline Management System
 
Project repo: https://github.com/detoritsmash/Airline-Management-System
 
## What this project does
A basic airline booking website. A passenger can register, log in, search flights
between two cities, book a flight, and view/cancel their bookings from a dashboard.
There's also a separate Python script that reads the same database and prints/plots
some basic stats (total bookings, revenue by airline, popular routes).
 
## Architecture (basic 3-layer setup)
 
```
   [ Browser ]
        |
        |  HTTP request (GET/POST)
        v
 [ PHP Backend - backend-php/ ]
   - index.php        (search flights)
   - login.php         (passenger login)
   - register.php      (passenger signup)
   - book_flight.php   (booking + confirm)
   - dashboard.php      (view/cancel bookings)
   - logout.php
        |
        |  SQL queries (via mysqli, prepared statements)
        v
   [ MySQL Database ]
     Airlines123123 (schema.sql)
        ^
        |  read-only queries
        |
 [ Python Analytics - scripts-python/ ]
   - db_connect.py
   - analytics.py  (uses matplotlib/seaborn to make charts)
```
 
## Layers, explained simply
 
**Frontend** - There's no separate frontend framework. Every PHP file prints its own
HTML directly (with inline CSS in a `<style>` tag at the top). So frontend and backend
are mixed together in the same .php files.
 
**Backend (PHP)** - Handles all the logic: reading form input, running SQL queries,
session handling (`$_SESSION['user_id']` for login state), redirects after actions.
All DB access goes through `config/db.php` which opens one mysqli connection.
 
**Database (MySQL)** - Single database called `Airlines123123`, 8 tables. Details in
the Database Design doc.
 
**Python analytics module** - Not connected to the PHP site directly. It's a standalone
script someone would run separately from the terminal to get reports/graphs from the
same database (revenue, popular routes etc).
 
## Tech used
- PHP (mysqli, prepared statements, sessions)
- MySQL
- Plain HTML/CSS (no JS framework, no Bootstrap - just inline styles)
- Python (mysql-connector, pandas-free, matplotlib, seaborn, tabulate)
## Notes / things I'd flag if this was a real project
- DB credentials are hardcoded in `config/db.php` (root, no password) - fine for a
  local student project, not fine for production.
- No `.env` file / config separation.
- Everything is server-rendered PHP, no API layer, no JS fetch calls - see the
  API/Request Flow doc for how pages actually talk to the DB.```text
```
airline-management-system/
│
├── database/
│   └── schema.sql          # The exported MySQL database structure
│
├── backend-php/
│   ├── config/
│   │   └── db.php          # Database connection file
│   ├── index.php           # Main landing/flight search page
│   ├── book_flight.php     # Booking logic
│   └── dashboard.php       # User/Admin dashboard
│
├── scripts-python/
│   ├── venv/               # Python virtual environment (ignored by git)
│   ├── db_connect.py       # Python database connection utility
│   └── analytics.py        # E.g., predicting popular routes or automating flight status updates
│
├── .gitignore
└── README.md               # Documentation of the project
```
