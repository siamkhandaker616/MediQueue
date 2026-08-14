<?php

namespace Tests\Feature;

use Tests\TestCase;

class MockupPagesTest extends TestCase
{
    public function test_mockup_index_renders(): void
    {
        $this->get('/mockup')->assertOk()->assertSee('MediQueue');
    }

    public function test_mockup_branch_b_screens_render(): void
    {
        foreach ([
            '/mockup/queue',
            '/mockup/schedule',
            '/mockup/prescription/create',
            '/mockup/prescriptions',
            '/mockup/medications',
            '/mockup/reviews',
            '/mockup/analytics',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
