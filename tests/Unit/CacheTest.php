<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CacheTestService;
use COM;
use Mockery;

class CacheTest extends TestCase
{

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
}


?>