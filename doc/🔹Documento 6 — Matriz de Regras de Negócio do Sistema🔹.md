
---

## 1. Finalidade do documento

Este documento consolida a matriz de regras de negócio do sistema, cruzando:

- módulo;
    
- ação;
    
- tabelas impactadas;
    
- service responsável;
    
- validações críticas;
    
- resposta esperada.
    

O objetivo é transformar a arquitetura funcional e a modelagem de dados em comportamento operacional explícito, servindo de base para:

- desenvolvimento backend;
    
- construção dos services;
    
- validações de controllers/requests;
    
- testes funcionais;
    
- governança do escopo do MVP e das fases seguintes.
    

---

## 2. Convenções da matriz

### 2.1 Colunas utilizadas

- **Módulo**: domínio funcional do sistema.
    
- **Ação**: operação executada pelo usuário ou pelo sistema.
    
- **Tabelas impactadas**: tabelas principais envolvidas.
    
- **Service responsável**: camada central de negócio sugerida.
    
- **Validações críticas**: regras obrigatórias para execução.
    
- **Resposta esperada**: resultado funcional esperado.
    

### 2.2 Premissas gerais

1. Toda ação de domínio institucional ou operacional deve respeitar `tenant_id`.
    
2. Toda ação sensível deve gerar trilha de auditoria.
    
3. Toda alteração relevante de status deve preservar histórico quando aplicável.
    
4. Nenhuma regra de negócio crítica deve ficar concentrada em controller.
    
5. A resposta esperada deve refletir o comportamento ideal do sistema, não apenas a persistência no banco.
    

---

# BLOCO A — ADMINISTRAÇÃO E GOVERNANÇA

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Administração|Criar tenant|`tenants`, `subscriptions`, `tenant_settings`, `security_policies`|`TenantService`|documento não duplicado; plano válido; dados mínimos obrigatórios|tenant criado com configurações iniciais e assinatura associada|
|Administração|Atualizar tenant|`tenants`|`TenantService`|tenant existente; usuário com permissão administrativa global|dados institucionais atualizados com trilha de auditoria|
|Administração|Ativar/inativar tenant|`tenants`|`TenantService`|tenant existente; regra comercial/contratual válida|tenant alterado sem perda do histórico|
|Administração|Configurar parâmetros do tenant|`tenant_settings`|`TenantSettingService`|chave permitida; valor compatível com tipo|configuração persistida e disponível ao sistema|
|Administração|Habilitar funcionalidade por plano|`tenant_feature_flags`|`FeatureFlagService`|feature existente; tenant elegível|funcionalidade liberada ou bloqueada para o tenant|
|Administração|Criar organização|`organizations`|`OrganizationService`|tenant válido; nome obrigatório|organização cadastrada e ativa|
|Administração|Atualizar organização|`organizations`|`OrganizationService`|organização do mesmo tenant; usuário autorizado|dados institucionais atualizados|
|Administração|Criar unidade organizacional|`organizational_units`|`OrganizationalUnitService`|organização válida; unidade pai do mesmo tenant, se informada|unidade cadastrada na hierarquia correta|
|Administração|Inativar unidade organizacional|`organizational_units`|`OrganizationalUnitService`|unidade existente; impacto operacional avaliado|unidade inativada sem exclusão histórica|
|Administração|Criar usuário|`users`, `user_roles`, `audit_logs`|`UserManagementService`|e-mail único por tenant; senha válida; organização obrigatória|usuário criado com acesso inicial configurado|
|Administração|Atualizar usuário|`users`|`UserManagementService`|usuário do mesmo tenant; campos consistentes|dados atualizados e auditados|
|Administração|Inativar usuário|`users`, `active_sessions`, `audit_logs`|`UserManagementService`|usuário existente; não violar regra crítica de administração mínima|usuário bloqueado e sessões encerradas|
|Administração|Vincular perfil ao usuário|`user_roles`|`RolePermissionService`|perfil existente; mesmo escopo do tenant ou perfil global|permissões do usuário atualizadas|
|Administração|Remover perfil do usuário|`user_roles`|`RolePermissionService`|usuário e perfil existentes; não remover único perfil crítico indevidamente|vínculo removido com auditoria|
|Administração|Criar perfil customizado|`roles`|`RolePermissionService`|nome e código válidos no escopo do tenant|perfil disponível para associação|
|Administração|Associar permissões ao perfil|`role_permissions`|`RolePermissionService`|permissões válidas; perfil editável|perfil passa a refletir o conjunto de permissões escolhido|
|Administração|Cadastrar órgão parceiro|`partner_agencies`|`PartnerAgencyService`|nome obrigatório; tenant válido|órgão parceiro cadastrado|
|Administração|Cadastrar contato estratégico|`strategic_contacts`|`StrategicContactService`|telefone principal obrigatório; vínculo institucional coerente|contato disponível para acionamento|
|Administração|Atualizar contato estratégico|`strategic_contacts`|`StrategicContactService`|contato do mesmo tenant|contato revisado e mantido no catálogo operacional|

---

# BLOCO B — BASE TERRITORIAL E RISCO

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Território|Criar território|`territories`|`TerritoryService`|nome e tipo obrigatórios; tenant válido|território cadastrado|
|Território|Criar unidade territorial|`territorial_units`|`TerritoryService`|território válido; unidade pai do mesmo território, se houver|subdivisão cadastrada corretamente|
|Território|Atualizar unidade territorial|`territorial_units`|`TerritoryService`|unidade existente; tenant compatível|registro territorial atualizado|
|Risco|Cadastrar área de risco|`risk_areas`|`RiskAreaService`|unidade territorial válida; tipo e prioridade obrigatórios|área de risco cadastrada|
|Risco|Atualizar área de risco|`risk_areas`|`RiskAreaService`|área existente; mesmo tenant|área atualizada sem perda do histórico de uso|
|Risco|Inativar área de risco|`risk_areas`|`RiskAreaService`|área existente; avaliar vínculo com plano vigente|área desativada para novos usos|
|Risco|Importar/desenhar geometria|`risk_area_geometries`|`GeoProcessingService`|área válida; geometria compatível; origem registrada|geometria associada à área|
|Risco|Atualizar geometria da área|`risk_area_geometries`|`GeoProcessingService`|área válida; novo dado espacial íntegro|geometria revisada e disponível para mapa|
|Risco|Criar cenário de risco|`risk_scenarios`|`RiskScenarioService`|tipologia válida; tenant compatível|cenário cadastrado para uso em plano e eventos|
|Risco|Atualizar cenário de risco|`risk_scenarios`|`RiskScenarioService`|cenário existente; mesmo tenant|cenário revisado|
|Abrigos|Cadastrar abrigo|`shelters`|`ShelterService`|unidade territorial válida; capacidade >= 0; endereço obrigatório|abrigo cadastrado para uso potencial|
|Abrigos|Atualizar abrigo|`shelters`|`ShelterService`|abrigo do mesmo tenant|abrigo atualizado|
|Abrigos|Inativar abrigo|`shelters`|`ShelterService`|abrigo não pode ser excluído se estiver em uso operacional ativo|abrigo marcado como inativo|
|Abrigos|Registrar capacidade detalhada|`shelter_capacities`|`ShelterService`|abrigo válido; quantidade >= 0|capacidade complementar persistida|
|Rotas|Cadastrar rota de evacuação|`evacuation_routes`|`RouteService`|origem e destino obrigatórios; unidade territorial válida|rota disponível para plano e resposta|
|Rotas|Atualizar rota|`evacuation_routes`|`RouteService`|rota existente; mesmo tenant|rota revisada|
|Pontos de apoio|Cadastrar ponto de apoio|`support_points`|`SupportPointService`|nome e tipo obrigatórios|ponto cadastrado|
|Bases operacionais|Cadastrar base operacional|`operational_bases`|`OperationalBaseService`|unidade territorial válida; nome obrigatório|base operacional cadastrada|

---

# BLOCO C — PLANO DE CONTINGÊNCIA

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Plano de Contingência|Criar plano|`contingency_plans`|`ContingencyPlanService`|território obrigatório; título obrigatório; usuário autorizado|plano criado em status inicial|
|Plano de Contingência|Atualizar metadados do plano|`contingency_plans`|`ContingencyPlanService`|plano do mesmo tenant; campos válidos|metadados atualizados|
|Plano de Contingência|Criar nova versão|`contingency_plan_versions`|`PlanVersioningService`|plano existente; numeração não duplicada|nova versão criada|
|Plano de Contingência|Clonar versão anterior|`contingency_plan_versions`, `contingency_plan_sections`, `responsibility_matrix`, `activation_protocols`|`PlanVersioningService`|versão origem válida; tenant compatível|nova versão criada com base na anterior|
|Plano de Contingência|Editar seção|`contingency_plan_sections`|`ContingencyPlanBuilderService`|versão editável; ordem e código válidos|seção persistida com conteúdo atualizado|
|Plano de Contingência|Inserir item estruturado de seção|`contingency_plan_section_items`|`ContingencyPlanBuilderService`|seção existente; item_key coerente; ordem válida|item estruturado salvo|
|Plano de Contingência|Atualizar item estruturado|`contingency_plan_section_items`|`ContingencyPlanBuilderService`|item existente; seção do mesmo tenant|item atualizado|
|Plano de Contingência|Registrar matriz de responsabilidades|`responsibility_matrix`|`ResponsibilityMatrixService`|ação obrigatória; responsável principal válido; versão editável|responsabilidade vinculada ao plano|
|Plano de Contingência|Atualizar matriz de responsabilidades|`responsibility_matrix`|`ResponsibilityMatrixService`|registro existente; responsáveis coerentes com o tenant|matriz ajustada|
|Plano de Contingência|Criar protocolo de ativação|`activation_protocols`|`ActivationProtocolService`|gatilho e passos obrigatórios; versão editável|protocolo salvo e pronto para uso|
|Plano de Contingência|Atualizar protocolo de ativação|`activation_protocols`|`ActivationProtocolService`|protocolo existente; steps válidos|protocolo revisado|
|Plano de Contingência|Anexar documento|`contingency_plan_attachments`|`ContingencyPlanService`|versão válida; arquivo permitido; usuário autorizado|anexo vinculado ao plano|
|Plano de Contingência|Submeter versão para revisão|`contingency_plan_versions`|`PlanPublishingService`|versão não pode estar vazia; regras mínimas de completude atendidas|versão muda para status de revisão|
|Plano de Contingência|Aprovar versão|`contingency_plan_versions`|`PlanPublishingService`|usuário com permissão; versão em fluxo correto|versão aprovada|
|Plano de Contingência|Publicar versão|`contingency_plan_versions`, `contingency_plans`, `contingency_plan_publications`|`PlanPublishingService`|somente uma vigente por escopo; versão aprovada|versão publicada e definida como vigente|
|Plano de Contingência|Revogar vigência de versão|`contingency_plan_versions`, `contingency_plans`|`PlanPublishingService`|plano deve manter coerência de vigência|versão retirada de vigência sem apagar histórico|
|Plano de Contingência|Gerar PDF do plano|`generated_reports`|`PlanPdfService`|versão válida; template disponível|PDF gerado e registrado|

---

# BLOCO D — PREPARAÇÃO E PRONTIDÃO

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Preparação|Criar simulado|`drills`|`DrillService`|título, objetivo e data obrigatórios|simulado cadastrado|
|Preparação|Atualizar simulado|`drills`|`DrillService`|simulado do mesmo tenant; status compatível|simulado atualizado|
|Preparação|Registrar participante de simulado|`drill_participants`|`DrillService`|participante interno ou externo obrigatório|participante associado ao exercício|
|Preparação|Registrar execução do simulado|`drills`|`DrillService`|simulado existente; status anterior compatível|simulado passa a constar como executado|
|Preparação|Registrar lição aprendida do simulado|`simulation_lessons`|`SimulationLessonService`|simulado válido; descrição obrigatória|lição aprendida vinculada ao simulado|
|Preparação|Criar treinamento|`trainings`|`TrainingService`|título e data obrigatórios|treinamento registrado|
|Preparação|Registrar participante de treinamento|`training_attendees`|`TrainingService`|treinamento válido; participante coerente|presença registrada|
|Preparação|Criar checklist de prontidão|`readiness_checklists`|`ReadinessAssessmentService`|título, escopo e data obrigatórios|checklist criado|
|Preparação|Inserir item de checklist|`readiness_checklist_items`|`ReadinessAssessmentService`|checklist válido; nome do item obrigatório|item associado ao checklist|
|Preparação|Atualizar status do item do checklist|`readiness_checklist_items`|`ReadinessAssessmentService`|item existente; status permitido|situação do item atualizada|
|Preparação|Criar pendência preparatória|`preparedness_tasks`|`PreparednessTaskService`|título e prioridade obrigatórios|pendência registrada|
|Preparação|Concluir pendência|`preparedness_tasks`|`PreparednessTaskService`|pendência existente; fluxo de status válido|pendência marcada como concluída|

---

# BLOCO E — EVENTOS E ATIVAÇÃO

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Eventos|Abrir evento|`disaster_events`, `disaster_event_status_history`, `disaster_event_timeline`, `audit_logs`|`DisasterEventService`|tipologia e severidade obrigatórias; código único por tenant; início obrigatório|evento criado e linha do tempo iniciada|
|Eventos|Atualizar dados do evento|`disaster_events`|`DisasterEventService`|evento existente; usuário autorizado; integridade temporal preservada|evento atualizado|
|Eventos|Classificar evento|`disaster_event_classifications`|`EventClassificationService`|evento válido; classificação consistente com domínio|classificação registrada|
|Eventos|Alterar status do evento|`disaster_events`, `disaster_event_status_history`|`EventStatusService`|transição de status permitida; evento do mesmo tenant|status alterado com histórico|
|Eventos|Registrar marco temporal|`disaster_event_timeline`|`EventTimelineService`|evento válido; data/hora obrigatória|marco incluído na timeline|
|Eventos|Anexar documento ao evento|`disaster_event_attachments`|`DisasterEventService`|evento válido; arquivo permitido|anexo registrado|
|Eventos|Vincular evento a cenário de risco|`disaster_events`|`DisasterEventService`|cenário e evento do mesmo tenant|evento passa a referenciar cenário específico|
|Eventos|Encerrar evento|`disaster_events`, `disaster_event_status_history`, `disaster_event_closures`, `disaster_event_timeline`|`EventClosureService`|evento aberto; justificativa obrigatória; coerência temporal|evento encerrado formalmente com fechamento registrado|

---

# BLOCO F — COMANDO E COORDENAÇÃO OPERACIONAL

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Comando|Ativar estrutura de comando|`command_structures`, `audit_logs`|`CommandStructureService`|evento ativo; modelo de comando válido|estrutura ativa vinculada ao evento|
|Comando|Desativar estrutura de comando|`command_structures`|`CommandStructureService`|estrutura ativa; data coerente|estrutura marcada como encerrada|
|Comando|Inserir posição funcional|`command_positions`|`CommandStructureService`|estrutura válida; tipo de posição existente|posição adicionada à estrutura|
|Comando|Designar responsável para posição|`command_assignments`|`CommandStructureService`|posição ativa; usuário do tenant ou nome externo informado|designação registrada|
|Comando|Encerrar designação|`command_assignments`|`CommandStructureService`|designação ativa|designação encerrada com registro temporal|
|Comando|Definir objetivo operacional|`operational_objectives`|`OperationalObjectiveService`|evento ativo; código único no evento; prioridade obrigatória|objetivo criado|
|Comando|Atualizar objetivo operacional|`operational_objectives`|`OperationalObjectiveService`|objetivo existente; fluxo de status válido|objetivo atualizado|
|Comando|Registrar reunião de situação|`situation_meetings`|`SituationMeetingService`|evento válido; data/hora obrigatória|reunião registrada|
|Comando|Registrar decisão operacional|`operational_decisions`|`OperationalDecisionService`|evento válido; descrição obrigatória|decisão persistida com autoria e data|
|Comando|Atualizar implementação da decisão|`operational_decisions`|`OperationalDecisionService`|decisão existente; status permitido|progresso da decisão refletido no sistema|
|Comando|Criar formulário operacional|`command_forms`, `command_form_entries`|`CommandFormService`|tipo de formulário válido; evento ativo|formulário criado|
|Comando|Atualizar campos do formulário|`command_form_entries`|`CommandFormService`|formulário editável; campos coerentes|formulário preenchido ou revisado|
|Comando|Transferir comando|`command_transfers`, `command_assignments`|`CommandTransferService`|estrutura ativa; motivo obrigatório; origem/destino coerentes|transferência registrada com rastreabilidade|

---

# BLOCO G — OPERAÇÕES E RESPOSTA

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Operações|Criar equipe operacional|`field_teams`|`FieldTeamService`|nome e tipo obrigatórios|equipe disponível para alocação|
|Operações|Atualizar disponibilidade da equipe|`field_teams`|`FieldTeamService`|equipe existente; status permitido|disponibilidade refletida no quadro operacional|
|Operações|Cadastrar recurso operacional|`operational_resources`|`OperationalResourceService`|nome e tipo obrigatórios|recurso disponível para uso|
|Operações|Atualizar status do recurso|`operational_resources`|`OperationalResourceService`|recurso existente; fluxo de status coerente|status do recurso atualizado|
|Operações|Abrir ocorrência|`operational_occurrences`, `operational_logs`|`OperationalOccurrenceService`|evento ativo; código único no evento; título obrigatório|ocorrência registrada|
|Operações|Atualizar ocorrência|`operational_occurrences`|`OperationalOccurrenceService`|ocorrência existente; mesmo tenant|ocorrência atualizada|
|Operações|Encerrar ocorrência|`operational_occurrences`|`OperationalOccurrenceService`|ocorrência aberta; data de fechamento coerente|ocorrência encerrada|
|Operações|Criar missão|`missions`|`MissionService`|evento obrigatório; código único no evento; descrição obrigatória|missão criada|
|Operações|Atualizar missão|`missions`|`MissionService`|missão existente; transição de status permitida|missão atualizada|
|Operações|Designar equipe/recurso à missão|`mission_assignments`|`MissionService`|missão válida; equipe ou recurso obrigatório; elementos disponíveis|missão passa a ter meios alocados|
|Operações|Iniciar missão|`missions`, `operational_logs`|`MissionService`|missão designada; status anterior compatível|missão em execução|
|Operações|Concluir missão|`missions`, `operational_logs`|`MissionService`|missão em execução; horário coerente|missão finalizada|
|Operações|Alocar recurso ao evento/missão|`resource_allocations`, `operational_resources`|`ResourceAllocationService`|recurso disponível; evento válido|alocação formal registrada|
|Operações|Liberar recurso alocado|`resource_allocations`, `operational_resources`|`ResourceAllocationService`|alocação ativa|recurso volta à disponibilidade adequada|
|Operações|Registrar avaliação de danos|`damage_assessments`|`DamageAssessmentService`|evento válido; avaliador obrigatório; números não negativos|avaliação de danos registrada|
|Operações|Atualizar avaliação de danos|`damage_assessments`|`DamageAssessmentService`|avaliação existente; mesmo tenant|avaliação revisada|
|Operações|Registrar avaliação de necessidades|`needs_assessments`|`NeedsAssessmentService`|categoria e prioridade obrigatórias|necessidade registrada|
|Operações|Atualizar situação da necessidade|`needs_assessments`|`NeedsAssessmentService`|necessidade existente; status permitido|necessidade atualizada|
|Operações|Registrar log operacional|`operational_logs`|`OperationalLogService`|evento válido; título e descrição obrigatórios|fato operacional incluído no diário|

---

# BLOCO H — ABRIGOS E AJUDA HUMANITÁRIA

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Abrigos|Ativar abrigo em evento|`active_shelters`|`ActiveShelterService`|evento ativo; abrigo do mesmo tenant; evitar duplicidade operacional incoerente|abrigo passa ao estado operacional ativo|
|Abrigos|Atualizar situação do abrigo ativo|`active_shelters`|`ActiveShelterService`|abrigo ativo existente|status e contadores atualizados|
|Abrigos|Encerrar abrigo ativo|`active_shelters`|`ActiveShelterService`|abrigo ativo; data coerente|abrigo operacional encerrado|
|Assistência|Cadastrar família assistida|`assisted_families`|`AssistedPopulationService`|evento válido; código de família obrigatório|família registrada|
|Assistência|Cadastrar pessoa assistida|`assisted_people`|`AssistedPopulationService`|evento válido; nome obrigatório; família ou abrigo conforme contexto|pessoa registrada|
|Assistência|Atualizar situação da família|`assisted_families`|`AssistedPopulationService`|família existente|situação familiar atualizada|
|Estoque humanitário|Cadastrar item de estoque|`humanitarian_stock_items`|`HumanitarianInventoryService`|nome, categoria e unidade obrigatórios|item cadastrado|
|Estoque humanitário|Registrar entrada/saída de estoque|`humanitarian_stock_movements`, `humanitarian_stock_items`|`HumanitarianInventoryService`|item válido; quantidade > 0; saldo não pode ficar negativo em saída|movimentação registrada e saldo atualizado|
|Ajuda humanitária|Registrar distribuição|`humanitarian_distributions`, `humanitarian_distribution_items`, `humanitarian_stock_movements`|`HumanitarianDistributionService`|evento válido; destinatário coerente; ao menos um item; estoque suficiente|distribuição registrada com baixa de estoque|
|Abrigos|Gerar relatório de abrigo|`shelter_operation_reports`, `generated_reports`|`ShelterOperationReportService`|abrigo ativo válido; data de referência obrigatória|relatório operacional consolidado|

---

# BLOCO I — RECUPERAÇÃO E PÓS-EVENTO

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Recuperação|Criar plano de recuperação|`recovery_plans`|`RecoveryPlanService`|evento encerrado ou em fase compatível; título obrigatório|plano de recuperação criado|
|Recuperação|Atualizar plano de recuperação|`recovery_plans`|`RecoveryPlanService`|plano existente; mesmo tenant|plano atualizado|
|Recuperação|Criar ação de recuperação|`recovery_actions`|`RecoveryActionService`|plano válido; eixo e título obrigatórios|ação registrada|
|Recuperação|Atualizar ação de recuperação|`recovery_actions`|`RecoveryActionService`|ação existente; status permitido|ação revisada|
|Recuperação|Registrar atualização de andamento|`recovery_action_updates`|`RecoveryActionService`|ação válida; descrição obrigatória|progresso historizado|
|Pós-evento|Registrar avaliação pós-evento|`post_event_evaluations`|`PostEventEvaluationService`|evento válido; data obrigatória|avaliação consolidada|
|Pós-evento|Registrar lição aprendida|`lessons_learned`|`LessonLearnedService`|categoria e descrição obrigatórias|lição aprendida salva|
|Memória institucional|Consolidar histórico do evento|`historical_disaster_records`|`DisasterHistoryService`|evento encerrado; dados mínimos disponíveis|registro histórico institucional gerado|

---

# BLOCO J — RELATÓRIOS, INTELIGÊNCIA, SEGURANÇA E AUDITORIA

|Módulo|Ação|Tabelas impactadas|Service responsável|Validações críticas|Resposta esperada|
|---|---|---|---|---|---|
|Relatórios|Gerar relatório por evento|`generated_reports`|`EventReportService`|evento válido; template disponível|relatório gerado e armazenado|
|Relatórios|Gerar relatório consolidado|`generated_reports`|`ConsolidatedReportService`|filtros válidos; permissões adequadas|relatório consolidado emitido|
|Inteligência|Gerar snapshot analítico|`analytical_snapshots`|`AnalyticsSnapshotService`|tenant válido; referência temporal definida|snapshot salvo para consulta|
|Dashboard|Atualizar cache de dashboard|`dashboard_cache`|`DashboardCacheService`|chave válida; dados consistentes|cache pronto para consumo rápido|
|Segurança|Criar sessão autenticada|`active_sessions`|`SessionManagementService`|usuário ativo; credenciais válidas; política de segurança atendida|sessão aberta|
|Segurança|Encerrar sessão|`active_sessions`|`SessionManagementService`|sessão existente|sessão invalidada|
|Segurança|Atualizar política de segurança|`security_policies`|`SecurityPolicyService`|usuário administrador do tenant; parâmetros válidos|política atualizada|
|Auditoria|Registrar log de auditoria|`audit_logs`|`AuditLogService`|evento auditável; dados mínimos do contexto|log persistido|
|Auditoria|Consultar trilha de auditoria|`audit_logs`|`AuditLogService`|perfil autorizado; filtros válidos|logs retornados conforme escopo|

---

## 3. Regras transversais obrigatórias

### RT-01 — Isolamento por tenant

Nenhuma ação pode ler, editar ou relacionar registros de outro tenant, salvo perfis globais da plataforma em contexto administrativo autorizado.

### RT-02 — Autorização por perfil

Toda ação deve ser protegida por permissão específica. O fato de o usuário estar autenticado não autoriza operação fora de seu escopo.

### RT-03 — Auditoria obrigatória

Devem gerar trilha de auditoria, no mínimo:

- criação e alteração de usuários;
    
- mudanças de permissões;
    
- publicação de plano;
    
- abertura e encerramento de evento;
    
- mudança de status crítico;
    
- decisões operacionais;
    
- geração de relatório oficial;
    
- alteração de política de segurança.
    

### RT-04 — Preservação histórica

Registros de histórico, encerramento, auditoria, decisões e relatórios gerados não devem ser apagados por rotinas operacionais comuns.

### RT-05 — Integridade temporal

Campos de tempo devem respeitar coerência cronológica. Encerramento não pode anteceder abertura. Conclusão não pode anteceder início.

### RT-06 — Status controlado

Status devem ser validados contra domínio controlado do sistema. Texto livre para status é vedado.

### RT-07 — Dados mínimos para emissão documental

Nenhum relatório oficial deve ser emitido sem que os campos mínimos exigidos pelo template estejam preenchidos.

### RT-08 — Regra de completude mínima do plano

Uma versão não pode ser publicada como vigente sem conter, no mínimo:

- seções essenciais preenchidas;
    
- matriz de responsabilidades mínima;
    
- protocolo de ativação cadastrado;
    
- dados territoriais básicos associados.
    

### RT-09 — Regra de evento ativo

Somente eventos ativos ou em fase operacional compatível podem receber:

- novas ocorrências;
    
- novas missões;
    
- designações de comando;
    
- registros operacionais correntes.
    

### RT-10 — Regra de estoque

Saídas de estoque não podem gerar saldo negativo, salvo operação administrativa excepcional com permissão elevada e rastreio obrigatório.

---

## 4. Regras críticas do MVP

Estas regras devem ser tratadas como obrigatórias já na primeira versão funcional do sistema:

1. criação e segregação correta do tenant;
    
2. cadastro seguro de usuários e perfis;
    
3. cadastro territorial mínimo;
    
4. criação e versionamento do plano de contingência;
    
5. publicação controlada de versão vigente;
    
6. abertura formal de evento;
    
7. mudança de status com histórico;
    
8. ativação da estrutura de comando;
    
9. criação de objetivos, ocorrências e missões;
    
10. registro de danos e necessidades;
    
11. geração de relatório por evento;
    
12. auditoria das ações críticas.
    

---

## 5. Prioridade de implementação por backend

### Prioridade 1 — Núcleo obrigatório

- `TenantService`
    
- `UserManagementService`
    
- `RolePermissionService`
    
- `TerritoryService`
    
- `RiskAreaService`
    
- `ContingencyPlanService`
    
- `PlanVersioningService`
    
- `PlanPublishingService`
    
- `DisasterEventService`
    
- `EventStatusService`
    
- `CommandStructureService`
    
- `OperationalObjectiveService`
    
- `OperationalOccurrenceService`
    
- `MissionService`
    
- `DamageAssessmentService`
    
- `NeedsAssessmentService`
    
- `EventReportService`
    
- `AuditLogService`
    

### Prioridade 2 — Ampliação operacional

- `ShelterService`
    
- `ActiveShelterService`
    
- `HumanitarianInventoryService`
    
- `HumanitarianDistributionService`
    
- `PreparednessTaskService`
    
- `DrillService`
    
- `TrainingService`
    
- `OperationalDecisionService`
    

### Prioridade 3 — Maturidade institucional

- `RecoveryPlanService`
    
- `RecoveryActionService`
    
- `PostEventEvaluationService`
    
- `LessonLearnedService`
    
- `DisasterHistoryService`
    
- `AnalyticsSnapshotService`
    
- `DashboardCacheService`
    

---

## 6. Encadeamento com os próximos documentos

A partir desta matriz, os próximos documentos tecnicamente corretos são:

1. **Casos de Uso do Sistema (UML textual e formal)**;
    
2. **Especificação formal do MVP por módulo**;
    
3. **Plano de migrations e seeds iniciais**;
    
4. **Esqueleto real dos Services em PHP/Laravel**;
    
5. **Plano de testes funcionais e de regras de negócio**.
    

---

## 7. Conclusão técnica

Esta matriz transforma o sistema de uma estrutura documental estática em um fluxo de regras operacionais verificável.

Ela reduz ambiguidade entre tela, banco e backend, evita implementação arbitrária por módulo e estabelece claramente o que cada service deve garantir.

O próximo passo mais correto é a produção dos **Casos de Uso do Sistema**, porque eles transformarão essas regras em fluxos formais de interação entre atores e funcionalidades.