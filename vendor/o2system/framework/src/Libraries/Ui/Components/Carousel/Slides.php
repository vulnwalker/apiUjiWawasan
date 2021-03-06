<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */

// ------------------------------------------------------------------------

namespace O2System\Framework\Libraries\Ui\Components\Carousel;

// ------------------------------------------------------------------------

use O2System\Framework\Libraries\Ui\Element;

/**
 * Class Slides
 *
 * @package O2System\Framework\Libraries\Ui\Components\Carousel
 */
class Slides extends Element
{
    /**
     * @var \O2System\Framework\Libraries\Ui\Components\Carousel\Indicators
     */
    private $indicators;
    private $target;

    public function __construct()
    {
        parent::__construct('div', 'slides');
        $this->attributes->addAttributeClass('carousel-inner');
        $this->attributes->addAttribute('role', 'listbox');
    }

    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    public function setIndicators(Indicators &$indicators)
    {
        $this->indicators =& $indicators;

        return $this;
    }

    public function createSlide()
    {
        $this->childNodes->push(new Slide());

        $slideNo = $this->childNodes->key();
        $indicator = $this->indicators->childNodes->createNode('li');
        $indicator->entity->setEntityName('indicator-' . $slideNo);
        $indicator->attributes->addAttribute('data-target', '#' . $this->target);
        $indicator->attributes->addAttribute('data-slide-to', $slideNo);

        return $this->childNodes->last();
    }
}