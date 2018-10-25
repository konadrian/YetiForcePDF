<?php
declare(strict_types=1);
/**
 * Offset class
 *
 * @package   YetiForcePDF\Layout\Coordinates
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout\Coordinates;

use YetiForcePDF\Layout\Box;

/**
 * Class Offset
 */
class Offset extends \YetiForcePDF\Base
{
	/**
	 * @var Box
	 */
	protected $box;
	/**
	 * Offset top
	 * @var float
	 */
	protected $top;
	/**
	 * Offset left
	 * @var int
	 */
	protected $left = 0;

	/**
	 * Get offset top
	 * @return float
	 */
	public function getTop()
	{
		return $this->top;
	}

	/**
	 * Set offset top
	 * @param float $top
	 * @return $this
	 */
	public function setTop(float $top)
	{
		$this->top = $top;
		return $this;
	}

	/**
	 * Get offset left
	 * @return float
	 */
	public function getLeft()
	{
		return $this->left;
	}

	/**
	 * Set offset left
	 * @param float $left
	 * @return $this
	 */
	public function setLeft(float $left)
	{
		$this->left = $left;
		return $this;
	}

	/**
	 * Set box
	 * @param \YetiForcePDF\Layout\Box $box
	 * @return $this
	 */
	public function setBox(Box $box)
	{
		$this->box = $box;
		return $this;
	}

	/**
	 * Get box
	 * @return \YetiForcePDF\Layout\Box
	 */
	public function getBox()
	{
		return $this->box;
	}

}