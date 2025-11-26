# 💼 RHEase

<img width="777" height="202" alt="image" src="https://github.com/user-attachments/assets/a8f8eb07-5703-458e-8c70-ce1bd9c591af" />

Sistema completo de automação para Recursos Humanos

---

## 📚 Informações Acadêmicas

**Universidade:** Universidade Federal do Tocantins - Campus Palmas  
**Curso:** Ciência da Computação  
**Disciplina:** Engenharia de Software  
**Semestre:** 2025/2  
**Professor:** Edeilson Milhomem

---

## 🔗 Links Importantes (Entrega Final)

| Artefato | Link de Acesso |
|----------|----------------|
| Landing Page (Produto) | 👉 Acessar Landing Page |
| Sistema Implantado (Live) | 👉 Acessar Sistema Online |
| Vídeo de Demonstração | 🎬 Assistir Vídeo |
| Apresentação Final | 📄 Ver Apresentação |

---

## 📋 Descrição do Projeto

O RHEase é uma solução de software completa, projetada para automatizar e otimizar os processos-chave do departamento de Recursos Humanos de uma empresa. Com foco em eficiência operacional, redução de tarefas manuais e melhoria da experiência dos colaboradores, o sistema centraliza as rotinas de RH em uma única plataforma moderna e integrada.

A aplicação cobre todo o ciclo de vida do colaborador — desde o recrutamento até o desligamento — permitindo que o setor de RH atue de forma mais estratégica e orientada por dados.

---

## 🎯 Objetivos

- **Automatizar processos de RH:** Reduzir tarefas manuais e repetitivas através de fluxos automatizados
- **Centralizar informações:** Unificar dados de colaboradores em uma plataforma única e segura
- **Melhorar a experiência do colaborador:** Facilitar o acesso a informações e serviços de RH
- **Aumentar a eficiência operacional:** Otimizar tempo e recursos da equipe de RH
- **Facilitar tomadas de decisão:** Fornecer relatórios e métricas para gestão estratégica

---

## ✅ Funcionalidades Implementadas

### 🔐 Gestão de Acesso e Segurança
- ✅ Autenticação segura com hash de senha (password_hash)
- ✅ Cadastro público de usuários com validação de dados
- ✅ Ativação de conta por e-mail com token único (PHPMailer / SMTP)
- ✅ Recuperação de senha com link temporário
- ✅ Controle de acesso por perfil (RBAC): Gestor/Admin x Colaborador
- ✅ Multi-tenancy: seletor de empresa/filial impactando relatórios e holerites

### 📊 Dashboards
**Dashboard do Gestor:**
- KPIs em tempo real: colaboradores ativos, vagas abertas, benefícios ativos
- Gráfico Donut de distribuição por tipo de contrato (CLT, PJ, Estágio)

**Dashboard do Colaborador:**
- Último registro de ponto
- Salário base e benefícios ativos
- Gráficos de horas semanais e composição salarial

### 👥 Gestão de Colaboradores
- ✅ CRUD completo
- ✅ Edição em modais via AJAX
- ✅ Busca em tempo real por nome/matrícula
- ✅ Desligamento lógico (Soft Delete) com preservação de histórico

### ⏱️ Controle de Frequência (Ponto)
- ✅ Registro de ponto com relógio em tempo real
- ✅ Geolocalização obrigatória
- ✅ Biometria facial com DeepFace (API Python)
- ✅ Bloqueio automático em caso de falha na validação
- ✅ Painel de gestão biométrica com reset de cadastro facial

### 🏥 Gestão de Benefícios
- ✅ CRUD de benefícios com tipos (Fixo, Variável, Descritivo)
- ✅ Regras automáticas por tipo de contrato
- ✅ Gestão de exceções manuais por colaborador

### 💰 Folha de Pagamento
- ✅ Motor de cálculo automático (salário base + benefícios - descontos)
- ✅ Geração de holerites em PDF (FPDF)
- ✅ Histórico acessível ao colaborador

### 🧠 Recrutamento e Seleção (ATS com IA)
- ✅ Gestão completa de vagas
- ✅ Portal público do candidato
- ✅ Upload de currículo em PDF
- ✅ Leitura automática de currículo (pdfparser)
- ✅ Análise por IA com Google Gemini
- ✅ Score de aderência (0-100%) com justificativa
- ✅ Ranking inteligente de candidatos

*Legenda: ✅ Implementado | ❌ Não implementado | 🔄 Em desenvolvimento*

## 👥 Integrantes da Equipe

| Nome | Matrícula | GitHub |
|------|-----------|--------|
| Vitória Milhomem Soares | 2024111648 | [@vitoriamilhomem](https://github.com/vitoriamilhomem) |
| Matheus de Sousa Silva | 2024110828 | [@math3us-sousa](https://github.com/math3us-sousa) |
| Vitória Ferreira Leal Santos | 2024111649 | [@vitorialeal06](https://github.com/vitorialeal06) |
| Rhyan Nascimento de Sousa | 2024110375 | [@drgeralt](https://github.com/drgeralt) |
| Gabriel Rodrigues Costa Ferreira | 2024111694 | [@Gabbilless](https://github.com/Gabbilless) |

---

## 🎥 Apresentação do Projeto

📹 **Link do vídeo demonstrativo:**

*Vídeo apresentando o funcionamento completo do sistema RHEase, suas funcionalidades principais e integração com recursos de inteligência artificial.*

---

## 🛠️ Tecnologias Utilizadas

**Backend:**
- PHP 8.2+ (MVC Puro)
- Composer

**Frontend:**
- HTML5
- CSS3 (Bootstrap 5)
- JavaScript (Vanilla + jQuery)

**Banco de Dados:**
- MySQL / MariaDB

**Inteligência Artificial & Integrações:**
- Python (Flask) – API de Reconhecimento Facial (DeepFace)
- Google Gemini API – Análise de currículos
- PHPMailer & FPDF – Envio de e-mails e geração de documentos

---

## ⚙️ Configuração e Execução

### Pré-requisitos

Antes de executar o RHEase, certifique-se de ter instalado:

- PHP 8.0+
- MySQL
- Python 3.x
- Composer
- XAMPP (Recomendado)

---

### Instalação

1. **Clone o repositório:**
```bash
git clone https://github.com/drgeralt/RHEase.git
cd RHEase
# Obs: O clone deve ser feito sob o diretório htdocs do XAMPP
```

2. **Instale dependências e configure o banco:**
```bash
composer install
# Importe o arquivo database.sql no seu MySQL
# Configure o arquivo .env com suas credenciais
```

3. **Inicie a API Facial (Python):**
```bash
cd app/api/facialapi
pip install -r requirements.txt
python app.py
```

---

### Execução

1. Inicie o Apache e MySQL pelo XAMPP.
2. Acesse a aplicação no navegador:
```
http://localhost/RHEase/public
```

---

## 📁 Estrutura do Projeto

```
RHEase/
├── app/                # Core da aplicação (MVC)
│   ├── Controller/     # Regras de negócio
│   ├── Model/          # Acesso a dados
│   ├── Core/           # Router e Configs
│   └── View/           # Interfaces de usuário
├── public/             # Assets públicos (CSS, JS, Uploads)
├── app/api/facialapi/  # API Python de IA
├── landing-page/       # Site de apresentação
├── docs/               # Documentação e vídeos
├── config.php          # Configurações
├── .env                # Variáveis de ambiente
└── database.sql        # Schema do banco
```

---

## 🤝 Contribuição

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. Commit suas mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para a branch (`git push origin feature/nova-funcionalidade`)
5. Abra um Pull Request

---

## 📄 Licença

Este projeto foi desenvolvido exclusivamente para fins acadêmicos como parte da disciplina de Engenharia de Software da Universidade Federal do Tocantins.

---

## 📞 Contato

Para dúvidas ou sugestões sobre o RHEase:

- 📧 Email: rhyan.sousa@mail.uft.edu.br  
- 🐙 GitHub: https://github.com/drgeralt/RHEase

---

*Desenvolvido por estudantes de Ciência da Computação da UFT - Campus Palmas*

