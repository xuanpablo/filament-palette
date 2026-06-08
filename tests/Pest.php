<?php

use Xuanpablo\CommandPalette\Tests\Browser\TestCase as BrowserTestCase;

// Unit/feature tests are class-based (PHPUnit\Framework\TestCase or the package
// TestCase) and run under Pest unchanged. Browser tests (tests/Browser) boot a
// Testbench app with a Filament panel + this plugin via the Browser TestCase.
uses(BrowserTestCase::class)->in('Browser');
