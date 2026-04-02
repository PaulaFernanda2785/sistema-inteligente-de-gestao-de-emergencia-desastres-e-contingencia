
---

## 1. Finalidade do documento

Este documento detalha o dicionário de dados do sistema, especificando a função de cada tabela, seus campos, tipo lógico, obrigatoriedade, chaves, regras de preenchimento e observações técnicas.

Este material servirá como base para:

- modelagem física do banco;
    
- criação de migrations;
    
- validações de backend;
    
- regras de integridade;
    
- documentação técnica formal do projeto.
    

---

## 2. Convenções adotadas

### 2.1 Tipos lógicos utilizados

- **UUID**: identificador universal quando aplicável.
    
- **PK**: chave primária.
    
- **FK**: chave estrangeira.
    
- **String curta**: texto curto controlado.
    
- **String longa**: texto extenso.
    
- **Booleano**: verdadeiro/falso.
    
- **Inteiro**: número inteiro.
    
- **Decimal**: número com casas decimais.
    
- **Data**: somente data.
    
- **DataHora**: data e hora.
    
- **JSON**: estrutura flexível controlada.
    
- **Geom**: campo geoespacial preparado para PostGIS.
    

### 2.2 Obrigatoriedade

- **Obrigatório**: campo indispensável.
    
- **Opcional**: pode permanecer nulo.
    
- **Condicional**: obrigatório dependendo do contexto de negócio.
    

### 2.3 Campos padrão de auditoria

Sempre que aplicável:

- `created_at`
    
- `updated_at`
    
- `deleted_at`
    
- `created_by`
    
- `updated_by`
    

---

# BLOCO A — TABELAS GLOBAIS DE REFERÊNCIA

## 3. Tabela: `disaster_typologies`

### Finalidade

Armazenar as tipologias de desastre ou evento utilizadas pelo sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador interno|
|code|String curta|Obrigatório, único|Código interno da tipologia|
|name|String curta|Obrigatório|Nome da tipologia|
|category|String curta|Obrigatório|Categoria macro|
|description|String longa|Opcional|Descrição detalhada|
|is_active|Booleano|Obrigatório|Indica se a tipologia está ativa|
|created_at|DataHora|Obrigatório|Data de criação|
|updated_at|DataHora|Obrigatório|Data da última atualização|

**Observações:**

- Não depende de tenant.
    
- Deve servir como domínio central para eventos e cenários de risco.
    

---

## 4. Tabela: `severity_levels`

### Finalidade

Padronizar níveis de severidade usados no sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador interno|
|code|String curta|Obrigatório, único|Código do nível|
|name|String curta|Obrigatório|Nome do nível|
|weight|Inteiro|Obrigatório|Peso para analytics|
|color_hex|String curta|Obrigatório|Cor associada|
|sort_order|Inteiro|Obrigatório|Ordem de exibição|
|is_active|Booleano|Obrigatório|Status ativo/inativo|

**Observações:**

- Utilizada em eventos, ocorrências e análises.
    

---

## 5. Tabela: `command_position_types`

### Finalidade

Padronizar os tipos de funções do sistema de comando.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador interno|
|code|String curta|Obrigatório, único|Código da função|
|name|String curta|Obrigatório|Nome da posição funcional|
|functional_area|String curta|Obrigatório|Área funcional|
|description|String longa|Opcional|Detalhamento|
|is_active|Booleano|Obrigatório|Situação ativa|

---

## 6. Tabela: `report_templates`

### Finalidade

Armazenar modelos globais de relatório.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|code|String curta|Obrigatório, único|Código do template|
|name|String curta|Obrigatório|Nome do template|
|module|String curta|Obrigatório|Módulo relacionado|
|template_type|String curta|Obrigatório|Tipo do relatório|
|version|String curta|Obrigatório|Versão do template|
|is_active|Booleano|Obrigatório|Indica uso ativo|
|created_at|DataHora|Obrigatório|Data de criação|
|updated_at|DataHora|Obrigatório|Data de atualização|

---

## 7. Tabela: `system_form_types`

### Finalidade

Armazenar os tipos de formulários operacionais disponíveis.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|code|String curta|Obrigatório, único|Código do formulário|
|name|String curta|Obrigatório|Nome do formulário|
|category|String curta|Obrigatório|Categoria operacional|
|description|String longa|Opcional|Descrição|
|is_active|Booleano|Obrigatório|Situação ativa|

---

# BLOCO B — NÚCLEO SAAS E GOVERNANÇA

## 8. Tabela: `tenants`

### Finalidade

Representar cada cliente contratante da plataforma.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador interno|
|uuid|UUID|Obrigatório, único|Identificador externo seguro|
|legal_name|String curta|Obrigatório|Razão social/nome oficial|
|trade_name|String curta|Opcional|Nome fantasia/institucional|
|tenant_type|String curta|Obrigatório|Tipo do contratante|
|document_number|String curta|Obrigatório|CNPJ/identificador oficial|
|state_code|String curta|Obrigatório|UF principal|
|city_name|String curta|Opcional|Cidade-base|
|plan_type|String curta|Obrigatório|Plano contratado|
|subscription_status|String curta|Obrigatório|Status da assinatura|
|contract_start_date|Data|Opcional|Início contratual|
|contract_end_date|Data|Opcional|Fim contratual|
|is_active|Booleano|Obrigatório|Tenant ativo/inativo|
|created_at|DataHora|Obrigatório|Data de criação|
|updated_at|DataHora|Obrigatório|Data de atualização|

**Observações:**

- Toda tabela de domínio do cliente deve se vincular a `tenant_id`.
    

---

## 9. Tabela: `tenant_settings`

### Finalidade

Guardar parâmetros configuráveis por cliente.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Referência ao tenant|
|setting_key|String curta|Obrigatório|Chave da configuração|
|setting_value|JSON/String longa|Obrigatório|Valor configurado|
|value_type|String curta|Obrigatório|Tipo lógico do valor|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

**Observações:**

- Índice composto recomendado em `tenant_id + setting_key`.
    

---

## 10. Tabela: `subscriptions`

### Finalidade

Controlar o vínculo comercial da assinatura.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|plan_name|String curta|Obrigatório|Nome do plano|
|billing_cycle|String curta|Obrigatório|Ciclo de cobrança|
|amount|Decimal|Obrigatório|Valor da assinatura|
|currency|String curta|Obrigatório|Moeda|
|starts_at|Data|Obrigatório|Início da assinatura|
|ends_at|Data|Opcional|Fim da vigência|
|status|String curta|Obrigatório|Situação da assinatura|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 11. Tabela: `feature_flags`

### Finalidade

Cadastrar funcionalidades habilitáveis da plataforma.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|code|String curta|Obrigatório, único|Código da funcionalidade|
|name|String curta|Obrigatório|Nome|
|description|String longa|Opcional|Descrição|
|is_active|Booleano|Obrigatório|Situação|

---

## 12. Tabela: `tenant_feature_flags`

### Finalidade

Vincular funcionalidades liberadas para cada tenant.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|feature_flag_id|FK|Obrigatório|Funcionalidade|
|is_enabled|Booleano|Obrigatório|Habilitada ou não|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

# BLOCO C — GESTÃO INSTITUCIONAL

## 13. Tabela: `organizations`

### Finalidade

Registrar a estrutura institucional principal do órgão no tenant.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome da organização|
|acronym|String curta|Opcional|Sigla|
|organization_type|String curta|Obrigatório|Tipo do órgão|
|address|String longa|Opcional|Endereço|
|email|String curta|Opcional|E-mail institucional|
|phone|String curta|Opcional|Telefone|
|coordinator_name|String curta|Opcional|Nome do coordenador|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 14. Tabela: `organizational_units`

### Finalidade

Representar setores, divisões e unidades internas.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|organization_id|FK|Obrigatório|Organização principal|
|parent_unit_id|FK|Opcional|Unidade pai|
|name|String curta|Obrigatório|Nome da unidade|
|unit_type|String curta|Obrigatório|Tipo da unidade|
|description|String longa|Opcional|Descrição|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 15. Tabela: `users`

### Finalidade

Cadastrar usuários do sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|organization_id|FK|Obrigatório|Organização|
|unit_id|FK|Opcional|Unidade organizacional|
|name|String curta|Obrigatório|Nome completo|
|email|String curta|Obrigatório, único por tenant|E-mail do usuário|
|cpf_hash|String curta|Opcional|Hash do CPF|
|password_hash|String longa|Obrigatório|Senha criptografada|
|phone|String curta|Opcional|Telefone|
|position_name|String curta|Opcional|Cargo/função|
|status|String curta|Obrigatório|Situação do usuário|
|last_login_at|DataHora|Opcional|Último acesso|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|
|deleted_at|DataHora|Opcional|Exclusão lógica|

**Observações:**

- CPF deve ser armazenado com proteção adequada; evitar texto puro.
    

---

## 16. Tabela: `roles`

### Finalidade

Cadastrar perfis de acesso.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Opcional|Nulo quando perfil for global do sistema|
|code|String curta|Obrigatório|Código do perfil|
|name|String curta|Obrigatório|Nome do perfil|
|description|String longa|Opcional|Descrição|
|is_system_role|Booleano|Obrigatório|Indica perfil padrão do sistema|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 17. Tabela: `permissions`

### Finalidade

Registrar permissões detalhadas por módulo e ação.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|code|String curta|Obrigatório, único|Código da permissão|
|name|String curta|Obrigatório|Nome da permissão|
|module|String curta|Obrigatório|Módulo relacionado|
|action|String curta|Obrigatório|Ação permitida|
|description|String longa|Opcional|Descrição|

---

## 18. Tabela: `role_permissions`

### Finalidade

Vincular permissões aos perfis.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|role_id|FK|Obrigatório|Perfil|
|permission_id|FK|Obrigatório|Permissão|

---

## 19. Tabela: `user_roles`

### Finalidade

Vincular perfis a usuários.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|user_id|FK|Obrigatório|Usuário|
|role_id|FK|Obrigatório|Perfil|

---

## 20. Tabela: `partner_agencies`

### Finalidade

Cadastrar órgãos parceiros ou apoiadores.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome do órgão|
|acronym|String curta|Opcional|Sigla|
|agency_type|String curta|Obrigatório|Tipo do órgão parceiro|
|contact_name|String curta|Opcional|Responsável principal|
|phone|String curta|Opcional|Telefone|
|email|String curta|Opcional|E-mail|
|address|String longa|Opcional|Endereço|
|notes|String longa|Opcional|Observações|
|is_active|Booleano|Obrigatório|Situação|

---

## 21. Tabela: `strategic_contacts`

### Finalidade

Registrar contatos estratégicos da rede de resposta.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|partner_agency_id|FK|Opcional|Órgão parceiro relacionado|
|organization_id|FK|Opcional|Organização interna relacionada|
|name|String curta|Obrigatório|Nome do contato|
|role_name|String curta|Obrigatório|Função/cargo|
|primary_phone|String curta|Obrigatório|Telefone principal|
|secondary_phone|String curta|Opcional|Telefone secundário|
|email|String curta|Opcional|E-mail|
|priority_level|String curta|Obrigatório|Nível de prioridade|
|is_active|Booleano|Obrigatório|Situação|

---

# BLOCO D — BASE TERRITORIAL E RISCO

## 22. Tabela: `territories`

### Finalidade

Representar o território principal atendido pelo tenant.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome do território|
|territory_type|String curta|Obrigatório|Tipo territorial|
|ibge_code|String curta|Opcional|Código IBGE|
|state_code|String curta|Obrigatório|UF|
|description|String longa|Opcional|Descrição|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 23. Tabela: `territorial_units`

### Finalidade

Registrar subdivisões do território.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territory_id|FK|Obrigatório|Território principal|
|parent_unit_id|FK|Opcional|Unidade pai|
|name|String curta|Obrigatório|Nome da unidade|
|unit_type|String curta|Obrigatório|Tipo da subdivisão|
|code|String curta|Opcional|Código interno|
|population_estimate|Inteiro|Opcional|População estimada|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 24. Tabela: `risk_areas`

### Finalidade

Cadastrar áreas de risco mapeadas.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Obrigatório|Unidade territorial|
|name|String curta|Obrigatório|Nome da área|
|risk_type|String curta|Obrigatório|Tipo de risco|
|priority_level|String curta|Obrigatório|Prioridade|
|exposed_population_estimate|Inteiro|Opcional|População exposta|
|description|String longa|Opcional|Descrição|
|monitoring_notes|String longa|Opcional|Observações de monitoramento|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 25. Tabela: `risk_area_geometries`

### Finalidade

Armazenar a geometria das áreas de risco.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|risk_area_id|FK|Obrigatório|Área de risco|
|geometry_type|String curta|Obrigatório|Tipo geométrico|
|geometry_data|Geom/JSON|Obrigatório|Geometria|
|source_type|String curta|Obrigatório|Origem do dado espacial|
|source_file_path|String longa|Opcional|Caminho do arquivo de origem|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 26. Tabela: `risk_scenarios`

### Finalidade

Cadastrar cenários de desastre relacionados ao território.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Opcional|Unidade territorial|
|disaster_typology_id|FK|Obrigatório|Tipologia do desastre|
|name|String curta|Obrigatório|Nome do cenário|
|description|String longa|Opcional|Descrição|
|trigger_conditions|String longa|Opcional|Condições de gatilho|
|possible_impacts|String longa|Opcional|Possíveis impactos|
|response_guidelines|String longa|Opcional|Diretrizes de resposta|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 27. Tabela: `shelters`

### Finalidade

Cadastrar abrigos potenciais disponíveis.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Obrigatório|Unidade territorial|
|name|String curta|Obrigatório|Nome do abrigo|
|shelter_type|String curta|Obrigatório|Tipo do abrigo|
|address|String longa|Obrigatório|Endereço|
|manager_name|String curta|Opcional|Responsável|
|contact_phone|String curta|Opcional|Telefone|
|max_people_capacity|Inteiro|Obrigatório|Capacidade máxima de pessoas|
|accessibility_features|String longa|Opcional|Recursos de acessibilidade|
|kitchen_available|Booleano|Obrigatório|Possui cozinha|
|water_supply_available|Booleano|Obrigatório|Possui água|
|energy_supply_available|Booleano|Obrigatório|Possui energia|
|sanitary_structure_description|String longa|Opcional|Estrutura sanitária|
|latitude|Decimal|Opcional|Latitude|
|longitude|Decimal|Opcional|Longitude|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 28. Tabela: `shelter_capacities`

### Finalidade

Detalhar capacidades específicas do abrigo.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|shelter_id|FK|Obrigatório|Abrigo|
|capacity_type|String curta|Obrigatório|Tipo de capacidade|
|quantity|Inteiro|Obrigatório|Quantidade|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 29. Tabela: `evacuation_routes`

### Finalidade

Cadastrar rotas de evacuação e deslocamento seguro.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Obrigatório|Unidade territorial|
|name|String curta|Obrigatório|Nome da rota|
|origin_description|String longa|Obrigatório|Origem|
|destination_description|String longa|Obrigatório|Destino|
|route_geometry|Geom/JSON|Opcional|Geometria da rota|
|route_type|String curta|Obrigatório|Tipo da rota|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 30. Tabela: `support_points`

### Finalidade

Cadastrar pontos de apoio logístico, comunitário ou operacional.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Obrigatório|Unidade territorial|
|name|String curta|Obrigatório|Nome do ponto|
|point_type|String curta|Obrigatório|Tipo do ponto|
|address|String longa|Opcional|Endereço|
|contact_name|String curta|Opcional|Responsável|
|contact_phone|String curta|Opcional|Telefone|
|latitude|Decimal|Opcional|Latitude|
|longitude|Decimal|Opcional|Longitude|
|is_active|Booleano|Obrigatório|Situação|

---

## 31. Tabela: `operational_bases`

### Finalidade

Cadastrar bases operacionais estratégicas.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territorial_unit_id|FK|Obrigatório|Unidade territorial|
|name|String curta|Obrigatório|Nome da base|
|base_type|String curta|Obrigatório|Tipo de base|
|address|String longa|Opcional|Endereço|
|manager_name|String curta|Opcional|Gestor responsável|
|contact_phone|String curta|Opcional|Telefone|
|structure_description|String longa|Opcional|Estrutura disponível|
|is_active|Booleano|Obrigatório|Situação|

---

# BLOCO E — PLANO DE CONTINGÊNCIA

## 32. Tabela: `contingency_plans`

### Finalidade

Representar o plano principal em nível institucional.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territory_id|FK|Obrigatório|Território de abrangência|
|title|String curta|Obrigatório|Título do plano|
|plan_scope|String curta|Obrigatório|Escopo do plano|
|status|String curta|Obrigatório|Status geral do plano|
|current_version_id|FK|Opcional|Versão vigente|
|validity_start_date|Data|Opcional|Início de vigência|
|validity_end_date|Data|Opcional|Fim de vigência|
|revision_frequency_months|Inteiro|Opcional|Periodicidade de revisão|
|created_by|FK|Obrigatório|Usuário criador|
|updated_by|FK|Opcional|Usuário que atualizou|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 33. Tabela: `contingency_plan_versions`

### Finalidade

Versionar formalmente o plano.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_id|FK|Obrigatório|Plano pai|
|version_number|String curta|Obrigatório|Número da versão|
|version_status|String curta|Obrigatório|Situação da versão|
|published_at|DataHora|Opcional|Publicação|
|approved_at|DataHora|Opcional|Aprovação|
|approved_by|FK|Opcional|Usuário aprovador|
|created_by|FK|Obrigatório|Usuário criador|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

**Observações:**

- Regra crítica: apenas uma versão publicada vigente por plano/escopo.
    

---

## 34. Tabela: `contingency_plan_sections`

### Finalidade

Armazenar seções textuais do plano por versão.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_version_id|FK|Obrigatório|Versão do plano|
|section_code|String curta|Obrigatório|Código da seção|
|section_title|String curta|Obrigatório|Título|
|section_order|Inteiro|Obrigatório|Ordem de exibição|
|content_text|String longa|Opcional|Conteúdo textual|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 35. Tabela: `contingency_plan_section_items`

### Finalidade

Guardar itens estruturados complementares de cada seção.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_section_id|FK|Obrigatório|Seção vinculada|
|item_type|String curta|Obrigatório|Tipo do item|
|item_key|String curta|Obrigatório|Chave lógica|
|item_value|String longa/JSON|Obrigatório|Valor do item|
|item_order|Inteiro|Obrigatório|Ordem|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 36. Tabela: `responsibility_matrix`

### Finalidade

Representar a matriz de responsabilidades do plano.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_version_id|FK|Obrigatório|Versão do plano|
|action_name|String curta|Obrigatório|Ação prevista|
|primary_responsible_type|String curta|Obrigatório|Tipo do responsável principal|
|primary_responsible_id|Inteiro/FK lógico|Obrigatório|Identificador do responsável|
|support_responsible_type|String curta|Opcional|Tipo do apoio|
|support_responsible_id|Inteiro/FK lógico|Opcional|Identificador do apoio|
|priority_level|String curta|Obrigatório|Prioridade|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 37. Tabela: `activation_protocols`

### Finalidade

Definir protocolos de acionamento do plano.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_version_id|FK|Obrigatório|Versão do plano|
|name|String curta|Obrigatório|Nome do protocolo|
|trigger_description|String longa|Obrigatório|Gatilho de ativação|
|activation_steps|String longa/JSON|Obrigatório|Passos do acionamento|
|communication_flow|String longa/JSON|Opcional|Fluxo de comunicação|
|required_roles|JSON|Opcional|Perfis necessários|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 38. Tabela: `contingency_plan_attachments`

### Finalidade

Registrar anexos do plano.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_version_id|FK|Obrigatório|Versão do plano|
|file_name|String curta|Obrigatório|Nome do arquivo|
|file_path|String longa|Obrigatório|Caminho armazenado|
|file_type|String curta|Obrigatório|Tipo do arquivo|
|attachment_category|String curta|Obrigatório|Categoria do anexo|
|uploaded_by|FK|Obrigatório|Usuário responsável|
|created_at|DataHora|Obrigatório|Upload|

---

## 39. Tabela: `contingency_plan_publications`

### Finalidade

Registrar o histórico de publicação do plano.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_version_id|FK|Obrigatório|Versão publicada|
|publication_type|String curta|Obrigatório|Tipo de publicação|
|published_by|FK|Obrigatório|Usuário que publicou|
|published_at|DataHora|Obrigatório|Data da publicação|
|notes|String longa|Opcional|Observações|

---

# BLOCO F — PREPARAÇÃO E PRONTIDÃO

## 40. Tabela: `drills`

### Finalidade

Cadastrar simulados planejados ou executados.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|contingency_plan_id|FK|Opcional|Plano relacionado|
|title|String curta|Obrigatório|Título do simulado|
|drill_type|String curta|Obrigatório|Tipo de simulado|
|objective|String longa|Obrigatório|Objetivo|
|scheduled_at|DataHora|Obrigatório|Data prevista|
|executed_at|DataHora|Opcional|Data executada|
|status|String curta|Obrigatório|Situação|
|location_description|String longa|Opcional|Local|
|summary_result|String longa|Opcional|Resultado resumido|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 41. Tabela: `drill_participants`

### Finalidade

Listar participantes de simulados.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|drill_id|FK|Obrigatório|Simulado|
|participant_type|String curta|Obrigatório|Tipo do participante|
|user_id|FK|Opcional|Usuário interno|
|external_name|String curta|Condicional|Nome externo quando não houver usuário|
|role_name|String curta|Obrigatório|Papel no exercício|
|attendance_status|String curta|Obrigatório|Presença|

---

## 42. Tabela: `trainings`

### Finalidade

Registrar treinamentos e capacitações.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|title|String curta|Obrigatório|Título|
|training_type|String curta|Obrigatório|Tipo|
|instructor_name|String curta|Opcional|Instrutor|
|workload_hours|Decimal|Opcional|Carga horária|
|held_at|DataHora|Obrigatório|Data de realização|
|status|String curta|Obrigatório|Situação|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 43. Tabela: `training_attendees`

### Finalidade

Registrar participantes dos treinamentos.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|training_id|FK|Obrigatório|Treinamento|
|user_id|FK|Opcional|Usuário interno|
|external_name|String curta|Condicional|Nome externo|
|attendance_status|String curta|Obrigatório|Situação de presença|
|certificate_issued|Booleano|Obrigatório|Certificado emitido|

---

## 44. Tabela: `readiness_checklists`

### Finalidade

Cadastrar checklists formais de prontidão.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|title|String curta|Obrigatório|Título|
|checklist_scope|String curta|Obrigatório|Escopo|
|status|String curta|Obrigatório|Situação|
|reference_date|Data|Obrigatório|Data de referência|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 45. Tabela: `readiness_checklist_items`

### Finalidade

Detalhar itens individuais do checklist.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|readiness_checklist_id|FK|Obrigatório|Checklist pai|
|item_name|String curta|Obrigatório|Nome do item|
|item_description|String longa|Opcional|Descrição|
|item_status|String curta|Obrigatório|Situação do item|
|assigned_to|FK|Opcional|Responsável|
|due_date|Data|Opcional|Prazo|
|completed_at|DataHora|Opcional|Conclusão|
|notes|String longa|Opcional|Observações|

---

## 46. Tabela: `preparedness_tasks`

### Finalidade

Gerenciar pendências e ações preparatórias.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|title|String curta|Obrigatório|Título|
|task_type|String curta|Obrigatório|Tipo|
|related_checklist_item_id|FK|Opcional|Item relacionado|
|assigned_to|FK|Opcional|Responsável|
|priority_level|String curta|Obrigatório|Prioridade|
|due_date|Data|Opcional|Prazo|
|status|String curta|Obrigatório|Situação|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 47. Tabela: `simulation_lessons`

### Finalidade

Registrar lições aprendidas oriundas de simulados.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|drill_id|FK|Obrigatório|Simulado|
|category|String curta|Obrigatório|Categoria|
|description|String longa|Obrigatório|Lição aprendida|
|recommended_action|String longa|Opcional|Ação recomendada|
|responsible_user_id|FK|Opcional|Responsável|
|status|String curta|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

# BLOCO G — EVENTOS E ATIVAÇÃO

## 48. Tabela: `disaster_events`

### Finalidade

Registrar cada desastre ou evento gerenciado.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|territory_id|FK|Obrigatório|Território principal|
|territorial_unit_id|FK|Opcional|Unidade local específica|
|event_code|String curta|Obrigatório, único por tenant|Código do evento|
|title|String curta|Obrigatório|Título|
|disaster_typology_id|FK|Obrigatório|Tipologia|
|severity_level_id|FK|Obrigatório|Severidade|
|contingency_plan_version_id|FK|Opcional|Versão do plano usada|
|risk_scenario_id|FK|Opcional|Cenário associado|
|event_status|String curta|Obrigatório|Status do evento|
|operational_phase|String curta|Obrigatório|Fase operacional|
|started_at|DataHora|Obrigatório|Início|
|ended_at|DataHora|Opcional|Fim|
|summary_description|String longa|Opcional|Resumo inicial|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 49. Tabela: `disaster_event_classifications`

### Finalidade

Registrar classificações formais do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|classification_type|String curta|Obrigatório|Tipo de classificação|
|classification_value|String curta|Obrigatório|Valor atribuído|
|classified_by|FK|Obrigatório|Responsável|
|classified_at|DataHora|Obrigatório|Momento|
|notes|String longa|Opcional|Observações|

---

## 50. Tabela: `disaster_event_status_history`

### Finalidade

Manter histórico de alterações de status do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|previous_status|String curta|Opcional|Status anterior|
|new_status|String curta|Obrigatório|Novo status|
|changed_by|FK|Obrigatório|Usuário responsável|
|changed_at|DataHora|Obrigatório|Momento da alteração|
|notes|String longa|Opcional|Observações|

---

## 51. Tabela: `disaster_event_timeline`

### Finalidade

Registrar marcos cronológicos relevantes do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|timeline_type|String curta|Obrigatório|Tipo do marco|
|title|String curta|Obrigatório|Título|
|description|String longa|Opcional|Descrição|
|occurred_at|DataHora|Obrigatório|Data/hora da ocorrência|
|created_by|FK|Obrigatório|Usuário autor|
|created_at|DataHora|Obrigatório|Registro|

---

## 52. Tabela: `disaster_event_attachments`

### Finalidade

Guardar anexos do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|file_name|String curta|Obrigatório|Nome do arquivo|
|file_path|String longa|Obrigatório|Caminho do arquivo|
|file_type|String curta|Obrigatório|Tipo|
|attachment_category|String curta|Obrigatório|Categoria|
|uploaded_by|FK|Obrigatório|Usuário responsável|
|created_at|DataHora|Obrigatório|Upload|

---

## 53. Tabela: `disaster_event_closures`

### Finalidade

Formalizar o encerramento do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório, único|Evento encerrado|
|closure_reason|String curta|Obrigatório|Motivo do encerramento|
|closure_summary|String longa|Obrigatório|Síntese final|
|closed_by|FK|Obrigatório|Usuário responsável|
|closed_at|DataHora|Obrigatório|Data/hora|
|final_report_id|FK|Opcional|Relatório final vinculado|

---

# BLOCO H — COMANDO E COORDENAÇÃO OPERACIONAL

## 54. Tabela: `command_structures`

### Finalidade

Registrar a estrutura de comando ativada para o evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|structure_name|String curta|Obrigatório|Nome da estrutura|
|command_model|String curta|Obrigatório|Modelo utilizado|
|activated_at|DataHora|Obrigatório|Ativação|
|deactivated_at|DataHora|Opcional|Desativação|
|status|String curta|Obrigatório|Situação|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Registro|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 55. Tabela: `command_positions`

### Finalidade

Registrar posições funcionais ativadas na estrutura.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|command_structure_id|FK|Obrigatório|Estrutura de comando|
|command_position_type_id|FK|Obrigatório|Tipo da posição|
|custom_name|String curta|Opcional|Nome customizado|
|status|String curta|Obrigatório|Situação|
|activated_at|DataHora|Obrigatório|Ativação|
|deactivated_at|DataHora|Opcional|Desativação|

---

## 56. Tabela: `command_assignments`

### Finalidade

Vincular pessoas às posições funcionais.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|command_position_id|FK|Obrigatório|Posição funcional|
|user_id|FK|Opcional|Usuário interno|
|external_person_name|String curta|Condicional|Nome externo, se aplicável|
|assigned_at|DataHora|Obrigatório|Início da designação|
|unassigned_at|DataHora|Opcional|Término|
|notes|String longa|Opcional|Observações|

---

## 57. Tabela: `operational_objectives`

### Finalidade

Registrar objetivos operacionais do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|objective_code|String curta|Obrigatório|Código do objetivo|
|title|String curta|Obrigatório|Título|
|description|String longa|Obrigatório|Descrição|
|priority_level|String curta|Obrigatório|Prioridade|
|status|String curta|Obrigatório|Situação|
|defined_by|FK|Obrigatório|Usuário autor|
|defined_at|DataHora|Obrigatório|Data de definição|
|expected_completion_at|DataHora|Opcional|Previsão|

---

## 58. Tabela: `situation_meetings`

### Finalidade

Registrar reuniões de situação do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|meeting_number|Inteiro|Obrigatório|Número sequencial|
|held_at|DataHora|Obrigatório|Data/hora da reunião|
|chaired_by|FK|Opcional|Responsável pela condução|
|summary|String longa|Opcional|Resumo da reunião|
|next_actions|String longa|Opcional|Próximas ações|
|created_at|DataHora|Obrigatório|Registro|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 59. Tabela: `command_forms`

### Finalidade

Registrar formulários operacionais emitidos no evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|system_form_type_id|FK|Obrigatório|Tipo de formulário|
|reference_code|String curta|Obrigatório|Código de referência|
|title|String curta|Obrigatório|Título|
|status|String curta|Obrigatório|Situação|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 60. Tabela: `command_form_entries`

### Finalidade

Armazenar campos preenchidos de cada formulário operacional.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|command_form_id|FK|Obrigatório|Formulário|
|field_key|String curta|Obrigatório|Chave do campo|
|field_label|String curta|Obrigatório|Rótulo|
|field_value|String longa|Opcional|Valor preenchido|
|field_order|Inteiro|Obrigatório|Ordem|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 61. Tabela: `operational_decisions`

### Finalidade

Registrar decisões relevantes tomadas durante o evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|situation_meeting_id|FK|Opcional|Reunião associada|
|decision_title|String curta|Obrigatório|Título|
|decision_description|String longa|Obrigatório|Descrição da decisão|
|decided_by|FK|Obrigatório|Responsável|
|decided_at|DataHora|Obrigatório|Data/hora|
|implementation_status|String curta|Obrigatório|Situação da implementação|
|notes|String longa|Opcional|Observações|

---

## 62. Tabela: `command_transfers`

### Finalidade

Registrar transferências de comando realizadas.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|command_structure_id|FK|Obrigatório|Estrutura|
|from_assignment_id|FK|Opcional|Designação anterior|
|to_assignment_id|FK|Opcional|Nova designação|
|transferred_at|DataHora|Obrigatório|Momento da transferência|
|reason|String longa|Obrigatório|Motivo|
|notes|String longa|Opcional|Observações|

---

# BLOCO I — OPERAÇÕES, DANOS E ASSISTÊNCIA

## 63. Tabela: `field_teams`

### Finalidade

Cadastrar equipes operacionais mobilizáveis.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome da equipe|
|team_type|String curta|Obrigatório|Tipo|
|leader_user_id|FK|Opcional|Líder da equipe|
|contact_phone|String curta|Opcional|Telefone|
|availability_status|String curta|Obrigatório|Disponibilidade|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 64. Tabela: `operational_resources`

### Finalidade

Cadastrar recursos materiais e logísticos disponíveis.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome do recurso|
|resource_type|String curta|Obrigatório|Tipo de recurso|
|identifier_code|String curta|Opcional|Código/placa/número|
|ownership_type|String curta|Obrigatório|Propriedade/origem|
|current_status|String curta|Obrigatório|Situação atual|
|location_description|String longa|Opcional|Localização|
|notes|String longa|Opcional|Observações|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 65. Tabela: `operational_occurrences`

### Finalidade

Registrar ocorrências pontuais no contexto do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|territorial_unit_id|FK|Opcional|Unidade territorial|
|occurrence_code|String curta|Obrigatório|Código|
|occurrence_type|String curta|Obrigatório|Tipo da ocorrência|
|title|String curta|Obrigatório|Título|
|description|String longa|Opcional|Descrição|
|severity_level_id|FK|Opcional|Severidade local|
|address|String longa|Opcional|Endereço|
|latitude|Decimal|Opcional|Latitude|
|longitude|Decimal|Opcional|Longitude|
|status|String curta|Obrigatório|Situação|
|opened_at|DataHora|Obrigatório|Abertura|
|closed_at|DataHora|Opcional|Encerramento|
|created_by|FK|Obrigatório|Usuário criador|

---

## 66. Tabela: `missions`

### Finalidade

Registrar missões operacionais executadas no evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|operational_occurrence_id|FK|Opcional|Ocorrência relacionada|
|operational_objective_id|FK|Opcional|Objetivo relacionado|
|mission_code|String curta|Obrigatório|Código da missão|
|title|String curta|Obrigatório|Título|
|description|String longa|Obrigatório|Descrição|
|priority_level|String curta|Obrigatório|Prioridade|
|status|String curta|Obrigatório|Situação|
|assigned_at|DataHora|Opcional|Designação|
|started_at|DataHora|Opcional|Início|
|completed_at|DataHora|Opcional|Conclusão|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 67. Tabela: `mission_assignments`

### Finalidade

Relacionar equipes e recursos às missões.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|mission_id|FK|Obrigatório|Missão|
|field_team_id|FK|Opcional|Equipe destacada|
|operational_resource_id|FK|Opcional|Recurso alocado|
|assigned_by|FK|Obrigatório|Responsável pela designação|
|assigned_at|DataHora|Obrigatório|Momento|
|notes|String longa|Opcional|Observações|

---

## 68. Tabela: `resource_allocations`

### Finalidade

Formalizar alocações de recursos no evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|operational_resource_id|FK|Obrigatório|Recurso|
|allocation_type|String curta|Obrigatório|Tipo de alocação|
|mission_id|FK|Opcional|Missão associada|
|allocated_at|DataHora|Obrigatório|Início|
|released_at|DataHora|Opcional|Liberação|
|allocated_by|FK|Obrigatório|Usuário responsável|
|notes|String longa|Opcional|Observações|

---

## 69. Tabela: `damage_assessments`

### Finalidade

Registrar avaliações de danos do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|territorial_unit_id|FK|Opcional|Unidade territorial|
|assessment_type|String curta|Obrigatório|Tipo de avaliação|
|people_affected|Inteiro|Opcional|Pessoas afetadas|
|homeless_count|Inteiro|Opcional|Desabrigados|
|displaced_count|Inteiro|Opcional|Desalojados|
|injured_count|Inteiro|Opcional|Feridos|
|deceased_count|Inteiro|Opcional|Óbitos|
|public_damage_description|String longa|Opcional|Danos públicos|
|private_damage_description|String longa|Opcional|Danos privados|
|infrastructure_damage_description|String longa|Opcional|Danos de infraestrutura|
|environmental_damage_description|String longa|Opcional|Danos ambientais|
|assessed_at|DataHora|Obrigatório|Momento|
|assessed_by|FK|Obrigatório|Avaliador|
|notes|String longa|Opcional|Observações|

---

## 70. Tabela: `needs_assessments`

### Finalidade

Registrar avaliações de necessidades do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|territorial_unit_id|FK|Opcional|Unidade territorial|
|need_category|String curta|Obrigatório|Categoria da necessidade|
|requested_quantity|Decimal|Opcional|Quantidade demandada|
|requested_unit|String curta|Opcional|Unidade de medida|
|priority_level|String curta|Obrigatório|Prioridade|
|status|String curta|Obrigatório|Situação|
|assessed_at|DataHora|Obrigatório|Avaliação|
|assessed_by|FK|Obrigatório|Avaliador|
|notes|String longa|Opcional|Observações|

---

## 71. Tabela: `operational_logs`

### Finalidade

Manter diário operacional do evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|log_type|String curta|Obrigatório|Tipo de log|
|title|String curta|Obrigatório|Título|
|description|String longa|Obrigatório|Descrição|
|logged_at|DataHora|Obrigatório|Momento do registro|
|logged_by|FK|Obrigatório|Autor|
|related_entity_type|String curta|Opcional|Tipo da entidade relacionada|
|related_entity_id|Inteiro|Opcional|ID relacionado|

---

# BLOCO J — ABRIGOS E AJUDA HUMANITÁRIA

## 72. Tabela: `active_shelters`

### Finalidade

Registrar abrigos efetivamente ativados em um evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|shelter_id|FK|Obrigatório|Abrigo base|
|activated_at|DataHora|Obrigatório|Ativação|
|deactivated_at|DataHora|Opcional|Desativação|
|current_status|String curta|Obrigatório|Situação do abrigo ativo|
|current_people_count|Inteiro|Obrigatório|Total de pessoas|
|current_family_count|Inteiro|Obrigatório|Total de famílias|
|manager_name|String curta|Opcional|Responsável operacional|
|notes|String longa|Opcional|Observações|

---

## 73. Tabela: `assisted_families`

### Finalidade

Cadastrar famílias atendidas em abrigos ou pela operação humanitária.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|active_shelter_id|FK|Opcional|Abrigo ativo|
|family_reference_code|String curta|Obrigatório|Código da família|
|responsible_name|String curta|Obrigatório|Responsável familiar|
|contact_phone|String curta|Opcional|Telefone|
|origin_location|String longa|Opcional|Local de origem|
|registration_date|Data|Obrigatório|Cadastro|
|current_status|String curta|Obrigatório|Situação|
|notes|String longa|Opcional|Observações|

---

## 74. Tabela: `assisted_people`

### Finalidade

Cadastrar pessoas assistidas individualmente.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|assisted_family_id|FK|Opcional|Família vinculada|
|active_shelter_id|FK|Opcional|Abrigo ativo|
|full_name|String curta|Obrigatório|Nome completo|
|document_number|String curta|Opcional|Documento|
|birth_date|Data|Opcional|Data de nascimento|
|gender|String curta|Opcional|Gênero|
|special_condition|String longa|Opcional|Condição especial|
|is_child|Booleano|Obrigatório|Indicador de criança|
|is_elderly|Booleano|Obrigatório|Indicador de idoso|
|notes|String longa|Opcional|Observações|

---

## 75. Tabela: `humanitarian_stock_items`

### Finalidade

Cadastrar itens controlados em estoque humanitário.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|name|String curta|Obrigatório|Nome do item|
|category|String curta|Obrigatório|Categoria|
|unit_of_measure|String curta|Obrigatório|Unidade|
|current_balance|Decimal|Obrigatório|Saldo atual|
|minimum_balance|Decimal|Opcional|Saldo mínimo|
|is_active|Booleano|Obrigatório|Situação|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 76. Tabela: `humanitarian_stock_movements`

### Finalidade

Registrar entradas e saídas de estoque humanitário.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|humanitarian_stock_item_id|FK|Obrigatório|Item movimentado|
|disaster_event_id|FK|Opcional|Evento relacionado|
|movement_type|String curta|Obrigatório|Entrada/saída/ajuste|
|quantity|Decimal|Obrigatório|Quantidade|
|movement_date|DataHora|Obrigatório|Data da movimentação|
|source_description|String longa|Opcional|Origem|
|destination_description|String longa|Opcional|Destino|
|recorded_by|FK|Obrigatório|Usuário responsável|
|notes|String longa|Opcional|Observações|

---

## 77. Tabela: `humanitarian_distributions`

### Finalidade

Registrar atos de distribuição de ajuda humanitária.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|assisted_family_id|FK|Opcional|Família beneficiada|
|assisted_person_id|FK|Opcional|Pessoa beneficiada|
|distribution_date|DataHora|Obrigatório|Data da entrega|
|delivered_by|FK|Obrigatório|Responsável pela entrega|
|receipt_type|String curta|Obrigatório|Tipo de comprovação|
|notes|String longa|Opcional|Observações|

**Observações:**

- Deve possuir ao menos um item na tabela `humanitarian_distribution_items`.
    

---

## 78. Tabela: `humanitarian_distribution_items`

### Finalidade

Detalhar itens entregues em cada distribuição.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|humanitarian_distribution_id|FK|Obrigatório|Distribuição|
|humanitarian_stock_item_id|FK|Obrigatório|Item|
|quantity|Decimal|Obrigatório|Quantidade|
|unit_of_measure|String curta|Obrigatório|Unidade|

---

## 79. Tabela: `shelter_operation_reports`

### Finalidade

Gerar relatórios operacionais de abrigo por data.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|active_shelter_id|FK|Obrigatório|Abrigo ativo|
|report_date|Data|Obrigatório|Data de referência|
|current_people_count|Inteiro|Obrigatório|Pessoas no abrigo|
|current_family_count|Inteiro|Obrigatório|Famílias no abrigo|
|stock_summary|String longa|Opcional|Resumo do estoque|
|critical_needs|String longa|Opcional|Necessidades críticas|
|generated_by|FK|Obrigatório|Usuário gerador|
|created_at|DataHora|Obrigatório|Geração|

---

# BLOCO K — RECUPERAÇÃO E MEMÓRIA INSTITUCIONAL

## 80. Tabela: `recovery_plans`

### Finalidade

Estruturar o plano de recuperação após o evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|title|String curta|Obrigatório|Título|
|status|String curta|Obrigatório|Situação|
|started_at|DataHora|Obrigatório|Início|
|expected_end_at|DataHora|Opcional|Previsão de encerramento|
|created_by|FK|Obrigatório|Usuário criador|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 81. Tabela: `recovery_actions`

### Finalidade

Detalhar ações do plano de recuperação.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|recovery_plan_id|FK|Obrigatório|Plano de recuperação|
|action_axis|String curta|Obrigatório|Eixo temático|
|title|String curta|Obrigatório|Título|
|description|String longa|Obrigatório|Descrição|
|responsible_type|String curta|Obrigatório|Tipo do responsável|
|responsible_id|Inteiro/FK lógico|Opcional|Identificador do responsável|
|priority_level|String curta|Obrigatório|Prioridade|
|status|String curta|Obrigatório|Situação|
|start_date|Data|Opcional|Início|
|end_date|Data|Opcional|Fim|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 82. Tabela: `recovery_action_updates`

### Finalidade

Registrar atualizações periódicas das ações de recuperação.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|recovery_action_id|FK|Obrigatório|Ação|
|update_date|DataHora|Obrigatório|Data da atualização|
|progress_percentage|Decimal|Opcional|Percentual de avanço|
|update_description|String longa|Obrigatório|Descrição da atualização|
|updated_by|FK|Obrigatório|Usuário responsável|

---

## 83. Tabela: `post_event_evaluations`

### Finalidade

Consolidar avaliação pós-evento.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento|
|evaluation_date|Data|Obrigatório|Data da avaliação|
|strengths|String longa|Opcional|Pontos fortes|
|weaknesses|String longa|Opcional|Pontos fracos|
|opportunities|String longa|Opcional|Oportunidades|
|threats|String longa|Opcional|Ameaças|
|recommendations|String longa|Opcional|Recomendações|
|created_by|FK|Obrigatório|Autor|
|created_at|DataHora|Obrigatório|Criação|

---

## 84. Tabela: `lessons_learned`

### Finalidade

Consolidar lições aprendidas do sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Opcional|Evento relacionado|
|source_type|String curta|Obrigatório|Origem da lição|
|source_id|Inteiro|Opcional|ID da origem|
|category|String curta|Obrigatório|Categoria|
|description|String longa|Obrigatório|Descrição|
|recommendation|String longa|Opcional|Recomendação|
|status|String curta|Obrigatório|Situação|
|created_by|FK|Obrigatório|Autor|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 85. Tabela: `historical_disaster_records`

### Finalidade

Consolidar memória institucional histórica dos eventos encerrados.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|disaster_event_id|FK|Obrigatório|Evento consolidado|
|record_year|Inteiro|Obrigatório|Ano do registro|
|disaster_typology_id|FK|Obrigatório|Tipologia|
|severity_level_id|FK|Opcional|Severidade consolidada|
|territory_summary|String longa|Obrigatório|Resumo territorial|
|people_affected|Inteiro|Opcional|Pessoas afetadas|
|total_damages_summary|String longa|Opcional|Resumo dos danos|
|response_summary|String longa|Opcional|Síntese da resposta|
|recovery_summary|String longa|Opcional|Síntese da recuperação|
|closed_at|DataHora|Opcional|Data de encerramento|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

# BLOCO L — RELATÓRIOS, INTELIGÊNCIA E AUDITORIA

## 86. Tabela: `generated_reports`

### Finalidade

Registrar relatórios gerados pelo sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|report_template_id|FK|Obrigatório|Template usado|
|related_entity_type|String curta|Opcional|Tipo de entidade relacionada|
|related_entity_id|Inteiro|Opcional|ID da entidade|
|title|String curta|Obrigatório|Título do relatório|
|file_path|String longa|Opcional|Caminho do arquivo|
|generated_by|FK|Obrigatório|Usuário gerador|
|generated_at|DataHora|Obrigatório|Momento de geração|
|generation_status|String curta|Obrigatório|Situação|

---

## 87. Tabela: `analytical_snapshots`

### Finalidade

Guardar snapshots analíticos consolidados.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|snapshot_type|String curta|Obrigatório|Tipo do snapshot|
|reference_date|Data|Obrigatório|Data de referência|
|data_payload|JSON|Obrigatório|Conteúdo consolidado|
|generated_at|DataHora|Obrigatório|Momento de geração|

---

## 88. Tabela: `dashboard_cache`

### Finalidade

Armazenar dados pré-processados de dashboard.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|dashboard_type|String curta|Obrigatório|Tipo de dashboard|
|cache_key|String curta|Obrigatório|Chave do cache|
|cache_payload|JSON|Obrigatório|Conteúdo em cache|
|expires_at|DataHora|Obrigatório|Expiração|
|created_at|DataHora|Obrigatório|Criação|

---

## 89. Tabela: `audit_logs`

### Finalidade

Registrar trilha de auditoria do sistema.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Opcional|Cliente|
|user_id|FK|Opcional|Usuário autor|
|event_type|String curta|Obrigatório|Tipo de evento|
|module|String curta|Obrigatório|Módulo|
|action|String curta|Obrigatório|Ação|
|entity_type|String curta|Opcional|Tipo de entidade|
|entity_id|Inteiro|Opcional|ID da entidade|
|old_values|JSON|Opcional|Valores anteriores|
|new_values|JSON|Opcional|Novos valores|
|ip_address|String curta|Opcional|IP|
|user_agent|String longa|Opcional|Navegador/dispositivo|
|created_at|DataHora|Obrigatório|Momento do log|

**Observações:**

- Tabela imutável em operação normal.
    

---

## 90. Tabela: `active_sessions`

### Finalidade

Controlar sessões autenticadas ativas.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório|Cliente|
|user_id|FK|Obrigatório|Usuário|
|session_token_hash|String longa|Obrigatório|Hash do token|
|ip_address|String curta|Opcional|IP|
|user_agent|String longa|Opcional|Navegador/dispositivo|
|last_activity_at|DataHora|Obrigatório|Última atividade|
|expires_at|DataHora|Obrigatório|Expiração|
|created_at|DataHora|Obrigatório|Início da sessão|

---

## 91. Tabela: `security_policies`

### Finalidade

Definir políticas de segurança por tenant.

|Campo|Tipo lógico|Regra|Descrição|
|---|---|---|---|
|id|PK|Obrigatório|Identificador|
|tenant_id|FK|Obrigatório, único|Cliente|
|password_policy_json|JSON|Opcional|Regras de senha|
|session_timeout_minutes|Inteiro|Obrigatório|Tempo máximo da sessão|
|mfa_required|Booleano|Obrigatório|MFA obrigatório|
|allow_external_users|Booleano|Obrigatório|Permite usuários externos|
|created_at|DataHora|Obrigatório|Criação|
|updated_at|DataHora|Obrigatório|Atualização|

---

## 92. Regras gerais de preenchimento

1. Toda tabela de domínio institucional ou operacional deve conter `tenant_id`, exceto domínios globais.
    
2. Campos de status devem ser normalizados por regras de negócio e não por texto livre aberto.
    
3. Campos de arquivo devem armazenar caminho e metadados, não o binário diretamente no banco.
    
4. Campos geográficos devem ser preparados para evolução para PostGIS.
    
5. Campos de decisão, avaliação e relatório devem preservar histórico.
    
6. Registros de auditoria, encerramento e histórico não devem ser apagados por rotina comum.
    

---

## 93. Chaves únicas recomendadas

- `tenants.uuid`
    
- `disaster_typologies.code`
    
- `severity_levels.code`
    
- `feature_flags.code`
    
- `permissions.code`
    
- `disaster_events (tenant_id, event_code)`
    
- `contingency_plan_versions (contingency_plan_id, version_number)`
    
- `security_policies.tenant_id`
    
- `tenant_settings (tenant_id, setting_key)`
    

---

## 94. Índices prioritários

- `tenant_id`
    
- `tenant_id + status`
    
- `tenant_id + created_at`
    
- `disaster_event_id`
    
- `territorial_unit_id`
    
- `disaster_typology_id`
    
- `severity_level_id`
    
- `active_shelter_id`
    
- `humanitarian_stock_item_id`
    

---

## 95. Próximo documento recomendado

O próximo documento correto é o **Modelo Físico do Banco com Tipos SQL + Schema Inicial**, pois o dicionário de dados já consolidou a semântica necessária para converter o modelo lógico em estrutura executável.

---

## 96. Conclusão

Este dicionário de dados estabelece a tradução formal entre o domínio de negócio do sistema e sua futura implementação técnica.

A partir dele, já é possível avançar com segurança para:

- schema SQL inicial;
    
- migrations;
    
- validações de backend;
    
- repositories e services;
    
- matriz detalhada de regras de negócio.