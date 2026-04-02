
## Documento 7 — Casos de Uso do Sistema

---

## 1. Finalidade do documento

Este documento descreve os casos de uso do sistema em formato técnico-textual, organizando os principais fluxos de interação entre os atores e as funcionalidades do SaaS.

Seu objetivo é:

- formalizar o comportamento esperado do sistema;
    
- reduzir ambiguidades entre regra de negócio e implementação;
    
- apoiar modelagem UML, backend, frontend e testes;
    
- estabelecer a visão operacional do MVP e das fases seguintes.
    

---

## 2. Escopo dos casos de uso

Este documento cobre:

- casos de uso centrais do MVP;
    
- casos de uso estruturantes da operação;
    
- casos de uso de apoio institucional;
    
- casos de uso de segurança, relatórios e auditoria.
    

Não cobre, em profundidade nesta fase:

- integrações externas automáticas avançadas;
    
- notificações multicanal complexas;
    
- analytics preditivo;
    
- automações de IA.
    

---

## 3. Atores do sistema

### 3.1 Superadministrador da Plataforma

Responsável pela administração global do SaaS, clientes, planos, recursos habilitados e supervisão da operação da plataforma.

### 3.2 Administrador do Tenant

Responsável pela administração do ambiente do cliente, usuários, perfis, parâmetros institucionais e governança local.

### 3.3 Coordenador de Defesa Civil

Responsável por planejamento, ativação de eventos, coordenação institucional e supervisão da operação.

### 3.4 Analista Técnico

Responsável pelo cadastro territorial, atualização de planos, registro técnico-operacional, consolidação de informações e relatórios.

### 3.5 Operador de Campo

Responsável por alimentar ocorrências, missões, danos, necessidades e ações operacionais.

### 3.6 Gestor Estadual / Regional

Responsável pela supervisão agregada, visão consolidada e coordenação em nível ampliado.

### 3.7 Auditor

Responsável pela consulta de trilhas, relatórios e integridade documental, sem atuação operacional direta.

### 3.8 Sistema

Atores automáticos internos da aplicação, usados em rotinas de persistência, auditoria, geração de relatórios e consistência operacional.

---

## 4. Relação resumida ator x domínio

|Ator|Domínios principais|
|---|---|
|Superadministrador|Plataforma, tenants, planos comerciais, recursos globais|
|Administrador do Tenant|usuários, perfis, configurações, segurança local|
|Coordenador|plano, evento, comando, relatório, encerramento|
|Analista Técnico|território, risco, plano, danos, necessidades, relatórios|
|Operador de Campo|ocorrências, missões, registros operacionais|
|Gestor Estadual/Regional|visão consolidada, relatórios, supervisão|
|Auditor|logs, relatórios, trilha de auditoria|
|Sistema|logs, PDFs, snapshots, status automáticos|

---

# BLOCO A — CASOS DE USO ESTRUTURANTES DO MVP

## UC-01 — Cadastrar tenant

### Objetivo

Criar um novo cliente na plataforma SaaS.

### Atores

- Primário: Superadministrador da Plataforma
    
- Secundário: Sistema
    

### Pré-condições

- usuário autenticado com permissão global de administração;
    
- dados mínimos do contratante disponíveis.
    

### Pós-condições

- tenant criado;
    
- parâmetros iniciais registrados;
    
- política de segurança inicial criada;
    
- vínculo comercial inicial registrado.
    

### Fluxo principal

1. O Superadministrador acessa a área de administração global.
    
2. Informa os dados do novo cliente.
    
3. Define tipo de tenant, plano e status inicial.
    
4. O sistema valida duplicidade e consistência mínima.
    
5. O sistema cria o tenant.
    
6. O sistema registra configurações iniciais.
    
7. O sistema confirma a criação.
    

### Fluxos alternativos

- 4A. Documento já existente: o sistema bloqueia a criação e informa conflito.
    
- 4B. Dados obrigatórios ausentes: o sistema rejeita a operação e indica campos pendentes.
    

---

## UC-02 — Cadastrar usuário no tenant

### Objetivo

Criar um novo usuário no ambiente do cliente.

### Atores

- Primário: Administrador do Tenant
    
- Secundário: Sistema
    

### Pré-condições

- tenant ativo;
    
- administrador autenticado;
    
- organização institucional cadastrada.
    

### Pós-condições

- usuário criado;
    
- perfil vinculado;
    
- log de auditoria registrado.
    

### Fluxo principal

1. O Administrador acessa a gestão de usuários.
    
2. Informa dados pessoais e institucionais do usuário.
    
3. Seleciona organização, unidade e perfil inicial.
    
4. O sistema valida unicidade do e-mail no tenant.
    
5. O sistema cria o usuário.
    
6. O sistema vincula o perfil.
    
7. O sistema registra a ação em auditoria.
    
8. O sistema confirma a operação.
    

### Fluxos alternativos

- 4A. E-mail já utilizado no tenant: operação negada.
    
- 4B. Perfil inválido ou fora do escopo: operação negada.
    

---

## UC-03 — Cadastrar território e base territorial mínima

### Objetivo

Estruturar o território institucional para planejamento e operação.

### Atores

- Primário: Analista Técnico
    
- Secundário: Coordenador de Defesa Civil
    

### Pré-condições

- tenant ativo;
    
- usuário autorizado.
    

### Pós-condições

- território cadastrado;
    
- subdivisões territoriais registradas;
    
- base inicial pronta para uso em planos e eventos.
    

### Fluxo principal

1. O Analista acessa o módulo territorial.
    
2. Cadastra o território principal.
    
3. Cadastra unidades territoriais derivadas.
    
4. O sistema valida consistência hierárquica.
    
5. O sistema persiste os dados.
    
6. O sistema confirma o cadastro.
    

### Fluxos alternativos

- 4A. Unidade territorial associada ao território errado: operação rejeitada.
    
- 4B. Dados obrigatórios ausentes: sistema solicita correção.
    

---

## UC-04 — Cadastrar área de risco

### Objetivo

Registrar área de risco associada ao território.

### Atores

- Primário: Analista Técnico
    
- Secundário: Sistema
    

### Pré-condições

- base territorial existente;
    
- unidade territorial válida.
    

### Pós-condições

- área de risco cadastrada;
    
- área disponível para cenários, plano e operação.
    

### Fluxo principal

1. O Analista acessa a área de risco.
    
2. Seleciona a unidade territorial.
    
3. Informa nome, tipo de risco, prioridade e descrição.
    
4. O sistema valida a consistência dos dados.
    
5. O sistema salva a área de risco.
    
6. O sistema confirma o cadastro.
    

### Fluxos alternativos

- 3A. Unidade territorial inexistente: operação bloqueada.
    
- 4A. Tipo de risco inválido: sistema rejeita o registro.
    

---

## UC-05 — Associar geometria à área de risco

### Objetivo

Registrar a delimitação espacial da área de risco.

### Atores

- Primário: Analista Técnico
    
- Secundário: Sistema
    

### Pré-condições

- área de risco cadastrada.
    

### Pós-condições

- geometria vinculada à área;
    
- dado disponível para mapa e cruzamentos geográficos.
    

### Fluxo principal

1. O Analista abre uma área de risco existente.
    
2. Importa ou desenha a geometria.
    
3. O sistema valida o formato e a integridade do dado espacial.
    
4. O sistema vincula a geometria à área.
    
5. O sistema confirma o sucesso.
    

### Fluxos alternativos

- 3A. Geometria inválida: sistema rejeita o envio.
    
- 3B. Arquivo incompatível: sistema informa erro de importação.
    

---

## UC-06 — Criar plano de contingência

### Objetivo

Criar a estrutura principal do Plano de Contingência.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- território cadastrado;
    
- usuário com permissão de gestão de planos.
    

### Pós-condições

- plano criado em estado inicial;
    
- plano disponível para versionamento e edição.
    

### Fluxo principal

1. O Coordenador acessa o módulo de plano.
    
2. Informa título, escopo e território de abrangência.
    
3. O sistema valida os dados obrigatórios.
    
4. O sistema cria o plano.
    
5. O sistema confirma a criação.
    

### Fluxos alternativos

- 3A. Território ausente ou inválido: sistema bloqueia a operação.
    

---

## UC-07 — Criar nova versão do plano

### Objetivo

Abrir uma nova versão editável do Plano de Contingência.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- plano existente;
    
- usuário autorizado.
    

### Pós-condições

- nova versão criada;
    
- versão disponível para edição.
    

### Fluxo principal

1. O usuário acessa o plano.
    
2. Solicita criação de nova versão.
    
3. O sistema gera a numeração da nova versão.
    
4. O sistema registra a nova versão em estado editável.
    
5. O sistema confirma a criação.
    

### Fluxos alternativos

- 3A. Numeração já existente por inconsistência: sistema aborta e exige correção.
    

---

## UC-08 — Editar seções do plano

### Objetivo

Preencher o conteúdo estruturado do Plano de Contingência.

### Atores

- Primário: Analista Técnico
    
- Secundário: Coordenador de Defesa Civil
    

### Pré-condições

- versão editável do plano existente.
    

### Pós-condições

- conteúdo da versão atualizado.
    

### Fluxo principal

1. O Analista abre uma versão editável.
    
2. Seleciona a seção a ser preenchida.
    
3. Insere ou altera conteúdo textual e itens estruturados.
    
4. O sistema valida integridade mínima.
    
5. O sistema salva as alterações.
    
6. O sistema confirma a atualização.
    

### Fluxos alternativos

- 4A. Versão não editável: sistema bloqueia alteração.
    
- 4B. Dados estruturados inválidos: sistema rejeita o salvamento.
    

---

## UC-09 — Registrar matriz de responsabilidades do plano

### Objetivo

Definir responsáveis principais e de apoio para ações previstas.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- versão editável existente.
    

### Pós-condições

- responsabilidades vinculadas ao plano.
    

### Fluxo principal

1. O usuário acessa a matriz de responsabilidades.
    
2. Define a ação prevista.
    
3. Define responsável principal e apoio.
    
4. O sistema valida o vínculo institucional.
    
5. O sistema salva a matriz.
    
6. O sistema confirma a operação.
    

### Fluxos alternativos

- 4A. Responsável fora do escopo do tenant: operação negada.
    
- 4B. Responsável principal ausente: sistema impede gravação.
    

---

## UC-10 — Registrar protocolo de ativação

### Objetivo

Definir os gatilhos e passos formais de acionamento do plano.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- versão editável do plano.
    

### Pós-condições

- protocolo cadastrado e utilizável.
    

### Fluxo principal

1. O usuário acessa os protocolos da versão.
    
2. Informa nome, gatilho, passos e fluxo de comunicação.
    
3. O sistema valida os dados obrigatórios.
    
4. O sistema salva o protocolo.
    
5. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Gatilho não informado: operação bloqueada.
    
- 3B. Passos de ativação vazios: operação bloqueada.
    

---

## UC-11 — Publicar versão vigente do plano

### Objetivo

Tornar uma versão oficialmente vigente para uso operacional.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Sistema
    

### Pré-condições

- versão existente;
    
- versão aprovada;
    
- completude mínima atendida.
    

### Pós-condições

- versão publicada;
    
- plano atualizado com referência à versão vigente;
    
- histórico de publicação registrado.
    

### Fluxo principal

1. O Coordenador seleciona a versão aprovada.
    
2. Solicita publicação.
    
3. O sistema valida se a versão cumpre os requisitos mínimos.
    
4. O sistema remove a vigência anterior, se houver.
    
5. O sistema marca a nova versão como vigente.
    
6. O sistema registra a publicação.
    
7. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Versão incompleta: sistema bloqueia publicação.
    
- 3B. Versão não aprovada: sistema impede a operação.
    

---

## UC-12 — Abrir evento/desastre

### Objetivo

Registrar formalmente o início de um evento operacional.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico, Sistema
    

### Pré-condições

- usuário autorizado;
    
- tipologia e severidade disponíveis.
    

### Pós-condições

- evento criado;
    
- status inicial registrado;
    
- linha do tempo iniciada;
    
- auditoria registrada.
    

### Fluxo principal

1. O Coordenador acessa o módulo de eventos.
    
2. Informa código, título, tipologia, severidade, horário de início e resumo.
    
3. O sistema valida unicidade do código no tenant.
    
4. O sistema cria o evento.
    
5. O sistema registra o primeiro status.
    
6. O sistema registra o primeiro marco na timeline.
    
7. O sistema grava a auditoria.
    
8. O sistema confirma a abertura do evento.
    

### Fluxos alternativos

- 3A. Código já utilizado no tenant: operação bloqueada.
    
- 3B. Tipologia ou severidade inválidas: sistema rejeita o cadastro.
    

---

## UC-13 — Alterar status do evento

### Objetivo

Modificar a situação operacional do evento com rastreabilidade.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Sistema
    

### Pré-condições

- evento existente;
    
- usuário autorizado.
    

### Pós-condições

- novo status persistido;
    
- histórico registrado.
    

### Fluxo principal

1. O Coordenador acessa o evento.
    
2. Seleciona novo status.
    
3. Informa justificativa, quando aplicável.
    
4. O sistema valida a transição.
    
5. O sistema atualiza o status do evento.
    
6. O sistema registra o histórico.
    
7. O sistema confirma a alteração.
    

### Fluxos alternativos

- 4A. Transição inválida: sistema bloqueia a alteração.
    
- 4B. Evento já encerrado sem permissão especial: operação negada.
    

---

## UC-14 — Ativar estrutura de comando

### Objetivo

Formalizar a estrutura de comando do evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Sistema
    

### Pré-condições

- evento ativo;
    
- usuário autorizado.
    

### Pós-condições

- estrutura de comando criada e vinculada ao evento.
    

### Fluxo principal

1. O Coordenador acessa o painel de comando do evento.
    
2. Informa nome da estrutura e modelo adotado.
    
3. O sistema valida o evento e a coerência do modelo.
    
4. O sistema cria a estrutura.
    
5. O sistema confirma a ativação.
    

### Fluxos alternativos

- 3A. Evento incompatível com ativação de comando: sistema bloqueia a operação.
    

---

## UC-15 — Designar responsáveis a posições funcionais

### Objetivo

Vincular pessoas às posições da estrutura de comando.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Sistema
    

### Pré-condições

- estrutura de comando ativa;
    
- posição funcional existente.
    

### Pós-condições

- designação registrada com horário e vínculo funcional.
    

### Fluxo principal

1. O Coordenador seleciona uma posição funcional.
    
2. Escolhe um usuário interno ou registra nome externo.
    
3. O sistema valida consistência da designação.
    
4. O sistema registra a atribuição.
    
5. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Nenhuma pessoa informada: operação negada.
    
- 3B. Pessoa incompatível com o escopo permitido: operação negada.
    

---

## UC-16 — Definir objetivo operacional

### Objetivo

Registrar objetivos formais da operação no evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento ativo.
    

### Pós-condições

- objetivo operacional criado.
    

### Fluxo principal

1. O usuário acessa os objetivos operacionais.
    
2. Informa código, título, descrição e prioridade.
    
3. O sistema valida unicidade do código no evento.
    
4. O sistema salva o objetivo.
    
5. O sistema confirma a criação.
    

### Fluxos alternativos

- 3A. Código duplicado no evento: sistema bloqueia o cadastro.
    

---

## UC-17 — Abrir ocorrência operacional

### Objetivo

Registrar fato operacional localizado dentro do evento.

### Atores

- Primário: Operador de Campo
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento ativo;
    
- usuário com permissão operacional.
    

### Pós-condições

- ocorrência registrada;
    
- diário operacional atualizado.
    

### Fluxo principal

1. O Operador acessa o módulo de ocorrências.
    
2. Informa código, tipo, título, localização e descrição.
    
3. O sistema valida a consistência mínima.
    
4. O sistema cria a ocorrência.
    
5. O sistema confirma o registro.
    

### Fluxos alternativos

- 3A. Evento inativo: sistema impede a criação.
    
- 3B. Código duplicado no evento: sistema rejeita o registro.
    

---

## UC-18 — Criar missão operacional

### Objetivo

Formalizar uma missão vinculada ao evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento ativo.
    

### Pós-condições

- missão criada e pronta para designação.
    

### Fluxo principal

1. O usuário acessa o módulo de missões.
    
2. Informa código, título, descrição, prioridade e vínculos operacionais.
    
3. O sistema valida o código e os vínculos.
    
4. O sistema cria a missão.
    
5. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Código já utilizado no evento: sistema bloqueia.
    
- 3B. Evento inativo: sistema rejeita a missão.
    

---

## UC-19 — Designar equipe ou recurso à missão

### Objetivo

Alocar meios operacionais à missão.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico, Sistema
    

### Pré-condições

- missão existente;
    
- equipe ou recurso disponível.
    

### Pós-condições

- vínculo de alocação registrado.
    

### Fluxo principal

1. O usuário acessa a missão.
    
2. Seleciona equipe e/ou recurso.
    
3. O sistema valida disponibilidade.
    
4. O sistema registra a designação.
    
5. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Nenhum meio operacional informado: sistema bloqueia.
    
- 3B. Recurso indisponível: sistema impede a designação.
    

---

## UC-20 — Registrar avaliação de danos

### Objetivo

Consolidar danos observados no evento.

### Atores

- Primário: Analista Técnico
    
- Secundário: Operador de Campo
    

### Pré-condições

- evento ativo ou em fase compatível.
    

### Pós-condições

- avaliação de danos registrada.
    

### Fluxo principal

1. O usuário acessa o módulo de danos.
    
2. Informa tipo de avaliação e dados quantitativos/qualitativos.
    
3. O sistema valida consistência dos valores.
    
4. O sistema persiste a avaliação.
    
5. O sistema confirma o registro.
    

### Fluxos alternativos

- 3A. Valores negativos: sistema rejeita o envio.
    
- 3B. Evento incompatível: sistema bloqueia a operação.
    

---

## UC-21 — Registrar avaliação de necessidades

### Objetivo

Consolidar necessidades geradas pelo evento.

### Atores

- Primário: Analista Técnico
    
- Secundário: Operador de Campo
    

### Pré-condições

- evento ativo ou compatível.
    

### Pós-condições

- necessidade registrada e disponível para gestão.
    

### Fluxo principal

1. O usuário acessa o módulo de necessidades.
    
2. Informa categoria, prioridade, quantidade e observações.
    
3. O sistema valida os campos mínimos.
    
4. O sistema salva a avaliação.
    
5. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Categoria ausente: operação negada.
    
- 3B. Prioridade inválida: sistema rejeita o cadastro.
    

---

## UC-22 — Registrar decisão operacional

### Objetivo

Formalizar decisão tomada durante a gestão do evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento existente;
    
- usuário autorizado.
    

### Pós-condições

- decisão registrada com autoria e data.
    

### Fluxo principal

1. O usuário acessa o painel de decisões.
    
2. Informa título, descrição e status de implementação.
    
3. O sistema valida os dados mínimos.
    
4. O sistema grava a decisão.
    
5. O sistema confirma o registro.
    

### Fluxos alternativos

- 3A. Descrição vazia: sistema impede o registro.
    

---

## UC-23 — Encerrar evento

### Objetivo

Formalizar o término do evento operacional.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Sistema
    

### Pré-condições

- evento aberto;
    
- usuário com permissão de encerramento.
    

### Pós-condições

- evento encerrado;
    
- status histórico registrado;
    
- fechamento formal persistido.
    

### Fluxo principal

1. O Coordenador acessa o evento.
    
2. Solicita encerramento.
    
3. Informa motivo e síntese final.
    
4. O sistema valida coerência temporal e estado do evento.
    
5. O sistema registra o encerramento.
    
6. O sistema altera o status do evento.
    
7. O sistema registra marco final na timeline.
    
8. O sistema confirma a operação.
    

### Fluxos alternativos

- 4A. Evento já encerrado: sistema bloqueia a ação.
    
- 4B. Dados de fechamento incompletos: sistema rejeita a operação.
    

---

## UC-24 — Gerar relatório por evento

### Objetivo

Emitir relatório institucional consolidado do evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico, Sistema
    

### Pré-condições

- evento existente;
    
- template disponível;
    
- dados mínimos preenchidos.
    

### Pós-condições

- relatório gerado;
    
- arquivo registrado;
    
- histórico de geração persistido.
    

### Fluxo principal

1. O usuário acessa a área de relatórios do evento.
    
2. Seleciona o tipo de relatório.
    
3. O sistema valida completude mínima.
    
4. O sistema consolida os dados.
    
5. O sistema gera o documento.
    
6. O sistema registra a geração.
    
7. O sistema disponibiliza o relatório ao usuário.
    

### Fluxos alternativos

- 3A. Dados obrigatórios ausentes: sistema impede emissão.
    
- 5A. Falha técnica na geração: sistema registra erro e informa indisponibilidade.
    

---

# BLOCO B — CASOS DE USO DE SEGURANÇA, CONTROLE E SUPORTE

## UC-25 — Autenticar usuário

### Objetivo

Permitir acesso seguro ao sistema.

### Atores

- Primário: Usuário autenticável do sistema
    
- Secundário: Sistema
    

### Pré-condições

- usuário cadastrado e ativo.
    

### Pós-condições

- sessão criada;
    
- acesso concedido conforme permissões.
    

### Fluxo principal

1. O usuário informa credenciais.
    
2. O sistema valida credenciais e status.
    
3. O sistema aplica política de segurança.
    
4. O sistema cria sessão autenticada.
    
5. O sistema libera acesso.
    

### Fluxos alternativos

- 2A. Credenciais inválidas: acesso negado.
    
- 2B. Usuário inativo: acesso bloqueado.
    
- 3A. MFA obrigatório e não atendido: autenticação não concluída.
    

---

## UC-26 — Encerrar sessão

### Objetivo

Finalizar sessão autenticada do usuário.

### Atores

- Primário: Usuário autenticado
    
- Secundário: Sistema
    

### Pré-condições

- sessão ativa.
    

### Pós-condições

- sessão invalidada.
    

### Fluxo principal

1. O usuário solicita saída do sistema.
    
2. O sistema invalida a sessão.
    
3. O sistema redireciona para tela de acesso.
    

---

## UC-27 — Consultar trilha de auditoria

### Objetivo

Permitir consulta de ações auditáveis do sistema.

### Atores

- Primário: Auditor
    
- Secundário: Administrador do Tenant
    

### Pré-condições

- usuário com permissão de auditoria.
    

### Pós-condições

- logs retornados conforme filtro e escopo autorizado.
    

### Fluxo principal

1. O Auditor acessa o módulo de auditoria.
    
2. Define filtros de período, módulo, ação ou usuário.
    
3. O sistema valida permissões e escopo.
    
4. O sistema recupera os registros.
    
5. O sistema apresenta os resultados.
    

### Fluxos alternativos

- 3A. Usuário sem permissão: acesso negado.
    
- 4A. Nenhum resultado encontrado: sistema informa ausência de registros.
    

---

## UC-28 — Atualizar política de segurança do tenant

### Objetivo

Definir regras de autenticação e sessão do ambiente do cliente.

### Atores

- Primário: Administrador do Tenant
    
- Secundário: Sistema
    

### Pré-condições

- usuário autorizado;
    
- tenant ativo.
    

### Pós-condições

- política de segurança atualizada.
    

### Fluxo principal

1. O Administrador acessa as configurações de segurança.
    
2. Define parâmetros de senha, sessão e MFA.
    
3. O sistema valida os valores.
    
4. O sistema atualiza a política.
    
5. O sistema registra auditoria.
    
6. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Valores inválidos: sistema rejeita a atualização.
    

---

# BLOCO C — CASOS DE USO DE EXPANSÃO OPERACIONAL

## UC-29 — Ativar abrigo em evento

### Objetivo

Colocar abrigo previamente cadastrado em operação no contexto de um evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento ativo;
    
- abrigo cadastrado e disponível.
    

### Pós-condições

- abrigo operacional ativo no evento.
    

### Fluxo principal

1. O usuário seleciona um abrigo cadastrado.
    
2. Solicita ativação no evento.
    
3. O sistema valida disponibilidade e coerência do vínculo.
    
4. O sistema cria o abrigo ativo.
    
5. O sistema confirma a ativação.
    

---

## UC-30 — Registrar família assistida

### Objetivo

Cadastrar família atendida pela operação humanitária.

### Atores

- Primário: Analista Técnico
    
- Secundário: Operador de Campo
    

### Pré-condições

- evento ativo ou em fase compatível.
    

### Pós-condições

- família registrada para acompanhamento e distribuição.
    

### Fluxo principal

1. O usuário acessa a assistência humanitária.
    
2. Informa código da família, responsável e origem.
    
3. O sistema valida dados mínimos.
    
4. O sistema cria o cadastro.
    
5. O sistema confirma o registro.
    

---

## UC-31 — Registrar movimentação de estoque humanitário

### Objetivo

Controlar entrada e saída de itens humanitários.

### Atores

- Primário: Analista Técnico
    
- Secundário: Sistema
    

### Pré-condições

- item de estoque previamente cadastrado.
    

### Pós-condições

- movimentação registrada;
    
- saldo atualizado.
    

### Fluxo principal

1. O usuário seleciona o item de estoque.
    
2. Informa tipo de movimentação, quantidade e referência.
    
3. O sistema valida quantidade e saldo.
    
4. O sistema registra a movimentação.
    
5. O sistema atualiza o saldo.
    
6. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Saída superior ao saldo: sistema bloqueia.
    
- 3B. Quantidade inválida: sistema rejeita.
    

---

## UC-32 — Registrar distribuição de ajuda humanitária

### Objetivo

Formalizar entrega de itens a pessoas ou famílias assistidas.

### Atores

- Primário: Analista Técnico
    
- Secundário: Operador de Campo, Sistema
    

### Pré-condições

- evento válido;
    
- destinatário identificado;
    
- estoque suficiente.
    

### Pós-condições

- distribuição registrada;
    
- itens baixados do estoque.
    

### Fluxo principal

1. O usuário seleciona destinatário.
    
2. Informa itens e quantidades.
    
3. O sistema valida estoque e consistência do destinatário.
    
4. O sistema registra a distribuição.
    
5. O sistema baixa os itens do estoque.
    
6. O sistema confirma a operação.
    

### Fluxos alternativos

- 3A. Estoque insuficiente: operação bloqueada.
    
- 3B. Nenhum item informado: sistema rejeita o registro.
    

---

## UC-33 — Criar plano de recuperação

### Objetivo

Abrir a fase formal de recuperação vinculada ao evento.

### Atores

- Primário: Coordenador de Defesa Civil
    
- Secundário: Analista Técnico
    

### Pré-condições

- evento em fase compatível com recuperação.
    

### Pós-condições

- plano de recuperação criado.
    

### Fluxo principal

1. O usuário acessa o módulo de recuperação.
    
2. Seleciona o evento.
    
3. Informa título e parâmetros iniciais.
    
4. O sistema valida elegibilidade do evento.
    
5. O sistema cria o plano.
    
6. O sistema confirma a operação.
    

---

## UC-34 — Registrar lição aprendida

### Objetivo

Preservar aprendizado institucional derivado de simulado ou evento.

### Atores

- Primário: Analista Técnico
    
- Secundário: Coordenador de Defesa Civil
    

### Pré-condições

- fonte da lição identificável.
    

### Pós-condições

- lição aprendida registrada e passível de consulta futura.
    

### Fluxo principal

1. O usuário acessa o módulo de lições aprendidas.
    
2. Informa categoria, descrição, origem e recomendação.
    
3. O sistema valida os campos mínimos.
    
4. O sistema salva a lição.
    
5. O sistema confirma o registro.
    

---

## UC-35 — Consolidar histórico do desastre

### Objetivo

Transformar o evento encerrado em memória institucional consultável.

### Atores

- Primário: Sistema
    
- Secundário: Coordenador de Defesa Civil, Analista Técnico
    

### Pré-condições

- evento encerrado;
    
- dados mínimos disponíveis.
    

### Pós-condições

- registro histórico consolidado.
    

### Fluxo principal

1. O usuário solicita a consolidação histórica ou o sistema a inicia por rotina controlada.
    
2. O sistema verifica se o evento está encerrado.
    
3. O sistema consolida dados essenciais do evento.
    
4. O sistema cria o registro histórico.
    
5. O sistema confirma a conclusão.
    

### Fluxos alternativos

- 2A. Evento ainda ativo: consolidação bloqueada.
    
- 3A. Dados mínimos insuficientes: sistema informa pendência e não consolida.
    

---

# BLOCO D — RELAÇÃO ENTRE CASOS DE USO E PRIORIDADE DO MVP

## 5. Casos de uso obrigatórios do MVP

Os casos de uso que devem existir já na primeira versão funcional do produto são:

- UC-01 Cadastrar tenant
    
- UC-02 Cadastrar usuário no tenant
    
- UC-03 Cadastrar território e base territorial mínima
    
- UC-04 Cadastrar área de risco
    
- UC-06 Criar plano de contingência
    
- UC-07 Criar nova versão do plano
    
- UC-08 Editar seções do plano
    
- UC-09 Registrar matriz de responsabilidades do plano
    
- UC-10 Registrar protocolo de ativação
    
- UC-11 Publicar versão vigente do plano
    
- UC-12 Abrir evento/desastre
    
- UC-13 Alterar status do evento
    
- UC-14 Ativar estrutura de comando
    
- UC-15 Designar responsáveis a posições funcionais
    
- UC-16 Definir objetivo operacional
    
- UC-17 Abrir ocorrência operacional
    
- UC-18 Criar missão operacional
    
- UC-19 Designar equipe ou recurso à missão
    
- UC-20 Registrar avaliação de danos
    
- UC-21 Registrar avaliação de necessidades
    
- UC-22 Registrar decisão operacional
    
- UC-23 Encerrar evento
    
- UC-24 Gerar relatório por evento
    
- UC-25 Autenticar usuário
    
- UC-27 Consultar trilha de auditoria
    

---

## 6. Relações de dependência entre casos de uso

### Dependências principais

- UC-02 depende de UC-01.
    
- UC-03 depende de UC-01.
    
- UC-04 depende de UC-03.
    
- UC-06 depende de UC-03.
    
- UC-07 depende de UC-06.
    
- UC-08, UC-09 e UC-10 dependem de UC-07.
    
- UC-11 depende de UC-08, UC-09 e UC-10.
    
- UC-12 depende de UC-01 e, idealmente, de UC-03.
    
- UC-14 depende de UC-12.
    
- UC-15 depende de UC-14.
    
- UC-16 depende de UC-12.
    
- UC-17 depende de UC-12.
    
- UC-18 depende de UC-12.
    
- UC-19 depende de UC-18.
    
- UC-20 e UC-21 dependem de UC-12.
    
- UC-23 depende de UC-12.
    
- UC-24 depende de UC-12 e do preenchimento mínimo dos dados.
    

---

## 7. Regras transversais aplicáveis a todos os casos de uso

### RTU-01

Todo caso de uso institucional ou operacional deve respeitar isolamento por tenant.

### RTU-02

Todo caso de uso sensível deve verificar perfil e permissão antes da execução.

### RTU-03

Todo caso de uso crítico deve produzir auditoria quando aplicável.

### RTU-04

Toda alteração de status relevante deve gerar histórico.

### RTU-05

Toda geração de relatório deve validar completude mínima dos dados.

### RTU-06

Toda ação com horário deve respeitar coerência temporal.

### RTU-07

Nenhum caso de uso pode remover registros históricos críticos por operação comum.

---

## 8. Encadeamento com os próximos documentos

A partir destes casos de uso, os próximos documentos tecnicamente corretos são:

1. diagrama de casos de uso UML;
    
2. especificação formal do MVP por módulo e tela;
    
3. esqueleto real dos Services em backend;
    
4. plano de testes por caso de uso;
    
5. backlog técnico priorizado por release.
    

---

## 9. Conclusão técnica

Este documento transforma a visão funcional do sistema em fluxos formais de interação, já com foco em comportamento, dependências, validações e resultado esperado.

Ele serve como ponte direta entre produto, regra de negócio, modelagem de dados, backend e testes.

O próximo passo mais correto é produzir o **Diagrama de Casos de Uso UML** e, em paralelo, a **Especificação Formal do MVP por módulo e tela**, pois isso dará ao projeto capacidade real de entrar em fase de construção organizada.