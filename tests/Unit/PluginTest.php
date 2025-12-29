<?php

declare(strict_types=1);

namespace Retrologin\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Retrologin\Plugin;

/**
 * Unit tests for the Plugin class.
 *
 * These are basic tests to verify the Plugin class can be instantiated
 * and implements the required interface.
 */
final class PluginTest extends TestCase
{
	/**
	 * Test that Plugin class exists and can be instantiated.
	 *
	 * @test
	 */
	public function it_can_be_instantiated(): void
	{
		$plugin = new Plugin();

		$this->assertInstanceOf(Plugin::class, $plugin);
	}

	/**
	 * Test that Plugin implements IteratorAggregate interface.
	 *
	 * @test
	 */
	public function it_implements_iterator_aggregate(): void
	{
		$plugin = new Plugin();

		$this->assertInstanceOf(\IteratorAggregate::class, $plugin);
	}

	/**
	 * Test that Plugin can return an iterator.
	 *
	 * @test
	 */
	public function it_returns_an_iterator(): void
	{
		$plugin = new Plugin();

		$iterator = $plugin->getIterator();

		$this->assertInstanceOf(\Traversable::class, $iterator);
	}

	/**
	 * Test that getIterator yields expected objects.
	 *
	 * @test
	 */
	public function it_yields_plugin_components(): void
	{
		$plugin = new Plugin();

		$components = iterator_to_array($plugin->getIterator());

		$this->assertNotEmpty($components, 'Plugin should yield at least one component');
		$this->assertIsArray($components);
	}
}
