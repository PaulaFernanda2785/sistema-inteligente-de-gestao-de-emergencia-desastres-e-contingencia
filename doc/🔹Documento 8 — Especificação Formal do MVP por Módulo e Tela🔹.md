
---

## 1. Finalidade do documento

Este documento define a especificação funcional formal do MVP do sistema, detalhando os módulos, telas, objetivos, componentes, ações, regras funcionais, permissões e saídas esperadas.

Seu objetivo é transformar a arquitetura, a modelagem de dados, a matriz de regras e os casos de uso em instruções claras para:

- desenvolvimento frontend;
    
- desenvolvimento backend;
    
- definição de rotas e controllers;
    
- construção de serviços;
    
- planejamento de testes;
    
- priorização de backlog.
    

---

## 2. Escopo do MVP

O MVP do produto deve entregar um núcleo operacional e documental utilizável por Defesas Civis municipais e estaduais, cobrindo:

1. administração básica do tenant;
    
2. gestão de usuários e perfis;
    
3. base territorial mínima;
    
4. áreas de risco;
    
5. cadastro de abrigos e pontos estratégicos;
    
6. criação e versionamento do plano de contingência;
    
7. protocolo de ativação e matriz de responsabilidades;
    
8. abertura e gestão de evento/desastre;
    
9. comando operacional básico;
    
10. objetivos operacionais;
    
11. ocorrências e missões;
    
12. danos e necessidades;
    
13. relatório por evento;
    
14. trilha de auditoria.
    

---

## 3. Diretrizes do MVP

### 3.1 Diretrizes funcionais

- o MVP deve ser simples, mas não superficial;
    
- o foco é operação real e geração de valor institucional;
    
- o sistema deve funcionar com baixo nível de maturidade digital do usuário;
    
- o MVP não deve depender de integrações externas para operar.
    

### 3.2 Diretrizes de interface

- interface clara e objetiva;
    
- formulários com agrupamento lógico;
    
- navegação lateral por módulos;
    
- breadcrumbs internos;
    
- filtros persistentes nas listagens;
    
- mensagens de sucesso, erro e aviso padronizadas;
    
- visual responsivo para notebook e tablet.
    

### 3.3 Diretrizes de segurança

- controle por perfil e permissão;
    
- isolamento por tenant;
    
- ações críticas auditáveis;
    
- bloqueio de acesso a dados externos ao escopo do usuário.
    

---

## 4. Perfis previstos no MVP

- Superadministrador da Plataforma
    
- Administrador do Tenant
    
- Coordenador de Defesa Civil
    
- Analista Técnico
    
- Operador de Campo
    
- Auditor
    
- Visualizador
    

---

# BLOCO A — MÓDULO 1: AUTENTICAÇÃO E ACESSO

## Tela 1.1 — Login

### Objetivo

Permitir autenticação segura do usuário.

### Tipo

Tela pública.

### Componentes principais

- campo e-mail;
    
- campo senha;
    
- botão entrar;
    
- alerta de credenciais inválidas;
    
- link de recuperação de acesso, se habilitado;
    
- mensagem de ambiente institucional.
    

### Ações disponíveis

- autenticar usuário;
    
- validar credenciais;
    
- criar sessão;
    
- redirecionar conforme perfil.
    

### Regras funcionais

- apenas usuários ativos podem autenticar;
    
- credenciais inválidas devem retornar mensagem controlada;
    
- o sistema deve aplicar a política de sessão do tenant;
    
- autenticação deve respeitar política de MFA, quando habilitada.
    

### Perfis com acesso

- todos os usuários autenticáveis.
    

### Saídas esperadas

- sessão ativa;
    
- redirecionamento ao dashboard correspondente.
    

---

## Tela 1.2 — Encerramento de sessão

### Objetivo

Finalizar sessão autenticada.

### Tipo

Ação global do sistema.

### Componentes principais

- botão sair;
    
- confirmação opcional.
    

### Ações disponíveis

- invalidar sessão;
    
- redirecionar ao login.
    

### Regras funcionais

- sessão deve ser invalidada imediatamente;
    
- sistema deve limpar contexto autenticado.
    

### Perfis com acesso

- todos os usuários autenticados.
    

---

# BLOCO B — MÓDULO 2: ADMINISTRAÇÃO DO TENANT

## Tela 2.1 — Dashboard administrativo do tenant

### Objetivo

Oferecer visão inicial de administração local.

### Tipo

Dashboard interno.

### Componentes principais

- card de total de usuários;
    
- card de perfis ativos;
    
- card de organizações/unidades;
    
- card de contatos estratégicos;
    
- resumo do plano contratado;
    
- alertas administrativos;
    
- atalhos rápidos.
    

### Ações disponíveis

- acessar usuários;
    
- acessar perfis;
    
- acessar organização;
    
- acessar configurações;
    
- acessar auditoria.
    

### Regras funcionais

- dados exibidos somente do tenant atual;
    
- cards devem respeitar permissão do usuário.
    

### Perfis com acesso

- Superadministrador;
    
- Administrador do Tenant.
    

---

## Tela 2.2 — Lista de usuários

### Objetivo

Gerenciar usuários do tenant.

### Tipo

Listagem com filtros.

### Componentes principais

- tabela de usuários;
    
- filtro por nome;
    
- filtro por status;
    
- filtro por perfil;
    
- filtro por unidade;
    
- botão novo usuário;
    
- ação editar;
    
- ação inativar;
    
- ação redefinir acesso, se habilitada.
    

### Colunas mínimas

- nome;
    
- e-mail;
    
- unidade;
    
- perfil principal;
    
- status;
    
- último acesso;
    
- ações.
    

### Regras funcionais

- paginação obrigatória;
    
- filtros persistentes na navegação;
    
- e-mail único por tenant;
    
- inativação não apaga histórico.
    

### Perfis com acesso

- Superadministrador;
    
- Administrador do Tenant.
    

---

## Tela 2.3 — Formulário de usuário

### Objetivo

Criar ou editar usuário.

### Tipo

Formulário.

### Campos mínimos

- nome;
    
- e-mail;
    
- telefone;
    
- organização;
    
- unidade;
    
- cargo/função;
    
- status;
    
- senha inicial, quando criação;
    
- perfis associados.
    

### Ações disponíveis

- salvar;
    
- salvar e continuar;
    
- cancelar.
    

### Regras funcionais

- campos obrigatórios validados no backend;
    
- senha só obrigatória na criação;
    
- perfil deve existir no escopo permitido;
    
- usuário não pode ser criado sem organização vinculada.
    

### Perfis com acesso

- Superadministrador;
    
- Administrador do Tenant.
    

---

## Tela 2.4 — Perfis e permissões

### Objetivo

Visualizar e administrar perfis do tenant.

### Tipo

Listagem + formulário.

### Componentes principais

- lista de perfis;
    
- tabela de permissões por módulo;
    
- seleção múltipla de permissões;
    
- indicador de perfil do sistema ou customizado.
    

### Regras funcionais

- perfis globais do sistema podem ser vinculados, mas não alterados livremente;
    
- perfis customizados do tenant podem ser editados;
    
- mudanças devem ser auditadas.
    

### Perfis com acesso

- Superadministrador;
    
- Administrador do Tenant.
    

---

## Tela 2.5 — Organização e unidades

### Objetivo

Cadastrar a estrutura institucional do órgão.

### Tipo

Formulário + árvore hierárquica.

### Componentes principais

- dados da organização principal;
    
- lista de unidades internas;
    
- botão nova unidade;
    
- visualização hierárquica.
    

### Regras funcionais

- unidades devem manter coerência pai-filho;
    
- inativação de unidade não remove vínculos históricos.
    

### Perfis com acesso

- Administrador do Tenant.
    

---

## Tela 2.6 — Órgãos parceiros e contatos estratégicos

### Objetivo

Manter catálogo de apoio institucional e contatos acionáveis.

### Tipo

Listagem + formulário.

### Campos mínimos

- nome do órgão parceiro;
    
- tipo;
    
- responsável;
    
- telefone;
    
- e-mail;
    
- contato estratégico;
    
- prioridade.
    

### Regras funcionais

- telefone principal obrigatório para contato estratégico;
    
- contatos inativos não aparecem em acionamento rápido.
    

### Perfis com acesso

- Administrador do Tenant;
    
- Coordenador;
    
- Analista Técnico.
    

---

# BLOCO C — MÓDULO 3: BASE TERRITORIAL E RISCO

## Tela 3.1 — Lista de territórios

### Objetivo

Gerenciar territórios do tenant.

### Tipo

Listagem.

### Componentes principais

- tabela de territórios;
    
- botão novo território;
    
- ação editar;
    
- ação visualizar unidades territoriais.
    

### Regras funcionais

- normalmente haverá um território principal por ente, mas o sistema pode suportar mais de um cadastro conforme configuração;
    
- filtros por nome e tipo.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.2 — Unidades territoriais

### Objetivo

Gerenciar subdivisões do território.

### Tipo

Árvore/listagem híbrida.

### Componentes principais

- filtro por território;
    
- tabela/lista hierárquica;
    
- botão nova unidade;
    
- ação editar;
    
- ação visualizar detalhes.
    

### Campos mínimos

- nome;
    
- tipo;
    
- unidade pai;
    
- código;
    
- população estimada.
    

### Regras funcionais

- unidade pai deve pertencer ao mesmo território;
    
- o sistema deve evitar ciclos hierárquicos.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.3 — Lista de áreas de risco

### Objetivo

Cadastrar e acompanhar áreas de risco.

### Tipo

Listagem com filtros.

### Componentes principais

- tabela de áreas;
    
- filtro por unidade territorial;
    
- filtro por tipo de risco;
    
- filtro por prioridade;
    
- botão nova área de risco;
    
- ação editar;
    
- ação mapa.
    

### Colunas mínimas

- nome;
    
- unidade territorial;
    
- tipo de risco;
    
- prioridade;
    
- população exposta;
    
- status;
    
- ações.
    

### Regras funcionais

- área deve pertencer ao mesmo tenant;
    
- apenas áreas ativas entram no fluxo padrão do plano.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.4 — Formulário de área de risco

### Objetivo

Criar ou editar área de risco.

### Campos mínimos

- unidade territorial;
    
- nome;
    
- tipo de risco;
    
- prioridade;
    
- população exposta;
    
- descrição;
    
- observações de monitoramento;
    
- status.
    

### Regras funcionais

- prioridade e tipo de risco devem usar domínio controlado;
    
- valores numéricos não podem ser negativos.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.5 — Mapa e geometria da área de risco

### Objetivo

Registrar e visualizar geometrias das áreas.

### Tipo

Mapa interativo.

### Componentes principais

- mapa base;
    
- ferramenta de desenho;
    
- importação de arquivo geográfico;
    
- botão salvar geometria;
    
- botão substituir geometria;
    
- painel de atributos da área.
    

### Regras funcionais

- sistema deve aceitar geometria válida;
    
- uma área pode ter geometria principal ativa;
    
- alterações devem preservar rastreabilidade técnica.
    

### Perfis com acesso

- Analista Técnico.
    

---

## Tela 3.6 — Cenários de risco

### Objetivo

Gerenciar cenários de desastre do território.

### Campos mínimos

- nome do cenário;
    
- tipologia de desastre;
    
- unidade territorial, quando aplicável;
    
- gatilhos;
    
- impactos prováveis;
    
- diretrizes de resposta.
    

### Regras funcionais

- cenário deve referenciar tipologia válida;
    
- cenários ativos ficam disponíveis no plano e na abertura de evento.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.7 — Abrigos

### Objetivo

Cadastrar abrigos potenciais.

### Tipo

Listagem + formulário.

### Campos mínimos

- nome;
    
- tipo;
    
- unidade territorial;
    
- endereço;
    
- responsável;
    
- telefone;
    
- capacidade máxima;
    
- acessibilidade;
    
- água;
    
- energia;
    
- cozinha;
    
- status.
    

### Regras funcionais

- capacidade máxima obrigatória e não negativa;
    
- abrigo inativo não pode ser novo abrigo operacional sem reativação.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 3.8 — Pontos de apoio e bases operacionais

### Objetivo

Manter cadastro dos pontos estratégicos de apoio.

### Tipo

Listagem + formulário.

### Campos mínimos

- nome;
    
- tipo;
    
- unidade territorial;
    
- endereço;
    
- contato;
    
- status.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

# BLOCO D — MÓDULO 4: PLANO DE CONTINGÊNCIA

## Tela 4.1 — Lista de planos

### Objetivo

Gerenciar planos de contingência do tenant.

### Tipo

Listagem.

### Componentes principais

- tabela de planos;
    
- filtro por território;
    
- filtro por status;
    
- botão novo plano;
    
- ação visualizar;
    
- ação editar;
    
- ação versões;
    
- ação PDF.
    

### Colunas mínimas

- título;
    
- escopo;
    
- território;
    
- status;
    
- versão vigente;
    
- vigência;
    
- ações.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Visualizador.
    

---

## Tela 4.2 — Formulário principal do plano

### Objetivo

Criar ou atualizar metadados do plano.

### Campos mínimos

- título;
    
- escopo;
    
- território;
    
- status;
    
- vigência inicial;
    
- vigência final;
    
- frequência de revisão.
    

### Regras funcionais

- território obrigatório;
    
- data final não pode anteceder data inicial.
    

### Perfis com acesso

- Coordenador.
    

---

## Tela 4.3 — Lista de versões do plano

### Objetivo

Gerenciar ciclo de vida das versões.

### Componentes principais

- tabela de versões;
    
- botão nova versão;
    
- botão clonar versão;
    
- ação editar;
    
- ação submeter para revisão;
    
- ação aprovar;
    
- ação publicar;
    
- indicador de versão vigente.
    

### Regras funcionais

- somente uma versão vigente por plano;
    
- apenas versões editáveis podem ser modificadas.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 4.4 — Editor de seções do plano

### Objetivo

Editar conteúdo detalhado da versão.

### Tipo

Editor estruturado por abas ou menu lateral.

### Seções mínimas recomendadas no MVP

- identificação do plano;
    
- base territorial;
    
- cenários de risco;
    
- estrutura institucional;
    
- responsabilidades;
    
- acionamento;
    
- recursos e pontos estratégicos;
    
- abrigos;
    
- procedimentos iniciais;
    
- contatos estratégicos.
    

### Componentes principais

- menu de seções;
    
- editor de conteúdo;
    
- formulário de itens estruturados;
    
- botão salvar;
    
- indicador de completude.
    

### Regras funcionais

- cada seção deve poder ser salva independentemente;
    
- sistema deve registrar progresso de preenchimento;
    
- versão publicada não pode ser editada diretamente.
    

### Perfis com acesso

- Analista Técnico;
    
- Coordenador.
    

---

## Tela 4.5 — Matriz de responsabilidades

### Objetivo

Definir responsáveis principais e de apoio das ações do plano.

### Tipo

Tabela editável.

### Colunas mínimas

- ação;
    
- responsável principal;
    
- tipo do responsável;
    
- apoio;
    
- prioridade;
    
- observações.
    

### Regras funcionais

- ação não pode ficar sem responsável principal;
    
- responsáveis devem existir no escopo do tenant ou estar claramente identificados como externos.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 4.6 — Protocolos de ativação

### Objetivo

Definir gatilhos e fluxo de acionamento.

### Campos mínimos

- nome do protocolo;
    
- gatilho;
    
- passos de ativação;
    
- fluxo de comunicação;
    
- perfis/órgãos requeridos.
    

### Regras funcionais

- protocolo sem gatilho ou passos não pode ser salvo como completo;
    
- versão vigente precisa ter ao menos um protocolo válido.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 4.7 — Anexos do plano

### Objetivo

Vincular documentos complementares.

### Componentes principais

- upload de arquivo;
    
- lista de anexos;
    
- categoria do anexo;
    
- ação remover, quando permitido;
    
- ação baixar.
    

### Regras funcionais

- tipos de arquivo controlados;
    
- anexos de versão publicada não devem ser apagados sem controle específico.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 4.8 — Publicação do plano

### Objetivo

Conduzir fluxo de revisão, aprovação e publicação.

### Componentes principais

- checklist de completude;
    
- status da versão;
    
- botão submeter;
    
- botão aprovar;
    
- botão publicar;
    
- histórico de publicações.
    

### Regras funcionais

- publicação exige completude mínima;
    
- somente perfil autorizado pode aprovar/publicar;
    
- publicação deve atualizar a versão vigente do plano.
    

### Perfis com acesso

- Coordenador.
    

---

# BLOCO E — MÓDULO 5: EVENTOS E ATIVAÇÃO

## Tela 5.1 — Lista de eventos

### Objetivo

Visualizar e gerenciar eventos/desastres do tenant.

### Tipo

Listagem com filtros.

### Componentes principais

- filtro por status;
    
- filtro por tipologia;
    
- filtro por severidade;
    
- filtro por período;
    
- botão novo evento;
    
- ação visualizar;
    
- ação editar;
    
- ação encerrar.
    

### Colunas mínimas

- código;
    
- título;
    
- tipologia;
    
- severidade;
    
- fase operacional;
    
- status;
    
- início;
    
- fim;
    
- ações.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Visualizador.
    

---

## Tela 5.2 — Formulário de abertura de evento

### Objetivo

Registrar a abertura formal do evento.

### Campos mínimos

- código do evento;
    
- título;
    
- território;
    
- unidade territorial;
    
- tipologia;
    
- severidade;
    
- cenário associado, opcional;
    
- versão do plano associada, opcional;
    
- data/hora de início;
    
- resumo inicial.
    

### Regras funcionais

- código único por tenant;
    
- data/hora de início obrigatória;
    
- tipologia e severidade obrigatórias.
    

### Perfis com acesso

- Coordenador.
    

---

## Tela 5.3 — Detalhe do evento

### Objetivo

Concentrar visão geral do evento.

### Componentes principais

- cabeçalho com status e severidade;
    
- resumo do evento;
    
- cards de objetivos, ocorrências, missões, danos, necessidades;
    
- timeline;
    
- atalhos para módulos operacionais;
    
- botão gerar relatório;
    
- botão encerrar evento.
    

### Regras funcionais

- dados apresentados conforme permissões;
    
- apenas eventos ativos exibem ações operacionais de edição corrente.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo;
    
- Visualizador.
    

---

## Tela 5.4 — Timeline do evento

### Objetivo

Registrar marcos temporais relevantes.

### Campos mínimos

- tipo do marco;
    
- título;
    
- descrição;
    
- data/hora.
    

### Regras funcionais

- marco temporal deve se vincular ao evento;
    
- sistema também pode inserir marcos automáticos.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 5.5 — Alteração de status do evento

### Objetivo

Modificar o status do evento com histórico.

### Campos mínimos

- novo status;
    
- justificativa, quando exigida.
    

### Regras funcionais

- transições inválidas devem ser bloqueadas;
    
- sistema deve registrar histórico automaticamente.
    

### Perfis com acesso

- Coordenador.
    

---

## Tela 5.6 — Encerramento do evento

### Objetivo

Formalizar o fechamento institucional do evento.

### Campos mínimos

- motivo do encerramento;
    
- síntese final;
    
- data/hora de encerramento.
    

### Regras funcionais

- evento precisa estar em condição de encerramento;
    
- fechamento deve gerar registro próprio e atualizar status.
    

### Perfis com acesso

- Coordenador.
    

---

# BLOCO F — MÓDULO 6: COMANDO E COORDENAÇÃO OPERACIONAL

## Tela 6.1 — Painel de comando

### Objetivo

Visualizar a estrutura de comando ativa do evento.

### Componentes principais

- cabeçalho do evento;
    
- resumo da estrutura ativa;
    
- lista de posições funcionais;
    
- status da estrutura;
    
- atalhos para objetivos, decisões e formulários.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Visualizador autorizado.
    

---

## Tela 6.2 — Ativação da estrutura de comando

### Objetivo

Criar a estrutura operacional do evento.

### Campos mínimos

- nome da estrutura;
    
- modelo adotado;
    
- data/hora de ativação;
    
- status.
    

### Regras funcionais

- só pode haver estrutura principal ativa compatível por evento;
    
- evento precisa estar ativo.
    

### Perfis com acesso

- Coordenador.
    

---

## Tela 6.3 — Posições funcionais e designações

### Objetivo

Gerenciar funções de comando e seus ocupantes.

### Componentes principais

- lista de posições;
    
- tipo da posição;
    
- ocupante atual;
    
- status;
    
- botão designar;
    
- botão encerrar designação.
    

### Regras funcionais

- posição deve pertencer à estrutura ativa;
    
- designação exige usuário interno ou pessoa externa identificada.
    

### Perfis com acesso

- Coordenador.
    

---

## Tela 6.4 — Objetivos operacionais

### Objetivo

Cadastrar e acompanhar objetivos da operação.

### Tipo

Listagem + formulário.

### Colunas mínimas

- código;
    
- título;
    
- prioridade;
    
- status;
    
- responsável autor;
    
- prazo previsto.
    

### Regras funcionais

- código único por evento;
    
- status controlado;
    
- objetivos encerrados não devem ser alterados sem regra específica.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 6.5 — Decisões operacionais

### Objetivo

Registrar decisões relevantes do evento.

### Campos mínimos

- título;
    
- descrição;
    
- status de implementação;
    
- data/hora;
    
- autor.
    

### Regras funcionais

- decisão deve manter autoria e data;
    
- edição deve ser controlada e auditada.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

# BLOCO G — MÓDULO 7: OPERAÇÕES E RESPOSTA

## Tela 7.1 — Quadro operacional

### Objetivo

Concentrar visão operacional do evento.

### Componentes principais

- total de ocorrências abertas;
    
- total de missões por status;
    
- equipes disponíveis;
    
- recursos alocados;
    
- atalhos operacionais.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo.
    

---

## Tela 7.2 — Lista de ocorrências

### Objetivo

Gerenciar ocorrências operacionais do evento.

### Tipo

Listagem com filtros.

### Colunas mínimas

- código;
    
- tipo;
    
- título;
    
- localização;
    
- severidade;
    
- status;
    
- abertura;
    
- ações.
    

### Regras funcionais

- somente eventos ativos podem receber novas ocorrências;
    
- filtro por status e território obrigatório.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo.
    

---

## Tela 7.3 — Formulário de ocorrência

### Objetivo

Criar ou atualizar ocorrência.

### Campos mínimos

- código;
    
- tipo;
    
- título;
    
- descrição;
    
- unidade territorial;
    
- endereço;
    
- latitude/longitude, quando houver;
    
- severidade local;
    
- status;
    
- data/hora de abertura.
    

### Regras funcionais

- código único no evento;
    
- campos mínimos obrigatórios;
    
- encerramento não pode anteceder abertura.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo.
    

---

## Tela 7.4 — Lista de missões

### Objetivo

Gerenciar missões do evento.

### Tipo

Listagem com filtros.

### Colunas mínimas

- código;
    
- título;
    
- prioridade;
    
- status;
    
- ocorrência vinculada;
    
- objetivo vinculado;
    
- início;
    
- conclusão;
    
- ações.
    

### Regras funcionais

- missão obrigatoriamente vinculada ao evento;
    
- filtro por status obrigatório;
    
- códigos únicos por evento.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 7.5 — Formulário de missão

### Objetivo

Criar ou atualizar missão operacional.

### Campos mínimos

- código;
    
- título;
    
- descrição;
    
- prioridade;
    
- ocorrência relacionada;
    
- objetivo operacional relacionado;
    
- status;
    
- horários relevantes.
    

### Regras funcionais

- missão sem descrição não pode ser salva;
    
- status deve obedecer fluxo permitido.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 7.6 — Designação de missão

### Objetivo

Alocar equipe ou recurso à missão.

### Componentes principais

- seleção de equipe;
    
- seleção de recurso;
    
- observações;
    
- botão designar.
    

### Regras funcionais

- deve haver ao menos uma equipe ou um recurso;
    
- meios operacionais devem estar disponíveis.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 7.7 — Equipes operacionais

### Objetivo

Gerenciar equipes mobilizáveis.

### Tipo

Listagem + formulário.

### Campos mínimos

- nome;
    
- tipo;
    
- líder;
    
- telefone;
    
- disponibilidade.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 7.8 — Recursos operacionais

### Objetivo

Gerenciar recursos materiais e logísticos.

### Tipo

Listagem + formulário.

### Campos mínimos

- nome;
    
- tipo;
    
- identificador;
    
- propriedade;
    
- status;
    
- localização.
    

### Regras funcionais

- recurso alocado deve refletir indisponibilidade ou status compatível;
    
- recurso precisa existir antes da alocação formal.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico.
    

---

## Tela 7.9 — Avaliação de danos

### Objetivo

Registrar danos humanos, materiais e ambientais.

### Tipo

Listagem + formulário.

### Campos mínimos

- tipo de avaliação;
    
- unidade territorial;
    
- pessoas afetadas;
    
- desabrigados;
    
- desalojados;
    
- feridos;
    
- óbitos;
    
- danos públicos;
    
- danos privados;
    
- danos de infraestrutura;
    
- danos ambientais;
    
- observações;
    
- data/hora;
    
- avaliador.
    

### Regras funcionais

- valores numéricos não podem ser negativos;
    
- avaliação deve permanecer historizada.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo, com restrição conforme perfil.
    

---

## Tela 7.10 — Avaliação de necessidades

### Objetivo

Registrar necessidades do evento.

### Campos mínimos

- categoria;
    
- quantidade;
    
- unidade;
    
- prioridade;
    
- status;
    
- observações;
    
- data/hora;
    
- avaliador.
    

### Regras funcionais

- categoria e prioridade obrigatórias;
    
- status controlado.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo, conforme permissão.
    

---

## Tela 7.11 — Diário operacional

### Objetivo

Registrar fatos relevantes do evento.

### Tipo

Listagem cronológica + formulário.

### Campos mínimos

- tipo do log;
    
- título;
    
- descrição;
    
- data/hora;
    
- autor;
    
- entidade relacionada, se houver.
    

### Regras funcionais

- entradas não devem ser removidas por rotina comum;
    
- logs podem ser gerados manualmente ou pelo sistema.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo.
    

---

# BLOCO H — MÓDULO 8: RELATÓRIOS E AUDITORIA

## Tela 8.1 — Relatório por evento

### Objetivo

Gerar relatório institucional do evento.

### Componentes principais

- seleção do template;
    
- pré-visualização resumida dos dados;
    
- botão gerar relatório;
    
- histórico de relatórios do evento.
    

### Regras funcionais

- sistema deve validar completude mínima antes da emissão;
    
- geração deve criar registro em `generated_reports`.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Auditor, para consulta.
    

---

## Tela 8.2 — Lista de relatórios gerados

### Objetivo

Consultar documentos já emitidos.

### Colunas mínimas

- título;
    
- template;
    
- entidade relacionada;
    
- usuário gerador;
    
- data de geração;
    
- status;
    
- ação baixar.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Auditor.
    

---

## Tela 8.3 — Auditoria do sistema

### Objetivo

Consultar trilha de auditoria.

### Tipo

Listagem com filtros avançados.

### Filtros mínimos

- período;
    
- módulo;
    
- ação;
    
- usuário;
    
- entidade.
    

### Colunas mínimas

- data/hora;
    
- usuário;
    
- módulo;
    
- ação;
    
- entidade;
    
- id da entidade;
    
- IP, quando disponível.
    

### Regras funcionais

- acesso restrito;
    
- dados devem respeitar escopo do tenant;
    
- logs não podem ser editados.
    

### Perfis com acesso

- Auditor;
    
- Administrador do Tenant, conforme política;
    
- Superadministrador.
    

---

# BLOCO I — DASHBOARDS MÍNIMOS DO MVP

## Tela 9.1 — Dashboard operacional inicial

### Objetivo

Servir como página inicial funcional para usuários operacionais.

### Componentes mínimos

- eventos ativos;
    
- planos vigentes;
    
- áreas de risco ativas;
    
- ocorrências abertas;
    
- missões em andamento;
    
- necessidades prioritárias;
    
- atalhos rápidos para abrir evento, ocorrência e missão.
    

### Perfis com acesso

- Coordenador;
    
- Analista Técnico;
    
- Operador de Campo.
    

---

## Tela 9.2 — Dashboard executivo simplificado

### Objetivo

Servir como visão gerencial resumida.

### Componentes mínimos

- total de eventos no período;
    
- eventos por tipologia;
    
- eventos por severidade;
    
- planos vigentes;
    
- usuários ativos;
    
- relatórios emitidos.
    

### Perfis com acesso

- Coordenador;
    
- Administrador do Tenant;
    
- Gestor Estadual/Regional, em fase posterior.
    

---

# BLOCO J — ROTAS FUNCIONAIS SUGERIDAS DO MVP

## 10. Rotas mínimas

```text
/auth/login
/auth/logout
/admin
/admin/users
/admin/users/create
/admin/users/{id}/edit
/admin/roles
/admin/organization
/admin/contacts
/territory
/territory/units
/risk-areas
/risk-areas/create
/risk-areas/{id}/edit
/risk-areas/{id}/map
/risk-scenarios
/shelters
/support-points
/operational-bases
/contingency-plans
/contingency-plans/create
/contingency-plans/{id}
/contingency-plans/{id}/versions
/contingency-plan-versions/{id}/editor
/contingency-plan-versions/{id}/responsibilities
/contingency-plan-versions/{id}/activation-protocols
/contingency-plan-versions/{id}/attachments
/disaster-events
/disaster-events/create
/disaster-events/{id}
/disaster-events/{id}/timeline
/disaster-events/{id}/status
/disaster-events/{id}/closure
/command/{eventId}
/command/{eventId}/positions
/command/{eventId}/objectives
/command/{eventId}/decisions
/operations/{eventId}
/operations/{eventId}/occurrences
/operations/{eventId}/missions
/operations/{eventId}/teams
/operations/{eventId}/resources
/operations/{eventId}/damages
/operations/{eventId}/needs
/operations/{eventId}/logs
/reports/events/{eventId}
/reports/generated
/audit
/dashboard
```

---

# BLOCO K — PRIORIDADE DE IMPLEMENTAÇÃO POR TELAS

## 11. Prioridade 1 — MVP indispensável

- Login
    
- Dashboard operacional inicial
    
- Lista de usuários
    
- Formulário de usuário
    
- Organização e unidades
    
- Territórios
    
- Unidades territoriais
    
- Áreas de risco
    
- Formulário de área de risco
    
- Abrigos
    
- Lista de planos
    
- Formulário principal do plano
    
- Lista de versões
    
- Editor de seções
    
- Matriz de responsabilidades
    
- Protocolos de ativação
    
- Lista de eventos
    
- Formulário de abertura de evento
    
- Detalhe do evento
    
- Alteração de status do evento
    
- Painel de comando
    
- Objetivos operacionais
    
- Lista de ocorrências
    
- Formulário de ocorrência
    
- Lista de missões
    
- Formulário de missão
    
- Avaliação de danos
    
- Avaliação de necessidades
    
- Relatório por evento
    
- Auditoria
    

## 12. Prioridade 2 — Ampliação imediata

- Mapa e geometria da área de risco
    
- Cenários de risco
    
- Pontos de apoio
    
- Bases operacionais
    
- Timeline do evento
    
- Posições funcionais e designações
    
- Decisões operacionais
    
- Equipes operacionais
    
- Recursos operacionais
    
- Diário operacional
    
- Lista de relatórios gerados
    
- Dashboard executivo simplificado
    

## 13. Prioridade 3 — Pós-MVP

- Simulados
    
- Treinamentos
    
- Checklists de prontidão
    
- Abrigos ativos
    
- Famílias assistidas
    
- Estoque humanitário
    
- Distribuição humanitária
    
- Recuperação
    
- Lições aprendidas
    
- Histórico consolidado
    

---

# BLOCO L — REGRAS DE ACEITE DO MVP

## 14. Critérios gerais de aceite

O MVP será considerado funcionalmente aceitável quando:

1. permitir autenticação e segregação correta por tenant;
    
2. permitir cadastro e gestão básica de usuários e perfis;
    
3. permitir cadastrar base territorial mínima;
    
4. permitir cadastrar áreas de risco e abrigos;
    
5. permitir criar plano, versão, matriz e protocolo de ativação;
    
6. permitir publicar uma versão vigente do plano;
    
7. permitir abrir evento com status inicial;
    
8. permitir ativar estrutura de comando;
    
9. permitir registrar objetivos, ocorrências e missões;
    
10. permitir registrar danos e necessidades;
    
11. permitir emitir relatório por evento;
    
12. permitir rastrear ações críticas em auditoria.
    

---

# BLOCO M — ENTREGÁVEIS TÉCNICOS DERIVADOS DESTE DOCUMENTO

## 15. Derivações imediatas

A partir desta especificação, podem ser produzidos de forma consistente:

1. backlog funcional por tela;
    
2. backlog técnico por controller/service/repository;
    
3. wireframes de baixa fidelidade;
    
4. matriz de testes por tela;
    
5. esqueleto real do backend;
    
6. planejamento de sprints.
    

---

## 16. Conclusão técnica

Esta especificação formal do MVP transforma o projeto em um pacote funcional implementável, porque define não apenas o que o sistema faz, mas em que tela isso acontece, com quais componentes, sob quais regras e para quais perfis.

Com isso, o projeto já está maduro para sair do plano conceitual e entrar na fase de construção técnica organizada.

O próximo passo mais correto é produzir um destes dois artefatos:

1. **Backlog técnico priorizado por sprint/release**;
    
2. **Esqueleto real do backend em Laravel, com módulos, rotas, controllers e services-base**.
    

Para entrar efetivamente em desenvolvimento, o mais útil agora é o **backlog técnico priorizado por sprint/release**, porque ele organiza a execução sem inflar o escopo.