<?php

/**
 * Log Analyser actions.
 *
 * @package    ttBerichtPlugin
 * @author     Taco Orens
 * @version    1.0
 */

class ttLogAnalyseActions extends sfActions
{

  /**
   *
   */
  public function executeIndex()
  {
    set_time_limit(0);
    bcscale(2);
    $detailPerFile = array();
    // Ophalen van alle bestanden in de log
    if ($handle = opendir(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'log')) {
      while (false !== ($entry = readdir($handle))) {
        if (strpos($entry, '.log') !== false) {
          $detailPerFile[$entry] = array('template' => array(), 'database' => array());

          $source_file = fopen(SF_ROOT_DIR . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . $entry, 'r');
          while (!feof($source_file)) {
            // Read linext line
            $buffer = fgets($source_file, 1024);

            // First {sfTimerManager} found
            $key = 'unknown';
            $parsedLineInfo = self::parseLine($buffer);

            // Analyse 1 - Template timers 
            if ($parsedLineInfo['line_type'] == 'timer') {
              $fullDetail = '';
              $action = '';
              $totalTimer = 0;
              $dbaseTimer = $templateTimer = 0;
              $queryCount = 0;
              while ($parsedLineInfo['line_type'] == 'timer') {
                if (isset($parsedLineInfo['action'])) {
                  $key = $parsedLineInfo['action'];
                } elseif ($parsedLineInfo['time_type'] == 'Database') {
                  $dbaseTimer = $parsedLineInfo['time'];
                  $queryCount = $parsedLineInfo['query_count'];
                } elseif ($parsedLineInfo['time_type'] == 'View') {
                  $templateTimer = $parsedLineInfo['time'];
                }
                $fullDetail .= $parsedLineInfo['raw_detail'] . '<br/>';
                $totalTimer = bcadd($totalTimer, $parsedLineInfo['time']);
                // Read next line
                $buffer = fgets($source_file, 1024);
                $parsedLineInfo = self::parseLine($buffer);
              }

              if ($totalTimer >= 2000 || $dbaseTimer >= 500 || $queryCount > 120) {
                if ((isset($detailPerFile[$entry]['template'][$key]) && count($detailPerFile[$entry]['template'][$key]) == 5) || $key == 'ttLogAnalyse/index') {
                  continue;
                }
                $detailPerFile[$entry]['template'][$key][] = array('time' => $totalTimer,
                  'database_timer' => $dbaseTimer,
                  'view_timer' => $templateTimer,
                  'detail' => $fullDetail,
                  'action' => $key);

              }
            } // Analyse 2 - Database timers
            else if ($parsedLineInfo['line_type'] == 'database' && $parsedLineInfo['time'] >= 120) {
              $detailPerFile[$entry]['database'][base64_encode($parsedLineInfo['query'])] = $parsedLineInfo;
            }
          }

        }
      }
      closedir($handle);
    }

    $this->detailPerFile = $detailPerFile;
  }

  /**
   *  Return parsed data
   *
   */
  private static function parseLine($line)
  {
    $parsedLineInfo = array('line_type' => false);

    // Case 1 - Timerline
    if (strpos($line, '{sfTimerManager}') !== false) {
      $parsedLineInfo['line_type'] = 'timer';
      $detail = substr($line, strpos($line, '{sfTimerManager}') + 17);
      $parsedLineInfo['raw_detail'] = $detail;

      $tmp = substr($detail, 0, strpos($detail, ' ms'));
      $parsedLineInfo['time'] = (float)substr($tmp, strrpos($tmp, ' '));


      $type = substr($detail, 0, strpos($detail, ' '));
      if ($type == 'Action') {
        $key = substr($detail, strpos($detail, '"') + 1, strrpos($detail, '"') - strpos($detail, '"') - 1);
        $parsedLineInfo['action'] = $key;
      } else {
        $parsedLineInfo['time_type'] = $type;
      }

      if ($type == 'Database')
      {
        $parsedLineInfo['query_count'] = substr($detail, strpos($detail, '(') + 1, strpos($detail, ')') -25);
      }
    } // Case 2 - Database
    else if (strpos($line, 'executeQuery():') !== false) {
      $parsedLineInfo['line_type'] = 'database';
      $detail = substr($line, strpos($line, 'executeQuery():') + 16);

      $parsedLineInfo['time'] = (float)substr($detail, 1, strpos($detail, 'ms') - 2);
      $parsedLineInfo['query'] = substr($detail, strpos($detail, ' ms]') + 5);

    }


    return $parsedLineInfo;
  }


}

?>
