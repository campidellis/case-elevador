<?php

namespace App\Services;

use SplQueue;
use InvalidArgumentException;

class Elevador
{
    private SplQueue $filaChamados;
    private int $andarAtual;
    private int $capacidade;

    public function __construct(int $capacidade)
    {
        $this->filaChamados = new SplQueue();
        $this->andarAtual   = 0;
        $this->capacidade   = $capacidade;
    }

    public function chamar(int $andar): void
    {
        if ($andar < 0) {
            throw new InvalidArgumentException(
                "Andar inválido: {$andar}. O andar deve ser maior ou igual a 0."
            );
        }

        $this->filaChamados->enqueue($andar);
    }

    public function mover(): void
    {
        if ($this->filaChamados->isEmpty()) {
            echo "Não há chamados pendentes. O elevador está parado no andar {$this->andarAtual}." . PHP_EOL;
            return;
        }

        $andarAnterior    = $this->andarAtual;
        $proximoAndar     = $this->filaChamados->dequeue();
        $this->andarAtual = $proximoAndar;

        echo "Elevador saindo do andar {$andarAnterior}..." . PHP_EOL;
        echo "Elevador chegando ao andar {$proximoAndar}." . PHP_EOL;
    }

    public function getAndarAtual(): int
    {
        return $this->andarAtual;
    }

    public function getChamadosPendentes(): SplQueue
    {
        return clone $this->filaChamados;
    }
}
