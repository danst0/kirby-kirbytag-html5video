<?php
/*
 * kirbytag html5video
 * html5 video player embedding for lazy people
 *
 * copyright: Jannik Beyerstedt | http://jannikbeyerstedt.de | code@jannikbeyerstedt.de
 * license: http://www.gnu.org/licenses/gpl-3.0.txt GPLv3 License
 *
 * version 2.2 (31.05.2015)
 * changelog:
 * - v2.0: kirby 2 support
 * - v2.1: fix broken default values
 * - v2.2: autodetection for poster file
 */

kirbytext::$tags['sublime'] = array(
  'attr' => array(
    'width',
    'height',
    'name',
    'uid'
  ),
  'html' => function($tag) {
    $source = $tag->attr('sublime');
    $page   = $tag->page();
    $baseurl =  '/' . $page->url() . '/';

    $width = $tag->attr('width');
    if(!$width)  $width  = c::get('kirbytext.video.width');
    $height = $tag->attr('height');
    if(!$height) $height = c::get('kirbytext.video.height');
    $name = $tag->attr('name');
    
    // gather all video files which match the given id/name    
    

    foreach($page->videos() as $v) {
    

      if(preg_match('!^' . preg_quote($source) . '!i', $v->name())) {
        $extension = f::extension($v->name());
        $mobile    = ($extension == 'mobile') ? $v->mobile = true : $v->mobile = false;
        $hd        = ($extension == 'hd')     ? $v->hd     = true : $v->hd     = false;
        $videos[] = $v;

      }

    }
    
    if(empty($videos)) return false;    

    // find the poster for this video
    foreach($page->images() as $i) {
      if(preg_match('!^' . preg_quote($source) . '!i', $i->name())) {
        $poster = $i;
        break;
      }
    }
    // check for a poster
    $poster = ($poster) ? ' poster="' . $poster->url() . '"' : false;


    $html = '<video class="video"' . $poster . ' width="' . $width . '" height="' . $height . '" data-name="' . $name . '" preload="none">';     
    foreach($videos as $video) {
      $type = '';
      if (strpos($video->url(),'.mp4') !== false) {
        $type = 'type="video/mp4"';
      } elseif (strpos($video->url(),'.ogv') !== false) {
        $type = 'type="video/ogg"';
      } elseif (strpos($video->url(),'.webm') !== false) {
        $type = 'type="video/webm"';
      }
      // check for hd quality
      $hd = ($video->hd) ? ' data-quality="hd"' : '';
      $hd = ($video->mobile) ? ' data-quality="mobile"' : '';
      // generate the source tag for each video
      $html .= '<source src="' . $video->url() . '"' . $hd . ' ' . $type . ' />';
    }
    $html .= '</video>';
    
    
    return $html;

  }

);
