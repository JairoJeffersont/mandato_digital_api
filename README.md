# API Mandato Digital

API da aplicação Mandato Digital

## 🚀 Tecnologias Utilizadas

- PHP 8.x
- Slim Framework 4.11
- PSR-7 Implementation
- PHP-DI (Dependency Injection)
- JWT Authentication
- MySQL/MariaDB
- Composer para gerenciamento de dependências

## 📋 Pré-requisitos

- PHP 8.0 ou superior
- Composer
- MySQL/MariaDB
- Extensões PHP necessárias:
  - PDO
  - JSON
  - FileInfo

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone [url-do-repositorio]
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o banco de dados:
   - Importe o arquivo `db.sql` para seu servidor MySQL/MariaDB
   - Configure as credenciais do banco de dados no arquivo de configuração apropriado

## 📁 Estrutura do Projeto

```
.
├── public/             # Ponto de entrada da aplicação
├── src/               # Código fonte da aplicação
│   ├── Config/       # Configurações da aplicação
│   ├── Controllers/  # Controladores da API
│   ├── Helpers/      # Classes auxiliares
│   ├── Middleware/   # Middlewares da aplicação
│   ├── Models/       # Modelos de dados
│   └── routes.php    # Definição das rotas da API
├── vendor/           # Dependências do Composer
├── bootstrap.php     # Arquivo de inicialização
├── composer.json     # Configuração do Composer
└── db.sql           # Schema do banco de dados
```

## 🔐 Autenticação

A API utiliza autenticação JWT (JSON Web Tokens) para proteger as rotas. Para acessar endpoints protegidos, é necessário:

1. Obter um token através do endpoint de autenticação
2. Incluir o token no header das requisições:
   ```
   Authorization: Bearer [seu-token]
   ```

## 🛣️ Principais Endpoints

A documentação completa dos endpoints está disponível em /api-doc.

## 🔨 Desenvolvimento

Para iniciar o servidor de desenvolvimento:

```bash
php -S localhost:8000 -t public
```

## 🚀 Implantação em Produção

Para implantar a aplicação em um ambiente de produção:

1. Faça o upload de todos os arquivos para a pasta `api` na raiz da sua hospedagem
2. Configure as variáveis de ambiente para produção
3. Ajuste as permissões dos diretórios conforme necessário

## 📄 Licença

Este projeto está sob a licença MIT.

## 📞 Suporte

Para suporte e questões, por favor abra uma issue no repositório do projeto. 