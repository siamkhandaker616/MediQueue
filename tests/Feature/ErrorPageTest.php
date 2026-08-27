<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPageTest extends TestCase
{
    /**
     * Every error view must render its OWN status code and heading (not just
     * fall back to a generic 404 content). Proves the individual blades wire
     * config correctly into the shared themed layout.
     */
    public function test_each_error_page_renders_its_own_status_and_heading(): void
    {
        $cases = [
            '404' => ['code' => '404 Error', 'heading' => 'Page not found'],
            '403' => ['code' => '403 Error', 'heading' => 'Access denied'],
            '419' => ['code' => '419 Error', 'heading' => 'Session expired'],
            '429' => ['code' => '429 Error', 'heading' => 'Too many requests'],
            '500' => ['code' => '500 Error', 'heading' => 'Something went wrong'],
            '503' => ['code' => '503 Error', 'heading' => 'Under maintenance'],
        ];

        foreach ($cases as $view => $expect) {
            $html = view("errors.{$view}")->render();

            $this->assertStringContainsString('MediQueue', $html, "errors.{$view} missing brand chrome");
            $this->assertStringContainsString($expect['code'], $html, "errors.{$view} should show '{$expect['code']}'");
            $this->assertStringContainsString($expect['heading'], $html, "errors.{$view} should show '{$expect['heading']}'");
            $this->assertMatchesRegularExpression(
                '#<span class="badge [^>]*>\s*' . preg_quote($expect['code'], '#') . '\s*</span>#',
                $html,
                "errors.{$view} status chip must be '{$expect['code']}'"
            );
        }
    }
}
