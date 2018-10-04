<?php
declare(strict_types=1);
/**
 * Element class
 *
 * @package   YetiForcePDF\Render\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Render\Dimensions;

/**
 * Class Element
 */
class Element extends Dimensions
{

	/**
	 * @var \YetiForcePDF\Style\Style
	 */
	protected $style;

	/**
	 * @var float
	 */
	protected $innerWidth = 0;
	/**
	 * @var float
	 */
	protected $innerHeight = 0;

	/**
	 * Set style
	 * @param \YetiForcePDF\Style\Style $style
	 * @return $this
	 */
	public function setStyle(\YetiForcePDF\Style\Style $style)
	{
		$this->style = $style;
		return $this;
	}

	/**
	 * Get innerWidth
	 * @return float
	 */
	public function getInnerWidth(): float
	{
		return $this->innerWidth;
	}

	/**
	 * Set innerWidth
	 * @param float $innerWidth
	 * @return $this
	 */
	public function setInnerWidth(float $innerWidth)
	{
		$this->innerWidth = $innerWidth;
		return $this;
	}

	/**
	 * Get innerHeight
	 * @return float
	 */
	public function getInnerHeight(): float
	{
		return $this->innerHeight;
	}

	/**
	 * Set innerHeight
	 * @param float $height
	 * @return $this
	 */
	public function setInnerHeight(float $innerHeight)
	{
		$this->innerHeight = $innerHeight;
		return $this;
	}

	/**
	 * Get available space inside container
	 * @return float
	 */
	public function getAvailableSpace()
	{
		if ($this->style->getElement()->isRoot()) {
			return $this->document->getCurrentPage()->getPageDimensions()->getInnerWidth();
		}
		$style = $this->style;
		$paddingWidth = $style->getRules('padding-left') + $style->getRules('padding-right');
		$borderWidth = $style->getRules('border-left-width') + $style->getRules('border-right-width');
		return $style->getParent()->getDimensions()->getAvailableSpace() - $paddingWidth - $borderWidth;
	}

	/**
	 * Calculate text dimensions
	 * @return float
	 */
	public function getTextWidth()
	{
		$text = $this->style->getElement()->getDOMElement()->textContent;
		$font = $this->style->getFont();
		return $font->getTextWidth($text);
	}

	/**
	 * Calculate text dimensions
	 * @return float
	 */
	public function getTextHeight()
	{
		$text = $this->style->getElement()->getDOMElement()->textContent;
		$font = $this->style->getFont();
		return $font->getTextHeight($text);
	}

}