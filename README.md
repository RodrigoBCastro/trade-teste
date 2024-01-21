
# Documentação da API RESTful e Front-end React

## Hospedado na nuvem para poder acessar e testar
### http://35.198.56.21

## 1. Introdução
Esta API RESTful foi desenvolvida utilizando o framework Laravel 9 e é acompanhada de um front-end React. Ela permite aos usuários logados criar campeonatos com times em seus bairros. Somente é possível adicionar um limite de 8 times por campeonato. Após fazer login, você pode criar um campeonato, e os jogos das quartas de final serão sorteados automaticamente. Depois, você poderá registrar os placares para determinar os times vencedores. As semifinais, a final e o terceiro lugar são gerados automaticamente assim que uma fase anterior termina.

## 2. Requisitos de Sistema
- PHP 8.2
- Composer
- Laravel 9
- MySQL
- Node 20
- Servidor web (Apache/Nginx)

## 3. Configuração do Ambiente
### Instalação do PHP, MySQL, e Node
- Instale PHP 8.2, MySQL e Node 20 seguindo as instruções oficiais de cada plataforma.
- Certifique-se de que o Composer (para PHP) e o NPM (para Node) estão instalados.

### Configuração do Laravel
- Utilize o Composer para instalar o Laravel 9.
- Configure o arquivo `.env` com as credenciais do seu banco de dados MySQL e outras variáveis de ambiente necessárias.
- `composer install`

### Configuração do React
- Utilize o NPM para criar um novo projeto React.
- Configure o ambiente React para se comunicar com a API Laravel utilizando o Axios ou outro cliente HTTP.
- `npm install`

### Informação
- Após toda a configuração, será possível acessar o localhost funcionando em http://localhost:3000. Através deste endereço, o front-end interagirá com o back-end. Lembre-se de que no arquivo .env, o VITE_API_BASE_URL deve estar configurado para http://localhost:8000.

## 4. Estrutura do Projeto
### Back-end (Laravel)
- `app/`: Contém a lógica principal da aplicação.
- `routes/api.php`: Define os endpoints da API.
- `database/migrations/`: Contém as migrações do banco de dados.
- `database/seeers/`: Contém as seeders para popular dados no banco.

### Front-end (React)
- `src/`: Contém os componentes React.
- `public/`: Contém arquivos estáticos como HTML, CSS, e imagens.
## 5. Configuração do Banco de Dados

O sistema utiliza o framework Laravel para a configuração e gerenciamento do banco de dados. As tabelas do banco de dados são criadas e mantidas através de migrações, e os dados iniciais são populados usando seeders.

### Tabelas Principais

1. **Users**: Armazena os dados dos usuários para autenticação e gerenciamento de acesso.
    - Campos principais incluem `id`, `name`, `email`, `password`, etc.
    - Esta tabela é utilizada pelo sistema de autenticação do Laravel.

2. **Campeonato**: Representa os campeonatos dentro do sistema.
    - Contém informações como `id`, `nome`, `data_inicio`, `data_fim`.
    - Cada campeonato pode ter vários jogos associados.

3. **Jogo**: Detalha os jogos individuais de cada campeonato.
    - Inclui campos como `id`, `campeonato_id`, `time_casa_id`, `time_visitante_id`, `gols_time_casa`, `gols_time_visitante`, `fase`, `data_jogo`.
    - Relaciona-se com as tabelas `Campeonato`, `Time`, e `Resultado`.

4. **Time**: Armazena informações sobre os times participantes.
    - Campos incluem `id`, `nome` entre outros.
    - Times são associados aos jogos e campeonatos.

5. **Resultado**: Registra os resultados dos jogos.
    - Contém `id`, `jogo_id`, `vencedor_id`, `perdedor_id`.
    - Está diretamente ligada à tabela `Jogo`.

### Migrações e Seeders

- **Migrações**: São utilizadas para criar e modificar a estrutura das tabelas do banco de dados de forma controlada e versionada. Cada tabela mencionada acima é criada e gerenciada por suas respectivas migrações.

- **Seeders**: Utilizados para alimentar o banco de dados com dados iniciais para desenvolvimento e testes. Isso inclui a criação de usuários padrão, times de exemplo, e configurações iniciais de campeonatos.

### Comandos Importantes

- Para executar as migrações: `php artisan migrate`
- Para popular o banco de dados com dados iniciais: `php artisan db:seed`

## 6. Implementação da API
### Endpoints
- `GET /campeonatos` - Listar todos os campeonatos
- `GET /campeonatos/{id}` - Obter detalhes de um campeonato
- `POST /campeonatos` - Criar um novo campeonato, deve ser enviado um array junto com os Ids dos times.
- `PUT /jogos/{id}/resultado` - Espera os atributos `gols_time_casa`,`gols_time_visitante` para poder gerar o resultado de um jogo.
- `GET /campeonatos/{campeonatoId}/resultado` - Traz o resultado do 1 ao 4 colocado caso o campeonato tenha sido finalizado.

### Autenticação
- Utiliza o sistema de autenticação padrão do Laravel (Sanctum).
- Requer que os usuários se autentiquem para acessar os endpoints.

## 7. Desenvolvimento Front-end
- Utiliza React para criar uma interface de usuário dinâmica.
- Integra-se com a API Laravel para realizar operações CRUD nas tarefas.

## 8. Segurança
- Implementa autenticação de usuários.
- Utiliza validações no lado do servidor para evitar injeções SQL e ataques XSS.
