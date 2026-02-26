# Sistema de Elevador – Fila FIFO

Implementação da classe `Elevador` em PHP/Laravel utilizando estrutura de dados **fila** com comportamento **FIFO** (First-In, First-Out) através da classe `SplQueue`.

## Requisitos

- PHP 8.2+
- Composer 2.x
- Laravel 12.0+

**OU**

- Docker e Docker Compose

## Instalação

### Opção 1: Docker (Recomendado)

```bash
git clone <URL_DO_REPOSITORIO>
cd elevador
docker compose up
```

A aplicação será executada automaticamente dentro do container.

### Opção 2: Instalação Local

```bash
git clone <URL_DO_REPOSITORIO>
cd elevador
composer install
cp .env.example .env
php artisan key:generate
```

## Como executar

### Com Docker

```bash
docker compose up
```

### Localmente

```bash
php artisan elevador:demonstrar
```

## Estrutura

```
app/
  Services/Elevador.php                        ← Classe principal
  Console/Commands/ElevadorDemonstrar.php      ← Script de demonstração
  Providers/AppServiceProvider.php             ← Registro no Service Container
bootstrap/
  app.php                                      ← Inicialização do Laravel
  providers.php                                ← Carregamento dos providers
```

## Classe Elevador

**Namespace:** `App\Services\Elevador`

### Atributos

- `$filaChamados` (SplQueue) - Fila FIFO de andares aguardando atendimento
- `$andarAtual` (int) - Andar atual do elevador. Inicializado como 0 (térreo)
- `$capacidade` (int) - Capacidade máxima de pessoas. Definida no construtor

### Métodos

#### `__construct(int $capacidade)`
Inicializa `$filaChamados` com uma nova `SplQueue`, define `$andarAtual = 0` e atribui `$capacidade`.

#### `chamar(int $andar): void`
Valida se `$andar >= 0` e utiliza `enqueue()` para adicionar ao final da fila. Lança `InvalidArgumentException` se o andar for negativo.

#### `mover(): void`
- Se a fila estiver vazia: exibe mensagem informando que não há chamados pendentes.
- Se houver chamados: utiliza `dequeue()` para remover o primeiro da fila (FIFO), atualiza `$andarAtual` e exibe mensagens de movimento e chegada.

#### `getAndarAtual(): int`
Retorna o andar atual do elevador.

#### `getChamadosPendentes(): SplQueue`
Retorna uma cópia da fila via `clone`, preservando a integridade da fila original.

## Exemplo de uso

```php
use App\Services\Elevador;

$elevador = new Elevador(capacidade: 10);

$elevador->chamar(5);
$elevador->chamar(2);
$elevador->chamar(8);

$elevador->mover(); // Atende andar 5 (primeiro que entrou)
$elevador->mover(); // Atende andar 2
$elevador->mover(); // Atende andar 8

echo $elevador->getAndarAtual(); // 8
```
## Conceito FIFO

A `SplQueue` implementa o comportamento FIFO:
- `enqueue($valor)` => insere no final da fila
- `dequeue()` => remove e retorna o primeiro elemento
- `isEmpty()` => verifica se a fila está vazia

As chamadas são processadas na ordem exata de chegada (primeiro a entrar, primeiro a sair).
