# Documentação do Projeto

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
8. **iso4217ControllerTest**: em `tests/Unit`, ele define uma série de testes unitários.

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
