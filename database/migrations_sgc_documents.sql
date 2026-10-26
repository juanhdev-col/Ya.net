-- Apply once on the existing yaconecta_db database.
ALTER TABLE sgc_documents
    ADD COLUMN valid_from DATE NULL AFTER is_downloadable,
    ADD COLUMN archived_at DATETIME NULL AFTER status,
    ADD COLUMN archived_by INT NULL AFTER archived_at,
    ADD COLUMN archive_reason VARCHAR(500) NULL AFTER archived_by,
    ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER created_by;

INSERT INTO sgc_documents (title, code, version, doc_type, department_id, file_path, is_downloadable, status, valid_from, created_at) VALUES
('Hoja de cálculo importada (pendiente de identificar)', 'IMPORTADO-001', 1, 'formato', NULL, '/uploads/documentos/1791217453_6ac3cf2d95e98.xlsx', 1, 'active', NULL, FROM_UNIXTIME(1791217453)),
('Administración de Usuarios', 'GTI-PRO-002', 2, 'procedimiento', 1, '/uploads/documentos/1791406198_GTI-PRO-002 Administración de Usuarios V2.pdf', 0, 'active', NULL, FROM_UNIXTIME(1791406198)),
('Política de seguridad de la información', 'CBS-POL-001', 6, 'politica', 2, '/uploads/documentos/1791406296_CBS-POL-001 Política de seguridad de la información.doc v6x - copia.docx', 1, 'active', NULL, FROM_UNIXTIME(1791406296)),
('Cuenta Test para Nathalie', 'TI-FOR-005', 1, 'formato', 1, '/uploads/documentos/1791407006_TI-FOR-005 Cuenta Test para Nathalie.pdf', 1, 'active', NULL, FROM_UNIXTIME(1791407006)),
('Instructivo funcionalidad plataforma usuario de caja', 'OPE-INS-003', 1, 'instructivo', NULL, '/uploads/documentos/1792182481_OPE-INS-003 INSTRUCTIVO FUNCIONALIDAD PLATAFORMA USUARIO DE CAJA   .pdf', 0, 'active', NULL, FROM_UNIXTIME(1792182481));
