# 💼 RHEase

<img width="777" height="202" alt="image" src="https://github.com/user-attachments/assets/a8f8eb07-5703-458e-8c70-ce1bd9c591af" />

Sistema completo de automação para Recursos Humanos

## 📚 Informações Acadêmicas

**Universidade:** Universidade Federal do Tocantins - Campus Palmas  
**Curso:** Ciência da Computação  
**Disciplina:** Engenharia de Software  
**Semestre:** 2025/2  
**Professor:** Edeilson Milhomem

## 📋 Descrição do Projeto

O RHEase é uma solução de software completa, projetada para automatizar e otimizar os processos-chave do departamento de Recursos Humanos de uma empresa. Com o objetivo de aumentar a eficiência, reduzir a carga de trabalho manual e melhorar a experiência dos colaboradores, este sistema centraliza e simplifica as tarefas diárias, permitindo que a equipe de RH se concentre em atividades mais estratégicas.

O sistema abrange todo o ciclo de vida do colaborador, desde a entrada até a saída da empresa, oferecendo uma plataforma integrada que facilita a gestão de pessoas e processos organizacionais.

## 🎯 Objetivos

- **Automatizar processos de RH:** Reduzir tarefas manuais e repetitivas através de fluxos automatizados
- **Centralizar informações:** Unificar dados de colaboradores em uma plataforma única e segura
- **Melhorar a experiência do colaborador:** Facilitar o acesso a informações e serviços de RH
- **Aumentar a eficiência operacional:** Otimizar tempo e recursos da equipe de RH
- **Facilitar tomadas de decisão:** Fornecer relatórios e métricas para gestão estratégica

- ## ✅ Sprints

### 🔍 01 - CRUD Inicial
- [X] **Funções** - Cadastro, atualização, deleção (safe-delete), e visualização de colaboradores
- [X] **Valor** - Permite ao Gerente de RH iniciar a integração dos colaboradores no RHease

### 🔍 02 - Funções iniciais
- [X] **Funções** - Login, Painel de vagas, inscrição, registro de pontos, remuneração, benefícios
- [X] **Valor** - Permite o funcionamento básico do RH, valida a arquitetura do projeto.

## ✅ Funcionalidades Implementadas

### 🔍 Recrutamento e Seleção
- [ ] **RF01** - Gerenciamento de vagas de emprego
- [ ] **RF02** - Sistema de banco de currículos

### 💬 Comunicação Interna
- [ ] **RF21** - Canal de comunicação integrado
- [ ] **RF22** - Sistema de anúncios e comunicados

### 💰 Folha de Pagamento
- [ ] **RF26** - Automação do cálculo de salários

### 🏥 Gestão de Benefícios
- [ ] **RF31** - Gerenciamento de planos de saúde
- [ ] **RF32** - Controle de vale-refeição e vale-transporte
- [ ] **RF33** - Gestão de seguro de vida

### 👋 Demissões (Offboarding)
- [ ] **RF36** - Fluxo de trabalho estruturado para saídas
- [ ] **RF37** - Coleta automatizada de feedback de saída


*Legenda: ✅ Implementado | ❌ Não implementado | 🔄 Em desenvolvimento*

## 👥 Integrantes da Equipe

| Nome                          | Matrícula   | GitHub User                                           |
|-------------------------------|-------------|-------------------------------------------------------|
| Vitória Milhomem Soares       | 2024111648  | [@vitoriamilhomem](https://github.com/vitoriamilhomem)|
| Matheus de Sousa Silva   | 2024110828  | [@math3us-sousa](https://github.com/math3us-sousa)         |
| Vitória Ferreira Leal Santos | 2024111649  | [@vitorialeal06](https://github.com/vitorialeal06)     |
| Rhyan Nascimento de Sousa     | 2024110375  | [@drgeralt](https://github.com/drgeralt)              |
| Gabriel Rodrigues Costa Ferreira | 2024111694 | [@Gabbilless](https://github.com/Gabbilless)        |
| Rick Ribeiro | 2024110431 | [@rickribeiroo](https://github.com/rickribeiroo)        |

## 🎥 Apresentação do Projeto

📹 **[Link do Vídeo de Apresentação](https://youtu.be/N42ZZCQRpSQ)**

*Vídeo demonstrando o funcionamento completo do sistema RHEase e explicando as principais funcionalidades implementadas para automação de RH.*

## 🛠️ Tecnologias Utilizadas

*[Preencher com as tecnologias específicas do projeto após análise do código]*

**Frontend:**
- HTML, CSS JavaScript

**Backend:**
- PHP

**Banco de Dados:**
- MySQL

**Outras Ferramentas:**
- GitFlow
- Padrão MVC

## ⚙️ Configuração e Execução

### Pré-requisitos

Antes de executar o RHEase, certifique-se de ter instalado:

- XAMPP

### Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/drgeralt/RHEase.git
   cd RHEase
   Obs: O clone deve ser feito sob o diretório htdocs, sob o diretório de instalação do XAMPP
   ```


2. **Configure o banco de dados:**
   ```bash
   Após rodar o XAMPP e iniciar o Apache e o MySQL, acesse o endereço 127.0.0.1, vá em phpadmin e use o backup do banco de dados chamado database.db
   ```


### Execução

1. **Inicie o sistema:**
   ```Inicie o Apache e MySQL a partir do XAMPP Control panel
   ```

2. **Acesse a aplicação:**
    - Sistema Principal: `http://localhost/RHEase/public`


## 📁 Estrutura do Projeto

```
RHEase/
├── app/                # Código do servidor
│   ├── Controller/        # Controladores do RHease
│   ├── models/            # Modelos de dados
│   ├── Core/              # Códigos comuns
│   └── views/             # Interfaces de usuário
├── public/              
│   ├── src/
│   │   ├── css/           # Estilização
│   │   ├── js/            # Scripts .js
├── config.php             # Configurações do banco de dados
├── README.md
└── database.sql
```

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade de RH'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

## 📄 Licença

Este projeto foi desenvolvido para fins acadêmicos como parte da disciplina de Engenharia de Software da UFT.

## 📞 Contato

Para dúvidas ou sugestões sobre o RHEase, entre em contato com a equipe:

- 📧 Email: [rhyan.sousa@mail.uft.edu.br]
- 🐙 GitHub: [RHEase Repository](https://github.com/drgeralt/RHEase)

---

*Desenvolvido️ por estudantes de Ciência da Computação na UFT - Campus Palmas*
