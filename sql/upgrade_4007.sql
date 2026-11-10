ALTER TABLE `civicrm_mailboxmailing_mail_settings`
  ADD COLUMN `use_default_header` tinyint DEFAULT 0 COMMENT 'Whether to use the default CiviCRM mailing header.' AFTER `signature_html`;
