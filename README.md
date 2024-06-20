# Considerações Gerais

## Tecnologias Utilizadas
- Framework: Laravel
- Banco de Dados: MySQL
- Servidor: Apache2

## Principais Componentes
1. **api.php**: localizado em `routes/`, define o endpoint da API.
2. **2024_06_19_024525_create_iso4217s_table.php**: em `migrations/`, cria o esquema do banco de dados.
3. **iso4217Factory.php**: em `app/Factory/`, facilita a criação flexível de objetos.
4. **iso4217Repository.php**: em `app/Repository/`, organiza e mantém o acesso aos dados.
5. **iso4217.php**: em `app/Models/`, modelo dos dados no BD.
6. **iso4217Controller.php**: em `app/Http/Controllers/`, gerencia requisições HTTP e crawling.
7. **.env**: arquivo de variáveis de ambiente para configuração do banco de dados.
8. **iso4217ControllerTest**: em `tests/Unit`, define uma série de testes unitários.

## Passo a Passo para Execução com Docker

1. **Acertar o .env para o seu ambiente docker:**
   - Configure as variáveis de ambiente no arquivo `.env` conforme necessário para seu ambiente Docker.

2. **Iniciar os containers Docker:**
   - Execute o comando `docker compose up --d` para iniciar os containers em segundo plano.

3. **Executar as migrações do Laravel:**
   - Após iniciar os containers, execute `docker compose exec app php artisan migrate` para aplicar as migrações do banco de dados.

4. **Executar os Testes Unitários:**
   - Para executar os testes unitários, utilize o seguinte comando:
     ```bash
     docker compose exec app php artisan test --testsuite=Unit
     ```
     Este comando executa todos os testes unitários definidos na sua aplicação Laravel.

5. **Parar os containers Docker:**
   - Quando não precisar mais dos containers em execução, pare-os com o comando `docker compose down`.

## Documentação

### Iso4217Controller

#### Descrição:
O `Iso4217Controller` é um controlador Laravel responsável por manipular requisições relacionadas aos códigos ISO 4217, que são padrões internacionais para códigos de moedas. Ele acessa informações sobre esses códigos a partir de uma fonte externa (Wikipedia) e interage com um repositório para persistir ou recuperar dados.

#### Métodos:

1. **Método `__construct(iso4217Repository $iso4217Repository)`**
   - **Descrição**: Construtor do controlador que recebe uma instância de `iso4217Repository`.
   - **Parâmetros**:
     - `$iso4217Repository`: Instância do repositório usado para interagir com dados relacionados aos códigos ISO 4217.

2. **Método `store(Request $request)`**
   - **Descrição**: Método que processa uma requisição para armazenar dados relacionados aos códigos ISO 4217.
   - **Parâmetros**:
     - `$request`: Objeto `Request` do Laravel contendo os dados da requisição HTTP.
   - **Retorno**:
     - Resposta HTTP (JSON) contendo os dados das moedas encontradas ou mensagens de erro, conforme o caso.
   - **Funcionalidades**:
     - Acessa a Wikipedia para obter informações sobre os códigos ISO 4217.
     - Filtra e processa os dados da tabela para extrair informações relevantes.
     - Valida os códigos fornecidos na requisição.
     - Atualiza ou cria entradas no repositório `iso4217Repository` conforme necessário.
     - Manipula exceções que podem ocorrer durante o acesso à Wikipedia ou processamento de dados.

#### Dependências:
- **Laravel Framework**: Utilizado como framework PHP para desenvolvimento web.
- **Symfony DomCrawler**: Usado para manipulação de HTML e extração de dados da Wikipedia.
- **Mockery**: Usado para criação de mocks em testes unitários.
- **PHPUnit**: Framework de teste para PHP, usado para escrever e executar testes unitários.

### Iso4217ControllerTest

#### Descrição:
Os testes unitários para o `Iso4217Controller` verificam o comportamento esperado de suas funcionalidades em cenários variados. Eles são implementados usando o PHPUnit e Mockery para isolar dependências e garantir que o controlador se comporte conforme o esperado em diferentes condições.

#### Métodos de Teste:

1. **Método `test_store_method_returns_error_on_empty_input()`**
   - **Descrição**: Verifica se o método `store()` retorna um erro quando nenhum código ou número é fornecido na requisição.
   - **Asserções**:
     - Verifica se o status da resposta é 400 (Bad Request).
     - Verifica se o conteúdo da resposta é um JSON válido contendo a mensagem de erro esperada.

2. **Método `test_coin_list()`**
   - **Descrição**: Testa o caso em que um código válido é fornecido na requisição.
   - **Asserções**:
     - Verifica se o status da resposta é 200 (OK).
     - Verifica se o conteúdo da resposta é um JSON válido.

3. **Método `test_invalid_code()`**
   - **Descrição**: Testa o caso em que um código inválido é fornecido na requisição.
   - **Asserções**:
     - Verifica se o status da resposta é 400 (Bad Request).
     - Verifica se o conteúdo da resposta é um JSON válido contendo a mensagem de erro esperada.

#### Dependências:
- **PHPUnit**: Framework de teste para PHP, usado para escrever e executar testes unitários.
- **Mockery**: Utilizado para criar mocks de dependências como o `iso4217Repository` durante os testes.
- **Laravel Framework**: Usado para criar instâncias do `Iso4217Controller` e simular requisições HTTP.


