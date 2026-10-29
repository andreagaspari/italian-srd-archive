-- Italian SRD Archive
-- MySQL 8.0+
-- Creates the final database schema for a fresh DB installation.

CREATE DATABASE IF NOT EXISTS italian_srd_archive
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE italian_srd_archive;

CREATE TABLE IF NOT EXISTS catalog_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    dataset VARCHAR(64) NOT NULL,
    item_id VARCHAR(160) NOT NULL,
    name VARCHAR(255) NOT NULL,
    search_text MEDIUMTEXT NOT NULL,
    payload JSON NOT NULL,
    source_version VARCHAR(32) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_catalog_items_dataset_id (dataset, item_id),
    KEY idx_catalog_items_dataset (dataset),
    FULLTEXT KEY ft_catalog_items_search (name, search_text)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spell_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    level TINYINT UNSIGNED NOT NULL,
    school VARCHAR(64) NOT NULL,
    casting_time VARCHAR(255) NOT NULL,
    range_value VARCHAR(128) NOT NULL,
    duration VARCHAR(128) NOT NULL,
    concentration BOOLEAN NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_spell_items_level (level),
    KEY idx_spell_items_school (school),
    KEY idx_spell_items_concentration (concentration),
    CONSTRAINT fk_spell_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS spell_classes (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    class_name VARCHAR(64) NOT NULL,
    PRIMARY KEY (catalog_item_id, class_name),
    KEY idx_spell_classes_name (class_name),
    CONSTRAINT fk_spell_classes_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS feat_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    subcategory VARCHAR(64) NOT NULL,
    prerequisite VARCHAR(255) NULL,
    repeatable BOOLEAN NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_feat_items_subcategory (subcategory),
    CONSTRAINT fk_feat_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS monster_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    monster_type VARCHAR(64) NOT NULL,
    size VARCHAR(32) NOT NULL,
    alignment VARCHAR(64) NOT NULL,
    challenge_rating VARCHAR(16) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_monster_items_type (monster_type),
    KEY idx_monster_items_size (size),
    KEY idx_monster_items_alignment (alignment),
    KEY idx_monster_items_challenge (challenge_rating),
    CONSTRAINT fk_monster_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS weapon_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    proficiency VARCHAR(64) NOT NULL,
    mode VARCHAR(32) NOT NULL,
    subcategory VARCHAR(64) NOT NULL,
    damage VARCHAR(64) NOT NULL,
    mastery VARCHAR(64) NOT NULL,
    weight_text VARCHAR(32) NOT NULL,
    cost_text VARCHAR(32) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_weapon_items_subcategory (subcategory),
    KEY idx_weapon_items_mode (mode),
    CONSTRAINT fk_weapon_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS armor_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    armor_type VARCHAR(64) NOT NULL,
    armor_class VARCHAR(128) NOT NULL,
    strength_requirement VARCHAR(32) NULL,
    stealth_disadvantage BOOLEAN NOT NULL,
    weight_text VARCHAR(32) NOT NULL,
    cost_text VARCHAR(32) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_armor_items_type (armor_type),
    CONSTRAINT fk_armor_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS ammunition_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    quantity VARCHAR(32) NOT NULL,
    container VARCHAR(64) NOT NULL,
    weight_text VARCHAR(32) NOT NULL,
    cost_text VARCHAR(32) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_ammunition_items_container (container),
    CONSTRAINT fk_ammunition_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tool_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    tool_type VARCHAR(64) NOT NULL,
    ability VARCHAR(64) NOT NULL,
    weight_text VARCHAR(32) NOT NULL,
    cost_text VARCHAR(32) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_tool_items_type (tool_type),
    CONSTRAINT fk_tool_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS adventuring_gear_items (
    catalog_item_id BIGINT UNSIGNED NOT NULL,
    category VARCHAR(64) NOT NULL,
    weight_text VARCHAR(32) NOT NULL,
    cost_text VARCHAR(32) NOT NULL,
    PRIMARY KEY (catalog_item_id),
    KEY idx_adventuring_gear_items_category (category),
    CONSTRAINT fk_adventuring_gear_items_catalog
        FOREIGN KEY (catalog_item_id) REFERENCES catalog_items (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rule_documents (
    document_key VARCHAR(64) NOT NULL,
    source_title VARCHAR(255) NOT NULL,
    source_version VARCHAR(32) NOT NULL,
    source_pages_from SMALLINT UNSIGNED NOT NULL,
    source_pages_to SMALLINT UNSIGNED NOT NULL,
    source_url VARCHAR(512) NOT NULL,
    introduction JSON NOT NULL,
    abbreviations JSON NOT NULL,
    categories JSON NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (document_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS rule_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    document_key VARCHAR(64) NOT NULL,
    item_id VARCHAR(160) NOT NULL,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(64) NULL,
    source_page SMALLINT UNSIGNED NOT NULL,
    content MEDIUMTEXT NOT NULL,
    see_also JSON NOT NULL,
    search_text MEDIUMTEXT NOT NULL,
    payload JSON NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rule_items_document_id (document_key, item_id),
    KEY idx_rule_items_category (category),
    KEY idx_rule_items_source_page (source_page),
    FULLTEXT KEY ft_rule_items_search (name, search_text),
    CONSTRAINT fk_rule_items_document
        FOREIGN KEY (document_key) REFERENCES rule_documents (document_key) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
