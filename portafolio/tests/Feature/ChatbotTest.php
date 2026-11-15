<?php

namespace Tests\Feature;

use App\Models\ChatConversation;
use App\Models\ChatLead;
use App\Services\Chatbot\ChatAssistant;
use App\Services\Chatbot\LanguageModel;
use App\Services\Chatbot\ModelResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FakeLanguageModel implements LanguageModel
{
    /** @var array<int, array{system: string, messages: array, tools: array}> */
    public array $calls = [];

    /** @param array<int, ModelResponse> $responses */
    public function __construct(private array $responses) {}

    public function send(string $system, array $messages, array $tools): ModelResponse
    {
        $this->calls[] = compact('system', 'messages', 'tools');

        return array_shift($this->responses);
    }
}

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    private function fakeModel(array $responses): FakeLanguageModel
    {
        $fake = new FakeLanguageModel($responses);
        $this->app->instance(ChatAssistant::class, new ChatAssistant($fake));

        return $fake;
    }

    private function text(string $text): ModelResponse
    {
        return new ModelResponse('end_turn', $text, [], [['type' => 'text', 'text' => $text]], 100, 20);
    }

    public function test_portfolio_page_loads_with_widget(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('chatbot/widget.js', false)
            ->assertSee('csrf-token', false);
    }

    public function test_replies_and_persists_conversation(): void
    {
        $fake = $this->fakeModel([$this->text('¡Hola! ¿Qué tipo de negocio tienes?')]);

        $response = $this->postJson('/chat/messages', ['message' => 'Quiero una página web'])
            ->assertOk()
            ->assertJsonPath('reply', '¡Hola! ¿Qué tipo de negocio tienes?')
            ->assertJsonPath('lead_captured', false);

        $conversation = ChatConversation::where('uuid', $response->json('conversation_id'))->firstOrFail();
        $this->assertSame(['user', 'assistant'], $conversation->messages->pluck('role')->all());
        $this->assertSame(120, $conversation->input_tokens + $conversation->output_tokens);
        $this->assertSame([['role' => 'user', 'content' => 'Quiero una página web']], $fake->calls[0]['messages']);
        $this->assertStringContainsString('<informacion_del_negocio>', $fake->calls[0]['system']);
    }

    public function test_continues_existing_conversation_with_history(): void
    {
        $fake = $this->fakeModel([$this->text('Primera'), $this->text('Segunda')]);

        $id = $this->postJson('/chat/messages', ['message' => 'Hola'])->json('conversation_id');
        $this->postJson('/chat/messages', ['message' => 'Tengo una clínica', 'conversation_id' => $id])
            ->assertOk()
            ->assertJsonPath('conversation_id', $id);

        $this->assertSame(1, ChatConversation::count());
        $this->assertSame(
            ['user', 'assistant', 'user'],
            array_column($fake->calls[1]['messages'], 'role'),
        );
    }

    public function test_lead_tool_saves_lead_and_notifies_owner(): void
    {
        Mail::fake();
        config(['chatbot.owner.email' => 'avisos@example.com']);

        $toolCall = ['id' => 'toolu_1', 'name' => 'registrar_lead', 'input' => [
            'nombre' => 'Ana',
            'email' => 'ana@example.com',
            'telefono' => '+57 300 000 0000',
            'tipo_proyecto' => 'sistema de reservas',
            'presupuesto' => 'USD 800',
            'resumen' => 'Clínica que necesita agenda en línea en un mes.',
        ]];

        $fake = $this->fakeModel([
            new ModelResponse('tool_use', '', [$toolCall], [['type' => 'tool_use'] + $toolCall]),
            $this->text('¡Gracias, Ana! Te contactaré en menos de 24 horas.'),
        ]);

        $this->postJson('/chat/messages', ['message' => 'Soy Ana, ana@example.com'])
            ->assertOk()
            ->assertJsonPath('lead_captured', true);

        $lead = ChatLead::firstOrFail();
        $this->assertSame('Ana', $lead->name);
        $this->assertSame('nuevo', $lead->status);

        // El segundo llamado incluye el turno del asistente y el resultado de la herramienta.
        $last = end($fake->calls[1]['messages']);
        $this->assertSame('tool_result', $last['content'][0]['type']);
        $this->assertSame('toolu_1', $last['content'][0]['toolUseID']);
        $this->assertFalse($last['content'][0]['isError']);
    }

    public function test_lead_tool_rejects_missing_contact(): void
    {
        $toolCall = ['id' => 'toolu_2', 'name' => 'registrar_lead', 'input' => [
            'nombre' => 'Luis', 'email' => 'no-es-correo', 'telefono' => null,
            'tipo_proyecto' => null, 'presupuesto' => null, 'resumen' => 'Quiere una web.',
        ]];

        $fake = $this->fakeModel([
            new ModelResponse('tool_use', '', [$toolCall], []),
            $this->text('¿Me compartes tu WhatsApp o correo?'),
        ]);

        $this->postJson('/chat/messages', ['message' => 'Soy Luis'])
            ->assertOk()
            ->assertJsonPath('lead_captured', false);

        $this->assertSame(0, ChatLead::count());
        $this->assertTrue(end($fake->calls[1]['messages'])['content'][0]['isError']);
    }

    public function test_refusal_returns_fallback_message(): void
    {
        config(['chatbot.owner.whatsapp' => '+57 300 111 2222']);
        $this->fakeModel([new ModelResponse('refusal', '', [], [])]);

        $this->postJson('/chat/messages', ['message' => 'Hola'])
            ->assertOk()
            ->assertJsonPath('reply', fn ($r) => str_contains($r, 'https://wa.me/573001112222'));
    }

    public function test_demo_mode_without_api_key(): void
    {
        config(['chatbot.api_key' => null]);

        $this->postJson('/chat/messages', ['message' => 'Hola'])
            ->assertOk()
            ->assertJsonPath('reply', fn ($r) => str_contains($r, 'modo demostración'));
    }

    public function test_model_errors_return_friendly_503(): void
    {
        $this->app->instance(ChatAssistant::class, new ChatAssistant(new class implements LanguageModel {
            public function send(string $system, array $messages, array $tools): ModelResponse
            {
                throw new \RuntimeException('API caída');
            }
        }));

        $this->postJson('/chat/messages', ['message' => 'Hola'])
            ->assertStatus(503)
            ->assertJsonStructure(['reply', 'conversation_id']);
    }

    public function test_validates_message(): void
    {
        $this->postJson('/chat/messages', ['message' => ''])->assertUnprocessable();
        $this->postJson('/chat/messages', ['message' => str_repeat('a', 1001)])->assertUnprocessable();
    }

    public function test_cannot_hijack_conversation_from_another_ip(): void
    {
        $this->fakeModel([$this->text('A'), $this->text('B')]);

        $id = $this->postJson('/chat/messages', ['message' => 'Hola'])->json('conversation_id');

        $other = $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.99'])
            ->postJson('/chat/messages', ['message' => 'Hola', 'conversation_id' => $id]);

        $this->assertNotSame($id, $other->json('conversation_id'));
        $this->assertSame(2, ChatConversation::count());
    }

    public function test_rate_limit(): void
    {
        config(['chatbot.rate_limit_per_minute' => 2, 'chatbot.api_key' => null]);

        $this->postJson('/chat/messages', ['message' => '1'])->assertOk();
        $this->postJson('/chat/messages', ['message' => '2'])->assertOk();
        $this->postJson('/chat/messages', ['message' => '3'])->assertStatus(429);
    }

    public function test_admin_panel_requires_credentials(): void
    {
        config(['chatbot.admin.password' => null]);
        $this->get('/admin/conversaciones')->assertNotFound();

        config(['chatbot.admin.password' => 'secreto']);
        $this->get('/admin/conversaciones')->assertUnauthorized();

        $auth = ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'secreto'];
        $this->withServerVariables($auth)->get('/admin/conversaciones')->assertOk()->assertSee('Conversaciones del asistente IA');
    }

    public function test_admin_can_view_conversation_and_update_lead(): void
    {
        config(['chatbot.admin.password' => 'secreto']);
        $auth = ['PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'secreto'];

        $conversation = ChatConversation::create(['uuid' => (string) \Illuminate\Support\Str::uuid()]);
        $conversation->messages()->create(['role' => 'user', 'content' => 'Necesito una tienda']);
        $lead = new ChatLead(['name' => 'Ana', 'phone' => '3000000000', 'summary' => 'Tienda online']);
        $lead->chat_conversation_id = $conversation->id;
        $lead->save();

        $this->withServerVariables($auth)->get("/admin/conversaciones/{$conversation->id}")
            ->assertOk()->assertSee('Necesito una tienda')->assertSee('Ana');

        $this->withServerVariables($auth)->patch("/admin/leads/{$lead->id}", ['status' => 'contactado'])
            ->assertRedirect();
        $this->assertSame('contactado', $lead->fresh()->status);
    }
}
