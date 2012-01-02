<?php
if ( !defined( 'MEDIAWIKI' ) ) die();
/**
 * A simple MediaWiki extension used to unify different variations of mapudungun.
 *
 * Special thanks to:
 * - Osmar Valdebenito (Wikimedia Chile)
 * - Andrés Chandía (chandia.net)
 *
 * @file
 * @ingroup Extensions
 * @author Patricio Molina (Mahadeva)
 * @copyright Copyright © 2012, Patricio Molina
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 3.0 or later
 */

define( 'UnifiedMapudungun_VERSION', '0.9.1' );

$wgExtensionCredits['other'][] = array(
    'path' => __FILE__,
    'name' => 'UnifiedMapudungun',
    'version' => UnifiedMapudungun_VERSION,
    'author' => array(
        '[http://meta.wikimedia.org/wiki/User:Mahadeva Mahadeva]'
    ),
    'url' => 'https://github.com/pmolina/UnifiedMapudungun',
    'description' => 'A simple MediaWiki extension used to unify different variations of mapudungun.',
);

$UnifiedMapudungun = new UnifiedMapudungun();
$wgHooks['ArticleSave'][] = $UnifiedMapudungun;

class UnifiedMapudungun {

    /**
     * Unification, using rules defined in "norwirin mapudungun trapümfe"
     * http://www.chandia.net/k%C3%BCdawkawe
     * 
     * @param $text string: original text
     * @return string: transformed text
     */
    private function transform( $text ) {
        /* Vowels */
		$text = preg_replace( '/ə|ï|v/', 'ü', $text );
        $text = preg_replace( '/Ï|V/', 'Ü', $text );

        /* Glides */
        $text = preg_replace( '/(.)q/', '$1g', $text );

        /* Consonants */
        $text = preg_replace( '/(C|c)([^h])/', '$1h$2', $text );
        $text = preg_replace( '/Sd|Z/', 'D', $text );
        $text = preg_replace( '/sd|z/', 'd', $text );
        $text = preg_replace( '/L(·|d|h)|B/', 'L\'', $text );
        $text = preg_replace( '/l(·|d|h)|b/', 'l\'', $text );
        $text = preg_replace( '/J/', 'Ll', $text );
        $text = preg_replace( '/j/', 'll', $text );
        $text = preg_replace( '/N(·|d|h)|H/', 'N\'', $text );
        $text = preg_replace( '/(n|T|t)(·|d|h)/', '$1\'', $text );
		$text = preg_replace( '/([^(C|c|N|n)])h/', '$1n\'', $text );
		$text = preg_replace( '/Ŋ|ŋ/', 'ng', $text );
		$text = preg_replace( '/(\b)G/', '$1Ng', $text );
		$text = preg_replace( '/(\b)g/', '$1ng', $text );
		$text = preg_replace( '/(T|t)x/', '$1r', $text );
		$text = preg_replace( '/X/', 'Tr', $text );
		$text = preg_replace( '/x/', 'tr', $text );
		$text = preg_replace( '/ʃ/', 'sh', $text );

        /* Diphthongs */
		$text = preg_replace( '/(A|a|E|e|O|o|U|u|Ü|ü)i/', '$1y', $text );
		$text = preg_replace( '/(A|a|E|e)(o|u)/', '$1w', $text );
		$text = preg_replace( '/I(a|e|o|u|w)/', 'Y$1', $text );
        // The diphthong doesn't apply when it's followed by an "a" or an "e"
		$text = preg_replace( '/i(a|e|o|u|w)([^a]|[^e])/', 'y$1$2', $text );
		$text = preg_replace( '/(O|U|Wu)(a|e|o)/', 'W$2', $text );
		$text = preg_replace( '/(o|u)(a|e|o)/', 'w$2', $text );
		$text = preg_replace( '/(O|o)u/', '$1w', $text );

        /* errors comming from spanish orthography */
		$text = preg_replace( '/Á/', 'A', $text );
		$text = preg_replace( '/á/', 'a', $text );
		$text = preg_replace( '/É/', 'E', $text );
		$text = preg_replace( '/é/', 'e', $text );
		$text = preg_replace( '/Í/', 'I', $text );
		$text = preg_replace( '/í/', 'i', $text );
		$text = preg_replace( '/Ó/', 'O', $text );
		$text = preg_replace( '/ó/', 'o', $text );
		$text = preg_replace( '/Ú/', 'U', $text );
		$text = preg_replace( '/ú/', 'u', $text );

        return $text;
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
            $text = $this->transform( $text );
            return true;
        }
        catch ( Exception $e ) {
            return sprintf( 'Error: %s', $e->getMessage() );
        }
    }
}
?>
