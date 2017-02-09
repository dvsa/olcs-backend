<?php

namespace Dvsa\Olcs\Api\Service\Document\Parser;

/**
 * RTF parser
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class RtfParser implements ParserInterface
{
    const PIXELS_TO_TWIPS = 15;

    public function getFileExtension()
    {
        return 'rtf';
    }

    public function extractTokens($content)
    {
        $matches = $this->getMatches($content);
        $tokens = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $tokens[] = $matches[1][$i];
        }
        return $tokens;
    }

    public function replace($content, $data)
    {
        $matches = $this->getMatches($content);

        $search  = [];
        $replace = [];

        for ($i = 0; $i < count($matches[0]); $i++) {
            $literal = $matches[0][$i];
            $token   = $matches[1][$i];
            // bear in mind the later str_replace will replace *all*
            // bookmarks of this name; probably what we want of course,
            // but worth being clear about
            if (isset($data[$token])) {
                $current = $data[$token];
                if (is_array($current)) {
                    $str = $current['content'];
                    $formatted = $current['preformatted'];
                } else {
                    $str = $current;
                    $formatted = false;
                }
                $search[]  = $literal;
                // we assume each bookmark will return 'plain' text, so
                // replace certain characters with RTF style markup
                $replace[] = $this->format($str, $formatted);
            }
        }

        return str_replace($search, $replace, $content);
    }

    public function renderImage($binData, $width, $height, $type)
    {
        return sprintf(
            "{\pict\%sblip\picw%d\pich%d\picwgoal%d\pichgoal%d %s}",
            $type,
            $width,
            $height,
            $width * self::PIXELS_TO_TWIPS,
            $height * self::PIXELS_TO_TWIPS,
            bin2hex($binData)
        );
    }

    private function getMatches($content)
    {
        preg_match_all(
            "#{\\\.\\\bkmkstart\s([^}]+)}[^{]*{\\\.\\\bkmkend\s[^}]+}#",
            $content,
            $matches
        );
        return $matches;
    }

    private function format($data, $formatted = false)
    {
        if ($formatted) {
            return $data;
        }

        return str_replace("\n", "\par ", $data);
    }

    /**
     * Helper method to replace special characters (e.g. apostrophes) with entities,
     * and to quote reserved RTF chanracters (e.g. { and })
     *
     * @param string $data data to be modified
     *
     * @return string
     */
    public function getEntitiesAndQuote($data)
    {
        return \PHPRtfLite_Utf8::getUnicodeEntities(\PHPRtfLite::quoteRtfCode($data, true), 'UTF-8');
    }
}
