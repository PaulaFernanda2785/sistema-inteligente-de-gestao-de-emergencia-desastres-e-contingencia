-- =========================================================
-- SIGEDC BRASIL
-- Schema Inicial do Banco de Dados (MVP Estrutural)
-- Banco recomendado: PostgreSQL 15+
-- Extensão geoespacial: PostGIS
-- =========================================================

-- =========================================================
-- 1. EXTENSOES
-- =========================================================
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS pgcrypto;

-- =========================================================
-- 2. OBSERVACOES GERAIS
-- =========================================================
-- Este schema cobre o nucleo estrutural do MVP:
-- - SaaS multi-tenant
-- - gestao institucional
-- - base territorial
-- - plano de contingencia
-- - eventos e ativacao
-- - comando operacional basico
-- - ocorrencias, missoes, danos e necessidades
-- - relatorios e auditoria
--
-- Convencoes adotadas:
-- - chaves primarias BIGSERIAL
-- - segregacao logica por tenant_id
-- - timestamps com time zone
-- - soft delete apenas onde fizer sentido administrativo
-- - tabelas de historico sem exclusao fisica operacional

-- =========================================================
-- 3. DOMINIOS GLOBAIS DE REFERENCIA
-- =========================================================

CREATE TABLE disaster_typologies (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE severity_levels (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    weight INTEGER NOT NULL CHECK (weight >= 0),
    color_hex VARCHAR(7) NOT NULL,
    sort_order INTEGER NOT NULL DEFAULT 0,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE command_position_types (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    functional_area VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE report_templates (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    module VARCHAR(100) NOT NULL,
    template_type VARCHAR(100) NOT NULL,
    version VARCHAR(30) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE system_form_types (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================================================
-- 4. NUCLEO SAAS E GOVERNANCA
-- =========================================================

CREATE TABLE tenants (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT gen_random_uuid() UNIQUE,
    legal_name VARCHAR(200) NOT NULL,
    trade_name VARCHAR(200),
    tenant_type VARCHAR(50) NOT NULL,
    document_number VARCHAR(30) NOT NULL,
    state_code VARCHAR(2) NOT NULL,
    city_name VARCHAR(150),
    plan_type VARCHAR(50) NOT NULL,
    subscription_status VARCHAR(50) NOT NULL,
    contract_start_date DATE,
    contract_end_date DATE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_tenants_state_code ON tenants(state_code);
CREATE INDEX idx_tenants_subscription_status ON tenants(subscription_status);

CREATE TABLE tenant_settings (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    setting_key VARCHAR(100) NOT NULL,
    setting_value JSONB NOT NULL,
    value_type VARCHAR(50) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_tenant_settings UNIQUE (tenant_id, setting_key)
);

CREATE TABLE subscriptions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    plan_name VARCHAR(100) NOT NULL,
    billing_cycle VARCHAR(30) NOT NULL,
    amount NUMERIC(12,2) NOT NULL CHECK (amount >= 0),
    currency VARCHAR(10) NOT NULL DEFAULT 'BRL',
    starts_at DATE NOT NULL,
    ends_at DATE,
    status VARCHAR(50) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE feature_flags (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE tenant_feature_flags (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    feature_flag_id BIGINT NOT NULL REFERENCES feature_flags(id) ON DELETE CASCADE,
    is_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_tenant_feature_flags UNIQUE (tenant_id, feature_flag_id)
);

-- =========================================================
-- 5. GESTAO INSTITUCIONAL E ACESSO
-- =========================================================

CREATE TABLE organizations (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    acronym VARCHAR(50),
    organization_type VARCHAR(100) NOT NULL,
    address TEXT,
    email VARCHAR(150),
    phone VARCHAR(30),
    coordinator_name VARCHAR(150),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_organizations_tenant_id ON organizations(tenant_id);
CREATE INDEX idx_organizations_is_active ON organizations(is_active);

CREATE TABLE organizational_units (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
    parent_unit_id BIGINT REFERENCES organizational_units(id) ON DELETE SET NULL,
    name VARCHAR(200) NOT NULL,
    unit_type VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_organizational_units_tenant_id ON organizational_units(tenant_id);
CREATE INDEX idx_organizational_units_organization_id ON organizational_units(organization_id);

CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    organization_id BIGINT NOT NULL REFERENCES organizations(id) ON DELETE RESTRICT,
    unit_id BIGINT REFERENCES organizational_units(id) ON DELETE SET NULL,
    name VARCHAR(200) NOT NULL,
    email VARCHAR(150) NOT NULL,
    cpf_hash VARCHAR(255),
    password_hash TEXT NOT NULL,
    phone VARCHAR(30),
    position_name VARCHAR(150),
    status VARCHAR(30) NOT NULL DEFAULT 'ATIVO',
    last_login_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    deleted_at TIMESTAMPTZ,
    CONSTRAINT uq_users_tenant_email UNIQUE (tenant_id, email)
);

CREATE INDEX idx_users_tenant_id ON users(tenant_id);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_users_deleted_at ON users(deleted_at);

CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id) ON DELETE CASCADE,
    code VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_system_role BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_roles_scope_code UNIQUE (tenant_id, code)
);

CREATE TABLE permissions (
    id BIGSERIAL PRIMARY KEY,
    code VARCHAR(80) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    module VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE role_permissions (
    id BIGSERIAL PRIMARY KEY,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    permission_id BIGINT NOT NULL REFERENCES permissions(id) ON DELETE CASCADE,
    CONSTRAINT uq_role_permissions UNIQUE (role_id, permission_id)
);

CREATE TABLE user_roles (
    id BIGSERIAL PRIMARY KEY,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id BIGINT NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT uq_user_roles UNIQUE (user_id, role_id)
);

CREATE TABLE partner_agencies (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    acronym VARCHAR(50),
    agency_type VARCHAR(100) NOT NULL,
    contact_name VARCHAR(150),
    phone VARCHAR(30),
    email VARCHAR(150),
    address TEXT,
    notes TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE strategic_contacts (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    partner_agency_id BIGINT REFERENCES partner_agencies(id) ON DELETE SET NULL,
    organization_id BIGINT REFERENCES organizations(id) ON DELETE SET NULL,
    name VARCHAR(150) NOT NULL,
    role_name VARCHAR(150) NOT NULL,
    primary_phone VARCHAR(30) NOT NULL,
    secondary_phone VARCHAR(30),
    email VARCHAR(150),
    priority_level VARCHAR(30) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_strategic_contacts_tenant_id ON strategic_contacts(tenant_id);
CREATE INDEX idx_strategic_contacts_priority_level ON strategic_contacts(priority_level);

-- =========================================================
-- 6. BASE TERRITORIAL E RISCO
-- =========================================================

CREATE TABLE territories (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    territory_type VARCHAR(100) NOT NULL,
    ibge_code VARCHAR(20),
    state_code VARCHAR(2) NOT NULL,
    description TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_territories_tenant_id ON territories(tenant_id);
CREATE INDEX idx_territories_ibge_code ON territories(ibge_code);

CREATE TABLE territorial_units (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territory_id BIGINT NOT NULL REFERENCES territories(id) ON DELETE CASCADE,
    parent_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    name VARCHAR(200) NOT NULL,
    unit_type VARCHAR(100) NOT NULL,
    code VARCHAR(50),
    population_estimate INTEGER CHECK (population_estimate >= 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_territorial_units_tenant_id ON territorial_units(tenant_id);
CREATE INDEX idx_territorial_units_territory_id ON territorial_units(territory_id);
CREATE INDEX idx_territorial_units_unit_type ON territorial_units(unit_type);

CREATE TABLE risk_areas (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT NOT NULL REFERENCES territorial_units(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    risk_type VARCHAR(100) NOT NULL,
    priority_level VARCHAR(30) NOT NULL,
    exposed_population_estimate INTEGER CHECK (exposed_population_estimate >= 0),
    description TEXT,
    monitoring_notes TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_risk_areas_tenant_id ON risk_areas(tenant_id);
CREATE INDEX idx_risk_areas_territorial_unit_id ON risk_areas(territorial_unit_id);
CREATE INDEX idx_risk_areas_risk_type ON risk_areas(risk_type);
CREATE INDEX idx_risk_areas_priority_level ON risk_areas(priority_level);

CREATE TABLE risk_area_geometries (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    risk_area_id BIGINT NOT NULL REFERENCES risk_areas(id) ON DELETE CASCADE,
    geometry_type VARCHAR(50) NOT NULL,
    geometry_data GEOMETRY,
    source_type VARCHAR(50) NOT NULL,
    source_file_path TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_risk_area_geometries_tenant_id ON risk_area_geometries(tenant_id);
CREATE INDEX idx_risk_area_geometries_risk_area_id ON risk_area_geometries(risk_area_id);
CREATE INDEX idx_risk_area_geometries_geom ON risk_area_geometries USING GIST(geometry_data);

CREATE TABLE risk_scenarios (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    disaster_typology_id BIGINT NOT NULL REFERENCES disaster_typologies(id) ON DELETE RESTRICT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    trigger_conditions TEXT,
    possible_impacts TEXT,
    response_guidelines TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_risk_scenarios_tenant_id ON risk_scenarios(tenant_id);
CREATE INDEX idx_risk_scenarios_typology_id ON risk_scenarios(disaster_typology_id);

CREATE TABLE shelters (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT NOT NULL REFERENCES territorial_units(id) ON DELETE RESTRICT,
    name VARCHAR(200) NOT NULL,
    shelter_type VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    manager_name VARCHAR(150),
    contact_phone VARCHAR(30),
    max_people_capacity INTEGER NOT NULL CHECK (max_people_capacity >= 0),
    accessibility_features TEXT,
    kitchen_available BOOLEAN NOT NULL DEFAULT FALSE,
    water_supply_available BOOLEAN NOT NULL DEFAULT FALSE,
    energy_supply_available BOOLEAN NOT NULL DEFAULT FALSE,
    sanitary_structure_description TEXT,
    latitude NUMERIC(10,7),
    longitude NUMERIC(10,7),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_shelters_tenant_id ON shelters(tenant_id);
CREATE INDEX idx_shelters_territorial_unit_id ON shelters(territorial_unit_id);
CREATE INDEX idx_shelters_is_active ON shelters(is_active);

CREATE TABLE support_points (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT NOT NULL REFERENCES territorial_units(id) ON DELETE RESTRICT,
    name VARCHAR(200) NOT NULL,
    point_type VARCHAR(100) NOT NULL,
    address TEXT,
    contact_name VARCHAR(150),
    contact_phone VARCHAR(30),
    latitude NUMERIC(10,7),
    longitude NUMERIC(10,7),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE operational_bases (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT NOT NULL REFERENCES territorial_units(id) ON DELETE RESTRICT,
    name VARCHAR(200) NOT NULL,
    base_type VARCHAR(100) NOT NULL,
    address TEXT,
    manager_name VARCHAR(150),
    contact_phone VARCHAR(30),
    structure_description TEXT,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================================================
-- 7. PLANO DE CONTINGENCIA
-- =========================================================

CREATE TABLE contingency_plans (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territory_id BIGINT NOT NULL REFERENCES territories(id) ON DELETE RESTRICT,
    title VARCHAR(200) NOT NULL,
    plan_scope VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    current_version_id BIGINT,
    validity_start_date DATE,
    validity_end_date DATE,
    revision_frequency_months INTEGER CHECK (revision_frequency_months >= 0),
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    updated_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_contingency_plans_tenant_id ON contingency_plans(tenant_id);
CREATE INDEX idx_contingency_plans_territory_id ON contingency_plans(territory_id);
CREATE INDEX idx_contingency_plans_status ON contingency_plans(status);

CREATE TABLE contingency_plan_versions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    contingency_plan_id BIGINT NOT NULL REFERENCES contingency_plans(id) ON DELETE CASCADE,
    version_number VARCHAR(30) NOT NULL,
    version_status VARCHAR(50) NOT NULL,
    published_at TIMESTAMPTZ,
    approved_at TIMESTAMPTZ,
    approved_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_contingency_plan_versions UNIQUE (contingency_plan_id, version_number)
);

CREATE INDEX idx_contingency_plan_versions_tenant_id ON contingency_plan_versions(tenant_id);
CREATE INDEX idx_contingency_plan_versions_status ON contingency_plan_versions(version_status);

ALTER TABLE contingency_plans
ADD CONSTRAINT fk_contingency_plans_current_version
FOREIGN KEY (current_version_id) REFERENCES contingency_plan_versions(id) ON DELETE SET NULL;

CREATE TABLE contingency_plan_sections (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    contingency_plan_version_id BIGINT NOT NULL REFERENCES contingency_plan_versions(id) ON DELETE CASCADE,
    section_code VARCHAR(50) NOT NULL,
    section_title VARCHAR(200) NOT NULL,
    section_order INTEGER NOT NULL,
    content_text TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_contingency_plan_sections_version_id ON contingency_plan_sections(contingency_plan_version_id);

CREATE TABLE responsibility_matrix (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    contingency_plan_version_id BIGINT NOT NULL REFERENCES contingency_plan_versions(id) ON DELETE CASCADE,
    action_name VARCHAR(200) NOT NULL,
    primary_responsible_type VARCHAR(50) NOT NULL,
    primary_responsible_id BIGINT NOT NULL,
    support_responsible_type VARCHAR(50),
    support_responsible_id BIGINT,
    priority_level VARCHAR(30) NOT NULL,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE activation_protocols (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    contingency_plan_version_id BIGINT NOT NULL REFERENCES contingency_plan_versions(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    trigger_description TEXT NOT NULL,
    activation_steps JSONB NOT NULL,
    communication_flow JSONB,
    required_roles JSONB,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================================================
-- 8. EVENTOS E ATIVACAO
-- =========================================================

CREATE TABLE disaster_events (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    territory_id BIGINT NOT NULL REFERENCES territories(id) ON DELETE RESTRICT,
    territorial_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    event_code VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    disaster_typology_id BIGINT NOT NULL REFERENCES disaster_typologies(id) ON DELETE RESTRICT,
    severity_level_id BIGINT NOT NULL REFERENCES severity_levels(id) ON DELETE RESTRICT,
    contingency_plan_version_id BIGINT REFERENCES contingency_plan_versions(id) ON DELETE SET NULL,
    risk_scenario_id BIGINT REFERENCES risk_scenarios(id) ON DELETE SET NULL,
    event_status VARCHAR(50) NOT NULL,
    operational_phase VARCHAR(50) NOT NULL,
    started_at TIMESTAMPTZ NOT NULL,
    ended_at TIMESTAMPTZ,
    summary_description TEXT,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_disaster_events_tenant_code UNIQUE (tenant_id, event_code)
);

CREATE INDEX idx_disaster_events_tenant_id ON disaster_events(tenant_id);
CREATE INDEX idx_disaster_events_status ON disaster_events(event_status);
CREATE INDEX idx_disaster_events_started_at ON disaster_events(started_at);
CREATE INDEX idx_disaster_events_typology_id ON disaster_events(disaster_typology_id);
CREATE INDEX idx_disaster_events_severity_id ON disaster_events(severity_level_id);

CREATE TABLE disaster_event_status_history (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    previous_status VARCHAR(50),
    new_status VARCHAR(50) NOT NULL,
    changed_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    changed_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    notes TEXT
);

CREATE INDEX idx_disaster_event_status_history_event_id ON disaster_event_status_history(disaster_event_id);
CREATE INDEX idx_disaster_event_status_history_changed_at ON disaster_event_status_history(changed_at);

CREATE TABLE disaster_event_timeline (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    timeline_type VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    occurred_at TIMESTAMPTZ NOT NULL,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_disaster_event_timeline_event_id ON disaster_event_timeline(disaster_event_id);
CREATE INDEX idx_disaster_event_timeline_occurred_at ON disaster_event_timeline(occurred_at);

CREATE TABLE disaster_event_closures (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL UNIQUE REFERENCES disaster_events(id) ON DELETE CASCADE,
    closure_reason VARCHAR(100) NOT NULL,
    closure_summary TEXT NOT NULL,
    closed_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    closed_at TIMESTAMPTZ NOT NULL,
    final_report_id BIGINT
);

-- =========================================================
-- 9. COMANDO E COORDENACAO OPERACIONAL
-- =========================================================

CREATE TABLE command_structures (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    structure_name VARCHAR(200) NOT NULL,
    command_model VARCHAR(50) NOT NULL,
    activated_at TIMESTAMPTZ NOT NULL,
    deactivated_at TIMESTAMPTZ,
    status VARCHAR(50) NOT NULL,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_command_structures_event_id ON command_structures(disaster_event_id);
CREATE INDEX idx_command_structures_status ON command_structures(status);

CREATE TABLE command_positions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    command_structure_id BIGINT NOT NULL REFERENCES command_structures(id) ON DELETE CASCADE,
    command_position_type_id BIGINT NOT NULL REFERENCES command_position_types(id) ON DELETE RESTRICT,
    custom_name VARCHAR(150),
    status VARCHAR(50) NOT NULL,
    activated_at TIMESTAMPTZ NOT NULL,
    deactivated_at TIMESTAMPTZ
);

CREATE INDEX idx_command_positions_structure_id ON command_positions(command_structure_id);

CREATE TABLE command_assignments (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    command_position_id BIGINT NOT NULL REFERENCES command_positions(id) ON DELETE CASCADE,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    external_person_name VARCHAR(200),
    assigned_at TIMESTAMPTZ NOT NULL,
    unassigned_at TIMESTAMPTZ,
    notes TEXT,
    CONSTRAINT chk_command_assignments_person CHECK (
        user_id IS NOT NULL OR external_person_name IS NOT NULL
    )
);

CREATE TABLE operational_objectives (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    objective_code VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority_level VARCHAR(30) NOT NULL,
    status VARCHAR(50) NOT NULL,
    defined_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    defined_at TIMESTAMPTZ NOT NULL,
    expected_completion_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_operational_objectives_event_code UNIQUE (disaster_event_id, objective_code)
);

CREATE INDEX idx_operational_objectives_event_id ON operational_objectives(disaster_event_id);
CREATE INDEX idx_operational_objectives_status ON operational_objectives(status);

CREATE TABLE operational_decisions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    decision_title VARCHAR(200) NOT NULL,
    decision_description TEXT NOT NULL,
    decided_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    decided_at TIMESTAMPTZ NOT NULL,
    implementation_status VARCHAR(50) NOT NULL,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_operational_decisions_event_id ON operational_decisions(disaster_event_id);
CREATE INDEX idx_operational_decisions_decided_at ON operational_decisions(decided_at);

-- =========================================================
-- 10. OPERACOES E RESPOSTA
-- =========================================================

CREATE TABLE field_teams (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    team_type VARCHAR(100) NOT NULL,
    leader_user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    contact_phone VARCHAR(30),
    availability_status VARCHAR(50) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE operational_resources (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    name VARCHAR(200) NOT NULL,
    resource_type VARCHAR(100) NOT NULL,
    identifier_code VARCHAR(100),
    ownership_type VARCHAR(50) NOT NULL,
    current_status VARCHAR(50) NOT NULL,
    location_description TEXT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_operational_resources_tenant_id ON operational_resources(tenant_id);
CREATE INDEX idx_operational_resources_status ON operational_resources(current_status);

CREATE TABLE operational_occurrences (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    occurrence_code VARCHAR(50) NOT NULL,
    occurrence_type VARCHAR(100) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    severity_level_id BIGINT REFERENCES severity_levels(id) ON DELETE SET NULL,
    address TEXT,
    latitude NUMERIC(10,7),
    longitude NUMERIC(10,7),
    status VARCHAR(50) NOT NULL,
    opened_at TIMESTAMPTZ NOT NULL,
    closed_at TIMESTAMPTZ,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_operational_occurrences_event_code UNIQUE (disaster_event_id, occurrence_code)
);

CREATE INDEX idx_operational_occurrences_event_id ON operational_occurrences(disaster_event_id);
CREATE INDEX idx_operational_occurrences_status ON operational_occurrences(status);
CREATE INDEX idx_operational_occurrences_opened_at ON operational_occurrences(opened_at);

CREATE TABLE missions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    operational_occurrence_id BIGINT REFERENCES operational_occurrences(id) ON DELETE SET NULL,
    operational_objective_id BIGINT REFERENCES operational_objectives(id) ON DELETE SET NULL,
    mission_code VARCHAR(50) NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    priority_level VARCHAR(30) NOT NULL,
    status VARCHAR(50) NOT NULL,
    assigned_at TIMESTAMPTZ,
    started_at TIMESTAMPTZ,
    completed_at TIMESTAMPTZ,
    created_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_missions_event_code UNIQUE (disaster_event_id, mission_code)
);

CREATE INDEX idx_missions_event_id ON missions(disaster_event_id);
CREATE INDEX idx_missions_status ON missions(status);
CREATE INDEX idx_missions_priority_level ON missions(priority_level);

CREATE TABLE mission_assignments (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    mission_id BIGINT NOT NULL REFERENCES missions(id) ON DELETE CASCADE,
    field_team_id BIGINT REFERENCES field_teams(id) ON DELETE SET NULL,
    operational_resource_id BIGINT REFERENCES operational_resources(id) ON DELETE SET NULL,
    assigned_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    assigned_at TIMESTAMPTZ NOT NULL,
    notes TEXT,
    CONSTRAINT chk_mission_assignments_target CHECK (
        field_team_id IS NOT NULL OR operational_resource_id IS NOT NULL
    )
);

CREATE TABLE damage_assessments (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    assessment_type VARCHAR(100) NOT NULL,
    people_affected INTEGER CHECK (people_affected >= 0),
    homeless_count INTEGER CHECK (homeless_count >= 0),
    displaced_count INTEGER CHECK (displaced_count >= 0),
    injured_count INTEGER CHECK (injured_count >= 0),
    deceased_count INTEGER CHECK (deceased_count >= 0),
    public_damage_description TEXT,
    private_damage_description TEXT,
    infrastructure_damage_description TEXT,
    environmental_damage_description TEXT,
    assessed_at TIMESTAMPTZ NOT NULL,
    assessed_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_damage_assessments_event_id ON damage_assessments(disaster_event_id);
CREATE INDEX idx_damage_assessments_assessed_at ON damage_assessments(assessed_at);

CREATE TABLE needs_assessments (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    disaster_event_id BIGINT NOT NULL REFERENCES disaster_events(id) ON DELETE CASCADE,
    territorial_unit_id BIGINT REFERENCES territorial_units(id) ON DELETE SET NULL,
    need_category VARCHAR(100) NOT NULL,
    requested_quantity NUMERIC(12,2),
    requested_unit VARCHAR(50),
    priority_level VARCHAR(30) NOT NULL,
    status VARCHAR(50) NOT NULL,
    assessed_at TIMESTAMPTZ NOT NULL,
    assessed_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    notes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_needs_assessments_event_id ON needs_assessments(disaster_event_id);
CREATE INDEX idx_needs_assessments_status ON needs_assessments(status);

-- =========================================================
-- 11. RELATORIOS, SEGURANCA E AUDITORIA
-- =========================================================

CREATE TABLE generated_reports (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    report_template_id BIGINT NOT NULL REFERENCES report_templates(id) ON DELETE RESTRICT,
    related_entity_type VARCHAR(100),
    related_entity_id BIGINT,
    title VARCHAR(200) NOT NULL,
    file_path TEXT,
    generated_by BIGINT NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    generated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    generation_status VARCHAR(50) NOT NULL
);

CREATE INDEX idx_generated_reports_tenant_id ON generated_reports(tenant_id);
CREATE INDEX idx_generated_reports_generated_at ON generated_reports(generated_at);

CREATE TABLE audit_logs (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT REFERENCES tenants(id) ON DELETE SET NULL,
    user_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    event_type VARCHAR(50) NOT NULL,
    module VARCHAR(100) NOT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(100),
    entity_id BIGINT,
    old_values JSONB,
    new_values JSONB,
    ip_address VARCHAR(64),
    user_agent TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_audit_logs_tenant_id ON audit_logs(tenant_id);
CREATE INDEX idx_audit_logs_module_action ON audit_logs(module, action);
CREATE INDEX idx_audit_logs_created_at ON audit_logs(created_at);

CREATE TABLE active_sessions (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    session_token_hash TEXT NOT NULL,
    ip_address VARCHAR(64),
    user_agent TEXT,
    last_activity_at TIMESTAMPTZ NOT NULL,
    expires_at TIMESTAMPTZ NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX idx_active_sessions_user_id ON active_sessions(user_id);
CREATE INDEX idx_active_sessions_expires_at ON active_sessions(expires_at);

CREATE TABLE security_policies (
    id BIGSERIAL PRIMARY KEY,
    tenant_id BIGINT NOT NULL UNIQUE REFERENCES tenants(id) ON DELETE CASCADE,
    password_policy_json JSONB,
    session_timeout_minutes INTEGER NOT NULL CHECK (session_timeout_minutes > 0),
    mfa_required BOOLEAN NOT NULL DEFAULT FALSE,
    allow_external_users BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- =========================================================
-- 12. CONSTRAINTS DE NEGOCIO BASICAS
-- =========================================================

ALTER TABLE disaster_events
ADD CONSTRAINT chk_disaster_events_time
CHECK (ended_at IS NULL OR ended_at >= started_at);

ALTER TABLE missions
ADD CONSTRAINT chk_missions_time
CHECK (
    (started_at IS NULL OR assigned_at IS NULL OR started_at >= assigned_at)
    AND (completed_at IS NULL OR started_at IS NULL OR completed_at >= started_at)
);

ALTER TABLE command_structures
ADD CONSTRAINT chk_command_structures_time
CHECK (deactivated_at IS NULL OR deactivated_at >= activated_at);

ALTER TABLE command_positions
ADD CONSTRAINT chk_command_positions_time
CHECK (deactivated_at IS NULL OR deactivated_at >= activated_at);

-- =========================================================
-- 13. DADOS INICIAIS MINIMOS
-- =========================================================

INSERT INTO severity_levels (code, name, weight, color_hex, sort_order)
VALUES
('BAIXO', 'Baixo', 1, '#CCC9C7', 1),
('MODERADO', 'Moderado', 2, '#FFE000', 2),
('ALTO', 'Alto', 3, '#FF7B00', 3),
('MUITO_ALTO', 'Muito Alto', 4, '#FF1D08', 4)
ON CONFLICT (code) DO NOTHING;

INSERT INTO command_position_types (code, name, functional_area)
VALUES
('COMANDO', 'Comando', 'Comando'),
('OPERACOES', 'Operacoes', 'Operacoes'),
('PLANEJAMENTO', 'Planejamento', 'Planejamento'),
('LOGISTICA', 'Logistica', 'Logistica'),
('FINANCAS', 'Financas e Administracao', 'Financas')
ON CONFLICT (code) DO NOTHING;

-- =========================================================
-- 14. OBSERVACOES DE EVOLUCAO
-- =========================================================
-- Fase seguinte do schema:
-- - active_shelters
-- - assisted_families
-- - assisted_people
-- - humanitarian_stock_items
-- - humanitarian_stock_movements
-- - humanitarian_distributions
-- - recovery_plans
-- - recovery_actions
-- - historical_disaster_records
-- - analytical_snapshots
--
-- Tambem deverao entrar, em etapa posterior:
-- - triggers para updated_at
-- - views analiticas
-- - policies de row-level security, se adotadas
-- - particionamento de tabelas historicas de grande volume
-- =========================================================
