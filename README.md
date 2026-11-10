# Mailbox Mailing

This extension allows CiviCRM mailings be created by sending e-mails to
mailboxes, that are configured to be scanned for incoming messages regularly.
This makes it possible to let people create and schedule CiviCRM mailings sent
to a configurable group of recipients without using the CiviCRM user interface.

## New Features compared to original extension

- Added a html ready signature (added after mail body)
- Added a checkbox to "Use default mailing header" (default: off).
- Mailing sender: The name is taken from the incoming email or, if not available, from the contact. The email address is always the one configured. If no selection is made, the default domain address is used.
- Reply-To: New field "Reply-To email address" in the mailbox settings. If it is empty, the original sender’s address is used as before.
- Sender selection: The clean option ID is now saved, and "/" and "&" are decoded.

## Features

-   Configurable mailboxes (or folders within a single mailbox) to be used as
    mailing lists
-   Regular scanning and processing of mailings via cron job
-   Support for file attachments and multipart e-mail
-   Bounce processing and reporting to the mailing author
-   Configurable allowed senders and recipients using CiviCRM groups
-   Notification of recipients about disallowed answers to a mailing e-mail
-   Archiving sent mailings
-   Token replacement in mailing subject

## Installation

Install as any other CiviCRM extension.
