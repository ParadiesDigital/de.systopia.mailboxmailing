ALTER TABLE `civicrm_mailboxmailing_mail_settings`
  ADD COLUMN `signature_html` longtext COMMENT 'HTML signature appended to mailings, before the regular CiviCRM footer.' AFTER `replyto_email`;
