


---

## 1. Finalidade do documento

Este documento define a modelagem conceitual e a estrutura lógica do banco de dados do SaaS nacional de planejamento e gestão operacional de desastres.

O objetivo é transformar a arquitetura funcional do sistema em uma base de dados consistente, escalável, auditável e preparada para suportar:

- fase de normalidade;
    
- fase de desastre;
    
- recuperação pós-evento;
    
- histórico institucional;
    
- relatórios analíticos;
    
- operação SaaS multi-tenant.
    

---

## 2. Diretrizes da modelagem

### 2.1 Premissas estruturais

1. O banco será modelado para arquitetura **multi-tenant**.
    
2. Toda informação operacional sensível será vinculada a um **tenant_id**.
    
3. O modelo será orientado a histórico, e não apenas a cadastro.
    
4. Eventos relevantes deverão preservar rastreabilidade temporal.
    
5. O banco será preparado para evolução geoespacial.
    
6. O modelo prioriza consistência de domínio antes de conveniência de implementação.
    

### 2.2 Tecnologia recomendada

- **PostgreSQL** como banco principal.
    
- **PostGIS** para geometrias e recursos espaciais.
    

### 2.3 Convenções de modelagem

- nomes de tabelas no plural;
    
- chaves primárias em `id`;
    
- chaves estrangeiras no padrão `*_id`;
    
- campos temporais padrão: `created_at`, `updated_at`;
    
- campos de exclusão lógica quando necessário: `deleted_at`;
    
- status controlados por domínio de negócio;
    
- campos de auditoria quando aplicável: `created_by`, `updated_by`.
    

---

## 3. Estratégia multi-tenant

### 3.1 Abordagem adotada

O sistema utilizará **segregação lógica por tenant**, com banco central e filtragem obrigatória por `tenant_id`.

### 3.2 Justificativa

Essa estratégia permite:

- menor custo operacional no início;
    
- governança central da aplicação;
    
- escalabilidade gradual;
    
- facilidade de manutenção e deploy;
    
- analytics controlado por cliente.
    

### 3.3 Regra estrutural obrigatória

Toda tabela de domínio institucional ou operacional deve possuir `tenant_id`, salvo as tabelas globais de referência.

---

## 4. Macrodomínios do banco de dados

O banco será dividido em 10 macrodomínios:

1. núcleo SaaS e governança;
    
2. gestão institucional;
    
3. base territorial e risco;
    
4. plano de contingência;
    
5. preparação e prontidão;
    
6. eventos e ativação;
    
7. comando e coordenação;
    
8. operações, danos e assistência;
    
9. recuperação e memória institucional;
    
10. relatórios, auditoria e inteligência.
    

---

## 5. Tabelas globais de referência

Estas tabelas não pertencem a um cliente específico e servirão como domínio comum da plataforma.

### 5.1 `disaster_typologies`

Armazena as tipologias de desastre/evento reconhecidas pelo sistema.

**Campos principais:**

- id
    
- code
    
- name
    
- category
    
- description
    
- is_active
    
- created_at
    
- updated_at
    

### 5.2 `severity_levels`

Define níveis padronizados de severidade.

**Campos principais:**

- id
    
- code
    
- name
    
- weight
    
- color_hex
    
- sort_order
    
- is_active
    

### 5.3 `command_position_types`

Define tipos de posições funcionais do sistema de comando.

**Campos principais:**

- id
    
- code
    
- name
    
- functional_area
    
- description
    
- is_active
    

### 5.4 `report_templates`

Modelos globais de relatórios.

**Campos principais:**

- id
    
- code
    
- name
    
- module
    
- template_type
    
- version
    
- is_active
    
- created_at
    
- updated_at
    

### 5.5 `system_form_types`

Tipos de formulários operacionais suportados.

**Campos principais:**

- id
    
- code
    
- name
    
- category
    
- description
    
- is_active
    

---

## 6. Núcleo SaaS e governança

## 6.1 `tenants`

Representa o cliente contratante.

**Campos principais:**

- id
    
- uuid
    
- legal_name
    
- trade_name
    
- tenant_type
    
- document_number
    
- state_code
    
- city_name
    
- plan_type
    
- subscription_status
    
- contract_start_date
    
- contract_end_date
    
- is_active
    
- created_at
    
- updated_at
    

## 6.2 `tenant_settings`

Parâmetros institucionais e operacionais do tenant.

**Campos principais:**

- id
    
- tenant_id
    
- setting_key
    
- setting_value
    
- value_type
    
- created_at
    
- updated_at
    

## 6.3 `subscriptions`

Controle comercial da assinatura.

**Campos principais:**

- id
    
- tenant_id
    
- plan_name
    
- billing_cycle
    
- amount
    
- currency
    
- starts_at
    
- ends_at
    
- status
    
- created_at
    
- updated_at
    

## 6.4 `feature_flags`

Recursos habilitados por plano ou cliente.

**Campos principais:**

- id
    
- code
    
- name
    
- description
    
- is_active
    

## 6.5 `tenant_feature_flags`

Vincula funcionalidades ao tenant.

**Campos principais:**

- id
    
- tenant_id
    
- feature_flag_id
    
- is_enabled
    
- created_at
    
- updated_at
    

**Relacionamentos principais:**

- `tenants` 1:N `tenant_settings`
    
- `tenants` 1:N `subscriptions`
    
- `tenants` N:N `feature_flags` via `tenant_feature_flags`
    

---

## 7. Gestão institucional

## 7.1 `organizations`

Estrutura principal do órgão dentro do tenant.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- acronym
    
- organization_type
    
- address
    
- email
    
- phone
    
- coordinator_name
    
- is_active
    
- created_at
    
- updated_at
    

## 7.2 `organizational_units`

Unidades, setores ou divisões internas.

**Campos principais:**

- id
    
- tenant_id
    
- organization_id
    
- parent_unit_id
    
- name
    
- unit_type
    
- description
    
- is_active
    
- created_at
    
- updated_at
    

## 7.3 `users`

Usuários da aplicação.

**Campos principais:**

- id
    
- tenant_id
    
- organization_id
    
- unit_id
    
- name
    
- email
    
- cpf_hash
    
- password_hash
    
- phone
    
- position_name
    
- status
    
- last_login_at
    
- created_at
    
- updated_at
    
- deleted_at
    

## 7.4 `roles`

Perfis de acesso.

**Campos principais:**

- id
    
- tenant_id nullable
    
- code
    
- name
    
- description
    
- is_system_role
    
- created_at
    
- updated_at
    

## 7.5 `permissions`

Permissões detalhadas.

**Campos principais:**

- id
    
- code
    
- name
    
- module
    
- action
    
- description
    

## 7.6 `role_permissions`

Associação entre perfis e permissões.

**Campos principais:**

- id
    
- role_id
    
- permission_id
    

## 7.7 `user_roles`

Associação entre usuários e perfis.

**Campos principais:**

- id
    
- user_id
    
- role_id
    

## 7.8 `partner_agencies`

Órgãos parceiros e apoio intersetorial.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- acronym
    
- agency_type
    
- contact_name
    
- phone
    
- email
    
- address
    
- notes
    
- is_active
    

## 7.9 `strategic_contacts`

Contatos estratégicos de resposta.

**Campos principais:**

- id
    
- tenant_id
    
- partner_agency_id nullable
    
- organization_id nullable
    
- name
    
- role_name
    
- primary_phone
    
- secondary_phone
    
- email
    
- priority_level
    
- is_active
    

**Relacionamentos principais:**

- `organizations` 1:N `organizational_units`
    
- `users` N:N `roles`
    
- `roles` N:N `permissions`
    
- `partner_agencies` 1:N `strategic_contacts`
    

---

## 8. Base territorial e risco

## 8.1 `territories`

Cadastro do território principal do tenant.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- territory_type
    
- ibge_code nullable
    
- state_code
    
- description
    
- created_at
    
- updated_at
    

## 8.2 `territorial_units`

Subdivisões territoriais.

**Campos principais:**

- id
    
- tenant_id
    
- territory_id
    
- parent_unit_id nullable
    
- name
    
- unit_type
    
- code nullable
    
- population_estimate nullable
    
- created_at
    
- updated_at
    

## 8.3 `risk_areas`

Áreas de risco mapeadas.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id
    
- name
    
- risk_type
    
- priority_level
    
- exposed_population_estimate
    
- description
    
- monitoring_notes
    
- is_active
    
- created_at
    
- updated_at
    

## 8.4 `risk_area_geometries`

Geometrias associadas às áreas de risco.

**Campos principais:**

- id
    
- tenant_id
    
- risk_area_id
    
- geometry_type
    
- geometry_data
    
- source_type
    
- source_file_path nullable
    
- created_at
    
- updated_at
    

## 8.5 `risk_scenarios`

Cenários de desastre vinculados ao território.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id nullable
    
- disaster_typology_id
    
- name
    
- description
    
- trigger_conditions
    
- possible_impacts
    
- response_guidelines
    
- is_active
    
- created_at
    
- updated_at
    

## 8.6 `shelters`

Cadastro de abrigos potenciais.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id
    
- name
    
- shelter_type
    
- address
    
- manager_name
    
- contact_phone
    
- max_people_capacity
    
- accessibility_features
    
- kitchen_available
    
- water_supply_available
    
- energy_supply_available
    
- sanitary_structure_description
    
- latitude nullable
    
- longitude nullable
    
- is_active
    
- created_at
    
- updated_at
    

## 8.7 `shelter_capacities`

Detalhes estruturados de capacidade.

**Campos principais:**

- id
    
- tenant_id
    
- shelter_id
    
- capacity_type
    
- quantity
    
- notes
    
- created_at
    
- updated_at
    

## 8.8 `evacuation_routes`

Rotas de evacuação e deslocamento.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id
    
- name
    
- origin_description
    
- destination_description
    
- route_geometry nullable
    
- route_type
    
- is_active
    
- created_at
    
- updated_at
    

## 8.9 `support_points`

Pontos de apoio logístico ou comunitário.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id
    
- name
    
- point_type
    
- address
    
- contact_name
    
- contact_phone
    
- latitude nullable
    
- longitude nullable
    
- is_active
    

## 8.10 `operational_bases`

Bases operacionais estratégicas.

**Campos principais:**

- id
    
- tenant_id
    
- territorial_unit_id
    
- name
    
- base_type
    
- address
    
- manager_name
    
- contact_phone
    
- structure_description
    
- is_active
    

**Relacionamentos principais:**

- `territories` 1:N `territorial_units`
    
- `territorial_units` 1:N `risk_areas`
    
- `risk_areas` 1:1/N `risk_area_geometries`
    
- `territorial_units` 1:N `shelters`
    
- `territorial_units` 1:N `evacuation_routes`
    
- `territorial_units` 1:N `support_points`
    
- `territorial_units` 1:N `operational_bases`
    

---

## 9. Plano de contingência

## 9.1 `contingency_plans`

Documento-mãe do plano.

**Campos principais:**

- id
    
- tenant_id
    
- territory_id
    
- title
    
- plan_scope
    
- status
    
- current_version_id nullable
    
- validity_start_date nullable
    
- validity_end_date nullable
    
- revision_frequency_months nullable
    
- created_by
    
- updated_by
    
- created_at
    
- updated_at
    

## 9.2 `contingency_plan_versions`

Versões do plano.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_id
    
- version_number
    
- version_status
    
- published_at nullable
    
- approved_at nullable
    
- approved_by nullable
    
- created_by
    
- notes
    
- created_at
    
- updated_at
    

## 9.3 `contingency_plan_sections`

Seções do plano por versão.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_version_id
    
- section_code
    
- section_title
    
- section_order
    
- content_text
    
- created_at
    
- updated_at
    

## 9.4 `contingency_plan_section_items`

Itens estruturados dentro da seção.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_section_id
    
- item_type
    
- item_key
    
- item_value
    
- item_order
    
- created_at
    
- updated_at
    

## 9.5 `responsibility_matrix`

Matriz de responsabilidades do plano.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_version_id
    
- action_name
    
- primary_responsible_type
    
- primary_responsible_id
    
- support_responsible_type nullable
    
- support_responsible_id nullable
    
- priority_level
    
- notes
    
- created_at
    
- updated_at
    

## 9.6 `activation_protocols`

Protocolos de acionamento.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_version_id
    
- name
    
- trigger_description
    
- activation_steps
    
- communication_flow
    
- required_roles
    
- created_at
    
- updated_at
    

## 9.7 `contingency_plan_attachments`

Anexos vinculados ao plano.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_version_id
    
- file_name
    
- file_path
    
- file_type
    
- attachment_category
    
- uploaded_by
    
- created_at
    

## 9.8 `contingency_plan_publications`

Histórico de publicação.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_version_id
    
- publication_type
    
- published_by
    
- published_at
    
- notes
    

**Relacionamentos principais:**

- `contingency_plans` 1:N `contingency_plan_versions`
    
- `contingency_plan_versions` 1:N `contingency_plan_sections`
    
- `contingency_plan_sections` 1:N `contingency_plan_section_items`
    
- `contingency_plan_versions` 1:N `responsibility_matrix`
    
- `contingency_plan_versions` 1:N `activation_protocols`
    
- `contingency_plan_versions` 1:N `contingency_plan_attachments`
    

---

## 10. Preparação e prontidão

## 10.1 `drills`

Simulados programados ou executados.

**Campos principais:**

- id
    
- tenant_id
    
- contingency_plan_id nullable
    
- title
    
- drill_type
    
- objective
    
- scheduled_at
    
- executed_at nullable
    
- status
    
- location_description
    
- summary_result nullable
    
- created_by
    
- created_at
    
- updated_at
    

## 10.2 `drill_participants`

Participantes do simulado.

**Campos principais:**

- id
    
- tenant_id
    
- drill_id
    
- participant_type
    
- user_id nullable
    
- external_name nullable
    
- role_name
    
- attendance_status
    

## 10.3 `trainings`

Capacitações e treinamentos.

**Campos principais:**

- id
    
- tenant_id
    
- title
    
- training_type
    
- instructor_name
    
- workload_hours
    
- held_at
    
- status
    
- notes
    
- created_at
    
- updated_at
    

## 10.4 `training_attendees`

Participantes de treinamento.

**Campos principais:**

- id
    
- tenant_id
    
- training_id
    
- user_id nullable
    
- external_name nullable
    
- attendance_status
    
- certificate_issued
    

## 10.5 `readiness_checklists`

Checklist geral de prontidão.

**Campos principais:**

- id
    
- tenant_id
    
- title
    
- checklist_scope
    
- status
    
- reference_date
    
- created_by
    
- created_at
    
- updated_at
    

## 10.6 `readiness_checklist_items`

Itens de prontidão.

**Campos principais:**

- id
    
- tenant_id
    
- readiness_checklist_id
    
- item_name
    
- item_description
    
- item_status
    
- assigned_to nullable
    
- due_date nullable
    
- completed_at nullable
    
- notes nullable
    

## 10.7 `preparedness_tasks`

Pendências e ações preparatórias.

**Campos principais:**

- id
    
- tenant_id
    
- title
    
- task_type
    
- related_checklist_item_id nullable
    
- assigned_to nullable
    
- priority_level
    
- due_date nullable
    
- status
    
- notes
    
- created_at
    
- updated_at
    

## 10.8 `simulation_lessons`

Lições aprendidas em simulados.

**Campos principais:**

- id
    
- tenant_id
    
- drill_id
    
- category
    
- description
    
- recommended_action
    
- responsible_user_id nullable
    
- status
    
- created_at
    
- updated_at
    

---

## 11. Eventos e ativação

## 11.1 `disaster_events`

Registro central de cada evento/desastre.

**Campos principais:**

- id
    
- tenant_id
    
- territory_id
    
- territorial_unit_id nullable
    
- event_code
    
- title
    
- disaster_typology_id
    
- severity_level_id
    
- contingency_plan_version_id nullable
    
- risk_scenario_id nullable
    
- event_status
    
- operational_phase
    
- started_at
    
- ended_at nullable
    
- summary_description
    
- created_by
    
- created_at
    
- updated_at
    

## 11.2 `disaster_event_classifications`

Classificações formais do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- classification_type
    
- classification_value
    
- classified_by
    
- classified_at
    
- notes
    

## 11.3 `disaster_event_status_history`

Histórico de status do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- previous_status nullable
    
- new_status
    
- changed_by
    
- changed_at
    
- notes
    

## 11.4 `disaster_event_timeline`

Marcos temporais do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- timeline_type
    
- title
    
- description
    
- occurred_at
    
- created_by
    
- created_at
    

## 11.5 `disaster_event_attachments`

Anexos do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- file_name
    
- file_path
    
- file_type
    
- attachment_category
    
- uploaded_by
    
- created_at
    

## 11.6 `disaster_event_closures`

Encerramento formal do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- closure_reason
    
- closure_summary
    
- closed_by
    
- closed_at
    
- final_report_id nullable
    

**Relacionamentos principais:**

- `disaster_events` 1:N `disaster_event_classifications`
    
- `disaster_events` 1:N `disaster_event_status_history`
    
- `disaster_events` 1:N `disaster_event_timeline`
    
- `disaster_events` 1:N `disaster_event_attachments`
    
- `disaster_events` 1:1 `disaster_event_closures`
    

---

## 12. Comando e coordenação operacional

## 12.1 `command_structures`

Estrutura de comando ativa por evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- structure_name
    
- command_model
    
- activated_at
    
- deactivated_at nullable
    
- status
    
- created_by
    
- created_at
    
- updated_at
    

## 12.2 `command_positions`

Posições ativadas na estrutura.

**Campos principais:**

- id
    
- tenant_id
    
- command_structure_id
    
- command_position_type_id
    
- custom_name nullable
    
- status
    
- activated_at
    
- deactivated_at nullable
    

## 12.3 `command_assignments`

Vincula pessoas às funções de comando.

**Campos principais:**

- id
    
- tenant_id
    
- command_position_id
    
- user_id nullable
    
- external_person_name nullable
    
- assigned_at
    
- unassigned_at nullable
    
- notes
    

## 12.4 `operational_objectives`

Objetivos operacionais por evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- objective_code
    
- title
    
- description
    
- priority_level
    
- status
    
- defined_by
    
- defined_at
    
- expected_completion_at nullable
    

## 12.5 `situation_meetings`

Reuniões de situação.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- meeting_number
    
- held_at
    
- chaired_by
    
- summary
    
- next_actions
    
- created_at
    
- updated_at
    

## 12.6 `command_forms`

Formulários operacionais produzidos.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- system_form_type_id
    
- reference_code
    
- title
    
- status
    
- created_by
    
- created_at
    
- updated_at
    

## 12.7 `command_form_entries`

Campos/itens do formulário.

**Campos principais:**

- id
    
- tenant_id
    
- command_form_id
    
- field_key
    
- field_label
    
- field_value
    
- field_order
    
- created_at
    
- updated_at
    

## 12.8 `operational_decisions`

Decisões registradas no contexto do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- situation_meeting_id nullable
    
- decision_title
    
- decision_description
    
- decided_by
    
- decided_at
    
- implementation_status
    
- notes
    

## 12.9 `command_transfers`

Transferência de comando.

**Campos principais:**

- id
    
- tenant_id
    
- command_structure_id
    
- from_assignment_id nullable
    
- to_assignment_id nullable
    
- transferred_at
    
- reason
    
- notes
    

---

## 13. Operações, danos e assistência

## 13.1 `field_teams`

Equipes operacionais disponíveis.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- team_type
    
- leader_user_id nullable
    
- contact_phone nullable
    
- availability_status
    
- created_at
    
- updated_at
    

## 13.2 `operational_resources`

Recursos mobilizáveis.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- resource_type
    
- identifier_code nullable
    
- ownership_type
    
- current_status
    
- location_description nullable
    
- notes
    
- created_at
    
- updated_at
    

## 13.3 `operational_occurrences`

Ocorrências registradas no evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- territorial_unit_id nullable
    
- occurrence_code
    
- occurrence_type
    
- title
    
- description
    
- severity_level_id nullable
    
- address nullable
    
- latitude nullable
    
- longitude nullable
    
- status
    
- opened_at
    
- closed_at nullable
    
- created_by
    

## 13.4 `missions`

Missões decorrentes do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- operational_occurrence_id nullable
    
- operational_objective_id nullable
    
- mission_code
    
- title
    
- description
    
- priority_level
    
- status
    
- assigned_at nullable
    
- started_at nullable
    
- completed_at nullable
    
- created_by
    
- created_at
    
- updated_at
    

## 13.5 `mission_assignments`

Vincula equipes e recursos às missões.

**Campos principais:**

- id
    
- tenant_id
    
- mission_id
    
- field_team_id nullable
    
- operational_resource_id nullable
    
- assigned_by
    
- assigned_at
    
- notes
    

## 13.6 `resource_allocations`

Alocação formal de recursos.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- operational_resource_id
    
- allocation_type
    
- mission_id nullable
    
- allocated_at
    
- released_at nullable
    
- allocated_by
    
- notes
    

## 13.7 `damage_assessments`

Avaliação de danos.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- territorial_unit_id nullable
    
- assessment_type
    
- people_affected nullable
    
- homeless_count nullable
    
- displaced_count nullable
    
- injured_count nullable
    
- deceased_count nullable
    
- public_damage_description nullable
    
- private_damage_description nullable
    
- infrastructure_damage_description nullable
    
- environmental_damage_description nullable
    
- assessed_at
    
- assessed_by
    
- notes
    

## 13.8 `needs_assessments`

Avaliação de necessidades.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- territorial_unit_id nullable
    
- need_category
    
- requested_quantity nullable
    
- requested_unit nullable
    
- priority_level
    
- status
    
- assessed_at
    
- assessed_by
    
- notes
    

## 13.9 `operational_logs`

Diário operacional.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- log_type
    
- title
    
- description
    
- logged_at
    
- logged_by
    
- related_entity_type nullable
    
- related_entity_id nullable
    

---

## 14. Abrigos e ajuda humanitária

## 14.1 `active_shelters`

Abrigos ativados em um evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- shelter_id
    
- activated_at
    
- deactivated_at nullable
    
- current_status
    
- current_people_count default 0
    
- current_family_count default 0
    
- manager_name nullable
    
- notes
    

## 14.2 `assisted_families`

Famílias assistidas.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- active_shelter_id nullable
    
- family_reference_code
    
- responsible_name
    
- contact_phone nullable
    
- origin_location nullable
    
- registration_date
    
- current_status
    
- notes
    

## 14.3 `assisted_people`

Pessoas assistidas.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- assisted_family_id nullable
    
- active_shelter_id nullable
    
- full_name
    
- document_number nullable
    
- birth_date nullable
    
- gender nullable
    
- special_condition nullable
    
- is_child default false
    
- is_elderly default false
    
- notes
    

## 14.4 `humanitarian_stock_items`

Itens controlados no estoque.

**Campos principais:**

- id
    
- tenant_id
    
- name
    
- category
    
- unit_of_measure
    
- current_balance
    
- minimum_balance nullable
    
- is_active
    
- created_at
    
- updated_at
    

## 14.5 `humanitarian_stock_movements`

Entradas e saídas de estoque.

**Campos principais:**

- id
    
- tenant_id
    
- humanitarian_stock_item_id
    
- disaster_event_id nullable
    
- movement_type
    
- quantity
    
- movement_date
    
- source_description nullable
    
- destination_description nullable
    
- recorded_by
    
- notes
    

## 14.6 `humanitarian_distributions`

Distribuição de ajuda humanitária.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- assisted_family_id nullable
    
- assisted_person_id nullable
    
- distribution_date
    
- delivered_by
    
- receipt_type
    
- notes
    

## 14.7 `humanitarian_distribution_items`

Itens entregues por distribuição.

**Campos principais:**

- id
    
- tenant_id
    
- humanitarian_distribution_id
    
- humanitarian_stock_item_id
    
- quantity
    
- unit_of_measure
    

## 14.8 `shelter_operation_reports`

Relatórios operacionais de abrigo.

**Campos principais:**

- id
    
- tenant_id
    
- active_shelter_id
    
- report_date
    
- current_people_count
    
- current_family_count
    
- stock_summary nullable
    
- critical_needs nullable
    
- generated_by
    
- created_at
    

---

## 15. Recuperação e memória institucional

## 15.1 `recovery_plans`

Plano de recuperação vinculado ao evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- title
    
- status
    
- started_at
    
- expected_end_at nullable
    
- created_by
    
- created_at
    
- updated_at
    

## 15.2 `recovery_actions`

Ações de recuperação.

**Campos principais:**

- id
    
- tenant_id
    
- recovery_plan_id
    
- action_axis
    
- title
    
- description
    
- responsible_type
    
- responsible_id nullable
    
- priority_level
    
- status
    
- start_date nullable
    
- end_date nullable
    
- created_at
    
- updated_at
    

## 15.3 `recovery_action_updates`

Atualizações de andamento.

**Campos principais:**

- id
    
- tenant_id
    
- recovery_action_id
    
- update_date
    
- progress_percentage nullable
    
- update_description
    
- updated_by
    

## 15.4 `post_event_evaluations`

Avaliação pós-evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- evaluation_date
    
- strengths nullable
    
- weaknesses nullable
    
- opportunities nullable
    
- threats nullable
    
- recommendations nullable
    
- created_by
    
- created_at
    

## 15.5 `lessons_learned`

Lições aprendidas.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id nullable
    
- source_type
    
- source_id nullable
    
- category
    
- description
    
- recommendation
    
- status
    
- created_by
    
- created_at
    
- updated_at
    

## 15.6 `historical_disaster_records`

Consolidação histórica institucional do evento.

**Campos principais:**

- id
    
- tenant_id
    
- disaster_event_id
    
- record_year
    
- disaster_typology_id
    
- severity_level_id nullable
    
- territory_summary
    
- people_affected nullable
    
- total_damages_summary nullable
    
- response_summary nullable
    
- recovery_summary nullable
    
- closed_at nullable
    
- created_at
    
- updated_at
    

---

## 16. Relatórios, inteligência e auditoria

## 16.1 `generated_reports`

Relatórios emitidos pelo sistema.

**Campos principais:**

- id
    
- tenant_id
    
- report_template_id
    
- related_entity_type nullable
    
- related_entity_id nullable
    
- title
    
- file_path nullable
    
- generated_by
    
- generated_at
    
- generation_status
    

## 16.2 `analytical_snapshots`

Snapshots analíticos consolidados.

**Campos principais:**

- id
    
- tenant_id
    
- snapshot_type
    
- reference_date
    
- data_payload
    
- generated_at
    

## 16.3 `dashboard_cache`

Cache de indicadores pré-processados.

**Campos principais:**

- id
    
- tenant_id
    
- dashboard_type
    
- cache_key
    
- cache_payload
    
- expires_at
    
- created_at
    

## 16.4 `audit_logs`

Trilha de auditoria do sistema.

**Campos principais:**

- id
    
- tenant_id nullable
    
- user_id nullable
    
- event_type
    
- module
    
- action
    
- entity_type nullable
    
- entity_id nullable
    
- old_values nullable
    
- new_values nullable
    
- ip_address nullable
    
- user_agent nullable
    
- created_at
    

## 16.5 `active_sessions`

Sessões ativas para segurança.

**Campos principais:**

- id
    
- tenant_id
    
- user_id
    
- session_token_hash
    
- ip_address nullable
    
- user_agent nullable
    
- last_activity_at
    
- expires_at
    
- created_at
    

## 16.6 `security_policies`

Políticas de segurança por tenant.

**Campos principais:**

- id
    
- tenant_id
    
- password_policy_json nullable
    
- session_timeout_minutes
    
- mfa_required
    
- allow_external_users
    
- created_at
    
- updated_at
    

---

## 17. Relacionamentos críticos do modelo

### 17.1 Núcleo estrutural

- `tenants` 1:N `organizations`
    
- `tenants` 1:N `users`
    
- `tenants` 1:N `territories`
    
- `tenants` 1:N `contingency_plans`
    
- `tenants` 1:N `disaster_events`
    

### 17.2 Planejamento

- `contingency_plans` 1:N `contingency_plan_versions`
    
- `contingency_plan_versions` 1:N `contingency_plan_sections`
    
- `contingency_plan_versions` 1:N `responsibility_matrix`
    
- `contingency_plan_versions` 1:N `activation_protocols`
    

### 17.3 Operação

- `disaster_events` 1:N `command_structures`
    
- `disaster_events` 1:N `operational_objectives`
    
- `disaster_events` 1:N `operational_occurrences`
    
- `disaster_events` 1:N `missions`
    
- `disaster_events` 1:N `damage_assessments`
    
- `disaster_events` 1:N `needs_assessments`
    
- `disaster_events` 1:N `active_shelters`
    
- `disaster_events` 1:N `recovery_plans`
    

### 17.4 Abrigos e assistência

- `shelters` 1:N `active_shelters`
    
- `active_shelters` 1:N `assisted_families`
    
- `assisted_families` 1:N `assisted_people`
    
- `humanitarian_distributions` 1:N `humanitarian_distribution_items`
    

### 17.5 Memória institucional

- `disaster_events` 1:1 `disaster_event_closures`
    
- `disaster_events` 1:1/N `historical_disaster_records`
    
- `disaster_events` 1:N `generated_reports`
    

---

## 18. Cardinalidades estratégicas

### 18.1 Tenant

Um tenant possui muitos usuários, planos, eventos, relatórios e registros operacionais.

### 18.2 Plano

Um plano possui muitas versões. Apenas uma versão poderá ser vigente por escopo e período.

### 18.3 Evento

Um evento pode possuir múltiplas classificações, múltiplos objetivos, múltiplas missões, múltiplas ocorrências e múltiplos registros de danos/necessidades.

### 18.4 Estrutura de comando

Um evento pode ativar uma ou mais estruturas de comando ao longo do tempo, mas apenas uma principal deve estar ativa por vez.

### 18.5 Abrigo

Um abrigo pode ser ativado em vários eventos distintos ao longo do histórico.

### 18.6 Estoque

Um item de estoque possui muitas movimentações e pode aparecer em múltiplas distribuições.

---

## 19. Regras de banco de dados críticas

### RBD-01

Nenhum registro operacional deve existir sem `tenant_id` válido.

### RBD-02

Todo evento deve estar vinculado a uma tipologia de desastre.

### RBD-03

Toda alteração crítica de status deve gerar histórico.

### RBD-04

Um plano pode ter muitas versões, mas somente uma versão publicada vigente.

### RBD-05

Uma missão pode existir sem ocorrência associada, mas deve estar sempre vinculada ao evento.

### RBD-06

Uma distribuição de ajuda deve possuir pelo menos um item vinculado.

### RBD-07

Um encerramento de evento só pode ocorrer para evento previamente aberto.

### RBD-08

Registros de auditoria não devem ser excluídos por usuários comuns.

### RBD-09

Campos de geolocalização devem aceitar evolução futura para geometria espacial nativa.

### RBD-10

O histórico consolidado do desastre deve permanecer preservado mesmo após alterações posteriores em cadastros auxiliares.

---

## 20. Índices recomendados

### Índices essenciais

- índices em `tenant_id` para todas as tabelas multi-tenant;
    
- índices compostos em `tenant_id + status`;
    
- índices compostos em `tenant_id + created_at`;
    
- índices em `disaster_event_id` para tabelas operacionais;
    
- índices em `territorial_unit_id` para consultas territoriais;
    
- índices em `disaster_typology_id` e `severity_level_id` para analytics;
    
- índice único em `event_code` por tenant;
    
- índice único em `version_number` por plano;
    
- índice espacial futuro para geometrias.
    

---

## 21. Estratégia de histórico e auditoria

### 21.1 Histórico operacional

Status, decisões, timeline, movimentações e atualizações devem ser gravados em tabelas históricas ou de log próprio.

### 21.2 Auditoria sistêmica

Alterações críticas em:

- usuários;
    
- permissões;
    
- planos;
    
- eventos;
    
- relatórios;
    
- configurações;
    
- decisões operacionais.
    

Devem gerar registros em `audit_logs`.

---

## 22. Estratégia de exclusão

### 22.1 Exclusão lógica

Aplicável a cadastros e entidades administrativas.

### 22.2 Exclusão física restrita

Permitida apenas para artefatos transitórios ou registros de teste controlado.

### 22.3 Registros imutáveis

Não devem ser fisicamente apagados em operação normal:

- auditoria;
    
- histórico de status;
    
- decisões;
    
- relatórios gerados;
    
- encerramentos de evento.
    

---

## 23. Modelo conceitual resumido por blocos

### Bloco A — Plataforma

`tenants` → `subscriptions` → `tenant_feature_flags`

### Bloco B — Institucional

`organizations` → `organizational_units` → `users` → `user_roles`

### Bloco C — Território e risco

`territories` → `territorial_units` → `risk_areas` / `shelters` / `support_points` / `operational_bases`

### Bloco D — Planejamento

`contingency_plans` → `contingency_plan_versions` → `sections` / `responsibility_matrix` / `activation_protocols`

### Bloco E — Evento

`disaster_events` → `classifications` / `timeline` / `status_history` / `attachments`

### Bloco F — Comando

`disaster_events` → `command_structures` → `command_positions` → `command_assignments`

### Bloco G — Operação

`disaster_events` → `operational_objectives` / `missions` / `operational_occurrences` / `damage_assessments` / `needs_assessments`

### Bloco H — Assistência

`active_shelters` → `assisted_families` → `assisted_people`

### Bloco I — Recuperação

`recovery_plans` → `recovery_actions` → `recovery_action_updates`

### Bloco J — Inteligência

`generated_reports` / `historical_disaster_records` / `analytical_snapshots` / `audit_logs`

---

## 24. Núcleo mínimo do MVP no banco

Para o primeiro ciclo comercial, o banco pode iniciar com as seguintes tabelas obrigatórias:

### Núcleo SaaS

- tenants
    
- tenant_settings
    
- users
    
- roles
    
- permissions
    
- user_roles
    
- role_permissions
    

### Núcleo territorial

- territories
    
- territorial_units
    
- risk_areas
    
- shelters
    
- support_points
    
- operational_bases
    

### Núcleo de planejamento

- contingency_plans
    
- contingency_plan_versions
    
- contingency_plan_sections
    
- responsibility_matrix
    
- activation_protocols
    

### Núcleo operacional

- disaster_events
    
- disaster_event_status_history
    
- disaster_event_timeline
    
- command_structures
    
- operational_objectives
    
- operational_occurrences
    
- missions
    
- damage_assessments
    
- needs_assessments
    

### Núcleo analítico

- generated_reports
    
- audit_logs
    

---

## 25. Decisões estratégicas recomendadas

### Decisão 1

Modelar o banco para histórico desde o início, mesmo que nem todos os relatórios sejam entregues na primeira versão.

### Decisão 2

Separar claramente planejamento, evento e recuperação, evitando tabela única genérica para tudo.

### Decisão 3

Não acoplar o modelo a um único manual específico; o banco deve suportar adaptação operacional controlada.

### Decisão 4

Tratar dados espaciais como parte estrutural da solução, não como acessório futuro.

### Decisão 5

Evitar customizações excessivas por cliente no nível do banco.

---

## 26. Próximos documentos derivados desta modelagem

A partir desta modelagem, os próximos documentos corretos são:

1. dicionário de dados completo por tabela e campo;
    
2. modelo físico com tipos de dados SQL;
    
3. matriz de regras de negócio por módulo;
    
4. casos de uso;
    
5. schema inicial do banco;
    
6. plano de migrações.
    

---

## 27. Conclusão técnica

Esta modelagem foi desenhada para sustentar um SaaS nacional especializado, cobrindo planejamento, operação, assistência, recuperação e inteligência institucional sem perder escalabilidade.

O banco não foi pensado apenas para armazenar formulários, mas para preservar contexto, histórico, decisão, territorialidade e memória institucional.

O próximo passo mais correto é elaborar o **Dicionário de Dados Completo**, pois ele vai detalhar tecnicamente cada tabela, campo, tipo lógico, obrigatoriedade, validações e observações de uso para desenvolvimento e documentação formal.