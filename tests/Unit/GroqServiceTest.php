<?php

namespace Tests\Unit;

use App\Services\GroqService;
use Tests\TestCase;

class GroqServiceTest extends TestCase
{
    /**
     * Test that GroqService can be instantiated.
     */
    public function test_groq_service_can_be_instantiated(): void
    {
        $service = new GroqService();
        $this->assertInstanceOf(GroqService::class, $service);
    }

    /**
     * Test that GroqService has correct default model.
     */
    public function test_groq_service_has_correct_default_model(): void
    {
        $service = new GroqService();
        $this->assertEquals('llama-3.3-70b-versatile', $service->getModel());
    }

    /**
     * Test that GroqService can change model.
     */
    public function test_groq_service_can_change_model(): void
    {
        $service = new GroqService();
        $service->setModel('llama-3.1-8b-instant');
        $this->assertEquals('llama-3.1-8b-instant', $service->getModel());
    }

    /**
     * Test that GroqService returns error when API key is not configured.
     */
    public function test_groq_service_returns_error_when_api_key_not_configured(): void
    {
        // Temporarily remove API key from config
        config(['services.groq.api_key' => '']);

        $service = new GroqService();
        $response = $service->chat('Hello', 'You are a helpful assistant.');

        $this->assertTrue(strpos($response, 'AI service is not configured') !== false);
    }
}
