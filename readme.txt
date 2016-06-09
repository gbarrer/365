
Get a calendar view (REST)

Minimum required scope: one of the following:

    https://outlook.office.com/calendars.read
    wl.calendars
    wl.contacts_calendars

Get the occurrences, exceptions, and single instances of events in a calendar view defined by a time range, from the user's primary calendar (../me/calendarview) or from a different calendar.

GET https://outlook.office.com/api/v2.0/me/calendarview?startDateTime={start_datetime}&endDateTime={end_datetime}
GET https://outlook.office.com/api/v2.0/me/calendars/{calendar_id}/calendarview?startDateTime={start_datetime}&endDateTime={end_datetime}

Required parameter	Type	Description
Header parameters
Prefer:	outlook.timezone	The default time zone for events in the response.
URL parameters
calendar_id	string	The calendar ID, if you're getting a calendar view from a specific calendar.
start_datetime	datetimeoffset	The date and time when the event starts.
end_datetime	datetimeoffset	The date and time when the event ends.

Use the Prefer: outlook.timezone header to specify the time zone to use for the event start and end times in the response. If the event was created in a different time zone, the start and end times will be adjusted to the specified time zone. See this list for the supported time zone names. If the Prefer: outlook.timezone header is not specified, the start and end times are returned in UTC.

Note By default, each event in the response includes all its properties. Use $select to specify only those properties you need for best performance. The Id property is always returned. See OData query parameters for filtering, sorting, and paging parameters.

For example, get the calendar view for the month of October, returning only the Subject property for each event. Assuming that the Prefer: outlook.timezone header is not included in the request, the time zone will be UTC.

GET https://outlook.office.com/api/v2.0/me/calendarview?startDateTime=2014-10-01T01:00:00&endDateTime=2014-10-31T23:00:00&$select=Subject

Response type

The expanded events within the specified time range.
