<?php
/**
 * php-gedcom.
 *
 * php-gedcom is a library for parsing, manipulating, importing and exporting
 * GEDCOM 5.5 files in PHP 5.3+.
 *
 * @author          Xiang Ming <wenqiangliu344@gmail.com>
 * @copyright       Copyright (c) 2010-2013, Xiang Ming
 * @license         MIT
 *
 * @link            http://github.com/mrkrstphr/php-gedcom
 */

namespace Writer\Indi;

class Famc
{
    /**
     * @param \Record\Indi\Famc $attr
     * @param int                         $level
     *
     * @return string
     */
    public static function convert(\Record\Indi\Famc &$famc, $level = 0)
    {
        $output = '';
        // NAME
        $_famc = $famc->getFams();
        if (empty($_fams)) {
            return $output;
        }
        $output .= $level.' FAMC @'.$_famc."@\n";
        // level up
        $level++;

        // PEDI
        $pedi = $famc->getPedi();
        if (!empty($pedi)) {
            $output .= $level.' PEDI '.$pedi."\n";
        }

        // note
        $note = $famc->getSour();
        if (!empty($note) && count($note) > 0) {
            foreach ($note as $item) {
                $_convert = \Writer\NoteRef::convert($item, $level);
                $output .= $_convert;
            }
        }

        return $output;
    }
}
