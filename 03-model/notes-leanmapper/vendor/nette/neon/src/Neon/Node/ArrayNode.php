<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types=1);

namespace Nette\Neon\Node;

use Nette\Neon\Node;


/** @internal */
final class ArrayNode extends Node
{
	/** @var ArrayItemNode[] */
	public $items = [];

	/** @var ?string */
	public $indent;


	public function __construct(?string $indent = null, int $pos = null)
	{
		$this->indent = $indent;
		$this->startPos = $this->endPos = $pos;
	}


	public function toValue(): array
	{
		return ArrayItemNode::itemsToArray($this->items);
	}


	public function toString(): string
	{
		if ($this->indent === null) {
			$isList = !array_filter($this->items, function ($item) { return $item->key; });
			$res = ArrayItemNode::itemsToInlineString($this->items);
			return ($isList ? '[' : '{') . $res . ($isList ? ']' : '}');

		} elseif (count($this->items) === 0) {
			return '[]';

		} else {
			return ArrayItemNode::itemsToBlockString($this->items);
		}
	}


	public function getSubNodes(): array
	{
		return $this->items;
	}
}
