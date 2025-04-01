<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CacheTestService;
use Carbon\Carbon;
use COM;
use Mockery;

class CacheTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    public function testConsultaChaveDevolvendoValorPadrão()
    {
        $cache = CacheTestService::consulta_chave();

        $this->assertTrue($cache['cache_limpo']);
        $this->assertNull($cache['consulta_1']);
        $this->assertEquals($cache['consulta_2'], 'Se aparecer essa mensagem então dê like no vídeo!');
        $this->assertEquals($cache['consulta_3'], 'conteúdo 2 vezes por semana.');
        $this->assertTrue(true);
    }

    public function testConsultaChaveDevolvendoValorPassadoComoParametroNoConsultaTres(){
        $cache = CacheTestService::consulta_chave('Diretor', 'Spelling');

        $this->assertTrue($cache['cache_limpo']);
        $this->assertNull($cache['consulta_1']);
        $this->assertEquals($cache['consulta_2'], 'Se aparecer essa mensagem então dê like no vídeo!');

        // consulta_3 deve devolver o valor passado como parametro
        $this->assertEquals($cache['consulta_3'], 'Spelling');
        $this->assertTrue(true);
    }

    public function testAlterandoEntreConexoes(){
        $cache = CacheTestService::alternando_entre_conexoes();

        $this->assertTrue($cache['cache_limpo']);
        $this->assertTrue($cache['cache_alternativo_limpo']);

        $this->assertNull($cache['consulta_1']);
        $this->assertStringContainsString($cache['consulta_2'], 'conteúdo 2 vezes por semana.');
        $this->assertNull($cache['consulta_3']);
        $this->assertStringContainsString($cache['consulta_4'], 'conteúdo 2 vezes por semana. | conexao 2');
    }

    // public function testAlterandoEntreConexoesSemLimparCache(){
    //     $cacheMock = Mockery::mock('alias:'.CacheTestService::class);
    //     $cacheMock->shouldReceive('limpar_cache')->andReturn(false);
        
    //     $cache = CacheTestService::alternando_entre_conexoes();

    //     self::assertNull($cache['consulta_1']);
    //     self::assertStringContainsString($cache['consulta_2'], 'conteúdo 2 vezes por semana.');
    //     self::assertStringContainsString($cache['consulta_3'], '| conexao 2');
    //     self::assertStringContainsString($cache['consulta_4'], '| conexao 2');
    // }

    public function testChecarSeChaveExiste(){
        $cache = CacheTestService::checar_se_chave_existe();

        $this->assertTrue($cache['cache_limpo']);
        $this->assertFalse($cache['consulta_1']);
        $this->assertFalse($cache['consulta_2']);
        $this->assertTrue($cache['consulta_3']);
        $this->assertTrue($cache['consulta_4']);
        $this->assertStringContainsString($cache['consulta_5'], 'chave existe e é falsa, nao existe');
        $this->assertStringContainsString($cache['consulta_6'], 'chave existe e é falsa, existe');
    }

    public function testAumentarEDiminuirChave(){
        $cache = CacheTestService::aumentar_diminuir_chave();

        $this->assertGreaterThanOrEqual(1, $cache['likes']);
        $this->assertLessThanOrEqual(15, $cache['likes']);
        
        $this->assertGreaterThanOrEqual(-3, $cache['deslikes']);
        $this->assertLessThanOrEqual(-1, $cache['deslikes']);
    }

    public function testConsultarERemover(){
        $cache = CacheTestService::consultar_e_remover('chave', 'A importância dos testes');

        $this->assertTrue($cache['cache_limpo']);
        $this->assertStringContainsString($cache['consulta_1'], 'valor padrão');
        $this->assertEquals($cache['consulta_2'], 'A importância dos testes');
        $this->assertNull($cache['consulta_3']);
    }

    public function testSalvarValor(){
        $cache = CacheTestService::salvar_valor();

        $this->assertEquals($cache['put_com_ttl'], 1);
        $this->assertEquals($cache['put_sem_ttl'], 2);
        $this->assertEquals($cache['put_com_ttl_agendado'], 3);
        $this->assertEquals($cache['add_com_ttl'], 4);
        $this->assertEquals($cache['forever'], 5);
    }

    public function testSalvarValorComTempoDeVidaExpirado(){
        // salva a data e hora atual
        Carbon::setTestNow(Carbon::now());

        // chma a função para salvar o cache com o tempo de vida
        CacheTestService::salvar_valor();

        // simlula o passar do tempo, para os cache expirar o TTL
        Carbon::setTestNow(Carbon::now()->addSeconds(30));

        // chama a função novamente, só que agora o TTL expirou
        $cache = CacheTestService::salvar_valor(true);

        $this->assertEquals($cache['put_com_ttl'], null);
        $this->assertEquals($cache['put_sem_ttl'], 2);
        $this->assertEquals($cache['put_com_ttl_agendado'], 3);
        $this->assertEquals($cache['add_com_ttl'], null);
        $this->assertEquals($cache['forever'], 5);
    }

    public function testUsandoHelper(){
        $cache = CacheTestService::usando_helper();

        $this->assertEquals($cache['put_com_ttl'], null);
        $this->assertEquals($cache['put_sem_ttl'], null);
        $this->assertEquals($cache['put_com_ttl_agendado'], null);
        $this->assertEquals($cache['add_com_ttl'], null);
        $this->assertEquals($cache['forever'], null);
        $this->assertEquals($cache['teste'], 'usando helper');
        $this->assertEquals($cache['dev tech tips'], null);
    }


}


?>