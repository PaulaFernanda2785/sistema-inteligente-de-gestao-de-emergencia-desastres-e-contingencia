### 1. Resumo executivo

O SIGEDC Brasil é uma plataforma SaaS multi-inquilino destinada a apoiar a gestão de riscos e desastres em órgãos de Proteção e Defesa Civil. O sistema cobre a fase de normalidade, a fase de desastre e o pós-evento, integrando planejamento, operação e inteligência institucional em uma única solução.

O produto será desenvolvido com foco em clareza, segurança, rastreabilidade e modularidade, permitindo adoção progressiva por municípios e estados com diferentes níveis de maturidade administrativa e operacional.

---

### 2. Objetivos do produto

#### 2.1 Objetivo geral

Disponibilizar uma plataforma digital que permita às Defesas Civis municipais e estaduais planejar, executar, registrar e analisar ações relacionadas à preparação, resposta e recuperação em desastres, com base em fluxos operacionais padronizados.

#### 2.2 Objetivos específicos

- digitalizar o Plano de Contingência em estrutura parametrizada;
- apoiar a preparação institucional na fase de normalidade;
- estruturar a ativação e o gerenciamento operacional do desastre;
- oferecer suporte ao uso de SCI/SCO;
- consolidar histórico de desastres e relatórios institucionais;
- fornecer indicadores de gestão e aprendizado.

---

### 3. Fora do escopo inicial

Para evitar expansão prematura do projeto, ficam fora do MVP:

- integração profunda com todas as bases nacionais logo no início;
- app mobile nativo completo;
- inteligência artificial preditiva avançada no primeiro ciclo;
- sistema GIS avançado de alta complexidade;
- automações federativas altamente específicas por estado;
- marketplace de fornecedores;
- despacho operacional com rádio/telemetria em tempo real.

Esses itens poderão entrar no roadmap posterior.

---

### 4. Escopo funcional por módulos

## Módulo 1 — Administração Institucional

### Finalidade

Registrar e organizar a estrutura do órgão contratante.

### Funcionalidades

- cadastro do ente contratante;
- cadastro de unidades organizacionais;
- cadastro de usuários;
- perfis e permissões;
- cadastro de órgãos de apoio;
- cadastro de equipes, funções e contatos;
- parametrizações institucionais.

### Entidades principais

- tenants
- unidades
- usuarios
- perfis
- orgaos_apoio
- contatos

---

## Módulo 2 — Base Territorial e Diagnóstico

### Finalidade

Organizar a base territorial do cliente e seus cenários de risco.

### Funcionalidades

- cadastro de municípios, distritos, bairros e comunidades;
- cadastro de áreas de risco;
- cadastro de cenários e tipologias de desastre;
- pontos de apoio;
- abrigos;
- rotas de evacuação;
- bases de operação;
- recursos territoriais estratégicos.

### Entidades principais

- territorios
- areas_risco
- cenarios_risco
- abrigos
- rotas
- pontos_apoio
- bases_operacionais

---

## Módulo 3 — Plano de Contingência

### Finalidade

Permitir a elaboração, revisão, aprovação e versionamento do Plano de Contingência.

### Funcionalidades

- criação guiada do plano;
- estrutura por seções padronizadas;
- editor por etapas;
- matriz de responsabilidades por órgão;
- protocolos de acionamento;
- definição de cenários e gatilhos;
- anexos e documentos de suporte;
- versionamento;
- aprovação e publicação;
- histórico de revisões.

### Entidades principais

- planos_contingencia
- versoes_plano
- secoes_plano
- responsabilidades_plano
- anexos_plano

---

## Módulo 4 — Preparação, Simulados e Prontidão

### Finalidade

Acompanhar o estado de preparação institucional.

### Funcionalidades

- calendário de simulados;
- planos de exercício;
- registros de treinamentos;
- checklists de prontidão;
- controle de pendências do plano;
- revisão periódica de contatos, abrigos e recursos;
- registro de lições dos simulados.

### Entidades principais

- simulados
- treinamentos
- checklists_prontidao
- pendencias_preparacao

---

## Módulo 5 — Ativação do Evento/Desastre

### Finalidade

Registrar a abertura formal do evento e iniciar a gestão operacional.

### Funcionalidades

- abertura de ocorrência/evento;
- classificação do desastre/evento;
- vinculação ao cenário de risco;
- data e hora de início;
- status operacional;
- severidade e prioridade;
- timeline de evolução;
- anexos iniciais;
- encerramento formal do evento.

### Entidades principais

- eventos_desastre
- classificacoes_evento
- timeline_evento
- anexos_evento

---

## Módulo 6 — Gestão SCI/SCO

### Finalidade

Estruturar a coordenação operacional do desastre com base em lógica de comando.

### Funcionalidades

- definição de comandante/coordenador;
- estrutura funcional da resposta;
- objetivos operacionais;
- reuniões de situação;
- formulários padronizados;
- gestão de recursos e missões;
- transferência de comando;
- registro de decisões.

### Entidades principais

- estrutura_comando
- objetivos_operacionais
- reunioes_situacao
- formularios_operacionais
- decisoes_operacionais
- transferencias_comando

---

## Módulo 7 — Operações e Campo

### Finalidade

Registrar e monitorar a execução das ações durante o desastre.

### Funcionalidades

- cadastro de ocorrências operacionais;
- despacho de equipes;
- controle de missões;
- cadastro de danos e necessidades;
- gestão de abrigos ativos;
- assistência humanitária;
- voluntários;
- recursos logísticos;
- acompanhamento de ações por área afetada.

### Entidades principais

- ocorrencias
- missoes
- equipes_operacionais
- danos
- necessidades
- abrigos_ativos
- distribuicoes
- voluntarios
- recursos_operacionais

---

## Módulo 8 — Recuperação e Pós-Evento

### Finalidade

Acompanhar as ações após a resposta imediata.

### Funcionalidades

- plano de recuperação por evento;
- ações por eixo temático;
- monitoramento da execução;
- pendências estruturais;
- avaliação de encerramento;
- lições aprendidas;
- relatório final do evento.

### Entidades principais

- planos_recuperacao
- acoes_recuperacao
- licoes_aprendidas
- encerramentos_evento

---

## Módulo 9 — Inteligência, Histórico e Relatórios

### Finalidade

Consolidar indicadores, histórico e relatórios institucionais.

### Funcionalidades

- painel executivo;
- relatórios por desastre;
- relatórios por período;
- histórico institucional;
- comparativos por tipologia;
- indicadores de resposta e prontidão;
- exportação PDF e planilhas;
- relatórios consolidados estaduais.

### Entidades principais

- relatorios_evento
- relatorios_gerais
- historico_desastres
- indicadores

---

### 5. Requisitos funcionais prioritários do MVP

#### RF-01

O sistema deve permitir cadastrar a estrutura institucional do órgão contratante.

#### RF-02

O sistema deve permitir cadastrar áreas de risco, abrigos, rotas e pontos de apoio.

#### RF-03

O sistema deve permitir criar o Plano de Contingência em fluxo guiado por etapas.

#### RF-04

O sistema deve permitir versionar o Plano de Contingência.

#### RF-05

O sistema deve permitir registrar simulados e checklists de prontidão.

#### RF-06

O sistema deve permitir abrir formalmente um evento/desastre.

#### RF-07

O sistema deve permitir organizar a estrutura operacional com lógica de comando.

#### RF-08

O sistema deve permitir cadastrar objetivos operacionais e missões.

#### RF-09

O sistema deve permitir registrar danos, necessidades e ações de resposta.

#### RF-10

O sistema deve permitir gerenciar abrigos e distribuição de ajuda humanitária.

#### RF-11

O sistema deve permitir registrar decisões operacionais e reuniões de situação.

#### RF-12

O sistema deve permitir encerrar o evento com relatório final.

#### RF-13

O sistema deve permitir consultar o histórico de desastres por filtros.

#### RF-14

O sistema deve gerar relatórios institucionais em PDF.

#### RF-15

O sistema deve gerar dashboards consolidados por período, tipologia e localidade.

---

### 6. Requisitos não funcionais iniciais

#### RNF-01 Segurança

Controle de acesso por perfil, autenticação segura e segregação de dados por tenant.

#### RNF-02 Auditoria

Toda ação crítica deve gerar log auditável.

#### RNF-03 Disponibilidade

A plataforma deve operar com alta disponibilidade compatível com contrato SaaS.

#### RNF-04 Performance

As operações principais do painel e consulta devem responder rapidamente sob carga moderada.

#### RNF-05 Escalabilidade

A arquitetura deve suportar expansão gradual de clientes e dados.

#### RNF-06 Integridade documental

Planos, relatórios e registros operacionais devem possuir versionamento e rastreabilidade.

#### RNF-07 Usabilidade

A interface deve ser objetiva, com linguagem clara e baixa curva de aprendizado.

#### RNF-08 Portabilidade

O sistema deve funcionar em navegadores modernos e em tablets/notebooks usados em campo.

#### RNF-09 Exportação

Dados e relatórios críticos devem poder ser exportados em formatos padronizados.

#### RNF-10 Backup

A solução deve possuir rotinas de backup e restauração.

---

### 7. Arquitetura conceitual proposta

#### 7.1 Modelo arquitetural

- SaaS multi-tenant;
- backend web modular;
- API preparada para integrações futuras;
- banco de dados central com segregação lógica por tenant;
- camada de arquivos para anexos e relatórios;
- trilha de auditoria;
- painel web responsivo.

#### 7.2 Diretriz tecnológica inicial

- Backend: PHP moderno com framework MVC robusto;
- Banco de dados: relacional, com suporte futuro a dados geoespaciais;
- Frontend: interface web responsiva;
- Filas e cache: camada específica para rotinas assíncronas futuras;
- Hospedagem: ambiente cloud evolutivo.

---

### 8. Perfis de usuário

- Superadministrador da plataforma;
- Administrador do tenant;
- Coordenador institucional;
- Técnico/analista;
- Operacional de campo;
- Visualizador/consulta;
- Gestor estadual/regional.

---

### 9. Regras de negócio macro

#### RN-01

Cada cliente contratado deve operar isoladamente em seu próprio contexto institucional.

#### RN-02

Um Plano de Contingência poderá possuir múltiplas versões, mas apenas uma versão vigente por escopo.

#### RN-03

Eventos/desastres devem possuir status operacional controlado.

#### RN-04

Toda decisão operacional relevante deve ser registrada com autor, data e contexto.

#### RN-05

A estrutura de comando poderá ser adaptada por evento, conforme o porte e a complexidade.

#### RN-06

O histórico do desastre não poderá ser removido por usuários comuns após encerramento.

#### RN-07

Relatórios oficiais devem refletir os dados registrados no sistema e manter vínculo com o evento.

#### RN-08

Recursos, abrigos e equipes poderão ter status de disponibilidade.

#### RN-09

O sistema deve distinguir dados de preparação e dados de resposta.

#### RN-10

O encerramento do desastre deverá consolidar indicadores e documentação final.

---

### 10. Indicadores de sucesso do produto

#### Indicadores operacionais

- tempo médio para criar/atualizar um plano;
- tempo para abrir um evento;
- tempo para gerar relatório oficial;
- número de ações registradas por evento;
- índice de atualização da base institucional.

#### Indicadores de adoção

- número de usuários ativos por tenant;
- frequência de uso por módulo;
- taxa de conclusão do plano;
- número de simulados registrados;
- taxa de renovação da assinatura.

#### Indicadores estratégicos

- redução de retrabalho documental;
- aumento da padronização dos registros;
- retenção anual de clientes;
- expansão da base municipal/estadual.

---

### 11. Roadmap sugerido

#### Fase 1 — MVP Comercial

- Administração Institucional;
- Base Territorial;
- Plano de Contingência;
- Ativação do Evento;
- Gestão básica de comando;
- Relatórios iniciais.

#### Fase 2 — Operação Ampliada

- Simulados e prontidão;
- danos e necessidades;
- abrigos e assistência;
- painel executivo ampliado;
- histórico consolidado.

#### Fase 3 — Maturidade SaaS

- supervisão estadual/regional;
- integrações externas;
- analytics avançado;
- automações;
- biblioteca nacional de templates.

---

### 12. Premissas técnicas

1. O sistema será desenvolvido como produto escalável e reutilizável.
2. A parametrização será controlada para não gerar versões paralelas do software.
3. O núcleo do produto será web-first.
4. A documentação técnica acompanhará a evolução módulo a módulo.
5. O projeto será conduzido com foco em MVP robusto e expansão posterior.

---

### 13. Dependências para avanço do projeto

- definição do nome comercial definitivo;
- definição da arquitetura tecnológica final;
- modelagem de dados detalhada;
- matriz de requisitos funcionais detalhados;
- UX flow das telas principais;
- política comercial e de implantação;
- estratégia de hospedagem e suporte.

---

### 14. Próximos documentos a produzir

1. Arquitetura completa do sistema;
2. mapa completo dos módulos e telas;
3. modelagem conceitual do banco de dados;
4. dicionário de dados;
5. matriz de regras de negócio;
6. casos de uso;
7. roadmap técnico de desenvolvimento;
8. plano comercial SaaS;
9. documento técnico formal do projeto;
10. especificação do MVP.

---

### 15. Decisão estratégica recomendada

Iniciar o projeto pela construção formal do **Documento de Arquitetura do Sistema** e do **Mapa Completo dos Módulos e Telas**, porque esses dois documentos servirão de base para o banco de dados, regras de negócio, UX e planejamento de desenvolvimento.

---

## Encaminhamento

Este documento representa a base inicial de produto e requisitos. Ele deve ser refinado nos próximos passos com profundidade técnica crescente, até se converter em documentação completa de engenharia, negócio e implantação.