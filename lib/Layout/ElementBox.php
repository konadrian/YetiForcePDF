<?php
declare(strict_types=1);
/**
 * ElementBox class
 *
 * @package   YetiForcePDF\Layout
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF\Layout;

use \YetiForcePDF\Layout\Coordinates\Coordinates;
use \YetiForcePDF\Layout\Coordinates\Offset;
use \YetiForcePDF\Layout\Dimensions\BoxDimensions;
use \YetiForcePDF\Html\Element;
use YetiForcePDF\Style\Style;

/**
 * Class ElementBox
 */
class ElementBox extends Box
{
    /**
     * @var Element
     */
    protected $element;

    /**
     * Get element
     * @return Element
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set element
     * @param Element $element
     * @return $this
     */
    public function setElement(Element $element)
    {
        $this->element = $element;
        $element->setBox($this);
        return $this;
    }

    /**
     * Get boxes by tag name
     * @param string $tagName
     * @return array
     */
    public function getBoxesByTagName(string $tagName)
    {
        $boxes = [];
        $allChildren = [];
        $this->getAllChildren($allChildren);
        foreach ($allChildren as $child) {
            if ($child instanceof ElementBox && $child->getElement() && $child->getElement()->getDOMElement()) {
                $elementTagName = $child->getElement()->getDOMElement()->tagName;
                if ($elementTagName && strtolower($elementTagName) === strtolower($tagName)) {
                    $boxes[] = $child;
                }
            }
        }
        return $boxes;
    }

    /**
     * Fix tables - iterate through cells and insert missing one
     * @return $this
     */
    public function fixTables()
    {
        $tables = $this->getBoxesByTagName('table');
        foreach ($tables as $tableBox) {
            $rowGroups = $tableBox->getChildren()[0]->getChildren();
            if (!isset($rowGroups[0])) {
                $rowGroup = $tableBox->createRowGroup();
                $row = $rowGroup->createRow();
                $column = $row->createColumn();
                $column->createCell();
            } else {
                $columnsCount = 0;
                foreach ($rowGroups as $rowIndex => $rowGroup) {
                    foreach ($rowGroup->getChildren() as $row) {
                        $columns = $row->getChildren();
                        $columnsCount = max($columnsCount, count($columns));
                        foreach ($columns as $columnIndex => $column) {
                            if ($column->getRowSpan() > 1) {
                                $rowSpans = $column->getRowSpan();
                                for ($i = 1; $i < $rowSpans; $i++) {
                                    $nextRowGroup = $rowGroup->getParent()->getChildren()[$rowIndex + $i];
                                    $nextRow = $nextRowGroup->getFirstChild();
                                    $rowChildren = $nextRow->getChildren();
                                    $insertColumn = $nextRow->removeChild($nextRow->createColumnBox());
                                    if (isset($rowChildren[$columnIndex])) {
                                        $before = $rowChildren[$columnIndex];
                                        $nextRow->insertBefore($insertColumn, $before);
                                    } else {
                                        $nextRow->appendChild($insertColumn);
                                    }
                                    $insertColumn->setStyle(clone $column->getStyle());
                                    $insertColumn->getStyle()->setBox($insertColumn);
                                    $insertCell = $insertColumn->createCellBox();
                                    $insertCell->setStyle(clone $column->getFirstChild()->getStyle());
                                    $insertCell->getStyle()->setBox($insertCell);
                                }
                            }
                        }
                    }
                    foreach ($rowGroup->getChildren() as $row) {
                        $columns = $row->getChildren();
                        $missing = $columnsCount - count($columns);
                        for ($i = 0; $i < $missing; $i++) {
                            $column = $row->createColumnBox();
                            $column->createCellBox();
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Span all rows
     * @return $this
     */
    public function spanAllRows()
    {
        $tables = $this->getBoxesByTagName('table');
        foreach ($tables as $tableBox) {
            $tableBox->getFirstChild()->spanRows();
        }
        return $this;
    }

    /**
     * Build tree
     * @param $parentBlock
     * @return $this
     */
    public function buildTree($parentBlock = null)
    {
        if ($this->getElement()) {
            $domElement = $this->getElement()->getDOMElement();
        } else {
            // tablebox doesn't have element so we can get it from table wrapper (parent box)
            $domElement = $this->getParent()->getElement()->getDOMElement();
        }
        if ($domElement->hasChildNodes()) {
            foreach ($domElement->childNodes as $childDomElement) {
                if ($childDomElement instanceof \DOMComment) {
                    continue;
                }
                $styleStr = '';
                if ($childDomElement instanceof \DOMElement && $childDomElement->hasAttribute('style')) {
                    $styleStr = $childDomElement->getAttribute('style');
                }
                $element = (new Element())
                    ->setDocument($this->document)
                    ->setDOMElement($childDomElement)
                    ->init();
                // for now only basic style is used - from current element only (with defaults)
                $style = (new \YetiForcePDF\Style\Style())
                    ->setDocument($this->document)
                    ->setElement($element)
                    ->setContent($styleStr)
                    ->parseInline();
                $display = $style->getRules('display');
                switch ($display) {
                    case 'block':
                        $this->appendBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table':
                        $tableWrapper = $this->appendTableWrapperBlockBox($childDomElement, $element, $style, $parentBlock);
                        $tableWrapper->appendTableBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-row':
                        $rowGroup = $this->appendTableRowGroupBox($childDomElement, $element, $style, $parentBlock);
                        $rowGroup->appendTableRowBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'table-cell':
                        $this->appendTableCellBox($childDomElement, $element, $style, $parentBlock);
                        break;
                    case 'inline':
                        $inline = $this->appendInlineBox($childDomElement, $element, $style, $parentBlock);
                        if (isset($inline) && $childDomElement instanceof \DOMText) {
                            $inline->setAnonymous(true)->appendText($childDomElement, null, null, $parentBlock);
                        }
                        break;
                    case 'inline-block':
                        $this->appendInlineBlockBox($childDomElement, $element, $style, $parentBlock);
                        break;
                }
            }
        }
        return $this;
    }

}
