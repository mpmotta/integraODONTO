# 🦷 IntegraODONTO

**O sistema completo e descomplicado para a sua clínica odontológica.**

O **IntegraODONTO** foi criado para organizar e facilitar o dia a dia de consultórios e clínicas. Com ele, você abandona as planilhas e o papel, centralizando o agendamento de pacientes, o histórico de tratamentos e o controle financeiro em um único ambiente seguro e fácil de usar.

---

## ✨ O que o sistema faz por você?

* 👥 **Cadastro de Pacientes:** Guarde todas as informações importantes, desde dados pessoais e de contato até a foto do paciente e informações do responsável legal.
* 📅 **Agenda Inteligente:** Controle seus horários de forma visual. Saiba quem está agendado, quem está aguardando na recepção e envie mensagens de confirmação diretamente para o WhatsApp do paciente com um clique.
* ⚕️ **Prontuário Digital:** Tenha a linha do tempo completa da saúde do paciente. Todo tratamento finalizado na agenda vai automaticamente para o histórico clínico.
* 💰 **Controle Financeiro Automático:** Quando um tratamento é marcado como "Concluído" na agenda, o sistema já cria uma cobrança pendente no financeiro. Você só precisa confirmar o recebimento depois.
* 📊 **Facilidade no Imposto de Renda:** Gere relatórios automáticos de tudo o que foi recebido no ano, já cruzando o CPF de quem pagou com o serviço realizado. Exporte para Excel ou PDF para mandar para o contador.
* 🔒 **Histórico de Segurança:** O sistema anota silenciosamente tudo o que acontece (quem acessou, o que apagou ou alterou), garantindo total transparência e segurança para o dono da clínica.

---

## 👩‍⚕️ Perfis de Acesso (Quem vê o quê?)

O IntegraODONTO entende que cada membro da equipe precisa de ferramentas diferentes. Por isso, ele divide os acessos em três perfis:

1. 👑 **Administrador (Dono/Gestor):** Tem a chave do consultório. Acessa tudo e é o único que pode criar senhas ou excluir outros usuários (como novos dentistas ou secretárias).
2. 🦷 **Dentista:** Focado no atendimento clínico. Tem acesso total à agenda, ficha dos pacientes, prontuários e também pode acompanhar os relatórios financeiros do seu faturamento. Não pode alterar as configurações do sistema.
3. 👩‍💻 **Recepção:** Focada no atendimento ao público. Pode marcar e desmarcar consultas na agenda e atualizar os dados de contato do paciente. **Não tem acesso** aos prontuários clínicos (sigilo médico) e nem à parte financeira da clínica.

---

## 💻 Como instalar (Passo a Passo)

A instalação foi desenhada para ser rápida e automatizada, montando toda a estrutura do sistema para você.

**1. Prepare o ambiente**
Coloque os 7 arquivos de instalação (`.sh`) que você baixou em uma pasta vazia no seu servidor web ou computador local (como o `htdocs` do XAMPP).

**2. Execute o instalador**
Abra o terminal (linha de comando) dentro dessa pasta e digite o comando:

```bash
bash install.sh

```

*O sistema vai criar todas as pastas e arquivos necessários automaticamente, além de gerar um arquivo chamado `database.sql`.*

**3. Prepare o Banco de Dados**
Pegue esse arquivo `database.sql` gerado e importe no seu painel do banco de dados (MySQL/phpMyAdmin). Ele vai criar as tabelas e o seu usuário inicial.

**4. Acesse o sistema**
Abra o navegador e acesse a pasta do projeto. Para entrar pela primeira vez, use:

> **Usuário:** `admin`
> **Senha:** `root`

*(Lembre-se de trocar a senha ou criar seu usuário definitivo logo no primeiro acesso nas configurações!)*

---

## 🛠️ Tecnologias Utilizadas (Para os curiosos)

O sistema foi construído de forma leve e direta, sem depender de ferramentas pesadas e complicadas:

* **Linguagem:** PHP (Rodando nos bastidores de forma segura).
* **Visual:** Telas amigáveis construídas com HTML, CSS e o padrão Bootstrap.
* **Banco de Dados:** MySQL (Rápido e confiável para guardar seus dados).
* **Segurança:** Criptografia forte para as senhas e proteção automática contra as principais tentativas de invasões e cliques falsos na internet.
