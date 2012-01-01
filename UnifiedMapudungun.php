<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * A simple MediaWiki extension used to unify different variations of mapudungun.
 *
 * @file
 * @ingroup Extensions
 * @author Patricio Molina, Dennis Tobar Calderón, Osmar Valdebenito
 * @copyright Copyright © 2012, Patricio Molina, Dennis Tobar Calderón, Osmar Valdebenito
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

define( 'UnifiedMapudungun_VERSION', '0.9' );

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'UnifiedMapudungun',
	'version' => UnifiedMapudungun_VERSION,
	'author' => array(
        '[http://meta.wikimedia.org/wiki/User:Mahadeva Mahadeva]',
        '[http://meta.wikimedia.org/wiki/User:B1mbo B1mbo]',
        '[http://meta.wikimedia.org/wiki/User:Superzerocool Superzerocool]'
    ),
	'url' => 'https://www.mediawiki.org/wiki/Extension:Awesomeness',
	'descriptionmsg' => 'unifiedmapudungun-desc',
);

$wgHooks['ArticleSave'][] = 'UnifiedMapudungun::articleSave';

class UnifiedMapudungun {
    public static function articleSave(&$article, &$user, &$text, &$summary, $minor,
                                       $watchthis, $sectionanchor, &$flags, &$status) {

        // Regular unification: transforming interdentals consonants
        $origin = array("l'", "n'", "t'", "L'", "N'", "T'");
        $result = array("ḻ", "ṉ", "ṯ", "Ḻ", "Ṉ", "Ṯ");
        $text = str_replace($origin, $result, $text);

        // Raguileo unification. 'g' is being converted to 'q', then 'nq' to 'ng'
        // TODO: needs improvement
        $origin = array(
            "ü", "ch", "tr", "d", "ll", "g", "nq", "ḻ", "ṉ", "Ü",
            "Ṉ", "Ch", "Tr", "D", "Ll", "G", "NQ", "Ḻ", "TR", "LL",
            "ṯ", "Ṯ", "CH", "l-l", "L-L", "l'", "n'", "t'", "L'","N'",
            "T'"
        );
        $result = array(
            "v", "c", "x", "z", "j", "q", "g", "b", "h", "V",
            "H", "C", "X", "Z", "J", "Q", "G", "B", "X", "J",
            "t", "T", "C", "ll", "LL", "h", "b", "t", "B", "H",
            "T"
        );
        $text = str_replace($origin, $result, $text);

        // Repeating unification for azümchefe, so we're avoding issues
        // with letters like "ng" - "q". Capital syllabic groups for Ḻ, Ṉ and Ṯ
        // Repairing q to q and interdental t to th
        $origin = array(
            "ü", "ch", "tr", "d", "ll", "g", "nq", "ḻ", "ṉ", "Ü",
            "Ṉ", "Ch", "Tr", "D", "G", "NQ", "ḺA", "ḺE", "ḺI", "ḺO",
            "ḺU", "ḺÜ", "AḺ", "EḺ", "IḺ", "OḺ", "UḺ", "ÜḺ", "NG", "Ḻ",
            "TR", "l-l", "L-L", "ṯ", "Ṯ", "ṮA", "ṮE", "ṮI", "ṮO", "ṮU",
            "ṮÜ", "AṮ", "EṮ", "IṮ", "OṮ", "UṮ", "ÜṮ", "ṈA", "ṈE", "ṈI",
            "ṈO", "ṈU", "ṈÜ", "AṈ", "EṈ", "IṈ", "OṈ", "UṈ", "ÜṈ", "l'",
            "n'", "t'", "L'", "N'", "T'"
        );
        $result = array(
            "ü", "ch", "tx", "z", "ll", "q", "g", "lh", "nh", "Ü",
            "Nh", "Ch", "Tx", "Z", "Q", "G", "LHA", "LHE", "LHI", "LHO",
            "LHU", "LHÜ", "ALH", "ELH", "ILH", "OLH", "ULH", "ÜLH", "G", "Lh",
            "TX", "ll", "LL", "th", "TH", "THA", "THE", "THI", "THO", "THU",
            "THÜ", "ATH", "ETH", "ITH", "OTH", "UTH", "ÜTH", "NHA", "NHE", "NHI",
            "NHO", "NHU", "NHÜ","ANH", "ENH", "INH", "ONH", "UNH", "ÜNH", "lh",
            "nh", "th", "LH", "NH", "TH"
        );
        $text = str_replace($origin, $result, $text);
        return true;
    }
}
?>
