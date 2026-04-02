
---

## 1. Finalidade do documento

Este documento organiza a execução técnica do MVP e da evolução inicial do produto em releases e sprints, definindo prioridades, dependências, entregáveis e critérios de pronto.

Seu objetivo é permitir que o desenvolvimento comece com ordem, previsibilidade e controle de escopo, evitando três erros comuns:

1. iniciar pelo módulo errado;
    
2. construir telas sem base de dados e regras maduras;
    
3. inflar o MVP antes de validar o núcleo operacional.
    

---

## 2. Estratégia de execução recomendada

A construção deve seguir esta lógica:

1. **fundação da plataforma**;
    
2. **governança e acesso**;
    
3. **base territorial e cadastro estrutural**;
    
4. **plano de contingência**;
    
5. **evento e comando operacional**;
    
6. **operação de campo mínima**;
    
7. **relatórios e auditoria**;
    
8. **ampliações pós-MVP**.
    

A ordem é deliberada. Não é racional começar por dashboards complexos, ajuda humanitária completa ou analytics antes de autenticação, tenant, plano e evento estarem estáveis.

---

## 3. Premissas de planejamento

### 3.1 Duração sugerida

- Sprints de 2 semanas.
    
- Releases agrupando 2 a 3 sprints, conforme estabilidade.
    

### 3.2 Equipe mínima ideal

- 1 Product Owner / decisor funcional;
    
- 1 desenvolvedor backend principal;
    
- 1 desenvolvedor frontend full stack ou apoio frontend;
    
- 1 designer/UX eventual ou apoio de prototipação;
    
- 1 QA funcional, mesmo que parcial;
    
- 1 responsável técnico de domínio institucional.
    

### 3.3 Regra operacional

Nenhuma sprint deve misturar excesso de módulos paralelos. O backlog deve respeitar encadeamento técnico e dependências reais.

---

## 4. Definições operacionais do backlog

### 4.1 Classificação de prioridade

- **P1**: essencial para o MVP funcionar;
    
- **P2**: importante para robustez operacional imediata;
    
- **P3**: ampliação pós-MVP;
    
- **P4**: evolução estratégica futura.
    

### 4.2 Tipo de item

- infraestrutura;
    
- backend;
    
- frontend;
    
- banco de dados;
    
- segurança;
    
- testes;
    
- documentação;
    
- UX/UI.
    

### 4.3 Critérios gerais de pronto

Um item só deve ser considerado concluído quando possuir:

- implementação funcional;
    
- validação backend;
    
- controle de permissão;
    
- persistência correta;
    
- tratamento de erro mínimo;
    
- auditoria, quando aplicável;
    
- teste funcional básico;
    
- documentação técnica mínima de apoio.
    

---

# BLOCO A — RELEASE 0: FUNDAÇÃO TÉCNICA

## Objetivo do release

Preparar a base arquitetural e técnica do sistema para receber os módulos do MVP.

## Resultado esperado

Projeto Laravel inicial estruturado, banco configurado, autenticação básica preparada, padrão de módulos definido e ambiente pronto para desenvolvimento contínuo.

---

## Sprint 0.1 — Estrutura-base do projeto

### Itens do backlog

#### BT-001 — Criar projeto base Laravel

- Prioridade: P1
    
- Tipo: infraestrutura/backend
    
- Dependências: nenhuma
    
- Entregável: projeto Laravel configurado
    
- Critério de pronto: aplicação sobe localmente com ambiente versionado
    

#### BT-002 — Configurar ambiente PostgreSQL + PostGIS

- Prioridade: P1
    
- Tipo: banco de dados/infraestrutura
    
- Dependências: BT-001
    
- Entregável: conexão estável com banco
    
- Critério de pronto: conexão funcional e migrations executáveis
    

#### BT-003 — Estruturar arquitetura modular do projeto

- Prioridade: P1
    
- Tipo: backend/arquitetura
    
- Dependências: BT-001
    
- Entregável: pastas por domínio e convenções de projeto
    
- Critério de pronto: estrutura padronizada aplicada ao repositório
    

#### BT-004 — Configurar autenticação base

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-001
    
- Entregável: autenticação funcional inicial
    
- Critério de pronto: login/logout operando com sessão
    

#### BT-005 — Configurar layout base do painel

- Prioridade: P1
    
- Tipo: frontend/UX
    
- Dependências: BT-001
    
- Entregável: layout com sidebar, topbar e área de conteúdo
    
- Critério de pronto: estrutura navegável e reutilizável
    

#### BT-006 — Criar padrão global de respostas e mensagens

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-001
    
- Entregável: padrão de feedback de sucesso, erro e aviso
    
- Critério de pronto: mensagens padronizadas disponíveis no sistema
    

#### BT-007 — Criar política base de auditoria técnica

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-001
    
- Entregável: estrutura inicial para logs de auditoria
    
- Critério de pronto: eventos críticos já podem ser auditados
    

#### BT-008 — Implantar schema inicial do banco

- Prioridade: P1
    
- Tipo: banco de dados
    
- Dependências: BT-002
    
- Entregável: migrations principais criadas e executáveis
    
- Critério de pronto: schema inicial aplicado sem erro
    

---

## Sprint 0.2 — Segurança, tenant e base de navegação

### Itens do backlog

#### BT-009 — Implementar contexto multi-tenant

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-008
    
- Entregável: resolução de tenant por contexto autenticado
    
- Critério de pronto: leitura e gravação isoladas por tenant
    

#### BT-010 — Implementar perfis e permissões

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-004, BT-008
    
- Entregável: middleware/policies por perfil
    
- Critério de pronto: telas e ações protegidas por permissão
    

#### BT-011 — Criar seeders iniciais de domínios globais

- Prioridade: P1
    
- Tipo: banco de dados
    
- Dependências: BT-008
    
- Entregável: severidades, tipos de posição, permissões-base
    
- Critério de pronto: seeders reproduzíveis em ambiente novo
    

#### BT-012 — Criar dashboard inicial vazio por perfil

- Prioridade: P1
    
- Tipo: frontend
    
- Dependências: BT-005, BT-010
    
- Entregável: landing interna por perfil
    
- Critério de pronto: usuário autenticado entra em dashboard compatível
    

#### BT-013 — Estruturar navegação lateral por módulos do MVP

- Prioridade: P1
    
- Tipo: frontend/UX
    
- Dependências: BT-005
    
- Entregável: menu funcional do MVP
    
- Critério de pronto: navegação consistente entre módulos já habilitados
    

#### BT-014 — Implantar trilha mínima de auditoria real

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-007, BT-008
    
- Entregável: gravação de logs críticos no banco
    
- Critério de pronto: login, criação de usuário e mudanças críticas geram log
    

---

# BLOCO B — RELEASE 1: GOVERNANÇA, USUÁRIOS E BASE TERRITORIAL

## Objetivo do release

Colocar o sistema em condição de uso administrativo institucional mínimo.

## Resultado esperado

Tenant consegue gerenciar usuários, organização, contatos, território, unidades e áreas de risco.

---

## Sprint 1.1 — Administração do tenant

### Itens do backlog

#### BT-015 — Implementar módulo de organizações

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-009, BT-010
    
- Entregável: CRUD de organização principal
    
- Critério de pronto: organização criada, editada e exibida
    

#### BT-016 — Implementar módulo de unidades organizacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-015
    
- Entregável: CRUD hierárquico de unidades
    
- Critério de pronto: unidade pai-filho validada corretamente
    

#### BT-017 — Implementar módulo de usuários

- Prioridade: P1
    
- Tipo: backend/frontend/segurança
    
- Dependências: BT-010, BT-015
    
- Entregável: listagem, criação, edição, inativação
    
- Critério de pronto: CRUD funcional com auditoria
    

#### BT-018 — Implementar associação de perfis a usuários

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-017, BT-010
    
- Entregável: gestão de perfis por usuário
    
- Critério de pronto: usuário recebe e perde perfil com controle de permissão
    

#### BT-019 — Implementar catálogo de órgãos parceiros

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-015
    
- Entregável: CRUD de órgãos parceiros
    
- Critério de pronto: parceiros listados e editáveis
    

#### BT-020 — Implementar contatos estratégicos

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-019 ou BT-015
    
- Entregável: CRUD de contatos estratégicos
    
- Critério de pronto: contatos disponíveis e filtráveis
    

---

## Sprint 1.2 — Território, unidades e risco

### Itens do backlog

#### BT-021 — Implementar módulo de territórios

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-009, BT-010
    
- Entregável: CRUD de territórios
    
- Critério de pronto: território criado e listado
    

#### BT-022 — Implementar unidades territoriais hierárquicas

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-021
    
- Entregável: CRUD de subdivisões territoriais
    
- Critério de pronto: hierarquia consistente e sem ciclos
    

#### BT-023 — Implementar módulo de áreas de risco

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-022
    
- Entregável: CRUD de áreas de risco
    
- Critério de pronto: área vinculada à unidade territorial corretamente
    

#### BT-024 — Implementar cadastro básico de cenários de risco

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-023
    
- Entregável: CRUD de cenários
    
- Critério de pronto: cenário disponível para plano e evento
    

#### BT-025 — Implementar cadastro de abrigos

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-022
    
- Entregável: CRUD de abrigos
    
- Critério de pronto: abrigo salvo com capacidade e status
    

#### BT-026 — Implementar cadastro de pontos de apoio e bases operacionais

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-022
    
- Entregável: CRUD desses pontos estratégicos
    
- Critério de pronto: registros disponíveis para consulta e plano
    

#### BT-027 — Implementar importação/desenho inicial de geometria

- Prioridade: P2
    
- Tipo: frontend/backend/geoespacial
    
- Dependências: BT-023, BT-002
    
- Entregável: vínculo geométrico simples da área de risco
    
- Critério de pronto: geometria salva e recuperada com integridade
    

---

# BLOCO C — RELEASE 2: PLANO DE CONTINGÊNCIA

## Objetivo do release

Entregar o núcleo documental do produto com versionamento e publicação controlada.

## Resultado esperado

Tenant consegue criar plano, abrir versões, editar seções, manter matriz de responsabilidades, protocolos de ativação e publicar versão vigente.

---

## Sprint 2.1 — Núcleo do plano

### Itens do backlog

#### BT-028 — Implementar módulo de planos de contingência

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-021, BT-017
    
- Entregável: CRUD de planos
    
- Critério de pronto: plano criado e listado por território/status
    

#### BT-029 — Implementar módulo de versões do plano

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-028
    
- Entregável: criação de versões e listagem
    
- Critério de pronto: versão criada com numeração única por plano
    

#### BT-030 — Implementar clonagem de versão

- Prioridade: P2
    
- Tipo: backend
    
- Dependências: BT-029
    
- Entregável: service de clonagem de versão
    
- Critério de pronto: nova versão reaproveita estrutura da anterior
    

#### BT-031 — Implementar editor de seções do plano

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-029
    
- Entregável: edição por seção
    
- Critério de pronto: seções persistem conteúdo e ordem
    

#### BT-032 — Implementar itens estruturados por seção

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-031
    
- Entregável: cadastro e atualização de itens estruturados
    
- Critério de pronto: editor aceita blocos de conteúdo estruturado
    

---

## Sprint 2.2 — Responsabilidades, ativação e publicação

### Itens do backlog

#### BT-033 — Implementar matriz de responsabilidades

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-029, BT-020, BT-017
    
- Entregável: tabela editável de responsabilidades
    
- Critério de pronto: cada ação possui responsável principal válido
    

#### BT-034 — Implementar protocolos de ativação

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-029
    
- Entregável: CRUD de protocolos
    
- Critério de pronto: gatilho, passos e fluxo de comunicação persistidos
    

#### BT-035 — Implementar anexos do plano

- Prioridade: P2
    
- Tipo: backend/frontend/arquivos
    
- Dependências: BT-029
    
- Entregável: upload e listagem de anexos
    
- Critério de pronto: anexos vinculados à versão correta
    

#### BT-036 — Implementar checklist de completude do plano

- Prioridade: P1
    
- Tipo: backend
    
- Dependências: BT-031, BT-033, BT-034
    
- Entregável: verificação automatizada de versão apta à publicação
    
- Critério de pronto: sistema informa versão completa/incompleta com motivos
    

#### BT-037 — Implementar fluxo de submissão, aprovação e publicação

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-036
    
- Entregável: estados controlados da versão
    
- Critério de pronto: apenas uma versão vigente por plano
    

#### BT-038 — Implementar geração inicial de PDF do plano

- Prioridade: P2
    
- Tipo: backend/relatórios
    
- Dependências: BT-037
    
- Entregável: PDF institucional do plano
    
- Critério de pronto: documento gerado e registrado em relatórios
    

---

# BLOCO D — RELEASE 3: EVENTO, COMANDO E OPERAÇÃO MÍNIMA

## Objetivo do release

Entregar o núcleo operacional do produto.

## Resultado esperado

Tenant consegue abrir evento, alterar status, ativar comando, registrar objetivos, ocorrências, missões, danos e necessidades.

---

## Sprint 3.1 — Evento e status

### Itens do backlog

#### BT-039 — Implementar módulo de eventos

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-021, BT-028, BT-024, BT-011
    
- Entregável: listagem, abertura e detalhe do evento
    
- Critério de pronto: evento aberto com código único, tipologia e severidade válidas
    

#### BT-040 — Implementar histórico de status do evento

- Prioridade: P1
    
- Tipo: backend
    
- Dependências: BT-039
    
- Entregável: transições com rastreio
    
- Critério de pronto: toda alteração de status gera histórico
    

#### BT-041 — Implementar timeline do evento

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039
    
- Entregável: linha do tempo manual e automática
    
- Critério de pronto: marcos podem ser cadastrados e exibidos em ordem
    

#### BT-042 — Implementar encerramento formal do evento

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-040
    
- Entregável: fechamento institucional do evento
    
- Critério de pronto: evento encerrado com motivo, síntese e consistência temporal
    

---

## Sprint 3.2 — Comando, objetivos e decisões

### Itens do backlog

#### BT-043 — Implementar estrutura de comando

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-011
    
- Entregável: ativação da estrutura de comando por evento
    
- Critério de pronto: estrutura criada e vinculada ao evento ativo
    

#### BT-044 — Implementar posições funcionais e designações

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-043, BT-017
    
- Entregável: cadastro de posições ativas e ocupantes
    
- Critério de pronto: posição pode receber designação válida com histórico temporal
    

#### BT-045 — Implementar objetivos operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039
    
- Entregável: CRUD de objetivos por evento
    
- Critério de pronto: código único por evento e controle de status
    

#### BT-046 — Implementar decisões operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039
    
- Entregável: registro de decisões do evento
    
- Critério de pronto: decisão com autoria, data e status de implementação
    

#### BT-047 — Implementar painel de comando do evento

- Prioridade: P2
    
- Tipo: frontend
    
- Dependências: BT-043, BT-044, BT-045, BT-046
    
- Entregável: tela consolidada de comando
    
- Critério de pronto: visão clara da estrutura e dos objetivos do evento
    

---

## Sprint 3.3 — Ocorrências, missões e registros técnicos

### Itens do backlog

#### BT-048 — Implementar ocorrências operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-022
    
- Entregável: CRUD de ocorrências por evento
    
- Critério de pronto: ocorrência criada com código único no evento
    

#### BT-049 — Implementar equipes operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-017
    
- Entregável: CRUD de equipes
    
- Critério de pronto: equipe disponível para designação em missão
    

#### BT-050 — Implementar recursos operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-017
    
- Entregável: CRUD de recursos
    
- Critério de pronto: recurso disponível para alocação e controle de status
    

#### BT-051 — Implementar missões operacionais

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-048, BT-045
    
- Entregável: CRUD de missões
    
- Critério de pronto: missão vinculada ao evento, com prioridade e status
    

#### BT-052 — Implementar designação de equipes/recursos às missões

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-049, BT-050, BT-051
    
- Entregável: vínculo operacional de meios às missões
    
- Critério de pronto: missão recebe equipe e/ou recurso disponível
    

#### BT-053 — Implementar avaliação de danos

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-022
    
- Entregável: cadastro de danos humanos, materiais e ambientais
    
- Critério de pronto: números validados e histórico preservado
    

#### BT-054 — Implementar avaliação de necessidades

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-039, BT-022
    
- Entregável: cadastro de necessidades do evento
    
- Critério de pronto: necessidade com categoria, prioridade e status
    

#### BT-055 — Implementar diário operacional

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-039
    
- Entregável: cronologia operacional do evento
    
- Critério de pronto: logs podem ser manuais e automáticos
    

---

# BLOCO E — RELEASE 4: RELATÓRIOS, AUDITORIA E ESTABILIZAÇÃO DO MVP

## Objetivo do release

Fechar o MVP com emissão documental, rastreabilidade e qualidade operacional mínima.

## Resultado esperado

Sistema já pode ser demonstrado, testado institucionalmente e usado em cenário controlado.

---

## Sprint 4.1 — Relatórios e documentos

### Itens do backlog

#### BT-056 — Implementar geração de relatório por evento

- Prioridade: P1
    
- Tipo: backend/frontend/relatórios
    
- Dependências: BT-039, BT-053, BT-054, BT-046
    
- Entregável: relatório institucional do evento
    
- Critério de pronto: relatório gerado somente com completude mínima validada
    

#### BT-057 — Implementar histórico de relatórios gerados

- Prioridade: P1
    
- Tipo: backend/frontend
    
- Dependências: BT-056
    
- Entregável: listagem de relatórios emitidos
    
- Critério de pronto: usuário consegue consultar e baixar documentos gerados
    

#### BT-058 — Implementar dashboard operacional inicial real

- Prioridade: P1
    
- Tipo: frontend/backend
    
- Dependências: BT-039, BT-048, BT-051, BT-054
    
- Entregável: dashboard com indicadores do MVP
    
- Critério de pronto: cards e atalhos alimentados por dados reais do tenant
    

#### BT-059 — Implementar dashboard executivo simplificado

- Prioridade: P2
    
- Tipo: frontend/backend
    
- Dependências: BT-056, BT-057
    
- Entregável: visão gerencial resumida
    
- Critério de pronto: métricas mínimas por período, tipologia e severidade
    

---

## Sprint 4.2 — Auditoria, testes e estabilização

### Itens do backlog

#### BT-060 — Expandir auditoria para eventos críticos do MVP

- Prioridade: P1
    
- Tipo: backend/segurança
    
- Dependências: BT-014, BT-056
    
- Entregável: cobertura de auditoria ampliada
    
- Critério de pronto: plano, evento, status, usuário e relatório auditados
    

#### BT-061 — Implementar tela de consulta de auditoria

- Prioridade: P1
    
- Tipo: frontend/backend
    
- Dependências: BT-060
    
- Entregável: listagem filtrável de logs
    
- Critério de pronto: auditor e admin local conseguem consultar trilha conforme escopo
    

#### BT-062 — Criar suíte mínima de testes funcionais do MVP

- Prioridade: P1
    
- Tipo: testes
    
- Dependências: BT-017, BT-028, BT-039, BT-043, BT-051, BT-056
    
- Entregável: testes cobrindo fluxos críticos
    
- Critério de pronto: casos essenciais passam de forma reprodutível
    

#### BT-063 — Revisar UX crítica do MVP

- Prioridade: P1
    
- Tipo: UX/UI
    
- Dependências: telas principais prontas
    
- Entregável: ajustes de usabilidade nas telas críticas
    
- Critério de pronto: redução de atrito em login, plano, evento, ocorrência e missão
    

#### BT-064 — Corrigir bugs e estabilizar release MVP

- Prioridade: P1
    
- Tipo: qualidade
    
- Dependências: todos os itens anteriores do MVP
    
- Entregável: versão funcional estável
    
- Critério de pronto: ausência de bugs bloqueantes conhecidos
    

---

# BLOCO F — RELEASE 5: AMPLIAÇÃO OPERACIONAL IMEDIATA

## Objetivo do release

Aprofundar a utilidade do produto logo após validação do MVP.

## Resultado esperado

Sistema passa a atender melhor preparação, abrigos ativos, assistência e pós-evento inicial.

---

## Sprint 5.1 — Preparação e prontidão

### Itens do backlog

#### BT-065 — Implementar simulados

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-028, BT-017
    
- Entregável: cadastro e execução de simulados
    
- Critério de pronto: simulado criado, executado e historizado
    

#### BT-066 — Implementar treinamentos

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-017
    
- Entregável: módulo de treinamentos
    
- Critério de pronto: capacitações com participantes registrados
    

#### BT-067 — Implementar checklists de prontidão

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-028, BT-017
    
- Entregável: checklists e itens de prontidão
    
- Critério de pronto: checklist com status consolidado
    

#### BT-068 — Implementar pendências preparatórias

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-067
    
- Entregável: tarefas de preparação
    
- Critério de pronto: pendência atribuída, acompanhada e concluída
    

---

## Sprint 5.2 — Abrigos ativos e ajuda humanitária

### Itens do backlog

#### BT-069 — Implementar abrigos ativos

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-025, BT-039
    
- Entregável: ativação operacional de abrigo no evento
    
- Critério de pronto: abrigo ativo vinculado ao evento com contadores mínimos
    

#### BT-070 — Implementar famílias e pessoas assistidas

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-069
    
- Entregável: cadastro de assistidos
    
- Critério de pronto: família e pessoa vinculadas ao evento/abrigo
    

#### BT-071 — Implementar estoque humanitário

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-039
    
- Entregável: cadastro e saldo de itens humanitários
    
- Critério de pronto: entradas e saídas alteram saldo corretamente
    

#### BT-072 — Implementar distribuição humanitária

- Prioridade: P2
    
- Tipo: backend/frontend
    
- Dependências: BT-070, BT-071
    
- Entregável: entrega formal de itens
    
- Critério de pronto: distribuição não permite saldo negativo e gera histórico
    

---

# BLOCO G — RELEASE 6: RECUPERAÇÃO E MEMÓRIA INSTITUCIONAL

## Objetivo do release

Cobrir a fase pós-evento e consolidar aprendizado institucional.

---

## Sprint 6.1 — Recuperação

### Itens do backlog

#### BT-073 — Implementar plano de recuperação

- Prioridade: P3
    
- Tipo: backend/frontend
    
- Dependências: BT-042
    
- Entregável: módulo de recuperação por evento
    
- Critério de pronto: plano criado para evento elegível
    

#### BT-074 — Implementar ações de recuperação

- Prioridade: P3
    
- Tipo: backend/frontend
    
- Dependências: BT-073
    
- Entregável: ações por eixo temático
    
- Critério de pronto: ação acompanhada por status e prazo
    

#### BT-075 — Implementar atualizações de andamento da recuperação

- Prioridade: P3
    
- Tipo: backend/frontend
    
- Dependências: BT-074
    
- Entregável: histórico de progresso por ação
    
- Critério de pronto: atualizações percentuais e descritivas registradas
    

---

## Sprint 6.2 — Pós-evento, lições e memória

### Itens do backlog

#### BT-076 — Implementar avaliação pós-evento

- Prioridade: P3
    
- Tipo: backend/frontend
    
- Dependências: BT-042
    
- Entregável: avaliação formal do evento encerrado
    
- Critério de pronto: SWOT/recomendações persistidas
    

#### BT-077 — Implementar lições aprendidas

- Prioridade: P3
    
- Tipo: backend/frontend
    
- Dependências: BT-065 ou BT-042
    
- Entregável: catálogo de lições aprendidas
    
- Critério de pronto: lição vinculada a evento ou simulado
    

#### BT-078 — Implementar histórico consolidado do desastre

- Prioridade: P3
    
- Tipo: backend
    
- Dependências: BT-042, BT-056
    
- Entregável: memória institucional estruturada
    
- Critério de pronto: evento encerrado gera registro histórico utilizável
    

---

# BLOCO H — RELEASE 7: CAMADA EXECUTIVA E ESCALA

## Objetivo do release

Ampliar supervisão, análise e capacidade de escala institucional.

---

## Itens principais

#### BT-079 — Implementar snapshots analíticos

- Prioridade: P3
    
- Tipo: backend/analytics
    
- Dependências: dados históricos mínimos
    
- Entregável: snapshots periódicos
    
- Critério de pronto: indicadores persistidos por referência temporal
    

#### BT-080 — Implementar cache de dashboards

- Prioridade: P3
    
- Tipo: backend/performance
    
- Dependências: BT-079
    
- Entregável: camada de cache analítico
    
- Critério de pronto: dashboards com resposta rápida em dados agregados
    

#### BT-081 — Implementar visão consolidada regional/estadual

- Prioridade: P4
    
- Tipo: backend/frontend
    
- Dependências: maturidade multi-tenant e governança superior
    
- Entregável: consolidação por recorte territorial ampliado
    
- Critério de pronto: painel agregado respeitando escopo e permissão
    

#### BT-082 — Preparar PWA operacional

- Prioridade: P4
    
- Tipo: frontend/arquitetura
    
- Dependências: estabilização do MVP
    
- Entregável: base para uso em mobilidade
    
- Critério de pronto: interface apta a evolução offline-first parcial
    

---

# BLOCO I — BACKLOG TÉCNICO TRANSVERSAL

## Itens contínuos em paralelo

#### BT-083 — Criar padrão de Requests/Validators por módulo

- Prioridade: P1
    
- Tipo: backend
    
- Dependências: módulos em construção
    
- Entregável: validações formais desacopladas do controller
    

#### BT-084 — Criar políticas e gates por permissão

- Prioridade: P1
    
- Tipo: segurança/backend
    
- Dependências: BT-010
    
- Entregável: proteção consistente de ações e telas
    

#### BT-085 — Criar factories e seeders de teste

- Prioridade: P1
    
- Tipo: testes/banco de dados
    
- Dependências: schema estabilizado
    
- Entregável: massa de teste reprodutível
    

#### BT-086 — Criar padrão de auditoria por service crítico

- Prioridade: P1
    
- Tipo: backend
    
- Dependências: BT-014
    
- Entregável: auditoria uniforme nos fluxos críticos
    

#### BT-087 — Criar componentes reutilizáveis de tabela, filtro e formulário

- Prioridade: P1
    
- Tipo: frontend/UX
    
- Dependências: layout base
    
- Entregável: aceleração da construção de telas
    

#### BT-088 — Criar padrão institucional de PDF

- Prioridade: P2
    
- Tipo: backend/frontend visual
    
- Dependências: geração de relatórios
    
- Entregável: template visual reutilizável para relatórios oficiais
    

#### BT-089 — Documentar endpoints internos e serviços críticos

- Prioridade: P2
    
- Tipo: documentação técnica
    
- Dependências: módulos implementados
    
- Entregável: documentação evolutiva de backend
    

#### BT-090 — Implantar monitoramento básico de erros

- Prioridade: P2
    
- Tipo: infraestrutura/qualidade
    
- Dependências: ambiente executando
    
- Entregável: rastreio de falhas operacionais
    

---

# BLOCO J — ORDEM REAL DE CONSTRUÇÃO RECOMENDADA

## Sequência objetiva

1. Fundação técnica
    
2. Tenant e segurança
    
3. Usuários, perfis e organização
    
4. Território e áreas de risco
    
5. Abrigos e pontos estratégicos básicos
    
6. Plano de contingência
    
7. Versionamento e publicação do plano
    
8. Evento
    
9. Status e timeline do evento
    
10. Estrutura de comando
    
11. Objetivos, ocorrências e missões
    
12. Danos e necessidades
    
13. Relatório por evento
    
14. Auditoria completa do MVP
    
15. Estabilização
    
16. Ampliações operacionais
    

---

# BLOCO K — RISCOS DE EXECUÇÃO E CONTRAMEDIDAS

## Risco 1 — Escopo inflado cedo demais

**Problema:** tentar colocar ajuda humanitária completa, analytics avançado e módulo estadual antes de estabilizar o núcleo.

**Contramedida:** congelar MVP até o Release 4.

## Risco 2 — Backend sem regra de negócio consolidada

**Problema:** controllers inchados e regras inconsistentes.

**Contramedida:** concentrar regra em Services desde o início.

## Risco 3 — Frontend adiantado em relação ao backend

**Problema:** telas prontas sem comportamento real.

**Contramedida:** construir frontend por bloco funcional fechado.

## Risco 4 — Dados históricos frágeis

**Problema:** perda de rastreabilidade operacional.

**Contramedida:** priorizar auditoria, status history e relatórios já no MVP.

## Risco 5 — Geoprocessamento atrasando o MVP

**Problema:** complexidade espacial travando o resto do sistema.

**Contramedida:** manter geometria útil, mas simples, no MVP; avançar refinamentos depois.

---

# BLOCO L — DEFINIÇÃO DE PRONTO POR RELEASE

## Release 0 pronto quando:

- projeto sobe com autenticação básica;
    
- banco e migrations funcionam;
    
- multi-tenant inicial está operacional;
    
- permissões-base existem.
    

## Release 1 pronto quando:

- tenant gerencia usuários, perfis, organização, território e áreas de risco.
    

## Release 2 pronto quando:

- tenant cria, versiona e publica plano de contingência vigente.
    

## Release 3 pronto quando:

- tenant abre evento, ativa comando e registra operação mínima.
    

## Release 4 pronto quando:

- tenant gera relatório por evento e possui trilha auditável estável.
    

## MVP pronto quando:

- Releases 0 a 4 estão concluídos e estabilizados.
    

---

# BLOCO M — RECOMENDAÇÃO ESTRATÉGICA FINAL

## Melhor ponto de partida para codificação real

A implementação deve começar imediatamente por:

1. **estrutura do projeto Laravel**;
    
2. **migrations do schema inicial**;
    
3. **autenticação + multi-tenant + permissões**;
    
4. **módulo de usuários e organização**.
    

Essa é a rota mais segura. Começar diretamente pelo plano ou pelo evento sem a base de tenant, usuário e permissão tende a gerar retrabalho estrutural.

---

## 5. Próximo artefato tecnicamente mais útil

Com este backlog definido, o próximo passo mais útil é um destes dois:

1. **Esqueleto real do backend em Laravel**;
    
2. **Plano de sprints em formato de quadro operacional de execução**.
    

Para sair do planejamento e entrar na construção, o melhor próximo passo é o **esqueleto real do backend em Laravel**, porque agora a ordem de execução já está suficientemente fechada.

---

## 6. Conclusão técnica

Este backlog priorizado transforma toda a documentação anterior em plano de execução técnica real.

Agora o projeto já possui:

- visão do produto;
    
- PRD;
    
- arquitetura;
    
- modelagem de dados;
    
- dicionário;
    
- schema inicial;
    
- regras de negócio;
    
- casos de uso;
    
- especificação funcional do MVP;
    
- backlog priorizado por release e sprint.
    

A partir daqui, a documentação já não é mais o gargalo. O próximo gargalo passa a ser execução técnica organizada.