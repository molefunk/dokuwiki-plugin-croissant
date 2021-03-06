<?php
/**
 * DokuWiki Plugin croissant (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Adrian Lang <lang@cosmocode.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_croissant extends DokuWiki_Syntax_Plugin {
    function getType() {
        return 'substition';
    }

    function getPType() {
        return 'normal';
    }

    function getSort() {
        return 1;
    }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('~~bc:.*?~~',$mode,'plugin_croissant');
        $this->Lexer->addSpecialPattern('~~nobc~~',$mode,'plugin_croissant');
    }

    function handle($match, $state, $pos, Doku_Handler $handler){
        if ($match == '~~nobc~~') {
            return null;
        }
        return trim(substr($match, 5, -2));
    }

    function render($mode, Doku_Renderer $renderer, $data) {
        if($mode === 'metadata') {
            if (blank($data)) {
                $renderer->meta['plugin_croissant_nobc'] = true;
            }
            $renderer->meta['plugin_croissant_bctitle'] = $data;
        }
        return true;
    }

    /**
     * Greatly cleaned up copy&paste from tpl_youarehere with custom titles
     */
    function tpl($sep=' &raquo; ') {
        global $ID;
        global $lang;

        if (p_get_metadata($ID, 'plugin_croissant_nobc') === true) {
            return;
        }

        $parts = explode(':', $ID);

        echo '<span class="plugin_croissant">';
        echo '<span class="bchead">'.$lang['youarehere'].'</span>';

        // always print the startpage
        array_unshift($parts, '');

        // print intermediate namespace links
        $part = $page = '';
        $count = count($parts);
        for($i = 0; $i < $count; ++$i) {
            $old_page = $page;
            $part .= $parts[$i];
            if ($i < $count - 1) {
                $part .= ':';
            }
            $page = $part;
            resolve_pageid('', $page, $exists);
            if ($page !== $old_page) {
                echo $sep;
                tpl_pagelink(':' . $page, p_get_metadata($page, 'plugin_croissant_bctitle'));
            }
        }
        echo '</span>';

        return true;
    }
}
