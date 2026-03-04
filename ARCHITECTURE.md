# 🏗️ Arquitetura Técnica - Gestor IA

Este documento detalha os padrões de engenharia e convenções adotadas no projeto.

## 🏗 Padrão Arquitetural

O projeto segue um padrão **MVC (Model-View-Controller)** simplificado, focado em alta performance e baixo overhead:

- **Controllers:** Não possuem lógica de negócio complexa. Eles orquestram a entrada (HTTP), chamam Models/Services e retornam a View ou JsonResponse.
- **Models:** Implementam a lógica de persistência usando PDO. Utilizam tipos estritos (Type Hinting) do PHP 8.1.
- **Core:** Contém as classes base (`Auth`, `Router`, `Csrf`) que não devem ser modificadas frequentemente.

## 💉 Injeção de Dependência (DI)

O roteador do projeto (`app/Core/Router.php`) implementa uma DI automática baseada em reflexão. 
- **Regra:** Dependências devem ser passadas como argumentos nos métodos dos Controllers.
- **Exemplo:** `public function index(Auth $auth, UserModel $users)`.

## 📂 Convenções de Nomenclatura (Nomenclature)

- **Classes/Controllers:** PascalCase (`ChatController`, `UserModel`).
- **Métodos/Variáveis:** camelCase (`findUserById`, `$currentUser`).
- **Arquivos de View:** snake_case ou kebab-case (`index.php`, `user_profile.php`).
- **Tabelas do Banco:** snake_case no plural (`users`, `chat_logs`).
- **Campos do Banco:** snake_case no singular (`created_at`, `manager_id`).

## 🛠 Padrões de Código

- **PSR-12:** Guia de estilo de código PHP.
- **Strict Types:** Todos os arquivos devem iniciar com `declare(strict_types=1);`.
- **SOLID:**
  - **Single Responsibility:** Services (ex: `UploadService`) cuidam de tarefas específicas fora dos Models.
  - **Dependency Inversion:** Controllers dependem de abstrações/classes de serviço injetadas.
- **Clean Code:** Métodos curtos, nomes descritivos e comentários apenas onde a lógica for intrinsecamente complexa.

## 🔒 Segurança

1. **CSRF:** Proteção obrigatória para todos os formulários e chamadas de API via `Csrf::getToken()` e `Csrf::validate()`.
2. **XSS:** Escapamento obrigatório de saída nas Views usando `htmlspecialchars()`.
3. **SQL Injection:** Uso estrito de Prepared Statements no PDO.
4. **RBAC:** Verificação de permissões centralizada na classe `Auth`.
