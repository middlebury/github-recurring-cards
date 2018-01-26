About
=====

This program can be run on a cron job to create recurring cards in the [Github](https://github.com/)
system.

Installation
============

1. Clone the Git repository to your machine.

       git clone https://github.com/middlebury/github-recurring-cards.git

2. Install `composer` if you don't have it.

       curl -sS https://getcomposer.org/installer | php

3. Install dependencies via `composer`.

       php composer.phar install

4. Get your account-specific Github API key under **Settings >> OAuth Apps** in Github. You will need this later.

5. Copy the `config.php.example` file to `config.php` and configure your values.

6. Manually edit vendor/knplabs/github-api/lib/Github/Api/AcceptHeaderTrait.php to read

      protected $acceptHeaderValue = 'application/vnd.github.inertia-preview+json';

Usage
=====

The `bin/grc_cron` command should be set up to run via cron no more frequently than
once per hour. The command has one required argument `--cron-freq-hours=<hours>`
which takes an integer number of hours that indicate time-window between cron runs (minimum 1 hour).

Card definitions should be placed in the `cards/` directory as `.json` files.

You can test the schedule of card creation by using the `bin/grc_test` command which
will run through all of your card definitions for each cron-run in the time-frame you
specify and will provide a statement of when cards would be created without actually
creating them. Example:

    bin/grc_test --start-date=2016-01-01 --end-date=2017-01-01 --cron-freq-hours=24

Card Definitions
----------------

Add `.json` files to the `cards/` directory that define each of your recurring
cards. The `hours`, `start_date`, `recurrence`, and `board` properties are all
required. The additional properties are defined by the
[Github's `create issue` method](https://developer.github.com/v3/issues/#create-an-issue).
Custom fields are supported and can be specified by name.

    {
      "hour": 8,
      "start_date": "2016-01-01",
      "recurrence": "FREQ=WEEKLY;BYDAY=MO",
      "board": 2,
      "column": 123456,
      "title": "Check for updates to the system",
      "body": "Documentation can be found in the <a href=\"http://wiki.example.edu/system_updates\">wiki</a>.",
      "labels": "WordPress,Periodic"
    }

* **hour** - An integer from 0-23. The hour at which this recurrence should occur.

* **start_date** - A date string of the form YYYY-MM-DD. The date on which recurrence begins.
  Think of this like the first date of a recurring calendar item. This may effect the offset
  of the recurrences if they are something like "every 2 days".

* **recurrence** - A recurrence rule string as defined by
  [RFC2445 (iCalendar specification)](https://tools.ietf.org/html/rfc2445#section-4.3.10) and implemented by the
  [Recurr library](https://github.com/simshaun/recurr).

  Examples:

  * `FREQ=WEEKLY;BYDAY=MO` - Weekly on Mondays.
  * `FREQ=MONTHLY;BYMONTHDAY=1` - Monthly on the first of the month.
  * `FREQ=MONTHLY;BYDAY=MO;BYSETPOS=1` - Monthly on the first Monday of the month.
  * `FREQ=YEARLY;BYMONTH=2,6,9;BYMONTHDAY=1` - 3 times per year on Feb 1, Jun 1, and Sept 1.

Templates
---------
Templates allow you to to define sets of default values that can be inherited across your card
definitions in cards that specify the template in their `templates` property:

    {
      "templates": ["weekly"],
      "title": "Updates WP plugins and themes",
      ...
    }

Here's an example of a template you might put at `templates/weekly.json`:

    {
      "hour": 8,
      "start_date": "2016-01-01",
      "recurrence": "FREQ=WEEKLY;BYDAY=MO"
    }

Template values only get applied if the value is not specified in the card definition
itself or a previous template (if multiple templates are applied).

Copyright and License
===================
This software is Copyright © *The President and Fellows of Middlebury College* and is provided as Free Software under the terms of the [GPLv3 (or later) license](http://www.gnu.org/licenses/gpl-3.0.en.html).

Authors
-------
* Adam Franco
* Ian McBride
