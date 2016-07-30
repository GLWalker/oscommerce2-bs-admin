<?php
/**
  * osCommerce Online Merchant
  *
  * @copyright (c) 2016 osCommerce; https://www.oscommerce.com
  * @license GPL; https://www.oscommerce.com/gpllicense.txt
  */

  use OSC\OM\Apps;
  use OSC\OM\HTML;
  use OSC\OM\OSCOM;

  require('includes/application_top.php');

  if (OSCOM::hasSitePage()) {
    if (OSCOM::isRPC() === false) {
        $page_file = OSCOM::getSitePageFile();

        if (empty($page_file) || !file_exists($page_file)) {
          $page_file = DIR_FS_CATALOG . 'includes/error_documents/404.php';
        }

        include($page_file);
    }

    goto main_sub3;
  }

  $languages = tep_get_languages();
  $languages_array = array();
  $languages_selected = DEFAULT_LANGUAGE;
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $languages_array[] = array('id' => $languages[$i]['code'],
                               'text' => $languages[$i]['name']);
    if ($languages[$i]['directory'] == $_SESSION['language']) {
      $languages_selected = $languages[$i]['code'];
    }
  }

  require(DIR_WS_INCLUDES . 'template_top.php');
?>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2" height="40">
          <tr>
            <td class="pageHeading"><?php echo STORE_NAME; ?></td>

<?php
  if (sizeof($languages_array) > 1) {
?>

            <td class="pageHeading" align="right"><?php echo HTML::form('adminlanguage', OSCOM::link(FILENAME_DEFAULT), 'get', null, ['session_id' => true]) . HTML::selectField('language', $languages_array, $languages_selected, 'onchange="this.form.submit();"') . '</form>'; ?></td>

<?php
  }
?>

          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( defined('MODULE_ADMIN_DASHBOARD_INSTALLED') && tep_not_null(MODULE_ADMIN_DASHBOARD_INSTALLED) ) {
    $adm_array = explode(';', MODULE_ADMIN_DASHBOARD_INSTALLED);

    $col = 0;

    for ( $i=0, $n=sizeof($adm_array); $i<$n; $i++ ) {
      $adm = $adm_array[$i];

      if (strpos($adm, '\\') !== false) {
        $class = Apps::getModuleClass($adm, 'AdminDashboard');
      } else {
        $class = substr($adm, 0, strrpos($adm, '.'));

        if ( !class_exists($class) ) {
          include(DIR_WS_LANGUAGES . $_SESSION['language'] . '/modules/dashboard/' . $adm);
          include(DIR_WS_MODULES . 'dashboard/' . $class . '.php');
        }
      }

      $ad = new $class();

      if ( $ad->isEnabled() ) {
        if ($col < 1) {
          echo '          <tr>' . "\n";
        }

        $col++;

        if ($col <= 2) {
          echo '            <td width="50%" valign="top">' . "\n";
        }

        echo $ad->getOutput();

        if ($col <= 2) {
          echo '            </td>' . "\n";
        }

        if ( !isset($adm_array[$i+1]) || ($col == 2) ) {
          if ( !isset($adm_array[$i+1]) && ($col == 1) ) {
            echo '            <td width="50%" valign="top">&nbsp;</td>' . "\n";
          }

          $col = 0;

          echo '  </tr>' . "\n";
        }
      }
    }
  }
?>
        </table></td>
      </tr>
    </table>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');

  main_sub3: // Sites and Apps skip to here

  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>
