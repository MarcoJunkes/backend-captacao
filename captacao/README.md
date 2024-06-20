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

## Passo a Passo para Execução com Docker

1. **Acertar o .env para o seu ambiente docker:**
2. **Iniciar os conteiners**: `docker compose up --d`
3. **Executar as migrações do laravel**: `docker compose exec app php artisan migrate`
