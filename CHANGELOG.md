## 1.0.4

- Limit messaging on the form for submit only.

## 1.0.3

- Form validation and error messaging added.

## 1.0.2

- Bug fix. Tags after invalid HTML markup were not being swapped.  To fix this problem, we now fetch all tag nodes and then loop through them all (which is slower obviously).
- Added processing time and number of tags swapped stats to let the person know what happed.
- Limiting `h1` tag swap to only one occurrence.
    - TODO-Tonya - need to generate a report to let the person know that there are only tags to be replaced in a document when using a `h1`.

## 1.0.1

Search attribute value comparison made more robust.

## 1.0.0

Initial release.