<?php
/* 
 * Automates the truncation of text for a given width based on the rendered font provided
 * This type of truncation is very usefull for truncating text that is rendered in a non-monospaced fonts.
 * This class requires GD library in PHP
 * The truncation performed in this class has been tuned for performance.
 * 
 * Note. auto_truncate is left in place because it was my first proof of concept.
 *   auto_truncate is aproximately twice as slow as the recursive truncate
 *   auto_truncate was left in for historical value and to confuse people :)
 *
 * STANDARD MIT LICENSE
 *
 * Copyright (c) <2009> <Sean McCleary (Swipht Technologies)>
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 */
class SmartTruncate {
  var $font, $points, $max_width, $m_width;

  function SmartTruncate($font, $points, $max_width) {
    if(!file_exists($font)) {
      trigger_error('Font file not found');
    }
    $this->font = $font;
    $this->points = $points;
    $this->max_width = $max_width;
    $this->m_width = $this->get_width('m');
  }

  public function get_width($str) {
    $char = false;
    // the evaluated width of one character is not sufficient, more accurate to evaluate longer strings
    if(strlen($str) == 1) {
      $char = true;
      $str = str_repeat($str, 20);
    }
    $d = imagettfbbox($this->points, 0, $this->font, $str);
    $w = $d[2] - $d[0];
    if($char) $w = $w / 20;
    return $w;
  }

  /**
   * wrapper and public accessor for recursive_truncate
   * @param <string> $str target to truncate
   * @param <string> $truncation_token '...'
   * @return <string>
   */
  public function truncate($str, $truncation_token = '') {
    $truncated = $this->recursive_truncate($str);
    if($truncation_token && strlen($str) == strlen($truncated)) {
      $truncation_token = '';
    }
    return  $truncated . $truncation_token;
  }

  /**
   * Recursive way of getting a truncated string
   * @param <string> $first_chunk
   * @param <string> $second_chunk
   * @return <string> truncated string
   */
  private function recursive_truncate($first_chunk, $second_chunk = '', $second_chunk_backup = '') {
    if(!$second_chunk && $this->get_width($first_chunk) <= $this->max_width) {
      # Nothing to do
      return $first_chunk;
    }
    if(!$second_chunk) {
      # split string in half
      $chunks = str_split($first_chunk, ceil(strlen($first_chunk) / 2));
      list($first_chunk, $second_chunk) = $chunks;
    }
    # determine if either half is longer than max_width if so throw out second half
    $first_chunk_width = $this->get_width($first_chunk);
    if($first_chunk_width > $this->max_width) {
      # throw out second half and start over
      return $this->recursive_truncate($first_chunk);
    }
    $second_chunk_width = $this->get_width($second_chunk);
    if(
      ($first_chunk_width + $second_chunk_width) < $this->max_width &&
      ($first_chunk_width + $second_chunk_width + $this->m_width) > $this->max_width
    ) {
      return $first_chunk . $second_chunk;
    }
    if(($combined_width = $this->get_width($first_chunk . $second_chunk)) < $this->max_width) {
      # start itterative process to finish it off
      $remaining_chars = substr($second_chunk_backup, - strlen($second_chunk));
      
      $count = 0;
      $max_count = strlen($second_chunk_backup);
      $new_string = $first_chunk.$second_chunk;
      while($combined_width < $this->max_width && $count < $max_count) {
        $char = $remaining_chars[$count];
        $char_width = $this->get_width($char);
        $combined_width += $char_width;
        if($combined_width <= $this->max_width) {
          $new_string .= $char;
        }
        $count++;
      }
      return $new_string;
    } else {
      return $this->recursive_truncate($first_chunk, substr($second_chunk, 0, ceil(strlen($second_chunk) / 2)), $second_chunk);
    }
  }
}

?>
