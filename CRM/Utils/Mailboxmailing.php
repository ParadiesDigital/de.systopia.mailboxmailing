<?php
/*-------------------------------------------------------+
| SYSTOPIA Mailbox Mailing Extension                     |
| Copyright (C) 2019 SYSTOPIA                            |
| Author: J. Schuppe (schuppe@systopia.de)               |
+--------------------------------------------------------+
| This program is released as free software under the    |
| Affero GPL license. You can redistribute it and/or     |
| modify it under the terms of this license which you    |
| can read by viewing the included agpl.txt or online    |
| at www.gnu.org/licenses/agpl.html. Removal of this     |
| copyright header is strictly prohibited without        |
| written permission from the original author(s).        |
+--------------------------------------------------------*/

use CRM_Mailboxmailing_ExtensionUtil as E;

class CRM_Utils_Mailboxmailing {

  /**
   * Retrieves the CiviCRM API parameter name representation of a custom field
   * within the Mailboxmailing extension's custom group for Mailing entities.
   *
   * @param $field_name
   *   The name of the custom field.
   *
   * @return string
   *   The CiviCRM API parameter name representation of the custom field
   *   ("custom_<id>").
   * @throws \CRM_Core_Exception
   */
  public static function resolveCustomField($field_name) {
    $custom_field = civicrm_api3('CustomField', 'getsingle', array(
      'custom_group_id' => 'mailing_mailboxmailing',
      'name' => $field_name,
    ));
    return 'custom_' . $custom_field['id'];
  }

  /**
   * Decodes HTML entities (also when encoded multiple times).
   */
  public static function decodeEntities($string) {
    $string = (string) $string;
    for ($i = 0; $i < 3 && strpos($string, '&') !== FALSE; $i++) {
      $decoded = html_entity_decode($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
      if ($decoded === $string) {
        break;
      }
      $string = $decoded;
    }
    return $string;
  }

  /**
   * Reverts HTML-escaping of special characters (&lt; &gt; &quot; &#039;),
   * also when applied multiple times, so that entered HTML stays HTML.
   */
  public static function decodeHtmlSpecialChars($string) {
    $string = (string) $string;
    for ($i = 0; $i < 3 && preg_match('/&(lt|gt|quot|#0?39|amp);/i', $string); $i++) {
      $decoded = htmlspecialchars_decode($string, ENT_QUOTES);
      if ($decoded === $string) {
        break;
      }
      $string = $decoded;
    }
    return $string;
  }

  /**
   * Options for the "From e-mail address" select: option value => plain label.
   * Labels are intentionally NOT HTML-escaped here (the form escapes them).
   */
  public static function getFromEmailAddressOptions() {
    $options = array();
    foreach ((array) CRM_Core_OptionGroup::values('from_email_address') as $value => $label) {
      $options[$value] = static::decodeEntities($label);
    }
    return $options;
  }

  /**
   * Resolves a stored from address (option value, or legacy stored label
   * string) into its parts. Falls back to the domain default address.
   *
   * @return array
   *   Keys: name, email, formatted.
   */
  public static function resolveFromAddress($stored = NULL) {
    $label = NULL;
    if (!empty($stored)) {
      $options = static::getFromEmailAddressOptions();
      $label = isset($options[$stored]) ? $options[$stored] : static::decodeEntities($stored);
    }
    if (empty($label)) {
      $default = CRM_Core_BAO_Domain::getNameAndEmail(FALSE, TRUE);
      $label = is_array($default) ? reset($default) : $default;
      $label = static::decodeEntities($label);
    }
    $name = '';
    $email = trim($label);
    if (preg_match('/^\s*"?(.*?)"?\s*<([^>]+)>\s*$/', $label, $matches)) {
      $name = trim($matches[1]);
      $email = trim($matches[2]);
    }
    return array(
      'name' => $name,
      'email' => $email,
      'formatted' => ($name !== '' ? '"' . str_replace('"', '', $name) . '" <' . $email . '>' : $email),
    );
  }

  /**
   * Prepares several object types used by this extension for usage as Smarty
   * variables, transforming them into arrays and filtering for relevant fields.
   *
   * @param array $input_vars
   *   An associative array of variables to make Smarty-compatible. The key of
   *   each array element will be the name of the Smarty variable. Depending on
   *   the class or type of the element, the returned value will be a - possibly
   *   filtered - array representation of the original value.
   *
   * @return array
   */
  public static function getSmartyVariables($input_vars) {
    $variables = array();

    foreach ($input_vars as $input_var_name => $input_var) {
      if (is_a($input_var, '\ezcMail')) {
        /* @var \ezcMail $input_var */
        // All properties accessible through magic getter \ezcMail::__get().
        $mail_properties = array(
          'to',
          'cc',
          'bcc',
          'from',
          'subject',
          'subjectCharset',
          'body',
          'messageId',
          'returnPath',
          'timestamp',
        );
        foreach ($mail_properties as $mail_property) {
          $variables[$input_var_name][$mail_property] = json_decode(json_encode($input_var->$mail_property), TRUE);
        }
      }
      elseif (is_a($input_var, '\CRM_Mailboxmailing_BAO_MailboxmailingMailSettings')) {
        /* @var \CRM_Mailboxmailing_BAO_MailboxmailingMailSettings $input_var */
        $variables[$input_var_name] = $input_var->toArray();
      }
      elseif (is_object($input_var)) {
        /* @var object $input_var */
        $variables[$input_var_name] = (array) $input_var;
      }
      else {
        $variables[$input_var_name] = $input_var;
      }
    }

    return $variables;
  }

}
