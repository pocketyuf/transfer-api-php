# Transfer API (PHP + SQL Server + NGINX + Docker)

API REST simples para simular transferências bancárias entre contas, com suporte a transações seguras, concorrência e teste de carga.

## 🧱 Tecnologias

- PHP 8.1 com Apache;
- SQL Server 2022;
- Docker & Docker Compose;
- NGINX como balanceador de carga;
- PDO + SQLSRV para acesso ao banco;

---

## 📂 Estrutura do projeto

<pre>
transfer-api-php/
├── docker-compose.yml                # ponto central da orquestração
├── nginx/
│   └── default.conf                  # config do NGINX
├── php/
│   ├── Dockerfile                    # imagem do PHP com extensões
│   ├── index.php                     # arquivo base de roteamento simples
│   ├── transfer.php                  # endpoint principal da API
│   ├── database/
│   │   ├── setup_db.php              # cria o banco/tabelas/dados
│   │   ├── sql_server.php            # credenciais/conexão
│   │   └── test_connection.php       # diagnóstico simples da conexão
│   └── tests/
│       ├── stress_test.php           # disparador de carga concorrente
│       └── stress_worker.php         # executor das chamadas de estresse
└── sql/
    └── schema.sql                    # opcional — estrutura da base para execução via SQL puro
</pre>

---

## 🚀 Como rodar o projeto

### 1. Pré-requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

### 2. Subir os containers

No diretório raiz do projeto, executar:
```bash
docker compose up --build -d
```

### 3. Verificar se a API está online

```curl
curl http://localhost:8080/
```

**Resposta esperada:**
```json
{
  "mensagem": "PHP carregado com sucesso!",
  "info": "API de Transferência - Acesse /transferir para realizar uma transferência ou execute o teste de estresse."
}
```

### 4. Inicializar o banco
```bash
docker exec -it php1 php database/setup_db.php
```

Contas de teste
| ID | Nome           | Saldo inicial |
|----|----------------|---------------|
| 1  | Carlos Silva   | R$ 1000,00    |
| 2  | João Pereira   | R$ 1500,00    |
| 3  | Maria Oliveira | R$ 2000,00    |
| 4  | Ana Souza      | R$ 1200,00    |

### 5. Testar conexão com o banco
```bash
docker exec -it php1 php database/test_connection.php
```

### 6. Teste de transação única
#### Endpoint: POST /transfer.php  
Realiza uma transferência entre contas.
```bash
curl -X POST http://localhost:8080/transfer.php \
     -H "Content-Type: application/json" \
     -d '{"origem": 2, "destino": 1, "valor": 9.99}'
```
```
Resposta de sucesso (HTTP 200):
- json { "mensagem": "Valor transferido com sucesso!" }
Erros comuns:
- Conta não encontrada
- Saldo insuficiente
- Dados inválidos
```

### 7. Teste de carga
Para simular concorrência:
``` bash
docker exec -it php1 php tests/stress_test.php
```

## 🔒 Segurança
- As queries são parametrizadas para evitar SQL Injection, enviando as variáveis por referência;
- Transações com locking (UPDLOCK, ROWLOCK) para evitar concorrência e deadlocks;

## 📌 Observações e detalhes
- Este projeto não usa frameworks, tudo é feito via PHP puro;
- O campo de valor foi criado em DECIMAL, pois FLOAT pode gerar inconsistências;
- Projeto está estruturado de forma stateless na camada da API e do load balancer. O estado fica centralizado no banco de dados, o que facilita escalabilidade horizontal (pode-se rodar quantos containers PHP quiser) e resiliência, porque não depende de sessão no servidor;
- Comportamento round-robin, típico do NGINX para balanceamento simples;
- Utilizando imagem oficial da Microsoft pra SQL Server e imagem PHP 8.1 com Apache;

## 🧑‍💻 Autor
Desenvolvido por [Raphael](https://www.linkedin.com/in/raphael-deodato/).
