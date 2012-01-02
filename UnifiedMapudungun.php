<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * A simple MediaWiki extension used to unify different variations of mapudungun.
 *
 * @file
 * @ingroup Extensions
 * @author Patricio Molina (Mahadeva), Osmar Valdebenito (B1mbo) and Dennis Tobar Calderón (Superzerocool)
 * @copyright Copyright © 2012, Patricio Molina, Osmar Valdebenito, Dennis Tobar Calderón
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
    'url' => 'https://github.com/pmolina/UnifiedMapudungun',
    'description' => 'A simple MediaWiki extension used to unify different variations of mapudungun.',
);

$UnifiedMapudungun = new UnifiedMapudungun();
$wgHooks['ArticleSave'][] = $UnifiedMapudungun;

class UnifiedMapudungun {

    /**
     * Regular unification: transforming interdentals consonants
     * 
     * @param $text string: original text
     * @return string: transformed text
     */
    private function toRegular( $text ) {
        $origin = array( "l'", "n'", "t'", "L'", "N'", "T'" );
        $result = array( "ḻ", "ṉ", "ṯ", "Ḻ", "Ṉ", "Ṯ" );
        $new_text = str_replace( $origin, $result, $text );
        return $new_text;
    }


    /**
     * Raguileo unification. 'g' is being converted to 'q', then 'nq' to 'ng'
     * TODO: needs improvement
     * 
     * @param $text string: original text
     * @return string: transformed text
     */
    private function toRaguileo( $text ) {
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
        $new_text = str_replace( $origin, $result, $text );
        return $new_text;
    }

    /**
     * Repeating unification for azümchefe, so we can avoid issues
     * with letters like "ng" to "q". Capital syllabic groups for Ḻ, Ṉ and Ṯ
     * Repairing q to q and interdental t to th
     * TODO: needs improvement
     * 
     * @param $text string: original text
     * @return string: transformed text
     */
    private function toAzumchefe( $text ) {
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
        $new_text = str_replace( $origin, $result, $text );
        return $new_text;
    }

    /**
     * Unifying variants of mapudungun. Used by ArticleSave hook
     *
     * @param $article object: the article (Article object) being saved
     * @param $user object: the user (User object) saving the article
     * @param $text string: the new article text
     * @param $summary string: the edit summary
     * @param $minor string: minor edit flag
     * @param $watchthis mixed: watch the page if true, unwatch the page if false, do nothing if null (since 1.17.0)
     * @param $sectionanchor: not used
     * @param $flags: bitfield, see documentation for details
     * @param $status object: the Status object that will be returned by Article::doEdit()
     * @return bool
     */
    public function onArticleSave( &$article, &$user, &$text, &$summary, $minor,
                                   $watchthis, $sectionanchor, &$flags, &$status ) {
        try {
            $text = $this->toRegular( $text );
            $text = $this->toRaguileo( $text );
            $text = $this->toAzumchefe( $text );
            return true;
        }
        catch ( Exception $e ) {
            return sprintf( 'Error: %s', $e->getMessage() );
        }
    }
}
?>
