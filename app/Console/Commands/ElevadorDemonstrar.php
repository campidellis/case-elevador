<?php

namespace App\Console\Commands;

use App\Services\Elevador;
use Illuminate\Console\Command;

/**
 * Artisan Command: elevador:demonstrar
 *
 * Comando Laravel que demonstra o funcionamento completo da classe Elevador.
 * A classe é injetada automaticamente pelo Service Container do Laravel,
 * conforme registrado no AppServiceProvider.
 *
 * Execução:
 *   php artisan elevador:demonstrar
 */
class ElevadorDemonstrar extends Command
{
    protected $signature = 'elevador:demonstrar';

    protected $description = 'Demonstra o funcionamento da classe Elevador com fila FIFO (SplQueue)';

    /**
     * O Laravel resolve a dependência Elevador automaticamente
     * via injeção de dependência (Service Container).
     */
    public function handle(Elevador $elevador): void
    {
        // ============================================================
        // DEMONSTRAÇÃO 1 – Fluxo básico FIFO
        // ============================================================
        $this->titulo('DEMONSTRAÇÃO 1 – Fluxo básico FIFO');

        $this->info("✔ Elevador criado via Service Container do Laravel.");
        $this->line("  Andar atual: {$elevador->getAndarAtual()}");
        $this->newLine();

        // Registra chamadas (enqueue)
        $this->comment('Registrando chamadas:');
        $elevador->chamar(5); $this->line('  chamar(5) → enqueue() na SplQueue');
        $elevador->chamar(2); $this->line('  chamar(2) → enqueue() na SplQueue');
        $elevador->chamar(8); $this->line('  chamar(8) → enqueue() na SplQueue');
        $elevador->chamar(3); $this->line('  chamar(3) → enqueue() na SplQueue');
        $this->newLine();

        // Exibe fila
        $this->comment('Fila de chamados (getChamadosPendentes()):');
        $this->exibirFila($elevador);
        $this->newLine();

        // Processa a fila (dequeue)
        $this->comment('Processando com mover() [dequeue FIFO]:');
        $this->newLine();

        foreach (range(1, 4) as $passo) {
            $this->line("  Passo {$passo}:");
            $elevador->mover();
            $this->line("  Andar atual: {$elevador->getAndarAtual()} | Pendentes: {$elevador->getChamadosPendentes()->count()}");
            $this->newLine();
        }

        // Fila vazia
        $this->line('  Tentando mover com fila vazia:');
        $elevador->mover();
        $this->newLine();

        // ============================================================
        // DEMONSTRAÇÃO 2 – Validação de andar inválido
        // ============================================================
        $this->titulo('DEMONSTRAÇÃO 2 – Validação de andar inválido');

        try {
            $elevador->chamar(-1);
        } catch (\InvalidArgumentException $e) {
            $this->info('✔ InvalidArgumentException capturada corretamente:');
            $this->error('  ' . $e->getMessage());
        }
        $this->newLine();

        // ============================================================
        // DEMONSTRAÇÃO 3 – getChamadosPendentes() usa clone
        // ============================================================
        $this->titulo('DEMONSTRAÇÃO 3 – getChamadosPendentes() preserva a fila original');

        /** @var Elevador $elevador2 */
        $elevador2 = app(Elevador::class);
        $elevador2->chamar(1);
        $elevador2->chamar(4);
        $elevador2->chamar(6);

        $this->line('  Fila original: 3 itens enfileirados.');

        $clone = $elevador2->getChamadosPendentes(); // retorna clone
        $clone->dequeue();
        $clone->dequeue();

        $this->line("  Após 2 dequeue() no clone: clone tem {$clone->count()} item(s).");
        $pendentes = $elevador2->getChamadosPendentes()->count();
        $this->info("  Fila original (deve ter 3): {$pendentes} item(s). ✔");
        $this->newLine();

        $this->titulo('FIM DA DEMONSTRAÇÃO');
    }

    /**
     * Exibe os andares da fila no formato visual FIFO.
     */
    private function exibirFila(Elevador $elevador): void
    {
        $fila  = $elevador->getChamadosPendentes();
        $itens = [];
        $pos   = 1;

        while (! $fila->isEmpty()) {
            $itens[] = "[{$pos}] Andar " . $fila->dequeue();
            $pos++;
        }

        $this->line('  ' . implode(' → ', $itens));
    }

    /**
     * Exibe um título formatado no terminal.
     */
    private function titulo(string $texto): void
    {
        $this->newLine();
        $this->line('<fg=yellow>' . str_repeat('=', strlen($texto) + 4) . '</>');
        $this->line("<fg=yellow>| {$texto} |</>");
        $this->line('<fg=yellow>' . str_repeat('=', strlen($texto) + 4) . '</>');
        $this->newLine();
    }
}
