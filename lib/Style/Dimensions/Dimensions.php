<?php
declare(strict_types=1);
/**
 * Dimensions class
 *
 * @package   YetiForcePDF\Style\Dimensions
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Style\Dimensions;

/**
 * Class Dimensions
 */
class Dimensions extends \YetiForcePDF\Base
{
	/**
	 * @var float
	 */
	protected $width = 0;
	/**
	 * @var float
	 */
	protected $height = 0;

	/**
	 * Get width
	 * @return float
	 */
	public function getWidth(): float
	{
		return $this->width;
	}

	/**
	 * Get height
	 * @return float
	 */
	public function getHeight(): float
	{
		return $this->height;
	}

	/**
	 * Set width
	 * @param float $width
	 * @return $this
	 */
	public function setWidth(float $width)
	{
		$this->width = $width;
		return $this;
	}

	/**
	 * Set height
	 * @param float $height
	 * @return $this
	 */
	public function setHeight(float $height)
	{
		$this->height = $height;
		return $this;
	}

	/**
	 * Calculate dimensions
	 * @return $this
	 */
	public function calculate()
	{
		return $this;
	}
}
