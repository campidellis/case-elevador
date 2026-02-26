<?php

namespace Tests\Unit;

use App\Services\Elevador;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ElevadorTest extends TestCase
{
    // ---------------------------------------------------------------
    // Construtor
    // ---------------------------------------------------------------

    public function test_elevador_inicia_no_andar_zero(): void
    {
        $elevador = new Elevador(10);

        $this->assertSame(0, $elevador->getAndarAtual());
    }

    public function test_fila_inicia_vazia(): void
    {
        $elevador = new Elevador(10);

        $this->assertTrue($elevador->getChamadosPendentes()->isEmpty());
    }

    // ---------------------------------------------------------------
    // chamar()
    // ---------------------------------------------------------------

    public function test_chamar_adiciona_andar_na_fila(): void
    {
        $elevador = new Elevador(10);
        $elevador->chamar(5);
        $elevador->chamar(3);

        $this->assertSame(2, $elevador->getChamadosPendentes()->count());
    }

    public function test_chamar_andar_negativo_lanca_excecao(): void
    {
        $elevador = new Elevador(10);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Andar inválido: -1');

        $elevador->chamar(-1);
    }

    public function test_chamar_andar_zero_e_valido(): void
    {
        $elevador = new Elevador(10);
        $elevador->chamar(0);

        $this->assertSame(1, $elevador->getChamadosPendentes()->count());
    }

    // ---------------------------------------------------------------
    // mover()
    // ---------------------------------------------------------------

    public function test_mover_processa_ordem_fifo(): void
    {
        $elevador = new Elevador(10);
        $elevador->chamar(5);
        $elevador->chamar(2);
        $elevador->chamar(8);

        $this->expectOutputRegex('/.*/');

        $elevador->mover();
        $this->assertSame(5, $elevador->getAndarAtual());

        $elevador->mover();
        $this->assertSame(2, $elevador->getAndarAtual());

        $elevador->mover();
        $this->assertSame(8, $elevador->getAndarAtual());
    }

    public function test_mover_reduz_fila_em_um(): void
    {
        $elevador = new Elevador(10);
        $elevador->chamar(3);
        $elevador->chamar(7);

        $this->expectOutputRegex('/.*/');

        $elevador->mover();

        $this->assertSame(1, $elevador->getChamadosPendentes()->count());
    }

    public function test_mover_com_fila_vazia_nao_lanca_excecao(): void
    {
        $elevador = new Elevador(10);

        $this->expectOutputRegex('/Não há chamados pendentes/');

        $elevador->mover();
    }

    public function test_mover_com_fila_vazia_mantem_andar_atual(): void
    {
        $elevador = new Elevador(10);

        $this->expectOutputRegex('/.*/');

        $elevador->mover();

        $this->assertSame(0, $elevador->getAndarAtual());
    }

    // ---------------------------------------------------------------
    // getChamadosPendentes()
    // ---------------------------------------------------------------

    public function test_get_chamados_pendentes_retorna_clone(): void
    {
        $elevador = new Elevador(10);
        $elevador->chamar(1);
        $elevador->chamar(4);
        $elevador->chamar(6);

        $clone = $elevador->getChamadosPendentes();
        $clone->dequeue();
        $clone->dequeue();

        // Fila original deve continuar intacta com 3 itens
        $this->assertSame(3, $elevador->getChamadosPendentes()->count());
    }
}
