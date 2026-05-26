# integraODONTO
Sistema de gestão de Clínicas Odontológicas

# IntegraODONTO

Sistema completo para gestão de clínicas odontológicas, desenvolvido com foco em performance, segurança e usabilidade. A aplicação utiliza uma arquitetura MVC (Model-View-Controller) nativa em PHP, dispensando o uso de frameworks externos para o back-end e garantindo fácil manutenção.

## Funcionalidades

* **Gestão de Pacientes:** Cadastro completo com foto, dados pessoais, endereço, vinculação de responsável legal e histórico clínico (prontuário).
* **Agenda de Consultas:** Controle de agendamentos por profissional, status de atendimento e gatilhos de integração com WhatsApp para confirmação com os pacientes.
* **Prontuário Clínico:** Linha do tempo integrada com todo o histórico de consultas e tratamentos realizados pelo paciente na clínica.
* **Módulo Financeiro:** Geração automática de lançamentos pendentes a partir da conclusão de tratamentos na agenda, controle de recebimentos e formas de pagamento.
* **Relatórios Fiscais:** Exportação de dados de faturamento anual para visualização e Excel (XLS), cruzando CPF de declarantes e dependentes para o Imposto de Renda.
* **Controle de Acesso (ACL):** Sistema de login hierárquico com permissões restritas por tipo de usuário (Administrador, Dentista e Recepção).
* **Auditoria:** Registro ininterrupto de logs (IP, usuário, ação e tabela) para todas as operações sensíveis no banco de dados.

## Tecnologias Utilizadas

* **Backend:** PHP 8+ (Orientação a Objetos, PDO).
* **Frontend:** HTML5, CSS3, Bootstrap 5.3.
* **Interatividade:** JavaScript, jQuery 3.6, chamadas assíncronas (AJAX).
* **Banco de Dados:** MySQL / MariaDB.
* **Segurança:** Hashes nativos de senha (`password_hash`), Proteção contra injeção de SQL (Prepared Statements), Prevenção contra ataques CSRF via tokens de sessão validados em todas as requisições.

## Níveis de Acesso

O sistema opera com 3 níveis distintos de permissões de visualização e ação:

1. **Administrador:** Acesso irrestrito à plataforma, incluindo a criação, edição e exclusão de contas de usuário na aba de configurações.
2. **Dentista:** Acesso à agenda, gestão de pacientes, prontuários, painel financeiro e relatórios de faturamento. Bloqueado para gestão de credenciais do sistema.
3. **Recepção:** Acesso exclusivo à agenda (para marcações e controle de status) e ao cadastro de dados básicos e de contato dos pacientes. Bloqueado terminantemente para prontuários, finanças e relatórios.

## Estrutura de Diretórios

A arquitetura respeita a segmentação de responsabilidades do padrão MVC:

* `/config` - Arquivos de configuração de ambiente e classe de conexão com o banco de dados.
* `/core` - Classes base do sistema, gerenciador de rotas e verificações de segurança globais.
* `/controllers` - Regras de negócio e intermediação entre as interações do usuário e o banco de dados.
* `/models` - Classes de abstração e persistência direta de dados no banco.
* `/views` - Telas de interface, fragmentos de layout (header, sidebar, footer) e assets estáticos (CSS/JS).
* `/uploads/pacientes` - Diretório de destinação física para armazenamento das imagens de perfil.

## Instalação e Configuração

A implantação do sistema é integralmente facilitada pelos scripts de automação Bash.

1. Os 7 scripts `.sh` de instalação são transferidos para um diretório vazio no servidor web (ex: `htdocs`, `public_html` ou `/var/www/html`).
2. Através de um terminal posicionado neste diretório, o instalador principal é executado:
```bash

```



bash install.sh

```
3. O script orquestrará a criação de toda a árvore de diretórios, a injeção dos códigos-fonte e a geração do arquivo `database.sql` na raiz do projeto.
4. O arquivo `database.sql` é importado no gerenciador de banco de dados MySQL para a criação das tabelas estruturais.
5. A configuração de credenciais do banco de dados (host, usuário e senha) é ajustada no arquivo `config/Database.php`, caso divirja do padrão do XAMPP/servidor local.
6. A plataforma fica pronta para uso e o acesso ocorre via navegador, apontando para a URL do diretório raiz.

## Credenciais Padrão

Após a inicialização do banco de dados, o sistema disponibiliza um usuário mestre nativo para o primeiro acesso:

* **Usuário:** `admin`
* **Senha:** `root`

```
