ALTER TABLE `civicrm_mailboxmailing_mail_settings`
  ADD COLUMN `replyto_email` varchar(255) COMMENT 'Reply-To e-mail address to use for mailings.' AFTER `from_email_address_id`;
