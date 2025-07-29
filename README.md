# SpicyBeats Events

This project is a simple PHP demo for submitting and voting on deals.
Users can log in, submit deals and vote. The main page now includes a
"Sort by" option for ordering deals by recent submissions, vote count
or rating. A logout endpoint (`logout.php`) has been added as well.

## Setup

Create a MySQL database using `config/spicybeats_schema.sql` and provide the
connection credentials via environment variables:

```
export DB_HOST=localhost
export DB_NAME=your_database
export DB_USER=your_user
export DB_PASS=your_pass
export ADMIN_EMAIL=admin@example.com
```

Run the PHP files through a local server of your choice.
