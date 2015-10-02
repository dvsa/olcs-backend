<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\FeeBundle as Qry;

/**
 * InsufficientFeeTable bookmark
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InsufficientFeeTable extends DynamicBookmark
{
    const RTF_BOLD_START = '{\b ';
    const RTF_BOLD_END = '}';

    public function getQuery(array $data)
    {
        return Qry::create(['id' => $data['fee']]);
    }

    public function render()
    {
        if (empty($this->data)) {
            return '';
        }

        $rows = [];
        $rows[] = [
            'COL1_BMK1' => self::RTF_BOLD_START . 'Fee description' . self::RTF_BOLD_END,
            'COL1_BMK2' => self::RFT_BOLD_START . 'Amount' . self::RTF_BOLD_END,
            'COL1_BMK3' => ''
        ];

        $rows[] = [
            'COL1_BMK1' => $this->data['fee']['description'],
            'COL1_BMK2' => '£',
            'COL1_BMK3' => $this->data['fee']['amount']
        ];

        $rows[] = [
            'COL1_BMK1' => 'Amount RECEIVED',
            'COL1_BMK2' => '£',
            'COL1_BMK3' => $this->data['outstandingAmount']
        ];

        $rows[] = [
            'COL1_BMK1' => self::RTF_BOLD_START . 'BALANCE NOW DUE' . self::RTF_BOLD_END,
            'COL1_BMK2' => self::RTF_BOLD_START . '£' . self::RTF_BOLD_END,
            'COL1_BMK3' => self::RTF_BOLD_START . ((float) $this->data['fee']['amount'] -
                (float) $this->data['outstandingAmount']) . self::RTF_BOLD_END
        ];

        $snippet = $this->getSnippet('TABLE_INSUFFICIENT_FEE');
        $parser  = $this->getParser();

        $str = '';
        foreach ($rows as $tokens) {
            $str .= $parser->replace($snippet, $tokens);
        }
        return $str;
    }
}
