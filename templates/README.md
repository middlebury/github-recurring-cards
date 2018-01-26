Templates allow you to define sets of values that can be inherited across your card
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
