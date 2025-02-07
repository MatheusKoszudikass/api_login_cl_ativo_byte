CREATE TABLE image (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    name VARCHAR(50) NOT NULL,
    path VARCHAR(255) NOT NULL,
    type_image VARCHAR(50) NOT NULL,
    type_image_extension VARCHAR(4) NOT NULL,
    owner_class VARCHAR(50) NOT NULL,
    owner_id VARCHAR(50) NOT NULL,
    date_created DATETIME NOT NULL,
    date_updated DATETIME NOT NULL,
    system_access DATETIME NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE login (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    email_user_name VARCHAR(50) DEFAULT NULL,
    last_login_attempt DATETIME NOT NULL,
    last_login_ip VARCHAR(255) NOT NULL,
    remember TINYINT(1) NOT NULL,
    date_created DATETIME NOT NULL,
    date_updated DATETIME NOT NULL,
    system_access DATETIME NOT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE role (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) NOT NULL,
    date_created DATETIME NOT NULL,
    date_updated DATETIME NOT NULL,
    system_access DATETIME NOT NULL,
    UNIQUE INDEX UNIQ_IDENTIFIER_NAME (name),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE `user` (
    id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    image_id CHAR(36) DEFAULT NULL COMMENT '(DC2Type:guid)',
    email VARCHAR(180) NOT NULL,
    password VARCHAR(60) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    cnpj_cpf_rg VARCHAR(14) NOT NULL,
    is_legal_entity TINYINT(1) NOT NULL,
    user_name VARCHAR(50) NOT NULL,
    two_factor_token VARCHAR(255) NOT NULL,
    two_factor_expires_at DATETIME NOT NULL,
    is_two_factor_enabled TINYINT(1) NOT NULL,
    reset_password_token VARCHAR(255) DEFAULT NULL,
    reset_password_token_expires_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    date_created DATETIME NOT NULL,
    date_updated DATETIME NOT NULL,
    system_access DATETIME NOT NULL,
    UNIQUE INDEX UNIQ_8D93D6493DA5256D (image_id),
    UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
    UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (user_name),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE user_roles (
    user_id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    role_id CHAR(36) NOT NULL COMMENT '(DC2Type:guid)',
    INDEX IDX_54FCD59FA76ED395 (user_id),
    INDEX IDX_54FCD59FD60322AC (role_id),
    PRIMARY KEY (user_id, role_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

ALTER TABLE `user`
ADD CONSTRAINT FK_8D93D6493DA5256D FOREIGN KEY (image_id) REFERENCES image (id);

ALTER TABLE user_roles
ADD CONSTRAINT FK_54FCD59FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id);

ALTER TABLE user_roles
ADD CONSTRAINT FK_54FCD59FD60322AC FOREIGN KEY (role_id) REFERENCES role (id);
